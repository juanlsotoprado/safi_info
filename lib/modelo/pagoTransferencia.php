<?php
require_once(SAFI_ENTIDADES_PATH."/pagoTransferencia.php");
include_once(SAFI_ENTIDADES_PATH . '/cuentaBanco.php');
include_once(SAFI_ENTIDADES_PATH . '/banco.php');


class SafiModeloPagoTransferencia
{
	public static function GetPagoTransferencia(array $params = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener el pago transferencia.";
			$where = "";
			$existeCriterio = false;
			$arrMsg = array();
			$pagoTransferencia = null;
			
			
			if($params === null)
				throw new Exception($preMsg. " El parámatro \"params\" es nulo.");
			if(!is_array($params))
				throw new Exception($preMsg. " El parámatro \"params\" no es un arreglo.");
				
			if(!isset($params['idTransferencia']))
				$arrMsg[] = $preMsg."El parámetro \"params['idTransferencia']\" no fue encontrado.";
			if(($idTransferencia=$params['idTransferencia']) === null)
				$arrMsg[] = $preMsg."El parámetro \"params['idTransferencia']\" es nulo.";
			else if(($idTransferencia=trim($idTransferencia)) == "")
				$arrMsg[] = $preMsg."El parámetro \"params['idTransferencia']\" está vacío.";
			else {
				$existeCriterio = true;
				$where = "
					pago_transferencia.trans_id = '".$idTransferencia."'
				";
			}
			
			if(!isset($params['idDocumento']))
				$arrMsg[] = $preMsg."El parámetro \"params['idDocumento']\" no fue encontrado.";
			if(($idDocumento=$params['idDocumento']) === null)
				$arrMsg[] = $preMsg."El parámetro \"params['idDocumento']\" es nulo.";
			else if(($idDocumento=trim($idDocumento)) == "")
				$arrMsg[] = $preMsg."El parámetro \"params['idDocumento']\" está vacío.";
			else {
				$existeCriterio = true;
				if ($where != "") $where .= " AND";
				$where .= "
					pago_transferencia.docg_id = '".$idDocumento."'
				";
			}
			
			if(!isset($params['idEstado']))
				$arrMsg[] = $preMsg."El parámetro \"params['idEstado']\" no fue encontrado.";
			if(($idEstado=$params['idEstado']) === null)
				$arrMsg[] = $preMsg."El parámetro \"params['idEstado']\" es nulo.";
			else if(($idEstado=trim($idEstado)) == "")
				$arrMsg[] = $preMsg."El parámetro \"params['idEstado'] idEstado\" está vacío.";
			else {
				$existeCriterio = true;
				if ($where != "") $where .= " AND";
				$where .= "
					pago_transferencia.esta_id = '".$idEstado."'
				";
			}
			
			if(!isset($params['noEnIdsEstados']))
				$arrMsg[] = $preMsg."El parámetro \"params['noEnIdsEstados']\" no fue encontrado.";
			if(($noEnIdsEstados=$params['noEnIdsEstados']) === null)
				$arrMsg[] = $preMsg."El parámetro \"params['noEnIdsEstados']\" es nulo.";
			if(!is_array($noEnIdsEstados))
				$arrMsg[] = $preMsg."El parámetro \"params['noEnIdsEstados']\" no es un arreglo.";
			else{
				$existeCriterio = true;
				if ($where != "") $where .= " AND";
				$where .= "
					pago_transferencia.esta_id = NOT IN ('".implode("', '", $noEnIdsEstados)."')
				";
			}
			
			if(!$existeCriterio)
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			
			$query = "
				SELECT
					".self::GetSelectFieldsPagoTransferencia()."
				FROM
					sai_pago_transferencia pago_transferencia
				WHERE
					".$where."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$pagoTransferencia = self::LlenarPagoTransferencia($row);
			}
			
			return $pagoTransferencia;
				
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetSelectFieldsPagoTransferencia()
	{
		return "
			pago_transferencia.trans_id AS pago_transferencia_transferencia_id,
			pago_transferencia.depe_id AS pago_transferencia_dependencia_id,
			to_char(pago_transferencia.trans_fecha, 'DD/MM/YYYY HH24:MI:SS') AS pago_transferencia_fecha_transferencia,
			pago_transferencia.nro_cuenta_emisor AS pago_transferencia_numero_cuenta_emisor,
			pago_transferencia.esta_id AS pago_transferencia_estado_id,
			pago_transferencia.nro_referencia AS pago_transferencia_numero_referencia,
			pago_transferencia.docg_id AS pago_transferencia_documento_id,
			pago_transferencia.trans_asunto AS pago_transferencia_asunto_transferencia,
			pago_transferencia.pres_anno_docg AS pago_transferencia_a_o_presupuestario_documento,
			pago_transferencia.rif_ci AS pago_transferencia_rif_cedula,
			pago_transferencia.trans_obs AS pago_transferencia_observaciones_trasnferencias,
			pago_transferencia.trans_monto AS pago_transferencia_monto_transferencia,
			pago_transferencia.nro_cuenta_receptor AS pago_transferencia_numero_cuenta_receptor,
			pago_transferencia.beneficiario AS pago_transferencia_beneficiario
		";
	}
	
