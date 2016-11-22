<?php

require_once(dirname(__FILE__) . '/../../init.php');

// Acciones
require_once(SAFI_ACCIONES_PATH. '/acciones.php');

// Includes
require_once(SAFI_INCLUDE_PATH. '/perfiles/constantesPerfiles.php');

// Modelo
require_once(SAFI_MODELO_PATH. '/avance.php');
require_once(SAFI_MODELO_PATH. '/banco.php');
require_once(SAFI_MODELO_PATH. '/beneficiarioviatico.php');
require_once(SAFI_MODELO_PATH. '/cargo.php');
require_once(SAFI_MODELO_PATH. '/compromiso.php');
require_once(SAFI_MODELO_PATH. '/dependencia.php');
require_once(SAFI_MODELO_PATH. '/estatus.php');
require_once(SAFI_MODELO_PATH. '/general.php');
require_once(SAFI_MODELO_PATH. '/empleado.php');
require_once(SAFI_MODELO_PATH. '/estado.php');
require_once(SAFI_MODELO_PATH. '/firma.php');
require_once(SAFI_MODELO_PATH. '/infocentro.php');
require_once(SAFI_MODELO_PATH. '/partida.php');
require_once(SAFI_MODELO_PATH. '/rendicionAvance.php');
require_once(SAFI_MODELO_PATH. '/wfgrupo.php');

if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}

