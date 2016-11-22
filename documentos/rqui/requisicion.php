<?php
ob_start();
session_start();
require("../../includes/conexion.php");
require("../../includes/funciones.php");
if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush();
	exit;
}
require("../../includes/constantes.php");
ob_end_flush();
$pres_anno=$_SESSION['an_o_presupuesto'];
$fecha = date('d/m/Y');
$estatusDeshabilitado = "0";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:. Ingresar Requisici&oacute;n</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script language="JavaScript" src="../../js/funciones.js"></script>
<script language="JavaScript" src="../../js/crearModificarRequisicion.js"></script>
<script>
	dependencia = '<?= $_SESSION["user_depe_id"];?>';
	tipoReqActual = '<?= TIPO_REQUISICION_COMPRA?>';
</script>
</head>
<body class="normal">
	<?php
	$msg=$_GET['msg'];
	if($msg=="0"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar el tipo de requisici&oacute;n (Compra o Servicio).</p>";
	}else if($msg=="1"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar el Proyecto o Acci&oacute;n Centralizada.</p>";
	}else if($msg=="2"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar la Acci&oacute;n Espec&iacute;fica.</p>";
	}else if($msg=="3"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Error interno. Falta la cantidad de art&iacute;culos agregados.</p>";
	}else if($msg=="4"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Las cantidades indicadas deben ser valores num&eacute;ricos.</p>";
	}else if($msg=="5"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar al menos un (1) art&iacute;culo, bien o servicio para la solicitud de requisici&oacute;n.</p>";
	}else if($msg=="6"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar la justificaci&oacute;n del punto de cuenta.</p>";
	}else if($msg=="7"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar la justificaci&oacute;n de la requisici&oacute;n.</p>";
	}
	?>
	<form name="form" method="post" action="requisicionAccion.php" id="form">
		<input type="hidden" name="accion" id="accion" value=""/>
		<input type="hidden" name="txt_id_tp_p_ac" id="txt_id_tp_p_ac" value=""/>
		<table align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td colspan="2" class="normalNegroNegrita">
					Registrar
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table class="tablaalertas" width="900px">
						<tr>
							<td width="20%">
								Tipo de requisici&oacute;n<span class="peq_naranja">(*)</span>
							</td>
							<td width="80%" class="normalNegro">
								<input type='radio' name='typo' value='<?= TIPO_REQUISICION_COMPRA?>' checked="checked"/>Compra
								<input type='radio' name='typo' value='<?= TIPO_REQUISICION_SERVICIO?>'/>Servicio
								<div class="normal" style="float: right;">
									Fecha
									<input type="text" size="10" id="fecha" name="fecha" class="dateparse" readonly="readonly" value="<?= $fecha?>"/>
									<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha');" title="Establecer Fecha">
										<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Establecer Fecha"/>
									</a>
								</div>
							</td>
						</tr>	
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table class="tablaalertas" width="900px">
						<tr class="td_gray">
							<td>
								<div align="center">
									<a href="javascript:verifica_partida();" id="mostrarCategorias">
										<img src="../../imagenes/estadistic.gif" width="24" height="24" border="0" />
										Categor&iacute;a
									</a>
									<span class="peq_naranja">(*)</span>
								</div>
							</td>
							<td align="center" class="normalNegrita">
								C&oacute;digo
							</td>
							<td align="center" class="normalNegrita">
								Denominaci&oacute;n
							</td>
						</tr>
						<tr>
							<td class="normalNegro">
								<div align="left">
									<input id="tipo_proyecto" name="chk_tp_imputa" type="radio" class="peq" value="1" disabled="disabled"/>
									Proyectos
								</div>
							</td>
							<td rowspan="2">
								<div align="center">
									<input id="txt_cod_imputa" name="txt_cod_imputa" type="hidden" value="" />
		  							<input id="txt_cod_imputa2" name="txt_cod_imputa2" type="text" class="ptotal" size="15" value="" readonly="readonly" />
								</div>
							</td>
							<td rowspan="2">
								<div align="center">
									<input id="txt_nombre_imputa" name="txt_nombre_imputa" type="text" class="ptotal" size="70" readonly="readonly" value=""/>
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" class="normalNegro">
								<div align="left">
									<input id="tipo_accion" name="chk_tp_imputa" type="radio" class="peq" value="0" disabled="disabled"/>
									Acci&oacute;n Cent.
								</div>
							</td>
						</tr>
						<tr>
							<td class="normalNegro">
								&nbsp;Acci&oacute;n Espec&iacute;fica
							</td>
							<td>
								<div align="center">
								    <input id="txt_cod_accion" name="txt_cod_accion" type="hidden" />
									<input id="txt_cod_accion2" name="txt_cod_accion2" type="text" class="ptotal" size="15" readonly="readonly" />
								</div>
							</td>
							<td>
								<div align="center"><input id="txt_nombre_accion" name="txt_nombre_accion" type="text" class="ptotal" size="70" readonly="readonly"/></div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table class="tablaalertas" width="900px">
						<tr>
							<td>
								Unidad de adscripci&oacute;n
							</td>
							<td>
							<div align="left">
								<?php
								if($_SESSION['user_depe_id']!="150" && $_SESSION['user_depe_id']!="350"){
								?>
								<select id="gerenciaAdscripcion" name="gerenciaAdscripcion" class="normalNegro">
									<?php
									$nivelOficinaGerencia = "5";
									$estadoAnulado = "15";
									$query = 	"SELECT depe_id,depe_nombre ".
												"FROM sai_dependenci ".
												"WHERE ".
												"depe_nivel = ".$nivelOficinaGerencia." AND ".
												"depe_id_sup = '".$_SESSION['user_depe_id']."' AND ".
												"esta_id <> ".$estadoAnulado." ".
												"ORDER BY depe_nombre";
									$resultado = pg_exec($conexion, $query);
									$numeroFilas = pg_numrows($resultado);
									for($i = 0; $i < $numeroFilas; $i++) {
										$row = pg_fetch_array($resultado, $i);
									?>
										<option value="<?= $row["depe_id"]?>"><?=$row["depe_nombre"]?></option>
									<?php 
										}
									?>
									<option value="<?= $_SESSION['user_depe_id']?>"><?=$_SESSION['user_depe']?></option>
								</select>
								<?php 
								}else{
									echo "<input type='hidden' id='gerenciaAdscripcion' name='gerenciaAdscripcion' value='".$_SESSION['user_depe_id']."'/><input type='text' class='ptotal' size='53' readonly='readonly' value='".$_SESSION['user_depe']."'/>";
								}
								?>
							</div>
							</td>
						</tr>
						<tr>
							<td>Descripci&oacute;n del producto o servicio solicitado</td>
							<td>
								<div style="width: 100%">
								<textarea class="normalNegro" id="descripcionGeneral" name="descripcionGeneral" cols="99" rows="2"
									onkeydown="textCounter(this,'descripcionGeneralLen',1000);"
									onkeyup="textCounter(this,'descripcionGeneralLen',1000);validarTexto(this);"></textarea><br/>
								<div style="text-align: right;"><input type="text" value="1000" class="normalNegro" maxlength="3" size="3" id="descripcionGeneralLen" name="descripcionGeneralLen" readonly="readonly"/></div>
								</div>
							</td>
						</tr>
						<tr>
							<td class="peq">Justificaci&oacute;n de la requisici&oacute;n<span class="peq_naranja">(*)</span></td>
							<td class="ptotal">
								<div style="width: 100%">
								<textarea class="normalNegro" id="justificacion" name="justificacion" cols="99" rows="2"
									onkeydown="textCounter(this,'justificacionLen',1000);"
									onkeyup="textCounter(this,'justificacionLen',1000);validarTexto(this);"></textarea><br/>
								<div style="text-align: right;"><input type="text" value="1000" class="normalNegro" maxlength="3" size="3" id="justificacionLen" name="justificacionLen" readonly="readonly"/></div>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table class="tablaalertas" width="900px">
						<?php /*
						<tr class="td_gray">
							<td>
								<div align="center" class="peqNegrita">Punto de Cuenta<span class="peq_naranja">(*)</span></div>
							</td>
							<td>
							<div align="left" class="peqNegrita">
								<select id="pcta" name="pcta">
									<option value="" onmouseover="HideContent('contenedorDetalle')">No aplica</option>
									<?php
									$estadoAprobado = 10;
									$asuntoAlcance = "013";
									$query = 	"SELECT sp.pcta_id, to_char(sp.pcta_fecha,'DD/MM/YYYY') as pcta_fecha, sp.pcta_descripcion, spa.pcas_descrip ".
												"FROM sai_pcuenta sp, sai_pcta_asunt spa ".
												"WHERE ".
													"sp.depe_id = '".$_SESSION['user_depe_id']."' AND ".
													"sp.esta_id = ".$estadoAprobado." AND ".
													"sp.pcta_monto_solicitado > 0 AND ".
													"sp.pcta_asunto <> '".$asuntoAlcance."' AND ".
													"sp.pcta_asunto = spa.pcas_id ".
												"ORDER BY spa.pcas_descrip, sp.pcta_id ";
									$resultado = pg_exec($conexion, $query);
									$numeroFilas = pg_numrows($resultado);
									for($i = 0; $i < $numeroFilas; $i++) {
										$row = pg_fetch_array($resultado, $i);
									?>
										<option id="<?= $row["pcta_id"]?>" value="<?= $row["pcta_id"]?>"><?= $row["pcta_id"]." - ".$row["pcas_descrip"].". Fecha: ".$row["pcta_fecha"]."."?></option>
										<script>
											document.getElementById("<?= $row["pcta_id"]?>").onmouseover = function() { ShowContent('contenedorDetalle','<?= $row["pcta_id"]?>','<?= str_replace("\n"," ", str_replace("\r"," ", $row["pcta_descripcion"]))?>'); }
											document.getElementById("<?= $row["pcta_id"]?>").onmouseout = function(){myTime = setTimeout('HideContent(\'contenedorDetalle\')', 20000);};
										</script>
									<?php 
										}
									?>
								</select>
							</div>
							</td>
						</tr>
						*/ ?>
						<tr>
							<td>
								Punto de Cuenta
							</td>
							<td>
								<select id="pcta" name="pcta" class="normalNegro" onchange="validarPcta();">
									<option value="">N/A</option>
									<?php
									$pctaPartidas = "[";
									$pctaImputaciones = "[";
									$pctaAnterior = "";
									$estadoAprobado = 10;
									$asuntoAlcance = "013";
									
									/*"sp.esta_id = ".$estadoAprobado." AND ".*/
									$query = 	"SELECT 
													sp.pcta_id, 
													spi.pcta_sub_espe, 
													s.tipo, 
													s.id_proyecto_accion, 
													s.id_accion_especifica,
													s.nombre_proyecto_accion, 
													s.nombre_accion_especifica,
													s.centro_gestor,
													s.centro_costo
												FROM 
													sai_pcuenta sp
													INNER JOIN sai_doc_genera sdg ON (sp.pcta_id = sdg.docg_id)
													INNER JOIN sai_pcta_asunt spa ON (sp.pcta_asunto = spa.pcas_id)
													INNER JOIN sai_pcta_imputa spi ON (sp.pcta_id = spi.pcta_id)
													INNER JOIN 
														(
															SELECT 
																'1'::BIT as tipo,
																spae.proy_id as id_proyecto_accion, 
																spae.paes_id as id_accion_especifica,
																sp.proy_titulo as nombre_proyecto_accion, 
																spae.paes_nombre as nombre_accion_especifica, 
																spae.centro_gestor, 
																spae.centro_costo
															FROM sai_proyecto sp, sai_proy_a_esp spae 
															WHERE 
																sp.pre_anno = spae.pres_anno AND 
																sp.proy_id = spae.proy_id AND 
																spae.pres_anno = ".$pres_anno." 
															UNION 
															SELECT 
																'0'::BIT as tipo,
																sae.acce_id as id_proyecto_accion,
																sae.aces_id as id_accion_especifica,
																sac.acce_denom as nombre_proyecto_accion, 
																sae.aces_nombre as nombre_accion_especifica, 
																sae.centro_gestor,
																sae.centro_costo  
															FROM sai_ac_central sac, sai_acce_esp sae 
															WHERE 
																sac.pres_anno = sae.pres_anno AND 
																sac.acce_id = sae.acce_id AND 
																sae.pres_anno = ".$pres_anno." 
														) AS s ON (spi.pcta_tipo_impu = s.tipo AND spi.pcta_acc_pp = s.id_proyecto_accion AND spi.pcta_acc_esp = s.id_accion_especifica)
												WHERE 
													sp.pcta_id LIKE '%".substr($pres_anno, -2)."' AND 
													sp.depe_id = '".$_SESSION['user_depe_id']."' AND 
													sp.pcta_monto_solicitado > 0 AND 
													sp.pcta_asunto <> '".$asuntoAlcance."' AND 
													(sdg.wfob_id_ini = 99 OR sdg.perf_id_act = '47350' OR sdg.perf_id_act = '65150') 
												ORDER BY sp.pcta_fecha, sp.pcta_id, spi.pcta_sub_espe";
									$resultado = pg_exec($conexion, $query);
									$numeroFilas = pg_numrows($resultado);
									for($i = 0; $i < $numeroFilas; $i++) {
										$row = pg_fetch_array($resultado, $i);
										if($pctaAnterior!=$row["pcta_id"]){
											$pctaAnterior=$row["pcta_id"];
											if($i == 0){
												$pctaPartidas .= "[";
												$pctaImputaciones .= "[";
											}else{
												$pctaPartidas = substr($pctaPartidas, 0, -1)."],[";
												$pctaImputaciones = substr($pctaImputaciones, 0, -1)."],[";
											}
									?>
											<option id="<?= $row["pcta_id"]?>" value="<?= $row["pcta_id"]?>"><?= $row["pcta_id"]?></option>
									<?php 
										}
										$pctaPartidas .= "'".$row["pcta_sub_espe"]."',";
										$pctaImputaciones .= "['".$row["tipo"]."','".$row["id_proyecto_accion"]."','".$row["id_accion_especifica"]."','".$row["nombre_proyecto_accion"]."','".$row["nombre_accion_especifica"]."','".$row["centro_gestor"]."','".$row["centro_costo"]."'],";
									}
									if($pctaPartidas!=""){
										$pctaPartidas = substr($pctaPartidas, 0, -1)."]]";
									}
									if($pctaImputaciones!=""){
										$pctaImputaciones = substr($pctaImputaciones, 0, -1)."]]";
									}
									?>
								</select>
								<script>
									pctaPartidas = <?= $pctaPartidas?>;
									pctaImputaciones = <?= $pctaImputaciones?>;
								</script>
							</td>
						</tr>
						<tr>
							<td>Justificaci&oacute;n del Punto de Cuenta<span class="peq_naranja">(*)</span></td>
							<td>
								<div style="width: 100%">
								<textarea class="normalNegro" id="pctaJustificacion" name="pctaJustificacion" cols="99" rows="2"
									onkeydown="textCounter(this,'pctaJustificacionLen',1000);"
									onkeyup="textCounter(this,'pctaJustificacionLen',1000);validarTexto(this);"></textarea><br/>
								<div style="text-align: right;"><input type="text" value="1000" class="normalNegro" maxlength="3" size="3" id="pctaJustificacionLen" name="pctaJustificacionLen" readonly="readonly"/></div>
								</div>
							</td>
						</tr>	
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="hidden" name="hid_largo" id="hid_largo"/>
					<input type="hidden" name="hid_val" id="hid_val"/>
					<table width="900px" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
						<tr>
							<td>
								<div id="itemContainer" style="width: 84%;float: left;">
									&nbsp;Rubro<span class="peq_naranja">(*)</span> <input autocomplete="off" size="96" type="text" id="itemCompletar" name="itemCompletar" value="" class="normalNegro"/>
								</div>
								<div style="float: left;width: 16%;">
									&nbsp;Cantidad: <input maxlength="10" type="text" id="cantidad" name="cantidad" onkeyup="validarInteger(this);" size="10" class="normalNegro"/>
								</div>
								<div style="width: 100%; float: left;text-align: left;margin-top: -10px;">
									<br/>&nbsp;Introduzca la partida o una palabra contenida en el nombre del art&iacute;culo, bien o servicio.
								</div>
								<div style="width: 100%;margin-top: 50px;">
									&nbsp;Especificaciones:<br/>
									<textarea class="normalNegro" id="articuloEspecificaciones" name="articuloEspecificaciones" cols="123" rows="3"
										onkeydown="textCounter(this,'articuloEspecificacionesLen',10000);"
										onkeyup="textCounter(this,'articuloEspecificacionesLen',10000);validarTexto(this);"></textarea><br/>
									<div style="text-align: right;"><input type="text" value="10000" class="normalNegro" maxlength="5" size="5" id="articuloEspecificacionesLen" name="articuloEspecificacionesLen" readonly="readonly"/>&nbsp;<input type="button" value="Agregar" onclick="javascript:agregarItem();" class="normalNegro"/></div>
								</div>					
								<?php
									$query = 	"SELECT ".
													"sp.part_id, ".
													"sp.part_nombre, ".
													"sip.id_item, ".
													"UPPER(si.nombre) as nombre ".
												"FROM sai_item si, sai_item_partida sip, sai_partida sp ".
												"WHERE ".
													"sp.pres_anno='".$pres_anno."' AND ".
													"sp.part_id NOT LIKE '4.03.18.01.00' AND ".
													"sp.part_id=sip.part_id AND ".
													"sip.id_item=si.id AND ".
													"si.esta_id <> ".$estatusDeshabilitado." ".
												"ORDER BY sp.part_id, si.nombre ";
									$resultado = pg_exec($conexion, $query);
									$numeroFilas = pg_num_rows($resultado);
									
									$arregloItems = "";
									$idsPartidasItems = "";
									$nombresPartidasItems = "";
									$idsItems = "";
									$nombresItems = "";
									while($row=pg_fetch_array($resultado)){
										$arregloItems .= "'".$row["part_id"]." : ".strtoupper(str_replace("\n"," ",$row["nombre"]))."',";
										$idsPartidasItems .= "'".$row["part_id"]."',";
										$idsItems .= "'".$row["id_item"]."',";
										$nombresPartidasItems .= "'".str_replace("\n"," ",strtoupper($row["part_nombre"]))."',";
										$nombresItems .= "'".str_replace("\n"," ",strtoupper($row["nombre"]))."',";
									}
									$arregloItems = quitarAcentosMayuscula(substr($arregloItems, 0, -1));
									$idsPartidasItems = substr($idsPartidasItems, 0, -1);
									$nombresPartidasItems = quitarAcentosMayuscula(substr($nombresPartidasItems, 0, -1));
									$idsItems = substr($idsItems, 0, -1);
									$nombresItems = quitarAcentosMayuscula(substr($nombresItems, 0, -1));
								?>
								<script>
									var arregloItems = new Array(<?= $arregloItems?>);
									var idsPartidasItems = new Array(<?= $idsPartidasItems?>);
									var nombresPartidasItems = new Array(<?= $nombresPartidasItems?>);
									var idsItems = new Array(<?= $idsItems?>);
									var nombresItems = new Array(<?= $nombresItems?>);
									actb(document.getElementById('itemCompletar'),arregloItems);
								</script>
							</td>
						</tr>
						<tr>
							<td>
								<table width="100%" class="tablaalertas" id="tbl_mod">
									<tr class="td_gray normalNegrita">
										<td align="center" width="10%">C&oacute;digo</td>
										<td align="center" width="15%">Nombre</td>
										<td align="center" width="10%">Partida</td>
										<td align="center" width="15%">Denominaci&oacute;n</td>
										<td align="center" width="30%">Especificaciones</td>
										<td align="center" width="10%">Cantidad</td>
										<td align="center" width="10%">Acci&oacute;n</td>
									</tr>
									<tbody id="item">
									</tbody>
									<tr>
										<td height="19" colspan="7">&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table class="tablaalertas" width="900px">
						<tr>
							<td>Proveedor sugerido (RIF o nombre)</td>
							<?php
								$estadoAnulado = 15;
								$sql_e="SELECT prov_id_rif,prov_nombre FROM sai_proveedor_nuevo WHERE prov_esta_id <> '".$estadoAnulado."' ORDER BY prov_nombre";
								$resultado_set_e=pg_query($conexion,$sql_e) or die("Error al mostrar");
								$numeroFilas = pg_numrows($resultado_set_e);
							?>
							<td>
								Sugerencia 1:
								<select class="normalNegro" id="prov_sug1" name="prov_sug1">
								<option value="">Seleccione</option> 
								<?php
									$i = 0;
									while($i<$numeroFilas){
										$rowe=pg_fetch_array($resultado_set_e,$i);
										$prov_id=trim($rowe['prov_id_rif']);
										$prov_nombre=$rowe['prov_nombre'];
										echo "<option value= ".$prov_id.">".$prov_nombre." (RIF ".strtoupper(substr(trim($prov_id),0,1))."-".substr(trim($prov_id),1).")</option>";
										$i++;
									}
								?>
								</select><br/>
								<div align="right">Otro: <input class="normalNegro" type=text name="prov_sug1_otro" size="35" maxlength="80" onkeyup="validarTexto(this);"/></div>
								<br/>
								Sugerencia 2:
								<select class="normalNegro" id="prov_sug2" name="prov_sug2">
								<option value="">Seleccione</option>
								<?php
									$i = 0;
									while($i<$numeroFilas){
										$rowe=pg_fetch_array($resultado_set_e,$i);
										$prov_id=trim($rowe['prov_id_rif']);
										$prov_nombre=$rowe['prov_nombre'];
										echo "<option value= ".$prov_id.">".$prov_nombre." (RIF ".strtoupper(substr(trim($prov_id),0,1))."-".substr(trim($prov_id),1).")</option>";
										$i++;
									}
								?>
								</select>
								<div align="right">Otro: <input class="normalNegro" type=text name="prov_sug2_otro" size="35" maxlength="80" onkeyup="validarTexto(this);"/></div>
								<br/>
								Sugerencia 3:
								<select class="normalNegro" id="prov_sug3" name="prov_sug3">
								<option value="">Seleccione</option>
								<?php
									$i = 0;
									while($i<$numeroFilas){
										$rowe=pg_fetch_array($resultado_set_e,$i);
										$prov_id=trim($rowe['prov_id_rif']);
										$prov_nombre=$rowe['prov_nombre'];
										echo "<option value= ".$prov_id.">".$prov_nombre." (RIF ".strtoupper(substr(trim($prov_id),0,1))."-".substr(trim($prov_id),1).")</option>";
										$i++;
									}
								?>
								</select>
								<div align="right">Otro: <input class="normalNegro" type=text name="prov_sug3_otro" size="35" maxlength="80" onkeyup="validarTexto(this);"/></div>
								<br/>
							</td>
						</tr>
						<tr>
							<td class="peq">Caracter&iacute;sticas sugeridas para seleccionar proveedor</td>
							<td>
								<table>
									<!-- <tr>
										<td class="peq">Calidad</td>
										<td class="ptotal"><select name=calidad class="normal">
											<option value="<?= NA?>">N/A</option>
											<option value="<?= CALIDAD_BAJA?>">Baja</option>
											<option value="<?= CALIDAD_MEDIA?>">Media</option>
											<option value="<?= CALIDAD_ALTA?>">Alta</option>
										</select></td>
									</tr> -->
									<tr>
										<td>Tiempo de entrega</td>
										<td>
											<select name="entrega" class="normalNegro">
												<option value="<?= NA?>">N/A</option>
												<option value="<?= TIEMPO_ENTREGA_MENOR_7_DIAS?>">Menor a 7 D&iacute;as</option>
												<option value="<?= TIEMPO_ENTREGA_MENOR_2_SEMANAS?>">Menor a 2 Semanas</option>
												<option value="<?= TIEMPO_ENTREGA_MENOR_1_MES?>">Menor a 1 Mes</option>
												<option value="<?= TIEMPO_ENTREGA_MAYOR_1_MES?>">Mayor a 1 Mes</option>
											</select>
										</td>
									</tr>
									<tr>
										<td>Garant&iacute;a</td>
										<td>
											<select name="garantia" class="normalNegro">
												<option value="<?= NA?>">N/A</option>
												<option value="<?= GARANTIA_SI?>">Si</option>
												<option value="<?= GARANTIA_NO?>">No</option>
											</select>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>Observaciones</td>
							<td>
								<textarea class="normalNegro" id="txt_observaciones" name="txt_observaciones" cols="85" rows="6"
									onkeydown="textCounter(this,'remLen',1000);"
									onkeyup="textCounter(this,'remLen',1000);validarTexto(this);"></textarea><br/>
								<div style="text-align: right; width: 100%;"><input type="text" value="1000" class="normalNegro" maxlength="3" size="3" id="remLen" name="remLen" readonly="readonly"/></div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br/>
		<div id="divAcciones" style="text-align: center;">
			<input class="normalNegro" type="button" value="Guardar en Borrador" onclick="enviar('<?= ACCION_GUARDAR_REQUISICION_EN_BORRADOR?>');"/>
			<input class="normalNegro" type="button" value="Enviar" onclick="enviar('<?= ACCION_ENVIAR_REQUISICION?>');"/>
		</div>
		<br/>
		<div class="ptotal"><span class="peq_naranja">(*)</span> Campo obligatorio</div>
	</form>
</body>
</html>
<?php pg_close($conexion); ?>