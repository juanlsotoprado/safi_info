<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');
include_once(SAFI_ENTIDADES_PATH . '/viaticonacional.php');

class Reporte1ViaticoNacionalForm extends Formularios
{
	private $_fechaInicio = '';
	private $_fechaFin = '';
	private $_idViaticoNacional = '';
	private $_viaticosNacionales = array();
	
	public function __construct()
	{
		
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
	public function GetIdViaticoNacional(){
		return $this->_idViaticoNacional;
	}
	public function SetIdViaticoNacional($idViaticoNacional){
		$this->_idViaticoNacional = $idViaticoNacional;
	}
	public function GetViaticosNacionales(){
		return $this->_viaticosNacionales;
	}
	public function SetViaticosNacionales(array $viaticosNacionales){
		$this->_viaticosNacionales = $viaticosNacionales;
	}
	public function GetViaticoNacional($id){
		return $this->_viaticosNacionales[$id];
	}
	public function SetViaticoNacional($id, EntidadViaticoNacional $viaticoNacional){
		$this->_viaticosNacionales[$id] = $viaticoNacional;
	}
}