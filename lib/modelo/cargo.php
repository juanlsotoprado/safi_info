<?php
include_once(SAFI_ENTIDADES_PATH . '/cargo.php');

class SafiModeloCargo
{
	public static function GetCargoByEmpleado($empleadoCedula){
		$cargo = null;
		
		$query = "
			SELECT
				c.carg_id,
				c.carg_nombre,
				c.carg_fundacion,
				c.carg_descrip,
				c.carg_nivel,
				c.esta_id,
				c.usua_login
			FROM
				sai_empleado e
				INNER JOIN sai_cargo c ON (c.carg_fundacion = e.carg_fundacion)
			WHERE
				e.empl_cedula = '".$empleadoCedula."'
		";
		
		
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) !== false) {
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$cargo = self::LlenarCargo($row);
			}
		}
		
		return $cargo;
	}
	
	public static function GetCargoByCargoFundaciones($cargoFundaciones)
	{
		$cargos = null;
		
		if(is_array($cargoFundaciones) && count($cargoFundaciones)>0){
			$query = "
				SELECT
					c.carg_id,
					c.carg_nombre,
					c.carg_fundacion,
					c.carg_descrip,
					c.carg_nivel,
					c.esta_id,
					c.usua_login
				FROM				
					sai_cargo c
				WHERE
					c.carg_fundacion IN (".implode(", ", $cargoFundaciones).")
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) !== false) {
				$cargos = array();
				while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$cargo = self::LlenarCargo($row);
					$cargos[$cargo->GetFundacion()] = $cargo;
				}
			}
		}
		
		return $cargos;
	}
	
	private static function LlenarCargo($row){
		$cargo = new EntidadCargo();
				
		$cargo->SetId($row['carg_id']);
		$cargo->SetNombre($row['carg_nombre']);
		$cargo->SetFundacion($row['carg_fundacion']);
		$cargo->SetDescripcion($row['carg_descrip']);
		$cargo->SetNivel($row['carg_nivel']);
		$cargo->SetIdEstatus($row['esta_id']);
		$cargo->SetUsuaLogin($row['usua_login']);
		
		return $cargo;
	}
}