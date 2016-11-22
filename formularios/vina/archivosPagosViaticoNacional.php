<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');
include_once(SAFI_ENTIDADES_PATH . "/viaticonacional.php");

class ArchivosPagosViaticoNacionalForm extends Formularios
{
	private $_idViatico;
	private $_fechaAbono;
	private $_viatico = null;
	
	public function __construct()
	{
		
	}
	
	public function GetIdViatico(){
		return $this->_idViatico;
	}
	public function SetIdViatico($idViatico){
		$this->_idViatico = $idViatico;
	}
	public function GetFechaAbono(){
		return $this->_fechaAbono;
	}
	public function SetFechaAbono($fechaAbono){
		$this->_fechaAbono = $fechaAbono;
	}
	public function GetViatico(){
		return $this->_viatico;
	}
	public function SetViatico(EntidadViaticoNacional $viatico = null){
		$this->_viatico = $viatico;
	}
}