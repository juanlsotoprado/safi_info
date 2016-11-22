<?php
require_once(SAFI_ENTIDADES_PATH . '/partida.php');

class EntidadRendicionAvancePartida
{
	private $_idResponsableAvance;
	private $_idRendicionAvance;
	private $_partida;
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
	public function GetPartida(){
		return $this->_partida;
	}
	public function SetPartida(EntidadPartida $partida = null){
		$this->_partida = $partida;
	}
	public function GetMonto(){
		return $this->_monto;
	}
	public function SetMonto($monto){
		$this->_monto = $monto;
	}
	public function __clone(){
		$this->_partida = ($this->_partida != null) ? clone $this->_partida : null;
	}
	public function UTF8Encode()
	{
		$this->_idResponsableAvance =  utf8_encode($this->_idResponsableAvance);
		$this->_idRendicionAvance =  utf8_encode($this->_idRendicionAvance);
		if($this->_partida != null) $this->_partida->UTF8Encode();
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
				'partida' => ($this->_partida != null) ? $this->_partida->ToArray() : null,
				'monto' => $this->_monto
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array()){
		return  json_encode($this->ToArray());
	}
}