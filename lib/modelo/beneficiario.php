<?php

include_once(SAFI_ENTIDADES_PATH . '/beneficiarioviatico.php');

class SafiModeloBeneficiario
{
	public static function GetAllBeneficiarios()
	{
		$beneficiarios = array();
		
		$query = "SELECT 
						prov_id_rif AS id,
						prov_nombre AS nombre,
						'' AS apellido,
						'P' AS tipo 
					FROM sai_proveedor_nuevo 
					--WHERE prov_esta_id=1
					UNION
					SELECT benvi_cedula AS id,
						 benvi_nombres AS nombre, 
						 benvi_apellidos AS apellido,
						 'V' AS tipo
					FROM
						sai_viat_benef 
					UNION
					SELECT empl_cedula AS id,
						empl_nombres AS nombre, 
						empl_apellidos AS apellido,
						'E' AS empleado
					FROM sai_empleado
					ORDER BY 2
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$beneficiarios[$row['id']] = self::LlenarBeneficiarios($row);
		}
		
		return $beneficiarios;
	}
	

	
	private static function LlenarBeneficiarios($row)
	{
		$beneficiario = new EntidadBeneficiarioViatico();
		$beneficiario->SetId($row['id']);
		$beneficiario->SetNombres($row['nombre']);
		$beneficiario->SetApellidos($row['apellido']);
		$beneficiario->SetTipo($row['tipo']);
		
		return $beneficiario;
	}
}
?>