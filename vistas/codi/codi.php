<?php
include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
$objCodi = Array();
$yearPresupuestario = $GLOBALS['SafiRequestVars']['yearPresupuestario'];
if (isset($GLOBALS['SafiRequestVars']['listaCodiDetalle'])) {
	foreach($GLOBALS['SafiRequestVars']['listaCodiDetalle'] as &$detalle){
		foreach($detalle as &$detalle2) {
			$detalle2['cpat_nombre'] = utf8_encode($detalle2['cpat_nombre']);		
			$detalle2['a_esp_nombre'] = utf8_encode($detalle2['a_esp_nombre']);
			$detalle2['p_acc_nombre'] = utf8_encode($detalle2['p_acc_nombre']);
			$detalle2['part_nombre'] = utf8_encode($detalle2['part_nombre']);
		}
	}
}
unset($detalle);
unset($detalle2);

$bandera = $this->_banderaCambioAnio;
$fechacambioanio = $this->_fechaCambioAnio;
?>
<html>
<head>
<title>.:SAFI:. Comprobante diario manual</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>" 
 rel="stylesheet" type="text/css" charset="utf-8" />
<?php require(SAFI_JAVASCRIPT_PATH.'/init.php'); ?>
<link href="<?=GetConfig("siteURL").'/css/safi0.2.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />

<?php require(SAFI_INCLUDE_PATH.'/fechaJs.php'); ?>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"
	charset="utf-8"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/ui.min.js';?>"
	charset="utf-8"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/constantes.js';?>"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/funciones.js';?>"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/func_montletra.js';?>"
	charset="utf-8">
	dependencia = <?php echo $_SESSION['user_depe_id'];?>

</script>
	
<script type="text/javascript" charset="utf-8">

    var codiDetalleContable = <?php 
		if (is_array($GLOBALS['SafiRequestVars']['listaCodiDetalle']) && count($GLOBALS['SafiRequestVars']['listaCodiDetalle']) > 0)
		{
			try {
				reset($GLOBALS['SafiRequestVars']['listaCodiDetalle']);
				echo json_encode(current($GLOBALS['SafiRequestVars']['listaCodiDetalle']),JSON_FORCE_OBJECT);
				
				if (json_last_error() != JSON_ERROR_NONE)
					throw new Exception("Error en la función json_encode()." .
						"\nArray que se intenta codificar:\n". print_r(current($GLOBALS['SafiRequestVars']['listaCodiDetalle']),true) );
					
			}
			catch (Exception $e){
				error_log($e);
			}
		} else echo "null";
	?>;
	
	var yearPresupuestario = <?php echo ($yearPresupuestario != null && $yearPresupuestario != "") ? $yearPresupuestario : "null"; ?>;
</script>

<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/codi/codi.js';?>" charset="utf-8"></script>
	
<style type="text/css">
.ui-autocomplete {
       max-height: 210px;
       font-size: 12px;
       overflow-y: auto;
       /* prevent horizontal scrollbar */
       overflow-x: hidden;
       /* add padding to account for vertical scrollbar */
       padding-right: 30px;
}
/* IE 6 doesn't support max-height
                       * we use height instead, but this forces the menu to always be this tall
                       */
* html .ui-autocomplete {
       font-size: 10px;
}

.ui-menu-item a {
       font-size: 10px;
}
</style>	
	
	
	<link type="text/css" href="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/calendarPopup/css/calpopup.css';?>"
	media="screen" rel="stylesheet" />

	<!-- estilo para el autocompletar -->
	<link
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />
  
</head>
<body>

