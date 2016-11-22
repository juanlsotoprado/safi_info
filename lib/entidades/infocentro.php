<?php
require_once(SAFI_ENTIDADES_PATH . '/parroquia.php');

class EntidadInfocentro
{
	private $_id = 0;
	private $_nombre;
	private $_direccion;
	private $_anho;
	private $_idParroquia = 0;
	private $_estatusActividad;
	private $_idEstatus = 0;
	private $_nemotecnico;
	private $_etapa;
	private $_parroquia;
	
	public function __construct()
	{
		
	}
	public function GetId()
	{
		return $this->_id;
	}
	public function SetId($id)
	{
		$this->_id = $id;
	}
	public function GetNombre()
	{
		return $this->_nombre;
	}
	public function SetNombre($nombre)
	{
		$this->_nombre = $nombre;
	}
	public function GetDireccion()
	{
		return $this->_direccion;
	}
	public function SetDireccion($direccion)
	{
		$this->_direccion = $direccion;
	}
	public function GetAnho()
	{
		return $this->_anho;
	}
	public function SetAnho($anho)
	{
		$this->_anho = $anho;
	}
	public function GetIdParroquia()
	{
		return $this->_idParroquia;
	}
	public function SetIdParroquia($idParroquia)
	{
		$this->_idParroquia = (int)$idParroquia;
	}
	public function GetEstatusActividad()
	{
		return $this->_estatusActividad;
	}
	public function SetEstatusActividad($estatusActividad)
	{
		$this->_estatusActividad = $estatusActividad;
	}
	public function GetIdEstatus()
	{
		return $this->_idEstatus;
	}
	public function SetIdEstatus($idEstatus)
	{
		$this->_idEstatus = $idEstatus;
	}
	public function GetNemotecnico()
	{
		return $this->_nemotecnico;
	}
	public function SetNemotecnico($nemotecnico)
	{
		$this->_nemotecnico = $nemotecnico;
	}
	public function GetEtapa()
	{
		return $this->_etapa;
	}
	public function SetEtapa($etapa)
	{
		$this->_etapa = $etapa;
	}
	public function GetParroquia(){
		return $this->_parroquia;
	}
	public function SetParroquia(EntidadParroquia $parroquia = null){
		$this->_parroquia = $parroquia;
	}
	public function __toString()
	{
		return "
			Id = ".$this->_id.",
			Nombre = ".$this->_nombre.",
			Direccion = ".$this->_direccion.",
			Anho = ".$this->_anho.",
			IdParroquia = ".$this->_idParroquia.",
			EstatusActividad = ".$this->_estatusActividad.",
			IdEstatus = ".$this->_idEstatus.",
			Nemotecnico = ".$this->_nemotecnico.",
			Etapa = ".$this->_etapa."
			Parroquia = ".($this->_parroquia !== null ? $this->_parroquia : "NULL")."
		";
	}
	public function __clone(){
		$this->_parroquia = $this->_parroquia !== null ? clone $this->_parroquia : null;
	}
	public function UTF8Encode(){
		$this->_id = utf8_encode($this->_id);
		$this->_nombre = utf8_encode($this->_nombre);
		$this->_direccion = utf8_encode($this->_direccion);
		$this->_anho = utf8_encode($this->_anho);
		$this->_idParroquia = utf8_encode($this->_idParroquia);
		$this->_estatusActividad = utf8_encode($this->_estatusActividad);
		$this->_idEstatus = utf8_encode($this->_idEstatus);
		$this->_nemotecnico = utf8_encode($this->_nemotecnico);
		$this->_etapa = utf8_encode($this->_etapa);
		$this->_parroquia = ($this->_parroquia != null ? $this->_parroquia->UTF8Encode() : null);
		
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
				'nombre' =>  $this->_nombre,
				'direccion' => $this->_direccion,
				'anho' => $this->_anho,
				'idParroquia' => $this->_idParroquia,
				'estatusActividad' => $this->_estatusActividad,
				'idEstatus' => $this->_idEstatus,
				'nemotecnico' => $this->_nemotecnico,
				'etapa' => $this->_etapa,
				'parroquia' => ($this->_parroquia != null ? $this->_parroquia->ToArray() : null)
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}