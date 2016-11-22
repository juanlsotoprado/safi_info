<?php
require_once(SAFI_ENTIDADES_PATH . '/estado.php');

class EntidadMunicipio
{
	private $_id;
	private $_nombre;
	private $_idEstado;
	private $_estatusActividad;
	private $_estado;
	
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
	public function GetIdEstado(){
		return $this->_idEstado;
	}
	public function SetIdEstado($idEstado){
		$this->_idEstado = $idEstado;
	}
	public function GetEstatusActividad(){
		return $this->_estatusActividad;
	}
	public function SetEstatusActividad($estatusActividad){
		$this->_estatusActividad = $estatusActividad;
	}
	public function GetEstado(){
		return $this->_estado;
	}
	public function SetEstado(EntidadEstado $estado){
		$this->_estado = $estado;
	} 
	public function __toString(){
		return "
			Id = ".$this->_id.",
			Nombre = ".$this->_nombre.",
			IdEstado = ".$this->_idEstado.",
			EstatusActividad = ".$this->_estatusActividad.",
			Estado = ".($this->_estado !== null ? $this->_estado : "NULL")."
		";
	}
	public function __clone()
	{
		$this->_estado = ($this->_estado != null) ? clone $this->_estado : null;
	}
	
	public function UTF8Encode(){
		$this->_id = utf8_encode($this->_id);
		$this->_nombre = utf8_encode($this->_nombre);
		$this->_idEstado = utf8_encode($this->_idEstado);
		$this->_estatusActividad = utf8_encode($this->_estatusActividad);
		$this->_estado = ($this->_estado != null ? $this->_estado->UTF8Encode() : null);
		
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
				'idEstado' => $this->_idEstado,
				'estatusActividad' => $this->_estatusActividad,
				'estado' => ($this->_estado != null ? $this->_estado->ToArray() : null)
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