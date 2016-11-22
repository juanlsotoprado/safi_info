<?php
include_once(SAFI_ENTIDADES_PATH . '/responsableRendicionAvancePartidas.php');

include_once(SAFI_MODELO_PATH . '/responsableRendicionAvance.php');
include_once(SAFI_MODELO_PATH . '/rendicionAvancePartida.php');
include_once(SAFI_MODELO_PATH . '/rendicionAvanceReintegro.php');

class SafiModeloResponsableRendicionAvancePartidas
{
	public static function GuardarResponsableRendicionAvancePartidas(
		EntidadResponsableRendicionAvancePartidas $responsableRendicionAvancePartidas
	){
		try
		{
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
				
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			if($responsableRendicionAvancePartidas == null)
				throw new Exception("El parametro responsableRendicionAvancePartidas es nulo");
			
			$responsableRendicionAvance = $responsableRendicionAvancePartidas->GetResponsableRendicionAvance();
			$rendicionAvancePartidas = $responsableRendicionAvancePartidas->GetRendicionAvancePartidas();
			$rendicionAvanceReintegros = $responsableRendicionAvancePartidas->GetRendicionAvanceReintegros();
			
			$idResponsable = SafiModeloResponsableRendicionAvance::GuardarResponsableRendicionAvance($responsableRendicionAvance);
			
			if($idResponsable === false)
				throw new Exception('Error al guardar el responsable-rendicionAvance-partida. No se pudo guardar guardar la información '.
					'del responsable-rendicionAvance');

			// Guardar los rendicionAvancePartidas
			if(is_array($rendicionAvancePartidas))
			{
				$indexRendicionAvancePartida = 0;
				foreach ($rendicionAvancePartidas as $rendicionAvancePartida)
				{
					if(
						$rendicionAvancePartida->GetPartida() != null && $rendicionAvancePartida->GetPartida()->GetId() != null
						&& trim($rendicionAvancePartida->GetPartida()->GetId()) != ''
						&& $rendicionAvancePartida->GetMonto() != null && trim($rendicionAvancePartida->GetMonto()) != ''
					){
						$rendicionAvancePartida->SetIdRendicionAvance($responsableRendicionAvance->GetIdRendicionAvance());
						$rendicionAvancePartida->SetIdResponsableAvance($idResponsable);
						
						if(SafiModeloRendicionAvancePartida::GuardarRendicionAvancePartida($rendicionAvancePartida) === false)
							throw new Exception('Error al guardar el responsable-rendicionAvance-partida. Detalles: '.
								'No se pudo guardar la informacion de la rendicionAvance-partida['.($indexRendicionAvancePartida+1).'].');
						
					}
					$indexRendicionAvancePartida++;
				} //  foreach ($rendicionAvancePartidas as $rendicionAvancePartida)
			}
			
			// Guardar los rendicionAvanceReintegros
			if(is_array($rendicionAvanceReintegros))
			{
				$indexRendicionAvanceReintegro = 0;
				foreach ($rendicionAvanceReintegros as $rendicionAvanceReintegro)
				{
					if(
						($banco=$rendicionAvanceReintegro->GetBanco()) != null
						&& ($idBanco=$banco->GetId()) != null && trim($idBanco) != ''
						&& ($referencia=$rendicionAvanceReintegro->GetReferencia()) != null && trim($referencia) != ''
						&& ($fecha=$rendicionAvanceReintegro->GetFecha()) != null && trim($fecha) != ''
						&& ($monto=$rendicionAvanceReintegro->GetMonto()) != null && trim($monto) != '' 
					){
						$rendicionAvanceReintegro->SetIdRendicionAvance($responsableRendicionAvance->GetIdRendicionAvance());
						$rendicionAvanceReintegro->SetIdResponsableAvance($idResponsable);
						
						if(SafiModeloRendicionAvanceReintegro::GuardarRendicionAvanceReintegro($rendicionAvanceReintegro) === false)
							throw new Exception('Error al guardar el responsable-rendicionAvance-partida. Detalles: '
								.'No se pudo guardar la información de la rendicionAvance-reintegro['
								.($indexRendicionAvanceReintegro+1).'].');
					}
				}
			}
			
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la función de guardado del responsable-rendicionAvance-partida. ".
					"Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
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
	
	public static function GetResponsableRendicionAvancePartidasByIdRendicion($idRendicion)
	{
		try
		{
			$preMsg = "Error al intentar obtener los responsablesRendicionAvancePartidasByIdRendicion.";
			
			if($idRendicion == null || ($idRendicion=trim($idRendicion)) == '')
				throw new Exception($preMsg." El parametro idRendicion es nulo");
				
			return self::GetResponsableRendicionAvancePartidas(array("idRendicion" => $idRendicion));
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetResponsableRendicionAvancePartidas($params = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener los responsablesRendicionAvancePartidas.";
			
			if ($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
				
			if (!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
				
			if(count($params) == 0)
				throw new Exception($preMsg." El parámetro params está vacío.");
				
			// Establecer los datos de los responsables de la rendición de avance
			$responsablesRendicionAvancePartidas = array();
			
			// Obtener los datos de los responsables de la rendición de avance
			$responsablesRendicionAvance = SafiModeloResponsableRendicionAvance::GetResponsablesRendicionAvance($params);
			
			$responsablesAvancePartidas = null;
			
			// Obtener el id de los responsables de la rendición de avance
			$idsResponsables = array();
			if(is_array($responsablesRendicionAvance) && count($responsablesRendicionAvance) > 0){
				reset($responsablesRendicionAvance);
				$idsResponsables[] = key($responsablesRendicionAvance);
				while (next($responsablesRendicionAvance)){
					$idsResponsables[] = key($responsablesRendicionAvance);
				}
				
				$responsablesAvancePartidas = SafiModeloResponsableAvancePartidas
					::GetResponsablesAvancePartidasByIdsResponsables($idsResponsables);
			}
			
			// Obtener los datos de las partidas de los responsables de la rendición de avance
			$lstRendicionAvancePartidas = SafiModeloRendicionAvancePartida::GetRendicionAvancePartidas($params);
			
			// Obtener los datos de reintegros de los responsables de la rendición de avance
			$lstRendicionAvanceReintegros = SafiModeloRendicionAvanceReintegro::GetRendicionAvanceReintegros($params);
			
			if(is_array($responsablesRendicionAvance)){
				foreach ($responsablesRendicionAvance as $idResponsable => $responsableRendicionAvance)
				{
					$responsableRendicionAvancePartidas = new EntidadResponsableRendicionAvancePartidas();
					$responsableRendicionAvancePartidas->SetResponsableRendicionAvance($responsableRendicionAvance);
					
					$rendicionAvancePartidas = is_array($lstRendicionAvancePartidas) ? $lstRendicionAvancePartidas[$idResponsable] : null;
					$rendicionAvanceReintegros = is_array($lstRendicionAvanceReintegros) ?
						$lstRendicionAvanceReintegros[$idResponsable] : null;
					$responsableAvancePartidas = is_array($responsablesAvancePartidas) ? $responsablesAvancePartidas[$idResponsable] : null;
					
					if($rendicionAvancePartidas != null && $responsableAvancePartidas != null)
						$responsableRendicionAvancePartidas->SetMontoAnticipo($responsableAvancePartidas->GetMontoTotal());
					
					$responsableRendicionAvancePartidas->SetRendicionAvancePartidas($rendicionAvancePartidas);
					$responsableRendicionAvancePartidas->SetRendicionAvanceReintegros($rendicionAvanceReintegros);
					
					$responsablesRendicionAvancePartidas[$responsableRendicionAvance->GetIdResponsableAvance()]
						= $responsableRendicionAvancePartidas;
				}
			}
			
			return $responsablesRendicionAvancePartidas;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	public static function BorrarResponsableRendicionAvancePartidasByIdRendicion($idRendicionAvance = null)
	{
		try
		{
			$preMsg = 'Error al intentar borrar los resposanblesRendicionAvancePartidas dado el id de la rendición.';
			
			if($idRendicionAvance == null)
				throw new Exception($preMsg.' El parámetro idRendicionAvance es nulo');
			
			if(($idRendicionAvance=trim($idRendicionAvance)) == '')
				throw new Exception($preMsg.' El parámetro idRendicionAvance está vacío');
			
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
				
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			if(SafiModeloRendicionAvanceReintegro::BorrarRendicionAvanceReintegroByIdRendicion($idRendicionAvance) === false)
				throw new Exception($preMsg.' No se pudo borrar la información de rendicionAvance-reintegro.');
				
			if(SafiModeloRendicionAvancePartida::BorrarRendicionAvancePartidaByIdRendicion($idRendicionAvance) === false)
				throw new Exception($preMsg.' No se pudo borrar la información de rendicionAvance-partida.');
				
			if(SafiModeloResponsableRendicionAvance::BorrarResponsableRendicionAvanceByIdRendicion($idRendicionAvance) === false)
				throw new Exception($preMsg.' No se pudo borrar la información de responsableRendicionAvance.');
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception($preMsg.'No se pudo realizar el commit. Detalles: '
					. utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
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
}