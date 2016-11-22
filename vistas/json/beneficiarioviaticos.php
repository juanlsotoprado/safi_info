<?php

	header("Content-type: application/json; charset=UTF-8");
	
	$listaBeneficiario = array();
	
	$beneficiarios = $GLOBALS['SafiRequestVars']['beneficiarioviaticos'];
	if($beneficiarios != null && is_array($beneficiarios) && count($beneficiarios) > 0)
		foreach( $beneficiarios as $beneficiario ){
			if($beneficiario instanceof EntidadBeneficiarioViatico)
			{
				$beneficiario->UTF8Encode();
				$listaBeneficiario[$beneficiario->GetId()] = $beneficiario->ToArray();
			} else {
				$listaBeneficiario[utf8_encode($beneficiario['benvi_cedula'])] = array(
					"cedula" => utf8_encode($beneficiario['benvi_cedula']),
					"nombres" => utf8_encode($beneficiario['benvi_nombres']),
					"apellidos" => utf8_encode($beneficiario['benvi_apellidos']),
					"nacionalidad" => utf8_encode($beneficiario['nacionalidad']),
					"iddependencia" => utf8_encode($beneficiario['depe_id']),
					"tipo" => utf8_encode($beneficiario['tipo']),
					"idestatus" => utf8_encode($beneficiario['benvi_esta_id'])
				);
			}
		}
	
	echo json_encode(array("listabeneficiarioviatico" => $listaBeneficiario));