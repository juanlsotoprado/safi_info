<?php
require_once(SAFI_ENTIDADES_PATH . '/proyectoAccionCentralizada.php');
require_once(SAFI_ENTIDADES_PATH . '/proyecto.php');
require_once(SAFI_ENTIDADES_PATH . '/proyectoespecifica.php');
require_once(SAFI_ENTIDADES_PATH . '/accioncentralizada.php');
require_once(SAFI_ENTIDADES_PATH . '/accioncentralizadaespecifica.php');
require_once(SAFI_ENTIDADES_PATH . '/categoriaviatico.php');
require_once(SAFI_ENTIDADES_PATH . '/red.php');
require_once(SAFI_ENTIDADES_PATH . '/rutaAvance.php');
require_once(SAFI_ENTIDADES_PATH . '/responsableAvancePartidas.php');
require_once(SAFI_ENTIDADES_PATH . '/dependencia.php');
require_once(SAFI_ENTIDADES_PATH . '/puntoCuenta.php');

class EntidadAvance
{
	private $_id;
	private $_fechaAvance;
	private $_fechaRegistro;
	private $_fechaUltimaModificacion;
	private $_categoria;
	private $_red;
	private $_tipoProyectoAccionCentralizada;
	private $_proyecto = null;
 	private $_proyectoEspecifica = null;
 	private $_accionCentralizada = null;
 	private $_accionCentralizadaEspecifica = null;
	private $_fechaInicioActividad;
	private $_fechaFinActividad;
	private $_objetivos;
	private $_descripcion;
	private $_justificacion;
	private $_nroParticipantes;
	private $_responsablesAvancePartidas;
	private $_rutasAvance;
	private $_infocentros;
	private $_observaciones;
	private $_usuaLogin;
	private $_dependencia;
	private $_puntoCuenta;
	private $_montoTotal;
	
