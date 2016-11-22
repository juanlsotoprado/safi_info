<?php

include(dirname(__FILE__).'/../../init.php');

// Acciones
require_once(SAFI_ACCIONES_PATH. '/acciones.php');

// Includes
require_once(SAFI_INCLUDE_PATH. '/perfiles/constantesPerfiles.php');

require_once(SAFI_INCLUDE_PATH. '/conexion.php');

//Modelos

require_once(SAFI_MODELO_PATH. '/pgch.php');
require_once(SAFI_MODELO_PATH. '/cheque.php');
require_once(SAFI_MODELO_PATH. '/cuentaBanco.php');
require_once(SAFI_MODELO_PATH. '/banco.php');
require_once(SAFI_MODELO_PATH. '/general.php');
require_once(SAFI_MODELO_PATH. '/beneficiario.php');

  if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../index.php',false);
   ob_end_flush(); 
   exit;
  }
  
 class Pgch extends Acciones{
	

    public function Iniciar(){
		include(SAFI_VISTA_PATH ."/pgch/reiniciarPago.php");
	}
	
	public function BuscarReiniciar(){
		$sopg_id = $_POST["sopgId"];
		$GLOBALS['SafiRequestVars']['pgch'] = SafiModeloPgch::GetPgchSopg($sopg_id);
		include(SAFI_VISTA_PATH ."/pgch/reiniciarPago.php");
	}	
	
	public function ReiniciarPago(){
		
		$params = array();
		$params['id_sopg'] = $_POST["idSopg"];
		$params['id_pgch'] = $_POST["idPgch"];		
		$GLOBALS['SafiRequestVars']['pgch'] = SafiModeloPgch::ReiniciarPago($params);
		$GLOBALS['SafiRequestVars']['mensaje'] = "La solicitud de pago: ".$params['id_sopg']." ya se encuentra nuevamente en la bandeja para iniciar pago";
		include(SAFI_VISTA_PATH ."/pgch/reiniciarPago.php");
	}
		
	public function ActualizarBeneficiarioCheque(){
		$GLOBALS['SafiRequestVars']['beneficiarios'] = SafiModeloBeneficiario::GetAllBeneficiarios();		
		include(SAFI_VISTA_PATH ."/pgch/actualizarCheque.php");
	}
			
	public function BuscarBeneficiario(){
		$ciRif=explode(":",$_POST["beneficiario"]);
		$GLOBALS['SafiRequestVars']['beneficiarios'] = SafiModeloBeneficiario::GetAllBeneficiarios();		
		$GLOBALS['SafiRequestVars']['nombreBeneficiario'] = $_POST["beneficiario"];		
		$GLOBALS['SafiRequestVars']['cheques'] = SafiModeloCheque::GetChequesPorBeneficiario(trim($ciRif[0]));
		include(SAFI_VISTA_PATH ."/pgch/actualizarCheque.php");
	}
		
	public function ActualizarBeneficiario(){
		$ciRif=explode(":",$_POST["beneficiarioBusqueda"]);
		$GLOBALS['SafiRequestVars']['beneficiarios'] = SafiModeloBeneficiario::GetAllBeneficiarios();
		$GLOBALS['SafiRequestVars']['cheques'] = SafiModeloCheque::ActualizarBeneficiario($_POST["idCheque"],trim($ciRif[1]));
		include(SAFI_VISTA_PATH ."/pgch/actualizarCheque.php");
		//echo $ciRif[1];
	}
	
}

new Pgch();