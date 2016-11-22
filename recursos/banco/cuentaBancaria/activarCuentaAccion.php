<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush(); 
 
$ctab_estatus=$_POST['ctab_estatus'];
$cuenta=$_POST['cuenta'];
$fecha=$_POST['fecha_apertura'];
if (!(empty($fecha))) {
	$ano=substr($fecha,6,9);
	$mes=substr($fecha,3,2);
	$dia=substr($fecha,0,2);
	$fecha_ini=$ano."-".$mes."-".$dia;
}
else $fecha_ini='';

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:SAFI:Habilitar cuenta</title>
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css"/>
<?
$ano1=date(Y);
$mes1=date(m);
$dia1=date(d);
$fecha_reg1=$ano1."-".$mes1."-".$dia1;
$sql="UPDATE sai_ctabanco
		SET ctab_fechacierre=null,
		ctab_fechacierrereg=null,
		ctab_fechaapert='".$fecha_ini."',
		ctab_estatus='1'
	WHERE ctab_numero='".$cuenta."'";
$result=pg_query($conexion,$sql) or die("ERROR: No se pudo habilitar la cuenta $sql"); 
/**********************/
echo "<div class='normal' align='center'><br>Se proces&oacute; satisfactoriamente el cambio de estado de la cuenta: <b>".$cuenta . "</b>";
echo "<br><br><a href='buscarCuenta.php'>Volver </a></div>";
pg_close($conexion);
?>
</head>
</html>