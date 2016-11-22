<?php
require_once(dirname(__FILE__) . '/../../../init.php');

// Acciones
require_once(SAFI_ACCIONES_PATH. '/acciones.php');

// Modelo
require_once(SAFI_MODELO_PATH. '/cuentaBanco.php');
require_once(SAFI_MODELO_PATH. '/tipoSolicitudPago.php');
require_once(SAFI_MODELO_PATH. '/tipoActividadCompromiso.php');
require_once(SAFI_MODELO_PATH. '/reporteContabilidad.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');

class reportesContabilidad extends Acciones
{
	public function cuentaBancariaPagado() {
		$params = array();
		$GLOBALS['SafiRequestVars']['cuentasBancarias'] = SafiModeloCuentaBanco::GetAllCuentaBancosActivos();
		require(SAFI_VISTA_PATH . "/reportes/contabilidad/cuentaBancariaPagado.php");
	}

	public function cuentaCausadoPagado() {
		$params = array();
		$GLOBALS['SafiRequestVars']['cuentasBancarias'] = SafiModeloCuentaBanco::GetAllCuentaBancosActivos();
		$GLOBALS['SafiRequestVars']['tipoSolicitudPago'] = SafiModeloTipoSolicitudPago::GetAllTipoSolicitudPago();
		$GLOBALS['SafiRequestVars']['tipoActividadCompromiso'] = SafiModeloTipoActividadCompromiso::GetAllTipoActividadCompromiso();				
		require(SAFI_VISTA_PATH . "/reportes/contabilidad/cuentasCausadoPagado.php");
	}

	public function cuentaBancariaPagadoAccion() {
		$params = array();		
		$cuentaBancaria = new EntidadCuentaBanco();
		$form = FormManager::GetForm(FORM_CUENTA_BANCARIA_PAGADO_CONTABILIDAD);
	
		if(isset($_POST["cuenta"]) && trim($_POST["cuenta"]) != '-1'){
			$cuentaBancaria -> SetId($_POST["cuenta"]);
			$form->SetCuentaBancaria($cuentaBancaria);
			$params['cuentaBancaria'] = $form->GetCuentaBancaria()->GetId();
		}
		if(isset($_POST['fecha_inicio']) && trim($_POST['fecha_inicio']) != '' && isset($_POST['fecha_fin'])  && trim($_POST['fecha_fin']) != ''){
			$form->SetFechaInicio($_POST["fecha_inicio"]);
			$form->SetFechaFin($_POST["fecha_fin"]);
			$params['fechaInicio'] = $form->GetfechaInicio();
			$params['fechaFin'] = $form->GetfechaFin();
		}		
		if(isset($_POST["reporte"]) && trim($_POST["reporte"]) != ''){
			$form->SetTipoReporte($_POST["reporte"]);
			$params['tipoReporte'] = $form->GetTipoReporte();
		}
		if(isset($_POST["estado"]) && trim($_POST["estado"]) != ''){
			$form->SetEstado($_POST["estado"]);
			$params['estado'] = $form->GetEstado();
		}
		
		$GLOBALS['SafiRequestVars']['listaMovimientos'] = SafiModeloReporteContabilidad::GetCuentaBancariaPagado($params);
		$GLOBALS['SafiRequestVars']['cuentasBancarias'] = SafiModeloCuentaBanco::GetAllCuentaBancosActivos();
		require(SAFI_VISTA_PATH . "/reportes/contabilidad/cuentaBancariaPagado.php");
	}

