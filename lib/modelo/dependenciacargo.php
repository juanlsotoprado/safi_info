<?php
include_once(SAFI_ENTIDADES_PATH . '/dependenciacargo.php');

class SafiModeloDependenciaCargo
{
	public static function GetSiguienteCargoDeCadena($idDependencia, $cargos)
	{
		include_once(SAFI_ENTIDADES_PATH . '/cargo.php');
		$cargo = null;

		
		$idCargos = array();
		if(is_array($cargos)){
			foreach($cargos as $cargo){
				if($cargo instanceof EntidadCargo){
					$idCargos[] = "'" . $cargo->GetId() . "'";
				}
			}
		}

		if(count($idCargos) > 0){
			$query = "
				SELECT
					substring(dc.carg_id from 1 for 2)||dc.depe_id AS perfil
				FROM
					sai_depen_cargo dc
				WHERE
					dc.depe_id = '".$idDependencia."' AND
					dc.carg_id IN (".implode(',', $idCargos).")
			";
			
			if($result=$GLOBALS['SafiClassDb']->Query($query)){
			
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$cargo = new EntidadCargo();
	
					$cargo->SetId($row['perfil']);
				}
			}
		}
		
		return $cargo;
	}
	
}