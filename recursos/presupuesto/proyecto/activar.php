<?php
ob_start();
session_start();
require("../../../includes/conexion.php");
require("../../../includes/constantes.php");
ob_end_flush();
$estadoActivo = 1;
$estadoInactivo = 2;
$anioPres = $_REQUEST['anioPres'];
$codigo = $_REQUEST['codigo'];
$tipo = $_REQUEST['tipo'];
$activar = $_REQUEST['activar'];
if($anioPres!="" && $codigo!="" && $tipo!="" && $activar!=""){
	if($tipo==TIPO_IMPUTACION_PROYECTO){
		if($activar=="true"){
			$sql="UPDATE sai_proyecto SET esta_id = ".$estadoActivo." WHERE proy_id = '".$codigo."' AND pre_anno = ".$anioPres;
			$resultado = pg_exec($conexion ,$sql);
			echo "activo";
		}else if($activar=="false"){
			$sql="UPDATE sai_proyecto SET esta_id = ".$estadoInactivo." WHERE proy_id = '".$codigo."' AND pre_anno = ".$anioPres;
			$resultado = pg_exec($conexion ,$sql);
			echo "inactivo";
		}		
	}else if($tipo==TIPO_IMPUTACION_ACCION_CENTRALIZADA){
		if($activar=="true"){
			$sql="UPDATE sai_ac_central SET esta_id = ".$estadoActivo." WHERE acce_id = '".$codigo."' AND pres_anno = ".$anioPres;
			$resultado = pg_exec($conexion ,$sql);
			echo "activo";
		}else if($activar=="false"){
			$sql="UPDATE sai_ac_central SET esta_id = ".$estadoInactivo." WHERE acce_id = '".$codigo."' AND pres_anno = ".$anioPres;
			$resultado = pg_exec($conexion ,$sql);
			echo "inactivo";
		}		
	}
}else{
	echo "error";
}
?>