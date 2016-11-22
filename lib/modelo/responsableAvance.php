<?php

include_once(SAFI_ENTIDADES_PATH . '/responsableAvance.php');

class SafiModeloResponsableAvance
{
	public static function GuardarResponsableAvance(EntidadResponsableAvance $responsableAvance)
	{
		try
		{
			if($responsableAvance == null)
				throw new Exception("El parámetro responsableAvance es nulo.");
			
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$idResponsable = $GLOBALS['SafiClassDb']->NextId('safi_responsable_avance__id__seq');
					
			if($idResponsable == false || !SafiIsId($idResponsable)){
				throw new Exception('Error al guardar el responsable del avance. No se pudo obtener el id del responsable.'
					.'Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			}
			
			$responsableFields = array();
			$responsableValues = array();

			if(
				strcmp($responsableAvance->GetTipoResponsable(), EntidadResponsable::TIPO_EMPLEADO) == 0
				&& $responsableAvance->GetEmpleado() != null && $responsableAvance->GetEmpleado()->GetId() != null
			){
				$responsableFields[] = 'empleado_cedula';
				$responsableValues[] = "'".$responsableAvance->GetEmpleado()->GetId()."'";
			} if(
				strcmp($responsableAvance->GetTipoResponsable(), EntidadResponsable::TIPO_BENEFICIARIO) == 0
				&& $responsableAvance->GetBeneficiario() != null && $responsableAvance->GetBeneficiario()->GetId() != null
			){
				$responsableFields[] = 'beneficiario_cedula';
				$responsableValues[] = "'".$responsableAvance->GetBeneficiario()->GetId()."'";
			}
			
			$query = "
				INSERT INTO safi_responsable_avance
					(
						id,
						avance_id,
						".implode(',', $responsableFields).",
						edo_id,
						numero_cuenta_bancaria,
						tipo_cuenta_bancaria,
						banco_cuenta_bancaria
					)
				VALUES
					(
						".$idResponsable.",
						".(
							(
								$responsableAvance->GetIdAvance() != null && trim($responsableAvance->GetIdAvance()) != ''
							)
							? "'" . $responsableAvance->GetIdAvance() . "'" : "NULL"
						
						).",
						".implode(',', $responsableValues).",
						".(
							(
								$responsableAvance->GetEstado() != null && $responsableAvance->GetEstado()->GetId() != null
								&& trim($responsableAvance->GetEstado()->GetId()) != '' 
								&& strcmp(trim($responsableAvance->GetEstado()->GetId()), "0") != 0
							)
							? trim($responsableAvance->GetEstado()->GetId()) : "NULL"
						).",
						".(
							$responsableAvance->GetNumeroCuenta() != null && trim($responsableAvance->GetNumeroCuenta()) != ''
							? "'".trim($responsableAvance->GetNumeroCuenta())."'" : "NULL"
						).",
						".(
							$responsableAvance->GetTipoCuenta() != null && trim($responsableAvance->GetTipoCuenta()) != ''
							? "'".trim($responsableAvance->GetTipoCuenta())."'" : "NULL"
						).",
						".(
							$responsableAvance->GetBanco() != null && trim($responsableAvance->GetBanco()) != ''
							? "'".$GLOBALS['SafiClassDb']->Quote(trim($responsableAvance->GetBanco()))."'" : "NULL"
						)."
					)
			";
						
			$result = $GLOBALS['SafiClassDb']->Query($query);
			
			if($result === false) throw new Exception('Error al guardar la información del responsable del avance. Detalles: '
				. utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la función de guardado del responsable del avance. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			return $idResponsable;
		}
		catch (Exception $e)
		{
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}
	
	public static function ActualizarResponsableAvance(EntidadResponsableAvance $responsableAvance)
	{
		try
		{
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			if($responsableAvance == null)
				throw new Exception("El parámetro responsableAvance es nulo.");
				
			if($responsableAvance->GetId() == null || trim($responsableAvance->GetId()) == '')
				throw new Exception("El parámetro responsableAvance id es nulo o está vacío.");
				
			if($responsableAvance->GetIdAvance() == null || trim($responsableAvance->GetIdAvance()) == '')
				throw new Exception("El parámetro responsableAvance idAvance es nulo o está vacío.");
			
			if(
				strcmp($responsableAvance->GetTipoResponsable(), EntidadResponsable::TIPO_EMPLEADO) == 0
				&& $responsableAvance->GetEmpleado() != null && $responsableAvance->GetEmpleado()->GetId() != null
				&& ($idEmpleado=trim($responsableAvance->GetEmpleado()->GetId())) != ''
			){
				$queryEmpleadoBeneficiario = "
					empleado_cedula = '" . $idEmpleado . "',
					beneficiario_cedula = NULL
				";
			} else if(
				strcmp($responsableAvance->GetTipoResponsable(), EntidadResponsable::TIPO_BENEFICIARIO) == 0
				&& $responsableAvance->GetBeneficiario() != null && $responsableAvance->GetBeneficiario()->GetId() != null
				&& ($idBeneficiario=trim($responsableAvance->GetBeneficiario()->GetId())) != ''
			){
				$queryEmpleadoBeneficiario = "
					empleado_cedula = NULL,
					beneficiario_cedula = '" . $idBeneficiario . "'
				";
			} else {
				throw new Exception("Error al actualizar la información del responsable del avance. No se puede obtener el ".
					"id del empleado/beneficiario del viático.");
			}
			
			$query = "
				UPDATE
					safi_responsable_avance
				SET
					" . $queryEmpleadoBeneficiario . ",
					edo_id =
						".(
							(
								$responsableAvance->GetEstado() != null && $responsableAvance->GetEstado()->GetId() != null
								&& trim($responsableAvance->GetEstado()->GetId()) != '' 
								&& strcmp(trim($responsableAvance->GetEstado()->GetId()), "0") != 0
							)
							? trim($responsableAvance->GetEstado()->GetId()) : "NULL"
						).",
					numero_cuenta_bancaria =
						".(
							$responsableAvance->GetNumeroCuenta() != null && trim($responsableAvance->GetNumeroCuenta()) != ''
							? "'".trim($responsableAvance->GetNumeroCuenta())."'" : "NULL"
						).",
					tipo_cuenta_bancaria = 
						".(
							$responsableAvance->GetTipoCuenta() != null && trim($responsableAvance->GetTipoCuenta()) != ''
							? "'".trim($responsableAvance->GetTipoCuenta())."'" : "NULL"
						).",
					banco_cuenta_bancaria =
						".(
							$responsableAvance->GetBanco() != null && trim($responsableAvance->GetBanco()) != ''
							? "'".$GLOBALS['SafiClassDb']->Quote(trim($responsableAvance->GetBanco()))."'" : "NULL"
						)."
				WHERE
					id = " . trim($responsableAvance->GetId()) . "
					AND avance_id = '" . trim($responsableAvance->GetIdAvance()) . "'
			";
				
			$result = $GLOBALS['SafiClassDb']->Query($query);
			
			if($result === false) throw new Exception('Error al actualizar la información del responsable del avance. Detalles: '
				. utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la función de actualización del responsable del avance. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			return true;
			
		}
		catch (Exception $e)
		{
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}
	
	public static function EliminarResponsableAvance(EntidadResponsableAvance $responsableAvance)
	{
		try
		{
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			if($responsableAvance == null)
				throw new Exception("El parámetro responsableAvance es nulo.");	
			
			if($responsableAvance->GetId() == null || trim($responsableAvance->GetId()) == '')
				throw new Exception("El parámetro responsableAvance id es nulo o está vacío.");
				
			if($responsableAvance->GetIdAvance() == null || trim($responsableAvance->GetIdAvance()) == '')
				throw new Exception("El parámetro responsableAvance idAvance es nulo o está vacío.");
			
			$query = "
				DELETE FROM
					safi_responsable_avance
				WHERE
					id = " . trim($responsableAvance->GetId()) . "
					AND avance_id = '" . trim($responsableAvance->GetIdAvance()) . "'
			";
				
			$result = $GLOBALS['SafiClassDb']->Query($query);
			
			if($result === false) throw new Exception('Error al eliminar la información del responsable del avance. Detalles: '
				. utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la función de eliminación del responsable del avance. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			return true;
			
		}
		catch (Exception $e)
		{
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
				
	}
	
	public static function EliminarResponsableAvanceByIdAvance($idAvance)
	{
		try
		{
			if($idAvance == null || ($idAvance=trim($idAvance)) == '')
				throw new Exception("El parámetro idAvance es nulo.");
			
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$query = "
				DELETE FROM
					safi_responsable_avance
				WHERE
					avance_id = '" . trim($idAvance) . "'
			";
				
			$result = $GLOBALS['SafiClassDb']->Query($query);
			
			if($result === false) throw new Exception('Error al eliminar la información del responsable del avance. Detalles: '
				. utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la función de eliminación del responsable del avance. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			return true;
			
		}
		catch (Exception $e)
		{
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetResponsablesAvance($params)
	{
		try
		{
			$preMsg = "Error al intentar obtener los responsables del avance.";
			$arrMsg = array();
			$existeCriterio = false;
			$queryWhere = "";
			$responsables = null;
			
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
				
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
				
			if(count($params) == 0)
				throw new Exception($preMsg." El parámetro params está vacío.");
			
			if(!isset($params["idAvance"]))
				$arrMsg[] = "El parámetro params['idAvance'] no pudo ser encontrado.";
			else if(($idAvance=$params["idAvance"]) == null )
				$arrMsg[] = "El parámetro params['idAvance'] es nulo.";
			else if(($idAvance=trim($idAvance)) == '')
				$arrMsg[] = "El parámetro params['idAvance'] está vacío.";
			else{
				$existeCriterio = true;
				$queryWhere = "avance_id = '" . $idAvance . "'";
			}
			
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
				$queryWhere .= "responsable_avance.id IN ('" . implode("', '", $idsResponsables) . "')";
			}
			
			if(!$existeCriterio){
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}
			
			$query = "
				SELECT
					".self::GetSelectFieldResponsableAvance()."
				FROM
					safi_responsable_avance responsable_avance
					LEFT JOIN sai_empleado empleado ON (empleado.empl_cedula = responsable_avance.empleado_cedula)
					LEFT JOIN sai_viat_benef beneficiario ON (beneficiario.benvi_cedula = responsable_avance.beneficiario_cedula)
					LEFT JOIN safi_edos_venezuela estado ON (estado.id = responsable_avance.edo_id)
				WHERE
					" . $queryWhere . "
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al obtener los responsables del avance. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()) . $query);
			
			$responsables = array();
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$responsables[$row['responsable_avance_id']] = self::LlenarResponsable($row); 
			}
			
			return $responsables;
			
		} catch (Exception $e) {
			error_log($e, 0);
			return null;
		}
	}
	
	public static function GetSelectFieldResponsableAvance()
	{
		return "
			responsable_avance.id AS responsable_avance_id,
			responsable_avance.avance_id AS responsable_avance_avance_id,
			responsable_avance.edo_id AS responsable_avance_edo_id,
			responsable_avance.numero_cuenta_bancaria AS responsable_avance_numero_cuenta_bancaria,
			responsable_avance.tipo_cuenta_bancaria AS responsable_avance_tipo_cuenta_bancaria,
			responsable_avance.banco_cuenta_bancaria AS responsable_avance_banco_cuenta_bancaria,
			empleado.empl_cedula AS empleado_cedula, 
			empleado.empl_nombres AS empleado_nombres,
			empleado.empl_apellidos AS empleado_apellidos,
			empleado.nacionalidad AS empleado_nacionalidad,
			empleado.depe_cosige AS empleado_dependencia,
			beneficiario.benvi_cedula AS beneficiario_cedula,
			beneficiario.benvi_nombres AS beneficiario_nombres,
			beneficiario.benvi_apellidos AS beneficiario_apellidos,
			beneficiario.nacionalidad AS beneficiario_nacionalidad,
			beneficiario.depe_id AS beneficiario_dependencia,
			beneficiario.tipo AS beneficiario_tipo,
			estado.id AS estado_id,
			estado.nombre AS estado_nombre,
			estado.estatus_actividad AS estado_estatus_actividad
		";
	}
	
	public static function LlenarResponsable($row)
	{
		$responsable = new EntidadResponsableAvance();
		$tipoResponsable = EntidadResponsable::TIPO_NINGUNO;
		$empleado = null;
		$beneficiario = null;
		$estado = null;
		
		// Establecer los datos del empleado/beneficiario
		if($row['empleado_cedula'] != null)
		{
			$tipoResponsable = EntidadResponsable::TIPO_EMPLEADO;
			
			$empleado = new EntidadEmpleado();
			$empleado->SetId($row['empleado_cedula']);
			$empleado->SetNombres($row['empleado_nombres']);
			$empleado->SetApellidos($row['empleado_apellidos']);
		}
		else if($row['beneficiario_cedula'] != null)
		{
			$tipoResponsable = EntidadResponsable::TIPO_BENEFICIARIO;
			
			$beneficiario = new EntidadBeneficiarioViatico();
			$beneficiario->SetId($row['beneficiario_cedula']);
			$beneficiario->SetNombres($row['beneficiario_nombres']);
			$beneficiario->SetApellidos($row['beneficiario_apellidos']);
			$beneficiario->SetTipo($row['beneficiario_tipo']);
		}
		
		// Establecer los datos del estado
		if($row['estado_id'] != null)
		{
			$estado = new EntidadEstado();
			
			$estado->SetId($row['estado_id']);
			$estado->SetNombre($row['estado_nombre']);
			$estado->SetEstatusActividad($row['estado_estatus_actividad']);
		}
		
		$responsable->SetId($row['responsable_avance_id']);
		$responsable->SetIdAvance($row['responsable_avance_avance_id']);
		$responsable->SetTipoResponsable($tipoResponsable);
		$responsable->SetEmpleado($empleado);
		$responsable->SetBeneficiario($beneficiario);
		$responsable->SetEstado($estado);
		$responsable->SetNumeroCuenta($row['responsable_avance_numero_cuenta_bancaria']);
		$responsable->SetTipoCuenta($row['responsable_avance_tipo_cuenta_bancaria']);
		$responsable->SetBanco($row['responsable_avance_banco_cuenta_bancaria']);
		
		return $responsable;
	}
}