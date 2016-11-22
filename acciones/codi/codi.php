<?php
include(dirname(__FILE__).'/../../init.php');

// Acciones
require_once(SAFI_ACCIONES_PATH. '/acciones.php');

//requires
require_once(SAFI_INCLUDE_PATH. '/perfiles/constantesPerfiles.php');


require_once(SAFI_INCLUDE_PATH. '/conexion.php');

//Modelos

require_once(SAFI_MODELO_PATH. '/codi.php');
require_once(SAFI_MODELO_PATH. '/empleado.php');
require_once(SAFI_MODELO_PATH. '/compromisoImputa.php');
require_once(SAFI_MODELO_PATH. '/cuentaContable.php');
require_once(SAFI_MODELO_PATH. '/cuentaBanco.php');
require_once(SAFI_MODELO_PATH. '/observacionesDoc.php');
require_once(SAFI_MODELO_PATH. '/general.php');
require_once(SAFI_MODELO_PATH. '/wfcadena.php');
require_once(SAFI_MODELO_PATH. '/proyecto.php');
require_once(SAFI_MODELO_PATH. '/proyectoespecifica.php');
require_once(SAFI_MODELO_PATH. '/accioncentralizada.php');
require_once(SAFI_MODELO_PATH. '/accioncentralizadaespecifica.php');
require_once(SAFI_MODELO_PATH. '/estatus.php');


