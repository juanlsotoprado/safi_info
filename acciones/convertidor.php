<?php

include(dirname(__FILE__) . '/../init.php');
include(SAFI_MODELO_PATH. '/convertidor.php');

new ConvertidorAccion();

class ConvertidorAccion
{
	public function __construct()
	{
		try
		{
			if(!isset($_REQUEST["accion"]) || ($accion=trim($_REQUEST["accion"])) == '')
				throw new Exception('No se ha seleccionado ninguna acci&oacute;n');
			
			if(!method_exists($this, $accion))
				throw new Exception( sprintf("Acci&oacute;n \"%s\" no definida: ", $accion));
			
			$method = new ReflectionMethod($this, $accion);
			if(!$method->isPublic())
				throw new Exception( sprintf("Acceso denegado a la acci&oacute;n: \"%s\"", $accion));
				
			$this->$accion();
			exit;
			
		}catch(Exception $e){
			$GLOBALS['SafiErrors']['general'] = array();
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
			
			exit;
		}
	}
	
	public function BuscarAsociacionConvertidor()
	{
		$key = trim($_REQUEST["key"]);
		$GLOBALS['SafiRequestVars']['convertidor'] = SafiModeloConvertidor::GetAsociacionConvertidor($key);		
		
		include(SAFI_VISTA_PATH ."/json/convertidor.php");	
	}	
	
	public function BuscarAsociacionConvertidorActivas()
	{
		$key = trim($_REQUEST["key"]);
		$GLOBALS['SafiRequestVars']['convertidor'] = SafiModeloConvertidor::GetAsociacionConvertidorActivas($key);
	
		include(SAFI_VISTA_PATH ."/json/convertidor.php");
	}
	public function BuscarAsociacionConvertidorCodi()
	{
		$key = trim($_REQUEST["key"]);
		$idsSelecteds = (is_array($_REQUEST["seleccionados"]) ? $_REQUEST["seleccionados"] : null);
		
		$GLOBALS['SafiRequestVars']['convertidor'] = SafiModeloConvertidor::GetAsociacionConvertidorCodi($key, 10, $idsSelecteds);
	
		include(SAFI_VISTA_PATH ."/json/convertidor.php");
	}
	
}