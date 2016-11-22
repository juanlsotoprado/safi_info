<?php
include(dirname(__FILE__) . '/../../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
$form = FormManager::GetForm(FORM_BUSCAR_TESORERIA);
$listaCheque = array();
$listaCheque = $GLOBALS['SafiRequestVars']['listaCheques'];

?>
<!DOCTYPE html>
<html> 
<head>
	<title>SAFI</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>" rel="stylesheet" type="text/css" />
	<?php require(SAFI_JAVASCRIPT_PATH.'/init.php'); ?>
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"></script>	
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/reportesTesoreria.js'?>"></script>
</head>

<body>
<form name="form" method="post" action="">
<div align="center"><?php echo $listaCheque['mensaje'];?></div>
<table class="tabla">
	<tr class="td_gray">
		<td colspan="2" class="normalNegroNegrita">Anular cheque</td>
	</tr>

	<tr>
		<td class="normalNegrita">Documento asociado:</td>
		<td class="normal">
			<a href="javascript:abrir_ventana('<?=GetConfig("siteURL");?>/documentos/sopg/sopg_detalle.php?codigo=<?php echo $listaCheque['sopg']; ?>)" class="copyright"><?php echo $listaCheque['sopg'];?></a>
			<input type="hidden" value="<?php echo $listaCheque['sopg']; ?>" name="idSopg" id="idSopg"></input>			
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">N&uacute;mero de cheque:</td>
		<td class="normal"><?php echo $listaCheque['nro_cheque'];?>
		<input type="hidden" value="<?php echo $listaCheque['nro_cheque']; ?>" name="numeroCheque" id="numeroCheque"></input>		
		<input type="hidden" value="<?php echo $listaCheque['id_cheque']; ?>" name="idCheque" id="idCheque"></input>
		
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">N&uacute;mero de cuenta:</td>
		<td class="normal"><?php echo $listaCheque['nro_cuenta_bancaria'];?>
		<input type="hidden" value="<?php echo $listaCheque['nro_cuenta_bancaria']; ?>" name="numeroCuenta" id="numeroCuenta"></input>		
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">Banco:</td>
		<td class="normal"><?php echo $listaCheque['nombre_banco'];?>
		<input type="hidden" value="<?php echo $listaCheque['nombre_banco'];?>" name="banco" id="banco"/>	
</td>
	</tr>
	<tr>
		<td class="normalNegrita">Beneficiario:</td>
		<td class="normal"><?php echo $listaCheque['ci_rif']." - ".$listaCheque['beneficiario_cheque'];?>
		<input type="hidden" value="<?php echo $listaCheque['id_beneficiario']." - ".$listaCheque['beneficiario_cheque']; ?>" name="beneficiarioCheque" id="beneficiarioCheque"></input>		
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">Monto Bs.:</td>
		<td class="normal"><?php echo $listaCheque['monto_cheque']; ?>
		<input type="hidden" value="<?php echo $listaCheque['monto_cheque']; ?>" name="montoCheque" id="montoCheque"></input>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">Concepto del pago:</td>
		<td class="normal"><?php echo $listaCheque['asunto'];?>
		<input type="hidden" value="<?php echo $listaCheque['asunto']; ?>" name="asunto" id="asunto"/>
		</td>		
	</tr>
	<tr>
		<td class="normalNegrita">Observaciones del pago:</td>
		<td class="normal"><?php echo $listaCheque['observaciones'];?>
		<input type="hidden" value="<?php echo $listaCheque['observaciones'];?>" name="observaciones" id="observaciones"></input>
		</td>		
	</tr>
	<tr>
		<td colspan="2"><input type="hidden" name="idSopg"	value="<?php echo $listaCheque['sopg'];?>" /></td>
	</tr>
	<?php if (strlen($listaCheque['mensaje'])<5) {?>
	<tr>
		<td width="18%" class="normalNegroNegrita">Motivo de la anulaci&oacute;n</td>
		<td>
		<select name="motivo" id="motivo" class="normal">
			<option value="AP-Impresion">AP - Impresi&oacute;n fallida</option>
			<option value="AP-Caducidad">AP - Fecha de caducidad</option>
			<option value="AP-Extravio">AP - Extrav&iacute;o</option>
			<option value="AP-Otro">AP - Otro</option>

			<option value="AT-Partida">AT - Error en partida</option>
			<option value="AT-Monto">AT - Error en monto</option>
			<option value="AT-Beneficiario">AT - Error en proveedor o
			beneficiario</option>
			<option value="AT-Motivo">AT - Otro</option>
		</select></td>
	</tr>
	<?php }?>
	<tr>
		<td class="normalNegroNegrita">Justificaci&oacute;n de la anulaci&oacute;n:</td>	
		<td class="normal">
		<?php if (strlen($listaCheque['mensaje'])>5)
			echo $listaCheque['observacionesAnulacion'];
		else {?>		
		<textarea name="observacionesAnulacion" id="observacionesAnulacion" rows="5" cols="50"></textarea>
		<?php }?>
		</td>
	</tr> 	
	<?php if (strlen($listaCheque['mensaje'])<5) {?>	
	<tr align="center">
		<td colspan="2" class="normal">
		<div align="center"><br></br><input type="button" value="Anular"
			onclick="javascript:anularCheque();" /></div>
		</td>
	</tr>
	<?php }?>
</table>
<br></br>
	<?php if (strlen($listaCheque['mensaje'])<5) {?>	
		<div class="normalNegrita">
		Nota: <br />
		1.- La anulaci&oacute;n de cheque parcial (AP) permite generar otro cheque con los mismos datos del
		beneficiario y monto.<br />
		2.- La anulaci&oacute;n de cheque total (AT) anula definitivamente la
		solicitud de pago (sopg) y pago con cheque (pgch). Adicionalmente se
		anulan los movimientos contables y presupuestarios generados.</div>
	<?php }?>
</form>
</body>
</html>