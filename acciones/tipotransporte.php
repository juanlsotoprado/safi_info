<?php
include(dirname(__FILE__) . '/../init.php');
include(SAFI_MODELO_PATH. '/tipotransporte.php');

new TipoTransporteAccion();

class TipoTransporteAccion
{
	public function __construct()
	{
		if(isset($_REQUEST["accion"]) && trim($_REQUEST["accion"]) != ''){
			$accion = trim($_REQUEST["accion"]);
			$this->$accion();
		} else {
			$accion = "GetAllTipoTransportes";
			$this->$accion();
			//echo "No se ha selecionado ninguna accion";
			//exit;
		}
	}
	
	public function GetTipoTransportesActivos()
	{
		$tipoTransportes = SafiModeloTipoTransporte::GetTipoTransportesActivos();
		
		$GLOBALS['SafiRequestVars']['tipoTransportes'] = $tipoTransportes;
		
		if(isset($_REQUEST["tipoRespuesta"]) && trim($_REQUEST["tipoRespuesta"]) != ''){
			$tipoRespuesta = $_REQUEST["tipoRespuesta"];
			 
			switch($tipoRespuesta){
				case 'json':
					include(SAFI_VISTA_PATH ."/json/tipotransportes.php");
					break;
				case 'xml':
					include(SAFI_VISTA_PATH ."/xml/tipotransportes.php");
					break;
			}
		}
	}
}