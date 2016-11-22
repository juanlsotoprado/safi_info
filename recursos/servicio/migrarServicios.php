<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");
require_once("../../includes/funciones.php");
if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	ob_end_flush();
	exit;
}
ob_end_flush();
$pres_anno = 2010;
$sql=	"SELECT part_id, part_nombre ".
		"FROM sai_partida ".
		"WHERE ".
			"pres_anno=".$pres_anno." AND ".
			"part_id LIKE '4.03%' AND ".
			"SUBSTRING(TRIM(part_id) FROM 9 for 5)<>'00.00' AND ".
			"part_id ".
				"NOT IN (SELECT DISTINCT part_id ".
					"FROM sai_arti_part_anno WHERE part_id LIKE '4.03%' AND pres_anno=".$pres_anno.") ".
			"ORDER BY part_nombre";

$resultado = pg_exec($conexion ,$sql);
$contador = 1;
$usua_login = "saiadmin";
$esta_id = 1;
while($row=pg_fetch_array($resultado)){
	$sql=	"INSERT INTO sai_servicios (id_servi, servi_nombre, esta_id, usua_login, descripcion) ".
			" VALUES ('".(($contador<10)?"0".$contador:$contador)."', '".cadenaAMayusculas(substr($row["part_nombre"], 0, 100))."', ".$esta_id.", '".$usua_login."', '".cadenaAMayusculas(substr($row["part_nombre"], 0, 100))."') ";
	$resultado2 = pg_exec($conexion ,$sql);
	
	$sql=	"INSERT INTO sai_serv_part_anno (serv_id, pres_anno, part_id) ".
			" VALUES ('".(($contador<10)?"0".$contador:$contador)."', ".$pres_anno.", '".$row["part_id"]."') ";
	$resultado2 = pg_exec($conexion ,$sql);
	$contador++;
}
?>