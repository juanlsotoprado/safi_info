<?php

include(dirname(__FILE__) . '/../init.php');
include(SAFI_MODELO_PATH. '/parroquia.php');

new ParroquiaAccion();

class ParroquiaAccion
{
	public function __construct()
	{
		if(isset($_REQUEST["accion"]) && trim($_REQUEST["accion"]) != ''){
			$accion = trim($_REQUEST["accion"]);
			$this->$accion();
		} else {
			$accion = "GetAllParroquias";
			$this->$accion();
			//echo "No se ha selecionado ninguna accion";
			//exit;
		}
	}
	
	public function GetParroquiasByMunicipio(){
		if(isset($_REQUEST["idMunicipio"]) && trim($_REQUEST["idMunicipio"]) != '')
		{
			$idMunicipio = $_REQUEST["idMunicipio"];
			
			if($idMunicipio != 0){
				$parroquias = SafiModeloParroquia::GetParroquiasByMunicipio($idMunicipio);
			}
			
			$GLOBALS['SafiRequestVars']['parroquias'] = $parroquias;
		}
		
		include(SAFI_VISTA_PATH ."/xml/parroquias.php");
	}
}