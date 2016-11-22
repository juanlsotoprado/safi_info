<?php

	require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');
	
	$form = FormManager::GetForm(FORM_REPORTE_RESPONSABLES_VIATICO);
	$datosViaticos = null;
	if($form != null && $form instanceof ReporteResponsablesViaticoForm){
		$datosViaticos = $form->GetDatosViaticos();
	}
	
	$estados = $GLOBALS['SafiRequestVars']['estados'];	
	$regionReportes = $GLOBALS['SafiRequestVars']['regionReportes'];
	$arrRegionReporteEstados = $GLOBALS['SafiRequestVars']['arrRegionReporteEstados'];
	$arrEstatus = $GLOBALS['SafiRequestVars']['arrEstatus'];
	$accionesEspecificas = $GLOBALS['SafiRequestVars']['accionesEspecificas'];	

?>
<html>

	<head>
		<title>.:SAFI:. Historial de Vi&aacute;ticos</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
		<!-- <link href="../../js/lib/shadowbox/shadowbox.css" rel="stylesheet" type="text/css"> -->
		
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<!-- <script type="text/javascript" src="../../js/lib/shadowbox/shadowbox.js"></script> -->
		<script type="text/javascript">

			g_Calendar.setDateFormat('dd/mm/yyyy');
			
			/*
			Shadowbox.init({
				overlayOpacity: 0.8
			}, setupDemos);

			function setupDemos() {
				Shadowbox.setup("a.detalleViatico", {
					gallery: "mustang",
					width: 1000,
					continuous: true,
					counterType: "skip"
				}); 
			}
			*/

			function limpiarFormulario()
			{
				$('#fechaInicio').val('');
				$('#fechaFin').val('');
				$('#fechaRendicionInicio').val('');
				$('#fechaRendicionFin').val('');
				$('#estatusRendicion').prop('selectedIndex', 0);
				$('#idEstado').prop('selectedIndex', 0);
				$('#idRegionReporte').prop('selectedIndex', 0);
			}
			
		</script>
	</head>
	
	<body class="normal">
		<table cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td><?php require(SAFI_VISTA_PATH . '/mensajes.php');?></td>
			</tr>
			<tr>
				<td>
					<form name="avanceReporteResponsableForm" id="avanceReporteResponsableForm" method="post" action="viaticonacional.php">
						<input type="hidden" name="accion" value="ReporteResponsables">
						<table cellspacing="0" width="800" align="center"
							background="../../imagenes/fondo_tabla.gif" class="reporteResponsablesRendicionAvance tablaalertas"
						>
							<tr> 
			    				<td height="21" colspan="2" class="normalNegroNegrita header" align="left">
			    					Historial de vi&aacute;tico
			    				</td>
							</tr>
							<tr>
								<td height="10" colspan="2"></td>
							</tr>
			  				<tr>
			  					<td class="normalNegrita">Vi&aacute;tico elaborado entre:</td>
			  					<td>
			  						<!-- Agregar los accesos rapidos de las fechas (Hoy, ayer, semana, semana pasada, etc.) -->
									<?php VistaFechas::ConstruirAccesosRapidosFechas("fechaInicio", "fechaFin", "dd/mm/yy") ?>
			  						<input
			  							type="text"
			  							size="10"
			  							id="fechaInicio"
			  							name="fechaInicio"
			  							class="dateparse"
										onfocus="javascript: compararFechasYBorrarById('fechaInicio', 'fechaFin', 'fechaInicio');"
										readonly="readonly"
										value="<?php echo !is_null($form->GetFechaInicio()) ? $form->GetFechaInicio() : "" ?>"
									/>
									<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaInicio');" title="Show popup calendar"><img
										src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"
									/></a>
									<input
										type="text"
										size="10"
										id="fechaFin"
										name="fechaFin"
										class="dateparse"
										onfocus="javascript: compararFechasYBorrarById('fechaInicio', 'fechaFin', 'fechaFin');"
										readonly="readonly"
										value="<?php echo !is_null($form->GetFechaFin()) ? $form->GetFechaFin() : "" ?>"
									/>
									<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaFin');" title="Show popup calendar"><img
										src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"
									/></a>
			  					</td>
			  				</tr>
			  				<tr>
			  					<td class="normalNegrita">Rendici&oacute;n elaborada entre:</td>
			  					<td>
			  						<!-- Agregar los accesos rapidos de las fechas (Hoy, ayer, semana, semana pasada, etc.) -->
									<?php VistaFechas::ConstruirAccesosRapidosFechas("fechaRendicionInicio", "fechaRendicionFin", "dd/mm/yy") ?>
									<input
			  							type="text"
			  							size="10"
			  							id="fechaRendicionInicio"
			  							name="fechaRendicionInicio"
			  							class="dateparse"
										onfocus="javascript: compararFechasYBorrarById('fechaRendicionInicio', 'fechaRendicionFin', 'fechaRendicionInicio');"
										readonly="readonly"
										value="<?php echo !is_null($form->GetFechaRendicionInicio()) ? $form->GetFechaRendicionInicio() : "" ?>"
									/>
									<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaRendicionInicio');" title="Show popup calendar"><img
										src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"
									/></a>
									<input
										type="text"
										size="10"
										id="fechaRendicionFin"
										name="fechaRendicionFin"
										class="dateparse"
										onfocus="javascript: compararFechasYBorrarById('fechaRendicionInicio', 'fechaRendicionFin', 'fechaRendicionFin');"
										readonly="readonly"
										value="<?php echo !is_null($form->GetFechaRendicionFin()) ? $form->GetFechaRendicionFin() : "" ?>"
									/>
									<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaRendicionFin');" title="Show popup calendar"><img
										src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"
									/></a>
			  					</td>
			  				</tr>
			  				<tr>
			  					<td class="normalNegrita">Estatus de rendici&oacute;n:</td>
			  					<td>
			  						<select class="normalNegro" id="estatusRendicion" name="estatusRendicion">
			  							<option value="0">Todos</option>
			  							<option
			  								value="1"
			  								<?php echo !is_null($form->GetEstatusRendicion()) && $form->GetEstatusRendicion() == "1"
			  									? 'selected="selected"' : "" ?>
			  							>
			  								Rendidos
			  							</option>
			  							<option
			  								value="2"
			  								<?php echo !is_null($form->GetEstatusRendicion()) && $form->GetEstatusRendicion() == "2"
			  									? 'selected="selected"' : "" ?>
			  							>
			  								No rendidos
			  							</option>
			  						</select>
			  					</td>
			  				</tr>
			  				<tr>
			  					<td class="normalNegrita">Estado:</td>
			  					<td>
			  						<select class="normalNegro" id="idEstado" name="idEstado">
			  							<option value="0">Seleccionar</option>
			  							<?php
			  								if(is_array($estados)){
				  								foreach ($estados As $idEstado => $estado){
				  									echo '
				  						<option
				  							value="'.$idEstado.'"
				  							'.( !is_null($form->GetIdEstado()) && $form->GetIdEstado() == $idEstado ? 'selected="selected"': '').'
				  						>
				  							'.$estado->GetNombre().'
				  						</option>
				  									';	
				  								}
			  								}
			  							?>
			  						</select>
			  					</td>
			  				</tr>
			  				<tr>
			  					<td class="normalNegrita">Regi&oacute;n:</td>
			  					<td>
			  						<select class="normalNegro" id="idRegionReporte" name="idRegionReporte">
			  							<option value="0">Seleccionar</option>
			  							<?php
			  								if(is_array($regionReportes)){
				  								foreach ($regionReportes As $idRegionReporte => $regionReporte){
				  									echo '
				  						<option
				  							value="'.$idRegionReporte.'"
				  							'.( !is_null($form->GetIdRegionReporte()) && $form->GetIdRegionReporte() == $idRegionReporte
				  								? 'selected="selected"': ''
				  							).'
				  						>
				  							'.$regionReporte->GetNombre().'
				  						</option>
				  									';	
				  								}
			  								}
			  							?>
			  						</select>
			  					</td>
			  				</tr>
			  				<tr>
			  					<td class="normalNegrita" style="vertical-align: top;">Centro  gestor/costo:</td>
			  					<td>
			  						<div style="color: red;">Esta categor&iacute;a program&aacute;tica se refiere al a&ntilde;o presupuestario
			  						 	en curso. En ning&uacute;n momento se podr&aacute; visualizar una categor&iacute;a program&aacute;tica
			  							de un a&ntilde;o presupuestario diferente al actual.
			  						</div>
			  						<select class="normalNegro" name="centroGestorCosto">
			  							<option value="0">Selecionar...</option>
			  							<?php 
			  								if(is_array($accionesEspecificas)){
				  								foreach ($accionesEspecificas AS $id => $accionEspecifica)
				  								{
				  									echo '
				  						<option
				  							value="'.$id.'"
				  							'.( !is_null($form->GetCentroGestorCosto()) && $form->GetCentroGestorCosto() == $id
				  								? 'selected="selected"': ''
				  							).'
				  						>
				  						'.$accionEspecifica['centro_gestor'].'/'.$accionEspecifica['centro_costo'].'
				  						</option>
				  									';
				  								}
			  								}
			  							?>
			  						</select>
			  					</td>
			  				</tr>
			  				<!-- 
			  				<tr>
			  					<td class="normalNegrita" style="vertical-align: top;">Proyecto / Acci&oacute;n centralizada:</td>
			  					<td>
			  						<fieldset>
			  							Tipo:
			  							<input type="radio" name="cp">Empleado&nbsp;&nbsp;&nbsp;
				  						<input type="radio" name="cp">Beneficiario
			  							<hr />
			  							Proyecto / Acci&oacute;n centralizada:<br />
			  							<select class="normalNegro"  style="width: 530px;">
			  								<option>Seleccionar...</option>
			  							</select><br />
			  							Acci&oacute;n espec&iacute;fica:<br />
			  							<select class="normalNegro"  style="width: 530px;">
			  								<option>Seleccionar...</option>
			  							</select>
			  						</fieldset>
			  					<td>
			  				</tr>
			  				 -->
			  				<!-- 
			  				<tr>
			  					<td colspan="2">
			  						<fieldset>
			  							<legend class="normalNegrita">Categor&iacute;a program&aacute;tica</legend>
				  						<table>
				  							<tr>
				  								<td>Tipo</td>
				  								<td>
				  									<input type="radio" name="cp">Empleado&nbsp;&nbsp;&nbsp;
				  									<input type="radio" name="cp">Beneficiario
				  								</td>
				  							</tr>
				  							<tr>
				  								<td>Proyecto / Acci&oacute;n centralizada:</td>
				  								<td>
				  									<select class="normalNegro"  style="width: 530px;">
				  										<option>Seleccionar...</option>
				  									</select>
				  								</td>
				  							</tr>
				  							<tr>
				  								<td>Acci&oacute;n espec&iacute;fica:</td>
				  								<td>
				  									<select class="normalNegro"  style="width: 530px;">
				  										<option>Seleccionar...</option>
				  									</select>
				  								</td>
				  							</tr>
				  						</table>
				  					</fieldset>
			  					</td>
			  				</tr>
			  				 -->
			  				<tr>
								<td height="52" colspan="2" align="center">
									<input type="submit" value="Buscar" class="normalNegro" />
									<input type="button" value="Limpiar" class="normalNegro" onclick="limpiarFormulario();"/>
								</td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
		
		<?php
			if(is_array($datosViaticos) && count($datosViaticos)>0)
			{
				
				echo '
		<table
			cellpadding="0"
			cellspacing="0"
			align="center"
			class="tablaalertas" 
			background="../../imagenes/fondo_tabla.gif"
			style="width: 100%;"
		>
			<tr class="normalNegroNegrita">
				<td class="header normalNegroNegrita">C&oacute;d. vi&aacute;tico</td>
				<td class="header normalNegroNegrita">Estatus</td>
				<td class="header normalNegroNegrita">Fecha vi&aacute;tico</td>
				<td class="header normalNegroNegrita">Responsable</td>
				<td class="header normalNegroNegrita">Regi&oacute;n</td>
				<td class="header normalNegroNegrita">Estado</td>
				<td class="header normalNegroNegrita">Fecha inicio</td>
				<td class="header normalNegroNegrita">Fecha fin</td>
				<td class="header normalNegroNegrita">Objetivos</td>
				<td class="header normalNegroNegrita">Centro gestor / Centro de Costo</td>
				<td class="header normalNegroNegrita">C&oacute;d. rendici&oacute;n</td>
				<td class="header normalNegroNegrita">Fecha rendici&oacute;n</td>
				<td class="header normalNegroNegrita">Anticipo</td>
				<td class="header normalNegroNegrita">Trasnporte Extraurbano</td>
				<td class="header normalNegroNegrita">Transporte entre ciudades</td>
				<td class="header normalNegroNegrita">Total Gastado</td>
				<td class="header normalNegroNegrita">Reintegro</td>
			</tr>
				';
				
				$tdClass = "even";
				
				foreach ($datosViaticos AS $idDesconocido => $datosViatico)
				{
					if(
						isset($datosViatico['ClassViaticoNacional'])
						&& ($viatico = $datosViatico['ClassViaticoNacional']) instanceof EntidadViaticoNacional
						&& isset($datosViatico['documentoViatico'])
						&& ($documentoViatico = $datosViatico['documentoViatico']) instanceof EntidadDocGenera
					){
						$rendicion = isset($datosViatico['ClassRendicionViatico'])
							&& ($rendicion=$datosViatico['ClassRendicionViatico']) instanceof EntidadRendicionViaticoNacional
							? $rendicion : null;
						
						$nombresResponsable = "---";
						$nombreEstado = "---";
						$tdClass = ($tdClass == "even") ? "odd" : "even";
						
						$fecha = explode(" ", $viatico->GetFechaViatico());
						$fechaViatico = $fecha[0];
						
						$idEstatus = null;
						$nombreEstatus = "---";
						
						if (is_array($arrEstatus) && isset($arrEstatus[$documentoViatico->GetIdEstatus()])){
							$objEstatus = $arrEstatus[$documentoViatico->GetIdEstatus()];
							$idEstatus = $objEstatus->GetId();
							$nombreEstatus = $objEstatus->GetNombre();
						}
						
						if(
							$viatico->GetResponsable() != null
							&& $viatico->GetResponsable() instanceof EntidadResponsableViatico
						){
							$nombresResponsable = mb_strtoupper($viatico->GetResponsable()->GetNombres() . ' '
								.$viatico->GetResponsable()->GetApellidos(), 'ISO-8859-1'); 
						}
						
						$especifica = null;
						if($viatico->GetProyectoEspecifica() != null){
							$especifica = $viatico->GetProyectoEspecifica(); 
						} else if($viatico->GetAccionCentralizadaEspecifica() != null){
							$especifica = $viatico->GetAccionCentralizadaEspecifica();
						}
						
						$strMontoTotal = "---";
						$strMontoTransporteExtraUrbano = "0.0";
						$strMontoTransporteEntreCiudades = "0.0";
						if(is_array($vRAsignaciones = $viatico->GetViaticoResponsableAsignaciones())){
							$montoTotal = CalcularMontoTotalAsignacionesViaticoNacional($vRAsignaciones);
							$strMontoTotal = number_format($montoTotal,2,',','.');
							
							$vRAsignacion = $vRAsignaciones[EntidadAsignacionViatico::COD_TRANSPORTE_EXTRAURBANO];
							if($vRAsignacion instanceof EntidadViaticoResponsableAsignacion){
								$strMontoTransporteExtraUrbano = number_format($vRAsignacion->GetMonto() * $vRAsignacion->GetUnidades(),2,',','.');
							}
							
							$vRAsignacion = $vRAsignaciones[EntidadAsignacionViatico::COD_TRANSPORTE_ENTRE_CIUDADES];
							if($vRAsignacion instanceof EntidadViaticoResponsableAsignacion){
								$strMontoTransporteEntreCiudades = number_format($vRAsignacion->GetMonto() * $vRAsignacion->GetUnidades(),2,',','.');
							}
						}
						
						$idRendicion = "";
						$fechaRendicion = "";
						if($rendicion != null)
						{
							$idRendicion = $rendicion->GetId();
							$fecha = explode(" ", $rendicion->GetFechaRendicion());
							$fechaRendicion = $fecha[0];	
						}
						
						echo '
					<tr class="resultados '.$tdClass.'" onclick="Registroclikeado(this);">
						<td>
							<a class="detalleViatico" href="viaticonacional.php?accion=VerDetalles&idViaticoNacional='.$viatico->GetId().'">
								'.$viatico->GetId().'
							</a>
						</td>
						<td '.($idEstatus != null && $idEstatus == ESTADO_ANULADO ? 'style="color: red;"' : '').'>'.$nombreEstatus.'</td>
						<td>'.$fechaViatico.'</td>
						<td>'.$nombresResponsable.'</td>
						<td >
							'.(
								$viatico->GetEstado() != null && isset($arrRegionReporteEstados[$viatico->GetEstado()->GetId()])
								? $arrRegionReporteEstados[$viatico->GetEstado()->GetId()]->GetNombre()
								: "---"
							).'
						</td>
						<td>'.(($viatico->GetEstado() != null) ? $viatico->GetEstado()->GetNombre() : "---").'</td>
						<td>'.$viatico->GetFechaInicioViaje().'</td>
						<td>'.$viatico->GetFechaFinViaje().'</td>
						<td>'.$viatico->GetObjetivosViaje().'</td>
						<td >
							'.($especifica != null ? $especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto() : "---").'
						</td>
						<td>'.$idRendicion.'</td>
						<td>'.$fechaRendicion.'</td>
						<td style="text-align: right;">'.$strMontoTotal.'</td>
						<td style="text-align: right;">'.$strMontoTransporteExtraUrbano.'</td>
						<td style="text-align: right;">'.$strMontoTransporteEntreCiudades.'</td>
						<td>'.($rendicion != null ? number_format($rendicion->GetTotalGastos(),2,',','.') : "").'</td>
						<td>'.($rendicion != null ? number_format($rendicion->GetMontoReintegro(),2,',','.') : "").'</td>
					</tr>
							';
						}
					}
				
				echo '
		</table>
				';
			}
		?>
	</body>
	
</html>