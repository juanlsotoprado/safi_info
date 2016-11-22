<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');
include_once(SAFI_ENTIDADES_PATH . "/avance.php");
include_once(SAFI_ENTIDADES_PATH . "/docgenera.php");

class BuscarAvanceForm extends Formularios
{
	private $_fechaInicio = '';
	private $_fechaFin = '';
	private $_avance = null;
	private $_docGenera = null;
	private $_dataAvances = array();
	
	public function __construct()
	{
		$this->SetAvance(new EntidadAvance());
		$this->GetAvance()->SetId(GetConfig("preCodigoAvance").GetConfig("delimitadorPreCodigoDocumento"));
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
	public function GetAvance(){
		return $this->_avance;
	}
	public function SetAvance(EntidadAvance $avance){
		$this->_avance = $avance;
	}
	public function GetDocGenera(){
		return $this->_docGenera;
	}
	public function SetDocGenera(EntidadDocGenera $docGenera = null){
		$this->_docGenera = $docGenera;
	}
	public function GetDataAvances(){
		return $this->_dataAvances;
	}
	public function SetDataAvances($dataAvances){
		$this->_dataAvances = $dataAvances;
	}
}