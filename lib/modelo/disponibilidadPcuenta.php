<?php
class SafiModeloDisponibilidadPcuenta
{


	/*  Primero consulta los montos programados para esta partida (FORMA 1125)  */
	public static function GetMontosProgramados(array $params = null)
	{

		try
		{

			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();

			if($resultTransaction === false)
			throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

			if($params === null)
			throw new Exception("El parámetro \"params\" es nulo en GetMontosProgramados.");
			if(!is_array($params))
			throw new Exception("El parámetro \"params\" no es un arreglo en GetMontosProgramados.");
			if(count($params) == 0)
			throw new Exception("El parámetro \"params\" está vacío en GetMontosProgramados.");

			$query = "

		SELECT 
		   COALESCE(SUM(b.fodt_monto),0) as monto_p 
		   
		FROM
		   sai_forma_1125 a
		    INNER JOIN sai_fo1125_det b ON(b.form_id=a.form_id)
		
		WHERE 
		    a.pres_anno='".$params['pres_anno']."' AND 
		    a.form_id_p_ac='".$params['form_id_p_ac']."' AND 
		    a.form_tipo= ".$params['form_tipo']."::bit AND 
		    a.form_id_aesp='".$params['form_id_aesp']."' AND   
		    a.esta_id<>15 AND 
		    a.esta_id<>2 AND 
		    a.pres_anno=b.pres_anno AND 
		    b.part_id='".$params['part_id']."'";


		


			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception('Error al obtener MontosProgramados. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$monto_prog = $row["monto_p"];
			}else{
			
				$result = false;
			}


			if($GLOBALS['SafiClassDb']->CommitTransaction() === false)
			throw new Exception('Error al intentar hacer commit. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
         //   error_log($monto_prog);
			return $monto_prog;


		}
		catch(Exception $e)
		{    
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
				
		}

	}
	
	
	
   /*  Luego consulta las modificaciones realizadas sobre dicha partida */
   /*  Primero, montos recibidos */
   public static function GetMontosRecibidos(array $params = null)
	{

		try
		{

			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();

			if($resultTransaction === false)
			throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

			if($params === null)
			throw new Exception("El parámetro \"params\" es nulo en GetMontosRecibidos.");
			if(!is_array($params))
			throw new Exception("El parámetro \"params\" no es un arreglo en GetMontosRecibidos.");
			if(count($params) == 0)
			throw new Exception("El parámetro \"params\" está vacío en GetMontosRecibidos.");

			
			$query = "
		SELECT 
		     COALESCE(SUM(b.f0dt_monto),0) as monto_r 
		     
		FROM 
		   sai_forma_0305 a
		   INNER JOIN sai_fo0305_det b  ON(b.f030_id=a.f030_id)
		   INNER JOIN sai_doc_genera dg  ON(dg.docg_id=a.f030_id)

		WHERE 
		    a.pres_anno='".$params['pres_anno']."' AND 
		    b.f0dt_id_p_ac='".$params['form_id_p_ac']."' AND 
		    b.f0dt_proy_ac= ".$params['form_tipo']."::bit AND 
		    b.f0dt_id_acesp='".$params['form_id_aesp']."' AND  
		    a.esta_id<>15 AND 
		    a.esta_id<>2 AND 
		    a.pres_anno=b.pres_anno AND
		     dg.esta_id = 13 AND
			
		    b.part_id='".$params['part_id']."'  AND 
		    b.f0dt_tipo= 1::bit";



			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception('Error al obtener MontosRecibidos. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$monto_rec = $row["monto_r"];
			}else{
			
				$result = false;
			}


			if($GLOBALS['SafiClassDb']->CommitTransaction() === false)
			throw new Exception('Error al intentar hacer commit. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
          //  error_log($monto_rec);
			return $monto_rec;


		}
		catch(Exception $e)
		{
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
				
		}

	}
	
	

 
    /*  Segundo, montos cedidos */
   public static function GetMontosCedidos(array $params = null)
	{
		try
		{

			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();

			if($resultTransaction === false)
			throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

			if($params === null)
			throw new Exception("El parámetro \"params\" es nulo en GetMontosCedidos.");
			if(!is_array($params))
			throw new Exception("El parámetro \"params\" no es un arreglo en GetMontosCedidos.");
			if(count($params) == 0)
			throw new Exception("El parámetro \"params\" está vacío en GetMontosCedidos.");


			$query = "
		SELECT 
		     COALESCE(SUM(b.f0dt_monto),0) as monto_c 
		     
		FROM 
		   sai_forma_0305 a
		   INNER JOIN sai_fo0305_det b  ON(b.f030_id=a.f030_id)
		   INNER JOIN sai_doc_genera dg  ON(dg.docg_id=a.f030_id)
		   

		WHERE 
		    a.pres_anno='".$params['pres_anno']."' AND 
		    b.f0dt_id_p_ac='".$params['form_id_p_ac']."' AND 
		    b.f0dt_proy_ac= ".$params['form_tipo']."::bit AND 
		    b.f0dt_id_acesp='".$params['form_id_aesp']."' AND  
		    a.esta_id<>15 AND 
		    a.esta_id<>2 AND ";
			
			if(!$params['pmod']){
			
				
			  $query .= "   dg.esta_id = 13 AND";
			  
			}
		
		    
			$query .=" a.pres_anno=b.pres_anno AND 
		    b.part_id='".$params['part_id']."' AND 
		    b.f0dt_tipo= 0::bit";
		

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception('Error al obtener MontosCedidos. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$monto_ced = $row["monto_c"];
			}else{
			
				$result = false;
			}


			if($GLOBALS['SafiClassDb']->CommitTransaction() === false)
			throw new Exception('Error al intentar hacer commit. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
         //   error_log($monto_ced);
			return $monto_ced;


		}
		catch(Exception $e)
		{
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
				
		}

	}

	
	
	
	/*  Ahora calcula los montos diferidos  */ 
   public static function GetMontosDiferidos(array $params = null)
	{

		try
		{

			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();

			if($resultTransaction === false)
			throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

			if($params === null)
			throw new Exception("El parámetro \"params\" es nulo en GetMontosDiferidos.");
			if(!is_array($params))
			throw new Exception("El parámetro \"params\" no es un arreglo en GetMontosDiferidos.");
			if(count($params) == 0)
			throw new Exception("El parámetro \"params\" está vacío en GetMontosDiferidos.");


			$query = "
			
			SELECT 
    COALESCE(SUM(b.pcta_monto),0) as monto_d
    
FROM  
    sai_pcuenta  a
    INNER JOIN sai_pcta_imputa b ON(b.pcta_id=a.pcta_id)
    INNER JOIN sai_doc_genera d ON(d.docg_id=a.pcta_id)  

WHERE 
    b.pres_anno='".$params['pres_anno']."' AND 
    a.esta_id<>15 AND 
    a.esta_id<>14 AND 
    a.esta_id<>2 AND 
    b.pcta_tipo_impu= ".$params['form_tipo']."::bit  AND 
    b.pcta_acc_pp='".$params['form_id_p_ac']."' AND
    b.pcta_acc_esp='".$params['form_id_aesp']."' AND 
    b.pcta_sub_espe = '".$params['part_id']."'";
    

			//error_log($query);


			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception('Error al obtener MontosDiferidos. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$monto_ced = $row["monto_d"];
			}else{
			
				$result = false;
			}


			if($GLOBALS['SafiClassDb']->CommitTransaction() === false)
			throw new Exception('Error al intentar hacer commit. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
          //  error_log($monto_ced);
			return $monto_ced;


		}
		catch(Exception $e)
		{
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
				
		}

	}
	
	
/*  Ahora calcula los montos comprometidos  */  
   public static function GetMontosComprometidos(array $params = null)
	{

		try
		{

			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();

			if($resultTransaction === false)
			throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

			if($params === null)
			throw new Exception("El parámetro \"params\" es nulo en GetMontosComprometidos.");
			if(!is_array($params))
			throw new Exception("El parámetro \"params\" no es un arreglo en GetMontosComprometidos.");
			if(count($params) == 0)
			throw new Exception("El parámetro \"params\" está vacío en GetMontosComprometidos.");


		$query = "
		
			SELECT 
			    COALESCE(SUM(b.comp_monto),0) as monto_c 
			    
			FROM  
			   sai_comp a
			   INNER JOIN sai_comp_imputa b ON(b.comp_id=a.comp_id) 
			   
			WHERE 
			  b.pres_anno='".$params['pres_anno']."' AND 
			  a.esta_id<>15 AND 
			  a.esta_id<>2 AND 
			  b.comp_tipo_impu=".$params['form_tipo']."::bit AND 
			  b.comp_acc_pp='".$params['form_id_p_ac']."' AND 
			  b.comp_acc_esp='".$params['form_id_aesp']."' AND 
			  length(a.pcta_id)>4 and 
			  b.comp_sub_espe = '".$params['part_id']."'";
    

			//error_log($query);


			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception('Error al obtener MontosComprometidos. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$monto_ced = $row["monto_c"];
			}else{
			
				$result = false;
			}


			if($GLOBALS['SafiClassDb']->CommitTransaction() === false)
			throw new Exception('Error al intentar hacer commit. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
          //  error_log($monto_ced);
			return $monto_ced;


		}
		catch(Exception $e)
		{
			$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
				
		}

	}
	
	
	
	
/*  Ahora calcula los montos comprometidos aislados  */ 
	 public static function GetMontosComprometidosAislados(array $params = null)
		{
	
			try
			{
	
				$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
	
				if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
	
				if($params === null)
				throw new Exception("El parámetro \"params\" es nulo en GetMontosComprometidosAislados.");
				if(!is_array($params))
				throw new Exception("El parámetro \"params\" no es un arreglo en GetMontosComprometidosAislados.");
				if(count($params) == 0)
				throw new Exception("El parámetro \"params\" está vacío en GetMontosComprometidosAislados.");
	
	
			$query = "
			
				SELECT 
				    COALESCE(SUM(b.comp_monto),0) as monto_c 
				    
				FROM  
				   sai_comp a
				   INNER JOIN sai_comp_imputa b ON(b.comp_id=a.comp_id) 
				   
				WHERE 
				  b.pres_anno='".$params['pres_anno']."' AND 
				  a.esta_id<>15 AND 
				  a.esta_id<>2 AND 
				  b.comp_tipo_impu=".$params['form_tipo']."::bit AND 
				  b.comp_acc_pp='".$params['form_id_p_ac']."' AND 
				  b.comp_acc_esp='".$params['form_id_aesp']."' AND 
				  length(a.pcta_id)<4 and 
				  b.comp_sub_espe = '".$params['part_id']."'";
	    
	
				//error_log($query);
	
	
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception('Error al obtener MontosComprometidosAislados. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
	
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$monto_ced = $row["monto_c"];
				}else{
				
					$result = false;
				}
	
	
				if($GLOBALS['SafiClassDb']->CommitTransaction() === false)
				throw new Exception('Error al intentar hacer commit. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
				
	         //   error_log($monto_ced);
				return $monto_ced;
	
	
			}
			catch(Exception $e)
			{
				$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
				error_log($e, 0);
				return false;
					
			}
	
		}
		
		
	/*  Ahora calcula los montos causados  */ 
	 public static function GetMontosCausados(array $params = null)
		{
	
			try
			{
	
				$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
	
				if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
	
				if($params === null)
				throw new Exception("El parámetro \"params\" es nulo en GetMontosCausados.");
				if(!is_array($params))
				throw new Exception("El parámetro \"params\" no es un arreglo en GetMontosCausados.");
				if(count($params) == 0)
				throw new Exception("El parámetro \"params\" está vacío en GetMontosCausados.");
	
	
			$query = "
			
				SELECT 
				   COALESCE(SUM(b.cadt_monto),0) as monto_c  
				   
				FROM  
				   sai_causado a
				   INNER JOIN sai_causad_det b ON(b.caus_id=a.caus_id) 
				   
				WHERE 
				   a.pres_anno='".$params['pres_anno']."' AND 
				   a.esta_id<>15 AND 
				   a.esta_id<>2 AND 
				   a.pres_anno=b.pres_anno AND 
				   b.cadt_tipo=".$params['form_tipo']."::bit AND 
				   b.cadt_id_p_ac='".$params['form_id_p_ac']."' AND 
				   b.cadt_cod_aesp='".$params['form_id_aesp']."' AND 
				   b.part_id = '".$params['part_id']."' AND 
				   b.cadt_abono='1' ";
	    
	
				//error_log($query);
	
	
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception('Error al obtener MontosCausados. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
	
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$monto_ced = $row["monto_c"];
				}else{
				
					$result = false;
				}
	
	
				if($GLOBALS['SafiClassDb']->CommitTransaction() === false)
				throw new Exception('Error al intentar hacer commit. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
				
	           // error_log($monto_ced);
				return $monto_ced;
	
	
			}
			catch(Exception $e)
			{
				$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
				error_log($e, 0);
				return false;
					
			}
	
		}
		
		
		
		

}