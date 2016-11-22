<?php
include_once(SAFI_ENTIDADES_PATH . '/cuentaBanco.php');

class SafiModeloCuentaBanco
{
	public static function GetCuentaBancoById($idCuentaBanco)
	{
		try
		{
			$preMsg = "Error al intentar obtener una cuenta bancaria por id.";
			
			if($idCuentaBanco == null)
				throw new Exception($preMsg." El parámetro idCuentaBanco es nulo.");
				
			if(($idBanco=trim($idCuentaBanco)) == '')
				throw new Exception($preMsg." El parámetro idCuentaBanco está vacío.");
			
			$cuentaBancos = self::GetCuentaBancos(array("idsCuentaBancos" => array($idCuentaBanco)));
			
			if(!is_array($cuentaBancos) || count($cuentaBancos) == 0)
				throw new Exception($preMsg." No se pudo obtener la cuenta bancaria con id \"".$idCuentaBanco."\".");
			
			return current($cuentaBancos);
		}
		catch (Exception $e)
		{
			error_log(utf8_encode($e));
			return null;
		}
		
	}
	
	public static function GetCuentaBancos(array $params)
	{
		try
		{
			$preMsg = "Error al intentar obtener las cuentas bancarias";
			$existeCriterio = false;
			$arrMsg = null;
			$queryWhere = "";
			$cuentaBancos = null;
			
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
				
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
			
			$arrMsg = array();
			if(!isset($params['idsCuentaBancos']))
				$arrMsg[] = $preMsg." El parámetro params['idsCuentaBancos'] no pudo ser encontrado.";
			else if(($idsCuentaBancos=$params['idsCuentaBancos']) == null)
				$arrMsg[] = $preMsg." El parámetro params['idsCuentaBancos'] es nulo.";
			else if(!is_array($idsCuentaBancos))
				$arrMsg[] = $preMsg." El parámetro params['idsCuentaBancos'] no es un arreglo.";
			else if(count($idsCuentaBancos) == 0)
				$arrMsg[] = $preMsg." El parámetro params['idsCuentaBancos'] está vacío.";
			else{
				$existeCriterio = true;
				$queryWhere =  "cuenta_banco.ctab_numero IN ('".implode("' , '", $idsCuentaBancos)."')";
			}
			
			if(!$existeCriterio){
				throw new Exception($preMsg." No existe criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}
			
			$query = "
				SELECT
					".self::GetSelectFieldsCuentaBanco()."
				FROM
					sai_ctabanco cuenta_banco
				WHERE
					".$queryWhere."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$cuentaBancos = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$cuentaBancos[$row['ctab_numero']] = self::LlenarCuentaBanco($row);
			}
			
			return $cuentaBancos;
			
		}
		catch(Exception $e){
			error_log($e);
			return null;
		}
	}

	public static function GetCuentaBancoByCuentaContable($cuentas)
	{
		try
		{
			$preMsg = "Error al intentar obtener las cuentas bancarias";
			$existeCriterio = false;
			$arrMsg = null;
			$queryWhere = "";
			$cuentaBancos = null;
				
			$existeCriterio = true;
			$queryWhere =  "c.cpat_id IN ('".implode("', '", $cuentas)."')";
				
			if(!$existeCriterio){
				throw new Exception($preMsg." No existe criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}
				
			$query = "
				SELECT
					c.cpat_id AS cpat_id,
					c.cpat_nombre AS cpat_nombre,
					COALESCE(cuenta_banco.ctab_numero,'') AS cbanco_id,
					cuenta_banco.ctab_descripcion AS cbanco_nombre										 
				FROM
					sai_cue_pat c
				LEFT OUTER JOIN	sai_ctabanco cuenta_banco ON (cuenta_banco.cpat_id = c.cpat_id)
				WHERE
					".$queryWhere."
			";
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$cuentaBancos = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$cuentaBancos[$row['cpat_id']]['cpat_id'] = $row['cpat_id'];
				$cuentaBancos[$row['cpat_id']]['cpat_nombre'] = $row['cpat_nombre'];
				$cuentaBancos[$row['cpat_id']]['cbanco_id'] = $row['cbanco_id'];
				$cuentaBancos[$row['cpat_id']]['cbanco_nombre'] = $row['cbanco_nombre'];
			}
				
			return $cuentaBancos;
				
		}
		catch(Exception $e){
			error_log($e);
			return null;
		}
	}
	
	
	
	
	public static function GetAllCuentaBancosActivos()
	{
		try
		{
			$bancos = null;
			$preMsg = "Error al intentar obtener todas las cuentas bancarias activas.";
		
			$query = "
				SELECT
					".self::GetSelectFieldsCuentaBanco()."
				FROM
					sai_ctabanco cuenta_banco
				WHERE
					cuenta_banco.ctab_estatus = 1
			";

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$cuentaBancos = array();
			$i=0;
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$cuentaBancos[$i] = self::LlenarCuentaBanco($row);
				$i++;
			}
			
		}catch(Exception $e){
			error_log($e);
			return null;
		}
		
		return $cuentaBancos;
	}
	
	public static function GetSelectFieldsCuentaBanco()
	{
		return "
			cuenta_banco.ctab_numero AS cuenta_banco_id,
			cuenta_banco.tipo_id AS cuenta_banco_tipo_cuenta,
			cuenta_banco.banc_id AS cuenta_banco_banco_id,
			cuenta_banco.ctab_descripcion AS cuenta_banco_descripcion,
			to_char(cuenta_banco.ctab_fechaapert, 'DD/MM/YYYY') AS cuenta_banco_fecha_apertura,
			to_char(cuenta_banco.ctab_fechacierre, 'DD/MM/YYYY') AS cuenta_banco_fecha_cierre,
			cuenta_banco.cpat_id AS cuenta_banco_cuenta_contable_id,
			cuenta_banco.ctab_estatus AS cuenta_banco_estatus_id,
			cuenta_banco.ctab_ano AS cuenta_banco_a_o_apertura,
			cuenta_banco.ctab_saldoinicial AS cuenta_banco_saldo_inicial,
			to_char(cuenta_banco.ctab_fechareg, 'DD/MM/YYYY') AS cuenta_banco_fecha_registro,
			to_char(cuenta_banco.ctab_fechacierrereg, 'DD/MM/YYYY')AS cuenta_banco_fecha_cierre_registro,
			cuenta_banco.usua_login AS cuenta_banco_usuario_login
		";
	}
	
	public static function LlenarCuentaBanco($row)
	{
		$cuentaBanco = new EntidadCuentaBanco();
		$cuentaContable = new EntidadCuentaContable();
		$cuentaContable -> SetId($row['cuenta_banco_cuenta_contable_id']);

		$banco = new EntidadBanco();
		$banco->SetId($row['cuenta_banco_banco_id']);
		$estatus = new EntidadEstatus();
		$estatus->SetId($row['cuenta_banco_estatus_id']);
		
		$cuentaBanco->SetId($row['cuenta_banco_id']);
		$cuentaBanco->SetTipoCuenta($row['cuenta_banco_tipo_cuenta']);
		$cuentaBanco->SetBanco($banco);
		$cuentaBanco->SetDescripcion($row['cuenta_banco_descripcion']);
		$cuentaBanco->SetFechaApertura($row['cuenta_banco_fecha_apertura']);
		$cuentaBanco->SetFechaCierre($row['cuenta_banco_fecha_cierre']);
		$cuentaBanco->SetCuentaContable($cuentaContable);				
		$cuentaBanco->SetEstatus($estatus);
		$cuentaBanco->SetA_oApertura($row['cuenta_banco_estatus_id']);
		$cuentaBanco->SetSaldoInicial($row['cuenta_banco_saldo_inicial']);
		$cuentaBanco->SetFechaRegistro($row['cuenta_banco_fecha_registro']);
		$cuentaBanco->SetFechaCierreRegistro($row['cuenta_banco_fecha_cierre_registro']);
		$cuentaBanco->SetUsuarioLogin($row['cuenta_banco_usuario_login']);
		
		return $cuentaBanco;
	}
}
?>