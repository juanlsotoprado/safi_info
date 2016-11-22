<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');

class ReporteResponsablesAvanceForm
{
	private $_idRendicion = null;
	private $_fechaInicio = null;
	private $_fechaFin = null;
	private $_fechaRendicionInicio = null;
	private $_fechaRendicionFin = null;
	private $_idAvance = null;
	private $_cedulaResponsable = null;
	private $_nombreResponsable = null;
	private $_idEstado = null;
	private $_idRegionReporte = null;
	private $_dataAvances = array();
	private $_estatusRendicion = null;
	
	public function __construct(){
		$this->_idRendicion = GetConfig("preCodigoRendicionAvance").GetConfig("delimitadorPreCodigoDocumento");
		$this->_idAvance = GetConfig("preCodigoAvance").GetConfig("delimitadorPreCodigoDocumento");
	}
	public function GetIdRendicion(){
		return $this->_idRendicion;
	}
	public function SetIdRendicion($idRendicion){
		$this->_idRendicion = $idRendicion;
	}
	public function GetFechaInicio(){
		return $this->_fechaInicio;
	}
	public function SetFechaInicio($fechInicio){
		$this->_fechaInicio = $fechInicio;
	}
	public function GetFechaFin(){
		return $this->_fechaFin;
	}
	public function SetFechaFin($fechaFin){
		$this->_fechaFin = $fechaFin;
	}
	public function GetFechaRendicionInicio(){
		return $this->_fechaRendicionInicio;
	}
	public function SetFechaRendicionInicio($fechRendicionInicio){
		$this->_fechaRendicionInicio = $fechRendicionInicio;
	}
	public function GetFechaRendicionFin(){
		return $this->_fechaRendicionFin;
	}
	public function SetFechaRendicionFin($fechaRendicionFin){
		$this->_fechaRendicionFin = $fechaRendicionFin;
	}
	public function GetIdAvance(){
		return $this->_idAvance;
	}
	public function SetIdAvance($idAvance){
		$this->_idAvance = $idAvance;
	}
	public function GetCedulaResponsable(){
		return $this->_cedulaResponsable;
	}
	public function SetCedulaResponsable($cedulaResponsable){
		$this->_cedulaResponsable = $cedulaResponsable;
	}
	public function GetNombreResponsable(){
		return $this->_nombreResponsable;
	}
	public function SetNombreResponsable($nombreResponsable){
		$this->_nombreResponsable = $nombreResponsable;
	}
	public function GetIdEstado(){
		return $this->_idEstado;
	}
	public function SetIdEstado($idEstado){
		$this->_idEstado = $idEstado;
	}
	public function GetIdRegionReporte(){
		return $this->_idRegionReporte;
	}
	public function SetIdRegionReporte($idRegionReporte){
		$this->_idRegionReporte = $idRegionReporte;
	}
	public function GetDataAvances(){
		return $this->_dataAvances;
	}
	public function SetDataAvances($dataAvances){
		$this->_dataAvances = $dataAvances;
	}
	public function GetEstatusRendicion(){
		return $this->_estatusRendicion;
	}
	public function SetEstatusRendicion($estatusRendicion){
		$this->_estatusRendicion = $estatusRendicion;
	}
}