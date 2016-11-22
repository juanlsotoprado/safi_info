<?php
header("Content-type: application/json; charset=UTF-8");

$compromiso = array();

if($GLOBALS['SafiRequestVars']['compromisoDetalle']){
		
	$Comp  = $GLOBALS['SafiRequestVars']['compromisoDetalle'];

	$Comp->UTF8Encode();

	$compromiso['compromiso'] =  $Comp->ToArray();
	
	
     //  error_log(print_r($compromiso['compromiso'],true));
	
	if($GLOBALS['SafiRequestVars']['observacionesDoc']){
	
	$compromiso['compromiso']['motivoAnulacion'] = current($GLOBALS['SafiRequestVars']['observacionesDoc']);

	}

}


 //error_log(print_r($compromiso['compromiso'],true));

echo json_encode($compromiso);
?>