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
<title>.:SAFI:Ingresar otro trabajador</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
</head>
<body>
<?php
  $benef_cedula=trim($_POST['identificacion']);
  $benef_nacionalidad=trim($_POST['nacionalidad']);
  $benef_nombres=trim($_POST['txt_nombre']);
  $benef_apellidos=trim($_POST['txt_apellido']);
  $dependencia=trim($_POST['cmb_dependencia']);
  $benef_tipo=trim($_POST['txt_tipo']);
  $usua_login=$_SESSION['login'];
  $esta_id=1;

  //Insercion de los datos en la tabla sai_viat_benef
  $sql="insert into sai_viat_benef (benvi_cedula,benvi_nombres,benvi_apellidos,depe_id,nacionalidad,tipo) 
  values ('".$benef_cedula."','".$benef_nombres."','".$benef_apellidos."','".$dependencia."','".$benef_nacionalidad."','".$benef_tipo."')"; 
  $resultado=pg_query($conexion,$sql);
?>
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray" >
	  <td colspan="2" class="normalNegroNegrita">TRABAJADOR REGISTRADO</td>
	</tr>
	<tr>
		<td class="normalNegrita">Documento de Identidad:</td>
		<td class="normal"><?php echo $benef_nacionalidad."-".$benef_cedula;?></td>
		</tr>
		<tr class="normal"> 
		<td class="normalNegrita">Nombre(s): </td>
		<td><?php echo $benef_nombres;?></td>
		</tr>
		<tr class="normal"> 
		<td class="normalNegrita">Apellido(s):</td>
		<td><?php echo $benef_apellidos;?></td>
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
		<td><?=$benef_tipo?></td>
		</tr>
		<tr class="normal"> 
		<td class="normalNegrita">Estado del Recurso:</td>
		<td>Activo</td>
		</tr>
		<tr><td height="16" colspan="5">&nbsp;</td></tr>
		<tr>
		<td height="16" colspan="5" align="center" class="normal">
			Registro generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br>
			<br>
			<a href="javascript:window.print()" class="normal"><img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br><br>
		</td>
		</tr>
		</table>
		<?php pg_close($conexion); ?>
</body>
</html>