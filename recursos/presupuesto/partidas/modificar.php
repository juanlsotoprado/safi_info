<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 
 ?>
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI:Modificar Partida</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="../../../includes/js/funciones.js"> </script>
<script>
//Función que permite verficar que la descripción no se encuentre repetida
function verificar_cod() { 
   codigo=document.form1.txt_codigo.value;
   <?php
   $sql_p="SELECT part_id FROM sai_partida"; 
   $resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al consultar codigo de partida");
   while($row=pg_fetch_array($resultado_set_most_p)) {?>
    codigo1="<?php echo trim($row['part_id']); ?>"
	if (codigo==codigo1) {
	 alert("Esta partida ya se encuentra registrada en la base de datos...");
	 document.form1.txt_codigo.value='';
	}
    <?php
   }
  ?> 
}
//Funcion que permite validar que solo se escriban numeros y puntos en el campo
function validar_cod(objeto) {
	//alert(objeto.value);
	var checkOK = "0123456789. ";
	var checkStr = objeto.value;
	var allValid = true;
	for (i = 0;  i < checkStr.length;  i++)
	{
		ch = checkStr.charAt(i);
		for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
		if (j == checkOK.length)
		{
			var cambio=checkStr.substring(-1,i) 
			objeto.value=cambio;
			alert("Escriba solo digitos caracteres y puntos...");
			break;
		}
	}
} 
//Función que valida el llenado de todos los campos 
function revisar() {
    if (document.form1.txt_nombre.value=="") {
		alert("Debe colocar el nombre de la partida.")
		document.form1.txt_nombre.focus();
		return;
	}
   	//Verificamos que el usuario este seguro de la operación
	if(confirm("Estos datos ser\u00E1n registrados. Est\u00E1 seguro que desea continuar?")) {
		  document.form1.submit()
	}
}

function clargo(txt, dst, formul, maximo) {
	var largo
	largo = formul[txt].value.length
	 if (largo > maximo) {
	   formul[txt].value = formul[txt].value.substring(0,maximo)
	 }
	formul[dst].value = formul[txt].value.length
}	
</script>
</head>
<body>
<?php 
$partida=trim($_GET['codigo']);
$pres_anno=trim($_GET['ano']);
$sql="SELECT p.part_id, p.part_nombre, p.pres_anno, p.part_observa, p.usua_login, p.esta_id, e.esta_nombre FROM sai_partida p, sai_estado e where p.esta_id=e.esta_id and part_id='".$partida."' AND pres_anno=".$pres_anno; 
$resultado=pg_query($conexion,$sql) or die("Error al consultar la partida a modificar");
if($row=pg_fetch_array($resultado)) {
	$part_id=$row['part_id'];
	$part_nombre=$row['part_nombre'];
	$part_observa=$row['part_observa'];
	$usua_login=$row['usua_login'];
	$esta_id=$row['esta_id'];
	$esta_nombre=$row['esta_nombre'];	
}
?>
<form name="form1" method="post" action="modificarAccion.php">
   <table width="60%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray"> 
		<td colspan="2" class="normalNegroNegrita">MODIFICAR PARTIDA PRESUPUESTARIA</td>
    </tr>
    <tr>
      <td class="normalNegrita">C&oacute;digo de la Partida:</td>
      <td>
      <input name="txt_codigo" type="text" class="normal" size="20"  value="<?php echo $part_id;?>" maxlength="20" disabled/>
	  <input type="hidden" name="txt_cod_partida" id="txt_cod_partida" value="<?php echo $part_id;?>" />	  
	  </td>
    </tr>
   <tr>
      <td class="normalNegrita">A&ntilde;o:</td>
      <td>
      <input name="txt_ano" type="text" class="normal" size="20"  value="<?php echo $pres_anno;?>" maxlength="20" disabled/>
	  <input type="hidden" name="txt_ano_partida" id="txt_ano_partida" value="<?php echo $pres_anno;?>" />	  
	  </td>
    </tr>    
    <tr>
      <td class="normalNegrita">Nombre de la Partida:</td>
      <td><textarea name="txt_nombre" class="normal" onkeypress="clargo('txt_nombre','txt_num',document.form1,250)" cols="40" rows="3"><?php echo $part_nombre;?></textarea></td>
    </tr>
	<tr>
      <td class="normalNegrita">Estado Actual:</td>
      <td class="normal"><input name="txt_pres_anno" type="hidden" value="<?php echo $pres_anno;?>"/>
	   <?php if($esta_id==1){?>
		<input name="opt_estado" type="radio" value="1" checked="checked" />Activo
		<input name="opt_estado" type="radio" value="2"/>Inactivo
		<?php }else{?>
		<input name="opt_estado" type="radio" value="1"/>Activo
		<input name="opt_estado" type="radio" value="2" checked="checked"/>Inactivo
		<?php }?>
	  </td>
    </tr>
	<tr>
      <td class="normalNegrita">Observaciones:</td>
      <td><textarea name="txt_observa" class="normal" cols="40" rows="3" onkeypress="clargo('txt_observa','txt_num',document.form1,250)"><?php echo $part_observa;?></textarea></td>
    </tr>
     <tr>
      <td colspan="2" align="center">
	  <input class="normalNegro" type="button" value="Modificar" onclick="javascript:revisar();"/>		  
	  </td>
    </tr>
</table>
</form>
</body>
<?php  pg_close($conexion);?>
</html>