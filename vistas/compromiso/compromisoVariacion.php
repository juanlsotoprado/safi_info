<?php
include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');

?>

<html>
<head>
<title>.:SAFI:. Compromiso</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>" rel="stylesheet" type="text/css" />
<link href="<?=GetConfig("siteURL").'/css/safi0.2.css';?>"rel="stylesheet" type="text/css" />
<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>" rel="stylesheet" type="text/css" />
<!-- elRTE -->
<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/elrte.min.css';?>" rel="stylesheet" type="text/css" />
<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/elrte-inner.css';?>" rel="stylesheet" type="text/css" />

<?php require(SAFI_JAVASCRIPT_PATH.'/init.php'); ?>
<?php require(SAFI_INCLUDE_PATH.'/fechaJs.php'); ?>
<!-- <script type="text/javascript" src="<?=//SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"></script>  -->
<!-- <script type="text/javascript" src="<?=//SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/ui.min.js';?>"></script>  -->
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/jquery-ui-1.8.13.custom.min.js';?>"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/constantes.js';?>"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/funciones.js';?>"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/uploadify/uploadify/jquery.uploadify.min.js';?>"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/func_montletra_jquery.js';?>"></script>
<!-- elRTE -->
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/elrte.min.js';?>"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/elRTE.options.js';?>"></script>
<!-- elRTE translation messages -->
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/i18n/elrte.es.js';?>" ></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/compromiso/compromiso.js';?>" ></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/DetalleCompletoDocumento.js';?>" ></script>
<script type="text/javascript" >PHPSESSID = '<?php echo $_COOKIE['PHPSESSID'];?>';</script>



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


</head>

