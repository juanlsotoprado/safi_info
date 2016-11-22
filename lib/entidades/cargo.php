<?php
class EntidadCargo
{
	private $_id = '0';
	private $_nombre; // Nombre del cargo
	private $_fundacion; // Denominacion MCT
	private $_descripcion; // Descripción del Cargo
	private $_nivel = 0;
	private $_idEstatus; // Estado del Recurso
	private $_usuaLogin;
	
	public function __construct()
	{
		
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
	public function GetFundacion(){
		return $this->_fundacion;
	}
	public function SetFundacion($fundacion){
		$this->_fundacion = $fundacion;
	}
	public function GetDescripcion(){
		return $this->_descripcion;
	}
	public function SetDescripcion($descripcion){
		$this->_descripcion = $descripcion;
	}
	public function GetNivel(){
		return $this->_nivel;
	}
	public function SetNivel($nivel){
		$this->_nivel = $nivel;
	}
	public function GetIdEstatus(){
		return $this->_idEstatus;
	}
	public function SetIdEstatus($idEstatus){
		$this->_idEstatus = $idEstatus;
	}
	public function GetUsuaLogin(){
		return $this->_usuaLogin;
	}
	public function SetUsuaLogin($usuaLogin){
		$this->_usuaLogin = $usuaLogin;
	}
}