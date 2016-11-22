<?php
require_once(SAFI_ENTIDADES_PATH . '/cuentaBanco.php');
require_once(SAFI_ENTIDADES_PATH . '/tipocuentabancaria.php');
require_once(SAFI_ENTIDADES_PATH . '/cuentaContable.php');
//require_once(SAFI_INCLUDE_PATH . '/arreglos_pg.php');

class SafiModeloReporteTesoreria
{
	public static function GetBusqueda(array $params)
	{
		try
		{
			$preMsg = "Error al obtener resultados de la búsqueda";
			$queryWhere = "";
				
			if($params == null)
			throw new Exception($preMsg." El parámetro params es nulo.");

			if(!is_array($params))
			throw new Exception($preMsg." El parámetro params no es un arreglo.");
				
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
				
			if($resultTransaction === false)
			throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			if (strcmp($params['busquedaInicial'], '1')==0) {
					
				/*Búsqueda inicial*/
					
				$nucleo = "SELECT
								ch.id_cheque AS id_cheque,
								ch.nro_cheque AS nro_cheque,
								p.pgch_id AS pgch_id,
								mv.conciliado AS conciliado,
								cq.ctab_numero AS nro_cuenta_bancaria,
								ch.monto_cheque AS monto_cheque,
								TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY') AS fecha_emision,
								ch.fechaemision_cheque AS fecha_emision_fecha,
								UPPER(ch.beneficiario_cheque) AS nombre_beneficiario,
								ch.docg_id AS sopg,
								ch.ci_rif_beneficiario_cheque AS ci_rif,
								e.esta_id AS id_estatus,
								e.esta_nombre AS estatus_nombre,
								ch.estatus_cheque AS estatus_cheque
							FROM sai_cheque ch
							INNER JOIN sai_chequera cq ON (ch.nro_chequera = cq.nro_chequera)
							INNER JOIN sai_estado e ON (ch.estatus_cheque = e.esta_id)
							INNER JOIN sai_pago_cheque p ON (p.docg_id = ch.docg_id)
							INNER JOIN sai_mov_cta_banco mv ON (mv.docg_id = ch.docg_id) 
							WHERE  
								ch.estatus_cheque = 44 AND
								p.esta_id != 2";				

				$query = "SELECT
							tn.id_cheque AS id_cheque,
							tn.estatus_cheque AS estatus_cheque,
							tn.pgch_id AS pgch_id, 
							tn.conciliado AS conciliado, 
							tn.nro_cheque AS nro_cheque,
							tn.nro_cuenta_bancaria AS nro_cuenta_bancaria, 
							tn.monto_cheque AS monto_cheque,
							tn.fecha_emision AS fecha_emision,
							tn.nombre_beneficiario AS nombre_beneficiario,
							tn.sopg AS sopg,
							tn.ci_rif AS ci_rif,
							tn.id_estatus AS id_estatus, 
							tn.estatus_nombre AS estatus_nombre,
							TO_CHAR(impreso.fecha_accion_cheque, 'DD/MM/YYYY') AS fecha_impreso, 
							TO_CHAR(anulado.fecha_accion_cheque, 'DD/MM/YYYY') AS fecha_anulado,
							anulado.comentario AS comentario

						FROM (".$nucleo.") AS tn
						LEFT OUTER JOIN sai_cheque_estados impreso ON (impreso.id_cheque = tn.id_cheque AND impreso.estatus_cheque = 45)
						LEFT OUTER JOIN sai_cheque_estados anulado ON (anulado.id_cheque = tn.id_cheque AND anulado.estatus_cheque = 15)
		
						ORDER BY tn.fecha_emision_fecha";	

				/*Fin búsqueda inicial*/
			}
			else {
				/*Definición de condiciones: cheque y transferencia*/
				if(isset($params['fechaInicio']) && $params['fechaInicio'] != '' && isset($params['fechaFin']) && $params['fechaFin'] !='') {
					$fechaInicio = explode ('/',$params['fechaInicio']);
					$fechaFin = explode ('/',$params['fechaFin']);
					if (strcmp($params['tipoBusqueda'], 'c')==0)
					$queryWhere =  " AND ch.fechaemision_cheque BETWEEN TO_TIMESTAMP('".$fechaInicio[2].'-'.$fechaInicio[1].'-'.$fechaInicio[0]." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]."', 'YYYY-MM-DD HH24:MI:SS')";
					else if (strcmp($params['tipoBusqueda'], 't')==0)
					$queryWhere =  " AND t.trans_fecha BETWEEN TO_TIMESTAMP('".$fechaInicio[2].'-'.$fechaInicio[1].'-'.$fechaInicio[0]." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]." 23:59:59', 'YYYY-MM-DD HH24:MI:SS')";
					else if (strcmp($params['tipoBusqueda'], 'cyt')==0) {
						$queryWhereCheque =  " AND ch.fechaemision_cheque BETWEEN TO_TIMESTAMP('".$fechaInicio[2].'-'.$fechaInicio[1].'-'.$fechaInicio[0]." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]." 23:59:59', 'YYYY-MM-DD HH24:MI:SS')";
						$queryWhereTransferencia =  " AND t.trans_fecha BETWEEN TO_TIMESTAMP('".$fechaInicio[2].'-'.$fechaInicio[1].'-'.$fechaInicio[0]." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]." 23:59:59', 'YYYY-MM-DD HH24:MI:SS')";
					}
				}
				if(isset($params['cuentaBancaria']) && $params['cuentaBancaria']!= -1)
				if (strcmp($params['tipoBusqueda'], 'c')==0)
				$queryWhere .=  " AND cq.ctab_numero='".$params['cuentaBancaria']."'";
				else if (strcmp($params['tipoBusqueda'], 't')==0)
				$queryWhere .=  " AND t.nro_cuenta_emisor='".$params['cuentaBancaria']."'";
				else if (strcmp($params['tipoBusqueda'], 'cyt')==0) {
					$queryWhereCheque .=  " AND cq.ctab_numero='".$params['cuentaBancaria']."'";
					$queryWhereTransferencia .=  " AND t.nro_cuenta_emisor='".$params['cuentaBancaria']."'";
				}

				if(isset($params['nroReferencia']))
				if (strcmp($params['tipoBusqueda'], 'c')==0)
				$queryWhere .=  " AND ch.nro_cheque='".$params['nroReferencia']."'";
				else if (strcmp($params['tipoBusqueda'], 't')==0)
				$queryWhere .=  " AND t.nro_referencia = '".$params['nroReferencia']."'";

				if(isset($params['beneficiario'])) {
					$beneficiario = explode (":",$params['beneficiario']);
					$idBeneficiario = $beneficiario[0];
					$nombreBeneficiario = $beneficiario[1];
					if (strcmp($params['tipoBusqueda'], 'c')==0)
					//	$queryWhere .=  "  AND (position('".strtoupper(trim($nombreBeneficiario))."' IN UPPER(ch.beneficiario_cheque)) != 0 OR  position('".trim($idBeneficiario)."' IN ci_rif_beneficiario_cheque) != 0)";
						$queryWhere .=  "  AND (position('".trim($idBeneficiario)."' IN ci_rif_beneficiario_cheque) != 0)";					
					else if (strcmp($params['tipoBusqueda'], 't')==0)
						//$queryWhere .=  "  AND (position('".strtoupper(trim($nombreBeneficiario))."' IN UPPER(t.beneficiario)) != 0 OR  position('".trim($idBeneficiario)."' IN t.rif_ci) != 0)";
						$queryWhere .=  "  AND (position('".trim($idBeneficiario)."' IN t.rif_ci) != 0)";					
					else if (strcmp($params['tipoBusqueda'], 'cyt')==0) {
						//$queryWhereCheque .= "  AND (position('".strtoupper(trim($nombreBeneficiario))."' IN UPPER(ch.beneficiario_cheque)) != 0 OR  position('".trim($idBeneficiario)."' IN ci_rif_beneficiario_cheque) != 0)";
						//$queryWhereTransferencia .= "  AND (position('".strtoupper(trim($nombreBeneficiario))."' IN UPPER(t.beneficiario)) != 0 OR  position('".trim($idBeneficiario)."' IN t.rif_ci) != 0)";
						$queryWhereCheque .= "  AND (position('".trim($idBeneficiario)."' IN ci_rif_beneficiario_cheque) != 0)";
						$queryWhereTransferencia .= "  AND (position('".trim($idBeneficiario)."' IN t.rif_ci) != 0)";
						
					}
				}
					

				/*Caso Cheque*/
				if (strcmp($params['tipoBusqueda'], 'c')==0) {
					/*Definición de condiciones*/
					if(isset($params['estatusCheque']) && $params['estatusCheque'] != -1)
					$queryWhere .=  " AND ch.estatus_cheque=".$params['estatusCheque'];

					if(isset($params['fechaInicioEmisionAnulado'])) {
						$fechaInicio = explode ('/',$params['fechaInicioEmisionAnulado']);
						$queryWhere .=  "  AND ch.fechaemision_cheque < TO_TIMESTAMP('".$fechaInicio[2].'-'.$fechaInicio[1].'-'.$fechaInicio[0]." 23:59:59','YYYY-MM-DD HH24:MI:SS')";
					}
						
					if(isset($params['fechaFinEmisionAnulado'])) {
						$fechaFin = explode ('/',$params['fechaFinEmisionAnulado']);
						$queryWhereAnulado =  " WHERE anulado.fecha_accion_cheque >= TO_TIMESTAMP('".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]." 23:59:59','YYYY-MM-DD HH24:MI:SS')";
					}
						
					if(isset($params['fechaInicioAnulacion']) && isset($params['fechaFinAnulacion'])) {
						$fechaInicio = explode ('/',$params['fechaInicioAnulacion']);
						$fechaFin = explode ('/',$params['fechaFinAnulacion']);
						$queryWhereAnulado =  " WHERE anulado.fecha_accion_cheque BETWEEN TO_TIMESTAMP('".$fechaInicio[2].'-'.$fechaInicio[1].'-'.$fechaInicio[0]." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]." 23:59:59', 'YYYY-MM-DD HH24:MI:SS')";
					}
						
					if(isset($params['anoEmisionCheque']) && isset($params['anoFinCheque'])) {
						$queryWhere .=  "  AND DATE_PART('year',ch.fechaemision_cheque) = ".$params['anoEmisionCheque'];
						$queryWhereAnulado = " WHERE DATE_PART('year',anulado.fecha_accion_cheque) = '".$params['anoFinCheque']."' ";
					}
						
					$nucleo = "SELECT
									ch.id_cheque AS id_cheque,
									ch.nro_cheque AS nro_cheque,
									p.pgch_id AS pgch_id,
									mv.conciliado AS conciliado,
									cq.ctab_numero AS nro_cuenta_bancaria,
									ch.monto_cheque AS monto_cheque,
									TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY') AS fecha_emision,
									ch.fechaemision_cheque AS fecha_emision_fecha,
									UPPER(ch.beneficiario_cheque) AS nombre_beneficiario,
									ch.docg_id AS sopg,
									ch.ci_rif_beneficiario_cheque AS ci_rif,
									e.esta_nombre AS estatus_nombre,
									e.esta_id AS id_estatus,
									ch.estatus_cheque AS estatus_cheque
							FROM sai_cheque ch
							INNER JOIN sai_chequera cq ON (ch.nro_chequera=cq.nro_chequera)
							INNER JOIN sai_estado e ON (ch.estatus_cheque=e.esta_id)
							INNER JOIN sai_pago_cheque p ON (p.docg_id=ch.docg_id)
							INNER JOIN sai_mov_cta_banco mv ON (mv.docg_id=ch.docg_id) 
							WHERE  
								mv.conciliado IN (50,51)". $queryWhere;


					$query = "SELECT
							tn.id_cheque AS id_cheque,
							tn.estatus_cheque AS estatus_cheque,
							tn.pgch_id AS pgch_id, 
							tn.conciliado AS conciliado, 
							tn.nro_cheque AS nro_cheque,
							tn.nro_cuenta_bancaria AS nro_cuenta_bancaria, 
							tn.monto_cheque AS monto_cheque,
							tn.fecha_emision AS fecha_emision,
							tn.nombre_beneficiario AS nombre_beneficiario,
							tn.sopg AS sopg,
							tn.ci_rif AS ci_rif,
							tn.id_estatus AS id_estatus, 
							tn.estatus_nombre AS estatus_nombre,
							TO_CHAR(impreso.fecha_accion_cheque, 'DD/MM/YYYY') AS fecha_impreso, 
							TO_CHAR(anulado.fecha_accion_cheque, 'DD/MM/YYYY') AS fecha_anulado,
							anulado.comentario AS comentario

						FROM (".$nucleo.") AS tn										
						LEFT OUTER JOIN sai_cheque_estados impreso ON (impreso.id_cheque=tn.id_cheque AND impreso.estatus_cheque=45)
						LEFT OUTER JOIN sai_cheque_estados anulado ON (anulado.id_cheque=tn.id_cheque AND anulado.estatus_cheque=15)".

					$queryWhereAnulado."
						ORDER BY tn.fecha_emision_fecha";
				}

				else if (strcmp($params['tipoBusqueda'], 't')==0) {
					$query ="SELECT
							t.nro_referencia AS id_cheque, 
							t.nro_referencia AS nro_cheque,
							t.trans_id AS pgch_id, 
							mv.conciliado AS conciliado,
							t.nro_cuenta_emisor AS nro_cuenta_bancaria,
							t.trans_monto AS monto_cheque,
							TO_CHAR(t.trans_fecha, 'DD/MM/YYYY') AS fecha_emision,
							UPPER(t.beneficiario) AS nombre_beneficiario,
							t.rif_ci AS ci_rif,
							t.docg_id AS sopg,
							e.esta_nombre AS estatus_nombre,
							e.esta_id AS id_estatus,
							'-' AS estatus_cheque, 
							t.trans_asunto AS comentario 

				FROM sai_pago_transferencia t
				INNER JOIN sai_estado e ON (t.esta_id=e.esta_id)
				LEFT OUTER JOIN sai_mov_cta_banco mv ON (mv.docg_id=t.docg_id)
				WHERE t.esta_id != 2 ". $queryWhere ."
				ORDER BY t.trans_fecha";
				}
					
				else if (strcmp($params['tipoBusqueda'], 'cyt')==0) {
					//Cheque
					$nucleo = "SELECT
									ch.id_cheque AS id_cheque,
									ch.nro_cheque AS nro_cheque,
									p.pgch_id AS pgch_id,
									mv.conciliado AS conciliado,
									cq.ctab_numero AS nro_cuenta_bancaria,
									ch.monto_cheque AS monto_cheque,
									TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY') AS fecha_emision,
									ch.fechaemision_cheque AS fecha_emision_fecha,
									UPPER(ch.beneficiario_cheque) AS nombre_beneficiario,
									ch.docg_id AS sopg,
									ch.ci_rif_beneficiario_cheque AS ci_rif,
									e.esta_nombre AS estatus_nombre,
									e.esta_id AS id_estatus,
									ch.estatus_cheque AS estatus_cheque
							FROM sai_cheque ch
							INNER JOIN sai_chequera cq ON (ch.nro_chequera=cq.nro_chequera)
							INNER JOIN sai_estado e ON (ch.estatus_cheque=e.esta_id)
							INNER JOIN sai_pago_cheque p ON (p.docg_id=ch.docg_id)
							INNER JOIN sai_mov_cta_banco mv ON (mv.docg_id=ch.docg_id) 
							WHERE  
								p.esta_id!=2". $queryWhereCheque;


					$query = "SELECT
							tn.id_cheque AS id_cheque, 
							tn.estatus_cheque AS estatus_cheque,
							tn.pgch_id AS pgch_id, 
							tn.conciliado AS conciliado, 
							tn.nro_cheque AS nro_cheque,
							tn.nro_cuenta_bancaria AS nro_cuenta_bancaria, 
							tn.monto_cheque AS monto_cheque,
							tn.fecha_emision AS fecha_emision,
							tn.fecha_emision_fecha AS fecha_emision_fecha,
							tn.nombre_beneficiario AS nombre_beneficiario,
							tn.sopg AS sopg,
							tn.ci_rif AS ci_rif,
							tn.id_estatus AS id_estatus, 
							tn.estatus_nombre AS estatus_nombre,
							TO_CHAR(impreso.fecha_accion_cheque, 'DD/MM/YYYY') AS fecha_impreso, 
							TO_CHAR(anulado.fecha_accion_cheque, 'DD/MM/YYYY') AS fecha_anulado,
							anulado.comentario AS comentario
							
						FROM (".$nucleo.") AS tn										
						LEFT OUTER JOIN sai_cheque_estados impreso ON (impreso.id_cheque=tn.id_cheque AND impreso.estatus_cheque=45)
						LEFT OUTER JOIN sai_cheque_estados anulado ON (anulado.id_cheque=tn.id_cheque AND anulado.estatus_cheque=15)".

					$queryWhereAnulado;
						


					//Transferencia
					$query .= " UNION ";
						
					$query .=" SELECT
							'0' AS id_cheque,
							1 AS estatus_cheque,
							t.trans_id AS pgch_id,
							mv.conciliado AS conciliado,
							t.nro_referencia AS nro_cheque,
							t.nro_cuenta_emisor AS nro_cuenta_bancaria,
							t.trans_monto AS monto_cheque,
							TO_CHAR(t.trans_fecha, 'DD/MM/YYYY') AS fecha_emision,
							t.trans_fecha AS fecha_emision_fecha,							
							UPPER(t.beneficiario) AS nombre_beneficiario,
							t.docg_id AS sopg,
							t.rif_ci AS ci_rif,
							e.esta_id AS id_estatus,							
							e.esta_nombre AS estatus_nombre,
							'' AS fecha_impreso, 
							'' AS fecha_anulado,
							t.trans_asunto AS comentario
							
				FROM sai_pago_transferencia t
				INNER JOIN sai_estado e ON (t.esta_id=e.esta_id)
				LEFT OUTER JOIN sai_mov_cta_banco mv ON (mv.docg_id=t.docg_id)
				WHERE t.esta_id != 2 ". $queryWhereTransferencia ."
				ORDER BY fecha_emision_fecha ";
					//ORDER BY tn.fecha_emision_fecha" ORDER BY t.trans_fecha";
				}
				else { //No se especifica parámetros de búsqueda, se buscan cheques preemitidos
					$nucleo = "SELECT
								ch.id_cheque AS id_cheque,
								ch.nro_cheque AS nro_cheque,
								p.pgch_id AS pgch_id,
								mv.conciliado AS conciliado,
								cq.ctab_numero AS nro_cuenta_bancaria,
								ch.monto_cheque AS monto_cheque,
								TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY') AS fecha_emision,
								ch.fechaemision_cheque AS fecha_emision_fecha,
								UPPER(ch.beneficiario_cheque) AS nombre_beneficiario,
								ch.docg_id AS sopg,
								ch.ci_rif_beneficiario_cheque AS ci_rif,
								e.esta_id AS id_estatus,
								e.esta_nombre AS estatus_nombre,
								ch.estatus_cheque AS estatus_cheque
							FROM sai_cheque ch
							INNER JOIN sai_chequera cq ON (ch.nro_chequera = cq.nro_chequera)
							INNER JOIN sai_estado e ON (ch.estatus_cheque = e.esta_id)
							INNER JOIN sai_pago_cheque p ON (p.docg_id = ch.docg_id)
							INNER JOIN sai_mov_cta_banco mv ON (mv.docg_id = ch.docg_id) 
							WHERE  
								ch.estatus_cheque = 44 AND
								p.esta_id != 2";				
						
					$query = "SELECT
						tn.estatus_cheque AS estatus_cheque,
						tn.pgch_id AS pgch_id, 
						tn.conciliado AS conciliado, 
						tn.nro_cheque AS nro_cheque,
						tn.nro_cuenta_bancaria AS nro_cuenta_bancaria, 
						tn.monto_cheque AS monto_cheque,
						tn.fecha_emision AS fecha_emision,
						tn.nombre_beneficiario AS nombre_beneficiario,
						tn.sopg AS sopg,
						tn.ci_rif AS ci_rif,
						tn.id_estatus AS id_estatus, 
						tn.estatus_nombre AS estatus_nombre,
						TO_CHAR(impreso.fecha_accion_cheque, 'DD/MM/YYYY') AS fecha_impreso, 
						TO_CHAR(anulado.fecha_accion_cheque, 'DD/MM/YYYY') AS fecha_anulado,
						anulado.comentario AS comentario

					FROM (".$nucleo.") AS tn
					LEFT OUTER JOIN sai_cheque_estados impreso ON (impreso.id_cheque = tn.id_cheque AND impreso.estatus_cheque = 45)
					LEFT OUTER JOIN sai_cheque_estados anulado ON (anulado.id_cheque = tn.id_cheque AND anulado.estatus_cheque = 15)

					ORDER BY tn.fecha_emision_fecha";	
				}
			}

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$reporte = array();
			$i=0;
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$reporte[$i]['id_cheque'] = $row['id_cheque'];
				$reporte[$i]['nro_cheque'] = $row['nro_cheque'];
				$reporte[$i]['conciliado'] = $row['conciliado'];
				$reporte[$i]['nro_cuenta_bancaria'] = $row['nro_cuenta_bancaria'];
				$reporte[$i]['monto_cheque'] = $row['monto_cheque'];
				$reporte[$i]['fecha_emision'] = $row['fecha_emision'];
				$reporte[$i]['nombre_beneficiario'] = $row['nombre_beneficiario'];
				$reporte[$i]['sopg'] = $row['sopg'];
				$reporte[$i]['ci_rif'] = $row['ci_rif'];
				$reporte[$i]['estatus_nombre'] = $row['estatus_nombre'];
				$reporte[$i]['fecha_impreso'] = $row['fecha_impreso'];
				$reporte[$i]['fecha_anulado'] = $row['fecha_anulado'];
				//$reporte[$i]['cheque_entrega'] = $row['cheque_entrega'];
				$reporte[$i]['comentario'] = $row['comentario'];
				$reporte[$i]['id_estatus'] = $row['id_estatus'];
				$reporte[$i]['pgch_id'] = $row['pgch_id'];
				$i++;
			}
				
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
				
