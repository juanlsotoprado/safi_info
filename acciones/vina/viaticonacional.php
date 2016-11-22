<?php
require_once(dirname(__FILE__) . '/../../init.php');

// Acciones
require_once(SAFI_ACCIONES_PATH. '/acciones.php');

// Entidades
require_once(SAFI_ENTIDADES_PATH.'/ruta.php');
require_once(SAFI_ENTIDADES_PATH.'/tipocuentabancaria.php');
require_once(SAFI_ENTIDADES_PATH.'/viaticoresponsableasignacion.php');

// Includes
require_once(SAFI_INCLUDE_PATH. '/arreglos_pg.php');
require_once(SAFI_INCLUDE_PATH. '/constantes.php');
require_once(SAFI_INCLUDE_PATH. '/perfiles/constantesPerfiles.php');

// Libs
require_once (SAFI_LIB_PATH . '/general.php');

// Modelos
require_once(SAFI_MODELO_PATH.'/accioncentralizada.php');
require_once(SAFI_MODELO_PATH.'/asignacionviatico.php');
require_once(SAFI_MODELO_PATH.'/beneficiarioviatico.php');
require_once(SAFI_MODELO_PATH.'/cargo.php');
require_once(SAFI_MODELO_PATH.'/categoriaviatico.php');
require_once(SAFI_MODELO_PATH.'/dependencia.php');
require_once(SAFI_MODELO_PATH.'/dependenciacargo.php');
require_once(SAFI_MODELO_PATH.'/docgenera.php');
require_once(SAFI_MODELO_PATH.'/empleado.php');
require_once(SAFI_MODELO_PATH.'/estado.php');
require_once(SAFI_MODELO_PATH.'/estatus.php');
require_once(SAFI_MODELO_PATH.'/firma.php');
require_once(SAFI_MODELO_PATH.'/general.php');
require_once(SAFI_MODELO_PATH.'/infocentro.php');
require_once(SAFI_MODELO_PATH.'/municipio.php');
require_once(SAFI_MODELO_PATH.'/parroquia.php');
require_once(SAFI_MODELO_PATH.'/proyecto.php');
require_once(SAFI_MODELO_PATH.'/red.php');
require_once(SAFI_MODELO_PATH.'/regionReporte.php');
require_once(SAFI_MODELO_PATH.'/regionReporteEstado.php');
require_once(SAFI_MODELO_PATH.'/responsableviatico.php');
require_once(SAFI_MODELO_PATH.'/revisionesDoc.php');
require_once(SAFI_MODELO_PATH.'/tipotransporte.php');
require_once(SAFI_MODELO_PATH.'/viaticonacional.php');
require_once(SAFI_MODELO_PATH.'/wfcadena.php');
require_once(SAFI_MODELO_PATH.'/wfgrupo.php');

if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}

