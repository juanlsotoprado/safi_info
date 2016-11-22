<?php
require_once(SAFI_MODELO_PATH . '/regionReporte.php');

class SafiModeloRegionReporteEstado
{
	public static function GetAllRegionReporteEstadoListByIdEstados()
	{
		try
		{
			$preMsg = "Error al intentar obtener todas las regionReporteEstados listadas por id de estados.";
			$arrRegionesPorEstado = null;
			
			$query =  "
				SELECT
					".self::GetSelectFieldsRegionReporteEstado().",
					".SafiModeloRegionReporte::GetSelectFieldsRegionReporte()."
				FROM
					safi_region_reporte_estado region_reporte_estado
					INNER JOIN safi_region_reporte region_reporte ON (region_reporte.id = region_reporte_estado.region_id)
				ORDER BY
					region_reporte_estado.edo_id
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg. "Detalles: " . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$regionReportes = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$arrRegionesPorEstado [$row['region_reporte_estado__estado_id']] = SafiModeloRegionReporte::LlenarRegionReporte($row); 
			}
			
			return $arrRegionesPorEstado;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
		}
		return null;
	}
	
	private static function GetSelectFieldsRegionReporteEstado()
	{
		return "
			region_reporte_estado.region_id AS region_reporte_estado__region_reporte_id,
			region_reporte_estado.edo_id AS region_reporte_estado__estado_id
		";
	}
}

?>