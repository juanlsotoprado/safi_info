<?php
include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');


$objComp = $GLOBALS['SafiRequestVars']['compromiso'];// SafiModeloCompromiso::GetCompromiso(array("idCompromiso" => "comp-4007413"));

if($objComp){
	$objComp->UTF8Encode();
	$objCompArray = $objComp->ToArray();
}
/*

echo "<pre>";
echo print_r($objComp);
echo "</pre>";
*/

if($objComp){

	$modificarComp = true;

	$comp = $objComp != null? utf8_decode($objComp->GetId())  : '';
	$fecha = $objComp  != null? utf8_decode($objComp->GetFecha())  : '';
	
	
	$fechahora = explode (' ',$fecha);

				$fecha = $fechahora[0];

				
				 
	$unidadDependencia = $objComp->GetGerencia() != null? utf8_decode($objComp->GetGerencia()->GetId()) : '';
	$proveedorSugerido = $objComp != null? utf8_decode($objComp->GetRifProveedorSugerido()) : '';


	$cadena = $proveedorSugerido;
	$caracter = ":";
	 
	if (strpos($cadena, $caracter) !== false){
		$proveedorSugerido2 = explode(":",$proveedorSugerido);
		$proveedorSugeridoVal = utf8_decode($proveedorSugerido2[0]);
	}else{
		 
		$proveedorSugeridoVal = '';
	}

	$asunto = $objComp->GetAsunto() != null? utf8_decode($objComp->GetAsunto()->GetNombre()) : '';
	$asuntoVal = $objComp->GetAsunto() != null? utf8_decode($objComp->GetAsunto()->GetId()) : '';
	 
	$actividad = $objComp->GetActividad() != null? utf8_decode($objComp->GetActividad()->GetNombre()) : '';
	$actividadVal = $objComp->GetActividad() != null? utf8_decode($objComp->GetActividad()->GetId()) : '';
	
	$actividad = $objComp->GetActividad() != null? utf8_decode($objComp->GetActividad()->GetNombre()) : '';
	$actividadVal = $objComp->GetActividad() != null? utf8_decode($objComp->GetActividad()->GetId()) : '';

	$fechaReporte = $objComp  != null? utf8_decode($objComp->GetFechaReporte())  : '';

	 
	$evento = $objComp->GetEvento() != null? utf8_decode($objComp->GetEvento()->GetNombre()) : '';
	$eventoVal = $objComp->GetEvento() != null? utf8_decode($objComp->GetEvento()->GetId()) : '';
	 
	$codDocumento = $objComp != null? utf8_decode($objComp->GetIdDocumento()) : '';
	$descripcion = $objComp != null?  utf8_decode($objComp->GetDescripcion()) : '';
	
	$compEstatus = $objComp != null?  utf8_decode($objComp->GetCompEstatus()) : '';
	
	$localidad = $objComp->GetLocalidad() != null? utf8_decode($objComp->GetLocalidad()->GetId()) : '';
	 
	$infocentro = $objComp->GetInfocentro() != null? utf8_decode($objComp->GetInfocentro()->GetNombre()) : '';
	$infocentroVal = $objComp->GetInfocentro() != null? utf8_decode($objComp->GetInfocentro()->GetId()) : '';
	 
	$nParticipantes = $objComp  != null? utf8_decode($objComp->GetParticipante())  : '';
	$observacion = $objComp  != null? utf8_decode($objComp->GetObservacion())  : '';


	$montoSolicitado =  $objComp != null? utf8_decode($objComp->GetMontoSolicitado())  : 0;
	$compromisoImputas = $objCompArray != null ? $objCompArray['compromisoImputas']  : null;

	$pctaAsociado = $objComp != null? utf8_decode($objComp->GetPcta())  : '';

	$controlinterno = $objComp->GetControlInterno() != null? utf8_decode($objComp->GetControlInterno()->GetId()) : '';

}



?>

<html>
<head>
<title>.:SAFI:. Compromiso</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />
<?php require(SAFI_JAVASCRIPT_PATH.'/init.php'); ?>
<link href="<?=GetConfig("siteURL").'/css/safi0.2.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />

<?php require(SAFI_INCLUDE_PATH.'/fechaJs.php'); ?>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"
	charset="utf-8"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/ui.min.js';?>"
	charset="utf-8"></script>

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/constantes.js';?>"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/funciones.js';?>"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/func_montletra_jquery.js';?>"
	charset="utf-8"></script>

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/uploadify/uploadify/jquery.uploadify.min.js';?>"
	charset="utf-8"></script>

