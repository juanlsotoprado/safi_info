<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaCiudad= array();
	
	foreach( $GLOBALS['SafiRequestVars']['ciudades'] as $ciudad ){
		$listaCiudad[$ciudad['id']] = array(
			"id" => utf8_encode($ciudad['id']),
			"nombre" => utf8_encode($ciudad['nombre']),
			"idestado" => utf8_encode($ciudad['edo_id']),
			"estatusactividad" => utf8_encode($ciudad['estatus_actividad'])
		);	
	}
	
	echo json_encode(array("listaciudad" => $listaCiudad));