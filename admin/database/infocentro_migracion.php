<?php
echo "No hace nada";
exit();

include(dirname(__FILE__) . "/../../init.php");

$infocentroMigracion = new InfocentroMigracion();

class InfocentroMigracion
{
	
	private $_tmpPathFile;
	private $_colIdInfocentroSAFI = 0;
	private $_colIdInfocentroSUI = 2;
	private $_headerRow = true; // Indica si la primera fila es de cabecera
	public $_dataInfocentros;
	public $_infocentrosParaActualizar;
	
	public function __construct()
	{
		$GLOBALS['SafiInfo']['general'] = array();
		$GLOBALS['SafiErrors']['general'] = array();
		
		try
		{
			/*
			 * Uno de los errores que puede hacer que $_REQUEST["accion"] no se haya establecido
			 * es el hecho de que el archivo exceda el tamaño establecido por las variables
			 * post_max_size o max_file_uploads en el php.ini. Refieráse al log de errores del php
			 * para verificar si esto fue la causa del error. La solución es aumentar el valor de esas
			 * variables.
			 */
			
			if(isset($_REQUEST["accion"]))
			{
				if(($accion=trim($_REQUEST["accion"])) == '')
					throw new Exception('No se ha seleccionado ninguna acci&oacute;n');
				
				if(!method_exists($this, $accion))
					throw new Exception( sprintf("Acci&oacute;n \"%s\" no definida", $accion));
				
				$method = new ReflectionMethod($this, $accion);
				if(!$method->isPublic())
					throw new Exception( sprintf("Acceso denegado a la acci&oacute;n: \"%s\"", $accion));
					
				$this->$accion();
				
			}
		}catch(Exception $e){
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
			error_log($e, 0);
		}
	}
	
