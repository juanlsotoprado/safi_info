<?php
include_once(SAFI_ENTIDADES_PATH . '/cheque.php');

class SafiModeloCheque
{
	public static function GetChequeBy(array $params = null)
	{
		$cheque = null;
		
		try
		{
			$preMsg = "Error al intentar obtener la cuentaBanco.";
			$where = "";
			$existeCriterio = false;
			$arrMsg = array();
			$cheque = null;
			
			if($params === null)
				throw new Exception($preMsg. " El parámatro \"params\" es nulo.");
			if(!is_array($params))
				throw new Exception($preMsg. " El parámatro \"params\" no es un arreglo.");
				
			if(!isset($params['id']))
				$arrMsg[] = $preMsg."El parámetro \"params['id']\" no fue encontrado.";
			if(($id=$params['id']) === null)
				$arrMsg[] = $preMsg."El parámetro \"params['id']\" es nulo.";
			else if(($id=trim($id)) == "")
				$arrMsg[] = $preMsg."El parámetro \"params['id']\" está vacío.";
			else {
				$existeCriterio = true;
				$where = "
					cheque.id_cheque = '".$id."'
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
					cheque.docg_id = '".$idDocumento."'
				";
			}
			
			if(!isset($params['idEstado']))
				$arrMsg[] = $preMsg."El parámetro \"params['idEstado']\" no fue encontrado.";
			if(($idEstado=$params['idEstado']) === null)
				$arrMsg[] = $preMsg."El parámetro \"params['idEstado']\" es nulo.";
			else if(($idEstado=trim($idEstado)) == "")
				$arrMsg[] = $preMsg."El parámetro \"params['idEstado']\" está vacío.";
			else {
				$existeCriterio = true;
				if ($where != "") $where .= " AND";
				$where .= "
					cheque.estatus_cheque = '".$idEstado."'
				";
			}
			
			if(!$existeCriterio)
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			
			$query = "
				SELECT
					cheque.id_cheque AS cheque_id,
					cheque.estatus_cheque AS cheque_estado_id,
					cheque.nro_cheque AS cheque_numero,
					cheque.monto_cheque AS cheque_monto,
					cheque.nro_chequera AS cheque_id_chequera,
					to_char(cheque.fechaemision_cheque, 'DD/MM/YYYY HH24:MI:SS') AS cheque_fecha_emision,
					cheque.docg_id AS cheque_id_documento
				FROM
					sai_cheque cheque
				WHERE
					".$where."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al obtener el cheque. Detalles: ". 
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()).$query);
					
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$cheque = self::LlenarCheque($row);
			}
			
			return $cheque;
			
		} catch (Exception $e) {
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetChequesPorBeneficiario($beneficiario = null)
	{
		$cheques = array();
		$cheque = null;
	
		try
		{
				
			$query = "
				SELECT
					cheque.id_cheque AS cheque_id,
					cheque.estatus_cheque AS cheque_estado_id,
					cheque.nro_cheque AS cheque_numero,
					cheque.monto_cheque AS cheque_monto,
					cheque.nro_chequera AS cheque_id_chequera,
					to_char(cheque.fechaemision_cheque, 'DD/MM/YYYY HH24:MI:SS') AS cheque_fecha_emision,
					cheque.docg_id AS cheque_id_documento
				FROM
					sai_cheque cheque
				WHERE
					cheque.estatus_cheque != 15 AND
					ci_rif_beneficiario_cheque = '".$beneficiario."'
				ORDER BY cheque.fechaemision_cheque DESC";
			
				
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al obtener el cheque. Detalles: ".
						utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()).$query);
				
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$cheque = self::LlenarCheque($row);
				$cheques[] = $cheque;
			}
				
			return $cheques;
				
		} catch (Exception $e) {
			error_log($e, 0);
			return false;
		}
	}
	
	public static function ActualizarBeneficiario($cheques, $beneficiario)
	{
		$contador = 0;
		try
		{
			$contador = count($cheques);
		
			if ($contador > 0) {
				for ($i = 0; $i<count($cheques); $i++) {
					$codigo = $cheques[$i];
					$error=0;
			
					//Actualizar los datos para mostrarlos
					$query = "UPDATE sai_cheque
							SET beneficiario_cheque = '".$beneficiario."'
							WHERE id_cheque=".$codigo;

					if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
						throw new Exception("Error al obtener el cheque. Detalles: ".
								utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()).$query);
						
				}
		
			}
		return $contador;
		} catch (Exception $e) {
			error_log($e, 0);
			return false;
		}
	}
	
	
	private static function LlenarCheque($row)
	{
		$cheque = new EntidadCheque();
		
		$cheque->SetId($row['cheque_id']);
		$cheque->SetIdEstado($row['cheque_estado_id']);
		$cheque->SetNumero($row['cheque_numero']);
		$cheque->SetMonto($row['cheque_monto']);
		$cheque->SetIdChequera($row['cheque_id_chequera']);
		$cheque->SetFechaEmision($row['cheque_fecha_emision']);
		$cheque->SetIdDocumento($row['cheque_id_documento']);
		
		return $cheque;
	}
}