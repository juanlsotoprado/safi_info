<?php
include_once(SAFI_ENTIDADES_PATH . '/empleado.php');;

class SafiModeloEmpleado
{
	
	
//*************************************************** CONSULTA DE TRES TABLAS  sai_proveedor_nuevo,sai_viat_benef,sai_empleado ****************
	
	
   public static function GetProveedorSugerido($key,$tipo = 'false')
	{
		$params = array();
		
		$key = strtoupper($key);
		
		$query = "
			SELECT
				proveedor_sugerido.*
			FROM
				( 
				";
		
		if($tipo == 'false'|| $tipo == "proveedor"){

			$query .= "	SELECT prov_id_rif as id,
		            prov_nombre as nombre,
		             'proveedor' as tipo
					FROM sai_proveedor_nuevo 
					WHERE 
					prov_esta_id=1
					AND ( UPPER(prov_id_rif) LIKE '%".strtoupper ($key)."%'
					OR UPPER(prov_nombre) LIKE '%".strtoupper ($key)."%') ";
		}
	

		

		if($tipo ==  'false'){ 

		   $query .= "UNION ";
		   
		}
		
		if($tipo == 'false' || $tipo == "otro"){
				$query .= "	SELECT benvi_cedula as id, 
					(benvi_nombres || ' ' || benvi_apellidos)  as nombre,
					 'otro' as tipo
					FROM sai_viat_benef 
					WHERE benvi_esta_id=1
					AND (UPPER(benvi_cedula) LIKE '%".strtoupper ($key)."%'
					OR UPPER(benvi_nombres) LIKE '%".strtoupper ($key)."%'
					OR UPPER(benvi_apellidos) LIKE '%".strtoupper ($key)."%')
		";
				
		}
				
		if($tipo == 'false'){ 	
		   $query .= "UNION ";
		   
		}
			if($tipo == 'false' || $tipo == "empleado"){   
				$query .= "			SELECT empl_cedula as id, 
				   	(empl_nombres || ' ' || empl_apellidos) as nombre,
				    'empleado' as tipo
					FROM sai_empleado 
					WHERE esta_id=1
					AND (UPPER(empl_cedula) LIKE '%".strtoupper ($key)."%'
					OR UPPER(empl_nombres) LIKE '%".strtoupper ($key)."%'
					OR UPPER(empl_apellidos) LIKE '%".strtoupper ($key)."%') ";
					
			}
		$query .= "	) AS proveedor_sugerido 
			LIMIT 100 ";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		   
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			
			$params[$row['id']] = array(
				'id' => $row['id'],
				'nombre' => $row['nombre'],
		      	'tipo' => $row['tipo']
			);
			
			
		}

		
		return $params;
	}
	
	
 public static function GetProveedoresSugerido($params)
	{

		$params2 = array();
		$query = "
			SELECT
				proveedor_sugerido.*
			FROM
				(		
					SELECT prov_id_rif as id,
		            prov_nombre as nombre 
					FROM sai_proveedor_nuevo 
					WHERE 
					prov_esta_id=1
					AND prov_id_rif IN ('".implode("', '",$params)."')
		    		   UNION
		    	
					SELECT benvi_cedula as id, 
					(benvi_nombres || ' ' || benvi_apellidos)  as nombre
					FROM sai_viat_benef 
					WHERE benvi_esta_id=1
					AND benvi_cedula IN ('".implode("', '",$params)."')

		     		  UNION
		     
					SELECT empl_cedula as id, 
				   	(empl_nombres || ' ' || empl_apellidos) as nombre
					FROM sai_empleado 
					WHERE esta_id=1
					AND empl_cedula IN ('".implode("', '",$params)."')
				) AS proveedor_sugerido 
		";
		

		
		$result = $GLOBALS['SafiClassDb']->Query($query);
 
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			
			$params2[$row['id']] = array(
				'id' => $row['id'],
				'nombre' => $row['nombre']
			);
			
			
		}
		
		return $params2;
	
	}
	
	
