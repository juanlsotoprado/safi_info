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
$user_perfil_id=$_SESSION['user_perfil_id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<title>.:SAFI: Modificar Usuario</title>
<link  rel="stylesheet"   href="../../css/plantilla.css" type="text/css" media="all"  />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php
include("encripta_desencripta.php");
$fecha_ini=date("Y-m-d");
$txt_nombres=trim($_POST['txt_nombres']);
$txt_usuario=trim($_POST['txt_usuario']);
$txt_cedula=trim($_POST['txt_cedula_rif']);
$slc_depen=trim($_POST['slc_depen']);
$slc_perf_principal=trim($_POST['slc_perf_principal']);
$descrip_unidad=trim($_POST['slc_depen_nom']);
$txt_arreglo2=trim($_POST['txt_arreglo2']); 
$txt_password=trim($_POST['txt_clave']);
$txt_activo=trim($_POST['txt_activo']);
$txt_arreglo=trim($_POST['txt_arreglo']); 
$principal=trim($_POST['slc_perf_principal_nom']); 
$roles=explode("/", $txt_arreglo); 
// Encriptar
$palabra="nodigitarnada";
$txt_clave=crypt_md5($txt_password,$palabra);
$valorizar=0;
//Estado del Recurso
if($txt_activo==true){$estado="Activo";}
else $estado="No Activo";

$sql = "select * from sai_modificar_usuario('".$txt_usuario."','".$txt_clave."','".$txt_activo."','".$txt_cedula."','".$slc_depen."') resultado_set (numeric)";
$resultado=pg_query($conexion,$sql);

if ($user_perfil_id == "01000") { //Sólo si es el caso de Administrador, puede actualizar perfiles
	$roles = explode("/", $txt_arreglo);
	//Borrar roles que no estén relacionados con revisiones_doc
 	$sql_p = "delete from sai_usua_perfil where (usua_login, carg_id) not in (select usua_login, perf_id from sai_revisiones_doc where usua_login='".$txt_usuario."') and usua_login='".$txt_usuario."' and uspe_tp<>'1'";
	$resultado_set = pg_exec($conexion ,$sql_p);	
	if ($txt_arreglo!="") {	
		foreach ($roles as $arreglo_rol) {
 	    // insertar perfiles del usuario...
 		    $sql_p = "select usua_login from sai_usua_perfil where usua_login='".$txt_usuario."' and carg_id='".$arreglo_rol."'";
			$resultado=pg_query($conexion,$sql_p);
			$filas = pg_NumRows($resultado);
			if ($filas<1) {	    		 	    
	   			$sql_p = "select * from sai_insert_usuario_perfil('".$txt_usuario."','".$arreglo_rol."','0','".$fecha_ini."','')resultado_set (numeric)";
   				$resultado=pg_exec($conexion,$sql_p);
			} 			   
		}
	}	
}
?>
<table width="80%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray"> 
	  <td colspan="2" class="normalNegroNegrita"><strong>USUARIO MODIFICADO</strong></td>
	</tr>
	<tr>
	<td class="normalNegrita">Nombre(s) y Apellido(s):
	</td>
	<td class="normal"><?php echo $txt_nombres;?></td>
	</tr>
	<tr>
	<td class="normalNegrita">Usuario:</td>
	<td class="normal"><?php echo $txt_usuario;?> </td>
	</tr>
	<tr class="normal"> 
	<td class="normalNegrita">Documento de Identidad:</td>
	<td><?php echo $txt_cedula;?></td>
	</tr>
	<tr class="normal"> 
	<td class="normalNegrita">Dependencia:</td>
	<td><?php echo $descrip_unidad;?> </td>
	</tr>
	<tr class="normal"> 
	<td class="normalNegrita">Cargo Principal:</td>
	<td><?php echo $principal;?></td>
	</tr>
	<?php if($txt_arreglo2!="")	{?>
		<tr class="normal"> 
		<td class="normalNegrita">Cargo Temporal:</td>
		<td colspan="2" class="normal">
		<?php
		$roles = explode("/", $txt_arreglo2);
		foreach ($roles as $arreglo_rol){echo $arreglo_rol.'<br>';}
		?>
		</td>
		</tr>
	<?php
	}?>	
	<tr class="normal"> 
	<td class="normalNegrita">Estado del Recurso:</td>
	<td ><?php echo $estado;?></td>
	</tr>   
	<tr>
	<td height="15" colspan="2" align="center" class="normal">
	<br><br>
	Registro generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br>
	<br>
	<a href="javascript:window.print()" class="normal"><img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br><br>
	</td>
	</tr>
</table>
</body>
</html>
<?php  pg_close($conexion); ?>