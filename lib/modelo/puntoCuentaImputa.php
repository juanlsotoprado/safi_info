<?php

include_once(SAFI_ENTIDADES_PATH ."/puntoCuentaImputa.php");

class SafiModeloPuntoCuentaImputa
{

	 	public static  function InsertPctaImputa($params){


		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){

				$preMsg = "Error al insertar el Estatus de Conectividad.";



				$partida_pcta= $params['partida_pcta'];
				$valTransaccion = true;
				$i = 0;
				$tamaño =  sizeof($partida_pcta['tipo']);
				if($tamaño > 0){

					while ($i < $tamaño ){

						$query = "
	                                   INSERT INTO sai_pcta_imputa 
	                                   
	                                   (pcta_id,  
	                                    pcta_sub_espe,
	                                    pcta_acc_esp,
	                                    pcta_monto,
	                                    pcta_acc_pp,
	                                    depe_id,
	                                    pcta_tipo_impu,
	                                    pres_anno)
	                                    
										VALUES (
											
										'".$params['pcta_id']."',
										'".$partida_pcta['codPartida'][$i]."',
										'". $partida_pcta['codProyAccEsp'][$i]."',	
										". $partida_pcta['monto'][$i].",
				 						'". $partida_pcta['codProyAcc'][$i]."',
                                        '".$params['DependenciaTramita']."',
										'".$partida_pcta['tipo'][$i]."',
										'".$_SESSION['an_o_presupuesto']."');";
						
                      $valTransaccion = $GLOBALS['SafiClassDb']->Query($query);

						if( $valTransaccion === false){

							break;
						}

						
						//error_log($query);
						
						$i++;
						
						
					}
				}

				if($valTransaccion === false) throw new Exception('Error al insertar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());  
				$valTransaccion = $GLOBALS['SafiClassDb']->CommitTransaction();

				return 'true';

			} else {
				throw new Exception('Error al iniciar la transacci&oacute;n');
			}
		}catch(Exception $e){
			$valTransaccion = $GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			
			return false;
			
		}

	}
	
	
  public static  function InsertPctaImputaTraza($params){


		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){

				$preMsg = "Error al insertar el imputa traza.";
				
				$partida_pcta= $params['partida_pcta'];
				$valTransaccion = true;
				$i = 0;
				$tamaño =  sizeof($partida_pcta['tipo']);
				
				if($tamaño > 0){
					
				$dateTime = new DateTime();	
	            $fecha2 = (String) $dateTime->format('Y-m-d h:m:s');
					
					while ($i < $tamaño ){
						
						
		  

				   $query = "
	                                   INSERT INTO sai_pcta_imputa_traza
	                                   
	                                   (pcta_id,  
	                                    pcta_sub_espe,
	                                    pcta_acc_esp,
	                                    pcta_monto,
	                                    pcta_acc_pp,
	                                    depe_id,
	                                    pcta_tipo_impu,
	                                    pres_anno,
	                                    pcta_fecha)
	                                    
										VALUES (
											
										'".$params['pcta_id']."',
										'".$partida_pcta['codPartida'][$i]."',
										'". $partida_pcta['codProyAccEsp'][$i]."',	
										". $partida_pcta['monto'][$i].",
				 						'". $partida_pcta['codProyAcc'][$i]."',
                                        '".$params['DependenciaTramita']."',
										'".$partida_pcta['tipo'][$i]."',
										'".$_SESSION['an_o_presupuesto']."',			
										'".$fecha2."');";
		

				   
				   
                      $valTransaccion = $GLOBALS['SafiClassDb']->Query($query);
					  if( $valTransaccion === false){

							break;
						}

						
						//error_log($query);
						
						$i++;
						
					}
					
				    $valTransaccion =  SafiModeloPuntoCuenta::InsertPctaTraza($params['pcta_id'],$fecha2);
					if( $valTransaccion === false){

							break;
							
					}
				}

				if($valTransaccion === false) throw new Exception('Error al insertar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());  
				$valTransaccion = $GLOBALS['SafiClassDb']->CommitTransaction();

				return 'true';

			} else {
				throw new Exception('Error al iniciar la transacci&oacute;n');
			}
		}catch(Exception $e){
			$valTransaccion = $GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			
			return false;
			
		}

	}

	 	public static  function EliminarPctaImputaIdPcta($params){


		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){

						$query = " DELETE 
						           
						           FROM
						              sai_pcta_imputa
						              
						           WHERE
						              pcta_id='".$params."'";

						
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
	
	
	
	
public static function GetPctaImputaPctaId($param = null)
	{
	
       $data = array();
	   $query = "
	   SELECT *
       FROM  sai_pcta_imputa
       WHERE 
       pcta_id = '".$param."'";
		$result = $GLOBALS['SafiClassDb']->Query($query);
		while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

			
			
		$partida = SafiModeloPartida::GetPartidasByIds(array($row['pcta_sub_espe']),$row['pres_anno']);
		
		if(isset($row['pcta_tipo_impu']) && ($row['pcta_tipo_impu'] > 0)){
	    //es un proyecto  
		
	    $proyecto = SafiModeloProyecto::GetProyectoById($row['pcta_acc_pp'],$row['pres_anno']);	
		$proyectoEsp = SafiModeloProyectoEspecifica::GetProyectoEspecificaById($row['pcta_acc_esp'],$row['pcta_acc_pp'],$row['pres_anno']);

		
		
		}else{
		$accionCentralizada = SafiModeloAccionCentralizada::GetAccionCentralizadaById($row['pcta_acc_pp'],$row['pres_anno']);
		
	 	$accionCentralizadaEsp =  SafiModeloAccionCentralizadaEspecifica::GetAccionCentralizadaEspecificaById($row['pcta_acc_esp'],$row['pcta_acc_pp'],$row['pres_anno']);

	 	
		}
		

        $data[] = self::LlenarPctaImputa($row,$partida,$proyecto,$proyectoEsp,$accionCentralizada,$accionCentralizadaEsp);
      
	
		}
        return $data;
        
      
	}
	

	
	
