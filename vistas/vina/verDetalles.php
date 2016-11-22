<?php 
	$form = FormManager::GetForm('viaticoNacional');
	$docGenera = $form->GetDocGenera();
?>
<html>
	<head>
		<title>.:SAFI:. Detalles de Vi&aacute;tico Nacional</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<script type="text/javascript" src="../../js/constantes.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script>
			function accionAprobar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea aprobar este vi"+aACUTE+"tico nacional? ")) 
				{
					var objAccionesForm = $('#accionesForm');
					var objHiddenAccion = $('#hiddenAccion');
		
		
					objHiddenAccion.val('Enviar');
		
					objAccionesForm.submit();
				}
			}

			function accionEnviar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea enviar este vi"+aACUTE+"tico nacional? ")) 
				{
					var objAccionesForm = $('#accionesFormAA');
					objAccionesForm.submit();
				}
			}

			function accionAnular()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea anular el vi"+aACUTE+"tico nacional? ")) 
				{
					return true;
				}

				return false;
			}
			
			function accionDevolver()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea devolver este vi"+aACUTE+"tico nacional? ")) 
				{
					var objAccionesForm = $('#accionesForm');
					var objHiddenAccion = $('#hiddenAccion');
					var objMemoContent = $('#hiddenMemo');

					contenido=prompt("Indique el motivo de la devoluci"+oACUTE+"n: ","");
					
			   		while (contenido!=null && $.trim(contenido) == ""){
			    		contenido=prompt("Debe especificar el motivo de la devoluci"+oACUTE+"n: ","");
			   		}

					if(contenido == null){
						alert("El proceso de devoluci"+oACUTE+"n ha sido cancelado");
					} else {
				    	if (contenido!=null && $.trim(contenido) != ""){
				     		objMemoContent.val($.trim(contenido));
				     		objHiddenAccion.val('Devolver');
				     		objAccionesForm.submit();
				    	}
					}
				}
			}
		</script>
	</head>
	
	<body class="normal">
		<table align="center">
			<tr>
				<td><?php include(SAFI_VISTA_PATH . '/mensajes.php');?></td>
			</tr>
			<tr>
				<td><table
					align="center" class="tablaalertas content" background="../../imagenes/fondo_tabla.gif" width="800px"
					cellpadding="0" cellspacing="0"
					>
					<tr>
						<td colspan="2" align="center" style="text-align: center;" class="header normalNegroNegrita">.: Vi&aacute;tico nacional <?php
							echo $form->GetIdViatico()." "?> :.
							<?php
								if($docGenera->GetIdEstatus() == "15") {
									echo '<br/><span style="color: red;">Anulado</span>';
								}
							?>
						</td>
					</tr>
					<?php
						if($form->GetRequisiciones() != null && is_array($form->GetRequisiciones()) && count($form->GetRequisiciones())>0)
						{
							$strIdRequisiciones = array();
							foreach($form->GetRequisiciones() as $requisicion){
								$strIdRequisiciones[] = $requisicion->GetId();
							}
							echo '
					<tr>
						<td><span class="normalNegrita">Requisiciones: </span></td>
						<td>
							'.(implode(', ', $strIdRequisiciones)).'
						</td>
					</tr>
							';
						}
					?>
					<tr>
						<td><span class="normalNegrita">Fecha del vi&aacute;tico: </span></td>
						<td><?php echo $form->GetFechaViatico()?></td>
					</tr>
					<tr>
						<td><span class="normalNegrita">Estado: </span></td>
						<td>
							<?php 
								if($form->GetEstado() != null){
									echo $form->GetEstado()->GetNombre();
								}
							?>
						</td>
					</tr>
					<tr>
						<td colspan="2"><table style="width: 100%" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width: 20%"><span class="normalNegrita">Categor&iacute;a: </span></td>
								<td style="width: 30%">
									<?php 
										if($form->GetCategoriaViatico() != null){
											echo $form->GetCategoriaViatico()->GetNombre();
										}
									?>
								</td>
								<td style="width: 20%">
									<?php 
										if($form->GetRed() != null){										
											echo '<span class="normalNegrita">Red: </span>';
										}
									?>
								</td>
								<td style="width: 30%">
									<?php 
										if($form->GetRed() != null){										
											echo $form->GetRed()->GetNombre();
										}
									?>
								</td>
							</tr>
						</table></td>
					</tr>
					<tr>
						<td colspan="2" class="header normalNegroNegrita">Datos del responsable</td>
					</tr>
					<tr>
						<td colspan="2"><?php
							if($form->GetResponsable() != null && $form->GetResponsable() instanceof EntidadResponsableViatico){
								$responsable = $form->GetResponsable();
								echo '
									<table style="width: 100%;" cellpadding="0" cellspacing="0">
										<tr>
											<td><span class="normalNegrita">C&eacute;dula</span></td>
											<td><span class="normalNegrita">Nombre</span></td>
											<td><span class="normalNegrita">Tipo</span></td>
										</tr>
										<tr>
										<td>'.$responsable->GetCedula().'</td>
										<td>'.mb_strtoupper($responsable->GetNombres().' '. $responsable->GetApellidos(), 'ISO-8859-1')
										.'</td>
										<td>'.
											(strcmp($responsable->GetTipoResponsable(), 
												EntidadResponsableViatico::TIPO_EMPLEADO)== 0 ? 'Empleado' :
												(
													strcmp($responsable->GetTipoResponsable(),
														EntidadResponsableViatico::TIPO_BENEFICIARIO)== 0 ?
															$responsable->GetTipoEmpleado() : ''
												)
											)
										.'</td>
										</tr>
									</table>
								';
							}
						?></td>
					</tr>
					<?php 
						if(isset($responsable) && $responsable instanceof EntidadResponsableViatico){
							echo '
								<tr>
									<td><br/><span class="normalNegrita">Nro. cuenta: </span></td>
									<td><br/>'.$responsable->GetNumeroCuenta().'</td>
								</tr>
								<tr>
									<td><span class="normalNegrita">Tipo cuenta: </span></td>
									<td>'.
										(strcmp($responsable->GetTipoCuenta(), EntidadTipoCuentabancaria::CUENTA_CORRRIENTE)== 0 ? 
											'Corriente' :
											(strcmp($responsable->GetTipoCuenta(), EntidadTipoCuentabancaria::CUENTA_DE_AHORRO) == 0 ?
												 'Ahorro' : ''
											)
										)
									.'</td>
								</tr>
								<tr>
									<td><span class="normalNegrita">Banco: </span></td>
									<td>'.$responsable->GetBanco().'</td>
								</tr>
							';
						}
					?>
					<tr>
						<td colspan="2" class="header normalNegroNegrita">Proyecto/Acci&oacute;n centralizada</td>
					</tr>
					<tr>
						<td><?php
							if( strcmp($form->GetTipoProyectoAccionCentralizada(), 'proyecto') == 0 ){
								echo '<span class="normalNegrita">Proyecto: </span>';	
							} else {
								echo '<span class="normalNegrita">Acci&oacute;n centralizada: </span>';
							}
						?></td>
						<td><?php 
							if( $form->GetProyecto() != null && $form->GetProyecto() instanceof EntidadProyecto){
								echo $form->GetProyecto()->GetNombre();	
							} else {
								echo $form->GetAccionCentralizada()->GetNombre();
							}					
						?></td>
					</tr>
					<tr>
						<td><span class="normalNegrita">Acci&oacute;n espec&iacute;fica: </span></td>
						<td><?php
							if(
								$form->GetProyectoEspecifica() != null
								&& $form->GetProyectoEspecifica() instanceof EntidadProyectoEspecifica
							){
								$especifica = $form->GetProyectoEspecifica(); 
								echo '(' .$especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto().') '.$especifica->GetNombre();	
							} else {
								$especifica = $form->GetAccionCentralizadaEspecifica();
								echo '(' .$especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto().') '.$especifica->GetNombre();
							}
						?></td>
					</tr>
					<tr>
						<td colspan="2" class="header normalNegroNegrita">Datos del viaje</td>
					</tr>
					<tr>
						<td><span class="normalNegrita">Fecha inicio del viaje: </span></td>
						<td><?php echo $form->GetFechaInicioViaje()?></td>
					</tr>
					<tr>
						<td><span class="normalNegrita">Fecha fin del viaje: </span></td>
						<td><?php echo $form->GetFechaFinViaje()?></td>
					</tr>
					<tr>
						<td><span class="normalNegrita">Objetivos del viaje: </span></td>
						<td><?php echo $form->GetObjetivosViaje()?></td>
					</tr>
					<?php
						if(is_array($form->GetInfocentros()) && count($form->GetInfocentros())>0){
							echo '
					<tr>
						<td colspan="2"><span class="normalNegrita">Infocentros: </span></td>
					</tr>
					<tr>
						<td colspan="2">	
							';
							$strInfocentros = '';
							foreach($form->GetInfocentros() as $infocentro){
								if($infocentro instanceof EntidadInfocentro){
									$label = $infocentro->GetNombre();
									if(	($parroquia=$infocentro->GetParroquia()) != null &&
										($municipio=$parroquia->GetMunicipio()) != null &&
										($estado=$municipio->GetEstado()) != null
									){
										$label .= ' - ' . $estado->GetNombre();
									}
									if($infocentro->GetEtapa() != ''){
										$label .= ' - ' . $infocentro->GetEtapa();  
									}
									$strInfocentros .= '<li>' .$label . '</li>';
								}
							}
							if ($strInfocentros != ''){
								echo '<ul>' . $strInfocentros .'</ul>';
							}
							echo '
						</td>
					</tr>		
							';
					
						}
					?>
					<tr>
						<td colspan="2" class="header normalNegroNegrita">Rutas</td>
					</tr>
					<tr>
						<td colspan="2">
						<?php 
							if(is_array($form->GetRutas())){
								foreach($form->GetRutas() as $ruta){
									if($ruta instanceof EntidadRuta ){
						?>
							<table class="wrapperRutas">
								<tr>
									<td>
										<span class="normalNegrita">Fecha inicio: </span><?php echo $ruta->GetFechaInicio()?>
										<span class="normalNegrita espacioGeneralEntreCampos">Fecha fin: </span><?php
											echo $ruta->GetFechaFin()?>
									</td>
								</tr>
								<tr>
									<td>
										<span class="normalNegrita">Noches hospedaje: </span><?php echo $ruta->GetDiasHospedaje()?>
										<span class="normalNegrita espacioGeneralEntreCampos">D&iacute;as alimentaci&oacute;n: </span><?php
											echo $ruta->GetDiasAlimentacion()?>
										<span class="normalNegrita espacioGeneralEntreCampos">
											D&iacute;as transporte interurbano: </span><?php echo $ruta->GetUnidadTransporteInterurbano()?>
									</td>
								</tr>
								<tr>
									<td>
										<span class="normalNegrita">Tipo transporte: </span><?php echo $ruta->GetNombreTransporte()?>
										<span class="normalNegrita espacioGeneralEntreCampos">Pasaje ida y vuelta: </span><?php
											echo ($ruta->GetPasajeIdaVuelta() ? 'Si' : 'No') ?>
										<span class="normalNegrita espacioGeneralEntreCampos">
											Transporte residencia - aeropuerto: </span><?php
												echo ($ruta->GetResidenciaAeropuerto() ? 'Si' : 'No')?>
										<span class="normalNegrita espacioGeneralEntreCampos">
											Transporte aeropuerto - residencia: </span><?php
												echo ($ruta->GetAeropuertoResidencia() ? 'Si' : 'No')?>
										<span class="normalNegrita espacioGeneralEntreCampos">
											Tasa aeroportuaria ida: </span><?php
												echo ($ruta->GetTasaAeroportuariaIda() ? 'Si' : 'No')?>
										<span class="normalNegrita espacioGeneralEntreCampos">
											Tasa aeroportuaria vuelta: </span><?php
												echo ($ruta->GetTasaAeroportuariaVuelta() ? 'Si' : 'No')?>
									</td>
								</tr>
								<tr>
									<td>
										<br/>
										<table width="100%">
											<tr>
												<td>&nbsp;</td>
												<td><span class="normalNegrita">Estado</span></td>
												<td><span class="normalNegrita">Municipio</span></td>
												<td><span class="normalNegrita">Ciudad</span></td>
												<td><span class="normalNegrita">Parroquia</span></td>
												<td><span class="normalNegrita">Direcci&oacute;n</span></td>
											</tr>
											<tr>
												<td><span class="normalNegrita">Origen</span></td>
												<td><?php echo $ruta->GetNombreFromEstado()?></td>
												<td><?php echo $ruta->GetNombreFromMunicipio()?></td>
												<td><?php echo $ruta->GetNombreFromCiudad()?></td>
												<td><?php echo $ruta->GetNombreFromParroquia()?></td>
												<td><?php echo $ruta->GetFromDireccion()?></td>
											</tr>
											<tr>
												<td><span class="normalNegrita">Destino</span></td>
												<td><?php echo $ruta->GetNombreToEstado()?></td>
												<td><?php echo $ruta->GetNombreToMunicipio()?></td>
												<td><?php echo $ruta->GetNombreToCiudad()?></td>
												<td><?php echo $ruta->GetNombreToParroquia()?></td>
												<td><?php echo $ruta->GetToDireccion()?></td>
											</tr>
										</table>
										<br/>
									</td>
								</tr>
								<?php 
									if(trim($ruta->GetObservaciones())){
										echo '
								<tr>
									<td>
										<span class="normalNegrita">Observaciones: </span> '.$ruta->GetObservaciones() . '
										<br/><br/>
									</td>
								</tr>	
										';
										
									}
								?>
							</table>
						<?php
									} // Fin de if($ruta instanceof EntidadRuta ){
								} // Fin de foreach($form->GetRutas() as $ruta){
							}// Fin de f(is_array($form->GetRutas())){
						?>
						</td>
					</tr>
					<tr>
						<td colspan="2"><table cellpadding="0" cellspacing="0" class="tablaalertas content" style="width: 100%">
							<tr>
								<td class="header normalNegroNegrita">Asignaci&oacute;n</td>
								<td class="header normalNegroNegrita">Monto</td>
								<td class="header normalNegroNegrita">Unidad de medida</td>
								<td class="header normalNegroNegrita">Unidades</td>
								<td class="header normalNegroNegrita">Subtotal</td>
							</tr>
							<?php
								$totalMonto = 0;
								
								$asignaciones = $GLOBALS['SafiRequestVars']['asignaciones'];
							
								if(is_array($form->GetViaticoResponsableAsignaciones())){
									$VRAsignaciones = $form->GetViaticoResponsableAsignaciones();
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_HOSPEDAJE];
									$viaticoRespAsig = $VRAsignaciones[EntidadAsignacionViatico::COD_HOSPEDAJE];
									if($viaticoRespAsig instanceof EntidadViaticoResponsableAsignacion){
										$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
										echo '
							<tr>
								<td class="normalNegrita">' . $asignacion->GetNombre() . '</td>
								<td>' . $viaticoRespAsig->GetMonto() . '</td>
								<td>Por noche</td>
								<td>'. $viaticoRespAsig->GetUnidades() .'</td>
								<td>'.($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades()).'</td>
							</tr>
										';
									}
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_ALIMENTACION];
									$viaticoRespAsig = $VRAsignaciones[EntidadAsignacionViatico::COD_ALIMENTACION];
									if($viaticoRespAsig instanceof EntidadViaticoResponsableAsignacion){
										$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
										echo '
							<tr>
								<td class="normalNegrita">' . $asignacion->GetNombre() . '</td>
								<td>' . $viaticoRespAsig->GetMonto() . '</td>
								<td>Diario</td>
								<td>'. $viaticoRespAsig->GetUnidades() .'</td>
								<td>'.($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades()).'</td>
							</tr>
										';
									}
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_TRANSPORTE_INTERURBANO];
									$viaticoRespAsig = $VRAsignaciones[EntidadAsignacionViatico::COD_TRANSPORTE_INTERURBANO];
									if($viaticoRespAsig instanceof EntidadViaticoResponsableAsignacion){
										$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
										echo '
							<tr>
								<td class="normalNegrita">' . $asignacion->GetNombre() . '</td>
								<td>' . $viaticoRespAsig->GetMonto() . '</td>
								<td>Diario</td>
								<td>'. $viaticoRespAsig->GetUnidades() .'</td>
								<td>'.($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades()).'</td>
							</tr>
										';
									}
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_RESIDENCIA_AEROPUERTO];
									$viaticoRespAsig = $VRAsignaciones[EntidadAsignacionViatico::COD_RESIDENCIA_AEROPUERTO];
									if($viaticoRespAsig instanceof EntidadViaticoResponsableAsignacion){
										$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
										echo '
							<tr>
								<td class="normalNegrita">' . $asignacion->GetNombre() . '</td>
								<td>' . $viaticoRespAsig->GetMonto() . '</td>
								<td>Por traslado</td>
								<td>'. $viaticoRespAsig->GetUnidades() .'</td>
								<td>'.($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades()).'</td>
							</tr>
										';
									}
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_AEROPUERTO_RESIDENCIA];
									$viaticoRespAsig = $VRAsignaciones[EntidadAsignacionViatico::COD_AEROPUERTO_RESIDENCIA];
									if($viaticoRespAsig instanceof EntidadViaticoResponsableAsignacion){
										$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
										echo '
							<tr>
								<td class="normalNegrita">' . $asignacion->GetNombre() . '</td>
								<td>' . $viaticoRespAsig->GetMonto() . '</td>
								<td>Por traslado</td>
								<td>'. $viaticoRespAsig->GetUnidades() .'</td>
								<td>'.($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades()).'</td>
							</tr>
										';
									}
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_TASA_AEROPORTUARIA];
									$viaticoRespAsig = $VRAsignaciones[EntidadAsignacionViatico::COD_TASA_AEROPORTUARIA];
									if($viaticoRespAsig instanceof EntidadViaticoResponsableAsignacion){
										$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
										echo '
							<tr>
								<td class="normalNegrita">' . $asignacion->GetNombre() . '</td>
								<td>' . $viaticoRespAsig->GetMonto() . '</td>
								<td>Por viaje</td>
								<td>'. $viaticoRespAsig->GetUnidades() .'</td>
								<td>'.($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades()).'</td>
							</tr>
										';
									}
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_SERVICIO_COMUNICACIONES];
									$viaticoRespAsig = $VRAsignaciones[EntidadAsignacionViatico::COD_SERVICIO_COMUNICACIONES];
									if($viaticoRespAsig instanceof EntidadViaticoResponsableAsignacion){
										$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
										echo '
							<tr>
								<td class="normalNegrita">' . $asignacion->GetNombre() . '</td>
								<td>' . $viaticoRespAsig->GetMonto() . '</td>
								<td>Por viaje</td>
								<td>'. $viaticoRespAsig->GetUnidades() .'</td>
								<td>'.($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades()).'</td>
							</tr>
										';
									}
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_ASIGNACION_TRANSPORTE];
									$viaticoRespAsig = $VRAsignaciones[EntidadAsignacionViatico::COD_ASIGNACION_TRANSPORTE];
									if($viaticoRespAsig instanceof EntidadViaticoResponsableAsignacion){
										$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
										echo '
							<tr>
								<td class="normalNegrita">' . $asignacion->GetNombre() . '</td>
								<td>' . $viaticoRespAsig->GetMonto() . '</td>
								<td>Por Km</td>
								<td>'. $viaticoRespAsig->GetUnidades() .'</td>
								<td>'.($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades()).'</td>
							</tr>
										';
									}
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_TRANSPORTE_EXTRAURBANO];
									$viaticoRespAsig = $VRAsignaciones[EntidadAsignacionViatico::COD_TRANSPORTE_EXTRAURBANO];
									if($viaticoRespAsig instanceof EntidadViaticoResponsableAsignacion){
										$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
										echo '
							<tr>
								<td class="normalNegrita">' . $asignacion->GetNombre() . '</td>
								<td>' . $viaticoRespAsig->GetMonto() . '</td>
								<td>Diario</td>
								<td>'. $viaticoRespAsig->GetUnidades() .'</td>
								<td>'.($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades()).'</td>
							</tr>
										';
									}
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_TRANSPORTE_ENTRE_CIUDADES];
									$viaticoRespAsig = $VRAsignaciones[EntidadAsignacionViatico::COD_TRANSPORTE_ENTRE_CIUDADES];
									if($viaticoRespAsig instanceof EntidadViaticoResponsableAsignacion){
										$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
										echo '
							<tr>
								<td class="normalNegrita">' . $asignacion->GetNombre() . '</td>
								<td>' . $viaticoRespAsig->GetMonto() . '</td>
								<td>Por viaje</td>
								<td>'. $viaticoRespAsig->GetUnidades() .'</td>
								<td>'.($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades()).'</td>
							</tr>
										';
									}
								}
							?>
							<tr>
								<td colspan="3"></td>
								<td class="normalNegrita">Total</td>
								<td><?php echo $totalMonto ?></td>
							</tr>
						</table></td>
					</tr>
					<?php
						if($form->GetObservaciones() != null && $form->GetObservaciones() != ''){
							echo '
					<tr>
						<td colspan="2" class="header normalNegroNegrita">Observaciones</td>
					</tr>
					<tr>
						<td colspan="2">'.$form->GetObservaciones().'</td>
					</tr>		
							';
						}
						
						if(
							(
								substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_GERENTE ||
								substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_DIRECTOR ||
								$_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO ||
								$_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_EJECUTIVO ||
								$_SESSION['user_perfil_id'] == PERFIL_PRESIDENTE
							)
							&&
							(strcmp($form->GetDocGenera()->GetIdPerfilActual(), $_SESSION['user_perfil_id']) == 0)
						){
					?>
					<tr>
						<td colspan="2">
							<form name="accionesForm" id="accionesForm" method="post" action="viaticonacional.php">
								<input type="hidden" name="idViaticoNacional" value="<?php echo $form->GetIdViatico()?>">
								<input id="hiddenAccion" type="hidden" name="accion" value="Enviar">
								<input id ="hiddenMemo" type="hidden" name="memoContent">
								<table class="tablaalertas" align="center" width="100%">
									<tr>
										<td style="text-align: center;">
											<input class="normalNegro" type="button" value="Aprobar" onclick="accionAprobar();">
											<input class="normalNegro" type="button" value="Devolver" onclick="accionDevolver();">
											<a href="viaticonacional.php?accion=Bandeja" style="text-decoration: none;">
												<input class="normalNegro" type="button" value="Cancelar">
											</a>
										</td>
									</tr>
								</table>
							</form>
						</td>
					</tr>
					<?php
						} else if(
							$_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS  &&
							strcmp($form->GetDocGenera()->GetIdPerfilActual(), $_SESSION['user_perfil_id']) != 0 &&
							$form->GetDocGenera()->GetIdEstatus() == 13
						){
					?>
					<tr>
						<td colspan="2">
							<form name="accionesForm" id="accionesForm" method="post" action="viaticonacional.php">
								<input type="hidden" name="idViaticoNacional" value="<?php echo $form->GetIdViatico()?>">
								<input id="hiddenAccion" type="hidden" name="accion" value="Devolver">
								<input id ="hiddenMemo" type="hidden" name="memoContent">
								<table class="tablaalertas" align="center" width="100%">
									<tr>
										<td style="text-align: center;">
											<input class="normalNegro" type="button" value="Devolver" onclick="accionDevolver();">
											<a href="viaticonacional.php?accion=Bandeja" style="text-decoration: none;">
												<input class="normalNegro" type="button" value="Cancelar">
											</a>
										</td>
									</tr>
								</table>
							</form>
						</td>
					</tr>
					<?php
						} else if(
							substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
							$_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO ||
							$_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA
						){
							// Estatus == 59 => "Por enviar" y Estatus == 7 => "Devuelto"
							if($form->GetDocGenera()->GetIdEstatus() == 59 || $form->GetDocGenera()->GetIdEstatus() == 7){
					?>
					<tr>
						<td colspan="2">
							<form name="accionesFormAA" id="accionesFormAA" method="post" action="viaticonacional.php">
								<input type="hidden" name="idViaticoNacional" value="<?php echo $form->GetIdViatico()?>">
								<input id="hiddenAccion" type="hidden" name="accion" value="Enviar">
								<table class="tablaalertas" align="center" width="100%">
									<tr>
										<td style="text-align: center;">
											<a
												href="viaticonacional.php?
													accion=Modificar&idViaticoNacional=<?php echo $form->GetIdViatico()?>"
												style="text-decoration: none;"
											>
												<input class="normalNegro" type="button" value="Modificar">
											</a>
											<input class="normalNegro" type="button" value="Enviar" onclick="accionEnviar();">
											<a
												href="viaticonacional.php?
													accion=Anular&idViaticoNacional=<?php echo $form->GetIdViatico()?>"
												style="text-decoration: none;"
												onclick="return accionAnular();"
											>
												<input class="normalNegro" type="button" value="Anular">
											</a>
										</td>
									</tr>
								</table>
							</form>
						</td>
					</tr>
					<?php
							}
						}
					?>
				</table></td>
			</tr>
		</table>	
		<?php
			// Para los documentos de soporte (Memos)
			include (SAFI_VISTA_PATH . '/respaldos.php');
			
			// Revisiones del documento
			include (SAFI_VISTA_PATH . '/revisiones.php');
			
			/*
			// Link hacía la generación del archivo de pago (txt)
			
			if(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO)
			{
		//?>
		<div align="center" class="normal" style="height: 50px;margin-top: 20px;">
			<div style="margin-top: 12px; float: left;text-align: right;width: 50%;">Para generar su archivo de pago haga clic</div>
			<div style="margin-top: 8px; float: right;width: 50%;">
				<table align="left">
					<tr>
						<td>
							<a
								href="viaticonacional.php?accion=archivosPagos&idViatico=<?php echo $form->GetIdViatico()?>"
								class="normal"
							>aqu&iacute;</a>
						</td>
					</tr>
				</table>
			</div>
		</div>
		//<?php
			}
		*/
			if(
				substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
				$_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO ||
				$_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA
			){
		?>
		<div align="center" class="normal" style="height: 50px;margin-top: 20px;">
			<div style="margin-top: 12px;float: left;text-align: right;width: 50%;">Para generar su documento en formato PDF presione</div>
			<div style="float: right;width: 50%;">
				<table align="left">
					<tr>
						<td align="center" width="150px">
							<a
								href="viaticonacional.php?tipo=L&accion=GenerarPDF&idVina=<?php echo $form->GetIdViatico()?>"
								class="normal"
							>aqu&iacute;</a>
							<a
								href="viaticonacional.php?tipo=L&accion=GenerarPDF&idVina=<?php echo $form->GetIdViatico()?>"
							><img src="../../imagenes/pdf_ico.jpg" border="0"/></a>
						</td>
						<!--
						<td align="center" width="150px">
							<a
								href="viaticonacional.php?tipo=F&accion=GenerarPDF&idVina=<?php echo $form->GetIdViatico()?>"
								class="normal"
							>aqu&iacute;</a>
							<a
								href="viaticonacional.php?tipo=F&accion=GenerarPDF&idVina=<?php echo $form->GetIdViatico()?>"
							><img src="../../imagenes/pdf_ico.jpg" border="0"/></a>
						</td>
						 -->
					</tr>
					<!-- 
					<tr>
						<td align="center"><b class="normalNegrita">Formato 1 (lineal)</b></td>
						<td align="center" ><b class="normalNegrita">Formato 2 (firmas por hoja)</b></td>
					</tr>
					 -->					
				</table>
			</div>
		</div>
		<?php 
			}
		?>
	</body>
</html>