<?php

	require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');

	$form = FormManager::GetForm(FORM_BUSCAR_AVANCE);
	if($form != null && $form instanceof BuscarAvanceForm){
		$dataAvances = $form->GetDataAvances();
	}
	
	$cargoFundacionInstanciaActuales = $GLOBALS['SafiRequestVars']['avanceCargoFundacionInstanciaActuales'];
	$dependenciaInstanciaActuales = $GLOBALS['SafiRequestVars']['avanceDependenciaInstanciaActuales'];
	$empleadosElaboradores  = $GLOBALS['SafiRequestVars']['avanceEmpleadosElaboradores'];
	$estatusList = $GLOBALS['SafiRequestVars']['estatusList'];
?>
<html>
	<head>
		<title>.:SAFI:. Buscar avances</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
		
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
				establecerFocoInicialCodigoDocumento("idAvance");
			}
			
		</script>
	</head>
	
	<body class="normal" onload="onLoad();">
	
		<table align="center">
			<tr>
				<td><?php include(SAFI_VISTA_PATH . '/mensajes.php');?></td>
			</tr>
			<tr>
				<td>
					<form name="avanceBuscarForm" id="avanceBuscarForm" method="post" action="avance.php">
						<input type="hidden" name="accion" value="Buscar">
						<table cellpadding="0" cellspacing="0" width="640" align="center"
							background="../../imagenes/fondo_tabla.gif" class="tablaalertas"
						>
							<tr> 
			    				<td height="21" colspan="2" class="normalNegroNegrita header" align="left">
			    					Buscar avances
			    				</td>
							</tr>
			  				<tr>
								<td height="10" colspan="2"></td>
							</tr>
			  				<tr>
			  					<td class="normalNegrita">Elaborados entre:</td>
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
										value=""
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
									/>
									<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar"><img
										src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"
									/></a>
			  					</td>
			  				</tr>
			  				<tr>
			  					<td class="normalNegrita">C&oacute;digo del avance</td>
			  					<td>
			  						<input
			  							type="text"
			  							id="idAvance"
			  							name="idAvance"
			  							class="normalNegro"
			  							value="<?php echo ($form->GetAvance()!=null) ? $form->GetAvance()->GetId() : null ?>"
			  						>
			  					</td>
			  				</tr>
						</table>
						<br/>
						<div align="center">
							<input type="submit" value="Buscar" class="normalNegro">
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
							<td class="header normalNegroNegrita">Avance</td>
							<td class="header normalNegroNegrita">Fecha avance</td>
							<td class="header normalNegroNegrita">Responsable</td>
							<td class="header normalNegroNegrita">Estatus</td>
							<td class="header normalNegroNegrita">Instancia actual</td>
							<td class="header normalNegroNegrita">Elaborado por</td>
							<td class="header normalNegroNegrita">Acci&oacute;n</td>
						</tr>
							';
							
							$tdClass = "even";
							
							foreach ($dataAvances as $idAvance => $dataAvance)
							{
								if(
									isset($dataAvance['ClassDocGenera'])
									&& ($docGenera=$dataAvance['ClassDocGenera']) instanceof EntidadDocGenera
									&& isset($dataAvance['ClassAvance'])
									&& ($avance=$dataAvance['ClassAvance']) instanceof EntidadAvance
								){
									$tdClass = ($tdClass == "even") ? "odd" : "even";
									
									$cargo = null;
									if(is_array($cargoFundacionInstanciaActuales)){
										$cargo = $cargoFundacionInstanciaActuales[GetCargoFundacionFromIdPerfil($docGenera->GetIdperfilActual())];
									}
									$cargoNombre = ($cargo != null && $cargo instanceof EntidadCargo) ? $cargo->GetNombre() : '';
									
									$dependencia = null;
									if(is_array($dependenciaInstanciaActuales)){
										$dependencia = $dependenciaInstanciaActuales[GetIdDependenciaFromIdPerfil($docGenera->GetIdperfilActual())];
									}
									$dependenciaNombre =  ($dependencia != null && $dependencia instanceof EntidadDependencia) ?
											'<br/>(<span style="font-style: italic"> ' . $dependencia->GetNombre() . ' </span>)' : '---';
									
									//$empleadosElaboradores
									$empleadosElaborador = null;
									if(is_array($empleadosElaboradores)){
										$empleadosElaborador = $empleadosElaboradores[$docGenera->GetUsuaLogin()];
									}
									$empleadosElaboradorString = ($empleadosElaborador != null && $empleadosElaborador instanceof EntidadEmpleado) ?
										mb_strtoupper($empleadosElaborador->GetNombres()." ".$empleadosElaborador->GetApellidos(), "ISO-8859-1")
										: "---";
										
									$fechaAvance = explode(" ", $avance->GetFechaAvance());
									
									$nombresResponsables = array();
									foreach ($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidas)
									{
										$responsableAvance = $responsableAvancePartidas->GetResponsableAvance();
										
										// Obtener los datos del empleado/beneficiario
										if(
											$responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_EMPLEADO
											&& $responsableAvance->GetEmpleado() != null
										){
											$empleado = $responsableAvance->GetEmpleado();
											$nombresResponsables[] = mb_strtoupper($empleado->GetNombres() . ' '
												.$empleado->GetApellidos(), 'ISO-8859-1'); 
										}
										else if (
											$responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_BENEFICIARIO
											&& $responsableAvance->GetBeneficiario() != null
										){
											$beneficiario = $responsableAvance->GetBeneficiario();
											$nombresResponsables[] = mb_strtoupper($beneficiario->GetNombres() . ' '
												.$beneficiario->GetApellidos(), 'ISO-8859-1');
										}
									}
									
									echo '
						<tr class="resultados '.$tdClass.'"  onclick="Registroclikeado(this);" >
						
						
						
							<td >
								<a
									href="avance.php?accion=VerDetalles&idAvance='.$avance->GetId().'"
								>
									'.$avance->GetId().'
								</a>
							</td>
							<td >'.$fechaAvance[0].'</td>
							<td >'.implode(", ", $nombresResponsables).'</td>
							<td >'.$estatusList[$docGenera->GetIdEstatus()]->GetNombre().'</td>				
							<td >'. $cargoNombre  . $dependenciaNombre . '</td>
							<td >'.$empleadosElaboradorString.'</td>
							<td >
								<a href="avance.php?accion=VerDetalles&idAvance='.$avance->GetId().'">Ver Detalle</a>
							</td>
						</tr>
									';
									
								}
							}
							echo '
					</table>
							';
						}
					?>
				</td>
			</tr>
		</table>
	</body>
</html>
