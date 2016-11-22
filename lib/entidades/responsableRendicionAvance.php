<?php
require_once (SAFI_ENTIDADES_PATH . '/responsable.php');
require_once (SAFI_ENTIDADES_PATH . '/empleado.php');
require_once (SAFI_ENTIDADES_PATH . '/beneficiarioviatico.php');
require_once (SAFI_ENTIDADES_PATH . '/estado.php');
require_once (SAFI_ENTIDADES_PATH . '/banco.php');

class EntidadResponsableRendicionAvance
{
	private $_idResponsableAvance;
	private $_idRendicionAvance;
	private $_tipoResponsable = EntidadResponsable::TIPO_EMPLEADO;
	private $_empleado;
	private $_beneficiario;
	private $_estado;
	
	public function __construct(){
	
	}
	public function GetIdResponsableAvance(){
		return $this->_idResponsableAvance;
	}
	public function SetIdResponsableAvance($idResponsableAvance){
		$this->_idResponsableAvance = $idResponsableAvance;
	}
	public function GetIdRendicionAvance(){
		return $this->_idRendicionAvance;
	}
	public function SetIdRendicionAvance($idRendicionAvance){
		$this->_idRendicionAvance = $idRendicionAvance;
	}
	public function GetTipoResponsable(){
		return $this->_tipoResponsable;
	}
	public function SetTipoResponsable($tipoResponsable){
		$this->_tipoResponsable = $tipoResponsable;
	}
	public function GetEmpleado(){
		return $this->_empleado;
	}
	public function SetEmpleado(EntidadEmpleado $empleado = null){
		$this->_empleado = $empleado;
	}
	public function GetBeneficiario(){
		return $this->_beneficiario;
	}
	public function SetBeneficiario(EntidadBeneficiarioViatico $beneficiario = null){
		$this->_beneficiario = $beneficiario;
	}
	public function GetEstado(){
		return $this->_estado;
	}
	public function SetEstado(EntidadEstado $estado = null){
		$this->_estado = $estado;
	}
	public function __clone(){
		$this->_empleado = ($this->_empleado != null) ? clone $this->_empleado : null;
		$this->_beneficiario = ($this->_beneficiario != null) ? clone $this->_beneficiario : null;
		$this->_estado = ($this->_estado != null) ? clone $this->_estado : null;
	}
	public function UTF8Encode()
	{
		$this->_idResponsableAvance = utf8_encode($this->_idResponsableAvance);
		$this->_idRendicionAvance = utf8_encode($this->_idRendicionAvance);
		$this->_tipoResponsable = utf8_encode($this->_tipoResponsable);
		if($this->_empleado != null) $this->_empleado->UTF8Encode();
		if($this->_beneficiario != null) $this->_beneficiario->UTF8Encode();
		if($this->_estado != null) $this->_estado->UTF8Encode();
		
		return $this;
	}
	public function ToArray($properties = array()){
		$data = array();
		
		if(is_array($properties) && count($properties) > 0){
			foreach($properties as $property){
				$nameProperty = '_' . $property;
				$data[$property] = $this->$property;
			}
		} else {
			$data = array(
				'idResponsableAvance' => $this->_idResponsableAvance,
				'idRendicionAvance' => $this->_idRendicionAvance,
				'tipoResponsable' => $this->_tipoResponsable,
				'empleado' => ($this->_empleado != null) ? $this->_empleado->ToArray() : null,
				'beneficiario' => ($this->_beneficiario != null) ? $this->_beneficiario->ToArray() : null,
				'estado' => ($this->_estado != null) ? $this->_estado->ToArray() : null
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}