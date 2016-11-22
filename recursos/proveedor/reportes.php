<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");
require_once("../../includes/funciones.php");
if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();
$estadoActivo = "1";
$opcion = "1";
if (isset($_REQUEST['opcion']) && $_REQUEST['opcion'] != "") {
	$opcion = $_REQUEST['opcion'];
}
$rif = "";
$rifNombre = "";
if (isset($_REQUEST['rifNombre']) && $_REQUEST['rifNombre'] != "") {
	$rifNombre = $_REQUEST['rifNombre'];
	if (isset($_REQUEST['rif']) && $_REQUEST['rif'] != "") {
		$rif = $_REQUEST['rif'];
	}
}
$partida = "";
if (isset($_REQUEST['partida']) && $_REQUEST['partida'] != "") {
	$partida = $_REQUEST['partida'];
}
$codigoPartida = trim(strtok($partida,":"));
$fechaInicio = "";
if (isset($_REQUEST['fechaInicio']) && $_REQUEST['fechaInicio'] != "") {
	$fechaInicio = $_REQUEST['fechaInicio'];
}
$fechaFin = "";
if (isset($_REQUEST['fechaFin']) && $_REQUEST['fechaFin'] != "") {
	$fechaFin = $_REQUEST['fechaFin'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Reportes de Proveedores</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css"/>
<link href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet" type="text/css"/>
<link href="../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
<script language="JavaScript" src="../../js/funciones.js"></script>
<script language="JavaScript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script language="JavaScript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script language="JavaScript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script language="JavaScript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script language="javascript" type="text/javascript">
function cambiarOpcion(){
	if($("#opcionTodos")[0].checked==true){
		$("#partida")[0].disabled = true;
		$("#inputSelectRifNombre")[0].disabled = true;
	}else if($("#opcionTodosSolicitudDeCotizacion")[0].checked==true){
		$("#partida")[0].disabled = true;
		$("#inputSelectRifNombre")[0].disabled = true;
	}/*else if(document.getElementById("opcionRifNombre").checked==true){
		document.getElementById("partida").disabled = true;
		document.getElementById("inputSelectRifNombre").disabled = false;
	}*/else if($("#opcionOrdenesDeCompra")[0].checked==true){
		$("#partida")[0].disabled = false;
		$("#inputSelectRifNombre")[0].disabled = false;
	}
	return;
}
function comparar_fechas(fecha_inicial,fecha_final){
	var fecha_inicial=$("#fechaInicio").val();
	var fecha_final=$("#fechaFin").val();
	
	var dia1 =fecha_inicial.substring(0,2);
	var mes1 =fecha_inicial.substring(3,5);
	var anio1=fecha_inicial.substring(6,10);
	
	var dia2 =fecha_final.substring(0,2);
	var mes2 =fecha_final.substring(3,5);
	var anio2=fecha_final.substring(6,10);

	dia1 = parseInt(dia1,10);
	mes1 = parseInt(mes1,10);
	anio1= parseInt(anio1,10);

	dia2 = parseInt(dia2,10);
	mes2 = parseInt(mes2,10);
	anio2= parseInt(anio2,10);
	
	if((anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2))){
		alert("La fecha inicial no debe ser mayor a la fecha final"); 
		document.form.fechaFin.value='';
		return;
	}
}
function buscar(){
	document.formProveedores.submit();
}
function exportar(){
	document.formProveedores.action="reportesHoja.php";
	document.formProveedores.submit();
}
function detalle(codigo){
	url="detalle.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) { newwindow.focus(); }
}

function onLoad(){
	var objInputRifNombre = $("#inputSelectRifNombre");
	var sendInputIdRif = "#rif";
	var errorIdRifNombre = "#errorRifNombre";

	objInputRifNombre.autocomplete({
		source: function(request, response){
			$.ajax({
				url: "../../acciones/ordc/ordc.php",
				dataType: "json",
				data: {
					accion: "SearchProveedores",
					rifNombre: request.term
				},
				success: function(json){
					var index = 0;
					var proveedores = new Array();
					$(sendInputIdRif)[0].value="";
					$(errorIdRifNombre)[0].innerHTML="";					
					$.each(json.listaProveedores, function(rif, objProveedor){
						proveedores[index++] = {
							id: rif,
							label: objProveedor.nombre+" ("+rif+")",
							value: objProveedor.nombre+" ("+rif+")"
						};
					});
					if(proveedores.length==0){
						$(errorIdRifNombre)[0].innerHTML="Proveedor inv&aacute;lido.";
					}
					response(proveedores);
				}
			});
		},
		minLength: 1,
		select: function(event, ui) {
			seleccionarItem({
				id: ui.item.id,
				nombre: ui.item.value,
				sendInputId: sendInputIdRif,
				objInput: objInputRifNombre
			});
			return false;
			
		}
	});

	function seleccionarItem(params){
		$(params['sendInputId'])[0].value=params['id'];
		$(params['objInput'])[0].value=params['nombre'];
	}
}
</script>
</head>
<body class="normal" onload="onLoad();">
	<form id="formProveedores" name="formProveedores" action="reportes.php">
		<table width="620" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td height="16" colspan="3" align="left" class="normalNegroNegrita">REPORTES DE PROVEEDORES</td>
			</tr>
			<tr>
				<td class="normalNegrita" colspan="3">
					<input type="radio" value="1" name="opcion" id="opcionTodos" onclick="cambiarOpcion();" <?php if($opcion=="1" || $opcion==""){echo "checked='checked'";}?>/>
					Todos los proveedores
				</td>
			</tr>
			<tr>
				<td class="normalNegrita" colspan="3">
					<input type="radio" value="2" name="opcion" id="opcionTodosSolicitudDeCotizacion" onclick="cambiarOpcion();" <?php if($opcion=="2"){echo "checked='checked'";}?>/>
					Todos los proveedores a los que se les ha enviado solicitud de cotizaci&oacute;n
				</td>
			</tr>
			<!-- <tr>
				<td class="normalNegrita" colspan="3">
					<input type="radio" value="3" name="opcion" id="opcionRifNombre" onclick="cambiarOpcion();" <?php if($opcion=="3"){echo "checked='checked'";}?>/>
					RIF o Nombre de proveedor
				</td>
			</tr>
			<tr>
				<td width="40px">&nbsp;</td>
				<td colspan="2" class="normalNegrita" height="30px;">
					Rif o Nombre: 
					<input id="inputSelectRifNombre" name="rifNombre" class="normalNegro" size="65" maxlength="200" value="<?= $rifNombre?>" <?php if($opcion != "3"){echo 'disabled="disabled"';} ?>/>
					<input id="rif" name="rif" type="hidden" value="<?= $rif?>"/>
					<div style="color: red; margin-top: 4px;" id="errorRifNombre"></div>
				</td>
			</tr> -->
			<tr>
				<td width="40" class="normalNegrita" colspan="3">
					<input type="radio" value="3" name="opcion" id="opcionOrdenesDeCompra" onclick="cambiarOpcion();" <?php if($opcion=="3"){echo "checked='checked'";}?>/>
					&Oacute;rdenes de compra/servicio
				</td>
			</tr>
			<tr>
				<td width="40px" rowspan="3">&nbsp;</td>
				<td colspan="2" class="normalNegrita">
					RIF o Nombre de proveedor: 
					<input id="inputSelectRifNombre" name="rifNombre" class="normalNegro" size="54" maxlength="200" value="<?= $rifNombre?>" <?php if($opcion != "3"){echo 'disabled="disabled"';} ?>/>
					<input id="rif" name="rif" type="hidden" value="<?= $rif?>"/>
					<div style="color: red; margin-top: 4px;" id="errorRifNombre"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="normalNegrita" height="60px;">
					Partida: <input autocomplete="off" size="70" type="text" id="partida" name="partida" value="<?php if($partida!=null){echo $partida;} ?>" class="normalNegro" <?php if($opcion != "3"){echo 'disabled="disabled"';} ?>/><br/>
					Introduzca el n&uacute;mero de partida o una palabra contenida en la descripci&oacute;n de la misma.
					<?php
					$query = 	"SELECT ".
									"sp.part_id, ".
									"sp.part_nombre ".
								"FROM sai_partida sp ".
								"WHERE ".
									"sp.esta_id <> 15 ".
								"GROUP BY ".
									"sp.part_id, ".
									"sp.part_nombre ".
								"ORDER BY sp.part_id";
					$resultado = pg_exec($conexion, $query);
					$arreglo = "";
					while($row=pg_fetch_array($resultado)){
						$arreglo .= "'".$row["part_id"]." : ".str_replace("\n"," ",$row["part_nombre"])."',";
					}
					$arreglo = substr($arreglo, 0, -1);
					?>
					<script>
						var partidas = new Array(<?= $arreglo?>);
						actb(document.getElementById('partida'),partidas);
					</script>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table border="0" align="left" cellpadding="0" cellspacing="0">
						<tr>
							<td class="normalNegrita" valign="middle">
								Fecha inicio:
								<input type="text" size="10" id="fechaInicio" name="fechaInicio" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly" <?php if($fechaInicio!=null && $fechaInicio!=""){echo "value='".$fechaInicio."'";}?>/>
								<a href="javascript:void(0);" onclick="if(document.getElementById('opcionOrdenesDeCompra').checked==true){g_Calendar.show(event, 'fechaInicio');}" title="Mostrar Fecha Inicio">
									<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Mostrar Fecha Inicio"/>
								</a>
							</td>
							<td>&nbsp;</td>
							<td class="normalNegrita" valign="middle">
								Fecha fin:
								<input type="text" size="10" id="fechaFin" name="fechaFin" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly" <?php if($fechaFin!=null && $fechaFin!=""){echo "value='".$fechaFin."'";}?>/>
								<a href="javascript:void(0);" onclick="if(document.getElementById('opcionOrdenesDeCompra').checked==true){g_Calendar.show(event, 'fechaFin');}" title="Mostrar Fecha Fin">
									<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Mostrar Fecha Fin"/>
								</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr align="center">
				<td colspan="3" align="center">
	  				<input class="normalNegro" type="button" value="Buscar" onclick="javascript:buscar();"/>
  					<input class="normalNegro" type="button" value="Exportar" onclick="javascript:exportar();"/>
	  			</td>
				
	  			
	  				  			
			</tr>
		</table>
	</form>
	<?
	if($opcion == "1" || $opcion == "2"){
		$query="SELECT 
					COUNT(DISTINCT(sp.prov_id_rif)) AS contador
				FROM ";
		if($opcion == "1"){
			$query .= "sai_proveedor_nuevo sp
						LEFT OUTER JOIN sai_estado se ON (sp.prov_esta_id = se.esta_id) 
						LEFT OUTER JOIN sai_prov_ramo_secundario sprs ON (UPPER(sprs.prov_id_rif) = UPPER(sp.prov_id_rif))
						LEFT OUTER JOIN sai_partida spa ON (sprs.id_ramo = spa.part_id) ";
		}else if($opcion == "2"){
			$query .= "sai_sol_coti ssc
						INNER JOIN sai_sol_coti_prov sscp ON (ssc.soco_id = sscp.soco_id)
						INNER JOIN sai_proveedor_nuevo sp ON (sscp.beneficiario_rif = sp.prov_id_rif)
						INNER JOIN sai_estado se ON (sp.prov_esta_id = se.esta_id)
						INNER JOIN sai_prov_ramo_secundario sprs ON (UPPER(sprs.prov_id_rif) = UPPER(sp.prov_id_rif))
						INNER JOIN sai_partida spa ON (sprs.id_ramo = spa.part_id) ";
		}
		$resultado=pg_query($conexion,$query) or die("Error al consultar el total de proveedores");
		$row=pg_fetch_array($resultado);
		$contador=$row["contador"];
		
		$query="SELECT 
					sp.prov_codigo,
					sp.prov_id_rif,
					sp.prov_nombre,
					sp.prov_telefonos,
					LOWER(sp.prov_email) AS prov_email,
					se.esta_nombre,
					sp.prov_esta_id,
					spa.part_id,
					spa.part_nombre
				FROM ";
		if($opcion == "1"){
			$query .= "sai_proveedor_nuevo sp
						LEFT OUTER JOIN sai_estado se ON (sp.prov_esta_id = se.esta_id) 
						LEFT OUTER JOIN sai_prov_ramo_secundario sprs ON (UPPER(sprs.prov_id_rif) = UPPER(sp.prov_id_rif))
						LEFT OUTER JOIN sai_partida spa ON (sprs.id_ramo = spa.part_id) ";
		}else if($opcion == "2"){
			$query .= "sai_sol_coti ssc
						INNER JOIN sai_sol_coti_prov sscp ON (ssc.soco_id = sscp.soco_id)
						INNER JOIN sai_proveedor_nuevo sp ON (sscp.beneficiario_rif = sp.prov_id_rif)
						INNER JOIN sai_estado se ON (sp.prov_esta_id = se.esta_id)
						INNER JOIN sai_prov_ramo_secundario sprs ON (UPPER(sprs.prov_id_rif) = UPPER(sp.prov_id_rif))
						INNER JOIN sai_partida spa ON (sprs.id_ramo = spa.part_id) ";
		}
		$query .= "GROUP BY
						sp.prov_codigo,
						sp.prov_id_rif,
						sp.prov_nombre,
						sp.prov_telefonos,
						sp.prov_email,
						se.esta_nombre,
						sp.prov_esta_id,
						spa.part_id,
						spa.part_nombre
					ORDER BY sp.prov_nombre";
	$resultado=pg_query($conexion,$query) or die("Error al consultar los proveedores");
	?>
	<span class="normalNegrita">Total <?= $contador?> proveedores</span>
	<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td class="normalNegroNegrita" align="center">Estatus</td>
			<td class="normalNegroNegrita" align="center">C&oacute;digo</td>
			<td class="normalNegroNegrita" align="center">RIF</td>
			<td class="normalNegroNegrita" align="center">Nombre</td>
			<td class="normalNegroNegrita" align="center">Tel&eacute;fonos</td>
			<td class="normalNegroNegrita" align="center">Email</td>
			<td class="normalNegroNegrita" align="center">Ramo(s)</td>
		</tr>
		<?php
		$rifProveedorAnterior="";
		$i = 1;
		$color = "background-color: #F6FFD5;";
		while($row=pg_fetch_array($resultado))  {
			if($rifProveedorAnterior!=$row["prov_id_rif"]){
				if($rifProveedorAnterior!=""){
		?>
						</ul>
					</td>
				</tr>
		<?php
					
				}
				$rifProveedorAnterior=$row["prov_id_rif"];
				if($i%2==0){
					$color = "background-color: #F6FFD5;";
				}else{
					$color = "";
				}
				$i++;
		?>
				<tr style="<?= $color?>">
					<td align="center" valign="top">
						<span class="normal" <?= (($row["prov_esta_id"]!="1")?"style='color:red;'":"")?>>
							<?= $row["esta_nombre"];?>
						</span>
					</td>
					<td align="right" valign="top"><span class="normal"><?= $row["prov_codigo"];?></span></td>
					<td valign="top"><span class="normal"><?= $row["prov_id_rif"];?></span></td>
					<td valign="top"><span class="normal"><?= $row["prov_nombre"];?></span></td>
					<td valign="top"><span class="normal"><?= $row["prov_telefonos"];?></span></td>
					<td valign="top"><span class="normal"><?= $row["prov_email"];?></span></td>
					<td valign="top" class="normal">
						<ul style="margin-top: 0pt;margin-bottom: 0pt;">
							<?php 
								if ( $row["part_id"]!=null && $row["part_id"]!='' ) {
									echo "<li>".$row["part_id"]." : ".$row["part_nombre"]."</li>";									
								} else {
									echo "<span style='color: red;'>N/A</span>";
								}
			}else{
				echo "<li>".$row["part_id"]." : ".$row["part_nombre"]."</li>";
			}
		}
		if($rifProveedorAnterior!=""){
		?>
					</ul>
				</td>
			</tr>
		<?php
		}
		?>		
	</table>
	<?
	}else if($opcion == "3"){
		if ($codigoPartida!=null && $codigoPartida!='') {
			while(endsWith($codigoPartida,".00")){
				$codigoPartida = substr($codigoPartida,0,-3);
			}			
		}
		$query="SELECT 
					COUNT(DISTINCT(s.ordc_id)) AS contador
				FROM 
				(SELECT 
					soc.ordc_id
				FROM
					sai_orden_compra soc
					INNER JOIN sai_req_bi_ma_ser srbms ON (soc.rebms_id = srbms.rebms_id)
					INNER JOIN sai_dependenci sd ON (srbms.depe_id = sd.depe_id)
					INNER JOIN sai_proveedor_nuevo sp ON (soc.rif_proveedor_seleccionado = sp.prov_id_rif)
					INNER JOIN sai_cotizacion sc ON (soc.ordc_id = sc.ordc_id AND soc.rif_proveedor_seleccionado = sc.rif_proveedor)
					INNER JOIN sai_cotizacion_base scb ON (sc.id_cotizacion = scb.id_cotizacion)
					INNER JOIN sai_cotizacion_item sci ON (sc.id_cotizacion = sci.id_cotizacion)
					INNER JOIN sai_item_partida sip ON (sci.id_item = sip.id_item)
					INNER JOIN sai_item si ON (sci.id_item = si.id)
				WHERE 
					soc.esta_id <> 15 AND soc.esta_id <> 2 ";
		//$query.=	(($rifNombre!=null && $rifNombre!='')?"AND LOWER(soc.rif_proveedor_seleccionado) LIKE '".mb_strtolower($rifNombre, 'UTF-8')."' ":"")."
		//position(UPPER(prov_nombre) in '0800PAPEL, C. A. 333') > 0
		$query.=	(($rifNombre!=null && $rifNombre!='')?" AND POSITION(LOWER(soc.rif_proveedor_seleccionado) IN '".mb_strtolower($rifNombre, 'UTF-8')."')>0 ":"")."

		
					".(($codigoPartida!=null && $codigoPartida!="")?" AND sip.part_id LIKE '".$codigoPartida."%' ":"")."
					".(($fechaInicio!=null && $fechaInicio!="" && $fechaFin!=null && $fechaFin!="")?"AND soc.fecha BETWEEN TO_DATE('".$fechaInicio."','DD/MM/YYYY') AND TO_DATE('".$fechaFin."','DD/MM/YYYY')+1 ":"")."
					".((($fechaInicio!=null && $fechaInicio!="") && ($fechaFin==null || $fechaFin==""))?"AND soc.fecha >= TO_DATE('".$fechaInicio."','DD/MM/YYYY') ":"")."
					".((($fechaInicio==null || $fechaInicio=="") && ($fechaFin!=null && $fechaFin!=""))?"AND soc.fecha <= TO_DATE('".$fechaFin."','DD/MM/YYYY') ":"").
				"GROUP BY 
					soc.ordc_id
				UNION
				SELECT 
					soc.ordc_id
				FROM
					sai_orden_compra soc
					INNER JOIN sai_req_bi_ma_ser srbms ON (soc.rebms_id = srbms.rebms_id)
					INNER JOIN sai_dependenci sd ON (srbms.depe_id = sd.depe_id)
					INNER JOIN sai_proveedor_nuevo sp ON (soc.rif_proveedor_seleccionado = sp.prov_id_rif)
					INNER JOIN sai_cotizacion sc ON (soc.ordc_id = sc.ordc_id AND soc.rif_proveedor_seleccionado = sc.rif_proveedor)
					INNER JOIN sai_cotizacion_base scb ON (sc.id_cotizacion = scb.id_cotizacion)
					INNER JOIN sai_cotizacion_item_adicional scia ON (sc.id_cotizacion = scia.id_cotizacion)
					INNER JOIN sai_item_partida sip ON (scia.id_item = sip.id_item)
					INNER JOIN sai_item si ON (scia.id_item = si.id)
				WHERE 
					soc.esta_id <> 15 AND soc.esta_id <> 2 ";
				$query.=	(($rifNombre!=null && $rifNombre!='')?"AND POSITION(LOWER(soc.rif_proveedor_seleccionado) IN '".mb_strtolower($rifNombre, 'UTF-8')."')>0 ":"")."
					".(($codigoPartida!=null && $codigoPartida!="")?" AND sip.part_id LIKE '".$codigoPartida."%' ":"")."
					".(($fechaInicio!=null && $fechaInicio!="" && $fechaFin!=null && $fechaFin!="")?"AND soc.fecha BETWEEN TO_DATE('".$fechaInicio."','DD/MM/YYYY') AND TO_DATE('".$fechaFin."','DD/MM/YYYY')+1 ":"")."
					".((($fechaInicio!=null && $fechaInicio!="") && ($fechaFin==null || $fechaFin==""))?"AND soc.fecha >= TO_DATE('".$fechaInicio."','DD/MM/YYYY') ":"")."
					".((($fechaInicio==null || $fechaInicio=="") && ($fechaFin!=null && $fechaFin!=""))?"AND soc.fecha <= TO_DATE('".$fechaFin."','DD/MM/YYYY') ":"").
				"GROUP BY  
					soc.ordc_id
				) AS s";
		$resultado=pg_query($conexion,$query) or die("Error al consultar el total de ordenes de compra");
		$row=pg_fetch_array($resultado);
		$contador=$row["contador"];
/*
		$query="SELECT 
					s.depe_nombre,
					s.rebms_id,
					s.ordc_id,
					TO_CHAR(s.fecha,'DD/MM/YYYY') AS fecha_elaboracion,
					s.rif_proveedor,
					s.nombre_proveedor,
					s.nombre_rubro,
					s.precio,
					s.unidad,
					s.cantidad_cotizada,
					s.iva,
					s.monto,
					s.partida,
					s.tipo
				FROM 
				(SELECT 
					sd.depe_nombre,
					soc.rebms_id,
					soc.ordc_id,
					soc.fecha,
					UPPER(sp.prov_id_rif) AS rif_proveedor,
					UPPER(sp.prov_nombre) AS nombre_proveedor,
					UPPER(si.nombre) AS nombre_rubro,
					SUM(sci.precio) AS precio,
					SUM(sci.unidad) AS unidad,
					SUM(sci.cantidad_cotizada) AS cantidad_cotizada,
					scb.iva,
					CASE
						WHEN scb.iva > 0 THEN
							SUM(sci.precio*sci.unidad*sci.cantidad_cotizada*(100+scb.iva)/100)
						ELSE
							SUM(sci.precio*sci.unidad*sci.cantidad_cotizada)
					END AS monto,
					sip.part_id AS partida,
					CASE
						WHEN srbms.rebms_tipo='1' THEN
							'Compra'
						ELSE
							'Servicio'
					END AS tipo
				FROM
					sai_orden_compra soc
					INNER JOIN sai_req_bi_ma_ser srbms ON (soc.rebms_id = srbms.rebms_id)
					INNER JOIN sai_dependenci sd ON (srbms.depe_id = sd.depe_id)
					INNER JOIN sai_proveedor_nuevo sp ON (soc.rif_proveedor_seleccionado = sp.prov_id_rif)
					INNER JOIN sai_cotizacion sc ON (soc.ordc_id = sc.ordc_id AND soc.rif_proveedor_seleccionado = sc.rif_proveedor)
					INNER JOIN sai_cotizacion_base scb ON (sc.id_cotizacion = scb.id_cotizacion)
					INNER JOIN sai_cotizacion_item sci ON (sc.id_cotizacion = sci.id_cotizacion)
					INNER JOIN sai_item_partida sip ON (sci.id_item = sip.id_item)
					INNER JOIN sai_item si ON (sci.id_item = si.id)
				WHERE 
					soc.esta_id <> 15 AND soc.esta_id <> 2 ";
		$query.=	(($rif!=null && $rif!='')?"AND LOWER(soc.rif_proveedor_seleccionado) LIKE '".mb_strtolower($rif, 'UTF-8')."' ":"")."
					".(($codigoPartida!=null && $codigoPartida!="")?" AND sip.part_id LIKE '".$codigoPartida."%' ":"")."
					".(($fechaInicio!=null && $fechaInicio!="" && $fechaFin!=null && $fechaFin!="")?"AND soc.fecha BETWEEN TO_DATE('".$fechaInicio."','DD/MM/YYYY') AND TO_DATE('".$fechaFin."','DD/MM/YYYY')+1 ":"")."
					".((($fechaInicio!=null && $fechaInicio!="") && ($fechaFin==null || $fechaFin==""))?"AND soc.fecha >= TO_DATE('".$fechaInicio."','DD/MM/YYYY') ":"")."
					".((($fechaInicio==null || $fechaInicio=="") && ($fechaFin!=null && $fechaFin!=""))?"AND soc.fecha <= TO_DATE('".$fechaFin."','DD/MM/YYYY') ":"").
				"GROUP BY  
					sd.depe_nombre,
					soc.rebms_id,
					soc.ordc_id,
					sp.prov_id_rif,
					sp.prov_nombre,
					si.nombre,
					sip.part_id,
					--sci.precio,
					--sci.unidad,
					--sci.cantidad_cotizada,
					scb.iva,
					soc.fecha,
					srbms.rebms_tipo
				UNION
				SELECT 
					sd.depe_nombre,
					soc.rebms_id,
					soc.ordc_id,
					soc.fecha,
					UPPER(sp.prov_id_rif) AS rif_proveedor,
					UPPER(sp.prov_nombre) AS nombre_proveedor,
					UPPER(si.nombre) AS nombre_rubro,
					SUM(scia.precio) AS precio,
					SUM(scia.unidad) AS unidad,
					SUM(scia.cantidad_cotizada) AS cantidad_cotizada,
					scb.iva,
					CASE
						WHEN scb.iva > 0 THEN
							SUM(scia.precio*scia.unidad*scia.cantidad_cotizada*(100+scb.iva)/100)
						ELSE
							SUM(scia.precio*scia.unidad*scia.cantidad_cotizada)
					END AS monto,
					sip.part_id AS partida,
					CASE
						WHEN srbms.rebms_tipo='1' THEN
							'Compra'
						ELSE
							'Servicio'
					END AS tipo
				FROM
					sai_orden_compra soc
					INNER JOIN sai_req_bi_ma_ser srbms ON (soc.rebms_id = srbms.rebms_id)
					INNER JOIN sai_dependenci sd ON (srbms.depe_id = sd.depe_id)
					INNER JOIN sai_proveedor_nuevo sp ON (soc.rif_proveedor_seleccionado = sp.prov_id_rif)
					INNER JOIN sai_cotizacion sc ON (soc.ordc_id = sc.ordc_id AND soc.rif_proveedor_seleccionado = sc.rif_proveedor)
					INNER JOIN sai_cotizacion_base scb ON (sc.id_cotizacion = scb.id_cotizacion)
					INNER JOIN sai_cotizacion_item_adicional scia ON (sc.id_cotizacion = scia.id_cotizacion)
					INNER JOIN sai_item_partida sip ON (scia.id_item = sip.id_item)
					INNER JOIN sai_item si ON (scia.id_item = si.id)
				WHERE 
					soc.esta_id <> 15 AND soc.esta_id <> 2 ";
		$query.=	(($rif!=null && $rif!='')?"AND LOWER(soc.rif_proveedor_seleccionado) LIKE '".mb_strtolower($rif, 'UTF-8')."' ":"")."
					".(($codigoPartida!=null && $codigoPartida!="")?" AND sip.part_id LIKE '".$codigoPartida."%' ":"")."
					".(($fechaInicio!=null && $fechaInicio!="" && $fechaFin!=null && $fechaFin!="")?"AND soc.fecha BETWEEN TO_DATE('".$fechaInicio."','DD/MM/YYYY') AND TO_DATE('".$fechaFin."','DD/MM/YYYY')+1 ":"")."
					".((($fechaInicio!=null && $fechaInicio!="") && ($fechaFin==null || $fechaFin==""))?"AND soc.fecha >= TO_DATE('".$fechaInicio."','DD/MM/YYYY') ":"")."
					".((($fechaInicio==null || $fechaInicio=="") && ($fechaFin!=null && $fechaFin!=""))?"AND soc.fecha <= TO_DATE('".$fechaFin."','DD/MM/YYYY') ":"").
				"GROUP BY
					sd.depe_nombre,
					soc.rebms_id,
					soc.ordc_id,
					sp.prov_id_rif,
					sp.prov_nombre,
					si.nombre,
					sip.part_id,
					--scia.precio,
					--scia.unidad,
					--scia.cantidad_cotizada,
					scb.iva,
					soc.fecha,
					srbms.rebms_tipo) AS s
				ORDER BY s.fecha ASC, s.depe_nombre, s.ordc_id ASC, s.partida ASC, s.nombre_rubro ASC";*/
		
	
	//REPORTE CON IVAS DE 8 Y 12
	$query="SELECT 
					s.depe_nombre,
					s.rebms_id,
					s.ordc_id,
					TO_CHAR(s.fecha,'DD/MM/YYYY') AS fecha_elaboracion,
					s.rif_proveedor,
					s.nombre_proveedor,
					s.nombre_rubro,
					s.precio,
					s.unidad,
					s.cantidad_cotizada,
					s.monto,
					s.numero_item,
					s.iva8,
					s.base8,
					s.monto8,
					s.iva12,
					s.base12,
					s.monto12,
					s.partida,
					s.tipo,
					s.justificacion,
					s.redondear,
					s.pcta_id,
					CASE 
						WHEN ( s.rebms_tipo_imputa = 0 ) THEN -- rebms_tipo_imputa = 0 => Accion Centralizada
							(	SELECT
									centro_gestor || '/' ||centro_costo
								FROM
									sai_acce_esp especifica
								WHERE
									especifica.acce_id = s.rebms_imp_p_c
									AND especifica.aces_id = s.rebms_imp_esp
									AND especifica.pres_anno = s.rebms_pres_anno
								LIMIT
									1
							)
						WHEN ( s.rebms_tipo_imputa = 1 ) THEN -- rebms_tipo_imputa = 1 => Proyecto
							(	SELECT
									centro_gestor || '/' ||centro_costo
								FROM
									sai_proy_a_esp especifica
								WHERE
									especifica.proy_id = s.rebms_imp_p_c
									AND especifica.paes_id = s.rebms_imp_esp
									AND especifica.pres_anno = s.rebms_pres_anno
								LIMIT
									1
							)
						ELSE
							''
					END AS centro_gestor_costo
				FROM 
				(SELECT 
				 	srbms.pcta_id,
				 	srbms.rebms_tipo_imputa,
				 	srbms.rebms_imp_p_c,
				 	srbms.rebms_imp_esp,
				 	srbms.pres_anno AS rebms_pres_anno,
					sd.depe_nombre,
					soc.rebms_id,
					soc.ordc_id,
					soc.fecha,
					UPPER(sp.prov_id_rif) AS rif_proveedor,
					UPPER(sp.prov_nombre) AS nombre_proveedor,
					UPPER(si.nombre) AS nombre_rubro,
					sci.precio,
					sci.unidad,
					sci.cantidad_cotizada,
					CASE 
						WHEN ( sc.redondear = TRUE ) THEN
							sci.unidad*sci.cantidad_cotizada*sci.precio
						ELSE
							TRUNC(CAST(sci.unidad*sci.cantidad_cotizada*sci.precio AS NUMERIC), 2) 
					END AS monto,
					sci.numero_item,
					scb8.iva AS iva8,
					scb8.base AS base8,
					CASE 
						WHEN ( sc.redondear = TRUE ) THEN
							scb8.base*scb8.iva/100
						ELSE
							TRUNC(CAST(scb8.base*scb8.iva/100 AS NUMERIC), 2) 
					END AS monto8,
					scb12.iva AS iva12,
					scb12.base AS base12,
					CASE 
						WHEN ( sc.redondear = TRUE ) THEN
							scb12.base*scb12.iva/100
						ELSE
							TRUNC(CAST(scb12.base*scb12.iva/100 AS NUMERIC), 2) 
					END AS monto12,
					sip.part_id AS partida,
					CASE
						WHEN srbms.rebms_tipo='1' THEN
							'Compra'
						ELSE
							'Servicio'
					END AS tipo,
					srbms.justificacion,
					sc.redondear
				FROM
					sai_orden_compra soc
					INNER JOIN sai_req_bi_ma_ser srbms ON (soc.rebms_id = srbms.rebms_id)
					INNER JOIN sai_dependenci sd ON (srbms.depe_id = sd.depe_id)
					INNER JOIN sai_proveedor_nuevo sp ON (soc.rif_proveedor_seleccionado = sp.prov_id_rif)
					INNER JOIN sai_cotizacion sc ON (soc.ordc_id = sc.ordc_id AND soc.rif_proveedor_seleccionado = sc.rif_proveedor)
					INNER JOIN sai_cotizacion_item sci ON (sc.id_cotizacion = sci.id_cotizacion)
					INNER JOIN sai_item_partida sip ON (sci.id_item = sip.id_item)
					INNER JOIN sai_item si ON (sci.id_item = si.id)
					LEFT OUTER JOIN sai_cotizacion_base scb8 ON (sc.id_cotizacion = scb8.id_cotizacion AND scb8.iva = 8)
					LEFT OUTER JOIN sai_cotizacion_base scb12 ON (sc.id_cotizacion = scb12.id_cotizacion AND scb12.iva = 12)					
				WHERE 
					soc.esta_id <> 15 AND soc.esta_id <> 2 ";
		//$query.=	(($rifNombre!=null && $rifNombre!='')?"AND LOWER(soc.rif_proveedor_seleccionado) LIKE '".mb_strtolower($rifNombre, 'UTF-8')."' ":"")."
				$query.=	(($rifNombre!=null && $rifNombre!='')?" AND POSITION(LOWER(soc.rif_proveedor_seleccionado) IN '".mb_strtolower($rifNombre, 'UTF-8')."')>0 ":"")."		
					".(($codigoPartida!=null && $codigoPartida!="")?" AND sip.part_id LIKE '".$codigoPartida."%' ":"")."
					".(($fechaInicio!=null && $fechaInicio!="" && $fechaFin!=null && $fechaFin!="")?"AND soc.fecha BETWEEN TO_DATE('".$fechaInicio."','DD/MM/YYYY') AND TO_DATE('".$fechaFin."','DD/MM/YYYY')+1 ":"")."
					".((($fechaInicio!=null && $fechaInicio!="") && ($fechaFin==null || $fechaFin==""))?"AND soc.fecha >= TO_DATE('".$fechaInicio."','DD/MM/YYYY') ":"")."
					".((($fechaInicio==null || $fechaInicio=="") && ($fechaFin!=null && $fechaFin!=""))?"AND soc.fecha <= TO_DATE('".$fechaFin."','DD/MM/YYYY') ":"").
				"GROUP BY  
				 	srbms.pcta_id,
				 	srbms.rebms_tipo_imputa,
				 	srbms.rebms_imp_p_c,
				 	srbms.rebms_imp_esp,
				 	srbms.pres_anno,
					sd.depe_nombre,
					soc.rebms_id,
					soc.ordc_id,
					sp.prov_id_rif,
					sp.prov_nombre,
					si.nombre,
					sip.part_id,
					sci.precio,
					sci.unidad,
					sci.cantidad_cotizada,
					sci.numero_item,
					scb8.iva,
					scb8.base,
					scb12.iva,
					scb12.base,
					soc.fecha,
					srbms.rebms_tipo,
					srbms.justificacion,
					sc.redondear
				UNION
				SELECT 
				 	srbms.pcta_id,
				 	srbms.rebms_tipo_imputa,
				 	srbms.rebms_imp_p_c,
				 	srbms.rebms_imp_esp,
				 	srbms.pres_anno AS rebms_pres_anno,
					sd.depe_nombre,
					soc.rebms_id,
					soc.ordc_id,
					soc.fecha,
					UPPER(sp.prov_id_rif) AS rif_proveedor,
					UPPER(sp.prov_nombre) AS nombre_proveedor,
					UPPER(si.nombre) AS nombre_rubro,
					scia.precio,
					scia.unidad,
					scia.cantidad_cotizada,
					CASE 
						WHEN ( sc.redondear = TRUE ) THEN
							scia.unidad*scia.cantidad_cotizada*scia.precio
						ELSE
							TRUNC(CAST(scia.unidad*scia.cantidad_cotizada*scia.precio AS NUMERIC), 2) 
					END AS monto,
					scia.numero_item,
					scb8.iva AS iva8,
					scb8.base AS base8,
					CASE 
						WHEN ( sc.redondear = TRUE ) THEN
							scb8.base*scb8.iva/100
						ELSE
							TRUNC(CAST(scb8.base*scb8.iva/100 AS NUMERIC), 2)  
					END AS monto8,
					scb12.iva AS iva12,
					scb12.base AS base12,
					CASE 
						WHEN ( sc.redondear = TRUE ) THEN
							scb12.base*scb12.iva/100
						ELSE
							TRUNC(CAST(scb12.base*scb12.iva/100 AS NUMERIC), 2) 
					END AS monto12,
					sip.part_id AS partida,
					CASE
						WHEN srbms.rebms_tipo='1' THEN
							'Compra'
						ELSE
							'Servicio'
					END AS tipo,
					srbms.justificacion,
					sc.redondear
				FROM
					sai_orden_compra soc
					INNER JOIN sai_req_bi_ma_ser srbms ON (soc.rebms_id = srbms.rebms_id)
					INNER JOIN sai_dependenci sd ON (srbms.depe_id = sd.depe_id)
					INNER JOIN sai_proveedor_nuevo sp ON (soc.rif_proveedor_seleccionado = sp.prov_id_rif)
					INNER JOIN sai_cotizacion sc ON (soc.ordc_id = sc.ordc_id AND soc.rif_proveedor_seleccionado = sc.rif_proveedor)
					INNER JOIN sai_cotizacion_item_adicional scia ON (sc.id_cotizacion = scia.id_cotizacion)
					INNER JOIN sai_item_partida sip ON (scia.id_item = sip.id_item)
					INNER JOIN sai_item si ON (scia.id_item = si.id)
					LEFT OUTER JOIN sai_cotizacion_base scb8 ON (sc.id_cotizacion = scb8.id_cotizacion AND scb8.iva = 8)
					LEFT OUTER JOIN sai_cotizacion_base scb12 ON (sc.id_cotizacion = scb12.id_cotizacion AND scb12.iva = 12)
				WHERE 
					soc.esta_id <> 15 AND soc.esta_id <> 2 ";
		//$query.=	(($rifNombre!=null && $rifNombre!='')?"AND LOWER(soc.rif_proveedor_seleccionado) LIKE '".mb_strtolower($rifNombre, 'UTF-8')."' ":"")."
					$query.=	(($rifNombre!=null && $rifNombre!='')?" AND POSITION(LOWER(soc.rif_proveedor_seleccionado) IN '".mb_strtolower($rifNombre, 'UTF-8')."')>0 ":"")."
					".(($codigoPartida!=null && $codigoPartida!="")?" AND sip.part_id LIKE '".$codigoPartida."%' ":"")."
					".(($fechaInicio!=null && $fechaInicio!="" && $fechaFin!=null && $fechaFin!="")?"AND soc.fecha BETWEEN TO_DATE('".$fechaInicio."','DD/MM/YYYY') AND TO_DATE('".$fechaFin."','DD/MM/YYYY')+1 ":"")."
					".((($fechaInicio!=null && $fechaInicio!="") && ($fechaFin==null || $fechaFin==""))?"AND soc.fecha >= TO_DATE('".$fechaInicio."','DD/MM/YYYY') ":"")."
					".((($fechaInicio==null || $fechaInicio=="") && ($fechaFin!=null && $fechaFin!=""))?"AND soc.fecha <= TO_DATE('".$fechaFin."','DD/MM/YYYY') ":"").
				"GROUP BY
				    srbms.pcta_id,
				    srbms.rebms_tipo_imputa,
				 	srbms.rebms_imp_p_c,
				 	srbms.rebms_imp_esp,
				 	srbms.pres_anno,
					sd.depe_nombre,
					soc.rebms_id,
					soc.ordc_id,
					sp.prov_id_rif,
					sp.prov_nombre,
					si.nombre,
					sip.part_id,
					scia.precio,
					scia.unidad,
					scia.cantidad_cotizada,
					scia.numero_item,
					scb8.iva,
					scb8.base,
					scb12.iva,
					scb12.base,
					soc.fecha,
					srbms.rebms_tipo,
					srbms.justificacion,
					sc.redondear) AS s
				ORDER BY s.fecha ASC, s.depe_nombre, s.ordc_id ASC, s.partida ASC, s.nombre_rubro ASC";
		
				$resultado = pg_query($conexion, $query);
				if($resultado === false){
					error_log(pg_last_error($conexion));
					echo "Error al consultar las ordenes de compra";
					exit;
				}
				//error_log(print_r($query,true));
	?>
	<br/>
	<span class="normalNegrita">Total <?= $contador?> &oacute;rdenes de compra/servicio</span>
	<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td valign="top" class="normal">N&deg;</td>
			<td width="150px" class="normalNegroNegrita" align="center">Dependencia</td>
			<td class="normalNegroNegrita" align="center">Requisici&oacute;n</td>
			<td class="normalNegroNegrita" align="center">Pcta asociado</td>
			<td class="normalNegroNegrita" align="center">Centro gestor/costo</td>
			<td class="normalNegroNegrita" align="center">Orden de Compra/Servicio</td>
			<td class="normalNegroNegrita" align="center">Tipo</td>
			<td class="normalNegroNegrita" align="center">Fecha</td>
			<td class="normalNegroNegrita" align="center">RIF</td>
			<td class="normalNegroNegrita" align="center">Proveedor</td>
			<td class="normalNegroNegrita" align="center">Partida</td>
			<td class="normalNegroNegrita" align="center">Rubro</td>
			<td class="normalNegroNegrita" align="center">Unidad</td>
			<td class="normalNegroNegrita" align="center">Cantidad</td>
			<td class="normalNegroNegrita" align="center">Precio</td>
			<td class="normalNegroNegrita" align="center">Monto</td>
			<td class="normalNegroNegrita" align="center">BASE 8%</td>
			<td class="normalNegroNegrita" align="center">IVA 8%</td>
			<td class="normalNegroNegrita" align="center">BASE 12%</td>
			<td class="normalNegroNegrita" align="center">IVA 12%</td>
			<td class="normalNegroNegrita" align="center">Total</td>
			<td class="normalNegroNegrita" align="center">Justificaci&oacute;n</td>
		</tr>
		<?php
		$total = 0;
		$totalIva8 = 0;
		$totalIva12 = 0;
		$ordenDeCompraAnterior = "";
		$ordenDeCompraAnteriorBase8 = 0;
		$ordenDeCompraAnteriorMonto8 = 0;
		$ordenDeCompraAnteriorBase12 = 0;
		$ordenDeCompraAnteriorMonto12 = 0;
		$ordenDeCompraAnteriorTotal = 0;
		$i = 1;
		$color = "background-color: #F6FFD5;";
		while($row=pg_fetch_array($resultado))  {
			if($ordenDeCompraAnterior!=$row["ordc_id"]){
				$total += $ordenDeCompraAnteriorTotal;
				if ( $ordenDeCompraAnterior!="" ) {
				?>
					<tr style="<?= $color?>"><td colspan="22" style="border-bottom: solid 1px #C3ECCC;">&nbsp;</td></tr>
					<script>
						$("#base8<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorBase8,2,',','.');?>");
						$("#monto8<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorMonto8,2,',','.');?>");
						$("#base12<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorBase12,2,',','.');?>");
						$("#monto12<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorMonto12,2,',','.');?>");
						$("#total<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorTotal,2,',','.');?>");
					</script>
				<?php 
				}
				
				$ordenDeCompraAnteriorBase8 = $row["base8"];
				$ordenDeCompraAnteriorMonto8 = $row["monto8"];
				$ordenDeCompraAnteriorBase12 = $row["base12"];
				$ordenDeCompraAnteriorMonto12 = $row["monto12"];
				$ordenDeCompraAnteriorTotal = $ordenDeCompraAnteriorMonto8+$ordenDeCompraAnteriorMonto12+$row["monto"];
				$totalIva8 += $ordenDeCompraAnteriorMonto8;
				$totalIva12 += $ordenDeCompraAnteriorMonto12;
				
				if($i%2==0){
					$color = "background-color: #F6FFD5;";
				}else{
					$color = "";
				}
				$i++;
				$ordenDeCompraAnterior=$row["ordc_id"];
				?>
				<tr style="<?= $color?>">
					<td valign="top" class="normal"><?= $i-1;?></td>
					<td valign="top" class="normal"><?= $row["depe_nombre"];?></td>
					<td align="center" valign="top" class="normal"><?= $row["rebms_id"];?></td>
					<td align="center" valign="top" class="normal"><? echo ($row["pcta_id"] != '') ? $row["pcta_id"] : '-'; ?></td>
					<td align="center" valign="top" class="normal"><? echo ($row["centro_gestor_costo"] != '') ? $row["centro_gestor_costo"] : '-'; ?></td>
					<td align="center" valign="top" class="normal"><?= $row["ordc_id"];?></td>
					<td align="center" valign="top" class="normal"><?= $row["tipo"];?></td>
					<td align="center" valign="top" class="normal"><?= $row["fecha_elaboracion"];?></td>
					<td align="center" valign="top" class="normal"><?= $row["rif_proveedor"];?></td>
					<td valign="top" class="normal"><?= $row["nombre_proveedor"];?></td>
					<td align="center" valign="top" class="normal"><?= $row["partida"];?></td>
					<td valign="top" class="normal"><?= $row["nombre_rubro"];?></td>
					<td align="center" valign="top" class="normal"><?= $row["unidad"];?></td>
					<td align="center" valign="top" class="normal"><?= $row["cantidad_cotizada"];?></td>
					<td align="right" valign="top" class="normal"><?= number_format($row["precio"],2,',','.');?></td>
					<td align="right" valign="top" class="normalNegrita"><?= number_format($row["monto"],2,',','.');?></td>
					<td id="base8<?= $row["ordc_id"]?>" align="right" valign="top" class="normal"></td>
					<td id="monto8<?= $row["ordc_id"]?>" align="right" valign="top" class="normalNegrita"></td>
					<td id="base12<?= $row["ordc_id"]?>" align="right" valign="top" class="normal"></td>
					<td id="monto12<?= $row["ordc_id"]?>" align="right" valign="top" class="normalNegrita"></td>
					<td id="total<?= $row["ordc_id"]?>" align="right" valign="top" class="normalNegrita"></td>
					<td valign="top" class="normal"><?= $row["justificacion"];?></td>
				</tr>
				<?php 
			}else{
		?>
				<tr style="<?= $color?>">
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td align="center" valign="top" class="normal"><?= $row["partida"];?></td>
					<td valign="top" class="normal"><?= $row["nombre_rubro"];?></td>
					<td align="center" valign="top" class="normal"><?= $row["unidad"];?></td>
					<td align="center" valign="top" class="normal"><?= $row["cantidad_cotizada"];?></td>
					<td align="right" valign="top" class="normal"><?= number_format($row["precio"],2,',','.');?></td>
					<td align="right" valign="top" class="normalNegrita"><?= number_format($row["monto"],2,',','.');?></td>
					<td align="right" valign="top" class="normal"></td>
					<td align="right" valign="top" class="normal"></td>
					<td align="right" valign="top" class="normal"></td>
					<td align="right" valign="top" class="normal"></td>
					<td align="right" valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
				</tr>
		<?php
				$ordenDeCompraAnteriorTotal += $row["monto"];
			}
		}
		$total += $ordenDeCompraAnteriorTotal;
		if ( $ordenDeCompraAnterior!="" ) {
		?>
			<tr style="<?= $color?>"><td colspan="22" style="border-bottom: solid 1px #C3ECCC;">&nbsp;</td></tr>
			<script>
				$("#base8<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorBase8,2,',','.');?>");
				$("#monto8<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorMonto8,2,',','.');?>");
				$("#base12<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorBase12,2,',','.');?>");
				$("#monto12<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorMonto12,2,',','.');?>");
				$("#total<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorTotal,2,',','.');?>");
			</script>
		<?php 
		}
		if($total>0){
		?>
			<tr>
				<td align="right" class="normalNegro" colspan="11"><b>Total IVA 8%:</b></td>
				<td align="right" class="normalNegro"><b><?= number_format($totalIva8,2,',','.')?></b></td>
				<td align="right" class="normalNegro" colspan="3"><b>Total IVA 12%:</b></td>
				<td align="right" class="normalNegro"><b><?= number_format($totalIva12,2,',','.')?></b></td>
				<td align="right" class="normalNegro" colspan="2"><b>Total:</b></td>
				<td align="right" class="normalNegro"><b><?= number_format($total,2,',','.')?></b></td>
				<td valign="top" class="normal"></td>
			</tr>
		<?php
		}
		?>		
	</table>
	<?
	}
	?>		
</body>
</html>