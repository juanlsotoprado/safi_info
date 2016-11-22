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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar Otro Trabajador</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script>
function deshabilitar_combo(valor) {
	if(valor=='1')  { 
		document.form.identificacion.disabled=false;
		document.form.txt_nombre.disabled=true;
		document.form.txt_apellido.disabled=true;
		document.form.slc_depen.disabled=true;
		document.form.slc_depen.value=0;
		document.form.slc_estado.disabled=true;
		document.form.slc_estado.value=0;
		document.form.slc_tipo.disabled=true;
 	}
	else if(valor=='2') { 
		document.form.identificacion.disabled=true;
		document.form.txt_nombre.disabled=false;
		document.form.txt_apellido.disabled=true;
		document.form.slc_depen.disabled=true;
		document.form.slc_depen.value=0;
		document.form.slc_estado.disabled=true;
		document.form.slc_estado.value=0;
		document.form.slc_tipo.disabled=true;
	}
	else if(valor=='3') { 
		document.form.identificacion.disabled=true;
		document.form.txt_nombre.disabled=true;
		document.form.txt_apellido.disabled=false;
		document.form.slc_depen.disabled=true;
		document.form.slc_depen.value=0;
		document.form.slc_estado.disabled=true;
		document.form.slc_estado.value=0;
		document.form.slc_tipo.disabled=true;
	}
	else if(valor=='4') { 
		document.form.identificacion.disabled=true;
		document.form.txt_nombre.disabled=true;
		document.form.txt_apellido.disabled=true;
		document.form.slc_depen.disabled=false;
		document.form.slc_estado.disabled=true;
		document.form.slc_depen.value=0;
		document.form.slc_tipo.disabled=true;
	}
	else if(valor=='5'){
		document.form.slc_estado.disabled=true;
		document.form.identificacion.disabled=true;
		document.form.txt_nombre.disabled=true;
		document.form.txt_apellido.disabled=true;
		document.form.slc_depen.disabled=true;
		document.form.slc_depen.value=0;
		document.form.slc_tipo.disabled=false;
	}
	else if(valor=='6'){
		document.form.slc_estado.disabled=false;
		document.form.identificacion.disabled=true;
		document.form.txt_nombre.disabled=true;
		document.form.txt_apellido.disabled=true;
		document.form.slc_depen.disabled=true;
		document.form.slc_depen.value=0;
		document.form.slc_tipo.disabled=true;		
	}	
}
function ejecutar() {
	if ((document.form.txt_nombre!='') || (document.form.txt_apellido!='') || (document.form.identificacion!='') || (document.form.slc_estado!='0') || (document.form.slc_tipo!='') || (document.form.slc_depen!='') || (document.form.slc_tipo!='0')) {
		document.form.validar.value=1;
		document.form.submit();
	}
	else alert ("Debe seleccionar una opci\u00f3n de b\u00fasqueda");
}

