 <?php

include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');


$objPmod =  $GLOBALS['SafiRequestVars']['pmod'] ;

if($objPmod){
	$objPmod->UTF8Encode();
	$objPmodArray = $objPmod->ToArray();
}


if($objPmod){

	$pmod = $objPmod != null? utf8_decode($objPmod->GetId())  : '';
	$fecha = $objPmod  != null? utf8_decode($objPmod->GetFecha())  : '';

	$fechahora = explode (' ',$fecha);

	$fecha = explode ('-',$fechahora[0]);

	$fecha  =  $fecha[0].'/'.$fecha[1].'/'.$fecha[2];

	$observacion = $objPmod  != null? utf8_decode($objPmod->GetObservacion())  : '';
	$tipo = $objPmod != null? utf8_decode($objPmod->GetTipoDoc()) : '';
	
	$pmodImputa = $objPmodArray != null ? $objPmodArray['mpresupuestariaImputas']  : null;
	$pmodRespaldo = $objPmodArray != null ? $objPmodArray['respaldos']  : null;


}

?>

<html>
<head>
<title>.:SAFI:. Sopg</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />
<?php require(SAFI_JAVASCRIPT_PATH.'/init.php'); ?>
<link href="<?=GetConfig("siteURL").'/css/safi0.2.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />
<?php require(SAFI_INCLUDE_PATH.'/fechaJs.php');?> <script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>" charset="utf-8"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/constantes.js';?>"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/funciones.js';?>"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/func_montletra.js';?>" charset="utf-8"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/uploadify/uploadify/jquery.uploadify.min.js';?>" charset="utf-8"></script>
<script language="javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/func_montletra.jss';?>"></script>
<link type="text/css" href="<?=SAFI_URL_JAVASCRIPT_PATH.'lib/calendarPopup/css/calpopup.css';?>" media="screen" rel="stylesheet" />
<link type="text/css" href="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib//uploadify/uploadify/uploadify.css';?>" media="screen" rel="stylesheet" />
<!-- jQuery and jQuery UI -->
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/jquery-ui-1.8.13.custom.min.js';?>" charset="utf-8"></script>
<link 	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />
<!-- elRTE -->
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/elrte.min.js';?>" charset="utf-8"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/elRTE.options.js';?>"	charset="utf-8"></script>
<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/elrte.min.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />
<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/elrte-inner.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />
<!-- elRTE translation messages -->
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/i18n/elrte.es.js';?>" charset="utf-8"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/sopg/sopgNuevo.js';?>" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">PHPSESSID = '<?php echo $_COOKIE['PHPSESSID'];?>';</script>
<script type="text/javascript" charset="utf-8">
	//  pmodImputa = <?php //echo json_encode($pmodImputa,JSON_FORCE_OBJECT); ?>;
	// pmodRespaldo = <?php //echo json_encode($pmodRespaldo,JSON_FORCE_OBJECT); ?>;
	estados =  <?php echo json_encode($GLOBALS['SafiRequestVars']['estadosVenezuela'],JSON_FORCE_OBJECT); ?>;
	ivas = <?php echo json_encode($GLOBALS['SafiRequestVars']['getPocentajesIva'],JSON_FORCE_OBJECT); ?>;
</script>
 