class ViaticoNacionalAccion extends Acciones
{	
	public function Bandeja()
	{
		$form = FormManager::GetForm('bandejaViaticoNacional');
		
		$login = $_SESSION['login'];
		$idPerfil = $_SESSION['user_perfil_id'];
		$idDependencia = $_SESSION['user_depe_id'];
		
		$estatusDevuelto = 7;
		$estatusEntransito = 10;
		
		// Obtener los viáticos en la bandeja
		
		$enBandeja = null;
		$estatus = array();
		
		// Bandeja principal
		if(
			substr($idPerfil,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
			$idPerfil == PERFIL_ASISTENTE_EJECUTIVO ||
			$idPerfil == PERFIL_ASISTENTE_PRESIDENCIA
		){
			$enBandeja = SafiModeloViaticoNacional::GetViaticoNacionalEnBandeja(array(
				'idPerfilActual' => $idPerfil,
				'estatus' => arraY($estatusDevuelto),
				'idDependencia' => $idDependencia 
			));
			
		} else if($idPerfil == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS){
			$enBandeja = SafiModeloViaticoNacional::GetViaticoNacionalEnBandeja(array(
				'idPerfilActual' => $idPerfil,
				'estatus' => arraY($estatusEntransito) 
			));
		} else if(
			(substr($idPerfil,0,2)."000" == PERFIL_DIRECTOR) ||
			(substr($idPerfil,0,2)."000" == PERFIL_GERENTE) ||
			$idPerfil == PERFIL_DIRECTOR_EJECUTIVO ||
			$idPerfil == PERFIL_PRESIDENTE
		){
			$enBandeja = SafiModeloViaticoNacional::GetViaticoNacionalEnBandeja(array(
				'idPerfilActual' => $idPerfil,
				'estatus' => arraY($estatusEntransito),
				'idDependencia' => $idDependencia 
			));
		} else if(
			$idPerfil == PERFIL_JEFE_PRESUPUESTO ||
			$idPerfil == PERFIL_ANALISTA_PRESUPUESTO
		){
			$enBandeja = SafiModeloViaticoNacional::GetViaticoNacionalEnBandeja(array(
				'idPerfilActual' => PERFIL_JEFE_PRESUPUESTO,
				'estatus' => arraY($estatusEntransito) 
			));
		}
		
		$form->SetEnBandeja($enBandeja);
		
		
		if(
			substr($idPerfil,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
			$idPerfil == PERFIL_ASISTENTE_EJECUTIVO ||
			$idPerfil == PERFIL_ASISTENTE_PRESIDENCIA
		){
		
		
			// Establecer la bandeja de viáticos por enviar
			$porEnviar = SafiModeloViaticoNacional::GetViaticoNacionalPorEnviar(array(
				"usuaLogin" => $login,
				"idPerfilActual" => $idPerfil
			));
			$form->SetPorEnviar($porEnviar);
			
		}

		/***********************
		* Viáticos en tránsito *
		************************/
		
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
			$enTransito = SafiModeloViaticoNacional::GetViaticoNacionalEnTransito($params);
			$form->SetEnTransito($enTransito);
			
			$cargoFundaciones = array();
			$idDependencias = array();
			
			if(is_array($enTransito)){
				foreach($enTransito as $dataViatico){
					$docGenera = $dataViatico['ClassDocGenera'];
					
					$idPerfilActual = $docGenera->GetIdPerfilActual();
					if($idPerfilActual != null && $idPerfilActual != '')
					{
						$cargoFundacion = GetCargoFundacionFromIdPerfil($idPerfilActual);
						$cargoFundaciones[$cargoFundacion] = $cargoFundacion;
						
						$idDependencia = GetIdDependenciaFromIdPerfil($idPerfilActual);
						$idDependencias[$idDependencia] = $idDependencia; 
					}
				}
			}
			
			if(count($cargoFundaciones)>=0){
				$cargoFundacionEnTransitos = SafiModeloCargo::GetCargoByCargoFundaciones($cargoFundaciones);
				$GLOBALS['SafiRequestVars']['cargoFundacionEnTransitos'] = $cargoFundacionEnTransitos;
			}
			
			if(count($idDependencias)>0){
				$dependenciaEnTransitos = SafiModeloDependencia::GetDependenciaByIds($idDependencias);
				$GLOBALS['SafiRequestVars']['dependenciaEnTransitos'] = $dependenciaEnTransitos;
			}
		}
		
		require(SAFI_VISTA_PATH ."/vina/bandeja.php");
	}
	
	public function Agregar()
	{
		$form = FormManager::GetForm('viaticoNacional');
		$form->SetTipoOperacion(ViaticoNacionalForm::TIPO_OPERACION_INSERTAR);
		// Descomentar para produccion
		$form->SetRuta(new EntidadRuta());
		
		//Comentar para produccion
		/*
		$form->GetEstado()->SetId(2);
		
		$form->GetCategoriaViatico()->SetId(2);
		$form->GetRed()->SetId(2);
		
		//$form->SetTipoProyectoAccionCentralizada('accionCentralizada');
		//$form->SetIdProyectoAccionCentralizada('1010');
		//$form->SetIdAccionEspecifica('1010');
		
		$form->SetIdProyectoAccionCentralizada('114254');
		$form->SetIdAccionEspecifica('114254 A-1');
		
		$form->SetFechaInicioViaje(date('d/m/Y', mktime(0, 0, 0, 1, 20, 2011)));
		$form->SetFechaFinViaje(date('d/m/Y', mktime(0, 0, 0, 1, 22, 2011)));
		$form->SetObjetivosViaje(utf8_decode('Objetivo del viaje con acento: razón'));
		
		$infoc = new EntidadInfocentro();
		$infoc->SetId(4);
		$infoc->SetNombre('Infocentro 1');		
		$form->SetInfocentro($infoc);
		
		$infoc = new EntidadInfocentro();
		$infoc->SetId(5);
		$infoc->SetNombre('Infocentro 2');
		$form->SetInfocentro($infoc);
		
		$rut = new EntidadRuta();
		$rut->SetFechaInicio(date('d/m/Y'));
		$rut->SetFechaFin(date('d/m/Y', mktime(0, 0, 0, 1, 22, 2011)));
		$rut->SetDiasAlimentacion(1);
		$rut->SetDiasHospedaje(3);
		$rut->SetUnidadTransporteInterurbano(2);
		$rut->SetIdTipoTransporte(1);
		$rut->SetPasajeIdaVuelta(true);
		$rut->SetAeropuertoResidencia(true);
		$rut->SetResidenciaAeropuerto(true);
		$rut->SetIdFromEstado(10);
		$rut->SetIdFromCiudad(293);
		$rut->SetIdFromMunicipio(104);
		$rut->SetIdFromParroquia(331);
		$rut->SetFromDireccion(utf8_decode('Calle Gómez'));
		$rut->SetIdToEstado(4);
		$rut->SetIdToCiudad(114);
		$rut->SetIdToMunicipio(47);
		$rut->SetIdToParroquia(142);
		$rut->SetToDireccion(utf8_decode('Calle real'));
		$rut->SetObservaciones(utf8_decode('Observación de la  ruta del viático'));
		$form->SetRuta($rut);
		
		$rut = new EntidadRuta();
		$rut->SetFechaInicio(date('d/m/Y', mktime(0, 0, 0, 6, 4, 2009)));
		$rut->SetFechaFin(date('d/m/Y', mktime(0, 0, 0, 8, 30, 2008)));
		$rut->SetDiasAlimentacion(3);
		$rut->SetDiasHospedaje(2);
		$rut->SetUnidadTransporteInterurbano(1);
		$rut->SetIdTipoTransporte(1);
		$rut->SetIdFromEstado(4);
		$rut->SetIdFromCiudad(114);
		$rut->SetIdFromMunicipio(47);
		$rut->SetIdFromParroquia(142);
		$rut->SetIdToEstado(10);
		$rut->SetFromDireccion(utf8_decode('Calle sonrisa'));
		$rut->SetObservaciones(utf8_decode('Otra observación de la  ruta del viático'));
		$form->SetRuta($rut);
		
		$form->SetTipoResponsable('empleado');
		
		$responsable = new EntidadResponsableViatico();
		$responsable->SetTipoResponsable('empleado');
		$responsable->SetCedula('15586921');
		$responsable->SetNombres('William Javier');
		$responsable->SetApellidos('Mendoza Mero');
		$responsable->SetNacionalidad('V');
		$responsable->SetNumeroCuenta('12345678912345678912');
		$responsable->SetTipoCuenta('C');
		$responsable->SetBanco('Venezuela');
		$form->SetResponsable($responsable);
		
		$form->SetObservaciones(utf8_decode('Observación del viático'));
		*/
		// Fin comentar para producción
		
		// Categorías del viatico nacional (Asunto)
		$categorias = SafiModeloCategoriaViatico::GetAllCategoriasActivas();
		
		// Redes del viatico nacional (relacionado con las categorías --Encuentros--)
		$redes = SafiModeloRed::GetAllRedesActivas();
		
		$proyectosAccionesGenerales =  SafiModeloGeneral::GetAllProyectosAccionesGenerales();
		
		$accionesEspecificas = SafiModeloGeneral::GetAllAccionesEspecificas();
		
		$estados = SafiModeloEstado::GetAllEstados2();
		
		// obtener empleadostivos
		$empleadosActivos = SafiModeloEmpleado::GetEmpleadosActivos();
		
		$labelRespEmpleados = array();
		
		foreach($empleadosActivos as $empleado){
			$nombre = str_replace("\n", " ", strtoupper($empleado['empl_nombres'] . " " . $empleado['empl_apellidos']));
			$labelRespEmpleados[] = "'" . $empleado['empl_cedula'] . " : " . $nombre . "'";
		}
		
		$jsLabelRespEmpleados = implode(",", $labelRespEmpleados);
		
		// obtener beneficiarios de viaticos
		$beneficiariosActivos = SafiModeloBeneficiarioViatico::GetAllBeneficiariosActivos();
		
		$labelRespBeneficiarios = array();
		
		foreach($beneficiariosActivos as $beneficiario){
			$nombre = str_replace("\n", " ", strtoupper($beneficiario['benvi_nombres'] . " " . $beneficiario['benvi_apellidos']));
			$labelRespBeneficiarios[] = "'" . $beneficiario['benvi_cedula'] . " : " . $nombre . "'";
		}
		
		$jsLabelRespBeneficiarios = implode(",", $labelRespBeneficiarios);
		
		// Obtener las asignaciones de viaticos
		$asignaciones = SafiModeloAsignacionViatico::GetAsignaciones();
		foreach($asignaciones as $asignacion){
			$viaticoRespAsign = new EntidadViaticoResponsableAsignacion();
			$viaticoRespAsign->SetAsignacionViaticoId($asignacion->GetId());
			$viaticoRespAsign->SetMonto($asignacion->GetMontoFijo());
			$viaticoRespAsign->SetUnidadMedida($asignacion->GetUnidadMedida());
			/*
				switch($asignacion->GetCodigo()){
					case EntidadAsignacionViatico::COD_SERVICIO_COMUNICACIONES:
						$viaticoRespAsign->SetUnidades(2);
						break;
					case EntidadAsignacionViatico::COD_ASIGNACION_TRANSPORTE:
						$viaticoRespAsign->SetUnidades(3);
						break;
					case EntidadAsignacionViatico::COD_TRANSPORTE_EXTRAURBANO:
						$viaticoRespAsign->SetUnidades(2);
						$viaticoRespAsign->SetMonto(100);
						break;
					case EntidadAsignacionViatico::COD_TRANSPORTE_ENTRE_CIUDADES:
						$viaticoRespAsign->SetUnidades(5);
						$viaticoRespAsign->SetMonto(30);
						break;
						
				}
			*/
			$form->SetViaticoResponsableAsignacion($asignacion->GetCodigo(), $viaticoRespAsign);
		}
		//borrar
	/*	echo '<pre>';
		var_dump($form);
		echo '</pre>';
		*/

		$GLOBALS['SafiRequestVars']['categorias'] =  $categorias;
		$GLOBALS['SafiRequestVars']['redes'] =  $redes;
		$GLOBALS['SafiRequestVars']['proyectosAccionesGenerales'] = $proyectosAccionesGenerales;
		$GLOBALS['SafiRequestVars']['accionesEspecificas'] = $accionesEspecificas;
		$GLOBALS['SafiRequestVars']['jsLabelRespEmpleados'] = $jsLabelRespEmpleados;
		$GLOBALS['SafiRequestVars']['jsLabelRespBeneficiarios'] = $jsLabelRespBeneficiarios;
		$GLOBALS['SafiRequestVars']['asignaciones'] =  $asignaciones;
		$GLOBALS['SafiRequestVars']['estados'] = $estados;
		
		require(SAFI_VISTA_PATH ."/vina/nuevoviatico.php");
	}
	
	public function Guardar()
	{
		$idViatico = $this->__Guardar();
		
		if($idViatico !== false){
			$GLOBALS['SafiInfo']['general'][] = "Vi&aacute;tico nacional \"" . $idViatico . "\" registrado satisfactoriamente.";
			$this->__VerDetalles(array('idViaticoNacional' => $idViatico));
		} else {
			$this->__DesplegarFormularioViatico();
		}
	}
	
	private function __Guardar()
	{
		$form = FormManager::GetForm('viaticoNacional');
		
		$GLOBALS['SafiErrors']['general'] = array();
		$idDependencia = $_SESSION['user_depe_id'];
		$annoPresupuesto = $_SESSION['an_o_presupuesto'];
		$loginRegistrador = $_SESSION['login'];
		$perfilRegistrador = $_SESSION['user_perfil_id'];
		
		//Descomentar para obligar a que el viático tenga la fecha del día que se generó
		//$form->SetFechaViatico(date('d/m/Y H:i:s'));
		
		// Obtener las asignaciones de viaticos
		$asignaciones = SafiModeloAsignacionViatico::GetAsignaciones();
		foreach($asignaciones as $asignacion){
			$viaticoRespAsign = new EntidadViaticoResponsableAsignacion();
			$viaticoRespAsign->SetAsignacionViaticoId($asignacion->GetId());
			$viaticoRespAsign->SetMonto($asignacion->GetMontoFijo());
			$viaticoRespAsign->SetUnidadMedida($asignacion->GetUnidadMedida());
			
			$form->SetViaticoResponsableAsignacion($asignacion->GetCodigo(), $viaticoRespAsign);
		}
		
		/*******************************************************************
		 * Validaciones
		 ******************************************************************/
		
		// Comentar para obligar a que el viático tenga la fecha del día que se generó
		// Obtener y validar la fecha del viático
		$this->__ValidarFechaViatico($form);
		
		// Obtener y validar la información del estado del viático
		$this->__ValidarEstado($form);
		// Obtener y validar la información de la categoría del viático
		$this->__ValidarCategoriaViatico($form);
		// Obtener y validar la información de la red
		$this->__ValidarRed($form);
		// obtener y validar la información del responsable
		$this->__ValidarResponsable($form);
		// Obtener y validar los datos de la cuanta bancaria
		$this->__ValidarCuentaBancaria($form->GetResponsable());
		// Obtener y validar el tipo de categoría programática
		$this->__ValidarTipoCategoriaProgramatica($form);
		// Obtener y validar de forma general el id del proyecto/accion centralizada
		$this->__ValidarIdProyectoAccionCentralizada($form);
		// Obtener y validar de forma general el id de la acción específica
		$this->__ValidarIdAccionEspecifica($form);
		// Obtener y validar la fecha de inicio del viaje
		$this->__ValidarFechaInicioViaje($form);
		// Obtener y validar la fecha de fin del viaje
		$this->__ValidarFechaFinViaje($form);
		// Obtener y validar los objetivos del viaje
		$this->__ValidarObjetivosViaje($form);
		// Obtener y validar los infocentros
		$this->__ValidarInfocentros($form);
		// Obtener y validar las rutas
		$this->__ValidarRutas($form);
		// Obtener y validar asignación de viaticos: Unidades de servicio de comunicaciones
		$this->__ValidarServicioComunicaciones($form);
		// Obtener y validar asignación de viaticos: Unidades de asignación por transporte
		$this->__ValidarAsignacionTransporte($form);
		// Obtener y validar asignación de viaticos: Monto y unidades de transporte extraurbano
		$this->__ValidarTransporteExtraurbano($form);
		// Obtener y validar asignación de viaticos: Monto y unidades de transporte entre ciudades
		$this->__validarTransporteEntreCiudades($form);
		// Obtener y validar las observaciones del viaje
		$this->__ValidarObservaciones($form);
		
		/***********************************************************************
		* Fin de Validaciones
		************************************************************************/
		
		if(count($GLOBALS['SafiErrors']['general']) == 0){
			$params = array();
			
			$params['idEstado'] = $form->GetEstado()->GetId();
			$params['idCategoriaViatico'] = $form->GetCategoriaViatico()->GetId();
			$params['idRed'] = $form->GetRed()->GetId();
			$params['idDependencia'] = $idDependencia; 
			$params['annoPresupuesto'] = $annoPresupuesto;
			$params['loginRegistrador'] = $loginRegistrador;
			$params['perfilRegistrador'] = $perfilRegistrador;
			$params['tipoProyectoAccionCentralizada'] = $form->GetTipoProyectoAccionCentralizada();
			$params['idProyectoAccionCentralizada'] = $form->GetIdProyectoAccionCentralizada();  // Id del proyecto o acción centralizada.
			$params['idAccionEspecifica'] = $form->GetIdAccionEspecifica();  // Id de la acción específica
			$params['fechaViatico'] = $form->GetFechaViatico();  // Fecha en que se realiza el viatico
			$params['fechaInicioViaje'] = $form->GetFechaInicioViaje();  // Fecha de inicio de laruta del viatico más cercana
			$params['fechaFinViaje'] = $form->GetFechaFinViaje();  // Fecha de fin de la ruta del viatico más lejana
			$params['objetivosViaje'] = $form->GetObjetivosViaje();  // Objetivos del viaje
			$params['infocentros'] = $form->GetInfocentros();  // Arreglo que contiene el Id de los infocentros
			$params['rutas'] = $form->GetRutas();  // Arreglo asociativo que contiene información de las rutas
			$params['tipoResponsable'] = $form->GetTipoResponsable();
			$params['responsable'] = $form->GetResponsable();  // Contiene información del responsable del viatico
			$params['viaticoResponsableAsignaciones'] = $form->GetViaticoResponsableAsignaciones();
			$params['observaciones'] = $form->GetObservaciones();  // Observaciones del viático
			
			$idViatico = SafiModeloViaticoNacional::GuardarViaticoNacional($params);
			if($idViatico === false){
				$GLOBALS['SafiErrors']['general'][] = 'El vi&aacute;tico nacional no pudo ser registrado. Intente m&aacute;s tarde.';
			}
			return $idViatico;
		}
		
		return false;
	}
	
	public function Actualizar()
	{
		$idViatico = $this->__Actualizar();
		
		if($idViatico !== false){
			$GLOBALS['SafiInfo']['general'][] = "Vi&aacute;tico nacional \"" . $idViatico . "\" modificado satisfactoriamente.";
			$this->__VerDetalles(array('idViaticoNacional' => $idViatico));
		} else {
			$this->__DesplegarFormularioViatico();
		}
	}
	
	private function __Actualizar()
	{	
		$form = FormManager::GetForm('viaticoNacional');
		$form->SetTipoOperacion(ViaticoNacionalForm::TIPO_OPERACION_MODIFICAR);
		
		$annoPresupuesto = $_SESSION['an_o_presupuesto'];
		
		// validar el identificador del viatico
		if(isset($_POST['idViatico']) && trim($_POST['idViatico'])!= ''){
			$form->SetIdViatico(trim($_POST['idViatico']));
			$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($form->GetIdViatico());
		} else {
			$GLOBALS['SafiErrors']['general'][] = 'No se encontr&oacute; ning&uacute;n identificador para el vi&aacute;tico.';
		}
		
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		$idDependencia = $_SESSION['user_depe_id'];
		$annoPresupuesto = $_SESSION['an_o_presupuesto'];
		$loginRegistrador = $_SESSION['login'];
		
		// Obtener las asignaciones de viaticos
		$asignaciones = SafiModeloAsignacionViatico::GetAsignaciones();
		foreach($asignaciones as $asignacion){
			$viaticoRespAsign = new EntidadViaticoResponsableAsignacion();
			$viaticoRespAsign->SetAsignacionViaticoId($asignacion->GetId());
			$viaticoRespAsign->SetMonto($asignacion->GetMontoFijo());
			$viaticoRespAsign->SetUnidadMedida($asignacion->GetUnidadMedida());
			
			$form->SetViaticoResponsableAsignacion($asignacion->GetCodigo(), $viaticoRespAsign);
		}
		
		/*******************************************************************
		 * Validaciones
		 ******************************************************************/
		
		// Comentar para obligar a que el viático tenga la fecha del día que se generó
		// Obtener y validar la fecha del viático
		$this->__ValidarFechaViatico($form);
		
		// Obtener y validar la información del estado del viático
		$this->__ValidarEstado($form);
		// Obtener y validar la información de la categoría del viático
		$this->__ValidarCategoriaViatico($form);
		// Obtener y validar la información de la red
		$this->__ValidarRed($form);
		// obtener y validar la información del responsable
		$this->__ValidarResponsable($form);
		// Obtener y validar los datos de la cuanta bancaria
		$this->__ValidarCuentaBancaria($form->GetResponsable());
		// Obtener y validar el tipo de categoría programática
		$this->__ValidarTipoCategoriaProgramatica($form);
		// Obtener y validar de forma general el id del proyecto/accion centralizada
		$this->__ValidarIdProyectoAccionCentralizada($form);
		// Obtener y validar de forma general el id de la acción específica
		$this->__ValidarIdAccionEspecifica($form);
		// Obtener y validar la fecha de inicio del viaje
		$this->__ValidarFechaInicioViaje($form);
		// Obtener y validar la fecha de fin del viaje
		$this->__ValidarFechaFinViaje($form);
		// Obtener y validar los objetivos del viaje
		$this->__ValidarObjetivosViaje($form);
		// Obtener y validar los infocentros
		$this->__ValidarInfocentros($form);
		// Obtener y validar las rutas
		$this->__ValidarRutas($form);
		// Obtener y validar asignación de viaticos: Unidades de servicio de comunicaciones
		$this->__ValidarServicioComunicaciones($form);
		// Obtener y validar asignación de viaticos: Unidades de asignación por transporte
		$this->__ValidarAsignacionTransporte($form);
		// Obtener y validar asignación de viaticos: Monto y unidades de transporte extraurbano
		$this->__ValidarTransporteExtraurbano($form);
		// Obtener y validar asignación de viaticos: Monto y unidades de transporte entre ciudades
		$this->__validarTransporteEntreCiudades($form);
		// Obtener y validar las observaciones del viaje
		$this->__ValidarObservaciones($form);
		
		/***********************************************************************
		* Fin de Validaciones
		************************************************************************/
		
		if(count($GLOBALS['SafiErrors']['general']) == 0){
			
			// Comentar para obligar a que el viático tenga la fecha del día que se generó
			$viatico->SetFechaViatico($form->getFechaViatico());
			
			$viatico->SetFechaInicioViaje($form->GetFechaInicioViaje());
			$viatico->SetFechaFinViaje($form->GetFechaFinViaje());
			$viatico->SetObjetivosViaje($form->GetObjetivosViaje());
			$viatico->SetInfocentros($form->GetInfocentros());
			$viatico->SetRutas($form->GetRutas());
			$viatico->SetResponsable($form->GetResponsable());
			$viatico->SetViaticoResponsableAsignaciones($form->GetViaticoResponsableAsignaciones());
			$viatico->SetObservaciones($form->GetObservaciones());
			$viatico->SetCategoriaViatico($form->GetCategoriaViatico());
			$viatico->SetRed($form->GetRed());
			$viatico->SetEstado($form->GetEstado());
			
			if(strcmp($form->GetTipoProyectoAccionCentralizada(), 'proyecto') == 0)
			{
				$viatico->SetProyectoId($form->GetIdProyectoAccionCentralizada());
				$viatico->SetProyectoAnho($annoPresupuesto);
				$viatico->SetProyectoEspecificaId($form->GetIdAccionEspecifica());
				
				$viatico->SetAccionCentralizadaId(null);
				$viatico->SetAccionCentralizadaAnho(null);
				$viatico->SetAccionCentralizadaEspecificaId(null);
			}
			else if(strcmp($form->GetTipoProyectoAccionCentralizada(), 'accionCentralizada') == 0)
			{
				$viatico->SetAccionCentralizadaId($form->GetIdProyectoAccionCentralizada());
				$viatico->SetAccionCentralizadaAnho($annoPresupuesto);
				$viatico->SetAccionCentralizadaEspecificaId($form->GetIdAccionEspecifica());
				
				$viatico->SetProyectoId(null);
				$viatico->SetProyectoAnho(null);
				$viatico->SetProyectoEspecificaId(null);
			}
			
			$resultado = SafiModeloViaticoNacional::ActualizarViaticoNacional($viatico);
			
			if($resultado === false){
				$GLOBALS['SafiErrors']['general'][] = 'El vi&aacute;tico nacional no pudo ser modificado. Intente m&aacute;s tarde.';
			}
			
			return $resultado;
			
		} else {
			if(isset($viatico) && $viatico instanceof EntidadViaticoNacional){

				/*
				if($viatico->GetAccionCentralizadaId() != null && $viatico->GetAccionCentralizadaAnho() != null &&
					$viatico->GetAccionCentralizadaEspecificaId() != null)
				{
					$form->SetTipoProyectoAccionCentralizada('accionCentralizada');
					$form->SetIdProyectoAccionCentralizada($viatico->GetAccionCentralizadaId());
					$form->SetIdAccionEspecifica($viatico->GetAccionCentralizadaEspecificaId());
					
				} else  if ($viatico->GetProyectoId() != null && $viatico->GetProyectoAnho() != null &&
					$viatico->GetProyectoEspecificaId())
				{
					$form->SetTipoProyectoAccionCentralizada('proyecto');
					$form->SetIdProyectoAccionCentralizada($viatico->GetProyectoId());
					$form->SetIdAccionEspecifica($viatico->GetProyectoEspecificaId());
				}
				*/
				$arrFecha = explode(' ', $viatico->GetFechaViatico());
				$form->SetFechaViatico($arrFecha[0]);
			}
			
			return false;
		}
	}
	
	private function __DesplegarFormularioViatico(){
		
		// Categorías del viatico nacional (Asunto)
		$categorias = SafiModeloCategoriaViatico::GetAllCategoriasActivas();
		
		// Redes del viatico nacional (relacionado con las categorías --Encuentros--)
		$redes = SafiModeloRed::GetAllRedesActivas();
		
		// Obtener los proyectos y acciones centralizadas
		$proyectosAccionesGenerales =  SafiModeloGeneral::GetAllProyectosAccionesGenerales();
		
		// Obtener las acciones específicas
		$accionesEspecificas = SafiModeloGeneral::GetAllAccionesEspecificas();
		
		$estados = SafiModeloEstado::GetAllEstados2();
		
		// Obtener las asignaciones de viaticos
		$asignaciones = SafiModeloAsignacionViatico::GetAsignaciones();

		// obtener empleados activos
		$empleadosActivos = SafiModeloEmpleado::GetEmpleadosActivos();
		
		$labelRespEmpleados = array();
		
		foreach($empleadosActivos as $empleado){
			$nombre = str_replace("\n", " ", strtoupper($empleado['empl_nombres'] . " " . $empleado['empl_apellidos']));
			$labelRespEmpleados[] = "'" . $empleado['empl_cedula'] . " : " . $nombre . "'";
		}
		
		$jsLabelRespEmpleados = implode(",", $labelRespEmpleados);
		
		// obtener beneficiarios de viaticos
		$beneficiariosActivos = SafiModeloBeneficiarioViatico::GetAllBeneficiariosActivos();
		
		$labelRespBeneficiarios = array();
		
		foreach($beneficiariosActivos as $beneficiario){
			$nombre = str_replace("\n", " ", strtoupper($beneficiario['benvi_nombres'] . " " . $beneficiario['benvi_apellidos']));
			$labelRespBeneficiarios[] = "'" . $beneficiario['benvi_cedula'] . " : " . $nombre . "'";
		}
		
		$jsLabelRespBeneficiarios = implode(",", $labelRespBeneficiarios);
		
		$GLOBALS['SafiRequestVars']['categorias'] = $categorias;
		$GLOBALS['SafiRequestVars']['redes'] = $redes;
		$GLOBALS['SafiRequestVars']['proyectosAccionesGenerales'] = $proyectosAccionesGenerales;
		$GLOBALS['SafiRequestVars']['accionesEspecificas'] = $accionesEspecificas;
		$GLOBALS['SafiRequestVars']['jsLabelRespEmpleados'] = $jsLabelRespEmpleados;
		$GLOBALS['SafiRequestVars']['jsLabelRespBeneficiarios'] = $jsLabelRespBeneficiarios;
		$GLOBALS['SafiRequestVars']['asignaciones'] =  $asignaciones;
		$GLOBALS['SafiRequestVars']['estados'] =  $estados;
		
		require(SAFI_VISTA_PATH ."/vina/nuevoviatico.php");
		
	}
	
	public function Modificar(){
		
		$form = FormManager::GetForm('viaticoNacional');
		$form->SetTipoOperacion(ViaticoNacionalForm::TIPO_OPERACION_MODIFICAR);
		
		// Categorías del viatico nacional (Asunto)
		$categorias = SafiModeloCategoriaViatico::GetAllCategoriasActivas();
		
		// Redes del viatico nacional (relacionado con las categorías --Encuentros--)
		$redes = SafiModeloRed::GetAllRedesActivas();
		
		$proyectosAccionesGenerales =  SafiModeloGeneral::GetAllProyectosAccionesGenerales();
		
		$accionesEspecificas = SafiModeloGeneral::GetAllAccionesEspecificas();
		
		$estados = SafiModeloEstado::GetAllEstados2();
		
		// obtener empleados activos
		$empleadosActivos = SafiModeloEmpleado::GetEmpleadosActivos();
		
		$labelRespEmpleados = array();
		
		foreach($empleadosActivos as $empleado){
			$nombre = str_replace("\n", " ", strtoupper($empleado['empl_nombres'] . " " . $empleado['empl_apellidos']));
			$labelRespEmpleados[] = "'" . $empleado['empl_cedula'] . " : " . $nombre . "'";
		}
		
		$jsLabelRespEmpleados = implode(",", $labelRespEmpleados);
		
		// obtener beneficiarios de viaticos
		$beneficiariosActivos = SafiModeloBeneficiarioViatico::GetAllBeneficiariosActivos();
		
		$labelRespBeneficiarios = array();
		
		foreach($beneficiariosActivos as $beneficiario){
			$nombre = str_replace("\n", " ", strtoupper($beneficiario['benvi_nombres'] . " " . $beneficiario['benvi_apellidos']));
			$labelRespBeneficiarios[] = "'" . $beneficiario['benvi_cedula'] . " : " . $nombre . "'";
		}
		
		$jsLabelRespBeneficiarios = implode(",", $labelRespBeneficiarios);
		
		// Obtener las asignaciones de viaticos
		$asignaciones = SafiModeloAsignacionViatico::GetAsignaciones();
		foreach($asignaciones as $asignacion){
			$viaticoRespAsign = new EntidadViaticoResponsableAsignacion();
			$viaticoRespAsign->SetAsignacionViaticoId($asignacion->GetId());
			$viaticoRespAsign->SetMonto($asignacion->GetMontoFijo());
			$viaticoRespAsign->SetUnidadMedida($asignacion->GetUnidadMedida());
			
				switch($asignacion->GetCodigo()){
					case EntidadAsignacionViatico::COD_SERVICIO_COMUNICACIONES:
						$viaticoRespAsign->SetUnidades(2);
						break;
					case EntidadAsignacionViatico::COD_ASIGNACION_TRANSPORTE:
						$viaticoRespAsign->SetUnidades(3);
						$viaticoRespAsign->SetMonto(100);
						break;
					case EntidadAsignacionViatico::COD_TRANSPORTE_EXTRAURBANO:
						$viaticoRespAsign->SetUnidades(2);
						$viaticoRespAsign->SetMonto(100);
						break;
					case EntidadAsignacionViatico::COD_TRANSPORTE_ENTRE_CIUDADES:
						$viaticoRespAsign->SetUnidades(5);
						$viaticoRespAsign->SetMonto(30);
						break;
						
				}
			
			$form->SetViaticoResponsableAsignacion($asignacion->GetCodigo(), $viaticoRespAsign);
		}
		
		$GLOBALS['SafiRequestVars']['categorias'] = $categorias;
		$GLOBALS['SafiRequestVars']['redes'] = $redes;
		$GLOBALS['SafiRequestVars']['proyectosAccionesGenerales'] = $proyectosAccionesGenerales;
		$GLOBALS['SafiRequestVars']['accionesEspecificas'] = $accionesEspecificas;
		$GLOBALS['SafiRequestVars']['jsLabelRespEmpleados'] = $jsLabelRespEmpleados;
		$GLOBALS['SafiRequestVars']['jsLabelRespBeneficiarios'] = $jsLabelRespBeneficiarios;
		$GLOBALS['SafiRequestVars']['asignaciones'] =  $asignaciones;
		$GLOBALS['SafiRequestVars']['estados'] =  $estados;
		
		if(isset($_REQUEST['idViaticoNacional']) && trim($_REQUEST['idViaticoNacional']) != ''){
			$idViaticoNacional = $_REQUEST['idViaticoNacional'];

			$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($idViaticoNacional);
			
			if($viatico instanceof EntidadViaticoNacional)
			{
				$form->SetIdViatico($viatico->GetId());
				
				if($viatico->GetAccionCentralizadaId() != null && $viatico->GetAccionCentralizadaAnho() != null &&
					$viatico->GetAccionCentralizadaEspecificaId() != null)
				{
					$form->SetTipoProyectoAccionCentralizada('accionCentralizada');
					$form->SetIdProyectoAccionCentralizada($viatico->GetAccionCentralizadaId());
					$form->SetIdAccionEspecifica($viatico->GetAccionCentralizadaEspecificaId());
					
				} else  if ($viatico->GetProyectoId() != null && $viatico->GetProyectoAnho() != null &&
					$viatico->GetProyectoEspecificaId())
				{
					$form->SetTipoProyectoAccionCentralizada('proyecto');
					$form->SetIdProyectoAccionCentralizada($viatico->GetProyectoId());
					$form->SetIdAccionEspecifica($viatico->GetProyectoEspecificaId());
				}
				
				$arrFecha = explode(' ', $viatico->GetFechaViatico());
				$form->SetFechaViatico($arrFecha[0]);
				$form->SetFechaInicioViaje($viatico->GetFechaInicioViaje());
				$form->SetFechaFinViaje($viatico->GetFechaFinViaje());
				$form->SetObjetivosViaje($viatico->GetObjetivosViaje());
				$form->SetInfocentros($viatico->GetInfocentros());
				$form->SetRutas($viatico->GetRutas());
				$form->SetResponsable($viatico->GetResponsable());
				$form->SetViaticoResponsableAsignaciones($viatico->GetViaticoResponsableAsignaciones());
				$form->SetObservaciones($viatico->GetObservaciones());
				$form->SetCategoriaViatico($viatico->GetCategoriaViatico());
				$form->SetRed($viatico->GetRed());
				$form->SetEstado($viatico->GetEstado());
			}
		}
		
		require(SAFI_VISTA_PATH ."/vina/nuevoviatico.php");
	}
	
	public function VerDetalles(){
		if(isset($_REQUEST['idViaticoNacional']) && trim($_REQUEST['idViaticoNacional']) != ''){

			$this->__VerDetalles(array('idViaticoNacional' => $_REQUEST['idViaticoNacional']));
		}
	}
	
	private function __VerDetalles($params)
	{
		$idViaticoNacional = $params['idViaticoNacional'];
		
		$form = FormManager::GetForm('viaticoNacional');
		
		// Obtener las asignaciones de viaticos
		$asignaciones = SafiModeloAsignacionViatico::GetAsignaciones();
		
		$GLOBALS['SafiRequestVars']['asignaciones'] =  $asignaciones;
		
		if($idViaticoNacional != null && trim($idViaticoNacional) != ''){

			$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($idViaticoNacional);
			
			$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idViaticoNacional);
			
			if($viatico instanceof EntidadViaticoNacional && $docGenera instanceof EntidadDocGenera)
			{
				$form->SetIdViatico($viatico->GetId());
				
				if($viatico->GetAccionCentralizadaId() != null && $viatico->GetAccionCentralizadaAnho() != null &&
					$viatico->GetAccionCentralizadaEspecificaId() != null)
				{
					$form->SetTipoProyectoAccionCentralizada('accionCentralizada');
					$form->SetIdProyectoAccionCentralizada($viatico->GetAccionCentralizadaId());
					$form->SetIdAccionEspecifica($viatico->GetAccionCentralizadaEspecificaId());
					
				} else  if ($viatico->GetProyectoId() != null && $viatico->GetProyectoAnho() != null &&
					$viatico->GetProyectoEspecificaId())
				{
					$form->SetTipoProyectoAccionCentralizada('proyecto');
					$form->SetIdProyectoAccionCentralizada($viatico->GetProyectoId());
					$form->SetIdAccionEspecifica($viatico->GetProyectoEspecificaId());
				}
				
				$arrFecha = explode(' ', $viatico->GetFechaViatico());
				$form->SetFechaViatico($arrFecha[0]);
				$form->SetFechaInicioViaje($viatico->GetFechaInicioViaje());
				$form->SetFechaFinViaje($viatico->GetFechaFinViaje());
				$form->SetObjetivosViaje($viatico->GetObjetivosViaje());
				$form->SetInfocentros($viatico->GetInfocentros());
				$form->SetRutas($viatico->GetRutas());
				$form->SetResponsable($viatico->GetResponsable());
				$form->SetViaticoResponsableAsignaciones($viatico->GetViaticoResponsableAsignaciones());
				$form->SetProyecto($viatico->GetProyecto());
				$form->SetProyectoEspecifica($viatico->GetProyectoEspecifica());
				$form->SetAccionCentralizada($viatico->GetAccionCentralizada());
				$form->SetAccionCentralizadaEspecifica($viatico->GetAccionCentralizadaEspecifica());
				$form->SetDocGenera($docGenera);
				$form->SetObservaciones($viatico->GetObservaciones());
				$form->SetCategoriaViatico($viatico->GetcategoriaViatico());
				$form->SetRed($viatico->GetRed());
				$form->SetEstado($viatico->GetEstado());
				$form->SetRequisiciones($viatico->GetRequisiciones());
				
				// Para los documentos de soporte (Memos)
				$GLOBALS['SafiRequestVars']['memos'] = GetDocumentosSoportesMemos($viatico->GetId());
				
				// Para las revisiones del documento
				$GLOBALS['SafiRequestVars']['datosRevisionesDocumento'] = GetDatosRevisionesDocumento($viatico->GetId());
			}
		}
		
		require(SAFI_VISTA_PATH ."/vina/verDetalles.php");
	}
	
