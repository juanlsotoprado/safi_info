<?php
include_once(SAFI_ENTIDADES_PATH . '/wfcadena.php');

class SafiModeloWFCadena
{
	
  /************************************************ Nueva cadena *****************************************************/
	
// Cadena 

    public static function GetWfca_id_inicial($lugar = NULL)
	{
		 $query = "SELECT 
		             MIN(sc.wfca_id) as wfca_id
		           FROM  
		             sai_wfcadena sc 
		           WHERE 
		              sc.docu_id = '".$lugar."' AND 
		              sc.wfca_proyecto = 1";
		 
		 
		$result = $GLOBALS['SafiClassDb']->Query($query);
		if($row = $GLOBALS['SafiClassDb']->Fetch($result)){
			
			
			return  $row['wfca_id'];
		}
		 
	}
	
	
	
   public static function GetId_cadena_hijo_id_cadena($param = null)
	{
	
       $data = array();
       
       
	   $query = "
	   
                  SELECT 
	                   ch.id_cadena_hijo AS id_cadena_hijo,
	                   ch.opcion,
	                   op.wfop_nombre,
	                   op.wfop_descrip
	                   
		           FROM  
		             cadena_hijos ch
		             INNER JOIN sai_wfopcion op ON (op.wfop_id = ch.opcion)
		             
		           WHERE 
		             ch.id_cadena =".$param."
		          
		            ORDER BY ch.id_cadena_hijo ASC  ";
	   
	     $result = $GLOBALS['SafiClassDb']->Query($query);
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				
			$data[] = $row;
			
		}
		

