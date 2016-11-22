<?php
include_once(SAFI_ENTIDADES_PATH . "/tipoActividadCompromiso.php");
include_once(SAFI_ENTIDADES_PATH . "/tipoSolicitudPago.php");

class SafiModeloTipoActividadCompromiso
{
	
	 public static function GetActividadIds(array $params = null){
	  	
				 	
						try {
							
							$params2 =array();
							
							if($params === null)
								throw new Exception($preMsg."El parámetro \"params\" es nulo.");
							if(!is_array($params))
								throw new Exception($preMsg."El parámetro \"params\" no es un arreglo.");
							if(count($params) == 0)
								throw new Exception($preMsg."El parámetro \"params\" está vacío.");
		

							$query = "

								SELECT 
								      * 
								       
								FROM 
								     sai_tipo_actividad 
								     
								where 
								     id IN ('".implode("', '", $params)."') 

								ORDER BY nombre";
							

							if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
							throw new Exception("Error GetActividadEstasIds . Detalles: ".
							utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
								
							}
							
							while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
								$params2[$row['id']] = self::LlenarTipoActividadCompromiso($row);
							}
		
				        return $params2;
	
						} catch (Exception $e) {
							error_log($e, 0);
							return false;
						}

					}		
	  public static function GetActividadEstasIds(array $params = null,$nombre){
	  	
				 	
						try {
							
							$params2 =array();
							
							if($params === null)
								throw new Exception($preMsg."El parámetro \"params\" es nulo.");
							if(!is_array($params))
								throw new Exception($preMsg."El parámetro \"params\" no es un arreglo.");
							if(count($params) == 0)
								throw new Exception($preMsg."El parámetro \"params\" está vacío.");
		

							$query = "

								SELECT 
								      * 
								       
								FROM 
								     sai_tipo_actividad 
								     
								where 
								     esta_id IN ('".implode("', '", $params)."') AND 
								     nombre  LIKE '%".$nombre."%' 

								ORDER BY nombre";
							

							if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
							throw new Exception("Error GetActividadEstasIds . Detalles: ".
							utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
								
							}
							
							while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
								$params2[$row['id']] = self::LlenarTipoActividadCompromiso($row);
							}
		
				        return $params2;
	
						} catch (Exception $e) {
							error_log($e, 0);
							return false;
						}

					}		
	
	
	
	public static function GetTipoActividadCompromisoById($idTipoActividadCompromiso){
		
		$tipoActividadCompromiso = null;
		
		$query = "
			SELECT
				id,
				nombre,
				esta_id
			FROM
				sai_tipo_actividad
			WHERE
				id = '".$idTipoActividadCompromiso."'
				AND esta_id != 15		
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) !== false) {
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$tipoActividadCompromiso = self::LlenarTipoActividadCompromiso($row);
			}
		}
		
		return $tipoActividadCompromiso;
	}
	
	public static function GetAllTipoActividadCompromiso()
	{
		$query = "
			SELECT
				id,
				nombre,
				esta_id
			FROM
				sai_tipo_actividad
			ORDER BY nombre		
		";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) !== false) {
				$tipoActividadCompromisos = array();
				while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					
					$tipoActividadCompromiso = self::LlenarTipoActividadCompromiso($row);
					$tipoActividadCompromisos[$tipoActividadCompromiso->GetId()] = $tipoActividadCompromiso;
				}
			}
			
		
		return $tipoActividadCompromisos;
	}
	
	private static function LlenarTipoActividadCompromiso($row)
	{
		$tipoActividadCompromiso = new EntidadTipoSolicitudPago();
		
		$tipoActividadCompromiso->SetId($row['id']);
		$tipoActividadCompromiso->SetNombre($row['nombre']);
		$tipoActividadCompromiso->SetIdEstatus($row['esta_id']);
		
		return $tipoActividadCompromiso;
	}
}