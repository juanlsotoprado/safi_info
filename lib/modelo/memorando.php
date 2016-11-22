<?php
class SafiModeloMemorando{
	public static function Search($key, $numItems){
		$memorandoAsuntos = array();
		$query = "
			SELECT
				id,
				nombre,
				descripcion,
				esta_id
			FROM
				sai_memorando_asunto
			WHERE
				esta_id <> 2 AND
				LOWER(nombre) LIKE '%".utf8_decode(mb_strtolower($key, 'UTF-8'))."%' 
			LIMIT
				".$numItems."
		";
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		if($result === false){
			echo $GLOBALS['SafiClassDb']->GetErrorMsg();
		}
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$memorandoAsunto = new EntidadMemorandoAsunto();
			$memorandoAsunto->SetId($row['id']);
			$memorandoAsunto->SetNombre(utf8_encode($row['nombre']));
			$memorandoAsunto->SetDescripcion(utf8_encode($row['descripcion']));
			$memorandoAsunto->SetEstaId($row['esta_id']);
			$memorandoAsuntos[] = $memorandoAsunto;
		}
		return $memorandoAsuntos;
	}

	public static function BuscarIdsMemorando($codigoDocumento, $idDependencia, $numLimit) {
	    $ids = null;
	    try  {
	        if($codigoDocumento == null || trim($codigoDocumento)=='' || trim($idDependencia) == '' || trim($idDependencia) == null)
	            throw new Exception("Error al buscar los ids de memorando. Detalles: El código del documento o la dependencia es nulo o vacío");
	
	        $query = "
	            SELECT
	                memo_id as id
	            FROM
	                sai_memorando
	            WHERE
	                memo_id like '%".$codigoDocumento."%' AND
	                SUBSTRING(depe_id,1,2)=SUBSTRING('".$idDependencia."',1,2) AND
	                esta_id<>15 AND
	                memo_id NOT IN (
	                			SELECT nro_documento
	                			FROM registro_documento
	                			WHERE tipo_documento = 'memr'
	                			AND id_estado=1
	                			AND user_depe='".$_SESSION['user_depe_id']."'
	                			) AND 
	                fecha_memorando LIKE '".$_SESSION['an_o_presupuesto']."%'  
	            ORDER BY memo_id 
				LIMIT
				".$numLimit."
	        ";
	        if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
	            throw new Exception("Error al obtener los ids de memorando. Detalles: ".
	                utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
	        $ids = array();
	
	        while($row = $GLOBALS['SafiClassDb']->Fetch($result))
	        {
	            $ids[] = $row['id'];
	        }
	
	    }catch(Exception $e){
	        error_log($e, 0);
	    }
	
	    return $ids;
	}

/*public static function obtenerBeneficiario($codigoDocumento) {
		include_once(SAFI_MODELO_PATH . '/compromiso.php' );
	    try  {
	        if($codigoDocumento == null || trim($codigoDocumento)=='')
	            throw new Exception("Error al buscar la información del memorando. Detalles: El código del documento es nulo o vacío");
	
	        $query = "
			SELECT cedula || ':' || e.empl_nombres || ' ' || e.empl_apellidos AS empleado 
			FROM sai_memorando_para mp, sai_empleado e
			WHERE mp.cedula=e.empl_cedula and mp.memo_id='".$codigoDocumento."'
			LIMIT 1";
	        if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
	            throw new Exception("Error al obtener el beneficiario del memo".
	                utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	        	$empleado = "";
	            if($row = $GLOBALS['SafiClassDb']->Fetch($result)) 
	        {
	            $empleado = $row['empleado'];
	        }
	
	    }catch(Exception $e){
	        error_log($e, 0);
	    }
	    return $empleado;
	} 	*/	 		
	
	public static function BuscarInfoMemorando($codigoDocumento) {
		include_once(SAFI_MODELO_PATH . '/compromiso.php' );
	    try  {
	        if($codigoDocumento == null || trim($codigoDocumento)=='')
	            throw new Exception("Error al buscar la información del memorando. Detalles: El código del documento es nulo o vacío");
	
	        $query = "
				SELECT m.memo_id AS id, 
					TO_CHAR(m.fecha_memorando, 'dd-mm-yyyy') AS fecha,
					ma.nombre AS asunto 
				FROM sai_memorando m, sai_memorando_asunto ma
				WHERE m.id_asunto = ma.id
	        		 AND m.memo_id='".$codigoDocumento."'";
	        if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
	            throw new Exception("Error al obtener los ids de memorando. Detalles: ".
	                utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
	        $ids = array();
	
	        if($row = $GLOBALS['SafiClassDb']->Fetch($result))
	        {
	            $ids[0] = $row['id'];
	            $ids[1] = '';
	            $ids[2] = 0;
	            $ids[3] = utf8_encode($row['asunto']);
	            if(SafiModeloCompromiso::GetCompromisoByIdDocumento($ids[0]))
	            	$ids[4] = SafiModeloCompromiso::GetCompromisoByIdDocumento($ids[0])->GetId();
	            else 
	            	$ids[4] = "comp-400";
				$ids[5] = $row['fecha'];	            	
	        }
	
	    }catch(Exception $e){
	        error_log($e, 0);
	    }
	    return $ids;
	} 		 	
	
}