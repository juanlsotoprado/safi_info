<?php
include_once(SAFI_ENTIDADES_PATH . "/tipoSolicitudPago.php");

class SafiModeloTipoSolicitudPago
{
	public static function GetTipoSolicitudPagoById($idTipoSolicitudPago){
		
		$tipoSolicitudPago = null;
		
		$query = "
			SELECT
				id_sol,
				nombre_sol,
				esta_id
			FROM
				sai_tipo_solicitud
			WHERE
				id_sol = '".$idTipoSolicitudPago."'
				AND esta_id != 15		
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) !== false) {
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$tipoSolicitudPago = self::LlenarTipoSolicitudPago($row);
			}
		}
		
		return $tipoSolicitudPago;
	}
	
	
	public static function GetTipoSolpagos($nombre){
	  	
						try {
							
							$params2 =array();
							
							$query = "

									SELECT
		                        		id_sol,
										upper(nombre_sol) as nombre_sol,
										esta_id
										
									FROM
										sai_tipo_solicitud
										
								   where ";
								 
								  $query .= "   upper(nombre_sol)  LIKE '%".strtoupper($nombre)."%'
								  ORDER BY nombre_sol ";
					  

							if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
							throw new Exception("Error GetSolpagos . Detalles: ".
							utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
								
							}
							
							while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
								$params2[$row['id_sol']] = self::LlenarTipoSolicitudPago($row);
							}
	

				        return $params2;
	
						} catch (Exception $e) {
							error_log($e, 0);
							return false;
						}

					}		
	
	
	
	public static function GetAllTipoSolicitudPago()
	{
		$query = "
			SELECT
				id_sol,
				nombre_sol,
				esta_id
			FROM
				sai_tipo_solicitud
			ORDER BY nombre_sol	
				
		";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) !== false) {
				$tipoSolicitudPagos = array();
				while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$tipoSolicitudPago = self::LlenarTipoSolicitudPago($row);
					$tipoSolicitudPagos[$tipoSolicitudPago->GetId()] = $tipoSolicitudPago;
				}
			}
			
		
		return $tipoSolicitudPagos;
	}
	
	private static function LlenarTipoSolicitudPago($row)
	{
		$tipoSolicitudPago = new EntidadTipoSolicitudPago();
		
		$tipoSolicitudPago->SetId($row['id_sol']);
		$tipoSolicitudPago->SetNombre($row['nombre_sol']);
		$tipoSolicitudPago->SetIdEstatus($row['esta_id']);
		
	/*	if($row['multiple_comp']){
			
			$tipoSolicitudPago->SetIdMultComp($row['multiple_comp']);
			
		}*/
		
		
		
		return $tipoSolicitudPago;
	}
}