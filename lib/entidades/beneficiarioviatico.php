<?php
class EntidadBeneficiarioViatico
{
	private $_id;
	private $_nombres;
	private $_apellidos;
	private $_tipo;
	
	public function __construct()
	{
	
	}
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetNombres(){
		return $this->_nombres;
	}
	public function SetNombres($nombres){
		$this->_nombres = $nombres;
	}
	public function GetApellidos(){
		return $this->_apellidos;
	}
	public function SetApellidos($apellidos){
		$this->_apellidos = $apellidos;
	}
	public function GetTipo(){
		$this->_tipo;
	}
	public function SetTipo($tipo){
		$this->_tipo = $tipo;
	}
	
	public function UTF8Encode(){
		$this->_id = utf8_encode($this->_id);
		$this->_nombres = utf8_encode($this->_nombres);
		$this->_apellidos = utf8_encode($this->_apellidos);
		$this->_tipo = utf8_encode($this->_tipo);
		
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
				'nombres' =>  $this->_nombres,
				'apellidos' => $this->_apellidos,
				'tipo' => $this->_tipo
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
	
}
?>