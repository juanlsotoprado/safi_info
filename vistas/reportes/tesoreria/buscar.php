<?php
include(dirname(__FILE__) . '/../../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
$form = FormManager::GetForm(FORM_BUSCAR_TESORERIA);
$cuentasBancarias = $GLOBALS['SafiRequestVars']['cuentasBancarias'];
$estatusCheques = $GLOBALS['SafiRequestVars']['estatusCheques'];
$tipoBusqueda = $GLOBALS['SafiRequestVars']['tipoBusqueda'];
$beneficiarios = $GLOBALS['SafiRequestVars']['beneficiarios'];
$listaCheques = array();
$listaCheques = $GLOBALS['SafiRequestVars']['listaCheques'];

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
<div class="normalNegrita">
<?php echo $GLOBALS['SafiRequestVars']['mensaje']."<br/><br/>";?>
</div>
  <table class="tabla">
<tr> 
  <th colspan="3" class="normalNegroNegrita">B&uacute;squeda de cheques y transferencias</th>
</tr>
<tr>
	<td class="normal">
		<input name="opcionCheque" type="radio" value="c" class="normal" onclick="javascript:deshabilitar_c(this.value)" checked="checked"/>
	</td>
	<td class="normal"> Cheque </td>
		<td rowspan="3" class="normal">
			<font class="normalNegrita">Fecha emisi&oacute;n:</font>
			<input type="text" size="10" id="fechaInicio" name="fechaInicio" class="dateparse"
			onfocus="javascript: comparar_fechas(document.form.fechaInicio.value, document.form.fechaFin.value);"
			readonly="readonly" value="<?=$form->GetFechaInicioEmision()?>"/>
			<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaInicio');" title="Show popup calendar">
			<img src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
			</a>
			<input type="text" size="10" id="fechaFin" name="fechaFin" class="dateparse"
			onfocus="javascript: comparar_fechas(document.form.fechaInicio.value, document.form.fechaFin.value);"
			readonly="readonly" value="<?=$form->GetFechaFinEmision()?>"/>
			<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaFin');" title="Show popup calendar">
			<img src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
			</a>	
			<a href="javascript:limpiar(1);"> Limpiar fechas </a>
		</td>
</tr>
<tr>	
	<td class="normal">
	<input name="opcionCheque" type="radio" value="t" class="normal" onclick="javascript:deshabilitar_c(this.value)"/>
	</td>
	<td class="normal"> Transferencia</td>
</tr>
<tr>	
	<td class="normal">
	<input name="opcionCheque" type="radio" value="cyt" class="normal" onclick="javascript:deshabilitar_c(this.value)"/>
	</td>
	<td class="normal"> Cheque y transferencia</td>

</tr>



<tr class="td_gray"><td  class="normalNegroNegrita" colspan="3">General</td></tr>
<tr>
	<td>&nbsp;</td>
	<td class="normalNegrita">Cuenta bancaria:</td>
     <td> 
		<select name="cuentaBancaria" class="normal" id="cuentaBancaria">
			 <option value="-1" selected="selected">Seleccione</option>
 			<?php
 				$selected = "";
  				if(is_array($cuentasBancarias)){
	  				foreach ($cuentasBancarias AS $cuentaBancaria){
	  					//if($form->GetCuentaBancaria()->GetId()==$idcuentaBancaria) $selected = "selected";
	  						echo '
	  							<option value="'.$cuentaBancaria->GetId().'".$selected.">'.$cuentaBancaria->GetId()." - ".$cuentaBancaria->GetDescripcion().'</option>';
	  				}
  				}
  			?>			 
 		</select>
	</td>	
</tr>
<tr>
	<td class="normal">
		<input name="opcionBusqueda" id="opcionBusquedaNumero" value="1" type="radio" class="normal" onclick="javascript:deshabilitar(this.value)" />
	</td>
	<td class="normalNegrita">N&uacute;mero de referencia (#Cheque o #Ref. Bancaria): </td>
	<td class="normal">
		<input name="nroReferencia" type="text" class="normal" id="nroReferencia" value="" size="10" disabled="disabled" value="<?= $form->GetNumeroReferencia()?>"/> 			<a href="javascript:limpiarOpcion();"> Limpiar #Referencia </a>		
	</td>
</tr>
<tr>
	<td class="normal">&nbsp;</td>
	<td class="normalNegrita">Estatus del cheque: </td>
	<td>
	  <select name="estatusCheque" id="estatusCheque" class="normal">
	  <option value="-1">Seleccione</option>
	  <?php
	  $selected = "";
	  if(is_array($estatusCheques)){
	  	foreach ($estatusCheques As $estatusCheque){
	  		if ($form->GetEstatusCheque()!= null)
	  		if($form->GetEstatusCheque()->GetId() == $idEstatusCheque) $selected="selected";
	  ?>		
		  	<option value="<?=$estatusCheque->GetId()?>" <?=$selected?>><?=$estatusCheque->GetNombre()?></option>
	 <?
  		}
  	}
	  ?>	  
        </select>
	</td>
</tr>
<tr>
	<td class="normal">&nbsp;</td>
	 <td class="normalNegrita">Beneficiario:</td>
  <td>
  <input type="text" name="beneficiario" id="beneficiario" class="normalNegro" size="70" autocomplete="off"/>
<?php 	
				$arregloProveedores = "";
				$cedulasProveedores = "";
				$nombresProveedores = "";
				$indice=0;
				/**************************/
				if(is_array($beneficiarios)){
				foreach ($beneficiarios As $idBeneficiario => $beneficiario){
					$arregloProveedores .= "'".$idBeneficiario." : ".strtoupper($beneficiario->GetNombres()).' '.strtoupper($beneficiario->GetApellidos())."',";
					$cedulasProveedores .= "'".$idBeneficiario."',";
					$nombresProveedores .= "'".str_replace("\n"," ",$beneficiario->GetNombres().' '.$beneficiario->GetApellidos())."',";
					$indice++;
					
				}
				}
				/*************************/
				$arregloProveedores = substr($arregloProveedores, 0, -1);
				$cedulasProveedores = substr($cedulasProveedores, 0, -1);
				$nombresProveedores = substr($nombresProveedores, 0, -1);
			?>
				<script>			
					var proveedor = new Array(<?= $arregloProveedores?>);
					var arreglo_rif = new Array(<?= $cedulasProveedores?>);
					var nombre_proveedor= new Array(<?= $nombresProveedores?>);
					actb(document.getElementById('beneficiario'),proveedor);
				</script>	  	
  	
  	<!-- Fin Nuevo -->
  </td>
</tr>
<tr> 
  <th colspan="3" class="normalNegroNegrita">Casos anulados</th>
</tr>
<tr class="normalNegrita">
	<td><input name="opcionBusqueda" id="opcionBusquedaFecha1" value="2" type="radio" class="normal" onclick="javascript:deshabilitar(this.value)" /></td>
	<td>Por fechas:</td>
	<td class="normal">
		Emitidos en fecha (antes del):
		 <input type="text" size="10" id="fechaInicioEmisionAnulado" name="fechaInicioEmisionAnulado" disabled="disabled" class="dateparse"
		onfocus="javascript: comparar_fechas(document.form.fechaInicioEmisionAnulado.value, document.form.fechaFinEmisionAnulado.value);"
		readonly="readonly" value="<?=$form->GetFechaInicioEmisionAnulado()?>"/>
		<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaInicioEmisionAnulado');" title="Show popup calendar">
		<img src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
		</a>
		Anulados en fecha (despu&eacute;s del):
		<input type="text" size="10" id="fechaFinEmisionAnulado"  name="fechaFinEmisionAnulado" disabled="disabled" class="dateparse"
		onfocus="javascript: comparar_fechas(document.form.fechaInicioEmisionAnulado.value, document.form.fechaFinEmisionAnulado.value);" 
		readonly="readonly" value="<?=$form->GetFechaFinEmisionAnulado()?>"/>
		<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaFinEmisionAnulado');" title="Show popup calendar">
		<img src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
		</a>
			<a href="javascript:limpiar(2);"> Limpiar fechas </a>
	</td>
</tr>
<tr>
	<td class="normal"><input name="opcionBusqueda" id="opcionBusquedaFecha2" value="3" type="radio" class="normal" onclick="javascript:deshabilitar(this.value)" /></td>
	<td class="normalNegrita">Por fecha anulaci&oacute;n:</td>
	<td class="normal">
		<input type="text" size="10" id="fechaInicioAnulado" name="fechaInicioAnulado" disabled="disabled" class="dateparse"
		onfocus="javascript: comparar_fechas(document.form.fechaInicioAnulado.value, document.form.fechaFinAnulado.value);"
		readonly="readonly" value="<?=$form->GetFechaInicioAnulacion()?>"/>
		<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaInicioAnulado');" title="Show popup calendar">
		<img src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
		</a>
		<input type="text" size="10" id="fechaFinAnulado" name="fechaFinAnulado" disabled="disabled" class="dateparse"
		onfocus="javascript: comparar_fechas(document.form.fechaInicioAnulado.value, document.form.fechaFinAnulado.value);"
		readonly="readonly" value="<?=$form->GetFechaFinAnulacion()?>"/>
		<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaFinAnulado');" title="Show popup calendar">
		<img src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
		</a>
		<a href="javascript:limpiar(3);"> Limpiar fechas </a>
	</td>
</tr>
<tr>
	<td><input name="opcionBusqueda" id="opcionBusquedaFecha3" value="4" type="radio" class="normal" onclick="javascript:deshabilitar(this.value)" /></td>
	<td class="normalNegrita">Por a&ntilde;os:</td>
     <td class="normal"> A&ntilde;o emisi&oacute;n cheque:
	 <select class="normal" name="anoInicio" id="anoInicio" disabled="disabled">
	 <option value="-1">Seleccione</option>
	 <?php for ($i=$_SESSION['an_o_presupuesto']; $i>=2009; $i--) {
	 if ($form->GetAnoEmisionCheque()==$i) {
	 ?>	 
	 <option value="<?=$i;?>" selected><?=$i;?></option>
 	 <?php }
  	else {
 	 ?>
  	 <option value="<?=$i?>"><?=$i?></option>
   <?php }}?>
	 </select>
  A&ntilde;o anulaci&oacute;n cheque:
	 <select class="normal" name="anoFin" id="anoFin" disabled="disabled">
	 <option value="-1">Seleccione</option>
	 <?php for ($i=$_SESSION['an_o_presupuesto']; $i>=2009; $i--) {
	 if ($form->GetAnoAnulacionCheque()==$i) {
	 ?>	 
	 <option value="<?=$i;?>" selected><?=$i;?></option>
 	 <?php }
  	else {
 	 ?>
  	 <option value="<?=$i;?>"><?=$i;?></option>
   <?php }}?>
	 </select>
	</td>	
</tr>

  <tr>
    <td colspan="3" align="center"><input type="hidden" value="" name="tipo"></input>
	  <input type="hidden" name="tipoBusqueda" value="c"></input></td>
  </tr>
  <tr>
  <td height="44" colspan="3" align="center">
		<input type="button" value="Buscar" onclick="buscar()" />  
  </td>
</tr>
</table>
</form>
<br/>
	  <table class="tablaResultado">
					<tr>
					<th width="8%">Documento  </th>
					  <th width="4%">Nro. Referencia </th>
					  <th width="7%">Monto Bs. </th>
					  <th width="23%">Beneficiario  </th>
					  <th width="12%">Cuenta bancaria  </th>
					<?php if (strcmp($GLOBALS['SafiRequestVars']['tipoBusqueda'], 't')<0) { ?>    					  
					  <th width="6%">Estado </th>
					  <?php }?>
					<th width="7%">Fecha emisi&oacute;n</th>
					<th width="8%">Comentario  </th>
					<?php if (strcmp($GLOBALS['SafiRequestVars']['tipoBusqueda'], 't')!=0) { ?>
					<th width="7%">Fecha impresi&oacute;n  </th>
					<th width="7%">Fecha anulaci&oacute;n  </th>
					<?php } ?>					
					<th width="7%">Doc. pago </th>
					<?php if (strcmp($GLOBALS['SafiRequestVars']['tipoBusqueda'], 't')!=0 && $_SESSION['user_perfil_id'] <> "09452") $condicion_opcion="Opciones";?>                       
					 <th width="11%"><?php echo $condicion_opcion?></th>
										 
                   </tr>
                     <?php 
                     if(is_array($listaCheques)){
                     foreach ($listaCheques as $listaCheque) {
					?>
                      <tr>
                       <td class="link"> <a href="javascript:abrir_ventana('<?=GetConfig("siteURL");?>/documentos/sopg/sopg_detalle.php?codigo=<?php echo $listaCheque['sopg']; ?>')" class="copyright"><?php echo $listaCheque['sopg'];?></a></td>
                         <td align="center"><?php echo $listaCheque['nro_cheque'] ;?></td>
                          <td align="right"><?php echo number_format($listaCheque['monto_cheque'],2,',','.') ;?></td>
						<td><?php echo $listaCheque['ci_rif']." - ".$listaCheque['nombre_beneficiario'];?></td>
                          <td><?php echo $listaCheque['nro_cuenta_bancaria'];?></td>
						<?php if (strcmp($GLOBALS['SafiRequestVars']['tipoBusqueda'], 't') != 0) { ?>                              
                            <td><?php echo $listaCheque['estatus_nombre'];?></td>
                           <?php }?> 
                            <td><?php echo $listaCheque['fecha_emision'];?></td>
                             <td><?php echo $listaCheque['comentario'];?></td>
							<?php if (strcmp($GLOBALS['SafiRequestVars']['tipoBusqueda'], 't') != 0) { ?>                             
                                <td><?php echo $listaCheque['fecha_impreso'];?></td>
                                  <td><?php echo $listaCheque['fecha_anulado'];?></td>
                             <?}?>    
                              <td><?php echo $listaCheque['pgch_id'];?></td>
                        
                          <td align="left">
                          <? if (strcmp($GLOBALS['SafiRequestVars']['tipoBusqueda'], 't') != 0) {?>
						  <a href="javascript:abrir_ventana('<?=GetConfig("siteURL");?>/acciones/reportes/tesoreria/reportesTesoreria.php?accion=detalleCheque&idCheque=<?php echo $listaCheque['id_cheque']; ?>&pgchId=<?php echo $listaCheque['pgch_id']; ?>')" class="copyright">Ver detalle</a>
						  <?php }
						  if (substr( $listaCheque['comentario'],0,3)=='AP-') {
						  ?>
						  <a href="javascript:abrir_ventana('<?=GetConfig("siteURL");?>/acciones/reportes/tesoreria/reportesTesoreria.php?accion=detalleCheque&idCheque=-1&pgchId=<?php echo $listaCheque['pgch_id']; ?>')" class="copyright">	Ver &uacute;lt cheque</a>						  
						<!-- Si es el analista de tesoreria y no está conciliado el documento -->
							<?} if (strcmp($GLOBALS['SafiRequestVars']['tipoBusqueda'], 't')!=0 && ($_SESSION['user_perfil_id'] == "36450") && strcmp($listaCheque['conciliado'],"51")==0 && ($listaCheque['id_estatus']==45 || $listaCheque['id_estatus']==46)) {?>
								<br/>
								<a href="javascript:reimprimir('<? echo $listaCheque['id_cheque']; ?>','<? echo $listaCheque['sopg']; ?>')" class="copyright">Reimprimir</a>
								<a href="javascript:anular('<? echo $listaCheque['id_cheque']; ?>','<? echo $listaCheque['sopg']; ?>')" class="copyright">Anular</a>
								<?php 
								//if (strlen($listaCheque['cheque_entrega'])<2) {
								?>
								<!-- <a href="javascript:entregar('<? //echo $listaCheque['id_cheque']; ?>','<? //echo $listaCheque['sopg']; ?>')" class="copyright">Entregar</a>-->								
							<? //}
							}?>
                          </td>
                       
                        </tr>
                   <?php } } ?>
	</table>
</body>
</html>