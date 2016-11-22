<?php
include_once(SAFI_ENTIDADES_PATH . '/docgenera.php');
include_once(SAFI_ENTIDADES_PATH . '/mpresupuestaria.php');

class SafiModeloMPresupuestarias
{
	
	  public static  function InsertPmod($params){
		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){

				  $preMsg = "error al insertar modificaciones presupuestarias.";
                     if($params['fecha']){
                     $fecha = explode ('/',$params['fecha']);
				     $fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' '.strftime('%H:%M:%S');
				      $fecha3  =  $fecha[0].'-'.$fecha[1].'-'.$fecha[2];
                     }

                    if($params['estatus']){
                     	
                    $estatus = 	$params['estatus'];
                     
                     }else{
                     
                       $estatus =  '10';
                     }
                     
                     
              
							$query = "
	                                   INSERT INTO sai_forma_0305 
	                                   (f030_id,       
	                                    f030_fecha,     
	                                    depe_id,
	                                    esta_id,
	                                    pres_anno,
	                                    f030_tipo,
	                                    f030_motivo,
	                                    f030_tipomodificacion
	                                   )
	                                    
										VALUES (
											
										'".$params['pmod_id']."',
										'".$fecha2."',
										'".$params['DependenciaTramita']."',
										".$estatus.",
										'".$params['presAnno']."',
										".$params['accionMP'].",
										'".$params['observaciones']."',
										'M')
										";

							
							 //f030_id='pmod-40021313'
                     $result = $GLOBALS['SafiClassDb']->Query($query);

					 if($result === false) throw new Exception('Error al insertar. Detalles  modificaciones presupuestarias: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());  

	
                        $result	= SafiModelomPresupuestarioImputa::InsertmPresupuestarioImputa($params);
					    if($result === false) throw new Exception('Error al insertar. Detalles imputa   modificaciones presupuestarias: ' . $GLOBALS['SafiClassDb']->GetErrorMsg()); 
			
					
						if($params['Fisico']){
        
                         $result = self::InsertRespaldo($params,'Fisico');
                         
                         if($result === false) throw new Exception('Error al insertar. Detalles respaldo: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
						
						}
						
					
						
			        if($params['Digital']){

                   $result = self::InsertRespaldo($params,'Digital');
                    
                    if($result === false) throw new Exception('Error al insertar. Detalles digital: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
     
			        }
			 
			     

    
		$data = array();
		$dateTime = new DateTime();
		
	    $fecha = (String) $dateTime->format('y-m-d h:m:s');
	    
	    
	    
		$data['docg_id'] = $params['pmod_id'] ;
		$data['docg_wfob_id_ini'] = $params['docg_wfob_id_ini'] != false ? $params['docg_wfob_id_ini'] :  0 ;
		$data['docg_wfca_id'] = $params['CadenaIdcadena'] ;
		$data['docg_usua_login'] = $_SESSION['login'];
		$data['docg_perf_id'] =  $params['IdPerfil']  != false ? $params['IdPerfil'] : $_SESSION['user_perfil_id'] ;
		$data['docg_fecha'] = 	$fecha3.' '.strftime('%H:%M:%S');
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
	

	
	 public static function	UpdatePmod($params){
	 	

	 	try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){

				  $preMsg = "error al insertar modificaciones presupuestarias.";
                     if($params['fecha']){
                     $fecha = explode ('/',$params['fecha']);
				     $fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' '.strftime('%H:%M:%S');
                     }

                    if($params['estatus']){
                     	
                    $estatus = 	$params['estatus'];
                     
                     }else{
                     
                       $estatus =  '10';
                     }

                     
				            $query = "UPDATE
				         	             sai_forma_0305
				         	             
				                      SET 
				                      
	                                    f030_fecha = '".$fecha2."',
	                                     depe_id = '".$params['DependenciaTramita']."',
	                                      esta_id = ".$estatus.",
	                                       pres_anno = '".$params['presAnno']."',
	                                        f030_tipo = ".$params['accionMP'].",
	                                         f030_motivo = '".$params['observaciones']."'
					                 WHERE	
					                 
					                    f030_id='".$params['pmod_id']."'";
				            

				            
          $result = $GLOBALS['SafiClassDb']->Query($query);
                
	    if($result === false) throw new Exception('Error al Modificar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());	

	    
	      $result	= SafiModelomPresupuestarioImputa::EliminarPmodImputaId($params['pmod_id']);
	      
	 
	        if($result === false) throw new Exception('Error al eliminar imputa. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());	
	   

                       $result	= SafiModelomPresupuestarioImputa::InsertmPresupuestarioImputa($params);
                        
                        
					  if($result === false) throw new Exception('Error al insertar. Detalles imputa   modificaciones presupuestarias: ' . $GLOBALS['SafiClassDb']->GetErrorMsg()); 
			
					
	    
				   if($params['regisFisDigiEli']){

			        $result	= self::EliminarRespaldoResp($params['regisFisDigiEli']);
			    
			       if($result === false) throw new Exception('Error al eliminar. Detalles respaldo: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			 
			   }
			   
			       
					     
						if($params['Fisico']){
        
                         $result = self::InsertRespaldo($params,'Fisico');
                         
                         if($result === false) throw new Exception('Error al insertar. Detalles respaldo: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
						
						}
						
					
						
			        if($params['Digital']){

                   $result = self::InsertRespaldo($params,'Digital');
                    
                    if($result === false) throw new Exception('Error al insertar. Detalles digital: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
     
			        }
	   
	    
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
 	

	
	public static  function InsertRespaldo($params,$tipo){
	
		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){
				$preMsg = "Error al insertar modificacionesp  respaldo.";
			    $data= $params[$tipo];	

			 
				
				$valTransaccion = true;
				$i = 0;
				$tamaño =  sizeof($data);
				
				if($tamaño > 0){
					
					

					while ($i < $tamaño ){

						$query = "
	                                   INSERT INTO sai_respaldo
	                                   
	                                   (
	                                    resp_valida, 
	                                    docg_id, 
	                                    resp_tipo,
	                                    resp_nombre,
	                                    perf_id,
	                                    usua_login)
	                                    
	                            
										VALUES (
										'0',	
										'".$params['pmod_id']."',
										'".$tipo."',
				 						'".$data[$i]."',
				 						'".$_SESSION['user_perfil_id']."',
										'".$_SESSION['login']."');";

                     $valTransaccion = $GLOBALS['SafiClassDb']->Query($query);

						$valTransaccion = true;
                      
						if( $valTransaccion === false){

							break;
						}

						$i++;
					}
				}

				if($valTransaccion === false) throw new Exception('Error al insertar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());  
				$valTransaccion = $GLOBALS['SafiClassDb']->CommitTransaction();

				return "true";

			} else {
				throw new Exception('Error al iniciar la transacci&oacute;n');
			}
		}catch(Exception $e){
			$valTransaccion = $GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return "falso";
		}
	}
	
	
public static function GetMpresupuesto(array $params = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener un pmod.";
			$existeCriterio = false;
			$arrMsg = array();
			$queryWhere = "";
			$pmod = null;
			$findParams = array();
			
			if($params === null)
				throw new Exception("El parámetro \"params\" es nulo.");
			if(!is_array($params))
				throw new Exception("El parámetro \"params\" no es un arreglo.");
			if(count($params) == 0)
				throw new Exception("El parámetro \"params\" está vacío.");
				
			if(!isset($params['idPmod']))
				$arrMsg[] = "El parámetro \"params['idPmod']\" no pudo ser encontrado.";
			if(($pmod=$params['idPmod']) === null)
				$arrMsg[] = "El parámetro \"params['idPmod']\" es nulo.";
			if(($pmod=trim($pmod)) == '')
				$arrMsg[] = "El parámetro \"params['idPmod']\" está vacío.";
			else {
				$existeCriterio = true;
				$findParams["idPmod"] = array($pmod);
			}
			
			if(!$existeCriterio){
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}
			
		$arrPmod = self::GetMpresupuestarias($findParams);
			
		
		
			if(!is_array($arrPmod) && count($arrPmod) == 0)
				throw new Exception($preMsg." No se pudo obtener el punto de cuenta con id \"".$pmod."\".");

			return current($arrPmod);

			
		}
		catch(Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	
	
	public static function GetMpresupuestarias(array $params = null, $filtro = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener los mod.";
			$existeCriterio = false;
			$arrMsg = array();
			$queryWhere = "";
			$arrPmod = null;
			
			if($params === null)
				throw new Exception($preMsg."El parámetro \"params\" es nulo.");
			if(!is_array($params))
				throw new Exception($preMsg."El parámetro \"params\" no es un arreglo.");
			if(count($params) == 0)
				throw new Exception($preMsg."El parámetro \"params\" está vacío.");
				
				
			if(!$filtro){	
					
				if(!isset($params['idPmod']))
					$arrMsg[] = "El parámetro \"params['idsPuntosCuenta']\" no pudo ser encontrado.";
				if(($pmod=$params['idPmod']) === null)
					$arrMsg[] = "El parámetro \"params['idPmod']\" es nulo.";
				if(!is_array($pmod))
					$arrMsg[] = "El parámetro \"params['idPmod']\" no es un arreglo.";
				if(count($pmod) == 0)
					$arrMsg[] = "El parámetro \"params['idPmod']\" está vacío.";
				else {
					$existeCriterio = true;
					$queryWhere = "f030_id IN ('".implode("', '", $pmod)."')";
				}	
			
			}else {
			
				$existeCriterio = true;
		
				$and = false;
			
				 $cargo = substr($_SESSION['user_perfil_id'],0,2);
       	   
       	   if($cargo !== substr(PERFIL_JEFE_PRESUPUESTO,0,2)){
			 	
					$and == true? $queryWhere .= 'AND ' : $and = true ;
					$queryWhere .= "fo030.depe_id = '".substr($_SESSION['user_perfil_id'],-3)."'";
					
				}
				
				
			    if($params['nPmod']){
				
					$and == true? $queryWhere .= 'AND ' : $and = true ;
					$queryWhere .= "fo030.f030_id = '".$params['nPmod']."'";
				}else {
	
					if($params['txt_inicio'] && $params['hid_hasta_itin']){
					
						$and == true? $queryWhere .= 'AND ' : $and = true ;
						$queryWhere .= "fo030.f030_fecha  BETWEEN to_date('".$params['txt_inicio']."', 'DD/MM/YYYY') AND to_date('".$params['hid_hasta_itin']."', 'DD/MM/YYYY') ";
	
					}else{
					
						$and == true? $queryWhere .= 'AND ' : $and = true ;
						$agno = $params['agno'];
						$queryWhere .= " TO_CHAR(fo030.f030_fecha,'YYYY') = '".$agno."'";
					}
			
					if($params['tipo']){
							
						$and == true? $queryWhere .= 'AND ' : $and = true ;
						$queryWhere .= "fo030.f030_tipo = '".$params['tipo']."'";
					}
					
					
					if($params['PartidaBusqueda'] || $params['ProyAccVal']){
						$pmodImputa =  SafiModelomPresupuestarioImputa::GetPmodImputaFiltro($params['PartidaBusqueda'],$params['ProyAccVal']);

						if(is_array($pmodImputa)){
							$and == true? $queryWhere .= 'AND ' : $and = true;
							$queryWhere .= "fo030.f030_id IN ('".implode("', '",$pmodImputa)."')";
						}
					}

                   


					if($params['palabraClave']){
							
						$and == true? $queryWhere .= 'AND ' : $and = true ;
						$queryWhere .= "fo030.f030_motivo LIKE '%".$params['palabraClave']."%'";
						

					}
			
					
			}
			
			}
			
			if(!$existeCriterio){
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}
			
			$query = "
				SELECT
					*,TO_CHAR(f030_fecha, 'DD-MM-YYYY HH24:MI:SS') AS f030_fecha
				FROM
					sai_forma_0305 fo030
				WHERE
					".$queryWhere."
			";
			
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$arrPmod  =array();
			$x = 1;
			
			$arrIdsEstatus = array();
			$arrIdsDependenciaQueTramita = array();
			$arrIdsPmodImputa = array();
			$arrIdsPmodRespaldo = array();

			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				

			$row2[$row['f030_id']] = $row;
		
			$arrIdsEstatus[$row['esta_id']] = $row['esta_id'];
			$arrIdsDependenciaQueTramita[$row['depe_id']] = $row['depe_id'];
			$arrId[$row['f030_id']] = $row['f030_id'];
			
			}
			
		    $estatus = SafiModeloEstatus::GetEstadoPctaIdPcuenta($arrIdsEstatus);
		    $dependenciaQueTramita= SafiModeloDependencia::GetDependenciaByIds($arrIdsDependenciaQueTramita);
		    
		    
		    
		    if(!$filtro){
		    	
          $pmodImputa =  SafiModelomPresupuestarioImputa::GetPmodId($arrId); 
		   $pmodRespaldo =  SafiModeloPuntoCuentaRespaldo::GetRespaldosDocgIds($arrId);

		    }

		    
		    
		
            $paramsLlnar = array();     
	
             if($row2){ 
              	
             	foreach ($row2 as $index => $val){
             		

             		$paramsLlnar ['f030_id'] = $val['f030_id'];
             		$paramsLlnar['fecha'] = $val['f030_fecha'];
             		$paramsLlnar['estatus'] = $estatus[$val['esta_id']];
             		$paramsLlnar['dependencia'] = $dependenciaQueTramita[$val['depe_id']];
             		$paramsLlnar['observacion'] = $val['f030_motivo'];
             		$paramsLlnar['tipo'] = $val['f030_tipo'];
             		$paramsLlnar['pres_anno'] = $val['pres_anno'];
             		$paramsLlnar['pmodImputa'] = $pmodImputa[$val['f030_id']];
             		$paramsLlnar['pmodRespaldo'] = $pmodRespaldo[$val['f030_id']];
             		$arrPmod[$val['f030_id']] = self::LlenarPmod($paramsLlnar);
             	}
             	
             	

			
             }
            
             
			return $arrPmod;
			
		}
		catch(Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	public static function LlenarPmod($paramsLlnar)
	{
		 $pmod = new EntidadMpresupuestaria();
		
		$pmod->SetId($paramsLlnar['f030_id']);
		$pmod->SetFecha($paramsLlnar['fecha']);
		$pmod->SetEstatus($paramsLlnar['estatus']);
		$pmod->SetDependencia($paramsLlnar['dependencia']);
		$pmod->SetObservacion($paramsLlnar['observacion']);
		$pmod->SetTipoDoc($paramsLlnar['tipo']);
		$pmod->SetAnno($paramsLlnar['pres_anno']);

	    $pmod->SetMpresupuestariasImputas($paramsLlnar['pmodImputa']);
	    $pmod->SetMpresupuestariasRespaldos($paramsLlnar['pmodRespaldo']);
	    
	    
	    
         	return $pmod;
         	
         	
         	
	}
	
	
	 public static function	UpdatePmodEstaId($params){
	 

	 	 $query = "UPDATE
				       sai_forma_0305
				     
				   SET 
	                  esta_id = ".$params['estaid']."
	                 
				   WHERE	
				   
					   f030_id='".$params['id']."'";
	 	 

	     $result = $GLOBALS['SafiClassDb']->Query($query);
	     
	     if($result){
	     	
	     	  return $result;
	     
	     }else{
	     
	     return false;
	     
	     }
	     

	 }
	 
	 
 public static function EliminarRespaldoResp($param)
	{
	
    	try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){

						$query = "DELETE  
						 
						           FROM
						              sai_respaldo
						              
						           WHERE
						              resp_id IN('".str_replace(",","','",$param)."')";
						
						
                      $result = $GLOBALS['SafiClassDb']->Query($query);
                      
				if($result === false) throw new Exception('Error al eliminar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());  
				
				$result = $GLOBALS['SafiClassDb']->CommitTransaction();
				return true;
				
			}

		}catch(Exception $e){
			$valTransaccion = $GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
		
	}

   
	
}