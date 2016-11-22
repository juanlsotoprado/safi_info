<?php
include_once(SAFI_ENTIDADES_PATH . '/compromiso.php');
include_once(SAFI_ENTIDADES_PATH . '/compromiso.php');
include_once(SAFI_LIB_PATH . '/general.php');

class SafiModeloCompromiso {


	/*public static function ReporteActividad($parametros) {
		$actividades = null;
		$estadoTransito = 10;
		$estadoAnulado = 15;
		$estadoInactivo = 2;
		try  {
		$query = "
		SELECT
		s.nombre_actividad,
		s.nombre_evento,
		TO_CHAR(s.fecha_inicio,'DD/MM/YYYY') AS fecha_inicio_str,
		TO_CHAR(s.fecha_fin,'DD/MM/YYYY') AS fecha_fin_str,
		s.centro_costo,
		s.centro_gestor,
		SUM(s.monto_solicitado) AS monto_solicitado,
		SUM(s.monto_causado) AS monto_causado,
		SUM(s.monto_pagado) AS monto_pagado,
		ARRAY
		(SELECT
		COALESCE(sc_i.comp_id,'')||'||'||COALESCE(sc_i.comp_observacion,'')||'||'||COALESCE(sev_i.nombre,'')
		FROM
		sai_comp sc_i
		INNER JOIN
		(
		SELECT
		scit.comp_id,
		MAX(scit.comp_fecha) AS comp_fecha
		FROM sai_comp_imputa_traza scit
		WHERE
		scit.comp_id LIKE '%".substr($_SESSION['an_o_presupuesto'],-2)."' AND
		scit.comp_tipo_impu = s.tipo_imputacion AND
		scit.comp_acc_pp = s.id_proyecto_accion AND
		scit.comp_acc_esp = s.id_accion_especifica
		GROUP BY
		scit.comp_id
		) AS scit_i ON (sc_i.comp_id = scit_i.comp_id)
		LEFT OUTER JOIN safi_edos_venezuela sev_i ON (sc_i.localidad = sev_i.id)
		WHERE
		sc_i.comp_id LIKE '%".substr($_SESSION['an_o_presupuesto'],-2)."' AND
		sc_i.esta_id = ".$estadoTransito." AND
		sc_i.id_actividad = s.id_actividad AND
		sc_i.id_evento = s.id_evento AND
		sc_i.fecha_inicio IS NOT NULL AND sc_i.fecha_fin IS NOT NULL AND
		sc_i.fecha_inicio = s.fecha_inicio AND
		sc_i.fecha_fin = s.fecha_fin
		ORDER BY
		sc_i.comp_id
		) AS compromisos
		FROM
		(SELECT
		sta.id AS id_actividad,
		sta.nombre AS nombre_actividad,
		ste.id AS id_evento,
		ste.nombre AS nombre_evento,
		sc.localidad,
		sc.fecha_inicio,
		sc.fecha_fin,
		spa.tipo_imputacion,
		spa.id_proyecto_accion,
		spa.id_accion_especifica,
		spa.centro_costo,
		spa.centro_gestor,
		sci.monto_solicitado,
		sca.monto_causado,
		spag.monto_pagado
		FROM
		sai_comp sc
		INNER JOIN sai_tipo_actividad sta ON (sc.id_actividad = sta.id)
		INNER JOIN sai_tipo_evento ste ON (sc.id_evento = ste.id)
		INNER JOIN
		(
		SELECT
		scit.comp_id AS comp_id,
		scit.comp_tipo_impu AS tipo_imputacion,
		scit.comp_acc_pp AS id_proyecto_accion,
		scit.comp_acc_esp AS id_accion_especifica,
		SUM(scit.comp_monto) AS monto_solicitado
		FROM
		sai_comp_imputa_traza scit
		INNER JOIN
		(
		SELECT
		scit.comp_id,
		MAX(scit.comp_fecha) AS comp_fecha
		FROM sai_comp_imputa_traza scit
		WHERE
		scit.comp_id LIKE '%".substr($_SESSION['an_o_presupuesto'],-2)."'
		GROUP BY
		scit.comp_id
		) AS s ON (scit.comp_fecha = s.comp_fecha)
		WHERE
		scit.comp_id LIKE '%".substr($_SESSION['an_o_presupuesto'],-2)."'
		GROUP BY
		scit.comp_id,
		scit.comp_tipo_impu,
		scit.comp_acc_pp,
		scit.comp_acc_esp
		) AS sci ON (sc.comp_id = sci.comp_id)
		INNER JOIN
		(
		SELECT
		spae.proy_id AS id_proyecto_accion,
		spae.paes_id AS id_accion_especifica,
		'1'::BIT AS tipo_imputacion,
		spae.centro_costo,
		spae.centro_gestor
		FROM sai_proyecto sp, sai_proy_a_esp spae
		WHERE
		sp.pre_anno = spae.pres_anno AND
		sp.proy_id = spae.proy_id AND
		sp.pre_anno = ".$_SESSION['an_o_presupuesto']."
		UNION
		SELECT
		sae.acce_id as id_proyecto_accion,
		sae.aces_id AS id_accion_especifica,
		'0'::BIT as tipo_imputacion,
		sae.centro_costo,
		sae.centro_gestor
		FROM sai_ac_central sac, sai_acce_esp sae
		WHERE
		sac.pres_anno = sae.pres_anno AND
		sac.acce_id = sae.acce_id AND
		sac.pres_anno = ".$_SESSION['an_o_presupuesto']."
		) AS spa ON (sci.id_proyecto_accion = spa.id_proyecto_accion AND sci.id_accion_especifica = spa.id_accion_especifica AND sci.tipo_imputacion = spa.tipo_imputacion)
		LEFT OUTER JOIN
		(
		SELECT SUM(s.monto_causado) AS monto_causado,s.comp_id
		FROM
		(
		SELECT
		ssp.comp_id,
		SUM(scd.cadt_monto) AS monto_causado
		FROM
		sai_sol_pago ssp
		INNER JOIN sai_causado sc ON (ssp.sopg_id = sc.caus_docu_id AND sc.pres_anno = ".$_SESSION['an_o_presupuesto']." AND sc.esta_id <> ".$estadoAnulado." AND sc.esta_id <> ".$estadoInactivo.")
		INNER JOIN sai_causad_det scd ON (sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno AND scd.cadt_abono = '1' AND scd.part_id NOT LIKE '4.11%')
		WHERE ssp.esta_id <> ".$estadoAnulado."
		GROUP BY ssp.comp_id
		UNION
		SELECT
		sco.nro_compromiso AS comp_id,
		SUM(scd.cadt_monto) AS monto_causado
		FROM
		sai_codi sco
		INNER JOIN sai_causado sc ON (sco.comp_id = sc.caus_docu_id AND sc.pres_anno = ".$_SESSION['an_o_presupuesto']." AND sc.esta_id <> ".$estadoAnulado." AND sc.esta_id <> ".$estadoInactivo.")
		INNER JOIN sai_causad_det scd ON (sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno AND scd.cadt_abono = '1' AND scd.part_id NOT LIKE '4.11%')
		WHERE sco.esta_id <> ".$estadoAnulado."
		GROUP BY sco.nro_compromiso
		) AS s
		GROUP BY s.comp_id
		) AS sca ON (sc.comp_id = sca.comp_id)
		LEFT OUTER JOIN
		(
		SELECT SUM(s.monto_pagado) AS monto_pagado,s.comp_id
		FROM
		(
		SELECT
		ssp.comp_id,
		SUM(spd.padt_monto) AS monto_pagado
		FROM
		sai_sol_pago ssp
		LEFT OUTER JOIN sai_pago_cheque spc on (spc.docg_id = ssp.sopg_id AND spc.esta_id <> ".$estadoAnulado.")
		LEFT OUTER JOIN sai_pago_transferencia spt on (spt.docg_id = ssp.sopg_id AND spt.esta_id <> ".$estadoAnulado.")
		INNER JOIN sai_pagado sp ON ((sp.paga_docu_id = spc.pgch_id OR sp.paga_docu_id = spt.trans_id) AND sp.pres_anno = ".$_SESSION['an_o_presupuesto']." AND sp.esta_id <> ".$estadoAnulado." AND sp.esta_id <> ".$estadoInactivo.")
		INNER JOIN sai_pagado_dt spd ON (sp.paga_id = spd.paga_id AND sp.pres_anno = spd.pres_anno AND spd.part_id NOT LIKE '4.11%')
		WHERE ssp.esta_id <> ".$estadoAnulado."
		GROUP BY ssp.comp_id
		UNION
		SELECT
		sco.nro_compromiso AS comp_id,
		COALESCE(SUM(spd.padt_monto),0) AS monto_pagado
		FROM
		sai_codi sco
		INNER JOIN sai_pagado sp ON (sco.comp_id = sp.paga_docu_id AND sp.pres_anno = ".$_SESSION['an_o_presupuesto']." AND sp.esta_id <> ".$estadoAnulado." AND sp.esta_id <> ".$estadoInactivo.")
		INNER JOIN sai_pagado_dt spd ON (sp.paga_id = spd.paga_id AND sp.pres_anno = spd.pres_anno AND spd.part_id NOT LIKE '4.11%')
		WHERE sco.esta_id <> ".$estadoAnulado."
		GROUP BY sco.nro_compromiso
		) AS s
		GROUP BY s.comp_id
		) AS spag ON (sc.comp_id = spag.comp_id)
		WHERE
		sc.comp_id LIKE '%".substr($_SESSION['an_o_presupuesto'],-2)."' AND
		sc.esta_id = ".$estadoTransito." AND
		sc.fecha_inicio IS NOT NULL AND sc.fecha_fin IS NOT NULL ";
		if(isset($parametros['idTipoActividadCompromiso']) && trim($parametros['idTipoActividadCompromiso']) != ''){
		$query .=	"	AND sc.id_actividad = ".$parametros['idTipoActividadCompromiso']." ";
		}
		if(isset($parametros['idTipoEvento']) && trim($parametros['idTipoEvento']) != ''){
		$query .=	"	AND sc.id_evento = ".$parametros['idTipoEvento']." ";
		}
		if(isset($parametros['idEstado']) && trim($parametros['idEstado']) != ''){
		$query .=	"	AND sc.localidad = ".$parametros['idEstado']." ";
		}
		if(isset($parametros['centroGestor']) && trim($parametros['centroGestor']) != ''){
		$query .=	"	AND spa.centro_gestor = '".$parametros['centroGestor']."' ";
		}
		if(isset($parametros['centroCosto']) && trim($parametros['centroCosto']) != ''){
		$query .=	"	AND spa.centro_costo = '".$parametros['centroCosto']."' ";
		}
		if(isset($parametros['fechaInicio']) && trim($parametros['fechaInicio']) != ''){
		$query .=	"	AND sc.fecha_inicio >= TO_DATE('".$parametros['fechaInicio']."','DD/MM/YYYY') ";
		}
		if(isset($parametros['fechaFin']) && trim($parametros['fechaFin']) != ''){
		$query .=	"	AND sc.fecha_fin <= TO_DATE('".$parametros['fechaFin']."','DD/MM/YYYY') ";
		}
		$query	.= "	) AS s
		GROUP BY
		s.id_actividad,
		s.nombre_actividad,
		s.id_evento,
		s.nombre_evento,
		s.fecha_inicio,
		s.fecha_fin,
		s.tipo_imputacion,
		s.id_proyecto_accion,
		s.id_accion_especifica,
		s.centro_costo,
		s.centro_gestor
		ORDER BY s.fecha_inicio, s.fecha_fin, s.centro_gestor, s.centro_costo, s.nombre_actividad, s.nombre_evento ";
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
		throw new Exception("Error al obtener los compromisos. Detalles: ".
		utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

		$actividades = array();
		while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
		$actividad = array();
		if ( $row['nombre_actividad'] ) {
		$actividad['nombre_actividad'] = $row['nombre_actividad'];
		}
		if ( $row['nombre_evento'] ) {
		$actividad['nombre_evento'] = $row['nombre_evento'];
		}
		if ( $row['fecha_inicio_str'] ) {
		$actividad['fecha_inicio'] = $row['fecha_inicio_str'];
		}
		if ( $row['fecha_fin_str'] ) {
		$actividad['fecha_fin'] = $row['fecha_fin_str'];
		}
		if ( $row['centro_costo'] ) {
		$actividad['centro_costo'] = $row['centro_costo'];
		}
		if ( $row['centro_gestor'] ) {
		$actividad['centro_gestor'] = $row['centro_gestor'];
		}
		if ( $row['monto_solicitado'] ) {
		$actividad['monto_solicitado'] = $row['monto_solicitado'];
		}
		if ( $row['monto_causado'] ) {
		$actividad['monto_causado'] = $row['monto_causado'];
		}
		if ( $row['monto_pagado'] ) {
		$actividad['monto_pagado'] = $row['monto_pagado'];
		}
		if ( isset($row['compromisos']) ) {
		$actividad["compromisos"] = array();
		$compromisosActividad = pg_array_parse($row['compromisos']);
		$i = 0;
		while ( $i < sizeof($compromisosActividad) ) {
		$actividad["compromisos"][] = array();
		$compromiso = explode("||", $compromisosActividad[$i]);
		if ( isset($compromiso[0]) ) {
		$actividad["compromisos"][sizeof($actividad["compromisos"])-1]['id'] = $compromiso[0];
		}
		if ( isset($compromiso[1]) ) {
		$PARTICIPANTES = "*Participantes:";
		if ( strpos($compromiso[1], $PARTICIPANTES) >= 0 ) {
		$actividad["compromisos"]
		[sizeof($actividad[""])-1]
		['participantes'] = trim(substr(
		$compromiso[1],
		strpos($compromiso[1], $PARTICIPANTES) + strlen($PARTICIPANTES),
		strpos(substr($compromiso[1], strpos($compromiso[1], $PARTICIPANTES) + strlen($PARTICIPANTES)), "*")
		)
		);
		}
		}
		if ( isset($compromiso[2]) ) {
		$actividad["compromisos"][sizeof($actividad["compromisos"])-1]['nombre_estado'] = $compromiso[2];
		}
		$i++;
		}
		}
		$actividades[] = $actividad;
		}

		}catch(Exception $e){
		error_log($e, 0);
		}

		return $actividades;
		}*/

