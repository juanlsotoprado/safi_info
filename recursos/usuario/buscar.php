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
<title>.:SAFI:Buscar Usuario</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script>
function deshabilitar_combo(valor) {
	if(valor=='1') { 
		document.form.txt_ci.disabled=false;
		document.form.txt_login.disabled=true;
		document.form.slc_depen.disabled=true;
		document.form.edo.disabled=true;
		document.form.slc_depen.value=0;
		document.form.edo.value=0;
		document.form.txt_login.value="";
	}
	else if(valor=='2') { 
		document.form.txt_ci.disabled=true;
		document.form.txt_login.disabled=false;
		document.form.slc_depen.disabled=true;
		document.form.edo.disabled=true;
		document.form.slc_depen.value=0;
		document.form.edo.value=0;
		document.form.txt_ci.value="";
	}
	else if(valor=='3') { 
		document.form.txt_ci.disabled=true;
		document.form.txt_login.disabled=true;
		document.form.slc_depen.disabled=false;
		document.form.edo.disabled=true;
		document.form.edo.value=0;
		document.form.txt_ci.value="";
		document.form.txt_login.value="";
	}
	else if(valor=='4') { 
		document.form.txt_ci.disabled=true;
		document.form.txt_login.disabled=true;
		document.form.slc_depen.disabled=true;
		document.form.edo.disabled=false;
		document.form.slc_depen.value=0;
		document.form.txt_ci.value="";
		document.form.txt_login.value="";
	}
}

function ejecutar_tres(codigoa,codigob,codigoc,codigod) {
	if ((codigoa=='') && (codigob=='') && (codigoc=='0') && (codigod=='0')) 
		document.form.hid_buscar1.value=1;
	else document.form.hid_buscar1.value=2;
	document.form.submit();
}

function detalle(codigo) {
    url="detalle.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) newwindow.focus();
}
</script>
<script language="JavaScript" src="../../includes/js/funciones.js"> </script>
</head>
<body>
<form name="form" action="buscar.php" method="post">
<input type="hidden" name="hid_buscar1" >
<br />
<br />
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
  <td colspan="3" class="normalNegroNegrita"> B&Uacute;SQUEDA DE USUARIOS</td>
</tr>
<tr>
  <td align="center" class="normal">
        <input name="opt_agencia" type="radio" value="1" onClick="javascript:deshabilitar_combo(1)" />
  </td>
  <td class="normalNegrita">Documento de Identidad:</td>
  <td class="normal">
  <input type="text" name="txt_ci" value="" disabled="true" class="normal">  </td>
</tr>
<tr>
  <td align="center" class="normal">
        <input name="opt_agencia" type="radio" value="2" onClick="javascript:deshabilitar_combo(2)" />
  </td>
  <td class="normalNegrita">Usuario:</td>
  <td class="normal">
  <input name="txt_login" type="text" disabled="true" class="normal" id="txt_login" value="">  </td>
</tr>
<tr>
  <td align="center" class="normal">
        <input name="opt_agencia" type="radio" value="3" onclick="javascript:deshabilitar_combo(3)" />
  </td>
  <td class="normalNegrita">Dependencia:</td>
  <td class="peq_naranja">
    <select name="slc_depen" id="slc_depen" class="normal" onclick="javascript:deshabilitar_combo(3)" disabled>
      <option value="0">[Seleccione]</option>
      <?php
	$sql="SELECT depe_id,depe_nombre FROM sai_dependenci where esta_id=1"; 
	$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
	while($row=pg_fetch_array($resultado)) { ?>
      <option value="<?=trim($row['depe_id'])?>"><?php echo $row['depe_nombre'];?></option>
    <?php } ?>
    </select>
	</td>
  </tr>
<tr>
  <td align="center"><div align="center"><span class="normalNegrita">
      <input name="opt_agencia" type="radio" value="4" onclick="javascript:deshabilitar_combo(4)" />
  </span></div></td>
  <td class="normalNegrita">Estado:</td>
  <td>
       <select name="edo" class="normal" id="edo" disabled="disabled">
        <option value="-1">Seleccione</option>
        <option value="1">Activo</option>
        <option value="0">Inactivo</option>
      </select>
      </td>
