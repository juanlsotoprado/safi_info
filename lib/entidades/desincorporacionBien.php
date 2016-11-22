<?php

require_once(SAFI_ENTIDADES_PATH . '/desincorporacionItem.php');

class EntidadDesincorporacionBien
{
	private $_id;
	private $_loginUsuario;
	private $_observaciones;
	private $_fecha;
	private $_idEstatus;
	private $_depeId;
	private $_items;
	
	
	public function __construct(){
		
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetLoginUsuario(){
		return $this->_loginUsuario;
	}
	public function SetLoginUsuario($loginUsuario){
		$this->_loginUsuario = $loginUsuario;
	}
	public function GetObservaciones(){
		return $this->_observaciones;
	}
	public function SetObservaciones($observaciones){
		$this->_observaciones = $observaciones;
	}
	public function GetFechaActa(){
		return $this->_fecha;
	}
	public function SetFechaActa($fecha){
		$this->_fecha = $fecha;
	}
	public function GetIdEstatus(){
		return $this->_idEstatus;
	}
	public function SetIdEstatus($idEstatus){
		$this->_idEstatus = $idEstatus;
	}
	public function GetDepeId(){
		return $this->_depeId;
	}
	public function SetDepeId($depeId){
		$this->IdEstatus = $depeId;
	}
	public function GetItems(){
		return $this->_items;
	}
	public function SetItems(array $items = null){
		$this->_items = $items;
	}
	
	public function __toString()
	{
		
		
		$idItems = array();
		if(is_array($this->_items))
		{
			foreach ($this->_items as $item)
			{
				$idItems[] = $item->GetId();
			}
		}
		

		return "
			Id => ".$this->GetId().",
			LoginUsuario = ".$this->_loginUsuario.",
			Observaciones = ".$this->_observaciones.",
			Fecha = ".$this->_fecha.",
			IdEstatus = ".$this->_idEstatus.",
			DepeId = ".$this->_depeId."
		";
	}
	public function __clone(){
		//$this->_parroquia = $this->_parroquia !== null ? clone $this->_parroquia : null;		
	}
	public function UTF8Encode()
	{
		$this->id = utf8_encode($this->id);
		$this->_loginUsuario = utf8_encode($this->_loginUsuario);
		$this->_observaciones = utf8_encode($this->_observaciones);
		$this->_fecha = utf8_encode($this->_fecha);
		$this->_idEstatus = utf8_encode($this->_idEstatus);
		$this->_depeId = utf8_encode($this->_depeId);
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
				//"id" => $this->GetId()
				'id' => $this->_id,
				'loginUsuario' => $this->_loginUsuario,
				'observaciones' => $this->_observaciones,
				'fecha' => $this->_fecha,
				'idEstatus' => $this->_idEstatus,
				'depeId' => $this->_depeId
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return json_encode($this->ToArray());
	}
}
?>