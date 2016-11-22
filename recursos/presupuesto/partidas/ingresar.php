<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
require("../../../includes/perfiles/constantesPerfiles.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../index.php',false);
	ob_end_flush(); 
	exit;
}ob_end_flush(); 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI:Ingresar Partida</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="../../../js/funciones.js"> </script>
<script>
var comprobar=1;
//Función que permite verficar que la descripción no se encuentre repetida
function verificar_cod(codigo) { 
   codigo=codigo;
   resu=0;
   anio=document.form1.opt_an_o.value;
   <?php
   $sql_p="SELECT part_id,pres_anno FROM sai_partida"; 
   $resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al consultar codigo de partida");
   while($row=pg_fetch_array($resultado_set_most_p)) {
   ?>
    codigo1="<?php echo trim($row['part_id']); ?>"
	anio1="<?php echo trim($row['pres_anno']); ?>"
	if ((codigo==codigo1) && (anio==anio1))
	  resu=1;
    <?php  } ?> 
  return resu;
}
//Funcion que permite validar que sólo se escriban números y puntos en el campo
function validar_partida_1(objeto)
{
	var checkOK = "0123456789";
	var checkOKp = ".";
	var checkStr = objeto.value;
	var allValid = true;
	var i;
	for (i = 0;  i < checkStr.length;  i++) 	{
			if (i==1) {
				if (checkStr.charAt(i)!= checkOKp.charAt(0)) {
					var cambio=checkStr.substring(-1,i) 
					objeto.value=cambio;
					alert("Escriba el punto");
					return;
				}
			}
			if ((i==0) || (i==2) || (i==3)) {
					if (checkStr.charAt(i)== checkOKp.charAt(0)) {
						var cambio=checkStr.substring(-1,i) 
						objeto.value=cambio;
						break;
					}
					for (k = 0;  k < checkStr.length;  k++) {
						for (j = 0;  j < checkOK.length;  j++)
						if (checkStr.charAt(k) == checkOK.charAt(j))
						break;
						if ((j == checkOK.length) && (k!=1)) {
							var cambio=checkStr.substring(-1,i) 
							objeto.value=cambio;
							break;
						}
					}
			}
	}
} 
//Funcion que permite validar que sólo se escriban números y puntos en el campo
function validar_especifica_1(objeto) {
	var checkOK = "0123456789";
	var checkOKp = ".";
	var checkStr = objeto.value;
	var allValid = true;
	var i;
	for (i = 0;  i < checkStr.length;  i++) {
			if (i==2 || i==5) {
				if (checkStr.charAt(i)!= checkOKp.charAt(0)) {
					var cambio=checkStr.substring(-1,i) 
					objeto.value=cambio;
					alert("Escriba el punto");
					return;
				}
			}
			if (i!=2 && i!=5) {
					if (checkStr.charAt(i)== checkOKp.charAt(0)) {
						var cambio=checkStr.substring(-1,i) 
						objeto.value=cambio;
						break;
					}
					for (k = 0;  k < checkStr.length;  k++) {
						for (j = 0;  j < checkOK.length;  j++)
						if (checkStr.charAt(k) == checkOK.charAt(j))
						break;
						if ((j == checkOK.length) && (k!=2)  && (k!=5)) {
							var cambio=checkStr.substring(-1,i) 
							objeto.value=cambio;
							break;
						}
					}
			}
	}
} 
/******************************************************/
function deshabilitar_combo(valor) {
	 if(valor=='1') { 
	    document.form1.txt_subpart_1.value="";
	    document.form1.txt_subespec_1.value="";
		
		document.form1.codigo_part_1.disabled=false;
	    document.form1.slc_subpart_1.disabled=true;
	    document.form1.txt_subpart_1.disabled=true;
	    document.form1.slc_subespec_1.disabled=true;
	    document.form1.txt_subespec_1.disabled=true;
		comprobar=1;
	 }
	 else
		 if(valor=='2') { 
		    document.form1.codigo_part_1.value="";
	    	document.form1.txt_subespec_1.value="";
		
			document.form1.codigo_part_1.disabled=true;
		    document.form1.slc_subpart_1.disabled=false;
		    document.form1.txt_subpart_1.disabled=false;
		    document.form1.slc_subespec_1.disabled=true;
		    document.form1.txt_subespec_1.disabled=true;
			comprobar=2;
		 }
		 else
			 if(valor=='3') { 
			    document.form1.codigo_part_1.value="";
				document.form1.txt_subpart_1.value="";
				
				document.form1.codigo_part_1.disabled=true;
			    document.form1.slc_subpart_1.disabled=true;
			    document.form1.txt_subpart_1.disabled=true;
			    document.form1.slc_subespec_1.disabled=false;
			    document.form1.txt_subespec_1.disabled=false;
				comprobar=3;
			 }
}
/**************************************************/
function validar_cod(objeto)
{
	var checkOK = "00123456789";
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
			break;
		}
	}
} 

