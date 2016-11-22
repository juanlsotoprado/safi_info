<?php
class EntidadCheque
{
	private $_id; // id_cheque
	private $_idEstado;
	private $_numero; // nro_cheque
	private $_monto; // monto_cheque
	private $_idChequera; // nro_chequera
	private $_fechaEmision; // fechaemision_cheque
	private $_idDocumento; // docg_id
	private $beneficiarioCheque;
	private $ciRifBeneficiarioCheque;	
	
	public function __construct(){
	
	}
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetIdEstado(){
		return $this->_idEstado;
	}
	public function SetIdEstado($idEstado){
		$this->_idEstado = $idEstado;
	}
	public function GetNumero(){
		return $this->_numero;
	}
	public function SetNumero($numero){
		$this->_numero = $numero;
	}
	public function GetMonto(){
		return $this->_monto;
	}
	public function SetMonto($monto){
		$this->_monto = $monto;
	}
	public function GetIdChequera(){
		return $this->_idChequera;
	}
	public function SetIdChequera($idChequera){
		$this->_idChequera = $idChequera;
	}
	public function GetFechaEmision(){
		return $this->_fechaEmision;
	}
	public function SetFechaEmision($fechaEmision){
		$this->_fechaEmision = $fechaEmision;
	}
	public function GetIdDocumento(){
		return $this->_idDocumento;
	}
	public function SetIdDocumento($idDocumento){
		$this->_idDocumento = $idDocumento;
	}
	public function GetBeneficiarioCheque(){
		return $this->_beneficiarioCheque;
	}
	public function SetBeneficiarioCheque($nombreBeneficiario){
		$this->_beneficiarioCheque = $nombreBeneficiario;
	}
	public function GetCiRifBeneficiarioCheque(){
		return $this->_ciRifBeneficiarioCheque;
	}
	public function SetCiRifBeneficiarioCheque($ciRif){
		$this->_ciRifBeneficiarioCheque = $ciRif;
	}		
	/*
	public function Get(){
		return $this->_;
	}
	public function Set($){
		$this->_ = $;
	}
	*/
	
}