	public static function ReporteActividad($parametros) {
		$actividades = null;
		$estadoTransito = 10;
		$estadoAnulado = 15;
		$estadoInactivo = 2;
		try  {
			$query = "
			            SELECT
			                s.nombre_actividad,
			                s.nombre_evento,
			                TO_CHAR(s.fecha_inicio,'DD/MM/YYYY') AS fecha_inicio_str,
				            TO_CHAR(s.fecha_fin,'DD/MM/YYYY') AS fecha_fin_str,
			                s.centro_costo,
			                s.centro_gestor,
							s.nombre_estado,
			                SUM(s.monto_solicitado) AS monto_solicitado,
			                SUM(s.monto_causado) AS monto_causado,
			                SUM(s.monto_pagado) AS monto_pagado,
	                        ARRAY
			                	(SELECT
			                		COALESCE(sc_i.comp_id,'')||'||'||COALESCE(sc_i.comp_observacion,'')
			                	FROM
			                		sai_comp sc_i
			                		INNER JOIN
		                				(
		                					SELECT
			                					scit.comp_id,
			                					MAX(scit.comp_fecha) AS comp_fecha
			                				FROM sai_comp_imputa_traza scit
			                				WHERE
			                					scit.comp_id LIKE '%".substr($_SESSION['an_o_presupuesto'],-2)."' AND
			                					scit.comp_tipo_impu = s.tipo_imputacion AND
								            	scit.comp_acc_pp = s.id_proyecto_accion AND
								            	scit.comp_acc_esp = s.id_accion_especifica
			                				GROUP BY
			                					scit.comp_id
		                				) AS scit_i ON (sc_i.comp_id = scit_i.comp_id)
			                	WHERE
			                		sc_i.comp_id LIKE '%".substr($_SESSION['an_o_presupuesto'],-2)."' AND
			                		sc_i.esta_id = ".$estadoTransito." AND
			            			sc_i.id_actividad = s.id_actividad AND
					            	sc_i.id_evento = s.id_evento AND
					            	sc_i.fecha_inicio IS NOT NULL AND sc_i.fecha_fin IS NOT NULL AND
					            	sc_i.fecha_inicio = s.fecha_inicio AND
					            	sc_i.fecha_fin = s.fecha_fin
					            ORDER BY
					            	 sc_i.comp_id
		                	) AS compromisos
			       		FROM
	        				(SELECT
				                sta.id AS id_actividad,
				                sta.nombre AS nombre_actividad,
				                ste.id AS id_evento,
				                ste.nombre AS nombre_evento,
				                sc.localidad,
				                sc.fecha_inicio,
				                sc.fecha_fin,
				                spa.tipo_imputacion,
				                spa.id_proyecto_accion,
				                spa.id_accion_especifica,
				                spa.centro_costo,
				                spa.centro_gestor,
			                	sev.nombre AS nombre_estado,
				                sci.monto_solicitado,
				                sca.monto_causado,
				                spag.monto_pagado
				            FROM
				                sai_comp sc
				                INNER JOIN sai_tipo_actividad sta ON (sc.id_actividad = sta.id)
				                INNER JOIN sai_tipo_evento ste ON (sc.id_evento = ste.id)
				                INNER JOIN
				                	(
				                		SELECT
				                			scit.comp_id AS comp_id,
				                			scit.comp_tipo_impu AS tipo_imputacion,
				                			scit.comp_acc_pp AS id_proyecto_accion,
				                			scit.comp_acc_esp AS id_accion_especifica,
				                			SUM(scit.comp_monto) AS monto_solicitado
				                		FROM
				                			sai_comp_imputa_traza scit
				                			INNER JOIN
				                				(
				                					SELECT
					                					scit.comp_id,
					                					MAX(scit.comp_fecha) AS comp_fecha
					                				FROM sai_comp_imputa_traza scit
					                				WHERE
					                					scit.comp_id LIKE '%".substr($_SESSION['an_o_presupuesto'],-2)."'
					                				GROUP BY
					                					scit.comp_id
				                				) AS s ON (scit.comp_fecha = s.comp_fecha)
				                		WHERE
				                			scit.comp_id LIKE '%".substr($_SESSION['an_o_presupuesto'],-2)."'
				                		GROUP BY
				                			scit.comp_id,
				                			scit.comp_tipo_impu,
				                			scit.comp_acc_pp,
				                			scit.comp_acc_esp
				                	) AS sci ON (sc.comp_id = sci.comp_id)
				                INNER JOIN
				                	(
					                	SELECT
											spae.proy_id AS id_proyecto_accion,
											spae.paes_id AS id_accion_especifica,
											'1'::BIT AS tipo_imputacion,
											spae.centro_costo,
											spae.centro_gestor
										FROM sai_proyecto sp, sai_proy_a_esp spae
										WHERE
											sp.pre_anno = spae.pres_anno AND
											sp.proy_id = spae.proy_id AND
											sp.pre_anno = ".$_SESSION['an_o_presupuesto']."
										UNION
										SELECT
											sae.acce_id as id_proyecto_accion,
											sae.aces_id AS id_accion_especifica,
											'0'::BIT as tipo_imputacion,
											sae.centro_costo,
											sae.centro_gestor
										FROM sai_ac_central sac, sai_acce_esp sae
										WHERE
											sac.pres_anno = sae.pres_anno AND
											sac.acce_id = sae.acce_id AND
											sac.pres_anno = ".$_SESSION['an_o_presupuesto']."
				                	) AS spa ON (sci.id_proyecto_accion = spa.id_proyecto_accion AND sci.id_accion_especifica = spa.id_accion_especifica AND sci.tipo_imputacion = spa.tipo_imputacion)
				                LEFT OUTER JOIN safi_edos_venezuela sev ON (sc.localidad = sev.id)
								LEFT OUTER JOIN
				                	(
					                	SELECT SUM(s.monto_causado) AS monto_causado,s.comp_id
					                	FROM
					                		(
					                		SELECT
					                			ssp.comp_id,
					                			SUM(scd.cadt_monto) AS monto_causado
					                		FROM
					                			sai_sol_pago ssp
					                			INNER JOIN sai_causado sc ON (ssp.sopg_id = sc.caus_docu_id AND sc.pres_anno = ".$_SESSION['an_o_presupuesto']." AND sc.esta_id <> ".$estadoAnulado." AND sc.esta_id <> ".$estadoInactivo.")
					                			INNER JOIN sai_causad_det scd ON (sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno AND scd.cadt_abono = '1' AND scd.part_id NOT LIKE '4.11%')
					                		WHERE ssp.esta_id <> ".$estadoAnulado."
					                		GROUP BY ssp.comp_id
					                		UNION
					                		SELECT
					                			sco.nro_compromiso AS comp_id,
					                			SUM(scd.cadt_monto) AS monto_causado
					                		FROM
					                			sai_codi sco
					                			INNER JOIN sai_causado sc ON (sco.comp_id = sc.caus_docu_id AND sc.pres_anno = ".$_SESSION['an_o_presupuesto']." AND sc.esta_id <> ".$estadoAnulado." AND sc.esta_id <> ".$estadoInactivo.")
					                			INNER JOIN sai_causad_det scd ON (sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno AND scd.cadt_abono = '1' AND scd.part_id NOT LIKE '4.11%')
					                		WHERE sco.esta_id <> ".$estadoAnulado."
					                		GROUP BY sco.nro_compromiso
					                		) AS s
					                	GROUP BY s.comp_id
				                	) AS sca ON (sc.comp_id = sca.comp_id)
				                LEFT OUTER JOIN
				                	(
					                	SELECT SUM(s.monto_pagado) AS monto_pagado,s.comp_id
					                	FROM
					                		(
					                		SELECT
					                			ssp.comp_id,
					                			SUM(spd.padt_monto) AS monto_pagado
					                		FROM
					                			sai_sol_pago ssp
					                			LEFT OUTER JOIN sai_pago_cheque spc on (spc.docg_id = ssp.sopg_id AND spc.esta_id <> ".$estadoAnulado.")
		      									LEFT OUTER JOIN sai_pago_transferencia spt on (spt.docg_id = ssp.sopg_id AND spt.esta_id <> ".$estadoAnulado.")
					                			INNER JOIN sai_pagado sp ON ((sp.paga_docu_id = spc.pgch_id OR sp.paga_docu_id = spt.trans_id) AND sp.pres_anno = ".$_SESSION['an_o_presupuesto']." AND sp.esta_id <> ".$estadoAnulado." AND sp.esta_id <> ".$estadoInactivo.")
					                			INNER JOIN sai_pagado_dt spd ON (sp.paga_id = spd.paga_id AND sp.pres_anno = spd.pres_anno AND spd.part_id NOT LIKE '4.11%')
					                		WHERE ssp.esta_id <> ".$estadoAnulado."
					                		GROUP BY ssp.comp_id
					                		UNION
					                		SELECT
					                			sco.nro_compromiso AS comp_id,
					                			COALESCE(SUM(spd.padt_monto),0) AS monto_pagado
					                		FROM
					                			sai_codi sco
					                			INNER JOIN sai_pagado sp ON (sco.comp_id = sp.paga_docu_id AND sp.pres_anno = ".$_SESSION['an_o_presupuesto']." AND sp.esta_id <> ".$estadoAnulado." AND sp.esta_id <> ".$estadoInactivo.")
					                			INNER JOIN sai_pagado_dt spd ON (sp.paga_id = spd.paga_id AND sp.pres_anno = spd.pres_anno AND spd.part_id NOT LIKE '4.11%')
					                		WHERE sco.esta_id <> ".$estadoAnulado."
					                		GROUP BY sco.nro_compromiso
					                		) AS s
					                	GROUP BY s.comp_id
				                	) AS spag ON (sc.comp_id = spag.comp_id)
				            WHERE
				                sc.comp_id LIKE '%".substr($_SESSION['an_o_presupuesto'],-2)."' AND
				                sc.esta_id = ".$estadoTransito." AND
				                sc.fecha_inicio IS NOT NULL AND sc.fecha_fin IS NOT NULL ";
			if(isset($parametros['idTipoActividadCompromiso']) && trim($parametros['idTipoActividadCompromiso']) != ''){
				$query .=	"	AND sc.id_actividad = ".$parametros['idTipoActividadCompromiso']." ";
			}
			if(isset($parametros['idTipoEvento']) && trim($parametros['idTipoEvento']) != ''){
				$query .=	"	AND sc.id_evento = ".$parametros['idTipoEvento']." ";
			}
			if(isset($parametros['idEstado']) && trim($parametros['idEstado']) != ''){
				$query .=	"	AND sc.localidad = ".$parametros['idEstado']." ";
			}
			if(isset($parametros['centroGestor']) && trim($parametros['centroGestor']) != ''){
				$query .=	"	AND spa.centro_gestor = '".$parametros['centroGestor']."' ";
			}
			if(isset($parametros['centroCosto']) && trim($parametros['centroCosto']) != ''){
				$query .=	"	AND spa.centro_costo = '".$parametros['centroCosto']."' ";
			}
			if(isset($parametros['fechaInicio']) && trim($parametros['fechaInicio']) != ''){
				$query .=	"	AND sc.fecha_inicio >= TO_DATE('".$parametros['fechaInicio']."','DD/MM/YYYY') ";
			}
			if(isset($parametros['fechaFin']) && trim($parametros['fechaFin']) != ''){
				$query .=	"	AND sc.fecha_fin <= TO_DATE('".$parametros['fechaFin']."','DD/MM/YYYY') ";
			}
			$query	.= "	) AS s
						GROUP BY
							s.id_actividad,
			                s.nombre_actividad,
			                s.id_evento,
			                s.nombre_evento,
			                s.fecha_inicio,
			                s.fecha_fin,
			                s.tipo_imputacion,
			                s.id_proyecto_accion,
			                s.id_accion_especifica,
			                s.centro_costo,
			                s.centro_gestor,
							s.nombre_estado
						HAVING
							SUM(s.monto_solicitado) > 0 AND
			                SUM(s.monto_causado) > 0 AND
			                SUM(s.monto_pagado) > 0
						ORDER BY s.fecha_inicio, s.fecha_fin, s.centro_gestor, s.centro_costo, s.nombre_actividad, s.nombre_evento ";
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener los compromisos. Detalles: ".
			utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			$actividades = array();
			while($row = $GLOBALS['SafiClassDb']->Fetch($result)){

				$actividad = array();
				if ( $row['nombre_actividad'] ) {
					$actividad['nombre_actividad'] = $row['nombre_actividad'];
				}
				if ( $row['nombre_evento'] ) {
					$actividad['nombre_evento'] = $row['nombre_evento'];
				}
				if ( $row['fecha_inicio_str'] ) {
					$actividad['fecha_inicio'] = $row['fecha_inicio_str'];
				}
				if ( $row['fecha_fin_str'] ) {
					$actividad['fecha_fin'] = $row['fecha_fin_str'];
				}
				if ( $row['centro_costo'] ) {
					$actividad['centro_costo'] = $row['centro_costo'];
				}
				if ( $row['centro_gestor'] ) {
					$actividad['centro_gestor'] = $row['centro_gestor'];
				}
				if ( $row['nombre_estado'] ) {
					$actividad['nombre_estado'] = $row['nombre_estado'];
				}
				if ( $row['monto_solicitado'] ) {
					$actividad['monto_solicitado'] = $row['monto_solicitado'];
				}
				if ( $row['monto_causado'] ) {
					$actividad['monto_causado'] = $row['monto_causado'];
				}
				if ( $row['monto_pagado'] ) {
					$actividad['monto_pagado'] = $row['monto_pagado'];
				}
				if ( isset($row['compromisos']) ) {


					$actividad["compromisos"] = array();

					$compromisosActividad = @pg_array_parse($row['compromisos']);


					$i = 0;
					while ( $i < sizeof($compromisosActividad) ) {
						$actividad["compromisos"][] = array();
						$compromiso = explode("||", $compromisosActividad[$i]);
						if ( isset($compromiso[0]) ) {
							$actividad["compromisos"][sizeof($actividad["compromisos"])-1]['id'] = $compromiso[0];
						}
						if ( isset($compromiso[1]) ) {
							$PARTICIPANTES = "*Participantes:";
							if ( strpos($compromiso[1], $PARTICIPANTES) >= 0 ) {
								$actividad["compromisos"]
								[sizeof($actividad["compromisos"])-1]
								['participantes'] = trim(substr(
								$compromiso[1],
								strpos($compromiso[1], $PARTICIPANTES) + strlen($PARTICIPANTES),
								strpos(substr($compromiso[1], strpos($compromiso[1], $PARTICIPANTES) + strlen($PARTICIPANTES)), "*")
								)
								);
							}
						}
						$i++;
					}
				}
				$actividades[] = $actividad;
			}

		}catch(Exception $e){
			error_log($e, 0);
		}

		return $actividades;
	}

	public static function BuscarIdsCompromiso($codigoDocumento, $idDependencia, $numLimit) {
		$ids = null;
		try  {
			if($codigoDocumento == null || trim($codigoDocumento)=='' || trim($idDependencia) == '' || trim($idDependencia) == null)
			throw new Exception("Error al buscar los ids de compromisos. Detalles: El código del documento o la dependencia es nulo o vacío");

			$query = "
	            SELECT
	                comp_id as id
	            FROM
	                sai_comp
	            WHERE
	                comp_id like '%".$codigoDocumento."%' AND
	                comp_gerencia='".$idDependencia."' AND
	                comp_fecha like '".$_SESSION['an_o_presupuesto']."%'  
	            ORDER BY comp_id 
				LIMIT
				".$numLimit."
	        ";
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener los ids de compromisos. Detalles: ".
			utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			$ids = array();

			while($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$ids[] = $row['id'];
			}

		}catch(Exception $e){
			error_log($e, 0);
		}

		return $ids;
	}

	public static function GetCompromisoByIdDocumento($idDocumento)
	{
		$compromiso = null;

		try {

			if($idDocumento == null || ($idDocumento=trim($idDocumento)) == '')
			throw new Exception("Error al obtener el compromiso dado el id de un documento. Detalles: ".
					"el parámetro idDocumento es vacío o nulo");

			$query = "
				SELECT
					 compromiso.comp_id AS comp_id,
					 compromiso.comp_documento AS comp_documento
				FROM
					sai_comp compromiso
				WHERE
					lower(compromiso.comp_documento) = '".$idDocumento."'
			";

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener el compromiso dado el id de un documento. Detalles: ".
			utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$compromiso = self::LlenarCompromiso($row);
			}

		} catch (Exception $e) {
			error_log($e, 0);
		}

		return $compromiso;
	}