//Función que valida el llenado de todos los campos 
function revisar() {
	var codigo;
	var resu;
	if (comprobar==1) {
		cadena=document.form1.codigo_part_1.value;
		if(document.form1.codigo_part_1.value=="") {
		   alert('Complete los d\u00edgitos de la partida');
		   return;
		}
		else
		if(cadena.length<4) {
		   alert('La longitud de la partida no es v\u00e1lida');
		   return;
		}
		else 		
		     codigo=document.form1.codigo_part_1.value+document.form1.codigo_part_2.value;
	}
	else
		if (comprobar==2) {
			cadena=document.form1.txt_subpart_1.value;
			if(document.form1.txt_subpart_1.value=="") {
			   alert('Complete los d\u00edgitos de la sub-partida');
			   return;
			}
			else
			if(cadena.length<2) {
			   alert('La longitud de la partida no es v\u00e1lida');
			   return;
			}
			else
			  codigo=document.form1.slc_subpart_1.value+document.form1.txt_subpart_1.value+document.form1.txt_subpart_2.value;
		}
		else
			if (comprobar==3) {
				cadena=document.form1.txt_subespec_1.value;
				if(document.form1.txt_subespec_1.value=="")	{
				   alert('Complete los d\u00edgitos de la sub-espec\u00edfica');
				   return;
				}
				else
				if(cadena.length<5) {
				   alert('La longitud de la partida no es v\u00e1lida');
				   return;
				}
				else
				  codigo=document.form1.slc_subespec_1.value+document.form1.txt_subespec_1.value;
			}
	resu=verificar_cod(codigo);
	if(resu==1) {
	  alert("El c\u00f3digo de partida en este a\u00F1o ya se encuentra registrada en la base de datos");
	  return;
	}  
    if (document.form1.txt_nombre.value=="") {
		alert("Debe colocar el nombre de la partida.")
		document.form1.txt_nombre.focus();
		return;
	}
	 if (document.form1.opt_an_o.selectedIndex==0) {
		alert("Seleccione el a\u00F1o.")
		document.form1.opt_an_o.focus();
		return;
	}
	if(confirm("Estos datos ser\u00E1n registrados. Est\u00E1 seguro que desea continuar?")) {
		  document.form1.action="ingresarAccion.php?codigo="+codigo
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
<form action="ingresarAccion.php" method="post" enctype="multipart/form-data" name="form1">
 <table width="60%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
<tr class="td_gray"> 
	<td colspan="3" class="normalNegroNegrita">INGRESAR PARTIDA PRESUPUESTARIA</td>
</tr>
 <tr>
    <td><input name="opt_codigo" type="radio" value="" onClick="javascript:deshabilitar_combo(1)" checked /></td>
    <td class="normalNegrita"> Partida</td>
    <td class="normal">
    <?php
    $condicion=""; 
    if ($_SESSION['user_perfil_id'] == PERFIL_JEFE_ORDENACION_CONTABILIDAD) $condicion = "readonly='true'";?>
	<input name="codigo_part_1" type="text" value="" size="12" maxlength="4" class="normal" <?php echo $condicion;?> onkeyup="validar_partida_1(this)" />	
	<input name="codigo_part_2" type="text" size="9" class="normal" value=".00.00.00" readonly="true" />
	</td>
</tr>
  <tr>
    <td><input name="opt_codigo" type="radio" value="" onClick="javascript:deshabilitar_combo(2)"/></td>
    <td class="normalNegrita">Sub-partida</td>
    <td class="normal">
	<?php
	$condicion= "";
	if ($_SESSION['user_perfil_id'] == PERFIL_JEFE_ORDENACION_CONTABILIDAD) $condicion = " and part_id like '4.11.0%'";
		$sql="SELECT part_id, part_nombre, pres_anno from  sai_partida where part_id like '%00.00.00' and pres_anno=".$_SESSION['an_o_presupuesto'].$condicion." order by part_id";			
		$resultado=pg_exec($sql);		  
	?>
     <select name="slc_subpart_1" class="select1" id="slc_subpart_1" disabled="true">
     <?php while($row=pg_fetch_array($resultado)) { ?>
     	<option value="<?php echo(substr(trim($row['part_id']),0,5)); ?>"><?php echo(substr(trim($row['part_id']),0,5)); ?></option>
     <?php }?>
    </select>
	<input name="txt_subpart_1" type="text" value="" size="2" class="normal" maxlength="2" disabled="true" onkeyup="validar_cod(this)"/>
	<input name="txt_subpart_2" type="text" value=".00.00" size="6" class="normal" readonly="true"/>
	</td>
  </tr>
  <tr>
    <td><input name="opt_codigo" type="radio" value="" onClick="javascript:deshabilitar_combo(3)" /></td>
    <td class="normalNegrita">Sub-especifica</td>
    <td>
	<?php
	$condicion= "";
	if ($_SESSION['user_perfil_id'] == PERFIL_JEFE_ORDENACION_CONTABILIDAD) $condicion = " and part_id like '4.11.0%'";
		$sql="SELECT part_id, part_nombre, pres_anno from  sai_partida where part_id like '%00.00' and part_id not like '%00.00.00'and pres_anno=".$_SESSION['an_o_presupuesto'].$condicion." order by part_id";
		$resultado=pg_exec($sql);		  
	?>
     <select name="slc_subespec_1" class="select1" id="slc_subespec_1" disabled="true">
     <?php while($row=pg_fetch_array($resultado)) { ?>
     	<option value="<?php echo(substr(trim($row['part_id']),0,8)); ?>"><?php echo(substr(trim($row['part_id']),0,8)); ?></option>
     <?php } ?>
    </select>
	<input name="txt_subespec_1" type="text" value="" maxlength="8" size="8" class="normal" disabled="true" onkeyup="validar_especifica_1(this)" />
	</td>
  </tr>
   <tr>
     <td>&nbsp;</td>
      <td class="normalNegrita">Nombre de la Partida:</td>
      <td> <textarea name="txt_nombre" class="normal"  cols="50" rows="3" onkeypress="clargo('txt_nombre','txt_num',document.form1,250)"></textarea></td>
    </tr>
	<tr>
	<td>&nbsp;</td>
      <td class="normalNegrita">Observaci&oacute;n:</td>
      <td> <textarea name="txt_observa" class="normal" cols="50" rows="3" onkeypress="clargo('txt_observa','txt_num',document.form1,250)"></textarea></td>
    </tr>
    <tr> 
    	<td>&nbsp;</td>
      <td class="normalNegrita">A&ntilde;o de Presupuesto:</td>
      <td class="normal">
	    <select name="opt_an_o" class="ptotal" id="opt_an_o" >
		  <?php 
			   echo("<option value=''>Seleccione</option>");
				if($_SESSION['POA']!=0){
				   echo("<option value='".$_SESSION['POA']."'>".$_SESSION['POA']."</option>");
				}
				$valor=$_SESSION['an_o_presupuesto']+1;
			   echo("<option value='".$_SESSION['an_o_presupuesto']."'>".$_SESSION['an_o_presupuesto']."</option>");
			   echo("<option value='".$valor."' selected='true'>".$valor."</option>");
			?>
	    </select>
	  </td>
    </tr>
	<tr>
      <td colspan="3" class="normal">
	    <input name="txt_num" type="hidden" id="txt_num" size="10" maxlength="8" class="normal" disabled/>
	  </td>
 </tr>
 <tr>
      <td colspan="3" align="center">
	  <input class="normalNegro" type="button" value="Ingresar" onclick="javascript:revisar();"/>		  
	  </td>
    </tr>
</table>
</form>
</body>
<?php  pg_close($conexion);?>
</html>