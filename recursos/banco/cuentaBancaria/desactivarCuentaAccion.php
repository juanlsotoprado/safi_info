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
 
$cuenta=$_POST['cuenta'];
$fecha=$_POST['fecha_cierre'];
if (!(empty($fecha))){
	$ano=substr($fecha,6,9);
	$mes=substr($fecha,3,2);
	$dia=substr($fecha,0,2);
	$fecha_ini=$ano."-".$mes."-".$dia;
}
else $fecha_ini='';
$ano=date(Y);
$mes=date(m);
$dia=date(d);
$fecha_cierre_sistema=$ano."-".$mes."-".$dia;

/**********************/
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Inhabilitar Cuenta Bancaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<?
$ano1=date(Y);
$mes1=date(m);
$dia1=date(d);
$fecha_reg1=$ano1."-".$mes1."-".$dia1;
$sql="UPDATE sai_ctabanco SET ctab_fechacierre='".$fecha_ini."', ctab_fechacierrereg='".$fecha_cierre_sistema."', ctab_estatus='2' WHERE ctab_numero='$cuenta'";
$result=pg_query($conexion,$sql) or die("ERROR: No se pudo inhabilitar la cuenta bancaria". $sql); 
/**********************/
/*$sql="DELETE FROM sai_chequera WHERE ctab_numero='$cuenta'";
$result_delete=pg_query($conexion,$sql) or die("ERROR: No se pudo eliminar las chequeras de la cuenta bancaria". $sql);*/ 
echo "<div class='normal' align='center'><br>Se proces&oacute; satisfactoriamente el cambio de estado de la cuenta: <b>".$cuenta."</b>";
echo "<br><br><a href='buscarCuenta.php'>Volver </a></div>";
pg_close($conexion);
?>
</head>
</html>