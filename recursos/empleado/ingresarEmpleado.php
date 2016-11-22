<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}ob_end_flush(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Ingresar Empleado</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../includes/js/funciones.js"> </script>
<script>
//función que se utiliza para validar si el correo está escrito de forma correcta
function validar_email() {
 if(document.form1.txt_email.value!='')  {
    if(document.form1.txt_email.value.search(/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/ig))     {
         alert("La cuenta de correo no es v\u00e1lida, debes escribirla de forma: nombre@servidor.dominio");
         document.form1.txt_email.value='';
         return;
    }
 }
} 
// Validar que la cedula no exista
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
   $sql_p="SELECT * FROM sai_empleado"; 
   $resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al consultar empleado");
   while($row=pg_fetch_array($resultado_set_most_p)) {?>
    codigo1="<?php echo trim($row['empl_cedula']); ?>"
	if (codigo==codigo1) {
	  alert("El documento de identidad ya se encuentra registrado en la base de datos");
	  document.form1.identificacion.value='';
	}
    <?php
   }
  ?> 
}
/***********************************************************************************/
function validar_numero(objeto) {
	var checkOK = "pPdD0123456789";
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
			alert("Algunos car\u00e1cteres no est\u00e1n permitidos en el documento de identidad");
			break;
		}
	}
}

function verificar_tlf() {
  var tlf=trim(document.form1.txt_telefono.value);
  if(tlf.length < 4) {
  	  alert('El n\u00famero de tel\u00e9fono o la extensi\u00f3n debe contener al menos 4 d\u00edgitos');
	  document.form1.txt_telefono.value="";
	  document.form1.txt_telefono.focus();
	  return;
  }
}
//Validar Campos
function revisar() {
	if (document.form1.identificacion.value=='') {
	  alert("Debe escribir el documento de identidad del empleado");
	  document.form1.identificacion.focus();
	  return;
	}
	else if (document.form1.txt_nombre.value=='') {
	  alert("Debe escribir el nombre del empleado");
	  document.form1.txt_nombre.focus();
	  return;
	}
	else if (document.form1.txt_apellido.value=='') 	{
	  alert("Debe escribir el apellido del empleado");
	  document.form1.txt_apellido.focus();
	  return;
	}
	else if (document.form1.slc_cargo_fundacion.value=='') {
	  alert("Debe seleccionar el cargo del empleado en la Fundaci\u00f3n");
	  document.form1.slc_cargo_fundacion.focus();
	  return;
	}
	else if (document.form1.cmb_dependencia.value=='0') {
	  alert("Debe seleccionar la dependencia");
	  document.form1.cmb_dependencia.focus();
	  return;
	}
	else if(confirm("Datos introducidos de manera correcta. Est\u00e1 seguro que desea continuar?."))
			document.form1.submit();
}
</script>
<script language="javascript">
//Valores del cargo a traves de la dependencia
function cargar_valores() {
	for (i = document.form1.slc_cargo_fundacion.length; i > 0; i--) 
		document.form1.slc_cargo_fundacion.options[i] = null
	depe=document.form1.cmb_dependencia.value;
    <?php
	$query = "
		SELECT
			d.depe_id,
			c.carg_nombre,
			c.carg_fundacion
		FROM
			sai_dependenci d,
			sai_depen_cargo dc,
			sai_cargo c
		where
			d.depe_id = dc.depe_id 
			and dc.carg_id = c.carg_id
		ORDER BY
			c.carg_nombre
	"; 
	$result=pg_query($conexion,$query);
	while($row=pg_fetch_array($result))	{?>
		if (depe==<?php echo trim($row['depe_id']);?>)	{
			<?php 
			$var=$row['depe_id'];
		    $cargo_nomb=$row['carg_nombre'];
			$carg_fundacion=$row['carg_fundacion'];
			?>
				var NewOption = new Option("<?php echo $cargo_nomb; ?>", "<?php echo $carg_fundacion; ?>", false, false)
				document.form1.slc_cargo_fundacion.options[document.form1.slc_cargo_fundacion.length] = NewOption;
		}	
	<?php } ?>
}			 
</script>
</head>
<body>
<form name="form1" action="ingresarEmpleadoAccion.php" method="post">
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
<tr class="td_gray">
<td colspan="2" class="normalNegroNegrita"><strong>INGRESAR EMPLEADO</strong></td>
</tr>
<tr>
<td colspan="2" class="peq_naranja" align="center">
Los campos que tienen asterisco (*) son obligatorios
</td>
</tr>
<tr>
<td class="normalNegrita">Documento de Identidad:</td>
<td class="peq_naranja"> 
<span class="normal">Nacionalidad:</span> <select name="nacionalidad">
<option value="V" selected>V</option>
<option value="E">E</option>
</select>
<input type="text" name="identificacion" value="" class="normal" maxlength="10" size="10" onkeypress="return validar_numero(this)" onchange="javascript:buscar_cedula()" />*</td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Nombre(s):</td>
<td class="peq_naranja"> <input type="text" name="txt_nombre" value="" class="normal" maxlength="30" size="40" onkeyup="return validar(txt_nombre,1)"/>*</td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Apellido(s):</td>
<td class="peq_naranja"><input type="text" name="txt_apellido" value="" class="normal" maxlength="30" size="40" onkeyup="return validar(txt_apellido,1)" />*</td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Tel&eacute;fono de Oficina:</td>
<td><input type="text" name="txt_telefono" value="7718000" class="normal" maxlength="12" size="15" onkeypress="return acceptNum(event)" onchange="verificar_tlf()"/></td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Email:</td>
<td class="peq_naranja"><input name="txt_email" type="text" size="50" maxlength="50" class="normal" value="@infocentro.gob.ve" onchange="validar_email()"/></td>
</tr> 
<tr>
<td class="normalNegrita">Dependencia:</td>
<td class="peq_naranja">
    <select name="cmb_dependencia" id="cmb_dependencia" class="normal" onchange="cargar_valores()">
	<option value="">[Seleccione]</option>
	<?php
	$sql="SELECT depe_id,depe_nombre FROM sai_dependenci where esta_id=1 ORDER BY depe_nombre"; 
	$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
	while($row=pg_fetch_array($resultado))	{ 
		?><option value="<?=trim($row['depe_id'])?>"><?php echo $row['depe_nombre'];?></option> <?php 
	} 
	?>
	</select>
    *</td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Cargo:</td>
<td class="peq_naranja">
<select name="slc_cargo_fundacion" id="slc_cargo_fundacion" class="normal">
<option value="">[Seleccione]</option>
</select>*</td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Observaciones:</td>
<td class="peq_naranja"><textarea name="txt_observa" rows="3" class="peq" cols="58"></textarea></td>
</tr> 
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td colspan="2">
	<div align="center">
	<input class="normalNegro" type="button" value="Ingresar" onclick="javascript:revisar();"/>		  
	</div>
</td>
</tr>
</table>
</form>
</body>
</html>
 <?php pg_close($conexion); ?>