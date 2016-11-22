<?php

class SafiModeloParroquia
{
	public static function GetAllParroquias()
	{
		$parroquias = array();
		
		$query = "
			SELECT
				id,
  				nombre,
  				municipio_id,
  				estatus_actividad
			FROM
				safi_parroquia
			ORDER BY
				nombre
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$parroquias[$row['id']] = $row;
		}
		
		return $parroquias;
	}
	
	public static function GetParroquiasByMunicipio($idMunicipio)
	{
		$parroquias = array();
		
		$query = "
			SELECT
				id,
  				nombre,
  				municipio_id,
  				estatus_actividad
			FROM
				safi_parroquia
			WHERE
				municipio_id = " . $idMunicipio . "
			ORDER BY
				nombre
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$parroquias[$row['id']] = $row;
		}
		
		return $parroquias;
	}
	
}