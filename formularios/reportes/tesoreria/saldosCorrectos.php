<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');

class SaldosCorrectosTesoreriaForm extends Formularios
{

	private $_fecha = '';
	private $_cuentaBancaria = null;

	public function __construct()
	{
	}
	public function GetCuentaBancaria(){
		return $this->_cuentaBancaria;
	}
	public function SetCuentaBancaria(EntidadCuentaBanco $cuentaBancaria){
		$this->_cuentaBancaria = $cuentaBancaria;
	}
	public function GetFecha(){
		return $this->_fecha;
	}
	public function SetFecha($fecha){
		$this->_fecha = $fecha;
	}
}