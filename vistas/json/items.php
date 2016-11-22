<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaItems = array();
	
	foreach( $GLOBALS['SafiRequestVars']['listaItems'] as $item ){
		$item->UTF8Encode();
		$listaItems[$item->GetId()] = $item->ToArray();	
	}
	echo json_encode(array("listaItems" => $listaItems));