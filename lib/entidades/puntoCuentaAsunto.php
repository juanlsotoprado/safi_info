<?php

require_once (SAFI_ENTIDADES_PATH.'/estatus.php');

class EntidadPuntoCuentaAsunto
{
	private $_id; // Código del asunto del punto de cuenta (clave primaria)
	private $_nombre; // Nombre del asunto
	private $_descripcion; // Descripción del asunto
	//private tipoCadena; // Tipo de cadena según asunto
	private $_estatus;
	
	public function __construct(){
	}
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
	public function GetEstatus(){
		return $this->_estatus;
	}
	public function SetEstatus(EntidadEstatus $estatus = null){
		$this->_estatus = $estatus;
	}
	public function __toString()
	{
		return "
			Id = ".$this->_id.",
			Nombre = ".$this->_nombre.",
			Descripcion = ".$this->_descripcion.",
			Estatus = ".($this->_estatus !== null ? $this->_estatus : "NULL")."
		";
	}
	public function __clone()
	{
		$this->_estatus = ($this->_estatus !== null) ? clone $this->_estatus : null;
	}
	public function UTF8Encode(){
		$this->_id = utf8_encode($this->_id);
		$this->_nombre = utf8_encode($this->_nombre);
		$this->_descripcion = utf8_encode($this->_descripcion);
		if($this->_estatus !== null) $this->_estatus->UTF8Encode();
		
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
				'descripcion' => $this->_descripcion,
				'estatus' => ($this->_estatus !== null) ? $this->_estatus->ToArray() : null
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}