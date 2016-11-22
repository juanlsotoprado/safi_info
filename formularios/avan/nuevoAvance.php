<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');
include_once(SAFI_ENTIDADES_PATH . "/avance.php");
include_once(SAFI_ENTIDADES_PATH . "/docgenera.php");
	
class NuevoAvanceForm extends Formularios
{
	const TIPO_OPERACION_INSERTAR = 1;
	const TIPO_OPERACION_MODIFICAR = 2;
	
	private $_avance;
	private $_tipoOperacion = self::TIPO_OPERACION_INSERTAR;
	private $_docGenera;
	
	public function __construct()
	{
		$this->_avance = new EntidadAvance();
	}
	
	public function GetAvance(){
		return $this->_avance;
	}
	public function SetAvance(EntidadAvance $avance = null){
		$this->_avance = $avance;
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
	public function SetDocGenera(EntidadDocGenera $docGenera = null){
		$this->_docGenera = $docGenera;
	}
	public function __clone()
	{
		$this->_avance = ($this->_avance != null) ? clone $this->_avance : null;
	}
	public function UTF8Encode()
	{
		if($this->_avance != null) $this->_avance->UTF8Encode();
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
				'avance' => ($this->_avance != null) ? $this->_avance->ToArray() : null,
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