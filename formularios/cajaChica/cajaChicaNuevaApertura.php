<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');

class CajaChicaNuevaAperturaForm extends Formularios
{	
	private $_cajaChica = null;
	
	public function __construct(){
	}
	public function GetCajaChica(){
		return $this->_cajaChica;
	}
	public function SetCajaChica($cajaChica){
		$this->_cajaChica = $cajaChica;
	}
}
?>