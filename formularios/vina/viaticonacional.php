<?php
include_once(SAFI_FORMULARIOS_PATH . '/formularios.php');
include_once(SAFI_ENTIDADES_PATH . '/infocentro.php');
include_once(SAFI_ENTIDADES_PATH . '/tipotransporte.php');
include_once(SAFI_ENTIDADES_PATH . '/ruta.php');
include_once(SAFI_ENTIDADES_PATH . '/responsableviatico.php');
include_once(SAFI_ENTIDADES_PATH . '/viaticoresponsableasignacion.php');
include_once(SAFI_ENTIDADES_PATH . '/tipocuentabancaria.php');
include_once(SAFI_ENTIDADES_PATH . '/viaticonacional.php');
include_once(SAFI_ENTIDADES_PATH . '/docgenera.php');
include_once(SAFI_ENTIDADES_PATH . '/categoriaviatico.php');
include_once(SAFI_ENTIDADES_PATH . '/red.php');
include_once(SAFI_ENTIDADES_PATH . '/estado.php');

class ViaticoNacionalForm extends Formularios
{
	const TIPO_OPERACION_INSERTAR = 1;
	const TIPO_OPERACION_MODIFICAR = 2;
	
	private $_idViatico;
	private $_tipoProyectoAccionCentralizada = 'proyecto';  // Indica si se seleccionó un proyecto o una acción centralizada
	private $_idProyectoAccionCentralizada = 0;  // Id del proyecto o acción centralizada.
	private $_idAccionEspecifica = 0;  // Id de la acción específica
	private $_fechaViatico;  // Fecha en que se realiza el viatico
	private $_fechaInicioViaje;  // Fecha de inicio de laruta del viatico más cercana
	private $_fechaFinViaje;  // Fecha de fin de la ruta del viatico más lejana
	private $_objetivosViaje;  // Objetivos del viaje
	private $_infocentros = array();  // Arreglo que contiene el Id de los infocentros
	private $_rutas = array();  // Arreglo asociativo que contiene información de las rutas
	private $_tipoResponsable;
	private $_responsable;  // Contiene información del responsable del viatico
							// (Persona a la que se entregará el cheque y realiazará la rendición)
	private $_viaticoResponsableAsignaciones = array();
	private $_proyecto = null;
 	private $_proyectoEspecifica = null;
 	private $_accionCentralizada = null;
 	private $_accionCentralizadaEspecifica = null;
	private $_tipoOperacion = self::TIPO_OPERACION_INSERTAR;
	private $_docGenera;
	private $_observaciones;  // Observaciones del viatico nacional
	private $_categoriaViatico;
	private $_red;
	private $_estado;
	private $_requisiciones = array();
	
