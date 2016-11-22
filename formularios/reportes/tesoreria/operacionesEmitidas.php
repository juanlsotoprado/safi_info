<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');

class OperacionesEmitidasTesoreriaForm extends Formularios
{
	private $_fechaInicio = '';
	private $_fechaFin = '';
	private $_cuentaBancaria = null;
	private $_tipoBusqueda = '';	

	public function __construct()
	{
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
	public function GetCuentaBancaria(){
		return $this->_cuentaBancaria;
	}
	public function SetCuentaBancaria(EntidadCuentaBanco $cuentaBancaria){
		$this->_cuentaBancaria = $cuentaBancaria;
	}
	public function GetTipoBusqueda(){
		return $this->_tipoBusqueda;
	}
	public function SetTipoBusqueda($tipoBusqueda){
		$this->_tipoBusqueda = $tipoBusqueda;
	}
}