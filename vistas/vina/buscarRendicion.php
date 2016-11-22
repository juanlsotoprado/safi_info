<?php

	require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');

	$form = FormManager::GetForm(FORM_BUSCAR_RENDICON_VIATICO_NACIONAL);
	
	if($form != null && $form instanceof BuscarRendicionViaticoNacionalForm){
		$dataRendiciones = $form->GetDataRendiciones();
	}
	$cargoFundacionInstanciaActuales = $GLOBALS['SafiRequestVars']['cargoFundacionInstanciaActuales'];
	$dependenciaInstanciaActuales = $GLOBALS['SafiRequestVars']['dependenciaInstanciaActuales'];
	$empleadosElaboradores = $GLOBALS['SafiRequestVars']['empleadosElaboradores'];
	$estatusList = $GLOBALS['SafiRequestVars']['estatusList'];

?>
<html>
	<head>
		<title>.:SAFI:. Buscar rendiciones de vi&aacute;ticos nacionales</title>
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
				establecerFocoInicialCodigoDocumento("idRendicion");
			}
			
		</script>
	</head>
	
	<body class="normal" onload="onLoad();">
		<form name="rendicionBuscarForm" id="rendicionBuscarForm" method="post" action="rendicion.php">
			<input type="hidden" name="accion" value="BuscarRendicion">
			<table cellpadding="0" cellspacing="0" width="640" align="center"
				background="../../../imagenes/fondo_tabla.gif" class="tablaalertas"
			>
				<tr> 
    				<td height="21" colspan="2" class="normalNegroNegrita header" align="left">
    					Buscar rendiciones de vi&aacute;ticos nacionales
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
  					<td class="normalNegrita">C&oacute;digo de la rendici&oacute;n</td>
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
			</table>
			<br/>
			<div align="center">
				<input type="submit" value="Buscar" class="normalNegro">
			</div>
		</form>
		
		<?php 
			if(is_array($dataRendiciones) && count($dataRendiciones)>0){
				echo '
		<table
			cellpadding="0"
			cellspacing="0"
			align="center"
			class="tablaalertas" 
			background="../../imagenes/fondo_tabla.gif"
		>
			<tr class="normalNegroNegrita">
				<td class="header normalNegroNegrita">C&oacute;digo Rendici&oacute;n</td>
				<td class="header normalNegroNegrita">Fecha rendici&oacute;n</td>
				<td class="header normalNegroNegrita">C&oacute;digo Vi&aacute;tico</td>
				<td class="header normalNegroNegrita">Fecha Vi&aacute;tico</td>
				<td class="header normalNegroNegrita">Responsable</td>
				<td class="header normalNegroNegrita">Estatus</td>
				<td class="header normalNegroNegrita">Instancia actual</td>
				<td class="header normalNegroNegrita">Elaborado por</td>
				<td class="header normalNegroNegrita">Acci&oacute;n</td>
			</tr>
				';
				
				$tdClass = "even";
				
				foreach ($dataRendiciones as $idRendicion => $dataRendicion){
					if(
						isset($dataRendicion['ClassDocGenera']) &&
						($docGenera=$dataRendicion['ClassDocGenera']) instanceof EntidadDocGenera &&
						isset($dataRendicion['ClassRendicionViaticoNacional']) &&
						($rendicion=$dataRendicion['ClassRendicionViaticoNacional']) instanceof EntidadRendicionViaticoNacional &&
						isset($dataRendicion['ClassViaticoNacional']) &&
						($viatico=$dataRendicion['ClassViaticoNacional']) instanceof EntidadViaticoNacional
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
							
						$fechaViatico = explode(" ", $viatico->GetFechaViatico());
						
						echo '
			<tr class="resultados class="'.$tdClass.'"" onclick="Registroclikeado(this);"   >
				<td >
					<a href="rendicion.php?accion=VerDetalles&idRendicion='.$rendicion->GetId().'">
						'.$rendicion->GetId().'
					</a>
				</td>
				<td >'.$rendicion->GetFechaRendicion().'</td>
				<td >
					<a href="viaticonacional.php?accion=VerDetalles&idViaticoNacional='.$rendicion->GetIdViaticoNacional().'">
						'.$rendicion->GetIdViaticoNacional().'
					</a>
				</td>
				<td >
					'.$fechaViatico[0].'
				</td>
				<td >
					'.$viatico->GetResponsable()->GetNacionalidad().'-'.
								$viatico->GetResponsable()->GetCedula().'<br/>'.
								mb_strtoupper($viatico->GetResponsable()->GetNombres().' '.$viatico->GetResponsable()->GetApellidos(),
									'ISO-8859-1').
					'
				</td>
				<td>'.$estatusList[$docGenera->GetIdEstatus()]->GetNombre().'</td>				
				<td >'. $cargoNombre  . $dependenciaNombre . '</td>
				<td >'.$empleadosElaboradorString.'</td>
				<td >
					<a href="rendicion.php?accion=VerDetalles&idRendicion='.$rendicion->GetId().'">Ver Detalle</a>
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
		
		
	</body>
</html>