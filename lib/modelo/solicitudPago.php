<?php
include_once(SAFI_ENTIDADES_PATH . '/solicitudPago.php');

class SafiModeloSolicitudPago
{

	public static function Insertsolpago($params)
	{
		
		/*echo "<pre>";
		echo print_r($params);
		echo "</pre>";*/
		try{
			
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($result === true){
				
				$preMsg = "error al insertar sopg.";

			 	if($params["rifProveedorSopg"]){
			 	
			 		$estadosVenezuela= SafiModeloEstadosVenezuela::GetEstadosVenezuelaId($params["rifProveedorSopg"]['estado']);
			 
			 		foreach($params["rifProveedorSopg"]['codigo'] as $index => $valor){//bucle para agregar varios beneficiarios
			 			
			 			//error_log(print_r($params["rifProveedorSopg"]['codigo'],true));
			 		
			 	 		//INICIO asignarle el tipo al proveedor
			 	 		
			 			if($params["rifProveedorSopg"]['tipo'][$index] == 'proveedor'){

			 				$params["rifProveedorSopg"]['tipo'][$index] = 2;

			 			}else if($params["rifProveedorSopg"]['tipo'][$index] == 'empleado'){

			 				$params["rifProveedorSopg"]['tipo'][$index] = 1;
	
			 			}else{

			 				$params["rifProveedorSopg"]['tipo'][$index] = 3;

			 			}
			 			
			 			//FIN asignarle el tipo al proveedor			 		
			 		
				 	 	//INICIO codigo del siguiente sopg
				 		$param['lugar'] = "sopg";
						$param['ano'] = substr($_SESSION['an_o_presupuesto'],2);
						$param['Dependencia']= substr($_SESSION['user_perfil_id'],2,3);
						$params['sopg_id'] = SafiModeloGeneral::GetNexId($param);
						//echo $params['sopg_id'];
						$id['codigos'][]=$params['sopg_id'];
						//FIN codigo del siguiente sopg
						
					 	$params['observacionSopg'] = $params['observaciones']." ".$params["rifProveedorSopg"]['observacion'][$index]." Estado : ". $estadosVenezuela[$params["rifProveedorSopg"]['estado'][$index]]->GetNombre() ;
					 
						//if($params['partidasCompromiso']["monto"]){
					  	//se asigna el monto total del sopg
					  		$params['montoSopg'] = 0;
					  			
					  		foreach($params['partidasCompromiso']["monto"] as $valor1){
					  	
					  			$params['montoSopg'] +=  $valor1;
					  			
					  			//se esta perdiendo en error_log(print_r("  montooooo:   ".$params['montoSopg'],true));
					  		}
					  			
					  	//}
					  	
					  	/*if(count($params["rifProveedorSopg"]['codigo']) > 1){
					  		 
					  		$params['montoSopg'] =   $params["rifProveedorSopg"]['monto'][$index];
					  		error_log(print_r("  montooooo22222222222222222:   ".$params['montoSopg'],true));
					  	}*/
					  	
					  	//INICIO insertar tabla principal
					  	
					  	$query = "
	                                   INSERT INTO sai_sol_pago
	                                   (sopg_id,
	                                    depe_id,
	                                    sopg_monto,
	                                    sopg_fecha,
	                                    pres_anno,
	                                    esta_id,
	                                    usua_login,
	                                    sopg_bene_ci_rif,
	                                    sopg_bene_tp,
	                                    sopg_detalle,
	                                    sopg_observacion,
	                                    sopg_tp_solicitud,
	                                    depe_solicitante,
	                                    edo_vzla,
	                                    documento_asociado)
	                  
										VALUES (
										'".$params['sopg_id']."',
										'".$param['Dependencia']."',
										".$params['montoSopg'].",
									     now(),
										".$_SESSION['an_o_presupuesto'].",
										'10',
										'".$_SESSION['login']."',
										'".$valor."',
										".$params["rifProveedorSopg"]['tipo'][$index].",
									    '".$params["motivo"]."',
										'".$params['observacionSopg']."',
										".$params['catPagoVal'].",
										'".$params['DependenciaSoli']."',
										".$params["rifProveedorSopg"]['estado'][$index].",
										'".$params['codigoDocumentoSopg']."')";
					  	//error_log(print_r($query,true));
					  		
					  	$result = $GLOBALS['SafiClassDb']->Query($query);
					  	if($result === false) throw new Exception('Error al insertar en tabla sai_sol_pago: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
						
					  	//FIN insertar tabla principal	    	        
														
					  	if($params['poseefactura']  == "on"){
					  		
					  		error_log(print_r(" la cedula del beneficiario es:  ".$valor,true));
							
							if($params["factura"]){
							
								foreach($params["factura"]["id"] as $index => $valor2){
									
									if($params["factura"]["beneficiario"][$index] == $valor){
									
									if($params["factura"]["fecha"][$index]){
										 
										$fecha = explode ('/',$params["factura"]["fecha"][$index]);
										$fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' '.strftime('%H:%M:%S');
										
										 
									}
									
									if($params["factura"]["montoExento"][$index]==""){
											
										$params["factura"]["montoExento"][$index] = 0;
											
									}
									if($params["factura"]["montoSugeto"][$index]==""){
											
										$params["factura"]["montoSugeto"][$index] = 0;
													
									}
									if($params["factura"]["ivaMonto"][$index]==""){
											
										$params["factura"]["ivaMonto"][$index] = 0;
											
									}
							
									$query = "
	                                   INSERT INTO safi_sol_pago_factura
	                                   (sopg_id,
	                                   sopg_factura,
	                                   sopg_factu_fecha,
	                                   sopg_factu_num_cont,
	                                   monto_sujeto,
			 						   monto_exento,
			 						   factura_fecha,
	                                   factura_iva,
									   factura_iva_monto)
	                  
										VALUES (
										'".$params['sopg_id']."',
										'".$valor2."',
										now(),
										'".$params["factura"]["codigo"][$index]."',
										".$params["factura"]["montoExento"][$index].",
										".$params["factura"]["montoSugeto"][$index].",
										'".$fecha2."',
										".$params["factura"]["iva"][$index].",
										".$params["factura"]["ivaMonto"][$index].")";
									
										error_log(print_r($query,true));

										$result = $GLOBALS['SafiClassDb']->Query($query);
										if($result === false) throw new Exception('Error al insertar en tabla safi_sol_pago_factura: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
										
									}
							
								}
							
							}
							
					  	}
					  	
					  	//INICIO insertar multiples compromisos
					  	
					  	if($params['compromisoCod']){
					  			
					  		foreach($params['compromisoCod'] as $index => $valor3){
					  				
					  			$query =
					  	
					  			"
                            	INSERT INTO safi_sol_pago_compromiso
                                (sopg_id,comp_id)
                  				VALUES (
								'".$params['sopg_id']."',
								'".$valor3."')
								";
					  				
					  			//echo $query;
					  				
					  				$result = $GLOBALS['SafiClassDb']->Query($query);
					  				if($result === false) throw new Exception('Error al insertar en tabla safi_sol_pago_compromiso: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					  					
					  					
					  		}
					  		
					  	}
					  	
					  	//FIN insertar multiples compromisos
					  	
					  	//INICIO insert sai sol pago imputa
					  	$id_depe = substr($_SESSION['user_perfil_id'],2,3);
					  	
					  	if($params['partidasCompromiso']['partida']){					  	
						  	foreach($params['partidasCompromiso']['partida'] as $index => $valor4)
						  	{
						  		
						  		$query = 
						  			"
						  			INSERT INTO sai_sol_pago_imputa
						  				(sopg_id,sopg_sub_espe,sopg_acc_esp,sopg_monto,sopg_acc_pp,depe_id, sopg_tipo_impu, pres_anno, sopg_monto_exento)
						  			VALUES
						  				('".$params['sopg_id']."',
						  				'".$params['partidasCompromiso']['partida'][$index]."',
						  				'".$params['partidasCompromiso']['proyAccEspe'][$index]."',
						  				".$params['montoSopg']/*pendiente monto*/.",
						  				'".$params['partidasCompromiso']['proyAcc'][$index]."',
						  				".$params['partidasCompromiso']['tipoimpu'].",
						  				".$id_depe.",
						  				".$_SESSION['an_o_presupuesto'].",
						  				".$params['partidasCompromiso']['partida'][$index]/*pendiente monto exento*/.")
						  			";
						  		
						  			//$result = $GLOBALS['SafiClassDb']->Query($query);
						  			//if($result === false) throw new Exception('Error al insertar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
						  		//echo $query;
						  		
						  	}
					  	}
					  	//FIN insert sai sol pago imputa
					  	
					  	//INICIO guardar en tabla sai respaldo
					  	if($params['Digital']){
						  	foreach($params['Digital'] as $index => $valor5)
						  	{
						  	$query =
						  		"
						  		INSERT INTO sai_respaldo
						  			(resp_valida,docg_id,resp_tipo,resp_nombre,perf_id,usua_login)
						  		VALUES
						  			(
						  			'0',
						  			'".$params['sopg_id']."',
						  			'Digital',
						  			'".$params['Digital'][$index]."',
						  			".$_SESSION['user_perfil_id'].",
						  			'".$_SESSION['login']."'
									)
						  		";
						  		
						  		//echo $query;
						  		$result = $GLOBALS['SafiClassDb']->Query($query);
						  		if($result === false) throw new Exception('Error al insertar en sai_respaldo: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
						  		
						  	}
					  	}
						if($params['Fisico']){
						  	foreach($params['Fisico'] as $index => $valor6)
						  	{
						  		$query =
						  		"
						  		INSERT INTO sai_respaldo
						  			(resp_valida,docg_id,resp_tipo,resp_nombre,perf_id,usua_login)
						  		VALUES
						  			(
						  			'0',
						  			'".$params['sopg_id']."',
						  			'Fisico',
						  			'".$params['Fisico'][$index]."',
						  			".$_SESSION['user_perfil_id'].",
						  			'".$_SESSION['login']."'
									)
						  		";
						  	
						  		$result = $GLOBALS['SafiClassDb']->Query($query);
						  		if($result === false) throw new Exception('Error al insertar sai_respaldo fisico: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
						  		
						  	}
					  	}
					  	//FIN guardar en tabla sai respaldo
			 		
					  	//INICIO guardar en tabla doc genera
					  	
					  	$data = array();
					  	$dateTime = new DateTime();
					  	
					  	$fecha = (String) $dateTime->format('Y-m-d h:m:s');
					  	$fecha2 = (String) $dateTime->format('d/m/Y h:m:s');
				  	
					  	$data['docg_id'] = $params['sopg_id'];
					  	$data['docg_wfob_id_ini'] = $params['docg_wfob_id_ini'] != false ? $params['docg_wfob_id_ini'] :  0 ;
					  	$data['docg_wfca_id'] = $params['CadenaIdcadena'] ;
					  	$data['docg_usua_login'] = $_SESSION['login'];
					  	$data['docg_perf_id'] =  $params['IdPerfil']  != false ? $params['IdPerfil'] : $_SESSION['user_perfil_id'] ;
					  	$data['docg_fecha'] = $fecha2;
					  	$data['docg_esta_id'] = $params['docg_esta_id'] != false ? $params['docg_esta_id'] :59 ;
					  	$data['docg_prioridad'] = 1 ;
					  	$data['docg_perf_id_act'] = $params['PerfilSiguiente'] ;
					  	$data['docg_estado_pres'] = '';
					  	$data['docg_numero_reserva'] =  '';
					  	$data['docg_fuente_finan'] = '';
					  	
					  	$docGenera = SafiModeloDocGenera::LlenarDocGenera($data);
					  	  	
					  	$result = SafiModeloDocGenera::GuardarDocGenera($docGenera);
					  	if($result === false) throw new Exception('Error al insertar en tabla doc_genera: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					  	
					  	//FIN para guardar en tabla doc genera
			 		
				}//FIN bucle para agregar varios beneficiarios

			}//FIN if($params["rifProveedorSopg"]) 
			 
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();

				return $id;
				
			}else{//else de if($result === true)
				
				throw new Exception('Error al iniciar la transacci&oacute;n');
			
			}

		}catch(Exception $e){
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}

	}
	
	public static function Detallesopg($rowcode)
	{
		error_log(print_r($rowcode,true));
		//query de detalles de cada sopg
		
		$querygeneral = 
		"
		SELECT
			general.sopg_id,
			general.depe_id,
			depen.depe_nombre,
			general.sopg_monto,
			general.sopg_fecha,
			general.sopg_bene_ci_rif,
			general.sopg_detalle,
			general.sopg_observacion,
			general.sopg_tp_solicitud,
			general.sopg_bene_tp,
			catpago.nombre_sol,
			general.depe_solicitante,
			general.edo_vzla,
			general.documento_asociado
		FROM
			sai_sol_pago general
			INNER JOIN sai_dependenci depen on (depen.depe_id = general.depe_id)
			INNER JOIN sai_tipo_solicitud catpago on (catpago.id_sol = general.sopg_tp_solicitud)			
		WHERE 
			general.sopg_id in ('".implode("', '", $rowcode['codigos'])."')		
		";
		
		if(($result = $GLOBALS['SafiClassDb']->Query($querygeneral)) != false){
					
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$matriz[$row['sopg_id']]['sopg_id'] = $row['sopg_id'];
				$matriz[$row['sopg_id']]['depe_nombre'] = $row['depe_nombre'];
				$matriz[$row['sopg_id']]['sopg_monto'] = $row['sopg_monto'];
				$matriz[$row['sopg_id']]['sopg_fecha']= $row['sopg_fecha'];
				$matriz[$row['sopg_id']]['sopg_bene_ci_rif'] = $row['sopg_bene_ci_rif'];
				$matriz[$row['sopg_id']]['sopg_detalle'] = $row['sopg_detalle'];
				$matriz[$row['sopg_id']]['sopg_observacion'] = $row['sopg_observacion'];
				$matriz[$row['sopg_id']]['nombre_sol'] = $row['nombre_sol'];
				$matriz[$row['sopg_id']]['sopg_bene_tp'] = $row['sopg_bene_tp'];
				if($matriz[$row['sopg_id']]['sopg_bene_tp']==1)
				{
					$sql = 
					"SELECT (empl_nombres || ' ' || empl_apellidos) as nombre
					FROM sai_empleado WHERE empl_cedula = '".$matriz[$row['sopg_id']]['sopg_bene_ci_rif']."' ";
					if(($result3 = $GLOBALS['SafiClassDb']->Query($sql)) != false){	
						while ($row3 = $GLOBALS['SafiClassDb']->Fetch($result3))
						{
							$matriz[$row['sopg_id']]['nombre_bene'] = $row3['nombre'];
							$matriz[$row['sopg_id']]['tipo_nombre'] = "Empleado";
						}
					}
				}
				if($matriz[$row['sopg_id']]['sopg_bene_tp']==2)
				{
					$sql =
					"SELECT prov_nombre as nombre
					FROM sai_proveedor_nuevo WHERE prov_id_rif = '".$matriz[$row['sopg_id']]['sopg_bene_ci_rif']."' ";
					if(($result4 = $GLOBALS['SafiClassDb']->Query($sql)) != false){
						while ($row4 = $GLOBALS['SafiClassDb']->Fetch($result4))
						{
							$matriz[$row['sopg_id']]['nombre_bene'] = $row4['nombre'];
							$matriz[$row['sopg_id']]['tipo_nombre'] = "Proveedor";
						}
					}
				}
				if($matriz[$row['sopg_id']]['sopg_bene_tp']==3)
				{
					$sql =
					"SELECT (benvi_nombres || ' ' || benvi_apellidos)  as nombre
					FROM sai_viat_benef WHERE benvi_cedula = '".$matriz[$row['sopg_id']]['sopg_bene_ci_rif']."' ";
					if(($result5 = $GLOBALS['SafiClassDb']->Query($sql)) != false){
						while ($row5 = $GLOBALS['SafiClassDb']->Fetch($result5))
						{
							$matriz[$row['sopg_id']]['nombre_bene'] = $row5['nombre'];
							$matriz[$row['sopg_id']]['tipo_nombre'] = "Otro";
						}
					}
				}
				$matriz[$row['sopg_id']]['documento_asociado'] = $row['documento_asociado'];
		
			}
		}
		
		$queryfactura = 
		"
		SELECT
			sopg_id,
			sopg_factura,
			sopg_factu_fecha,
			sopg_factu_num_cont,
			monto_sujeto,
			monto_exento,
			factura_fecha,
			factura_iva,
			factura_iva_monto
		FROM
			safi_sol_pago_factura
		WHERE
			sopg_id in ('".implode("', '", $rowcode['codigos'])."')
		";

		if(($result = $GLOBALS['SafiClassDb']->Query($queryfactura)) != false){
				
			while ($row2 = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$matriz[$row2['sopg_id']]['facturas'][] = $row2;
			}
		}
		
		$querycompromiso =
		"
		SELECT
			sopg_id,
			comp_id
		FROM
			safi_sol_pago_compromiso
		WHERE
			sopg_id in ('".implode("', '", $rowcode['codigos'])."')
		";

		if(($result = $GLOBALS['SafiClassDb']->Query($querycompromiso)) != false){
		
			while ($row3 = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$matriz[$row3['sopg_id']]['compromisos'][] = $row3;
			}
		}
		
		return $matriz;
			
	}

	public static function GetSolicitudPagoBy(EntidadSolicitudPago $findSolicitudPago)
	{
		$solicitudPago = null;

		try {
				
			if($findSolicitudPago == null)
			throw new Exception("Error al obtener la solicitud de pago. Detalles: ".
					"el parámetro findSolicitudPago es nulo");
				
			$where = "";
				
			if ($findSolicitudPago->GetId() != null && $findSolicitudPago->GetId() != ''){
				$where .= " solicitud_pago.sopg_id = '".$findSolicitudPago->GetId()."'";
			}
				
			if($findSolicitudPago->GetIdCompromiso() != null && $findSolicitudPago->GetIdCompromiso() != ''){
				if($where != '') $where .= " AND";
				$where .= " solicitud_pago.comp_id = '".$findSolicitudPago->GetIdCompromiso()."'";
			}
				
			if($findSolicitudPago->GetBeneficiarioCedulaRif() != null && $findSolicitudPago->GetBeneficiarioCedulaRif() != ''){
				if($where != '') $where .= " AND";
				$where .= " solicitud_pago.sopg_bene_ci_rif = '".$findSolicitudPago->GetBeneficiarioCedulaRif()."'";
			}
				
			if($where == ''){
				throw new Exception("Error al obtener la solicitud de pago. Detalles: ".
					"No se ha encontrado nigún criterio de búsqueda");
			}
				
			$where .= " AND esta_id <> 15";
				
			$query = "
				SELECT
					".self::GetSelectFieldsSolicitudPago()."
				FROM
					sai_sol_pago solicitud_pago
				WHERE
					".$where."
			";
				
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener la solicitud de pago. Detalles: ".
			utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$solicitudPago = self::LlenarSolicitudPago($row);
			}
				
		} catch (Exception $e) {
			error_log($e, 0);
		}

		return $solicitudPago;
	}

	public static function SearchIdsSolicitudPago(array $params = null)
	{
		try
		{
			$preMsg = "Error al intentar buscar (search) los ids de las solicitudes de pago.";
			$existeCriterio = false;
			$arrMsg = array();
			$queryWhere = "";
			$localNumeroItems = GetConfig("numeroItemsDefault");
			$arrSolicitudPago = null;
				
			if($params === null)
			throw new Exception($preMsg."El parámetro \"params\" es nulo.");
			if(!is_array($params))
			throw new Exception($preMsg."El parámetro \"params\" no es un arreglo.");
			if(count($params) == 0)
			throw new Exception($preMsg."El parámetro \"params\" está vacío.");

			if(!isset($params['key']))
			$arrMsg[] = "El parámetro \"params['key']\" no pudo ser encontrado.";
			if(($key=$params['key']) === null)
			$arrMsg[] = "El parámetro \"params['key']\" es nulo.";
			if(($key=trim($key)) == "")
			$arrMsg[] = "El parámetro \"parmas['key']\" está vacío";
			else {
				$existeCriterio = true;
				$queryWhere = "lower(solicitud_pago.sopg_id) LIKE '%".mb_strtolower($GLOBALS['SafiClassDb']->Quote($key))."%'";
			}
				
			if(!isset($params['idDependencia']))
			$arrMsg[] = "El parámetro \"params['idDependencia']\" no pudo ser encontrado.";
			if(($idDependencia=$params['idDependencia']) === null)
			$arrMsg[] = "El parámetro \"params['idDependencia']\" es nulo.";
			if(($idDependencia=trim($idDependencia)) == "")
			$arrMsg[] = "El parámetro \"params['idDependencia']\" está vacío.";
			else {
				$existeCriterio = true;
				if($queryWhere != "") $queryWhere .= "
					AND ";
				$queryWhere .= "solicitud_pago.depe_id = '".$GLOBALS['SafiClassDb']->Quote($idDependencia)."'";
			}
				
			if(!isset($params['a_oPresupuesto']))
			$arrMsg[] =  "El parámetro \"params['a_oPresupuesto']\" no pudo ser encontrado.";
			if(($a_oPresupuesto=$params['a_oPresupuesto']) === null)
			$arrMsg[] = "El parámetro \"params['a_oPresupuesto']\" es nulo.";
			if(($a_oPresupuesto=trim($a_oPresupuesto)) == "")
			$arrMsg[] = "El parámetro \"params['a_oPresupuesto']\" está vacío.";
			else {
				$existeCriterio = true;
				if($queryWhere != "") $queryWhere .= "
					AND ";
				$queryWhere .= "solicitud_pago.pres_anno = '".$GLOBALS['SafiClassDb']->Quote($a_oPresupuesto)."'";
			}
				
			if(!isset($params['idEstatus']))
			$arrMsg[] = "El parámetro \"params['idEstatus']\" no pudo ser encontrado.";
			if(($idEstatus=$params['isEstatus']) === null)
			$arrMsg[] = "El parámetro \"params['idEstatus']\" no es nulo.";
			if(($idEstatus=trim($idEstatus)) == "")
			$arrMsg[] = "El parámetro \"idEstatus\" está vacío.";
			else {
				$existeCriterio = true;
				if($queryWhere != "") $queryWhere .= "
					AND ";
				$queryWhere .= "solicitud_pago.esta_id = '".$GLOBALS['SafiClassDb']->Quote($idEstatus)."'";
			}
				
			if(!$existeCriterio){
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}
				
			if(isset($params['numeroItems']) && ($numeroItems=$params['numeroItems']) !== null
			&& ($numeroItems=trim($numeroItems)) != ""
			){
				$localNumeroItems = $numeroItems;
			}
				
			$query = "
				SELECT
					".self::GetSelectFieldsSolicitudPago()."
				FROM
					sai_sol_pago solicitud_pago
				WHERE
					".$queryWhere."
				LIMIT
					".$localNumeroItems."
			";
				
			//error_log($query);
				
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			$arrSolicitudPago = array();
				
			while($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$solicitudPago = self::LlenarSolicitudPago($row);
				$arrSolicitudPago[$solicitudPago->GetId()] = $solicitudPago;
			}
				
			return $arrSolicitudPago;
				
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}

	public static function GetSopgPorIniciarPago($idEstado)
	{
		$listaSopg = array();
		$query = "
			SELECT
				s.sopg_id AS sopg,
				TO_CHAR(s.sopg_fecha, 'DD/MM/YYYY') AS fecha,
				UPPER(COALESCE(em.empl_nombres,''))||' '||UPPER(COALESCE(em.empl_apellidos,''))||UPPER(COALESCE(p.prov_nombre,'')) AS nombre_beneficiario, UPPER(COALESCE(v.benvi_nombres,'')) ||' '|| UPPER(COALESCE(v.benvi_apellidos,'')) AS beneficiariov,
				UPPER(SUBSTRING(s.sopg_detalle FROM 0 FOR 80)) AS detalle
			FROM sai_sol_pago s
			LEFT OUTER JOIN sai_doc_genera d ON (d.docg_id=s.sopg_id and d.esta_id=".$idEstado.")
			LEFT OUTER JOIN sai_proveedor_nuevo p ON (trim(p.prov_id_rif)=trim(s.sopg_bene_ci_rif))
			LEFT OUTER JOIN sai_empleado em ON (trim(em.empl_cedula)=trim(s.sopg_bene_ci_rif) and trim(em.empl_cedula) not in (select prov_id_rif from sai_proveedor_nuevo where prov_esta_id=1))
			LEFT OUTER JOIN sai_viat_benef v ON (trim(v.benvi_cedula)=trim(s.sopg_bene_ci_rif) and trim(v.benvi_cedula) not in (select prov_id_rif from sai_proveedor_nuevo where prov_esta_id=1))
			WHERE
				d.docg_id = s.sopg_id AND
				sopg_fecha NOT LIKE '2008%'
			ORDER BY s.sopg_fecha DESC";
		
		//error_log(print_r($query,true));

		$sopg = null;
		if ($result = $GLOBALS['SafiClassDb']->Query($query)){
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$sopg = new EntidadSolicitudPago();
				$sopg->SetId($row['sopg']);
				$sopg->SetFecha($row['fecha']);
				if (strlen($row['nombre_beneficiario']) >4)
				$beneficiario = $row['nombre_beneficiario'];
				else
				$beneficiario = $row['beneficiariov'];
				$sopg->SetBeneficiarioNombre($beneficiario);
				$sopg->SetDetalle($row['detalle']);
				$listaSopg[] = $sopg;
			}
				
		}

		return $listaSopg;
	}

	public static function GetSelectFieldsSolicitudPago()
	{
		return "
			solicitud_pago.sopg_id AS solicitud_pago_id,
			solicitud_pago.comp_id AS solicitud_pago_id_compromiso,
			solicitud_pago.sopg_bene_ci_rif AS solicitud_pago_beneficiario_cedula_rif
		";
	}

	public static function GetSolPagoIdComp($idDocumento)
	{
		$compromiso = null;

		try {

			if($idDocumento == null || ($idDocumento=trim($idDocumento)) == '')
			throw new Exception("Error al obtener solicitud de pago dado  el id de un documento. Detalles: ".
					"el parámetro idDocumento es vacío o nulo");

			$query = "
				SELECT 
                	 count(sopg_id) as numero

				FROM
   					 sai_sol_pago

				WHERE
   					 esta_id<>15 AND
    				 comp_id= '".$idDocumento."'"; 


			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener solicitud de pago  dado el id de un documento. Detalles: ".
			utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){


				$numero = 	$row['numero'];
					
	
				return $numero;
					
			}else{
					
				return 0;

					
			}
				
				

		} catch (Exception $e) {
			error_log($e, 0);
		}


		return $numero;
	}

	public static function LlenarSolicitudPago($row)
	{
		$solicitudPago = new EntidadSolicitudPago();

		$solicitudPago->SetId($row['solicitud_pago_id']);
		$solicitudPago->SetIdCompromiso($row['solicitud_pago_id_compromiso']);
		$solicitudPago->SetBeneficiarioCedulaRif($row['solicitud_pago_beneficiario_cedula_rif']);

		return $solicitudPago;
	}
	

}
?>
