<?php
include_once(SAFI_ENTIDADES_PATH . '/estatus.php');

class SafiModeloEstatus
{
	public static function GetAllEstatus()
	{
		$estatusList = null;
		
		try {
			
			$query = "
				SELECT
					esta_id,
					esta_nombre,
					esta_descripcion,
					usua_login
				FROM
					sai_estado
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al obtener todos los estatus. Detalles: ". 
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$estatusList = array();
					
			while($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$estatusList[$row['esta_id']] = self::LlenarEstatus($row);
			}
			
			return $estatusList;
			
		} catch (Exception $e) {
			error_log($e, 0);
		}
		
		return $estatusList;
	}

	public static function GetEstatusCheques(){
		$estatusList = null;
	
		$query = "
					SELECT DISTINCT (e.esta_nombre) AS esta_nombre, 
						e.esta_id AS esta_id, '' AS esta_descripcion, '' AS usua_login
					FROM sai_cheque_estados ce
					INNER JOIN sai_estado e ON (ce.estatus_cheque = e.esta_id)
				";
		//echo $query;
		$estatusList = array();		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				//echo $query;
				$estatusList[] = self::LlenarEstatus($row);
			}
		}
	
		return $estatusList;
	}
	
	
	
	
   public static function GetEstadoPctaIdPcuenta($idEstado){
		$estado = null;
		
		$query = "
			SELECT
					esta_id,
					esta_nombre,
					esta_descripcion,
					usua_login
				FROM
					sai_estado
			WHERE
				esta_id IN ('".implode("', '",$idEstado)."')";

		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				
				$estado[$row['esta_id']] = self::LlenarEstatus($row);
				
			}

		}
	
		return $estado;
	}
	
	
	
	
	
	
	
	
	public static function GetEstadoPcta()
	{
	
	
		$query="SELECT
		            esta_id,
		            esta_nombre 
		            
		         FROM 
		            sai_estado
		            
		         WHERE 
		            esta_id='10' or 
		            esta_id='7' or
		            esta_id='13'
		            or esta_id='15'
		            
		        order by 
		            esta_nombre";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		if($result){
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$datos[] = $row;
		}
		
		
		
		return $datos;

	}else{
	
		return false;
	}
	
	}
	
	
	
	private static function LlenarEstatus($row)
	{
		
		$estatus = new EntidadEstatus();
		$estatus->SetId($row['esta_id']);
		$estatus->SetNombre($row['esta_nombre']);
		$estatus->SetDescripcion($row['esta_descripcion']);
		$estatus->SetUsuaLogin($row['usua_login']);
	
		return $estatus;
	}
}