<?php
echo "No hace nada";
exit();

/*
 * Id de los Infocentros del SAFI no migrados:
 * 	649
 * 	73
 * 	686
 * Id de los Infocentros del SAFI que existen en SUI
 * 	145
 * 	179
 * 	558
 * 	526
 * 	707
 * 	713
 *
 * */

include(dirname(__FILE__) . "/../../init.php");
require(SAFI_BASE_PATH . '/lib/database/mysql.php');

$migracion = new InfocentrosSAFIInfocentrosSUI();

class InfocentrosSAFIInfocentrosSUI
{
	private $db = null;
	private $_tmpPathFile;
	private $_colIdInfocentroSAFI = 0;
	private $_colNemotecnicoInfocentroSAFI = 1;
	private $_colNemotecnicoInfocentroSUI = 8;
	private $_headerRow = true; // Indica si la primera fila es de cabecera
	public $_dataInfocentros;
	public $_idsInfocentrosFaltantesSAFI;
	
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
	
	public function ConectarSistemaInfocentros()
	{
		if ($this->db !== null){
			return;
		}
		
		// Conexion a sigetec
		$configInfocentros = array();
		
		//Producción
		
		$configInfocentros["dbEncoding"] = 'UTF8';
		//$configInfocentros["dbEncoding"] = 'LATIN1';
		$configInfocentros["dbServer"] = "192.168.110.22";
		$configInfocentros["dbPort"] = "3306";
		$configInfocentros["dbUser"] = "sui_lectura";
		$configInfocentros["dbPass"] = "*sui8485.";
		$configInfocentros["dbDatabase"] = "sui";
		
		/*
		//Localhost
		$configInfocentros["dbEncoding"] = 'UTF8';
		//$configInfocentros["dbEncoding"] = 'LATIN1';
		$configInfocentros["dbServer"] = "localhost";
		$configInfocentros["dbPort"] = "3306";
		$configInfocentros["dbUser"] = "root";
		$configInfocentros["dbPass"] = "p741852*";
		$configInfocentros["dbDatabase"] = "sui";
		*/
		
		$db_type = 'MySQLDb';
		$db = new $db_type();
		$db->charset = $configInfocentros["dbEncoding"];
		
		$connection = $db->Connect(
			$configInfocentros["dbServer"].":".$configInfocentros["dbPort"],
			$configInfocentros["dbUser"],
			$configInfocentros["dbPass"], 
			$configInfocentros["dbDatabase"]
		);
		
		$this->db = &$db;
		
		if (!$connection) {
			list($error, $level) = $db->GetError();
			
			$error = str_replace($configInfocentros['dbServer'], "[database server]", $error);
			$error = str_replace($configInfocentros['dbPort'], "[database port]", $error);
			$error = str_replace($configInfocentros['dbUser'], "[database user]", $error);
			$error = str_replace($configInfocentros['dbPass'], "[database pass]", $error);
			$error = str_replace($configInfocentros['dbDatabase'], "[database]", $error);
		
			echo "<strong>Problemas de conexion con la base de datos de infocentros: </strong>".$error;
			exit;
		}
	}
	
