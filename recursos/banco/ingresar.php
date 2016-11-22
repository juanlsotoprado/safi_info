<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Ingresar entidad bancaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="javascript">
//Buscar nombre para evaluar que solo exista uno en la base de datos
function buscar_denominacion() { 
	var nombre;
	var nombre1; 
	nombre=document.form.txt_nombre_enti.value;
	<?php
	$sql_p="SELECT banc_nombre FROM sai_banco"; 
	$resultado_set_most_p=pg_query($conexion,$sql_p);
	while($row=pg_fetch_array($resultado_set_most_p)) {?>
		nombre1="<?php echo trim($row['banc_nombre']); ?>"
		if (nombre.toUpperCase()==nombre1.toUpperCase()) {
			alert("La entidad bancaria ya se encuentra registrada");
			document.form.txt_nombre_enti.value='';
			document.form.txt_nombre_enti.focus();
			return;
		}
<?php } ?>
}
//Buscar denominacion corto para evaluar que solo exista uno en la base de datos
function buscar_codigo() { 
	var codigo;
	var codigo1; 
	codigo=document.form.txt_codigo_enti.value;
	<?php
	$sql_p="SELECT banc_id FROM sai_banco"; 
	$resultado_set_most_p=pg_query($conexion,$sql_p);
	while($row=pg_fetch_array($resultado_set_most_p)) {?>
		codigo1="<?php echo trim($row['banc_id']); ?>"
		if (codigo.toUpperCase()==codigo1.toUpperCase()) {
			alert("Este c\u00f3digo de entidad bancaria ya se encuentra registrado en el sistema");
			document.form.txt_codigo_enti.value='';
			document.form.txt_codigo_enti.focus();
			return;
		}
<?php } ?>
}
function revisar() {
	if (document.form.txt_codigo_enti.value=='' || document.form.txt_codigo_enti.value.length!=4) {
		alert("Debe indicar el c\u00f3digo de 4 d\u00edgitos de la entidad bancaria");
		document.form.txt_codigo_enti.focus();
		return;
	}
	else if (document.form.txt_nombre_enti.value=='') {
		alert("Debe indicar el nombre de la entidad bancaria");
		document.form.txt_nombre_enti.focus();
		return;
	}
	else if (confirm("Datos introducidos de manera correcta. Est\u00e1 seguro que desea continuar?.")) 
		document.form.submit();
}
</script>
</head>

<body>
<form name="form" action="ingresarAccion.php" method="post">
  <table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
<tr class="td_gray"> 
 <td colspan="2" class="normalNegroNegrita">Registrar entidad bancaria </td>
</tr>
<tr>
  <td colspan="2"><div align="center" class="peq_naranja">Los campos que tienen asterisco (*) son obligatorios</div></td>
  </tr>
<tr> 
<td class="normalNegrita">C&oacute;digo:</td>
<td class="peq"><input name="txt_codigo_enti" type="text" class="normal" id="txt_codigo_enti" value="" size="4" maxlength="4" onkeyup="buscar_codigo()" onkeypress="return acceptNum(event);" /> <span class="peq_naranja">(*)</span> </td>
</tr>
<tr> 
<td class="normalNegrita">Nombre:</td>
<td class="peq"><input name="txt_nombre_enti" type="text" class="normal" id="txt_nombre_enti" value="" size="30" maxlength="200" onkeyup="buscar_denominacion()" /> <span class="peq_naranja">(*)</span> </td>
</tr>
<tr> 
<td class="normalNegrita">P&aacute;gina Web:</td>
<td class="peq"><input type="text" name="txt_pagina_enti" id="txt_pagina_enti" class="normal" maxlength="20" size="30" value=""/></td>
</tr>
<tr>
  <td colspan="2" align="center"><br />
  	<input class="normalNegro" type="button" value="Ingresar" onclick="javascript:revisar();"/>
</td>
</tr>
</table>
</form>
</body>
</html>
<?php pg_close($conexion);?>