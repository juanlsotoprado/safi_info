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
<title>.:SAFI:Detalle otro trabajador</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../includes/js/funciones.js"> </script>
</head>
<body>
<?php
$cedula=trim($_GET['codigo']); 
$sql="SELECT v.*, est.esta_nombre, d.depe_nombre from sai_viat_benef v, sai_estado est, sai_dependenci d where v.benvi_esta_id=est.esta_id and v.depe_id=d.depe_id and v.benvi_cedula='".$cedula."'";
$resultado=pg_query($conexion,$sql);
if($row=pg_fetch_array($resultado)) {
    $benvi_cedula=trim($row['benvi_cedula']);
    $benvi_nombres=trim($row['benvi_nombres']);
    $benvi_apellidos=trim($row['benvi_apellidos']);
    $benvi_nacionalidad=trim($row['nacionalidad']);
    $dependencia=trim($row['depe_id']);
    $benvi_tipo=trim($row['tipo']);
    $estado = trim($row['esta_nombre']);
?>
<table width="80%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray" >
	  <td colspan="2" class="normalNegroNegrita">DETALLE TRABAJADOR</td>
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
		<td><?php echo $estado;?></td>
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
<?php	
}
else  {    ?>
		<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray"> 
		<td class="normalNegroNegrita">DETALLE OTRO TRABAJADOR</td>
		</tr>
		<tr>
		<td class="normal" align="center">
		<img src="../../imagenes/vineta_azul.gif" width="11" height="7">
		Ha ocurrido un error al ingresar los datos<br>
		<?php echo(pg_errormessage($conexion)); ?><br>
		<img src="../../imagenes/mano_bad.gif" width="31" height="38">
		<br><br>
		</td>
		</tr>
		</table>
		<?php   }  pg_close($conexion); ?>
</body>
</html>