if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../index.php',false);
	ob_end_flush(); 
	exit;
}
  
 class Codi extends Acciones
 {
 	
 	public $_dependencia = null;
 	public $_fechaActual = null;
 	public $_login = null;
 	public $_userPerfilId = null;
 	public $_yearPresupuestario = null;
 	public $_banderaCambioAnio = null;
 	public $_fechaCambioAnio = null;
 	
 	public function __construct()
 	{
 		// Parche pra que los codis funciones en el año actual, mientras punto de cuenta funciona en el año anterior
 		//$_SESSION['an_o_presupuesto'] = '2015';
 		
 		$this->_dependencia = $_SESSION['user_depe_id'];
 		$this->_fechaActual = date("d/m/Y");
 		$this->_userPerfilId = $_SESSION['user_perfil_id'];
 		$this->_login = $_SESSION['login'];
 		//$this->_yearPresupuestario = $_SESSION['an_o_presupuesto'];
 		$this->_yearPresupuestario =  SafiModeloGeneral::GetAnnoDocumento('codi');// se cambia compromiso por el documento
 		$this->_banderaCambioAnio = SafiModeloGeneral::GetBanderaCambioAnio('codi');
 		$this->_fechaCambioAnio = SafiModeloGeneral::GetFechaCambioAnio('codi');
 		
		
 		/* Parche para que los codis se resgistren con fecha y código del año anterior */
 		/*
		$this->_yearPresupuestario = '2014';
 		$this->_fechaActual = "30/12/2014"; 
 		*/
 		
 		
 		// Borrar, por ahora está para pruebas
 		/*
 		$this->_yearPresupuestario = '2017';
 		$this->_dependencia = '123456';
 		*/
 		
 		//error_log($_SESSION['an_o_presupuesto']);

 		parent::__construct();
 		
 	}

	public function Registrar() {
		/*Seteo de variables iniciales cadena*/
		$lugar = 'codi';
		$id_cadena_inicial = SafiModeloWFCadena::GetWfca_id_inicial($lugar);
		$GLOBALS['SafiRequestVars']['idCadenaActual'] = $id_cadena_inicial;
		$id_HijosCadena = SafiModeloWFCadena::GetId_cadena_hijo_id_cadena($id_cadena_inicial);
		$GLOBALS['SafiRequestVars']['idCadenaPadre'] = $id_cadena;
		$GLOBALS['SafiRequestVars']['opciones'] = $id_HijosCadena;
		$GLOBALS['SafiRequestVars']['proyectos'] = SafiModeloProyecto::GetAllProyectosAprobados($this->_yearPresupuestario, $this->_dependencia);
		$GLOBALS['SafiRequestVars']['acc'] = SafiModeloAccionCentralizada::GetAllAccionesCentralizadasAprobadas($this->_yearPresupuestario, $this->_dependencia);
		$GLOBALS['SafiRequestVars']['yearPresupuestario'] = $this->_yearPresupuestario;
		
		require(SAFI_VISTA_PATH . "/codi/codi.php");
	}
		
	public function IngresarAccion()
	{
		$params = array();
		
		/*Generacion del id*/
		$param['lugar'] = "codi";
		$param['ano'] = substr ($this->_yearPresupuestario, 2);
		$param['Dependencia'] = $this->_dependencia;
		$params['docu_id'] = 'codi';
		$params['codi_id'] = SafiModeloGeneral::GetNexId($param);
		/*Fin de generación del id*/

		$params['dependencia'] = $this->_dependencia;
		$params['login'] = $this->_login;
		$params['userPerfilId'] = $this->_userPerfilId;
		$params['yearPresupuestario'] = $this->_yearPresupuestario;
		$params['fechaEfectiva'] = trim($_POST['fecha']);
		$params['fechaEmision'] = $this->_fechaActual . " " . date("H:i:s");
		$params['elaboradoPor'] = $this->_login;
		$params['compromisoId'] = trim($_POST['comp_id']);
		$params['documentoAsociado'] = trim($_POST['docAsociado']);
		$params['referenciaBancaria'] = trim($_POST['refBancaria']);
		$params['justificacion'] = trim($_POST['justificacion']);
		$params['cuentaContable'] = $_POST['cuentas_'];
		$params['cuentaContableBanco'] = SafiModeloCuentaBanco::GetCuentaBancoByCuentaContable($params['cuentaContable']);
		$params['partidaPresupuestaria'] = $_POST['partidas_'];
		$params['montoDebe'] = str_replace(".", "", $_POST['debe_']);
		$params['montoDebe'] = str_replace(",",".",$params['montoDebe']);
		$params['montoHaber'] = str_replace(".", "", $_POST['haber_']);
		$params['montoHaber'] = str_replace(",",".",$params['montoHaber']);		
		$params['proyAcc'] = $_POST['proy_acc'];
		$params['AccEsp'] = $_POST['proy_aesp'];
		$params['proyAccTipo'] = $_POST['proy_tipo'];	
		$lugar = 'codi';
		$id_cadena_inicial = SafiModeloWFCadena::GetWfca_id_inicial($lugar);
		$GLOBALS['SafiRequestVars']['idCadenaActual'] = $id_cadena_inicial;
		$id_HijosCadena = SafiModeloWFCadena::GetId_cadena_hijo_id_cadena($id_cadena_inicial);
		$GLOBALS['SafiRequestVars']['idCadenaPadre'] = $id_cadena;
		$GLOBALS['SafiRequestVars']['opciones'] = $id_HijosCadena;	
		$params['PerfilSiguiente'] = $this->_userPerfilId;
		$params['CadenaIdcadena'] = trim($_REQUEST['idCadenaSiguiente']);
		$cadenaIdGrupo =  SafiModeloWFCadena::GetCadenaIdGrupo($params['CadenaIdcadena']);
		$params['CadenaGrupo'] = $cadenaIdGrupo;
		$GLOBALS['SafiRequestVars']['id_codi'] = $params['codi_id'];
		
		
		//echo "  fecha de emisionnnnnnnnnnnnnnnnnnn  ".$params['fechaEmision'];
		SafiModeloCodi::InsertCodi($params);
		
		$this->GenerarPDF();
		$GLOBALS['SafiRequestVars']['listaCodi'] = null;
		$params = null;
		$this->Registrar();
	}	

	public function Buscar() {
		$GLOBALS['SafiRequestVars']['empleado'] = SafiModeloEmpleado::GetEmpleadoByCargo(PERFIL_ANALISTA_CONTABLE);
		$GLOBALS['SafiRequestVars']['EstadoCodi'] = SafiModeloEstatus::GetEstadoPctaIdPcuenta(array(11,15));
		require(SAFI_VISTA_PATH . "/codi/buscar.php");
	}

	public function BuscarAccion() {
		require_once(SAFI_INCLUDE_PATH. '/paginacion.php');
		$form = FormManager::GetForm(FORM_BUSCAR_CODI);
		$params = array();
		
		$params['cuentaBancaria'] = '-1';
		$params['tamanoPagina'] = $tamanoPagina;
		$params['tamanoVentana'] = $tamanoVentana;
	
		if ($_POST['pagina'] == "") {
			$params['pagina'] = $pagina;
			$params['desplazamiento'] = $desplazamiento;
		}
		else {
			$params['pagina'] = $_POST['pagina'];
			$params['desplazamiento'] = ($params['pagina']-1)*$params['tamanoPagina'];
		}
	
		if(isset($_POST['fecha_inicio']) && trim($_POST['fecha_inicio']) != ''){
			$form->SetFechaEmisionInicio(trim($_POST["fecha_inicio"]));
			$params['fecha_inicio'] = $form->GetFechaEmisionInicio();
		}
		if(isset($_POST['fecha_fin']) && trim($_POST['fecha_fin']) != ''){
			$form->SetFechaEmisionFin(trim($_POST["fecha_fin"]));
			$params['fecha_fin'] = $form->GetFechaEmisionFin();
		}
		if(isset($_REQUEST["idCodi"]) && trim($_REQUEST["idCodi"]) != ''){
			$form->SetIdCodi(trim($_REQUEST["idCodi"]));
			$params['idCodi'] = $form->GetIdCodi();
		}
		if(isset($_POST["referenciaBancaria"]) && trim($_POST["referenciaBancaria"]) != ''){
			$form->SetNumeroReferencia(trim($_POST["referenciaBancaria"]));
			$params['referenciaBancaria'] = $form->GetNumeroReferencia();
		}
		if(isset($_POST["documentoAsociado"]) && trim($_POST["documentoAsociado"]) != ''){
			$form->SetDocumentoAsociado(trim($_POST["documentoAsociado"]));
			$params['documentoAsociado'] = $form->GetDocumentoAsociado();
		}
		if(isset($_POST["justificacion"]) && trim($_POST["justificacion"]) != ''){
			$form->SetJustificacion(trim($_POST["justificacion"]));
			$params['justificacion'] = $form->GetJustificacion();
		}
		if(isset($_POST['fechae_inicio']) && trim($_POST['fechae_inicio']) != ''){
			$form->SetFechaElaboracionInicio(trim($_POST["fechae_inicio"]));
			$params['fechae_inicio'] = $form->GetFechaElaboracionInicio();
		}
		if(isset($_POST['fechae_fin']) && trim($_POST['fechae_fin']) != ''){
			$form->SetFechaElaboracionFin(trim($_POST["fechae_fin"]));
			$params['fechae_fin'] = $form->GetFechaElaboracionFin();
		}
		if(isset($_POST['empleado']) && trim($_POST['empleado']) != ''){
			$empleado = new EntidadEmpleado();
			$empleado->SetId(trim($_POST["empleado"]));
			$form->SetUsuario($empleado);
			$empleado = $form->GetUsuario();
			$params['empleado'] = $form->GetUsuario();
		}
		if(isset($_POST['cuentaContable']) && trim($_POST['cuentaContable']) != ''){
			$params['cuentaContable'] = substr($_POST['cuentaContable'], 0,strrpos($_POST['cuentaContable'], ' : '));
		}
		if(isset($_POST['estatusCodi']) && trim($_POST['estatusCodi']) != ''){
			$form->SetIdEstado($_POST["estatusCodi"]);
			$params['estatusCodi'] = $form->GetIdEstado();
		}
		if(isset($_POST['nro_compromiso']) && trim($_POST['nro_compromiso']) != ''){
			$form->SetNroCompromiso($_POST["nro_compromiso"]);
			$params['compromiso'] = $form->GetNroCompromiso();
		}
		$GLOBALS['SafiRequestVars']['listaCodi'] = SafiModeloCodi::GetBusqueda($params);
		$GLOBALS['SafiRequestVars']['listaCodiContador'] = SafiModeloCodi::GetBusquedaContador($params);
		$GLOBALS['SafiRequestVars']['empleado'] = SafiModeloEmpleado::GetEmpleadoByCargo(PERFIL_ANALISTA_CONTABLE);
		$GLOBALS['SafiRequestVars']['EstadoCodi'] = SafiModeloEstatus::GetEstadoPctaIdPcuenta(array(11,15));
		require(SAFI_VISTA_PATH . "/codi/buscar.php");
	}
	
	
	public function Modificar() {
		$params = array();
		$params['tamanoPagina'] = 1;
		$params['tamanoVentana'] = 1;
		$params['pagina'] = 1;
		$params['desplazamiento'] = 0;
	
		if(isset($_GET["idCodi"]) && trim($_GET["idCodi"]) != ''){
			$params['idCodi'] = $_GET["idCodi"];
			$params['codis'] = "'".$_GET["idCodi"]."'";
		}
		
		$GLOBALS['SafiRequestVars']['proyectos'] = SafiModeloProyecto::GetAllProyectosAprobados($this->_yearPresupuestario, $this->_dependencia);
		$GLOBALS['SafiRequestVars']['acc'] = SafiModeloAccionCentralizada::GetAllAccionesCentralizadasAprobadas($this->_yearPresupuestario, $this->_dependencia);
		$GLOBALS['SafiRequestVars']['listaCodi'] = SafiModeloCodi::GetBusqueda($params);
		$GLOBALS['SafiRequestVars']['listaCodiDetalle'] = SafiModeloCodi::GetPDFDetalleCodi($params,1);
		$GLOBALS['SafiRequestVars']['yearPresupuestario'] = $this->_yearPresupuestario;
		/*$GLOBALS['SafiRequestVars']['listaCodiPresupuesto'] = SafiModeloCodi::GetPDFDetallePresupuestoCodi($params);
		foreach ($GLOBALS['SafiRequestVars']['listaCodiPresupuesto'] as $presupuesto) {
			foreach ($presupuesto as $presupuesto2) {
				$GLOBALS['SafiRequestVars']['centro_costo']= $presupuesto2['centro_costo'];
			}
		}*/
		require(SAFI_VISTA_PATH . "/codi/codi.php");
	}
	
	public function VerDetalles()
	{
		if(isset($_REQUEST['idCodi']) && trim($_REQUEST['idCodi']) != '')
		{
			$this->__VerDetalles(array('idCodi' => $_REQUEST['idCodi']));
		}
	}
	
	private function __VerDetalles($params)
	{
		$idCodi = $params['idCodi'];
		
		if($idCodi != null && trim($idCodi) != '')
		{
			$params['codis'] = $idCodi;
			
			$GLOBALS['SafiRequestVars']['listaCodi'] = SafiModeloCodi::GetPDF($params);
			$GLOBALS['SafiRequestVars']['listaCodiDetalle'] = SafiModeloCodi::GetPDFDetalleCodi($params, 1);
		}
		
		require(SAFI_VISTA_PATH ."/codi/verDetalles.php");
		
	}
		
	public function ModificarAccion()
	{
		$params = array();
		$params['login'] = $this->_login;
		$params['userPerfilId'] = $this->_userPerfilId;
		$params['yearPresupuestario'] = $this->_yearPresupuestario;
		$params['idCodi'] = trim($_POST['idCodi']);
		$params['fechaEfectiva'] = trim($_POST['fecha']);
		$params['fechaEmision'] = $this->_fechaActual . " " . date("H:i:s");
		$params['elaboradoPor'] = $this->_login;
		$params['compromisoId'] = trim($_POST['comp_id']);
		$params['documentoAsociado'] = trim($_POST['docAsociado']);
		$params['referenciaBancaria'] = trim($_POST['refBancaria']);
		$params['justificacion'] = trim($_POST['justificacion']);
		$params['cuentaContable'] = $_POST['cuentas_'];
		$params['cuentaContableBanco'] = SafiModeloCuentaBanco::GetCuentaBancoByCuentaContable($params['cuentaContable']);
		$params['partidaPresupuestaria'] = $_POST['partidas_'];
		$params['montoDebe'] = str_replace(".", "", $_POST['debe_']);
		$params['montoDebe'] = str_replace(",",".",$params['montoDebe']);
		$params['montoHaber'] = str_replace(".", "", $_POST['haber_']);
		$params['montoHaber'] = str_replace(",",".",$params['montoHaber']);
		$params['proyAcc'] = $_POST['proy_acc'];
		$params['AccEsp'] = $_POST['proy_aesp'];
		$params['proyAccTipo'] = $_POST['proy_tipo'];
		$lugar = 'codi';
		$id_cadena_inicial = SafiModeloWFCadena::GetWfca_id_inicial($lugar);
		$GLOBALS['SafiRequestVars']['idCadenaActual'] = $id_cadena_inicial;
		$id_HijosCadena = SafiModeloWFCadena::GetId_cadena_hijo_id_cadena($id_cadena_inicial);
		$GLOBALS['SafiRequestVars']['idCadenaPadre'] = $id_cadena;
		$GLOBALS['SafiRequestVars']['opciones'] = $id_HijosCadena;
		$params['PerfilSiguiente'] = $this->_userPerfilId;
		$params['CadenaIdcadena'] = trim($_REQUEST['idCadenaSiguiente']);
		$cadenaIdGrupo =  SafiModeloWFCadena::GetCadenaIdGrupo($params['CadenaIdcadena']);
		$params['CadenaGrupo'] = $cadenaIdGrupo;
		
		$GLOBALS['SafiRequestVars']['id_codi'] = SafiModeloCodi::UpdateCodi($params);
		
		$this->GenerarPDF();
		$GLOBALS['SafiRequestVars']['listaCodi'] = null;
		$params = null;
		$this->Buscar();
	}	
		
	public function GenerarPDF() {
		
		$salidaEstandar = false; 
		
		if(isset($_REQUEST['salidaEstandar']) &&  $_REQUEST['salidaEstandar'] == 'true')
		{
			$salidaEstandar = true;
		}
		
		if (isset($_REQUEST['codis'])) {
			if (substr($_REQUEST['codis'],0,1)=="'") 
				$codis = $_REQUEST['codis'];
			else 
				$codis = "'".$_REQUEST['codis']."'";
		}
		else {
			$codis = "'".$GLOBALS['SafiRequestVars']['id_codi']."'";
		}
		
		$params['codis'] = $codis;
		
		if (isset($_REQUEST['caso']) && strlen($_REQUEST['caso'])>5) {		
			$GLOBALS['SafiRequestVars']['listaCodi'] = SafiModeloCodi::GetPDF($params);
			$GLOBALS['SafiRequestVars']['listaCodiDetalle'] = SafiModeloCodi::GetPDFDetalleCodi($params,2);
			//$GLOBALS['SafiRequestVars']['listaCodiPresupuesto'] = SafiModeloCodi::GetPDF2DetallePresupuestoCodi($params);
		}
		else {
			$GLOBALS['SafiRequestVars']['listaCodi'] = SafiModeloCodi::GetPDF($params);
			$GLOBALS['SafiRequestVars']['listaCodiDetalle'] = SafiModeloCodi::GetPDFDetalleCodi($params,1);
			//$GLOBALS['SafiRequestVars']['listaCodiPresupuesto'] = SafiModeloCodi::GetPDFDetallePresupuestoCodi($params);			
		}
		
		$GLOBALS['SafiRequestVars']['salidaEstandar'] = $salidaEstandar;
		
		require(SAFI_VISTA_PATH . "/codi/codiPDF.php");
	}

	public function Anular() {
		if (isset($_REQUEST['key']) && strlen($_REQUEST['key'])>5) {
			$codis = "'".$_REQUEST['key']."'";
		}
		else {
			$codis = "'".$GLOBALS['SafiRequestVars']['id']."'";
		}
		$params = array();
		$params['codis'] = $codis;
		
		$GLOBALS['SafiRequestVars']['listaCodi'] = SafiModeloCodi::GetPDF($params);
		$GLOBALS['SafiRequestVars']['listaCodiDetalle'] = SafiModeloCodi::GetPDFDetalleCodi($params,1);
		//$GLOBALS['SafiRequestVars']['listaCodiPresupuesto'] = SafiModeloCodi::GetPDFDetallePresupuestoCodi($params);
		include(SAFI_VISTA_PATH ."/codi/json/anular.php");		
	}
	
	public function AnularAccion() {
		$params = array();
		$params['id'] = $_GET['idCodi'];
		$params['observacion'] = $_GET['justificacion'];
		$params['estaid'] = 15;
		$params['perfil'] = $this->_userPerfilId;
		$params['opcion'] = 0;
		$params['fechaEmision'] = $this->_fechaActual . " " . date("H:i:s");
		$params['login'] = $this->_login;
		
		/* Parche para que las observaciones de la anulación queden en el año anterior */
		/*
		$siguinteHora = SafiModeloObservacionesDoc::GetObservacionesDocSiguienteHoraCodi($params['id']);
		if($siguinteHora) $params['fechaEmision'] = $this->_fechaActual . " " . $siguinteHora;
		*/
		
		SafiModeloObservacionesDoc::InsertarObservacionesDoc($params);
		
		if (SafiModeloCodi::Anular($params)) {
			$GLOBALS['SafiRequestVars']['id_codi'] = $params['id'];
			$this->GenerarPDF();
		}
		else
			echo "Error al anular el documento: ".$params['id'];
		$GLOBALS['SafiRequestVars']['listaCodi'] = null;
		require(SAFI_VISTA_PATH . "/codi/buscar.php");
	}	
	
	public function  SearchImputasComp(){
		$datos = array();
		$key = trim(utf8_decode($_REQUEST["key"]));
		$accEsp = trim(utf8_decode($_REQUEST["accEsp"]));
	    $imputas =   SafiModeloCompromisoImputa::GetCompImputasInfo($key);
		$GLOBALS['SafiRequestVars']['compImputa'] = $imputas;
		include(SAFI_VISTA_PATH ."/json/compImputa.php");
	}	
}
new Codi();