<form name="formCodi" id="formCodi" method="post" action="codi.php">
	<input type="hidden" id="accion" name="accion"/>

		<table style="width: 100%; align:center" class="tablaalertas">
			<tr>
				<th class="header"><span class="normalNegroNegrita">.: Comprobante diario manual :.</span>
			</tr>
			<tr>
				<td>
				<fieldset id="recuadro1" style="width:60%">
							<legend>
							<b>Detalle del codi <?=(isset($GLOBALS['SafiRequestVars']['listaCodi']) ? $GLOBALS['SafiRequestVars']['listaCodi'][0]->GetId() : '') ?></b>
							</legend>
							<input type="hidden" id="idCodi" name="idCodi" value="<?=(isset($GLOBALS['SafiRequestVars']['listaCodi']) ? $GLOBALS['SafiRequestVars']['listaCodi'][0]->GetId() : '') ?>">
					<table>
						<tr>
							<td style="width: 200px;" class="normalNegrita">Fecha:</td>
							<td><input type="text" size="10" id="fecha" name="fecha"
								class="dateparse" readonly="readonly"
								value="<?=(isset($GLOBALS['SafiRequestVars']['listaCodi']) ? $GLOBALS['SafiRequestVars']['listaCodi'][0]->GetFechaEmision() : ''); if($bandera == 1){echo $fechacambioanio;}?>"/>
								<a href="javascript:void(0);"
								onclick="g_Calendar.show(event, 'fecha');"
								title="Show popup calendar">
								<?php 
								if($bandera <> 1)
								{
								?>
								<img
									src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif"
									class="cp_img" alt="Open popup calendar" /> 
								<?php	
								}
								?>
								</a></td>
						</tr>
						<!-- <tr>
							<td class="normal"> <b>Elaborado por:</b></td>
							<td class="normal" ><?php echo $_SESSION['solicitante']; ?></td>
						</tr>
						-->
						<tr>
							<td class="normalNegrita">Nro. compromiso:</td>
							<td><input type="text" name="comp_id" id="comp_id" class="normalNegro" size="15" value="<?=(isset($GLOBALS['SafiRequestVars']['listaCodi']) ? $GLOBALS['SafiRequestVars']['listaCodi'][0]->GetNroCompromiso() : '') ?>"></td>
						</tr>
						<tr>
							<td class="normalNegrita">Documento asociado:</td>
							<td><input type="text" name="docAsociado" id="docAsociado" size="15" value="<?=(isset($GLOBALS['SafiRequestVars']['listaCodi']) ? $GLOBALS['SafiRequestVars']['listaCodi'][0]->GetDocumentoAsociado() : 'sopg-') ?>"/></td>
						</tr>
						<tr>
							<td class="normalNegrita">Nro. Referencia bancaria:</td>
							<td><input type="text" name="refBancaria" id="refBancaria" size="15" maxlength="20" value="<?=(isset($GLOBALS['SafiRequestVars']['listaCodi']) ? $GLOBALS['SafiRequestVars']['listaCodi'][0]->GetNumeroReferencia() : '') ?>"/></td>
						</tr>
						<tr>
							<td class="normalNegrita">Justificaci&oacute;n:</td>
							<td><textarea class="normalNegro" name="justificacion" cols="50" id="justificacion" onkeydown="textCounter(this,'comentarioLen',60);" onkeyup="textCounter(this,'comentarioLen',60);"  onkeypress="validarTexto(justificacion)"><?=(isset($GLOBALS['SafiRequestVars']['listaCodi']) ? $GLOBALS['SafiRequestVars']['listaCodi'][0]->GetJustificacion() : '') ?></textarea>
								<input type="hidden" name="accEsp" id="accEsp" size="15" maxlength="20" value="<?=(isset($GLOBALS['SafiRequestVars']['centro_costo']) ? $GLOBALS['SafiRequestVars']['centro_costo']: '') ?>"/>								  
								<div style="text-align: right;width: 380px"><input type="text" value="60" class="peqNegrita" maxlength="3" size="3" id="comentarioLen" name="comentarioLen" readonly="readonly"/></div>							
							</td>
						</tr>
			</table>
