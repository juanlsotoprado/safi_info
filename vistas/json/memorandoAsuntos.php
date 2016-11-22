<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaMemorandoAsuntos = array();
	
	foreach( $GLOBALS['SafiRequestVars']['memorandoAsuntos'] as $memorandoAsunto ){
		$listaMemorandoAsuntos[$memorandoAsunto->GetId()] = $memorandoAsunto->ToArray();	
	}
	echo json_encode(array("listaMemorandoAsuntos" => $listaMemorandoAsuntos));