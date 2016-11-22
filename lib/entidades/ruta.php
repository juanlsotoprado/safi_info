<?php
class EntidadRuta
{
	private $_id = 0;
	private $_idViatico = '';
	private $_fechaInicio;
	private $_fechaFin;
	private $_diasAlimentacion = 0;
	private $_diasHospedaje = 0;
	private $_unidadTransporteInterurbano = 0;
	private $_idTipoTransporte = 0;
	private $_pasajeIdaVuelta = false;
	private $_aeropuertoResidencia = false;
	private $_residenciaAeropuerto = false;
	private $_tasaAeroportuariaIda = false;
	private $_tasaAeroportuariaVuelta = false;
	private $_idFromEstado = 0;
	private $_idFromCiudad = 0;
	private $_idFromMunicipio = 0;
	private $_idFromParroquia = 0;
	private $_fromDireccion;
	private $_idToEstado = 0;
	private $_idToCiudad = 0;
	private $_idToMunicipio = 0;
	private $_idToParroquia = 0;
	private $_toDireccion;
	private $_nombreTransporte='';
	private $_nombreFromParroquia = '';
	private $_nombreFromMunicipio;
	private $_nombreFromCiudad='';
	private $_nombreFromEstado='';
	private $_nombreToParroquia = '';
	private $_nombreToMunicipio;
	private $_nombreToCiudad='';
	private $_nombreToEstado='';
	private $_observaciones;
	
