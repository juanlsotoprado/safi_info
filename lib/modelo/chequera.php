<?php
include_once(SAFI_ENTIDADES_PATH . '/chequera.php');

class SafiModeloChequera
{
	public static function GetChequeraBy(EntidadChequera $findChequera)
	{
		$chequera = null;
		
		try {
			
			if($findChequera == null)
				throw new Exception("Error al obtener la chequera. Detalles: ".
					"el parámetro findChequera es nulo");
			
			$where = "";
			
			if ($findChequera->GetId() != null && $findChequera->GetId() != ''){
				$where .= " chequera.nro_chequera = '".$findChequera->GetId()."'";
			}
			
			if($where == ''){
				throw new Exception("Error al obtener la chequera. Detalles: ".
					"No se ha encontrado nigún criterio de búsqueda");
			}
			
			$query = "
				SELECT
					chequera.nro_chequera AS chequera_id,
					chequera.cheq_cantidad AS chequera_cantidad,
					chequera.banc_id AS chequera_id_banco,
					chequera.ctab_numero AS chequera_numero_cuenta_bancaria,
					chequera.cheq_activa AS chequera_activa,
					banco.banc_nombre AS banco_nombre,
					banco.banc_www AS banco_www,
					banco.esta_id AS banco_esta_id,
					banco.usua_login AS banco_usua_login
				FROM
					sai_chequera chequera
					LEFT JOIN sai_banco banco ON (banco.banc_id = chequera.banc_id)
				WHERE
					".$where."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al obtener la chequera. Detalles: ". 
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$chequera = self::LlenarChequera($row);
			}
			
		} catch (Exception $e) {
			error_log($e, 0);
		}
		
		return $chequera;
	}
	
	private static function LlenarChequera($row)
	{
		$banco = null;
		
		$banco = new EntidadBanco();
		$banco->SetId($row['chequera_id_banco']);
		
		if($row['chequera_id_banco']!=null && trim($row['chequera_id_banco'])!=''){
			$banco = new EntidadBanco();
			$banco->SetId(trim($row['chequera_id_banco']));
			
			if(isset($row['banco_nombre'])){
				$banco->SetNombre($row['banco_nombre']);
				$banco->SetSitioWeb($row['banco_www']);
				$banco->SetIdEstatus($row['banco_esta_id']);
				$banco->SetUsuaLogin($row['banco_usua_login']);
			}
		}
		
		$chequera = new EntidadChequera();
		
		$chequera->SetId($row['chequera_id']);
		$chequera->SetCantidad($row['chequera_cantidad']);
		$chequera->SetBanco($banco);
		$chequera->SetNumeroCuentaBancaria($row['chequera_numero_cuenta_bancaria']);
		$chequera->SetActiva($row['chequera_activa']);
		
		return $chequera;
	}
	
}