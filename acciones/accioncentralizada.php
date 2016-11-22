<?php

include(dirname(__FILE__) . '/../init.php');
include(SAFI_MODELO_PATH. '/accioncentralizada.php');

new AccionCentralizadaAccion();

class AccionCentralizadaAccion
{	
	public function __construct()
	{
		if(isset($_REQUEST["accion"]) && trim($_REQUEST["accion"]) != ''){
			$accion = trim($_REQUEST["accion"]);
			$this->$accion();
		} else {
			$accion = "GetAllAccionesCentralizadasAprobadas";
			$this->$accion();
			//echo "No se ha selecionado ninguna accion";
			//exit;
		}
	}
	
	public function GetAllAccionesCentralizadasAprobadas()
	{
		$accionesCentralizadas = SafiModeloAccionCentralizada::GetAllAccionesCentralizadasAprobadas();
		
		$GLOBALS['SafiRequestVars']['accionesCentralizadas'] = $accionesCentralizadas;
	
		include(SAFI_VISTA_PATH ."/json/accionescentralizadas.php");
	}
	
	public function GetAccionesEspecificasBy()
	{
		if(isset($_REQUEST["idAccionCentralizada"]) && trim($_REQUEST["idAccionCentralizada"]) != ''){
			$idAccionCentralizada = trim($_REQUEST["idAccionCentralizada"]);
			
			$accionesEspecificas = SafiModeloAccionCentralizada::GetAccionesEspecificasBy($idAccionCentralizada);
			
			$GLOBALS['SafiRequestVars']['accionCentralizadaEspecificas'] = $accionesEspecificas;
	
			include(SAFI_VISTA_PATH ."/json/accioncentralizadaespecificas.php");
		}
	}
}