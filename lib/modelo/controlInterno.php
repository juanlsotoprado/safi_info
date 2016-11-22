<?php
include_once(SAFI_ENTIDADES_PATH . '/controlinterno.php');

class SafiModeloControlinterno {

	public static function GetcontrolInternos(){

		try {
				
				
			$params2 = array();

			$query = "

								SELECT 
								     * 
								FROM 
								    sai_control_comp 
								WHERE esta_id = 1    
								ORDER BY nombre
								";
				


			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
				throw new Exception("Error GetAsuntosEstasIds . Detalles: ".
				utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			}
				
			while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$params2[$row['id']] = self::LLenarControlInterno($row);
			}
				
			return $params2;
			

		} catch (Exception $e) {
			error_log($e, 0);
			return false;
		}

	}
	
	
   public static function GetcontrolInternosIds($params = null)
	{

		$query = "
			
			SELECT 
				* 
			FROM 
				sai_control_comp 
				
		    WHERE 
		       id IN ('".implode("', '",$params)."')
		";

			
		$result = $GLOBALS['SafiClassDb']->Query($query);
			

		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

			$control = self::LLenarControlInterno($row);
			$controlinterno[$row['id']] = $control;
				
		}

		return $controlinterno;
	}
	

	private static function LLenarControlInterno($row){
			
		$control = new EntidadControlinterno();
		$control->SetId($row['id']);
		$control->SetNombre($row['nombre']);

		return $control;
			

	}


}