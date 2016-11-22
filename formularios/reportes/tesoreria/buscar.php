<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');

class BuscarTesoreriaForm extends Formularios
{
	private $_tipoBusqueda = '';	
	private $_fechaInicioEmision = '';
	private $_fechaFinEmision = '';
	private $_cuentaBancaria = null;
	private $_numeroReferencia = '';
	private $_estatusCheque = null;
	private $_beneficiario = null;
	private $_fechaInicioEmisionAnulado = '';
	private $_fechaFinEmisionAnulado = '';
	private $_fechaInicioAnulacion = '';						
	private $_fechaFinAnulacion = '';
	private $_anoEmisionCheque = '';
	private $_anoAnulacionCheque = '';	

	public function __construct()
	{
	}
	public function GetTipoBusqueda(){
		return $this->_tipoBusqueda;
	}
	public function SetTipoBusqueda($tipoBusqueda){
		$this->_tipoBusqueda = $tipoBusqueda;
	}
	public function GetFechaInicioEmision(){
		return $this->_fechaInicioEmision;
	}
	public function SetFechaInicioEmision($fechaInicioEmision){
		$this->_fechaInicioEmision = $fechaInicioEmision;
	}
	public function GetFechaFinEmision(){
		return $this->_fechaFinEmision;
	}
	public function SetFechaFinEmision($fechaFinEmision){
		$this->_fechaFinEmision = $fechaFinEmision;
	}
	public function GetCuentaBancaria(){
		return $this->_cuentaBancaria;
	}
	public function SetCuentaBancaria(EntidadCuentaBanco $cuentaBancaria){
		$this->_cuentaBancaria = $cuentaBancaria;
	}
	public function GetNumeroReferencia(){
		return $this->_numeroReferencia;
	}
	public function SetNumeroReferencia($numeroReferencia){
		$this->_numeroReferencia = $numeroReferencia;
	}
	public function GetEstatusCheque(){
		return $this->_estatusCheque;
	}
	public function SetEstatusCheque(EntidadEstatus $estatusCheque){
		$this->_estatusCheque = $estatusCheque;
	}
	public function GetBeneficiario(){
		return $this->_beneficiario;
	}
	public function SetBeneficiario(EntidadBeneficiarioViatico $beneficiario){
		$this->_beneficiario = $beneficiario;
	}
	public function GetFechaInicioEmisionAnulado(){
		return $this->_fechaInicioEmisionAnulado;
	}
	public function SetFechaInicioEmisionAnulado($fechaInicioEmisionAnulado){
		$this->_fechaInicioEmisionAnulado = $fechaInicioEmisionAnulado;
	}	
	public function GetFechaFinEmisionAnulado(){
		return $this->_fechaFinEmisionAnulado;
	}
	public function SetFechaFinEmisionAnulado($fechaFinEmisionAnulado){
		$this->_fechaFinEmisionAnulado = $fechaFinEmisionAnulado;
	}	
	public function GetFechaInicioAnulacion(){
		return $this->_fechaInicioAnulacion;
	}
	public function SetFechaInicioAnulacion($fechaInicioAnulacion){
		$this->_fechaInicioAnulacion = $fechaInicioAnulacion;
	}
	public function GetFechaFinAnulacion(){
		return $this->_fechaFinAnulacion;
	}
	public function SetFechaFinAnulacion($fechaFinAnulacion){
		$this->_fechaFinAnulacion = $fechaFinAnulacion;
	}
	public function GetAnoEmisionCheque(){
		return $this->_anoEmisionCheque;
	}
	public function SetAnoEmisionCheque($anoEmisionCheque){
		$this->_anoEmisionCheque = $anoEmisionCheque;
	}
	public function GetAnoAnulacionCheque(){
		return $this->_anoAnulacionCheque;
	}
	public function SetAnoAnulacionCheque($anoAnulacionCheque){
		$this->_anoAnulacionCheque = $anoAnulacionCheque;
	}
	
}