//********************************************************************************************************************************
	
	
public static function nombre_apellido_perfil($id_depe,$caso)
	{
	try{
		  // $id_depe = 350;
		  
		$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
		if($resultTransaction === false){
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
		}
		
		
     	$depenPresi = DEPENDENCIA_PRESIDENCIA;
        $depenDir_ejec= DEPENDENCIA_DIRECCION_EJECUTIVA ;
        $talentoh =DEPENDENCIA_OFICINA_TALENTO_HUMANO;
		
        $presi = substr(PERFIL_PRESIDENTE,0,2);
        $dir_ejec= substr(PERFIL_DIRECTOR_EJECUTIVO,0,2);
        $director= substr(PERFIL_DIRECTOR,0,2);
        $consultor= substr(PERFIL_CONSULTOR_JURIDICO,0,2);
        $coordinador= substr(PERFIL_COORDINADOR,0,2);
        $gerente= substr(PERFIL_GERENTE,0,2);
      
        

	
        if ($id_depe == $depenPresi) {
        	
         $query="SELECT
      se.empl_cedula AS empl_cedula,
      se.empl_nombres AS empl_nombres,
      se.empl_apellidos AS empl_apellidos
        
      FROM sai_empleado se
        
      INNER JOIN sai_usuario su ON (su.empl_cedula = se.empl_cedula)	
      WHERE
        su.usua_activo=true";
         
        $caso == 2? $query .="  AND depe_cosige like '".$id_depe."%'" : "";
        
       $query .=" AND (carg_fundacion='".$presi."')";
       
       $query .=" ORDER BY se.empl_nombres ASC ";
       

       if($result = $GLOBALS['SafiClassDb']->Query($query)){
     
     	     $i=0;
     	 while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
              
     	 $reporte[$i]['ci'] = $row['empl_cedula'];	
     	 $reporte[$i]['nombres'] = $row['empl_nombres'];	
     	 $reporte[$i]['apellidos'] = $row['empl_apellidos'];
             
    
        $i++;
        
     	 }
       }

       }else if($id_depe == $depenDir_ejec){
       	
      $query="SELECT 
      se.empl_cedula AS empl_cedula,
      se.empl_nombres AS empl_nombres,
      se.empl_apellidos AS empl_apellidos
        
      FROM sai_empleado se
        
      INNER JOIN sai_usuario su ON (su.empl_cedula = se.empl_cedula)	

      WHERE
        su.usua_activo=true ";
      
     $caso == 2? $query .="  AND depe_cosige like '".$id_depe."%'" : ""; 
      
     $query .="   AND (carg_fundacion='".$dir_ejec."' 
        or   carg_fundacion='".$presi."')";

     $query .=" ORDER BY se.empl_nombres ASC ";
     
       if($result = $GLOBALS['SafiClassDb']->Query($query)){
     
     	     $i=0;
     	 while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
              
     	 $reporte[$i]['ci'] = $row['empl_cedula'];	
     	 $reporte[$i]['nombres'] = $row['empl_nombres'];	
     	 $reporte[$i]['apellidos'] = $row['empl_apellidos'];
             
    
        $i++;
        
     	 }
       }  	
     }else if($id_depe == $talentoh){
     	
     	
     	
      $query="SELECT 
      se.empl_cedula AS empl_cedula,
      se.empl_nombres AS empl_nombres,
      se.empl_apellidos AS empl_apellidos
        
      FROM sai_empleado se
        
      INNER JOIN sai_usuario su ON (su.empl_cedula = se.empl_cedula)	

      WHERE
        su.usua_activo=true"; 
      
      $caso == 2? $query .="  AND depe_cosige like '".$id_depe."%'" : ""; 
      
      $query .="  AND (carg_fundacion='".$director."' 
        OR carg_fundacion='".$consultor."' 
         OR carg_fundacion='".$dir_ejec."' 
         OR carg_fundacion='".$presi."' 
          OR (carg_fundacion='".$coordinador."' AND depe_cosige ='".DEPENDENCIA_COORDINACION_DE_ATENCION_AL_SOBERANO."')
        OR carg_fundacion='".$gerente."')";
     	
     	
     	  }else{  
  
      $query="SELECT 
      se.empl_cedula AS empl_cedula,
      se.empl_nombres AS empl_nombres,
      se.empl_apellidos AS empl_apellidos
        
      FROM sai_empleado se
        
      INNER JOIN sai_usuario su ON (su.empl_cedula = se.empl_cedula)	

      WHERE
        su.usua_activo=true"; 
      
      $caso == 2? $query .="  AND depe_cosige like '".$id_depe."%'" : ""; 
      
      $query .="  AND (carg_fundacion='".$director."' 
        OR carg_fundacion='".$consultor."' 
        OR carg_fundacion='".$gerente."')";
        
       }

      $query .=" ORDER BY se.empl_nombres ASC ";
      
  /*-    
echo "<pre>";
echo print_r($query);
echo "</pre>";
      
      */
       if($result = $GLOBALS['SafiClassDb']->Query($query)){
     
     	     $i=0;
     	 while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
              
     	 $reporte[$i]['ci'] = $row['empl_cedula'];	
     	 $reporte[$i]['nombres'] = $row['empl_nombres'];	
     	 $reporte[$i]['apellidos'] = $row['empl_apellidos'];
             
    
        $i++;
     }

       }
       
		$result = $GLOBALS['SafiClassDb']->CommitTransaction();
		if($result === false)
				throw new Exception("Error al ejecutar  el commit en la funcion de seleccionar del nombre_apellido_perfil. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

		       
      return $reporte;			
					
	   }catch (Exception $e)
		{
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}				

	
	 }
	
	
	public static function GetEmpleadosActivos()
	{
		$empleados = array();
		$query = "
			SELECT
				empl_cedula,
				empl_nombres,
				empl_apellidos,
				empl_tlf_ofic,
				nacionalidad,
				empl_email,
				depe_cosige,
				carg_fundacion,
				empl_observa,
				usua_login,
				esta_id
			FROM
				sai_empleado
			WHERE
				esta_id = 1
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$empleados[$row['empl_cedula']] = $row;
		}
		
		return $empleados;
	}
	
	public static function GetEmpleadoActivoByCedula($cedula)
	{
		$empleado = null;
	
		$query = "
			SELECT
				empl_cedula,
				empl_nombres,
				empl_apellidos,
				empl_tlf_ofic,
				nacionalidad,
				empl_email,
				depe_cosige,
				carg_fundacion,
				empl_observa,
				usua_login,
				esta_id,
				banco_nomina,
				tipo_cuenta_nomina,
				cuenta_nomina
			FROM
				sai_empleado
			WHERE
				esta_id = 1 AND
				empl_cedula = '" . $cedula . "'
		";
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			$empleado = $GLOBALS['SafiClassDb']->Fetch($result);
		}

		return $empleado;
	}
	
	public static function GetEmpleadosByCedulas(array $cedulas = null)
	{
		$empleados = null;
		
		$query = "
			SELECT
				empl_cedula,
				empl_nombres,
				empl_apellidos,
				empl_tlf_ofic,
				nacionalidad,
				empl_email,
				depe_cosige,
				carg_fundacion,
				empl_observa,
				usua_login,
				esta_id
			FROM
				sai_empleado
			WHERE
				empl_cedula IN ('" . implode("', '", $cedulas) . "')
		";
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$empleados[$row['empl_cedula']] = self::LlenarEmpleado($row);
			}
		}
		
		return $empleados;
	}
	
	public static function GetEmpleadoActivoByCedula2($cedula)
	{
		$empleado = null;
		
		$query = "
			SELECT
				empl_cedula,
				empl_nombres,
				empl_apellidos,
				empl_tlf_ofic,
				nacionalidad,
				empl_email,
				depe_cosige,
				carg_fundacion,
				empl_observa,
				usua_login,
				esta_id
			FROM
				sai_empleado
			WHERE
				esta_id = 1 AND
				empl_cedula = '" . $cedula . "'
		";
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$empleado = self::LlenarEmpleado($row);
			}
		}
		
		return $empleado;
	}
	
	public static function GetEmpleadoByCedula($cedula)
	{
		$empleado = null;
		
		$query = "
			SELECT
				empl_cedula,
				empl_nombres,
				empl_apellidos,
				empl_tlf_ofic,
				nacionalidad,
				empl_email,
				depe_cosige,
				carg_fundacion,
				empl_observa,
				usua_login,
				esta_id
			FROM
				sai_empleado
			WHERE
				empl_cedula = '" . $cedula . "'
		";
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$empleado = self::LlenarEmpleado($row);
			}
		}
		
		return $empleado;
	}
	
	public static function Search($key, $numItems, array $params = null)
	{
		try
		{
			$preMsg = "Error al intentar buscar los empleados.";
			$empleados = array();
			$where = "";
			
			if($params != null && is_array($params))
			{
				if(($idEstatus=$params['idEstatus']) != null && ($idEstatus=trim($idEstatus))){
					$where = "AND esta_id = " . $idEstatus;
				}
			}
			
			$query = "
				SELECT
					empl_cedula,
					empl_nombres,
					empl_apellidos,
					empl_tlf_ofic,
					nacionalidad,
					empl_email,
					depe_cosige,
					carg_fundacion,
					empl_observa,
					usua_login,
					esta_id
				FROM
					sai_empleado
				WHERE
					(
						empl_cedula LIKE '%" . $GLOBALS['SafiClassDb']->Quote(utf8_decode($key)) . "%' OR
						lower(empl_nombres || ' ' || empl_apellidos) LIKE '%"
							. utf8_decode(mb_strtolower($GLOBALS['SafiClassDb']->Quote($key), 'UTF-8')) . "%'
					)
					".$where."
				LIMIT
					" . $numItems . "
			";
			
			$result = $GLOBALS['SafiClassDb']->Query($query);
			
			if($result === false)
				throw new Exception(utf8_decode($preMsg." Detalles: " . $GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$empleados[$row['empl_cedula']] = $row;
			}
			
			return $empleados;
		}
		catch (Exception $e)
		{
			error_log($e);
			return null;
		}
	}
	
	public static function GetEmpleadoByCargoFundacionYDependencia($cargoFundacion, EntidadDependencia $dependencia)
	{
		try
		{
			$preMsg = "Error al intentar obtener un empleado dado los cargoFundacion y dependencia";
			$empleado = null;	
			
			$query = "
				SELECT
					e.empl_cedula,
					e.empl_nombres,
					e.empl_apellidos,
					e.empl_tlf_ofic,
					e.nacionalidad,
					e.empl_email,
					e.depe_cosige,
					e.carg_fundacion,
					e.empl_observa,
					e.usua_login,
					e.esta_id
				FROM 
					sai_empleado e
				WHERE
					e.carg_fundacion = '".$cargoFundacion."' AND
					e.depe_cosige = '".$dependencia->GetId()."' AND
					e.esta_id = 1
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$empleado = self::LlenarEmpleado($row);
			}
			
			return $empleado;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}

	public static function GetEmpleadoByCargo($perfil)
	{
		try
		{
			$preMsg = "Error al intentar obtener un empleado dado el cargoFundacion";
			$empleados = array();
				
			$query = "
				SELECT
					e.empl_cedula,
					e.empl_nombres,
					e.empl_apellidos
				FROM
					sai_empleado e
				INNER JOIN sai_usua_perfil up ON (up.usua_login = e.empl_cedula)
				WHERE e.esta_id = 1 AND
					up.carg_id= '".$perfil."' AND
					uspe_fin is NULL AND 
					up.usua_login NOT IN (17286920,14045453, 15586921, 19163767)
				ORDER BY e.empl_nombres								
			";
			//echo $query;
				
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$empleados[] = self::LlenarEmpleado($row);
			}
				
			return $empleados;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	
	public static function GetEmpleadosByUsuaLogins(array $usuaLogins)
	{
		$empleados = null;
		
		try {
			$query = "
				SELECT
					empleado.empl_cedula,
					empleado.empl_nombres,
					empleado.empl_apellidos,
					empleado.empl_tlf_ofic,
					empleado.nacionalidad,
					empleado.empl_email,
					empleado.depe_cosige,
					empleado.carg_fundacion,
					empleado.empl_observa,
					empleado.usua_login,
					empleado.esta_id,
					usuario.usua_login AS usuario_usua_login
				FROM
					sai_empleado empleado
					INNER JOIN sai_usuario usuario ON (usuario.empl_cedula = empleado.empl_cedula)
				WHERE
					usuario.usua_login IN ('".implode("', '", $usuaLogins)."')
			";
				
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al obtener los empleados dado sus usuaLogins. Detalles: ". 
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$empleados = array();
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$empleados[$row['usuario_usua_login']] = self::LlenarEmpleado($row);
			}
			
		} catch (Exception $e) {
			error_log($e, 0);
			return false;
		}
		
		return $empleados;
	}
	
	private static function LlenarEmpleado($row){
		$empleado = new EntidadEmpleado();
		
		$empleado->SetId($row['empl_cedula']);
		$empleado->SetNombres($row['empl_nombres']);
		$empleado->SetApellidos($row['empl_apellidos']);
		
		return $empleado;
	}
}