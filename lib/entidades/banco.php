<?php
class EntidadBanco
{

	private $_id; // Identificador del banco en la tabla
	private $_nombre; // Nombre del banco
	private $_sitioWeb; // Sitio web del banco
	private $_idEstatus; // Estatus del banco
	private $_usuaLogin; // Login del Usuario
	
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
	public function GetSitioWeb(){
		return $this->_sitioWeb;
	}
	public function SetSitioWeb($sitioWeb){
		$this->_sitioWeb = $sitioWeb;
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
	public function __toString()
	{
		return "
			id = " . $this->GetId() . "
			nombre = " . $this->GetNombre() . "
			sitioWeb = " . $this->GetSitioWeb() . "
			idEstatus = " . $this->GetIdEstatus() . "
			usuaLogin = " . $this->GetUsuaLogin() . "
		";
	}
	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_nombre = utf8_encode($this->_nombre);
		$this->_sitioWeb = utf8_encode($this->_sitioWeb);
		$this->_idEstatus = utf8_encode($this->_idEstatus);
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
				'id' => $this->_id,
				'nombre' => $this->_nombre,
				'sitioWeb' => $this->_sitioWeb,
				'idEstatus' => $this->_idEstatus,
				'usuaLogin' => $this->_usuaLogin
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
	
}