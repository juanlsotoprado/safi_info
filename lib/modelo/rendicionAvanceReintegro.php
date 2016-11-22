<?php
include_once (SAFI_ENTIDADES_PATH. '/rendicionAvanceReintegro.php');
include_once (SAFI_MODELO_PATH . '/banco.php');

class SafiModeloRendicionAvanceReintegro
{
	public static function GuardarRendicionAvanceReintegro(EntidadRendicionAvanceReintegro $rendicionAvanceReintegro)
	{
		try
		{
			$preMsg = "Error al intentar guardar la información de la rendiciónAvance-reintegro.";
			
			if($rendicionAvanceReintegro == null)
				throw new Exception($preMsg." El parámetro rendicionAvanceReintegro es nulo");
				
			if($rendicionAvanceReintegro->GetIdResponsableAvance() == null)
				throw new Exception($preMsg." El parámetro rendicionAvanceReintegro idResponsableAvance es nulo.");
				
			if(($idResponsable=trim($rendicionAvanceReintegro->GetIdResponsableAvance())) == '')
				throw new Exception($preMsg." El parámetro rendicionAvanceReintegro idResponsableAvance está vacío.");
				
			if($rendicionAvanceReintegro->GetIdRendicionAvance() == null)
				throw new Exception($preMsg." El parámetro rendicionAvanceReintegro idRendicionAvance es nulo.");
				
			if(($idRendicionAvance=trim($rendicionAvanceReintegro->GetIdRendicionAvance())) == null)
				throw new Exception($preMsg." El parámetro rendicionAvanceReintegro idRendicionAvance está vacío.");
				
			if($rendicionAvanceReintegro->GetBanco() == null)
				throw new Exception($preMsg." El parámetro rendicionAvanceReintegro banco es nulo.");
				
			if(($idBanco=$rendicionAvanceReintegro->GetBanco()->GetId()) == null)
				throw new Exception($preMsg." El parámetro rendicionAvanceReintegro banco id es nulo.");
				
			if(($idBanco=trim($idBanco)) == '')
				throw new Exception($preMsg." El parámetro rendicionAvanceReintegro banco id está vacío.");
				
			if(($referencia=$rendicionAvanceReintegro->GetReferencia()) == null)
				throw new Exception($preMsg." El parámetro rendicionAvanceReintegro referencia es nulo.");
				
			if(($referencia=trim($referencia)) == '')
				throw new Exception($preMsg." El parámetro rendicionAvanceReintegro referencia está vacío.");
				
			if(($fecha=$rendicionAvanceReintegro->GetFecha()) == null)
				throw new Exception($preMsg." El parámetro rendicionAvanceReintegro fecha es nulo.");
				
			if(($fecha=trim($fecha)) == '')
				throw new Exception($preMsg." El parámetro rendicionAvanceReintegro fecha está vacío.");
				
			if(($monto=$rendicionAvanceReintegro->GetMonto()) == null)
				throw new Exception($preMsg." El parámetro rendicionAvanceReintegro monto es nulo.");
				
			if(($monto=trim($monto)) == '')
				throw new Exception($preMsg." El parámetro rendicionAvanceReintegro monto está vacío.");
			
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception($preMsg.'No se pudo iniciar la transacción. Detalles: '
					. utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$query = "
				INSERT INTO safi_responsable_rendicion_avance_reintegro
					(
						responsable_avance_id,
						rendicion_avance_id,
						banco_id,
						referencia,
						fecha,
						monto					
					)
				VALUES
					(
						'".$idResponsable."',
						'".$idRendicionAvance."',
						'".$idBanco."',
						'".$GLOBALS['SafiClassDb']->Quote($referencia)."',
						to_date('".$fecha."', 'DD/MM/YYYY'),
						'".$monto."'
					)
			";
			
			$result = $GLOBALS['SafiClassDb']->Query($query);
			
			if($result === false) throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception($preMsg." Detalles: " . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
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
	
	public static function GetRendicionAvanceReintegros($params = null)
	{
		try
		{
			$preMsg = "Error al obtener los datos de reintegro de un responsable de una rendicion de avance.";
			$arrMsg = array();
			$existeCriterio = false;
			$queryWhere = "";
			
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
				$queryWhere = "reintegro.rendicion_avance_id = '" . $idRendicion . "'";
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
				$queryWhere .= "reintegro.responsable_avance_id IN ('" . implode("', '", $idsResponsables) . "')";
			}
			
			if(!$existeCriterio){
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}
			
			
			$query = "
				SELECT
					reintegro.responsable_avance_id AS reintergro_responsable_avance_id,
					reintegro.rendicion_avance_id AS reintergro_rendicion_avance_id,
					reintegro.banco_id AS reintergro_banco_id,
					reintegro.referencia AS reintergro_referencia,
					to_char(reintegro.fecha, 'DD/MM/YYYY') AS reintergro_fecha,
					reintegro.monto AS reintergro_monto
				FROM
					safi_responsable_rendicion_avance_reintegro reintegro
				WHERE
					" . $queryWhere . "
				ORDER BY
					reintegro.responsable_avance_id
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ".($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$rendicionAvancePartidas = array();
			
			$rows = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){ $rows[] = $row; }
			if(count($rows) == 0) return array();
			
			return  self::LlenarRendicionAvanceReintegros($rows);
			
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
		return false;
	}
	
	public static function BorrarRendicionAvanceReintegroByIdRendicion($idRendicionAvance)
	{
		try
		{
			$preMsg = 'Error al intentar borrar de rendicionAvance-reintegro dado el id de la rendición.';
			
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
					safi_responsable_rendicion_avance_reintegro
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
	
	public static function LlenarRendicionAvanceReintegros(array $rows = null)
	{
		try
		{
			$preMsg = "Error al intentar llenar los objetos de los responsables de rendición de avance.";
			$idsBancos = array();
			$bancos = null;
			$rendicionAvanceReintegros = array();
			
			foreach ($rows as $row)
			{
				$idsBancos[] = $row['reintergro_banco_id'];
			}
			
			if(count($idsBancos) > 0)
				$bancos = SafiModeloBanco::GetBancos(array('idsBancos' =>  $idsBancos));
			
			foreach ($rows as $row)
			{
				if(($banco=$bancos[$row['reintergro_banco_id']]) == null)
					throw new Exception($preMsg." El banco[".($row['reintergro_banco_id'])."] del responsable ["
						.$row['reintergro_responsable_avance_id']."], de la rendicion de avance["
						.$row['reintergro_rendicion_avance_id']."] no pudo ser cargado.");
				
				if(!isset($rendicionAvanceReintegros[$row['reintergro_responsable_avance_id']]))
				{
					$rendicionAvanceReintegros[$row['reintergro_responsable_avance_id']] = array();
				}
						
				$rendicionAvanceReintegros[$row['reintergro_responsable_avance_id']][] = self::LlenarRendicionAvanceReintegro($row, $banco);
			}
			
			return $rendicionAvanceReintegros;
				
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	public static function LlenarRendicionAvanceReintegro(array $row, EntidadBanco $banco)
	{
		$rendicionAvanceReintegro = new EntidadRendicionAvanceReintegro();
		
		$rendicionAvanceReintegro->SetIdResponsableAvance($row['reintergro_responsable_avance_id']);
		$rendicionAvanceReintegro->SetIdRendicionAvance($row['reintergro_rendicion_avance_id']);
		$rendicionAvanceReintegro->SetBanco($banco);
		$rendicionAvanceReintegro->SetReferencia($row['reintergro_referencia']);
		$rendicionAvanceReintegro->SetFecha($row['reintergro_fecha']);
		$rendicionAvanceReintegro->SetMonto($row['reintergro_monto']);
		
		return $rendicionAvanceReintegro;
	}
}