<?php
include_once(SAFI_ENTIDADES_PATH . '/accioncentralizadaespecifica.php');

class SafiModeloAccionCentralizadaEspecifica
{
	public static function GetAccionCentralizadaEspecificaById($id, $idAccionCentralizada, $anho){
		$accionEspcifica = null;
		
		if($id != null && $id != '' && $idAccionCentralizada != null && $idAccionCentralizada != '' && $anho != null && $anho != ''){
			
			$query = "
				SELECT
					ace.aces_id,
					ace.acce_id,
					ace.pres_anno,
					ace.aces_nombre,
					ace.centro_gestor,
					ace.centro_costo
				FROM
					sai_acce_esp ace
				WHERE
					ace.aces_id = '".$id."' AND
					ace.acce_id = '".$idAccionCentralizada."' AND
					ace.pres_anno = '".$anho."'
			";
			
			if($result = $GLOBALS['SafiClassDb']->Query($query)){
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					
					$accionEspcifica = new EntidadAccionCentralizadaEspecifica();
					
					$accionEspcifica->SetId($row['aces_id']);
					$accionEspcifica->SetIdAccionCentralizada($row['acce_id']);
					$accionEspcifica->SetAnho($row['pres_anno']);
					$accionEspcifica->SetNombre($row['aces_nombre']);
					$accionEspcifica->SetCentroGestor($row['centro_gestor']);
					$accionEspcifica->SetCentroCosto($row['centro_costo']);
				}
			}
		}
		
		return $accionEspcifica;
	}
}