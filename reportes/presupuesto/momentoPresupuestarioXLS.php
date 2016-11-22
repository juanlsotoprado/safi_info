<?php
ob_start();
session_start();
require_once("../../includes/excel.php");
require_once("../../includes/excel-ext.php");
require_once("../../includes/conexion.php");

if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();

$usuario = $_SESSION['login'];
$user_perfil_id = $_SESSION['user_perfil_id'];
$pres_anno = $_SESSION['an_o_presupuesto'];
//$pres_anno = 2014;


//$pres_anno = 2014;


if ($_REQUEST['hid_validar']==2) {
	
	

	if (strlen($_REQUEST['fechaInicio'])>5) {
		$fechaInicio=trim($_REQUEST['fechaInicio']);
		$fechaFin=trim($_REQUEST['fechaFin']);
		$fechaInicio=substr($fechaInicio,6,4)."-".substr($fechaInicio,3,2)."-".substr($fechaInicio,0,2);
		$fechaFin=substr($fechaFin,6,4)."-".substr($fechaFin,3,2)."-".substr($fechaFin,0,2);
	}

	$suma_apart_monto=0;
	$suma_comp_monto=0;
	$suma_caus_monto=0;
	$suma_pag_monto=0;

	$ano = $_REQUEST['ano'];

	if (strlen($_REQUEST['fechaInicio'])<5) {
		$fechaInicio=$_REQUEST['ano']."-01-01";
		$fechaFin=$_REQUEST['ano']."-12-31";
	}

	if (strlen($_REQUEST['proyac'])>8) {
		list( $idProyectoAccion, $idAccionEspecifica ) = split( ':::', $_REQUEST['proyac'] );
	}

	$sql_apart="	SELECT 
						nucleo.codigo, 
						nucleo.partida AS partida, 
						nucleo.pcta_asociado,
						nucleo.monto, 
						nucleo.fecha, 
						COALESCE(p.centro_gestor,'')||COALESCE(a.centro_gestor,'') || '/'|| COALESCE(p.centro_costo,'')||COALESCE(a.centro_costo,'') AS centro
					FROM 
						(
							SELECT 
								a.pcta_id AS codigo, 
								a.pcta_monto AS monto, 
							    b.pcta_asociado AS pcta_asociado,
								a.pcta_acc_pp, 
								a. pcta_acc_esp, 
								a.pcta_sub_espe AS partida, 
								TO_CHAR(b.pcta_fecha, 'DD/MM/YYYY') AS fecha
							FROM 
								sai_pcta_imputa_traza a, 
								sai_pcta_traza b
							WHERE 
								a.pres_anno=".$ano." AND 
								a.pcta_id=b.pcta_id AND 
								a.pres_anno=".$ano. 
								(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" AND a.pcta_id LIKE '%".$_REQUEST['compromiso']."%' ":"").
								(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?" AND a.pcta_id LIKE '%".$_REQUEST['pcta']."%' ":"").
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND TO_TIMESTAMP(b.pcta_fecha, 'YYYY/MM/DD HH24:MI:SS') BETWEEN TO_TIMESTAMP('".$fechaInicio." 00:00:00', 'YYYY/MM/DD HH24:MI:SS') AND TO_TIMESTAMP('".$fechaFin." 24:59:59', 'YYYY/MM/DD HH24:MI:SS') ":"").
								(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" AND a.pcta_acc_pp='".$idProyectoAccion."' AND a.pcta_acc_esp='".$idAccionEspecifica."' ":"").
								(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" AND a.pcta_sub_espe LIKE '".$_REQUEST['partida']."%' ":"").
							"ORDER BY 
								a.pcta_id, 
								a.pcta_sub_espe
						) AS nucleo
						LEFT OUTER JOIN sai_proy_a_esp p ON (nucleo.pcta_acc_pp=p.proy_id AND nucleo.pcta_acc_esp=p.paes_id)
						LEFT OUTER JOIN sai_acce_esp a ON (nucleo.pcta_acc_esp = a.aces_id AND nucleo.pcta_acc_pp=a.acce_id)";
	$resultado_set_most_apart=pg_query($conexion,$sql_apart) or die("Error al consultar la descripcion del apartado");

	$sql_comp="	SELECT 
					nucleo.codigo, 
					nucleo.partida AS partida, 
					nucleo.monto, 
					nucleo.fecha, 
					COALESCE(p.centro_gestor,'')||COALESCE(a.centro_gestor,'') || '/'|| COALESCE(p.centro_costo,'')||COALESCE(a.centro_costo,'') AS centro, 
					nucleo.pcta_id AS pcta_id
				FROM
					(
						SELECT 
							a.comp_id AS codigo, 
							a.comp_monto AS monto, 
							a.comp_acc_pp, 
							a.comp_acc_esp, 
							a.comp_sub_espe AS partida, 
							TO_CHAR(a.comp_fecha, 'DD/MM/YYYY') AS fecha, 
							c.pcta_id AS pcta_id
						FROM 
							sai_comp_traza_reporte a, 
							sai_comp c
						WHERE 
							length(c.pcta_id)>4 AND 
							c.comp_id=a.comp_id AND 
							a.pres_anno=".$ano." AND 
							a.pres_anno=".$ano. 
							(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" AND a.comp_id LIKE '%".$_REQUEST['compromiso']."' ":"").
							(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?" AND c.pcta_id LIKE '%".$_REQUEST['pcta']."%' ":"").
							(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND a.comp_fecha BETWEEN TO_TIMESTAMP('".$fechaInicio." 00:00:00','YYYY/MM/DD HH24:MI:SS') AND TO_TIMESTAMP('".$fechaFin." 24:59:59', 'YYYY/MM/DD HH24:MI:SS')":"").
							(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" AND a.comp_acc_pp='".$idProyectoAccion."' AND a.comp_acc_esp='".$idAccionEspecifica."' ":"").
							(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" AND a.comp_sub_espe LIKE '".$_REQUEST['partida']."%' ":"").
						"ORDER BY 
							a.comp_id, 
							a.comp_sub_espe
					) AS nucleo
				LEFT OUTER JOIN sai_proy_a_esp p ON (nucleo.comp_acc_pp=p.proy_id AND nucleo.comp_acc_esp=p.paes_id)
				LEFT OUTER JOIN sai_acce_esp a ON (nucleo.comp_acc_esp = a.aces_id AND nucleo.comp_acc_pp=a.acce_id)";
	$resultado_set_most_comp=pg_query($conexion,$sql_comp) or die("Error al consultar la descripcion del compromiso");

	$sql_comp_ais="	SELECT 
						nucleo.codigo, 
						nucleo.partida AS partida, 
						nucleo.monto, 
						nucleo.fecha, 
						COALESCE(p.centro_gestor,'')||COALESCE(a.centro_gestor,'') || '/'|| COALESCE(p.centro_costo,'')||COALESCE(a.centro_costo,'') AS centro, 
						nucleo.pcta_id AS pcta_id
					FROM 
						(
							SELECT 
								a.comp_id AS codigo, 
								a.comp_monto AS monto, 
								a.comp_acc_pp, 
								a. comp_acc_esp, 
								a.comp_sub_espe AS partida, 
								TO_CHAR(a.comp_fecha, 'DD/MM/YYYY') AS fecha, 
								c.pcta_id AS pcta_id
							FROM 
								sai_comp_traza_reporte a, 
								sai_comp c
							WHERE 
								length(c.pcta_id)<4 AND 
								c.comp_id=a.comp_id AND 
								a.pres_anno=".$ano." AND 
								a.pres_anno=".$ano. 
								(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" AND a.comp_id LIKE '%".$_REQUEST['compromiso']."' ":"").
								(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?" AND c.pcta_id LIKE '%".$_REQUEST['pcta']."%' ":"").
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND a.comp_fecha BETWEEN TO_TIMESTAMP('".$fechaInicio." 00:00:00','YYYY/MM/DD HH24:MI:SS') AND TO_TIMESTAMP('".$fechaFin." 24:59:59', 'YYYY/MM/DD HH24:MI:SS')":"").
								(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" AND a.comp_acc_pp='".$idProyectoAccion."' AND a.comp_acc_esp='".$idAccionEspecifica."' ":"").
								(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" AND a.comp_sub_espe LIKE '".$_REQUEST['partida']."%' ":"").
							"ORDER BY 
								a.comp_id, a
								.comp_sub_espe
						) AS nucleo
					LEFT OUTER JOIN sai_proy_a_esp p ON (nucleo.comp_acc_pp=p.proy_id AND nucleo.comp_acc_esp=p.paes_id)
					LEFT OUTER JOIN sai_acce_esp a ON (nucleo.comp_acc_esp = a.aces_id AND nucleo.comp_acc_pp=a.acce_id)";
	$resultado_set_most_comp_ais=pg_query($conexion,$sql_comp_ais) or die("Error al consultar la descripcion del compromiso");

	$sql_caus2="	SELECT 
						c.caus_docu_id AS causado, 
						s.sopg_id AS codigo, 
						COALESCE(s.comp_id,'') AS comp_id,
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') AS fecha, 
						cd.part_id AS partida, 
						cd.cadt_monto AS monto,
						c.caus_fecha AS fecha_date 
					FROM 
						(
							SELECT * 
							FROM sai_causado c
							WHERE 
								c.pres_anno='".$ano."' AND 
								c.esta_id<>15 AND 
								c.esta_id<>2 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?"AND c.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").
						") AS c
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd
								WHERE
								 	".((strlen($_REQUEST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_REQUEST['partida'])>1)?"cd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM 
									sai_sol_pago s
								WHERE 
									".(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?"s.comp_id LIKE '".$_REQUEST['compromiso']."' AND ":"")."
									".(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										"s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											) AND "
										:"")."
									s.esta_id<>15
							) AS s ON (c.caus_docu_id = s.sopg_id), 
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						cd.cadt_id_p_ac = pa.id_proyecto_accion AND 
						cd.cadt_cod_aesp = pa.id_accion_especifica
					UNION
					SELECT 
						c.caus_docu_id AS causado,
						cdi.comp_id AS codigo, 
						codi.nro_compromiso AS comp_id,
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						dg.numero_reserva AS reserva, 
						TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') AS fecha, 
						cd.part_id AS partida, 
						cd.cadt_monto AS monto,
						c.caus_fecha AS fecha_date  
					FROM 
						(
							SELECT * 
							FROM sai_causado c
							WHERE 
								c.pres_anno='".$ano."' AND 
								c.esta_id<>15 AND 
								c.esta_id<>2 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?"AND c.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").
						") AS c
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd
								WHERE
								 	".((strlen($_REQUEST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_REQUEST['partida'])>1)?"cd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN
							(
								SELECT *
								FROM sai_comp_diario cdi
								WHERE 
									cdi.esta_id<>15 ".
									((strlen($_REQUEST['pcta'])>5)?
										"AND (
												cdi.comp_doc_id IN 
												(
													SELECT sopg_id 
													FROM sai_sol_pago 
													WHERE comp_id IN 
														(
															SELECT comp_id 
															FROM sai_comp_traza 
															WHERE pcta_id LIKE '%".$_REQUEST['pcta']."%'
														)
												) OR 
												cdi.comp_doc_id IN 
												(
													SELECT comp_id 
													FROM sai_comp 
													WHERE pcta_id = '%".$_REQUEST['pcta']."%'
												)
											)":"")."
							) AS cdi ON (c.caus_docu_id = cdi.comp_id)
						INNER JOIN sai_codi codi ON (c.caus_docu_id = codi.comp_id)
						INNER JOIN sai_doc_genera dg ON (c.caus_docu_id = dg.docg_id AND dg.esta_id<>15), 
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						cd.cadt_id_p_ac = pa.id_proyecto_accion AND 
						cd.cadt_cod_aesp = pa.id_accion_especifica ".
						((strlen($_REQUEST['compromiso'])>5)?
							"AND (
									cdi.comp_doc_id IN 
									(
										SELECT sopg_id 
										FROM sai_sol_pago 
										WHERE comp_id LIKE '%".$_REQUEST['compromiso']."'
									) OR 
									codi.nro_compromiso LIKE '%".$_REQUEST['compromiso']."'
								)
							":"")."
					UNION
					SELECT 
						c.caus_docu_id AS causado,
						s.sopg_id AS codigo,
						COALESCE(s.comp_id,'') AS comp_id,
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						--TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') AS fecha,
						TO_CHAR(c.fecha_anulacion, 'DD/MM/YYYY') AS fecha, 
						cd.part_id AS partida, 
						cd.cadt_monto*-1 AS monto,
						c.fecha_anulacion AS fecha_date  
					FROM 
						(
							SELECT * 
							FROM sai_causado c
							WHERE 
								c.pres_anno='".$ano."' AND 
								c.esta_id=15 AND
								SUBSTRING(TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') FROM 4 for 7) <> SUBSTRING(TO_CHAR(c.fecha_anulacion, 'DD/MM/YYYY') FROM 4 for 7) ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?
									"AND c.caus_fecha <= TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND 
									c.fecha_anulacion BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").								
						") AS c
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd 
								WHERE
								 	".((strlen($_REQUEST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_REQUEST['partida'])>1)?"cd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM 
									sai_sol_pago s
								WHERE 
									".(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?"s.comp_id LIKE '".$_REQUEST['compromiso']."' AND ":"")."
									".(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										"s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											) AND ":"")."
									(s.esta_id=15 OR s.esta_id=7)
							) AS s ON (c.caus_docu_id = s.sopg_id), 
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						cd.cadt_id_p_ac = pa.id_proyecto_accion AND 
						cd.cadt_cod_aesp = pa.id_accion_especifica						 
					UNION
					SELECT 
						c.caus_docu_id AS causado,
						s.sopg_id AS codigo, 
						COALESCE(s.comp_id,'') AS comp_id,
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') AS fecha,
						cd.part_id AS partida, 
						cd.cadt_monto AS monto,
						c.caus_fecha AS fecha_date  
					FROM 
						(
							SELECT * 
							FROM sai_causado c
							WHERE 
								c.pres_anno='".$ano."' AND 
								c.esta_id=15 AND 
								SUBSTRING(TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') FROM 4 for 7) <> SUBSTRING(TO_CHAR(c.fecha_anulacion, 'DD/MM/YYYY') FROM 4 for 7) ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?
									"AND c.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') AND
									c.fecha_anulacion > TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":""). 
						") AS c 
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd 
								WHERE
								 	".((strlen($_REQUEST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_REQUEST['partida'])>1)?"cd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM 
									sai_sol_pago s
								WHERE 
									".(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?"s.comp_id LIKE '".$_REQUEST['compromiso']."' AND ":"")."
									".(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										"s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											) AND ":"")."
									(s.esta_id=15 OR s.esta_id=7)
							) AS s ON (c.caus_docu_id = s.sopg_id), 
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						cd.cadt_id_p_ac = pa.id_proyecto_accion AND 
						cd.cadt_cod_aesp = pa.id_accion_especifica
					UNION
					SELECT 
						c.caus_docu_id AS causado,
						s.sopg_id AS codigo, 
						COALESCE(s.comp_id,'') AS comp_id,
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') AS fecha,
						cd.part_id AS partida, 
						cd.cadt_monto AS monto,
						c.caus_fecha AS fecha_date  
					FROM 
						(
							SELECT * 
							FROM sai_causado c
							WHERE 
								c.pres_anno='".$ano."' AND 
								c.esta_id=15 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?
									"AND c.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') AND 
									c.fecha_anulacion <= TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").
						") AS c 
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd 
								WHERE
								 	".((strlen($_REQUEST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_REQUEST['partida'])>1)?"cd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM 
									sai_sol_pago s
								WHERE 
									".(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?"s.comp_id LIKE '".$_REQUEST['compromiso']."' AND ":"")."
									".(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										"s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											) AND ":"")."
									(s.esta_id=15 OR s.esta_id=7)
							) AS s ON (c.caus_docu_id = s.sopg_id), 
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						cd.cadt_id_p_ac = pa.id_proyecto_accion AND 
						cd.cadt_cod_aesp = pa.id_accion_especifica 
					UNION
					SELECT 
						c.caus_docu_id AS causado,
						s.sopg_id AS codigo, 
						COALESCE(s.comp_id,'') AS comp_id,
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						--TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') AS fecha, 
						TO_CHAR(c.fecha_anulacion, 'DD/MM/YYYY') AS fecha,
						cd.part_id AS partida, 
						cd.cadt_monto*-1 AS monto,
						c.fecha_anulacion AS fecha_date  
					FROM 
						(
							SELECT * 
							FROM sai_causado c
							WHERE 
								c.pres_anno='".$ano."' AND 
								c.esta_id=15 ". 
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?
									"AND c.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') AND 
									c.fecha_anulacion <= TO_DATE('".$fechaFin."', 'YYYY-MM-DD')":""). 
						") AS c 
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd 
								WHERE
								 	".((strlen($_REQUEST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_REQUEST['partida'])>1)?"cd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM 
									sai_sol_pago s
								WHERE 
									".(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?"s.comp_id LIKE '".$_REQUEST['compromiso']."' AND ":"")."
									".(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										"s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											) AND ":"")."
									(s.esta_id=15 OR s.esta_id=7)
							) AS s ON (c.caus_docu_id = s.sopg_id), 
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						cd.cadt_id_p_ac = pa.id_proyecto_accion AND 
						cd.cadt_cod_aesp = pa.id_accion_especifica ";
	$sql_caus = "SELECT * FROM (".$sql_caus2.") AS s ORDER BY fecha_date,partida";//causado,partida
	$resultado_set_most_caus=pg_query($conexion,$sql_caus) or die("Error al consultar la descripcion del causado");

	if (strlen($_REQUEST['fechaInicio'])>1) {
		$sql_pag2="	SELECT 
						p.paga_docu_id AS pagado, 
						pc.pgch_id AS codigo, 
						s.comp_id AS comp_id, 
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
						pd.part_id AS partida, 
						pd.padt_monto AS monto,
						p.paga_fecha AS fecha_date
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id<>15 AND 
								p.esta_id<>2 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."','YYYY-MM-DD') AND TO_DATE('".$fechaFin."','YYYY-MM-DD') ":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
							) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM sai_pago_cheque pc
							) AS pc ON (p.paga_docu_id = pc.pgch_id)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM sai_sol_pago s
								WHERE 
									s.esta_id <> 15 ".
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										"AND s.comp_id IN 
										(
											SELECT comp_id 
											FROM sai_comp_traza 
											WHERE 
												esta_id<>2 AND 
												pcta_id LIKE '%".$_REQUEST['pcta']."%'
										)":""
									).
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" AND s.comp_id LIKE '%".$_REQUEST['compromiso']."'":"").
							") AS s ON (pc.docg_id = s.sopg_id), 
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND
						pd.padt_cod_aesp = pa.id_accion_especifica 
					UNION
					SELECT 
						p.paga_docu_id AS pagado, 
						ptr.trans_id AS codigo, 
						s.comp_id AS comp_id, 
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
						pd.part_id AS partida, 
						pd.padt_monto AS monto,
						p.paga_fecha AS fecha_date
					FROM
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id<>15 AND 
								p.esta_id<>2 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."','YYYY-MM-DD') AND TO_DATE('".$fechaFin."','YYYY-MM-DD') ":"").
						") AS p 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
							) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pago_transferencia ptr 
							WHERE
								ptr.esta_id<>15 ".
						") AS ptr ON (p.paga_docu_id = ptr.trans_id)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM sai_sol_pago s
								WHERE 
									s.esta_id <> 15 ".
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										" AND s.comp_id IN 
										(
											SELECT comp_id 
											FROM sai_comp_traza 
											WHERE 
												esta_id<>2 AND 
												pcta_id LIKE '%".$_REQUEST['pcta']."%'
										)":""
									).
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" AND s.comp_id LIKE '%".$_REQUEST['compromiso']."'":"").
							") AS s ON (ptr.docg_id = s.sopg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica  
					UNION
					SELECT 
						p.paga_docu_id AS pagado, 
						cdi.comp_id AS codigo, 
						codi.nro_compromiso AS comp_id, 
 						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
 						dg.numero_reserva AS reserva, 
 						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
 						pd.part_id AS partida, 
 						pd.padt_monto AS monto,
						p.paga_fecha AS fecha_date
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id<>15 AND 
								p.esta_id<>2 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."','YYYY-MM-DD') AND TO_DATE('".$fechaFin."','YYYY-MM-DD') ":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_comp_diario cdi 
								WHERE
									cdi.esta_id<>15 ".
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
									" AND (
											cdi.comp_doc_id IN 
											(
												SELECT sopg_id 
												FROM sai_sol_pago 
												WHERE comp_id IN 
													(
														SELECT comp_id 
														FROM sai_comp_traza 
														WHERE pcta_id LIKE '%".$_REQUEST['pcta']."%'
													)
											) OR 
											cdi.comp_doc_id IN 
											(
												SELECT comp_id 
												FROM sai_comp 
												WHERE pcta_id = '%".$_REQUEST['pcta']."%'
											)
									)":"").
						") AS cdi ON (p.paga_docu_id = cdi.comp_id)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_codi codi ".
						") AS codi ON (cdi.comp_id = codi.comp_id)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_doc_genera dg 
								WHERE
									dg.esta_id<>15 ".
						") AS dg ON (p.paga_docu_id = dg.docg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica ".
						(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?
						" AND (
								cdi.comp_doc_id IN 
								(
									SELECT sopg_id 
									FROM sai_sol_pago 
									WHERE comp_id LIKE '%".$_REQUEST['compromiso']."'
								) OR 
								codi.nro_compromiso LIKE '%".$_REQUEST['compromiso']."'
						) ":"").
					"UNION
					SELECT 
						p.paga_docu_id AS pagado, 
						pc.pgch_id AS codigo, 
						s.comp_id AS comp_id, 
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						--TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
						TO_CHAR(p.fecha_anulacion, 'DD/MM/YYYY') AS fecha,
						pd.part_id AS partida, 
						pd.padt_monto*-1 AS monto,
						p.fecha_anulacion AS fecha_date
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id=15 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND SUBSTRING(TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') FROM 4 for 7) <> SUBSTRING(TO_CHAR(p.fecha_anulacion, 'DD/MM/YYYY') FROM 4 for 7) AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaFin."'":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pago_cheque pc 
								WHERE
									(pc.esta_id=15 OR pc.esta_id=7) ".
						") AS pc ON (p.paga_docu_id = pc.pgch_id)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_sol_pago s ".
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" WHERE s.comp_id LIKE '%".$_REQUEST['compromiso']."'":"").
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										" WHERE s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											)":"").
						") AS s ON (pc.docg_id = s.sopg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica
					UNION
					SELECT 
						p.paga_docu_id AS pagado,
						ptr.trans_id AS codigo, 
						s.comp_id AS comp_id, 
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						--TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha,
 						TO_CHAR(p.fecha_anulacion, 'DD/MM/YYYY') AS fecha, 
						pd.part_id AS partida, 
						pd.padt_monto*-1 AS monto,
						p.fecha_anulacion AS fecha_date
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id=15 ". 
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND SUBSTRING(TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') FROM 4 for 7) <> SUBSTRING(TO_CHAR(p.fecha_anulacion, 'DD/MM/YYYY') FROM 4 for 7) AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaFin."'":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pago_transferencia ptr
								WHERE 
									(ptr.esta_id=15 OR ptr.esta_id=7)
						) AS ptr ON (p.paga_docu_id = ptr.trans_id)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_sol_pago s ".
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" WHERE s.comp_id LIKE '%".$_REQUEST['compromiso']."' ":"").
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										" WHERE s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											) ":"").
						") AS s ON (ptr.docg_id = s.sopg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica 
					UNION 
					SELECT 
						p.paga_docu_id AS pagado,
						pc.pgch_id AS codigo, 
						s.comp_id AS comp_id, 
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
						pd.part_id AS partida, 
						pd.padt_monto AS monto,
						p.paga_fecha AS fecha_date
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id=15 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND SUBSTRING(TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') FROM 4 for 7) <> SUBSTRING(TO_CHAR(p.fecha_anulacion, 'DD/MM/YYYY') FROM 4 for 7) AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaFin."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')>'".$fechaFin."'":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pago_cheque pc
								WHERE 
									(pc.esta_id=15 OR pc.esta_id=7)
						) AS pc ON (p.paga_docu_id = pc.pgch_id)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_sol_pago s ".
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" WHERE s.comp_id LIKE '%".$_REQUEST['compromiso']."'":"").
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										" WHERE s.comp_id IN 
											(
											SELECT comp_id 
											FROM sai_comp_traza 
											WHERE 
												esta_id<>2 AND 
												pcta_id LIKE '%".$_REQUEST['pcta']."%'
											)":"").
						") AS s ON (pc.docg_id = s.sopg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica 
					UNION
					SELECT 
						p.paga_docu_id AS pagado,
						ptr.trans_id AS codigo, 
						s.comp_id AS comp_id, 
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
						pd.part_id AS partida, 
						pd.padt_monto AS monto,
						p.paga_fecha AS fecha_date
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id=15 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND SUBSTRING(TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') FROM 4 for 7) <> SUBSTRING(TO_CHAR(p.fecha_anulacion, 'DD/MM/YYYY') FROM 4 for 7) AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaFin."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')>'".$fechaFin."'":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pago_transferencia ptr
								WHERE 
									(ptr.esta_id=15 OR ptr.esta_id=7)
						) AS ptr ON (p.paga_docu_id = ptr.trans_id) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_sol_pago s ".
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" WHERE s.comp_id LIKE '%".$_REQUEST['compromiso']."'":"").
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										" WHERE s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											)":"").
						") AS s ON (ptr.docg_id = s.sopg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica 
					ORDER BY 
						fecha_date,
						centro,
						partida";//centro, partida
	} else {
		
		$sql_pag2="	SELECT 
						p.paga_docu_id AS pagado,
						s.sopg_id AS codigo, 
						s.comp_id AS comp_id, 
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
						pd.part_id AS partida, 
						pd.padt_monto AS monto,
						p.paga_fecha AS fecha_date
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id<>15 AND p.esta_id<>2 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").
						") AS p 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pago_cheque pc
						) AS pc ON (p.paga_docu_id = pc.pgch_id)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_sol_pago s 
								WHERE
									s.esta_id<>15 ".
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" AND s.comp_id LIKE '%".$_REQUEST['compromiso']."'":"").
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										" AND s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											)":"").
						") AS s ON (pc.docg_id = s.sopg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica
					UNION 
					SELECT 
						p.paga_docu_id AS pagado,
						ptr.trans_id AS codigo, 
						s.comp_id AS comp_id, 
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
						pd.part_id AS partida, 
						pd.padt_monto AS monto,
						p.paga_fecha AS fecha_date
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id<>15 AND p.esta_id<>2 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."','YYYY-MM-DD') AND TO_DATE('".$fechaFin."','YYYY-MM-DD') ":"").
						") AS p 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pago_transferencia ptr
						) AS ptr ON (p.paga_docu_id = ptr.trans_id)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_sol_pago s 
								WHERE 
									s.esta_id<>15 ".
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" AND s.comp_id LIKE '%".$_REQUEST['compromiso']."'":"").
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										" AND s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											)":"").
						") AS s ON (ptr.docg_id = s.sopg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa					
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica 
					UNION
					SELECT 
						p.paga_docu_id AS pagado,
						cdi.comp_id AS codigo, 
						codi.nro_compromiso AS comp_id,
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						dg.numero_reserva AS reserva, 
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
						pd.part_id AS partida, 
						pd.padt_monto AS monto,
						p.paga_fecha AS fecha_date 
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id<>15 AND p.esta_id<>2 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").
						") AS p 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_comp_diario cdi ".
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
									" WHERE (
										cdi.comp_doc_id IN 
											(
												SELECT sopg_id 
												FROM sai_sol_pago 
												WHERE comp_id IN 
													(
														SELECT comp_id 
														FROM sai_comp_traza 
														WHERE pcta_id LIKE '%".$_REQUEST['pcta']."%'
													)
											) OR 
										cdi.comp_doc_id IN 
											(
												SELECT comp_id 
												FROM sai_comp 
												WHERE pcta_id = '%".$_REQUEST['pcta']."%'
											)
										)":"").
						") AS cdi ON (cdi.comp_id = p.paga_docu_id)  
						INNER JOIN 
							(
								SELECT * 
								FROM sai_codi codi ".
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" WHERE codi.nro_compromiso LIKE '%".$_REQUEST['compromiso']."' ":"").
						") AS codi ON (cdi.comp_id = codi.comp_id)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_doc_genera dg
								WHERE 
									dg.esta_id<>15
						) AS dg ON (p.paga_docu_id = dg.docg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa	
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica ".
						(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?
						" AND (
							cdi.comp_doc_id IN 
								(
									SELECT sopg_id 
									FROM sai_sol_pago 
									WHERE comp_id LIKE '%".$_REQUEST['compromiso']."'
								) OR 
								codi.nro_compromiso LIKE '%".$_REQUEST['compromiso']."'
							)":"").
					"ORDER BY 
						fecha_date,
						centro, 
						partida";//centro, partida
	}
		
	$sql_pag = "SELECT * FROM (".$sql_pag2. ") AS b ORDER BY pagado,partida";
	$resultado_set_most_pag=pg_query($conexion,$sql_pag) or die("Error al consultar la descripcion del pagado");

	$ano1=substr($fechaInicio,0,4);
	$mes1=substr($fechaInicio,5,2);
	$dia1=substr($fechaInicio,8,2);

	$ano2=substr($fechaFin,0,4);
	$mes2=substr($fechaFin,5,2);
	$dia2=substr($fechaFin,8,2);
	
      $mprXls[0][0] = 'APARTADO';
      $mprXls[1][0] = 'Codigo';
      $mprXls[1][1] = 'Pcta Asociado';
      $mprXls[1][2] = 'Proy/Acc';
      $mprXls[1][3] = 'Fecha';
      $mprXls[1][4] = 'Partida';
      $mprXls[1][5] = 'Monto';
      
      $mprXls[0][6] = 'COMPROMISO';
	  $mprXls[1][6] = 'Codigo';
	  $mprXls[1][7] = 'Pcta Asociado';
      $mprXls[1][8] = 'Proy/Acc';
      $mprXls[1][9] = 'Fecha';
      $mprXls[1][10] = 'Partida';
      $mprXls[1][11] = 'Monto';
      
      $mprXls[0][12] = 'COMPROMISO AISLADO';
      $mprXls[1][12] = 'Codigo';
      $mprXls[1][13] = 'Proy/Acc';
      $mprXls[1][14] = 'Fecha';
      $mprXls[1][15] = 'Partida';
      $mprXls[1][16] = 'Monto';
      
      $mprXls[0][17] = 'CAUSADO';
	  $mprXls[1][17] = 'Codigo';
	  $mprXls[1][18] = 'Comp';
      $mprXls[1][19] = 'Proy/Acc';
      $mprXls[1][20] = 'Nro. Reserva';
      $mprXls[1][21] = 'Fecha';
      $mprXls[1][22] = 'Partida';
      $mprXls[1][23] = 'Monto';
      
      $mprXls[0][24] = 'PAGADO';
	  $mprXls[1][24] = 'Codigo';
	  $mprXls[1][25] = 'Comp';
      $mprXls[1][26] = 'Proy/Acc';
      $mprXls[1][27] = 'Nro. Reserva';
      $mprXls[1][28] = 'Fecha';
      $mprXls[1][29] = 'Partida';
      $mprXls[1][30] = 'Monto';
      
    $i = 2;

          while ($rowapart=pg_fetch_array($resultado_set_most_apart)) {
						$suma_apart_monto+=$rowapart['monto'];
						
						
						
						
						    $mprXls[$i][0] = $rowapart['codigo'];
						    $mprXls[$i][1] = $rowapart['pcta_asociado'] != '' ?  $rowapart['pcta_asociado'] : '-';
						    $mprXls[$i][2] = $rowapart['centro'];
						    $mprXls[$i][3] =  $rowapart['fecha'];
						    $mprXls[$i][4] = $rowapart['partida'];
						    $mprXls[$i][5] = number_format($rowapart['monto'],2,',','.');
						
				$i++; 
          }
				
					
					$valor=0;
					$i=0;
					$monto=0;
					$partida=	'';
					$comp= '';

					
		  			  $i = 2;
		  			  
					while ($rowacomp=pg_fetch_array($resultado_set_most_comp)) {

						    $mprXls[$i][6] = $rowacomp['codigo'];
						    $mprXls[$i][7] = $rowacomp['pcta_id'];
				            $mprXls[$i][8] = $rowacomp['centro'];
						    $mprXls[$i][9] =  $rowacomp['fecha'];
						    $mprXls[$i][10] = $rowacomp['partida'];
						    $mprXls[$i][11] = number_format($rowacomp['monto'],2,',','.');
						    


						$suma_comp_monto+=$rowacomp['monto'];
					$i++; }
					
					$valor=0;
					$i=0;
					$monto=0;
					$partida=	'';
					$comp= '';
					
	
    $i = 2;		
						

					while ($rowacomp=pg_fetch_array($resultado_set_most_comp_ais)) {
						if ($i==0) {
							$valor= $rowacomp['monto']; $monto=$rowacomp['monto'];
						}
						if ($rowacomp['partida']<>$partida || $comp<>$rowacomp['codigo']) {
							$partida=	$rowacomp['partida'];
							$comp=$rowacomp['codigo'];
							$valor= $rowacomp['monto']; $monto=$rowacomp['monto'];
						} else {

							$monto=$rowacomp['monto']-$valor;
							$valor=$rowacomp['monto'];
						}
						// $suma_comp_monto+=$monto;
						if ($monto<>0) {
	
							$mprXls[$i][12] = $rowacomp['codigo'];
						    $mprXls[$i][13] = $rowacomp['centro'];
						    $mprXls[$i][14] =  $rowacomp['fecha'];
						    $mprXls[$i][15] = $rowacomp['partida'];
						    $mprXls[$i][16] = number_format($rowacomp['monto'],2,',','.');
							
					        $i++;
					 	$suma_comp_monto_ais+=$rowacomp['monto'];
						}
						
					}
					
					$compromiso="";
					
                    $i = 2;
                    
						   
					while ($rowacaus=pg_fetch_array($resultado_set_most_caus)) {
				 		$suma_caus_monto+=$rowacaus['monto'];
				 		
				 		      
				 		
				 		    $mprXls[$i][17] = $rowacaus['codigo'];
				 		    $mprXls[$i][18] = $rowacaus['comp_id'];
						    $mprXls[$i][19] = $rowacaus['centro'];
						    $mprXls[$i][20] = $rowacaus['reserva'];
						    $mprXls[$i][21] = $rowacaus['fecha'];
						    $mprXls[$i][22] = $rowacaus['partida'];
						    $mprXls[$i][23] = number_format($rowacaus['monto'],2,',','.');
						    
		
					$i++; }
				
				
                    $i = 2;
                  
				 		
				 		
					
					while ($rowpag=pg_fetch_array($resultado_set_most_pag)) {
				 		$suma_pag_monto+=$rowpag['monto'];
				 		
				 		    $mprXls[$i][24] = $rowpag['codigo'];
				 		    $mprXls[$i][25] = $rowpag['comp_id'];
						    $mprXls[$i][26] = $rowpag['centro'];
						    $mprXls[$i][27] = $rowpag['reserva'];
						    $mprXls[$i][28] = $rowpag['fecha'];
						    $mprXls[$i][29] = $rowpag['partida'];
						    $mprXls[$i][30] = number_format($rowpag['monto'],2,',','.');
				
					$i++;
					}
					
					
          
          
				
}

 

pg_close($conexion);




if($mprXls){

	$filas = count($mprXls);
	$columnas = 29;

	for($i = 0; $i < $filas; $i++ ){

		for($j = 0; $j < $columnas; $j++ ){
				
			if(!$mprXls[$i][$j]){

				$mprXls[$i][$j] = '';

			}
			
			$contenido[$i][$j] = $mprXls[$i][$j];
				
		}

	}
	
}

/*


echo "<pre>";
echo print_r($contenido);
echo "</pre>";

*/


   

createExcel("momentoPresupuestario.xls",$contenido);
?>