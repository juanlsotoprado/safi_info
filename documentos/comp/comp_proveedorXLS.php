<?php 

ob_start();
session_start();
require_once("../../includes/excel.php");
require_once("../../includes/excel-ext.php");
require_once("../../includes/conexion.php");
require_once("../../includes/fechas.php");

if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
$pressAnno = $_SESSION['an_o_presupuesto'];
//$pressAnno = 2014;

$fechaInicio = $_POST['fechaInicio'];
$fechaFin = $_POST['fechaFin'];
$compId = $_POST['compId'];
$asunto = $_POST['asunto'];
//$tipoActividad = $_POST['tipoActividad'];
$proyectoAccion = $_POST['proyectoAccion'];
$rifProveedor = $_POST['rifProveedor'];
$palabraClave = $_POST['palabraClave'];
$estatus = $_POST['estatus'];
$centroGestor = $_POST['centroGestor'];
$mostrarPartidas = $_POST['mostrarPartidas'];

if ($_POST['buscar']=="true") {

$query = "
						SELECT
							s.comp_id, 
							s.comp_fecha,
							s.fecha,
							s.pcta_id,
							s.centro_gestor,
							s.centro_costo,
							s.fecha_reporte,
							s.estado,
							s.infocentro,
							s.tipo_obra,
							s.monto,
							s.id_proyecto_accion,
							s.id_accion_especifica,
							s.rif_proveedor_sugerido,
							s.beneficiario,
							s.asunto,
							s.empl_nombres,
							s.empl_apellidos,
							s.dependencia,
							s.comp_estatus,
							s.comp_documento,
							s.comp_descripcion,
							--s.actividad,
							--s.evento,
							s.control,
							s.localidad,
							s.comp_observacion,
							s.fecha_inicio,
							s.fecha_fin,
							ARRAY
							(
								(
									SELECT
										COALESCE(SUM(scd.cadt_monto),0)||'||'||COALESCE(sp.sopg_id,'')
									FROM
										sai_sol_pago sp
										LEFT OUTER JOIN sai_causado sc ON (
																			sc.pres_anno = ".$pressAnno." AND

																			--sc.esta_id <> 15 AND

																			sc.esta_id <> 2 AND
																			sp.sopg_id = sc.caus_docu_id 
										)
										LEFT OUTER JOIN sai_causad_det scd ON (
																				sc.caus_id = scd.caus_id AND
																				sc.pres_anno = scd.pres_anno AND
																				scd.cadt_id_p_ac = s.id_proyecto_accion AND
																				scd.cadt_cod_aesp = s.id_accion_especifica AND
																				scd.cadt_abono = '1' AND
																				scd.part_id NOT LIKE '4.11.0%' ";
			if ( $mostrarPartidas=='true' ) {
				$query .= 														" AND scd.part_id = s.part_id ";
			}
			$query .= 		"			)
									WHERE
										sp.comp_id = s.comp_id ";
			if ( $fechaInicio!=null && $fechaInicio!='' && $fechaFin!=null && $fechaFin!='' ) {				
				$query .= 	"			AND CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND 
										(sc.fecha_anulacion IS NULL OR (CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) ";
				//$query .= 	" 			AND sc.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY')+1 ";
			} else {
				$query .= 	"			AND sp.esta_id <> 15 AND
		 								sc.esta_id <> 15 ";
			}
			$query .= 				"GROUP BY sp.sopg_id
								)
								UNION 
								( 
									SELECT
	 									COALESCE(SUM(scd.cadt_monto),0)||'||'||COALESCE(sci.comp_id,'')
									FROM
										sai_codi sci
										LEFT OUTER JOIN sai_causado sc ON (
																			sc.pres_anno = ".$pressAnno." AND
					
																			--sc.esta_id <> 15 AND
					
																			sc.esta_id <> 2 AND
																			sci.comp_id = sc.caus_docu_id 
										)
										LEFT OUTER JOIN sai_causad_det scd ON (
																				sc.caus_id = scd.caus_id AND
																				sc.pres_anno = scd.pres_anno AND
																				scd.part_id NOT LIKE '4.11.0%' ";
			if ( $mostrarPartidas=='true' ) {
				$query .= 														" AND scd.part_id = s.part_id ";
			}
			$query .= 					")
									WHERE
										sci.nro_compromiso = s.comp_id ";			
			if ( $fechaInicio!=null && $fechaInicio!='' && $fechaFin!=null && $fechaFin!='' ) {
				$query .= 	"			AND CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND
										(sc.fecha_anulacion IS NULL OR (CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) AND 
										sci.comp_fec BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY')";
				/*$query .= 				"AND sc.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY')+1 
										AND sci.comp_fec BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY') ";*/
			} else {
				$query .= 	"			AND sci.esta_id <> 15 AND
		 								sc.esta_id <> 15 ";
			}
			$query .= 	"			GROUP BY sci.comp_id
								)
							) AS causados, ";
			if ( $mostrarPartidas=='true' ) {
				$query .= 	"s.part_id ";
			} else {
				$query .=	"ARRAY
							(
								SELECT
									CASE 
										WHEN ( spc.pgch_id IS NOT NULL AND spc.pgch_id <> '' ) THEN
											COALESCE(SUM(spd.padt_monto),0)||'||'||COALESCE(spc.nro_cuenta,' ')||'||'||COALESCE(spc.pgch_id,' ')	
										WHEN ( spt.trans_id IS NOT NULL AND spt.trans_id <> '' ) THEN
											COALESCE(SUM(spd.padt_monto),0)||'||'||COALESCE(spt.nro_cuenta_emisor,' ')||'||'||COALESCE(spt.trans_id,' ')
										ELSE ''
									END 
								FROM 
									(
										SELECT
											ssp.sopg_id,
											ssp.esta_id
										FROM
											sai_sol_pago ssp
										WHERE ";
				if ( $fechaInicio==null || $fechaInicio=='' || $fechaFin==null || $fechaFin=='' ) {
					$query .=				" ssp.esta_id <> 15 AND ";
				}
				$query .=					" ssp.comp_id = s.comp_id 
									) AS ssp
									LEFT OUTER JOIN
										(
											SELECT
												spc.pgch_id,
												spc.docg_id,
												spc.nro_cuenta,
												spc.esta_id
											FROM 
												sai_pago_cheque spc
											WHERE ";
				if ( $fechaInicio==null || $fechaInicio=='' || $fechaFin==null || $fechaFin=='' ) {
					$query .=					" spc.esta_id <> 15 AND ";
				}
				$query .=						" spc.esta_id <> 2 AND
												spc.pres_anno_docg = ".$pressAnno."
										) AS spc
										ON (spc.docg_id = ssp.sopg_id)
									LEFT OUTER JOIN
										(
											SELECT
												spt.trans_id,
												spt.docg_id,
												spt.nro_cuenta_emisor,
												spt.esta_id
											FROM 
												sai_pago_transferencia spt
											WHERE ";
				if ( $fechaInicio==null || $fechaInicio=='' || $fechaFin==null || $fechaFin=='' ) {
					$query .=					" spt.esta_id <> 15 AND ";
				}
				$query .=						" spt.esta_id <> 2 AND
												spt.pres_anno_docg = ".$pressAnno."
										) AS spt
										ON (spt.docg_id = ssp.sopg_id) 
									LEFT OUTER JOIN 
										(
											SELECT
												sp.paga_id,
												sp.paga_docu_id
											FROM 
												sai_pagado sp 
											WHERE
												sp.pres_anno = ".$pressAnno." AND
												sp.esta_id <> 2 ";
				if ( $fechaInicio!=null && $fechaInicio!='' && $fechaFin!=null && $fechaFin!='' ) {
					$query .=					" AND CAST(sp.paga_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND
												(sp.fecha_anulacion IS NULL OR (CAST(sp.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) ";
					//$query .= 					"AND sp.paga_fecha BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY')+1 ";
				} else {
					$query .=					" AND sp.esta_id <> 15 ";
				}
				$query .= 				") AS sp
										ON (sp.paga_docu_id = spc.pgch_id OR sp.paga_docu_id = spt.trans_id) 
									LEFT OUTER JOIN
										(
											SELECT
												spd.paga_id,
												spd.padt_id_p_ac,
												spd.padt_cod_aesp,
												spd.padt_monto
											FROM 
												sai_pagado_dt spd
											WHERE
												spd.pres_anno = ".$pressAnno." AND 
												spd.part_id NOT LIKE '4.11.0%' AND 
												spd.padt_id_p_ac = s.id_proyecto_accion AND 
												spd.padt_cod_aesp = s.id_accion_especifica
										) AS spd
										ON (sp.paga_id = spd.paga_id)
								GROUP BY 
									spc.nro_cuenta,
									spt.nro_cuenta_emisor,
									spc.pgch_id,
									spt.trans_id
					 			UNION
					 			SELECT 
									COALESCE(SUM(spd.padt_monto),0)||'|| ||'||COALESCE(sci.comp_id,' ')
					 			FROM 
									(
										SELECT
											sci.comp_id											
										FROM
											sai_codi sci
										WHERE
											sci.nro_compromiso = s.comp_id ";
				if ( $fechaInicio!=null && $fechaInicio!='' && $fechaFin!=null && $fechaFin!='' ) {
					$query .=				"AND sci.comp_fec BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY') ";
				}
				$query .= 			") AS sci
									LEFT OUTER JOIN
										(
											SELECT
												sp.paga_id,
												sp.paga_docu_id
											FROM 
												sai_pagado sp 
											WHERE
												sp.pres_anno = ".$pressAnno." AND
												sp.esta_id <> 2 ";
				if ( $fechaInicio!=null && $fechaInicio!='' && $fechaFin!=null && $fechaFin!='' ) {
					$query .=					" AND CAST(sp.paga_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND
												(sp.fecha_anulacion IS NULL OR (CAST(sp.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) ";
					//$query .= 					"AND sp.paga_fecha BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY')+1 ";
				} else {
					$query .=					" AND sp.esta_id <> 15 ";	
				}
				$query .= 				") AS sp
										ON (sci.comp_id = sp.paga_docu_id)
									LEFT OUTER JOIN
										(
											SELECT
												spd.paga_id,
												spd.padt_id_p_ac,
												spd.padt_cod_aesp,
												spd.padt_monto
											FROM 
												sai_pagado_dt spd
											WHERE
												spd.pres_anno = ".$pressAnno." AND 
												spd.part_id NOT LIKE '4.11.0%' AND 
												spd.padt_id_p_ac = s.id_proyecto_accion AND 
												spd.padt_cod_aesp = s.id_accion_especifica
										) AS spd
										ON (sp.paga_id = spd.paga_id)										
								GROUP BY sci.comp_id
							) AS pagados ";
			}
			$query .=	"	FROM
							(
							SELECT
								sci.comp_id,
								sc.comp_fecha,
								TO_CHAR(sc.comp_fecha, 'DD/MM/YYYY') AS fecha,
								sc.pcta_id AS pcta_id,
								COALESCE(sae.centro_gestor,'') AS centro_gestor,
								COALESCE(sae.centro_costo,'') AS centro_costo,
								TO_CHAR(sc.fecha_reporte, 'DD/MM/YYYY') AS fecha_reporte,
								sc.esta_id AS estado,
								'' AS infocentro, 
								'' AS tipo_obra,
								SUM(sci.comp_monto) AS monto,
								sae.id_proyecto_accion,
								sae.id_accion_especifica,
								sc.rif_sugerido AS rif_proveedor_sugerido,
								sc.beneficiario,
								sca.cpas_nombre AS asunto,
								se.empl_nombres,
								se.empl_apellidos,
								sd.depe_nombre AS dependencia,
								sc.comp_estatus,
								sc.comp_documento,
								sc.comp_descripcion,
								--sta.nombre AS actividad,
								--ste.nombre AS evento,
								scc.nombre AS control,
								sev.nombre AS localidad,
								sc.comp_observacion,
								TO_CHAR(sc.fecha_inicio, 'DD/MM/YYYY') AS fecha_inicio,
								TO_CHAR(sc.fecha_fin, 'DD/MM/YYYY') AS fecha_fin ";
			if ( $mostrarPartidas=='true' ) {
				$query .= 		", sci.part_id ";
			}
			$query .= 		"FROM ";
			if ( $fechaInicio!=null && $fechaInicio!='' && $fechaFin!=null && $fechaFin!='' ) {
				$query .= 		"(
									SELECT
										scit.comp_id,
										scit.comp_acc_pp,
										scit.comp_acc_esp, ";
				if ( $mostrarPartidas=='true' ) {
					$query .= 			"scit.comp_sub_espe AS part_id, ";
				}
				$query .= 				"SUM(scit.comp_monto) AS comp_monto
									FROM
										(
											SELECT
												scit.comp_id,
												MAX(scit.comp_fecha) AS comp_fecha
											FROM
												sai_comp_imputa_traza scit
											WHERE
												scit.comp_fecha BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY')+1
											GROUP BY 
												scit.comp_id
										) AS s
										INNER JOIN sai_comp_imputa_traza scit ON (scit.comp_id = s.comp_id AND scit.comp_fecha = s.comp_fecha)
									WHERE 
										scit.comp_monto > 0 AND
										s.comp_id NOT IN
											(
												SELECT
													sct.comp_id
												FROM 
													sai_comp_traza sct
												WHERE
													sct.comp_fecha2 BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY')+1 AND
													(sct.esta_id = 15 OR sct.esta_id = 2)  
											)
									GROUP BY 
										scit.comp_id,
										scit.comp_acc_pp,
										scit.comp_acc_esp ";
				if ( $mostrarPartidas=='true' ) {
					$query .= 			", scit.comp_sub_espe ";
				}
				$query .= 		") AS sci ";
			} else {
				$query .= 		"(
									SELECT
										sci.comp_id,
										sci.comp_acc_pp,
										sci.comp_acc_esp, ";
				if ( $mostrarPartidas=='true' ) {
					$query .= 			"sci.comp_sub_espe AS part_id, ";
				}
				$query .= 				"SUM(sci.comp_monto) AS comp_monto
									FROM
										sai_comp_imputa sci
									WHERE 
										sci.comp_monto > 0
									GROUP BY
										sci.comp_id,
										sci.comp_acc_pp,
										sci.comp_acc_esp ";
				if ( $mostrarPartidas=='true' ) {
					$query .= 			", sci.comp_sub_espe ";
				}
				$query .= 		") AS sci ";
			}
			$query .= 			"INNER JOIN sai_comp sc ON (sci.comp_id = sc.comp_id)
								LEFT OUTER JOIN 
								(
									SELECT 
										s.id_proyecto_accion,
										s.id_accion_especifica,
										s.tipo,
										s.pres_anno,
										s.centro_gestor,
										s.centro_costo
									FROM 
										(
											SELECT 
												spae.proy_id AS id_proyecto_accion,
												spae.paes_id AS id_accion_especifica,
												1 AS tipo,
												spae.pres_anno,
												spae.centro_gestor, 
												spae.centro_costo
											FROM 
												sai_proy_a_esp spae 
											WHERE 
												spae.pres_anno = '".$pressAnno."'
											UNION
											SELECT 
												sae.acce_id AS id_proyecto_accion, 
												sae.aces_id AS id_accion_especifica,
												'0' AS tipo,
	     										sae.pres_anno,
												sae.centro_gestor,
												sae.centro_costo
											FROM 
												sai_acce_esp sae 
											WHERE 
												sae.pres_anno = '".$pressAnno."'
										) AS s
									GROUP BY 
										s.id_proyecto_accion,
										s.id_accion_especifica,
										s.tipo,
										s.pres_anno, 
										s.centro_gestor,
										s.centro_costo
								) AS sae ON (sci.comp_acc_pp = sae.id_proyecto_accion AND sci.comp_acc_esp = sae.id_accion_especifica)
								LEFT OUTER JOIN sai_empleado se ON (sc.usua_login = se.empl_cedula)
								LEFT OUTER JOIN sai_compromiso_asunt sca ON (sc.comp_asunto = sca.cpas_id)
								LEFT OUTER JOIN sai_dependenci sd ON (sc.comp_gerencia = sd.depe_id)
								--LEFT OUTER JOIN sai_tipo_actividad sta ON (sc.id_actividad = sta.id)
								--LEFT OUTER JOIN sai_tipo_evento ste ON (sc.id_evento = ste.id)
								LEFT OUTER JOIN sai_control_comp scc ON (sc.control_interno = scc.id)
								LEFT OUTER JOIN safi_edos_venezuela sev ON (sc.localidad = sev.id)
						 	";
			
			/*$hayCondicion = false;
			if ( $fechaInicio!=null && $fechaInicio!='' && $fechaFin!=null && $fechaFin!='' ) {
				$query .= " WHERE
								sc.comp_fecha BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY')+1 ";
				$hayCondicion = true;
			}*/
			$query .= " WHERE
							DATE_PART('year', sc.comp_fecha) = ".$pressAnno." ";
			$hayCondicion = true;
			
			if ( $compId!=null && $compId!='' && $compId!='comp-') {
				if ( $hayCondicion == false ) {
					$query .= " WHERE ";
				} else {
					$query .= " AND ";
				}
				$query .= " sc.comp_id='".$compId."' ";
				$hayCondicion = true;
			}
			
			if ( $asunto != null && $asunto != '' ) {
				if ( $hayCondicion == false ) {
					$query .= " WHERE ";	
				} else {
					$query .= " AND ";
				}
				$query .= " sc.comp_asunto='".$asunto."' ";
				$hayCondicion = true;
			}			

			/*if ( $tipoActividad!=null && $tipoActividad!='' ) {
				if ( $hayCondicion == false ) {
					$query .= " WHERE ";
				} else {
					$query .= " AND ";
				}
				$query .= " sc.id_actividad='".$tipoActividad."' ";
				$hayCondicion = true;
			}*/			

			if ( $estatus!=null && $estatus!='' ){
				if ( $hayCondicion == false ) {
					$query .= " WHERE ";
				} else {
					$query .= " AND ";
				}
				$query .= " sc.comp_estatus='".$estatus."' ";
				$hayCondicion = true;
			}
			
			if ( $proyectoAccion!=null && $proyectoAccion!='' ) {
				list($proyecto,$accionEspecififica) = split(':::', $proyectoAccion);
				if ( $hayCondicion == false ) {
					$query .= " WHERE ";
				} else {
					$query .= " AND ";
				}
				$query .= " sci.comp_acc_pp='".$proyecto."' AND sci.comp_acc_esp='".$accionEspecififica."' ";
				$hayCondicion = true;
			}
			
			if ( $centroGestor!=null && $centroGestor!='' ){
				if ( $hayCondicion == false ) {
					$query .= " WHERE ";
				} else {
					$query .= " AND ";
				}
				$query .= " sae.centro_gestor LIKE '%".$centroGestor."%' ";//REVISAR
				$hayCondicion = true;
			}
			
			if ( $rifProveedor!=null && $rifProveedor!='' ) {
				if ( $hayCondicion == false ) {
					$query .= " WHERE ";
				} else {
					$query .= " AND ";
				}
				$query .= " ( UPPER(sc.rif_sugerido) LIKE UPPER('%".$rifProveedor."%') OR UPPER(sc.beneficiario) LIKE UPPER('%".$rifProveedor."%') ) ";
				$hayCondicion = true;
			}
			
			if ( $palabraClave!=null && $palabraClave!='' ) {
				if ( $hayCondicion == false ) {
					$query .= " WHERE ";
				} else {
					$query .= " AND ";
				}
				$query .= " LOWER(sc.comp_descripcion) LIKE '%'||LOWER('".$palabraClave."')||'%' ";
				$hayCondicion = true;				
			}
			
			$query .= " 	GROUP BY
								sci.comp_id,
								sc.comp_fecha,
								sc.pcta_id,
								sae.centro_gestor,
								sae.centro_costo,
								sc.fecha_reporte,
								sc.esta_id,
								infocentro, 
								tipo_obra,
								sae.id_proyecto_accion,
								sae.id_accion_especifica,
								sc.rif_sugerido,
								sc.beneficiario,
								sca.cpas_nombre,
								se.empl_nombres,
								se.empl_apellidos,
								sd.depe_nombre,
								sc.comp_estatus,
								sc.comp_documento,
								sc.comp_descripcion,
								--sta.nombre,
								--ste.nombre,
								scc.nombre,
								sev.nombre,
								sc.comp_observacion,
								sc.fecha_inicio,
								sc.fecha_fin ";
			if ( $mostrarPartidas=='true' ) {
				$query .= 		", sci.part_id ";
			}
			$query .= 		") AS s					
							ORDER BY 
								s.comp_fecha, s.comp_id ";
			if ( $mostrarPartidas=='true' ) {
				$query .= 		", s.part_id ";
			}

	$resultado = pg_query($conexion,$query) or die("Error al consultar la descripcion del compromiso");

	$f = 0;
	$c = 0;

	//$contenido[$f][0]=utf8_decode("RESULTADO DE LA BÚSQUEDA DE COMPROMISOS");
	$contenido[$f][0]=utf8_decode("Total ".pg_num_rows($resultado)." registros");
	$f++;

	$contenido[$f][$c]=utf8_decode("Código del Documento");$c++;
	$contenido[$f][$c]=utf8_decode("Fecha");$c++;
	$contenido[$f][$c]=utf8_decode("Elaborado Por");$c++;
	$contenido[$f][$c]=utf8_decode("Unidad Solicitante");$c++;
	$contenido[$f][$c]=utf8_decode("Punto de Cuenta");$c++;
	$contenido[$f][$c]=utf8_decode("Asunto");$c++;
	$contenido[$f][$c]=utf8_decode("Estatus");$c++;
	$contenido[$f][$c]=utf8_decode("Nº Documento");$c++;
	$contenido[$f][$c]=utf8_decode("Proveedor");$c++;
	$contenido[$f][$c]=utf8_decode("CI/RIF");$c++;
	$contenido[$f][$c]=utf8_decode("Centro Gestor");$c++;
	$contenido[$f][$c]=utf8_decode("Cestro de Costo");$c++;
	if ( $mostrarPartidas=='true' ) {
		$contenido[$f][$c]=utf8_decode("Partida");$c++;
	}
	$contenido[$f][$c]=utf8_decode("Monto Solicitado");$c++;
	$contenido[$f][$c]=utf8_decode("Descripción");$c++;
	//$contenido[$f][$c]=utf8_decode("Tipo Actividad");$c++;
	//$contenido[$f][$c]=utf8_decode("Tipo Evento");$c++;
	$contenido[$f][$c]=utf8_decode("Control Interno");$c++;
	$contenido[$f][$c]=utf8_decode("Estado");$c++;
	/*$contenido[$f][$c]=utf8_decode("Infocentro");$c++;
	$contenido[$f][$c]=utf8_decode("Nº Participantes");$c++;*/
	$contenido[$f][$c]=utf8_decode("Duración de la actividad");$c++;
	$contenido[$f][$c]=utf8_decode("Observación");$c++;
	$contenido[$f][$c]=utf8_decode("Fecha de Reporte");$c++;
	$contenido[$f][$c]=utf8_decode("Monto Causado");$c++;
	$contenido[$f][$c]=utf8_decode("Documentos Causado");$c++;
	if ( $mostrarPartidas!='true' ) {
		$contenido[$f][$c]=utf8_decode("Monto Pagado");$c++;
		$contenido[$f][$c]=utf8_decode("Documentos Pagado");$c++;
		$contenido[$f][$c]=utf8_decode("Número de cuenta");$c++;
	}

	$totalMontoSolicitado = 0.0;
	$totalMontoCausado = 0.0;
	$totalMontoPagado = 0.0;
	while($row=pg_fetch_array($resultado)) {
		$c=0;
		$f++;
		
		$info_adicional=$row['comp_observacion'];
		$longitud=strlen($info_adicional);
		$info_adicional=substr($info_adicional,1,$longitud);
		$posicion = strpos($info_adicional, ":");
		$posicion2 = strpos($info_adicional, "*");
		
		$infocentro=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
		$info_adicional=substr($info_adicional,$posicion2+1);
		$posicion = strpos($info_adicional, ":");
		$posicion2 = strpos($info_adicional, "*");
			
		$participante=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
		$info_adicional=substr($info_adicional,$posicion2+1);
		$posicion = strpos($info_adicional, ":");
		$posicion2 = strpos($info_adicional, "*");
		$observacion=substr($info_adicional,$posicion+1);
		
		$contenido[$f][$c]=$row['comp_id'];$c++;
		$contenido[$f][$c]=$row['fecha'];$c++;
		$contenido[$f][$c]=$row['empl_nombres']." ".$row['empl_apellidos'];$c++;
		$contenido[$f][$c]=$row['dependencia'];$c++;
		if ( $row['pcta_id'] == null || $row['pcta_id'] == '' || $row['pcta_id'] == '0' ){
			$contenido[$f][$c]="N/A";$c++;
		} else {
			$contenido[$f][$c]=$row['pcta_id'];$c++;
		}
		$contenido[$f][$c]=$row['asunto'];$c++;
		$contenido[$f][$c]=$row['comp_estatus'];$c++;
		$contenido[$f][$c]=$row['comp_documento'];$c++;
		$contenido[$f][$c]=$row['beneficiario'];$c++;
		$contenido[$f][$c]=$row['rif_proveedor_sugerido'];$c++;
		$contenido[$f][$c]=$row['centro_gestor'];$c++;
		$contenido[$f][$c]=$row['centro_costo'];$c++;
		if ( $mostrarPartidas=='true' ) {
			$contenido[$f][$c]=$row['part_id'];$c++;
		}
		//$contenido[$f][$c]=(number_format($row['monto'],2,',','.'));$c++;
		$contenido[$f][$c]=floatval($row['monto']);$c++;
		$contenido[$f][$c]=$row['comp_descripcion'];$c++;
		//$contenido[$f][$c]=$row['actividad'];$c++;
		//$contenido[$f][$c]=$row['evento'];$c++;
		$contenido[$f][$c]=$row['control'];$c++;
		if ( $row['localidad']==null || $row['localidad']=='' ){
			$contenido[$f][$c]="N/A";$c++;
		} else {
			$contenido[$f][$c]=$row['localidad'];$c++;
		}
		/*$contenido[$f][$c]=$infocentro;$c++;
		$contenido[$f][$c]=$participante;$c++;*/
		$contenido[$f][$c]=$row['fecha_inicio']."-".$row['fecha_fin'];$c++;
		$contenido[$f][$c]=$observacion;$c++;
		$contenido[$f][$c]=$row['fecha_reporte'];$c++;
		
		$montoCausado=0;
		$montoPagado=0;
		$sopgsId="";
		$pagadosId="";
		$nroCuentas="";
		
		if ( isset($row['causados']) ) {
			$causados = pg_array_parse($row['causados'], $causados);
			$i = 0;
			while ( $i < sizeof($causados) ) {
				$causado = explode("||", $causados[$i]);
				if ( isset($causado[0]) ) {
					$montoCausado += $causado[0];
				}
				if ( isset($causado[1]) ) {
					$sopgsId .= $causado[1]."\n";
				}
				$i++;
			}
		}
		if ( isset($row['pagados']) ) {
			$pagados = pg_array_parse($row['pagados'], $pagados);
			$i = 0;
			while ( $i < sizeof($pagados) ) {
				$pagado = explode("||", $pagados[$i]);
				if ( isset($pagado[0]) ) {
					$montoPagado += $pagado[0];
				}
				if ( isset($pagado[1]) ) {
					$nroCuentas .= $pagado[1]."\n";
				}
				if ( isset($pagado[2]) ) {
					$pagadosId .= $pagado[2]."\n";
				}
				$i++;
			}
		}
		//$contenido[$f][$c]=(number_format($montoCausado,2,',','.'));$c++;
		$contenido[$f][$c]=$montoCausado;$c++;
		$contenido[$f][$c]=$sopgsId;$c++;
		if ( $mostrarPartidas!='true' ) {
			//$contenido[$f][$c]=(number_format($montoPagado,2,',','.'));$c++;
			$contenido[$f][$c]=$montoPagado;$c++;
			$contenido[$f][$c]=$pagadosId;$c++;
			$contenido[$f][$c]=$nroCuentas;$c++;
		}
		$totalMontoSolicitado += $row['monto'];
		$totalMontoCausado += $montoCausado;
		$totalMontoPagado += $montoPagado;
	}
	$c=0;
	$f++;
	$contenido[$f][$c]="";$c++;
	$contenido[$f][$c]="";$c++;
	$contenido[$f][$c]="";$c++;
	$contenido[$f][$c]="";$c++;
	$contenido[$f][$c]="";$c++;
	$contenido[$f][$c]="";$c++;
	$contenido[$f][$c]="";$c++;
	$contenido[$f][$c]="";$c++;
	$contenido[$f][$c]="";$c++;
	$contenido[$f][$c]="";$c++;
	$contenido[$f][$c]="";$c++;
	if ( $mostrarPartidas=='true' ) {
		$contenido[$f][$c]="";$c++;
	}
	$contenido[$f][$c]="Total";$c++;
	$contenido[$f][$c]=$totalMontoSolicitado;$c++;
	$contenido[$f][$c]="";$c++;
	/*$contenido[$f][$c]="";$c++;
	$contenido[$f][$c]="";$c++;*/
	$contenido[$f][$c]="";$c++;
	$contenido[$f][$c]="";$c++;
	/*$contenido[$f][$c]="";$c++;
	 $contenido[$f][$c]="";$c++;*/
	$contenido[$f][$c]="";$c++;
	$contenido[$f][$c]="";$c++;
	$contenido[$f][$c]="";$c++;
	$contenido[$f][$c]=$totalMontoCausado;$c++;
	$contenido[$f][$c]="";$c++;
	if ( $mostrarPartidas!='true' ) {
		$contenido[$f][$c]=$totalMontoPagado;$c++;
		$contenido[$f][$c]="";$c++;
		$contenido[$f][$c]="";$c++;		
	}
}
createExcel("reporte-proveedor.xls",$contenido);
?>