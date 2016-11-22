<?php
require_once(SAFI_ENTIDADES_PATH . '/infocentro.php');

class SafiModeloInfocentro
{
	

   public static function GetAllInfocentrosIds(array $params = null)
	{
		$infocentros = array();
		
		$query = "
			SELECT
				*
			
			FROM
				safi_infocentro
				
			WHERE 
			
			     id IN ('".implode("', '", $params)."') 
		";

		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$infocentros[$row['id']] = self::LlenarInfocentro($row);
		}
		return $infocentros;
	}
	
	
	public static function GetAllInfocentros()
	{
		$infocentros = array();
		
		$query = "
			SELECT
				id,
				nombre,
				direccion,
				anho,
				parroquia_id,
				estatus_actividad,
				estatus_id,
				nemotecnico,
				etapa
			FROM
				safi_infocentro
		";
		
		

		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$infocentros[$row['id']] = $row;
		}
		
		return $infocentros;
	}
	
   public static function GetAllInfocentros2($nombre)
	{
		$infocentros = array();
		
		$query = "
			SELECT
				*
			
			FROM
				safi_infocentro
				
			WHERE 
			    nombre  LIKE '%".$nombre."%' 
		";

		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$infocentros[$row['id']] = self::LlenarInfocentro($row);
		}
		return $infocentros;
	}
	
	
	
	public static function Search($key, $numItems, $selecteds = array())
	{
		try
		{
			$preMsg = "Error al buscar los infocentros (Search).";
			$infocentros = array();
			
			$where = '';
			if($selecteds != null && is_array($selecteds) && count($selecteds)>0){
				$where = "
					AND
					i.id NOT IN (".implode(',', $selecteds).")
				";
			}
			
			$query = "
				SELECT
					i.id,
					i.nombre,
					i.direccion,
					i.anho,
					i.estatus_actividad,
					i.estatus_id,
					i.nemotecnico,
					i.etapa,
					p.id AS parroquia_id,
					p.nombre AS parroquia_nombre,
					p.estatus_actividad AS parroquia_estatus_actividad,
					m.id AS municipio_id,
					m.nombre AS municipio_nombre,
					m.estatus_actividad AS municipio_estatus_actividad,
					e.id AS estado_id,
	  				e.nombre AS estado_nombre,
	  				e.estatus_actividad AS estado_estatus_actividad
				FROM
					safi_infocentro i
					LEFT JOIN safi_parroquia p ON (p.id = i.parroquia_id)
					LEFT JOIN safi_municipio m ON (m.id = p.municipio_id)
					LEFT JOIN safi_edos_venezuela e ON (e.id = m.edo_id)
				WHERE
					i.estatus_actividad = '1' AND
					lower(i.nombre) LIKE '%" . utf8_decode(mb_strtolower($GLOBALS['SafiClassDb']->Quote($key), 'UTF-8')) . "%'
					" . $where . "
				LIMIT
					" . $numItems . "
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception(utf8_decode($preMsg." Detalles: ".$GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$infocentro = new EntidadInfocentro();
				
				$infocentro = new EntidadInfocentro();
				
				$infocentro->SetId($row['id']);
				$infocentro->SetNombre($row['nombre']);
				$infocentro->SetDireccion($row['direccion']);
				$infocentro->SetAnho($row['anho']);
				$infocentro->SetIdParroquia($row['parroquia_id']);
				$infocentro->SetEstatusActividad($row['estatus_actividad']);
				$infocentro->SetIdEstatus($row['estatus_id']);
				$infocentro->SetNemotecnico($row['nemotecnico']);
				$infocentro->SetEtapa($row['etapa']);
				
				if($row['parroquia_id'] != null){
					$parroquia = new EntidadParroquia();
					
					$parroquia->SetId($row['parroquia_id']);
					$parroquia->SetNombre($row['parroquia_nombre']);
					$parroquia->SetIdMunicipio($row['municipio_id']);
					$parroquia->SetEstatusActividad($row['parroquia_estatus_actividad']);
					
					$infocentro->SetParroquia($parroquia);
					
					if($row['municipio_id'] != null){
						$municipio = new EntidadMunicipio();
						
						$municipio->SetId($row['municipio_id']);
						$municipio->SetNombre($row['municipio_nombre']);
						$municipio->SetIdEstado($row['estado_id']);
						$municipio->SetEstatusActividad($row['municipio_estatus_actividad']);
	
						$parroquia->SetMunicipio($municipio);
						
						if($row['estado_id'] != null){
							$estado = new EntidadEstado();
							
							$estado->SetId($row['estado_id']);
							$estado->SetNombre($row['estado_nombre']);
							$estado->SetEstatusActividad($row['estado_estatus_actividad']);
							
							$municipio->SetEstado($estado);
						}
					}
				}
				
				$infocentros[(int)$row['id']] = $infocentro; 
			}
			
			return $infocentros;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	public static function GetInfocentrosByIdViaticoNacional($idViaticoNacional){
		$infocentros = array();
			
		$query = "
			SELECT
				i.id,
				i.nombre,
				i.direccion,
				i.anho,
				i.parroquia_id,
				i.estatus_actividad,
				i.estatus_id,
				i.nemotecnico,
				i.etapa,
				p.id AS parroquia_id,
				p.nombre AS parroquia_nombre,
				p.estatus_actividad AS parroquia_estatus_actividad,
				m.id AS municipio_id,
				m.nombre AS municipio_nombre,
				m.estatus_actividad AS municipio_estatus_actividad,
				e.id AS estado_id,
  				e.nombre AS estado_nombre,
  				e.estatus_actividad AS estado_estatus_actividad
			FROM
				safi_viatico_infocentro vi
				INNER JOIN safi_infocentro i ON vi.infocentro_id = i.id
				LEFT JOIN safi_parroquia p ON (p.id = i.parroquia_id)
				LEFT JOIN safi_municipio m ON (m.id = p.municipio_id)
				LEFT JOIN safi_edos_venezuela e ON (e.id = m.edo_id)
			WHERE
				vi.viatico_id = '".$idViaticoNacional."' 
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
	
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
		{
			$infocentro = new EntidadInfocentro();
			
			$infocentro->SetId($row['id']);
			$infocentro->SetNombre($row['nombre']);
			$infocentro->SetDireccion($row['direccion']);
			$infocentro->SetAnho($row['anho']);
			$infocentro->SetIdParroquia($row['parroquia_id']);
			$infocentro->SetEstatusActividad($row['estatus_actividad']);
			$infocentro->SetIdEstatus($row['estatus_id']);
			$infocentro->SetNemotecnico($row['nemotecnico']);
			$infocentro->SetEtapa($row['etapa']);
			
			if($row['parroquia_id'] != null){
				$parroquia = new EntidadParroquia();
				
				$parroquia->SetId($row['parroquia_id']);
				$parroquia->SetNombre($row['parroquia_nombre']);
				$parroquia->SetIdMunicipio($row['municipio_id']);
				$parroquia->SetEstatusActividad($row['parroquia_estatus_actividad']);
				
				$infocentro->SetParroquia($parroquia);
				
				if($row['municipio_id'] != null){
					$municipio = new EntidadMunicipio();
					
					$municipio->SetId($row['municipio_id']);
					$municipio->SetNombre($row['municipio_nombre']);
					$municipio->SetIdEstado($row['estado_id']);
					$municipio->SetEstatusActividad($row['municipio_estatus_actividad']);

					$parroquia->SetMunicipio($municipio);
					
					if($row['estado_id'] != null){
						$estado = new EntidadEstado();
						
						$estado->SetId($row['estado_id']);
						$estado->SetNombre($row['estado_nombre']);
						$estado->SetEstatusActividad($row['estado_estatus_actividad']);
						
						$municipio->SetEstado($estado);
					}
				}
			}
			
			$infocentros[$row['id']] = $infocentro; 
		}
	
		return $infocentros;

	}
	
	public static function GetInfocentrosByIdAvance($idAvance)
	{
		$infocentros = null;
		
		try {
			
			if($idAvance == null || trim($idAvance) == '')
				throw new Exception("El parametro idAvance es nulo o esta vacio");
			
			$query = "
				SELECT
					infocentro.id,
					infocentro.nombre,
					infocentro.direccion,
					infocentro.anho,
					infocentro.parroquia_id,
					infocentro.estatus_actividad,
					infocentro.estatus_id,
					infocentro.nemotecnico,
					infocentro.etapa,
					parroquia.id AS parroquia_id,
					parroquia.nombre AS parroquia_nombre,
					parroquia.estatus_actividad AS parroquia_estatus_actividad,
					municipio.id AS municipio_id,
					municipio.nombre AS municipio_nombre,
					municipio.estatus_actividad AS municipio_estatus_actividad,
					edo.id AS estado_id,
	  				edo.nombre AS estado_nombre,
	  				edo.estatus_actividad AS estado_estatus_actividad
				FROM
					safi_avance_infocentro avance_infocentro
					INNER JOIN safi_infocentro infocentro ON avance_infocentro.infocentro_id = infocentro.id
					LEFT JOIN safi_parroquia parroquia ON (parroquia.id = infocentro.parroquia_id)
					LEFT JOIN safi_municipio municipio ON (municipio.id = parroquia.municipio_id)
					LEFT JOIN safi_edos_venezuela edo ON (edo.id = municipio.edo_id)
				WHERE
					avance_infocentro.avance_id = '".$idAvance."' 
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al obtener los infocentros de un avance. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$infocentros = array();		
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$infocentro = self::LlenarInfocentro($row);
				$infocentros[$infocentro->GetId()] = $infocentro;
			}
			
			return $infocentros;
			
		} catch (Exception $e) {
			error_log($e, 0);
			return null;
		}
	}
	
	private function LlenarInfocentro($row)
	{
		$infocentro = new EntidadInfocentro();
		
		$infocentro->SetId($row['id']);
		$infocentro->SetNombre($row['nombre']);
		$infocentro->SetDireccion($row['direccion']);
		$infocentro->SetAnho($row['anho']);
		$infocentro->SetIdParroquia($row['parroquia_id']);
		$infocentro->SetEstatusActividad($row['estatus_actividad']);
		$infocentro->SetIdEstatus($row['estatus_id']);
		$infocentro->SetNemotecnico($row['nemotecnico']);
		$infocentro->SetEtapa($row['etapa']);
		
		if($row['parroquia_id'] != null){
			$parroquia = new EntidadParroquia();
			
			$parroquia->SetId($row['parroquia_id']);
			$parroquia->SetNombre($row['parroquia_nombre']);
			$parroquia->SetIdMunicipio($row['municipio_id']);
			$parroquia->SetEstatusActividad($row['parroquia_estatus_actividad']);
			
			$infocentro->SetParroquia($parroquia);
			
			if($row['municipio_id'] != null){
				$municipio = new EntidadMunicipio();
				
				$municipio->SetId($row['municipio_id']);
				$municipio->SetNombre($row['municipio_nombre']);
				$municipio->SetIdEstado($row['estado_id']);
				$municipio->SetEstatusActividad($row['municipio_estatus_actividad']);

				$parroquia->SetMunicipio($municipio);
				
				if($row['estado_id'] != null){
					$estado = new EntidadEstado();
					
					$estado->SetId($row['estado_id']);
					$estado->SetNombre($row['estado_nombre']);
					$estado->SetEstatusActividad($row['estado_estatus_actividad']);
					
					$municipio->SetEstado($estado);
				}
			}
		}
		
		return $infocentro;
	}
	
	public static function GetInfocentrosByIds($ids){
		$infocentros = array();
		$idsValidos = array();
		if(is_array($ids)){
			foreach($ids as $id){
				if(SafiIsId($id)){
					$idsValidos[] = $id;
				}
			}
		}
		
		if(count($idsValidos) > 0){
			$query = "
				SELECT
					i.id,
					i.nombre,
					i.direccion,
					i.anho,
					i.parroquia_id,
					i.estatus_actividad,
					i.estatus_id,
					i.nemotecnico,
					i.etapa,
					p.id AS parroquia_id,
					p.nombre AS parroquia_nombre,
					p.estatus_actividad AS parroquia_estatus_actividad,
					m.id AS municipio_id,
					m.nombre AS municipio_nombre,
					m.estatus_actividad AS municipio_estatus_actividad,
					e.id AS estado_id,
	  				e.nombre AS estado_nombre,
	  				e.estatus_actividad AS estado_estatus_actividad
				FROM
					safi_infocentro i
					LEFT JOIN safi_parroquia p ON (p.id = i.parroquia_id)
					LEFT JOIN safi_municipio m ON (m.id = p.municipio_id)
					LEFT JOIN safi_edos_venezuela e ON (e.id = m.edo_id)
				WHERE
					i.id IN (".implode(',', $idsValidos).")
			";
			
			$result = $GLOBALS['SafiClassDb']->Query($query);
		
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$infocentro = new EntidadInfocentro();
				
				$infocentro->SetId($row['id']);
				$infocentro->SetNombre($row['nombre']);
				$infocentro->SetDireccion($row['direccion']);
				$infocentro->SetAnho($row['anho']);
				$infocentro->SetIdParroquia($row['parroquia_id']);
				$infocentro->SetEstatusActividad($row['estatus_actividad']);
				$infocentro->SetIdEstatus($row['estatus_id']);
				$infocentro->SetNemotecnico($row['nemotecnico']);
				$infocentro->SetEtapa($row['etapa']);
				
				if($row['parroquia_id'] != null){
					$parroquia = new EntidadParroquia();
					
					$parroquia->SetId($row['parroquia_id']);
					$parroquia->SetNombre($row['parroquia_nombre']);
					$parroquia->SetIdMunicipio($row['municipio_id']);
					$parroquia->SetEstatusActividad($row['parroquia_estatus_actividad']);
					
					$infocentro->SetParroquia($parroquia);
					
					if($row['municipio_id'] != null){
						$municipio = new EntidadMunicipio();
						
						$municipio->SetId($row['municipio_id']);
						$municipio->SetNombre($row['municipio_nombre']);
						$municipio->SetIdEstado($row['estado_id']);
						$municipio->SetEstatusActividad($row['municipio_estatus_actividad']);
	
						$parroquia->SetMunicipio($municipio);
						
						if($row['estado_id'] != null){
							$estado = new EntidadEstado();
							
							$estado->SetId($row['estado_id']);
							$estado->SetNombre($row['estado_nombre']);
							$estado->SetEstatusActividad($row['estado_estatus_actividad']);
							
							$municipio->SetEstado($estado);
						}
					}
				}
				
				$infocentros[$row['id']] = $infocentro; 
			}
			
		}
		return $infocentros;
	}
}