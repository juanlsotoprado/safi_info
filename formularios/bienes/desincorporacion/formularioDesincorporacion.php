<?php
require_once(SAFI_FORMULARIOS_PATH . '/formularios.php');
require_once(SAFI_ENTIDADES_PATH . "/desincorporacionBien.php");

class FormularioDesincorporacionForm extends Formularios
{
	const TIPO_OPERACION_INSERTAR = 1;
	const TIPO_OPERACION_MODIFICAR = 2;
	
	private $_tipoOperacion = self::TIPO_OPERACION_INSERTAR;
	private $_desincorporacionBien;
	
	public function __construct(){
		$this->_desincorporacionBien = new EntidadDesincorporacionBien();
	}
	public function GetDesincorporacionBien(){
		$this->_desincorporacionBien;
	}
	public function SetDesincorporacionBien($desincorporacionBien){
		$this->_desincorporacionBien = $desincorporacionBien;
	}
	public function __clone()
	{
		if($this->GetDesincorporacionBien() != null) $this->SetDesincorporacionBien(clone $this->GetDesincorporacionBien());
	}
	public function UTF8Encode()
	{
		$this->_desincorporacionBien->UTF8Encode();
		
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
				"desincorporacionBien" => ($this->GetDesincorporacionBien() != null ? $this->GetDesincorporacionBien()->ToArray() : null)
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