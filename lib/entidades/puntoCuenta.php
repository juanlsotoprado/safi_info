<?php
require_once (SAFI_ENTIDADES_PATH.'/puntoCuentaImputa.php');
require_once (SAFI_ENTIDADES_PATH.'/puntoCuentaAsunto.php');
require_once (SAFI_ENTIDADES_PATH.'/empleado.php');
require_once (SAFI_ENTIDADES_PATH.'/estatus.php');
require_once (SAFI_ENTIDADES_PATH.'/usuario.php');
require_once (SAFI_ENTIDADES_PATH.'/dependencia.php');
require_once (SAFI_ENTIDADES_PATH.'/infocentro.php');

class EntidadPuntoCuenta
{
	private $_id;  // Código de identificacion del punto de cuenta
	private $_asunto; // Asunto al cual se refiere el punto de cuenta
	private $_rifProveedorSugerido; // Rif del proveedor sugerido
	private $_descripcion; // Relación expuesta del punto de cuenta
	private $_fecha; // Fecha de elaboración del punto de cuenta
	private $_remitente;
	private $_presentadoPor;
	private $_estatus;
	private $_recursos;
	private $_usuario;
	private $_dependencia;
	private $_observacion;
	private $_justificacion;
	private $_destinatario;
	private $_lapso;
	private $_condicionPago;
	private $_montoSolicitado;
	private $_garantia;
	private $_observacioneDireccionEjecutiva;
	private $_observacionePresidencia;
	private $_descripcionPresupuesto;
	private $_puntoCuentaAsociado;
	private $_puntoCuentasImputas;
	private $_puntoCuentasRespaldos;
	private $_infocentro;
	
	public function __construct(){
	
	}
	public function GetDestinatario(){
		return $this->_destinatario;
	}
	public function SetDestinatario($destinatario){
		$this->_destinatario = $destinatario;
	}
    public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetAsunto(){
		return $this->_asunto;
	}
	public function SetAsunto(EntidadPuntoCuentaAsunto $asunto = null){
		$this->_asunto = $asunto;
	}
	public function GetRifProveedorSugerido(){
		return $this->_rifProveedorSugerido;
	}
	public function SetRifProveedorSugerido($rifProveedorSugerido){
		$this->_rifProveedorSugerido = $rifProveedorSugerido; 
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
		$this->_fecha = $fecha;
	}
	public function GetRemitente(){
		return $this->_remitente;
	}
	public function SetRemitente(EntidadEmpleado $remitente = null){
		$this->_remitente = $remitente;
	}
    public function GetPresentadoPor(){
		return $this->_presentadoPor;
	}
	public function SetPresentadoPor(EntidadEmpleado $remitente = null){
		$this->_presentadoPor = $remitente;
	}
	
	public function GetEstatus(){
		return $this->_estatus;
	}
	public function SetEstatus(EntidadEstatus $estatus = null){
		$this->_estatus = $estatus;
	}
	
