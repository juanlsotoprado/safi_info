<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
require("../../includes/perfiles/constantesPerfiles.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Ingresar otro trabajador</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script>
function acceptIntt(evt){	
	// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57	
	var nav4 = window.Event ? true : false;
	
	var key = nav4 ? evt.which : evt.keyCode;	

	return (key <= 13 || (key >= 48 && key <= 57));

}

function buscar_cedula() { 
   var codigo;
   var codigo1;
   codigo=document.form1.identificacion.value;  
   if(codigo.length < 6) {
	      alert('El documento de identidad debe tener como m\u00ednimo seis(6) d\u00edgitos');
	  document.form1.identificacion.value='';
	  return;
   }
   <?php
   $sql="SELECT * FROM sai_viat_benef"; 
   $resultado=pg_query($conexion,$sql) or die("Error al consultar trabajador");
   while($row=pg_fetch_array($resultado)) {?>
    codigo1="<?php echo trim($row['benvi_cedula']); ?>"
	if (codigo==codigo1) {
	  alert("Documento de identificaci\u00f3n ya existe en la base de datos...");
	  document.form1.identificacion.value='';
	}
    <?php
   }
  ?> 
}
/***********************************************************************************/
function validar_numero(objeto) {
	var checkOK = "0123456789";
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
			objeto.value='';
			alert("Algunos caracteres no est\u00e1n permitidos,s\u00F3lo se permiten n\u00FAmeros");
			break;
		}
	}
}

function revisar() {
	if (document.form1.identificacion.value=='') {
	  alert("Debe escribir el documento de identidad del trabajador");
	  document.form1.identificacion.focus();
	  return;
	}
	else if (document.form1.txt_nombre.value=='') {
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

	else if (document.form1.txt_tipo.value=='0') {
		  alert("Debe seleccionar el tipo de trabajador");
		  document.form1.txt_tipo.focus();
		  return;
		}
	
	else if(confirm("Datos introducidos de manera correcta. Est\u00e1  seguro que desea continuar?."))
	 document.form1.submit();
}
</script>
</head>
<body>
<form name="form1" action="ingresarTrabajadorAccion.php" method="post">
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
<tr class="td_gray" >
<td colspan="2" class="normalNegroNegrita"><strong>INGRESAR OTRO TRABAJADOR</strong></td>
</tr>
<tr>
<td colspan="2" class="peq_naranja" align="center">
Los campos que tienen asterisco (*) son obligatorios
</td>
</tr>
<tr>
<td class="normalNegrita">Documento de Identidad:</td>
<td class="peq_naranja"> 
<span class="normal">Nacionalidad:<select name="nacionalidad">
<option value="V" selected>V</option>
<option value="E">E</option>
</select>
<input type="text" name="identificacion" value="" class="normal" maxlength="10" size="10" onkeypress="return validar_numero(identificacion)" onchange="javascript:buscar_cedula()" />*</td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Nombre(s): </td>
<td> <input type="text" name="txt_nombre" value="" class="normal" maxlength="30" size="40"/>*</td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Apellido(s):</td>
<td><input type="text" name="txt_apellido" value="" class="normal" maxlength="30" size="40"/>*</td>
</tr>
<tr>
<td class="normalNegrita">Dependencia:</td>
<td class="peq_naranja">
    <select name="cmb_dependencia" id="cmb_dependencia" class="normal">
	<option value="0">[Seleccione]</option>
	<?php
	if (substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) {
	   $sql="SELECT depe_id, depe_nombre FROM sai_dependenci where depe_id=".$_SESSION['user_depe_id']." and esta_id=1";
	}else{
	   $sql="SELECT depe_id, depe_nombre FROM sai_dependenci where depe_nivel in (4,3,2,1) and esta_id=1";	
	}
	echo (substr($_SESSION['user_perfil_id'],0,2)."000")."<br>";
	 echo $sql;
	$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
	while($row=pg_fetch_array($resultado)) { 
		?><option value="<?=trim($row['depe_id'])?>"><?php echo $row['depe_nombre'];?></option> <?php 
	} 
	?>
	</select>
    *</td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Tipo:</td>
<td> 
<select name="txt_tipo" id="txt_tipo" class="normal">
<option value="0">[Seleccione]</option>
<?php if (substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) {?>
<option value="Alfabetizador">Alfabetizador</option>
<option value="Promotor">Promotor</option>
<option value="Facilitador">Facilitador</option>
<?php }else{?>
<option value="Alfabetizador">Alfabetizador</option>
<option value="Promotor">Promotor</option>
<option value="Facilitador">Facilitador</option>
<option value="Beca Tecnol&oacute;gica">Beca Tecnol&oacute;gica</option>
<option value="Bolsa Trabajo">Bolsa Trabajo</option>
<option value="Enlace Estatal">Enlace Estatal</option>
<option value="HP">HP</option>
<option value="Invitado">Invitado</option>
<option value="Pasante">Pasante</option>
<?php }?>
</select>
</td>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td colspan="2" align="center">
	<input class="normalNegro" type="button" value="Ingresar" onclick="javascript:revisar();"/>		  
</td>
</tr>
</table>
</form>
</body>
</html>
 <?php pg_close($conexion);?>