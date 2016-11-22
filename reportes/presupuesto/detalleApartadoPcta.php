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
 //MONTO APARTADO DEL PCTA MAS SUS ALCANCES
		$query=	
				"SELECT ".
					"spi.pcta_sub_espe as part_id,spi.pcta_id as pcta_id, ".
					"SUM(spi.pcta_monto) as monto_apartado, ".
					"cast(substr(spi.pcta_id,6) as integer) ".
				"FROM sai_pcta_imputa spi ".
 				"WHERE ".
					"spi.pcta_id ='".$pcta."' and ".
					"spi.pcta_sub_espe='".$partida."' and ".
					"spi.pcta_id IN ".
					"(SELECT docg_id ".
					 "FROM sai_doc_genera sdg ".
					 "WHERE ".
					 "esta_id=13 AND ".
					 "sdg.docg_id = spi.pcta_id) ".
				"GROUP BY 1 ,spi.pcta_id ".
		"UNION ".
				"SELECT ".
					"spi.pcta_sub_espe as part_id, spi.pcta_id as pcta_id, ".
					"SUM(spi.pcta_monto) as monto_apartado, ".
					"cast(substr(spi.pcta_id,6) as integer) ".
				"FROM sai_pcta_imputa spi, sai_pcuenta sp  ".
 				"WHERE ".
					"sp.pcta_id=spi.pcta_id and sp.pcta_asociado='".$pcta."' and ".
					"spi.pcta_sub_espe='".$partida."' and ".
					"spi.pcta_id IN ".
					"(SELECT docg_id ".
					 "FROM sai_doc_genera sdg ".
					 "WHERE ".
					 "esta_id=13 AND ".
					 "sdg.docg_id = spi.pcta_id) ".
				"GROUP BY 1 ,spi.pcta_id ".
				"order by 4 ";

		$resultadoMontoApartado=pg_query($query) or die("Error en el monto apartado");	
			
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
while ($filaComprometidos=pg_fetch_array($resultadoMontoApartado)) {
	$codigo=$filaComprometidos['pcta_id'];
	$monto=$filaComprometidos['monto_apartado'];
	$sumatoria=$sumatoria+$monto;
?>
<tr class="normal">
<td><a href="javascript:abrir_ventana('../../documentos/pcta/pcta_detalle.php?codigo=<?php echo trim($codigo); ?>&amp;esta_id=10')" class="copyright"><?=$codigo;?></a></td>	
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