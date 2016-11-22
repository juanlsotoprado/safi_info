<?php
include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');

$objPcta =  $GLOBALS['SafiRequestVars']['puntosCuenta'];
 
if($objPcta){
  $objPcta->UTF8Encode();
   $objPctaArray = $objPcta->ToArray();
}


 if($objPcta){
	
 //	error_log(print_r($objPcta,true));

 	$pcta = $objPcta  != null? utf8_decode($objPcta->GetId())  : '';
 	$fecha = $objPcta  != null? utf8_decode($objPcta->GetFecha())  : '';
    $pctaAsociado = $objPcta->GetPuntoCuentaAsociado()  != null? utf8_decode($objPcta->GetPuntoCuentaAsociado()->GetId()) : '';
 	$preparadoPara = $cargodestinatario2 != null? utf8_decode($cargodestinatario2) : '';
 	$destinatario = $objPcta  != null? utf8_decode($objPcta->GetDestinatario())  : '';
 	$elaboradoPor = $_SESSION['solicitante'];
    $solicitadoPor = $objPcta->GetRemitente() != null? utf8_decode($objPcta->GetRemitente()->GetId()): '';
 	$presentadoPor = $objPcta->GetPresentadoPor() != null? utf8_decode($objPcta->GetPresentadoPor()->GetId()) : '';
 	$dependencia = $objPcta->GetDependencia() != null? utf8_decode($objPcta->GetDependencia()->GetId()) : '';
    $asunto = $objPcta->GetAsunto() != null? utf8_decode($objPcta->GetAsunto()->GetId()) : '';
    $descripcion = $objPcta != null? $objPcta->GetDescripcion() : '';
    $justificacion = $objPcta != null? utf8_decode($objPcta->GetJustificacion()) : '';

 	$lapsor = $objPcta != null? utf8_decode($objPcta->GetLapso()) : '';
    $recursos = $objPcta != null? utf8_decode($objPcta->GetRecursos()) : '';
 	$garantia = $objPcta != null? utf8_decode($objPcta->GetGarantia())  : '';
    $proveedorSugerido = $objPcta != null? utf8_decode($objPcta->GetRifProveedorSugerido()) : '';
    $cadena = $proveedorSugerido;
    $caracter = ":";
             
    if (strpos($cadena, $caracter) !== false){
    $proveedorSugerido2 = explode(":",$proveedorSugerido);
    $proveedorSugeridoVal = utf8_decode($proveedorSugerido2[0]);
    }else{
    	
    	$proveedorSugeridoVal = '';
    }
    
 	$condicionPago =  $objPcta != null? utf8_decode($objPcta->GetCondicionPago()) : '';
 	$observacion = $objPcta  != null? utf8_decode($objPcta->GetObservacion())  : '';
 	$montoSolicitado =  $objPcta != null? utf8_decode($objPcta->GetMontoSolicitado())  : 0;
    $puntoCuentaImputa = $objPctaArray != null ? $objPctaArray['puntoCuentaImputa']  : null;
    $puntoCuentaRespaldo = $objPctaArray != null ? $objPctaArray['puntoCuentaRespaldo']  : null;
	
 }
 
?>
<html>
<head>
<title>.:SAFI:. Punto de cuenta</title>
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

	<link type="text/css" href="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib//uploadify/uploadify/uploadify.css';?>"
	media="screen" rel="stylesheet" />

	
		<link type="text/css" href="<?=SAFI_URL_JAVASCRIPT_PATH.'lib/calendarPopup/css/calpopup.css';?>"
	media="screen" rel="stylesheet" />
	
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
	
	
<script language="javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/func_montletra.jss';?>"></script>

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/pcta/pcuenta.js';?>"
	charset="utf-8"></script>	
	
 <script type="text/javascript" charset="utf-8">
      dependencia = <?php echo $id_depe;?>;
       PHPSESSID = '<?php echo $_COOKIE['PHPSESSID'];?>';
       
 </script>		
  
