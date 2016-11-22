<?php
class EntidadMemorandoAsunto{
	private $_id = '';
	private $_nombre = '';
	private $_descripcion = '';
	private $_estaId = 0;

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
	public function GetDescripcion(){
		return $this->_descripcion;
	}
	public function SetDescripcion($descripcion){
		$this->_descripcion = $descripcion;
	}
	public function GetEstaId(){
		return $this->_estaId;
	}
	public function SetEstaId($estaId){
		$this->_estaId = (int)$estaId;
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
				'nombre' =>  $this->_nombre,
				'descripcion' => $this->_descripcion,
				'estaId' => $this->_estaId
			);
		}
		return $data;
	}
	public function ToJson($properties = array()){
		return  json_encode($this->ToArray());
	}
}