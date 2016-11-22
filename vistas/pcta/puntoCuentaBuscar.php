include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
?>
<html>
<head>
<title>.:SAFI:. Punto de cuenta</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.pack.js"></script>
<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />
	
<link href="<?=GetConfig("siteURL").'/css/plantilla.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />
	
<link href="<?=GetConfig("siteURL").'/css/safi0.2.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />
	
<?php require(SAFI_JAVASCRIPT_PATH.'/init.php'); ?>

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
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/func_montletra.js';?>"
	charset="utf-8"></script>
	
	<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/uploadify/uploadify/jquery.uploadify.min.js';?>"
	charset="utf-8"></script>

	<link type="text/css" href="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib//uploadify/uploadify/uploadify.css';?>"
	media="screen" rel="stylesheet" />
	
	<script language="javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/func_montletra.jss';?>"></script>
	
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
<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />
	
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/pcta/pcuenta.js';?>"
	charset="utf-8"></script>	
	
	<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/DetalleCompletoDocumento.js';?>"
	charset="utf-8"></script>
	
<link
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/elrte.min.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />
 <script type="text/javascript" charset="utf-8">
      dependencia = <?php echo $id_depe;?>;
 </script>		


</head>
<body>

<?php include(SAFI_VISTA_PATH . '/detalleCompletoDocumeto.php');
      include(SAFI_VISTA_PATH . '/mensajes.php');
     $params = $GLOBALS['SafiRequestVars']['puntoCuentaFiltroData'];
