<?
ob_start();
session_start();
require_once("../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();
$partida=$_GET['partida'];
$idProyectoAccion=$_GET['proy'];
$idAccionEspecifica=$_GET['aesp'];
$fechaInicio=$_GET['fecha_inicio'];
$fechaFin=$_GET['fecha_fin'];
$tipoImputacion=$_GET['tipo'];
$opcionConsolidar=$_GET['consolidado'];
$centroGestor=$_GET['centroGestor'];
$monto=$_GET['monto'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>SAFI.:Detalle Compromiso Apartado</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="../../js/funciones.js"></script>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css">
</head>
<body>
<?
list($diaInicio,$mesInicio,$anioInicio) = split('[/.-]', $fechaInicio);
list($diaFin,$mesFin,$anioFin) = split('[/.-]', $fechaFin);

//$sql_str="SELECT * FROM sai_pres_reporte_mcompromisos_pcta_det(".$year_f.",'".$tipo."','".$proy."','".$id_aesp."','".$partida."','','".$opcionConsolidar."','".$anioInicio."/".$mesInicio."/".$diaInicio."','".$year_f."/".$month_f."/".$day_f."') as resultado";//
/*$sql_str="SELECT * FROM sai_disponibilidad_apartado_detalle(".$year_f.",'".$tipo."','".$proy."','".$id_aesp."','".$partida."','','".$opcionConsolidar."','".$anioInicio."/".$mesInicio."/".$diaInicio."','".$year_f."/".$month_f."/".$day_f."','".$centroGestor."') as resultado";
$res=pg_exec($sql_str);
$row_dif=pg_fetch_array($res);
$apartados = explode(",", $row_dif['resultado']);*/

$detalleApartado = '';
if($opcionConsolidar == '0' && $centroGestor == ''){
	/*$query =		"SELECT ".
						"DISTINCT(spt.pcta_id) AS documento, ".
						"spit.pcta_monto AS monto ".
					"FROM ".
						"sai_pcta_imputa_traza spit, ".
						"(SELECT ".
							"spit.pcta_id, ".
							"MAX(spit.pcta_fecha) AS fecha ".
						"FROM ".
							"sai_pcta_traza spt, ".
							"sai_pcta_imputa_traza spit ".
						"WHERE ".
							"spit.pres_anno = ".$anioInicio." AND ".
							"spit.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days' AND ".
							"spt.esta_id <> 15 AND spt.esta_id <> 2 AND ".
							"spt.pcta_id = spit.pcta_id AND ".
							"spit.pcta_tipo_impu = ".$tipoImputacion."::BIT AND ".
							"spit.pcta_acc_pp = '".$idProyectoAccion."' AND ".
							"spit.pcta_acc_esp = '".$idAccionEspecifica."' ".
						"GROUP BY spit.pcta_id) AS s, ".
						"sai_pcta_traza spt ".
					"WHERE ".
						"spit.pcta_id = spt.pcta_id AND ".
						"spit.pcta_id = s.pcta_id AND ".
						"s.pcta_id = spt.pcta_id AND ".
						"spit.pcta_fecha = s.fecha AND ".
						"spit.pcta_tipo_impu = '".$tipoImputacion."' AND ".
						"spit.pcta_acc_pp = '".$idProyectoAccion."' AND ".
						"spit.pcta_acc_esp = '".$idAccionEspecifica."' AND ";
	if(substr($partida,-8) == '00.00.00'){
		$query .= 	"spit.pcta_sub_espe LIKE '".substr($partida,0,4)."' ";
	}else if(substr($partida,-5) == '00.00.00'){
		$query .= 	"spit.pcta_sub_espe LIKE '".substr($partida,0,7)."' ";
	}else{
		$query .= 	"spit.pcta_sub_espe LIKE '".$partida."' ";
	}
	$query .=	"ORDER BY documento";*/
	$query =		"SELECT ".
						"spt.pcta_id AS documento, ".
						"SUM(spit.pcta_monto) AS monto ".
						",cast(substr(spt.pcta_id,6) as integer) ".
					"FROM ".
						"sai_pcta_imputa_traza spit, ".
						"sai_pcta_traza spt, ".
						"sai_pcuenta sp ".
					"WHERE ".
						"spit.pres_anno = ".$anioInicio." AND ".
						"spit.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days' AND ".
						"spit.pcta_id = spt.pcta_id AND ".
						"spit.pcta_id = sp.pcta_id AND ".
						"to_char(spit.pcta_fecha,'YYYY-MM-DD HH24:MI') = to_char(spt.pcta_fecha2,'YYYY-MM-DD HH24:MI') AND ".
						/*"spt.esta_id <> 15 AND spt.esta_id <> 2 AND ".*/
						"sp.esta_id <> 2 AND ".
						"(sp.pcta_asunto <> '020' OR sp.esta_id <> 15) AND ".
	
						"spit.pcta_tipo_impu = '".$tipoImputacion."' AND ".
						"spit.pcta_acc_pp = '".$idProyectoAccion."' AND ".
						"spit.pcta_acc_esp = '".$idAccionEspecifica."' AND ";
	if(substr($partida,-8) == '00.00.00'){
		$query .= 	"spit.pcta_sub_espe LIKE '".substr($partida,0,4)."' ";
	}else if(substr($partida,-5) == '00.00.00'){
		$query .= 	"spit.pcta_sub_espe LIKE '".substr($partida,0,7)."' ";
	}else{
		$query .= 	"spit.pcta_sub_espe LIKE '".$partida."' ";
	}
	$query .=	"GROUP BY spt.pcta_id ".
				"ORDER BY 3";
	$resultado=pg_exec($query);
	while($fila=pg_fetch_array($resultado)){
		$detalleApartado .= ','.$fila["documento"].':'.$fila["monto"];
	}
}else{
	$query =	"SELECT ".
					"spt.pcta_id AS documento, ".
					"SUM(spit.pcta_monto) AS monto ".
					",cast(substr(spt.pcta_id,6) as integer) ".
				"FROM ".
					"sai_pcta_imputa_traza spit, ".
					"(";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anioInicio." ";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anioInicio." ";
		}			
	}else if($opcionConsolidar=="2"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anioInicio." ";
	}else if($opcionConsolidar=="3"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anioInicio." ".
					"UNION ".
					"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.pres_anno = ".$anioInicio." ";
	}
	$query.=		") AS s, ".
					/*"(SELECT ".
						"spit.pcta_id, ".
						"MAX(spit.pcta_fecha) AS fecha ".
					"FROM ".
						"sai_pcta_traza spt, ".
						"sai_pcta_imputa_traza spit, ".
						"(";
	if($tipoImputacion=="1"){//proyecto
		$query .= 		"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
		if($centroGestor && $centroGestor!=""){
			$query .=		"spae.centro_gestor = '".$centroGestor."' AND ";
		}
		$query .=			"spae.pres_anno = ".$anioInicio." ";
	}else if($tipoImputacion=="0"){//accion centralizada
		$query .= 		"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
		if($centroGestor && $centroGestor!=""){
			$query .=		"sae.centro_gestor = '".$centroGestor."' AND ";
		}
		$query .=			"sae.pres_anno = ".$anioInicio." ";
	}
	$query.=			") as s ".
					"WHERE ".
						"spit.pres_anno = ".$anioInicio." AND ".
						"spit.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days' AND ".
						"spt.esta_id <> 15 AND spt.esta_id <> 2 AND ".
						"spt.pcta_id = spit.pcta_id AND ".
						"spit.pcta_tipo_impu = ".$tipoImputacion."::BIT AND ".
						"spit.pcta_acc_pp = '".$idProyectoAccion."' AND ".
						"spit.pcta_acc_pp = s.id_proyecto_accion AND ".
						"spit.pcta_acc_esp = s.id_accion_especifica ".
					"GROUP BY spit.pcta_id) AS ss, ".*/
					"sai_pcta_traza spt, ".
					"sai_pcuenta sp ".
				"WHERE ";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		$query.=		"spit.pcta_tipo_impu = ".$tipoImputacion."::BIT AND ".
						"spit.pcta_acc_pp = '".$idProyectoAccion."' AND ";
	}
	$query.=		"spit.pres_anno = ".$anioInicio." AND ".
					"spit.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days' AND ".
					"spit.pcta_id = spt.pcta_id AND ".
					"spit.pcta_id = sp.pcta_id AND ".
					"to_char(spit.pcta_fecha,'YYYY-MM-DD HH24:MI') = to_char(spt.pcta_fecha2,'YYYY-MM-DD HH24:MI') AND ".
					/*"spt.esta_id <> 15 AND spt.esta_id <> 2 AND ".*/
					"sp.esta_id <> 2 AND ".
					"(sp.pcta_asunto <> '020' OR sp.esta_id <> 15) AND ".
	
					"spit.pcta_acc_pp = s.id_proyecto_accion AND ".
					"spit.pcta_acc_esp = s.id_accion_especifica AND ";
	if(substr($partida,-8) == '00.00.00'){
		$query .= 	"spit.pcta_sub_espe LIKE '".substr($partida,0,4)."' ";
	}else if(substr($partida,-5) == '00.00.00'){
		$query .= 	"spit.pcta_sub_espe LIKE '".substr($partida,0,7)."' ";
	}else{
		$query .= 	"spit.pcta_sub_espe LIKE '".$partida."' ";
	}
	$query .=	"GROUP BY spt.pcta_id ".
				"ORDER BY 3";
	$resultado=pg_exec($query);
	while($fila=pg_fetch_array($resultado)){
		$detalleApartado .= ','.$fila["documento"].':'.$fila["monto"];
	}
}
?>
<div class="normalNegroNegrita" align="center">Detalle Apartado - Monto total Bs.:<?=number_format($monto,2,',','.');?></div>
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray">
		<td class="normalNegroNegrita">C&oacute;digo</td>
		<td class="normalNegroNegrita">Monto Bs.</td>
		<td class="normalNegroNegrita">Partida</td>
	</tr>
<?php
$apartados = explode(",", $detalleApartado);
$sumatoria=0;
for($i=1;$i<count($apartados);$i++) {
?>
	<tr class="normal">
	<?php
	list($codigo,$monto) = split('[:]', $apartados[$i]);
	$sumatoria+=$monto;
	?>
		<td>
			<a href="javascript:abrir_ventana('../../documentos/pcta/pcta_detalle.php?codigo=<?= trim($codigo)?>&amp;esta_id=10')" class="copyright"><?=$codigo;?></a>
		</td>
		<td><?= number_format($monto,2,',','.')?></td>
		<td><?= $partida?></td>
	</tr>
<?php
}
pg_close($conexion);
?>
	<tr class="td_gray">
		<td class="normalNegroNegrita">Total Bs.:</td>
		<td colspan="2" class="normalNegroNegrita"><?= number_format($sumatoria,2,',','.')?></td>
	</tr>
</table>