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
		//MONTOS PAGADOS
		//1ero los cheques
		$query=	"SELECT ".
					"spd.part_id as part_id, ".
					"COALESCE(SUM(spd.padt_monto),0) AS monto_pagado,paga_docu_id, ".
				    "cast(substr(spc.pgch_id,6) as integer) ".
				"FROM sai_pagado sp, sai_pagado_dt spd, sai_pago_cheque spc, sai_sol_pago ssp, sai_comp sc ".
				"WHERE ".
					"spc.pgch_id=sp.paga_docu_id and spc.docg_id=ssp.sopg_id and ssp.comp_id=sc.comp_id and ".
					"sc.pcta_id= '".$pcta."' and ".
					"sp.pres_anno = ".$anno_pres." AND ".
					"sp.esta_id <> 15 AND sp.esta_id <> 2 AND ".
					"sp.paga_id = spd.paga_id AND ".
					"sp.pres_anno = spd.pres_anno AND ".
					"spd.part_id LIKE '".$partida."%' ".
				"GROUP BY spd.part_id,paga_docu_id,spc.pgch_id ".
				"UNION ALL ".
				"SELECT ".
					"spd.part_id as part_id, ".
					"COALESCE(SUM(spd.padt_monto),0) AS monto_pagado,paga_docu_id,  ".
				    "cast(substr(spt.trans_id,6) as integer) ".		
				"FROM sai_pagado sp, sai_pagado_dt spd, sai_pago_transferencia spt, sai_sol_pago ssp, sai_comp sc ".
				"WHERE ".
					"spt.trans_id=sp.paga_docu_id and spt.docg_id=ssp.sopg_id and ssp.comp_id=sc.comp_id and ".
					"sc.pcta_id= '".$pcta."' and ".
					"sp.pres_anno = ".$anno_pres." AND ".
					"sp.esta_id <> 15 AND sp.esta_id <> 2 AND ".
					"sp.paga_id = spd.paga_id AND ".
					"sp.pres_anno = spd.pres_anno AND ".
					"spd.part_id LIKE '".$partida."%' ".
				"GROUP BY spd.part_id,paga_docu_id,spt.trans_id ".
				"UNION ALL ".
				"SELECT ".
					"spd.part_id as part_id, ".
					"COALESCE(SUM(spd.padt_monto),0) AS monto_pagado,paga_docu_id,  ".
				    "cast(substr(sco.comp_id,6) as integer) ".
				"FROM sai_pagado sp, sai_pagado_dt spd, sai_codi sco,sai_comp sc ".
				"WHERE ".
					"sco.comp_id=sp.paga_docu_id and sco.nro_compromiso=sc.comp_id and ".
					"sc.pcta_id= '".$pcta."' and ".
					"sp.pres_anno = ".$anno_pres." AND ".
					"sp.esta_id <> 15 AND sp.esta_id <> 2 AND ".
					"sp.paga_id = spd.paga_id AND ".
					"sp.pres_anno = spd.pres_anno AND ".
					"spd.part_id LIKE '".$partida."%' ".
				"GROUP BY spd.part_id,paga_docu_id,sco.comp_id ".
		
				"ORDER BY 4";
				//echo $query;
		$resultadoMontosCausados=pg_query($query) or die("Error en los montos causados");
?>
<div class="normalNegroNegrita" align="center">Detalle pagado - Monto total Bs.:<?=number_format($monto,2,',','.');?></div>
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
<td class="normalNegroNegrita">C&oacute;digo</td>
<td class="normalNegroNegrita">Monto Bs.</td>
<td class="normalNegroNegrita">Partida</td>
</tr>
<?php
$sumatoria=0; 
while ($filaCausados=pg_fetch_array($resultadoMontosCausados)) {
	$codigo=$filaCausados['paga_docu_id'];
	$monto=$filaCausados['monto_pagado'];
	$sumatoria=$sumatoria+$monto;
?>
<tr class="normal">
<td>
<?php if (strcmp(substr(trim($codigo), 0, 4),"pgch")==0) {?>
<a href="javascript:abrir_ventana('../../documentos/pgch/pgch_detalle.php?codigo=<?php echo trim($codigo); ?>&amp;esta_id=10')" class="copyright"><?=$codigo;?></a>
<?}?>
<?php if (strcmp(substr(trim($codigo), 0, 4),"tran")==0) {?>
<a href="javascript:abrir_ventana('../../documentos/tran/tran_detalle.php?codigo=<?php echo trim($codigo); ?>&amp;esta_id=10')" class="copyright"><?=$codigo;?></a>
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