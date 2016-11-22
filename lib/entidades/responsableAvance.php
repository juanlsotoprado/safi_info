<?php
require_once (SAFI_ENTIDADES_PATH . '/responsable.php');
require_once (SAFI_ENTIDADES_PATH . '/empleado.php');
require_once (SAFI_ENTIDADES_PATH . '/beneficiarioviatico.php');
require_once (SAFI_ENTIDADES_PATH . '/estado.php');
require_once (SAFI_ENTIDADES_PATH . '/tipocuentabancaria.php');

class EntidadResponsableAvance
{
	private $_id;
	private $_idAvance;
	private $_tipoResponsable = EntidadResponsable::TIPO_EMPLEADO;
	private $_empleado;
	private $_beneficiario;
	private $_estado;
	private $_numeroCuenta;
	private $_tipoCuenta;
	private $_banco;
	
	public function __construct(){
	
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetIdAvance(){
		return $this->_idAvance;
	}
	public function SetIdAvance($idAvance){
		$this->_idAvance = $idAvance;
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
	public function GetNumeroCuenta(){
		return $this->_numeroCuenta;
	}
	public function SetNumeroCuenta($numeroCuenta){
		$this->_numeroCuenta = $numeroCuenta;
	}
	public function GetTipoCuenta(){
		return $this->_tipoCuenta;
	}
	public function SetTipoCuenta($tipoCuenta){
		$this->_tipoCuenta = $tipoCuenta;
	}
	public function GetBanco(){
		return $this->_banco;
	}
	public function SetBanco($banco){
		$this->_banco = $banco;
	}
	public function __clone(){
		$this->_empleado = ($this->_empleado != null) ? clone $this->_empleado : null;
		$this->_beneficiario = ($this->_beneficiario != null) ? clone $this->_beneficiario : null;
		$this->_estado = ($this->_estado != null) ? clone $this->_estado : null;
	}
	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_idAvance = utf8_encode($this->_idAvance);
		$this->_tipoResponsable = utf8_encode($this->_tipoResponsable);
		if($this->_empleado != null) $this->_empleado->UTF8Encode();
		if($this->_beneficiario != null) $this->_beneficiario->UTF8Encode();
		if($this->_estado != null) $this->_estado->UTF8Encode();
		$this->_numeroCuenta = utf8_encode($this->_numeroCuenta);
		$this->_tipoCuenta = utf8_encode($this->_tipoCuenta);
		$this->_banco = utf8_encode($this->_banco);
		
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
				'id' => $this->_id,
				'idAvance' => $this->_idAvance,
				'tipoResponsable' => $this->_tipoResponsable,
				'empleado' => ($this->_empleado != null) ? $this->_empleado->ToArray() : null,
				'beneficiario' => ($this->_beneficiario != null) ? $this->_beneficiario->ToArray() : null,
				'estado' => ($this->_estado != null) ? $this->_estado->ToArray() : null,
				'numeroCuenta' => $this->_numeroCuenta,
				'tipoCuenta' => $this->_tipoCuenta,
				'banco' => $this->_banco
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}