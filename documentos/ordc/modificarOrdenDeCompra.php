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
$codigo = "";
if (isset($_GET['codigo']) && $_GET['codigo'] != "") {
	$codigo = $_GET['codigo'];
}
$codigoCR = "";
if (isset($_GET['codigoCR']) && $_GET['codigoCR'] != "") {
	$codigoCR = $_GET['codigoCR'];
}
$idOrdc = "";
if (isset($_REQUEST['idOrdc']) && $_REQUEST['idOrdc'] != "") {
	$idOrdc = $_REQUEST['idOrdc'];
}
$tipoBusq = TIPO_BUSQUEDA_ORDENES_DE_COMPRA;	
if (isset($_GET['tipoBusq']) && $_GET['tipoBusq'] != "") {
	$tipoBusq = $_GET['tipoBusq'];
}
$tipoRequ = TIPO_REQUISICION_TODAS;
if (isset($_GET['tipoRequ']) && $_GET['tipoRequ'] != "") {
	$tipoRequ = $_GET['tipoRequ'];
}
$pagina = "1";
if (isset($_GET['pagina']) && $_GET['pagina'] != "") {
	$pagina = $_GET['pagina'];
}
$dependencia = "";
if (isset($_GET['dependencia']) && $_GET['dependencia'] != "") {
	$dependencia = $_GET['dependencia'];
}
$estado = ESTADO_REQUISICION_NO_REVISADAS;
if (isset($_GET['estado']) && $_GET['estado'] != "") {
	$estado = $_GET['estado'];
}
$rifProveedor = "";
if (isset($_GET['rifProveedor']) && $_GET['rifProveedor'] != "") {
	$rifProveedor = $_GET['rifProveedor'];
}
$nombreProveedor = "";
if (isset($_GET['nombreProveedor']) && $_GET['nombreProveedor'] != "") {
	$nombreProveedor = $_GET['nombreProveedor'];
}
$idItem = "";
if (isset($_GET['idItem']) && $_GET['idItem'] != "") {
	$idItem = $_GET['idItem'];
}
$nombreItem = "";
if (isset($_GET['nombreItem']) && $_GET['nombreItem'] != "") {
	$nombreItem = $_GET['nombreItem'];
}
$controlFechas = "";
if (isset($_GET['controlFechas']) && $_GET['controlFechas'] != "") {
	$controlFechas = $_GET['controlFechas'];
}
$fechaInicio = "";
if (isset($_GET['fechaInicio']) && $_GET['fechaInicio'] != "") {
	$fechaInicio = $_GET['fechaInicio'];
}
$fechaFin = "";
if (isset($_GET['fechaFin']) && $_GET['fechaFin'] != "") {
	$fechaFin = $_GET['fechaFin'];
}
$bandeja = "";
if (isset($_GET['bandeja']) && $_GET['bandeja'] != "") {
	$bandeja = $_GET['bandeja'];
}
$pres_anno=$_SESSION['an_o_presupuesto'];
$estatusDeshabilitado = "0";
ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:. Modificar Orden de Compra</title>
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
<script>
	var codigo = '<?= $codigo?>';
	var codigoCR = '<?= $codigoCR?>';
	var tipoRequ = '<?= $tipoRequ?>';
	var controlFechas = '<?= $controlFechas?>';
	var fechaInicio = '<?= $fechaInicio?>';
	var fechaFin = '<?= $fechaFin?>';
	var dependencia = '<?= $dependencia?>';
	var estado = '<?= $estado?>';
	var rifProveedor = '<?= $rifProveedor?>';
	var nombreProveedor = '<?= $nombreProveedor?>';
	var idItem = '<?= $idItem?>';
	var nombreItem = '<?= $nombreItem?>';
	var pagina = '<?= $pagina?>';
	var tipoBusq = '<?= $tipoBusq?>';
	var idOrdc = '<?= $idOrdc?>';

	function irABandeja(){
		location.href = "bandeja.php";
	}
	
	function irARequisiciones(){
		location.href = "../rqui/busquedas.php?codigo="+codigo+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR+"&estado="+estado+"&rifProveedor="+rifProveedor+"&nombreProveedor="+nombreProveedor+"&idItem="+idItem+"&nombreItem="+nombreItem;
	}

	function anular(){
		if(confirm(pACUTE+'Est'+aACUTE+' seguro que desea anular esta Orden de Compra?.')){
			accion = '<?= ACCION_ANULAR_REQUISICION?>';
			location.href = "modificarOrdenDeCompraAccion.php?codigo="+codigo+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR+"&estado="+estado+"&idOrdc="+idOrdc+"&accion="+accion+"&rifProveedor="+rifProveedor+"&nombreProveedor="+nombreProveedor+"&idItem="+idItem+"&nombreItem="+nombreItem;
		}
	}
