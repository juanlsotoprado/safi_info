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
$comprobar=0;
$txt_usuario=trim($_GET['codigo']);
$sqlu = "select * from sai_buscar_usuario_empleado('4','','$txt_usuario')
resultado_set(usua_login varchar,nombre text,empl_cedula varchar,usua_activo boolean,
depe_id varchar,nom_depen varchar,usua_clave varchar)"; 
$ejecuta=pg_query($conexion,$sqlu);	
if($row = pg_fetch_array($ejecuta)) {
	$comprobar=1;
	$nombres = trim($row['nombre']);
	$txt_login = trim($row['usua_login']);
	$txt_cedula = trim($row['empl_cedula']);
	$activo = trim($row['usua_activo']);
	$cod_depen = trim($row['depe_id']);
	$nom_depen = trim($row['nom_depen']);
	$txt_clave = trim($row['usua_clave']);
	//Estado del Recurso
	if($activo=='t') $estado="Activo";
	else $estado="Inactivo";
}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<title>SAFI: Detalle Usuario</title>
<link  rel="stylesheet"   href="../../css/plantilla.css" type="text/css" media="all"  />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php 
if($comprobar==1) {?>
	<br />
	<br />
	<table width="90%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray"> 
	<td colspan="2" class="normalNegroNegrita"><strong>DETALLE DEL USUARIO</strong></td>
	</tr>
	<tr>
	<td class="normalNegrita">Nombre(s) y Apellido(s):</td>
	<td class="normal"><?php echo $nombres;?></td>
	</tr>
	<tr>
	<td class="normalNegrita">Usuario:</td>
	<td class="normal"><?php echo $txt_usuario;?></td>
	</tr>
	<tr class="normal"> 
	<td class="normalNegrita">Documento de Identidad:</td>
	<td><?php echo $txt_cedula;?></td>
	</tr>
	<tr class="normal"> 
	<td class="normalNegrita">Dependencia:</td>
	<td><?php echo $nom_depen;?></td>
	</tr>
	<tr class="normal"> 
	<td  class="normalNegrita">Cargo Principal:</td>
	<td>
	<?php
	$sql="select up.carg_id as codigo, upper(c.carg_nombre) as cargo, upper(d.depe_nombre) as dependencia 
			from sai_usua_perfil up, sai_cargo c, sai_dependenci d where uspe_tp='1' and substring(up.carg_id from 0 for 3)=c.carg_fundacion and substring(up.carg_id from 3 for 3)=d.depe_id and up.usua_login='".$txt_usuario."' order by cargo"; 		
	$respuesta=pg_query($conexion,$sql) or die("Error al mostrar el perfil principal"); 
	if($row=pg_fetch_array($respuesta)){echo $row['cargo']."-".$row['dependencia'];}
	?>
	</td>
	</tr>
	<tr class="normal"> 
	<td class="normalNegrita">Cargo Temporal:</td>
	<td class="normal">
	<?php
	$sql="select up.carg_id as codigo, upper(c.carg_nombre) as cargo, upper(d.depe_nombre) as dependencia 
			from sai_usua_perfil up, sai_cargo c, sai_dependenci d where uspe_tp='0' and substring(up.carg_id from 0 for 3)=c.carg_fundacion and substring(up.carg_id from 3 for 3)=d.depe_id and up.usua_login='".$txt_usuario."' order by cargo"; 	
	$respuesta=pg_query($conexion,$sql) or die("Error al mostrar los perfiles temporales"); 
	if(pg_num_rows($respuesta)==0) echo "No posee"; 
	else {
		while($row=pg_fetch_array($respuesta)) {
			  echo $row['cargo']."-".$row['dependencia']."<br>";
			}     
		}
	?></td>
	</tr>
	<tr class="normal"> 
	<td class="normalNegrita">Estado del Recurso:</td>
	<td><?php echo $estado;?></td>
	</tr> 
	<tr>
	<td colspan="2" class="normal" align="center">
	<br>
	Detalle generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br>
	<br>	
	<a href="javascript:window.print()" class="normal"><img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br><br>
	<br>
	</tr>
	</table>
<?php
} else  {
?>
	    <table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray"> 
		<td class="normalNegroNegrita">DETALLE USUARIO</td>
		</tr>
		<tr>
		<td class="normal" align="center"><br>
		<img src="../../imagenes/vineta_azul.gif" width="11" height="7">
		Ha ocurrido al consultar detalles
		<br><?php echo(pg_errormessage($conexion)); ?><br>
		<img src="../../imagenes/mano_bad.gif" width="31" height="38">
		<br><br>
		</td>
		</tr>
		</table>
	 <?php } pg_close($conexion);?>
</body>
</html>