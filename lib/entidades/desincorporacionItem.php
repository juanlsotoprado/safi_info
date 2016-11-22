<?php

class EntidadDesincorporacionItem
{
	private $_id;
	private $_artiId;
	private $_modelo;
	private $_marcaId;
	private $_ubicacion;
	private $_precio;
	private $_serial;
	
	
	public function __construct(){
		
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetArtiId(){
		return $this->_artiId;
	}
	public function SetArtiId($artiId){
		$this->_artiId = $artiId;
	}
	public function GetModelo(){
		return $this->_modelo;
	}
	public function SetModelo($modelo){
		$this->_modelo = $modelo;
	}
	public function GetMarcaId(){
		return $this->_marcaId;
	}
	public function SetMarcaId($marcaId){
		$this->_marcaId = $marcaId;
	}
	public function GetUbicacion(){
		return $this->_ubicacion;
	}
	public function SetUbicacion($ubicacion){
		$this->_ubicacion = $ubicacion;
	}
	public function GetPrecio(){
		return $this->_precio;
	}
	public function SetPrecio($precio){
		$this->_precio = $precio;
	}
	public function GetSerial(){
		return $this->_serial;
	}
	public function SetSerial($serial){
		$this->_serial = $serial;
	}
	
	public function __toString()
	{
		return "
			Id => ".$this->_id."
			ArtiId = ".$this->_artiId.",
			Modelo = ".$this->_modelo.",
			MarcaId = ".$this->_marcaId.",
			Ubicacion = ".$this->_ubicacion.",
			Precio = ".$this->_precio.",
			Serial = ".$this->_serial."
		";
	}
	public function __clone(){
		
	}
	public function UTF8Encode()
	{
		$this->id = utf8_encode($this->id);
		$this->_artiId = utf8_encode($this->_artiId);
		$this->_modelo = utf8_encode($this->_modelo);
		$this->_marcaId = utf8_encode($this->_marcaId);
		$this->_ubicacion = utf8_encode($this->_ubicacion);
		$this->_precio = utf8_encode($this->_precio);
		$this->_serial = utf8_encode($this->_serial);
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
				'id' => $this->id,
				'artiId' => $this->_artiId,
				'modelo' => $this->_modelo,
				'marcaId' => $this->_marcaId,
				'ubicacion' => $this->_ubicacion,
				'precio' => $this->_precio,
				'serial' => $this->_serial
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return json_encode($this->ToArray());
	}
}
?>