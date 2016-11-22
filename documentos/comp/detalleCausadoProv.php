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
$comp=$_GET['comp'];	
$monto=$_GET['monto'];
$anno_pres=$_GET['aopres'];
$ao_sopg="%".substr($anno_pres,2,2);
	
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
				$query=" select sopg_id,sopg_monto 
	 			from sai_sol_pago sp,sai_doc_genera sdg 
	 			where sp.esta_id<>15 and
	 			sp.sopg_id like '".$ao_sopg."' and sdg.docg_id=sp.sopg_id and sp.comp_id='".$comp."'  
	 			UNION
	 			SELECT sci.comp_id as sopg_id,COALESCE(SUM(scd.cadt_monto),0) AS sopg_monto 
				FROM sai_codi sci 
				left outer join sai_causado sc on (sc.pres_anno = ".$anno_pres." AND sc.esta_id <> 15 AND sc.esta_id <> 2 AND sci.comp_id=caus_docu_id) ".
				"left outer join sai_causad_det scd on (sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno AND scd.part_id NOT LIKE '4.11.0%') ".
					" WHERE  sci.esta_id<>15  and nro_compromiso='".$comp."' group by sci.comp_id";
				
				//echo $query;
		$resultadoMontosCausados=pg_query($query) or die("Error en los montos causados");
?>
<div class="normalNegroNegrita" align="center">Detalle causado - Monto total Bs.:<?=number_format($monto,2,',','.');?></div>
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
<td class="normalNegroNegrita">C&oacute;digo</td>
<td class="normalNegroNegrita">Monto Bs.</td>
</tr>
<?php
$sumatoria=0; 
while ($filaCausados=pg_fetch_array($resultadoMontosCausados)) {
	$codigo=$filaCausados['sopg_id'];
	$monto=$filaCausados['sopg_monto'];
	$sumatoria=$sumatoria+$monto;
?>
<tr class="normal">
<td>
<?php if (substr($codigo,0,4)=="sopg"){?>
<a href="javascript:abrir_ventana('../../documentos/sopg/sopg_detalle.php?codigo=<?php echo trim($codigo); ?>&amp;esta_id=10')" class="copyright"><?=$codigo;?></a>
<?php }else{ ?>
<a href="javascript:abrir_ventana('../../documentos/codi/codi_detalle.php?codigo=<?php echo trim($codigo); ?>&amp;esta_id=10')" class="copyright"><?=$codigo;?></a>
<?php }?>
</td>	
<td><? echo number_format($monto,2,',','.');?></td>
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