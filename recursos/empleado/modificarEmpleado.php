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
<title>.:SAFI:Modificar Empleado</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../includes/js/funciones.js"> </script>
<script language="javascript">
//Valores del cargo a traves de la dependencia
function cargar_valores() {
	for (i = document.form1.slc_cargo_fundacion.length; i > 0; i--) {
		document.form1.slc_cargo_fundacion.options[i] = null
	}
	depe=document.form1.cmb_dependencia.value;
    <?php
	$query="SELECT depe_id FROM sai_dependenci";
	$result=pg_query($conexion,$query);
	while($row=pg_fetch_array($result)) {?>
		if (depe=="<?php echo trim($row['depe_id']); ?>") {
			<?php 
			$var=$row['depe_id'];
			$relacion="select carg_id,depe_id from sai_depen_cargo where depe_id='".$var."'"; 
			$resulta=pg_query($conexion,$relacion);
	        while($rowe=pg_fetch_array($resulta)) {
				$cargo=$rowe['carg_id'];
			    $query_cargo="select carg_nombre,carg_fundacion from sai_cargo where carg_id='".$cargo."'"; 
			    $res=pg_query($conexion,$query_cargo);
			    $rowc=pg_fetch_array($res);
			    $cargo_nomb=$rowc['carg_nombre'];
				$carg_fundacion=$rowc['carg_fundacion'];
			    ?>
				var NewOption = new Option("<?php echo $cargo_nomb; ?>", "<?php echo $carg_fundacion; ?>", false, false)
				document.form1.slc_cargo_fundacion.options[document.form1.slc_cargo_fundacion.length] = NewOption;
			    <?php } ?>
		}
	 <?php	}?>
}
//Validar Tlf
function verificar_tlf() {
  var tlf=trim(document.form1.txt_telefono.value);
  if(tlf.length < 4) {
  	  alert('El n\u00famero de tel\u00e9fono o la extensi\u00f3n debe contener al menos 4 d\u00edgitos');
	  document.form1.txt_telefono.value="";
	  document.form1.txt_telefono.focus();
	  return;
  }
}
//función que se utiliza para validar si el correo esta escrito de forma correcta
function validar_email() {
 if(document.form1.txt_email.value!='')  {
    if(document.form1.txt_email.value.search(/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/ig))     {
         alert("La cuenta de correo no es v\u00e1lida, debes escribirla de forma: nombre@servidor.dominio");
         document.form1.txt_email.value='';
         return;
    }
 }
} 
/***********************************************************************************/
function revisar() {
	if (document.form1.txt_nombre.value=='') {
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
}</script>
</head>
<body>
<?php
$cedula=trim($_GET['codigo']); 
$sql="SELECT * FROM sai_empleado where empl_cedula='".$cedula."'"; 
$resultado=pg_query($conexion,$sql) or die("Error al consultar empleado");
if($row=pg_fetch_array($resultado)) {
	$empl_cedula=trim($row['empl_cedula']);
    $empl_nombres=trim($row['empl_nombres']);
    $empl_apellidos=trim($row['empl_apellidos']);
    $empl_telefono=trim($row['empl_tlf_ofic']);
    $empl_nacionalidad=trim($row['nacionalidad']);
    $empl_email=trim($row['empl_email']);
    $depe_cosige=trim($row['depe_cosige']);
    $carg_fundacion=trim($row['carg_fundacion']);
    $empl_observaciones=trim($row['empl_observa']);
    $usua_login=trim($row['usua_login']);
    $esta_id=trim($row['esta_id']);
?>
<form name="form1" action="modificarEmpleadoAccion.php" method="post">
  <br />
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
<tr class="td_gray">
<td colspan="2" class="normalNegroNegrita"><strong>MODIFICAR EMPLEADO</strong></td>
</tr>
<tr>
<td colspan="2" class="peq_naranja" align="center">
Los campos que tienen asterisco (*) son obligatorios
</td>
</tr>
<tr>
<td class="normalNegrita">Documento de Identidad:</td>
<td class="peq_naranja"> 
<span class="normal">Nacionalidad:</span> <select name="nacionalidad" readonly class="normal">
<?php 
$selectedv="";
$selectede="";
if (strcmp($empl_nacionalidad,'V')==0) $selectedv="selected";
else $selectede="selected";
?>
<option value="V" <?=$selectedv?>>V</option>
<option value="E" <?=$selectede?>>E</option>
</select>
<input type="text" name="identificacion" value="<?=$empl_cedula?>" class="normal" maxlength="10" size="10" readonly/></td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Nombre(s):</td>
<td class="peq_naranja"> <input type="text" name="txt_nombre" value="<?=$empl_nombres?>" class="normal" maxlength="30" size="40" onkeyup="return validar(txt_nombre,1)"/>*</td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Apellido(s):</td>
<td class="peq_naranja"><input type="text" name="txt_apellido" value="<?=$empl_apellidos?>" class="normal" maxlength="30" size="40" onkeyup="return validar(txt_apellido,1)" />*</td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Tel&eacute;fono de Oficina:</td>
<td><input type="text" name="txt_telefono" value="<?=$empl_telefono?>" class="normal" maxlength="12" size="15" onkeypress="return acceptNum(event)" onchange="verificar_tlf()"/></td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Email:</td>
<td class="peq_naranja"><input name="txt_email" type="text" size="50" maxlength="50" class="normal" value="<?=$empl_email?>" onchange="validar_email()"/></td>
</tr> 
<tr>
<td class="normalNegrita">Dependencia:</td>
<td class="peq_naranja">
    <select name="cmb_dependencia" id="cmb_dependencia" class="normal" onchange="cargar_valores()">
	<?php
	$sql="SELECT depe_id,depe_nombre FROM sai_dependenci where esta_id=1"; 
	$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
	while($row=pg_fetch_array($resultado)) { 
		$depe=trim($row['depe_id']);
		?><option value="<?=$depe?>" <?php if($depe_cosige==$depe){?> selected <?php }?> ><?php echo $row['depe_nombre'];?></option> <?php 
	} 
	?>
	</select>
    *</td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Cargo en la Fundaci&oacute;n:</td>
<td class="peq_naranja">
    <select name="slc_cargo_fundacion" id="slc_cargo_fundacion" class="normal">
	<?php
	$sql="SELECT carg_fundacion, carg_nombre FROM sai_cargo where carg_fundacion='".$carg_fundacion."'"; 
	$resultado=pg_query($conexion,$sql) or die("Error al mostrar los cargos");
	while($row=pg_fetch_array($resultado)) { 
		$cargo=trim($row['carg_fundacion']);
		?><option value="<?=$cargo?>" <?php if($carg_fundacion==$cargo){?> selected <?php }?> ><?php echo $row['carg_nombre'];?></option> <?php 
	} 
	?>
	</select>
    *</td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Observaciones:</td>
<td class="peq_naranja"><textarea name="txt_observa" rows="3" class="peq" cols="58"><?=$empl_observaciones?></textarea></td>
</tr> 
<tr>
<td class="normalNegrita">Estado actual:</td>
<td class="normal">
	<?php  
	if($esta_id==1){?>
	<input name="opt_estado" type="radio" value="1" checked="checked" />Activo
	<input name="opt_estado" type="radio" value="2"/>No Activo
	<?php }else{?>
	<input name="opt_estado" type="radio" value="1"/>Activo
	<input name="opt_estado" type="radio" value="2" checked="checked"/>No Activo
	<?php }?>
	</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td colspan="2">
	<div align="center">
	<input class="normalNegro" type="button" value="Modificar" onclick="javascript:revisar();"/>
	</div>
</td>
</tr>
</table>
</form>
<?php } pg_close($conexion);?>
</body>
</html>