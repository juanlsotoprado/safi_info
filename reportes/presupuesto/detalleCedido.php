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
$proy=$_GET['proy'];
$id_aesp=$_GET['aesp'];	
$fecha_inicio=$_GET['fecha_inicio'];
$fecha_fin=$_GET['fecha_fin'];	
$tipo=$_GET['tipo'];	
$consolidado=$_GET['consolidado'];
$centro_gestor=$_GET['centroGestor'];
$monto=$_GET['monto'];	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>SAFI:Detalle Recibido</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../js/funciones.js"> </script>
</head>
<body>
<?
//list($day_i,$month_i,$year_i) = split( '[/.-]', $fecha_inicio);
list($day_f,$month_f,$year_f) = split( '[/.-]', $fecha_fin);
	
if(!$consolidado && $centro_gestor==""){
	$query=	"SELECT ".
				"sf0305.f030_id as codigo, sf0305d.part_id as partida, ".
				"sf0305d.f0dt_monto as monto ".
			"FROM sai_forma_0305 sf0305, sai_fo0305_det sf0305d ".
			"WHERE ".
				"sf0305.pres_anno = ".$year_f." AND ".
				"sf0305.esta_id <> 15 AND sf0305.esta_id <> 2 AND ".
				"sf0305.f030_fecha BETWEEN to_date('".$fecha_inicio."', 'DD/MM/YYYY') AND to_date('".$fecha_fin."', 'DD/MM/YYYY')+1 AND ".
				"sf0305.f030_id = sf0305d.f030_id AND ".
				"sf0305.pres_anno = sf0305d.pres_anno AND ".
				"sf0305d.f0dt_id_acesp = '".$id_aesp."' AND ".
				"sf0305d.f0dt_tipo='0' AND ".
				"sf0305d.part_id LIKE '".$partida."%' ".
			"ORDER BY sf0305d.part_id";
} else {
	$query=	"SELECT ".
				"sf0305.f030_id as codigo, sf0305d.part_id as partida, ".
				"sf0305d.f0dt_monto as monto ".
			"FROM sai_forma_0305 sf0305, sai_fo0305_det sf0305d, ".
			"(";
	if($consolidado=="1" || $centro_gestor!=""){
		if($tipo=="1"){//proyecto
			$query .= 	"SELECT ".
					"spae.proy_id as id_proyecto_accion, ".
					"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
					"spae.proy_id = '".$proy."' AND ";
			if($centro_gestor && $centro_gestor!=""){
				$query .=	"spae.centro_gestor = '".$centro_gestor."' AND ";
			}
			$query .=		"spae.pres_anno = ".$year_f." ";
		}else if($tipo=="0"){//accion centralizada
			$query .= 	"SELECT ".
					"sae.acce_id as id_proyecto_accion, ".
					"sae.aces_id as id_accion_especifica ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
					"sae.acce_id = '".$proy."' AND ";
			if($centro_gestor && $centro_gestor!=""){
				$query .=	"sae.centro_gestor = '".$centro_gestor."' AND ";
			}
			$query .=		"sae.pres_anno = ".$year_f." ";
		}
	}else if($consolidado=="2"){
		$query .= 	"SELECT ".
				"spae.proy_id as id_proyecto_accion, ".
				"spae.paes_id as id_accion_especifica ".
				"FROM sai_proy_a_esp spae ".
				"WHERE ".
				"spae.pres_anno = ".$year_f." ";
	}else if($consolidado=="3"){
		$query .= 	"SELECT ".
				"spae.proy_id as id_proyecto_accion, ".
				"spae.paes_id as id_accion_especifica ".
				"FROM sai_proy_a_esp spae ".
				"WHERE ".
				"spae.pres_anno = ".$year_f." ".
				"UNION ".
				"SELECT ".
				"sae.acce_id as id_proyecto_accion, ".
				"sae.aces_id as id_accion_especifica ".
				"FROM sai_acce_esp sae ".
				"WHERE ".
				"sae.pres_anno = ".$year_f." ";
	}
	$query.=	") as s ".
			"WHERE ";
	if($consolidado=="1" || $centro_gestor!=""){
		$query.=	"sf0305d.f0dt_proy_ac = '".$tipo."' AND ".
				"sf0305d.f0dt_id_p_ac = '".$proy."' AND ";
	}
	$query.=	"sf0305.pres_anno = ".$year_f." AND ".
				"sf0305.esta_id <> 15 AND sf0305.esta_id <> 2 AND ".
				"sf0305.f030_fecha BETWEEN to_date('".$fecha_inicio."', 'DD/MM/YYYY') AND to_date('".$fecha_fin."', 'DD/MM/YYYY')+1 AND ".
				"sf0305.f030_id = sf0305d.f030_id AND ".
				"sf0305.pres_anno = sf0305d.pres_anno AND ".
				"sf0305d.f0dt_id_p_ac = s.id_proyecto_accion AND ".
				"sf0305d.f0dt_id_acesp = s.id_accion_especifica AND ".
				"sf0305d.f0dt_tipo='0' AND ".
				"sf0305d.part_id LIKE '".$partida."%' ".
			"ORDER BY sf0305d.part_id";
}
$resultadoMontosRecibidos=pg_query($query) or die("Error en los montos recibidos");
?>
<div class="normalNegroNegrita" align="center">Detalle Cedido - Monto total Bs.:<?=number_format($monto,2,',','.');?></div>
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
<td class="normalNegroNegrita">C&oacute;digo</td>
<td class="normalNegroNegrita">Monto Bs.</td>
<td class="normalNegroNegrita">Partida</td>
</tr>
<?php
$sumatoria=0; 
while ($row=pg_fetch_array($resultadoMontosRecibidos))  {
  	$sumatoria+=$row['monto'];
?>
<tr class="normal">
<td>
<a href="javascript:abrir_ventana('../../documentos/pmod/pmod_detalle.php?codigo=<?php echo trim($row['codigo']); ?>&amp;esta_id=10')" class="copyright">
<?=$row['codigo'];?>
</a>
</td>	
<td><? echo number_format($row['monto'],2,',','.');?></td>
<td><?=$row['partida'];?></td>
</tr>
<?php 
}
pg_close($conexion);
?>
<tr class="td_gray">
<td class="normalNegroNegrita">Total Bs.:</td>
<td colspan="2" class="normalNegroNegrita"><?echo number_format($sumatoria,2,',','.');?></td>
</tr>
</table>