<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaInfocentro = array();
	$infocentros = $GLOBALS['SafiRequestVars']['infocentros'];
	
	if($infocentros != null && is_array($infocentros) && count($infocentros) > 0){
	
		
		foreach( $infocentros as $infocentro ){
			if($infocentro instanceof EntidadInfocentro){
				$infocentro->UTF8Encode();
				$listaInfocentro[$infocentro->GetId()] = $infocentro->ToArray();
			}	
			
			
		}
	}
   
	echo json_encode(array("listainfocentro" => $listaInfocentro));
?>