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
<title>.:SAFI:Ingresar Empleado</title>
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
  $empl_observa=trim($_POST['txt_observa']);
  $usua_login=$_SESSION['login'];
  $carg_fundacion=trim($_POST['slc_cargo_fundacion']);
  $esta_id=1;
  //Insercion de los datos en la tabla sai_empleado
  $sql="INSERT INTO sai_empleado (empl_cedula, empl_nombres, empl_apellidos, empl_tlf_ofic, nacionalidad, empl_email, depe_cosige, carg_fundacion, empl_observa, usua_login, esta_id) values ('".$empl_cedula."','".$empl_nombres."','".$empl_apellidos."','".$empl_tlf_ofic."','".$empl_nacionalidad."',
 '".$empl_email."','".$dependencia."','".$carg_fundacion."','".$empl_observa."','".$usua_login."',".$esta_id.")"; 
   $resultado=pg_query($conexion,$sql);
   
		if($empl_tlf_ofic=="") $empl_tlf_ofic="No disponible";
		//Consulta a la tabla sai_cargo 
		$sql2="SELECT carg_nombre,carg_fundacion FROM sai_cargo where carg_fundacion='".$carg_fundacion."'"; 
		$resultado_set_most2=pg_query($conexion,$sql2);
		if($rowc=pg_fetch_array($resultado_set_most2)){$cargo=trim($rowc['carg_nombre']);}
		
		$sql="SELECT depe_nombre,depe_id FROM sai_dependenci where depe_id='".$depe_cosige."'"; 
		$resultado_set_most=pg_query($conexion,$sql);
		if($row=pg_fetch_array($resultado_set_most)){$dependencia=trim($row['depe_nombre']);}
		?>
		<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
		<tr class="td_gray" >
		  <td colspan="2" class="normalNegroNegrita">EMPLEADO REGISTRADO</td>
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
<? pg_close($conexion); ?>
</body>
</html>