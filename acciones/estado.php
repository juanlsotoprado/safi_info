<?php
include(dirname(__FILE__) . '/../init.php');
include(SAFI_MODELO_PATH. '/estado.php');

new EstadoAccion();

class EstadoAccion
{
	public function __construct()
	{
		if(isset($_REQUEST["accion"]) && trim($_REQUEST["accion"]) != ''){
			$accion = trim($_REQUEST["accion"]);
			$this->$accion();
		} else {
			$accion = "GetAllEstados";
			$this->$accion();
			//echo "No se ha selecionado ninguna accion";
			//exit;
		}
	}
	
	public function GetAllEstados()
	{
		
		$estados = SafiModeloEstado::GetAllEstados();
			
		$GLOBALS['SafiRequestVars']['estados'] = $estados;
		
		include(SAFI_VISTA_PATH ."/xml/estados.php");
	}
}