	// Validar y obtener el archivo desde donde se cargará la información de los nemotecnicos(código) de los infocentros
	public function BuscarIdsFromNemotecnicos()
	{
		try {
			
			$this->__ValidarArchivo();
			
			$headerRow = $this->_headerRow;
			$dataInfocentros = array(); // array que almacena los datos de los infocentros
			$nemotecnicosInfocentrosSUI = array();
			$idsInfocentrosSAFI = array();
			
			if (($handle = fopen($this->_tmpPathFile, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE)
				{
					if($headerRow){
						$headerRow = false;
						continue;
					}
					
					$idInfocentroSAFI = trim($data[$this->_colIdInfocentroSAFI]);
					$nemotecnicoInfocentroSAFI = trim($data[$this->_colNemotecnicoInfocentroSAFI]);
					$nemotecnicoInfocentroSUI = trim($data[$this->_colNemotecnicoInfocentroSUI]);
					
					$nemotecnicosInfocentrosSUI[] = utf8_decode($nemotecnicoInfocentroSUI);

					$idsInfocentrosSAFI[$nemotecnicoInfocentroSUI] = $idInfocentroSAFI;
					
					$dataInfocentro = array(
						"idInfocentroSAFI" => $idInfocentroSAFI,
						"nemotecnicoInfocentroSAFI" => $nemotecnicoInfocentroSAFI,
						"nemotecnicoInfocentroSUI" => $nemotecnicoInfocentroSUI
					);
					
					$dataInfocentros[$idInfocentroSAFI] = $dataInfocentro;
					
				}
				fclose($handle);
			}
			
			$this->ConectarSistemaInfocentros();
			
			$query = "
				SELECT
					id,
					codigo
				FROM
					infocentro
				WHERE
					codigo IN ('".implode("', '", $nemotecnicosInfocentrosSUI)."')
			";
			
			if(!($result = $this->db->Query($query)))
				throw new Exception($this->db->GetErrorMsg());
			
			while ($row = $this->db->Fetch($result)) {
				if(isset($idsInfocentrosSAFI[$row['codigo']])){
					$dataInfocentros[$idsInfocentrosSAFI[$row['codigo']]]['idInfocentroSUI'] = $row['id'];
				}
			}
			
			$this->_dataInfocentros = $dataInfocentros;
			
			$idsInfocentrosFaltantesSAFI = array();
			
			foreach ($dataInfocentros as $dataInfocentro){
				if(!isset($dataInfocentro['idInfocentroSUI'])){
					$idsInfocentrosFaltantesSAFI[] = $dataInfocentro['idInfocentroSAFI'];
				}
			}
			$this->_idsInfocentrosFaltantesSAFI = $idsInfocentrosFaltantesSAFI;
			
			if(count($this->_idsInfocentrosFaltantesSAFI) == 0){
				$this->GenerarCSV();
				exit;
			} else {
				throw new Exception("Uno o más infocentros no pudieron ser asociados");
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	
	public function GenerarCSV()
	{
		header ('Content-type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="infocentros_safi__infocentros_sui.csv"');
		
		// Para evitar la cache del navegador o proxy
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado
		
	    $handle = fopen("php://output", 'w');
	    
	    fputcsv($handle, array("Id SAFI", "Nemotecnico SAFI", "Id SUI", "Nemotecnico SUI"), ',', '"');
	    
	    foreach ($this->_dataInfocentros as $dataInfocentro)
	    {
	    	if(isset($dataInfocentro['idInfocentroSUI'])){
		    	fputcsv(
		    		$handle, 
		    		array(
		    			$dataInfocentro['idInfocentroSAFI'],
						$dataInfocentro['nemotecnicoInfocentroSAFI'],
						$dataInfocentro['idInfocentroSUI'],
						$dataInfocentro['nemotecnicoInfocentroSUI']
		    		), 
		    		',', 
		    		'"'
		    	);
	    	}
	    }
	
	    fclose($handle);
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
			action="infocentros_safi__infocentros_sui.php"
			method="post"
			enctype="multipart/form-data"
		>
			<input type="hidden" name="accion" value="BuscarIdsFromNemotecnicos">
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
			<br/><br/>
			<?php
				// Desplegar los infocnetros faltantes
				if(is_array($migracion->_idsInfocentrosFaltantesSAFI)){
					echo '
			<div>Infocentros faltantes</div>
			<table border="1">
				<tr>
					<td>Id SAFI</td>
					<td>Nemotecnico SAFI</td>
					<td>Nemotecnico SUI</td>
				</tr>
					';
					if(count($migracion->_idsInfocentrosFaltantesSAFI) == 0){
						echo '
				<tr>
					<td>Ninguno</td>
				</tr>
						';
					} else {
						foreach ($migracion->_idsInfocentrosFaltantesSAFI as $idInfocentroFaltante){
							echo '
				<tr>
					<td>'.($migracion->_dataInfocentros[$idInfocentroFaltante]['idInfocentroSAFI']).'</td>
					<td>'.($migracion->_dataInfocentros[$idInfocentroFaltante]['nemotecnicoInfocentroSAFI']).'</td>
					<td>'.($migracion->_dataInfocentros[$idInfocentroFaltante]['nemotecnicoInfocentroSUI']).'</td>
				</tr>
							';
						}
					}
					echo '
			</table>
			<br/><br/>
					';
				}
				
				// Desplegar todos los infocentros
				if(is_array($migracion->_dataInfocentros)){
					$index = 0;
					echo '
			<div>Todos los Infocentros</div>
			<table border="1">
				<tr>
					<td>Nro</td>
					<td>Id SAFI</td>
					<td>Nemotecnico SAFI</td>
					<td>Id SUI</td>
					<td>Nemotecnico SUI</td>
				</tr>
					';
					foreach ($migracion->_dataInfocentros as $dataInfocentro){
						echo '
				<tr>
					<td>'.(++$index).'</td>
					<td>'.utf8_decode($dataInfocentro['idInfocentroSAFI']).'</td>
					<td>'.utf8_decode($dataInfocentro['nemotecnicoInfocentroSAFI']).'</td>
					<td>'.utf8_decode($dataInfocentro['idInfocentroSUI']).'</td>
					<td>'.utf8_decode($dataInfocentro['nemotecnicoInfocentroSUI']).'</td>
				</tr>
						';
					}
					echo '
			</table>
					';
				}
			?>
		</form>
	</body>
</html>