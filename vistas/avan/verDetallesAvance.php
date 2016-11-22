<?php
	$form = FormManager::GetForm(FORM_NUEVO_AVANCE);
	$avance = null;
	$docGenera = null;
	
	if($form != null)
	{
		$avance = $form->GetAvance();
		$docGenera = $form->GetDocGenera();
	}
?>
<html>
	<head>
		<title>.:SAFI:. Detalles del Avance</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<script type="text/javascript" src="../../js/constantes.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript">
			function accionAprobar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea aprobar este avance? ")) 
				{
					var objAccionesForm = $('#accionesForm');
					var objHiddenAccion = $('#hiddenAccion');
		
					objHiddenAccion.val('Enviar');
		
					objAccionesForm.submit();
				}
			}
	
			function accionEnviar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea enviar este avance? ")) 
				{
					var objAccionesForm = $('#accionesFormAA');
					objAccionesForm.submit();
				}
			}

			function accionAnular()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea anular este avance? ")) 
				{
					return true;
				}

				return false;
			}
			
			function accionDevolver()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea devolver este avance? ")) 
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
	<body>
		<table align="center">
			<tr>
				<td><?php include(SAFI_VISTA_PATH . '/mensajes.php');?></td>
			</tr>
			<tr>
				<td><table  cellpadding="0" cellspacing="0"  align="center" background="../../imagenes/fondo_tabla.gif"
					class="tablaalertas content" width="800px;"
				>
					<tr class="td_gray">
						<td colspan="2" style="text-align: center;" class="normalNegroNegrita header documentTitle">
							.: Avance <?php echo $avance->GetId() ?> :.
							<?php
							if($docGenera->GetIdEstatus() == "15") {
								echo '<br/><span style="color: red;">Anulado</span>';
							}
						?>
						</td>
					</tr>
					<!-- 
					<tr>
						<td class="normalNegrita">Fecha de registro</td>
						<td><?php
							$fecha = explode(" ", $avance->GetFechaRegistro());
							echo $fecha[0];
						?></td>
					</tr>
					 -->
					<tr>
						<td class="normalNegrita">Fecha del avance: </td>
						<td><?php echo $avance->GetFechaAvance(); ?></td>
					</tr>
					<?php
						if (
							$avance != null && $avance && $avance->GetPuntoCuenta() != null
							&& ($idPuntoCuenta=$avance->GetPuntoCuenta()->GetId()) != null && ($idPuntoCuenta=trim($idPuntoCuenta)) != ''
						) {
							echo '
					<tr>
						<td class="normalNegrita">Punto de cuenta: </td>
						<td>'.$idPuntoCuenta.'</td>
					</tr>		
							';
						}
					?>
					<tr>
						<td colspan="2"><table cellpadding="0" cellspacing="0" style="width: 100%;">
							<tr>
								<td class="normalNegrita" style="width: 25%">Categor&iacute;a: </td>
								<td style="width: 35%"><?php 
									if($avance->GetCategoria() != null){
										echo $avance->GetCategoria()->GetNombre();
									}
								?></td>
								<td class="normalNegrita" style="width: 5%";><?php 
									if($avance->GetRed() != null){										
										echo 'Red: ';
									}
								?></td>
								<td style="width: 35%";><?php 
									if($avance->GetRed() != null){										
										echo $avance->GetRed()->GetNombre();
									}
								?></td>
							</tr>
						</table></td>
					</tr>
					<tr>
						<td colspan="2" class="normalNegroNegrita header">Proyecto/Acci&oacute;n centralizada</td>
					</tr>
					<tr>
						<td class="normalNegrita"><?php
							if($avance->GetProyecto() != null){
								echo "Proyecto";
							} else if($avance->GetAccionCentralizada() != null){
								echo "Acci&oacute;n centralizada";
							}
						?>: </td>
						<td><?php
							if($avance->GetProyecto() != null){
								echo $avance->GetProyecto()->GetNombre();
							} else if($avance->GetAccionCentralizada() != null){
								echo $avance->GetAccionCentralizada()->GetNombre();
							}
						?></td>
					</tr>
					<tr>
						<td class="normalNegrita"><span class="normalNegrita">Acci&oacute;n espec&iacute;fica: </span></td>
						<td><?php
							if($avance->GetProyecto() != null && $avance->GetProyectoEspecifica() != null){
								$especifica = $avance->GetProyectoEspecifica(); 
								echo '(' .$especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto().') '.$especifica->GetNombre();
							} else if($avance->GetAccionCentralizada() != null && $avance->GetAccionCentralizadaEspecifica() != null){
								$especifica = $avance->GetAccionCentralizadaEspecifica();
								echo '(' .$especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto().') '.$especifica->GetNombre();
							}
						?></td>
					</tr>
					<tr>
						<td colspan="2" class="normalNegroNegrita header">Datos de la actividad</td>
					</tr>
					<tr>
						<td class="normalNegrita">Fecha inicio: </td>
						<td><?php echo $avance->GetFechaInicioActividad() ?></td>
					</tr>
					<tr>
						<td class="normalNegrita">Fecha Fin: </td>
						<td><?php echo $avance->GetFechaFinActividad() ?></td>
					</tr>
					<tr>
						<td style="vertical-align: top;" class="normalNegrita">Objetivos: </td>
						<td style="text-align: justify;"><?php
							$objetivos = ($avance != null) ? $avance->GetObjetivos() : "";
							echo ToHtmlEncode($objetivos);
						?></td>
					</tr>
					<?php
						if (
							$avance != null && ($decripcion=$avance->GetDescripcion()) != null
							&& ($decripcion=trim($decripcion)) != ""
						) {
							echo '
					<tr>
						<td style="vertical-align: top;" class="normalNegrita">Descripci&oacute;n: </td>
						<td style="text-align: justify;">'.ToHtmlEncode($decripcion).'</td>
					</tr>		
							';
							
						}
						
						if (
							$avance != null && ($justificacion=$avance->GetJustificacion()) != null
							&& ($justificacion=trim($justificacion)) != ""
						) {
							echo '
					<tr>
						<td style="vertical-align: top;" class="normalNegrita">Justificaci&oacute;n: </td>
						<td style="text-align: justify;">'.ToHtmlEncode($justificacion).'</td>
					</tr>	
							';
						}
					?>
					<tr>
						<td class="normalNegrita">Nro. participantes: </td>
						<td><?php echo $avance->GetNroParticipantes() ?></td>
					</tr>
					<?php
						if(is_array($avance->GetInfocentros()) && count($avance->GetInfocentros())>0){
							echo '
					<tr>
						<td colspan="2" class="normalNegrita">Infocentros: </td>
					</tr>
					<tr>
						<td colspan="2">	
							';
							$strInfocentros = '';
							
							foreach($avance->GetInfocentros() as $infocentro){
								if($infocentro instanceof EntidadInfocentro){
									$label = $infocentro->GetNombre() . ' - ' . $infocentro->GetNemotecnico();
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
					<!-- 
					<tr>
						<td class="normalNegrita">Anexos: </td>
						<td><?php ?></td>
					</tr>
					 -->
					<tr>
						<td colspan="2" class="normalNegroNegrita header">Responsables</td>
					</tr>
					<tr>
						<td colspan="2">
						<?php
						if(is_array($avance->GetResponsablesAvancePartidas()) && count($avance->GetResponsablesAvancePartidas())>0)
						{
							$montoTotal = 0;
							
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
									$nombre = $empleado->GetNombres() . ' ' .$empleado->GetApellidos(); 
								}
								else if (
									$responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_BENEFICIARIO
									&& $responsableAvance->GetBeneficiario() != null
								){
									$beneficiario = $responsableAvance->GetBeneficiario();
									
									$id = $beneficiario->GetId();
									$nombre = $beneficiario->GetNombres() . ' ' .$beneficiario->GetApellidos();
								}
								
								// Obtener los datos del tipo de cuenta bancarios
								if(strcmp($responsableAvance->GetTipoCuenta(), EntidadTipoCuentabancaria::CUENTA_DE_AHORRO) == 0)
								{
									$tipoCuenta = "Ahorro";
								}
								elseif (strcmp($responsableAvance->GetTipoCuenta(), EntidadTipoCuentabancaria::CUENTA_CORRRIENTE) == 0)
								{
									$tipoCuenta = "Corriente";
								}
								
								echo '
							<table class="wrapperResponsablesAvance">
								<tr>
									<td><table class="tableSub">
										<tr>
											<td class="normalNegrita">Nombre: </td>
											<td>'.$nombre.'</td>
											<td class="normalNegrita">C&eacute;dula: </td>
											<td>'.$id.'</td>
											<td class="normalNegrita">Estado: </td>
											<td>'.(
												($responsableAvance->GetEstado() != null)
												? $responsableAvance->GetEstado()->GetNombre() : ""
											).'</td>
										</tr>
										<tr>
											<td class="normalNegrita">Nro. cuenta: </td>
											<td>'.$responsableAvance->GetNumeroCuenta().'</td>
											<td class="normalNegrita">Tipo cuenta: </td>
											<td>'.$tipoCuenta.'</td>
											<td class="normalNegrita">Banco: </td>
											<td>'.$responsableAvance->GetBanco().'</td>
										</tr>
									</table></td>
								</tr>
								';
								
								if(
									is_array($responsableAvancePartidas->GetAvancePartidas())
									&& count($responsableAvancePartidas->GetAvancePartidas()) > 0
								){
									echo '
								<tr>
									<td><table class="tableSub" cellspacing="0" cellpadding="0">
										<tr>
											<td class="normalNegrita" style="width: 25%;">Partida</td>
											<td class="normalNegrita" style="width: 25%;">Monto</td>
											<td class="normalNegrita" style="width: 25%;">Partida</td>
											<td class="normalNegrita" style="width: 25%;">Monto</td>
										</tr>
									';
									
									$countAvancePartida = 0;
									$esPar = true;
									$montoSubTotal = 0;
									
									foreach ($responsableAvancePartidas->GetAvancePartidas() as $avanPartida)
									{
										$montoSubTotal += $avanPartida->GetMonto();
										$montoTotal += $avanPartida->GetMonto();
										$idpartida = ($avanPartida->GetPartida() ? $avanPartida->GetPartida()->GetId() : "");
										
										if($countAvancePartida % 2 == 0) $esPar = true;
										else $esPar = false;
										
										if($esPar){echo '<tr>';}
										echo '
												<td>'.$idpartida.'</td>
												<td>'.number_format($avanPartida->GetMonto(),2,',','.').'</td>
										';
										if(!$esPar){echo '</tr>';}
										
										$countAvancePartida++;
									}
									if($esPar){
										echo '
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>';
									}
									
									echo '
									</table></td>
								</tr>
								<tr>
									<td style="text-align: right; padding-right: 25px;">
										<span class="normalNegrita">Subtotal</span>:&nbsp;&nbsp;'.number_format($montoSubTotal,2,',','.').'
									</td>
								</tr>
									';
									
								} // Fin de if(
								  // 			is_array($responsableAvancePartidas->GetAvancePartidas())
								  // 			&& count($responsableAvancePartidas->GetAvancePartidas())>0
								  //		){
								
								echo '
							</table>
								';
							} // foreach ($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidas)
							echo '
							<table class="wrapperResponsablesAvance">
								<tr>
									<td class="normalNegrita" style="text-align: right; padding-right: 25px;">
										<span class="normalNegrita">Total</span>:&nbsp;&nbsp;'.number_format($montoTotal,2,',','.').'
									</td>
								</tr>
							</table>
							';
							
						}  // Fin de
						   // if(is_array($avance->GetResponsablesAvancePartidas()) && count($avance->GetResponsablesAvancePartidas())>0) 
						?>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="normalNegroNegrita header">Rutas</td>
					</tr>
					<tr>
						<td colspan="2">
						<?php 
						if(is_array($avance->GetRutasAvance()))
						{
							echo '
							<table class="wrapperRutas">
								<tr>
									<td class="normalNegrita">Estado</td>
									<td class="normalNegrita">Ciudad</td>
									<td class="normalNegrita">Municipio</td>
									<td class="normalNegrita">Parroquia</td>
									<td class="normalNegrita">Direcci&oacute;n</td>
								</tr>
							';
							foreach($avance->GetRutasAvance() as $rutaAvance)
							{
								echo '
								<tr>
									<td>'.($rutaAvance->GetEstado() != null ? $rutaAvance->GetEstado()->GetNombre() : "").'</td>
									<td>'.($rutaAvance->GetCiudad() != null ? $rutaAvance->GetCiudad()->GetNombre() : "").'</td>
									<td>'.($rutaAvance->GetMunicipio() != null ? $rutaAvance->GetMunicipio()->GetNombre() : "").'</td>
									<td>'.($rutaAvance->GetParroquia() != null ? $rutaAvance->GetParroquia()->GetNombre() : "").'</td>
									<td>'.($rutaAvance->GetDireccion() != null ? $rutaAvance->GetDireccion() : "").'</td>
								</tr>
								';
							}
							echo '
							</table>
							';
						}
						?>
						</td>
					</tr>
					<?php
						if($avance->GetObservaciones() != null && $avance->GetObservaciones() != ''){
							echo '
					<tr>
						<td colspan="2" class="normalNegroNegrita header">Observaciones</td>
					</tr>
					<tr>
						<td colspan="2">'.ToHtmlEncode($avance->GetObservaciones()).'</td>
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
							(strcmp($docGenera->GetIdPerfilActual(), $_SESSION['user_perfil_id']) == 0)
						){
					?>
					<tr>
						<td colspan="2">
							<form name="accionesForm" id="accionesForm" method="post" action="avance.php">
								<input type="hidden" name="idAvance" value="<?php echo $avance->GetId() ?>">
								<input id="hiddenAccion" type="hidden" name="accion" value="Enviar">
								<input id ="hiddenMemo" type="hidden" name="memoContent">
								<table class="tablaalertas" align="center" width="100%">
									<tr>
										<td style="text-align: center;">
											<input class="normalNegro" type="button" value="Aprobar" onclick="accionAprobar();">
											<input class="normalNegro" type="button" value="Devolver" onclick="accionDevolver();">
											<a href="avance.php?accion=Bandeja" style="text-decoration: none;">
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
							strcmp($docGenera->GetIdPerfilActual(), $_SESSION['user_perfil_id']) != 0 &&
							$docGenera->GetIdEstatus() == 13
						){
					?>
					<tr>
						<td colspan="2">
							<form name="accionesForm" id="accionesForm" method="post" action="avance.php">
								<input type="hidden" name="idAvance" value="<?php echo $avance->GetId() ?>">
								<input id="hiddenAccion" type="hidden" name="accion" value="Devolver">
								<input id ="hiddenMemo" type="hidden" name="memoContent">
								<table class="tablaalertas" align="center" width="100%">
									<tr>
										<td style="text-align: center;">
											<input class="normalNegro" type="button" value="Devolver" onclick="accionDevolver();">
											<a href="avance.php?accion=Bandeja" style="text-decoration: none;">
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
							if($docGenera->GetIdEstatus() == 59 || $docGenera->GetIdEstatus() == 7){
					?>
					<tr>
						<td colspan="2">
							<form name="accionesFormAA" id="accionesFormAA" method="post" action="avance.php">
								<input type="hidden" name="idAvance" value="<?php echo $avance->GetId() ?>">
								<input id="hiddenAccion" type="hidden" name="accion" value="Enviar">
								<table class="tablaalertas" align="center" width="100%">
									<tr>
										<td style="text-align: center;">
											<a
												href="avance.php?
													accion=Modificar&idAvance=<?php echo $avance->GetId()?>"
												style="text-decoration: none;"
											>
												<input class="normalNegro" type="button" value="Modificar">
											</a>
											<input class="normalNegro" type="button" value="Enviar" onclick="accionEnviar();">
											<a
												href="avance.php?
													accion=Anular&idAvance=<?php echo $avance->GetId()?>"
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
			
			// Link hacía la generación del archivo de pago (txt)
		/*	
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
								href="avance.php?accion=archivosPagos&idAvance=<?php echo $avance->GetId()?>"
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
								href="avance.php?tipo=L&accion=GenerarPDF&idAvance=<?php echo $avance->GetId()?>"
								class="normal"
							>aqu&iacute;</a>
							<a
								href="avance.php?tipo=L&accion=GenerarPDF&idAvance=<?php echo $avance->GetId()?>"
							><img src="../../imagenes/pdf_ico.jpg" border="0"/></a>
						</td>
						<!-- 
						<td align="center" width="150px">
							<a
								href="avance.php?tipo=F&accion=GenerarPDF&idAvance=<?php echo $avance->GetId()?>"
								class="normal"
							>aqu&iacute;</a>
							<a
								href="avance.php?tipo=F&accion=GenerarPDF&idAvance=<?php echo $avance->GetId()?>"
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