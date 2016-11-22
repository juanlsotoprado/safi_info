<?php

class EntidadEstado
{
	private $_id;
	private $_nombre;
	private $_estatusActividad;
	
	public function __construct()
	{
		
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = (int)$id;
	}
	public function GetNombre(){
		return $this->_nombre;
	}
	public function SetNombre($nombre){
		$this->_nombre = $nombre;
	}
	public function GetEstatusActividad(){
		return $this->_estatusActividad;
	}
	public function SetEstatusActividad($estatusActividad){
		$this->_estatusActividad = $estatusActividad;
	}
	public function __toString(){
		return "
			Id = ".$this->_id.",
			Nombre = ".$this->_nombre.",
			EstatusActividad = ".$this->_estatusActividad."
		";
	}
	public function UTF8Encode(){
		$this->_id = utf8_encode($this->_id);
		$this->_nombre = utf8_encode($this->_nombre);
		$this->_estatusActividad = utf8_encode($this->_estatusActividad);
		
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
				'nombre' => $this->_nombre,
				'estatusActividad' => $this->_estatusActividad
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}