<link type="text/css"
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'lib/calendarPopup/css/calpopup.css';?>"
	media="screen" rel="stylesheet" />

<style>
.uploadify-button {
	background: transparent;
	border: none;
	padding-left: 0;
	background-image: url('../../js/lib/uploadify/examinar.png');
	border: 0;
}

.uploadify:hover .uploadify-button {
	background: transparent;
	border: none;
	background-image: url('../../js/lib/uploadify/examinar2.png');
	border: 0;
}
</style>

<!-- jQuery and jQuery UI -->

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/jquery-ui-1.8.13.custom.min.js';?>"
	charset="utf-8"></script>
<link
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />

<!-- elRTE -->

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/elrte.min.js';?>"
	charset="utf-8"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/elRTE.options.js';?>"
	charset="utf-8"></script>
<link
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/elrte.min.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />
<link
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/elrte-inner.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />



<!-- elRTE translation messages -->

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/i18n/elrte.es.js';?>"
	charset="utf-8"></script>

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/compromiso/compromiso.js';?>"
	charset="utf-8"></script>

<script type="text/javascript" charset="utf-8">
       PHPSESSID = '<?php echo $_COOKIE['PHPSESSID'];?>';
 </script>

<script type="text/javascript" charset="utf-8">
      compImputa = <?php echo json_encode($compromisoImputas,JSON_FORCE_OBJECT); ?>;
	  pctaAsociado = "<?php echo $pctaAsociado;?>";
	  modificarComp = "<?php echo $modificarComp;?>";
 </script>


	<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/DetalleCompletoDocumento.js';?>"
	charset="utf-8"></script>
</head>

