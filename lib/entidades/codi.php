<?php
 
class EntidadCodi
{
	private $_idCodi; // Código del comprobante diario
	private $_nroCompromiso; // Código del compromiso
	private $_docAsociado; // Documento asociado
	private $_fechaEmision; // Fecha emisión
	private $_idEstado; // Estatus del comprobante diario
	private $_numeroReferencia; // Número de referencia bancaria
	private $_justificacion; // Justificación del comprobante
	private $_fechaEfectiva; // Fecha efectiva del codi
	private $_idUsuario; // Usuario que elabora el comprobante
	private $_memoContenido; // Contenido del memo del comprobante
	private $_memoResponsable; // Usuario que elabora el comprobante		
	

	public function __construct(){
	}
	
	public function GetId(){
		return $this->_idCodi;
	}
	public function SetId($idCodi){
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
	public function GetFechaEmision(){
		return $this->_fechaEmision;
	}
	public function SetFechaEmision($fechaEmision){
		$this->_fechaEmision = $fechaEmision;
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
	public function GetFechaEfectiva(){
		return $this->_fechaEfectiva;
	}
	public function SetFechaEfectiva($fechaEfectiva){
		$this->_fechaEfectiva = $fechaEfectiva;
	}
	public function GetIdUsuario(){
		return $this->_idUsuario;
	}
	public function SetIdUsuario($idUsuario) {
		$this->_idUsuario = $idUsuario;
	}
	public function GetMemoContenido(){
		return $this->_memoContenido;
	}
	public function SetMemoContenido($memoContenido) {
		$this->_memoContenido = $memoContenido;
	}
	public function GetMemoResponsable(){
		return $this->_memoResponsable;
	}
	public function SetMemoResponsable($memoResponsable) {
		$this->_memoResponsable = $memoResponsable;
	}
	
	
}
?>