<?php
class EntidadEmpleado
{
	private $_id; // Cédula
	private $_nombres;
	private $_apellidos;
	
	public function __construct(){
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetNombres(){
		return $this->_nombres;
	}
	public function SetNombres($nombres){
		$this->_nombres = $nombres;
	}
	public function GetApellidos(){
		return $this->_apellidos;
	}
	public function SetApellidos($apellidos){
		$this->_apellidos = $apellidos;
	}
	public function __toString()
	{
		return  "
			Id = ".$this->_id.",
			Nombres = ".$this->_nombres.",
			Apellidos = ".$this->_apellidos."
		";
	}
	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_nombres = utf8_encode($this->_nombres);
		$this->_apellidos = utf8_encode($this->_apellidos);
		
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
				'nombres' =>  $this->_nombres,
				'apellidos' => $this->_apellidos
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}