<?php

include_once(SAFI_ENTIDADES_PATH . '/beneficiarioviatico.php');

class SafiModeloBeneficiarioViatico
{
	public static function GetAllBeneficiariosActivos()
	{
		$beneficiarios = array();
		
		$query = "
			SELECT
				benvi_nombres,
				nacionalidad,
				depe_id,
				benvi_apellidos,
				benvi_cedula,
				tipo,
				benvi_esta_id,
				banco_nomina,
				tipo_cuenta_nomina,
				cuenta_nomina
			FROM
				sai_viat_benef
			WHERE
				benvi_esta_id = 1
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$beneficiarios[$row['benvi_cedula']] = $row;
		}
		
		return $beneficiarios;
	}
	
	public static function GetBeneficiarioViaticoActivoByCedula($cedula)
	{
		$beneficiario = null;
		
		$query = "
			SELECT
				benvi_nombres,
				nacionalidad,
				depe_id,
				benvi_apellidos,
				benvi_cedula,
				tipo,
				benvi_esta_id,
				banco_nomina,
				tipo_cuenta_nomina,
				cuenta_nomina
			FROM
				sai_viat_benef
			WHERE
				benvi_esta_id = 1 AND
				benvi_cedula = " . $cedula . "
		";
		
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			$beneficiario = $GLOBALS['SafiClassDb']->Fetch($result);
		}
		
		return $beneficiario;
	}
	
	public static function GetBeneficiarioViaticoActivoByCedula2($cedula)
	{
		$beneficiario = null;
		
		$query = "
			SELECT
				benvi_nombres,
				nacionalidad,
				depe_id,
				benvi_apellidos,
				benvi_cedula,
				tipo,
				benvi_esta_id
			FROM
				sai_viat_benef
			WHERE
				benvi_esta_id = 1 AND
				benvi_cedula = " . $cedula . "
		";
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$beneficiario = self::LlenarBeneficiarios($row);
			}
		}
		
		return $beneficiario;
	}
	
	public static function GetBeneficiarioViaticoByCedula($cedula)
	{
		$beneficiario = null;
		
		$query = "
			SELECT
				benvi_nombres,
				nacionalidad,
				depe_id,
				benvi_apellidos,
				benvi_cedula,
				tipo,
				benvi_esta_id
			FROM
				sai_viat_benef
			WHERE
				benvi_cedula = " . $cedula . "
		";
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$beneficiario = self::LlenarBeneficiarios($row);
			}
		}
		
		return $beneficiario;
	}
	
	public static function Search($key, $numItems, array $params = null)
	{
		$beneficiarios = array();
		$where = "";
		$preMsg = 'Error al obtener los beneficiarios de viáticos.';
		
		try {
			if($params != null && is_array($params))
			{
				if(($idEstatus=$params['idEstatus']) != null && ($idEstatus=trim($idEstatus))){
					$where = "AND benvi_esta_id = " . $idEstatus;
				}
			}
			
			$query = "
				SELECT
					benvi_nombres,
					nacionalidad,
					depe_id,
					benvi_apellidos,
					benvi_cedula,
					tipo,
					benvi_esta_id
				FROM
					sai_viat_benef
				WHERE
					(
						benvi_cedula LIKE '%" . $GLOBALS['SafiClassDb']->Quote(utf8_decode($key)) . "%' OR
						lower(benvi_nombres || ' ' || benvi_apellidos) LIKE '%"
							. utf8_decode(mb_strtolower($GLOBALS['SafiClassDb']->Quote($key, 'UTF-8'))) . "%'
					)
					".$where."
			LIMIT
				" . $numItems . "
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) ==false)
				throw new Exception(utf8_decode($preMsg.' Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$beneficiarios[$row['benvi_cedula']] = self::LlenarBeneficiarios($row);
			}
			
			
		} catch (Exception $e) {
			error_log(utf8_encode($e));
			return null;
		}
		
		return $beneficiarios;
	}
	
	private static function LlenarBeneficiarios($row)
	{
		$beneficiario = new EntidadBeneficiarioViatico();
		
		$beneficiario->SetId($row['benvi_cedula']);
		$beneficiario->SetNombres($row['benvi_nombres']);
		$beneficiario->SetApellidos($row['benvi_apellidos']);
		$beneficiario->SetTipo($row['tipo']);
		
		return $beneficiario;
	}
}
?>