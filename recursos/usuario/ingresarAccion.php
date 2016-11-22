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
include("encripta_desencripta.php");
//Datos proveniente del formulario origen
$fecha_ini=date("Y-m-d");
$txt_login=trim($_POST['txt_login']);
$txt_usuario=trim($_POST['txt_nombre']);
$txt_cedula=trim($_POST['txt_cedula']);
$slc_depen=trim($_POST['slc_depen']);
$descrip_unidad=trim($_POST['slc_depen_nom']);
$descrip_tipo=trim($_POST['descrip_tipo']);
$txt_arreglo_nombre=trim($_POST['txt_arreglo']);
$txt_password=trim($_POST['txt_clave']);
$txt_activo='t';
$txt_arreglo2=trim($_POST['txt_arreglo2']);
$slc_perf_principal=trim($_POST['slc_perf_principal']);
$principal_nombre=trim($_POST['slc_perf_principal_nom']);
//Estado del Recurso
if($txt_activo=='t'){$estado="Activo";}
else{$estado="Inactivo";}
// Encriptar
$palabra="nodigitarnada";
$txt_clave=crypt_md5($txt_password,$palabra);
$ejecuta=0;
// insertar el usuario
$sql = "select * from sai_insert_usuario('".$txt_login."','$txt_clave','".$txt_cedula."','".$slc_depen."') resultado_set (numeric)";
$resultado=pg_query($conexion,$sql);
if($row=pg_fetch_array($resultado)) {
	$mostrar=$row[0];
	if ($row[0]==null) $error="No se pudo crear al usuario porque existe en la  Base de Datos";
    else {  
		   $ejecuta=1;
		   //Registro del perfil principal  
		   $perfil_id=substr($slc_perf_principal,0,2).$slc_depen;
		   $sql2 = "select * from sai_insert_usuario_perfil('$txt_login','$perfil_id','1','$fecha_ini','')resultado_set (numeric)";
           $resultadol=pg_query($conexion,$sql2); 
		   if($txt_arreglo2!="") {
		   		$roles = explode("/", $txt_arreglo2);
				foreach ($roles as $arreglo_rol) {
					$perfil_id=substr($arreglo_rol,0,2).$slc_depen;
				   // insertar perfiles del usuario...
		       		$sql_p = "select * from sai_insert_usuario_perfil('$txt_login','$perfil_id','0','$fecha_ini','')resultado_set (numeric)";
               		$resultado=pg_query($conexion,$sql_p); 			   
		   		}
			}	
	    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<title>SAFI: Ingreso de Usuario</title>
<link  rel="stylesheet"   href="../../css/plantilla.css" type="text/css" media="all"  />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php
if($ejecuta==1){?>
<br/>
<br/>
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray"> 
	<td colspan="2" class="normalNegroNegrita">USUARIO REGISTRADO</td>
	</tr>
	<tr>
	<td class="normalNegrita">Nombre(s) y Apellido(s):</td>
	<td class="normal"><?php echo $txt_usuario;?></td>
	</tr>
	<tr>
	<td class="normalNegrita">Usuario:</td>
	<td class="normal"><?php echo $txt_login;?></td>
	</tr>
	<tr class="normal"> 
	<td class="normalNegrita">Documento de Identidad:</td>
	<td class="normal"><?php echo $txt_cedula;?></td>
	</tr>
	<?php  if ($mostrar<>0){?>
		<tr class="normal"> 
		<td class="normalNegrita">Dependencia:</td>
		<td class="normal"><?php echo $descrip_unidad;?> </td>
		</tr>
		<tr class="normal"> 
		<td class="normalNegrita">Cargo Principal:</td>
		<td class="normal"><?php echo $principal_nombre;?></td>
		</tr>
		<?php if($txt_arreglo2!=""){?>
		<tr class="normal"> 
		<td class="normalNegrita">Cargo Temporal:</td>
		<td class="normal">
		<?php
			$roles = explode("/", $txt_arreglo_nombre);
			foreach ($roles as $arreglo_rol){echo $arreglo_rol.'<br>';}
		?>		</td>
		</tr>
		<?php } 
		  } else {?>
				 <tr class="normal"> 
				 <td height="31" colspan="3" align="center" class="peq_naranja">
				 	Usuario ya se encuentra registrado en el sistema</td>
				 </tr> 
		<?php }?>	   
	<tr class="normal"> 
	<td class="normalNegrita">Estado del Recurso:</td>
	<td class="normal"><?php echo $estado;?></td>
	</tr> 
	<tr>
	<td colspan="2" align="center" class="normal">
	<br/><br/>
	Registro generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br>
	<br>
	<a href="javascript:window.print()" class="normal"><img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br><br>
	<br/><br/>
	</tr>
</table>
<?php }
else {?>
		<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray"> 
		<td class="normalNegroNegrita">INGRESAR USUARIO</td>
		</tr>
		<tr>
		<td colspan="4" class="normal"><br>
		<div align="center">
		<img src="../../imagenes/vineta_azul.gif" width="11" height="7">
		Ha ocurrido un error al ingresar los datos 
		<br><?php echo(pg_errormessage($conexion)); ?><br>
		<?php echo $error;?><br>
		<img src="../../imagenes/mano_bad.gif" width="31" height="38">
		</div>
		</td>
		</tr>
		</table>
		<?php   }  pg_close($conexion);  ?>
</body>
</html>