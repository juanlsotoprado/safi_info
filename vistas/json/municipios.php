<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaMunicipio = array();
	
	foreach( $GLOBALS['SafiRequestVars']['municipios'] as $municipio ){
		if($municipio instanceof EntidadMunicipio){
			$municipio->UTF8Encode();
			$listaMunicipio[$municipio->GetId()] = $municipio->ToArray();
		}	
	}

	echo json_encode(array("listamunicipio" => $listaMunicipio));
?>