	public function Buscar()
	{
		$form = FormManager::GetForm('buscarViaticoNacional');
		
		$idDependencia = $_SESSION['user_depe_id'];
		$idUserPerfil = $_SESSION['user_perfil_id'];
		
		// Validar la fecha de inicio
		if(!isset($_POST['fechaInicio']) || trim($_POST['fechaInicio']) == ''){
			//$GLOBALS['SafiErrors']['general'][] = 'Debe indicar la fecha de inicio del viaje';
		} else {
			$fecha = explode('/', $_POST['fechaInicio']);
			if (count($fecha) != 3){
				$GLOBALS['SafiErrors']['general'][] = 'Fecha de inicio inv&aacute;lida.';
			} else {
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				if(!checkdate ($month ,$day ,$year)){
					$GLOBALS['SafiErrors']['general'][] = 'Fecha de inicio inv&aacute;lida.';
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
				$GLOBALS['SafiErrors']['general'][] = 'Fecha de fin inv&aacute;lida.';
			} else {
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				if(!checkdate ($month ,$day ,$year)){
					$GLOBALS['SafiErrors']['general'][] = 'Fecha de fin inv&aacute;lida.';
				} else {
					$form->SetFechaFin($day . '/' . $month . '/' . $year);
				}
			}
		}
	
		// Validar el id del viatico nacional
		if(
			!isset($_POST['idViaticoNacioanal'])
			|| ($idViaticoNacioanal=trim($_POST['idViaticoNacioanal'])) == ''
			|| strlen($idViaticoNacioanal) < 5
			|| $idViaticoNacioanal == 'vnac-'
		){
			$idViaticoNacioanal = '';
		} else {
			$form->SetIdViaticoNacional(trim($idViaticoNacioanal));
		}
		
		$params = array();
		
		$params['fechaInicio'] = $form->GetFechaInicio();
		$params['fechaFin'] = $form->GetFechaFin();
		$params['idViaticoNacional'] = $idViaticoNacioanal;
		
		if(
			strcmp($idUserPerfil, PERFIL_ANALISTA_CONTABLE) != 0 &&
			strcmp($idUserPerfil, PERFIL_ANALISTA_COMPRAS) != 0 &&
			strcmp($idUserPerfil, PERFIL_ANALISTA_ORDENACION_PAGOS) != 0 &&
			strcmp($idUserPerfil, PERFIL_ANALISTA_PRESUPUESTO) != 0 &&
			strcmp($idUserPerfil, PERFIL_COORDINADOR_COMPRAS) != 0 &&
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
		
		$viaticosNacionales = SafiModeloViaticoNacional::BuscarViaticoNacional($params);
		$form->SetViaticosNacionales($viaticosNacionales);
		
		$cargoFundaciones = array();
		$idDependencias = array();
		
		if($viaticosNacionales !== false){
			foreach($viaticosNacionales as $dataViatico){
				$docGenera = $dataViatico['ClassDocGenera'];
				
				$idPerfilActual = $docGenera->GetIdPerfilActual();
				if($idPerfilActual != null && $idPerfilActual != '')
				{
					$cargoFundacion = GetCargoFundacionFromIdPerfil($idPerfilActual);
					$cargoFundaciones[$cargoFundacion] = $cargoFundacion;
					
					$idDependencia = GetIdDependenciaFromIdPerfil($idPerfilActual);
					$idDependencias[$idDependencia] = $idDependencia; 
				}
			}
			
			if(count($cargoFundaciones)>=0){
				$cargoFundacionInstanciaActuales = SafiModeloCargo::GetCargoByCargoFundaciones($cargoFundaciones);
				$GLOBALS['SafiRequestVars']['cargoFundacionInstanciaActuales'] = $cargoFundacionInstanciaActuales;
			}
			
			if(count($idDependencias)>0){
				$dependenciaInstanciaActuales = SafiModeloDependencia::GetDependenciaByIds($idDependencias);
				$GLOBALS['SafiRequestVars']['dependenciaInstanciaActuales'] = $dependenciaInstanciaActuales;
			}
		} else 
		{
			$GLOBALS['SafiErrors']['general'][] = "Error al realizar la b&uacute;squeda.";
		}
		
		require(SAFI_VISTA_PATH ."/vina/buscar.php");
	}
	
	public function GenerarPDF()
	{
		$idUserPerfil = $_SESSION['user_perfil_id'];
		
		if(isset($_REQUEST['tipo']) && trim($_REQUEST['tipo']) != '' && isset($_REQUEST['idVina']) && trim($_REQUEST['idVina']) != ''){
			$tipo = trim($_REQUEST['tipo']);
			$idVina = trim($_REQUEST['idVina']);
			$viaticoNacionalEntidad =  SafiModeloViaticoNacional::GetViaticoNacionalById($idVina);
			$GLOBALS['SafiRequestVars']['tipo'] = $tipo;
			$GLOBALS['SafiRequestVars']['viaticoNacionalEntidad'] = $viaticoNacionalEntidad;
			if($dependencia instanceof EntidadDependencia){
				$GLOBALS['SafiRequestVars']['dependencia'] = $dependencia;
			}else{
				$dependencia =  SafiModeloDependencia::GetDependenciaById($viaticoNacionalEntidad->GetDependenciaId());
				if($dependencia instanceof EntidadDependencia){
					$GLOBALS['SafiRequestVars']['dependencia'] = $dependencia;
				}
			}
			
			$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($viaticoNacionalEntidad->GetId());
			
			$elaboradoPor = SafiModeloEmpleado::GetEmpleadoByCedula($viaticoNacionalEntidad->GetUsuaLogin());
			
			// Cargo de director o gerente
			$cargosGerenteDirector = GetPerfilCargosGerenteDirectorByIdUserPerfil($docGenera->GetIdPerfil());
			
			$cargoGerenteDirector = SafiModeloDependenciaCargo::GetSiguienteCargoDeCadena
				($viaticoNacionalEntidad->GetDependenciaId(), $cargosGerenteDirector);

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
			
			$GLOBALS['SafiRequestVars']['docGenera'] = $docGenera;
			$GLOBALS['SafiRequestVars']['arrFirmas'] = $arrFirmas;
			$GLOBALS['SafiRequestVars']['perfilGerenteDirector'] = $perfilGerenteDirector;
			$GLOBALS['SafiRequestVars']['elaboradoPor'] = $elaboradoPor;
		}
		require(SAFI_VISTA_PATH ."/vina/viaticoNacional_PDF.php");
	}
	
	public function ArchivosPagos()
	{
		try {
			$form = FormManager::GetForm(FORM_ARCHIVOS_PAGOS_VIATICO_NACIONAL);
			
			$existCriteria = false;
		
			$viatico = null;			
			
			// Validar el id del viatico
			if(isset($_REQUEST['idViatico']) && trim($_REQUEST['idViatico']) != '')
			{
				$form->SetIdViatico(trim($_REQUEST['idViatico']));
				$existCriteria = true;
			} /*else {
				$form->SetIdViatico("vnac-");
			}*/
			
			if($existCriteria)
			{
				$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($form->GetIdViatico());
				$form->SetViatico($viatico);
				
				if($viatico == null)
				{
					throw new Exception("El id del vi&aacute;tico \"".$form->GetIdVaitico()."\" es incorrecto o no existe.");
				} else {
					$form->SetFechaAbono(date('d/m/Y'));
				}
			}
			
		}catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
		}
		
		require(SAFI_VISTA_PATH . "/vina/archivosPagos.php");
	}
	
	public function GenerarArchivosPagos()
	{
		try {
			$form = FormManager::GetForm(FORM_ARCHIVOS_PAGOS_VIATICO_NACIONAL);
			
			// Validar el id del viático
			if(!isset($_POST['idViatico']) || ($idViatico=trim($_POST['idViatico'])) == '')
				throw new Exception("Identificador del vi&aacute;tico no encontrado.");
			
			// Validar la fecha de abono
			if(!isset($_POST['fechaAbono']) || trim($_POST['fechaAbono']) == '')
				throw new Exception("Debe indicar una fecha de abono.");
			
			if(($fecha = $this->__ValidarFecha($_POST['fechaAbono'])) === false)
				throw new Exception("Fecha de abono inv&aacute;lida.");
			
			// Llenar el formulario
			$form->SetIdViatico($idViatico);
			$form->SetFechaAbono($fecha);
			
			// Obtener una instancia del viático
			if(($viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($form->GetIdViatico())) == null)
				throw new Exception("El vi&aacute;tico \"".$idViatico."\" no pudo ser cargado.");
			
			// Obtener cada componente de la fecha de abono en un array
			$arrFechaAbono = explode('/', $form->GetFechaAbono());
			
			// Contruir el detalle del pago de cada responsable
			$personas = array(); // Arreglo que guardará la información de cada una de las personas onjetos de la transferencia
			$montoTotal = 0;
			
			if($viatico->GetResponsable() != null && $viatico->GetResponsable() instanceof EntidadResponsableViatico)
			{
				$responsable = $viatico->GetResponsable();
				$montoTotal = CalcularMontoTotalAsignacionesViaticoNacional($viatico->GetViaticoResponsableAsignaciones());
				
				$persona["id"] = $responsable->GetCedula();
				$persona["nombres"] = $responsable->GetNombres();
				$persona["apellidos"] = $responsable->GetApellidos();
				$persona["numeroCuenta"] = $responsable->GetNumeroCuenta();
				$persona["montoTotal"] = $montoTotal;
					
				$personas[$persona["id"]] = $persona;
			}
			
			$datosArchivosPagos = array(
				"fechaAbono" => $form->GetFechaAbono(),
				"montoTotal" => $montoTotal,
				"personas" => $personas
			);
			
			$GLOBALS['SafiRequestVars']['datosArchivosPagos'] = $datosArchivosPagos;
			
			require(SAFI_VISTA_PATH . "/archivosPagos/archivosPagosBancoDeVenezuela.php");
			
		} catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}
	
	private function __Enviar($idViatico)
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
			$wFCadena = SafiModeloWFCadena::GetWFNextCadenaByIdDocument($idViatico);
			
			if($wFCadena == null){
				$GLOBALS['SafiErrors']['general'][] = 'Error al enviar. WFCadena inicial no encontrada.';
			} else if($wFCadena->GetWFCadenaHijo() == null){
				$GLOBALS['SafiErrors']['general'][] = 'Error al enviar. WFCadena hija no encontrada.';
			// Obtener la cadena siguiente, a la inicial, de vaiticos nacionales
			} else {
				
				$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($idViatico);
				
				// 0 = Documento finalizado
				if(
					strcmp($wFCadena->GetWFCadenaHijo()->GetId(), "0") == 0 ||
					// Finalizar si está en presupuesto y la gerencia es la oficina de gestión administrativa y financiera
					($idPerfil == PERFIL_JEFE_PRESUPUESTO && $viatico->GetDependenciaId() == "450")
				){
					// Obtener una instancia de docgenera para el viatico nacional a enviar (actualizar)
					$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idViatico);
					
					$estadoAprobado = 13;
					
					$docGenera->SetIdWFObjeto(99);
					$docGenera->SetIdWFCadena(0);
					$docGenera->SetIdEstatus($estadoAprobado);
					$docGenera->SetIdPerfilActual(null);
					
					$Revisiones = new EntidadRevisionesDoc();
						
					$Revisiones->SetIdDocumento($idViatico);
					$Revisiones->SetLoginUsuario($loginUsuario);
					$Revisiones->SetIdPerfil($idPerfil);
					$Revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
					$Revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
					
					if(
						$idPerfil == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS
						|| ($idPerfil == PERFIL_JEFE_PRESUPUESTO && $viatico->GetDependenciaId() == "450")
					){
						$requisicion = $this->LlenarRequisicion($docGenera);
					}
					
					// Guardar el registro del documento en docGenera (estado de la cadena)
					if(($enviado = SafiModeloViaticoNacional::EnviarViaticoNacional($docGenera, $Revisiones, $requisicion)) === false){
						$GLOBALS['SafiErrors']['general'][] = 'Error al enviar. No se pudo actualizar docGenera.';
					}
					
				} else if (($wFCadenaHijo = SafiModeloWFCadena::GetWFCadena($wFCadena->GetWFCadenaHijo())) == null){
					$GLOBALS['SafiErrors']['general'][] = 'Error al enviar. WFCadena hija no encontrada.';
				} else if($wFCadenaHijo->GetWFGrupo() == null) {
					$GLOBALS['SafiErrors']['general'][] = 'Error al enviar. WFGrupo de WFCadena hija no encontrado.';
				} else if(($perfilActual = SafiModeloDependenciaCargo::
					GetSiguienteCargoDeCadena($idDependencia, $wFCadenaHijo->GetWFGrupo()->GetPerfiles())) == null)
				{
					$GLOBALS['SafiErrors']['general'][] = 
						'Error al enviar. No se puede encontrar el perfil de la siguiente instancia en la cadena.';
				} else {
					
					// Obtener una instancia de docgenera para el viatico nacional a enviar (actualizar)
					$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idViatico);
					
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
						
						$Revisiones->SetIdDocumento($idViatico);
						$Revisiones->SetLoginUsuario($loginUsuario);
						$Revisiones->SetIdPerfil($idPerfil);
						$Revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
						$Revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
					}
					
