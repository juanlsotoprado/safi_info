<?php
include(dirname(__FILE__) . '/../init.php');
include(SAFI_MODELO_PATH. '/empleado.php');

new EmpleadoAccion();

class EmpleadoAccion
{

	public function __construct()
	{
		if(isset($_REQUEST["accion"]) && trim($_REQUEST["accion"]) != ''){
			$accion = trim($_REQUEST["accion"]);
			$this->$accion();
		} else {
			$accion = "GetAllEmpleadosActivos";
			$this->$accion();
			//echo "No se ha selecionado ninguna accion";
			//exit;
		}
	}
	
	public static function GetEmpleadosActivos()
	{
		$empleados = SafiModeloEmpleado::GetEmpleadosActivos();
		
		$GLOBALS['SafiRequestVars']['empleados'] = $empleados;
		
		if(isset($_REQUEST["tipoRespuesta"]) && trim($_REQUEST["tipoRespuesta"]) != ''){
			$tipoRespuesta = $_REQUEST["tipoRespuesta"];
			 
			switch($tipoRespuesta){
				case 'json':
					include(SAFI_VISTA_PATH ."/json/empleados.php");
					break;
				case 'xml':
					include(SAFI_VISTA_PATH ."/xml/empleados.php");
					break;
			}
		}
	}
	
	public static function GetEmpleadoActivoByCedula()
	{
		if(isset($_REQUEST["cedula"]) && trim($_REQUEST["cedula"]) != ''){
			$cedula = $_REQUEST["cedula"];
			
			$empleados = array();
			
			$empleados[] = SafiModeloEmpleado::GetEmpleadoActivoByCedula($cedula);
			
			$GLOBALS['SafiRequestVars']['empleados'] = $empleados;
			
			if(isset($_REQUEST["tipoRespuesta"]) && trim($_REQUEST["tipoRespuesta"]) != ''){
				$tipoRespuesta = $_REQUEST["tipoRespuesta"];
				 
				switch($tipoRespuesta){
					case 'json':
						include(SAFI_VISTA_PATH ."/json/empleados.php");
						break;
					case 'xml':
						include(SAFI_VISTA_PATH ."/xml/empleados.php");
						break;
				}
			}
		}
	}
	
	public static function Search()
	{
		$params = array();
		$numItems = 5;
		
		if(isset($_REQUEST["numItems"]) && trim($_REQUEST["numItems"]) != ''){
			$numItems = trim($_REQUEST["numItems"]);
		}
		
		// Si no se especifica es parámetro idEstatus, por defecto se asume estatus activo (1)
		if(!isset($_REQUEST["idEstatus"])){
			$params["idEstatus"] = 1;  // Estado activo
		} else {
			if(
				($idEstatus=$_REQUEST["idEstatus"]) != null && ($idEstatus=trim($idEstatus)) != ''
				&& strcasecmp($idEstatus, "all") != 0
			){
				$params["idEstatus"] = $idEstatus;
			}
		}
		
		if(isset($_REQUEST["key"]) && trim($_REQUEST["key"]) != ''){
			
			$key = trim($_REQUEST["key"]);
			
			$empleados = SafiModeloEmpleado::Search($key, $numItems, $params);
			
			$GLOBALS['SafiRequestVars']['empleados'] = $empleados;
		}
		
		if(isset($_REQUEST["tipoRespuesta"]) && trim($_REQUEST["tipoRespuesta"]) != ''){
			$tipoRespuesta = $_REQUEST["tipoRespuesta"];
			 
			switch($tipoRespuesta){
				case 'json':
					include(SAFI_VISTA_PATH ."/json/empleados.php");
					break;
				case 'xml':
					include(SAFI_VISTA_PATH ."/xml/empleados.php");
					break;
			}
		}
	}
}
?>