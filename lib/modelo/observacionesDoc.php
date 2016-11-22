<?php

class SafiModeloObservacionesDoc
{
	public static function InsertarObservacionesDoc(array $params = null)
	{
		
		if($params['fechaEmision'] != null && $params['fechaEmision'] != "" )
			$fechaEmision = $params['fechaEmision'];
		else
			$fechaEmision = date("d/m/Y H:i:s");
		
		if($params['login'] != null && $params['login'] != "" )
			$login = $params['login'];
		else
			$login = $_SESSION['login'];
		
		
	/*Caso particular de punto de cuenta*/	
	if (strlen($params['idPcta'])   >  10)
		$params['id'] = $params['idPcta'];
	/*Fin de caso particular*/
			$query = "
				INSERT INTO
					safi_observaciones_doc
					(
						id_doc,
						fecha,
						perfil,
						observacion,
						opcion,
						usua_login
					)
				    VALUES
					(
						'".$params['id']."',
						TO_TIMESTAMP('".$fechaEmision."', 'DD/MM/YYYY HH24:MI:SS'),
						'".$params['perfil']."',
						'".$params['observacion']."',
						'".$params['opcion']."',
						 '".$login."'
					
					)
			";
			
			if($GLOBALS['SafiClassDb']->Query($query) === false){
				echo 'Error al guardar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg();
				return false;
			} else {
				return true;
			}
	
		
		return false;
		
	
	}
	
	public static function InsertarObservacionesDocDesi(array $params = null)
	{
	
	
		/*Caso particular de punto de cuenta*/
		if (strlen($params['idPcta'])   >  10)
			$params['id'] = $params['idPcta'];
		/*Fin de caso particular*/
		$query = "
				INSERT INTO
					safi_observaciones_doc
					(
						id_doc,
						fecha,
						perfil,
						observacion,
						opcion
					)
				    VALUES
					(
						'".$params['id']."',
						now(),
						'".$params['perfil']."',
						'".$params['observacion']."',
						'".$params['opcion']."'
			
					)
			";
			
			
		if($GLOBALS['SafiClassDb']->Query($query) === false){
			echo 'Error al guardar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg() . 'query ' . $query ;
			return false;
		} else {
			return true;
		}
	
	
		return false;
	
	
	}
	
  public static function GetObservacionesDoc($params = null)
	{
		
		if($params === null)
				throw new Exception($preMsg."El parámetro \"params\" es nulo.");
			if(!is_array($params))
				throw new Exception($preMsg."El parámetro \"params\" no es un arreglo.");
			if(count($params) == 0)
				throw new Exception($preMsg."El parámetro \"params\" está vacío.");
	
      $data  = array();
       
	   $query = "
	   SELECT 
	         *
       FROM  
           safi_observaciones_doc
           
       WHERE 
           id_doc  IN ('".implode("', '",$params)."')";
	   
	   
	   //error_log(print_r($query,true));

		if($result = $GLOBALS['SafiClassDb']->Query($query)){
		while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

        $data[] = $row;
       
		}

         return $data;
		
		}
          
        return false;
	}
	
	public static function GetObservacionesDocrs($params = null)
	{
	
		if($params === null)
			throw new Exception($preMsg."El parámetro \"params\" es nulo.");
		if(!is_array($params))
			throw new Exception($preMsg."El parámetro \"params\" no es un arreglo.");
		if(count($params) == 0)
			throw new Exception($preMsg."El parámetro \"params\" está vacío.");
	
		$data  = array();
		 
		$query = "
	   SELECT
	         *
       FROM
           safi_observaciones_doc
      
       WHERE
           id_doc  IN ('".implode("', '",$params)."')";
	
	
		//error_log(print_r($query,true));
	
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
	
				$data[] = array_map('utf8_encode', $row); //array_map devuelve un array que contiene todos los elementos de "row" después de haber aplicado la función callback a cada uno de ellos
				 
			}
	
			return $data;
	
		}
	
		return false;
	}
	
	
 public static function GetObservacionesDocDevueltoFinalizado($params = null)
	{
	
		if($params === null)
			throw new Exception($preMsg."El parámetro \"params\" es nulo.");
			
		if(count($params) == 0)
			throw new Exception($preMsg."El parámetro \"params\" está vacío.");
	
    
	   $query = "
	   SELECT 
	         id_doc
       FROM  
           safi_observaciones_doc
           
       WHERE 
          opcion = '1' AND
	      id_doc = '".$params."'";
	   
		$result = $GLOBALS['SafiClassDb']->Query($query);
		if($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

       return  true;
       
		}

        return false;
	}
	
	public static function GetObservacionesDocSiguienteHoraCodi($idDoc = null)
	{
		try
		{
			if($idDoc === null)
				throw new Exception($preMsg."El parámetro \"idDoc\" es nulo.");
			if(count($idDoc) == 0)
				throw new Exception($idDoc."El parámetro \"idDoc\" está vacío.");
			 
			$query = "
				SELECT
					TO_CHAR((max_fecha.fecha + '1m'::interval), 'HH24:MI:SS') AS hora
				FROM
					(
						SELECT
							MAX(fecha) AS fecha
						FROM
							(
								(
									SELECT
										comp_id AS comp_id,
										comp_fec_emis AS fecha
									FROM
										sai_comp_diario
									WHERE
										comp_id = '".$idDoc."'
									LIMIT 
										1
								)
								UNION ALL
								(
									SELECT
										id_doc AS comp_id,
										fecha AS fecha
									FROM
										safi_observaciones_doc
									WHERE
										id_doc = '".$idDoc."'
									ORDER BY
										fecha DESC
									LIMIT
										1
								)
							) AS all_fechas
					) AS max_fecha
			";
		
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al consultar la última fecha de las observacions de codi. Detalles: " .
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			if($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				return $row['hora'];
			}
			
			return null;
			
		} catch (Exception $e){
			error_log($e, 0);
			return false;
		}
	}
	
	
	
}