<body>
<?php include(SAFI_VISTA_PATH . '/detalleCompletoDocumeto.php');
      include(SAFI_VISTA_PATH . '/mensajes.php');?>


	<form name="formPctaFiltro" id="formCompFiltro" method="post" action="">
		<table width="640" align="center" style='border:1px solid #BEBEBE'
			background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr>
				<th class="header" colspan='2'><span class="normalNegroNegrita">.:Buscar
						Compromiso :.</span></th>
			</tr>
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
				<td><span class="normalNegrita"></span><input name="ncompromiso"
						type="text" class="normalNegro" id="ncompromiso" size="25"
						value="<?php echo $params['ncompromiso']; ?>" />
				
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center"><br /> <input type="button"
					value="Buscar" id="BuscarCompVariacion" /> <input type="reset"
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

							<th class="header"><span class="normalNegroNegrita">Seleccione</span>
							</th>
							<?php
							$num = 1;
							$tdClass ='even';

							if( $GLOBALS['SafiRequestVars']['compVariacion']['data']){?>

							<?php  foreach( $GLOBALS['SafiRequestVars']['compVariacion']['data'] as $index => $valor){
								$tdClass = ($tdClass == "even") ? "odd" : "even";
								?>
						
						
						<tr class="<?php echo $tdClass;?>"
							onclick="Registroclikeado(this)">
							<td valign="top"><?php echo $num; ?>
							</td>

							<td valign="top"><a href="#dialog" docgId="<?php echo $index; ?>"
								class="detalleOpcion" opcion="null" tipoDetalle="compromiso"> <?php echo $index; ?> </a>
							</td>

							<td valign="top"><?php echo $valor->GetFecha(); ?>
							</td>

							<td valign="top">

								<table border="0">
									<tr>
										<td colspan=6><hr color="red"></td>
									</tr>
									<tr>
										<td><b>Fecha</b></td>
										<td><b>Partida</b></td>
										<td><b>CG</b></td>
										<td><b>CC</b></td>
										<td><b>Monto</b></td>
									</tr>
									<tr>
										<td colspan=6><hr color="red"></td>
									</tr>

									<?php
									$vuelta=0;
									$vuelta2=0;
									$recorrida="";


									if($GLOBALS['SafiRequestVars']['compVariacion']['VariacionComp1'][$index]){?>

									<?php  foreach( $GLOBALS['SafiRequestVars']['compVariacion']['VariacionComp1'][$index] as $index2 => $valor2){


																				if($fecha==""||($fecha==$valor2['fecha1'] && $vuelta>0))	{											$sub1[$vuelta]=$valor2['comp_sub_espe'];
											$cg1[$vuelta]=$valor2['comp_acc_pp'];
											$cc1[$vuelta]=$valor2['comp_acc_esp'];
											$esta1[$vuelta]=$valor2['esta_id'];
											$fecha1[$vuelta]=$valor2['fecha1'];
											$fecha2[$vuelta]=$valor2['fecha2'];
											$mto1[$vuelta]=$valor2['comp_monto'];
											$fecha=$valor2['fecha1']; ?>

									<tr>
										<td class="normalNegro"><b><?php echo $fecha1[$vuelta]?> </b>
										</td>
										<td><b><?php echo $sub1[$vuelta]?> </b>
										</td>
										<td><b><?php echo $cg1[$vuelta]?> </b>
										</td>
										<td><b><?php echo $cc1[$vuelta]?> </b>
										</td>
										<td align=right><b> <?php echo number_format($mto1[$vuelta], 2, ",", ".");?>
										</b>
										</td>
									</tr>

									<?php
									$recorrida=$recorrida.",".$vuelta;
									$vuelta=$vuelta+1;
									$vuelta2=$vuelta;

										}else{
												
												
												
											if($fecha!=$valor2['fecha1']){

												$taman=count(explode(",",$recorrida))-1;
												$arreglo=explode(",",$recorrida);
												if($taman!=$vuelta2){
													for($i=1;$i<$taman;$i++){
														if($arreglo[$i]!=$i){
															$mto1[$i]="";
															$cg1[$i]="";
															$cc1[$i]="";
															$fecha1[$i]="";
															$sub1[$i]="";
															$esta1[$i]="";
														}
													}
												}
												$recorrida="";

												?>
									<tr>
										<td colspan=6 height=20></td>
									</tr>
									<?php

											}
											$encontro=0;
											for($i=0;$i<$vuelta2;$i++){
												if($valor2['comp_sub_espe']==$sub1[$i]){
														
													if($mto1[$i]==$valor2['comp_monto']){
														$mto=0;
													}else{
														$mto=$valor2['comp_monto']-$mto1[$i];
													}
													if($cg1[$i]==$valor2['comp_acc_pp']){
														$cg="-";
													}else{
														$cg=$valor2['comp_acc_pp'];
													}
													if($cc1[$i]==$valor2['comp_acc_esp']){
														$cc="-";
													}else{
														$cc=$valor2['comp_acc_esp'];
													}
													$fecha=$valor2['fecha1'];
													$sub=$valor2['comp_sub_espe'];
													$esta=$valor2['esta_id'];

													$mto1[$i]=$valor2['comp_monto'];
													$cg1[$i]=$valor2['comp_acc_pp'];
													$cc1[$i]=$valor2['comp_acc_esp'];
													$fecha1[$i]=$valor2['fecha1'];
													$fecha2[$i]=$valor2['fecha2'];
													$sub1[$i]=$valor2['comp_sub_espe'];
													$esta1[$i]=$valor2['esta_id'];
													$recorrida=$recorrida.",".$i;
													$fecha22=$valor2['fecha2'];
													$encontro=1;
														
												}
											}

											if($encontro==0){
												$sub=$valor2['comp_sub_espe'];
												$mto=$valor2['comp_monto'];
												$cg=$valor2['comp_acc_pp'];
												$cc=$valor2['comp_acc_esp'];
												$fecha=$valor2['fecha1'];
												$esta=$valor2['esta_id'];
												$mto1[$vuelta2]=$valor2['comp_monto'];
												$cg1[$vuelta2]=$valor2['comp_acc_pp'];
												$cc1[$vuelta2]=$valor2['comp_acc_esp'];
												$fecha1[$vuelta2]=$valor2['fecha1'];
												$fecha2[$vuelta2]=$valor2['fecha2'];
												$sub1[$vuelta2]=$valor2['comp_sub_espe'];
												$esta1[$vuelta2]=$valor2['esta_id'];
												$fecha22=$valor2['fecha2'];

												$recorrida=$recorrida.",".$vuelta2;
												$vuelta2=$vuelta2+1;
											}
											if($mto<>0||$cg!="-"||$cc!="-"){?>
									<tr>
										<td><?php echo $fecha1?>
										</td>
										<td><?php echo $sub?>
										</td>
										<td><?php echo $cg?>
										</td>
										<td><?php echo $cc?>
										</td>
										<td align=right><?php echo number_format($mto, 2, ",", ".")?>
										</td>
									</tr>
									<?php
											}
											$vuelta=0;

									 }}}
									 	
									 $vuelta=0;
									 $sub="";
									 $cg="";
									 $cc="";
									 $mto=0;
									 $esta="";
									 $fecha="";
									 $fecha22="";
									 $vuelta=0;
									 $vuelta2=0;
									 $recorrida="";

									 ?>
									<tr>
										<td colspan=6 height=20></td>
									</tr>
									<?php if($GLOBALS['SafiRequestVars']['compVariacion']['VariacionComp2'][$index]){?>

									<?php  foreach( $GLOBALS['SafiRequestVars']['compVariacion']['VariacionComp2'][$index] as $index3 => $valor3){
										if($fecha==""||($fecha==$valor3['fecha1']&&$vuelta>0))	{
												
											$sub1[$vuelta]=$valor3['comp_sub_espe'];
											$cg1[$vuelta]=$valor3['comp_acc_pp'];
											$cc1[$vuelta]=$valor3['comp_acc_esp'];
											$esta1[$vuelta]=$valor3['esta_id'];
											$fecha1[$vuelta]=$valor3['fecha1'];
											$fecha2[$vuelta]=$valor3['fecha2'];
											$mto1[$vuelta]=$valor3['comp_monto'];
											$fecha=$valor3['fecha1'];	?>
									<tr>
										<td><b><?php echo $fecha1[$vuelta]?>
										</b>
										</td>
										<td><b><?php echo $sub1[$vuelta]?>
										</b>
										</td>
										<td><b><?php echo $cg1[$vuelta]?>
										</b>
										</td>
										<td><b><?php echo $cc1[$vuelta]?>
										</b>
										</td>
										<td align=right><b><?php echo number_format($mto1[$vuelta], 2, ",", ".")?>
										</b>
										</td>
									</tr>
									<?php 
											$vuelta=$vuelta+1;
											$vuelta2=$vuelta;
										 }else
											  {
												break;
											  }
  									
								}}
									?>


								</table></td>

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

</body>
</html>
