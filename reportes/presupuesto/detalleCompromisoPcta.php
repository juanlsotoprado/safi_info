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
<title>SAFI.:Detalle Compromiso</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="../../js/funciones.js"> </script>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css">
</head>
<body>
<?
	//MONTOS COMPROMETIDOS
	$query=	"SELECT ".
			"scit.comp_sub_espe as part_id,sct.comp_id, ".
			"scit.comp_monto as monto_comprometido, ".
			"cast(substr(sct.comp_id,6) as integer) ".
			"FROM sai_comp sct, sai_comp_imputa scit ".
			"WHERE ".
			"scit.pres_anno = ".$anno_pres." AND ".
			"sct.pcta_id ='".$pcta."' AND ".
			"sct.esta_id <> 15 AND sct.esta_id <> 2 AND ".
			"sct.comp_id = scit.comp_id  AND ".
			"scit.comp_sub_espe LIKE '".$partida."%' ".
			"ORDER BY 4";

	$resultadoMontosComprometidos=pg_query($query) or die("Error en los montos comprometidos");
		
			
?>
<div class="normalNegroNegrita" align="center">Detalle compromiso - Monto total Bs.:<?=number_format($monto,2,',','.');?></div>
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
<td class="normalNegroNegrita">C&oacute;digo</td>
<td class="normalNegroNegrita">Monto Bs.</td>
<td class="normalNegroNegrita">Partida</td>
</tr>

<?php
$sumatoria=0; 
while ($filaComprometidos=pg_fetch_array($resultadoMontosComprometidos)) {
	$codigo=$filaComprometidos['comp_id'];
	$monto=$filaComprometidos['monto_comprometido'];
	$sumatoria=$sumatoria+$monto;
?>
<tr class="normal">
<td><a href="javascript:abrir_ventana('../../documentos/comp/comp_detalle.php?codigo=<?php echo trim($codigo); ?>&amp;esta_id=10')" class="copyright"><?=$codigo;?></a></td>	
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