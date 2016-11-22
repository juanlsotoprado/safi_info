<?php
class EntidadAsignacionViatico
{
	const TIPO_FIJO = 1;
	const TIPO_VARIABLE = 2;
	
	const COD_HOSPEDAJE = 1;
	const COD_ALIMENTACION = 2;
	const COD_RESIDENCIA_AEROPUERTO = 3;
	const COD_AEROPUERTO_RESIDENCIA = 4;
	const COD_TRANSPORTE_INTERURBANO = 5;
	const COD_TASA_AEROPORTUARIA = 6;
	const COD_SERVICIO_COMUNICACIONES = 7;
	const COD_ASIGNACION_TRANSPORTE = 8; // por km recorrido con vehículo propio
	const COD_TRANSPORTE_EXTRAURBANO = 9;
	const COD_TRANSPORTE_ENTRE_CIUDADES = 10;
	
	// Unidad de medida de las asignaciones
	const UNIDAD_MEDIDA_POR_NOCHE = 1;
	const UNIDAD_MEDIDA_DIARIO = 2;
	const UNIDAD_MEDIDA_POR_TRASLADO = 3;
	const UNIDAD_MEDIDA_POR_VIAJE = 4;
	const UNIDAD_MEDIDA_POR_KILOMETRO = 5;
	
	private $_id;
	private $_codigo;
	private $_nombre;
	private $_fechaInicio;
	private $_fechaFin;
	private $_tipo;
	private $_observacion;
	private $_unidadMedida;
	private $_montoFijo;
	private $_estatusActividad;
	private $_ordenacionTipo;
	private $_ordenacionGlobal;
	
	public function __construct()
	{
		
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetCodigo(){
		return $this->_codigo;
	}
	public function SetCodigo($codigo){
		$this->_codigo = $codigo;
	}
	public function GetNombre(){
		return $this->_nombre;
	}
	public function SetNombre($nombre){
		$this->_nombre = $nombre;
	}
	public function GetFechaInicio(){
		return $this->_fechaInicio;
	}
	public function SetFechaInicio($fechaInicio){
		$this->_fechaInicio = $fechaInicio;
	}
	public function GetFechaFin(){
		return $this->_fechaFin;
	}
	public function SetFechaFin($fechaFin){
		$this->_fechaFin = $fechaFin;
	}
	public function GetTipo(){
		return $this->_tipo;
	}
	public function SetTipo($tipo){
		$this->_tipo = $tipo;
	}
	public function GetObservacion(){
		return $this->_observacion;
	}
	public function SetObservacion($observacion){
		$this->_observacion = $observacion;
	}
	public function GetUnidadMedida(){
		return $this->_unidadMedida;
	}
	public function SetUnidadMedida($unidadMedida){
		$this->_unidadMedida = $unidadMedida;
	}
	public function GetMontoFijo(){
		return $this->_montoFijo;
	}
	public function SetMontoFijo($montoFijo){
		$this->_montoFijo = $montoFijo;
	}
	public function GetEstatusActividad(){
		return $this->_estatusActividad;
	}
	public function SetEstatusActividad($estatusActividad){
		$this->_estatusActividad = $estatusActividad;
	}
	public function GetOrdenacionTipo(){
		return $this->_ordenacionTipo;
	}
	public function SetOrdenacionTipo($ordenacionTipo){
		$this->_ordenacionTipo = $ordenacionTipo;
	}
	public function GetOrdenacionGlobal(){
		return $this->_ordenacionGlobal;
	}
	public function SetOrdenacionGlobal($ordenacionGlobal){
		$this->_ordenacionGlobal = $ordenacionGlobal;
	}
}