<?php
class Formularios
{
	const TIPO_OPERACION_INSERTAR = 1;
	const TIPO_OPERACION_MODIFICAR = 2;
	
	private $_tipoOperacion = self::TIPO_OPERACION_INSERTAR;
	
	public function GetTipoOperacion(){
		return $this->_tipoOperacion;
	}
	public function SetTipoOperacion($tipoOperacion){
		$this->_tipoOperacion = $tipoOperacion;
	}
}