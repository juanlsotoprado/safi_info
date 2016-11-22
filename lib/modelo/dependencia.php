<?php
include_once(SAFI_ENTIDADES_PATH . "/dependencia.php");

class SafiModeloDependencia
{
	public static function GetDependenciaById($idDependencia){
		
		$dependencia = null;
		
		$query = "
			SELECT
				depe_id,
				depe_nombre,
				depe_nombrecort,
				depe_id_sup,
				depe_nivel,
				depe_cosige,
				usua_login,
				depe_observa,
				esta_id
			FROM
				sai_dependenci
			WHERE
				depe_id = '".$idDependencia."'
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) !== false) {
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$dependencia = self::LlenarDependencia($row);
			}
		}
		
		return $dependencia;
	}
	
public static function GetDependenciasByNivel($nivel){
	
	   $params = array();
		
		$dependencia = null;
		
		$query = "
			SELECT
				depe_id,
				depe_nombre,
				depe_nombrecort,
				depe_id_sup,
				depe_nivel,
				depe_cosige,
				usua_login,
				depe_observa,
				esta_id
			FROM
				sai_dependenci
			WHERE
				depe_nivel = '".$nivel."'
		";
		
		
		
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) !== false) {
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$dependencia = self::LlenarDependencia($row);
				
				$params[] = $dependencia;
			}
		}
		
		
		return $params;
	}
	
	
	
	
public static function GetDependenciasByNivels($niveles){
	
	   $params = array();
		
		$dependencia = null;
		
		$query = "
			SELECT
				depe_id,
				depe_nombre,
				depe_nombrecort,
				depe_id_sup,
				depe_nivel,
				depe_cosige,
				usua_login,
				depe_observa,
				esta_id
			FROM
				sai_dependenci
			WHERE
				depe_nivel IN ('".implode("', '", $niveles)."')
				
		ORDER BY depe_nombre";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) !== false) {
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$dependencia = self::LlenarDependencia($row);
				
				$params[$row['depe_id']] = $dependencia;
			}
		}
		
		
		return $params;
	}
	
	
	public static function GetDependenciaByIds($idDependencias)
	{
		if(is_array($idDependencias) && count($idDependencias)>0){
			$query = "
				SELECT
					depe_id,
					depe_nombre,
					depe_nombrecort,
					depe_id_sup,
					depe_nivel,
					depe_cosige,
					usua_login,
					depe_observa,
					esta_id
				FROM
					sai_dependenci
				WHERE
					depe_id IN (".implode(", ", $idDependencias).")
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) !== false) {
				$dependencias = array();
				while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$dependencia = self::LlenarDependencia($row);
					$dependencias[$dependencia->GetId()] = $dependencia;
				}
			}
		}	
		
		return $dependencias;
	}
	
	private static function LlenarDependencia($row)
	{
		$dependecia = new EntidadDependencia();
		
		$dependecia->SetId($row['depe_id']);
		$dependecia->SetNombre($row['depe_nombre']);
		$dependecia->SetNombreCorto($row['depe_nombrecort']);
		$dependecia->SetIdDependenciaPadre($row['depe_id_sup']);
		$dependecia->SetNivel($row['depe_nivel']);
		$dependecia->SetCodigoSigecof($row['depe_cosige']);
		$dependecia->SetLoginUsuario($row['usua_login']);
		$dependecia->SetObservaciones($row['depe_observa']);
		$dependecia->SetIdEstatus($row['esta_id']);
		
		return $dependecia;
	}
}