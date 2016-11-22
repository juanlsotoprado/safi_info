<?php
include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_ACCIONES_PATH. '/acciones.php');
include_once(SAFI_MODELO_PATH. '/proveedor.php');
include_once(SAFI_MODELO_PATH. '/item.php');
include_once(SAFI_BASE_PATH.'/lib/entidades'. '/proveedor.php');
include_once(SAFI_BASE_PATH.'/lib/entidades'. '/item.php');

if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}

class OrdenDeCompraAccion{
	
	public function __construct(){
		if(isset($_REQUEST["accion"]) && trim($_REQUEST["accion"]) != ''){
			$accion = trim($_REQUEST["accion"]);
			$this->$accion();
		} else {
			echo "No se ha selecionado ninguna acci&oacute;n";
			exit;
		}
	}
	
	public function SearchItems(){
		$key = trim($_REQUEST["key"]); 
		$listaItems = SafiModeloItem::Search($key, 20);
		$GLOBALS['SafiRequestVars']['listaItems'] = $listaItems;
		include(SAFI_VISTA_PATH ."/json/items.php");
	}
	
	public function SearchProveedores(){
		if ( $_REQUEST["key"]!=null && $_REQUEST["key"]!='') {
			$key = trim($_REQUEST["key"]);
			$listaProveedores = SafiModeloProveedor::Search($key, 20);
		} else if ( $_REQUEST["rifNombre"]!=null && $_REQUEST["rifNombre"]!='' ) {
			$rifNombre = trim($_REQUEST["rifNombre"]);
			$listaProveedores = SafiModeloProveedor::SearchByRifNombre($rifNombre, 20);
		}
		$GLOBALS['SafiRequestVars']['listaProveedores'] = $listaProveedores;
		include(SAFI_VISTA_PATH ."/json/proveedores.php");
	}	
}
new OrdenDeCompraAccion();