
<?php
include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');

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

<script language="javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/func_montletra.jss';?>"></script>

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
      include(SAFI_VISTA_PATH . '/mensajes.php');

      ?>


	<form name="formPctaFiltro" id="formCompFiltro" method="post" action="">
		<table width="640" align="center" style='border:1px solid #BEBEBE'
			background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr>
				<th class="header" colspan='2'><span class="normalNegroNegrita">.:Buscar
						Compromiso :.</span></th>
			</tr>
			<tr>
				<td width="175" height="29" class="normalNegrita" align="left"><span
					class="normalNegrita"> A&ntilde;o Presupuestario: </span></td>
				<td><select class="normalNegrita" id="agnoComp" name="agnoComp">
				<?php
				$fecha = new DateTime();
				$valueInic = 7;
				$agnoFin = $fecha->format('Y');
				$agnoInic = 2007;
					
				while ($agnoInic <= $agnoFin){?>

						<option
						<?php

							
							
						if($agnoInic == $params['agnoComp'])
						{
							echo 'selected'; $selected = true;

						}else{

							if($selected == false && $agnoInic == $agnoFin){

								echo 'selected';

							}} ?>
							value="<?php echo $agnoInic ?>">
							<?php echo $agnoInic ?>
						</option>

						<?$valueInic++;$agnoInic++;}?>

				</select>
				</td>
			
			
			<tr>
				<td width="175" height="29" class="normalNegrita" align="left">Solicitados
					entre:</td>
				<td><input type="text" size="10" id="txt_inicio" name="txt_inicio"
					class="dateparse" onfocus="javascript: comparar_fechas(this);"
					readonly="readonly" value="<?php echo $params['txt_inicio']; ?>" />
					<a href="javascript:void(0);"
					onclick="g_Calendar.show(event, 'txt_inicio');"
					title="Show popup calendar"> <img
						src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"
						alt="Open popup calendar" /> </a> <input type="text" size="10"
					id="hid_hasta_itin" name="hid_hasta_itin"
					value="<?php echo $params['hid_hasta_itin']; ?>" class="dateparse"
					onfocus="javascript: comparar_fechas(this);" readonly="readonly"
					value="" /> <a href="javascript:void(0);"
					onclick="g_Calendar.show(event, 'hid_hasta_itin');"
					title="Show popup calendar"> <img
						src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"
						alt="Open popup calendar" /> </a>
				</td>
			</tr>
			<tr id="pctaBuscarTr3">
				<td class="normalNegrita" align="left">Nro. Compromiso:</td>
				<td><span class="normalNegrita"><input name="ncompromiso"
						type="text" class="normalNegro" id="ncompromiso" size="25"
						value="<?php echo $params['ncompromiso']; ?>" />
				
				</td>
			</tr>

			<tr id="pctaBuscarTr4">
				<td class="normalNegrita" align="left">Asunto :</td>
				<td><input type="text" id="asunto" name="asunto" size="19" /> <input
					type="hidden" name="asuntoVal" id="asuntoVal"
					value="<?php echo $asuntoVal ?>">
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
										value="<?php echo $var->GetId(); ?>">
										<?php echo $var->GetNombre();?>
									</option>


									<?php }} ?>

							</select>
							</td>

						</tr>
			<tr id="pctaBuscarTr4"  style="display: none;" > <!-- campo comentado inhabilitado por presupuesto 01/04/13 -->
				<td class="normalNegrita" align="left">Tipo actividad:</td>
				<td><input type="text" id="tipoActividad" name="tipoActividad"
					size="19" /> <input type="hidden" name="tipoActividadVal"
					id="tipoActividadVal" value="<?php echo $tipoActividad ?>">
				</td>
			</tr>
			<tr id="pctaBuscarTr5">
				<td class="normalNegrita" align="left">Partida:</td>
				<td><span class="normalNegrita"> <input type="text"
						name="PartidaBusqueda" id="PartidaBusqueda" class="normalNegro"
						value="<?php echo $params['PartidaBusqueda']; ?>" size="25" /> </span>
				</td>
			</tr>
			<tr id="pctaBuscarTr6">
				<td class="normalNegrita" align="left">Proyecto/Acc
					espec&iacute;fica:</td>
				<td><input size="25" name="compProyAcc" type="text"
					class="normalNegro" id="compProyAcc"
					value="<?php echo $params['pctaProyAcc']; ?>" /> <input
					name="compProyAccVal" type="hidden" id="compProyAccVal"
					value="<?php echo $params['pctaProyAccVal']; ?>" /></span></td>
			</tr>

			<tr id="pctaBuscarTr7">
				<td class="normalNegrita" align="left">Palabra Clave:</td>
				<td><span class="normalNegrita"><input size="25" name="palabraClave"
						type="text" class="normalNegro" id="palabraClave"
						value="<?php echo $params['palabraClave']; ?>" /> </span><span
					class="normal">(Incluida en la descripci&oacute;n del punto de
						cuenta)</span></td>
			</tr>
			</td>

			</tr>


			<tr>
				<td colspan="2" align="center"><br /> <input type="button"
					value="Buscar" id="BuscarComp" /> <input type="reset"
					value="Limpiar" id="reset" name="reset" />
				</td>

			</tr>

		</table>

	</form>

	<table style="width: 100%;">

		<tr>
			<td><table cellpadding="0" cellspacing="0" align="center" style="border:1px solid #BEBEBE;width: 100%"
					 background="../../imagenes/fondo_tabla.gif"
					class="tablaalertas" id="tablaBusqueda">
					<tbody id="tablaFiltro">
						<tr>
							<th class="header"><span class="normalNegroNegrita">#</span>
							</th>
							<th class="header"><span class="normalNegroNegrita">C&oacute;digo
									del documento</span>
							</th>

							<th class="header"><span class="normalNegroNegrita">Fecha
									solicitud</span>
							</th>
							
							<th class="header"><span class="normalNegroNegrita">Estado</span>
							</th>
							
							<?php if($_SESSION['user_perfil_id'] == '46400' or $_SESSION['user_perfil_id'] == '62400'){  ?>
							
						 <th class="header"><span class="normalNegroNegrita">Actualizar fecha</span>
							</th>
							
<?php };?>
							<th class="header"><span class="normalNegroNegrita">Seleccione</span>
							</th>
							<?php
							$num = 1;
							$tdClass ='even';

							if($GLOBALS['SafiRequestVars']['compFiltro']){?>

							<?php  foreach($GLOBALS['SafiRequestVars']['compFiltro']as $index => $valor){
								$tdClass = ($tdClass == "even") ? "odd" : "even";
								?>
						
						
						<tr class="<?php echo $tdClass;?>"
							onclick="Registroclikeado(this)">
							<td valign="top"><?php echo $num; ?>
							</td>

							<td valign="top"><a href="#dialog" docgId="<?php echo $index; ?>"
								class="detalleOpcion" opcion="null" tipoDetalle="compromiso"> <?php echo $index; ?> </a>
							</td>

                          
							<td valign="top" class="fechatd" ><?php echo $valor->GetFecha(); ?>
							</td>
							
							  <td valign="top" <?php 
							  if($valor->GetEstatus()->GetId() == 15){ echo "style='color:red'";
							$GetEstatus =  $valor->GetEstatus()->GetNombre();
							  }else{
							  
							  $GetEstatus =  "Activo";
							  } ?>>
							  
							  <?php echo $GetEstatus; ?>
							  
							</td>
							
							<?php  if($valor->GetEstatus()->GetId() != 15){ 
							
								$annocomp = substr($index,-2);
								
								?>
							
							
							
							<?php if(($_SESSION['user_perfil_id'] == '46400' or $_SESSION['user_perfil_id'] == '62400') && (substr($index,-2) == substr($_SESSION['an_o_presupuesto'],-2))){  ?>
							
							
							
							<td valign="top"><a href="javascript:void(0);" onclick="CambioTraza(this)" comp="<?php echo $index; ?>">Cambiar fecha traza</a>
							
					
							
							</td>
<?php }else{   ?>   <td valign="top"> - </td>


<?php } ?>
							<td valign="top"><a href="#dialog" docgId="<?php echo $index; ?>"
								class="detalleOpcion" opcion="true" tipoDetalle="compromiso"> <?php echo $index; ?> </a>
							</td>

							<?php 

							}else{ ?>
								
							<td valign="top">-
							</td>

							<td valign="top">-
							</td>
								
								
						<?php }
							
							
							$num++;}}else{?>
						
						
						<tr class="odd">
							<td colspan='12'>No se han encontrado registros</td>
						</tr>
						<?php } ?>
					</tbody>

				</table>
			</td>
		</tr>
	</table>

	</form>
	<div id="dialogisss" title="">
			<table cellpadding="0" cellspacing="0" align="center"
				style="width: 100%;" background="../../imagenes/fondo_tabla.gif"
				class="tablaalertas">
				<tr id='detalleDisponTr1'>
					<th class="header"><span class="normalNegroNegrita">Compromiso 
					
					</th>
				
					<th class="header"><span class="normalNegroNegrita">Fecha actual
					</th>
					
					<th class="header"><span class="normalNegroNegrita">Nueva fecha
					</th>

				</tr>
				<tbody id="tablaTraza">
				</tbody>
			
			</table>
			 <br/>
	
			<div align='center'>
				<input type="button" value="Modificar" onclick='ModificarFechaTraza()' 
					name=ModificarFechaTraza /> <input type="reset" value="Limpiar"
					id="reset" name="reset" />
			</div>
	</div>
</body>
</html>
