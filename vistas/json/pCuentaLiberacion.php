<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	// error_log(print_r($GLOBALS['SafiRequestVars']['PcuantaLiberacion'],true));
	
	echo json_encode($GLOBALS['SafiRequestVars']['PcuantaLiberacion']);
?>