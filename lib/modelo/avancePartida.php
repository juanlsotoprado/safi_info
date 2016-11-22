<?php

include_once(SAFI_ENTIDADES_PATH . '/avancePartida.php');
include_once(SAFI_ENTIDADES_PATH . '/partida.php');

class SafiModeloAvancePartida
{
	public static function GuardarAvancePartida(EntidadAvancePartida $avancePartida)
	{
		try
		{
			if($avancePartida == null)
				throw new Exception('El parámetro avancePartida es nulo');
		
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$query = "
				INSERT INTO safi_responsable_avance_partida
					(
						responsable_id,
						avance_id,
						partida_id,
						monto
					)
				VALUES
					(
						".(
							(
								$avancePartida->GetIdResponsableAvance() != null && trim($avancePartida->GetIdResponsableAvance()) != ''
							)
							? "'" . $avancePartida->GetIdResponsableAvance() . "'" : "NULL"
						
						).",
						".(
							(
								$avancePartida->GetIdAvance() != null && trim($avancePartida->GetIdAvance()) != ''
							)
							? "'" . $avancePartida->GetIdAvance() . "'" : "NULL"
						
						).",
						'".trim($avancePartida->GetPartida()->GetId())."',
						".(
							(
								$avancePartida->GetMonto() != null && trim($avancePartida->GetMonto()) != ''
							)
							? "'".trim($avancePartida->GetMonto())."'" : "'0.0'"
						)."
					)
			";
				
			$result = $GLOBALS['SafiClassDb']->Query($query);
			
			if($result === false) throw new Exception('Error al guardar la información del avance-partida. Detalles: ' .
				utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la funcion de guardado del avance-partida. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			return true;
		}
		catch (Exception $e)
		{
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}
	
	public static function EliminarAvancePartidasByIdAvance($idAvance)
	{
		try
		{
			if($idAvance == null || ($idAvance=trim($idAvance)) == '')
				throw new Exception("El parámetro idAvance es nulo o está vacío.");
			
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$query = "
				DELETE FROM
					safi_responsable_avance_partida
				WHERE
					avance_id = '" . $idAvance . "'
			";
			
			$result = $GLOBALS['SafiClassDb']->Query($query);
			
			if($result === false) throw new Exception('Error al guardar la información del avance-partida. Detalles: ' .
				utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la funcion de guardado del avance-partida. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			return true;
		}
		catch (Exception $e)
		{
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}
	
	public static function EliminarAvancePartidasByIdAvanceAndIdResponsable($idAvance, $idResponsable)
	{
		try
		{
			if($idAvance == null || ($idAvance=trim($idAvance)) == '')
				throw new Exception("El parámetro idAvance es nulo o está vacío.");
				
			if($idResponsable == null || ($idResponsable=trim($idResponsable)) == '')
				throw new Exception("El parámetro idResponsable es nulo o está vacío.");
			
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$query = "
				DELETE FROM
					safi_responsable_avance_partida
				WHERE
					avance_id = '" . $idAvance . "'
					AND responsable_id = '" . $idResponsable . "'
			";
			
			$result = $GLOBALS['SafiClassDb']->Query($query);
			
			if($result === false) throw new Exception('Error al guardar la información del avance-partida. Detalles: ' .
				utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la funcion de guardado del avance-partida. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			return true;
		}
		catch (Exception $e)
		{
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetAvancePartidas($params = null)
	{
		try
		{
			$avancePartidas = null;
			
			if($params == null)
				throw new Exception("El parámetro params es nulo.");
				
			if(!is_array($params))
				throw new Exception("El parámetro params no es un arreglo.");
				
			if(count($params) == 0)
				throw new Exception("El parámetro params está vacío.");
				
			if(isset($params["idAvance"]) && ($idAvance=$params["idAvance"]) != null && ($idAvance=trim($idAvance)) != '')
				$queryWhere = "avance_id = '" . $idAvance . "'";
				
			if(
				isset($params["idsResponsables"]) && ($idsResponsables=$params["idsResponsables"]) != null
				&& is_array($idsResponsables) && count($idsResponsables) > 0
			){
				if($queryWhere != ""){
					$queryWhere = "
						AND ";
				}
				$queryWhere .= "responsable_id IN ('" . implode("', '", $idsResponsables) . "')";
			}
			
			if($queryWhere == ""){
				throw new Exception("No se han encontrado criterios para buscar los responsables del avance.");
			}
			
			$query = "
				SELECT
					responsable_id,
					avance_id,
					partida_id,
					monto
				FROM
					safi_responsable_avance_partida
				WHERE
					" . $queryWhere . "
				ORDER BY
					responsable_id
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al obtener las partidas del avance: ".$idAvance.". Detalles: ".
					($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$avancePartidas = array();
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				if(!isset($avancePartidas[$row['responsable_id']]))
				{
					$avancePartidas[$row['responsable_id']] = array();
				}
				
				$avancePartidas[$row['responsable_id']][] = self::LlenarAvancePartida($row);
			}
			
			return $avancePartidas;
		}
		catch (Exception $e) {
			error_log($e, 0);
			return null;
		}
	}
	
	private static function LlenarAvancePartida($row)
	{
		$partida = new EntidadPartida();
		$partida->SetId($row['partida_id']);
		
		$avancePartida = new EntidadAvancePartida();
		$avancePartida->SetIdResponsableAvance($row['responsable_id']);
		$avancePartida->SetIdAvance($row['avance_id']);
		$avancePartida->SetPartida($partida);
		$avancePartida->SetMonto($row['monto']);
		
		return $avancePartida;
	}
}