</fieldset>
</td>
</tr>
</table>
<br/>
<!-- Categoría y cuenta contable -->

   				<div id="categoriaCuentaContable">
						<fieldset id="recuadro2" style="width:70%">
							<legend>
								<b>Categor&iacute;a program&aacute;tica</b>
							</legend>

							<div class="normal">
								<strong style="margin-right: 130px;">Proy/Acc:</strong> <select
									name="selectProyAcc" class="normal" id="selectProyAcc" style="width: 650px;">
									<option selected value="">.:.Seleccione.:.</option>


									<optgroup label="Proyectos">
										<?
										if(is_array($GLOBALS['SafiRequestVars']['proyectos']) && count($GLOBALS['SafiRequestVars']['proyectos'])>0) { 
										foreach ( $GLOBALS['SafiRequestVars']['proyectos'] as $proyectos ){ ?>
										<option value="<?php echo $proyectos['proy_id']; ?>">
											<?php echo $proyectos['proy_titulo']?>
										</option>
										<?php }}?>
									</optgroup>

									<optgroup label="Acc.">
										<?
										if(is_array($GLOBALS['SafiRequestVars']['acc']) && count($GLOBALS['SafiRequestVars']['acc'])>0) {
										foreach ( $GLOBALS['SafiRequestVars']['acc'] as $acc){ ?>

										<option value="<?php echo $acc['acce_id']; ?>">
											<?php echo $acc['acce_denom']; ?>
										</option>

										<?php }}?>
									</optgroup>


								</select> <br>
								<br>
								<div id="accionEspecifica">
									<div align="left" class="normal">
										<strong style="margin-right: 103px;">Ac. Espec&iacute;fica:</strong>
										<input type="text" name="categoria" id="categoria" class="normalNegro" size="68" value="" />

									</div>
								</div>
								
								<div id="tablaCategoria">

									<table class="tablaalertas" border="1">
										<tr>
											<th class="header"><span class="normalNegroNegrita">Tipo</span></th>
											<th class="header"><span class="normalNegroNegrita">Nombre Proy/Acc</span></th>
											<th class="header"><span class="normalNegroNegrita">Proy/Acc</span></th>
											<th class="header"><span class="normalNegroNegrita">Nombre Acci&oacute;n esp.</span></th>
										</tr>
										<tr>
											<td id="c1" class="normal"></td>
											<td id="c3" class="normal"></td>
											<td id="c2" class="normal"></td>
											<td id="c4" class="normal"></td>
											
										</tr>
									</table>

								 </div>
<!-- 
								<div id="divCuentaMonto" > 
									<br />
									<hr width="100%">
									<div align="left" class="normal">
									<table class="tablaalertas" border="1">
									<tr>
									<tr>
									<td class="normalNegroNegrita">Cuenta contable:
										<input type="text" name="cuentaContable" id="cuentaContable" class="normalNegro" size="60" value="" />
									</td>
									<td class="normalNegroNegrita">Debe:
										<input type="text" name="debe_cuenta" id="debe_cuenta"	class="normalNegro" size="15" value="" /> 
									</td>
									<td class="normalNegroNegrita">Haber:									
										<input type="text" name="haber_cuenta" id="haber_cuenta" class="normalNegro" size="15" value="" />
									</td>
									</tr>
									<tr>
									<td colspan="3" align="center">
									<input
											style="margin-left: 30px" type="button" id="confirmarCuenta"
											value="Confirmar"/>
									</td>
									</tr>
									</table>
									</div>
								 </div>