	public static function LlenarPagoTransferencia($row)
	{
		$pagoTransferencia = new EntidadPagoTransferencia();
		$cuenta = new EntidadCuentaBanco();
		$cuenta->SetId($row['pago_transferencia_numero_cuenta_emisor']);
		$pagoTransferencia->SetIdTransferencia($row['pago_transferencia_transferencia_id']);
		$pagoTransferencia->SetIdDependencia($row['pago_transferencia_dependencia_id']);
		$pagoTransferencia->SetFechaTransferencia($row['pago_transferencia_fecha_transferencia']);
		$pagoTransferencia->SetCuentaEmisor($cuenta);
		$pagoTransferencia->SetIdEstado($row['pago_transferencia_estado_id']);
		$pagoTransferencia->SetNumeroReferencia($row['pago_transferencia_numero_referencia']);
		$pagoTransferencia->SetIdDocumento($row['pago_transferencia_documento_id']);
		$pagoTransferencia->SetAsuntoTransferencia($row['pago_transferencia_asunto_transferencia']);
		$pagoTransferencia->SetA_oPresupuestarioDocumento($row['pago_transferencia_a_o_presupuestario_documento']);
		$pagoTransferencia->SetRifCedula($row['pago_transferencia_rif_cedula']);
		$pagoTransferencia->SetObservacionesTransferencia($row['pago_transferencia_observaciones_trasnferencias']);
		$pagoTransferencia->SetMontoTransferencia($row['pago_transferencia_monto_transferencia']);
		$pagoTransferencia->SetNumeroCuentaReceptor($row['pago_transferencia_numero_cuenta_receptor']);
		$pagoTransferencia->SetBeneficiario($row['pago_transferencia_beneficiario']);
		
		return $pagoTransferencia;
	}

