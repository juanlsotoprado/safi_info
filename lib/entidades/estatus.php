<?php

class EntidadEstatus
{
	const ESTATUS_APROBADO = 13;
	const ESTATUS_ANULADO = 15;
	
	private $_id;
	private $_nombre;
	private $_descripcion;
	private $_usuaLogin;
	
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
	public function GetDescripcion(){
		return $this->_descripcion;
	}
	public function SetDescripcion($descripcion){
		$this->_descripcion = $descripcion;
	}
	public function GetUsuaLogin(){
		return $this->_usuaLogin;
	}
	public function SetUsuaLogin($usuaLogin){
		$this->_usuaLogin = $usuaLogin;
	}
	public function __toString(){
		return "
			id = " . $this->GetId() . ",
			nombre = " . $this->GetNombre() . ",
			descripcion = " . $this->GetDescripcion() . ",
			usuaLogin = " . $this->GetUsuaLogin() . "
		";
	}
	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_nombre = utf8_encode($this->_nombre);
		$this->_descripcion = utf8_encode($this->_descripcion);
		$this->_usuaLogin = utf8_encode($this->_usuaLogin);
		
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
				'id' => $this->GetId(),
				'nombre' => $this->GetNombre(),
				'descripcion' => $this->GetDescripcion(),
				'usuaLogin' => $this->GetUsuaLogin()
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}