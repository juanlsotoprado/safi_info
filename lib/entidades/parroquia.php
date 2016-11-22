<?php
require_once(SAFI_ENTIDADES_PATH . '/municipio.php');

class EntidadParroquia
{
	private $_id;
	private $_nombre;
	private $_idMunicipio;
	private $_estatusActividad;
	private $_municipio;
	
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
	public function GetIdMunicipio(){
		return $this->_idMunicipio;
	}
	public function SetIdMunicipio($idMunicipio){
		$this->_idMunicipio = $idMunicipio;
	}
	public function GetEstatusActividad(){
		return $this->_estatusActividad;
	}
	public function SetEstatusActividad($estatusActividad){
		$this->_estatusActividad = $estatusActividad;
	}
	public function GetMunicipio(){
		return $this->_municipio;
	}
	public function SetMunicipio(EntidadMunicipio $municipio){
		$this->_municipio = $municipio;
	}
	public function __toString(){
		return "
			Id = ".$this->_id.",
			Nombre = ".$this->_nombre.",
			IdMunicipio = ".$this->_idMunicipio.",
			EstatusActividad = ".$this->_estatusActividad.",
			Municipio = ".($this->_municipio !== null ? $this->_municipio : "NULL")."
		";
	}
	public function __clone()
	{
		$this->_municipio = ($this->_municipio != null) ? clone $this->_municipio : null;
	}
	public function UTF8Encode(){
		$this->_id = utf8_encode($this->_id);
		$this->_nombre = utf8_encode($this->_nombre);
		$this->_idMunicipio = utf8_encode($this->_idMunicipio);
		$this->_estatusActividad = utf8_encode($this->_estatusActividad);
		$this->_municipio = ($this->_municipio != null ? $this->_municipio->UTF8Encode() : null);
		
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
				'idMunicipio' => $this->_idMunicipio,
				'estatusActividad' => $this->_estatusActividad,
				'municipio' => ($this->_municipio != null ? $this->_municipio->ToArray() : null)
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}