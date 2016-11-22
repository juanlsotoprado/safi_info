<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();    exit;
}ob_end_flush(); 
 
$codigo=$_GET['codigo'];
$cuenta=$_GET['cuenta'];
$estatus=$_GET['estatus'];
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Inhabilitar Chequera</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/JavaScript">
function regresar() {
	history.back();
}
</script>
<?
$sql="select * from sai_deshabilita_chequera ('".$codigo."','".$cuenta."',".$estatus.") as resultado_set(text)";
$result=pg_query($conexion,$sql) or die("ERROR: No se pudo realizar el cambio de estado de la chequera $sql"); 
echo "<div class='normal' align='center'><strong><br>Se proces&oacute; satisfactoriamente el cambio de estado de la chequera: ".$codigo."</strong>";
?>
<br><br>
<input class="normalNegro" type="button" value="Regresar" onclick="javascript:regresar();"/>
</head>
</html>