    public function GetRecursos(){
		return $this->_recursos;
	}
	public function SetRecursos($recursos){
		$this->_recursos = $recursos;
	}
	public function GetUsuario(){
		return $this->_usuario;
	}
	public function SetUsuario(EntidadUsuario $usuario = null){
		$this->_usuario = $usuario;
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
	public function GetJustificacion(){
		return $this->_justificacion;
	}
	public function SetJustificacion($justificacion){
		$this->_justificacion = $justificacion;
	}
	public function GetLapso(){
		return $this->_lapso;
	}
	public function SetLapso($lapso){
		$this->_lapso = $lapso;
	}
	public function GetCondicionPago(){
		return $this->_condicionPago;
	}
	public function SetCondicionPago($condicionPago){
		$this->_condicionPago = $condicionPago;
	}
	public function GetMontoSolicitado(){
		return $this->_montoSolicitado;
	}
	public function SetMontoSolicitado($montoSolicitado){
		$this->_montoSolicitado = $montoSolicitado;
	}
	public function GetGarantia(){
		return $this->_garantia;
	}
	public function SetGarantia($garantia){
		$this->_garantia = $garantia;
	}
	public function GetObservacioneDireccionEjecutiva(){
		return $this->_observacioneDireccionEjecutiva;
	}
	public function SetObservacioneDireccionEjecutiva($observacioneDireccionEjecutiva){
		$this->_observacioneDireccionEjecutiva = $observacioneDireccionEjecutiva;
	}
	public function GetObservacionePresidencia(){
		return $this->_observacionePresidencia;
	}
	public function SetObservacionPresidencia($observacionPresidencia){
		$this->_observacionePresidencia = $observacionPresidencia;
	}
	public function GetDescripcionPresupuesto(){
		return $this->_descripcionPresupuesto;
	}
	public function SetDescripcionPresupuesto($descripcionPresupuesto){
		$this->_descripcionPresupuesto = $descripcionPresupuesto;
	}
	public function GetPuntoCuentaAsociado(){
		return $this->_puntoCuentaAsociado;
	}
	public function SetPuntoCuentaAsociado(EntidadPuntoCuenta $puntoCuentaAsociado = null){
		$this->_puntoCuentaAsociado = $puntoCuentaAsociado;
	}
    public function GetPuntoCuentasImputas(){
		return $this->_puntoCuentasImputas;
	}
	public function SetPuntoCuentasImputas(array $puntoCuentasImputas = null){
		$this->_puntoCuentasImputas = $puntoCuentasImputas;
	}
     public function GetPuntoCuentasRespaldos(){
		return $this->_puntoCuentasRespaldos;
	}
	public function SetPuntoCuentasRespaldos(array $puntoCuentasRespaldos = null){
		$this->_puntoCuentasRespaldos = $puntoCuentasRespaldos;
	}
	public function GetInfocentro(){
		return $this->_infocentro;
	}
	public function SetInfocentro(EntidadInfocentro $infocentro = null){
		$this->_infocentro = $infocentro;
	}
	public function __toString()
	{
		// PuntoCuentaImputas = ".($this->_puntoCuentaImputa !== null ? $this->_puntoCuentaImputa : "NULL").", y respaldos
		return "
		
		
			Id = ".$this->_id.",
			Asunto = ".($this->_asunto !== null ? $this->_asunto : "NULL").",
			RifProveedorSugerido = ".$this->_rifProveedorSugerido.",
			Descripcion = ".$this->_descripcion.",
			Fecha = ".$this->_fecha.",
			Remitente = ".($this->_remitente !== null ? $this->_remitente : "NULL").",
		    PresentadoPor = ".($this->_presentadoPor !== null ? $this->_presentadoPor : "NULL").",
			Estatus = ".($this->_estatus !== null ? $this->_estatus : "NULL").",
			Usuario = ".($this->_usuario !== null ? $this->_usuario : "NULL").",
			Dependencia = ".($this->_dependencia !== null ? $this->_dependencia : "NULL").",
			Observacion = ".$this->_observacion.",
		    Recursos = ".$this->_recursos.",
			destinatario = ".$this->_destinatario.",
			Justificacion = ".$this->_justificacion.",
			Lapso = ".$this->_lapso.",
			CondicionPago = ".$this->_condicionPago.",
			MontoSolicitado = ".$this->_montoSolicitado.",
			Garantia = ".$this->_garantia.",
			ObservacionDireccionEjecutiva = ".$this->_observacioneDireccionEjecutiva.",
			OnservacionPresidencia = ".$this->_observacionePresidencia.",
			DescripcionPresupuesto = ".$this->_descripcionPresupuesto.",
			PuntoCuentaAsociado = ".($this->_puntoCuentaAsociado !== null ? $this->_puntoCuentaAsociado : "NULL").",
			Infocentro = ".($this->_infocentro !== null ? $this->_infocentro : "NULL")."
		";
	}
	public function __clone()
	{
		$this->_destinatario = ($this->_destinatario !== null) ? clone $this->_destinatario : null;
		$this->_asunto = ($this->_asunto !== null) ? clone $this->_asunto : null;
		$this->_remitente = ($this->_remitente !== null) ? clone $this->_remitente : null;
		$this->_presentadoPor = ($this->_presentadoPor !== null) ? clone $this->_presentadoPor : null;
		$this->_estatus = ($this->_estatus !== null) ? clone $this->_estatus : null;
		$this->_usuario = ($this->_usuario !== null) ? clone $this->_usuario : null;
		$this->_dependencia = ($this->_dependencia !== null) ? clone $this->_dependencia : null;
		$this->_puntoCuentaAsociado = ($this->_puntoCuentaAsociado !== null) ? clone $this->_puntoCuentaAsociado : null;
        $this->_puntoCuentaImputa = ($this->_puntoCuentaImputa !== null) ? clone $this->_puntoCuentaImputa : null;
        $this->_puntoCuentaRespaldo = ($this->_puntoCuentaRespaldo !== null) ? clone $this->_puntoCuentaRespaldo : null;
        
		$this->_infocentro = ($this->_infocentro !== null) ? clone $this->_infocentro : null;
	}
	
	public function UTF8Encode()
	{	
		$this->_id = utf8_encode($this->_id);
		if($this->_asunto !== null) $this->_asunto->UTF8Encode();
		$this->_rifProveedorSugerido = utf8_encode($this->_rifProveedorSugerido);
		$this->_descripcion = utf8_encode($this->_descripcion);
		$this->_destinatario = utf8_encode($this->_destinatario);
		$this->_fecha = utf8_encode($this->_fecha);
		if($this->_remitente !== null) $this->_remitente->UTF8Encode();
		if($this->_presentadoPor !== null) $this->_presentadoPor->UTF8Encode();
		if($this->_estatus !== null) $this->_estatus->UTF8Encode();
		if($this->_usuario !== null) $this->_usuario->UTF8Encode();
		if($this->_dependencia !== null) $this->_dependencia->UTF8Encode();
		if($this->_observacion !== null) $this->_observacion = utf8_encode($this->_observacion);
		if($this->_justificacion !== null)$this->_justificacion = utf8_encode($this->_justificacion);
		if($this->_lapso !== null)$this->_lapso = utf8_encode($this->_lapso);
		

		if($this->_condicionPago !== null)$this->_condicionPago = utf8_encode($this->_condicionPago);
		$this->_montoSolicitado = utf8_encode($this->_montoSolicitado);
		$this->_garantia = utf8_encode($this->_garantia);
		$this->_recursos = utf8_encode($this->_recursos);
		$this->_observacioneDireccionEjecutiva = utf8_encode($this->_observacioneDireccionEjecutiva);
		$this->_observacionePresidencia = utf8_encode($this->_observacionePresidencia);
		$this->_descripcionPresupuesto = utf8_encode($this->_descripcionPresupuesto);
		if($this->_puntoCuentaAsociado !== null) $this->_puntoCuentaAsociado->UTF8Encode();
		if($this->_infocentro !== null) $this->_infocentro->UTF8Encode();
		
		
	if($this->_puntoCuentasImputas){
			  foreach ( $this->_puntoCuentasImputas as $puntoCuentaImputa){	
			  	
			  $puntoCuentaImputa->UTF8Encode();
			
			  }
	  
	      }
	      
	if($this->_puntoCuentasRespaldos){
			  foreach ( $this->_puntoCuentasRespaldos as $puntoCuentaRespaldo){	
			  	
			  $puntoCuentaRespaldo->UTF8Encode();
			
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
				'asunto' => ($this->_asunto !== null) ? $this->_asunto->ToArray() : null,
				'rifProveedorSugerido' => $this->_rifProveedorSugerido,
				'descripcion' => $this->_descripcion,
				'fecha' => $this->_fecha,
				'destinatario' =>$this->_destinatario,
			    'remitente' => ($this->_remitente !== null) ? $this->_remitente->ToArray() : null,
			    'presentadoPor'  => ($this->_presentadoPor !== null) ? $this->_presentadoPor->ToArray(): null,
				'estatus' => ($this->_estatus !== null) ? $this->_estatus->ToArray() : null,
				'usuario' => ($this->_usuario !== null) ? $this->_usuario->ToArray() : null,
				'dependencia' => ($this->_dependencia !== null) ? $this->_dependencia->ToArray() : null,
				'observacion' => $this->_observacion,
				'justificacion' => $this->_justificacion,
				'lapso' => $this->_lapso,
				'condicionPago' => $this->_condicionPago,
				'montoSolicitado' => $this->_montoSolicitado,
				'garantia' => $this->_garantia,
			    'recursos' => $this->_recursos,
				'observacionDireccionEjecutiva' => $this->_observacioneDireccionEjecutiva,
				'observacionPresidencia' => $this->_observacionePresidencia,
				'descripcionPresupuesto' => $this->_descripcionPresupuesto,
				'puntoCuentaAsociado' => ($this->_puntoCuentaAsociado !== null) ? $this->_puntoCuentaAsociado->ToArray() : null,
				'infocentro' => ($this->_infocentro !== null) ? $this->_infocentro->ToArray() : null
			);
		
			
			
			if($this->_puntoCuentasImputas){
			  foreach ( $this->_puntoCuentasImputas as $puntoCuentaImputa){	
			  	
			  $data['puntoCuentaImputa'][] = $puntoCuentaImputa->ToArray();
			
			  }
			  
	      }
	      
		if($this->_puntoCuentasRespaldos){
			  foreach ( $this->_puntoCuentasRespaldos as $puntoCuentaRespaldo){	
			  	
			  $data['puntoCuentaRespaldo'][] = $puntoCuentaRespaldo->ToArray();
			
			  }
			  
	      }
	      
	      
	      
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}