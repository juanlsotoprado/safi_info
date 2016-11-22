<?php

include_once (SAFI_ENTIDADES_PATH. '/rendicionAvancePartida.php');

class SafiModeloRendicionAvancePartida
{
	public static function GuardarRendicionAvancePartida(EntidadRendicionAvancePartida $rendicionAvancePartida)
	{
		try
		{
			$preMsg = "Error al intentar guardar la información de la rendiciónAvance-partida.";
			
			if($rendicionAvancePartida == null)
				throw new Exception($preMsg." El parámetro rendicionAvancePartida es nulo");
				
			if($rendicionAvancePartida->GetIdResponsableAvance() == null)
				throw new Exception($preMsg." El parámetro rendicionAvancePartida idResponsableAvance es nulo.");
				
			if(($idResponsable=trim($rendicionAvancePartida->GetIdResponsableAvance())) == '')
				throw new Exception($preMsg." El parámetro rendicionAvancePartida idResponsableAvance está vacío.");
				
			if($rendicionAvancePartida->GetIdRendicionAvance() == null)
				throw new Exception($preMsg." El parámetro rendicionAvancePartida idRendicionAvance es nulo.");
				
			if(($idRendicionAvance=trim($rendicionAvancePartida->GetIdRendicionAvance())) == null)
				throw new Exception($preMsg." El parámetro rendicionAvancePartida idRendicionAvance está vacío.");
				
			if($rendicionAvancePartida->GetPartida() == null)
				throw new Exception($preMsg." El parámetro rendicionAvancePartida partida es nulo");
				
			if($rendicionAvancePartida->GetPartida()->GetId() == null)
				throw new Exception($preMsg." El parámetro rendicionAvancePartida partida id es nulo");
				
			if(($idPartida=trim($rendicionAvancePartida->GetPartida()->GetId())) == '')
				throw new Exception($preMsg." El parámetro rendicionAvancePartida partida id está vacío");
				
			if($rendicionAvancePartida->GetMonto() == null)
				throw new Exception($preMsg." El parámetro rendicionAvancePartida monto es nulo");
				
			if(($monto=trim($rendicionAvancePartida->GetMonto())) == '')
				throw new Exception($preMsg." El parámetro rendicionAvancepartida monto está vacío");
				
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$query = "
				INSERT INTO safi_responsable_rendicion_avance_partida
					(
						responsable_avance_id,
						rendicion_avance_id,
						partida_id,
						monto
					)
				VALUES
					(
						'".$idResponsable."',
						'".$idRendicionAvance."',
						'".$idPartida."',
						'"./*$monto*/str_replace(",", ".", trim($monto))."'
					)
			";
			
			$result = $GLOBALS['SafiClassDb']->Query($query);
			
			if($result === false) throw new Exception($preMsg." Detalles: ".utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception($preMsg." No se pudo al realizar el commit en la funcion de guardado de la rendicionAvance-partida."
					." Detalles: ".utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			return true;
		}
		catch (Exception $e)
		{
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
		return false;
	}
	
	public static function GetRendicionAvancePartidas($params = null)
	{
		try
		{
			$preMsg = "Error al obtener las partidas de un responsable de una rendicion de avance.";
			$arrMsg = array();
			$existeCriterio = false;
			$queryWhere = "";
			$rendicionAvancePartidas = null;
			
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
				
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
				
			if(count($params) == 0)
				throw new Exception($preMsg." El parámetro params está vacío.");
				
			$arrMsg = array();
			
			if(!isset($params["idRendicion"]))
				$arrMsg[] = "El parámetro params['idRendicion'] no pudo ser encontrado.";
			else if(($idRendicion=$params["idRendicion"]) == null)
				$arrMsg[] = "El parámetro params['idRendicion'] es nulo.";
			else if(($idRendicion=trim($idRendicion)) == '')
				$arrMsg[] = "El parámetro params['idRendicion'] está vacío.";
			else{
				$existeCriterio = true;
				$queryWhere = "rendicion_avance_id = '" . $idRendicion . "'";
			}
				
			if(!isset($params["idsResponsables"]))
				$arrMsg[] = "El parámetro params['idsResponsables'] no pudo ser encontrado.";
			else if(($idsResponsables=$params["idsResponsables"]) == null)
				$arrMsg[] = "El parámetro params['idsResponsables'] es nulo.";
			else if(!is_array($idsResponsables))
				$arrMsg[] = "El parámetro params['idsResponsables'] no es un arreglo.";
			else if(count($idsResponsables) == 0)
				$arrMsg[] = "El parámetro params['idsResponsables'] está vacío.";
			else{
				$existeCriterio = true;
				if($queryWhere != ""){
					$queryWhere = "
						AND ";
				}
				$queryWhere .= "responsable_avance_id IN ('" . implode("', '", $idsResponsables) . "')";
			}
			
			if(!$existeCriterio){
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}
			echo $query;
			$query = "
				SELECT
					responsable_avance_id,
					rendicion_avance_id,
					partida_id,
					monto
				FROM
					safi_responsable_rendicion_avance_partida
				WHERE
					" . $queryWhere . "
				ORDER BY
					responsable_avance_id
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ".($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$rendicionAvancePartidas = array();
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				if(!isset($rendicionAvancePartidas[$row['responsable_avance_id']]))
				{
					$rendicionAvancePartidas[$row['responsable_avance_id']] = array();
				}
				
				$rendicionAvancePartidas[$row['responsable_avance_id']][] = self::LlenarRendicionAvancePartida($row);
			}
			
			return $rendicionAvancePartidas;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	public static function BorrarRendicionAvancePartidaByIdRendicion($idRendicionAvance = null)
	{
		try
		{
			$preMsg = 'Error al intentar borrar de rendicionAvance-partida dado el id de la rendición.';
			
			if($idRendicionAvance == null)
				throw new Exception($preMsg.' El parámetro idRendicionAvance es nulo.');
				
			if(($idRendicionAvance=trim($idRendicionAvance)) == '')
				throw new Exception($preMsg.' El parámetro idRendicionAvance está vacío.');
				
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception($preMsg.'No se pudo iniciar la transacción. Detalles: '
					. utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$query = "
				DELETE FROM
					safi_responsable_rendicion_avance_partida
				WHERE
					rendicion_avance_id = '".$idRendicionAvance."'
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception($preMsg." No se pudo realizar el commit. Detalles: "
					.utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
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
	
	public static function LlenarRendicionAvancePartida($row)
	{
		$partida = new EntidadPartida();
		$partida->SetId($row['partida_id']);

		$rendicionAvancePartida = new EntidadRendicionAvancePartida();
		$rendicionAvancePartida->SetIdResponsableAvance($row['responsable_avance_id']);
		$rendicionAvancePartida->SetIdRendicionAvance($row['rendicion_avance_id']);
		$rendicionAvancePartida->SetPartida($partida);
		$rendicionAvancePartida->SetMonto($row['monto']);
		
		return $rendicionAvancePartida;
	}
}