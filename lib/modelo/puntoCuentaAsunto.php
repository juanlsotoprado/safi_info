<?php

include_once(SAFI_ENTIDADES_PATH ."/puntoCuentaAsunto.php");

class SafiModeloPuntoCuentaAsunto
{

	
	public static function GetPctaAsusnto()
	{
		$asuntos = array();
		$query = "
			SELECT  
			*
			FROM  sai_pcta_asunt
		    WHERE esta_id=1 ORDER BY pcas_nombre ASC
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		 
		$i= 0;
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

	    $asunto = self::LlenarAsunto($row);
		$asuntos[$i] = $asunto;
					
			$i++;
		}
		
		return $asuntos;
	}
	
public static function GetPctaAsusntoId($params = null)
	{
		
		 
		$query = "
			SELECT  
			* 
			FROM  sai_pcta_asunt
		    WHERE esta_id=1
		    AND pcas_id = '".$params."'
		     ORDER BY pcas_nombre ASC
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		 
		$i= 0;
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

	    $asunto = self::LlenarAsunto($row);
		$asuntos[$i] = $asunto;
					
			$i++;
		}
		
		return $asuntos;
	}
	
   public static function GetPctaAsusntosId($params = null)
	{
		
		 
		$query = "
			SELECT  
			    * 
			    
			FROM  
			   sai_pcta_asunt
			   
		    WHERE 
		       pcas_id IN ('".implode("', '",$params)."')
		       
		   ORDER BY pcas_nombre ASC
		";
		
		
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		 

		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

	    $asunto = self::LlenarAsunto($row);
		$asuntos[$row['pcas_id']] = $asunto;
					
		}
		
		return $asuntos;
	}
	
    private static function LlenarAsunto($row){
    	
		$asunto = new EntidadPuntoCuentaAsunto();
		$asunto->SetId($row['pcas_id']);
		$asunto->SetNombre($row['pcas_nombre']);
		$asunto->SetDescripcion($row['pcas_descrip']);
		
		return $asunto;
	}
	

}