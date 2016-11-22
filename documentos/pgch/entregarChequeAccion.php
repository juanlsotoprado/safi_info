<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ) {
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();

$ano = $_SESSION['an_o_presupuesto'];
$user_depe_id = substr($_SESSION['user_perfil_id'],2,3);
$motivo = $_REQUEST["motivo"];
$idPgch = $_REQUEST["pg"]; //Id del pgch
$otroCheque = $_REQUEST["otro"];
$sopg =  trim($_POST['idSopg']); 
$idCheque =  trim($_POST['idCheque']);
$numeroCheque =  trim($_POST['numeroCheque']); 
$numeroCuenta = trim($_POST['numeroCuenta']);
$observaciones = trim($_POST['observaciones']);
$obs_entrega = trim($_POST['obs_entrega']);
$asunto = trim($_POST['asunto']);
$banco = trim($_POST['banco']);
$beneficiarioCheque = trim($_POST['beneficiarioCheque']);
$beneficiarioId = trim($_POST['beneficiarioId']);
$montoCheque = trim($_POST['montoCheque']);
$error=1;


/*Validar si el pago esta entregado*/
$conciliado="";
$sql="select id_cheque from cheque_entrega where id_cheque='".$idCheque."'";
$resultado=pg_query($conexion,$sql);
if ($row=pg_fetch_array($resultado)) {
	$entregado = trim($row['id_cheque']);
}

if (strlen($entregado)<2) {
	$fecha = date("Y/m/d H:i:s");	
	$error=0;
	$sql =  "INSERT into cheque_entrega (id_cheque, beneficiario, observacion, fecha) values ('".$idCheque."','".$beneficiarioCheque."', '".$obs_entrega."', '".$fecha."')";
	//$sql = "SELECT * FROM insertar_cheque_entrega (id_cheque, beneficiario, observacion) values ('".$idCheque."','".$beneficiarioCheque."', '".$obs_entrega."') as resultado ";
	$resultado = pg_exec($conexion ,$sql) or die("Error al intentar registrar la entrega del cheque");
	$mensaje = "El cheque ".$cheque_numero." ha sido registrado como entregado";			
}
	


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css"
	media="all" />
<title>.:SAFI: Entregar Cheque</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script language="JavaScript" src="../../js/func_montletra.js"> </script>
<script language="JavaScript" type="text/JavaScript">
function monto_en_letras(monto_1) {
	var monto= number_format(monto_1,2,'.','');	
  	ver_monto_letra(monto ,'txt_monto_letras','');
}
</script>
</head>
<body
	onload="monto_en_letras(<?php echo str_replace(",",".",str_replace(".","",$cheque_monto));?>)">
<form name="form" method="post" action="">
<table width="80%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray">
		<td colspan="2" class="normalNegroNegrita">ENTREGAR CHEQUE</td>
	</tr>
	<tr>
		<td colspan="2" align="center" class="normalNegrita">
				<?
				$condicion = "";
				if ($error==1) $condicion = "NO ";
				?>
				El cheque <? echo $numeroCheque;?><?=$condicion;?> ha
				sido registrado como entregado
			</td>
	</tr>
	<tr>
		<td class="normalNegrita">Documento asociado:</td>
		<td class="normal"><a
			href="javascript:abrir_ventana('../sopg/sopg_detalle.php?codigo=<?php echo trim($sopg);?>')"
			class="link"><?php echo $sopg;?></a>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">N&uacute;mero de cheque:</td>
		<td class="normal"><?php echo $numeroCheque;?></td>
	</tr>
	<tr>
		<td class="normalNegrita">N&uacute;mero de cuenta:</td>
		<td class="normal"><?php echo $numeroCuenta;?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Banco:</td>
		<td class="normal"><?php echo $banco;?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Beneficiario:</td>
		<td class="normal"><?php echo $beneficiarioCheque;?></td>
	</tr>
	<tr>
		<td class="normalNegrita">CI o RIF del Beneficiario:</td>
		<td class="normal"><?php echo $beneficiarioId;?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Monto Bs.:</td>
		<td class="normal"><?php echo $montoCheque; ?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Concepto del Pago:</td>
		<td class="normal"><?php echo $asunto;?></td>
	</tr>
	<tr>
		<td class="normalNegrita" width="18%" >Observaciones del Pago:</td>
		<td class="normal"><?php echo $observaciones;?></td>
	</tr>
	<tr>
		<td class="normalNegrita" width="18%" >Motivo:</td>
		<td class="normal"><?php echo $motivo;?></td>
	</tr>	
</table>
<br></br>
<div align="center" class="normalNegrita_naranja">
	<?php echo $mensaje; ?>
	<br /></br><br></br>
	<a href="javascript:window.print()"><img
		src="../../imagenes/boton_imprimir.gif" width="23" height="20"
		border="0" alt="Imprimir Detalle" /></a>
</div>
</form>
</body>
</html>
<?php pg_close($conexion);?>