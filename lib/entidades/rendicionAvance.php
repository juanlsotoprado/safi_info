<?php

require_once(SAFI_ENTIDADES_PATH . '/responsableRendicionAvancePartidas.php');
require_once(SAFI_ENTIDADES_PATH . '/dependencia.php');

class EntidadRendicionAvance
{
	private $_id;
	private $_avance;
	private $_fechaRendicion;
	private $_fechaRegistro;
	private $_fechaUltimaModificacion;
	private $_fechaInicioActividad;
	private $_fechaFinActividad;
	private $_objetivos;
	private $_descripcion;
	private $_nroParticipantes;
	private $_responsablesRendicionAvancePartidas;
	private $_observaciones;
	private $_usuaLogin;
	private $_dependencia;
	private $_montoAnticipo;
	private $_montoTotal;
	
	public function __construct(){
		$this->_fechaRendicion = date("d/m/Y");
		$this->_fechaRegistro = date("d/m/Y H:i:s");
		$this->_fechaUltimaModificacion = date("d/m/Y H:i:s");
		
		/* Parche para que las rendiciones de Avance funcionen en el año anterior */
		/*$this->_fechaRendicion = "30/12/2014";
		$this->_fechaRegistro = "30/12/2014" . date(" H:i:s");
		$this->_fechaUltimaModificacion = "30/12/2014" . date(" H:i:s");*/
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetAvance(){
		return $this->_avance;
	}
	public function SetAvance(EntidadAvance $avance = null){
		$this->_avance = $avance;
	}
	public function GetFechaRendicion(){
		return $this->_fechaRendicion;
	}
	public function SetFechaRendicion($fechaRendicion){
		$this->_fechaRendicion = $fechaRendicion;
	}
	public function GetFechaRegistro(){
		return $this->_fechaRegistro;
	}
	public function SetFechaRegistro($fechaRegistro){
		$this->_fechaRegistro = $fechaRegistro;
	}
	public function GetFechaUltimaModificacion(){
		return $this->_fechaUltimaModificacion;
	}
	public function SetFechaUltimaModificacion($fechaUltimaModificacion){
		$this->_fechaUltimaModificacion = $fechaUltimaModificacion;
	}
	public function GetFechaInicioActividad(){
		return $this->_fechaInicioActividad;
	}
	public function SetFechaInicioActividad($fechaInicioActividad){
		$this->_fechaInicioActividad = $fechaInicioActividad;
	}
	public function GetFechaFinActividad(){
		return $this->_fechaFinActividad;
	}
	public function SetFechaFinActividad($fechaFinActividad){
		$this->_fechaFinActividad = $fechaFinActividad;
	}
	public function GetObjetivos(){
		return $this->_objetivos;
	}
	public function SetObjetivos($objetivos){
		$this->_objetivos = $objetivos;
	}
	public function GetDescripcion(){
		return $this->_descripcion;
	}
	public function SetDescripcion($descripcion){
		$this->_descripcion = $descripcion;
	}
	public function GetNroParticipantes(){
		return $this->_nroParticipantes;
	}
	public function SetNroParticipantes($nroParticipantes){
		$this->_nroParticipantes = $nroParticipantes;
	}
	public function GetResponsablesRendicionAvancePartidas(){
		return $this->_responsablesRendicionAvancePartidas;
	}
	public function SetResponsablesRendicionAvancePartidas(array $responsablesRendicionAvancePartidas = null){
		$this->_responsablesRendicionAvancePartidas = $responsablesRendicionAvancePartidas;
	}
	public function GetObservaciones(){
		return $this->_observaciones;
	}
	public function SetObservaciones($observaciones){
		$this->_observaciones = $observaciones;
	}
	public function GetUsuaLogin(){
		return $this->_usuaLogin;
	}
	public function SetUsuaLogin($usuaLogin){
		$this->_usuaLogin = $usuaLogin;
	}
	public function GetDependencia(){
		return $this->_dependencia;
	}
	public function SetDependencia(EntidadDependencia $dependencia = null){
		$this->_dependencia = $dependencia;
	}
	public function CalcularSubtotalesPorResponsable(){
		if(is_array($this->_responsablesRendicionAvancePartidas))
		{
			foreach ($this->_responsablesRendicionAvancePartidas as $responsableRendicionAvancePartidas)
			{
				$responsableRendicionAvancePartidas->GetMontoTotal();
			}
		}
	}
	public function GetMontoAnticipo(){
		if($this->_montoAnticipo == null){
			$montoAnticipo = 0;
			
			if(is_array($this->_responsablesRendicionAvancePartidas)){
				foreach ($this->_responsablesRendicionAvancePartidas as $responsableRendicionAvancePartidas)
				{
					$monto = $responsableRendicionAvancePartidas->GetMontoAnticipo();
					if($monto != null && trim($monto) != ''){
						$montoAnticipo += $monto;
					}
				}
			}
			$this->_montoAnticipo = $montoAnticipo;
		}
		
		return $this->_montoAnticipo;
	}
	public function GetMontoTotal(){
		if($this->_montoTotal === null){
			$montoTotal = 0;
		
			if(is_array($this->_responsablesRendicionAvancePartidas)){
				foreach ($this->_responsablesRendicionAvancePartidas as $responsablesRendicionAvancePartidas)
				{
					$monto = $responsablesRendicionAvancePartidas->GetMontoTotal();
					if($monto != null && trim($monto) != ''){
						$montoTotal += $monto;
					} 
				}
			}
			$this->_montoTotal = $montoTotal;
		}
			
		return $this->_montoTotal;
	}
	public function __toString()
	{
		// Datos de los responsables del avance
		$idResponsablesRendicionAvance = array();
		if(is_array($this->_responsablesRendicionAvancePartidas))
		{
			foreach ($this->_responsablesRendicionAvancePartidas as $responsableRendicionAvancePartidas)
			{
				$idResponsablesRendicionAvance[] = $responsableRendicionAvancePartidas->GetResponsableRendicionAvance()->GetId();
			}
		}
		
		return "
			Id = " . $this->GetId() . ",
			FechaRendicion = " .$this->GetFechaRendicion() . ",
			FechaRegistro = " .$this->GetFechaRegistro() . ",
			FechaUltimaModificacion = " .$this->GetFechaUltimaModificacion() . ",
			FechaInicioActividad = " .$this->GetFechaInicioActividad() . ",
			FechaFinActividad = " .$this->GetFechaFinActividad() . ",
			Objetivos = " .$this->GetObjetivos() . ",
			Descripcion = " .$this->GetDescripcion() . ",
			Nroparticipantes = " .$this->GetNroParticipantes() . ",
			ResponsablesRendicionAvancePartidas = (" . implode(", ", $idResponsablesRendicionAvance) . "),
			Observaciones " .$this->GetObservaciones() . "
		";
	}
	
	public function __clone()
	{
		$responsablesRendicionAvancePartidas = null;
		if(is_array($this->_responsablesRendicionAvancePartidas)){
			$responsablesRendicionAvancePartidas = array();
			foreach($this->_responsablesRendicionAvancePartidas as $responsableRendicionAvancePartidas){
				$responsablesRendicionAvancePartidas[] = clone $responsableRendicionAvancePartidas;
			}
		}
		
		$this->_responsablesRendicionAvancePartidas = $responsablesRendicionAvancePartidas;
	}
	
	public function UTF8Encode()
	{
		if(is_array($this->_responsablesRendicionAvancePartidas)){
			foreach($this->_responsablesRendicionAvancePartidas as &$responsableRendicionAvancePartidas){
				$responsableRendicionAvancePartidas->UTF8Encode();
			}
			unset($responsableRendicionAvancePartidas);
		}
		
		$this->_id = utf8_encode($this->_id);		
		$this->_fechaRendicion = utf8_encode($this->_fechaRendicion);
		$this->_fechaRegistro = utf8_encode($this->_fechaRegistro);
		$this->_fechaUltimaModificacion = utf8_encode($this->_fechaUltimaModificacion);
		$this->_fechaInicioActividad = utf8_encode($this->_fechaInicioActividad);
		$this->_fechaFinActividad = utf8_encode($this->_fechaFinActividad);
		$this->_objetivos = utf8_encode($this->_objetivos);
		$this->_descripcion = utf8_encode($this->_descripcion);
		$this->_nroParticipantes = utf8_encode($this->_nroParticipantes);
		$this->_observaciones = utf8_encode($this->_observaciones);
		
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
			$responsablesRendicionAvancePartidas = null;
			if(is_array($this->_responsablesRendicionAvancePartidas)){
				$responsablesRendicionAvancePartidas = array();
				foreach($this->_responsablesRendicionAvancePartidas as $responsableRendicionAvancePartidas){
					$responsablesRendicionAvancePartidas[] = $responsableRendicionAvancePartidas->ToArray(); 
				}
			}
			
			$data = array(
				'id' => $this->GetId(),
				'fechaRendicion' => $this->GetFechaRendicion(),
				'fechaRegistro' => $this->GetFechaRegistro(),
				'fechaUltimaModificacion' => $this->GetFechaUltimaModificacion(),
				'fechaInicioActividad' => $this->GetFechaInicioActividad(),
				'fechaFinActividad' => $this->GetFechaFinActividad(),
				'objetivos' => $this->GetObjetivos(),
				'descripcion' => $this->GetDescripcion(),
				'nroparticipantes' => $this->GetNroParticipantes(),
				'responsablesRendicionAvancePartidas' => $responsablesRendicionAvancePartidas,
				'observaciones' => $this->GetObservaciones()
			);
		}
		
		return $data;
	}
	
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}
?>