<?php
include_once(SAFI_ENTIDADES_PATH . '/categoriaviatico.php');

class SafiModeloCategoriaViatico
{
	public static function GetAllCategoriasActivas()
	{
		$categorias = array();
		
		$query = "
			SELECT
				id,
				nombre,
				descripcion,
				estatus_actividad
			FROM
				safi_categoria_viatico
			WHERE
				estatus_actividad = '1'
			ORDER BY
				nombre
		";
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$categoria = new EntidadCategoriaViatico();
				$categoria->SetId($row['id']);
				$categoria->SetNombre($row['nombre']);
				$categoria->SetDescripcion($row['descripcion']);
				$categoria->SetEstatusActividad($row['estatus_actividad']);
				
				$categorias[] = $categoria;
			}
		}
		
		return $categorias;
	}
}