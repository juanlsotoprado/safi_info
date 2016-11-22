<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');

class BuscarCodiForm extends Formularios
{
	private $_idCodi; // Código del comprobante diario
	private $_nroCompromiso; // Código del compromiso
	private $_docAsociado; // Documento asociado
	private $_fechaEmisionInicio; // Fecha emisión inicio
	private $_fechaEmisionFin; // Fecha emisión fin
	private $_fechaElaboracionInicio; // Fecha emisión inicio
	private $_fechaElaboracionFin; // Fecha emisión fin	
	private $_idEstado; // Estatus del comprobante diario
	private $_numeroReferencia; // Número de referencia bancaria
	private $_justificacion; // Justificación del comprobante
	private $_usuario = null; // Usuario que elabora el comprobante
	

	public function __construct(){
	}
	
	public function GetIdCodi(){
		return $this->_idCodi;
	}
	public function SetIdCodi($idCodi){
		$this->_idCodi = $idCodi;
	}
	public function GetNroCompromiso(){
		return $this->_nroCompromiso;
	}
	public function SetNroCompromiso($nroCompromiso){
		$this->_nroCompromiso = $nroCompromiso;
	}
	public function GetDocumentoAsociado(){
		return $this->_docAsociado;
	}
	public function SetDocumentoAsociado($docAsociado){
		$this->_docAsociado = $docAsociado;
	}
	public function GetFechaEmisionInicio(){
		return $this->_fechaEmisionInicio;
	}
	public function SetFechaEmisionInicio($fechaEmision){
		$this->_fechaEmisionInicio = $fechaEmision;
	}
	public function GetFechaEmisionFin(){
		return $this->_fechaEmisionFin;
	}
	public function SetFechaEmisionFin($fechaEmision){
		$this->_fechaEmisionFin = $fechaEmision;
	}
	public function GetFechaElaboracionInicio(){
		return $this->_fechaElaboracionInicio;
	}
	public function SetFechaElaboracionInicio($fechaEmision){
		$this->_fechaElaboracionInicio = $fechaEmision;
	}
	public function GetFechaElaboracionFin(){
		return $this->_fechaElaboracionFin;
	}
	public function SetFechaElaboracionFin($fechaEmision){
		$this->_fechaElaboracionFin = $fechaEmision;
	}	
	
	public function GetIdEstado(){
		return $this->_idEstado;
	}
	public function SetIdEstado($idEstado){
		$this->_idEstado = $idEstado;
	}
	public function GetNumeroReferencia(){
		return $this->_numeroReferencia;
	}
	public function SetNumeroReferencia($numeroReferencia){
		$this->_numeroReferencia = $numeroReferencia;
	}
	public function GetJustificacion(){
		return $this->_justificacion;
	}
	public function SetJustificacion($justificacion){
		$this->_justificacion = $justificacion;
	}
	public function GetUsuario(){
		return $this->_usuario;
	}
	public function SetUsuario(EntidadEmpleado $empleado) {
		$this->_usuario = $empleado;
	}
}
?>