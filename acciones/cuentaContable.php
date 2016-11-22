<?php

include(dirname(__FILE__) . '/../init.php');
include(SAFI_MODELO_PATH. '/cuentaContable.php');

new CuentaContableAccion();

class CuentaContableAccion
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
			
		//	include(SAFI_VISTA_PATH . "/desplegarmensajes.php");
			exit;
		}
	}
	
	/*public function Search()
	{
		$params = Array();
		error_log($_REQUEST['key']);
		$GLOBALS['SafiRequestVars']['cuentas'] = SafiModeloCuentaContable::GetCuentasContables($params);
		//error_log(print_r($GLOBALS['SafiRequestVars']['cuentas'],true));
		include(SAFI_VISTA_PATH ."/json/cuentaContable.php");
	}*/

	public function Search()
	{
	
		if(isset($_REQUEST["key"]) && trim($_REQUEST["key"]) != ''){
			$key = trim($_REQUEST["key"]);
				
			$numItems = 20;
				
			$idsSelecteds = (is_array($_REQUEST["seleccionados"]) ? $_REQUEST["seleccionados"] : null);
			$GLOBALS['SafiRequestVars']['cuentas'] = SafiModeloConvertidor::Search($key, $numItems, $idsSelecteds);;
							
			$GLOBALS['SafiRequestVars']['partidas'] = $partidas;
				
			include(SAFI_VISTA_PATH ."/json/cuentaContable.php");
		}
	}
	
	
	
}