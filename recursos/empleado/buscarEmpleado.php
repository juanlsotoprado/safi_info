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
<title>.:SAFI:Buscar Empleado</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script>
function deshabilitar_combo(valor) {
	if(valor=='1')  { 
		document.form.txt_ci.disabled=false;
		document.form.txt_nombre.disabled=true;
		document.form.txt_apellido.disabled=true;
		document.form.slc_depen.disabled=true;
		document.form.slc_depen.value=0;
		document.form.slc_estado.disabled=true;
		document.form.slc_cargo_fundacion.disabled=true;	      
	}
	else if(valor=='2') { 
		document.form.txt_ci.disabled=true;
		document.form.txt_nombre.disabled=false;
		document.form.txt_apellido.disabled=true;
		document.form.slc_depen.disabled=true;
		document.form.slc_depen.value=0;
		document.form.slc_estado.disabled=true;
		document.form.slc_cargo_fundacion.disabled=true;	      
	}
	else if(valor=='3') { 
		document.form.txt_ci.disabled=true;
		document.form.txt_nombre.disabled=true;
		document.form.txt_apellido.disabled=false;
		document.form.slc_depen.disabled=true;
		document.form.slc_depen.value=0;
		document.form.slc_estado.disabled=true;
		document.form.slc_cargo_fundacion.disabled=true;	      
	}
	else if(valor=='4')  { 
		document.form.txt_ci.disabled=true;
		document.form.txt_nombre.disabled=true;
		document.form.txt_apellido.disabled=true;
		document.form.slc_depen.disabled=false;
		document.form.slc_estado.disabled=true;
		document.form.slc_cargo_fundacion.disabled=true;	    
	}
	else if(valor=='5')  { 
		document.form.txt_ci.disabled=true;
		document.form.txt_nombre.disabled=true;
		document.form.txt_apellido.disabled=true;
		document.form.slc_depen.disabled=true;
		document.form.slc_estado.disabled=false;
		document.form.slc_cargo_fundacion.disabled=true;	  
	}
	else if(valor=='6')  { 
		document.form.txt_ci.disabled=true;
		document.form.txt_nombre.disabled=true;
		document.form.txt_apellido.disabled=true;
		document.form.slc_depen.disabled=true;
		document.form.slc_estado.disabled=true;
		document.form.slc_cargo_fundacion.disabled=false;	 	  
	} 
}

function ejecutar() {
	if ((document.form.txt_nombre!='') || (document.form.txt_apellido!='') || (document.form.txt_ci!='') || (document.form.slc_estado!='0') || (document.form.slc_cargo_fundacion!='') || (document.form.slc_depen!='')) {
		document.form.validar.value=1;
		document.form.submit();
	}
	else alert ("Debe seleccionar una opci\u00f3n de b\u00fasqueda");
}	

