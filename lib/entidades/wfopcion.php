<?php
class EntidadWFOpcion
{
	private $_id = 0; // Código de la Opción (clave primaria)
	private $_nombre = ''; // Nombre de la Opción
	private $_descripcion = ''; // Descripción de la Opción
	
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
	public function GetDescripcion(){
		return $this->_descripcion;
	}
	public function SetDescripcion($descripcion){
		$this->_descripcion = $descripcion;
	}
}