<?php
include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
?>
<!DOCTYPE html>
<html> 
<head>
	<title>SAFI</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"></script>
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/funciones.js';?>"></script>
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/actb.js';?>"></script>
	<?php require(SAFI_JAVASCRIPT_PATH.'/init.php'); ?>
	<?php require(SAFI_INCLUDE_PATH.'/fechaJs.php'); ?>
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/pgch.js'?>"></script>
</head>

<body>

	  <table class="tabla">
	   <tr class="td_gray">
			<th>C&oacute;digo</th>
			<th>Fecha</th>
			<th>Beneficiario</th>
			<th>Detalle</th>		
			<th>Opciones</th>
		</tr>
		<?php foreach ($GLOBALS['SafiRequestVars']['sopg'] as $listaSopg) {?> 
		<tr class="normalNegro" height="14">				
			<td height="34"><a href="javascript:abrir_ventana('../../documentos/sopg/sopg_detalle.php?codigo=<?php echo $listaSopg->GetId();?>&esta_id=39')"><?php echo $listaSopg->GetId();?></a></td>
			<td><?php echo $listaSopg->GetFecha();?></td>
			<td><?php echo $listaSopg->GetBeneficiarioNombre();?></td>
			<td><?php echo $listaSopg->GetDetalle();?></td>
			<td><a href="../../accion_documento.php?accion=1&tipo=pgch&codigo=<?php echo $listaSopg->GetId();?>" class="copyright"> Iniciar Pago</a></td>
		</tr>					
		<?php }	?>	  
	</table>
</form>	
</body>
</html>