<?php
require_once(dirname(__FILE__) . '/../../init.php');

// Acciones
require_once(SAFI_ACCIONES_PATH. '/acciones.php');

// Includes
require_once(SAFI_INCLUDE_PATH. '/perfiles/constantesPerfiles.php');

// Libs
require_once (SAFI_LIB_PATH . '/general.php');

// Modelo
require_once(SAFI_MODELO_PATH. '/asignacionviatico.php');
require_once(SAFI_MODELO_PATH. '/banco.php');
require_once(SAFI_MODELO_PATH. '/cargo.php');
require_once(SAFI_MODELO_PATH. '/cheque.php');
require_once(SAFI_MODELO_PATH. '/chequera.php');
require_once(SAFI_MODELO_PATH. '/compromiso.php');
require_once(SAFI_MODELO_PATH. '/dependencia.php');
require_once(SAFI_MODELO_PATH. '/dependenciacargo.php');
require_once(SAFI_MODELO_PATH. '/docgenera.php');
require_once(SAFI_MODELO_PATH. '/empleado.php');
require_once(SAFI_MODELO_PATH. '/estatus.php');
require_once(SAFI_MODELO_PATH. '/firma.php');
require_once(SAFI_MODELO_PATH. '/general.php');
require_once(SAFI_MODELO_PATH. '/rendicionViaticoNacional.php');
require_once(SAFI_MODELO_PATH. '/solicitudPago.php');
require_once(SAFI_MODELO_PATH. '/viaticonacional.php');
require_once(SAFI_MODELO_PATH. '/wfcadena.php');
require_once(SAFI_MODELO_PATH. '/wfgrupo.php');
require_once(SAFI_MODELO_PATH. '/pagoTransferencia.php');
require_once(SAFI_MODELO_PATH. '/cuentaBanco.php');

if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}

