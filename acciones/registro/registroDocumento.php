<?php
require_once(dirname(__FILE__) . '/../../init.php');
require_once(SAFI_ACCIONES_PATH. '/acciones.php');
require_once(SAFI_MODELO_PATH. '/viaticonacional.php');
require_once(SAFI_MODELO_PATH. '/rendicionAvance.php');
require_once(SAFI_MODELO_PATH. '/rendicionViaticoNacional.php');
require_once(SAFI_MODELO_PATH. '/avance.php');
require_once(SAFI_MODELO_PATH. '/requisicion.php');
require_once(SAFI_MODELO_PATH. '/ordenCompra.php');
require_once(SAFI_MODELO_PATH. '/puntoCuenta.php');
require_once(SAFI_MODELO_PATH. '/compromiso.php');
require_once(SAFI_MODELO_PATH. '/memorando.php');
require_once(SAFI_MODELO_PATH. '/registroDocumento.php');
require_once(SAFI_BASE_PATH.'/lib/entidades'. '/registroDocumento.php');

if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}

class RegistroDocumentoAccion{
	
	public function __construct(){
		if(isset($_REQUEST["accion"]) && trim($_REQUEST["accion"]) != ''){
			$accion = trim($_REQUEST["accion"]);
			$this->$accion();
		} else {
			echo "No se ha seleccionado ninguna acci&oacute;n";
			exit;
		}
	}
	
	public function Buscar(){
		$codigoDocumento = trim($_REQUEST["codigoDocumento"]);
		$dependencia = trim($_REQUEST["dependencia"]);
		$tipoDocumento = trim($_REQUEST["tipoDocumento"]);	
		$codigoDocumentos = SafiModeloRegistroDocumento::Buscar($codigoDocumento, $dependencia, $tipoDocumento, 20);
		$GLOBALS['SafiRequestVars']['codigoDocumento'] = $codigoDocumentos;
		$GLOBALS['vista']=1;
		include(SAFI_VISTA_PATH ."/json/registroDocumento.php");
		
	}
	public function Completar(){
		$codigoDocumento = trim($_REQUEST["codigoDocumento"]);
		$tipoDocumento = trim($_REQUEST["tipoDocumento"]);
		$datosDocumento = SafiModeloRegistroDocumento::Completar($codigoDocumento, $tipoDocumento, 20);
		$GLOBALS['SafiRequestVars']['codigoDocumento'] = $datosDocumento;
		$GLOBALS['vista']=2;
		include(SAFI_VISTA_PATH ."/json/registroDocumento.php");
	}
	
}
new RegistroDocumentoAccion();