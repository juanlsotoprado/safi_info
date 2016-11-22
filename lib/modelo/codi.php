<?php
require_once(SAFI_ENTIDADES_PATH."/codi.php");
include_once(SAFI_ENTIDADES_PATH . '/empleado.php');
require_once(SAFI_MODELO_PATH. '/docgenera.php');


class SafiModeloCodi
{
	public static function GetBusqueda(array $params = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener el comprobante manual.";
			$where = "";
			$existeCriterio = false;
			$arrMsg = array();
			$codi = array();
			
			
			if($params === null)
				throw new Exception($preMsg. " El parámatro \"params\" es nulo.");
			if(!is_array($params))
				throw new Exception($preMsg. " El parámatro \"params\" no es un arreglo.");
				
			$empleado = new EntidadEmpleado();
			$empleado = $params['empleado'];
			
			
				$condicion_analista="";
				$condicion_cuenta="";
				$condicion_justificacion = "";
				$condicion_referencia = "";
				$condicion_documento = "";
				$condicion_compromiso = "";
				$condicion_emision = "";
				$condicion_elaboracion = "";
				$condicion_codi = "";
			
			if ($params['empleado'] != '')
				$condicion_analista=" AND sdg.usua_login='".$params['empleado']->GetId()."' ";

			if ($params['compromiso'] != '')
				$condicion_compromiso=" AND sc.nro_compromiso='".$params['compromiso']."' ";			
			
			if ($params['cuentaContable'] != '')
				$condicion_cuenta=" AND rc1.cpat_id='".$params['cuentaContable']."' ";
			
			if ($params['justificacion'] != '')
				$condicion_justificacion = " AND LOWER(sc.comp_comen) LIKE  LOWER('%".$params['justificacion']."%') ";

			if ($params['referenciaBancaria'] != '')
				$condicion_referencia = "  AND trim(sc.nro_referencia) like '%".$params['referenciaBancaria']."%'";
				
			if ($params['documentoAsociado'] != '')
				$condicion_documento = "  AND LOWER(trim(sc.comp_doc_id)) LIKE LOWER('%".$params['documentoAsociado']."%')";
				
			if ($params['fecha_inicio'] != '' && $params['fecha_fin'] != '')
				$condicion_emision = " AND sc.comp_fec BETWEEN to_date('".$params['fecha_inicio']."', 'DD/MM/YYYY') AND to_date('".$params['fecha_fin']."', 'DD/MM/YYYY') ";

			if ($params['fechae_inicio'] != '' && $params['fechae_fin'] != '')
				$condicion_elaboracion = " AND sc.comp_fec_emis BETWEEN to_date('".$params['fechae_inicio']."', 'DD/MM/YYYY') AND to_date('".$params['fechae_fin']."', 'DD/MM/YYYY')+1 ";
						
			if ($params['idCodi'] != '' && $params['idCodi'] != '')
				$condicion_codi = " AND sc.comp_id = '".$params['idCodi']."'";			

			if ($params['estatusCodi'] != '' && $params['estatusCodi'] != '')
				$condicion_estatus = " AND sc.esta_id = '".$params['estatusCodi']."'";
				

			$query = "
				SELECT
					DISTINCT(sc.comp_id) AS comp_id, 
					TO_CHAR(sc.comp_fec, 'DD/MM/YYYY') AS fecha_emision, 
					UPPER(sc.comp_comen) AS comentario,
					sc.comp_fec_emis, 
					CASE sc.esta_id 
						WHEN 15 THEN 'Anulado' 
							ELSE 'Activo' 
						END AS estado,
					sc.comp_doc_id, 
					sc.nro_referencia, 
					sc.nro_compromiso AS compromiso, 
					UPPER(sem.empl_nombres) ||' '|| UPPER(sem.empl_apellidos) AS usuario, 
					f1.fuente_financiamiento,
					COALESCE(UPPER(semm.empl_nombres),'') ||' '|| COALESCE(UPPER(semm.empl_apellidos),'') ||' '|| COALESCE(UPPER(semmo.empl_nombres),'') ||' '|| COALESCE(UPPER(semmo.empl_apellidos), '') AS memo_responsable, 
					sm.memo_id, 
					UPPER(sm.memo_contenido) AS memo_contenido,
					TO_CHAR(sm.memo_fecha_crea, 'DD/MM/YYYY') AS memo_fecha_crea
				FROM
					sai_codi sc 
					INNER JOIN sai_doc_genera sdg ON (sdg.docg_id = sc.comp_id)
					INNER JOIN sai_empleado sem ON (sdg.usua_login = sem.empl_cedula)
					--INNER JOIN sai_reng_comp rc ON (rc.comp_id = sc.comp_id)
					INNER JOIN (
						SELECT
							DISTINCT(rc1.comp_id),
							max(rc1.pr_ac) AS pr_ac,
							max(rc1.a_esp) AS a_esp
						FROM
							sai_reng_comp rc1 
						WHERE
							rc1.comp_id LIKE 'codi%'
							".$condicion_cuenta."
							/*AND fecha_emis BETWEEN to_date('01/02/2014', 'DD/MM/YYYY') AND to_date('11/03/2014', 'DD/MM/YYYY') esto fue una prueba*/ 
						GROUP BY
							rc1.comp_id
						ORDER BY
							rc1.comp_id
					) rc ON (rc.comp_id=sc.comp_id)
					LEFT OUTER JOIN sai_forma_1125 f1 ON (f1.form_id_p_ac = rc.pr_ac AND f1.form_id_aesp = rc.a_esp)
					LEFT OUTER JOIN sai_docu_sopor sds ON (LOWER(TRIM(sds.doso_doc_fuente)) = LOWER(TRIM(sc.comp_id))) 
					LEFT OUTER JOIN sai_memo sm ON (sm.memo_id=sds.doso_doc_soport)
					LEFT OUTER JOIN sai_empleado semm ON (sm.usua_login = semm.empl_cedula) 
					LEFT OUTER JOIN safi_observaciones_doc od ON (od.id_doc = LOWER(TRIM(sc.comp_id)))
					LEFT OUTER JOIN sai_empleado semmo ON (od.usua_login = semmo.empl_cedula)  
				WHERE TRUE ".
					 $condicion_emision.
					 $condicion_elaboracion.
					 $condicion_codi.
					 $condicion_referencia.
					 $condicion_documento.
					 $condicion_detalle.
					 $condicion_analista.
					 $condicion_estatus.
					 $condicion_compromiso.
					 $condicion_justificacion."
				ORDER BY
					sc.comp_id
				LIMIT
					".$params['tamanoPagina']."
				OFFSET ".$params['desplazamiento']."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$codi[] = self::LlenarCodi($row);
			}
			
