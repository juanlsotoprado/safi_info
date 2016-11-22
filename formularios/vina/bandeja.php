<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');

class BandejaViaticoNacionalForm extends Formularios
{
	private $_enBandeja = array();
	private $_porEnviar = array();
	private $_enTransito = array();
	
	public function __construct()
	{
		
	}
	
	public function GetEnBandeja(){
		return $this->_enBandeja;
	}
	public function SetEnBandeja($enBandeja){
		$this->_enBandeja = $enBandeja;
	}
	public function GetPorEnviar(){
		return $this->_porEnviar;
	}
	public function SetPorEnviar($porEnviar){
		$this->_porEnviar = $porEnviar;
	}
	public function GetEnTransito(){
		return $this->_enTransito;
	}
	public function SetEnTransito($enTransito){
		$this->_enTransito = $enTransito;
	}
}