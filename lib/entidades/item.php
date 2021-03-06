<?php
class EntidadItem
{
	const TIPO_MATERIAL = 1;
	const TIPO_BIEN = 2;
	
	private $_id = '';
	private $_nombre = '';

	public function __construct(){}
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetNombre(){
		return $this->_nombre;
	}
	public function SetNombre($nombre){
		$this->_nombre = $nombre;
	}
	public function __toString()
	{
		return "
			Id = ".$this->GetId().",
			Nombre = " . $this->GetNombre() . "
		";
	}
	public function __clone()
	{		
	}
	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_nombre = utf8_encode($this->_nombre);
		
		return $this;
	}
	public function ToArray($properties = array())	{
		$data = array();
		if(is_array($properties) && count($properties) > 0){
			foreach($properties as $property){
				$nameProperty = '_' . $property;
				$data[$property] = $this->$property;
			}
		}else{
			$data = array(
				'id' => $this->_id,
				'nombre' => $this->_nombre
			);
		}
		return $data;
	}
	public function ToJson($properties = array()){
		return  json_encode($this->ToArray());
	}
}