<?php
	$basePath = dirname(realpath(dirname(__FILE__).'/../index.php'));
	include_once($basePath.'/lib/general.php');
	include_once($basePath.'/config/config.php');

	session_start();

	$conexion=pg_connect("
		host=".GetConfig("dbServer")."
		port=".GetConfig("dbPort")."
		dbname=".GetConfig("dbDatabase")."
		user=".GetConfig("dbUser")."
		password=".GetConfig("dbPass")."
	"); 

	if (!$conexion){
		echo "<CENTER> Problemas de conexion con la base de datos. </CENTER>";
        exit;
	}else{
		pg_query($conexion,"set client_encoding to '".GetConfig("dbEncoding")."'");
	}	
?>