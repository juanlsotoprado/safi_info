<?php

	require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');
	
	$form = FormManager::GetForm(FORM_REPORTE_RESPONSABLES_AVANCE);
	$dataAvances = null;
	if($form != null && $form instanceof ReporteResponsablesAvanceForm){
		$dataAvances = $form->GetDataAvances();
	}

	$estados = $GLOBALS['SafiRequestVars']['estados'];	
	$regionReportes = $GLOBALS['SafiRequestVars']['regionReportes'];
	$arrRegionReporteEstados = $GLOBALS['SafiRequestVars']['arrRegionReporteEstados'];
	$arrEstatus = $GLOBALS['SafiRequestVars']['arrEstatus'];
	
?>
<html>
	<head>
		<title>.:SAFI:. Historial de Avance</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
		<link href="../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />
		<style>
			.nombreResponsable{
				background-color: inherit;
			}
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
		
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<script type="text/javascript">

			g_Calendar.setDateFormat('dd/mm/yyyy');

			function onLoad()
			{
				// input del responsable
				var objInputResponsable = $("#responsable");
				var objInputTipoEmpleado = $("#tipoResponsableEmpleado");
				var objInputTipoBeneficiario = $("#tipoResponsableBeneficiario"); 
				var objCedulaResponsable = $("#cedulaResponsable");
				var objNombreResponsable = $("#nombreResponsable");
				var objTipoResponsable = $("#tipoResponsable");
				var objDisplayCedulaResponsable = $("#displayCedulaResponsable");
				var objDisplayNombreResponsable = $("#displayNombreResponsable");
				var objEliminarResponsable = $("#eliminarResponsable");
				
				objInputResponsable.val('');
				
				// Cargar los datos de los responsables
				cargarResponsables({
					tipo: '<?php echo EntidadResponsable::TIPO_EMPLEADO?>',
					objInputResponsable: objInputResponsable,
					objInputTipoEmpleado: objInputTipoEmpleado,
					objInputTipoBeneficiario: objInputTipoBeneficiario,
					focus: false,
					objCedulaResponsable: objCedulaResponsable,
					objNombreResponsable: objNombreResponsable,
					objTipoResponsable: objTipoResponsable,
					objDisplayCedulaResponsable: objDisplayCedulaResponsable,
					objDisplayNombreResponsable: objDisplayNombreResponsable
				});

				objInputTipoEmpleado.unbind('click');
				objInputTipoEmpleado.bind('click', {objInputResponsable: objInputResponsable}, function(event){
					autoCompleteResponsables({
						tipo: '<?php echo EntidadResponsable::TIPO_EMPLEADO?>',
						objInputResponsable: event.data.objInputResponsable,
						focus: true,
						objCedulaResponsable: objCedulaResponsable,
						objNombreResponsable: objNombreResponsable,
						objTipoResponsable: objTipoResponsable,
						objDisplayCedulaResponsable: objDisplayCedulaResponsable,
						objDisplayNombreResponsable: objDisplayNombreResponsable
					});
				});

				objInputTipoBeneficiario.unbind('click');
				objInputTipoBeneficiario.bind('click', {objInputResponsable: objInputResponsable}, function(event){
					autoCompleteResponsables({
						tipo: '<?php echo EntidadResponsable::TIPO_BENEFICIARIO?>',
						objInputResponsable: event.data.objInputResponsable,
						focus: true,
						objCedulaResponsable: objCedulaResponsable,
						objNombreResponsable: objNombreResponsable,
						objTipoResponsable: objTipoResponsable,
						objDisplayCedulaResponsable: objDisplayCedulaResponsable,
						objDisplayNombreResponsable: objDisplayNombreResponsable
					});
				});

				objEliminarResponsable.unbind('click');
				objEliminarResponsable.bind('click', function(event){
					objCedulaResponsable.val('');
					objNombreResponsable.val('');
					objTipoResponsable.val('');
					objDisplayCedulaResponsable.empty();
					objDisplayNombreResponsable.empty();
				});
			}

			function cargarResponsables(params){

				var tipo = params['tipo'];
				var objInputResponsable = params['objInputResponsable'];
				var objInputTipoEmpleado = params['objInputTipoEmpleado'];
				var objInputTipoBeneficiario = params['objInputTipoBeneficiario'];
				var focus = params['focus'];
				var objCedulaResponsable = params['objCedulaResponsable'];
				var objNombreResponsable = params['objNombreResponsable'];
				var objTipoResponsable = params['objTipoResponsable'];
				var objDisplayCedulaResponsable = params['objDisplayCedulaResponsable'];
				var objDisplayNombreResponsable = params['objDisplayNombreResponsable'];
				
				if(tipo == '<?php echo EntidadResponsable::TIPO_EMPLEADO ?>'){
					autoCompleteResponsables({
						tipo: tipo, 
						objInputResponsable: objInputResponsable,
						focus: focus,
						objCedulaResponsable: objCedulaResponsable,
						objNombreResponsable: objNombreResponsable,
						objTipoResponsable: objTipoResponsable,
						objDisplayCedulaResponsable: objDisplayCedulaResponsable,
						objDisplayNombreResponsable: objDisplayNombreResponsable
					});
					objInputTipoEmpleado.attr('checked', 'checked');
				} else if(tipo == '<?php echo EntidadResponsable::TIPO_BENEFICIARIO ?>'){
					autoCompleteResponsables({
						tipo: tipo,
						objInputResponsable: objInputResponsable,
						focus: focus,
						objCedulaResponsable: objCedulaResponsable,
						objNombreResponsable: objNombreResponsable,
						objTipoResponsable: objTipoResponsable,
						objDisplayCedulaResponsable: objDisplayCedulaResponsable,
						objDisplayNombreResponsable: objDisplayNombreResponsable
					});
					objInputTipoBeneficiario.attr('checked', 'checked');
				}
			}

			function autoCompleteResponsables(params)
			{				
				var tipo = params['tipo'];
				var objInputResponsable = params['objInputResponsable'];
				var objCedulaResponsable = params['objCedulaResponsable'];
				var objNombreResponsable = params['objNombreResponsable'];
				var objTipoResponsable = params['objTipoResponsable'];
				var focus = params['focus'];
				var objDisplayCedulaResponsable = params['objDisplayCedulaResponsable'];
				var objDisplayNombreResponsable = params['objDisplayNombreResponsable'];
				
				var numItems = 10;
				
				objInputResponsable.val('');
				//objInputResponsable.autocomplete("destroy");
				
				if(tipo == '<?php echo EntidadResponsable::TIPO_EMPLEADO?>'){
					objInputResponsable.autocomplete({
						source: function(request, response){
							$.ajax({
								url: "../empleado.php",
								dataType: "json",
								data: {
									accion: "Search",
									tipoRespuesta: 'json',
									key: request.term,
									numItems: numItems,
									idEstatus: "all"
								},
								success: function(json){
									var index = 0;
									var items = new Array();
		
									$.each(json.listaempleado, function(cedula, objEmpleado){
										var value = objEmpleado.cedula + ' : ' + objEmpleado.nombres.toUpperCase() + ' ' + objEmpleado.apellidos.toUpperCase();
										items[index++] = {
												id: cedula,
												label: value,
												value: value,
												tipo: tipo,
												nombres: objEmpleado.nombres.toUpperCase(),
												apellidos: objEmpleado.apellidos.toUpperCase()
										};
									});
									response(items);
								}
							});
						},
						minLength: 1,
						select: function(event, ui)
						{
							objCedulaResponsable.val('');
							objNombreResponsable.val('');
							objTipoResponsable.val('');
							objCedulaResponsable.val(ui.item.id);
							objNombreResponsable.val(ui.item.nombres + " " + ui.item.apellidos);
							objTipoResponsable.val(ui.item.tipo);

							objDisplayCedulaResponsable.empty();
							objDisplayNombreResponsable.empty();
							objDisplayCedulaResponsable.append(document.createTextNode(ui.item.id));
							objDisplayNombreResponsable.append(document.createTextNode(ui.item.nombres + " " + ui.item.apellidos));
							
							objInputResponsable.val('');
							
							return false;
						}
					});
				} else if (tipo == '<?php echo EntidadResponsable::TIPO_BENEFICIARIO?>') {
					objInputResponsable.autocomplete({
						source: function(request, response){
							$.ajax({
								url: "../beneficiarioviatico.php",
								dataType: "json",
								data: {
									accion: "Search",
									tipoRespuesta: 'json',
									key: request.term,
									numItems: numItems,
									idEstatus: "all"
								},
								success: function(json){
									var index = 0;
									var items = new Array();
		
									$.each(json.listabeneficiarioviatico, function(cedula, objBeneficiario){
										var value = objBeneficiario.id + ' : ' + objBeneficiario.nombres.toUpperCase() + ' ' + objBeneficiario.apellidos.toUpperCase();
										items[index++] = {
												id: objBeneficiario.id,
												label: value,
												value: value,
												tipo: tipo,
												nombres: objBeneficiario.nombres.toUpperCase(),
												apellidos: objBeneficiario.apellidos.toUpperCase(),
												tipoEmpleado: objBeneficiario.tipo
										};
									});
									response(items);
								}
							});
						},
						minLength: 1,
						select: function(event, ui)
						{
							objCedulaResponsable.val('');
							objNombreResponsable.val('');
							objTipoResponsable.val('');
							objCedulaResponsable.val(ui.item.id);
							objNombreResponsable.val(ui.item.nombres + " " + ui.item.apellidos);
							objTipoResponsable.val(ui.item.tipo);

							objDisplayCedulaResponsable.empty();
							objDisplayNombreResponsable.empty();
							objDisplayCedulaResponsable.append(document.createTextNode(ui.item.id));
							objDisplayNombreResponsable.append(document.createTextNode(ui.item.nombres + " " + ui.item.apellidos));
							
							objInputResponsable.val('');
							
							return false;
						}
					});
				}

				if(focus){
					objInputResponsable.focus();
				}
			}

			function limpiarFormulario()
			{
				$('#txt_inicio').val('');
				$('#hid_hasta_itin').val('');
				$('#fechaRendicionInicio').val('');
				$('#fechaRendicionFin').val('');
				$('#idAvance').val('avan-');
				$('#idRendicion').val('rava-');
				$('#estatusRendicion').prop('selectedIndex', 0);
				$('#idEstado').prop('selectedIndex', 0);
				$('#idRegionReporte').prop('selectedIndex', 0);

				$("#cedulaResponsable").val('');
				$("#nombreResponsable").val('');
				$("#tipoResponsable").val('');
				$("#displayCedulaResponsable").empty();
				$("#displayNombreResponsable").empty();
				
				autoCompleteResponsables({
					tipo: <?php echo EntidadResponsable::TIPO_EMPLEADO ?>, 
					objInputResponsable: $("#responsable"),
					focus: false,
					objCedulaResponsable: $("#cedulaResponsable"),
					objNombreResponsable: $("#nombreResponsable"),
					objTipoResponsable: $("#tipoResponsable"),
					objDisplayCedulaResponsable: $("#displayCedulaResponsable"),
					objDisplayNombreResponsable: $("#displayNombreResponsable")
				});
				$("#tipoResponsableEmpleado").prop('checked', true);
			}

		</script>
	</head>
	<body class="normal" onload="onLoad();">
		<form name="avanceReporteResponsableForm" id="avanceReporteResponsableForm" method="post" action="avance.php">
			<input type="hidden" name="accion" value="ReporteResponsables">
			<table cellspacing="0" width="800" align="center"
				background="../../imagenes/fondo_tabla.gif" class="reporteResponsablesRendicionAvance tablaalertas"
			>
				<tr> 
    				<td height="21" colspan="2" class="normalNegroNegrita header" align="left">
    					Historial de avance
    				</td>
				</tr>
				<tr>
					<td height="10" colspan="2"></td>
				</tr>
  				<tr>
  					<td class="normalNegrita">Avance elaborado entre:</td>
  					<td>
  						<!-- Agregar los accesos rapidos de las fechas (Hoy, ayer, semana, semana pasada, etc.) -->
						<?php VistaFechas::ConstruirAccesosRapidosFechas("txt_inicio", "hid_hasta_itin", "dd/mm/yy") ?>
  						<input
  							type="text"
  							size="10"
  							id="txt_inicio"
  							name="fechaInicio"
  							class="dateparse"
							onfocus="javascript: compararFechasYBorrarById('txt_inicio', 'hid_hasta_itin', 'txt_inicio');"
							readonly="readonly"
							value="<?php echo !is_null($form->GetFechaInicio()) ? $form->GetFechaInicio() : "" ?>"
						/>
						<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar"><img
							src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"
						/></a>
						<input
							type="text"
							size="10"
							id="hid_hasta_itin"
							name="fechaFin"
							class="dateparse"
							onfocus="javascript: compararFechasYBorrarById('txt_inicio', 'hid_hasta_itin', 'hid_hasta_itin');"
							readonly="readonly"
							value="<?php echo !is_null($form->GetFechaFin()) ? $form->GetFechaFin() : "" ?>"
						/>
						<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar"><img
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
  							<option value="1"
  								<?php echo !is_null($form->GetEstatusRendicion()) && $form->GetEstatusRendicion() == "1"
			  						? 'selected="selected"' : "" ?>
  							>
  								Rendidos
  							</option>
  							<option value="2"
  								<?php echo !is_null($form->GetEstatusRendicion()) && $form->GetEstatusRendicion() == "2"
			  						? 'selected="selected"' : "" ?>
  							>
  								No rendidos
  							</option>
  						</select>
  					</td>
  				</tr>
  				<tr>
  					<td class="normalNegrita">C&oacute;digo de la rendici&oacute;n:</td>
  					<td>
  						<input
  							type="text"
  							id="idRendicion"
  							name="idRendicion"
  							class="normalNegro"
  							value="<?php echo $form->GetIdRendicion() ?>"
  						>
  					</td>
  				</tr>
  				<tr>
  					<td class="normalNegrita">C&oacute;digo de avance:</td>
  					<td>
  						<input
  							type="text"
  							id="idAvance"
  							name="idAvance"
  							class="normalNegro"
  							value="<?php echo $form->GetIdAvance() ?>"
  						>
  					</td>
  				</tr>
  				<tr>
  					<td class="normalNegrita">Responsable:</td>
  					<td>
  						<input
  							type="radio"
  							id="tipoResponsableEmpleado"
  							name="radioTipoResponsable"
  						>
  						Empleado
  						<input
  							type="radio"
  							id="tipoResponsableBeneficiario"
  							name="radioTipoResponsable"
  						>
  						Beneficiario&nbsp;&nbsp;
  						<input
  							type="text"
  							id="responsable"
  							class="normalNegro"
  							style="width: 450px;"
  							value="<?php //echo $form->Get() ?>"
  						>
  					</td>
  				</tr>
  				<tr>
  					<td></td>
  					<td>
  						<input
  							type="hidden" id="cedulaResponsable" name="cedulaResponsable"
  							value="<?php echo !is_null($form->GetCedulaResponsable()) ? $form->GetCedulaResponsable() : "" ?>"
  						>
  						<input
  							type="hidden" id="nombreResponsable" name="nombreResponsable"
  							value="<?php echo !is_null($form->GetNombreResponsable()) ? $form->GetNombreResponsable() : "" ?>"
  						>
  						<input type="hidden" id="tipoResponsable" name="tipoResponsable">
	  					<table width="100%">
	  						<tr>
	  							<td>C.I: <span id="displayCedulaResponsable">
	  								<?php echo !is_null($form->GetCedulaResponsable()) ? $form->GetCedulaResponsable() : "" ?>
	  							</span></td>
	  							<td>Nombre: <span id="displayNombreResponsable">
	  								<?php echo !is_null($form->GetNombreResponsable()) ? $form->GetNombreResponsable() : "" ?>
	  							</span></td>
	  							<td style="width: 20px;"><div id="eliminarResponsable" class="botonEliminarResponsable"></div></td>
	  						</tr>
	  					</table>  						
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
			</table>
			<br/>
			<div align="center">
				<input type="submit" value="Buscar" class="normalNegro">
				<input type="button" value="Limpiar" class="normalNegro" onclick="limpiarFormulario();"/>
			</div>
		</form>
		
		<?php
			if(is_array($dataAvances) && count($dataAvances)>0)
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
				<td class="header normalNegroNegrita">C&oacute;d. avance</td>
				<td class="header normalNegroNegrita">Estatus</td>
				<td class="header normalNegroNegrita">Fecha avance</td>
				<td class="header normalNegroNegrita">Responsable</td>
				<td class="header normalNegroNegrita">Regi&oacute;n</td>
				<td class="header normalNegroNegrita">Estado</td>
				<td class="header normalNegroNegrita">Fecha inicio</td>
				<td class="header normalNegroNegrita">Fecha fin</td>
				<td class="header normalNegroNegrita">Objetivos</td>
				<td class="header normalNegroNegrita">Acci&oacute;n Espec&iacute;fica</td>
				<td class="header normalNegroNegrita">C&oacute;d. rendici&oacute;n</td>
				<td class="header normalNegroNegrita">Fecha rendici&oacute;n</td>
				<td class="header normalNegroNegrita" style="text-align: right;">Anticipo</td>
				<td class="header normalNegroNegrita" style="text-align: right;">Gastado</td>
				<td class="header normalNegroNegrita" style="text-align: right;">Reintegro</td>
				<td class="header normalNegroNegrita" style="text-align: right;">Reintegrado</td>
				<td class="header normalNegroNegrita" style="text-align: right;">Diferencia</td>
			</tr>
				';
				
				$tdClass = "even";
				
				foreach ($dataAvances AS $idDesconocido => $dataAvance)
				{
					if(
						isset($dataAvance['ClassAvance'])
						&& ($avance=$dataAvance['ClassAvance']) instanceof EntidadAvance
						&& isset($dataAvance['ClassResponsableAvancePartidas'])
						&& ($responsableAvancePartidas=$dataAvance['ClassResponsableAvancePartidas'])
							instanceof EntidadResponsableAvancePartidas
						&& isset($dataAvance['documentoAvance'])
					){
						$documentoAvance = $dataAvance['documentoAvance'];
						
						$rendicion = isset($dataAvance['ClassRendicionAvance'])
							&& ($rendicion=$dataAvance['ClassRendicionAvance']) instanceof EntidadRendicionAvance
							? $rendicion : null;
						
						$responsableRendicionAvancePartidas = isset($dataAvance['ClassResponsableRendicionAvancePartidas'])
							&& $dataAvance['ClassResponsableRendicionAvancePartidas'] instanceof EntidadResponsableRendicionAvancePartidas
							? $dataAvance['ClassResponsableRendicionAvancePartidas'] : null;
						
						$montoAnticipo = $responsableAvancePartidas->GetMontoTotal();
						$montoGastado = "";
						$montoReintegrado = "";
						$montoReintegro = "";
						$diferencia = "";
						if($responsableRendicionAvancePartidas != null){
							$montoGastado = $responsableRendicionAvancePartidas->GetMontoTotal();
							$montoReintegrado = $responsableRendicionAvancePartidas->GetMontoReintegrado();
							$montoReintegro = $montoAnticipo - $montoGastado;
							$diferencia = $montoReintegro - $montoReintegrado;
						}
						
						$strMontoGastado = $montoGastado === "" ? "" : number_format($montoGastado,2,',','.');
						$strMontoReintegrado = $montoReintegrado === "" ? "" : number_format($montoReintegrado,2,',','.');
						$strMontoReintegro = $montoReintegro === "" ? "" : number_format($montoReintegro,2,',','.');
						$strDiferencia = $diferencia === "" ? "" : number_format($diferencia,2,',','.');
						
						$responsableAvance = $responsableAvancePartidas->GetResponsableAvance();
						$tdClass = ($tdClass == "even") ? "odd" : "even";
						
						$fecha = explode(" ", $avance->GetFechaAvance());
						$fechaAvance = $fecha[0];
						
						$nombresResponsable = "";
						// Obtener los datos del empleado/beneficiario
						if(
							$responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_EMPLEADO
							&& $responsableAvance->GetEmpleado() != null
						){
							$empleado = $responsableAvance->GetEmpleado();
							$nombresResponsable = mb_strtoupper($empleado->GetNombres() . ' '
								.$empleado->GetApellidos(), 'ISO-8859-1'); 
						}
						else if (
							$responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_BENEFICIARIO
							&& $responsableAvance->GetBeneficiario() != null
						){
							$beneficiario = $responsableAvance->GetBeneficiario();
							$nombresResponsable = mb_strtoupper($beneficiario->GetNombres() . ' '
								.$beneficiario->GetApellidos(), 'ISO-8859-1');
						}
						
						$idRendicion = "";
						$fechaRendicion = "";
						if($rendicion != null)
						{
							$idRendicion = $rendicion->GetId();
							$fecha = explode(" ", $rendicion->GetFechaRendicion());
							$fechaRendicion = $fecha[0];	
						}
						
						$especifica = null;
						if($avance->GetProyecto() != null && $avance->GetProyectoEspecifica() != null){
							$especifica = $avance->GetProyectoEspecifica(); 
						} else if($avance->GetAccionCentralizada() != null && $avance->GetAccionCentralizadaEspecifica() != null){
							$especifica = $avance->GetAccionCentralizadaEspecifica();
						}
						
						$idEstatus = null;
						$nombreEstatus = "---";
						
						if (is_array($arrEstatus) && isset($arrEstatus[$documentoAvance->GetIdEstatus()])){
							$objEstatus = $arrEstatus[$documentoAvance->GetIdEstatus()];
							$idEstatus = $objEstatus->GetId();
							$nombreEstatus = $objEstatus->GetNombre();
						}
						
						echo '
			<tr class="resultados '.$tdClass.'" onclick="Registroclikeado(this);">
				<td >'.$avance->GetId().'</td>
				<td '.($idEstatus != null && $idEstatus == ESTADO_ANULADO ? 'style="color: red;"' : '').'>'.$nombreEstatus.'</td>
				<td >'.$fechaAvance.'</td>
				<td >'.$nombresResponsable.'</td>
				<td >
					'.(
						$responsableAvance->GetEstado() != null && isset($arrRegionReporteEstados[$responsableAvance->GetEstado()->GetId()])
						? $arrRegionReporteEstados[$responsableAvance->GetEstado()->GetId()]->GetNombre()
						: "---"
					).'
				</td>
				<td >
					'.($responsableAvance->GetEstado() != null ? $responsableAvance->GetEstado()->GetNombre() : "").'
				</td>
				<td >'.$avance->GetFechaInicioActividad().'</td>
				<td >'.$avance->GetFechaFinActividad().'</td>
				<td >'.$avance->GetObjetivos().'</td>
				<td >'.(
						$especifica != null ? $especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto() : "---"
					).'</td>
				<td >'. $idRendicion.'</td>
				<td >'. $fechaRendicion.'</td>
				<td  style="text-align: right;">'.number_format($montoAnticipo,2,',','.').'</td>
				<td  style="text-align: right;">'. $strMontoGastado.'</td>
				<td  style="text-align: right;">'. $strMontoReintegro.'</td>
				<td  style="text-align: right;">'. $strMontoReintegrado.'</td>
				<td  style="text-align: right;">'. $strDiferencia.'</td>
			</tr>
						';
					
					} // if(
					  //	isset($dataRendicionAvance['ClassDocGenera'])
					  //	&& ($docGenera=$dataRendicionAvance['ClassDocGenera']) instanceof EntidadDocGenera
					  //	&& isset($dataRendicionAvance['ClassRendicionAvance'])
					  //	&& ($rendicion=$dataRendicionAvance['ClassRendicionAvance']) instanceof EntidadRendicionAvance
					  //){
				} // foreach ($dataRendicionAvances AS $idRendicion => $dataRendicionAvance)
				
				echo '
		</table>
				';
				
			} // if(is_array($dataRendicionAvances) && count($dataRendicionAvances)>0)
		?>
	</body>
</html>