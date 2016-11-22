<?php
include_once(SAFI_ENTIDADES_PATH . '/compromisoAsunto.php');

class SafiModeloCompromisoAsunto {

	public static function GetAsuntosEstasIds(array $params = null,$nombre){

		try {
				
				
			if($params === null)
			throw new Exception($preMsg."El parámetro \"params\" es nulo.");
			if(!is_array($params))
			throw new Exception($preMsg."El parámetro \"params\" no es un arreglo.");
			if(count($params) == 0)
			throw new Exception($preMsg."El parámetro \"params\" está vacío.");

			$params2 = array();

			$query = "

								SELECT 
								      * 
								       
								FROM 
								     sai_compromiso_asunt 
								     
								where 
								     esta_id IN ('".implode("', '", $params)."') AND 
								     lower(cpas_nombre) LIKE '%".utf8_decode(mb_strtolower($nombre))."%' 

								ORDER BY cpas_nombre";
				


			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
				throw new Exception("Error GetAsuntosEstasIds . Detalles: ".
				utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			}
				
			while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$params2[$row['cpas_id']] = self::LLenarAsunto($row);
			}

				
			return $params2;

		} catch (Exception $e) {
			error_log($e, 0);
			return false;
		}

	}

	public static function GetAsusntosId($params = null)
	{

		$query = "
			SELECT  
			    * 
			    
			FROM  
			   sai_compromiso_asunt
			   
		    WHERE 
		       cpas_id IN ('".implode("', '",$params)."')
		       
		   ORDER BY cpas_nombre ASC
		";

			
		$result = $GLOBALS['SafiClassDb']->Query($query);
			

		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

			$asunto = self::LlenarAsunto($row);
			$asuntos[$row['cpas_id']] = $asunto;
				
		}

		return $asuntos;
	}



		
	private static function LLenarAsunto($row){
			
		$asunto = new EntidadCompromisoAsunto();
		$asunto->SetId($row['cpas_id']);
		$asunto->SetNombre($row['cpas_nombre']);

		return $asunto;
			

	}


}