<!DOCTYPE>
<html>
<head>
	<title>.:SAFI:Reporte Actividad</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
	<script language="JavaScript" src="../../js/funciones.js"></script>
	<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
	<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
	<script>
		function limpiar(){
			$("#idTipoActividadCompromiso").val("");
			$("#idEstado").val("");
			$("#fechaInicio").val("");
			$("#fechaFin").val("");
		}
		function buscar(){
			$("#click").val(true);
			$("#reporteActividadForm").submit();
		}
		function compararFechas(){
			var fechaInicio = $("#fechaInicio").val();
			var fechaFin = $("#fechaFin").val();

			if ( fechaInicio != "" && fechaFin != "" ) {
				var diaInicial = fechaInicio.substring(0,2);
				var mesInicial = fechaInicio.substring(3,5);
				var anioInicial = fechaInicio.substring(6,10);
				
				var diaFin = fechaFin.substring(0,2);
				var mesFin = fechaFin.substring(3,5);
				var anioFin = fechaFin.substring(6,10);

				diaInicial = parseInt(diaInicial,10);
				mesInicial = parseInt(mesInicial,10);
				anioInicial = parseInt(anioInicial,10);

				diaFin = parseInt(diaFin,10);
				mesFin = parseInt(mesFin,10);
				anioFin = parseInt(anioFin,10);
				
				if((anioInicial>anioFin) || ((anioInicial==anioFin) && (mesInicial>mesFin)) || ((anioInicial == anioFin) && (mesInicial==mesFin) && (diaInicial>diaFin))){
					alert("La fecha inicial no debe ser mayor a la fecha final"); 
					$("#fechaFin").val("");
					return;
				}
			}
		}
	</script>
