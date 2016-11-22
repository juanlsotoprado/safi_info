<?php
include_once (SAFI_ENTIDADES_PATH . '/puntoCuenta.php');
include_once(SAFI_ENTIDADES_PATH . '/docgenera.php');

//$_SESSION['an_o_presupuesto'] = '2014';

class SafiModeloPuntoCuenta
{	
	public static function GetPuntoCuenta(array $params = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener un punto de cuenta.";
			$existeCriterio = false;
			$arrMsg = array();
			$queryWhere = "";
			$puntoCuenta = null;
			$findParams = array();
			
			if($params === null)
				throw new Exception("El parámetro \"params\" es nulo.");
			if(!is_array($params))
				throw new Exception("El parámetro \"params\" no es un arreglo.");
			if(count($params) == 0)
				throw new Exception("El parámetro \"params\" está vacío.");
				
			if(!isset($params['idPuntoCuenta']))
				$arrMsg[] = "El parámetro \"params['idPuntoCuenta']\" no pudo ser encontrado.";
			if(($idPuntoCuenta=$params['idPuntoCuenta']) === null)
				$arrMsg[] = "El parámetro \"params['idPuntoCuenta']\" es nulo.";
			if(($idPuntoCuenta=trim($idPuntoCuenta)) == '')
				$arrMsg[] = "El parámetro \"params['idPuntoCuenta']\" está vacío.";
			else {
				$existeCriterio = true;
				$findParams["idsPuntosCuenta"] = array($idPuntoCuenta);
			}
			
			if(!$existeCriterio){
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}
			
			$arrPuntoCuenta = self::GetPuntosCuenta($findParams);
			if(!is_array($arrPuntoCuenta) && count($arrPuntoCuenta) == 0)
				throw new Exception($preMsg." No se pudo obtener el punto de cuenta con id \"".$idPuntoCuenta."\".");
			
			return current($arrPuntoCuenta);
		}
		catch(Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	

	public static function GetPuntosCuenta(array $params = null, $filtro = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener los puntos de cuenta.";
			$existeCriterio = false;
			$arrMsg = array();
			$queryWhere = "";
			$arrPuntoCuenta = null;
			
			if($params === null)
				throw new Exception($preMsg."El parámetro \"params\" es nulo.");
			if(!is_array($params))
				throw new Exception($preMsg."El parámetro \"params\" no es un arreglo.");
			if(count($params) == 0)
				throw new Exception($preMsg."El parámetro \"params\" está vacío.");
				
				
			if(!$filtro){	
					
				if(!isset($params['idsPuntosCuenta']))
					$arrMsg[] = "El parámetro \"params['idsPuntosCuenta']\" no pudo ser encontrado.";
				if(($idsPuntosCuenta=$params['idsPuntosCuenta']) === null)
					$arrMsg[] = "El parámetro \"params['idsPuntosCuenta']\" es nulo.";
				if(!is_array($idsPuntosCuenta))
					$arrMsg[] = "El parámetro \"params['idsPuntosCuenta']\" no es un arreglo.";
				if(count($idsPuntosCuenta) == 0)
					$arrMsg[] = "El parámetro \"params['idsPuntosCuenta']\" está vacío.";
				else {
					$existeCriterio = true;
					$queryWhere = "punto_cuenta.pcta_id IN ('".implode("', '", $idsPuntosCuenta)."')";
				}	
			
			} else {
			
				$existeCriterio = true;
		
				$and = false;
			
				 $cargo = substr($_SESSION['user_perfil_id'],0,2);
       	   
       	   if(
					($cargo !== substr(PERFIL_JEFE_PRESUPUESTO,0,2)) &&
					($cargo !== substr(PERFIL_ANALISTA_PRESUPUESTO,0,2))  &&
					($cargo !== substr(PERFIL_DIRECTOR_PRESUPUESTO,0,2))  &&
					($cargo !== substr(PERFIL_ASISTENTE_PRESUPUESTO,0,2))  &&
					($cargo !== substr(PERFIL_DIRECTOR_EJECUTIVO,0,2)) &&
					($cargo !== substr(PERFIL_PRESIDENTE,0,2)) 
				){
			 	
					$and == true? $queryWhere .= 'AND ' : $and = true ;
					$queryWhere .= "punto_cuenta.depe_id = '".substr($_SESSION['user_perfil_id'],-3)."'";
				}
				
				
				if($params['ncompromiso']){
				

					
		         $idpctaComp = 	self::GetCompromiso($params['ncompromiso']);


  
					$and == true? $queryWhere .= 'AND ' : $and = true ;
					$queryWhere .= "punto_cuenta.pcta_id = '".$idpctaComp."'";
					
					
				 
				}
				
				
				else if($params['codigPctaBusqueda']){
				
					$and == true? $queryWhere .= 'AND ' : $and = true ;
					$queryWhere .= "punto_cuenta.pcta_id = '".$params['codigPctaBusqueda']."'";
				}
			
				else {
			
			
					if($params['pctaAsunto']){
					
						$and == true? $queryWhere .= 'AND ' : $and = true ;
						$queryWhere .= "punto_cuenta.pcta_asunto= '".$params['pctaAsunto']."'";
					}
				
					if($params['palabraClave']){
					
						$and == true? $queryWhere .= 'AND ' : $and = true ;
						$queryWhere .= "punto_cuenta.pcta_descripcion LIKE '%".$params['palabraClave']."%'";
	
					}
					if($params['txt_inicio'] && $params['hid_hasta_itin']){
					
						$and == true? $queryWhere .= 'AND ' : $and = true ;
						$queryWhere .= "punto_cuenta.pcta_fecha  BETWEEN to_date('".$params['txt_inicio']."', 'DD/MM/YYYY') AND to_date('".$params['hid_hasta_itin']."', 'DD/MM/YYYY') ";
	
					}else{
					
						$and == true? $queryWhere .= 'AND ' : $and = true ;
						$agno = $params['agnoPcta'];
						$queryWhere .= " TO_CHAR(punto_cuenta.pcta_fecha,'YYYY') = '".$agno."'";
					}
					
				    if($params['DependenciaPcta']){
					
						$and == true? $queryWhere .= 'AND ' : $and = true ;
			
						$queryWhere .= "punto_cuenta.depe_id= ".$params['DependenciaPcta']."";
	
					}
					
					if($params['estatusPcta']){
					
						$and == true? $queryWhere .= 'AND ' : $and = true ;
			
						$queryWhere .= "punto_cuenta.esta_id= ".$params['estatusPcta']."";
	
					}
				
					if($params['PartidaBusqueda'] || $params['pctaProyAccVal']){
					
						//	error_log($params['pctaProyAccVal']);
			     
						$puntoCuentaImputa =  SafiModeloPuntoCuentaImputa::GetPctaImputaFiltro($params['PartidaBusqueda'],$params['pctaProyAccVal']);
	
						if(is_array($puntoCuentaImputa)){
							$and == true? $queryWhere .= 'AND ' : $and = true;
							$queryWhere .= "punto_cuenta.pcta_id IN ('".implode("', '",$puntoCuentaImputa)."')";
						}
					}
				}
			}
			
			
			if(!$existeCriterio){
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}
			
			$query = "
				SELECT
					".self::GetSelectFieldsPuntoCuenta()."
				FROM
					sai_pcuenta punto_cuenta
				WHERE
					".$queryWhere."
			";

//echo $query;

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$arrPuntoCuenta  =array();
			$x = 1;
			
			
			$arrIdsEstatus = array();
			$arrIdsRemitentes = array();
			$arrIdsPresentadoPor = array();
			$arrIdsProveedorSugerido = array();
			$arrIdsProveedorSugerido2 = array();
			$arrIdsPctaAsusnto = array();
			$arrIdsDependenciaQueTramita = array();
			$arrIdsPuntoCuentaImputa = array();
			$arrIdsPuntoCuentaRespaldo = array();

			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				

			$row2[$row['punto_cuenta_id']] = $row;
			
			$arrIdsEstatus[$row['punto_cuenta_id_estatus']] = $row['punto_cuenta_id_estatus'];
	        $arrIdsRemitentes[$row['punto_cuenta_remitente']] = $row['punto_cuenta_remitente'];
		    $arrIdsPresentadoPor[$row['punto_cuenta_presentado_por']] = $row['punto_cuenta_presentado_por'];
			$arrIdsPctaAsusnto[$row['punto_cuenta_id_asunto']] = $row['punto_cuenta_id_asunto'];
			$arrIdsDependenciaQueTramita[$row['punto_cuenta_id_dependencia']] = $row['punto_cuenta_id_dependencia'];
			$arrIdsPuntoCuentaIds[$row['punto_cuenta_id']] = $row['punto_cuenta_id'];
		    
			$cadena = $row['punto_cuenta_rif_proveedor_sugerido'];
            $caracter = "~";
             
             if (strpos($cadena, $caracter) !== false){
             	
               $val1 = explode ('~',$row['punto_cuenta_rif_proveedor_sugerido']);	
             	
              $arrIdsProveedorSugerido2[$row['punto_cuenta_rif_proveedor_sugerido']] = $val1[1];
              
             }else{
             
              $arrIdsProveedorSugerido[$row['punto_cuenta_rif_proveedor_sugerido']] = $row['punto_cuenta_rif_proveedor_sugerido'];
             
             }
			
			}
			
			
		    $estatus = SafiModeloEstatus::GetEstadoPctaIdPcuenta($arrIdsEstatus);
            $remitente = SafiModeloEmpleado::GetEmpleadosByCedulas($arrIdsRemitentes);
		    $presentadoPor = SafiModeloEmpleado::GetEmpleadosByCedulas($arrIdsPresentadoPor);
		    
			$proveedorSugerido  = SafiModeloEmpleado::GetProveedoresSugerido($arrIdsProveedorSugerido);
		
		    $pctaAsunto= SafiModeloPuntoCuentaAsunto::GetPctaAsusntosId($arrIdsPctaAsusnto);
		    
		    $dependenciaQueTramita= SafiModeloDependencia::GetDependenciaByIds($arrIdsDependenciaQueTramita);
		    
		    if(!$filtro){	
            $puntoCuentaImputa =  SafiModeloPuntoCuentaImputa::GetPctaImputasPctaId($arrIdsPuntoCuentaIds); ////// optimizar
		 	$puntoCuentaRespaldo =  SafiModeloPuntoCuentaRespaldo::GetRespaldosDocgIds($arrIdsPuntoCuentaIds);
		 	
		
		    }
		    
		    
		 	
		
            $paramsLlnar = array();     
	
             if($row2){ 
              	
             	foreach ($row2 as $index => $val){

             		$paramsLlnar ['punto_cuenta_id'] = $val['punto_cuenta_id'];
             		$paramsLlnar['asunto'] = $pctaAsunto[$val['punto_cuenta_id_asunto']];

             		$cadena = $val['punto_cuenta_rif_proveedor_sugerido'];
             		
             		$caracter = "~";
             		 
             		$fecha = explode ('/',$val['punto_cuenta_fecha']);

             		
             			$fecha_cambio = strtotime("31-12-2013");
	                $fecha_entrada  = strtotime($fecha[0]."-".$fecha[1]."-".$fecha[2]);

						
             		if($fecha_entrada < $fecha_cambio){

             			if($proveedorSugerido[$val['punto_cuenta_rif_proveedor_sugerido']]){

             			$paramsLlnar['proveedorsugerido'] = $proveedorSugerido[$val['punto_cuenta_rif_proveedor_sugerido']]['id'].":".$proveedorSugerido[$val['punto_cuenta_rif_proveedor_sugerido']]['nombre'];
             			
             			}else{
             			
             			$paramsLlnar['proveedorsugerido'] = $val['punto_cuenta_rif_proveedor_sugerido'];
             			
             			}
             			
             		
             		  
             			
             		}else if(strpos($cadena, $caracter) !== false){



             			$paramsLlnar['proveedorsugerido'] = $arrIdsProveedorSugerido2[$val['punto_cuenta_rif_proveedor_sugerido']];
             			 
             		}else{
         

             			$paramsLlnar['proveedorsugerido'] = $proveedorSugerido[$val['punto_cuenta_rif_proveedor_sugerido']]['id'].":".$proveedorSugerido[$val['punto_cuenta_rif_proveedor_sugerido']]['nombre'];

             		}

             		$paramsLlnar['descripcion'] = $val['punto_cuenta_descripcion'];
             		$paramsLlnar['fecha'] = $val['punto_cuenta_fecha'];
             		$paramsLlnar['remitente'] = $remitente[$val['punto_cuenta_remitente']];
             		$paramsLlnar['presentadopor'] = $presentadoPor[$val['punto_cuenta_presentado_por']];
             		$paramsLlnar['estatus'] = $estatus[$val['punto_cuenta_id_estatus']];
             		$paramsLlnar['usuario'] = $val['punto_cuenta_id_usuario'];
             		$paramsLlnar['dependencia'] = $dependenciaQueTramita[$val['punto_cuenta_id_dependencia']];
             		$paramsLlnar['recursos'] = $val['punto_cuenta_recursos'];
             		$paramsLlnar['observacion'] = $val['punto_cuenta_observacion'];
             		$paramsLlnar['justificacion'] = $val['punto_cuenta_justificacion'];
             		$paramsLlnar['lapso'] = $val['punto_cuenta_lapso'];
             		$paramsLlnar['condicionpago'] = $val['punto_cuenta_condicion_pago'];
             		$paramsLlnar['monto'] = $val['punto_cuenta_monto_solicitado'];
             		$paramsLlnar['garantia'] = $val['punto_cuenta_garantia'];
             		$paramsLlnar['observaciondireccionejecutiva'] = $val['punto_cuenta_observacion_direccion_ejecutiva'];
             		$paramsLlnar['observacionpresidencia'] = $val['punto_cuenta_observacion_presidencia'];
             		$paramsLlnar['descripcionpresupuesto'] = $val['punto_cuenta_descripcion_presupuesto'];
             		$paramsLlnar['pctasociado'] = $val['punto_cuenta_punto_cuenta_asociado'];
             		$paramsLlnar['puctadestinatario'] = $val['punto_cuenta_destinatario'];
             		$paramsLlnar['puntoCuentaImputa'] = $puntoCuentaImputa[$val['punto_cuenta_id']];
             		$paramsLlnar['puntoCuentaRespaldo'] = $puntoCuentaRespaldo[$val['punto_cuenta_id']];
             		$PuntoCuenta[$val['punto_cuenta_id']] = self::LlenarPuntoCuenta($paramsLlnar);


             		
             	}
             }

			return $PuntoCuenta;
			
		}
		catch(Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
      public static function GetProveedorSugerido($params = null)
	{
	
		$query = "
		SELECT
					rif_sugerido AS rif_sugerido
				FROM
					sai_pcuenta
				WHERE
					pcta_id = '".$params."'";
			
		
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) != false){
				
				if($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				
				return $row['rif_sugerido'];
				
				}
				
			}
			