</script>
<script language="JavaScript" src="../../js/crearModificarOrdenDeCompra.js"></script>
</head>
<body class="normal">
<?php
if($idOrdc && $idOrdc!=""){
	$query = 	"SELECT ".
					"soc.rif_proveedor_seleccionado, ".
					"soc.criterio_seleccion, ".
					"soc.observaciones, ".
					"soc.fecha, ".
					"soc.justificacion, ".
					"soc.fecha_entrega, ".
					"soc.forma_pago, ".
					"soc.garantia_anticipo, ".
					"soc.lugar_entrega, ".
					"soc.condiciones_entrega, ".
					"soc.otras_garantias, ".
					"soc.rebms_id, ".
					"soc.depe_id, ".
					"soc.usua_login, ".
					"soc.esta_id, ".
					"soc.codigo_analisis_cotizacion, ".	
					"se.esta_nombre, ".
					"se.esta_id, ".
					"srbms.rebms_tipo_imputa, ".
					"srbms.rebms_imp_p_c, ".
					"srbms.rebms_imp_esp, ".
					"srbms.rebms_tipo, ".
					"sd.depe_nombre, ".
					"sp.prov_nombre, ".
					"soc.otras_condiciones_observaciones ".
				"FROM sai_orden_compra soc, sai_req_bi_ma_ser srbms, sai_estado se, sai_dependenci sd, sai_proveedor_nuevo sp ".
				"WHERE ".
				"soc.ordc_id = '".$idOrdc."' AND ".
				"soc.rif_proveedor_seleccionado = sp.prov_id_rif AND ".
				"soc.esta_id = se.esta_id AND ".
				"soc.rebms_id = srbms.rebms_id AND ".
				"srbms.depe_id = sd.depe_id ";
	
	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado, 0);
	
	$rif_proveedor_seleccionado = $row["rif_proveedor_seleccionado"];
	$criterio_seleccion = $row["criterio_seleccion"];
	$observaciones = $row["observaciones"];
	$fecha = $row["fecha"];
	$justificacion = $row["justificacion"];
	$fecha_entrega = $row["fecha_entrega"];
	$forma_pago = $row["forma_pago"];
	$garantia_anticipo = $row["garantia_anticipo"];
	$lugar_entrega = $row["lugar_entrega"];
	$condiciones_entrega = $row["condiciones_entrega"];
	$otras_garantias = $row["otras_garantias"];
	$rebms_id = $row["rebms_id"];
	$depe_id = $row["depe_id"];
	$usua_login = $row["usua_login"];
	$esta_id = $row["esta_id"];
	$codigo_analisis_cotizacion = $row["codigo_analisis_cotizacion"];	
	$esta_nombre = $row["esta_nombre"];
	$esta_id = $row["esta_id"];
	$rebms_tipo_imputa = $row["rebms_tipo_imputa"];
	$rebms_imp_p_c = $row["rebms_imp_p_c"];
	$rebms_imp_esp = $row["rebms_imp_esp"];
	$rebms_tipo = $row["rebms_tipo"];
	$depe_nombre = $row["depe_nombre"];
	$prov_nombre = $row["prov_nombre"];
	$otras_condiciones = $row["otras_condiciones_observaciones"];
	
	if($rebms_tipo_imputa==TIPO_IMPUTACION_PROYECTO){//Proyecto
		$query = "SELECT proy_titulo FROM sai_proyecto WHERE proy_id = '".$rebms_imp_p_c."'";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$proy_titulo = $row["proy_titulo"];
		$query = "SELECT paes_nombre,centro_gestor,centro_costo FROM sai_proy_a_esp WHERE proy_id = '".$rebms_imp_p_c."' AND paes_id = '".$rebms_imp_esp."'";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$aces_nombre = $row["paes_nombre"]."(".$row["centro_gestor"]."-".$row["centro_costo"].")";
	}else if($rebms_tipo_imputa==TIPO_IMPUTACION_ACCION_CENTRALIZADA){//Accion centralizada
		$query = "SELECT aces_nombre,centro_gestor,centro_costo FROM sai_acce_esp WHERE acce_id = '".$rebms_imp_p_c."' AND aces_id = '".$rebms_imp_esp."'";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$aces_nombre = $row["aces_nombre"]."(".$row["centro_gestor"]."-".$row["centro_costo"].")";
		$proy_titulo = $rebms_imp_p_c."-".$row["aces_nombre"];
	}
