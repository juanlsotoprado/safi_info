<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');

class AuxiliarForm extends Formularios
{
	private $_cuentaContable = null;
	private $_fechaInicio = '';
	private $_fechaFin = '';

	public function GetCuentaContable(){
		return $this->_cuentaContable;
	}
	public function SetCuentaContable(EntidadCuentaContable $cuentaContable){
		$this->_cuentaContable = $cuentaContable;
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
}