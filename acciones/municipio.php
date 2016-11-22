<?php

include(dirname(__FILE__) . '/../init.php');
include(SAFI_MODELO_PATH. '/municipio.php');

new MunicipioAccion();

class MunicipioAccion
{
	public function __construct()
	{
		if(isset($_REQUEST["accion"]) && trim($_REQUEST["accion"]) != ''){
			$accion = trim($_REQUEST["accion"]);
			$this->$accion();
		} else {
			$accion = "GetAllMunicipios";
			$this->$accion();
			//echo "No se ha selecionado ninguna accion";
			//exit;
		}
	}
	
	public function GetAllMunicipios()
	{
		$municipios = SafiModeloMunicipio::GetAllMunicipios();
	}
	
	public function GetMunicipiosByEstado()
	{
		if(isset($_REQUEST["idEstado"]) && trim($_REQUEST["idEstado"]) != ''){
			$idEstado = $_REQUEST["idEstado"];
			if($idEstado != 0){
				$municipios = SafiModeloMunicipio::GetMunicipiosByEstado($idEstado);
			}
			$GLOBALS['SafiRequestVars']['municipios'] = $municipios;
		}
		
		
		include(SAFI_VISTA_PATH ."/xml/municipios.php");
	}
	
	public function getMunicipiosByEstado2()
	{
		if(isset($_REQUEST["idEstado"]) && trim($_REQUEST["idEstado"]) != ''){
			$idEstado = $_REQUEST["idEstado"];
			if($idEstado != 0){
				$municipios = SafiModeloMunicipio::GetMunicipiosByEstado2($idEstado);
			}
			$GLOBALS['SafiRequestVars']['municipios'] = $municipios;
		}
		
		if(isset($_REQUEST["tipoRespuesta"]) && trim($_REQUEST["tipoRespuesta"]) != ''){
			$tipoRespuesta = $_REQUEST["tipoRespuesta"];
			 
			switch($tipoRespuesta){
				case 'json':
					include(SAFI_VISTA_PATH ."/json/municipios.php");
					break;
				case 'xml':
					//include(SAFI_VISTA_PATH ."/xml/municipios.php");
					break;
			}
		}
	}
	
}
?>