</head>
<body class="normal">
	<form id="reporteActividadForm" name="reporteActividadForm" method="post" accept-charset="utf-8">
		<input type="hidden" id="click" name="click" value="false" />
		<table border="0" width="100%" cellpadding="0" cellspacing="0" align="center">
			<tr>
				<td colspan="3">
					<table border="0" width="50%" align="center" class="tablaalertas">
						<tr class="td_gray normalNegroNegrita">
							<td colspan="2" height="20px">REPORTE DE ACTIVIDAD</td>
						</tr>
						<tr class="normal" >
							<td valign="middle" height="30px" width="25%">
								Tipo de actividad
							</td>
							<td class="normal" valign="middle">
								<select id="idTipoActividadCompromiso" name="idTipoActividadCompromiso" class="normalNegro">
									<option value="">Todas</option>
									<?php 
									foreach($GLOBALS['SafiRequestVars']['listaTipoActividades'] as $tipoActividad){
										if($form->GetTipoActividad() && $form->GetTipoActividad()->GetId()==$tipoActividad->GetId()){
											echo "<option value='".$tipoActividad->GetId()."' selected='selected'>".$tipoActividad->GetNombre()."</option>";
										}else{
											echo "<option value='".$tipoActividad->GetId()."'>".$tipoActividad->GetNombre()."</option>";
										}
									}
									?>	
								</select>								
							</td>
						</tr>
						<tr class="normal" >
							<td valign="middle" height="30px" width="25%">
								Tipo de evento
							</td>
							<td class="normal" valign="middle">
								<select id="idTipoEvento" name="idTipoEvento" class="normalNegro">
									<option value="">Todos</option>
									<?php 
									foreach($GLOBALS['SafiRequestVars']['listaTipoEventos'] as $tipoEvento){
										if($form->GetTipoEvento() && $form->GetTipoEvento()->GetId()==$tipoEvento->GetId()){
											echo "<option value='".$tipoEvento->GetId()."' selected='selected'>".$tipoEvento->GetNombre()."</option>";
										}else{
											echo "<option value='".$tipoEvento->GetId()."'>".$tipoEvento->GetNombre()."</option>";
										}
									}
									?>	
								</select>								
							</td>
						</tr>
						<tr class="normal">
							<td valign="middle" height="30px">
								Estado
							</td>
							<td class="normal" valign="middle">
								<select id="idEstado" name="idEstado" class="normalNegro">
									<option value="">Todos</option>
									<?php 
									foreach($GLOBALS['SafiRequestVars']['listaEstados'] as $estado){
										if($form->GetEstado() && $form->GetEstado()->GetId()==$estado->GetId()){
											echo "<option value='".$estado->GetId()."' selected='selected'>".$estado->GetNombre()."</option>";
										}else{
											echo "<option value='".$estado->GetId()."'>".$estado->GetNombre()."</option>";
										}
									}
									?>	
								</select>
							</td>
						</tr>
						<tr class="normal">
							<td valign="middle" height="30px">
								Centro Gestor/Costo
							</td>
							<td class="normal" valign="middle">
								<select id="centroGestorCosto" name="centroGestorCosto" class="normalNegro">
									<option value="">Todos</option>
									<?php 
									foreach($GLOBALS['SafiRequestVars']['listaCentroGestorCostos'] as $centroGestorCosto){
										if($form->GetCentroGestorCosto()==$centroGestorCosto){
											echo "<option value='".$centroGestorCosto."' selected='selected'>".$centroGestorCosto."</option>";
										}else{
											echo "<option value='".$centroGestorCosto."'>".$centroGestorCosto."</option>";
										}
									}
									?>	
								</select>
							</td>
						</tr>
						<tr class="normal">
							<td valign="middle" height="30px">
								Duraci&oacute;n de la actividad
							</td>
							<td class="normal" valign="middle">
								Fecha Inicio:
								<input 
									type="text" 
									size="10" 
									id="fechaInicio" 
									name="fechaInicio" 
									class="dateparse" 
									onfocus="javascript: compararFechas();" 
									readonly="readonly"
									value="<?= $form->GetFechaInicio()?>"/>
								<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaInicio');">
									<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Fecha Inicio"/>
								</a>
								&nbsp;&nbsp;
								Fecha Fin:
								<input 
									type="text" 
									size="10" 
									id="fechaFin" 
									name="fechaFin" 
									class="dateparse" 
									onfocus="javascript: compararFechas();" 
									readonly="readonly"
									value="<?= $form->GetFechaFin()?>"/>
								<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaFin');">
									<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Fecha Fin"/>
								</a>
							</td>
						</tr>
						<tr>
							<td colspan="2" valign="middle" align="center" height="30px">
								<input type="button" value="Buscar" onclick="buscar();" class="normalNegro"/>
								<!-- <input type="button" value="Limpiar" onclick="limpiar();" class="normalNegro"/> -->
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<?php 
			if ( isset($GLOBALS['SafiRequestVars']['listaActividades']) ){
			?>
			<tr>
				<td colspan="3" style="font-size: 9pt;"><strong>Total <?= sizeof($GLOBALS['SafiRequestVars']['listaActividades'])?> Actividades</strong></td>
			</tr>
			<tr>
				<td colspan="3">
					<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
						<tr class="td_gray">
							<td width="15%" align="center" class="normalNegroNegrita">Tipo Actividad</td>
							<td width="15%" align="center" class="normalNegroNegrita">Tipo Evento</td>
							<td width="14%" align="center" class="normalNegroNegrita">Estado</td>
							<td width="14%" align="center" class="normalNegroNegrita">Duraci&oacute;n de la Actividad</td>
							<td width="7%" align="center" class="normalNegroNegrita">Nro Participantes</td>
							<td width="7%" align="center" class="normalNegroNegrita">Centro Gestor / Centro Costo</td>
							<td width="7%" align="center" class="normalNegroNegrita">Monto Solicitado</td>
							<td width="7%" align="center" class="normalNegroNegrita">Monto Causado</td>
							<td width="7%" align="center" class="normalNegroNegrita">Monto Pagado</td>
							<td width="7%" align="center" class="normalNegroNegrita">C&oacute;digo del Documento</td>
						</tr>
						<?php
						$totalParticipantesActividad = 0.0;
						$totalParticipantes = 0.0;
						$totalSolicitado = 0.0;
						$totalCausado = 0.0;
						$totalPagado = 0.0;
						$i=1;
						foreach($GLOBALS['SafiRequestVars']['listaActividades'] as $actividad){
						?>
							<tr class="normal">
								<td style="border-bottom: 1px solid #C3ECCC">
									<?php
									echo $i.".&nbsp;";
									$i++;
									if ( isset($actividad["nombre_actividad"]) ) {
										echo $actividad["nombre_actividad"];	
									} else echo "&nbsp;";
									?>
								</td>
								<td style="border-bottom: 1px solid #C3ECCC">
									<?php
									if ( isset($actividad["nombre_evento"]) ) {
										echo $actividad["nombre_evento"];	
									} else echo "&nbsp;";
									?>
								</td>
								<td style="border-bottom: 1px solid #C3ECCC">
									<?php
									/*if ( isset($actividad["compromisos"]) && sizeof($actividad["compromisos"])>0 ) {
										foreach($actividad["compromisos"] as $compromiso){
											echo $compromiso["nombre_estado"]."<br/>";
										}
									} else echo "&nbsp;";*/
									if ( isset($actividad["nombre_estado"]) ) {
										echo $actividad["nombre_estado"];
									} else echo "&nbsp;";
									?>
								</td>
								<td style="border-bottom: 1px solid #C3ECCC" align="center">
									<?php
										if ( isset($actividad["fecha_inicio"]) && isset($actividad["fecha_fin"]) ) {
											echo $actividad["fecha_inicio"]."-".$actividad["fecha_fin"];
										} else echo "&nbsp;";
									?>
								</td>
								<td style="border-bottom: 1px solid #C3ECCC">
									<?php
										if ( isset($actividad["compromisos"]) && sizeof($actividad["compromisos"])>0 ) {
											$totalParticipantesActividad = 0.0;
											foreach($actividad["compromisos"] as $compromiso){
												$token = strtok($compromiso["participantes"], " ");
												while ($token !== false) {
													if ( is_numeric($token)==true ) {
														$totalParticipantesActividad += intval($token);
													}
													$token = strtok(" ");
												}
											}
											if ( $totalParticipantesActividad > 0 && sizeof($actividad["compromisos"])>1 ) {
												$totalParticipantes += $totalParticipantesActividad;
												echo $totalParticipantesActividad."<br/>";
											} else {
												foreach($actividad["compromisos"] as $compromiso){
													echo $compromiso["participantes"]."<br/>";
												}												
											}
										} else echo "&nbsp;";
									?>
								</td>
								<td style="border-bottom: 1px solid #C3ECCC" align="center">
									<?php
										if (  isset($actividad["centro_costo"]) && isset($actividad["centro_gestor"]) ) {
											echo $actividad["centro_gestor"]."/".$actividad["centro_costo"];
										} else echo "&nbsp;";
									?>
								</td>
								<td style="border-bottom: 1px solid #C3ECCC" align="right">
									<?php
										if ( isset($actividad["monto_solicitado"]) ) {
											$totalSolicitado += $actividad["monto_solicitado"];
											echo $actividad["monto_solicitado"];
										} else echo "0.0";
									?>
								</td>
								<td style="border-bottom: 1px solid #C3ECCC" align="right">
									<?php
										if ( isset($actividad["monto_causado"]) ) {
											$totalCausado += $actividad["monto_causado"];
											echo $actividad["monto_causado"];
										} else echo "0.0";
									?>
								</td>
								<td style="border-bottom: 1px solid #C3ECCC" align="right">
									<?php
										if ( isset($actividad["monto_pagado"]) ) {
											$totalPagado += $actividad["monto_pagado"];
											echo $actividad["monto_pagado"];
										} else echo "0.0";
									?>
								</td>
								<td style="border-bottom: 1px solid #C3ECCC" align="center">
									<?php
									if ( isset($actividad["compromisos"]) && sizeof($actividad["compromisos"])>0 ) {
										foreach($actividad["compromisos"] as $compromiso){
											echo "<a target='_blank' href='../../documentos/comp/comp_detalle.php?codigo=".$compromiso["id"]."'>".$compromiso["id"]."</a><br/>";
										}
									} else echo "&nbsp;";
									?>									
								</td>
							</tr>
						<?php 
						}
						?>
						<tr class="normal">
							<td style="border-bottom: 1px solid #C3ECCC" align="right" colspan="3">
								<strong>Total Bs.</strong>
							</td>
							<td></td>
							<td style="border-bottom: 1px solid #C3ECCC">
								<strong>
								<?php
									if ( $totalParticipantes > 0 ) {
										echo $totalParticipantes;
									} else echo "0.0";
								?>
								</strong>
							</td>
							<td></td>
							<td style="border-bottom: 1px solid #C3ECCC" align="right">
								<strong>
								<?php
									if ( $totalSolicitado > 0 ) {
										echo $totalSolicitado;
									} else echo "0.0";
								?>
								</strong>
							</td>
							<td style="border-bottom: 1px solid #C3ECCC" align="right">
								<strong>
								<?php
									if ( $totalCausado > 0 ) {
										echo $totalCausado;
									} else echo "0.0";
								?>
								</strong>
							</td>
							<td style="border-bottom: 1px solid #C3ECCC" align="right">
								<strong>
								<?php
									if ( $totalPagado > 0 ) {
										echo $totalPagado;
									} else echo "0.0";
								?>
								</strong>
							</td>
							<td style="border-bottom: 1px solid #C3ECCC" align="center">
								&nbsp;							
							</td>
						</tr>
					</table>
				</td>			
			</tr>
			<?php 
			}
			?>
		</table>
	</form>
</body>
</html>