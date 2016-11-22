<?php

include(dirname(__FILE__).'/../../init.php');

// Acciones
require_once(SAFI_ACCIONES_PATH. '/acciones.php');

// Includes
require_once(SAFI_INCLUDE_PATH. '/perfiles/constantesPerfiles.php');

require_once(SAFI_INCLUDE_PATH. '/conexion.php');

//Modelos

require_once(SAFI_MODELO_PATH. '/pagoTransferencia.php');
require_once(SAFI_MODELO_PATH. '/cuentaBanco.php');
require_once(SAFI_MODELO_PATH. '/banco.php');
require_once(SAFI_MODELO_PATH. '/general.php');

  if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../index.php',false);
   ob_end_flush(); 
   exit;
  }
  
 class Tran extends Acciones{
	

     public function Modificar(){
    
		include(SAFI_VISTA_PATH ."/tran/modificar.php");

	}
	public function ModificarBuscar(){
		$idTransferencia = $_POST["numeroTransferencia"];
		$form = FormManager::GetForm(FORM_TRANSFERENCIA);
		
		if(isset($_POST['numeroTransferencia']) && trim($_POST['numeroTransferencia']) != ''){
			$form->SetId($_POST["numeroTransferencia"]);
		}
		$GLOBALS['SafiRequestVars']['cuentasBancarias'] = SafiModeloCuentaBanco::GetAllCuentaBancosActivos();		
		$GLOBALS['SafiRequestVars']['transferencia'] = SafiModeloPagoTransferencia::GetTransferencia($idTransferencia);
		include(SAFI_VISTA_PATH ."/tran/modificar.php");
	}

	public function ModificarAccion(){
		$params = array();
		$params['idTransferencia'] = $_POST['idTransferencia'];
		$params['nuevaFecha'] = $_POST['nuevaFecha'];
		$params['nuevaReferencia'] = $_POST['nuevaReferencia'];		
		$params['nuevaCuentaBancaria'] = $_POST['nuevaCuentaBancaria'];
		$resultado = SafiModeloPagoTransferencia::ModificarTransferencia($params);
		if ($resultado)
			$GLOBALS['SafiRequestVars']['mensaje'] = "Los datos de la transferencia fueron modificados";
		include(SAFI_VISTA_PATH ."/tran/modificar.php");
	}		
	
}

new Tran();