class RendicionAvanceAccion extends Acciones
{
	public function Bandeja()
	{
		$idPerfil = $_SESSION['user_perfil_id'];
		$idDependencia = $_SESSION['user_depe_id'];
		$login = $_SESSION['login'];
		
		$estatusDevuelto = 7;
		$estatusEntransito = 10;
		
		/**********************************
		*        Bandeja principal        *
		***********************************/
		
		$enBandeja = null;
		if(
			substr($idPerfil,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
			$idPerfil == PERFIL_ASISTENTE_EJECUTIVO ||
			$idPerfil == PERFIL_ASISTENTE_PRESIDENCIA
		){
			$enBandeja = SafiModeloRendicionAvance::GetRendicionAvanceEnBandeja(array(
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
			$enBandeja = SafiModeloRendicionAvance::GetRendicionAvanceEnBandeja(array(
				'idPerfilActual' => $idPerfil,
				'estatus' => array($estatusEntransito),
				'idDependencia' => $idDependencia 
			));			
		} else if(
			$idPerfil == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS
		){
			$enBandeja = SafiModeloRendicionAvance::GetRendicionAvanceEnBandeja(array(
				'idPerfilActual' => $idPerfil,
				'estatus' => array($estatusEntransito) 
			));
		} else if(
			$idPerfil == PERFIL_JEFE_PRESUPUESTO ||
			$idPerfil == PERFIL_ANALISTA_PRESUPUESTO
		){
			$enBandeja = SafiModeloRendicionAvance::GetRendicionAvanceEnBandeja(array(
				'idPerfilActual' => PERFIL_JEFE_PRESUPUESTO,
				'estatus' => array($estatusEntransito) 
			));
		}
		
		if(is_array($enBandeja) && count($enBandeja)>0)
		{
			$usuaLogins = array();
			
			foreach ($enBandeja AS $dataRendicion){
				$docGenera = $dataRendicion['ClassDocGenera'];
	
				/* para obtener los datos de el usuario que eleboró la rendición */
				$usuaLogins[] = $docGenera->GetUsuaLogin();
			}
			
			if(count($usuaLogins)>0){
				$usuaLogins = array_unique($usuaLogins);
				$empleadosElaboradoresEnBandejas = SafiModeloEmpleado::GetEmpleadosByUsuaLogins($usuaLogins);
				$GLOBALS['SafiRequestVars']['empleadosElaboradoresEnBandejas'] = $empleadosElaboradoresEnBandejas;
			}
		}
		
		$GLOBALS['SafiRequestVars']['rendicionAvancesEnBandeja'] = $enBandeja;
		
		/**********************************
		*    Fin de Bandeja principal     *
		***********************************/
		
		
		if(
			substr($idPerfil,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
			$idPerfil == PERFIL_ASISTENTE_EJECUTIVO ||
			$idPerfil == PERFIL_ASISTENTE_PRESIDENCIA
		){
			/**********************************
			 ******* Bandeja por enviar *******
			 **********************************/

			$porEnviar = SafiModeloRendicionAvance::GetRendicionAvancePorEnviar(array(
				"usuaLogin" => $login,
				"idPerfilActual" => $idPerfil
			));
			
			$GLOBALS['SafiRequestVars']['rendicionAvancesPorEnviar'] = $porEnviar;
			
			/**********************************
			 *    Fin de Bandeja por enviar   *
			 **********************************/
			
		}
		
		/***********************************
		 *       Bandeja en tránsito       *
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
			
			// Establecer la bandeja de rendición de avances en transito
			$enTransito = SafiModeloRendicionAvance::GetRendicionAvanceEnTransito($params);
			
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
				$GLOBALS['SafiRequestVars']['rendicionAvancesCargoFundacionEnTransitos'] = $cargoFundacionEnTransitos;
			}
			
			if(count($idDependencias)>0){
				$dependenciaEnTransitos = SafiModeloDependencia::GetDependenciaByIds($idDependencias);
				$GLOBALS['SafiRequestVars']['rendicionAvancesDependenciaEnTransitos'] = $dependenciaEnTransitos;
			}
			
			if(count($usuaLogins)>0){
				$usuaLogins = array_unique($usuaLogins);
				$empleadosElaboradoresEnTransitos = SafiModeloEmpleado::GetEmpleadosByUsuaLogins($usuaLogins);
				$GLOBALS['SafiRequestVars']['rendicionAvancesEmpleadosElaboradoresEnTransitos'] = $empleadosElaboradoresEnTransitos;
			}
			
			$GLOBALS['SafiRequestVars']['rendicionAvancesEnTransito'] = $enTransito;
			
			/***********************************
			 *    Fin de Bandeja en transito   *
			 ***********************************/
		}
		
		
		require(SAFI_VISTA_PATH ."/avan/bandejaRendicionAvance.php");
	}
	
	public function BuscarAvance()
	{
		$form = FormManager::GetForm(FORM_NUEVA_RENDICION_AVANCE);
		$form->SetTipoOperacion(NuevaRendicionAvanceForm::TIPO_OPERACION_INSERTAR);
		
		$idDependencia = $_SESSION['user_depe_id'];

		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		
		try
		{
			if(!isset($_POST['idAvanceBuscado']) || ($idAvanceBuscado=trim($_POST['idAvanceBuscado'])) == '')
				throw new Exception("Se debe indicar un c&oacute;digo de avance.");
				
			if (
				strlen($idAvanceBuscado) <= 5 || $idAvanceBuscado == GetConfig("preCodigoAvance").GetConfig("delimitadorPreCodigoDocumento")
			)
				throw new Exception("El c&oacute;digo de avance \"".$idAvanceBuscado."\" es incorrecto.");
			
			$form->SetIdAvanceBuscado($idAvanceBuscado);
			
			$findAvance = new EntidadAvance();
			$findAvance->SetId($form->GetIdAvanceBuscado());
			
			$avance = SafiModeloAvance::GetAvance($findAvance);
			
			if($avance == null)
				throw new Exception("El avance " . $form->GetIdAvanceBuscado() . " es incorrecto.");
				
			if(
					($dependenciaAvance = $avance->GetDependencia()) == null
					|| ($idDependenciaAvance=$dependenciaAvance->GetId()) == null
					|| ($idDependenciaAvance=trim($idDependenciaAvance)) == ''
			)
				throw new Exception("No se puede establecer la dependencia del avance  \"" . $form->GetIdAvanceBuscado() . "\".");
			
			if($idDependenciaAvance != $idDependencia)
				throw new Exception("El avance \"" . $form->GetIdAvanceBuscado() . "\" no pertenece a su dependencia.");
				
			$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($avance->GetId());
			if($docGenera == null)
				throw new Exception("El documento del avance \"" . $form->GetIdAvanceBuscado() . "\" no pudo ser cargado.");
			
			if($docGenera->GetIdEstatus() == EntidadEstatus::ESTATUS_ANULADO
				&& $docGenera->GetIdWFObjeto() == EntidadWFObjeto::WFOBJETO_RECHAZADO
			)
				throw new Exception("El avance \"" . $form->GetIdAvanceBuscado() . "\" est&aacute; anulado.");
			else if($docGenera->GetIdEstatus() != EntidadEstatus::ESTATUS_APROBADO
				&& $docGenera->GetIdWFObjeto() != EntidadWFObjeto::WFOBJETO_APROBADO
			)
				throw new Exception("El avance \"" . $form->GetIdAvanceBuscado() . "\" aun no ha finalizado.");
			
			// Establecer el avance al formulario
			$form->SetAvance($avance);
			
			$responsablesDisponibles = $this->__CargarResponsablesDisponibles($avance, null, true);
			
			if(!is_array($responsablesDisponibles) || count($responsablesDisponibles) == 0)
				throw new Exception("Todos los responsables del avance seleccionado ya est&aacute;n incluidos en una o m&aacute;s ".
					"rendiciones. Por favor verifique todas las rendiciones de dicho avance.");
			
			// Crear un nuevo objeto de rendicionAvance y guardar la referencia en el formulario
			$rendicion = new EntidadRendicionAvance();
			$form->SetRendicionAvance($rendicion);
			
			$rendicion->SetFechaInicioActividad($avance->GetFechaInicioActividad());
			$rendicion->SetFechaFinActividad($avance->GetFechaFinActividad());
			// $rendicion->SetObjetivos($avance->GetObjetivos());
			// $rendicion->SetDescripcion($avance->GetDescripcion());
			$rendicion->SetNroParticipantes($avance->GetNroParticipantes());
			
			$this->__DesplegarFormularioRendicionAvance();
		}
		catch (Exception $e)
		{
			if($form != null) $form->SetAvance(null);
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
			$this->Insertar();
		}
	}
	
	public function Insertar()
	{
		$form = FormManager::GetForm(FORM_NUEVA_RENDICION_AVANCE);
		$form->SetTipoOperacion(NuevaRendicionAvanceForm::TIPO_OPERACION_INSERTAR);
		
		$this->__DesplegarFormularioRendicionAvance();
	}
	
	public function Guardar()
	{
		$form = FormManager::GetForm(FORM_NUEVA_RENDICION_AVANCE);
		
		$idRendicion = $this->__Guardar();
		
		if($idRendicion !== false){
			$GLOBALS['SafiInfo']['general'][] = "Rendici&oacute;n de avance \"" . $idRendicion . "\" registrada satisfactoriamente.";
			$this->__VerDetalles(array('idRendicion' => $idRendicion));
		} else {
			$this->__DesplegarFormularioRendicionAvance();
		}
	}
	
	public function GuardarYEnviar()
	{
		$form = FormManager::GetForm(FORM_NUEVA_RENDICION_AVANCE);
		
		$idRendicion = $this->__Guardar();
		
		if($idRendicion !== false){
			if($this->__Enviar($idRendicion) !== false){
				$GLOBALS['SafiInfo']['general'][] = 'Rendici&oacute;n de avance "'.$idRendicion.'"
					registrada y enviada satisfactoriamente.';
			} else {
				$GLOBALS['SafiInfo']['general'][] = 'La rendici&oacute;n de avance "'.$idRendicion.'" fue registrada satisfactoriamente, '.
					'pero no se pudo enviar. Intente enviarla m&aacute;s tarde.';
			}
			$this->__VerDetalles(array('idRendicion' => $idRendicion));
		} else {
			$this->__DesplegarFormularioRendicionAvance();
		}
	}
	
	private function __Guardar()
	{
		$form = FormManager::GetForm(FORM_NUEVA_RENDICION_AVANCE);
		
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		
		$fecha = date("d/m/Y H:i:s");
		$idDependencia = $_SESSION['user_depe_id'];
		$loginRegistrador = $_SESSION['login'];
		$annoPresupuesto = $_SESSION['an_o_presupuesto'];
		$perfilRegistrador = $_SESSION['user_perfil_id'];
		
		/* Parche para que la rendición de viáticos salga en el año anterior */
		/*$fecha = "30/12/2014". date(" H:i:s");
		$annoPresupuesto = 2014;*/
		
		if($form->GetRendicionAvance() == null) $form->SetRendicionAvance(new EntidadRendicionAvance());
		
		$rendicionAvance = $form->GetRendicionAvance();
			
		$rendicionAvance->SetFechaRegistro($fecha);
		$rendicionAvance->SetFechaRendicion($fecha);
		
		// Obtener y validar el id del avance
		$this->__ValidarIdAvance($form);
		// Obtener y validar la fecha de la rendición
		//$this->__ValidarFechaRendicion($form);
		// Obtener y validar la fecha de inicio de la actividad
		$this->__ValidarFechaInicioActividad($form);
		// Obtener y validar la fecha de fin de la actividad
		$this->__ValidarFechaFinActividad($form);
		// Obtener y validar los objetivos
		$this->__ValidarObjetivos($form);
		// Obtener y validar la descripción
		$this->__ValidarDescripcion($form);
		// Obtener y validar la nroParticipantes
		$this->__ValidarNroParticipantes($form);
		// Obtener y validar los responsables de la rendición de avance y sus partidas
		$this->__ValidarResponsablesRendicionAvancePartidas($form, $annoPresupuesto);
		// Obtener y validar las observaciones del avance
		$this->__ValidarObservaciones($form);
		
		if(count($GLOBALS['SafiErrors']['general']) == 0)
		{
			$dependencia = new EntidadDependencia();
			$dependencia->SetId($idDependencia);
			
			$rendicionAvance->SetDependencia($dependencia);
			$rendicionAvance->SetUsuaLogin($loginRegistrador);
			
			$rendicionAvance->SetAvance($form->GetAvance());
			$idRendicion = SafiModeloRendicionAvance::GuardarRendicionAvance(
				$rendicionAvance, array('perfilRegistrador' => $perfilRegistrador));
			
			if($idRendicion === false){
				$GLOBALS['SafiErrors']['general'][] = 'La rendici&oacute;n de avance no pudo ser registrada. Intente m&aacute;s tarde.';
			}
			
			return $idRendicion;
		}
		
		return false;
	}	
	
	private function  __CargarResponsablesDisponibles(
		EntidadAvance $avance = null, EntidadRendicionAvance $rendicionAvance = null, $forzar = false
	){
		$responsablesDisponibles = null;
		
		if(!$forzar && isset($GLOBALS['SafiRequestVars']['responsablesDisponibles'])){
			$responsablesDisponibles = $GLOBALS['SafiRequestVars']['responsablesDisponibles'];
		} else if($forzar || (!isset($GLOBALS['SafiRequestVars']['responsablesDisponibles']) && $avance != null))
		{
			$responsablesRendicionAvancePartidas = 
				$rendicionAvance != null ? $rendicionAvance->GetResponsablesRendicionAvancePartidas() : null;
			
			$responsablesDisponibles = SafiModeloRendicionAvance::GetResponsablesDisponiblesRendicionAvance(
				$avance, $responsablesRendicionAvancePartidas);
		}
		
		if ($forzar || !isset($GLOBALS['SafiRequestVars']['responsablesDisponibles']))
			$GLOBALS['SafiRequestVars']['responsablesDisponibles'] = $responsablesDisponibles;
			
		return $responsablesDisponibles;
	}
	
	private function __DesplegarFormularioRendicionAvance()
	{
		$form = FormManager::GetForm(FORM_NUEVA_RENDICION_AVANCE);
		
		$avance = $form->GetAvance(); // Obtener una referencia del avance en el form
		$rendicionAvance = $form->GetRendicionAvance(); // Obtener una referencia de la  rendición de avance en el form
		
		if($avance != null){
			$listaTodosResponsables = SafiModeloRendicionAvance::
				ResponsablesAvancePartidasToResponsablesRendicionAvancePartidas($avance->GetResponsablesAvancePartidas());
		}
		
		// Estados del país
		$estados = SafiModeloEstado::GetAllEstados2();
		
		// Obtener los bancos activos de la fundación
		$bancos = SafiModeloBanco::GetAllBancosActivos();

		$this->__CargarResponsablesDisponibles($avance, $rendicionAvance, false);
		
		$GLOBALS['SafiRequestVars']['estados'] =  $estados;
		$GLOBALS['SafiRequestVars']['listaResponsables'] = $listaTodosResponsables;
		$GLOBALS['SafiRequestVars']['bancos'] =  $bancos;

		require(SAFI_VISTA_PATH . "/avan/nuevaRendicion.php");
	}
	
	public function Modificar()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		
		try
		{
			$form = FormManager::GetForm(FORM_NUEVA_RENDICION_AVANCE);
			$form->SetTipoOperacion(NuevaRendicionAvanceForm::TIPO_OPERACION_MODIFICAR);
			
			if(!isset($_REQUEST['idRendicion']) || ($idRendicion=trim($_REQUEST['idRendicion'])) == '')
				throw new Exception("No se encontr&oacute; ning&uacute;n identificador de la rendici&oacute;n de avance.");
				
			if (
				strlen($idRendicion) <= 5
				|| $idRendicion == GetConfig("preCodigoRendicionAvance").GetConfig("delimitadorPreCodigoDocumento")
			)
				throw new Exception("El c&oacute;digo de la rendici&oacute;n de avance \"".$idRendicion."\" es incorrecto.");
				
			// Obtener la rendición de avance
			$findRendicion = new EntidadRendicionAvance();
			$findRendicion->SetId($idRendicion);
			if(($rendicion = SafiModeloRendicionAvance::GetRendicionAvance($findRendicion)) == null)
				throw new Exception("La rendici&oacute;n de avance \"" . $idRendicion . "\" no pudo ser encontrado.");
				
			if($rendicion->GetAvance() == null)
				throw new Exception("El avance asociado a la rendici&oacute;n es nulo.");
				
			if(($idAvance = $rendicion->GetAvance()->GetId()) == null)
				throw new Exception("El id del avance asociado a la rendici&oacute;n es nulo.");
				
			if(($idAvance=trim($idAvance)) == '')
				throw new Exception("El id del avance asociado a la rendici&oacute;n está vacío.");
				
			// Obtener el avance asociado a la rendición de avance
			$findAvance = new EntidadAvance();
			$findAvance->SetId($idAvance);
			$avance = SafiModeloAvance::GetAvance($findAvance);
			
			// Establecer los datos del formulario
			$form->SetRendicionAvance($rendicion);
			$form->SetAvance($avance);
			
			$this->__DesplegarFormularioRendicionAvance();
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = utf8_decode($e->getMessage());
			$this->Bandeja();
		}
	}
	
	public function Actualizar()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		
		$annoPresupuesto = $_SESSION['an_o_presupuesto'];
		
		try
		{
			$form = FormManager::GetForm(FORM_NUEVA_RENDICION_AVANCE);
			$form->SetTipoOperacion(NuevaRendicionAvanceForm::TIPO_OPERACION_MODIFICAR);
			
			if(!isset($_REQUEST['idRendicion']) || ($idRendicion=trim($_REQUEST['idRendicion'])) == '')
				throw new Exception("No se encontr&oacute; ning&uacute;n identificador de la rendici&oacute;n de avance.");
				
			if (
				strlen($idRendicion) <= 5
				|| $idRendicion == GetConfig("preCodigoRendicionAvance").GetConfig("delimitadorPreCodigoDocumento")
			)
				throw new Exception("El c&oacute;digo de la rendici&oacute;n de avance \"".$idRendicion."\" es incorrecto.");
			
			$findRendicion = new EntidadRendicionAvance();
			$findRendicion->SetId($idRendicion);
			
			if(($rendicion = SafiModeloRendicionAvance::GetRendicionAvance($findRendicion)) == null)
				$GLOBALS['SafiErrors']['general'][] = "La rendici&oacute;n de avance \"".$idRendicion."\" no pudo ser cargada.";
			
			if($rendicion->GetAvance() == null)
				throw new Exception("El avance asociado a la rendici&oacute;n \"".$idRendicion."\" no pudo ser encontrado.");
				
			if(($idAvance = $rendicion->GetAvance()->GetId()) == null)
				throw new Exception("El id del avance asociado a la rendici&oacuten \"".$idRendicion."\" no pudo ser encontrado.");
			
			$findAvance = new EntidadAvance();
			$findAvance->SetId(trim($idAvance));
			if(($avance = SafiModeloAvance::GetAvance($findAvance)) == null)
				throw new Exception("El avance asociado a la rendici&oacuten \"".$idRendicion."\" no pudo ser cargado.");

			// Establecer los datos del formulario
			//if($form->GetRendicionAvance() == null) $form->SetRendicionAvance(new EntidadRendicionAvance());
			$form->SetRendicionAvance($rendicion);
			$form->SetAvance($avance);
			
			// Obtener y validar la fecha de la rendición
			//$this->__ValidarFechaRendicion($form);
			// Obtener y validar la fecha de inicio de la actividad
			$this->__ValidarFechaInicioActividad($form);
			// Obtener y validar la fecha de fin de la actividad
			$this->__ValidarFechaFinActividad($form);
			// Obtener y validar los objetivos
			$this->__ValidarObjetivos($form);
			// Obtener y validar la descripción
			$this->__ValidarDescripcion($form);
			// Obtener y validar la nroParticipantes
			$this->__ValidarNroParticipantes($form);
			// Obtener y validar los responsables de la rendición de avance y sus partidas
			$this->__ValidarResponsablesRendicionAvancePartidas($form, $annoPresupuesto);
			// Obtener y validar las observaciones del avance
			$this->__ValidarObservaciones($form);
			
			if(count($GLOBALS['SafiErrors']['general']) == 0)
			{
				if(SafiModeloRendicionAvance::ActualizarRendicionAvance($rendicion) !== false)
				{
					$GLOBALS['SafiInfo']['general'][] = 
						utf8_decode('Rendici&oacute;n de avance "' . $rendicion->GetId() . '" modificada satisfactoriamente.');
					$this->__VerDetalles(array("idRendicion" => $rendicion->GetId()));
					return;
				}
				else
				{
					$GLOBALS['SafiErrors']['general'][] = utf8_decode('La rendici&oacute;n de avance "'
						.$rendicion->GetId() . '" no pudo ser modificada. Póngase en contacto con el administrador del sistema.');
				}
			}
			
			$this->__DesplegarFormularioRendicionAvance();
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = utf8_decode($e->getMessage());
			$this->Bandeja();
		}
	}
	
	public function ActualizarYEnviar()
	{
		echo "Actualizando y enviando";
	}
	
	public function BuscarRendicion()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
			
		try
		{
			$form = FormManager::GetForm(FORM_BUSCAR_RENDICION_AVANCE);
			$existCriteria = false;
			$idRendicion = null;
			$idAvance = null;
			
			$idDependencia = $_SESSION['user_depe_id'];
			$idUserPerfil = $_SESSION['user_perfil_id'];
			
			// Validar la fecha de inicio
			if(isset($_POST['fechaInicio']) && trim($_POST['fechaInicio']) != '')
			{
				$fecha = explode('/', trim($_POST['fechaInicio']));
				
				if (count($fecha) != 3)
					throw new Exception('Fecha de inicio inv&aacute;lida.');
				
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				
				if(!checkdate ($month ,$day ,$year))
					throw new Exception('Fecha de inicio inv&aacute;lida.');
				
				$form->SetFechaInicio($day . '/' . $month . '/' . $year);
				
				$existCriteria = true;
			}
			
			// Validar la fecha de fin
			if(isset($_POST['fechaFin']) && trim($_POST['fechaFin']) != '')
			{
				$fecha = explode('/', trim($_POST['fechaFin']));
				
				if (count($fecha) != 3)
					throw new Exception('Fecha de fin inv&aacute;lida.');
				
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				
				if(!checkdate ($month ,$day ,$year))
					throw new Exception('Fecha de fin inv&aacute;lida.');
				
				$form->SetFechaFin($day . '/' . $month . '/' . $year);
				
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
			
			// Validar el id de avance
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
				if($idRendicion != null) $params['idRendicion'] = $idRendicion;
				if($idAvance != null) $params['idAvance'] = $idAvance;
				$params['dependencia'] = $dependencia;
				
				$dataRendicionAvances = SafiModeloRendicionAvance::BuscarRendicionAvance($params);
				$form->SetDataRendicionAvances($dataRendicionAvances);
				
				if($dataRendicionAvances != null)
				{
					$cargoFundaciones = array();
					$idDependencias = array();
					$usuaLogins = array();
				
					foreach($dataRendicionAvances as $dataRendicionAvance)
					{
						$docGenera = $dataRendicionAvance['ClassDocGenera'];
						
						/* Para obtener los datos de la instancia actual */
						$idPerfilActual = $docGenera->GetIdPerfilActual();
						if($idPerfilActual != null && $idPerfilActual != '')
						{
							$cargoFundacion = GetCargoFundacionFromIdPerfil($idPerfilActual);
							$cargoFundaciones[$cargoFundacion] = $cargoFundacion;
							
							$idDependencia = GetIdDependenciaFromIdPerfil($idPerfilActual);
							$idDependencias[$idDependencia] = $idDependencia; 
						}
						
						/* para obtener los datos de el usuario que eleboró la rendición de avance*/
						$usuaLogins[] = $docGenera->GetUsuaLogin();
					}
					
					if(count($cargoFundaciones)>=0){
						$cargoFundacionInstanciaActuales = SafiModeloCargo::GetCargoByCargoFundaciones($cargoFundaciones);
						$GLOBALS['SafiRequestVars']['rendicionAvanceCargoFundacionInstanciaActuales']
							= $cargoFundacionInstanciaActuales;
					}
					
					if(count($idDependencias)>0){
						$dependenciaInstanciaActuales = SafiModeloDependencia::GetDependenciaByIds($idDependencias);
						$GLOBALS['SafiRequestVars']['rendicionAvanceDependenciaInstanciaActuales'] = $dependenciaInstanciaActuales;
					}
					
					if(count($usuaLogins)>0){
						$usuaLogins = array_unique($usuaLogins);
						$empleadosElaboradores = SafiModeloEmpleado::GetEmpleadosByUsuaLogins($usuaLogins);
						$GLOBALS['SafiRequestVars']['rendicionAvanceEmpleadosElaboradores'] = $empleadosElaboradores;
					}
					
					$estatusList = SafiModeloEstatus::GetAllEstatus();
					$GLOBALS['SafiRequestVars']['estatusList'] = $estatusList;
				}
			
			} // if($existCriteria)
			
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
		}
		
		require(SAFI_VISTA_PATH ."/avan/buscarRendicion.php");
	}
	