-->

								 <!-- <div id="cuerpoCategoriaCuenta">
									<br />
								</div>-->

							</div>
						</fieldset>
					</div> <!-- Categoría y cuenta contable -->

					<br/>
					<div id="cuerpoCategoriaCuenta">
				 <fieldset  style="width:70%"> 
			<input type="hidden" id="arreglosCuentaContable" name="arreglosCuentaContable" value="<?=(isset($GLOBALS['SafiRequestVars']['listaCodiDetalle']) ? $GLOBALS['SafiRequestVars']['listaCodiDetalle']: '')?>">
		<!-- 	<input type="hidden" id="arreglosPartidaPresupuestaria" id="arreglosPartidaPresupuestaria" value="<?//=(isset($GLOBALS['SafiRequestVars']['listaCodiPresupuesto']) ? $GLOBALS['SafiRequestVars']['listaCodiPresupuesto']: '')?>">-->
						<table class="tablaalertas" border="1">
							<tbody id="tablaCategoriaCuenta">
								<tr>
									<th class="header"><span class="normalNegroNegrita">#</span></th>
									<th class="header"><span class="normalNegroNegrita">Cuenta</span></th>
									<th class="header"><span class="normalNegroNegrita">Proy_Acc/Acc Esp.</span></th>
									<th class="header"><span class="normalNegroNegrita">Partida</span></th>
									<th class="header"><span class="normalNegroNegrita">Denominaci&oacute;n</span></th>
									<th class="header"><span class="normalNegroNegrita">Monto disponible</span></th>
									<th class="header"><span class="normalNegroNegrita">Debe</span></th>
									<th class="header"><span class="normalNegroNegrita">Haber</span></th>
									<th class="header"><span class="normalNegroNegrita">Opciones</span></th>
								</tr>
							</tbody>
						</table>
						 </fieldset> 	
					</div>
				<!-- </fieldset> -->
				
				<br>	
				<div id="divCuentaMonto" > 
				
					<fieldset style="width: 70%">
						<legend>
							<b>Selecci&oacute;n de cuenta contable y monto</b>
						</legend>
						<br>
						<div align="left" class="normal">
							
							<span class="normalNegrita" style="padding-right: 10px;">Cuenta contable:</span>
							<input type="text" name="cuentaContable" id="cuentaContable" class="normalNegro" size="100" value="" />
							<br><br>
							<table class="tablaalertas" border="1" style="width: 100%">
								<tbody>
									<tr>
										<th class="header"><span class="normalNegroNegrita">Cuenta</span></th>
										<th class="header"><span class="normalNegroNegrita">Partida</span></th>
										<th class="header"><span class="normalNegroNegrita">Denominaci&oacute;n</span></th>
										<th class="header" style="width: 135px;"><span class="normalNegroNegrita">Debe</span></th>
										<th class="header" style="width: 135px;"><span class="normalNegroNegrita">Haber</span></th>
										<th class="header" style="width: 65px;"><span class="normalNegroNegrita">Opciones</span></th>
									</tr>
									<tr class="normal">
										<td>
											<span id="_spanCuentaContable"></span>
											<input id="_inputCuentaContable" type="hidden">
										</td>
										<td>
											<span id="_spanPartida"></span>
											<input id="_inputPartida" type="hidden">
										</td>
										<td>
											<span id="_spanDenominacion"></span>
											<input id="_inputDenominacion" type="hidden">
										</td>
										<td class="normalNegroNegrita">
											<input type="text" name="debe_cuenta" id="debe_cuenta"	class="normalNegro" size="15" value="" />
										</td>
										<td>
											<input type="text" name="haber_cuenta" id="haber_cuenta" class="normalNegro" size="15" value="" />
										</td>
										<td class="link" style="text-align: center;">
											<!-- <input style="margin-left: 0px" type="button" id="confirmarCuenta" value="Agregar"/>  -->
											<a href="javascript:void(0);" id="confirmarCuenta">Agregar</a>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</fieldset>
				</div>					
					
				<br>		
				<div id="cuerpoRegistro">
					<fieldset  style="width:70%">
						<legend>
							<b>Detalles de las cuentas contables presentes en el codi</b>
						</legend>
					 					
						<table class="tablaalertas" border="1" style="width: 100%;">
							<tbody id="tablaRegistro">
								<tr>
									<th class="header"><span class="normalNegroNegrita">#</span></th>
									<th class="header"><span class="normalNegroNegrita">Cuenta</span></th>
									<th class="header"><span class="normalNegroNegrita">Proy_Acc / Acc Esp.</span></th>
									<th class="header"><span class="normalNegroNegrita">Partida</span></th>
									<th class="header"><span class="normalNegroNegrita">Denominaci&oacute;n</span></th>
									<th class="header"><span class="normalNegroNegrita">Debe</span></th>
									<th class="header"><span class="normalNegroNegrita">Haber</span></th>
									<th class="header" style="width: 65px;"><span class="normalNegroNegrita">Opciones</span></th>
								</tr>
							</tbody>
						</table>
						<br/>
						<div id="accionesEjecutar" align="center">
								<?php 
								if(is_array($GLOBALS['SafiRequestVars']['opciones']) && count($GLOBALS['SafiRequestVars']['opciones'])>0) {
								foreach ($GLOBALS['SafiRequestVars']['opciones'] as $index){ ?>
								    <span class="cadena">
									<input  type="button" value="<?php echo $index['wfop_nombre'];?>"   id="<?php echo $index['wfop_descrip'];?>" onclick="GenerarSiguienteCadena(<?php echo $index['id_cadena_hijo'];?>,0)"  />
								    </span>
								<?php }}
								else {?>
									<input  type="button" value="Modificar" onclick="Modificar(this)"  />
								<?php }
								
								?>			
						</div>
					</fieldset>
					
				</div>	
			</form>
</body>
</html>