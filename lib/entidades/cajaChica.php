<?php
require_once(SAFI_ENTIDADES_PATH . '/empleado.php');
require_once(SAFI_ENTIDADES_PATH . '/puntoCuenta.php');
require_once(SAFI_ENTIDADES_PATH . '/solicitudPago.php');

class EntidadCajaChica
{
	private $_id;
	private $_fechaApertura;
	private $_fechaRegistro;
	private $_fechaUltimaModificacion;
	private $_justificacion;
	private $_responsable;
	private $_custodio;
	private $_puntosCuenta;
	private $_solicitudPago;
	
	public function __construct(){
		$this->_fechaRegistro = date("d/m/Y H:i:s");
		$this->_fechaAvance = date("d/m/Y");
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetFechaApertura(){
		return $this->_fechaApertura;
	}
	public function SetFechaApertura($fechaApertura){
		$this->_fechaApertura = $fechaApertura;
	}
	public function GetFechaRegistro(){
		return $this->_fechaRegistro;
	}
	public function SetFechaRegistro($fechaRegistro){
		$this->_fechaRegistro = $fechaRegistro;
	}
	public function GetFechaUltimaModificacion(){
		return $this->_fechaUltimaModificacion;
	}
	public function SetFechaUltimaModificacion($fechaUltimaModificacion){
		$this->_fechaUltimaModificacion = $fechaUltimaModificacion;
	}
	public function GetJustificacion(){
		return $this->_justificacion;
	}
	public function SetJustificacion($justificacion){
		$this->_justificacion = $justificacion;
	}
	public function GetResponsable(){
		return $this->_responsable;
	}
	public function SetResponsable(EntidadEmpleado $responsable){
		$this->_responsable = $responsable;
	}
	public function GetCustodio(){
		return $this->_custodio;
	}
	public function SetCustodio(EntidadEmpleado $custodio){
		$this->_custodio = $custodio;
	}
	public function GetPuntosCuenta(){
		return $this->_puntosCuenta;
	}
	public function SetPuntosCuenta(array $puntosCuenta = null){
		$this->_puntosCuenta = $puntosCuenta;
	}
	public function GetSolicitudPago(){
		return $this->_solicitudPago;
	}
	public function SetSolicitudPago(EntidadSolicitudPago $solicitudPago = null){
		$this->_solicitudPago = $solicitudPago;
	}
	public function __toString()
	{
		return "
			Id = ".$this->_id.",
			FechaApertura = ".$this->_fechaApertura.",
			FechaRegistro = ".$this->_fechaRegistro.",
			FechaUltimaModificacion = ".$this->_fechaUltimaModificacion.",
			Justificacion = ".$this->_justificacion.",
			Responsable = ".($this->_responsable !== null ? $this->_responsable : "NULL").",
			Custodio = = ".($this->_custodio !== null ? $this->_custodio : "NULL")."
		";
	}
	public function __clone()
	{
		$this->_responsable = ($this->_responsable !== null) ? clone $this->_responsable : null;
		$this->_custodio = ($this->_custodio !== null) ? clone $this->_custodio : null;
	}
	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_fechaApertura = utf8_encode($this->_fechaApertura);
		$this->_fechaRegistro = utf8_encode($this->_fechaRegistro);
		$this->_fechaUltimaModificacion = utf8_encode($this->_fechaUltimaModificacion);
		$this->_justificacion = utf8_encode($this->_justificacion);
		if($this->_responsable !== null) $this->_responsable->UTF8Encode();
		if($this->_custodio !== null) $this->_custodio->UTF8Encode();
	}
	public function ToArray($properties = array())
	{
		$data = array();
		
		if(is_array($properties) && count($properties) > 0){
			foreach($properties as $property){
				$nameProperty = '_' . $property;
				$data[$property] = $this->$property;
			}
		} else {
			$data = array(
				'id' => $this->_id,
				'fechaApertura' => $this->_fechaApertura,
				'fechaRegistro' => $this->_fechaRegistro,
				'fechaUltimaModificacion' => $this->_fechaUltimaModificacion,
				'justificacion' => $this->_justificacion,
				'responsable' => ($this->_responsable !== null) ? $this->_responsable->ToArray() : null,
				'custodio' => ($this->_custodio !== null) ? $this->_custodio->ToArray() : null
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}
?>