	public function cuentaCausadoPagadoAccion() {
		$params = array();
		$cuentaBancaria = new EntidadCuentaBanco();
		$tipoSolicitudPago = new EntidadTipoSolicitudPago();
		$tipoActividadCompromiso = new EntidadTipoActividadCompromiso();
		$form = FormManager::GetForm(FORM_CUENTA_BANCARIA_PAGADO_CONTABILIDAD);
	
		if(isset($_POST["cuenta"]) && trim($_POST["cuenta"]) != '-1'){
			$cuentaBancaria -> SetId($_POST["cuenta"]);
			$form->SetCuentaBancaria($cuentaBancaria);
			$params['cuentaBancaria'] = $form->GetCuentaBancaria()->GetId();
		}
		if(isset($_POST['fecha_inicio']) && trim($_POST['fecha_inicio']) != '' && isset($_POST['fecha_fin'])  && trim($_POST['fecha_fin']) != ''){
			$form->SetFechaInicio($_POST["fecha_inicio"]);
			$form->SetFechaFin($_POST["fecha_fin"]);
			$params['fechaInicio'] = $form->GetfechaInicio();
			$params['fechaFin'] = $form->GetfechaFin();
		}
		if(isset($_POST["tipoSolicitudPago"]) && trim($_POST["tipoSolicitudPago"]) != '-1'){
			$tipoSolicitudPago -> SetId($_POST["tipoSolicitudPago"]);
			$form->SetTipoSolicitudPago($tipoSolicitudPago);
			$params['tipoSolicitudPago'] = $form->GetTipoSolicitudPago()->GetId();
		}
		if(isset($_POST["tipoActividadCompromiso"]) && trim($_POST["tipoActividadCompromiso"]) != '-1'){
			$tipoActividadCompromiso -> SetId($_POST["tipoActividadCompromiso"]);
			$form->SetTipoActividadCompromiso($tipoActividadCompromiso);
			$params['tipoActividadCompromiso'] = $form->GetTipoActividadCompromiso()->GetId();
		}
		if(isset($_POST["detalleSolicitudPago"]) && trim($_POST["detalleSolicitudPago"]) != ''){
			$form->SetDetalleSolicitudPago($_POST["detalleSolicitudPago"]);
			$params['detalleSolicitudPago'] = $form->GetDetalleSolicitudPago();
		}
		
		$GLOBALS['SafiRequestVars']['listaMovimientos'] = SafiModeloReporteContabilidad::GetCuentaCausadoPagadoConsolidado($params);
		$GLOBALS['SafiRequestVars']['cuentasBancarias'] = SafiModeloCuentaBanco::GetAllCuentaBancosActivos();
		$GLOBALS['SafiRequestVars']['tipoSolicitudPago'] = SafiModeloTipoSolicitudPago::GetAllTipoSolicitudPago();
		$GLOBALS['SafiRequestVars']['tipoActividadCompromiso'] = SafiModeloTipoActividadCompromiso::GetAllTipoActividadCompromiso();
		
		require(SAFI_VISTA_PATH . "/reportes/contabilidad/cuentasCausadoPagado.php");
	}
	
	public function auxiliar() {
		require(SAFI_VISTA_PATH . "/reportes/contabilidad/auxiliar.php");
	}

	public function auxiliarAccion() {
		$params = array();
		$cuentaContable = new EntidadCuentaContable();
		$form = FormManager::GetForm(FORM_AUXILIAR);
	
		if(isset($_POST["cuenta"]) && trim($_POST["cuenta"]) != '-1'){
			$cuentaContable -> SetId($_POST["cuenta"]);
			$form->SetCuentaContable($cuentaContable);
			$params['idCuentaContable'] = $form->GetCuentaContable()->GetId();
		}
		if(isset($_POST['fechaInicio']) && trim($_POST['fechaInicio']) != '' && isset($_POST['fechaFin'])  && trim($_POST['fechaFin']) != ''){
			$form->SetFechaInicio($_POST["fechaInicio"]);
			$form->SetFechaFin($_POST["fechaFin"]);
			$params['fechaInicio'] = $form->GetfechaInicio();
			$params['fechaFin'] = $form->GetfechaFin();
		}

		$GLOBALS['SafiRequestVars']['listaAuxiliar'] = SafiModeloReporteContabilidad::GetAuxiliar($params);
		require(SAFI_VISTA_PATH . "/reportes/contabilidad/auxiliar.php");
	}
}
new reportesContabilidad();