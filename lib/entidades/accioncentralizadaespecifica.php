<?php
class EntidadAccionCentralizadaEspecifica
{
/*
acce_id character varying(15) NOT NULL, -- FK codigo de la accion central que utiliza la accion especifica
pres_anno smallint NOT NULL, -- FK año de la accion central que utiliza la accion especifica
aces_id character varying(15) NOT NULL, -- codigo de la accion especifica
aces_fecha_ini date, -- fecha de inicio de la accion especifica
aces_fecha_fin date, -- fecha final de la accion especifica
aces_nombre character varying(150), -- Nombre de la accion especifica
centro_gestor character varying(5) DEFAULT '0'::character varying,
centro_costo character varying(5) DEFAULT '0'::character varying,
*/
  
	// clave primaria (id, idproyecto, anho)
	private $_id;
	private $_idAccionCentralizada;
	private $_anho;
	private $_nombre;
	private $_centroGestor;
	private $_centroCosto;
	
	public function __construct()
	{
		
	}
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetIdAccionCentralizada(){
		return $this->_idAccionCentralizada;
	}
	public function SetIdAccionCentralizada($idAccionCentralizada){
		$this->_idAccionCentralizada = $idAccionCentralizada;
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
	public function GetCentroGestor(){
		return $this->_centroGestor;
	}
	public function SetCentroGestor($centroGestor){
		$this->_centroGestor = $centroGestor;
	}
	public function GetCentroCosto(){
		return $this->_centroCosto;
	}
	public function SetCentroCosto($centroCosto){
		$this->_centroCosto = $centroCosto;
	}
	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_idAccionCentralizada = utf8_encode($this->_idAccionCentralizada);
		$this->_anho = utf8_encode($this->_anho);
		$this->_nombre = utf8_encode($this->_nombre);
		$this->_centroGestor = utf8_encode($this->_centroGestor);
		$this->_centroCosto = utf8_encode($this->_centroCosto);

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
				'idAccionCentralizada' =>  $this->_idAccionCentralizada,
				'anho' => $this->_anho,
				'nombre' => $this->_nombre,
				'centroGestor' => $this->_centroGestor,
				'centroCosto' => $this->_centroCosto
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
	
}