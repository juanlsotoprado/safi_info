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
<title>.:SAFI:Ingresar Entidad Bancaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
$codigo=trim($_POST['txt_codigo_enti']);
$nombre=trim($_POST['txt_nombre_enti']);
$pagina=trim($_POST['txt_pagina_enti']);
$usuario = $_SESSION['login'];

$sql_banc="select * from sai_insert_banco('".$codigo."','".$nombre."','".$pagina."','".$usuario."')"; 
$resultado_banc=pg_query($conexion,$sql_banc);
if($row_banc=pg_fetch_array($resultado_banc)) {
?>
<br />
<br />
	<table width="50%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
	  <td class="normalNegroNegrita">Registro de entidad bancaria</td>
	</tr>
	<tr class="normal">
	<td> <b>C&oacute;digo:&nbsp;&nbsp;</b> <?=$codigo;?></td>
	</tr>
	<tr class="normal">
	<td> <b>Nombre:&nbsp;&nbsp;</b> <?=$nombre;?></td>
	</tr>
	<tr class="normal">
	  <td> <b>P&aacute;gina Web:&nbsp;&nbsp;</b><?=$pagina;?></td>
	  </tr>
	<tr class="normal">
	<td> <b>Estado:&nbsp;&nbsp;</b> Activa</td>
	</tr>
	<tr>
	<td height="16" align="center" class="normal">
		<br/>
		<br/>
	   Detalle generado el d&iacute;a <?=date("d/m/y");?> a las <?=date("h:i:s");?><br/>
		<br/>
		<a href="javascript:window.print()" class="normal"><img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br/><br/>
		<input class="normalNegro" type="button" value="Regresar" onclick="javascript:location.href='ingresar.php';" />
 	</td>
	</tr>
	</table>
	<?php } else   { ?>
		<table width="50%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="normal"> 
		<td class="titularMedio style1">INGRESAR ENTIDAD BANCARIA </td>
		</tr>
		<tr>
		<td class="normal" align="center">
		<img src="../../imagenes/vineta_azul.gif" width="11" height="7"></img>
		Ha ocurrido un error al ingresar los datos<br/>
		<?php echo(pg_errormessage($conexion)); ?><br/>
		<img src="../../imagenes/mano_bad.gif" width="31" height="38"/>
		<br/>
		<input class="normalNegro" type="button" value="Regresar" onclick="javascript:location.href='ingresar.php';"/> 
		<br/>
		</td>
		</tr>
		</table>
	<?php  }  pg_close($conexion); ?> 
</body>
</html>