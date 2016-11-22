<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');

class ReporteActividadForm extends Formularios{
	private $_tipoActividad = null;
	private $_tipoEvento = null;
	private $_estado = null;
	private $_centroGestorCosto;
	private $_fechaInicio;
	private $_fechaFin;
	
	public function __construct(){
		$this->_tipoActividad = new EntidadTipoActividadCompromiso();
		$this->_tipoEvento = new EntidadTipoEvento();
		$this->_estado = new EntidadEstado();
		$this->_fechaInicio = date('01/01/Y');
		$this->_fechaFin = date('d/m/Y');
	}

	public function GetTipoActividad(){
		return $this->_tipoActividad;
	}
	public function SetTipoActividad(EntidadTipoActividadCompromiso $tipoActividad){
		$this->_tipoActividad = $tipoActividad;
	}
	
	public function GetTipoEvento(){
		return $this->_tipoEvento;
	}
	public function SetTipoEvento(EntidadTipoEventoCompromiso $tipoEvento){
		$this->_tipoEvento = $tipoEvento;
	}
	
	public function GetEstado(){
		return $this->_estado;
	}
	public function SetEstado(EntidadEstado $estado){
		$this->_estado = $estado;
	}
	
	public function GetCentroGestorCosto(){
		return $this->_centroGestorCosto;
	}
	public function SetCentroGestorCosto($centroGestorCosto){
		$this->_centroGestorCosto = $centroGestorCosto;
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