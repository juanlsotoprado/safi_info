<?php
include_once(SAFI_ENTIDADES_PATH . "/viaticoresponsableasignacion.php");

class SafiModeloViaticoResponsableAsignacion
{
	public static function GetAsignacionesByIdViatico($idViaticoNacional)
	{
		$asignaciones = array();
		
		$asignacionesByIdsViaticos = self::GetAsignacionesByIdsViaticos(array($idViaticoNacional));
		
		if(is_array($asignacionesByIdsViaticos) && count($asignacionesByIdsViaticos) > 0)
		{
			$asignaciones = current($asignacionesByIdsViaticos);
		}
		
		return $asignaciones;
	}
	
	public static function GetAsignacionesByIdsViaticos($idsViaticosNacionales)
	{
		$asignacionesByIdsViaticos = array();
		
		if(!is_array($idsViaticosNacionales) || count($idsViaticosNacionales) == 0)
			return $asignacionesByIdsViaticos;
		
		$query = "
			SELECT
				vra.viatico_id,
				vra.responsable_id,
				vra.asignacion_viatico_id,
				vra.monto,
				vra.unidad_medida,
				vra.unidades,
				av.codigo
			FROM
				safi_viatico_responsable_asignacion vra
				INNER JOIN safi_asignacion_viatico av ON av.id = vra.asignacion_viatico_id
			WHERE
				vra.viatico_id IN ('".implode("', '", $idsViaticosNacionales)."')
			ORDER BY
				vra.viatico_id,
				av.ordenacion_global
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
		{
			$asignacion = new EntidadViaticoResponsableAsignacion();
			
			$asignacion->SetViaticoId($row['viatico_id']);
			$asignacion->SetResponsableId($row['responsable_id']);
			$asignacion->SetAsignacionViaticoId($row['asignacion_viatico_id']);
			$asignacion->SetMonto($row['monto']);
			$asignacion->SetUnidadMedida($row['unidad_medida']);
			$asignacion->SetUnidades($row['unidades']);
			
			$asignacionesByIdsViaticos[$row['viatico_id']][$row['codigo']] = $asignacion;
		}
		
		return $asignacionesByIdsViaticos;
	}
}