public static function GetPctaImputasPctaId($param)
	{
	
       $data = array();
       
	   $query = "
	   SELECT *
       FROM  sai_pcta_imputa
       WHERE 
       pcta_id  IN ('".implode("', '",$param)."')";
	   
	   
	   
	   
		$result = $GLOBALS['SafiClassDb']->Query($query);
		while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

		$partida = SafiModeloPartida::GetPartidasByIds(array($row['pcta_sub_espe']),$row['pres_anno']);
		
		
		
		if(isset($row['pcta_tipo_impu']) && ($row['pcta_tipo_impu'] > 0)){
	    //es un proyecto  
		
	    $proyecto = SafiModeloProyecto::GetProyectoById($row['pcta_acc_pp'],$row['pres_anno']);	
		$proyectoEsp = SafiModeloProyectoEspecifica::GetProyectoEspecificaById($row['pcta_acc_esp'],$row['pcta_acc_pp'],$row['pres_anno']);

		
		}else{
		$accionCentralizada = SafiModeloAccionCentralizada::GetAccionCentralizadaById($row['pcta_acc_pp'],$row['pres_anno']);
		
	 	$accionCentralizadaEsp =  SafiModeloAccionCentralizadaEspecifica::GetAccionCentralizadaEspecificaById($row['pcta_acc_esp'],$row['pcta_acc_pp'],$row['pres_anno']);

		}
		
        $data[$row['pcta_id']][] = self::LlenarPctaImputa($row,$partida,$proyecto,$proyectoEsp,$accionCentralizada,$accionCentralizadaEsp);
      
	
		}
        return $data;
        
      
	}
	
	
	public static function GetPctaImputaFiltro($param1 = null,$param2 = null)
	{
		$and = false;
		
		if($param1){	
		$and == true? $queryWhere .= 'AND ' : $and = true ;   	
		
		$queryWhere .= "pcta_sub_espe = '".$param1."'";
		}
		
	  if($param2){	
		$and == true? $queryWhere .= 'AND ' : $and = true ;   	
		
		$queryWhere .= "pcta_acc_esp = '".$param2."'";
		}
	
       $data = array();
       
	   $query = "
	   SELECT pcta_id
       FROM  sai_pcta_imputa
       WHERE 
       ".$queryWhere."";

	   
		$result = $GLOBALS['SafiClassDb']->Query($query);
		while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

        $data[] = $row['pcta_id'] ;

		}
        return $data;
        
      
	}
	
	
	
	
	
    public static function LlenarPctaImputa(array $row = null,$partida,$proyecto,$proyectoEsp,$accionCentralizada,$accionCentralizadaEsp)
	{
		$puntoCuenta = new EntidadPuntoCuentaImputa();
		
		
		
	if(isset($row['pcta_sub_espe'])){
		
		    $partida = $partida[$row['pcta_sub_espe']];
		}
		
		if(isset($row['depe_id'])){
			
			$DependenciaQueTramita = $row['depe_id'];
			$dependencia = new EntidadDependencia();
			$dependencia->SetId($DependenciaQueTramita);  
	
		}
		
		
	if(isset($row['pcta_tipo_impu']) && ($row['pcta_tipo_impu'] > 0)){
	//es un proyecto  

	 $puntoCuenta->SetProyecto($proyecto);
	 $puntoCuenta->SetProyectoEspecifica($proyectoEsp);
	
   
         
		
		}else{
			
	//es una acc. centralizada   
	
    
	 //error_log(print_r($accionCentralizadaEsp,true)); 	
	 $puntoCuenta->SetAccionCentralizada($accionCentralizada);
     $puntoCuenta->SetAccionCentralizadaEspecifica($accionCentralizadaEsp);		

			
	}
		
 
		$puntoCuenta->SetId($row['pcta_id']);
		$puntoCuenta->SetMonto($row['pcta_monto']); 
		$puntoCuenta->SetTipoImpu($row['pcta_tipo_impu']);
		$puntoCuenta->SetPresAnno($row['pres_anno']);
        $puntoCuenta->SetPartida($partida);
        $puntoCuenta->SetDependencia($dependencia);
        

			

      // error_log(print_r($puntoCuenta,true));	
      
        return $puntoCuenta;
	 }
	 
	 
	 
	 
	 public static function GetImputasPartidaPcta($pctas,$partidas)
	 {
	 	
	$params= array();
 
	   $query = "
	   SELECT 
	         distinct(pcta_sub_espe),
                     pcta_acc_esp,
                     pcta_acc_pp,
                     pcta_tipo_impu
       FROM 
             sai_pcta_imputa
             
       WHERE 
            pcta_id  IN ('".implode("', '",$pctas)."') AND
            pcta_sub_espe  IN ('".implode("', '",$partidas)."')
  
       ";

	
	  if($result = $GLOBALS['SafiClassDb']->Query($query)){
		while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

		
		$params[] = $row;
	 
		}
		
		return $params;
	}
       
	return false;
	 
	 }

}