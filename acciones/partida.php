<?php

include(dirname(__FILE__) . '/../init.php');
include(SAFI_MODELO_PATH. '/partida.php');

new PartidaAccion();

class PartidaAccion
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
	
	public function Search()
	{
		$annoPresupuesto = $_SESSION['an_o_presupuesto'];
		
		if(isset($_REQUEST["key"]) && trim($_REQUEST["key"]) != ''){
			$key = trim($_REQUEST["key"]);
			$anno = trim($_REQUEST["anno"]);
			
			if($anno != null && $anno != "") $annoPresupuesto = $anno;
			
			$numItems = 20;
			
			$idsSelecteds = (is_array($_REQUEST["seleccionados"]) ? $_REQUEST["seleccionados"] : null);
			$partidas = SafiModeloPartida::Search($key, $numItems, $annoPresupuesto, $idsSelecteds,$_REQUEST["tipoDocumento"]);
			
			$GLOBALS['SafiRequestVars']['partidas'] = $partidas;
			
			if(isset($_REQUEST["tipoRespuesta"]) && trim($_REQUEST["tipoRespuesta"]) != ''){
				$tipoRespuesta = $_REQUEST["tipoRespuesta"];
				 
				switch($tipoRespuesta){
					case 'json':
						include(SAFI_VISTA_PATH ."/json/partidas.php");
						break;
					case 'xml':
						//include(SAFI_VISTA_PATH ."/xml/partidas.php");
						break;
				}
			}
		}
	}
	
	
}