?>

	<form name="formPctaFiltro" id="formPctaFiltro" method="post" action="">
		<table width="640" align="center"  background="../../imagenes/fondo_tabla.gif"class="tablaalertas">
  <tr>
  <th class="header" colspan='2' ><span class="normalNegroNegrita">.:Buscar puntos de cuenta :.</span></th> 
  </tr>
          <tr>
				<td width="175" height="29" class="normalNegrita" align="left"><span class="normalNegrita"> A&ntilde;o Presupuestario: </span></td>
				<td>
				<select class="normalNegrita" id="agnoPcta" name="agnoPcta">					
					<?php
                    $fecha = new DateTime();
					$valueInic = 7;
					$agnoFin = $fecha->format('Y');
					$agnoInic = 2007;
			
					while ($agnoInic <= $agnoFin){?>
						
					<option <?php

					
					
					if($agnoInic == $params['agnoPcta'])
					              { 
						            echo 'selected'; $selected = true; 
						            
					               }else{
					               	
					               	  if($selected == false && $agnoInic == $agnoFin){

					               	   echo 'selected';
					               	   
					               	  }} ?> 
					               	  
					               	  
					               	   value="<?php echo $agnoInic ?>"  ><?php echo $agnoInic ?></option>	

					<?$valueInic++;$agnoInic++;}?>

				</select>
				</td>
			</tr>
			<tr>
				<td width="175" height="29" class="normalNegrita" align="left">Solicitados
					entre:</td>
				<td><input type="text" size="10" id="txt_inicio" name="txt_inicio"
					class="dateparse" onfocus="javascript: comparar_fechas(this);"
					readonly="readonly" value="<?php echo $params['txt_inicio']; ?>" /> <a href="javascript:void(0);"
					onclick="g_Calendar.show(event, 'txt_inicio');"
					title="Show popup calendar"> <img
						src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"
						alt="Open popup calendar" /> </a> <input type="text" size="10"
					id="hid_hasta_itin" name="hid_hasta_itin" value="<?php echo $params['hid_hasta_itin']; ?>" class="dateparse"
					onfocus="javascript: comparar_fechas(this);" readonly="readonly"
					value="" /> <a href="javascript:void(0);"
					onclick="g_Calendar.show(event, 'hid_hasta_itin');"
					title="Show popup calendar"> <img
						src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"
						alt="Open popup calendar" /> </a>
				</td>
			</tr>
			<tr id="pctaBuscarTr2" >
				<td class="normalNegrita" align="left">C&oacute;digo del documento:</td>
				<td><span class="normalNegrita"><input name="codigPctaBusqueda" id="codigPctaBusqueda" type="text"
						class="normalNegro" size="12" value="<?php echo $params['codigPctaBusqueda']; ?>"/></span>
				</td>
			</tr>
			<tr id="pctaBuscarTr3">
	<td class="normalNegrita" align="left">Nro. Compromiso:</td>
	<td><span class="normalNegrita"><input name="ncompromiso" type="text" class="normalNegro" id="ncompromiso"  size="25" value="<?php echo $params['ncompromiso']; ?>"/>
	</td>
   </tr>

  <tr id="pctaBuscarTr4">
	<td class="normalNegrita" align="left">Asunto del punto cuenta:</td>
	<td>

	
 <select name="pctaAsunto" id="pctaAsunto" class="normalNegrita">
									<option selected value="">.: Todos :.</option>
									<?php
									foreach ( $GLOBALS['SafiRequestVars']['PctaAsusnto'] as  $var){ ?>

									<option <?php echo $params['pctaAsunto'] == $var->GetId()? 'selected': ''  ?> value="<?php echo $var->GetId(); ?>">
									<?php echo $var->GetNombre();?>
									</option>


									<?php } ?>

							</select>

	</td>
	</tr>
	<tr id="pctaBuscarTr5">
		<td  class="normalNegrita"  align="left">Partida:</td>
	<td><span class="normalNegrita">
	<input type="text" name="PartidaBusqueda" id="PartidaBusqueda" class="normalNegro" value="<?php echo $params['PartidaBusqueda']; ?>"/></span></td>
  </tr>
   <tr id="pctaBuscarTr6">
	<td class="normalNegrita" align="left">Proyecto/Acc espec&iacute;fica:</td>
	<td>
	 <input name="pctaProyAcc" type="text" class="normalNegro" id="pctaProyAcc" value="<?php echo $params['pctaProyAcc']; ?>" />
	  <input name="pctaProyAccVal" type="hidden" id="pctaProyAccVal" value="<?php echo $params['pctaProyAccVal']; ?>"/></span></td>

   </tr>
  <tr id="pctaBuscarTr7">
	<td class="normalNegrita" align="left">Palabra Clave:</td>
	<td><span class="normalNegrita"><input name="palabraClave" type="text" class="normalNegro" id="palabraClave" value="<?php echo $params['palabraClave']; ?>"/>
	</span><span class="normal">(Incluida en la descripci&oacute;n del punto de cuenta)</span></td>
  </tr>
  <tr id="pctaBuscarTr8">
  	<td class="normalNegrita" align="left">Dependencia:</td> 
	<td>

	<select class="normalNegrita" id="DependenciaPcta" name="DependenciaPcta">
		<option value="" selected="selected">.:Todos:.</option>
		<?php
		
		if($GLOBALS['SafiRequestVars']['DependenciaPcta']){
			foreach ( $GLOBALS['SafiRequestVars']['DependenciaPcta'] as $obj){ ?>
	
			<option <?php echo $params['DependenciaPcta'] == $obj->GetId()? 'selected': ''  ?>  value="<? echo $obj->GetId(); ?>"> <? echo $obj->GetNombre(); ?></option>
				
		<?php } }?>

    </select>  
	</td>
  </tr>
  
  <tr id="pctaBuscarTr9">
  	<td class="normalNegrita" align="left">Estatus:</td> 
	<td>

	<select class="normalNegrita" id="estatusPcta" name="estatusPcta">
		<option value="" selected="selected">.:Todos:.</option>
		<?php
		
		if($GLOBALS['SafiRequestVars']['EstadoPcta']){
			foreach ( $GLOBALS['SafiRequestVars']['EstadoPcta'] as  $var){ ?>
	
			<option <?php echo $params['estatusPcta'] == $var['esta_id']? 'selected': ''  ?> value="<? echo $var['esta_id'] ?>"> <? echo $var['esta_nombre']?></option>
				
			<?php } }?>

    </select>  
	</td>
	
	
	
  </tr>
  
  
  <tr>
  <td colspan="2" align="center">
  <br/>
  <input  type="button" value="Buscar"   id="BuscarPcta"  />
  <input type="reset" value="Limpiar" id="reset" name="reset" />
  
  </td>
  
  </tr>
		
		</table>
		
		  
	</form>

		<table style="width: 100%;">

		<tr>
			<td><table cellpadding="0" cellspacing="0" align="center"
					style="width: 100%; " background="../../imagenes/fondo_tabla.gif"
					class="tablaalertas" id="tablaBusqueda">
					 <tbody id="tablaFiltro" >
					<tr>
						<th class="header"><span class="normalNegroNegrita">#</span>
						</th>
						</th>
						<th class="header"><span class="normalNegroNegrita">C&oacute;digo del documento</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Asunto</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Dependencia</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Estatus</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Descripci&oacute;n</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Monto</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Fecha solicitud</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Monto disponible</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Instancia actual</span>
						</th>
						
						<?php 
			if($_SESSION['user_perfil_id'] == PERFIL_PRESIDENTE){?>
			
						<th class="header"><span class="normalNegroNegrita">Devolver</span>
						</th>
			<?php } ?>			
			<?php 
			if($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO){?>	
			
			<th class="header"><span class="normalNegroNegrita">Liberar</span>
						</th>		
						
				<?php } ?>		
						
                   </tr>
                   <?php 
                   
                   $num = 1;
                    $tdClass ='even';
                   
                   if($GLOBALS['SafiRequestVars']['puntoCuentaFiltro']){?>

                 <?php  foreach($GLOBALS['SafiRequestVars']['puntoCuentaFiltro'] as $index => $valor){
                   	$tdClass = ($tdClass == "even") ? "odd" : "even";
                   	?>
                   	
                   	
                   	
                   	<tr class="<?php echo $tdClass;?>" onclick="Registroclikeado(this)">
                       <td valign ="top"> <?php echo $num; ?> </td>
                       <td valign ="top"> <a href="#dialog" docgId="<?php echo $index; ?>" class="detalleOpcion" opcion="null">  <?php echo $index; ?> </a> </td>
                        <td valign ="top"> <?php echo $valor->GetAsunto() != null? $valor->GetAsunto()->GetNombre() : ''; ?> </td>
                        <td valign ="top"> <?php echo $valor->GetDependencia()!= null?$valor->GetDependencia()->GetNombre() : ''; ?> </td>
                        <td valign ="top"> <?php echo $valor->GetEstatus() != null?$valor->GetEstatus()->GetNombre() : '' ; ?> </td>
                        <td valign ="top" class="descripFiltro" style="color:black"> <?php echo $valor->GetDescripcion()  != null? $valor->GetDescripcion() : ''; ?> </td>
                        
                        
                        <td valign ="top" > <?php echo $valor->GetMontoSolicitado() != null? number_format($valor->GetMontoSolicitado(),2,',','.') : '' ?> </td>
                       
                        <td valign ="top"> <?php echo $valor->GetFecha(); ?> </td>
                        
                        <td valign ="top" class="montos" ><?php echo   $GLOBALS['SafiRequestVars']['disponiblePcta'][$index] != null ?
                         $valor->GetAsunto()!= null?
                           $valor->GetAsunto()->GetId() == '013'?
                            '-' :   number_format($GLOBALS['SafiRequestVars']['disponiblePcta'][$index],2,',','.')       : '0,00'   : '0,00'; ?></td>
                       
                        <td valign ="top"><?php echo $GLOBALS['SafiRequestVars']['instActual'][$index]!= false? $GLOBALS['SafiRequestVars']['instActual'][$index] : '-'?></td>
 
						<?php
						if($valor->GetEstatus() != null && $valor->GetEstatus()->GetId() == 13 && $_SESSION['user_perfil_id'] == PERFIL_PRESIDENTE){ ?>
						
						<td valign ="top" > 
                            <?php if(!isset($GLOBALS['SafiRequestVars']['pcuantaDevolver'][$index])){?>
						
						Tiene compromisos asociados.
						
						<?php }else{ ?>
						
                             <a href="javascript:void(0);" onclick="DevolverAprobado(this);" ><?php echo $index; ?></a>

						</td>

						<?php } ?>
						<?php }else{
							if($_SESSION['user_perfil_id'] == PERFIL_PRESIDENTE){
							?>
						
						
						<td valign ="top" > - </td>
						
						
                   </tr>

                   <?php }

						if($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO && $GLOBALS['SafiRequestVars']['pctaALiberar'][$index]){
							?>
						
						
						<td valign ="top" >
						
						   <a href="javascript:void(0);" onclick="LiberarPcuenta(this);" ><?php echo $index ?></a>
						
						</td>
	
                   </tr>

                   <?php }else{  
                   
                   if($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO){
							?>
						
						
						<td valign ="top" > - </td>
						
						
                   </tr>

                   <?php }} }
                 
                 
                 
                 
                 
                $num++;}}else{ ?>
                   <tr class="odd">
						<td colspan='12'>No se han encontrado registros</td>
					    </tr>
					   <?php }?>
                     </tbody>
				
				</table>
			</td>
		</tr>
	</table>


	<div id="dialogisss" title="">
		<form name="formLiberacion" id="formLiberacion" method="post" action="">
			<div class="normalNegroNegrita" align="center">
				<span id='detalleLiberacion'></span>
			</div>
			<br />
			<table cellpadding="0" cellspacing="0" align="center"
				style="width: 100%;" background="../../imagenes/fondo_tabla.gif"
				class="tablaalertas">
				<tr id='detalleDisponTr1'>
					<th class="header"><span class="normalNegroNegrita">Partida 
					
					</th>
					<th class="header"><span class="normalNegroNegrita">Monto
							Disponible. 
					</th>

				</tr>
				<tbody id="tablaLiberacion">
				</tbody>
				<tr>
				<td class="header"><span class="normalNegroNegrita">Monto total a liberar</span> </td>
				<td class="header"><span class="normalNegroNegrita" id="totalMontoDisponible" ></span></td>
				</tr>
			</table>
			 <br/>
			 
		 <fieldset >
			<legend ><b>Justificaci&oacute;n</b></legend>
			<textarea rows="5" name="justificacionLiberacion" id="justificacionLiberacion" cols="40"><?php echo $lapsor ?></textarea>
			</fieldset>
			<br/>
			<div align='center'>
			<input type="hidden" id="pctaLiberarId" value=""/> 
				<input type="button" value="Enviar" onclick='RevisarLiberacion()' 
					name="LiberarPcta" /> <input type="reset" value="Limpiar"
					id="reset" name="reset" />
			</div>
		</form>
	</div>
</body>
 </html>    
