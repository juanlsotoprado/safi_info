<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");
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
$accion = "";
if (isset($_REQUEST['accion']) && $_REQUEST['accion'] != "") {
	$accion = $_REQUEST['accion'];
}
$estadia = "";
if (isset($_GET['estadia']) && $_GET['estadia'] != "") {
	$estadia = $_GET['estadia'];
}
$bandeja = "";
if (isset($_GET['bandeja']) && $_GET['bandeja'] != "") {
	$bandeja = $_GET['bandeja'];
}
$user_perfil_id = $_SESSION['user_perfil_id'];
ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:. Orden de Compra</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css"/>
<script><!--
	function irARequisiciones(){
		codigo = '<?= $codigo?>';
		codigoCR = '<?= $codigoCR?>';
		tipoRequ = '<?= $tipoRequ?>';
		controlFechas = '<?= $controlFechas?>';
		fechaInicio = '<?= $fechaInicio?>';
		fechaFin = '<?= $fechaFin?>';
		dependencia = '<?= $dependencia?>';
		estado = '<?= $estado?>';
		rifProveedor = '<?= $rifProveedor?>';
		nombreProveedor = '<?= $nombreProveedor?>';
		idItem = '<?= $idItem?>';
		nombreItem = '<?= $nombreItem?>';
		pagina = '<?= $pagina?>';
		tipoBusq = '<?= $tipoBusq?>';
		location.href = "../rqui/busquedas.php?codigo="+codigo+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR+"&estado="+estado+"&rifProveedor="+rifProveedor+"&nombreProveedor="+nombreProveedor+"&idItem="+idItem+"&nombreItem="+nombreItem;
	}
<?php
if($idOrdc && $idOrdc!=""){
?>
	function generarAnalisisDeCotizacinoPdf1(){
		location.href = "analisisDeCotizacion_PDF.php?tipo=L&idOrdc=<?= $idOrdc?>";
	}
	function generarOrdenDeCompraPdf1(){
		location.href = "ordenDeCompra_PDF.php?tipo=L&idOrdc=<?= $idOrdc?>";
	}
<?php
}
?>
--></script>
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
	
	if($accion!="generar" && $estadia!="true" && $bandeja!="true"){
?>
<p align="center">
	<a href='javascript: irARequisiciones();'>Volver a los resultados de la b&uacute;squeda</a>
</p>
<?php
	}