</head>
<body>

	  <script type="text/javascript" charset="utf-8">
      pctaModRecursos =<?php echo $recursos;?>;
	  puntoCuentaImputa = <?php echo json_encode($puntoCuentaImputa,JSON_FORCE_OBJECT); ?>;
	  puntoCuentaRespaldo = <?php echo json_encode($puntoCuentaRespaldo,JSON_FORCE_OBJECT); ?>;
	  pctaAsociado = "<?php echo $pctaAsociado;?>";

     </script>		
     
	<form name="formPcta" id="formPcta" method="post" action="">
		<table cellpadding="0" cellspacing="0" align="center"
				style="width: 100%;" background="../../imagenes/fondo_tabla.gif"
				class="tablaalertas">
			<tr>
				<th class="header"><span class="normalNegroNegrita"<?php echo $pcta?> >.: Punto de
							Cuenta :.<span id="modificarPcta"><?php echo $pcta?></span></span>
			</tr>
			<tr>
				<td>
					<table>
						<tr>
							<td style="width: 200px;" class="normalNegrita">Fecha:</td>
							<td  ><input type="text" size="10" id="fecha" name="fecha"
								class="dateparse" readonly="readonly"
								value="<?php echo $fecha?>"
								
								
								 /> <a
								href="javascript:void(0);"
								onclick="g_Calendar.show(event, 'fecha');"
								title="Show popup calendar"> <img
									src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif"
									class="cp_img" alt="Open popup calendar" /> </a></td>
						</tr>
						<tr>
							<td><div align="left" class="normal">
									<b>Elaborado por:</b>
								</div></td>
							<td class="normal" ><?php echo $_SESSION['solicitante']; ?></td>
						</tr>
						<tr>
							<td class="normalNegrita">Preparado para:</td>
							<td><select id="preparado_para" name="preparado_para" class="normalNegrita">
									<option selected value="">.:.Seleccione.:.</option>
							<?php foreach($GLOBALS['SafiRequestVars']['preparado_para'] as $data){ ?>

									<option  <?php echo $data['id'] == $destinatario ? 'selected': ''  ?> value="<?php echo $data['id']; ?>"> 
						        	<?php echo $data['nombre']; ?>
									</option>

									<?php	} ?>
							</select></td>
						</tr>
						<tr>
							<td class="normalNegrita">Solicitado por:</td>
							<td><select name="SolicitadoPor" id="SolicitadoPor"
								class="normalNegrita">
									<option selected value="">.:.Seleccione.:.</option>
									<option selected value="">Direcci&oacute;n de Gesti&oacute;n Administrativa y Financiera</option>
									<?php foreach ($GLOBALS['SafiRequestVars']['preparadoPara'] as $empleados){ ?>

									<option 
									
										<?php echo $empleados['ci'] == $solicitadoPor? 'selected': ''; ?>
									
									value="<?php echo $empleados['ci'] ?>">
									<?php echo $empleados['nombres']." ".$empleados['apellidos']; ?>
									</option>

									<?php 	} ?>
							</select></td>

						</tr>
						<tr>
							<td class="normalNegrita">Presentado por:</td>
							<td><select name="presentado_por" id="presentado_por" class="normalNegrita">

							<?php foreach ($GLOBALS['SafiRequestVars']['presentadoPor'] as $empleados){ ?>

									<option <?php echo $empleados['ci'] == $presentadoPor? 'selected': ''  ?> value="<?php echo $empleados['ci'] ?>">
									<?php echo $empleados['nombres']." ".$empleados['apellidos']; ?>
									</option>

									<?php 	} ?>
							</select></td>
						</tr>
						<tr>
							<td class="normalNegrita">Dependencia que tramita:</td>
							<td><select name="DependenciaTramita" id="DependenciaTramita"
								class="normalNegrita">

								<?php if($GLOBALS['SafiRequestVars']['DependenciaQueTramita'] = $DependenciaQueTramita){ ?>

									<option <?php echo $DependenciaQueTramita->GetId() == $dependencia? 'selected': ''  ?>  value="<?php echo  $DependenciaQueTramita->GetId(); ?>">
									<?php echo  $DependenciaQueTramita->GetNombre(); ?>
									</option>

									<?php 	} ?>
							</select></td>
						</tr>
						<tr id="DependenciaSolicitantetr">
							<td class="normalNegrita">Dependencia solicitante:</td>
							<td><select name="DependenciaSolicitante" id="DependenciaSolicitante"
								class="normalNegrita">

									<option selected value=""></option>

							</select></td>
						</tr>

						<tr>
							<td class="normalNegrita">Asunto:</td>
							<td><select name="pctaAsunto" id="pctaAsunto"
								class="normalNegrita">
									<option selected value="">.:.Seleccione.:.</option>
									<?php
									foreach ( $GLOBALS['SafiRequestVars']['PctaAsusnto'] as  $var){ ?>

									<option 
								
									
									<?php 
									 if($objPcta){
									
									if($asunto == '013'){

									
									echo $var->GetId() == $asunto? 'selected': 'disabled = "disabled"';
										
									
									}else{
										
									echo $var->GetId() == $asunto? 'selected': '';

									if($var->GetId() == '013'){ echo 'disabled = "disabled"'; } 
									
									
									}}
									
									
									?> 

									
									value="<?php echo $var->GetId(); ?>">
									<?php echo $var->GetNombre();?>
									</option>


									<?php } ?>

							</select></td>
						</tr>
						<tr id="trPctaAsociado">
							<td class="normalNegrita">Punto de cuenta asociado:</td>
							<td>
						<?php 	if($GLOBALS['SafiRequestVars']['pctaAsociado']){ ?>
							<select name="pctaAsociado" id="pctaAsociado"
								class="normalNegrita">
								<option selected value="">.:.Seleccione.:.</option>
							  <?php
							  
							        if($GLOBALS['SafiRequestVars']['pctaAsociado']){
									foreach ($GLOBALS['SafiRequestVars']['pctaAsociado'] as $index => $var){ ?>

									<option 
									
									<?php if($objPcta){
									
									
									 echo $index == $pctaAsociado? 'selected': 'disabled = "disabled"';
									
									
									
									 }
									
									?>
									
									
									value="<?php echo $index; ?>">
									<?php echo $index;?>
									</option>


							   <?php }} ?>
							
							</select>
							   
							
							   <?php }else{ ?>

							 <span class="normal" >No hay puntos de cuenta aprobados.</span>
							 
							 
							     <?php } ?>
							</td>
						</tr>
						<tr>

							<td class="normalNegrita">Descripci&oacute;n:</td>
							<td><span id="spanDescrip" class="normalNegrita"> <b class="links">Mostrar editor</b><img
									id="imgDescrip" src="../../imagenes/triangulonegro1.jpg"
									style="width: 10px; heigth: 10px; margin-left: 5px;" /> <img
									id="imgDescrip2" src="../../imagenes/triangulonegro2.jpg"
									style="width: 10px; heigth: 10px; margin-left: 5px;" /> </span>
 
								<br /> <br /> <input type="hidden" value="" id="spanDescripval" />
                                
								<div id="pcuenta_descripcion2">
                                     
									<div id="pcuenta_descripcion" class="pcuenta_descripcion"><?php echo utf8_decode($descripcion) ?></div>
									<input type="hidden" name="pcuenta_descripcionVal" id="pcuenta_descripcionVal" value="">
							</div>
							</td>
							</div>
						</tr>
						<tr>
							<td class="normalNegrita">Justificaci&oacute;n:</td>
							<td><textarea rows="3" id="justificacion" name="justificacion" cols="50"><?php echo $justificacion ?></textarea>
							</td>
						</tr>
						<tr>
							<td class="normalNegrita">Lapso de convenio/contrato:</td>
							<td><textarea rows="2" name="convenio" id="convenio" cols="50"><?php echo $lapsor ?></textarea></td>
						</tr>
						<tr>
							<td class="normalNegrita">Garant&iacute;a:</td>
							<td><textarea rows="2" name="garantia" id="garantia" cols="50"><?php echo $garantia ?></textarea></td>
						</tr>
						<tr>
							<td class="normalNegrita">Proveedor sugerido:</td>
							<td><input type="text" name="ProveedorSugerido"
								id="ProveedorSugerido" class="normalNegro" size="68" value="<?php echo $proveedorSugerido ?>" />
								<input type="hidden" name="ProveedorSugeridoval" 
								id="ProveedorSugeridoValor" value="<?php echo $proveedorSugeridoVal ?>"></td>
						</tr>
						<tr>
							<td><div align="left" class="normal">
									<strong>Observaciones:</strong>
								</div></td>
							<td><textarea rows="3" name="observaciones"  id="observaciones" cols="50"><?php echo $observacion ?></textarea>
							</td>
						</tr>
						<tr>
							<td class="normalNegrita">Requiere recursos monetarios:</td>
							<td class="normal"><input type="radio" name="op_recursos"
								value="1" id="op_recursosSi" checked="true"  >SI
								<input type="radio" name="op_recursos" value="0"
								id="op_recursosNo">NO</td>
						</tr>
						<tr id="condPagotr">
							<td><div align="left" class="normal">
									<strong>Condiciones de pago:</strong>
								</div></td>
							<td><textarea rows="2" name="cond_pago" id="cond_pago" cols="50"><?php echo $condicionPago ?></textarea></td>
						</tr>

					</table>
					
					 <table>
						
							<tr>
						<td colspan="2">
						<br/>




						<fieldset id="filsed1">
							<legend ><b>Categor&iacute;a program&aacute;tica/Partida</b></legend>
							
							
							<div align="left" class="normal">
								<strong style="margin-right: 130px;">Proy/Acc:</strong>
								<select name="selectProyAcc" class="normalNegrita" id="selectProyAcc">
									<option selected value="">.:.Seleccione.:.</option>
									
									
									<optgroup label="Proyectos">
									<?php foreach ( $GLOBALS['SafiRequestVars']['proyectos'] as $proyectos ){ ?>
									
                                        <option value="<?php echo $proyectos['proy_id']; ?>" ><?php echo $proyectos['proy_titulo']?></option>
                                        
                                     <?php }?>
                                    </optgroup>
                                    
                                    <optgroup label="Acc." >
                                       <?php foreach ( $GLOBALS['SafiRequestVars']['acc'] as $acc){ ?>
                                       
                                        <option value="<?php echo $acc['acce_id']; ?>" ><?php echo $acc['acce_denom']; ?></option>
                                        
                                     <?php }?>
                                    </optgroup>
									
									
							     </select>
							     
							     </br></br>
							     <div id="trcategoriaesp">
							<div align="left" class="normal">
								<strong style="margin-right: 103px;">Ac. Espec&iacute;fica:</strong>
								<input type="text" name="Categoria" id="Categoria" class="normalNegro" size="68" value="" />
								
						     </div>
						     </div>
							<div id="tablacategoria">
							 
							<table class="tablaalertas" border="1" >
							       
									<tr>
										<th class="header"><span class="normalNegroNegrita">Tipo</span></th>
										<th class="header"><span class="normalNegroNegrita">Nombre Proy/Acc</span></th>
										<th class="header"><span class="normalNegroNegrita">Proy/Acc</th>
										<th class="header"><span class="normalNegroNegrita">Nombre Acci&oacute;n esp.</span></th>
									</tr>
									<tr  >
										<td id="c1"  class="normal"></td>
										<td id="c3"   class="normal"></td>
										<td id="c2"  class="normal"></td>
										<td id="c4"   class="normal"></td>
									</tr>
								</table>
							
							</div>

                            <div id="divPartida" >
                            <br/>
                             <hr width="100%" height="1px">
                            <div align="left" class="normal">
								<strong style="margin-right: 136px;">Partida :</strong>
								<input type="text" name="Partida" id="Partida" class="normalNegro" size="20" value="" />
								<br/><br/>
								<span class="patidaseleccionada">
								<strong style="margin-right: 96px;">Denominaci&oacute;n :</strong>
								<strong  id="Partidadenominacion" style="color:black" ></strong>
								
								<input style="margin-left:30px" type="button"  id="confirmarPartida" value="Confirmar">
								</span>
								</div>
								</div>
                                <div id="cuerpoCategoriaPartida">
                                <br/>
                                
                                <table class="tablaalertas" border="1"  >
                                <tbody id="tablaCategoriaPartida">
                                  <tr>
										<th class="header"><span class="normalNegroNegrita">Tipo</span></th>	
										<th class="header"><span class="normalNegroNegrita">Nombre proy/acc</span></th>
										<th class="header"><span class="normalNegroNegrita">Proy/Acc</th>
										<th class="header"><span class="normalNegroNegrita">Nombre Acci&oacute;n esp.</span></th>	
										<th class="header"><span class="normalNegroNegrita">Partida</span></th>	
										<th class="header"><span class="normalNegroNegrita">Denominaci&oacute;n</span></th>
										<th class="header"><span class="normalNegroNegrita">Disponibilidad</span></th>
										<th class="header"><span class="normalNegroNegrita">Monto</span></th>	
										<th class="header"><span class="normalNegroNegrita">Eliminar</span></th>									
									</tr>
                                </tbody>
								</table>
                              
                                
                                </div>
								
								</div>
						</fieldset>

								</td>
						</tr>
						<tr>
						 <td>
						 <br/>
						 <fieldset style="width: 400px;">
							<legend ><b>Total a solicitar</b></legend>
							 <div align="left" class="normal">
							<strong style="margin-right: 10px;">En Bs. :</strong>
							
						<strong style="font-size:12px; color:black" id="montoTotal" >0</strong>
							<input type="hidden" name="montoTotalHidden" id="montoTotalHidden" value="0"> 
							</div>
							<br/>
							
							<div align="left" class="normal">
							<strong style="margin-right: 10px;">En letras. :</strong>
							<strong style="font-size:12px; color:black" id="monto_letras" ></strong>
                             </div>
							</fieldset style="width: 400px;">
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
                              	 <tbody id="tbodyRespDigitalesPcuenta" >
                              	  
                                </tbody>
                                 <tr>
                              	        	<th class="normalNegroNegrita" align="left" style="color:#585858;"> * Por subir</th>	
                              	    
                              	    
                              	    </tr>
                              	   
                                 <tr>
                              	      <td>
                              	        <div align="left" class="normal">
								<strong style="margin-right: 136px;">Seleccione los archivos (m&aacute;ximo 5) : </strong>
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
                              	 <tbody id="tbodyRespFisicoPcuenta" >
                              	  
                                </tbody>
                                 <tr>
                              	        	<th class="normalNegroNegrita" align="left" style="color:#585858;"> * Por subir</th>	
                              	    
                              	    
                              	    </tr>
                              	   
                                 <tr>
                              	      <td>
							
							 <div align="left" class="normal">
								<strong style="margin-right:20px;">Respaldo  : </strong>
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
			
		</table>
		<div align="center" id="accionesEjecutar">
		<br/>
		
		<span id="opcionModificar">
		
		 
		<?php 
		
		foreach ($GLOBALS['SafiRequestVars']['opciones'] as $index){ ?>
	    <span class="cadena">
		<input  type="button" value="<?php echo $index['wfop_nombre'];?>"   id="<?php echo $index['wfop_descrip'];?>" onclick="GenerarSigienteCadena(<?php echo $index['id_cadena_hijo'];?>,0)"  />
	    </span>
		<?php }?>
		
		<input type="reset" value="Limpiar" id="reset" name="reset" />
		 
		</span>
		 
		
          <input type="hidden" value=""  name="regisFisDigiEli" id="regisFisDigiEli" />
          <input type="hidden" value=""  name="regisNombreDigital" id="regisNombreDigital" />
	     
		
		</div>
		
	</form>
</body>
</html>