<style type="text/css">
.uploadify-button {
	background: transparent;
	border: none;
	padding-left: 0;
	background-image: url('../../js/lib/uploadify/examinar.png');
	border: 0;
}
.uploadify:hover .uploadify-button {
	background: transparent;
	border: none;
	background-image: url('../../js/lib/uploadify/examinar2.png');
	border: 0;
}
.classname {
	-moz-box-shadow: inset 0px 1px 3px -7px #caefab;
	-webkit-box-shadow: inset 0px 1px 3px -7px #caefab;
	box-shadow: inset 0px 1px 3px -7px #caefab;
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #6cad32
		), color-stop(1, #1d3807) );
	background: -moz-linear-gradient(center top, #6cad32 5%, #1d3807 100%);
	filter: progid : DXImageTransform.Microsoft.gradient ( startColorstr =
		'#6cad32', endColorstr = '#1d3807' );
	background-color: #6cad32;
	-moz-border-radius: 14px;
	-webkit-border-radius: 14px;
	border-radius: 14px;
	border: 1px solid #56c742;
	display: inline-block;
	font-family: arial;
	font-size: 15px;
	font-weight: 100;
	padding: 3px 20px;
}

.classname:hover {
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #1d3807
		), color-stop(1, #6cad32) );
	background: -moz-linear-gradient(center top, #1d3807 5%, #6cad32 100%);
	filter: progid : DXImageTransform.Microsoft.gradient ( startColorstr =
		'#1d3807', endColorstr = '#6cad32' );
	background-color: #1d3807;
}

.classname:active {
	position: relative;
	top: 1px;
}

.switch-wrapper {
  display: inline-block;
  position: relative;
  top: 3px;
}

	/* Used for the Switch effect: */
	
	.cb-enable, .cb-disable, .cb-enable span, .cb-disable span { background: url(<?=SAFI_URL_JAVASCRIPT_PATH ?>/switchesboton/switch.gif) repeat-x; display: block; float: left; }
	.cb-enable span, .cb-disable span { line-height: 22px; display: block; background-repeat: no-repeat; font-weight: bold; }
	.cb-enable span { background-position: left -90px; padding: 0 10px; }
	.cb-disable span { background-position: right -180px;padding: 0 10px; }
	.cb-disable.selected { background-position: 0 -30px; }
	.cb-disable.selected span { background-position: right -210px; color: #fff; }
	.cb-enable.selected { background-position: 0 -60px; }
	.cb-enable.selected span { background-position: left -150px; color: #fff; }

</style>
</head>

<body>
<?php

include(SAFI_VISTA_PATH . '/mensajes.php');?>
	<form name="formspg" id="formspg" method="post" action="">
		<table cellpadding="0" cellspacing="0" align="center"
			style="width: 100%;"
			class="tablaalertas">
			<tr>
				<th class="header"><span class="normalNegroNegrita">.: Solicitud de
						Pago :. <?php  if($sopg){echo "<span id= 'pmodAmodificar'>".$sopg."</span>";}; ?>
				</span></th>

			</tr>
			<tr>
				<td>
					<table style="width: 100%;">
						<tr>
							<td>
								<fieldset style="width: 45%;">
									<legend>
										<b style="font-size: 16px">Detalle del pago:</b>
									</legend>

									<div>

										<table border="0">

											<tr>

												<td><strong class="normalNegrita">Tipo solicitud:</strong>
												</td>

												<td><select class="normalNegrita" id="tiposolicitud"
													name="tiposolicitud">
														<option value="" selected="selected">.:Todos:.</option>
														<option value="" selected="selected">Otro</option>


												</select>
												</td>

											</tr>

											<tr>

												<td><strong class="normalNegrita">Cod. Documento: </strong>

												</td>

												<td><input type="text" id="codigoDocumento"
													name="codigoDocumento" value="<?php echo $tiposolicitud ?>"
													size="35" /> <input type="hidden" name="tiposolicitudVal"
													id="tiposolicitudVal"
													value="<?php echo $tiposolicitudVal ?>">
												</td>

											</tr>
											<tr>

												<td><strong class="normalNegrita">Dependencia Solicitante:</strong>

												</td>
												<td><select class="normalNegrita" id="DependenciaPcta"
													name="DependenciaPcta">
														<option value="" selected="selected">.:Todos:.</option>
														<?php

														if($GLOBALS['SafiRequestVars']['DependenciaPcta']){
															foreach ( $GLOBALS['SafiRequestVars']['DependenciaPcta'] as $obj){ ?>

														<option
														<?php echo $params['DependenciaPcta'] == $obj->GetId()? 'selected': ''  ?>
															value="<? echo $obj->GetId(); ?>">
															<? echo $obj->GetNombre(); ?>
														</option>

														<?php } }?>

												</select>
												</td>
											</tr>
											<tr>
												<td><strong class="normalNegrita">Categor&iacute;a del pago:
												</strong>
												</td>
												<td><input type="hidden" name="catPagoVal" id="catPagoVal"
													value="<?php echo $catPagoVal ?>"> <input type="text"
													id="catPago" name="catPago" value="<?php echo $catPago?>"
													size="35" />
												</td>
											</tr>
											
											
											<tr>
												<td><strong class="normalNegrita">Posee factura:
												</strong>
												</td>
												<td>
												
												<p  style="border: 1px solid white;" class="field switch">
		<label class="cb-enable"><span>Si</span></label>
		<label class="cb-disable selected"><span>No</span></label>
		<input style="display: none" type="checkbox" id="checkbox" class="checkbox" name="poseefactura" />
		
	</p>
												</td>
											</tr>
											
											
	

											<tr>
												<td><strong class="normalNegrita">Observaci&oacute;n: </strong>

												</td>
												<td><textarea rows="3" name="observaciones"id="observaciones" cols="50"><?php echo $observacion ?></textarea>
												</td>
											</tr>

											<tr>
												<td><strong class="normalNegrita">Motivo: </strong>
												</td>
												<td><textarea rows="3" name="motivo" id="motivo" cols="50"><?php echo $motivo ?></textarea>
												</td>
											</tr>
										</table>
									</div>
								</fieldset> <br />
							</td>
						</tr>
						<tr>
							<td>
								<fieldset style="background: white; width: 45%">
									<legend>
										<b style="font-size: 16px">Imputaci&oacute;n Presupuestaria:</b>
									</legend>

									<div>
										<strong style="margin-right: 130px;" class="normalNegrita">Compromiso: </strong><input type="text" name="compromiso"
											id="compromiso" class="normalNegro" size="30"
											value="<?php echo $Compromiso ?>" /> <span
											id="AgregarnuevoCompromiso"> <-- <b class="links"
											style="color: #005E20;"> Agregar</b> </span> <input
											type="hidden" name="compromisoVal" id="compromisoVal"
											value=""> <input type="hidden" name="compromisoff"
											id="compromisoff" value=""> <input type="hidden"
											name="compromisoproveedor" id="compromisoproveedor" value="">
											<input type="hidden" name="compromisotipo" id="compromisotipo" value="">
										<br /> <br /> <br />
										<table class="tablaalertas" id="tablacompselec" border="1"
											tyle="width: 100%;">
											<tbody id="tbodycompselec">
												<tr>

													<th class="header"><span class="normalNegroNegrita">Compromiso</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Fuente
															de Financianmiento</span></th>
													<th class="header"><span class="normalNegroNegrita">Proveedor</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">
															Opci&oacute;n </span>
													</th>
												</tr>
											</tbody>
										</table>
										<br>

									</div>
								</fieldset> <br> <br>

								<fieldset id="compfieldset" class="prueba"
									style="background: white; width: 45%">
									<legend>
										<b style="font-size: 16px">Partidas:</b>
									</legend>
									<table cellpadding="0" cellspacing="0" align="center"
										style="width: 100%;"
										background="../../imagenes/fondo_tabla.gif"
										class="tablaalertas" border=1 id='CategoriaPartidasopgPadre'>
										<tbody id="tablaCategoriaPartidasopg">
											<tr>
											
												<th class="header normalNegroNegrita">Partida</th>
												<th class="header normalNegroNegrita">Denominaci&oacute;n</th>
												<th class="header normalNegroNegrita">Proy/Acc</th>
												<th class="header normalNegroNegrita">Monto total</th>
										<!-- 	<th class="header normalNegroNegrita"  id="mutilizar">Monto a utilizar</th>
												<th class="header normalNegroNegrita" id="mexento" >Monto exento</th>
												<th class="header normalNegroNegrita"  id="msujeto">Monto sujeto</th>
										 -->			
											</tr>

										</tbody>
									</table>


								</fieldset>
							</td>
						</tr>
						<tr>
							<td>
								<fieldset id="benefieldset" class="prueba"
									style="background: white; width: 50%">
									<legend>
										<b style="font-size: 16px">Datos del Beneficiario:</b>
									</legend>

									<div>
									
								
										<strong id="montopordefectoSpan"  style="margin-right: 92px;" class="normalNegrita">Monto por defecto: </strong> <input type="text"
											id="montopordefecto"
											class="normalNegro" size="12"
											value="" />
											</br>
											</br> 
							
									
										<strong style="margin-right: 40px;" class="normalNegrita">Empleado/
											Proveedor/ Otros:</strong> <input type="text"
											name="ProveedorSugerido" id="ProveedorSugerido"
											class="normalNegro" size="47"
											value="<?php echo $proveedorSugerido ?>" /> <span
											id="Agregarnuevo">  <b class="links"
											style="color: #005E20;"> Agregar</b> </span> <input
											type="hidden" name="ProveedorSugeridoval"
											id="ProveedorSugeridoValor" value=""> <input type="hidden"
											name="ProveedorSugeridoNombre" id="ProveedorSugeridoNombre"
											value=""> <br /> <br /> <br />
										<table class="tablaalertas" id="tablabeneficiarios" border="1"
											style="width: 100%;">
											<tbody id="tbodyneficiarios">
												<tr>
													<th class="header"><span class="normalNegroNegrita">#</span>
													</th>
													
													<th class="header"><span class="normalNegroNegrita">C&eacute;dula/Rif</span></th>
													<th class="header"><span class="normalNegroNegrita">Nombre</span>
													</th>

													<th class="header"><span class="normalNegroNegrita">Tipo</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Observaci&oacute;n</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Estado</span>
													</th>
													<th class="header" id="MontoasignadoBeneficiario" ><span class="normalNegroNegrita">Monto asignado</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Opci&oacute;n</span>
													</th>
												</tr>
											</tbody>
										</table>

									</div>
								</fieldset> <br />
							</td>

						</tr>
						<tr>
							<td>
								<fieldset id="facturasFilset"
									style="background: white; width: 15%">
									<legend>
										<b style="font-size: 16px">Facturaci&oacute;n:</b>
									</legend>
									<a href="javascript:void(0);" class="classname"
										style='color: white;'>Agregar factura</a> <br /> <br />

									<table cellpadding="0" cellspacing="0" align="center"
										style="width: 100%; font-size: 14px;"
										background="../../imagenes/fondo_tabla.gif"
										class="tablaalertas" id='facturas'>
										
										<tr>
												<th class="header normalNegroNegrita">#</th>
												<th class="header normalNegroNegrita">Beneficiario</th>
												<th class="header normalNegroNegrita">N&#176; factura</th>
												<th class="header normalNegroNegrita">N&#176; control</th>
												<th class="header normalNegroNegrita">Fecha</th>
												<th class="header normalNegroNegrita">Monto total</th>
											<!--  	<th class="header normalNegroNegrita">Monto a utilizar</th> -->
												<th class="header normalNegroNegrita">Monto exento</th>
												<th class="header normalNegroNegrita">Monto sujeto</th>
												<th class="header normalNegroNegrita">Total monto Iva</th>
												<th class="header normalNegroNegrita">Iva (%)</th>
												<th class="header normalNegroNegrita">Iva</th>
												<th class="header normalNegroNegrita">Opciones</th>
											</tr>
										
										<tbody id="tbodyfacturas">

										</tbody>
									</table>

								</fieldset>
							</td>
						</tr>
					</table>
				</td>
			</tr>
						<tr>
							<td>
							<br/>
				 			<fieldset style="width: 400px;">
								<legend ><b style="font-size: 16px">Respaldos digitales</b></legend>
								<table > 
									<tr id="trActualesDigital">     	     
                              	    	<th class="normalNegroNegrita" style="color:#585858" align="left"> * Actuales</th>	
                              	    </tr>
									<tbody id="tbodyRespDigitales" ></tbody>
                                 	<tr>
                              	    	<th class="normalNegroNegrita" align="left" style="color:#585858;"> * Por subir</th>                   	    
                              	    </tr>
                                 	<tr>
										<td>
                              	        	<div align="left" class="normal">
												<strong style="margin-right: 136px;">Seleccione los archivos (m&aacute;ximo 5): </strong>
												<br/><br/>
						     					<input id="file_upload" name="file_upload" type="file" multiple="true">
											</div>
										</td>
                              	    
									</tr>
								</table>
							</fieldset>
						 	</td>
						</tr>
						<tr>
							<td>
							<br/>
								<fieldset style="width: 400px;">
									<legend ><b>Respaldos f&iacute;sicos</b></legend>
									<table > 
										<tr id="trActualesFis">     	     
											<th class="normalNegroNegrita" style="color:#585858" align="left"> * Actuales</th>	
										</tr>
										<tbody id="tbodyRespFisico" >
										</tbody>
										<tr>
											<th class="normalNegroNegrita" align="left" style="color:#585858;"> * Por subir</th>	
										</tr>
										<tr>
											<td>
												<div align="left" class="normal">
													<strong style="margin-right:20px;">Respaldo: </strong>
													<input id="pctaRespaldoFisico" name="pctaRespaldoFisico" type="text" >
													<b class="links"  id="AgregarRespaldoFisico" > Agregar</b>
													<div id="cuerpoRespaldoFisico" >
														<br/>
														<table background="../../imagenes/fondo_tabla.gif" class="tablaalertas" border="1"  style="width:300px;" >
															<tbody id="tablaBodyRespaldoFisico" >
																<tr>
																	<th class="header"><span class="normalNegroNegrita">Nombre</span></th>
																	<th class="header"><span class="normalNegroNegrita">Eliminar</span></th>									
																</tr>
															</tbody>
														</table>                              
													</div>
												</div>
											</td>
										</tr>
									</table>
								</fieldset>
							<br/><br/>
							</td>
						</tr>
		</table>
		<br/><br/>
		<div align="center" id="accionesEjecutar">
			<?php 
				foreach ($GLOBALS['SafiRequestVars']['opciones'] as $index){ ?>
		    		<span class="cadena">
					<input  type="button" value="<?php echo $index['wfop_nombre'];?>"   id="<?php echo $index['wfop_descrip'];?>" onclick="GenerarSigienteCadena(<?php echo $index['id_cadena_hijo'];?>,0)"  />
		    		</span>
			<?php }?>
			
			 <input type="reset" value="Limpiar" id="reset" name="reset" />

		</div>

		<div align="center" id="opcionModificar"></div>

		<input type="hidden" value="" name="regisFisDigiEli"
			id="regisFisDigiEli" /> <input type="hidden" value=""
			name="regisNombreDigital" id="regisNombreDigital" />
			<input type="hidden" value=""
			name="tipocatpago" id="tipocatpago" />

	</form>

</body>
</html>