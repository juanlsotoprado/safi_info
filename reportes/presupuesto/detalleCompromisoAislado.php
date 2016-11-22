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
<title>SAFI.:Detalle Compromiso Aislado</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css">
</head>
<body>
<?
list($diaInicio,$mesInicio,$anioInicio) = split( '[/.-]', $fechaInicio);
list($diaFin,$mesFin,$anioFin) = split( '[/.-]', $fechaFin);

/*$sql_str="SELECT * FROM sai_pres_reporte_mcompromisos_aislado_det(".$anioFin.",'".$tipoImputacion."','".$idProyectoAccion."','".$idAccionEspecifica."','".$partida."','','".$opcionConsolidar."','".$anioInicio."/".$mesInicio."/".$diaInicio."','".$anioFin."/".$mesFin."/".$diaFin."') as resultado";//
$res=pg_exec($sql_str);
$row_dif=pg_fetch_array($res);
$apartados = explode(",",$row_dif['resultado']);*/

$detalleCompromisoAislado = '';
if($opcionConsolidar == '0' && $centroGestor == ''){
	$query = 	"SELECT ".
					"scit.comp_id AS documento, ".
					"scit.comp_monto AS monto  ".
					",cast(substr(scit.comp_id,6) as integer) ".
				"FROM sai_comp_imputa_traza scit, ".
					"(SELECT scit.comp_id, MAX(scit.comp_fecha) AS fecha ".
					"FROM sai_comp_traza sct, sai_comp_imputa_traza scit,sai_comp sc ".
					"WHERE ".
						"scit.pres_anno = ".$anioInicio." AND ".
						"length(sc.pcta_id) < 4 AND ".
	"scit.comp_id = sc.comp_id AND ".
						"scit.comp_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days' AND ".
						"sct.esta_id<>15 AND sct.esta_id<>2 AND ".
						"sct.comp_id NOT IN ".
							"(SELECT comp_id ".
							"FROM sai_comp_traza ".
							"WHERE ".
								"(esta_id=15 OR esta_id=2) AND ".
								"comp_fecha2 < to_date('".$fechaFin."', 'DD/MM/YYYY')+1) AND ".
						"sct.comp_id = scit.comp_id AND ".
						"scit.comp_tipo_impu = ".$tipoImputacion."::BIT AND ".
						"scit.comp_acc_pp = '".$idProyectoAccion."' AND ".
						"scit.comp_acc_esp = '".$idAccionEspecifica."' ".						
					"GROUP BY scit.comp_id) AS ss ".
				"WHERE ".
					"scit.comp_id = ss.comp_id AND ".
					"scit.comp_fecha = ss.fecha AND ".
					"scit.comp_tipo_impu = ".$tipoImputacion."::BIT AND ".
					"scit.comp_acc_pp = '".$idProyectoAccion."' AND ".
					"scit.comp_acc_esp = '".$idAccionEspecifica."' AND ";
	if(substr($partida,-8) == '00.00.00'){
		$query .= 	"scit.comp_sub_espe LIKE '".substr($partida,0,4)."' ";
	}else if(substr($partida,-5) == '00.00.00'){
		$query .= 	"scit.comp_sub_espe LIKE '".substr($partida,0,7)."' ";
	}else{
		$query .= 	"scit.comp_sub_espe LIKE '".$partida."' ";
	}
	$query .=	"ORDER BY 3 ASC";
	
	error_log(print_r($query,true));
	
	echo $query;
	
	$resultado=pg_exec($query);
	while($fila=pg_fetch_array($resultado)){
		$detalleCompromisoAislado .= ','.$fila["documento"].':'.$fila["monto"];
	}
}else{
	$query = 	"SELECT ".
					"scit.comp_id AS documento, ".
					"scit.comp_monto AS monto ".
					",cast(substr(scit.comp_id,6) as integer) ".
				"FROM sai_comp_imputa_traza scit, ".
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
	$query.=		") as s, ".
					"(SELECT scit.comp_id, MAX(scit.comp_fecha) AS fecha ".
					"FROM sai_comp_traza sct, sai_comp_imputa_traza scit, ".
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
		$query .= 		"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anioInicio." ";
	}else if($opcionConsolidar=="3"){
		$query .= 		"SELECT ".
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
	$query.=			") AS s ".
					"WHERE ";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		$query.=		"scit.comp_tipo_impu = ".$tipoImputacion."::BIT AND ".
						"scit.comp_acc_pp = '".$idProyectoAccion."' AND ";
	}
	$query.=			"scit.pres_anno = ".$anioInicio." AND ".
						"length(sct.pcta_id) < 4 AND ".
						"scit.comp_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days' AND ".
						"sct.esta_id <> 15 AND sct.esta_id <> 2 AND ".
						"sct.comp_id NOT IN ".
							"(SELECT comp_id ".
							"FROM sai_comp_traza ".
							"WHERE ".
								"(esta_id = 15 OR esta_id = 2) AND ".
								"comp_fecha2 < to_date('".$fechaFin."', 'DD/MM/YYYY')+1) AND ".
						"sct.comp_id = scit.comp_id AND ".
						"scit.comp_acc_pp = s.id_proyecto_accion AND ".
						"scit.comp_acc_esp = s.id_accion_especifica ".
					"GROUP BY scit.comp_id) AS ss ".
				"WHERE ";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		$query.=	"scit.comp_tipo_impu = ".$tipoImputacion."::BIT AND ".
					"scit.comp_acc_pp = '".$idProyectoAccion."' AND ";
	}
	$query.=		"scit.comp_id = ss.comp_id AND ".
					"scit.comp_fecha = ss.fecha AND ".
					"scit.comp_acc_pp = s.id_proyecto_accion AND ".
					"scit.comp_acc_esp = s.id_accion_especifica AND ";
	if(substr($partida,-8) == '00.00.00'){
		$query .= 	"scit.comp_sub_espe LIKE '".substr($partida,0,4)."' ";
	}else if(substr($partida,-5) == '00.00.00'){
		$query .= 	"scit.comp_sub_espe LIKE '".substr($partida,0,7)."' ";
	}else{
		$query .= 	"scit.comp_sub_espe LIKE '".$partida."' ";
	}
	$query .=	"ORDER BY 3 ASC";
	
					"ORDER BY se.centro_gestor, se.centro_costo, se.id_accion_especifica, scit.comp_sub_espe";
	
	
	$resultado=pg_exec($query);
	
	
		
	while($fila=pg_fetch_array($resultado)){
		$detalleCompromisoAislado .= ','.$fila["documento"].':'.$fila["monto"];
	}
		
}