	public static function GetTransferencia($idTransferencia)
	{
	
		$query = "
				SELECT
					t.trans_id AS trans_id,
					t.docg_id AS sopg_id,
					t.nro_referencia AS nro_referencia,
					t.nro_cuenta_emisor AS nro_cuenta,
					t.beneficiario AS beneficiario,
					t.rif_ci AS rif_ci,
					t.trans_monto AS monto,
					t.trans_asunto AS asunto,
					TO_CHAR(t.trans_fecha, 'DD/MM/YYYY') AS fecha,
					b.banc_nombre AS nombre_banco
				FROM
					sai_pago_transferencia t
				INNER JOIN sai_ctabanco ctb ON (ctb.ctab_numero = t.nro_cuenta_emisor)
				INNER JOIN sai_banco b	ON (ctb.banc_id = b.banc_id)
				WHERE
					t.esta_id != 15
					AND t.esta_id != 2
					AND t.trans_id NOT IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE '%tran%')
					AND t.trans_id='".$idTransferencia."'";
	
	
	
	
		$trans = null;
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$trans = new EntidadPagoTransferencia();
				$cuenta = new EntidadCuentaBanco();
				$banco = new EntidadBanco();
				$banco->SetNombre($row['nombre_banco']);
				$cuenta->SetId($row['nro_cuenta']);
				$cuenta->SetBanco($banco);
				$trans->SetIdTransferencia($row['trans_id']);
				$trans->SetIdDocumento($row['sopg_id']);
				$trans->SetCuentaEmisor($cuenta);
				$trans->SetBeneficiario($row['beneficiario']);
				$trans->SetRifCedula($row['rif_ci']);
				$trans->SetMontoTransferencia($row['monto']);
				$trans->SetAsuntoTransferencia($row['asunto']);
				$trans->SetFechaTransferencia($row['fecha']);
				$trans->SetNumeroReferencia($row['nro_referencia']);
			}
		}
	
		return $trans;
	}
	public static function ModificarTransferencia($params)
	{
		try {
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
				
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
				
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
				
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$query = " SELECT docg_id
						FROM sai_pago_transferencia
						WHERE trans_id = '".$params['idTransferencia']."'";
				
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result))
				$sopg_id = $row["docg_id"];			
	
			if ($params['nuevaFecha']!=null && $params['nuevaFecha']!='') {
				$query = " UPDATE sai_doc_genera
							SET docg_fecha = TO_DATE('".$params['nuevaFecha']."','DD-MM-YYYY')
							WHERE docg_id='".$params['idTransferencia']."'";
					
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
				$query = " UPDATE sai_pago_transferencia
							SET trans_fecha = TO_DATE('".$params['nuevaFecha']."','DD-MM-YYYY')
							WHERE trans_id='".$params['idTransferencia']."'";
	
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
				$query = " UPDATE sai_comp_diario
							SET comp_fec = TO_DATE('".$params['nuevaFecha']."','DD-MM-YYYY'),
								comp_fec_emis = TO_DATE('".$params['nuevaFecha']."','DD-MM-YYYY')
							WHERE comp_doc_id='".$params['idTransferencia']."'";
	
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
				$query = " UPDATE sai_pagado
							SET paga_fecha = TO_DATE('".$params['nuevaFecha']."','DD-MM-YYYY')
							WHERE paga_docu_id='".$params['idTransferencia']."'";
	
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
				$query = " UPDATE sai_ctabanco_saldo
							SET fecha_saldo = TO_DATE('".$params['nuevaFecha']."','DD-MM-YYYY')
							WHERE docg_id='".$params['idTransferencia']."'";
	
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				$query = " UPDATE sai_mov_cta_banco
							SET fechaemision_cheque = TO_DATE('".$params['nuevaFecha']."','DD-MM-YYYY')
							WHERE docg_id='".$sopg_id."'";
					
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));				
			}
	
			if ($params['nuevaReferencia'] != null && $params['nuevaReferencia'] != '') {
				$query = " UPDATE sai_pago_transferencia
							SET nro_referencia = '".$params['nuevaReferencia']."'
							WHERE trans_id='".$params['idTransferencia']."'";
	
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
				$query = " UPDATE sai_comp_diario
							SET nro_referencia = '".$params['nuevaReferencia']."'
							WHERE comp_doc_id='".$params['idTransferencia']."'";
	
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				$query = " UPDATE sai_mov_cta_banco
							SET nro_cheque = '".$params['nuevaReferencia']."'
							WHERE docg_id='".$sopg_id."'";
					
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			}
	
			if ($params['nuevaCuentaBancaria'] != null && $params['nuevaCuentaBancaria'] != -1) {
				$query = " SELECT cpat_id
							FROM sai_ctabanco
							WHERE ctab_numero = '".$params['nuevaCuentaBancaria']."'";
					
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result))
					$cpat_id = $row["cpat_id"];
					
				$query = " UPDATE sai_mov_cta_banco
							SET ctab_numero = '".$params['nuevaCuentaBancaria']."'
							WHERE docg_id='".$sopg_id."'";
					
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
				$query = " UPDATE sai_pago_transferencia
							SET nro_cuenta_emisor = '".$params['nuevaCuentaBancaria']."'
							WHERE trans_id='".$params['idTransferencia']."'";
					
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
	
				$query = " UPDATE sai_ctabanco_saldo
							SET ctab_numero = '".$params['nuevaCuentaBancaria']."'
							WHERE docg_id='".$params['idTransferencia']."'";
					
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
				$query = " SELECT comp_id
							FROM sai_comp_diario
							WHERE comp_doc_id='".$params['idTransferencia']."'
									AND esta_id != 15";
	
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
				$coda = "";
	
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$coda = $row["comp_id"];
				}
				if ($coda != "") {
					/*Se ubica el codigo de la cuenta bancaria registrada del codi*/
					$query = " SELECT rc.cpat_id AS cpat_id 
								FROM sai_reng_comp rc
								INNER JOIN sai_ctabanco cb ON (cb.cpat_id = rc.cpat_id)
								WHERE rc.comp_id = '".$coda."'";
					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
						throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					
					if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
						$cpat_id_pagado = $row["cpat_id"];
					}					
					
					/*Se ubica el nombre de la cuenta de banco registrada del codi*/
					$query = " SELECT cpat_nombre
							   FROM sai_cue_pat 
								WHERE cpat_id = '".$cpat_id."'";
					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
						throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
						
					if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
						$cpat_nombre_nuevo = $row["cpat_nombre"];
					}
						
					/*Se actualiza el codigo y el nombre de la cuenta bancaria*/
					
					$query = " UPDATE sai_reng_comp
								SET cpat_id = '".$cpat_id."', 
									cpat_nombre = '".$cpat_nombre_nuevo."'	
								WHERE comp_id = '".$coda."' 
										AND cpat_id = '".$cpat_id_pagado."'";
	
					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
						throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				}
	
			}
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
				
			if($result === false)
				throw new Exception("Error al realizar el commit en la función de actualización de la transferencia".
						utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			return $result;
		}
		catch(Exception $e){
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}	
}