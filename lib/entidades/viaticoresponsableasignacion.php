<?php
class EntidadViaticoResponsableAsignacion
{
	private $_viaticoId = 0;
	private $_responsableId = 0;
	private $_asignacionViaticoId = 0;
	private $_monto = 0.0;
	private $_unidadMedida;
	private $_unidades = 0;
	
	public function __construct()
	{
		
	}
	public function GetViaticoId(){
		return $this->_viaticoId;
	}
	public function SetViaticoId($viaticoId){
		$this->_viaticoId = $viaticoId;
	}
	public function GetResponsableId(){
		return $this->_responsableId;
	}
	public function SetResponsableId($responsableId){
		$this->_responsableId = $responsableId;
	}
	public function GetAsignacionViaticoId(){
		return $this->_asignacionViaticoId;
	}
	public function SetAsignacionViaticoId($asignacionViaticoId){
		$this->_asignacionViaticoId = $asignacionViaticoId;
	}
	public function GetMonto(){
		return $this->_monto;
	}
	public function SetMonto($monto){
		$this->_monto = floatval($monto);
	}
	public function GetUnidadMedida(){
		return $this->_unidadMedida;
	}
	public function SetUnidadMedida($unidadMedida){
		$this->_unidadMedida = (int)$unidadMedida;
	}	
	public function GetUnidades(){
		return $this->_unidades;
	}
	public function SetUnidades($unidades){
		$this->_unidades = (int)$unidades;
	}
}