<?php
require_once (SAFI_ENTIDADES_PATH.'/compromisoAsunto.php');
require_once (SAFI_ENTIDADES_PATH.'/tipoActividadCompromiso.php');
require_once (SAFI_ENTIDADES_PATH.'/tipoEvento.php');
include_once(SAFI_ENTIDADES_PATH . '/estado.php');
include_once(SAFI_ENTIDADES_PATH . '/tipoActividadCompromiso.php');
include_once(SAFI_ENTIDADES_PATH . '/tipoSolicitudPago.php');
require_once (SAFI_ENTIDADES_PATH.'/puntoCuenta.php');
require_once (SAFI_ENTIDADES_PATH.'/usuario.php');
require_once (SAFI_ENTIDADES_PATH.'/empleado.php');
 require_once (SAFI_ENTIDADES_PATH.'/controlinterno.php');

class EntidadCompromiso{

	private $_id;
	private $_asunto;
	private $_documento;
	private $_tipoDoc;
	private $_descripcion;
	private $_fecha;
	private $_estatus;
	private $_usuario;
	private $_dependencia;
	private $_observacion;
	private $_justificacion;
	private $_lapso;
	private $_condicionPago;
	private $_montoSolicitado;
	private $_prioridad;
	private $_numeroReserva;
	private $_gerencia;
	private $_recursos;
	private $_compEstatus;
	private $_compDependencia;
	private $_pcta;
	private $_rifProveedorSugerido;
	private $_actividad;
	private $_fechaReporte;
	private $_localidad;
	private $_beneficiario;
	private $_evento;
	private $_fechaInicio;
	private $_fechaFin;
	private $_controlInterno;
	private $_participante;
	private $_infocentro;
	private $_compromisoImputas;
	private $_control;



