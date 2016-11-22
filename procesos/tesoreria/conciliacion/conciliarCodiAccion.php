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
<title>Conciliaci&oacute;n Bancaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body>
<br />
<div align="center">
<?php 
$depe_id=$_SESSION['user_depe_id'];
$fecha = $_POST['fecha'];
$codigos = "";
if (isset($_POST['solicitud'])) {
	$cod = $_POST["solicitud"];
}
if (count($cod)>0) {
	$sql_conci = "select * from sai_insert_conci_bancaria('".$depe_id."') resultado_set(text)";
	$resultado1=pg_query($conexion,$sql_conci);

	for ($x=0;$x<count($cod);$x++) {
		$codigo = $cod[$x];
		$sql = "select * from sai_cambiar_estado_mov_bancario('".$codigo."', '".$fecha."', '".$depe_id."') resultado_set(bool)";
		$resultado=pg_query($conexion,$sql) or die("No se actualizaron movimientos conciliados en banco");

		//Buscar los datos para mostrarlos
		$sql="select c.depe_id, c.comp_fec as fecha, b.ctab_numero as nro_cuenta, c.comp_id as referencia, r.rcomp_debe as debe, r.rcomp_haber as haber from sai_comp_diario c, sai_reng_comp r, sai_ctabanco b where b.cpat_id=r.cpat_id and r.comp_id=c.comp_id and r.comp_id like 'codi-%' and r.cpat_id like '1.1.1.01.02%' and r.cpat_id not like '1.1.1.01.02.02.04%' and c.esta_id<>15 and r.comp_id='".$codigo."'";
		$resultado=pg_query($conexion,$sql);
		if ($row=pg_fetch_array($resultado)) {
			$codi_depe_id = trim($row['depe_id']);
			$codi_fecha = trim($row['fecha']);
			$codi_nro_cuenta = trim($row['nro_cuenta']);
			$codi_referencia = trim($row['comp_id']);
			$codi_debe = trim($row['debe']);	
			$codi_haber = trim($row['haber']);
		} //end if

		$sql_caus = "SELECT * FROM sai_insert_saldo_codi_conci('".$codigo."','".$fecha."','".$codi_nro_cuenta."','".$codi_referencia."',".$codi_debe.",".$codi_haber.") AS resultado";	
		if ($codigos == "") $codigos = $codigo;
		else $codigos = $codigos . ", ". $codigo;
		$resultado_set = pg_exec($conexion ,$sql_caus) or die("Error al mostrar");
	} //end for
	echo "<div class='normalNegrita' align='center'>Se proces&oacute; satisfactoriamente la conciliaci&oacute;n bancaria de los siguientes documentos: ".$codigos;
} //end if
else echo "<div class='normalNegrita' align='center'>Error: Debe seleccionar al menos un asiento manual para conciliar";
?>
</div>
</body>
</html>
<?php pg_close($conexion);?>