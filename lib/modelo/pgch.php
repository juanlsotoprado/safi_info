<?php
include_once(SAFI_ENTIDADES_PATH . '/pagoCheque.php');
include_once(SAFI_ENTIDADES_PATH . '/cheque.php');

class SafiModeloPgch
{
	public static function GetPgchSopg($sopgId)
	{
	$fecha = date("d/m/yy");
	/*$query = "
				SELECT
					p.pgch_id AS pgch_id,
					p.docg_id AS sopg_id,
					s.sopg_detalle AS detalle,
					p.nro_cuenta AS nro_cuenta,
					ch.nro_cheque AS nro_cheque,
					ch.beneficiario_cheque AS beneficiario,
					TO_CHAR(p.pgch_fecha, 'DD/MM/YYYY') AS fecha
				FROM
					sai_pago_cheque p
				INNER JOIN sai_sol_pago s ON (s.sopg_id = p.docg_id)
				INNER JOIN sai_causado c ON (s.sopg_id = c.caus_docu_id)
				INNER JOIN sai_cheque ch ON (s.sopg_id = ch.docg_id)
				WHERE
					s.esta_id = 15
					AND p.esta_id = 15
					AND TO_CHAR(c.fecha_anulacion, 'DD/MM/YYYY') = '".date("d/m/Y")."'
					AND s.sopg_id = '".$sopgId."'";*/	
		$query = "
				SELECT
					p.pgch_id AS pgch_id,
					p.docg_id AS sopg_id,
					s.sopg_detalle AS detalle,
					p.nro_cuenta AS nro_cuenta,
					TO_CHAR(p.pgch_fecha, 'DD/MM/YYYY') AS fecha,
					p.pgch_obs AS observaciones		
				FROM
					sai_pago_cheque p
				INNER JOIN sai_sol_pago s ON (s.sopg_id = p.docg_id)
				INNER JOIN sai_causado c ON (s.sopg_id = c.caus_docu_id)
				WHERE 
					s.esta_id = 15 
					AND p.esta_id = 15
				 	AND TO_CHAR(c.fecha_anulacion, 'DD/MM/YYYY') = '".date("d/m/Y")."'
					AND TO_CHAR(c.fecha_anulacion, 'MM/YYYY') = TO_CHAR(c.caus_fecha, 'MM/YYYY')
					AND s.sopg_id = '".$sopgId."'";

		$pgch = null;
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				//$cheq = new EntidadCheque();
				$pgch = new EntidadPagoCheque();	
				$pgch->SetId($row['pgch_id']);
				$pgch->SetIdDocumento($row['sopg_id']);
				$pgch->SetAsuntoPgch($row['detalle']);
				$pgch->SetFechaPgch($row['fecha']);
				$pgch->SetNumeroCuenta($row['nro_cuenta']);
				$pgch->SetObservaciones($row['observaciones']);
				/*$cheq->SetNumero($row['nro_cheque']);
				$cheq->SetBeneficiarioCheque($row['beneficiario']);
				$pgch->SetCheque($cheq);*/
			}
		}
		
		return $pgch;
	}
	public static function ReiniciarPago($params)
	{
		try {
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				$query = " UPDATE sai_sol_pago
							SET esta_id = 13
							WHERE sopg_id='".$params['id_sopg']."'";
			
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				$query = " UPDATE sai_doc_genera
							SET esta_id = 39
							WHERE docg_id='".$params['id_sopg']."'";
			

				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				/*Anular la anulación de la contabilidad del causado*/				
				$query = " UPDATE sai_comp_diario
							SET esta_id = 15 
							WHERE comp_doc_id='".$params['id_sopg']."'
								AND comp_comen LIKE 'A-%'";

				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				/*Anular la contabilidad del pagado*/
				$query = " UPDATE sai_comp_diario
							SET esta_id = 15
							WHERE comp_doc_id='".$params['id_pgch']."'
								AND comp_comen LIKE 'P-%'";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				/*Anular la anulacion de la contabilidad del pagado*/
				$query = " UPDATE sai_comp_diario
							SET esta_id = 15
							WHERE comp_doc_id='".$params['id_pgch']."'
								AND comp_comen LIKE 'A_%'";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				/*Eliminar fecha de anulacion*/
				$query = " UPDATE sai_causado
							SET esta_id = 1,
								fecha_anulacion = null 
							WHERE caus_docu_id='".$params['id_sopg']."'";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				
				/*Anular mov_cta_banco*/
				$query = " UPDATE sai_mov_cta_banco
							SET conciliado = 2
							WHERE docg_id='".$params['id_sopg']."'";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				
				/*Colocar el pago con cheque inactivo*/
				$query = " UPDATE sai_pago_cheque
							SET esta_id = 2
							WHERE docg_id = '".$params['id_sopg']."'";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				$query = " UPDATE sai_pagado
							SET esta_id = 2
							WHERE paga_docu_id='".$params['id_pgch']."'";

				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				
				
				$query = " DELETE FROM sai_memo_desti
							WHERE memo_id IN (
									SELECT memo_id
									FROM sai_memo
									WHERE UPPER(memo_asunto) LIKE '%ANULA%'
										AND memo_id IN (SELECT doso_doc_soport
														FROM sai_docu_sopor
														WHERE doso_doc_fuente = '".$params['id_pgch']."'
														)
							)";
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));				
			

				$query = " DELETE FROM sai_memo
							WHERE UPPER(memo_asunto) LIKE '%ANULA%'
								AND memo_id IN (SELECT doso_doc_soport
												FROM sai_docu_sopor
												WHERE doso_doc_fuente = '".$params['id_pgch']."'
												)";

				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

				$query = " DELETE FROM sai_docu_sopor
							WHERE doso_doc_fuente = '".$params['id_pgch']."'";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la función de reiniciar pago".
						utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			return null;			
		}
		catch(Exception $e){
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;	
		}
	}
}