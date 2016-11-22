<?php
/*
 * Historial de acciones sobre los documentos
 */
class EntidadHistorialAccion
{
	private $_id; // Código del registro (Código de historial de acciones)
	private $_idDocumento; // Codigo del documento aprobado
	private $_fecha; // Fecha y hora de la acción
	private $_usuaLogin; // Login del empleado que ejecuta la acción
	private $_idPerfil; // Perfil bajo el cual el Usuario ejecuta la acción
	private $_idAccion; // Código de la acción realizada sobre el documento
	private $_idDependencia;
	private $_descripcion; // Descripción adicional de la acción
	private $_firma; // Indica si la acción ejecutada implicará firma digital
	
	public function __construct(){
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetIdDocumento(){
		return $this->_idDocumento;
	}
	public function SetIdDocumento($idDocumento){
		$this->_idDocumento = $idDocumento;
	}
	public function GetFecha(){
		return $this->_fecha;
	}
	public function SetFecha($fecha){
		$this->_fecha = $fecha;
	}
	public function GetUsuaLogin(){
		return $this->_usuaLogin;
	}
	public function SetUsuaLogin($usuaLogin){
		$this->_usuaLogin = $usuaLogin;
	}
	public function GetIdPerfil(){
		return $this->_idPerfil;
	}
	public function SetIdPerfil($idPerfil){
		$this->_idPerfil = $idPerfil;
	}
	public function GetIdAccion(){
		return $this->_idAccion;
	}
	public function SetIdAccion($idAccion){
		$this->_idAccion = $idAccion;
	}
	public function GetIdDependencia(){
		return $this->_idDependencia;
	}
	public function SetIdDependencia($idDependencia){
		$this->_idDependencia = $idDependencia;
	}
	public function GetDescripcion(){
		return $this->_descripcion;
	}
	public function SetDescripcion($descripcion){
		$this->_descripcion = $descripcion;
	}
	public function GetFirma(){
		return $this->_firma;
	}
	public function SetFirma($firma){
		$this->_firma = $firma;
	}
}
?>