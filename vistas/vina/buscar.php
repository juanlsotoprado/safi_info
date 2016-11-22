<?php

	require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');	

	$form = FormManager::GetForm('buscarViaticoNacional');
	
	$cargoFundacionInstanciaActuales = $GLOBALS['SafiRequestVars']['cargoFundacionInstanciaActuales'];
	$dependenciaInstanciaActuales = $GLOBALS['SafiRequestVars']['dependenciaInstanciaActuales'];
?>
<html>
	<head>
		<title>.:SAFI:. Buscar vi&aacute;ticos nacionales</title>
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
				establecerFocoInicialCodigoDocumento("idViaticoNacioanal");
			}
			
		</script>
	</head>
	
	<body class="normal" onload="onLoad();">
		<form name="viaticoNacionalBuscarForm" id="viaticoNacionalBuscarForm" method="post" action="viaticonacional.php">
			<input type="hidden" name="accion" value="buscar">
			<table align="center">
				<tr>
					<td><?php include(SAFI_VISTA_PATH . '/mensajes.php');?></td>
				</tr>
				<tr>
					<td><table cellpadding="0" cellspacing="0" width="640" align="center"
						background="../../../imagenes/fondo_tabla.gif" class="tablaalertas"
					>
						<tr> 
		    				<td height="21" colspan="2" class="normalNegroNegrita header" align="left">
		    					Buscar vi&aacute;ticos nacionales
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
		  					<td class="normalNegrita">C&oacute;digo del documento</td>
		  					<td>
		  						<input
		  							id="idViaticoNacioanal"
		  							type="text"
		  							name="idViaticoNacioanal"
		  							class="normalNegro"
		  							value="<?php echo $form->GetIdViaticoNacional() ?>"
		  						>
		  					</td>
		  				</tr>
					</table></td>
				</tr>
			</table>
			<br/>
			<div align="center">
				<input type="submit" value="Buscar" class="normalNegro">
			</div>
		</form>
		
		<?php
		
			if(is_array($ViaticosNacionales=$form->GetViaticosNacionales()) && count($ViaticosNacionales)>0){
				
				$rows = '
					<tr class="normalNegroNegrita">
						<td class="header normalNegroNegrita">C&oacute;digo</td>
						<td class="header normalNegroNegrita">Elaborado en fecha</td>
						<td class="header normalNegroNegrita">Instancia actual</td>
						<td class="header normalNegroNegrita">Responsable</td>
						<td class="header normalNegroNegrita">Proy/Acc</td>
						<td class="header normalNegroNegrita">Dependencia</td>
						<td class="header normalNegroNegrita">Acci&oacute;n</td>
					</tr>
				';
				
				$tdClass = "even";
				
				foreach ($ViaticosNacionales as $idViatico => $DataViaticoNacional){
					
					if(	isset($DataViaticoNacional['ClassDocGenera']) && 
									($docGenera=$DataViaticoNacional['ClassDocGenera']) instanceof EntidadDocGenera &&
									isset($DataViaticoNacional['ClassVaiticoNacional']) &&
									($viatico=$DataViaticoNacional['ClassVaiticoNacional']) instanceof EntidadViaticoNacional
					){
					
						$tdClass = ($tdClass == "even") ? "odd" : "even";
						$inputRendir = '';
						
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
								'<br/>(<span style="font-style: italic"> ' . $dependencia->GetNombre() . ' </span>)' : '';
						
						if(	substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO && false){
							$inputRendir = '
								<br/>
								<a href="viaticonacional.php?accion=Rendir&idViaticoNacional='.$viatico->GetId().'">Rendir</a>
							';
						}
						
						$rows .= '
							<tr class="resultados '.$tdClass.'" onclick="Registroclikeado(this);">
								<td><a
									href="viaticonacional.php?accion=VerDetalles&idViaticoNacional='.$viatico->GetId().'">'.
										$viatico->GetId().'
								</a></td>
								<td >'.$viatico->GetFechaViatico().'</td>								
								<td>
									'. $cargoNombre  . $dependenciaNombre . '
								</td>
								<td >'
									./*(strcmp($viatico->GetResponsable()->GetTipoEmpleado(), EntidadResponsableViatico::TIPO_EMPLEADO) == 0 ? 
										'Empleado' : $viatico->GetResponsable()->GetTipoEmpleado()
									).': '.*/
									$viatico->GetResponsable()->GetNacionalidad().'-'.
									$viatico->GetResponsable()->GetCedula().' <br/> '.
									mb_strtoupper($viatico->GetResponsable()->GetNombres().' '.$viatico->GetResponsable()->GetApellidos(),
										'ISO-8859-1').'
								</td>
								<td >'.$viatico->GetCentroGestor().'/'.$viatico->GetCentroCosto().'</td>
								<td >'.$viatico->GetDependencia()->GetNombre().'</td>
								<td >
									<a href="viaticonacional.php?accion=VerDetalles&idViaticoNacional='.$viatico->GetId().'">
										Ver Detalle
									</a>
									'.$inputRendir.'
								</td>
							</tr>
						';
					}
					
				}
				
				$output = '
					<table cellpadding="0" cellspacing="0" align="center" class="tablaalertas" 
						background="../../imagenes/fondo_tabla.gif"
					>
						'.$rows.'
					</table>		
				';
				
				echo $output;
			}
		?>
	</body>
</html>
