<?php
include(dirname(__FILE__) . '/../init.php');

// Acciones
require_once(SAFI_ACCIONES_PATH. '/acciones.php');

// Modelo
include(SAFI_MODELO_PATH. '/solicitudPago.php');

class SolicitudPagoAccion extends Acciones
{
	public function Search()
	{
		try
		{
			// Verificar si se ha iniciado sessión
			if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
				throw new Exception("No se ha iniciado sesión.");
					
			if(!isset($_REQUEST["key"]) || ($key=trim($_REQUEST["key"])) == '')
				throw new Exception("Parámetro \"key\" no encontrado.");
				
			if(!isset($_REQUEST['numeroItems']) || ($numeroItems=$_REQUEST['numeroItems']) == '')
				throw new Exception("Parámetro se sesión \"numeroItems\" no encontrado.");
				
			if(!isset($_SESSION['an_o_presupuesto']) || ($a_oPresupuesto=$_SESSION['an_o_presupuesto']) == '')
				throw new Exception("Parámetro sesión \"an_o_presupuesto\" no encontrado");
				
			$params = array(
				"key" => $key,
				"numeroItems" => $numeroItems,
				"a_oPresupuesto" => $a_oPresupuesto
			);
			
			$arrSolicitudPago = SafiModeloSolicitudPago::SearchIdsSolicitudPago($params);
			
			$GLOBALS['SafiRequestVars']['arrSolicitudPago'] = $arrSolicitudPago;
			
			if(isset($_REQUEST["tipoRespuesta"]) && trim($_REQUEST["tipoRespuesta"]) != ''){
				$tipoRespuesta = $_REQUEST["tipoRespuesta"];
				 
				switch($tipoRespuesta){
					case 'json':
						include(SAFI_VISTA_PATH ."/json/solicitudPago.php");
						break;
				}
			}
		}
		catch (Exception $e)
		{
			
		}
	}
}

new SolicitudPagoAccion();