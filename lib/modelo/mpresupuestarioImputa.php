<?php
include_once(SAFI_ENTIDADES_PATH ."/compromisoImputa.php");

class SafiModelomPresupuestarioImputa {


	public static  function  VerificarPartidaCreadaEnFo1125($codProyAcc,$codProyAccEsp,$codPartida){

		 
		$querys = "	SELECT
					         count(sf.form_id)
					FROM sai_forma_1125 sf
					INNER JOIN sai_fo1125_det sfd ON(sfd.form_id=sf.form_id)
					INNER JOIN sai_partida p ON(p.part_id=sfd.part_id AND p.pres_anno=sf.pres_anno)
					WHERE 
				   sf.form_id_p_ac =   '".$codProyAcc."'
			  AND  sf.form_id_aesp = '".$codProyAccEsp."'
			  AND sf.pres_anno = ".$_SESSION['an_o_presupuesto']."
			  AND sfd.part_id= '".$codPartida."'";

		//   error_log(print_r($querys,true));

		$result = $GLOBALS['SafiClassDb']->Query($querys);



	 if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){

	  if ($row['count'] <= 0){
	  	 

	   $querys = "INSERT INTO sai_fo1125_det (part_id,pres_anno,fodt_monto,fodt_mes,form_id)
                   VALUES('".$codPartida."',".$_SESSION['an_o_presupuesto'].",0,1,
                            (SELECT sf.form_id
                             FROM sai_forma_1125 sf
                             WHERE sf.form_id_p_ac = '".$codProyAcc."'
                               AND sf.form_id_aesp =  '".$codProyAccEsp."'
                               AND sf.pres_anno = ".$_SESSION['an_o_presupuesto']."))";
	    

	   $GLOBALS['SafiClassDb']->Query($querys);

	   	
	  }

		}



	}


	public static  function InsertmPresupuestarioImputa($params){



		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){

				$preMsg = "Error al insertar InsertCompromisoImputa.";

				if($params['accionMP'] == 1){


					$partida = $params['mpresupuestaria'];
						
					$valTransaccion = true;
					$i = 0;
					$tamaño =  sizeof($partida['tipo']);




					if($tamaño > 0){

						while ($i < $tamaño ){

							self::VerificarPartidaCreadaEnFo1125($partida['codProyAcc'][$i],$partida['codProyAccEsp'][$i],$partida['codPartida'][$i]);

							$query = "
	                                   INSERT INTO sai_fo0305_det 
	                                   
	                                   (f030_id,  
	                                   pres_anno,
	                                   f0dt_id_p_ac,
	                                   f0dt_id_acesp,
	                                   f0dt_monto,
	                                   f0dt_tipo,
	                                   part_id,
	                                   depe_id,
	                                   f0dt_proy_ac)
	                                    
										VALUES (
										'".$params['pmod_id']."',
										".$_SESSION['an_o_presupuesto'].",
										'". $partida['codProyAcc'][$i]."',
										'". $partida['codProyAccEsp'][$i]."',	
										". $partida['monto'][$i].",
										1::bit,
									    '".$partida['codPartida'][$i]."',
										'".$params['DependenciaTramita']."',
										".$partida['tipo'][$i]."::bit
										);";
								



							$valTransaccion = $GLOBALS['SafiClassDb']->Query($query);
							if( $valTransaccion === false){

								break;
							}

							$i++;


						}
					}

					if($valTransaccion === false) throw new Exception('Error al insertar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					$valTransaccion = $GLOBALS['SafiClassDb']->CommitTransaction();

				}else if($params['accionMP'] == 2){

					$partida = $params['mpresupuestariadisp'];
					$partida2 = $params['mpresupuestaria'];

					$valTransaccion = true;
					$i = 0;
					$tamaño =  sizeof($partida['tipo']);
					if($tamaño > 0){
							
							

						while ($i < $tamaño ){

							self::VerificarPartidaCreadaEnFo1125($partida['codProyAcc'][$i],$partida['codProyAccEsp'][$i],$partida['codPartida'][$i]);

							$query = "
	                                   INSERT INTO sai_fo0305_det 
	                                   
	                                   (f030_id,  
	                                   pres_anno,
	                                   f0dt_id_p_ac,
	                                   f0dt_id_acesp,
	                                   f0dt_monto,
	                                   f0dt_tipo,
	                                   part_id,
	                                   depe_id,
	                                   f0dt_proy_ac)
	                                    
										VALUES (
										'".$params['pmod_id']."',
										".$_SESSION['an_o_presupuesto'].",
										'". $partida['codProyAcc'][$i]."',
										'". $partida['codProyAccEsp'][$i]."',	
										".$partida['monto'][$i].",
									      0::bit,
									    '".$partida['codPartida'][$i]."',
										'".$params['DependenciaTramita']."',
										".$partida['tipo'][$i]."::bit
										);";

							$valTransaccion = $GLOBALS['SafiClassDb']->Query($query);

							if( $valTransaccion === false){

								break;
							}

							$i++;


						}
					}


					$valTransaccion = true;
					$i = 0;
					$tamaño =  sizeof($partida2['tipo']);
					if($tamaño > 0){

						while ($i < $tamaño ){

							self::VerificarPartidaCreadaEnFo1125($partida2['codProyAcc'][$i],$partida2['codProyAccEsp'][$i],$partida2['codPartida'][$i]);

							$query = "
	                                   INSERT INTO sai_fo0305_det 
	                                   
	                                   (f030_id,  
	                                   pres_anno,
	                                   f0dt_id_p_ac,
	                                   f0dt_id_acesp,
	                                   f0dt_monto,
	                                   f0dt_tipo,
	                                   part_id,
	                                   depe_id,
	                                   f0dt_proy_ac)
	                                    
										VALUES (
										'".$params['pmod_id']."',
										".$_SESSION['an_o_presupuesto'].",
										'".$partida2['codProyAcc'][$i]."',
										'". $partida2['codProyAccEsp'][$i]."',	
										". $partida2['monto'][$i].",
										  1::bit,
									    '".$partida2['codPartida'][$i]."',
										'".$params['DependenciaTramita']."',
										".$partida2['tipo'][$i]."::bit
										);";


							$valTransaccion = $GLOBALS['SafiClassDb']->Query($query);

							if( $valTransaccion === false){

								break;
							}

							$i++;


						}
					}


				}else{


					$partida = $params['mpresupuestaria'];


					$valTransaccion = true;
					$i = 0;
					$tamaño =  sizeof($partida['tipo']);
					if($tamaño > 0){

						while ($i < $tamaño ){

							self::VerificarPartidaCreadaEnFo1125($partida['codProyAcc'][$i],$partida['codProyAccEsp'][$i],$partida['codPartida'][$i]);

							$query = "
	                                   INSERT INTO sai_fo0305_det 
	                                   
	                                   (f030_id,  
	                                   pres_anno,
	                                   f0dt_id_p_ac,
	                                   f0dt_id_acesp,
	                                   f0dt_monto,
	                                   f0dt_tipo,
	                                   part_id,
	                                   depe_id,
	                                   f0dt_proy_ac)
	                                    
										VALUES (
										'".$params['pmod_id']."',
										".$_SESSION['an_o_presupuesto'].",
										'". $partida['codProyAcc'][$i]."',
										'". $partida['codProyAccEsp'][$i]."',	
										". $partida['monto'][$i].",
										0::bit,
									    '".$partida['codPartida'][$i]."',
										'".$params['DependenciaTramita']."',
										".$partida['tipo'][$i]."::bit
										);";



							$valTransaccion = $GLOBALS['SafiClassDb']->Query($query);

							if( $valTransaccion === false){

								break;
							}

							$i++;


						}
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

	public static  function EliminarPmodImputaId($params){

		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){

				$query = " DELETE
						           
						           FROM
						              sai_fo0305_det
						              
						           WHERE
						              f030_id='".$params."'";


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


	public static function GetPmodId($param)
	{

		$data = array();
			
		$query = "
	   SELECT 
	         *
       FROM  
             sai_fo0305_det
       WHERE 
       
      f030_id  IN ('".implode("', '",$param)."')";



		$result = $GLOBALS['SafiClassDb']->Query($query);
		while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

			$partida = SafiModeloPartida::GetPartidasByIds(array($row['part_id']),$row['pres_anno']);
			$dependencia= SafiModeloDependencia::GetDependenciaByIds(array($row['depe_id'],$row['depe_id']));


				
			if($row['f0dt_proy_ac'] > 0){
				//es un proyecto

				$proyecto = SafiModeloProyecto::GetProyectoById($row['f0dt_id_p_ac'],$row['pres_anno']);
				$proyectoEsp = SafiModeloProyectoEspecifica::GetProyectoEspecificaById($row['f0dt_id_acesp'],$row['f0dt_id_p_ac'],$row['pres_anno']);


				 


			}else{
				$accionCentralizada = SafiModeloAccionCentralizada::GetAccionCentralizadaById($row['f0dt_id_p_ac'],$row['pres_anno']);
				$accionCentralizadaEsp =  SafiModeloAccionCentralizadaEspecifica::GetAccionCentralizadaEspecificaById($row['f0dt_id_acesp'],$row['f0dt_id_p_ac'],$row['pres_anno']);

			}
				
				

			$data[$row['f030_id']][] = self::LlenarPmod($row,current($partida),$proyecto,$proyectoEsp,$accionCentralizada,$accionCentralizadaEsp,current($dependencia));



		}
		//error_log(print_r($data,true));


		return $data;


	}




	public static function LlenarPmod(array $row = null,$partida,$proyecto,$proyectoEsp,$accionCentralizada,$accionCentralizadaEsp,$dependencia)
	{
		$compromiso = new EntidadMpresupuestariaimputa();



		if($row['f0dt_proy_ac'] > 0){
			//es un proyecto

			$compromiso->SetProyecto($proyecto);
			$compromiso->SetProyectoEspecifica($proyectoEsp);




		}else{

			//es una acc. centralizada


			//error_log(print_r($accionCentralizadaEsp,true));
			$compromiso->SetAccionCentralizada($accionCentralizada);
			$compromiso->SetAccionCentralizadaEspecifica($accionCentralizadaEsp);


		}
		$compromiso->SetId($row['f030_id']);
		$compromiso->SetMonto($row['f0dt_monto']);
		$compromiso->SetTipo($row['f0dt_tipo']);
		$compromiso->SetTipoImpu($row['f0dt_proy_ac']);
		$compromiso->SetPresAnno($row['pres_anno']);
		$compromiso->SetPartida($partida);
		$compromiso->SetDependencia($dependencia);

			

		//error_log(print_r($puntoCuenta,true));

		return $compromiso;


	}

	public static function GetPmodImputaFiltro($param1 = null,$param2 = null)
	{

		$and = false;

		if($param1){
			$and == true? $queryWhere .= 'AND ' : $and = true ;

			$queryWhere .= "part_id = '".$param1."'";
		}

		if($param2){
			$and == true? $queryWhere .= 'AND ' : $and = true ;

			$queryWhere .= "f0dt_id_acesp = '".$param2."'";
		}

		$data = array();

			
		$query = "
	   SELECT f030_id
       FROM  sai_fo0305_det
       WHERE 
       ".$queryWhere."";


		$result = $GLOBALS['SafiClassDb']->Query($query);
		while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

			$data[] = $row['f030_id'] ;


		}
		return $data;


	}







}
