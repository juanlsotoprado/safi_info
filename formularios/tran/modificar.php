<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');

class ModificarTransferenciaForm extends Formularios
{
	private $_id = '';	
	private $_sopg_id = '';
	private $_fecha = '';
	private $_nroReferencia = null;
	private $_banco = null;
	private $_cuentaBancaria = null;

	public function __construct()
	{
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}

	public function GetFecha(){
		return $this->_fecha;
	}
	public function SetFecha($fecha){
		$this->_fecha = $fecha;
	}
	public function GetNroReferencia(){
		return $this->_nroReferencia;
	}
	public function SetNroReferencia($nroReferencia){
		$this->_nroReferencia = $nroReferencia;
	}
	public function GetBanco(){
		return $this->_banco;
	}
	public function SetBanco(EntidadBanco $banco){
		$this->_banco = $banco;
	}
	public function GetCuentaBancaria(){
		return $this->_cuentaBancaria;
	}
	public function SetCuentaBancaria(EntidadCuentaBanco $cuentaBancaria){
		$this->_cuentaBancaria = $cuentaBancaria;
	}
	
}