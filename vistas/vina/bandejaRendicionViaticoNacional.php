<?php
	$form = FormManager::GetForm(FORM_BANDEJA_RENDICON_VIATICO_NACIONAL);
	
	$cargoFundacionEnTransitos = $GLOBALS['SafiRequestVars']['cargoFundacionEnTransitos'];
	$dependenciaEnTransitos = $GLOBALS['SafiRequestVars']['dependenciaEnTransitos'];
	$empleadosElaboradoresEnBandejas = $GLOBALS['SafiRequestVars']['empleadosElaboradoresEnBandejas'];
	$empleadosElaboradoresEnTransitos = $GLOBALS['SafiRequestVars']['empleadosElaboradoresEnTransitos'];
?>
<html>
	<head>
		<title>.:SAFI:. Ingresar Vi&aacute;tico Nacional</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		<link href="../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />

		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<script type="text/javascript" src="../../js/constantes.js"></script>
		<script>
			function accionEnviar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea enviar la rendici"+oACUTE+"n de vi"+aACUTE+"tico nacional? ")) 
				{
					return true;
				}

				return false;
			}

			function accionAnular()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea anular la rendici"+oACUTE+"n de vi"+aACUTE+"tico nacional? ")) 
				{
					return true;
				}

				return false;
			}
		</script>
	</head>
	
	<body class="normal">
		<table style="width: 100%;">
			<tr>
				<td><?php include(SAFI_VISTA_PATH . '/mensajes.php');?></td>
			</tr>
			<tr>
				<td style="text-align: center;" class="normalNegroNegrita">
					<span style="padding-bottom: 20px; display: block;"><?php 
						if(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO){
							echo 'Rendiciones de vi&aacute;ticos nacionales devueltas';
						} else {
							echo 'Bandeja de entrada';
						}
					?></span>
				</td>
			</tr>
			<tr>
				<td><table cellpadding="0" cellspacing="0" align="center" style="width: 100%;"
						background="../../imagenes/fondo_tabla.gif" class="tablaalertas"
					>
					<tr>
						<td class="header"><span class="normalNegroNegrita">C&oacute;digo</span></td>
						<td class="header"><span class="normalNegroNegrita">Fecha rendici&oacute;n</span></td>
						<td class="header"><span class="normalNegroNegrita">C&oacute;digo vi&aacute;tico</span></td>
						<td class="header"><span class="normalNegroNegrita">Fecha vi&aacute;tico</span></td>
						<td class="header"><span class="normalNegroNegrita">Monto vi&aacute;tico</span></td>
						<td class="header"><span class="normalNegroNegrita">Responsable</span></td>
						<td class="header"><span class="normalNegroNegrita">Elaborado por</span></td>
						<td class="header"><span class="normalNegroNegrita">Opciones</span></td>
					</tr>
					<?php
					if(is_array($enBandeja=$form->GetEnBandeja()) && count($enBandeja)>0)
					{
						$tdClass = "even";
						
						foreach ($enBandeja as $idRendicion => $dataRendicion)
						{
							if(	isset($dataRendicion['ClassDocGenera']) && 
								($docGenera=$dataRendicion['ClassDocGenera']) instanceof EntidadDocGenera &&
								isset($dataRendicion['ClassRendicionViaticoNacional']) &&
								($rendicion=$dataRendicion['ClassRendicionViaticoNacional']) instanceof EntidadRendicionViaticoNacional &&
								isset($dataRendicion['ClassViaticoNacional']) &&
								($viatico=$dataRendicion['ClassViaticoNacional']) instanceof EntidadViaticoNacional
							){
								$tdClass = ($tdClass == "even") ? "odd" : "even";
								
								//$empleadosElaboradores
								$empleadosElaborador = null;
								if(is_array($empleadosElaboradoresEnBandejas)){
									$empleadosElaborador = $empleadosElaboradoresEnBandejas[$docGenera->GetUsuaLogin()];
								}
								$empleadosElaboradorString = ($empleadosElaborador != null && $empleadosElaborador instanceof EntidadEmpleado) ?
									mb_strtoupper($empleadosElaborador->GetNombres()." ".$empleadosElaborador->GetApellidos(), "ISO-8859-1")
									: "---";
					?>
					<tr>
						<td >
							<a
								href="rendicion.php?accion=VerDetalles&idRendicion=<?php echo $rendicion->GetId()?>"
							>
								<?php echo $rendicion->GetId() ?>
							</a>
						</td>
						<td ><?php echo $rendicion->GetFechaRendicion() ?></td>
						<td >
							<a
								href="viaticonacional.php?accion=VerDetalles&idViaticoNacional=<?php
									echo $rendicion->GetIdViaticoNacional()
								?>"
							>
								<?php echo $rendicion->GetIdViaticoNacional() ?>
							</a>
						</td>
						<td ><?php
							$fecha = explode(" ", $viatico->GetFechaViatico());
							echo $fecha[0];
						?></td>
						<td style="text-align: right; padding-right: 25px;"><?php
							if(is_array($viatico->GetViaticoResponsableAsignaciones())){
								$montoTotal = CalcularMontoTotalAsignacionesViaticoNacional($viatico->GetViaticoResponsableAsignaciones());
								echo number_format($montoTotal,2,',','.');
							}
							
						?></td>
						<td ><?php
							echo 
								$viatico->GetResponsable()->GetNacionalidad().'-'.
								$viatico->GetResponsable()->GetCedula().' '.
								mb_strtoupper($viatico->GetResponsable()->GetNombres().' '.$viatico->GetResponsable()->GetApellidos(),
									'ISO-8859-1')
						?></td>
						<td ><?php echo $empleadosElaboradorString ?></td>
						<td >
						<?php 
							if(
								substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
								$_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO ||
								$_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA
							){
								echo '
									<a href="rendicion.php?accion=VerDetalles&idRendicion='.$rendicion->GetId().'">
										Ver Detalle
									</a>
									<br/>
									<a href="rendicion.php?accion=Modificar&idRendicion='.$rendicion->GetId().'">
										Modificar
									</a>
									<br/>
									<a
										href="rendicion.php?accion=Enviar&idRendicion='.$rendicion->GetId().'"
										onclick="return accionEnviar();"
									>
										Enviar
									</a>
									<br />
									<a
										href="rendicion.php?accion=Anular&idRendicion='.$rendicion->GetId().'"
										onclick="return accionAnular();"
									>
										Anular
									</a>
								';
							} else if(
									substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_GERENTE ||
									substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_DIRECTOR ||
									$_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO ||
									$_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_EJECUTIVO ||
									$_SESSION['user_perfil_id'] == PERFIL_PRESIDENTE
								){
									echo '
										<a href="rendicion.php?accion=VerDetalles&idRendicion='.$rendicion->GetId().'">
											Revisar
										</a>
									';
								} else if($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO)
								{
									echo '
										<a href="rendicion.php?accion=VerDetalles&idRendicion='.$rendicion->GetId().'">
											Ver Detalle
										</a>
									';
								}
						?>
						</td>
					</tr>
					<?php
							}
						}
					} else {
						echo '
							<tr>
								<td class="odd" colspan="8">
									No existen documentos en bandeja
								</td>
							</tr>
						';
						
					}
					?>
				</table></td>
			</tr>
			<?php 
			if(
				substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
				$_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO ||
				$_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA
			){
			?>
			<tr>
				<td style="text-align: center;" class="normalNegroNegrita">
					<span style="padding-top: 50px; padding-bottom: 20px; display: block;">Pendientes por enviar</span>
				</td>
			</tr>
			<tr>
				<td><table cellpadding="0" cellspacing="0" align="center" style="width: 100%;"
						background="../../imagenes/fondo_tabla.gif" class="tablaalertas"
					>
					<tr>
						<td class="header"><span class="normalNegroNegrita">C&oacute;digo</span></td>
						<td class="header"><span class="normalNegroNegrita">Fecha rendici&oacute;n</span></td>
						<td class="header"><span class="normalNegroNegrita">C&oacute;digo vi&aacute;tico</span></td>
						<td class="header"><span class="normalNegroNegrita">Fecha vi&aacute;tico</span></td>
						<td class="header"><span class="normalNegroNegrita">Responsable</span></td>
						<td class="header"><span class="normalNegroNegrita">Opciones</span></td>
					</tr>
					<?php
					if(is_array($porEnviar=$form->GetPorEnviar()) && count($porEnviar)>0)
					{
						$tdClass = "even";
						
						foreach ($porEnviar as $dataRendicion)
						{
							if(	isset($dataRendicion['ClassDocGenera']) && 
								($docGenera=$dataRendicion['ClassDocGenera']) instanceof EntidadDocGenera &&
								isset($dataRendicion['ClassRendicionViaticoNacional']) &&
								($rendicion=$dataRendicion['ClassRendicionViaticoNacional']) instanceof EntidadRendicionViaticoNacional &&
								isset($dataRendicion['ClassViaticoNacional']) &&
								($viatico=$dataRendicion['ClassViaticoNacional']) instanceof EntidadViaticoNacional
							){
								$tdClass = ($tdClass == "even") ? "odd" : "even";
					?>
					<tr class="<?php echo $tdClass ?>" onclick="Registroclikeado(this);" >
						<td >
							<a
								href="rendicion.php?accion=VerDetalles&idRendicion=<?php echo $rendicion->GetId()?>"
							>
								<?php echo $rendicion->GetId() ?>
							</a>
						</td>
						<td><?php echo $rendicion->GetFechaRendicion() ?></td>
						<td>
							<a
								href="viaticonacional.php?accion=VerDetalles&idViaticoNacional=<?php
									echo $rendicion->GetIdViaticoNacional()
								?>"
							>
								<?php echo $rendicion->GetIdViaticoNacional() ?>
							</a>
						</td>
						<td ><?php
							$fecha = explode(" ", $viatico->GetFechaViatico());
							echo $fecha[0];
						?></td>
						<td ><?php
							echo 
								$viatico->GetResponsable()->GetNacionalidad().'-'.
								$viatico->GetResponsable()->GetCedula().' '.
								mb_strtoupper($viatico->GetResponsable()->GetNombres().' '.$viatico->GetResponsable()->GetApellidos(),
									'ISO-8859-1')
						?></td>
						<td >
							<a href="rendicion.php?accion=VerDetalles&idRendicion=<?php echo $rendicion->GetId()?>">
								Ver Detalle
							</a>
							<br/>
							<a href="rendicion.php?accion=Modificar&idRendicion=<?php echo $rendicion->GetId()?>">Modificar</a>
							<br/>
							<a
								href="rendicion.php?accion=Enviar&idRendicion=<?php echo $rendicion->GetId()?>"
								onclick="return accionEnviar();"
							>Enviar</a>
							<br/>
							<a
								href="rendicion.php?accion=Anular&idRendicion=<?php echo $rendicion->GetId()?>"
								onclick="return accionAnular();"
							>
								Anular
							</a>
						</td>
					</tr>
					<?php
							}
						}
					} else {
						echo '
							<tr>
								<td class="odd" colspan="6">
									No existen documentos por enviar
								</td>
							</tr>
						';
					}
					?>
				</table></td>
			</tr>
			<?php 
			}
			// Bandeja en tránsito
			if(
				$idPerfil == PERFIL_ANALISTA_PRESUPUESTO
				|| substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO
				|| $_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO
				|| $_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA
				|| $idPerfil == PERFIL_JEFE_PRESUPUESTO
				|| substr($idPerfil,0,2)."000" == PERFIL_GERENTE
				|| substr($idPerfil,0,2)."000" == PERFIL_DIRECTOR
				|| $idPerfil == PERFIL_DIRECTOR_EJECUTIVO
				|| $idPerfil == PERFIL_PRESIDENTE
			){
			?>
			<tr>
				<td style="text-align: center;" class="normalNegroNegrita">
					<span style="padding-top: 50px; padding-bottom: 20px; display: block;">En tr&aacute;nsito</span>
				</td>
			</tr>
			<tr>
				<td><table cellpadding="0" cellspacing="0" 
						align="center" style="width: 100%;" background="../../imagenes/fondo_tabla.gif" class="tablaalertas"
					>
					<tr>
						<td class="header normalNegroNegrita">C&oacute;digo</td>
						<td class="header normalNegroNegrita">Fecha rendici&oacute;n</td>
						<td class="header normalNegroNegrita">C&oacute;digo vi&aacute;tico</td>
						<td class="header normalNegroNegrita">Fecha vi&aacute;tico</td>
						<td class="header normalNegroNegrita">Responsable</td>
						<td class="header normalNegroNegrita">Instancia actual</td>
						<td class="header normalNegroNegrita">Elaborado por</td>
						<td class="header normalNegroNegrita">Opciones</td>
					</tr>
					<?php
					if(is_array($enTransito=$form->GetEnTransito()) && count($enTransito)>0)
					{
						$tdClass = "even";
						foreach ($enTransito as $idRendicion => $dataRendicion)
						{
							if(	isset($dataRendicion['ClassDocGenera']) && 
								($docGenera=$dataRendicion['ClassDocGenera']) instanceof EntidadDocGenera &&
								isset($dataRendicion['ClassRendicionViaticoNacional']) &&
								($rendicion=$dataRendicion['ClassRendicionViaticoNacional']) instanceof EntidadRendicionViaticoNacional &&
								isset($dataRendicion['ClassViaticoNacional']) &&
								($viatico=$dataRendicion['ClassViaticoNacional']) instanceof EntidadViaticoNacional
							){
								$tdClass = ($tdClass == "even") ? "odd" : "even";
								
								$cargo = null;
								if(is_array($cargoFundacionEnTransitos)){
									$cargo =  $cargoFundacionEnTransitos[GetCargoFundacionFromIdPerfil($docGenera->GetIdperfilActual())];
								}
								$cargoNombre = ($cargo != null && $cargo instanceof EntidadCargo) ? $cargo->GetNombre() : '';
								
								$dependencia = null;
								if(is_array($dependenciaEnTransitos)){
									$dependencia = $dependenciaEnTransitos[GetIdDependenciaFromIdPerfil($docGenera->GetIdperfilActual())];
								}
								$dependenciaNombre =  ($dependencia != null && $dependencia instanceof EntidadDependencia) ?
										'(<span style="font-style: italic">' . $dependencia->GetNombre() . '</span>)' : '';
								
								//$empleadosElaboradores
								$empleadosElaborador = null;
								if(is_array($empleadosElaboradoresEnTransitos)){
									$empleadosElaborador = $empleadosElaboradoresEnTransitos[$docGenera->GetUsuaLogin()];
								}
								$empleadosElaboradorString = ($empleadosElaborador != null && $empleadosElaborador instanceof EntidadEmpleado) ?
									mb_strtoupper($empleadosElaborador->GetNombres()." ".$empleadosElaborador->GetApellidos(), "ISO-8859-1")
									: "---";
					?>
					<tr class="<?php echo $tdClass ?>" onclick="Registroclikeado(this);" >
						<td >
							<a
								href="rendicion.php?accion=VerDetalles&idRendicion=<?php echo $rendicion->GetId()?>"
							>
								<?php echo $rendicion->GetId() ?>
							</a>
						</td>
						<td ><?php echo $rendicion->GetFechaRendicion() ?></td>
						<td >
							<a
								href="viaticonacional.php?accion=VerDetalles&idViaticoNacional=<?php
									echo $rendicion->GetIdViaticoNacional()
								?>"
							>
								<?php echo $rendicion->GetIdViaticoNacional() ?>
							</a>
						</td>
						<td ><?php
							$fecha = explode(" ", $viatico->GetFechaViatico());
							echo $fecha[0];
						?></td>
						<td ><?php
							echo 
								$viatico->GetResponsable()->GetNacionalidad().'-'.
								$viatico->GetResponsable()->GetCedula().' '.
								mb_strtoupper($viatico->GetResponsable()->GetNombres().' '.$viatico->GetResponsable()->GetApellidos(),
									'ISO-8859-1')
						?></td>
						<td ><?php echo $cargoNombre . " ". $dependenciaNombre ?></td>
						<td ><?php echo $empleadosElaboradorString ?></td>
						<td>
							<a href="rendicion.php?accion=VerDetalles&idRendicion=<?php echo $rendicion->GetId()?>">
								Ver Detalle
							</a>
						</td>
					</tr>
					<?php
							} // Fin de if(	isset($dataRendicion['ClassDocGenera']) && 
							  // ($docGenera=$dataRendicion['ClassDocGenera']) instanceof EntidadDocGenera &&
							  // isset($dataRendicion['ClassRendicionViaticoNacional']) &&
							  // ($viatico=$dataRendicion['ClassRendicionViaticoNacional']) instanceof EntidadRendicionViaticoNacional
						} // Fin foreach ($enTransito as $idViatico => $DataViaticoNacional)
					} else {
						echo '
							<tr>
								<td class="odd" colspan="8">
									No existen documentos en tr&aacute;nsito
								</td>
							</tr>
						';
						
					}
					?>
				</table></td>
			</tr>
			<?php 
			}
			?>
		</table>
	</body>
</html>