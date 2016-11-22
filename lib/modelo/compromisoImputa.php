<?php
include_once(SAFI_ENTIDADES_PATH ."/compromisoImputa.php");

class SafiModeloCompromisoImputa {

	/////////////////////////////////////////////< temporal eliminar al lanzar pcta /////////////////////////////////////////////////////////
	public static  function InsertCompromisoDisponibilidad($params){


		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){

				$preMsg = "Error al insertar el Estatus de Conectividad.";

				$partida = $params['imputa'];



				$valTransaccion = true;
				$i = 0;
				$tamaño =  sizeof($partida['tipo']);
				if($tamaño > 0){

					while ($i < $tamaño ){


						$query = "
	                                   INSERT INTO sai_disponibilidad_comp 
	                                   
	                                   (partida,  
	                                    monto,
	                                    comp_id,
	                                    comp_acc_pp,
	                                    comp_acc_esp)
	                                    
										VALUES (
											
										
										'".$partida['codPartida'][$i]."',
										". $partida['monto'][$i].",
										'".$params['comp_id']."',
										'". $partida['codProyAcc'][$i]."',
										'". $partida['codProyAccEsp'][$i]."');";


						$valTransaccion = $GLOBALS['SafiClassDb']->Query($query);

						if( $valTransaccion === false){

							break;
						}

						$i++;


					}
				}

				if($valTransaccion === false) throw new Exception('Error al insertar disponibilidad comp . Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
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


	public static  function UpdateDisponibilidadPcta($params){


		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){

				$preMsg = "Error al insertar el Estatus de Conectividad.";

				$partida = $params['imputa'];



				$valTransaccion = true;
				$i = 0;
				$tamaño =  sizeof($partida['tipo']);
				if($tamaño > 0){

					$partidaMonto = array();

					$query = "
						   SELECT 
						         comp_monto,comp_sub_espe
					       FROM  
					             sai_comp_imputa
					       WHERE 
					       
					      comp_id = '".$params['comp_id']."'";
					
					
					if($result1 = $GLOBALS['SafiClassDb']->Query($query)){

						
						while ($row = $GLOBALS['SafiClassDb']->Fetch($result1))
						
				         {
							$partidaMonto[$row['comp_sub_espe']] = $row['comp_monto'];

						}
						
					}
					
					
					
					
				
					
			   	foreach($partidaMonto as $index => $montointerno){
					 
						$query = "
						UPDATE
						sai_disponibilidad_pcta
						 
						SET
						monto = (monto + ".$montointerno.")

						WHERE
						partida = '".$index."' AND
						pcta_id =  '".$params['compAsociado']."'";


						$valTransaccion = $GLOBALS['SafiClassDb']->Query($query);

						if($valTransaccion === false) throw new Exception('Error al insertar disponibilidad pcta . Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
						
					 
					 
					 
					 
					 }
					
					

					 while ($i < $tamaño ){

					 	
						$query = "
						UPDATE
						sai_disponibilidad_pcta
						 
						SET
						monto = ((monto) - ". $partida['monto'][$i].")

						WHERE
						partida = '".$partida['codPartida'][$i]."' AND
						pcta_id =  '".$params['compAsociado']."'";


						$valTransaccion = $GLOBALS['SafiClassDb']->Query($query);


				        if($valTransaccion === false) throw new Exception('Error al modificar disp.  pcta . Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
	

						$i++;


						}
						
				}

				if($valTransaccion === false) throw new Exception('Error al insertar disponibilidad pcta . Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
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




	///////////////////////////////////////////// temporal eliminar al lanzar pcta  > /////////////////////////////////////////////////////////



	public static  function InsertCompromisoImputa($params){


		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){

				$preMsg = "Error al insertar el Estatus de Conectividad.";

				$partida = $params['imputa'];



				$valTransaccion = true;
				$i = 0;
				$tamaño =  sizeof($partida['tipo']);
				if($tamaño > 0){

					while ($i < $tamaño ){


						$query = "
	                                   INSERT INTO sai_comp_imputa 
	                                   
	                                   (comp_id,  
	                                    comp_sub_espe,
	                                    comp_acc_esp,
	                                    comp_monto,
	                                    depe_id,
	                                    comp_acc_pp,
	                                    comp_tipo_impu,
	                                    pres_anno)
	                                    
										VALUES (
											
										'".$params['comp_id']."',
										'".$partida['codPartida'][$i]."',
										'". $partida['codProyAccEsp'][$i]."',	
										". $partida['monto'][$i].",
										'".$params['DependenciaTramita']."',
				 						'". $partida['codProyAcc'][$i]."',
										'".$partida['tipo'][$i]."',
										".$_SESSION['an_o_presupuesto'].");";


						$valTransaccion = $GLOBALS['SafiClassDb']->Query($query);

						if( $valTransaccion === false){

							break;
						}

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

	public static  function EliminarCompImputaId($params){


		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){

				$query = " DELETE
						           
						           FROM
						              sai_comp_imputa
						              
						           WHERE
						              comp_id='".$params."'";


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

	
	
	
		public static  function EliminarCompromisoDisponibilidad($params){


		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){

				$query = " DELETE
						           
						           FROM
						           
						              sai_disponibilidad_comp
						              
						           WHERE
						              comp_id='".$params."'";
				
				
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


	



	public static  function InsertCompromisoImputaTraza($params,$fecha2){






		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){

				$preMsg = "Error al insertar el Estatus de Conectividad.";


				$partida= $params['imputa'];


				$valTransaccion = true;
				$i = 0;
				$tamaño =  sizeof($partida['tipo']);
				if($tamaño > 0){

					while ($i < $tamaño ){




						$query = "
	                                   INSERT INTO sai_comp_imputa_traza 
	                                   
	                                   (comp_id,  
	                                    comp_sub_espe,
	                                    comp_acc_esp,
	                                    comp_monto,
	                                    depe_id,
	                                    comp_acc_pp,
	                                    comp_tipo_impu,
	                                    pres_anno,
	                                    comp_fecha)
	                                    
										VALUES (
											
										'".$params['comp_id']."',
										'".$partida['codPartida'][$i]."',
										'". $partida['codProyAccEsp'][$i]."',	
										". $partida['monto'][$i].",
										'".$params['DependenciaTramita']."',
				 						'". $partida['codProyAcc'][$i]."',
										'".$partida['tipo'][$i]."',
										".$_SESSION['an_o_presupuesto'];
							

						if($fecha2){
							$query .=  ",'".$fecha2."')";

						}else{
							$query .=  ",now())";
						}
							
						$valTransaccion = $GLOBALS['SafiClassDb']->Query($query);

						if( $valTransaccion === false){

							break;
						}

						$i++;


					}
				}

				if($valTransaccion === false) throw new Exception('Error al insertar imputa traza. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
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



	public static  function InsertCompromisoTrazaReporte($params,$fechatraza){

		try{
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			if($result === true){

				$preMsg = "Error al insertar el Estatus de Conectividad.";


				$partida= $params['imputa'];


				$valTransaccion = true;
				$i = 0;
				$tamaño =  sizeof($partida['tipo']);
				if($tamaño > 0){



					while ($i < $tamaño ){
						$query = "
	                                   INSERT INTO sai_comp_traza_reporte 
	                                   
	                                   (comp_id,  
	                                    comp_sub_espe,
	                                    comp_acc_esp,
	                                    comp_monto,
	                                    depe_id,
	                                    comp_acc_pp,
	                                    comp_tipo_impu,
	                                    pres_anno,
	                                    comp_fecha)
	                                    
										VALUES (
											
										'".$params['comp_id']."',
										'".$partida['codPartida'][$i]."',
										'". $partida['codProyAccEsp'][$i]."',	
										". $partida['monto'][$i].",
										'".$params['DependenciaTramita']."',
				 						'". $partida['codProyAcc'][$i]."',
										'".$partida['tipo'][$i]."',
										".$_SESSION['an_o_presupuesto'];
							

						if($fechatraza){
							$query .=  ",'".$fechatraza."')";

						}else{
							$query .=  ",'".strftime('%Y-%m-%d %H:%M:%S')."')";
						}

						$valTransaccion = $GLOBALS['SafiClassDb']->Query($query);

						if( $valTransaccion === false){

							break;
						}

						$i++;


					}
				}

				if($valTransaccion === false) throw new Exception('Error al insertar imputa traza. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
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


	public static function GetPctaImputasCompId($param)
	{

		$data = array();
			
		$query = "
	   SELECT 
	         *
       FROM  
             sai_comp_imputa
       WHERE 
       
      Comp_id  IN ('".implode("', '",$param)."')";




		$result = $GLOBALS['SafiClassDb']->Query($query);
		while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {


			$partida = SafiModeloPartida::GetPartidasByIds(array($row['comp_sub_espe']),$row['pres_anno']);
			$dependencia= SafiModeloDependencia::GetDependenciaByIds(array($row['depe_id'],$row['depe_id']));


			if(isset($row['comp_tipo_impu']) && ($row['comp_tipo_impu'] > 0)){
				//es un proyecto

				$proyecto = SafiModeloProyecto::GetProyectoById($row['comp_acc_pp'],$row['pres_anno']);
				$proyectoEsp = SafiModeloProyectoEspecifica::GetProyectoEspecificaById($row['comp_acc_esp'],$row['comp_acc_pp'],$row['pres_anno']);


			}else{
				$accionCentralizada = SafiModeloAccionCentralizada::GetAccionCentralizadaById($row['comp_acc_pp'],$row['pres_anno']);

				$accionCentralizadaEsp =  SafiModeloAccionCentralizadaEspecifica::GetAccionCentralizadaEspecificaById($row['comp_acc_esp'],$row['comp_acc_pp'],$row['pres_anno']);

			}

			$data[$row['comp_id']][] = self::LlenarCompImputa($row,current($partida),$proyecto,$proyectoEsp,$accionCentralizada,$accionCentralizadaEsp,current($dependencia));



		}

		return $data;


	}


	public static function SetPctaCompTrazaReporte($params,$fechatraza = null)
	{

		$partida= $params['imputa'];
		$codPartidaNuevas = array_flip($partida['codPartida']);
		$data = array();
		$categoriaIngresar = array();

		$query = "
		   SELECT 
		         *
	       FROM  
	             sai_comp_imputa
	       WHERE 
	       
	       Comp_id  = '".$params['comp']."'";
			
			
		$result = $GLOBALS['SafiClassDb']->Query($query);

		$i = 0;

		while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

			if(($partidaId = $codPartidaNuevas[$row['comp_sub_espe']]) != false ||
			$codPartidaNuevas[$row['comp_sub_espe']] == '0'){

					
				if(($row['comp_acc_esp'] == $partida['codProyAccEsp'][$partidaId]) &&
				($row['comp_acc_pp'] == $partida['codProyAcc'][$partidaId])){


					if(($row['comp_monto'] < $partida['monto'][$partidaId]) || ($row['comp_monto'] > $partida['monto'][$partidaId])){

						$categoriaIngresar['codPartida'][] =  $row['comp_sub_espe'];
						$categoriaIngresar['codProyAccEsp'][] =  $row['comp_acc_esp'];
						$categoriaIngresar['codProyAcc'][] =  $row['comp_acc_pp'];
						$categoriaIngresar['tipo'][] =  $row['comp_tipo_impu'];
						$categoriaIngresar['monto'][] = ($partida['monto'][$partidaId] - $row['comp_monto']);
							
						unset($partida['codPartida'][$partidaId]);
						unset($partida['codProyAccEsp'][$partidaId]);
						unset($partida['codProyAcc'][$partidaId]);
						unset($partida['tipo'][$partidaId]);
						unset($partida['monto'][$partidaId]);

					}else{
							
						unset($partida['codPartida'][$partidaId]);
						unset($partida['codProyAccEsp'][$partidaId]);
						unset($partida['codProyAcc'][$partidaId]);
						unset($partida['tipo'][$partidaId]);
						unset($partida['monto'][$partidaId]);
							
							
					}

				}


			}else{

				$categoriaIngresar['codPartida'][] =  $row['comp_sub_espe'];
				$categoriaIngresar['codProyAccEsp'][] =  $row['comp_acc_esp'];
				$categoriaIngresar['codProyAcc'][] =  $row['comp_acc_pp'];
				$categoriaIngresar['tipo'][] =  $row['comp_tipo_impu'];
				$categoriaIngresar['monto'][] = (($row['comp_monto']) * (-1));
					
			}
		}

		if($partida){

			foreach ($partida['codPartida'] as $index =>  $valor){
					
				$categoriaIngresar['codPartida'][] = $partida['codPartida'][$index];
				$categoriaIngresar['codProyAccEsp'][] =  $partida['codProyAccEsp'][$index];
				$categoriaIngresar['codProyAcc'][] =  $partida['codProyAcc'][$index];
				$categoriaIngresar['tipo'][] =  $partida['tipo'][$index];
				$categoriaIngresar['monto'][] = $partida['monto'][$index];

			}

		}


		return $params['imputa'] = $categoriaIngresar;


	}


	public static function GetCompImputaAnuladas($compId)
	{

		$query = "
		   SELECT 
		         *
	       FROM  
	             sai_comp_imputa
	       WHERE 
	       
	       Comp_id  = '".$compId."'";


		if($result = $GLOBALS['SafiClassDb']->Query($query)){

			while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

				$partidas['codPartida'][] =  $row['comp_sub_espe'];
				$partidas['codProyAccEsp'][] =  $row['comp_acc_esp'];
				$partidas['codProyAcc'][] =  $row['comp_acc_pp'];
				$partidas['tipo'][] =  $row['comp_tipo_impu'];
				$partidas['monto'][] = 0;
			}


			return $partidas;

		}
	}


	public static function GetCompImputas($compId)
	{

		$query = "
		   SELECT 
		         *
	       FROM  
	             sai_comp_imputa
	       WHERE 
	       
	       Comp_id  = '".$compId."'";


		if($result = $GLOBALS['SafiClassDb']->Query($query)){

			while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

				$partidas['codPartida'][] =  $row['comp_sub_espe'];
				$partidas['codProyAccEsp'][] =  $row['comp_acc_esp'];
				$partidas['codProyAcc'][] =  $row['comp_acc_pp'];
				$partidas['tipo'][] =  $row['comp_tipo_impu'];
				$partidas['monto'][] = $row['comp_monto'];
			}

			return $partidas;

		}
	}
	
	public static function GetCompImputasInfo($compId)
	{
	
		$query = "
			SELECT
				ci.comp_sub_espe AS partida, 
				p.part_nombre AS partida_nombre, 
				COALESCE (
					(
						SELECT
							ci.comp_monto - SUM (disponible.montos)  AS montos
						FROM
							(
								SELECT
									SUM(spi.sopg_monto + spi.sopg_monto_exento) AS montos,
									spi.sopg_sub_espe AS partida
								FROM
									sai_sol_pago_imputa spi
									INNER JOIN sai_sol_pago sp ON (sp.sopg_id = spi.sopg_id)
								WHERE
									sp.comp_id='".$compId."'
									AND spi.sopg_sub_espe = p.part_id
									AND sp.esta_id != '15' 								 
									AND spi.sopg_acc_pp = pa.proy_id AND pa.paes_id = spi.sopg_acc_esp
								GROUP BY
									spi.sopg_sub_espe
											
								UNION
											
								SELECT
									SUM(rcomp_debe) - SUM(rcomp_haber) AS montos,
									src.part_id AS partida
								FROM
									sai_reng_comp src
									INNER JOIN sai_codi c ON (c.comp_id = src.comp_id)
								WHERE
									c.nro_compromiso = '".$compId."'
									AND src.part_id = p.part_id 
									AND c.esta_id != 15								 
									AND src.pr_ac = pa.proy_id AND pa.paes_id = src.a_esp					
								GROUP BY
									src.part_id
							) AS disponible
						GROUP BY
							disponible.partida
					),
					ci.comp_monto
				) AS monto,
				--),ci.comp_monto) AS monto,	
				pa.centro_gestor||'/'||pa.centro_costo AS proy_acc,
				c.cpat_id AS cpat_id,
				ci.comp_acc_pp AS id_proyecto,
				ci.comp_acc_esp AS id_aesp,
				ci.comp_tipo_impu AS proy_tipo			
		FROM
				--sai_comp_imputa ci
				(
					SELECT 
						*
					from  
						sai_comp_imputa 
					where 
						comp_id  = '".$compId."'
					UNION
					SELECT 
						partidas.comp_id,
						partidas.partida,
						imp.comp_acc_esp,
						0 as comp_monto,
						imp.comp_acc_pp,
						imp.depe_id,
						imp.comp_tipo_impu,
						imp.pres_anno,
						imp.comp_monto_exento
					FROM                     
						(
						SELECT  sp.comp_id,
							spi.sopg_sub_espe AS partida
						FROM
							sai_sol_pago_imputa spi
							INNER JOIN sai_sol_pago sp ON (sp.sopg_id = spi.sopg_id)
						WHERE
							sp.comp_id= '".$compId."'
							AND sp.esta_id != '15' 						
						UNION 
						SELECT	
							c.nro_compromiso,	
							src.part_id AS partida
						FROM
							sai_reng_comp src
							INNER JOIN sai_codi c ON (c.comp_id = src.comp_id)
						WHERE
							c.nro_compromiso = '".$compId."'
							AND c.esta_id != 15 
							AND src.part_id NOT LIKE '4.11%'		
						) as partidas
						INNER JOIN sai_comp_imputa imp on (imp.comp_id =  partidas.comp_id)
					WHERE 
						partidas.partida NOT IN 
						(
							SELECT   
							comp_sub_espe 
							from  
							sai_comp_imputa 
							where 
							comp_id  = '".$compId."'
						)
					GROUP BY 
						partidas.comp_id,
						partidas.partida,
						imp.comp_acc_esp,
						imp.comp_acc_pp,
						imp.depe_id,
						imp.comp_tipo_impu,
						imp.pres_anno,
						imp.comp_monto_exento
				) as ci
				INNER JOIN sai_partida p ON (p.part_id = ci.comp_sub_espe)        
				INNER JOIN sai_proy_a_esp pa ON (pa.proy_id = ci.comp_acc_pp AND pa.paes_id = ci.comp_acc_esp)        
				INNER JOIN sai_convertidor c ON (c.part_id = ci.comp_sub_espe)        
			WHERE
				--ci.comp_id  = '".$compId."' and
				p.pres_anno = ci.pres_anno
						
			UNION
						
			SELECT
				ci.comp_sub_espe AS partida, 
				p.part_nombre AS partida_nombre, 
				COALESCE (
					(
						SELECT
							ci.comp_monto - SUM (disponible.montos) AS montos
						FROM
							(
								SELECT
									SUM(spi.sopg_monto + spi.sopg_monto_exento) AS montos,
									spi.sopg_sub_espe AS partida
								FROM
									sai_sol_pago_imputa spi
									INNER JOIN sai_sol_pago sp ON (sp.sopg_id = spi.sopg_id)
								WHERE
									sp.comp_id = '".$compId."'
									AND spi.sopg_sub_espe = p.part_id 
									AND sp.esta_id != '15' 						
									AND spi.sopg_acc_pp = pa.acce_id AND pa.aces_id = spi.sopg_acc_esp
								GROUP BY
									spi.sopg_sub_espe
											
								UNION
											
								SELECT
									SUM(rcomp_debe) - SUM(rcomp_haber) AS montos,
									src.part_id AS partida
								FROM
									sai_reng_comp src
									INNER JOIN sai_codi c ON (c.comp_id = src.comp_id)
								WHERE
									c.nro_compromiso = '".$compId."'
									AND src.part_id = p.part_id 
									AND c.esta_id != 15 			
									AND src.pr_ac = pa.acce_id AND pa.aces_id = src.a_esp					
								GROUP BY
									src.part_id
							) AS disponible
						GROUP BY
							disponible.partida
					),
					ci.comp_monto
				) AS monto,
				--),ci.comp_monto) AS monto,
				pa.centro_gestor||'/'||pa.centro_costo AS proy_acc,
				c.cpat_id AS cpat_id,
				ci.comp_acc_pp AS id_proyecto,
				ci.comp_acc_esp AS id_aesp,
				ci.comp_tipo_impu AS proy_tipo			
		FROM
				--sai_comp_imputa ci
				(
					SELECT 
						*
					from  
						sai_comp_imputa 
					where 
						comp_id  = '".$compId."'
					UNION
					SELECT 
						partidas.comp_id,
						partidas.partida,
						imp.comp_acc_esp,
						0 as comp_monto,
						imp.comp_acc_pp,
						imp.depe_id,
						imp.comp_tipo_impu,
						imp.pres_anno,
						imp.comp_monto_exento
					FROM                     
						(
						SELECT  sp.comp_id,
							spi.sopg_sub_espe AS partida
						FROM
							sai_sol_pago_imputa spi
							INNER JOIN sai_sol_pago sp ON (sp.sopg_id = spi.sopg_id)
						WHERE
							sp.comp_id= '".$compId."'
							AND sp.esta_id != '15' 						
						UNION 
						SELECT	
							c.nro_compromiso,	
							src.part_id AS partida
						FROM
							sai_reng_comp src
							INNER JOIN sai_codi c ON (c.comp_id = src.comp_id)
						WHERE
							c.nro_compromiso = '".$compId."'
							AND c.esta_id != 15 
							AND src.part_id NOT LIKE '4.11%'		
						) as partidas
						INNER JOIN sai_comp_imputa imp on (imp.comp_id =  partidas.comp_id)
					WHERE 
						partidas.partida NOT IN 
						(
							SELECT   
							comp_sub_espe 
							from  
							sai_comp_imputa 
							where 
							comp_id  = '".$compId."'
						)
					GROUP BY 
						partidas.comp_id,
						partidas.partida,
						imp.comp_acc_esp,
						imp.comp_acc_pp,
						imp.depe_id,
						imp.comp_tipo_impu,
						imp.pres_anno,
						imp.comp_monto_exento
				) as ci
				INNER JOIN sai_partida p ON (p.part_id = ci.comp_sub_espe)        
				INNER JOIN sai_acce_esp pa ON (pa.acce_id = ci.comp_acc_pp AND pa.aces_id = ci.comp_acc_esp)        
				INNER JOIN sai_convertidor c ON (c.part_id = ci.comp_sub_espe)        
			WHERE
				--ci.comp_id  = '".$compId."' and
				p.pres_anno = ci.pres_anno
		";
	
		//error_log(print_r($query,true));
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
	
			while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
	
				$partidas[$row['partida']]['nombre_partida'] =  utf8_encode($row['partida_nombre']);
				$partidas[$row['partida']]['monto'] =  $row['monto'];
				$partidas[$row['partida']]['proy_acc'] =  $row['proy_acc'];
				$partidas[$row['partida']]['cpat_id']= $row['cpat_id'];
				$partidas[$row['partida']]['id_proy']= $row['id_proyecto'];	
				$partidas[$row['partida']]['id_aesp']= $row['id_aesp'];
				$partidas[$row['partida']]['proy_tipo']= $row['proy_tipo'];
			}
			
			return $partidas;
		}
	}

	public static function AnularcomTrazaReporte($compId)
	{

		$query = "
		   SELECT 
		         *
	       FROM  
	             sai_comp_imputa
	       WHERE 
	       
	       Comp_id  = '".$compId."'";


		if($result = $GLOBALS['SafiClassDb']->Query($query)){

			while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

				$partidas['codPartida'][] =  $row['comp_sub_espe'];
				$partidas['codProyAccEsp'][] =  $row['comp_acc_esp'];
				$partidas['codProyAcc'][] =  $row['comp_acc_pp'];
				$partidas['tipo'][] =  $row['comp_tipo_impu'];
				$partidas['monto'][] = ($row['comp_monto'] ) * -1;
			}


			return $partidas;

		}
	}



	public static function LlenarCompImputa(array $row = null,$partida,$proyecto,$proyectoEsp,$accionCentralizada,$accionCentralizadaEsp,$dependencia)
	{
		$compromiso = new EntidadCompromisoImputa();



		if(isset($row['comp_tipo_impu']) && ($row['comp_tipo_impu'] > 0)){
			//es un proyecto

			$compromiso->SetProyecto($proyecto);
			$compromiso->SetProyectoEspecifica($proyectoEsp);




		}else{

			//es una acc. centralizada


			//error_log(print_r($accionCentralizadaEsp,true));
			$compromiso->SetAccionCentralizada($accionCentralizada);
			$compromiso->SetAccionCentralizadaEspecifica($accionCentralizadaEsp);


		}


		$compromiso->SetId($row['comp_id']);
		$compromiso->SetMonto($row['comp_monto']);
		$compromiso->SetTipoImpu($row['comp_tipo_impu']);
		$compromiso->SetPresAnno($row['comp_anno']);
		$compromiso->SetPartida($partida);
		$compromiso->SetDependencia($dependencia);

			

		//error_log(print_r($puntoCuenta,true));

		return $compromiso;
	}


	public static function GetCompImputaFiltro($param1 = null,$param2 = null)
	{
		$and = false;

		if($param1){
			$and == true? $queryWhere .= 'AND ' : $and = true ;

			$queryWhere .= "comp_sub_espe = '".$param1."'";
		}

		if($param2){
			$and == true? $queryWhere .= 'AND ' : $and = true ;

			$queryWhere .= "comp_acc_esp = '".$param2."'";
		}

		$data = array();
			
		$query = "
	   SELECT comp_id
       FROM  sai_comp_imputa
       WHERE 
       ".$queryWhere."";


		$result = $GLOBALS['SafiClassDb']->Query($query);
		while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {

			$data[] = $row['comp_id'] ;


		}
		return $data;


	}



}