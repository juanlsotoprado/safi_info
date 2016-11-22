<?php 
	$form = FormManager::GetForm(FORM_NUEVA_RENDICION_VIATICO_NACIONAL);
	$rendicion = $form->GetRendicionViaticoNacional();
	$viatico = $form->GetViatico();
	if($viatico != null){
		$responsable = $viatico->GetResponsable();
	}
	
	$asignaciones = $GLOBALS['SafiRequestVars']['asignaciones'];
	$bancos = $GLOBALS['SafiRequestVars']['bancos'];
?>

<html>
	<head>
		<title>.:SAFI:. Ingresar Rendición de Vi&aacute;tico Nacional</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
		
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../js/constantes.js"></script>
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<script>
			g_Calendar.setDateFormat('dd/mm/yyyy');

			function onLoad()
			{
				establecerFocoInicialCodigoDocumento("idViaticoBuscado");
			}
			
			function calcularReintegro(montoAnticipo, idInputTotalGastos, idDisplayMontoReintegro, idDisplayReintegroMonto)
			{
				var objInputTotalGastos = $("#" + idInputTotalGastos);
				var objDisplayMontoReintegro = $("#" + idDisplayMontoReintegro);
				var objDisplayReintegroMonto = $("#" + idDisplayReintegroMonto);

				var TotalGastos = objInputTotalGastos.val();
				
				objDisplayMontoReintegro.empty();
				objDisplayMontoReintegro.append(montoAnticipo - TotalGastos);

				objDisplayReintegroMonto.empty();
				objDisplayReintegroMonto.append(montoAnticipo - TotalGastos);
			}

			function desactivarBotones()
			{
				$('#btnMultiple').attr('disabled', 'disabled');
				$('#btnGuardarYEnviar').attr('disabled', 'disabled');
			}

			function accionGuardar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea registrar esta rendici"+oACUTE+"n de vi"+aACUTE+"tico nacional?")) 
				{
					desactivarBotones();
					var objForm = $('#rendicionViaticoNacionalForm');
					objForm.submit();
				}
			}

			function guardarYEnviar(objAccion)
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea registrar y enviar esta rendici"+oACUTE+"n de vi"+aACUTE
					+"tico nacional?")) 
				{
					desactivarBotones();
					$('#' + objAccion).val('GuardarYEnviar');
					var objForm = $('#rendicionViaticoNacionalForm');
					objForm.submit();
				}
			}
			
			function accionActualizar(){
				if (
					confirm(pACUTE+"Est"+aACUTE+" seguro que desea modificar esta rendici"+oACUTE+"n de vi"
					+aACUTE+"tico nacional? ")
				){
					desactivarBotones();
					var objForm = $('#rendicionViaticoNacionalForm');
					objForm.submit();
				}
			}

			function accionActualizarYEnviar(objAccion)
			{
				if (
					confirm(pACUTE+"Est"+aACUTE+" seguro que desea modificar y enviar esta rendici"+oACUTE+"n de vi"+aACUTE
					+"tico nacional? ")
				) {
					desactivarBotones();
					$('#' + objAccion).val('actualizarYEnviar');
					var objForm = $('#rendicionViaticoNacionalForm');
					objForm.submit();
				}
			}
			
		</script>
		
	</head>
	
	<body class="normal" onload="onLoad();">
		<table align="center">
			<tr>
				<td><?php include(SAFI_VISTA_PATH . '/mensajes.php');?></td>
			</tr>
			<?php
				if ($form->GetTipoOperacion() == NuevaRendicionViaticoNacionalForm::TIPO_OPERACION_INSERTAR){
			?>
			<tr>
				<td>
					<form name="viaticoNacionalBuscarForm" id="viaticoNacionalBuscarForm" method="post" action="rendicion.php">
						<input type="hidden" name="accion" value="buscarViaticoNacional">
						<table cellpadding="0" cellspacing="0" width="640" align="center"
							background="../../imagenes/fondo_tabla.gif" class="tablaalertas"
						>
							<tr> 
			    				<td height="21" colspan="2" class="normalNegroNegrita header" align="left">
			    					Seleccionar vi&aacute;tico nacional
			    				</td>
							</tr>
			  				<tr>
								<td height="10" colspan="2"></td>
							</tr>
			  				<tr>
			  					<td class="normalNegrita">C&oacute;digo del vi&aacute;tico</td>
			  					<td>
			  						<input
			  							<?php echo "autocomplete=\"off\"" ?>
			  							type="text"
			  							id="idViaticoBuscado"
			  							name="idViaticoBuscado"
			  							class="normalNegro"
			  							value="<?php echo $form->GetIdViaticoBuscado() ?>"
			  						>
			  					</td>
			  				</tr>
						</table>
						<br/>
						<div align="center">
							<input type="submit" value="Buscar" class="normalNegro">
						</div>
					</form>				
				</td>
			</tr>
			<?php
				}
				if(
					!empty($viatico) && $viatico instanceof EntidadViaticoNacional &&
					!empty($rendicion) && $rendicion instanceof EntidadRendicionViaticoNacional
				){
			?>
			<tr><td>
				<form
					name="rendicionViaticoNacionalForm"
					id="rendicionViaticoNacionalForm"
					method="post"
					action="rendicion.php"
					enctype="multipart/form-data"
				>
					<input
						type="hidden"
						name="idViaticoNacional"
						value="<?php echo $viatico->GetId() ?>"
					>
					<input
						id="hiddenAccion"
						type="hidden"
						name="accion"
						value="<?php
							if($form->GetTipoOperacion() == NuevaRendicionViaticoNacionalForm::TIPO_OPERACION_INSERTAR){
								echo 'Guardar';
							} else if ($form->GetTipoOperacion() == NuevaRendicionViaticoNacionalForm::TIPO_OPERACION_MODIFICAR){
								echo 'Actualizar';
							}
						?>"
					>
					<?php
						if ($form->GetTipoOperacion() == NuevaRendicionViaticoNacionalForm::TIPO_OPERACION_MODIFICAR){
							echo '
					<input
						type="hidden"
						name="idRendicion"
						value="'.$rendicion->GetId().'"
					>
							';
						}
					?>
					<table align="center">
						<tr>
							<td><table align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas content"
								width="800px;" cellpadding="0" cellspacing="0"
							>
								<tr>
									<td colspan="2" class="header normalNegroNegrita documentTitle">
										.: Rendici&oacute;n de vi&aacute;tico nacional <?php
										if ($form->GetTipoOperacion() == NuevaRendicionViaticoNacionalForm::TIPO_OPERACION_MODIFICAR)
										{
											echo $rendicion->GetId()." ";
										}
									?>:.</td>
								</tr>
								<!-- 
								<tr>
									<td class="normalNegrita">Fecha de registro</td>
									<td class="normalNegro">
										<?php //echo $rendicion->GetFechaRegistro() ?>
									</td>
								</tr>
								 -->
								<tr>
									<td class="normalNegrita">Fecha de la rendici&oacute;n</td>
									<td class="normalNegro">
										<input
											type="text"
											size="10"
											id="fechaRendicion"
											name="fechaRendicion"
											class="dateparse"
											readonly="readonly"
											value="<?php
												echo $rendicion->GetFechaRendicion()
											?>"
										/>
										<!-- 
										<a 
											href="javascript:void(0);" 
											onclick="g_Calendar.show(event, 'fechaRendicion');" 
											title="Show popup calendar"
										><img 
												src="../../js/lib/calendarPopup/img/calendar.gif" 
												class="cp_img" 
												alt="Open popup calendar"
										/></a>
										 -->
									</td>
								</tr>
								<tr>
									<td class="normalNegrita">Informe de la rendici&oacute;n</td>
									<td>
										<a href="<?php
											$path = "../..".str_replace(SAFI_BASE_PATH, "", SAFI_UPLOAD_RENDICION_VIATICO_NACIONAL_PATH);
											echo $path."/".$rendicion->GetInformeFileName()
										?>">
											<?php echo $rendicion->GetInformeFileName()?>
										</a>
										<?php
											if ($form->GetTipoOperacion() == NuevaRendicionViaticoNacionalForm::TIPO_OPERACION_INSERTAR){
										?>
										<input class="normalNegro" type="file" name="informe">
										<?php
											}
										?>
									</td>
								</tr>
								<tr>
									<td class="normalNegrita">C&oacute;digo del vi&aacute;tico</td>
									<td class="normalNegro"><?php echo $viatico->GetId()?></td>
								</tr>
								<tr>
									<td  class="normalNegrita">Fecha del vi&aacute;tico:</td>
									<td><?php echo $viatico->GetFechaViatico() ?></td>
								</tr>
								<tr>
									<td colspan="2" class="header normalNegroNegrita">Datos del responsable</td>
								</tr>
								<tr>
									<td colspan="2"><?php
										if($responsable != null){
											echo '
										<table style="width: 100%;" cellpadding="0" cellspacing="0">
											<tr>
												<td><span class="normalNegrita">C&eacute;dula</span></td>
												<td><span class="normalNegrita">Nombre</span></td>
												<td><span class="normalNegrita">Tipo</span></td>
											</tr>
											<tr>
											<td>'.$responsable->GetCedula().'</td>
											<td>'.mb_strtoupper($responsable->GetNombres().' '. $responsable->GetApellidos(), 'ISO-8859-1').'</td>
											<td>'.
												(strcmp($responsable->GetTipoResponsable(), 
													EntidadResponsableViatico::TIPO_EMPLEADO)== 0 ? 'Empleado' :
													(
														strcmp($responsable->GetTipoResponsable(),
															EntidadResponsableViatico::TIPO_BENEFICIARIO)== 0 ?
																$responsable->GetTipoEmpleado() : ''
													)
												)
											.'</td>
											</tr>
										</table>
											';
										}
									?></td>
								</tr>
								<tr>
									<td colspan="2" class="header normalNegroNegrita">Proyecto/Acci&oacute;n centralizada</td>
								</tr>
								<tr>
									<td><span class="normalNegrita"><?php 
										if($viatico->GetProyecto() != null){
											echo "Proyecto:";
										} else if($viatico->GetAccionCentralizada() != null){
											echo "Acci&oacute;n Centralizada:";
										}
									?></span></td>
									<td><?php
										if($viatico->GetProyecto() != null){
											echo $viatico->GetProyecto()->GetNombre();
										} else if($viatico->GetAccionCentralizada() != null){
											echo $viatico->GetAccionCentralizada()->GetNombre();
										}
									?></td>
								</tr>
								<tr>
									<td><span class="normalNegrita">Acci&oacute;n espec&iacute;fica: </span></td>
									<td><?php
										if( $viatico->GetProyectoEspecifica() != null){
											$especifica = $viatico->GetProyectoEspecifica(); 
											echo '(' .$especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto().') '.$especifica->GetNombre();	
										} else if($viatico->GetAccionCentralizadaEspecifica() != null){
											$especifica = $viatico->GetAccionCentralizadaEspecifica();
											echo '(' .$especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto().') '.$especifica->GetNombre();
										}
									?></td>
								</tr>
								<tr>
									<td colspan="2" class="header normalNegroNegrita">Datos del viaje</td>
								</tr>
								<tr>
									<td><span class="normalNegrita">Fecha inicio del viaje(*)</span></td>
									<td>
										<?php echo $viatico->GetFechaInicioViaje()?>
										&nbsp;
										&nbsp;
										&nbsp;
										<input
											type="text"
											size="10"
											id="txt_inicio"
											name="fechaInicioViaje"
											class="dateparse"
											readonly="readonly"
											onfocus="javascript: compararFechasYBorrarById('txt_inicio', 'hid_hasta_itin', 'txt_inicio');"
											value="<?php echo $rendicion->GetFechaInicioViaje() ?>"
										/>
										<a 
											href="javascript:void(0);" 
											onclick="g_Calendar.show(event, 'txt_inicio');" 
											title="Show popup calendar"
										><img 
												src="../../js/lib/calendarPopup/img/calendar.gif" 
												class="cp_img" 
												alt="Open popup calendar"
										/></a>
									</td>
								</tr>
								<tr>
									<td><span class="normalNegrita">Fecha fin del viaje(*)</span></td>
									<td>
										<?php echo $viatico->GetFechaFinViaje()?>
										&nbsp;
										&nbsp;
										&nbsp;
										<input
											type="text"
											size="10"
											id=hid_hasta_itin
											name="fechaFinViaje"
											class="dateparse"
											readonly="readonly"
											onfocus="javascript: compararFechasYBorrarById('txt_inicio', 'hid_hasta_itin', 'hid_hasta_itin');"
											value="<?php echo $rendicion->GetFechaFinViaje() ?>"
										/>
										<a 
											href="javascript:void(0);" 
											onclick="g_Calendar.show(event, 'hid_hasta_itin');" 
											title="Show popup calendar"
										><img 
												src="../../js/lib/calendarPopup/img/calendar.gif" 
												class="cp_img" 
												alt="Open popup calendar"
										/></a>		
									</td>
								</tr>
								<tr>
									<td colspan="2" class="header normalNegroNegrita">Logros alcanzados</td>
								</tr>
								<tr>
									<td class="normalNegrita">Objetivos del viaje del vi&aacute;tico(*)</td>
									<td>
										<?php echo $viatico->GetObjetivosViaje(); ?>
									</td>
								</tr>
								<tr>
									<td class="normalNegrita mensajeError">Nota:</td>
									<td class="mensajeError">
										- Se debe indicar el n&uacute;mero de participantes en los logros alcanzados.<br />
										- Los objetivos del viaje del vi&aacute;tico nacional se colocan como ayuda, pero no deben ser
											iguales a los logros alcanzados en la rendici&oacute;n del vi&aacute;tico nacional.
											Por favor no realizar la operaci&oacute;n de copiado y pegado sobre estos datos.
									</td>
								</tr>
								<tr>
									<td class="normalNegrita">Logros alcanzados(*)</td>
									<td><textarea name="objetivosViaje" rows="4" cols="80"><?php
										echo $rendicion->GetObjetivosViaje()
									?></textarea></td>
								</tr>
								<tr>
									<td colspan="2"><table class="tablaalertas content" cellpadding="0" cellspacing="0" style="width: 100%">
										<tr>
											<td class="header normalNegroNegrita">Asignaci&oacute;n</td>
											<td class="header normalNegroNegrita">Monto</td>
											<td class="header normalNegroNegrita">Unidad de medida</td>
											<td class="header normalNegroNegrita">Unidades</td>
											<td class="header normalNegroNegrita">Subtotal</td>
										</tr>
										<?php
											$totalMonto = 0.0;
											if(is_array($viatico->GetViaticoResponsableAsignaciones())){
												$viaticoRespAsignaciones = $viatico->GetViaticoResponsableAsignaciones();
												foreach($viaticoRespAsignaciones as $codigoAsignacion => $viaticoRespAsignacion){
													$asignacion = $asignaciones[$codigoAsignacion];
													$totalMonto += $viaticoRespAsignacion->GetMonto() * $viaticoRespAsignacion->GetUnidades();
										?>
										<tr>
											<td class="normalNegrita"><?php echo $asignacion->GetNombre() ?></td>
											<td><?php echo $viaticoRespAsignacion->GetMonto() ?></td>
											<td><?php
												switch($asignacion->GetUnidadMedida()){
													case EntidadAsignacionViatico::UNIDAD_MEDIDA_POR_NOCHE:
														echo "Por noche";
														break;
													case EntidadAsignacionViatico::UNIDAD_MEDIDA_DIARIO:
														echo "Diario";
														break;
													case EntidadAsignacionViatico::UNIDAD_MEDIDA_POR_TRASLADO:
														echo "Por traslado";
														break;
													case EntidadAsignacionViatico::UNIDAD_MEDIDA_POR_VIAJE:
														echo "Por viaje";
														break;
													case EntidadAsignacionViatico::UNIDAD_MEDIDA_POR_KILOMETRO:
														echo "Por km";
														break;
												}
											?></td>
											<td><?php echo $viaticoRespAsignacion->GetUnidades() ?></td>
											<td><?php echo $viaticoRespAsignacion->GetMonto() * $viaticoRespAsignacion->GetUnidades() ?></td>
										</tr>
										<?php	
												}
											}
										?>
										<tr>
											<td colspan="3"></td>
											<td class="normalNegrita">Anticipo entregado</td>
											<td><?php echo $totalMonto ?></td>
										</tr>
										<tr>
											<td colspan="3"></td>
											<td class="normalNegrita">Total gastos(*)</td>
											<td><input
												id="totalGastos"
												name="totalGastos"
												<?php echo "autocomplete=\"off\"" ?>
												value="<?php echo $rendicion->GetTotalGastos() ?>"
												onkeyup="calcularReintegro('<?php echo $totalMonto ?>', 'totalGastos',
													'tdMontoReintegro', 'reintegroMonto');"
											>
											</td>
										</tr>
										<tr>
											<td colspan="3"></td>
											<td class="normalNegrita">Reintegro a la fundaci&oacute;n</td>
											<td id="tdMontoReintegro"><?php echo $totalMonto - $rendicion->GetTotalGastos() ?></td>
										</tr>
									</table></td>
								</tr>
								<tr>
									<td colspan="2" class="header normalNegroNegrita">Datos del reintegro</td>
								</tr>
								<tr>
									<td class="normalNegrita">Banco</td>
									<td>
										<select name="reintegroIdBanco" class="normalNegro" name="banco">
											<option value="0">Seleccionar...</option>
											<?php
												if(is_array($bancos)){
													$IdBancoActual = (
														$rendicion->GetReintegroBanco() != null &&
														$rendicion->GetReintegroBanco() instanceof EntidadBanco
													) ? $rendicion->GetReintegroBanco()->GetId() : "";
															 
													foreach ($bancos as $banco){
														if($banco->GetId() == $IdBancoActual){
															$selected = ' selected = "selected"';
														} else
															$selected = '';
														
														echo '
											<option
												value="'.$banco->GetId().'"
												'.$selected.'
											>
												'. mb_strtoupper($banco->GetNombre(), "ISO-8859-1").'
											</option>
														';
													}
												}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="normalNegrita">Nro. referencia</td>
									<td><input
										name="reintegroReferencia"
										class="normalNegro"
										type="text"
										<?php echo "autocomplete=\"off\"" ?>
										value="<?php echo $rendicion->GetReintegroReferencia() ?>"
									></td>
								</tr>
								<tr>
									<td class="normalNegrita">Fecha</td>
									<td>
										<input
											type="text"
											size="10"
											id="reintegroFecha"
											name="reintegroFecha"
											class="dateparse"
											value="<?php echo $rendicion->GetReintegroFecha() ?>"
										/>
										<a 
											href="javascript:void(0);" 
											onclick="g_Calendar.show(event, 'reintegroFecha');" 
											title="Show popup calendar"
										><img 
												src="../../js/lib/calendarPopup/img/calendar.gif" 
												class="cp_img" 
												alt="Open popup calendar"
										/></a>
									</td>
								</tr>
								<tr>
									<td class="normalNegrita">Monto</td>
									<td id="reintegroMonto"><?php echo $totalMonto - $rendicion->GetTotalGastos() ?></td>
								</tr>
								<tr>
									<td colspan="2" class="header normalNegroNegrita">Observaciones</td>
								</tr>
								<tr>
									<td colspan="2">
										<textarea
											name="observaciones"
											class="normalNegro"
											rows="3"
											cols="120"
										><?php echo $rendicion->GetObservaciones()?></textarea>
									</td>
								</tr>
							</table></td>
						</tr>
						<tr>
							<td class="normal"><div class="ptotal"><span class="peq_naranja">(*)</span> Campo obligatorio</div></td>
						</tr>
					</table>
					<br/>
					<div id="divAcciones" style="text-align: center;">
						<input
							id="btnMultiple"
							type="button"
							class="normalNegro"
							<?php
								if($form->GetTipoOperacion() == NuevaRendicionViaticoNacionalForm::TIPO_OPERACION_INSERTAR){
									echo 'value="Registrar"
										onclick="accionGuardar();"';
								} else if ($form->GetTipoOperacion() == NuevaRendicionViaticoNacionalForm::TIPO_OPERACION_MODIFICAR){
									echo 'value="Modificar"
										onclick="accionActualizar();"
									';
								}
							?>
						>
						<input
							id="btnGuardarYEnviar"
							type="button"
							class="normalNegro"
							<?php 
								if($form->GetTipoOperacion() == NuevaRendicionViaticoNacionalForm::TIPO_OPERACION_INSERTAR){
									echo 'value="Registrar y enviar"
										onclick="guardarYEnviar(\'hiddenAccion\');"';
								} else if ($form->GetTipoOperacion() == NuevaRendicionViaticoNacionalForm::TIPO_OPERACION_MODIFICAR){
									echo 'value="Modificar y enviar"
										onclick="accionActualizarYEnviar(\'hiddenAccion\');"
									';
								}
							?>
						>
					</div>
				</form></td>
			</tr>
			<?php	
				}
			?>
		</table>
	</body>
</html>
