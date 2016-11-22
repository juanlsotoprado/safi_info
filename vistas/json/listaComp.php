<?php 
	header("Content-type: application/json; charset=UTF-8");

	
	$beginning = array('N/A' => '0');
	
	
	foreach ($GLOBALS['SafiRequestVars']['compFiltro'] AS &$var1)
	{
		foreach ($var1 AS &$var2){
			$var2 = utf8_encode($var2);
		}
	}
	
	unset($var1);
	unset($var2);
	
	$end = $GLOBALS['SafiRequestVars']['compFiltro'];
	$compFiltro = array_merge((array)$beginning, (array)$end);

	echo json_encode($compFiltro);
	