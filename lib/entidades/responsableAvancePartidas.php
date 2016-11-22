<?php
require_once(SAFI_ENTIDADES_PATH . '/responsableAvance.php');
require_once(SAFI_ENTIDADES_PATH . '/avancePartida.php');

class EntidadResponsableAvancePartidas
{
	private $_responsableAvance;
	private $_avancePartidas;
	private $_montoTotal = null;
	
	public function __construct(){
	
	}
	public function GetResponsableAvance(){
		return $this->_responsableAvance;
	}
	public function SetResponsableAvance(EntidadResponsableAvance $responsableAvance = null){
		$this->_responsableAvance = $responsableAvance;
	}
	public function GetAvancePartidas(){
		return $this->_avancePartidas;
	}
	public function SetAvancePartidas(array $avancePartidas = null){
		$this->_avancePartidas = $avancePartidas;
		$this->_montoTotal = null;
	}
	public function GetMontoTotal(){
		if ($this->_montoTotal == null)
			$this->__CarculaMontoTotal();
			
		return $this->_montoTotal;
	}
	private function __CarculaMontoTotal(){
		$montoTotal = 0;
		// Verificar que existen partidas/montos para el responsable 
		if(is_array($this->_avancePartidas)){
			foreach ($this->_avancePartidas as $avancePartida)
			{
				$monto = $avancePartida->GetMonto();
				if ($monto != null && trim($monto) != ''){
					$montoTotal += $monto; 
				}
			}
		}
		$this->_montoTotal = $montoTotal;
	}
	public function __clone(){
		$avancePartidas = null;
		if(is_array($this->_avancePartidas)){
			$avancePartidas = array();
			foreach($this->_avancePartidas as $avancePartida){
				$avancePartidas[] = clone $avancePartida;
			}
		}
		
		$this->_responsableAvance = ($this->_responsableAvance != null) ? clone $this->_responsableAvance : null;
		$this->_avancePartidas = $avancePartidas;
	}
	public function UTF8Encode()
	{
		if(is_array($this->_avancePartidas)){
			foreach($this->_avancePartidas as &$avancePartida){
				$avancePartida->UTF8Encode();
			}
			unset($avancePartida);
		}
		
		if($this->_responsableAvance != null) $this->_responsableAvance->UTF8Encode();
		$this->_montoTotal = utf8_encode($this->_montoTotal);
		
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
			$avancePartidas = null;
			if(is_array($this->_avancePartidas)){
				$avancePartidas = array();
				foreach($this->_avancePartidas as $avancePartida){
					$avancePartidas[] = $avancePartida->ToArray(); 
				}
			}
			
			$data = array(
				'responsableAvance' => ($this->_responsableAvance != null) ? $this->_responsableAvance->ToArray() : null,
				'avancePartidas' => $avancePartidas,
				'montoTotal' => $this->_montoTotal
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}