<?php
require_once(SAFI_ENTIDADES_PATH . '/regionReporte.php');

class SafiModeloRegionReporte
{
	public static function GetAllRegionReportes()
	{
		try
		{
			$preMsg = "Error al intentar obtener todas las regionReportes.";
			$regionReportes = null;
			
			$query = "
				SELECT
					".self::GetSelectFieldsRegionReporte()."
				FROM
					safi_region_reporte region_reporte
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg. "Detalles: " . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$regionReportes = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$regionReportes[$row['region_reporte_id']] = self::LlenarRegionReporte($row);
			}
			
			return $regionReportes;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
		}
		return null;
	}
	
	public static function GetSelectFieldsRegionReporte()
	{
		return "
			region_reporte.id AS region_reporte_id,
			region_reporte.nombre AS region_reporte_nombre
		";
	}
	
	public static function LlenarRegionReporte($row)
	{
		$regionReporte = new EntidadRegionReporte();
		
		$regionReporte->SetId($row['region_reporte_id']);
		$regionReporte->SetNombre($row['region_reporte_nombre']);
		
		return $regionReporte;
	}
}