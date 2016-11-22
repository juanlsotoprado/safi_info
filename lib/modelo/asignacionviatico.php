<?php
include_once(SAFI_ENTIDADES_PATH . '/asignacionviatico.php');

class SafiModeloAsignacionViatico
{	
	public static function GetAsignaciones()
	{
		$asignaciones = array();
		$query = "
			SELECT
				id,
				codigo,
				nombre,
				fecha_inicio,
				fecha_fin,
				tipo,
				observacion,
				unidad_medida,
				monto_fijo,
				estatus_actividad,
				ordenacion_tipo,
				ordenacion_global
			FROM
				safi_asignacion_viatico
			ORDER BY
				ordenacion_global
		";

		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$asignacion = self::ArrayToObject($row);
			$asignaciones[$asignacion->GetId()] = $asignacion;
		}
		
		return $asignaciones;
	}
	
	public static function GetAsignacionesFijas()
	{
		$asignaciones = array();
		$query = "
			SELECT
				id,
				codigo,
				nombre,
				fecha_inicio,
				fecha_fin,
				tipo,
				observacion,
				unidad_medida,
				monto_fijo,
				estatus_actividad,
				ordenacion_tipo,
				ordenacion_global
			FROM
				safi_asignacion_viatico
			WHERE
				tipo = " . EntidadAsignacionViatico::TIPO_FIJO . "
			ORDER BY
				ordenacion_tipo
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$asignacion = self::ArrayToObject($row);
			$asignaciones[$asignacion->GetId()] = $asignacion;
		}
		
		return $asignaciones;
	}
	
	public static function GetAsignacionesVariables()
	{
		$asignaciones = array();
		$query = "
			SELECT
				id,
				codigo,
				nombre,
				fecha_inicio,
				fecha_fin,
				tipo,
				observacion,
				unidad_medida,
				monto_fijo,
				estatus_actividad,
				ordenacion_tipo,
				ordenacion_global
			FROM
				safi_asignacion_viatico
			WHERE
				tipo = " . EntidadAsignacionViatico::TIPO_VARIABLE . "
			ORDER BY
				ordenacion_tipo
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$asignacion = self::ArrayToObject($row);
			$asignaciones[$asignacion->GetId()] = $asignacion;
		}
		
		return $asignaciones;
	}
	
	private static function ArrayToObject($row){
		$asignacion = new EntidadAsignacionViatico();
		$asignacion->SetId($row['id']);
		$asignacion->SetCodigo($row['codigo']);
		$asignacion->SetNombre($row['nombre']);
		$asignacion->SetFechaInicio($row['fecha_inicio']);
		$asignacion->SetFechaFin($row['fecha_fin']);
		$asignacion->SetTipo(($row['tipo']));
		$asignacion->SetObservacion($row['observaion']);
		$asignacion->SetUnidadMedida($row['unidad_medida']);
		$asignacion->SetMontoFijo($row['monto_fijo']);
		$asignacion->SetEstatusActividad($row['estatus_actividad']);
		$asignacion->SetOrdenacionTipo($row['ordenacion_tipo']);
		$asignacion->SetordenacionGlobal($row['ordenacion_global']);
		
		return $asignacion;
	}
}