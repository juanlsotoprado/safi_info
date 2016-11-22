<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$asunto = $GLOBALS['SafiRequestVars']['asunto'];
	$asuntos =array();
	
	if($asunto){

	   foreach ($asunto as $index => $valor){
	   	
	   		$valor->UTF8Encode();
	   		$asuntos[$index] = $valor->ToArray();
	   	
	   }
		
		
	}else{
		   $asuntos = false;
	
	}

	echo json_encode($asuntos);
?>