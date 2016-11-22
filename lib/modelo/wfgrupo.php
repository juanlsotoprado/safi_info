<?php
include_once(SAFI_ENTIDADES_PATH . '/wfgrupo.php');

abstract class SafiModeloWFGrupo
{
	
	public static function sai_buscar_grupos_perfil($idPerfil)
	{
		$wFGrupo = null;
		
     $perfil = trim($idPerfil);
	
    if ($perfil == '' ){
         
    	return false;}
    	
    	
     $perfil_general = substr($perfil,1,2);
     
     
     $query  =" SELECT wfgr_id FROM sai_wfgrupo WHERE  wfgr_perf LIKE '%". $perfil."%' or wfgr_perf LIKE '%".$perfil_general."%' ";
     
    
   
     if($result = $GLOBALS['SafiClassDb']->Query($query)){
     
     	     $i=0;
     	 while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
              
     	 $reporte[$i]['grupos_id'] = $row['wfgr_id'];		
             
    
        $i++;
     }
      echo $query;
      return $reporte;

     }
		
     

		/* $query = "
			SELECT
				wfg.wfgr_id,
				wfg.wfob_id,
				wfg.wfgr_descrip,
				wfg.wfgr_perf
			FROM
				sai_wfgrupo wfg
			WHERE
				wfg.wfgr_perf like '%".$idPerfil."%'
		";
		
		
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
		
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$wFGrupo = new EntidadWFGrupo();

				$wFGrupo->SetId($row['wfgr_id']);
				$wFGrupo->SetWFObjeto($row['wfob_id']);
				$wFGrupo->SetDescripcion($row['wfgr_descrip']);
				$wFGrupo->SetPerfiles(self::DecodePerfiles($row['wfgr_perf']));
			}
		}
		
		return $wFGrupo;
		
		*/
	}
	
    public static function GetIdPerfilWFGrupoBy(array $params = null)
	{
		
	  $data = array();
		
		$query = "
			SELECT
				wfg.wfgr_perf,
				wfg.wfgr_id
				
			FROM
				sai_wfgrupo wfg
			WHERE
				wfg.wfgr_id IN ('".implode("', '", $params)."')";
		
		
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
		
			while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				
			   $data[$row['wfgr_id']] = $row['wfgr_perf'];
				
			}
			
			 return $data;
		}else{
		
		 return false;
		}
	
	}
	

	
	public static function GetWFGrupoByIdPerfil($idPerfil)
	{
		$wFGrupo = null;
		
		$query = "
			SELECT
				wfg.wfgr_id,
				wfg.wfob_id,
				wfg.wfgr_descrip,
				wfg.wfgr_perf
			FROM
				sai_wfgrupo wfg
			WHERE
				wfg.wfgr_perf like '%".$idPerfil."%'
		";
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
		
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$wFGrupo = new EntidadWFGrupo();

				$wFGrupo->SetId($row['wfgr_id']);
				$wFGrupo->SetWFObjeto($row['wfob_id']);
				$wFGrupo->SetDescripcion($row['wfgr_descrip']);
				$wFGrupo->SetPerfiles(self::DecodePerfiles($row['wfgr_perf']));
			}
		}
		
		return $wFGrupo;
		
	}
	
	public static function GetWFGrupoByIdPerfilResSocial($grupo)
	{
		$wFGrupo = null;
	
		$query = "
			SELECT
				wfg.wfgr_id,
				wfg.wfob_id,
				wfg.wfgr_descrip,
				wfg.wfgr_perf
			FROM
				sai_wfgrupo wfg
			WHERE
				wfg.wfgr_id = $grupo
		";
	
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
	
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$wFGrupo = new EntidadWFGrupo();
	
				$wFGrupo->SetId($row['wfgr_id']);
				$wFGrupo->SetWFObjeto($row['wfob_id']);
				$wFGrupo->SetDescripcion($row['wfgr_descrip']);
				$wFGrupo->SetPerfiles(self::DecodePerfiles($row['wfgr_perf']));
			}
		}
	
		return $wFGrupo;
	
	}
	
	public static function GetWFPerfilbyGrupo($idPerfil)
	{
		$wFGrupo = null;
	
		$query = "
			SELECT
				wfgr_perf
			FROM
				sai_wfgrupo 
			WHERE
				wfgr_id =$idPerfil
		";
		


	
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
	
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				
			return	$row["wfgr_perf"];
			}
		}
	
		return false;
	
	}
	
	
	
	public static function GetWFGrupoByIdsPerfil(array $idsPerfil = null)
	{
		
		
		$wFGrupo = null;
		
		$query = "
			SELECT
				wfg.wfgr_id,
				wfg.wfob_id,
				wfg.wfgr_descrip,
				wfg.wfgr_perf
			FROM
				sai_wfgrupo wfg
			WHERE
				wfg.wfgr_perf IN ('".implode("', '",$idsPerfil)."')";
		
 // echo $query;

		if($result = $GLOBALS['SafiClassDb']->Query($query)){
		
			while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				
				$wFGrupo = new EntidadWFGrupo();
				$wFGrupo->SetId($row['wfgr_id']);
				$wFGrupo->SetWFObjeto($row['wfob_id']);
				$wFGrupo->SetDescripcion($row['wfgr_descrip']);
				$wFGrupo->SetPerfiles(self::DecodePerfiles($row['wfgr_perf']));
				
				$params[$row['wfgr_perf']] = $wFGrupo;
				
			}
		}
		
		return $params;
	}
	
	
	
	
	public static function DecodePerfiles($strPerfiles)
	{
		$perfiles = array();

		$idPerfiles = explode('/', $strPerfiles);
		foreach($idPerfiles as $idPerfil){
			$gCargo = new EntidadCargo();
			$gCargo->SetId($idPerfil);
			
			$perfiles[] = $gCargo;
		}
		
		return $perfiles;
	}
}