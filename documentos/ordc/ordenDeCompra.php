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
$idRequ = "";
if (isset($_REQUEST['idRequ']) && $_REQUEST['idRequ'] != "") {
	$idRequ = trim($_REQUEST['idRequ']);
}
ob_end_flush();
$pres_anno=$_SESSION['an_o_presupuesto'];
$estatusDeshabilitado = "0";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:. Orden de Compra</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css"/>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
	g_Calendar.setDateFormat('dd/mm/yyyy');
</script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script language="JavaScript" src="../../js/funciones.js"></script>
<script language="JavaScript" src="../../js/botones.js"></script>
<script language="JavaScript" src="../../js/date.js"></script>
<script language="JavaScript" src="../../js/crearModificarOrdenDeCompra.js"></script>
<script>
	idRequ = '<?= $idRequ?>';
</script>
</head>
<body class="normal">
<?php
	$msg=$_GET['msg'];
	if($msg=="1"){
		echo "<p class='normal' style='color: red;text-align: center;'>Debe indicar el id de la Requisici&oacute;n.</p>";
	}else if($msg=="2A"){
		echo "<p class='normal' style='color: red;text-align: center;'>No se encontraron cotizaciones.</p>";
	}else if($msg=="2B"){
		echo "<p class='normal' style='color: red;text-align: center;'>Debe ingresar al menos una (1) cotizaci&oacute;n.</p>";
	}else if($msg=="3"){
		echo "<p class='normal' style='color: red;text-align: center;'>Debe indicar el proveedor que seleccion&oacute;.</p>";
	}else if($msg=="4"){
		echo "<p class='normal' style='color: red;text-align: center;'>Debe indicar el criterio de selecci&oacute;n del proveedor.</p>";
	}else if($msg=="5"){
		echo "<p class='normal' style='color: red;text-align: center;'>El criterio de selecci&oacute;n indicado es \"Otros\", por lo tanto debe especificarlo en las Observaciones.</p>";
	}else if($msg=="6"){
		echo "<p class='normal' style='color: red;text-align: center;'>Debe indicar la Forma de Pago de la Orden de Compra.</p>";
	}else if($msg=="7"){
		echo "<p class='normal' style='color: red;text-align: center;'>Debe indicar el Lugar de Entrega de la Orden de Compra.</p>";
	}else if($msg=="8"){
		echo "<p class='normal' style='color: red;text-align: center;'>Debe indicar la Justificación de la Orden de Compra.</p>";
	}
	?>
	<form id="submitForm" name="submitForm" method="post" action="ordenDeCompraAccion.php"></form>
	<form name="form" method="post" action="" id="form" onsubmit="return false;">
		<input type="hidden" name="txt_id_tp_p_ac" id="txt_id_tp_p_ac" value=""/>
		<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td colspan="2" class="normalNegroNegrita">
					Registrar orden de compra
				</td>
			</tr>
			<?php
			$requisiciones=0;
			if($idRequ && $idRequ!=""){
				$query = 	"SELECT ".
								"srbms.rebms_tipo ".
							"FROM sai_req_bi_ma_ser srbms, sai_sol_coti ssc ".
							"WHERE ".
							"lower(srbms.rebms_id) like '".strtolower($idRequ)."' AND lower(ssc.rebms_id) like '".strtolower($idRequ)."'";
				$resultado = pg_exec($conexion, $query);
				$requisiciones = pg_numrows($resultado);
				if($requisiciones>0){
					$row = pg_fetch_array($resultado, 0);
					$rebms_tipo = $row["rebms_tipo"];
			?>
			<tr>
				<td colspan="2" height="48" align="center" valign="bottom">
					C&oacute;digo de la requisici&oacute;n: <span class="peq_naranja">(*)</span><input class="normalNegro" type="text" id="idRequ" value="<?= $idRequ?>" onkeyup="validarCodigo(this);"/><input type="button" value="cargar" class="normalNegro" onclick="cargarRequisicion();"/>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="24" class="normal peq_verde_bold">
					Fecha de orden de compra<span class="peq_naranja">(*)</span>
								<input type="text" size="10" name="fechaOrden" id="fechaOrden" class="dateparse" readonly="readonly"/>
								<a href="javascript:void(0);" 
									onclick="g_Calendar.show(event, 'fechaOrden');" 
									title="Show popup calendar">
									<img src="../../js/lib/calendarPopup/img/calendar.gif" 
										class="cp_img" 
										alt="Open popup calendar"/>
								</a>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="24" class="normal peq_verde_bold">
					Ingresar cotizaci&oacute;n
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table class="tablaalertas" width="100%">
						<tr class="td_gray normalNegrita">
							<td colspan="2">
								Proveedor<span class="peq_naranja">(*)</span>
								<select id="proveedor" name="proveedor" class="normalNegro">
									<option value="">Seleccione</option>
									<?php
									$estadoInactivo = "2";
									$resultado = pg_exec($conexion,	"SELECT sp.prov_id_rif, sp.prov_nombre ".
																	"FROM sai_sol_coti ssc, sai_sol_coti_prov sscp, sai_proveedor_nuevo sp ".
																	"WHERE ".
																		"lower(ssc.rebms_id) like '".strtolower($idRequ)."' ".
																		"AND ssc.soco_id = sscp.soco_id ".
																		"AND sscp.beneficiario_rif = sp.prov_id_rif ".
																		"AND sp.prov_esta_id <> ".$estadoInactivo." ".
																		"AND sp.temporal <> 1 ".
																	"GROUP BY sp.prov_id_rif, sp.prov_nombre ".
																	"ORDER BY sp.prov_nombre ASC");
									
									$numeroFilas = pg_numrows($resultado);
									for($ri = 0; $ri < $numeroFilas; $ri++) {
								   		$row = pg_fetch_array($resultado, $ri);
								   		echo "<option value='".$row["prov_id_rif"]."'>".$row["prov_nombre"]." (RIF ".strtoupper(substr(trim($row["prov_id_rif"]),0,1))."-".substr(trim($row["prov_id_rif"]),1).")</option>";
									}
									?>
								</select>
								<br/>
								Fecha de Cotizaci&oacute;n<span class="peq_naranja">(*)</span>
								<input type="text" size="10" id="fechaCotizacion" class="dateparse" readonly="readonly"/>
								<a href="javascript:void(0);" 
									onclick="g_Calendar.show(event, 'fechaCotizacion');" 
									title="Show popup calendar">
									<img src="../../js/lib/calendarPopup/img/calendar.gif" 
										class="cp_img" 
										alt="Open popup calendar"/>
								</a>
							
								<?php
								$estadoAnulado = "15";
								$query =	"SELECT ".
												"impu_porc ".
											"FROM sai_impuesto_porce sip ".
											"WHERE impu_id = 'IVA' AND impu_retencion = CAST(0 as bit) AND impu_porc > 0 AND esta_id <> ".$estadoAnulado." ".
											"ORDER BY impu_porc";
								$resultado = pg_exec($conexion, $query);
								$elementos = pg_numrows($resultado);
								$ivas = array();
								for($i=0;$i<$elementos;$i++){
									$row = pg_fetch_array($resultado, $i);
									$ivas[$i] = $row["impu_porc"];
									?>
									<script>
										ivas[ivas.length] = parseFloat(<?= $row["impu_porc"]?>);
										ivasBandera[ivasBandera.length] = false;
									</script>
									<?php
								}
								for($j=0;$j<sizeof($ivas);$j++){
									echo "<span style='margin-left:5px;'>Base IVA ".$ivas[$j]."%</span><input id='base".$ivas[$j]."' name='base".$ivas[$j]."' class='normalNegro' type='text' size='6' maxlength='10' onkeyup='validarDecimal(this);'/>";
								}
								?>
								<input type="checkbox" id="redondear" name="redondear"/>Redondear
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<table id="tbl_mod" align="center" class="tablaalertas" width="100%">
									<tr class="td_gray normalNegrita">
										<td align="center">
											<?php 
											if($rebms_tipo==TIPO_REQUISICION_COMPRA){
												echo "C&oacute;digo de art&iacute;culo/activo";
											}else if($rebms_tipo==TIPO_REQUISICION_SERVICIO){
												echo "C&oacute;digo de servicio";
											}
											?>
										</td>
										<td align="center">
											<?php 
											if($rebms_tipo==TIPO_REQUISICION_COMPRA){
												echo "Art&iacute;culo/Bien";
											}else if($rebms_tipo==TIPO_REQUISICION_SERVICIO){
												echo "Servicio";
											}
											?>
										</td>
										<td align="center">
											Partida
										</td>
										<td align="center">
											Denominaci&oacute;n
										</td>
										<td align="center">
											Especificaciones
										</td>
										<td align="center">
											Cantidad total requerida
										</td>
										<td align="center">
											Cantidad solicitada
										</td>
										<td align="center">
											Cantidad requerida faltante
										</td>
										<td align="center">
											Unidad
										</td>
										<td align="center">
											Cantidad unitaria cotizada
										</td>
										<td align="center">
											Precio unitario
										</td>
									</tr>
									<tbody id="item">
									<?php
									
									$query =	"SELECT ".
													"sci.numero_item ".
												"FROM ".
													"sai_orden_compra soc, ".
													"sai_cotizacion sc, ".
													"sai_cotizacion_item sci ".
												"WHERE ".
													"lower(soc.rebms_id) like '".strtolower($idRequ)."' AND ".
													"soc.rif_proveedor_seleccionado = sc.rif_proveedor AND ".
													"soc.ordc_id = sc.ordc_id AND ".
													"sc.id_cotizacion = sci.id_cotizacion ";
									$resultado = pg_exec($conexion, $query);
									$elementos = pg_numrows($resultado);
									$articulosSolicitados = "";
									if($elementos>0){
										$articulosSolicitados = "(";
										for($i=0;$i<$elementos;$i++){
											$row = pg_fetch_array($resultado, $i);
											$articulosSolicitados .= $row["numero_item"].",";
										}
										$articulosSolicitados = substr($articulosSolicitados, 0, -1).")";										
									}
									
									if($articulosSolicitados!=""){
										$query =	"SELECT ".
														"id, ".
														"numero_item, ".
														"nombre, ".
														"cantidad, ".
														"descripcion, ".
														"id_partida, ".
														"nombre_partida, ".
														"cantidad_solicitada ".
													"FROM ( ".
													 	"SELECT ".
															"si.id, ".
															"sci.numero_item, ".
															"si.nombre, ".
															"sri.rbms_item_cantidad as cantidad, ".
															"sri.rbms_item_desc as descripcion, ".
															"sp.part_id as id_partida, ".
															"sp.part_nombre as nombre_partida, ".
															"SUM(sci.cantidad_cotizada*sci.unidad) as cantidad_solicitada ".
														"FROM ".
															"sai_rqui_items sri, ".
															"sai_item si, ".
															"sai_item_partida sip, ".
															"sai_partida sp, ".
															"sai_orden_compra soc, ".
															"sai_cotizacion sc, ".
															"sai_cotizacion_item sci ".
														"WHERE ".
															"lower(sri.rebms_id) like '".strtolower($idRequ)."' AND ".
															"soc.rebms_id = sri.rebms_id AND ".
															"soc.ordc_id = sc.ordc_id AND ".
															"soc.rif_proveedor_seleccionado = sc.rif_proveedor AND ".
															"sc.id_cotizacion = sci.id_cotizacion AND ".
															"sci.numero_item = sri.numero_item AND ".
															"sri.rbms_item_arti_id = si.id AND ".
															"sri.rbms_item_arti_id = sip.id_item AND ".
															"sip.part_id = sp.part_id AND ".
															"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ".
														"GROUP BY si.id, sci.numero_item, si.nombre, sri.rbms_item_cantidad, sri.rbms_item_desc, sp.part_id, sp.part_nombre ".
														"UNION ".
														"SELECT ".
															"si.id, ".
															"sri.numero_item, ".
															"si.nombre, ".
															"sri.rbms_item_cantidad as cantidad, ".
															"sri.rbms_item_desc as descripcion, ".
															"sp.part_id as id_partida, ".
															"sp.part_nombre as nombre_partida, ".
															"0 as cantidad_solicitada ".
														"FROM ".
															"sai_rqui_items sri, ".
															"sai_item si, ".
															"sai_item_partida sip, ".
															"sai_partida sp ".
														"WHERE ".
															"lower(sri.rebms_id) like '".strtolower($idRequ)."' AND ".
															"sri.numero_item NOT IN ".$articulosSolicitados." AND ".
															"sri.rbms_item_arti_id = si.id AND ".
															"sri.rbms_item_arti_id = sip.id_item AND ".
															"sip.part_id = sp.part_id AND ".
															"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ".
														"GROUP BY si.id, sri.numero_item, si.nombre, sri.rbms_item_cantidad, sri.rbms_item_desc, sp.part_id, sp.part_nombre ".
													" ) AS s ".
													"ORDER BY nombre ";
									}else{
										$query =	"SELECT ".
														"si.id, ".
														"sri.numero_item, ".
														"si.nombre, ".
														"sri.rbms_item_cantidad as cantidad, ".
														"sri.rbms_item_desc as descripcion, ".
														"sp.part_id as id_partida, ".
														"sp.part_nombre as nombre_partida, ".
														"0 as cantidad_solicitada ".
													"FROM ".
														"sai_rqui_items sri, ".
														"sai_item si, ".
														"sai_item_partida sip, ".
														"sai_partida sp ".
													"WHERE ".
														"lower(sri.rebms_id) like '".strtolower($idRequ)."' AND ".
														"(sri.rebms_id NOT IN (SELECT soc.rebms_id FROM sai_orden_compra soc WHERE soc.rif_proveedor_seleccionado IS NOT NULL AND soc.rif_proveedor_seleccionado <> '')) AND ".
														"sri.rbms_item_arti_id = si.id AND ".
														"sri.rbms_item_arti_id = sip.id_item AND ".
														"sip.part_id = sp.part_id AND ".
														"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ".
													"GROUP BY si.id, sri.numero_item, si.nombre, sri.rbms_item_cantidad, sri.rbms_item_desc, sp.part_id, sp.part_nombre ".
													"ORDER BY si.nombre ";
									}
									$resultado = pg_exec($conexion, $query);
									$elementos = pg_numrows($resultado);
									for($i=0;$i<$elementos;$i++){
										$row = pg_fetch_array($resultado, $i);
									?>
									<tr>
										<td valign="top" align="center" class="normalNegro">
											<!-- <input type="hidden" id="txt_id_art<?=$i?>" name="txt_id_art<?=$i?>" value="<?=$row["id"]?>"/> -->
											<?=$row["id"]?>
										</td>
										<td valign="top" align="left" class="normalNegro">
											<!-- <input type="hidden" id="txt_nb_art<?=$i?>" name="txt_nb_art<?=$i?>" value="<?=$row["nombre"]?>"/> -->
											<?=$row["nombre"]?>
										</td>
										<td valign="top" align="center" class="normalNegro">
											<!-- <input type="hidden" id="txt_id_pda<?=$i?>" name="txt_id_pda<?=$i?>" value="<?=$row["id_partida"]?>"/> -->
											<?=$row["id_partida"]?>
										</td>
										<td valign="top" align="left" class="normalNegro">
											<!-- <input type="hidden" id="txt_nb_pda<?=$i?>" name="txt_nb_pda<?=$i?>" value="<?=$row["nombre_partida"]?>"/> -->
											<?=$row["nombre_partida"]?>
										</td>
										<td valign="top" align="left" class="normalNegro">
											<?=$row["descripcion"]?>
										</td>
										<td valign="top" align="center" class="normalNegro">
											<!-- <input type="hidden" id="txt_cantidad<?=$i?>" name="txt_cantidad<?=$i?>" value="<?=$row["cantidad"]?>"/> -->
											<?=$row["cantidad"]?>
										</td>
										<td valign="top" align="center" class="normalNegro">
											<!-- <input type="hidden" id="cantidadSolicitada<?=$i?>" name="cantidadSolicitada<?=$i?>" value="<?=$row["cantidad_solicitada"]?>"/> -->
											<?=$row["cantidad_solicitada"]?>
										</td>
										<td valign="top" align="center" class="normalNegro">
											<!-- <input type="hidden" id="cantidadRequerida<?=$i?>" name="cantidadRequerida<?=$i?>" value="<?=($row["cantidad"]-$row["cantidad_solicitada"]>0)?$row["cantidad"]-$row["cantidad_solicitada"]:"0"?>"/> -->
											<?=($row["cantidad"]-$row["cantidad_solicitada"]>0)?$row["cantidad"]-$row["cantidad_solicitada"]:"0"?>
										</td>
										<td valign="top" align="center" class="normalNegro">
											<input class="normalNegro" type="text" id="unidad<?=$row["numero_item"]?>" name="unidad<?=$row["numero_item"]?>" size="8" onkeyup="validarInteger(this);verificarMenorQue(this,<?= ($row["cantidad"])?>,'El valor del campo Unidad multiplicado por la Cantidad Unitaria Cotizada no puede ser superior a la Cantidad Total Requerida','cantidadUnitaria<?=$row["numero_item"]?>');" maxlength="10"/>
										</td>
										<td valign="top" align="center" class="normalNegro">
											<input class="normalNegro" type="text" id="cantidadUnitaria<?=$row["numero_item"]?>" name="cantidadUnitaria<?=$row["numero_item"]?>" size="8" onkeyup="validarInteger(this);verificarMenorQue(this,<?= ($row["cantidad"])?>,'El valor del campo cantidad unitaria cotizada no puede ser superior a la cantidad total requerida');" maxlength="10" value=""/>
										</td>
										<td valign="top" align="center" class="normalNegro">
											<input class="normalNegro" type="text" id="precioUnitario<?=$row["numero_item"]?>" name="precioUnitario<?=$row["numero_item"]?>" size="8" value="" onkeyup="validarDecimal(this);" maxlength="10"/>
										</td>
									</tr>
									<script>
										articulos[i] = '<?=$row["id"]?>';
										numeroItems[i] = '<?=$row["numero_item"]?>';
										nombresArticulos[i] = '<?=$row["nombre"]?>';
										i++;  
									</script>
									<?php
										}
									?>
									</tbody>
									<tr class="td_gray">
										<td colspan="11" align="center">
											<input class="normalNegro" type="button" value="Agregar" onclick="confirmar()"/>
											<input class="normalNegro" type="button" value="Limpiar" onclick="limpiar()"/>
										</td>
									</tr>
								</table>
								
								<input type="hidden" name="hid_largo" id="hid_largo"/>
								<input type="hidden" name="hid_val" id="hid_val"/>
								<table background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
									<tr class="td_gray normalNegrita">
										<td align="center">
											RUBROS ADICIONALES
										</td>
									</tr>
									<tr>
										<td>
											<div id="itemContainer">
												&nbsp;Rubro<span class="peq_naranja">(*)</span> <input autocomplete="off" size="96" type="text" id="itemCompletar" name="itemCompletar" value="" class="normalNegro"/>
											</div>
											<div style="margin-top: -10px;">
												<br/>&nbsp;Introduzca la partida o una palabra contenida en el nombre del art&iacute;culo, activo o servicio.
											</div>
											<div style="margin-top: 10px;">
												&nbsp;Especificaciones:<br/>
												<textarea class="normalNegro" id="articuloEspecificaciones" name="articuloEspecificaciones" cols="100" rows="2"
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
												$resultadoTodosLosRubros = pg_exec($conexion, $query);
												$numeroFilas = pg_num_rows($resultadoTodosLosRubros);
												
												$arregloItems = "";
												$idsPartidasItems = "";
												$nombresPartidasItems = "";
												$idsItems = "";
												$nombresItems = "";
												while($row=pg_fetch_array($resultadoTodosLosRubros)){
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
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="24" class="normal peq_verde_bold">
					Cotizaciones ingresadas
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table class="tablaalertas">
						<tr class="td_gray normalNegrita" id="header1">
							<td colspan="3" align="center">
								Seleccionar proveedor<span class="peq_naranja">(*)</span>
								<br/>
								Criterio<span class="peq_naranja">(*)</span>
								<select id="criterio" name="criterio" class="normalNegro">
									<option value="">Seleccione</option>
									<option value="1">Precio</option>
									<option value="2">Acuerdo de pago</option>
									<option value="3">Cantidad</option>
									<option value="4">Fecha entrega</option>
									<option value="5">Marca reconocida</option>
									<option value="6">Otros</option>
								</select>
							</td>
						</tr>
						<tr class="td_gray normalNegrita" id="header2">
							<td colspan="3">
								Proveedor
							</td>
						</tr>
						<tr class="td_gray normalNegrita" id="header3">
							<td>
								Rubro
							</td>
							<td>Especificaciones</td>
							<td align="center">
								Cantidad requerida faltante
							</td>
						</tr>
						<tbody id="cotizaciones">
							<?php 
							for($i=0;$i<$elementos;$i++){
								$row = pg_fetch_array($resultado, $i);
							?>
							<tr class="normalNegro" id="articulo<?= $row["numero_item"]?>">
								<td width="150px" align="left" valign="top"><?=$row["nombre"]?></td>
								<td width="150px" align="left" valign="top"><?=$row["descripcion"]?></td>
								<td width="50px" align="center" valign="middle"><?=($row["cantidad"]-$row["cantidad_solicitada"]>0)?$row["cantidad"]-$row["cantidad_solicitada"]:"0"?></td>
							</tr>
							<?php
							}
							?>
						</tbody>
						<tbody id="articulosOrdenDeCompra">
						
						</tbody>
						<tbody id="totales">
							<tr id="subTotal">
								<td align="right" colspan="2">Sub Total</td>
								<td>&nbsp;</td>
							</tr>
							<tr id="filaTotal">
								<td align="right" colspan="2">Total</td>
								<td>&nbsp;</td>
							</tr>
							<!-- <tr id="filaFooter" class="td_gray">
								<td height="15px" colspan="2">&nbsp;</td>
							</tr> -->
						</tbody>
					</table>
				</td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td valign="top">
					Observaciones:
				</td>
				<td>
					<textarea class="normalNegro" id="observaciones" name="observaciones" cols="75" rows="6"
						onkeydown="textCounter(this,'remLenO',1000);"
						onkeyup="textCounter(this,'remLenO',1000);validarTexto(this);"></textarea><br/>
					<div style="text-align: right;width: 554px"><input type="text" value="1000" class="normalNegro" maxlength="3" size="3" id="remLenO" name="remLenO" readonly="readonly"/></div>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="72" class="normal peq_verde_bold" align="left">
					Datos de la orden de compra
				</td>
			</tr>
			<tr>
				<td valign="top">
					Fecha de entrega:
				</td>
				<td>
					<input type="text" id="fechaEntrega" name="fechaEntrega" class="normalNegro" maxlength="50" size="77" onkeyup="validarTexto(this);"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					Forma de pago:
				</td>
				<td>
					<input type="text" id="formaPago" name="formaPago" class="normalNegro" maxlength="50" size="77" onkeyup="validarTexto(this);" value="Cr&eacute;dito"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					Garant&iacute;a de anticipo:
				</td>
				<td>
					<input type="text" id="garantiaAnticipo" name="garantiaAnticipo" class="normalNegro" maxlength="100" size="77" onkeyup="validarTexto(this);"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					Lugar de entrega:
				</td>
				<td>
					<input type="text" id="lugarEntrega" name="lugarEntrega" class="normalNegro" maxlength="100" size="77" onkeyup="validarTexto(this);" value="Av. Universidad, Torre Ministerial, Piso 11, Fundaci&oacute;n Infocentro."/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					Condiciones de entrega:
				</td>
				<td>
					<input type="text" id="condicionesEntrega" name="condicionesEntrega" class="normalNegro" maxlength="100" size="77" onkeyup="validarTexto(this);"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					Otras Garant&iacute;as:
				</td>
				<td>
					<input type="text" id="otrasGarantias" name="otrasGarantias" class="normalNegro" maxlength="100" size="77" onkeyup="validarTexto(this);"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					Otras condiciones / Observaciones:
				</td>
				<td>
					<textarea class="normalNegro" id="otrasCondiciones" name="otrasCondiciones" cols="75" rows="6"
						onkeydown="textCounter(this,'remLenC',1000);"
						onkeyup="textCounter(this,'remLenC',1000);validarTexto(this);"></textarea><br/>
					<div style="text-align: right;width: 554px"><input type="text" value="1000" class="normalNegro" maxlength="3" size="3" id="remLenC" name="remLenC" readonly="readonly"/></div>
				</td>
			</tr>
			<tr>
				<td valign="top">
					Justificaci&oacute;n:<span class="peq_naranja">(*)</span>
				</td>
				<td>
					<textarea class="normalNegro" id="justificacion" name="justificacion" cols="75" rows="6"
						onkeydown="textCounter(this,'remLenJ',1000);"
						onkeyup="textCounter(this,'remLenJ',1000);validarTexto(this);"></textarea><br/>
					<div style="text-align: right;width: 554px"><input type="text" value="1000" class="normalNegro" maxlength="3" size="3" id="remLenJ" name="remLenJ" readonly="readonly"/></div>
				</td>
			</tr>		
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td height="16" colspan="2" align="center">
					<input class="normalNegro" type="button" value="Enviar" onclick="enviar();"/>
				</td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<?php 
				}else{?>
			<tr>
				<td colspan="2" height="24" class="peq_naranja" align="center" style="color: red">
					<br/><br/>El c&oacute;digo de requisici&oacute;n indicado no existe o no se ha enviado solicitud de cotizaci&oacute;n para esta requisici&oacute;n.
				</td>
			</tr>
			<tr>
				<td colspan="2" height="48" align="center">
					C&oacute;digo de la requisici&oacute;n: <span class="peq_naranja">(*)</span><input class="normalNegro" type="text" id="idRequ" onkeyup="validarCodigo(this);"/><input type="button" value="cargar" class="normalNegro" onclick="cargarRequisicion();"/>
				</td>
			</tr>
			<?php
				}
			}else{
			?>
			<tr>
				<td colspan="2" height="72" align="center">
					C&oacute;digo de la requisici&oacute;n: <span class="peq_naranja">(*)</span><input class="normalNegro" type="text" id="idRequ" onkeyup="validarCodigo(this);"/><input type="button" value="cargar" class="normalNegro" onclick="cargarRequisicion();"/>
				</td>
			</tr>
			<?php
			}
			?>
		</table>
		<br/>
		<?php if($idRequ && $idRequ!="" && $requisiciones>0){ ?>
		<span class="peq_naranja">(*)</span> Campo obligatorio
		<?php } ?>
		</form>
</body>
</html>
<?php pg_close($conexion); ?>