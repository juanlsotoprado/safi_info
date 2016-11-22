<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');
include_once(SAFI_ENTIDADES_PATH . "/viaticonacional.php");
include_once(SAFI_ENTIDADES_PATH . "/rendicionViaticoNacional.php");
include_once(SAFI_ENTIDADES_PATH . "/docgenera.php");

class NuevaRendicionViaticoNacionalForm extends Formularios
{
	const TIPO_OPERACION_INSERTAR = 1;
	const TIPO_OPERACION_MODIFICAR = 2;
	
	private $_idViaticoBuscado = "vnac-";
	private $_viatico;
	private $_rendicionViaticoNacional;
	private $_informeTmpPath;
	private $_tipoOperacion = self::TIPO_OPERACION_INSERTAR;
	private $_docGenera;
	
	public function __construct()
	{
		$this->_rendicionViaticoNacional = new EntidadRendicionViaticoNacional();
	}
	public function GetViatico(){
		return $this->_viatico;
	}
	public function SetViatico(EntidadViaticoNacional $viatico = null){
		$this->_viatico = $viatico;
	}
	public function GetIdViaticoBuscado(){
		return $this->_idViaticoBuscado;
	}
	public function SetIdViaticoBuscado($idViaticoBuscado){
		$this->_idViaticoBuscado = $idViaticoBuscado;
	}
	public function GetRendicionViaticoNacional(){
		return $this->_rendicionViaticoNacional;
	}
	public function SetRendicionViaticoNacional(EntidadRendicionViaticoNacional $rendicionViaticoNacional = null){
		$this->_rendicionViaticoNacional = $rendicionViaticoNacional;
	}
	public function GetInformeTmpPath(){
		return $this->_informeTmpPath;
	}
	public function SetInformeTmpPath($informeTmpPath){
		$this->_informeTmpPath = $informeTmpPath;
	}
	public function GetTipoOperacion(){
		return $this->_tipoOperacion;
	}
	public function SetTipoOperacion($tipoOperacion){
		$this->_tipoOperacion = $tipoOperacion;
	}
	public function GetDocGenera(){
		return $this->_docGenera;
	}
	public function SetDocGenera(EntidadDocGenera $docGenera = null){
		$this->_docGenera = $docGenera;
	}
}