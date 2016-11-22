<?php
include(dirname(__FILE__) . '/../../init.php');
include(SAFI_ACCIONES_PATH. '/acciones.php');
include(SAFI_MODELO_PATH. '/memorando.php');
include(SAFI_BASE_PATH.'/lib/entidades'. '/memorandoAsunto.php');

if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}

class MemorandoAccion{
	
	public function __construct(){
		if(isset($_REQUEST["accion"]) && trim($_REQUEST["accion"]) != ''){
			$accion = trim($_REQUEST["accion"]);
			$this->$accion();
		} else {
			echo "No se ha selecionado ninguna acci&oacute;n";
			exit;
		}
	}
	
	public function Search(){
		$key = trim($_REQUEST["key"]); 
		$memorandoAsuntos = SafiModeloMemorando::Search($key, 20);
		$GLOBALS['SafiRequestVars']['memorandoAsuntos'] = $memorandoAsuntos;
		include(SAFI_VISTA_PATH ."/json/memorandoAsuntos.php");
	}
}
new MemorandoAccion();