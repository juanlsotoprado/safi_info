<?php
class EntidadMemoRemitente
{
	private $_id; // Código del memo remitente
	private $_idMemo; // Codigo del memo
	private $_usuaLoginRemitente; // Usuario que remite
	private $_idEstado; // Estado (Estatus)
	private $_fecha; // Fecha y hora en que fue remitido

	public function __construct(){
	}
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
	public function GetUsuaLoginRemitente(){
		return $this->_usuaLoginRemitente;
	}
	public function SetUsuaLoginRemitente($usuaLoginRemitente){
		$this->_usuaLoginRemitente = $usuaLoginRemitente;
	}
	public function GetIdEstado(){
		return $this->_idEstado;
	}
	public function SetIdEstado($idEstado){
		$this->_idEstado = $idEstado;
	}
	public function GetFecha(){
		return $this->_fecha;
	}
	public function SetFecha($fecha){
		$this->_fecha = $fecha;
	}
}