	public function MigrarInfocentros()
	{
		try {
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('SAFI: Error al iniciar la transacci&oacute. Detalles: ' .
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$this->__ValidarArchivo();
			
			$headerRow = $this->_headerRow;
			$dataInfocentros = array(); // array que almacena los datos de los infocentros
			
			if (($handle = fopen($this->_tmpPathFile, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE)
				{
					if($headerRow){
						$headerRow = false;
						continue;
					}
					
					$idInfocentroSAFI = trim($data[$this->_colIdInfocentroSAFI]);
					
					$dataInfocentro = array(
						"idInfocentroSAFI" => $idInfocentroSAFI,
						"idInfocentroSUI" => trim($data[$this->_colIdInfocentroSUI])
					);
					
					$dataInfocentros[$idInfocentroSAFI] = $dataInfocentro;
				}
				fclose($handle);
			}
			
			$this->_dataInfocentros = $dataInfocentros;
			
			// Obtener todos los ids de infocentros utilizados en viáticos nacionales
			$query = "
				SELECT
					viatico_infocentro.viatico_id,
					viatico_infocentro.infocentro_id
				FROM
					safi_viatico_infocentro viatico_infocentro
				ORDER BY
					viatico_infocentro.viatico_id,
					viatico_infocentro.infocentro_id
			";
			
			if(!($result = $GLOBALS['SafiClassDb']->Query($query)))
				throw new Exception('SAFI: ' .$GLOBALS['SafiClassDb']->GetErrorMsg());
				
			$idInfocentrosSAFINoEncontrados = array();
			$infocentrosParaActualizar = array();	
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$idInfocentroSAFI = $row['infocentro_id'];
				
				if(isset($this->_dataInfocentros[$idInfocentroSAFI]))
				{
					$infocentrosParaActualizar[] = array(
						"idInfocentroSAFI" => $idInfocentroSAFI,
						"idInfocentroSUI" => $this->_dataInfocentros[$idInfocentroSAFI]["idInfocentroSUI"],
						"idViatico" => $row['viatico_id']
					);
				} else {
					$idInfocentrosSAFINoEncontrados[] = $idInfocentroSAFI; 
				}
			}
			
			if (count($idInfocentrosSAFINoEncontrados)>0){
				throw new Exception("Ids de infocentros en el SAFI que no aparecen en el archivo csv: " . 
					implode(", ", $idInfocentrosSAFINoEncontrados));
			}
			
			if (count($infocentrosParaActualizar) == 0){
				throw new Exception("No se encontraron infocentros par actualizar en los viáticos nacionales del SAFI: " . 
					implode(", ", $idInfocentrosSAFINoEncontrados));
			}
			
			$this->_infocentrosParaActualizar = $infocentrosParaActualizar;
			
			$count = 0;
			foreach ($infocentrosParaActualizar as $infocentro){
				$query = "
					UPDATE
						safi_viatico_infocentro
					SET
						infocentro_id = '".$infocentro["idInfocentroSUI"]."'
					WHERE
						viatico_id = '".$infocentro["idViatico"]."' AND
						infocentro_id = '".$infocentro["idInfocentroSAFI"]."';
				";
				
				if(!($result = $GLOBALS['SafiClassDb']->Query($query)))
					throw new Exception('SAFI: ' .$GLOBALS['SafiClassDb']->GetErrorMsg());
			}
			
			$GLOBALS['SafiInfo']['general'][] = "Finalizado con &eacute;xito";
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
		} catch (Exception $e) {
			if(isset($resultTransaction) && $resultTransaction === true)
				$result = $GLOBALS['SafiClassDb']->RollbackTransaction();
			throw $e;
		}
	}
	
	// Validar y obtener el archivo desde donde se cargará la información de las rendiciones de viáticos
	private function __ValidarArchivo()
	{	
		$this->_tmpPathFile = null;
		
		if(!isset($_FILES['archivo']) || !is_array($_FILES['archivo']))
			throw new Exception("No se ha especificado ning&uacute;n archivo");
		
		if($_FILES['archivo']['error'] != UPLOAD_ERR_OK )
			throw new Exception("Error al cargar el archivo");
			
		if(strpos($_FILES['archivo']['type'], "csv") === false)
			throw new Exception("El archivo debe ser de tipo csv");
			
		if(is_file($_FILES['archivo']['tmp_name']) === false)
			throw new Exception("No se puede encontrar el archivo en la ubicación temporal");
			
		if(is_readable($_FILES['archivo']['tmp_name']) === false)
			throw new Exception("No se tienen permisos de lectura para el archivo en la ubicación temporal");
		
		$this->_tmpPathFile = $_FILES['archivo']['tmp_name'];
	}

}
?>
<html>
	<head>
		<title>.:SAFI:. Ingresar Vi&aacute;tico Nacional</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<?php
			if(isset($_REQUEST["accion"]))
			{
				include(SAFI_VISTA_PATH . "/desplegarmensajes.php");
			}
		?>
		<form
			name=""
			id=""
			action="infocentro_migracion.php"
			method="post"
			enctype="multipart/form-data"
		>
			<input type="hidden" name="accion" value="MigrarInfocentros">
			<table>
				<tr>
					<td>
						Archivo
					</td>
					<td>
						<input type="file" name="archivo">
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" value="Enviar"></td>
				</tr>
			</table>
		</form>
		<?php 
			if(is_array($infocentroMigracion->_infocentrosParaActualizar)){
				$count = 0;
				echo '
		<table border="1">
			<tr>
				<td>Nro</td>
				<td>Id Infocentro SAFI</td>
				<td>Id Viatico SAFI</td>
				<td>Id Infocentro SUI</td>
			</tr>
				';
				foreach ($infocentroMigracion->_infocentrosParaActualizar as $infocentro){
					echo '
			<tr>
				<td>'.(++$count).'</td>
				<td>'.$infocentro["idInfocentroSAFI"].'</td>
				<td>'.$infocentro["idViatico"].'</td>
				<td>'.$infocentro["idInfocentroSUI"].'</td>
			</tr>
					';
				}
				echo "
		</table>
				";
			}
		?>
	</body>
</html>