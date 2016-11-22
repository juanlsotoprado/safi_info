<?php
class EntidadTipoSolicitudPago
{	
	private $_id;  // Código de la dependencia
	private $_nombre;  // Nombre completo de la dependencia
	private $_idMultComp;  
	private $_idEstatus;  // Estado del Recurso
	
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
	public function GetIdEstatus(){
		return $this->_idEstatus;
	}
	public function SetIdEstatus($idEstatus){
		$this->_idEstatus = $idEstatus;
	}
	
	public function GetIdMultComp(){
		return $this->_idMultComp;
	}
	
	public function SetIdMultComp($idMultComp){
		$this->_idMultComp = $idMultComp;
	}
	
	
	
	public function __toString()
	{
		return "
			Id = ".$this->_id.",
			Nombre = ".$this->_nombre.",
			IdEstatus = ".$this->_idEstatus.",
			IdMultComp = ".$this->_idEstatus."
		";
	}
	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_nombre = utf8_encode($this->_nombre);
		$this->_idEstatus = utf8_encode($this->_idEstatus);
		$this->_idMultComp = utf8_encode($this->_idMultComp);
		
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
				'nombre'=>$this->_nombre,
				'idEstatus' => $this->_idEstatus,
			      'idMultComp' => $this->_idMultComp
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}