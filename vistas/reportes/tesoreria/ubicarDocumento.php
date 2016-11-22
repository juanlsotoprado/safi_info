<?php
include(dirname(__FILE__) . '/../../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
$form = FormManager::GetForm(FORM_UBICAR_DOCUMENTO_TESORERIA);
$listaDocumentos = array();
$listaDocumentos = $GLOBALS['SafiRequestVars']['listaDocumentos'];

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
<form name="form" id="form" action="" method="post">
  <table class="tablaPequena">
<tr> 
  <th colspan="2">Ubicar documento(Documento/Referencia)</th>
</tr>
<tr>
	<td colspan="2" class="normalNegrita">
	<input type="radio" name="tipo" value="sopg" onclick="javascript:deshabilitarUbicar()"/> Sopg&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="radio" name="tipo" value="pgch" onclick="javascript:deshabilitarUbicar()"/> Pgch&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="radio" name="tipo" value="tran" onclick="javascript:deshabilitarUbicar()"/> Tran &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="radio" name="tipo" value="cheq" onclick="javascript:deshabilitarUbicar()"/> Nro. Cheque&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="radio" name="tipo" value="ntran" onclick="javascript:deshabilitarUbicar()"/> Nro. Referencia (Transferencia)
  </td>
</tr>
<tr>
	<td class="normalNegrita">Nro. Documento: </td>
	<td class="normal"><input name="documento" type="text" class="normal" id="documento" value="" size="20" disabled="disabled"/> S&oacute;lo el n&uacute;mero, sin el prefijo xxxx-</td>
</tr>
<tr>
		<td colspan="2" class="normal">
			<font class="normalNegrita">Fecha emisi&oacute;n:</font>
			<input type="text" size="10" id="fechaInicio" name="fechaInicio" class="dateparse"
			onfocus="javascript: comparar_fechas(document.form.fechaInicio.value, document.form.fechaFin.value);"
			readonly="readonly" value="<?=$form->GetFechaInicio()?>"/>
			<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaInicio');" title="Show popup calendar">
			<img src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
			</a>
			<input type="text" size="10" id="fechaFin" name="fechaFin" class="dateparse"
			onfocus="javascript: comparar_fechas(document.form.fechaInicio.value, document.form.fechaFin.value);"
			readonly="readonly" value="<?=$form->GetFechaFin()?>"/>
			<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaFin');" title="Show popup calendar">
			<img src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
			</a>	
			<a href="javascript:limpiar(1);"> Limpiar fechas </a>
		</td>
</tr>
  <tr>
  <td height="44" colspan="2" align="center">
		<input type="button" value="Buscar" onclick="ubicarDocumento()" />  
  </td>
</tr>
</table>
</form>
<br/>
<table class="tabla">
	<tr>
    	<th width="8%" class="normalNegroNegrita" align="center">Sopg </th>
        <th width="8%" class="normalNegroNegrita" align="center">Fecha sopg </th>    	
    	<th width="8%" class="normalNegroNegrita" align="center">Pgch/Tran</th>
        <th width="6%" class="normalNegroNegrita" align="center">Nro referencia </th>
 		<th width="6%" class="normalNegroNegrita" align="center">Estado </th>        
        <th width="10%" class="normalNegroNegrita" align="center">Nro. Cuenta</th>
        <th width="19%" class="normalNegroNegrita" align="center">Beneficiario</th>
        <th width="6%" class="normalNegroNegrita" align="center">Monto</th>
        <th width="25%" class="normalNegroNegrita" align="center">Detalle</th>                        
 		<th width="10%" class="normalNegroNegrita" align="center">Perfil actual</th>
     </tr>
     <?php
     if ($GLOBALS['SafiRequestVars']['tipoBusqueda'] != '') { 
     foreach ($listaDocumentos as $listaDocumento) {
     ?>
     <tr class="normal" align="left">
		<td><?php echo $listaDocumento["documento_inicial"] ;?></td>
        <td><?php echo $listaDocumento["fecha"];?></td>		
        <td><?php echo $listaDocumento["documento_relacion"] ;?></td>
        <td><?php echo $listaDocumento["nro_referencia"] ;?></td>
		<td><?php echo $listaDocumento["estado_documento"] ;?></td>        
        <td><?php echo $listaDocumento["nro_cuenta"];?></td>
        <td><?php echo $listaDocumento["beneficiario"];?></td>
        <td><?php echo $listaDocumento["monto_pago"];?></td>
        <td><?php echo $listaDocumento["detalle"];?></td>
        <td><?php echo $listaDocumento["perfil"];?></td>
	</tr>
    <?php } }?>
</table>
</body>
</html>