<?php
include(dirname(__FILE__) . '/../init.php');
include(SAFI_MODELO_PATH. '/ciudad.php');

new CiudadAccion();

class CiudadAccion
{
	public function __construct()
	{
		if(isset($_REQUEST["accion"]) && trim($_REQUEST["accion"]) != ''){
			$accion = trim($_REQUEST["accion"]);
			$this->$accion();
		} else {
			$accion = "GetAllCiudades";
			$this->$accion();
			//echo "No se ha selecionado ninguna accion";
			//exit;
		}
	}
	
	public function GetCiudadesActivasByEstado(){
		if(isset($_REQUEST["idEstado"]) && trim($_REQUEST["idEstado"]) != ''){
			$idEstado = $_REQUEST["idEstado"];
			if($idEstado != 0){
				$ciudades = SafiModeloCiudad::GetCiudadesActivasByEstado($idEstado);
			}
			$GLOBALS['SafiRequestVars']['ciudades'] = $ciudades;
		}
		
		if(isset($_REQUEST["tipoRespuesta"]) && trim($_REQUEST["tipoRespuesta"]) != ''){
			$tipoRespuesta = $_REQUEST["tipoRespuesta"];
			 
			switch($tipoRespuesta){
				case 'json':
					include(SAFI_VISTA_PATH ."/json/ciudades.php");
					break;
				case 'xml':
					include(SAFI_VISTA_PATH ."/xml/ciudades.php");
					break;
			}
		}
	}
}