<?php
	$form = FormManager::GetForm(FORM_CAJA_CHICA_NUEVA_APERTURA);
	$cajaChica = $form->GetCajaChica();
?>
<html>
	<head>
		<title>.:SAFI:. Ingresar Avance</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		<link href="../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />
		<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
		<style>
			.ui-autocomplete {
				max-height: 110px;
				overflow-y: auto;
				/* prevent horizontal scrollbar */
				overflow-x: hidden;
				/* add padding to account for vertical scrollbar */
				padding-right: 20px;
			}
			/* IE 6 doesn't support max-height
			 * we use height instead, but this forces the menu to always be this tall
			 */
			* html .ui-autocomplete {
				height: 120px;
			}
		</style>
		
		<?php  require_once(SAFI_JAVASCRIPT_PATH.'/init.php') ?>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript" src="../../js/constantes.js"></script>
		<script type="text/javascript" src="../../js/funciones.js"></script>
		
		<script type="text/javascript">
			g_Calendar.setDateFormat('dd/mm/yyyy');

			function onLoad()
			{
				var objInputResponsable = $('#autocompletarResponsable');
				var objInputCustodio = $('#autocompletarCustodio');
				var numItems = 10;

				autoCompleteResponsables({
					objInputResponsable: objInputResponsable,
					numItems: numItems,
					selectFunction: selectAutocompleteResponsable
				});

				autoCompleteResponsables({
					objInputResponsable: objInputCustodio,
					numItems: numItems,
					selectFunction: selectAutocompleteResponsable
				});

				autoCompletePuntoCuenta({
					objInputPuntoCuenta: $('#autocompletePuntoCuenta'),
					numItems: numItems,
					selectFunction: selectAutocompletePuntoCuenta
				});

				autocompleteSolicitudPago({
					objInputSolicitudPago: $('#autocompleteSolicitudPago'),
					numItems: numItems,
					selectFunction: selectAutocompleteSolicitudPago
				});
			}			

			function selectAutocompleteResponsable(event, ui)
			{
				var objInputResponsable = $(this);
				var objTable = objInputResponsable.parents(".wrapperResponsables");
				var objNombreResponsable = objTable.find(".nombreResponsable");
				var objCedulaResponsable = objTable.find(".cedulaResponsable");
				var objInputHiddenCedula = objTable.find(".inputCedulaResponsable");
				
				objNombreResponsable.empty();
				objCedulaResponsable.empty();

				objNombreResponsable.append(document.createTextNode(ui.item.nombres + " " + ui.item.apellidos));
				objCedulaResponsable.append(document.createTextNode(ui.item.id));
				objInputHiddenCedula.val(ui.item.id);
				
				this.value = '';
				return false;
			}

			function autoCompletePuntoCuenta()
			{
				var params = arguments[0] || null;
				var objInputPuntoCuenta = params != null ? (params.objInputPuntoCuenta || null) : null;
				var numItems = params != null ? (params.numItems || null) : null;
				var selectFunction = params != null ? (params.selectFunction || null) : null;

				if(objInputPuntoCuenta == null) return;
				
				objInputPuntoCuenta.autocomplete({
					source: function(request, response){
						$.ajax({
							url: SAFI_URL_ACCIONES_PATH + "/puntoCuenta.php",
							dataType: "json",
							data: {
								accion: "Search",
								tipoRespuesta: 'json',
								key: request.term,
								numItems: numItems
							},
							success: function(json){
								var index = 0;
								var items = new Array();

								$.each(json.listaPuntoCuenta, function(idPuntoCuenta, objPuntoCuenta){

									var label = objPuntoCuenta.id;
									
									items[index++] = {
											id: objPuntoCuenta.id,
											label: label,
											value: objPuntoCuenta.id
									};
								});
								response(items);
							}
						});
					},
					minLength: 1,
					select: selectFunction
				});
			}

			function selectAutocompletePuntoCuenta(event, ui)
			{
				var objInputHiddenIdPuntoCuenta = $('input[name="idPuntoCuenta"]');
				var objIdPuntoCuenta = $(".idPuntoCuenta");
				var objMonto = $(".puntoCuentaMonto");

				objIdPuntoCuenta.empty();
				objMonto.empty();
				
				$.ajax({
					url: SAFI_URL_ACCIONES_PATH + "/puntoCuenta.php",
					dataType: "json",
					data: {
						accion: "GetPuntoCuenta",
						tipoRespuesta: 'json',
						idPuntoCuenta: ui.item.value
					},
					success: function(json){

						$.each(json.listaPuntoCuenta, function(idPuntoCuenta, objPuntoCuenta){
							objInputHiddenIdPuntoCuenta.val(objPuntoCuenta.id);
							objIdPuntoCuenta.append(document.createTextNode(objPuntoCuenta.id));
							objMonto.append(document.createTextNode(objPuntoCuenta.montoSolicitado));
						});
					}
				});

				this.value = '';
				return false;
			}

			function autocompleteSolicitudPago()
			{
				var params = arguments[0] || null;
				var objInputSolicitudPago = params != null ? (params.objInputSolicitudPago || null) : null;
				var numItems = params != null ? (params.numItems || null) : null;
				var selectFunction = params != null ? (params.selectFunction || null) : null;

				if(objInputSolicitudPago == null) return;
				
				objInputSolicitudPago.autocomplete({
					source: function(request, response){
						$.ajax({
							url: SAFI_URL_ACCIONES_PATH + "/solicitudPago.php",
							dataType: "json",
							data: {
								accion: "Search",
								tipoRespuesta: 'json',
								key: request.term,
								numeroItems: numItems
							},
							success: function(json){
								var index = 0;
								var items = new Array();

								$.each(json.listaSolicitudPago, function(idSolicitudPago, objSolicitudPago){

									var label = objSolicitudPago.id;
									
									items[index++] = {
											id: objSolicitudPago.id,
											label: label,
											value: objSolicitudPago.id
									};
								});
								response(items);
							}
						});
					},
					minLength: 1,
					select: selectFunction
				});
			}

			function selectAutocompleteSolicitudPago(event, ui)
			{
				var objInputHiddenIdSolicitudPago = $('input[name="idSolicitudPago"]');
				var objIdSolicitudPago = $(".idSolicitudPago");

				objIdSolicitudPago.empty();

				objInputHiddenIdSolicitudPago.val(ui.item.id);
				objIdSolicitudPago.append(document.createTextNode(ui.item.id));

				this.value = '';
				return false;
			}

			function desactivarBotones()
			{
				$('#btnMultiple').attr('disabled', 'disabled');
			}

			function accionGuardar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea registrar esta apertura de caja chica? ")) 
				{
					desactivarBotones();
					var objForm = $('#cajaChicaAperturaForm');
					objForm.submit();
				}
			}
			
			function accionActualizar(){
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea modificar esta apertura de caja chica? ")) 
				{
					desactivarBotones();
					var objForm = $('#cajaChicaAperturaForm');
					objForm.submit();
				}
			}
			
		</script>
	</head>
	<body class="normal" onload="onLoad();">
		<form id="cajaChicaAperturaForm" action="cajaChicaApertura.php" method="post">
			<input
				id="hiddenAccion"
				type="hidden"
				name="accion"
				value="guardar"
			>
			<table align="center">
				<tr>
					<td><?php include(SAFI_VISTA_PATH . '/mensajes.php');?></td>
				</tr>
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" align="center" background="../../imagenes/fondo_tabla.gif"
							class="tablaalertas content" width="800px;"
						>
							<tr>
								<td colspan="2" class="normalNegroNegrita header documentTitle">.: Caja chica :.</td>
							</tr>
							<tr>
								<td class="normalNegrita">Fecha: </td>
								<td class="normalNegro">
									<input
										type="text"
										size="10"
										id="fechaApertura"
										name="fechaApertura"
										class="dateparse"
										readonly="readonly"
										value="<?php echo $cajaChica->GetFechaApertura(); ?>"
									/>
									<a 
										href="javascript:void(0);" 
										onclick="g_Calendar.show(event, 'fechaApertura');" 
										title="Show popup calendar"
									><img 
											src="../../js/lib/calendarPopup/img/calendar.gif" 
											class="cp_img" 
											alt="Open popup calendar"
									/></a>
								</td>
							</tr>
							<tr>
								<td class="normalNegrita">Justificaci&oacute;n: </td>
								<td>
									<textarea
										name="justificacion"
										class="normalNegro"
										rows="2"
										style="width: 500px;"
									><?php echo $cajaChica->GetJustificacion(); ?></textarea>
								</td>
							</tr>
							<?php
								$cedula = "";
								$nombre = "";
								if(is_object($responsable = $cajaChica->GetResponsable()))
								{
									$cedula = $responsable->GetId();
									$nombre = mb_strtoupper($responsable->GetNombres() . '' . $responsable->GetApellidos(), "ISO-8859-1");
								}
							?>
							<tr>
								<td colspan="2" class="normalNegroNegrita header">Responsable</td>
							</tr>
							<tr>
								<td colspan="2"><table class="wrapperResponsables">
									<tr>
										<td colspan="2"><span class="normalNegrita">Responsable: </span> 
											<input
												id="autocompletarResponsable"
												class="normalNegro autocompletarResponsable"
												type="text"
											>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<table cellpadding="0" cellspacing="0" class="tableSub">
												<tr>
													<td class="normalNegrita">Nombre: </td>
													<td class="nombreResponsable"><?php echo $nombre; ?></td>
													<td class="normalNegrita">C&eacute;dula:
														<input class="inputCedulaResponsable" type="hidden" name="cedulaResponsable"
															value="<?php echo $cedula; ?>">
													</td>
													<td class="cedulaResponsable"><?php echo $cedula; ?></td>
												</tr>
											</table>
										</td>
									</tr>
								</table></td>
							</tr>
							<?php
								$cedula = "";
								$nombre = "";
								if(is_object($custodio = $cajaChica->GetCustodio()))
								{
									$cedula = $custodio->GetId();
									$nombre = mb_strtoupper($custodio->GetNombres() . '' . $custodio->GetApellidos(), "ISO-8859-1");
								}
							?>
							<tr>
								<td colspan="2" class="normalNegroNegrita header">Custodio</td>
							</tr>
							<tr>
								<td colspan="2"><table class="wrapperResponsables">
									<tr>
										<td colspan="2"><span class="normalNegrita">Custodio: </span> 
											<input
												id="autocompletarCustodio"
												class="normalNegro autocompletarResponsable"
												type="text"
											>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<table cellpadding="0" cellspacing="0" class="tableSub">
												<tr>
													<td class="normalNegrita">Nombre: </td>
													<td class="nombreResponsable"><?php echo $nombre; ?></td>
													<td class="normalNegrita">C&eacute;dula:
														<input class="inputCedulaResponsable" type="hidden" name="cedulaCustodio"
															value="<?php echo $cedula; ?>">
													</td>
													<td class="cedulaResponsable"><?php echo $cedula; ?></td>
												</tr>
											</table>
										</td>
									</tr>
								</table></td>
							</tr>
							<tr>
								<td colspan="2" class="normalNegroNegrita header">Punto de cuenta</td>
							</tr>
							<tr>
								<td colspan="2"><table>
									<tr>
										<td>
											<span class="normalNegrita">Punto de cuenta: </span>
											<input 
												id="autocompletePuntoCuenta"
												class="normalNegro"
												type="text"
											>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<table>
												<tr>
													<td class="normalNegrita">Punto cuenta:&nbsp;
														<input type="hidden" name="idPuntoCuenta" value="">
													</td>
													<td class="idPuntoCuenta"></td>
												</tr>
												<tr>
													<td class="normalNegrita">Monto: </td>
													<td class="puntoCuentaMonto"></td>
												</tr>
											</table>
										</td>
									</tr>
								</table></td>
							</tr>
							<tr>
								<td colspan="2" class="normalNegroNegrita header">Solicitud de pago</td>
							</tr>
							<tr>
								<td colspan="2"><table>
									<tr>
										<td>
											<span class="normalNegrita">Solicitud pago: </span>
											<input 
												id="autocompleteSolicitudPago"
												class="normalNegro"
												type="text"
											>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<table>
												<tr>
													<td class="normalNegrita">Solicituda pago:&nbsp;
														<input type="hidden" name="idSolicitudPago" value="">
													</td>
													<td class="idSolicitudPago"></td>
												</tr>
												<tr>
													<td class="normalNegrita">Monto: </td>
													<td class="solicitudpagoMonto"></td>
												</tr>
											</table>
										</td>
									</tr>
								</table></td>
							</tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
						</table>
						<br/>
						<div id="divAcciones" style="text-align: center;">
							<input
								id="btnMultiple"
								type="button"
								class="normalNegro"
								<?php
									if($form->GetTipoOperacion() == CajaChicaNuevaAperturaForm::TIPO_OPERACION_INSERTAR){
										echo 'value="Registar"
											onclick="accionGuardar();"';
									} else if ($form->GetTipoOperacion() == CajaChicaNuevaAperturaForm::TIPO_OPERACION_MODIFICAR){
										echo 'value="Modificar"
											onclick="accionActualizar();"
										';
									}
								?>
							>
						</div>
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>