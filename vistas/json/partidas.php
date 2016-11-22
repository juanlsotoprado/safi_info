<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaPartida = array();
	
	foreach( $GLOBALS['SafiRequestVars']['partidas'] as $partida ){
		if($partida instanceof EntidadPartida){
			$partida->UTF8Encode();
			$listaPartida[$partida->GetId()] = $partida->ToArray();
		}	
	}

	echo json_encode(array("listaPartida" => $listaPartida));
?>