	public function __construct()
	{
		
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetIdViatico(){
		return $this->_idViatico;
	}
	public function SetIdViatico($idViatico){
		$this->_idViatico = $idViatico;
	}
	public function GetFechaInicio()
	{
		return $this->_fechaInicio;
	}
	public function SetFechaInicio($fechaInicio)
	{
		$this->_fechaInicio = $fechaInicio;
	}
	public function GetFechaFin()
	{
		return $this->_fechaFin;
	}
	public function SetFechaFin($fechaFin)
	{
		$this->_fechaFin = $fechaFin;
	}
	public function GetDiasAlimentacion()
	{
		return $this->_diasAlimentacion;
	}
	public function SetDiasAlimentacion($diasAlimentacion)
	{
		$this->_diasAlimentacion = (int)$diasAlimentacion;
	}
	public function GetDiasHospedaje()
	{
		return $this->_diasHospedaje;
	}
	public function SetDiasHospedaje($diasHospedaje)
	{
		$this->_diasHospedaje = (int)$diasHospedaje;
	}
	public function GetUnidadTransporteInterurbano()
	{
		return $this->_unidadTransporteInterurbano;
	}
	public function SetUnidadTransporteInterurbano($unidadTransporteInterurbano)
	{
		$this->_unidadTransporteInterurbano = (int)$unidadTransporteInterurbano;
	}
	public function GetIdTipoTransporte()
	{
		return $this->_idTipoTransporte;
	}
	public function SetIdTipoTransporte($idTipoTransporte)
	{
		$this->_idTipoTransporte = (int)$idTipoTransporte;
	}
	public function GetPasajeIdaVuelta()
	{
		return $this->_pasajeIdaVuelta;
	}
	public function SetPasajeIdaVuelta($pasajeIdaVuelta)
	{
		$this->_pasajeIdaVuelta = $pasajeIdaVuelta;
	}
	public function GetAeropuertoResidencia()
	{
		return $this->_aeropuertoResidencia;
	}
	public function SetAeropuertoResidencia($aeropuertoResidencia)
	{
		$this->_aeropuertoResidencia = $aeropuertoResidencia;
	}
	public function GetResidenciaAeropuerto()
	{
		return $this->_residenciaAeropuerto;
	}
	public function SetResidenciaAeropuerto($residenciaAeropuerto)
	{
		$this->_residenciaAeropuerto = $residenciaAeropuerto;
	}
	public function GetTasaAeroportuariaIda()
	{
		return $this->_tasaAeroportuariaIda;
	}
	public function SetTasaAeroportuariaIda($tasaAeroportuariaIda)
	{
		$this->_tasaAeroportuariaIda = $tasaAeroportuariaIda;
	}
	public function GetTasaAeroportuariaVuelta()
	{
		return $this->_tasaAeroportuariaVuelta;
	}
	public function SetTasaAeroportuariaVuelta($tasaAeroportuariaVuelta)
	{
		$this->_tasaAeroportuariaVuelta = $tasaAeroportuariaVuelta;
	}
	public function GetIdFromEstado()
	{
		return $this->_idFromEstado;
	}
	public function SetIdFromEstado($idFromEstado)
	{
		$this->_idFromEstado = (int)$idFromEstado;
	}
	public function GetIdFromCiudad()
	{
		return $this->_idFromCiudad;
	}
	public function SetIdFromCiudad($idFromCiudad)
	{
		$this->_idFromCiudad = (int)$idFromCiudad;
	}
	public function GetIdFromMunicipio()
	{
		return $this->_idFromMunicipio;
	}
	public function SetIdFromMunicipio($idFromMunicipio)
	{
		$this->_idFromMunicipio = (int)$idFromMunicipio;
	}
	public function GetIdFromParroquia()
	{
		return $this->_idFromParroquia;
	}
	public function SetIdFromParroquia($idFromParroquia)
	{
		$this->_idFromParroquia = (int)$idFromParroquia;
	}
	public function GetFromDireccion()
	{
		return $this->_fromDireccion;
	}
	public function SetFromDireccion($fromDireccion)
	{
		$this->_fromDireccion = $fromDireccion;
	}
	public function GetIdToEstado()
	{
		return $this->_idToEstado;
	}
	public function SetIdToEstado($idToEstado)
	{
		$this->_idToEstado = (int)$idToEstado;
	}
	public function GetIdToCiudad()
	{
		return $this->_idToCiudad;
	}
	public function SetIdToCiudad($idToCiudad)
	{
		$this->_idToCiudad = (int)$idToCiudad;
	}
	public function GetIdToMunicipio()
	{
		return $this->_idToMunicipio;
	}
	public function SetIdToMunicipio($idToMunicipio)
	{
		$this->_idToMunicipio = (int)$idToMunicipio;
	}
	public function GetIdToParroquia()
	{
		return $this->_idToParroquia;
	}
	public function SetIdToParroquia($idToParroquia)
	{
		$this->_idToParroquia = (int)$idToParroquia;
	}
	public function GetToDireccion()
	{
		return $this->_toDireccion;
	}
	public function SetToDireccion($toDireccion)
	{
		$this->_toDireccion = $toDireccion;
	}
	public function GetNombreTransporte()
	{
		return $this->_nombreTransporte;
	}
	public function SetNombreTransporte($nombreTransporte)
	{
		$this->_nombreTransporte = $nombreTransporte;
	}
	public function GetNombreFromParroquia()
	{
		return $this->_nombreFromParroquia;
	}
	public function SetNombreFromParroquia($nombreFromParroquia)
	{
		$this->_nombreFromParroquia = $nombreFromParroquia;
	}
	public function GetNombreFromMunicipio()
	{
		return $this->_nombreFromMunicipio;
	}
	public function SetNombreFromMunicipio($nombreFromMunicipio)
	{
		$this->_nombreFromMunicipio = $nombreFromMunicipio;
	}
	public function GetNombreFromCiudad()
	{
		return $this->_nombreFromCiudad;
	}
	public function SetNombreFromCiudad($nombreFromCiudad)
	{
		$this->_nombreFromCiudad = $nombreFromCiudad;
	}
	public function GetNombreFromEstado()
	{
		return $this->_nombreFromEstado;
	}
	public function SetNombreFromEstado($nombreFromEstado)
	{
		$this->_nombreFromEstado = $nombreFromEstado;
	}
	public function GetNombreToParroquia()
	{
		return $this->_nombreToParroquia;
	}
	public function SetNombreToParroquia($nombreToParroquia)
	{
		$this->_nombreToParroquia = $nombreToParroquia;
	}
	public function GetNombreToMunicipio()
	{
		return $this->_nombreToMunicipio;
	}
	public function SetNombreToMunicipio($nombreToMunicipio)
	{
		$this->_nombreToMunicipio = $nombreToMunicipio;
	}
	public function GetNombreToCiudad()
	{
		return $this->_nombreToCiudad;
	}
	public function SetNombreToCiudad($nombreToCiudad)
	{
		$this->_nombreToCiudad = $nombreToCiudad;
	}
	public function GetNombreToEstado()
	{
		return $this->_nombreToEstado;
	}
	public function SetNombreToEstado($nombreToEstado)
	{
		$this->_nombreToEstado = $nombreToEstado;
	}
	public function GetObservaciones(){
		return $this->_observaciones;
	}
	public function SetObservaciones($observaciones){
		$this->_observaciones = $observaciones;
	}
	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_idViatico = utf8_encode($this->_idViatico);
		$this->_fechaInicio = utf8_encode($this->_fechaInicio);
		$this->_fechaFin = utf8_encode($this->_fechaFin);
		$this->_diasAlimentacion = utf8_encode($this->_diasAlimentacion);
		$this->_diasHospedaje = utf8_encode($this->_diasHospedaje);
		$this->_unidadTransporteInterurbano = utf8_encode($this->_unidadTransporteInterurbano);
		$this->_idTipoTransporte = utf8_encode($this->_idTipoTransporte);
		// $this->_pasajeIdaVuelta = utf8_encode($this->_pasajeIdaVuelta); // Es booleano
		// $this->_aeropuertoResidencia = utf8_encode($this->_aeropuertoResidencia); // Es booleano
		// $this->_residenciaAeropuerto = utf8_encode($this->_residenciaAeropuerto); // Es booleano
		// $this->_tasaAeroportuariaIda = utf8_encode($this->_tasaAeroportuariaIda); // Es booleano
		// $this->_tasaAeroportuariaVuelta = utf8_encode($this->_tasaAeroportuariaVuelta); // Es booleano
		$this->_idFromEstado = utf8_encode($this->_idFromEstado);
		$this->_idFromCiudad = utf8_encode($this->_idFromCiudad);
		$this->_idFromMunicipio = utf8_encode($this->_idFromMunicipio);
		$this->_idFromParroquia = utf8_encode($this->_idFromParroquia);
		$this->_fromDireccion = utf8_encode($this->_fromDireccion);
		$this->_idToEstado = utf8_encode($this->_idToEstado);
		$this->_idToCiudad = utf8_encode($this->_idToCiudad);
		$this->_idToMunicipio = utf8_encode($this->_idToMunicipio);
		$this->_idToParroquia = utf8_encode($this->_idToParroquia);
		$this->_toDireccion = utf8_encode($this->_toDireccion);
		$this->_nombreTransporte = utf8_encode($this->_nombreTransporte);
		$this->_nombreFromParroquia = utf8_encode($this->_nombreFromParroquia);
		$this->_nombreFromMunicipio = utf8_encode($this->_nombreFromMunicipio);
		$this->_nombreFromCiudad = utf8_encode($this->_nombreFromCiudad);
		$this->_nombreFromEstado = utf8_encode($this->_nombreFromEstado);
		$this->_nombreToParroquia = utf8_encode($this->_nombreToParroquia);
		$this->_nombreToMunicipio = utf8_encode($this->_nombreToMunicipio);
		$this->_nombreToCiudad = utf8_encode($this->_nombreToCiudad);
		$this->_nombreToEstado = utf8_encode($this->_nombreToEstado);
		$this->_observaciones = utf8_encode($this->_observaciones);
		
		return $this;
	}
	public function ToArray($properties = array())
	{
		$data = array();
		
		if(is_array($properties) && count($properties) > 0){
			foreach($properties as $property){
				$nameProperty = '_' . $property;
				$data[$property] = $this->$property;
			}
		} else {
			$data = array(
				'id' => $this->_id,
				'idViatico'=> $this->_idViatico,
				'fechaInicio' => $this->_fechaInicio,
				'fechaFin' => $this->_fechaFin,
				'diasAlimentacion' => $this->_diasAlimentacion,
				'diasHospedaje' => $this->_diasHospedaje,
				'unidadTransporteInterurbano' => $this->_unidadTransporteInterurbano,
				'idTipoTransporte' => $this->_idTipoTransporte,
				'pasajeIdaVuelta' => $this->_pasajeIdaVuelta,
				'aeropuertoResidencia' => $this->_aeropuertoResidencia,
				'residenciaAeropuerto' => $this->_residenciaAeropuerto,
				'tasaAeroportuariaIda' => $this->_tasaAeroportuariaIda,
				'tasaAeroportuariaVuelta' => $this->_tasaAeroportuariaVuelta,
				'idFromEstado' => $this->_idFromEstado,
				'idFromCiudad' => $this->_idFromCiudad,
				'idFromMunicipio' => $this->_idFromMunicipio,
				'idFromParroquia' => $this->_idFromParroquia,
				'fromDireccion' => $this->_fromDireccion,
				'idToEstado' => $this->_idToEstado,
				'idToCiudad' => $this->_idToCiudad,
				'idToMunicipio' => $this->_idToMunicipio,
				'idToParroquia' => $this->_idToParroquia,
				'toDireccion' => $this->_toDireccion,
				'nombreTransporte' => $this->_nombreTransporte,
				'nombreFromParroquia' => $THIS->_nombreFromParroquia,
				'nombreFromMunicipio' => $this->_nombreFromMunicipio,
				'nombreFromCiudad' => $this->_nombreFromCiudad,
				'nombreFromEstado' => $this->_nombreFromEstado,
				'nombreToParroquia' => $THIS->_nombreToParroquia,
				'nombreToMunicipio' => $this->_nombreToMunicipio,
				'nombreToCiudad' => $this->_nombreToCiudad,
				'nombreToEstado' => $this->_nombreToEstado,
				'observaciones' => $this->_observaciones
			);
		}
		return $data;
	}
	public function ToJson($properties = array()){
		return json_encode($this->ToArray());
	}
}