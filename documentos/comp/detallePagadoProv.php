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
$ao_sopg="sopg-%".substr($anno_pres,2,2);	
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
				$query="SELECT COALESCE(SUM(scd.padt_monto),0) AS monto_pagado, coalesce(spc.pgch_id, ' ') || coalesce(spt.trans_id, ' ') as documento ".
					"FROM sai_sol_pago sp ".
      				"left outer join sai_pago_cheque spc on (spc.docg_id=sp.sopg_id and spc.esta_id<>15) ".
      				"left outer join sai_pago_transferencia spt on (spt.docg_id=sp.sopg_id and spt.esta_id<>15) ".
      				"left outer join sai_pagado sc on (sc.pres_anno = ".$anno_pres." AND sc.esta_id <> 15 AND sc.esta_id <> 2 ) ".
      				"left outer join sai_pagado_dt scd on (sc.paga_id = scd.paga_id AND sc.pres_anno = scd.pres_anno  AND scd.part_id NOT LIKE '4.11.0%' ) ".
		        	"WHERE comp_id='".$comp."' AND sp.esta_id<>15 AND (paga_docu_id=spc.pgch_id or paga_docu_id=spt.trans_id) ".
					"group by documento 
					UNION
					SELECT COALESCE(SUM(spd.padt_monto),0) AS monto_pagado,sco.comp_id AS documento
					FROM sai_pagado sp, sai_pagado_dt spd, sai_codi sco,sai_comp sc 
					WHERE sco.comp_id=sp.paga_docu_id and sco.nro_compromiso=sc.comp_id 
					and sc.comp_id='".$comp."' and sp.pres_anno =".$anno_pres." AND sp.esta_id <> 15 AND sp.esta_id <> 2 AND sp.paga_id = spd.paga_id 
					AND sp.pres_anno = spd.pres_anno AND spd.part_id NOT LIKE '4.11.0%'
 					GROUP BY sco.comp_id";
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
	$codigo=$filaCausados['documento'];
	$monto=$filaCausados['monto_pagado'];
	$sumatoria=$sumatoria+$monto;
?>
<tr class="normal">
<td>
<?=$codigo;?>
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