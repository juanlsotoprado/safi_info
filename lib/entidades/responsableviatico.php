<?php
class EntidadResponsableViatico
{
	const TIPO_EMPLEADO = 'empleado';
	const TIPO_BENEFICIARIO = 'beneficiario';
	
	private $_id;
	private $_idViatico;
	private $_tipoResponsable = self::TIPO_EMPLEADO;
	private $_cedula;
	private $_nombres;
	private $_apellidos;
	private $_nacionalidad;
	private $_idDependencia;
	private $_tipoEmpleado;
	private $_numeroCuenta;
	private $_tipoCuenta;
	private $_banco;

	public function __construct()
	{
		
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetIdViatico(){
		return $this->_idViatico;
	}
	public function SetIdViatico($idViatico){
		$this->_idViatico = $idViatico;
	}
	public function GetTipoResponsable(){
		return $this->_tipoResponsable;
	}
	public function SetTipoResponsable($tipoResponsable){
		$this->_tipoResponsable = $tipoResponsable;
	}
	public function GetCedula()
	{
		return $this->_cedula;
	}
	public function SetCedula($cedula)
	{
		$this->_cedula = $cedula;
	}
	public function GetNombres()
	{
		return $this->_nombres;
	}
	public function SetNombres($nombres)
	{
		$this->_nombres = $nombres;
	}
	public function GetApellidos()
	{
		return $this->_apellidos;
	}
	public function SetApellidos($apellidos)
	{
		$this->_apellidos = $apellidos;
	}
	public function GetNacionalidad()
	{
		return $this->_nacionalidad;
	}
	public function SetNacionalidad($nacionalidad)
	{
		$this->_nacionalidad = $nacionalidad;
	}
	public function GetIdDependencia()
	{
		return $this->_idDependencia;
	}
	public function SetIdDependencia($idDependencia)
	{
		$this->_idDependencia = $idDependencia;
	}
	public function GetTipoEmpleado()
	{
		return $this->_tipoEmpleado;
	}
	public function SetTipoEmpleado($tipoEmpleado)
	{
		$this->_tipoEmpleado = $tipoEmpleado;
	}
	public function GetNumeroCuenta()
	{
		return $this->_numeroCuenta;
	}
	public function SetNumeroCuenta($numeroCuenta)
	{
		// El número de cuenta debe tener un máximo de 20 caracteres
		$this->_numeroCuenta = substr($numeroCuenta, 0, 20) ;
	}
	public function GetTipoCuenta()
	{
		return $this->_tipoCuenta;
	}
	public function SetTipoCuenta($tipoCuenta)
	{
		// El tipo de cuanta es solo un caracter
		$this->_tipoCuenta = substr($tipoCuenta, 0, 1);
	}
	public function GetBanco()
	{
		return $this->_banco;
	}
	public function SetBanco($banco)
	{
		$this->_banco = $banco;
	}
	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_idViatico = utf8_encode($this->_idViatico);
		$this->_tipoResponsable = utf8_encode($this->_tipoResponsable);
		$this->_cedula = utf8_encode($this->_cedula);
		$this->_nombres = utf8_encode($this->_nombres);
		$this->_apellidos = utf8_encode($this->_apellidos);
		$this->_nacionalidad = utf8_encode($this->_nacionalidad);
		$this->_idDependencia = utf8_encode($this->_idDependencia);
		$this->_tipoEmpleado = utf8_encode($this->_tipoEmpleado);
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
				'idViatico' => $this->_idViatico,
				'tipoResponsable' => $this->_tipoResponsable,
				'cedula' => $this->_cedula,
				'nombres' => $this->_nombres,
				'apellidos' => $this->_apellidos,
				'nacionalidad' => $this->_nacionalidad,
				'idDependencia' => $this->_idDependencia,
				'tipoEmpleado' => $this->_tipoEmpleado,
				'numeroCuenta' => $this->_numeroCuenta,
				'tipoCuenta' => $this->_tipoCuenta,
				'banco' => $this->_banco
			);
		}
		
		return $data;
	}
	public function ToJson(){
		return json_encode($this->ToArray());
	}
}