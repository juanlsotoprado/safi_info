<?php
include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
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
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/pgch.js'?>"></script>
</head>

<body>
<form name="form" id="form" action="" method="post">
<div class="normalNegrita">
<?php echo $GLOBALS['SafiRequestVars']['mensaje']."<br/><br/>";?>
</div>
  <table class="tablaPequena">
<tr> 
  <th colspan="3" class="normalNegroNegrita">Iniciar pago (tran)</th>
</tr>
<tr>
		<td class="normalNegrita">N&uacute;mero de sopg:</td>
		<td class="normal">
		<input type="text" class="normal"  value="sopg-" name="sopgId" id="sopgId"></input>
		</td>
</tr>
<tr>
<td class="normal" colspan="2">	<b>Nota:</b><br/> 
1.- El pago con cheque asociado a la solicitud de pago debe ser ANULADO previamente (el MISMO D&Iacute;A que se realice la transferencia). <br/>
2.- El pago DEBE estar causado y anulado en el mismo MES que se realice el reinicio del pago por transferencia.
 
</td>
</tr>
<tr align="center">
		<td colspan="2" class="normal" align="center">
			<input type="button" value="Buscar" onClick="buscarAccion()"/>
		</td>
</tr>	
</table>
<br/>

	  <table class="tablaPequena">
	   <tr class="td_gray">
		   <th class="normalNegroNegrita">Detalle del pago con cheque</td>
			<?php 
				if(isset($GLOBALS['SafiRequestVars']['pgch']) && $GLOBALS['SafiRequestVars']['pgch'] != null) {
			?>
		<tr class="normalNegro">
			<td><b>Nro. Pago con cheque:</b> <?php echo $GLOBALS['SafiRequestVars']['pgch']->GetId();?>
			<input type="hidden" name="idPgch" id="idPgch" value="<?php echo $GLOBALS['SafiRequestVars']['pgch']->GetId()?>"/>			
			<input type="hidden" name="idSopg" id="idSopg" value="<?php echo $GLOBALS['SafiRequestVars']['pgch']->GetIdDocumento()?>"/>
			</td>
		</tr>
		<tr class="normalNegro">				
			<td ><b>Documento asociado:</b> <?php echo $GLOBALS['SafiRequestVars']['pgch']->GetIdDocumento();?></td>
		</tr>
		<tr class="normalNegro"> 
			<td > <b>Fecha:</b> <?php echo $GLOBALS['SafiRequestVars']['pgch']->GetFechaPgch();?></td>
		</tr>
		<tr class="normalNegro"> 
			<td > <b>N&uacute;mero Cuenta:</b> <?php echo $GLOBALS['SafiRequestVars']['pgch']->GetNumeroCuenta();?></td>
		</tr>	
		<tr class="normalNegro"> 
			<td > <b>Observaciones:</b> <?php echo $GLOBALS['SafiRequestVars']['pgch']->GetObservaciones();?></td>
		</tr>				
		<!-- <tr class="normalNegro"> 
			<td > <b>Beneficiario:</b> <?php //echo $GLOBALS['SafiRequestVars']['pgch']->GetCheque()->GetBeneficiarioCheque();?></td>
		</tr>-->	
		<tr class="normalNegro"> 
			<td align="center"> <input type="button" value="Volver a Bandeja de Iniciar Pago" onClick="reiniciarPago();"></td>
		</tr>					
		<?php }	?>	  
	</table>
</form>	
</body>
</html>


