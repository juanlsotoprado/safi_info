<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');
include_once(SAFI_ENTIDADES_PATH . "/avance.php");

class ArchivosPagosAvanceForm extends Formularios
{
	private $_idAvance;
	private $_fechaAbono;
	private $_avance = null;
	
	public function __construct()
	{
		
	}
	
	public function GetIdAvance(){
		return $this->_idAvance;
	}
	public function SetIdAvance($idAvance){
		$this->_idAvance = $idAvance;
	}
	public function GetFechaAbono(){
		return $this->_fechaAbono;
	}
	public function SetFechaAbono($fechaAbono){
		$this->_fechaAbono = $fechaAbono;
	}
	public function GetAvance(){
		return $this->_avance;
	}
	public function SetAvance(EntidadAvance $avance = null){
		$this->_avance = $avance;
	}
}