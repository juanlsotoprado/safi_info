<?php
include_once(SAFI_ENTIDADES_PATH . '/responsableAvancePartidas.php');

include_once(SAFI_MODELO_PATH . '/responsableAvance.php' );
include_once(SAFI_MODELO_PATH . '/avancePartida.php' );

class SafiModeloResponsableAvancePartidas
{
	public static function GuardarResponsableAvancePartidas(EntidadResponsableAvancePartidas $responsableAvancePartidas = null)
	{
		try
		{
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			if($responsableAvancePartidas == null)
				throw new Exception("El parametro responsableAvancePartidas es nulo");
			
			$responsableAvance = $responsableAvancePartidas->GetResponsableAvance();
			$avancePartidas = $responsableAvancePartidas->GetAvancePartidas();
			
			$idResponsable = SafiModeloResponsableAvance::GuardarResponsableAvance($responsableAvance);

			if($idResponsable === false)
				throw new Exception('Error al guardar el responsable-avance-partida. No se pudo guardar guardar la información '.
					'del responsable-avance');
				
			if(is_array($avancePartidas))
			{
				$indexAvancePartida = 0;
				foreach ($avancePartidas as $avancePartida)
				{
					if(
						$avancePartida->GetPartida() != null && $avancePartida->GetPartida()->GetId() != null
						&& trim($avancePartida->GetPartida()->GetId()) != ''
						&& $avancePartida->GetMonto() != null && trim($avancePartida->GetMonto()) != ''
					){
						$avancePartida->SetIdAvance($responsableAvance->GetIdAvance());
						$avancePartida->SetIdResponsableAvance($idResponsable);
						
						if(SafiModeloAvancePartida::GuardarAvancePartida($avancePartida) === false)
							throw new Exception('Error al guardar el responsable-avance-partida. No se pudo guardar la informacion del '.
								'avance-partida['.($indexAvancePartida+1).'].');
					}
					
					$indexAvancePartida++;
				} // Fin de foreach ($avancePartidas as $avancePartida)
			}
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la función de guardado del responsable-avance-partida. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			return $idResponsable;
		}
		catch (Exception $e)
		{
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}
	
	public static function ActualizarResponsableAvancePartidas(EntidadResponsableAvancePartidas $responsableAvancePartidas)
	{
		try
		{
			if($responsableAvancePartidas == null)
				throw new Exception("El parametro responsableAvancePartidas es nulo");
			
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$responsableAvance = $responsableAvancePartidas->GetResponsableAvance();
			$avancePartidas = $responsableAvancePartidas->GetAvancePartidas();
			
			if(SafiModeloResponsableAvance::ActualizarResponsableAvance($responsableAvance) === false)
				throw new Exception('Error al actualizar el responsable-avance-partida. No se pudo guardar actualizar la información '.
					'del responsable-avance');
			
			// Eliminar las relaciones responsable-avance-partidas del avance guardadas
			if(
				SafiModeloAvancePartida::EliminarAvancePartidasByIdAvanceAndIdResponsable(
					$responsableAvance->GetIdAvance(), $responsableAvance->GetId()) === false
			)
				throw new Exception('Error al actualizar el responsable-avance-partida. No se pudo eliminar la información '.
					'del avance-partidas');
			
			// Insertar las relaciones responsable-avance-partidas del avance actualizado
			if(is_array($avancePartidas))
			{
				$indexAvancePartida = 0;
				foreach ($avancePartidas as $avancePartida)
				{
					if(
						$avancePartida->GetPartida() != null && $avancePartida->GetPartida()->GetId() != null
						&& trim($avancePartida->GetPartida()->GetId()) != '' && $avancePartida->GetPartida() != null
						&& $avancePartida->GetMonto() != null && trim($avancePartida->GetMonto()) != ''
					){
						$avancePartida->SetIdAvance($responsableAvance->GetIdAvance());
						$avancePartida->SetIdResponsableAvance($responsableAvance->GetId());
						
						if(SafiModeloAvancePartida::GuardarAvancePartida($avancePartida) === false)
							throw new Exception('Error al actualizar el responsable-avance-partida. No se pudo guardar la informacion del '.
								'avance-partida['.($indexAvancePartida+1).'].');
					}
					
					$indexAvancePartida++;
				}
			}	
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la función de actualización del responsable-avance-partida. Detalles: ".
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
	
	public static function EliminarResponsableAvancePartidas(EntidadResponsableAvancePartidas $responsableAvancePartidas)
	{
		try
		{
			if($responsableAvancePartidas == null)
				throw new Exception("El parametro responsableAvancePartidas es nulo");
			
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$responsableAvance = $responsableAvancePartidas->GetResponsableAvance();
			
			if($responsableAvance == null)
				throw new Exception("El parámetro responsableAvancePartidas responsableAvance es nulo");

			// Eliminar las relaciones responsable-avance-partida del responsable a eliminar
			if( SafiModeloAvancePartida::EliminarAvancePartidasByIdAvanceAndIdResponsable(
				$responsableAvance->GetIdAvance(), $responsableAvance->GetId()) === false
			)
				throw new Exception('Error al eliminar la relación responsable-avance-partidas. Detalles: '
					. utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			// Eliminar la relación responsable-avance
			if(SafiModeloResponsableAvance::EliminarResponsableAvance($responsableAvance) === false)
				throw new Exception('Error al eliminar el responsable-avance-partida. No se pudo eliminar la información '.
					'del responsable-avance');
								
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la función de eliminación del responsable-avance-partida. Detalles: ".
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
	
	public static function EliminarResponsableAvancePartidasByIdAvance($idAvance)
	{
		try
		{
			if($idAvance == null || ($idAvance=trim($idAvance)) == '')
				throw new Exception("El parametro idAvance es nulo");
			
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			// Eliminar las relaciones responsable-avance-partida
			if( SafiModeloAvancePartida::EliminarAvancePartidasByIdAvance($idAvance) === false)
				throw new Exception('Error al eliminar la relación responsable-avance-partidas. Detalles: '
					. utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			// Eliminar la relación responsable-avance
			if(SafiModeloResponsableAvance::EliminarResponsableAvanceByIdAvance($idAvance) === false)
				throw new Exception('Error al eliminar el responsable-avance-partida. No se pudo eliminar la información '.
					'del responsable-avance');
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la función de eliminación del responsable-avance-partida. Detalles: ".
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
	
	public static function GetResponsableAvancePartidasByIdAvance($idAvance)
	{
		try
		{
			if($idAvance == null || ($idAvance=trim($idAvance)) == '')
				throw new Exception("El parametro idAvance es nulo");
				
			return self::GetResponsableAvancePartidas(array("idAvance" => $idAvance));
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetResponsablesAvancePartidasByIdsResponsables(array $idsResponsables = null)
	{
		try
		{
			if ($idsResponsables == null)
				throw new Exception("El parámetro idsResponsables es nulo.");
				
			if (!is_array($idsResponsables))
				throw new Exception("El parámetro idsResponsables no es un arreglo.");
				
			if(count($idsResponsables) == 0)
				throw new Exception("El parámetro idsResponsables está vacío.");
				
			return self::GetResponsableAvancePartidas(array("idsResponsables" => $idsResponsables));
				
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetResponsableAvancePartidas($params = null)
	{
		try
		{
			if ($params == null)
				throw new Exception("El parámetro params es nulo.");
				
			if (!is_array($params))
				throw new Exception("El parámetro params no es un arreglo.");
				
			if(count($params) == 0)
				throw new Exception("El parámetro params está vacío.");
			
			// Establecer los datos de los responsables del avance
			$responsablesAvancePartidas = array();
			
			// Obtener los datos de los responsables del avance
			$responsablesAvance = SafiModeloResponsableAvance::GetResponsablesAvance($params);
			
			// Obtener los datos de las partidas de los responsables del avance
			$avancePartidas = SafiModeloAvancePartida::GetAvancePartidas($params);
			
			if(is_array($responsablesAvance)){
				foreach ($responsablesAvance as $responsableAvance)
				{
					$responsableAvancePartidas = new EntidadResponsableAvancePartidas();
					$responsableAvancePartidas->SetResponsableAvance($responsableAvance);
					
					if(is_array($avancePartidas))
					{
						$responsableAvancePartidas->SetAvancePartidas($avancePartidas[$responsableAvance->GetId()]);
					}
					
					$responsablesAvancePartidas[$responsableAvance->GetId()] = $responsableAvancePartidas; 
				}
			}
			
			return $responsablesAvancePartidas;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
}