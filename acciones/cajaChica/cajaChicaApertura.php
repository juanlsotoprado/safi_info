<?php
require_once(dirname(__FILE__) . '/../../init.php');

// Acciones
require_once(SAFI_ACCIONES_PATH. '/acciones.php');

// Modelos
require_once(SAFI_MODELO_PATH. '/cajaChica.php');
require_once(SAFI_MODELO_PATH. '/empleado.php');

if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}

class CajaChicaAperturaAccion extends Acciones
{
	public function Insertar()
	{
		$form = FormManager::GetForm(FORM_CAJA_CHICA_NUEVA_APERTURA);
		$cajaChica = new EntidadCajaChica();
		$form->SetCajaChica($cajaChica);
		$fechaHoy = date('d/m/Y'); 
		
		$cajaChica->SetFechaApertura($fechaHoy);
		
		/***************************************************
		************* Comentar para producción ************* 
		****************************************************/
		
		$responsable = SafiModeloEmpleado::GetEmpleadoActivoByCedula2("15586921");
		$custodio = SafiModeloEmpleado::GetEmpleadoActivoByCedula2("14155345");
		
		$cajaChica->SetJustificacion(utf8_decode("Esta es la justificación por defecto."));		
		$cajaChica->SetResponsable($responsable);
		$cajaChica->SetCustodio($custodio);
		
		/***************************************************
		********* Fin de Comentar para producción ********** 
		****************************************************/
		
		$this->__DesplegarFormulario();
	}
	
	public function Guardar()
	{
		$idCajaChica = $this->__Guardar();
		
		if($idCajaChica !== false){
			$GLOBALS['SafiInfo']['general'][] = "Apertura de caja chica \"" . $idCajaChica . "\" registrada satisfactoriamente.";
			$this->__VerDetalles(array('idCajaChica' => $idCajaChica));
		} else {
			$this->__DesplegarFormulario();
		}
	}
	
	private function __Guardar()
	{
		$GLOBALS['SafiErrors']['general'] = array();
		$GLOBALS['SafiInfo']['general'] = array();
		
		$form = FormManager::GetForm(FORM_CAJA_CHICA_NUEVA_APERTURA);
		$cajaChica = new EntidadCajaChica();
		$form->SetCajaChica($cajaChica);
		
		// Obtener y validar la fecha de la apertura
		$this->__ValidarFechaApertura($form);
		// Obtener y validar la justifición
		$this->__ValidarJustificacion($form);
		// Obtener y validar el responsable
		$this->__ValidarResponsable($form);
		// Obtener y validar el custodio
		$this->__ValidarCustodio($form);
		
		return false;
	}
	
	private function __DesplegarFormulario()
	{
		require (SAFI_VISTA_PATH."/cajaChica/cajaChicaNuevaApertura.php");
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
	private function __ValidarFechaApertura(CajaChicaNuevaAperturaForm $form)
	{
		if(!isset($_POST['fechaApertura']) || trim($_POST['fechaApertura']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Debe indicar la fecha de apertura.";
		} else {
			if(($fecha = $this->__ValidarFecha($_POST['fechaApertura'])) !== false){
				$form->GetCajaChica()->SetFechaApertura($fecha);
			} else {
				$GLOBALS['SafiErrors']['general'][] = "Fecha de apertura inv&aacute;lida.";
			}
		}
	}
	
	// Obtener y validar la justifición
	private function __ValidarJustificacion(CajaChicaNuevaAperturaForm $form)
	{
		if(!isset($_POST['justificacion']) || trim($_POST['justificacion']) == ''){
			$GLOBALS['SafiErrors']['general'][] = "Debe indicar la justificaci&oacute;n.";
		} else {
			$form->GetCajaChica()->SetJustificacion($_POST['justificacion']);
		}
	}
	
	// Obtener y validar el responsable
	private function __ValidarResponsable(CajaChicaNuevaAperturaForm $form)
	{
		if(!isset($_POST['cedulaResponsable']) || ($cedulaResponsable=trim($_POST['cedulaResponsable'])) == '')
			$GLOBALS['SafiErrors']['general'][] = "Debe seleccionar un responsable.";
		else
		{
			$responsable = SafiModeloEmpleado::GetEmpleadoActivoByCedula2($cedulaResponsable);
			if(!is_object($responsable))
				$GLOBALS['SafiErrors']['general'][] = "Responsable no encontrado.";
			else
				$form->GetCajaChica()->SetResponsable($responsable);
		}
	}

	// Obtener y validar el custodio
	private function __ValidarCustodio(CajaChicaNuevaAperturaForm $form)
	{
		if(!isset($_POST['cedulaCustodio']) || ($cedulaCustodio=trim($_POST['cedulaCustodio'])) == '')
			$GLOBALS['SafiErrors']['general'][] = "Debe seleccionar un custodio.";
		else
		{
			$custodio = SafiModeloEmpleado::GetEmpleadoActivoByCedula2($cedulaCustodio);
			if(!is_object($custodio))
				$GLOBALS['SafiErrors']['general'][] = "Custodio no encontrado.";
			else
				$form->GetCajaChica()->SetCustodio($custodio);
		}
	}
	
	// Obtener y validar punto de cuenta
	private function __ValidarPuntoCuenta()
	{
		if(!isset($_POST['idPuntosCuenta']))
			$GLOBALS['SafiErrors']['general'][] = "Debe seleccionar un punto de cuenta.";
		else
		{
			$puntoCuenta = new EntidadPuntoCuenta();
			$puntoCuenta = SafiModeloPuntoCuenta::GetPuntoCuenta(array("IdPuntoCuenta" => $idPuntoCuenta));
		}
	}
}

new CajaChicaAperturaAccion();
?>