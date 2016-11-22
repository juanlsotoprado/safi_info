<?php
include_once(SAFI_ENTIDADES_PATH . '/cuentaContable.php');

class SafiModeloCuentaContable
{
	public static function GetCuentaContableById($idCuentaContable)
	{
		try
		{
			$preMsg = "Error al intentar obtener una cuenta contable por id.";
			
			if($idCuentaContable == null)
				throw new Exception($preMsg." El parámetro idCuentaBanco es nulo.");
				
			if(($idBanco=trim($idCuentaContable)) == '')
				throw new Exception($preMsg." El parámetro idCuentaBanco está vacío.");
			
			$cuentaContables = self::GetCuentaContable(array("idsCuentaContables" => array($idCuentaContable)));
			
			if(!is_array($cuentacontables) || count($cuentaContables) == 0)
				throw new Exception($preMsg." No se pudo obtener la cuenta contable con id \"".$idCuentaContable."\".");
			
			return current($cuentaContables);
		}
		catch (Exception $e)
		{
			error_log(utf8_encode($e));
			return null;
		}
		
	}

	
	
	public static function GetNombreCuentaContable($params)
	{
		try
		{
			$preMsg = "Error al intentar obtener las cuentas contables";
			$existeCriterio = false;
			$arrMsg = null;
			$queryWhere = "";
			
			
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
				
			//if(!is_array($params))
				//throw new Exception($preMsg." El parámetro params no es un arreglo.");
			
			$arrMsg = array();

				$existeCriterio = true;
				//$queryWhere =  "c.cpat_id IN ('".implode("' , '", $params)."')";
				$queryWhere =  "c.cpat_id IN (".$params.")";
			
			
			if(!$existeCriterio){
				throw new Exception($preMsg." No existe criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}
			
			$query = "
				SELECT
					".self::GetSelectFieldsCuentaContable()."
				FROM
					sai_cue_pat c
				WHERE
					".$queryWhere."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$cuentaContables = array();
			//$cuentaContables = "";
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$cuentaContables[$row['cpat_id']] = self::LlenarCuentaContable($row);
				//$cuentaContables .= $row['cpat_nombre'].",";
			}
			
			return $cuentaContables;
			//return  substr($cuentaContables, 0, -1);
			
		}
		catch(Exception $e){
			error_log($e);
			return null;
		}
	}

	
	public static function Search($key, $numItems, $selecteds = array())
	{
		try {
				
			$where = "";
			if($selecteds != null && is_array($selecteds) && count($selecteds)>0){
				$where .= "
					AND c.cpat_id NOT IN ('".implode("', '", $GLOBALS['SafiClassDb']->Quote($selecteds))."')
				";
			}
				
			$dato = strlen(trim($key));
				
			if($dato > 7){
					
				$d1 = substr($key,0,1);
				$d2 = substr($key,1,2);
				$d3 = substr($key,3,2);
				$d4 = substr($key,5,2);
				$d5 = substr($key,7);
					
				error_log($key2);
					
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
					".self::GetSelectFieldsCuentaContable()."
				FROM
					sai_cue_pat c
				WHERE
					((c.cpat_id LIKE '".$GLOBALS['SafiClassDb']->Quote($key)."%' OR
					c.cpat_id LIKE '".$GLOBALS['SafiClassDb']->Quote($key2)."%' OR
					LOWER(c.cpat_nombre) LIKE '%" . utf8_decode(mb_strtolower($GLOBALS['SafiClassDb']->Quote($key), 'UTF-8')) . "%') AND
						SUBSTRING(c.cpat_id,16) != '00'
					)
					" . $where . "
				ORDER BY
					c.cpat_id
				LIMIT
					".$numItems."
			";

				
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error en la búsqueda de partidas. Detalles: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				
			$cuentaContables = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$cuentaContables[$row['cpat_id']] = self::LlenarCuentaContable($row);
			}
				
			return $cuentaContables;
				
		} catch (Exception $e) {
			error_log($e, 0);
			return null;
		}
	
	}
	
	
	public static function GetCuentasContables()
	{
		try
		{
			$preMsg = "Error al intentar obtener las cuentas contables";
					
				
			$query = "
				SELECT
					".self::GetSelectFieldsCuentaContable()."
				FROM
					sai_cue_pat c
				
			";
				
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$cuentaContables = array();
			//$cuentaContables = "";
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$cuentaContables[$row['cpat_id']] = self::LlenarCuentaContable($row);
				//$cuentaContables .= $row['cpat_nombre'].",";
			}
				
			return $cuentaContables;
			//return  substr($cuentaContables, 0, -1);
				
		}
		catch(Exception $e){
			error_log($e);
			return null;
		}
	}
	
	
	
	public static function GetSelectFieldsCuentaContable()
	{
		return "
			c.cpat_id AS cpat_id,
			c.cpat_nombre AS cpat_nombre
		";
	}
	
	public static function LlenarCuentaContable($row)
	{
		$cuentaContable = new EntidadCuentaContable();
		$cuentaContable -> SetId($row['cpat_id']);
		$cuentaContable -> SetNombre($row['cpat_nombre']);		
		return $cuentaContable;
	}
}
?>