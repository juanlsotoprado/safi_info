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
</head>

 <script type="text/javascript" charset="utf-8">
      TABMENUITEM_PUNTO_CUENTA_BANDEJA = "<?php echo TABMENUITEM_PUNTO_CUENTA_BANDEJA;?>";
	  TABMENUITEM_PUNTO_CUENTA_INSERTAR = "<?php echo TABMENUITEM_PUNTO_CUENTA_INSERTAR;?>";
 </script>		
<body>

<?php include(SAFI_VISTA_PATH . '/detalleCompletoDocumeto.php'); ?>



   		<?php include(SAFI_VISTA_PATH . '/mensajes.php'); if($_GLOBALS['SafiRequestVars']['pctaPorEnviar']){ ?>
	<table style="width:100%;">
		<tr>
			<td style="text-align: center;" class="normalNegroNegrita"><span
				style="padding-bottom: 20px; display: block;">Puntos de Cuenta Por
					enviar/aprobar</span>
			</td>
		</tr>
		<tr>
			<td><table cellpadding="0" cellspacing="0" align="center"
					style="width: 100%;" background="../../imagenes/fondo_tabla.gif"
					class="tablaalertas">
					<tr>
						<th class="header"><span class="normalNegroNegrita">#</span>
						</th>
						</th>
						<th class="header"><span class="normalNegroNegrita">C&oacute;digo</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Fecha</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Asunto</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Justificaci&oacute;n</span>
						</th>
						<td class="header"><span class="normalNegroNegrita">Opciones</span>
						
						</th>
					</tr>


					<?php
					$tdClass ='even';
					$i = 1;

					if($_GLOBALS['SafiRequestVars']['pctaPorEnviar']){
						foreach ($_GLOBALS['SafiRequestVars']['pctaPorEnviar'] as $index ){

							$tdClass = ($tdClass == "even") ? "odd" : "even";

							?>


					<tr onclick="Registroclikeado(this);"
						class="<?php echo $tdClass;?>">

						<td style="font-weight: bold;"><?php echo $i ?></td>
						<td><a href="#dialog" docgId="<?php echo $index['docg_id']; ?>"
							class="detalleOpcion" opcion="null"  > <?php echo $index['docg_id'] ?>
						</a>
						</td>
						<td><?php echo $index['docg_fecha'] ?></td>
						<td><?php echo $index['pcta_asunto'] ?></td>
						<td><?php echo $index['pcta_justificacion'] ?></td>
						<td><script type="text/javascript">
					var pctaPorEnviarOpciones = <?php echo json_encode($GLOBALS['SafiRequestVars']['opciones'],JSON_FORCE_OBJECT); ?>;


					
					
				 </script> <a style="margin-right: 5px;" href="#dialog"
							class="detalleOpcion" opcion="pctaPorEnviarOpciones"
							docgId="<?php echo $index['docg_id']; ?> " idCadenaActual="<?php echo $index['wfca_id'] ?>" >Seleccionar</a>
						</td>
					</tr>


					<?php $i++; }}else{ ?>

					<tr class="odd" onclick='Registroclikeado(this)'>
						<td colspan="7">No se han encontrado registros</td>

					</tr>


					<?php } ?>


				</table>
			</td>
		</tr>
	</table>
	
	<br/>

	<br/>
		<?php } if($_GLOBALS['SafiRequestVars']['pctaDevuelto']){?>
	<table style="width: 100%;">

		<tr>
			<td style="text-align: center;" class="normalNegroNegrita"><span
				style="padding-bottom: 20px; display: block;">Puntos de Cuenta
					Devueltos</span>
			</td>
		</tr>
		<tr>
			<td><table cellpadding="0" cellspacing="0" align="center"
					style="width: 100%;" background="../../imagenes/fondo_tabla.gif"
					class="tablaalertas">
					<tr>
						<th class="header"><span class="normalNegroNegrita">#</span>
						</th>
						</th>
						<th class="header"><span class="normalNegroNegrita">C&oacute;digo</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Fecha</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Asunto</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Justificaci&oacute;n</span>
						</th>
						<td class="header"><span class="normalNegroNegrita">Opciones</span>
						
						</th>
					</tr>


					<?php
					$tdClass ='even';
					$i = 1;

					if($_GLOBALS['SafiRequestVars']['pctaDevuelto']){
						foreach ($_GLOBALS['SafiRequestVars']['pctaDevuelto'] as $index ){

							$tdClass = ($tdClass == "even") ? "odd" : "even";


							?>


					<tr onclick="Registroclikeado(this);"
						class="<?php echo $tdClass;?>">

						<td style="font-weight: bold;"><?php echo $i ?></td>
						<td><a href="#dialog" docgId="<?php echo $index['docg_id']; ?>"
							class="detalleOpcion" opcion="null"> <?php echo $index['docg_id'] ?>
						</a>
						</td>
						<td><?php echo $index['docg_fecha'] ?></td>
						<td><?php echo $index['pcta_asunto'] ?></td>
						<td><?php echo $index['pcta_justificacion'] ?></td>
						<td><script type="text/javascript">
					var pctaPorEnviarOpciones = <?php echo json_encode($GLOBALS['SafiRequestVars']['opciones'],JSON_FORCE_OBJECT); ?>;

				 </script> <a style="margin-right: 5px;" href="#dialog"
							class="detalleOpcion" opcion="pctaPorEnviarOpciones"
							docgId="<?php echo $index['docg_id']; ?> "   idCadenaActual="<?php echo $index['wfca_id'] ?>">Seleccionar</a>
						</td>
					</tr>


					<?php $i++; }}else{ ?>

					<tr class="odd" onclick='Registroclikeado(this)'>
						<td colspan="7">No se han encontrado registros</td>

					</tr>


					<?php } ?>


				</table>
			</td>
		</tr>
	</table>
	
	<br/>
	<br/>
	
	<?php } 
	
	if($_GLOBALS['SafiRequestVars']['pctaEnTransito']){
	
	?>
	
		<table style="width: 100%;">

		<tr>
			<td style="text-align: center;" class="normalNegroNegrita"><span
				style="padding-bottom: 20px; display: block;">Puntos de Cuenta en Tr&aacute;nsito</span>
			</td>
		</tr>
		<tr>
			<td><table cellpadding="0" cellspacing="0" align="center"
					style="width: 100%;" background="../../imagenes/fondo_tabla.gif"
					class="tablaalertas">
					<tr>
						<th class="header"><span class="normalNegroNegrita">#</span>
						</th>
						</th>
						<th class="header"><span class="normalNegroNegrita">C&oacute;digo</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Fecha</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Asunto</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Justificaci&oacute;n</span>
						</th>
						<th class="header"><span class="normalNegroNegrita">Instancia actual</span>
						</th>
					</tr>


					<?php
					$tdClass ='even';
					$i = 1;

					if($_GLOBALS['SafiRequestVars']['pctaEnTransito']){
						foreach ($_GLOBALS['SafiRequestVars']['pctaEnTransito'] as $index ){

							$tdClass = ($tdClass == "even") ? "odd" : "even";


							?>


					<tr onclick="Registroclikeado(this);"
						class="<?php echo $tdClass;?>">

						<td style="font-weight: bold;"><?php echo $i ?></td>
						<td><a href="#dialog" docgId="<?php echo $index['docg_id']; ?>"
							class="detalleOpcion" opcion="null"> <?php echo $index['docg_id'] ?>
						</a>
						</td>
						<td><?php echo $index['docg_fecha'] ?></td>
						<td><?php echo $index['pcta_asunto'] ?></td>
						<td><?php echo $index['pcta_justificacion'] ?></td>
						<td><?php echo $index['perf_id_act'] ?></td>

					</tr>


					<?php $i++; }}else{ ?>

					<tr class="odd" onclick='Registroclikeado(this)'>
						<td colspan="7">No se han encontrado registros</td>

					</tr>


					<?php } ?>


				</table>
			</td>
		</tr>
	</table>
	
	<?php } ?>
	

</body>
</html>
