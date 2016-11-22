<?php
require_once(dirname(__FILE__) . '/../../../init.php');

// Acciones
require_once(SAFI_ACCIONES_PATH. '/acciones.php');

// Modelo
require_once(SAFI_MODELO_PATH. '/cuentaBanco.php');
require_once(SAFI_MODELO_PATH. '/banco.php');
require_once(SAFI_MODELO_PATH. '/beneficiario.php');
require_once(SAFI_MODELO_PATH. '/estatus.php');
require_once(SAFI_MODELO_PATH. '/reporteTesoreria.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');


class reportesTesoreria extends Acciones
{
	public function Buscar() {
		$params = array();
		$GLOBALS['SafiRequestVars']['cuentasBancarias'] = SafiModeloCuentaBanco::GetAllCuentaBancosActivos();
		$GLOBALS['SafiRequestVars']['estatusCheques'] = SafiModeloEstatus::GetEstatusCheques();
		$GLOBALS['SafiRequestVars']['beneficiarios'] = SafiModeloBeneficiario::GetAllBeneficiarios();
		$GLOBALS['SafiRequestVars']['tipoBusqueda'] = 'c';
		$params['busquedaInicial'] = '1';
		$GLOBALS['SafiRequestVars']['listaCheques'] = SafiModeloReporteTesoreria::GetBusqueda($params);
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/buscar.php");
	}

	public function BuscarAccion() {
		$params = array();		
		$cuentaBancaria = new EntidadCuentaBanco();
		$estatusCheque = new EntidadEstatus();
		$beneficiarioViatico = new EntidadBeneficiarioViatico();
		$params['cuentaBancaria'] = '-1';	
		$form = FormManager::GetForm(FORM_BUSCAR_TESORERIA);
		$params['busquedaInicial'] = '0';

		if(isset($_POST["opcionCheque"]) && trim($_POST["opcionCheque"]) != ''){
			$form->SetTipoBusqueda($_POST["opcionCheque"]);
			$params['tipoBusqueda'] = $form->GetTipoBusqueda();
		}
	
		if(isset($_POST["cuentaBancaria"]) && trim($_POST["cuentaBancaria"]) != ''){
			$cuentaBancaria -> SetId($_POST["cuentaBancaria"]);
			$form->SetCuentaBancaria($cuentaBancaria);
			$params['cuentaBancaria'] = $form->GetCuentaBancaria()->GetId();
		}
		if(isset($_POST["nroReferencia"]) && trim($_POST["nroReferencia"]) != ''){
			$form->SetNumeroReferencia($_POST["nroReferencia"]);
			$params['nroReferencia'] = $form->GetNumeroReferencia();
		}
		if(isset($_POST["estatusCheque"]) && trim($_POST["estatusCheque"]) != '-1'){
			$estatusCheque -> SetId($_POST["estatusCheque"]);
			$form->SetEstatusCheque($estatusCheque);
			$params['estatusCheque'] = $form->GetEstatusCheque()->GetId();
		}
		if(isset($_POST["beneficiario"]) && trim($_POST["beneficiario"]) != ''){
			$beneficiario = explode (":",$_POST["beneficiario"]);
			$idBeneficiario = $beneficiario[0];
			$nombreBeneficiario = $beneficiario[1];
			$beneficiarioViatico -> SetId($idBeneficiario);
			$beneficiarioViatico -> SetNombres($nombreBeneficiario);			
			
			$form->SetBeneficiario($beneficiarioViatico);
			$params['beneficiario'] = $_POST["beneficiario"];
		}
		if(isset($_POST['fechaInicioEmisionAnulado']) && trim($_POST['fechaInicioEmisionAnulado']) != ''){
			$form->SetFechaInicioEmisionAnulado($_POST["fechaInicioEmisionAnulado"]);
			$params['fechaInicioEmisionAnulado'] = $form->GetfechaInicioEmisionAnulado();
		}
		if(isset($_POST['fechaFinEmisionAnulado']) && trim($_POST['fechaFinEmisionAnulado']) != ''){
			$form->SetFechaFinEmisionAnulado($_POST["fechaFinEmisionAnulado"]);
			$params['fechaFinEmisionAnulado'] = $form->GetfechaFinEmisionAnulado();
		}
		if(isset($_POST['fechaInicioAnulado']) && trim($_POST['fechaInicioAnulado']) != ''){
			$form->SetFechaInicioAnulacion($_POST["fechaInicioAnulado"]);
			$params['fechaInicioAnulacion'] = $form->GetfechaInicioAnulacion();
		}
		if(isset($_POST['fechaFinAnulado']) && trim($_POST['fechaFinAnulado']) != ''){
			$form->SetFechaFinAnulacion($_POST["fechaFinAnulado"]);
			$params['fechaFinAnulacion'] = $form->GetfechaFinAnulacion();
		}
		if(isset($_POST["anoInicio"]) && trim($_POST["anoInicio"]) != '' && isset($_POST["anoFin"]) && trim($_POST["anoFin"]) != ''){
			$form->SetAnoEmisionCheque($_POST["anoInicio"]);
			$form->SetAnoAnulacionCheque($_POST["anoFin"]);			
			$params['anoEmisionCheque'] = $form->GetAnoEmisionCheque();
			$params['anoFinCheque'] = $form->GetAnoAnulacionCheque();			
		}
		if(isset($_POST['fechaInicio']) && trim($_POST['fechaInicio']) != '' && isset($_POST['fechaFin'])  && trim($_POST['fechaFin']) != ''){
			$form->SetFechaInicioEmision($_POST["fechaInicio"]);
			$form->SetFechaFinEmision($_POST["fechaFin"]);
			$params['fechaInicio'] = $form->GetfechaInicioEmision();
			$params['fechaFin'] = $form->GetfechaFinEmision();			
		}
		
		$GLOBALS['SafiRequestVars']['tipoBusqueda'] = $_POST["opcionCheque"];
		$GLOBALS['SafiRequestVars']['listaCheques'] = SafiModeloReporteTesoreria::GetBusqueda($params);
		$GLOBALS['SafiRequestVars']['cuentasBancarias'] = SafiModeloCuentaBanco::GetAllCuentaBancosActivos();
		$GLOBALS['SafiRequestVars']['estatusCheques'] = SafiModeloEstatus::GetEstatusCheques();
		$GLOBALS['SafiRequestVars']['beneficiarios'] = SafiModeloBeneficiario::GetAllBeneficiarios();
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/buscar.php");
	}

