<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');

class UbicarDocumentoTesoreriaForm extends Formularios
{
	private $_tipoBusqueda = '';	
	private $_fechaInicio = '';
	private $_fechaFin = '';
	private $_documento = '';

	public function __construct()
	{
	}
	public function GetTipoBusqueda(){
		return $this->_tipoBusqueda;
	}
	public function SetTipoBusqueda($tipoBusqueda){
		$this->_tipoBusqueda = $tipoBusqueda;
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
	public function GetDocumento(){
		return $this->_documento;
	}
	public function SetDocumento($documento){
		$this->_documento = $documento;
	}
}