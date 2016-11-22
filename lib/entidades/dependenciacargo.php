<?php
class EntidadDependenciaCargo
{
	// clave primaria (idDependencia, idCargo)
	
	private $_idDependencia;
	private $_iDcargo;
	private $_idEstatus; // Estado del Recurso
	private $_usuaLogin; // Login del Usuario
	
	public function __construct()
	{
		
	}
	
	public function GetIdDependencia(){
		return $this->_idDependencia;
	}
	public function SetIdDependencia($idDependencia){
		$this->_idDependencia = $idDependencia;
	}
	public function GetIDcargo(){
		return $this->_iDcargo;
	}
	public function SetIDcargo($iDcargo){
		$this->_iDcargo = $iDcargo;
	}
	public function GetIdEstatus(){
		return $this->_idEstatus;
	}
	public function SetIdEstatus($idEstatus){
		$this->_idEstatus = $idEstatus;
	}
	public function GetUsuaLogin(){
		return $this->_usuaLogin;
	}
	public function SetUsuaLogin($usuaLogin){
		$this->_usuaLogin = $usuaLogin;
	} 
}