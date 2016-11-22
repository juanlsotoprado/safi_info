<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');

class CuentaBancariaPagadoContabilidadForm extends Formularios
{
	private $_cuentaBancaria = null;
	private $_tipoSolicitudPago = null;
	private $_tipoActividadCompromiso = null;
	private $_detalleSolicitudPago = '';	
	private $_fechaInicio = '';
	private $_fechaFin = '';
	private $_tipoReporte = '';	
	private $_estado = '';

	public function GetCuentaBancaria(){
		return $this->_cuentaBancaria;
	}
	public function SetCuentaBancaria(EntidadCuentaBanco $cuentaBancaria){
		$this->_cuentaBancaria = $cuentaBancaria;
	}

	public function GetTipoSolicitudPago(){
		return $this->_tipoSolicitudPago;
	}
	public function SetTipoSolicitudPago(EntidadTipoSolicitudPago $tipoSolicitudPago){
		$this->_tipoSolicitudPago = $tipoSolicitudPago;
	}
	
	public function GetTipoActividadCompromiso(){
		return $this->_tipoActividadCompromiso;
	}
	public function SetTipoActividadCompromiso(EntidadTipoActividadCompromiso $tipoActividadCompromiso){
		$this->_tipoActividadCompromiso = $tipoActividadCompromiso;
	}	
	public function GetDetalleSolicitudPago(){
		return $this->_detalleSolicitudPago;
	}
	public function SetDetalleSolicitudPago($detalleSolicitudPago){
		$this->_detalleSolicitudPago = $detalleSolicitudPago;
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
	public function GetTipoReporte(){
		return $this->_tipoReporte;
	}
	public function SetTipoReporte($tipoReporte){
		$this->_tipoReporte = $tipoReporte;
	}
	
	public function GetEstado(){
		return $this->_estado;
	}
	public function SetEstado($estado){
		$this->_estado = $estado;
	}
}