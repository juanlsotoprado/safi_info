<?php
include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');


  $objPmod =  $GLOBALS['SafiRequestVars']['pmod'] ;

if($objPmod){
	$objPmod->UTF8Encode();
	$objPmodArray = $objPmod->ToArray();
}


if($objPmod){

	$pmod = $objPmod != null? utf8_decode($objPmod->GetId())  : '';
	$fecha = $objPmod  != null? utf8_decode($objPmod->GetFecha())  : '';
	
	$fechahora = explode (' ',$fecha);

				$fecha = explode ('-',$fechahora[0]);

				$fecha  =  $fecha[0].'/'.$fecha[1].'/'.$fecha[2];
				 
	$observacion = $objPmod  != null? utf8_decode($objPmod->GetObservacion())  : '';
    $tipo = $objPmod != null? utf8_decode($objPmod->GetTipoDoc()) : '';
    
   
    
    $pmodImputa = $objPmodArray != null ? $objPmodArray['mpresupuestariaImputas']  : null;
    $pmodRespaldo = $objPmodArray != null ? $objPmodArray['respaldos']  : null;
   
         
}				 
						 
?>

<html>
<head>
<title>.:SAFI:. Compromiso</span></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />
<?php require(SAFI_JAVASCRIPT_PATH.'/init.php'); ?>
<link href="<?=GetConfig("siteURL").'/css/safi0.2.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />

<?php require(SAFI_INCLUDE_PATH.'/fechaJs.php');?>
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
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/func_montletra.js';?>"
	charset="utf-8"></script>

	<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/uploadify/uploadify/jquery.uploadify.min.js';?>"
	charset="utf-8"></script>
	
	
<script language="javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/func_montletra_jquery.js';?>"></script>

<link type="text/css"
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'lib/calendarPopup/css/calpopup.css';?>"
	media="screen" rel="stylesheet" />

	<link type="text/css" href="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/uploadify/uploadify/uploadify.css';?>"
	media="screen" rel="stylesheet" />
	

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
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/modificacionpresupuestaria/mpresupuestaria.js';?>"
	charset="utf-8"></script>

<script type="text/javascript" charset="utf-8">
       PHPSESSID = '<?php echo $_COOKIE['PHPSESSID'];?>';
 </script>

<script type="text/javascript" charset="utf-8">
      pmodImputa = <?php echo json_encode($pmodImputa,JSON_FORCE_OBJECT); ?>;
	  pmodRespaldo = <?php echo json_encode($pmodRespaldo,JSON_FORCE_OBJECT); ?>;
 </script>
 
 <style>


    .uploadify-button {
     background: transparent;
        border: none;
        padding-left: 0;
        background-image:url('../../js/lib/uploadify/examinar.png');
        border:0;
    }
    .uploadify:hover .uploadify-button {
      background: transparent;
        border: none;
        background-image:url('../../js/lib/uploadify/examinar2.png');
        border:0;
    }
      
        
</style>

</head>