function detalle(codigo) {
    url="detalleEmpleado.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}
</script>
<script language="JavaScript" src="../../includes/js/funciones.js"> </script>
</head>
<body>
<form name="form" action="buscarEmpleado.php" method="post">
<input type="hidden" name="validar" value="0" >
<br />
<br />
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
  <td colspan="3"class="normalNegroNegrita">
  B&Uacute;SQUEDA DE EMPLEADOS </span></td>
</tr>
<tr>
  <td class="normal"><input name="opcion" type="radio" value="1" onClick="javascript:deshabilitar_combo(1)" /> </td>
  <td class="normalNegrita">Documento de Identidad:</td>
  <td class="normal"><input type="text" name="txt_ci" value="" disabled class="normal"/>  </td>
</tr>
<tr class="normal">
  <td><input name="opcion" type="radio" value="2" onClick="javascript:deshabilitar_combo(2)" /></td>
  <td class="normalNegrita">Nombre(s):</td>
  <td><input type="text" name="txt_nombre" value="" disabled class="normal"></td>
</tr>
<tr class="normal">
  <td><input name="opcion" type="radio" value="3" onClick="javascript:deshabilitar_combo(3)" /></td>
  <td class="normalNegrita">Apellido(s):</td>
  <td><input type="text" name="txt_apellido" value="" disabled class="normal"/></td>
</tr>
<tr class="normal">
  <td><input name="opcion" type="radio" value="4" onclick="javascript:deshabilitar_combo(4)" /></td>
  <td class="normalNegrita">Dependencia:</td>
  <td>
    <select name="slc_depen" id="slc_depen" class="normal" onclick="javascript:deshabilitar_combo(4)" disabled>
      <option value="0">[Seleccione]</option>
      <?php
	$sql="SELECT depe_id,depe_nombre FROM sai_dependenci where esta_id=1"; 
	$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
	while($row=pg_fetch_array($resultado)){ ?>
      <option value="<?=trim($row['depe_id'])?>"><?php echo $row['depe_nombre'];?></option>
      <?php } ?>
    </select>
    </span>
   </td>
  </tr>
<tr class="normal">
  <td><input name="opcion" type="radio" value="5" onclick="javascript:deshabilitar_combo(5)" /></td>
  <td class="normalNegrita">Estado:</td>
  <td>
   <select name="slc_estado" id="slc_estado" class="normal">
      <option value="0">[Seleccione]</option>
      <option value="1">Activo</option>
	  <option value="2">Inactivo</option>
   </select>  </td>
</tr>
<tr class="normal">
	<td><input name="opcion" type="radio" value="6" onclick="javascript:deshabilitar_combo(6)" /></td>
	<td class="normalNegrita">Cargo en la Fundaci&oacute;n:</td>
	<td>
	<select name="slc_cargo_fundacion" class="normal">
	<option value="">[Seleccione]</option>
	<?php
	$sql="SELECT carg_nombre,carg_fundacion FROM sai_cargo where esta_id=1"; 
	$resultado=pg_query($conexion,$sql) or die("Error al mostrar cargo en la Fundaci&oacute;n");
	while($row=pg_fetch_array($resultado)) { 
		$carg_nombre=trim($row['carg_nombre']);
		$carg_fundacion=trim($row['carg_fundacion']);
		?> <option value="<?php echo $carg_fundacion;?>"><?php echo $carg_nombre;?></option> <?php 
	} 
	?>
	</select></td>
</tr>
<tr>
  <td colspan="3" > &nbsp;</td>
</tr>
<tr>
  <td height="49" colspan="3" align="center">
    <input class="normalNegro" type="button" value="Buscar" onclick="javascript:ejecutar();"/>
</table>
</form>
<br/>
<?php 
$condicion = "and e.esta_id=1";
if ($_GET['validar']==1) 
    echo "<SCRIPT LANGUAGE='JavaScript'>"."alert ('Seleccione una Opción de Búsqueda......');"."</SCRIPT>";
else {
	if ($_POST['txt_ci']!='') $condicion=" and empl_cedula='".trim($_POST['txt_ci'])."'"; 
	else if ($_POST['txt_nombre']!='') $condicion=" and upper(empl_nombres) like '%".trim(strtoupper($_POST['txt_nombre']))."%'";
	else if ($_POST['txt_apellido']!='') $condicion=" and upper(empl_apellidos) like '%".trim(strtoupper($_POST['txt_apellido']))."%'"; 
	else if ($_POST['slc_depen']!='0' && $_POST['slc_depen']!='') $condicion=" and depe_id='".trim(strtolower($_POST['slc_depen']))."'";
	else if ($_POST['slc_cargo_fundacion']!='') $condicion=" and e.carg_fundacion='".$_POST['slc_cargo_fundacion']."'";  
	else if ($_POST['slc_estado']!='0' && $_POST['slc_estado']!='') $condicion=" and e.esta_id=".$_POST['slc_estado'];
    $nroFilas=0;
    $sql= "SELECT e.empl_cedula, upper(e.empl_nombres) as empl_nombres, upper(e.empl_apellidos) as empl_apellidos, e.empl_tlf_ofic,
		e.nacionalidad,e.empl_email,e.depe_cosige,e.carg_fundacion,
		e.empl_observa,e.usua_login,e.esta_id, est.esta_nombre, upper(c.carg_nombre) as carg_nombre, upper(d.depe_nombre) as depe_nombre from sai_empleado e, sai_estado est, sai_cargo c, sai_dependenci d where e.esta_id=est.esta_id and e.carg_fundacion=c.carg_fundacion and e.depe_cosige=d.depe_id ".$condicion. " order by d.depe_id, e.empl_nombres, e.empl_apellidos";
	$resultado=pg_query($conexion,$sql) or die("Error al consultar empleado");
?>
<table width="100%" border="0" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray" align="center">
		<td class="normalNegroNegrita" width="10%">Documento de Identidad</td>
		<td class="normalNegroNegrita" width="15%">Nombre(s)</td>
		<td class="normalNegroNegrita" width="15%">Apellido(s)</td>
		<td class="normalNegroNegrita" width="20%">Cargo</td>
		<td class="normalNegroNegrita" width="25%">Dependencia</td>
		<td class="normalNegroNegrita" width="5%">Estado</td>
		<td class="normalNegroNegrita" width="10%">Opciones</td>
	 </tr>
<?php
	while($row=pg_fetch_array($resultado)) {
		 $ci=trim($row['empl_cedula']);
		 $nacionalidad=trim($row['nacionalidad']);
		 $nombre=trim($row['empl_nombres']);
		 $apellido=trim($row['empl_apellidos']);
		 $esta_id=trim($row['esta_id']); 
		 $estado=trim($row['esta_nombre']);					 
		 $cargo=trim($row['carg_nombre']);
		 $dependencia=trim($row['depe_nombre']);
	?>
	  <tr class="normal">
		<td><?php echo $nacionalidad."-".$ci;?></td>
		<td><?php echo $nombre;?></td>
		<td><?php echo $apellido;?></td>
		<td><?php echo $cargo;?></td>
		<td><?php echo $dependencia;?></td>
		<td><?php echo $estado;?></td>
		<td align="left" class="normal">
		<a href="javascript:detalle('<?=$ci?>')" class="normal"> Ver Detalle</a><br>
	    <a href="modificarEmpleado.php?codigo=<?=$ci?>" class="normal"> Modificar</a>					    </td>
	  </tr>
   <?php }?>
  </table> 
	<?php } pg_close($conexion); ?>
</body>
</html>