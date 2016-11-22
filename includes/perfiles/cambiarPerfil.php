<?php
ob_start();
session_start();
$perfilActual=$_SESSION['user_perfil_id'];
$perfiles=$_SESSION['perfiles'];
$perfil=$_REQUEST['perfil'];

if($perfil && $perfil!=""){
	$_SESSION['user_perfil_id']=$perfil;
	$perfilActual=$perfil;
	$query="SELECT * FROM sai_buscar_cargo_depen('".$perfil."') as carg_nombre ";
	$resultado=pg_query($conexion,$query) or die("Error al mostrar");
	$row=pg_fetch_array($resultado);
	$_SESSION['user_perfil']= trim($row['carg_nombre']);
	$_SESSION['user_depe_id']= substr($perfil,2,3) ;
	$query="SELECT * FROM sai_buscar_dependencia('".$_SESSION['user_depe_id']."') as dep_nombre ";	
	$resultado=pg_query($conexion,$query) or die("Error al mostrar");
	$row=pg_fetch_array($resultado);	
	$_SESSION['user_depe']= trim($row["dep_nombre"]); 
}
?>