			return false;
				
		
	}
	  ///////////////////////////////////////////////////////////////////////////////////// mudar a compromiso ////////////	
        
					 public static function GetCompromiso($idComp){
				 	
						try {
								
							if($idComp == null || ($idComp=trim($idComp)) == '')
							throw new Exception("Error al obtener el compromiso asociado. Detalles: ".
									"el parámetro idComp es vacío o nulo");
								
							$query = "
								SELECT
								
									pcta_id as pcuanta_id
									
								FROM
									sai_comp 
								WHERE
									comp_id = '".$idComp."'
							";

							if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
							throw new Exception("Error al obtener el pcta asociado. Detalles: ".
							utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
								
							}
							
							if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
								$idPcta = $row['pcuanta_id'];
							}
							
								
							
				
				          return $idPcta;
							
								
						} catch (Exception $e) {
							error_log($e, 0);
							return false;
						}

					}		
					
					
					
						public static function GetCompromisoIdPcuenta(array $params = null){
				 	
						try {
							
							
							if($params === null)
								throw new Exception($preMsg."El parámetro \"params\" es nulo.");
							if(!is_array($params))
								throw new Exception($preMsg."El parámetro \"params\" no es un arreglo.");
							if(count($params) == 0)
								throw new Exception($preMsg."El parámetro \"params\" está vacío.");

							$query = "
								SELECT
								
									pcta_id as pcuanta_id
									
								FROM
									sai_comp 
								WHERE
									pcta_id IN ('".implode("', '", $params)."')
									GROUP BY pcuanta_id ";
							
							if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
							throw new Exception("Error GetCompromisoIdPcuenta . Detalles: ".
							utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
								
							}
							
							while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
								unset($params[$row['pcuanta_id']]);
							}

				          return $params;
						
							
								
						} catch (Exception $e) {
							error_log($e, 0);
							return false;
						}

					}		
					
					
					
					
					
					
	 /////////////////////////////////////////////////////////////////////////////////////-- mudar a compromiso ////////////						
	
	public static function SearchIdsPuntoCuenta(array $params)
	{
		$puntosCuenta = null;
		
		try
		{
			$query = "
				SELECT
					punto_cuenta.pcta_id as punto_cuenta_id
				FROM
					sai_doc_genera doc_genera
					INNER JOIN sai_pcuenta punto_cuenta
						ON (punto_cuenta.pcta_id = doc_genera.docg_id)
				WHERE
					doc_genera.wfob_id_ini = ".$GLOBALS['SafiClassDb']->Quote($params['idObjeto'])."
					AND doc_genera.esta_id = ".$GLOBALS['SafiClassDb']->Quote($params['idEstaus'])."
					AND punto_cuenta.depe_id = '".$GLOBALS['SafiClassDb']->Quote($params['idDependencia'])."'
					AND punto_cuenta.pcta_fecha like '".$GLOBALS['SafiClassDb']->Quote($params['ahnoPresupuesto'])."%'
					AND lower(punto_cuenta.pcta_id) LIKE '%".mb_strtolower($GLOBALS['SafiClassDb']->Quote($params['key']))."%'
				LIMIT
					".$params['numItems']."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al obtener los ids de puntos de cuenta. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
		 	
			
			while($row = $GLOBALS['SafiClassDb']->Fetch($result))
	        {
	        	$puntoCuenta = new EntidadPuntoCuenta();
	        	$puntoCuenta->SetId($row['punto_cuenta_id']);
	        	
	            $puntosCuenta[$row['punto_cuenta_id']] = $puntoCuenta;
	        }
			
	        return $puntosCuenta;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	public static function BuscarIdsPuntoCuenta($codigoDocumento, $idDependencia, $numLimit) {
	    $ids = null;
	    try  {
	        if($codigoDocumento == null || trim($codigoDocumento)=='' || trim($idDependencia) == '' || trim($idDependencia) == null)
	            throw new Exception("Error al buscar los ids de punto de cuenta. Detalles: El código del documento o la dependencia es nulo o vacío");
	
	        $query = "
	            SELECT
	                pcta_id AS id
	            FROM
	                sai_pcuenta
	            WHERE
	                pcta_id LIKE '%".$codigoDocumento."%' AND
	                esta_id <>15 AND
	                pcta_id NOT IN (
	                				SELECT nro_documento
	                				FROM registro_documento
	                				WHERE tipo_documento = 'pcta'
	                				AND id_estado=1
	                				AND user_depe='".$_SESSION['user_depe_id']."'
	                				) AND
	                depe_id='".$idDependencia."' AND
	                pcta_fecha LIKE '".$_SESSION['an_o_presupuesto']."%'  
	            ORDER BY pcta_id 
				LIMIT
				".$numLimit."
	        ";
	        if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
	            throw new Exception("Error al obtener los ids de puntos de cuenta. Detalles: ".
	                utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
	        $ids = array();
	
	        while($row = $GLOBALS['SafiClassDb']->Fetch($result))
	        {
	            $ids[] = $row['id'];
	        }
	
	    }catch(Exception $e){
	        error_log($e, 0);
	    }
	
	    return $ids;
	} 

	public static function obtenerBeneficiario($codigoDocumento) {
		include_once(SAFI_MODELO_PATH . '/compromiso.php' );
	    try  {
	        if($codigoDocumento == null || trim($codigoDocumento)=='')
	            throw new Exception("Error al buscar la información del memorando. Detalles: El código del documento es nulo o vacío");
	
	        $query = "
			SELECT e.empl_cedula || ':' || e.empl_nombres || ' ' || e.empl_apellidos as empleado 
			FROM sai_pcuenta p, sai_empleado e
			WHERE trim(substr(p.rif_sugerido, 1,position(':' in p.rif_sugerido)-1))=e.empl_cedula and p.pcta_id='".$codigoDocumento."'
			--LIMIT 1
			UNION
			SELECT v.benvi_cedula || ':' || v.benvi_nombres || ' ' || v.benvi_apellidos as empleado 
			FROM sai_pcuenta p, sai_viat_benef v
			WHERE trim(substr(p.rif_sugerido, 1,position(':' in p.rif_sugerido)-1))=v.benvi_cedula and p.pcta_id='".$codigoDocumento."'
			--LIMIT 1
			UNION
			SELECT pn.prov_id_rif || ':' || pn.prov_nombre 
			FROM sai_pcuenta p, sai_proveedor_nuevo pn
			WHERE trim(substr(p.rif_sugerido, 1,position(':' in p.rif_sugerido)-1))=pn.prov_id_rif and p.pcta_id='".$codigoDocumento."'
			LIMIT 1
			";
	        if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
	            throw new Exception("Error al obtener el beneficiario del memo".
	                utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	        	$empleado = "";
	            if($row = $GLOBALS['SafiClassDb']->Fetch($result)) 
	        {
	            $empleado = $row['empleado'];
	        }
	
	    }catch(Exception $e){
	        error_log($e, 0);
	    }
	    return $empleado;
	} 		 	

	public static function BuscarInfoPuntoCuenta($codigoDocumento) {
	    try  {
	        if($codigoDocumento == null || trim($codigoDocumento)=='')
	            throw new Exception("Error al buscar los ids de punto de cuenta. Detalles: El código del documento es nulo o vacío");
	
	        $query = "
				SELECT p.pcta_id AS id,
					TO_CHAR(p.pcta_fecha, 'dd-mm-yyyy') AS fecha,
					p.pcta_monto_solicitado AS monto, 
					p.pcta_justificacion AS observaciones
				FROM sai_pcuenta p
				WHERE p.pcta_id =  '".$codigoDocumento."'";

	        if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
	            throw new Exception("Error al obtener los ids de puntos de cuenta".
	                utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
	        $ids = array();
	
	        if($row = $GLOBALS['SafiClassDb']->Fetch($result))
	        {
	            $ids[0] = $row['id'];
	            $ids[1] = '';
	            $ids[2] = $row['monto'];
	            $ids[3] = utf8_encode($row['observaciones']);
	            if(SafiModeloCompromiso::GetCompromisoByIdDocumento($ids[0]))
	            	$ids[4] = SafiModeloCompromiso::GetCompromisoByIdDocumento($ids[0])->GetId();
	            else 
	            	$ids[4] = "comp-400";
	            
	           	$ids[5] = $row['fecha'];
	        }
	
	    }catch(Exception $e){
	        error_log($e, 0);
	    }
	
	    return $ids;
	}
		
	public static function ExistsPuntoCuenta($idPuntoCuenta)
	{
		$exists = false;
		
		try{
			if($idPuntoCuenta == null || ($idPuntoCuenta=trim($idPuntoCuenta))=='')
				return $exists;
				
			$query = "
				SELECT count(*) FROM sai_pcuenta WHERE lower(pcta_id) = '".mb_strtolower($idPuntoCuenta)."'
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al verificar si existe el punto de cuenta: \"".$idPuntoCuenta."\". Detalles: ". 
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					
			if($GLOBALS['SafiClassDb']->FetchOne($result) == 1){
				$exists = true;
			}
			
			return $exists;
			
		} catch (Exception $e){
			error_log($e, 0);
			return false;
		}
	}
	
	public static function PerteneceALaDependenciaElPuntoCuenta($idPuntoCuenta, $idDependencia)
	{
		$exists = false;
		
		try{
			if($idPuntoCuenta == null || ($idPuntoCuenta=trim($idPuntoCuenta))=='')
				return $exists;
				
			if($idDependencia == null || ($idDependencia=trim($idDependencia))=='')
				return $exists;
				
			$query = "
				SELECT
					count(*)
				FROM
					sai_pcuenta
				WHERE
					lower(pcta_id) = '".mb_strtolower($GLOBALS['SafiClassDb']->Quote($idPuntoCuenta))."'
					AND depe_id = '".$idDependencia."'
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al verificar si existe el punto de cuenta: \"".$idPuntoCuenta."\". Detalles: ". 
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					
			if($GLOBALS['SafiClassDb']->FetchOne($result) == 1){
				$exists = true;
			}
			
			return $exists;
			
		} catch (Exception $e){
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetSelectFieldsPuntoCuenta()
	{
		return "
			punto_cuenta.pcta_id AS punto_cuenta_id,
			punto_cuenta.pcta_asunto AS punto_cuenta_id_asunto,
			punto_cuenta.rif_sugerido AS punto_cuenta_rif_proveedor_sugerido,
			punto_cuenta.pcta_descripcion AS punto_cuenta_descripcion,
			to_char(punto_cuenta.pcta_fecha, 'DD/MM/YYYY') AS punto_cuenta_fecha,
			punto_cuenta.pcta_id_remit AS punto_cuenta_remitente,
			punto_cuenta.pcta_id_dest AS punto_cuenta_destinatario,
			punto_cuenta.esta_id AS punto_cuenta_id_estatus,
			punto_cuenta.usua_login AS punto_cuenta_id_usuario,
			punto_cuenta.depe_id AS punto_cuenta_id_dependencia,
			punto_cuenta.pcta_observacion AS punto_cuenta_observacion,
			punto_cuenta.pcta_justificacion AS punto_cuenta_justificacion,
			punto_cuenta.pcta_lapso AS punto_cuenta_lapso,
			punto_cuenta.pcta_cond_pago AS punto_cuenta_condicion_pago,
			punto_cuenta.pcta_monto_solicitado AS punto_cuenta_monto_solicitado,
			punto_cuenta.pcta_garantia AS punto_cuenta_garantia,
			punto_cuenta.observacion1 AS punto_cuenta_observacion_direccion_ejecutiva,
			punto_cuenta.observacion2 AS punto_cuenta_observacion_presidencia,
			punto_cuenta.descripcion_presupuesto AS punto_cuenta_descripcion_presupuesto,
			punto_cuenta.pcta_asociado AS punto_cuenta_punto_cuenta_asociado,
			punto_cuenta.infocentro_id AS punto_cuenta_id_infocentro,
			punto_cuenta.pcta_presentado_por AS punto_cuenta_presentado_por,
			punto_cuenta.recursos AS punto_cuenta_recursos
		";
	}
	
	public static function LlenarPuntoCuenta($paramsLlnar)
	{
		$puntoCuenta = new EntidadPuntoCuenta();
		
		$puntoCuentaAsociado = new EntidadPuntoCuenta();
		
		if($paramsLlnar['pctasociado'] != null){
		$puntoCuentaAsociado->SetId($paramsLlnar['pctasociado']);
		
		}else{
		$puntoCuentaAsociado = null;
		}
		
		
		$infocentro = null;

                 if(isset($paramsLlnar['usuario']) && ($idUsuario=$paramsLlnar['usuario']) !== null){

			$usuario = new EntidadUsuario();
			$usuario->SetId($paramsLlnar['usuario']);
		}else{
                 $usuario= null;

         }
         
	
		$puntoCuenta->SetId($paramsLlnar['punto_cuenta_id']);
		$puntoCuenta->SetAsunto($paramsLlnar['asunto']);
		$puntoCuenta->SetRifProveedorSugerido($paramsLlnar['proveedorsugerido']);
		$puntoCuenta->SetDescripcion($paramsLlnar['descripcion']);
		$puntoCuenta->SetFecha($paramsLlnar['fecha']);
		$puntoCuenta->SetRemitente($paramsLlnar['remitente']);
		$puntoCuenta->SetPresentadoPor($paramsLlnar['presentadopor']);
		$puntoCuenta->SetEstatus($paramsLlnar['estatus']);
		$puntoCuenta->SetUsuario($usuario);
		$puntoCuenta->SetDependencia($paramsLlnar['dependencia']);
		$puntoCuenta->SetRecursos($paramsLlnar['recursos']);
		$puntoCuenta->SetDestinatario($paramsLlnar['puctadestinatario']);
		$puntoCuenta->SetObservacion($paramsLlnar['observacion']);
		$puntoCuenta->SetJustificacion($paramsLlnar['justificacion']);
		$puntoCuenta->SetLapso($paramsLlnar['lapso']);
		$puntoCuenta->SetCondicionPago($paramsLlnar['condicionpago']);
		$puntoCuenta->SetMontoSolicitado($paramsLlnar['monto']);
		$puntoCuenta->SetGarantia($paramsLlnar['garantia']);
		$puntoCuenta->SetObservacioneDireccionEjecutiva($paramsLlnar['observaciondireccionejecutiva']);
		$puntoCuenta->SetObservacionPresidencia($paramsLlnar['observacionpresidencia']);
		$puntoCuenta->SetDescripcionPresupuesto($paramsLlnar['descripcionpresupuesto']);
		
		$puntoCuenta->SetPuntoCuentaAsociado($puntoCuentaAsociado);
		$puntoCuenta->SetInfocentro($infocentro);
	    $puntoCuenta->SetPuntoCuentasImputas($paramsLlnar['puntoCuentaImputa']);
	    $puntoCuenta->SetPuntoCuentasRespaldos($paramsLlnar['puntoCuentaRespaldo']);
	    
	    
	    
         	return $puntoCuenta;
         	
         	
         	
	}
	
	
	
	
      public static  function InsertPcuenta($params){
 
		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){
			
				  $preMsg = "error al insertar punto de cuenta.";
                     if($params['fecha']){
                     $fecha = explode ('/',$params['fecha']);
				     $fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
			        $fecha3  =  $fecha[0].'-'.$fecha[1].'-'.$fecha[2];
                     }
                     
                     
                     if($params['estatus']){
                     	
                    $estatus = 	$params['estatus'];
                     
                     }else{
                     
                       $estatus =  '10';
                     }

							$query = "
	                                   INSERT INTO sai_pcuenta 
	                                   (pcta_id,  
	                                    pcta_asunto,
	                                    rif_sugerido,
	                                    pcta_descripcion,
	                                    pcta_fecha,
	                                    pcta_id_remit, 
	                                    pcta_id_dest,
	                                    esta_id,
	                                    usua_login,
	                                    depe_id,
	                                    pcta_observacion,
	                                    pcta_justificacion,
	                                    pcta_lapso,
	                                    pcta_monto_solicitado,
	                                    pcta_prioridad,
	                                    numero_reserva, 
	                                    pcta_gerencia, 
	                                    pcta_presentado_por, 
	                                    recursos,
	                                    pcta_garantia,
	                                    pcta_asociado,
	                                    pcta_cond_pago)
	                                    
										VALUES (
											
										'".$params['pcta_id']."',
										'".$params['pctaAsunto']."',
										'".$params['ProveedorSugeridoval']."',	
										'".$params['pcuenta_descripcion']."',
										";
							
								$query .= $fecha2 != false? "'".$fecha2."'," : "now()," ;
							
							 $query .= "
										'".$params['SolicitadoPor']."',
										'".$params['preparado_para']."',
										'".$estatus."',
										'".$_SESSION['login']."',
										'".$GLOBALS['SafiClassDb']->Quote($params['DependenciaTramita'])."',
										'".$GLOBALS['SafiClassDb']->Quote($params['observaciones'])."',
										'".$GLOBALS['SafiClassDb']->Quote($params['justificacion'])."',
										'".$GLOBALS['SafiClassDb']->Quote($params['convenio'])."',
										".$GLOBALS['SafiClassDb']->Quote($params['montoTotal']).",
										1,
										'',
										'".$GLOBALS['SafiClassDb']->Quote($params['DependenciaTramita'])."',
										'".$GLOBALS['SafiClassDb']->Quote($params['presentado_por'])."',
										".$GLOBALS['SafiClassDb']->Quote($params['recursos']).",
										'".$GLOBALS['SafiClassDb']->Quote($params['garantia'])."',
										'".$GLOBALS['SafiClassDb']->Quote($params['pctaAsociado'])."',
										'".$GLOBALS['SafiClassDb']->Quote($params['cond_pago'])."');
										";
		
                     $result = $GLOBALS['SafiClassDb']->Query($query);

					 if($result === false) throw new Exception('Error al insertar. Detalles punto de cuenta: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());  


				     if( $params['recursos'] > 0){
				     
						 	
                       $result	= SafiModeloPuntoCuentaImputa::InsertPctaImputa($params);

					    if($result === false) throw new Exception('Error al insertar. Detalles imputa: ' . $GLOBALS['SafiClassDb']->GetErrorMsg()); 
					    
						}
						
						if($params['Fisico']){
        
                       $result = SafiModeloPuntoCuentaRespaldo::InsertPctaRespaldo($params,'Fisico'); 
                         
                         if($result === false) throw new Exception('Error al insertar. Detalles respaldo: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
						
						}
						
			        if($params['Digital']){

                   $result = SafiModeloPuntoCuentaRespaldo::InsertPctaRespaldo($params,'Digital');
                    
                    if($result === false) throw new Exception('Error al insertar. Detalles digital: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
     
			        }
			        

    
		$data = array();
		$dateTime = new DateTime();
		
	    $fecha = (String) $dateTime->format('y-m-d h:m:s');
		$data['docg_id'] = $params['pcta_id'] ;
		$data['docg_wfob_id_ini'] = $params['docg_wfob_id_ini'] != false ? $params['docg_wfob_id_ini'] :  0 ;
		$data['docg_wfca_id'] = $params['CadenaIdcadena'] ;
		$data['docg_usua_login'] = $_SESSION['login'];
		$data['docg_perf_id'] =  $params['IdPerfil']  != false ? $params['IdPerfil'] : $_SESSION['user_perfil_id'] ;
		$data['docg_fecha'] =  	$fecha3.' '.strftime('%H:%M:%S');
		$data['docg_esta_id'] = $params['docg_esta_id'] != false ? $params['docg_esta_id'] :59 ;
		$data['docg_prioridad'] = 1 ;
		$data['docg_perf_id_act'] = $params['PerfilSiguiente'] ;
		$data['docg_estado_pres'] = '' ;
		$data['docg_numero_reserva'] =  '' ;
		$data['docg_fuente_finan'] = '' ;
		

		$docGenera = SafiModeloDocGenera::LlenarDocGenera($data);
		
	
	$result = SafiModeloDocGenera::GuardarDocGenera($docGenera);
		
		
		
	    if($result === false) throw new Exception('Error al insertar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
	

				$result = $GLOBALS['SafiClassDb']->CommitTransaction();
				
				
				 
			
				return true;
				
			} else {
				throw new Exception('Error al iniciar la transacci&oacute;n');
			}			
		}catch(Exception $e){
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}
	
	}

	
	 public static function	UpdatePcuentaEstaId($params){
	 

	 	 $query = "UPDATE
				       sai_pcuenta
				     
				   SET 
	                  esta_id = ".$params['estaid']."
	                 
				   WHERE	
					   pcta_id='".$params['idPcta']."'";
	 	 
				            
	     $result = $GLOBALS['SafiClassDb']->Query($query);
	     
	     if($result){
	     	
	     	  return $result;
	     
	     }else{
	     
	     return false;
	     
	     }
	     

	 }
	 
 public static function	UpdatePcuentasEstaId($params){
	 try
		{
			      
		$insertoRevision = true;
		$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
	    if($params){  
 	
	 	foreach ($params as $valor){
	 		
	 	if($insertoRevision != false){	
	 		
	 	}
	 	 $query = "UPDATE
				       sai_pcuenta
				     
				   SET 
	                  esta_id = ".$valor['estaid']."
	                 
				   WHERE	
					   pcta_id='".$valor['idPcta']."'";
	 	 
	 	 
           if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
				
				throw new Exception('Error al  anular pcuenta('.$docGenera->Getid().')');
				$insertoRevision =  $result;
				
				}
					 

	 	}
	 	 
	    }else{
		   	
		   $insertoRevision = false;
		   
		   }
	
		if($insertoRevision != true){
			
				throw new Exception('Error al anular pcuenta' . $GLOBALS['SafiClassDb']->GetErrorMsg());
				
				break;
				} 
 
			if($GLOBALS['SafiClassDb']->CommitTransaction() === false)
				throw new Exception('Error al intentar hacer commit. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
				
			return true;	   
		   	   
     }
     catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
     
	 }
	
	
	  public static  function InsertPctaTraza($params,$fecha3){
		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){

				$preMsg = "Error al insertar el pcta traza.";

				$valTransaccion = true;

			 $objPcta =  self::GetPuntoCuenta(array("idPuntoCuenta" => $params ));
	 	     
             
	 	 if($objPcta){
 	
 	$pcta = $objPcta  != null? $objPcta->GetId()  : '';
    $asunto = $objPcta->GetAsunto() != null? $objPcta->GetAsunto()->GetId() : '';
    $proveedorSugerido = SafiModeloPuntoCuenta::GetProveedorSugerido($pcta);
    $descripcion = $objPcta != null? $objPcta->GetDescripcion() : '';
    $fechas = $objPcta  != null? $objPcta->GetFecha()  : '';
    $solicitadoPor = $objPcta->GetRemitente() != null? $objPcta->GetRemitente()->GetId() : '';
    $estatus = $objPcta->GetEstatus() != null? $objPcta->GetEstatus()->GetId() : '';
    $destinatario = $objPcta  != null? $objPcta->GetDestinatario()  : '';
    $usuario = $objPcta->GetUsuario() != null? $objPcta->GetUsuario()->GetId(): '';
    $dependencia = $objPcta->GetDependencia() != null? $objPcta->GetDependencia()->GetId() : '';
    $observacion = $objPcta  != null? $objPcta->GetObservacion()  : '';
    $justificacion = $objPcta != null? $objPcta->GetJustificacion() : '';
    $lapso = $objPcta != null? $objPcta->GetLapso() : '';
    $condicionPago =  $objPcta != null? $objPcta->GetCondicionPago() : '';
    $montoSolicitado =  $objPcta != null? $objPcta->GetMontoSolicitado()  : 0;
    $presentadoPor = $objPcta->GetPresentadoPor() != null? $objPcta->GetPresentadoPor()->GetId() : '';
    $recursos = $objPcta != null? $objPcta->GetRecursos() : '';
 	$garantia = $objPcta != null? $objPcta->GetGarantia()  : '';
    $pctaAsociado = $objPcta->GetPuntoCuentaAsociado()  != null? $objPcta->GetPuntoCuentaAsociado()->GetId() : '';
    
   
    $fecha = explode ('/',$fechas);
	$fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
    
 }

             $query = "

	                                   INSERT INTO sai_pcta_traza
	                                   (pcta_id, 
	                                    pcta_asunto,
	                                    rif_sugerido,
	                                    pcta_descripcion,
	                                    pcta_fecha,
	                                    pcta_id_remit, 
	                                    pcta_id_dest,
	                                    esta_id,
	                                    usua_login,
	                                    depe_id,
	                                    pcta_observacion,
	                                    pcta_justificacion,
	                                    pcta_lapso,
	                                    pcta_monto_solicitado,
	                                    pcta_prioridad,
	                                    numero_reserva, 
	                                    pcta_gerencia, 
	                                    pcta_presentado_por, 
	                                    recursos,
	                                    pcta_garantia,
	                                    pcta_asociado,
	                                    pcta_cond_pago,
	                                    pcta_fecha2)
	                                    
										VALUES (
											
										'".$pcta."',
										'".$asunto."',
										'".$proveedorSugerido."',	
										'".$descripcion."',
										'".$fecha2."',
										'".$solicitadoPor."',
										'".$destinatario."',
										".$estatus.",
										'".$usuario."',
										'".$dependencia."',
										'".$observacion."',
										'".$justificacion."',
										'".$lapso."',
										 ".$montoSolicitado.",
										1,
										'',
										'".$dependencia."',
										'".$presentadoPor."',
										".$recursos.",
										'".$garantia."',
										'".$pctaAsociado."',
										'".$condicionPago."',
										'".$fecha3."');
										";

				   
                      $valTransaccion = $GLOBALS['SafiClassDb']->Query($query);
                      
                      
                      
						if( $valTransaccion === false){

							break;
						}



				if($valTransaccion === false) throw new Exception('Error al insertar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());  
				$valTransaccion = $GLOBALS['SafiClassDb']->CommitTransaction();

				return true;

			} else {
				throw new Exception('Error al iniciar la transacci&oacute;n');
			}
		}catch(Exception $e){
			$valTransaccion = $GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			
			return false;
			
		}

	}
	 
	

	
 public static function	UpdatePcuenta($params){
 	

 		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){
				
				$fecha = explode ('/',$params['fecha']);
				     $fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
				     $fecha3  =  $fecha[0].'-'.$fecha[1].'-'.$fecha[2];
				
				            $query = "UPDATE
				         	             sai_pcuenta
				         	             
				                      SET 
				                      
	                                    pcta_asunto = '".$params['pctaAsunto']."',
	                                    pcta_asociado = '".$params['pctaAsociado']."',
	                                    rif_sugerido = '".$params['ProveedorSugeridoval']."',	
	                                    pcta_descripcion = '".$params['pcuenta_descripcion']."',
	                                    pcta_fecha = '".$fecha2."',
	                                    pcta_id_remit = '".$params['SolicitadoPor']."',
	                                    pcta_id_dest = '".$params['preparado_para']."',
	                                    usua_login = '".$_SESSION['login']."',
	                                    depe_id = '".$params['DependenciaTramita']."',
	                                    pcta_observacion = '".$params['observaciones']."',
	                                    pcta_justificacion = '".$params['justificacion']."',
	                                    pcta_lapso = '".$params['convenio']."',
	                                    pcta_monto_solicitado = ".$params['montoTotal'].",
	                                    pcta_gerencia = '".$params['DependenciaTramita']."',
	                                    pcta_presentado_por = '".$params['presentado_por']."',
	                                    recursos = ".$params['recursos'].",
	                                    pcta_garantia = '".$params['garantia']."',
	                                    pcta_cond_pago = '".$params['cond_pago']."'
	                                    
					                 WHERE	
					                    pcta_id='".$params['idPcta']."'";
				            

        $result = $GLOBALS['SafiClassDb']->Query($query);
                
	    if($result === false) throw new Exception('Error al Modificar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());	 

	    
	 
       //  error_log($query);
					  
					  
		$result	= SafiModeloPuntoCuentaImputa::EliminarPctaImputaIdPcta($params['idPcta']);
	    
	     if($result === false) throw new Exception('Error al eliminar. Detalles imputa: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());  	
	      
	      
			 if( $params['recursos'] > 0){
				      	
					    $result	= SafiModeloPuntoCuentaImputa::InsertPctaImputa($params);
					    
					    if($result === false) throw new Exception('Error al insertar. Detalles imputa: ' . $GLOBALS['SafiClassDb']->GetErrorMsg()); 
					    
			  }
				
			 
			   if($params['regisFisDigiEli']){

			    $result	= SafiModeloPuntoCuentaRespaldo::EliminarRespaldoResp($params['regisFisDigiEli']);
			    
			     if($result === false) throw new Exception('Error al eliminar. Detalles respaldo: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			 
			   }
			   
			   
			   if($params['Fisico']){
        
                         $result = SafiModeloPuntoCuentaRespaldo::InsertPctaRespaldo($params,'Fisico'); 
                         
                         if($result === false) throw new Exception('Error al insertar. Detalles respaldo: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
						
						}
						
			        if($params['Digital']){

                    $result = SafiModeloPuntoCuentaRespaldo::InsertPctaRespaldo($params,'Digital');
                    
                    if($result === false) throw new Exception('Error al insertar. Detalles digital: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
     
			        }
			        
			        
			        
			        $entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($params['idPcta']);
			        $entidadDocg->SetFecha($fecha3.' '.strftime('%H:%M:%S'));
			       

     			$result = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
			    if($result === false) throw new Exception('Error al modificar docg. Detalles digital: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());  


			        
			     
			  
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
				
				return true;
				
			} else {
				throw new Exception('Error al iniciar la transacci&oacute;n');
			}			
		}catch(Exception $e){
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}
 

 }
 

  public static function GetIdperfil(array $params = null)
	{
	
       $data = array();
       
	   $query = "
	   SELECT empl_cedula
       FROM  sai_empleado
       WHERE 
       esta_id= 1 
       and carg_fundacion= ".$params['perfil'];

		$result = $GLOBALS['SafiClassDb']->Query($query);
		if($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

        
        return $row['empl_cedula'];
        
		}

	}
	
 public static function GetIdPctaAlcance($param = null,$finalizado = null)
	{
		
		
     $data = array();
	   $query = "
	   SELECT 
	      pcta_id
	      
       FROM  
         sai_pcuenta
       
       WHERE 
          pcta_asociado= '".$param."' AND
          pcta_asunto = '013'";
	   
        $finalizado != null ? $query .= "AND esta_id = '13'" : ''; 

		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			
		while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			
          $data[] = $row['pcta_id'];
          
          
		}
		
		}else{
			
		 return false;
		 
		 
		}
		
         return $data;
         
          
   	    	
	}
	
	
	
	
public static function GetPuntosCuentaAsociados($idDepedencia)
	{
	
		
	$queryWhere=" pc.depe_id = '".$idDepedencia."' AND
	              pc.pcta_asunto != '013' AND
	              dg.esta_id != '15'AND
	              pc.pcta_asunto != '020' AND
	              dg.wfob_id_ini = 99 AND
	              pc.pcta_id like '%".substr($_SESSION['an_o_presupuesto'],2,3)."'
	              
	              
	              ";	
		         
	
	$query = "
				SELECT
					pc.pcta_id,
					dg.wfob_id_ini
				FROM
					sai_pcuenta pc
					INNER JOIN sai_doc_genera dg ON (dg.docg_id = pc.pcta_id)
				WHERE
					".$queryWhere."
			";

	
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
	       while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
              $PuntoCuenta[$row['pcta_id']] = $row['pcta_id'];	
		
			}
            
	    return $PuntoCuenta;

	
	   }else{
	   
	   	return false;
	}}
	
	
	
public static function GetPuntosCuentasAsociadosId($params)


	{
	
		
	$queryWhere=" pc.depe_id = '".$params['Dependencia']."' AND
	              pc.pcta_asunto != '013' AND
	              dg.esta_id != '15'AND
	              pc.pcta_asunto != '020' AND
	              dg.wfob_id_ini = 99 AND
	              pc.pcta_id like '%".substr($_SESSION['an_o_presupuesto'],2,3)."' AND
	               pc.pcta_id like '%".$params['key']."%'
	              
	              
	              ";	
		         
	
	$query = "
				SELECT
					pc.pcta_id,
					dg.wfob_id_ini
				FROM
					sai_pcuenta pc
					INNER JOIN sai_doc_genera dg ON (dg.docg_id = pc.pcta_id)
				WHERE
					".$queryWhere."
			";
	
	//error_log(print_r($query,true));

		if($params){
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
	       while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
              $PuntoCuenta[$row['pcta_id']] = $row['pcta_id'];	
		
			}
            
	    return $PuntoCuenta;

	
	   }else{
	   
	   	return false;
	}
	
	
		}
	}
	
	
public static function GetPuntosCuentaALiberar($params)
	{
	
		
	$queryWhere=" 
	              pc.pcta_asunto != '013' AND
	              dg.esta_id != '15'AND
	              pc.pcta_asunto != '020' AND
	              dg.wfob_id_ini = 99 AND
	              pc.pcta_id  IN ('".implode("', '", $params)."')  ";	
		         
	
	$query = "
				SELECT
					pc.pcta_id
				FROM
					sai_pcuenta pc
					INNER JOIN sai_doc_genera dg ON (dg.docg_id = pc.pcta_id)
				WHERE
					".$queryWhere."
			";

		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
	       while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
              $PuntoCuenta[$row['pcta_id']] = $row['pcta_id'];	
		
			}
            
	    return $PuntoCuenta;

	
	   }else{
	   
	   	return false;
	}}
	
	
/*última versión de reporte integrado disponibilidad*/
	public static function GetPctaDisponibilidad($params)
	{
		
		try
		{

		    $row2 = array();
            $arrIdsProveedorSugerido2 = array();
			$preMsg = "Error al intentar obtener los puntos de cuenta.";
			$existeCriterio = false;
			$arrMsg = array();
			$categoriaProgramatica = explode("/", $params['proy_acc']);
			$idProyAcc = $categoriaProgramatica[0];
			$idEspecifica = $categoriaProgramatica[1];

			$arrPtoCuenta = array();
			$arrPtoCuentaPartidas = array();
	
			$queryWhere = "";
			$arrPuntoCuenta = null;
			$arrPuntoCuentaPartidas = null;
	
			if($params === null)
				throw new Exception($preMsg."El parámetro \"params\" es nulo.");
			if(!is_array($params))
				throw new Exception($preMsg."El parámetro \"params\" no es un arreglo.");
			if(count($params) == 0)
				throw new Exception($preMsg."El parámetro \"params\" está vacío.");
			else {
				$existeCriterio = true;
			}

			if(isset($params['pcta_id']) && strlen($params['pcta_id']) > 7){
				
				
				$queryWhere = " p.pcta_id = '".$params['pcta_id']."'";
				
				
			}else if(strlen($categoriaProgramatica[0]) > 2){
				$queryWhere = "(
								(spae.proy_id = '".$idProyAcc."' AND spae.paes_id = '".$idEspecifica."') OR
								(sae.acce_id = '".$idProyAcc."' AND sae.aces_id = '".$idEspecifica."')
								) AND
								(spae.pres_anno = ".$params['anoPresupuesto']." OR sae.pres_anno = ".$params['anoPresupuesto'].")";
				$queryWhereProy = " AND spae.proy_id = '".$idProyAcc."' AND spae.paes_id = '".$idEspecifica."'";
				$queryWhereAcc = "AND sae.acce_id = '".$idProyAcc."' AND sae.aces_id = '".$idEspecifica."'";
				
				
	
				
			}			
				
			if(!$existeCriterio){
				throw new Exception($preMsg." No existe criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}
			else {
				
				$query = "SELECT
							p.pcta_id AS pcta_id,
							p.descripcion_presupuesto AS descripcion_presupuesto,
							TO_CHAR(p.pcta_fecha, 'DD/MM/YYYY') AS pcta_fecha,
							p.rif_sugerido AS rif_sugerido,
							p.infocentro_id AS infocentro_id,
							inf.nombre AS infocentro_nombre,
							edo.nombre AS estado_nombre,
							COALESCE(spae.centro_gestor,'') || '/' || COALESCE(spae.centro_costo,'')  ||  COALESCE(sae.centro_gestor,'') || '/' || COALESCE(sae.centro_costo,'') AS proy_acc,
							COALESCE(spae.proy_id,'') || '/' || COALESCE(spae.paes_id,'')  || COALESCE(sae.acce_id,'') || '/' || COALESCE(sae.aces_id,'') AS id_proy_acc,
							au.pcas_nombre AS asunto,
							p.pcta_asunto
						FROM
							sai_pcuenta p
							INNER JOIN sai_doc_genera sdg ON (p.pcta_id = sdg.docg_id)
							INNER JOIN sai_pcta_asunt au ON (au.pcas_id = p.pcta_asunto)
							LEFT OUTER JOIN safi_infocentro inf ON (inf.nemotecnico = p.infocentro_id)
							LEFT OUTER JOIN safi_edos_venezuela edo ON (edo.id = inf.edo_id)
							LEFT OUTER JOIN sai_pcta_imputa pi ON (pi.pcta_id = p.pcta_id)
							LEFT OUTER JOIN sai_proy_a_esp spae ON (spae.proy_id = pi.pcta_acc_pp AND spae.paes_id = pi.pcta_acc_esp AND spae.pres_anno = pi.pres_anno ".$queryWhereProy.")
							LEFT OUTER JOIN sai_acce_esp sae ON (sae.acce_id = pi.pcta_acc_pp AND sae.aces_id = pi.pcta_acc_esp AND sae.pres_anno = pi.pres_anno ".$queryWhereAcc.")
						WHERE
							(sdg.esta_id = 13 OR sdg.perf_id_act = ".PERFIL_DIRECTOR_EJECUTIVO." OR sdg.perf_id_act = ".PERFIL_PRESIDENTE.") AND
							p.pcta_asunto != '013' AND
							p.pcta_asunto != '039'  ";

			if($queryWhere != ''){
				
					$query .= ' AND '.$queryWhere;
				
				}
				         
				       
							
				
	     

			
                  
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
				$cadenaPcta = "";
				while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$row2[$row["pcta_id"]] = $row;
					
					if ($row["pcta_asunto"] != '013' && $row["pcta_asunto"] != '020' && $row["pcta_asunto"] != '039')
						$cadenaPcta .= "'".$row["pcta_id"]."',";
						
					$arrPtoCuenta[$row["pcta_id"]]["pcta_id"] = $row["pcta_id"];
					$arrPtoCuenta[$row["pcta_id"]]["asunto"] = $row["asunto"];
					$arrPtoCuenta[$row["pcta_id"]]["descripcion_presupuesto"] = $row["descripcion_presupuesto"];
					$arrPtoCuenta[$row["pcta_id"]]["pcta_fecha"] = $row["pcta_fecha"];
					
				    $cadena = $row['rif_sugerido'];
                    $caracter = "~";
             
                     if (strpos($cadena, $caracter) !== false){
             	
                         $val1 = explode ('~',$row['rif_sugerido']);

                          $arrIdsProveedorSugerido[$row['rif_sugerido']] = $val1[1];
              
                       }else{
             
                        $arrIdsProveedorSugerido[$row['rif_sugerido']] = $row['rif_sugerido'];
             
                        }
			

                        
					
					$arrPtoCuenta[$row["pcta_id"]]["rif_sugerido"] = $row["rif_sugerido"];
					$arrPtoCuenta[$row["pcta_id"]]["infocentro_id"] = $row["infocentro_id"];
					$arrPtoCuenta[$row["pcta_id"]]["infocentro_nombre"] = $row["infocentro_nombre"];
					$arrPtoCuenta[$row["pcta_id"]]["estado_nombre"] = $row["estado_nombre"];
					$arrPtoCuenta[$row["pcta_id"]]["proy_acc"] = $row["proy_acc"];
					$arrPtoCuenta[$row["pcta_id"]]["partidas"] = array();	
					
				}
				
				


				if($row2){
								

					$proveedorSugerido  = SafiModeloEmpleado::GetProveedoresSugerido($arrIdsProveedorSugerido);
					
					foreach ($row2 as $index => $val){
						$cadena = $val['rif_sugerido'];
						$caracter = "~";

						$fecha = explode ('/',$val['pcta_fecha']);
						$agno  = (int)$fecha[2];
						
						

						if($agno <= 2012){

							if($proveedorSugerido[$val['rif_sugerido']]){

								$arrPtoCuenta[$index]["rif_sugerido"] = $proveedorSugerido[$val['rif_sugerido']]['nombre'];

							}else{

								$arrPtoCuenta[$index]["rif_sugerido"] = $val['rif_sugerido'];

							}

							 
							 

						}else if(strpos($cadena, $caracter) !== false){


							$arrPtoCuenta[$index]["rif_sugerido"] = $arrIdsProveedorSugerido[$val['rif_sugerido']];
							 
						}else{

							$arrPtoCuenta[$index]["rif_sugerido"] = $proveedorSugerido[$val['rif_sugerido']]['nombre'];

						}

					}
				}

				
				if($arrPtoCuenta){
				
				$cadenaPcta = substr($cadenaPcta,0,strlen($cadenaPcta)-1);
	
				//Apartado Pcta + Alcances
				$query=
					"SELECT
							s.pcta_id,
							s.part_id,
							SUM(s.monto_apartado) AS monto_apartado
					FROM (
						SELECT
							spi.pcta_id,
							spi.pcta_sub_espe AS part_id,
							SUM(spi.pcta_monto) AS monto_apartado
						FROM
							sai_pcta_imputa spi
							INNER JOIN sai_doc_genera sdg ON (sdg.docg_id = spi.pcta_id)
						WHERE
								spi.pcta_id IN ($cadenaPcta) AND
								sdg.esta_id = 13
						GROUP BY spi.pcta_id, spi.pcta_sub_espe
						UNION ALL
						SELECT
							sp.pcta_asociado AS pcta_id,
							spi.pcta_sub_espe AS part_id,
							SUM(spi.pcta_monto) AS monto_apartado
						FROM
							sai_pcta_imputa spi
						INNER JOIN sai_pcuenta sp ON (sp.pcta_id=spi.pcta_id)
						INNER JOIN sai_doc_genera sdg ON (sdg.docg_id = spi.pcta_id)
						WHERE
								sp.pcta_asociado IN ($cadenaPcta) AND
								(sdg.esta_id = 13 /* OR sdg.perf_id_act = '47350' OR sdg.perf_id_act = '38150'*/)
						GROUP BY sp.pcta_asociado, spi.pcta_sub_espe
					) AS s
					GROUP BY s.pcta_id, s.part_id
					ORDER BY s.pcta_id, s.part_id";
				
				

				
				
					
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					
				$i = 0;
				while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					
					$arrPtoCuenta[$row["pcta_id"]]["partidas"][$row["part_id"]]=array();
					$arrPtoCuenta[$row["pcta_id"]]["partidas"][$row["part_id"]]["partida"]=$row["part_id"];
					$arrPtoCuenta[$row["pcta_id"]]["partidas"][$row["part_id"]]["montoApartado"] = $row["monto_apartado"];
				}

				foreach ($arrPtoCuenta as $arrPartida) {
					$pcta = $arrPartida['pcta_id'];
					foreach ($arrPartida['partidas'] as $partidas) {
						/*comprometido*/
						$query=	"SELECT
									scoi.comp_sub_espe AS part_id,
									SUM(scoi.comp_monto) AS monto_comprometido
								FROM sai_comp sco
									INNER JOIN sai_comp_imputa scoi ON (sco.comp_id = scoi.comp_id)
								WHERE
									sco.pcta_id ='".$pcta."' AND
									sco.esta_id != 15 AND sco.esta_id != 2 AND
									scoi.comp_sub_espe LIKE '".$partidas['partida']."%'
								GROUP BY scoi.comp_sub_espe
								ORDER BY 1";

						if(($resultComp = $GLOBALS['SafiClassDb']->Query($query)) === false)
							throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
								
						if ($rowComp = $GLOBALS['SafiClassDb']->Fetch($resultComp)) {
							$arrPtoCuenta[$pcta]["partidas"][$partidas['partida']]["montoComprometido"] = $rowComp["monto_comprometido"];
						}
						/*Fin monto compromiso*/
							
						//causados
						$queryC =	"SELECT 
										scd.part_id AS part_id, 
										COALESCE(SUM(scd.cadt_monto),0) AS monto_causado 
									FROM 
										sai_causado sc 
										INNER JOIN sai_causad_det scd ON (sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno)
										INNER JOIN sai_sol_pago ssp ON (ssp.sopg_id = sc.caus_docu_id)
										INNER JOIN sai_comp  scomp ON (ssp.comp_id=scomp.comp_id)
									WHERE 
										scomp.pcta_id='".$pcta."' AND";
									
						if(!isset($params['pcta_id'])){
							
							$queryC .= "sc.pres_anno = ".$params['anoPresupuesto']." AND ";
									
						}				
								$queryC .= "

										sc.esta_id != 15 AND 
										sc.esta_id != 2 AND 
										scd.part_id LIKE '".$partidas['partida']."%' 
									GROUP BY scd.part_id 
									UNION ALL 
									SELECT 
										scd.part_id AS part_id, 
										COALESCE(SUM(scd.cadt_monto),0) AS monto_causado 
									FROM 
										sai_causado sc 
										INNER JOIN sai_causad_det scd ON (sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno)
										INNER JOIN sai_codi scodi ON (scodi.comp_id=sc.caus_docu_id)
										INNER JOIN sai_comp  scomp ON (scodi.nro_compromiso=scomp.comp_id)
									WHERE 
										scomp.pcta_id='".$pcta."' AND ";
									
						if(!isset($params['pcta_id'])){
							
							$queryC .= "sc.pres_anno = ".$params['anoPresupuesto']." AND ";
									
						}				
								$queryC .= "
										sc.esta_id != 15 AND 
										sc.esta_id != 2 AND 
										scd.part_id LIKE '".$partidas['partida']."%' 
									GROUP BY scd.part_id 
									ORDER BY 1";
							
							$query = "SELECT
									n.part_id,
									SUM(n.monto_causado) AS monto_causado
								FROM (".$queryC.") n
								GROUP BY 1";								
							
						if(($resultComp = $GLOBALS['SafiClassDb']->Query($query)) === false)
							throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
								
						if ($rowComp = $GLOBALS['SafiClassDb']->Fetch($resultComp)) {
							$arrPtoCuenta[$pcta]["partidas"][$partidas['partida']]["montoCausado"] = $rowComp["monto_causado"];
						}
						/*Fin monto causado*/
							
						//pagado
						$queryP =	"SELECT 
									spd.part_id AS part_id, 
									COALESCE(SUM(spd.padt_monto),0) AS monto_pagado 
								FROM sai_pagado sp 
									INNER JOIN sai_pagado_dt spd ON (sp.paga_id = spd.paga_id AND sp.pres_anno = spd.pres_anno) 
									INNER JOIN sai_pago_cheque spc ON (spc.pgch_id = sp.paga_docu_id) 
									INNER JOIN sai_sol_pago ssp ON (spc.docg_id = ssp.sopg_id)
									INNER JOIN sai_comp sc ON (ssp.comp_id = sc.comp_id)
								WHERE 
									sc.pcta_id= '".$pcta."' AND ";
									
						if(!isset($params['pcta_id'])){
							
							$queryP .= "sc.pres_anno = ".$params['anoPresupuesto']." AND ";
									
						}				
								$queryP .= "
									
									sp.esta_id != 15 AND 
									sp.esta_id != 2 AND 
									spd.part_id LIKE '".$partidas['partida']."%' 
								GROUP BY spd.part_id 
								UNION ALL 
								SELECT 
									spd.part_id AS part_id, 
									COALESCE(SUM(spd.padt_monto),0) AS monto_pagado 
								FROM sai_pagado sp
									INNER JOIN sai_pagado_dt spd ON (sp.paga_id = spd.paga_id AND sp.pres_anno = spd.pres_anno)
									INNER JOIN sai_pago_transferencia spt ON (spt.trans_id = sp.paga_docu_id)
									INNER JOIN sai_sol_pago ssp ON (spt.docg_id = ssp.sopg_id)
									INNER JOIN sai_comp sc ON (ssp.comp_id = sc.comp_id)
								WHERE 
									sc.pcta_id= '".$pcta."' AND ";
									
						if(!isset($params['pcta_id'])){
							
							$queryP .= "sp.pres_anno = ".$params['anoPresupuesto']." AND ";
									
						}				
								$queryP .= "sp.esta_id != 15 AND 
									sp.esta_id != 2 AND 
									spd.part_id LIKE '".$partidas['partida']."%' 
								GROUP BY spd.part_id 
								UNION ALL 
								SELECT 
									spd.part_id AS part_id, 
									COALESCE(SUM(spd.padt_monto),0) AS monto_pagado 
								FROM sai_pagado sp
									INNER JOIN sai_pagado_dt spd ON (sp.paga_id = spd.paga_id AND sp.pres_anno = spd.pres_anno)
									INNER JOIN  sai_codi sco ON (sco.comp_id = sp.paga_docu_id)
									
									INNER JOIN sai_comp sc ON (sco.nro_compromiso = sc.comp_id)
								WHERE 
									sc.pcta_id= '".$pcta."' AND ";
									
						if(!isset($params['pcta_id'])){
							
							$queryP .= "sc.pres_anno = ".$params['anoPresupuesto']." AND ";
									
						}				
								$queryP .= "
									
									sp.esta_id != 15 AND 
									sp.esta_id != 2 AND 
									spd.part_id LIKE '".$partidas['partida']."%' 
								GROUP BY spd.part_id 
								ORDER BY 1";
								
						$query = "SELECT
										n.part_id, 
										SUM(n.monto_pagado) AS monto_pagado 
									FROM (".$queryP.") n 
									GROUP BY 1";	
						//echo $querySuma;	
						
						//echo $query;
						if(($resultComp = $GLOBALS['SafiClassDb']->Query($query)) === false)
							throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
							
						if ($rowComp = $GLOBALS['SafiClassDb']->Fetch($resultComp)) {
							$arrPtoCuenta[$pcta]["partidas"][$partidas['partida']]["montoPagado"] = $rowComp["monto_pagado"];
						}
						/*F
						 * in monto pagado*/
					}
				}
			  }
			}
			return $arrPtoCuenta;
		}
		catch(Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetDetalleApartadoPcta($params)
	{
		$pcta = new EntidadPuntoCuenta();
		$PuntoCuenta = array();
		$query = " SELECT 
						spi.pcta_sub_espe AS part_id,
						spi.pcta_id AS pcta_id, 
						SUM(spi.pcta_monto) AS monto_apartado, 
						CAST(SUBSTR(spi.pcta_id,6) AS integer) 
					FROM sai_pcta_imputa spi 
 					WHERE 
						spi.pcta_id = '".$params['pcta']."' AND
						spi.pcta_sub_espe = '".$params['partida']."' AND
						spi.pcta_id IN 
										(SELECT 
											docg_id 
										 FROM 
											sai_doc_genera sdg 
										 WHERE 
										 	(esta_id = 13 OR perf_id_act = ".PERFIL_DIRECTOR_EJECUTIVO." OR perf_id_act = ".PERFIL_PRESIDENTE.") AND 
										 	sdg.docg_id = spi.pcta_id
										) 
					GROUP BY 
							spi.pcta_sub_espe, 
							spi.pcta_id 
					UNION 
					SELECT 
						spi.pcta_sub_espe AS part_id, 
						spi.pcta_id AS pcta_id, 
						SUM(spi.pcta_monto) AS monto_apartado, 
						CAST(SUBSTR(spi.pcta_id,6) AS integer) 
					FROM sai_pcta_imputa spi, 
						sai_pcuenta sp  
 					WHERE 
						sp.pcta_id = spi.pcta_id AND
						sp.pcta_asociado = '".$params['pcta']."' AND
						spi.pcta_sub_espe = '".$params['partida']."' AND
						spi.pcta_id IN 
									(SELECT
										docg_id 
									 FROM 
										sai_doc_genera sdg 
									 WHERE 
									 	(esta_id = 13 OR perf_id_act = ".PERFIL_DIRECTOR_EJECUTIVO." OR perf_id_act = ".PERFIL_PRESIDENTE.") AND 
									 	sdg.docg_id = spi.pcta_id
									) 
					GROUP BY spi.pcta_sub_espe, 
							spi.pcta_id 
					ORDER BY 4 ";				
			
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$pcta = new EntidadPuntoCuenta();				
				$pcta -> SetId($row['pcta_id']);
				$pcta -> SetMontoSolicitado($row['monto_apartado']);
				
				$PuntoCuenta[] = $pcta;
			}
	
			return $PuntoCuenta;
	
	
		}else{
	
			return false;
		}
	}
	
	public static function GetDetalleCompromisoPcta($params)
	{
		$pcta = new EntidadPuntoCuenta();
		$PuntoCuenta = array();
		$query = "SELECT 
					scit.comp_sub_espe AS part_id,
					sct.comp_id, 
					scit.comp_monto AS monto_comprometido, 
					CAST(SUBSTR(sct.comp_id,6) AS INTEGER) 
				FROM sai_comp sct, 
					sai_comp_imputa scit 
				WHERE 
					scit.pres_anno = ".$params['ano']." AND 
					sct.pcta_id ='".$params['pcta']."' AND 
					sct.esta_id != 15 AND 
					sct.esta_id != 2 AND 
					sct.comp_id = scit.comp_id  AND 
					scit.comp_sub_espe LIKE '".$params['partida']."%' 
					ORDER BY 4"; 
		
			
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$pcta = new EntidadPuntoCuenta();
				$pcta -> SetId($row['comp_id']);
				$pcta -> SetMontoSolicitado($row['monto_comprometido']);
	
				$PuntoCuenta[] = $pcta;
			}
	
			return $PuntoCuenta;
	
	
		}else{
	
			return false;
		}
	}	
	
	public static function GetDetalleCausadoPcta($params)
	{
		$pcta = new EntidadPuntoCuenta();
		$PuntoCuenta = array();
		$query = "SELECT 
					scd.part_id,
					scd.cadt_monto AS monto_causado,
					caus_docu_id, 
				    CAST(SUBSTR(ssp.sopg_id,6) AS integer) 
				FROM sai_causado sc, 
					sai_causad_det scd, 
					sai_sol_pago ssp, 
					sai_comp  scomp 
				WHERE 
					ssp.sopg_id = sc.caus_docu_id AND
					scomp.pcta_id = '".$params['pcta']."' AND 
					ssp.comp_id = scomp.comp_id AND 
					sc.pres_anno = ".$params['ano']." AND 
					sc.esta_id != 15 AND 
					sc.esta_id != 2 AND 
					sc.caus_id = scd.caus_id AND 
					sc.pres_anno = scd.pres_anno AND 
					scd.part_id LIKE '".$params['partida']."%' 
				UNION ALL 
				SELECT scd.part_id, 
						scd.cadt_monto AS monto_causado,
						caus_docu_id,  
				 		CAST(SUBSTR(scodi.comp_id,6) AS INTEGER) 
				FROM sai_causado sc, 
						sai_causad_det scd, 
						sai_codi scodi, 
						sai_comp scomp  
				WHERE scodi.comp_id = sc.caus_docu_id AND
						scodi.nro_compromiso = scomp.comp_id AND 
						scomp.pcta_id='".$params['pcta']."' AND
						sc.pres_anno = ".$params['ano']." AND 
						sc.esta_id != 15 AND 
						sc.esta_id != 2 AND 
						sc.caus_id = scd.caus_id AND 
						sc.pres_anno = scd.pres_anno AND 
						scd.part_id LIKE '".$params['partida']."%' 
				ORDER BY 4";
	
			
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$pcta = new EntidadPuntoCuenta();
				$pcta -> SetId($row['caus_docu_id']);
				$pcta -> SetMontoSolicitado($row['monto_causado']);
	
				$PuntoCuenta[] = $pcta;
			}
	
			return $PuntoCuenta;
	
	
		}else{
	
			return false;
		}
	}
		
	public static function GetDetallePagadoPcta($params)
	{
		$pcta = new EntidadPuntoCuenta();
		$PuntoCuenta = array();
		$query = "SELECT 
					spd.part_id AS part_id, 
					COALESCE(SUM(spd.padt_monto),0) AS monto_pagado,
					paga_docu_id, 
				    CAST(SUBSTR(spc.pgch_id,6) AS INTEGER) 
				FROM sai_pagado sp, 
					sai_pagado_dt spd, 
					sai_pago_cheque spc, 
					sai_sol_pago ssp, 
					sai_comp sc 
				WHERE 
					spc.pgch_id = sp.paga_docu_id AND
					spc.docg_id = ssp.sopg_id AND
					ssp.comp_id = sc.comp_id AND
					sc.pcta_id= '".$params['pcta']."' AND 
					sp.pres_anno = ".$params['ano']." AND 
					sp.esta_id != 15 AND 
					sp.esta_id != 2 AND 
					sp.paga_id = spd.paga_id AND 
					sp.pres_anno = spd.pres_anno AND 
					spd.part_id LIKE '".$params['partida']."%' 
				GROUP BY spd.part_id,
					paga_docu_id,
					spc.pgch_id 
				UNION ALL 
				SELECT 
					spd.part_id AS part_id, 
					COALESCE(SUM(spd.padt_monto),0) AS monto_pagado,
					paga_docu_id,  
				    CAST(SUBSTR(spt.trans_id,6) AS INTEGER) 		
				FROM sai_pagado sp, 
					sai_pagado_dt spd, 
					sai_pago_transferencia spt, 
					sai_sol_pago ssp, 
					sai_comp sc 
				WHERE 
					spt.trans_id = sp.paga_docu_id AND
					spt.docg_id = ssp.sopg_id AND
					ssp.comp_id=sc.comp_id AND 
					sc.pcta_id= '".$params['pcta']."' AND 
					sp.pres_anno = ".$params['ano']." AND 
					sp.esta_id != 15 AND 
					sp.esta_id != 2 AND 
					sp.paga_id = spd.paga_id AND 
					sp.pres_anno = spd.pres_anno AND 
					spd.part_id LIKE '".$params['partida']."%' 
				GROUP BY spd.part_id,
						paga_docu_id,
						spt.trans_id 
				UNION ALL 
				SELECT 
					spd.part_id AS part_id, 
					COALESCE(SUM(spd.padt_monto),0) AS monto_pagado,
					paga_docu_id,  
				    CAST(SUBSTR(sco.comp_id,6) AS INTEGER) 
				FROM sai_pagado sp, 
				    sai_pagado_dt spd, 
				    sai_codi sco,
				    sai_comp sc 
				WHERE 
					sco.comp_id = sp.paga_docu_id AND 
					sco.nro_compromiso = sc.comp_id AND
					sc.pcta_id= '".$params['pcta']."' AND
					sp.pres_anno = ".$params['ano']." AND 
					sp.esta_id != 15 AND 
					sp.esta_id != 2 AND 
					sp.paga_id = spd.paga_id AND 
					sp.pres_anno = spd.pres_anno AND 
					spd.part_id LIKE '".$params['partida']."%' 
				GROUP BY spd.part_id,
						paga_docu_id,
						sco.comp_id 
				ORDER BY 4";
	
	
			
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$pcta = new EntidadPuntoCuenta();
				$pcta -> SetId($row['paga_docu_id']);
				$pcta -> SetMontoSolicitado($row['monto_pagado']);
	
				$PuntoCuenta[] = $pcta;
			}
	
			return $PuntoCuenta;
	
	
		}else{
	
			return false;
		}
	}
	public static function GetPctaDisponibilidadPcuanta($params = null)
	{
		try
		{

			$preMsg = "Error al intentar obtener los monto disponible de cuenta.";
			$existeCriterio = false;
			$arrMsg = array();
			
			
			$query = "SELECT pcuenta.pcta_id, COALESCE(apartado.disponible,0) - COALESCE(compromiso.disponible,0) AS compromiso 
            FROM sai_pcuenta pcuenta
INNER JOIN (
					
	     		SELECT
							s.pcta_id,
							SUM(s.monto_apartado) AS disponible
					FROM (
						SELECT
							spi.pcta_id,
							SUM(spi.pcta_monto) AS monto_apartado
						FROM
							sai_pcta_imputa spi
							INNER JOIN sai_doc_genera sdg ON (sdg.docg_id = spi.pcta_id)
						WHERE
								spi.pcta_id IN ('".implode("', '", $params)."') AND
								(sdg.esta_id = 13 OR sdg.perf_id_act = '47350' OR sdg.perf_id_act = '38150')
						GROUP BY spi.pcta_id
						UNION ALL
						SELECT
							sp.pcta_asociado AS pcta_id,
							SUM(spi.pcta_monto) AS disponible
						FROM
							sai_pcta_imputa spi
						INNER JOIN sai_pcuenta sp ON (sp.pcta_id=spi.pcta_id)
						INNER JOIN sai_doc_genera sdg ON (sdg.docg_id = spi.pcta_id)
						WHERE
								sp.pcta_asociado IN ('".implode("', '", $params)."') AND
								(sdg.esta_id = 13 OR sdg.perf_id_act = '47350' OR sdg.perf_id_act = '38150') 
						GROUP BY sp.pcta_asociado
					) AS s
					
					GROUP BY s.pcta_id
	        		
) apartado ON (apartado.pcta_id = pcuenta.pcta_id)
LEFT OUTER JOIN (
            SELECT c.pcta_id, SUM(comp_monto) AS disponible 
            FROM sai_comp_imputa ci
            INNER JOIN sai_comp c ON (c.comp_id = ci.comp_id)
			INNER JOIN sai_doc_genera d ON (d.docg_id = c.pcta_id)	        		
            WHERE c.esta_id != 15
            AND c.comp_id LIKE  '%".substr($_SESSION['an_o_presupuesto'],2,3)."'
            AND c.pcta_id IN ('".implode("', '", $params)."')
            AND d.esta_id != 15
            GROUP BY c.pcta_id
) compromiso ON (compromiso.pcta_id = pcuenta.pcta_id)
WHERE pcuenta.esta_id != 15";
  // echo $query;			
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
	       while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
                $arrPtoCuentaDisp[$row['pcta_id']] = $row['compromiso'];  
				
		
			}
            
	    return $arrPtoCuentaDisp;

	
	   }
			
		}
		catch(Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
}
	