	public function UbicarDocumento() {
		$form = FormManager::GetForm(FORM_UBICAR_DOCUMENTO_TESORERIA);
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/ubicarDocumento.php");
	}
	public function UbicarDocumentoAccion() {
		$form = FormManager::GetForm(FORM_UBICAR_DOCUMENTO_TESORERIA);
		$params = array();
		$form->SetTipoBusqueda($_POST["tipo"]);
		$params['tipo'] = $form->GetTipoBusqueda();
	
	
		if(isset($_POST["documento"]) && trim($_POST["documento"]) != ''){
			$form->SetDocumento($_POST["documento"]);
			$params['documento'] = $form->GetDocumento();
		}
	
		if(isset($_POST['fechaInicio']) && trim($_POST['fechaInicio']) != ''){
			$form->SetFechaInicio($_POST["fechaInicio"]);
			$params['fechaInicio'] = $form->GetfechaInicio();
		}
		if(isset($_POST['fechaFin']) && trim($_POST['fechaFin']) != ''){
			$form->SetFechaFin($_POST["fechaFin"]);
			$params['fechaFin'] = $form->GetfechaFin();
		}
	
		$GLOBALS['SafiRequestVars']['tipoBusqueda'] = $params['tipo'];
	
		$GLOBALS['SafiRequestVars']['listaDocumentos'] = SafiModeloReporteTesoreria::ubicarDocumento($params);
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/ubicarDocumento.php");
	}	


