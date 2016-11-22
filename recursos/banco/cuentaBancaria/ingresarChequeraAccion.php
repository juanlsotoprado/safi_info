<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();    exit;
}
ob_end_flush(); 
/**********************************/
$txt_cuenta=trim($_POST['txt_cuenta']);
$txt_banc_id=trim($_POST['txt_banc_id']);
$txt_cheq_inicio=trim($_POST['txt_cheq_inicio']);
$txt_cheq_fin=trim($_POST['txt_cheq_fin']);
$ceros=trim($_POST['resta']);
$cantidad=$txt_cheq_fin-$txt_cheq_inicio+1;

/******Validar no existan cheques en esa cuenta con los mismos numeros******/
$contador = 0;
for ($i=$txt_cheq_inicio;$i<=$txt_cheq_fin;$i++) { 
	$sql_cheque="select * from sai_buscar_cheque_igual ('$i','$txt_cuenta') as resultado_set(text)";
	$result_cheque=pg_query($conexion,$sql_cheque) or die("ERROR: No se pudo realizar la Consulta $sql_cheque"); 
	if ($r=pg_fetch_array($result_cheque)) {
   		$v=trim($r['text']);
   		if ($v=='1')  $contador=$contador+1;
	}
}
if ($contador>0) {
 //consiguio cheques?>
<script language="javascript">
		alert("Ya existe otra chequera de la cuenta:<?=$txt_cuenta?> con la misma numeraci\u00f3n de cheques");
		window.location="buscarCuenta.php"
</script>
<?
}
else {
/************Insertar chequera**********************/
$sql="select * from sai_insert_chequeras ('".$cantidad."','".$txt_banc_id."','1','".$txt_cuenta."') as resultado_set(text)";
$result=pg_query($conexion,$sql) or die("ERROR: No se pudo realizar la Consulta $sql"); 
/**********************************/
$sql_max="select nro_chequera as cod from sai_chequera where ctab_numero='".$txt_cuenta."' order by oid desc";
$result_max=pg_query($conexion,$sql_max) or die("ERROR: No se pudo realizar la Consulta $sql_max"); 
$row_max=pg_fetch_array($result_max);
if($row_max["cod"]!="")	$nro_chequera=$row_max["cod"];
/**********************************/
$numCheque="";
$cont=0;
for ($i=$txt_cheq_inicio;$i<=$txt_cheq_fin;$i++) {
	if ($cont<1) $numCheque = $i;
	else $numCheque = $ceros.$i;
	$sql_cheque="select * from sai_insert_cheque_correlativo ('".$numCheque."','1','$nro_chequera') as resultado_set(text)";
	$result_cheque=pg_query($conexion,$sql_cheque) or die("ERROR: No se pudo realizar la Consulta $sql_cheque"); 
	if ($r=pg_fetch_array($result_cheque)) {
   		$v=trim($r['text']);
   		if ($v=='0')  {
?>
		<script language="javascript">
			alert("La chequera que introdujo ya se encuentra registrada");
			window.location="buscarCuenta.php"
		</script>
<?php }//fin del if 
	}//fin del if
$cont++;}//fin del for
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>SAFI: Ingresar Chequera</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="javascript">

function regresar() {
	history.back();
}
</script>
<script language="JavaScript" src="../contabilidad/pantallas16/includes/js/funciones.js"> </SCRIPT>
</head>
<body>
<table width="60%" align="center" background="../../../imagenes/fondo_tabla.gif" bgcolor="#FFFFFF" class="tablaalertas" >
<tr class="td_gray">
  <td colspan="2" class="normalNegroNegrita">Registro de chequera</td>
</tr>
<?php
$sql="SELECT cb.cpat_id, to_char(cb.ctab_fechacierrereg,'DD-MM-YYYY') as fecha_cierre_sistema, to_char(cb.ctab_fechaapert,'DD-MM-YYYY') as fecha_apertura, to_char(cb.ctab_fechareg,'DD-MM-YYYY') as fecha_registro, to_char(cb.ctab_fechacierrereg,'DD-MM-YYYY') as fecha_cierre, cb.ctab_numero, cb.banc_id, b.banc_nombre, e.esta_nombre, cb.ctab_ano, cb.ctab_descripcion, cb.ctab_estatus, tc.tipo_nombre, cb.tipo_id, ctab_saldoinicial from sai_ctabanco cb, sai_banco b, sai_estado e, sai_tipocuenta tc where cb.banc_id=b.banc_id and cb.tipo_id=tc.tipo_id and cb.ctab_estatus=e.esta_id and ctab_numero='".$txt_cuenta."'";
$resultado=pg_query($conexion,$sql);

if($row=pg_fetch_array($resultado)) {
	$banco=trim($row['banc_nombre']);
	$ctab_numero=trim($row['ctab_numero']);
	$cpat_id=trim($row['cpat_id']);
	$ctab_descripcion=trim($row['ctab_descripcion']);
	$fecha_apertura=trim($row['fecha_apertura']);
	$fecha_registro=trim($row['fecha_registro']);
	$fecha_cierre=trim($row['fecha_cierre']);
	$fecha_cierre_sistema=trim($row['fecha_cierre_sistema']);	
	$tipo_cuenta=trim($row['tipo_nombre']);
	$saldo=trim($row['ctab_saldoinicial']);
	$estado=$row['esta_nombre']; ?>
    <tr class="normal">
      <td class="normalNegrita">Banco:</td>
      <td><?php echo $banco;?></td>
    </tr>
    <tr class="normal">
      <td class="normalNegrita">N&uacute;mero de cuenta:</td>
      <td><?php echo $ctab_numero;?></td>
    </tr>
    <tr class="normal">
      <td class="normalNegrita">Cuenta contable:</td>
      <td><?php echo $cpat_id;?></td>
    </tr>
    <tr class="normal">
      <td class="normalNegrita">Descripci&oacute;n:</td>
      <td ><?php echo $ctab_descripcion;?></td>
    </tr>
    <tr class="normal">
      <td class="normalNegrita">Tipo de cuenta:</td>
      <td><?php echo $tipo_cuenta;?></td>
    </tr>
    <tr class="normal">
      <td class="normalNegrita">Monto de apertura:</td>
      <td><?php echo number_format($saldo,2,'.',',');?></td>
    </tr>
    <tr class="normal">
      <td class="normalNegrita">Fecha de registro:</td>
      <td><?php echo $fecha_registro;?></td>
    </tr>
    <tr class="normal">
      <td class="normalNegrita">Fecha de apertura:</td>
      <td><?php echo $fecha_apertura;?></td>
    </tr>
	<tr class="normal">
  <td class="normalNegrita">Cantidad de cheques:</td>
  <td><?php echo $cantidad; ?></td>
  </tr>
<?php } ?>
</table>
</body>
</html>
<?php } pg_close($conexion); ?>