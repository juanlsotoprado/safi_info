<?php
include(dirname(__FILE__) . '/../init.php');
include(SAFI_MODELO_PATH. '/responsableviatico.php');

new ResponsableViaticoAccion();

class ResponsableViaticoAccion
{
	public function __construct()
	{
		if(isset($_REQUEST["accion"]) && trim($_REQUEST["accion"]) != ''){
			$accion = trim($_REQUEST["accion"]);
			$this->$accion();
		} else {
			$accion = "GetAllResponsablesViaticos";
			$this->$accion();
			//echo "No se ha selecionado ninguna accion";
			//exit;
		}
	}
	
	public function GetUltimaCuentaBancaria()
	{
		if(isset($_REQUEST["cedula"]) && ($cedula = trim($_REQUEST["cedula"])) != ''){
			
			$responsableViatico = SafiModeloResponsableViatico::GetUltimaCuentaBancaria($cedula);
			
			$GLOBALS['SafiRequestVars']['responsablesViatico'] = array(
					$responsableViatico != null ? $responsableViatico->GetId() : 0 => $responsableViatico
			);
		}
		
		include(SAFI_VISTA_PATH ."/json/responsablesViatico.php");
	}
}
?>