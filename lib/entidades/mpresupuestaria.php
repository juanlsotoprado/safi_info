<?php
require_once (SAFI_ENTIDADES_PATH.'/usuario.php');
require_once (SAFI_ENTIDADES_PATH.'/empleado.php');
require_once (SAFI_ENTIDADES_PATH.'/mpresupuestariaimputa.php');


class EntidadMpresupuestaria{
	
	

	private $_id;
	private $_descripcion;
	private $_fecha;
	private $_dependencia;
	private $_estatus;
	private $_anno;
	private $_observacion;
	private $_tipoDoc;
	private $_mpresupuestariaImputas;
	private $_mpresupuestariasRespaldos;
	

   public function GetMpresupuestariasRespaldos(){
		return $this->_mpresupuestariasRespaldos;
	}
	public function SetMpresupuestariasRespaldos(array $puntoCuentasRespaldos = null){
		$this->_mpresupuestariasRespaldos = $puntoCuentasRespaldos;
	}

	public function __construct(){

	}
	
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}



	public function GetTipoDoc(){
		return $this->_tipoDoc;
	}
	public function SetTipoDoc($tipoDoc){
		$this->_tipoDoc= $tipoDoc;
	}




	public function GetDescripcion(){
		return $this->_descripcion;
	}
	public function SetDescripcion($descripcion){
		$this->_descripcion = $descripcion;
	}


	public function GetFecha(){
		return $this->_fecha;
	}
	public function SetFecha($fecha){
		$this->_fecha= $fecha;
	}


	public function GetEstatus(){
		return $this->_estatus;
	}

	public function SetEstatus(EntidadEstatus $estatus = null){
		$this->_estatus = $estatus;
	}



	public function GetDependencia(){
		return $this->_dependencia;
	}
	public function SetDependencia(EntidadDependencia $dependencia = null){
		$this->_dependencia = $dependencia;
	}


	public function GetObservacion(){
		return $this->_observacion;
	}
	public function SetObservacion($observacion){
		$this->_observacion = $observacion;
	}





	public function Anno(){
		return $this->_anno;
	}
	public function SetAnno($anno = null){
		$this->_anno= $anno;
	}



	public function GetMpresupuestariasImputas(){
		return $this->_mpresupuestariaImputas;
	}

	public function SetMpresupuestariasImputas(array $mpresupuestariasImputas = null){
		$this->_mpresupuestariaImputas = $mpresupuestariasImputas;

	}
	
	

	
	public function __toString()
	{
		// PuntoCuentaImputas = ".($this->_puntoCuentaImputa !== null ? $this->_puntoCuentaImputa : "NULL").", y respaldos
		return "
		   
			Id = ".$this->_id.",
			TipoDoc = ".$this->_tipoDoc.",
			Descripcion = ".$this->_descripcion.",
			Fecha = ".$this->_fecha.",
			Estatus = ".($this->_estatus !== null ? $this->_estatus : "NULL").",
			Dependencia = ".($this->_dependencia !== null ? $this->_dependencia : "NULL").",
			Observacion = ".$this->_observacion.",
			Anno = ".$this->_anno."
		";
	}


	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_tipoDoc = utf8_encode($this->_tipoDoc);
		$this->_descripcion = utf8_encode($this->_descripcion);
		$this->_fecha = utf8_encode($this->_fecha);
		if($this->_estatus !== null) $this->_estatus->UTF8Encode();
		if($this->_dependencia !== null)  $this->_dependencia->UTF8Encode() ;
		if($this->_observacion !== null) $this->_observacion = utf8_encode($this->_observacion);
		if($this->_anno !== null)$this->_anno= utf8_encode($this->_anno);
		
		if($this->_mpresupuestariaImputas){
			foreach ( $this->_mpresupuestariaImputas as $mpresupuestariaImputas){

				$mpresupuestariaImputas->UTF8Encode();
					
			}
			 
		}
		
		if($this->_mpresupuestariasRespaldos){
			  foreach ( $this->_mpresupuestariasRespaldos as $mpresupuestariasRespaldo){	
			  	
			  $mpresupuestariasRespaldo->UTF8Encode();
			
			  }
	  
	      }
		 

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
			'tipoDoc' => $this->_tipoDoc,
			'descripcion' => $this->_descripcion,
			'fecha' => $this->_fecha,
			'estatus' => ($this->_estatus !== null ? $this->_estatus->ToArray() : null),
			'dependencia' => ($this->_dependencia !== null ? $this->_dependencia->ToArray() : null),
			'Observacion' => $this->_observacion,
			'anno' => $this->_anno
			
			);
				
			if($this->_mpresupuestariaImputas){
			foreach ( $this->_mpresupuestariaImputas as $mpresupuestariaImputas){

				 $data['mpresupuestariaImputas'][] = $mpresupuestariaImputas->ToArray(); 	
			}
			 
		}
		
		if($this->_mpresupuestariasRespaldos){
		

			  foreach ( $this->_mpresupuestariasRespaldos as $mpresupuestariasRespaldo){	
			  	
			$data['respaldos'][] =   $mpresupuestariasRespaldo->ToArray();
			
			  }
	  
	      }
		
	   
		}

		return $data;
	}



}


