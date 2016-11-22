<?php 
	header("Content-type: application/json; charset=UTF-8");

	$listaCiudad= array();
	
	
	 $GLOBALS['SafiRequestVars']['DependenciaSolicitado'] = $DependenciaSolicitado;  
	 

		$listaCiudad = array(
		
			"id" => utf8_encode($DependenciaSolicitado->GetId()),
			"nombre" => utf8_encode($DependenciaSolicitado->GetNombre()),
			
		);	

	
	echo json_encode($listaCiudad);