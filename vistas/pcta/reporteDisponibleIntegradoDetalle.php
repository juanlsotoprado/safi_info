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
   	
      ?>
</head>
<body>
<div class="normalNegroNegrita" align="center">Monto total Bs.:<?=number_format($GLOBALS['SafiRequestVars']['monto'],2,',','.');?></div>
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
<td class="normalNegroNegrita">C&oacute;digo</td>
<td class="normalNegroNegrita">Monto Bs.</td>
<td class="normalNegroNegrita">Partida</td>
</tr>

<?php
$sumatoria = 0; 
foreach ($GLOBALS['SafiRequestVars']['pctaDisponibilidad'] as $pcta){
	//$resultado  = explode('*', $pcta);
	//$monto = $resultado[1];
	$monto = $pcta -> GetMontoSolicitado();
	$sumatoria = $sumatoria + $monto;
?>
<tr class="normal">
<?php 
if (strcmp(substr($pcta -> GetId(),0,4), 'pcta')==0)
$hipervinculo = "documentos/pcta/pcta_detalle";
else if (strcmp(substr($pcta -> GetId(),0,4), 'comp')==0)
	$hipervinculo = "documentos/comp/comp_detalle.php";
else if (strcmp(substr($pcta -> GetId(),0,4), 'sopg')==0)
	$hipervinculo = "documentos/sopg/sopg_detalle.php";
else if (strcmp(substr($pcta -> GetId(),0,4), 'tran')==0)
	$hipervinculo = "documentos/tran/tran_detalle.php";
else if (strcmp(substr($pcta -> GetId(),0,4), 'pgch')==0)
	$hipervinculo = "documentos/pgch/pgch_detalle.php";
else if (strcmp(substr($pcta -> GetId(),0,4), 'codi')==0)
	$hipervinculo = "documentos/codi/codi_detalle.php";
?>
<td><a href="javascript:abrir_ventana('../../<?=$hipervinculo;?>?codigo=<?php echo trim($pcta -> GetId()); ?>&amp;esta_id=10')" class="copyright"><?=$pcta -> GetId();?></a></td>	
<td><?=number_format($monto,2,',','.');?></td>
<td><?=$GLOBALS['SafiRequestVars']['partida'];?></td>
</tr>
<?php 
}
?>
<tr class="td_gray">
<td class="normalNegroNegrita">Detalle Total Bs.:</td>
<td colspan="2" class="normalNegroNegrita"><?echo number_format($sumatoria,2,',','.');?></td>
</tr>
</table>
</body>
</html>     