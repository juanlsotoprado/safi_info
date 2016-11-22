<?php
include_once(SAFI_ENTIDADES_PATH . '/wfobjeto.php');
include_once(SAFI_ENTIDADES_PATH . '/wfopcion.php');
include_once(SAFI_ENTIDADES_PATH . '/wfgrupo.php');
include_once(SAFI_ENTIDADES_PATH . '/documento.php');

class EntidadWFCadena
{
	private $_id = 0; // Código de la cadena (clave primaria)
	private $_wFObjetoInicial; // Objeto Inicial en la Cadena del WF
	private $_wFObjetoSiguiente; // Objeto Siguiente en la Cadena del WF
	private $_wFOpcion; // Opción en la Cadena
	private $_wFGrupo; // Grupo en la cadena del WF
	private $_wFCadenaPadre; // Registro Padre, de esta cadena, en la Cadena del WF
	private $_wFCadenaHijo; // Próximo Registro, desde esta cadena, en la Cadena del WF
	private $_documento; // Documento
	private $_proyecto = 0; // Especifica si la cadena implica la intervencion de un Jefe de Proyecto
	private $_tipo = 0; // Para indicar tipo de cadena según el documento
	private $_dependencia;
	
	public function __construct()
	{
		
	}
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetWFObjetoInicial(){
		return $this->_wFObjetoInicial;
	}
	public function SetWFObjetoInicial($wFObjetoInicial){
		if($wFObjetoInicial == null || $wFObjetoInicial instanceof EntidadWFObjeto){
			$this->_wFObjetoInicial = $wFObjetoInicial;
		}
		
	}
	public function GetWFObjetoSiguiente(){
		return $this->_wFObjetoSiguiente;
	}
	public function SetWFObjetoSiguiente($wFObjetoSiguiente){
		if($wFObjetoSiguiente == null || $wFObjetoSiguiente instanceof EntidadWFObjeto){
			$this->_wFObjetoSiguiente = $wFObjetoSiguiente;
		}
	}
	public function GetWFOpcion(){
		return $this->_wFOpcion;
	}
	public function SetWFOpcion($wFOpcion){
		if($wFOpcion == null || $wFOpcion instanceof EntidadWFOpcion){
			$this->_wFOpcion = $wFOpcion;
		}
	}
	public function GetWFGrupo(){
		return $this->_wFGrupo;
	}
	public function SetWFGrupo($wFGrupo){
		if($wFGrupo == null || $wFGrupo instanceof EntidadWFGrupo){
			$this->_wFGrupo = $wFGrupo;
		}
	}
	public function GetWFCadenaPadre(){
		return $this->_wFCadenaPadre;
	}
	public function SetWFCadenaPadre($wFCadenaPadre){
		if($wFCadenaPadre == null || $wFCadenaPadre instanceof EntidadWFCadena){
			$this->_wFCadenaPadre = $wFCadenaPadre;
		}
	}
	public function GetWFCadenaHijo(){
		return $this->_wFCadenaHijo;
	}
	public function SetWFCadenaHijo($wFCadenaHijo){
		if($wFCadenaHijo == null || $wFCadenaHijo instanceof EntidadWFCadena){
			$this->_wFCadenaHijo = $wFCadenaHijo;
		}
	}
	public function GetDocumento(){
		return $this->_documento;
	}
	public function SetDocumento($documento){
		if($documento == null || $documento instanceof EntidadDocumento ){
			$this->_documento = $documento;
		}
	}
	public function GetProyecto(){
		return $this->_proyecto;
	}
	public function SetProyecto($proyecto){
		$this->_proyecto = $proyecto;
	}
	public function GetTipo(){
		return $this->_tipo;
	}
	public function SetTipo($tipo){
		$this->_tipo = $tipo;
	}
	public function GetDependencia(){
		return $this->_dependencia;
	}
	public function SetDependencia(EntidadDependencia $dependencia = null){
		$this->_dependencia = $dependencia;
	}
}