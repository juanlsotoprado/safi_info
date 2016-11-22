<?php
class EntidadSolicitudPago
{
	private $_id; // sopg_id
	private $_idCompromiso; // comp_id
	private $_beneficiarioCedulaRif; // sopg_bene_ci_rif
	private $_beneficiarioNombre; // 
	private $_fecha; // fecha
	private $_detalle; // detalle
	
	public function __construct(){
	
	}
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetIdCompromiso(){
		return $this->_idCompromiso;
	}
	public function SetIdCompromiso($idCompromiso){
		$this->_idCompromiso = $idCompromiso;
	}
	public function GetBeneficiarioCedulaRif(){
		return $this->_beneficiarioCedulaRif;
	}
	public function SetBeneficiarioCedulaRif($beneficiarioCedulaRif){
		$this->_beneficiarioCedulaRif = $beneficiarioCedulaRif;
	}
	public function GetBeneficiarioNombre(){
		return $this->_beneficiarioNombre;
	}
	public function SetBeneficiarioNombre($beneficiarioNombre){
		$this->_beneficiarioNombre = $beneficiarioNombre;
	}
	public function GetFecha(){
		return $this->_fecha;
	}
	public function SetFecha($fecha){
		$this->_fecha = $fecha;
	}
	public function GetDetalle(){
		return $this->_detalle;
	}
	public function SetDetalle($detalle){
		$this->_detalle = $detalle;
	}		
	
	public function __toString()
	{
		return "
			Id = ".$this->_id.",
			IdCompromiso = ".$this->_idCompromiso.",
			BeneficiarioCedulaRif = ".$this->_beneficiarioCedulaRif.",
			BeneficiarioNombre = ".$this->_beneficiarioNombre.",  
			Fecha = ".$this->fecha.",
			Detalle = ".$this->detalle." 					
		";
	}
	public function __clone()
	{
	}
	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_idCompromiso = utf8_encode($this->_idCompromiso);
		$this->_beneficiarioCedulaRif = utf8_encode($this->_beneficiarioCedulaRif);
		$this->_beneficiarioNombre = utf8_encode($this->_beneficiarioNombre);
		$this->_fecha = utf8_encode($this->_fecha);
		$this->_detalle = utf8_encode($this->_detalle);						
		return $this;
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
				'idCompromiso' => $this->_idCompromiso,
				'beneficiarioCedulaRif' => $this->_beneficiarioCedulaRif,
				'beneficiarioNombre' => $this->_beneficiarioNombre,
				'fecha' => $this->_fecha,
				'detalle' => $this->_beneficiarioCedulaRif															
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}