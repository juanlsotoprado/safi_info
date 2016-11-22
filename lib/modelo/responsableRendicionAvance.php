<?php
include_once(SAFI_ENTIDADES_PATH . '/responsableRendicionAvance.php');

class SafiModeloResponsableRendicionAvance
{
	public static function GuardarResponsableRendicionAvance(EntidadResponsableRendicionAvance $responsableRendicionAvance)
	{
		try
		{
			// Validar los parámetros de entrada
			if($responsableRendicionAvance == null)
				throw new Exception("El parámetro responsableRendicionAvance es nulo.");
				
			if($responsableRendicionAvance->GetIdResponsableAvance() == null)
				throw new Exception("El parámetro responsableRendicionAvance id es nulo.");
				
			if(($idResponsable=trim($responsableRendicionAvance->GetIdResponsableAvance())) == '')
				throw new Exception("El parámetro responsableRendicionAvance id está vacío.");
				
			if($responsableRendicionAvance->GetIdRendicionAvance() == null)
				throw new Exception("El parámetro responsableRendicionAvance idRendicionAvance es nulo.");
				
			if(($idRendicionAvance=trim($responsableRendicionAvance->GetIdRendicionAvance())) == null)
				throw new Exception("El parámetro responsableRendicionAvance idRendicionAvance está vacío.");
			 
			// Iniciar la transacción
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			// Verificar que la transacción se inicio correctamente
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			// Query de la inserción en la tabla safi_responsable_rendicion_avance
			$query = "
				INSERT INTO safi_responsable_rendicion_avance
					(
						responsable_avance_id,
						rendicion_avance_id
					)
				VALUES
					(
						'".$idResponsable."',
						'".$idRendicionAvance."'
					)
			";
			
			$result = $GLOBALS['SafiClassDb']->Query($query);
			
			if($result === false) throw new Exception('Error al guardar la información del responsable de la rendición de avance. '.
				' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la función de guardado del responsable de la rendición '.
					'de avance. Detalles: " . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			return $idResponsable;
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
	
	public static function GetResponsablesRendicionAvance($params)
	{
		try
		{
			$preMsg = "Error al intentar obtener los responsablesRendicionAvance.";
			$arrMsg = null;
			$queryWhere = "";
			$existeCriterio = false;
			
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
				$arrMsg[] = " El parámetro params['idsResponsables'] es nulo.";
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
				
			$query = "
				SELECT
					responsable_avance_id,
					rendicion_avance_id
				FROM
					safi_responsable_rendicion_avance
				WHERE
					".$queryWhere."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ".utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$rows = array();
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$rows[$row['responsable_avance_id']] = $row;
			}
			
			if(count($rows) == 0)
				throw new Exception($preMsg. " No se encontró ningún responsable de rendición de avance que cumpla con los"
					." criterios de búsqueda. Verifique que existe al menos un responsable en la rendición.");
			
			return self::LlenarResponsables($rows);
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	public static function BorrarResponsableRendicionAvanceByIdRendicion($idRendicionAvance = null)
	{
		try
		{
			$preMsg = 'Error al intentar borrar de responsableRendicionAvance dado el id de la rendición.';
			
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
					safi_responsable_rendicion_avance
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
	
	private static function LlenarResponsables(array $rows)
	{
		try
		{
			$preMsg = "Error al intentar llenar los objetos de los responsables de rendición de avance.";
			$idsResponsables = array();
			$responsablesRendicionAvance = array();
			
			foreach ($rows as $row)
			{
				$idsResponsables[] = $row['responsable_avance_id'];
			}
			
			// Obtener los objetos de los responsables del avance, para completar la información de los
			// responsables de la rendición de avance
			if(count($idsResponsables) > 0)
				$responsablesAvance = SafiModeloResponsableAvance::GetResponsablesAvance(array('idsResponsables' => $idsResponsables));
				
			foreach ($rows as $row)
			{
				if(($responsableAvance=$responsablesAvance[$row['responsable_avance_id']]) == null)
					throw new Exception($preMsg." El responsable del avance \"".$row['responsable_avance_id']."\" no pudo ser cargado.");
				
				$responsablesRendicionAvance[$row['responsable_avance_id']] = self::LlenarResponsable($row, $responsableAvance);
			}
			
			return $responsablesRendicionAvance;
			
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	private static function LlenarResponsable(array $row, EntidadResponsableAvance $responsableAvance)
	{
		$responsableRendicionAvance = new EntidadResponsableRendicionAvance();
		
		$responsableRendicionAvance->SetIdResponsableAvance($row['responsable_avance_id']);
		$responsableRendicionAvance->SetIdRendicionAvance($row['rendicion_avance_id']);
		$responsableRendicionAvance->SetTipoResponsable($responsableAvance->GetTipoResponsable());
		$responsableRendicionAvance->SetEmpleado($responsableAvance->GetEmpleado());
		$responsableRendicionAvance->SetBeneficiario($responsableAvance->GetBeneficiario());
		$responsableRendicionAvance->SetEstado($responsableAvance->GetEstado());
		
		return $responsableRendicionAvance;
	}
}