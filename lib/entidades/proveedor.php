<?php
class EntidadProveedor{
	private $_rif = '';
	private $_nombre = '';

	public function __construct(){}
	
	public function GetRif(){
		return $this->_rif;
	}
	public function SetRif($rif){
		$this->_rif = $rif;
	}
	public function GetNombre(){
		return $this->_nombre;
	}
	public function SetNombre($nombre){
		$this->_nombre = $nombre;
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
				'rif' => $this->_rif,
				'nombre' => $this->_nombre
			);
		}
		return $data;
	}
	public function ToJson($properties = array()){
		return  json_encode($this->ToArray());
	}
}