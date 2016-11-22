<?php
include_once(SAFI_ENTIDADES_PATH . '/rutaAvance.php');

class SafiModeloRutaAvance
{
	public static function GuardarRutaAvance(EntidadRutaAvance $rutaAvance)
	{
		try
		{
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$query = "
				INSERT INTO safi_ruta_avance
					(
						avance_id,
						parroquia_id,
						municipio_id,
						ciudad_id,
						edo_id,
						direccion
					)
				VALUES
					(
						".(
							(
								$rutaAvance->GetIdAvance() != null && trim($rutaAvance->GetIdAvance()) != ''
							)
							? "'" . $rutaAvance->GetIdAvance() . "'" : "NULL"
						
						).",
						".(
							(
								$rutaAvance->GetParroquia() != null && $rutaAvance->GetParroquia()->GetId() != null
								&& trim($rutaAvance->GetParroquia()->GetId()) != ''
							)
							? "'".trim($rutaAvance->GetParroquia()->GetId())."'" : "NULL"
						).",
						".(
							(
								$rutaAvance->GetMunicipio() != null && $rutaAvance->GetMunicipio()->GetId() != null
								&& trim($rutaAvance->GetMunicipio()->GetId()) != ''
							)
							? "'".trim($rutaAvance->GetMunicipio()->GetId())."'": "NULL"
						).",
						".(
							(
								$rutaAvance->GetCiudad() != null && $rutaAvance->GetCiudad()->GetId() != null
								&& trim($rutaAvance->GetCiudad()->GetId()) != ''
							)
							? "'".trim($rutaAvance->GetCiudad()->GetId())."'": "NULL"
						).",
						".(
							(
								$rutaAvance->GetEstado() != null && $rutaAvance->GetEstado()->GetId() != null
								&& trim($rutaAvance->GetEstado()->GetId()) != ''
							)
							? "'".trim($rutaAvance->GetEstado()->GetId())."'": "NULL"
						).",
						".(
							($rutaAvance->GetDireccion() != null && trim($rutaAvance->GetDireccion()) != '')
							? "'".$GLOBALS['SafiClassDb']->Quote(trim($rutaAvance->GetDireccion()))."'": "NULL"
						)."
					)
			";
			
			$result = $GLOBALS['SafiClassDb']->Query($query);
		
			if($result === false) throw new Exception('Error al guardar la ruta del avance '. $rutaAvance->GetId() .'. Detalles: ' .
				$GLOBALS['SafiClassDb']->GetErrorMsg());
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la funcion de guardado de la ruta del avance. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			return true;
		}
		catch (Exception $e) {
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}
	
	public static function ActualizarRutaAvance(EntidadRutaAvance $rutaAvance)
	{
		try
		{
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$query = "
				UPDATE
					safi_ruta_avance
				SET
					parroquia_id = ".(
						(
							$rutaAvance->GetParroquia() != null && $rutaAvance->GetParroquia()->GetId() != null
							&& trim($rutaAvance->GetParroquia()->GetId()) != ''
						)
						? "'".trim($rutaAvance->GetParroquia()->GetId())."'": "NULL"
					).",
					municipio_id = ".(
						(
							$rutaAvance->GetMunicipio() != null && $rutaAvance->GetMunicipio()->GetId() != null
							&& trim($rutaAvance->GetMunicipio()->GetId()) != ''
						)
						? "'".trim($rutaAvance->GetMunicipio()->GetId())."'": "NULL"
					).",
					ciudad_id = ".(
						(
							$rutaAvance->GetCiudad() != null && $rutaAvance->GetCiudad()->GetId() != null
							&& trim($rutaAvance->GetCiudad()->GetId()) != ''
						)
						? "'".trim($rutaAvance->GetCiudad()->GetId())."'": "NULL"
					).",
					edo_id = ".(
						(
							$rutaAvance->GetEstado() != null && $rutaAvance->GetEstado()->GetId() != null
							&& trim($rutaAvance->GetEstado()->GetId()) != ''
						)
						? "'".trim($rutaAvance->GetEstado()->GetId())."'": "NULL"
					).",
					direccion = ".(
						($rutaAvance->GetDireccion() != null && trim($rutaAvance->GetDireccion()) != '')
						? "'".trim($GLOBALS['SafiClassDb']->Quote($rutaAvance->GetDireccion()))."'": "NULL"
					)."
				WHERE
					id = ".$rutaAvance->GetId()."
					AND avance_id = '" . $rutaAvance->GetIdAvance() . "'
			";
			
			$result = $GLOBALS['SafiClassDb']->Query($query);
		
			if($result === false) throw new Exception('Error al actualizar la ruta del avance '. $rutaAvance->GetId() .'. Detalles: ' .
				$GLOBALS['SafiClassDb']->GetErrorMsg());
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la funcion de actualización de la ruta del avance. Detalles: ".
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
	
	public static function EliminarRutaAvance(EntidadRutaAvance $rutaAvance)
	{
		try
		{
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$query = "
				DELETE FROM
					safi_ruta_avance
				WHERE
					id = ".$rutaAvance->GetId()."
					AND avance_id = '" . $rutaAvance->GetIdAvance() . "'
			";
			
			$result = $GLOBALS['SafiClassDb']->Query($query);
		
			if($result === false) throw new Exception('Error al eliminar la ruta del avance '. $rutaAvance->GetId() .'. Detalles: ' .
				$GLOBALS['SafiClassDb']->GetErrorMsg());
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la funcion de eliminación de la ruta del avance. Detalles: ".
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
	
	public static function EliminarRutasAvanceByIdAvance($idAvance)
	{
		try
		{
			if($idAvance == null || trim($idAvance) == '')
				throw new Exception("El parametro idAvance es nulo o está vacío.");
			
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$query = "
				DELETE FROM
					safi_ruta_avance
				WHERE
					avance_id = '" . $idAvance . "'
			";
			
			$result = $GLOBALS['SafiClassDb']->Query($query);
		
			if($result === false) throw new Exception('Error al eliminar las ruta del avance '. $idAvance .'. Detalles: ' .
				$GLOBALS['SafiClassDb']->GetErrorMsg());
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
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
	
	public static function GetRutasByIdsAvances(array $idsAvances)
	{
		return self::__GetRutasByIdsAvances($idsAvances);
	}
	
	public static function GetRutasByIdAvance($idAvance)
	{
		$rutasByAvances = self::__GetRutasByIdsAvances(array($idAvance));
		
		if(count($rutasByAvances)>0) return  current($rutasByAvances);
		else return null; 
	}
	
	private static function __GetRutasByIdsAvances(array $idsAvances)
	{
		$rutasByAvances = null;
		
		try {
			
			if(!is_array($idsAvances) || count($idsAvances) == 0)
				throw new Exception("El parametro idsAvances es nulo o esta vacio.");
			
			$query = "
				SELECT
					ruta_avance.id,
					ruta_avance.avance_id,
					ruta_avance.direccion,
					edo.id as edo_id,
					edo.nombre as edo_nombre,
					edo.estatus_actividad as edo_estatus_actividad,
					ciudad.id as ciudad_id,
					ciudad.nombre as ciudad_nombre,
					ciudad.edo_id as ciudad_edo_id,
					ciudad.estatus_actividad as ciudad_estatus_actividad,
					municipio.id as municipio_id,
					municipio.nombre as municipio_nombre,
					municipio.edo_id as municipio_edo_id,
					municipio.estatus_actividad as municipio_estatus_actividad,
					parroquia.id as parroquia_id,
					parroquia.nombre as parroquia_nombre,
					parroquia.municipio_id as parroquia_municipio_id,
					parroquia.estatus_actividad as parroquia_estatus_actividad
				FROM
					safi_ruta_avance ruta_avance
					LEFT OUTER JOIN safi_edos_venezuela edo ON (edo.id = ruta_avance.edo_id)
					LEFT OUTER JOIN safi_ciudad ciudad ON (ciudad.id = ruta_avance.ciudad_id)
					LEFT OUTER JOIN safi_municipio municipio ON (municipio.id = ruta_avance.municipio_id)
					LEFT OUTER JOIN safi_parroquia parroquia ON (parroquia.id = ruta_avance.parroquia_id)
				WHERE
					avance_id IN ('".implode("' ,'", $idsAvances)."')
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al obtener las rutas del avance. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$rutasByAvances = array();
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				if(!isset($rutasByAvances[$row['avance_id']])){
					$rutasByAvances[$row['avance_id']] = array();
				}
				$rutasByAvances[$row['avance_id']][] = self::LlenarRutaAvance($row);
			}
			
			return $rutasByAvances;
			
		} catch (Exception $e) {
			error_log($e, 0);
			return null;
		}
	}
	
	private static function LlenarRutaAvance($row)
	{
		$estado = null;
		$ciudad = null;
		$municipio = null;
		$parroquia = null;
		
		// Establecer los datos del estado de la ruta
		if($row['edo_id'] != null)
		{
			$estado = new EntidadEstado();
			
			$estado->SetId($row['edo_id']);
			$estado->SetNombre($row['edo_nombre']);
			$estado->SetEstatusActividad($row['edo_estatus_actividad']);
		}
		
		// Establecer los datos de la ciudad
		if($row['ciudad_id'] != null)
		{
			$ciudad = new EntidadCiudad();
			
			$ciudad->SetId($row['ciudad_id']);
			$ciudad->SetNombre($row['ciudad_nombre']);
			$ciudad->SetEstatusActividad($row['ciudad_estatus_actividad']);
		}
		
		// Establecer los datos de el municipio
		if($row['municipio_id'] != null)
		{
			$municipio = new EntidadMunicipio();
			
			$municipio->SetId($row['municipio_id']);
			$municipio->SetNombre($row['municipio_nombre']);
			$municipio->SetEstatusActividad($row['municipio_estatus_actividad']);
		}
		
		// Establecer los datos de la parroquia
		if($row['parroquia_id'] != null)
		{
			$parroquia = new EntidadParroquia();
			
			$parroquia->SetId($row['parroquia_id']);
			$parroquia->SetNombre($row['parroquia_nombre']);
			$parroquia->SetEstatusActividad($row['parroquia_estatus_actividad']);
		}
		
		// crear y llenar los datos de la ruta del avance
		$rutaAvance = new EntidadRutaAvance();
		
		$rutaAvance->SetId($row['id']);
		$rutaAvance->SetIdAvance($row['avance_id']);
		$rutaAvance->SetEstado($estado);
		$rutaAvance->SetCiudad($ciudad);
		$rutaAvance->SetMunicipio($municipio);
		$rutaAvance->SetParroquia($parroquia);
		$rutaAvance->SetDireccion($row['direccion']);
		
		return $rutaAvance;
	}
}