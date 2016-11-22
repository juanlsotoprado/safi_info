<?php

require_once (SAFI_ENTIDADES_PATH.'/partida.php');
require_once (SAFI_ENTIDADES_PATH.'/dependencia.php');
require_once (SAFI_ENTIDADES_PATH.'/proyecto.php');
require_once (SAFI_ENTIDADES_PATH.'/proyectoespecifica.php');
require_once (SAFI_ENTIDADES_PATH.'/accioncentralizada.php');
require_once (SAFI_ENTIDADES_PATH.'/accioncentralizadaespecifica.php');

class EntidadPuntoCuentaImputa
{
	private $_id; 
	private $_monto;
	private $_tipoImpu;
	private $_presAnno;
	private $_partida; 
	private $_dependencia;
	private $_proyecto;
	private $_accionCentralizada;
	private $_accionEspecificaProyecto;
	private $_accionEspecificaAcCentralizada;
	
	
	
	public function __construct(){
		
	}
	
	public function GetMonto(){
		return $this->_monto;
	}
	public function SetMonto($monto){
		$this->_monto = $monto;
	}
    public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
   public function GetTipoImpu(){
		return $this->_tipoImpu;
	}
	public function SetTipoImpu($tipoImpu){
		$this->_tipoImpu = $tipoImpu;
	}
    public function GetPresAnno(){
		return $this->_presAnno;
	}
	public function SetPresAnno($presAnno){
		$this->_presAnno = $presAnno;
	}
	
    public function GetPartida(){
		return $this->_partida;
	}
	public function SetPartida(EntidadPartida $partida = null){
		$this->_partida = $partida;
	}
    public function GetDependencia(){
		return $this->_dependencia;
	}
	
	public function SetDependencia(EntidadDependencia $dependencia = null){
		$this->_dependencia = $dependencia;
	}
	

    public function GetProyecto(){
		return $this->_proyecto;
	}
	
	public function SetProyecto(EntidadProyecto $proyecto = null){
		$this->_proyecto = $proyecto;
	}
	
    public function GetProyectoEspecifica(){
		return $this->_accionEspecificaProyecto;
	}
	
	public function SetProyectoEspecifica(EntidadProyectoEspecifica $accionEspecificaProyecto = null){
		$this->_accionEspecificaProyecto = $accionEspecificaProyecto;
	}
	
	
    public function GetAccionCentralizada(){
		return $this->_accionCentralizada;
	}
	
	public function SetAccionCentralizada(EntidadAccionCentralizada $accionCentralizada = null){
		$this->_accionCentralizada = $accionCentralizada;
	}
    public function GetAccionCentralizadaEspecifica(){
    	
    	return $this->_accionEspecificaAcCentralizada;
	}
	
	public function SetAccionCentralizadaEspecifica(EntidadAccionCentralizadaEspecifica $accionCentralizadaEspecifica = null){
		$this->_accionEspecificaAcCentralizada = $accionCentralizadaEspecifica;
	}
	
	
	public function __toString()
	{
		return "
			Id = ".$this->_id.",
			Monto = ".$this->_monto.",
			TipoImpu = ".$this->_tipoImpu.",
			PresAnno = ".$this->_presAnno.",
			Partida = ".($this->_partida !== null ? $this->_partida : "NULL").",
		    Dependencia = ".($this->_dependencia !== null ? $this->_dependencia : "NULL").",
		    Proyecto = ".($this->_proyecto !== null ? $this->_proyecto : "NULL").",
	      	ProyectoEspecifica = ".($this->_accionEspecificaProyecto !== null ? $this->_accionEspecificaProyecto : "NULL").",
	      	AccionCentralizada = ".($this->_accionCentralizada !== null ? $this->_accionCentralizada : "NULL").",
	      	CentralizadaEspecifica = ".($this->_accionEspecificaAcCentralizada !== null ? $this->_accionEspecificaAcCentralizada : "NULL").",
		";
	}
	
	
	
	public function __clone()
	{
		
		$this->_id = ($this->_id !== null) ? clone $this->_id : null;
		$this->_monto = ($this->_monto !== null) ? clone $this->_monto : null;
		$this->_tipoImpu = ($this->_tipoImpu !== null) ? clone $this->_tipoImpu : null;
		$this->_presAnno = ($this->_presAnno !== null) ? clone $this->_presAnno : null;
		$this->_partida = ($this->_partida !== null) ? clone $this->_partida : null;
		$this->_dependencia = ($this->_dependencia !== null) ? clone $this->_dependencia : null;
		$this->_proyecto = ($this->_proyecto !== null) ? clone $this->_proyecto : null;
		$this->_accionEspecificaProyecto = ($this->_accionEspecificaProyecto !== null) ? clone $this->_accionEspecificaProyecto : null;
		$this->_accionCentralizada = ($this->_accionCentralizada !== null) ? clone $this->_accionCentralizada : null;
		$this->_accionEspecificaAcCentralizada = ($this->_accionEspecificaAcCentralizada !== null) ? clone $this->_accionEspecificaAcCentralizada : null;

	}
	
	
	
	public function UTF8Encode(){
		$this->_id = utf8_encode($this->_id);
		$this->_monto = utf8_encode($this->_monto);
		$this->_tipoImpu = utf8_encode($this->_tipoImpu);
	    $this->_presAnno = utf8_encode($this->_presAnno);
		if($this->_dependencia !== null) $this->_dependencia->UTF8Encode();
		if($this->_partida !== null) $this->_partida->UTF8Encode();
		if($this->_proyecto !== null) $this->_proyecto->UTF8Encode();
		if($this->_accionEspecificaProyecto !== null) $this->_accionEspecificaProyecto->UTF8Encode();
		if($this->_accionCentralizada !== null) $this->_accionCentralizada->UTF8Encode();
		if($this->_accionEspecificaAcCentralizada !== null) $this->_accionEspecificaAcCentralizada->UTF8Encode();
	
		return $this;
	}
	
	
	
	public function ToArray($properties = array())
	{
		$data = array();
		
		if(is_array($properties) && count($properties) > 0){
			foreach($properties as $property){
				$nameProperty = '_' . $property;
				$data[$property] = $this->$property;
			}
		} else {
			$data = array(
				'id' => $this->_id,
				'monto' => $this->_monto,
				'tipoImpu' => $this->_tipoImpu,
		     	'presAnno' => $this->_presAnno,
				'dependencia' => ($this->_dependencia !== null) ? $this->_dependencia->ToArray() : null,
			    'partida' => ($this->_partida !== null) ? $this->_partida->ToArray() : null,
				'proyecto' => ($this->_proyecto !== null) ? $this->_proyecto->ToArray() : null,
				'proyectoEspecifica' => ($this->_accionEspecificaProyecto !== null) ? $this->_accionEspecificaProyecto->ToArray() : null,
				'accionCentralizada' => ($this->_accionCentralizada !== null) ? $this->_accionCentralizada->ToArray() : null,
				'CentralizadaEspecifica' => ($this->_accionEspecificaAcCentralizada !== null) ? $this->_accionEspecificaAcCentralizada->ToArray() : null
			
			);
		}
		
		return $data;
	}
	
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
	
	
}