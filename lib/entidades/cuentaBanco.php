<?php
include_once(SAFI_ENTIDADES_PATH . '/tipocuentabancaria.php');
include_once(SAFI_ENTIDADES_PATH . '/banco.php');
include_once(SAFI_ENTIDADES_PATH . '/cuentaContable.php');
include_once(SAFI_ENTIDADES_PATH . '/estatus.php');

class EntidadCuentaBanco
{
	private $_id; // Identificador de la cuenta bancaria en la tabla
	private $_tipoCuenta = null; // Tipo cuenta bancaria 
	private $_banco = null; // Banco. Entidad bancaria donde de apertura la cuenta 
	private $_descripcion; // Descripcion del objeto de la cuenta
	private $_fechaApertura; // Fecha de apertura de la cuenta bancaria en el banco
	private $_fechaCierre; // Fecha de cierre de la cuenta bancaria
	private $_cuentaContable; // Objeto de la cuenta contable
	private $_estatus = null; // // Estatus de la cuenta
	private $_a_oApertura ; // Año de apertura de la cuenta
	private $_saldoInicial; // Monto por el cual se aperturo la cuenta
	private $_fechaRegistro; // Fecha de registro de la cuenta bancaria en el sistema
	private $_fechaCierreRegistro; // Fecha de cierre de la cuenta bancaria en el sistema
	private $_usuarioLogin;
	
	public function __construct(){
		
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetTipoCuenta(){
		return $this->_tipoCuenta;
	}
	public function SetTipoCuenta($tipo){
		$this->_tipoCuenta = $tipo;
	}	
	public function GetBanco(){
		return $this->_banco;
	}
	public function SetBanco(EntidadBanco $banco = null){
		$this->_banco = $banco;
	}
	public function GetDescripcion(){
		return $this->_descripcion;
	}
	public function SetDescripcion($descripcion){
		$this->_descripcion = $descripcion;
	}
	public function GetFechaApertura(){
		return $this->_fechaApertura;
	}
	public function SetFechaApertura($fechaApertura){
		$this->_fechaApertura = $fechaApertura;
	}
	public function GetFechaCierre(){
		return $this->_fechaCierre;
	}
	public function SetFechaCierre($fechaCierre){
		$this->_fechaCierre = $fechaCierre;
	}
	public function GetCuentaContable(){
		return $this->_cuentaContable;
	}
	public function SetCuentaContable(EntidadCuentaContable $cuentaContable = null){
		$this->_cuentaContable = $cuentaContable;
	}
	public function GetEstatus(){
		return $this->_estatus;
	}
	public function SetEstatus(EntidadEstatus $estatus = null){
		$this->_estatus = $estatus;
	}
	public function GetA_oApertura(){
		return $this->_a_oApertura;
	}
	public function SetA_oApertura($a_oApertura){
		$this->_a_oApertura = $a_oApertura;
	}
	public function GetSaldoInicial(){
		return $this->_saldoInicial;
	}
	public function SetSaldoInicial($saldoInicial){
		$this->_saldoInicial = $saldoInicial;
	}
	public function GetFechaRegistro(){
		return $this->_fechaRegistro;
	}
	public function SetFechaRegistro($fechaRegistro){
		$this->_fechaRegistro = $fechaRegistro;
	}
	public function GetFechaCierreRegistro(){
		return $this->_fechaCierreRegistro;
	}
	public function SetFechaCierreRegistro($fechaCierreRegistro){
		$this->_fechaCierreRegistro = $fechaCierreRegistro;
	}
	public function GetUsuarioLogin(){
		return $this->_usuarioLogin;
	}
	public function SetUsuarioLogin($usuarioLogin){
		$this->_usuarioLogin = $usuarioLogin;
	}
	
	public function __toString()
	{
		return "
			id = " . $this->GetId() . ",
			tipoCuenta = " . $this->GetTipoCuenta() . ",
			banco = " . $this->GetBanco() . ",
			descripcion = " . $this->GetDescripcion() . ",
			fechaApertura = " . $this->GetFechaApertura() . ",
			fechaCierre = " . $this->GetFechaCierre() . ",
			cuentaContable = " . $this->GetCuentaContable() . ",
			estatus = " . $this->GetEstatus() . ",
			a_oApertura = ".$this->GetA_oApertura().",
			saldoInicial = ".$this->GetSaldoInicial().",
			fechaRegistro = ".$this->GetFechaRegistro().",
			fechaCierreRegistro = ".$this->GetFechaCierreRegistro().",
			usuarioLogin = ".$this->GetUsuarioLogin()."
		";
	}
	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_tipoCuenta = utf8_encode($this->_tipoCuenta);
		if(is_object($this->_banco)) $this->_banco->UTF8Encode();
		$this->_descripcion = utf8_encode($this->_descripcion);
		$this->_fechaApertura = utf8_encode($this->_fechaApertura);
		$this->_fechaCierre = utf8_encode($this->_fechaCierre);
		$this->_cuentaContable = utf8_encode($this->_cuentaContable);
		if(is_object($this->_estatus)) $this->_estatus->UTF8Encode();
		$this->_a_oApertura = utf8_encode($this->_a_oApertura);
		$this->_saldoInicial = utf8_encode($this->_saldoInicial);
		$this->_fechaRegistro = utf8_encode($this->_fechaRegistro);
		$this->_fechaCierreRegistro = utf8_encode($this->_fechaCierreRegistro);
		$this->_usuarioLogin = utf8_encode($this->_usuarioLogin);
		
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
				'tipoCuenta' => $this->_tipoCuenta,
				'banco' => (is_object($this->_banco) ? $this->_banco->ToArray() : null),
				'descripcion' => $this->_descripcion,
				'fechaApertura' => $this->_fechaApertura,
				'fechaCierre' => $this->_fechaCierre,
				'cuentaContable' => (is_object($this->_cuentaContable) ? $this->_cuentaContable->ToArray() : null),
				'estatus' => (is_object($this->_estatus) ? $this->_estatus->ToArray() : null),
				'a_oApertura' => $this->_a_oApertura,
				'saldoInicial' => $this->_saldoInicial,
				'fechaRegistro' => $this->_fechaRegistro,
				'fechaCierreRegistro' => $this->_fechaCierreRegistro,
				'usuarioLogin' => $this->_usuarioLogin
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