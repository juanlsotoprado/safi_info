<?php
include(dirname(__FILE__) . '/../init.php');
include(SAFI_MODELO_PATH. '/puntoCuenta.php');

new PuntoCuentaAccion();

class PuntoCuentaAccion
{
	public function __construct()
	{
		try
		{
			if(!isset($_REQUEST["accion"]) || ($accion=trim($_REQUEST["accion"])) == '')
				throw new Exception('No se ha seleccionado ninguna acción');
			
			if(!method_exists($this, $accion))
				throw new Exception( sprintf("Acción \"%s\" no definida: ", $accion));
			
			$method = new ReflectionMethod($this, $accion);
			if(!$method->isPublic())
				throw new Exception( sprintf("Acceso denegado a la acción: \"%s\"", $accion));
				
			$this->$accion();
			exit;
			
		}
		catch(Exception $e)
		{
			error_log($e, 0);
			
			$GLOBALS['SafiErrors']['general'] = array();
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
			// include(SAFI_VISTA_PATH . "/desplegarmensajes.php");
			exit;
		}
	}
	
	public function GetPuntoCuenta()
	{
		try
		{
			// Verificar si se ha iniciado sessión
			if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
			throw new Exception("No se ha iniciado sesión");
			
			if(!isset($_REQUEST["idPuntoCuenta"]) || ($idPuntoCuenta=trim($_REQUEST['idPuntoCuenta'])) == "")
				throw new Exception("Parámetro \"idPuntoCuenta\" no encontrado.");
				
			$puntoCuenta = SafiModeloPuntoCuenta::GetPuntoCuenta(array("idPuntoCuenta" => $idPuntoCuenta));
			
			$GLOBALS['SafiRequestVars']['puntosCuenta'] = array($puntoCuenta->GetId() => $puntoCuenta);
			
			if(isset($_REQUEST["tipoRespuesta"]) && trim($_REQUEST["tipoRespuesta"]) != ''){
				$tipoRespuesta = $_REQUEST["tipoRespuesta"];
				 
				switch($tipoRespuesta){
					case 'json':
						include(SAFI_VISTA_PATH ."/json/puntosCuenta.php");
						break;
					case 'xml':
						//include(SAFI_VISTA_PATH ."/xml/puntosCuenta.php");
						break;
				}
			}
		}
		catch (Exception $e)
		{
			
		}
	}
	
	public function Search()
	{
		// Verificar si se ha iniciado sessión
		if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
			throw new Exception("No se ha iniciado sesión");
				
		if(!isset($_REQUEST["key"]) || ($key=trim($_REQUEST["key"])) == '')
			throw new Exception("Parámetro \"key\" no encontrado");
		
		if(!isset($_SESSION['user_depe_id']) || ($idDependencia=$_SESSION['user_depe_id']) == '')
			throw new Exception("Parámetro se sesión \"user_depe_id\" no encontrado");
			
		if(!isset($_SESSION['an_o_presupuesto']) || ($anhoPresupuesto=$_SESSION['an_o_presupuesto']) == '')
			throw new Exception("Parámetro se sesión \"an_o_presupuesto\" no encontrado");
			
			
		$params = array();

		$params['idDependencia'] = $idDependencia;
		$params['ahnoPresupuesto'] = $anhoPresupuesto;
		$params['key'] = $key;
		$params['numItems'] = 20;
		$params['idObjeto'] = 99;
		$params['idEstaus'] = 13;
		
		$puntosCuenta = SafiModeloPuntoCuenta::SearchIdsPuntoCuenta($params);
		
		$GLOBALS['SafiRequestVars']['puntosCuenta'] = $puntosCuenta;
		
		if(isset($_REQUEST["tipoRespuesta"]) && trim($_REQUEST["tipoRespuesta"]) != ''){
			$tipoRespuesta = $_REQUEST["tipoRespuesta"];
			 
			switch($tipoRespuesta){
				case 'json':
					include(SAFI_VISTA_PATH ."/json/puntosCuenta.php");
					break;
				case 'xml':
					//include(SAFI_VISTA_PATH ."/xml/partidas.php");
					break;
			}
		}
		
	}
}