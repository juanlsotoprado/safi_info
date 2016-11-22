<?php
 
class EntidadPagoCheque
{
	private $_id; // Código de la trasnferencia
	private $_idDependencia; // Código de la dependencia
	private $_fechaPgch; // Fecha y Hora del pago con transferencia
	private $_numeroCuenta; // Número de cuenta
	private $_idEstado; // Estatus del pago con transferencia
	private $_cheque = null; // Número de la trasnferencia asociada
	private $_idDocumento; // Id del documento (doc_genera) asociado (sopg)
	private $_asuntoPgch; // Asunto del pago con transferencia
	private $_observaciones; // Observaciones del pago con transferencia
	private $_a_oPresupuestarioDocumento; // Año del presupuesto del doc generado (sopg, cfcc, ...)
	
	public function __construct(){
	}
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($idPgch){
		$this->_id = $idPgch;
	}
	public function GetIdDependencia(){
		return $this->_idDependencia;
	}
	public function SetIdDependencia($idDependencia){
		$this->_idDependencia = $idDependencia;
	}
	public function GetFechaPgch(){
		return $this->_fechaPgch;
	}
	public function SetFechaPgch($fechaPgch){
		$this->_fechaPgch = $fechaPgch;
	}
	public function GetNumeroCuenta(){
		return $this->_numeroCuenta;
	}
	public function SetNumeroCuenta($numeroCuenta){
		$this->_numeroCuenta = $numeroCuenta;
	}
	public function GetIdEstado(){
		return $this->_idEstado;
	}
	public function SetIdEstado($idEstado){
		$this->_idEstado = $idEstado;
	}
	public function GetCheque(){
		return $this->_cheque;
	}
	public function SetCheque(EntidadCheque $cheque){
		$this->_cheque = $cheque;
	}
	public function GetIdDocumento(){
		return $this->_idDocumento;
	}
	public function SetIdDocumento($idDocumento){
		$this->_idDocumento = $idDocumento;
	}
	public function GetAsuntoPgch(){
		return $this->_asuntoPgch;
	}
	public function SetAsuntoPgch($asuntoPgch){
		$this->_asuntoPgch = $asuntoPgch;
	}
	public function GetObservaciones(){
		return $this->_observaciones;
	}
	public function SetObservaciones($observaciones){
		$this->_observaciones = $observaciones;
	}	
	public function GetA_oPresupuestarioDocumento(){
		return $this->_a_oPresupuestarioDocumento;
	}
	public function SetA_oPresupuestarioDocumento($a_oPresupuestarioDocumento){
		$this->_a_oPresupuestarioDocumento = $a_oPresupuestarioDocumento;
	}
}
?>