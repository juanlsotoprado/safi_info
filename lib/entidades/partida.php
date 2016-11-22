<?php

class EntidadPartida
{
	private $_id; // Código de la partida, por ejemplo 4.01
	private $_anho;  // plan de cuentas relacionado con la partida
	private $_nombre;  // Nombre de la partida segun el plan de cuentas
	private $_especial; // Categorizacion de la partida como especial.
	private $_observaciones; // Observaciones de modificación de las  partida
	private $_usuaLogin; // Usuario que registra las partidas
	private $_idEstatus; // Estado del recurso 1:activo 2:inactivo
	private $_regular; // Campo define si la partida se oculta o no
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetAnho(){
		return $this->_anho;
	}
	public function SetAnho($anho){
		$this->_anho = $anho;
	}
	public function GetNombre(){
		return $this->_nombre;
	}
	public function SetNombre($nombre){
		$this->_nombre = $nombre;
	}
	public function GetEspecial(){
		return $this->_especial;
	}
	public function SetEspecial($especial){
		$this->_especial = $especial;
	}
	public function GetObservaciones(){
		return $this->_observaciones;
	}
	public function SetObservaciones($observaciones){
		$this->_observaciones = $observaciones;
	}
	public function GetUsuaLogin(){
		return $this->_usuaLogin;
	}
	public function SetUsuaLogin($usuaLogin){
		$this->_usuaLogin = $usuaLogin;
	}
	public function GetIdEstatus(){
		return $this->_idEstatus;
	}
	public function SetIdEstatus($idEstatus){
		$this->_idEstatus = $idEstatus;
	}
	public function GetRegular(){
		return $this->_regular;
	}
	public function SetRegular($regular){
		$this->_regular = $regular;
	}
	
	public function UTF8Encode(){
		$this->_id = utf8_encode($this->_id);
		$this->_anho = utf8_encode($this->_anho);
		$this->_nombre = utf8_encode($this->_nombre);
		$this->_observaciones = utf8_encode($this->_observaciones);
		$this->_usuaLogin = utf8_encode($this->_usuaLogin);
		$this->_idEstatus = utf8_encode($this->_idEstatus);
		
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
				'anho' =>  $this->_anho,
				'nombre' => $this->_nombre,
				'especial' => $this->_especial,
				'observaciones' => $this->_observaciones,
				'usuaLogin' => $this->_usuaLogin,
				'idEstatus' => $this->_idEstatus,
				'regular' => $this->_regular
			);
		}
		
		return $data;
	}
	
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}