if($esta_id==7 || $esta_id==15){
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
			<?= $row["fecha"]?>
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
}
?>
<form name="form" method="post" action="" id="form" onsubmit="return false;">
	<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td colspan="2" class="normalNegroNegrita">
				DETALLE DE ORDEN DE COMPRA C&Oacute;DIGO: <?= $idOrdc?>
			</td>
		</tr>
		<tr>
			<td align="left" height="24" width="200px;">
				C&oacute;digo de la Orden de Compra: 
			</td>
			<td class="normalNegro"><?= $idOrdc?></td>
		</tr>
		<tr>
			<td align="left" height="24">
				Estado de la Orden de Compra: 
			</td>
			<td class="normalNegro"><?= $esta_nombre?></td>
		</tr>
		<tr>
			<td align="left" height="24">
				C&oacute;digo de la Requisici&oacute;n: 
			</td>
			<td class="normalNegro"><?= $rebms_id?></td>
		</tr>
		<tr>
			<td align="left" height="24">
				Tipo de Requisici&oacute;n: 
			</td>
			<td class="normalNegro">
				<?php
					if($rebms_tipo == TIPO_REQUISICION_COMPRA){ echo "Compra";}
					else if($rebms_tipo == TIPO_REQUISICION_SERVICIO){ echo "Servicio";}
				?>
			</td>
		</tr>
		<tr>
			<td align="left" height="24">
				Proyecto o Acci&oacute;n Centralizada: 
			</td>
			<td class="normalNegro"><?= $proy_titulo?></td>
		</tr>
		<tr>
			<td align="left" height="24">
				Acci&oacute;n Espec&iacute;fica: 
			</td>
			<td class="normalNegro"><?= $aces_nombre?></td>
		</tr>
		<?php /*?>
		<tr>
			<td colspan="2" height="24" class="normal peq_verde_bold" align="left">
				Detalle de Cotizaci&oacute;n
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="tablaalertas">
					<?php 
						$query = 	"SELECT ".
										"to_char(sc.fecha,'DD/MM/YYYY') as fecha, ".
										"sc.redondear ".
									"FROM sai_cotizacion sc ".
									"WHERE ".
										"sc.ordc_id = '".$idOrdc."' AND ".
										"sc.rif_proveedor = '".$rif_proveedor_seleccionado."' ";
						$resultadoProveedores = pg_exec($conexion, $query);
						$totalProveedores = pg_numrows($resultadoProveedores);
					?>
					<tr class="td_gray normalNegrita" id="header2">
						<td colspan="5">
							Proveedor
						</td>
						<?php 
							$redondearProveedores = "t";
							if($totalProveedores>0){
								$row = pg_fetch_array($resultadoProveedores, 0);
								$redondearProveedores = $row["redondear"];
								echo "<td colspan='4' class='normalNegro'>".$prov_nombre." (RIF ".strtoupper(substr(trim($rif_proveedor_seleccionado),0,1))."-".substr(trim($rif_proveedor_seleccionado),1).")<br/>Fecha de cot.: ".$row["fecha"]."</td>";
							}
						?>
					</tr>
					<tr class="td_gray normalNegrita" id="header3">
						<td align='center'>C&oacute;digo</td>
						<td align='center'>Nombre</td>
						<td align='center'>Partida</td>
						<td align='center'>Denominaci&oacute;n</td>
						<td align='center'>Especificaciones</td>
						<td align='center' width='60px'>Unidad</td>
						<td align='center' width='60px'>Cantidad</td>
						<td align='center' width='60px'>Precio</td>
						<td align='center' width='60px'>Total</td>
					</tr>
					<tbody id="cotizaciones">
						<?php
						
						$query =	"SELECT ".
										"scb.iva, ".
										"scb.base ".
									"FROM ".
										"sai_cotizacion sc, ".
										"sai_cotizacion_base scb ".
									"WHERE ".
										"sc.ordc_id = '".$idOrdc."' AND ".
										"sc.rif_proveedor = '".$rif_proveedor_seleccionado."' AND ".
										"sc.id_cotizacion = scb.id_cotizacion ".
									"ORDER BY scb.iva ";
						$resultadoBases = pg_exec($conexion, $query);
						$totalBases = pg_numrows($resultadoBases);
						
						$ivas = array();
						$basesCotizaciones = array();
						for($i=0;$i<$totalBases;$i++){
							$row = pg_fetch_array($resultadoBases, $i);
							if($row["iva"]!="" && $row["iva"]!="0"){
								$basesCotizaciones[sizeof($basesCotizaciones)] = $row["base"]*($row["iva"]/100);
								$ivas[sizeof($ivas)]=$row["iva"];
							}
						}
						
						//De la requisición
						$query =	"SELECT ".
										"sci.id_item as id, ".
										"sci.numero_item, ".
										"sci.cantidad_cotizada, ".
										"sci.precio, ".
										"sci.unidad, ".
										"si.nombre, ".
										"sp.part_id as id_partida, ".
										"sp.part_nombre as nombre_partida, ".
										"sri.rbms_item_desc as descripcion ".
									"FROM ".
										"sai_cotizacion sc, ".
										"sai_cotizacion_item sci, ".
										"sai_rqui_items sri, ".
										"sai_item si, ".
										"sai_item_partida sip, ".
										"sai_partida sp ".
									"WHERE ".
										"sc.ordc_id = '".$idOrdc."' AND ".
										"sc.rif_proveedor = '".$rif_proveedor_seleccionado."' AND ".
										"sc.id_cotizacion = sci.id_cotizacion AND ".
										"sci.numero_item = sri.numero_item AND ".
										"sci.id_item = si.id AND ".
										"sci.id_item = sip.id_item AND ".
										"sip.part_id = sp.part_id AND ".
										"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ".
									"GROUP BY sci.id_item, sci.numero_item, sci.cantidad_cotizada, sci.precio, sci.unidad, si.nombre, sp.part_id, sp.part_nombre, sri.rbms_item_desc ".
									"ORDER BY si.nombre, sci.numero_item";
						
						$resultadoCotizaciones = pg_exec($conexion, $query);
						$totalArticulosCotizaciones = pg_numrows($resultadoCotizaciones);
						
						//De la orden de compra
						$query =	"SELECT ".
										"scia.id_item as id, ".
										"scia.numero_item, ".
										"scia.cantidad_cotizada, ".
										"scia.precio, ".
										"scia.unidad, ".
										"si.nombre, ".
										"sp.part_id as id_partida, ".
										"sp.part_nombre as nombre_partida, ".
										"soci.especificaciones as descripcion ".
									"FROM ".
										"sai_cotizacion sc, ".
										"sai_cotizacion_item_adicional scia, ".
										"sai_orden_compra_item soci, ".
										"sai_item si, ".
										"sai_item_partida sip, ".
										"sai_partida sp ".
									"WHERE ".
										"sc.ordc_id = '".$idOrdc."' AND ".
										"sc.rif_proveedor = '".$rif_proveedor_seleccionado."' AND ".
										"sc.id_cotizacion = scia.id_cotizacion AND ".
										"sc.ordc_id = soci.ordc_id AND ".
										"scia.numero_item = soci.numero_item AND ".
										"scia.id_item = si.id AND ".
										"scia.id_item = sip.id_item AND ".
										"sip.part_id = sp.part_id AND ".
										"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ".
									"GROUP BY scia.id_item, scia.numero_item, scia.cantidad_cotizada, scia.precio, scia.unidad, si.nombre, sp.part_id, sp.part_nombre, soci.especificaciones ".
									"ORDER BY si.nombre, scia.numero_item";
						
						$resultadoCotizacionesAdicionales = pg_exec($conexion, $query);
						$totalArticulosCotizacionesAdicionales = pg_numrows($resultadoCotizacionesAdicionales);
											
						$subtotal = 0;
						$total = 0;
	
						for($i=0;$i<$totalArticulosCotizaciones;$i++){
							$row = pg_fetch_array($resultadoCotizaciones, $i);
						?>
						<tr class="normalNegro" id="articulo<?= $row["numero_item"]?>">
							<td align="center" valign="top">
								<?=$row["id"]?>
							</td>
							<td align="left" valign="top">
								<?=$row["nombre"]?>
							</td>
							<td align="center" valign="top">
								<?=$row["id_partida"]?>
							</td>
							<td align="left" valign="top">
								<?=$row["nombre_partida"]?>
							</td>
							<td align="left" valign="top">
								<?=$row["descripcion"]?>
							</td>
							<?php
								echo	"<td class='normalNegro' align='center'>".(($row["unidad"]>1)?$row["unidad"]:"&nbsp;")."</td>".
										"<td class='normalNegro' align='center'>".$row["cantidad_cotizada"]."</td>".
										"<td class='normalNegro' align='center'>".$row["precio"]."</td>";	
								if($redondearProveedores=="t"){
									echo	"<td class='normalNegro' align='center'>".round(($row["cantidad_cotizada"]*$row["precio"]*$row["unidad"]),2)."</td>";
									$subtotal += $row["cantidad_cotizada"]*$row["precio"]*$row["unidad"];
									$total += $row["cantidad_cotizada"]*$row["precio"]*$row["unidad"];
								}else{
									$textStr = ($row["cantidad_cotizada"]*$row["precio"]*$row["unidad"])+"";
									if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
										$textStr = substr($textStr,0,strpos($textStr, ".")+3);
									}
									echo	"<td class='normalNegro' align='center'>".$textStr."</td>";
									$subtotal+=(float)$textStr;
									$total+=(float)$textStr;
								}
							?>
						</tr>
						<?php
						}
						
						for($i=0;$i<$totalArticulosCotizacionesAdicionales;$i++){
							$row = pg_fetch_array($resultadoCotizacionesAdicionales, $i);
						?>
						<tr class="normalNegro" id="articulo<?= $row["numero_item"]?>">
							<td align="center" valign="top">
								<?=$row["id"]?>
							</td>
							<td align="left" valign="top">
								<?=$row["nombre"]?>
							</td>
							<td align="center" valign="top">
								<?=$row["id_partida"]?>
							</td>
							<td align="left" valign="top">
								<?=$row["nombre_partida"]?>
							</td>
							<td align="left" valign="top">
								<?=$row["descripcion"]?>
							</td>
							<?php
								echo	"<td class='normalNegro' align='center'>".(($row["unidad"]>1)?$row["unidad"]:"&nbsp;")."</td>".
										"<td class='normalNegro' align='center'>".$row["cantidad_cotizada"]."</td>".
										"<td class='normalNegro' align='center'>".$row["precio"]."</td>";
								if($redondearProveedores=="t"){
									echo	"<td class='normalNegro' align='center'>".round(($row["cantidad_cotizada"]*$row["precio"]*$row["unidad"]),2)."</td>";
									$subtotal += $row["cantidad_cotizada"]*$row["precio"]*$row["unidad"];
									$total += $row["cantidad_cotizada"]*$row["precio"]*$row["unidad"];
								}else{
									$textStr = ($row["cantidad_cotizada"]*$row["precio"]*$row["unidad"])+"";
									if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
										$textStr = substr($textStr,0,strpos($textStr, ".")+3);
									}
									echo	"<td class='normalNegro' align='center'>".$textStr."</td>";
									$subtotal+=(float)$textStr;
									$total+=(float)$textStr;
								}
							?>
						</tr>
						<?php
						}
						?>
					</tbody>
					<tr id="subTotal">
						<td align="right" colspan="5">Sub Total</td>
						<?php
							if($redondearProveedores=="t"){
								echo "<td class='normalNegro' align='right' colspan='4'>".round($subtotal,2)."</td>";
							}else{
								$textStr = ($subtotal)+"";
								if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
									$textStr = substr($textStr,0,strpos($textStr, ".")+3);
								}
								echo "<td class='normalNegro' align='right' colspan='4'>".$textStr."</td>";
							}
						?>
					</tr>
					<?php
					for($i=0;$i<sizeof($ivas);$i++){
					?>
					<tr id="iva<?= $iva[$i]?>">
						<td align="right" colspan="5">IVA del <?= $ivas[$i]?>%</td>
						<?php 
							if($redondearProveedores=="t"){
								$total += $basesCotizaciones[$i];
								echo "<td class='normalNegro' align='right' colspan='4'>".round($basesCotizaciones[$i],2)."</td>";
							}else{
								$textStr = ($basesCotizaciones[$i])+"";
								if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
									$textStr = substr($textStr,0,strpos($textStr, ".")+3);
								}
								$total+=(float)$textStr;
								echo "<td class='normalNegro' align='right' colspan='4'>".$textStr."</td>";
							}
						?>
					</tr>
					<?php
					}
					?>
					<tr id="total">
						<td align="right" colspan="5">Total</td>
						<?php 
							if($redondearProveedores=="t"){
								echo "<td class='normalNegro' align='right' colspan='4'>".round($total,2)."</td>";
							}else{
								$textStr = ($total)+"";
								if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
									$textStr = substr($textStr,0,strpos($textStr, ".")+3);
								}
								echo "<td class='normalNegro' align='right' colspan='4'>".$textStr."</td>";
							}
						?>
					</tr>
					<!-- <tr class="td_gray">
						<td id="footer" colspan="9">&nbsp;</td>
					</tr> -->
				</table>
			</td>
		</tr><?php */ ?>
		<tr>
			<td align="left" height="24">
				Factura: 
			</td>
			<td class="normalNegro" id="tdFactura"><?= $factura?></td>
		</tr>
		<tr>
			<td align="left" height="24">
				Concepto: 
			</td>
			<td class="normalNegro" id="tdConcepto"><?= $concepto?></td>
		</tr>
		<tr>
			<td colspan="2" height="24" class="normal peq_verde_bold" align="left">
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
					<tr class="td_gray" id="header2">
						<td class="td_gray normalNegrita" colspan="5">
							Proveedor
						</td>
						<?php 
							$i=0;
							$redondearProveedores = array();
							while($i<$totalProveedores){
								$row = pg_fetch_array($resultadoProveedores, $i);
								$redondearProveedores[$i] = $row["redondear"];
								echo "<td colspan='4' class='normalNegro'>".$row["prov_nombre"]." (RIF ".strtoupper(substr(trim($row["prov_id_rif"]),0,1))."-".substr(trim($row["prov_id_rif"]),1).")<br/>Fecha de cot.: ".$row["fecha"]."<br/>Redondear: ".(($row["redondear"]=="t")?"Si":"No")."</td>";
								$i++;
							}
						?>
					</tr>
					<tr class="td_gray normalNegrita" id="header3">
						<td align='center'>C&oacute;digo</td>
						<td align='center'>Nombre</td>
						<td align='center'>Partida</td>
						<td align='center'>Denominaci&oacute;n</td>
						<td align='center'>Especificaciones</td>
						<?php
							$i=0;
							while($i<$totalProveedores){
								$row = pg_fetch_array($resultadoProveedores, $i);
								echo 	"<td align='center' width='60px'>Unidad</td>".
										"<td align='center' width='60px'>Cantidad</td>".
										"<td align='center' width='60px'>Precio</td>".
										"<td align='center' width='60px'>Total</td>";
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
							}
							$indiceIva = ivaRegistrado($ivas, $row["iva"]);
							if($indiceIva!=-1){
								$basesCotizaciones[sizeof($basesCotizaciones)-1][$indiceIva] = $row["base"]*($row["iva"]/100);
							}
						}
						
						//De la requisición
						$query =	"SELECT ".
										"sci.id_item as id, ".
										"sci.numero_item, ".
										"si.nombre, ".
										"sp.part_id as id_partida, ".
										"sp.part_nombre as nombre_partida, ".
										"sri.rbms_item_desc as descripcion ".
									"FROM ".
										"sai_cotizacion sc, ".
										"sai_cotizacion_item sci, ".
										"sai_rqui_items sri, ".
										"sai_item si, ".
										"sai_item_partida sip, ".
										"sai_partida sp ".
									"WHERE ".
										"sc.ordc_id = '".$idOrdc."' AND ".
										"sc.id_cotizacion = sci.id_cotizacion AND ".
										"sci.numero_item = sri.numero_item AND ".
										"sci.id_item = si.id AND ".
										"sci.id_item = sip.id_item AND ".
										"sip.part_id = sp.part_id AND ".
										"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ".
									"GROUP BY sci.id_item, sci.numero_item, si.nombre, sp.part_id, sp.part_nombre, sri.rbms_item_desc ".
									"ORDER BY si.nombre, sci.numero_item";
						
						$resultadoArticulos = pg_exec($conexion, $query);
						$totalArticulos = pg_numrows($resultadoArticulos);
						
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
									"ORDER BY spr.prov_nombre, si.nombre, sci.numero_item";
						
						$resultadoCotizaciones = pg_exec($conexion, $query);
						$totalArticulosCotizaciones = pg_numrows($resultadoCotizaciones);
						
						//De la orden de compra
						$query =	"SELECT ".
										"soci.id_item as id, ".
										"soci.numero_item, ".
										"si.nombre, ".
										"sp.part_id as id_partida, ".
										"sp.part_nombre as nombre_partida, ".
										"soci.especificaciones as descripcion ".
									"FROM ".
										"sai_orden_compra_item soci, ".
										"sai_item si, ".
										"sai_item_partida sip, ".
										"sai_partida sp ".
									"WHERE ".
										"soci.ordc_id = '".$idOrdc."' AND ".
										"soci.id_item = si.id AND ".
										"soci.id_item = sip.id_item AND ".
										"sip.part_id = sp.part_id AND ".
										"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ".
									"GROUP BY soci.id_item, soci.numero_item, si.nombre, sp.part_id, sp.part_nombre, soci.especificaciones ".
									"ORDER BY si.nombre, soci.numero_item";
						
						$resultadoArticulosAdicionales = pg_exec($conexion, $query);
						$totalArticulosAdicionales = pg_numrows($resultadoArticulosAdicionales);

						$query =	"SELECT ".
										"sc.rif_proveedor, ".
										"scia.id_item as id, ".
										"scia.numero_item, ".
										"scia.cantidad_cotizada, ".
										"scia.precio, ".
										"scia.unidad, ".
										"spr.prov_nombre ".
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
							}
							$articuloDetalle = array();
							$articuloDetalle[0] = $row["id"];
							$articuloDetalle[1] = $row["cantidad_cotizada"];
							$articuloDetalle[2] = $row["precio"];
							$articuloDetalle[3] = $row["unidad"];
							$articuloDetalle[4] = $row["numero_item"];
							$cotizaciones[sizeof($cotizaciones)-1][sizeof($cotizaciones[sizeof($cotizaciones)-1])]=$articuloDetalle;
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
								}else if($banderaAdicionales==1){
									$banderaAdicionales=2;
								}
								if($banderaAdicionales==2){
									break;
								}
							}
						}else{
							for($i=0;$i<$totalArticulosCotizacionesAdicionales;$i++){
								$rowAdicionales = pg_fetch_array($resultadoCotizacionesAdicionales, $i);
								if($proveedorAnterior!=$rowAdicionales["rif_proveedor"]){
									$proveedorAnterior = $rowAdicionales["rif_proveedor"];
									$cotizaciones[sizeof($cotizaciones)]=array();
									$indicesCotizaciones[sizeof($indicesCotizaciones)]=0;
									$subtotalesCotizaciones[sizeof($subtotalesCotizaciones)]=0;
									$totalesCotizaciones[sizeof($totalesCotizaciones)]=0;
								}
								$articuloDetalle = array();
								$articuloDetalle[0] = $rowAdicionales["id"];
								$articuloDetalle[1] = $rowAdicionales["cantidad_cotizada"];
								$articuloDetalle[2] = $rowAdicionales["precio"];
								$articuloDetalle[3] = $rowAdicionales["unidad"];
								$articuloDetalle[4] = $rowAdicionales["numero_item"];
								$cotizaciones[sizeof($cotizaciones)-1][sizeof($cotizaciones[sizeof($cotizaciones)-1])]=$articuloDetalle;
							}
							
							/*for($j=0;$j<$totalArticulosCotizacionesAdicionales;$j++){
								$rowAdicionales = pg_fetch_array($resultadoCotizacionesAdicionales, $j);
								$articuloDetalle = array();
								$articuloDetalle[0] = $rowAdicionales["id"];
								$articuloDetalle[1] = $rowAdicionales["cantidad_cotizada"];
								$articuloDetalle[2] = $rowAdicionales["precio"];
								$articuloDetalle[3] = $rowAdicionales["unidad"];
								$articuloDetalle[4] = $rowAdicionales["numero_item"];
								$cotizaciones[sizeof($cotizaciones)-1][sizeof($cotizaciones[sizeof($cotizaciones)-1])]=$articuloDetalle;
							}*/
						}
						
						for($i=0;$i<$totalArticulos;$i++){
							$row = pg_fetch_array($resultadoArticulos, $i);
						?>
						<tr class="normalNegro" id="articulo<?= $row["numero_item"]?>">
							<td align="center" valign="top">
								<?=$row["id"]?>
							</td>
							<td align="left" valign="top">
								<?=$row["nombre"]?>
							</td>
							<td align="center" valign="top">
								<?=$row["id_partida"]?>
							</td>
							<td align="left" valign="top">
								<?=$row["nombre_partida"]?>
							</td>
							<td align="left" valign="top">
								<?=$row["descripcion"]?>
							</td>
							<?php
								for($j=0;$j<sizeof($cotizaciones);$j++){
									if($row["numero_item"]==$cotizaciones[$j][$indicesCotizaciones[$j]][4]){
										echo	"<td class='normalNegro' align='center'>".(($cotizaciones[$j][$indicesCotizaciones[$j]][3]>1)?$cotizaciones[$j][$indicesCotizaciones[$j]][3]:"&nbsp;")."</td>".
												"<td class='normalNegro' align='center'>".$cotizaciones[$j][$indicesCotizaciones[$j]][1]."</td>".
												"<td class='normalNegro' align='center'>".$cotizaciones[$j][$indicesCotizaciones[$j]][2]."</td>";
										if($redondearProveedores[$j]=="t"){
											echo	"<td class='normalNegro' align='center'>".round(($cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3]),2)."</td>";
											$subtotalesCotizaciones[$j]+=$cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3];
											$totalesCotizaciones[$j]+=$cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3];
										}else{
											$textStr = ($cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3])+"";
											if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
												$textStr = substr($textStr,0,strpos($textStr, ".")+3);
											}
											echo	"<td class='normalNegro' align='center'>".$textStr."</td>";
											$subtotalesCotizaciones[$j]+=(float)$textStr;
											$totalesCotizaciones[$j]+=(float)$textStr;
										}
										$indicesCotizaciones[$j]++;
									}else{
										echo	"<td class='normalNegro' align='center'>&nbsp;</td>".
												"<td class='normalNegro' align='center'>&nbsp;</td>".
												"<td class='normalNegro' align='center'>&nbsp;</td>".
												"<td class='normalNegro' align='center'>&nbsp;</td>";										
									}
								}
							?>
						</tr>
						<?php
						}
						
						for($i=0;$i<$totalArticulosAdicionales;$i++){
							$row = pg_fetch_array($resultadoArticulosAdicionales, $i);
						?>
						<tr class="normalNegro" id="articulo<?= $row["numero_item"]?>">
							<td align="center" valign="top">
								<?=$row["id"]?>
							</td>
							<td align="left" valign="top">
								<?=$row["nombre"]?>
							</td>
							<td align="center" valign="top">
								<?=$row["id_partida"]?>
							</td>
							<td align="left" valign="top">
								<?=$row["nombre_partida"]?>
							</td>
							<td align="left" valign="top">
								<?=$row["descripcion"]?>
							</td>
							<?php
								for($j=0;$j<sizeof($cotizaciones);$j++){
									if($row["numero_item"]==$cotizaciones[$j][$indicesCotizaciones[$j]][4]){
										echo	"<td class='normalNegro' align='center'>".(($cotizaciones[$j][$indicesCotizaciones[$j]][3]>1)?$cotizaciones[$j][$indicesCotizaciones[$j]][3]:"&nbsp;")."</td>".
												"<td class='normalNegro' align='center'>".$cotizaciones[$j][$indicesCotizaciones[$j]][1]."</td>".
												"<td class='normalNegro' align='center'>".$cotizaciones[$j][$indicesCotizaciones[$j]][2]."</td>";
										if($redondearProveedores[$j]=="t"){
											echo	"<td class='normalNegro' align='center'>".round(($cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3]),2)."</td>";
											$subtotalesCotizaciones[$j]+=$cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3];
											$totalesCotizaciones[$j]+=$cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3];
										}else{
											$textStr = ($cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3])+"";
											if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
												$textStr = substr($textStr,0,strpos($textStr, ".")+3);
											}
											echo	"<td class='normalNegro' align='center'>".$textStr."</td>";
											$subtotalesCotizaciones[$j]+=(float)$textStr;
											$totalesCotizaciones[$j]+=(float)$textStr;
										}
										$indicesCotizaciones[$j]++;
									}else{
										echo	"<td class='normalNegro' align='center'>&nbsp;</td>".
												"<td class='normalNegro' align='center'>&nbsp;</td>".
												"<td class='normalNegro' align='center'>&nbsp;</td>".
												"<td class='normalNegro' align='center'>&nbsp;</td>";										
									}
								}
							?>
						</tr>
						<?php
						}
						?>
					</tbody>
					<tr id="subTotal">
						<td align="right" colspan="5">Sub Total</td>
						<?php
							for($i=0;$i<sizeof($subtotalesCotizaciones);$i++){
								if($redondearProveedores[$i]=="t"){
									echo "<td class='normalNegro' align='right' colspan='4'>".round($subtotalesCotizaciones[$i],2)."</td>";
								}else{
									$textStr = ($subtotalesCotizaciones[$i])+"";
									if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
										$textStr = substr($textStr,0,strpos($textStr, ".")+3);
									}
									echo "<td class='normalNegro' align='right' colspan='4'>".$textStr."</td>";
								}
							}
						?>
					</tr>
					<?php
					for($i=0;$i<sizeof($ivas);$i++){
					?>
					<tr id="iva<?= $iva[$i]?>">
						<td align="right" colspan="5">IVA del <?= $ivas[$i]?>%</td>
						<?php 
							for($j=0;$j<sizeof($basesCotizaciones);$j++){
								if($redondearProveedores[$j]=="t"){
									$totalesCotizaciones[$j]+=$basesCotizaciones[$j][$i];
									echo "<td class='normalNegro' align='right' colspan='4'>".round($basesCotizaciones[$j][$i],2)."</td>";
								}else{
									$textStr = ($basesCotizaciones[$j][$i])+"";
									if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
										$textStr = substr($textStr,0,strpos($textStr, ".")+3);
									}
									$totalesCotizaciones[$j]+=(float)$textStr;
									echo "<td class='normalNegro' align='right' colspan='4'>".$textStr."</td>";
								}
							}
						?>
					</tr>
					<?php
					}
					?>
					<tr id="total">
						<td align="right" colspan="5">Total</td>
						<?php 
							for($i=0;$i<$totalProveedores;$i++){
								if($redondearProveedores[$i]=="t"){
									echo "<td class='normalNegro' align='right' colspan='4'>".round($totalesCotizaciones[$i],2)."</td>";
								}else{
									$textStr = ($totalesCotizaciones[$i])+"";
									if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
										$textStr = substr($textStr,0,strpos($textStr, ".")+3);
									}
									echo "<td class='normalNegro' align='right' colspan='4'>".$textStr."</td>";
								}
							}
						?>
					</tr>
					<!-- <tr class="td_gray">
						<td id="footer" colspan="<?= 5+(sizeof($cotizaciones)*4)?>">&nbsp;</td>
					</tr> -->
				</table>
			</td>
		</tr>
		<tr>
			<td height="24">
				Proveedor seleccionado:
			</td>
			<td class="normalNegro"><?=$prov_nombre." (RIF ".strtoupper(substr(trim($rif_proveedor_seleccionado),0,1))."-".substr(trim($rif_proveedor_seleccionado),1).")"?></td>
		</tr>
		<tr>
			<td height="24">
				Criterio de selecci&oacute;n:
			</td>
			<td class="normalNegro">
				<?php 
					if($criterio_seleccion=="1"){ echo "Precio"; }
					else if($criterio_seleccion=="2"){ echo "Acuerdo de pago"; }
					else if($criterio_seleccion=="3"){ echo "Cantidad"; }
					else if($criterio_seleccion=="4"){ echo "Fecha entrega"; }
					else if($criterio_seleccion=="5"){ echo "Marca reconocida"; }
					else if($criterio_seleccion=="6"){ echo "Otros"; } 
				?></td>
		</tr>
		<tr>
			<td height="24">
				Observaciones: 
			</td>
			<td class="normalNegro"><?=$observaciones?></td>
		</tr>
		<tr>
			<td colspan="2" height="24" class="normal peq_verde_bold" align="left">
				Datos de la orden de compra
			</td>
		</tr>
		<tr>
			<td height="24">
				Fecha de Entrega: 
			</td>
			<td class="normalNegro">
				<?=$fecha_entrega?>
			</td>
		</tr>
		<tr>
			<td height="24">
				Forma de Pago: 
			</td>
			<td class="normalNegro">
				<?=$forma_pago?>
			</td>
		</tr>
		<tr>
			<td height="24">
				Garant&iacute;a de Anticipo: 
			</td>
			<td class="normalNegro">
				<?=$garantia_anticipo?>
			</td>
		</tr>
		<tr>
			<td height="24">
				Lugar de Entrega: 
			</td>
			<td class="normalNegro">
				<?=$lugar_entrega?>
			</td>
		</tr>
		<tr>
			<td height="24">
				Condiciones de Entrega: 
			</td>
			<td class="normalNegro">
				<?=$condiciones_entrega?>
			</td>
		</tr>
		<tr>
			<td height="24">
				Otras Garant&iacute;as: 
			</td>
			<td class="normalNegro">
				<?=$otras_garantias?>
			</td>
		</tr>
		<tr>
			<td height="24">
				Otras condiciones / Observaciones:
			</td>
			<td class="normalNegro"><?=$otras_condiciones?></td>
		</tr>
		<tr>
			<td height="24">
				Justificaci&oacute;n:
			</td>
			<td class="normalNegro"><?=$justificacion?></td>
		</tr>
		<tr>
			<td height="16" colspan="2">
				<div align="center" class="normal" style="height: 50px;margin-top: 20px;">
					<div style="margin-top: 12px;float: left;text-align: right;width: 66%;">Para generar su An&aacute;lisis de Cotizaciones en formato PDF haga clic <a href="javascript: generarAnalisisDeCotizacinoPdf1();" style="color: blue;font-weight: bold">aqu&iacute;</a></div><div style="float: right;width: 34%;"><a href="javascript: generarAnalisisDeCotizacinoPdf1();"><img src="../../imagenes/pdf_ico.jpg" border="0" align="left"/></a></div><br/>&nbsp;
				</div>
			</td>
		</tr>		
		<tr>
			<td height="16" colspan="2">
				<div align="center" class="normal" style="height: 50px;margin-top: 20px;">
					<div style="margin-top: 12px;float: left;text-align: right;width: 66%;">Para generar su Orden de Compra en formato PDF haga clic <a href="javascript: generarOrdenDeCompraPdf1();" style="color: blue;font-weight: bold">aqu&iacute;</a></div><div style="float: right;width: 34%;"><a href="javascript: generarOrdenDeCompraPdf1();"><img src="../../imagenes/pdf_ico.jpg" border="0" align="left"/></a></div><br/>&nbsp;
				</div>
			</td>
		</tr>		
	</table>
	<br/>
