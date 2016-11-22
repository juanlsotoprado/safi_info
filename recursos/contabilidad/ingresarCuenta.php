<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
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
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI:Ingresar Cuenta contable</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><style type="text/css">
</style><script language="JavaScript" src="../../includes/js/funciones.js"> </script>
<script>
var comprobar=1;
//Función que permite verficar que el código no se encuentre repetida
function verificar_cod(codigo) { 
   codigo=codigo;
   resu=false;
   <?php
   $sql_p="SELECT cpat_id FROM sai_cue_pat"; 
   $resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al consultar codigo de partida");
   while($row=pg_fetch_array($resultado_set_most_p)) {?>
    codigo1="<?php echo trim($row['cpat_id']); ?>"
	if (codigo==codigo1)  {
	  resu=true;
	}
    <?php } ?> 
  return resu;
}
//Funcion que permite validar que solo se escriban numeros y puntos en el campo
function validar_cuenta(objeto) {
	var checkOK = "0123456789";
	var checkOKp = ".";
	var checkStr = objeto.value;
	var allValid = true;
	var i;
	for (i = 0;  i < checkStr.length;  i++) {
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
				if ((j == checkOK.length) && (k!=1) ) {
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
	    document.form1.txt_nivel1_1.value="";
		document.form1.txt_nivel1_1.disabled=false;
	    document.form1.slc_nivel2_1.disabled=true;
	    document.form1.txt_nivel2_1.disabled=true;
	    document.form1.slc_nivel3_1.disabled=true;
	    document.form1.txt_nivel3_1.disabled=true;
	    document.form1.slc_nivel4_1.disabled=true;
	    document.form1.txt_nivel4_1.disabled=true;
	    document.form1.slc_nivel5_1.disabled=true;
	    document.form1.txt_nivel5_1.disabled=true;
	    document.form1.slc_nivel6_1.disabled=true;
	    document.form1.txt_nivel6_1.disabled=true;
	    document.form1.slc_nivel7_1.disabled=true;
	    document.form1.txt_nivel7_1.disabled=true;
		comprobar=1;
	    document.form1.txt_nivel.value=comprobar;		
	 }
	else if(valor=='2')	 { 
		document.form1.txt_nivel2_1.value="";
		document.form1.slc_nivel2_1.disabled=false;			    
		document.form1.txt_nivel2_1.disabled=false;
		document.form1.txt_nivel1_1.disabled=true;
		document.form1.slc_nivel3_1.disabled=true;
		document.form1.txt_nivel3_1.disabled=true;
		document.form1.slc_nivel4_1.disabled=true;
		document.form1.txt_nivel4_1.disabled=true;
		document.form1.slc_nivel5_1.disabled=true;
		document.form1.txt_nivel5_1.disabled=true;
		document.form1.slc_nivel6_1.disabled=true;
		document.form1.txt_nivel6_1.disabled=true;
		document.form1.slc_nivel7_1.disabled=true;
		document.form1.txt_nivel7_1.disabled=true;
		comprobar=2;
	    document.form1.txt_nivel.value=comprobar;			
	}
	else if(valor=='3') { 
   		document.form1.txt_nivel3_1.value="";
		document.form1.slc_nivel3_1.disabled=false;				    
		document.form1.txt_nivel3_1.disabled=false;
	    document.form1.slc_nivel2_1.disabled=true;
	    document.form1.txt_nivel2_1.disabled=true;
	    document.form1.txt_nivel1_1.disabled=true;
	    document.form1.slc_nivel4_1.disabled=true;
	    document.form1.txt_nivel4_1.disabled=true;
	    document.form1.slc_nivel5_1.disabled=true;
	    document.form1.txt_nivel5_1.disabled=true;
	    document.form1.slc_nivel6_1.disabled=true;
	    document.form1.txt_nivel6_1.disabled=true;
	    document.form1.slc_nivel7_1.disabled=true;
	    document.form1.txt_nivel7_1.disabled=true;
	    comprobar=3;
	    document.form1.txt_nivel.value=comprobar;				    
	}
	else if(valor=='4') { 
	    document.form1.txt_nivel4_1.value="";
	    document.form1.slc_nivel4_1.disabled=false;					    
		document.form1.txt_nivel4_1.disabled=false;
	    document.form1.slc_nivel2_1.disabled=true;
	    document.form1.txt_nivel2_1.disabled=true;
	    document.form1.slc_nivel3_1.disabled=true;
	    document.form1.txt_nivel3_1.disabled=true;
	    document.form1.txt_nivel1_1.disabled=true;
	    document.form1.slc_nivel5_1.disabled=true;
	    document.form1.txt_nivel5_1.disabled=true;
	    document.form1.slc_nivel6_1.disabled=true;
	    document.form1.txt_nivel6_1.disabled=true;
	    document.form1.slc_nivel7_1.disabled=true;
	    document.form1.txt_nivel7_1.disabled=true;
	    comprobar=4;
	    document.form1.txt_nivel.value=comprobar;					    
	}
	else if(valor=='5') { 
	    document.form1.txt_nivel5_1.value="";
	    document.form1.slc_nivel5_1.disabled=false;						    
		document.form1.txt_nivel5_1.disabled=false;
	    document.form1.slc_nivel2_1.disabled=true;
	    document.form1.txt_nivel2_1.disabled=true;
	    document.form1.slc_nivel3_1.disabled=true;
	    document.form1.txt_nivel3_1.disabled=true;
	    document.form1.slc_nivel4_1.disabled=true;
	    document.form1.txt_nivel4_1.disabled=true;
	    document.form1.txt_nivel1_1.disabled=true;
	    document.form1.slc_nivel6_1.disabled=true;
	    document.form1.txt_nivel6_1.disabled=true;
	    document.form1.slc_nivel7_1.disabled=true;
	    document.form1.txt_nivel7_1.disabled=true;
	    comprobar=5;
	    document.form1.txt_nivel.value=comprobar;						    
	 }
	 else if(valor=='6') { 
	    document.form1.txt_nivel6_1.value="";
	    document.form1.slc_nivel6_1.disabled=false;							    
		document.form1.txt_nivel6_1.disabled=false;
	    document.form1.slc_nivel2_1.disabled=true;
	    document.form1.txt_nivel2_1.disabled=true;
	    document.form1.slc_nivel3_1.disabled=true;
	    document.form1.txt_nivel3_1.disabled=true;
	    document.form1.slc_nivel4_1.disabled=true;
	    document.form1.txt_nivel4_1.disabled=true;
	    document.form1.slc_nivel5_1.disabled=true;
	    document.form1.txt_nivel5_1.disabled=true;
	    document.form1.txt_nivel1_1.disabled=true;
	    document.form1.slc_nivel7_1.disabled=true;
	    document.form1.txt_nivel7_1.disabled=true;
	    comprobar=6;
	    document.form1.txt_nivel.value=comprobar;							    
 	}
	else if(valor=='7') { 
	    document.form1.txt_nivel7_1.value="";
	    document.form1.slc_nivel7_1.disabled=false;	
		document.form1.txt_nivel7_1.disabled=false;								    							    
	    document.form1.slc_nivel2_1.disabled=true;
	    document.form1.txt_nivel2_1.disabled=true;
	    document.form1.slc_nivel3_1.disabled=true;
	    document.form1.txt_nivel3_1.disabled=true;
	    document.form1.slc_nivel4_1.disabled=true;
	    document.form1.txt_nivel4_1.disabled=true;
	    document.form1.slc_nivel5_1.disabled=true;
	    document.form1.txt_nivel5_1.disabled=true;
	    document.form1.slc_nivel6_1.disabled=true;
	    document.form1.txt_nivel6_1.disabled=true;
	    document.form1.txt_nivel1_1.disabled=true;
	    comprobar=7;
	    document.form1.txt_nivel.value=comprobar;								    
	 }	 	 	 	 
}
/**************************************************/
function validar_cod(objeto) {
	var checkOK = "00123456789";
	var checkStr = objeto.value;
	var allValid = true;
	for (i = 0;  i < checkStr.length;  i++)	{
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

//Funcion que valida el llenado de todos los campos 
function revisar() {
	var codigo;
	var resu;
	
	if (comprobar==1) {

		cadena=document.form1.txt_nivel1_1.value;
		if(document.form1.txt_nivel1_1.value=="") {
		   alert('Complete los d\u00edgitos del primer nivel');
		   return;
		}
		else if(cadena.length<1) {
		   alert('Longitud de la cuenta no es v\u00e1lida');
		   return;
		}
		else {		
		     codigo=document.form1.txt_nivel1_1.value+document.form1.txt_nivel1_2.value;
		   }	 
	}
	else if (comprobar==2) {
		cadena=document.form1.txt_nivel2_1.value;
		if(document.form1.txt_nivel2_1.value=="") {
		   alert('Complete los d\u00edgitos del segundo nivel');
		   return;
		}
		else if(cadena.length<2) {
		   alert('Longitud de la cuenta no es v\u00e1lida');
		   return;
		}
		else {
			  codigo=document.form1.slc_nivel2_1.value+document.form1.txt_nivel2_1.value+document.form1.txt_nivel2_2.value;
		} 
	}
	else if (comprobar==3) {
		cadena=document.form1.txt_nivel3_1.value;
		if(document.form1.txt_nivel3_1.value=="") {
			alert('Complemente los d\u00edgitos del tercer nivel');
			return;
		}
		else if(cadena.length<1) {
			alert('Longitud de la cuenta no es v\u00e1lida');
			return;
		}
		else {
			codigo=document.form1.slc_nivel3_1.value+document.form1.txt_nivel3_1.value;
		}  
	}
	resu=verificar_cod(codigo);
	if(resu==2) {
	  alert("Esa cuenta contable ya se encuentra registrada en el sistema");
	  return;
	}  
	else if (document.form1.txt_nombre.value=="") {
		alert("Debe colocar el nombre de la cuenta patrimonial")
		document.form1.txt_nombre.focus();
		return;
	}
    else if (document.form1.txt_grupo.value=="") {
		alert("Debe colocar el nombre del grupo")
		document.form1.txt_grupo.focus();
		return;
	}
    else if (document.form1.txt_subgrupo.value=="") {
		alert("Debe colocar el nombre del subgrupo")
		document.form1.txt_subgrupo.focus();
		return;
	}
    else if (document.form1.txt_rubro.value=="") {
		alert("Debe colocar el nombre del rubro")
		document.form1.txt_rubro.focus();
		return;
	}
	if(confirm("Estos datos ser\u00E1n registrados. Est\u00E1 seguro que desea continuar?")) {
		  document.form1.submit()
	}
}		
</script>
</head>
<body>
<form action="ingresarCuentaAccion.php" method="post" enctype="multipart/form-data" name="form1">
<table width="75%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray"> 
		<td colspan="3" class="normalNegroNegrita">INGRESAR CUENTA PATRIMONIAL</td>
	</tr>
   <tr>
    <td width="4%"><input name="opt_codigo" type="radio" value="" onClick="javascript:deshabilitar_combo(1)" checked /></td>
    <td width="23%" class="normalNegrita"> Nivel 1</td>
    <td width="73%" class="normal">
	<input name="txt_nivel1_1" type="text" value="" size="1" maxlength="1" class="normal" onkeyup="validar_cuenta(this)" />	
	<input name="txt_nivel1_2" type="text" size="12" class="normal" value=".0.0.00.00.00.00" readonly="true" />
	</td>
  </tr> 
  <tr>
    <td><input name="opt_codigo" type="radio" value="" onClick="javascript:deshabilitar_combo(2)"/></td>
    <td class="normalNegrita">Nivel 2</td>
    <td class="normal">
	<?php
	$sql_str="SELECT cpat_id from  sai_cue_pat where cpat_id like '%.0.0.00.00.00.00' order by cpat_id";
	$res_q=pg_exec($sql_str);		  
	?>
     <select name="slc_nivel2_1" class="select1" id="slc_nivel2_1" disabled="true">
     <?php while($depe_row=pg_fetch_array($res_q)) { ?>
     	<option value="<?php echo(substr(trim($depe_row['cpat_id']),0,2)); ?>"><?php echo(substr(trim($depe_row['cpat_id']),0,2)); ?></option>
     <?php } ?>
    </select>
	<input name="txt_nivel2_1" type="text" value="" size="1" class="normal" maxlength="2" disabled="true" onkeyup="validar_cod(this)"/>
	<input name="txt_nivel2_2" type="text" value=".0.00.00.00.00" size="12" class="normal" readonly="true"/>
	</td>
  </tr>  
   <tr>
    <td><input name="opt_codigo" type="radio" value="" onClick="javascript:deshabilitar_combo(3)"/></td>
    <td class="normalNegrita">Nivel 3</td>
    <td class="normal">
	<?php
	$sql_str="SELECT cpat_id from  sai_cue_pat where cpat_id like '%.0.00.00.00.00' and cpat_id not like '%.0.0.00.00.00.00' order by cpat_id";
	$res_q=pg_exec($sql_str);		  
	?>
     <select name="slc_nivel3_1" class="select1" id="slc_nivel3_1" disabled="true">
     <?php while($depe_row=pg_fetch_array($res_q)) { ?>
     	<option value="<?php echo(substr(trim($depe_row['cpat_id']),0,4)); ?>"><?php echo(substr(trim($depe_row['cpat_id']),0,4)); ?></option>
     <?php } ?>
    </select>
	<input name="txt_nivel3_1" type="text" value="" size="1" class="normal" maxlength="2" disabled="true" onkeyup="validar_cod(this)"/>
	<input name="txt_nivel3_2" type="text" value=".00.00.00.00" size="10" class="normal" readonly="true"/>
	</td>
  </tr>
   <tr>
    <td><input name="opt_codigo" type="radio" value="" onClick="javascript:deshabilitar_combo(4)"/></td>
    <td class="normalNegrita">Nivel 4</td>
    <td class="normal">
	<?php
		$sql_str="SELECT cpat_id from  sai_cue_pat where cpat_id like '%.00.00.00.00' and cpat_id not like '%0.00.00.00.00' order by cpat_id";
		$res_q=pg_exec($sql_str);		  
	?>
     <select name="slc_nivel4_1" class="select1" id="slc_nivel4_1" disabled="true">
     <?php while($depe_row=pg_fetch_array($res_q)) { ?>
     	<option value="<?php echo(substr(trim($depe_row['cpat_id']),0,6)); ?>"><?php echo(substr(trim($depe_row['cpat_id']),0,6)); ?></option>
     <?php } ?>
    </select>
	<input name="txt_nivel4_1" type="text" value="" size="2" class="normal" maxlength="2" disabled="true" onkeyup="validar_cod(this)"/>
	<input name="txt_nivel4_2" type="text" value=".00.00.00" size="9" class="normal" readonly="true"/>
	</td>
  </tr>
   <tr>
    <td><input name="opt_codigo" type="radio" value="" onClick="javascript:deshabilitar_combo(5)"/></td>
    <td class="normalNegrita">Nivel 5</td>
    <td class="normal">
	<?php
		$sql_str="SELECT cpat_id from  sai_cue_pat where cpat_id like '%.00.00.00' and cpat_id not like '%.00.00.00.00' order by cpat_id";
		$res_q=pg_exec($sql_str);		  
	?>
     <select name="slc_nivel5_1" class="select1" id="slc_nivel5_1" disabled="true">
     <?php while($depe_row=pg_fetch_array($res_q)) { ?>
     	<option value="<?php echo(substr(trim($depe_row['cpat_id']),0,9)); ?>"><?php echo(substr(trim($depe_row['cpat_id']),0,9)); ?></option>
     <?php } ?>
    </select>
	<input name="txt_nivel5_1" type="text" value="" size="2" class="normal" maxlength="2" disabled="true" onkeyup="validar_cod(this)"/>
	<input name="txt_nivel5_2" type="text" value=".00.00" size="6" class="normal" readonly="true"/>
	</td>
  </tr>  
   <tr>
    <td><input name="opt_codigo" type="radio" value="" onClick="javascript:deshabilitar_combo(6)"/></td>
    <td class="normalNegrita">Nivel 6</td>
    <td class="normal">
	<?php
		$sql_str="SELECT cpat_id from  sai_cue_pat where cpat_id like '%.00.00' and cpat_id not like '%.00.00.00' order by cpat_id";
		$res_q=pg_exec($sql_str);		  
	?>
     <select name="slc_nivel6_1" class="select1" id="slc_nivel6_1" disabled="true">
     <?php while($depe_row=pg_fetch_array($res_q)) { ?>
     	<option value="<?php echo(substr(trim($depe_row['cpat_id']),0,12)); ?>"><?php echo(substr(trim($depe_row['cpat_id']),0,12)); ?></option>
     <?php } ?>
    </select>
	<input name="txt_nivel6_1" type="text" value="" size="2" class="normal" maxlength="2" disabled="true" onkeyup="validar_cod(this)"/>
	<input name="txt_nivel6_2" type="text" value=".00" size="4" class="normal" readonly="true"/>
	</td>
  </tr>  
   <tr>
    <td><input name="opt_codigo" type="radio" value="" onClick="javascript:deshabilitar_combo(7)"/></td>
    <td class="normalNegrita">Nivel 7</td>
    <td class="normal">
	<?php
		$sql_str="SELECT cpat_id from  sai_cue_pat where cpat_id like '%.00' and cpat_id not like '%.00.00' order by cpat_id";
		$res_q=pg_exec($sql_str);		  
	?>
     <select name="slc_nivel7_1" class="select1" id="slc_nivel7_1" disabled="true">
     <?php while($depe_row=pg_fetch_array($res_q)) { ?>
     	<option value="<?php echo(substr(trim($depe_row['cpat_id']),0,15)); ?>"><?php echo(substr(trim($depe_row['cpat_id']),0,15)); ?></option>
     <?php } ?>
    </select>
	<input name="txt_nivel7_1" type="text" value="" size="2" class="normal" maxlength="2" disabled="true" onkeyup="validar_cod(this)"/>
	<input name="txt_nivel" type="hidden" value="1"/>	
	</td>
  </tr>  
    <tr>
    <td>&nbsp;</td>
      <td class="normalNegrita">Nombre de la Cuenta:</td>
      <td><textarea name="txt_nombre" class="normal"  cols="50" rows="3"></textarea></td>
    </tr>
   <tr class="normal">
       <td>&nbsp;</td>
      <td class="normalNegrita">Saldo inicial Bs.:</td>
      <td><input type="text" size="13" name="txt_saldo" class="normal" value="0,00" >(###,##)</input></td>
    </tr>    
   <tr class="normal">
       <td>&nbsp;</td>
      <td class="normalNegrita">Grupo:</td>
      <td><input type="text" name="txt_grupo" class="normal"></input>Ej: Activo, Pasivos, Ingresos, Cuentas de Orden</td>
    </tr>  
   <tr class="normal">
       <td>&nbsp;</td>
      <td class="normalNegrita">SubGrupo:</td>
      <td><input type="text" name="txt_subgrupo" class="normal">Ej: A circulante, A no circulante, Transferencias, P circulante, G consumo</input></td>
    </tr>      
   <tr class="normal">
       <td>&nbsp;</td>
      <td class="normalNegrita">Rubro:</td>
      <td><input type="text" name="txt_rubro" class="normal">Ej: A. Disponible, A. Exigible, A. Realizable, Gastos de Personal, etc</input></td>
    </tr>      
     <tr>
      <td colspan="3" align="center"><br />
		<input class="normalNegro" type="button" value="Ingresar" onclick="javascript:revisar();"/>		  
	  </td>
    </tr>
</table>
</form>
</body>
<?php  pg_close($conexion);?>
</html>