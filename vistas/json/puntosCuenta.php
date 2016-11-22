<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaPuntoCuenta = array();
	
	if(is_array($GLOBALS['SafiRequestVars']['puntosCuenta']))
	{
		foreach( $GLOBALS['SafiRequestVars']['puntosCuenta'] as $puntoCuenta ){
			if($puntoCuenta instanceof EntidadPuntoCuenta){
				$puntoCuenta->UTF8Encode();
				$listaPuntoCuenta[$puntoCuenta->GetId()] = $puntoCuenta->ToArray();
			}	
		}
	}

	echo json_encode(array("listaPuntoCuenta" => $listaPuntoCuenta));
?>