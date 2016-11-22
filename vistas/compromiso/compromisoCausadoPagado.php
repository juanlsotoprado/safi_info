<?php
include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
$anno_pres = $_SESSION['an_o_presupuesto'];
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
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/uploadify/uploadify/jquery.uploadify.min.js';?>"
	charset="utf-8"></script>

<!-- <script language="javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/func_montletra_jquery.js';?>"></script>-->

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

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/DetalleCompletoDocumento.js';?>"
	charset="utf-8"></script>

<script type="text/javascript" charset="utf-8">
       PHPSESSID = '<?php echo $_COOKIE['PHPSESSID'];?>';
       
 </script>


</head>

<body>
<?php include(SAFI_VISTA_PATH . '/detalleCompletoDocumeto.php');
include(SAFI_VISTA_PATH . '/mensajes.php');?>


	<form name="formPctaFiltro" id="formCompFiltro" method="post" action="">
		<table width="640" align="center" style='border: 1px solid #BEBEBE'
			background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr>
				<th class="header" colspan='2'><span class="normalNegroNegrita">.:Buscar
						Compromiso :.</span></th>
			</tr>
			<tr>
				<td width="175" height="29" class="normalNegrita" align="left">Fecha
					del compromiso:</td>
				<td><input type="text" size="10" id="hid_hasta_itin"
					name="hid_hasta_itin" class="dateparse" readonly="readonly"
					value="<?php echo $_POST["hid_hasta_itin"];?>" /> <a
					href="javascript:void(0);"
					onclick="g_Calendar.show(event, 'hid_hasta_itin');"
					title="Show popup calendar"> <img
						src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"
						alt="Open popup calendar" /> </a></td>
			</tr>
			<tr>
				<td class="normalNegrita" align="left">Categor&iacute;a:</td>
				<td><span class="normalNegrita"> <select name="tipo_reporte"
						id="tipo_reporte" class="normalNegro">
							<option value="">Seleccione</option>
							<option value="1">
							<?php echo "Actualmente NO causados";?>
							</option>
							<option value="2">
							<?php echo "Causados y NO pagados actualmente";?>
							</option>
					</select> </span></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><br /> <input type="button"
					value="Buscar" id="BuscarCompCausadoPagado" /> <input type="reset"
					value="Limpiar" id="reset" name="reset" />
				</td>

			</tr>

		</table>

	</form>

	<table style="width: 80%;" align="center">

		<tr>
			<td><table cellpadding="0" cellspacing="0" align="center"
					style="border: 1px solid #BEBEBE; width: 100%"
					background="../../imagenes/fondo_tabla.gif" class="tablaalertas"
					id="tablaBusqueda">
					<tbody id="tablaFiltro">
						<tr>
							<th class="header"><span class="normalNegroNegrita">#</span>
							</th>
							<th class="header"><span class="normalNegroNegrita">C&oacute;digo
									del documento</span>
							</th>

							<th class="header"><span class="normalNegroNegrita">Proyecto/Acci&oacute;n Centralizada</span>
							</th>

							<th class="header"><span class="normalNegroNegrita">Acci&oacute;n Espec&iacute;fica</span>
							</th>
							
							
							<th class="header"><span class="normalNegroNegrita">Monto Compromiso Bs.</span>
							</th>
							
								<?php if ($GLOBALS['SafiRequestVars']['causadoPagado']['tipo'] == 2) { ?>
							
							<th class="header"><span class="normalNegroNegrita">Causado </span>
							</th>
							
							<th class="header"><span class="normalNegroNegrita">Fecha del compromiso</span>
							</th>
							
								<?php }?>
							
		
							
							<?php
					
							$tdClass ='even';

							if($GLOBALS['SafiRequestVars']['causadoPagado']){?>

							<?php
							$num=1;
							foreach($GLOBALS['SafiRequestVars']['causadoPagado'] as $index => $valor){
								$tdClass = ($tdClass == "even") ? "odd" : "even";
								
								?>
						
						
						<tr class="<?php echo $tdClass;?>"
							onclick="Registroclikeado(this)">
							<td valign="top"><?php echo $num; ?>
							</td>
							

							<td valign="top"><a href="#dialog" docgId="<?php echo $index; ?>"
								class="detalleOpcion" opcion="null" tipoDetalle="compromiso"> <?php echo $index; ?>
							</a>
							</td>

							<td valign="top">
							<?php echo $valor['centrogestor']; ?>
							</td>

							<td valign="top">
							<?php echo  $valor['centrocosto'];?>
							 </td>
							 
							 <td valign="top" align="right">
							<?php echo  number_format($valor['comp_monto_solicitado'],2,',','.'); ?>
							 </td>
							 
							 	<?php if ($GLOBALS['SafiRequestVars']['causadoPagado']['tipo'] == 2) { ?>
							 	
							 <td valign="top">
							 
						<a target="_blank" href="../../documentos/comp/detalleCausadoComp.php?comp=<?=$index?>&aopres=<?=$anno_pres?>&monto=<?=$valor['causado']?>"><?php echo number_format($valor['causado'],2,',','.');?></a></td> 

							 </td>
							 
							  <td valign="top">
							<?php echo  $valor['comp_fecha']; ?>
							 </td>
							 
							 
							 	<?php }?>
							 
								<?php

								$num++;}}else{?>
						
						
						<tr class="odd">
							<td colspan='12'>No se han encontrado registros</td>
						</tr>
						<?php }?>
					</tbody>

				</table>
			</td>
		</tr>
	</table>

	</form>
</body>
</html>