	public function __construct()
	{
		$this->_fechaRegistro = date("d/m/Y H:i:s");
		$this->_fechaAvance = date("d/m/Y");
		$this->_tipoProyectoAccionCentralizada = EntidadProyectoAccionCentralizada::TIPO_PROYECTO;
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetFechaAvance(){
		return $this->_fechaAvance;
	}
	public function SetFechaAvance($fechaAvance){
		$this->_fechaAvance = $fechaAvance;
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
	public function GetCategoria(){
		return $this->_categoria;
	}
	public function SetCategoria(EntidadCategoriaViatico $categoria){
		$this->_categoria = $categoria;
	}
	public function GetRed(){
		return $this->_red;
	}
	public function SetRed($red){
		$this->_red = $red;
	}
	public function GetTipoProyectoAccionCentralizada(){
		return $this->_tipoProyectoAccionCentralizada;
	}
	public function SetTipoProyectoAccionCentralizada($tipoProyectoAccionCentralizada){
		$this->_tipoProyectoAccionCentralizada = $tipoProyectoAccionCentralizada;
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
	public function GetJustificacion(){
		return $this->_justificacion;
	}
	public function SetJustificacion($justificacion){
		$this->_justificacion = $justificacion;
	}
	public function GetNroParticipantes(){
		return $this->_nroParticipantes;
	}
	public function SetNroParticipantes($nroParticipantes){
		$this->_nroParticipantes = $nroParticipantes;
	}
	public function GetResponsablesAvancePartidas(){
		return $this->_responsablesAvancePartidas;
	}
	public function SetResponsablesAvancePartidas(array $responsablesAvancePartidas = null){
		$this->_responsablesAvancePartidas = $responsablesAvancePartidas;
	}
	public function GetRutasAvance(){
		return $this->_rutasAvance;
	}
	public function SetRutasAvance(array $rutasAvance = null){
		$this->_rutasAvance = $rutasAvance;
	}
	public function GetInfocentros(){
		return $this->_infocentros;
	}
	public function SetInfocentros(array $infocentros = null){
		$this->_infocentros = $infocentros;
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
	public function GetPuntoCuenta()
	{
		return $this->_puntoCuenta;
	}
	public function SetPuntoCuenta(EntidadPuntoCuenta $puntoCuenta = null)
	{
		$this->_puntoCuenta = $puntoCuenta;
	}
	public function GetMontoTotal()
	{
		if($this->_montoTotal === null){
			$montoTotal = 0;
			
			if(is_array($this->_responsablesAvancePartidas)){
				foreach ($this->_responsablesAvancePartidas AS $responsableAvancePartidas){
					$monto = $responsableAvancePartidas->GetMontoTotal();
					if($monto != null && trim($monto) != ''){
						$montoTotal += $monto;
					} 
				}
			}
			$this->_montoTotal = $montoTotal;
		}
		
		return $this->_montoTotal;
	}
	public function CalcularSubtotalesPorResponsable()
	{
		if(is_array($this->_responsablesAvancePartidas))
		{
			foreach ($this->_responsablesAvancePartidas as $responsableAvancePartidas)
			{
				$responsableAvancePartidas->GetMontoTotal();
			}
		}
	}
	public function __toString()
	{
		// Datos de los infocentros
		$idInfocentros = array();
		if(is_array($this->_infocentros))
		{
			foreach ($this->_infocentros as $infocentro)
			{
				$idInfocentros[] = $infocentro->GetId();
			}
		}
		
		// Datos de los responsables del avance
		$idResponsablesAvance = array();
		if(is_array($this->_responsablesAvancePartidas))
		{
			foreach ($this->_responsablesAvancePartidas as $responsableAvancePartidas)
			{
				$idResponsablesAvance[] = $responsableAvancePartidas->GetResponsableAvance()->GetId();
			}
		}
		
		//Datos de las rutas
		$idRutasAvance = array();
		if(is_array($this->_rutasAvance))
		{
			foreach ($this->_rutasAvance as $rutaAvance)
			{
				$idRutasAvance[] = $rutaAvance->GetId();
			}
		}
		
		return "
			Id = ".$this->GetId().",
			FechaAvance = " . $this->GetFechaAvance() . ",
			FechaRegistro = " . $this->GetFechaRegistro() . ",
			FechaUltimaModificacion = " . $this->GetFechaUltimaModificacion() . ",
			Categoria = " . ($this->_categoria != null ? $this->_categoria->GetId() : "NULL") . ",
			Red = " . ($this->_red != null ? $this->_red->GetId() : "NULL") . ",
			TipoProyectoAccionCentralizada = " . EntidadProyectoAccionCentralizada::TIPO_PROYECTO
				. " (".EntidadProyectoAccionCentralizada::TIPO_PROYECTO." = Proyecto, "
				. EntidadProyectoAccionCentralizada::TIPO_ACCION_CENTRALIZADA . " = Accion centralizada),
			Proyecto = " . ($this->_proyecto != null ? $this->_proyecto->GetId() : "NULL") . ",
			ProyectoEspecifica = " . ($this->_proyectoEspecifica != null ? $this->_proyectoEspecifica->GetId() : "NULL") . ",
			AccionCentralizada = " . ($this->_accionCentralizada != null ? $this->_accionCentralizada->GetId() : "NULL") . ",
			AccionCentralizadaEspecifica = " . 
				($this->_accionCentralizadaEspecifica != null ? $this->_accionCentralizadaEspecifica->GetId() : "NULL") . ",
			FechaInicioActividad = " . $this->GetFechaInicioActividad() . ",
			FechaFinActividad = " . $this->GetFechaFinActividad() . "
			Objetivos = " . $this->GetObjetivos() . ",
			Descripcion = " . $this->GetDescripcion() . ",
			Justificacion = " . $this->GetJustificacion() . ",
			Nroparticipantes = " . $this->GetNroParticipantes() . ",
			ResponsablesAvancePartidas = (" . implode(", ", $idResponsablesAvance) . "),
			Infocentros = (" . implode(", ", $idInfocentros) . "),
			RutasAvance = (" . implode(", ", $idRutasAvance) . "),
			Observaciones = " . $this->GetObservaciones() . "
		";
	}
	public function __clone()
	{
		$responsablesAvancePartidas = null;
		if(is_array($this->_responsablesAvancePartidas)){
			$responsablesAvancePartidas = array();
			foreach($this->_responsablesAvancePartidas as $responsableAvancePartidas){
				$responsablesAvancePartidas[] = clone $responsableAvancePartidas;
			}
		}
		
		$rutasAvance = null;
		if(is_array($this->_rutasAvance)){
			$rutasAvance = array();
			foreach($this->_rutasAvance as $rutaAvance){
				$rutasAvance[] = clone $rutaAvance;
			}
		}
		
		$infocentros = null;
		if(is_array($this->_infocentros)){
			$infocentros = array();
			foreach($this->_infocentros as $infocentro){
				$infocentros[] = clone $infocentro;
			}
		}
		
		$this->_categoria = ($this->_categoria != null) ? clone $this->_categoria : null;
		$this->_red = ($this->_red != null) ? clone $this->_red : null;
		$this->_proyecto = ($this->_proyecto != null) ? clone $this->_proyecto : null;
		$this->_proyectoEspecifica = ($this->_proyectoEspecifica != null) ? clone $this->_proyectoEspecifica : null;
		$this->_accionCentralizada = ($this->_accionCentralizada != null) ? clone $this->_accionCentralizada : null;
		$this->_accionCentralizadaEspecifica = 
			($this->_accionCentralizadaEspecifica != null) ? clone $this->_accionCentralizadaEspecifica : null;
		$this->_responsablesAvancePartidas = $responsablesAvancePartidas;
		$this->_rutasAvance = $rutasAvance;
		$this->_infocentros = $infocentros;
	}
	public function UTF8Encode()
	{
		if(is_array($this->_responsablesAvancePartidas)){
			foreach($this->_responsablesAvancePartidas as &$responsableAvancePartidas){
				$responsableAvancePartidas->UTF8Encode();
			}
			unset($responsableAvancePartidas);
		}
		
		if(is_array($this->_rutasAvance)){
			foreach($this->_rutasAvance as &$rutaAvance){
				$rutaAvance->UTF8Encode();
			}
			unset($rutaAvance);
		}
		
		if(is_array($this->_infocentros)){
			foreach($this->_infocentros as &$infocentro){
				$infocentro->UTF8Encode();
			}
			unset($infocentro);
		}
		
		$this->_id = utf8_encode($this->_id);
		$this->_fechaAvance = utf8_encode($this->_fechaAvance);
		$this->_fechaRegistro = utf8_encode($this->_fechaRegistro);
		$this->_fechaUltimaModificacion = utf8_encode($this->_fechaUltimaModificacion);
		if($this->_categoria != null) $this->_categoria->UTF8Encode();
		if($this->_red != null) $this->_red->UTF8Encode();
		$this->_tipoProyectoAccionCentralizada = utf8_encode($this->_tipoProyectoAccionCentralizada);
		if($this->_proyecto != null) $this->_proyecto->UTF8Encode();
		if($this->_proyectoEspecifica != null) $this->_proyectoEspecifica->UTF8Encode();
		if($this->_accionCentralizada != null) $this->_accionCentralizada->UTF8Encode();
		if($this->_accionCentralizadaEspecifica != null) $this->_accionCentralizadaEspecifica->UTF8Encode();
		$this->_fechaInicioActividad = utf8_encode($this->_fechaInicioActividad);
		$this->_fechaFinActividad = utf8_encode($this->_fechaFinActividad);
		$this->_objetivos = utf8_encode($this->_objetivos);
		$this->_descripcion = utf8_encode($this->_descripcion);
		$this->_justificacion = utf8_encode($this->_justificacion);
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
			$responsablesAvancePartidas = null;
			if(is_array($this->_responsablesAvancePartidas)){
				$responsablesAvancePartidas = array();
				foreach($this->_responsablesAvancePartidas as $responsableAvancePartidas){
					$responsablesAvancePartidas[] = $responsableAvancePartidas->ToArray(); 
				}
			}
			
			$rutasAvance = null;
			if(is_array($this->_rutasAvance)){
				$rutasAvance = array();
				foreach($this->_rutasAvance as $rutaAvance){
					$rutasAvance[] = $rutaAvance->ToArray(); 
				}
			}
			
			$infocentros = null;
			if(is_array($this->_infocentros)){
				$infocentros = array();
				foreach($this->_infocentros as $infocentro){
					$infocentros[$infocentro->GetId()] = $infocentro->ToArray(); 
				}
			}
			
			$data = array(
				'id' => $this->_id,
				'fechaAvance' => $this->_fechaAvance,
				'fechaRegistro' => $this->_fechaRegistro,
				'fechaUltimaModificacion' => $this->_fechaUltimaModificacion,
				'categoria' => ($this->_categoria != null) ? $this->_categoria->ToArray() : null,
				'red' => ($this->_red != null) ? $this->_red->ToArray() : null,
				'tipoProyectoAccionCentralizada' => $this->_tipoProyectoAccionCentralizada,
				'proyecto' => ($this->_proyecto != null) ? $this->_proyecto->ToArray() : null,
				'proyectoEspecifica' => ($this->_proyectoEspecifica != null) ? $this->_proyectoEspecifica->ToArray() : null,
				'accionCentralizada' => ($this->_accionCentralizada != null) ? $this->_accionCentralizada->ToArray() : null,
				'accionCentralizadaEspecifica' => 
					($this->_accionCentralizadaEspecifica != null) ? $this->_accionCentralizadaEspecifica->ToArray() : null,
				'fechaInicioActividad' => $this->_fechaInicioActividad,
				'fechaFinActividad' => $this->_fechaFinActividad,
				'objetivos' => $this->_objetivos,
				'descripcion' => $this->_descripcion,
				'justificacion' => $this->_justificacion,
				'nroParticipantes' => $this->_nroParticipantes,
				'responsablesAvancePartidas' => $responsablesAvancePartidas,
				'rutasAvance' => $rutasAvance,
				'infocentros' => $infocentros,
				'observaciones' => $this->_observaciones
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}