	public function OperacionesEmitidas() {
		$form = FormManager::GetForm(FORM_OPERACIONES_EMITIDAS_TESORERIA);		
		$GLOBALS['SafiRequestVars']['cuentasBancarias'] = SafiModeloCuentaBanco::GetAllCuentaBancosActivos();		
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/operacionesEmitidas.php");
	}
		
	public function OperacionesEmitidasAccion() {
		$params = array();		
		$cuentaBancaria = new EntidadCuentaBanco();
		$form = FormManager::GetForm(FORM_OPERACIONES_EMITIDAS_TESORERIA);
		
		if(isset($_POST['fechaInicio']) && trim($_POST['fechaInicio']) != ''){
			$form->SetFechaInicio($_POST["fechaInicio"]);
			$params['fechaInicio'] = $form->GetfechaInicio();
		}
		if(isset($_POST['fechaFin']) && trim($_POST['fechaFin']) != ''){
			$form->SetFechaFin($_POST["fechaFin"]);
			$params['fechaFin'] = $form->GetfechaFin();
		}

		if(isset($_POST["cuentaBancaria"]) && trim($_POST["cuentaBancaria"]) != ''){
			$cuentaBancaria -> SetId($_POST["cuentaBancaria"]);
			$form->SetCuentaBancaria($cuentaBancaria);
			$params['cuentaBancaria'] = $form->GetCuentaBancaria()->GetId();
		}

		if(isset($_POST['tipo']) && trim($_POST['tipo']) != ''){
			$form->SetTipoBusqueda($_POST["tipo"]);
			$params['tipoBusqueda'] = $form->GetTipoBusqueda();
		}

		$params['tipoPago'] = $_POST["tipopago"];

		$GLOBALS['SafiRequestVars']['cuentasBancarias'] = SafiModeloCuentaBanco::GetAllCuentaBancosActivos();		
		$GLOBALS['SafiRequestVars']['listaDocumentos'] = SafiModeloReporteTesoreria::OperacionesEmitidas($params);
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/operacionesEmitidas.php");
	}

	
		
	public function  operacionesEmitidasPDFAccion() {
		$params = array();		
		$cuentaBancaria = new EntidadCuentaBanco();
		$form = FormManager::GetForm(FORM_OPERACIONES_EMITIDAS_TESORERIA);
		
		if(isset($_POST['fechaInicio']) && trim($_POST['fechaInicio']) != ''){
			$form->SetFechaInicio($_POST["fechaInicio"]);
			$params['fechaInicio'] = $form->GetfechaInicio();
		}
		if(isset($_POST['fechaFin']) && trim($_POST['fechaFin']) != ''){
			$form->SetFechaFin($_POST["fechaFin"]);
			$params['fechaFin'] = $form->GetfechaFin();
		}

		if(isset($_POST["cuentaBancaria"]) && trim($_POST["cuentaBancaria"]) != ''){
			$cuentaBancaria -> SetId($_POST["cuentaBancaria"]);
			$form->SetCuentaBancaria($cuentaBancaria);
			$params['cuentaBancaria'] = $form->GetCuentaBancaria()->GetId();
		}

		if(isset($_POST['tipo']) && trim($_POST['tipo']) != ''){
			$form->SetTipoBusqueda($_POST["tipo"]);
			$params['tipoBusqueda'] = $form->GetTipoBusqueda();
		}

		$params['tipoPago'] = $_POST["tipopago"];

		$GLOBALS['SafiRequestVars']['cuentasBancarias'] = SafiModeloCuentaBanco::GetAllCuentaBancosActivos();		
		$GLOBALS['SafiRequestVars']['listaDocumentos'] = SafiModeloReporteTesoreria::OperacionesEmitidas($params);
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/operacionesEmitidasPDF.php");
	}
	
	
	public function detalleCheque() {
		$params['pgchId'] = $_REQUEST["pgchId"];
		$params['idCheque'] = $_REQUEST["idCheque"];
		$GLOBALS['SafiRequestVars']['listaCheques'] = SafiModeloReporteTesoreria::GetDetalleCheque($params);	
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/detalleCheque.php");
	}
		
