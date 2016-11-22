<?php
include_once(SAFI_ENTIDADES_PATH . '/ruta.php');

class SafiModeloRutaViatico {
	public static function GetRutasByIdsViaticoNacionales(array $idsViaticosNacionales){
		return self::__GetRutasByIdsViaticoNacionales($idsViaticosNacionales);
	}
	
	public static function GetRutasByIdViaticoNacional($idViaticoNacional){
		$rutasByViaticos = self::__GetRutasByIdsViaticoNacionales(array($idViaticoNacional));
		
		if(count($rutasByViaticos)>0) return  current($rutasByViaticos);
		else return array(); 
	}
	
	private static function __GetRutasByIdsViaticoNacionales(array $idsViaticosNacionales)
	{
		$rutasByViaticos = array();
		
		$query = "
			SELECT
				srv.id,
				srv.viatico_id,
				to_char(srv.fecha_inicio, 'DD/MM/YYYY') AS fecha_inicio,
				to_char(srv.fecha_fin, 'DD/MM/YYYY') AS fecha_fin,
				srv.dias_alimentacion,
				srv.dias_hospedaje,
				srv.dias_transporte_interurbano,
				srv.tipo_transporte,
				srv.pasaje_ida_vuelta,
				srv.transporte_aeropuerto_residencia,
				srv.transporte_residencia_aeropuerto,
				srv.tasa_aeroportuaria_ida,
				srv.tasa_aeroportuaria_vuelta,
				srv.origen_parroquia_id,
				srv.origen_municipio_id,
				srv.origen_ciudad_id,
				srv.origen_edo_id,
				srv.origen_direccion,
				srv.destino_parroquia_id,
				srv.destino_municipio_id,
				srv.destino_ciudad_id,
				srv.destino_edo_id,				
				srv.destino_direccion,
				srv.observaciones,
				stt.nombre AS nombre_transporte,
				spo.nombre AS origen_parroquia_nombre,
				spd.nombre AS destino_parroquia_nombre,
				smo.nombre AS origen_municipio_nombre,
				smd.nombre AS destino_municipio_nombre,
				sco.nombre AS origen_ciudad_nombre,
				scd.nombre AS destino_ciudad_nombre,
				sevo.nombre AS origen_edo_nombre,
				sevd.nombre AS destino_edo_nombre 
			FROM
				safi_tipo_transporte stt,
				safi_ruta_viatico srv
				LEFT OUTER JOIN safi_parroquia spo ON (srv.origen_parroquia_id = spo.id)
				LEFT OUTER JOIN safi_parroquia spd ON (srv.destino_parroquia_id = spd.id)
				LEFT OUTER JOIN safi_municipio smo ON (srv.origen_municipio_id = smo.id)
				LEFT OUTER JOIN safi_municipio smd ON (srv.destino_municipio_id = smd.id)
				LEFT OUTER JOIN safi_ciudad sco ON (srv.origen_ciudad_id = sco.id)
				LEFT OUTER JOIN safi_ciudad scd ON (srv.destino_ciudad_id = scd.id)
				LEFT OUTER JOIN safi_edos_venezuela sevo ON (srv.origen_edo_id = sevo.id)
				LEFT OUTER JOIN safi_edos_venezuela sevd ON (srv.destino_edo_id = sevd.id) 
			WHERE
				srv.viatico_id IN ('".implode("' ,'", $idsViaticosNacionales)."') AND
				srv.tipo_transporte = stt.id
			ORDER BY
				srv.viatico_id,
				srv.id
		";
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
		{
			if(!isset($rutasByViaticos[$row['viatico_id']])){
				$rutasByViaticos[$row['viatico_id']] = array();
			}
			$rutas = &$rutasByViaticos[$row['viatico_id']];
			
			$ruta = new EntidadRuta();
			
			$pasajeIdaVuelta = (strcasecmp($row['pasaje_ida_vuelta'], 't') == 0 || 
				strcasecmp($row['pasaje_ida_vuelta'], 'true') == 0) ? true : false;
			$aeropuertoResidencia = (strcasecmp($row['transporte_aeropuerto_residencia'], 't') == 0 || 
				strcasecmp($row['transporte_aeropuerto_residencia'], 'true') == 0) ? true : false;
			$residenciaAeropuerto = (strcasecmp($row['transporte_residencia_aeropuerto'], 't') == 0 || 
				strcasecmp($row['transporte_residencia_aeropuerto'], 'true') == 0) ? true : false;
			$tasaAeroportuariaIda = (strcasecmp($row['tasa_aeroportuaria_ida'], 't') == 0 ||
				strcasecmp($row['tasa_aeroportuaria_ida'], 'true') == 0) ? true : false;
			$tasaAeroportuariaVuelta = (strcasecmp($row['tasa_aeroportuaria_vuelta'], 't') == 0 ||
				strcasecmp($row['tasa_aeroportuaria_vuelta'], 'true') == 0) ? true : false;
				
			$ruta->SetId($row['id']);
			$ruta->SetIdViatico($row['viatico_id']);
			$ruta->SetFechaInicio($row['fecha_inicio']);
			$ruta->SetFechaFin($row['fecha_fin']);
			$ruta->SetDiasAlimentacion($row['dias_alimentacion']);
			$ruta->SetDiasHospedaje($row['dias_hospedaje']);
			$ruta->SetUnidadTransporteInterurbano($row['dias_transporte_interurbano']);
			$ruta->SetIdTipoTransporte($row['tipo_transporte']);
			$ruta->SetPasajeIdaVuelta($pasajeIdaVuelta);
			$ruta->SetAeropuertoResidencia($aeropuertoResidencia);
			$ruta->SetResidenciaAeropuerto($residenciaAeropuerto);
			$ruta->SetTasaAeroportuariaIda($tasaAeroportuariaIda);
			$ruta->SetTasaAeroportuariaVuelta($tasaAeroportuariaVuelta);
			$ruta->SetIdFromParroquia($row['origen_parroquia_id']);
			$ruta->SetIdFromMunicipio($row['origen_municipio_id']);
			$ruta->SetIdFromCiudad($row['origen_ciudad_id']);
			$ruta->SetIdFromEstado($row['origen_edo_id']);
			$ruta->SetFromDireccion($row['origen_direccion']);
			$ruta->SetIdToParroquia($row['destino_parroquia_id']);
			$ruta->SetIdToMunicipio($row['destino_municipio_id']);
			$ruta->SetIdToCiudad($row['destino_ciudad_id']);
			$ruta->SetIdToEstado($row['destino_edo_id']);
			$ruta->SetToDireccion($row['destino_direccion']);
			$ruta->SetNombreTransporte($row['nombre_transporte']);
			$ruta->SetNombreFromParroquia($row['origen_parroquia_nombre']);
			$ruta->SetNombreFromMunicipio($row['origen_municipio_nombre']);
			$ruta->SetNombreFromCiudad($row['origen_ciudad_nombre']);
			$ruta->SetNombreFromEstado($row['origen_edo_nombre']);
			$ruta->SetNombreToParroquia($row['destino_parroquia_nombre']);
			$ruta->SetNombreToMunicipio($row['destino_municipio_nombre']);
			$ruta->SetNombreToCiudad($row['destino_ciudad_nombre']);
			$ruta->SetNombreToEstado($row['destino_edo_nombre']);
			$ruta->SetObservaciones($row['observaciones']);
			
			$rutas[] = $ruta;
		}
		
		return $rutasByViaticos;
	}
}