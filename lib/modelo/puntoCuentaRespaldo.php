<?php

include_once(SAFI_ENTIDADES_PATH ."/puntoCuentaRespaldo.php");

class SafiModeloPuntoCuentaRespaldo
{

	
	
	public static  function InsertPctaRespaldo($params,$tipo){
	
		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){
				$preMsg = "Error al insertar el Estatus de Conectividad.";

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
										'".$params['pcta_id']."',
										'".$tipo."',
				 						'".$data[$i]."',
				 						'".$_SESSION['user_perfil_id']."',
										'".$_SESSION['login']."');";

                      $valTransaccion = $GLOBALS['SafiClassDb']->Query($query);
                      
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
	
	
	
	
public static function GetRespaldosDocgId($param = null)
	{
	
       $data = array();
       
	   $query = "
	   SELECT *
       FROM  sai_respaldo
       WHERE 
      docg_id = '".$param."'";
	   
		$result = $GLOBALS['SafiClassDb']->Query($query);
		while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

        $data[] =  self::LlenarPctaRespaldo($row);
        
		}
          
        return $data;
	}
	
public static function GetRespaldosDocgIds($param)
	{
	
       $data = array();
      
	   $query = "
	   SELECT *
       FROM  sai_respaldo
       WHERE 
      docg_id  IN ('".implode("', '",$param)."')";
	   
	   
	   
		$result = $GLOBALS['SafiClassDb']->Query($query);
		while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

        $data[$row['docg_id']][] =  self::LlenarPctaRespaldo($row);
        
		}

        return $data;
        
        
	}
	
	
    public static function LlenarPctaRespaldo(array $row = null)
	{
		$respaldo = new EntidadPuntoCuentaRespaldo();
		
		$respaldo->SetId($row['resp_id']);
		$respaldo->SetDocgId($row['docg_id']); 
		$respaldo->SetRespTipo($row['resp_tipo']);
		$respaldo->SetRespnombre($row['resp_nombre']);
		$respaldo->SetPerfId($row['perf_id']); 
		$respaldo->SetUsuaLogin($row['usua_login']);
		$respaldo->SetRespValida($row['resp_valida']);

		
		
        return $respaldo;
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