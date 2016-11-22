<?php
if (isset ( $_REQUEST ['PHPSESSID'] )) {
	
	$_COOKIE ['PHPSESSID'] = $_REQUEST ['PHPSESSID'];
}

include (dirname ( __FILE__ ) . '/../../init.php');

// Acciones
require_once (SAFI_ACCIONES_PATH . '/acciones.php');

// Includes
require_once (SAFI_INCLUDE_PATH . '/perfiles/constantesPerfiles.php');

require_once (SAFI_INCLUDE_PATH . '/conexion.php');

// Modelos
include (SAFI_MODELO_PATH . '/compromiso.php');
include (SAFI_MODELO_PATH . '/compromisoAsunto.php');
include (SAFI_MODELO_PATH . '/tipoActividadCompromiso.php');
include (SAFI_MODELO_PATH . '/estadosVenezuela.php');
include (SAFI_MODELO_PATH . '/infocentro.php');
include (SAFI_MODELO_PATH . '/estado.php');
include (SAFI_MODELO_PATH . '/tipoEvento.php');
include (SAFI_MODELO_PATH . '/centroGestorCosto.php');
require_once (SAFI_MODELO_PATH . '/puntoCuenta.php');
require_once (SAFI_MODELO_PATH . '/dependencia.php');
require_once (SAFI_MODELO_PATH . '/proyecto.php');
require_once (SAFI_MODELO_PATH . '/accioncentralizada.php');
require_once (SAFI_MODELO_PATH . '/proyectoespecifica.php');
require_once (SAFI_MODELO_PATH . '/accioncentralizadaespecifica.php');
require_once (SAFI_MODELO_PATH . '/disponibilidadPcuenta.php');
require_once (SAFI_MODELO_PATH . '/empleado.php');
require_once (SAFI_MODELO_PATH . '/general.php');
require_once (SAFI_MODELO_PATH . '/compromisoImputa.php');
require_once (SAFI_MODELO_PATH . '/docgenera.php');
require_once (SAFI_MODELO_PATH . '/estatus.php');
require_once (SAFI_MODELO_PATH . '/partida.php');
require_once (SAFI_MODELO_PATH . '/solicitudPago.php');
require_once (SAFI_MODELO_PATH . '/observacionesDoc.php');
require_once (SAFI_MODELO_PATH . '/controlInterno.php');
require_once (SAFI_MODELO_PATH . '/tipoSolicitudPago.php');
require_once (SAFI_MODELO_PATH . '/wfgrupo.php');
require_once (SAFI_MODELO_PATH . '/wfopcion.php');
require_once (SAFI_MODELO_PATH . '/wfcadena.php');
require_once (SAFI_MODELO_PATH . '/solicitudPago.php');

if (empty ( $_SESSION ['login'] ) || ($_SESSION ['registrado'] != "registrado")) {
	$_COOKIE ['PHPSESSID'] = $_REQUEST ['PHPSESSID'];
}

