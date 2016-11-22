<?php
include(dirname(__FILE__) . '/../../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
$form = FormManager::GetForm(FORM_RELACION_CONTABILIDAD_TESORERIA);
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
<form name="form" id="form" action="" method="POST">
  <table class="tablaPequena">
<tr> 
  <th colspan="2">Relaci&oacute;n contabilidad</th>
</tr>
<tr>
	<td class="normalNegrita">
	 <fieldset>
    <legend>Opci&oacute;n:</legend>
 	<input type="hidden" name="orden" id="orden" value=""></input>	
	<input type="hidden" name="tipoBusqueda" id="tipoBusqueda" value="<?=$form->GetTipoBusqueda()?>"></input>	
	<input type="hidden" name="tipoOpcion" id="tipoOpcion" value="<?=$form->GetOpcion()?>"></input>	 	 	
    <input type="radio" name="opcion" value="0" <?php echo (($form->GetOpcion()=="0")? "checked": "");?>/> Por conciliar&nbsp;&nbsp;
	<input type="radio" name="opcion" value="1" <?php echo (($form->GetOpcion()=="1")? "checked": "");?>/> Anulados&nbsp;&nbsp;
	<input type="radio" name="opcion" value="2" <?php echo (($form->GetOpcion()=="2")? "checked": "");?>/> Conciliados&nbsp;&nbsp;
  </fieldset>
	
  </td>
	<td class="normal">
		<input type="button" value="   Cheque    " onclick="javascript:relacionContabilidad('cheque');"/>
		<input type="button" value="Transferencia"  onclick="javascript:relacionContabilidad('transferencia');"/>
	</td>
</tr>
<tr>
<td colspan="2">
<hr>
</td>
</tr>
<tr>
<td class="normalNegrita">
 <fieldset>
    <legend>Buscar Acta:</legend>
    Nro.: <input type="text" name="numeroActa" id="numeroActa" size="5" value="">
    <input type="button" value="   Buscar   " id="buscarActa" onclick="javascript:relacionContabilidad('buscarActa');"/>
	<input type="button" value=" Generar PDF " id="imprimirActa" onclick="javascript:relacionContabilidad('imprimirActa');"/>
  </fieldset>
</td>
<td class="normalNegrita">
 <fieldset>
    <legend>Buscar Referencia:</legend>
    Nro.: <input type="text" id="referencia" name="referencia" size="5" value="">
	<input type="button" value=" Buscar " id="buscarReferencia" onclick="javascript:relacionContabilidad('buscarReferencia');"/>
  </fieldset>
</td>
</tr>	

<br>
<tr>
<td class="normalNegrita">
 <fieldset>
    <legend>Buscar por sopg:</legend>
    Nro.: <input type="text" id="sopg" name="sopg" value="">
	<input type="button" value=" Buscar " id="buscarSopg" onclick="javascript:relacionContabilidad('buscarSopg');"/>
  </fieldset>
</td>
</tr>
</table>

<br/>

<?php 


if  ($params['tipoBusqueda'] == 'cheque' || $params['tipoBusqueda'] == 'transferencia') {
?>
<div class="normalNegroNegrita" align="center">
<?php 
echo strtoupper($params['tipoBusqueda']) . "S  ". (($params['opcion'] == "0")? "POR CONCILIAR": (($params['opcion'] == "1")? "ANULADOS" : (($params['opcion'] == "2")? "CONCILIADOS": "" )));
?>
</div>
<table class="tabla">
	<tr>
    	<th class="normalNegroNegrita" align="center">&nbsp; </th>
    	<th class="normalNegroNegrita" align="center">Nro. Cheque</th>
        <th class="normalNegroNegrita" align="center">Nro. Cuenta </th>
        <th class="normalNegroNegrita" align="center">Monto </th>
 		<th class="normalNegroNegrita" align="center">C&oacute;digo sopg </th>        
        <th class="normalNegroNegrita" align="center">Fecha solicitud</th>
     </tr>
     <?php
          			
     
     if ($GLOBALS['SafiRequestVars']['listaDocumentos'] != '' && $GLOBALS['SafiRequestVars']['listaDocumentos'] != null) { 
     $i=0;
     
     foreach ($GLOBALS['SafiRequestVars']['listaDocumentos'] as $listaDocumento) {
		$i++;
     ?>
     <tr class="normal">
		<td align="center"><?echo $i;?><input type="checkbox" name="solicitud[]" value="<?php echo $listaDocumento['id_referencia'];?>" onClick="javascript:ordenar(this)";/></td>
        <td align="center"><?php echo $listaDocumento["nro_referencia"] ;?></td>
        <td align="center"><?php echo $listaDocumento["nro_cuenta"];?></td>        
        <td align="right"><?php echo number_format($listaDocumento["monto"],2,',','.');?></td>
		<td align="center"><?php echo $listaDocumento["docg_id"] ;?></td>        
        <td align="center"><?php echo $listaDocumento["fecha"];?></td>
	</tr>
    <?php } }?>
</table>
		<?php 
		if ($i>0) {
		?>
		<div align="center">
		<input type="button" value="Relacionar" onclick="javascript:validarSeleccion('<?php echo $GLOBALS['SafiRequestVars']['tipoBusqueda'];?>');"/>
		</div>
		<?php
		} 
}
elseif ($params['tipoBusqueda'] == 'buscarActa' || $params['tipoBusqueda'] == 'buscarReferencia' || $params['tipoBusqueda'] == 'buscarSopg') {
?>
<table class="tabla">
		   <tr>
		     <th class="normalNegroNegrita">Nro.</th>
		     <th class="normalNegroNegrita">Movimiento</th>
		     <th class="normalNegroNegrita">Fecha acta</th>
		     <?php if ($params['tipoBusqueda'] == 'buscarActa') {?>
		     <th class="normalNegroNegrita">Estatus</th>
		     <?php }?>
		     <th class="normalNegroNegrita">Nro Registro</th>
		     <th class="normalNegroNegrita">Fecha emisi&oacute;n</th>
		     <th class="normalNegroNegrita">Nro referencia</th>
		     <th class="normalNegroNegrita">Beneficiario</th>
		     <th class="normalNegroNegrita">Concepto</th>
		     <th class="normalNegroNegrita">Monto</th>		
		     <th class="normalNegroNegrita">Sopg</th>		   
		   </tr>
<?php 
if ($GLOBALS['SafiRequestVars']['listaDocumentos'] != '' && $GLOBALS['SafiRequestVars']['listaDocumentos'] != null) {
	$i=1;
	foreach ($GLOBALS['SafiRequestVars']['listaDocumentos'] as $listaDocumento) {
		?>
     <tr class="normal">
        <td align="center"><?php echo $listaDocumento["nro_acta"] ;?></td>
        <td align="center"><?php echo $listaDocumento["movimiento"] ;?></td>
        <td align="center"><?php echo $listaDocumento["fecha_acta"];?></td>
		<?php if ($params['tipoBusqueda'] == 'buscarActa') {?>
        <td align="center"><?php echo $listaDocumento["estatus"] ;?></td>
		<?php }?>        
        <td align="center"><?php echo $listaDocumento["nro_registro"] ;?></td>
        <td align="center"><?php echo $listaDocumento["fecha_emision"] ;?></td>
        <td align="center"><?php echo $listaDocumento["nro_referencia"] ;?></td>
        <td align="center"><?php echo $listaDocumento["beneficiario"] ;?></td>
        <td align="center"><?php echo $listaDocumento["concepto"] ;?></td>        
        <td align="right"><?php echo number_format($listaDocumento["monto"],2,',','.');?></td>
        <td align="center"><?php echo $listaDocumento["sopg"] ;?></td> 

    <?php $i++;} }?>
</table>
<?php 
}
?>
</form>
</body>
</html>