					// Guardar el registro del documento en docGenera (estado de la cadena)
					if(($enviado=SafiModeloViaticoNacional::EnviarViaticoNacional($docGenera, $Revisiones)) === false){
						$GLOBALS['SafiErrors']['general'][] = 'Error al enviar.';
					}
				}
			}
		}
		
		return $enviado;
	}
	
	private function LlenarRequisicion(EntidadDocGenera $docGenera)
	{
		$requisicion = null;
		$fechaUsuario = date('d/m/Y');
		
		$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($docGenera->GetId());
		
		/*
			sai_req_bi_ma_ser
			sai_rqui_items
		 * */
		
		$tipoProyectoAccionCentralizada = null;
		$idProyectoAccionCentralizada = null;
		$idAccionEspecifica = null;
		$annoPresupuestario = null;
		
		if(	$viatico->GetProyectoId() != null && $viatico->GetProyectoAnho() != null && 
			$viatico->GetProyectoEspecificaId() != null)
		{
			$tipoProyectoAccionCentralizada = 1;
			$idProyectoAccionCentralizada = $viatico->GetProyectoId();
			$idAccionEspecifica = $viatico->GetProyectoEspecificaId();
			$annoPresupuestario = $viatico->GetProyectoAnho();
		} else if(
			$viatico->GetAccionCentralizadaId() != null && $viatico->GetAccionCentralizadaAnho() != null &&
			$viatico->GetAccionCentralizadaEspecificaId() != null)
		{
			$tipoProyectoAccionCentralizada = 0;
			$idProyectoAccionCentralizada = $viatico->GetAccionCentralizadaId();
			$idAccionEspecifica = $viatico->GetAccionCentralizadaEspecificaId();
			$annoPresupuestario = $viatico->GetAccionCentralizadaAnho();
		}
		
		$nombreCargo = '';
		
		if($viatico->GetResponsable() != null){
			if($viatico->GetResponsable()->GetTipoResponsable() == EntidadResponsableViatico::TIPO_EMPLEADO)
			{
				$cargo = SafiModeloCargo::GetCargoByEmpleado($viatico->GetResponsable()->GetCedula());
				if($cargo != null){
					$nombreCargo = utf8_decode(" EL/LA ").$cargo->GetNombre();
					
					if($cargo->GetFundacion() != "65" && $cargo->GetFundacion() != "47"){
						$dependencia = SafiModeloDependencia::GetDependenciaById($viatico->GetResponsable()->GetIdDependencia());	
	
						if($dependencia != null){
							$nombreCargo .= utf8_decode(" DE ") . $dependencia->GetNombre();
						}
					}
				}
				
				;
				
			} else if($viatico->GetResponsable()->GetTipoResponsable() == EntidadResponsableViatico::TIPO_BENEFICIARIO)
			{
				$nombreCargo = utf8_decode(" EL/LA RESPONSABLE");
				
			}
		}
		
		$descripcionGeneral = utf8_decode("
			SOLICITUD DE BOLETO AÉREO PARA").$nombreCargo." ".$viatico->GetResponsable()->GetNombres().
			" ".$viatico->GetResponsable()->GetApellidos().utf8_decode(" C.I. ").$viatico->GetResponsable()->GetCedula().
			utf8_decode(" PARA LAS RUTAS: ");
		
		// rutas
		$rutas = $viatico->GetRutas();
		
		$arrayDescripcionGeneral = array();
		$arrayItemEspecificaciones = array();
		
		
		foreach($viatico->GetRutas() as $ruta){
			if($ruta->GetIdTipoTransporte() == EntidadTipoTransporte::TIPO_AEREO)
			{
				$observaciones = ($ruta->GetObservaciones()) ?
					utf8_decode(", CON LAS SIGUIENTES OBSERVACIONES: ") . $ruta->GetObservaciones() : '';
				
				if($ruta->GetPasajeIdaVuelta())
				{
					$arrayDescripcionGeneral[] = utf8_decode("[IDA Y VUELTA ").$ruta->GetNombreFromEstado() . utf8_decode(" - ") .
						$ruta->GetNombreToEstado(). utf8_decode(", SALIDA EL DÍA ") . $ruta->GetFechaInicio() .
						/*utf8_decode(" EN HORA DE LA MAÑANA Y RETORNO EL DÍA ").*/
						utf8_decode(" Y RETORNO EL DÍA ").
						$ruta->GetFechaFin() . /*utf8_decode(" EN HORA DE LA MAÑANA")*/
						$observaciones . "]"
						;
					
					$arrayItemEspecificaciones[] = utf8_decode("[ IDA Y VUELTA ").$ruta->GetNombreFromEstado() . utf8_decode(" - ") .
						$ruta->GetNombreToEstado() . utf8_decode(" - ") . $ruta->GetNombreFromEstado() . " ]";
					;
				} else {
					$arrayDescripcionGeneral[] = "[" . $ruta->GetNombreFromEstado() . utf8_decode(" - ") . $ruta->GetNombreToEstado() .
					utf8_decode(", SALIDA EL DÍA ") . $ruta->GetFechaInicio() . /*utf8_decode(" EN HORA DE LA MAÑANA")*/
					$observaciones . "]";
					
					$arrayItemEspecificaciones[] = "[" . $ruta->GetNombreFromEstado() . utf8_decode(" - ") . 
						$ruta->GetNombreToEstado() . "]";
				}	
			}
		}
		
		if(count($arrayDescripcionGeneral)>0){
			$descripcionGeneral .= implode(" - ", $arrayDescripcionGeneral).".";
		} else {
			return $requisicion;
		}
		
		$descripcionGeneral = mb_strtoupper($descripcionGeneral, "ISO-8859-1");
		
		$idItems = array(600);
		$itemCantidades= array(1);
		$itemEspecificaciones = array(
			mb_strtoupper(utf8_decode(" SOLICITUD DE BOLETO AÉREO PARA ")
				.$viatico->GetResponsable()->GetNombres()." ".$viatico->GetResponsable()->GetApellidos()
				.utf8_decode(" PARA LAS RUTAS: ").implode(" - ", $arrayItemEspecificaciones).
			".", "ISO-8859-1")
		);
		
		$requisicion = array(
			'tipo' => 2,  //  1 Compra / 2 Servicio
			'idDependenciaCreador' =>  $viatico->GetDependenciaId(), // Id de dependencia de la persona que crea la requisición
			'loginUsuarioCreador' => $docGenera->GetUsuaLogin(),
			'annoPresupuestario' => $annoPresupuestario,
			'tipoProyectoAccionCentralizada' => $tipoProyectoAccionCentralizada,
			'idProyectoAccionCentralizada' => $idProyectoAccionCentralizada,
			'idAccionEspecifica' => $idAccionEspecifica,
			'proveedorSugerido1' => null,
			'proveedorSugerido2' => null,
			'proveedorSugerido3' => null,
			'calidad' => null,
			'tiempoDeEntrega' => null,
			'garantia' => null,
			'observaciones' => null,
			'idPuntoCuenta' => null,
			'justificacionPuntoCuenta' => 'N/A',
			'idGerenciaAdscripcion' => $viatico->GetDependenciaId(),
			'descripcionGeneral' => $descripcionGeneral,
			'justificacion' => $viatico->GetObjetivosViaje(),
			'idViatico' => $viatico->GetId(),
			'idEstatus' => 13,
			'idItems' => convierte_arreglo($idItems),
			'fechaUsuario' => $fechaUsuario,
			'itemCantidades' => convierte_arreglo($itemCantidades),
			'itemEspecificaciones' => convierte_arreglo($itemEspecificaciones),
			'fechaRequisicion' => date("d/m/Y H:i:s")
		);
		
		return $requisicion;
	}
	
	public function Enviar()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general']  =array();
		
		// Validar el id del viatico nacional
		if(!isset($_REQUEST['idViaticoNacional']) || trim($_REQUEST['idViaticoNacional']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Identificador del vi&aacute;tico nacional no encontrado.';
		} else {
			
			$idViatico = trim($_REQUEST['idViaticoNacional']);
			
			if(($result=$this->__Enviar($idViatico)) !== false){
				
				if(
					substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
					$_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO ||
					$_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA
				){
					$GLOBALS['SafiInfo']['general'][] = "Vi&aacute;tico nacional \"" . $idViatico ."\" enviado satisfactoriamente." .
						((is_array($result) && isset($result['idRequisicion']))
						? " Requisici&oacute;n: \"" . $result['idRequisicion'] : "\"." );
				} else {
					
					$GLOBALS['SafiInfo']['general'][] = "Vi&aacute;tico nacional \"" . $idViatico ."\" aprobado satisfactoriamente.".
						((is_array($result) && isset($result['idRequisicion'])) ?
					 	" Requisici&oacute;n: \"" . $result['idRequisicion'] : "\"." );
				}
			}
		}
		
		$this->Bandeja();
	}
	
	public function Devolver()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general']  =array();
		
		$form = FormManager::GetForm('buscarViaticoNacional');
		
		$idDependencia = $_SESSION['user_depe_id'];
		$idPerfil = $_SESSION['user_perfil_id'];
		$loginUsuario = $_SESSION['login'];
		$opcionDevolver = 5;
		$fechaHoy = date("d/m/Y H:i:s");
		
		// Validar el id del viatico nacional
		if(!isset($_REQUEST['idViaticoNacional']) || trim($_REQUEST['idViaticoNacional']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Error al devolver. Identificador del vi&aacute;tico nacional no encontrado.';
		} else if(!isset($_REQUEST['memoContent']) || trim($_REQUEST['memoContent']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Error al devolver. Motivo de la devoluci&oacute;n no encontrado.';
		} else {
			$form->SetIdViaticoNacional($_REQUEST['idViaticoNacional']);
			$memoContent = trim($_REQUEST['memoContent']);
			
			if(	$idPerfil == PERFIL_JEFE_PRESUPUESTO ||
				$idPerfil == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS
			){
				$documento = new EntidadDocumento();
				$documento->SetId('vnac');
				
				$wFOpcion = new EntidadWFOpcion();
				$wFOpcion->SetId($opcionDevolver);
				
				$wFGrupo = SafiModeloWFGrupo::GetWFGrupoByIdPerfil($idPerfil);
				
				if ($wFGrupo == null){
					$GLOBALS['SafiErrors']['general'][] = 'Error al devolver. WFGrupo no encontrado para el perfil actual.';
				} else
				{
					$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($form->GetIdViaticoNacional());
					
					$dependencia = new EntidadDependencia();
					if($viatico->GetDependenciaId()=="350" || $viatico->GetDependenciaId()=="150"){
						$dependencia->SetId($viatico->GetDependenciaId());
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
						$GLOBALS['SafiErrors']['general'][] = 'Error al devolver. WFCadena inicial no encontrada.';
					} else {
						// Obtener una instancia de docgenera para el viatico nacional a enviar (actualizar)
						$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($form->GetIdViaticoNacional());
						
						$perfilActual = $docGenera->GetIdPerfil();
						$estadoDevuelto = 7;
						
						$docGenera->SetIdWFObjeto($wFCadena->GetWFObjetoInicial()->GetId());
						$docGenera->SetIdWFCadena($wFCadena->GetId());
						$docGenera->SetIdEstatus($estadoDevuelto);
						$docGenera->SetIdPerfilActual($perfilActual);
						
						$memo = new EntidadMemo();
						$memo->SetLoginUsuario($loginUsuario);
						$memo->SetAsunto(utf8_decode('Devolución de viático nacional'));
						$memo->SetContenido($memoContent);
						$memo->SetIdDependencia($idDependencia);
						$memo->SetFechaCreacion($fechaHoy);
						
						$Revisiones = new EntidadRevisionesDoc();
							
						$Revisiones->SetIdDocumento($form->GetIdViaticoNacional());
						$Revisiones->SetLoginUsuario($loginUsuario);
						$Revisiones->SetIdPerfil($idPerfil);
						$Revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
						$Revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
						
						if(SafiModeloViaticoNacional::DevolverViaticoNacional($docGenera, $memo, $Revisiones) === false){
							$GLOBALS['SafiErrors']['general'][] = 'Error al devolver.';
						} else {
							$GLOBALS['SafiInfo']['general'][] = "Vi&aacute;tico nacional \"" . $form->GetIdViaticoNacional() ."\""
								." devuelto satisfactoriamente.";
						}
						
							/*
						// Guardar el registro del documento en docGenera (estado de la cadena)
						if(SafiModeloDocGenera::ActualizarDocGenera($docGenera) === false)
							$GLOBALS['SafiErrors']['general'][] = 'Error al devolver. No se pudo actualizar docGenera. ';
						*/
					}
				}
			} // Fin de else if($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO){
			else if(	
				substr($idPerfil,0,2)."000" == PERFIL_DIRECTOR ||
				substr($idPerfil,0,2)."000" == PERFIL_GERENTE ||
				$idPerfil == PERFIL_DIRECTOR_EJECUTIVO ||
				$idPerfil == PERFIL_PRESIDENTE
			){
				$documento = new EntidadDocumento();
				$documento->SetId('vnac');
				
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
					$GLOBALS['SafiErrors']['general'][] = 'Error al devolver. WFGrupo no encontrado para el perfil actual.';
				} else 
				{
					$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($form->GetIdViaticoNacional());
					
					$dependencia = new EntidadDependencia();
					if($viatico->GetDependenciaId()=="350" || $viatico->GetDependenciaId()=="150"){
						$dependencia->SetId($viatico->GetDependenciaId());
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
						$GLOBALS['SafiErrors']['general'][] = 'Error al devolver. WFCadena inicial no encontrada.';
					} else if($wFCadena->GetWFCadenaHijo() == null){
						$GLOBALS['SafiErrors']['general'][] = 'Error al devolver. Detalles: WFCadena hija no encontrada.';
					// Obtener la cadena siguiente, a la inicial, de vaiticos nacionales
					} else if (($wFCadenaHijo = SafiModeloWFCadena::GetWFCadena($wFCadena->GetWFCadenaHijo())) == null){
						$GLOBALS['SafiErrors']['general'][] = 'Error al devolver. Detalles: WFCadena hija no encontrada.';
					} else if($wFCadenaHijo->GetWFGrupo() == null) {
						$GLOBALS['SafiErrors']['general'][] = 'Error al devolver. Detalles: WFGrupo de WFCadena hija no encontrado.';
					} else if(($perfilActual = SafiModeloDependenciaCargo::
						GetSiguienteCargoDeCadena($idDependencia, $wFCadenaHijo->GetWFGrupo()->GetPerfiles())) == null)
					{
						$GLOBALS['SafiErrors']['general'][] = 
							'Error al devolver. Detalles: No se puede encontrar el perfil de la siguiente instancia en la cadena.';
					} else {
						// Obtener una instancia de docgenera para el viatico nacional a enviar (actualizar)
						$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($form->GetIdViaticoNacional());
						
						$estadoDevuelto = 7;
						
						$docGenera->SetIdWFObjeto($wFCadena->GetWFObjetoInicial()->GetId());
						$docGenera->SetIdWFCadena($wFCadena->GetId());
						$docGenera->SetIdEstatus($estadoDevuelto);
						$docGenera->SetIdPerfilActual($perfilActual->GetId());
						
						$memo = new EntidadMemo();
						$memo->SetLoginUsuario($loginUsuario);
						$memo->SetAsunto('Devolución de viático nacional');
						$memo->SetContenido($memoContent);
						$memo->SetIdDependencia($idDependencia);
						$memo->SetFechaCreacion($fechaHoy);
						
						$Revisiones = new EntidadRevisionesDoc();
						$Revisiones->SetIdDocumento($form->GetIdViaticoNacional());
						$Revisiones->SetLoginUsuario($loginUsuario);
						$Revisiones->SetIdPerfil($idPerfil);
						$Revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
						$Revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
						
						if(SafiModeloViaticoNacional::DevolverViaticoNacional($docGenera, $memo, $Revisiones) === false){
							$GLOBALS['SafiErrors']['general'][] = 'Error al devolver.';
						} else {
							$GLOBALS['SafiInfo']['general'][] = "Vi&aacute;tico nacional \"" . $form->GetIdViaticoNacional()
								."\" devuelto satisfactoriamente.";
						}
					}
				}
			} // fin de else if(	
			  //					substr($idPerfil,0,2)."000" == PERFIL_DIRECTOR ||
			  //					substr($idPerfil,0,2)."000" == PERFIL_GERENTE ||
			  // 					$idPerfil == PERFIL_DIRECTOR_EJECUTIVO ||
			  //					$idPerfil == PERFIL_PRESIDENTE
		}
		
		$this->Bandeja();
	}
	
	public function GuardarYEnviar()
	{
		$idViatico = $this->__Guardar();
		
		if($idViatico !== false){
			if($this->__Enviar($idViatico)){
				$GLOBALS['SafiInfo']['general'][] = "Vi&aacute;tico nacional \"".$idViatico."\" registrado y enviado satisfactoriamente.";
			} else {
				$GLOBALS['SafiInfo']['general'][] = "El vi&aacute;tico nacional \"".$idViatico
					."\" fue registrado satisfactoriamente, pero no se pudo enviar. Intente enviarlo m&aacute;s tarde.";
			}
			
			$this->__VerDetalles(array('idViaticoNacional' => $idViatico));
		} else {
			$this->__DesplegarFormularioViatico();
		}
	
	}
	
	public function ActualizarYEnviar()
	{
	 	$idViatico = $this->__Actualizar();
		
		if($idViatico !== false)
		{
			if($this->__Enviar($idViatico)){
				$GLOBALS['SafiInfo']['general'][] = "Vi&aacute;tico nacional \"".$idViatico."\" modificado y enviado satisfactoriamente.";
			} else {
				$GLOBALS['SafiInfo']['general'][] = "El vi&aacute;tico nacional \"".$idViatico."\" fue modificado satisfactoriamente,
					pero no se pudo enviar. Intente enviarlo m&aacute;s tarde.";
			}
			
			$this->__VerDetalles(array('idViaticoNacional' => $idViatico));
			
		} else {
			$this->__DesplegarFormularioViatico();
		}
	}

	public function Anular()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		
		$idPerfil = $_SESSION['user_perfil_id'];
		$loginUsuario = $_SESSION['login'] ;
	
		// Validar el id del viatico nacional
		if(!isset($_REQUEST['idViaticoNacional']) || trim($_REQUEST['idViaticoNacional']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Identificador del vi&aacute;tico nacional no encontrado.';
		} else {
			
			$idViatico = trim($_REQUEST['idViaticoNacional']);
		
			// Identificar el perfil que desea hacer la anulación
			if(	
				substr($idPerfil,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO ||
				$idPerfil == PERFIL_ASISTENTE_EJECUTIVO ||
				$idPerfil == PERFIL_ASISTENTE_PRESIDENCIA
			){
				$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idViatico);
					
				$estadoAnulado = 15;
				$wfObjetoAnulado = 98;
				$wFOpcionAnular = 24;
				
				$docGenera->SetIdWFObjeto($wfObjetoAnulado);
				$docGenera->SetIdWFCadena(0);
				$docGenera->SetIdEstatus($estadoAnulado);
				$docGenera->SetIdPerfilActual(null);
				
				$revisiones = new EntidadRevisionesDoc();
				$revisiones->SetIdDocumento($idViatico);
				$revisiones->SetLoginUsuario($loginUsuario);
				$revisiones->SetIdPerfil($idPerfil);
				$revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
				$revisiones->SetIdWFOpcion($wFOpcionAnular);
				
				// Guardar el registro del documento en docGenera (estado de la cadena)
				if(SafiModeloViaticoNacional::AnularViaticoNacional($docGenera, $revisiones) === false){
					$GLOBALS['SafiErrors']['general'][] = 'Error al anular.';
				} else {
					$GLOBALS['SafiInfo']['general'][] = "Vi&aacute;tico nacional \"".$idViatico."\" anulado satisfactoriamente.";
				}
			}
		}
		
		$this->Bandeja();
	
	}
	
	public function VerCategoriaViaticoInfo(){
		
		$categoriaViaticos = SafiModeloCategoriaViatico::GetAllCategoriasActivas();
		
		
		$GLOBALS['SafiRequestVars']['categoriaViaticos'] = $categoriaViaticos;
		
		require(SAFI_VISTA_PATH . "/vina/categoriaViaticoMasInfo.php");
	}
	
	public function Reporte1()
	{
		$form = FormManager::GetForm(FORM_REPORTE_1_VIATICO_NACIONAL);
		
		$idDependencia = $_SESSION['user_depe_id'];
		$idUserPerfil = $_SESSION['user_perfil_id'];
		
		// Validar la fecha de inicio
		if(!isset($_POST['fechaInicio']) || trim($_POST['fechaInicio']) == ''){
			//$GLOBALS['SafiErrors']['general'][] = 'Debe indicar la fecha de inicio del viaje';
		} else {
			$fecha = explode('/', $_POST['fechaInicio']);
			if (count($fecha) != 3){
				$GLOBALS['SafiErrors']['general'][] = 'Fecha de inicio inv&aacute;lida.';
			} else {
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				if(!checkdate ($month ,$day ,$year)){
					$GLOBALS['SafiErrors']['general'][] = 'Fecha de inicio inv&aacute;lida.';
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
				$GLOBALS['SafiErrors']['general'][] = 'Fecha de fin inv&aacute;lida.';
			} else {
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				if(!checkdate ($month ,$day ,$year)){
					$GLOBALS['SafiErrors']['general'][] = 'Fecha de fin inv&aacute;lida.';
				} else {
					$form->SetFechaFin($day . '/' . $month . '/' . $year);
				}
			}
		}
	
		// Validar el id del viatico nacional
		if(!isset($_POST['idViaticoNacioanal']) || trim($_POST['idViaticoNacioanal']) == ''){
		
		} else {
			$form->SetIdViaticoNacional($_POST['idViaticoNacioanal']);
		}
		
		$params = array();
		
		$params['fechaInicio'] = $form->GetFechaInicio();
		$params['fechaFin'] = $form->GetFechaFin();
		$params['idViaticoNacional'] = $form->GetIdViaticoNacional();
		
		$viaticosNacionales = SafiModeloViaticoNacional::GetReporte1ViaticoNacional($params);
		$form->SetViaticosNacionales($viaticosNacionales);
		
		require(SAFI_VISTA_PATH ."/vina/reporte1ViaticoNacional.php");
	}
	
	public function ReporteResponsables()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		$caracterProyecto = "p";
		$caracterAccionCentralizada = "a";
		$caracterSeparacionAccionesEspecificas = "__";
			
		try
		{
			$form = FormManager::GetForm(FORM_REPORTE_RESPONSABLES_VIATICO);
			$existCriteria = false;
			$estatusRendicion = null;
			$idEstado = null;
			$idRegionReporte = null;
			
			$idDependencia = $_SESSION['user_depe_id'];
			$idUserPerfil = $_SESSION['user_perfil_id'];
			
			// Obtener todas las acciones específicas de proyectos aprobadas
			$proyectoAccionesEspecificas = SafiModeloProyecto::GetAllAccionesEspecificasAprobadas();
			// Obtener todas las acciones específicas de acciones centralizadas aprobadas
			$accionAccionesEspecificas = SafiModeloAccionCentralizada::GetAllAccionesEspecificasAprobadas();
			
			// Unir las acciones específicas de proyectos y de acciones centralizadas
			$accionesEspecificas = array();
			foreach ($proyectoAccionesEspecificas AS $proyectoAccionEspecifica)
			{
				$accionesEspecificas["p".$caracterSeparacionAccionesEspecificas.$proyectoAccionEspecifica['proy_id']
					.$caracterSeparacionAccionesEspecificas.$proyectoAccionEspecifica['paes_id']] = $proyectoAccionEspecifica;
			}
			foreach ($accionAccionesEspecificas AS $accionAccionEspecifica)
			{
				$accionesEspecificas["a".$caracterSeparacionAccionesEspecificas.$accionAccionEspecifica['acce_id']
					.$caracterSeparacionAccionesEspecificas.$accionAccionEspecifica['aces_id']] = $accionAccionEspecifica;
			}
			
			// Validar la fecha de inicio del viático
			if(isset($_POST['fechaInicio']) && trim($_POST['fechaInicio']) != '')
			{
				$fecha = explode('/', trim($_POST['fechaInicio']));
				
				if (count($fecha) != 3)
					throw new Exception("Fecha inicio del vi&aacute;tico inv&aacute;lida.");
				
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				
				if(!checkdate ($month ,$day ,$year))
					throw new Exception("Fecha inicio del vi&aacute;tico inv&aacute;lida.");
				
				$form->SetFechaInicio($day . '/' . $month . '/' . $year);
				
				$existCriteria = true;
			}
			
			// Validar la fecha de fin del viático
			if(isset($_POST['fechaFin']) && trim($_POST['fechaFin']) != '')
			{
				$fecha = explode('/', trim($_POST['fechaFin']));
				
				if (count($fecha) != 3)
					throw new Exception("Fecha fin del vi&aacute;tico inv&aacute;lida.");
				
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				
				if(!checkdate ($month ,$day ,$year))
					throw new Exception("Fecha fin del vi&aacute;tico inv&aacute;lida.");
				
				$form->SetFechaFin($day . '/' . $month . '/' . $year);
				
				$existCriteria = true;
			}
			
			// Validar la fecha de inicio de la rendición de viático
			if(isset($_POST['fechaRendicionInicio']) && trim($_POST['fechaRendicionInicio']) != '')
			{
				$fecha = explode('/', trim($_POST['fechaRendicionInicio']));
				
				if (count($fecha) != 3)
					throw new Exception("Fecha inicio de la rendici&oacute;n inv&aacute;lida.");
				
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				
				if(!checkdate ($month ,$day ,$year))
					throw new Exception("Fecha inicio de la rendici&oacute;n inv&aacute;lida.");
				
				$form->SetFechaRendicionInicio($day . '/' . $month . '/' . $year);
				
				$existCriteria = true;
			}
			
			// Validar la fecha de fin de la rendición de viático
			if(isset($_POST['fechaRendicionFin']) && trim($_POST['fechaRendicionFin']) != '')
			{
				$fecha = explode('/', trim($_POST['fechaRendicionFin']));
				
				if (count($fecha) != 3)
					throw new Exception("Fecha fin de la rendición inv&aacute;lida.");
				
				$day = $fecha[0];
				$month = $fecha[1];
				$year = $fecha[2];
				
				if(!checkdate ($month ,$day ,$year))
					throw new Exception("Fecha fin de la rendición inv&aacute;lida.");
				
				$form->SetFechaRendicionFin($day . '/' . $month . '/' . $year);
				
				$existCriteria = true;
			}
			
			// Validar el estatus de rendición del viático nacional
			if(isset($_POST['estatusRendicion']) && ($__estatusRendicion=trim($_POST['estatusRendicion'])) != '' && $__estatusRendicion != "0")
			{
				$form->SetEstatusRendicion($__estatusRendicion);
				$estatusRendicion = $__estatusRendicion;
				$existCriteria = true;
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
			
			// Validar el centro gestor/costo
			if(isset($_POST['centroGestorCosto']) && ($__centroGestorCosto=trim($_POST['centroGestorCosto'])) != '' && $__centroGestorCosto != "0")
			{
				$form->SetCentroGestorCosto($__centroGestorCosto);
				if(
					is_array($accionesEspecificas) && isset($accionesEspecificas[$__centroGestorCosto] )
					&& is_array($accionEspecifica = $accionesEspecificas[$__centroGestorCosto]) )
				{
					if(strcmp(substr($__centroGestorCosto, 0, 1), $caracterProyecto) == "0"){
						$form->SetTipoProyectoAccionCentralizada('proyecto');
						$form->SetIdProyectoAccionCentralizada($accionEspecifica['proy_id']);
						$form->SetIdAccionEspecifica($accionEspecifica['paes_id']);
						$existCriteria = true;
					} else if(strcmp(substr($__centroGestorCosto, 0, 1), $caracterAccionCentralizada) == "0"){
						$form->SetTipoProyectoAccionCentralizada('accionCentralizada');
						$form->SetIdProyectoAccionCentralizada($accionEspecifica['acce_id']);
						$form->SetIdAccionEspecifica($accionEspecifica['aces_id']);
						$existCriteria = true;
					}
				}
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
				if($estatusRendicion != null) $params['estatusRendicion'] = $estatusRendicion;
				if($idEstado != null) $params['idEstado'] = $idEstado;
				if($idRegionReporte != null) $params['idRegionReporte'] = $idRegionReporte;
				if($form->GetTipoProyectoAccionCentralizada() != null)
					$params['tipoProyectoAccionCentralizada'] = $form->GetTipoProyectoAccionCentralizada();
				if($form->GetIdProyectoAccionCentralizada() != null) $params['idProyectoAccionCentralizada'] = $form->GetIdProyectoAccionCentralizada();
				if($form->GetIdAccionEspecifica() != null) $params['idAccionEspecifica'] = $form->GetIdAccionEspecifica();
				$params['dependencia'] = $dependencia;
				
				$datosViaticos = SafiModeloViaticoNacional::ReporteResponsables($params);
				$form->SetDatosViaticos($datosViaticos);
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
			$GLOBALS['SafiRequestVars']['accionesEspecificas'] =  $accionesEspecificas;
			
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
		}
		require(SAFI_VISTA_PATH ."/vina/reporteResponsablesViatico.php");
			
	}
	
	// Obtener y validar la fecha del viático
	private function __ValidarFechaViatico($form)
	{
		if(!isset($_POST['fechaViatico']) || trim($_POST['fechaViatico']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Debe indicar la fecha del vi&aacute;tico.';
		} else {
			
			if(($fecha = $this->__ValidarFecha($_POST['fechaViatico'])) !== false){
				$form->SetFechaViatico($fecha);
			} else {
				$GLOBALS['SafiErrors']['general'][] = 'Fecha del vi&acute;tico inv&aacute;lida.';
			}
		}
	}
	
	// Obtener y validar la información del estado del viático
	private function __ValidarEstado($form){
		if(!isset($_POST['estado']) || trim($_POST['estado']) == '' || strcmp(trim($_POST['estado']), '0') == 0){
			$GLOBALS['SafiErrors']['general'][] = 'Debe seleccionar un estado.';
		} else {
			$form->GetEstado()->SetId($_POST['estado']);
		}
	}
	
	
	
	// Obtener y validar la información de la categoría del viático
	private function __ValidarCategoriaViatico($form){
		if(!isset($_POST['categoria']) || trim($_POST['categoria']) == '' || strcmp(trim($_POST['categoria']), '0') == 0){
			$GLOBALS['SafiErrors']['general'][] = 'Debe seleccionar una categor&iacute;a.';
		} else {
			$form->GetCategoriaViatico()->SetId($_POST['categoria']);
		}
	}
	
	// Obtener y validar la información de la red
	private function __ValidarRed($form){
		if(	isset($_POST['red']) && trim($_POST['red']) != '' &&
			isset($_POST['categoria']) && trim($_POST['categoria']) != '' && strcmp(trim($_POST['categoria']), '2') == 0){
			$form->GetRed()->SetId($_POST['red']);
		}
	}
	
	// Obtener y validar la información del responsable
	private function __ValidarResponsable($form){
		$formResponsable = $form->GetResponsable();
		
		if(!isset($_POST['responsable']) || trim($_POST['responsable']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Debe seleccionar un responsable.';
		} else {
			$formResponsable->SetCedula($_POST['responsable']);
			
			// Validar tipo de responsable (Empleado/Beneficiario)
			if(!isset($_POST['tipoResponsable']) || trim($_POST['tipoResponsable']) == ''){
				$GLOBALS['SafiErrors']['general'][] = 'No se puede identificar el tipo de responsable.';
			} else if($_POST['tipoResponsable'] != 'empleado' && $_POST['tipoResponsable'] != 'beneficiario'){
				$GLOBALS['SafiErrors']['general'][] = 'El tipo del responsable es inv&aacute;lido.';
			} else{
				$formResponsable->SetTipoResponsable($_POST['tipoResponsable']);
				
				$responsable = SafiModeloResponsableViatico::GetResponsableByTipoYCedula($formResponsable->GetTipoResponsable(), $formResponsable->GetCedula());
				
				$formResponsable->SetCedula($responsable->GetCedula());
				$formResponsable->SetNombres($responsable->GetNombres());
				$formResponsable->SetApellidos($responsable->GetApellidos());
				$formResponsable->SetNacionalidad($responsable->GetNacionalidad());
				$formResponsable->SetIdDependencia($responsable->GetIdDependencia());
				$formResponsable->SetTipoEmpleado($responsable->GetTipoEmpleado());
			}
		}
	}
	
	// obtener y validar la información de la cuenta bancaria
	private function __ValidarCuentaBancaria($responsable){
		$existeInfo = false;
		$msgErrors = array(); 
		
		// Validar número de cuenta
		if(isset($_POST['numeroCuenta']) && trim($_POST['numeroCuenta']) != ''){
			$existeInfo = true;
			$numeroCuenta = trim($_POST['numeroCuenta']);
			$responsable->SetNumeroCuenta($numeroCuenta);
			if (mb_strlen($numeroCuenta) == 20) {
				if(!SafiEsCadenaDigitosNumericos($numeroCuenta)){
					$msgErrors[] = 'El n&uacute;mero de cuenta bancaria debe contener solo caracteres num&eacute;ricos';
				}
			} else {
				$msgErrors[] = 'El n&uacute;mero de cuenta bancaria debe tener 20 d&iacute;gitos';
			}
			
		} else {
			$msgErrors[] = 'Debe indicar un n&uacute;mero de cuenta bancaria';
		}
		
		// Validar tipo de cuenta
		if(isset($_POST['tipoCuenta']) && trim($_POST['tipoCuenta']) != '' && strcmp(trim($_POST['tipoCuenta']), "0") != 0){
			$existeInfo = true;
			$responsable->SetTipoCuenta(trim($_POST['tipoCuenta']));
		} else {
			$msgErrors[] = 'Debe indicar un tipo de cuenta bancaria';
		}
		
		// Validar banco
		if(isset($_POST['banco']) && trim($_POST['banco']) != ''){
			$existeInfo = true;
			$responsable->SetBanco($_POST['banco']);			
		} else {
			$msgErrors[] = 'Debe indicar un banco para la cuenta bancaria';
		}
		
		if($existeInfo){
			foreach($msgErrors as $msgError){
				$GLOBALS['SafiErrors']['general'][] = $msgError; 
			}
		}
	}
	
	// Obtener y validar el tipo de categoría programática
	private function __ValidarTipoCategoriaProgramatica($form){
		if(!isset($_POST['tipoProyectoAccionCentralizada']) || trim($_POST['tipoProyectoAccionCentralizada']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Debe seleccionar un tipo (Proyecto/Acci&oacute;n centralizada).';
		} else if($_POST['tipoProyectoAccionCentralizada'] != 'proyecto' &&
				$_POST['tipoProyectoAccionCentralizada'] != 'accionCentralizada'){
			$GLOBALS['SafiErrors']['general'][] = 'El tipo de Proyecto/Acci&oacute;n centralizada es incorrecto.';
		} else {
			$form->SetTipoProyectoAccionCentralizada($_POST['tipoProyectoAccionCentralizada']);
		}
	}
	
	// Obtener y validar de forma general el id del proyecto/accion centralizada
	private function __ValidarIdProyectoAccionCentralizada($form){
		if(	!isset($_POST['proyectoAccionCentralizada']) || trim($_POST['proyectoAccionCentralizada']) == '' ||
			trim($_POST['proyectoAccionCentralizada']) == '0'
		){
			$GLOBALS['SafiErrors']['general'][] = 'Debe seleccionar un Proyecto/Acci&oacute;n centralizada.';
		} else
		{
			$form->SetIdProyectoAccionCentralizada($_POST['proyectoAccionCentralizada']);
		}
	}
	
	// Obtener y validar de forma general el id de la acción específica
	private function __ValidarIdAccionEspecifica($form){
		
		if(	!isset($_POST['accionEspecifica']) || trim($_POST['accionEspecifica']) == '' ||
			trim($_POST['accionEspecifica']) == '0'
		){
			$GLOBALS['SafiErrors']['general'][] = 'Debe seleccionar una acci&oacute;n espec&iacute;fica.';
		} else {
			$form->SetIdAccionEspecifica($_POST['accionEspecifica']);
		}
	}
	
	// Obtener y validar la fecha de inicio del viaje
	private function __ValidarFechaInicioViaje($form){
		if(!isset($_POST['fechaInicioViaje']) || trim($_POST['fechaInicioViaje']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Debe indicar la fecha inicio del viaje.';
		} else {
			if(($fecha = $this->__ValidarFecha($_POST['fechaInicioViaje'])) !== false){
				$form->SetFechaInicioViaje($fecha);
			} else {
				$GLOBALS['SafiErrors']['general'][] = 'Fecha inicio del viaje inv&aacute;lida.';
			}
		}
	}
	
	// Obtener y validar la fecha de fin del viaje
	private function __ValidarFechaFinViaje($form){
		if(!isset($_POST['fechaFinViaje']) || trim($_POST['fechaFinViaje']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Debe indicar la fecha fin del viaje.';
		} else {
			if(($fecha = $this->__ValidarFecha($_POST['fechaFinViaje'])) !== false){
				$form->SetFechaFinViaje($fecha);
			} else {
				$GLOBALS['SafiErrors']['general'][] = 'Fecha fin del viaje inv&aacute;lida.';
			}
		}
	}
	
	// Obtener y validar los objetivos del viaje
	private function __ValidarObjetivosViaje($form){
		if(!isset($_POST['objetivosViaje']) || trim($_POST['objetivosViaje']) == ''){
			$GLOBALS['SafiErrors']['general'][] = 'Debe indicar los objetivos del viaje.';
		} else {
			$form->SetObjetivosViaje($_POST['objetivosViaje']);
		}
	}
	
	// Obtener y validar los infocentros
	private function __ValidarInfocentros($form){
		if(isset($_POST['infocentros']) && is_array($_POST['infocentros']))
		{
			$infocentros = SafiModeloInfocentro::GetInfocentrosByIds($_POST['infocentros']);
			$form->SetInfocentros($infocentros);
		}
		
	}
	
	private function __ValidarRutas($form){
		
		$tipoTransportes = SafiModeloTipoTransporte::GetTipoTransportesActivos();
		
		$rutas = array();
		
		// Validar las rutas
		if(!isset($_POST['rutas']) || !is_array($_POST['rutas'])){
			$GLOBALS['SafiErrors']['general'][] = 'Debe indicar al menos una ruta de viaje.';
		} else {
			$countRutas = count($_POST['rutas']);
			if($countRutas > 0)
			{
				// Crear las entidades rutas
				foreach($_POST['rutas'] as $ruta){
					$rutas[] = new EntidadRuta();
				}
				
				// Validar id de la ruta
				if(!isset($_POST['idRuta']) || !is_array($_POST['idRuta'])){
					$GLOBALS['SafiErrors']['general'][] = 'Una o mas rutas carecen de id.';
				} else {
					$countId = count($_POST['idRuta']);
					if($countRutas != $countId) {
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de rutas y de ids de rutas no coincide.';
					} else {
						$indexRutasLocal = 0;
						foreach($_POST['idRuta'] as $idRuta)
						{					
							if(trim($idRuta) == '' || strcmp($idRuta, "0")==0){
								$rutas[$indexRutasLocal]->SetId("0");
							} else {
								if(!SafiIsId($idRuta)){
									$GLOBALS['SafiErrors']['general'][] = 'El id de rutas['.($indexRutasLocal+1).
										'] debe ser num&eacute;rico.';
								} else {
									$rutas[$indexRutasLocal]->SetId($idRuta);
								}
							}
							$indexRutasLocal++;
						}
					}
				}
				
				// Validar fecha de inicio de la ruta
				if(!isset($_POST['fechaInicioRuta']) || !is_array($_POST['fechaInicioRuta'])){
					$GLOBALS['SafiErrors']['general'][] = 'Una o mas rutas carecen de fecha inicio.';
				} else {
					$countFechaInicio = count($_POST['fechaInicioRuta']);
					if($countRutas != $countFechaInicio) {
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de rutas y de fecha inicio de rutas no coincide.';
					} else {
						$indexRutasLocal = 0;
						foreach($_POST['fechaInicioRuta'] as $fechaInicioRuta)
						{					
							if(trim($fechaInicioRuta) == ''){
								$GLOBALS['SafiErrors']['general'][] = 'Debe indicar una fecha inicio de rutas['.($indexRutasLocal+1).'].';
							} else {
								if(($fecha = $this->__ValidarFecha($fechaInicioRuta)) !== false){
									$rutas[$indexRutasLocal]->SetFechaInicio($fecha);
								} else {
									$GLOBALS['SafiErrors']['general'][] = 'Fecha de inicio rutas['.($indexRutasLocal+1)
										.'] inv&aacute;lida.';
								}
							}
							$indexRutasLocal++;
						}
					}
				}
				
				// Validar fecha de fin de la ruta
				if(!isset($_POST['fechaFinRuta']) || !is_array($_POST['fechaFinRuta'])){
					$GLOBALS['SafiErrors']['general'][] = 'Una o mas rutas carecen de fecha fin.';
				} else {
					$countFechaFin = count($_POST['fechaFinRuta']);
					if($countRutas != $countFechaFin) {
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de rutas y de fecha fin de rutas no coincide.';
					} else {
						$indexRutasLocal = 0;
						foreach($_POST['fechaFinRuta'] as $fechaFinRuta)
						{
							if(trim($fechaFinRuta) == ''){
								$GLOBALS['SafiErrors']['general'][] = 'Debe indicar una fecha fin de rutas['.($indexRutasLocal+1).'].';
							} else {
								if(($fecha = $this->__ValidarFecha($fechaFinRuta)) !== false){
									$rutas[$indexRutasLocal]->SetFechaFin($fecha);
								} else {
									$GLOBALS['SafiErrors']['general'][] = 'Fecha de fin rutas['.($indexRutasLocal+1).'] inv&aacute;lida.';
								}
							}
							$indexRutasLocal++;
						}
					}
				}
				
				// Validar días a cancelar por hospedaje
				$totalUnidad = 0;
				if(!isset($_POST['diasHospedaje']) || !is_array($_POST['diasHospedaje'])){
					$GLOBALS['SafiErrors']['general'][] = 'Una o mas rutas carecen de d&iacute;as a cancelar por hospedaje.';
				} else {
					$countDiasHospedaje = count($_POST['diasHospedaje']);
					if($countRutas != $countDiasHospedaje) {
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de rutas y de &iacute;as a
							cancelar por hospedaje no coincide.';
					} else {
						$indexRutasLocal = 0;
						foreach($_POST['diasHospedaje'] as $diasHospedaje){
							if($diasHospedaje != null && $diasHospedaje != ''){
								if(!SafiIsInt($diasHospedaje)){
									$GLOBALS['SafiErrors']['general'][] = 'El campo d&iacute;as a cancelar por hospedaje['.
										($indexRutasLocal+1).'] debe ser num&eacute;rico.';
								} else {
									$rutas[$indexRutasLocal]->SetDiasHospedaje($diasHospedaje);
									$totalUnidad += $rutas[$indexRutasLocal]->GetDiasHospedaje();
								}
							}
							
							$indexRutasLocal++;
						}
					}
				}
				$form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_HOSPEDAJE)->
					SetUnidades($totalUnidad);
				
				// Validar días a cancelar por alimentación
				$totalUnidad = 0;
				if(!isset($_POST['diasAlimentacion']) || !is_array($_POST['diasAlimentacion'])){
					$GLOBALS['SafiErrors']['general'][] = 'Una o mas rutas carecen de d&iacute;as a cancelar por alimentaci&oacute;n.';
				} else {
					$countDiasAlimentacion = count($_POST['diasAlimentacion']);
					if($countRutas != $countDiasAlimentacion) {
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de rutas y de &iacute;as a cancelar por
							alimentaci&oacute;n no coincide.';
					} else {
						$indexRutasLocal = 0;
						foreach($_POST['diasAlimentacion'] as $diasAlimentacion){
							if($diasAlimentacion != null && $diasAlimentacion != ''){
								if(!SafiIsInt($diasAlimentacion)){
									$GLOBALS['SafiErrors']['general'][] = 'El campo d&iacute;as a cancelar por alimentaci&oacute;n['.
										($indexRutasLocal+1).'] debe ser num&eacute;rico.';
								} else {
									$rutas[$indexRutasLocal]->SetDiasAlimentacion($diasAlimentacion);
									$totalUnidad += $rutas[$indexRutasLocal]->GetDiasAlimentacion();
								}
							}
							$indexRutasLocal++;
						}
					}
				}
				$form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_ALIMENTACION)->
					SetUnidades($totalUnidad);
				
				// Validar días a cancelar por transporte interurbano
				$totalUnidad = 0;
				if(!isset($_POST['unidadTransporteInterurbano']) || !is_array($_POST['unidadTransporteInterurbano'])){
					$GLOBALS['SafiErrors']['general'][] = 'Una o mas rutas carecen de d&iacute;as a cancelar por transporte interurbano.';
				} else {
					$countDiasTransporteInterurbano = count($_POST['unidadTransporteInterurbano']);
					if($countRutas != $countDiasTransporteInterurbano) {
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de rutas y de &iacute;as a cancelar por transporte
							interurbano no coincide.';
					} else {
						$indexRutasLocal = 0;
						foreach($_POST['unidadTransporteInterurbano'] as $diasTransporteInterurbano){
							if($diasTransporteInterurbano != null && $diasTransporteInterurbano != ''){
								if(!SafiIsInt($diasTransporteInterurbano)){
									$GLOBALS['SafiErrors']['general'][] = 'El campo d&iacute;as a cancelar por transporte interurbano['.
										($indexRutasLocal+1).'] debe ser num&eacute;rico.';
								} else {
									$rutas[$indexRutasLocal]->SetUnidadTransporteInterurbano($diasTransporteInterurbano);
									$totalUnidad += $rutas[$indexRutasLocal]->GetUnidadTransporteInterurbano();								
								}
							}
							$indexRutasLocal++;
						}
					}
				}
				$form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_TRANSPORTE_INTERURBANO)->
					SetUnidades($totalUnidad);
				
				// Validar tipo de transporte
				$totalUnidadTasaAeroportuaria = 0;
				if(!isset($_POST['tipoTransporte']) || !is_array($_POST['tipoTransporte'])){
					$GLOBALS['SafiErrors']['general'][] = 'Una o mas rutas carecen de tipo de transporte.';
				} else {
					$countTipoTransporte = count($_POST['tipoTransporte']);
					if($countRutas != $countTipoTransporte) {
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de rutas y tipo de transportes no coincide.';
					} else {
						$indexRutasLocal = 0;
						foreach($_POST['tipoTransporte'] as $tipoTransporte){
							if(trim($tipoTransporte) == '' || (strcmp(trim($tipoTransporte), "0")==0)){
								$GLOBALS['SafiErrors']['general'][] = 'Debe indicar el tipo de transporte de rutas['
									.($indexRutasLocal+1).'].';
							} else {
								if(!SafiIsInt($tipoTransporte)){
									$GLOBALS['SafiErrors']['general'][] = 'El campo tipo de transportes['.
										($indexRutasLocal+1).'] debe ser num&eacute;rico.';
								} else {
									$rutas[$indexRutasLocal]->SetIdTipoTransporte($tipoTransporte);
									$idTipoTransporte = $rutas[$indexRutasLocal]->GetIdTipoTransporte();
									if(isset($tipoTransportes[$idTipoTransporte]) &&
										$tipoTransportes[$idTipoTransporte]['tipo'] == EntidadTipoTransporte::TIPO_AEREO)
									{
										$totalUnidadTasaAeroportuaria++;
									}
								}
							}
							$indexRutasLocal++;
						}
					}
				}
				
				// Validar pasaje ida y vuelta
				if(!isset($_POST['pasajeIdaVuelta']) || !is_array($_POST['pasajeIdaVuelta'])){
					$GLOBALS['SafiErrors']['general'][] = 'Una o mas rutas carecen del pasaje ida y vuelta.';
				} else {
					$countPasajeIdaVuelta = count($_POST['pasajeIdaVuelta']);
					if($countRutas != $countPasajeIdaVuelta) {
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de rutas y de pasaje ida y vuelta no coincide.';
					} else {
						$indexRutasLocal = 0;
						
						foreach($_POST['pasajeIdaVuelta'] as $pasajeIdaVuelta)
						{
							if(trim($pasajeIdaVuelta) == ''){
								$GLOBALS['SafiErrors']['general'][] = 'Debe indicar si desea o no el pasaje ida y
									vuelta['.($indexRutasLocal+1).'].';
							} else {
								$pasajeIdaVuelta = (strcasecmp($pasajeIdaVuelta, 'true') == 0 ) ? true : false;
								
								// Validar si para la ruta en $indexRutasLocal el tipo de transporte es aéreo
								$idTipoTransporte = $rutas[$indexRutasLocal]->GetIdTipoTransporte();
								if(isset($tipoTransportes[$idTipoTransporte]) &&
									$tipoTransportes[$idTipoTransporte]['tipo'] == EntidadTipoTransporte::TIPO_AEREO &&
									$pasajeIdaVuelta == true)
								{
									$rutas[$indexRutasLocal]->SetPasajeIdaVuelta($pasajeIdaVuelta);
									$totalUnidadTasaAeroportuaria++;
								}
							}
							$indexRutasLocal++;
						}
					}
				}
				/*
				$form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_TASA_AEROPORTUARIA)->
					SetUnidades($totalUnidadTasaAeroportuaria);
				*/
				
				// Validar transporte residencia - aeropuerto
				$totalUnidad = 0;
				if(!isset($_POST['residenciaAeropuerto']) || !is_array($_POST['residenciaAeropuerto'])){
					$GLOBALS['SafiErrors']['general'][] = 'Una o mas rutas carecen del transporte residencia - aeropuerto.';
				} else {
					$countResidenciaAeropuerto = count($_POST['residenciaAeropuerto']);
					if($countRutas != $countResidenciaAeropuerto) {
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de rutas y de transporte residencia - aeropuerto
							no coincide.';
					} else {
						$indexRutasLocal = 0;
						foreach($_POST['residenciaAeropuerto'] as $residenciaAeropuerto)
						{
							if(trim($residenciaAeropuerto) == ''){
								$GLOBALS['SafiErrors']['general'][] = 'Debe indicar si desea o no el transporte residencia - aeropuerto['
									.($indexRutasLocal+1).'].';
							} else {
								$residenciaAeropuerto = (strcasecmp($residenciaAeropuerto, 'true') == 0 ) ? true : false;
								
								// Validar si para la ruta en $indexRutasLocal el tipo de transporte es aéreo
								$idTipoTransporte = $rutas[$indexRutasLocal]->GetIdTipoTransporte();
								if(isset($tipoTransportes[$idTipoTransporte]) &&
									$tipoTransportes[$idTipoTransporte]['tipo'] == EntidadTipoTransporte::TIPO_AEREO &&
									$residenciaAeropuerto == true)
								{
									$rutas[$indexRutasLocal]->SetResidenciaAeropuerto($residenciaAeropuerto);
									$totalUnidad++;
								}
								
							}
							$indexRutasLocal++;
						}
					}
				}
				$form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_RESIDENCIA_AEROPUERTO)->
					SetUnidades($totalUnidad);
					
				// Validar transporte aeropuerto - residencia
				$totalUnidad = 0;
				if(!isset($_POST['aeropuertoResidencia']) || !is_array($_POST['aeropuertoResidencia'])){
					$GLOBALS['SafiErrors']['general'][] = 'Una o mas rutas carecen del transporte aeropuerto - residencia.';
				} else {
					$countAeropuertoResidencia = count($_POST['aeropuertoResidencia']);
					if($countRutas != $countAeropuertoResidencia) {
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de rutas y de transporte aeropuerto - residencia no
							coincide.';
					} else {
						$indexRutasLocal = 0;
						foreach($_POST['aeropuertoResidencia'] as $aeropuertoResidencia)
						{
							if(trim($aeropuertoResidencia) == ''){
								$GLOBALS['SafiErrors']['general'][] = 'Debe indicar si desea o no el transporte
									aeropuerto - residencia['.($indexRutasLocal+1).'].';
							} else {
								$aeropuertoResidencia = (strcasecmp($aeropuertoResidencia, 'true') == 0 ) ? true : false;
								
								// Validar si para la ruta en $indexRutasLocal el tipo de transporte es aéreo
								$idTipoTransporte = $rutas[$indexRutasLocal]->GetIdTipoTransporte();
								$pasajeIdaVuelta = $rutas[$indexRutasLocal]->GetPasajeIdaVuelta();
								$residenciaAeropuerto = $rutas[$indexRutasLocal]->GetResidenciaAeropuerto();
								if(isset($tipoTransportes[$idTipoTransporte]) &&
									$tipoTransportes[$idTipoTransporte]['tipo'] == EntidadTipoTransporte::TIPO_AEREO &&
									$aeropuertoResidencia == true)
								{
									if($pasajeIdaVuelta == true){
										$rutas[$indexRutasLocal]->SetAeropuertoResidencia($aeropuertoResidencia);
										$totalUnidad++;
									} else if($residenciaAeropuerto == false){
										$rutas[$indexRutasLocal]->SetAeropuertoResidencia($aeropuertoResidencia);
										$totalUnidad++;
									}
								}
							}
							$indexRutasLocal++;
						}
					}
				}
				$form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_AEROPUERTO_RESIDENCIA)->
					SetUnidades($totalUnidad);
				
				// Validar tasa aeroportuaria ida
				$totalUnidadTasaAeroportuaria = 0;
				if(!isset($_POST['tasaAeroportuariaIda']) || !is_array($_POST['tasaAeroportuariaIda'])){
					$GLOBALS['SafiErrors']['general'][] = 'Una o mas rutas carecen de tasa aeroportuaria de ida';
				} else {
					if($countRutas != count($_POST['tasaAeroportuariaIda'])) {
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de rutas y de tasa aeroportuaria de ida no coincide.';
					} else {
						$indexRutasLocal = 0;
						foreach($_POST['tasaAeroportuariaIda'] as $tasaAeroportuariaIda)
						{
							if(trim($tasaAeroportuariaIda) == ''){
								$GLOBALS['SafiErrors']['general'][] = 'Debe indicar si desea o no la tasa
									aeroportuaria de ida['.($indexRutasLocal+1).'].';
							} else {
								$tasaAeroportuariaIda = (strcasecmp(trim($tasaAeroportuariaIda), 'true') == 0 ) ? true : false;
								
								// Validar si para la ruta en $indexRutasLocal el tipo de transporte es aéreo
								$idTipoTransporte = $rutas[$indexRutasLocal]->GetIdTipoTransporte();
								if(isset($tipoTransportes[$idTipoTransporte]) &&
									$tipoTransportes[$idTipoTransporte]['tipo'] == EntidadTipoTransporte::TIPO_AEREO &&
									$tasaAeroportuariaIda == true)
								{
									$rutas[$indexRutasLocal]->SetTasaAeroportuariaIda($tasaAeroportuariaIda);
									$totalUnidadTasaAeroportuaria++;
								}
							}
							$indexRutasLocal++;
						}
					}
				}
				
				// Validar tasa aeroportuaria vuelta
				if(!isset($_POST['tasaAeroportuariaVuelta']) || !is_array($_POST['tasaAeroportuariaVuelta'])){
					$GLOBALS['SafiErrors']['general'][] = 'Una o mas rutas carecen de tasa aeroportuaria de vuelta';
				} else {
					if($countRutas != count($_POST['tasaAeroportuariaVuelta'])) {
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de rutas y de tasa aeroportuaria de vuelta no coincide.';
					} else {
						$indexRutasLocal = 0;
						foreach($_POST['tasaAeroportuariaVuelta'] as $tasaAeroportuariaVuelta)
						{
							if(trim($tasaAeroportuariaVuelta) == ''){
								$GLOBALS['SafiErrors']['general'][] = 'Debe indicar si desea o no la tasa
									aeroportuaria de vuelta['.($indexRutasLocal+1).'].';
							} else {
								$tasaAeroportuariaVuelta = (strcasecmp(trim($tasaAeroportuariaVuelta), 'true') == 0 ) ? true : false;
								
								// Validar si para la ruta en $indexRutasLocal el tipo de transporte es aéreo
								$idTipoTransporte = $rutas[$indexRutasLocal]->GetIdTipoTransporte();
								$pasajeIdaVuelta = $rutas[$indexRutasLocal]->GetPasajeIdaVuelta();
								$tasaAeroportuariaIda = $rutas[$indexRutasLocal]->GetTasaAeroportuariaIda();
								if(isset($tipoTransportes[$idTipoTransporte]) &&
									$tipoTransportes[$idTipoTransporte]['tipo'] == EntidadTipoTransporte::TIPO_AEREO &&
									$tasaAeroportuariaVuelta == true)
								{
									if($pasajeIdaVuelta == true){
										$rutas[$indexRutasLocal]->SetTasaAeroportuariaVuelta($tasaAeroportuariaVuelta);
										$totalUnidadTasaAeroportuaria++;
									} else if($tasaAeroportuariaIda == false){
										$rutas[$indexRutasLocal]->SetTasaAeroportuariaVuelta($tasaAeroportuariaVuelta);
										$totalUnidadTasaAeroportuaria++;
									}
								}
								
							}
							$indexRutasLocal++;
						}
					}
				}
				
				// Establecer el monto de la tasa aeroportuaria
				$form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_TASA_AEROPORTUARIA)->
					SetUnidades($totalUnidadTasaAeroportuaria);
					
				// Validar observaciones
				if(!isset($_POST['observacionesRutas']) || !is_array($_POST['observacionesRutas'])){
					$GLOBALS['SafiErrors']['general'][] = 'Una o mas rutas carecen del campo observaciones.';
				} else {
					$countObservaciones = count($_POST['observacionesRutas']);
					if($countRutas != $countObservaciones) {
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de rutas y de observaciones de rutas no coincide.';
					} else {
						$indexRutasLocal = 0;
						foreach($_POST['observacionesRutas'] as $observaciones)
						{
							// Validar si para la ruta en $indexRutasLocal el tipo de transporte es aéreo
							$idTipoTransporte = $rutas[$indexRutasLocal]->GetIdTipoTransporte();
							if(isset($tipoTransportes[$idTipoTransporte]) &&
								$tipoTransportes[$idTipoTransporte]['tipo'] == EntidadTipoTransporte::TIPO_AEREO
							){
								if($observaciones != null && $observaciones != ''){
									$rutas[$indexRutasLocal]->SetObservaciones($observaciones);
								}
							}
							$indexRutasLocal++;
						}
					}
				}
					
				// Validar Estados origen
				$this->ValidarSelectDireccion2(array(
					'rutas' => &$rutas,
					'itemRequestName' => 'fromEstados',
					'itemObjectName' => 'idFromEstado',
					'countRutas' => $countRutas,
					'esObligatorio' => true,
					'msjItemsVacios' => 'Una o mas rutas carecen de estado origen',
					'msjItemsContadorDistinto' => 'El n&uacute;mero de rutas y estados origen no coincide',
					'msjItemsNoNumericos' => 'El campo estado origen de rutas[%u] debe ser num&eacute;rico',
					'msjEsobligatorio' => 'Debe indicar un estado de origen de rutas[%u]'
				));
				
				// Validar Ciudades origen
				$this->ValidarSelectDireccion2(array(
					'rutas' => &$rutas,
					'itemRequestName' => 'fromCiudades',
					'itemObjectName' => 'idFromCiudad',
					'countRutas' => $countRutas,
					'msjItemsVacios' => 'Una o mas rutas carecen de ciudad origen',
					'msjItemsContadorDistinto' => 'El n&uacute;mero de rutas y ciudades origen no coincide',
					'msjItemsNoNumericos' => 'El campo ciudad origen de rutas[%u] debe ser num&eacute;rico'
				));
				
				// Validar Municipios origen
				$this->ValidarSelectDireccion2(array(
					'rutas' => &$rutas,
					'itemRequestName' => 'fromMunicipios',
					'itemObjectName' => 'idFromMunicipio',
					'countRutas' => $countRutas,
					'msjItemsVacios' => 'Una o mas rutas carecen de municipio origen',
					'msjItemsContadorDistinto' => 'El n&uacute;mero de rutas y municipios origen no coincide',
					'msjItemsNoNumericos' => 'El campo municipio origen de rutas[%u] debe ser num&eacute;rico'
				));
				
				// Validar parroquias origen
				$this->ValidarSelectDireccion2(array(
					'rutas' => &$rutas,
					'itemRequestName' => 'fromParroquias',
					'itemObjectName' => 'idFromParroquia',
					'countRutas' => $countRutas,
					'msjItemsVacios' => 'Una o mas rutas carecen de parroquia origen',
					'msjItemsContadorDistinto' => 'El n&uacute;mero de rutas y parroquias origen no coincide',
					'msjItemsNoNumericos' => 'El campo parroquia origen de rutas[%u] debe ser num&eacute;rico'
				));
				
				// Validar dirección origen
				$itemRequestName = 'fromDireccion';
				$itemArrayName = $itemRequestName; 
				if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
					$GLOBALS['SafiErrors']['general'][] = 'Una o mas rutas carecen de direcci&oacute;n origen.';
				} else {
					$countItems = count($_POST[$itemRequestName]);
					if($countRutas != $countItems) {
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de rutas y de direcci&oacute;n origen no coincide.';
					} else {
						$indexRutasLocal = 0;
						foreach($_POST[$itemRequestName] as $item){
							if(trim($item) != '')
							{
								$rutas[$indexRutasLocal]->SetFromDireccion($item);
							}
							$indexRutasLocal++;
						}
					}
				}
				
				// Validar Estados destino
				$this->ValidarSelectDireccion2(array(
					'rutas' => &$rutas,
					'itemRequestName' => 'toEstados',
					'itemObjectName' => 'idToEstado',
					'countRutas' => $countRutas,
					'esObligatorio' => true,
					'msjItemsVacios' => 'Una o mas rutas carecen de estado destino',
					'msjItemsContadorDistinto' => 'El n&uacute;mero de rutas y estados destino no coincide',
					'msjItemsNoNumericos' => 'El campo estado destino de rutas[%u] debe ser num&eacute;rico',
					'msjEsobligatorio' => 'Debe indicar un estado de destino de rutas[%u]'
				));
				
				// Validar Ciudades destino
				$this->ValidarSelectDireccion2(array(
					'rutas' => &$rutas,
					'itemRequestName' => 'toCiudades',
					'itemObjectName' => 'idToCiudad',
					'countRutas' => $countRutas,
					'msjItemsVacios' => 'Una o mas rutas carecen de ciudad destino',
					'msjItemsContadorDistinto' => 'El n&uacute;mero de rutas y destino origen no coincide',
					'msjItemsNoNumericos' => 'El campo ciudad destino de rutas[%u] debe ser num&eacute;rico'
				));
				
				// Validar Municipios destino
				$this->ValidarSelectDireccion2(array(
					'rutas' => &$rutas,
					'itemRequestName' => 'toMunicipios',
					'itemObjectName' => 'idToMunicipio',
					'countRutas' => $countRutas,
					'msjItemsVacios' => 'Una o mas rutas carecen de municipio destino',
					'msjItemsContadorDistinto' => 'El n&uacute;mero de rutas y municipios destino no coincide',
					'msjItemsNoNumericos' => 'El campo municipio destino de rutas[%u] debe ser num&eacute;rico'
				));
				
				// Validar parroquias destino
				$this->ValidarSelectDireccion2(array(
					'rutas' => &$rutas,
					'itemRequestName' => 'toParroquias',
					'itemObjectName' => 'idToParroquia',
					'countRutas' => $countRutas,
					'msjItemsVacios' => 'Una o mas rutas carecen de parroquia destino',
					'msjItemsContadorDistinto' => 'El n&uacute;mero de rutas y parroquias destino no coincide',
					'msjItemsNoNumericos' => 'El campo parroquia destino de rutas[%u] debe ser num&eacute;rico'
				));
				
				// Validar dirección destino
				$itemRequestName = 'toDireccion';
				$itemArrayName = $itemRequestName; 
				if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
					$GLOBALS['SafiErrors']['general'][] = 'Una o mas rutas carecen de direcci&oacute;n destino.';
				} else {
					$countItems = count($_POST[$itemRequestName]);
					if($countRutas != $countItems) {
						$GLOBALS['SafiErrors']['general'][] = 'El n&uacute;mero de rutas y de direcci&oacute;n destino no coincide.';
					} else {
						$indexRutasLocal = 0;
						foreach($_POST[$itemRequestName] as $item){
							if(trim($item) != '')
							{
								$rutas[$indexRutasLocal]->SetToDireccion($item);
							}
							$indexRutasLocal++;
						}
					}
				}
				
				$form->SetRutas($rutas);
				
			} else {
				$GLOBALS['SafiErrors']['general'][] = 'Debe indicar al menos una ruta de viaje.';
			}
		}
	}
	
	// Obtener y validar asignación de viaticos: Unidades de asignación por transporte
	private function __ValidarAsignacionTransporte($form){
		if(isset($_POST['unidadAsignacionTransporte']) && trim($_POST['unidadAsignacionTransporte']) != ''){
			$unidad = trim($_POST['unidadAsignacionTransporte']);
			
			if(!SafiIsInt($unidad)){
				$GLOBALS['SafiErrors']['general'][] = 'El campo de las unidades de asignaci&oacute;n por transporte deber
					ser num&eacute;rico.';
			} else {
				$form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_ASIGNACION_TRANSPORTE)->
					SetUnidades($unidad);
			}
		}

	}
	
	// Obtener y validar asignación de viaticos: Unidades de servicio de comunicaciones
	private function __ValidarServicioComunicaciones($form){
		if(isset($_POST['unidadServicioComunicaciones']) && trim($_POST['unidadServicioComunicaciones']) != ''){
			$unidad = trim($_POST['unidadServicioComunicaciones']);
			
			if(!SafiIsInt($unidad)){
				$GLOBALS['SafiErrors']['general'][] = 'El campo de las unidades de servicios de comunicaciones deber ser num&eacute;rico.';
			} else {
				$form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_SERVICIO_COMUNICACIONES)->
					SetUnidades($unidad);
			}
		}
	}
	
	// Obtener y validar el monto y las unidades del tranporte extraurbano
	private function __ValidarTransporteExtraurbano($form)
	{
		// Obtener y validar asignación de viaticos: monto de transporte extraurbano
		if(isset($_POST['montoTransporteExtraurbano']) && trim($_POST['montoTransporteExtraurbano']) != ''){
			$unidad = trim($_POST['montoTransporteExtraurbano']);
			
			$form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_TRANSPORTE_EXTRAURBANO)->
					SetMonto($unidad);
			/*
			if(!SafiIsInt($unidad)){
				$GLOBALS['SafiErrors']['general'][] = 'El campo de las unidades de transporte extraurbano deber ser num&eacute;rico';
			} else {
				$form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_ASIGNACION_TRANSPORTE)->
					SetUnidades($unidad);
			}
			*/
			
		}
		
		// Obtener y validar asignación de viaticos: unidades de transporte extraurbano
		if(isset($_POST['unidadTransporteExtraurbano']) && trim($_POST['unidadTransporteExtraurbano']) != ''){
			$unidad = trim($_POST['unidadTransporteExtraurbano']);
			
			if(!SafiIsInt($unidad)){
				$GLOBALS['SafiErrors']['general'][] = 'El campo de las unidades de transporte extraurbano deber ser num&eacute;rico.';
			} else {
				$form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_TRANSPORTE_EXTRAURBANO)->
					SetUnidades($unidad);
			}
		}
	}
	
	// Obtener y validar asignación de viaticos: Monto y unidades de transporte entre ciudades
	private function __validarTransporteEntreCiudades($form)
	{
		// Obtener y validar asignación de viaticos: Monto de transporte entre ciudades
		if(isset($_POST['montoTransporteCiudades']) && trim($_POST['montoTransporteCiudades']) != ''){
			$unidad = trim($_POST['montoTransporteCiudades']);
			
			$form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_TRANSPORTE_ENTRE_CIUDADES)->
					SetMonto($unidad);
			/*
			if(!SafiIsInt($unidad)){
				$GLOBALS['SafiErrors']['general'][] = 'El campo de las unidades de transporte extraurbano deber ser num&eacute;rico';
			} else {
				$form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_ASIGNACION_TRANSPORTE)->
					SetUnidades($unidad);
			}
			*/
			
		}
		
		// Obtener y validar asignación de viaticos: unidades de transporte entre ciudades
		if(isset($_POST['unidadTransporteCiudades']) && trim($_POST['unidadTransporteCiudades']) != ''){
			$unidad = trim($_POST['unidadTransporteCiudades']);
			
			if(!SafiIsInt($unidad)){
				$GLOBALS['SafiErrors']['general'][] = 'El campo de las unidades de transporte entre ciudades deber ser num&eacute;rico.';
			} else {
				$form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_TRANSPORTE_ENTRE_CIUDADES)->
					SetUnidades($unidad);
			}
		}
	}
	
	// Obtener y validar las observaciones del viático
	private function __ValidarObservaciones($form){
		if(isset($_POST['observaciones']) && trim($_POST['observaciones']) != ''){
			$form->SetObservaciones(trim($_POST['observaciones']));
		}
	}
	
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
	/*
	private function ValidarSelectDireccion($params)
	{
		$rutas = &$params['rutas'];
		$itemRequestName = $params['itemRequestName'];
		$itemArrayName =  $params['itemArrayName'];
		$countRutas = $params['countRutas'];
		$msjItemsVacios = $params['msjItemsVacios'];
		$msjItemsContadorDistinto = $params['msjItemsContadorDistinto'];
		$msjItemsNoNumericos = $params['msjItemsNoNumericos'];
		
		if(!isset($_POST[$itemRequestName]) || !is_array($_POST[$itemRequestName])){
			$GLOBALS['SafiErrors']['general'][] = $msjItemsVacios;
		} else {
			$countItems = count($_POST[$itemRequestName]);
			if($countRutas != $countItems) {
				$GLOBALS['SafiErrors']['general'][] = $msjItemsContadorDistinto;
			} else {
				$indexRutasLocal = 0;
				foreach($_POST[$itemRequestName] as $item){
					$rutas[$indexRutasLocal][$itemArrayName] = (int)0;
					if(!SafiIsInt($item)){
						$GLOBALS['SafiErrors']['general'][] = sprintf($msjItemsNoNumericos, ($indexRutasLocal+1));
					} else {
						$rutas[$indexRutasLocal][$itemArrayName] = (int)$item;
					}
					$indexRutasLocal++;
				}
			}
		}
	}
	*/
	private function ValidarSelectDireccion2($params)
	{
		$rutas = &$params['rutas'];
		$itemRequestName = $params['itemRequestName'];
		$itemObjectName =  $params['itemObjectName'];
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
							$rutas[$indexRutasLocal]->$itemSetter($item);
						}
					}
					
					$indexRutasLocal++;
				}
			}
		}
	}

}

new ViaticoNacionalAccion();