	public static  function InsertCompromiso($params){

		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){
					
				$preMsg = "error al insertar compromiso.";

				if($params['fecha']){
					$fecha = explode ('/',$params['fecha']);
					$fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
				}
				$fecha2 = $fecha2 != false? "'".$fecha2."'" : "now()" ;
				  $fecha7  =  $fecha[0].'-'.$fecha[1].'-'.$fecha[2];

				if($params['txt_inicio']){
					$fecha3 = explode ('/',$params['txt_inicio']);
					$fecha4 =  $fecha3[2].'-'.$fecha3[1].'-'.$fecha3[0];
				}
				$fecha4 = $fecha4 != false? "'".$fecha4."'" : "now()" ;


					
				if($params['hid_hasta_itin']){
					$fecha5 = explode ('/',$params['hid_hasta_itin']);
					$fecha6 =  $fecha5[2].'-'.$fecha5[1].'-'.$fecha5[0];
				}
				$fecha6 = $fecha6 != false? "'".$fecha6."'" : "now()" ;
				
	
				$fechatraza = strftime('%Y-%m-%d %H:%M:%S');
					

				if($params['estatus']){

					$estatus = 	$params['estatus'];

				}else{

					$estatus =  '10';
				}
					

				if($params['tipoActividadVal'] == ''){

					$params['tipoActividadVal'] = 'null';

				}

				if($params['tipoEventoVal'] == ''){

					$params['tipoEventoVal'] = 'null';

				}


				if($params['infocentroVal'] == ''){

					$params['infocentroVal'] = 'null';

				}

				if($params['numeroParticipantes'] == ''){

					$params['numeroParticipantes'] = 'null';

				}
				
				if($params['localidad'] == false || $params['localidad'] == '' ){
					
					$params['localidad'] = 'null';

				}

				

				$query = "
	                               
	                                   INSERT INTO sai_comp
	                                   (comp_id,  
	                                    comp_asunto,
	                                    comp_documento,
	                                    comp_tipo_doc,
	                                    comp_descripcion,
	                                    comp_fecha,
	                                    esta_id,
	                                    usua_login,
	                                    depe_id,
	                                    comp_observacion,
	                                    comp_justificacion,
	                                    comp_lapso,
	                                    comp_cond_pago,
	                                    comp_monto_solicitado,
	                                    comp_prioridad,
	                                    numero_reserva,
	                                    comp_gerencia,
	                                    recursos,
	                                    comp_estatus,
	                                    comp_depe,
	                                    pcta_id,
	                                    rif_sugerido,
	                                    id_actividad,
	                                    fecha_reporte,
	                                    localidad,
	                                    beneficiario,
	                                    id_evento,
	                                    fecha_inicio,
                                        fecha_fin,
                                        control_interno,
                                        infocentro,
                                        comp_participantes
	                                   
	                                    
	                                    )
	                                    
										VALUES (
											
										'".$params['comp_id']."',
										'".$params['asuntoVal']."',
										'".$params['CodigoDocumento']."',
										'',
										'".$params['compromiso_descripcionVal']."',
										"; 
				                             
				                           
				                                  
				
				
				                       $query .= $fecha2 != false? "$fecha2," : "now()," ;
				                       
				                       $query .= " '".$estatus."',
							           '".$_SESSION['login']."',
							           '".$params['DependenciaTramita']."',
							           '".$params['observaciones']."',
							           '',
							           '',
							           '',
							           ".$params['montoTotal'].",
							            1,
							           '',
							           '".$params['unidadDependencia']."',
							            1,
							             '".$params['esta']."',
							           '".$params['unidadDependencia']."',
							           '".$params['compAsociado']."',
							           '".$params['ProveedorSugeridoval']."',
							           ". $params['tipoActividadVal'] .",
							            null,
							            ".$params['localidad'].",
							           '', 
							           ".$params['tipoEventoVal'].","; 
				$query .= $fecha4 != false? "'".$fecha4."'," : "null," ;
				$query .= $fecha6 != false? "'".$fecha6."'," : "null," ;
				$query .= $params['controlinterno'].",
                                         '".$params['infocentroVal']."',
                                         ".$params['numeroParticipantes']."
                                          )";			


			   	$result = $GLOBALS['SafiClassDb']->Query($query);
				if($result === false) throw new Exception('Error al insertar compromiso: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

				$result =   self::InsertCompromisoTraza($params,$fechatraza);
									
				if($result === false) throw new Exception('Error al insertar. Detalles compromiso traza: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

				
			
				if($params['compAsociado']){

					$result	= SafiModeloCompromisoImputa::UpdateDisponibilidadPcta($params);
					if($result === false) throw new Exception('Error al modificar disp pcta . Detalles imputa: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

				}

					
				$result	= SafiModeloCompromisoImputa::InsertCompromisoImputa($params);
				if($result === false) throw new Exception('Error al insertar. Detalles imputa: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					

				$result	= SafiModeloCompromisoImputa::InsertCompromisoDisponibilidad($params);
				if($result === false) throw new Exception('Error al insertar. Detalles imputa: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

					
					
					
					
				$result = SafiModeloCompromisoImputa:: InsertCompromisoImputaTraza($params,$fechatraza);

				if($result === false) throw new Exception('Error al insertar. Detalles imputa traza: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

				$result = SafiModeloCompromisoImputa:: InsertCompromisoTrazaReporte($params,$fechatraza);
				if($result === false) throw new Exception('Error al insertar. Detalles imputa traza reporte: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());


				$data = array();
				$dateTime = new DateTime();

				$fecha = (String) $dateTime->format('y-m-d h:m:s');
				$data['docg_id'] = $params['comp_id'] ;
				$data['docg_wfob_id_ini'] =  0 ;
				$data['docg_wfca_id'] = 0 ;
				$data['docg_usua_login'] = $_SESSION['login'];
				$data['docg_perf_id'] =   $_SESSION['user_perfil_id'] ;
				$data['docg_fecha'] = $fecha7.' '.strftime('%H:%M:%S');
				$data['docg_esta_id'] = $params['docg_esta_id'] != false ? $params['docg_esta_id'] :59 ;
				$data['docg_prioridad'] = 1 ;
				$data['docg_perf_id_act'] = '' ;
				$data['docg_estado_pres'] = '' ;
				$data['docg_numero_reserva'] =  '' ;
				$data['docg_fuente_finan'] = '' ;


				$docGenera = SafiModeloDocGenera::LlenarDocGenera($data);
				$result = SafiModeloDocGenera::GuardarDocGenera($docGenera);


					

				if($result === false) throw new Exception('Error al insertar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
				
				$result = $GLOBALS['SafiClassDb']->CommitTransaction();

				return true;

			} else {
				throw new Exception('Error al iniciar la transacci&oacute;n');
			}
		}catch(Exception $e){
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}

	}



	public static  function InsertCompromisoTraza($params,$fechaTraza = false){

		try{

			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			

			if($result === true){
				
			
					
				$preMsg = "error al insertar compromiso traza.";
				
				 		//	error_log(print_r("entro2",true));
					
				if($params['fecha']){
					$fecha3 = explode ('/',$params['fecha']);
					$fecha4 =  $fecha3[2].'-'.$fecha3[1].'-'.$fecha3[0].' '.strftime('%H:%M:%S');
				}
	
					
				if($params['txt_inicio']){
					$fecha5 = explode ('/',$params['txt_inicio']);
					$fecha6 =  $fecha5[2].'-'.$fecha5[1].'-'.$fecha5[0];
				}

					
				if($params['hid_hasta_itin']){
					$fecha = explode ('/',$params['hid_hasta_itin']);
					$fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
				}

				
				
					
				if($params['estatus']){

					$estatus = 	$params['estatus'];

				}else{

					$estatus =  '10';
				}


				$query = "
	                                   INSERT INTO sai_comp_traza
	                                   (comp_id,  
	                                    comp_asunto,
	                                    comp_descripcion,
	                                    comp_fecha,
	                                    esta_id,
	                                    usua_login,
	                                    depe_id,
	                                    comp_observacion,
	                                    comp_justificacion,
	                                    comp_lapso,
	                                    comp_cond_pago,
	                                    comp_monto_solicitado,
	                                    comp_prioridad,
	                                    numero_reserva,
	                                    comp_gerencia,
	                                    recursos,
	                                    comp_estatus,
	                                    comp_depe,
	                                    pcta_id,
	                                    rif_sugerido,
                                        comp_fecha2
	                                   
	                                    
	                                    )
	                                    
										VALUES (
											
										'".$params['comp_id']."',
										'".$params['asuntoVal']."',
										'".$params['compromiso_descripcionVal']."',
										"; $query .= $fecha4 != false? "'".$fecha4."'," : "now()," ;
				$query .= " '".$estatus."',
							           '".$_SESSION['login']."',
							           '".$params['DependenciaTramita']."',
							           '".$params['observaciones']."',
							           '',
							           '',
							           '',
							           ".$params['montoTotal'].",
							            1,
							           '',
							           '".$params['unidadDependencia']."',
							            1,
							             '".$params['esta']."',
							           '".$params['unidadDependencia']."',
							           '".$params['compAsociado']."',
							           '".$params['ProveedorSugeridoval']."'";
					

				if($fechaTraza){
					$query .=  ",'".$fechaTraza."')";

				}else{
					$query .=  "'".strftime('%Y-%m-%d %H:%M:%S')."'";
				}

				$result = $GLOBALS['SafiClassDb']->Query($query);

				if($result === false) throw new Exception('Error al insertar. Detalles punto de cuenta: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());


				$result = $GLOBALS['SafiClassDb']->CommitTransaction();
	
				 
					
				return true;
				
		
			} else {
				throw new Exception('Error al iniciar la transacci&oacute;n');
			}
			
		}catch(Exception $e){
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}

	}


	public static function GetCompromisoBasico($param = null)
	{
		try
		{



			$query = "
				SELECT
				     *
					
				FROM
					sai_comp 
					
				WHERE
					comp_id= '".$param."';
			";


			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			if($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
					
				return $row;

			}
		}
		catch(Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}





	public static function GetCompromiso(array $params = null)
	{
		try
		{


			$preMsg = "Error al intentar obtener compromiso.";
			$existeCriterio = false;
			$arrMsg = array();
			$queryWhere = "";
			$compromiso = null;
			$findParams = array();
			if($params === null)
			throw new Exception("El parámetro \"params\" es nulo.");
			if(!is_array($params))
			throw new Exception("El parámetro \"params\" no es un arreglo.");
			if(count($params) == 0)
			throw new Exception("El parámetro \"params\" está vacío.");
			if(!isset($params['idCompromiso']))
			$arrMsg[] = "El parámetro \"params['idCompromiso']\" no pudo ser encontrado.";
			if(($idCompromiso = $params['idCompromiso']) === null)
			$arrMsg[] = "El parámetro \"params['idCompromiso']\" es nulo.";
			if(($idCompromiso=trim($idCompromiso)) == '')
			$arrMsg[] = "El parámetro \"params['idCompromiso']\" está vacío.";
			else {
				$existeCriterio = true;
				$findParams["idsCompromiso"] = array($idCompromiso);

			}

			if(!$existeCriterio){
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}



			$arrCompromiso = self::GetCompromisos($findParams);

			if(!is_array($arrCompromiso) && count($arrCompromiso) == 0)
			throw new Exception($preMsg." No se pudo obtener compromiso con id \"".$idPuntoCuenta."\".");

			return current($arrCompromiso);

		}
		catch(Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}



	public static function GetCompromisos(array $params = null, $filtro = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener los compromisos.";
			$existeCriterio = false;
			$arrMsg = array();
			$queryWhere = "";
			$arrCompromisos= null;

			if($params === null)
			throw new Exception($preMsg."El parámetro \"params\" es nulo.");
			if(!is_array($params))
			throw new Exception($preMsg."El parámetro \"params\" no es un arreglo.");
			if(count($params) == 0)
			throw new Exception($preMsg."El parámetro \"params\" está vacío.");



			if(!$filtro){


					
				if(!isset($params['idsCompromiso']))
				$arrMsg[] = "El parámetro \"params['idsCompromiso']\" no pudo ser encontrado.";
				if(($idsCompromiso=$params['idsCompromiso']) === null)
				$arrMsg[] = "El parámetro \"params['idsCompromiso']\" es nulo.";
				if(!is_array($idsCompromiso))
				$arrMsg[] = "El parámetro \"params['idsCompromiso']\" no es un arreglo.";
				if(count($idsCompromiso) == 0)
				$arrMsg[] = "El parámetro \"params['idsCompromiso']\" está vacío.";
				else {
					$existeCriterio = true;
					$queryWhere = "Compromiso.comp_id IN ('".implode("', '", $idsCompromiso)."')";
				}
					
			} else {





				////filtro /////////////////////////////////////////////////////////////////////////////filtro

					
				$existeCriterio = true;

				$and = false;
					
				$cargo = substr($_SESSION['user_perfil_id'],0,2);
					
				if(
				($cargo !== substr(PERFIL_JEFE_PRESUPUESTO,0,2)) &&
				($cargo !== substr(PERFIL_DIRECTOR_EJECUTIVO,0,2)) &&
				($cargo !== substr(PERFIL_PRESIDENTE,0,2))
				){

					$and == true? $queryWhere .= 'AND ' : $and = true ;
					$queryWhere .= "compromiso.depe_id = '".substr($_SESSION['user_perfil_id'],-3)."'";
				}


				if(trim($params['ncompromiso'])){

					$and == true? $queryWhere .= 'AND ' : $and = true ;
					$queryWhere = "Compromiso.comp_id = '".trim($params['ncompromiso'])."'";



				}else {




					if($params['txt_inicio'] && $params['hid_hasta_itin']){
							
						$and == true? $queryWhere .= 'AND ' : $and = true ;
						$queryWhere .= "Compromiso.comp_fecha  BETWEEN to_date('".$params['txt_inicio']."', 'DD/MM/YYYY') AND to_date('".$params['hid_hasta_itin']."', 'DD/MM/YYYY') ";

					}else{
							
						$and == true? $queryWhere .= 'AND ' : $and = true ;
						$agno = $params['agnoComp'];
						$queryWhere .= " TO_CHAR(comp_fecha,'YYYY') = '".$agno."'";
					}


					if($params['asuntoVal']){
							
						$and == true? $queryWhere .= 'AND ' : $and = true ;
						$queryWhere .= "Compromiso.comp_asunto = '".$params['asuntoVal']."'";
					}

					if($params['PartidaBusqueda'] || $params['compProyAccVal']){
						$puntoCuentaImputa =  SafiModeloCompromisoImputa::GetCompImputaFiltro($params['PartidaBusqueda'],$params['compProyAccVal']);

						if(is_array($puntoCuentaImputa)){
							$and == true? $queryWhere .= 'AND ' : $and = true;
							$queryWhere .= "Compromiso.comp_id IN ('".implode("', '",$puntoCuentaImputa)."')";
						}
					}




					if($params['palabraClave']){
							
						$and == true? $queryWhere .= 'AND ' : $and = true ;
						$queryWhere .= "Compromiso.comp_descripcion LIKE '%".$params['palabraClave']."%'";

					}


					if($params['tipoActividadVal']){
							
						$and == true? $queryWhere .= 'AND ' : $and = true ;
						$queryWhere .= "Compromiso.id_actividad = '".$params['tipoActividadVal']."'";
					}


				}

				// filtro /////////////////////////////////////////////////////////////////////////////filtro */
			}


			if(!$existeCriterio){
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}

			$query = "
				SELECT
					 *,
					 TO_CHAR(comp_fecha, 'DD/MM/YYYY HH24:MI:SS') AS comp_fecha,
					 TO_CHAR(fecha_reporte, 'DD/MM/YYYY') AS fecha_reporte,
					 TO_CHAR(fecha_inicio, 'DD/MM/YYYY') AS fecha_inicio,
					 TO_CHAR(fecha_fin, 'DD/MM/YYYY') AS fecha_fin
				FROM
					sai_comp compromiso
					
				WHERE
					".$queryWhere."
			";
				

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));


			$x = 1;
			$arrCompromisos  = array();
			$arrIdsAsunto = array();
			$arrIdsEstatus = array();
			$arrIdsDependencias = array();
			$arrIdsPuntosCuenta = array();
			$arrIdsProveedorSugerido = array();
			$arrIdsProveedorSugerido2 = array();
			$arrIdsActividades = array();
			$arrIdsPresentadoPor = array();
			$arrIdsEvento = array();
			$arrIdsInfocentro = array();
			$arrIdsUsuarios = array();
			$arrIdsControlInterno = array();




			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$row2[$row['comp_id']] = $row;


				$row['depe_id'] != null ? 	$arrIdsDependencias[$row['depe_id']] = $row['depe_id']: '';

				$row['comp_gerencia'] != null ? 	$arrIdsDependencias[$row['comp_gerencia']] = $row['comp_gerencia']: '';
				$arrIdsControlInterno[$row['control_interno']] = $row['control_interno'];
				$row['comp_depe'] != null ? $arrIdsDependencias[$row['comp_depe']] = $row['comp_depe'] : '';
				$arrIdsUsuarios[$row['usua_login']] = $row['usua_login'];
				$arrIdsAsunto[$row['comp_asunto']] = $row['comp_asunto'];
				$arrIdsEstatus[$row['esta_id']] = $row['esta_id'];
				//$arrIdsLocalidad[$row['localidad']] = $row['localidad'];
				//	$arrIdsActividades[$row['id_actividad']] = $row['id_actividad'];
				//	$arrIdsEvento[$row['id_evento']] = $row['id_evento'];
				//	$arrIdsInfocentro[$row['sai_infocentro']] = $row['sai_infocentro'];
				$arrCompromisos[$row['comp_id']] = $row['comp_id'];



				$cadena = $row['rif_sugerido'];

					

				$caracter = "~";
					
				if (strpos($cadena, $caracter) !== false){

					$val1 = explode ('~',$row['rif_sugerido']);

					$arrIdsProveedorSugerido2[$row['rif_sugerido']] = $val1[1];

				}else{

					$arrIdsProveedorSugerido[$row['rif_sugerido']] = $row['rif_sugerido'];

				}

					

			}

				
				


			 


			$usuarios = $arrIdsUsuarios  != null ?SafiModeloEmpleado::GetEmpleadosByCedulas($arrIdsUsuarios) : null;
			$dependencias=$arrIdsDependencias != null ?SafiModeloDependencia::GetDependenciaByIds($arrIdsDependencias) : null;
			$controlinterno = $arrIdsControlInterno != null ?SafiModeloControlinterno::GetcontrolInternosIds($arrIdsControlInterno) : null;
			$asuntos =$arrIdsAsunto != null ?SafiModeloCompromisoAsunto::GetAsusntosId($arrIdsAsunto) : null;
			$estatus =$arrIdsEstatus != null ?SafiModeloEstatus::GetEstadoPctaIdPcuenta($arrIdsEstatus) : null;
			$proveedorSugerido = $arrIdsProveedorSugerido  != null ?SafiModeloEmpleado::GetProveedoresSugerido($arrIdsProveedorSugerido) : null;
			//$estadosVenezuela = $arrIdsLocalidad  != null ?SafiModeloEstadosVenezuela::GetEstadosVenezuelaId($arrIdsLocalidad) : null;

			//$actividades = $arrIdsActividades  != null ?SafiModeloTipoActividadCompromiso::GetActividadIds($arrIdsActividades) : null;
			//$eventos = $arrIdsEvento  != null ?SafiModeloTipoEvento::GetEventosIds($arrIdsEvento) : null;
			//$infocentros = $arrIdsInfocentro  != null ?SafiModeloInfocentro::GetAllInfocentrosIds($arrIdsInfocentro) : null;

				

			if(!$filtro){

				$compromisoImputa =  SafiModeloCompromisoImputa::GetPctaImputasCompId($arrCompromisos);



			}



			$paramsLlnar = array();
			
			
			
			if($row2){

					
					
				foreach ($row2 as $index => $val){


					$cadena = $val['rif_sugerido'];
					$caracter = "~";

					$fecha = explode ('/',$val['comp_fecha']);

					$fecha_cambio = strtotime("31-12-2013");
					$fecha_entrada  = strtotime($fecha[0]."-".$fecha[1]."-".$fecha[2]);
					

					if($fecha_entrada < $fecha_cambio){  
						
						
						
						if($proveedorSugerido[$val['rif_sugerido']]){
							
							

							$paramsLlnar['rif_sugerido'] = $proveedorSugerido[$val['rif_sugerido']]['id'].":".$proveedorSugerido[$val['rif_sugerido']]['nombre'];

						}else{

							$vineta = explode('~',$val['rif_sugerido']);
							$paramsLlnar['rif_sugerido'] = $vineta[0];
							
							

						}

							
							

					}else if(strpos($cadena, $caracter) !== false){

						$vineta = explode('~',$arrIdsProveedorSugerido2[$val['rif_sugerido']]);
						$paramsLlnar['rif_sugerido'] = $vineta[0];
						
							
					}else{
						

						$paramsLlnar['rif_sugerido'] = $proveedorSugerido[$val['rif_sugerido']]['id'].":".$proveedorSugerido[$val['rif_sugerido']]['nombre'];

					}


					$paramsLlnar ['comp_id'] = $val['comp_id'];
					$paramsLlnar ['comp_documento'] = $val['comp_documento'];
					$paramsLlnar ['comp_tipo_doc'] = $val['comp_tipo_doc'];
					$paramsLlnar ['comp_descripcion'] = $val['comp_descripcion'];
					$paramsLlnar ['comp_fecha'] = $val['comp_fecha'];
					$paramsLlnar ['comp_observacion'] = $val['comp_observacion'];
					$paramsLlnar ['comp_justificacion'] = $val['comp_justificacion'];
					$paramsLlnar ['comp_lapso'] = $val['comp_lapso'];
					$paramsLlnar ['comp_cond_pago'] = $val['comp_cond_pago'];
					$paramsLlnar ['comp_monto_solicitado'] = $val['comp_monto_solicitado'];
					$paramsLlnar ['comp_prioridad'] = $val['comp_prioridad'];
					$paramsLlnar ['numero_reserva'] = $val['numero_reserva'];
					$paramsLlnar ['recursos'] = $val['recursos'];
					$paramsLlnar ['comp_estatus'] = $val['comp_estatus'];
					$paramsLlnar ['pcta_id'] = $val['pcta_id'];
					$paramsLlnar ['fecha_reporte'] = $val['fecha_reporte'];
					$paramsLlnar ['beneficiario'] = $val['beneficiario'];
					$paramsLlnar ['fecha_inicio'] = $val['fecha_inicio'];
					$paramsLlnar ['fecha_fin'] = $val['fecha_fin'];
					$paramsLlnar ['control_interno'] = $controlinterno[$val['control_interno']];
					$paramsLlnar ['comp_participantes'] = $val['comp_participantes'];
					$paramsLlnar['comp_asunto'] = $asuntos[$val['comp_asunto']];
					$paramsLlnar['esta_id'] = $estatus[$val['esta_id']];
					$paramsLlnar['usua_login'] = $usuarios[$val['usua_login']];
					$paramsLlnar['depe_id'] = $dependencias[$val['depe_id']];
					$paramsLlnar['comp_gerencia'] = $dependencias[$val['comp_gerencia']];
					$paramsLlnar['comp_depe'] = $dependencias[$val['comp_depe']];
					//	$paramsLlnar['id_actividad'] = $actividades[$val['id_actividad']];
				//	$paramsLlnar['localidad'] = $estadosVenezuela[$val['localidad']];
					//	$paramsLlnar['id_evento'] = $eventos[$val['id_evento']];
					//	$paramsLlnar['sai_infocentro'] = $infocentros[$val['sai_infocentro']];
					$paramsLlnar['compromisoImputa'] = $compromisoImputa[$val['comp_id']];
					$compromiso[$val['comp_id']] = self::LlenarCompromiso($paramsLlnar);


				}



			}


			return $compromiso;





		}catch(Exception $e)

		{
			error_log($e, 0);
			return false;
		}
	}






	public static function	UpdateComp($params){

		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){


			 if($params['fecha']){
					$fecha = explode ('/',$params['fecha']);
					$fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' '.strftime('%H:%M:%S');
				}
				$fecha2 = $fecha2 != false? "'".$fecha2."'" : "now()" ;

				if($params['txt_inicio']){
					$fecha3 = explode ('/',$params['txt_inicio']);
					$fecha4 =  $fecha3[2].'-'.$fecha3[1].'-'.$fecha3[0];
				}
				$fecha4 = $fecha4 != false? "'".$fecha4."'" : "now()" ;
				
				if($params['txt_inicio']){
					$fecha3 = explode ('/',$params['txt_inicio']);
					$fecha4 =  $fecha3[2].'-'.$fecha3[1].'-'.$fecha3[0];
				}
				$fecha4 = $fecha4 != false? "'".$fecha4."'" : "now()" ;

				
				if($params['hid_hasta_itin']){
					$fecha5 = explode ('/',$params['hid_hasta_itin']);
					$fecha6 =  $fecha5[2].'-'.$fecha5[1].'-'.$fecha5[0];
				}
				$fecha6 = $fecha6 != false? "'".$fecha6."'" : "now()";
				
				
			//	error_log(print_r($params,true));
				
				if($params['fechaReporte']){
					$fecha7 = explode ('/',$params['fechaReporte']);
					$fecha8 =  $fecha7[2].'-'.$fecha7[1].'-'.$fecha7[0];
				}else{
				
                     $fecha8 = false;				
				
				}
				
				$fecha8 = $fecha8 != false? "'".$fecha8."'" : "null";
				

				$fechatraza = strftime('%Y-%m-%d %H:%M:%S');
					
				if($params['estatus']){

					$estatus = 	$params['estatus'];

				}else{

					$estatus =  '10';
				}


				if($params['tipoActividadVal'] == ''){

					$params['tipoActividadVal'] = 'null';

				}

				if($params['tipoEventoVal'] == ''){

					$params['tipoEventoVal'] = 'null';

				}


				if($params['infocentroVal'] == ''){

					$params['infocentroVal'] = 'null';

				}

				if($params['numeroParticipantes'] == ''){

					$params['numeroParticipantes'] = 'null';

				}
				
				

			if($params['localidad'] == false || $params['localidad'] == '' ){
					
					$params['localidad'] = 'null';

				}
					
					
				$params['comp_id'] = $params['comp'];

				$query = "   UPDATE
				         	     sai_comp
				                      SET 
				                      
	                                    comp_id = '".$params['comp']."',
	                                    comp_asunto = '".$params['asuntoVal']."',
	                                    comp_documento = '".$params['CodigoDocumento']."',
	                                    comp_descripcion = '".$params['compromiso_descripcionVal']."',
	                                    comp_fecha = ".$fecha2.",
	                                    esta_id = '".$estatus."',
	                                    usua_login = '".$_SESSION['login']."',
	                                    depe_id = '".$params['DependenciaTramita']."',
	                                    comp_observacion =  '".$params['observaciones']."',
	                                    comp_gerencia =  '".$params['unidadDependencia']."',
	                                    comp_monto_solicitado = ".$params['montoTotal'].",
	                                    comp_estatus =  '".$params['esta']."',
	                                    comp_depe =  '".$params['unidadDependencia']."',
	                                    pcta_id =  '".$params['compAsociado']."',
	                                    rif_sugerido =  '".$params['ProveedorSugeridoval']."',
	                                    id_actividad =  ".$params['tipoActividadVal'].",
	                                    fecha_reporte =  ".$fecha8.",
	                                    localidad =  ".$params['localidad'].",
	                                    id_evento =   ".$params['tipoEventoVal'].",
	                                    fecha_inicio = ".$fecha4.",
	                                    fecha_fin = ".$fecha6.",
	                                    infocentro = '".$params['infocentroVal']."',
	                                    comp_cond_pago =  ".$params['numeroParticipantes'].",
	                                    control_interno =  ".$params['controlinterno']."
	                                    
	                                  WHERE 
	                                
	                                   comp_id=  '".$params['comp']."';
	                                           
	                                    ";	
				
						
				//error_log(print_r($query,true));
				
		
				$result = $GLOBALS['SafiClassDb']->Query($query);
				if($result === false) throw new Exception('Error al Modificar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());



				$result = SafiModeloCompromisoImputa:: SetPctaCompTrazaReporte($params);

				if($result){
					$params2 =  $params;
					$params2['imputa'] = $result;


				 $result = SafiModeloCompromisoImputa:: InsertCompromisoTrazaReporte($params2,$fechatraza);
				 
				 if($result === false) throw new Exception('Error al insertar. Detalles imputa traza reporte: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

				}


				$result =   self::InsertCompromisoTraza($params,$fechatraza);
				if($result === false) throw new Exception('Error al insertar. Detalles punto de cuenta traza: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());


				if($params['compAsociado']){

					$result	= SafiModeloCompromisoImputa::UpdateDisponibilidadPcta($params);
					if($result === false) throw new Exception('Error al modificar disp pcta . Detalles imputa: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

				}
					

				$result = SafiModeloCompromisoImputa::EliminarCompImputaId($params['comp']);
				if($result === false) throw new Exception('Error al eliminar imputas. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());



				$result	= SafiModeloCompromisoImputa::InsertCompromisoImputa($params);
				if($result === false) throw new Exception('Error al insertar. Detalles imputa: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());



				$result = SafiModeloCompromisoImputa::EliminarCompromisoDisponibilidad($params['comp']);
				if($result === false) throw new Exception('Error al eliminar imputas disp. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());



				$result	= SafiModeloCompromisoImputa::InsertCompromisoDisponibilidad($params);
				if($result === false) throw new Exception('Error al insertar. Detalles imputa: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

					
					
				$result = SafiModeloCompromisoImputa:: InsertCompromisoImputaTraza($params,$fechatraza);
				if($result === false) throw new Exception('Error al insertar. Detalles imputa traza: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());


				$result = $GLOBALS['SafiClassDb']->CommitTransaction();

				return true;

			} else {
				throw new Exception('Error al iniciar la transacci&oacute;n');
			}
		}catch(Exception $e){
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}

	}


	public static function GetCompTrazaVariacion(array $param = null){

		try {

			if($param === null)
			throw new Exception($preMsg."El parámetro \"params\" es nulo.");
			if(!is_array($param))
			throw new Exception($preMsg."El parámetro \"params\" no es un arreglo.");
			if(count($param) == 0)
			throw new Exception($preMsg."El parámetro \"params\" está vacío.");


			$params = array();

			$and = false;

			if($param['ncompromiso']){

				$and == true? $queryWhere .= 'AND ' : $and = true ;
				$queryWhere = "comp_id = '".$param['ncompromiso']."'";
				$queryWhere2 = "sc.comp_id = '".$param['ncompromiso']."'";
					
			}
			if($param['txt_inicio'] && $param['hid_hasta_itin']){
					
				$and == true? $queryWhere .= 'AND ' : $and = true ;
				$queryWhere .= "comp_fecha  BETWEEN to_date('".$param['txt_inicio']."', 'DD/MM/YYYY') AND to_date('".$param['hid_hasta_itin']."', 'DD/MM/YYYY') ";
				$queryWhere2 .= "sc.comp_fecha  BETWEEN to_date('".$param['txt_inicio']."', 'DD/MM/YYYY') AND to_date('".$param['hid_hasta_itin']."', 'DD/MM/YYYY') ";

			}

			$queryWhere2 =  $queryWhere2 != false ? "AND ".$queryWhere2 : '';

			$query = "
				SELECT
					 comp_id,
					 TO_CHAR(comp_fecha, 'DD/MM/YYYY') AS comp_fecha
				FROM
					sai_comp_traza 
					
				WHERE
					".$queryWhere."
			";



			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
				throw new Exception("Error variacion . Detalles: ".
				utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

		 }

		 $paramsLlnar = array();

			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{


				$paramsLlnar ['comp_id'] = $row['comp_id'];
				$paramsLlnar ['comp_fecha'] = $row['comp_fecha'];
				$params['data'][$row['comp_id']] = self::LlenarCompromiso($paramsLlnar);
					
					
				////////////////////////////////////////////////////         1        /////////////////////////////////////////////////////////////////////


				$query2 = "
	 select  distinct t7.comp_id, 
			 to_char(sc.comp_fecha, 'DD/MM/YYYY') as comp_fecha,
			 case sc.esta_id when '10' then 'Activo' else 'Inactivo' end  as esta_id,
			 t7.comp_sub_espe,
			 coalesce(t8.centro_gestor, ' ') || coalesce(t9.centro_gestor, ' ') as comp_acc_pp, 
			 coalesce(t8.centro_costo, ' ') || coalesce(t9.centro_costo, ' ') as comp_acc_esp,
			 t7.comp_monto,
			 t7.comp_monto_exento,
			 to_char(t7.comp_fecha, 'DD/MM/YYYY HH:mm:ss')  as fecha1,
			 to_char(sc.comp_fecha2, 'DD/MM/YYYY HH:mm:ss'),
			 to_char(sc.comp_fecha, 'DD/MM/YYYY') as fecha2
			 
	 from 
			 sai_comp_traza sc,sai_comp_imputa_traza t7 left outer join sai_acce_esp t8 on (t7.comp_acc_pp=t8.acce_id and t7.comp_acc_esp=t8.aces_id )
			 left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id) 
			 left outer join sai_proy_a_esp t9 on(t7.comp_acc_pp=t9.proy_id and t7.comp_acc_esp=t9.paes_id) 
			 left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id ) 
	 where 
	 
			 sc.esta_id<>2 and 
			 t7.comp_id=sc.comp_id and 
			 to_char(t7.comp_fecha, 'DD/MM/YYYY HH:mm:ss')=to_char(sc.comp_fecha2, 'YYYY-MM-DD HH:mm:ss')  ".$queryWhere2."
			 and t7.comp_id='".$row['comp_id']. "' 
			 order by fecha1 asc,3,4,5,6 
			";
					

					
				if(($result2 = $GLOBALS['SafiClassDb']->Query($query2)) === false){
					throw new Exception("Error variacion . Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				}
					
			 $paramsLlnar = array();
			 	
			 while ($row2 = $GLOBALS['SafiClassDb']->Fetch($result2))
			 {

			 	$params['VariacionComp1'][$row['comp_id']][] = $row2;

			 }
			 	
			 	
			 	
			 ////////////////////////////////////////////////////         2        /////////////////////////////////////////////////////////////////////
			 	

			 $query3 = "
				select distinct t7.comp_id, to_char(sc.comp_fecha, 'DD/MM/YYYY') as comp_fecha,
						case sc.esta_id when '10' then 'Activo' else 'Inactivo' end  as esta_id,
						t7.comp_sub_espe, coalesce(t8.centro_gestor, ' ') || coalesce(t9.centro_gestor, ' ') as comp_acc_pp, 
						coalesce(t8.centro_costo, ' ') || coalesce(t9.centro_costo, ' ') as comp_acc_esp, 
						t7.comp_monto,t7.comp_monto_exento,to_char(t7.comp_fecha, 'DD/MM/YYYY HH:mm:ss')  as fecha1, to_char(sc.comp_fecha2, 'DD/MM/YYYY HH:mm:ss'),
						to_char(sc.comp_fecha, 'DD/MM/YYYY') as fecha2
						
				from 
						sai_comp_traza sc,sai_comp_imputa_traza t7 left outer join sai_acce_esp t8 on (t7.comp_acc_pp=t8.acce_id and t7.comp_acc_esp=t8.aces_id )
						left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id) 
						left outer join sai_proy_a_esp t9 on(t7.comp_acc_pp=t9.proy_id and t7.comp_acc_esp=t9.paes_id) 
						left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id ) 
						
				where 
				
						sc.esta_id<>2 and 
						t7.comp_id=sc.comp_id and 
						to_char(t7.comp_fecha, 'YYYY-MM-DD HH:mm:ss')=to_char(sc.comp_fecha2, 'YYYY-MM-DD HH:mm:ss') ".$queryWhere2 . "
						and t7.comp_id='".$row['comp_id']. "' 
						order by fecha1 desc,3,4,5,6  "; 
			 	
			 	
			 	
			 if(($result3 = $GLOBALS['SafiClassDb']->Query($query3)) === false){
			 	throw new Exception("Error variacion . Detalles: ".
			 	utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			 }
			 	
			 $paramsLlnar = array();
			 	
			 while ($row3 = $GLOBALS['SafiClassDb']->Fetch($result3))
			 {

			 	$params['VariacionComp2'][$row['comp_id']][] = $row3;

			 }



			}


			return $params;

		} catch (Exception $e) {
			error_log($e, 0);
			return false;
		}

	}

	public static function GetCompCausadoPagado(array $param = null){

		try {


			if($param === null)
			throw new Exception($preMsg."El parámetro \"params\" es nulo.");
			if(!is_array($param))
			throw new Exception($preMsg."El parámetro \"params\" no es un arreglo.");
			if(count($param) == 0)
			throw new Exception($preMsg."El parámetro \"params\" está vacío.");

			$params = array();

			$and = false;

			if($param['hid_hasta_itin'] && $param['tipo_reporte']){
					
					
				if(
				!isset($_POST["hid_hasta_itin"])
				|| $_POST["hid_hasta_itin"] == null
				|| ($fecha_hasta=trim($_POST["hid_hasta_itin"])) == ''
				|| strlen($fecha_hasta) != 10
				){
					$anno_pres = $_SESSION['an_o_presupuesto'];
				} else {
					$anno_pres = substr($fecha_hasta, 6, 4);
				}

					
				$fecha_fi=trim($_POST['hid_hasta_itin']);
				$fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2);
				$fecha_fin2=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2);
				$fecha_ini2=substr($fecha_fi,6,4)."-01-01";
				$ano2=substr($fecha_fin,2,2);

				$queryWhere .= "AND sc.comp_fecha  BETWEEN to_date('".$fecha_ini2."', 'YYYY-MM-DD') AND to_date('".$fecha_fin2."', 'YYYY-MM-DD') ";



				if ($param['tipo_reporte']==1) {

					/*$query = "
				
				SELECT 
				        distinct t7.comp_id, to_char(sc.comp_fecha, 'DD/MM/YYYY') as comp_fecha, 
						coalesce(t8.centro_gestor, ' ') || coalesce(t9.centro_gestor, ' ') as centrogestor, 
						coalesce(t8.centro_costo, ' ') || coalesce(t9.centro_costo, ' ') as centrocosto,sc.esta_id ,
						sc.comp_fecha as fecha,comp_monto_solicitado
						
				FROM
						sai_comp_imputa t7 left outer join sai_comp sc on (t7.comp_id=sc.comp_id) 
						left outer join sai_acce_esp t8 on (t7.comp_acc_pp=t8.acce_id and t7.comp_acc_esp=t8.aces_id )
						left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id) 
						left outer join sai_proy_a_esp t9 on(t7.comp_acc_pp=t9.proy_id and t7.comp_acc_esp=t9.paes_id) 
						left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id ) 
						
				WHERE 
						sc.esta_id<>2 and sc.esta_id<>15 ".$queryWhere."
				        and t7.comp_id not in (select comp_id from sai_sol_pago where sopg_id like '%".$ano2."') and t7.comp_id not in (select nro_compromiso from sai_codi where comp_id like '%".$ano2."') 
				
			   ORDER BY 3,4,sc.comp_fecha";*/
			   
			   $query = "
							SELECT DISTINCT sci.comp_id,
	                			TO_CHAR(sc.comp_fecha, 'DD/MM/YYYY') AS comp_fecha,
	               				COALESCE(acesp.centro_gestor, ' ') AS centrogestor,
	                			COALESCE(acesp.centro_costo, ' ') AS centrocosto,
	                			sc.esta_id ,
	                			sc.comp_fecha AS fecha,
	                			comp_monto_solicitado
							FROM sai_comp_imputa sci
							INNER JOIN sai_comp sc ON (sci.comp_id = sc.comp_id)
							INNER JOIN sai_acce_esp acesp ON (sci.comp_acc_pp = acesp.acce_id 
			   									               AND sci.comp_acc_esp = acesp.aces_id
																AND sci.pres_anno = acesp.pres_anno
															)
							INNER JOIN sai_ac_central acc ON(acesp.acce_id = acc.acce_id)
							WHERE sc.esta_id != 2
  								AND sc.esta_id != 15
  								AND sc.comp_fecha  BETWEEN to_date('".$fecha_ini2."', 'YYYY-MM-DD') AND to_date('".$fecha_fin2."', 'YYYY-MM-DD')
  								AND (
										-- Caso nunca causados
										(
										sci.comp_id NOT IN
									    		(SELECT comp_id
											 	FROM sai_sol_pago
												 WHERE sopg_id LIKE '%".$ano2."')
										AND sci.comp_id NOT IN
									    		(SELECT nro_compromiso
												FROM sai_codi
												WHERE comp_id LIKE '%".$ano2."')
										)
										-- Caso el sopg está anulado/El codi está anulado/ 
										OR 	
										(
											(-- Caso el sopg está anulado o devuelto
											sci.comp_id IN
										    		(SELECT comp_id
												 FROM sai_sol_pago
												 WHERE sopg_id LIKE '%".$ano2."'
													AND (esta_id = 15 OR esta_id = 2 OR esta_id = 7)
												)
											AND -- Sólo tiene sopgs en estado 15
												(
												sci.comp_id NOT IN
										    		(SELECT comp_id
												 FROM sai_sol_pago
												 WHERE sopg_id LIKE '%".$ano2."'
													AND (esta_id != 15 AND esta_id != 2 AND esta_id != 7)
												)
									
												)
											)

										OR (-- Caso el codi está anulado/ 
										 sci.comp_id IN
									    		(SELECT nro_compromiso
											FROM sai_codi
											WHERE comp_id LIKE '%".$ano2."'
												AND esta_id = 15
											)
										AND -- Sólo tiene codis en estado 15
											sci.comp_id IN
									    		(SELECT nro_compromiso
											FROM sai_codi
											WHERE comp_id LIKE '%".$ano2."'
												AND esta_id != 15
											)
										)
	
									)
								)

								UNION
								
								SELECT DISTINCT sci.comp_id,
								                TO_CHAR(sc.comp_fecha, 'DD/MM/YYYY') AS comp_fecha,
								                COALESCE(pesp.centro_gestor, ' ') AS centrogestor,
								                COALESCE(pesp.centro_costo, ' ') AS centrocosto,
								                sc.esta_id ,
								                sc.comp_fecha AS fecha,
								                comp_monto_solicitado
								FROM sai_comp_imputa sci
								INNER JOIN sai_comp sc ON (sci.comp_id = sc.comp_id)
								INNER JOIN sai_proy_a_esp pesp ON (sci.comp_acc_pp=pesp.proy_id
								                                     AND sci.comp_acc_esp=pesp.paes_id
												AND sci.pres_anno = pesp.pres_anno
												)
								INNER JOIN sai_proyecto proy ON(pesp.proy_id = proy.proy_id)
								WHERE sc.esta_id != 2
								  AND sc.esta_id != 15
								  AND sc.comp_fecha  BETWEEN to_date('".$fecha_ini2."', 'YYYY-MM-DD') AND to_date('".$fecha_fin2."', 'YYYY-MM-DD')
								  AND (
										-- Caso nunca causados
										(
											sci.comp_id NOT IN
										    		(SELECT comp_id
												 FROM sai_sol_pago
												 WHERE sopg_id LIKE '%".$ano2."')
											AND sci.comp_id NOT IN
										    		(SELECT nro_compromiso
												FROM sai_codi
												WHERE comp_id LIKE '%".$ano2."')
										)
										-- Caso el sopg está anulado/El codi está anulado/ 
										OR 	
										(
											(-- Caso el sopg está anulado o devuelto
											sci.comp_id IN
										    		(SELECT comp_id
												 FROM sai_sol_pago
												 WHERE sopg_id LIKE '%".$ano2."'
													AND (esta_id = 15 OR esta_id = 2 OR esta_id = 7)
												)
											AND -- Sólo tiene sopgs en estado 15
												(
												sci.comp_id NOT IN
										    		(SELECT comp_id
												 FROM sai_sol_pago
												 WHERE sopg_id LIKE '%".$ano2."'
													AND (esta_id != 15 AND esta_id != 2 AND esta_id != 7)
												)
									
												)
											)
									
											OR (-- Caso el codi está anulado/ 
											 sci.comp_id IN
										    		(SELECT nro_compromiso
												FROM sai_codi
												WHERE comp_id LIKE '%".$ano2."'
													AND esta_id = 15
												)
											AND -- Sólo tiene codis en estado 15
												sci.comp_id IN
										    		(SELECT nro_compromiso
												FROM sai_codi
												WHERE comp_id LIKE '%".$ano2."'
													AND esta_id != 15
													)
												)
									
											)
										)
								ORDER BY 3,
								         4,
									6			   		
								
								";


				}else{


					$ao_sopg="sopg-%".substr($anno_pres,2,2);
					$ao_comp="comp-%".substr($anno_pres,2,2);
				/*	$query = "
				select 
						compromiso.comp_id,
						compromiso.centrogestor,
						compromiso.centrocosto,
						compromiso.comp_monto_solicitado,
						sum(compromiso.monto_sopg) as causado,
						compromiso.comp_fecha
						 
               from
                        (select 
		                        sc.comp_id ,
		                        coalesce(sae.centro_gestor, ' ') || coalesce(spa.centro_gestor, ' ') as centrogestor, 
		                        coalesce(sae.centro_costo, ' ') || coalesce(spa.centro_costo, ' ') as centrocosto,comp_monto_solicitado,
		                        sp.sopg_monto as monto_sopg, 
		                        to_char(sc.comp_fecha, 'DD/MM/YYYY') as comp_fecha
		                        
                         from 
                                sai_sol_pago_imputa spi
								left outer join sai_sol_pago sp on (spi.sopg_id=sp.sopg_id and sp.sopg_id like '".$ao_sopg."'  and sp.comp_id like '".$ao_comp."')
								left outer join sai_doc_genera sdg on (sdg.docg_id=sp.sopg_id and sdg.esta_id=39 )
								left outer join sai_acce_esp sae on (spi.sopg_acc_pp=sae.acce_id and spi.sopg_acc_esp=sae.aces_id )
								left outer join sai_ac_central sac on(sae.acce_id=sac.acce_id) 
								left outer join sai_proy_a_esp spa on(spi.sopg_acc_pp=spa.proy_id and spi.sopg_acc_esp=spa.paes_id) 
								left outer join sai_proyecto spr on (spa.proy_id=spr.proy_id ) 
						        ,sai_comp sc
						        
						where 
						        sc.comp_id=sp.comp_id and 
						        sdg.docg_id=sp.sopg_id and 
						        sdg.esta_id=39 
						        
                        group by sc.comp_id,
                        centrogestor,
                        centrocosto,
                        comp_monto_solicitado,
                        monto_sopg,comp_fecha
                        ) as compromiso
                        
            group by 
            	        compromiso.comp_id,
            	        compromiso.centrogestor,
            	        compromiso.centrocosto,
            	        compromiso.comp_monto_solicitado,
            	        compromiso.comp_fecha";


echo $query;*/
					$query ="
							SELECT DISTINCT sci.comp_id,
							                TO_CHAR(sc.comp_fecha, 'DD/MM/YYYY') AS comp_fecha,
							                COALESCE(acesp.centro_gestor, ' ') AS centrogestor,
							                COALESCE(acesp.centro_costo, ' ') AS centrocosto,
							                sc.esta_id ,
							                sc.comp_monto_solicitado,
											SUM(sopg.sopg_monto) AS causado
											--sopg.sopg_monto AS causado
							FROM sai_comp_imputa sci
							INNER JOIN sai_comp sc ON (sci.comp_id = sc.comp_id)
							INNER JOIN sai_acce_esp acesp ON (sci.comp_acc_pp = acesp.acce_id
							                                AND sci.comp_acc_esp = acesp.aces_id
											AND sci.pres_anno = acesp.pres_anno
											)
							INNER JOIN sai_ac_central acc ON(acesp.acce_id = acc.acce_id)
							INNER JOIN sai_sol_pago sopg ON (sopg.comp_id = sc.comp_id)
							INNER JOIN sai_causado caus ON (sopg.sopg_id = caus.caus_docu_id)
							WHERE sc.esta_id != 2
							  AND sc.esta_id != 15
							  AND sopg.esta_id != 15  
							  AND sc.comp_id NOT IN (SELECT nro_compromiso FROM sai_codi WHERE esta_id != 15 AND esta_id !=2 AND comp_id LIKE '%".$ano2."')
							  AND sc.comp_fecha  BETWEEN to_date('".$fecha_ini2."', 'YYYY-MM-DD') AND to_date('".$fecha_fin2."', 'YYYY-MM-DD')
							-- NO TIENE PGCH NI TRANS
							AND (
								sopg.sopg_id NOT IN (SELECT docg_id 
										FROM sai_pago_cheque
										WHERE pgch_id LIKE '%".$ano2."'
										)
								OR
								sopg.sopg_id IN (SELECT docg_id 
										FROM sai_pago_cheque
										WHERE pgch_id LIKE '%".$ano2."'
											AND pgch_id NOT IN (SELECT paga_docu_id FROM sai_pagado WHERE paga_docu_id LIKE 'pgch%' AND paga_docu_id LIKE '%".$ano2."' AND esta_id = 1)
										)
								OR
								sopg.sopg_id IN (SELECT docg_id 
										FROM sai_pago_cheque
										WHERE pgch_id LIKE '%".$ano2."'
											AND pgch_id NOT IN (SELECT paga_docu_id FROM sai_pagado WHERE paga_docu_id LIKE 'pgch-%14' AND esta_id != 1)
										)
							
							)
							AND (
								sopg.sopg_id NOT IN (SELECT docg_id 
										FROM sai_pago_transferencia
										WHERE trans_id LIKE '%".$ano2."'
										)
								OR
								sopg.sopg_id IN (SELECT docg_id 
										FROM sai_pago_transferencia
										WHERE trans_id LIKE '%".$ano2."'
											AND trans_id NOT IN (SELECT paga_docu_id FROM sai_pagado WHERE paga_docu_id LIKE 'tran-%' AND paga_docu_id LIKE '%".$ano2."' AND esta_id = 1)
										)
								OR
								sopg.sopg_id IN (SELECT docg_id 
										FROM sai_pago_transferencia
										WHERE trans_id LIKE 'tran-%14'
											AND trans_id IN (SELECT paga_docu_id FROM sai_pagado WHERE paga_docu_id LIKE 'tran-%' AND paga_docu_id LIKE '%".$ano2."' AND esta_id != 1 )
										)	
							)
							GROUP BY sopg.comp_id, 
									sci.comp_id,
							        sc.comp_fecha,
							        acesp.centro_gestor,
							        acesp.centro_costo,
							        sc.esta_id,
							        sc.comp_monto_solicitado														
							
							UNION
							
							SELECT DISTINCT sci.comp_id,
							                TO_CHAR(sc.comp_fecha, 'DD/MM/YYYY') AS comp_fecha,
							                COALESCE(pesp.centro_gestor, ' ') AS centrogestor,
							                COALESCE(pesp.centro_costo, ' ') AS centrocosto,
							                sc.esta_id ,
							                sc.comp_monto_solicitado,
											SUM(sopg.sopg_monto) AS causado
											--sopg.sopg_monto AS causado
							FROM sai_comp_imputa sci
							INNER JOIN sai_comp sc ON (sci.comp_id = sc.comp_id)
							INNER JOIN sai_proy_a_esp pesp ON (sci.comp_acc_pp=pesp.proy_id
							                                     AND sci.comp_acc_esp=pesp.paes_id
											AND sci.pres_anno = pesp.pres_anno
											)
							INNER JOIN sai_proyecto proy ON(pesp.proy_id = proy.proy_id)
							INNER JOIN sai_sol_pago sopg ON (sopg.comp_id = sc.comp_id)
							INNER JOIN sai_causado caus ON (sopg.sopg_id = caus.caus_docu_id)
							WHERE sc.esta_id != 2
							  AND sc.esta_id != 15
							 AND sopg.esta_id != 15  
							  AND sc.comp_id NOT IN (SELECT nro_compromiso FROM sai_codi WHERE esta_id != 15 AND esta_id !=2 AND comp_id LIKE '%".$ano2."')
							  AND sc.comp_fecha  BETWEEN to_date('".$fecha_ini2."', 'YYYY-MM-DD') AND to_date('".$fecha_fin2."', 'YYYY-MM-DD')
							-- NO TIENE PGCH NI TRANS
							AND (
								sopg.sopg_id NOT IN (SELECT docg_id 
										FROM sai_pago_cheque
										WHERE pgch_id LIKE '%".$ano2."'
										)
								OR
								sopg.sopg_id IN (SELECT docg_id 
										FROM sai_pago_cheque
										WHERE pgch_id LIKE '%".$ano2."'
											AND pgch_id NOT IN (SELECT paga_docu_id FROM sai_pagado WHERE paga_docu_id LIKE 'pgch-%' AND paga_docu_id LIKE '%".$ano2."' AND esta_id = 1)
										)
							/*	OR
								sopg.sopg_id IN (SELECT docg_id 
										FROM sai_pago_cheque
										WHERE pgch_id LIKE '%".$ano2."'
											AND pgch_id IN (SELECT paga_docu_id FROM sai_pagado WHERE paga_docu_id LIKE 'pgch-%' AND paga_docu_id LIKE '%".$ano2."' AND esta_id != 1)
										)*/
							)
							AND (
								sopg.sopg_id NOT IN (SELECT docg_id 
										FROM sai_pago_transferencia
										WHERE trans_id LIKE '%".$ano2."'
										)
								OR
								sopg.sopg_id IN (SELECT docg_id 
										FROM sai_pago_transferencia
										WHERE trans_id LIKE '%".$ano2."'
											AND trans_id NOT IN (SELECT paga_docu_id FROM sai_pagado WHERE paga_docu_id LIKE 'tran-%' AND paga_docu_id LIKE '%".$ano2."' AND esta_id = 1 )
										)
								/* OR
								sopg.sopg_id IN (SELECT docg_id 
										FROM sai_pago_transferencia
										WHERE trans_id LIKE 'tran-%14'
											AND trans_id IN (SELECT paga_docu_id FROM sai_pagado WHERE paga_docu_id LIKE 'tran-%' AND paga_docu_id LIKE '%".$ano2."' AND esta_id != 1 )
										)		*/
							)

							GROUP BY sopg.comp_id, 
									sci.comp_id,
							        sc.comp_fecha,
							        pesp.centro_gestor,
							        pesp.centro_costo,
							        sc.esta_id,
							        sc.comp_monto_solicitado													
							
							";
				}



				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
					throw new Exception("Error variacion . Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				}

				$paramsLlnar = array();

				while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
				{
					$params[$row['comp_id']] = $row;

				}
			}

			return $params;

		} catch (Exception $e) {
			error_log($e, 0);
			return false;
		}

	}



	public static function GetSaiCodiIdComp($idDocumento)
	{
		$compromiso = null;

		try {

			if($idDocumento == null || ($idDocumento=trim($idDocumento)) == '')
			throw new Exception("Error al obtener sai_codi dado  el id de un documento. Detalles: ".
					"el parámetro idDocumento es vacío o nulo");

			$query = "
				SELECT 
                   count(nro_compromiso) as numero

				FROM
    				sai_codi

				WHERE
    				esta_id<>15 AND
  					  nro_compromiso like lower('".$idDocumento."')";
			
		//	error_log(print_r($query,true));

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener sai_codi  dado el id de un documento. Detalles: ".
			utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){

				$numero = 	$row['numero'];
					
					
				return $numero;
					
			}else{
					
				return 0;

					
			}



		} catch (Exception $e) {
			error_log($e, 0);
		}


		return $numero;
	}



	public static function	AnularComp($compId,$memo){

		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();

			if($result === true){

				$fechatraza = strftime('%Y-%m-%d %H:%M:%S');
					
				$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($compId);
				$entidadDocg->SetIdEstatus(15);
				$result = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
				if($result === false) throw new Exception('Error al insertar. Detalles docg : ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					
					
				$query = "UPDATE
				       sai_comp  
				     
				   SET 
	                esta_id =  '15'
	                 
				   WHERE	
				   
					comp_id='".$compId."'";
					
				$result = $GLOBALS['SafiClassDb']->Query($query);
				if($result === false) throw new Exception('Error al modificar. Detalles sai _comp: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

				$params = 	self::GetCompromisoBasico($compId);
				$params2 =array();

				$params2['comp_id'] =  $params['comp_id'];
				$params2['asuntoVal'] =  $params['comp_asunto'];
				$params2['compromiso_descripcionVal'] =  $params['comp_descripcion'];
				$params2['fecha'] =  $params['fecha'];
				$params2['estatus'] =  '15';
				$params2['DependenciaTramita'] =  $params['depe_id'];
				$params2['observaciones'] =  $params['comp_observacion'];
				$params2['montoTotal'] =  $params['comp_monto_solicitado'];
				$params2['unidadDependencia'] =  $params['comp_gerencia'];
				$params2['esta'] =  $params['comp_estatus'];
				$params2['compAsociado'] =  $params['pcta_id'];
				$params2['ProveedorSugeridoval'] =  $params['rif_sugerido'];

					
				$result =   self::InsertCompromisoTraza($params2,$fechatraza);
					
				if($result === false) throw new Exception('Error al insertar. Detalles punto de cuenta traza: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

				$imputa = SafiModeloCompromisoImputa::GetCompImputaAnuladas($compId);
					
				if($imputa){
					$params3=  $params2;
					$params3['imputa'] = $imputa;


					$result = SafiModeloCompromisoImputa::InsertCompromisoImputaTraza($params3,$fechatraza);
					if($result === false) throw new Exception('Error al insertar. Detalles imputa traza: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());


					$imputa = SafiModeloCompromisoImputa::AnularcomTrazaReporte($compId);
					$params3['imputa'] = $imputa;


					$result = SafiModeloCompromisoImputa:: InsertCompromisoTrazaReporte($params3,$fechatraza);
					if($result === false) throw new Exception('Error al insertar. Detalles imputa traza reporte: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

				}


				$param['idPcta'] = $compId;
				$param['estaid'] = 15;
				$param['observacion'] = $memo;
				$param['perfil'] = $_SESSION['user_perfil_id'];
				$param['opcion'] = 0;

				$result =	SafiModeloObservacionesDoc::InsertarObservacionesDoc($param);
				if($result === false) throw new Exception('Error al insertar. Detalles  observaciones doc: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());


				$result = $GLOBALS['SafiClassDb']->CommitTransaction();

				return true;

			} else {
				throw new Exception('Error al iniciar la transacci&oacute;n');
			}
		}catch(Exception $e){
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}

	}



	public static function	ReintegroTotal($compId,$memo){

		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();

			if($result === true){

				$fechatraza = strftime('%Y-%m-%d %H:%M:%S');
					
				$params = 	self::GetCompromisoBasico($compId);
				$params2 =array();

				$params2['comp_id'] =  $params['comp_id'];
				$params2['asuntoVal'] =  $params['comp_asunto'];
				$params2['compromiso_descripcionVal'] =  $params['comp_descripcion'];
				$params2['fecha'] =  $params['fecha'];
				$params2['estatus'] =  $params['esta_id'];
				$params2['DependenciaTramita'] =  $params['depe_id'];
				$params2['observaciones'] =  $params['comp_observacion'];
				$params2['montoTotal'] =  $params['comp_monto_solicitado'];
				$params2['unidadDependencia'] =  $params['comp_gerencia'];
				$params2['esta'] =  $params['comp_estatus'];
				$params2['compAsociado'] =  $params['pcta_id'];
				$params2['ProveedorSugeridoval'] =  $params['rif_sugerido'];

				$result =   self::InsertCompromisoTraza($params2,$fechatraza);
				if($result === false) throw new Exception('Error al insertar. Detalles punto de cuenta traza: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

					
				
				
				$imputa = SafiModeloCompromisoImputa::GetCompImputas($compId);
				$imputaComprometido = SafiModeloGeneral::GetMontoComprometido($compId);
				$imputaAnuladas = SafiModeloCompromisoImputa::GetCompImputaAnuladas($compId);

				if($imputa){

					$params3 = $params2;
					$params3['imputa'] = $imputa;

					$params4 = $params2;
					$params4['imputa'] = $imputaAnuladas;

					$result = SafiModeloCompromisoImputa:: InsertCompromisoImputaTraza($params4,$fechatraza);
					if($result === false) throw new Exception('Error al insertar. Detalles imputa traza: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());


					foreach ($imputa['codPartida'] as $index => $valor ){
							
						$montoBase = $imputa['monto'][$index];

						if($montoUsado = $imputaComprometido[$valor]){

							$montototal = ($montoBase - $montoUsado);

							if($montototal == 0) {
									

								unset($params3['imputa']['codPartida'][$index]);
								unset($params3['imputa']['codProyAccEsp'][$index]);
								unset($params3['imputa']['codProyAcc'][$index]);
								unset($params3['imputa']['tipo'][$index]);
								unset($params3['imputa']['monto'][$index]);

									
							}else if($montototal  < 0 ){

								throw new Exception('Error el monto de la partida ('.$valor.') en el compromiso('.$compId.') es  menor('.$montoBase.') a el monto que se le desea restar('.$montoUsado.')' . $GLOBALS['SafiClassDb']->GetErrorMsg());

							}else{

								$params3['imputa']['monto'][$index] = ($montototal * -1);

							}


						}else{

							$params3['imputa']['monto'][$index] = ($montoBase * -1);

						}
					}

					if($params3['imputa']['codPartida']){
						$result = SafiModeloCompromisoImputa:: InsertCompromisoTrazaReporte($params3,$fechatraza);
						if($result === false) throw new Exception('Error al insertar. Detalles imputa traza reporte: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

					}

				}
				$param['idPcta'] = $compId;
				$param['estaid'] = 10;
				$param['observacion'] = $memo;
				$param['perfil'] = $_SESSION['user_perfil_id'];
				$param['opcion'] = '1';
				
					$query = " UPDATE
				         	     sai_comp_imputa
				         	         
				            SET 
				                 comp_monto = 0
	                                    
                            WHERE 
	                             comp_id= '".trim($compId)."'";

		        	//error_log(print_r($query,true));
					
		            $result = $GLOBALS['SafiClassDb']->Query($query);
		            
				if($result === false) throw new Exception('Error al modificar el monto imputa. Detalles imputa: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					
					

				$result =	SafiModeloObservacionesDoc::InsertarObservacionesDoc($param);
				if($result === false) throw new Exception('Error al insertar. Detalles  observaciones doc: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

				$result = $GLOBALS['SafiClassDb']->CommitTransaction();

				return true;

			} else {
				throw new Exception('Error al iniciar la transacci&oacute;n');
			}
		}catch(Exception $e){
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}

	}


	private static function LlenarCompromiso($paramsLlnar)
	{
		$compromiso = new EntidadCompromiso();


		$compromiso->SetId($paramsLlnar ['comp_id']);
		$compromiso->SetIdDocumento($paramsLlnar ['comp_documento']);
		$compromiso->SetTipoDoc($paramsLlnar ['comp_tipo_doc']);
		$compromiso->SetDescripcion($paramsLlnar ['comp_descripcion']);
		$compromiso->SetFecha($paramsLlnar['comp_fecha']);
		$compromiso->SetObservacion($paramsLlnar['comp_observacion']);
		$compromiso->SetJustificacion($paramsLlnar['comp_justificacion']);
		$compromiso->SetLapso($paramsLlnar['comp_lapso']);
		$compromiso->SetCondicionPago($paramsLlnar['comp_cond_pago']);
		$compromiso->SetMontoSolicitado($paramsLlnar['comp_monto_solicitado']);
		$compromiso->SetPrioridad($paramsLlnar['comp_prioridad']);
		$compromiso->SetNumeroReserva($paramsLlnar['numero_reserva']);
		$compromiso->SetRecursos($paramsLlnar['recursos']);
		$compromiso->SetCompEstatus($paramsLlnar['comp_estatus']);
		$compromiso->SetPcta($paramsLlnar['pcta_id']);
		$compromiso->SetRifProveedorSugerido($paramsLlnar['rif_sugerido']);
		$compromiso->SetFechaReporte($paramsLlnar['fecha_reporte']);
		$compromiso->SetBeneficiario($paramsLlnar['beneficiario']);
		$compromiso->SetFechaInicio($paramsLlnar['fecha_inicio']);
		$compromiso->SetFechaFin($paramsLlnar['fecha_fin']);
		$compromiso->SetControlInterno($paramsLlnar['control_interno']);
		$compromiso->SetParticipante($paramsLlnar['comp_participantes']);
		$compromiso->SetAsunto($paramsLlnar['comp_asunto']);
		$compromiso->SetEstatus($paramsLlnar['esta_id']);
		$compromiso->SetUsuario($paramsLlnar['usua_login']);
		$compromiso->SetDependencia($paramsLlnar['depe_id']);
		$compromiso->SetGerencia($paramsLlnar['comp_gerencia']);
		$compromiso->SetCompDependencia($paramsLlnar['comp_depe']);
		//	$compromiso->SetActividad($paramsLlnar['id_actividad']);
	//	$compromiso->SetLocalidad($paramsLlnar['localidad']);
		//$compromiso->SetEvento($paramsLlnar['id_evento']);
		//		$compromiso->SetInfocentro($paramsLlnar['sai_infocentro']);
		$compromiso->SetCompromisoImputas($paramsLlnar['compromisoImputa']);

		return $compromiso;
	}


	public static function	CambiarTraza($params){
		
					//	error_log(print_r("CambiarTraza",true));
		

		if($params === null)
		throw new Exception("El parámetro \"params\" es nulo.");
		if(!is_array($params))
		throw new Exception("El parámetro \"params\" no es un arreglo.");
		if(count($params) == 0)
		throw new Exception("El parámetro \"params\" está vacío.");
			
		if($params['fechanueva']){
			$fecha = explode ('/',$params['fechanueva']);
			$fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' '.strftime('%H:%M:%S');
		}

		if($params['fechaActual']){
				
				
			$fecha3 = explode(" ",$params['fechaActual']);

			$fecha4 = explode ('/',$fecha3[0]);

			$fecha5  =  $fecha4[2].'-'.$fecha4[1].'-'.$fecha4[0].' '.$fecha3[1];
				
		}


		$query = " UPDATE
				         	     sai_comp_traza
				         	         
				            SET 
				                 comp_fecha2 = '".$fecha2."'
	                                    
                            WHERE 
	                             comp_id= '".trim($params['Comp'])."' AND
	                             comp_fecha2 = '".$fecha5."'";


		$result1 = $GLOBALS['SafiClassDb']->Query($query);
			

		$query = " UPDATE
				         	     sai_comp_imputa_traza
				         	         
				            SET 
				                 comp_fecha = '".$fecha2."'
	                                    
                            WHERE 
	                             comp_id= '".trim($params['Comp'])."' AND
	                             comp_fecha = '".$fecha5."'";

		$result2 = $GLOBALS['SafiClassDb']->Query($query);

		$query = " UPDATE
				         	     sai_comp_traza_reporte
				         	         
				            SET 
				                 comp_fecha = '".$fecha2."'
	                                    
                            WHERE 
	                             comp_id= '".trim($params['Comp'])."' AND
	                             comp_fecha = '".$fecha5."'";


		$result3 = $GLOBALS['SafiClassDb']->Query($query);
			
			
		$arrays = array(
			
			'comp_id' => trim($params['Comp']),
			'fecha_anterior' =>  $fecha5,
			'fecha_nueva' =>  $fecha2
			
		);
			
		$result4 = self::InsertCambiarTrazaCompromiso($arrays);


		if($result1 !== false || $result2 !== false || $result3 !== false  ){

			return true;

		}else{

			return false;



		}

		return true;

	}
	
	
	
	
    public static function	CambiarTraza2($params){

		if($params === null)
		throw new Exception("El parámetro \"params\" es nulo.");
		if(!is_array($params))
		throw new Exception("El parámetro \"params\" no es un arreglo.");
		if(count($params) == 0)
		throw new Exception("El parámetro \"params\" está vacío.");
		
		
		
		$query = "  SELECT 
				         	
				         	 ".$params['datostabla']['campo']." as fecha,
				         	  EXTRACT(EPOCH FROM TIMESTAMP '".$params['datostabla']['fechaIni']."' ) AS num
				    FROM
				            
	                          ".$params['datostabla']['tabla']."
                    WHERE 
	                       comp_id= '".trim($params['Comp'])."' AND 
	                       ".$params['datostabla']['campo']." > '".$params['datostabla']['fechaIni']."' 
	                 GROUP BY  ".$params['datostabla']['campo']."          
	                ORDER BY ".$params['datostabla']['campo']." ASC";
				
                $result = $GLOBALS['SafiClassDb']->Query($query);

			//	error_log(print_r($query,true));
              
					
              while($row = $GLOBALS['SafiClassDb']->Fetch($result)){

	     	  $params2[] = $row;
		  
              }
			  		
           // error_log(print_r($params2,true));	
			 
              
			  	
			  	$i = 1;
			  	
			  	
			if($params2){
				
				foreach ($params2 as $index => $row){
			  		
			  	
     $tablas = '';
            
		$query = " UPDATE
				         	     sai_comp_traza
				         	         
				            SET 
				                 comp_fecha2 = TO_TIMESTAMP((SELECT TIMESTAMP WITH TIME ZONE 'epoch' + ".($row['num'] + $i)." * INTERVAL '1 second'), 'YYYY-MM-DD HH24:MI:SS')
	                                    
                            WHERE 
	                             comp_id= '".trim($params['Comp'])."' AND
	                             comp_fecha2 = '".$row['fecha']."'";
		
		    //   error_log(print_r($query,true));	
                     
   
	   $result = $GLOBALS['SafiClassDb']->Query($query);
		
		if($result === false){ throw new Exception('Error al modificar. Detalles comp  traza : ' . $GLOBALS['SafiClassDb']->GetErrorMsg());}else{
		
		
			$tablas .=  ' sai_comp_traza' ;
		}
		
		
		
		$query = " UPDATE
				         	     sai_comp_imputa_traza
				         	         
				            SET 
				                 comp_fecha = TO_TIMESTAMP((SELECT TIMESTAMP WITH TIME ZONE 'epoch' + ".($row['num'] + $i)." * INTERVAL '1 second'), 'YYYY-MM-DD HH24:MI:SS')
	                                    
                            WHERE 
	                             comp_id= '".trim($params['Comp'])."' AND
	                             comp_fecha = '".$row['fecha']."'";

	  	$result = $GLOBALS['SafiClassDb']->Query($query);
		
		 if($result === false){ throw new Exception('Error al modificar. Detalles imputa traza : ' . $GLOBALS['SafiClassDb']->GetErrorMsg());}else{
		
			$tablas .=  ' sai_comp_imputa_traza' ;
		}
		
		
		
		
		
		$query = " UPDATE
				         	     sai_comp_traza_reporte
				         	         
				            SET 
				                 comp_fecha = TO_TIMESTAMP((SELECT TIMESTAMP WITH TIME ZONE 'epoch' + ".($row['num'] + $i)." * INTERVAL '1 second'), 'YYYY-MM-DD HH24:MI:SS')
	                                    
                            WHERE 
	                             comp_id= '".trim($params['Comp'])."' AND
	                             comp_fecha = '".$row['fecha']."'";
		
		
  	$result = $GLOBALS['SafiClassDb']->Query($query);
		
		if($result === false){ throw new Exception('Error al modificar. Detalles imputa traza reporte: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());}else{
		
			$tablas .=  ' sai_comp_traza_reporte' ;
		}
		
        
        
        
        
	    

			  		
			$query= "SELECT (TIMESTAMP WITH TIME ZONE 'epoch' + ".($row['num'] + $i)." * INTERVAL '1 second') as fecha";
		

             $result = $GLOBALS['SafiClassDb']->Query($query);	
		
            if ($resultado = $GLOBALS['SafiClassDb']->Fetch($result)){
            	
            $arrays[$i]['comp_id'] = trim($params['Comp']);
            $arrays[$i]['tablasafectadas'] = $tablas;
            $arrays[$i]['memo'] = $params['memo'];
			$arrays[$i]['fecha_anterior'] =  $row['fecha'];
			$arrays[$i]['fecha_nueva'] =  $resultado['fecha'];
			
            	
            }
					
		 $i++;
 		
					
				}
				
				
	//error_log(print_r($arrays,true));	
		
			
		$result4 = self::InsertCambiarTrazaCompromiso($arrays);

		if($result1 !== false || $result2 !== false || $result3 !== false  ){

		return true;

		}else{

			return false;



		}

		return true;
		
		
		//   error_log(print_r($query,true));	

	}
    }
	
  public static function CambiarTrazasPorGrupo($params){

		if($params === null)
		throw new Exception("El parámetro \"params\" es nulo.");
		if(!is_array($params))
		throw new Exception("El parámetro \"params\" no es un arreglo.");
		if(count($params) == 0)
		throw new Exception("El parámetro \"params\" está vacío.");
		
	
		
		
		
		$query = "  SELECT 
				         	
				         	 ".$params['datostabla']['campo']." as fecha,
				         	  EXTRACT(EPOCH FROM TIMESTAMP '".$params['datostabla']['fechaIni']."' ) AS num
				    FROM
				            
	                          ".$params['datostabla']['tabla']."
                    WHERE 
	                       comp_id= '".trim($params['Comp'])."' AND 
	                       ".$params['datostabla']['campo']." > '".$params['datostabla']['fechaIni']."' 
	                 GROUP BY  ".$params['datostabla']['campo']."          
	                ORDER BY ".$params['datostabla']['campo']."  ASC";
				
                $result = $GLOBALS['SafiClassDb']->Query($query);

				error_log(print_r($query,true));
              
					
              while($row = $GLOBALS['SafiClassDb']->Fetch($result)){

	     	  $params2[] = $row;
		  
              }
			  		
			//  	error_log(print_r($params2,true));	
			  	
			 	$i = 1;
			  	
			  	
			  	if($params2){
			  	foreach ($params2 as $index => $row){
			 		
					//  error_log($i ." ". $row['fecha']);	
		$query = " UPDATE
				         	     sai_comp_traza
				         	         
				            SET 
				                 comp_fecha2 = TO_TIMESTAMP((SELECT TIMESTAMP WITH TIME ZONE 'epoch' + ".($row['num'] + $i)." * INTERVAL '1 second'), 'YYYY-MM-DD HH24:MI:SS')
	                                    
                            WHERE 
	                             comp_id= '".trim($params['Comp'])."' AND
	                             comp_fecha2 = '".$row['fecha']."'";

   
	   $result = $GLOBALS['SafiClassDb']->Query($query);
		
		if($result === false) throw new Exception('Error al modificar. Detalles comp  traza : ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
		
		
		
		$query = " UPDATE
				         	     sai_comp_imputa_traza
				         	         
				            SET 
				                 comp_fecha = TO_TIMESTAMP((SELECT TIMESTAMP WITH TIME ZONE 'epoch' + ".($row['num'] + $i)." * INTERVAL '1 second'), 'YYYY-MM-DD HH24:MI:SS')
	                                    
                            WHERE 
	                             comp_id= '".trim($params['Comp'])."' AND
	                             comp_fecha = '".$row['fecha']."'";

	  	$result = $GLOBALS['SafiClassDb']->Query($query);
		
		 if($result === false) throw new Exception('Error al modificar. Detalles imputa traza : ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
		
		
		
		$query = " UPDATE
				         	     sai_comp_traza_reporte
				         	         
				            SET 
				                 comp_fecha = TO_TIMESTAMP((SELECT TIMESTAMP WITH TIME ZONE 'epoch' + ".($row['num'] + $i)." * INTERVAL '1 second'), 'YYYY-MM-DD HH24:MI:SS')
	                                    
                            WHERE 
	                             comp_id= '".trim($params['Comp'])."' AND
	                             comp_fecha = '".$row['fecha']."'";
		
		
   	$result = $GLOBALS['SafiClassDb']->Query($query);
		
		if($result === false) throw new Exception('Error al modificar. Detalles imputa traza reporte: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
		
					
		 $i++;
 		
					
				}
				
				
				}

			
		$arrays = array(
			
			'comp_id' => trim($params['Comp']),
			'fecha_anterior' =>  $fecha5,
			'fecha_nueva' =>  $fecha2
			
		);
			
		$result4 = self::InsertCambiarTrazaCompromiso($arrays);


		if($result1 !== false || $result2 !== false || $result3 !== false  ){

			return true;

		}else{

			return false;



		}

		return true;
		
		
		

	}
	
	public static function	GetUltimaTraza($params){
	
		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
	
			if($result === true){
	
				$query = "
               SELECT
                    TO_CHAR(MAX(tabla.fmax), 'DD/MM/YYYY HH24:MI:SS') as max
           
               FROM  (
                     SELECT
				         	MAX(comp_fecha2) as fmax
	
				    FROM
				            sai_comp_traza
	             
                    WHERE
	                       comp_id= '".trim($params['Comp'])."'
	
                                  UNION
	
	                SELECT
				         	MAX(comp_fecha) as fmax
	
				    FROM
				            sai_comp_imputa_traza
	             
                    WHERE
	                         comp_id= '".trim($params['Comp'])."'
	
                                  UNION
  
                    SELECT
				         	MAX(comp_fecha) as fmax
	
				   FROM
				           sai_comp_traza_reporte
	             
                  WHERE
	                       comp_id='".trim($params['Comp'])."'
	
	             )AS tabla";
	
				$result = $GLOBALS['SafiClassDb']->Query($query);
					
				if($result === false) throw new Exception('Error al consultar fechas. Detalles sai _comp: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					
				if($row = $GLOBALS['SafiClassDb']->Fetch($result)){
	
					$result2 =  $row['max'];
	
				}
	
				$result = $GLOBALS['SafiClassDb']->CommitTransaction();
	
				return $result2;
	
	
	
			} else {
				throw new Exception('Error al iniciar la transacci&oacute;n');
			}
		}catch(Exception $e){
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}
	
	}
	
public static function	GetTablaCampoTraza($params){

		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();

			if($result === true){

				$query = "
			               SELECT CASE
			           WHEN count(comp_fecha2) <
			                  (SELECT CASE
			                              WHEN count(comp_fecha) <
			                                     (SELECT count(comp_fecha)
			                                      FROM sai_comp_traza_reporte
			                                      WHERE comp_id= '".trim($params['Comp'])."'
			                                      GROUP BY comp_id) THEN
			                                     (SELECT count(comp_fecha)
			                                      FROM sai_comp_traza_reporte
			                                      WHERE comp_id= '".trim($params['Comp'])."'
			                                      GROUP BY comp_id)
			                              ELSE (count(comp_fecha))
			                          END AS numero
			                   FROM sai_comp_imputa_traza
			                   WHERE comp_id= '".trim($params['Comp'])."') THEN
			                  (SELECT CASE
			                              WHEN count(comp_fecha) <
			                                     (SELECT count(comp_fecha)
			                                      FROM sai_comp_traza_reporte
			                                      WHERE comp_id= '".trim($params['Comp'])."'
			                                      GROUP BY comp_id) THEN ('sai_comp_traza_reporte')
			                              ELSE ('sai_comp_imputa_traza')
			                          END AS tabla
			                   FROM sai_comp_imputa_traza
			                   WHERE comp_id= '".trim($params['Comp'])."')
			           ELSE ('sai_comp_traza')
			       END AS tabla,
			       CASE
			           WHEN count(comp_fecha2) <
			                  (SELECT CASE
			                              WHEN count(comp_fecha) <
			                                     (SELECT count(comp_fecha)
			                                      FROM sai_comp_traza_reporte
			                                      WHERE comp_id= '".trim($params['Comp'])."'
			                                      GROUP BY comp_id) THEN
			                                     (SELECT count(comp_fecha)
			                                      FROM sai_comp_traza_reporte
			                                      WHERE comp_id='".trim($params['Comp'])."'
			                                      GROUP BY comp_id)
			                              ELSE (count(comp_fecha))
			                          END AS numero
			                   FROM sai_comp_imputa_traza
			                   WHERE comp_id= '".trim($params['Comp'])."') THEN ('comp_fecha')
			           ELSE ('comp_fecha2')
			       END AS campo
			FROM sai_comp_traza
			WHERE comp_id= '".trim($params['Comp'])."'";


				$result = $GLOBALS['SafiClassDb']->Query($query);
					
								
				if($result === false) throw new Exception('Error al consultar fechas. Detalles sai _comp: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					
				if($row = $GLOBALS['SafiClassDb']->Fetch($result)){
					
					$params2 = array();
					
					$params2['tabla'] = $row['tabla'];
					$params2['campo'] = $row['campo'];
					
				
				
				/////////////////////////////////////////////////////// fecha nueva segun mes////////////
				
				$mes = $params['fechanueva'];
				
				if ($mes < 10) { $mes2 = '0'.$mes;}else{$mes2 = $mes;}
			$query = "
					   SELECT CASE
				           WHEN
				                  ( SELECT max(comp_fecha2)
				                   FROM sai_comp_traza
				                   WHERE comp_id= '".trim($params['Comp'])."'
				                     AND date_part('MONTH', comp_fecha2) = ".$mes.") IS NOT NULL THEN
				                  (SELECT TO_CHAR(max(comp_fecha2), 'YYYY-MM-DD HH24:MI:SS')
				                   FROM sai_comp_traza
				                   WHERE comp_id= '".trim($params['Comp'])."'
				                     AND date_part('MONTH', comp_fecha2) = ".$mes.")
				           ELSE
				                  (SELECT TO_CHAR(
				                                    (SELECT CURRENT_TIMESTAMP), 'YYYY')||'-".$mes2."-01 08:00:00')
				       END AS dato";

				$result = $GLOBALS['SafiClassDb']->Query($query);
					
								
				if($result === false) throw new Exception('Error al consultar fechas. Detalles sai _comp: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					
				if($row = $GLOBALS['SafiClassDb']->Fetch($result)){
					
					$params2['fechaIni'] = $row['dato'];
					
				}
				
				}
				
				////////////////////////////////////////////////       ////////////////////////
				
				//error_log(print_r($params2,true));

				$result = $GLOBALS['SafiClassDb']->CommitTransaction();

				return $params2;

			} else {
				throw new Exception('Error al iniciar la transacci&oacute;n');
			}
		}catch(Exception $e){
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}

	}
	
	public static  function InsertCambiarTrazaCompromiso($params){

		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){
				
				if($params){
						
						
					//	error_log(print_r($params,true));
						
					foreach ($params as $index => $row){
					
				$preMsg = "error al insertar . cambio traza del compromiso.";
					
				$query = "
	                                   INSERT INTO comp_cambio_traza
	                                   (comp_id, 
										comp_tabla,
	                                    memo, 
	                                    comp_fecha,
	                                    usua_login,
	                                    comp_fecha_anterior,
                                         comp_fecha_nueva
	                                    )
	                                    
										VALUES (
											
										'".$row['comp_id']."',
										'".$row['tablasafectadas']."',
										'".$row['memo']."',
										'".strftime('%Y-%m-%d %H:%M:%S')."',
							            '".$_SESSION['login']."',
							            '".$row['fecha_anterior']."',
							            '".$row['fecha_nueva']."')";


			//	error_log(print_r($query,true));

				$result = $GLOBALS['SafiClassDb']->Query($query);
				
					}
				
				}

				if($result === false) throw new Exception('Error al insertar. cambio traza: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());


				$result = $GLOBALS['SafiClassDb']->CommitTransaction();

				return true;

			} else {
				throw new Exception('Error al iniciar la transacci&oacute;n');
			}
		}catch(Exception $e){
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}

	}



	public static function GetCompromisosFiltro($comp, $ff = false, $compArray = false, $yearPresupuestario = null ,$ti = null)
	{
		
		try
		{
			if ($yearPresupuestario == null)
				throw new Exception('El parámetro $yearPresupuestario es nulo');
			
			if ($yearPresupuestario == "")
				throw new Exception('El parámetro $yearPresupuestario está vacío');
			
			$params2 = array();

			$query = "
				SELECT
					sc.comp_fecha as comp_fecha,
					sc.comp_id as comp_id,
					sc.rif_sugerido as rif_sugerido,
					ff.fuente_financiamiento,
					sci.comp_tipo_impu
				FROM
					sai_comp sc
					INNER JOIN  sai_comp_imputa sci ON (sc.comp_id = sci.comp_id)
					INNER JOIN  sai_forma_1125 ff ON (ff.form_id_p_ac = sci.comp_acc_pp AND ff.form_id_aesp = sci.comp_acc_esp )
				WHERE
					sc.comp_id LIKE '%".trim($comp)."%'
					AND sc.comp_id  LIKE '%".substr($yearPresupuestario,2,3)."'
					AND ff.fuente_financiamiento <> ''
			";
			
	      	if($compArray){
				$query .= "
					AND sc.comp_id NOT IN ('".implode("' , '", $compArray)."')
				";
			}

			if($ff){
				$query .= "
					AND ff.fuente_financiamiento = '".$ff."'
				";
			}
			
			if($ti){
				$query .= "
					AND sci.comp_tipo_impu = '".$ti."'
				";
			}
			
			$query .= "
				ORDER BY
					sc.comp_id
					limit 15
			";
			
// error_log(print_r($query,true));
				
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
				throw new Exception("Error comp . Detalles: ".
				utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			}
				
			

			while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$params2[$row['comp_id']]['tipo'] =($row['comp_tipo_impu']);
				$params2[$row['comp_id']]['fuente'] =($row['fuente_financiamiento']);

				$cadena = $row['rif_sugerido'];

				$caracter = "~";

				$fecha = explode ('/',$row['comp_fecha']);

				$fecha_cambio = strtotime("22-04-2013");
				$fecha_entrada  = strtotime($fecha[0]."-".$fecha[1]."-".$fecha[2]);


				if($fecha_entrada < $fecha_cambio){

				$proveedorSugerido = $row['rif_sugerido'] != null ? SafiModeloEmpleado::GetProveedoresSugerido(array($row['rif_sugerido'] => $row['rif_sugerido'])) : null;

				if($proveedorSugerido){
				$params2[$row['comp_id']]['proveedor']  = $proveedorSugerido[$row['rif_sugerido']]['id'].":".$proveedorSugerido[$row['rif_sugerido']]['nombre'];
		
				}else{
				
					$params2[$row['comp_id']]['proveedor'] =($row['rif_sugerido']);
					
				}

				
				}else if (strpos($cadena, $caracter) !== false){
					
				$val1 = explode ('~',$row['rif_sugerido']);
					
				$row['rif_sugerido'] = $val1[1];

				$params2[$row['comp_id']]['proveedor'] =($row['rif_sugerido']);
					
				}else{
					$proveedorSugerido = $row['rif_sugerido'] != null ?  SafiModeloEmpleado::GetProveedoresSugerido(array($row['rif_sugerido'] => $row['rif_sugerido'])) : null;
					$params2[$row['comp_id']]['proveedor']  = $proveedorSugerido[$row['rif_sugerido']]['id'];


				}

			}


		//    error_log(print_r($params2,true));
			
			
			return $params2;

			 

							 
			


		} catch (Exception $e) {
			error_log($e, 0);
			return false;
		}

	}


}