</tr>
<tr>
  <td colspan="3" align="center">
      <input class="normalNegro" type="button" value="Buscar" onclick="javascript:ejecutar_tres(document.form.txt_ci.value,document.form.txt_login.value,document.form.slc_depen.value,document.form.edo.value);"/>
   </table>
</form>
<?php 
if ($_POST['txt_ci']!='') $condicion=" where empl_cedula='".trim($_POST['txt_ci'])."'";
else if ($_POST['txt_login']!='') $condicion=" where lower(usua_login)='".trim(strtolower($_POST['txt_login']))."'"; 
else if ($_POST['slc_depen']!='0' && $_POST['slc_depen']!='') $condicion=" where depe_id='".trim(strtolower($_POST['slc_depen']))."'"; 
else if ($_POST['edo']!='-1' && $_POST['edo']!='') $condicion=" where usua_activo='".trim($_POST['edo'])."'";
					
$sql = "select * from sai_usuario". $condicion . " order by empl_cedula";
								
$nroFilas=0;
if (($_POST['txt_ci']!='') || ($_POST['txt_login']!='') || ($_POST['slc_depen']!='0') || ($_POST['edo']!='0')){
	$resultado_set_most=pg_query($conexion,$sql);
	$nroFilas = pg_num_rows($resultado_set_most);
	if($nroFilas<=0) {?> 
	<br/> <br/>
	 <?php
	     echo "<center><font color='#003399' class='titularMedio'>"."Actualmente No existen empleados con el criterio de b&uacute;squeda especificado"."</font></div></center>";
} 
else { ?>
<br />
<br />				 
<form name="form3" action="" method="post">
	<table width="60%" border="0" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td align="center" class="normalNegroNegrita"><span >Documento de Identidad</span></td>
			<td align="center" class="normalNegroNegrita">Usuario</td>
			<td align="center" class="normalNegroNegrita"><span >Nombre(s)</span></td>
			<td align="center" class="normalNegroNegrita"><span >Apellido(s)</span></td>
			<td align="center" class="normalNegroNegrita"><span >Estado</span></td>
			<td align="center" class="normalNegroNegrita"><span >Opciones</span></td>
		</tr>
		<?php
		while($rowor=pg_fetch_array($resultado_set_most))  {
			$ci=trim($rowor['empl_cedula']);
			$login=trim($rowor['usua_login']);
			$var=trim($rowor['usua_activo']);
			
			if ($var=='t')  $status="Activo";
			else $status="Inactivo";
			$sql_emp = "select nacionalidad, upper(empl_nombres) as empl_nombres, upper(empl_apellidos) as empl_apellidos from sai_empleado where empl_cedula='".$ci."'";
			$resultado_set_most_emp=pg_query($conexion,$sql_emp);
			$rowemp=pg_fetch_array($resultado_set_most_emp);
			$nacionalidad=trim($rowemp['nacionalidad']);		 
			$nombre=trim($rowemp['empl_nombres']);
			$apellido=trim($rowemp['empl_apellidos']);
		?>
		<tr class="normal">
			<td align="center" class="normal"><?php echo $nacionalidad."-".$ci;?></td>
			<td><?php echo $login;?></td>
			<td><?php echo $nombre;?></td>
			<td><?php echo $apellido;?></td>
			<td><?php echo $status;?></td>
			<td align="left" class="normal"><img src="../../imagenes/vineta_azul.gif" width="11" height="7"><a href="javascript:detalle('<?=$login?>')" class="normal"> Ver Detalle</a><br>
		    <img src="../../imagenes/vineta_azul.gif" width="11" height="7"><a href="modificar.php?txt_usuario=<?php echo $login;?>" class="normal"> Modificar</a>					    </td>
		</tr>
		 <?php }?>
  </table> 
	<?php } } pg_close($conexion); ?>
</form>
</body>
</html>