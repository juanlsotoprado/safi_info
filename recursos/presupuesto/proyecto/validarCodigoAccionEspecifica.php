<?php
ob_start();
session_start();
require("../../../includes/conexion.php");
require('../../../includes/constantes.php');
$tipo=trim($_REQUEST['tipo']);
$anioPres=trim($_REQUEST['anioPres']);
$codigo=(($_REQUEST['codigo'])?trim($_REQUEST['codigo']):"");
$codigoAccionEspecifica=trim($_REQUEST['codigoAccionEspecifica']);
$centroGestor=trim($_REQUEST['centroGestor']);
$centroCostos=trim($_REQUEST['centroCostos']);
if($tipo==TIPO_IMPUTACION_PROYECTO){
	$query = 	"SELECT ".
					"COUNT(spae.paes_id) ".
				"FROM ".
					"sai_proy_a_esp spae ".
				"WHERE ".
					"spae.pres_anno = ".$anioPres." AND spae.paes_id = '".$codigoAccionEspecifica."'".(($codigo!="")?" AND proy_id<>'".$codigo."'":"");
	$resultado=pg_query($conexion,$query);
	$row=pg_fetch_array($resultado);
	if($row[0]>0){
		echo "codigo";
	}else{
		$query = 	"SELECT ".
						"COUNT(spae.centro_gestor) ".
					"FROM ".
						"sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anioPres." AND spae.centro_gestor = '".$centroGestor."'".(($codigo!="")?" AND proy_id<>'".$codigo."'":"");
		$resultado=pg_query($conexion,$query);
		$row=pg_fetch_array($resultado);
		if($row[0]>0){
			echo "centroGestor";
		}else{
			$query = 	"SELECT ".
							"COUNT(spae.centro_costo) ".
						"FROM ".
							"sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anioPres." AND spae.centro_costo = '".$centroCostos."'".(($codigo!="")?" AND proy_id<>'".$codigo."'":"");
			$resultado=pg_query($conexion,$query);
			$row=pg_fetch_array($resultado);
			if($row[0]>0){
				echo "centroCostos";
			}
		}
	}	
}else if($tipo==TIPO_IMPUTACION_ACCION_CENTRALIZADA){
	$query = 	"SELECT ".
					"COUNT(sae.aces_id) ".
				"FROM ".
					"sai_acce_esp sae ".
				"WHERE ".
					"sae.pres_anno = ".$anioPres." AND sae.aces_id = '".$codigoAccionEspecifica."'".(($codigo!="")?" AND acce_id<>'".$codigo."'":"");
	$resultado=pg_query($conexion,$query);
	$row=pg_fetch_array($resultado);
	if($row[0]>0){
		echo "codigo";
	}else{
		$query = 	"SELECT ".
						"COUNT(sae.centro_gestor) ".
					"FROM ".
						"sai_acce_esp sae ".
					"WHERE ".
						"sae.pres_anno = ".$anioPres." AND sae.centro_gestor = '".$centroGestor."'".(($codigo!="")?" AND acce_id<>'".$codigo."'":"");
		$resultado=pg_query($conexion,$query);
		$row=pg_fetch_array($resultado);
		if($row[0]>0){
			echo "centroGestor";
		}else{
			$query = 	"SELECT ".
							"COUNT(sae.centro_costo) ".
						"FROM ".
							"sai_acce_esp sae ".
						"WHERE ".
							"sae.pres_anno = ".$anioPres." AND sae.centro_costo = '".$centroCostos."'".(($codigo!="")?" AND acce_id<>'".$codigo."'":"");
			$resultado=pg_query($conexion,$query);
			$row=pg_fetch_array($resultado);
			if($row[0]>0){
				echo "centroCostos";
			}
		}
	}
}
ob_end_flush();
pg_close($conexion);
?>