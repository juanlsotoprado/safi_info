<?php 
	header("Content-type: application/json; charset=UTF-8");

	
   $compFiltro =	$GLOBALS['SafiRequestVars']['compFiltro'];

	echo json_encode($compFiltro);
	