	public function VerDetalles()
	{
		try
		{
			$preMsg = "Error en ver detalles de la rendición de avance.";
			
			if(!isset($_REQUEST['idRendicion']))
				throw new Exception($preMsg." El identificador de la rendicion de avance \"idRendicion\" no pudo ser encontrado.");
				
			if(($idRendicion=$_REQUEST['idRendicion']) == null)
				throw new Exception($preMsg." El identificador de la rendicion de avance \"idRendicion\" es nulo.");
				
			if(($idRendicion=trim($idRendicion)) == '')
				throw new Exception($preMsg." El identificador de la rendicion de avance \"idRendicion\" está vacío.");
				
			$this->__VerDetalles(array('idRendicion' => $_REQUEST['idRendicion']));
		}
		catch (Exception $e)
		{
			// borrar
			echo "Error en ver detalles";
			error_log($e, 0);
		}
	}
	
	private function __VerDetalles($params)
	{
		/*
		 * 
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		 **/
		
		try {
			$form = FormManager::GetForm(FORM_NUEVA_RENDICION_AVANCE);
			
			$preMsg = "Error en ver detalles de la rendición de avance.";
			
			if($form == null)
				throw new Exception($preMsg." El formulario 'FORM_NUEVA_RENDICION_AVANCE' no pudo ser obtenido.");
			
			if(!isset($params['idRendicion']))
				throw new Exception($preMsg." No pudo se encontrado el parámetro params['idRendicion'].");
				
			if(($idRendicion=$params['idRendicion']) == null)
				throw new Exception($preMsg." El parámetro idRendicion es nulo.");
				
			if(($idRendicion=trim($idRendicion)) == '')
				throw new Exception($preMsg." El parámetro idRendicion está vacío.");
		
			$findRendicion = new EntidadRendicionAvance();
			$findRendicion->SetId($idRendicion);
			
			$rendicion = SafiModeloRendicionAvance::GetRendicionAvance($findRendicion);
			if($rendicion == null)
				throw new Exception($preMsg." No se pudo obtener la rendición de avance \"".$idRendicion."\".");
				
			$findAvance = new EntidadAvance();
			$findAvance->SetId($rendicion->GetAvance()->GetId());
			$avance = SafiModeloAvance::GetAvance($findAvance);
			
			if($avance == null)
				throw new Exception($preMsg." No se pudo obtener el avance \"".$findAvance->GetId()."\".");
				
			$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idRendicion);
			
			if($docGenera == null)
				throw new Exception($preMsg." No se pudo obtener el docGenera de la rendici&oacute;n de avance \"".$idRendicion."\".");
				
			$form->SetRendicionAvance($rendicion);
			$form->SetDocGenera($docGenera);
			$form->SetAvance($avance);
			
			// Para los documentos de soporte (Memos)
			$GLOBALS['SafiRequestVars']['memos'] = GetDocumentosSoportesMemos($idRendicion);
				
			// Para las revisiones del documento
			$GLOBALS['SafiRequestVars']['datosRevisionesDocumento'] = GetDatosRevisionesDocumento($idRendicion);
			
			require(SAFI_VISTA_PATH ."/avan/verDetallesRendicion.php");
			return;
			
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = "Ha ocurrido un error al intentar ver los detalles de la rendici&oacute;n de avance.";
			error_log($e, 0);
		}
		
