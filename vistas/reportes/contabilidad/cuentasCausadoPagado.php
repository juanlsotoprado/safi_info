<?php
include(dirname(__FILE__) . '/../../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
$form = FormManager::GetForm(FORM_CUENTA_BANCARIA_PAGADO_CONTABILIDAD);
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
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/reportesContabilidad.js'?>"></script>
</head>

<body>
<form name="form" id="form" action="" method="post">
	<table class="tablaPequena">
		<tr> 
		  <th colspan="2">Movimientos consolidados causados/pagados por cuenta bancaria</th>
		</tr>
		<tr>
			<td class="normalNegrita">Cuenta bancaria:</td>
			<td>
				<select name="cuenta" class="normal" id="cuenta">
			 		<option value="-1" selected="selected">Seleccione</option>
 					<?php
 						$selected = "";
  						if(is_array($GLOBALS['SafiRequestVars']['cuentasBancarias'])){
	  						foreach ($GLOBALS['SafiRequestVars']['cuentasBancarias'] AS $cuentaBancaria){
								if (isset($form) && $form->GetCuentaBancaria() != null) {
									if (strcmp($form->GetCuentaBancaria()->GetId(),$cuentaBancaria->GetId())==0)
										$selected = "selected";
									else  $selected = "";
								}
								?>
	  								<option value="<?php echo $cuentaBancaria->GetId()?>"<?php echo $selected?>><?php echo $cuentaBancaria->GetId()." - ".$cuentaBancaria->GetDescripcion()?></option>
	  							<?php 	
	  						}
  						}
  					?>			 
 				</select>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">Rango de fechas:</td>
			<td class="normalNegrita">
				<!-- <table>
				<tr>
				<td>
				<div id="fecha" class="normal"  style="display:none">-->
					<input type="text" size="10" id="fecha_inicio" name="fecha_inicio" class="dateparse" readonly="readonly" value="<?php echo $form->GetfechaInicio();?>"/>
					<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha_inicio');" 	title="Show popup calendar" >
						<img src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
					</a>
				<!-- </div>-->	

					<input type="text" size="10" id="fecha_fin" name="fecha_fin" class="dateparse"	readonly="readonly" value="<?php echo $form->GetfechaFin();?>"/>
					<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha_fin');" title="Show popup calendar" >
						<img src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
					</a>

					<!-- </td>
					</tr>
					</table>-->	
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">Tipo de solicitud pago:</td>
			<td>
				<select name="tipoSolicitudPago" class="normal" id="tipoSolicitudPago">
			 		<option value="-1" selected="selected">Seleccione</option>
 					<?php
 						$selected = "";
  						if(is_array($GLOBALS['SafiRequestVars']['tipoSolicitudPago'])){
	  						foreach ($GLOBALS['SafiRequestVars']['tipoSolicitudPago'] AS $tipoSolicitudPago){
								if (isset($form) && $form->GetTipoSolicitudPago() != null) {
									if (strcmp($form->GetTipoSolicitudPago()->GetId(),$tipoSolicitudPago->GetId())==0)
										$selected = "selected";
									else  $selected = "";
								}
	  							?>
	  								<option value="<?=$tipoSolicitudPago->GetId()?>" <?=$selected?>><?php echo $tipoSolicitudPago->GetNombre()?></option>
	  							<?	
	  						}
  						}
  					?>			 
 				</select>
			</td>
		</tr>	
		<tr>
			<td class="normalNegrita">Actividad compromiso:</td>
			<td>
				<select name="tipoActividadCompromiso" class="normal" id="tipoActividadCompromiso">
			 		<option value="-1" selected="selected">Seleccione</option>
 					<?php
 						$selected = "";
  						if(is_array($GLOBALS['SafiRequestVars']['tipoActividadCompromiso'])){
	  						foreach ($GLOBALS['SafiRequestVars']['tipoActividadCompromiso'] AS $tipoActividadCompromiso){
								if (isset($form) && $form->GetTipoActividadCompromiso() != null) {
								if (strcmp($form->GetTipoActividadCompromiso()->GetId(),$tipoActividadCompromiso->GetId())==0)
									 $selected = "selected";
								else  $selected = "";
								}
								?>
	  								<option value="<?php echo $tipoActividadCompromiso->GetId()?>" <?=$selected?>><?=$tipoActividadCompromiso->GetNombre()?></option>
	  							<?php 
	  						}
  						}
  					?>			 
 				</select>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">Detalle solicitud pago:</td>
			<td>
				<input type="text" value="<?php echo $form->GetDetalleSolicitudPago();?>" name="detalleSolicitudPago" id="detalleSolicitudPago"></input>
			</td>
		</tr>												
		
		<tr align="center">
			<td colspan="2">
				<input type="hidden" value="0" name="hid_validar" value="0"/>	
				<input type="button" value="Buscar" onclick="ejecutar2()" /> 
			</td>
		</tr> 
	</table>
</form>

<br/>
<div class="normalNegroNegrita" align="center">
	Movimientos <?php echo $GLOBALS['SafiRequestVars']['descripcion_tipo'] . $GLOBALS['SafiRequestVars']['descripcion_fecha'];?> 
</div>
<table class="tabla">
	<tr>
		<th class="normalNegroNegrita" align="center">Nro.</th>
		<th class="normalNegroNegrita" align="center">Sopg </th>
		<th class="normalNegroNegrita" align="center">CODA/CODI</th>
		<th class="normalNegroNegrita" align="center">Partida/Cuenta  </th>
		<th class="normalNegroNegrita" align="center">Comp </th>
		<th class="normalNegroNegrita" align="center">Tipo sol. </th>		
		<th class="normalNegroNegrita" align="center">Factura</th>
		<th class="normalNegroNegrita" align="center">PROY/ACC</th>
		<th class="normalNegroNegrita" align="center">Fte Financiamiento</th>		
		<th class="normalNegroNegrita" align="center">Fecha</th>		
		<th class="normalNegroNegrita" align="center">N&ordm; </th>	
		<th class="normalNegroNegrita" align="center">Descripci&oacute;n/Concepto del pago</th>
		<th class="normalNegroNegrita" align="center">Monto Bs.</th>		
		<th class="normalNegroNegrita" align="center">IVA </th>
		<th class="normalNegroNegrita" align="center">Monto + IVA </th>		
		<th class="normalNegroNegrita" align="center">IVA Retenido </th>		
		<th class="normalNegroNegrita" align="center">ISLR </th>
		<th class="normalNegroNegrita" align="center">LTF </th>		
		<th class="normalNegroNegrita" align="center">Otras retenciones</th>		
		<th class="normalNegroNegrita" align="center">Fecha</th>				
		<th class="normalNegroNegrita" align="center">Nro. Cheque o Transf.</th>
		<th class="normalNegroNegrita" align="center">Monto Pagado</th>				
     </tr>
     <?php
     $i = 1;
     if(is_array($GLOBALS['SafiRequestVars']['listaMovimientos'])){
     foreach ($GLOBALS['SafiRequestVars']['listaMovimientos'] as $listaMovimiento) {
     ?>
     <tr class="normal" align="left">
		<td><?php echo $i;?></td>
		<td><?php echo $listaMovimiento["documento_sopg"] ;	?>	</td>
		<td><?php echo /*$listaMovimiento["documento_pago"]*/$listaMovimiento["asiento"];?></td>
		<td><?php echo $listaMovimiento["partida_causado"] ;?></td>
		<td><?php echo $listaMovimiento["compromiso"] ;	?>	</td>
		<td><?php echo $listaMovimiento["tipo_solicitud"] ;?></td>
		<td><?php echo $listaMovimiento["factura"] ;?></td>
		<td><?php echo $listaMovimiento["proy_acc"] ;?></td>		
		<td><?php echo $listaMovimiento["fte_financiamiento"] ;?></td>		
		<td><?php echo $listaMovimiento["fecha_causado"] ;?></td>						
		<td><?php echo $listaMovimiento["documento_asociado"] ;?></td>
		<td><?php echo $listaMovimiento["detalle_solicitud"];?></td>
		<td><?php echo number_format($listaMovimiento["monto_causado"],2,',','.');?></td>	
		<td><?php echo number_format($listaMovimiento["iva"],2,',','.') ;?></td>
		<td><?php echo number_format($listaMovimiento["monto_base"]+$listaMovimiento["iva"],2,',','.');?></td>
		<td><?php echo number_format($listaMovimiento["riva"],2,',','.') ;?></td>		
		<td><?php echo number_format($listaMovimiento["rislr"],2,',','.') ;?></td>	
		<td><?php echo number_format($listaMovimiento["rltf"],2,',','.') ;?></td>					
		<td><?php echo number_format($listaMovimiento["rfza"],2,',','.') ;?></td>	
		<td><?php echo $listaMovimiento["fecha_pagado"] ;?></td>	
		<td><?php echo $listaMovimiento["referencia"] ;?></td>			
		<td><?php echo number_format($listaMovimiento["monto_cheque_tran"],2,',','.') ;?></td>				
	</tr>
    <?php $i++;}}?>
	<!-- <tr class="normalNegroNegrita">
		<td colspan="8"><b>Totales (exceptuando partidas 4.11)</b></td>
		<td>&nbsp;<b><?=number_format($listaMovimiento["sum_montoP"],2,',','.');?></b></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><b><?=number_format($listaMovimiento["sum_montoCH"],2,',','.');?></b></td>
		<td>&nbsp;</td>
		<td><b><?=number_format($listaMovimiento["sum_montoLTF"],2,',','.');?></b></td>
		<td><b><?=number_format($listaMovimiento["sum_montoIVA"],2,',','.');?></b></td>
		<td><b><?=number_format($listaMovimiento["sum_montoISLR"],2,',','.');?></b></td>
		<td><b><?=number_format($listaMovimiento["sum_montoFIANZA"],2,',','.');?></b></td>
	</tr> -->    
</table>
</body>
</html>     