</form>
<?php
	if($accion!=""){
?>
<div class="normalNegrita" align="center">
	<?php
		if($accion=="generar"){
			$opcion = 1;
	?>
		<p align="center">Usted ha generado con &eacute;xito el documento: <span class="resultados"><?= $idOrdc?></span></p>
	<?php 
		}else if($accion=="modificar"){
			$opcion = 2;
	?>
		<p align="center">Usted ha modificado con &eacute;xito el documento: <span class="resultados"><?= $idOrdc?></span></p>
	<?php
		}else if($accion=="aprobar"){
			if($user_perfil_id=="42456"){
				$depe_id = "450";
			}else if($user_perfil_id=="46450"){
				$depe_id = "400";
			}else if($user_perfil_id=="30400"){
				$depe_id = "400";
			}else if($user_perfil_id=="46400"){
				$depe_id = "";
			}
			$opcion = 6;
	?>
		<p align="center">Usted ha aprobado con &eacute;xito el documento: <span class="resultados"><?= $idOrdc?></span></p>
	<?php
		}else if($accion=="devolver"){
			if($user_perfil_id=="46400"){
				$depe_id = "400";
			}
			$opcion = 5;
	?>
		<p align="center">Usted ha devuelto con &eacute;xito el documento: <span class="resultados"><?= $idOrdc?></span></p>
	<?php
		}else if($accion=="anular"){
			$opcion = 5;
	?>
		<p align="center">Usted ha anulado con &eacute;xito el documento: <span class="resultados"><?= $idOrdc?></span></p>
	<?php
		}
		
		if($accion!="anular" && $depe_id!=""){
			
			//OBTENER GRUPO E INDICAR OPERACION
			$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
						"WHERE ".
							"swfg.wfgr_perf = '".$user_perfil_id."' ";		
			$resultado = pg_exec($conexion, $query);
			$row = pg_fetch_array($resultado, 0);
			$idGrupo = $row["wfgr_id"];
			
			$sql=	"SELECT swfg.wfgr_descrip ".
					"FROM sai_wfcadena swfc,sai_wfcadena swfch, sai_wfgrupo swfg ".
					"WHERE ".
						"swfc.docu_id = 'ordc' AND ".
						"swfc.wfop_id = ".$opcion." AND ".
						"swfc.wfgr_id = ".$idGrupo." AND ".
						"swfc.wfca_id_hijo = swfch.wfca_id AND ".
						"swfch.wfgr_id = swfg.wfgr_id";
			$resultado = pg_exec($conexion,$sql);
			if($resultado){
				$row = pg_fetch_array($resultado,0);
				$wfgr_descrip=trim($row["wfgr_descrip"]);
				
				$query = 	"SELECT depe_nombre FROM sai_dependenci ".
							"WHERE ".
								"depe_id = '".$depe_id."'";
				$resultado = pg_exec($conexion, $query);
				$row = pg_fetch_array($resultado, 0);
				$depe_nombre = $row["depe_nombre"];

	?>
	<p align="center">El Documento fue enviado a la instancia: <span class="resultados"><?= $wfgr_descrip." en ".$depe_nombre?></span></p>
	<?php 	}
		}
	?>
</div>
<?php
	}
}
?>
</body>
</html>
<?php pg_close($conexion); ?>