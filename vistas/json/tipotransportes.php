<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaTipoTransporte = array();
	
	foreach( $GLOBALS['SafiRequestVars']['tipoTransportes'] as $tipoTransporte ){
		$listaTipoTransporte[$tipoTransporte['id']] = array(
			"id" => utf8_encode($tipoTransporte['id']),
			"tipo" => utf8_encode($tipoTransporte['tipo']),
			"nombre" => utf8_encode($tipoTransporte['nombre']),
			"estatusactividad" => utf8_encode($tipoTransporte['estatusactividad'])
		);	
	}
	
	echo json_encode(array("listatipotransporte" => $listaTipoTransporte));