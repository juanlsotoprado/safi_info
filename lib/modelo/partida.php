<?php
require_once(SAFI_ENTIDADES_PATH . '/partida.php');

class SafiModeloPartida
{
	public static function Search($key, $numItems, $annoPresupuesto = null, $selecteds = array())
	{
		$partidas = array();
		try {
			
			$where = "";
			if($annoPresupuesto != null)
			{
				$where = "
					AND partida.pres_anno = '".$annoPresupuesto . "'
				";
			}else{
			
				$where = "
					AND partida.pres_anno = '".$_SESSION['an_o_presupuesto']. "'
				";
		
			}
			
			
			
			if($selecteds != null && is_array($selecteds) && count($selecteds)>0){
				$where .= "
					AND partida.part_id NOT IN ('".implode("', '", $GLOBALS['SafiClassDb']->Quote($selecteds))."')
				";
			}
			
			$dato = strlen(trim($key));
			
			if($dato > 7){
					
			$d1 = substr($key,0,1);
			$d2 = substr($key,1,2);
			$d3 = substr($key,3,2);
			$d4 = substr($key,5,2);
			$d5 = substr($key,7);
			
			//error_log($key2); 
			
			$key2 = $d1.'.'.$d2.'.'.$d3.'.'.$d4.'.'.$d5;

		    }else if($dato >  5 && $dato < 8){

				
			$d1 = substr($key,0,1);
			$d2 = substr($key,1,2);
			$d3 = substr($key,3,2);
			$d4 = substr($key,5,2);
			
			
			
			$key2 = $d1.'.'.$d2.'.'.$d3.'.'.$d4;
			
			
		    }else if($dato > 3 && $dato < 6 ){

			$d1 = substr($key,0,1);
			$d2 = substr($key,1,2);
			$d3 = substr($key,3);
			
			$key2 = $d1.'.'.$d2.'.'.$d3;
			
			
			}else{
	
			$d1 = substr($key,0,1);
			$d2 = substr($key,1,2);
			$key2 = $d1.'.'.$d2;
			

			}

          
			    
			

			$query = "
				SELECT
					SUBSTRING(partida.part_id,-5) AS partida_id,
					partida.pres_anno AS partida_anho,
					partida.part_nombre AS partida_nombre,
					partida.part_especial AS partida_especial,
					partida.part_observa AS partida_observaciones,
					partida.usua_login AS partida_usua_login,
					partida.esta_id AS partida_esta_id,
					partida.part_regular AS partida_regular
				FROM
					sai_partida partida
				WHERE
					((partida.part_id LIKE '".$GLOBALS['SafiClassDb']->Quote($key)."%' OR
					partida.part_id LIKE '".$GLOBALS['SafiClassDb']->Quote($key2)."%' OR
					lower(partida.part_nombre) LIKE '%" . utf8_decode(mb_strtolower($GLOBALS['SafiClassDb']->Quote($key), 'UTF-8')) . "%')AND
						SUBSTRING(partida.part_id,9) <> '00.00'
					)
					" . $where . "
				ORDER BY
					partida.part_id
				LIMIT
					".$numItems."
			";
			
			
	
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error en la búsqueda de partidas. Detalles: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
		 
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$partidas[$row['partida_id']] = self::LlenarPartida($row);
			}
			
			return $partidas;
			
		} catch (Exception $e) {
			error_log($e, 0);
			return null;
		}
		
	}
	
	public static function GetPartidasByIds(array $idsPartidas = null, $presAnno = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener las partidas por ids y año presupuestario.";
			$partidas = null;
			
			if($idsPartidas == null)
				throw new Exception($preMsg." El parámetro idsPartidas es nulo");
				
			if(!is_array($idsPartidas))
				throw new Exception($preMsg." El parámetro idsPartidas no es un arreglo");
				
			if(count($idsPartidas) == 0)
				throw new Exception($preMsg." El parámetro idsPartidas está vacío.");
				
			if($presAnno == null)
				throw new Exception($preMsg." El párametro presAnno es nulo.");
				
			if(($presAnno=trim($presAnno)) == '')
				throw new Exception($preMsg." El párametro presAnno está vacío.");
			
			$query = "
				SELECT DISTINCT
					partida.part_id AS partida_id,
					partida.pres_anno AS partida_anho,
					partida.part_nombre AS partida_nombre,
					partida.part_especial AS partida_especial,
					partida.part_observa AS partida_observaciones,
					partida.usua_login AS partida_usua_login,
					partida.esta_id AS partida_esta_id,
					partida.part_regular AS partida_regular
				FROM
					sai_partida partida
				WHERE
					partida.part_id IN ('".implode("', '", $GLOBALS['SafiClassDb']->Quote($idsPartidas))."')
					AND partida.pres_anno = '".$presAnno."' 
				ORDER BY
					partida.part_id
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				
			$partidas = array();
		 
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$partidas[$row['partida_id']] = self::LlenarPartida($row);
			}
			
			return $partidas;
		}
		catch (Exception $e)
		{
			error_log($e);
			return null;
		}
	}
	
	private static function LlenarPartida($row)
	{
		$especial = (strcasecmp($row['partida_especial'], 't') == 0 || 
				strcasecmp($row['partida_especial'], 'true') == 0) ? true : false;
		$regular = (strcasecmp($row['partida_regular'], 't') == 0 || 
			strcasecmp($row['partida_regular'], 'true') == 0) ? true : false;
		
		$partida = new EntidadPartida();
		$partida->SetId($row['partida_id']);
		$partida->SetAnho($row['partida_anho']);
		$partida->SetNombre($row['partida_nombre']);
		$partida->SetEspecial($especial);
		$partida->SetObservaciones($row['partida_observaciones']);
		$partida->SetUsuaLogin($row['partida_usua_login']);
		$partida->SetIdEstatus($row['partida_esta_id']);
		$partida->SetRegular($regular);
		
		return $partida;
	}
}