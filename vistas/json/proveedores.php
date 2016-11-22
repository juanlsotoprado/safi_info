<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaProveedores = array();
	
	foreach( $GLOBALS['SafiRequestVars']['listaProveedores'] as $proveedor ){
		$listaProveedores[$proveedor->GetRif()] = $proveedor->ToArray();
	}
	echo json_encode(array("listaProveedores" => $listaProveedores));