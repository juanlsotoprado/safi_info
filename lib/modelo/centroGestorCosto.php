<?php

class SafiModeloCentroGestorCosto
{
	public static function GetAllCentroGestorCosto($anho){
		$listaCentroGestorCostos = null;
		
		if($anho != null && $anho != ''){
			$listaCentroGestorCostos = array();
			$query = "
				SELECT 
					spae.centro_gestor,
					spae.centro_costo
				FROM sai_proyecto sp, sai_proy_a_esp spae 
				WHERE 
					sp.pre_anno = spae.pres_anno AND 
					sp.proy_id = spae.proy_id AND 
					sp.pre_anno = ".$anho."
				UNION 
				SELECT
					sae.centro_gestor,
					sae.centro_costo
				FROM sai_ac_central sac, sai_acce_esp sae 
				WHERE 
					sac.pres_anno = sae.pres_anno AND 
					sac.acce_id = sae.acce_id AND 
					sac.pres_anno = ".$anho."
				ORDER BY centro_gestor,centro_costo
			";
			
			if($result = $GLOBALS['SafiClassDb']->Query($query)){
				while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$listaCentroGestorCostos[] = $row['centro_gestor']."/".$row['centro_costo'];
				}
			}
		}
		
		return $listaCentroGestorCostos;
	}
}