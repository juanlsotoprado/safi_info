<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	//error_log(print_r($detalleDisponibilidad,true));
	
	
	echo json_encode($GLOBALS['SafiRequestVars']['pctaDisponibilidad']);
?>