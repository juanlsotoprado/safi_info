<?php

class SafiModeloWFOpcion
{
	public static function GetWFOpciones(array $params = null)
	{
	try
		{
			$preMsg = "Error al intentar obtener WFOpciones.";
			$arrMsg = array();
			$queryWhere = "";
			$existeCriterio = false;
			$wFOpciones = null;
			
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
				
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
				
			if(count($params) == 0)
				throw new Exception($preMsg." El parámetro params está vacío.");
				
			if(!isset($params['idWFOpciones']))
				$arrMsg[] = "El parámetro params['idWFOpciones'] no pudo ser encontrado.";
			else if(($wFOpciones=$params['idWFOpciones']) == null)
				$arrMsg[] = "El parámetro params['idWFOpciones'] es nulo.";
			else if(!is_array($wFOpciones))
				$arrMsg[] = "El parámetro params['idWFOpciones'] está vacío.";
			else if(count($wFOpciones) == 0)
				$arrMsg[] = "El parámetro params['idWFOpciones'] está vacío.";
			else {
				$existeCriterio = true;
				$queryWhere = " wfopcion.wfop_id IN ('".implode("', '", $wFOpciones)."')";
			}
			
			if(!$existeCriterio)
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			
			$query = "
				SELECT
					wfopcion.wfop_id AS wfopcion_id,
					wfopcion.wfop_nombre AS wfopcion_nombre,
					wfopcion.wfop_descrip AS wfopcion_descripcion
				FROM
					sai_wfopcion wfopcion
				WHERE
					".$queryWhere."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ".($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$wFOpciones = array();	
				
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$wFOpciones[$row['wfopcion_id']] = self::LlenarWFOpcion($row);
			}
			
			return $wFOpciones;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	public static function LlenarWFOpcion($row)
	{
		$wFOpcion = new EntidadWFOpcion();
		
		$wFOpcion->SetId($row['wfopcion_id']);
		$wFOpcion->SetNombre($row['wfopcion_nombre']);
		$wFOpcion->SetDescripcion($row['wfopcion_descripcion']);
		
		return $wFOpcion;
	}	
}