			return $codi;
				
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}

	public static function GetBusquedaContador(array $params = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener el comprobante manual.";
			$where = "";
			$existeCriterio = false;
			$arrMsg = array();
			$contador = 0;
				
				
			if($params === null)
				throw new Exception($preMsg. " El parámatro \"params\" es nulo.");
			if(!is_array($params))
				throw new Exception($preMsg. " El parámatro \"params\" no es un arreglo.");
	
			$empleado = new EntidadEmpleado();
			$empleado = $params['empleado'];

				$condicion_analista="";
				$condicion_cuenta="";
				$condicion_justificacion = "";
				$condicion_referencia = "";
				$condicion_documento = "";
				$condicion_compromiso = "";
				$condicion_emision = "";
				$condicion_elaboracion = "";
				$condicion_codi = "";
			
				
			if ($params['empleado'] != '')
				$condicion_analista=" AND d.usua_login='".$empleado->GetId()."' ";
			
			if ($params['cuentaContable'] != '')
				$condicion_cuenta=" AND rc1.cpat_id='".$params['cuentaContable']."' ";

			if ($params['compromiso'] != '')
				$condicion_compromiso=" AND c.nro_compromiso='".$params['compromiso']."' ";
						
			if ($params['justificacion'] != '')
				$condicion_justificacion = " AND lower(c.comp_comen) LIKE '%".$params['justificacion']."%'";

			if ($params['referenciaBancaria'] != '')
				$condicion_referencia = "  AND trim(c.nro_referencia) like '%".$params['referenciaBancaria']."%'";
				
			if ($params['documentoAsociado'] != '')
				$condicion_documento = "  AND trim(c.comp_doc_id) like '%".$params['documentoAsociado']."%'";
				
			if ($params['fecha_inicio'] != '' && $params['fecha_fin'] != '')
				$condicion_emision = " AND c.comp_fec BETWEEN to_date('".$params['fecha_inicio']."', 'DD/MM/YYYY') AND to_date('".$params['fecha_fin']."', 'DD/MM/YYYY') ";

			if ($params['fechae_inicio'] != '' && $params['fechae_fin'] != '')
				$condicion_elaboracion = " AND c.comp_fec_emis BETWEEN to_date('".$params['fechae_inicio']."', 'DD/MM/YYYY') AND to_date('".$params['fechae_fin']."', 'DD/MM/YYYY')+1 ";
			
			if ($params['idCodi'] != '' && $params['idCodi'] != '')
				$condicion_codi = " AND c.comp_id = '".$params['idCodi']."'";	

			if ($params['estatusCodi'] != '' && $params['estatusCodi'] != '')
				$condicion_estatus = " AND sc.esta_id = '".$params['estatusCodi']."'";
						
	
			$query = "
					SELECT
						DISTINCT(c.comp_id) AS contador
					FROM sai_codi c
						INNER JOIN sai_comp_diario cd ON (c.comp_id = cd.comp_id)
						INNER JOIN sai_doc_genera d ON (c.comp_id = d.docg_id)
						INNER JOIN sai_empleado em ON (d.usua_login = em.empl_cedula)
						--INNER JOIN sai_reng_comp src ON (src.comp_id = c.comp_id)

					INNER JOIN (SELECT DISTINCT(rc1.comp_id), max(rc1.pr_ac) AS pr_ac, max(rc1.a_esp) AS a_esp
						FROM sai_reng_comp rc1 
						WHERE rc1.comp_id LIKE 'codi%'".
						$condicion_cuenta.
						"
						/*AND fecha_emis BETWEEN to_date('01/02/2014', 'DD/MM/YYYY') AND to_date('11/03/2014', 'DD/MM/YYYY') esto fue una prueba*/ 
						GROUP BY rc1.comp_id
						
						ORDER BY rc1.comp_id
						
						
					) rc ON (rc.comp_id=c.comp_id) 
													
					WHERE
						 TRUE ".
						 $condicion_emision.
						 $condicion_elaboracion.
						 $condicion_codi.
						 $condicion_referencia.
						 $condicion_documento.
						 $condicion_detalle.
						 $condicion_cuenta.
						 $condicion_analista.
						 $condicion_compromiso.
						 $condicion_justificacion
					;
					//error_log(print_r($query,true));
					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
						throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
							
					if ($row = $GLOBALS['SafiClassDb']->CountResult($result)){
							$contador = $row;
					}
							
						return $contador;
	
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetPDF($params = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener el comprobante manual.";
			$arrMsg = array();
			$listaCodi = array();
	
	
			if($params === null)
				throw new Exception($preMsg. " El parámatro \"params\" es nulo.");
	
			$query = "
					SELECT DISTINCT(sc.comp_id) AS comp_id, 
						sc.comp_fec, 
						UPPER(sc.comp_comen) AS comp_comen,
						sc.comp_fec_emis, 
						sc.esta_id, 
						sc.comp_doc_id, 
						sc.nro_referencia, 
						sc.nro_compromiso, 
						UPPER(sem.empl_nombres) ||' '|| UPPER(sem.empl_apellidos) AS usuario, 
						f1.fuente_financiamiento,
						COALESCE(UPPER(semm.empl_nombres),'') ||' '|| COALESCE(UPPER(semm.empl_apellidos),'') || COALESCE(UPPER(semo.empl_nombres),'') ||' '|| COALESCE(UPPER(semo.empl_apellidos),'')  AS memo_responsable, 
						COALESCE(sm.memo_id,'') || id_observaciones_doc AS memo_id, 
						COALESCE(UPPER(sm.memo_contenido),'') || COALESCE(odoc.observacion,'') AS memo_contenido,
						COALESCE(TO_CHAR(sm.memo_fecha_crea, 'DD/MM/YYYY HH24:MI:SS'),'') || COALESCE(TO_CHAR(odoc.fecha, 'DD/MM/YYYY HH24:MI:SS'),'')  AS memo_fecha_crea  
						 
					FROM sai_codi sc 
					INNER JOIN sai_doc_genera sdg ON (sdg.docg_id = sc.comp_id)
					INNER JOIN sai_empleado sem ON (sdg.usua_login = sem.empl_cedula)
					INNER JOIN sai_reng_comp rc ON (rc.comp_id = sc.comp_id)
					LEFT OUTER JOIN sai_forma_1125 f1 ON (f1.form_id_p_ac = rc.pr_ac AND f1.form_id_aesp = rc.a_esp)
					LEFT OUTER JOIN sai_docu_sopor sds ON (LOWER(TRIM(sds.doso_doc_fuente)) = LOWER(TRIM(sc.comp_id))) 
					LEFT OUTER JOIN sai_memo sm ON (sm.memo_id=sds.doso_doc_soport)
					LEFT OUTER JOIN safi_observaciones_doc odoc ON (odoc.id_doc = sc.comp_id)
					LEFT OUTER JOIN sai_empleado semm ON (sm.usua_login = semm.empl_cedula) 
					LEFT OUTER JOIN sai_empleado semo ON (odoc.usua_login = semo.empl_cedula) 
					WHERE sc.comp_id IN (".$params['codis'].")  
					ORDER BY sc.comp_fec, sc.comp_id";
					//error_log(print_r($query,true));
					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
							 	throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
							 	
					while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
						 $listaCodi[$row['comp_id']]['comp_id'] = $row['comp_id'];
						 $listaCodi[$row['comp_id']]['comp_fec'] = $row['comp_fec'];
						 $listaCodi[$row['comp_id']]['comp_comen'] = $row['comp_comen'];
						 $listaCodi[$row['comp_id']]['comp_fec_emis'] = $row['comp_fec_emis'];
						 $listaCodi[$row['comp_id']]['esta_id'] = $row['esta_id'];
						 $listaCodi[$row['comp_id']]['comp_doc_id'] = $row['comp_doc_id'];
						 $listaCodi[$row['comp_id']]['nro_referencia'] = $row['nro_referencia'];
						 $listaCodi[$row['comp_id']]['nro_compromiso'] = $row['nro_compromiso'];
						 $listaCodi[$row['comp_id']]['usuario'] = $row['usuario'];						 
						 $listaCodi[$row['comp_id']]['fuente_financiamiento'] = $row['fuente_financiamiento'];
						 $listaCodi[$row['comp_id']]['memo_contenido'] = $row['memo_contenido'];
						 $listaCodi[$row['comp_id']]['memo_id'] = $row['memo_id'];						 
						 $listaCodi[$row['comp_id']]['memo_responsable'] = $row['memo_responsable'];
						 $listaCodi[$row['comp_id']]['memo_fecha_crea'] = $row['memo_fecha_crea'];						 						 
					}
							 	
						return $listaCodi;

		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}

	/*public static function GetPDF2($params = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener el comprobante manual.";
			$arrMsg = array();
			$listaCodi = array();
	
	
			if($params === null)
				throw new Exception($preMsg. " El parámatro \"params\" es nulo.");
			
	
			$query = "
					SELECT DISTINCT(sc.comp_id) AS comp_id,
						sc.comp_fec,
						UPPER(sc.comp_comen) AS comp_comen,
						sc.comp_fec_emis,
						sc.esta_id,
						sc.comp_doc_id,
						sc.nro_referencia,
						sc.nro_compromiso,
						UPPER(sem.empl_nombres) ||' '|| UPPER(sem.empl_apellidos) AS usuario,
						f1.fuente_financiamiento,
						COALESCE(UPPER(semm.empl_nombres),'') ||' '|| COALESCE(UPPER(semm.empl_apellidos),'') || COALESCE(UPPER(semo.empl_nombres),'') ||' '|| COALESCE(UPPER(semo.empl_apellidos),'')  AS memo_responsable,
						COALESCE(sm.memo_id,'') || id_observaciones_doc AS memo_id,
						COALESCE(UPPER(sm.memo_contenido),'') || COALESCE(odoc.observacion,'') AS memo_contenido,
						COALESCE(TO_CHAR(sm.memo_fecha_crea, 'DD/MM/YYYY HH24:MI:SS'),'') || COALESCE(TO_CHAR(odoc.fecha, 'DD/MM/YYYY HH24:MI:SS'),'')  AS memo_fecha_crea
				
					FROM sai_codi sc
					INNER JOIN sai_doc_genera sdg ON (sdg.docg_id = sc.comp_id)
					INNER JOIN sai_empleado sem ON (sdg.usua_login = sem.empl_cedula)
					INNER JOIN sai_reng_comp rc ON (rc.comp_id = sc.comp_id)
					LEFT OUTER JOIN sai_forma_1125 f1 ON (f1.form_id_p_ac = rc.pr_ac AND f1.form_id_aesp = rc.a_esp)
					LEFT OUTER JOIN sai_docu_sopor sds ON (LOWER(TRIM(sds.doso_doc_fuente)) = LOWER(TRIM(sc.comp_id)))
					LEFT OUTER JOIN sai_memo sm ON (sm.memo_id=sds.doso_doc_soport)
					LEFT OUTER JOIN safi_observaciones_doc odoc ON (odoc.id_doc = sc.comp_id)
					LEFT OUTER JOIN sai_empleado semm ON (sm.usua_login = semm.empl_cedula)
					LEFT OUTER JOIN sai_empleado semo ON (odoc.usua_login = semo.empl_cedula)
					WHERE sc.comp_id IN (".$params['codis'].")
					ORDER BY sc.comp_fec, sc.comp_id";
	
			//error_log(print_r($query,true));
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$listaCodi[$row['comp_id']]['comp_id'] = $row['comp_id'];
				$listaCodi[$row['comp_id']]['comp_fec'] = $row['comp_fec'];
				$listaCodi[$row['comp_id']]['comp_comen'] = $row['comp_comen'];
				$listaCodi[$row['comp_id']]['comp_fec_emis'] = $row['comp_fec_emis'];
				$listaCodi[$row['comp_id']]['esta_id'] = $row['esta_id'];
				$listaCodi[$row['comp_id']]['comp_doc_id'] = $row['comp_doc_id'];
				$listaCodi[$row['comp_id']]['nro_referencia'] = $row['nro_referencia'];
				$listaCodi[$row['comp_id']]['nro_compromiso'] = $row['nro_compromiso'];
				$listaCodi[$row['comp_id']]['usuario'] = $row['usuario'];
				$listaCodi[$row['comp_id']]['fuente_financiamiento'] = $row['fuente_financiamiento'];
				$listaCodi[$row['comp_id']]['memo_contenido'] = $row['memo_contenido'];
				$listaCodi[$row['comp_id']]['memo_id'] = $row['memo_id'];
				$listaCodi[$row['comp_id']]['memo_responsable'] = $row['memo_responsable'];
				$listaCodi[$row['comp_id']]['memo_fecha_crea'] = $row['memo_fecha_crea'];
			}
				
			return $listaCodi;
	
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}*/

	public static function GetPDFDetalleCodi(array $params = null, $caso)
	{
		try
		{
			$preMsg = "Error al intentar obtener el comprobante manual.";
			$arrMsg = array();
			$listaCodi = array();
	
	
			if($params === null)
				throw new Exception($preMsg. " El parámatro \"params\" es nulo.");
			if(!is_array($params))
				throw new Exception($preMsg. " El parámatro \"params\" no es un arreglo.");
	
			$query = "
				SELECT
					src.comp_id AS comp_id,
					src.reng_comp AS reng_comp,
					src.cpat_id AS cpat_id,
					src.cpat_nombre AS cpat_nombre,
					src.rcomp_debe AS rcomp_debe,
					src.rcomp_haber AS rcomp_haber,
					src.part_id AS part_id,
					src.pr_ac AS pr_ac,
					src.a_esp AS a_esp,
					src.pr_ac_tipo AS pr_ac_tipo,
					p.part_nombre AS part_nombre,
					COALESCE(spae.centro_gestor, '')||COALESCE(sae.centro_gestor, '')||'/'||COALESCE(spae.centro_costo, '')||COALESCE(sae.centro_costo, '') AS centros,
					COALESCE(spae.paes_nombre, '')||COALESCE(sae.aces_nombre, '') AS a_esp_nombre,
					COALESCE(proy.proy_titulo, '')||COALESCE(acc.acce_denom, '') AS p_acc_nombre
				FROM
					sai_reng_comp src
					LEFT OUTER JOIN sai_partida p ON (p.part_id = src.part_id AND src.pres_anno=p.pres_anno)
					LEFT OUTER JOIN sai_proy_a_esp spae ON (spae.paes_id=src.a_esp AND spae.proy_id=src.pr_ac)
					LEFT OUTER JOIN sai_proyecto proy ON (proy.proy_id=spae.proy_id)					
					LEFT OUTER JOIN sai_acce_esp sae ON (sae.aces_id=src.a_esp AND sae.acce_id=src.pr_ac)
					LEFT OUTER JOIN sai_ac_central acc ON (acc.acce_id=sae.acce_id)
				WHERE
					src.comp_id IN (".$params['codis'].")
				ORDER BY src.comp_id,
					src.reng_comp";

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				
				if ($caso=1) {
					$listaCodi[$row['comp_id']][] = array(
							'comp_id' => $row['comp_id'],
							'reng_comp' => $row['reng_comp'],
							'cpat_id' => $row['cpat_id'],
							'cpat_nombre' => $row['cpat_nombre'],
							'rcomp_debe' => $row['rcomp_debe'],
							'rcomp_haber' => $row['rcomp_haber'],
							'pr_ac' => $row['pr_ac'],
							'a_esp' => $row['a_esp'],
							'part_id' => $row['part_id'],
							'pr_ac_tipo' => $row['pr_ac_tipo'],
							'centros' => $row['centros'],
							'a_esp_nombre' => $row['a_esp_nombre'],
							'p_acc_nombre' => $row['p_acc_nombre']
					);
				}
				
				else {
					$listaCodi[$row['comp_id']][$row['cpat_id']]['comp_id'] = $row['comp_id'];
					$listaCodi[$row['comp_id']][$row['cpat_id']]['reng_comp'] = $row['reng_comp'];
					$listaCodi[$row['comp_id']][$row['cpat_id']]['cpat_id'] = $row['cpat_id'];
					$listaCodi[$row['comp_id']][$row['cpat_id']]['cpat_nombre'] = $row['cpat_nombre'];
					$listaCodi[$row['comp_id']][$row['cpat_id']]['rcomp_debe'] = $row['rcomp_debe'];
					$listaCodi[$row['comp_id']][$row['cpat_id']]['rcomp_haber'] = $row['rcomp_haber'];
					$listaCodi[$row['comp_id']][$row['cpat_id']]['part_id'] = $row['part_id'];				
					$listaCodi[$row['comp_id']][$row['cpat_id']]['centros'] = $row['centros'];
				}
			}
			
			return $listaCodi;
	
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	

	/*public static function GetPDFDetallePresupuestoCodi(array $params = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener el comprobante manual.";
			$arrMsg = array();
			$listaCodi = array();
	
	
			if($params === null)
				throw new Exception($preMsg. " El parámatro \"params\" es nulo.");
			if(!is_array($params))
				throw new Exception($preMsg. " El parámatro \"params\" no es un arreglo.");
	
			$query = "
					SELECT
						s.comp_id,
						s.centro_gestor,
						s.centro_costo,
						s.comp_fec,
						s.part_id,
						sc.cpat_id,
						s.cadt_monto
						FROM
						(
								(SELECT
							sc.comp_id,
							sc.comp_fec,
							spae.centro_gestor,
							spae.centro_costo,
							scad.part_id,
							scad.cadt_monto
							FROM sai_causad_det scad, sai_causado sca, sai_codi sc, sai_proyecto sp, sai_proy_a_esp spae
							WHERE
							sca.caus_docu_id IN ('".$params['codis']."') AND
							sca.esta_id<>15 AND
							sca.caus_id = scad.caus_id AND
							sca.caus_id = scad.caus_id AND
							scad.pres_anno = SUBSTR(sc.comp_fec,0,5) AND
							sca.caus_docu_id = sc.comp_id AND
							scad.cadt_tipo = '1' AND
							sp.proy_id = scad.cadt_id_p_ac AND
							sp.pre_anno = sca.pres_anno AND
							spae.proy_id = sp.proy_id AND
							spae.paes_id = scad.cadt_cod_aesp AND
							spae.pres_anno = sca.pres_anno)
					UNION
					(SELECT
							sc.comp_id,
							sc.comp_fec,
							sae.centro_gestor,
							sae.centro_costo,
							scad.part_id,
							scad.cadt_monto
							FROM sai_causad_det scad, sai_causado sca, sai_codi sc, sai_ac_central sac, sai_acce_esp sae
							WHERE
							sca.caus_docu_id IN ('".$params['codis']."') AND
							sca.esta_id<>15 AND
							sca.caus_id = scad.caus_id AND
							sca.caus_id = scad.caus_id AND
							scad.pres_anno = SUBSTR(sc.comp_fec,0,5) AND
							sca.caus_docu_id = sc.comp_id AND
							scad.cadt_tipo = '0' AND
							sac.acce_id = scad.cadt_id_p_ac AND
							sac.pres_anno = sca.pres_anno AND
							sae.acce_id = sac.acce_id AND
							sae.aces_id = scad.cadt_cod_aesp AND
							sae.pres_anno = sca.pres_anno)
			) AS s, sai_convertidor sc
			WHERE
			s.part_id = sc.part_id
			ORDER BY s.comp_fec, s.comp_id ";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$listaCodi[$row['comp_id']][$row['part_id']]['comp_id'] = $row['comp_id'];
				$listaCodi[$row['comp_id']][$row['part_id']]['centro_gestor'] = $row['centro_gestor'];
				$listaCodi[$row['comp_id']][$row['part_id']]['centro_costo'] = $row['centro_costo'];
				$listaCodi[$row['comp_id']][$row['part_id']]['part_id'] = $row['part_id'];
				$listaCodi[$row['comp_id']][$row['part_id']]['cpat_id'] = $row['cpat_id'];				
				$listaCodi[$row['comp_id']][$row['part_id']]['cadt_monto'] = $row['cadt_monto'];
			}
	
			return $listaCodi;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}*/

	public static function GetPDF2DetalleCodi(array $params = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener el comprobante manual.";
			$arrMsg = array();
			$listaCodi = array();
	
	
			if($params === null)
				throw new Exception($preMsg. " El parámatro \"params\" es nulo.");
			if(!is_array($params))
				throw new Exception($preMsg. " El parámatro \"params\" no es un arreglo.");
	
			$query = "
				SELECT
					src.comp_id,
					src.reng_comp,
					src.cpat_id,
					src.cpat_nombre,
					src.rcomp_debe,
					src.rcomp_haber,
					src.rcomp_tot_db,
					src.rcomp_tot_hab
				FROM sai_reng_comp src
				WHERE
				src.comp_id IN (".$params['codis'].")
				ORDER BY src.comp_id,
						src.reng_comp";
				
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$listaCodi[$row['comp_id']][$row['cpat_id']]['comp_id'] = $row['comp_id'];
				$listaCodi[$row['comp_id']][$row['cpat_id']]['reng_comp'] = $row['reng_comp'];
				$listaCodi[$row['comp_id']][$row['cpat_id']]['cpat_id'] = $row['cpat_id'];
				$listaCodi[$row['comp_id']][$row['cpat_id']]['cpat_nombre'] = $row['cpat_nombre'];
				$listaCodi[$row['comp_id']][$row['cpat_id']]['rcomp_debe'] = $row['rcomp_debe'];
				$listaCodi[$row['comp_id']][$row['cpat_id']]['rcomp_haber'] = $row['rcomp_haber'];
				$listaCodi[$row['comp_id']][$row['cpat_id']]['rcomp_tot_db'] = $row['rcomp_tot_db'];
			}
	
			return $listaCodi;
	
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	
	/*public static function GetPDF2DetallePresupuestoCodi(array $params = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener el comprobante manual.";
			$arrMsg = array();
			$listaCodi = array();
	
	
			if($params === null)
				throw new Exception($preMsg. " El parámatro \"params\" es nulo.");
			if(!is_array($params))
				throw new Exception($preMsg. " El parámatro \"params\" no es un arreglo.");
	
			$query = "
					SELECT
						s.comp_id,
						s.centro_gestor,
						s.centro_costo,
						s.comp_fec,
						s.part_id,
						sc.cpat_id,
						s.cadt_monto
						FROM
						(
								(SELECT
							sc.comp_id,
							sc.comp_fec,
							spae.centro_gestor,
							spae.centro_costo,
							scad.part_id,
							scad.cadt_monto
							FROM sai_causad_det scad, sai_causado sca, sai_codi sc, sai_proyecto sp, sai_proy_a_esp spae
							WHERE
							sca.caus_docu_id IN (".$params['codis'].") AND
							sca.esta_id<>15 AND
							sca.caus_id = scad.caus_id AND
							sca.caus_id = scad.caus_id AND
							scad.pres_anno = SUBSTR(sc.comp_fec,0,5) AND
							sca.caus_docu_id = sc.comp_id AND
							scad.cadt_tipo = '1' AND
							sp.proy_id = scad.cadt_id_p_ac AND
							sp.pre_anno = sca.pres_anno AND
							spae.proy_id = sp.proy_id AND
							spae.paes_id = scad.cadt_cod_aesp AND
							spae.pres_anno = sca.pres_anno)
					UNION
					(SELECT
							sc.comp_id,
							sc.comp_fec,
							sae.centro_gestor,
							sae.centro_costo,
							scad.part_id,
							scad.cadt_monto
							FROM sai_causad_det scad, sai_causado sca, sai_codi sc, sai_ac_central sac, sai_acce_esp sae
							WHERE
							sca.caus_docu_id IN (".$params['codis'].") AND
							sca.esta_id<>15 AND
							sca.caus_id = scad.caus_id AND
							sca.caus_id = scad.caus_id AND
							scad.pres_anno = SUBSTR(sc.comp_fec,0,5) AND
							sca.caus_docu_id = sc.comp_id AND
							scad.cadt_tipo = '0' AND
							sac.acce_id = scad.cadt_id_p_ac AND
							sac.pres_anno = sca.pres_anno AND
							sae.acce_id = sac.acce_id AND
							sae.aces_id = scad.cadt_cod_aesp AND
							sae.pres_anno = sca.pres_anno)
			) AS s, sai_convertidor sc
			WHERE
			s.part_id = sc.part_id
			ORDER BY s.comp_fec, s.comp_id ";
				
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$listaCodi[$row['comp_id']][$row['part_id']]['comp_id'] = $row['comp_id'];
				$listaCodi[$row['comp_id']][$row['part_id']]['centro_gestor'] = $row['centro_gestor'];
				$listaCodi[$row['comp_id']][$row['part_id']]['centro_costo'] = $row['centro_costo'];
				$listaCodi[$row['comp_id']][$row['part_id']]['part_id'] = $row['part_id'];
				$listaCodi[$row['comp_id']][$row['part_id']]['cpat_id'] = $row['cpat_id'];
				$listaCodi[$row['comp_id']][$row['part_id']]['cadt_monto'] = $row['cadt_monto'];
			}
	
			return $listaCodi;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}*/
	
	
	
	
	public static function GetSelectFieldsCodi()
	{
		return "
			DISTINCT(c.comp_id) AS comp_id,
			c.comp_doc_id AS comp_doc_id, 	
			c.nro_compromiso AS compromiso,	
			UPPER(c.comp_comen) AS comentario, 
			UPPER(em.empl_nombres)||' '||upper(em.empl_apellidos) AS usuario, 
			TO_CHAR(c.comp_fec, 'DD/MM/YYYY') AS fecha_emision, 
			c.nro_referencia AS nro_referencia, 
			CASE c.esta_id 
				WHEN 15 THEN 'Anulado' 
				ELSE 'Activo' 
				END AS estado
		";
	}
	
	public static function LlenarCodi($row)
	{
		$comprobante = new EntidadCodi();
		
		$comprobante->SetId($row['comp_id']);
		$comprobante->SetDocumentoAsociado($row['comp_doc_id']);
		$comprobante->SetJustificacion($row['comentario']);
		$comprobante->SetNroCompromiso($row['compromiso']);
		$comprobante->SetNumeroReferencia($row['nro_referencia']);
		$comprobante->SetIdUsuario($row['usuario']);
		$comprobante->SetIdEstado($row['estado']);
		$comprobante->SetFechaEmision($row['fecha_emision']);
		$comprobante->SetMemoContenido($row['memo_contenido']);
		$comprobante->SetMemoResponsable($row['memo_responsable']);				
		
		return $comprobante;
	}

	public static function Anular($params)
	{
		try
		{		
		$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
		if($resultTransaction === false)
			throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
		
		$query = "UPDATE 
					sai_comp_diario 
				SET esta_id = 15
				WHERE comp_id='".$params['id']."'";
			
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al guardar el codi en comp_diario. Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));		
		
		$query = "UPDATE
					sai_codi
				SET esta_id = 15
				WHERE comp_id = '".$params['id']."'";
			
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al guardar el codi en codi. Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

		$query = "UPDATE
					sai_doc_genera
				SET esta_id = 15
				WHERE docg_id = '".$params['id']."'";
		
			
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al anular el codi. Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
		
		$query = "UPDATE
					sai_causado
				SET esta_id = 2
				WHERE caus_docu_id = '".$params['id']."'";
		
			
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al guardar el codi. Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
		
		$query = "UPDATE
					sai_pagado
				SET esta_id = 2
				WHERE paga_docu_id = '".$params['id']."'";
		
			
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al guardar el codi. Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

		$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
		if($result === false)
			throw new Exception("Error al realizar el commit en la funcion de anulacion del codi. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
		
		return true;		
		} catch (Exception $e) {
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}		
		
	}

	public static  function InsertCodi($params){
	
		try
		{
			if(!is_array($params))
				throw new Exception('El parámetro "$params" no es un arreglo');
			if($params['fechaEmision'] == null)
				throw new Exception('El parámetro "$params[\'fechaEmision\']" es nulo');
			if($params['fechaEmision'] == "")
				throw new Exception('El parámetro "$params[\'fechaEmision\']" está vacío');
			if($params['dependencia'] == null)
				throw new Exception('El parámetro "$params[\'dependencia\']" es nulo');
			if($params['dependencia'] == "")
				throw new Exception('El parámetro "$params[\'dependencia\']" está vacío');
			if($params['login'] == null)
				throw new Exception('El parámetro "$params[\'login\']" es nulo');
			if($params['login'] == "")
				throw new Exception('El parámetro "$params[\'login\']" está vacío');
			if($params['userPerfilId'] == null)
				throw new Exception('El parámetro "$params[\'userPerfilId\']" es nulo');
			if($params['userPerfilId'] == "")
				throw new Exception('El parámetro "$params[\'userPerfilId\']" está vacío');
			if($params['yearPresupuestario'] == null)
				throw new Exception('El parámetro "$params[\'yearPresupuestario\']" es nulo');
			if($params['yearPresupuestario'] == "")
				throw new Exception('El parámetro "$params[\'yearPresupuestario\']" está vacío');
			
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){
					
				$preMsg = "Error al insertar un codi.";
				
				$fechaElaboracion = $params['fechaEmision'];
				
				/*La fuente de financiamiento no se llena se ubica en forma 1125*/
			
				$query = "
                           INSERT INTO sai_comp_diario
                           (comp_id,
							comp_fec,
							comp_tipo,
							comp_comen,
							comp_fec_emis,
							esta_id,
							depe_id,
							comp_doc_id,
							nro_referencia
							)
                  
							VALUES (
							'".$params['codi_id']."',
							TO_DATE('".$params['fechaEfectiva']."', 'DD/MM/YYYY'),
							'Diario',
							'".$params['justificacion']."',
							TO_TIMESTAMP('".$fechaElaboracion."','DD/MM/YYYY HH24:MI:SS'),
							11,
							'".$params['dependencia']."',
							'".$params['documentoAsociado']."',
							'".$params['referenciaBancaria']."');
						";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));	

				$query = "
	                                   INSERT INTO sai_codi
	                                   (comp_id,
										comp_fec,
										comp_comen,
										comp_fec_emis,
										esta_id,
										depe_id,
										comp_doc_id,
										nro_referencia,
										nro_compromiso,
										fte_financiamiento
										)
	         
										VALUES (
				
										'".$params['codi_id']."',
										TO_DATE('".$params['fechaEfectiva']."', 'DD/MM/YYYY'),
										'".$params['justificacion']."',
										TO_TIMESTAMP('".$fechaElaboracion."','DD/MM/YYYY HH24:MI:SS'),
										11,
										'".$params['dependencia']."',
										'".$params['documentoAsociado']."',
										'".$params['referenciaBancaria']."',
										'".$params['compromisoId']."',																								
										null);
										";
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));	
								
			/*Llenado en sai_doc_genera*/	
	
				$data = array();
				$data['docg_id'] = $params['codi_id'] ;
				$data['docg_wfob_id_ini'] = $params['docg_wfob_id_ini'] != false ? $params['docg_wfob_id_ini'] :  0 ;
				$data['docg_wfca_id'] = $params['CadenaIdcadena'] ;
				$data['docg_usua_login'] = $params['login'];
				$data['docg_perf_id'] =  $params['userPerfilId'];
				$data['docg_fecha'] = $fechaElaboracion;
				$data['docg_esta_id'] = $params['docg_esta_id'] != false ? $params['docg_esta_id'] :59 ;
				$data['docg_prioridad'] = 1 ;
				$data['docg_perf_id_act'] = $params['PerfilSiguiente'] ;
				$data['docg_estado_pres'] = '' ;
				$data['docg_numero_reserva'] =  '' ;
				$data['docg_fuente_finan'] = '' ;
	
				$docGenera = SafiModeloDocGenera::LlenarDocGenera($data);
	
				$result = SafiModeloDocGenera::GuardarDocGenera($docGenera);
	
				if($result === false) throw new Exception($preMsg . ' Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			/*Fin llenado sai_doc_genera*/
				
				/*Llenado en causado*/
				$query = "INSERT INTO sai_causado
			         			(caus_id,
								  pres_anno,
								  caus_fecha,
								  caus_desc,
								  caus_docu_id,
								  esta_id,
								  fecha_anulacion)
       						VALUES ('".$params['codi_id']."',
       								".$params['yearPresupuestario'].",
       								TO_TIMESTAMP('".$params['fechaEfectiva']."','DD/MM/YYYY'),
       								'Causado de un codi',
									'".$params['codi_id']."',
									1,
       								NULL
       								)";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				
				/*Llenado en pagado*/
				$query = "INSERT INTO sai_pagado
			         			(paga_id,
								  pres_anno,
								  paga_fecha,
								  paga_descripcion,
								  paga_docu_id,
								  esta_id,
								  fecha_anulacion
			         			)
         						VALUES ('".$params['codi_id']."',
       								".$params['yearPresupuestario'].",
       								TO_TIMESTAMP('".$params['fechaEfectiva']."','DD/MM/YYYY'),
       								'Pagado de un codi',
       								'".$params['codi_id']."',
       								1,
       								NULL
       								)";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			/*Llenado sai_reng_comp*/
			/*	$listaCuentas = explode(",",$params['cuentaContable']);*/
				
				$listaCuentas = $params['cuentaContable'];
				$listaCuentasBanco = $params['cuentaContableBanco'];
				$listaPartidas = $params['partidaPresupuestaria'];
				$listaDebe = $params['montoDebe'];
				$listaHaber = $params['montoHaber'];
				$listaProyAcc = $params['proyAcc'];
				$listaAcEsp = $params['AccEsp'];
				$listaProyAccTipo =	$params['proyAccTipo'];
				
				$i=0;				
				foreach ($listaCuentas as $cuentas) {
					/*Inserción de reng_comp*/
					$query  = "INSERT INTO sai_reng_comp 
								(comp_id,
								reng_comp,
								cpat_id,
								cpat_nombre,
								rcomp_debe, 
								rcomp_haber, 
								rcomp_tot_db, 
								rcomp_tot_hab, 
								rcomp_dife,
								fecha_emis,
								mostrar,
								part_id,
								pres_anno,
								pr_ac,
								a_esp,
								pr_ac_tipo) 
          					VALUES ('".$params['codi_id']."',
							".($i+1).",
          					'".$cuentas."',
          					'".$listaCuentasBanco[$cuentas]['cpat_nombre']."',          							
          					".$listaDebe[$i].",
          					".$listaHaber[$i].",
          					".($listaDebe[$i] + $listaHaber[$i]).",
          					".($listaDebe[$i] + $listaHaber[$i]).",
          					0,
          					TO_DATE('".$params['fechaEfectiva']."', 'DD/MM/YYYY'),
          					true,
          					TRIM('".$listaPartidas[$i]."'),
          					".$params['yearPresupuestario'].",
          					'".$listaProyAcc[$i]."',
							'".$listaAcEsp[$i]."',									
          					".$listaProyAccTipo[$i]."::bit)";
				
					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
						throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
						
			     
			        if (strlen($listaPartidas[$i])> 5) { 
						/*Inserción en causado detalle*/
					         $query = "INSERT INTO sai_causad_det
					         			(part_id, 
					         			caus_id, 
					         			pres_anno, 
					         			cadt_monto, 
					         			cadt_abono, 
					         			cadt_detalle, 
					         			cadt_id_p_ac, 
					         			cadt_cod_aesp, 
					         			cadt_tipo, 
					         			depe_id)
		       						VALUES (TRIM('".$listaPartidas[$i]."'),
		       								'".$params['codi_id']."',
		       								".$params['yearPresupuestario'].",
		       								".($listaDebe[$i] - $listaHaber[$i]).",
		       								1::bit,
		       								'Causado de un codi',
											'".$listaProyAcc[$i]."',
											'".$listaAcEsp[$i]."',									
		          							".$listaProyAccTipo[$i]."::bit,
		          							450		       										
		       						)";
					         if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					         	throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					         
					        /*Inserción en pagado detalle*/
					         $query = "INSERT INTO sai_pagado_dt
					         			(part_id, 
					         			paga_id, 
					         			pres_anno, 
					         			padt_monto, 
					         			padt_abono, 
					         			padt_tipo, 
					         			padt_cod_aesp, 
					         			padt_id_p_ac, 
					         			depe_id, 
					         			padt_detalle
					         			)
		         						VALUES (TRIM('".$listaPartidas[$i]."'),
		       								'".$params['codi_id']."',
		       								".$params['yearPresupuestario'].",
		       								".($listaDebe[$i] - $listaHaber[$i]).",
		       								1::bit,
		       								".$listaProyAccTipo[$i]."::bit,
											'".$listaAcEsp[$i]."',	       										
											'".$listaProyAcc[$i]."',
											450,
		          							'Pagado de un codi'	       										
		       								)";
					         
					         if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					         	throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					}
					/*Fin llenado sai_reng_comp, causado y pagado*/
					
					/*Llenado cuentas financieras sai_mov_ctabanco*/
			         if (strlen($listaCuentasBanco[$cuentas]['cbanco_id']) > 5) {
			         	$query  = "INSERT INTO sai_mov_cta_banco
										(ctab_numero,
										nro_cheque,
										docg_id,
										monto_debe,
										monto_haber,
										conciliado,
										fechaemision_cheque
										)
									VALUES ('".$listaCuentasBanco[$cuentas]['cbanco_id']."',
										'".$params['referenciaBancaria']."',
										'".$params['codi_id']."',
										".$listaDebe[$i].",
										".$listaHaber[$i].",
										51,
										TO_DATE('".$params['fechaEfectiva']."', 'DD/MM/YYYY'))";

				         if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				         	throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			         }
			         /*Fin llenado cuentas financieras sai_mov_ctabanco*/
			         
			         $i++;
				}

				$result = $GLOBALS['SafiClassDb']->CommitTransaction();
				
					
				return true;
	
			} else {
				throw new Exception('Error al iniciar la transacción');
			}
		}catch(Exception $e){
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}
	}

	public static  function UpdateCodi($params){
	
		try{
			
			if(!is_array($params))
				throw new Exception('El parámetro "$params" no es un arreglo');
			if($params['fechaEmision'] == null)
				throw new Exception('El parámetro "$params[\'fechaEmision\']" es nulo');
			if($params['fechaEmision'] == "")
				throw new Exception('El parámetro "$params[\'fechaEmision\']" está vacío');
			if($params['login'] == null)
				throw new Exception('El parámetro "$params[\'login\']" es nulo');
			if($params['login'] == "")
				throw new Exception('El parámetro "$params[\'login\']" está vacío');
			if($params['userPerfilId'] == null)
				throw new Exception('El parámetro "$params[\'userPerfilId\']" es nulo');
			if($params['userPerfilId'] == "")
				throw new Exception('El parámetro "$params[\'userPerfilId\']" está vacío');
			if($params['yearPresupuestario'] == null)
				throw new Exception('El parámetro "$params[\'yearPresupuestario\']" es nulo');
			if($params['yearPresupuestario'] == "")
				throw new Exception('El parámetro "$params[\'yearPresupuestario\']" está vacío');
			
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($result === true)
			{
					
				$preMsg = "Error al modificar un codi.";
				
				$fechaModificacion = $params['fechaEmision'];
				
				/*La fuente de financiamiento no se llena se ubica en forma 1125*/

				$query  = "INSERT INTO safi_observaciones_doc
										(
										id_doc,  
										fecha, 
							  			perfil, 
							  			observacion, 
							  			opcion, 
							  			usua_login 
										)
							VALUES ('".$params['idCodi']."',
										TO_TIMESTAMP('".$fechaModificacion."','DD/MM/YYYY HH24:MI:SS'),
										'".$params['userPerfilId']."',
										'Modificacion',
										2,
										'".$params['login']."'
									)";
											
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				$query = "
                           UPDATE sai_comp_diario
						   SET comp_fec = TO_DATE('".$params['fechaEfectiva']."', 'DD/MM/YYYY'),
								comp_comen = '".$params['justificacion']."',
								comp_fec_emis = TO_TIMESTAMP('".$params['fechaEfectiva']."','DD/MM/YYYY HH24:MI:SS'),
								comp_doc_id = '".$params['documentoAsociado']."',
								nro_referencia = '".$params['referenciaBancaria']."'
							WHERE comp_id = '".$params['idCodi']."'";

				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception("Error al modificar comp_diario. Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				$query = "
                           UPDATE sai_codi
						   SET comp_fec = TO_DATE('".$params['fechaEfectiva']."', 'DD/MM/YYYY'),
								comp_comen = '".$params['justificacion']."',
								comp_fec_emis = TO_TIMESTAMP('".$fechaModificacion."','DD/MM/YYYY HH24:MI:SS'),
								comp_doc_id = '".$params['documentoAsociado']."',
								nro_referencia = '".$params['referenciaBancaria']."'
							WHERE comp_id = '".$params['idCodi']."'";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				/*Actualización en sai_doc_genera*/
				$query = "
                           UPDATE sai_doc_genera
						   SET docg_fecha = TO_DATE('".$params['fechaEfectiva']."', 'DD/MM/YYYY')
							WHERE docg_id = '".$params['idCodi']."'";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
				/*Borrado de tablas*/
				$query = "
                           DELETE FROM sai_causad_det
							WHERE caus_id = '".$params['idCodi']."'";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));				
				
						$query = "UPDATE sai_causado
							SET caus_fecha = TO_TIMESTAMP('".$params['fechaEfectiva']."','DD/MM/YYYY HH24:MI:SS')
						WHERE caus_id = '".$params['idCodi']."'";
						
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				$query = "
                           DELETE FROM sai_pagado_dt
							WHERE paga_id = '".$params['idCodi']."'";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				//cambio
				$query = "UPDATE sai_pagado
							SET paga_fecha = TO_TIMESTAMP('".$params['fechaEfectiva']."','DD/MM/YYYY HH24:MI:SS')
						WHERE paga_id = '".$params['idCodi']."'";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				$query = "
                           DELETE FROM sai_reng_comp
							WHERE comp_id = '".$params['idCodi']."'";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));				
				
				/*Llenado sai_reng_comp*/
				$listaCuentas = $params['cuentaContable'];
				$listaCuentasBanco = $params['cuentaContableBanco'];
				$listaPartidas = $params['partidaPresupuestaria'];
				$listaDebe = $params['montoDebe'];
				$listaHaber = $params['montoHaber'];
				$listaProyAcc = $params['proyAcc'];
				$listaAcEsp = $params['AccEsp'];
				$listaProyAccTipo =	$params['proyAccTipo'];
				
				$i=0;
				foreach ($listaCuentas as $cuentas)
				{
					/*Inserción de reng_comp*/
					$query  = "INSERT INTO sai_reng_comp
								(comp_id,
								reng_comp,
								cpat_id,
								cpat_nombre,
								rcomp_debe,
								rcomp_haber,
								rcomp_tot_db,
								rcomp_tot_hab,
								rcomp_dife,
								fecha_emis,
								mostrar,
								part_id,
								pres_anno,
								pr_ac,
								a_esp,
								pr_ac_tipo)
          					VALUES ('".$params['idCodi']."',
							".($i+1).",
          					'".$cuentas."',
          					'".$listaCuentasBanco[$cuentas]['cpat_nombre']."',
          					".$listaDebe[$i].",
          					".$listaHaber[$i].",
          					".($listaDebe[$i] + $listaHaber[$i]).",
          					".($listaDebe[$i] + $listaHaber[$i]).",
          					0,
          					TO_DATE('".$params['fechaEfectiva']."', 'DD/MM/YYYY'),
          					true,
          					TRIM('".$listaPartidas[$i]."'),
          					".$params['yearPresupuestario'].",
          					'".$listaProyAcc[$i]."',
							'".$listaAcEsp[$i]."',
          					".$listaProyAccTipo[$i]."::bit)";
	
					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
						throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
	
					if (strlen($listaPartidas[$i])> 5) {
						/*Inserción en causado*/
						$query = "INSERT INTO sai_causad_det
					         			(part_id,
					         			caus_id,
					         			pres_anno,
					         			cadt_monto,
					         			cadt_abono,
					         			cadt_detalle,
					         			cadt_id_p_ac,
					         			cadt_cod_aesp,
					         			cadt_tipo,
					         			depe_id)
		       						VALUES (TRIM('".$listaPartidas[$i]."'),
		       								'".$params['idCodi']."',
		       								".$params['yearPresupuestario'].",
		       								".($listaDebe[$i] - $listaHaber[$i]).",
		       								1::bit,
		       								'Causado de un codi',
											'".$listaProyAcc[$i]."',
											'".$listaAcEsp[$i]."',
		          							".$listaProyAccTipo[$i]."::bit,
		          							450
		       						)";
						if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
							throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
	
						/*Inserción en pagado*/
						$query = "INSERT INTO sai_pagado_dt
					         			(part_id,
					         			paga_id,
					         			pres_anno,
					         			padt_monto,
					         			padt_abono,
					         			padt_tipo,
					         			padt_cod_aesp,
					         			padt_id_p_ac,
					         			depe_id,
					         			padt_detalle
					         			)
		         						VALUES (TRIM('".$listaPartidas[$i]."'),
		       								'".$params['idCodi']."',
		       								".$params['yearPresupuestario'].",
		       								".($listaDebe[$i] - $listaHaber[$i]).",
		       								1::bit,
		       								".$listaProyAccTipo[$i]."::bit,
											'".$listaAcEsp[$i]."',
											'".$listaProyAcc[$i]."',
											450,
		          							'Pagado de un codi'
		       								)";
	
						if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
							throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					}
					/*Fin llenado sai_reng_comp, causado y pagado*/
					 	
					/*Llenado cuentas financieras sai_mov_ctabanco*/
					if (strlen($listaCuentasBanco[$cuentas]['cbanco_id']) > 5) {
						$query  = "INSERT INTO sai_mov_cta_banco
										(ctab_numero,
										nro_cheque,
										docg_id,
										monto_debe,
										monto_haber,
										conciliado,
										fechaemision_cheque
										)
									VALUES ('".$listaCuentasBanco[$cuentas]['cbanco_id']."',
										'".$params['referenciaBancaria']."',
										'".$params['idCodi']."',
										".$listaDebe[$i].",
										".$listaHaber[$i].",
										51,
										TO_DATE('".$params['fechaEfectiva']."', 'DD/MM/YYYY'))";
	
						if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
							throw new Exception($preMsg . " Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					}
					$i++;
				}
	
				/*Fin llenado cuentas financieras sai_mov_ctabanco*/
	
				$result = $GLOBALS['SafiClassDb']->CommitTransaction();
					
				return $params['idCodi'];
	
			} else {
				throw new Exception('Error al iniciar la transacci&oacute;n');
			}
		}catch(Exception $e){
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}
	}	
}