	public function __construct()
	{
		$this->_fechaViatico = date('d/m/Y');
		$this->_responsable = new EntidadResponsableViatico();
		$this->_tipoResponsable = EntidadResponsableViatico::TIPO_EMPLEADO;
		$this->_categoriaViatico = new EntidadCategoriaViatico();
		$this->_red = new EntidadRed();
		$this->_estado = new EntidadEstado();
	}
	public function GetIdViatico(){
		return $this->_idViatico;
	}
	public function SetIdViatico($idViatico){
		$this->_idViatico = $idViatico;
	}
	public function GetTipoProyectoAccionCentralizada()
	{
		return $this->_tipoProyectoAccionCentralizada;
	}
	public function SetTipoProyectoAccionCentralizada($tipoProyectoAccionCentralizada)
	{
		$this->_tipoProyectoAccionCentralizada = $tipoProyectoAccionCentralizada;
	}
	public function GetIdProyectoAccionCentralizada()
	{
		return $this->_idProyectoAccionCentralizada;
	}
	public function SetIdProyectoAccionCentralizada($idProyectoAccionCentralizada)
	{
		$this->_idProyectoAccionCentralizada = $idProyectoAccionCentralizada;
	}
	public function GetIdAccionEspecifica()
	{
		return $this->_idAccionEspecifica;
	}
	public function SetIdAccionEspecifica($idAccionEspecifica)
	{
		$this->_idAccionEspecifica = $idAccionEspecifica;
	}
	public function GetFechaViatico()
	{
		return $this->_fechaViatico;
	}
	public function SetFechaViatico($fechaViatico)
	{
		$this->_fechaViatico = $fechaViatico;
	}
	public function GetFechaInicioViaje()
	{
		return $this->_fechaInicioViaje;
	}
	public function SetFechaInicioViaje($fechaInicioViaje)
	{
		$this->_fechaInicioViaje = $fechaInicioViaje;
	}
	public function GetFechaFinViaje()
	{
		return $this->_fechaFinViaje;
	}
	public function SetFechaFinViaje($fechaFinViaje)
	{
		$this->_fechaFinViaje = $fechaFinViaje;
	}
	public function GetObjetivosViaje()
	{
		return $this->_objetivosViaje;
	}
	public function SetObjetivosViaje($objetivosViaje)
	{
		$this->_objetivosViaje = $objetivosViaje;
	}
	public function GetInfocentros()
	{
		return $this->_infocentros;
	}
	public function SetInfocentros($infocentros)
	{
		$this->_infocentros = $infocentros;
	}
	public function SetInfocentro($infocentro){
		$this->_infocentros[] = $infocentro;
	}
	public function GetRutas()
	{
		return $this->_rutas;
	}
	public function SetRutas($rutas)
	{
		$this->_rutas = $rutas;
	}
	public function SetRuta($ruta)
	{
		$this->_rutas[] = $ruta;
	}
	public function GetTipoResponsable()
	{
		return $this->_tipoResponsable;
	}
	public function SetTipoResponsable($tipoResponsable)
	{
		$this->_tipoResponsable = $tipoResponsable;
	}
	public function GetResponsable()
	{
		return $this->_responsable;
	}
	public function SetResponsable(EntidadResponsableViatico $responsable = null)
	{
		$this->_responsable = $responsable;
	}
	public function GetViaticoResponsableAsignaciones(){
		return $this->_viaticoResponsableAsignaciones;
	}
	public function SetViaticoResponsableAsignaciones($viaticoResponsableAsignaciones){
		$this->_viaticoResponsableAsignaciones = $viaticoResponsableAsignaciones;
	}
	public function GetViaticoResponsableAsignacion($idAsignacion){
		return $this->_viaticoResponsableAsignaciones[$idAsignacion];
	}
	public function SetViaticoResponsableAsignacion($idAsignacion, $viaticoResponsableAsignacion){
		$this->_viaticoResponsableAsignaciones[$idAsignacion] = $viaticoResponsableAsignacion;
	}
	public function GetTipoOperacion(){
		return $this->_tipoOperacion;
	}
	public function SetTipoOperacion($tipoOperacion){
		$this->_tipoOperacion = $tipoOperacion;
	}
	public function GetProyecto(){
		return $this->_proyecto;
	}
	public function SetProyecto(EntidadProyecto  $proyecto = null){
		$this->_proyecto = $proyecto;
	}
	public function GetProyectoEspecifica(){
		return $this->_proyectoEspecifica;
	}
	public function SetProyectoEspecifica(EntidadProyectoEspecifica $proyectoEspecifica = null){
		$this->_proyectoEspecifica = $proyectoEspecifica;
	}
	public function GetAccionCentralizada(){
		return $this->_accionCentralizada;
	}
	public function SetAccionCentralizada(EntidadAccionCentralizada $accionCentralizada = null){
		$this->_accionCentralizada = $accionCentralizada;
	}
	public function GetAccionCentralizadaEspecifica(){
		return $this->_accionCentralizadaEspecifica;
	}
	public function SetAccionCentralizadaEspecifica(EntidadAccionCentralizadaEspecifica $accionCentralizadaEspecifica = null){
		$this->_accionCentralizadaEspecifica = $accionCentralizadaEspecifica;
	}
	public function GetDocGenera(){
		return $this->_docGenera;
	}
	public function SetDocGenera(EntidadDocGenera $docGenera = null){
		$this->_docGenera = $docGenera;
	}
	public function GetObservaciones(){
		return $this->_observaciones;
	}
	public function SetObservaciones($observaciones){
		$this->_observaciones = $observaciones;
	}
	public function GetCategoriaViatico(){
		return $this->_categoriaViatico;
	}
	public function SetCategoriaViatico(EntidadCategoriaViatico $categoriaViatico){
		$this->_categoriaViatico = $categoriaViatico;
	}
	public function GetRed(){
		return $this->_red;
	}
	public function SetRed(EntidadRed $red = null){
		$this->_red = $red;
	}
	public function GetEstado(){
		return $this->_estado;
	}
	public function SetEstado(EntidadEstado $estado){
		$this->_estado = $estado;
	}
	public function GetRequisiciones(){
		return $this->_requisiciones;
	}
	public function SetRequisiciones($requisiciones){
		$this->_requisiciones = $requisiciones;
	}
	public function GetRequisicion($index){
		return $this->_requisiciones[$index];
	}
	public function SetRequisicion(EntidadRequisicion $requisicion){
		$this->_requisiciones[] = $requisicion;
	}
	public function __clone()
	{
		$infocentros = null;
		if(is_array($this->_infocentros)){
			$infocentros = array();
			foreach($this->_infocentros as $infocentro){
				$infocentros[] = clone $infocentro;
			}
		}
		
		$rutas = null;
		if(is_array($this->_rutas)){
			$rutas = array();
			foreach($this->_rutas as $ruta){
				$rutas[] = clone $ruta;
			}
		}
		
		$asignaciones = null;
		if(is_array($this->_viaticoResponsableAsignaciones)){
			$asignaciones = array();
			foreach($this->_viaticoResponsableAsignaciones as $asignacion){
				$asignaciones[] = clone $asignacion;
			}
		}
		
		$requisiciones = null;
		if(is_array($this->_requisiciones)){
			$requisiciones = array();
			foreach($this->_requisiciones as $requisicion){
				$requisiciones[] = clone $requisicion;
			}
		} 
		
		$this->_infocentros = $infocentros;
		$this->_rutas = $rutas;
		$this->_responsable = ($this->_responsable != null) ? clone $this->_responsable : null;
		$this->_viaticoResponsableAsignaciones = $asignaciones;
		$this->_proyecto = ($this->_proyecto != null) ? clone $this->_proyecto : null;
		$this->_proyectoEspecifica = ($this->_proyectoEspecifica != null) ? clone $this->_proyectoEspecifica : null;
		$this->_accionCentralizada = ($this->_accionCentralizada != null) ? clone $this->_accionCentralizada : null;
		$this->_accionCentralizadaEspecifica = ($this->_accionCentralizadaEspecifica) ? clone $this->_accionCentralizadaEspecifica : null;
		$this->_docGenera = ($this->_docGenera != null) ? clone $this->_docGenera : null;
		$this->_categoriaViatico = ($this->_categoriaViatico != null) ? clone $this->_categoriaViatico : null;
		$this->_red = ($this->_red != null) ? clone $this->_red : null;
		$this->_estado = ($this->_estado != null) ? clone $this->_estado : null;
		$this->_requisiciones = $requisiciones;
	}
	public function UTF8Encode()
	{
		if(is_array($this->_infocentros)){
			foreach($this->_infocentros as &$infocentro){
				$infocentro->UTF8Encode();
			}
		}
		
		if(is_array($this->_rutas)){
			foreach($this->_rutas as &$ruta){
				$ruta->UTF8Encode();
			}
		}
		
		$this->_idViatico = utf8_encode($this->_idViatico);
		$this->_tipoProyectoAccionCentralizada = utf8_encode($this->_tipoProyectoAccionCentralizada);
		$this->_idProyectoAccionCentralizada = utf8_encode($this->_idProyectoAccionCentralizada);
		$this->_idAccionEspecifica = utf8_encode($this->_idAccionEspecifica);
		$this->_fechaViatico = utf8_encode($this->_fechaViatico);
		$this->_fechaInicioViaje = utf8_encode($this->_fechaInicioViaje);
		$this->_fechaFinViaje = utf8_encode($this->_fechaFinViaje);
		$this->_objetivosViaje = utf8_encode($this->_objetivosViaje);
		//$this->_infocentros = $infocentros;
		//$this->_rutas = $rutas;
		$this->_tipoResponsable = utf8_encode($this->_tipoResponsable);
		if($this->_responsable != null) $this->_responsable->UTF8Encode();
		//if($this->_proyecto != null) $this->_proyecto->UTF8Encode();
		//if($this->_proyectoEspecifica != null) $this->_proyectoEspecifica->UTF8Encode();
		//if($this->_accionCentralizada != null) $this->_accionCentralizada->UTF8Encode();
		//if($this->_accionCentralizadaEspecifica != null) $this->_accionCentralizadaEspecifica->UTF8Encode();
		$this->_tipoOperacion = utf8_encode($this->_tipoOperacion);
		//if($this->_docGenera != null) $this->_docGenera->UTF8Encode();
		$this->_observaciones = utf8_encode($this->_observaciones);
		if($this->_categoriaViatico != null) $this->_categoriaViatico->UTF8Encode();
		if($this->_red != null) $this->_red->UTF8Encode();
		//if($this->_estado != null) $this->_estado->UTF8Encode();
		
		/*
		private $_viaticoResponsableAsignaciones = array();
		private $_requisiciones = array();
		*/
		
		
		return $this;
	}
	public function ToArray($properties = array())
	{
		$data = array();
		
		// No está completo este método
		if(is_array($properties) && count($properties) > 0){
			foreach($properties as $property){
				$nameProperty = '_' . $property;
				$data[$property] = $this->$property;
			}
		} else {
			$infocentros = array();
			if(is_array($this->_infocentros)){
				foreach($this->_infocentros as $infocentro){
					$infocentros[$infocentro->GetId()] = $infocentro->ToArray(); 
				}
			}
			
			$rutas = array();
			if(is_array($this->_rutas)){
				foreach($this->_rutas as $ruta){
					$rutas[] = $ruta->ToArray(); 
				}
			}
			
			$data = array(
				'idViatico' => $this->_idViatico,
				'tipoProyectoAccionCentralizada' => $this->_tipoProyectoAccionCentralizada,
				'idProyectoAccionCentralizada' => $this->_idProyectoAccionCentralizada,
				'idAccionEspecifica' => $this->_idAccionEspecifica,
				'fechaViatico' => $this->_fechaViatico,
				'fechaInicioViaje' => $this->_fechaInicioViaje,
				'fechaFinViaje' => $this->_fechaFinViaje,
				'objetivosViaje' => $this->_objetivosViaje,
				'infocentros' => $infocentros,
				'rutas' => $rutas,
				'tipoResponsable' => $this->_tipoResponsable,
				'responsable' => ($this->_responsable != null) ? $this->_responsable->ToArray() : null,
				'observaciones' => $this->_observaciones,
				'categoriaViatico' => ($this->_categoriaViatico != null) ? $this->_categoriaViatico->ToArray() : null,
				'red' => ($this->_red != null) ? $this->_red->ToArray() : null
			);
	
			//private $_viaticoResponsableAsignaciones = array();
			//private $_proyecto = null;
		 	//private $_proyectoEspecifica = null;
		 	//private $_accionCentralizada = null;
		 	//private $_accionCentralizadaEspecifica = null;
			//private $_tipoOperacion = self::TIPO_OPERACION_INSERTAR;
			//private $_docGenera;
			//private $_estado;
			//private $_requisiciones = array();
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}
?>