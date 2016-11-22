<?php
class SafiModeloCiudad
{
	public static function GetCiudadesActivasByEstado($idEstado){
		$ciudades = array();
		
		$query = "
			SELECT
				id,
				nombre,
				edo_id,
				estatus_actividad
			FROM
				safi_ciudad
			WHERE
				edo_id = " . $idEstado . " AND
				estatus_actividad = '1'
			ORDER BY
				nombre
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$ciudades[$row['id']] = $row;
		}
		
		return $ciudades;
	}
}