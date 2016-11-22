<?php

// Entidades
require_once(SAFI_ENTIDADES_PATH.'/tipotransporte.php');

class SafiModeloTipoTransporte
{
	public static function GetTipoTransportesActivos()
	{
		$tipoTransportes = array();
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		$query = "
			SELECT
				id,
				tipo,
				nombre,
				estatus_actividad
			FROM
				safi_tipo_transporte
			WHERE
				estatus_actividad = '1'
			ORDER BY
				nombre
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$tipoTransportes[$row['id']] = $row;
		}
		
		return $tipoTransportes;
	}
}