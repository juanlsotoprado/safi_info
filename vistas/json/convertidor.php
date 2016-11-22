<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaConvertidor = array();
	
	$convertidores = $GLOBALS['SafiRequestVars']['convertidor'];
	if($convertidores != null && is_array($convertidores) && count($convertidores) > 0)
		foreach( $convertidores as $convertidor ){
			if($convertidor instanceof EntidadConvertidor)
			{
				$convertidor->UTF8Encode();
				$listaConvertidor[] = $convertidor->ToArray();
			} else {
				$listaConvertidor = null;
			}
		}
	echo json_encode(array("listaConvertidor" => $listaConvertidor));
?>