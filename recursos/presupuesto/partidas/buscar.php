<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 
$an=$_SESSION['an_o_presupuesto'];
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar Partida</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../../js/funciones.js"> </script>
<script>
function deshabilitar_combo(valor) {
	if(valor=='1') { 
		document.form.txt_cod_partida.disabled=false;
		document.form.txt_nom_partida.disabled=true;
	}
 	else if(valor=='2') { 
		document.form.txt_cod_partida.disabled=true;
		document.form.txt_nom_partida.disabled=false;
	}
 	else if(valor=='3') { 
		document.form.txt_cod_partida.disabled=true;
		document.form.txt_nom_partida.disabled=true;
	}
 	else if(valor=='4') { 
		document.form.txt_cod_partida.disabled=true;
		document.form.txt_nom_partida.disabled=true;
	}
}
//Función que permite validar que sólo se escriban números y puntos en el campo
function validar_cod(objeto) {
	var checkOK = "0123456789. ";
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
			alert("Escriba solo d\u00edgitos caracteres y puntos...");
			break;
		}
	}
} 

//Funcion que permite abrir una ventana
function detalle(codigo, ano) {
	url="detalle.php?codigo="+codigo+"&ano="+ano;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) newwindow.focus();
}

function ejecutar() {
	valor="0"
		for(i=0;i<document.form.opt_partida.length;i++)
			if(document.form.opt_partida[i].checked) valor=document.form.opt_partida[i].value;
		document.form.validar.value=valor;	
		document.form.submit();
}
</script>
</head>
<body> 
<form name="form" method="post" action="buscar.php">
<table width="60%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
<td colspan="3" class="normalNegroNegrita">B&Uacute;SQUEDA DE PARTIDAS</font>
  </td>
</tr>
<tr>
  <td class="normal"><input name="opt_partida" type="radio" class="normal" value="1" onClick="javascript:deshabilitar_combo(1)" /></td>
  <td class="normalNegrita">C&oacute;digo:</td>
  <td class="normal"><input type="text" name="txt_cod_partida" value="" class="normal" size="20" onkeyup="return validar_cod(txt_cod_partida)" disabled /></td>
</tr>
<tr>
  <td class="normal"><input name="opt_partida" type="radio" class="normal" value="2" onClick="javascript:deshabilitar_combo(2)" /></td>
  <td class="normalNegrita">Nombre:</td>
  <td class="normal"><input type="text" name="txt_nom_partida" class="normal" value="" size="70" onkeyup="return validar(txt_nom_partida,1)" disabled /> </td>
</tr>
<tr>
  <td class="normal"><input name="opt_partida" type="radio" class="normal" value="3" onClick="javascript:deshabilitar_combo(3)" /></td>
  <td class="normalNegrita">Subpartidas de:</td>
  <td class="normal">
  <select name="cmb_subpartidas" class="normal">
  <option value="">[Seleccione]</option>
  <?php
  $sql="SELECT * FROM sai_buscar_partida('', 0, ".$_SESSION['an_o_presupuesto'].", '', 1) as resultado_set(codigo varchar, nombre varchar, estado int4)";
  $resultado=pg_query($conexion,$sql) or die("Error al consultar partida");
  while($row=pg_fetch_array($resultado)) { ?>
   	  <option value="<?php echo $row['codigo'];?>"><?php echo $row['codigo'];?></option> <?php 
  } ?>
  </select>
  </td>
</tr>
<tr>
  <td class="normal"><input name="opt_partida" type="radio" class="normal" value="4" onClick="javascript:deshabilitar_combo(4)" /></td>
  <td class="normalNegrita">Estado del Recurso:</td>
  <td class="normal">
  <select name="cmb_estado" id="cmb_estado" class="normal">
      <option value="0">[Seleccione]</option>
      <option value="1">Activo</option>
	  <option value="15">Inactivo</option>
   </select>