			if($result === false)
			throw new Exception("Error al realizar el commit en la función de búsqueda del cheque".
							utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));			
			return $reporte;
				
		}
		catch(Exception $e){
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;		
		}
	}


	public static function GetDetalleCheque(array $params)
	{
		try
		{
			$preMsg = "Error al obtener resultados de la búsqueda";
			$queryWhere = "";

			if($params == null)
			throw new Exception($preMsg." El parámetro params es nulo.");

			if(!is_array($params))
			throw new Exception($preMsg." El parámetro params no es un arreglo.");

			if (strcmp($params['idCheque'], '-1')==0) {
					
				/*Es anulación parcial - Se busca el último cheque impreso*/
					
				$query = "SELECT
							ch.id_cheque AS id_cheque, 
							ch.monto_cheque AS monto_cheque,
							ch.nro_cheque AS nro_cheque,
							ch.estatus_cheque AS estatus_cheque,									 
							ch.ci_rif_beneficiario_cheque AS ci_rif,
							UPPER(ch.beneficiario_cheque) AS beneficiario_cheque,
							TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY') AS fecha_emision,
							pgch_id AS pgch_id,
							pch.nro_cuenta AS nro_cuenta_bancaria,
							pch.docg_id AS sopg,
							pch.pgch_asunto AS asunto,
							pch.pgch_obs AS observaciones,
							b.banc_nombre AS nombre_banco,
							'' AS motivo_anulacion
						FROM sai_cheque ch
						INNER JOIN sai_pago_cheque pch ON (pch.docg_id = ch.docg_id)
						INNER JOIN sai_ctabanco ctb ON (pch.nro_cuenta=ctb.ctab_numero)	
						INNER JOIN sai_banco b ON (ctb.banc_id = b.banc_id)
						WHERE
							ch.estatus_cheque != 15
							AND pch.pgch_id='".$params['pgchId']."'";	
				/*Fin búsqueda inicial*/
			}
			else {
				$query = "SELECT
						ch.id_cheque AS id_cheque, 
						ch.monto_cheque AS monto_cheque,
						ch.nro_cheque AS nro_cheque,
						ch.estatus_cheque AS estatus_cheque,
						ch.ci_rif_beneficiario_cheque AS ci_rif, 
						UPPER(ch.beneficiario_cheque) AS beneficiario_cheque,
						TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY') AS fecha_emision,
						pgch_id AS pgch_id,
						pch.nro_cuenta AS nro_cuenta_bancaria,
						pch.docg_id AS sopg,
						pch.pgch_asunto AS asunto,
						pch.pgch_obs AS observaciones,
						b.banc_nombre AS nombre_banco,
						anulado.comentario AS motivo_anulacion
					FROM sai_cheque ch
					INNER JOIN sai_pago_cheque pch ON (pch.docg_id = ch.docg_id)
					INNER JOIN sai_ctabanco ctb ON (pch.nro_cuenta=ctb.ctab_numero)	
					INNER JOIN sai_banco b ON (ctb.banc_id = b.banc_id)
					LEFT OUTER JOIN sai_cheque_estados anulado ON (anulado.estatus_cheque = 15 AND anulado.id_cheque=ch.id_cheque)
					WHERE
						ch.id_cheque=".$params['idCheque'];	
			}
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			$reporte = array();
			$i=0;
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$reporte[$i]['id_cheque'] = $row['id_cheque'];
				$reporte[$i]['monto_cheque'] = $row['monto_cheque'];
				$reporte[$i]['nro_cheque'] = $row['nro_cheque'];
				$reporte[$i]['estatus_cheque'] = $row['estatus_cheque'];
				$reporte[$i]['nro_cuenta_bancaria'] = $row['nro_cuenta_bancaria'];
				$reporte[$i]['fecha_emision'] = $row['fecha_emision'];
				$reporte[$i]['beneficiario_cheque'] = $row['beneficiario_cheque'];
				$reporte[$i]['sopg'] = $row['sopg'];
				$reporte[$i]['ci_rif'] = $row['ci_rif'];
				$reporte[$i]['asunto'] = $row['asunto'];
				$reporte[$i]['observaciones'] = $row['observaciones'];
				$reporte[$i]['nombre_banco'] = $row['nombre_banco'];
				$reporte[$i]['pgch_id'] = $row['pgch_id'];
				$reporte[$i]['motivo_anulacion'] = $row['motivo_anulacion'];
				$i++;
			}

			return $reporte;

		}
		catch(Exception $e){
			error_log($e);
			return null;
		}
	}


	public static function ImprimirCheque(array $params)
	{
		try
		{
			$preMsg = "Error al obtener resultados de la búsqueda";
			$queryWhere = "";

			if($params == null)
			throw new Exception($preMsg." El parámetro params es nulo.");

			if(!is_array($params))
			throw new Exception($preMsg." El parámetro params no es un arreglo.");

			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
			throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				

			if (strcmp($params['sopg'], '')!=0) { /*Búsqueda datos del cheque*/
					
				$query = "SELECT
							ch.id_cheque AS id_cheque, 
							ch.monto_cheque AS monto_cheque,
							ch.nro_cheque AS nro_cheque,
							ch.estatus_cheque AS estatus_cheque,									 
							ch.ci_rif_beneficiario_cheque AS ci_rif,
							UPPER(ch.beneficiario_cheque) AS beneficiario_cheque,
							TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY') AS fecha_emision,
							pgch_id AS pgch_id,
							pch.nro_cuenta AS nro_cuenta_bancaria,
							pch.docg_id AS sopg,
							pch.pgch_asunto AS asunto,
							pch.pgch_obs AS observaciones,
							b.banc_nombre AS nombre_banco,
							'' AS motivo_anulacion
						FROM sai_cheque ch
						INNER JOIN sai_pago_cheque pch ON (pch.docg_id = ch.docg_id)
						INNER JOIN sai_ctabanco ctb ON (pch.nro_cuenta=ctb.ctab_numero)	
						INNER JOIN sai_banco b ON (ctb.banc_id = b.banc_id)
						WHERE
							ch.estatus_cheque != 15
							AND ch.docg_id='".$params['sopg']."'";	


				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				$reporte = array();
				while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
				{
					$reporte['id_cheque'] = $row['id_cheque'];
					$reporte['monto_cheque'] = $row['monto_cheque'];
					$reporte['nro_cheque'] = $row['nro_cheque'];
					$reporte['estatus_cheque'] = $row['estatus_cheque'];
					$reporte['nro_cuenta_bancaria'] = $row['nro_cuenta_bancaria'];
					$reporte['fecha_emision'] = $row['fecha_emision'];
					$reporte['beneficiario_cheque'] = $row['beneficiario_cheque'];
					$reporte['sopg'] = $row['sopg'];
					$reporte['ci_rif'] = $row['ci_rif'];
					$reporte['asunto'] = $row['asunto'];
					$reporte['observaciones'] = $row['observaciones'];
					$reporte['nombre_banco'] = $row['nombre_banco'];
					$reporte['pgch_id'] = $row['pgch_id'];
					$reporte['motivo_anulacion'] = $row['motivo_anulacion'];
				}
				/*Fin búsqueda datos cheque*/

				/*Inicio actualización de estados*/
				$query = " SELECT * FROM sai_modificar_estado_doc_genera('".$reporte['pgch_id']."',10) AS resultado ";
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));


				//Modificar el estado del cheque a "Emitido"
				$query = " SELECT * FROM sai_cambiar_estado_cheque('".$reporte['id_cheque']."',45, '') AS resultado ";
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					
				/*Fin actualización de estados*/


				/*Inicio búsqueda cuentas contables*/
				$query = "SELECT
							sopg_sub_espe 
						FROM sai_sol_pago_imputa
						WHERE sopg_id='".$reporte['sopg']."'
						AND sopg_sub_espe != '4.03.18.01.00'";

				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$reporte['partida'] = $row['sopg_sub_espe'];
				}

				$query = "SELECT
							cpat_pasivo_id,
							cpat_transitoria_id
						FROM sai_convertidor
						WHERE part_id='".$reporte['partida']."'";

				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$reporte['pasivo_id'] = $row['cpat_pasivo_id'];
					$reporte['transitoria_id'] = $row['cpat_transitoria_id'];
				}

				$query = "SELECT
							cpat_nombre 
						FROM sai_cue_pat
						WHERE cpat_id = '".$reporte['pasivo_id']."'";

				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$reporte['pasivo_nombre'] = $row["cpat_nombre"];
				}
				/*Fin búsqueda cuentas contables*/
				
				$result = $GLOBALS['SafiClassDb']->CommitTransaction();
					
				if($result === false)
				throw new Exception("Error al realizar el commit en la función de impresión del cheque".
								utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));				
				return $reporte;
			}
		}
		catch(Exception $e){
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;		
		}
	}
	
	public static function ReimprimirCheque(array $params)
	{
		try
		{
			$preMsg = "Error al obtener resultados de la búsqueda";
			$queryWhere = "";
	
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
	
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
	
			if (strcmp($params['idCheque'], '')!=0) { /*Búsqueda datos del cheque*/
					
				$query = "UPDATE
							sai_cheque
						SET estatus_cheque = 44
						WHERE
							docg_id = '".$params['sopg']."'
							AND id_cheque=".$params['idCheque'];
	
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				$query = "DELETE FROM
							sai_cheque_estados
						WHERE
							estatus_cheque = 45
							AND id_cheque=".$params['idCheque'];
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			return 1;
			}
		}
		catch(Exception $e){
			error_log($e);
			return null;
		}
	}	

	public static function AnularCheque(array $params)
	{
		try
		{
			$preMsg = "Error al obtener resultados de la búsqueda";
			$queryWhere = "";

			if($params == null)
			throw new Exception($preMsg." El parámetro params es nulo.");

			if(!is_array($params))
			throw new Exception($preMsg." El parámetro params no es un arreglo.");

			if (strcmp($params['idCheque'], '')!=0) { /*Búsqueda datos del cheque*/
					
				$query = "SELECT
								ch.id_cheque AS id_cheque, 
								ch.monto_cheque AS monto_cheque,
								ch.nro_cheque AS nro_cheque,
								ch.estatus_cheque AS estatus_cheque,									 
								ch.ci_rif_beneficiario_cheque AS ci_rif,
								UPPER(ch.beneficiario_cheque) AS beneficiario_cheque,
								TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY') AS fecha_emision,
								pgch_id AS pgch_id,
								pch.nro_cuenta AS nro_cuenta_bancaria,
								pch.docg_id AS sopg,
								pch.pgch_asunto AS asunto,
								pch.pgch_obs AS observaciones,
								b.banc_nombre AS nombre_banco
							FROM sai_cheque ch
							INNER JOIN sai_pago_cheque pch ON (pch.docg_id = ch.docg_id)
							INNER JOIN sai_ctabanco ctb ON (pch.nro_cuenta=ctb.ctab_numero)	
							INNER JOIN sai_banco b ON (ctb.banc_id = b.banc_id)
							WHERE
								ch.estatus_cheque = 45
								AND ch.id_cheque=".$params['idCheque'];	

				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				$reporte = array();
				while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$reporte['id_cheque'] = $row['id_cheque'];
					$reporte['monto_cheque'] = $row['monto_cheque'];
					$reporte['nro_cheque'] = $row['nro_cheque'];
					$reporte['estatus_cheque'] = $row['estatus_cheque'];
					$reporte['nro_cuenta_bancaria'] = $row['nro_cuenta_bancaria'];
					$reporte['fecha_emision'] = $row['fecha_emision'];
					$reporte['beneficiario_cheque'] = $row['beneficiario_cheque'];
					$reporte['sopg'] = $row['sopg'];
					$reporte['ci_rif'] = $row['ci_rif'];
					$reporte['asunto'] = $row['asunto'];
					$reporte['observaciones'] = $row['observaciones'];
					$reporte['nombre_banco'] = $row['nombre_banco'];
					$reporte['pgch_id'] = $row['pgch_id'];
				}
				/*Fin búsqueda datos cheque*/

				return $reporte;
			}
		}
		catch(Exception $e){
			error_log($e);
			return null;
		}
	}

	public static function AnularChequeAccion(array $params)
	{
		$an_o_presupuesto = $_SESSION['an_o_presupuesto'];
		/*Parche para anulación total de cheques del año anterior*/
		//$an_o_presupuesto = 2014;
		
		try
		{
			$preMsg = "Error al obtener resultados de la búsqueda";
			$queryWhere = "";

			if($params == null)
			throw new Exception($preMsg." El parámetro params es nulo.");

			if(!is_array($params))
			throw new Exception($preMsg." El parámetro params no es un arreglo.");

			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
				
			if($resultTransaction === false)
			throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			if (strcmp($params['idCheque'], '')!=0) { /*Búsqueda datos del cheque*/
					
				$query = "SELECT
							ch.id_cheque AS id_cheque, 
							ch.monto_cheque AS monto_cheque,
							ch.nro_cheque AS nro_cheque,
							ch.estatus_cheque AS estatus_cheque,									 
							ch.ci_rif_beneficiario_cheque AS ci_rif,
							UPPER(ch.beneficiario_cheque) AS beneficiario_cheque,
							TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY') AS fecha_emision,
							pgch_id AS pgch_id,
							pch.nro_cuenta AS nro_cuenta_bancaria,
							pch.docg_id AS sopg,
							pch.pgch_asunto AS asunto,
							pch.pgch_obs AS observaciones,
							b.banc_nombre AS nombre_banco
						FROM sai_cheque ch
							INNER JOIN sai_pago_cheque pch ON (pch.docg_id = ch.docg_id)
							INNER JOIN sai_ctabanco ctb ON (pch.nro_cuenta=ctb.ctab_numero)	
							INNER JOIN sai_banco b ON (ctb.banc_id = b.banc_id)
						WHERE
							ch.estatus_cheque = 45
							AND ch.id_cheque=".$params['idCheque'];	

				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				$reporte = array();
				while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$reporte['id_cheque'] = $row['id_cheque'];
					$reporte['monto_cheque'] = $row['monto_cheque'];
					$reporte['nro_cheque'] = $row['nro_cheque'];
					$reporte['estatus_cheque'] = $row['estatus_cheque'];
					$reporte['nro_cuenta_bancaria'] = $row['nro_cuenta_bancaria'];
					$reporte['fecha_emision'] = $row['fecha_emision'];
					$reporte['beneficiario_cheque'] = $row['beneficiario_cheque'];
					$reporte['sopg'] = $row['sopg'];
					$reporte['ci_rif'] = $row['ci_rif'];
					$reporte['asunto'] = $row['asunto'];
					$reporte['observaciones'] = $row['observaciones'];
					$reporte['nombre_banco'] = $row['nombre_banco'];
					$reporte['pgch_id'] = $row['pgch_id'];
				} /*Fin búsqueda datos cheque*/


				/*Inicio anulación*/

				/*Inserción del memo*/
				$query = "SELECT *
						 FROM sai_insert_memo (
						'".$_SESSION['login']."',
						'".$_SESSION['user_depe_id']."',
						'".$reporte['observaciones']."',
						'Anulacion del Pago - ".$reporte['pgch_id']."','0', '0','0','',0, 0, '0', '',
						 '".$reporte['pgch_id']."')";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));				

				/*Validar si el pago está conciliado*/
				$conciliado="";
				$query = "SELECT
							docg_id
						FROM sai_ctabanco_saldo
						WHERE docg_id = '".$reporte['pgch_id']."'";

				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				if ($row = $GLOBALS['SafiClassDb']->Fetch($result))	{
					$conciliado = trim($row['docg_id']);
				}
				
				
				if (strlen($conciliado)<1 && $params['otro'] == 1) { /*Si el pago no está conciliado*/
					//Indica si genera o no otro cheque con los mismos datos
					$mensaje = "";

					/*Verificar que la cuenta tiene chequera activa*/
					$query = "SELECT
								 *
							FROM sai_verificar_cuenta_chequera_activa('".$reporte['nro_cuenta_bancaria']."') 
							AS nro_chequera ";

					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

					if ($row = $GLOBALS['SafiClassDb']->Fetch($result))	{
						$numeroChequera = trim($row['nro_chequera']);
					}
						
					/*Si la cuenta no tiene chequera activa*/
					if ($numeroChequera == "0") {
						$error = 1;
						$mensaje= "La cuenta '".$reporte['nro_cuenta_bancaria']."' no tiene chequeras activas";
					}
					else {
						$query = "SELECT
									*
								FROM sai_buscar_cheque_activo('".$numeroChequera."') 
								RESULTADO_SET (id_cheque varchar, nro_cheque varchar) ";

						if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
						throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

						if ($row = $GLOBALS['SafiClassDb']->Fetch($result))	{
							$idNumeroChequeActivo = trim($row['id_cheque']);
							$numeroChequeActivo = trim($row['nro_cheque']);
						}
					}
					/*Fin búsqueda nuevo numero de cheque*/
					if (strlen($numeroChequeActivo<1)) {
						$error = 1;
						$mensaje= "No hay cheques activos. El pago no puede ser anulado parcialmente";
					}
					else { /*Actualización tabla sai_pago_cheque. Asignándole al pago el nuevo nro de cheque*/
						$query = "SELECT
									*
								FROM reemplazar_cheque('".$idNumeroChequeActivo."',
										'".$reporte['monto_cheque']."',
										'".$reporte['beneficiario_cheque']."',
										'".$reporte['ci_rif']."',
										'".$reporte['sopg']."',
										'".$params['motivo'].' '.$params['observacionesAnulacion']."',
										'".$reporte['pgch_id']."',
										'".$numeroChequeActivo."',
										'".$reporte['id_cheque']."')
								AS resultado ";

						if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
						throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
						$mensaje = "El cheque ".$cheque_numero." ha sido anulado y reemplazado por el cheque nro. ".$numeroChequeActivo."<br> El documento ".$sopg." se encuentra nuevamente en el buz&oacute;n de cheques preemitidos";


						
					}
				}
				else if (strlen($conciliado)<1 && $otroCheque == 0) {
					//No genera otro cheque. El pago no sera procesado
						
					/*Caso causado*/
					$query = "SELECT MAX(SUBSTR(TRIM(comp_id),6,LENGTH(TRIM(comp_id))-5)) AS maximo
							FROM sai_comp_diario 
							WHERE comp_doc_id = '".$reporte['sopg']."'
							AND comp_comen LIKE 'C-%'
							AND esta_id != 15";

					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

					if ($row = $GLOBALS['SafiClassDb']->Fetch($result))
					$codaCausado = "coda-".$row["maximo"];
						

					$query = "SELECT SUBSTR(comp_fec,1,4) AS ano
							FROM sai_comp_diario
							WHERE comp_id ='".$codaCausado."'";
					
					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

					if ($row = $GLOBALS['SafiClassDb']->Fetch($result))
					$anoCausado = $row["ano"];
						
					/*Caso transitoria. (Hasta julio 2009)*/
					$query = "SELECT comp_id
								FROM sai_comp_diario 
								WHERE comp_doc_id = '".$reporte['pgch_id']."'
								AND comp_comen LIKE 'T-%'
								AND esta_id != 15";

					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

					if ($row = $GLOBALS['SafiClassDb']->Fetch($result))
					$codaTransitoria = $row["comp_id"];
						
						
					/*Caso pagado*/
					$query = "SELECT comp_id
							FROM sai_comp_diario 
							WHERE comp_doc_id = '".$reporte['pgch_id']."'
					 		AND comp_comen LIKE 'P-%'
							AND esta_id != 15";
					
					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

					if ($row = $GLOBALS['SafiClassDb']->Fetch($result))
					$codaPagado = $row["comp_id"];

					/*Se debe verificar el pago no tenga retenciones. De lo contrario, no se podrá anular en una primera fase*/
					/*$sql_gasto = "SELECT src.comp_id, src.cpat_id as cpat_id, src.rcomp_debe as monto_debe, src.rcomp_haber as monto_haber, src.fecha_emis, to_char(src.fecha_emis, 'MM') as mes, to_char(src.fecha_emis, 'YYYY') as ano from sai_reng_comp src, sai_comp_diario sc where src.comp_id = sc.comp_id and sc.comp_doc_id = '".$pgch_docg_id."'";
					 $resultado_set = pg_exec($conexion ,$sql_gasto) or die("Error al mostrar cuentas para la verificacion de retenciones");
					$valido = 1;
					while($row=pg_fetch_array($resultado_set)) {
					if (trim($row['cpat_id'])=='2.1.1.03.04.01.03' || trim($row['cpat_id'])=='2.1.1.03.04.01.01' || trim($row['cpat_id'])=='2.1.1.03.04.01.02') $valido = 0; //tiene retenciones
					$mes_sopg=$row['mes'];
					$ano_sopg=$row['ano'];
					}
					if ($mes_sopg==date("m") && $ano_sopg==date("Y")) //La anulación se está realizando en el mismo mes en que se registraron las retenciones.-
					$valido = 1;
					if ($valido>0) {/*No existen retenciones o la anulación se está realizando en el mismo mes que el del registro de la solicitud*/


					/*Reverso obligatorio del pagado*/
					$query = "SELECT src.comp_id,
								src.cpat_id,
								src.rcomp_debe,
								src.rcomp_haber, 
								src.fecha_emis 
							FROM sai_reng_comp src 
							INNER JOIN sai_comp_diario sc ON (src.comp_id = sc.comp_id) 
							WHERE src.comp_id = '".$codaPagado."'
							AND sc.comp_comen LIKE 'P%'
							ORDER BY src.fecha_emis DESC";

					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

						
					$cta1_pagado = "";
					$cta2_pagado = "";
					$mto1_pagado = 0;
					$mto2_pagado = 0;
					$tipo1_pagado = 0;
					$i=1;
					while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
						if ($i<2) {
							$cta1_pagado = $row["cpat_id"];
							$mto1_pagado = $row["rcomp_debe"] + $row["rcomp_haber"];
							if ($row["rcomp_debe"]>0) $tipo1_pagado = 1;
							else $tipo1_pagado = 0;
						}
						if ($i<3 && $i>1) {
							$cta2_pagado = $row["cpat_id"];
							$mto2_pagado = $row["rcomp_debe"] + $row["rcomp_haber"];
						}
						$i++;
					}

					$mto1_transitoria = 0;

					/*Reverso opcional transitoria (Casos antes de Agosto de 2009)*/
					if (strcmp($codaTransitoria, '') !=0 || $$codaTransitoria != "" || $codaTransitoria != null ) {
						$query = "SELECT src.comp_id,
									src.cpat_id,
									src.rcomp_debe,
									src.rcomp_haber, 
									src.fecha_emis, 
								FROM sai_reng_comp src,
								INNER JOIN sai_comp_diario sc ON (src.comp_id = sc.comp_id)
								WHERE sc.comp_id = '".$codaTransitoria."'
								AND sc.comp_comen LIKE 'T%' 
								ORDER BY src.fecha_emis DESC";
						
						/*reverso transitoria*/
						if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
						throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

						$cta1_transitoria = "";
						$cta2_transitoria = "";
						$mto2_transitoria = 0;
						$tipo1_transitoria = 0;
							
						$i=1;
						while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
							if ($i<2) {
								$cta1_transitoria = $row["cpat_id"];
								$mto1_transitoria = $row["rcomp_debe"] + $row["rcomp_haber"];
								if ($row["rcomp_debe"]>0) $tipo1_transitoria = 1;
								else $tipo1_transitoria = 0;
							}
							if ($i<3 && $i>1) {
								$cta2_transitoria = $row["cpat_id"];
								$mto2_transitoria = $row["rcomp_debe"] + $row["rcomp_haber"];
							}
							$i++;
						}
					} /*Fin reverso opcional transitoria*/

					/*Anulación gasto $coda_causado*/
					if ($anoCausado==$an_o_presupuesto-1) {
						$cuentaResultado = $GLOBALS['SAFI_CFG']["idCuentaResultadoDelEjercicio"];
					}
					else if ($anoCausado!=$an_o_presupuesto-1) {
						$cuentaResultado = $GLOBALS['SAFI_CFG']["idCuentaResultadoAcumulado"];
					}
					$query = "SELECT comp_id,
							cpat_id,
							rcomp_debe AS monto_debe,
							rcomp_haber AS monto_haber,
							fecha_emis,
							part_id,
							pr_ac,
							a_esp,
							pr_ac_tipo
						FROM sai_reng_comp 
						WHERE comp_id = '".$codaCausado."'";
					
					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
						
					$i=0;
					while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
						if ($anoCausado != $an_o_presupuesto && substr(trim($row['cpat_id']),0,1) == 6)
						$ctaAno = $cuentaResultado;
						else
						$ctaAno = trim($row['cpat_id']);

						$matriz_ctas[$i] = $ctaAno;
						if ($anoCausado != $an_o_presupuesto && substr(trim($row['cpat_id']),0,1)==6) {
							$matriz_partidas[$i] = null;
							$matriz_pr_ac[$i] = null;
							$matriz_a_esp[$i] = null;
							$matriz_pr_ac_tipo[$i] = null;
						}
						else {
							$matriz_partidas[$i]=trim($row['part_id']);
							$matriz_pr_ac[$i] = trim($row['pr_ac']);
							$matriz_a_esp[$i] = trim($row['a_esp']);
							$matriz_pr_ac_tipo[$i] = trim($row['pr_ac_tipo']);
						}

						$matriz_monto[$i]=$row['monto_debe']+$row['monto_haber'];
						if ($row["monto_debe"]>0) $matriz_tipo[$i]=1; /*1 va por el debe*/
						else $matriz_tipo[$i]=0;
						$i++;
					}

					require_once(SAFI_INCLUDE_PATH . '/arreglos_pg.php');
					$arreglo_ctas=convierte_arreglo($matriz_ctas);
					$arreglo_partidas=convierte_arreglo($matriz_partidas);
					$arreglo_monto=convierte_arreglo($matriz_monto);
					$arreglo_tipo=convierte_arreglo($matriz_tipo);
					$arreglo_pr_ac=convierte_arreglo($matriz_pr_ac);
					$arreglo_a_esp=convierte_arreglo($matriz_a_esp);
					$arreglo_pr_ac_tipo=convierte_arreglo($matriz_pr_ac_tipo);
					/*Fin anulación causado*/

					$query = "SELECT *
						FROM anulacion_total ('".$reporte['sopg']."', 
							'".$reporte['pgch_id']."',
							'".$_SESSION['user_depe_id']."',
							'".$cta1_pagado."',
							".$mto1_pagado.",
							'".$tipo1_pagado."',
							'".$cta2_pagado."',
							'".$reporte['nro_cheque']."',
							'".$codaTransitoria."',
							'".$cta1_transitoria."',
							".$mto1_transitoria.", 
							'".$tipo1_transitoria."', 
							'".$cta2_transitoria."', 
							'".$arreglo_ctas."', 
							'".$arreglo_monto."', 
							'".$arreglo_tipo."' ,
							'".$arreglo_partidas."', 
							'".$reporte['id_cheque']."', 
							'".$params['motivo'].' '.$params['observacionesAnulacion']."',
							'".$arreglo_pr_ac."', 
							'".$arreglo_a_esp."',
							'".$arreglo_pr_ac_tipo."',
							".$an_o_presupuesto.")
						 AS resultado";
					
					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
						

					//Retenciones
					if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
						if ($row[0] != null) {
							//REVERSAR LOS MONTOS DE LAS RETENCIONES DEL ISLR
							$query = "SELECT *
								FROM sai_retenciones_islr 
								WHERE cedula='".$reporte['ci_rif']."'";
													
							if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
							throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

							if ($row = $GLOBALS['SafiClassDb']->Fetch($result))	 {
								$monto_actualizado = $row['monto_pagado'] - $reporte['monto_cheque'] ;
								$query = "UPDATE sai_retenciones_islr
									SET monto_pagado=".$monto_actualizado."
									WHERE cedula='".$reporte['ci_rif']."'";
															
								if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
								throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
							}
						}
					}

					$mensaje = "El cheque ".$cheque_numero." ha sido anulado <br> Los movimientos presupuestarios y contables asociados al documento ".$id_doc_imputacion." igualmente han sido anulados";


					/*************************************************************************************************/
					//LIBERAR DISPONIBILIDAD DEL COMP AL ANULAR TOTALMENTE UN SOPG ELABORADO A PARTIR DEL 2011
					//SIEMPRE QUE EL SOPG ESTE ASOCIADO A UN COMP
					$query = "SELECT
								comp_id
							FROM sai_sol_pago
							WHERE sopg_id='".$reporte['sopg']."'";
					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
						
					if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
						$l = strlen($reporte['pgch_id']);
						$ao = substr($reporte['pgch_id'],$l-2,$l);
						$comp_id = $row['comp_id'];
						if (($comp_id != '') && ($row['comp_id'] != 'N/A') && ($ao != '08') && ($ao != '09') && ($ao != '10')){

							$query = " SELECT *
								FROM sai_buscar_sopg_imputacion('".trim($reporte['sopg'])."') 
								AS result ";
							$query .= " (sopg_id varchar,
									sopg_acc_pp varchar,
									sopg_acc_esp varchar,
									depe_id varchar,
									sopg_sub_espe varchar,
									sopg_monto float8,
									tipo bit,
									sopg_monto_exento float8)";
							if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
							throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
								
							$i = 0;
							while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
								$matriz_imputacion[$i]=trim($row['tipo']);
								$matriz_acc_pp[$i]=trim($row['sopg_acc_pp']);
								$matriz_acc_esp[$i]=trim($row['sopg_acc_esp']);
								$matriz_sub_esp[$i]=trim($row['sopg_sub_espe']);
								$matriz_uel[$i]=trim($row['depe_id']);
								$matriz_monto_partida[$i]=$row['sopg_monto']+$row['sopg_monto_exento'];
								$i++;
							}
							$total_imputacion = $i;

							for($j=0; $j<$total_imputacion; $j++)  {
								$query = "SELECT
											monto AS disponible
										FROM sai_disponibilidad_comp 
										WHERE comp_id='".$comp_id."'
											AND partida='".$matriz_sub_esp[$j]."'
											AND comp_acc_pp='".$matriz_acc_pp[$j]."'
											AND comp_acc_esp='".$matriz_acc_esp[$j]."'";
								if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
								throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

								if ($row = $GLOBALS['SafiClassDb']->Fetch($result))	{
									$disponible=$row['disponible']+$matriz_monto_partida[$j];
									$query = "UPDATE sai_disponibilidad_comp
									 		 SET monto='".$disponible."' 
									 		 WHERE comp_id='".$comp_id."'
												AND partida='".$matriz_sub_esp[$j]."' 
												AND comp_acc_pp='".$matriz_acc_pp[$j]."'
												AND comp_acc_esp='".$matriz_acc_esp[$j]."'";
									if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
									throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
								}
							}
						}
					}//fin liberación de disponibilidad del compromiso
				} //fin if ($otro_cheque==0)

				else if (strlen($conciliado)>1) {
					$mensaje = "El cheque no puede anularse pues ya fue conciliado";
				}
				$reporte['observacionesAnulacion'] = $params['motivo'].' '.$params['observacionesAnulacion'];
				$reporte['mensaje'] = $mensaje;
				/*Fin anulación*/
				
				
				$result = $GLOBALS['SafiClassDb']->CommitTransaction();
					
				if($result === false)
				throw new Exception("Error al realizar el commit en la función de guardado de la anulación del cheque".
				utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				return $reporte;
				
			}
		}

		catch(Exception $e){
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;		
		}
	}

	public static function ubicarDocumento(array $params)
	{
		try
		{
			$preMsg = "Error al obtener resultados de la búsqueda";
			$queryWhere = "";
	
			if($params == null)
			throw new Exception($preMsg." El parámetro params es nulo.");
	
			if(!is_array($params))
			throw new Exception($preMsg." El parámetro params no es un arreglo.");
			
			$condicion = "";

			if(isset($params['tipo']) && $params['tipo'] != '' && isset($params['documento']) && strlen($params['documento'])>2) {

				if (strcmp($params['tipo'],'sopg')==0) $queryWhere = " s.sopg_id='sopg-".$params['documento']."'";
				else if (strcmp($params['tipo'],'pgch')==0) $queryWhere = " p.pgch_id='pgch-".$params['documento']."'";
				else if (strcmp($params['tipo'],'tran')==0) $queryWhere = " t.trans_id='tran-".$params['documento']."'";
				else if (strcmp($params['tipo'],'cheq')==0) $queryWhere = " ch.nro_cheque='".$params['documento']."'";
				else if (strcmp($params['tipo'],'ntran')==0) $queryWhere = " t.nro_referencia='".$params['documento']."'";
				$condicion = " AND ";				
			}

			if(isset($params['fechaInicio']) && strlen($params['fechaInicio'])>5 && isset($params['fechaFin']) && strlen($params['fechaFin'])>5) {
				$fechaInicio = explode ('/',$params['fechaInicio']);
				$fechaFin = explode ('/',$params['fechaFin']);				
				$queryWhere .=  $condicion." s.sopg_fecha BETWEEN TO_TIMESTAMP('".$fechaInicio[2].'-'.$fechaInicio[1].'-'.$fechaInicio[0]." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fechaFin[2].'-'.$fechaFin[1].'-'.$fechaFin[0]." 23:59:59','YYYY-MM-DD HH24:MI:SS')";
			}
				

			if (strcmp($params['tipo'],'sopg')==0) {
				$nucleo= "SELECT
							s.sopg_id AS sopg_id,
							s.sopg_detalle AS detalle,
							d.perf_id_act AS perfil,
							TO_CHAR(s.sopg_fecha, 'DD/MM/YYYY') AS fecha,
							d.wfob_id_ini AS objeto,
							d.esta_id AS esta_id
						FROM
							sai_sol_pago s
						INNER JOIN  sai_doc_genera d ON (d.docg_id = s.sopg_id)
						WHERE ".$queryWhere;
				
				$queryWhere2 = "";
				if (strlen($params['documento']) >4)
					$queryWhere2 = " WHERE nucleo.sopg_id = 'sopg-".$params['documento']."'";
				

				$query = "SELECT 
							nucleo.sopg_id AS documento_inicial,
							COALESCE(p.pgch_id,'') || COALESCE(t.trans_id,'') AS documento_relacion,
							COALESCE(ch.nro_cheque,'') || COALESCE(t.nro_referencia,'') AS nro_referencia,
							nucleo.detalle AS detalle,
							COALESCE(p.nro_cuenta,'') || COALESCE(t.nro_cuenta_emisor,'') AS nro_cuenta,
							CASE 
							WHEN ((p.esta_id=2 OR t.esta_id!=2) AND LENGTH(t.trans_id) > 2 ) THEN
								COALESCE(t.beneficiario) 
							ELSE
								COALESCE(ch.ci_rif_beneficiario_cheque,'') || ' ' || COALESCE(ch.beneficiario_cheque,'') 
							END AS beneficiario,
							CASE
							WHEN (p.esta_id=2 OR t.esta_id!=2) THEN  COALESCE(t.trans_monto,0)
							ELSE COALESCE(ch.monto_cheque,0)
							
							END AS monto_pago, 						
							CASE 
								WHEN (nucleo.esta_id=13)
								/*THEN (
										CASE 
										WHEN (ch.estatus_cheque=45) THEN 'Emitido'
										WHEN (ch.estatus_cheque=44) THEN 'Pre emitido' 
										WHEN (ch.estatus_cheque=15) THEN 'Anulado'
										ELSE ''
										END
								)*/
								THEN 'Emitido'
								WHEN nucleo.esta_id=15 
								THEN 'Anulado' 
								ELSE 'Por iniciar pago'
								END AS estado_documento,
							nucleo.fecha AS fecha,
							CASE
								WHEN (nucleo.perfil='') 
								THEN (
										CASE
											WHEN (
													SELECT
														 perf_id_act
													FROM sai_doc_genera
													WHERE (docg_id = p.pgch_id or docg_id = t.trans_id)
											) = ''
											THEN 'Finalizado'
											ELSE (
													SELECT
														wfgr_descrip
													FROM sai_wfgrupo
													WHERE (
															SELECT
																 perf_id_act
															FROM sai_doc_genera
															WHERE (docg_id = p.pgch_id or docg_id = t.trans_id)
														) LIKE  wfgr_perf
													)
											END
										)
								ELSE (
										SELECT
											wfgr_descrip
										FROM sai_wfgrupo
										WHERE nucleo.perfil LIKE  wfgr_perf
									)
							 	END AS perfil
							FROM (".$nucleo.") AS nucleo
							LEFT OUTER JOIN sai_pago_cheque p ON (p.docg_id = nucleo.sopg_id AND p.esta_id !=2)
							LEFT OUTER JOIN sai_cheque ch ON (nucleo.sopg_id = ch.docg_id)
							LEFT OUTER JOIN sai_pago_transferencia t ON (t.docg_id = nucleo.sopg_id AND t.esta_id!=2) 									
							". $queryWhere2; 
			}
			else if (strcmp($params['tipo'],'pgch')==0)
			$query = "SELECT
						s.sopg_id AS documento_inicial, 
						p.pgch_id AS documento_relacion,
						ch.nro_cheque AS nro_referencia,
						s.sopg_detalle AS detalle,
						p.nro_cuenta AS nro_cuenta,
						ch.ci_rif_beneficiario_cheque || ' ' || ch.beneficiario_cheque AS beneficiario,
						ch.monto_cheque AS monto_pago,
						CASE 
							WHEN (d.wfob_id_ini = 99)
							THEN 'Finalizado'
							ELSE (
								SELECT
									wfgr_descrip
								FROM sai_wfgrupo
								WHERE substring(d.perf_id_act from 1 for 2) = substring(wfgr_perf from 1 for 2)
								LIMIT 1
							) END AS perfil,
						CASE 
							WHEN (ch.estatus_cheque = 45) 
							THEN 'Emitido'
							WHEN (ch.estatus_cheque = 44)
							THEN 'Pre emitido' 
							ELSE 'Anulado'
							END AS estado_documento, 
							TO_CHAR(s.sopg_fecha, 'DD/MM/YYYY') AS fecha
					  FROM sai_pago_cheque p
					  	INNER JOIN sai_sol_pago s ON (p.docg_id = s.sopg_id)
					  	INNER JOIN sai_cheque ch ON (s.sopg_id = ch.docg_id)
					  	INNER JOIN sai_doc_genera d ON (d.docg_id=p.pgch_id)
					  WHERE ".$queryWhere;
			
			
			else if (strcmp($params['tipo'],'tran')==0)
				$query = "SELECT
							s.sopg_id AS documento_inicial,
							t.trans_id AS documento_relacion,
							t.nro_referencia AS nro_referencia,
							s.sopg_detalle AS detalle,
							t.nro_cuenta_emisor AS nro_cuenta,
							t.beneficiario AS beneficiario,
							t.trans_monto AS monto_pago,
							CASE WHEN (d.wfob_id_ini = 99) 
							THEN 'Finalizado'
							ELSE (
								SELECT
									wfgr_descrip 
								FROM sai_wfgrupo
								WHERE substring(d.perf_id_act from 1 for 2) = substring(wfgr_perf from 1 for 2)
								LIMIT 1
								)
							END AS perfil,
							CASE 
								WHEN (t.esta_id = 10 OR t.esta_id = 13) 
								THEN 'Emitido'
								ELSE 'Anulado' 
								END AS estado_documento, 
							TO_CHAR(s.sopg_fecha, 'DD/MM/YYYY') AS fecha
						FROM 
							sai_sol_pago s
						INNER JOIN sai_pago_transferencia t ON (t.docg_id = s.sopg_id AND t.esta_id!=2)
						INNER JOIN sai_doc_genera d ON (d.docg_id=t.trans_id)
						WHERE  ".$queryWhere;
			
			else if (strcmp($params['tipo'],'cheq')==0)
				$query = "SELECT
							s.sopg_id AS documento_inicial,
							p.pgch_id AS documento_relacion,
							ch.nro_cheque AS nro_referencia,
							s.sopg_detalle AS detalle, 
							p.nro_cuenta AS nro_cuenta,
							ch.ci_rif_beneficiario_cheque || ' ' || ch.beneficiario_cheque AS beneficiario,
							ch.monto_cheque AS monto_pago,
							CASE WHEN (d.wfob_id_ini = 99)
							THEN 'Finalizado'
							ELSE (
								SELECT wfgr_descrip
								FROM sai_wfgrupo
								WHERE substring(d.perf_id_act from 1 for 2) = substring(wfgr_perf from 1 for 2)
								LIMIT 1
							)
							END AS perfil,
							CASE
								WHEN (ch.estatus_cheque = 45) 
								THEN 'Emitido'
								WHEN (ch.estatus_cheque = 44)
								THEN 'Pre emitido'
								ELSE 'Anulado' 
							END AS estado_documento,
							TO_CHAR(s.sopg_fecha, 'DD/MM/YYYY') AS fecha
						FROM sai_pago_cheque p
						INNER JOIN sai_sol_pago s ON (p.docg_id = s.sopg_id)
						INNER JOIN sai_cheque ch ON (s.sopg_id = ch.docg_id)
						INNER JOIN sai_doc_genera d ON (d.docg_id = p.pgch_id)
						WHERE ". $queryWhere;
			
			else if (strcmp($params['tipo'],'ntran')==0)
				$query = "SELECT
							s.sopg_id AS documento_inicial,
							t.trans_id AS documento_relacion,
							t.nro_referencia AS nro_referencia,
							s.sopg_detalle AS detalle,
							t.nro_cuenta_emisor AS nro_cuenta,
							t.beneficiario AS beneficiario,
							t.trans_monto AS monto_pago,
							CASE
								WHEN (d.wfob_id_ini = 99)
							 	THEN 'Finalizado'
							 	ELSE (
							 		SELECT 
							 			wfgr_descrip
							 		FROM sai_wfgrupo 
									WHERE substring(d.perf_id_act from 1 for 2) = substring(wfgr_perf from 1 for 2)
									LIMIT 1
													 	) 
							 END AS perfil,
							 CASE
							 	WHEN (t.esta_id = 10)
							 	THEN 'Emitido'
							 	WHEN (t.esta_id = 13)
							 	THEN 'Emitido'
							 	ELSE 'Anulado'
							 	END AS estado_documento, 
							 	TO_CHAR(s.sopg_fecha, 'DD/MM/YYYY') AS fecha
						FROM sai_sol_pago s
						INNER JOIN sai_pago_transferencia t ON (t.docg_id = s.sopg_id AND t.esta_id!=2)
						INNER JOIN sai_doc_genera d ON (d.docg_id=t.trans_id)
						WHERE ".$queryWhere;
			//echo $query;
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
			$reporte = array();
			$i=0;
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$reporte[$i]['documento_inicial'] = $row['documento_inicial'];
				$reporte[$i]['documento_relacion'] = $row['documento_relacion'];
				$reporte[$i]['fecha'] = $row['fecha'];
				$reporte[$i]['nro_referencia'] = $row['nro_referencia'];
				$reporte[$i]['estado_documento'] = $row['estado_documento'];
				$reporte[$i]['nro_cuenta'] = $row['nro_cuenta'];
				$reporte[$i]['beneficiario'] = $row['beneficiario'];
				$reporte[$i]['monto_pago'] = $row['monto_pago'];
				$reporte[$i]['detalle'] = $row['detalle'];
				$reporte[$i]['perfil'] = $row['perfil'];
				$i++;
			}
	
			return $reporte;
	
		}
		catch(Exception $e){
			error_log($e);
			return null;
		}
	}


	public static function OperacionesEmitidas(array $params)
	{
		try
		{
			$preMsg = "Error al obtener resultados de la búsqueda";
			$queryWhere = "";
	
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
	
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
	

			$fecha_inicio_antes = date("d/m/Y",mktime(0,0,0,substr($params['fechaInicio'], 3, 2),(01 - 1),substr($params['fechaInicio'], 6)));
			$ano = substr($params['fechaInicio'],6,4);
			$fecha_mes = substr($params['fechaInicio'],6,4)."-".substr($params['fechaInicio'],3,2);
			$fecha_mes2 = substr($params['fechaInicio'],3,2)."/".substr($params['fechaInicio'],6,4);
			$fecha_in2 = substr($params['fechaInicio'],6,4)."-".substr($params['fechaInicio'],3,2)."-".substr($params['fechaInicio'],0,2);
			$fecha_fi2 = substr($params['fechaInicio'],6,4)."-".substr($params['fechaFin'],3,2)."-".substr($params['fechaFin'],0,2);
			
			$condicionq = "";
			$condicionb = "";
			$condiciont = "";
			if (strcmp($params['cuentaBancaria'], '-1') !=0 ) {
				$condicionq=" AND cq.ctab_numero='".$params['cuentaBancaria']."'";
				$condicionb=" AND cb.ctab_numero='".$params['cuentaBancaria']."'";
				$condiciont=" AND (ptr.nro_cuenta_emisor='".$params['cuentaBancaria']."')";
			}
			
			/*Cálculo de saldos finales contabilidad*/
			$fecha_ini = substr($params['fechaInicio'],6,4)."-".substr($params['fechaInicio'],3,2)."-01";			
			$query = "SELECT monto_haber 
					FROM sai_ctabanco_saldo
	   				WHERE 
	   				docg_id LIKE 'sb-".$ano."' AND 
	   				ctab_numero='".trim($params['cuentaBancaria'])."'";

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$saldo_banco = $row["monto_haber"];
			}

			$query= "SELECT SUM(monto_debe) AS suma_debe
				FROM sai_ctabanco_saldo
		   		WHERE
		   		ctab_numero='".$params['cuentaBancaria']."' AND
		   		TO_CHAR(fecha_saldo,'YYYY') =  '".$ano."' AND
		   		fecha_saldo  < TO_TIMESTAMP('".$fecha_ini." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND
		   		docg_id NOT LIKE 'sb%' AND
		   		docg_id NOT LIKE 'si%' AND
		   		docg_id NOT IN (
		   					SELECT docg_id
							FROM sai_doc_genera
							WHERE esta_id = 15
							)";

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));			
			
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$saldo_banco = $saldo_banco - $row["suma_debe"] ;
			}
				
			$query= "SELECT 
						SUM(monto_haber) AS suma_haber
					FROM sai_ctabanco_saldo
		  			WHERE
		  				ctab_numero='".$params['cuentaBancaria']."' AND
		  				TO_CHAR(fecha_saldo,'YYYY') =  '".$ano."' AND
		  				fecha_saldo  < TO_TIMESTAMP('".$fecha_ini." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND
		  				docg_id NOT LIKE 'sb%' AND
		  				docg_id NOT LIKE 'si%' AND
		  				docg_id NOT IN (
		  								SELECT 
		  									docg_id 
		  								FROM sai_doc_genera 
		  								WHERE esta_id = 15)";

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
						
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$saldo_banco = $saldo_banco+$row["suma_haber"] ;
			}
			$saldo_inicial = 0;
			$saldo_final = 0;
			
						
			/*Fin de Cálculo de saldos finales*/

			if (strcmp($params['tipoBusqueda'],'0')==0) {
				
				//filtros para verificar si es una transferencia o es un pago con cheque
				if($params['tipoPago'] == 0)
				{
					$querytipopago =  "
					UNION (
					SELECT
							sp.sopg_id AS sopg_id,
							pch.pgch_id AS pago_id,
							cq.ctab_numero AS numero_cuenta,
							CAST(ch.fechaemision_cheque AS DATE) AS fecha_pagado_date,
							TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY') AS fecha_pagado,
							UPPER(COALESCE(em.empl_nombres,''))||' '||UPPER(COALESCE(em.empl_apellidos,'')) AS beneficiario,
							COALESCE(ch.nro_cheque,'') AS referencia,
							CASE ch.estatus_cheque
								WHEN 15 THEN 'A'
								WHEN 45 THEN
									CASE
										WHEN p.paga_docu_id IN
											(SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
										THEN 'C'
									ELSE 'N'
									END
							END AS condicion,
							COALESCE(ch.monto_cheque,0) AS monto,
							COALESCE(sp.sopg_detalle, '') AS comentario
						FROM
							sai_pagado p,
							sai_cheque ch,
							sai_chequera cq,
							sai_sol_pago sp,
							sai_pago_cheque pch,
							sai_empleado em
						WHERE
							TRIM(sp.sopg_bene_ci_rif) = TRIM(em.empl_cedula) AND
							p.paga_docu_id = pch.pgch_id AND
							pch.docg_id = sp.sopg_id AND
							ch.docg_id = sp.sopg_id AND
							ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
							p.pres_anno != 2008 AND
							ch.fechaemision_cheque BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND	TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
							pch.esta_id!=2
				
						UNION (
						SELECT
							sp.sopg_id AS sopg_id,
							pch.pgch_id AS pago_id,
							cq.ctab_numero AS numero_cuenta,
							CAST(ch.fechaemision_cheque AS DATE) AS fecha_pagado_date,
							TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY') AS fecha_pagado,
							UPPER(COALESCE(v.benvi_nombres,''))||' '||UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiario,
							COALESCE(ch.nro_cheque,'') AS referencia,
							CASE ch.estatus_cheque
								WHEN 15 THEN 'A'
								WHEN 45 THEN
									CASE
										WHEN p.paga_docu_id IN (
											SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
										THEN 'C'
									ELSE 'N'
									END
							END AS condicion,
							COALESCE(ch.monto_cheque,0) AS monto,
							COALESCE(sp.sopg_detalle,'') AS comentario
						FROM
							sai_pagado p,
							sai_cheque ch,
							sai_chequera cq,
							sai_sol_pago sp,
							sai_pago_cheque pch,
							sai_viat_benef v
						WHERE
							TRIM(sp.sopg_bene_ci_rif) = TRIM(v.benvi_cedula) AND
							sp.sopg_bene_ci_rif NOT IN(SELECT empl_cedula FROM sai_empleado) AND
							p.paga_docu_id = pch.pgch_id AND
							pch.docg_id = sp.sopg_id AND
							ch.docg_id = sp.sopg_id AND
							ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
							p.pres_anno != 2008 AND
							ch.fechaemision_cheque BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS')
						UNION (
						SELECT
							sp.sopg_id AS sopg_id,
							pch.pgch_id AS pago_id,
							cq.ctab_numero AS numero_cuenta,
							CAST(ch.fechaemision_cheque AS DATE) AS fecha_pagado_date,
							TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY') AS fecha_pagado,
							UPPER(COALESCE(pr.prov_nombre,'')) AS beneficiario,
							COALESCE(ch.nro_cheque,'') AS referencia,
							CASE ch.estatus_cheque
								WHEN 15 THEN 'A'
								WHEN 45 THEN
									CASE
										WHEN p.paga_docu_id IN(
											SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
										THEN 'C'
										ELSE 'N'
									END
							END AS condicion,
							COALESCE(ch.monto_cheque,0) AS monto,
							COALESCE(sp.sopg_detalle,'') AS comentario
						FROM
							sai_pagado p,
							sai_cheque ch,
							sai_chequera cq,
							sai_sol_pago sp,
							sai_pago_cheque pch,
							sai_proveedor_nuevo pr
						WHERE
							TRIM(sp.sopg_bene_ci_rif) = TRIM(pr.prov_id_rif) AND
							sp.sopg_bene_ci_rif NOT IN(SELECT empl_cedula FROM sai_empleado) AND
							sp.sopg_bene_ci_rif NOT IN(SELECT benvi_cedula FROM sai_viat_benef) AND
							p.paga_docu_id = pch.pgch_id AND
							pch.docg_id = sp.sopg_id AND
							ch.docg_id = sp.sopg_id AND
							ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
							p.pres_anno != 2008 AND
							ch.fechaemision_cheque BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS')
					
						UNION (
						SELECT
							sp.sopg_id AS sopg_id,
							ptr.trans_id AS pago_id,
							ptr.nro_cuenta_emisor AS numero_cuenta,
							CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
							TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
							UPPER(COALESCE(em.empl_nombres,''))||' '||UPPER(COALESCE(em.empl_apellidos,'')) AS beneficiario,
							COALESCE(ptr.nro_referencia,'') AS referencia,
							CASE ptr.esta_id
								WHEN 15 THEN 'A'
								WHEN 10 THEN
									CASE
										WHEN p.paga_docu_id IN(
											SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%')
										THEN 'C'
									ELSE 'N'
									END
							END AS condicion,
							COALESCE(ptr.trans_monto,0) AS monto,
							COALESCE(sp.sopg_detalle,'') AS comentario
						FROM
							sai_pagado p,
							sai_pago_transferencia ptr,
							sai_sol_pago sp,
							sai_empleado em
						WHERE
							TRIM(sp.sopg_bene_ci_rif) = TRIM(em.empl_cedula) AND
							p.paga_docu_id = ptr.trans_id AND
							ptr.docg_id = sp.sopg_id ".$condiciont." AND
							p.pres_anno != 2008 AND
							p.paga_fecha BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS')
						UNION (
						SELECT
							sp.sopg_id AS sopg_id,
							ptr.trans_id AS pago_id,
							ptr.nro_cuenta_emisor AS numero_cuenta,
							CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
							TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
							UPPER(COALESCE(v.benvi_nombres,''))||' '||UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiario,
							COALESCE(ptr.nro_referencia,'') AS referencia,
							CASE ptr.esta_id
								WHEN 15 THEN 'A'
								WHEN 10 THEN
									CASE
										WHEN p.paga_docu_id IN(
											SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%')
										THEN 'C'
										ELSE 'N'
									END
							END AS condicion,
							COALESCE(ptr.trans_monto,0) AS monto,
							COALESCE(sp.sopg_detalle,'') AS comentario
						FROM
							sai_pagado p,
							sai_pago_transferencia ptr,
							sai_sol_pago sp,
							sai_viat_benef v
						WHERE
							TRIM(sp.sopg_bene_ci_rif) = TRIM(v.benvi_cedula) AND
							sp.sopg_bene_ci_rif NOT IN (SELECT empl_cedula FROM sai_empleado) AND
							p.paga_docu_id = ptr.trans_id AND
							ptr.docg_id = sp.sopg_id ".$condiciont." AND
							p.pres_anno != 2008 AND
							p.paga_fecha BETWEEN	TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS')
						UNION (
						SELECT
							sp.sopg_id AS sopg_id,
							ptr.trans_id AS pago_id,
							ptr.nro_cuenta_emisor AS numero_cuenta,
							CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
							TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
							UPPER(COALESCE(pr.prov_nombre,'')) AS beneficiario,
							COALESCE(ptr.nro_referencia,'') AS referencia,
							CASE ptr.esta_id
								WHEN 15 THEN 'A'
								WHEN 10 THEN
									CASE
										WHEN p.paga_docu_id IN (
											SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%')
										THEN 'C'
										ELSE 'N'
									END
							END AS condicion,
							COALESCE(ptr.trans_monto,0) AS monto,
							COALESCE(sp.sopg_detalle,'') AS comentario
						FROM
							sai_pagado p,
							sai_pago_transferencia ptr,
							sai_sol_pago sp,
							sai_proveedor_nuevo pr
						WHERE
							TRIM(sp.sopg_bene_ci_rif) = TRIM(pr.prov_id_rif) AND
							sp.sopg_bene_ci_rif NOT IN (SELECT empl_cedula FROM sai_empleado) AND
							sp.sopg_bene_ci_rif NOT IN (SELECT benvi_cedula FROM sai_viat_benef) AND
							p.paga_docu_id = ptr.trans_id AND
							ptr.docg_id = sp.sopg_id ".$condiciont." AND
							p.pres_anno != 2008 AND
							p.paga_fecha BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') ";
					$parentesis = " )))))) ";
				}
				if($params['tipoPago'] == 1)
				{
					$querytipopago = " UNION (
					SELECT
							sp.sopg_id AS sopg_id,
							pch.pgch_id AS pago_id,
							cq.ctab_numero AS numero_cuenta,
							CAST(ch.fechaemision_cheque AS DATE) AS fecha_pagado_date,
							TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY') AS fecha_pagado,
							UPPER(COALESCE(em.empl_nombres,''))||' '||UPPER(COALESCE(em.empl_apellidos,'')) AS beneficiario,
							COALESCE(ch.nro_cheque,'') AS referencia,
							CASE ch.estatus_cheque
								WHEN 15 THEN 'A'
								WHEN 45 THEN
									CASE
										WHEN p.paga_docu_id IN
											(SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
										THEN 'C'
									ELSE 'N'
									END
							END AS condicion,
							COALESCE(ch.monto_cheque,0) AS monto,
							COALESCE(sp.sopg_detalle, '') AS comentario
						FROM
							sai_pagado p,
							sai_cheque ch,
							sai_chequera cq,
							sai_sol_pago sp,
							sai_pago_cheque pch,
							sai_empleado em
						WHERE
							TRIM(sp.sopg_bene_ci_rif) = TRIM(em.empl_cedula) AND
							p.paga_docu_id = pch.pgch_id AND
							pch.docg_id = sp.sopg_id AND
							ch.docg_id = sp.sopg_id AND
							ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
							p.pres_anno != 2008 AND
							ch.fechaemision_cheque BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND	TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
							pch.esta_id!=2
				
						UNION (
						SELECT
							sp.sopg_id AS sopg_id,
							pch.pgch_id AS pago_id,
							cq.ctab_numero AS numero_cuenta,
							CAST(ch.fechaemision_cheque AS DATE) AS fecha_pagado_date,
							TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY') AS fecha_pagado,
							UPPER(COALESCE(v.benvi_nombres,''))||' '||UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiario,
							COALESCE(ch.nro_cheque,'') AS referencia,
							CASE ch.estatus_cheque
								WHEN 15 THEN 'A'
								WHEN 45 THEN
									CASE
										WHEN p.paga_docu_id IN (
											SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
										THEN 'C'
									ELSE 'N'
									END
							END AS condicion,
							COALESCE(ch.monto_cheque,0) AS monto,
							COALESCE(sp.sopg_detalle,'') AS comentario
						FROM
							sai_pagado p,
							sai_cheque ch,
							sai_chequera cq,
							sai_sol_pago sp,
							sai_pago_cheque pch,
							sai_viat_benef v
						WHERE
							TRIM(sp.sopg_bene_ci_rif) = TRIM(v.benvi_cedula) AND
							sp.sopg_bene_ci_rif NOT IN(SELECT empl_cedula FROM sai_empleado) AND
							p.paga_docu_id = pch.pgch_id AND
							pch.docg_id = sp.sopg_id AND
							ch.docg_id = sp.sopg_id AND
							ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
							p.pres_anno != 2008 AND
							ch.fechaemision_cheque BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS')
						UNION (
						SELECT
							sp.sopg_id AS sopg_id,
							pch.pgch_id AS pago_id,
							cq.ctab_numero AS numero_cuenta,
							CAST(ch.fechaemision_cheque AS DATE) AS fecha_pagado_date,
							TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY') AS fecha_pagado,
							UPPER(COALESCE(pr.prov_nombre,'')) AS beneficiario,
							COALESCE(ch.nro_cheque,'') AS referencia,
							CASE ch.estatus_cheque
								WHEN 15 THEN 'A'
								WHEN 45 THEN
									CASE
										WHEN p.paga_docu_id IN(
											SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
										THEN 'C'
										ELSE 'N'
									END
							END AS condicion,
							COALESCE(ch.monto_cheque,0) AS monto,
							COALESCE(sp.sopg_detalle,'') AS comentario
						FROM
							sai_pagado p,
							sai_cheque ch,
							sai_chequera cq,
							sai_sol_pago sp,
							sai_pago_cheque pch,
							sai_proveedor_nuevo pr
						WHERE
							TRIM(sp.sopg_bene_ci_rif) = TRIM(pr.prov_id_rif) AND
							sp.sopg_bene_ci_rif NOT IN(SELECT empl_cedula FROM sai_empleado) AND
							sp.sopg_bene_ci_rif NOT IN(SELECT benvi_cedula FROM sai_viat_benef) AND
							p.paga_docu_id = pch.pgch_id AND
							pch.docg_id = sp.sopg_id AND
							ch.docg_id = sp.sopg_id AND
							ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
							p.pres_anno != 2008 AND
							ch.fechaemision_cheque BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') ";
				
					$parentesis = " ))) ";
				}
				if($params['tipoPago'] == 2)
				{
					$querytipopago ="
						UNION (
						SELECT
							sp.sopg_id AS sopg_id,
							ptr.trans_id AS pago_id,
							ptr.nro_cuenta_emisor AS numero_cuenta,
							CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
							TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
							UPPER(COALESCE(em.empl_nombres,''))||' '||UPPER(COALESCE(em.empl_apellidos,'')) AS beneficiario,
							COALESCE(ptr.nro_referencia,'') AS referencia,
							CASE ptr.esta_id
								WHEN 15 THEN 'A'
								WHEN 10 THEN
									CASE
										WHEN p.paga_docu_id IN(
											SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%')
										THEN 'C'
									ELSE 'N'
									END
							END AS condicion,
							COALESCE(ptr.trans_monto,0) AS monto,
							COALESCE(sp.sopg_detalle,'') AS comentario
						FROM
							sai_pagado p,
							sai_pago_transferencia ptr,
							sai_sol_pago sp,
							sai_empleado em
						WHERE
							TRIM(sp.sopg_bene_ci_rif) = TRIM(em.empl_cedula) AND
							p.paga_docu_id = ptr.trans_id AND
							ptr.docg_id = sp.sopg_id ".$condiciont." AND
							p.pres_anno != 2008 AND
							p.paga_fecha BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS')
						UNION (
						SELECT
							sp.sopg_id AS sopg_id,
							ptr.trans_id AS pago_id,
							ptr.nro_cuenta_emisor AS numero_cuenta,
							CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
							TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
							UPPER(COALESCE(v.benvi_nombres,''))||' '||UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiario,
							COALESCE(ptr.nro_referencia,'') AS referencia,
							CASE ptr.esta_id
								WHEN 15 THEN 'A'
								WHEN 10 THEN
									CASE
										WHEN p.paga_docu_id IN(
											SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%')
										THEN 'C'
										ELSE 'N'
									END
							END AS condicion,
							COALESCE(ptr.trans_monto,0) AS monto,
							COALESCE(sp.sopg_detalle,'') AS comentario
						FROM
							sai_pagado p,
							sai_pago_transferencia ptr,
							sai_sol_pago sp,
							sai_viat_benef v
						WHERE
							TRIM(sp.sopg_bene_ci_rif) = TRIM(v.benvi_cedula) AND
							sp.sopg_bene_ci_rif NOT IN (SELECT empl_cedula FROM sai_empleado) AND
							p.paga_docu_id = ptr.trans_id AND
							ptr.docg_id = sp.sopg_id ".$condiciont." AND
							p.pres_anno != 2008 AND
							p.paga_fecha BETWEEN	TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS')
						UNION (
						SELECT
							sp.sopg_id AS sopg_id,
							ptr.trans_id AS pago_id,
							ptr.nro_cuenta_emisor AS numero_cuenta,
							CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
							TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
							UPPER(COALESCE(pr.prov_nombre,'')) AS beneficiario,
							COALESCE(ptr.nro_referencia,'') AS referencia,
							CASE ptr.esta_id
								WHEN 15 THEN 'A'
								WHEN 10 THEN
									CASE
										WHEN p.paga_docu_id IN (
											SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%')
										THEN 'C'
										ELSE 'N'
									END
							END AS condicion,
							COALESCE(ptr.trans_monto,0) AS monto,
							COALESCE(sp.sopg_detalle,'') AS comentario
						FROM
							sai_pagado p,
							sai_pago_transferencia ptr,
							sai_sol_pago sp,
							sai_proveedor_nuevo pr
						WHERE
							TRIM(sp.sopg_bene_ci_rif) = TRIM(pr.prov_id_rif) AND
							sp.sopg_bene_ci_rif NOT IN (SELECT empl_cedula FROM sai_empleado) AND
							sp.sopg_bene_ci_rif NOT IN (SELECT benvi_cedula FROM sai_viat_benef) AND
							p.paga_docu_id = ptr.trans_id AND
							ptr.docg_id = sp.sopg_id ".$condiciont." AND
							p.pres_anno != 2008 AND
							p.paga_fecha BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') ";
				
					$parentesis = " ))) ";
				}
				//FIN filtros para verificar si es una transferencia o es un pago con cheque
				
				$GLOBALS['SafiRequestVars']['titulo'] = "OPERACIONES EMITIDAS ";
				$query = 

				"
				SELECT
							cdi.comp_id AS sopg_id,
							cdi.comp_id AS pago_id,
							cb.ctab_numero AS numero_cuenta,
							CAST(cdi.comp_fec AS DATE) AS fecha_pagado_date,
							TO_CHAR(cdi.comp_fec, 'DD/MM/YYYY') AS fecha_pagado,
							'-',
							COALESCE(cdi.nro_referencia,'') AS referencia,
							CASE
								WHEN cdi.comp_id IN(
									SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'codi%')
								THEN 'C'
								ELSE 'N'
							END AS condicion,
							COALESCE(reg.rcomp_haber,0)-COALESCE(reg.rcomp_debe,0) AS monto,
							COALESCE(cdi.comp_comen,'') AS comentario
						FROM
							sai_comp_diario cdi,
							sai_reng_comp reg,
							sai_ctabanco cb
						WHERE
							cdi.comp_id = reg.comp_id AND
							reg.cpat_id = cb.cpat_id ".$condicionb." AND
							cdi.esta_id != 15 AND
							cdi.comp_fec BETWEEN TO_DATE('".$fecha_in2."','YYYY-MM-DD') AND TO_DATE('".$fecha_fi2."','YYYY-MM-DD') AND
							cdi.comp_id LIKE 'codi%' ".$querytipopago." ".$parentesis."
						ORDER BY numero_cuenta ASC,fecha_pagado_date ASC, referencia ASC";/*ORDER BY numero_cuenta, fecha_pagado*/
			
			} else if (strcmp($params['tipoBusqueda'],'1')==0) {
				
				if($params['tipoPago'] == 0)
				{
					$querytipopago = " UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						pch.pgch_id AS pago_id,
						cq.ctab_numero AS numero_cuenta,
						CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(em.empl_nombres,''))||' '||UPPER(COALESCE(em.empl_apellidos,'')) AS beneficiario,
						COALESCE(ch.nro_cheque,'') AS referencia,
						CASE ch.estatus_cheque
							WHEN 15 THEN 'A'
							WHEN 45 THEN
								CASE
									WHEN p.paga_docu_id IN (
										SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
									THEN 'C'
									ELSE 'N'
								END
						END AS condicion,
						COALESCE(ch.monto_cheque,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_cheque ch,
						sai_chequera cq,
						sai_sol_pago sp,
						sai_pago_cheque pch,
						sai_empleado em
					WHERE
						(p.paga_docu_id NOT IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%') OR
						p.paga_docu_id IN (
							SELECT docg_id
							FROM sai_ctabanco_saldo
							WHERE
								docg_id LIKE 'pgch%' AND
								fecha_saldo > TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS'))) AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(em.empl_cedula) AND
						p.paga_docu_id = pch.pgch_id AND
						pch.docg_id = sp.sopg_id AND
						ch.docg_id = sp.sopg_id AND
						ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
						p.pres_anno != 2008 AND
						p.paga_fecha BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						p.esta_id != 15 AND p.esta_id != 2 AND
						em.esta_id != 15 AND em.esta_id != 2
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						pch.pgch_id AS pago_id,
						cq.ctab_numero AS numero_cuenta,
						CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(v.benvi_nombres,''))||' '||UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiario,
						COALESCE(ch.nro_cheque,'') AS referencia,
						CASE ch.estatus_cheque
							WHEN 15 THEN 'A'
							WHEN 45 THEN
								CASE
									WHEN p.paga_docu_id IN (
										SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
									THEN 'C'
									ELSE 'N'
								END
						END AS condicion,
						COALESCE(ch.monto_cheque,0) AS monto,
						COALESCE(sp.sopg_detalle, '') AS comentario
					FROM
						sai_pagado p,
						sai_cheque ch,
						sai_chequera cq,
						sai_sol_pago sp,
						sai_pago_cheque pch,
						sai_viat_benef v
					WHERE
						(p.paga_docu_id NOT IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%') OR
						p.paga_docu_id IN (
							SELECT docg_id
							FROM sai_ctabanco_saldo
							WHERE
								docg_id LIKE 'pgch%' AND
								fecha_saldo > TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS'))) AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(v.benvi_cedula) AND
						sp.sopg_bene_ci_rif NOT IN (SELECT empl_cedula FROM sai_empleado WHERE esta_id = 1) AND
						p.paga_docu_id = pch.pgch_id AND
						pch.docg_id = sp.sopg_id AND
						ch.docg_id = sp.sopg_id AND
						ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
						p.pres_anno != 2008 AND
						p.paga_fecha BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						p.esta_id != 15 AND p.esta_id != 2 AND
						v.benvi_esta_id != 15 AND v.benvi_esta_id != 2
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						pch.pgch_id AS pago_id,
						cq.ctab_numero AS numero_cuenta,
						CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(pr.prov_nombre,'')) AS beneficiario,
						COALESCE(ch.nro_cheque,'') AS referencia,
						CASE ch.estatus_cheque
							WHEN 15 THEN 'A'
							WHEN 45 THEN
								CASE
									WHEN p.paga_docu_id IN (
										SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ch.monto_cheque,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_cheque ch,
						sai_chequera cq,
						sai_sol_pago sp,
						sai_pago_cheque pch,
						sai_proveedor_nuevo pr
					WHERE
						(p.paga_docu_id NOT IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%') OR
						p.paga_docu_id IN (
							SELECT docg_id
							FROM sai_ctabanco_saldo
							WHERE
								docg_id LIKE 'pgch%' AND
								fecha_saldo > TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS'))) AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(pr.prov_id_rif) AND
						p.paga_docu_id = pch.pgch_id AND
						pch.docg_id = sp.sopg_id AND
						ch.docg_id = sp.sopg_id AND
						ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
						p.pres_anno != 2008 AND
						p.paga_fecha BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						p.esta_id != 15 AND p.esta_id != 2 AND
						pr.prov_esta_id != 15 AND pr.prov_esta_id != 2
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						ptr.trans_id AS pago_id,
						cb.ctab_numero AS numero_cuenta,
						CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(em.empl_nombres,''))||' '||UPPER(COALESCE(em.empl_apellidos,'')) AS beneficiario,
						COALESCE(ptr.nro_referencia,'') AS referencia,
						CASE ptr.esta_id
							WHEN 15 THEN 'A'
							WHEN 10 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ptr.trans_monto,0) AS monto,
						COALESCE(sp.sopg_detalle, '') AS comentario
					FROM
						sai_pagado p,
						sai_pago_transferencia ptr,
						sai_ctabanco cb,
						sai_sol_pago sp,
						sai_empleado em
					WHERE
						(p.paga_docu_id NOT IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%') OR
						p.paga_docu_id IN (
							SELECT docg_id
							FROM sai_ctabanco_saldo
							WHERE
								docg_id LIKE 'tran%' AND
								fecha_saldo > TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS'))) AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(em.empl_cedula) AND
						p.paga_docu_id = ptr.trans_id AND
						ptr.docg_id = sp.sopg_id ".$condicionb." AND
						p.pres_anno != 2008 AND
						p.paga_fecha BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						p.esta_id != 15 AND p.esta_id != 2 AND
						em.esta_id != 15 AND em.esta_id != 2
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						ptr.trans_id AS pago_id,
						cb.ctab_numero AS numero_cuenta,
						CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(v.benvi_nombres,''))||' '||UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiario,
						COALESCE(ptr.nro_referencia,'') AS referencia,
						CASE ptr.esta_id
							WHEN 15 THEN 'A'
							WHEN 10 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ptr.trans_monto,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_pago_transferencia ptr,
						sai_ctabanco cb,
						sai_sol_pago sp,
						sai_viat_benef v
					WHERE
						(p.paga_docu_id NOT IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%') OR
						p.paga_docu_id IN (
							SELECT docg_id
							FROM sai_ctabanco_saldo
							WHERE
								docg_id LIKE 'tran%' AND
								fecha_saldo > TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS'))) AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(v.benvi_cedula) AND
						sp.sopg_bene_ci_rif NOT IN (SELECT empl_cedula FROM sai_empleado WHERE esta_id = 1) AND
						p.paga_docu_id = ptr.trans_id AND
						ptr.docg_id = sp.sopg_id ".$condicionb." AND
						p.pres_anno != 2008 AND
						p.paga_fecha BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						p.esta_id != 15 AND p.esta_id != 2 AND
						v.benvi_esta_id != 15 AND v.benvi_esta_id != 2
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						ptr.trans_id AS pago_id,
						cb.ctab_numero AS numero_cuenta,
						CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(pr.prov_nombre,'')) AS beneficiario,
						COALESCE(ptr.nro_referencia,'') AS referencia,
						CASE ptr.esta_id
							WHEN 15 THEN 'A'
							WHEN 10 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ptr.trans_monto,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_pago_transferencia ptr,
						sai_ctabanco cb,
						sai_sol_pago sp,
						sai_proveedor_nuevo pr
					WHERE
						(p.paga_docu_id NOT IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%') OR
						p.paga_docu_id IN (
							SELECT docg_id
							FROM sai_ctabanco_saldo
							WHERE
								docg_id LIKE 'tran%' AND
								fecha_saldo > TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS'))) AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(pr.prov_id_rif) AND
						p.paga_docu_id = ptr.trans_id AND
						ptr.docg_id = sp.sopg_id ".$condicionb." AND
						p.pres_anno != 2008 AND
						p.paga_fecha BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						p.esta_id != 15 AND p.esta_id != 2 AND
						pr.prov_esta_id != 15 AND pr.prov_esta_id != 2
				 ";
					$parentesis = " )))))) ";
						
				}
				if($params['tipoPago'] == 1)
				{
					$querytipopago = " UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						pch.pgch_id AS pago_id,
						cq.ctab_numero AS numero_cuenta,
						CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(em.empl_nombres,''))||' '||UPPER(COALESCE(em.empl_apellidos,'')) AS beneficiario,
						COALESCE(ch.nro_cheque,'') AS referencia,
						CASE ch.estatus_cheque
							WHEN 15 THEN 'A'
							WHEN 45 THEN
								CASE
									WHEN p.paga_docu_id IN (
										SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
									THEN 'C'
									ELSE 'N'
								END
						END AS condicion,
						COALESCE(ch.monto_cheque,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_cheque ch,
						sai_chequera cq,
						sai_sol_pago sp,
						sai_pago_cheque pch,
						sai_empleado em
					WHERE
						(p.paga_docu_id NOT IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%') OR
						p.paga_docu_id IN (
							SELECT docg_id
							FROM sai_ctabanco_saldo
							WHERE
								docg_id LIKE 'pgch%' AND
								fecha_saldo > TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS'))) AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(em.empl_cedula) AND
						p.paga_docu_id = pch.pgch_id AND
						pch.docg_id = sp.sopg_id AND
						ch.docg_id = sp.sopg_id AND
						ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
						p.pres_anno != 2008 AND
						p.paga_fecha BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						p.esta_id != 15 AND p.esta_id != 2 AND
						em.esta_id != 15 AND em.esta_id != 2
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						pch.pgch_id AS pago_id,
						cq.ctab_numero AS numero_cuenta,
						CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(v.benvi_nombres,''))||' '||UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiario,
						COALESCE(ch.nro_cheque,'') AS referencia,
						CASE ch.estatus_cheque
							WHEN 15 THEN 'A'
							WHEN 45 THEN
								CASE
									WHEN p.paga_docu_id IN (
										SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
									THEN 'C'
									ELSE 'N'
								END
						END AS condicion,
						COALESCE(ch.monto_cheque,0) AS monto,
						COALESCE(sp.sopg_detalle, '') AS comentario
					FROM
						sai_pagado p,
						sai_cheque ch,
						sai_chequera cq,
						sai_sol_pago sp,
						sai_pago_cheque pch,
						sai_viat_benef v
					WHERE
						(p.paga_docu_id NOT IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%') OR
						p.paga_docu_id IN (
							SELECT docg_id
							FROM sai_ctabanco_saldo
							WHERE
								docg_id LIKE 'pgch%' AND
								fecha_saldo > TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS'))) AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(v.benvi_cedula) AND
						sp.sopg_bene_ci_rif NOT IN (SELECT empl_cedula FROM sai_empleado WHERE esta_id = 1) AND
						p.paga_docu_id = pch.pgch_id AND
						pch.docg_id = sp.sopg_id AND
						ch.docg_id = sp.sopg_id AND
						ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
						p.pres_anno != 2008 AND
						p.paga_fecha BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						p.esta_id != 15 AND p.esta_id != 2 AND
						v.benvi_esta_id != 15 AND v.benvi_esta_id != 2
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						pch.pgch_id AS pago_id,
						cq.ctab_numero AS numero_cuenta,
						CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(pr.prov_nombre,'')) AS beneficiario,
						COALESCE(ch.nro_cheque,'') AS referencia,
						CASE ch.estatus_cheque
							WHEN 15 THEN 'A'
							WHEN 45 THEN
								CASE
									WHEN p.paga_docu_id IN (
										SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ch.monto_cheque,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_cheque ch,
						sai_chequera cq,
						sai_sol_pago sp,
						sai_pago_cheque pch,
						sai_proveedor_nuevo pr
					WHERE
						(p.paga_docu_id NOT IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%') OR
						p.paga_docu_id IN (
							SELECT docg_id
							FROM sai_ctabanco_saldo
							WHERE
								docg_id LIKE 'pgch%' AND
								fecha_saldo > TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS'))) AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(pr.prov_id_rif) AND
						p.paga_docu_id = pch.pgch_id AND
						pch.docg_id = sp.sopg_id AND
						ch.docg_id = sp.sopg_id AND
						ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
						p.pres_anno != 2008 AND
						p.paga_fecha BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						p.esta_id != 15 AND p.esta_id != 2 AND
						pr.prov_esta_id != 15 AND pr.prov_esta_id != 2 ";
					
					$parentesis = " ))) ";
				}
				if($params['tipoPago'] == 2)
				{
					$querytipopago = " UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						ptr.trans_id AS pago_id,
						cb.ctab_numero AS numero_cuenta,
						CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(em.empl_nombres,''))||' '||UPPER(COALESCE(em.empl_apellidos,'')) AS beneficiario,
						COALESCE(ptr.nro_referencia,'') AS referencia,
						CASE ptr.esta_id
							WHEN 15 THEN 'A'
							WHEN 10 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ptr.trans_monto,0) AS monto,
						COALESCE(sp.sopg_detalle, '') AS comentario
					FROM
						sai_pagado p,
						sai_pago_transferencia ptr,
						sai_ctabanco cb,
						sai_sol_pago sp,
						sai_empleado em
					WHERE
						(p.paga_docu_id NOT IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%') OR
						p.paga_docu_id IN (
							SELECT docg_id
							FROM sai_ctabanco_saldo
							WHERE
								docg_id LIKE 'tran%' AND
								fecha_saldo > TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS'))) AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(em.empl_cedula) AND
						p.paga_docu_id = ptr.trans_id AND
						ptr.docg_id = sp.sopg_id ".$condicionb." AND
						p.pres_anno != 2008 AND
						p.paga_fecha BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						p.esta_id != 15 AND p.esta_id != 2 AND
						em.esta_id != 15 AND em.esta_id != 2
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						ptr.trans_id AS pago_id,
						cb.ctab_numero AS numero_cuenta,
						CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(v.benvi_nombres,''))||' '||UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiario,
						COALESCE(ptr.nro_referencia,'') AS referencia,
						CASE ptr.esta_id
							WHEN 15 THEN 'A'
							WHEN 10 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ptr.trans_monto,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_pago_transferencia ptr,
						sai_ctabanco cb,
						sai_sol_pago sp,
						sai_viat_benef v
					WHERE
						(p.paga_docu_id NOT IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%') OR
						p.paga_docu_id IN (
							SELECT docg_id
							FROM sai_ctabanco_saldo
							WHERE
								docg_id LIKE 'tran%' AND
								fecha_saldo > TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS'))) AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(v.benvi_cedula) AND
						sp.sopg_bene_ci_rif NOT IN (SELECT empl_cedula FROM sai_empleado WHERE esta_id = 1) AND
						p.paga_docu_id = ptr.trans_id AND
						ptr.docg_id = sp.sopg_id ".$condicionb." AND
						p.pres_anno != 2008 AND
						p.paga_fecha BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						p.esta_id != 15 AND p.esta_id != 2 AND
						v.benvi_esta_id != 15 AND v.benvi_esta_id != 2
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						ptr.trans_id AS pago_id,
						cb.ctab_numero AS numero_cuenta,
						CAST(p.paga_fecha AS DATE) AS fecha_pagado_date,
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(pr.prov_nombre,'')) AS beneficiario,
						COALESCE(ptr.nro_referencia,'') AS referencia,
						CASE ptr.esta_id
							WHEN 15 THEN 'A'
							WHEN 10 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ptr.trans_monto,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_pago_transferencia ptr,
						sai_ctabanco cb,
						sai_sol_pago sp,
						sai_proveedor_nuevo pr
					WHERE
						(p.paga_docu_id NOT IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%') OR
						p.paga_docu_id IN (
							SELECT docg_id
							FROM sai_ctabanco_saldo
							WHERE
								docg_id LIKE 'tran%' AND
								fecha_saldo > TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS'))) AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(pr.prov_id_rif) AND
						p.paga_docu_id = ptr.trans_id AND
						ptr.docg_id = sp.sopg_id ".$condicionb." AND
						p.pres_anno != 2008 AND
						p.paga_fecha BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						p.esta_id != 15 AND p.esta_id != 2 AND
						pr.prov_esta_id != 15 AND pr.prov_esta_id != 2
					 ";
					$parentesis = " ))) ";
				}
				$GLOBALS['SafiRequestVars']['titulo'] = "OPERACIONES EN TR&Aacute;NSITO ";
				$query = 
				"
					SELECT
						cdi.comp_id AS sopg_id,
						cdi.comp_id AS pago_id,
						cb.ctab_numero AS numero_cuenta,
						CAST(cdi.comp_fec AS DATE) AS fecha_pagado_date,
						TO_CHAR(cdi.comp_fec, 'DD/MM/YYYY') AS fecha_pagado,
						'-',
						COALESCE(cdi.nro_referencia,'') AS referencia,
						CASE
							WHEN cdi.comp_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'codi%')
							THEN 'C'
						ELSE 'N'
						END AS condicion,
						COALESCE(reg.rcomp_debe,0)-COALESCE(reg.rcomp_haber,0) AS monto,
						COALESCE(cdi.comp_comen,'') AS comentario
					FROM
						sai_comp_diario cdi,
						sai_reng_comp reg,
						sai_ctabanco cb
					WHERE
						(cdi.comp_id NOT IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'codi%') OR
						cdi.comp_id IN (
							SELECT docg_id
							FROM sai_ctabanco_saldo
							WHERE
								docg_id LIKE 'codi%' AND
								fecha_saldo > TO_TIMESTAMP('".$fecha_fi2." 00:00:00','YYYY-MM-DD HH24:MI:SS'))) AND
								cdi.comp_id = reg.comp_id AND
								reg.cpat_id = cb.cpat_id ".$condicionb." AND
								cdi.esta_id != 15 AND
								cdi.comp_fec BETWEEN TO_DATE('".$fecha_in2."','YYYY-MM-DD') AND TO_DATE('".$fecha_fi2."','YYYY-MM-DD') AND
								cdi.comp_id LIKE 'codi%' ".$querytipopago." ".$parentesis." 
					ORDER BY numero_cuenta ASC, fecha_pagado_date ASC, referencia ASC";
			} else {
				
				if($params['tipoPago'] == 0)
				{
					$querytipopago = " UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						pch.pgch_id AS pago_id,
						cq.ctab_numero AS numero_cuenta,
						CAST(ctb.fecha_saldo AS DATE) AS fecha_pagado_date,
						TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(em.empl_nombres,''))||' '||UPPER(COALESCE(em.empl_apellidos,'')) AS beneficiario,
						COALESCE(ch.nro_cheque,'') AS referencia,
						CASE ch.estatus_cheque
							WHEN 15 THEN 'A'
							WHEN 45 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ch.monto_cheque,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_cheque ch,
						sai_chequera cq,
						sai_sol_pago sp,
						sai_pago_cheque pch,
						sai_empleado em,
						sai_ctabanco_saldo ctb
					WHERE
						p.paga_docu_id = ctb.docg_id AND
						ctb.docg_id LIKE 'pgch%' AND
						ctb.fecha_saldo BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(em.empl_cedula) AND
						p.paga_docu_id = pch.pgch_id AND
						pch.docg_id = sp.sopg_id AND
						ch.docg_id = sp.sopg_id AND
						ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
						p.pres_anno != 2008 AND
						p.esta_id != 15 AND p.esta_id != 2 AND ch.estatus_cheque != 15 /*AND
						em.esta_id != 15 AND em.esta_id != 2*/
					
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						pch.pgch_id AS pago_id,
						cq.ctab_numero AS numero_cuenta,
						CAST(ctb.fecha_saldo AS DATE) AS fecha_pagado_date,
						TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(v.benvi_nombres,''))||' '||UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiario,
						COALESCE(ch.nro_cheque,'') AS referencia,
						CASE ch.estatus_cheque
							WHEN 15 THEN 'A'
							WHEN 45 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ch.monto_cheque,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_cheque ch,
						sai_chequera cq,
						sai_sol_pago sp,
						sai_pago_cheque pch,
						sai_viat_benef v,
						sai_ctabanco_saldo ctb
					WHERE
						p.paga_docu_id = ctb.docg_id AND
						ctb.docg_id LIKE 'pgch%' AND
						fecha_saldo BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(v.benvi_cedula) AND
						TRIM(sp.sopg_bene_ci_rif) NOT IN (SELECT empl_cedula FROM sai_empleado WHERE esta_id = 1) AND
						p.paga_docu_id = pch.pgch_id AND
						pch.docg_id = sp.sopg_id AND
						ch.docg_id = sp.sopg_id AND
						ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
						p.pres_anno != 2008 AND
						p.esta_id != 15 AND p.esta_id != 2 AND ch.estatus_cheque != 15
						AND v.benvi_cedula NOT IN (SELECT empl_cedula FROM sai_empleado)
						/*AND
						v.benvi_esta_id != 15 AND v.benvi_esta_id != 2*/
					
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						pch.pgch_id AS pago_id,
						cq.ctab_numero AS numero_cuenta,
						CAST(ctb.fecha_saldo AS DATE) AS fecha_pagado_date,
						TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(pr.prov_nombre,'')) AS beneficiario,
						COALESCE(ch.nro_cheque,'') AS referencia,
						CASE ch.estatus_cheque
							WHEN 15 THEN 'A'
							WHEN 45 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ch.monto_cheque,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_cheque ch,
						sai_chequera cq,
						sai_sol_pago sp,
						sai_pago_cheque pch,
						sai_proveedor_nuevo pr,
						sai_ctabanco_saldo ctb
					WHERE
						p.paga_docu_id = ctb.docg_id AND
						ctb.docg_id LIKE 'pgch%' AND
						ctb.fecha_saldo BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(pr.prov_id_rif) AND
						TRIM(sp.sopg_bene_ci_rif) NOT IN (SELECT empl_cedula FROM sai_empleado WHERE esta_id = 1) AND
						p.paga_docu_id = pch.pgch_id AND
						pch.docg_id = sp.sopg_id AND
						ch.docg_id = sp.sopg_id AND
						ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
						p.pres_anno != 2008 AND
						p.esta_id != 15 AND p.esta_id != 2 AND ch.estatus_cheque != 15
						AND pr.prov_id_rif NOT IN (SELECT benvi_cedula FROM sai_viat_benef)
						AND pr.prov_id_rif NOT IN (SELECT empl_cedula FROM sai_empleado)
						/*AND
						pr.prov_esta_id != 15 AND pr.prov_esta_id != 2*/
					
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						ptr.trans_id AS pago_id,
						cb.ctab_numero AS numero_cuenta,
						CAST(ctb.fecha_saldo AS DATE) AS fecha_pagado_date,
						TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(em.empl_nombres,''))||' '||UPPER(COALESCE(em.empl_apellidos,'')) AS beneficiario,
						COALESCE(ptr.nro_referencia,'') AS referencia,
						CASE ptr.esta_id
							WHEN 15 THEN 'A'
							WHEN 10 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%') THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ptr.trans_monto,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_pago_transferencia ptr,
						sai_ctabanco cb,
						sai_sol_pago sp,
						sai_empleado em,
						sai_ctabanco_saldo ctb
					WHERE
						(cb.ctab_numero = ptr.nro_cuenta_emisor) AND
						p.paga_docu_id = ctb.docg_id AND
						ctb.docg_id LIKE 'tran%' AND
						ctb.fecha_saldo BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(em.empl_cedula) AND
						p.paga_docu_id = ptr.trans_id AND
						ptr.docg_id = sp.sopg_id ".$condicionb." AND
						p.pres_anno != 2008 AND
						p.esta_id != 15 AND p.esta_id != 2 /*AND
						em.esta_id != 15 AND em.esta_id != 2*/
					
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						ptr.trans_id AS pago_id,
						cb.ctab_numero AS numero_cuenta,
						CAST(ctb.fecha_saldo AS DATE) AS fecha_pagado_date,
						TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(v.benvi_nombres,''))||' '||UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiario,
						COALESCE(ptr.nro_referencia,'') AS referencia,
						CASE ptr.esta_id
							WHEN 15 THEN 'A'
							WHEN 10 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ptr.trans_monto,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_pago_transferencia ptr,
						sai_ctabanco cb,
						sai_sol_pago sp,
						sai_viat_benef v,
						sai_ctabanco_saldo ctb
					WHERE
						(cb.ctab_numero = ptr.nro_cuenta_emisor) AND
						p.paga_docu_id = ctb.docg_id AND
						ctb.docg_id LIKE 'tran%' AND
						ctb.fecha_saldo BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(v.benvi_cedula) AND
						TRIM(sp.sopg_bene_ci_rif) NOT IN (SELECT empl_cedula FROM sai_empleado WHERE esta_id = 1) AND
						p.paga_docu_id = ptr.trans_id AND
						ptr.docg_id = sp.sopg_id ".$condicionb." AND
						p.pres_anno != 2008 AND
						p.esta_id != 15 AND p.esta_id != 2
						AND v.benvi_cedula NOT IN (SELECT empl_cedula FROM sai_empleado)
						/*AND
						v.benvi_esta_id != 15 AND v.benvi_esta_id != 2*/
					
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						ptr.trans_id AS pago_id,
						cb.ctab_numero AS numero_cuenta,
						CAST(ctb.fecha_saldo AS DATE) AS fecha_pagado_date,
						TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(pr.prov_nombre,'')) AS beneficiario,
						COALESCE(ptr.nro_referencia,'') AS referencia,
						CASE ptr.esta_id
							WHEN 15 THEN 'A'
							WHEN 10 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ptr.trans_monto,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_pago_transferencia ptr,
						sai_ctabanco cb,
						sai_sol_pago sp,
						sai_proveedor_nuevo pr,
						sai_ctabanco_saldo ctb
					WHERE
						(cb.ctab_numero = ptr.nro_cuenta_emisor) AND
						p.paga_docu_id = ctb.docg_id AND
						ctb.docg_id LIKE 'tran%' AND
						ctb.fecha_saldo BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(pr.prov_id_rif) AND
						TRIM(sp.sopg_bene_ci_rif) NOT IN (SELECT empl_cedula FROM sai_empleado WHERE esta_id = 1) AND
						p.paga_docu_id = ptr.trans_id AND
						ptr.docg_id = sp.sopg_id ".$condicionb." AND
						p.pres_anno != 2008 AND
						p.esta_id != 15 AND p.esta_id != 2
						AND pr.prov_id_rif NOT IN (SELECT benvi_cedula FROM sai_viat_benef)
						AND pr.prov_id_rif NOT IN (SELECT empl_cedula FROM sai_empleado)
						/*AND
						pr.prov_esta_id != 15 AND pr.prov_esta_id != 2*/
					 ";
					$parentesis = " )))))) ";
				}
				if($params['tipoPago'] == 1)
				{
					$querytipopago = " UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						pch.pgch_id AS pago_id,
						cq.ctab_numero AS numero_cuenta,
						CAST(ctb.fecha_saldo AS DATE) AS fecha_pagado_date,
						TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(em.empl_nombres,''))||' '||UPPER(COALESCE(em.empl_apellidos,'')) AS beneficiario,
						COALESCE(ch.nro_cheque,'') AS referencia,
						CASE ch.estatus_cheque
							WHEN 15 THEN 'A'
							WHEN 45 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ch.monto_cheque,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_cheque ch,
						sai_chequera cq,
						sai_sol_pago sp,
						sai_pago_cheque pch,
						sai_empleado em,
						sai_ctabanco_saldo ctb
					WHERE
						p.paga_docu_id = ctb.docg_id AND
						ctb.docg_id LIKE 'pgch%' AND
						ctb.fecha_saldo BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(em.empl_cedula) AND
						p.paga_docu_id = pch.pgch_id AND
						pch.docg_id = sp.sopg_id AND
						ch.docg_id = sp.sopg_id AND
						ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
						p.pres_anno != 2008 AND
						p.esta_id != 15 AND p.esta_id != 2 AND ch.estatus_cheque != 15 /*AND
						em.esta_id != 15 AND em.esta_id != 2*/
					
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						pch.pgch_id AS pago_id,
						cq.ctab_numero AS numero_cuenta,
						CAST(ctb.fecha_saldo AS DATE) AS fecha_pagado_date,
						TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(v.benvi_nombres,''))||' '||UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiario,
						COALESCE(ch.nro_cheque,'') AS referencia,
						CASE ch.estatus_cheque
							WHEN 15 THEN 'A'
							WHEN 45 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ch.monto_cheque,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_cheque ch,
						sai_chequera cq,
						sai_sol_pago sp,
						sai_pago_cheque pch,
						sai_viat_benef v,
						sai_ctabanco_saldo ctb
					WHERE
						p.paga_docu_id = ctb.docg_id AND
						ctb.docg_id LIKE 'pgch%' AND
						fecha_saldo BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(v.benvi_cedula) AND
						TRIM(sp.sopg_bene_ci_rif) NOT IN (SELECT empl_cedula FROM sai_empleado WHERE esta_id = 1) AND
						p.paga_docu_id = pch.pgch_id AND
						pch.docg_id = sp.sopg_id AND
						ch.docg_id = sp.sopg_id AND
						ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
						p.pres_anno != 2008 AND
						p.esta_id != 15 AND p.esta_id != 2 AND ch.estatus_cheque != 15
						AND v.benvi_cedula NOT IN (SELECT empl_cedula FROM sai_empleado)
						/*AND
						v.benvi_esta_id != 15 AND v.benvi_esta_id != 2*/
					
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						pch.pgch_id AS pago_id,
						cq.ctab_numero AS numero_cuenta,
						CAST(ctb.fecha_saldo AS DATE) AS fecha_pagado_date,
						TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(pr.prov_nombre,'')) AS beneficiario,
						COALESCE(ch.nro_cheque,'') AS referencia,
						CASE ch.estatus_cheque
							WHEN 15 THEN 'A'
							WHEN 45 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'pgch%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ch.monto_cheque,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_cheque ch,
						sai_chequera cq,
						sai_sol_pago sp,
						sai_pago_cheque pch,
						sai_proveedor_nuevo pr,
						sai_ctabanco_saldo ctb
					WHERE
						p.paga_docu_id = ctb.docg_id AND
						ctb.docg_id LIKE 'pgch%' AND
						ctb.fecha_saldo BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(pr.prov_id_rif) AND
						TRIM(sp.sopg_bene_ci_rif) NOT IN (SELECT empl_cedula FROM sai_empleado WHERE esta_id = 1) AND
						p.paga_docu_id = pch.pgch_id AND
						pch.docg_id = sp.sopg_id AND
						ch.docg_id = sp.sopg_id AND
						ch.nro_chequera = cq.nro_chequera ".$condicionq." AND
						p.pres_anno != 2008 AND
						p.esta_id != 15 AND p.esta_id != 2 AND ch.estatus_cheque != 15
						AND pr.prov_id_rif NOT IN (SELECT benvi_cedula FROM sai_viat_benef)
						AND pr.prov_id_rif NOT IN (SELECT empl_cedula FROM sai_empleado)
						/*AND
						pr.prov_esta_id != 15 AND pr.prov_esta_id != 2*/
					 ";
					$parentesis = " ))) ";
				}
				if($params['tipoPago'] == 2)
				{
					$querytipopago = " UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						ptr.trans_id AS pago_id,
						cb.ctab_numero AS numero_cuenta,
						CAST(ctb.fecha_saldo AS DATE) AS fecha_pagado_date,
						TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(em.empl_nombres,''))||' '||UPPER(COALESCE(em.empl_apellidos,'')) AS beneficiario,
						COALESCE(ptr.nro_referencia,'') AS referencia,
						CASE ptr.esta_id
							WHEN 15 THEN 'A'
							WHEN 10 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%') THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ptr.trans_monto,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_pago_transferencia ptr,
						sai_ctabanco cb,
						sai_sol_pago sp,
						sai_empleado em,
						sai_ctabanco_saldo ctb
					WHERE
						(cb.ctab_numero = ptr.nro_cuenta_emisor) AND
						p.paga_docu_id = ctb.docg_id AND
						ctb.docg_id LIKE 'tran%' AND
						ctb.fecha_saldo BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(em.empl_cedula) AND
						p.paga_docu_id = ptr.trans_id AND
						ptr.docg_id = sp.sopg_id ".$condicionb." AND
						p.pres_anno != 2008 AND
						p.esta_id != 15 AND p.esta_id != 2 /*AND
						em.esta_id != 15 AND em.esta_id != 2*/
					
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						ptr.trans_id AS pago_id,
						cb.ctab_numero AS numero_cuenta,
						CAST(ctb.fecha_saldo AS DATE) AS fecha_pagado_date,
						TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(v.benvi_nombres,''))||' '||UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiario,
						COALESCE(ptr.nro_referencia,'') AS referencia,
						CASE ptr.esta_id
							WHEN 15 THEN 'A'
							WHEN 10 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ptr.trans_monto,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_pago_transferencia ptr,
						sai_ctabanco cb,
						sai_sol_pago sp,
						sai_viat_benef v,
						sai_ctabanco_saldo ctb
					WHERE
						(cb.ctab_numero = ptr.nro_cuenta_emisor) AND
						p.paga_docu_id = ctb.docg_id AND
						ctb.docg_id LIKE 'tran%' AND
						ctb.fecha_saldo BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(v.benvi_cedula) AND
						TRIM(sp.sopg_bene_ci_rif) NOT IN (SELECT empl_cedula FROM sai_empleado WHERE esta_id = 1) AND
						p.paga_docu_id = ptr.trans_id AND
						ptr.docg_id = sp.sopg_id ".$condicionb." AND
						p.pres_anno != 2008 AND
						p.esta_id != 15 AND p.esta_id != 2
						AND v.benvi_cedula NOT IN (SELECT empl_cedula FROM sai_empleado)
						/*AND
						v.benvi_esta_id != 15 AND v.benvi_esta_id != 2*/
					
					UNION (
					SELECT
						sp.sopg_id AS sopg_id,
						ptr.trans_id AS pago_id,
						cb.ctab_numero AS numero_cuenta,
						CAST(ctb.fecha_saldo AS DATE) AS fecha_pagado_date,
						TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY') AS fecha_pagado,
						UPPER(COALESCE(pr.prov_nombre,'')) AS beneficiario,
						COALESCE(ptr.nro_referencia,'') AS referencia,
						CASE ptr.esta_id
							WHEN 15 THEN 'A'
							WHEN 10 THEN
								CASE
									WHEN p.paga_docu_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'tran%')
									THEN 'C'
								ELSE 'N'
								END
						END AS condicion,
						COALESCE(ptr.trans_monto,0) AS monto,
						COALESCE(sp.sopg_detalle,'') AS comentario
					FROM
						sai_pagado p,
						sai_pago_transferencia ptr,
						sai_ctabanco cb,
						sai_sol_pago sp,
						sai_proveedor_nuevo pr,
						sai_ctabanco_saldo ctb
					WHERE
						(cb.ctab_numero = ptr.nro_cuenta_emisor) AND
						p.paga_docu_id = ctb.docg_id AND
						ctb.docg_id LIKE 'tran%' AND
						ctb.fecha_saldo BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						TRIM(sp.sopg_bene_ci_rif) = TRIM(pr.prov_id_rif) AND
						TRIM(sp.sopg_bene_ci_rif) NOT IN (SELECT empl_cedula FROM sai_empleado WHERE esta_id = 1) AND
						p.paga_docu_id = ptr.trans_id AND
						ptr.docg_id = sp.sopg_id ".$condicionb." AND
						p.pres_anno != 2008 AND
						p.esta_id != 15 AND p.esta_id != 2
						AND pr.prov_id_rif NOT IN (SELECT benvi_cedula FROM sai_viat_benef)
						AND pr.prov_id_rif NOT IN (SELECT empl_cedula FROM sai_empleado)
						/*AND
						pr.prov_esta_id != 15 AND pr.prov_esta_id != 2*/
					 ";
					$parentesis = " ))) ";
				}
				$GLOBALS['SafiRequestVars']['titulo'] = "LIBRO BANCO ";
				$query = 
					"
					SELECT
						cdi.comp_id AS sopg_id,
						cdi.comp_id AS pago_id,
						cb.ctab_numero AS numero_cuenta,
						CAST(ctb.fecha_saldo AS DATE) AS fecha_pagado_date,
						TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY') AS fecha_pagado,
						'-',
						COALESCE(cdi.nro_referencia,'') AS referencia,
						CASE
							WHEN cdi.comp_id IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE 'codi%')
							THEN 'C'
						ELSE 'N'
						END AS condicion,
						COALESCE(reg.rcomp_haber,0)-COALESCE(reg.rcomp_debe,0) AS monto,
						COALESCE(cdi.comp_comen,'') AS comentario
					FROM
						sai_comp_diario cdi,
						sai_reng_comp reg,
						sai_ctabanco cb,
						sai_ctabanco_saldo ctb
					WHERE
						ctb.docg_id = cdi.comp_id AND
						ctb.docg_id LIKE 'codi%' AND
						ctb.fecha_saldo BETWEEN TO_TIMESTAMP('".$fecha_in2." 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP('".$fecha_fi2." 23:59:59','YYYY-MM-DD HH24:MI:SS') AND
						cdi.comp_id = reg.comp_id AND
						reg.cpat_id = cb.cpat_id ".$condicionb." AND
						cdi.esta_id != 15 AND
						cdi.comp_id LIKE 'codi%' ".$querytipopago." ".$parentesis."
					ORDER BY numero_cuenta ASC,fecha_pagado_date ASC, referencia ASC";
			}
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$reporte = array();
			$i = 0;
		
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$reporte[$i]['sopg_id'] = $row["sopg_id"];
				$reporte[$i]['pago_id'] = $row["pago_id"];
				$reporte[$i]['nro_cuenta_bancaria'] = $row["numero_cuenta"];
				$reporte[$i]['fecha_pagado'] = $row["fecha_pagado"];
				$reporte[$i]['referencia'] = $row["referencia"];
				$reporte[$i]['condicion'] = $row["condicion"];
				$reporte[$i]['beneficiario'] = $row["beneficiario"];
				$reporte[$i]['comentario'] = $row["comentario"];
				$reporte[$i]['monto'] = $row["monto"];
				$i++;
					
					
			}
			if (strcmp($params['cuentaBancaria'], '-1')==0) 
				$GLOBALS['SafiRequestVars']['titulo2'] =" PARA TODAS LAS CUENTAS DEL ".$params['fechaInicio']." AL ".$params['fechaFin'];
			else 
				$GLOBALS['SafiRequestVars']['titulo2'] = " PARA LA CUENTA NRO. ". $params['cuentaBancaria']. " DEL ".$params['fechaInicio']." AL ".$params['fechaFin'];
			
			$GLOBALS['SafiRequestVars']['cuentaBancaria'] = $params['cuentaBancaria'];
			$GLOBALS['SafiRequestVars']['saldo_banco'] = $saldo_banco;
			$GLOBALS['SafiRequestVars']['fecha_inicio_antes'] = $fecha_inicio_antes;
			
			return $reporte;
		}
		catch(Exception $e){
			error_log($e);
			return null;
		}
	}
	
	public static function relacionContabilidad(array $params)
	{
		try
		{
			$preMsg = "Error al obtener resultados de la búsqueda";
			$queryWhere = "";
	
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
	
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
	
			if (strcmp($params['tipoBusqueda'], 'cheque') ==0 || strcmp($params['tipoBusqueda'], 'transferencia') ==0 ) {
				if (strcmp($params['tipoBusqueda'], 'cheque') ==0 ) { /*Búsqueda datos del cheque*/
						
					if (strcmp($params["opcion"],"0")==0) { 
						/*Por conciliar*/
						$query = "SELECT
									sh.id_cheque AS id_referencia,
									sh.docg_id AS docg_id,
									mb.ctab_numero AS numero_cuenta,
									TO_CHAR(sh.fechaemision_cheque,'DD/MM/YYYY') AS fecha, 
									sh.nro_cheque AS nro_referencia, 
									sh.monto_cheque AS monto,
									mb.conciliado AS conciliado
								FROM sai_cheque sh
								INNER JOIN sai_mov_cta_banco mb ON (mb.docg_id = sh.docg_id)
								WHERE 
									sh.id_cheque NOT IN (SELECT
																 id
														FROM acta_contabilidad_detalle
														WHERE id IS NOT NULL AND nro_acta IN (SELECT nro_acta FROM acta_contabilidad WHERE estatus != 15)
														)
									AND sh.estatus_cheque!=15
									AND  mb.conciliado=51 
									AND (sh.fechaemision_cheque LIKE '".($_SESSION['an_o_presupuesto']-1)."%' OR sh.fechaemision_cheque LIKE '".$_SESSION['an_o_presupuesto']."%') 
								ORDER BY 
									mb.ctab_numero, 
									sh.fechaemision_cheque";
				 	}	
				
					else if (strcmp($params["opcion"],"1")==0) {	
						/*Anulados*/
						$query = "SELECT
									sh.id_cheque AS id_referencia, 
									sh.docg_id AS docg_id, 
									ctab_numero AS numero_cuenta, 
									TO_CHAR(sh.fechaemision_cheque,'DD/MM/YYYY') AS fecha, 
									sh.nro_cheque AS nro_referencia, 
									sh.monto_cheque AS monto 
								FROM sai_cheque sh
								INNER JOIN sai_chequera cq ON (cq.nro_chequera = sh.nro_chequera)
								WHERE 
									sh.id_cheque NOT IN (SELECT
															 id
														FROM acta_contabilidad_detalle
														WHERE id IS NOT NULL AND nro_acta IN (SELECT nro_acta FROM acta_contabilidad WHERE estatus != 15)
														)
									AND sh.estatus_cheque=15
									AND (sh.fechaemision_cheque LIKE '".($_SESSION['an_o_presupuesto']-1)."%' OR sh.fechaemision_cheque LIKE '".($_SESSION['an_o_presupuesto'])."%' OR sh.fechaemision_cheque LIKE '".($_SESSION['an_o_presupuesto']-2)."%' OR sh.fechaemision_cheque LIKE '".($_SESSION['an_o_presupuesto']-3)."%') 
								ORDER BY sh.fechaemision_cheque";
					}
					else { 	
						/*Conciliados*/
						$query = "SELECT 
									sh.id_cheque AS id_referencia, 
									sh.docg_id AS docg_id, 
									mb.ctab_numero AS numero_cuenta, 
									TO_CHAR(sh.fechaemision_cheque,'DD/MM/YYYY') AS fecha, 
									sh.nro_cheque AS nro_referencia, 
									sh.monto_cheque AS monto,
									conciliado
								FROM sai_cheque sh
								INNER JOIN sai_mov_cta_banco mb ON (mb.docg_id = sh.docg_id)
								WHERE 
									sh.id_cheque NOT IN (SELECT 
															id 
														FROM acta_contabilidad_detalle
														WHERE id IS NOT NULL AND nro_acta IN (SELECT nro_acta FROM acta_contabilidad WHERE estatus != 15)
														)
									AND sh.estatus_cheque != 15 
									AND mb.conciliado = 50 
									AND (mb.fecha_descon LIKE '".$_SESSION['an_o_presupuesto']."%' OR mb.fecha_descon LIKE '".($_SESSION['an_o_presupuesto']-1)."%') 
									ORDER BY 
										mb.ctab_numero, 
										sh.fechaemision_cheque";
					}	
				}		
	
				else if (strcmp($params['tipoBusqueda'], 'transferencia') ==0 ) { /*Búsqueda datos del cheque*/
							
					$query = "SELECT
								pc.trans_id AS id_referencia,
								pc.docg_id AS docg_id,
								pc.nro_cuenta_emisor AS numero_cuenta,
								TO_CHAR(pc.trans_fecha,'DD/MM/YYYY') AS fecha,
								pc.nro_referencia AS nro_referencia,
								pc.trans_monto AS monto
							FROM sai_pago_transferencia pc
							INNER JOIN sai_mov_cta_banco b ON (b.docg_id = pc.docg_id)
							WHERE pc.nro_referencia NOT IN (SELECT nro_referencia
															FROM acta_contabilidad_detalle
															WHERE nro_referencia IS NOT NULL
															)
								 AND pc.esta_id != 15
								 AND b.conciliado = 51";
					
				}				
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
		
				while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$reporte[] = array('id_referencia' => $row['id_referencia'],'monto'=> $row['monto'],nro_referencia => $row['nro_referencia'], 'nro_cuenta'=> $row['numero_cuenta'], 'fecha' => $row['fecha'], 'docg_id'=> $row['docg_id']);
				}
					/*Fin búsqueda datos cheque*/
		
					return $reporte;
				}
			
			else if (strlen($params['numeroActa'])>1 || strlen($params['referencia'])>1 || strlen($params['sopg'])>1) { 
				if (strlen($params['numeroActa'])>1) {
					$query = "SELECT
								a.nro_acta AS nro_acta,
								a.movimiento AS movimiento,
								TO_CHAR(a.fecha_acta, 'dd/mm/yyyy') AS fecha_acta,
								ad.nro_registro AS nro_registro,
								TO_CHAR(ad.fecha_emision, 'dd/mm/yyyy') AS fecha_emision,	
								ad.nro_referencia AS nro_referencia,
								ad.beneficiario AS beneficiario,
								ad.concepto AS concepto,
								ad.monto AS monto,
								estatus AS estatus,
								ad.sopg AS sopg
							FROM
								acta_contabilidad a
							INNER JOIN acta_contabilidad_detalle ad ON (a.nro_acta = ad.nro_acta)
							WHERE 
								a.nro_acta='".$params["numeroActa"]."'
							ORDER BY ad.nro_registro";
				}
				else if (strlen($params['referencia'])>1 ) {
					$query = "SELECT
									a.nro_acta AS nro_acta,
									a.movimiento AS movimiento,
									TO_CHAR(a.fecha_acta, 'dd/mm/yyyy') AS fecha_acta,
									ad.nro_registro AS nro_registro,
									TO_CHAR(ad.fecha_emision, 'dd/mm/yyyy') AS fecha_emision,
									ad.nro_referencia AS nro_referencia,
									ad.beneficiario AS beneficiario,
									ad.concepto AS concepto,
									ad.monto AS monto,
									'' AS estatus,
									ad.sopg AS sopg  
							FROM
								acta_contabilidad a
								INNER JOIN acta_contabilidad_detalle ad ON (a.nro_acta=ad.nro_acta)
							WHERE
								nro_referencia='".$params["referencia"]."'
							ORDER BY ad.nro_registro";
					
				}		
			else if (strlen($params['sopg'])>1 ) {
					$query = "SELECT
									a.nro_acta AS nro_acta,
									a.movimiento AS movimiento,
									TO_CHAR(a.fecha_acta, 'dd/mm/yyyy') AS fecha_acta,
									ad.nro_registro AS nro_registro,
									TO_CHAR(ad.fecha_emision, 'dd/mm/yyyy') AS fecha_emision,
									ad.nro_referencia AS nro_referencia,
									ad.beneficiario AS beneficiario,
									ad.concepto AS concepto,
									ad.monto AS monto,
									'' AS estatus,
									ad.sopg AS sopg  
							FROM
								acta_contabilidad a
								INNER JOIN acta_contabilidad_detalle ad ON (a.nro_acta=ad.nro_acta)
							WHERE
								ad.sopg='".$params["sopg"]."'
							ORDER BY ad.nro_registro";
					
					
				}				
					
				$reporte = null;
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$reporte[] = array('nro_acta' => $row['nro_acta'],'movimiento'=> $row['movimiento'],fecha_acta => $row['fecha_acta'], 'nro_registro'=> $row['nro_registro'], 'fecha_emision' => $row['fecha_emision'], 'nro_referencia'=> $row['nro_referencia'], 'beneficiario'=> $row['beneficiario'], 'concepto'=> $row['concepto'], 'monto'=> $row['monto'], 'sopg'=> $row['sopg'], 'estatus'=> $row['estatus']);
				}
				/*Fin búsqueda datos cheque*/

				return $reporte;
								
			}
		}
		catch(Exception $e){
			error_log($e);
			return null;
		}
	}
	
	public static function relacionContabilidadAccion(array $params)
	{
		try
		{
			$preMsg = "Error al obtener resultados de la búsqueda";
			$queryWhere = "";
	
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
	
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
	
			if (strcmp($params['tipoBusqueda'], 'cheque') ==0 || strcmp($params['tipoBusqueda'], 'transferencia') ==0 ) {
				if (strcmp($params['tipoBusqueda'], 'cheque') ==0 ) { /*Búsqueda datos del cheque*/
	
					$query = "SELECT
									ch.nro_cheque AS numero,
									ch.monto_cheque AS monto,
									TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY') AS fecha,
									ch.ci_rif_beneficiario_cheque AS id_beneficiario,
									ch.beneficiario_cheque AS beneficiario,
									t.nombre_sol AS tipo_solicitud,
									cq.ctab_numero AS ctab_numero,
									ch.id_cheque AS id,
									ch.docg_id AS sopg
								FROM 
									sai_cheque ch
								INNER JOIN	sai_chequera cq ON (ch.nro_chequera=cq.nro_chequera)					
								INNER JOIN	sai_sol_pago s ON (ch.docg_id=s.sopg_id)
								LEFT OUTER JOIN	sai_tipo_solicitud t ON (s.sopg_tp_solicitud=t.id_sol)
								WHERE ch.id_cheque IN (".$params['codigo'].") 
								ORDER BY ". $params['order'];
				}
	
				else if (strcmp($params['tipoBusqueda'], 'transferencia') ==0 ) { /*Búsqueda datos del cheque*/
						
					$query = "SELECT
									p.nro_referencia AS numero,
									p.trans_monto AS monto, 
									TO_CHAR(p.trans_fecha, 'DD/MM/YYYY') AS fecha, 
									'' AS id_beneficiario, 
									p.beneficiario AS beneficiario, 
									t.nombre_sol AS tipo_solicitud,
									p.nro_cuenta_emisor AS ctab_numero,
									p.trans_id AS id,
									p.docg_id AS sopg
							FROM sai_pago_transferencia p
							INNER JOIN sai_sol_pago s ON (p.docg_id=s.sopg_id)
							INNER JOIN sai_tipo_solicitud t ON (s.sopg_tp_solicitud = t.id_sol)
							WHERE s.esta_id != 15 AND p.esta_id != 15 AND p.trans_id IN (".$params['codigo'].")
							ORDER BY p.trans_fecha";
						
				}

				$query2 = "INSERT	INTO acta_contabilidad (movimiento, fecha_acta)
						VALUES ('".$params['tipoBusqueda']."', '".DATE ('Y/m/d')."')";
				if(($result = $GLOBALS['SafiClassDb']->Query($query2)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				

				$query2 = "SELECT
								MAX(nro_acta) AS maximo
							FROM acta_contabilidad";

				if(($result = $GLOBALS['SafiClassDb']->Query($query2)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				

				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$numeroActa = $row['maximo'];
				}				
				
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				$i = 0;
				while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$i++;
					$reporte[] = array('id_referencia' => $row['id_referencia'],
										'monto'=> $row['monto'],
										'nro_registro'=> '',
										'nro_acta'=> $numeroActa,
										'nro_referencia' => $row['numero'], 
										'nro_cuenta'=> $row['ctab_numero'], 
										'id_beneficiario' => $row['id_beneficiario'],
										'beneficiario' => $row['beneficiario'],
										'fecha' => $row['fecha'], 
										'docg_id'=> $row['sopg'],
										'tipo_solicitud'=> $row['tipo_solicitud']);
					$query= "INSERT INTO acta_contabilidad_detalle (
																	nro_acta,
																	nro_registro,
																	fecha_emision, 
																	nro_referencia, 
																	beneficiario, 
																	concepto, 
																	monto, 
																	nro_cuenta, 
																	id, 
																	sopg) 
											VALUES (".$numeroActa.",
													".$i.",
													TO_DATE('".$row['fecha']."','DD/MM/YYYY'), 
													'".$row['numero']."', 
													'".$row['id_beneficiario'].' '.$row['beneficiario']."',
													'".$row['tipo_solicitud']."',
													'".$row['monto']."',
													'".$row['ctab_numero']."',
													'".$row['id']."',
													'".$row['sopg']."')";
					if(($result2 = $GLOBALS['SafiClassDb']->Query($query)) === false)
						throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				}
				/*Fin búsqueda datos cheque*/
	
				return $reporte;
			}
				
		}
		catch(Exception $e){
			error_log($e);
			return null;
		}
	}

	public static function relacionContabilidadImprimir(array $params)
	{
		try
		{
			$preMsg = "Error al obtener resultados de la búsqueda";
			$queryWhere = "";
	
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
	
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
	
			if (strlen($params['numeroActa'])>0) {

	
					$query = "SELECT 
									c.nro_acta AS nro_acta, 
									c.movimiento AS movimiento, 
									TO_CHAR(c.fecha_acta, 'DD/MM/YYYY') AS fecha, 
									cd.nro_registro AS nro_registro, 
									TO_CHAR(cd.fecha_emision, 'DD/MM/YYYY') AS fecha_emision, 
									cd.nro_referencia AS nro_referencia, 
									cd.beneficiario AS beneficiario, 
									cd.concepto AS concepto, 
									cd.monto AS monto, 
									cd.sopg AS sopg
							FROM acta_contabilidad c 
							INNER JOIN acta_contabilidad_detalle cd ON (c.nro_acta = cd.nro_acta)
							WHERE c.nro_acta=". $params['numeroActa'] ."
							ORDER BY cd.nro_registro";
				
	
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
	
				$i = 0;
				while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$i++;
					$reporte[] = array('nro_acta' => $row['nro_acta'],
							'movimiento'=> $row['movimiento'],
							'fecha' => $row['fecha'],
							'nro_registro'=> $row['nro_registro'],
							'fecha_emision' => $row['fecha_emision'],
							'beneficiario' => $row['beneficiario'],
							'nro_referencia' => $row['nro_referencia'],
							'beneficiario'=> $row['beneficiario'],
							'tipo_solicitud'=> $row['concepto'],
							'sopg'=> $row['sopg'],
							'monto'=> $row['monto']);
					
				}
				/*Fin búsqueda datos cheque*/
	
				return $reporte;
			}
	
		}
		catch(Exception $e){
			error_log($e);
			return null;
		}
	}

	public static function SaldosCorrectos(array $params)
	{
		try
		{
			$preMsg = "Error al obtener resultados de la búsqueda";
			$queryWhere = "";
	
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
	
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
	
			$fecha_inicio = substr($params['fecha'],6,4)."-".substr($params['fecha'],3,2)."-01";
			$fecha = substr($params['fecha'],6,4)."-".substr($params['fecha'],3,2)."-".substr($params['fecha'],0,2);
			$fecha_mes2 = substr($params['fecha'],3,2)."/".substr($params['fecha'],6,4);
			
			$ano = substr($params['fecha'],6,4);
			$ano_antes = $ano-1;
			$mes = intval(substr($fecha, 5, 2));
			
			// Borrar
			/*
			echo "<pre>";
			echo "params: " . print_r($params, true) . "\n";
			echo "fecha_inicio: " . print_r($fecha_inicio, true) . "\n";
			echo "fecha: " . print_r($fecha, true) . "\n";
			echo "fecha_mes2: " . print_r($fecha_mes2, true) . "\n";
			echo "ano: " . print_r($ano, true) . "\n";
			echo "ano_antes: " . print_r($ano_antes, true) . "\n";
			echo "mes: " . print_r($mes, true) . "\n";
			echo "</pre>";
			*/
			// Fin Borrar
					
			/*Inicio de Cálculo de saldos finales*/
			/*Obtención de la cuenta contable, asociada a la cuenta bancaria*/			
			$query = "SELECT
					 		cpat_id
					FROM sai_ctabanco
					WHERE ctab_numero='".$params['cuentaBancaria']."'";

	
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$cuentaContable = $row["cpat_id"];
			} else {
				throw new Exception($preMsg.' Detalles: No se pudo encontrar la cuenta contable');
			}
			
			/*Obtener saldo del mes, si existe en la tabla safi_saldo_contable*/
			$query_saldo = "
				SELECT
					cpat_id, 
					saldo
				FROM
					safi_saldo_contable
				WHERE
					mes=".$mes."
					AND ano=".$ano." 
					AND cpat_id='".$cuentaContable."'
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query_saldo)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			/*Si no existe registro en saldo contable*/
			if ($GLOBALS['SafiClassDb']->CountResult($result) < 1)
			{
				$ano = substr($params['fecha'],6,4);
				$mes = substr($fecha, 5, 2)-1;
				
				// Borrar
				/*
				echo "<pre>";
				echo "ano 2: " . print_r($ano, true) . "\n";
				echo "mes 2: " . print_r($mes, true) . "\n";
				echo "</pre>";
				*/
				// Fin Borrar
				
				$query_saldo = "
					SELECT 
						COALESCE(MAX(mes),0) AS mes 
					FROM
						safi_saldo_contable 
					WHERE
						ano=".$ano."
						--AND mes <".$mes . "
						AND mes <=".$mes . "
				";
				
				// Borrar
				/*
				echo "<pre>";
				echo print_r($query_saldo, true);
				echo "</pre>";
				*/
				// Fin Borrar
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query_saldo)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				/*Se encuentra registro dentro del año en curso (1)*/
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$mes = $row["mes"];
					// Borrar
					/*
					echo "<pre>";
					echo "Mes 3: ".print_r($mes, true);
					echo "</pre>";
					*/
					// Fin Borrar
				}				
				
				if ($mes < 1)
				{
					$query_saldo = "
						SELECT 
							COALESCE(MAX(mes),0) AS mes 
						FROM
							safi_saldo_contable
						WHERE
							ano=".$ano_antes."
					";
					
					
					if(($result = $GLOBALS['SafiClassDb']->Query($query_saldo)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					
					/*Se encuentra registro en el año anterior*/
					if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
						$mes = $row["mes"];
					}				
					/*No existen registros en saldos_contables de años anteriores*/
					if ($mes < 1) {
						$max_mes_antes = 0;
						$error = 1;
					}
					/*Existen registros del año anterior*/
					else {
						$max_mes_antes = $mes;
						$query_saldo = "
							SELECT
								cpat_id, 
								saldo 
							FROM
								safi_saldo_contable 
							WHERE
								mes = ".$max_mes_antes." 
								AND ano = ".$ano_antes." 
								AND cpat_id = '".$cuentaContable."'
						";
						
					}
				}
				/*Se encuentra registro dentro del año en curso (2)*/ 
				else {
					//$max_mes=$row["mes"];
					$max_mes = $mes;
					$query_saldo = "
						SELECT
							cpat_id, 
							saldo 
						FROM
							safi_saldo_contable 
						WHERE
							mes = ".$mes." 
							AND ano = ".$ano." 
							AND cpat_id = '".$cuentaContable."'
					";
					
					// Borrar
					/*
					echo "<pre>";
					echo print_r($query_saldo, true);
					echo "</pre>";
					*/
					// Fin Borrar
					
					
				}
			}
			else { /*Si existe registro en saldo contable en el año actual y el mes en curso*/
				$max_mes = $mes;
			}
			
			// Borrar
			/*
			echo "<pre>";
			echo "max_mes: " . $max_mes;
			echo "</pre>";
			*/
			// Fin Borrar
			
			if ($max_mes != 0) {
				if ($max_mes == 1 || $max_mes == 2 || $max_mes == 3 || $max_mes == 4 || $max_mes == 5 || $max_mes == 6 || $max_mes == 7 || $max_mes == 8 || $max_mes == 9) {
					$mes_x = $max_mes;
					$max_mes = "0".$max_mes;
			
				}
				$mes_total = $max_mes;
				$ano_total = $ano;
			}
			else {
				
				// Borrar
				/*
				echo "<pre>";
				echo "max_mes_antes: " . $max_mes_antes;
				echo "</pre>";
				*/
				// Fin Borrar
				
				if ($max_mes_antes != 0) {
					if ($max_mes_antes == 1 || $max_mes_antes == 2 || $max_mes_antes == 3 || $max_mes_antes == 4 || $max_mes_antes == 5 || $max_mes_antes == 6 || $max_mes_antes == 7 || $max_mes_antes == 8 || $max_mes_antes == 9) {
						$mes_x = $max_mes_antes;
						$max_mes_antes = "0".$max_mes_antes;
			
					}
					$mes_total = $max_mes_antes;
					$ano_total = $ano-1;
				}
			}
			
			/*Fin de Cálculo de saldos finales*/
			
			if ($error!=1) {
				$fechaIinicio = "01/".$mes_total."/".$ano_total;
				$fechaFfin = $fecha_inicio;
		
				// Borrar
				/*
				echo "<pre>";
				echo print_r($query_saldo, true);
				echo "</pre>";
				*/
				// Fin Borrar
						
				// Borrar
				/*
				echo "<pre>";
				echo "Fecha Inico: " . $fechaIinicio . "\n";
				echo "Fecha Fin: " . $fechaFfin . "\n";
				echo "Mes_total: " . $mes_total . "\n";
				echo "Ano total: " . $ano_total . "\n";
				echo "</pre>";
				*/
				// Fin Borrar
					
				/*saldoDiarioSaldosCorrectos*/
				$nucleo = "SELECT
							cpat_id, 
							SUM(rcomp_debe) AS rcomp_debe,
							SUM(rcomp_haber) AS rcomp_haber
						FROM
							sai_reng_comp src
						INNER JOIN sai_comp_diario scd ON (src.comp_id = scd.comp_id )
						WHERE src.cpat_id='".$cuentaContable."' AND
							scd.esta_id!='15' AND
							scd.comp_fec BETWEEN
							TO_DATE('".$fechaIinicio."', 'DD/MM/YYYY') AND
							TO_DATE('".$fechaFfin."', 'YYYY-MM-DD')-1
						GROUP BY src.cpat_id";
	
				$nucleo2 = "SELECT
								scp.cpat_id,
								CASE
									WHEN POSITION('6' IN scp.cpat_id) = 1 OR POSITION('1' IN scp.cpat_id) = 1 OR POSITION('4' IN scp.cpat_id) = 1 THEN
									sd.saldo + scp.rcomp_debe - scp.rcomp_haber
								ELSE
									sd.saldo - scp.rcomp_debe + scp.rcomp_haber
								END AS cpat_sal_actual
						FROM
							(".$query_saldo.") sd
						INNER JOIN (".$nucleo.") scp ON (scp.cpat_id = sd.cpat_id)";
		
				$nucleo3 = "SELECT
								sc2.cpat_id,
								sc2.cpat_sal_actual
							FROM
								(".$nucleo2.") sc2
	 						UNION
	 							".$query_saldo." 
	 						AND cpat_id NOT IN (SELECT cpat_id FROM (".$nucleo2.") sc)";
			
				/*Llenado de saldo diario por dia de cuentas que tuvieron movimiento*/
				$query_saldo = "SELECT
									TO_DATE(TO_CHAR(now(), 'YYYY MM DD'), 'YYYY MM DD') AS fecha,
									cpat_id,
									cpat_sal_actual AS saldo
								 FROM
									(".$nucleo3.") scp ";
				
			
				if(($result = $GLOBALS['SafiClassDb']->Query($query_saldo)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				// Borrar
				/*
				echo "<pre>";
				echo print_r($query_saldo, true);
				echo "</pre>";
				*/
				// Fin Borrar
				
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$_SESSION['resultados'] = $row['saldo'];
				}
			}	
			
			/*saldoDiarioSaldosCorrectos*/
				
			/*Saldo final banco*/
			$query = "SELECT 
							monto_haber
						FROM sai_ctabanco_saldo
		   				WHERE docg_id LIKE 'sb-".$ano."%' 
		   					AND ctab_numero='".$params['cuentaBancaria']."'";
				
				
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$saldo_banco = $row["monto_haber"] ;
			}
				
			$query = "SELECT
					 		SUM(monto_debe) AS suma_debe
						FROM sai_ctabanco_saldo
		   				WHERE ctab_numero='".$params['cuentaBancaria']."'
		   					AND fecha_saldo LIKE '".$ano."%'
							AND fecha_saldo < TO_TIMESTAMP('".$fecha_inicio." 00:00:00','YYYY-MM-DD HH24:MI:SS')  
   							AND docg_id NOT LIKE 'sb%' 
		   					AND docg_id NOT LIKE 'si%' 
		   					AND docg_id NOT IN (SELECT 
		   											docg_id
		   										FROM sai_doc_genera 
		   										WHERE esta_id = 15)";
				
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$saldo_banco = $saldo_banco - $row["suma_debe"] ;
			}
				
			$query = "SELECT 
							SUM(monto_haber) AS suma_haber 
						FROM sai_ctabanco_saldo
		  				WHERE ctab_numero='".$params['cuentaBancaria']."' 
		  						AND fecha_saldo LIKE '".$ano."%' 
		  						AND fecha_saldo < TO_TIMESTAMP('".$fecha_inicio." 00:00:00','YYYY-MM-DD HH24:MI:SS') 
		  						AND docg_id NOT LIKE 'sb%'
		  						AND docg_id NOT LIKE 'si%' 
		  						AND docg_id NOT IN (SELECT 
		  												docg_id 
		  											FROM sai_doc_genera 
		  											WHERE esta_id = 15)";
				
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			
			
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$saldo_banco = $saldo_banco + $row["suma_haber"] ;
			}
			/*Fin de cálculo de saldo final banco*/
			
			
			$WhereFechaConciliado=" AND ctb.fecha_saldo LIKE  '".substr($params['fecha'],6,4)."-".substr($params['fecha'],3,2)."-%'";
			$WhereFechaTransito=" AND c.paga_fecha <= TO_TIMESTAMP('".$fecha." 23:59:59','YYYY-MM-DD HH24:MI:SS')";
			
			
			$sumatoria_cheq_mes_transito = 0; //Contabilidad
			$sumatoria_cheq_transito_banco=0;
			
			$sumatoria_codi_transito = 0;
			
			$sumatoria_cheq_anulado=0;
			
			$sumatoria_cheq_conciliado_contabilidad = 0;
			$sumatoria_cheq_conciliado_banco = 0;
			
			$sumatoria_codi_conciliado_contabilidad = 0;
			$sumatoria_codi_conciliado_banco = 0;
			
			
			/***************************************************************************
			 ***************** Querys de depósitos/débitos concialidos ***************** 
			 **************************************************************************/

			$query_codi_conciliado = "
				SELECT
					cdi.nro_referencia AS referencia,
					UPPER(cdi.comp_comen) AS comentario,
					TO_CHAR(cdi.comp_fec, 'DD/MM/YYYY') AS fecha_emision,
					COALESCE(TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY'),'') AS fecha,
					ctb.monto_haber-ctb.monto_debe AS monto,
					cdi.comp_id AS id_documento
				FROM
					sai_comp_diario cdi
				INNER JOIN
					sai_ctabanco_saldo ctb ON (ctb.docg_id = cdi.comp_id)
				WHERE
					ctb.ctab_numero='".$params['cuentaBancaria']."'
					AND cdi.comp_id LIKE 'codi%'
					AND cdi.comp_fec NOT LIKE '2008%'
					AND cdi.esta_id != 15 ". $WhereFechaConciliado . "
				ORDER BY
					cdi.comp_fec
			";
				
			// Borrar
				
			/*
			 echo "<pre>";
			print_r($query_codi_conciliado);
			echo "</pre>";
			*/
			/**********************************************************************************
			 ***************** Fin de Querys de depósitos/débitos concialidos *****************
			**********************************************************************************/
			
			
			
			
			/********************************************************************************
			 ***************** Querys de cheques/transferencias concialidos *****************
			********************************************************************************/
			
			$query_cheq_tran_conciliado = "
					
				-- Cheques conciliados
					
				SELECT
					ch.nro_cheque AS referencia,
					COALESCE(TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY'),'') AS fecha,
					COALESCE(TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY'),'') AS fecha_emision,
					--SELECT (TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY'),'') AS fecha_emision,
					--COALESCE(TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY'),'') AS fecha_emision,
					( SELECT COALESCE(TO_CHAR(MIN(cd.fechaemision_cheque), 'DD/MM/YYYY'),'') FROM sai_cheque cd WHERE docg_id = ch.docg_id GROUP BY docg_id) AS fecha_minima,
					--( SELECT COALESCE(TO_CHAR(ce.fecha_accion_cheque, 'DD/MM/YYYY'),'') FROM sai_cheque_estados ce WHERE estatus_cheque=15 AND id_cheque = (SELECT id_cheque FROM sai_cheque cq WHERE docg_id = ch.docg_id AND cq.fechaemision_cheque = (SELECT MIN(cd.fechaemision_cheque) FROM sai_cheque cd WHERE docg_id = ch.docg_id ) ) ) AS fecha_anulacion,
					--( SELECT COALESCE(TO_CHAR(MAX(ce.fecha_accion_cheque), 'DD/MM/YYYY'),'') FROM sai_cheque_estados ce WHERE id_cheque = (SELECT id_cheque FROM sai_cheque cq WHERE docg_id = ch.docg_id GROUP BY docg_id HAVING MIN(cq.fechaemision_cheque)) GROUP BY docg_id) AS fecha_anulacion,
					--( SELECT COALESCE(TO_CHAR(ce.fecha_accion_cheque, 'DD/MM/YYYY'),'') FROM sai_cheque_estados ce WHERE estatus_cheque=15 AND id_cheque = (SELECT id_cheque FROM sai_cheque cq WHERE docg_id = ch.docg_id AND cq.fechaemision_cheque = (SELECT MIN(cd.fechaemision_cheque) FROM sai_cheque cd WHERE docg_id = ch.docg_id ) ) ) AS fecha_anulacion,
					/*(
						SELECT
							COALESCE(TO_CHAR(ce.fecha_accion_cheque, 'DD/MM/YYYY'),'')
						FROM
							sai_cheque_estados ce 
						WHERE
							estatus_cheque=15 
							AND id_cheque = (
								SELECT 
									id_cheque
								FROM
									sai_cheque cq 
								WHERE
									estatus_cheque = 15 
									AND docg_id = ch.docg_id
									AND cq.fechaemision_cheque = (
										SELECT 
											MAX(cd.fechaemision_cheque) 
										FROM
											sai_cheque cd 
										WHERE
											docg_id = ch.docg_id
											AND estatus_cheque = 15
									)
							)
					) AS fecha_anulacion,*/
					(
						SELECT
							COALESCE(TO_CHAR(MAX(ce.fecha_accion_cheque), 'DD/MM/YYYY'), '')
						FROM
							sai_cheque_estados ce
							INNER JOIN sai_cheque cq ON (cq.id_cheque = ce.id_cheque)
							INNER JOIN (
								SELECT
									MAX(cd.fechaemision_cheque) AS fechaemision_cheque
								FROM
									sai_cheque cd 
								WHERE
									estatus_cheque = 15
									AND cd.docg_id = ch.docg_id
							) cmax ON (cmax.fechaemision_cheque = cq.fechaemision_cheque)
						WHERE
							ce.estatus_cheque = 15
							AND cq.estatus_cheque = 15
							AND cq.docg_id = ch.docg_id
					) AS fecha_anulacion,
					--COALESCE(TO_CHAR(p.pgch_fecha, 'DD/MM/YYYY'),'') AS fecha_emision,						
					--COALESCE(TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY'),'') AS fecha_emision,							
					beneficiario_cheque AS beneficiario, 
					ci_rif_beneficiario_cheque AS id_beneficiario,
					monto_cheque AS monto,
					COALESCE(ctb.monto_debe,0) + COALESCE(ctb.monto_haber,0) AS monto_contable,
					p.docg_id AS id_documento
				FROM
					sai_cheque ch
					INNER JOIN sai_chequera cq ON (cq.nro_chequera = ch.nro_chequera)
					INNER JOIN sai_pago_cheque p ON (p.docg_id = ch.docg_id)
					INNER JOIN sai_ctabanco_saldo ctb ON (ctb.docg_id = p.pgch_id)
				WHERE
					cq.ctab_numero = '".$params['cuentaBancaria']."'
					AND ch.fechaemision_cheque <= TO_TIMESTAMP('".$fecha." 23:59:59','YYYY-MM-DD HH24:MI:SS') 
					". $WhereFechaConciliado . "
					AND ch.estatus_cheque!=15
					AND p.esta_id != 15
					AND p.esta_id != 2
					
				UNION
							
				-- Transferencia conciliadas
							
				SELECT
					ptr.nro_referencia AS referencia,
					COALESCE(TO_CHAR(ctb.fecha_saldo, 'DD/MM/YYYY'),'') AS fecha,
					COALESCE(TO_CHAR(ptr.trans_fecha, 'DD/MM/YYYY'),'') AS fecha_emision,
					COALESCE(TO_CHAR(ptr.trans_fecha, 'DD/MM/YYYY'),'') AS fecha_minima,															
					COALESCE(TO_CHAR(ptr.trans_fecha, 'DD/MM/YYYY'),'') AS fecha_anulacion,
					ptr.beneficiario AS beneficiario,
					ptr.rif_ci AS id_beneficiario,
					ptr.trans_monto AS monto,
					COALESCE(ctb.monto_debe,0) + COALESCE(ctb.monto_haber,0) AS monto_contable,
					ptr.docg_id AS id_documento
				FROM
					sai_pago_transferencia ptr
					INNER JOIN sai_ctabanco_saldo ctb ON (ctb.docg_id = ptr.trans_id)
				WHERE
					ptr.nro_cuenta_emisor = '".$params['cuentaBancaria']."'
					AND ptr.trans_fecha <= TO_TIMESTAMP('".$fecha." 23:59:59','YYYY-MM-DD HH24:MI:SS') 
					". $WhereFechaConciliado . "
					AND ptr.esta_id != 15
					AND ptr.esta_id != 2
			";
			
			// Borrar
			/*
			echo "<pre>";
			print_r($query_cheq_tran_conciliado);
			echo "</pre>";
			*/
			
			/************************************************************************************
			 ***************** Fin Querys de cheques/transferencias concialidos ***************** 
			 ***********************************************************************************/
			
			
			
			
			/********************************************************************************
			 ***************** Querys de cheques/transferencias en tránsito *****************
			 *******************************************************************************/
				
			$query_cheq_tran_transito = "
			
				-- Cheques conciliados después de la fecha
					
				SELECT
					ch.nro_cheque AS referencia,
					--COALESCE(TO_CHAR(s.sopg_fecha, 'DD/MM/YYYY'),'') AS fecha,
					--(SELECT COALESCE(TO_CHAR(MIN(fechaemision_cheque),'DD/MM/YYYY'),'') FROM sai_cheque WHERE docg_id=s.sopg_id) AS fecha,
					--(SELECT COALESCE(TO_CHAR(fechaemision_cheque,'DD/MM/YYYY'),'') FROM sai_cheque WHERE docg_id=s.sopg_id) AS fecha,
					COALESCE(TO_CHAR(ch.fechaemision_cheque, 'DD/MM/YYYY'),'') AS fecha,
					beneficiario_cheque AS beneficiario,
					ci_rif_beneficiario_cheque AS id_beneficiario,
					monto_cheque AS monto,
					s.sopg_id AS id_documento
				FROM
					sai_cheque ch
					INNER JOIN sai_chequera cq ON (cq.nro_chequera = ch.nro_chequera)
					INNER JOIN sai_cheque_estados ce ON (ce.id_cheque = ch.id_cheque)
					INNER JOIN sai_sol_pago s ON (s.sopg_id = ch.docg_id)
					INNER JOIN sai_pago_cheque p ON (p.docg_id = ch.docg_id)
					INNER JOIN sai_ctabanco_saldo ctb ON (ctb.docg_id = p.pgch_id)
				WHERE
					cq.ctab_numero = '".$params['cuentaBancaria']."'
					AND ce.estatus_cheque = 45
					AND ce.fecha_accion_cheque <= TO_TIMESTAMP('".$fecha." 23:59:59','YYYY-MM-DD HH24:MI:SS')
					AND ch.fechaemision_cheque <= TO_TIMESTAMP('".$fecha." 23:59:59','YYYY-MM-DD HH24:MI:SS')
					AND ctb.fecha_saldo > TO_TIMESTAMP('".$fecha." 23:59:59','YYYY-MM-DD HH24:MI:SS')
					AND ch.estatus_cheque != 15
					AND p.esta_id != 15
					AND p.esta_id != 2
							
				UNION
							
				-- Cheques emitidos pero no conciliados
							
				SELECT
					ch.nro_cheque AS referencia,
					COALESCE(TO_CHAR(fechaemision_cheque, 'DD/MM/YYYY'),'') AS fecha,
					beneficiario_cheque AS beneficiario,
					ci_rif_beneficiario_cheque AS id_beneficiario,
					monto_cheque AS monto,
					p.docg_id AS id_documento
				FROM
					(
						SELECT
							ch.nro_cheque,
							ch.fechaemision_cheque,
							ch.beneficiario_cheque,
							ch.ci_rif_beneficiario_cheque,
							ch.monto_cheque,
							ch.nro_chequera,
							ch.docg_id
						FROM
							sai_cheque ch
							INNER JOIN sai_mov_cta_banco mv ON (mv.docg_id = ch.docg_id)
							INNER JOIN sai_cheque_estados ce ON (ce.id_cheque = ch.id_cheque)														
						WHERE
							mv.fechaemision_cheque <= TO_TIMESTAMP('".$fecha." 23:59:59','YYYY-MM-DD HH24:MI:SS')
							AND mv.fechaemision_cheque NOT LIKE '2008%'		
							AND ch.estatus_cheque != 15
							AND ce.estatus_cheque = 45 
							AND ce.fecha_accion_cheque <= TO_TIMESTAMP('".$fecha." 23:59:59','YYYY-MM-DD HH24:MI:SS')
					) AS ch
					INNER JOIN
					(
						SELECT
							nro_chequera
						FROM
							sai_chequera
						WHERE
							ctab_numero = '".$params['cuentaBancaria']."'
					) AS cq ON (cq.nro_chequera = ch.nro_chequera)
					INNER JOIN
					(
						--SELECT 
							--p.pgch_id, 
							--p.docg_id 
						--FROM sai_pago_cheque p 
						--WHERE 
							--p.pgch_id NOT IN
								--(
									--SELECT
									--	docg_id
									--FROM sai_ctabanco_saldo
									--WHERE 
									--	docg_id LIKE 'pgch%'
								--) AND
						SELECT
						      p.pgch_id,
						      p.docg_id
				     
						FROM
							sai_pago_cheque p
							LEFT OUTER JOIN
							(
								SELECT
									docg_id
								FROM
									sai_ctabanco_saldo
								WHERE
									docg_id LIKE 'pgch%'
							) AS s ON (p.pgch_id = s.docg_id)
						WHERE
							s.docg_id IS NULL
							AND p.esta_id != 15
							AND p.esta_id != 2	
							
					) AS p ON (p.docg_id = ch.docg_id)
				
				UNION
									
				-- Cheques Anulados
									
				SELECT
					ch.nro_cheque AS referencia,
					--COALESCE(TO_CHAR(fechaemision_cheque, 'DD/MM/YYYY'),'') AS fecha,
					(SELECT COALESCE(TO_CHAR(MIN(fechaemision_cheque),'DD/MM/YYYY'),'') FROM sai_cheque WHERE docg_id=p.docg_id and sai_cheque.id_cheque = ch.id_cheque) AS fecha,
					beneficiario_cheque AS beneficiario,
					ci_rif_beneficiario_cheque AS id_beneficiario,
					monto_cheque AS monto,
					p.docg_id AS id_documento
				FROM
					sai_cheque ch
					INNER JOIN sai_chequera cq ON (cq.nro_chequera = ch.nro_chequera)
					INNER JOIN sai_pago_cheque p ON (p.docg_id = ch.docg_id)
					INNER JOIN sai_cheque_estados ce ON (ce.id_cheque = ch.id_cheque)
				WHERE
					cq.ctab_numero = '".$params['cuentaBancaria']."'
					AND ch.fechaemision_cheque <= TO_TIMESTAMP('".$fecha." 23:59:59','YYYY-MM-DD HH24:MI:SS')
					AND ch.fechaemision_cheque NOT LIKE '2008%'
					AND ce.comentario LIKE 'A%-%' AND
					ce.estatus_cheque = 15
					AND
					(
						ce.fecha_accion_cheque > TO_TIMESTAMP('".$fecha." 23:59:59','YYYY-MM-DD HH24:MI:SS')
						OR ce.fecha_accion_cheque LIKE '".substr($params['fecha'],6,4)."-".substr($params['fecha'],3,2)."-%'
					)
				
				UNION
								
				-- Transferencia conciliadas
								
				SELECT
					ptr.nro_referencia AS referencia,
					COALESCE(TO_CHAR(ptr.trans_fecha, 'DD/MM/YYYY'),'') AS fecha,
					ptr.beneficiario AS beneficiario,
					ptr.rif_ci AS id_beneficiario,
					ptr.trans_monto AS monto,
					ptr.docg_id AS id_documento
				FROM
					sai_pago_transferencia ptr
					INNER JOIN sai_ctabanco_saldo ctb ON (ctb.docg_id = ptr.trans_id)
				WHERE
					ptr.nro_cuenta_emisor = '".$params['cuentaBancaria']."'
					AND ptr.trans_fecha <= TO_TIMESTAMP('".$fecha." 23:59:59','YYYY-MM-DD HH24:MI:SS')
					AND ptr.trans_fecha NOT LIKE '2008%'
					AND ctb.fecha_saldo > TO_TIMESTAMP('".$fecha." 23:59:59','YYYY-MM-DD HH24:MI:SS')
					AND ptr.esta_id != 15
					AND ptr.esta_id != 2
				
				UNION
							
				--Transferencias no conciliadas
							
				SELECT
					ptr.referencia AS referencia,
					ptr.fecha AS fecha,
					ptr.beneficiario AS beneficiario,
					ptr.id_beneficiario AS id_beneficiario,
					ptr.monto AS monto,
					ptr.id_documento AS id_documento
				FROM
					(
						SELECT
							nro_referencia AS referencia,
							COALESCE(TO_CHAR(trans_fecha, 'DD/MM/YYYY'),'') AS fecha,
							beneficiario AS beneficiario,
							rif_ci AS id_beneficiario,
							trans_monto AS monto,
							docg_id AS id_documento
						FROM
							sai_pago_transferencia
						WHERE
							nro_cuenta_emisor = '".$params['cuentaBancaria']."'
							AND trans_fecha <= TO_TIMESTAMP('".$fecha." 23:59:59','YYYY-MM-DD HH24:MI:SS')
							AND trans_fecha NOT LIKE '2008%'
							AND esta_id != 15
							AND esta_id != 2
							AND trans_id NOT IN
							(
								SELECT
									docg_id
								FROM
									sai_ctabanco_saldo
								WHERE
									docg_id LIKE 'tran%'
									AND ctab_numero = '".$params['cuentaBancaria']."'
							)
					
					) AS ptr
			";
			
			/*
			echo "<pre>";
			print_r($query_cheq_tran_transito);
			echo "</pre>";
			*/
			
			/***************************************************************************************
			 ***************** Fin de Querys de cheques/transferencias en tránsito *****************
			 **************************************************************************************/
			
			
			
			
			/***************************************************************************
			 ***************** Querys de depósitos/débitos en tránsito *****************
			***************************************************************************/
			
			//Codis en tránsito
			$query_codi_transito = "
				SELECT
					cdi.nro_referencia AS referencia,
					UPPER(cdi.comp_comen) AS comentario,
					cdi.comp_fec AS fecha,
					COALESCE(ctb.monto_debe,0)+COALESCE(ctb.monto_haber,0) AS monto,
					cdi.comp_id AS id_documento
				FROM
					sai_comp_diario cdi
					INNER JOIN sai_ctabanco_saldo ctb ON (cdi.comp_id = ctb.docg_id)
				WHERE ctb.ctab_numero = '".$params['cuentaBancaria']."'
					AND cdi.comp_id LIKE 'codi%'
					AND cdi.comp_id NOT LIKE 'codi%08'
					AND cdi.esta_id != 15
					AND cdi.comp_fec <= TO_TIMESTAMP('".$fecha." 23:59:59','YYYY-MM-DD HH24:MI:SS')
					AND cdi.comp_fec NOT LIKE '2008%'
					AND ctb.fecha_saldo > TO_TIMESTAMP('".$fecha." 23:59:59','YYYY-MM-DD HH24:MI:SS')
					
				--UNION (No funciona pues los codi 2009 no se conciliaban y no quedó registro en ctabanco_saldo)
					
					--SELECT
						--cdi.comp_id,
						--cdi.nro_referencia AS referencia,
						--UPPER(cdi.comp_comen) AS comentario,
						--cdi.comp_fec AS fecha,
						--0 AS monto
					--FROM
						--sai_comp_diario cdi
					--WHERE
						--cdi.comp_id LIKE 'codi%' AND
						--cdi.comp_id NOT LIKE 'codi%08' AND
						--cdi.esta_id != 15 AND
						--cdi.comp_id NOT IN (
							--SELECT 
								--docg_id
							--FROM
								--sai_ctabanco_saldo
							--WHERE
								--docg_id like 'codi%' AND
								--ctab_numero  = '".$params['cuentaBancaria']."'
						--)
			";
			//Fin transito codi
			/*
			echo "<pre>";
			print_r($query_codi_transito);
			echo "</pre>";
			*/
			
			/**********************************************************************************
			 ***************** Fin de Querys de depósitos/débitos en tránsito *****************
			**********************************************************************************/
			
			
			
			
			/**************************************************************************
			 ***************** Querys de cheques en tránsito anulados *****************
			**************************************************************************/
				
			//Cheques en tránsito anulados
			
			
			$query_cheq_anulado = "
				SELECT
					ch.nro_cheque AS referencia,
					--COALESCE(TO_CHAR(fechaemision_cheque, 'DD/MM/YYYY'),'') AS fecha,
					COALESCE(TO_CHAR(ce.fecha_accion_cheque, 'DD/MM/YYYY'),'') AS fecha,
					--(SELECT COALESCE(TO_CHAR(MIN(fechaemision_cheque),'DD/MM/YYYY'),'') FROM sai_cheque WHERE docg_id=p.docg_id) AS fecha,						
					beneficiario_cheque AS beneficiario,
					ci_rif_beneficiario_cheque AS id_beneficiario,
					monto_cheque AS monto,
					p.docg_id AS id_documento
				FROM
					sai_cheque ch
					INNER JOIN sai_chequera cq ON (cq.nro_chequera = ch.nro_chequera)
					INNER JOIN sai_pago_cheque p ON (p.docg_id = ch.docg_id)
					INNER JOIN sai_cheque_estados ce ON (ce.id_cheque = ch.id_cheque)
				WHERE
					cq.ctab_numero = '".$params['cuentaBancaria']."'
					AND ch.fechaemision_cheque <= TO_TIMESTAMP('".$fecha." 23:59:59','YYYY-MM-DD HH24:MI:SS')
					AND ch.fechaemision_cheque NOT LIKE '2008%'
					AND ce.comentario LIKE 'A%-%'
					AND ce.estatus_cheque = 15
					AND ce.fecha_accion_cheque LIKE '".substr($params['fecha'],6,4)."-".substr($params['fecha'],3,2)."-%'
			";
			
			/*Fin Saldo Diario saldos correctos*/
				
			/*
			echo "<pre>";
			print_r($query_cheq_anulado);
			echo "</pre>";
			*/
				
			/*********************************************************************************
			 ***************** Fin de Querys de cheques en tránsito anulados *****************
			*********************************************************************************/
			

		$reporte = array();
		/*Llenado cheques en tránsito*/
			if(($result = $GLOBALS['SafiClassDb']->Query($query_cheq_tran_transito)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			else {	
			$i = 0;
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$reporte['cheques_transito'][$i]['fecha'] = $row["fecha"];
				$reporte['cheques_transito'][$i]['referencia'] = $row["referencia"];
				$reporte['cheques_transito'][$i]['comentario'] = $row["comentario"];
				$reporte['cheques_transito'][$i]['beneficiario'] = $row["beneficiario"];
				$reporte['cheques_transito'][$i]['monto'] = $row["monto"];
				$reporte['cheques_transito'][$i]['fecha_mes2'] = $fecha_mes2;
				$reporte['cheques_transito'][$i]['id_documento'] = $row['id_documento'];
				$i++;
			}
			}

			/*Llenado codis en tránsito*/
			if(($result = $GLOBALS['SafiClassDb']->Query($query_codi_transito)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			else {
			$i = 0;
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$reporte['codi_transito'][$i]['fecha'] = $row["fecha"];
				$reporte['codi_transito'][$i]['referencia'] = $row["referencia"];
				$reporte['codi_transito'][$i]['comentario'] = $row["comentario"];
				$reporte['codi_transito'][$i]['beneficiario'] = ((strlen($row["beneficiario"]) > 2) ? $row["beneficiario"] : (strlen($row["beneficiariov"])>2) ? $row["beneficiariov"]: $row["beneficiariop"]);
				$reporte['codi_transito'][$i]['monto'] = $row["monto"];
				$reporte['codi_transito'][$i]['id_documento'] = $row["id_documento"];
				$i++;
			}	
			}	

			/*Llenado codis conciliado*/
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query_codi_conciliado)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$i = 0;
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$reporte['codi_conciliado'][$i]['fecha'] = $row["fecha"];
				$reporte['codi_conciliado'][$i]['referencia'] = $row["referencia"];
				$reporte['codi_conciliado'][$i]['comentario'] = $row["comentario"];
				$reporte['codi_conciliado'][$i]['beneficiario'] = ((strlen($row["beneficiario"]) > 2) ? $row["beneficiario"] : (strlen($row["beneficiariov"])>2) ? $row["beneficiariov"]: $row["beneficiariop"]);
				$reporte['codi_conciliado'][$i]['monto'] = $row["monto"];
				$reporte['codi_conciliado'][$i]['id_documento'] = $row["id_documento"];
				$i++;
			}	
					
			/*Llenado cheques tránsito conciliado*/
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query_cheq_tran_conciliado)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$i = 0;
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$reporte['cheques_conciliados'][$i]['fecha'] = $row["fecha"];
				$reporte['cheques_conciliados'][$i]['referencia'] = $row["referencia"];
				$reporte['cheques_conciliados'][$i]['comentario'] = $row["comentario"];
				$reporte['cheques_conciliados'][$i]['beneficiario'] =  $row["beneficiario"];
				$reporte['cheques_conciliados'][$i]['monto'] = $row["monto"];
				$reporte['cheques_conciliados'][$i]['fecha_emision'] = $row["fecha_emision"];
				$reporte['cheques_conciliados'][$i]['fecha_minima'] = $row["fecha_minima"];
				$reporte['cheques_conciliados'][$i]['fecha_anulacion'] = $row["fecha_anulacion"];
				$reporte['cheques_conciliados'][$i]['monto_contable'] = $row["monto_contable"];
				$reporte['cheques_conciliados'][$i]['fecha_mes2'] = $fecha_mes2;
				$reporte['cheques_conciliados'][$i]['id_documento'] = $row['id_documento'];
				$i++;
			}
			
			/*Llenado cheques anulados*/
			if(($result = $GLOBALS['SafiClassDb']->Query($query_cheq_anulado)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			else {
			$i = 0;
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$reporte['cheques_anulados'][$i]['fecha'] = $row["fecha"];
				$reporte['cheques_anulados'][$i]['referencia'] = $row["referencia"];
				$reporte['cheques_anulados'][$i]['comentario'] = $row["comentario"];
				$reporte['cheques_anulados'][$i]['beneficiario'] = $row["beneficiario"];
				$reporte['cheques_anulados'][$i]['monto'] = $row["monto"];
				$reporte['cheques_anulados'][$i]['id_documento'] = $row["id_documento"];
				$i++;
			}
			}
			$_SESSION['saldo_banco'] = $saldo_banco;
			return $reporte;
		}
		catch(Exception $e){
			error_log($e);
			return null;
		}
	}
	
}	