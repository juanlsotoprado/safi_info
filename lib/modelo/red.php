<?php
include_once(SAFI_ENTIDADES_PATH . '/red.php');

class SafiModeloRed
{
	public static function GetAllRedesActivas()
	{
		$redes = array();
		
		$query = "
			SELECT
				id,
				nombre,
				estatus_actividad
			FROM
				safi_red
			WHERE
				estatus_actividad = '1'
		";
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$red = new EntidadCategoriaViatico();
				$red->SetId($row['id']);
				$red->SetNombre($row['nombre']);
				$red->SetEstatusActividad($row['estatus_actividad']);
				
				$redes[] = $red;
			}
		}
		
		return $redes;
	}
}