//	echo "$query";

?>
<div class="normalNegroNegrita" align="center">Detalle compromiso aislado - Monto total Bs.:<?= number_format($monto,2,',','.');?></div>
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
<td class="normalNegroNegrita">C&oacute;digo</td>
<td class="normalNegroNegrita">Monto Bs.</td>
<td class="normalNegroNegrita">Partida</td>
</tr>
<?php
$comprometidoAislado = explode(",", $detalleCompromisoAislado);
$sumatoria=0; 
for($i=1;$i<count($comprometidoAislado);$i++){
?>
	<tr class="normal">
	<?php
	list($codigo,$monto) = split( '[:]', $comprometidoAislado[$i]);
	$sumatoria+=$monto;
	?>
		<td><a href="javascript:abrir_ventana('../../documentos/comp/comp_detalle.php?codigo=<?= trim($codigo)?>&amp;esta_id=10')" class="copyright"><?=$codigo?></a></td>
		<td><?= number_format($monto,2,',','.');?></td>
		<td><?= $partida;?></td>
	</tr>
<?php 
}


pg_close($conexion);
?>
	<tr class="td_gray">
		<td class="normalNegroNegrita">Total Bs.:</td>
		<td colspan="2" class="normalNegroNegrita"><?= number_format($sumatoria,2,',','.');?></td>
	</tr>
</table>
