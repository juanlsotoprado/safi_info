<?php
include_once(SAFI_ENTIDADES_PATH . '/estado.php');

class SafiModeloEstado
{
	private static function GetSelectFildsEstado()
	{
		return "
			id AS estado_id,
	  		nombre AS estado_nombre,
	  		estatus_actividad AS estado_estatus_actividad
		";
	}
	
	private static function LLenarEstado($row)
	{
		$estado = new EntidadEstado();
		$estado->SetId($row['estado_id']);
		$estado->SetNombre($row['estado_nombre']);
		$estado->SetEstatusActividad($row['estado_estatus_actividad']);
		
		return $estado;
	}
	
	public static function  GetAllEstados()
	{
		$estados = array();
		
		$query = "
			SELECT
				id,
  				nombre,
  				estatus_actividad
			FROM
				safi_edos_venezuela
			ORDER BY
				nombre
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$estados[$row['id']] = $row;
		}
		
		return $estados;
	}
	
	public static function GetAllEstados2()
	{
		$estados = array();
		
		$query = "
			SELECT
				".self::GetSelectFildsEstado()."
			FROM
				safi_edos_venezuela estado
			ORDER BY
				estado.nombre
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$estados[$row['estado_id']] = self::LlenarEstado($row);
		}
		
		return $estados;
	}
	
	public static function GetEstadoById($idEstado){
		$estado = null;
		
		$query = "
			SELECT
				".self::GetSelectFildsEstado()."
			FROM
				safi_edos_venezuela estado
			WHERE
				estado.id = '".$idEstado."'
		";
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$estado = self::LlenarEstado($row);
			}
		}
		
		return $estado;
	}
    
	
 public static function GetEstadosById($idEstado){
		$estado = null;
		
		$query = "
			SELECT
				".self::GetSelectFildsEstado()."
			FROM
				safi_edos_venezuela estado
			WHERE
				estado.id = '".$idEstado."'
		";
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$estado = self::LlenarEstado($row);
			}
		}
		
		return $estado;
	}

	public static function GetEstados($params)
	{
		try
		{
			$preMsg = "Error al intentar obtener los estados de Venezuela.";
			$arrMsg = array();
			$existeCriterio = false;
			$queryWhere = "";
			$estados = null;
			
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
				
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
				
			if(count($params) == 0)
				throw new Exception($preMsg." El parámetro params está vacío.");
			
			if(!isset($params['idsEstados']))
				$arrMsg[] = "El parámetro params['idsEstados'] no pudo ser encontrado.";
			else if(($idsEstados=$params['idsEstados']) == null)
				$arrMsg[] = "El parámetro params['idsEstados'] es nulo.";
			else if(!is_array($idsEstados))
				$arrMsg[] = "El parámetro params['idsEstados'] no es un arreglo.";
			else if(count($idsEstados) == 0)
				$arrMsg[] = "El parámetro params['idsEstados'] está vacío.";
			else{
				$existeCriterio = true;
				if($queryWhere != ""){
					$queryWhere = "
						AND ";
				}
				$queryWhere .= "estado.id IN ('" . implode("', '", $idsEstados) . "')";
			}
			
			if(!$existeCriterio){
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}
			
			$query = "
				SELECT
					".self::GetSelectFildsEstado()."
				FROM
					safi_edos_venezuela estado
				WHERE
					" . $queryWhere . "
				ORDER BY
					nombre
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ".utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()) . $query);
			
			$estados = array();
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$estados[$row['estado_id']] = self::LlenarEstado($row); 
			}
			
			return $estados;
			
		} catch (Exception $e) {
			error_log($e, 0);
			return null;
		}
	}	
}