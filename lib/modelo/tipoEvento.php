<?php
include_once(SAFI_ENTIDADES_PATH . '/tipoEvento.php');

class SafiModeloTipoEvento
{
	
	  public static function GetEventosIds(array $params = null){
	  	
				 	
						try {
							
							$params2 =array();
							
							$query = "

								SELECT 
								      * 
								       
								FROM 
								     sai_tipo_evento 
								     
								where ";
								
								
								  if($params){ $query .= "  id IN ('".implode("', '", $params)."') ";};
		

							if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
							throw new Exception("Error GetEventosEstasIds . Detalles: ".
							utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
								
							}
							
							while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
								$params2[$row['id']] = self::LlenarTipoEvento($row);
							}
	
				        return $params2;
	
						} catch (Exception $e) {
							error_log($e, 0);
							return false;
						}

					}		
					
					
	  public static function GetEventosEstasIds(array $params = null,$nombre){
	  	
				 	
						try {
							
							$params2 =array();
							
							$query = "

								SELECT 
								      * 
								       
								FROM 
								     sai_tipo_evento 
								     
								where ";
								
								
								  if($params){ $query .= "  esta_id IN ('".implode("', '", $params)."') AND ";};
								  $query .= "   nombre  LIKE '%".$nombre."%'
								  ORDER BY nombre ";
					  

							if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
							throw new Exception("Error GetEventosEstasIds . Detalles: ".
							utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
								
							}
							
							while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
								$params2[$row['id']] = self::LlenarTipoEvento($row);
							}
	
				        return $params2;
	
						} catch (Exception $e) {
							error_log($e, 0);
							return false;
						}

					}		
	
	
	
	public static function GetAllTipoEventos()
	{
		$tipoEventos = array();
		
		$query = "
			SELECT
				id,
  				nombre
			FROM
				sai_tipo_evento
			ORDER BY
				nombre
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$tipoEventos[$row['id']] = self::LlenarTipoEvento($row);
		}
		
		return $tipoEventos;
	}
	
	private static function LLenarTipoEvento($row){
		$tipoEvento = new EntidadTipoEvento();
		$tipoEvento->SetId($row['id']);
		$tipoEvento->SetNombre($row['nombre']);
		
		return $tipoEvento;
	}
}