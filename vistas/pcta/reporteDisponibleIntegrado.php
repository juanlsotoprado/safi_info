<?php 
include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');

  
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

<link href="<?=GetConfig("siteURL").'/css/plantilla.css';?>"
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

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/pcta/pcuenta.js';?>"
	charset="utf-8"></script>	
	
 <script type="text/javascript" charset="utf-8">
      dependencia = <?php echo $id_depe;?>;
      
 </script>		


<?php require(SAFI_JAVASCRIPT_PATH.'/init.php'); ?>
<?php require(SAFI_INCLUDE_PATH.'/fechaJs.php'); ?>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/ui.min.js';?>"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/constantes.js';?>"></script>

<?php include(SAFI_VISTA_PATH . '/detalleCompletoDocumeto.php');
      include(SAFI_VISTA_PATH . '/mensajes.php');
     $params = $GLOBALS['SafiRequestVars']['puntoCuentaFiltroData'];		
      ?>
      
      <script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/DetalleCompletoDocumento.js';?>"
	charset="utf-8"></script>

</head>
<body>

<?php include(SAFI_VISTA_PATH . '/detalleCompletoDocumeto.php'); ?>
	<form name="formDisponibilidad" id="formDisponibilidad" method="post" action="">
		<table width="500"  align="center"  background="../../imagenes/fondo_tabla.gif"class="tablaalertas">
			<tr>
			<th class="header" colspan='2' ><span class="normalNegroNegrita">.:Disponibilidad de punto de cuenta:.</span></th>
			</tr>
			<tr>
				<td class="normalNegrita" align="left" style="width: 50%;" >C&oacute;digo del documento:</td>
				<td>
					<input name="pcta_id" class="normalNegro" type="text" id="codigPctaDis"  size="18"/>
					
				</td>
			</tr>
			
				<tr>
				<td class="normalNegrita" align="left" style="width: 50%;" >Proyecto/ Acci&oacute;n Centralizada:</td>
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
				<input name="pctaProyAcc" type="text" class="normalNegro" id="pctaProyAcc" value="<?php echo $params['pctaProyAcc']; ?>" />
	            <input name="pctaProyAccVal2" type="hidden" id="pctaProyAccVal2" value="<?php echo $params['pctaProyAccVal2']; ?>"/></span>

				</td>
								
				
			</tr>	
			 <tr>
				<td colspan="2" valign="middle" align="center">
				 <input type="button" value="Buscar" onclick="javascript:ReporteDisponibleIntegrado()" class="normalNegro"/>
				  <input type="reset" value="Limpiar" id="reset" name="reset" class="normalNegro" />
				</td>
			</tr>			
		</table>
	</form>

	<br/>
	
	<table cellpadding="0" cellspacing="0" align="center"
					style="width: 100%;" background="../../imagenes/fondo_tabla.gif"
					class="tablaalertas" id="tablaBusqueda">
	<tr>
       	<th class="normalNegroNegrita header">#</th>
		<th class="normalNegroNegrita header">Punto Cuenta</th>
		<!-- <th class="normalNegroNegrita">Alcances</th>-->
		<th class="normalNegroNegrita header">Proveedor</th>
	<th class="normalNegroNegrita header">Asunto</th>		
