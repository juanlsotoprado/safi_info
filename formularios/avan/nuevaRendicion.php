<?php

include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');
include_once(SAFI_ENTIDADES_PATH . "/avance.php");
include_once(SAFI_ENTIDADES_PATH . "/rendicionAvance.php");
include_once(SAFI_ENTIDADES_PATH . "/docgenera.php");

class NuevaRendicionAvanceForm extends Formularios
{
	const TIPO_OPERACION_INSERTAR = 1;
	const TIPO_OPERACION_MODIFICAR = 2;
	
	private $_idAvanceBuscado;
	private $_idAvance;
	private $_avance;
	private $_rendicionAvance;
	private $_tipoOperacion = self::TIPO_OPERACION_INSERTAR;
	private $_docGenera;
	
	public function __construct(){
		$this->_idAvanceBuscado = GetConfig("preCodigoAvance") . GetConfig("delimitadorPreCodigoDocumento");
	}
	public function GetIdAvanceBuscado(){
		return $this->_idAvanceBuscado;
	}
	public function SetIdAvanceBuscado($idAvanceBuscado){
		$this->_idAvanceBuscado = $idAvanceBuscado;
	}
	public function GetIdAvance(){
		return $this->_idAvance;
	}
	public function SetIdAvance($idAvance){
		$this->_idAvance = $idAvance;
	}
	public function GetAvance(){
		return $this->_avance;
	}
	public function SetAvance(EntidadAvance $avance = null){
		$this->_avance = $avance;
	}
	public function GetRendicionAvance(){
		return $this->_rendicionAvance;
	}
	public function SetRendicionAvance(EntidadRendicionAvance $rendicionAvance = null){
		$this->_rendicionAvance = $rendicionAvance;
	}
	public function GetTipoOperacion(){
		return $this->_tipoOperacion;
	}
	public function SetTipoOperacion($tipoOperacion){
		$this->_tipoOperacion = $tipoOperacion;
	}
	public function GetDocGenera(){
		return $this->_docGenera;
	}
	public function SetDocGenera(EntidadDocGenera $docGenera){
		$this->_docGenera = $docGenera;
	}
	public function __clone()
	{
		$this->_avance = ($this->_avance != null) ? clone $this->_avance : null;
		$this->_rendicionAvance = ($this->_rendicionAvance != null) ? clone $this->_rendicionAvance : null;
	}
	public function UTF8Encode()
	{
		$this->_idAvanceBuscado = utf8_encode($this->_idAvanceBuscado);
		if($this->_avance != null) $this->_avance->UTF8Encode();
		if($this->_rendicionAvance != null) $this->_rendicionAvance->UTF8Encode();
		$this->_tipoOperacion = utf8_encode($this->_tipoOperacion);
		
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
				'idAvanceBuscado' => $this->_idAvanceBuscado,
				'avance' => ($this->_avance != null) ? $this->_avance->ToArray() : null,
				'rendicionAvance' => ($this->_rendicionAvance != null) ? $this->_rendicionAvance->ToArray() : null,
				'tipoOperacion' => $this->_tipoOperacion
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