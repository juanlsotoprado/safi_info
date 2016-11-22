<?php

class EntidadUsuario
{
	private $_id;
	private $_clave; // Corresponde a la contrasen_a del usuario
	private $_activo;  // Indica si el usuario esta activo o no...
	private $_cedula;
	private $_idDependencia;
	
	public function __construct(){
	
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetUsuaLogin(){
		return $this->_id;
	}
	public function SetUsuaLogin($usuaLogin){
		$this->_id = $usuaLogin;
	}
	public function GetClave(){
		return $this->_clave;
	}
	public function SetClave($clave){
		$this->_clave = $clave;
	}
	public function GetActivo(){
		return $this->_activo;
	}
	public function SetActivo($activo){
		$this->_activo = $activo;
	}
	public function GetCedula(){
		return $this->_cedula;
	}
	public function SetCedula($cedula){
		$this->_cedula = $cedula;
	}
	public function GetIdDependencia(){
		return $this->_idDependencia;
	}
	public function SetIdDependencia($idDependencia){
		$this->_idDependencia = $idDependencia;
	}
	public function __toString()
	{
		return "
			Id = ".$this->_id.",
			Clave = ".$this->_clave.",
			Activo = ".$this->_activo.",
			Cedula = ".$this->_cedula.",
			IdDepenedencia = ".$this->_idDependencia."
		";
	}
	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_clave = utf8_encode($this->_clave);
		$this->_activo = utf8_encode($this->_activo);
		$this->_cedula = utf8_encode($this->_cedula);
		$this->_idDependencia = utf8_encode($this->_idDependencia);
		
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
				'clave' => $this->_clave,
				'activo' => $this->_activo,
				'cedula' => $this->_cedula,
				'idDependencia' => $this->_idDependencia
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}