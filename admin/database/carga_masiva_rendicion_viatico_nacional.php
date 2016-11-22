<?php

echo "No hace nada";
exit;

include(dirname(__FILE__) . '/../../init.php');
include(SAFI_MODELO_PATH. '/rendicionViaticoNacional.php');
include(SAFI_MODELO_PATH. '/viaticonacional.php');

class CargaMasivaRendicionViaticoNacional
{
	private $_tmpPathFile;
	private $_idDocumentoTienePrefijo = true;
	private $_prefijoDocumento = "vnac";
	private $_separadorPrefijoIdDocumento = "-";
	private $_formatoFechaRendicionEntrada = "d/m/y"; // <dia 2 digitos>/<mes 2 digitos>/<año 2 digitos>
	private $_formatoFechaRendicionSalida = "d/m/Y"; // <dia 2 digitos>/<mes 2 digitos>/<año 4 digitos>
	private $_rendicionElaboradaPorUsuaLogin = "12952064"; // usuaLogin del empleado que elabora la rendición
	private $_rendicionElaboradaPorPerfil = "37450"; // Perfil  del empleado que elabora la rendición
	private $_colIdViatico = 1;  // Columna donde se específica el id del viático
	private $_colFechaRendicion = 4;  // Columna donde se específica la fecha de la rendición
	private $_headerRow = true; // Indica si la primera fila es de cabecera
	
