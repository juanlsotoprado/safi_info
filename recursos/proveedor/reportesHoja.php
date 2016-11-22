<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");
require_once("../../includes/funciones.php");
require_once("../../includes/excel.php");
require_once("../../includes/excel-ext.php");

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
	$c = 0;
	$f = 0;
	$contenido[$f][$c]=utf8_decode("Total:".$contador." proveedores");$f++;
	$contenido[$f][$c]=utf8_decode("Estatus");$c++;
	$contenido[$f][$c]=utf8_decode("Código");$c++;
	$contenido[$f][$c]=utf8_decode("RIF");$c++;	
	$contenido[$f][$c]=utf8_decode("Nombre");$c++;	
	$contenido[$f][$c]=utf8_decode("Teléfonos");$c++;
	$contenido[$f][$c]=utf8_decode("Email");$c++;
	$contenido[$f][$c]=utf8_decode("Ramos");
	$f++;$c=0;	

	$rifProveedorAnterior="";
	$i = 1;
	$color = "background-color: #F6FFD5;";

	while($row=pg_fetch_array($resultado))  {
		$f++;
			if($rifProveedorAnterior!=$row["prov_id_rif"]){
				if($rifProveedorAnterior!=""){
		
					/*	</ul>
					</td>
				</tr>*/
		
					
				}
				$rifProveedorAnterior=$row["prov_id_rif"];
				if($i%2==0){
					$color = "background-color: #F6FFD5;";
				}else{
					$color = "";
				}
				$i++;
		
					$contenido[$f][$c]= $row["esta_nombre"];
					$c++;
					$contenido[$f][$c]= $row["prov_codigo"];
					$c++;
					$contenido[$f][$c] = $row["prov_id_rif"];
					$c++;
					$contenido[$f][$c] = $row["prov_nombre"];
					$c++;
					$contenido[$f][$c] = $row["prov_telefonos"];
					$c++;
					$contenido[$f][$c] = $row["prov_email"];
					$c++;
								
					if ( $row["part_id"]!=null && $row["part_id"]!='' ) {
						$contenido[$f][$c] = $row["part_id"]." : ".$row["part_nombre"];									
					} else {
						$contenido[$f][$c] = "N/A";
					}
			}else{
				$c= 0;
				$contenido[$f][$c]= "";$c++;
				$contenido[$f][$c]= "";$c++;
				$contenido[$f][$c]= "";$c++;
				$contenido[$f][$c]= "";$c++;
				$contenido[$f][$c]= "";$c++;
				$contenido[$f][$c]= "";$c++;
				$contenido[$f][$c] = $row["part_id"]." : ".$row["part_nombre"];
			}
		}
		if($rifProveedorAnterior!=""){

					/*</ul>
				</td>
			</tr>*/
		}


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
				$query.=	(($rifNombre!=null && $rifNombre!='')?" AND POSITION(LOWER(soc.rif_proveedor_seleccionado) IN '".mb_strtolower($rifNombre, 'UTF-8')."')>0 ":"")."
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
	$c=0;
	$f=0;
	$contenido[$f][$c] = utf8_decode("Total: "+$contador+"Órdenes de compra/servicio");
	$f++;
	$contenido[$f][$c] = utf8_decode("N°");$c++;
	$contenido[$f][$c] = utf8_decode("Dependencia");$c++;
	$contenido[$f][$c] = utf8_decode("Requisición");$c++;
	$contenido[$f][$c] = utf8_decode("Pcta asociado");$c++;
	$contenido[$f][$c] = utf8_decode("Centro gestor/costo");$c++;
	$contenido[$f][$c] = utf8_decode("Orden de Compra/Servicio");$c++;
	$contenido[$f][$c] = utf8_decode("Tipo");$c++;
	$contenido[$f][$c] = utf8_decode("Fecha");$c++;
	$contenido[$f][$c] = utf8_decode("RIF");$c++;
	$contenido[$f][$c] = utf8_decode("Proveedor");$c++;
	$contenido[$f][$c] = utf8_decode("Partida");$c++;
	$contenido[$f][$c] = utf8_decode("Rubro");$c++;
	$contenido[$f][$c] = utf8_decode("Unidad");$c++;
	$contenido[$f][$c] = utf8_decode("Cantidad");$c++;
	$contenido[$f][$c] = utf8_decode("Precio");$c++;
	$contenido[$f][$c] = utf8_decode("Monto");$c++;
	$contenido[$f][$c] = utf8_decode("BASE 8%");$c++;
	$contenido[$f][$c] = utf8_decode("IVA 8%");$c++;
	$contenido[$f][$c] = utf8_decode("BASE 12%");$c++;
	$contenido[$f][$c] = utf8_decode("IVA 12%");$c++;
	$contenido[$f][$c] = utf8_decode("Total");$c++;
	$contenido[$f][$c] = utf8_decode("Justificación");
	
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
		$f++;
		if($ordenDeCompraAnterior!=$row["ordc_id"]){
			$total += $ordenDeCompraAnteriorTotal;
				if ( $ordenDeCompraAnterior!="" ) {


/*					<script>
						$("#base8<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorBase8,2,',','.');?>");
						$("#monto8<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorMonto8,2,',','.');?>");
						$("#base12<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorBase12,2,',','.');?>");
						$("#monto12<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorMonto12,2,',','.');?>");
						$("#total<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorTotal,2,',','.');?>");
					</script>*/
				
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
				
				$contenido[$f][$c] = $i-1;$c++;
				$contenido[$f][$c] = $row["depe_nombre"];$c++;
				$contenido[$f][$c] = $row["rebms_id"];$c++;
				$contenido[$f][$c] = ($row["pcta_id"] != '') ? $row["pcta_id"] : '-';$c++; 
				$contenido[$f][$c] = ($row["centro_gestor_costo"] != '') ? $row["centro_gestor_costo"] : '-';$c++; 
				$contenido[$f][$c] = $row["ordc_id"];$c++;
				$contenido[$f][$c] = $row["tipo"];$c++;
				$contenido[$f][$c] = $row["fecha_elaboracion"];$c++;
				$contenido[$f][$c] = $row["rif_proveedor"];$c++;
				$contenido[$f][$c] = $row["nombre_proveedor"];$c++;
				$contenido[$f][$c] = $row["partida"];$c++;
				$contenido[$f][$c] = $row["nombre_rubro"];$c++;
				$contenido[$f][$c] = $row["unidad"];$c++;
				$contenido[$f][$c] = $row["cantidad_cotizada"];$c++;
				$contenido[$f][$c] = number_format($row["precio"],2,',','.');$c++;
				$contenido[$f][$c] = number_format($row["monto"],2,',','.');$c++;
				/*$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;*/
				
				
				/*	<td id="base8<?= $row["ordc_id"]?>" align="right" valign="top" class="normal"></td>
					<td id="monto8<?= $row["ordc_id"]?>" align="right" valign="top" class="normalNegrita"></td>
					<td id="base12<?= $row["ordc_id"]?>" align="right" valign="top" class="normal"></td>
					<td id="monto12<?= $row["ordc_id"]?>" align="right" valign="top" class="normalNegrita"></td>
					<td id="total<?= $row["ordc_id"]?>" align="right" valign="top" class="normalNegrita"></td>*/
					$contenido[$f][$c] = number_format($ordenDeCompraAnteriorBase8,2,',','.');$c++;
					$contenido[$f][$c] = number_format($ordenDeCompraAnteriorMonto8,2,',','.');$c++;
					$contenido[$f][$c] = number_format($ordenDeCompraAnteriorBase12,2,',','.');$c++;
					$contenido[$f][$c] = number_format($ordenDeCompraAnteriorMonto12,2,',','.');$c++;
					$contenido[$f][$c] = number_format($ordenDeCompraAnteriorTotal,2,',','.');$c++;				
				$contenido[$f][$c]	= $row["justificacion"];$c++;
				
			}else{
					$f++;
					$c = 0;
					$contenido[$f][$c] = "";$c++;
					$contenido[$f][$c] = "";$c++;
					$contenido[$f][$c] = "";$c++;
					$contenido[$f][$c] = "";$c++;
					$contenido[$f][$c] = "";$c++;
					$contenido[$f][$c] = "";$c++;
					$contenido[$f][$c] = "";$c++;
					$contenido[$f][$c] = "";$c++;
					$contenido[$f][$c] = "";$c++;
					$contenido[$f][$c] = "";$c++;
					
					/*<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>*/
					$contenido[$f][$c] = $row["partida"];$c++;
					$contenido[$f][$c] = $row["nombre_rubro"];$c++;
					$contenido[$f][$c] = $row["unidad"];$c++;
					$contenido[$f][$c] = $row["cantidad_cotizada"];$c++;
					$contenido[$f][$c] = number_format($row["precio"],2,',','.');$c++;
					$contenido[$f][$c] = number_format($row["monto"],2,',','.');$c++;
					/*$contenido[$f][$c] = "";$c++;
					$contenido[$f][$c] = "";$c++;
					$contenido[$f][$c] = "";$c++;
					$contenido[$f][$c] = "";$c++;
					$contenido[$f][$c] = "";$c++;
					$contenido[$f][$c] = "";$c++;
					$contenido[$f][$c] = "";$c++;*/
					/*<td align="right" valign="top" class="normal"></td>
					<td align="right" valign="top" class="normal"></td>
					<td align="right" valign="top" class="normal"></td>
					<td align="right" valign="top" class="normal"></td>
					<td align="right" valign="top" class="normal"></td>
					<td valign="top" class="normal"></td>
				</tr>*/
		
				$ordenDeCompraAnteriorTotal += $row["monto"];
			}
		}
		$total += $ordenDeCompraAnteriorTotal;
		if ( $ordenDeCompraAnterior!="" ) {
		/*	<tr style="<?= $color?>"><td colspan="22" style="border-bottom: solid 1px #C3ECCC;">&nbsp;</td></tr>
			<script>*/
			/*$contenido[$f][$c] = number_format($ordenDeCompraAnteriorBase8,2,',','.');$c++;
			$contenido[$f][$c] = number_format($ordenDeCompraAnteriorMonto8,2,',','.');$c++;
			$contenido[$f][$c] = number_format($ordenDeCompraAnteriorBase12,2,',','.');$c++;
			$contenido[$f][$c] = number_format($ordenDeCompraAnteriorMonto12,2,',','.');$c++;
			$contenido[$f][$c] = number_format($ordenDeCompraAnteriorTotal,2,',','.');$c++;*/
				/*$("#base8<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorBase8,2,',','.');?>");
				$("#monto8<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorMonto8,2,',','.');?>");
				$("#base12<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorBase12,2,',','.');?>");
				$("#monto12<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorMonto12,2,',','.');?>");
				$("#total<?= $ordenDeCompraAnterior?>").html("<?= number_format($ordenDeCompraAnteriorTotal,2,',','.');?>");*/
			/*</script>*/

		}
		if($total>0){
				$f++;
				$f++;
				$c=0;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;								
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;
				$contenido[$f][$c] = "";$c++;												
				$contenido[$f][$c] = "Total IVA 8%";$c++;
				$contenido[$f][$c] = number_format($totalIva8,2,',','.');$c++;
				$contenido[$f][$c] = "Total IVA 12%:";$c++;
				$contenido[$f][$c] = number_format($totalIva12,2,',','.');$c++;
				$contenido[$f][$c] = "Total:";$c++;
				$contenido[$f][$c] = number_format($total,2,',','.');$c++;
		}
	}
	createExcel("compras.xls",$contenido);