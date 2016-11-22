<?php
include_once(SAFI_ENTIDADES_PATH . '/dependencia.php');
include_once(SAFI_ENTIDADES_PATH . '/banco.php');

class EntidadRendicionViaticoNacional
{
	private $_id;
	private $_idViaticoNacional;
	private $_fechaRendicion;
	private $_fechaRegistro;
	private $_fechaUltimaModificacion;
	private $_fechaInicioViaje;
	private $_fechaFinViaje;
	private $_objetivosViaje;
	private $_montoAnticipo = 0.0;
	private $_totalGastos = 0.0;
	private $_montoReintegro = 0.0;
	private $_reintegroBanco;
	private $_reintegroReferencia;
	private $_reintegroFecha;
	private $_observaciones;
	private $_informeFileName;
	private $_usuaLogin;
	private $_dependencia;
	
	public function __construct()
	{
		$this->_fechaRegistro = date("d/m/Y H:i:s");
		$this->_fechaRendicion = date("d/m/Y");
	}
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetIdViaticoNacional(){
		return $this->_idViaticoNacional;
	}
	public function SetIdViaticoNacional($idViaticoNacional){
		$this->_idViaticoNacional = $idViaticoNacional;
	}
	public function GetFechaRendicion(){
		return $this->_fechaRendicion;
	}
	public function SetFechaRendicion($fechaRendicion){
		$this->_fechaRendicion = $fechaRendicion;
	}
	public function GetFechaRegistro(){
		return $this->_fechaRegistro;
	}
	public function SetFechaRegistro($fechaRegistro){
		$this->_fechaRegistro = $fechaRegistro;
	}
	public function GetFechaUltimaModificacion(){
		return $this->_fechaUltimaModificacion;
	}
	public function SetFechaUltimaModificacion($fechaUltimaModificacion){
		$this->_fechaUltimaModificacion = $fechaUltimaModificacion;
	}
	public function GetFechaInicioViaje(){
		return $this->_fechaInicioViaje;
	}
	public function SetFechaInicioViaje($fechaInicioViaje){
		$this->_fechaInicioViaje = $fechaInicioViaje;
	}
	public function GetFechaFinViaje(){
		return $this->_fechaFinViaje;
	}
	public function SetFechaFinViaje($fechaFinViaje){
		$this->_fechaFinViaje = $fechaFinViaje;
	}
	public function GetObjetivosViaje(){
		return $this->_objetivosViaje;
	}
	public function SetObjetivosViaje($objetivosViaje){
		$this->_objetivosViaje = $objetivosViaje;
	}
	public function GetMontoAnticipo(){
		return $this->_montoAnticipo;
	}
	public function SetMontoAnticipo($montoAnticipo){
		$this->_montoAnticipo = $montoAnticipo;
	}
	public function GetTotalGastos(){
		return $this->_totalGastos;
	}
	public function SetTotalGastos($totalGastos){
		$this->_totalGastos = $totalGastos;
	}
	public function GetMontoReintegro(){
		return $this->_montoReintegro;
	}
	public function SetMontoReintegro($montoReintegro){
		$this->_montoReintegro = $montoReintegro;
	}
	public function GetReintegroBanco(){
		return $this->_reintegroBanco;
	}
	public function SetReintegroBanco(EntidadBanco $reintegroBanco = null){
		$this->_reintegroBanco = $reintegroBanco;
	}
	public function GetReintegroReferencia(){
		return $this->_reintegroReferencia;
	}
	public function SetReintegroReferencia($reintegroReferencia){
		$this->_reintegroReferencia = $reintegroReferencia;
	}
	public function GetReintegroFecha(){
		return $this->_reintegroFecha;
	}
	public function SetReintegroFecha($reintegroFecha){
		$this->_reintegroFecha = $reintegroFecha;
	}
	public function GetObservaciones(){
		return $this->_observaciones;
	}
	public function SetObservaciones($observaciones){
		$this->_observaciones = $observaciones;
	}
	public function GetInformeFileName(){
		return $this->_informeFileName;
	}
	public function SetInformeFileName($informeFileName){
		$this->_informeFileName = $informeFileName;
	}
	public function GetUsuaLogin(){
		return $this->_usuaLogin;
	}
	public function SetUsuaLogin($usuaLogin){
		$this->_usuaLogin = $usuaLogin;
	}
	public function GetDependencia(){
		return $this->_dependencia;
	}
	public function SetDependencia(EntidadDependencia $dependencia = null){
		$this->_dependencia = $dependencia;
	}
}