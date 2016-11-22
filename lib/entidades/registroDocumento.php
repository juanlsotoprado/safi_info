<?php
class EntidadRegistroDocumento {
	private $_id = '';
	private $_beneficiario = '';
	private $_observaciones = '';
	private $_monto = 0;
	
	public function __construct(){}
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetBeneficiario(){
		return $this->_beneficiario;
	}
	public function SetBeneficiario($nombre){
		$this->_beneficiario = $nombre;
	}
	public function GetObservaciones(){
		return $this->_observaciones;
	}
	public function SetObservaciones($observaciones){
		$this->_observaciones = $observaciones;
	}
	public function GetMonto(){
		return $this->_monto;
	}
	public function SetMonto($monto){
		$this->_monto = $monto;
	}
	public function ToArray($properties = array())	{
		$data = array();
		if(is_array($properties) && count($properties) > 0){
			foreach($properties as $property){
				$nameProperty = '_' . $property;
				$data[$property] = $this->$property;
			}
		}else{
			$data = array(
				'id' => $this->_id,
				'beneficiario' =>  $this->_beneficiario,
				'observaciones' => $this->_observaciones,
				'monto' => $this->_monto
			);
		}
		return $data;
	}
	public function ToJson($properties = array()){
		return json_encode($this->ToArray());
	}
}