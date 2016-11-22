<?php
include_once (SAFI_ENTIDADES_PATH . "/municipio.php");

class SafiModeloMunicipio
{
	public static function GetAllMunicipios()
	{
		$municipios = array();
		
		$query = "
			SELECT
				id,
  				nombre,
  				edo_id,
  				estatus_actividad
			FROM
				safi_municipio
			ORDER BY
				nombre
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$municipios[$row['id']] = $row;
		}
		
		return $municipios; 
	}
	
	public static function GetMunicipiosByEstado($idEstado)
	{
		$municipios = array();
		
		$query = "
			SELECT
				id,
  				nombre,
  				edo_id,
  				estatus_actividad
			FROM
				safi_municipio
			WHERE
				edo_id = " . $idEstado . "
			ORDER BY
				nombre
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$municipios[$row['id']] = $row;
		}
		
		return $municipios;
	}
	
	public static function GetMunicipiosByEstado2($idEstado)
	{
		$municipios = array();
		
		$query = "
			SELECT
				municipio.id AS municipio_id,
  				municipio.nombre AS municipio_nombre,
  				municipio.edo_id AS municipio_estado_id,
  				municipio.estatus_actividad AS municipio_estatus_actividad
			FROM
				safi_municipio municipio
			WHERE
				municipio.edo_id = " . $idEstado . "
			ORDER BY
				municipio.nombre
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$municipios[$row['municipio_id']] = self::LlenarMunicipio($row);
		}
		
		return $municipios;
	}
	
	private static function LlenarMunicipio($row)
	{
		$municipio = new EntidadMunicipio();
		
		$estado = new EntidadEstado();
		$estado->SetId($row['municipio_estado_id']);
	
		$municipio->SetId($row['municipio_id']);
		$municipio->SetNombre($row['municipio_nombre']);
		$municipio->SetIdEstado($row['municipio_estado_id']);
		$municipio->SetEstatusActividad($row['municipio_estatus_actividad']);
		$municipio->SetEstado($estado);
		
		return $municipio;
	}
}
?>