<?php 
	$form = FormManager::GetForm(FORM_NUEVA_RENDICION_VIATICO_NACIONAL);
	$rendicion = $form->GetRendicionViaticoNacional();
	$viatico = $form->GetViatico();
	//$docGenera = $form->GetDocGenera();
	
	if($viatico != null){
		$responsable = $viatico->GetResponsable();
	}
	
	$asignaciones = $GLOBALS['SafiRequestVars']['asignaciones'];
?>
<html>
	<head>
		<title>.:SAFI:. Detalles de Rendici&oacute;n de Vi&aacute;tico Nacional</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<script type="text/javascript" src="../../js/constantes.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript">
			function accionFinalizar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea finalizar esta rendici"+oACUTE+"n de vi"+aACUTE+"tico nacional? ")) 
				{
					var objAccionesForm = $('#accionesForm');
					var objHiddenAccion = $('#hiddenAccion');
		
		
					objHiddenAccion.val('Enviar');
		
					objAccionesForm.submit();
				}
			}
			function accionAprobar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea aprobar esta rendici"+oACUTE+"n de vi"+aACUTE+"tico nacional? ")) 
				{
					var objAccionesForm = $('#accionesForm');
					var objHiddenAccion = $('#hiddenAccion');
		
		
					objHiddenAccion.val('Enviar');
		
					objAccionesForm.submit();
				}
			}

			function accionEnviar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea enviar esta rendici"+oACUTE+"n de vi"+aACUTE+"tico nacional? ")) 
				{
					var objAccionesForm = $('#accionesFormAA');
					objAccionesForm.submit();
				}
			}

			function accionAnular()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea anular la rendici"+oACUTE+"n de avance? ")) 
				{
					return true;
				}
	
				return false;
			}
			
			function accionDevolver()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea devolver esta rendici"+oACUTE+"n de vi"+aACUTE+"tico nacional? ")) 
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
				<td><table align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas content" width="800px;"
					cellpadding="0" cellspacing="0"
					>
					<tr>
						<td colspan="2" style="text-align: center;" class="header normalNegroNegrita">
							.: Rendici&oacute;n de vi&aacute;tico nacional <?php
							echo $rendicion->GetId()." "
						?>:.
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
						<td class="normalNegro"><?php //echo $rendicion->GetFechaRegistro() ?></td>
					</tr>
					 -->
					<tr>
						<td class="normalNegrita">Fecha de la rendici&oacute;n:</td>
						<td class="normalNegro"><?php echo $rendicion->GetFechaRendicion() ?></td>
					</tr>
					<tr>
						<td class="normalNegrita">Informe de la rendici&oacute;n:</td>
						<td class="normalNegro">
							<a href="<?php
								$path = "../..".str_replace(SAFI_BASE_PATH, "", SAFI_UPLOAD_RENDICION_VIATICO_NACIONAL_PATH);
								echo $path."/".$rendicion->GetInformeFileName()
							?>">
								<?php echo $rendicion->GetInformeFileName()?>
							</a>
					</tr>
					<tr>
						<td class="normalNegrita">C&oacute;digo del vi&aacute;tico:</td>
						<td class="normalNegro"><?php echo $viatico->GetId() ?></td>
					</tr>
					<tr>
						<td class="normalNegrita">Fecha del vi&aacute;tico:</td>
						<td class="normalNegro"><?php echo $viatico->GetFechaViatico() ?></td>
					</tr>
					<tr>
						<td colspan="2" class="header normalNegroNegrita">Datos del responsable</td>
					</tr>
					<tr>
						<td colspan="2"><?php
							if($responsable != null){
								echo '
							<table style="width: 100%;" cellpadding="0" cellspacing="0">
								<tr>
									<td><span class="normalNegrita">C&eacute;dula</span></td>
									<td><span class="normalNegrita">Nombre</span></td>
									<td><span class="normalNegrita">Tipo</span></td>
								</tr>
								<tr>
								<td>'.$responsable->GetCedula().'</td>
								<td>'.mb_strtoupper($responsable->GetNombres().' '. $responsable->GetApellidos(), 'ISO-8859-1').'</td>
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
					<tr>
						<td colspan="2" class="header normalNegroNegrita">Proyecto/Acci&oacute;n centralizada</td>
					</tr>
					<tr>
						<td><span class="normalNegrita"><?php 
							if($viatico->GetProyecto() != null){
								echo "Proyecto:";
							} else if($viatico->GetAccionCentralizada() != null){
								echo "Acci&oacute;n Centralizada:";
							}
						?></span></td>
						<td><?php
							if($viatico->GetProyecto() != null){
								echo $viatico->GetProyecto()->GetNombre();
							} else if($viatico->GetAccionCentralizada() != null){
								echo $viatico->GetAccionCentralizada()->GetNombre();
							}
						?></td>
					</tr>
					<tr>
						<td><span class="normalNegrita">Acci&oacute;n espec&iacute;fica: </span></td>
						<td><?php
							if( $viatico->GetProyectoEspecifica() != null){
								$especifica = $viatico->GetProyectoEspecifica(); 
								echo '(' .$especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto().') '.$especifica->GetNombre();	
							} else if($viatico->GetAccionCentralizadaEspecifica() != null){
								$especifica = $viatico->GetAccionCentralizadaEspecifica();
								echo '(' .$especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto().') '.$especifica->GetNombre();
							}
						?></td>
					</tr>
					<tr>
						<td colspan="2" class="header normalNegroNegrita">Datos del viaje</td>
					</tr>
					<tr>
						<td class="normalNegrita">Fecha de inicio del viaje: </td>
						<td class="normalNegro"><?php echo $viatico->GetFechaInicioViaje()?></td>
					</tr>
					<tr>
						<td class="normalNegrita">Fecha de fin del viaje: </td>
						<td class="normalNegro"><?php echo $viatico->GetFechaFinViaje()?></td>
					</tr>
					<tr>
						<td colspan="2" class="header normalNegroNegrita">Logros alcanzados</td>
					</tr>
					<tr>
						<td colspan="2" class="normalNegro"><?php echo $rendicion->GetObjetivosViaje() ?></td>
					</tr>
					<tr>
						<td colspan="2"><table class="tablaalertas content" style="width: 100%"
							cellpadding="0" cellspacing="0"
						>
							<tr>
								<td class="header normalNegroNegrita">Asignaci&oacute;n</td>
								<td class="header normalNegroNegrita">Monto</td>
								<td class="header normalNegroNegrita">Unidad de medida</td>
								<td class="header normalNegroNegrita">Unidades</td>
								<td class="header normalNegroNegrita">Subtotal</td>
							</tr>
							<?php
								$totalMonto = 0.0;
								if(is_array($viatico->GetViaticoResponsableAsignaciones())){
									$viaticoRespAsignaciones = $viatico->GetViaticoResponsableAsignaciones();
									foreach($viaticoRespAsignaciones as $codigoAsignacion => $viaticoRespAsignacion){
										$asignacion = $asignaciones[$codigoAsignacion];
										$totalMonto += $viaticoRespAsignacion->GetMonto() * $viaticoRespAsignacion->GetUnidades();
							?>
							<tr>
								<td class="normalNegrita"><?php echo $asignacion->GetNombre() ?></td>
								<td><?php echo $viaticoRespAsignacion->GetMonto() ?></td>
								<td><?php
									switch($asignacion->GetUnidadMedida()){
										case EntidadAsignacionViatico::UNIDAD_MEDIDA_POR_NOCHE:
											echo "Por noche";
											break;
										case EntidadAsignacionViatico::UNIDAD_MEDIDA_DIARIO:
											echo "Diario";
											break;
										case EntidadAsignacionViatico::UNIDAD_MEDIDA_POR_TRASLADO:
											echo "Por traslado";
											break;
										case EntidadAsignacionViatico::UNIDAD_MEDIDA_POR_VIAJE:
											echo "Por viaje";
											break;
										case EntidadAsignacionViatico::UNIDAD_MEDIDA_POR_KILOMETRO:
											echo "Por km";
											break;
									}
								?></td>
								<td><?php echo $viaticoRespAsignacion->GetUnidades() ?></td>
								<td><?php echo $viaticoRespAsignacion->GetMonto() * $viaticoRespAsignacion->GetUnidades() ?></td>
							</tr>
							<?php	
									}
								}
							?>
							<tr>
								<td colspan="3"></td>
								<td class="normalNegrita">Anticipo:</td>
								<td><?php echo $totalMonto ?></td>
							</tr>
							<tr>
								<td colspan="3"></td>
								<td class="normalNegrita">Total gastado:</td>
								<td><?php echo $rendicion->GetTotalGastos() ?></td>
							</tr>
							<tr>
								<td colspan="3"></td>
								<td class="normalNegrita">Reintegro a la fundaci&oacute;n:</td>
								<td id="tdMontoReintegro"><?php echo $totalMonto - $rendicion->GetTotalGastos() ?></td>
							</tr>
						</table></td>
					</tr>
					<tr>
						<td colspan="2" class="header normalNegroNegrita">Datos del reintegro</td>
					</tr>
					<tr>
						<td class="normalNegrita">Banco</td>
						<td class="normalNegro"><?php
							echo (($rendicion->GetReintegroBanco() != null && $rendicion->GetReintegroBanco() instanceof EntidadBanco) ?
								$rendicion->GetReintegroBanco()->GetNombre() : "")
						?></td>
					</tr>
					<tr>
						<td class="normalNegrita">Nro. referencia</td>
						<td class="normalNegro"><?php echo $rendicion->GetReintegroReferencia() ?></td>
					</tr>
					<tr>
						<td class="normalNegrita">Fecha</td>
						<td class="normalNegro"><?php echo $rendicion->GetReintegroFecha() ?></td>
					</tr>
					<tr>
						<td class="normalNegrita">Monto</td>
						<td class="normalNegro"><?php echo $totalMonto - $rendicion->GetTotalGastos() ?></td>
					</tr>
					<?php
						if($rendicion->GetObservaciones() != null && $rendicion->GetObservaciones() != ''){
							echo '
					<tr>
						<td colspan="2" class="header normalNegroNegrita">Observaciones</td>
					</tr>
					<tr>
						<td colspan="2">'.$rendicion->GetObservaciones().'</td>
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
							$finalizarPorNoReintegro = false;
							// Validar que no exista reintegro en el viático, para que el gerente lo pueda finalizar
							if(
								substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_DIRECTOR ||
								substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_GERENTE
							)
							{
								$diferencia = $totalMonto - $rendicion->GetTotalGastos();
								
								if($diferencia < 0.000001)
								{
									$finalizarPorNoReintegro = true;
								}
							}
					?>
					<tr>
						<td colspan="2">
							<form name="accionesForm" id="accionesForm" method="post" action="rendicion.php">
								<input type="hidden" name="idRendicion" value="<?php echo $rendicion->GetId() ?>">
								<input id="hiddenAccion" type="hidden" name="accion" value="Enviar">
								<input id ="hiddenMemo" type="hidden" name="memoContent">
								<table class="tablaalertas" align="center" width="100%">
									<tr>
										<td style="text-align: center;">
											<?php if ($finalizarPorNoReintegro) {?>
											<input class="normalNegro" type="button" value="Finalizar" onclick="accionFinalizar();">
											<?php } else { ?>
											<input class="normalNegro" type="button" value="Aprobar" onclick="accionAprobar();">
											<?php }  ?>
											<input class="normalNegro" type="button" value="Devolver" onclick="accionDevolver();">
											<a href="rendicion.php?accion=Bandeja" style="text-decoration: none;">
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
							<form name="accionesForm" id="accionesForm" method="post" action="rendicion.php">
								<input type="hidden" name="idRendicion" value="<?php echo $rendicion->GetId() ?>">
								<input id="hiddenAccion" type="hidden" name="accion" value="Devolver">
								<input id ="hiddenMemo" type="hidden" name="memoContent">
								<table class="tablaalertas" align="center" width="100%">
									<tr>
										<td style="text-align: center;">
											<input class="normalNegro" type="button" value="Devolver" onclick="accionDevolver();">
											<a href="rendicion.php?accion=Bandeja" style="text-decoration: none;">
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
							<form name="accionesFormAA" id="accionesFormAA" method="post" action="rendicion.php">
								<input type="hidden" name="idRendicion" value="<?php echo $rendicion->GetId() ?>">
								<input id="hiddenAccion" type="hidden" name="accion" value="Enviar">
								<table class="tablaalertas" align="center" width="100%">
									<tr>
										<td style="text-align: center;">
											<a
												href="rendicion.php?
													accion=Modificar&idRendicion=<?php echo $rendicion->GetId() ?>"
												style="text-decoration: none;"
											>
												<input class="normalNegro" type="button" value="Modificar">
											</a>
											<input class="normalNegro" type="button" value="Enviar" onclick="accionEnviar();">
											<a
												href="rendicion.php?
													accion=Anular&idRendicion=<?php echo $rendicion->GetId(); ?>"
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
								href="rendicion.php?tipo=L&accion=GenerarPDF&idRendicion=<?php echo $rendicion->GetId()?>"
								class="normal"
							>aqu&iacute;</a>
							<a
								href="rendicion.php?tipo=L&accion=GenerarPDF&idRendicion=<?php echo $rendicion->GetId()?>"
							><img src="../../imagenes/pdf_ico.jpg" border="0"/></a>
						</td>
						<!-- 
						<td align="center" width="150px">
							<a
								href="rendicion.php?tipo=F&accion=GenerarPDF&idRendicion=<?php echo $rendicion->GetId()?>"
								class="normal"
							>aqu&iacute;</a>
							<a
								href="rendicion.php?tipo=F&accion=GenerarPDF&idRendicion=<?php echo $rendicion->GetId()?>"
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
