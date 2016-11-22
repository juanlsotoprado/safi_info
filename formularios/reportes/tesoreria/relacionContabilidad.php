<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');

class RelacionContabilidadTesoreriaForm extends Formularios
{
	private $_opcion = '';	
	private $_numeroActa = '';
	private $_numeroReferencia = '';
	private $_tipoBusqueda = '';	

	public function __construct()
	{
	}
	public function Getopcion(){
		return $this->_opcion;
	}
	public function SetOpcion($opcion){
		$this->_opcion = $opcion;
	}
	public function GetNumeroActa(){
		return $this->_numeroActa;
	}
	public function SetNumeroActa($numeroActa){
		$this->_numeroActa = $numeroActa;
	}
	public function GetNumeroReferencia(){
		return $this->_numeroReferencia;
	}
	public function SetNumeroReferencia($numeroReferencia){
		$this->_numeroReferencia = $numeroReferencia;
	}
	public function GetTipoBusqueda(){
		return $this->_tipoBusqueda;
	}
	public function SetTipoBusqueda($tipoBusqueda){
		$this->_tipoBusqueda = $tipoBusqueda;
	}	
}