<th class="normalNegroNegrita header">Descripci&oacute;n</th>
	<th class="normalNegroNegrita header">Fecha</th>
		<th class="normalNegroNegrita header">Infocentro</th>
		<th class="normalNegroNegrita header">Proy/Acc</th>
	 	<th class="normalNegroNegrita header">Partida</th>
		<th class="normalNegroNegrita header">Apartado</th>
		<th class="normalNegroNegrita header">Comprometido</th>
		<!-- <th class="normalNegroNegrita">Diferencia</th>-->
		<th class="normalNegroNegrita header">Causado</th>
		<th class="normalNegroNegrita header">Pagado</th>
		<th class="normalNegroNegrita header">Disponible</th> 
     </tr>

     <?php
     if (isset($GLOBALS['SafiRequestVars']['pctaDisponibilidad']) && $GLOBALS['SafiRequestVars']['pctaDisponibilidad']!= null && $GLOBALS['SafiRequestVars']['pctaDisponibilidad'] !='') {
?>


<?php
 $i = 1;
foreach ($GLOBALS['SafiRequestVars']['pctaDisponibilidad'] as $pcta){
    foreach ($pcta["partidas"] as $partida){
    	
    $proveedorSugerido = $pcta["rif_sugerido"] != null? $pcta["rif_sugerido"] : '';


    $cadena = $proveedorSugerido;
    $caracter = ":";
             
    if (strpos($cadena, $caracter) !== false){
    $proveedorSugerido2 = explode(":",$proveedorSugerido);
    $proveedorSugeridoVal = utf8_decode($proveedorSugerido2[0]);
    }else{
    	
    	$proveedorSugeridoVal = '';
    }
    
    
    	
    	
    	
    	   
    	 	$tdClass = ($tdClass == "even") ? "odd" : "even";
     ?>
     <tr class="<?php echo $tdClass;?>" onclick="Registroclikeado(this)">
     		<td style="border-bottom: 1px solid #C3ECCC" align="right"><?php echo $i++;?></td>
		 <td valign ="top" style="border-bottom: 1px solid #C3ECCC" > <a href="#dialog" docgId="<?php echo $pcta["pcta_id"]; ?>" class="detalleOpcion" opcion="null"> <?php echo $pcta["pcta_id"] ;?></a> </td>
		<!-- <td><?php echo $pcta["pcta_asociados"] ;?></td>-->
		<td style="border-bottom: 1px solid #C3ECCC" align="right"><?php echo  $proveedorSugerido;?></td>
		<td style="border-bottom: 1px solid #C3ECCC" align="right"><?php echo $pcta["asunto"] ;?></td>		
		<td style="border-bottom: 1px solid #C3ECCC" align="right">&nbsp;<?php echo $pcta["descripcion_presupuesto"] ;?></td>
		<td style="border-bottom: 1px solid #C3ECCC" align="right"><?php echo $pcta["pcta_fecha"] ;?></td>
		<td style="border-bottom: 1px solid #C3ECCC" align="right">&nbsp;<?php echo $pcta["infocentro_id"] ;?></td>
		<?php 
		if (substr($pcta["proy_acc"],0,1) == '/') $proyecto = substr($pcta["proy_acc"],1,strlen($pcta["proy_acc"])); 
		else $proyecto = substr($pcta["proy_acc"],0,strlen($pcta["proy_acc"])-1)
		?>
				<td style="border-bottom: 1px solid #C3ECCC" align="right"><?php echo $proyecto ;?></td>
		<td style="border-bottom: 1px solid #C3ECCC" align="right"><?php echo $partida["partida"] ;?></td>										
		<td style="border-bottom: 1px solid #C3ECCC" align="right">
		

		
		<a href="javascript:void(0)" class="detalleApartadoPcta" 
		tipo='Apartado' partida='<?=$partida["partida"]?>' 
		pcta='<?=$pcta["pcta_id"]?>' 
		aopres='<?=substr($pcta["pcta_fecha"],6,4)?>'
		monto='<?=$partida["montoApartado"]?>'><?php echo number_format($partida["montoApartado"],2,',','.');?> </a>
		
		</td>
		<td style="border-bottom: 1px solid #C3ECCC" align="right">
		
		
		<a href="javascript:void(0)" class="detalleApartadoPcta" 
		tipo='Compromiso' partida='<?=$partida["partida"]?>' 
		pcta='<?=$pcta["pcta_id"]?>' 
		aopres='<?=substr($pcta["pcta_fecha"],6,4)?>'
		monto='<?=$partida["montoComprometido"]?>'><?php echo number_format($partida["montoComprometido"],2,',','.');?> </a>
		
		</td>
		<td style="border-bottom: 1px solid #C3ECCC" align="right">
		
		<a href="javascript:void(0)" class="detalleApartadoPcta" 
		tipo='Causado' partida='<?=$partida["partida"]?>' 
		pcta='<?=$pcta["pcta_id"]?>' 
		aopres='<?=substr($pcta["pcta_fecha"],6,4)?>'
		monto='<?=$partida["montoCausado"]?>'><?php echo number_format($partida["montoCausado"],2,',','.');?> </a>
		
		</td>
		<td style="border-bottom: 1px solid #C3ECCC" align="right">
		
		
		<a href="javascript:void(0)" class="detalleApartadoPcta" 
		tipo='Pagado' partida='<?=$partida["partida"]?>' 
		pcta='<?=$pcta["pcta_id"]?>' 
		aopres='<?=substr($pcta["pcta_fecha"],6,4)?>'
		monto='<?=$partida["montoPagado"]?>'><?php echo number_format($partida["montoPagado"],2,',','.');?> </a>
		
		</td>
		
		<td style="border-bottom: 1px solid #C3ECCC" align="right"><?php if(number_format($partida["montoApartado"]-$partida["montoComprometido"],2,',','.')!=0){ echo "***********".number_format($partida["montoApartado"]-$partida["montoComprometido"],2,',','.');}else{echo number_format($partida["montoApartado"]-$partida["montoComprometido"],2,',','.');}?></td>

	<!-- 	eporteDisponibleIntegradoAccionDetalle  -->
		
				
	</tr>
    <?php   }}}else{?>
    
    <tr class="odd">
		<td colspan='14'>No se han encontrado registros</td>
			</tr>
			<?php }?>
		
</table>

<div id="dialogisss" title="Monto disponible">
<div class="normalNegroNegrita" align="center"><span  id='detalleDisponPartida'></span></div>
<br/>
<table cellpadding="0" cellspacing="0" align="center" style="width: 100%; " background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr id='detalleDisponTr1' > 
<th class="header"><span class="normalNegroNegrita">C&oacute;digo</th>
<th class="header"><span class="normalNegroNegrita">Monto Bs.</th>
</tr>
  <tbody id="tablaDetalleDisponibilidad">

</tbody>
<tr> 

<th class="header"><span class="normalNegroNegrita">Detalle Total Bs.:</th>


<th class="header" colspan="2"><span class="normalNegroNegrita" id='detalleDisponMontoTotal2' >0</th>


</tr>

</table></div>


</body>
</html>     