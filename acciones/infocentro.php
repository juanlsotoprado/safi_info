<?php

include(dirname(__FILE__) . '/../init.php');
include(SAFI_MODELO_PATH. '/infocentro.php');

new InfocentroAccion();

class InfocentroAccion
{
	public function __construct()
	{
		if(isset($_REQUEST["accion"]) && trim($_REQUEST["accion"]) != ''){
			$accion = trim($_REQUEST["accion"]);
			$this->$accion();
		} else {
			$accion = "GetAllInfocentros";
			$this->$accion();
			//echo "No se ha selecionado ninguna accion";
			//exit;
		}
	}
	
	public function Search()
	{
		if(isset($_REQUEST["key"]) && trim($_REQUEST["key"]) != ''){
			$key = trim($_REQUEST["key"]);
			$idsSelecteds = (is_array($_REQUEST["seleccionados"]) ? $_REQUEST["seleccionados"] : null);
			
			$infocentros = SafiModeloInfocentro::Search($key, 10, $idsSelecteds);
			
			$GLOBALS['SafiRequestVars']['infocentros'] = $infocentros;
		}
		include(SAFI_VISTA_PATH ."/json/infocentros.php");
	}
}