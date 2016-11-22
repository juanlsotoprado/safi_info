<?php
include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
$form = FormManager::GetForm(FORM_TRANSFERENCIA);
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
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/transferencia.js'?>"></script>
</head>

<body>
<form name="form" id="form" action="" method="post">
<div class="normalNegrita">
<?php echo $GLOBALS['SafiRequestVars']['mensaje']."<br/><br/>";?>
</div>
  <table class="tablaPequena">
<tr> 
  <th colspan="3" class="normalNegroNegrita">Modificar transferencia</th>
</tr>
<tr>
		<td class="normalNegrita">N&uacute;mero de transferencia:</td>
		<td class="normal">
		<input type="text" class="normal"  value="tran-" name="numeroTransferencia" id="numeroTransferencia"></input>
		</td>
</tr>
<tr>
<td class="normal" colspan="2">	<b>Nota:</b> La transferencia no debe estar conciliada, de ser as&iacute;, primero debe des-conciliarse para luego realizar la modificaci&oacute;n respectiva.</td>
</tr>
<tr align="center">
		<td colspan="2" class="normal" align="center">
			<input type="button" value="Buscar" onClick="buscarAccion()"/>
		</td>
</tr>	
</table>
<br/>

	  <table class="tablaResultado">
	   <tr class="td_gray">
		   <th class="normalNegroNegrita" colspan="2">Detalle de la transferencia</td>
			<?php 
				if(isset($GLOBALS['SafiRequestVars']['transferencia']) && $GLOBALS['SafiRequestVars']['transferencia'] != null) {
			?>
		<tr class="normalNegroNegrita">
			<td><b>Nro.:</b> <?php echo $GLOBALS['SafiRequestVars']['transferencia']->GetIdTransferencia();?>
			<input type="hidden" name="idTransferencia" id="idTransferencia" value="<?php echo $GLOBALS['SafiRequestVars']['transferencia']->GetIdTransferencia()?>"/>
			</td>
		</tr>
		<tr class="normalNegroNegrita">				
			<td colspan="2"><b>Documento asociado:</b> <?php echo $GLOBALS['SafiRequestVars']['transferencia']->GetIdDocumento();?></td>
		</tr>
		<tr class="normalNegroNegrita"> 
			<td> <b>Fecha:</b> <?php echo $GLOBALS['SafiRequestVars']['transferencia']->GetFechaTransferencia();?>
			<input type="hidden" id="fechaVieja" name="fechaVieja" value="<?php echo $GLOBALS['SafiRequestVars']['transferencia']->GetFechaTransferencia();?>" />
			
			</td>
			
					<td class="normalNegrita">NUEVA FECHA:
						<input value="" type="text" size="10" id="nuevaFecha" name="nuevaFecha" class="dateparse" readonly="readonly"/>
						<a 	href="javascript:void(0);"
							onclick="g_Calendar.show(event, 'nuevaFecha');"
							title="Show popup calendar">
							<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"	alt="Open popup calendar"/>
						</a>
					</td>			
		</tr>
		<tr class="normalNegroNegrita">				
			<td><b>Nro. Referencia:</b> <?php echo $GLOBALS['SafiRequestVars']['transferencia']->GetNumeroReferencia();?></td>
			<td class="normalNegrita">NUEVA REFERENCIA:
					<input type="text" class="normal" id="nuevaReferencia" name="nuevaReferencia" value=""></td>
				</td>				
		</tr>
		<tr class="normalNegroNegrita">				
			<td><b>Banco/#Cuenta:</b> <?php echo $GLOBALS['SafiRequestVars']['transferencia']->GetCuentaEmisor()->GetBanco()->GetNombre() ."-". $GLOBALS['SafiRequestVars']['transferencia']->GetCuentaEmisor()->GetId();?></td>
			<td class="normalNegrita">NUEVA CUENTA: 
							<select name="nuevaCuentaBancaria" class="normal" id="nuevaCuentaBancaria">
							 <option value="-1" selected="selected">Seleccione</option>
					 			<?php
					 				$selected = "";
					  				if(is_array($GLOBALS['SafiRequestVars']['cuentasBancarias'])){
						  				foreach ($GLOBALS['SafiRequestVars']['cuentasBancarias'] AS $cuentaBancaria){
						  					/*if($cuentaBancaria->GetId()==$GLOBALS['SafiRequestVars']['transferencia']->GetCuentaBancaria()->GetId()) $selected = "selected";
						  					else $selected="";*/
						  					?>
				  							<option value="<?php echo $cuentaBancaria->GetId()?>" <?php echo $selected?>><?php echo $cuentaBancaria->GetId()." - ".$cuentaBancaria->GetDescripcion()?></option>
				  							<?php
						  				}
					  				}
					  			?>			 
			 		</select>
				</td>			
		</tr>
		<tr class="normalNegroNegrita">				
			<td><b>Beneficiario: </b><?php echo $GLOBALS['SafiRequestVars']['transferencia']->GetRifCedula().":  ".$GLOBALS['SafiRequestVars']['transferencia']->GetBeneficiario();?></td>
				<input type="hidden" class="normal" id="beneficiarioId" name="beneficiarioId" value="<?php echo $GLOBALS['SafiRequestVars']['transferencia']->GetRifCedula();?>">
				<input type="hidden" class="normal" id="beneficiarioNombre" name="beneficiarioNombre" value="<?php echo $GLOBALS['SafiRequestVars']['transferencia']->GetBeneficiario()?>">
		</tr>
				<tr class="normalNegroNegrita">				
				<td><b>Monto:</b> <?php echo number_format($GLOBALS['SafiRequestVars']['transferencia']->GetMontoTransferencia(),2,',','.');?></td>
				</tr>
				<tr class="normalNegroNegrita">				
				<td><b>Asunto:</b> <?php echo $GLOBALS['SafiRequestVars']['transferencia']->GetAsuntoTransferencia();?></td>			
				</tr>
							
				<tr align="center">
				<td colspan="2" class="normal">
				<div align="center"><br></br><input type="button" value="Modificar" onClick="javascript:modificarAccion();"/></div>
				</td>
				</tr>
		<?php }	?>	  
	</table>
</form>	
</body>
</html>


