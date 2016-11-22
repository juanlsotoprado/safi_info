<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');
include_once(SAFI_ENTIDADES_PATH . "/rendicionAvance.php");
include_once(SAFI_ENTIDADES_PATH . "/avance.php");
include_once(SAFI_ENTIDADES_PATH . "/docgenera.php");

class BuscarRendicionAvanceForm extends Formularios
{
	private $_idRendicion = null;
	private $_fechaInicio = null;
	private $_fechaFin = null;
	private $_idAvance = null;
	private $_dataRendicionAvances = array();
	
	public function __construct(){
		$this->_idRendicion = GetConfig("preCodigoRendicionAvance").GetConfig("delimitadorPreCodigoDocumento");
		$this->_idAvance = GetConfig("preCodigoAvance").GetConfig("delimitadorPreCodigoDocumento");
	}
	public function GetIdRendicion(){
		return $this->_idRendicion;
	}
	public function SetIdRendicion($idRendicion){
		$this->_idRendicion = $idRendicion;
	}
	public function GetFechaInicio(){
		return $this->_fechaInicio;
	}
	public function SetFechaInicio($fechInicio){
		$this->_fechaInicio = $fechInicio;
	}
	public function GetFechaFin(){
		return $this->_fechaFin;
	}
	public function SetFechaFin($fechaFin){
		$this->_fechaFin = $fechaFin;
	}
	public function GetIdAvance(){
		return $this->_idAvance;
	}
	public function SetIdAvance($idAvance){
		$this->_idAvance = $idAvance;
	}
	public function GetDataRendicionAvances(){
		return $this->_dataRendicionAvances;
	}
	public function SetDataRendicionAvances($dataRendicionAvances){
		$this->_dataRendicionAvances = $dataRendicionAvances;
	}
}