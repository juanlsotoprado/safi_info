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
<script language="JavaScript" src="../../js/funciones.js"> </script>
</head>
<body>
<?php
  $benvi_cedula=trim($_POST['identificacion']);
  $benvi_nombres=trim($_POST['txt_nombre']);
  $benvi_apellidos=trim($_POST['txt_apellido']);
  $benvi_nacionalidad=trim($_POST['nacionalidad']);
  $dependencia=trim($_POST['cmb_dependencia']);
  $benvi_tipo=trim($_POST['txt_tipo']);
  $benvi_estado=trim($_POST['estado']);
  $usua_login=$_SESSION['login'];

  $sql="UPDATE sai_viat_benef SET benvi_nombres = '".$benvi_nombres."', benvi_apellidos='".$benvi_apellidos."', nacionalidad='".$benvi_nacionalidad."',tipo='".$benvi_tipo."', depe_id= '".$dependencia."', benvi_esta_id=".$benvi_estado." WHERE benvi_cedula='".$benvi_cedula."'";
  $resultado=pg_query($conexion,$sql);
?>
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray" >
	  <td colspan="2" class="normalNegroNegrita">TRABAJADOR MODIFICADO</td>
	</tr>
	<tr>
		<td class="normalNegrita">Documento de Identidad:</td>
		<td class="normal"><?php echo $benvi_nacionalidad."-".$benvi_cedula;?></td>
		</tr>
		<tr class="normal"> 
		<td class="normalNegrita">Nombre(s): </td>
		<td><?php echo $benvi_nombres;?></td>
		</tr>
		<tr class="normal"> 
		<td class="normalNegrita">Apellido(s):</td>
		<td><?php echo $benvi_apellidos;?></td>
		</tr>
		<tr class="normal">
		<td class="normalNegrita">Dependencia:</td>
	<?php 
	$sql="SELECT depe_nombre FROM sai_dependenci where depe_id='".$dependencia."'"; 
	$resultado=pg_query($conexion,$sql) or die("Error al mostrar el nombre de la dependencia");
	if ($row=pg_fetch_array($resultado)) $dependencia = $row['depe_nombre'];
	?>		
		<td><?php echo $dependencia;?></td>
		</tr>
		<tr class="normal"> 
		<td class="normalNegrita">Tipo:</td>
		<td><?=$benvi_tipo?></td>
		</tr>
		<tr class="normal"> 
		<td class="normalNegrita">Estado del Recurso:</td>
	<?php 
	$sql="SELECT esta_nombre FROM sai_estado where esta_id=".$benvi_estado; 
	$resultado=pg_query($conexion,$sql) or die("Error al mostrar el nombre del estado");
	if ($row=pg_fetch_array($resultado)) $estado = $row['esta_nombre'];
	?>		
		<td><?=$estado;?></td>
		</tr>
		<tr><td height="16" colspan="2">&nbsp;</td></tr>
		<tr>
		<td height="16" colspan="2" align="center" class="normal">
			Registro generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br>
			<br>
			<a href="javascript:window.print()" class="normal"><img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br><br>
		</td>
		</tr>
		</table>
		<?php  pg_close($conexion); ?>
</body>
</html>