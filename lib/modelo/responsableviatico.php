<?php
include_once(SAFI_ENTIDADES_PATH . "/responsableviatico.php");

class SafiModeloResponsableViatico
{
	public static function GetResponsableByIdViaticoNacional($idViaticoNacional){
		$responsable = null;
		
		$query = "
			SELECT
				rv.id,
				rv.viatico_id,
				rv.empleado_cedula,
				rv.beneficiario_viatico_cedula,
				rv.numero_cuenta_bancaria,
				rv.tipo_cuenta_bancaria,
				rv.banco_cuenta_bancaria,
				e.empl_nombres AS empleado_nombres,
				e.empl_apellidos AS empleado_apellidos,
				e.nacionalidad AS empleado_nacionalidad,
				e.depe_cosige AS empleado_dependencia,
				vb.benvi_nombres AS beneficiario_nombres,
				vb.benvi_apellidos AS beneficiario_apellidos,
				vb.nacionalidad AS beneficiario_nacionalidad,
				vb.depe_id AS beneficiario_dependencia,
				vb.tipo AS tipo_empleado,
				cargo.carg_nombre AS empleado_cargo
			FROM
				safi_responsable_viatico rv
				LEFT JOIN sai_empleado e ON e.empl_cedula = rv.empleado_cedula
				LEFT JOIN sai_viat_benef vb ON vb.benvi_cedula = rv.beneficiario_viatico_cedula
				LEFT JOIN sai_cargo cargo ON (cargo.carg_fundacion = e.carg_fundacion)
			WHERE
				rv.viatico_id = '".$idViaticoNacional."'
		";
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
	
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$responsable = new EntidadResponsableViatico();
				
				$responsable->SetId($row['id']);
				$responsable->SetIdViatico($row['viatico_id']);
				if($row['empleado_cedula'] != null){
					$responsable->SetTipoResponsable(EntidadResponsableViatico::TIPO_EMPLEADO);
					$responsable->SetCedula($row['empleado_cedula']);
					$responsable->SetNombres($row['empleado_nombres']);
					$responsable->SetApellidos($row['empleado_apellidos']);
					$responsable->SetNacionalidad($row['empleado_nacionalidad']);
					$responsable->SetIdDependencia($row['empleado_dependencia']);
					$responsable->SetTipoEmpleado($row['empleado_cargo']);
				} else {
					$responsable->SetTipoResponsable(EntidadResponsableViatico::TIPO_BENEFICIARIO);
					$responsable->SetCedula($row['beneficiario_viatico_cedula']);
					$responsable->SetNombres($row['beneficiario_nombres']);
					$responsable->SetApellidos($row['beneficiario_apellidos']);
					$responsable->SetNacionalidad($row['beneficiario_nacionalidad']);
					$responsable->SetIdDependencia($row['beneficiario_dependencia']);
					$responsable->SetTipoEmpleado($row['tipo_empleado']);
				}
				$responsable->SetNumeroCuenta($row['numero_cuenta_bancaria']);
				$responsable->SetTipoCuenta($row['tipo_cuenta_bancaria']);
				$responsable->SetBanco($row['banco_cuenta_bancaria']);
			}
		}
		
		return $responsable;
	}
	
	public static function GetResponsableByTipoYCedula($tipo, $cedula){
		$responsable = null;
		
		if($tipo != null && $tipo != '' && $cedula != null && $cedula != ''){
			if(strcmp($tipo, EntidadResponsableViatico::TIPO_EMPLEADO)==0){
				$query = "
					SELECT
						e.empl_cedula AS cedula,
						e.empl_nombres AS nombres,
						e.empl_apellidos AS apellidos,
						e.nacionalidad AS nacionalidad,
						e.depe_cosige AS dependencia,
						'".EntidadResponsableViatico::TIPO_EMPLEADO."' AS tipo_empleado
					FROM
						sai_empleado e
					WHERE
						e.empl_cedula = '".$cedula."'
				";
			} else if (strcmp($tipo, EntidadResponsableViatico::TIPO_BENEFICIARIO)==0){
				$query = "
					SELECT
						vb.benvi_cedula AS cedula,
						vb.benvi_nombres AS nombres,
						vb.benvi_apellidos AS apellidos,
						vb.nacionalidad AS nacionalidad,
						vb.depe_id AS dependencia,
						vb.tipo AS tipo_empleado
					FROM
						sai_viat_benef vb
					WHERE
						vb.benvi_cedula = '".$cedula."'
				";
			}
			
			if(isset($query)){
				if($result = $GLOBALS['SafiClassDb']->Query($query))
				{
					if ($row = $GLOBALS['SafiClassDb']->Fetch($result))
					{
						$responsable = new EntidadResponsableViatico();
						
						$responsable->SetTipoResponsable($tipo);
						$responsable->SetCedula($row['cedula']);
						$responsable->SetNombres($row['nombres']);
						$responsable->SetApellidos($row['apellidos']);
						$responsable->SetNacionalidad($row['nacionalidad']);
						$responsable->SetIdDependencia($row['dependencia']);
						$responsable->SetTipoEmpleado($row['tipo_empleado']);
					}
				}
			}
		}
		
		return $responsable;
	}
	
	public static function GetResponsableViaticos($params = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener los responsables del viático nacional.";
			$arrMsg = array();
			$existeCriterio = false;
			$queryWhere = "";
			$responsablesViaticos = null;
			
			if ($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
				
			if (!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
				
			if(count($params) == 0)
				throw new Exception($preMsg." El parámetro params está vacío.");
			
			if(!isset($params['idsResponsables']))
				$arrMsg[] = "El parámetro params['idsResponsables'] no pudo ser encontrado.";
			else if(($idsResponsables=$params['idsResponsables']) == null)
				$arrMsg[] = "El parámetro params['idsResponsables'] es nulo.";
			else if(!is_array($idsResponsables))
				$arrMsg[] = "El parámetro params['idsResponsables'] no es un arreglo.";
			else if(count($idsResponsables) == 0)
				$arrMsg[] = "El parámetro params['idsResponsables'] está vacío.";
			else{
				$existeCriterio = true;
				if($queryWhere != ""){
					$queryWhere = "
						AND ";
				}
				$queryWhere .= "responsable_viatico.id IN ('" . implode("', '", $idsResponsables) . "')";
			}
			
			if(!$existeCriterio){
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}
			
			$query = "
				SELECT
					responsable_viatico.id,
					responsable_viatico.viatico_id,
					responsable_viatico.empleado_cedula,
					responsable_viatico.beneficiario_viatico_cedula,
					responsable_viatico.numero_cuenta_bancaria,
					responsable_viatico.tipo_cuenta_bancaria,
					responsable_viatico.banco_cuenta_bancaria,
					empleado.empl_nombres AS empleado_nombres,
					empleado.empl_apellidos AS empleado_apellidos,
					empleado.nacionalidad AS empleado_nacionalidad,
					empleado.depe_cosige AS empleado_dependencia,
					beneficiario.benvi_nombres AS beneficiario_nombres,
					beneficiario.benvi_apellidos AS beneficiario_apellidos,
					beneficiario.nacionalidad AS beneficiario_nacionalidad,
					beneficiario.depe_id AS beneficiario_dependencia,
					beneficiario.tipo AS tipo_empleado,
					cargo.carg_nombre AS empleado_cargo
				FROM
					safi_responsable_viatico responsable_viatico
					LEFT JOIN sai_empleado empleado ON empleado.empl_cedula = responsable_viatico.empleado_cedula
					LEFT JOIN sai_viat_benef beneficiario ON beneficiario.benvi_cedula = responsable_viatico.beneficiario_viatico_cedula
					LEFT JOIN sai_cargo cargo ON (cargo.carg_fundacion = empleado.carg_fundacion)
				WHERE
					" . $queryWhere . "
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg. " Detalles: ".utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()) . $query);
			
			$responsablesViaticos = array();
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				 $responsable = new EntidadResponsableViatico();
				
				$responsable->SetId($row['id']);
				$responsable->SetIdViatico($row['viatico_id']);
				if($row['empleado_cedula'] != null){
					$responsable->SetTipoResponsable(EntidadResponsableViatico::TIPO_EMPLEADO);
					$responsable->SetCedula($row['empleado_cedula']);
					$responsable->SetNombres($row['empleado_nombres']);
					$responsable->SetApellidos($row['empleado_apellidos']);
					$responsable->SetNacionalidad($row['empleado_nacionalidad']);
					$responsable->SetIdDependencia($row['empleado_dependencia']);
					$responsable->SetTipoEmpleado($row['empleado_cargo']);
				} else {
					$responsable->SetTipoResponsable(EntidadResponsableViatico::TIPO_BENEFICIARIO);
					$responsable->SetCedula($row['beneficiario_viatico_cedula']);
					$responsable->SetNombres($row['beneficiario_nombres']);
					$responsable->SetApellidos($row['beneficiario_apellidos']);
					$responsable->SetNacionalidad($row['beneficiario_nacionalidad']);
					$responsable->SetIdDependencia($row['beneficiario_dependencia']);
					$responsable->SetTipoEmpleado($row['tipo_empleado']);
				}
				$responsable->SetNumeroCuenta($row['numero_cuenta_bancaria']);
				$responsable->SetTipoCuenta($row['tipo_cuenta_bancaria']);
				$responsable->SetBanco($row['banco_cuenta_bancaria']);
				
				$responsablesViaticos[$row['id']] = $responsable;
			}
			
			return $responsablesViaticos;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetUltimaCuentaBancaria($cedula)
	{
		$preMsg = "Error al intentar obtener la última cuenta bancaria de los responsables del viático nacional.";
		$responsable = null;
		
		if ($cedula == null)
			throw new Exception($preMsg." El parámetro cedula es nulo.");
			
		if (($cedula = trim($cedula)) == '')
			throw new Exception($preMsg." El parámetro cedula está vacío.");
				
		
		$query = "
			SELECT
				responsable.id,
				responsable.empleado_cedula,
				responsable.beneficiario_viatico_cedula,
				responsable.numero_cuenta_bancaria,
				responsable.tipo_cuenta_bancaria,
				responsable.banco_cuenta_bancaria,
				responsable.viatico_id
				
			FROM
				
				safi_responsable_viatico responsable
				INNER JOIN sai_doc_genera documento ON (documento.docg_id = responsable.viatico_id)
			WHERE
				responsable.numero_cuenta_bancaria IS NOT NULL
				AND responsable.tipo_cuenta_bancaria IS NOT NULL
				AND responsable.banco_cuenta_bancaria IS NOT NULL
				AND (
					empleado_cedula = '".$cedula."'
					OR beneficiario_viatico_cedula = '".$cedula."'
				)
			ORDER BY
				documento.docg_fecha DESC
			LIMIT
				1
		";
		
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg. " Detalles: ".utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()) . $query);
			
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
		{
			 $responsable = new EntidadResponsableViatico();
			
			$responsable->SetId($row['id']);
			$responsable->SetIdViatico($row['viatico_id']);
			if($row['empleado_cedula'] != null){
				$responsable->SetTipoResponsable(EntidadResponsableViatico::TIPO_EMPLEADO);
				$responsable->SetCedula($row['empleado_cedula']);
			} else {
				$responsable->SetTipoResponsable(EntidadResponsableViatico::TIPO_BENEFICIARIO);
				$responsable->SetCedula($row['beneficiario_viatico_cedula']);
			}
			$responsable->SetNumeroCuenta($row['numero_cuenta_bancaria']);
			$responsable->SetTipoCuenta($row['tipo_cuenta_bancaria']);
			$responsable->SetBanco($row['banco_cuenta_bancaria']);
		}
		
		return $responsable;
	}
}