<?php
require_once(SAFI_ENTIDADES_PATH . '/responsableRendicionAvance.php');
require_once(SAFI_ENTIDADES_PATH . '/rendicionAvancePartida.php');
require_once(SAFI_ENTIDADES_PATH . '/rendicionAvanceReintegro.php');

class EntidadResponsableRendicionAvancePartidas
{
	private $_responsableRendicionAvance;
	private $_rendicionAvancePartidas;
	private $_rendicionAvanceReintegros;
	private $_montoAnticipo = 0;
	private $_montoTotal = null;
	private $_montoReintegrado = null;
	
	public function __construct(){
		$this->_rendicionAvanceReintegros = array(new EntidadRendicionAvanceReintegro());
	}
	public function GetResponsableRendicionAvance(){
		return $this->_responsableRendicionAvance;
	}
	public function SetResponsableRendicionAvance(EntidadResponsableRendicionAvance $responsableRendicionAvance = null){
		$this->_responsableRendicionAvance = $responsableRendicionAvance;
	}
	public function GetRendicionAvancePartidas(){
		return $this->_rendicionAvancePartidas;
	}
	public function SetRendicionAvancePartidas(array $rendicionAvancePartidas = null){
		$this->_rendicionAvancePartidas = $rendicionAvancePartidas;
		$this->_montoTotal = null;
	}
	public function GetRendicionAvanceReintegros(){
		return $this->_rendicionAvanceReintegros;
	}
	public function SetRendicionAvanceReintegros(array $rendicionAvanceReintegros = null){
		$this->_rendicionAvanceReintegros = $rendicionAvanceReintegros;
	}
	public function GetMontoAnticipo(){
		return $this->_montoAnticipo;
	}
	public function SetMontoAnticipo($montoAnticipo){
		$this->_montoAnticipo = $montoAnticipo;
	}
	public function GetMontoTotal(){
		if($this->_montoTotal == null)
			$this->__CalcularMontoTotal();
			
		return $this->_montoTotal;
	}
	private function __CalcularMontoTotal(){
		$montoTotal = 0;
		if(is_array($this->_rendicionAvancePartidas)){
			foreach ($this->_rendicionAvancePartidas as $rendicionAvancePartida)
			{
				$monto = $rendicionAvancePartida->GetMonto();
				if($monto != null && trim($monto) != ''){
					$montoTotal += $monto;
				} 
			}
		}
		$this->_montoTotal = $montoTotal;
	}
	public function GetMontoReintegrado()
	{
		if($this->_montoReintegrado == null)
		{
			$montoReintegrado = 0;
			if(is_array($this->_rendicionAvanceReintegros)){
				foreach ($this->_rendicionAvanceReintegros as $rendicionAvanceReintegro)
				{
					$monto = $rendicionAvanceReintegro->GetMonto();
					if($monto != null && trim($monto) != ''){
						$montoReintegrado += $monto;
					} 
				}
			}
			$this->_montoReintegrado = $montoReintegrado;
		}
		
		return $this->_montoReintegrado;
	}
	public function __clone(){
		$rendicionAvancePartidas = null;
		if(is_array($this->_rendicionAvancePartidas)){
			$rendicionAvancePartidas = array();
			foreach($this->_rendicionAvancePartidas as $rendicionAvancePartida){
				$rendicionAvancePartidas[] = clone $rendicionAvancePartida;
			}
		}
		
		$rendicionAvanceReintegros = null;
		if(is_array($this->_rendicionAvanceReintegros)){
			$rendicionAvanceReintegros = array();
			foreach ($this->_rendicionAvanceReintegros as $rendicionAvanceReintegro){
				$rendicionAvanceReintegros[] = clone $rendicionAvanceReintegro;
			}
		}
		
		$this->_responsableRendicionAvance = ($this->_responsableRendicionAvance != null) ? clone $this->_responsableRendicionAvance : null;
		$this->_rendicionAvancePartidas = $rendicionAvancePartidas;
		$this->_rendicionAvanceReintegros = $rendicionAvanceReintegros;
	}
	public function UTF8Encode()
	{
		if(is_array($this->_rendicionAvancePartidas)){
			foreach($this->_rendicionAvancePartidas as &$rendicionAvancePartida){
				$rendicionAvancePartida->UTF8Encode();
			}
			unset($rendicionAvancePartida);
		}
		
		if(is_array($this->_rendicionAvanceReintegros)){
			foreach ($this->_rendicionAvanceReintegros as &$rendicionAvanceReintegro){
				$rendicionAvanceReintegro->UTF8Encode();
			}
			unset($rendicionAvanceReintegro);
		}
		
		if($this->_responsableRendicionAvance != null) $this->_responsableRendicionAvance->UTF8Encode();
		$this->_montoAnticipo = utf8_encode($this->_montoAnticipo);
		$this->_montoTotal = utf8_encode($this->_montoTotal);
		$this->_montoReintegrado = utf8_encode($this->_montoReintegrado);
		
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
			$rendicionAvancePartidas = null;
			if(is_array($this->_rendicionAvancePartidas)){
				$rendicionAvancePartidas = array();
				foreach($this->_rendicionAvancePartidas as $rendicionAvancePartida){
					$rendicionAvancePartidas[] = $rendicionAvancePartida->ToArray(); 
				}
			}
			
			$rendicionAvanceReintegros = null;
			if(is_array($this->_rendicionAvanceReintegros)){
				$rendicionAvanceReintegros = array();
				foreach ($this->_rendicionAvanceReintegros as $rendicionAvanceReintegro){
					$rendicionAvanceReintegros[] = $rendicionAvanceReintegro->ToArray();
				}
			}
			
			$data = array(
				'responsableRendicionAvance' => 
					($this->_responsableRendicionAvance != null) ? $this->_responsableRendicionAvance->ToArray() : null,
				'rendicionAvancePartidas' => $rendicionAvancePartidas,
				'rendicionAvanceReintegros' => $rendicionAvanceReintegros,
				'montoAnticipo' => $this->_montoAnticipo,
				'montoTotal' => $this->_montoTotal,
				'montoReintegrado' => $this->_montoReintegrado
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}