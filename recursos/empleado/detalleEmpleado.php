<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
    ob_end_flush();
    exit;
}
ob_end_flush(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Detalle Empleado</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script LANGUAGE="JavaScript" SRC="../../includes/js/funciones.js"> </SCRIPT>
</head>
<body>
<?php
$cedula=trim($_GET['codigo']); 
$sql="SELECT e.empl_cedula, e.empl_nombres, e.empl_apellidos, e.empl_tlf_ofic,
e.nacionalidad,e.empl_email,e.depe_cosige,e.carg_fundacion,
e.empl_observa,e.usua_login,e.esta_id, est.esta_nombre, c.carg_nombre, d.depe_nombre from sai_empleado e, sai_estado est, sai_cargo c, sai_dependenci d where e.esta_id=est.esta_id and e.carg_fundacion=c.carg_fundacion and e.depe_cosige=d.depe_id and e.empl_cedula='".$cedula."'"; 
$resultado=pg_query($conexion,$sql) or die("Error al consultar empleado");
if($row=pg_fetch_array($resultado)) {
	$empl_cedula=trim($row['empl_cedula']);
    $empl_nombres=trim($row['empl_nombres']);
    $empl_apellidos=trim($row['empl_apellidos']);
    $empl_telefono=trim($row['empl_tlf_ofic']);
    $empl_nacionalidad=trim($row['nacionalidad']);
    $empl_email=trim($row['empl_email']);
    $dependencia=trim($row['depe_nombre']);
    $cargo=trim($row['carg_nombre']);
    $empl_observaciones=trim($row['empl_observa']);
    $usua_login=trim($row['usua_login']);
    $esta_id=trim($row['esta_nombre']);
    
	//Verifica si las variables no nulas estan vacias
	if($empl_tlf_ofic=="") $empl_tlf_ofic="No disponible";
?>
		<table width="80%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
		<tr class="td_gray" >
		  <td colspan="2" class="normalNegroNegrita">DETALLE EMPLEADO</td>
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
		<td>Activo</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
		<td colspan="2" align="center" class="normal">
			Registro generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br>
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
		<td class="normalNegroNegrita">DETALLE EMPLEADO</td>
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
<?php } pg_close($conexion); ?>
</body>
</html>