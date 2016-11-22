<?php
	$form = FormManager::GetForm(FORM_REPORTE_1_VIATICO_NACIONAL);
	
	if($form != null && $form instanceof Reporte1ViaticoNacionalForm){
		$ViaticosNacionales = $form->GetViaticosNacionales();
	}
?>

<html>
	<head>
		<title>.:SAFI:. Busqueda detallada de Viatico Nacional</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
		<link href="../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript" src="../../js/constantes.js"></script>
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<script type="text/javascript">
		
			g_Calendar.setDateFormat('dd/mm/yyyy');

			function comparar_fechas(elemento){
				
				var fecha_inicial=document.getElementById("txt_inicio").value;
				var fecha_final=document.getElementById("hid_hasta_itin").value;
				
				var dia1 =fecha_inicial.substring(0,2);
				var mes1 =fecha_inicial.substring(3,5);
				var anio1=fecha_inicial.substring(6,10);
				
				var dia2 =fecha_final.substring(0,2);
				var mes2 =fecha_final.substring(3,5);
				var anio2=fecha_final.substring(6,10);
		
				dia1 = parseInt(dia1,10);
				mes1 = parseInt(mes1,10);
				anio1= parseInt(anio1,10);
		
				dia2 = parseInt(dia2,10);
				mes2 = parseInt(mes2,10);
				anio2= parseInt(anio2,10); 
					
				if ( (anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || 
				 ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2)) ){
				  alert("La fecha inicial no debe ser mayor a la fecha final"); 
				  elemento.value='';
				  return;
				}
			}
			
		</script>		
	</head>
	
	<body class="normal">
		<form name="reporte1ViaticoForm" id="reporte1ViaticoForm" method="post" action="viaticonacional.php">
			<input type="hidden" name="accion" value="reporte1">
			<table cellpadding="0" cellspacing="0" width="640" align="center"
				background="../../../imagenes/fondo_tabla.gif" class="tablaalertas"
			>
				<tr> 
    				<td height="21" colspan="2" class="normalNegroNegrita header" align="left">
    					B&uacute;squeda detallada de vi&aacute;ticos nacionales
    				</td>
				</tr>
				<tr>
					<td height="10" colspan="2"></td>
				</tr>
  				<tr>
  					<td class="normalNegrita">Creados entre:</td>
  					<td>
  						<input
  							type="text"
  							size="10"
  							id="txt_inicio"
  							name="fechaInicio"
  							class="dateparse"
							onfocus="javascript: comparar_fechas(this);"
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
							onfocus="javascript: comparar_fechas(this);"
							readonly="readonly"
							value=""
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
  							type="text"
  							name="idViaticoNacioanal"
  							class="normalNegro"
  							value=""
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
			if(is_array($ViaticosNacionales) && count($ViaticosNacionales)>0){
				
				echo  '
		<table
			cellpadding="0"
			cellspacing="0"
			align="center"
			class="tablaalertas" 
			background="../../imagenes/fondo_tabla.gif"
		>
			<tr class="normalNegroNegrita">
				<td class="header normalNegroNegrita">C&oacute;digo Vi&aacute;tico</td>
				<td class="header normalNegroNegrita">Elaborado en Fecha</td>
				<td class="header normalNegroNegrita">Categor&iacute;a</td>
				<td class="header normalNegroNegrita">Fecha Inicio Viaje</td>
				<td class="header normalNegroNegrita">Fecha Fin Viaje</td>
				<td class="header normalNegroNegrita">Rutas</td>
				<td class="header normalNegroNegrita">Estado</td>
				<td class="header normalNegroNegrita">Fuente Financiamiento</td>
				<td class="header normalNegroNegrita">Compromiso</td>
				<td class="header normalNegroNegrita">CI del<br/>Responsable</td>
				<td class="header normalNegroNegrita">Nombre del<br/>Responsable</td>
				<td class="header normalNegroNegrita">Objetivos viaje</td>
				<td class="header normalNegroNegrita">Gerencia Solicitante</td>
				<td class="header normalNegroNegrita">Centro de Costo / Centro Gestor</td>
				<td class="header normalNegroNegrita">Monto</td>
			</tr>
				';
				
				$tdClass = "even";
				
				foreach ($ViaticosNacionales as $idViatico => $DataViaticoNacional){
					if(	
						isset($DataViaticoNacional['ClassDocGenera']) && 
						($docGenera=$DataViaticoNacional['ClassDocGenera']) instanceof EntidadDocGenera &&
						isset($DataViaticoNacional['ClassVaiticoNacional']) &&
						($viatico=$DataViaticoNacional['ClassVaiticoNacional']) instanceof EntidadViaticoNacional
					){
						$tdClass = ($tdClass == "even") ? "odd" : "even";
						
						if(
							isset($DataViaticoNacional['ClassCompromiso']) &&
							($compromiso=$DataViaticoNacional['ClassCompromiso']) instanceof EntidadCompromiso
						){
							$idCompromiso = $compromiso->GetId();  
						} else{
							$idCompromiso = '--';
						}
						
						if(
							isset($DataViaticoNacional['ClassFuenteFinanciamiento']) &&
							($fuenteFinanciamiento=$DataViaticoNacional['ClassFuenteFinanciamiento'])
								instanceof EntidadFuenteFinanciamiento
						){
							$idFuenteFinanciamiento = $fuenteFinanciamiento->GetId();  
						} else{
							$idFuenteFinanciamiento = '--';
						}
						
						if(
							isset($DataViaticoNacional['ClassEstatus']) &&
							($estatus=$DataViaticoNacional['ClassEstatus'])
								instanceof EntidadEstatus
						){
							$nombreEstatus = $estatus->GetNombre();  
						} else{
							$nombreEstatus = '--';
						}
						
						$arrEstadoRutas = array();
						
						if($viatico->GetRutas()!=null && is_array($viatico->GetRutas())){
							foreach($viatico->GetRutas() as $ruta){
								$arrEstadoRutas[] = $ruta->GetNombreFromEstado()."-".$ruta->GetNombreToEstado();
							}
						}
						
						if(count($arrEstadoRutas)>0){
							$nombreEstados = "[" .implode("], [", $arrEstadoRutas) . "]";
						} else {
							$nombreEstados = "--";
						}
						echo '
			<tr class="resultados class="'.$tdClass.'"" onclick="Registroclikeado(this);">
				<td >
					<a href="viaticonacional.php?accion=VerDetalles&idViaticoNacional='.$viatico->GetId().'">
						' . $viatico->GetId() . '
					</a>
				</td>
				<td >
					'.$viatico->GetFechaViatico().'
				</td>
				<td >
					'.($viatico->GetCategoriaViatico()!=null ? $viatico->GetCategoriaViatico()->GetNombre() : "").'
				</td>
				<td >
					'.$viatico->GetFechaInicioViaje().'
				</td>
				<td >
					'.$viatico->GetFechaFinViaje().'
				</td>
				<td >
					'.$nombreEstados.'
				</td>
				<td >
					'.$nombreEstatus.'
				</td>
				<td >
					'.$idFuenteFinanciamiento.'
				</td>
				<td >
					'.$idCompromiso.'
				</td>
				<td >
					'.$viatico->GetResponsable()->GetNacionalidad().'-'.$viatico->GetResponsable()->GetCedula().'
				</td>
				<td >
					'.mb_strtoupper($viatico->GetResponsable()->GetNombres().' '
					.$viatico->GetResponsable()->GetApellidos(), 'ISO-8859-1').'
				</td>
				<td >
					'.$viatico->GetObjetivosViaje().'
				</td>
				<td >
					'.$viatico->GetDependencia()->GetNombre().'
				</td>
				<td >
					'.$viatico->GetCentroGestor().'/'.$viatico->GetCentroCosto().'
				</td>
				<td >
					'.number_format($viatico->GetMontoTotal(),2,',','.').'
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