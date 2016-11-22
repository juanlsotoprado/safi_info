<?php
include_once(SAFI_ENTIDADES_PATH."/historialAccion.php");

class SafiModeloHistorialAccion
{
	public static function GetHistorialAcciones(array $params = null)
	{
		try
		{
			$historialAcciones = null;
			$preMsg = "Error al intentar obtener el historialAcciones.";
			$arrMsg = array();
			$queryWhere = "";
			$existeCriterio = false;
			
			if($params === null)
				throw new Exception($preMsg. "El parámetro \"params\" es nulo.");
			if(count($params) == 0)
				throw new Exception($preMsg. "El parámetro \"params\" está vacío.");
				
			if(!isset($params['idDocumento']))
				$arrMsg[] = "El parámetro \"params['idDocumento']\" no pudo ser encontrado.";
			else if(($idDocumento=$params['idDocumento']) === null)
				$arrMsg[] = "El parámetro \"params['idDocumento']\" es nulo.";
			else{
				$existeCriterio = true;
				$queryWhere = "historial_accion.hist_docu='".$idDocumento."'";
			}
			
			if(!$existeCriterio)
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			
			
			$query = "
				SELECT
					".self::GetSelectFieldsHistorialAccion()."
				FROM
					sai_histaccion historial_accion
				WHERE
					".$queryWhere."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ".utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$historialAcciones = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$historialAcciones[] = self::LlenarHistorialAccion($row);
			}
				
			return $historialAcciones;
		}
		catch(Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetUsuaLoginsByIdDocumento($idDocumento = null)
	{
		try
		{
			$usuaLogins = null;
			$preMsg = "Error al intentar obtener el usauLogin del historial de acción dado el id del documento.";
			
			if($idDocumento === null)
				throw new Exception($preMsg." El parámetro idDocumento es nulo.");
			
			if(($idDocumento=trim($idDocumento)) == "")
				throw new Exception($preMsg." El parámetro idDocumento está vacío.");
				
			$query = "
				SELECT DISTINCT
					historial_accion.usua_login AS historial_accion_usua_login
				FROM
					sai_histaccion historial_accion
				WHERE
					historial_accion.hist_docu = '".$idDocumento."'
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ".utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$usuaLogins = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$usuaLogins[] = $row['historial_accion_usua_login'];
			}
			
			return $usuaLogins;
		}
		catch(Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetSelectFieldsHistorialAccion()
	{
		return "
					historial_accion.hist_id AS historial_accion_id,		
					historial_accion.hist_docu AS historial_accion_documento_id,
					to_char(historial_accion.hist_fecha_hora, 'DD/MM/YYYY HH24:MI:SS') AS historial_accion_fecha,
					historial_accion.usua_login AS historial_accion_usua_login,
					historial_accion.carg_id AS historial_accion_perfil_id,
					historial_accion.acci_id AS historial_accion_accion_id,
					historial_accion.depe_id AS historial_accion_dependencia_id,
					historial_accion.hist_descrip AS historial_accion_descripcion,
					historial_accion.hist_firma AS historial_accion_firma
		";
	}
	
	public static function LlenarHistorialAccion($row)
	{
		$historialAccion = new EntidadHistorialAccion();
		
		$historialAccion->SetId($row['historial_accion_id']);
		$historialAccion->SetIdDocumento($row['historial_accion_documento_id']);
		$historialAccion->SetFecha($row['historial_accion_fecha']);
		$historialAccion->SetUsuaLogin($row['historial_accion_usua_login']);
		$historialAccion->SetIdPerfil($row['historial_accion_perfil_id']);
		$historialAccion->SetIdAccion($row['historial_accion_accion_id']);
		$historialAccion->SetIdDependencia($row['historial_accion_dependencia_id']);
		$historialAccion->SetDescripcion($row['historial_accion_descripcion']);
		$historialAccion->SetFirma($row['historial_accion_firma']);
		
		return $historialAccion; 
	}
}

?>