<body>
<?php include(SAFI_VISTA_PATH . '/mensajes.php');?>
	<form name="formMP" id="formMP" method="post" action="">
		<table cellpadding="0" cellspacing="0" align="center"
			style="width: 100%;" background="../../imagenes/fondo_tabla.gif"
			class="tablaalertas">
			<tr>
				<th class="header"><span class="normalNegroNegrita">.: Compromiso :.
				<?php  if($pmod){echo "<span id= 'pmodAmodificar'>".$pmod."</span>";}; ?></span></th>
			
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
							<td style="width: 200px;" class="normalNegrita">Acci&oacute;n a relizar:</td>
							<td><select name="accionMP" class="normalNegrita" id="accionMP">
									<option selected <?php if($tipo == 2){ echo 'selected';}?> value="2">Traspaso</option>
									<option <?php if($tipo == 1){ echo 'selected';}?>  value="1">Cr&eacute;dito</option>
									<option  <?php if($tipo == 3){ echo 'selected';}?> value="3">Disminuci&oacute;n</option>
									
									</select>
							
							</td>
						</tr>
								<tr>
							<td><div align="left" class="normal">
									<strong>Exposici&oacute;n de motivos:</strong>
								</div></td>
							<td><textarea rows="7" name="observaciones" id="observaciones" cols="50"><?php if($observacion != null){ echo $observacion;}?></textarea>
							</td>
						</tr>

					</table>


					<table>

						<tr>
							<td colspan="2"><br />

								<fieldset id="filsed1">
									<legend>
										<b id='accionArealizar'>Seleccione las partidas que ceder&aacute;n monto</b>
									</legend>


									<div align="left" class="normal">
									
									<div>
										<strong style="margin-right: 100px;">Acci&oacute;n Partida:</strong> <select
												name="accionpartida" class="normalNegrita"
												id="accionpartida">
												
												<option selected value="1" >Cede</option>
												<option value="2" >Recibe</option>

											</select> </br></br>
									
									</div>
										<div id="trselectProyAcc">
											<strong style="margin-right: 130px;">Proy/Acc:</strong> <select
												name="selectProyAcc" class="normalNegrita"
												id="selectProyAcc">
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
														<th class="header"><span class="normalNegroNegrita">Proy/Acc

													</th>
													<th class="header"><span class="normalNegroNegrita">Nombre
															Proy/Acc</span></th>
												
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
											<br/>
											<hr width="100%" height="1px">
											<div align="left" class="normal">
												<strong style="margin-right: 136px;">Partida:</strong> <input
													type="text" name="Partida" id="Partida" class="normalNegro"
													size="20" value="" /> <br /> <br /> <span
													class="patidaseleccionada"> <strong
													style="margin-right: 96px;">Denominaci&oacute;n:</strong>
													<strong id="Partidadenominacion" style="color: black"></strong>
													<input style="margin-left: 30px" type="button"
													id="confirmarPartida" value="Confirmar"> </span>
											</div>
										</div>
									</div>
								</fieldset>
							</td>
						
						</tr>

						<tr>
							<td>
								<div id="cuerpoCategoriaPartida">
									<fieldset>
										<legend >
											<b>Partidas que ceder&aacute;n monto </b>
										</legend>
										<table class="tablaalertas" border="1">
											<tbody id="tablaCategoriaPartida">
												<tr>
													<th class="header"><span class="normalNegroNegrita">Tipo</span>
													</th>
														<th class="header"><span class="normalNegroNegrita">Proy/Acc</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Nombre
															proy/acc</span></th>
												
													<th class="header"><span class="normalNegroNegrita">Nombre
															Acci&oacute;n esp.</span></th>
													<th class="header"><span class="normalNegroNegrita">Partida</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Denominaci&oacute;n</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Disponibilidad</span>
													</th>
														<th class="header"><span class="normalNegroNegrita">Monto a ceder</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Opciones</span>
													</th>
												</tr>
											</tbody>
										</table>

									</fieldset>	
										<br />
								</div>	
						
								<div id="cuerpoCategoriaPartidaReceptora">

									<fieldset>
										<legend >
											<b>Partidas a acreditarle monto</b>
										</legend>
										<br />
										<table class="tablaalertas" border="1">
											<tbody id="tablaCategoriaPartidaReceptora">
												<tr>
													<th class="header"><span class="normalNegroNegrita">Tipo</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Proy/Acc</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Nombre
															proy/acc</span></th>
													
													<th class="header"><span class="normalNegroNegrita">Nombre
															Acci&oacute;n esp.</span></th>
													<th class="header"><span class="normalNegroNegrita">Partida</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Denominaci&oacute;n</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Disponibilidad</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Monto a recibir</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Eliminar
															partida</span>
													</th>
												</tr>
											</tbody>
										</table>

									</fieldset>
										<br />
								</div>
						
								<div id="cuerpoCategoriaPartidaCredito">
								
									<fieldset>
										<legend >
											<b>Partidas a acreditarle monto</b>
										</legend>
										<br />
										<table class="tablaalertas" border="1">
											<tbody id="tablaCategoriaPartidaCredito">
												<tr>
													<th class="header"><span class="normalNegroNegrita">Tipo</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Proy/Acc</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Nombre
															proy/acc</span></th>
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
													<th class="header"><span class="normalNegroNegrita">Opciones
															</span>
													</th>
												</tr>
											</tbody>
										</table>

									</fieldset>
										<br />
								</div>
							
								<div id="cuerpoCategoriaPartidaDisminuir">
									<fieldset>
										<legend >
											<b>Partidas a disminuir monto</b>
										</legend>
										<br />
										<table class="tablaalertas" border="1">
											<tbody id="tablaCategoriaPartidaDisminuir">
												<tr>
													<th class="header"><span class="normalNegroNegrita">Tipo</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Proy/Acc</span>
													</th>
													<th class="header"><span class="normalNegroNegrita">Nombre
															proy/acc</span></th>
													
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
													<th class="header"><span class="normalNegroNegrita">Opciones</span>
													</th>
												</tr>
											</tbody>
										</table>

									</fieldset>
										<br />
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<fieldset id='fieldset2' style="width: 600px;">
									<legend>
										<b>Total a solicitar</b>
									</legend>

									<table style="width: 350px;">

										<tr >
											<td align="left" class="normal"><strong
												style="margin-right: 10px;"> <span id="nombreAccion">Monto a ceder:</span> </strong>
											</td>
											<td align="left" class="normal"><strong
												style="font-size: 12px; color: black" id="montoTotal">0,00</strong>
												<input type="hidden" name="montoTotalHidden" id="montoTotalHidden" value="0"> 
											</td>

										</tr>

										
										<tr >
											<td align="left"  class="normal" ><strong
												style="margin-right: 10px;">Monto a recibir: </strong>
											</td>
											<td align="left" class="normal"><strong
												style="font-size: 12px; color: black" id="totalDisp">0,00</strong>

											</td>

										</tr>
									</table>


						</fieldset>
						</div>
						</td>
						</tr>
	                    		
					  <tr>
						 <td>
						 <br/>
						 <fieldset style="width: 400px;">
							<legend ><b>Respaldos digitales</b></legend>
							
								<table > 
								 <tr id="trActualesDigital">     	     
                              	        	<th class="normalNegroNegrita" style="color:#585858" align="left"> * Actuales</th>	
                              	    
                              	    </tr>
                              	 <tbody id="tbodyRespDigitales" >
                              	  
                                </tbody>
                                 <tr>
                              	        	<th class="normalNegroNegrita" align="left" style="color:#585858;"> * Por subir</th>	
                              	    
                              	    
                              	    </tr>
                              	   
                                 <tr>
                              	      <td>
                              	<div align="left" class="normal">
								<strong style="margin-right: 136px;">Seleccione los archivos (m&aacute;ximo 5): </strong>
								<br/><br/>
						     <input id="file_upload" name="file_upload" type="file" multiple="true">
		                     </div>
		                     
                              	      </td>
                              	    
                              	 </tr>
                                </table>
							
							</fieldset>
						 </td>
						</tr>
						   <tr>
						 <td>
						 <br/>
						 <fieldset style="width: 400px;">
							<legend ><b>Respaldos f&iacute;sicos</b></legend>

								<table > 
								 <tr id="trActualesFis">     	     
                              	        	<th class="normalNegroNegrita" style="color:#585858" align="left"> * Actuales</th>	
                              	    
                              	    </tr>
                              	 <tbody id="tbodyRespFisico" >
                              	  
                                </tbody>
                                 <tr>
                              	        	<th class="normalNegroNegrita" align="left" style="color:#585858;"> * Por subir</th>	
                              	    
                              	    
                              	    </tr>
                              	   
                                 <tr>
                              	      <td>
							
							 <div align="left" class="normal">
								<strong style="margin-right:20px;">Respaldo: </strong>
						     <input id="pctaRespaldoFisico" name="pctaRespaldoFisico" type="text" >
						     <b class="links"  id="AgregarRespaldoFisico" > Agregar</b>
						        <div id="cuerpoRespaldoFisico" >
                                <br/>
                                
                                <table background="../../imagenes/fondo_tabla.gif"
				                    class="tablaalertas" border="1"  style="width:300px;" >
                                <tbody id="tablaBodyRespaldoFisico" >
                                  <tr>
										<th class="header"><span class="normalNegroNegrita">Nombre</span></th>
                						<th class="header"><span class="normalNegroNegrita">Eliminar</span></th>									
									</tr>
                                </tbody>
								</table>
                              
                                
                                </div>     
						    
		                     </div>
		                     
		                     </td>
		                     </tr>
		                     </table>
							</fieldset>
							<br/><br/>
						 </td>
						</tr>
						
					</table>

				</td>
			</tr>
			

					</table> <br /> <br />
		<div align="center" id="accionesEjecutar">
								
		<?php 
		
		
		foreach ($GLOBALS['SafiRequestVars']['opciones'] as $index){ ?>
	    <span class="cadena">
		<input  type="button" value="<?php echo $index['wfop_nombre'];?>"   id="<?php echo $index['wfop_descrip'];?>" onclick="GenerarSigienteCadena(<?php echo $index['id_cadena_hijo'];?>,0)"  />
	    </span>
		<?php }?>
		
		 <input type="reset" value="Limpiar" id="reset" name="reset" />

					</div>
					
	  <div align="center" id="opcionModificar">
	  
	  </div>
	
		  <input type="hidden" value=""  name="regisFisDigiEli" id="regisFisDigiEli" />
          <input type="hidden" value=""  name="regisNombreDigital" id="regisNombreDigital" />
	
	</form>

</body>
</html>
