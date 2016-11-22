<?php
require_once(SAFI_ENTIDADES_PATH . '/estado.php');
require_once(SAFI_ENTIDADES_PATH . '/ciudad.php');
require_once(SAFI_ENTIDADES_PATH . '/municipio.php');
require_once(SAFI_ENTIDADES_PATH . '/parroquia.php');

class EntidadRutaAvance
{
	private $_id;
	private $_idAvance;
	private $_estado;
	private $_ciudad;
	private $_municipio;
	private $_parroquia;
	private $_direccion;
	
	public function __construct(){
	
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetIdAvance(){
		return $this->_idAvance;
	}
	public function SetIdAvance($idAvance){
		$this->_idAvance = $idAvance;
	}
	public function GetEstado(){
		return $this->_estado;
	}
	public function SetEstado($estado){
		$this->_estado = $estado;
	}
	public function GetCiudad(){
		return $this->_ciudad;
	}
	public function SetCiudad($ciudad){
		$this->_ciudad = $ciudad;
	}
	public function GetMunicipio(){
		return $this->_municipio;
	}
	public function SetMunicipio($municipio){
		$this->_municipio = $municipio;
	}
	public function GetParroquia(){
		return $this->_parroquia;
	}
	public function SetParroquia($parroquia){
		$this->_parroquia = $parroquia;
	}
	public function GetDireccion(){
		return $this->_direccion;
	}
	public function SetDireccion($direccion){
		$this->_direccion = $direccion;
	}
	public function __clone()
	{
		$this->_estado = ($this->_estado != null) ? clone $this->_estado : null;
		$this->_ciudad = ($this->_ciudad != null) ? clone $this->_ciudad : null;
		$this->_municipio = ($this->_municipio != null) ? clone $this->_municipio : null;
		$this->_parroquia = ($this->_parroquia != null) ? clone $this->_parroquia : null;
		
	}
	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_idAvance = utf8_encode($this->_idAvance);
		if($this->_estado != null) $this->_estado->UTF8Encode();
		if($this->_ciudad != null) $this->_ciudad->UTF8Encode();
		if($this->_municipio != null) $this->_municipio->UTF8Encode();
		if($this->_parroquia != null) $this->_parroquia->UTF8Encode();
		$this->_direccion = utf8_encode($this->_direccion);
		
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
				'idAvance' => $this->_idAvance,
				'estado' => ($this->_estado != null) ? $this->_estado->ToArray() : null,
				'ciudad' => ($this->_ciudad != null) ? $this->_ciudad->ToArray() : null,
				'municipio' => ($this->_municipio != null) ? $this->_municipio->ToArray() : null,
				'parroquia' => ($this->_parroquia != null) ? $this->_parroquia->ToArray() : null,
				'direccion' => $this->_direccion
			);
		}
		return $data;
	}
	public function ToJson($properties = array()){
		return json_encode($this->ToArray());
	}
	/*
	public function Get(){
		return $this->_;
	}
	public function Set($){
		$this->_ = $;
	}
	*/
}