class Sopg extends Acciones{
	public function IniciarPago()
	{
		
		/* parche para iniciar pago funcione en el año actual, ya que la llamada require_once(SAFI_MODELO_PATH. '/puntoCuenta.php'); lo cambia */
		//$_SESSION['an_o_presupuesto'] = '2015';
		 
		$GLOBALS['SafiRequestVars']['sopg'] = SafiModeloSolicitudPago::GetSopgPorIniciarPago(39);
		//print_r($GLOBALS['SafiRequestVars']['sopg']);
		include(SAFI_VISTA_PATH ."/sopg/iniciarPago.php");
	}
	public function Ingresar() {
		$lugar = 'sopg';
		$id_cadena_inicial = SafiModeloWFCadena::GetWfca_id_inicial ( $lugar );
		$GLOBALS ['SafiRequestVars'] ['idCadenaActual'] = $id_cadena_inicial;
		
		$id_HijosCadena = SafiModeloWFCadena::GetId_cadena_hijo_id_cadena ( $id_cadena_inicial );
		$GLOBALS ['SafiRequestVars'] ['opciones'] = $id_HijosCadena;
		
		$DependenciaPcta = SafiModeloDependencia::GetDependenciasByNivel ( 4 );
		
		$GLOBALS ['SafiRequestVars'] ['DependenciaPcta'] = $DependenciaPcta;
		
		$estadosVenezuela = SafiModeloEstadosVenezuela::GetEstadosVenezuela ( array (
				1 
		) );
		
		if (is_array ( $estadosVenezuela )) {
			
			foreach ( $estadosVenezuela as $estadosVenezuela2 ) {
				
				if ($estadosVenezuela2 instanceof EntidadEstadosVenezuela) {
					$estadosVenezuela2->UTF8Encode ();
					$listaEstados [$estadosVenezuela2->GetId ()] = $estadosVenezuela2->ToArray ();
				}
			}
		}
		
		$getPocentajesIva = SafiModeloGeneral::GetPocentajesIva ();
		
		$GLOBALS ['SafiRequestVars'] ['getPocentajesIva'] = $getPocentajesIva;
		
		$GLOBALS ['SafiRequestVars'] ['estadosVenezuela'] = $listaEstados;
		
		include (SAFI_VISTA_PATH . "/sopg/sopg.php");
	}
	public function SearchTipoSoliciud() {
		$key = trim ( utf8_decode ( $_REQUEST ["key"] ) );
		
		$tipoSolpagos = SafiModeloTipoSolicitudPago::GetTipoSolpagos ( $key );
		
		if ($tipoSolpagos) {
			
			$GLOBALS ['SafiRequestVars'] ['tipoSolpagos'] = $tipoSolpagos;
		} else {
			
			$GLOBALS ['SafiRequestVars'] ['tipoSolpagos'] = false;
		}
		
		include (SAFI_VISTA_PATH . "/json/sopgTipoSolicitud.php");
	}
	public function SearchProveedorSugerido() {
		$key = trim ( $_REQUEST ["key"] );
		
		$ProveedorSugerido = SafiModeloEmpleado::GetProveedorSugerido ( utf8_decode ( $key ), $_REQUEST ["tipoGet"] );
		
		$GLOBALS ['SafiRequestVars'] ['ProveedorSugerido'] = $ProveedorSugerido;
		
		include (SAFI_VISTA_PATH . "/json/ProveedorSugerido.php");
	}
	public function SearchCompromiso() {
		if (is_array ( $_REQUEST ["compromisosActuales"] )) {
			
			$compArray = $_REQUEST ["compromisosActuales"];
		}
		$key = trim ( $_REQUEST ["key"] );
		
		if (strlen ( $_REQUEST ["tipoff"] ) != 0) {
			
			$ff = $_REQUEST ["tipoff"];
		}
		
		if (strlen ( $_REQUEST ["tipoimput"] ) != 0) {
			
			$ti = $_REQUEST ["tipoimput"];
		}
		
		//error_log(print_r($_REQUEST,true));
		
		//error_log(print_r($compArray,true));		
		// error_log($_REQUEST["tipoff"]);
		
	     //$anio = '2014';
	     $anio = $_SESSION['an_o_presupuesto'];
	     error_log(print_r($anio,true)); 
		// Esta función debe ser llamada con el año presupuestario en el cuarto parámetro
		$compromiso = SafiModeloCompromiso::GetCompromisosFiltro ( $key, $ff, $compArray,$anio, $ti );
		
		
		/*
		try {
			throw new Exception("Revisa la llamada este procedimiento: GetCompromisosFiltro()");
		}catch (Exception $e){
			echo "<pre>";
			echo $e;
			echo "</pre>";
			error_log($e);
		}
		*/
		
		
		
		$GLOBALS ['SafiRequestVars'] ['compFiltro'] = $compromiso;
		
		include (SAFI_VISTA_PATH . "/json/sopgComp.php");
	}
	
