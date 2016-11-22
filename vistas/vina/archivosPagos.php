<?php
	$form = FormManager::GetForm(FORM_ARCHIVOS_PAGOS_VIATICO_NACIONAL);
	$viatico = null;
	
	if($form != null)
	{
		$viatico = $form->GetViatico();
	}
?>
<html>
	<head>
		<title>.:SAFI:. Archivos de pagos de vi&aacute;ticos nacionales</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		<link href="../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />
		<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
		
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript" src="../../js/constantes.js"></script>
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<script>
			g_Calendar.setDateFormat('dd/mm/yyyy');
		</script>
	</head>
	
	<body class="normal">
		<table align="center">
			<tr>
				<td><?php include(SAFI_VISTA_PATH . '/mensajes.php');?></td>
			</tr>
			<?php
				if ($viatico != null){
			?>
			<tr>
				<td>
					<table  cellpadding="0" cellspacing="0"  align="center" background="../../imagenes/fondo_tabla.gif"
						class="tablaalertas content" width="800px;"
					>
						<tr class="td_gray">
							<td colspan="2" style="text-align: center;" class="normalNegroNegrita header documentTitle">
								.: Vi&aacute;tico <?php echo $viatico->GetId() ?> :.
							</td>
						</tr>
						<tr>
							<td class="normalNegrita">Fecha del vi&aacute;tico</td>
							<td><?php echo $viatico->GetFechaViatico() ?></td>
						</tr>
						<?php
							if($viatico->GetResponsable() != null && $viatico->GetResponsable() instanceof EntidadResponsableViatico)
							{
								$responsable = $viatico->GetResponsable();
								$montoTotal = CalcularMontoTotalAsignacionesViaticoNacional($viatico->GetViaticoResponsableAsignaciones());
						?>
						<tr>
							<td colspan="2" class="normalNegroNegrita header">Responsable</td>
						</tr>
						<tr>
							<td colspan="2"><table style="width: 100%;">
								<tr>
									<td class="normalNegroNegrita">Responsable</td>
									<td class="normalNegroNegrita">N&uacute;mero de cuenta</td>
									<td class="normalNegroNegrita">Banco</td>
									<td class="normalNegroNegrita">Monto</td>
								</tr>
								<tr>
									<td><?php
										echo mb_strtoupper($responsable->GetNombres().' '. $responsable->GetApellidos(), 'ISO-8859-1')
									?></td>
									<td><?php echo $responsable->GetNumeroCuenta() ?></td>
									<td><?php echo $responsable->GetBanco() ?></td>
									<td><?php echo number_format($montoTotal,2,',','.') ?></td>
								</tr>
							</table></td>
						</tr>
						<?php
							}
						?>
					</table>
					<br/>
					<br/>
					<form id="generarArchivoPagosViaticoNacionalForm" action="viaticonacional.php" method="post">
						<input
							id="hiddenAccion"
							type="hidden"
							name="accion"
							value="GenerarArchivosPagos"
						>
						<input
							id="hiddenAccion"
							type="hidden"
							name="idViatico"
							value="<?php echo $viatico->GetId() ?>"
						>
						<table cellpadding="0" cellspacing="0" align="center" background="../../imagenes/fondo_tabla.gif"
							class="tablaalertas content" width="640px;"
						>
							<tr>
								<td colspan="2" class="normalNegroNegrita header documentTitle">.:Par&aacute;metros del archivo:.</td>
							</tr>
							<tr>
								<td class="normalNegrita">Fecha de abono</td>
								<td class="normalNegro">
									<input
										type="text"
										size="10"
										id="fechaAbono"
										name="fechaAbono"
										class="dateparse"
										readonly="readonly"
										value="<?php
											echo $form->GetFechaAbono()
										?>"
									/>
									<a 
										href="javascript:void(0);" 
										onclick="g_Calendar.show(event, 'fechaAbono');" 
										title="Show popup calendar"
									><img 
											src="../../js/lib/calendarPopup/img/calendar.gif" 
											class="cp_img" 
											alt="Open popup calendar"
									/></a>
								</td>
							</tr>
						</table>
						<br/>
						<div align="center">
							<input type="submit" value="Generar archivo" class="normalNegro">
						</div>
					</form>
				</td>
			</tr>
			<?php
				}
			?>
		</table>
	</body>
</html>
