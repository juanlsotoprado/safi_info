<?php
	$enBandeja = $GLOBALS['SafiRequestVars']['avancesEnBandeja'];
	$empleadosElaboradoresEnBandejas = $GLOBALS['SafiRequestVars']['empleadosElaboradoresEnBandejas'];
	$porEnviar = $GLOBALS['SafiRequestVars']['avancesPorEnviar'];
	$enTransito = $GLOBALS['SafiRequestVars']['avancesEnTransito'];
	
	$cargoFundacionEnTransitos = $GLOBALS['SafiRequestVars']['avancesCargoFundacionEnTransitos'];
	$dependenciaEnTransitos = $GLOBALS['SafiRequestVars']['avancesDependenciaEnTransitos'];
	$empleadosElaboradoresEnTransitos = $GLOBALS['SafiRequestVars']['avancesEmpleadosElaboradoresEnTransitos'];
?>
<html>
	<head>
		<title>.:SAFI:. Bandeja de Avance</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		<link href="../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />
		<!--                   onclick="Registroclikeado(this);"                --> 
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript" src="../../js/constantes.js"></script>
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<script>
			function accionEnviar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea enviar el avance? ")) 
				{
					return true;
				}

				return false;
			}

			function accionAnular()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea anular el avance? ")) 
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
						if(
							substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO
							|| $_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA
							|| $_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO
						){
							echo 'Avances devueltos';
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
						<td class="header"><span class="normalNegroNegrita">Fecha avance</span></td>
						<td class="header"><span class="normalNegroNegrita">Responsables</span></td>
						<td class="header"><span class="normalNegroNegrita">Elaborado por</span></td>
						<td class="header"><span class="normalNegroNegrita">Opciones</span></td>
					</tr>
					<?php 
						if(is_array($enBandeja) && count($enBandeja) > 0)
						{
							$tdClass = "even";
						
							foreach ($enBandeja as $idAvance => $dataAvance)
							{
								if(
									isset($dataAvance['ClassDocGenera'])
									&& ($docGenera=$dataAvance['ClassDocGenera']) instanceof EntidadDocGenera
									&& isset($dataAvance['ClassAvance']) && ($avance=$dataAvance['ClassAvance']) instanceof EntidadAvance
								){
									$tdClass = ($tdClass == "even") ? "odd" : "even";
									
									$fechaAvance = explode(" ", $avance->GetFechaAvance());
									
									// Establecer los datos del usuario que elaboró el documento de avance
									$empleadosElaborador = null;
									if(is_array($empleadosElaboradoresEnBandejas)){
										$empleadosElaborador = $empleadosElaboradoresEnBandejas[$docGenera->GetUsuaLogin()];
									}
									$empleadosElaboradorString = 
										($empleadosElaborador != null && $empleadosElaborador instanceof EntidadEmpleado)
										? mb_strtoupper($empleadosElaborador->GetNombres()." ".
											$empleadosElaborador->GetApellidos(), "ISO-8859-1") : "---";
											
									// 	Establecer los botones según las opciones de cada perfil de usuario
									if(
										substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
										$_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO ||
										$_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA
									){
										$botonesString = '
								<a href="avance.php?accion=VerDetalles&idAvance='.$avance->GetId().'">
									Ver Detalle
								</a>
								<br/>
								<a href="avance.php?accion=Modificar&idAvance='.$avance->GetId().'">
									Modificar
								</a>
								<br/>
								<a
									href="avance.php?accion=Enviar&idAvance='.$avance->GetId().'"
									onclick="return accionEnviar();"
								>
									Enviar
								</a>
								<br />
								<a
									href="avance.php?accion=Anular&idAvance='.$avance->GetId().'"
									onclick="return accionAnular();"
								>
									Anular
								</a>
										';
									}
									else if (
										substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_GERENTE ||
										substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_DIRECTOR ||
										$_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO ||
										$_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_EJECUTIVO ||
										$_SESSION['user_perfil_id'] == PERFIL_PRESIDENTE
									){
										$botonesString = '
								<a href="avance.php?accion=VerDetalles&idAvance='.$avance->GetId().'">
									Revisar
								</a>
										';
									}
									else if($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO)
									{
										$botonesString = '
								<a href="avance.php?accion=VerDetalles&idaVANCE='.$avance->GetId().'">
									Ver Detalle
								</a>
										';
									}
									else 
									{
										$botonesString = '';
									}
									
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
									
									// Generar el html con los datos de los avances
									echo '
						<tr class="'.$tdClass.'" onclick="Registroclikeado(this);">
						
							<td >
								<a
									href="avance.php?accion=VerDetalles&idAvance='.$avance->GetId().'"
								>
									'.$avance->GetId().'
								</a>
							</td>
							<td >'.$fechaAvance[0].'</td>
							<td >'.implode(", ", $nombresResponsables).'</td>
							<td >'.$empleadosElaboradorString.'</td>
							<td >'.$botonesString.'</td>
						</tr>
						
									';
								}
							}
						}
						else {
							echo '
								<tr>
									<td class="odd" colspan="6">
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
						<td class="header"><span class="normalNegroNegrita">Fecha avance</span></td>
						<td class="header"><span class="normalNegroNegrita">Responsables</span></td>
						<td class="header"><span class="normalNegroNegrita">Opciones</span></td>
					</tr>
					<?php
						if(is_array($porEnviar) && count($porEnviar)>0)
						{
							$tdClass = "even";
							
							foreach ($porEnviar as $idAvance => $dataAvance)
							{
								if(	isset($dataAvance['ClassDocGenera']) && 
									($docGenera=$dataAvance['ClassDocGenera']) instanceof EntidadDocGenera
									&& isset($dataAvance['ClassAvance'])
									&& ($avance=$dataAvance['ClassAvance']) instanceof EntidadAvance
								){
									$tdClass = ($tdClass == "even") ? "odd" : "even";
									
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
					<tr class="'.$tdClass.'" onclick="Registroclikeado(this);">
						<td >
							<a
								href="avance.php?accion=VerDetalles&idAvance='.$avance->GetId().'"
							>
								'.$avance->GetId().'
							</a>
						</td>
						<td>'.$fechaAvance[0].'</td>
						<td >'.implode(", ", $nombresResponsables).'</td>
						<td >
							<a href="avance.php?accion=VerDetalles&idAvance='.$avance->GetId().'">
								Ver Detalle
							</a>
							<br/>
							<a href="avance.php?accion=Modificar&idAvance='.$avance->GetId().'">Modificar</a>
							<br/>
							<a
								href="avance.php?accion=Enviar&idAvance='.$avance->GetId().'"
								onclick="return accionEnviar();"
							>Enviar</a>
							<br/>
							<a
								href="avance.php?accion=Anular&idAvance='.$avance->GetId().'"
								onclick="return accionAnular();"
							>
								Anular
							</a>
						</td>
					</tr>
									';
								}
							}
						} else {
							echo '
								<tr>
									<td class="odd" colspan="4">
										No existen documentos por enviar
									</td>
								</tr>
							';
						}
					?>
				</table></td>
			</tr>
			<?php
			} // Fin de if(
				// substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
				// $_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO ||
				// $_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA
				// ){
				
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
						<td class="header normalNegroNegrita">Fecha avance</td>
						<td class="header normalNegroNegrita">Responsables</td>
						<td class="header normalNegroNegrita">Instancia actual</td>
						<td class="header normalNegroNegrita">Elaborado por</td>
						<td class="header normalNegroNegrita">Opciones</td>
					</tr>
					<?php
						if(is_array($enTransito) && count($enTransito)>0)
						{
							$tdClass = "even";
							foreach ($enTransito as $idAvance => $dataAvance)
							{
								if(	isset($dataAvance['ClassDocGenera']) 
									&& ($docGenera=$dataAvance['ClassDocGenera']) instanceof EntidadDocGenera
									&& isset($dataAvance['ClassAvance'])
									&& ($avance=$dataAvance['ClassAvance']) instanceof EntidadAvance
								){
									$tdClass = ($tdClass == "even") ? "odd" : "even";
									
									$fechaAvance = explode(" ", $avance->GetFechaAvance());
									
									$cargo = null;
									if(is_array($cargoFundacionEnTransitos)){
										$cargo =  $cargoFundacionEnTransitos
											[GetCargoFundacionFromIdPerfil($docGenera->GetIdperfilActual())];
									}
									$cargoNombre = ($cargo != null && $cargo instanceof EntidadCargo) ? $cargo->GetNombre() : '';
									
									$dependencia = null;
									if(is_array($dependenciaEnTransitos)){
										$dependencia = $dependenciaEnTransitos
											[GetIdDependenciaFromIdPerfil($docGenera->GetIdperfilActual())];
									}
									$dependenciaNombre =  ($dependencia != null && $dependencia instanceof EntidadDependencia) ?
											'(<span style="font-style: italic">' . $dependencia->GetNombre() . '</span>)' : '';
									
									//$empleadosElaboradores
									$empleadosElaborador = null;
									if(is_array($empleadosElaboradoresEnTransitos)){
										$empleadosElaborador = $empleadosElaboradoresEnTransitos[$docGenera->GetUsuaLogin()];
									}
									$empleadosElaboradorString = 
										($empleadosElaborador != null && $empleadosElaborador instanceof EntidadEmpleado)
										? mb_strtoupper($empleadosElaborador->GetNombres()." "
											.$empleadosElaborador->GetApellidos(), "ISO-8859-1") : "---";
									
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
							<tr class="'.$tdClass.'" onclick="Registroclikeado(this);">
								<td >
									<a
										href="avance.php?accion=VerDetalles&idAvance='.$avance->GetId().'"
									>
										'.$avance->GetId().'
									</a>
								</td>
								<td >'.$fechaAvance[0].'</td>
								<td>'.implode(", ", $nombresResponsables).'</td>
								<td >'.$cargoNombre . " ". $dependenciaNombre.'</td>
								<td >'.$empleadosElaboradorString.'</td>
								<td >
									<a href="avance.php?accion=VerDetalles&idAvance='.$avance->GetId().'">
										Ver Detalle
									</a>
								</td>
							</tr>
									';
								}
							}
						}
						else
						{
							echo '
							<tr>
								<td class="odd" colspan="6">
									No existen documentos en tr&aacute;nsito
								</td>
							</tr>
							';
						}
					?>
				</table></td>
			</tr>
			<?php 
			} // Fin de if(
				// $idPerfil == PERFIL_ANALISTA_PRESUPUESTO
				// substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO
				// $_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO
				// $_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA
				// $idPerfil == PERFIL_JEFE_PRESUPUESTO
				// substr($idPerfil,0,2)."000" == PERFIL_GERENTE
				// substr($idPerfil,0,2)."000" == PERFIL_DIRECTOR
				// $idPerfil == PERFIL_DIRECTOR_EJECUTIVO
				// $idPerfil == PERFIL_PRESIDENTE
				// ){
			?>
		</table>
	</body>
</html>