<body>
<?php include(SAFI_VISTA_PATH . '/detalleCompletoDocumeto.php');
      include(SAFI_VISTA_PATH . '/mensajes.php');

      ?>
	<form name="formcompromiso" id="formcompromiso" method="post" action="">
		<table cellpadding="0" cellspacing="0" align="center"
			style="width: 100%;" background="../../imagenes/fondo_tabla.gif"
			class="tablaalertas">
			<tr>
				<th class="header"><span class="normalNegroNegrita">.: Compromiso :.
				<?php echo $comp ?> </span>
			
			</tr>
			<tr>
				<td>
					<table>
						<tr>
							<td style="width: 200px;" class="normalNegrita">Fecha:</td>
							<td><input type="text" size="10" id="fecha" name="fecha"
								class="dateparse" readonly="readonly"
								value="<?php echo $fecha?>" /> <a href="javascript:void(0);"
								onclick="g_Calendar.show(event, 'fecha');"
								title="Show popup calendar"> <img
									src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif"
									class="cp_img" alt="Open popup calendar" /> </a></td>
						</tr>
						<tr>
							<td><div align="left" class="normal">
									<b>Elaborado por:</b>
								</div></td>
							<td class="normal"><?php echo $_SESSION['solicitante']; ?></td>
						</tr>
						<tr>

							<td class="normalNegrita">Unidad/Dependencia:</td>
							<td><select name="unidadDependencia" id="unidadDependencia"
								class="normalNegrita">
									<option selected value="">.:.Seleccione.:.</option>
									<?php
									if($GLOBALS['SafiRequestVars']['unidadDependencia']){
										foreach ($GLOBALS['SafiRequestVars']['unidadDependencia'] as  $var){ ?>

									<option
									<?php echo $var->GetId() == $unidadDependencia? 'selected': ''  ?>
										value="<?php echo $var->GetId(); ?>">
										<?php echo $var->GetNombre();?>
									</option>


									<?php }} ?>

							</select>
							</td>

						</tr>
						<tr id="trCompAsociado">
							<td class="normalNegrita">Punto cuenta asociado:</td>
							<td><input type="text" id="compAsociado" name="compAsociado" />
							</td>
						</tr>
						<tr>
							<td class="normalNegrita">Proveedor sugerido:</td>
							<td><input type="text" name="ProveedorSugerido"
								id="ProveedorSugerido" class="normalNegro" size="65"
								value="<?php echo $proveedorSugerido ?>" /> <input type="hidden"
								name="ProveedorSugeridoval" id="ProveedorSugeridoValor"
								value="<?php echo $proveedorSugeridoVal ?>"></td>
						</tr>
						<tr>
							<td class="normalNegrita">Asunto:</td>
							<td><input type="text" id="asunto" name="asunto"
								value="<?php echo $asunto ?>" /> <input type="hidden"
								name="asuntoVal" id="asuntoVal" value="<?php echo $asuntoVal ?>">
							</td>
						</tr>
						

						
						<tr id='estatuscomp' >
							<td class="normalNegrita">Estatus:</td>
							<td>
							
                            <select name="estatus" class="normalNegrita">
							<option <?php echo "Por Rendir" == $compEstatus? 'selected': ''  ?>
										value="<?php echo $compEstatus; ?>">
						
							Por Rendir</option>
							<option <?php echo "Reportado" == $compEstatus? 'selected': ''  ?>
										value="Reportado">
						  Reportado</option>
							
							</select>
							
							</td>
						</tr>
						
						<tr id='estatusfecha'>
							<td style="width: 200px;" class="normalNegrita">Fecha reporte:</td>
							<td><input type="text" size="10" id="fechaReporte" name="fechaReporte"
								class="dateparse" readonly="readonly"
								value="<?php echo $fechaReporte?>" /> <a href="javascript:void(0);"
								onclick="g_Calendar.show(event, 'fechaReporte');"
								title="Show popup calendar"> <img
									src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif"
									class="cp_img" alt="Open popup calendar" /> </a></td>
						</tr>
						
						<tr style="display: none;">
							<!-- campo comentado inhabilitado por presupuesto 01/04/13 -->
							<td class="normalNegrita">Tipo Actividad:</td>
							<td><input type="text" id="tipoActividad"
								value="<?php echo $actividad ?>" name="tipoActividad" /> <input
								type="hidden" name="tipoActividadVal" id="tipoActividadVal"
								value="<?php echo $actividadVal ?>">
							</td>
						</tr>

						<tr style="display: none;">
							<!-- campo comentado inhabilitado por presupuesto 01/04/13 -->

							<td width="175" height="29" class="normalNegrita" align="left">Duracit&oacute;n
								de la actividad:</td>
							<td><input type="text" size="10" id="txt_inicio"
								name="txt_inicio" class="dateparse"
								onfocus="javascript: comparar_fechas(this);" readonly="readonly"
								value="<?php echo $fechaInicio; ?>" /> <a
								href="javascript:void(0);"
								onclick="g_Calendar.show(event, 'txt_inicio');"
								title="Show popup calendar"> <img
									src="../../js/lib/calendarPopup/img/calendar.gif"
									class="cp_img" alt="Open popup calendar" /> </a> <input
								type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin"
								value="<?php echo $fechaFin; ?>" class="dateparse"
								onfocus="javascript: comparar_fechas(this);" readonly="readonly"
								value="" /> <a href="javascript:void(0);"
								onclick="g_Calendar.show(event, 'hid_hasta_itin');"
								title="Show popup calendar"> <img
									src="../../js/lib/calendarPopup/img/calendar.gif"
									class="cp_img" alt="Open popup calendar" /> </a>
							</td>
						</tr>

						<tr style="display: none;">
							<!-- campo comentado inhabilitado por presupuesto 01/04/13 -->
							<td class="normalNegrita">Tipo Evento:</td>
							<td><input type="text" id="tipoEvento" name="tipoEvento"
								value="<?php echo $evento ?>" /> <input type="hidden"
								name="tipoEventoVal" id="tipoEventoVal"
								value="<?php echo $eventoVal ?>">
							</td>
						</tr>
						<tr>
							<td class="normalNegrita">C&oacute;digo Documento:</td>
							<td><input type="text" id="CodigoDocumento"
								value="<?php echo $codDocumento ?>" name="CodigoDocumento" />
							</td>
						</tr>

						<tr>

							<td class="normalNegrita">Descripci&oacute;n:</td>
							<td><span id="spanDescrip" class="normalNegrita"> <b
									class="links">Mostrar editor</b><img id="imgDescrip"
									src="../../imagenes/triangulonegro1.jpg"
									style="width: 10px; heigth: 10px; margin-left: 5px;" /> <img
									id="imgDescrip2" src="../../imagenes/triangulonegro2.jpg"
									style="width: 10px; heigth: 10px; margin-left: 5px;" /> </span>

								<br /> <br /> <input type="hidden" value="" id="spanDescripval" />

								<div id="compromiso_descripcion2">

									<div id="compromiso_descripcion" class="compromiso_descripcion">
									<?php echo $descripcion ?>
									</div>
									<input type="hidden" name="compromiso_descripcionVal"
										id="compromiso_descripcionVal" value="">
								</div>
							</td>
						</tr>

						<tr>
							<td class="normalNegrita">Estado:</td>
							<td><select name="localidad" id="localidad" class="normalNegrita">
									<option selected value="">.:.Seleccione.:.</option>
									<optgroup label="Venezuela">
									<?php
									if($GLOBALS['SafiRequestVars']['estadosVenezuela']){
										foreach ($GLOBALS['SafiRequestVars']['estadosVenezuela'] as  $var){ ?>

										<option
										<?php echo $var->GetId() == $localidad? 'selected': ''  ?>
											value="<?php echo $var->GetId(); ?>">
											<?php echo $var->GetNombre();?>
										</option>


										<?php }} ?>
									</optgroup>
							</select>
							</td>
						</tr>
						<tr>

							<td class="normalNegrita">Control interno:</td>
							<td><select name="controlinterno" id="controlinterno"
								class="normalNegrita">
									<option selected value="">.:.Seleccione.:.</option>
									<?php
									if($GLOBALS['SafiRequestVars']['controlInterno']){
										foreach ($GLOBALS['SafiRequestVars']['controlInterno'] as  $var){ ?>

									<option
									<?php echo $var->GetId() == $controlinterno? 'selected': ''  ?>
										value="<?php echo $var->GetId(); ?>">
										<?php echo $var->GetNombre();?>
									</option>


									<?php }} ?>

							</select>
							</td>

						</tr>
						<tr style="display: none;">
							<!-- campo comentado inhabilitado por presupuesto 01/04/13 -->
							<td class="normalNegrita">Infocentro:</td>
							<td><input type="text" id="infocentro" name="infocentro"
								size="50" value="<?php echo $infocentro ?>" /> <input
								type="hidden" name="infocentroVal" id="infocentroVal"
								value="<?php echo $infocentroVal ?>">
							</td>
						</tr>
						<tr style="display: none;">
							<!-- campo comentado inhabilitado por presupuesto 01/04/13 -->
							<td class="normalNegrita">Numero de participantes:</td>
							<td><input type="text" id="numeroParticipantes"
								name="numeroParticipantes" value="<?php echo $nParticipantes ?>" />
							</td>
						</tr>
						<tr>
							<td><div align="left" class="normal">
									<strong>Observaciones:</strong>
								</div></td>
							<td><textarea rows="3" name="observaciones" id="observaciones"cols="50"><?php echo $observacion ?></textarea>
							</td>
						</tr>

					</table>
				</td>
			</tr>
		</table>


		<table>

			<tr>
				<td colspan="2"><br />




					<fieldset id="filsed1">
						<legend>
							<b>Categor&iacute;a program&aacute;tica/Partida</b>
						</legend>


						<div align="left" class="normal">
							<div id="trselectProyAcc">

								<strong style="margin-right: 130px;">Proy/Acc:</strong> <select
									name="selectProyAcc" class="normalNegrita" id="selectProyAcc">
									<option selected value="">.:.Seleccione.:.</option>


									<optgroup label="Proyectos">
									<?php foreach ( $GLOBALS['SafiRequestVars']['proyectos'] as $proyectos ){ ?>

										<option value="<?php echo $proyectos['proy_id']; ?>">
										<?php echo $proyectos['proy_titulo']?>
										</option>

										<?php }?>
									</optgroup>

									<optgroup label="Acc.">
									<?php foreach ( $GLOBALS['SafiRequestVars']['acc'] as $acc){ ?>

										<option value="<?php echo $acc['acce_id']; ?>">
										<?php echo $acc['acce_denom']; ?>
										</option>

										<?php }?>
									</optgroup>


								</select> </br> </br>

							</div>
							<div id="trcategoriaesp">
								<div align="left" class="normal">
									<strong style="margin-right: 103px;">Ac. Espec&iacute;fica:</strong>
									<input type="text" name="Categoria" id="Categoria"
										class="normalNegro" size="68" value="" />

								</div>
							</div>
							<div id="tablacategoria">

								<table class="tablaalertas" border="1">

									<tr>
										<th class="header"><span class="normalNegroNegrita">Tipo</span>
										</th>
										<th class="header"><span class="normalNegroNegrita">Nombre
												Proy/Acc</span></th>
										<th class="header"><span class="normalNegroNegrita">Proy/Acc 
										
										</th>
										<th class="header"><span class="normalNegroNegrita">Nombre
												Acci&oacute;n esp.</span></th>
									</tr>
									<tr>
										<td id="c1" class="normal"></td>
										<td id="c3" class="normal"></td>
										<td id="c2" class="normal"></td>
										<td id="c4" class="normal"></td>
									</tr>
								</table>

							</div>

							<div id="divPartida">
								<br />
								<hr width="100%" height="1px">
								<div align="left" class="normal">
									<strong style="margin-right: 136px;">Partida :</strong> <input
										type="text" name="Partida" id="Partida" class="normalNegro"
										size="20" value="" /> <br /> <br /> <span
										class="patidaseleccionada"> <strong
										style="margin-right: 96px;">Denominaci&oacute;n :</strong> <strong
										id="Partidadenominacion" style="color: black"></strong> <input
										style="margin-left: 30px" type="button" id="confirmarPartida"
										value="Confirmar"> </span>
								</div>
							</div>

							<div id="cuerpoCategoriaPartidaEliminada">
								<fieldset>
									<legend style="font-size: 13px; font-family: sans-serif;">
										<b>Partidas del punto de cuenta no insertadas en el compromiso</b>
									</legend>
									<br />
									<table class="tablaalertas" border="1">
										<tbody id="tablaCategoriaPartidaAgregar">
											<tr>
												<th class="header"><span class="normalNegroNegrita">Tipo</span>
												</th>
												<th class="header"><span class="normalNegroNegrita">Nombre
														proy/acc</span></th>
												<th class="header"><span class="normalNegroNegrita">Proy/Acc

												
												
												</th>
												<th class="header"><span class="normalNegroNegrita">Nombre
														Acci&oacute;n esp.</span></th>
												<th class="header"><span class="normalNegroNegrita">Partida</span>
												</th>
												<th class="header"><span class="normalNegroNegrita">Denominaci&oacute;n</span>
												</th>
												<th class="header"><span class="normalNegroNegrita">Disponibilidad</span>
												</th>
												<th class="header"><span class="normalNegroNegrita" aling="center">Insertar en el compromiso</span>
												</th>
											</tr>
										</tbody>
									</table>
								</fieldset>
								<br />

							</div>



							<div id="cuerpoCategoriaPartida">
								<br />
								<fieldset>
									<legend style="font-size: 13px; font-family: sans-serif;">
										<b>Partidas del punto de cuenta insertadas en el compromiso</b>
									</legend>
									<br />
									<table class="tablaalertas" border="1">
										<tbody id="tablaCategoriaPartida">
											<tr>
												<th class="header"><span class="normalNegroNegrita">Tipo</span>
												</th>
												<th class="header"><span class="normalNegroNegrita">Nombre
														proy/acc</span></th>
												<th class="header"><span class="normalNegroNegrita" >Proy/Acc</span></th>
												<th class="header"><span class="normalNegroNegrita">Nombre
														Acci&oacute;n esp.</span></th>
												<th class="header"><span class="normalNegroNegrita">Partida</span>
												</th>
												<th class="header"><span class="normalNegroNegrita">Denominaci&oacute;n</span>
												</th>
												<th class="header"><span class="normalNegroNegrita">Disponibilidad</span>
												</th>
												<th class="header"><span class="normalNegroNegrita">Monto</span>
												</th>
												<th class="header"><span class="normalNegroNegrita">Quitar del compromiso</span>
												</th>
											</tr>
										</tbody>
									</table>

								</fieldset>
							</div>

						</div>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td><br />
					<fieldset id='fieldset2' style="width: 400px;">
						<legend>
							<b>Total a solicitar</b>
						</legend>
						<div align="left" class="normal">
							<strong style="margin-right: 10px;">En Bs. :</strong> <strong
								style="font-size: 12px; color: black" id="montoTotal">0</strong>
							<input type="hidden" name="montoTotalHidden"
								id="montoTotalHidden" value="0">
						</div>
						<br />

						<div align="left" class="normal">
							<strong style="margin-right: 10px;">En letras. :</strong> <strong
								style="font-size: 12px; color: black" id="monto_letras"></strong>
						</fieldset>
						</div>
					
				</td>
			</tr>

		</table>

		<br /> <br />
		<div align='center'>
			<input type="button" value='Registrar' id="registrar"
				name="Registrar Documento" onclick="Revisar(this)" ; /> <input
				type="reset" value="Limpiar" id="reset" name="reset" /> <input
				type="hidden" name="comp" id="compHidden"
				value="<?php echo $comp ?>">
		</div>







	</form>

</body>
</html>
