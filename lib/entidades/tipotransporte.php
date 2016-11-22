<?php
class EntidadTipoTransporte
{
	const TIPO_AEREO = 1;
	const TIPO_MARITIMO = 2;
	const TIPO_TERRESTRE = 3;
	
	private $_id;
	private $_tipo;
	private $_nombre;
	private $_estatusActividad;
	
	public function __construct()
	{
		
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetTipo(){
		return $this->_tipo;
	}
	public function SetTipo($tipo){
		$this->_tipo = $tipo;
	}
	public function GetNombre(){
		return $this->_nombre;
	}
	public function SetNombre($nombre){
		$this->_nombre = $nombre;
	}
	public function GetEstatusActividad(){
		return $this->_estatusActividad;
	}
	public function SetEstatusActividad($estatusActividad){
		$this->_estatusActividad = $estatusActividad;
	}
	
}