<?php
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
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/func_montletra_jquery.js';?>"
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
      include(SAFI_VISTA_PATH . '/mensajes.php'); ?>

	<form name="formPctaDisp" id="formPctaDisp" method="post" action="">
		<table width="380"  align="center"  background="../../imagenes/fondo_tabla.gif"class="tablaalertas">
  <tr>
  <th class="header" colspan='2' ><span class="normalNegroNegrita">.: DISPONIBILIDAD PRESUPUESTARIA DEL PUNTO DE CUENTA :.</span></th> 
  </tr>
			
			<tr >
				<td class="normalNegrita" align="left" style="width: 50%;" >C&oacute;digo del documento:</td>
				<td><span class="normalNegrita"><input name="codigPctaBusquedaDisp" id="codigPctaBusquedaDisp" type="text"
						class="normalNegro" value="" size="15" /></span>

				</td>
			</tr>

  <tr>
  <td colspan="2" align="center">
  <br/>
  <input  type="button" value="Buscar"   id="BuscarPctaDisp"  />
  <input type="reset" value="reset" id="reset" name="reset" />
  
  </td>
  
  </tr>
		
		</table>
		
		  
	</form>

		<table style="width: 100%;">

		<tr>
			<td><table cellpadding="0" cellspacing="0" align="center"
					style="width: 100%;" background="../../imagenes/fondo_tabla.gif"
					class="tablaalertas" id="tablaBusqueda">
					 <tbody id="tablaFiltro" >
					<tr>
						<th class="header"><span class="normalNegroNegrita">1</span>
						</th>
						</th>
						<th class="header"><span class="normalNegroNegrita">2</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">3</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">4</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">5</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">6</span>
						</th>
					
                   </tr>
                   <?php 
                   
                   $num = 1;
                    $tdClass ='even';
                   
                   if($GLOBALS['SafiRequestVars']['puntoCuentaDisponibilidad']){?>

                 <?php  foreach($GLOBALS['SafiRequestVars']['puntoCuentaDisponibilidad'] as $index => $valor){
                   	$tdClass = ($tdClass == "even") ? "odd" : "even";
                   	?>
                   	
                   	
                   	
                   	<tr class="<?php echo $tdClass;?>" onclick="Registroclikeado(this)">
                   	
                       <td valign ="top"> <?php echo $num; ?> </td>
                         <td valign ="top"> <?php echo $num; ?> </td>
                           <td valign ="top"> <?php echo $num; ?> </td>
                             <td valign ="top"> <?php echo $num; ?> </td>
                               <td valign ="top"> <?php echo $num; ?> </td>
                                 <td valign ="top"> <?php echo $num; ?> </td>
                               
                      
                    </tr>

                   <?php $num++;}}else{ ?>
                   
                   <tr class="odd">
						<td colspan="6">No se han encontrado registros</td>
					    </tr>
					   <?php }?>
                     </tbody>
				
				</table>
			</td>
		</tr>
	</table>


</body>
     
