<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');

class BuscarRendicionViaticoNacionalForm extends Formularios
{
	private $_fechaInicio = '';
	private $_fechaFin = '';
	private $_idRendicion;
	private $_dataRendiciones = array();
	
	public function __construct()
	{
		$this->SetIdRendicion(
			$_idRendicion = GetConfig("preCodigoRendicionViaticoNacional").GetConfig("delimitadorPreCodigoDocumento")
		);
	}
	public function GetFechaInicio(){
		return $this->_fechaInicio;
	}
	public function SetFechaInicio($fechInicio){
		$this->_fechaInicio = $fechInicio;
	}
	public function GetFechaFin(){
		return $this->_fechaFin;
	}
	public function SetFechaFin($fechaFin){
		$this->_fechaFin = $fechaFin;
	}
	public function GetIdRendicion(){
		return $this->_idRendicion;
	}
	public function SetIdRendicion($idRendicion){
		$this->_idRendicion = $idRendicion;
	}
	public function GetDataRendiciones(){
		return $this->_dataRendiciones;
	}
	public function SetDataRendiciones($dataRendiciones){
		$this->_dataRendiciones = $dataRendiciones;
	}
}