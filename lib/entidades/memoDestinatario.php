<?php
class EntidadMemoDestinatario
{
	private $_id; // Código del memo destinatario
	private $_idMemo; // Código del memo
	private $_usuaLoginDestinatario; // Login del susuario destinatario
	private $_tipoDestinatario = 0; // Tipo del destinatario
	private $_fecha; // Fecha y hora
	private $_usuaLoginRemitente; // Remitente del memo
	private $_recibioAnexo = 1; // Recibio Anexo
	private $_idEstado = 1; // Estado (Estatus)
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetIdMemo(){
		return $this->_idMemo;
	}
	public function SetIdMemo($idMemo){
		$this->_idMemo = $idMemo;
	}
	public function GetUsuaLoginDestinatario(){
		return $this->_usuaLoginDestinatario;
	}
	public function SetUsuaLoginDestinatario($usuaLoginDestinatario){
		$this->_usuaLoginDestinatario = $usuaLoginDestinatario;
	}
	public function GetTipoDestinatario(){
		return $this->_tipoDestinatario;
	}
	public function SetTipoDestinatario($tipoDestinatario){
		$this->_tipoDestinatario = $tipoDestinatario;
	}
	public function GetFecha(){
		return $this->_fecha;
	}
	public function SetFecha($fecha){
		$this->_fecha = $fecha;
	}
	public function GetUsuaLoginRemitente(){
		return $this->_usuaLoginRemitente;
	}
	public function SetUsuaLoginRemitente($usuaLoginRemitente){
		$this->_usuaLoginRemitente = $usuaLoginRemitente;
	}
	public function GetRecibioAnexo(){
		return $this->_recibioAnexo;
	}
	public function SetRecibioAnexo($recibioAnexo){
		$this->_recibioAnexo = $recibioAnexo;
	}
	public function GetIdEstado(){
		return $this->_idEstado;
	}
	public function SetIdEstado($idEstado){
		$this->_idEstado = $idEstado;
	}
}