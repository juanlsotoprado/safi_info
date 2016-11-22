<?php
include_once(SAFI_ENTIDADES_PATH . '/banco.php');

class EntidadChequera
{
	private $_id; // nro_chequera
	private $_cantidad; //  cheq_cantidad
	private $_banco; // banc_id
	private $_numeroCuentaBancaria; // ctab_numero
  	private $_activa; // cheq_activa integer,
	
	public function __construct()
	{
	
	}
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetCantidad(){
		return $this->_cantidad;
	}
	public function SetCantidad($cantidad){
		$this->_cantidad = $cantidad;
	}
	public function GetBanco(){
		return $this->_banco;
	}
	public function SetBanco(EntidadBanco $banco){
		$this->_banco = $banco;
	}
	public function GetNumeroCuentaBancaria(){
		return $this->_numeroCuentaBancaria;
	}
	public function SetNumeroCuentaBancaria($numeroCuentaBancaria){
		$this->_numeroCuentaBancaria = $numeroCuentaBancaria;
	}
	public function GetActiva(){
		return $this->_activa;
	}
	public function SetActiva($activa){
		$this->_activa = $activa;
	}
}