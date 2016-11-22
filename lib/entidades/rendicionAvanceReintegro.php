<?php
require_once (SAFI_ENTIDADES_PATH . '/banco.php');

class EntidadRendicionAvanceReintegro
{
	private $_idResponsableAvance;
	private $_idRendicionAvance;
	private $_banco;
	private $_referencia;
	private $_fecha;
	private $_monto;
	
	public function __construct(){
	
	}
	public function GetIdResponsableAvance(){
		return $this->_idResponsableAvance;
	}
	public function SetIdResponsableAvance($idResponsableAvance){
		$this->_idResponsableAvance = $idResponsableAvance;
	}
	public function GetIdRendicionAvance(){
		return $this->_idRendicionAvance;
	}
	public function SetIdRendicionAvance($idRendicionAvance){
		$this->_idRendicionAvance = $idRendicionAvance;
	}
	public function GetBanco(){
		return $this->_banco;
	}
	public function SetBanco(EntidadBanco $banco = null){
		$this->_banco = $banco;
	}
	public function GetReferencia(){
		return $this->_referencia;
	}
	public function SetReferencia($referencia){
		$this->_referencia = $referencia;
	}
	public function GetFecha(){
		return $this->_fecha;
	}
	public function SetFecha($fecha){
		$this->_fecha = $fecha;
	}
	public function GetMonto(){
		return $this->_monto;
	}
	public function SetMonto($monto){
		$this->_monto = $monto;
	}
	public function __clone(){
		$this->_banco = ($this->_banco != null) ? clone $this->_banco : null;
	}
	public function UTF8Encode()
	{
		$this->_idResponsableAvance = utf8_encode($this->_idResponsableAvance);
		$this->_idRendicionAvance = utf8_encode($this->_idRendicionAvance);
		if($this->_banco != null) $this->_banco->UTF8Encode();
		$this->_referencia = utf8_encode($this->_referencia);
		$this->_fecha = utf8_encode($this->_fecha);
		$this->_monto = utf8_encode($this->_monto);
		
		return $this;
	}
	public function ToArray($properties = array()){
		$data = array();
		
		if(is_array($properties) && count($properties) > 0){
			foreach($properties as $property){
				$nameProperty = '_' . $property;
				$data[$property] = $this->$property;
			}
		} else {
			$data = array(
				'idResponsableAvance' => $this->_idResponsableAvance,
				'idRendicionAvance' => $this->_idRendicionAvance,
				'banco' => ($this->_banco != null) ? $this->_banco->ToArray() : null,
				'referencia' => $this->_referencia,
				'fecha' => $this->_fecha,
				'monto' => $this->_monto
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}