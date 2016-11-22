<?php
ob_start();
session_start();
require("../../../includes/conexion.php");
require('../../../includes/constantes.php');
$tipo=trim($_REQUEST['tipo']);
$anioPres=trim($_REQUEST['anioPres']);
$codigo=trim($_REQUEST['codigo']);
if($tipo==TIPO_IMPUTACION_PROYECTO){
	$query = 	"SELECT ".
					"COUNT(sp.proy_id) ".
				"FROM ".
					"sai_proyecto sp ".
				"WHERE ".
					"sp.proy_id = '".$codigo."' AND sp.pre_anno = ".$anioPres;
	$resultado=pg_query($conexion,$query);
	$row=pg_fetch_array($resultado);
	if($row[0]==0){
		echo "false";
	}else{
		echo "true";
	}
}else if($tipo==TIPO_IMPUTACION_ACCION_CENTRALIZADA){
	$query = 	"SELECT ".
					"COUNT(sac.acce_id) ".
				"FROM ".
					"sai_ac_central sac ".
				"WHERE ".
					"sac.acce_id = '".$codigo."' AND sac.pres_anno = ".$anioPres;
	$resultado=pg_query($conexion,$query);
	$row=pg_fetch_array($resultado);
	if($row[0]==0){
		echo "false";
	}else{
		echo "true";
	}
}
ob_end_flush();
pg_close($conexion);
?>