if ( $bandeja!="true" ) {
?>
<p align="center">
	<a href='javascript: irARequisiciones();'>Volver a los resultados de la b&uacute;squeda</a>
</p>	
<?php
}
$msg=$_GET['msg'];
if($msg=="1"){
	echo "<p class='normal' style='color: red;text-align: center;'>Debe indicar el id de la Orden de Compra.</p>";
}else if($msg=="2A"){
	echo "<p class='normal' style='color: red;text-align: center;'>Debe ingresar al menos una (1) cotizaci&oacute;n.</p>";
}else if($msg=="2B"){
	echo "<p class='normal' style='color: red;text-align: center;'>No se encontraron cotizaciones.</p>";
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
$query = 	"SELECT ".
				"sm.memo_id, ".
				"to_char(sm.memo_fecha_crea,'DD/MM/YYYY') as fecha, ".
				"sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante, ".
				"sm.depe_id, ".
				"sm.memo_asunto, ".
				"sm.memo_contenido ".
			"FROM ".
				"sai_docu_sopor sds, sai_memo sm, sai_usuario su, sai_empleado sem ".
			"WHERE ".
				"sds.doso_doc_fuente = '".$idOrdc."' AND ".
				"sds.doso_doc_soport = sm.memo_id AND ".
				"sm.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula ".
			"ORDER BY sm.memo_fecha_crea DESC LIMIT 1";
$resultado = pg_exec($conexion, $query);
$memos = pg_numrows($resultado);
if($memos>0){
	$row = pg_fetch_array($resultado, 0);
	$sql=	"SELECT swfg.wfgr_descrip, swfg.wfgr_perf ".
			"FROM sai_doc_genera sdg, sai_wfcadena swfc, sai_wfgrupo swfg ".
			"WHERE ".
				"sdg.docg_id = '".$idOrdc."' AND ".
				"sdg.wfca_id = swfc.wfca_id AND ".
				"swfc.wfgr_id = swfg.wfgr_id";
	$resultadoInstancia = pg_exec($conexion,$sql);
	$rowInstancia = pg_fetch_array($resultadoInstancia,0);
	$wfgr_descrip=trim($rowInstancia["wfgr_descrip"]);
	$wfgr_perf=trim($rowInstancia["wfgr_perf"]);
	$depeIdInstancia = $row["depe_id"];//Revisar Dependencia de Instancia que Devuelve
	$query = 	"SELECT depe_nombre FROM sai_dependenci ".
				"WHERE ".
					"depe_id = '".$depeIdInstancia."'";
	$resultadoInstancia = pg_exec($conexion, $query);
	$rowInstancia = pg_fetch_array($resultadoInstancia, 0);
	$depe_nombre = $rowInstancia["depe_nombre"];
?>
<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray">
		<td colspan="2" class="normalNegroNegrita">
			C&Oacute;DIGO DEL &Uacute;LTIMO MEMO: <?= $row["memo_id"]?>
		</td>
	</tr>
	<tr>
		<td width="80px;">
			Fecha:
		</td>
		<td class="normalNegro">
			<input id="fechaOrden" value="<?= $row["fecha"]?>" readonly="readonly"/>
		</td>
	</tr>
	<tr>
		<td>
			De:
		</td>
		<td class="normalNegro">
			<?= $row["solicitante"]?>
		</td>
	</tr>
	<tr>
		<td>
			Instancia:
		</td>
		<td class="normalNegro">
			<?= $wfgr_descrip." en ".$depe_nombre?>
		</td>
	</tr>
	<tr>
		<td>
			Asunto:
		</td>
		<td class="normalNegro">
			<?= $row["memo_asunto"]?>
		</td>
	</tr>
	<tr>
		<td>
			Contenido:
		</td>
		<td class="normalNegro">
			<?= $row["memo_contenido"]?>
		</td>
	</tr>
</table>
<br/>
<?php
	}
?>
	<form id="submitForm" name="submitForm" method="post" action="modificarOrdenDeCompraAccion.php"></form>
	<form name="form" method="post" action="" id="form" onsubmit="return false;">
		<input type="hidden" name="txt_id_tp_p_ac" id="txt_id_tp_p_ac" value=""/>
		<input type="hidden" name="bandeja" id="bandeja" value="<?=$bandeja?>"/>
		<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td colspan="2" class="normalNegroNegrita">
					MODIFICAR ORDEN DE COMPRA C&Oacute;DIGO: <?= $idOrdc?> N&deg; <?= $codigo_analisis_cotizacion?>
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
																		"ssc.rebms_id = '".$rebms_id."' ".
																		"AND ssc.soco_id = sscp.soco_id ".
																		"AND sscp.beneficiario_rif = sp.prov_id_rif ".
																		"AND sp.prov_esta_id <> ".$estadoInactivo." ".
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
								&nbsp;Fecha de Cotizaci&oacute;n<span class="peq_naranja">(*)</span>
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
								<table align="center" class="tablaalertas" width="100%" id="tbl_mod">
									<tr class="td_gray normalNegrita">
										<td align="center">
											<?php 
											if($rebms_tipo==TIPO_REQUISICION_COMPRA){
												echo "C&oacute;digo de Art&iacute;culo/Bien";
											}else if($rebms_tipo==TIPO_REQUISICION_SERVICIO){
												echo "C&oacute;digo de Servicio";
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
											Cantidad Total Requerida
										</td>
										<td align="center">
											Cantidad Solicitada
										</td>
										<td align="center">
											Cantidad Requerida Faltante
										</td>
										<td align="center">
											Unidad
										</td>
										<td align="center">
											Cantidad Unitaria Cotizada
										</td>
										<td align="center">
											Precio Unitario
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
													"soc.rebms_id = '".$rebms_id."' AND ".
													"soc.rif_proveedor_seleccionado = sc.rif_proveedor AND ".
													"soc.ordc_id = sc.ordc_id AND ".
													"sc.id_cotizacion = sci.id_cotizacion";
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
															"sri.rebms_id = '".$rebms_id."' AND ".
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
															"sri.rebms_id = '".$rebms_id."' AND ".
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
														"sri.rebms_id = '".$rebms_id."' AND ".
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
											<input type="hidden" id="txt_id_art<?=$i?>" name="txt_id_art<?=$i?>" value="<?=$row["id"]?>"/>
											<?=$row["id"]?>
										</td>
										<td valign="top" align="left" class="normalNegro">
											<input type="hidden" id="txt_nb_art<?=$i?>" name="txt_nb_art<?=$i?>" value="<?=$row["nombre"]?>"/>
											<?=$row["nombre"]?>
										</td>
										<td valign="top" align="center" class="normalNegro">
											<input type="hidden" id="txt_id_pda<?=$i?>" name="txt_id_pda<?=$i?>" value="<?=$row["id_partida"]?>"/>
											<?=$row["id_partida"]?>
										</td>
										<td valign="top" align="left" class="normalNegro">
											<input type="hidden" id="txt_nb_pda<?=$i?>" name="txt_nb_pda<?=$i?>" value="<?=$row["nombre_partida"]?>"/>
											<?=$row["nombre_partida"]?>
										</td>
										<td valign="top" align="left" class="normalNegro">
											<?=$row["descripcion"]?>
										</td>
										<td valign="top" align="center" class="normalNegro">
											<input type="hidden" id="txt_cantidad<?=$i?>" name="txt_cantidad<?=$i?>" value="<?=$row["cantidad"]?>"/>
											<?=$row["cantidad"]?>
										</td>
										<td valign="top" align="center" class="normalNegro">
											<input type="hidden" id="cantidadSolicitada<?=$i?>" name="cantidadSolicitada<?=$i?>" value="<?=$row["cantidad_solicitada"]?>"/>
											<?=$row["cantidad_solicitada"]?>
										</td>
										<td valign="top" align="center" class="normalNegro">
											<input type="hidden" id="cantidadRequerida<?=$i?>" name="cantidadRequerida<?=$i?>" value="<?=($row["cantidad"]-$row["cantidad_solicitada"]>0)?$row["cantidad"]-$row["cantidad_solicitada"]:"0"?>"/>
											<?=($row["cantidad"]-$row["cantidad_solicitada"]>0)?$row["cantidad"]-$row["cantidad_solicitada"]:"0"?>
										</td>
										<td valign="top" align="center" class="normalNegro">
											<input class="normalNegro" type="text" id="unidad<?=$row["numero_item"]?>" name="unidad<?=$row["numero_item"]?>" size="8" class="ptotal" onkeyup="validarInteger(this);verificarMenorQue(this,<?= ($row["cantidad"])?>,'El valor del campo Unidad multiplicado por la Cantidad Unitaria Cotizada no puede ser superior a la Cantidad Total Requerida','cantidadUnitaria<?=$row["numero_item"]?>');" maxlength="10"/>
										</td>
										<td valign="top" align="center" class="normalNegro">
											<input class="normalNegro" type="text" id="cantidadUnitaria<?=$row["numero_item"]?>" name="cantidadUnitaria<?=$row["numero_item"]?>" size="8" class="ptotal" onkeyup="validarInteger(this);verificarMenorQue(this,<?= ($row["cantidad"])?>,'El valor del campo cantidad unitaria cotizada no puede ser superior a la cantidad total requerida');" maxlength="10" value=""/>
										</td>
										<td valign="top" align="center" class="normalNegro">
											<input class="normalNegro" type="text" id="precioUnitario<?=$row["numero_item"]?>" name="precioUnitario<?=$row["numero_item"]?>" size="8" class="ptotal" value="" onkeyup="validarDecimal(this);" maxlength="10"/>
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
										
										//De la orden de compra
										$query =	"SELECT ".
														"si.id, ".
														"soci.numero_item, ".
														"si.nombre, ".
														"soci.especificaciones as descripcion, ".
														"sp.part_id as id_partida, ".
														"sp.part_nombre as nombre_partida ".
													"FROM ".
														"sai_orden_compra_item soci, ".
														"sai_item si, ".
														"sai_item_partida sip, ".
														"sai_partida sp ".
													"WHERE ".
														"lower(soci.ordc_id) like '".strtolower($idOrdc)."' AND ".
														"soci.id_item = si.id AND ".
														"soci.id_item = sip.id_item AND ".
														"sip.part_id = sp.part_id AND ".
														"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ".
													"GROUP BY si.id, soci.numero_item, si.nombre, soci.especificaciones, sp.part_id, sp.part_nombre ".
													"ORDER BY si.nombre, soci.numero_item";
										
									$resultadoArticulosAdicionales = pg_exec($conexion, $query);
									$elementosArticulosAdicionales = pg_numrows($resultadoArticulosAdicionales);
									?>
									<script>
										i=0;
									</script>
									<?php 									
									for($i=0;$i<$elementosArticulosAdicionales;$i++){
										$row = pg_fetch_array($resultadoArticulosAdicionales, $i);
									?>
									<tr>
										<td valign="top" align="center" class="normalNegro">
											<!-- <input type="hidden" id="txt_id_art<?=$i?>" name="txt_id_art<?=$i?>" value="<?=$row["id"]?>"/> -->
											<a href="javascript:eliminarItem('<?= ($i+1)?>',<?= $row["id"]?>,<?= $row["numero_item"]?>)">Eliminar</a><br/>
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
										<td valign="top" align="center" class="normalNegro"></td>
										<td valign="top" align="center" class="normalNegro"></td>
										<td valign="top" align="center" class="normalNegro"></td>
										<td valign="top" align="center" class="normalNegro">
											<input class="normalNegro" type="text" id="unidad<?=$row["id"]."-".$row["numero_item"]?>" name="unidad<?=$row["id"]."-".$row["numero_item"]?>" size="8" class="ptotal" onkeyup="validarInteger(this);" maxlength="10"/>
										</td>
										<td valign="top" align="center" class="normalNegro">
											<input class="normalNegro" type="text" id="cantidadUnitaria<?=$row["id"]."-".$row["numero_item"]?>" name="cantidadUnitaria<?=$row["id"]."-".$row["numero_item"]?>" size="8" class="ptotal" onkeyup="validarInteger(this);" maxlength="10" value=""/>
										</td>
										<td valign="top" align="center" class="normalNegro">
											<input class="normalNegro" type="text" id="precioUnitario<?=$row["id"]."-".$row["numero_item"]?>" name="precioUnitario<?=$row["id"]."-".$row["numero_item"]?>" size="8" class="ptotal" value="" onkeyup="validarDecimal(this);" maxlength="10"/>
										</td>
									</tr>
									<script>
										var registro = new Array(6);
										registro[0]='<?=$row["id"]?>';
										registro[1]='<?=$row["nombre"]?>';//nombreItem;
										registro[2]='<?=$row["id_partida"]?>';//idPartida;
										registro[3]='<?=$row["nombre_partida"]?>';//nombrePartida;
										registro[4]='<?=$row["descripcion"]?>';//especificaciones;
										registro[5]='<?=$row["numero_item"]?>';//new Date().getTime();
										articulosOrdenDeCompra[i] = registro;
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
												<br/>&nbsp;Introduzca la partida o una palabra contenida en el nombre del art&iacute;culo, bien o servicio.
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
						<?php 
							$query = 	"SELECT ".
											"to_char(sc.fecha,'DD/MM/YYYY') as fecha, ".
											"sc.id_cotizacion, ".
											"sp.prov_id_rif, ".
											"sp.prov_nombre, ".
											"sc.redondear ".
										"FROM sai_cotizacion sc, sai_proveedor_nuevo sp ".
										"WHERE ".
											"sc.ordc_id = '".$idOrdc."' AND ".
											"sc.rif_proveedor = sp.prov_id_rif ".
										"ORDER BY sp.prov_nombre";
							$resultadoProveedores = pg_exec($conexion, $query);
							$totalProveedores = pg_numrows($resultadoProveedores);
						?>
						<tr class="td_gray normalNegrita" id="header1">
							<td colspan="3" align="center">
								Seleccionar Proveedor<span class="peq_naranja">(*)</span>
								<br/>
								Criterio<span class="peq_naranja">(*)</span>
								<select id="criterio" name="criterio" class="normalNegro">
									<option value="">Seleccione</option>
									<option value="1" <?php if($criterio_seleccion=="1"){echo "selected='selected'";}?>>Precio</option>
									<option value="2" <?php if($criterio_seleccion=="2"){echo "selected='selected'";}?>>Acuerdo de Pago</option>
									<option value="3" <?php if($criterio_seleccion=="3"){echo "selected='selected'";}?>>Cantidad</option>
									<option value="4" <?php if($criterio_seleccion=="4"){echo "selected='selected'";}?>>Fecha Entrega</option>
									<option value="5" <?php if($criterio_seleccion=="5"){echo "selected='selected'";}?>>Marca Reconocida</option>
									<option value="6" <?php if($criterio_seleccion=="6"){echo "selected='selected'";}?>>Otros</option>
								</select>
							</td>
							<script>i=0;</script>
							<?php
								$i=0;
								$redondearProveedores = array();
								while($i<$totalProveedores){
									$row = pg_fetch_array($resultadoProveedores, $i);
									$redondearProveedores[$i] = $row["redondear"];
									if($row["prov_id_rif"]==$rif_proveedor_seleccionado){
										echo "<td align='center' id='header1td".$row["prov_id_rif"]."' colspan='3'><input type='radio' id='proveedorSeleccionado".$row["prov_id_rif"]."' name='proveedorSeleccionado' value='".$row["prov_id_rif"]."' onclick='habiProveedor(this);' checked='checked'/></td>";	
									}else{
										echo "<td align='center' id='header1td".$row["prov_id_rif"]."' colspan='3'><input type='radio' id='proveedorSeleccionado".$row["prov_id_rif"]."' name='proveedorSeleccionado' value='".$row["prov_id_rif"]."' onclick='habiProveedor(this);'/></td>";
									}
							?>
								<script>
									proveedores[i] = '<?=$row["prov_id_rif"]?>';
									nombresProveedores[i] = '<?=$row["prov_nombre"]." (RIF ".strtoupper(substr(trim($row["prov_id_rif"]),0,1))."-".substr(trim($row["prov_id_rif"]),1).")"?>';
									fechaCotizacionProveedores[i] = '<?=$row["fecha"]?>';
									proveedoresRedondear[i] = <?=(($row["redondear"]=="t")?"true":"false")?>;
									i++;
								</script>
							<?php
									$i++;
								}
							?>						
						</tr>
						<tr class="td_gray normalNegrita" id="header2">
							<td colspan="3">
								Proveedor
							</td>
							<?php
								$i=0;
								while($i<$totalProveedores){
									$row = pg_fetch_array($resultadoProveedores, $i);
									echo "<td id='header2td".$row["prov_id_rif"]."' colspan='3' class='normalNegro'>".$row["prov_nombre"]." (RIF ".strtoupper(substr(trim($row["prov_id_rif"]),0,1))."-".substr(trim($row["prov_id_rif"]),1).")<br/>Fecha de cot.: ".$row["fecha"]."<br/><a href='javascript:eliminarCotizacion(\"".$row["prov_id_rif"]."\");'><img src='../../imagenes/delete.png' border='0'/>Eliminar</a></td>";
									$i++;
								}
							?>
						</tr>
						<tr class="td_gray normalNegrita" id="header3">
							<td>
								<?php
									if($rebms_tipo==TIPO_REQUISICION_COMPRA){
										echo "Art&iacute;culo/Bien";
									}else if($rebms_tipo==TIPO_REQUISICION_SERVICIO){
										echo "Servicio";
									}
								?>
							</td>
							<td>Especificaciones</td>
							<td align="center">
								Cantidad Requerida Faltante
							</td>
							<?php
								$i=0;
								while($i<$totalProveedores){
									$row = pg_fetch_array($resultadoProveedores, $i);
									echo 	"<td id='header3cctd".$row["prov_id_rif"]."' align='center' width='60px'>Cantidad Cotizada</td>".
											"<td id='header3prtd".$row["prov_id_rif"]."' align='center' width='60px'>Precio</td>".
											"<td id='header3totd".$row["prov_id_rif"]."' align='center' width='60px'>Total</td>";
									$i++;
								}
							?>
						</tr>
						<tbody id="cotizaciones">
							<?php
							
						function ivaRegistrado($ivas, $nuevoIva){
							$j = 0;
							while($j<sizeof($ivas)){
								if($ivas[$j]==$nuevoIva){
									return $j;
								}
								$j++;
							}
							return -1;
						}
						
						$query =	"SELECT ".
										"scb.iva ".
									"FROM ".
										"sai_cotizacion sc, ".
										"sai_cotizacion_base scb ".
									"WHERE ".
										"sc.ordc_id = '".$idOrdc."' AND ".
										"sc.id_cotizacion = scb.id_cotizacion ".
									"GROUP BY scb.iva ".
									"ORDER BY scb.iva ";
						$resultadoIvas = pg_exec($conexion, $query);
						$totalIvas = pg_numrows($resultadoIvas);
						$ivas = array();
						for($i=0;$i<$totalIvas;$i++){
							$row = pg_fetch_array($resultadoIvas, $i);
							if($row["iva"]!="" && $row["iva"]!="0"){
								$ivas[sizeof($ivas)]=$row["iva"];
							}
						}
						
						$query =	"SELECT ".
										"sc.rif_proveedor, ".
										"scb.iva, ".
										"scb.base ".
									"FROM ".
										"sai_cotizacion sc, ".
										"sai_cotizacion_base scb, ".
										"sai_proveedor_nuevo spr ".
									"WHERE ".
										"sc.ordc_id = '".$idOrdc."' AND ".
										"sc.rif_proveedor = spr.prov_id_rif AND ".
										"sc.id_cotizacion = scb.id_cotizacion ".
									"GROUP BY sc.rif_proveedor, spr.prov_nombre, scb.iva, scb.base ".
									"ORDER BY spr.prov_nombre, scb.iva ";
						$resultadoBases = pg_exec($conexion, $query);
						$totalBases = pg_numrows($resultadoBases);
						
						$basesCotizaciones = array();
						$proveedorAnterior = "";
						for($i=0;$i<$totalBases;$i++){
							$row = pg_fetch_array($resultadoBases, $i);
							if($row["rif_proveedor"]!=$proveedorAnterior){
								$proveedorAnterior = $row["rif_proveedor"];
								$basesCotizaciones[sizeof($basesCotizaciones)]=array();
								for($j=0;$j<sizeof($ivas);$j++){
									$basesCotizaciones[sizeof($basesCotizaciones)-1][$j] = 0;		
								}
								?>
								<script>
									proveedoresIva[proveedoresIva.length] = new Array();
									j = 0;
									while(j<ivas.length){
										proveedoresIva[proveedoresIva.length-1][j] = 0;
										j++;
									}
								</script>
								<?php 
							}
							$indiceIva = ivaRegistrado($ivas, $row["iva"]);
							if($indiceIva!=-1){
								$basesCotizaciones[sizeof($basesCotizaciones)-1][$indiceIva] = $row["base"]*($row["iva"]/100);
								?>
								<script>
									indiceIva = ivaRegistrado(parseFloat(<?= $row["iva"]?>));
									if(indiceIva!=-1){
										ivasBandera[indiceIva] = true;
										proveedoresIva[proveedoresIva.length-1][indiceIva] = <?= $row["base"]?>;
									}
								</script>
								<?php
							}
						}
						
						//De la requisición
						$query =	"SELECT ".
										"sc.rif_proveedor, ".
										"sci.id_item as id, ".
										"sci.numero_item, ".
										"sci.cantidad_cotizada, ".
										"sci.precio, ".
										"sci.unidad, ".
										"spr.prov_nombre, ".
										"si.nombre ".
									"FROM ".
										"sai_cotizacion sc, ".
										"sai_cotizacion_item sci, ".
										"sai_item si, ".
										"sai_item_partida sip, ".
										"sai_partida sp, ".
										"sai_proveedor_nuevo spr ".
									"WHERE ".
										"sc.ordc_id = '".$idOrdc."' AND ".
										"sc.rif_proveedor = spr.prov_id_rif AND ".
										"sc.id_cotizacion = sci.id_cotizacion AND ".
										"sci.id_item = si.id AND ".
										"sci.id_item = sip.id_item AND ".
										"sip.part_id = sp.part_id AND ".
										"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ".
									"GROUP BY spr.prov_nombre, si.nombre, sc.rif_proveedor, sci.id_item, sci.numero_item, sci.cantidad_cotizada, sci.precio, sci.unidad ".
									"ORDER BY spr.prov_nombre, si.nombre";
								
						$resultadoCotizaciones = pg_exec($conexion, $query);
						$totalArticulosCotizaciones = pg_numrows($resultadoCotizaciones);
						
						//De la orden de compra
						$query =	"SELECT ".
										"sc.rif_proveedor, ".
										"scia.id_item as id, ".
										"scia.numero_item, ".
										"scia.cantidad_cotizada, ".
										"scia.precio, ".
										"scia.unidad, ".
										"spr.prov_nombre, ".
										"si.nombre ".
									"FROM ".
										"sai_cotizacion sc, ".
										"sai_cotizacion_item_adicional scia, ".
										"sai_item si, ".
										"sai_item_partida sip, ".
										"sai_partida sp, ".
										"sai_proveedor_nuevo spr ".
									"WHERE ".
										"sc.ordc_id = '".$idOrdc."' AND ".
										"sc.rif_proveedor = spr.prov_id_rif AND ".
										"sc.id_cotizacion = scia.id_cotizacion AND ".
										"scia.id_item = si.id AND ".
										"scia.id_item = sip.id_item AND ".
										"sip.part_id = sp.part_id AND ".
										"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ".
									"GROUP BY spr.prov_nombre, si.nombre, sc.rif_proveedor, scia.id_item, scia.numero_item, scia.cantidad_cotizada, scia.precio, scia.unidad ".
									"ORDER BY spr.prov_nombre, si.nombre, scia.numero_item";
						
						$resultadoCotizacionesAdicionales = pg_exec($conexion, $query);
						$totalArticulosCotizacionesAdicionales = pg_numrows($resultadoCotizacionesAdicionales);
						
						$cotizaciones = array();
						$indicesCotizaciones = array();
						$subtotalesCotizaciones = array();
						$totalesCotizaciones = array();

						$proveedorAnterior = "";
						for($i=0;$i<$totalArticulosCotizaciones;$i++){
							$row = pg_fetch_array($resultadoCotizaciones, $i);
							if($proveedorAnterior!=$row["rif_proveedor"]){
								if($proveedorAnterior != ""){
									$banderaAdicionales = 0;
									for($j=0;$j<$totalArticulosCotizacionesAdicionales;$j++){
										$rowAdicionales = pg_fetch_array($resultadoCotizacionesAdicionales, $j);
										if($proveedorAnterior==$rowAdicionales["rif_proveedor"]){
											$banderaAdicionales = 1;
											$articuloDetalle = array();
											$articuloDetalle[0] = $rowAdicionales["id"];
											$articuloDetalle[1] = $rowAdicionales["cantidad_cotizada"];
											$articuloDetalle[2] = $rowAdicionales["precio"];
											$articuloDetalle[3] = $rowAdicionales["unidad"];
											$articuloDetalle[4] = $rowAdicionales["numero_item"];
											$cotizaciones[sizeof($cotizaciones)-1][sizeof($cotizaciones[sizeof($cotizaciones)-1])]=$articuloDetalle;
											?>
											<script>
												proveedoresArticulos[proveedoresArticulos.length-1][proveedoresArticulos[proveedoresArticulos.length-1].length] = new Array('<?= $rowAdicionales["id"]?>',<?= $rowAdicionales["cantidad_cotizada"]?>,<?= $rowAdicionales["precio"]?>,<?= $rowAdicionales["unidad"]?>,<?= $rowAdicionales["numero_item"]?>,'ordc');
											</script>
											<?php
										}else if($banderaAdicionales==1){
											$banderaAdicionales=2;
										}
										if($banderaAdicionales==2){
											break;
										}
									}
								}
								
								$proveedorAnterior = $row["rif_proveedor"];
								$cotizaciones[sizeof($cotizaciones)]=array();
								$indicesCotizaciones[sizeof($indicesCotizaciones)]=0;
								$subtotalesCotizaciones[sizeof($subtotalesCotizaciones)]=0;
								$totalesCotizaciones[sizeof($totalesCotizaciones)]=0;
						?>
								<script>
									proveedoresArticulos[proveedoresArticulos.length] = new Array();
									proveedoresTotal[proveedoresTotal.length] = 0;
								</script>
						<?php
							}
							$articuloDetalle = array();
							$articuloDetalle[0] = $row["id"];
							$articuloDetalle[1] = $row["cantidad_cotizada"];
							$articuloDetalle[2] = $row["precio"];
							$articuloDetalle[3] = $row["unidad"];
							$articuloDetalle[4] = $row["numero_item"];
							$cotizaciones[sizeof($cotizaciones)-1][sizeof($cotizaciones[sizeof($cotizaciones)-1])]=$articuloDetalle;
						?>
								<script>
									proveedoresArticulos[proveedoresArticulos.length-1][proveedoresArticulos[proveedoresArticulos.length-1].length] = new Array('<?= $row["id"]?>',<?= $row["cantidad_cotizada"]?>,<?= $row["precio"]?>,<?= $row["unidad"]?>,<?= $row["numero_item"]?>,'rqui');
								</script>
						<?php
							
						}
						if($proveedorAnterior != ""){
							$banderaAdicionales = 0;
							for($j=0;$j<$totalArticulosCotizacionesAdicionales;$j++){
								$rowAdicionales = pg_fetch_array($resultadoCotizacionesAdicionales, $j);
								if($proveedorAnterior==$rowAdicionales["rif_proveedor"]){
									$banderaAdicionales = 1;
									$articuloDetalle = array();
									$articuloDetalle[0] = $rowAdicionales["id"];
									$articuloDetalle[1] = $rowAdicionales["cantidad_cotizada"];
									$articuloDetalle[2] = $rowAdicionales["precio"];
									$articuloDetalle[3] = $rowAdicionales["unidad"];
									$articuloDetalle[4] = $rowAdicionales["numero_item"];
									$cotizaciones[sizeof($cotizaciones)-1][sizeof($cotizaciones[sizeof($cotizaciones)-1])]=$articuloDetalle;
									?>
									<script>
									proveedoresArticulos[proveedoresArticulos.length-1][proveedoresArticulos[proveedoresArticulos.length-1].length] = new Array('<?= $rowAdicionales["id"]?>',<?= $rowAdicionales["cantidad_cotizada"]?>,<?= $rowAdicionales["precio"]?>,<?= $rowAdicionales["unidad"]?>,<?= $rowAdicionales["numero_item"]?>,'ordc');
									</script>
									<?php
								}else if($banderaAdicionales==1){
									$banderaAdicionales=2;
								}
								if($banderaAdicionales==2){
									break;
								}
							}
						}
						
						//BUSCAR DETALLE DE COTIZACIONES
						for($i=0;$i<$elementos;$i++){
							$row = pg_fetch_array($resultado, $i);
						?>
						<tr class="normalNegro" id="articulo<?= $row["numero_item"]?>">
							<td width="150px" align="left" valign="top"><?=$row["nombre"]?></td>
							<td width="150px" align="left" valign="top"><?=$row["descripcion"]?></td>
							<td width="50px" align="center" valign="middle"><?=($row["cantidad"]-$row["cantidad_solicitada"]>0)?$row["cantidad"]-$row["cantidad_solicitada"]:"0"?></td>
							<?php
								for($j=0;$j<sizeof($cotizaciones);$j++){
									$rowProveedor = pg_fetch_array($resultadoProveedores, $j);
									if($row["numero_item"]==$cotizaciones[$j][$indicesCotizaciones[$j]][4]){
										echo	"<td id='".$row["numero_item"]."cctd".$rowProveedor["prov_id_rif"]."' align='center'>".$cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][3]."</td>".
												"<td id='".$row["numero_item"]."prtd".$rowProveedor["prov_id_rif"]."' align='center'>".$cotizaciones[$j][$indicesCotizaciones[$j]][2]."</td>";
										if($redondearProveedores[$j]=="t"){
											echo	"<td id='".$row["numero_item"]."totd".$rowProveedor["prov_id_rif"]."' align='center'>".round(($cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3]),2)."</td>";
											$subtotalesCotizaciones[$j]+=$cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3];
											$totalesCotizaciones[$j]+=$cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3];
										}else{
											$textStr = ($cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3])+"";
											if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
												$textStr = substr($textStr,0,strpos($textStr, ".")+3);
											}
											echo	"<td id='".$row["numero_item"]."totd".$rowProveedor["prov_id_rif"]."' align='center'>".$textStr."</td>";
											$subtotalesCotizaciones[$j]+=(float)$textStr;
											$totalesCotizaciones[$j]+=(float)$textStr;
										}
										$indicesCotizaciones[$j]++;
									}else{
										echo	"<td id='".$row["numero_item"]."cctd".$rowProveedor["prov_id_rif"]."' align='center'>&nbsp;</td>".
												"<td id='".$row["numero_item"]."prtd".$rowProveedor["prov_id_rif"]."' align='center'>&nbsp;</td>".
												"<td id='".$row["numero_item"]."totd".$rowProveedor["prov_id_rif"]."' align='center'>&nbsp;</td>";										
									}
								}
							?>
						</tr>
					</tbody>
					<tbody id="articulosOrdenDeCompra">
						<?php
						}
						
						for($i=0;$i<$elementosArticulosAdicionales;$i++){
							$row = pg_fetch_array($resultadoArticulosAdicionales, $i);
						?>
						<tr class="normalNegro" id="articuloCotizacion<?= $row["id"]."-".$row["numero_item"]?>">
							<td width="150px" align="left" valign="top"><?=$row["nombre"]?></td>
							<td width="150px" align="left" valign="top"><?=$row["descripcion"]?></td>
							<td width="50px" align="center" valign="top"><?=$row["cantidad_solicitada"]?></td>
							<?php
								for($j=0;$j<sizeof($cotizaciones);$j++){
									$rowProveedor = pg_fetch_array($resultadoProveedores, $j);
									if($row["numero_item"]==$cotizaciones[$j][$indicesCotizaciones[$j]][4]){
										echo	"<td id='".$row["id"]."-".$row["numero_item"]."cctd".$rowProveedor["prov_id_rif"]."' align='center'>".$cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][3]."</td>".
												"<td id='".$row["id"]."-".$row["numero_item"]."prtd".$rowProveedor["prov_id_rif"]."' align='center'>".$cotizaciones[$j][$indicesCotizaciones[$j]][2]."</td>";
										if($redondearProveedores[$j]=="t"){
											echo	"<td id='".$row["id"]."-".$row["numero_item"]."totd".$rowProveedor["prov_id_rif"]."' align='center'>".round(($cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3]),2)."</td>";
											$subtotalesCotizaciones[$j]+=$cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3];
											$totalesCotizaciones[$j]+=$cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3];
										}else{
											$textStr = ($cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3])+"";
											if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
												$textStr = substr($textStr,0,strpos($textStr, ".")+3);
											}
											echo	"<td id='".$row["id"]."-".$row["numero_item"]."totd".$rowProveedor["prov_id_rif"]."' align='center'>".$textStr."</td>";
											$subtotalesCotizaciones[$j]+=(float)$textStr;
											$totalesCotizaciones[$j]+=(float)$textStr;
										}
										$indicesCotizaciones[$j]++;
									}else{
										echo	"<td id='".$row["id"]."-".$row["numero_item"]."cctd".$rowProveedor["prov_id_rif"]."' align='center'>&nbsp;</td>".
												"<td id='".$row["id"]."-".$row["numero_item"]."prtd".$rowProveedor["prov_id_rif"]."' align='center'>&nbsp;</td>".
												"<td id='".$row["id"]."-".$row["numero_item"]."totd".$rowProveedor["prov_id_rif"]."' align='center'>&nbsp;</td>";										
									}
								}
							?>
						</tr>
						<?php
						}
						?>
						</tbody>
						<tbody id="totales">
						<tr id="subTotal">
							<td align="right" colspan="2">Sub Total</td>
							<td>&nbsp;</td>
							<?php
								//COLOCAR TODOS LOS SUBTOTALES DE LAS COTIZACIONES
								for($i=0;$i<sizeof($subtotalesCotizaciones);$i++){
									$row = pg_fetch_array($resultadoProveedores, $i);
									if($redondearProveedores[$i]=="t"){
										echo "<td id='subTotaltd".$row["prov_id_rif"]."' class='normalNegro' align='right' colspan='3'>".round($subtotalesCotizaciones[$i],2)."</td>";
									}else{
										$textStr = ($subtotalesCotizaciones[$i])+"";
										if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
											$textStr = substr($textStr,0,strpos($textStr, ".")+3);
										}
										echo "<td id='subTotaltd".$row["prov_id_rif"]."' class='normalNegro' align='right' colspan='3'>".$textStr."</td>";
									}
								}
							?>
						</tr>
						<?php
						for($i=0;$i<sizeof($ivas);$i++){
						?>
						<tr id="iva<?= $ivas[$i]?>">
							<td align="right" colspan="2">IVA del <?= $ivas[$i]?>%</td>
							<td>&nbsp;</td>	
							<?php 
								for($j=0;$j<sizeof($basesCotizaciones);$j++){
									$row = pg_fetch_array($resultadoProveedores, $j);
									if($redondearProveedores[$j]=="t"){
										$totalesCotizaciones[$j]+=$basesCotizaciones[$j][$i];
										echo "<td class='normalNegro' align='right' colspan='3' id='iva".$ivas[$i].$row["prov_id_rif"]."'>".round($basesCotizaciones[$j][$i],2)."</td>";
									}else{
										$textStr = ($basesCotizaciones[$j][$i])+"";
										if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
											$textStr = substr($textStr,0,strpos($textStr, ".")+3);
										}
										$totalesCotizaciones[$j]+=(float)$textStr;
										echo "<td class='normalNegro' align='right' colspan='3' id='iva".$ivas[$i].$row["prov_id_rif"]."'>".$textStr."</td>";
									}
								}
							?>
						</tr>
						<?php
						}
						?>
						<tr id="filaTotal">
							<td align="right" colspan="2">Total</td>
							<td>&nbsp;</td>
							<?php 
								for($i=0;$i<$totalProveedores;$i++){
									?>
									<script>
										proveedoresTotal[<?= $i?>] = <?= $totalesCotizaciones[$i]?>;
									</script>
									<?php
									if($redondearProveedores[$i]=="t"){
										echo "<td class='normalNegro' align='right' colspan='3'>".round($totalesCotizaciones[$i],2)."</td>";
									}else{
										$textStr = ($totalesCotizaciones[$i])+"";
										if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
											$textStr = substr($textStr,0,strpos($textStr, ".")+3);
										}
										echo "<td class='normalNegro' align='right' colspan='3'>".$textStr."</td>";
									}
								}
							?>
						</tr>
						<!-- <tr class="td_gray" id="filaFooter">
							<td colspan="<?= 2+(sizeof($cotizaciones)*3)?>" height="15px">&nbsp;</td>
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
						onkeyup="textCounter(this,'remLenO',1000);validarTexto(this);"><?=$observaciones?></textarea><br/>
					<div style="text-align: right;width: 554px"><input type="text" value="1000" class="normalNegro" maxlength="3" size="3" id="remLenO" name="remLenO" readonly="readonly"/></div>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="72" class="normal peq_verde_bold" align="left">
					Datos de la Orden de Compra
				</td>
			</tr>
			<tr>
				<td valign="top">
					Fecha de Entrega:
				</td>
				<td>
					<input type="text" id="fechaEntrega" name="fechaEntrega" class="normalNegro" maxlength="50" size="77" onkeyup="validarTexto(this);" value="<?=$fecha_entrega?>"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					Forma de Pago:
				</td>
				<td>
					<input type="text" id="formaPago" name="formaPago" class="normalNegro" maxlength="50" size="77" onkeyup="validarTexto(this);" value="<?=$forma_pago?>"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					Garant&iacute;a de Anticipo:
				</td>
				<td>
					<input type="text" id="garantiaAnticipo" name="garantiaAnticipo" class="normalNegro" maxlength="100" size="77" onkeyup="validarTexto(this);" value="<?=$garantia_anticipo?>"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					Lugar de Entrega:
				</td>
				<td>
					<input type="text" id="lugarEntrega" name="lugarEntrega" class="normalNegro" maxlength="100" size="77" onkeyup="validarTexto(this);" value="<?=$lugar_entrega?>"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					Condiciones de Entrega:
				</td>
				<td>
					<input type="text" id="condicionesEntrega" name="condicionesEntrega" class="normalNegro" maxlength="100" size="77" onkeyup="validarTexto(this);" value="<?=$condiciones_entrega?>"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					Otras Garant&iacute;as:
				</td>
				<td>
					<input type="text" id="otrasGarantias" name="otrasGarantias" class="normalNegro" maxlength="100" size="77" onkeyup="validarTexto(this);" value="<?=$otras_garantias?>"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					Otras condiciones / Observaciones:
				</td>
				<td>
					<textarea class="normalNegro" id="otrasCondiciones" name="otrasCondiciones" cols="75" rows="6"
						onkeydown="textCounter(this,'remLenC',1000);"
						onkeyup="textCounter(this,'remLenC',1000);validarTexto(this);"><?=$otras_condiciones?></textarea><br/>
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
						onkeyup="textCounter(this,'remLenJ',1000);validarTexto(this);"><?=$justificacion?></textarea><br/>
					<div style="text-align: right;width: 554px"><input type="text" value="1000" class="normalNegro" maxlength="3" size="3" id="remLenJ" name="remLenJ" readonly="readonly"/></div>
				</td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td height="16" colspan="2" align="center">
					<input class="normalNegro" type="button" value="Enviar" onclick="enviar();"/>
					<input class="normalNegro" type="button" value="Anular" onclick="anular();"/>
					<?php 
						  if ( $bandeja!="true" ) {
					?>
							<input type="button" class="normalNegro" value="Cancelar" onclick="irARequisiciones();"/>
					<?php } else { ?>
							<input type="button" class="normalNegro" value="Cancelar" onclick="irABandeja();"/>
					<?php } ?>
				</td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
		</table>
		<br/>
		<span class="peq_naranja">(*)</span> Campo obligatorio
	</form>
<?php
}
?>
</body>
</html>
<?php pg_close($conexion); ?>