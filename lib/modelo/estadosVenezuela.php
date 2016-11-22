<?php
include_once(SAFI_ENTIDADES_PATH . '/estadosVenezuela.php');

class SafiModeloEstadosVenezuela {
	
		
	public static function GetEstadosVenezuelaId(array $params = null){
		
		
		if($params === null)
			throw new Exception($preMsg."El parámetro \"params\" es nulo.");
		if(!is_array($params))
			throw new Exception($preMsg."El parámetro \"params\" no es un arreglo.");
		if(count($params) == 0)
			throw new Exception($preMsg."El parámetro \"params\" está vacío.");

				 	
		try {
				
			$params2 = array();

			$query = "
				SELECT 
					* 
				FROM 
					safi_edos_venezuela      
				WHERE 
					id IN ('".implode("', '", $params)."')
				ORDER BY
					nombre
			";
 

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
				throw new Exception("Error GetEstadosVenezuelaId . Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			}
					
			while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$params2[$row['id']] = self::LLenarEstadosVenezuela($row);
			}
				
	        return $params2;	

		} catch (Exception $e) {
			error_log($e, 0);
			return false;
		}
	}	
	
	
	public static function GetEstadosVenezuela(array $params = null){
				 	
						try {
							
								$params2 = array();

							$query = "

								SELECT 
								      * 
								       
								FROM 
								     safi_edos_venezuela 
								     
								where 
								     estatus_actividad IN ('".implode("', '", $params)."')

								ORDER BY nombre";
			 

							if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
							throw new Exception("Error GetAsuntosEstasIds . Detalles: ".
							utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
								
							}
							
							while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
								$params2[$row['id']] = self::LLenarEstadosVenezuela($row);
							}

							
				        return $params2;	
	
						} catch (Exception $e) {
							error_log($e, 0);
							return false;
						}

					}	

					
					
					
		private static function LLenarEstadosVenezuela($row){
			
		$estadosVenezuela = new EntidadEstadosVenezuela();
		$estadosVenezuela->SetId($row['id']);
		$estadosVenezuela->SetNombre($row['nombre']);

			return $estadosVenezuela;
							

					}		
	

}