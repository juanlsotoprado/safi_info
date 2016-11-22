<?php
class EntidadProyecto
{
/*
	 proy_id character varying(15) NOT NULL, -- codigo de identificacion del proyecto
	proy_titulo character varying(200) NOT NULL, -- titulo del proyecto
	proy_desc character varying(580) NOT NULL, -- descripcion del proyecto
	proy_resultado character varying(580), -- resultado del proyecto
	proy_obj character varying(580) NOT NULL, -- objetivos del proyecto
	pre_anno smallint NOT NULL, -- Fk an_o del presupuesto
	esta_id integer NOT NULL, -- Fk codigo del estado
	proy_observa character varying(200), -- Observaciones que se realizan cuando se modifica un proyecto
	usua_login character varying(15) NOT NULL, -- Usuario que registra proyecto
	usua_log_resp character varying(15), -- Login responsable proyectos.
	proy_cod_onapre character varying(30),
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