	public function GuardarImg() {
		if (! empty ( $_FILES )) {
			
			if (! isset ( $_SESSION ['SafiRequestVars'] ['nameFile'] )) {
				$_SESSION ['SafiRequestVars'] ['nameFile'] = array ();
			}
			
			$prefijo = substr ( md5 ( uniqid ( rand () ) ), 0, 6 );
			
			$name = $_FILES ['Filedata'] ['name'];
			
			$name2 = $prefijo . "_" . $name;
			
			$_SESSION ['SafiRequestVars'] ['nameFile'] [] = $name2;
			
			$targetFolder = SAFI_TMP_PATH . '/';
			
			$tempFile = $_FILES ['Filedata'] ['tmp_name'];
			$targetPath = $targetFolder;
			$targetFile = rtrim ( $targetPath, '/' ) . '/' . $name2;
			
			// Validate the file type
			$fileTypes = array (
					'jpg',
					'jpeg',
					'gif',
					'png',
					'pdf' 
			); // File extensions
			$fileParts = pathinfo ( $_FILES ['Filedata'] ['name'] );
			
			if (in_array ( $fileParts ['extension'], $fileTypes )) {
				copy ( $tempFile, $targetFile );
				echo "1";
			} else {
				
				echo "Error";
			}
		}
		
	}
	public function IngresarAccion() {
		$params ['tipocatpago'] = $_REQUEST ['tipocatpago'];
		
		$params ['codigoDocumentoSopg'] = $_REQUEST ['codigoDocumento'];
		
		$params ['DependenciaSoli'] = $_REQUEST ['DependenciaPcta'];
		
		$params ['observaciones'] = $_REQUEST ['observaciones'];
		
		$params ['motivo'] = $_REQUEST ['motivo'];
		
		$params ['poseefactura'] = $_REQUEST ['poseefactura'];
		
		if ($_REQUEST ['beneficiario']) {
			$params ['rifProveedorSopg'] = $_REQUEST ['beneficiario'];
		}
		
		if ($_REQUEST ['compromisoCod']) {
			$params ['compromisoCod'] = $_REQUEST ["compromisoCod"] ['compromiso'];
			;
		}
		
		if ($_REQUEST ['partidasCompromiso']) {
			$params ['partidasCompromiso'] = $_REQUEST ['partidasCompromiso'];
		}
		
		$params ['catPagoVal'] = $_POST ["catPagoVal"];
		
		$params ['fechaSopg'] = $_POST ["fecha"];
		
		$params ['PerfilSiguiente'] = $_SESSION ['user_perfil_id'];
		
		$params ['CadenaIdcadena'] = trim ( $_REQUEST ['idCadenaSigiente'] );
		
		$cadenaIdGrupo = SafiModeloWFCadena::GetCadenaIdGrupo ( $params ['CadenaIdcadena'] );
		
		$params ['CadenaGrupo'] = $cadenaIdGrupo;
		
		if ($_REQUEST ['factura']) {
			$params ['factura'] = $_REQUEST ['factura'];
		}
		
		// respaldos fisicos
		
		$params ['Fisico'] = $_REQUEST ['RegistroFisico'];
		
		// respaldos dig
		
		if (isset ( $_SESSION ['SafiRequestVars'] ['nameFile'] )) {
			
			$i = 0;
			foreach ( $_SESSION ['SafiRequestVars'] ['nameFile'] as $index => $valor ) {
				
				$targetFolder = SAFI_UPLOADS_PATH . '/sopg/' . $valor;
				$tempFile = SAFI_TMP_PATH . '/' . $valor;
				copy ( $tempFile, $targetFolder );
				
				$params ['Digital'] [] = $valor;
			}
		}
		
		unset ( $_SESSION ['SafiRequestVars'] ['nameFile'] );
		
		$val = SafiModeloSolicitudPago::Insertsolpago ( $params );
				
		$this->__VerDetalles($val,true);
		
		
	}
	
	public function __VerDetalles($params)
	{
		
		$val = SafiModeloSolicitudPago::Detallesopg($params);
	
		require(SAFI_VISTA_PATH ."/sopg/sopgDetalles.php");
	}
}

new Sopg ();