<?php

require_once(dirname(__FILE__) . '/../../init.php');

// Acciones
require_once(SAFI_ACCIONES_PATH. '/acciones.php');

// Includes
require_once(SAFI_INCLUDE_PATH. '/constantes.php');
require_once(SAFI_INCLUDE_PATH. '/perfiles/constantesPerfiles.php');

// Modelo
require_once(SAFI_MODELO_PATH. '/avance.php');
require_once(SAFI_MODELO_PATH. '/beneficiarioviatico.php');
require_once(SAFI_MODELO_PATH. '/cargo.php');
require_once(SAFI_MODELO_PATH. '/categoriaviatico.php');
require_once(SAFI_MODELO_PATH. '/dependencia.php');
require_once(SAFI_MODELO_PATH. '/empleado.php');
require_once(SAFI_MODELO_PATH. '/estado.php');
require_once(SAFI_MODELO_PATH. '/estatus.php');
require_once(SAFI_MODELO_PATH. '/firma.php');
require_once(SAFI_MODELO_PATH. '/infocentro.php');
require_once(SAFI_MODELO_PATH. '/partida.php');
require_once(SAFI_MODELO_PATH. '/puntoCuenta.php');
require_once(SAFI_MODELO_PATH. '/red.php');
require_once(SAFI_MODELO_PATH. '/regionReporte.php');
require_once(SAFI_MODELO_PATH. '/regionReporteEstado.php');
require_once(SAFI_MODELO_PATH. '/wfgrupo.php');

if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}

class AvanceAccion extends Acciones
{
	public function Bandeja()
	{
		$idPerfil = $_SESSION['user_perfil_id'];
		$idDependencia = $_SESSION['user_depe_id'];
		$login = $_SESSION['login'];
		
		$estatusDevuelto = 7;
		$estatusEntransito = 10;
		
		/*********************************
		 ******* Bandeja principal *******
		 *********************************/
		 
		$enBandeja = null;
		if(
			substr($idPerfil,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
			$idPerfil == PERFIL_ASISTENTE_EJECUTIVO ||
			$idPerfil == PERFIL_ASISTENTE_PRESIDENCIA
		){
			$enBandeja = SafiModeloAvance::GetAvanceEnBandeja(array(
				'idPerfilActual' => $idPerfil,
				'estatus' => array($estatusDevuelto),
				'idDependencia' => $idDependencia 
			));
		} else if(
			(substr($idPerfil,0,2)."000" == PERFIL_GERENTE) ||
			$idPerfil == PERFIL_DIRECTOR_EJECUTIVO ||
			$idPerfil == PERFIL_PRESIDENTE ||
			(substr($idPerfil,0,2)."000" == PERFIL_DIRECTOR && $idPerfil != PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS)
		){
			$enBandeja = SafiModeloAvance::GetAvanceEnBandeja(array(
				'idPerfilActual' => $idPerfil,
				'estatus' => array($estatusEntransito),
				'idDependencia' => $idDependencia 
			));
		} else if(
			$idPerfil == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS
		){
			$enBandeja = SafiModeloAvance::GetAvanceEnBandeja(array(
				'idPerfilActual' => $idPerfil,
				'estatus' => arraY($estatusEntransito) 
			));
		} else if(
			$idPerfil == PERFIL_JEFE_PRESUPUESTO ||
			$idPerfil == PERFIL_ANALISTA_PRESUPUESTO
		){
			$enBandeja = SafiModeloAvance::GetAvanceEnBandeja(array(
				'idPerfilActual' => PERFIL_JEFE_PRESUPUESTO,
				'estatus' => arraY($estatusEntransito) 
			));
		}

		if(is_array($enBandeja) && count($enBandeja)>0)
		{
			$usuaLogins = array();
			
			foreach ($enBandeja AS $dataAvance){				
				$docGenera = $dataAvance['ClassDocGenera'];
	
				/* para obtener los datos de el usuario que eleboró la rendición */
				$usuaLogins[] = $docGenera->GetUsuaLogin();
			}
			
			if(count($usuaLogins)>0){
				$usuaLogins = array_unique($usuaLogins);
				$empleadosElaboradoresEnBandejas = SafiModeloEmpleado::GetEmpleadosByUsuaLogins($usuaLogins);
				$GLOBALS['SafiRequestVars']['empleadosElaboradoresEnBandejas'] = $empleadosElaboradoresEnBandejas;
			}
		}
		
		$GLOBALS['SafiRequestVars']['avancesEnBandeja'] = $enBandeja;
		
		if(
			substr($idPerfil,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
			$idPerfil == PERFIL_ASISTENTE_EJECUTIVO ||
			$idPerfil == PERFIL_ASISTENTE_PRESIDENCIA
		){
			/**********************************
			 ******* Bandeja por enviar *******
			 **********************************/
			
			$porEnviar = SafiModeloAvance::GetAvancePorEnviar(array(
				"usuaLogin" => $login,
				"idPerfilActual" => $idPerfil
			));
			
			$GLOBALS['SafiRequestVars']['avancesPorEnviar'] = $porEnviar;
		}
		
		/***********************************
		 ******* Bandeja en transito *******
		 ***********************************/
		if(
			$idPerfil == PERFIL_ANALISTA_PRESUPUESTO
			|| substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO
			|| $_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO
			|| $_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA
			|| $idPerfil == PERFIL_JEFE_PRESUPUESTO
			|| substr($idPerfil,0,2)."000" == PERFIL_GERENTE
			|| substr($idPerfil,0,2)."000" == PERFIL_DIRECTOR
			|| $idPerfil == PERFIL_DIRECTOR_EJECUTIVO
			|| $idPerfil == PERFIL_PRESIDENTE
		){
			
			$params = array();
			
			// Perfil jefe de presupuesto
			if($idPerfil == PERFIL_JEFE_PRESUPUESTO || $idPerfil == PERFIL_ANALISTA_PRESUPUESTO)
			{
				$params['enPerfiles'] = array(PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS);
				$params['noEnDependencia'] = array(GetIdDependenciaFromIdPerfil(PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS));
			}
			else { $params['idDependencia'] = $idDependencia; } 
			
			// Perfil de gerente o director o director ejecutivo o presidente
			if(
				substr($idPerfil,0,2)."000" == PERFIL_DIRECTOR || substr($idPerfil,0,2)."000" == PERFIL_GERENTE
				|| $idPerfil == PERFIL_DIRECTOR_EJECUTIVO || $idPerfil == PERFIL_PRESIDENTE
			){
				$params['enPerfiles'] = array(PERFIL_JEFE_PRESUPUESTO);
				if($idPerfil != PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS){
					$params['enPerfiles'][] = PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS;
				}
			}
			
			$enTransito = SafiModeloAvance::GetAvanceEnTransito($params);
			
			$cargoFundaciones = array();
			$idDependencias = array();
			$usuaLogins = array();
			
			foreach($enTransito as $dataAvance){
				$docGenera = $dataAvance['ClassDocGenera'];
				
				/* para obtener los datos de la instancia actual */
				$idPerfilActual = $docGenera->GetIdPerfilActual();
				if($idPerfilActual != null && $idPerfilActual != '')
				{
					$cargoFundacion = GetCargoFundacionFromIdPerfil($idPerfilActual);
					$cargoFundaciones[$cargoFundacion] = $cargoFundacion;
					
					$idDependencia = GetIdDependenciaFromIdPerfil($idPerfilActual);
					$idDependencias[$idDependencia] = $idDependencia; 
				}
				
				/* para obtener los datos de el usuario que eleboró la rendición */
				$usuaLogins[] = $docGenera->GetUsuaLogin();
			}
			
			if(count($cargoFundaciones)>=0){
				$cargoFundacionEnTransitos = SafiModeloCargo::GetCargoByCargoFundaciones($cargoFundaciones);
				$GLOBALS['SafiRequestVars']['avancesCargoFundacionEnTransitos'] = $cargoFundacionEnTransitos;
			}
			
			if(count($idDependencias)>0){
				$dependenciaEnTransitos = SafiModeloDependencia::GetDependenciaByIds($idDependencias);
				$GLOBALS['SafiRequestVars']['avancesDependenciaEnTransitos'] = $dependenciaEnTransitos;
			}
			
			if(count($usuaLogins)>0){
				$usuaLogins = array_unique($usuaLogins);
				$empleadosElaboradoresEnTransitos = SafiModeloEmpleado::GetEmpleadosByUsuaLogins($usuaLogins);
				$GLOBALS['SafiRequestVars']['avancesEmpleadosElaboradoresEnTransitos'] = $empleadosElaboradoresEnTransitos;
			}
			
			$GLOBALS['SafiRequestVars']['avancesEnTransito'] = $enTransito;
			
		}
		
		require(SAFI_VISTA_PATH ."/avan/bandejaAvance.php");
	}
	
	public function Insertar()
	{
		$form = FormManager::GetForm(FORM_NUEVO_AVANCE);
		
		$puntoCuenta = new EntidadPuntoCuenta();
		$puntoCuenta->SetId("pcta-");
		$form->GetAvance()->SetPuntoCuenta($puntoCuenta);
		
		// Descomentar para producción
		$form->GetAvance()->SetRutasAvance(array(new EntidadRutaAvance()));
		$form->GetAvance()->SetResponsablesAvancePartidas(array(new EntidadResponsableAvancePartidas()));
		
		
		/***************************************************
		************* Comentar para producción ************* 
		****************************************************/
		/*
		$categoria = new EntidadCategoriaViatico();
		$categoria->SetId(2);
		
		$red = new EntidadRed();
		$red->SetId(4);
		
		$proyecto = new EntidadProyecto();
		$proyecto->SetId("114254");
		
		$proyectoEspecifica = new EntidadProyectoEspecifica();
		$proyectoEspecifica->SetId("114254 A-1");
		
		// Ruta del avance
		$rutasAvance = array();
		
		$estado = new EntidadEstado();
		$estado->SetId(10);
		
		$ciudad = new EntidadCiudad();
		$ciudad->SetId(293);
		
		$municipio = new EntidadMunicipio();
		$municipio->SetId(104);
		
		$parroquia = new EntidadParroquia();
		$parroquia->SetId(339);
		
		$rutaAvance = new EntidadRutaAvance();
		$rutaAvance->SetEstado($estado);
		$rutaAvance->SetCiudad($ciudad);
		$rutaAvance->SetMunicipio($municipio);
		$rutaAvance->SetParroquia($parroquia);
		$rutaAvance->SetDireccion(utf8_decode('Av. Cincurvalación'));
		
		$rutasAvance[] = $rutaAvance;
		
		// Responsables del avance
		$empleado = SafiModeloEmpleado::GetEmpleadoActivoByCedula2("15586921");
		$estado = SafiModeloEstado::GetEstadoById(10);
		
		$responsableAvance = new EntidadResponsableAvance();
		$responsableAvance->SetTipoResponsable(EntidadResponsable::TIPO_EMPLEADO);
		$responsableAvance->SetEmpleado($empleado);
		$responsableAvance->SetEstado($estado);
		$responsableAvance->SetNumeroCuenta("12345678901234567890");
		$responsableAvance->SetTipoCuenta(EntidadTipoCuentabancaria::CUENTA_DE_AHORRO);
		$responsableAvance->SetBanco("Mercantil");		
		
		// Partidas del responsable
		$responsablesAvancePartidas = array();
		$avancePartidas = array();
		
		$partida = new EntidadPartida();
		$partida->SetId("4.01.01.01.04");
		
		$avancePartida = new EntidadAvancePartida();
		$avancePartida->SetPartida($partida);
		$avancePartida->SetMonto("100.23");
		$avancePartidas[] = $avancePartida;
		
		$partida = new EntidadPartida();
		$partida->SetId("4.03.01.02.00");
		
		$avancePartida = new EntidadAvancePartida();
		$avancePartida->SetPartida($partida);
		$avancePartida->SetMonto("548.589");
		$avancePartidas[] = $avancePartida;
		
		$responsableAvancePartidas = new EntidadResponsableAvancePartidas();
		$responsableAvancePartidas->SetResponsableAvance($responsableAvance);
		$responsableAvancePartidas->SetAvancePartidas($avancePartidas);
		
		$responsablesAvancePartidas[] = $responsableAvancePartidas;
		// Fin Responsables del avance
		
		
		// Ruta del avance
		$estado = new EntidadEstado();
		$estado->SetId(2);
		
		$ciudad = new EntidadCiudad();
		$ciudad->SetId(32);
		
		$municipio = new EntidadMunicipio();
		$municipio->SetId(10);
		
		$parroquia = new EntidadParroquia();
		$parroquia->SetId(31);
		
		$rutaAvance = new EntidadRutaAvance();
		$rutaAvance->SetEstado($estado);
		$rutaAvance->SetCiudad($ciudad);
		$rutaAvance->SetMunicipio($municipio);
		$rutaAvance->SetParroquia($parroquia);
		$rutaAvance->SetDireccion(utf8_decode('Ídolo'));
		
		$rutasAvance[] = $rutaAvance;
		
		// Infocentros
		$infocentros = SafiModeloInfocentro::GetInfocentrosByIds(array(5, 4));
		
		$avance = $form->GetAvance();
		$avance->SetCategoria($categoria);
		$avance->SetRed($red);
		$avance->SetTipoProyectoAccionCentralizada(EntidadProyectoAccionCentralizada::TIPO_PROYECTO);
		$avance->SetProyecto($proyecto);
		$avance->SetProyectoEspecifica($proyectoEspecifica);
		$avance->SetFechaInicioActividad("23/05/2011");
		$avance->SetFechaFinActividad("06/06/2011");
		$avance->SetObjetivos(utf8_decode("Un acento en administración"));
		$avance->SetDescripcion(utf8_decode("Descripción del avance"));
		$avance->SetJustificacion(utf8_decode("Justificación de avance"));
		$avance->SetNroParticipantes(utf8_decode("20"));
		$avance->SetResponsablesAvancePartidas($responsablesAvancePartidas);
		$avance->SetRutasAvance($rutasAvance);
		$avance->SetInfocentros($infocentros);
		
		
		$avance->SetObservaciones(utf8_decode("Observación del avance"));
		*/
		/***************************************************
		********* Fin de Comentar para producción ********** 
		****************************************************/
		
		$this->__DesplegarFormularioAvance();
	}
	
	public function Guardar()
	{
		$idAvance = $this->__Guardar();
		
		if($idAvance !== false){
			$GLOBALS['SafiInfo']['general'][] = "Avance \"" . $idAvance . "\" registrado satisfactoriamente.";
			$this->__VerDetalles(array('idAvance' => $idAvance));
		} else {
			$this->__DesplegarFormularioAvance();
		}
	}
	
	private function __Guardar()
	{
		$form = FormManager::GetForm(FORM_NUEVO_AVANCE);
		
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		
		$idDependencia = $_SESSION['user_depe_id'];
		$loginRegistrador = $_SESSION['login'];
		$annoPresupuesto = $_SESSION['an_o_presupuesto'];
		$perfilRegistrador = $_SESSION['user_perfil_id'];
		
		$form->GetAvance()->SetFechaRegistro(date('d/m/Y H:i:s'));
		$form->GetAvance()->SetFechaAvance(date('d/m/Y'));
		
		/* Comentar para que se pueda elegir la fecha del avance */
		// Obtener y validar la fecha del avance
		$this->__ValidarFechaAvance($form);
		
		// Obtener y validar el id del punto de cuenta asociado al avance
		$this->__ValidarIdPuntoCuenta($form);
		// Obtener y validar la información de la categoría
		$this->__ValidarCategoria($form);
		//  Obtener y validar la información de la red
		$this->__ValidarRed($form);
		// Obtener y validar el tipo de proyecto o acción centralizada
		$this->__ValidarTipoProyectoAccionCentralizada($form);
		// Obtener y validar de forma general el id del proyecto/accion centralizada
		$this->__ValidarIdProyectoAccionCentralizada($form);
		// Obtener y validar de forma general el id de la acción específica
		$this->__ValidarIdAccionEspecifica($form);
		// Obtener y validar la fecha de inicio de la actividad
		$this->__ValidarFechaInicioActividad($form);
		// Obtener y validar la fecha de fin de la actividad
		$this->__ValidarFechaFinActividad($form);
		// Obtener y validar los objetivos del viaje
		$this->__ValidarObjetivos($form);
			// Obtener y validar la descripcion
		$this->__ValidarDescripcion($form);
		// Obtener y validar la justificación
		$this->__ValidarJustificacion($form);
		// Obtener y validar la nroParticipantes
		$this->__ValidarNroParticipantes($form);
		// Obtener y validar los infocentros
		$this->__ValidarInfocentros($form);
		// Obtener y validar los responsables del avance y sus partidas
		$this->__ValidarResponsablesAvancePartidas($form, $annoPresupuesto);
		// Obtener y validar los rutas del avance
		$this->__ValidarRutasAvance($form);
		// Obtener y validar las observaciones del avance
		$this->__ValidarObservaciones($form);
		
		if(count($GLOBALS['SafiErrors']['general']) == 0)
		{
			$avance = $form->GetAvance();
			
			$dependencia = new EntidadDependencia();
			$dependencia->SetId($idDependencia);
			
			$avance->SetDependencia($dependencia);
			$avance->SetUsuaLogin($loginRegistrador);
			
			if ($avance->GetTipoProyectoAccionCentralizada() == EntidadProyectoAccionCentralizada::TIPO_PROYECTO)
			{
				if($avance->GetProyecto() != null)
					$avance->GetProyecto()->SetAnho($annoPresupuesto);
					
				if ($avance->GetProyectoEspecifica() != null)
					$avance->GetProyectoEspecifica()->SetAnho($annoPresupuesto);
				
			} else if ($avance->GetTipoProyectoAccionCentralizada() == EntidadProyectoAccionCentralizada::TIPO_ACCION_CENTRALIZADA)
			{
				if($avance->GetAccionCentralizada() != null)
					$avance->GetAccionCentralizada()->SetAnho($annoPresupuesto);
				
				if($avance->GetAccionCentralizadaEspecifica() != null)
					$avance->GetAccionCentralizadaEspecifica()->SetAnho($annoPresupuesto);
			}
			
			$idAvance = SafiModeloAvance::GuardarAvance($avance, array('perfilRegistrador' => $perfilRegistrador));
			if($idAvance === false){
				$GLOBALS['SafiErrors']['general'][] = "El avance no pudo ser registrado. Int&eacute;ntelo m&aacute;s tarde
					o p&oacute;ngase en contacto con el administrador del sistema.";
			}
			return $idAvance;
		}
		
		return false;
	}
	
	public function GuardarYEnviar()
	{
		$idAvance = $this->__Guardar();
		
		if($idAvance !== false){
			if($this->__Enviar($idAvance) !== false)
			{
				$GLOBALS['SafiInfo']['general'][] = "Avance \"".$idAvance."\" registrado y enviado satisfactoriamente.";
			} else {
				$GLOBALS['SafiInfo']['general'][] = "El avance \"" . $idAvance. "\" fue registrado satisfactoriamente,
					pero no se pudo enviar. Intente enviarlo desde la bandeja.";
			}
			$this->__VerDetalles(array('idAvance' => $idAvance));
			return;
		} else {
			$this->__DesplegarFormularioAvance();
			return;
		}
		$this->Bandeja();
	}
	
	private function __DesplegarFormularioAvance(){
		
		// Categorías del avance (Asunto)
		$categorias = SafiModeloCategoriaViatico::GetAllCategoriasActivas();
		
		// Redes del avance (relacionado con las categorías --Encuentros--)
		$redes = SafiModeloRed::GetAllRedesActivas();
		
		// Estados del país
		$estados = SafiModeloEstado::GetAllEstados2();
		
		$GLOBALS['SafiRequestVars']['categorias'] = $categorias;
		$GLOBALS['SafiRequestVars']['redes'] =  $redes;
		$GLOBALS['SafiRequestVars']['estados'] =  $estados;
		
		require(SAFI_VISTA_PATH . "/avan/nuevoAvance.php");
	}
	
	public function Modificar()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		
		try {
			$form = FormManager::GetForm(FORM_NUEVO_AVANCE);
			$form->SetTipoOperacion(NuevoAvanceForm::TIPO_OPERACION_MODIFICAR);
			
			if(!isset($_REQUEST['idAvance']) || $_REQUEST['idAvance'] == null || ($idAvance=trim($_REQUEST['idAvance'])) == '')
				throw new Exception("No se encontró ningún identificador para el avance.");
				
			$findAvance = new EntidadAvance();
			$findAvance->SetId($idAvance);
			
			if(($avance = SafiModeloAvance::GetAvance($findAvance)) == null)
				throw new Exception("El avance \"" . $idAvance . "\" no pudo ser encontrado.");
				
			if(
				$avance->GetPuntoCuenta() == null || $avance->GetPuntoCuenta()->GetId() == null
				|| trim($avance->GetPuntoCuenta()->GetId()) == '' 
			){
				$puntoCuenta = new EntidadPuntoCuenta();
				$puntoCuenta->SetId("pcta-");
				$avance->SetPuntoCuenta($puntoCuenta);
			}

			$form->SetAvance($avance);
			
			$this->__DesplegarFormularioAvance();
		} 
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = utf8_decode($e->getMessage());
			require(SAFI_VISTA_PATH . "/desplegarmensajes.php");
		}
	}
	
