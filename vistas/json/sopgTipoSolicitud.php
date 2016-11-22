<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$evento = $GLOBALS['SafiRequestVars']['tipoSolpagos'];
	
	
	$eventos =array();
	
	if($evento){

	   foreach ($evento as $index => $valor){
	   	
	   		$valor->UTF8Encode();
	   		$eventos[$index] = $valor->ToArray();
	   	
	   }
		

	}else{
		
		   $eventos = false;
	
	}

	echo json_encode($eventos);
?>