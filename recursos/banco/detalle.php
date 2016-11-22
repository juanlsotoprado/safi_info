<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();    exit;
}
ob_end_flush(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Detalle de Entidad bancaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php 
$id=$_GET['codigo'];
$sql="select b.*, sb.ctab_numero, sb.ctab_saldoinicial, es.esta_nombre as estado
from sai_banco b
left outer join sai_ctabanco sb on (b.banc_id=sb.banc_id)
left outer join sai_estado es on (b.esta_id=es.esta_id)
where b.banc_id='".$id."'";
$i=1;
$resultado=pg_exec($sql);
while ($row=pg_fetch_array($resultado)) {
if ($i<2) {
?>
	<table width="90%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
	  <td class="normalNegroNegrita">Entidad bancaria</td>
	</tr>
	<tr class="normal">
	<td> <b>C&oacute;digo:&nbsp;&nbsp;</b> <?=$row['banc_id']?></td>
	</tr>
	<tr class="normal">
	<td> <b>Nombre:&nbsp;&nbsp;</b> <?=$row['banc_nombre']?></td>
	</tr>
	<tr class="normal">
	  <td> <b>P&aacute;gina Web:&nbsp;&nbsp;</b><?=$row['banc_www']?></td>
	  </tr>
	<tr class="normal">
	<td> <b>Estado:&nbsp;&nbsp;</b> <?=$row['estado']?></td>
	</tr>
	<tr>
	<td height="16" align="center" class="normal">
		<br/>
		<br/>
	   Detalle generado el d&iacute;a <?=date("d/m/y");?> a las <?=date("h:i:s");?><br/>
		<a href="javascript:window.print()" class="normal"><img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br/><br/>
	</td>
	</tr>
	</table>
	<br />
	<br />
	<?php }?>
	<table width="90%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
	  <td colspan="2" class="normalNegroNegrita">Cuenta <?=$i;?></td>
	</tr>
	<tr class="normalNegrita">
	<td>C&oacute;digo:</td>
	<td>Saldo Inicial</td>
	</tr>
	<tr>
	<td class="normal"><?=$row['ctab_numero']?></td>
	<td class="normal"><?=$row['ctab_saldoinicial']?></td>
	</tr>
<?
$i++;
}
pg_close($conexion);
?>
</table>
</body>
</html>