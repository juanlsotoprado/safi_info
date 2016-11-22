<?php
include(dirname(__FILE__) . '/../../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
$form = FormManager::GetForm(FORM_SALDOS_CORRECTOS_TESORERIA);
?>
<!DOCTYPE html>
<html> 
<head>
	<title>SAFI</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"></script>
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/funciones.js';?>"></script>
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/actb.js';?>"></script>
	<?php require(SAFI_JAVASCRIPT_PATH.'/init.php'); ?>
	<?php require(SAFI_INCLUDE_PATH.'/fechaJs.php'); ?>
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/reportesTesoreria.js'?>"></script>
</head>

<body>
<form name="form" id="form" action="" method="post"  accept-charset=utf-8>
  <table class="tablaPequena">
<tr> 
  <th colspan="2">Saldos correctos</th>
</tr>
	<tr>
		<td class="normalNegrita">Fecha del movimiento:</td>
		<td class="normalNegrita" colspan="2">
			<input type="text" size="10"
				id="fecha" name="fecha" class="dateparse"
				 readonly="readonly"
				value="<?= $form->GetFecha();?>"/>
			<a href="javascript:void(0);" onclick="g_Calendar.show(event,'fecha');" title="Show popup calendar">
				<img src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
			</a>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">Cuenta bancaria:</td>
		<td>
			<select name="cuentaBancaria" class="normal" id="cuentaBancaria">
			 <option value="-1">Seleccione</option>
 			<?php
 				$selected = "";
  				if(is_array($GLOBALS['SafiRequestVars']['cuentasBancarias'])){
	  				foreach ($GLOBALS['SafiRequestVars']['cuentasBancarias'] AS $cuentaBancaria){
						if ($form->GetCuentaBancaria() != null) {
	  						if($form->GetCuentaBancaria()->GetId() == $cuentaBancaria->GetId()) $selected = "selected=selected";
	  						else $selected = null;
	  					}	
	  					
	  					?>
	  						<option value="<?php echo $cuentaBancaria->GetId()?>" <?php echo $selected;?>><?php echo $cuentaBancaria->GetId()." - ".$cuentaBancaria->GetDescripcion();?></option>
	  					<?php 
	  				}
  				}
  			?>			 
 			</select>
 		</td>
	</tr>
	<tr>
		<td height="44" colspan="2" align="center">
			<input type="hidden" name="hid_validar" id=hid_validar value="1"/>
			<input type="button" value="Buscar" onclick="saldosCorrectos(1)"/>
			<input type="button" value="Generar PDF" onclick="saldosCorrectos(0)"/>
		</td>
	</tr>
</table>
</form>
<br/>
<?php 	if ($form->GetCuentaBancaria() != null) {?>
<div class="normalNegroNegrita" align="center">SALDOS CORRECTOS CONTABLES PARA LA CUENTA NRO.<?= $form->GetCuentaBancaria()->GetId()?> AL<?php echo $form->GetFecha();?></div>
<?php }?>
<table class="tabla">
	<tr class="td_gray" align="center">
		<th class="normalNegroNegrita">Afectaci&oacute;n</th>
		<th class="normalNegroNegrita">N&ordm; Referencia</th>
		<th class="normalNegroNegrita">Documento</th>
		<th class="normalNegroNegrita">Beneficiario</th>
		<th class="normalNegroNegrita">Parcial</th>
		<th class="normalNegroNegrita">Contabilidad</th>
		<th class="normalNegroNegrita">Banco</th>
	</tr>
	<tr align="center">
		<td class="normalNegroNegrita"><?=$fechaFfin;?></td>
		<td class="normalNegroNegrita">&nbsp;</td>
		<td class="normalNegroNegrita">&nbsp;</td>
		<td class="normalNegroNegrita">&nbsp; </td>
		<td class="normalNegroNegrita">&nbsp; </td>
		<td class="normalNegroNegrita" align="right"><?php echo number_format($_SESSION['resultados'],2,',','.');?></td>					
		<td class="normalNegroNegrita" align="right"><?php echo number_format($_SESSION['saldo_banco']*-1,2,',','.');?></td>
	</tr>	
	<?
	$sumatoria_contabilidad = $_SESSION['resultados'];
	$sumatoria_banco = $_SESSION['saldo_banco']*-1;	
	?>	
	<tr>
		<td>&nbsp;</td>
		<td class="normalNegroNegrita" align="center" colspan="4">Cheques/Transferencias en Tr&aacute;nsito</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>				
	</tr>
<?php
if(is_array($GLOBALS['SafiRequestVars']['listaDocumentos']['cheques_transito'])){
 foreach ($GLOBALS['SafiRequestVars']['listaDocumentos']['cheques_transito'] as $listaDocumento) {
	$sumatoria_cheq_transito_banco += $listaDocumento['monto'];
	if (strcmp(trim($listaDocumento['fecha_mes2']),substr(trim($listaDocumento['fecha']),3,7))==0) 
		$sumatoria_cheq_mes_transito += $listaDocumento['monto'];
?>
	<tr>
	<td align="left" class="normal"><?php echo $listaDocumento['fecha'];?></td>
	<td align="left" class="normal"><?php echo $listaDocumento['referencia'];?></td>
	<td align="left" class="normal"><?php echo $listaDocumento['id_documento'];?></td>
	<td align="left" class="normal"><?php echo $listaDocumento['beneficiario'];?></td>
	<td align="right" class="normal"><?php echo number_format($listaDocumento['monto'],2,',','.');?></td>
	<td align="right" class="normal">&nbsp;</td>
	<td align="right" class="normal">&nbsp;</td>
	</tr>
<?php }} ?>
	<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td class="normalNegroNegrita" align="right"><?php echo number_format($sumatoria_cheq_mes_transito*-1,2,',','.');?></td>
	<td class="normalNegroNegrita" align="right"><?php echo number_format($sumatoria_cheq_transito_banco,2,',','.');?></td>
	</tr>
<?php 
	$sumatoria_contabilidad += $sumatoria_cheq_mes_transito*-1;
	$sumatoria_banco += $sumatoria_cheq_transito_banco;	
?>
	<tr>
	<td>&nbsp;</td>
	<td class="normalNegroNegrita" align="center" colspan="4">Dep&oacute;sitos/D&eacute;bitos en Tr&aacute;nsito</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>				
	</tr>
<?php
if(is_array($GLOBALS['SafiRequestVars']['listaDocumentos']['codi_transito'])){
 foreach ($GLOBALS['SafiRequestVars']['listaDocumentos']['codi_transito'] as $listaDocumento) {
		$sumatoria_codi_transito += $listaDocumento['monto'];
?>
	<tr>
	<td align="left" class="normal"><?php echo $listaDocumento['fecha'];?></td>
	<td align="left" class="normal"><?php echo $listaDocumento['referencia'];?></td>
	<td align="left" class="normal"><?php echo $listaDocumento['id_documento'];?></td>
	<td align="left" class="normal"><?php echo $listaDocumento['comentario'];?></td>
	<td align="right" class="normal"><?php echo number_format($listaDocumento['monto'],2,',','.');?></td>
	<td align="left" class="normal">&nbsp;</td>
	<td align="left" class="normal">&nbsp;</td>
	</tr>
<?php }} ?>
	<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td class="normalNegroNegrita" align="right"><?php echo number_format($sumatoria_codi_transito,2,',','.');?></td>
	<td class="normalNegroNegrita" align="right"><?php echo number_format($sumatoria_codi_transito*-1,2,',','.');?></td>
	</tr>
<?php 
	$sumatoria_contabilidad += $sumatoria_codi_transito;
	$sumatoria_banco += $sumatoria_codi_transito*-1;	
?>	
	<tr>
	<td>&nbsp;</td>
	<td class="normalNegroNegrita" align="center" colspan="4">Dep&oacute;sitos/D&eacute;bitos conciliados</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>				
	</tr>
<?php
//$sql_codi_conciliado;
if(is_array($GLOBALS['SafiRequestVars']['listaDocumentos']['codi_conciliado'])){
 foreach ($GLOBALS['SafiRequestVars']['listaDocumentos']['codi_conciliado'] as $listaDocumento) {
	if (strcmp(trim($fecha_mes2),substr(trim($row["fecha_emision"]),3,7))==0)
		 $monto_cambiado = $listaDocumento['monto']*-1;
	else $monto_cambiado = 0;
	$sumatoria_codi_conciliado_contabilidad += $monto_cambiado*-1;		
	$sumatoria_codi_conciliado_banco += $listaDocumento['monto'];		
?>
	<tr>
	<td align="left" class="normal"><?php echo $listaDocumento['fecha'];?></td>
	<td align="left" class="normal"><?php echo $listaDocumento['referencia'];?></td>
	<td align="left" class="normal"><?php echo $listaDocumento['id_documento'];?></td>
	<td align="left" class="normal"><?php echo $listaDocumento['comentario'];?></td>
	<td align="left" class="normal">&nbsp;</td>
	<td align="right" class="normal"><?php echo number_format($monto_cambiado,2,',','.');?></td>
	<td align="right" class="normal"><?php echo number_format($listaDocumento['monto']*-1,2,',','.');?></td>
	</tr>
<?php }} ?>	
	<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td class="normalNegroNegrita" align="right"><?php echo number_format($sumatoria_codi_conciliado_contabilidad,2,',','.');?></td>
	<td class="normalNegroNegrita" align="right"><?php echo number_format($sumatoria_codi_conciliado_banco*-1,2,',','.');?></td>
	</tr>
<?php 
	$sumatoria_contabilidad += $sumatoria_codi_conciliado_contabilidad;
	$sumatoria_banco += $sumatoria_codi_conciliado_banco*-1;	
?>	
	<tr>
	<td>&nbsp;</td>
	<td class="normalNegroNegrita" align="center" colspan="4">Cheques/Transferencias conciliados</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>				
	</tr>
<?php
if(is_array($GLOBALS['SafiRequestVars']['listaDocumentos']['cheques_conciliados'])){
	foreach ($GLOBALS['SafiRequestVars']['listaDocumentos']['cheques_conciliados'] as $listaDocumento) {
		$sumatoria_cheq_conciliado_banco += $listaDocumento['monto'];	
?>
	<tr>
	<td align="left" class="normal"><?php echo $listaDocumento["fecha"];?></td>
	<td align="left" class="normal"><?php echo $listaDocumento['referencia'];?></td>
	<td align="left" class="normal"><?php echo $listaDocumento['id_documento'];?></td>
	<td align="left" class="normal"><?php echo $listaDocumento['beneficiario'];?></td>
	<td align="left" class="normal">&nbsp;</td>
	<?php 
	if ((strcmp(trim($listaDocumento['fecha_mes2']),substr(trim($listaDocumento["fecha_emision"]),3,7))==0)) 
		if (strlen($listaDocumento["fecha_anulacion"])>2) {
			if ((strcmp(trim($listaDocumento['fecha_mes2']),substr(trim($listaDocumento["fecha_anulacion"]),3,7))==0))
				$monto_contabilidad = $listaDocumento["monto_contable"]*-1;
			else	
				$monto_contabilidad = 0;
		}
		else	
			$monto_contabilidad = $listaDocumento["monto_contable"]*-1;
	else	
		$monto_contabilidad = 0;
	
		$sumatoria_cheq_conciliado_contabilidad += $monto_contabilidad;	
	?>		
	<td align="right" class="normal"><?php echo number_format($monto_contabilidad,2,',','.');?></td>
	<td align="right" class="normal"><?php echo number_format($listaDocumento['monto'],2,',','.');?></td>
	</tr>
	<?php }} ?>
	<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td class="normalNegroNegrita" align="right"><?php echo number_format($sumatoria_cheq_conciliado_contabilidad,2,',','.');?></td>
	<td class="normalNegroNegrita" align="right"><?php echo number_format($sumatoria_cheq_conciliado_banco,2,',','.');?></td>
	</tr>
<?php 
	$sumatoria_contabilidad += $sumatoria_cheq_conciliado_contabilidad;
	$sumatoria_banco += $sumatoria_cheq_conciliado_banco;	
?>
	<tr>
	<td>&nbsp;</td>
	<td class="normalNegroNegrita" align="center" colspan="4">Cheques en Tr&aacute;nsito Anulados</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>				
	</tr>
<?php
if(is_array($GLOBALS['SafiRequestVars']['listaDocumentos']['cheques_anulados'])){
	foreach ($GLOBALS['SafiRequestVars']['listaDocumentos']['cheques_anulados'] as $listaDocumento) {
		$sumatoria_cheq_anulado += $listaDocumento['monto'];
	
?>
	<tr>
	<td align="left" class="normal"><?php echo $listaDocumento['fecha'];?></td>
	<td align="left" class="normal"><?php echo $listaDocumento['referencia'];?></td>
	<td align="left" class="normal"><?php echo $listaDocumento['id_documento'];?></td>
	<td align="left" class="normal"><?php echo $listaDocumento['beneficiario'];?></td>
	<td align="right" class="normal"><?php echo number_format($listaDocumento['monto']*-1,2,',','.');?></td>
	<td align="left" class="normal">&nbsp;</td>
	<td align="left" class="normal">&nbsp;</td>
	</tr>
<?php 
}
}
?>
	<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td class="normalNegroNegrita" align="right"><?php echo number_format($sumatoria_cheq_anulado,2,',','.');?></td>
	<td class="normalNegroNegrita" align="right"><?php echo number_format($sumatoria_cheq_anulado*-1,2,',','.');?></td>
	</tr>
<?php 
	$sumatoria_contabilidad += $sumatoria_cheq_anulado;
	$sumatoria_banco += $sumatoria_cheq_anulado*-1;	
?>
	<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td class="normalNegroNegrita" align="right"><?php echo number_format($sumatoria_contabilidad,2,',','.');?></td>
	<td class="normalNegroNegrita" align="right"><?php echo number_format($sumatoria_banco,2,',','.');?></td>
	</tr>	
</table> 
</body>
</html>