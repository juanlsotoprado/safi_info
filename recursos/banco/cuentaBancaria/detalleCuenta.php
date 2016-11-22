<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}ob_end_flush(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Detalle Cuenta</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../../js/funciones.js"> </script>
</head>
<body>
<?php
$codigo=trim($_GET['codigo']);
$sql="SELECT cb.cpat_id,
		TO_CHAR(cb.ctab_fechacierrereg,'DD-MM-YYYY') AS fecha_cierre_sistema,
		TO_CHAR(cb.ctab_fechaapert,'DD-MM-YYYY') AS fecha_apertura,
		TO_CHAR(cb.ctab_fechareg,'DD-MM-YYYY') AS fecha_registro,
		TO_CHAR(cb.ctab_fechacierre,'DD-MM-YYYY') AS fecha_cierre,
		cb.ctab_numero,
		cb.banc_id,
		b.banc_nombre,
		e.esta_nombre,
		cb.ctab_ano,
		cb.ctab_descripcion,
		cb.ctab_estatus,
		tc.tipo_nombre,
		cb.tipo_id,
		ctab_saldoinicial
	FROM sai_ctabanco cb,
		sai_banco b,
		sai_estado e,
		sai_tipocuenta tc
	WHERE cb.banc_id = b.banc_id
		AND cb.tipo_id = tc.tipo_id
		AND cb.ctab_estatus = e.esta_id
		AND ctab_numero='".$codigo."'";
$resultado=pg_query($conexion,$sql);
if($row=pg_fetch_array($resultado)) {
	$banco=trim($row['banc_nombre']);
	$ctab_numero=trim($row['ctab_numero']);
	$cpat_id=trim($row['cpat_id']);
	$ctab_descripcion=trim($row['ctab_descripcion']);
	$fecha_apertura=trim($row['fecha_apertura']);
	$fecha_registro=trim($row['fecha_registro']);
	$fecha_cierre=trim($row['fecha_cierre']);
	$fecha_cierre_sistema=trim($row['fecha_cierre_sistema']);	
	$tipo_cuenta=trim($row['tipo_nombre']);
	$saldo=trim($row['ctab_saldoinicial']);
	$estado=$row['esta_nombre'];
?>
<table width="80%" align="center" background="../../../imagenes/fondo_tabla.gif" bgcolor="#FFFFFF" class="tablaalertas" >
<tr class="td_gray">
    <td colspan="2" class="normalNegroNegrita">Cuenta bancaria</td>
</tr>
	<tr class="normal"> 
	<td class="normalNegrita">Banco:</td>
	<td><?php echo $banco;?></td>
	</tr>
<tr class="normal"> 
<td class="normalNegrita">N&uacute;mero de cuenta:</td>
<td><?php echo $ctab_numero;?></td>
</tr>
<tr class="normal"> 
<td class="normalNegrita"> Estado: </td>
<td><?php echo $estado;?></td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Cuenta contable:</td>
<td><?php echo $cpat_id;?></td>
</tr>
<tr class="normal"> 
<td class="normalNegrita"> Descripci&oacute;n : </td>
<td><?php echo $ctab_descripcion;?></td>
</tr> 
	<tr class="normal"> 
	<td class="normalNegrita">Tipo de cuenta:</td>
	<td><?php echo $tipo_cuenta;?></td>
	</tr> 
<tr class="normal"> 
<td class="normalNegrita">Monto de apertura: </td>
<td><?php echo number_format($saldo,2,'.',',');?></td>
</tr>
	<tr class="normal"> 
	<td class="normalNegrita"> Fecha de registro en el sistema:</td>
	<td><?php echo $fecha_registro;?></td>
	</tr> 
	<tr class="normal"> 
	<td class="normalNegrita">Fecha de apertura en el banco:</td>
	<td><?php echo $fecha_apertura;?></td>
	</tr> 
<?php if ($fecha_cierre_sistema!='') { ?>
	<tr class="normal"> 
	<td class="normalNegrita">Fecha de cierre en el sistema:</td>
	<td><?php echo $fecha_cierre_sistema;?></td>
	</tr> 
<?php } ?>
<?php if ($fecha_cierre!='') { ?>
	<tr class="normal"> 
	<td class="normalNegrita">Fecha de cierre en el banco:</td>
	<td><?php echo $fecha_cierre;?></td>
	</tr> 
<?php } ?>
<tr>
<td colspan="2" align="center" class="normal">
<br/>
Detalle generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br/>
<br/>
<a href="javascript:window.print()" class="normal"><img src="../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br/><br/>
</td>
</tr>
<tr><td height="16" colspan="3">&nbsp;</td></tr>
</table>
<?php
}
	 else  {   ?>
		<table width="50%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="normal"> 
		<td class="normalNegroNegrita">Cuenta bancaria</td>
		</tr>
		<tr>
		<td class="normal" align="center">
		Ha ocurrido un error al modificar los datos<br/>
		<?php echo(pg_errormessage($conexion)); ?><br/>
		<img src="../../../imagenes/mano_bad.gif" width="31" height="38"/>
		</td>
		</tr>
		</table>
<?php } pg_close($conexion); ?>   
</body>
</html>