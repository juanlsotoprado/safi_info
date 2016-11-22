<?php
include_once(SAFI_ENTIDADES_PATH . '/dependencia.php');
include_once(SAFI_ENTIDADES_PATH . '/proyecto.php');
include_once(SAFI_ENTIDADES_PATH . '/accioncentralizada.php');
include_once(SAFI_ENTIDADES_PATH . '/categoriaviatico.php');
include_once(SAFI_ENTIDADES_PATH . '/red.php');
include_once(SAFI_ENTIDADES_PATH . '/estado.php');
include_once(SAFI_ENTIDADES_PATH . '/requisicion.php');

class EntidadViaticoNacional{

	private $_id = null;
	private $_fechaViatico = '';
	private $_fechaInicioViaje = '';
	private $_fechaFinViaje = '';
	private $_objetivosViaje = '';
	private $_partidaId = '';
	private $_partidaAnho = 0;
	private $_accionCentralizadaId = null;
	private $_accionCentralizadaAnho = null;
	private $_accionCentralizadaEspecificaId = null;
	private $_proyectoId = null;
	private $_proyectoAnho = null;
	private $_proyectoEspecificaId = null;
	private $_usuaLogin = '';
	private $_dependenciaId = '';
 	private $_dependencia;
 	private $_centroGestor = '';
 	private $_centroCosto = '';
 	private $_infocentros = array();
 	private $_rutas = array();
 	private $_responsable = null;
 	private $_viaticoResponsableAsignaciones = array();
 	private $_proyecto = null;
 	private $_proyectoEspecifica = null;
 	private $_accionCentralizada = null;
 	private $_accionCentralizadaEspecifica = null;
 	private $_observaciones = '';
 	private $_categoriaViatico;
 	private $_red;
 	private $_estado;
 	private $_requisiciones = array();
 	private $_montoTotal = 0.0;

	public function __construct()
	{
		
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetFechaViatico(){
		return $this->_fechaViatico;
	}
	public function SetFechaViatico($fechaViatico){
		$this->_fechaViatico = $fechaViatico;
	}
	public function GetFechaInicioViaje(){
		return $this->_fechaInicioViaje;
	}
	public function SetFechaInicioViaje($fechaInicioViaje){
		$this->_fechaInicioViaje = $fechaInicioViaje;
	}
	public function GetFechaFinViaje(){
		return $this->_fechaFinViaje;
	}
	public function SetFechaFinViaje($fechaFinViaje){
		$this->_fechaFinViaje = $fechaFinViaje;
	}
	public function GetObjetivosViaje(){
		return $this->_objetivosViaje;
	}
	public function SetObjetivosViaje($objetivosViaje){
		$this->_objetivosViaje = $objetivosViaje;
	}
	public function GetPartidaId(){
		return $this->_partidaId;
	}
	public function SetPartidaId($partidaId){
		$this->_partidaId = $partidaId;
	}
	public function GetPartidaAnho(){
		return $this->_partidaAnho;
	}
	public function SetPartidaAnho($partidaAnho){
		$this->_partidaAnho = $partidaAnho;
	}
	public function GetAccionCentralizadaId(){
		return $this->_accionCentralizadaId;
	}
	public function SetAccionCentralizadaId($accionCentralizadaId){
		$this->_accionCentralizadaId = $accionCentralizadaId;
	}
	public function GetAccionCentralizadaAnho(){
		return $this->_accionCentralizadaAnho;
	}
	public function SetAccionCentralizadaAnho($accionCentralizadaAnho){
		$this->_accionCentralizadaAnho = $accionCentralizadaAnho;
	}
	public function GetAccionCentralizadaEspecificaId(){
		return $this->_accionCentralizadaEspecificaId;
	}
	public function SetAccionCentralizadaEspecificaId($accionCentralizadaEspecificaId){
		$this->_accionCentralizadaEspecificaId = $accionCentralizadaEspecificaId;
	}
	public function GetProyectoId(){
		return $this->_proyectoId;
	}
	public function SetProyectoId($proyectoId){
		$this->_proyectoId = $proyectoId;
	}
	public function GetProyectoAnho(){
		return $this->_proyectoAnho;
	}
	public function SetProyectoAnho($proyectoAnho){
		$this->_proyectoAnho = $proyectoAnho;
	}
	public function GetProyectoEspecificaId(){
		return $this->_proyectoEspecificaId;
	}
	public function SetProyectoEspecificaId($proyectoEspecificaId){
		$this->_proyectoEspecificaId = $proyectoEspecificaId;
	}
	public function GetUsuaLogin(){
		return $this->_usuaLogin;
	}
	public function SetUsuaLogin($usuaLogin){
		$this->_usuaLogin = $usuaLogin;
	}
	public function GetDependenciaId(){
		return $this->_dependenciaId;
	}
	public function SetDependenciaId($dependenciaId){
		$this->_dependenciaId = $dependenciaId;
	}
	public function GetDependencia(){
		return $this->_dependencia;
	}
	public function SetDependencia($dependencia){
		$this->_dependencia = $dependencia;
	}
	public function GetCentroCosto()
	{
		return $this->_centroCosto;
	}
	public function SetCentroCosto($centroCosto){
		$this->_centroCosto = $centroCosto;
	}
	public function GetCentroGestor(){
		return $this->_centroGestor;
	}
	public function  SetCentroGestor($centroGestor){
		$this->_centroGestor = $centroGestor;
	}
	public function GetInfocentros(){
		return $this->_infocentros;
	}
	public function SetInfocentros($infocentros){
		$this->_infocentros = $infocentros;
	}
	public function GetInfocentro($idInfocentro){
		return $this->_infocentros[$idInfocentro];
	}
	public function SetInfocentro($idInfocentro, $infocentro){
		$this->_infocentros[$idInfocentro] = $infocentro;
	}
	public function GetRutas(){
		return $this->_rutas;
	}
	public function SetRutas($rutas){
		$this->_rutas = $rutas;
	}
	public function GetResponsable(){
		return $this->_responsable;
	}
	public function SetResponsable($responsable){
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
	public function GetProyecto(){
		return $this->_proyecto;
	}
	public function SetProyecto(EntidadProyecto $proyecto = null){
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
	public function GetMontoTotal(){
		return $this->_montoTotal;
	}
	public function SetMontoTotal($montoTotal){
		$this->_montoTotal = $montoTotal;
	}
}