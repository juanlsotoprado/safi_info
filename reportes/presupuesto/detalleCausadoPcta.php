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
$pcta=$_GET['pcta'];	
$anno_pres=$_GET['aopres'];
$monto=$_GET['monto'];
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>SAFI:Detalle Causado</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../js/funciones.js"> </script>
</head>
<body>
<?
				//MONTOS CAUSADOS
				$query=	"SELECT ".
					"scd.part_id, ".
					"scd.cadt_monto AS monto_causado,caus_docu_id, ".
				    "cast(substr(ssp.sopg_id,6) as integer) ".
				"FROM sai_causado sc, sai_causad_det scd, sai_sol_pago ssp, sai_comp  scomp ".
				"WHERE ".
					"ssp.sopg_id=sc.caus_docu_id and scomp.pcta_id='".$pcta."' and ".
					"ssp.comp_id=scomp.comp_id and ".
					"sc.pres_anno = ".$anno_pres." AND ".
					"sc.esta_id <> 15 AND sc.esta_id <> 2 AND ".
					"sc.caus_id = scd.caus_id AND ".
					"sc.pres_anno = scd.pres_anno AND ".
					"scd.part_id LIKE '".$partida."%' ".
				"UNION ALL ".
				"SELECT scd.part_id, scd.cadt_monto AS monto_causado,caus_docu_id, ". 
				 "cast(substr(scodi.comp_id,6) as integer) ".
				"FROM sai_causado sc, sai_causad_det scd, sai_codi scodi, sai_comp scomp ". 
				"WHERE scodi.comp_id=sc.caus_docu_id and scodi.nro_compromiso=scomp.comp_id ".
				"and scomp.pcta_id='".$pcta."' and ".
					"sc.pres_anno = ".$anno_pres." AND ".
					"sc.esta_id <> 15 AND sc.esta_id <> 2 AND ".
					"sc.caus_id = scd.caus_id AND ".
					"sc.pres_anno = scd.pres_anno AND ".
				"scd.part_id LIKE '".$partida."%' ".
				"ORDER BY 4";
				//echo $query;
		$resultadoMontosCausados=pg_query($query) or die("Error en los montos causados");
?>
<div class="normalNegroNegrita" align="center">Detalle causado - Monto total Bs.:<?=number_format($monto,2,',','.');?></div>
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
<td class="normalNegroNegrita">C&oacute;digo</td>
<td class="normalNegroNegrita">Monto Bs.</td>
<td class="normalNegroNegrita">Partida</td>
</tr>
<?php
$sumatoria=0; 
while ($filaCausados=pg_fetch_array($resultadoMontosCausados)) {
	$codigo=$filaCausados['caus_docu_id'];
	$monto=$filaCausados['monto_causado'];
	$sumatoria=$sumatoria+$monto;
?>
<tr class="normal">
<td>
<?php if (strcmp(substr(trim($codigo), 0, 4),"sopg")==0) {?>
<a href="javascript:abrir_ventana('../../documentos/sopg/sopg_detalle.php?codigo=<?php echo trim($codigo); ?>&amp;esta_id=10')" class="copyright"><?=$codigo;?></a>
<?}?>
<?php if (strcmp(substr(trim($codigo), 0, 4),"codi")==0) {?>
<a href="javascript:abrir_ventana('../../documentos/codi/codi_detalle.php?codigo=<?php echo trim($codigo); ?>&amp;esta_id=10')" class="copyright"><?=$codigo;?></a>
<?}?>
</td>	
<td><? echo number_format($monto,2,',','.');?></td>
<td><?=$partida;?></td>
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