</tr>
<tr>
  <td class="normal">&nbsp;</td>
  <td class="normalNegrita"><div align="left">A&ntilde;o de Presupuesto:</div></td>
  <td class="normal">
  <input type="text" name="txt_pres_anno" value="<?php echo $_SESSION['an_o_presupuesto'];?>" maxlength="4" class="normal" size="6" onkeypress="return acceptNum(event)"/> 
  </td>
</tr>
<tr>
  <td height="47" colspan="3" align="center">
  <input class="normalNegro" type="button" value="Buscar" onclick="javascript:ejecutar();"/>
	<input type="hidden" id="validar" name="validar" value="0"> </input>    
    </td>
</tr>
</table>
</form>
<br></br>
<?php 
$condicion=" and p.pres_anno = '".$_SESSION['an_o_presupuesto']."'";
	if (isset($_POST['txt_cod_partida']) && strlen(trim($_POST['txt_cod_partida']))>2) $condicion=" and p.pres_anno = '".$_POST['txt_pres_anno']."' and trim(p.part_id)='".$_POST['txt_cod_partida']."'";
	else if (isset($_POST['txt_nom_partida']) && trim($_POST['txt_nom_partida'])!='0') $condicion=" and p.pres_anno = '".$_POST['txt_pres_anno']."' and upper(trim(p.part_nombre)) like upper(trim('%".$_POST['txt_nom_partida']."%'))";
	else if (isset($_POST['cmb_subpartidas']) && trim($_POST['cmb_subpartidas'])!='0') $condicion=" and p.pres_anno = '".$_POST['txt_pres_anno']."' and trim(p.part_id) like trim('".$_POST['cmb_subpartidas']."%')";	
	else if (isset($_POST['cmb_estado']) && trim($_POST['cmb_estado'])!='0') $condicion=" and p.pres_anno = '".$_POST['txt_pres_anno']."' and p.esta_id = '".$_POST['cmb_estado']."%'";
	else if (isset($_POST['txt_pres_anno']) && trim($_POST['txt_pres_anno'])!='0') $condicion=" and trim(p.pres_anno) = trim('".$_POST['txt_pres_anno']."%')";
	   

	$sql="SELECT p.part_id, p.part_nombre, p.pres_anno, e.esta_nombre FROM sai_partida p, sai_estado e where p.esta_id=e.esta_id".$condicion." order by part_id";
	$resultado=pg_query($conexion,$sql) or die("Error al consultar las entidades bancarias");
?>
		  <table width="80%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
			  <tr class="td_gray">
			  <td width="10%" class="normalNegroNegrita"><div align="center">C&oacute;digo</div></td>
			  <td width="50%" class="normalNegroNegrita"><div align="center">Nombre </div></td>
			  <td width="5%" class="normalNegroNegrita"><div align="center">A&ntilde;o </div></td>
			  <td width="5%" class="normalNegroNegrita"><div align="center">Estado</div></td>
			  <td width="10%" class="normalNegroNegrita"><div align="center">Opciones</div></td>
			  </tr>
		 <?php 	while($row=pg_fetch_array($resultado)) {  ?>
	
			  <tr class="normal">
			    <td width="10%"><?=$row['part_id']?></td> 
			    <td width="50%"><?=$row['part_nombre']?></td>
			    <td width="5%"><?=$row['pres_anno']?></td>
 				<td width="5%"><?=$row['esta_nombre']?></td>
 				<td width="10%">			    
				<img src="../../../imagenes/vineta_azul.gif" width="11" height="7"><a href="javascript:detalle('<?=$row['part_id']?>','<?=$row['pres_anno']?>' )" class="normal" > Ver Detalle</a><br>
				<img src="../../../imagenes/vineta_azul.gif" width="11" height="7"><a href="modificar.php?codigo=<?=$row['part_id']?>&ano=<?=$row['pres_anno']?>" class="normal"> Modificar</a>
				</td>
			  </tr>
<?php  } 
pg_close($conexion);
?>
</body>
</html>