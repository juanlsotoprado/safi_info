<?php
class EntidadAccionCentralizada
{
/*	 
acce_id character varying(15) NOT NULL, -- codigo de la accion centralizada
pres_anno smallint NOT NULL, -- an_o del presupuesto
acce_denom character varying(150), -- denominacion de la accion centralizada
esta_id integer NOT NULL, -- estado de la accion
acce_observa character varying(200), -- Observaciones que se realizan cuando se hace una modificacion
usua_login character varying(15),
acce_visib bit(1), --
*/
	
	// clave primaria (id, anho)
	private $_id;
	private $_anho;
	private $_nombre;
	
	public function __construct()
	{
		
	}
	
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
		$this->_anho = (int)$anho;
	}
	public function GetNombre(){
		return $this->_nombre;
	}
	public function SetNombre($nombre){
		$this->_nombre = $nombre;
	}
	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_anho = utf8_encode($this->_anho);
		$this->_nombre = utf8_encode($this->_nombre);
		
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
				'anho' => $this->_anho,
				'nombre' => $this->_nombre
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}