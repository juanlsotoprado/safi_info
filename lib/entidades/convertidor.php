<?php
class EntidadConvertidor
{
	private $_partida; // Entidad partida
	private $_cuentaContable; // Entidad cuenta contable
	private $_pasivo; // Entidad cuenta contable	
	
	public function __construct(){
		
	}
	public function GetPartida(){
		return $this->_partida;
	}
	public function SetPartida(EntidadPartida $partida = null){
		$this->_partida = $partida;
	}
	public function GetCuentaContable(){
		return $this->_cuentaContable;
	}
	public function SetCuentaContable(EntidadCuentaContable $cuentaContable = null){
		$this->_cuentaContable = $cuentaContable;
	}
	public function GetPasivo(){
		return $this->_pasivo;
	}
	public function SetPasivo(EntidadCuentaContable $cuentaContable = null){
		$this->_pasivo = $cuentaContable;
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
					'cuentaContable' => (is_object($this->_cuentaContable) ? $this->_cuentaContable->ToArray() : null),
					'pasivo' => (is_object($this->_pasivo) ? $this->_pasivo->ToArray() : null),					
					'partida' => (is_object($this->_partida) ? $this->_partida->ToArray() : null)
			);
		}
	
		return $data;
	}

	public function UTF8Encode()
	{
		if(is_object($this->_cuentaContable)) $this->_cuentaContable->UTF8Encode();
		if(is_object($this->_pasivo)) $this->_pasivo->UTF8Encode();
		if(is_object($this->_partida)) $this->_partida->UTF8Encode();		
	
		return $this;
	}	
	
	
}