	public function Actualizar()
	{
		$idAvance = $this->__Actualizar();
		
		if($idAvance !== false){
			$GLOBALS['SafiInfo']['general'][] = "Avance \"".$idAvance. "\" modificado satisfactoriamente.";
			$this->__VerDetalles(array('idAvance' => $idAvance));
		} else {
			$this->__DesplegarFormularioAvance();
		}
	}
	
	private function __Actualizar()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		
		$annoPresupuesto = $_SESSION['an_o_presupuesto'];
		
		try {
			$form = FormManager::GetForm(FORM_NUEVO_AVANCE);
			$form->SetTipoOperacion(NuevoAvanceForm::TIPO_OPERACION_MODIFICAR);
			
			if(!isset($_REQUEST['idAvance']) || $_REQUEST['idAvance'] == null || ($idAvance=trim($_REQUEST['idAvance'])) == '')
				throw new Exception("No se encontró ningún identificador para avance.");
				
			$findAvance = new EntidadAvance();
			$findAvance->SetId($idAvance);
			
			if(($avance = SafiModeloAvance::GetAvance($findAvance)) == null)
				throw new Exception("El avance " . $idAvance . " no pudo ser encontrado");
			
			/* Comentar para que se pueda elegir la fecha del avance */
			// Obtener y validar la fecha del avance
			$this->__ValidarFechaAvance($form);
			
			// Establecer el id del avance del formulario
			$form->GetAvance()->SetId($avance->GetId());
			// Obtener y validar el id del punto de cuenta asociado al avance
			$this->__ValidarIdPuntoCuenta($form);
			// Obtener y validar la información de la categoría
			$this->__ValidarCategoria($form);
			//  Obtener y validar la información de la red
			$this->__ValidarRed($form);
			// Obtener y validar el tipo de proyecto o acción centralizada
			$this->__ValidarTipoProyectoAccionCentralizada($form);
			// Obtener y validar de forma general el id del proyecto/accion centralizada
			$this->__ValidarIdProyectoAccionCentralizada($form);
			// Obtener y validar de forma general el id de la acción específica
			$this->__ValidarIdAccionEspecifica($form);
			// Obtener y validar la fecha de inicio de la actividad
			$this->__ValidarFechaInicioActividad($form);
			// Obtener y validar la fecha de fin de la actividad
			$this->__ValidarFechaFinActividad($form);
			// Obtener y validar los objetivos del viaje
			$this->__ValidarObjetivos($form);
				// Obtener y validar la descripcion
			$this->__ValidarDescripcion($form);
			// Obtener y validar la justificación
			$this->__ValidarJustificacion($form);
			// Obtener y validar la nroParticipantes
			$this->__ValidarNroParticipantes($form);
			// Obtener y validar los infocentros
			$this->__ValidarInfocentros($form);
			// Obtener y validar los responsables del avance y sus partidas
			$this->__ValidarResponsablesAvancePartidas($form, $annoPresupuesto);
			// Obtener y validar los rutas del avance
			$this->__ValidarRutasAvance($form);
			// Obtener y validar las observaciones del avance
			$this->__ValidarObservaciones($form);
			
			if(count($GLOBALS['SafiErrors']['general']) == 0)
			{
				$avanceForm = $form->GetAvance();
				
				/* Comentar para que se pueda elegir la fecha del avance */
				// Establecer los datos en el objeto avance que se va a actualizar en la base de datos
				$avance->SetFechaAvance($avanceForm->GetFechaAvance());
				
				$avance->SetCategoria($avanceForm->GetCategoria());
				$avance->SetRed($avanceForm->GetRed());
				$avance->SetTipoProyectoAccionCentralizada($avanceForm->GetTipoProyectoAccionCentralizada());
				$avance->SetFechaInicioActividad($avanceForm->GetFechaInicioActividad());
				$avance->SetFechaFinActividad($avanceForm->GetFechaFinActividad());
				$avance->SetObjetivos($avanceForm->GetObjetivos());
				$avance->SetDescripcion($avanceForm->GetDescripcion());
				$avance->SetJustificacion($avanceForm->GetJustificacion());
				$avance->SetNroParticipantes($avanceForm->GetNroParticipantes());
				$avance->SetInfocentros($avanceForm->GetInfocentros());
				$avance->SetResponsablesAvancePartidas($avanceForm->GetResponsablesAvancePartidas());
				$avance->SetRutasAvance($avanceForm->GetRutasAvance());
				$avance->SetObservaciones($avanceForm->GetObservaciones());
				$avance->SetPuntoCuenta($avanceForm->GetPuntoCuenta());
				
				if($avance->GetTipoProyectoAccionCentralizada() == EntidadProyectoAccionCentralizada::TIPO_PROYECTO)
				{
					$avance->SetProyecto($avanceForm->GetProyecto());
					$avance->SetProyectoEspecifica($avanceForm->GetProyectoEspecifica());
					$avance->SetAccionCentralizada(null);
					$avance->SetAccionCentralizadaEspecifica(null);
					
					if($avance->GetProyecto() != null)
						$avance->GetProyecto()->SetAnho($annoPresupuesto);
					
					if ($avance->GetProyectoEspecifica() != null)
						$avance->GetProyectoEspecifica()->SetAnho($annoPresupuesto);
				}
				else if ($avance->GetTipoProyectoAccionCentralizada() == EntidadProyectoAccionCentralizada::TIPO_ACCION_CENTRALIZADA)
				{
					$avance->SetProyecto(null);
					$avance->SetProyectoEspecifica(null);
					$avance->SetAccionCentralizada($avanceForm->GetAccionCentralizada());
					$avance->SetAccionCentralizadaEspecifica($avanceForm->GetAccionCentralizadaEspecifica());
					
					if($avance->GetAccionCentralizada() != null)
						$avance->GetAccionCentralizada()->SetAnho($annoPresupuesto);
					
					if($avance->GetAccionCentralizadaEspecifica() != null)
						$avance->GetAccionCentralizadaEspecifica()->SetAnho($annoPresupuesto);
				}
				
				if(SafiModeloAvance::ActualizarAvance($avance) !== false)
					return $avance->GetId();
				else
					$GLOBALS['SafiErrors']['general'][] = "El avance \"" . $avance->GetId() . "\" no pudo ser modificado. ".
						"P&oacute;ngase en contacto con el administrador del sistema.";
			}
			
			if(
				$form->GetAvance()->GetPuntoCuenta() == null || $form->GetAvance()->GetPuntoCuenta()->GetId() == null
				|| trim($form->GetAvance()->GetPuntoCuenta()->GetId()) == ''
			){
				$puntoCuenta = new EntidadPuntoCuenta();
				$puntoCuenta->SetId("pcta-");
				$form->GetAvance()->SetPuntoCuenta($puntoCuenta);
			}
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = utf8_decode($e->getMessage());
		}
		return false;
	}
	
	public function ActualizarYEnviar()
	{
		$idAvance = $this->__Actualizar();
		
		if($idAvance !== false){
			if($this->__Enviar($idAvance) !== false){
				$GLOBALS['SafiInfo']['general'][] = "Avance \"".$idAvance."\" modificado y enviado satisfactoriamente.";
			} else {
				$GLOBALS['SafiInfo']['general'][] = "El avance \"".$idAvance."\" fue modificado satisfactoriamente,
					pero no se pudo enviar. Intente enviarlo m&aacute;s tarde.";
			}
			$this->__VerDetalles(array('idAvance' => $idAvance));
		} else {
			$this->__DesplegarFormularioAvance();
		}
	}
	
	public function VerDetalles(){
		if(isset($_REQUEST['idAvance']) && trim($_REQUEST['idAvance']) != ''){

			$this->__VerDetalles(array('idAvance' => $_REQUEST['idAvance']));
		}
	}
	
	private function __VerDetalles($params)
	{
		$idAvance = $params['idAvance'];
		
		$form = FormManager::GetForm(FORM_NUEVO_AVANCE);
		
		if($idAvance != null && trim($idAvance) != '')
		{
			$findAvance = new EntidadAvance();
			$findAvance->SetId($idAvance);
			
			$avance = SafiModeloAvance::GetAvance($findAvance);
			
			$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idAvance);
			
			$form->SetAvance($avance);
			$form->SetDocGenera($docGenera);
			
			// Para los documentos de soporte (Memos)
			$GLOBALS['SafiRequestVars']['memos'] = GetDocumentosSoportesMemos($idAvance);
				
			// Para las revisiones del documento
			$GLOBALS['SafiRequestVars']['datosRevisionesDocumento'] = GetDatosRevisionesDocumento($idAvance);
		}
		
		require(SAFI_VISTA_PATH ."/avan/verDetallesAvance.php");
	}
	
	public function Enviar()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general']  =array();
		
		$idPerfil = $_SESSION['user_perfil_id'];
		
		// Validar el id de la rendición de viático nacional
		if(!isset($_REQUEST['idAvance']) || ($idAvance=trim($_REQUEST['idAvance'])) == '')
		{
			$GLOBALS['SafiErrors']['general'][] = "Identificador del avance no encontrado";
		}
		else if(($result=$this->__Enviar($_REQUEST['idAvance'])) !== false)
		{
			if(
				substr($idPerfil,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
				$idPerfil == PERFIL_ASISTENTE_EJECUTIVO ||
				$idPerfil == PERFIL_ASISTENTE_PRESIDENCIA
			){
				$GLOBALS['SafiInfo']['general'][] = "Avance \"".$idAvance."\" enviado satisfactoriamente.";
			} else {
				$GLOBALS['SafiInfo']['general'][] = "Avance \"".$idAvance."\" aprobado satisfactoriamente.";
			}
		}
		
		$this->Bandeja();
	}
	
	private function __Enviar($idAvance)
	{
		try
		{
			$idPerfil = $_SESSION['user_perfil_id'];
			$loginUsuario = $_SESSION['login'] ;
			$idDependencia = $_SESSION['user_depe_id'];
			
			if($idAvance == null || ($idAvance=trim($idAvance)) == '')
				throw new Exception("Identificador del avance no encontrado"); 
			
			// Identificar el perfil que desea hacer el envío
			if(	
				substr($idPerfil,0,2)."000" != PERFIL_ASISTENTE_ADMINISTRATIVO
				&& substr($idPerfil,0,2)."000" != PERFIL_DIRECTOR
				&& substr($idPerfil,0,2)."000" != PERFIL_GERENTE
				&& $idPerfil != PERFIL_JEFE_PRESUPUESTO
				&& $idPerfil != PERFIL_ASISTENTE_EJECUTIVO
				&& $idPerfil != PERFIL_DIRECTOR_EJECUTIVO
				&& $idPerfil != PERFIL_ASISTENTE_PRESIDENCIA
				&& $idPerfil != PERFIL_PRESIDENTE
			)
				throw new Exception("Error al enviar. Operaci&oacute;n no permitida para este perfil");
				
			$wFCadena = SafiModeloWFCadena::GetWFNextCadenaByIdDocument($idAvance);
			
			if($wFCadena == null)
				throw new Exception("Error al enviar. WFCadena inicial no encontrada");
				
			if($wFCadena->GetWFCadenaHijo() == null)
				throw new Exception("Error al enviar. WFCadena hija no encontrada");
			
			$findAvance = new EntidadAvance();
			$findAvance->SetId($idAvance);
			if(($avance = SafiModeloAvance::GetAvance($findAvance)) == null)
				throw new Exception("Error al enviar. El avance de id \"" . $idAvance . "\" no pudo ser cargado.");
			
			if($avance->GetDependencia() == null)
				throw new Exception("Error al enviar. Se produjo un problema durante la carga de \"avance dependencia\" ".
					" para el avance de id \"".$idAvance."\".");
				
			// 0 = Documento finalizado
			if(
				strcmp($wFCadena->GetWFCadenaHijo()->GetId(), "0") == 0 ||
				// Finalizar si está en presupuesto y la gerencia es la oficina de gestión administrativa y financiera
				($idPerfil == PERFIL_JEFE_PRESUPUESTO && $avance->GetDependencia()->GetId() == "450")
			){
				// Obtener una instancia de docgenera para el avance a enviar (actualizar)
				if(($docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idAvance)) == null)
					throw new Exception("Error al enviar. El DocGenera no pudo ser cargado para el avance de id \"".$idAvance."\".");
				
				$estadoAprobado = 13;
				
				$docGenera->SetIdWFObjeto(99);
				$docGenera->SetIdWFCadena(0);
				$docGenera->SetIdEstatus($estadoAprobado);
				$docGenera->SetIdPerfilActual(null);
				
				$Revisiones = new EntidadRevisionesDoc();
					
				$Revisiones->SetIdDocumento($idAvance);
				$Revisiones->SetLoginUsuario($loginUsuario);
				$Revisiones->SetIdPerfil($idPerfil);
				$Revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
				$Revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
				
				// Guardar el registro del documento en docGenera (estado de la cadena)
				if(($enviado = SafiModeloDocGenera::EnviarDocumento($docGenera, $Revisiones)) === false)
					throw new Exception("Error al enviar. No se pudo actualizar docGenera o revisionesDoc.");
			}
			else
			{
				if (($wFCadenaHijo = SafiModeloWFCadena::GetWFCadena($wFCadena->GetWFCadenaHijo())) == null)
					throw new Exception("Error al enviar. WFCadena hija no encontrada");
					
				if($wFCadenaHijo->GetWFGrupo() == null)
					throw new Exception("Error al enviar. WFGrupo de WFCadena hija no encontrado");
					
				if(($perfilActual = SafiModeloDependenciaCargo::
					GetSiguienteCargoDeCadena($idDependencia, $wFCadenaHijo->GetWFGrupo()->GetPerfiles())) == null)
					throw new Exception("Error al enviar. No se puede encontrar el perfil de la siguiente instancia en la cadena");
				
				// Obtener una instancia de docgenera para el avance a enviar (actualizar)
				if(($docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idAvance)) == null)
					throw new Exception("Error al devolver. El DocGenera no pudo ser cargado para el avance de id \"".$idAvance."\".");
				
				$estadoEntransito = 10;
				
				$docGenera->SetIdWFObjeto($wFCadena->GetWFObjetoInicial()->GetId());
				$docGenera->SetIdWFCadena($wFCadena->GetId());
				$docGenera->SetIdEstatus($estadoEntransito);
				$docGenera->SetIdPerfilActual($perfilActual->GetId());
				
				$Revisiones = null;
				
				if(
					substr($idPerfil,0,2)."000" != PERFIL_ASISTENTE_ADMINISTRATIVO &&
					$idPerfil != PERFIL_ASISTENTE_EJECUTIVO &&
					$idPerfil != PERFIL_ASISTENTE_PRESIDENCIA
				){
					$Revisiones = new EntidadRevisionesDoc();
					
					$Revisiones->SetIdDocumento($idAvance);
					$Revisiones->SetLoginUsuario($loginUsuario);
					$Revisiones->SetIdPerfil($idPerfil);
					$Revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
					$Revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
				}
				
				// Guardar el registro del documento en docGenera (estado de la cadena)
				if(SafiModeloDocGenera::EnviarDocumento($docGenera, $Revisiones) === false)
					throw new Exception("Error al enviar. No se pudo actualizar docGenera o revisionesDoc.");
			}
			
			return true;
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
			return false;
		}
	}
	
	public function Devolver()
	{
		try
		{
			$GLOBALS['SafiErrors']['general'] = array();
			$GLOBALS['SafiInfo']['general']  = array();
			
			$preMsg = "Error en la devolución del avance.";
			$idDependencia = $_SESSION['user_depe_id'];
			$idPerfil = $_SESSION['user_perfil_id'];
			$loginUsuario = $_SESSION['login'];
			$opcionDevolver = 5;
			$fechaHoy = date("d/m/Y H:i:s");
			
			if(!isset($_REQUEST['idAvance']) || ($idAvance=trim($_REQUEST['idAvance'])) == '')
				throw new Exception($preMsg." Identificador del avance no encontrado.");
					
			if(!isset($_REQUEST['memoContent']) || ($memoContent=trim($_REQUEST['memoContent'])) == '')
				throw new Exception($preMsg." Motivo de la devoluci&oacute;n no encontrado para el avance \"".$idAvance."\".");
			
			if(	
				$idPerfil == PERFIL_JEFE_PRESUPUESTO ||
				$idPerfil == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS
			){
				$documento = new EntidadDocumento();
				$documento->SetId(GetConfig("preCodigoAvance"));
				
				$wFOpcion = new EntidadWFOpcion();
				$wFOpcion->SetId($opcionDevolver);
				
				$wFGrupo = SafiModeloWFGrupo::GetWFGrupoByIdPerfil($idPerfil);
				
				if ($wFGrupo == null)
					throw new Exception($preMsg." WFGrupo no encontrado para el perfil actual. Avance \"".$idAvance."\".");
				
				$findAvance = new EntidadAvance();
				$findAvance->SetId($idAvance);
				if(($avance=SafiModeloAvance::GetAvance($findAvance)) == null)
					throw new Exception($preMsg." El avance de id \"" . $idAvance . "\" no pudo ser cargado.");
				
				if($avance->GetDependencia() == null)
					throw new Exception(" Se produjo un problema durante la carga de \"avance dependencia\" ".
						" para el avance \"".$idAvance."\".");
					
				$dependencia = new EntidadDependencia();
				if($avance->GetDependencia()->GetId()=="350" || $avance->GetDependencia()->GetId()=="150"){
					$dependencia->SetId($avance->GetDependencia()->GetId());
				} else {
					$dependencia->SetId(null);
				}
				
				$findWFCadena = new EntidadWFCadena();
				$findWFCadena->SetDocumento($documento);
				$findWFCadena->SetWFOPcion($wFOpcion);
				$findWFCadena->SetWFGrupo($wFGrupo);
				$findWFCadena->SetDependencia($dependencia);
				if(($wFCadena = SafiModeloWFCadena::GetWFCadena($findWFCadena)) == null)
					throw new Exception($preMsg." WFCadena inicial no encontrada. Avance \"".$idAvance."\".");
					
				// Obtener una instancia de docgenera para el avance (actualizar)
				if(($docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idAvance)) == null)
					throw new Exception($preMsg." El DocGenera no pudo ser cargado para el avance \"".$idAvance."\".");
				
				$perfilActual = $docGenera->GetIdPerfil();
				$estadoDevuelto = 7;
				
				$docGenera->SetIdWFObjeto($wFCadena->GetWFObjetoInicial()->GetId());
				$docGenera->SetIdWFCadena($wFCadena->GetId());
				$docGenera->SetIdEstatus($estadoDevuelto);
				$docGenera->SetIdPerfilActual($perfilActual);
				
				$memo = new EntidadMemo();
				$memo->SetLoginUsuario($loginUsuario);
				$memo->SetAsunto(utf8_decode('Devolución de Avance'));
				$memo->SetContenido($memoContent);
				$memo->SetIdDependencia($idDependencia);
				$memo->SetFechaCreacion($fechaHoy);
				
				$revisiones = new EntidadRevisionesDoc();
				$revisiones->SetIdDocumento($idAvance);
				$revisiones->SetLoginUsuario($loginUsuario);
				$revisiones->SetIdPerfil($idPerfil);
				$revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
				$revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
				
				if(SafiModeloDocGenera::DevolverDocumento($docGenera, $memo, $revisiones) === false)
					throw new Exception($preMsg);
				
				$GLOBALS['SafiInfo']['general'][] = "Avance \"" . $idAvance . "\" devuelto satisfactoriamente.";
			} // Fin de if(
			  //			$_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO ||
			  //			$_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS
			  //		)
			else if(	
				substr($idPerfil,0,2)."000" == PERFIL_DIRECTOR ||
				substr($idPerfil,0,2)."000" == PERFIL_GERENTE ||
				$idPerfil == PERFIL_DIRECTOR_EJECUTIVO ||
				$idPerfil == PERFIL_PRESIDENTE
			){
				$documento = new EntidadDocumento();
				$documento->SetId(GetConfig("preCodigoAvance"));
				
				$wFOpcion = new EntidadWFOpcion();
				$wFOpcion->SetId($opcionDevolver);
				
				if(
					$idPerfil == PERFIL_DIRECTOR_EJECUTIVO ||
					$idPerfil == PERFIL_PRESIDENTE
				){
					$wFGrupo = SafiModeloWFGrupo::GetWFGrupoByIdPerfil($idPerfil);
				} else {
					$wFGrupo = SafiModeloWFGrupo::GetWFGrupoByIdPerfil(substr($idPerfil,0,2)."000");
				}
				
				if ($wFGrupo == null)
					throw new Exception($preMsg." WFGrupo no encontrado para el perfil actual. Avance \"".$idAvance."\".");
					
				$findAvance = new EntidadAvance();
				$findAvance->SetId($idAvance);
				if(($avance=SafiModeloAvance::GetAvance($findAvance)) == null)
					throw new Exception($preMsg." El avance \"" . $idAvance . "\" no pudo ser cargado.");
				
				if($avance->GetDependencia() == null)
					throw new Exception($preMsg." Se produjo un problema durante la carga de \"avance dependencia\" ".
						" para el avance \"".$idAvance."\".");
				
				$dependencia = new EntidadDependencia();
				if($avance->GetDependencia()->GetId()=="350" || $avance->GetDependencia()->GetId()=="150"){
					$dependencia->SetId($avance->GetDependencia()->GetId());
				} else {
					$dependencia->SetId(null);
				}
				
				$findWFCadena = new EntidadWFCadena();
				$findWFCadena->SetDocumento($documento);
				$findWFCadena->SetWFOPcion($wFOpcion);
				$findWFCadena->SetWFGrupo($wFGrupo);
				$findWFCadena->SetDependencia($dependencia);
				if(($wFCadena = SafiModeloWFCadena::GetWFCadena($findWFCadena)) == null)
					throw new Exception($preMsg." WFCadena inicial no encontrada. Avance \"".$idAvance."\".");
					
				if($wFCadena->GetWFCadenaHijo() == null)
					throw new Exception($preMsg." WFCadena hija no encontrada. Avance \"".$idAvance."\".");
						
				// Obtener la cadena siguiente, a la inicial, de rendición de viáticos nacionales
				if (($wFCadenaHijo = SafiModeloWFCadena::GetWFCadena($wFCadena->GetWFCadenaHijo())) == null)
					throw new Exception($preMsg." WFCadena hija no pudo ser cargada. Avance \"".$idAvance."\".");
					
				if($wFCadenaHijo->GetWFGrupo() == null)
					throw new Exception($preMsg." WFGrupo de WFCadena hija no encontrado. Avance \"".$idAvance."\".");
					
				if(($perfilActual = SafiModeloDependenciaCargo::
					GetSiguienteCargoDeCadena($idDependencia, $wFCadenaHijo->GetWFGrupo()->GetPerfiles())) == null)
					throw new Exception($preMsg." No se puede encontrar el perfil de la siguiente '
						.'instancia en la cadena. Avance \"".$idAvance."\".");

				// Obtener una instancia de docgenera para el avance (actualizar)
				if(($docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idAvance)) == null)
					throw new Exception($preMsg." El DocGenera no pudo ser cargado para el avance \"".$idAvance."\".");
					
				$estadoDevuelto = 7;
						
				$docGenera->SetIdWFObjeto($wFCadena->GetWFObjetoInicial()->GetId());
				$docGenera->SetIdWFCadena($wFCadena->GetId());
				$docGenera->SetIdEstatus($estadoDevuelto);
				$docGenera->SetIdPerfilActual($perfilActual->GetId());
				
				$memo = new EntidadMemo();
				$memo->SetLoginUsuario($loginUsuario);
				$memo->SetAsunto(utf8_decode('Devolución de Avance'));
				$memo->SetContenido($memoContent);
				$memo->SetIdDependencia($idDependencia);
				$memo->SetFechaCreacion($fechaHoy);
				
				$revisiones = new EntidadRevisionesDoc();
				$revisiones->SetIdDocumento($idAvance);
				$revisiones->SetLoginUsuario($loginUsuario);
				$revisiones->SetIdPerfil($idPerfil);
				$revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
				$revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
				
				if(SafiModeloDocGenera::DevolverDocumento($docGenera, $memo, $revisiones) === false)
					throw new Exception($preMsg);
				
				$GLOBALS['SafiInfo']['general'][] = "Avance \"" . $idAvance . "\" devuelto satisfactoriamente.";
			} // Fin de else if(	
			  //		substr($idPerfil,0,2)."000" == PERFIL_DIRECTOR ||
			  //		substr($idPerfil,0,2)."000" == PERFIL_GERENTE ||
			  //		$idPerfil == PERFIL_DIRECTOR_EJECUTIVO ||
			  //		$idPerfil == PERFIL_PRESIDENTE
			  //	)
			else
			{
				$GLOBALS['SafiErrors']['general'][] = $preMsg." Operaci&oacute;n no permitida para este perfil.";
			}
			
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			$GLOBALS['SafiErrors']['general'][] = "Error en la devoluci&oacute;n del avance.";
		}
		
		$this->Bandeja();
	}
	
	public function Anular()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
			
		try
		{
			$idPerfil = $_SESSION['user_perfil_id'];
			$loginUsuario = $_SESSION['login'] ;
			
			// Validar el id del avance
			if(!isset($_REQUEST['idAvance']) || ($idAvance=trim($_REQUEST['idAvance'])) == '')
				throw new Exception('Identificador del avance no encontrado');

			if(	
				substr($idPerfil,0,2)."000" != PERFIL_ASISTENTE_ADMINISTRATIVO
				&& $idPerfil != PERFIL_ASISTENTE_EJECUTIVO
				&& $idPerfil != PERFIL_ASISTENTE_PRESIDENCIA
			)
				throw new Exception("Error al anular. Operaci&oacute;n no permitida para este perfil");
				
			$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idAvance);
				
			$estadoAnulado = 15;
			$wfObjetoAnulado = 98;
			$wFOpcionAnular = 24;
			
			$docGenera->SetIdWFObjeto($wfObjetoAnulado);
			$docGenera->SetIdWFCadena(0);
			$docGenera->SetIdEstatus($estadoAnulado);
			$docGenera->SetIdPerfilActual(null);
			
			$revisiones = new EntidadRevisionesDoc();
			$revisiones->SetIdDocumento($idAvance);
			$revisiones->SetLoginUsuario($loginUsuario);
			$revisiones->SetIdPerfil($idPerfil);
			$revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
			$revisiones->SetIdWFOpcion($wFOpcionAnular);
			
			if(SafiModeloDocGenera::AnularDocumento($docGenera, $revisiones) === false)
				throw new Exception('Error al anular. No se pudo actualizar docGenera o insertar las revisiones.');
			
			$GLOBALS['SafiInfo']['general'][] = "Avance \"".$idAvance."\" anulado satisfactoriamente.";
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
		}
		
		$this->Bandeja();
	}
	
	public function Buscar()
	{
		try {
			$form = FormManager::GetForm(FORM_BUSCAR_AVANCE);
			$existCriteria = false;
			
			$idDependencia = $_SESSION['user_depe_id'];
			$idUserPerfil = $_SESSION['user_perfil_id'];
			
			// Validar la fecha de inicio
			if(isset($_POST['fechaInicio']) && trim($_POST['fechaInicio']) != '')
			{
				$fecha = explode('/', trim($_POST['fechaInicio']));
				
				if (count($fecha) != 3)
					throw new Exception("Fecha inicio inv&aacute;lida.");
				
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				
				if(!checkdate ($month ,$day ,$year))
					throw new Exception("Fecha inicio inv&aacute;lida.");
				
				$form->SetFechaInicio($day . '/' . $month . '/' . $year);
				
				$existCriteria = true;
			}
			
			// Validar la fecha de fin
			if(isset($_POST['fechaFin']) && trim($_POST['fechaFin']) != '')
			{
				$fecha = explode('/', trim($_POST['fechaFin']));
				
				if (count($fecha) != 3)
					throw new Exception("Fecha fin inv&aacute;lida.");
				
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				
				if(!checkdate ($month ,$day ,$year))
					throw new Exception("Fecha fin inv&aacute;lida.");
				
				$form->SetFechaFin($day . '/' . $month . '/' . $year);
				
				$existCriteria = true;
			}
			
			$avance = null;
			$docGenera = null;
			
			// Validar el id del avance
			if(
				isset($_POST['idAvance'])
				&& ($idRendicion=trim($_POST['idAvance'])) != ''
				&& strlen($idRendicion) >= 5
				&& $idRendicion != GetConfig("preCodigoAvance").GetConfig("delimitadorPreCodigoDocumento")
			){
				if($form->GetAvance() == null) $form->SetAvance(new EntidadAvance());
				$avance = $form->GetAvance();
				$avance->SetId(trim($_POST['idAvance']));
				
				$existCriteria = true;
			}
			
			$params = array();
			
			$params['fechaInicio'] = $form->GetFechaInicio();
			$params['fechaFin'] = $form->GetFechaFin();
			
			if($existCriteria)
			{
				$dependencia = null;
				if(
					strcmp($idUserPerfil, PERFIL_ANALISTA_CONTABLE) != 0 &&
					strcmp($idUserPerfil, PERFIL_ANALISTA_ORDENACION_PAGOS) != 0 &&
					strcmp($idUserPerfil, PERFIL_ANALISTA_PRESUPUESTO) != 0 &&
					strcmp($idUserPerfil, PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS) != 0 &&
					strcmp($idUserPerfil, PERFIL_DIRECTOR_EJECUTIVO) != 0 &&
					strcmp($idUserPerfil, PERFIL_DIRECTOR_PRESUPUESTO) != 0 &&
					strcmp($idUserPerfil, PERFIL_JEFE_ORDENACION_CONTABILIDAD) != 0 &&
					strcmp($idUserPerfil, PERFIL_JEFE_PRESUPUESTO) != 0 &&
					strcmp($idUserPerfil, PERFIL_PRESIDENTE) != 0
				){
					if(
						strcmp(
							GetCargoFundacionFromIdPerfil($idUserPerfil), 
							GetCargoFundacionFromIdPerfil(PERFIL_ASISTENTE_ADMINISTRATIVO)
						) == 0
					){
						// 450 = dependencia Oficina de gestión administrativa y financiera
						if($idDependencia != "450")
						{
							$dependencia = new EntidadDependencia();
							$dependencia->SetId($idDependencia);
						}
					} else {
						$dependencia = new EntidadDependencia();
						$dependencia->SetId($idDependencia);
					}
				}
				
				if($avance == null) $avance = new EntidadAvance();
				$avance->SetDependencia($dependencia);
				/*
				if(
					strcmp($idUserPerfil, PERFIL_ANALISTA_CONTABLE) != 0 &&
					strcmp($idUserPerfil, PERFIL_ANALISTA_ORDENACION_PAGOS) != 0 &&
					strcmp($idUserPerfil, PERFIL_ANALISTA_PRESUPUESTO) != 0 &&
					strcmp($idUserPerfil, PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS) != 0 &&
					strcmp($idUserPerfil, PERFIL_DIRECTOR_EJECUTIVO) != 0 &&
					strcmp($idUserPerfil, PERFIL_DIRECTOR_PRESUPUESTO) != 0 &&
					strcmp($idUserPerfil, PERFIL_JEFE_PRESUPUESTO) != 0 &&
					strcmp($idUserPerfil, PERFIL_PRESIDENTE) != 0
					// 450 = dependencia Oficina de gestión administrativa y financiera
					&& (	strcmp(
								GetCargoFundacionFromIdPerfil($idUserPerfil), 
								GetCargoFundacionFromIdPerfil(PERFIL_ASISTENTE_ADMINISTRATIVO)
							) == 0
						&& $idDependencia != "450")
				){
					$dependencia = new EntidadDependencia();
					$dependencia->SetId($idDependencia);
					
					if($avance == null) $avance = new EntidadAvance();
					$avance->SetDependencia($dependencia);
				}
				*/
				
				$dataAvances = SafiModeloAvance::BuscarAvance($avance, $docGenera, $params);
				$form->SetDataAvances($dataAvances);
				
				if($dataAvances != null)
				{
					$cargoFundaciones = array();
					$idDependencias = array();
					$usuaLogins = array();
				
					foreach($dataAvances as $dataAvance){
						$avance = $dataAvance['ClassAvance'];
						$docGenera = $dataAvance['ClassDocGenera'];
						
						/* Para obtener los datos de la instancia actual */
						$idPerfilActual = $docGenera->GetIdPerfilActual();
						if($idPerfilActual != null && $idPerfilActual != '')
						{
							$cargoFundacion = GetCargoFundacionFromIdPerfil($idPerfilActual);
							$cargoFundaciones[$cargoFundacion] = $cargoFundacion;
							
							$idDependencia = GetIdDependenciaFromIdPerfil($idPerfilActual);
							$idDependencias[$idDependencia] = $idDependencia; 
						}
						
						/* para obtener los datos de el usuario que eleboró el avance*/
						$usuaLogins[] = $docGenera->GetUsuaLogin();
					}
					
					if(count($cargoFundaciones)>=0){
						$cargoFundacionInstanciaActuales = SafiModeloCargo::GetCargoByCargoFundaciones($cargoFundaciones);
						$GLOBALS['SafiRequestVars']['avanceCargoFundacionInstanciaActuales'] = $cargoFundacionInstanciaActuales;
					}
					
					if(count($idDependencias)>0){
						$dependenciaInstanciaActuales = SafiModeloDependencia::GetDependenciaByIds($idDependencias);
						$GLOBALS['SafiRequestVars']['avanceDependenciaInstanciaActuales'] = $dependenciaInstanciaActuales;
					}
					
					if(count($usuaLogins)>0){
						$usuaLogins = array_unique($usuaLogins);
						$empleadosElaboradores = SafiModeloEmpleado::GetEmpleadosByUsuaLogins($usuaLogins);
						$GLOBALS['SafiRequestVars']['avanceEmpleadosElaboradores'] = $empleadosElaboradores;
					}
					
					$estatusList = SafiModeloEstatus::GetAllEstatus();
					$GLOBALS['SafiRequestVars']['estatusList'] = $estatusList;
				}
				
				
			} // Fin de if($existCriteria)
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
		}
		
		require(SAFI_VISTA_PATH . "/avan/buscarAvance.php");
	}
	
	public function GenerarPDF()
	{
		try
		{
			if(!isset($_REQUEST['idAvance']) || ($idAvance=trim($_REQUEST['idAvance'])) == '')
				throw new Exception("No se ha encontrado ning&uacute;n  identificador para el avance");
			
			if (!isset($_REQUEST['tipo']) || ($tipo=trim($_REQUEST['tipo'])) == '')
				throw new Exception("No se ha encontrado el tipo de documento a imprimir (Lineal o firmas por p&aacute;ginas)");
			
			$findAvance = new EntidadAvance();
			$findAvance->SetId($idAvance);
			$avance = SafiModeloAvance::GetAvance($findAvance);
			
			if($avance == null)
				throw new Exception("El avance \"".$idAvance."\" no pudo ser cargado.");
			
			$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($avance->GetId());
			
			if($docGenera == null)
				throw new Exception("El docGenera para el avance \"".$idAvance."\" no pudo ser cargado.");
			
			$elaboradoPor = SafiModeloEmpleado::GetEmpleadoByCedula($avance->GetUsuaLogin());
			
			// Cargo de director o gerente
			$cargosGerenteDirector = GetPerfilCargosGerenteDirectorByIdUserPerfil($docGenera->GetIdPerfil());
			
			$cargoGerenteDirector = SafiModeloDependenciaCargo::GetSiguienteCargoDeCadena
				($avance->GetDependencia()->GetId(), $cargosGerenteDirector);
				
			$perfilGerenteDirector = $cargoGerenteDirector->GetId();
			// Fin cargo de director o gerente
			
			// Obtener los datos de las firmas del documento
			$arrFirmas = SafiModeloFirma::GetFirmaByPerfiles(array(
					$perfilGerenteDirector,
					PERFIL_DIRECTOR_EJECUTIVO,
					PERFIL_DIRECTOR_PRESUPUESTO,
					PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS,
					PERFIL_PRESIDENTE
			));
			
			$GLOBALS['SafiRequestVars']['arrFirmas'] = $arrFirmas;
			$GLOBALS['SafiRequestVars']['perfilGerenteDirector'] = $perfilGerenteDirector;
			$GLOBALS['SafiRequestVars']['tipo'] = $tipo;
			$GLOBALS['SafiRequestVars']['avance'] = $avance;
			$GLOBALS['SafiRequestVars']['docGenera'] =  $docGenera;
			$GLOBALS['SafiRequestVars']['elaboradoPor'] = $elaboradoPor;
			
			require(SAFI_VISTA_PATH . "/avan/avance_PDF.php");
			return;
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
		}
		
		if(count($GLOBALS['SafiErrors']['general'])<=0){
			$GLOBALS['SafiErrors']['general'][] = "Se ha producido un error al intentar imprimir el avance. ".
				 "Comuniquese con el administrador del sistema.";
		}
		
		if($avance != null)
			$this->__VerDetalles(array('idAvance' => $avance->GetId()));
		else
			$this->Bandeja();
		
		return;
	}
	
	public function ArchivosPagos()
	{
		try {
			$form = FormManager::GetForm(FORM_ARCHIVOS_PAGOS_AVANCE);
			$existCriteria = false;
		
			$avance = null;			
			
			// Validar el id del avance
			if(isset($_REQUEST['idAvance']) && trim($_REQUEST['idAvance']) != '')
			{
				$form->SetIdAvance(trim($_REQUEST['idAvance']));
				$existCriteria = true;
			} /*else {
				$form->SetIdAvance("avan-");
			}*/
			
			if($existCriteria)
			{
				$findAvance = new EntidadAvance();
				$findAvance->SetId($form->GetIdAvance());
				$avance = SafiModeloAvance::GetAvance($findAvance);
				$form->SetAvance($avance);
				
				if($avance == null)
				{
					throw new Exception("El id del avance \"".$form->GetIdAvance()."\" es incorrecto o no existe.");
				} else {
					$form->SetFechaAbono(date('d/m/Y'));
				}
			}
			
		}catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
		}
		
		require(SAFI_VISTA_PATH . "/avan/archivosPagos.php");
		
	}
	
	public function GenerarArchivosPagos()
	{
		try {
			$form = FormManager::GetForm(FORM_ARCHIVOS_PAGOS_AVANCE);
			
			// Validar el id del avance
			if(!isset($_POST['idAvance']) || ($idAvance=trim($_POST['idAvance'])) == '')
				throw new Exception("Identificador del avance no encontrado.");
			
			// Validar la fecha de abono
			if(!isset($_POST['fechaAbono']) || trim($_POST['fechaAbono']) == '')
				throw new Exception("Debe indicar una fecha de abono.");
			
			if(($fecha = $this->__ValidarFecha($_POST['fechaAbono'])) === false)
				throw new Exception("Fecha de abono inv&aacute;lida.");
			
			// Llenar el formulario
			$form->SetIdAvance($idAvance);
			$form->SetFechaAbono($fecha);
			
			// Obtener una instancia del avance
			$findAvance = new EntidadAvance();
			$findAvance->SetId($idAvance);
			if(($avance = SafiModeloAvance::GetAvance($findAvance)) == null)
				throw new Exception("El avance \"".$idAvance."\" no pudo ser cargado.");
			
			// Obtener cada componente de la fecha de abono en un array
			$arrFechaAbono = explode('/', $form->GetFechaAbono());
			
			// Contruir el detalle del pago de cada responsable
			$personas = array(); // Arreglo que guardará la información de cada una de las personas onjetos de la transferencia
			$montoTotal = 0;
			foreach ($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidas)
			{
				$responsableAvance = $responsableAvancePartidas->GetResponsableAvance();
				$id = "";
				$tipoCuenta = "";
				$persona = array();
				
				// Obtener los datos del empleado/beneficiario
				if(
					$responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_EMPLEADO
					&& $responsableAvance->GetEmpleado() != null
				){
					$empleado = $responsableAvance->GetEmpleado();
					$id = $empleado->GetId();
					
					$persona["id"] = $id;
					$persona["nombres"] = $empleado->GetNombres();
					$persona["apellidos"] = $empleado->GetApellidos();
				}
				else if (
					$responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_BENEFICIARIO
					&& $responsableAvance->GetBeneficiario() != null
				){
					$beneficiario = $responsableAvance->GetBeneficiario();
					$id = $beneficiario->GetId();
					
					$persona["id"] = $id;
					$persona["nombres"] = $beneficiario->GetNombres();
					$persona["apellidos"] = $beneficiario->GetApellidos();
				}
				
				if(count($persona)>0){
					// Calcular el total del monto por responsable
					$responsableMontoTotal = 0;
					if(is_array($responsableAvancePartidas->GetAvancePartidas()))
					{
						foreach ($responsableAvancePartidas->GetAvancePartidas() as $avanPartida)
						{
							$responsableMontoTotal += $avanPartida->GetMonto();
							$montoTotal += $avanPartida->GetMonto();
						}
					}
					$persona["numeroCuenta"] = $responsableAvance->GetNumeroCuenta();
					$persona["montoTotal"] = $responsableMontoTotal;
					
					$personas[$persona["id"]] = $persona;
				}
				
			}// Fin foreach ($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidas)
			
			$datosArchivosPagos = array(
				"fechaAbono" => $form->GetFechaAbono(),
				"montoTotal" => $montoTotal,
				"personas" => $personas
			);
			
			$GLOBALS['SafiRequestVars']['datosArchivosPagos'] = $datosArchivosPagos;
			
			require(SAFI_VISTA_PATH . "/archivosPagos/archivosPagosBancoDeVenezuela.php");
		}
		catch (Exception $e)
		{
			echo $e->getMessage();	
		}
	}
	
	public function VerCategoriaAvanceInfo()
	{
		
		$categoriaViaticos = SafiModeloCategoriaViatico::GetAllCategoriasActivas();
		
		
		$GLOBALS['SafiRequestVars']['categoriaViaticos'] = $categoriaViaticos;
		
		require(SAFI_VISTA_PATH . "/vina/categoriaViaticoMasInfo.php");
	}
	
	public function ReporteResponsables()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
			
		try
		{
			$form = FormManager::GetForm(FORM_REPORTE_RESPONSABLES_AVANCE);
			$existCriteria = false;
			$idRendicion = null;
			$idAvance = null;
			$cedulaResponsable = null;
			$idEstado = null;
			$idRegionReporte = null;
			$estatusRendicion = null;
			
			$idDependencia = $_SESSION['user_depe_id'];
			$idUserPerfil = $_SESSION['user_perfil_id'];
			
			// Validar la fecha de inicio del avance
			if(isset($_POST['fechaInicio']) && trim($_POST['fechaInicio']) != '')
			{
				$fecha = explode('/', trim($_POST['fechaInicio']));
				
				if (count($fecha) != 3)
					throw new Exception("Fecha inicio del avance inv&aacute;lida.");
				
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				
				if(!checkdate ($month ,$day ,$year))
					throw new Exception("Fecha inicio del avance inv&aacute;lida.");
				
				$form->SetFechaInicio($day . '/' . $month . '/' . $year);
				
				$existCriteria = true;
			}
			
			// Validar la fecha de fin del avance
			if(isset($_POST['fechaFin']) && trim($_POST['fechaFin']) != '')
			{
				$fecha = explode('/', trim($_POST['fechaFin']));
				
				if (count($fecha) != 3)
					throw new Exception("Fecha fin del avance inv&aacute;lida.");
				
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				
				if(!checkdate ($month ,$day ,$year))
					throw new Exception("Fecha fin del avance inv&aacute;lida.");
				
				$form->SetFechaFin($day . '/' . $month . '/' . $year);
				
				$existCriteria = true;
			}
			
			// Validar la fecha de inicio de la rendición del avance
			if(isset($_POST['fechaRendicionInicio']) && trim($_POST['fechaRendicionInicio']) != '')
			{
				$fecha = explode('/', trim($_POST['fechaRendicionInicio']));
				
				if (count($fecha) != 3)
					throw new Exception("Fecha inicio de la rendici&oacute;n del avance inv&aacute;lida.");
				
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				
				if(!checkdate ($month ,$day ,$year))
					throw new Exception("Fecha inicio de la rendici&oacute;n del avance inv&aacute;lida.");
				
				$form->SetFechaRendicionInicio($day . '/' . $month . '/' . $year);
				
				$existCriteria = true;
			}
			
			// Validar la fecha de fin del avance
			if(isset($_POST['fechaRendicionFin']) && trim($_POST['fechaRendicionFin']) != '')
			{
				$fecha = explode('/', trim($_POST['fechaRendicionFin']));
				
				if (count($fecha) != 3)
					throw new Exception("Fecha fin de la rendici&oacute;n del avance inv&aacute;lida.");
				
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				
				if(!checkdate ($month ,$day ,$year))
					throw new Exception("Fecha fin de la rendici&oacute;n del avance inv&aacute;lida.");
				
				$form->SetFechaRendicionFin($day . '/' . $month . '/' . $year);
				
				$existCriteria = true;
			}
			
			// Validar el id de la rendición de avance
			if(
				isset($_POST['idRendicion'])
				&& ($__idRendicion=trim($_POST['idRendicion'])) != ''
				&& strlen($__idRendicion) >= 5
				&& $__idRendicion != GetConfig("preCodigoRendicionAvance").GetConfig("delimitadorPreCodigoDocumento")
			){
				$form->SetIdRendicion($__idRendicion);
				$idRendicion = $__idRendicion;
				$existCriteria = true;
			}
			
			// Validar el id del avance
			if(
				isset($_POST['idAvance'])
				&& ($__idAvance=trim($_POST['idAvance'])) != ''
				&& strlen($__idAvance) >= 5
				&& $__idAvance != GetConfig("preCodigoAvance").GetConfig("delimitadorPreCodigoDocumento")
			){
				$form->SetIdAvance($__idAvance);
				$idAvance = $__idAvance;
				$existCriteria = true;
			}
			
			// Validar la cédula del responsable
			if(
				isset($_POST['cedulaResponsable'])
				&& ($__cedulaResponsable=trim($_POST['cedulaResponsable'])) != ''
			){
				$form->SetCedulaResponsable($__cedulaResponsable);
				$cedulaResponsable = $__cedulaResponsable;
				$existCriteria = true;
			}
			
			// Validar el nombre del responsable
			if(
				isset($_POST['nombreResponsable'])
				&& ($__nombreResponsable=trim($_POST['nombreResponsable'])) != ''
			){
				$form->SetNombreResponsable($__nombreResponsable);
			}
			
			// Validar el id del estado del país
			if(isset($_POST['idEstado']) && ($__idEstado=trim($_POST['idEstado'])) != '' && $__idEstado != "0")
			{
				$form->SetIdEstado($__idEstado);
				$idEstado = $__idEstado;
				$existCriteria = true;
			}
			
			// Validar el id de la regionReporte
			if(isset($_POST['idRegionReporte']) && ($__idRegionReporte=trim($_POST['idRegionReporte'])) != '' && $__idRegionReporte != "0")
			{
				$form->SetIdRegionreporte($__idRegionReporte);
				$idRegionReporte = $__idRegionReporte;
				$existCriteria = true;
			}
			
			// Validar el estatus de rendición del avance
			if(isset($_POST['estatusRendicion']) && ($__estatusRendicion=trim($_POST['estatusRendicion'])) != '' && $__estatusRendicion != "0")
			{
				$form->SetEstatusRendicion($__estatusRendicion);
				$estatusRendicion = $__estatusRendicion;
				$existCriteria = true;
			}
			
			if($existCriteria)
			{
				$dependencia = null;
				if(
					strcmp($idUserPerfil, PERFIL_ANALISTA_CONTABLE) != 0 &&
					strcmp($idUserPerfil, PERFIL_ANALISTA_ORDENACION_PAGOS) != 0 &&
					strcmp($idUserPerfil, PERFIL_ANALISTA_PRESUPUESTO) != 0 &&
					strcmp($idUserPerfil, PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS) != 0 &&
					strcmp($idUserPerfil, PERFIL_DIRECTOR_EJECUTIVO) != 0 &&
					strcmp($idUserPerfil, PERFIL_DIRECTOR_PRESUPUESTO) != 0 &&
					strcmp($idUserPerfil, PERFIL_JEFE_ORDENACION_CONTABILIDAD) != 0 &&
					strcmp($idUserPerfil, PERFIL_JEFE_PRESUPUESTO) != 0 &&
					strcmp($idUserPerfil, PERFIL_PRESIDENTE) != 0
				){
					if(
						strcmp(
							GetCargoFundacionFromIdPerfil($idUserPerfil), 
							GetCargoFundacionFromIdPerfil(PERFIL_ASISTENTE_ADMINISTRATIVO)
						) == 0
					){
						// 450 = dependencia Oficina de gestión administrativa y financiera
						if($idDependencia != "450")
						{
							$dependencia = new EntidadDependencia();
							$dependencia->SetId($idDependencia);
						}
					} else {
						$dependencia = new EntidadDependencia();
						$dependencia->SetId($idDependencia);
					}
				}
				
				$params = array();
							
				if($form->GetFechaInicio() != null) $params['fechaInicio'] = $form->GetFechaInicio();
				if($form->GetFechaFin() != null) $params['fechaFin'] = $form->GetFechaFin();
				if($form->GetFechaRendicionInicio() != null) $params['fechaRendicionInicio'] = $form->GetFechaRendicionInicio();
				if($form->GetFechaRendicionFin() != null) $params['fechaRendicionFin'] = $form->GetFechaRendicionFin();
				if($idRendicion != null) $params['idRendicion'] = $idRendicion;
				if($idAvance != null) $params['idAvance'] = $idAvance;
				if($cedulaResponsable != null) $params['cedulaResponsable'] = $cedulaResponsable;
				if($idEstado != null) $params['idEstado'] = $idEstado;
				if($idRegionReporte != null) $params['idRegionReporte'] = $idRegionReporte;
				$params['dependencia'] = $dependencia;
				if($estatusRendicion != null) $params['estatusRendicion'] = $estatusRendicion;
				
				$dataAvances = SafiModeloAvance::ReporteResponsables($params);
				$form->SetDataAvances($dataAvances);
			}
			
			// Obtener todos los estados del país
			$estados = SafiModeloEstado::GetAllEstados2();
			// Obtener todas las regionReportes
			$regionReportes = SafiModeloRegionReporte::GetAllRegionReportes();
			// Obtener un listado de los estados con sus respectivas regiones
			$arrRegionReporteEstados = SafiModeloRegionReporteEstado::GetAllRegionReporteEstadoListByIdEstados();
			// Obtener todos los estatus posibles de los documentos, etc.
			$arrEstatus = SafiModeloEstatus::GetAllEstatus();
			
			$GLOBALS['SafiRequestVars']['estados'] =  $estados;
			$GLOBALS['SafiRequestVars']['regionReportes'] =  $regionReportes;
			$GLOBALS['SafiRequestVars']['arrRegionReporteEstados'] =  $arrRegionReporteEstados;
			$GLOBALS['SafiRequestVars']['arrEstatus'] =  $arrEstatus;
			
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
		}
		
		require(SAFI_VISTA_PATH . "/avan/reporteResponsablesAvance.php");
	}
	
	private function __ValidarFecha($fecha)
	{
		$valida = false;
		
		$arrFecha = explode('/', $fecha);
		if (count($arrFecha) == 3){
			$day = $arrFecha[0];
			$month = $arrFecha[1];
			$year = $arrFecha[2];
			if(checkdate ($month ,$day ,$year)){
				$valida = $day . '/' . $month . '/' . $year;
			}
		}
		
		return $valida;
	}
	
	// Obtener y validar la fecha del avance
	private function __ValidarFechaAvance(NuevoAvanceForm $form)
	{
		if(!isset($_POST['fechaAvance']) || trim($_POST['fechaAvance']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Debe indicar la fecha del avance.";
		} else {
			if(($fecha = $this->__ValidarFecha($_POST['fechaAvance'])) !== false){
				$form->GetAvance()->SetFechaAvance($fecha);
			} else {
				$GLOBALS['SafiErrors']['general'][] = "Fecha del avance inv&aacute;lida.";
			}
		}
	}
	
	// Obtener y validar la información de la categoría
	private function __ValidarCategoria(NuevoAvanceForm $form)
	{
		if(!isset($_POST['categoria']) || trim($_POST['categoria']) == '' || strcmp(trim($_POST['categoria']), '0') == 0){
			$GLOBALS['SafiErrors']['general'][] = "Debe seleccionar una categor&iacute;a.";
		} else {
			$categoria = new EntidadCategoriaViatico();
			$categoria->SetId($_POST['categoria']);
			$form->GetAvance()->SetCategoria($categoria);
		}
	}
	
	//  Obtener y validar la información de la red
	private function __ValidarRed($form){
		if(	isset($_POST['red']) && trim($_POST['red']) != '' &&
			isset($_POST['categoria']) && trim($_POST['categoria']) != '' && strcmp(trim($_POST['categoria']), '2') == 0
		){
				$red = new EntidadRed();
				$red->SetId($_POST['red']);
				$form->GetAvance()->SetRed($red);
		}
	}
	
	// Obtener y validar el tipo de proyecto o acción centralizada
	private function __ValidarTipoProyectoAccionCentralizada(NuevoAvanceForm $form){
		if(!isset($_POST['tipoProyectoAccionCentralizada']) || trim($_POST['tipoProyectoAccionCentralizada']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Debe seleccionar un tipo (Proyecto/Acci&oacute;n centralizada).";
		} else if($_POST['tipoProyectoAccionCentralizada'] != EntidadProyectoAccionCentralizada::TIPO_PROYECTO &&
				$_POST['tipoProyectoAccionCentralizada'] != EntidadProyectoAccionCentralizada::TIPO_ACCION_CENTRALIZADA){
			$GLOBALS['SafiErrors']['general'][] = "El tipo de Proyecto/Acci&oacute;n centralizada es incorrecto.";
		} else {
			$form->GetAvance()->SetTipoProyectoAccionCentralizada($_POST['tipoProyectoAccionCentralizada']);
		}
	}
	
	// Obtener y validar de forma general el id del proyecto/accion centralizada
	private function __ValidarIdProyectoAccionCentralizada(NuevoAvanceForm $form){
		if(	!isset($_POST['proyectoAccionCentralizada']) || trim($_POST['proyectoAccionCentralizada']) == '' ||
			trim($_POST['proyectoAccionCentralizada']) == '0'
		){
			$GLOBALS['SafiErrors']['general'][] = "Debe seleccionar un Proyecto/Acci&oacute;n centralizada.";
		} else
		{
			$tipo = $form->GetAvance()->GetTipoProyectoAccionCentralizada();
			if ($tipo == EntidadProyectoAccionCentralizada::TIPO_PROYECTO)
			{
				$proyecto = new EntidadProyecto();
				$proyecto->SetId($_POST['proyectoAccionCentralizada']);
				$form->GetAvance()->SetProyecto($proyecto);
			} else if ($tipo == EntidadProyectoAccionCentralizada::TIPO_ACCION_CENTRALIZADA)
			{
				$accionCentralizada = new EntidadAccionCentralizada();
				$accionCentralizada->SetId($_POST['proyectoAccionCentralizada']);
				$form->GetAvance()->SetAccionCentralizada($accionCentralizada);
			}
		}
	}
	
	// Obtener y validar de forma general el id de la acción específica
	private function __ValidarIdAccionEspecifica(NuevoAvanceForm $form){
		
		if(	!isset($_POST['accionEspecifica']) || trim($_POST['accionEspecifica']) == '' ||
			trim($_POST['accionEspecifica']) == '0'
		){
			$GLOBALS['SafiErrors']['general'][] = "Debe seleccionar una acci&oacute;n espec&iacute;fica.";
		} else {
		$tipo = $form->GetAvance()->GetTipoProyectoAccionCentralizada();
			if ($tipo == EntidadProyectoAccionCentralizada::TIPO_PROYECTO)
			{
				$proyectoEspecifica = new EntidadProyectoEspecifica();
				$proyectoEspecifica->SetId($_POST['accionEspecifica']);
				$form->GetAvance()->SetProyectoEspecifica($proyectoEspecifica);
			} else if ($tipo == EntidadProyectoAccionCentralizada::TIPO_ACCION_CENTRALIZADA)
			{
				$accionCentralizadaEspecifica = new EntidadAccionCentralizadaEspecifica();
				$accionCentralizadaEspecifica->SetId($_POST['accionEspecifica']);
				$form->GetAvance()->SetAccionCentralizadaEspecifica($accionCentralizadaEspecifica);
			}
		}
	}
	
	// Obtener y validar la fecha de inicio de la actividad
	private function __ValidarFechaInicioActividad(NuevoAvanceForm $form)
	{
		if(!isset($_POST['fechaInicioActividad']) || trim($_POST['fechaInicioActividad']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Debe indicar la fecha inicio de la actividad.";
		} else {
			if(($fecha = $this->__ValidarFecha($_POST['fechaInicioActividad'])) !== false){
				$form->GetAvance()->SetFechaInicioActividad($fecha);
			} else {
				$GLOBALS['SafiErrors']['general'][] = "Fecha inicio de la actividad inv&aacute;lida.";
			}
		}
	}
	
	// Obtener y validar la fecha de fin de la actividad
	private function __ValidarFechaFinActividad(NuevoAvanceForm $form)
	{
		if(!isset($_POST['fechaFinActividad']) || trim($_POST['fechaFinActividad']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Debe indicar la fecha fin de la actividad.";
		} else {
			if(($fecha = $this->__ValidarFecha($_POST['fechaFinActividad'])) !== false){
				$form->GetAvance()->SetFechaFinActividad($fecha);
			} else {
				$GLOBALS['SafiErrors']['general'][] = "Fecha fin de la actividad inv&aacute;lida.";
			}
		}
	}
	
	// Obtener y validar los objetivos
	private function __ValidarObjetivos(NuevoAvanceForm $form)
	{
		if(!isset($_POST['objetivos']) || trim($_POST['objetivos']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Debe indicar los objetivos.";
		} else {
			$form->GetAvance()->SetObjetivos($_POST['objetivos']);
		}
	}
	
	// Obtener y validar la descripcion
	private function __ValidarDescripcion(NuevoAvanceForm $form)
	{
		if(isset($_POST['descripcion']) && trim($_POST['descripcion']) != ''){
			$form->GetAvance()->SetDescripcion($_POST['descripcion']);
		}
	}
	
	// Obtener y validar la justificiación
	private function __ValidarJustificacion(NuevoAvanceForm $form)
	{
		if(isset($_POST['justificacion']) && trim($_POST['justificacion']) != ''){
			$form->GetAvance()->SetJustificacion($_POST['justificacion']);
		}
	}
	
	// Obtener y validar la nroParticipantes
	private function __ValidarNroParticipantes(NuevoAvanceForm $form)
	{
		if(!isset($_POST['nroParticipantes']) || trim($_POST['nroParticipantes']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Debe indicar el n&uacute;mero de participantes.";
		} else {
			$form->GetAvance()->SetNroParticipantes(trim($_POST['nroParticipantes']));
		}
	}
	
	// Obtener y validar los infocentros
	private function __ValidarInfocentros(NuevoAvanceForm $form){
		if(isset($_POST['infocentros']) && is_array($_POST['infocentros']))
		{
			$infocentros = SafiModeloInfocentro::GetInfocentrosByIds($_POST['infocentros']);
			$form->GetAvance()->SetInfocentros($infocentros);
		}
		
	}
	
	// Obtener y validar los responsables del avance y sus partidas
	private function __ValidarResponsablesAvancePartidas(NuevoAvanceForm $form, $annoPresupuesto)
	{
		$responsablesAvancePartidas = array();
		
		// Validar los responsablesAvancePArtidas
		if(!isset($_POST['tiposResponsables']) || !is_array($_POST['tiposResponsables'])){
			$GLOBALS['SafiErrors']['general'][] = "Debe indicar al menos un responsable del avance. ".
				"Identificador del tipo de responsable no encontrado.";
		} else {
			$countTiposResponsables = count($_POST['tiposResponsables']);
			if($countTiposResponsables > 0)
			{
				$indexResponsablesLocal = 0;
				// Crear entidades responsablesAvancePartidas
				foreach ($_POST['tiposResponsables'] as $tipoResponsable)
				{
					$responsableAvancePartidas = new EntidadResponsableAvancePartidas();
					$responsableAvance = new EntidadResponsableAvance();
					$responsableAvancePartidas->SetResponsableAvance($responsableAvance);
					
					if($tipoResponsable == EntidadResponsable::TIPO_EMPLEADO){
						$responsableAvance->SetTipoResponsable($tipoResponsable);	
					} else if($tipoResponsable == EntidadResponsable::TIPO_BENEFICIARIO){
						$responsableAvance->SetTipoResponsable($tipoResponsable);
					} else {
						$responsableAvance->SetTipoResponsable(EntidadResponsable::TIPO_NINGUNO);
						$GLOBALS['SafiErrors']['general'][] = "Debe seleccionar el responsable[".($indexResponsablesLocal+1)."].";
					}
					$indexResponsablesLocal++;
					
					$responsablesAvancePartidas[] = $responsableAvancePartidas;
				}
				
				// Validar cedulasResponsables
				$itemRequestName = 'cedulasResponsables';
				if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
					$GLOBALS['SafiErrors']['general'][] = "Uno o mas responsables carecen de c&eacute;dula.";
				} else {
					$countItems = count($_POST[$itemRequestName]);
					if($countTiposResponsables != $countItems) {
						$GLOBALS['SafiErrors']['general'][] = "El n&uacute;mero de responsables y de c&eacute;dulas no coincide.";
					} else {
						$indexResponsablesLocal = 0;
						foreach($_POST[$itemRequestName] as $item){
							
							$responsableAvancePartidas = $responsablesAvancePartidas[$indexResponsablesLocal];
							$responsableAvance = $responsableAvancePartidas->GetResponsableAvance();
							if(trim($item) != '')
							{
								if($responsableAvance->GetTipoResponsable() != EntidadResponsable::TIPO_NINGUNO){
									if($responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_EMPLEADO){
										$empleado = SafiModeloEmpleado::GetEmpleadoActivoByCedula2(trim($item));
										$responsableAvance->SetEmpleado($empleado);
									} else if($responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_BENEFICIARIO){
										$beneficiario = SafiModeloBeneficiarioViatico::GetBeneficiarioViaticoActivoByCedula2(trim($item));
										$responsableAvance->SetBeneficiario($beneficiario);
									}
								}
							} else {
								$GLOBALS['SafiErrors']['general'][] = "C&eacute;dula del responsable[".
									($indexResponsablesLocal+1)."] no encontrada.";
							}
							$indexResponsablesLocal++;
						}
					}
				}
				
				// Validar estadosResponsables
				$itemRequestName = 'estadosResponsables';
				if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
					$GLOBALS['SafiErrors']['general'][] = "Uno o mas responsables carecen de estado.";
				} else {
					$countItems = count($_POST[$itemRequestName]);
					if($countTiposResponsables != $countItems) {
						$GLOBALS['SafiErrors']['general'][] = "El n&uacute;mero de responsables y de estados no coincide.";
					} else {
						$indexResponsablesLocal = 0;
						foreach($_POST[$itemRequestName] as $item)
						{
							if(trim($item) == null || trim($item) == '' || (strcmp(trim($item), "0")==0)){
								$GLOBALS['SafiErrors']['general'][] = "Debe indicar un estado para el responsable[".
									($indexResponsablesLocal+1)."].";
							} else {
								$estado = new EntidadEstado();
								$estado->SetId(trim($item));
								$responsablesAvancePartidas[$indexResponsablesLocal]->GetResponsableAvance()->SetEstado($estado);
							}
							$indexResponsablesLocal++;
						}
					}
				}
				
				$arrExisteInfo = array();
				$arrMsgErrors = array();
				// Inicialmente se asume que no existe información de cuentas bancarias
				for ($i = 0; $i < $countTiposResponsables; $i++)
				{
					$arrExisteInfo[$i] = false;
					$arrMsgErrors[$i] = array();
				}
				
				// Validar nrosCuentasResponsables
				$itemRequestName = 'nrosCuentasResponsables';
				if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
					$GLOBALS['SafiErrors']['general'][] = "Uno o mas responsables carecen de n&uacute,meros de cuentas.";
				} else {
					$countItems = count($_POST[$itemRequestName]);
					if($countTiposResponsables != $countItems) {
						$GLOBALS['SafiErrors']['general'][] = "La cantidad de responsables y de n&umeros;meros de cuentas no coincide.";
					} else {
						$indexResponsablesLocal = 0;
						foreach($_POST[$itemRequestName] as $item)
						{
							// Validar número de cuenta
							if(trim($item) != ''){
								$arrExisteInfo[$indexResponsablesLocal] = true;
								$numeroCuenta = trim($item);
								$responsablesAvancePartidas[$indexResponsablesLocal]->GetResponsableAvance()->SetNumeroCuenta($numeroCuenta);
								if (mb_strlen($numeroCuenta) == 20) {
									if(!SafiEsCadenaDigitosNumericos($numeroCuenta)){
										$arrMsgErrors[$indexResponsablesLocal][] = "El n&uacute;mero de cuenta bancaria [".($indexResponsablesLocal+1).
											"] debe contener solo caracteres num&eacute;ricos.";
									}
								} else {
									$arrMsgErrors[$indexResponsablesLocal][] = "El n&uacute;mero de cuenta bancaria [".($indexResponsablesLocal+1).
										"] debe tener 20 d&iacute;gitos.";
								}
								
							} else {
								$arrMsgErrors[$indexResponsablesLocal][] = "Debe indicar un n&uacute;mero de cuenta bancaria [".($indexResponsablesLocal+1)."].";
							}
							$indexResponsablesLocal++;
						}
					}
				}
				
				// Validar tiposCuentasResponsables
				$itemRequestName = 'tiposCuentasResponsables';
				if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
					$GLOBALS['SafiErrors']['general'][] = "Uno o mas responsables carecen de tipo de cuenta bancaria.";
				} else {
					$countItems = count($_POST[$itemRequestName]);
					if($countTiposResponsables != $countItems) {
						$GLOBALS['SafiErrors']['general'][] = "La cantidad de responsables y de tipos de cuentas no coincide.";
					} else {
						$indexResponsablesLocal = 0;
						foreach($_POST[$itemRequestName] as $item)
						{
							// Validar tipo de cuenta
							if(trim($item) != '' && strcmp(trim($item), "0") != 0)
							{
								$arrExisteInfo[$indexResponsablesLocal] = true;
								$tipoCuenta = trim($item);
								$responsablesAvancePartidas[$indexResponsablesLocal]->GetResponsableAvance()->SetTipoCuenta($tipoCuenta);
							} else {
								$arrMsgErrors[$indexResponsablesLocal][] = "Debe indicar un tipo de cuenta bancaria [".($indexResponsablesLocal+1)."]";
							}
							$indexResponsablesLocal++;
						}
					}
				}
				
				// Validar bancosResponsables
				$itemRequestName = 'bancosResponsables';
				if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
					$GLOBALS['SafiErrors']['general'][] = "Uno o mas responsables carecen de banco.";
				} else {
					$countItems = count($_POST[$itemRequestName]);
					if($countTiposResponsables != $countItems) {
						$GLOBALS['SafiErrors']['general'][] = "La cantidad de responsables y de bancos no coincide.";
					} else {
						$indexResponsablesLocal = 0;
						foreach($_POST[$itemRequestName] as $item)
						{
							// Validar banco
							if(trim($item) != ''){
								$arrExisteInfo[$indexResponsablesLocal] = true;
								$banco = trim($item);
								$responsablesAvancePartidas[$indexResponsablesLocal]->GetResponsableAvance()->SetBanco($banco);
							} else {
								$arrMsgErrors[$indexResponsablesLocal][] = "Debe indicar un banco para la cuenta bancaria [".($indexResponsablesLocal+1)."]";
							}
							$indexResponsablesLocal++;
						}
					}
				}
				
				$indexResponsablesLocal = 0;
				foreach ($arrExisteInfo as $existeInfo){
					if($existeInfo === true){
						foreach($arrMsgErrors[$indexResponsablesLocal] as $msgError){
							$GLOBALS['SafiErrors']['general'][] = $msgError; 
						}
					}
					$indexResponsablesLocal++;
				}
				
				// Validar correlativosResponsables
				$itemRequestName = 'correlativosResponsables';
				$arrCorrelativos = array();
				if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
					$GLOBALS['SafiErrors']['general'][] = "Uno o mas responsables carecen de correlativos.";
				} else {
					$countItems = count($_POST[$itemRequestName]);
					if($countTiposResponsables != $countItems) {
						$GLOBALS['SafiErrors']['general'][] = "El n&uacute;mero de responsables y de correlativos no coincide.";
					} else {
						$indexResponsablesLocal = 0;
						foreach($_POST[$itemRequestName] as $item)
						{
							if(trim($item) == null || trim($item) == ''){
								$GLOBALS['SafiErrors']['general'][] = "Debe indicar un correlativo para el responsable[".
									($indexResponsablesLocal+1)."].";
							} else {
								$arrCorrelativos[] = trim($item);
							}
							$indexResponsablesLocal++;
						}
					}
				}
				
				$arrExisteInfo = array();
				$arrMsgErrors = array();
				
				// Validar partidas
				$itemRequestName = 'partidas';
				if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
					$GLOBALS['SafiErrors']['general'][] = "Uno o mas responsables carecen de partidas.";
				} else {
					$countItems = count($_POST[$itemRequestName]);
					if($countTiposResponsables != $countItems) {
						$GLOBALS['SafiErrors']['general'][] = "El n&uacute;mero de responsables y de listas de partidas "
							 ."no coincide.";
					} else {
						$indexResponsablesLocal = 0;
						foreach($_POST[$itemRequestName] as $index => $partidas)
						{
							$responsableAvancePartidas = $responsablesAvancePartidas[$indexResponsablesLocal];
							$avancePartidas = array();
							$arrExisteInfo[$indexResponsablesLocal] = array();
							$arrMsgErrors[$indexResponsablesLocal] = array();
							
							if($index != $arrCorrelativos[$indexResponsablesLocal])
							{
								$GLOBALS['SafiErrors']['general'][] = "El indice de la listas de partidas[".($indexResponsablesLocal+1)."]="
								 . $index.", y el correlativo ".$arrCorrelativos[$indexResponsablesLocal]
								 ." no coinciden.";
							} else {
								$indexPartida = 0;
								$idsPartidas = array();
								foreach ($partidas as $idPartida){
									// Se asume que inicialmente no existe información de partidas 
									$arrExisteInfo[$indexResponsablesLocal][$indexPartida] = false;
									$arrMsgErrors[$indexResponsablesLocal][$indexPartida] = array();
									
									$partida = new EntidadPartida();
									
									$avancePartida = new EntidadAvancePartida();
									$avancePartida->SetPartida($partida);
									
									$avancePartidas[] = $avancePartida;
									
									// Validar id de partida
									if(($idPartida=trim($idPartida)) != ''){
										$arrExisteInfo[$indexResponsablesLocal][$indexPartida] = true;
										
										// Validar si existe una partida repetida
										if( count($idsPartidas) > 0 && ($indexPartidaRepetida = array_search($idPartida, $idsPartidas)) !== false ){
											$arrMsgErrors[$indexResponsablesLocal][$indexPartida][] = 'En el responsable['.($indexResponsablesLocal+1).
												'] la partida['.($indexPartidaRepetida+1).'] ('.$idPartida.')' . ' y la partida['.($indexPartida+1).
												'] est&aacute;n repetidas.';
										}
										
										$partida->SetId($idPartida);
										$idsPartidas[] = $idPartida;
										
									} else {
										$arrMsgErrors[$indexResponsablesLocal][$indexPartida][] = 'Debe indicar una partida['
											.($indexPartida+1).']'. ' para el responsable['.($indexResponsablesLocal+1).']';
									}
									
									$indexPartida++;	
								}
								
								if(count($idsPartidas) > 0){
									$partidasValidas = SafiModeloPartida::GetPartidasByIds($idsPartidas, $annoPresupuesto);
									
									if($partidasValidas !== null && is_array($partidasValidas))
									{
										foreach ($avancePartidas as $indexPartida => $avancePartida)
										{
											if($avancePartida != null && $avancePartida->GetPartida() != null
												&& ($idPartida=$avancePartida->GetPartida()->GetId()) != null
												&& ($idPartida=trim($idPartida)) != ''
											){
												if(!array_key_exists($idPartida, $partidasValidas)){
													$arrMsgErrors[$indexResponsablesLocal][$indexPartida][] = 'La partida['
														.($indexPartida+1).'] ('.$idPartida.')'. ' para el responsable['
														.($indexResponsablesLocal+1).']'.' es inv&aacute;lida.';
												}
											}	
										}
									}
								}
								$responsableAvancePartidas->SetAvancePartidas($avancePartidas);
							}
							$indexResponsablesLocal++;
						}
					}
				}
				
				// Validar montos de partidas (partidasMontos)
				$itemRequestName = 'partidasMontos';
				if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
					$GLOBALS['SafiErrors']['general'][] = "Uno o mas responsables carecen de montos de partidas.";
				} else {
					$countItems = count($_POST[$itemRequestName]);
					if($countTiposResponsables != $countItems) {
						$GLOBALS['SafiErrors']['general'][] = "El n&uacute;mero de responsables y de listas de montos de partidas "
							 ."no coincide.";
					} else {
						$indexResponsablesLocal = 0;
						foreach($_POST[$itemRequestName] as $index => $partidasMontos)
						{
							$responsableAvancePartidas = $responsablesAvancePartidas[$indexResponsablesLocal];
							$avancePartidas = $responsableAvancePartidas->GetAvancePartidas();
							
							if($index != $arrCorrelativos[$indexResponsablesLocal])
							{
								$GLOBALS['SafiErrors']['general'][] = "El indice de la listas de montos de partidas["
									.$indexResponsablesLocal."]=" . $index.", y el correlativo ".$arrCorrelativos[$indexResponsablesLocal]
									." no coinciden.";
							} else {
								if(count($partidasMontos) != count($avancePartidas)){
									$GLOBALS['SafiErrors']['general'][] = "El n&uacute;mero de partidas[".$indexResponsablesLocal
										."] y el n&uacute;mero de montos de partidas[".$indexResponsablesLocal."] no coinciden"
										."count($partidasMontos): " . count($partidasMontos) 
										. ", count($avancePartidas): " . count($avancePartidas).".";
								}
								else {
									$indexPartidaMonto = 0;
									foreach ($partidasMontos as $partidaMonto)
									{
										// Validar el monto de la partida
										if(trim($partidaMonto) != ''){
											$arrExisteInfo[$indexResponsablesLocal][$indexPartidaMonto] = true;
											
											$avancePartida = $avancePartidas[$indexPartidaMonto];
											$avancePartida->SetMonto(trim($partidaMonto));
											
										} else {
											$arrMsgErrors[$indexResponsablesLocal][$indexPartidaMonto][] = "Debe indicar un monto de partida["
											.($indexPartidaMonto+1)."]". " para el responsable[".($indexResponsablesLocal+1)."].";
										}
										$indexPartidaMonto++;
									}
								}
							}
							$indexResponsablesLocal++;
						}
					}
				}

				// Mostrar los mensajes de error para las partidas y los montos si son necesarios
				$indexResponsablesLocal = 0;
				foreach ($arrExisteInfo as $subExisteInfo)
				{
					$indexPartida = 0;
					foreach ($subExisteInfo as $existeInfo)
					{
						if($existeInfo === true)
						{
							foreach($arrMsgErrors[$indexResponsablesLocal][$indexPartida] as $msgError){
								$GLOBALS['SafiErrors']['general'][] = $msgError; 
							}
						}
						$indexPartida++;
					}
					$indexResponsablesLocal++;
				}
				
				$form->GetAvance()->SetResponsablesAvancePartidas($responsablesAvancePartidas);
			}
		}
		
	}
	
	// Obtener y validar los rutas del avance
	private function __ValidarRutasAvance(NuevoAvanceForm $form)
	{
		$rutasAvance = array();
		
		// Validar las rutas
		if(!isset($_POST['idRutasAvance']) || !is_array($_POST['idRutasAvance'])){
			$GLOBALS['SafiErrors']['general'][] = 'Debe indicar al menos una ruta del avance. Identificador de ruta no encontrado.';
		} else {
			$countIdRutasAvance = count($_POST['idRutasAvance']);
			if($countIdRutasAvance > 0)
			{
				$indexRutasLocal = 0;
				// Crear las entidades rutas del avance
				foreach($_POST['idRutasAvance'] as $idRutaAvance)
				{
					$rutaAvance = new EntidadRutaAvance();
					
					// validar el id de las rutas del avance
					if(trim($idRutaAvance) == '' || strcmp($idRutaAvance, "0")==0){
						$rutaAvance->SetId("0");
					} else {
						if(!SafiIsId($idRutaAvance)){
							$GLOBALS['SafiErrors']['general'][] = 'El id de rutas['.($indexRutasLocal+1).'] debe ser num&eacute;rico.';
						} else {
							$rutaAvance->SetId($idRutaAvance);
						}
					}
					$indexRutasLocal++;
					
					$rutasAvance[] = $rutaAvance;
				}
				
				// Validar Estados
				$this->ValidarSelectDireccion(array(
					'rutas' => &$rutasAvance,
					'itemRequestName' => 'estados',
					'itemObjectName' => 'estado',
					'itemClassName' => 'EntidadEstado',
					'countRutas' => $countIdRutasAvance,
					'esObligatorio' => true,
					'msjItemsVacios' => 'Una o mas rutas carecen de estado',
					'msjItemsContadorDistinto' => 'El n&uacute;mero de rutas y estados no coincide',
					'msjItemsNoNumericos' => 'El campo estado de rutas[%u] debe ser num&eacute;rico',
					'msjEsobligatorio' => 'Debe indicar un estado para las rutas[%u]'
				));
				
				// Validar Ciudades
				$this->ValidarSelectDireccion(array(
					'rutas' => &$rutasAvance,
					'itemRequestName' => 'ciudades',
					'itemObjectName' => 'ciudad',
					'itemClassName' => 'EntidadCiudad',
					'countRutas' => $countIdRutasAvance,
					'msjItemsVacios' => 'Una o mas rutas carecen de ciudad',
					'msjItemsContadorDistinto' => 'El n&uacute;mero de rutas y ciudades no coincide',
					'msjItemsNoNumericos' => 'El campo ciudad de rutas[%u] debe ser num&eacute;rico'
				));
				
				// Validar Municipios
				$this->ValidarSelectDireccion(array(
					'rutas' => &$rutasAvance,
					'itemRequestName' => 'municipios',
					'itemObjectName' => 'municipio',
					'itemClassName' => 'EntidadMunicipio',
					'countRutas' => $countIdRutasAvance,
					'msjItemsVacios' => 'Una o mas rutas carecen de municipo',
					'msjItemsContadorDistinto' => 'El n&uacute;mero de rutas y municipios no coincide',
					'msjItemsNoNumericos' => 'El campo municipio de rutas[%u] debe ser num&eacute;rico',
				));
				
				// Validar Parroquias
				$this->ValidarSelectDireccion(array(
					'rutas' => &$rutasAvance,
					'itemRequestName' => 'parroquias',
					'itemObjectName' => 'parroquia',
					'itemClassName' => 'EntidadParroquia',
					'countRutas' => $countIdRutasAvance,
					'msjItemsVacios' => 'Una o mas rutas carecen de parroquia',
					'msjItemsContadorDistinto' => 'El n&uacute;mero de rutas y parroquia no coincide',
					'msjItemsNoNumericos' => 'El campo parroquia de rutas[%u] debe ser num&eacute;rico',
				));
				
				// Validar dirección
				$itemRequestName = 'direcciones';
				$itemArrayName = $itemRequestName; 
				if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
					$GLOBALS['SafiErrors']['general'][] = 'Una o mas rutas carecen de direcci&oacute;n.';
				} else {
					$countItems = count($_POST[$itemRequestName]);
					if($countIdRutasAvance != $countItems) {
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de rutas y de direcci&oacute;n no coincide.';
					} else {
						$indexRutasLocal = 0;
						foreach($_POST[$itemRequestName] as $item){
							if(trim($item) != '')
							{
								$rutasAvance[$indexRutasLocal]->SetDireccion(trim($item));
							}
							$indexRutasLocal++;
						}
					}
				}
				
				$form->GetAvance()->SetRutasAvance($rutasAvance);
				
			}
		}
	}
	
	// Obtener y validar las observaciones del avance
	private function __ValidarObservaciones(NuevoAvanceForm $form)
	{
		if(isset($_POST['observaciones']) && trim($_POST['observaciones']) != ''){
			$form->GetAvance()->SetObservaciones(trim($_POST['observaciones']));
		}
	}
	
	// Obtener y validar el id del punto de cuenta asociado al avance
	private function __ValidarIdPuntoCuenta(NuevoAvanceForm $form)
	{
		$idDependencia = $_SESSION['user_depe_id'];
		
		if(isset($_POST['idPuntoCuenta']) && ($idPuntoCuenta=trim($_POST['idPuntoCuenta'])) != '' && strcasecmp($idPuntoCuenta, "pcta-"))
		{
			if(SafiModeloPuntoCuenta::PerteneceALaDependenciaElPuntoCuenta($idPuntoCuenta, $idDependencia))
			{
				$puntoCuenta = new EntidadPuntoCuenta();
				$puntoCuenta->SetId(mb_strtolower($idPuntoCuenta));
				$form->GetAvance()->SetPuntoCuenta($puntoCuenta);
			} else {
				$puntoCuenta = new EntidadPuntoCuenta();
				$puntoCuenta->SetId(mb_strtolower($idPuntoCuenta));
				$form->GetAvance()->SetPuntoCuenta($puntoCuenta);
				
				$GLOBALS['SafiErrors']['general'][] = 'El punto de cuenta: "'.$idPuntoCuenta
					.'" no existe o pertenece a otra dependencia.';
			}
		}
		
		//$GLOBALS['SafiErrors']['general'][] = 'Debe indicar al menos una ruta del avance. Identificador de ruta no encontrado.';
	}
	
	private function ValidarSelectDireccion($params)
	{
		$rutas = &$params['rutas'];
		$itemRequestName = $params['itemRequestName'];
		$itemObjectName =  $params['itemObjectName'];
		$itemClassName = $params['itemClassName'];
		$countRutas = $params['countRutas'];
		$msjItemsVacios = $params['msjItemsVacios'];
		$msjItemsContadorDistinto = $params['msjItemsContadorDistinto'];
		$msjItemsNoNumericos = $params['msjItemsNoNumericos'];
		$esObligatorio = isset($params['esObligatorio']) ? $params['esObligatorio'] : false;
		$esObligatorio = ($esObligatorio === true || $esObligatorio === true) ? $esObligatorio : false;
		if($esObligatorio){
			$msjEsObligatorio = (isset($params['msjEsobligatorio']) ? $params['msjEsobligatorio'] : 'Campo de ruta obligatorio');
		}
		
		$itemSetter = 'Set' . ucfirst($itemObjectName);
		
		if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
			$GLOBALS['SafiErrors']['general'][] = $msjItemsVacios;
		} else {
			$countItems = count($_POST[$itemRequestName]);
			if($countRutas != $countItems) {
				$GLOBALS['SafiErrors']['general'][] = $msjItemsContadorDistinto;
			} else {
				$indexRutasLocal = 0;
				foreach($_POST[$itemRequestName] as $item){
					$continuarValidando = true;
					if($esObligatorio){
						if(trim($item) == null || trim($item) == '' || (strcmp(trim($item), "0")==0)){
							$GLOBALS['SafiErrors']['general'][] = sprintf($msjEsObligatorio, ($indexRutasLocal+1));
							$continuarValidando = false;
						}
					} else{
						if(trim($item) == ''){
							$item = 0;
						}
					}
					
					if($continuarValidando){
						if(!SafiIsInt($item)){
							$GLOBALS['SafiErrors']['general'][] = sprintf($msjItemsNoNumericos, ($indexRutasLocal+1));
						} else {
							$entidad = new $itemClassName();
							$entidad->SetId($item);
							$rutas[$indexRutasLocal]->$itemSetter($entidad);
						}
					}
					
					$indexRutasLocal++;
				}
			}
		}
	}
}

new AvanceAccion();
?>