		$this->Bandeja();
	}
	
	public function Enviar()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general']  =array();
		
		$idPerfil = $_SESSION['user_perfil_id'];
		
		// Validar el id de la rendición de viático nacional
		if(!isset($_REQUEST['idRendicion']) || ($idRendicion=trim($_REQUEST['idRendicion'])) == '')
		{
			$GLOBALS['SafiErrors']['general'][] = 'Identificador de la  rendici&oacute;n de avance no encontrado.';
		}
		else if(($result=$this->__Enviar($_REQUEST['idRendicion'])) !== false)
		{
			if(
				substr($idPerfil,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
				$idPerfil == PERFIL_ASISTENTE_EJECUTIVO ||
				$idPerfil == PERFIL_ASISTENTE_PRESIDENCIA
			){
				$GLOBALS['SafiInfo']['general'][] = 'Rendici&oacute;n de avance "'.$idRendicion.'" enviada satisfactoriamente.';
			} else {
				$GLOBALS['SafiInfo']['general'][] = 'Rendici&oacute;n de avance "'.$idRendicion.'" aprobada satisfactoriamente.';
			}
		}
		
		$this->Bandeja();
	}
	
	private function __Enviar($idRendicion)
	{
		try
		{
			$preMsg = "Error al intentar enviar la rendici&oacute;n de avance.";
			$idPerfil = $_SESSION['user_perfil_id'];
			$loginUsuario = $_SESSION['login'] ;
			$idDependencia = $_SESSION['user_depe_id'];
			$fechaHoy = date("d/m/Y H:i:s");
			
			/* Parche para que la rendición de avances salga en el año anterior */
			//$fechaHoy = "30/12/2014". date(" H:i:s");
			
			if($idRendicion == null || ($idRendicion=trim($idRendicion)) == '')
				throw new Exception("Identificador de la  rendici&oacute;n de avance no encontrado.");
				
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
				throw new Exception($preMsg." Operaci&oacute;n no permitida para este perfil.");
			
			$findRendicion = new EntidadRendicionAvance();
			$findRendicion->SetId($idRendicion);
			if(($rendicion = SafiModeloRendicionAvance::GetRendicionAvance($findRendicion)) == null)
				throw new Exception($preMsg." La rendici&oacute;n de avance \"" . $idRendicion . "\" no pudo ser cargada.");
			
			if($rendicion->GetDependencia() == null)
				throw new Exception($preMsg." Se produjo un problema durante la carga de la \"rendicionAvance dependencia\" ".
					" para la rendici&oacute;n de avance \"".$idRendicion."\".");
				
			$wFCadena = SafiModeloWFCadena::GetWFNextCadenaByIdDocument($idRendicion);
			
			if($wFCadena == null)
				throw new Exception($preMsg." WFCadena inicial no encontrada.");
				
			if($wFCadena->GetWFCadenaHijo() == null)
				throw new Exception($preMsg." WFCadena hija no encontrada.");
				
				// 0 = Documento finalizado
			if(
				strcmp($wFCadena->GetWFCadenaHijo()->GetId(), "0") == 0 ||
				// Finalizar si está en presupuesto y la gerencia es la oficina de gestión administrativa y financiera
				($idPerfil == PERFIL_JEFE_PRESUPUESTO && $rendicion->GetDependencia()->GetId() == "450")
			){
				// Obtener una instancia de docgenera para el avance a enviar (actualizar)
				if(($docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idRendicion)) == null)
					throw new Exception($preMsg." El DocGenera no pudo ser cargado para la rendici&oacute;n de avance \""
						.$idRendicion."\".");
				
				$estadoAprobado = 13;
				
				$docGenera->SetIdWFObjeto(99);
				$docGenera->SetIdWFCadena(0);
				$docGenera->SetIdEstatus($estadoAprobado);
				$docGenera->SetIdPerfilActual(null);
				
				$revisiones = new EntidadRevisionesDoc();
					
				$revisiones->SetIdDocumento($idRendicion);
				$revisiones->SetLoginUsuario($loginUsuario);
				$revisiones->SetIdPerfil($idPerfil);
				$revisiones->SetFechaRevision($fechaHoy);
				$revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
				
				// Guardar el registro del documento en docGenera (estado de la cadena)
				if(($enviado = SafiModeloDocGenera::EnviarDocumento($docGenera, $revisiones)) === false)
					throw new Exception($preMsg." No se pudo actualizar docGenera o revisionesDoc.");
			}
			else
			{
				if (($wFCadenaHijo = SafiModeloWFCadena::GetWFCadena($wFCadena->GetWFCadenaHijo())) == null)
					throw new Exception($preMsg." WFCadena hija no encontrada.");
					
				if($wFCadenaHijo->GetWFGrupo() == null)
					throw new Exception($preMsg." WFGrupo de WFCadena hija no encontrado.");
					
				if(($perfilActual = SafiModeloDependenciaCargo::
					GetSiguienteCargoDeCadena($idDependencia, $wFCadenaHijo->GetWFGrupo()->GetPerfiles())) == null)
					throw new Exception($preMsg." No se puede encontrar el perfil de la siguiente instancia en la cadena.");
				
				// Obtener una instancia de docgenera para la rendición de avance a enviar (actualizar)
				if(($docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idRendicion)) == null)
					throw new Exception($preMsg." El DocGenera no pudo ser cargado para la rendici&oacute;n de avance \""
						.$idRendicion."\".");
				
				$estadoEntransito = 10;
				
				$docGenera->SetIdWFObjeto($wFCadena->GetWFObjetoInicial()->GetId());
				$docGenera->SetIdWFCadena($wFCadena->GetId());
				$docGenera->SetIdEstatus($estadoEntransito);
				$docGenera->SetIdPerfilActual($perfilActual->GetId());
				
				$revisiones = null;
				
				if(
					substr($idPerfil,0,2)."000" != PERFIL_ASISTENTE_ADMINISTRATIVO &&
					$idPerfil != PERFIL_ASISTENTE_EJECUTIVO &&
					$idPerfil != PERFIL_ASISTENTE_PRESIDENCIA
				){
					$revisiones = new EntidadRevisionesDoc();
					
					$revisiones->SetIdDocumento($idRendicion);
					$revisiones->SetLoginUsuario($loginUsuario);
					$revisiones->SetIdPerfil($idPerfil);
					$revisiones->SetFechaRevision($fechaHoy);
					$revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
				}
				
				// Guardar el registro del documento en docGenera (estado de la cadena)
				if(SafiModeloDocGenera::EnviarDocumento($docGenera, $revisiones) === false)
					throw new Exception($preMsg." No se pudo actualizar docGenera o revisionesDoc.");
			}
			
			return true;
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = "Error en el env&iacute;o de la rendici&oacute;n de avance.";
			error_log($e, 0);
			return false;
		}
	}
	
	public function Devolver()
	{
		try
		{
			$GLOBALS['SafiErrors']['general'] = array();
			$GLOBALS['SafiInfo']['general']  = array();
			
			$preMsg = "Error al intentar devolver la rendici&oacute;n de avance.";
			$idDependencia = $_SESSION['user_depe_id'];
			$idPerfil = $_SESSION['user_perfil_id'];
			$loginUsuario = $_SESSION['login'];
			$opcionDevolver = 5;
			$fechaHoy = date("d/m/Y H:i:s");
			
			/* Parche para que la devolución de la rendición de avances salga en el año anterior */
			//$fechaHoy = "30/12/2014". date(" H:i:s");
			
			if(!isset($_REQUEST['idRendicion']) || ($idRendicion=trim($_REQUEST['idRendicion'])) == '')
				throw new Exception($preMsg." Identificador de la rendici&oacute;n de avance no encontrado.");
					
			if(!isset($_REQUEST['memoContent']) || ($memoContent=trim($_REQUEST['memoContent'])) == '')
				throw new Exception($preMsg." Motivo de la devoluci&oacute;n no encontrado.");
			
			if(	
				$idPerfil == PERFIL_JEFE_PRESUPUESTO ||
				$idPerfil == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS
			){
				$findRendicion = new EntidadRendicionAvance();
				$findRendicion->SetId($idRendicion);
				if(($rendicion=SafiModeloRendicionAvance::GetRendicionAvance($findRendicion)) == null)
					throw new Exception($preMsg." La rendci&oacute;n de avance \"" . $idRendicion . "\" no pudo ser cargada.");
				
				if($rendicion->GetDependencia() == null)
					throw new Exception($preMsg." Se produjo un problema durante la carga de \"rendicionAvance dependencia\" ".
						" para la rendici&oacute;n de avance \"".$idRendicion."\".");
					
				$dependencia = new EntidadDependencia();
				if($rendicion->GetDependencia()->GetId()=="350" || $rendicion->GetDependencia()->GetId()=="150"){
					$dependencia->SetId($rendicion->GetDependencia()->GetId());
				} else {
					$dependencia->SetId(null);
				}
				
				$documento = new EntidadDocumento();
				$documento->SetId(GetConfig("preCodigoRendicionAvance"));
				
				$wFOpcion = new EntidadWFOpcion();
				$wFOpcion->SetId($opcionDevolver);
				
				$wFGrupo = SafiModeloWFGrupo::GetWFGrupoByIdPerfil($idPerfil);
				
				if ($wFGrupo == null)
					throw new Exception($preMsg." WFGrupo no encontrado para el perfil actual. Rendición de avance: \""
						.$idRendicion."\".");
				
				$findWFCadena = new EntidadWFCadena();
				$findWFCadena->SetDocumento($documento);
				$findWFCadena->SetWFOPcion($wFOpcion);
				$findWFCadena->SetWFGrupo($wFGrupo);
				$findWFCadena->SetDependencia($dependencia);
				if(($wFCadena = SafiModeloWFCadena::GetWFCadena($findWFCadena)) == null)
					throw new Exception($preMsg." WFCadena inicial no encontrada. Rendición de avance: \"".$idRendicion."\".");
					
				// Obtener una instancia de docgenera para la rendición de  avance (actualizar)
				if(($docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idRendicion)) == null)
					throw new Exception($preMsg." El DocGenera no pudo ser cargado para la rendici&oacute;n de avance \""
						.$idRendicion."\".");
				
				$perfilActual = $docGenera->GetIdPerfil();
				$estadoDevuelto = 7;
				
				$docGenera->SetIdWFObjeto($wFCadena->GetWFObjetoInicial()->GetId());
				$docGenera->SetIdWFCadena($wFCadena->GetId());
				$docGenera->SetIdEstatus($estadoDevuelto);
				$docGenera->SetIdPerfilActual($perfilActual);
				
				$memo = new EntidadMemo();
				$memo->SetLoginUsuario($loginUsuario);
				$memo->SetAsunto(utf8_decode('Devolución de rendición de avance'));
				$memo->SetContenido($memoContent);
				$memo->SetIdDependencia($idDependencia);
				$memo->SetFechaCreacion($fechaHoy);
				
				$revisiones = new EntidadRevisionesDoc();
				$revisiones->SetIdDocumento($idRendicion);
				$revisiones->SetLoginUsuario($loginUsuario);
				$revisiones->SetIdPerfil($idPerfil);
				$revisiones->SetFechaRevision($fechaHoy);
				$revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
				
				if(SafiModeloDocGenera::DevolverDocumento($docGenera, $memo, $revisiones) === false)
					throw new Exception($preMsg." Rendición de avance: \"".$idRendicion."\".");
				
				$GLOBALS['SafiInfo']['general'][] = "Rendici&oacute;n de avance \"" . $idRendicion
					. "\" devuelta satisfactoriamente.";
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
				$documento->SetId(GetConfig("preCodigoRendicionAvance"));
				
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
					throw new Exception($preMsg." WFGrupo no encontrado para el perfil actual. Rendición de avance: \""
						.$idRendicion."\".");
					
				$findRendicion = new EntidadRendicionAvance();
				$findRendicion->SetId($idRendicion);
				if(($rendicion=SafiModeloRendicionAvance::GetRendicionAvance($findRendicion)) == null)
					throw new Exception($preMsg." La rendci&oacute;n de avance \"" . $idRendicion . "\" no pudo ser cargado.");
				
				if($rendicion->GetDependencia() == null)
					throw new Exception($preMsg." Se produjo un problema durante la carga de \"rendicionAvance dependencia\" ".
						" para la rendici&oacute;n de avance \"".$idRendicion."\".");
				
				$dependencia = new EntidadDependencia();
				if($rendicion->GetDependencia()->GetId()=="350" || $rendicion->GetDependencia()->GetId()=="150"){
					$dependencia->SetId($rendicion->GetDependencia()->GetId());
				} else {
					$dependencia->SetId(null);
				}
				
				$findWFCadena = new EntidadWFCadena();
				$findWFCadena->SetDocumento($documento);
				$findWFCadena->SetWFOPcion($wFOpcion);
				$findWFCadena->SetWFGrupo($wFGrupo);
				$findWFCadena->SetDependencia($dependencia);
				if(($wFCadena = SafiModeloWFCadena::GetWFCadena($findWFCadena)) == null)
					throw new Exception($preMsg." WFCadena inicial no encontrada. Rendición de avance: \"".$idRendicion."\".");
					
				if($wFCadena->GetWFCadenaHijo() == null)
					throw new Exception($preMsg." Detalles: WFCadena hija no encontrada. Rendición de avance: \"".$idRendicion."\".");
						
				// Obtener la cadena siguiente, a la inicial, de rendición de viáticos nacionales
				if (($wFCadenaHijo = SafiModeloWFCadena::GetWFCadena($wFCadena->GetWFCadenaHijo())) == null)
					throw new Exception($preMsg." Detalles: WFCadena hija no pudo ser cargada. Rendición de avance: \""
						.$idRendicion."\".");
					
				if($wFCadenaHijo->GetWFGrupo() == null)
					throw new Exception($preMsg." Detalles: WFGrupo de WFCadena hija no encontrado. Rendición de avance: \""
						.$idRendicion."\"");
					
				if(($perfilActual = SafiModeloDependenciaCargo::
					GetSiguienteCargoDeCadena($idDependencia, $wFCadenaHijo->GetWFGrupo()->GetPerfiles())) == null)
					throw new Exception($preMsg."Detalles: No se puede encontrar el perfil de la siguiente "
						."instancia en la cadena. Rendición de avance: \"".$idRendicion."\".");

				// Obtener una instancia de docgenera para la rendición de avance (actualizar)
				if(($docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idRendicion)) == null)
					throw new Exception($preMsg." El DocGenera no pudo ser cargado para la rendici&oacute;n de avance \""
						.$idRendicion."\".");
					
				$estadoDevuelto = 7;
						
				$docGenera->SetIdWFObjeto($wFCadena->GetWFObjetoInicial()->GetId());
				$docGenera->SetIdWFCadena($wFCadena->GetId());
				$docGenera->SetIdEstatus($estadoDevuelto);
				$docGenera->SetIdPerfilActual($perfilActual->GetId());
				
				$memo = new EntidadMemo();
				$memo->SetLoginUsuario($loginUsuario);
				$memo->SetAsunto(utf8_decode('Devolución de rendición de avance'));
				$memo->SetContenido($memoContent);
				$memo->SetIdDependencia($idDependencia);
				$memo->SetFechaCreacion($fechaHoy);
				
				$revisiones = new EntidadRevisionesDoc();
				$revisiones->SetIdDocumento($idRendicion);
				$revisiones->SetLoginUsuario($loginUsuario);
				$revisiones->SetIdPerfil($idPerfil);
				$revisiones->SetFechaRevision($fechaHoy);
				$revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
				
				if(SafiModeloDocGenera::DevolverDocumento($docGenera, $memo, $revisiones) === false)
					throw new Exception($preMsg." Rendición de avance: \"".$idRendicion."\".");
				
				$GLOBALS['SafiInfo']['general'][] = "Rendici&oacute;n de avance \"" . $idRendicion
					. "\", devuelta satisfactoriamente.";
			} // Fin de else if(	
			  //		substr($idPerfil,0,2)."000" == PERFIL_DIRECTOR ||
			  //		substr($idPerfil,0,2)."000" == PERFIL_GERENTE ||
			  //		$idPerfil == PERFIL_DIRECTOR_EJECUTIVO ||
			  //		$idPerfil == PERFIL_PRESIDENTE
			  //	)
			else
			{
				$GLOBALS['SafiErrors']['general'][] = "Error al devolver. Operaci&oacute;n no permitida para este perfil.";
			}
			
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = "Error en la devoluci&oacute;n de la rendici&oacute;n de avance.";
			error_log($e, 0);
		}
		
		$this->Bandeja();
	}
	
	public function Anular()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		
		$fechaHoy = date("d/m/Y H:i:s");
			
		/* Parche para que la anulación de la rendición de avances salga en el año anterior */
		//$fechaHoy = "30/12/2014". date(" H:i:s");
			
		try
		{
			$preMsg = "Error al intentar anular la rendici&oacute;n de avance.";
			$idPerfil = $_SESSION['user_perfil_id'];
			$loginUsuario = $_SESSION['login'] ;
			
			// Validar el id del avance
			if(!isset($_REQUEST['idRendicion']) || ($idRendicion=trim($_REQUEST['idRendicion'])) == '')
				throw new Exception($preMsg." Identificador de la  rendici&oacute;n de avance no encontrado.");

			if(	
				substr($idPerfil,0,2)."000" != PERFIL_ASISTENTE_ADMINISTRATIVO
				&& $idPerfil != PERFIL_ASISTENTE_EJECUTIVO
				&& $idPerfil != PERFIL_ASISTENTE_PRESIDENCIA
			)
				throw new Exception($preMsg." Operaci&oacute;n no permitida para este perfil.");
				
			$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idRendicion);
				
			$estadoAnulado = 15;
			$wfObjetoAnulado = 98;
			$wFOpcionAnular = 24;
			
			$docGenera->SetIdWFObjeto($wfObjetoAnulado);
			$docGenera->SetIdWFCadena(0);
			$docGenera->SetIdEstatus($estadoAnulado);
			$docGenera->SetIdPerfilActual(null);
			
			$revisiones = new EntidadRevisionesDoc();
			$revisiones->SetIdDocumento($idRendicion);
			$revisiones->SetLoginUsuario($loginUsuario);
			$revisiones->SetIdPerfil($idPerfil);
			$revisiones->SetFechaRevision($fechaHoy);
			$revisiones->SetIdWFOpcion($wFOpcionAnular);
			
			if(SafiModeloDocGenera::AnularDocumento($docGenera, $revisiones) === false)
				throw new Exception($preMsg." No se pudo actualizar docGenera o insertar las revisiones.");
			
			$GLOBALS['SafiInfo']['general'][] = "Rendici&oacute;n de avance \"".$idRendicion."\ anulada satisfactoriamente.";
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
		}
		
		$this->Bandeja();
	}
	
	public function GenerarPDF()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		
		try
		{
			if (!isset($_REQUEST['tipo']) || ($tipo=trim($_REQUEST['tipo'])) == '')
				throw new Exception("No se ha encontrado el tipo de documento a imprimir (Lineal o firmas por p&aacute;ginas).");
				
			if(!isset($_REQUEST['idRendicion']) || ($idRendicion=trim($_REQUEST['idRendicion'])) == '')
				throw new Exception("No se encontr&oacute; ning&uacute;n identificador de la rendici&oacute;n de avance.");
				
			if (
				strlen($idRendicion) <= 5
				|| $idRendicion == GetConfig("preCodigoRendicionAvance").GetConfig("delimitadorPreCodigoDocumento")
			)
				throw new Exception("El c&oacute;digo de la rendici&oacute;n de avance \"".$idRendicion."\" es incorrecta.");
			
			$findRendicion = new EntidadRendicionAvance();
			$findRendicion->SetId($idRendicion);
			
			if(($rendicion = SafiModeloRendicionAvance::GetRendicionAvance($findRendicion)) == null)
				$GLOBALS['SafiErrors']['general'][] = "La rendici&oacute;n de avance \"".$idRendicion."\" no pudo ser cargada.";
			
			if($rendicion->GetAvance() == null)
				throw new Exception("El avance asociado a la rendici&oacuten \"".$idRendicion."\" no pudo ser encontrado.");
				
			if(($idAvance = $rendicion->GetAvance()->GetId()) == null)
				throw new Exception("El id del avance asociado a la rendici&oacuten \"".$idRendicion."\" no pudo ser encontrado.");
			
			$findAvance = new EntidadAvance();
			$findAvance->SetId(trim($idAvance));
			if(($avance = SafiModeloAvance::GetAvance($findAvance)) == null)
				throw new Exception("El avance asociado a la rendici&oacuten \"".$idRendicion."\" no pudo ser cargado.");
			
			$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($rendicion->GetId());
			
			if($docGenera == null)
				throw new Exception("El docGenera para la rendici&oacute;n avance \"".$idRendicion."\" no pudo ser cargado.");
			
			$elaboradoPor = SafiModeloEmpleado::GetEmpleadoByCedula($rendicion->GetUsuaLogin());
			
			// Cargo de director o gerente
			$cargosGerenteDirector = GetPerfilCargosGerenteDirectorByIdUserPerfil($docGenera->GetIdPerfil());
			
			$cargoGerenteDirector = SafiModeloDependenciaCargo::GetSiguienteCargoDeCadena
				($rendicion->GetDependencia()->GetId(), $cargosGerenteDirector);
				
			$perfilGerenteDirector = $cargoGerenteDirector->GetId();
			// Fin cargo de director o gerente
			
			// Cargo de presidencia
			$cargoPresidente = GetPerfilCargoPesidente();
			
			$dependenciaPresidencia = SafiModeloDependencia::GetDependenciaById(substr($cargoPresidente->GetId(), 2, 3));
			
			$presidente = SafiModeloEmpleado::GetEmpleadoByCargoFundacionYDependencia
				(substr($cargoPresidente->GetId(), 0, 2), $dependenciaPresidencia);
			// Fin cargo de presidencia
			
			// Obtener los datos de las firmas del documento
			$arrFirmas = SafiModeloFirma::GetFirmaByPerfiles(array(
					$perfilGerenteDirector,
					PERFIL_DIRECTOR_EJECUTIVO,
					PERFIL_DIRECTOR_PRESUPUESTO,
					PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS,
					PERFIL_PRESIDENTE
			));
			
			$compromiso = null;
			$solicitudPago = null;
			
			// Comprobar si el avance de la rendición tiene un compromiso asociado y obtenerlo 
			$compromiso = SafiModeloCompromiso::GetCompromisoByIdDocumento($avance->GetId());
			
			$GLOBALS['SafiRequestVars']['arrFirmas'] = $arrFirmas;
			$GLOBALS['SafiRequestVars']['perfilGerenteDirector'] = $perfilGerenteDirector;
			$GLOBALS['SafiRequestVars']['tipo'] = $tipo;
			$GLOBALS['SafiRequestVars']['rendicionAvance'] = $rendicion;
			$GLOBALS['SafiRequestVars']['docGenera'] = $docGenera;
			$GLOBALS['SafiRequestVars']['avance'] = $avance;
			$GLOBALS['SafiRequestVars']['elaboradoPor'] = $elaboradoPor;
			$GLOBALS['SafiRequestVars']['compromiso'] = $compromiso;

			require(SAFI_VISTA_PATH . "/avan/rendicionAvance_PDF.php");
			return;
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
			error_log($e, 0);
		}
		
		if(count($GLOBALS['SafiErrors']['general'])<=0){
			$GLOBALS['SafiErrors']['general'][] = "Se ha producido un error al intentar imprimir la rendici&oacute;n de avance. ".
				 "Comuniquese con el administrador del sistema.";
		}
		
		if($avance != null)
			$this->__VerDetalles(array('idRendicion' => $rendicion->GetId()));
		else
			$this->Bandeja();
		
		return;
	}
	
	private function __CargarAvance(NuevaRendicionAvanceForm $form)
	{
		// Buscar y establecer el objeto avance asociado a la rendición de avance
		$form->SetIdAvanceBuscado($form->GetIdAvance());
		$findAvance = new EntidadAvance();
		$findAvance->SetId($form->GetIdAvance());
		$avance = SafiModeloAvance::GetAvance($findAvance);
		$form->SetAvance($avance);
		
		if($avance === null)
			$GLOBALS['SafiErrors']['general'][] = 'El avance "'.$form->GetIdAvance().'" no puede ser encontrado.';
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
	
	// Obtener y validar el id de la rendicion
	private function __ValidarIdRendicionAvance(NuevaRendicionAvanceForm $form){
		if(!isset($_POST['idRendicion']) || trim($_POST['idRendicion']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Identificador de la rendici&oacute;n de avance no encontrado.';
		} else {
			$idRendicion=trim($_POST['idRendicion']);
			if(
				strlen($idRendicion)<11 || 
				strpos($idRendicion, GetConfig("preCodigoRendicionAvance") . GetConfig("delimitadorPreCodigoDocumento")) !== 0
			){
				$GLOBALS['SafiErrors']['general'][] = 'Identificador de la rendici&oacute;n de avance inv&aacute;lido.';
			} else {
				$form->GetRendicionAvance()->SetId($idRendicion);
			}
		}
	}
	
	// Obtener y validar el id del avance
	private function __ValidarIdAvance(NuevaRendicionAvanceForm $form){
		if(!isset($_POST['idAvance']) || trim($_POST['idAvance']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Identificador del avance no encontrado.';
		} else {
			$idAvance=trim($_POST['idAvance']);
			if(
				strlen($idAvance)<11 || 
				strpos($idAvance, GetConfig("preCodigoAvance") . GetConfig("delimitadorPreCodigoDocumento")) !== 0
			){
				$GLOBALS['SafiErrors']['general'][] = 'Identificador del avance inv&aacute;lido.';
			} else {
				$form->SetIdAvance($idAvance);
				$this->__CargarAvance($form);
			}
		}
	}
	
	// Obtener y validar la fecha de la rendición
	private function __ValidarFechaRendicion(NuevaRendicionAvanceForm $form)
	{
		if(!isset($_POST['fechaRendicion']) || trim($_POST['fechaRendicion']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Debe indicar la fecha de la rendici&oacute;n de avance.';
		} else {
			if(($fecha = $this->__ValidarFecha($_POST['fechaRendicion'])) !== false){
				$form->GetRendicionAvance()->SetFechaRendicion($fecha);
			} else {
				$GLOBALS['SafiErrors']['general'][] = 'Fecha de avance inv&aacute;lida.';
			}
		}
	}
	
	// Obtener y validar la fecha de inicio de la actividad
	private function __ValidarFechaInicioActividad(NuevaRendicionAvanceForm $form)
	{
		if(!isset($_POST['fechaInicioActividad']) || trim($_POST['fechaInicioActividad']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Debe indicar la fecha inicio de la actividad.';
		} else {
			if(($fecha = $this->__ValidarFecha($_POST['fechaInicioActividad'])) !== false){
				$form->GetRendicionAvance()->SetFechaInicioActividad($fecha);
			} else {
				$GLOBALS['SafiErrors']['general'][] = 'Fecha inicio de la actividad inv&aacute;lida.';
			}
		}
	}
	
	// Obtener y validar la fecha de fin de la actividad
	private function __ValidarFechaFinActividad(NuevaRendicionAvanceForm $form)
	{
		if(!isset($_POST['fechaFinActividad']) || trim($_POST['fechaFinActividad']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Debe indicar la fecha fin de la actividad.';
		} else {
			if(($fecha = $this->__ValidarFecha($_POST['fechaFinActividad'])) !== false){
				$form->GetRendicionAvance()->SetFechaFinActividad($fecha);
			} else {
				$GLOBALS['SafiErrors']['general'][] = 'Fecha fin de la actividad inv&aacute;lida.';
			}
		}
	}
	
	// Obtener y validar los objetivos
	private function __ValidarObjetivos(NuevaRendicionAvanceForm $form)
	{
		if(!isset($_POST['objetivos']) || trim($_POST['objetivos']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Debe indicar los logros alcanzados.';
		} else {
			$form->GetRendicionAvance()->SetObjetivos(($_POST['objetivos']));
		}
	}
	
	// Obtener y validar la descripción
	private function __ValidarDescripcion(NuevaRendicionAvanceForm $form)
	{
		if(isset($_POST['descripcion']) && trim($_POST['descripcion']) != ''){
			$form->GetRendicionAvance()->SetDescripcion(($_POST['descripcion']));
		}
	}
	
	// Obtener y validar la nroParticipantes
	private function __ValidarNroParticipantes(NuevaRendicionAvanceForm $form)
	{
		if(!isset($_POST['nroParticipantes']) || trim($_POST['nroParticipantes']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Debe indicar el n&uacute;mero de participantes.';
		} else {
			$form->GetRendicionAvance()->SetNroParticipantes(trim($_POST['nroParticipantes']));
		}
	}
	
	// Obtener y validar los responsables del avance y sus partidas
	private function __ValidarResponsablesRendicionAvancePartidas(NuevaRendicionAvanceForm $form, $annoPresupuesto)
	{
		$responsablesRendicionAvancePartidas = array();
		$responsablesRendicionAvancePartidasByCedula = array();
		
		try
		{
			/******************************************************************************
			 * - Obtener los datos de los responsables del avance para poder luego,       *
			 *   (a partir de los mismos) establecer ciertos datos de los responsables    *
			 *   de la rendición del avance: como el estado de los responsables, y el     *
			 *   monto de anticipo para los responsables incluidos en la rendición        *
			 *   actual.                                                                  *
			 ******************************************************************************/
			
			$avance = $form->GetAvance();
			$responsablesAvancePartidasByIdResponsable = array();
			// Buscar el estado de los responsables de la rendición de avance
			if($avance != null && is_array($avance->GetResponsablesAvancePartidas()))
			{
				foreach($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidas)
				{
					$cedula = null;
					$responsableAvance = $responsableAvancePartidas->GetResponsableAvance();
					
					if($responsableAvance != null && $responsableAvance->GetId() != null)
					{
						// Obtener el id del responsable
						$responsablesAvancePartidasByIdResponsable[$responsableAvance->GetId()] = $responsableAvancePartidas;
					} // if($responsableAvance != null)
					
				} // foreach($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidas)
			} // if($avance != null && is_array($avance->GetResponsablesAvancePartidas()))
			
			/******************************************************************************
			 * - Fin de obtener los datos de los responsables del avance para poder luego,*
			 *   (a partir de los mismos) establecer ciertos datos de los responsables    *
			 *   de la rendición del avance: como el estado de los responsables, y el     *
			 *   monto de anticipo para los responsables incluidos en la rendición        *
			 *   actual.                                                                  *
			 ******************************************************************************/
			
			// Validar los responsablesRendicionAvancePartidas
			if(!isset($_POST['tiposResponsables']) || !is_array($_POST['tiposResponsables']))
				throw new Exception('Debe indicar al menos un responsable para la rendici&oacute;n de avance. '.
					'Identificador del tipo de responsable no encontrado.');
				
			if(($countTiposResponsables = count($_POST['tiposResponsables'])) <= 0)
				throw new Exception('Debe indicar al menos un responsable para la rendici&oacute;n de avance. '.
					'El contador de responsables es cero.');
				
			$indexResponsablesLocal = 0;
			// Crear entidades responsableRendicionAvancePartidas
			foreach ($_POST['tiposResponsables'] as $tipoResponsable)
			{
				$responsableRendicionAvancePartidas = new EntidadResponsableRendicionAvancePartidas();
				$responsableRendicionAvance = new EntidadResponsableRendicionAvance();
				$responsableRendicionAvancePartidas->SetResponsableRendicionAvance($responsableRendicionAvance);
				
				if($tipoResponsable == EntidadResponsable::TIPO_EMPLEADO || $tipoResponsable == EntidadResponsable::TIPO_BENEFICIARIO){
					$responsableRendicionAvance->SetTipoResponsable($tipoResponsable);
				} else {
					$responsableRendicionAvance->SetTipoResponsable(EntidadResponsable::TIPO_NINGUNO);
					$GLOBALS['SafiErrors']['general'][] = 'Responsable['.($indexResponsablesLocal+1).'] no encontrado.';
				}
				
				$indexResponsablesLocal++;
				
				$responsablesRendicionAvancePartidas[] = $responsableRendicionAvancePartidas;
			}
			
			// validar idResponsablesAvance
			$itemRequestName = 'idResponsablesAvance';
			if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
				$GLOBALS['SafiErrors']['general'][] = 'Uno o mas responsables carecen de ids.';
			} else if(count($_POST[$itemRequestName]) != $countTiposResponsables) {
				$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de responsables y de ids no coincide.';
			} else {
				$indexResponsablesLocal = 0;
				reset($responsablesRendicionAvancePartidas);
				$responsableRendicionAvancePartidas = current($responsablesRendicionAvancePartidas);
				foreach($_POST[$itemRequestName] as $item)
				{
					$responsableRendicionAvance = $responsableRendicionAvancePartidas->GetResponsableRendicionAvance();
					if(($idResponsable=trim($item)) != '')
					{
						$responsableRendicionAvance->SetIdResponsableAvance($idResponsable);
						
						if($idResponsable != null && isset($responsablesAvancePartidasByIdResponsable[$idResponsable])){
							// Establecer el estado del responsable actual ($cedula) de la rendición de avance 
							$responsableRendicionAvance->SetEstado(
								$responsablesAvancePartidasByIdResponsable[$idResponsable]->GetResponsableAvance()->GetEstado()
							);
							// Obtener el monto total de anticipo del responsable actual (cedula)
							$responsableRendicionAvancePartidas->SetMontoAnticipo(
								$responsablesAvancePartidasByIdResponsable[$idResponsable]->GetMontoTotal());
						}
					}else {
						$GLOBALS['SafiErrors']['general'][] = 'Id del responsable['.($indexResponsablesLocal+1).'] no encontrado.';
					}
					$indexResponsablesLocal++;
					$responsableRendicionAvancePartidas = next($responsablesRendicionAvancePartidas);
				}
			}
			
			// Validar cedulasResponsables
			$itemRequestName = 'cedulasResponsables';
			if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
				$GLOBALS['SafiErrors']['general'][] = 'Uno o mas responsables carecen de c&eacute;dula.';
			} else if(count($_POST[$itemRequestName]) != $countTiposResponsables) {
				$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de responsables y de c&eacute;dulas no coincide.';
			} else {
				$indexResponsablesLocal = 0;
				reset($responsablesRendicionAvancePartidas);
				$responsableRendicionAvancePartidas = current($responsablesRendicionAvancePartidas);
				foreach($_POST[$itemRequestName] as $item)
				{
					$cedula = null;
					$responsableRendicionAvance = $responsableRendicionAvancePartidas->GetResponsableRendicionAvance();
					if(trim($item) != '')
					{
						if($responsableRendicionAvance->GetTipoResponsable() != EntidadResponsable::TIPO_NINGUNO){
							if($responsableRendicionAvance->GetTipoResponsable() == EntidadResponsable::TIPO_EMPLEADO){
								$empleado = SafiModeloEmpleado::GetEmpleadoByCedula(trim($item));
								$responsableRendicionAvance->SetEmpleado($empleado);
								if($empleado !== null)
									$cedula = $empleado->GetId();
								else
									$GLOBALS['SafiErrors']['general'][] = 'Responsable empleado['.
										($indexResponsablesLocal+1).'] C.I. '.trim($item).' no puede ser cargado.';
							} else if($responsableRendicionAvance->GetTipoResponsable() == EntidadResponsable::TIPO_BENEFICIARIO){
								$beneficiario = SafiModeloBeneficiarioViatico::GetBeneficiarioViaticoByCedula(trim($item));
								$responsableRendicionAvance->SetBeneficiario($beneficiario);
								if($beneficiario !== null)
									$cedula = $beneficiario->GetId();
								else
									$GLOBALS['SafiErrors']['general'][] = 'Responsable beneficiario['.
										($indexResponsablesLocal+1).'] C.I. '.trim($item).' no puede ser cargado.';
							}
						}
					} else {
						$GLOBALS['SafiErrors']['general'][] = 'C&eacute;dula del responsable['.
							($indexResponsablesLocal+1).'] no encontrada.';
					}
					$indexResponsablesLocal++;
					$responsableRendicionAvancePartidas = next($responsablesRendicionAvancePartidas);
				}
			}
			
			// Validar correlativosResponsables
			$itemRequestName = 'correlativosResponsables';
			$arrCorrelativos = array();
			if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
				$GLOBALS['SafiErrors']['general'][] = 'Uno o mas responsables carecen de correlativos.';
			} else if(count($_POST[$itemRequestName]) != $countTiposResponsables) {
				$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de responsables y de correlativos no coincide.';
			} else {
				$indexResponsablesLocal = 0;
				foreach($_POST[$itemRequestName] as $item)
				{
					if(trim($item) == null || trim($item) == ''){
						$GLOBALS['SafiErrors']['general'][] = 'Debe indicar un correlativo para el responsable['.
							($indexResponsablesLocal+1).'].';
					} else {
						$arrCorrelativos[] = trim($item);
					}
					$indexResponsablesLocal++;
				}
			}
			
			$arrExisteInfo = array();
			$arrMsgErrors = array();
			
			// Validar partidas
			$itemRequestName = 'partidas';
			if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
				$GLOBALS['SafiErrors']['general'][] = 'Uno o mas responsables carecen de partidas.';
			} else if(count($_POST[$itemRequestName]) != $countTiposResponsables) {
				$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de responsables y de listas de partidas no coincide.';
			} else {
				$indexResponsablesLocal = 0;
				reset($responsablesRendicionAvancePartidas);
				$responsableRendicionAvancePartidas = current($responsablesRendicionAvancePartidas);
				foreach($_POST[$itemRequestName] as $index => $partidas)
				{
					$rendicionAvancePartidas = array();
					$arrExisteInfo[$indexResponsablesLocal] = array();
					$arrMsgErrors[$indexResponsablesLocal] = array();
					
					if($index != $arrCorrelativos[$indexResponsablesLocal])
					{
						$GLOBALS['SafiErrors']['general'][] = 'El indice de la listas de partidas['.($indexResponsablesLocal+1).']='
						 . $index.', y el correlativo '.$arrCorrelativos[$indexResponsablesLocal]
						 .' no coinciden.';
					} else {
						$indexPartida = 0;
						$idsPartidas = array();
						foreach ($partidas as $idPartida){
							// Se asume que inicialmente no existe información de partidas 
							$arrExisteInfo[$indexResponsablesLocal][$indexPartida] = false;
							$arrMsgErrors[$indexResponsablesLocal][$indexPartida] = array();
							
							$partida = new EntidadPartida();
									
							$rendicionAvancePartida = new EntidadRendicionAvancePartida();
							$rendicionAvancePartida->SetPartida($partida);
							
							$rendicionAvancePartidas[] = $rendicionAvancePartida;
							
							// Validar id de partida
							if(trim($idPartida) != ''){
								$arrExisteInfo[$indexResponsablesLocal][$indexPartida] = true;
								
								$partida->SetId(trim($idPartida));
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
								foreach ($rendicionAvancePartidas as $indexPartida => $rendicionAvancePartida)
								{
									if($rendicionAvancePartida != null && $rendicionAvancePartida->GetPartida() != null
										&& ($idPartida=$rendicionAvancePartida->GetPartida()->GetId()) != null
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
								
						$responsableRendicionAvancePartidas->SetRendicionAvancePartidas($rendicionAvancePartidas);
					}
					$indexResponsablesLocal++;
					$responsableRendicionAvancePartidas = next($responsablesRendicionAvancePartidas);
				}
			}
			
			// Validar montos de partidas (partidasMontos)
			$itemRequestName = 'partidasMontos';
			if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
				$GLOBALS['SafiErrors']['general'][] = 'Uno o mas responsables carecen de montos de partidas.';
			} else if(count($_POST[$itemRequestName]) != $countTiposResponsables) {
				$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de responsables y de listas de montos de partidas no coincide.';
			} else {
				$indexResponsablesLocal = 0;
				reset($responsablesRendicionAvancePartidas);
				$responsableRendicionAvancePartidas = current($responsablesRendicionAvancePartidas);
				foreach($_POST[$itemRequestName] as $index => $partidasMontos)
				{
					$rendicionAvancePartidas = $responsableRendicionAvancePartidas->GetRendicionAvancePartidas();
					
					if($index != $arrCorrelativos[$indexResponsablesLocal])
					{
						$GLOBALS['SafiErrors']['general'][] = 'El indice de la listas de montos de partidas['
							.$indexResponsablesLocal.']=' . $index.', y el correlativo '.$arrCorrelativos[$indexResponsablesLocal]
							.' no coinciden.';
					} else if(count($partidasMontos) != count($rendicionAvancePartidas)){
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de partidas['.$indexResponsablesLocal
							.'] y el n&uacute;mero de montos de partidas['.$indexResponsablesLocal.'] no coinciden'
							.'count($partidasMontos): ' . count($partidasMontos) 
							. ', count($rendicionAvancePartidas): ' . count($rendicionAvancePartidas).".";
					} else {
						$indexPartidaMonto = 0;
						foreach ($partidasMontos as $partidaMonto)
						{
							// Validar el monto de la partida
							if(trim($partidaMonto) != ''){
								$arrExisteInfo[$indexResponsablesLocal][$indexPartidaMonto] = true;
								
								$rendicionAvancePartida = $rendicionAvancePartidas[$indexPartidaMonto];
								$rendicionAvancePartida->SetMonto(trim($partidaMonto));
								
							} else {
								$arrMsgErrors[$indexResponsablesLocal][$indexPartidaMonto][] = 'Debe indicar un monto de partida['
								.($indexPartidaMonto+1).']'. ' para el responsable['.($indexResponsablesLocal+1).']';
							}
							$indexPartidaMonto++;
						}
					}
					$indexResponsablesLocal++;
					$responsableRendicionAvancePartidas = next($responsablesRendicionAvancePartidas);
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
			
			
			/**************************************************************************************
			 *   Validar la información referente al reintegro de dinero en caso de que aplique   *
			 **************************************************************************************/
			
			$arrExisteInfo = array();
			$arrMsgErrors = array();
			// Inicialmente se asume que no existe información de cuentas bancarias
			for ($i = 0; $i < $countTiposResponsables; $i++)
			{
				$arrExisteInfo[$i] = array();
				$arrMsgErrors[$i] = array();
			}
			
			// Validar bancosReintegros
			$itemRequestName = 'bancosReintegros';
			if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
				$GLOBALS['SafiErrors']['general'][] = 'Uno o mas responsables carecen de bancos para el reintegro.';
			} else if(count($_POST[$itemRequestName]) != $countTiposResponsables) {
				$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de responsables y de bancos para el reintegro no coincide.';
			} else {
				$indexResponsablesLocal = 0;
				reset($responsablesRendicionAvancePartidas);
				$responsableRendicionAvancePartidas = current($responsablesRendicionAvancePartidas);
				
				foreach ($_POST[$itemRequestName] as $index => $bancos){
				
					$rendicionAvanceReintegros = array();
					$arrExisteInfo[$indexResponsablesLocal] = array();
					$arrMsgErrors[$indexResponsablesLocal] = array();
					
					if($index != $arrCorrelativos[$indexResponsablesLocal])
					{
						$GLOBALS['SafiErrors']['general'][] = 'El indice de la listas de bancos de de reintegros['
							.($indexResponsablesLocal+1).']='. $index.', y el correlativo '.$arrCorrelativos[$indexResponsablesLocal]
							.' no coinciden.';
					} else {
						$indexBanco = 0;
						foreach($bancos as $idBanco)
						{
							// Se asume que inicialmente no existe información de partidas 
							$arrExisteInfo[$indexResponsablesLocal][$indexBanco] = false;
							$arrMsgErrors[$indexResponsablesLocal][$indexBanco] = array();
							
							$rendicionAvanceReintegro = new EntidadRendicionAvanceReintegro();
							$rendicionAvanceReintegros[] = $rendicionAvanceReintegro;
							
							// Validar id del banco
							if($idBanco != null && ($idBanco=trim($idBanco)) != '' && strcmp($idBanco, "0")!=0){
								$arrExisteInfo[$indexResponsablesLocal][$indexBanco] = true;
								if(($banco=SafiModeloBanco::GetBancoById($idBanco)) != null){
									$rendicionAvanceReintegro->SetBanco($banco);	
								} else {
									$arrMsgErrors[$indexResponsablesLocal][] = 'El banco del reintegro['.($indexBanco+1).'] '
										.'del responsable ['.($indexResponsablesLocal+1).'] no pudo ser cargado: ';
								}
							} else {
								$arrMsgErrors[$indexResponsablesLocal][$indexBanco][] = 'Debe indicar un banco del reintegro['
									.($indexBanco+1).']'. ' del responsable['.($indexResponsablesLocal+1).']';
							}
							$indexBanco++;
						}// foreach($bancos as $idBanco)
						$responsableRendicionAvancePartidas->SetRendicionAvanceReintegros($rendicionAvanceReintegros);
					}
					$indexResponsablesLocal++;
					$responsableRendicionAvancePartidas = next($responsablesRendicionAvancePartidas);
				} // foreach ($_POST[$itemRequestName] as $bancos){
			}
			
			// Validar referenciasReintegros
			$itemRequestName = 'referenciasReintegros';
			if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
				$GLOBALS['SafiErrors']['general'][] = 'Uno o mas responsables carecen de referencia bancaria para el reintegro.';
			} else if(count($_POST[$itemRequestName]) != $countTiposResponsables) {
				$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de responsables y de referencias bancarias para el
					reintegro no coincide.';
			} else {
				$indexResponsablesLocal = 0;
				reset($responsablesRendicionAvancePartidas);
				$responsableRendicionAvancePartidas = current($responsablesRendicionAvancePartidas);
				foreach($_POST[$itemRequestName] as $index => $referencias)
				{
					$rendicionAvanceReintegros = $responsableRendicionAvancePartidas->GetRendicionAvanceReintegros();
					
					if($index != $arrCorrelativos[$indexResponsablesLocal])
					{
						$GLOBALS['SafiErrors']['general'][] = 'El indice de la listas de referencias de reintegros['
							.$indexResponsablesLocal.']=' . $index.', y el correlativo '.$arrCorrelativos[$indexResponsablesLocal]
							.' no coinciden.';
					} else if(count($referencias) != count($rendicionAvanceReintegros)){
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de reintegros['.$indexResponsablesLocal
							.'] y el n&uacute;mero de referencias de reintegro['.$indexResponsablesLocal.'] no coinciden'
							.'count($referencias): ' . count($referencias) 
							. ', count($rendicionAvanceReintegros): ' . count($rendicionAvanceReintegros).".";
					} else {
						$indexReferencia = 0;
						reset($rendicionAvanceReintegros);
						$rendicionAvanceReintegro = current($rendicionAvanceReintegros);
						foreach ($referencias as $referencia)
						{
							// Validar la referencia
							if($referencia != null && ($referencia=trim($referencia)) != ''){
								$arrExisteInfo[$indexResponsablesLocal][$indexReferencia] = true;
								$rendicionAvanceReintegro->SetReferencia($referencia);
							} else {
								$arrMsgErrors[$indexResponsablesLocal][$indexReferencia][] = 'Debe indicar una referencia del reintegro['
									.($indexReferencia+1).']'. ' del responsable['.($indexResponsablesLocal+1).'].';
							}
							$indexReferencia++;
							$rendicionAvanceReintegro = next($rendicionAvanceReintegros);
						} // foreach ($referencias as $referencia)
					}
					
					$indexResponsablesLocal++;
					$responsableRendicionAvancePartidas = next($responsablesRendicionAvancePartidas);
				} // foreach($_POST[$itemRequestName] as $index => $referencias)
			}
			
			// Validar fechasReintegros
			$itemRequestName = 'fechasReintegros';
			if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
				$GLOBALS['SafiErrors']['general'][] = 'Uno o mas responsables carecen de fecha para el reintegro.';
			} else if(count($_POST[$itemRequestName]) != $countTiposResponsables) {
				$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de responsables y de fechas para el reintegro no coincide.';
			} else {
				$indexResponsablesLocal = 0;
				reset($responsablesRendicionAvancePartidas);
				$responsableRendicionAvancePartidas = current($responsablesRendicionAvancePartidas);
				foreach($_POST[$itemRequestName] as $index => $fechas)
				{
					$rendicionAvanceReintegros = $responsableRendicionAvancePartidas->GetRendicionAvanceReintegros();
					
					if($index != $arrCorrelativos[$indexResponsablesLocal])
					{
						$GLOBALS['SafiErrors']['general'][] = 'El indice de la listas de fechas de reintegros['
							.$indexResponsablesLocal.']=' . $index.', y el correlativo '.$arrCorrelativos[$indexResponsablesLocal]
							.' no coinciden.';
					} else if(count($fechas) != count($rendicionAvanceReintegros)){
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de reintegros['.$indexResponsablesLocal
							.'] y el n&uacute;mero de fechas de reintegro['.$indexResponsablesLocal.'] no coinciden'
							.'count($fechas): ' . count($fechas) 
							. ', count($rendicionAvanceReintegros): ' . count($rendicionAvanceReintegros).".";
					} else {
						$indexFecha = 0;
						reset($rendicionAvanceReintegros);
						$rendicionAvanceReintegro = current($rendicionAvanceReintegros);
						foreach ($fechas as $fecha)
						{
							// validar fecha para el reintegro
							if($fecha != null && ($fecha=trim($fecha)) != ''){
								if(($fecha = $this->__ValidarFecha($fecha)) !== false){
									$arrExisteInfo[$indexResponsablesLocal][$indexFecha] = true;
									$rendicionAvanceReintegro->SetFecha($fecha);
								} else {
									$arrMsgErrors[$indexResponsablesLocal][$indexFecha][] = 'La fecha del reintegro['
										.($indexFecha+1).'] del responsable ['.($indexResponsablesLocal+1).'] es inv&aacute;lida.';
								}
							} else {
								$arrMsgErrors[$indexResponsablesLocal][$indexFecha][] = 'Debe indicar una fecha del reintegro['
									.($indexFecha+1).'] del responsable['.($indexResponsablesLocal+1).'].';
							}
							
							$indexFecha++;
							$rendicionAvanceReintegro = next($rendicionAvanceReintegros);
						} // foreach ($fechas as $fecha)
					}
					
					$indexResponsablesLocal++;
					$responsableRendicionAvancePartidas = next($responsablesRendicionAvancePartidas);
				} // foreach($_POST[$itemRequestName] as $index => $fechas)
			}
			
			// Validar montosReintegros
			$itemRequestName = 'montosReintegros';
			if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
				$GLOBALS['SafiErrors']['general'][] = 'Uno o mas responsables carecen de monto para el reintegro.';
			} else if(count($_POST[$itemRequestName]) != $countTiposResponsables) {
				$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de responsables y de montos para el reintegro no coincide.';
			} else {
				$indexResponsablesLocal = 0;
				reset($responsablesRendicionAvancePartidas);
				$responsableRendicionAvancePartidas = current($responsablesRendicionAvancePartidas);
				
				foreach($_POST[$itemRequestName] as $index => $montos)
				{
					$rendicionAvanceReintegros = $responsableRendicionAvancePartidas->GetRendicionAvanceReintegros();
					
					if($index != $arrCorrelativos[$indexResponsablesLocal])
					{
						$GLOBALS['SafiErrors']['general'][] = 'El indice de la listas de montos de reintegros['
							.$indexResponsablesLocal.']=' . $index.', y el correlativo '.$arrCorrelativos[$indexResponsablesLocal]
							.' no coinciden.';
					} else if(count($montos) != count($rendicionAvanceReintegros)){
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de reintegros['.$indexResponsablesLocal
							.'] y el n&uacute;mero de montos de reintegro['.$indexResponsablesLocal.'] no coinciden'
							.'count($montos): ' . count($montos) 
							. ', count($rendicionAvanceReintegros): ' . count($rendicionAvanceReintegros);
					} else {
						$indexMonto = 0;
						reset($rendicionAvanceReintegros);
						$rendicionAvanceReintegro = current($rendicionAvanceReintegros);
						foreach ($montos as $monto)
						{
							// Validar el monto de reintegro
							if($monto != null && ($monto=trim($monto)) != ''){
								$arrExisteInfo[$indexResponsablesLocal][$indexMonto] = true;
								$rendicionAvanceReintegro->SetMonto($monto);
								
								if($monto < 0.000001)
								{
									$arrMsgErrors[$indexResponsablesLocal][$indexMonto][] = 'El monto del reintegro['.($indexMonto+1)
										.']'. ' del responsable['.($indexResponsablesLocal+1).'] debe ser mayor a 0.';
								}
							} else {
								$arrMsgErrors[$indexResponsablesLocal][$indexMonto][] = 'Debe indicar un monto del reintegro['
									.($indexMonto+1).']'. ' del responsable['.($indexResponsablesLocal+1).'].';
							}
							$indexMonto++;
							$rendicionAvanceReintegro = next($rendicionAvanceReintegros);
						} // foreach ($montos as $monto)
					}
					
					$indexResponsablesLocal++;
					$responsableRendicionAvancePartidas = next($responsablesRendicionAvancePartidas);
				} // foreach($_POST[$itemRequestName] as $index => $montos)
			}
			
			// Mostrar los mensajes de error para los datos de reintegro si son necesarios
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
			
			/*********************************************************************************************
			 *   Fin de Validar la información referente al reintegro de dinero en caso de que aplique   *
			 *********************************************************************************************/
			
			$form->GetRendicionAvance()->SetResponsablesRendicionAvancePartidas($responsablesRendicionAvancePartidas);
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
		}
	}
	
	// Obtener y validar las observaciones de la rendición de avance
	private function __ValidarObservaciones(NuevaRendicionAvanceForm $form)
	{
		if(isset($_POST['observaciones']) && trim($_POST['observaciones']) != ''){
			$form->GetRendicionAvance()->SetObservaciones(trim($_POST['observaciones']));
		}
	}
}

new RendicionAvanceAccion();

?>