	public function __construct(){

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

	public function SetAsunto(EntidadCompromisoAsunto $asunto = null){
		$this->_asunto = $asunto;
	}


	public function GetIdDocumento(){
		return $this->_documento;
	}
	public function SetIdDocumento($documento){
		$this->_documento = $documento;
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





	public function GetUsuario(){
		return $this->_usuario;
	}
	public function SetUsuario(EntidadEmpleado $usuario = null){
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
		$this->_lapso= $lapso;
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
		$this->_montoSolicitado= $montoSolicitado;
	}





	public function GetPrioridad(){
		return $this->_prioridad;
	}
	public function SetPrioridad($prioridad){
		$this->_prioridad= $prioridad;
	}





	public function GetNumeroReserva(){
		return $this->_numeroReserva;
	}
	public function SetNumeroReserva($numeroReserva){
		$this->_numeroReserva= $numeroReserva;
	}





	public function GetGerencia(){
		return $this->_gerencia;
	}
	public function SetGerencia(EntidadDependencia $gerencia = null){
		$this->_gerencia= $gerencia;
	}





	public function GetRecursos(){
		return $this->_recursos;
	}
	public function SetRecursos($recursos){
		$this->_recursos= $recursos;
	}





	public function GetCompEstatus(){
		return $this->_compEstatus;
	}
	public function SetCompEstatus($compEstatus){
		$this->_compEstatus= $compEstatus;
	}





	public function GetCompDependencia(){
		return $this->compDependencia;
	}
	public function SetCompDependencia(EntidadDependencia $compDependencia = null){
		$this->_compDependencia= $compDependencia;
	}







	public function GetPcta(){
		return $this->_pcta;
	}
	public function SetPcta($pcta = null){
		$this->_pcta= $pcta;
	}





	public function GetRifProveedorSugerido(){

		return $this->_rifProveedorSugerido;
	}
	public function SetRifProveedorSugerido($rifProveedorSugerido){
		$this->_rifProveedorSugerido = $rifProveedorSugerido;
	}





	public function GetActividad(){
		return $this->_actividad;
	}
	public function SetActividad(EntidadTipoSolicitudPago $actividad = null){
		$this->_actividad= $actividad;
	}



	public function GetFechaReporte(){
		return $this->_fechaReporte;
	}
	public function SetFechaReporte($fechaReporte){
		$this->_fechaReporte= $fechaReporte;
	}





	public function GetLocalidad(){
		return $this->_localidad;
	}
	public function SetLocalidad(EntidadEstadosVenezuela $localidad = null){
		$this->_localidad= $localidad;
	}




	public function GetBeneficiario(){
		return $this->_beneficiario;
	}
	public function SetBeneficiario($beneficiario){
		$this->_beneficiario= $beneficiario ;
	}




	public function GetEvento(){
		return $this->_evento;
	}
	public function SetEvento(EntidadTipoEvento $evento = null){
		$this->_evento= $evento  ;
	}




	public function GetFechaInicio(){
		return $this->_fechaInicio;

	}
	public function SetFechaInicio($fechaInicio){
		$this->_fechaInicio= $fechaInicio;
	}





	public function SetFechaFin($fechaFin){
		$this->_fechaFin = $fechaFin;
	}
	public function GetFechaFin(){
		return $this->_fechaFin;
	}



	public function GetControlInterno(){
		return $this->_controlInterno;
	}
	
	public function SetControlInterno(EntidadControlinterno $controlInterno = null){
		$this->_controlInterno = $controlInterno ;
	}

	
	public function GetParticipante(){
		return $this->_participante;
	}
	public function SetParticipante($participante){
		$this->_participante= $participante ;
	}



	public function GetInfocentro(){
		return $this->_infocentro;
	}
	public function SetInfocentro($infocentro){
		$this->_infocentro= $infocentro ;
	}




	public function GetCompromisoImputas(){
		return $this->_compromisoImputas;
	}

	public function SetCompromisoImputas(array $compromisoImputas = null){
		$this->_compromisoImputas = $compromisoImputas;

	}
	
	
  
	
	public function __toString()
	{
		// PuntoCuentaImputas = ".($this->_puntoCuentaImputa !== null ? $this->_puntoCuentaImputa : "NULL").", y respaldos
		return "
		   
			Id = ".$this->_id.",
			Asunto = ".($this->_asunto !== null ? $this->_asunto : "NULL").",
			Documento = ".$this->_documento.",
			TipoDoc = ".$this->_tipoDoc.",
			Descripcion = ".$this->_descripcion.",
			Fecha = ".$this->_fecha.",
			Estatus = ".($this->_estatus !== null ? $this->_estatus : "NULL").",
			Usuario = ".($this->_usuario !== null ? $this->_usuario : "NULL").",
			Dependencia = ".($this->_dependencia !== null ? $this->_dependencia : "NULL").",
			Observacion = ".$this->_observacion.",
			Justificacion = ".$this->_justificacion.",
			Lapso = ".$this->_lapso.",
			CondicionPago = ".$this->_condicionPago.",
			MontoSolicitado = ".$this->_montoSolicitado.",
			NumeroReserva = ".$this->_numeroReserva.",
			Gerencia = ".($this->_gerencia !== null ? $this->_gerencia : "NULL").",
			Recursos = ".$this->_recursos.",
			CompEstatus = ".$this->_compEstatus.",
			CompDependencia = ".($this->_compDependencia !== null ? $this->_compDependencia : "NULL").",
			Pcta = ".$this->_pcta.",
			RifProveedorSugerido = ".$this->_rifProveedorSugerido.",
			Actividad = ".($this->_actividad!== null ? $this->_actividad : "NULL").",
			FechaReporte = ".$this->_fechaReporte.",
			Localidad = ".($this->_localidad !== null ? $this->_localidad : "NULL").",
			Beneficiario = ".$this->_beneficiario.",
			Evento = ".($this->_evento !== null ? $this->_evento : "NULL").",
			fechaInicio = ".$this->_fechaInicio.",
			fechaFin = ".$this->_fechaFin.",
			Participante = ".$this->_participante.",
			ControlInterno = ".($this->_controlInterno !== null ? $this->_controlInterno : "NULL").",
			Infocentro = ".($this->_infocentro !== null ? $this->_infocentro : "NULL")."		
		";
	}


	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		if($this->_asunto !== null)$this->_asunto =  $this->_asunto->UTF8Encode();
		$this->_documento = utf8_encode($this->_documento);
		
		if($this->_usuario  !== null)$this->_usuario =  $this->_usuario->UTF8Encode();
		$this->_tipoDoc = utf8_encode($this->_tipoDoc);
		$this->_descripcion = utf8_encode($this->_descripcion);
		$this->_fecha = utf8_encode($this->_fecha);
		if($this->_estatus !== null) $this->_estatus->UTF8Encode();
		if($this->_dependencia !== null)  $this->_dependencia->UTF8Encode() ;
		if($this->_observacion !== null) $this->_observacion = utf8_encode($this->_observacion);
		if($this->_justificacion !== null)$this->_justificacion = utf8_encode($this->_justificacion);
		if($this->_lapso !== null)$this->_lapso = utf8_encode($this->_lapso);
		$this->_condicionPago = utf8_encode($this->_condicionPago);
		$this->_montoSolicitado = utf8_encode($this->_montoSolicitado);
		$this->_numeroReserva = utf8_encode($this->_numeroReserva);
		if($this->_gerencia !== null) $this->_gerencia->UTF8Encode();
		$this->_recursos = utf8_encode($this->_recursos);
		$this->_compEstatus = utf8_encode($this->_compEstatus);
		if($this->_compDependencia !== null) $this->_compDependencia = $this->_compDependencia ;
		$this->_pcta = utf8_encode($this->_pcta);
		$this->_rifProveedorSugerido = utf8_encode($this->_rifProveedorSugerido);
		if($this->_actividad !== null) $this->_actividad->UTF8Encode();
		$this->_fechaReporte = utf8_encode($this->_fechaReporte);
		if($this->_localidad !== null) $this->_localidad->UTF8Encode();
		$this->_beneficiario = utf8_encode($this->_beneficiario);
		if($this->_evento !== null) $this->_evento->UTF8Encode();
		$this->_fechaInicio = utf8_encode($this->_fechaInicio);
		$this->_fechaFin = utf8_encode($this->_fechaFin);
		$this->_participante = utf8_encode($this->_participante);
		if($this->_controlInterno !== null) $this->_controlInterno->UTF8Encode();
		if($this->_infocentro !== null) $this->_infocentro->UTF8Encode();
		
		if($this->_compromisoImputas){
			foreach ( $this->_compromisoImputas as $CompromisoImputas){

				$CompromisoImputas->UTF8Encode();
					
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
			'asunto' => ($this->_asunto !== null ? $this->_asunto->ToArray() : null),
			'documento' => $this->_documento,
			'tipoDoc' => $this->_tipoDoc,
			'descripcion' => $this->_descripcion,
			'fecha' => $this->_fecha,
			'estatus' => ($this->_estatus !== null ? $this->_estatus->ToArray() : null),
			'usuario' => ($this->_usuario !== null ? $this->_usuario->ToArray() : null),
			'dependencia' => ($this->_dependencia !== null ? $this->_dependencia->ToArray() : null),
			'Observacion' => $this->_observacion,
			'justificacion' => $this->_justificacion,
			'lapso' => $this->_lapso,
			'condicionPago' => $this->_condicionPago,
			'montoSolicitado' => $this->_montoSolicitado,
			'mNumeroReserva' => $this->_numeroReserva,
			'gerencia' => ($this->_gerencia !== null ? $this->_gerencia->ToArray() : null),
			'recursos' => $this->_recursos,
			'compEstatus' => $this->_compEstatus,
			'compDependencia' => ($this->_compDependencia !== null ? $this->_compDependencia->ToArray() : null),
			'pcta' => $this->_pcta,
			'rifProveedorSugerido' => $this->_rifProveedorSugerido,
			'actividad' => ($this->_actividad!== null ? $this->_actividad->ToArray() : null),
			'fechaReporte' => $this->_fechaReporte,
			'localidad' => ($this->_localidad !== null ? $this->_localidad->ToArray() : null),
			'beneficiario' => $this->_beneficiario,
			'evento' => ($this->_evento !== null ? $this->_evento->ToArray() : null),
			'fechaInicio' => $this->_fechaInicio,
			'fechaFin' => $this->_fechaFin,
			'participante' => $this->_participante,
			'controlInterno' => ($this->_controlInterno !== null ? $this->_controlInterno->ToArray() : null),
			'infocentro' => ($this->_infocentro !== null ? $this->_infocentro->ToArray() : null)
			);
				
			if($this->_compromisoImputas){
			foreach ( $this->_compromisoImputas as $CompromisoImputas){

				 $data['compromisoImputas'][] = $CompromisoImputas->ToArray();
					
			}
			 
		}
		
	   
		}

		return $data;
	}


}


