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
<title>.:SAFI: Modificar Otro Trabajador</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script src="../../js/funciones.js"></script>
<script>
function validar_objeto(objeto) {
	var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789()-_/";
	var checkStr = objeto.value;
	var allValid = true;
	for (i = 0;  i < checkStr.length;  i++) {
		ch = checkStr.charAt(i);
		for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
		if (j == checkOK.length) {
			var cambio=checkStr.substring(-1,i) 
			objeto.value=cambio;
			alert("Algunos car\u00e1cteres no est\u00e1n permitidos");
			break;
		}
	}
}

function revisar() {
	if (document.form1.txt_nombre.value=='') {
	  alert("Debe colocar el nombre del trabajador");
	  document.form1.txt_nombre.focus();
	  return;
	}
	else if (document.form1.txt_apellido.value=='') {
	  alert("Debe colocar el apellido del trabajador");
	  document.form1.txt_apellido.focus();
	  return;
	}
	else if (document.form1.cmb_dependencia.value=='0') {
	  alert("Debe seleccionar la dependencia");
	  document.form1.cmb_dependencia.focus();
	  return;
	}
	
	else if(confirm("Datos introducidos de manera correcta. Est\u00e1  seguro que desea continuar?."))
	 document.form1.submit();
}
<?php
$cedula=trim($_GET['codigo']); 
$sql="SELECT v.*, est.esta_nombre, d.depe_nombre from sai_viat_benef v, sai_estado est, sai_dependenci d where v.benvi_esta_id=est.esta_id and v.depe_id=d.depe_id and v.benvi_cedula='".$cedula."'"; 
$resultado=pg_query($conexion,$sql) or die("Error al consultar trabajador");
if($row=pg_fetch_array($resultado)) {
    $benvi_cedula=trim($row['benvi_cedula']);
    $benvi_nombres=trim($row['benvi_nombres']);
    $benvi_apellidos=trim($row['benvi_apellidos']);
    $benvi_nacionalidad=trim($row['nacionalidad']);
    $depe_cosige=trim($row['depe_nombre']);
    $depe_id=trim($row['depe_id']);
    $benvi_tipo=trim($row['tipo']);
    $benvi_estado=trim($row['benvi_esta_id']);
?>
</script>
</head>
<body>
<form name="form1" action="modificarTrabajadorAccion.php" method="post">
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
<tr class="td_gray" >
<td colspan="2" class="normalNegroNegrita"><strong>MODIFICAR OTRO TRABAJADOR</strong></td>
</tr>
<tr align="center">
<td colspan="2" class="peq_naranja">Los campos que tienen asterisco (*) son obligatorios</td>
</tr>
<tr>
<td class="normalNegrita">Documento de Identidad:</td>
<td class="peq_naranja"> 
<span class="normal">Nacionalidad:</span> <select name="nacionalidad" readonly>
<?php 
$selectedv="";
$selectede="";
if (strcmp($benvi_nacionalidad,'V')==0) $selectedv="selected";
else $selectede="selected";
?>
<option value="V" <?=$selectedv?>>V</option>
<option value="E" <?=$selectede?>>E</option>
</select>
<input type="text" name="identificacion" value="<?=$benvi_cedula?>" class="normal" maxlength="10" size="10" readonly/></td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Nombre(s):</td>
<td class="peq_naranja"><input type="text" name="txt_nombre" value="<?php echo $benvi_nombres;?>" class="normal" maxlength="30" size="40" onkeyup="return validar(txt_nombre,1)"/>*</td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Apellido(s):</td>
<td><input type="text" name="txt_apellido" value="<?php echo $benvi_apellidos;?>" class="normal" maxlength="30" size="40" onkeyup="return validar(txt_apellido,1)" />*</td>
</tr>
<tr>
<td class="normalNegrita">Dependencia:</td>
<td class="peq_naranja">
    <select name="cmb_dependencia" id="cmb_dependencia" class="normal">
	<option value="">[Seleccione]</option>
	<?php
	$sql="SELECT depe_id,depe_nombre FROM sai_dependenci where esta_id=1 and depe_nivel<=4"; 
	$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
	$selectee="";
	while($row=pg_fetch_array($resultado)) 	{ 
		if (strcmp(trim($row['depe_nombre']),$depe_cosige)==0) $selectee="selected";
		else $selectee="";
		?><option value="<?=trim($row['depe_id'])?>" <?=$selectee?>><?php echo $row['depe_nombre'];?></option>
	 <?php } ?>
	</select>
    *</td>
</tr>
<?
$selectea="";
$selectebe="";
$selectebo="";
$selecteh="";
$selectep="";
$selectej="";
$selectee="";
$selectet="";
$selectei="";

if (strcmp($benvi_tipo,"Alfabetizador")==0) $selectea="selected";
if (strcmp($benvi_tipo,"Beca Tecnológica")==0) $selectebe="selected";
if (strcmp($benvi_tipo,"Bolsa Trabajo")==0) $selectebo="selected";
if (strcmp($benvi_tipo,"HP")==0) $selecteh="selected";
if (strcmp($benvi_tipo,"Invitado")==0) $selectei="selected";
if (strcmp($benvi_tipo,"Pasante")==0) $selectep="selected";
if (strcmp($benvi_tipo,"Junta Directiva")==0) $selectej="selected";
if (strcmp($benvi_tipo,"Enlace Estatal")==0) $selectee="selected";
if (strcmp($benvi_tipo,"Ex Trabajador")==0) $selectet="selected";
if (strcmp($benvi_tipo,"Promotor")==0) $selectet="selected";


?>
<tr class="normal"> 
<td class="normalNegrita">Tipo:</td>
<td class="peq_naranja"> 
<select name="txt_tipo" id="txt_tipo" class="normal"> 
<option value="Alfabetizador" <?=$selectea?>>Alfabetizador</option>
<option value="Beca Tecnol&oacute;gica">Beca Tecnol&oacute;gica</option>
<option value="Bolsa Trabajo" <?=$selectebo?>>Bolsa Trabajo</option>
<option value="Enlace Estatal" <?=$selectee?>>Enlace Estatal</option>
<option value="Ex Trabajador" <?=$selectet?>>Ex Trabajador</option>
<option value="HP" <?=$selecteh?>>HP</option>
<option value="Invitado" <?=$selectei?>>Invitado</option>
<option value="Junta Directiva" <?=$selectej?>>Junta Directiva</option>
<option value="Pasante" <?=$selectep?>>Pasante</option>
<option value="Promotor" <?=$selectep?>>Promotor</option>
<option value="Facilitador" <?=$selectep?>>Facilitador</option>
</select>
</td>
</tr> 
<tr>
<td class="normalNegrita">Estado:</td>
<?
$selecteact="";
$selecteinact="";
if (strcmp($benvi_estado,'1')==0) {
$selecteact="selected";}
else {
$selecteinact="selected";}
?>
<td><select name="estado" class="normal">
<option value="1" <?=$selecteact?>>Activo</option>
<option value="2" <?=$selecteinact?>>Inactivo</option>
</select></td>
</tr>
<tr>
<td colspan="2">
	<div align="center">
	<input class="normalNegro" type="button" value="Modificar" onclick="javascript:revisar();"/>		  
	</div>
</td>
</tr>
<tr><td height="16" colspan="3">&nbsp;</td></tr>
</table>
</form>
<?} pg_close($conexion);?>
</body>
</html>