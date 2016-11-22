<?php
include_once(SAFI_ENTIDADES_PATH . '/banco.php');

class SafiModeloBanco
{
	public static function GetBancoById($idBanco)
	{
		try
		{
			$preMsg = "Error al intentar obtener un banco por id.";
			
			if($idBanco == null)
				throw new Exception($preMsg." El parámetro idBanco es nulo.");
				
			if(($idBanco=trim($idBanco)) == '')
				throw new Exception($preMsg." El parámetro idBanco está vacío.");
			
			$bancos = self::GetBancos(array("idsBancos" => array($idBanco)));
			if(!is_array($bancos) && count($bancos) == 0)
				throw new Exception($preMsg." No se pudo obtener el banco con id \"".$idBanco."\".");
			
			return current($bancos);
		}
		catch (Exception $e)
		{
			error_log(utf8_encode($e));
			return null;
		}
		
	}
	
	public static function GetBancos(array $params)
	{
		try
		{
			$preMsg = "Error al intentar obtener los bancos.";
			$existeCriterio = false;
			$arrMsg = null;
			$queryWhere = "";
			$banco = null;
			
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
				
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
			
			$arrMsg = array();
			if(!isset($params['idsBancos']))
				$arrMsg[] = $preMsg." El parámetro params['idsBancos'] no pudo ser encontrado.";
			else if(($idsBancos=$params['idsBancos']) == null)
				$arrMsg[] = $preMsg." El parámetro params['idsBancos'] es nulo.";
			else if(!is_array($idsBancos))
				$arrMsg[] = $preMsg." El parámetro params['idsBancos'] no es un arreglo.";
			else if(count($idsBancos) == 0)
				$arrMsg[] = $preMsg." El parámetro params['idsBancos'] está vacío.";
			else{
				$existeCriterio = true;
				$queryWhere =  "banc_id IN ('".implode("' , '", $idsBancos)."')";
			}
			
			if(!$existeCriterio){
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}
			
			$query = "
				SELECT
					banc_id,
					banc_nombre,
					banc_www,
					esta_id,
					usua_login
				FROM
					sai_banco
				WHERE
					".$queryWhere."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
			$bancos = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$bancos[$row['banc_id']] = self::LlenarBanco($row);
			}
			
			return $bancos;
			
		}
		catch(Exception $e){
			error_log(utf8_encode($e));
			return null;
		}
	}
	
	public static function GetAllBancosActivos()
	{
		$bancos = null;
		
		try{
			$query = "
				SELECT
					banc_id,
					banc_nombre,
					banc_www,
					esta_id,
					usua_login
				FROM
					sai_banco
				WHERE
					esta_id = 1
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception('Error al obtener todos los bancos. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
			$bancos = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$bancos[] = self::LlenarBanco($row);
			}
			
		}catch(Exception $e){
			error_log(utf8_encode($e));
			return null;
		}
		
		return $bancos;
	}
	
	private static function LlenarBanco($row){
		$banco = new EntidadBanco();
		
		$banco->SetId($row['banc_id']);
		$banco->SetNombre($row['banc_nombre']);
		$banco->SetSitioWeb($row['banc_www']);
		$banco->SetIdEstatus($row['esta_id']);
		$banco->SetUsuaLogin($row['usua_login']);
		
		return $banco;
	}
}