function detalle(codigo) {
    url="detalleTrabajador.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}
</script>
<script language="JavaScript" src="../../js/funciones.js"> </script>
</head>
<body>
<form name="form" action="buscarTrabajador.php" method="post">
<input type="hidden" name="validar" value="0" >
<br />
<br />
<table width="80%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
  <td colspan="3" class="normalNegroNegrita">
  B&Uacute;SQUEDA DE OTROS TRABAJADORES</td>
</tr>
<tr>
  <td class="normal"><input name="opcion" type="radio" value="1" onClick="javascript:deshabilitar_combo(1)" /> </td>
  <td class="normalNegrita">Documento de Identidad:</td>
  <td class="normal"><input type="text" name="identificacion" value="" disabled="true" class="normal">  </td>
</tr>
<tr class="normal">
  <td><input name="opcion" type="radio" value="2" onClick="javascript:deshabilitar_combo(2)" /></td>
  <td class="normalNegrita">Nombre(s):</td>
  <td><input type="text" name="txt_nombre" value="" disabled="true" class="normal"></td>
</tr>
<tr class="normal">
  <td><input name="opcion" type="radio" value="3" onClick="javascript:deshabilitar_combo(3)" /></td>
  <td class="normalNegrita">Apellido(s):</td>
  <td><input type="text" name="txt_apellido" value="" disabled="true" class="normal"></td>
</tr>
<tr class="normal">
  <td><input name="opcion" type="radio" value="4" onclick="javascript:deshabilitar_combo(4)" /></td>
  <td class="normalNegrita">Dependencia:</td>
  <td>
    <select name="slc_depen" id="slc_depen" class="normal">
      <option value="0">[Seleccione]</option>
      <?php
	$sql="SELECT depe_id, depe_nombre from sai_dependenci where esta_id=1 and (depe_nivel=4 or depe_nivel=3)"; 
	$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
	while($row=pg_fetch_array($resultado)){ ?>
      <option value="<?=trim($row['depe_id'])?>"><?php echo $row['depe_nombre'];?></option>
      <?php } ?>
    </select>
   </td>
  </tr>
<tr class="normal"> 
  <td><input name="opcion" type="radio" value="5" onclick="javascript:deshabilitar_combo(5)" /></td>
<td class="normalNegrita">Tipo:</td>
<td> 
<select name="slc_tipo" id="slc_tipo" disabled class="normal">
<option value="0">Seleccione</option>
<option value="Alfabetizador">Alfabetizador</option>
<option value="Beca Tecnol&oacute;gica">Beca Tecnol&oacute;gica</option>
<option value="Bolsa Trabajo">Bolsa Trabajo</option>
<option value="Enlace Estatal">Enlace Estatal</option>
<option value="Ex Trabajador">Ex Trabajador</option>
<option value="HP">HP</option>
<option value="Invitado">Invitado</option>
<option value="Junta Directiva">Junta Directiva</option>
<option value="Pasante">Pasante</option>
<option value="Promotor">Promotor</option>
</select>
</td>
</tr>   
<tr class="normal">
  <td><input name="opcion" type="radio" value="6" onclick="javascript:deshabilitar_combo(6)" /></td>
  <td class="normalNegrita">Estado:</td>
  <td>
   <select name="slc_estado" id="slc_estado" class="normal" disabled>
      <option value="0">[Seleccione]</option>
      <option value="1">Activo</option>
	  <option value="2">Inactivo</option>
   </select>  </td>
</tr>
<tr>
  <td colspan="3" > &nbsp;</td>
</tr>
<tr>
  <td colspan="3" align="center">
   <input class="normalNegro" type="button" value="Buscar" onclick="javascript:ejecutar();"/>
</table>
</form>
<br/>
<?php 
$condicion = "and trim(benvi_esta_id)=1";
if ($_POST['validar']==1) { 
 	if ($_POST['identificacion']!='') $condicion=" and benvi_cedula='".trim($_POST['identificacion'])."'"; 
	else if ($_POST['txt_nombre']!='') $condicion=" and upper(benvi_nombres) like '%".trim(strtoupper($_POST['txt_nombre']))."%'";
	else if ($_POST['txt_apellido']!='') $condicion=" and upper(benvi_apellidos) like '%".trim(strtoupper($_POST['txt_apellido']))."%'"; 
	else if ($_POST['slc_depen']!='') $condicion=" and v.depe_id='".trim(strtolower($_POST['slc_depen']))."'";
	else if ($_POST['slc_tipo']!='0' && $_POST['slc_tipo']!='') $condicion=" and trim(tipo)='".trim($_POST['slc_tipo'])."'";  
	else if ($_POST['slc_estado']!='0' && $_POST['slc_estado']!='') $condicion=" and trim(benvi_esta_id)=".trim($_POST['slc_estado']);
}

    $nroFilas=0;
    $sql= "SELECT v.nacionalidad, v.benvi_cedula, upper(v.benvi_nombres) as benvi_nombres, upper(v.benvi_apellidos) as benvi_apellidos, v.benvi_esta_id, v.tipo, est.esta_nombre, upper(d.depe_nombre) as depe_nombre from sai_viat_benef v, sai_estado est, sai_dependenci d where v.benvi_esta_id=est.esta_id and v.depe_id=d.depe_id ".$condicion . "order by v.depe_id, v.benvi_nombres, v.benvi_apellidos";
	$resultado=pg_query($conexion,$sql) or die("Error al consultar otro trabajador");    
?>
<table width="80%" border="0" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray" align="center">
		<td class="normalNegroNegrita" width="10%">Documento de Identidad</td>
		<td class="normalNegroNegrita" width="17%">Nombre(s)</td>
		<td class="normalNegroNegrita" width="17%">Apellido(s)</td>
		<td class="normalNegroNegrita" width="30%">Dependencia</td>
		<td class="normalNegroNegrita" width="11%">Tipo</td>
		<td class="normalNegroNegrita" width="5%">Estado</td>
		<td class="normalNegroNegrita" width="10%">Opciones</td>
	 </tr>
<?php
	while($row=pg_fetch_array($resultado)) {
		 $ci=trim($row['benvi_cedula']);
		 $nacionalidad=trim($row['nacionalidad']);
		 $nombre=trim($row['benvi_nombres']);
		 $apellido=trim($row['benvi_apellidos']);
		 $esta_id=trim($row['benvi_esta_id']); 
		 $esta_nombre=trim($row['esta_nombre']);					 
		 $dependencia=trim($row['depe_nombre']);
		 $tipo=trim($row['tipo']);
	?>
	  <tr class="normal">
		<td><?php echo $nacionalidad."-".$ci;?></td>
		<td><?php echo $nombre;?></td>
		<td><?php echo $apellido;?></td>
		<td><?php echo $dependencia;?></td>
		<td><?php echo $tipo;?></td>
		<td><?php echo $esta_nombre;?></td>
		<td class="normal">
		<a href="javascript:detalle('<?=$ci?>')" class="normal"> Ver Detalle</a><br>
	    <a href="modificarTrabajador.php?codigo=<?=$ci?>" class="normal"> Modificar</a>					    </td>
	  </tr>
   <?php }?>
  </table> 
	<?php  pg_close($conexion); ?>
</body>
</html>