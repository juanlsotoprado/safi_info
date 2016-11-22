<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");
if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	ob_end_flush();
	exit;
}
ob_end_flush();
$estadoActivo = "1";
$estadoInactivo = "2";
$rif = "";
if (isset($_REQUEST['rif']) && $_REQUEST['rif'] != "") {
	$rif = $_REQUEST['rif'];
}
$estado = "";
if (isset($_REQUEST['estado']) && $_REQUEST['estado'] != "") {
	$estado = $_REQUEST['estado'];
}
if($rif!="" && $estado!=""){
	if($estado==$estadoActivo){
		$sql="UPDATE sai_proveedor_nuevo SET prov_esta_id = ".$estado." WHERE prov_id_rif = '".$rif."'";
		$resultado = pg_exec($conexion ,$sql);
		echo "activo";	
	}else if($estado==$estadoInactivo){
		$sql="UPDATE sai_proveedor_nuevo SET prov_esta_id = ".$estado." WHERE prov_id_rif = '".$rif."'";
		$resultado = pg_exec($conexion ,$sql);
		echo "inactivo";
	}	
}else{
	echo "error";
}
?>