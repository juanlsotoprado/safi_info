<?php
include(dirname(__FILE__) . '/../../../init.php');
require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');//ConstruirAccesosRapidosFechas
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
$form = FormManager::GetForm(FORM_OPERACIONES_EMITIDAS_TESORERIA);


?>
<!DOCTYPE html>
<html> 
<head>
	<title>SAFI</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"></script>
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/ui.min.js';?>"></script>
	<link href="<?=GetConfig("siteURL").'/css/plantilla.css';?>" rel="stylesheet" type="text/css" />
	<link href="<?=GetConfig("siteURL").'/css/safi0.2.css';?>" rel="stylesheet" type="text/css" />
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
  <th colspan="2">Operaciones emitidas en tr&aacute;nsito y/o conciliadas</th>
	</tr>
	<tr>
	  <td height="5" colspan="3" align="right" style="padding-right: 25px;">
	  <!-- Agregar los accesos rapidos de las fechas (Hoy, ayer, semana, semana pasada, etc.) -->
		<?php VistaFechas::ConstruirAccesosRapidosFechas("fechaInicio", "fechaFin", "dd/mm/yy") ?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Fecha del movimiento:</td>
		<td class="normalNegrita" colspan="2">
			<input type="text" size="10"
				id="fechaInicio" name="fechaInicio" class="dateparse"
				onfocus="javascript: comparar_fechas(document.form.fechaInicio.value, document.form.fechaFin.value);" readonly="readonly"
				value="<?= $form->GetFechaInicio();?>"/>
			<a href="javascript:void(0);" onclick="g_Calendar.show(event,'fechaInicio');" title="Show popup calendar">
				<img src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
			</a>
			<input type="text" size="10"
				id="fechaFin" name="fechaFin" class="dateparse"
				onfocus="javascript: comparar_fechas(document.form.fechaInicio.value, document.form.fechaFin.value);" readonly="readonly"
				value="<?= $form->GetFechaFin()?>"/>
			<a href="javascript:void(0);" onclick="g_Calendar.show(event,'fechaFin');" title="Show popup calendar">
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
		<td class="normalNegrita">Tipo:</td>
		<td>
			<select name="tipo" id="tipo" class="normal">
				<option value="0" <?php if($form->GetTipoBusqueda() == "0"){echo 'selected="selected"';}?>>Operaciones emitidas</option>
				<option value="1" <?php if($form->GetTipoBusqueda() == "1"){echo 'selected="selected"';}?>>Operaciones en tr&aacute;nsito</option>
				<option value="2" <?php if($form->GetTipoBusqueda() == "2"){echo 'selected="selected"';}?>>Libro	banco</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">Tipo de pago:</td>
		<td>
			<select name="tipopago" id="tipopago" class="normal">
				<option value="0" >pgch y tran</option>
				<option value="1" >Pago con cheque</option>
				<option value="2" >Transferencia</option>
			</select>
		</td>
	</tr>
	<tr>
		<td height="44" colspan="2" align="center">
			<input type="hidden" name="hid_validar" id=hid_validar value="1"/>
			<input type="button" value="Buscar" onclick="operacionesEmitidas(1)"/>
			<input type="button" value="Generar PDF" onclick="operacionesEmitidas(0)"/>
		</td>
	</tr>
</table>
</form>
<br/>
<div class="normalNegroNegrita" align="center"><?= $GLOBALS['SafiRequestVars']['titulo'].$GLOBALS['SafiRequestVars']['titulo2'];?></div>
<table class="tabla">
	<tr class="td_gray" align="center">
		<th class="normalNegroNegrita">Sopg/codi</th>
		<th class="normalNegroNegrita">Pgch/tran</th>
		<th class="normalNegroNegrita">N&uacute;mero cuenta</th>
		<th class="normalNegroNegrita">Fecha Conciliaci&oacute;n</th>
		<th class="normalNegroNegrita">N&uacute;mero cheque</th>
		<th class="normalNegroNegrita">Estatus del cheque</th>
		<th class="normalNegroNegrita">Beneficiario</th>
		<th class="normalNegroNegrita">Concepto</th>
		<th class="normalNegroNegrita">Saldo inicial</th>
		<th class="normalNegroNegrita">Cargos (Debe)</th>
		<th class="normalNegroNegrita">Abonos (Haber)</th>
		<th class="normalNegroNegrita">Saldo final</th>
	</tr>
	<tr align="center">
		<td class="normalNegroNegrita">&nbsp;</td>
		<td class="normalNegroNegrita">&nbsp;</td>
		<?php if (strcmp($GLOBALS['SafiRequestVars']['cuentaBancaria'], '-1')==0) 
				$cuenta = "";
			else 
				$cuenta = $GLOBALS['SafiRequestVars']['cuentaBancaria'];
		?>
		<td class="normalNegroNegrita"><?= $cuenta;?></td>
		<td class="normalNegroNegrita"><?= $GLOBALS['SafiRequestVars']['fecha_inicio_antes'];?></td>
		<td class="normalNegroNegrita">&nbsp;</td>
		<td class="normalNegroNegrita">I</td>
		<td class="normalNegroNegrita">&nbsp;</td>
		<td class="normalNegroNegrita">&nbsp;</td>
		<?php
		$saldo_inicial = $GLOBALS['SafiRequestVars']['saldo_banco'];
		$saldo_final = $GLOBALS['SafiRequestVars']['saldo_banco'];
		?>
		<td class="normalNegroNegrita"><?= number_format($GLOBALS['SafiRequestVars']['saldo_banco'],2,',','.');?></td>
		<td class="normalNegroNegrita">&nbsp;</td>
		<td class="normalNegroNegrita">&nbsp;</td>
		<td class="normalNegroNegrita"><?= number_format($GLOBALS['SafiRequestVars']['saldo_banco'],2,',','.');?></td>
	</tr>	
     <?php
     if(is_array($GLOBALS['SafiRequestVars']['listaDocumentos'])){
     foreach ($GLOBALS['SafiRequestVars']['listaDocumentos'] as $listaDocumento) {
     ?>
     <tr class="normal" align="left">
		<td><?php echo $listaDocumento["sopg_id"] ;?></td>
        <td><?php echo $listaDocumento["pago_id"] ;?></td>
        <td><?php echo $listaDocumento["nro_cuenta_bancaria"];?></td>        
        <td><?php echo $listaDocumento["fecha_pagado"] ;?></td>
		<td><?php echo $listaDocumento["referencia"] ;?></td>        
        <td><?php echo $listaDocumento["condicion"];?></td>
        <td><?php echo $listaDocumento["beneficiario"];?></td>
        <td><?php echo $listaDocumento["comentario"];?></td>
        <td><?php echo number_format($saldo_inicial,2,',','.');?></td>
		<?php
		 if ($listaDocumento["monto"] > 0) {
		 	$monto = $listaDocumento["monto"] * -1;
		 	$saldo_inicial = $saldo_inicial + $monto;
		 	$saldo_final = $saldo_final + $monto;
		 } else {
		 	$monto="";
		 	$saldo_inicial = $saldo_inicial - $listaDocumento["monto"];
		 	$saldo_final = $saldo_final - $listaDocumento["monto"];
		 }
		?>
		<td class="normal"><?= number_format($monto*-1,2,',','.');?></td>
	<?php
	 	if ($listaDocumento["monto"]<0) $monto = $listaDocumento["monto"];
	 	else $monto = "";
	?>
		<td class="normal"><?= number_format($monto*-1,2,',','.');?></td>
		<td class="normal"><?= number_format($saldo_final,2,',','.');?></td>        

	</tr>
    <?php }} ?>
</table>
</body>
</html>