	public function imprimirCheque() {
		$params['sopg'] = $_REQUEST["sopg"];
		$params['idCheque'] = $_REQUEST["idCheque"];		
		$GLOBALS['SafiRequestVars']['listaCheques'] = SafiModeloReporteTesoreria::ImprimirCheque($params);
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/impresionCheque.php");
	}

	public function reimprimirCheque() {
		$params['sopg'] = $_REQUEST["sopg"];
		$params['idCheque'] = $_REQUEST["idCheque"];
		$GLOBALS['SafiRequestVars']['listaCheques'] = SafiModeloReporteTesoreria::ReimprimirCheque($params);
		$GLOBALS['SafiRequestVars']['mensaje'] = "El cheque se encuentra nuevamente en estado preimpreso. Realice la b&uacute;squeda para su reimpresi&oacute;n";
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/buscar.php");
	}
		
	public function anularCheque() {
		$params['sopg'] = $_REQUEST["sopg"];
		$params['idCheque'] = $_REQUEST["idCheque"];
		$GLOBALS['SafiRequestVars']['listaCheques'] = SafiModeloReporteTesoreria::AnularCheque($params);
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/anularCheque.php");
	}
	public function anularChequeAccion() {
		$params['sopg'] = $_POST["sopg"];
		$params['idCheque'] = $_POST["idCheque"];		
		$params['motivo'] = $_REQUEST["motivo"];
		$params['otro'] = $_REQUEST["otro"];		
		$params['observacionesAnulacion'] = $_REQUEST["observacionesAnulacion"];		
		$params['mensaje'] = "Cheque anulado";
								
		$GLOBALS['SafiRequestVars']['listaCheques'] = SafiModeloReporteTesoreria::AnularChequeAccion($params);
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/anularCheque.php");
	}
	

	public function RelacionContabilidad() {
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/relacionContabilidad.php");
	}
		
	public function RelacionContabilidadAccion() {
		$form = FormManager::GetForm(FORM_RELACION_CONTABILIDAD_TESORERIA);
			
		$params['tipoBusqueda'] = $_POST['tipoBusqueda'];
		$params['opcion'] = $_POST['opcion'];
		$params['numeroActa'] = $_POST['numeroActa'];
		$params['referencia'] = $_POST['referencia'];
		$params['sopg'] = $_POST['sopg'];
		
		
		
		$form->SetNumeroActa($params['numeroActa']);
		
		
	if($params['referencia'] == ''  ){
		$form->SetNumeroReferencia($params['sopg']);
		
	}else{
		
		$form->SetNumeroReferencia($params['referencia']);
		
		}
		
		$form->SetOpcion($params['opcion']);
		$form->SetTipoBusqueda($params['tipoBusqueda']);
		
		if (strcmp($params['tipoBusqueda'],'imprimirActa') != 0) {
			
			
			$GLOBALS['SafiRequestVars']['listaDocumentos'] = SafiModeloReporteTesoreria::relacionContabilidad($params);
			
			require(SAFI_VISTA_PATH . "/reportes/tesoreria/relacionContabilidad.php");
		}
		
		else {
			$params['tipoBusqueda'] = "";
			$GLOBALS['SafiRequestVars']['listaDocumentos'] = SafiModeloReporteTesoreria::relacionContabilidadImprimir($params);
			require(SAFI_VISTA_PATH . "/reportes/tesoreria/relacionContabilidadPDF.php");
		}
	}	
	public function RelacionContabilidadSeleccionAccion() {
		$form = FormManager::GetForm(FORM_RELACION_CONTABILIDAD_TESORERIA);		
		
		$params['tipoBusqueda'] = $_POST['tipoBusqueda'];
		$GLOBALS['SafiRequestVars']['tipoBusqueda'] = $params['tipoBusqueda'];
		
		$params['opcion'] = $_POST['tipoOpcion'];
		$GLOBALS['SafiRequestVars']['opcion'] = (($_POST['tipoOpcion'] == "0")? "Por conciliar": (($_POST['tipoOpcion'] == "1")? "Anulados" : (($_POST['tipoOpcion'] == "2")? "Conciliados": "" )));

		$params['numeroActa'] = $_POST['numeroActa'];
		$params['referencia'] = $_POST['referencia'];
		$form->SetNumeroActa($params['numeroActa']);
		$form->SetNumeroReferencia($params['referencia']);
		$form->SetOpcion($params['opcion']);
		$codigo = "";
		$params['order'] = $_POST['orden'];
		if (isset($_POST['solicitud'])) {
			$cod = $_POST["solicitud"];
		}
		if (count($cod) > 0) {
			for ($x=0;$x<count($cod);$x++) {
				if ($x==0) {
					$params['codigo'] = "'".$cod[$x]."'";
				}
				else {
					$params['codigo'] .= ",'".$cod[$x]."'";
				}
			}
			$params['codigo'] = str_replace(",,", ",", $params['codigo']);
			$params['order'] = str_replace(",,", ",", $params['order']);	
		}
		$GLOBALS['SafiRequestVars']['listaDocumentos'] = SafiModeloReporteTesoreria::relacionContabilidadAccion($params);
		$params['tipoBusqueda'] = $params['tipoBusqueda']."S";
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/relacionContabilidadPDF.php");
		
	}	
	
