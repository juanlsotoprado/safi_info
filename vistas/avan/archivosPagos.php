<?php
	$form = FormManager::GetForm(FORM_ARCHIVOS_PAGOS_AVANCE);
	$avance = null;
	
	if($form != null)
	{
		$avance = $form->GetAvance();
	}
?>
<html>
	<head>
		<title>.:SAFI:. Archivos de pagos de Avances</title>
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
			<tr>
				<td>
					<form name="avanceArchivosPagosForm" id="avanceArchivosPagosForm" method="post" action="avance.php">
						<input type="hidden" name="accion" value="archivosPagos">
						<table cellpadding="0" cellspacing="0" width="640" align="center"
							background="../../../imagenes/fondo_tabla.gif" class="tablaalertas"
						>
							<tr> 
			    				<td height="21" colspan="2" class="normalNegroNegrita header" align="left">
			    					B&uacute;squeda de avances
			    				</td>
							</tr>
			  				<tr>
								<td height="10" colspan="2"></td>
							</tr>
			  				<tr>
			  					<td class="normalNegrita">C&oacute;digo del avance</td>
			  					<td>
			  						<input
			  							type="text"
			  							name="idAvance"
			  							class="normalNegro"
			  							value="<?php echo $form->GetIdAvance()?>"
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
		</table>
		<?php
			if ($avance != null){
		?>
		<table  cellpadding="0" cellspacing="0" align="center" background="../../imagenes/fondo_tabla.gif"
			class="tablaalertas content" width="800px;"
		>
			<tr class="td_gray">
				<td colspan="2" style="text-align: center;" class="normalNegroNegrita header documentTitle">
					.: Avance <?php echo $avance->GetId() ?> :.
				</td>
			</tr>
			<tr>
				<td class="normalNegrita">Fecha del avance</td>
				<td><?php echo $avance->GetFechaAvance(); ?></td>
			</tr>
			<tr>
				<td colspan="2" class="normalNegroNegrita header">Responsables</td>
			</tr>
			<tr>
				<td colspan="2">
				<?php 
					//responsablesAvancePartidas
				if(is_array($avance->GetResponsablesAvancePartidas()))
				{
					$montoTotal = 0;
					
					echo '
					<table style="width: 100%;">
						<tr>
							<td class="normalNegroNegrita">Responsable</td>
							<td class="normalNegroNegrita">N&uacute;mero de cuenta</td>
							<td class="normalNegroNegrita">Banco</td>
							<td class="normalNegroNegrita">Monto</td>
						</tr>
					';
					foreach ($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidas)
					{
						$responsableAvance = $responsableAvancePartidas->GetResponsableAvance();
						$id = "";
						$nombre = "";
						$tipoCuenta = "";
						
						// Obtener los datos del empleado/beneficiario
						if(
							$responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_EMPLEADO
							&& $responsableAvance->GetEmpleado() != null
						){
							$empleado = $responsableAvance->GetEmpleado();

							$id = $empleado->GetId();
							$nombre = mb_strtoupper($empleado->GetNombres() . ' ' .$empleado->GetApellidos(), "ISO-8859-1");
						}
						else if (
							$responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_BENEFICIARIO
							&& $responsableAvance->GetBeneficiario() != null
						){
							$beneficiario = $responsableAvance->GetBeneficiario();
							
							$id = $beneficiario->GetId();
							$nombre = mb_strtoupper($beneficiario->GetNombres() . ' ' .$beneficiario->GetApellidos(), "ISO-8859-1");
						}
						
						// Obtener los datos del tipo de cuanta bancarios
						if(strcmp($responsableAvance->GetTipoCuenta(), EntidadTipoCuentabancaria::CUENTA_DE_AHORRO) == 0)
						{
							$tipoCuenta = "Ahorro";
						}
						elseif (strcmp($responsableAvance->GetTipoCuenta(), EntidadTipoCuentabancaria::CUENTA_CORRRIENTE) == 0)
						{
							$tipoCuenta = "Corriente";
						}
						
						// Calcular el total del monto por responsable
						$responsableMontoTotal = 0;
						if(is_array($responsableAvancePartidas->GetAvancePartidas()))
						{
							foreach ($responsableAvancePartidas->GetAvancePartidas() as $avanPartida)
							{
								$responsableMontoTotal += $avanPartida->GetMonto();
								$montoTotal += $avanPartida->GetMonto();
							}
						}
						
						$numeroCuenta = ($responsableAvance != null && trim($responsableAvance->GetNumeroCuenta()) != '')
							? $responsableAvance->GetNumeroCuenta() : "---";
							
						$nombreBanco = $responsableAvance != null && trim($responsableAvance->GetBanco()) != ''
							? $responsableAvance->GetBanco() : "---";
						
						echo '
						<tr>
							<td>'.$nombre.'</td>
							<td>'.$numeroCuenta.'</td>
							<td>'.$nombreBanco.'</td>
							<td>'.number_format($responsableMontoTotal,2,',','.').'</td>
						</tr>
				
						';
					}// Fin foreach ($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidas)
					echo '
						<tr>
							<td colspan="3" class="normalNegrita">Total</td>
							<td class="normalNegrita">'.number_format($montoTotal,2,',','.').'</td>
						</tr>
					</table>	
					';
				}
				?>
				</td>
			</tr>
		</table>
		<br/>
		<br/>
		<form id="generarArchivoPagosAvanceForm" action="avance.php" method="post">
			<input
				id="hiddenAccion"
				type="hidden"
				name="accion"
				value="GenerarArchivosPagos"
			>
			<input
				id="hiddenAccion"
				type="hidden"
				name="idAvance"
				value="<?php echo $avance->GetId() ?>"
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
		
		
		<?php 
			}
		?>
		
	</body>
</html>