	public function __construct()
	{
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
			
			$GLOBALS['SafiErrors']['general'] = array();
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
			
			include(SAFI_VISTA_PATH . "/desplegarmensajes.php");
			exit;
		}
	}
	
	public function RealizarCarga()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		
		try {
			
			// Validar y obtener el archivo desde donde se cargará la información de las rendiciones de viáticos
			$this->__ValidarArchivo();
			
			if(count($GLOBALS['SafiErrors']['general'])>0 || count($GLOBALS['SafiInfo']['general'])>0){
				include(SAFI_VISTA_PATH . "/desplegarmensajes.php");
			} else {
				
				$headerRow = $this->_headerRow;
				$dataRendiciones = array(); // array que almacena los id de los viativos a aprobar
				
				if (($handle = fopen($this->_tmpPathFile, "r")) !== FALSE) {
					while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {
						if($headerRow){
							$headerRow = false;
							continue;
						}
						
						$idViatico = trim($data[$this->_colIdViatico]);
						$fechaRendicion = $data[$this->_colFechaRendicion];
						
						if($idViatico != '' && $fechaRendicion != '')
						{
							$fecha = $this->__ValidarFecha($fechaRendicion);
							if($fecha===false)
								throw new Exception("Fecha " .$fechaRendicion. " del vi&aacute;tico ".$idViatico." inv&aacute;lida");
							
							$fechaRendicion =  date_format(date_create_from_format(
								$this->_formatoFechaRendicionEntrada, $fecha), $this->_formatoFechaRendicionSalida);
							
							$dataRendicion = array(
								"fechaRendicion" => $fechaRendicion,
								"idViatico" => ($this->_idDocumentoTienePrefijo ? strtolower($idViatico) : $this->_prefijoDocumento . 
									$this->_separadorPrefijoIdDocumento.$idViatico)
							);
							$dataRendiciones[$idViatico] = $dataRendicion;
						}
					}
					fclose($handle);
				}
				
				if(count($dataRendiciones)<=0){
					throw new Exception("No se pudo obtener ning&uacute;n dato de rendici&oacute;n del el archivo");
					return false;
				} else {
					
					$huboErrores = false;
					
					$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
					if($resultTransaction === false)
						throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					
					foreach ($dataRendiciones as $dataRendicion){
						try {
							
							/************************************************
							 *********** Para guardar la rendición **********
							 ************************************************/
							
							$idViatico = $dataRendicion['idViatico'];
							
							$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($idViatico);
							
							if($viatico == null)
								throw new Exception("El vi&aacute;tico ". $idViatico ." no se encuentra registrado en el sistema");
							
							// Calcular el monto total del viático
							$montoTotal = CalcularMontoTotalAsignacionesViaticoNacional($viatico->GetViaticoResponsableAsignaciones());
							
							$dependencia = new EntidadDependencia();
							$dependencia->SetId($viatico->GetDependenciaId());
								
							$rendicion = new EntidadRendicionViaticoNacional();
							
							$rendicion->SetIdViaticoNacional($viatico->GetId());
							$rendicion->SetFechaRendicion($dataRendicion['fechaRendicion']);
							$rendicion->SetFechaInicioViaje($viatico->GetFechaInicioViaje());
							$rendicion->SetFechaFinViaje($viatico->GetFechaFinViaje());
							$rendicion->SetObjetivosViaje($viatico->GetObjetivosViaje());
							$rendicion->SetTotalGastos($montoTotal);
							$rendicion->SetMontoAnticipo($montoTotal);
							$rendicion->SetDependencia($dependencia);
							$rendicion->SetUsuaLogin($this->_rendicionElaboradaPorUsuaLogin);
							/*
							echo "<pre>";
							print_r($rendicion);
							echo "</pre>";
							*/
							$params = array();
							$params['perfilRegistrador'] = $this->_rendicionElaboradaPorPerfil;
							
							$idRendicion = SafiModeloRendicionViaticoNacional::GuardarRendicion
								($rendicion, $params, null);
								
							if($idRendicion === false)
								throw new Exception("La rendici&oacute;n del vi&aacute;tico ".$viatico->GetId()." no pudo ser guardada.");
							
							/************************************************
							*********** Para guardar la rendición **********
							************************************************/
								
							// Obtener una instancia de docgenera para el viatico nacional a enviar (actualizar)
							$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idRendicion);
							
							$estadoAprobado = 13;
							
							$docGenera->SetIdWFObjeto(99);
							$docGenera->SetIdWFCadena(0);
							$docGenera->SetIdEstatus($estadoAprobado);
							$docGenera->SetIdPerfilActual(null);
							
							// Guardar el registro del documento en docGenera (estado de la cadena)
							if(SafiModeloDocGenera::EnviarDocumento($docGenera) === false)
								throw new Exception("Error al enviar. No se pudo actualizar docGenera para la 
									rendici&oacute;n: " . $idRendicion);
							
						} catch (Exception $e) {
							$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
							$huboErrores = true;
						}
					}
					
					/*
					echo "<pre>";
					print_r($dataRendiciones);
					echo "</pre>";
					*/
					
					if($huboErrores === false){
						$GLOBALS['SafiClassDb']->CommitTransaction();
						$GLOBALS['SafiInfo']['general'][] = "Carga realizada exitosamente de ".count($dataRendiciones)." rendiciones";
						include(SAFI_VISTA_PATH . "/desplegarmensajes.php");
					}
					else {
						throw new Exception('Una o m&aacute;s rendiciones no se pueden guardar.');
					}
				}
			}
		} catch (Exception $e) {
			if(isset($resultTransaction) && $resultTransaction)
				$GLOBALS['SafiClassDb']->RollbackAllTransactions();
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
			include(SAFI_VISTA_PATH . "/desplegarmensajes.php");
		}
	}
	
	// Validar y obtener el archivo desde donde se cargará la información de las rendiciones de viáticos
	private function __ValidarArchivo()
	{	
		$this->_tmpPathFile = null;
		
		try {
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
			
		} catch (Exception $e) {
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
		}
	}
	
	// Validar fechas
	private function __ValidarFecha($fecha){
		$valida = false;
		
		$arrFecha = explode('/', $fecha);
		if (count($arrFecha) == 3){
			$day = $arrFecha[0];
			$month = $arrFecha[1];
			$year = $arrFecha[2];
			if(checkdate ($month ,$day ,$year)){
				$valida = $day . '/' . $month . '/' . $year;
			}
		}
		
		return $valida;
	}
	
}

new CargaMasivaRendicionViaticoNacional();
?>
<html>
	<head>
		<title>Carga masiva de rendiciones de vi&aacute;ticos nacionales</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	</head>
	<body>
		<form
			name=""
			id=""
			action="carga_masiva_rendicion_viatico_nacional.php"
			method="post"
			enctype="multipart/form-data"
		>
			<input type="hidden" name="accion" value="RealizarCarga">
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
	</body>
</html>