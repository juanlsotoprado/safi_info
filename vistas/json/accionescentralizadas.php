<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaaccion = array();
	
	foreach( $GLOBALS['SafiRequestVars']['accionesCentralizadas'] as $accion ){
		$listaaccion[] = array(
			"id" => array(
				"idaccioncentralizada" => utf8_encode($accion['acce_id']),
				"anhopresupuesto" => utf8_encode($accion['pres_anno'])
			),
			"nombre" => utf8_encode($accion['acce_denom']),
			"anhopresupuesto" => utf8_encode($accion['pres_anno']),
			"idestatus" => utf8_encode($accion['esta_id']),
			"observacion" => utf8_encode($accion['acce_observa']),
			"loginregistrador" => utf8_encode($accion['usua_login']),
			"visibilidad" => utf8_encode($accion['acce_visib'])
		);	
	}
	
	echo json_encode(array("listaaccioncentralizada" => $listaaccion));