		 return $data;
	}
	
	
	
	
   public static function GetId_cadena_hijos_id_cadenas(array $param = null)
	{
	
       $data = array();
       
       
	   $query = "
	   
                  SELECT 
                       ch.id_cadena AS id_cadena,
	                   ch.id_cadena_hijo AS id_cadena_hijo,
	                   ch.opcion,
	                   op.wfop_nombre,
	                   op.wfop_descrip
	                   
		           FROM  
		             cadena_hijos ch
		             INNER JOIN sai_wfopcion op ON (op.wfop_id = ch.opcion)
		             
		           WHERE 
		             ch.id_cadena  IN ('".implode("', '", $param)."')
		          
		            ORDER BY ch.id_cadena_hijo ASC  ";
	   
	     $result = $GLOBALS['SafiClassDb']->Query($query);
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				
			$data[$row['id_cadena']][] = $row;
			
		}
		

		 return $data;
	}
	
	
    public static function GetCadenaIdGrupo($param = 0)///////////////////////////////////
	{

		$query = "SELECT
	                 wfgr_id
	                 
                  FROM 
                     sai_wfcadena
                     
                  WHERE 
                      wfca_id = ".$param."

                  LIMIT 1";
		 
		$result = $GLOBALS['SafiClassDb']->Query($query);
		if($row = $GLOBALS['SafiClassDb']->Fetch($result)){
			 
			return $row['wfgr_id'];


		}
		
	}

	
	 public static function GetCadenasIdGrupos(array $params = null)
	{
        $data = array(); 
		$query = "SELECT
	                 wfgr_id,
	                 wfca_id
	                 
                  FROM 
                     sai_wfcadena
                     
                  WHERE 
                      wfca_id  IN ('".implode("', '", $params)."')";
		
		
		 
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			
		while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
			 
			$data[$row['wfca_id']] = $row['wfgr_id'];

		}
		
		return $data;
		 
		}else{
		
			return false;
			
	}

	}
	
   public static function GetPerfilSiguiente(array $params = null)
	{
		
		
     $data = array();

     $query= "  SELECT 
     			   wfgr_perf
     			   
     			FROM 
     			   sai_wfgrupo 
     			   
     			WHERE 
     			   wfgr_id=".$params['CadenaGrupo'];
     
        $result = $GLOBALS['SafiClassDb']->Query($query);
		if($row = $GLOBALS['SafiClassDb']->Fetch($result)){

		 $val = strstr($row['wfgr_perf'], '/');
		    
            if($val){
        	
       
				$query = "  SELECT 
				       		   e.carg_fundacion||e.depe_cosige AS perfil_siguiente
				       		   
                            FROM 
                               sai_empleado e
                               
                            WHERE 
                               e.depe_cosige = '".$_SESSION['user_depe_id']."'AND 
                               (POSITION ('/'||e.carg_fundacion IN 
                                      (SELECT 
                                          wfgr_perf 
                                          
                                       FROM 
                                          sai_wfgrupo 
                                          
                                       WHERE wfgr_id=".$params['CadenaGrupo'].")) > 0 
                                       
		                                  OR  SUBSTRING((SELECT wfgr_perf FROM sai_wfgrupo WHERE wfgr_id=".$params['CadenaGrupo'].") FROM 1 FOR 2) = e.carg_fundacion)
                           LIMIT 1";
	   
	    $result = $GLOBALS['SafiClassDb']->Query($query);
		if($row = $GLOBALS['SafiClassDb']->Fetch($result)){
       
		$val = $row['perfil_siguiente'];
       }
        
	  
		}else{
		
			$val = $row['wfgr_perf'];
		
		} 
		
		return $val;

	 }	
	}
	

   public static function GetPerfilSiguiente2(array $params = null)
	{
		
		
     $data = array();

     $query= "  SELECT 
     			   wfgr_perf
     			   
     			FROM 
     			   sai_wfgrupo 
     			   
     			WHERE 
     			   wfgr_id=".$params['CadenaGrupo'];
     
        $result = $GLOBALS['SafiClassDb']->Query($query);
		if($row = $GLOBALS['SafiClassDb']->Fetch($result)){

		 $val = strstr($row['wfgr_perf'], '/');
		    
            if($val){
        	
       
				$query = "  SELECT 
				       		   e.carg_fundacion||e.depe_cosige AS perfil_siguiente
				       		   
                            FROM 
                               sai_empleado e
                               
                            WHERE 
                               e.depe_cosige = '".$_SESSION['user_depe_id']."'AND 
                               (POSITION ('/'||e.carg_fundacion IN 
                                      (SELECT 
                                          wfgr_perf 
                                          
                                       FROM 
                                          sai_wfgrupo 
                                          
                                       WHERE wfgr_id=".$params['CadenaGrupo'].")) > 0 
                                       
		                                  OR  SUBSTRING((SELECT wfgr_perf FROM sai_wfgrupo WHERE wfgr_id=".$params['CadenaGrupo'].") FROM 1 FOR 2) = e.carg_fundacion)
                           LIMIT 1";
	   
	    $result = $GLOBALS['SafiClassDb']->Query($query);
		if($row = $GLOBALS['SafiClassDb']->Fetch($result)){
       
		$data[$row['perfil_siguiente']] = true;
       }
        
	  
		}else{
		
			$data[$row['wfgr_perf']] = false ;
			
			
		
		} 
		
		return $data;

	 }	
	}
	
	/************************************************ Fin Nueva cadena *****************************************************/
	
	
	public static function GetWFCadena(EntidadWFCadena $wFCadena)
	{

		
		$wfCadenaResult = null;
		
	
		$where = '';
		
		if(($id=$wFCadena->GetId()) != null && ($id=trim($id)) != '')
		{
			$where = " wfc.wfca_id = " . $id;
		}
		if(	($documento=$wFCadena->GetDocumento()) != null && ($idDocumento=$documento->GetId()) != null && 
			($idDocumento=trim($idDocumento)) != '' && strcmp($idDocumento, "0") != 0
		){
			$where .= ($where != '') ? ' AND' : '';
			$where .= " wfc.docu_id = '" . $idDocumento ."'"; 
		}
		
		if(	(($wFObjeto=$wFCadena->GetWFObjetoInicial()) != null && ($idWFObjeto=$wFObjeto->GetId()) != null) &&
			($idWFObjeto = trim($idWFObjeto)) != ''
		){
			$where .= ($where != '') ? ' AND' : '';
			$where .= " wfc.wfob_id_ini = " . $idWFObjeto;
		}
		
		if(	($wFOpcion=$wFCadena->GetWFOpcion()) != null && ($idWFOpcion=$wFOpcion->GetId()) != null &&
			($idWFOpcion=trim($idWFOpcion)) != ''
		){
			$where .= ($where != '') ? ' AND' : '';
			$where .= " wfc.wfop_id = " . $idWFOpcion;
		}
		
		if( ($wFGrupo=$wFCadena->GetWFGrupo()) != null && ($idGrupo=$wFGrupo->GetId()) != null &&
			($idGrupo = trim($idGrupo)) != ''
		){
			$where .= ($where != '') ? ' AND' : '';
			$where .= " wfc.wfgr_id = " . $idGrupo;
		}
		
		if( ($dependencia=$wFCadena->GetDependencia()) != null){
			$where .= ($where != '') ? ' AND' : '';
			if(($dependencia->GetId() == null || trim($dependencia->GetId()) == '')){
				$where .= " (wfc.depe_id IS NULL OR wfc.depe_id = '')";
			} else {
				$where .= " wfc.depe_id = '" .  trim($dependencia->GetId()) . "'";
			}
		}
		
		if($where != ''){
			$query = "
				SELECT
					wfc.wfca_id,
					wfc.wfob_id_ini,
					wfc.wfob_id_sig,
					wfc.wfop_id,
					wfc.docu_id,
					wfc.wfca_proyecto,
					wfc.wfca_tipo,
					wfc.depe_id,
					wfc_padre.wfca_id AS padre_wfca_id,
					wfc_hijo.wfca_id AS hijo_wfca_id,
					wfg.wfgr_id AS g_wfgr_id,
					wfg.wfob_id AS g_wfob_id,
					wfg.wfgr_descrip AS g_wfgr_descrip,
					wfg.wfgr_perf AS g_wfgr_perf
				FROM
					sai_wfcadena wfc
					LEFT JOIN sai_wfcadena wfc_padre ON (wfc_padre.wfca_id = wfc.wfca_id_padre)
					LEFT JOIN sai_wfcadena wfc_hijo ON (wfc_hijo.wfca_id = wfc.wfca_id_hijo)
					LEFT JOIN sai_wfgrupo wfg ON (wfg.wfgr_id = wfc.wfgr_id)
				WHERE
					" . $where . "
			";
			
			if($result = $GLOBALS['SafiClassDb']->Query($query)){
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result))
				{
					$wfCadenaResult = self::LlenarWFCadena($row);
				}
			}
		}
		
		return $wfCadenaResult;
	}
	
	public static function GetWFCadenaHijo(EntidadWFCadena $wFCadena)
	{
		$wfCadenaResult = null;
		
		$where = '';
		
		if(($id=$wFCadena->GetId()) != null && ($id=trim($id)) != '')
		{
			$where = " wfc_actual.wfca_id = " . $id;
		}
		if(	($documento=$wFCadena->GetDocumento()) != null && ($idDocumento=$documento->GetId()) != null && 
			($idDocumento=trim($idDocumento)) != '' && strcmp($idDocumento, "0") != 0
		){
			$where .= ($where != '') ? ' AND' : '';
			$where .= " wfc_actual.docu_id = '" . $idDocumento ."'"; 
		}
		
		if(	(($wFObjeto=$wFCadena->GetWFObjetoInicial()) != null && ($idWFObjeto=$wFObjeto->GetId()) != null) &&
			($idWFObjeto = trim($idWFObjeto)) != ''
		){
			$where .= ($where != '') ? ' AND' : '';
			$where .= " wfc_actual.wfob_id_ini = " . $idWFObjeto;
		}
		
		if(	($wFOpcion=$wFCadena->GetWFOpcion()) != null && ($idWFOpcion=$wFOpcion->GetId()) != null &&
			($idWFOpcion=trim($idWFOpcion)) != ''
		){
			$where .= ($where != '') ? ' AND' : '';
			$where .= " wfc_actual.wfop_id = " . $idWFOpcion;
		}
		
		if( ($wFGrupo=$wFCadena->GetWFGrupo()) != null && ($idGrupo=$wFGrupo->GetId()) != null &&
			($idGrupo = trim($idGrupo)) != ''
		){
			$where .= ($where != '') ? ' AND' : '';
			$where .= " wfc_actual.wfgr_id = " . $idGrupo;
		}
		
		if($where != ''){
			$query = "
				SELECT
					wfc.wfca_id,
					wfc.wfob_id_ini,
					wfc.wfob_id_sig,
					wfc.wfop_id,
					wfc.docu_id,
					wfc.wfca_proyecto,
					wfc.wfca_tipo,
					wfc.depe_id,
					wfc_padre.wfca_id AS padre_wfca_id,
					wfc_hijo.wfca_id AS hijo_wfca_id,
					wfg.wfgr_id AS g_wfgr_id,
					wfg.wfob_id AS g_wfob_id,
					wfg.wfgr_descrip AS g_wfgr_descrip,
					wfg.wfgr_perf AS g_wfgr_perf
				FROM
					sai_wfcadena wfc_actual
					INNER JOIN sai_wfcadena wfc ON (wfc.wfca_id = wfc_actual.wfca_id_hijo)
					LEFT JOIN sai_wfcadena wfc_padre ON (wfc_padre.wfca_id = wfc.wfca_id_padre)
					LEFT JOIN sai_wfcadena wfc_hijo ON (wfc_hijo.wfca_id = wfc.wfca_id_hijo)
					LEFT JOIN sai_wfgrupo wfg ON (wfg.wfgr_id = wfc.wfgr_id)
				WHERE
					" . $where . "
			";
			
			if($result = $GLOBALS['SafiClassDb']->Query($query)){
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result))
				{
					$wfCadenaResult = self::LlenarWFCadena($row);
				}
			}
		}
		
		return $wfCadenaResult;
	}

	public static function GetWFCadenaByIdDocument($idDocument)
	{
		$wfCadenaResult = null;
		
		if($idDocument != null && $idDocument != '')
		{
			$query = "
				SELECT
					wfc.wfca_id,
					wfc.wfob_id_ini,
					wfc.wfob_id_sig,
					wfc.wfop_id,
					wfc.docu_id,
					wfc.wfca_proyecto,
					wfc.wfca_tipo,
					wfc.depe_id,
					wfc_padre.wfca_id AS padre_wfca_id,
					wfc_hijo.wfca_id AS hijo_wfca_id,
					wfg.wfgr_id AS g_wfgr_id,
					wfg.wfob_id AS g_wfob_id,
					wfg.wfgr_descrip AS g_wfgr_descrip,
					wfg.wfgr_perf AS g_wfgr_perf
				FROM
					sai_doc_genera dg
					LEFT JOIN sai_wfcadena wfc ON (wfc.wfca_id = dg.wfca_id)
					LEFT JOIN sai_wfcadena wfc_padre ON (wfc_padre.wfca_id = wfc.wfca_id_padre)
					LEFT JOIN sai_wfcadena wfc_hijo ON (wfc_hijo.wfca_id = wfc.wfca_id_hijo)
					LEFT JOIN sai_wfgrupo wfg ON (wfg.wfgr_id = wfc.wfgr_id)
				WHERE
					dg.docg_id = '".$idDocument."'
			";
		}
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$wfCadenaResult = self::LlenarWFCadena($row);
			}
		}
		
		return $wfCadenaResult;
	}
	
	public static function GetWFNextCadenaByIdDocument($idDocument)
	{
		$wfCadenaResult = null;
		
		if($idDocument != null && $idDocument != '')
		{
			$query = "
				SELECT
					wfc.wfca_id,
					wfc.wfob_id_ini,
					wfc.wfob_id_sig,
					wfc.wfop_id,
					wfc.docu_id,
					wfc.wfca_proyecto,
					wfc.wfca_tipo,
					wfc.depe_id,
					wfc_padre.wfca_id AS padre_wfca_id,
					wfc_hijo.wfca_id AS hijo_wfca_id,
					wfg.wfgr_id AS g_wfgr_id,
					wfg.wfob_id AS g_wfob_id,
					wfg.wfgr_descrip AS g_wfgr_descrip,
					wfg.wfgr_perf AS g_wfgr_perf
				FROM
					sai_doc_genera dg
					INNER JOIN sai_wfcadena wfc_actual ON (wfc_actual.wfca_id = dg.wfca_id)
					INNER JOIN sai_wfcadena wfc ON (wfc.wfca_id = wfc_actual.wfca_id_hijo)
					LEFT JOIN sai_wfcadena wfc_padre ON (wfc_padre.wfca_id = wfc.wfca_id_padre)
					LEFT JOIN sai_wfcadena wfc_hijo ON (wfc_hijo.wfca_id = wfc.wfca_id_hijo)
					LEFT JOIN sai_wfgrupo wfg ON (wfg.wfgr_id = wfc.wfgr_id)
				WHERE
					dg.docg_id = '".$idDocument."'
			";
		}
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$wfCadenaResult = self::LlenarWFCadena($row);
			}
		}
		
		return $wfCadenaResult;
	}
	
	private static function LlenarWFCadena($row)
	{
		$wfCadenaResult = new EntidadWFCadena();
						
		$wFObjetoInicial = new EntidadWFObjeto();
		$wFObjetoInicial->SetId($row['wfob_id_ini']);
		
		$wFObjetoSiguiente = new EntidadWFObjeto();
		$wFObjetoSiguiente->SetId($row['wfob_id_sig']);
		
		$wFOpcion = new EntidadWFOpcion();
		$wFOpcion->SetId($row['wfop_id']);
		
		if($row['g_wfgr_id'] != null){
			$wFGrupo = new EntidadWFGrupo();
			
			$gWFObjeto = new EntidadWFObjeto();
			$gWFObjeto->Setid($row['g_wfob_id']);
			
			$perfiles = array();
			$idPerfiles = explode('/', $row['g_wfgr_perf']);
			foreach($idPerfiles as $idPerfil){
				$gCargo = new EntidadCargo();
				$gCargo->SetId($idPerfil);
				
				$wFGrupo->SetPerfil($gCargo);
			}
			
			$wFGrupo->SetId($row['g_wfgr_id']);
			$wFGrupo->SetWFObjeto($gWFObjeto);
			$wFGrupo->SetDescripcion($row['g_wfgr_descrip']);
			
		} else {
			$wFGrupo = null;
		}
		
		if($row['padre_wfca_id'] != null){
			$wFCadenaPadre = new EntidadWFCadena();
			$wFCadenaPadre->SetId($row['padre_wfca_id']);
		} else {
			$wFCadenaPadre = null;
		}
		
		if($row['hijo_wfca_id'] != null){
			$wFCadenaHijo = new EntidadWFCadena();
			$wFCadenaHijo->SetId($row['hijo_wfca_id']);
		} else {
			$wFCadenaHijo = null;
		}
		
		$documento = new EntidadDocumento();
		$documento->SetId($row['docu_id']);
		
		if($row['depe_id'] != null && $row['depe_id'] != ''){
			$dependencia = new EntidadDependencia();
			$dependencia->SetId($row['depe_id']);
		} else {
			$dependencia = null;
		}
		
		$wfCadenaResult->SetId($row['wfca_id']);
		$wfCadenaResult->SetWFObjetoInicial($wFObjetoInicial);
		$wfCadenaResult->SetWFObjetoSiguiente($wFObjetoSiguiente);
		$wfCadenaResult->SetWFOpcion($wFOpcion);
		$wfCadenaResult->SetWFGrupo($wFGrupo);
		$wfCadenaResult->SetWFCadenaPadre($wFCadenaPadre);
		$wfCadenaResult->SetWFCadenaHijo($wFCadenaHijo);
		$wfCadenaResult->SetDocumento($documento);
		$wfCadenaResult->SetProyecto($row['wfca_proyecto']);
		$wfCadenaResult->SetTipo($row['wfca_tipo']);
		$wfCadenaResult->SetDependencia($dependencia);
		
		return $wfCadenaResult;
	}
}