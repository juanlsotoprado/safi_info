<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaSolicitudPago = array();
	
	if(is_array($GLOBALS['SafiRequestVars']['arrSolicitudPago']))
	{
		foreach( $GLOBALS['SafiRequestVars']['arrSolicitudPago'] as $solicitudPago ){
			if($solicitudPago instanceof EntidadSolicitudPago){
				$solicitudPago->UTF8Encode();
				$listaSolicitudPago[$solicitudPago->GetId()] = $solicitudPago->ToArray();
			}	
		}
	}

	echo json_encode(array("listaSolicitudPago" => $listaSolicitudPago));
?>