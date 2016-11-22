<?php
 
class EntidadPagoTransferencia
{
	private $_idTransferencia; // Código de la trasnferencia
	private $_idDependencia; // Código de la dependencia
	private $_fechaTransferencia; // Fecha y Hora del pago con transferencia
	private $_cuentaEmisor; // Número de cuenta
	private $_idEstado; // Estatus del pago con transferencia
	private $_numeroReferencia; // Número de la trasnferencia asociada
	private $_idDocumento; // Id del documento (doc_genera) asociado (sopg)
	private $_asuntoTransferencia; // Asunto del pago con transferencia
	private $_a_oPresupuestarioDocumento; // Año del presupuesto del doc generado (sopg, cfcc, ...)
	private $_rifCedula; // Rif o Cédula del beneficiario
	private $_observacionesTransferencia; // Observaciones del pago con transferencia
	private $_montoTransferencia; //  Monto del pago con transferencia
	private $_numeroCuentaReceptor;
	private $_beneficiario;
	

	public function __construct(){
	}
	
	public function GetIdTransferencia(){
		return $this->_idTransferencia;
	}
	public function SetIdTransferencia($idTransferencia){
		$this->_idTransferencia = $idTransferencia;
	}
	public function GetIdDependencia(){
		return $this->_idDependencia;
	}
	public function SetIdDependencia($idDependencia){
		$this->_idDependencia = $idDependencia;
	}
	public function GetFechaTransferencia(){
		return $this->_fechaTransferencia;
	}
	public function SetFechaTransferencia($fechaTransferencia){
		$this->_fechaTransferencia = $fechaTransferencia;
	}
	public function GetCuentaEmisor(){
		return $this->_cuentaEmisor;
	}
	public function SetCuentaEmisor(EntidadCuentaBanco $cuentaEmisor){
		$this->_cuentaEmisor = $cuentaEmisor;
	}
	public function GetIdEstado(){
		return $this->_idEstado;
	}
	public function SetIdEstado($idEstado){
		$this->_idEstado = $idEstado;
	}
	public function GetNumeroReferencia(){
		return $this->_numeroReferencia;
	}
	public function SetNumeroReferencia($numeroReferencia){
		$this->_numeroReferencia = $numeroReferencia;
	}
	public function GetIdDocumento(){
		return $this->_idDocumento;
	}
	public function SetIdDocumento($idDocumento){
		$this->_idDocumento = $idDocumento;
	}
	public function GetAsuntoTransferencia(){
		return $this->_asuntoTransferencia;
	}
	public function SetAsuntoTransferencia($asuntoTransferencia){
		$this->_asuntoTransferencia = $asuntoTransferencia;
	}
	public function GetA_oPresupuestarioDocumento(){
		return $this->_a_oPresupuestarioDocumento;
	}
	public function SetA_oPresupuestarioDocumento($a_oPresupuestarioDocumento){
		$this->_a_oPresupuestarioDocumento = $a_oPresupuestarioDocumento;
	}
	public function GetRifCedula(){
		return $this->_rifCedula;
	}
	public function SetRifCedula($rifCedula){
		$this->_rifCedula = $rifCedula;
	}
	public function GetObservacionesTransferencia(){
		return $this->_observacionesTransferencia;
	}
	public function SetObservacionesTransferencia($observacionesTransferencia){
		$this->_observacionesTransferencia = $observacionesTransferencia;
	}
	public function GetMontoTransferencia(){
		return $this->_montoTransferencia;
	}
	public function SetMontoTransferencia($montoTransferencia){
		$this->_montoTransferencia = $montoTransferencia;
	}
	public function GetNumeroCuentaReceptor(){
		return $this->_numeroCuentaReceptor;
	}
	public function SetNumeroCuentaReceptor($numeroCuentaReceptor){
		$this->_numeroCuentaReceptor = $numeroCuentaReceptor;
	}
	public function GetBeneficiario(){
		return $this->_beneficiario;
	}
	public function SetBeneficiario($beneficiario){
		$this->_beneficiario = $beneficiario;
	}
}
?>