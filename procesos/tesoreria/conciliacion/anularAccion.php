<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
require_once("../../../includes/arreglos_pg.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado"))  {
	header('Location:../../../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Anular Conciliaci&oacute;n Bancaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body>
<br />
<br />
<div align="center">
<?php 
$depe_id=$_SESSION['user_depe_id'];
$fecha = $_POST['hid_desde_itin'];
$codigos = "";
if (isset($_POST['solicitud'])) {
	$cod = $_POST["solicitud"];
}
if (count($cod)>0) {

for ($x=0;$x<count($cod);$x++) {
	$codigo = $cod[$x];

/*Actualizar mov_cta-banco*/
$sql="update sai_mov_cta_banco set conciliado=51, num_conci=null, fecha_descon=null where docg_id='".$codigo."'";
$resultado=pg_query($conexion,$sql);

if ($codigos == "") $codigos = $codigo;
else $codigos = $codigos . ", ". $codigo;

/*Actualizar cta-banco-saldo*/
$sql="delete from sai_ctabanco_saldo where docg_id in (select pgch_id from sai_pago_cheque where docg_id='".$codigo."' and esta_id<>15) or docg_id in (select trans_id from sai_pago_transferencia where docg_id='".$codigo."' and esta_id<>15) or (docg_id='".$codigo."' and docg_id like 'codi%')";
$resultado=pg_query($conexion,$sql);
} 
echo "<div class='normalNegrita' align='center'>Se proces&oacute; satisfactoriamente la anulaci&oacute;n de la conciliaci&oacute;n bancaria de los siguientes documentos: ".$codigos."</div>";
}
else echo "<div class='normalNegrita' align='center'>Error: Debe seleccionar al menos un documento para anular la conciliaci&oacute;n";
?>
</div>
</body>
</html>