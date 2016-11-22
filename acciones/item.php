<?php
include(dirname(__FILE__) . '/../init.php');
include(SAFI_MODELO_PATH. '/item.php');

new ItemAccion();

class ItemAccion
{

	public function __construct()
	{
		if(isset($_REQUEST["accion"]) && trim($_REQUEST["accion"]) != ''){
			$accion = trim($_REQUEST["accion"]);
			$this->$accion();
		} else {
			//$accion = "GetAllItems";
			//$this->$accion();
			//echo "No se ha selecionado ninguna accion";
			//exit;
		}
	}
	
	public function SearchItems()
	{
		if(isset($_REQUEST["key"]) && trim($_REQUEST["key"]) != ''){
			$key = trim($_REQUEST["key"]);
			$idsSelecteds = (is_array($_REQUEST["seleccionados"]) ? $_REQUEST["seleccionados"] : null);
			$tipoItem = (($tipoItem = $_REQUEST["tipoItem"]) != null && ($tipoItem = trim($tipoItem)) != "") ? $tipoItem : null;
			
			$items = SafiModeloItem::SearchItems($key, 10, $idsSelecteds, array("tipoItem" => $tipoItem));
			
			$GLOBALS['SafiRequestVars']['listaItems'] = $items;
		}
		include(SAFI_VISTA_PATH ."/json/items.php");
	}
}