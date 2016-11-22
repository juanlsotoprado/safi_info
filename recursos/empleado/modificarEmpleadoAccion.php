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
</head>
<body>
<?php
  $empl_cedula=trim($_POST['identificacion']);
  $empl_nacionalidad=trim($_POST['nacionalidad']);  
  $empl_nombres=trim($_POST['txt_nombre']);
  $empl_apellidos=trim($_POST['txt_apellido']);
  $empl_tlf_ofic=trim($_POST['txt_telefono']);
  $empl_email=trim($_POST['txt_email']);
  $dependencia=trim($_POST['cmb_dependencia']);
  $carg_fundacion=trim($_POST['slc_cargo_fundacion']);  
  $empl_observa=trim($_POST['txt_observa']);
  $usua_login=$_SESSION['login'];
  $esta_id=trim($_POST['opt_estado']);
  //modificación de los datos en la tabla sai_empleado
  $sql="select * from  sai_modificar_empleado('".$empl_cedula."','".$empl_nombres."','".$empl_apellidos."','".$empl_tlf_ofic."','".$empl_nacionalidad."',
 '".$empl_email."','".$dependencia."','".$carg_fundacion."','".$empl_observa."',".$esta_id.")"; 
   $resultado=pg_query($conexion,$sql);
   if($row=pg_fetch_array($resultado)) {
		if($empl_tlf_ofic=="") $empl_tlf_ofic="No disponible";
		//Consulta a la tabla sai_cargo 
		$sql1="SELECT carg_nombre,carg_fundacion FROM sai_cargo where carg_fundacion='".$carg_fundacion."'"; 
		$resultado1=pg_query($conexion,$sql1);
		if($row1=pg_fetch_array($resultado1)){$cargo=trim($row1['carg_nombre']);}
		
		$sql1="SELECT depe_nombre,depe_id FROM sai_dependenci where depe_id='".$dependencia."'"; 
		$resultado1=pg_query($conexion,$sql1);
		if($row1=pg_fetch_array($resultado1)){$dependencia=trim($row1['depe_nombre']);}
		?>
		<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
		<tr class="td_gray" >
		  <td colspan="2" class="normalNegroNegrita">EMPLEADO MODIFICADO</td>
		</tr>
		<tr>
		<td class="normalNegrita">Documento de Identidad:</td>
		<td class="normal"><?php echo $empl_nacionalidad."-".$empl_cedula;?></td>
		</tr>
		<tr class="normal"> 
		<td class="normalNegrita">Nombre(s):</td>
		<td><?php echo $empl_nombres;?></td>
		</tr>
		<tr class="normal"> 
		<td class="normalNegrita">Apellido(s):</td>
		<td><?php echo $empl_apellidos;?></td>
		</tr>
		<tr class="normal"> 
		<td class="normalNegrita">Tel&eacute;fono de Oficina:</td>
		<td><?php echo $empl_tlf_ofic;?></td>
		</tr>
		<tr class="normal"> 
		<td class="normalNegrita">Email:</td>
		<td><?php echo $empl_email;?></td>
		</tr> 
		<tr>
		<td class="normalNegrita">Dependencia:</td>
		<td class="normal"><?php echo $dependencia;?></td>
		</tr>
		<tr class="normal"> 
		<td class="normalNegrita">Cargo en la Fundaci&oacute;n:</td>
		<td><?php echo $cargo;?></td>
		</tr>
		<?php 
		if($empl_observa!=""){?>
		<tr class="normal"> 
		<td class="normalNegrita">Observaciones:</td>
		<td><?=$empl_observa?></td>
		</tr>
		<?php }?>
		<tr class="normal"> 
		<td class="normalNegrita">Estado del Recurso:</td>
	<?php 
	$sql="SELECT esta_nombre FROM sai_estado where esta_id=".$esta_id; 
	$resultado=pg_query($conexion,$sql) or die("Error al mostrar el nombre del estado");
	if ($row=pg_fetch_array($resultado)) $estado = $row['esta_nombre'];
	?>		
		<td><?=$estado;?></td>		
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
		<td colspan="2" align="center" class="normal">
			Registro modificado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br>
			<br>
			<a href="javascript:window.print()" class="normal"><img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br><br>
		</td>
		</tr>
		</table>
<?php	
}
else
   {    ?>
		<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray"> 
		<td class="normalNegroNegrita">MODIFICAR EMPLEADO</td>
		</tr>
		<tr>
		<td class="normal" align="center">
		<img src="../../imagenes/vineta_azul.gif" width="11" height="7">
		Ha ocurrido un error al ingresar los datos<br>
		<?php echo(pg_errormessage($conexion)); ?><br>
		<img src="../../imagenes/mano_bad.gif" width="31" height="38">
		<br><br>
		 	<input class="normalNegro" type="button" value="Regresar" onclick="javascript:location.href='buscarEmpleado.php';"/> 
		</td>
		</tr>
		</table>
<?php } pg_close($conexion); ?>
</body>
</html>