class RendicionViaticoNacionalAccion extends Acciones
{	
	public function Bandeja(){
		$form = FormManager::GetForm(FORM_BANDEJA_RENDICON_VIATICO_NACIONAL);
		
		$login = $_SESSION['login'];
		$idPerfil = $_SESSION['user_perfil_id'];
		$idDependencia = $_SESSION['user_depe_id'];
		
		$estatusDevuelto = 7;
		$estatusEntransito = 10;
		
		// Bandeja principal
		$enBandeja = null;
		if(
			substr($idPerfil,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
			$idPerfil == PERFIL_ASISTENTE_EJECUTIVO ||
			$idPerfil == PERFIL_ASISTENTE_PRESIDENCIA
		){
			$enBandeja = SafiModeloRendicionViaticoNacional::GetRendicionEnBandeja(array(
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
			$enBandeja = SafiModeloRendicionViaticoNacional::GetRendicionEnBandeja(array(
				'idPerfilActual' => $idPerfil,
				'estatus' => array($estatusEntransito),
				'idDependencia' => $idDependencia 
			));
		} else if(
			$idPerfil == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS
		){
			$enBandeja = SafiModeloRendicionViaticoNacional::GetRendicionEnBandeja(array(
				'idPerfilActual' => $idPerfil,
				'estatus' => arraY($estatusEntransito) 
			));
		} else if(
			$idPerfil == PERFIL_JEFE_PRESUPUESTO
			|| $idPerfil == PERFIL_ANALISTA_PRESUPUESTO
		){
			$enBandeja = SafiModeloRendicionViaticoNacional::GetRendicionEnBandeja(array(
				'idPerfilActual' => PERFIL_JEFE_PRESUPUESTO,
				'estatus' => arraY($estatusEntransito) 
			));
		}
		
		if(is_array($enBandeja) && count($enBandeja)>0){
			
			$idsViaticos = array();
			
			foreach ($enBandeja AS $dataRendicion){
				$rendicion = $dataRendicion['ClassRendicionViaticoNacional'];
				$docGenera = $dataRendicion['ClassDocGenera'];
				
				$idsViaticos[] = $rendicion->GetIdViaticoNacional();

				/* para obtener los datos de el usuario que eleboró la rendición */
				$usuaLogins[] = $docGenera->GetUsuaLogin();
			}
			
			$viaticos = SafiModeloViaticoNacional::GetListViaticosNacionales(array('idsViaticos' => $idsViaticos));
			
			foreach ($enBandeja AS &$dataRendicion){
				$rendicion = $dataRendicion['ClassRendicionViaticoNacional'];
				$dataRendicion['ClassViaticoNacional'] = $viaticos[$rendicion->GetIdViaticoNacional()];
			}
			unset($dataRendicion);
			
			if(count($usuaLogins)>0){
				$usuaLogins = array_unique($usuaLogins);
				$empleadosElaboradoresEnBandejas = SafiModeloEmpleado::GetEmpleadosByUsuaLogins($usuaLogins);
				$GLOBALS['SafiRequestVars']['empleadosElaboradoresEnBandejas'] = $empleadosElaboradoresEnBandejas;
			}
		}
		
		$form->SetEnBandeja($enBandeja);
		
		// Fin Bandeja principal
		
		if(
			substr($idPerfil,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
			$idPerfil == PERFIL_ASISTENTE_EJECUTIVO ||
			$idPerfil == PERFIL_ASISTENTE_PRESIDENCIA
		){
			// Establecer la bandeja de viáticos por enviar
			$porEnviar = SafiModeloRendicionViaticoNacional::GetRendicionPorEnviar(array(
				"usuaLogin" => $login,
				"idPerfilActual" => $idPerfil
			));
			
			if(is_array($porEnviar) && count($porEnviar)>0){
				
				$idsViaticos = array();
				
				foreach ($porEnviar AS $dataRendicion){
					$rendicion = $dataRendicion['ClassRendicionViaticoNacional'];
					$idsViaticos[] = $rendicion->GetIdViaticoNacional(); 
				}
				
				$viaticos = SafiModeloViaticoNacional::GetListViaticosNacionales(array('idsViaticos' => $idsViaticos));
				
				foreach ($porEnviar AS &$dataRendicion){
					$rendicion = $dataRendicion['ClassRendicionViaticoNacional'];
					$dataRendicion['ClassViaticoNacional'] = $viaticos[$rendicion->GetIdViaticoNacional()];
				}
				unset($dataRendicion);
			}
			
			$form->SetPorEnviar($porEnviar);
			// Fin Establecer la bandeja de viáticos por enviar
			
		}
		/**************************************************
		 * Rendiciónes de viáticos nacionales en tránsito *
		 **************************************************/
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
			
			// Establecer la bandeja de viáticos en transito
			$enTransito = SafiModeloRendicionViaticoNacional::GetRendicionEnTransito($params);
			
			$cargoFundaciones = array();
			$idDependencias = array();
			$idsViaticos = array();
			$usuaLogins = array();
			
			foreach($enTransito as $dataRendicion){
				$rendicion = $dataRendicion['ClassRendicionViaticoNacional'];
				$docGenera = $dataRendicion['ClassDocGenera'];
				
				$idsViaticos[] = $rendicion->GetIdViaticoNacional();
				
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
			
			$viaticos = SafiModeloViaticoNacional::GetListViaticosNacionales(array('idsViaticos' => $idsViaticos));
				
			foreach ($enTransito AS &$dataRendicion){
				$rendicion = $dataRendicion['ClassRendicionViaticoNacional'];
				$dataRendicion['ClassViaticoNacional'] = $viaticos[$rendicion->GetIdViaticoNacional()];
			}
			unset($dataRendicion);
			
			if(count($cargoFundaciones)>=0){
				$cargoFundacionEnTransitos = SafiModeloCargo::GetCargoByCargoFundaciones($cargoFundaciones);
				$GLOBALS['SafiRequestVars']['cargoFundacionEnTransitos'] = $cargoFundacionEnTransitos;
			}
			
			if(count($idDependencias)>0){
				$dependenciaEnTransitos = SafiModeloDependencia::GetDependenciaByIds($idDependencias);
				$GLOBALS['SafiRequestVars']['dependenciaEnTransitos'] = $dependenciaEnTransitos;
			}
			
			if(count($usuaLogins)>0){
				$usuaLogins = array_unique($usuaLogins);
				$empleadosElaboradoresEnTransitos = SafiModeloEmpleado::GetEmpleadosByUsuaLogins($usuaLogins);
				$GLOBALS['SafiRequestVars']['empleadosElaboradoresEnTransitos'] = $empleadosElaboradoresEnTransitos;
			}
			
			$form->SetEnTransito($enTransito);
			// Fin Establecer la bandeja de viáticos en transito
		}
		
		
		require(SAFI_VISTA_PATH ."/vina/bandejaRendicionViaticoNacional.php");
	}
	
	public function BuscarViaticoNacional()
	{
		try
		{
			$form = FormManager::GetForm(FORM_NUEVA_RENDICION_VIATICO_NACIONAL);
			
			$idDependencia = $_SESSION['user_depe_id'];
			
			$GLOBALS['SafiErrors']['general'] = array();
			$GLOBALS['SafiInfo']['general'] = array();
			
			if(!isset($_POST['idViaticoBuscado']))
				throw new Exception("Identificador de vi&aacute;tico nacional no encontrado.
					Comuniquese con el administrador del sistema.");
			
			if(($idViaticoBuscado=trim($_POST['idViaticoBuscado'])) == '' || $idViaticoBuscado == "vnac-")
				throw new Exception("Debe indicar un vi&aacute;tico nacional.");
			
			if (strlen($idViaticoBuscado) <= 5)
				throw new Exception("El c&oacute;digo de vi&aacute;tico nacional es incorrecto.");
			
			$form->SetIdViaticoBuscado(trim($_POST['idViaticoBuscado']));
			
			// obtener el viático nacional
			$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($form->GetIdViaticoBuscado());
			
			if($viatico === null)
				throw new Exception("El vi&aacute;tico nacional \"".$form->GetIdViaticoBuscado()."\" es incorrecto.");
			
			if($viatico->GetDependenciaId() != $idDependencia)
				throw new Exception("El vi&aacute;tico nacional \"".$form->GetIdViaticoBuscado()."\" no pertenece a su dependencia.");
			
			$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($form->GetIdViaticoBuscado());
			if($docGenera == null)
				throw new Exception("El documento del vi&aacute;tico \"" . $form->GetIdViaticoBuscado() . "\" no pudo ser cargado.");
				
			if($docGenera->GetIdEstatus() == EntidadEstatus::ESTATUS_ANULADO
				&& $docGenera->GetIdWFObjeto() == EntidadWFObjeto::WFOBJETO_RECHAZADO
			)
				throw new Exception("El vi&aacute;tico \"" . $form->GetIdViaticoBuscado() . "\" est&aacute; anulado.");
			else if($docGenera->GetIdEstatus() != EntidadEstatus::ESTATUS_APROBADO
				&& $docGenera->GetIdWFObjeto() != EntidadWFObjeto::WFOBJETO_APROBADO
			)
				throw new Exception("El vi&aacute;tico \"" . $form->GetIdViaticoBuscado() . "\" aun no ha finalizado.");
			
			$idRendicion = GetIdRendicionByIdViatico($form->GetIdViaticoBuscado());
			
			if(($existenRendicion = SafiModeloRendicionViaticoNacional::ExistsRendicion($idRendicion)) === true)
				throw new Exception("El vi&aacute;tico nacional \"" . $form->GetIdViaticoBuscado() . "\" ya tiene ".
					"una rendici&oacute;n creada.");
			
			if($existenRendicion === null)
				throw new Exception("Error al intentar obtener el vi&aacute;tico nacional asociado.");
			
			// Obtener las asignaciones de viaticos
			$asignaciones = SafiModeloAsignacionViatico::GetAsignaciones();
			// Obtener los bancos activos de la fundación
			$bancos = SafiModeloBanco::GetAllBancosActivos();
			
			$totalGastos = $this->CalcularMontoTotalAsignaciones($viatico->GetViaticoResponsableAsignaciones());
			
			$banco = new EntidadBanco();
			$banco->SetId('003');
			
			$rendicion = $form->GetRendicionViaticoNacional();
			$rendicion->SetFechaInicioViaje($viatico->GetFechaInicioViaje());
			$rendicion->SetFechaFinViaje($viatico->GetFechaFinViaje());
			//$rendicion->SetObjetivosViaje($viatico->GetObjetivosViaje());
			
			// Comentar para producción
			/*
			$rendicion->SetReintegroBanco($banco);
			$rendicion->SetReintegroReferencia('1234');
			$rendicion->SetReintegroFecha('24/12/2010');
			$rendicion->SetObservaciones(utf8_decode('Observación de prueba'));
			*/
			// Fin Comentar para producción
			
			$rendicion->SetTotalGastos($totalGastos);
			
			$GLOBALS['SafiRequestVars']['asignaciones'] =  $asignaciones;
			$GLOBALS['SafiRequestVars']['bancos'] =  $bancos;
			
			$form->SetViatico($viatico);
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
		}
		require(SAFI_VISTA_PATH . "/vina/nuevaRendicion.php");
	} 
	
	public function Insertar()
	{
		$form = FormManager::GetForm(FORM_NUEVA_RENDICION_VIATICO_NACIONAL);
		$form->SetTipoOperacion(NuevaRendicionViaticoNacionalForm::TIPO_OPERACION_INSERTAR);
		
		require(SAFI_VISTA_PATH . "/vina/nuevaRendicion.php");
	}
	
	public function Guardar()
	{
		$idRendicion = $this->__Guardar();
		
		if($idRendicion !== false){
			$GLOBALS['SafiInfo']['general'][] = "Rendici&oacute;n de vi&aacute;tico nacional \"" . $idRendicion
				. "\" registrada satisfactoriamente.";
			$this->__VerDetalles(array('idRendicion' => $idRendicion));
		} else {
			$this->__DesplegarFormularioRendicion();
		}
	}
	
	private function __Guardar()
	{
		$form = FormManager::GetForm(FORM_NUEVA_RENDICION_VIATICO_NACIONAL);
		
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		
		$idDependencia = $_SESSION['user_depe_id'];
		$loginRegistrador = $_SESSION['login'];
		$perfilRegistrador = $_SESSION['user_perfil_id'];
		
		$form->GetRendicionViaticoNacional()->SetFechaRendicion(date('d/m/Y H:i:s'));
		
		// Obtener y validar el id del viático nacional
		$this->__ValidarIdVaiticoNacional($form);
		// Obtener y validar la fecha de la rendición
		//$this->__ValidarFechaRendicion($form);
		// Obtener y validar el archivo del informe de rendicion 
		$this->__ValidarInforme($form);
		// Obtener y validar la fecha de inicio del viaje
		$this->__ValidarFechaInicioViaje($form);
		// Obtener y validar la fecha de fin del viaje
		$this->__ValidarFechaFinViaje($form);
		// Obtener y validar los objetivos del viaje
		$this->__ValidarObjetivosViaje($form);
		// Obtener y validar el total de gastos del viaje
		$this->__ValidarTotalGastos($form);
		// Obtener y validar los datos de reintegro a la fundación, en caso de ser necesario
		$this->__ValidarDatosReintegro($form);
		// Obtener y validar el id del banco en caso de reintegro
		//$this->__ValidarReintegroBanco($form);
		// Obtener y validar la referencia en caso de reintegro
		//$this->__ValidarReintegroReferencia($form);
		// Obtener y validar la fecha en caso de reintegro
		//$this->__ValidarReintegroFecha($form);
		// Obtener y validar las observaciones de la rendición
		$this->__ValidarObservaciones($form);
		
		$form->SetIdViaticoBuscado($form->GetRendicionViaticoNacional()->GetIdViaticoNacional());
		$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($form->GetRendicionViaticoNacional()->GetIdViaticoNacional());
		
		if($viatico === null)
			$GLOBALS['SafiErrors']['general'][] = "El vi&aacute;tico nacional no puede ser encontrado.";
		
		$form->SetViatico($viatico);
			
		if(count($GLOBALS['SafiErrors']['general']) == 0)
		{
			$params = array();
			$rendicion = $form->GetRendicionViaticoNacional();
			
			// Calcular el monto total del viático
			$montoTotal = $this->CalcularMontoTotalAsignaciones($viatico->GetViaticoResponsableAsignaciones());
			
			$rendicion->SetMontoAnticipo($montoTotal);
			$rendicion->SetMontoReintegro($montoTotal - $form->GetRendicionViaticoNacional()->GetTotalGastos());
			
			$dependencia = new EntidadDependencia();
			$dependencia->SetId($idDependencia);
			$rendicion->SetDependencia($dependencia);
			
			$rendicion->SetUsuaLogin($loginRegistrador);
			
			$params['perfilRegistrador'] = $perfilRegistrador;
			
			$idRendicion = SafiModeloRendicionViaticoNacional::GuardarRendicion
				($form->GetRendicionViaticoNacional(), $params, $form->GetInformeTmpPath());
			
			if($idRendicion === false){
				$GLOBALS['SafiErrors']['general'][] = "La rendici&oacute;n de vi&aacute;tico nacional no pudo ser registrada. 
					Intente m&aacute;s tarde.";
			}
			return $idRendicion;
		}
		
		return false;
	}
	
	public function GuardarYEnviar()
	{
		
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general']  =array();
		
		$idRendicion = $this->__Guardar();
		
		if($idRendicion !== false){
			
			if($this->__Enviar($idRendicion) !== false)
			{
				$GLOBALS['SafiInfo']['general'][] = "Rendici&oacute;n de vi&aacute;tico nacional \"" . $idRendicion
					. "\" registrada y enviada satisfactoriamente.";
			} else {
				$GLOBALS['SafiInfo']['general'][] = "La Rendici&oacute;n de vi&aacute;tico nacional \"" . $idRendicion
					. "\" fue registrada satisfactoriamente, pero no se pudo enviar. Intente enviarla desde la bandeja.";
			}
			$this->__VerDetalles(array('idRendicion' => $idRendicion));
			return;
		} else {
			$this->__DesplegarFormularioRendicion();
			return;
		}
		
		$this->Bandeja();
	}
	
	public function Modificar()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		
		if(isset($_REQUEST['idRendicion']) && trim($_REQUEST['idRendicion']) != '')
		{
			$form = FormManager::GetForm(FORM_NUEVA_RENDICION_VIATICO_NACIONAL);
			$form->SetTipoOperacion(NuevaRendicionViaticoNacionalForm::TIPO_OPERACION_MODIFICAR);
			
			$rendicion = SafiModeloRendicionViaticoNacional::GetRendicionById(trim($_REQUEST['idRendicion']));
			$form->SetRendicionViaticoNacional($rendicion);
			
			if(!empty($rendicion)){
				$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($rendicion->GetIdViaticoNacional());
				$form->SetViatico($viatico);
				
				if(!empty($viatico)){
					// Obtener las asignaciones de viaticos
					$asignaciones = SafiModeloAsignacionViatico::GetAsignaciones();
					// Obtener los bancos activos de la fundación
					$bancos = SafiModeloBanco::GetAllBancosActivos();
					
					$GLOBALS['SafiRequestVars']['asignaciones'] =  $asignaciones;
					$GLOBALS['SafiRequestVars']['bancos'] =  $bancos;
				}
			}
			
			require(SAFI_VISTA_PATH . "/vina/nuevaRendicion.php");
			
		} else {
			$GLOBALS['SafiErrors']['general'][] = "Identidicador de la rendici&oacute;n de vi&aacute;tico nacional no encontrado.";
			require(SAFI_VISTA_PATH . "/desplegarmensajes.php");
		}
	}
	
	public function Actualizar()
	{
		$idRendicion = $this->__Actualizar();
		
		if($idRendicion !== false){
			$GLOBALS['SafiInfo']['general'][] = "Rendici&oacute;n de vi&aacute;tico nacional \"" . $idRendicion
				. "\" modificada satisfactoriamente.";
			$this->__VerDetalles(array('idRendicion' => $idRendicion));
		} else {
			$this->__DesplegarFormularioRendicion();
		}
	}
	
	private function __Actualizar()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		
		$form = FormManager::GetForm(FORM_NUEVA_RENDICION_VIATICO_NACIONAL);
		$form->SetTipoOperacion(NuevaRendicionViaticoNacionalForm::TIPO_OPERACION_MODIFICAR);
		
		// validar el identificador del viatico
		if(isset($_POST['idRendicion']) && trim($_POST['idRendicion'])!= '')
		{
			$form->GetRendicionViaticoNacional()->SetId(trim($_POST['idRendicion']));
			$rendicion = SafiModeloRendicionViaticoNacional::GetRendicionById($form->GetRendicionViaticoNacional()->GetId());
			
			if($rendicion != null){
				
				$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($rendicion->GetIdViaticoNacional());
				$form->SetViatico($viatico);
				
				if(!empty($viatico)){
					// Obtener las asignaciones de viaticos
					$asignaciones = SafiModeloAsignacionViatico::GetAsignaciones();
					// Obtener los bancos activos de la fundación
					$bancos = SafiModeloBanco::GetAllBancosActivos();
					
					$GLOBALS['SafiRequestVars']['asignaciones'] =  $asignaciones;
					$GLOBALS['SafiRequestVars']['bancos'] =  $bancos;
				}
			}
		} else {
			$GLOBALS['SafiErrors']['general'][] = "No se encontr&oacute; ning&uacute;n identificador para el vi&aacute;tico nacional.";
		}
		
		// Obtener y validar la fecha de la rendición
		//$this->__ValidarFechaRendicion($form);
		// Obtener y validar el archivo del informe de rendicion 
		//$this->__ValidarInforme($form);
		// Obtener y validar la fecha de inicio del viaje
		$this->__ValidarFechaInicioViaje($form);
		// Obtener y validar la fecha de fin del viaje
		$this->__ValidarFechaFinViaje($form);
		// Obtener y validar los objetivos del viaje
		$this->__ValidarObjetivosViaje($form);
		// Obtener y validar el total de gastos del viaje
		$this->__ValidarTotalGastos($form);
		// Obtener y validar los datos de reintegro a la fundación, en caso de ser necesario
		$this->__ValidarDatosReintegro($form);
		// Obtener y validar el id del banco en caso de reintegro
		//$this->__ValidarReintegroBanco($form);
		// Obtener y validar la referencia en caso de reintegro
		//$this->__ValidarReintegroReferencia($form);
		// Obtener y validar la fecha en caso de reintegro
		//$this->__ValidarReintegroFecha($form);
		// Obtener y validar las observaciones de la rendición
		$this->__ValidarObservaciones($form);
		
		if(count($GLOBALS['SafiErrors']['general']) == 0)
		{
			// Calcular el monto total del viático
			$montoTotal = $this->CalcularMontoTotalAsignaciones($viatico->GetViaticoResponsableAsignaciones());
			
			$rendicion->SetMontoAnticipo($montoTotal);
			
			$rendicionForm = $form->GetRendicionViaticoNacional();
			//$rendicion->SetFechaRendicion($rendicionForm->GetFechaRendicion());
			$rendicion->SetFechaInicioViaje($rendicionForm->GetFechaInicioViaje());
			$rendicion->SetFechaFinViaje($rendicionForm->GetFechaFinViaje());
			$rendicion->SetObjetivosViaje($rendicionForm->GetObjetivosViaje());
			$rendicion->SetMontoAnticipo($montoTotal);
			$rendicion->SetTotalGastos($rendicionForm->GetTotalGastos());
			$rendicion->SetMontoReintegro($montoTotal - $rendicionForm->GetTotalGastos());
			$rendicion->SetReintegroBanco($rendicionForm->GetReintegroBanco());
			$rendicion->SetReintegroReferencia($rendicionForm->GetReintegroReferencia());
			$rendicion->SetReintegroFecha($rendicionForm->GetReintegroFecha());
			$rendicion->SetObservaciones($rendicionForm->GetObservaciones());
			//private $_informeFileName;
			
			$resultado = SafiModeloRendicionViaticoNacional::ActualizarRendicion($rendicion);
			
			if($resultado === false){
				$GLOBALS['SafiErrors']['general'][] = "La rendici&oacuten de vi&aacute;tico nacional no pudo ser modificada.
					P&oacute;ngase en contacto con el administrador del sistema.";
			}
			
			return $resultado;
		} else {
			return false;
		}
		
	}
	
	public function ActualizarYEnviar()
	{
	 	$idRendicion = $this->__Actualizar();
		
		if($idRendicion !== false)
		{
			if($this->__Enviar($idRendicion) !== false){
				$GLOBALS['SafiInfo']['general'][] = "Rendici&oacute;n de vi&aacute;tico nacional \"".$idRendicion
					."\" modificada y enviada satisfactoriamente.";
			} else {
				$GLOBALS['SafiInfo']['general'][] = "La rendici&oacute;n de vi&aacute;tico nacional \"".$idRendicion
					."\" fue modificada satisfactoriamente, pero no se pudo enviar. Intente enviarla m&aacute;s tarde.";
			}
			$this->__VerDetalles(array('idRendicion' => $idRendicion));
		} else {
			$this->__DesplegarFormularioRendicion();
		}
	}
	
	public function VerDetalles(){
		
		if(isset($_REQUEST['idRendicion']) && trim($_REQUEST['idRendicion']) != ''){

			$this->__VerDetalles(array('idRendicion' => trim($_REQUEST['idRendicion'])));
		}
	}
	
	private function __VerDetalles($params)
	{
		$idRendicion = $params['idRendicion'];
		
		try {
			$form = FormManager::GetForm(FORM_NUEVA_RENDICION_VIATICO_NACIONAL);
			
			if($idRendicion == null && ($idRendicion=trim($idRendicion)) == '')
				throw new Exception("No se ha encontrado el identificador de la rendici&oacute;n");	
			
			$rendicion = SafiModeloRendicionViaticoNacional::GetRendicionById($idRendicion);
			if($rendicion == null) throw new Exception("No se ha podido cargar la rendici&oacute;n");
		
			$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($rendicion->GetIdViaticoNacional());
			if($viatico == null) throw new Exception("No se ha podido cargar el vi&aacute;tico asociado a la rendici&oacute;n");
			
			$asignaciones = SafiModeloAsignacionViatico::GetAsignaciones();
			
			$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idRendicion);
			if($docGenera == null) throw new Exception("Se ha producido un error al intentar ver los detalles de la rendici&oacute;n");
			
			// Validar el pago con cheque o con transferencia
			try
			{
				$compromiso = null;
				$solicitudPago = null;
				$cheque = null;
				$chequera = null;
				
				// Comprobar si el viático nacional de la rendición tiene un compromiso asociado y obtenerlo 
				$compromiso = SafiModeloCompromiso::GetCompromisoByIdDocumento($rendicion->GetIdViaticoNacional());
				if($compromiso === null)
					throw new Exception("No se ha encontrado un compromiso para el vi&aacute;tico nacional de esta rendici&oacute;n.");
				
				// Comprobar si el viático nacional de la rendición tiene una solicitud de pago asociada
				// (a través del compromiso encontrado) y obtenerla
				$findSolicitudPago = new EntidadSolicitudPago();
				$findSolicitudPago->SetIdCompromiso($compromiso->GetId());
				$findSolicitudPago->SetBeneficiarioCedulaRif($viatico->GetResponsable()->GetCedula());
				$solicitudPago = SafiModeloSolicitudPago::GetSolicitudPagoBy($findSolicitudPago);
				if($solicitudPago === null)
					throw new Exception("No se ha encontrado una solicitud de pago para el vi&aacute;tico nacional
						de esta rendici&oacute;n.");
				
				$exiteInformacionPago = false;
				
				$pagoTransferencia = SafiModeloPagoTransferencia::GetPagoTransferencia(
					array(
						'idDocumento' => $solicitudPago->GetId(),
						'noEnIdEstados' => array(2, 15)
					)
				);
				
				if(is_object($pagoTransferencia)){
					$exiteInformacionPago = true;
					
					$cuentaBanco = SafiModeloCuentaBanco::GetCuentaBancoById($pagoTransferencia->GetCuentaEmisor()->GetId());
					if(!is_object($cuentaBanco))
						throw new Exception("No se ha encontrado la cuenta bancaria de la transferencia para el vi&aacute;tico nacional
							de esta rendici&oacute;n.");
					
					if(is_object($cuentaBanco->GetBanco()) && ($idBanco=$cuentaBanco->GetBanco()->GetId()) !== null
						&& ($idBanco=trim($idBanco)) != ""
					){
						$banco = SafiModeloBanco::GetBancoById($idBanco);
						if(!is_object($banco))
							throw new Exception("No se ha encontrado un banco en la cuenta bancaria de la transferencia
								para el vi&aacute;tico nacional de esta rendici&oacute;n.");
					} else
						throw new Exception("No se encuentra disponible la informaci&oacuten del banco en la cuenta bancaria de la
							transferencia para el vi&aacute;tico nacional de esta rendici&oacute;n.");
				}
				else
				{
					// Comprobar si el viático nacional de la rendición tiene un cheque asociado
					// (a través de la solicitud de pago) y obtenerlo
					$cheque = SafiModeloCheque::GetChequeBy(array(
						'idDocumento' => $solicitudPago->GetId(),
						'idEstado' => 45
					));
					if(is_object($cheque)){
						$exiteInformacionPago = true;
						
						// Comprobar si el viático nacional de la rendición tiene una chequera asociada
						// (a través del cheque) y obtenerla
						$findChequera = new EntidadChequera();
						$findChequera->SetId($cheque->GetIdChequera());
						$chequera = SafiModeloChequera::GetChequeraBy($findChequera);
						if($chequera === null)
							throw new Exception("No se ha encontrado una chequera para el vi&aacute;tico nacional
								de esta rendici&oacute;n.");
					}
				}
				
				if(!$exiteInformacionPago)
					throw new Exception("No se ha encontrado la informaci&oacute;n de pago para el vi&aacute;tico
						nacional de esta rendici&oacute;n.");
					
			}
			catch (Exception $e)
			{
				$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
			}
			
			$form->SetRendicionViaticoNacional($rendicion);
			$form->SetViatico($viatico);
			$form->SetDocGenera($docGenera);
			
			// Para los documentos de soporte (Memos)
			$GLOBALS['SafiRequestVars']['memos'] = GetDocumentosSoportesMemos($idRendicion);
				
			// Para las revisiones del documento
			$GLOBALS['SafiRequestVars']['datosRevisionesDocumento'] = GetDatosRevisionesDocumento($idRendicion);
			
			$GLOBALS['SafiRequestVars']['asignaciones'] =  $asignaciones;
		
			require(SAFI_VISTA_PATH ."/vina/verDetallesRendicion.php");
			return;
		
		} catch (Exception $e) {
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
		}
		
		if(count($GLOBALS['SafiErrors']['general'])<=0){
			$GLOBALS['SafiErrors']['general'][] = "Se ha producido un error al intentar ver la rendici&oacute;n. ".
				"Comuniquese con el administrador del sistema.";
		}
		
		$this->Bandeja();
	}
	
	public function Enviar()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general']  =array();
		
		// Validar el id de la rendición de viático nacional
		if(!isset($_REQUEST['idRendicion']) || trim($_REQUEST['idRendicion']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Identificador de la rendici&oacute;n de vi&aacute;tico nacional no encontrado.";
		} else {
			$idRendicion = trim($_REQUEST['idRendicion']);
			
			if(($result=$this->__Enviar($idRendicion)) !== false)
			{
				if(
					substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
					$_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO ||
					$_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA
				){
					$GLOBALS['SafiInfo']['general'][] = "Rendici&oacute;n de vi&aacute;tico nacional \"".$idRendicion.
						"\" enviada satisfactoriamente.";
				} else {
					$GLOBALS['SafiInfo']['general'][] = "Rendici&oacute;n de vi&aacute;tico nacional \"".$idRendicion.
						"\" aprobada satisfactoriamente.";
				}
			}
		}
		
		$this->Bandeja();
	}
	
	private function __Enviar($idRendicion)
	{
		$enviado = false;
		
		$idDependencia = $_SESSION['user_depe_id'];
		$idPerfil = $_SESSION['user_perfil_id'];
		$loginUsuario = $_SESSION['login'] ;
		
		// Identificar el perfil que desea hacer el envío
		if(	
			substr($idPerfil,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
			substr($idPerfil,0,2)."000" == PERFIL_DIRECTOR ||
			substr($idPerfil,0,2)."000" == PERFIL_GERENTE ||
			$idPerfil == PERFIL_JEFE_PRESUPUESTO ||
			$idPerfil == PERFIL_ASISTENTE_EJECUTIVO ||
			$idPerfil == PERFIL_DIRECTOR_EJECUTIVO ||
			$idPerfil == PERFIL_ASISTENTE_PRESIDENCIA ||
			$idPerfil == PERFIL_PRESIDENTE
		){
			$wFCadena = SafiModeloWFCadena::GetWFNextCadenaByIdDocument($idRendicion);
			
			if($wFCadena == null){
				$GLOBALS['SafiErrors']['general'][] = "Error al enviar. WFCadena inicial no encontrada.";
			} else if($wFCadena->GetWFCadenaHijo() == null){
				$GLOBALS['SafiErrors']['general'][] = "Error al enviar. WFCadena hija no encontrada.";
			// Obtener la cadena siguiente, a la inicial, de vaiticos nacionales
			} else {
				$rendicion = SafiModeloRendicionViaticoNacional::GetRendicionById($idRendicion);
				if($rendicion == null) throw new Exception("No se ha podido cargar la rendici&oacute;n");
				
				$finalizarPorNoReintegro = false;
				// Validar que no exista reintegro en el viático, para que el gerente lo pueda finalizar
				if(
					substr($idPerfil,0,2)."000" == PERFIL_DIRECTOR ||
					substr($idPerfil,0,2)."000" == PERFIL_GERENTE
				)
				{
					$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($rendicion->GetIdViaticoNacional());
					if($viatico == null) throw new Exception("No se ha podido cargar el vi&aacute;tico asociado a la rendici&oacute;n");
					
					$montoTotal = CalcularMontoTotalAsignacionesViaticoNacional($viatico->GetViaticoResponsableAsignaciones());
					
					$diferencia = $montoTotal - $rendicion->GetTotalGastos();
					
					if($diferencia < 0.000001)
					{
						$finalizarPorNoReintegro = true;
					}
				}
				
				// 0 = Documento finalizado
				if(
					strcmp($wFCadena->GetWFCadenaHijo()->GetId(), "0") == 0 ||
					// Finalizar si está en presupuesto y la gerencia es la oficina de gestión administrativa y financiera
					($idPerfil == PERFIL_JEFE_PRESUPUESTO && $rendicion->GetDependencia()->GetId() == "450") ||
					// Finaliza el gerente del área por no reintegro
					$finalizarPorNoReintegro === true
				){
					// Obtener una instancia de docgenera para el viatico nacional a enviar (actualizar)
					$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idRendicion);
					
					$estadoAprobado = 13;
					
					$docGenera->SetIdWFObjeto(99);
					$docGenera->SetIdWFCadena(0);
					$docGenera->SetIdEstatus($estadoAprobado);
					$docGenera->SetIdPerfilActual(null);
					
					$Revisiones = new EntidadRevisionesDoc();
						
					$Revisiones->SetIdDocumento($idRendicion);
					$Revisiones->SetLoginUsuario($loginUsuario);
					$Revisiones->SetIdPerfil($idPerfil);
					$Revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
					$Revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
					
					// Guardar el registro del documento en docGenera (estado de la cadena)
					if(($enviado = SafiModeloDocGenera::EnviarDocumento($docGenera, $Revisiones)) === false){
						$GLOBALS['SafiErrors']['general'][] = "Error al enviar. No se pudo actualizar docGenera o revisionesDoc.";
					}
					
				} else if (($wFCadenaHijo = SafiModeloWFCadena::GetWFCadena($wFCadena->GetWFCadenaHijo())) == null){
					$GLOBALS['SafiErrors']['general'][] = "Error al enviar. WFCadena hija no encontrada.";
				} else if($wFCadenaHijo->GetWFGrupo() == null) {
					$GLOBALS['SafiErrors']['general'][] = "Error al enviar. WFGrupo de WFCadena hija no encontrado.";
				} else if(($perfilActual = SafiModeloDependenciaCargo::
					GetSiguienteCargoDeCadena($idDependencia, $wFCadenaHijo->GetWFGrupo()->GetPerfiles())) == null)
				{
					$GLOBALS['SafiErrors']['general'][] = 
						"Error al enviar. No se puede encontrar el perfil de la siguiente instancia en la cadena.";
				} else {
					
					// Obtener una instancia de docgenera para el viatico nacional a enviar (actualizar)
					$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idRendicion);
					
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
						
						$Revisiones->SetIdDocumento($idRendicion);
						$Revisiones->SetLoginUsuario($loginUsuario);
						$Revisiones->SetIdPerfil($idPerfil);
						$Revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
						$Revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
					}
					
					// Guardar el registro del documento en docGenera (estado de la cadena)
					if(($enviado = SafiModeloDocGenera::EnviarDocumento($docGenera, $Revisiones)) === false){
						$GLOBALS['SafiErrors']['general'][] = "Error al enviar.";
					}
				}
			}
		}
		
		return $enviado;
	}
	
	public function  Devolver()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general']  = array();
		
		$idDependencia = $_SESSION['user_depe_id'];
		$idPerfil = $_SESSION['user_perfil_id'];
		$loginUsuario = $_SESSION['login'];
		$opcionDevolver = 5;
		$fechaHoy = date("d/m/Y H:i:s");
		
		if(!isset($_REQUEST['idRendicion']) || trim($_REQUEST['idRendicion']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Error al devolver. Identificador de la rendici&oacute; no encontrado.";
		} else if(!isset($_REQUEST['memoContent']) || trim($_REQUEST['memoContent']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Error al devolver. Motivo de la devoluci&oacute;n no encontrado.";
		} else {
			$idRendicion = trim($_REQUEST['idRendicion']);
			$memoContent = trim($_REQUEST['memoContent']);
			
			if(	$idPerfil == PERFIL_JEFE_PRESUPUESTO ||
				$idPerfil == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS
			){
				$documento = new EntidadDocumento();
				$documento->SetId(GetConfig("preCodigoRendicionViaticoNacional"));
				
				$wFOpcion = new EntidadWFOpcion();
				$wFOpcion->SetId($opcionDevolver);
				
				$wFGrupo = SafiModeloWFGrupo::GetWFGrupoByIdPerfil($idPerfil);
				
				if ($wFGrupo == null){
					$GLOBALS['SafiErrors']['general'][] = "Error al devolver. WFGrupo no encontrado para el perfil actual.";
				} else
				{
					$rendicion = SafiModeloRendicionViaticoNacional::GetRendicionById($idRendicion);
					
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
					
					$wFCadena = SafiModeloWFCadena::GetWFCadena($findWFCadena);
					
					
					if($wFCadena == null){
						$GLOBALS['SafiErrors']['general'][] = "Error al devolver. WFCadena inicial no encontrada.";
					} else {
						// Obtener una instancia de docgenera para el viatico nacional a enviar (actualizar)
						$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idRendicion);
						
						$perfilActual = $docGenera->GetIdPerfil();
						$estadoDevuelto = 7;
						
						$docGenera->SetIdWFObjeto($wFCadena->GetWFObjetoInicial()->GetId());
						$docGenera->SetIdWFCadena($wFCadena->GetId());
						$docGenera->SetIdEstatus($estadoDevuelto);
						$docGenera->SetIdPerfilActual($perfilActual);
						
						$memo = new EntidadMemo();
						$memo->SetLoginUsuario($loginUsuario);
						$memo->SetAsunto(utf8_decode('Devolución de Rendición de Viático Nacional'));
						$memo->SetContenido($memoContent);
						$memo->SetIdDependencia($idDependencia);
						$memo->SetFechaCreacion($fechaHoy);
						
						$revisiones = new EntidadRevisionesDoc();
						$revisiones->SetIdDocumento($idRendicion);
						$revisiones->SetLoginUsuario($loginUsuario);
						$revisiones->SetIdPerfil($idPerfil);
						$revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
						$revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
						
						if(SafiModeloDocGenera::DevolverDocumento($docGenera, $memo, $revisiones) === false){
							$GLOBALS['SafiErrors']['general'][] = 
								"Error al devolver la rendici&oacute;n de vi&aacute;tico nacional.";
						} else {
							$GLOBALS['SafiInfo']['general'][] = 
								"Rendici&oacute;n de vi&aacute;tico nacional \"" . $idRendicion . "\" devuelta satisfactoriamente.";
						}
					}
				}
			} // if(	$_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO ||
			  //		$_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS
			else if(	
				substr($idPerfil,0,2)."000" == PERFIL_DIRECTOR ||
				substr($idPerfil,0,2)."000" == PERFIL_GERENTE ||
				$idPerfil == PERFIL_DIRECTOR_EJECUTIVO ||
				$idPerfil == PERFIL_PRESIDENTE
			){
				$documento = new EntidadDocumento();
				$documento->SetId(GetConfig("preCodigoRendicionViaticoNacional"));
				
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
				
				if ($wFGrupo == null){
					$GLOBALS['SafiErrors']['general'][] = "Error al devolver. WFGrupo no encontrado para el perfil actual.";
				} else 
				{
					$rendicion = SafiModeloRendicionViaticoNacional::GetRendicionById($idRendicion);
					
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
					
					$wFCadena = SafiModeloWFCadena::GetWFCadena($findWFCadena);
					
					if($wFCadena == null){
						$GLOBALS['SafiErrors']['general'][] = "Error al devolver. WFCadena inicial no encontrada.";
					} else if($wFCadena->GetWFCadenaHijo() == null){
						$GLOBALS['SafiErrors']['general'][] = "Error al devolver. Detalles: WFCadena hija no encontrada.";
					// Obtener la cadena siguiente, a la inicial, de rendición de viáticos nacionales
					} else if (($wFCadenaHijo = SafiModeloWFCadena::GetWFCadena($wFCadena->GetWFCadenaHijo())) == null){
						$GLOBALS['SafiErrors']['general'][] = "Error al devolver. Detalles: WFCadena hija no encontrada.";
					} else if($wFCadenaHijo->GetWFGrupo() == null) {
						$GLOBALS['SafiErrors']['general'][] = "Error al devolver. Detalles: WFGrupo de WFCadena hija no encontrado.";
					} else if(($perfilActual = SafiModeloDependenciaCargo::
						GetSiguienteCargoDeCadena($idDependencia, $wFCadenaHijo->GetWFGrupo()->GetPerfiles())) == null)
					{
						$GLOBALS['SafiErrors']['general'][] = 
							"Error al devolver. Detalles: No se puede encontrar el perfil de la siguiente instancia en la cadena.";
					} else {
						// Obtener una instancia de docgenera para la rendición de viatico nacional a devolver (actualizar)
						$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idRendicion);
						
						$estadoDevuelto = 7;
						
						$docGenera->SetIdWFObjeto($wFCadena->GetWFObjetoInicial()->GetId());
						$docGenera->SetIdWFCadena($wFCadena->GetId());
						$docGenera->SetIdEstatus($estadoDevuelto);
						$docGenera->SetIdPerfilActual($perfilActual->GetId());
						
						$memo = new EntidadMemo();
						$memo->SetLoginUsuario($loginUsuario);
						$memo->SetAsunto(utf8_decode('Devolución de Rendición de Viático Nacional'));
						$memo->SetContenido($memoContent);
						$memo->SetIdDependencia($idDependencia);
						$memo->SetFechaCreacion($fechaHoy);
						
						$revisiones = new EntidadRevisionesDoc();
						$revisiones->SetIdDocumento($idRendicion);
						$revisiones->SetLoginUsuario($loginUsuario);
						$revisiones->SetIdPerfil($idPerfil);
						$revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
						$revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
						
						
						if(SafiModeloDocGenera::DevolverDocumento($docGenera, $memo, $revisiones) === false){
							$GLOBALS['SafiErrors']['general'][] = 
								"Error al devolver la rendici&oacute;n de vi&aacute;tico nacional.";
						} else {
							$GLOBALS['SafiInfo']['general'][] = 
								"Rendici&oacute;n de vi&aacute;tico nacional \"" . $idRendicion . "\" devuelta satisfactoriamente.";
						}
					}
				}
			} // fin de else if(	
			  //				substr($idPerfil,0,2)."000" == PERFIL_DIRECTOR ||
			  //				substr($idPerfil,0,2)."000" == PERFIL_GERENTE ||
			  //				$idPerfil == PERFIL_DIRECTOR_EJECUTIVO ||
			  //				$idPerfil == PERFIL_PRESIDENTE
		}
		$this->Bandeja();
	}
	
	public function Anular()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		
		$idPerfil = $_SESSION['user_perfil_id'];
		$loginUsuario = $_SESSION['login'] ;
		
		// Validar el id del viatico nacional
		if(!isset($_REQUEST['idRendicion']) || ($idRendicion=trim($_REQUEST['idRendicion'])) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Identificador de la  rendici&oacute;n vi&aacute;tico nacional no encontrado.";
		} else {
			// Identificar el perfil que desea hacer la anulación
			if(	
				substr($idPerfil,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
				$idPerfil == PERFIL_ASISTENTE_EJECUTIVO ||
				$idPerfil == PERFIL_ASISTENTE_PRESIDENCIA
			){
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
				$revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
				$revisiones->SetIdWFOpcion($wFOpcionAnular);
				
				if(SafiModeloDocGenera::AnularDocumento($docGenera, $revisiones) === false){
					$GLOBALS['SafiErrors']['general'][] = "Error al anular.";
				} else {
					$GLOBALS['SafiInfo']['general'][] = "Rendici&oacute;n de vi&aacute;tico nacional \"".$idRendicion.
						"\" anulada satisfactoriamente.";
				}
			}
		}
		$this->Bandeja();
	}
	
	public function BuscarRendicion()
	{
		
		$form = FormManager::GetForm(FORM_BUSCAR_RENDICON_VIATICO_NACIONAL);
		
		$idDependencia = $_SESSION['user_depe_id'];
		$idUserPerfil = $_SESSION['user_perfil_id'];
		
		// Validar la fecha de inicio
		if(!isset($_POST['fechaInicio']) || trim($_POST['fechaInicio']) == ''){
			//$GLOBALS['SafiErrors']['general'][] = 'Debe indicar la fecha de inicio del viaje';
		} else {
			$fecha = explode('/', $_POST['fechaInicio']);
			if (count($fecha) != 3){
				$GLOBALS['SafiErrors']['general'][] = "Fecha inicio inv&aacute;lida.";
			} else {
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				if(!checkdate ($month ,$day ,$year)){
					$GLOBALS['SafiErrors']['general'][] = "Fecha inicio inv&aacute;lida.";
				} else {
					$form->SetFechaInicio($day . '/' . $month . '/' . $year);
				}
			}
		}
		
		// Validar la fecha de fin
		if(!isset($_POST['fechaFin']) || trim($_POST['fechaFin']) == ''){
			//$GLOBALS['SafiErrors']['general'][] = 'Debe indicar la fecha de inicio del viaje';
		} else {
			$fecha = explode('/', $_POST['fechaFin']);
			if (count($fecha) != 3){
				$GLOBALS['SafiErrors']['general'][] = "Fecha fin inv&aacute;lida.";
			} else {
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				if(!checkdate ($month ,$day ,$year)){
					$GLOBALS['SafiErrors']['general'][] = "Fecha fin inv&aacute;lida.";
				} else {
					$form->SetFechaFin($day . '/' . $month . '/' . $year);
				}
			}
		}
	
		// Validar el id del viatico nacional
		if(
			!isset($_POST['idRendicion'])
			|| ($idRendicion=trim($_POST['idRendicion'])) == ''
			|| strlen($idRendicion) < 5
			|| $idRendicion == GetConfig("preCodigoRendicionViaticoNacional").GetConfig("delimitadorPreCodigoDocumento")
		){
			$idRendicion = null;
		} else {
			$form->SetIdRendicion($idRendicion);
		}
		
		$params = array();
		
		$params['fechaInicio'] = $form->GetFechaInicio();
		$params['fechaFin'] = $form->GetFechaFin();
		$params['idRendicion'] = $idRendicion;
		
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
					$params['idDependencia'] = $idDependencia;
				}
			} else {
				$params['idDependencia'] = $idDependencia;
			}
		}
		
		if(
			($form->GetFechaFin()!=null && $form->GetFechaFin()!='') ||
			($form->GetFechaInicio()!=null && $form->GetFechaInicio()!='') ||
			($idRendicion!=null && $form->GetIdRendicion())
		){
			$dataRendiciones = SafiModeloRendicionViaticoNacional::BuscarRendicion($params);
			
			$idsViaticos = array();
			$cargoFundaciones = array();
			$idDependencias = array();
			$usuaLogins = array();
			
			if($dataRendiciones !== false){
			
				foreach($dataRendiciones as $dataRendicion){
					$rendicion = $dataRendicion['ClassRendicionViaticoNacional'];
					$docGenera = $dataRendicion['ClassDocGenera'];
					
					$idsViaticos[] = $rendicion->GetIdViaticoNacional();
					
					/* Para obtener los datos de la instancia actual */
					$idPerfilActual = $docGenera->GetIdPerfilActual();
					if($idPerfilActual != null && $idPerfilActual != '')
					{
						$cargoFundacion = GetCargoFundacionFromIdPerfil($idPerfilActual);
						$cargoFundaciones[$cargoFundacion] = $cargoFundacion;
						
						$idDependencia = GetIdDependenciaFromIdPerfil($idPerfilActual);
						$idDependencias[$idDependencia] = $idDependencia; 
					}
					
					/* para obtener los datos de el usuario que eleboró la rendición*/
					$usuaLogins[] = $docGenera->GetUsuaLogin();
				}
				
				$viaticos = SafiModeloViaticoNacional::GetListViaticosNacionales(array('idsViaticos' => $idsViaticos));
					
				foreach ($dataRendiciones AS &$dataRendicion){
					$rendicion = $dataRendicion['ClassRendicionViaticoNacional'];
					$dataRendicion['ClassViaticoNacional'] = $viaticos[$rendicion->GetIdViaticoNacional()];
				}
				unset($dataRendicion);
				
				if(count($cargoFundaciones)>=0){
					$cargoFundacionInstanciaActuales = SafiModeloCargo::GetCargoByCargoFundaciones($cargoFundaciones);
					$GLOBALS['SafiRequestVars']['cargoFundacionInstanciaActuales'] = $cargoFundacionInstanciaActuales;
				}
				
				if(count($idDependencias)>0){
					$dependenciaInstanciaActuales = SafiModeloDependencia::GetDependenciaByIds($idDependencias);
					$GLOBALS['SafiRequestVars']['dependenciaInstanciaActuales'] = $dependenciaInstanciaActuales;
				}
				
				if(count($usuaLogins)>0){
					$usuaLogins = array_unique($usuaLogins);
					$empleadosElaboradores = SafiModeloEmpleado::GetEmpleadosByUsuaLogins($usuaLogins);
					$GLOBALS['SafiRequestVars']['empleadosElaboradores'] = $empleadosElaboradores;
				}
				
				$estatusList = SafiModeloEstatus::GetAllEstatus();
				$GLOBALS['SafiRequestVars']['estatusList'] = $estatusList;
			}
			
			$form->SetDataRendiciones($dataRendiciones);
		}
		
		require(SAFI_VISTA_PATH ."/vina/buscarRendicion.php");
	}
	
	public function GenerarPDF()
	{
		try {
			
			if(!isset($_REQUEST['idRendicion']) || ($idRendicion=trim($_REQUEST['idRendicion'])) == '')
				throw new Exception("No se ha encontrado ning&uacute;n  identificador para la rendici&oacute;n");
			
			if (!isset($_REQUEST['tipo']) || ($tipo=trim($_REQUEST['tipo'])) == '')
				throw new Exception("No se ha encontrado el tipo de documento a imprimir (Lineal o firmas por p&aacute;ginas)");
			
			$rendicion = SafiModeloRendicionViaticoNacional::GetRendicionById($idRendicion);
			
			$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($rendicion->GetId());
			
			$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($rendicion->GetIdViaticoNacional());
			
			// Obtener las asignaciones de viaticos
			$asignaciones = SafiModeloAsignacionViatico::GetAsignaciones();
			
			$elaboradoPor = SafiModeloEmpleado::GetEmpleadoByCedula($rendicion->GetUsuaLogin());
			
			// Cargo de director o gerente
			$cargosGerenteDirector = GetPerfilCargosGerenteDirectorByIdUserPerfil($docGenera->GetIdPerfil());
			
			$cargoGerenteDirector = SafiModeloDependenciaCargo::GetSiguienteCargoDeCadena
				($rendicion->GetDependencia()->GetId(), $cargosGerenteDirector);
			
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
			
			$compromiso = null;
			$solicitudPago = null;
			$cheque = null;
			$chequera = null;
			$bancoTransferencia = null;
			
			// Comprobar si el viático nacional de la rendición tiene un compromiso asociado y obtenerlo 
			$compromiso = SafiModeloCompromiso::GetCompromisoByIdDocumento($rendicion->GetIdViaticoNacional());
			
			if($compromiso != null){
				// Comprobar si el viático nacional de la rendición tiene una solicitud de pago asociada
				// (a través del compromiso encontrado) y obtenerla
				$findSolicitudPago = new EntidadSolicitudPago();
				$findSolicitudPago->SetIdCompromiso($compromiso->GetId());
				$findSolicitudPago->SetBeneficiarioCedulaRif($viatico->GetResponsable()->GetCedula());
				$solicitudPago = SafiModeloSolicitudPago::GetSolicitudPagoBy($findSolicitudPago);
				
				if($solicitudPago != null)
				{
					$paramsPagoTransferencia = array(
						'idDocumento' => $solicitudPago->GetId(),
						'noEnIdEstados' => array(2, 15)
					);
					$pagoTransferencia = SafiModeloPagoTransferencia::GetPagoTransferencia($paramsPagoTransferencia);
					
					if(is_object($pagoTransferencia))
					{
						$cuentaBanco = SafiModeloCuentaBanco::GetCuentaBancoById($pagoTransferencia->GetCuentaEmisor()->GetId());
						
						if(
							is_object($cuentaBanco) && is_object($cuentaBanco->GetBanco())
							&& ($idBanco=$cuentaBanco->GetBanco()->GetId()) !== null && ($idBanco=trim($idBanco)) != ""
						){
							$bancoTransferencia = SafiModeloBanco::GetBancoById($idBanco);
						}
					} else {
						// Comprobar si el viático nacional de la rendición tiene un cheque asociado
						// (a través de la solicitud de pago) y obtenerlo
						$cheque = SafiModeloCheque::GetChequeBy(array(
							'idDocumento' => $solicitudPago->GetId(),
							'idEstado' => 45
						));
						if(is_object($cheque)){
							// Comprobar si el viático nacional de la rendición tiene una chequera asociada
							// (a través del cheque) y obtenerla
							$findChequera = new EntidadChequera();
							$findChequera->SetId($cheque->GetIdChequera());
							$chequera = SafiModeloChequera::GetChequeraBy($findChequera);
						}
					}
				}
			}
			
			/*
			Encontar viaticon que tienen:
				compromiso
				solicitud de pago
				cheque
			
			SELECT
				viatico.id AS viatico_id,
				compromiso.comp_id AS compromiso,
				solicitud_pago.sopg_id AS solicitud_pago,
				solicitud_pago.sopg_bene_ci_rif AS solicitud_pago_beneficiario,
				cheque.id_cheque AS cheque
			FROM
				safi_viatico viatico
				INNER JOIN sai_comp compromiso ON (compromiso.comp_documento = viatico.id)
				INNER JOIN sai_sol_pago solicitud_pago ON (solicitud_pago.comp_id = compromiso.comp_id)
				INNER JOIN sai_cheque cheque ON (cheque.docg_id = solicitud_pago.sopg_id)
			WHERE
				viatico.id like 'vnac-650%'
			
			ORDER BY
				viatico.fecha_viatico
			LIMIT
				10
				*/
			
			$GLOBALS['SafiRequestVars']['arrFirmas'] = $arrFirmas;
			$GLOBALS['SafiRequestVars']['perfilGerenteDirector'] = $perfilGerenteDirector;
			$GLOBALS['SafiRequestVars']['tipo'] = $tipo;
			$GLOBALS['SafiRequestVars']['rendicion'] = $rendicion;
			$GLOBALS['SafiRequestVars']['viatico'] = $viatico;
			$GLOBALS['SafiRequestVars']['docGenera'] = $docGenera;
			$GLOBALS['SafiRequestVars']['asignaciones'] =  $asignaciones;
			$GLOBALS['SafiRequestVars']['elaboradoPor'] = $elaboradoPor;
			$GLOBALS['SafiRequestVars']['compromiso'] = $compromiso;
			$GLOBALS['SafiRequestVars']['solicitudPago'] = $solicitudPago;
			$GLOBALS['SafiRequestVars']['pagoTransferencia'] = $pagoTransferencia;
			$GLOBALS['SafiRequestVars']['bancoTransferencia'] = $bancoTransferencia;
			$GLOBALS['SafiRequestVars']['cheque'] = $cheque;
			$GLOBALS['SafiRequestVars']['chequera'] = $chequera;
				
			require(SAFI_VISTA_PATH . "/vina/rendicionViaticoNacional_PDF.php");
			return;
			
		} catch (Exception $e) {
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
		}
		
		if(count($GLOBALS['SafiErrors']['general'])<=0){
			$GLOBALS['SafiErrors']['general'][] = "Se ha producido un error al intentar imprimir la rendici&oacute;n. ".
				 "Comuniquese con el administrador del sistema.";
		}
		
		if(!empty($rendicion))
			$this->__VerDetalles(array('idRendicion' => $rendicion->GetId()));
		else
			$this->Bandeja();
		
		return;
	}
	
	// Desplegar las vista del fomulario
	private function __DesplegarFormularioRendicion()
	{
		$form = FormManager::GetForm(FORM_NUEVA_RENDICION_VIATICO_NACIONAL);
		
		if(($viatico=$form->GetViatico()) !== null){
			// Obtener las asignaciones de viaticos
			$asignaciones = SafiModeloAsignacionViatico::GetAsignaciones();
			// Obtener los bancos activos de la fundación
			$bancos = SafiModeloBanco::GetAllBancosActivos();
			
			$GLOBALS['SafiRequestVars']['asignaciones'] =  $asignaciones;
			$GLOBALS['SafiRequestVars']['bancos'] =  $bancos;
		}
		
		require(SAFI_VISTA_PATH . "/vina/nuevaRendicion.php");
	}
	
	// Obtener y validar el id del viático nacional
	private function __ValidarIdVaiticoNacional($form){
		if(!isset($_POST['idViaticoNacional']) || trim($_POST['idViaticoNacional']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Identificador del vi&aacute;tico nacional no encontrado.";
		} else {
			$idViatico=trim($_POST['idViaticoNacional']);
			if(
				strlen($idViatico)<11 || 
				strpos($idViatico, "vnac-") !== 0
			){
				$GLOBALS['SafiErrors']['general'][] = "Identificador del vi&aacute;tico nacional inv&aacute;lido.";
			} else {
				$form->GetRendicionViaticoNacional()->SetIdViaticoNacional($idViatico);
			}
		}
	}
	
	// Obtener y validar la fecha de la rendición
	private function __ValidarFechaRendicion(NuevaRendicionViaticoNacionalForm $form){	
		if(!isset($_POST['fechaRendicion']) || trim($_POST['fechaRendicion']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Debe indicar la fecha de la rendici&oacute;n.";
		} else {
			if(($fecha = $this->__ValidarFecha($_POST['fechaRendicion'])) !== false){
				$form->GetRendicionViaticoNacional()->SetFechaRendicion($fecha);
			} else {
				$GLOBALS['SafiErrors']['general'][] = "Fecha de la rendici&oacute;n inv&aacute;lida.";
			}
		}
	}
	
	// Obtener y validar el archivo del informe de rendicion 
	private function __ValidarInforme($form){
		if(
			$form->GetRendicionViaticoNacional()->GetIdViaticoNacional() != null &&
			trim($_FILES['informe']['name']) != ''
		){
			if($_FILES['informe']['error'] != UPLOAD_ERR_OK ){
				$GLOBALS['SafiErrors']['general'][] = "Error al cargar el archivo del informe.";
			} else {
				$extensiones = explode(".", $_FILES['informe']['name']);
				array_shift($extensiones);
				$idRendicion = GetIdRendicionByIdViatico($form->GetRendicionViaticoNacional()->GetIdViaticoNacional());				
				$fileName = $idRendicion . ((count($extensiones)>0) ? "." . implode(".", $extensiones) : "");

				if(move_uploaded_file($_FILES['informe']['tmp_name'], SAFI_TMP_PATH . "/" . $fileName) === true){
					$form->SetInformeTmpPath(SAFI_TMP_PATH . "/" . $fileName);
					$form->GetRendicionViaticoNacional()->SetInformeFileName($fileName);
					// Borrar
					chmod(SAFI_TMP_PATH . "/" . $fileName, 777);
				} else {
					$GLOBALS['SafiErrors']['general'][] = "No se pudo copiar el archivo del informe desde la ubicaci&oacute;n de carga.";
				}
			}
		}
	}
	
	// Obtener y validar la fecha de inicio del viaje
	private function __ValidarFechaInicioViaje(NuevaRendicionViaticoNacionalForm $form){
		if(!isset($_POST['fechaInicioViaje']) || trim($_POST['fechaInicioViaje']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Debe indicar la fecha inicio del viaje.";
		} else {
			if(($fecha = $this->__ValidarFecha($_POST['fechaInicioViaje'])) !== false){
				$form->GetRendicionViaticoNacional()->SetFechaInicioViaje($fecha);
			} else {
				$GLOBALS['SafiErrors']['general'][] = "Fecha inicio del viaje inv&aacute;lida.";
			}
		}
	}
	
	// Obtener y validar la fecha de fin del viaje
	private function __ValidarFechaFinViaje(NuevaRendicionViaticoNacionalForm $form){
		if(!isset($_POST['fechaFinViaje']) || trim($_POST['fechaFinViaje']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Debe indicar la fecha fin del viaje.";
		} else {
			if(($fecha = $this->__ValidarFecha($_POST['fechaFinViaje'])) !== false){
				$form->GetRendicionViaticoNacional()->SetFechaFinViaje($fecha);
			} else {
				$GLOBALS['SafiErrors']['general'][] = "Fecha fin del viaje inv&aacute;lida.";
			}
		}
	}
	
	// Obtener y validar los objetivos del viaje
	private function __ValidarObjetivosViaje(NuevaRendicionViaticoNacionalForm $form){
		if(!isset($_POST['objetivosViaje']) || trim($_POST['objetivosViaje']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Debe indicar los logros alcanzados.";
		} else {
			$form->GetRendicionViaticoNacional()->SetObjetivosViaje(trim($_POST['objetivosViaje']));
		}
	}
	
	// Obtener y validar el total de gastos del viaje
	private function __ValidarTotalGastos(NuevaRendicionViaticoNacionalForm $form)
	{
		if(!isset($_POST['totalGastos']) || trim($_POST['totalGastos']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Debe indicar el total de gastos.";
		} else {
			$form->GetRendicionViaticoNacional()->SetTotalGastos(trim($_POST['totalGastos']));
		}
	}
	
	// Obtener y validar los datos de reintegro a la fundación, en caso de ser necesario
	private function  __ValidarDatosReintegro(NuevaRendicionViaticoNacionalForm $form)
	{
		$existeInfo = false;
		$msgErrors = array();

		// Obtener y validar el id del banco en caso de reintegro
		if(
			isset($_POST['reintegroIdBanco']) &&
			($reintegroIdBanco=trim($_POST['reintegroIdBanco'])) != '' &&
			strcmp($reintegroIdBanco, "0") != 0 
		)
		{
			$existeInfo = true;
			$banco = new EntidadBanco();
			$banco->SetId($reintegroIdBanco);
			$form->GetRendicionViaticoNacional()->SetReintegroBanco($banco);
		} else {
			$msgErrors[] = 'Debe indicar un banco para el reintegro';
		}
		
		// Obtener y validar la referencia en caso de reintegro
		if(isset($_POST['reintegroReferencia']) && trim($_POST['reintegroReferencia']) != '')
		{
			$existeInfo = true;
			$form->GetRendicionViaticoNacional()->SetReintegroReferencia(trim($_POST['reintegroReferencia']));
		} else {
			$msgErrors[] = 'Debe indicar una referencia bancaria para el reintegro';
		}
		
		// Obtener y validar la fecha en caso de reintegro
		if(isset($_POST['reintegroFecha']) && trim($_POST['reintegroFecha']) != ''){
			if(($fecha = $this->__ValidarFecha($_POST['reintegroFecha'])) !== false){
				$existeInfo = true;
				$form->GetRendicionViaticoNacional()->SetReintegroFecha($fecha);
			} else {
				$msgErrors[] = 'Fecha de reintegro inv&aacute;lida';
			}
		} else {
			$msgErrors[] = 'Debe indicar una fecha para el reintegro';
		}
		
		if($existeInfo){
			foreach($msgErrors as $msgError){
				$GLOBALS['SafiErrors']['general'][] = $msgError; 
			}
		}
	}
	
	// Obtener y validar el id del banco en caso de reintegro
	private function __ValidarReintegroBanco(NuevaRendicionViaticoNacionalForm $form){
		if(!isset($_POST['reintegroIdBanco']) || trim($_POST['reintegroIdBanco']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Debe indicar un banco para el reintegro.";
		} else {
			$banco = new EntidadBanco();
			$banco->SetId(trim($_POST['reintegroIdBanco']));
			$form->GetRendicionViaticoNacional()->SetReintegroBanco($banco);
		}
	}
	
	// Obtener y validar la referencia en caso de reintegro
	private function __ValidarReintegroReferencia(NuevaRendicionViaticoNacionalForm $form){
		if(!isset($_POST['reintegroReferencia']) || trim($_POST['reintegroReferencia']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Debe indicar una referencia bancaria para el reintegro.";
		} else {
			$form->GetRendicionViaticoNacional()->SetReintegroReferencia(trim($_POST['reintegroReferencia']));
		}
	}
	
	// Obtener y validar la fecha en caso de reintegro
	private function __ValidarReintegroFecha(NuevaRendicionViaticoNacionalForm $form){
		if(!isset($_POST['reintegroFecha']) || trim($_POST['reintegroFecha']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Debe indicar una fecha para el reintegro.";
		} else {
			if(($fecha = $this->__ValidarFecha($_POST['reintegroFecha'])) !== false){
				$form->GetRendicionViaticoNacional()->SetReintegroFecha($fecha);
			} else {
				$GLOBALS['SafiErrors']['general'][] = "Fecha de reintegro inv&aacute;lida.";
			}
		}
	}
	
	// Obtener y validar las observaciones de la rendición
	private function __ValidarObservaciones(NuevaRendicionViaticoNacionalForm $form){
		if(isset($_POST['observaciones']) && trim($_POST['observaciones']) != ''){
			$form->GetRendicionViaticoNacional()->SetObservaciones(trim($_POST['observaciones']));
		}
	}
	
	// Validar fechas
	private function __ValidarFecha($fecha){
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
	
	private function CalcularMontoTotalAsignaciones(array $viaticoRespAsignaciones = null)
	{
		$totalGastos = 0.0;
		if($viaticoRespAsignaciones != null){
			foreach($viaticoRespAsignaciones as $viaticoRespAsig){
				if($viaticoRespAsig instanceof EntidadViaticoResponsableAsignacion){
					$totalGastos += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
				} 
			}
		}
		return $totalGastos;
	}
}

new RendicionViaticoNacionalAccion();