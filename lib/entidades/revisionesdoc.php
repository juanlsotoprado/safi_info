<?php
class EntidadRevisionesDoc
{
	
	private $_id; // Código del registro
	private $_idDocumento; // Código del documento revisado
	private $_loginUsuario; // Login del usuario que revisa el documento
	private $_idPerfil; // Perfil del usuario que revisa el documento
	private $_fechaRevision; // Fecha y hora de revisión
	private $_idWFOpcion; // Código de la Opción de revisión
	private $_firmaRevision; // Firma del usuario
	
	public function __construct()
	{
		
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
	public function GetLoginUsuario(){
		return $this->_loginUsuario;
	}
	public function SetLoginUsuario($loginUsuario){
		$this->_loginUsuario = $loginUsuario;
	}
	public function GetIdPerfil(){
		return $this->_idPerfil;
	}
	public function SetIdPerfil($idPerfil){
		$this->_idPerfil = $idPerfil;
	}
	public function GetFechaRevision(){
		return $this->_fechaRevision;
	}
	public function SetFechaRevision($fechaRevision){
		$this->_fechaRevision = $fechaRevision;
	}
	public function GetIdWFOpcion(){
		return $this->_idWFOpcion;
	}
	public function SetIdWFOpcion($idWFOpcion){
		$this->_idWFOpcion = $idWFOpcion;
	}
	public function GetFirmaRevision(){
		return $this->_firmaRevision;
	}
	public function SetFirmaRevision($firmaRevision){
		$this->_firmaRevision = $firmaRevision;
	}
	
}