<?php
	$form = FormManager::GetForm(FORM_NUEVA_RENDICION_AVANCE);
	$rendicion = null;
	$docGenera = null;
	$avance = null;
	
	if($form != null)
	{
		$rendicion = $form->GetRendicionAvance();
		$docGenera = $form->GetDocGenera();
		$avance = $form->GetAvance();
	}
	
?>
<html>
	<head>
		<title>.:SAFI:. Detalles de la rendici&oacute;n avance</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<script type="text/javascript" src="../../js/constantes.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript">
			function accionAprobar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea aprobar esta rendici"+oACUTE+"n de avance? ")) 
				{
					var objAccionesForm = $('#accionesForm');
					var objHiddenAccion = $('#hiddenAccion');
		
					objHiddenAccion.val('Enviar');
		
					objAccionesForm.submit();
				}
			}
	
			function accionEnviar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea enviar esta rendici"+oACUTE+"n de avance? ")) 
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
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea devolver esta rendici"+oACUTE+"n de avance? ")) 
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
							.: Rendici&oacute;n de avance <?php echo $rendicion->GetId() ?> :.
							<?php
								if($docGenera->GetIdEstatus() == "15") {
									echo '<br/><span style="color: red;">Anulado</span>';
								}
							?>
						</td>
					</tr>
					<!-- 
					<tr>
						<td class="normalNegrita">Fecha de registro:</td>
						<td><?php
							//$fecha = explode(" ", $rendicion->GetFechaRegistro());
							//echo $fecha[0];
						?></td>
					</tr>
					-->
					<tr>
						<td class="normalNegrita">Fecha de la rendici&oacute;n:</td>
						<td><?php echo $rendicion->GetFechaRendicion(); ?></td>
					</tr>
					<tr>
						<td class="normalNegrita">C&oacute;digo del avance:</td>
						<td class="normalNegro"><?php echo $avance->GetId() ?></td>
					</tr>
					<tr>
						<td class="normalNegrita">Fecha del avance:</td>
						<td class="normalNegro"><?php
							$fecha = explode(" ", $avance->GetFechaAvance());
							echo $fecha[0];
						?></td>
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
						<td class="normalNegrita">Fecha de inicio: </td>
						<td><?php echo $rendicion->GetFechaInicioActividad() ?></td>
					</tr>
					<tr>
						<td class="normalNegrita">Fecha de Fin: </td>
						<td><?php echo $rendicion->GetFechaFinActividad() ?></td>
					</tr>
					<tr>
						<td style="vertical-align: top;" class="normalNegrita">Logros alcanzados: </td>
						<td style="text-align: justify;"><?php
							echo ToHtmlEncode($rendicion->GetObjetivos());
						?></td>
					</tr>
					<?php
						if($rendicion->GetDescripcion() != null && trim($rendicion->GetDescripcion()) != '')
						{
							echo '
					<tr>
						<td style="vertical-align: top;" class="normalNegrita">Descripci&oacute;n de la actividad: </td>
						<td style="text-align: justify;">' . ToHtmlEncode($rendicion->GetDescripcion()) . '</td>
					</tr>
							';
						}
					?>
					<tr>
						<td class="normalNegrita">Nro. participantes: </td>
						<td><?php echo $rendicion->GetNroParticipantes() ?></td>
					</tr>
					<tr>
						<td colspan="2" class="normalNegroNegrita header">Responsables</td>
					</tr>
					<tr>
						<td colspan="2">
						<?php
						if(is_array($rendicion->GetResponsablesRendicionAvancePartidas()) 
							&& count($rendicion->GetResponsablesRendicionAvancePartidas())>0
						){
							foreach ($rendicion->GetResponsablesRendicionAvancePartidas() as $responsableRendicionAvancePartidas)
							{
								$responsableRendicionAvance = $responsableRendicionAvancePartidas->GetResponsableRendicionAvance();
								$id = "";
								$nombre = "";
								
								// Obtener los datos del empleado/beneficiario
								if(
									$responsableRendicionAvance->GetTipoResponsable() == EntidadResponsable::TIPO_EMPLEADO
									&& $responsableRendicionAvance->GetEmpleado() != null
								){
									$empleado = $responsableRendicionAvance->GetEmpleado();

									$id = $empleado->GetId();
									$nombre = $empleado->GetNombres() . ' ' .$empleado->GetApellidos(); 
								}
								else if (
									$responsableRendicionAvance->GetTipoResponsable() == EntidadResponsable::TIPO_BENEFICIARIO
									&& $responsableRendicionAvance->GetBeneficiario() != null
								){
									$beneficiario = $responsableRendicionAvance->GetBeneficiario();
									
									$id = $beneficiario->GetId();
									$nombre = $beneficiario->GetNombres() . ' ' .$beneficiario->GetApellidos();
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
												($responsableRendicionAvance->GetEstado() != null)
												? $responsableRendicionAvance->GetEstado()->GetNombre() : ""
											).'</td>
										</tr>
									</table></td>
								</tr>
								';
											
								if(
									is_array($responsableRendicionAvancePartidas->GetRendicionAvancePartidas())
									&& count($responsableRendicionAvancePartidas->GetRendicionAvancePartidas()) > 0
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
									
									$countRendicionAvancePartida = 0;
									$esPar = true;
									
									foreach ($responsableRendicionAvancePartidas->GetRendicionAvancePartidas() as $rendicionAvancePartida)
									{
										$idpartida = ($rendicionAvancePartida->GetPartida()
											? $rendicionAvancePartida->GetPartida()->GetId() : "");
										
										if($countRendicionAvancePartida % 2 == 0) $esPar = true;
										else $esPar = false;
										
										if($esPar){echo '<tr>';}
										echo '
												<td>'.$idpartida.'</td>
												<td>'.number_format($rendicionAvancePartida->GetMonto(),2,',','.').'</td>
										';
										if(!$esPar){echo '</tr>';}
										
										$countRendicionAvancePartida++;
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
									';
									
									$rendicionAvanceReintegros =
										$responsableRendicionAvancePartidas->GetRendicionAvanceReintegros();
										
									if (is_array($rendicionAvanceReintegros)&& count($rendicionAvanceReintegros) > 0)
									{
										echo '
								<tr>
									<td><table class="tableSub" cellspacing="0" cellpadding="0">
										<tr>
											<td class="normalNegrita">Banco</td>
											<td class="normalNegrita">Nro. referencia</td>
											<td class="normalNegrita">Fecha</td>
											<td class="normalNegrita">Monto</td>
										</tr>
										';
										
										foreach ($rendicionAvanceReintegros as $rendicionAvanceReintegro)
										{
											$bancoNombre = $rendicionAvanceReintegro->GetBanco() != null ?
												mb_strtoupper($rendicionAvanceReintegro->GetBanco()->GetNombre(), 'ISO-8859-1') : "";
											
											echo '
										<tr>
											<td>'.$bancoNombre.'</td>
											<td>'.$rendicionAvanceReintegro->GetReferencia().'</td>
											<td>'.$rendicionAvanceReintegro->GetFecha().'</td>
											<td>'.number_format($rendicionAvanceReintegro->GetMonto(),2,',','.').'</td>
										</tr>
											';
										}
										
										echo '
									</table></td>
								</tr>
										';
										
									}
									
									$montoAnticipo = $responsableRendicionAvancePartidas->GetMontoAnticipo();
									$montoGastado = $responsableRendicionAvancePartidas->GetMontoTotal();
									$montoReintegrado = $responsableRendicionAvancePartidas->GetMontoReintegrado();
									$montoReintegro = $montoAnticipo - $montoGastado;
									$diferencia = $montoReintegro - $montoReintegrado;  
									
									$textoEtiquetaReintegro = "Reintegro";
									if($montoAnticipo < $montoGastado - 0.000001)
									{
										$textoEtiquetaReintegro = "Asumido por el trabajador";
										$montoReintegro *= -1;
									}
									else if($montoAnticipo > $montoGastado + 0.000001)
									{
										$textoEtiquetaReintegro = "Reintegro a la Fundaci&oacute;n";
									}
									
									echo '
								<tr>
									<td><table class="tableSub" cellspacing="0" cellpadding="0">
										<tr>
											<td style="text-align: right; padding-right: 25px;">
												<span class="normalNegrita">Monto anticipo:</span>&nbsp;&nbsp;'
													.number_format($montoAnticipo,2,',','.').'
											</td>
										</tr>
										<tr>
											<td style="text-align: right; padding-right: 25px;">
												<span class="normalNegrita">Monto gastado:</span>&nbsp;&nbsp;'
													.number_format($montoGastado,2,',','.').'
											</td>
										</tr>
										<tr>
											<td style="text-align: right; padding-right: 25px;">
												<span class="normalNegrita">'.$textoEtiquetaReintegro.':</span>&nbsp;&nbsp;'
													.number_format($montoReintegro,2,',','.').'
											</td>
										</tr>
										<tr>
											<td style="text-align: right; padding-right: 25px;">
												<span class="normalNegrita">Monto reintegrado:</span>&nbsp;&nbsp;'
													.number_format($montoReintegrado,2,',','.').'
											</td>
										</tr>
										<tr>
											<td style="text-align: right; padding-right: 25px;">
												<span class="normalNegrita">Monto diferencia:</span>&nbsp;&nbsp;'
													.number_format($diferencia,2,',','.').'
											</td>
										</tr>
									</table></td>
								</tr>
									';
									
								} // if(
								  // 	is_array($responsableAvancePartidas->GetAvancePartidas())
								  // 	&& count($responsableAvancePartidas->GetAvancePartidas()) > 0
								  //){
								
								echo '
							</table>
								';
								
							} // foreach ($rendicion->GetResponsablesRendicionAvancePartidas() as $responsableRendicionAvancePartidas)
							/*
							$montoAnticipo = $rendicion->GetMontoAnticipo();
							$montoTotal = $rendicion->GetMontoTotal();
							echo '
							<table class="wrapperResponsablesAvance">
								<tr>
									<td class="normalNegrita" style="text-align: right; padding-right: 25px;">
										<span class="normalNegrita">Total anticipo</span>:&nbsp;&nbsp;'
											.number_format($montoAnticipo,2,',','.').'
									</td>
								</tr>
								<tr>
									<td class="normalNegrita" style="text-align: right; padding-right: 25px;">
										<span class="normalNegrita">Total gastado</span>:&nbsp;&nbsp;'
											.number_format($montoTotal,2,',','.').'
									</td>
								</tr>
								<!--
								<tr>
									<td class="normalNegrita" style="text-align: right; padding-right: 25px;">
										<span class="normalNegrita">Total reintegro a la fundaci&oacute;n</span>:&nbsp;&nbsp;'
											.number_format(-1,2,',','.').'
									</td>
								</tr>
								<tr>
									<td class="normalNegrita" style="text-align: right; padding-right: 25px;">
										<span class="normalNegrita">Total asumido por el trabajador</span>:&nbsp;&nbsp;'
											.number_format(-1,2,',','.').'
									</td>
								</tr>
								-->
							</table>
							';
							*/
						} // if(is_array($rendicion->GetResponsablesRendicionAvancePartidas()) 
						  //	&& count($rendicion->GetResponsablesRendicionAvancePartidas())>0
						?>
						</td>
					</tr>
					<?php
						if($rendicion->GetObservaciones() != null && $rendicion->GetObservaciones() != ''){
							echo '
					<tr>
						<td colspan="2" class="normalNegroNegrita header">Observaciones</td>
					</tr>
					<tr>
						<td colspan="2">'.ToHtmlEncode($rendicion->GetObservaciones()).'</td>
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
							<form name="accionesForm" id="accionesForm" method="post" action="rendicion.php">
								<input type="hidden" name="idRendicion" value="<?php echo $rendicion->GetId() ?>">
								<input id="hiddenAccion" type="hidden" name="accion" value="Enviar">
								<input id ="hiddenMemo" type="hidden" name="memoContent">
								<table class="tablaalertas" align="center" width="100%">
									<tr>
										<td style="text-align: center;">
											<input class="normalNegro" type="button" value="Aprobar" onclick="accionAprobar();">
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
							strcmp($docGenera->GetIdPerfilActual(), $_SESSION['user_perfil_id']) != 0 &&
							$docGenera->GetIdEstatus() == 13
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
							if($docGenera->GetIdEstatus() == 59 || $docGenera->GetIdEstatus() == 7){
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
													accion=Modificar&idRendicion=<?php echo $rendicion->GetId()?>"
												style="text-decoration: none;"
											>
												<input class="normalNegro" type="button" value="Modificar">
											</a>
											<input class="normalNegro" type="button" value="Enviar" onclick="accionEnviar();">
											<a
												href="rendicion.php?
													accion=Anular&idRendicion=<?php echo $rendicion->GetId()?>"
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
								href="rendicion.php?tipo=F&accion=GenerarPDF&idRendicion=<?php //echo $rendicion->GetId()?>"
								class="normal"
							>aqu&iacute;</a>
							<a
								href="rendicion.php?tipo=F&accion=GenerarPDF&idRendicion=<?php //echo $rendicion->GetId()?>"
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