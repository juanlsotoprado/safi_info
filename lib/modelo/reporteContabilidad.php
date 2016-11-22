<?php
require_once(SAFI_ENTIDADES_PATH . '/cuentaBanco.php');
require_once(SAFI_ENTIDADES_PATH . '/tipocuentabancaria.php');
require_once(SAFI_ENTIDADES_PATH . '/cuentaContable.php');
//require_once(SAFI_INCLUDE_PATH . '/arreglos_pg.php');

class SafiModeloReporteContabilidad
{
	public static function GetCuentaBancariaPagado(array $params)
	{
		try
		{
			$preMsg = "Error al obtener resultados de la búsqueda";
			$queryWhere = "";
				
			if($params == null)
			throw new Exception($preMsg." El parámetro params es nulo.");

			if(!is_array($params))
			throw new Exception($preMsg." El parámetro params no es un arreglo.");
				
			if(isset($params['fechaInicio']) && $params['fechaInicio'] != '' && isset($params['fechaFin']) && $params['fechaFin'] !='') {
				$fechaInicio = explode ('/',$params['fechaInicio']);
				$fechaFin = explode ('/',$params['fechaFin']);
			}	
			
			if (strcmp($params['tipoReporte'], 'C')==0) {
				$descripcion_tipo = "CAUSADOS";
				$descripcion_fecha = " en rango de fecha ".$params['fechaInicio']."-".$params['fechaFin'];				
				if(isset($params['cuentaBancaria']) && $params['cuentaBancaria']!= -1) {
					$condicion = "  (t.nro_cuenta_emisor='".$params['cuentaBancaria']."' OR t.nro_cuenta_receptor='".$params['cuentaBancaria']."') OR cq.ctab_numero='".$params['cuentaBancaria']."'";
				}

				$WhereFecha =  " AND c.caus_fecha::DATE BETWEEN '".$fechaInicio[2].'-'.$fechaInicio[1].'-'.$fechaInicio[0]."' AND '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."'";
				/*Tiene que aparecer el monto en negativo*/
				$WhereFecha2 = " AND SUBSTRING(TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') FROM 4 for 7) != SUBSTRING(TO_CHAR(c.fecha_anulacion, 'DD/MM/YYYY') FROM 4 FOR 7)
						AND c.caus_fecha::DATE <= '".$fechaInicio[2].'-'.$fechaInicio[1].'-'.$fechaInicio[0]."' AND c.caus_fecha::DATE >= '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."' 
						AND c.fecha_anulacion::DATE <= '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."'";
				
				/*Tiene que aparecer el monto en positivo*/
				$WhereFecha3 = " AND SUBSTRING(TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') FROM 4 for 7) != SUBSTRING(TO_CHAR(c.fecha_anulacion, 'DD/MM/YYYY') FROM 4 FOR 7)
						AND c.caus_fecha::DATE BETWEEN '".$fechaInicio[2].'-'.$fechaInicio[1].'-'.$fechaInicio[0]."' AND '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."'
						AND c.fecha_anulacion::DATE > '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."'";
				
				
				$query1 =
						"SELECT 
							c.caus_id AS caus_id,
							sp.comp_id AS compromiso,
							sp.sopg_bene_ci_rif AS sopg_bene_ci_rif,
							TO_CHAR(sp.sopg_fecha, 'DD/MM/YYYY') AS fecha_sopg,
							TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') AS fecha_causado,
							TRIM(sp.sopg_factura) AS factura,
							sp.sopg_id AS codigo_sopg,
							sp.numero_reserva AS reserva,
							MAX(cdi.comp_fec_emis) AS max_fecha,
							ts.nombre_sol AS tipo_solicitud,
							c.pres_anno AS pres_anno
						FROM sai_sol_pago sp 
						INNER JOIN sai_comp_diario cdi ON (cdi.comp_doc_id = sp.sopg_id) 
						INNER JOIN sai_causado c ON (sp.sopg_id = c.caus_docu_id)
						INNER JOIN sai_tipo_solicitud ts ON (sp.sopg_tp_solicitud = ts.id_sol)
						WHERE  cdi.comp_comen like 'C-%' 
							
							AND c.esta_id != 2 
							AND ((c.esta_id != 15 ". $WhereFecha. ") OR (c.esta_id = 15". $WhereFecha2. ") OR (c.esta_id = 15". $WhereFecha3. "))
						GROUP BY 1,2,3,4,5,6,7,8,10,11";		

				$query2 =
						"SELECT
							busq1.*,
							cd.cadt_monto AS monto_causado, 
							cd.part_id AS partida,
							cd.cadt_id_p_ac AS cadt_id_p_ac,
							cd.cadt_cod_aesp AS cadt_cod_aesp,
							busq1.pres_anno AS cpres_anno,	
							cd.pres_anno AS cdpres_anno
						FROM sai_causad_det cd, 
							(".$query1.") AS busq1
						WHERE cd.caus_id = busq1.caus_id 
								AND busq1.pres_anno = cd.pres_anno";
				
				$query =
						"SELECT 
							busq2.compromiso AS compromiso,
							busq2.tipo_solicitud AS tipo_solicitud,
							cdi.comp_id AS asiento,
							busq2.fecha_sopg AS fecha_sopg,
							busq2.fecha_causado AS fecha_causado,
							busq2.factura AS factura, 
							busq2.codigo_sopg AS codigo_sopg,
							busq2.reserva AS reserva,
							UPPER(COALESCE(em.empl_nombres,'')) || ' ' || UPPER(COALESCE(em.empl_apellidos,'')) AS beneficiario,
							UPPER(COALESCE(p.prov_nombre,'')) AS beneficiariop,
							UPPER(COALESCE(v.benvi_nombres,'')) ||' '|| UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiariov, 
							COALESCE(ae.centro_gestor, '') || COALESCE(pe.centro_gestor, '') || '/' || COALESCE(ae.centro_costo, '') || COALESCE(pe.centro_costo, '') AS proy_acc, 
							busq2.monto_causado AS monto_causado, 
							busq2.partida AS partida, 
							CASE 
								WHEN (substr(partida,0,6) = '4.11.0') THEN 
									(SELECT cpat_id FROM sai_convertidor WHERE part_id=partida)
								ELSE 
									partida 
							END AS cuenta,	
							COALESCE(ch.nro_cheque,'') || COALESCE(t.nro_referencia,'') AS referencia,
							COALESCE(TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY'),'') || COALESCE(TO_CHAR(t.trans_fecha,'DD/MM/YYYY'), '') AS fecha_emision_pago, 
							COALESCE(ch.monto_cheque,0) + COALESCE(t.trans_monto,0) AS monto,
							COALESCE(riva.sopg_ret_monto,0) AS riva, 
							COALESCE(rislr.sopg_ret_monto,0) AS rislr, 
							COALESCE(rltf.sopg_ret_monto,0) AS rltf, 
							COALESCE(rfza.sopg_ret_monto,0) AS rfza,
							conv.cpat_id AS cpat_id
						FROM (".$query2.") AS busq2
						LEFT OUTER JOIN sai_empleado em ON (trim(busq2.sopg_bene_ci_rif) = trim(em.empl_cedula))
						LEFT OUTER JOIN sai_viat_benef v ON (trim(busq2.sopg_bene_ci_rif) = trim(v.benvi_cedula))
						LEFT OUTER JOIN sai_proveedor_nuevo p ON (trim(busq2.sopg_bene_ci_rif) = trim(p.prov_id_rif))
						LEFT OUTER JOIN sai_cheque ch ON (busq2.codigo_sopg = ch.docg_id AND ch.estatus_cheque != 15)
						LEFT OUTER JOIN sai_pago_transferencia t ON (busq2.codigo_sopg = t.docg_id)
						LEFT OUTER JOIN sai_comp_diario cdi ON (cdi.comp_doc_id = busq2.codigo_sopg AND cdi.comp_comen like 'C-%' AND cdi.comp_fec_emis=busq2.max_fecha)		
						LEFT OUTER JOIN sai_chequera cq ON (ch.nro_chequera = cq.nro_chequera)
						LEFT OUTER JOIN sai_sol_pago_retencion riva ON (riva.sopg_id = busq2.codigo_sopg AND riva.impu_id = 'IVA')
						LEFT OUTER JOIN sai_sol_pago_retencion rltf ON (rltf.sopg_id = busq2.codigo_sopg AND rltf.impu_id = 'LTF')
						LEFT OUTER JOIN sai_sol_pago_retencion rislr ON (rislr.sopg_id = busq2.codigo_sopg AND rislr.impu_id = 'ISLR')
						LEFT OUTER JOIN sai_sol_pago_retencion rfza ON (rfza.sopg_id = busq2.codigo_sopg AND rfza.impu_id = 'FZA')
						LEFT OUTER JOIN sai_acce_esp ae ON (busq2.cadt_id_p_ac = ae.acce_id AND busq2.cadt_cod_aesp=ae.aces_id ) 
						LEFT OUTER JOIN sai_ac_central ac ON(ae.acce_id = ac.acce_id AND ac.pres_anno='".$fechaInicio[2]."')
						LEFT OUTER JOIN sai_proy_a_esp pe ON (busq2.cadt_id_p_ac = pe.proy_id AND busq2.cadt_cod_aesp = pe.paes_id ) 
						LEFT OUTER JOIN sai_proyecto py ON (pe.proy_id = py.proy_id AND py.pre_anno = '".$fechaInicio[2]."')
						LEFT OUTER JOIN sai_convertidor conv ON (conv.part_id = busq2.partida)
						WHERE ".$condicion;
			}
			
			//Si es Pagado

			else if (strcmp($params['estado'], 'Tr')==0) {
				$descripcion_tipo = " PAGADOS EN TR&Aacute;NSITO ";
				$descripcion_fecha = " al ".$params['fechaFin'];
				$query = 
				"SELECT
					sp.comp_id AS compromiso, 
					cdi.comp_id AS asiento,
					TO_CHAR(sp.sopg_fecha, 'DD/MM/YYYY') AS fecha_sopg,
					TRIM(sp.sopg_factura) AS factura, 
					sp.sopg_id AS codigo_sopg,
					sp.numero_reserva AS reserva,
					UPPER(COALESCE(em.empl_nombres,'')) || ' ' || UPPER(COALESCE(em.empl_apellidos,'')) AS beneficiario, 
					UPPER(COALESCE(prov.prov_nombre,'')) AS beneficiariop,
					UPPER(COALESCE(v.benvi_nombres,'')) ||' '|| UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiariov, 
					COALESCE(ac.centro_gestor, '') || COALESCE(py.centro_gestor, '') || '/' || COALESCE(ac.centro_costo, '') || COALESCE(py.centro_costo, '') AS proy_acc,
					pd.padt_monto AS monto_causado, 
					pd.part_id AS partida,
					CASE 
						WHEN (substr(pd.part_id,0,6) = '4.11.0') THEN 
							(SELECT cpat_id FROM sai_convertidor WHERE part_id = pd.part_id)
						ELSE 
							pd.part_id 
					END AS cuenta,	
				
					COALESCE(ch.nro_cheque,'') || COALESCE(ptr.nro_referencia,'') AS referencia,
					COALESCE(TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY'),'') || COALESCE(TO_CHAR(ptr.trans_fecha,'DD/MM/YYYY'), '') AS fecha_emision_pago, 
					COALESCE(ch.monto_cheque,0) + COALESCE(ptr.trans_monto,0) AS monto,
					COALESCE(riva.sopg_ret_monto,0) AS riva, 
					COALESCE(rislr.sopg_ret_monto,0) AS rislr, 
					COALESCE(rltf.sopg_ret_monto,0) AS rltf,
					COALESCE(rfza.sopg_ret_monto,0) AS rfza,
					ts.nombre_sol AS tipo_solicitud
				FROM sai_pagado p
				LEFT OUTER JOIN sai_pago_cheque pch ON (pch.pgch_id = p.paga_docu_id) 
				LEFT OUTER JOIN sai_pago_transferencia ptr ON (trim(ptr.trans_id)=trim(p.paga_docu_id) AND ptr.esta_id != 15) 
				INNER JOIN sai_sol_pago sp ON ((sp.sopg_id=pch.docg_id OR sp.sopg_id=ptr.docg_id))
				INNER JOIN sai_tipo_solicitud ts ON (sp.sopg_tp_solicitud = ts.id_sol)
				LEFT OUTER JOIN sai_empleado em ON (trim(sp.sopg_bene_ci_rif)=trim(em.empl_cedula)) 
				LEFT OUTER JOIN sai_viat_benef v ON (trim(sp.sopg_bene_ci_rif)=trim(v.benvi_cedula)) 
				LEFT OUTER JOIN sai_proveedor_nuevo prov ON (trim(sp.sopg_bene_ci_rif)=trim(prov.prov_id_rif)) 
				LEFT OUTER JOIN sai_cheque ch ON (sp.sopg_id=ch.docg_id) 
				INNER JOIN sai_mov_cta_banco mv ON (sp.sopg_id=mv.docg_id) 
				INNER JOIN sai_pagado_dt pd ON (p.paga_id = pd.paga_id AND p.pres_anno=pd.pres_anno)
				INNER JOIN sai_comp_diario cdi ON (cdi.comp_doc_id=p.paga_docu_id AND cdi.comp_comen like 'P-%')
				LEFT OUTER JOIN  sai_sol_pago_retencion riva ON (riva.sopg_id=sp.sopg_id AND riva.impu_id='IVA')
				LEFT OUTER JOIN  sai_sol_pago_retencion rltf ON (rltf.sopg_id=sp.sopg_id AND rltf.impu_id='LTF')
				LEFT OUTER JOIN  sai_sol_pago_retencion rislr ON (rislr.sopg_id=sp.sopg_id AND rislr.impu_id='ISLR')
				LEFT OUTER JOIN  sai_sol_pago_retencion rfza ON (rfza.sopg_id=sp.sopg_id AND rfza.impu_id='FZA')
				LEFT OUTER JOIN  sai_acce_esp ac ON (pd.padt_id_p_ac = ac.acce_id AND pd.padt_cod_aesp = ac.aces_id AND ac.pres_anno = pd.pres_anno) 
				LEFT OUTER JOIN  sai_proy_a_esp py ON (pd.padt_id_p_ac = py.proy_id AND pd.padt_cod_aesp = py.paes_id AND py.pres_anno = pd.pres_anno)
				WHERE p.esta_id!=2 AND
					 (ch.nro_chequera IN (SELECT 
					 							nro_chequera
					 						FROM sai_chequera
					 						WHERE ctab_numero='".$params['cuentaBancaria']."') OR 
					 	(ptr.nro_cuenta_emisor='".$params['cuentaBancaria']."' OR
					 	 ptr.nro_cuenta_emisor='".$params['cuentaBancaria']."'
					 	)
					 ) AND
					p.paga_docu_id NOT LIKE 'codi%' AND
					p.paga_docu_id NOT LIKE '%08' AND
					(mv.fechaemision_cheque::DATE<= '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."'  AND 
					p.paga_docu_id IN (SELECT
											docg_id
										FROM sai_ctabanco_saldo
										WHERE fecha_saldo::DATE> '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."') OR
											 (mv.fechaemision_cheque::DATE <= '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."'  AND 
											 p.paga_docu_id NOT IN (SELECT 
											 							docg_id
											 						FROM sai_ctabanco_saldo
											 						WHERE (docg_id LIKE 'pgch%' OR docg_id LIKE 'tran%')
											 						)
											 )
										) AND
					((sp.esta_id!=15 AND ((pch.esta_id!=15 AND ch.estatus_cheque!=15) OR ptr.esta_id!=15)
					OR ch.id_cheque IN (SELECT 
										id_cheque
									FROM sai_cheque_estados che
									WHERE che.comentario LIKE 'AT-%' AND
										che.estatus_cheque = 15 AND
										che.fecha_accion_cheque::DATE > '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."' )
									)
				 	OR (ch.id_cheque IN (SELECT
				 							id_cheque
				 						FROM sai_cheque_estados che
				 						WHERE che.comentario LIKE 'AT-%'
				 							AND che.estatus_cheque = 15
				 							AND SUBSTR(che.fecha_accion_cheque::DATE,6,2) LIKE '".$fechaFin[1]."%'
				 						)
				 		)
				 	)
				 							
				UNION
						
						
				SELECT cdi.comp_doc_id AS compromiso,
						cdi.comp_id AS asiento,
						'-' AS fecha_sopg,
						'-' AS factura,
						cdi.comp_doc_id AS codigo_sopg,
						dg.numero_reserva AS reserva, 
						'-' AS beneficiario, 
						'-' AS beneficiariop, 
						'-' AS beneficiariov, 
						COALESCE(ac.centro_gestor, '') || '/' || COALESCE(ac.centro_costo, '') AS proy_acc,
						pd.padt_monto AS monto_causado,
						pd.part_id AS partida,
						CASE 
							WHEN (substr(pd.part_id,0,6) = '4.11.0') THEN 
								(SELECT cpat_id FROM sai_convertidor WHERE part_id = pd.part_id)
							ELSE 
								pd.part_id 
						END AS cuenta,	
						
						cdi.nro_referencia AS referencia,
						COALESCE(TO_CHAR(mv.fechaemision_cheque, 'DD/MM/YYYY'),'') AS fecha_emision_pago,
						0 AS monto, 
						0 AS riva,
						0 AS rislr,
						0 AS rltf,
						0 AS rfza,
						'-' AS tipo_solicitud
				FROM sai_comp_diario cdi
				INNER JOIN sai_mov_cta_banco mv ON (cdi.comp_id = mv.docg_id) 
				INNER JOIN sai_pagado p ON (cdi.comp_id=p.paga_docu_id)
				INNER JOIN sai_pagado_dt pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno)
				INNER JOIN sai_doc_genera dg ON (cdi.comp_id = dg.docg_id)
				INNER JOIN sai_acce_esp ac ON (ac.pres_anno=pd.pres_anno AND pd.padt_id_p_ac = ac.acce_id AND pd.padt_cod_aesp=ac.aces_id)
				INNER JOIN sai_reng_comp reg ON (cdi.comp_id=reg.comp_id)
				INNER JOIN sai_ctabanco cb ON (cb.cpat_id=reg.cpat_id)
				WHERE  cb.ctab_numero='".$params['cuentaBancaria']."' AND
					mv.ctab_numero='".$params['cuentaBancaria']."' AND
					cdi.comp_id LIKE 'codi%' AND
					cdi.comp_id NOT LIKE 'codi%08' AND
					cdi.esta_id != 15 AND  
					mv.docg_id NOT IN (SELECT
											docg_id
										FROM sai_ctabanco_saldo
										WHERE docg_id LIKE 'codi%'
									   ) AND
					cdi.comp_fec::DATE <= '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."' 
				
				UNION
					
				SELECT cdi.comp_doc_id AS compromiso, 
						cdi.comp_id AS asiento,
						'-' AS fecha_sopg,  
						'-' AS factura, 
						cdi.comp_doc_id AS codigo_sopg, 
						dg.numero_reserva AS reserva, 
						'-' AS beneficiario, 
						'-' AS beneficiariop, 
						'-' AS beneficiariov, 
						COALESCE(py.centro_gestor, '') || '/' || COALESCE(py.centro_costo, '') AS proy_acc,
						pd.padt_monto AS monto_causado,
						pd.part_id AS partida,
						CASE 
							WHEN (substr(pd.part_id,0,6) = '4.11.0') THEN 
								(SELECT cpat_id FROM sai_convertidor WHERE part_id = pd.part_id)
							ELSE 
								pd.part_id 
						END AS cuenta,	
				
						cdi.nro_referencia AS referencia, 
						COALESCE(TO_CHAR(mv.fechaemision_cheque, 'DD/MM/YYYY'),'') AS fecha_emision_pago,
						0 AS monto, 
						0 AS riva, 
						0 AS rislr, 
						0 AS rltf, 
						0 AS rfza,
						'-' AS tipo_solicitud						
				FROM sai_comp_diario cdi
				INNER JOIN sai_mov_cta_banco mv ON (cdi.comp_id = mv.docg_id) 
				INNER JOIN sai_pagado p ON (cdi.comp_id = p.paga_docu_id)
				INNER JOIN sai_pagado_dt pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno)
				INNER JOIN sai_doc_genera dg ON (cdi.comp_id = dg.docg_id)
				INNER JOIN sai_proy_a_esp py ON (pd.padt_id_p_ac = py.proy_id AND pd.padt_cod_aesp=py.paes_id AND py.pres_anno = pd.pres_anno)				
				INNER JOIN sai_reng_comp reg ON (cdi.comp_id = reg.comp_id)
				INNER JOIN sai_ctabanco cb ON (cb.cpat_id = reg.cpat_id)
				WHERE cb.ctab_numero = '".$params['cuentaBancaria']."' AND
					mv.ctab_numero = '".$params['cuentaBancaria']."' AND
					cdi.comp_id LIKE 'codi%' AND
					cdi.comp_id NOT LIKE 'codi%08' AND
					cdi.esta_id != 15 AND
					cdi.comp_id = mv.docg_id AND
					mv.docg_id NOT IN (SELECT
											docg_id
										FROM sai_ctabanco_saldo
										WHERE docg_id LIKE 'codi%') AND
									cdi.comp_fec::DATE <= '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."'
				ORDER BY fecha_emision_pago, referencia";
			}	
			else if (strcmp($params['estado'], 'Co')==0) {
				$descripcion_tipo = " PAGADOS CONCILIADOS ";
				$descripcion_fecha = " al ".$params['fechaFin'];				
				/*$WhereFechaConciliado=" AND SUBSTR(ctb.fecha_saldo::DATE,6,2) LIKE '".$fechaFin[1]."%' ";*/
				$WhereFechaConciliado= " AND ctb.fecha_saldo::DATE BETWEEN '".$fechaInicio[2].'-'.$fechaInicio[1].'-'.$fechaInicio[0]."' AND '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."'";
				$query="SELECT sp.comp_id AS compromiso, 
							cdi.comp_id AS asiento,
							TO_CHAR(sp.sopg_fecha, 'DD/MM/YYYY') AS fecha_sopg,
							trim(sp.sopg_factura) AS factura, 
							sp.sopg_id AS codigo_sopg,
							sp.numero_reserva AS reserva,
							UPPER(COALESCE(em.empl_nombres,'')) || ' ' || UPPER(COALESCE(em.empl_apellidos,'')) AS beneficiario, UPPER(COALESCE(prov.prov_nombre,'')) AS beneficiariop, UPPER(COALESCE(v.benvi_nombres,'')) ||' '|| UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiariov,
							COALESCE(ac.centro_gestor, '') || COALESCE(py.centro_gestor, '') || '/' || COALESCE(ac.centro_costo, '') || COALESCE(py.centro_costo, '') AS proy_acc, 
							pd.padt_monto AS monto_causado, 
							pd.part_id AS partida,
							CASE 
								WHEN (substr(pd.part_id,0,6) = '4.11.0') THEN 
									(SELECT cpat_id FROM sai_convertidor WHERE part_id = pd.part_id)
								ELSE 
									pd.part_id 
							END AS cuenta,									
							COALESCE(ch.nro_cheque,'') || COALESCE(ptr.nro_referencia,'') AS referencia, 
							COALESCE(to_char(ctb.fecha_saldo, 'DD/MM/YYYY'),'') AS fecha_emision_pago,
							COALESCE(ch.monto_cheque,0) + COALESCE(ptr.trans_monto,0) AS monto,
							COALESCE(riva.sopg_ret_monto,0) AS riva,
							COALESCE(rislr.sopg_ret_monto,0) AS rislr,
							COALESCE(rltf.sopg_ret_monto,0) AS rltf,
							COALESCE(rfza.sopg_ret_monto,0) AS rfza,
							ts.nombre_sol AS tipo_solicitud								
				FROM sai_ctabanco_saldo ctb
				INNER JOIN sai_pagado p ON (p.paga_docu_id = ctb.docg_id)
				INNER JOIN sai_pagado_dt pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno)
				LEFT OUTER JOIN  sai_pago_transferencia ptr ON (trim(ptr.trans_id) = trim(p.paga_docu_id))
				LEFT OUTER JOIN  sai_pago_cheque pch ON (pch.pgch_id=p.paga_docu_id)
				INNER JOIN sai_sol_pago sp ON (sp.sopg_id = pch.docg_id OR sp.sopg_id = ptr.docg_id)
				INNER JOIN sai_tipo_solicitud ts ON (sp.sopg_tp_solicitud = ts.id_sol) 
				LEFT OUTER JOIN sai_empleado em ON (trim(sp.sopg_bene_ci_rif) = trim(em.empl_cedula))
				LEFT OUTER JOIN sai_viat_benef v ON (trim(sp.sopg_bene_ci_rif) = trim(v.benvi_cedula))
				LEFT OUTER JOIN sai_proveedor_nuevo prov ON (trim(sp.sopg_bene_ci_rif) = trim(prov.prov_id_rif))
				LEFT OUTER JOIN sai_cheque ch ON (sp.sopg_id = ch.docg_id)
				INNER JOIN sai_comp_diario cdi ON (cdi.comp_doc_id = p.paga_docu_id)
				LEFT OUTER JOIN  sai_sol_pago_retencion riva ON (riva.sopg_id = sp.sopg_id AND riva.impu_id = 'IVA')
				LEFT OUTER JOIN  sai_sol_pago_retencion rltf ON (rltf.sopg_id = sp.sopg_id AND rltf.impu_id = 'LTF')
				LEFT OUTER JOIN  sai_sol_pago_retencion rislr ON (rislr.sopg_id = sp.sopg_id AND rislr.impu_id = 'ISLR')
				LEFT OUTER JOIN  sai_sol_pago_retencion rfza ON (rfza.sopg_id = sp.sopg_id AND rfza.impu_id = 'FZA')
				LEFT OUTER JOIN  sai_acce_esp ac ON (pd.padt_id_p_ac = ac.acce_id AND pd.padt_cod_aesp = ac.aces_id AND ac.pres_anno = pd.pres_anno) 
				LEFT OUTER JOIN  sai_proy_a_esp py ON (pd.padt_id_p_ac = py.proy_id AND pd.padt_cod_aesp = py.paes_id AND py.pres_anno = pd.pres_anno)
				WHERE p.esta_id != 2 AND
					ch.estatus_cheque != 15
					AND cdi.esta_id != 15 
					AND cdi.comp_comen like 'P-%'
					AND cdi.esta_id != 15
					AND ctb.ctab_numero = '".$params['cuentaBancaria']."' 
					AND ctb.docg_id NOT LIKE 'codi%' 
					AND p.esta_id != 15  ". $WhereFechaConciliado . "
		
				UNION
				
				SELECT 
					cdi.comp_doc_id AS compromiso, 
					cdi.comp_id AS asiento,  
					'-' AS fecha_sopg,  
					'-' AS factura, 
					cdi.comp_doc_id AS codigo_sopg, 
					dg.numero_reserva AS reserva, 
					'-' AS beneficiario, 
					'-' AS beneficiariop, 
					'-' AS beneficiariov, 
					COALESCE(ac.centro_gestor, '') || '/' || COALESCE(ac.centro_costo, '') AS proy_acc, 
					pd.padt_monto AS monto_causado, 
					pd.part_id AS partida, 
					CASE 
							WHEN (substr(pd.part_id,0,6) = '4.11.0') THEN 
								(SELECT cpat_id FROM sai_convertidor WHERE part_id = pd.part_id)
							ELSE 
								pd.part_id 
					END AS cuenta,						
					cdi.nro_referencia AS referencia, 
					COALESCE(TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY'),'') AS fecha_emision_pago, 
					0 AS monto, 
					0 AS riva, 
					0 AS rislr, 
					0 AS rltf, 
					0 AS rfza,
					'-' AS tipo_solicitud					
				FROM 
					sai_comp_diario cdi 
				INNER JOIN sai_ctabanco_saldo ctb ON (cdi.comp_id = ctb.docg_id) 
				INNER JOIN 	sai_pagado p ON (cdi.comp_id = p.paga_docu_id) 
				INNER JOIN 	sai_pagado_dt pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
				INNER JOIN 	sai_doc_genera dg ON (cdi.comp_id = dg.docg_id)  
				INNER JOIN 	sai_acce_esp ac ON (pd.padt_id_p_ac = ac.acce_id AND pd.padt_cod_aesp = ac.aces_id AND ac.pres_anno = pd.pres_anno) 
				INNER JOIN 	sai_reng_comp reg ON (cdi.comp_id = reg.comp_id)  
				INNER JOIN 	sai_ctabanco cb ON (cb.cpat_id = reg.cpat_id) 
				WHERE 
					cb.ctab_numero = '".$params['cuentaBancaria']."' 
					AND ctb.ctab_numero = '".$params['cuentaBancaria']."' 
					AND cdi.comp_id LIKE 'codi%' 
					AND cdi.comp_id NOT LIKE 'codi%08' 
					AND cdi.esta_id != 15 ".$WhereFechaConciliado." 
					
				UNION
				
				SELECT 
					cdi.comp_doc_id AS compromiso, 
					cdi.comp_id AS asiento,  
					'-' AS fecha_sopg,  
					'-' AS factura, 
					cdi.comp_doc_id AS codigo_sopg, 
					dg.numero_reserva AS reserva, 
					'-' AS beneficiario, 
					'-' AS beneficiariop, 
					'-' AS beneficiariov, 
					COALESCE(py.centro_gestor, '') || '/' || COALESCE(py.centro_costo, '') AS proy_acc, 
					pd.padt_monto AS monto_causado, 
					pd.part_id AS partida, 
					CASE 
						WHEN (substr(pd.part_id,0,6) = '4.11.0') THEN 
							(SELECT cpat_id FROM sai_convertidor WHERE part_id = pd.part_id)
						ELSE 
							pd.part_id 
					END AS cuenta,						
					cdi.nro_referencia AS referencia, 
					COALESCE(TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY'),'') AS fecha_emision_pago, 
					0 AS monto, 
					0 AS riva, 
					0 AS rislr, 
					0 AS rltf, 
					0 AS rfza,
					'-' AS tipo_solicitud
				FROM sai_comp_diario cdi 
				INNER JOIN sai_ctabanco_saldo ctb ON (cdi.comp_id = ctb.docg_id) 
				INNER JOIN sai_pagado p ON (cdi.comp_id = p.paga_docu_id)
				INNER JOIN sai_pagado_dt pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno)
				INNER JOIN sai_doc_genera dg ON (cdi.comp_id = dg.docg_id)
				INNER JOIN sai_proy_a_esp py ON (pd.padt_id_p_ac = py.proy_id AND pd.padt_cod_aesp = py.paes_id AND py.pres_anno = pd.pres_anno)
				INNER JOIN sai_reng_comp reg ON (cdi.comp_id = reg.comp_id)
				INNER JOIN sai_ctabanco cb ON (cb.cpat_id = reg.cpat_id)
				WHERE 
					cb.ctab_numero = '".$params['cuentaBancaria']."'
					AND ctb.ctab_numero = '".$params['cuentaBancaria']."' 
					AND cdi.comp_id LIKE 'codi%' 
					AND cdi.comp_id NOT LIKE 'codi%08' 
					AND cdi.esta_id != 15 ".$WhereFechaConciliado;
				
			}
			else if (strcmp($params['estado'], 'To')==0) {
				$descripcion_tipo = " PAGADOS TODOS ";
				$descripcion_fecha = " al ".$params['fechaFin'];
				$WhereFechaConciliado= " AND ctb.fecha_saldo::DATE BETWEEN '".$fechaInicio[2].'-'.$fechaInicio[1].'-'.$fechaInicio[0]."' AND '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."'";				
				$query = 
				"SELECT 
					sp.comp_id AS compromiso, 
					cdi.comp_id AS asiento,  
					TO_CHAR(sp.sopg_fecha, 'DD/MM/YYYY') AS fecha_sopg,
					TRIM(sp.sopg_factura) AS factura,
					sp.sopg_id AS codigo_sopg,
					sp.numero_reserva AS reserva,
					UPPER(COALESCE(em.empl_nombres,'')) || ' ' || UPPER(COALESCE(em.empl_apellidos,'')) AS beneficiario,
					UPPER(COALESCE(prov.prov_nombre,'')) AS beneficiariop,
					UPPER(COALESCE(v.benvi_nombres,'')) ||' '|| UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiariov,
					COALESCE(ac.centro_gestor, '') || COALESCE(py.centro_gestor, '') || '/' || COALESCE(ac.centro_costo, '') || COALESCE(py.centro_costo, '') AS proy_acc,
					pd.padt_monto AS monto_causado,
					pd.part_id AS partida,
					CASE 
						WHEN (substr(pd.part_id,0,6) = '4.11.0') THEN 
							(SELECT cpat_id FROM sai_convertidor WHERE part_id = pd.part_id)
						ELSE 
							pd.part_id 
					END AS cuenta,						
					COALESCE(ch.nro_cheque,'') || COALESCE(ptr.nro_referencia,'') AS referencia,
					COALESCE(TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY'),'') || COALESCE(TO_CHAR(ptr.trans_fecha,'DD/MM/YYYY'), '') AS fecha_emision_pago,
					COALESCE(ch.monto_cheque,0) + COALESCE(ptr.trans_monto,0) AS monto,
					COALESCE(riva.sopg_ret_monto,0) AS riva,
					COALESCE(rislr.sopg_ret_monto,0) AS rislr,
					COALESCE(rltf.sopg_ret_monto,0) AS rltf,
					COALESCE(rfza.sopg_ret_monto,0) AS rfza,
					ts.nombre_sol AS tipo_solicitud
				FROM sai_pagado p
				LEFT OUTER JOIN sai_pago_cheque pch ON (pch.pgch_id = p.paga_docu_id) 
				LEFT OUTER JOIN sai_pago_transferencia ptr ON (trim(ptr.trans_id) = trim(p.paga_docu_id) AND ptr.esta_id != 15) 
				INNER JOIN sai_sol_pago sp ON ((sp.sopg_id = pch.docg_id OR sp.sopg_id = ptr.docg_id))
				INNER JOIN sai_tipo_solicitud ts ON (sp.sopg_tp_solicitud = ts.id_sol)
				LEFT OUTER JOIN sai_empleado em ON (trim(sp.sopg_bene_ci_rif) = trim(em.empl_cedula)) 
				LEFT OUTER JOIN sai_viat_benef v ON (trim(sp.sopg_bene_ci_rif) = trim(v.benvi_cedula)) 
				LEFT OUTER JOIN sai_proveedor_nuevo prov ON (trim(sp.sopg_bene_ci_rif) = trim(prov.prov_id_rif)) 
				LEFT OUTER JOIN sai_cheque ch ON (sp.sopg_id = ch.docg_id) 
				INNER JOIN sai_mov_cta_banco mv ON (sp.sopg_id = mv.docg_id) 
				INNER JOIN sai_pagado_dt pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno)
				INNER JOIN sai_comp_diario cdi ON (cdi.comp_doc_id = p.paga_docu_id AND cdi.comp_comen LIKE 'P-%')
				LEFT OUTER JOIN sai_sol_pago_retencion riva ON (riva.sopg_id = sp.sopg_id AND riva.impu_id = 'IVA')
				LEFT OUTER JOIN sai_sol_pago_retencion rltf ON (rltf.sopg_id = sp.sopg_id AND rltf.impu_id = 'LTF')
				LEFT OUTER JOIN sai_sol_pago_retencion rislr ON (rislr.sopg_id = sp.sopg_id AND rislr.impu_id = 'ISLR')
				LEFT OUTER JOIN sai_sol_pago_retencion rfza ON (rfza.sopg_id = sp.sopg_id AND rfza.impu_id = 'FZA')
				LEFT OUTER JOIN sai_acce_esp ac ON (pd.padt_id_p_ac = ac.acce_id AND pd.padt_cod_aesp = ac.aces_id AND ac.pres_anno = pd.pres_anno) 
				LEFT OUTER JOIN sai_proy_a_esp py ON (pd.padt_id_p_ac = py.proy_id AND pd.padt_cod_aesp = py.paes_id AND py.pres_anno = pd.pres_anno)
				WHERE p.esta_id != 2 AND 
					((ch.nro_chequera IN (
										SELECT
											nro_chequera
										FROM sai_chequera
										WHERE ctab_numero='".$params['cuentaBancaria']."'
						 			 ) OR 
					 (ptr.nro_cuenta_emisor = '".$params['cuentaBancaria']."' OR ptr.nro_cuenta_receptor = '".$params['cuentaBancaria']."')
					) AND 
					p.paga_docu_id NOT LIKE 'codi%' AND 
					p.paga_docu_id NOT LIKE '%08' AND 
					(
						mv.fechaemision_cheque::DATE <= '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."' AND 
						p.paga_docu_id IN (SELECT
												docg_id
											FROM sai_ctabanco_saldo 
											WHERE fecha_saldo::DATE > '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."') OR 
												(mv.fechaemision_cheque::DATE <= '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."' AND
												p.paga_docu_id NOT IN (SELECT 
																			docg_id
																		FROM sai_ctabanco_saldo
																		WHERE docg_id LIKE 'pgch%' OR docg_id LIKE 'tran%'
																		)
												)
											) AND
						 ((sp.esta_id !=15 AND ((pch.esta_id != 15 AND ch.estatus_cheque != 15) OR ptr.esta_id != 15)
				
				OR ch.id_cheque IN
				      (SELECT 
				      	id_cheque
				      FROM sai_cheque_estados che
				      WHERE 
				      	che.comentario LIKE 'AT-%' 
				      	AND che.estatus_cheque = 15
				       AND che.fecha_accion_cheque::DATE >'".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."')
				      	
					) 
				OR (
				ch.id_cheque IN (
								SELECT id_cheque 
								FROM sai_cheque_estados che
								WHERE che.comentario LIKE 'AT-%'
								AND che.estatus_cheque=15 
								AND SUBSTR(che.fecha_accion_cheque::DATE,6,2) LIKE '".$fechaFin[1]."%')))) OR
				
				 (p.esta_id != 15 
				 AND p.paga_docu_id IN (SELECT 
				 							docg_id
				 						FROM sai_ctabanco_saldo ctb 
				 						WHERE ctb.docg_id NOT LIKE 'codi%' 
				 							AND ctb.ctab_numero = '".$params['cuentaBancaria']."' 
											AND ctb.fecha_saldo::DATE BETWEEN '".$fechaInicio[2].'-'.$fechaInicio[1].'-'.$fechaInicio[0]."' AND '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."'				 									
				 									

				 						)
				 
				) 
				UNION
				
				SELECT 
					cdi.comp_doc_id AS compromiso,
				 	cdi.comp_id AS asiento,
				   	'-' AS fecha_sopg,
				    '-' AS factura,
				    cdi.comp_doc_id AS codigo_sopg,
				    dg.numero_reserva AS reserva, 
				    '-' AS beneficiario,
				    '-' AS beneficiariop, 
				    '-' AS beneficiariov, 
				    COALESCE(ac.centro_gestor, '') || '/' || COALESCE(ac.centro_costo, '') AS proy_acc, 
				    pd.padt_monto AS monto_causado, 
				    pd.part_id AS partida, 
					CASE 
						WHEN (substr(pd.part_id,0,6) = '4.11.0') THEN 
							(SELECT cpat_id FROM sai_convertidor WHERE part_id = pd.part_id)
						ELSE 
							pd.part_id 
					END AS cuenta,						
				    cdi.nro_referencia AS referencia, 
				    COALESCE(TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY'),'') AS fecha_emision_pago, 
				    0 AS monto,
				    0 AS riva, 
				    0 AS rislr, 
				    0 AS rltf, 
				    0 AS rfza,
					'-' AS tipo_solicitud				    
				FROM sai_comp_diario cdi
				INNER JOIN sai_ctabanco_saldo ctb ON (cdi.comp_id = ctb.docg_id)
				INNER JOIN sai_pagado p ON (cdi.comp_id = p.paga_docu_id) 
				INNER JOIN sai_pagado_dt pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
				INNER JOIN sai_doc_genera dg ON (cdi.comp_id = dg.docg_id) 
				INNER JOIN sai_acce_esp ac ON (pd.padt_id_p_ac = ac.acce_id AND pd.padt_cod_aesp = ac.aces_id AND ac.pres_anno = pd.pres_anno) 
				INNER JOIN sai_reng_comp reg ON (cdi.comp_id = reg.comp_id ) 
				INNER JOIN sai_ctabanco cb ON (cb.cpat_id = reg.cpat_id)
				WHERE 
					cb.ctab_numero = '".$params['cuentaBancaria']."' 
					AND ctb.ctab_numero = '".$params['cuentaBancaria']."' 
					AND cdi.comp_id LIKE 'codi%' 
					AND cdi.comp_id NOT LIKE 'codi%08' 
					AND cdi.esta_id != 15 ".$WhereFechaConciliado."
		
				UNION
				
				SELECT cdi.comp_doc_id AS compromiso, 
				cdi.comp_id AS asiento,  
				'-' AS fecha_sopg,  
				'-' AS factura, 
				cdi.comp_doc_id AS codigo_sopg, 
				dg.numero_reserva AS reserva,
				'-' AS beneficiario,
				'-' AS beneficiariop,
				'-' AS beneficiariov,
				COALESCE(py.centro_gestor, '') || '/' || COALESCE(py.centro_costo, '') AS proy_acc,
				pd.padt_monto AS monto_causado,
				pd.part_id AS partida,
				CASE 
					WHEN (substr(pd.part_id,0,6) = '4.11.0') THEN 
						(SELECT cpat_id FROM sai_convertidor WHERE part_id = pd.part_id)
					ELSE 
						pd.part_id 
				END AS cuenta,						
				cdi.nro_referencia AS referencia,
				COALESCE(TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY'),'') AS fecha_emision_pago,
				0 AS monto,
				0 AS riva,
				0 AS rislr,
				0 AS rltf, 
				0 AS rfza,
				'-' AS tipo_solicitud				
				FROM sai_comp_diario cdi 
				INNER JOIN sai_ctabanco_saldo ctb ON (cdi.comp_id = ctb.docg_id )
				INNER JOIN 	sai_pagado p ON (cdi.comp_id = p.paga_docu_id) 
				INNER JOIN 	sai_pagado_dt pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno)
				INNER JOIN 	sai_doc_genera dg ON (cdi.comp_id = dg.docg_id) 
				INNER JOIN 	sai_proy_a_esp py ON (pd.padt_id_p_ac = py.proy_id AND pd.padt_cod_aesp = py.paes_id AND py.pres_anno = pd.pres_anno) 
				INNER JOIN 	sai_reng_comp reg ON (cdi.comp_id = reg.comp_id)
				INNER JOIN 	sai_ctabanco cb ON (cb.cpat_id = reg.cpat_id)
				WHERE 
					cb.ctab_numero = '".$params['cuentaBancaria']."' 
					AND ctb.ctab_numero = '".$params['cuentaBancaria']."' 
					AND cdi.comp_id LIKE 'codi%' 
					AND cdi.comp_id NOT LIKE 'codi%08' 
					AND cdi.esta_id != 15 ".$WhereFechaConciliado;
			}

			/*****Ejecución*****/
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$reporte = array();
			$i=0;
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				if (strcmp(substr($row["partida"], 0,6),'4.11.0')!=0) {
					$sum_montoP += $row["monto_causado"];
				}
				
				if (strcmp(substr($row["codigo_sopg"],0,4),'-')==0) 
					$documento ="";
				else 
					$documento = $row["codigo_sopg"];

				if (strlen($row["beneficiario"])>2)
					$reporte[$i]['beneficiario'] = $row["beneficiario"];
				else if (strlen($row["beneficiariop"])>2)
					$reporte[$i]['beneficiario'] = $row["beneficiariop"];
				else 
					$reporte[$i]['beneficiario'] = $row["beneficiariov"];


				$monto = 0;
				
				$monto = 0;
				if (strcmp($codigo_documentoCh,$documento)!=0 && strcmp(substr($rowor["partida"], 0,10),'4.03.18.01')!=0) {
					// Mostrar sólo el monto del cheque una vez
					$codigo_documentoCh = $documento;
					$monto = $row["monto"];
				}				

				if (strcmp(substr($row["partida"], 0,6),'4.11.0') != 0) 
					$sum_montoCH += $monto;

				$iva = 0;
				$islr = 0;
				$ltf = 0;
				$fianza = 0;
				$monto_base = "";
				if (strcmp($codigo_documento,$documento)!=0 && strcmp(substr($row["partida"], 0,10),'4.03.18.01')!=0) {
					// Mostrar sólo las retenciones para una partida del sopg distinta a la del IVA
					$codigo_documento = $documento;
					$impuesto = trim($row['impu_id']);
				
					$islr = $row["rislr"];
					$ltf = $row["rltf"];
					$fianza=$row["rfza"];
				}
				if (/*strcmp($codigo_documento,$documento)!=0 &&*/ strcmp(substr($row["partida"], 0,10),'4.03.18.01')==0) {
					// Mostrar sólo la retención del iva para la partida del IVA en el sopg
					$iva = $row["riva"];
				}
				
				$sum_montoIVA += $row["riva"];
				$sum_montoISLR += $row["rislr"];
				$sum_montoLTF += $row["rltf"];
				$sum_montoFIANZA += $row["rfza"];				
	
				
				$reporte[$i]['compromiso'] = $row['compromiso'];
				$reporte[$i]['tipo_solicitud'] = $row['tipo_solicitud'];
				$reporte[$i]['asiento'] = $row['asiento'];
				$reporte[$i]['fecha_sopg'] = $row['fecha_sopg'];
				$reporte[$i]['fecha_causado'] = $row['fecha_causado'];
				$reporte[$i]['factura'] = $row['factura'];
				$reporte[$i]['codigo_sopg'] = $row['codigo_sopg'];
				$reporte[$i]['reserva'] = $row['reserva'];
				$reporte[$i]['proy_acc'] = $row['proy_acc'];
				$reporte[$i]['monto_causado'] = $row['monto_causado'];
				$reporte[$i]['partida'] = $row['partida'];
				$reporte[$i]['cuenta'] = $row['cuenta'];
				$reporte[$i]['referencia'] = $row['referencia'];
				$reporte[$i]['fecha_emision_pago'] = $row['fecha_emision_pago'];
				$reporte[$i]['monto'] = $monto;
				$reporte[$i]['riva'] = $iva;
				$reporte[$i]['rislr'] = $islr;
				$reporte[$i]['rltf'] = $ltf;
				$reporte[$i]['rfza'] = $fianza;
				$reporte[$i]['cpat_id'] = $row['cpat_id'];
				$reporte[$i]['sum_montoP'] = $sum_montoP;
				$reporte[$i]['sum_montoCH'] = $sum_montoCH;
				$reporte[$i]['sum_montoLTF'] = $sum_montoLTF;
				$reporte[$i]['sum_montoIVA'] = $sum_montoIVA;
				$reporte[$i]['sum_montoISLR'] = $sum_montoISLR;
				$reporte[$i]['sum_montoFIANZA'] = $sum_montoFIANZA;				
				$i++;
			}
			
				
			$GLOBALS['SafiRequestVars']['descripcion_tipo'] = $descripcion_tipo;
			$GLOBALS['SafiRequestVars']['descripcion_fecha'] = $descripcion_fecha;
				
			return $reporte;
				
		}
		catch(Exception $e){
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;		
		}
	}
	

	
	public static function GetCuentaCausadoPagadoConsolidado(array $params)
	{
		try
		{
			$preMsg = "Error al obtener resultados de la búsqueda";
			$queryWhere = "";
	
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
	
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
	
			if(isset($params['tipoSolicitudPago']) && $params['tipoSolicitudPago'] != '-1') {
				$whereTipoSolicitudPago = " AND ts.id_sol='".$params['tipoSolicitudPago']."'";
				
			}
			if(isset($params['tipoActividadCompromiso']) && $params['tipoActividadCompromiso'] != '-1') {
				$whereTipoActividadCompromiso = " AND tia.id='".$params['tipoActividadCompromiso']."'";
			}
			if(isset($params['detalleSolicitudPago']) && $params['detalleSolicitudPago'] != '') {
				$whereDetalleSolicitudPago = " AND UPPER(sopg_detalle) LIKE '%".strtoupper($params['detalleSolicitudPago'])."%'";
				$whereDetalleCodi = " AND UPPER(codi.comp_comen) LIKE '%".strtoupper($params['detalleSolicitudPago'])."%'";				

			}
				
			
			
			if(isset($params['fechaInicio']) && $params['fechaInicio'] != '' && isset($params['fechaFin']) && $params['fechaFin'] !='') {
				$fechaInicio = explode ('/',$params['fechaInicio']);
				$fechaFin = explode ('/',$params['fechaFin']);
			}
				
			$descripcion_tipo = "CAUSADOS/PAGADOS";
			$descripcion_fecha = " en rango de fecha ".$params['fechaInicio']."-".$params['fechaFin'];
	
			$WhereFecha =  " AND ca.caus_fecha::DATE BETWEEN '".$fechaInicio[2].'-'.$fechaInicio[1].'-'.$fechaInicio[0]."' AND '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."'";
			/*Tiene que aparecer el monto en negativo*/
			$WhereFecha2 = " AND SUBSTRING(TO_CHAR(ca.caus_fecha, 'DD/MM/YYYY') FROM 4 for 7) != SUBSTRING(TO_CHAR(ca.fecha_anulacion, 'DD/MM/YYYY') FROM 4 FOR 7)
					AND ca.caus_fecha::DATE <= '".$fechaInicio[2].'-'.$fechaInicio[1].'-'.$fechaInicio[0]."' AND ca.caus_fecha::DATE >= '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."'
					AND ca.fecha_anulacion::DATE <= '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."'";

			/*Tiene que aparecer el monto en positivo*/
			$WhereFecha3 = " AND SUBSTRING(TO_CHAR(ca.caus_fecha, 'DD/MM/YYYY') FROM 4 for 7) != SUBSTRING(TO_CHAR(ca.fecha_anulacion, 'DD/MM/YYYY') FROM 4 FOR 7)
					AND ca.caus_fecha::DATE BETWEEN '".$fechaInicio[2].'-'.$fechaInicio[1].'-'.$fechaInicio[0]."' AND '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."'
					AND ca.fecha_anulacion::DATE > '".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."'";


				$query =
						"SELECT 
							sdi.docg_monto_base AS monto_base,
							sdi.docg_monto_iva AS iva,
							sp.sopg_detalle	AS detalle_solicitud,
							span.soan_otro_deta AS documento_asociado,		
							TO_CHAR(ca.caus_fecha, 'DD/MM/YYYY') AS fecha_causado,
							trim(sp.sopg_factura) AS factura, 
							cad.cadt_monto AS monto_causado, 
							CASE 
								WHEN (substr(cad.part_id,0,6) = '4.11.0') THEN 
									(SELECT cpat_id FROM sai_convertidor WHERE part_id = cad.part_id LIMIT 1)
								ELSE 
									cad.part_id 
							END AS partida_causado,		
							p.paga_docu_id AS documento_pago,
							COALESCE(ptr.nro_referencia,'') AS referencia, 
							COALESCE(to_char(p.paga_fecha, 'DD/MM/YYYY'),'') AS fecha_pagado,
							COALESCE(ptr.trans_monto,0) AS monto_cheque_tran,
							COALESCE(riva.sopg_ret_monto,0) AS riva,
							COALESCE(rislr.sopg_ret_monto,0) AS rislr,
							COALESCE(rltf.sopg_ret_monto,0) AS rltf,
							COALESCE(rfza.sopg_ret_monto,0) AS rfza,
							ts.nombre_sol AS tipo_solicitud,
							sp.comp_id AS compromiso, 
							sp.sopg_id AS documento_sopg,
							f11.fuente_financiamiento AS fte_financiamiento,	
							COALESCE(ac.centro_gestor, '') || COALESCE(py.centro_gestor, '') || '/' || COALESCE(ac.centro_costo, '') || COALESCE(py.centro_costo, '') AS proy_acc,
							cdi.comp_id AS asiento	
							FROM sai_sol_pago sp
							INNER JOIN sai_tipo_solicitud ts ON (sp.sopg_tp_solicitud = ts.id_sol ".$whereTipoSolicitudPago.") 	
							INNER JOIN sai_causado ca ON (ca.caus_docu_id = sp.sopg_id AND ca.esta_id != 2)
							INNER JOIN sai_causad_det cad ON (ca.caus_id = cad.caus_id AND ca.pres_anno = cad.pres_anno)
							INNER JOIN sai_comp_diario cdi ON (cdi.comp_doc_id = ca.caus_docu_id)
							INNER JOIN  sai_pago_transferencia ptr ON (trim(ptr.docg_id) = trim(sp.sopg_id) AND ptr.esta_id!=15 AND ptr.esta_id!=2)
							LEFT OUTER JOIN  sai_sol_pago_anexo span ON (sp.sopg_id=span.sopg_id)									
							LEFT OUTER JOIN sai_pagado p ON (p.esta_id != 15 AND p.esta_id != 2 AND p.paga_docu_id = ptr.trans_id)
							LEFT OUTER JOIN  sai_docu_iva sdi ON (sp.sopg_id = sdi.docg_id)									
							LEFT OUTER JOIN  sai_sol_pago_retencion riva ON (riva.sopg_id = sp.sopg_id AND riva.impu_id = 'IVA')
							LEFT OUTER JOIN  sai_sol_pago_retencion rltf ON (rltf.sopg_id = sp.sopg_id AND rltf.impu_id = 'LTF')
							LEFT OUTER JOIN  sai_sol_pago_retencion rislr ON (rislr.sopg_id = sp.sopg_id AND rislr.impu_id = 'ISLR')
							LEFT OUTER JOIN  sai_sol_pago_retencion rfza ON (rfza.sopg_id = sp.sopg_id AND rfza.impu_id = 'FZA')
							LEFT OUTER JOIN  sai_acce_esp ac ON (cad.cadt_id_p_ac = ac.acce_id AND cad.cadt_cod_aesp = ac.aces_id AND ac.pres_anno = cad.pres_anno) 
							LEFT OUTER JOIN  sai_proy_a_esp py ON (cad.cadt_id_p_ac = py.proy_id AND cad.cadt_cod_aesp = py.paes_id AND py.pres_anno = cad.pres_anno)
							INNER JOIN sai_forma_1125 f11 ON ((f11.form_id_aesp = ac.aces_id AND f11.form_id_p_ac = ac.acce_id AND f11.pres_anno = ac.pres_anno) OR (f11.form_id_aesp = py.paes_id AND f11.form_id_p_ac = py.proy_id AND f11.pres_anno = py.pres_anno))									
							WHERE sp.esta_id!=15 AND 
								cdi.comp_comen NOT LIKE 'A-%'	
								AND cdi.comp_id LIKE 'coda%'
								AND cad.part_id != '4.11.05.02.00'
								AND cad.part_id != '4.03.18.01.00'	
								AND ((ca.esta_id != 15 AND cdi.comp_id!=15 ". $WhereFecha. ") OR (ca.esta_id = 15 AND cdi.comp_id = 15". $WhereFecha2. ") OR (ca.esta_id = 15 AND cdi.comp_id = 15". $WhereFecha3. "))
								AND (ptr.nro_cuenta_emisor='".$params['cuentaBancaria']."')".$whereDetalleSolicitudPago." 
							AND cdi.comp_fec_emis IN (SELECT MAX(comp_fec_emis) FROM sai_comp_diario WHERE comp_doc_id = sp.sopg_id AND comp_id LIKE 'coda%' AND esta_id!=15)

										
-- CHEQUE

UNION SELECT 
							sdi.docg_monto_base AS monto_base,
							sdi.docg_monto_iva AS iva,
							sp.sopg_detalle	AS detalle_solicitud,
							span.soan_otro_deta AS documento_asociado,		
							TO_CHAR(ca.caus_fecha, 'DD/MM/YYYY') AS fecha_causado,
							trim(sp.sopg_factura) AS factura, 
							cad.cadt_monto AS monto_causado, 
							CASE 
								WHEN (substr(cad.part_id,0,6) = '4.11.0') THEN 
									(SELECT cpat_id FROM sai_convertidor WHERE part_id = cad.part_id LIMIT 1)
								ELSE 
									cad.part_id 
							END AS partida_causado,		
							p.paga_docu_id AS documento_pago,
							COALESCE(ch.nro_cheque,'') AS referencia, 
							COALESCE(to_char(p.paga_fecha, 'DD/MM/YYYY'),'') AS fecha_pagado,
							COALESCE(ch.monto_cheque,0) AS monto_cheque_tran,
							COALESCE(riva.sopg_ret_monto,0) AS riva,
							COALESCE(rislr.sopg_ret_monto,0) AS rislr,
							COALESCE(rltf.sopg_ret_monto,0) AS rltf,
							COALESCE(rfza.sopg_ret_monto,0) AS rfza,
							ts.nombre_sol AS tipo_solicitud,
							sp.comp_id AS compromiso, 
							sp.sopg_id AS documento_sopg,
							f11.fuente_financiamiento AS fte_financiamiento,	
							COALESCE(ac.centro_gestor, '') || COALESCE(py.centro_gestor, '') || '/' || COALESCE(ac.centro_costo, '') || COALESCE(py.centro_costo, '') AS proy_acc,
							cdi.comp_id AS asiento	
							FROM sai_sol_pago sp
							INNER JOIN sai_tipo_solicitud ts ON (sp.sopg_tp_solicitud = ts.id_sol ".$whereTipoSolicitudPago.") 	
							INNER JOIN sai_causado ca ON (ca.caus_docu_id = sp.sopg_id AND ca.esta_id != 2)
							INNER JOIN sai_causad_det cad ON (ca.caus_id = cad.caus_id AND ca.pres_anno = cad.pres_anno)
							INNER JOIN sai_comp_diario cdi ON (cdi.comp_doc_id = ca.caus_docu_id)
							INNER JOIN sai_pago_cheque pch ON (pch.docg_id=sp.sopg_id AND pch.esta_id!=15 AND pch.esta_id!=2)
							INNER JOIN sai_cheque ch ON (sp.sopg_id = ch.docg_id AND ch.estatus_cheque != 15)
							INNER JOIN sai_chequera cq ON (ch.nro_chequera = cq.nro_chequera)																		
							LEFT OUTER JOIN  sai_sol_pago_anexo span ON (sp.sopg_id=span.sopg_id)									
							LEFT OUTER JOIN sai_pagado p ON (p.esta_id != 15 AND p.esta_id != 2 AND p.paga_docu_id = pch.pgch_id)
							LEFT OUTER JOIN  sai_docu_iva sdi ON (sp.sopg_id = sdi.docg_id)									
							LEFT OUTER JOIN  sai_sol_pago_retencion riva ON (riva.sopg_id = sp.sopg_id AND riva.impu_id = 'IVA')
							LEFT OUTER JOIN  sai_sol_pago_retencion rltf ON (rltf.sopg_id = sp.sopg_id AND rltf.impu_id = 'LTF')
							LEFT OUTER JOIN  sai_sol_pago_retencion rislr ON (rislr.sopg_id = sp.sopg_id AND rislr.impu_id = 'ISLR')
							LEFT OUTER JOIN  sai_sol_pago_retencion rfza ON (rfza.sopg_id = sp.sopg_id AND rfza.impu_id = 'FZA')
							LEFT OUTER JOIN  sai_acce_esp ac ON (cad.cadt_id_p_ac = ac.acce_id AND cad.cadt_cod_aesp = ac.aces_id AND ac.pres_anno = cad.pres_anno) 
							LEFT OUTER JOIN  sai_proy_a_esp py ON (cad.cadt_id_p_ac = py.proy_id AND cad.cadt_cod_aesp = py.paes_id AND py.pres_anno = cad.pres_anno)
							INNER JOIN sai_forma_1125 f11 ON ((f11.form_id_aesp = ac.aces_id AND f11.form_id_p_ac = ac.acce_id AND f11.pres_anno = ac.pres_anno) OR (f11.form_id_aesp = py.paes_id AND f11.form_id_p_ac = py.proy_id AND f11.pres_anno = py.pres_anno))									
							WHERE sp.esta_id!=15 AND 
								cdi.comp_comen NOT LIKE 'A-%'	
								AND cdi.comp_id LIKE 'coda%'
								AND cad.part_id != '4.11.05.02.00'
								AND cad.part_id != '4.03.18.01.00'	
								AND ((ca.esta_id != 15 AND cdi.comp_id!=15 ". $WhereFecha. ") OR (ca.esta_id = 15 AND cdi.comp_id = 15". $WhereFecha2. ") OR (ca.esta_id = 15 AND cdi.comp_id = 15". $WhereFecha3. "))
								AND (cq.ctab_numero='".$params['cuentaBancaria']."')".$whereDetalleSolicitudPago." 
							AND cdi.comp_fec_emis IN (SELECT MAX(comp_fec_emis) FROM sai_comp_diario WHERE comp_doc_id = sp.sopg_id AND comp_id LIKE 'coda%' AND esta_id!=15)
																
--CODI
										
										
										
							UNION
						
							SELECT 
								0 AS monto_base,
								0 AS iva,									
								COALESCE(sp.sopg_detalle,'') || COALESCE(cdi.comp_comen,'') AS detalle_solicitud,
								'' AS documento_asociado,													
								TO_CHAR(ca.caus_fecha, 'DD/MM/YYYY') AS fecha_causado,
								trim(sp.sopg_factura) AS factura, 
								cad.cadt_monto AS monto_causado, 
								CASE 
									WHEN (substr(cad.part_id,0,6) = '4.11.0') THEN 
										(SELECT cpat_id FROM sai_convertidor WHERE part_id = cad.part_id LIMIT 1)
									ELSE 
										cad.part_id 
								END AS partida_causado,		
								p.paga_docu_id AS documento_pago,
								'-' AS referencia, 
								COALESCE(to_char(p.paga_fecha, 'DD/MM/YYYY'),'') AS fecha_pagado,
								0 AS monto_cheque_tran,
								COALESCE(riva.sopg_ret_monto,0) AS riva,
								COALESCE(rislr.sopg_ret_monto,0) AS rislr,
								COALESCE(rltf.sopg_ret_monto,0) AS rltf,
								COALESCE(rfza.sopg_ret_monto,0) AS rfza,
								ts.nombre_sol AS tipo_solicitud,
								codi.nro_compromiso AS compromiso, 
								sp.sopg_id AS documento_sopg,
							f11.fuente_financiamiento AS fte_financiamiento,				
								COALESCE(ac.centro_gestor, '') || COALESCE(py.centro_gestor, '') || '/' || COALESCE(ac.centro_costo, '') || COALESCE(py.centro_costo, '') AS proy_acc,
								cdi.comp_id AS asiento		
							FROM sai_codi codi
							INNER JOIN sai_comp_diario cdi  ON (codi.comp_id = cdi.comp_id) 
							INNER JOIN sai_ctabanco_saldo ctb ON (cdi.comp_id = ctb.docg_id) 	
							LEFT OUTER JOIN sai_sol_pago sp ON (sp.sopg_id = LOWER(codi.comp_doc_id) ".$whereDetalleSolicitudPago.") 		
							LEFT OUTER JOIN sai_tipo_solicitud ts ON (sp.sopg_tp_solicitud = ts.id_sol ".$whereTipoSolicitudPago.") 	
							INNER JOIN sai_causado ca ON (ca.caus_docu_id = codi.comp_id AND ca.esta_id != 2)
							INNER JOIN sai_causad_det cad ON (ca.caus_id = cad.caus_id AND ca.pres_anno = cad.pres_anno)
							LEFT OUTER JOIN sai_pagado p ON (p.esta_id != 15 AND p.esta_id != 2 AND p.paga_docu_id = codi.comp_id)
							LEFT OUTER JOIN  sai_comp co ON (sp.comp_id = co.comp_id)
							LEFT OUTER JOIN  sai_sol_pago_retencion riva ON (riva.sopg_id = sp.sopg_id AND riva.impu_id = 'IVA')
							LEFT OUTER JOIN  sai_sol_pago_retencion rltf ON (rltf.sopg_id = sp.sopg_id AND rltf.impu_id = 'LTF')
							LEFT OUTER JOIN  sai_sol_pago_retencion rislr ON (rislr.sopg_id = sp.sopg_id AND rislr.impu_id = 'ISLR')
							LEFT OUTER JOIN  sai_sol_pago_retencion rfza ON (rfza.sopg_id = sp.sopg_id AND rfza.impu_id = 'FZA')
							LEFT OUTER JOIN  sai_acce_esp ac ON (cad.cadt_id_p_ac = ac.acce_id AND cad.cadt_cod_aesp = ac.aces_id AND ac.pres_anno = cad.pres_anno) 
							LEFT OUTER JOIN  sai_proy_a_esp py ON (cad.cadt_id_p_ac = py.proy_id AND cad.cadt_cod_aesp = py.paes_id AND py.pres_anno = cad.pres_anno)
							INNER JOIN sai_forma_1125 f11 ON ((f11.form_id_aesp = ac.aces_id AND f11.form_id_p_ac = ac.acce_id AND f11.pres_anno = ac.pres_anno) OR (f11.form_id_aesp = py.paes_id AND f11.form_id_p_ac = py.proy_id AND f11.pres_anno = py.pres_anno))									
							WHERE cdi.esta_id!=15
								AND ((ca.esta_id != 15 ". $WhereFecha. ") OR (ca.esta_id = 15". $WhereFecha2. ") OR (ca.esta_id = 15". $WhereFecha3. "))
								AND ctb.ctab_numero = '".$params['cuentaBancaria']."'
								AND cdi.comp_id LIKE 'codi%' 
								AND cdi.comp_id NOT LIKE 'codi%08'".$whereDetalleCodi;
	
	//error_log(print_r($query,true));
			/*****Ejecución*****/
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
			$reporte = array();
			$i=0;
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				if (strcmp(substr($row["partida_causado"], 0,6),'4.11.0')!=0) {
					$sum_montoP += $row["monto_causado"];
				}
	
				if (strcmp(substr($row["documento_sopg"],0,4),'-')==0)
					$documento ="";
				else
					$documento = $row["documento_sopg"];
	
				/*if (strlen($row["beneficiario"])>2)
					$reporte[$i]['beneficiario'] = $row["beneficiario"];
				else if (strlen($row["beneficiariop"])>2)
					$reporte[$i]['beneficiario'] = $row["beneficiariop"];
				else
					$reporte[$i]['beneficiario'] = $row["beneficiariov"];*/
	
	
				$monto = 0;
	
				$monto = 0;
				if (strcmp($codigo_documentoCh,$documento)!=0)  {
					// Mostrar sólo el monto del cheque una vez
					$codigo_documentoCh = $documento;
					$monto = $row["monto_cheque_tran"];
				}
	
				if (strcmp(substr($row["partida_causado"], 0,6),'4.11.0') != 0)
					$sum_montoCH += $monto;
	
				$iva = 0;
				$riva = 0;
				$islr = 0;
				$ltf = 0;
				$fianza = 0;
				$monto_base = "";
				if (strcmp($codigo_documento,$documento)!=0) {
					// Mostrar sólo las retenciones para una partida del sopg distinta a la del IVA
					$codigo_documento = $documento;
					$impuesto = trim($row['impu_id']);
					$iva = $row["iva"];
					$islr = $row["rislr"];
					$ltf = $row["rltf"];
					$fianza=$row["rfza"];
					$riva = $row["riva"];
				}
/*				if (strcmp(substr($row["partida_causado"], 0,10),'4.03.18.01')==0 || strcmp(substr($row["partida_causado"], 0,10),'4.11.05.02.00')==0) {
					// Mostrar sólo la retención del iva para la partida del IVA en el sopg
					$riva = $row["riva"];
					$iva = 0;
				}*/
	
				$sum_montoIVA += $row["riva"];
				$sum_montoISLR += $row["rislr"];
				$sum_montoLTF += $row["rltf"];
				$sum_montoFIANZA += $row["rfza"];
	
				$reporte[$i]['actividad_compromiso'] = $row['actividad_compromiso'];	
				$reporte[$i]['monto_base'] = $row['monto_base'];
				$reporte[$i]['iva'] = $iva;								
				$reporte[$i]['asiento'] = $row['asiento'];
				$reporte[$i]['tipo_solicitud'] = $row['tipo_solicitud'];
				$reporte[$i]['detalle_solicitud'] = $row['detalle_solicitud'];
				
				$reporte[$i]['compromiso'] = $row['compromiso'];
				$reporte[$i]['fte_financiamiento'] = $row['fte_financiamiento'];
				$reporte[$i]['tipo_solicitud'] = $row['tipo_solicitud'];

				$reporte[$i]['fecha_causado'] = $row['fecha_causado'];
				$reporte[$i]['monto_causado'] = $row['monto_causado'];
				$reporte[$i]['partida_causado'] = $row['partida_causado'];
				
				$reporte[$i]['factura'] = $row['factura'];
				$reporte[$i]['documento_sopg'] = $row['documento_sopg'];
				$reporte[$i]['documento_asociado'] = $row['documento_asociado'];				
				$reporte[$i]['documento_pago'] = $row['documento_pago'];
				$reporte[$i]['proy_acc'] = $row['proy_acc'];
				$reporte[$i]['referencia'] = $row['referencia'];
				$reporte[$i]['fecha_pagado'] = $row['fecha_pagado'];
				$reporte[$i]['monto_cheque_tran'] = $monto;
				$reporte[$i]['riva'] = $riva;
				$reporte[$i]['rislr'] = $islr;
				$reporte[$i]['rltf'] = $ltf;
				$reporte[$i]['rfza'] = $fianza;
				$reporte[$i]['sum_montoP'] = $sum_montoP;
				$reporte[$i]['sum_montoCH'] = $sum_montoCH;
				$reporte[$i]['sum_montoLTF'] = $sum_montoLTF;
				$reporte[$i]['sum_montoIVA'] = $sum_montoIVA;
				$reporte[$i]['sum_montoISLR'] = $sum_montoISLR;
				$reporte[$i]['sum_montoFIANZA'] = $sum_montoFIANZA;
				$i++;
			}
				
	
			$GLOBALS['SafiRequestVars']['descripcion_tipo'] = $descripcion_tipo;
			$GLOBALS['SafiRequestVars']['descripcion_fecha'] = $descripcion_fecha;
	//error_log(print_r($reporte,true));
			return $reporte;
	
		}
		catch(Exception $e){
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetCuentaCausadoPagado(array $params){
		try{
			$preMsg = "Error al obtener resultados de la búsqueda";
			$queryWhere = "";
	
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
	
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
	
			$query = "
						SELECT
							s.fecha, 
							s.comp_id,
							s.comentario,
							s.monto,
							s.cpat_id,
							s.sopg,
							s.fuente_financiamiento 
						FROM
							(SELECT 
								max(scd.comp_fec) AS fecha, 
								max(scd.comp_id) AS comp_id,
								max(scd.comp_comen) AS comentario,
								SUM(src.rcomp_debe) - SUM(src.rcomp_haber) AS monto,
								src.cpat_id AS cpat_id,
								CASE 
									WHEN (substring(comp_doc_id, 1, 4)='sopg') THEN
										comp_doc_id
									WHEN (substring(comp_doc_id, 1, 4)='pgch') THEN
										(SELECT
										 	docg_id
										  FROM sai_pago_cheque
										  WHERE pgch_id=comp_doc_id)
									WHEN substring(comp_doc_id, 1, 4)='tran' THEN
										(SELECT 
											docg_id
										 FROM sai_pago_transferencia
										 WHERE trans_id=comp_doc_id)
								END AS sopg,
								sf1125.fuente_financiamiento 
							FROM 
								sai_reng_comp src
								INNER JOIN sai_comp_diario scd ON (scd.comp_id = src.comp_id)
								LEFT OUTER JOIN sai_forma_1125 sf1125 ON (src.pr_ac_tipo = sf1125.form_tipo AND src.pr_ac = sf1125.form_id_p_ac AND src.a_esp = sf1125.form_id_aesp)
							WHERE
								scd.esta_id <> '15' ".
								((isset($params['idCuentaContable']) && $params['idCuentaContable'] != '-1')?" AND src.cpat_id = '".$params['idCuentaContable']."' ":"").
								((isset($params['fechaInicio']) && $params['fechaInicio'] != '' && isset($params['fechaFin']) && $params['fechaFin'] !='')?" AND scd.comp_fec BETWEEN TO_DATE('".$params['fechaInicio']."', 'DD/MM/YYYY') AND TO_DATE('".$params['fechaFin']."', 'DD/MM/YYYY') ":"").
							"GROUP BY 
								src.cpat_id,
								sopg,
								sf1125.fuente_financiamiento
							) AS s
						WHERE
							s.sopg IS NOT NULL AND s.sopg <> ''
						UNION
						SELECT
							s.fecha, 
							s.comp_id,
							s.comentario,
							s.monto,
							s.cpat_id,
							s.sopg,
							s.fuente_financiamiento 
						FROM
							(SELECT 
								scd.comp_fec AS fecha, 
								scd.comp_id AS comp_id,
								scd.comp_comen AS comentario,
								SUM(src.rcomp_debe)- SUM(src.rcomp_haber) AS monto,
								src.cpat_id AS cpat_id,
								CASE 
									WHEN (substring(comp_doc_id, 1, 4)='sopg') THEN
										comp_doc_id
									WHEN (substring(comp_doc_id, 1, 4)='pgch') THEN
										(SELECT
										 	docg_id
										  FROM sai_pago_cheque
										  WHERE pgch_id=comp_doc_id)
									WHEN substring(comp_doc_id, 1, 4)='tran' THEN
										(SELECT 
											docg_id
										 FROM sai_pago_transferencia
										 WHERE trans_id=comp_doc_id)
								END AS sopg,
								sf1125.fuente_financiamiento 
							FROM 
								sai_reng_comp src
								INNER JOIN sai_comp_diario scd ON (scd.comp_id = src.comp_id)
								LEFT OUTER JOIN sai_forma_1125 sf1125 ON (src.pr_ac_tipo = sf1125.form_tipo AND src.pr_ac = sf1125.form_id_p_ac AND src.a_esp = sf1125.form_id_aesp)
							WHERE
								scd.esta_id <> '15' ".
								((isset($params['idCuentaContable']) && $params['idCuentaContable'] != '-1')?" AND src.cpat_id = '".$params['idCuentaContable']."' ":"").
								((isset($params['fechaInicio']) && $params['fechaInicio'] != '' && isset($params['fechaFin']) && $params['fechaFin'] !='')?" AND scd.comp_fec BETWEEN TO_DATE('".$params['fechaInicio']."', 'DD/MM/YYYY') AND TO_DATE('".$params['fechaFin']."', 'DD/MM/YYYY') ":"").
							"GROUP BY
								scd.comp_fec,
								scd.comp_id,
								scd.comp_comen,
								src.cpat_id,
								sopg,
								sf1125.fuente_financiamiento
							) AS s
						WHERE
							s.sopg IS NULL OR s.sopg = ''
						ORDER by fecha";
	
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
			$auxiliar = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				/*$registro = array();
				$registro["fecha"] = $row["fecha"];
				$registro["comp_id"] = $row["comp_id"];
				$registro["comentario"] = $row["comentario"];
				$registro["monto"] = $row["monto"];
				$registro["cpat_id"] = $row["cpat_id"];
				$registro["sopg"] = $row["sopg"];
				$registro["fuente_financiamiento"] = $row["fuente_financiamiento"];
				$auxiliar[] = $registro;*/
				$auxiliar[] = $row;			
			}
	
			return $auxiliar;
	
		} catch(Exception $e) {
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}	
	
}