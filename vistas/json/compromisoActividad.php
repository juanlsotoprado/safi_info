<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$actividad = $GLOBALS['SafiRequestVars']['actividad'];
	
	
	$actividades =array();
	
	if($actividad){

	   foreach ($actividad as $index => $valor){
	   	
	   		$valor->UTF8Encode();
	   		$actividades[$index] = $valor->ToArray();
	   	
	   }
		
	

	
	}else{
		
		   $actividades = false;
	
	}

	//error_log(print_r($actividades,true));
	
	echo json_encode($actividades);
?>