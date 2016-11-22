<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');

class ReporteResponsablesViaticoForm
{
	private $_fechaInicio = null;
	private $_fechaFin = null;
	private $_fechaRendicionInicio = null;
	private $_fechaRendicionFin = null;
	private $_estatusRendicion = null;
	private $_idEstado = null;
	private $_idRegionReporte = null;
	private $_centroGestorCosto = null;
	private $_tipoProyectoAccionCentralizada = null;  // Indica si se seleccionó un proyecto o una acción centralizada.
															// Valores ('proyecto', 'accionCentralizada')
	private $_idProyectoAccionCentralizada = null;  // Id del proyecto o acción centralizada.
	private $_idAccionEspecifica = null;  // Id de la acción específica
	private $_datosViaticos = array();
	
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
	public function GetFechaRendicionInicio(){
		return $this->_fechaRendicionInicio;
	}
	public function SetFechaRendicionInicio($fechRendicionInicio){
		$this->_fechaRendicionInicio = $fechRendicionInicio;
	}
	public function GetFechaRendicionFin(){
		return $this->_fechaRendicionFin;
	}
	public function SetFechaRendicionFin($fechaRendicionFin){
		$this->_fechaRendicionFin = $fechaRendicionFin;
	}
	public function GetEstatusRendicion(){
		return $this->_estatusRendicion;
	}
	public function SetEstatusRendicion($estatusRendicion){
		$this->_estatusRendicion = $estatusRendicion;
	}
	public function GetIdEstado(){
		return $this->_idEstado;
	}
	public function SetIdEstado($idEstado){
		$this->_idEstado = $idEstado;
	}
	public function GetIdRegionReporte(){
		return $this->_idRegionReporte;
	}
	public function SetIdRegionReporte($idRegionReporte){
		$this->_idRegionReporte = $idRegionReporte;
	}
	public function GetCentroGestorCosto(){
		return $this->_centroGestorCosto;
	}
	public function SetCentroGestorCosto($centroGestorCosto){
		$this->_centroGestorCosto = $centroGestorCosto;
	}
	public function GetTipoProyectoAccionCentralizada(){
		return $this->_tipoProyectoAccionCentralizada;
	}
	public function SetTipoProyectoAccionCentralizada($tipoProyectoAccionCentralizada){
		$this->_tipoProyectoAccionCentralizada = $tipoProyectoAccionCentralizada;
	}
	public function GetIdProyectoAccionCentralizada(){
		return $this->_idProyectoAccionCentralizada;
	}
	public function SetIdProyectoAccionCentralizada($idProyectoAccionCentralizada){
		$this->_idProyectoAccionCentralizada = $idProyectoAccionCentralizada;
	}
	public function GetIdAccionEspecifica(){
		return $this->_idAccionEspecifica;
	}
	public function SetIdAccionEspecifica($idAccionEspecifica){
		$this->_idAccionEspecifica = $idAccionEspecifica;
	}
	public function GetDatosViaticos(){
		return $this->_datosViaticos;
	}
	public function SetDatosViaticos($datosViaticos){
		$this->_datosViaticos = $datosViaticos;
	}
}
?>