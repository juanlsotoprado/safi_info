<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	//error_log(print_r($GLOBALS['SafiRequestVars']['Anular'],true));
	
	
	echo json_encode($GLOBALS['SafiRequestVars']['Anular']);
?>