	public function SaldosCorrectos() {
		$form = FormManager::GetForm(FORM_SALDOS_CORRECTOS_TESORERIA);
		$GLOBALS['SafiRequestVars']['cuentasBancarias'] = SafiModeloCuentaBanco::GetAllCuentaBancosActivos();		
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/saldosCorrectos.php");
	}

	public function SaldosCorrectosAccion() {
		$params = array();
		$cuentaBancaria = new EntidadCuentaBanco();
		$form = FormManager::GetForm(FORM_SALDOS_CORRECTOS_TESORERIA);
	
		if(isset($_POST['fecha']) && trim($_POST['fecha']) != ''){
			$form->SetFecha($_POST["fecha"]);
			$params['fecha'] = $form->Getfecha();
		}
		if(isset($_POST["cuentaBancaria"]) && trim($_POST["cuentaBancaria"]) != ''){
			$cuentaBancaria -> SetId($_POST["cuentaBancaria"]);
			$form->SetCuentaBancaria($cuentaBancaria);
			$params['cuentaBancaria'] = $form->GetCuentaBancaria()->GetId();
		}
	
		$GLOBALS['SafiRequestVars']['cuentasBancarias'] = SafiModeloCuentaBanco::GetAllCuentaBancosActivos();
		$GLOBALS['SafiRequestVars']['listaDocumentos'] = SafiModeloReporteTesoreria::SaldosCorrectos($params);
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/saldosCorrectos.php");
	}

	public function saldosCorrectosPDFAccion() {
		$params = array();
		$cuentaBancaria = new EntidadCuentaBanco();
		$form = FormManager::GetForm(FORM_SALDOS_CORRECTOS_TESORERIA);
	
		if(isset($_POST['fecha']) && trim($_POST['fecha']) != ''){
			$form->SetFecha($_POST["fecha"]);
			$params['fecha'] = $form->Getfecha();
		}
		if(isset($_POST["cuentaBancaria"]) && trim($_POST["cuentaBancaria"]) != ''){
			$cuentaBancaria -> SetId($_POST["cuentaBancaria"]);
			$form->SetCuentaBancaria($cuentaBancaria);
			$params['cuentaBancaria'] = $form->GetCuentaBancaria()->GetId();
		}
	
		$GLOBALS['SafiRequestVars']['cuentasBancarias'] = SafiModeloCuentaBanco::GetAllCuentaBancosActivos();
		$GLOBALS['SafiRequestVars']['listaDocumentos'] = SafiModeloReporteTesoreria::SaldosCorrectos($params);
		require(SAFI_VISTA_PATH . "/reportes/tesoreria/saldosCorrectosPDF.php");
	}	
	
	
}
new reportesTesoreria();