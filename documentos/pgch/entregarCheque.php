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

$idSopg = $_REQUEST["pgch"];
$sql="select pgch.pgch_id as pgch_id, pgch_asunto as asunto, pgch.pgch_obs as observaciones, ch.id_cheque as id_cheque, ch.nro_cheque as nro_cheque, ch.monto_cheque as monto_cheque, ch.beneficiario_cheque as beneficiario_cheque,  ch.ci_rif_beneficiario_cheque as id_beneficiario, cq.ctab_numero as cuenta_bancaria, b.banc_nombre as banco
from sai_pago_cheque pgch, sai_cheque ch, sai_ctabanco cb, sai_banco b, sai_chequera cq
where pgch.docg_id=ch.docg_id and ch.estatus_cheque=45 and ch.nro_chequera = cq.nro_chequera and cq.banc_id=b.banc_id
and cb.ctab_numero=cq.ctab_numero and pgch.id_nro_cheque=ch.id_cheque and pgch.docg_id='".$idSopg."'"; 
$resultado=pg_query($conexion,$sql);
if ($row=pg_fetch_array($resultado)) {
	$idPgch = trim($row['pgch_id']);
	$numeroCuenta = trim($row['cuenta_bancaria']);
	$asunto = trim($row['pgch_asunto']);
	$observaciones = trim($row['observaciones']);
	$numeroCheque = trim($row['nro_cheque']);
	$idCheque = trim($row['id_cheque']);	
	$montoCheque = number_format(trim($row['monto_cheque']),2,',','.');
	$montoChequeSF = $row['monto_cheque']; //Sin formato
	$beneficiarioId = trim($row['id_beneficiario']);
	$beneficiarioCheque = trim($row['beneficiario_cheque']);
	$banco = trim($row['banco']);
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

function revisar() {
	var mensaje = "";
	mensaje = "Est\u00e1 seguro que desea entregar el cheque?";
	if (confirm(mensaje)) { 
		document.form.action= "entregarChequeAccion.php?pg=<?php echo $idPgch; ?>&beneficiario="+document.form.beneficiarioCheque.value+"&observacion="+document.form.obs_entrega.value;
		document.form.submit();
	}
	else return;
}

function monto_en_letras(monto_1) {
	var monto= number_format(monto_1,2,'.','');	
  	ver_monto_letra(monto ,'txt_monto_letras','');
}
</script>
</head>
<body
	onload="monto_en_letras(<?php echo str_replace(",",".",str_replace(".","",$montoCheque));?>)">
<form name="form" method="post" action="">
<table width="90%" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" align="center">
	<tr class="td_gray">
		<td colspan="2" class="normalNegroNegrita">ENTREGAR CHEQUE</td>
	</tr>
	<tr>
		<td class="normalNegrita">Documento asociado:</td>
		<td class="normal"><a
			href="javascript:abrir_ventana('../sopg/sopg_detalle.php?codigo=<?php echo trim($idSopg);?>')"
			class="link"><?php echo $idSopg;?></a>
			<input type="hidden" value="<?php echo $idSopg; ?>" name="idSopg" id="idSopg"></input>			
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">N&uacute;mero de cheque:</td>
		<td class="normal"><?php echo $numeroCheque;?>
		<input type="hidden" value="<?php echo $numeroCheque; ?>" name="numeroCheque" id="numeroCheque"></input>		
		<input type="hidden" value="<?php echo $idCheque; ?>" name="idCheque" id="idCheque"></input>
		
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">N&uacute;mero de cuenta:</td>
		<td class="normal"><?php echo $numeroCuenta;?>
		<input type="hidden" value="<?php echo $numeroCuenta; ?>" name="numeroCuenta" id="numeroCuenta"></input>		
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">Banco:</td>
		<td class="normal"><?php echo $banco;?></td>
		<input type="hidden" value="<?php echo $banco; ?>" name="banco" id="banco"></input>		
	</tr>
	<tr>
		<td class="normalNegrita">Beneficiario:</td>
		<td class="normal">
		<input type="text" value="<?php echo $beneficiarioCheque; ?>" name="beneficiarioCheque" id="beneficiarioCheque" size="50"></input>		
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">CI o RIF del Beneficiario:</td>
		<td class="normal"><?php echo $beneficiarioId;?>
		<input type="hidden" value="<?php echo $beneficiarioId; ?>" name="beneficiarioId" id="beneficiarioId"></input>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">Monto Bs.:</td>
		<td class="normal"><?php echo $montoCheque; ?>
		<input type="hidden" value="<?php echo $montoChequeSF; ?>" name="montoCheque" id="montoCheque"></input>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">Monto en Letras:</td>
		<td class="normal"><textarea class="peq" rows="2"
			cols="80" id="txt_monto_letras" name="txt_monto_letras"
			readonly="readonly"></textarea></td>
	</tr>
	<tr>
		<td class="normalNegrita">Concepto del Pago:</td>
		<td class="normal"><?php echo $asunto;?></td>
		<input type="hidden" value="<?php echo $asunto; ?>" name="asunto" id="asunto"></input>		
	</tr>
	<tr>
		<td class="normalNegrita">Observaciones del Pago:</td>
		<td class="normal"><?php echo $observaciones;?></td>
		<input type="hidden" value="<?php echo $observaciones; ?>" name="observaciones" id="observaciones"></input>		
	</tr>
	<tr>
		<td colspan="2"><input type="hidden" name="idSopg"	value="<?php echo $idSopg;?>" /></td>
	</tr>
	<tr>
		<td width="18%" class="normalNegroNegrita">Observaci&oacute;n de la entrega</td>
		<td>
<input type="text" name="obs_entrega"></input></td>
	</tr>
	<tr align="center">
		<td colspan="2" class="normal">
		<div align="center"><br></br><input type="button" value="Entregar"
			onClick="javascript:revisar();" /></div>
		</td>
	</tr>
</table>
</form>
</body>
</html>
