<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();    exit;
}ob_end_flush(); 
 
$codigo=$_GET['codigo'];
$cuenta=$_GET['cuenta'];
$estatus=$_GET['estatus'];
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Activar Chequera</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/JavaScript">
function regresar() {
	history.back();
}
</script>
<?
$sql = "select id_cheque
from sai_chequera cq, sai_cheque ch
where cq.nro_chequera=ch.nro_chequera
and ch.estatus_cheque=1 and cq.nro_chequera='".$codigo."'";

$resultado=pg_query($conexion,$sql) or die("Error al realizar la consulta de cheques libre por chequera");
$nroFilas = pg_num_rows($resultado);
if (($nroFilas<=0) and (($_GET['codigo']!=0)))  {
	echo "<div class='normal' align='center'>No existen cheques libres para la chequera nro. ".$codigo."; por lo tanto no puede ser activada";
}
else {
	$sql = "UPDATE sai_chequera 
	SET cheq_activa='2'
	WHERE  ctab_numero='".$cuenta."' and cheq_activa=1"; 
	$resultado=pg_query($conexion,$sql) or die("Error al deshabilitar chequeras activas");

	$sql = "UPDATE sai_chequera
	SET cheq_activa='1'
	WHERE  nro_chequera='".$codigo."'";
	$resultado=pg_query($conexion,$sql) or die("Error al activar chequera inactiva");
	echo "<div class='normal' align='center'>Se activ\u00f3 la chequera nro.".$codigo.", la cual posee ".$nroFilas." cheque(s) activo(s)";
}
echo "<br><br><a href='javascript:regresar();'>Volver </a></div>";
?>
</head>
</html>
<?php pg_close($conexion); ?>