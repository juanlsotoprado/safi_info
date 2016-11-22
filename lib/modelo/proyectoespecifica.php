<?php
include_once(SAFI_ENTIDADES_PATH . '/proyectoespecifica.php');

class SafiModeloProyectoEspecifica
{
	public static function GetProyectoEspecificaById($id, $idProyecto, $anho){
		$accionEspcifica = null;
		
		if($id != null && $id != '' && $idProyecto != null && $idProyecto != '' && $anho != null && $anho != ''){
			
			$query = "
				SELECT
					pe.paes_id,
					pe.proy_id,
					pe.pres_anno,
					pe.paes_nombre,
					pe.centro_gestor,
					pe.centro_costo
				FROM
					sai_proy_a_esp pe
				WHERE
					pe.paes_id = '".$id."' AND
					pe.proy_id = '".$idProyecto."' AND
					pe.pres_anno = '".$anho."'
			";
			
			if($result = $GLOBALS['SafiClassDb']->Query($query)){
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					
					$accionEspcifica = new EntidadProyectoEspecifica();
					
					$accionEspcifica->SetId($row['paes_id']);
					$accionEspcifica->SetIdProyecto($row['proy_id']);
					$accionEspcifica->SetAnho($row['pres_anno']);
					$accionEspcifica->SetNombre($row['paes_nombre']);
					$accionEspcifica->SetCentroGestor($row['centro_gestor']);
					$accionEspcifica->SetCentroCosto($row['centro_costo']);
				}
			}
		}
		
		return $accionEspcifica;
	}
}