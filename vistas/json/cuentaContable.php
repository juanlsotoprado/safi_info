<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaCuenta = array();
	
	foreach($GLOBALS['SafiRequestVars']['cuentas'] as $cuenta ){
		if($cuenta instanceof EntidadCuentaContable){
			$cuenta->UTF8Encode();
			$listaCuenta[$cuenta->GetId()] = $cuenta->ToArray();
		}	
	}

	echo json_encode(array("listaCuenta" => $listaCuenta));
?>