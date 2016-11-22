<?php

	if (isset($_REQUEST['PHPSESSID'])) { 

               $_COOKIE['PHPSESSID'] = $_REQUEST['PHPSESSID'];
         }
                   
include(dirname(__FILE__).'/../../init.php');
// Acciones
require_once(SAFI_ACCIONES_PATH. '/acciones.php');
// Includes
require_once(SAFI_INCLUDE_PATH. '/perfiles/constantesPerfiles.php');
require_once(SAFI_INCLUDE_PATH. '/conexion.php');

//Modelos
include(SAFI_MODELO_PATH. '/compromiso.php');
include(SAFI_MODELO_PATH. '/compromisoAsunto.php');
include(SAFI_MODELO_PATH. '/tipoActividadCompromiso.php');
include(SAFI_MODELO_PATH. '/estadosVenezuela.php');
include(SAFI_MODELO_PATH. '/infocentro.php');
include(SAFI_MODELO_PATH. '/estado.php');
include(SAFI_MODELO_PATH. '/tipoEvento.php');
include(SAFI_MODELO_PATH. '/centroGestorCosto.php');
require_once(SAFI_MODELO_PATH. '/puntoCuenta.php');
require_once(SAFI_MODELO_PATH. '/dependencia.php');
require_once(SAFI_MODELO_PATH. '/proyecto.php');
require_once(SAFI_MODELO_PATH. '/accioncentralizada.php');
require_once(SAFI_MODELO_PATH. '/proyectoespecifica.php');
require_once(SAFI_MODELO_PATH. '/accioncentralizadaespecifica.php');
require_once(SAFI_MODELO_PATH. '/disponibilidadPcuenta.php');
require_once(SAFI_MODELO_PATH. '/empleado.php');
require_once(SAFI_MODELO_PATH. '/general.php');
require_once(SAFI_MODELO_PATH. '/compromisoImputa.php');
require_once(SAFI_MODELO_PATH. '/docgenera.php');
require_once(SAFI_MODELO_PATH. '/estatus.php');
require_once(SAFI_MODELO_PATH. '/partida.php');
require_once(SAFI_MODELO_PATH. '/solicitudPago.php');
require_once(SAFI_MODELO_PATH. '/observacionesDoc.php');
require_once(SAFI_MODELO_PATH. '/controlInterno.php');
require_once(SAFI_MODELO_PATH. '/wfgrupo.php');
require_once(SAFI_MODELO_PATH. '/wfopcion.php');
require_once(SAFI_MODELO_PATH. '/wfcadena.php');
require_once(SAFI_MODELO_PATH. '/modificacionespresupuestarias.php');
require_once(SAFI_MODELO_PATH. '/mpresupuestarioImputa.php');
require_once(SAFI_MODELO_PATH. '/puntoCuentaRespaldo.php');
require_once(SAFI_MODELO_PATH. '/revisionesDoc.php');
require_once(SAFI_MODELO_PATH. '/observacionesDoc.php');
require_once(SAFI_MODELO_PATH. '/cargo.php');


if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../index.php',false);
	ob_end_flush();
	exit;
}

class Mpresupuestarias extends Acciones{

public function Ingresar($pmod = null){


	    $lugar = 'pmod';
		$id_cadena_inicial = SafiModeloWFCadena::GetWfca_id_inicial($lugar);
		$GLOBALS['SafiRequestVars']['idCadenaActual'] = $id_cadena_inicial;
		
		$id_HijosCadena = SafiModeloWFCadena::GetId_cadena_hijo_id_cadena($id_cadena_inicial);
		$GLOBALS['SafiRequestVars']['opciones'] = $id_HijosCadena;

		$proyectos = SafiModeloProyecto::GetAllProyectosAprobados();
		$GLOBALS['SafiRequestVars']['proyectos'] = $proyectos;

		$acc =SafiModeloAccionCentralizada::GetAllAccionesCentralizadasAprobadas();
		$GLOBALS['SafiRequestVars']['acc'] = $acc;
	
	   if($pmod){ $GLOBALS['SafiRequestVars']['pmod'] = $pmod; }

		include(SAFI_VISTA_PATH ."/modificacionesPresupuestarias/mpresupuestaria.php");
		

	}
	
	
public function Registrar(){
	
		$params['fecha'] = $_POST['fecha'];
		$params['accionMP'] = $_POST['accionMP'];
		$params['observaciones'] = $_POST['observaciones'];
		$params['mpresupuestariadisp'] = $_POST['mpresupuestariadisp'];
		$params['mpresupuestaria'] = $_POST['mpresupuestaria'];
		$params['montoTotal'] = $_POST['montoTotalHidden'];
        $params['PerfilSiguiente'] = $_SESSION['user_perfil_id'];

		// respaldos fisicos

		$params['Fisico'] = $_REQUEST['RegistroFisico'];


		// respaldos dig
		 
		if(isset($_SESSION['SafiRequestVars']['nameFile'])){

			 
			 
		$i = 0;
		

	 	foreach ($_SESSION['SafiRequestVars']['nameFile'] as $index => $valor){
	 		
	 
	 		$targetFolder = SAFI_UPLOADS_PATH.'/mpresupuestarias/'.$valor;
	 		$tempFile =  SAFI_TMP_PATH.'/'. $valor;
	 		copy($tempFile,$targetFolder);
	    
	 		$params['Digital'][] = $valor;

			 
	 	}

    }
    
    
     $param['lugar'] = "pmod";
	 $param['ano'] = substr($_SESSION['an_o_presupuesto'],2);
	 $niveles = array(3,4);
	 $param['Dependencia']= substr($_SESSION['user_perfil_id'],2,3);

	 $params['pmod_id'] = SafiModeloGeneral::GetNexId($param);

	 $params['docu_id'] = 'pmod';

	 $params['CadenaIdcadena'] = trim($_REQUEST['idCadenaSigiente']);

	 $cadenaIdGrupo =  SafiModeloWFCadena::GetCadenaIdGrupo($params['CadenaIdcadena']);

	 $params['CadenaGrupo'] = $cadenaIdGrupo;
	 
	 $params['DependenciaTramita'] = $param['Dependencia'];
	 
     $params['presAnno'] = $_SESSION['an_o_presupuesto'];

     
	$val = SafiModeloMPresupuestarias::InsertPmod($params);
	

	 
	 unset($_SESSION['SafiRequestVars']['nameFile']);
	 
	 
	$val === false?
     		$GLOBALS['SafiErrors']['general'][] = "Error al Guardar la modificaci&oacute;n presupuestaria (". $params['pmod_id'].")" :
     		$GLOBALS['SafiInfo']['general'][] = "modificaci&oacute;n presupuestaria (". $params['pmod_id'].") Guardada  satisfactoriamente.";


  // error_log(print_r($params,true));
	 
     self::Bandeja();
     

	}

		public function RegistrarModificar(){
			
		$params = array();
		
		 
		$params['fecha'] = $_POST['fecha'];
		$params['accionMP'] = $_POST['accionMP'];
		$params['observaciones'] = $_POST['observaciones'];
		$params['mpresupuestariadisp'] = $_POST['mpresupuestariadisp'];
		$params['mpresupuestaria'] = $_POST['mpresupuestaria'];
		$params['montoTotal'] = $_POST['montoTotalHidden'];
        $params['PerfilSiguiente'] = $_SESSION['user_perfil_id'];
	    $params['pmod_id'] =  $_POST['idPmod'];
	    $params['regisFisDigiEli'] = $_REQUEST['regisFisDigiEli'];

		// respaldos fisicos

		$params['Fisico'] = $_REQUEST['RegistroFisico'];


		// respaldos dig
		 
		if(isset($_SESSION['SafiRequestVars']['nameFile'])){

			 
			 
		$i = 0;
		

	 	foreach ($_SESSION['SafiRequestVars']['nameFile'] as $index => $valor){
	 		
	 
	 		$targetFolder = SAFI_UPLOADS_PATH.'/mpresupuestarias/'.$valor;
	 		$tempFile =  SAFI_TMP_PATH.'/'. $valor;
	 		copy($tempFile,$targetFolder);
	    
	 		$params['Digital'][] = $valor;

			 
	 	}

    }

	 unset($_SESSION['SafiRequestVars']['nameFile']);
	 
	    
     $params['lugar'] = "pmod";
     $params['presAnno'] = $_SESSION['an_o_presupuesto'];
	 $params['DependenciaTramita']= substr($_SESSION['user_perfil_id'],2,3);
	 
	 $updatePcuenta = SafiModeloMPresupuestarias::UpdatePmod($params);

	 if(!UpdatePcuenta === false){

	 	$GLOBALS['SafiInfo']['general'][] = "Modificaci&oacute;n presupuestaria (".$params['pmod_id'].") modificado  satisfactoriamente.";

	 	if($_REQUEST['regisNombreDigital']){

	 		$registro = explode(',',$_REQUEST['regisNombreDigital']);
	 		 
	 		foreach ($registro as $valor){

	 			unlink(SAFI_UPLOADS_PATH.'/mpresupuestarias/'.$valor);

	 		}

	 	}

	 }else{

	 	$GLOBALS['SafiErrors']['general'][] = "Error al modificar la modificaci&oacute;n presupuestaria (".$params['pmod_id'].").";

	 }

		$this->Bandeja();


	}
	
	

	
	
	
	
	
	
	
	public function  GuardarImg(){
		
		if (!empty($_FILES)) {
			
			
			if(!isset($_SESSION['SafiRequestVars']['nameFile'])){ $_SESSION['SafiRequestVars']['nameFile'] = array();}


		
			
			$prefijo = substr(md5(uniqid(rand())), 0, 6);

			$name = $_FILES['Filedata']['name'];

			$name2 =  $prefijo . "_" . $name;

			$_SESSION['SafiRequestVars']['nameFile'][] = $name2;

			$targetFolder = SAFI_TMP_PATH.'/';
			
			
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$targetPath = $targetFolder;
			$targetFile = rtrim($targetPath,'/') . '/' .$name2 ;
			
			// Validate the file type
			$fileTypes = array('jpg','jpeg','gif','png','pdf'); // File extensions
			$fileParts = pathinfo($_FILES['Filedata']['name']);

			if (in_array($fileParts['extension'],$fileTypes)) {
				copy($tempFile, $targetFile);

				echo "1";
			} else {

				echo "Error";
			}
  
		}
		

	}

public function Bandeja($condicion = false){
	
	    $params =array();
		$Idcadena =array();
		$lugar = 'pmod';
		
		$_GLOBALS['SafiRequestVars']['pctaPorEnviar'] = SafiModeloDocGenera::GetRegistrosEnBandeja($lugar);


	   if($_GLOBALS['SafiRequestVars']['pctaPorEnviar']){
				
						foreach ($_GLOBALS['SafiRequestVars']['pctaPorEnviar'] as $index ){
									
					$Idcadena[$index['wfca_id']]= $index['wfca_id'];		
							
			}
		}
		
		
		$GLOBALS['SafiRequestVars']['opciones'] = SafiModeloWFCadena::GetId_cadena_hijos_id_cadenas($Idcadena);

		 $_GLOBALS['SafiRequestVars']['pctaEnTransito'] =  SafiModeloDocGenera::GetRegistrosEnTransito($lugar);
		 

		 
		 
		if((!$_GLOBALS['SafiRequestVars']['pctaPorEnviar']) && (!$_GLOBALS['SafiRequestVars']['pctaEnTransito']) && ($param == null)){
			
	 	if(!$condicion) {
			$GLOBALS['SafiInfo']['general'][] = "No se han encontrado registros";

			}

		}else{
		
	  $_GLOBALS['SafiRequestVars']['pctaPorEnviar'] =  $this->FormatBandeja($_GLOBALS['SafiRequestVars']['pctaPorEnviar']);		
	  
	  $_GLOBALS['SafiRequestVars']['pctaEnTransito'] =  $this->FormatBandeja($_GLOBALS['SafiRequestVars']['pctaEnTransito']);
	  
	  $_GLOBALS['SafiRequestVars']['pctaDevuelto'] = $_SESSION['SafiRequestVars']['pctaDevuelto'];
	  
	  unset($_SESSION['SafiRequestVars']['pctaDevuelto']);
		

		

		}

		include(SAFI_VISTA_PATH ."/modificacionesPresupuestarias/mpresupuestariaBandeja.php");
		

	}
	
	
	public function  FormatBandeja($valor){

		if($valor){
			$i = 0;
			 
			foreach ($valor as $index){

			
				 
				$fechahora = explode (' ',$index['docg_fecha']);

				$fecha = explode ('-',$fechahora[0]);

				$fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' '.$fechahora[1];
				 
				$valor[$i]['docg_fecha'] = $fecha2;

				$perf_id_act = SafiModeloWFGrupo::GetWFGrupoByIdPerfil($index['perf_id_act']);

				$valor[$i]['perf_id_act'] = $perf_id_act->GetDescripcion();
				 
				if($valor[$i]['wfob_id_ini'] == 1 ){

					$_SESSION['SafiRequestVars']['pctaDevuelto'][] = $valor[$i];

					unset($valor[$i]);
					 
				}

				$i++;

 
			}

	 }

	 return $valor;
	  
	}
	
	
    public function  SearchPmodDetalle(){

    	$key = trim(utf8_decode($_REQUEST["key"]));
    	
    	
    	$revisionesDoc = SafiModeloRevisionesDoc::GetRevisionesDoc(array('idDocumento' => $key));
		 
    		if($revisionesDoc){
			 
			$cedula =  array();
			$dependenciaUsuario =  array();
			$cargoUsuario =  array();
			$params =  array();
			$opcion = array();

			foreach ($revisionesDoc as $valor){
				 
				$dependenciaUsuario[substr($valor->GetIdPerfil(), -3)] = substr($valor->GetIdPerfil(), -3);
				$cargoUsuario[substr($valor->GetIdPerfil(),0,2)] = substr($valor->GetIdPerfil(),0,2);
				$cedulas[$valor->GetLoginUsuario()] = $valor->GetLoginUsuario();
				$opcion[$valor->GetIdWFOpcion()] = $valor->GetIdWFOpcion();

			}



			$nombreYApellido = SafiModeloEmpleado::GetEmpleadosByCedulas($cedulas);
			$dependencias= SafiModeloDependencia::GetDependenciaByIds($dependenciaUsuario);
			$cargos =  SafiModeloCargo::GetCargoByCargoFundaciones($cargoUsuario);
			$opciones = SafiModeloWFOpcion::GetWFOpciones(array('idWFOpciones' => $opcion));
			 
			 
			 
			foreach ($revisionesDoc as $valor){
				 
				$params[$valor->GetId()]['pcta'] = utf8_encode($valor->GetIdDocumento());
				$params[$valor->GetId()]['nombreApellido'] = utf8_encode($nombreYApellido[$valor->GetLoginUsuario()]->GetNombres()." ".$nombreYApellido[$valor->GetLoginUsuario()]->GetApellidos());
				$params[$valor->GetId()]['cargoDependencia'] = utf8_encode($cargos[substr($valor->GetIdPerfil(),0,2)]->GetDescripcion()."(".$dependencias[substr($valor->GetIdPerfil(), -3)]->GetNombre().")");
				$params[$valor->GetId()]['fecha'] = utf8_encode($valor->GetFechaRevision());
				$params[$valor->GetId()]['opcion'] = utf8_encode($opciones[$valor->GetIdWFOpcion()]->GetNombre());

			}
			 
			 

			$GLOBALS['SafiRequestVars']['revisiones'] = $params;
		}
		

		 
		 $data = SafiModeloObservacionesDoc::GetObservacionesDoc(array($key => $key));
		 

		 
		if($data){
			 
			$dependenciaUsuario =  array();
			$cargoUsuario =  array();

			foreach ($data as $valor){
				 
				 
				$dependenciaUsuario[substr($valor['perfil'], -3)] = substr($valor['perfil'], -3);
				$cargoUsuario[substr($valor['perfil'],0,2)] = substr($valor['perfil'],0,2);
				 
			}
			 

			 
			$dependencias= SafiModeloDependencia::GetDependenciaByIds($dependenciaUsuario);
			$cargos =  SafiModeloCargo::GetCargoByCargoFundaciones($cargoUsuario);
			 
			 
			 
			$num = 0;
			foreach ($data as $valor){
				$data[$num]['perfilNombre'] = utf8_encode($cargos[substr($valor['perfil'],0,2)]->GetDescripcion()."(".$dependencias[substr($valor['perfil'], -3)]->GetNombre().")");

				$num++;
			}
			 
			$GLOBALS['SafiRequestVars']['observacionesDoc'] = $data;
		 
		 
		}    	

		$pmod =  SafiModeloMPresupuestarias::GetMpresupuesto(array("idPmod" => $key));

    	$GLOBALS['SafiRequestVars']['pmod'] = $pmod;
    	
    
    	

		include(SAFI_VISTA_PATH ."/json/pmod.php");	
    	
    }
    
	 
	public function DetallePmodPdf(){


		$key = trim(utf8_decode($_REQUEST["pmod"]));
		
		
		$pmod =  SafiModeloMPresupuestarias::GetMpresupuesto(array("idPmod" => $key));
		
		
		
		$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($key);
		
		$usario = SafiModeloEmpleado::GetEmpleadoActivoByCedula($entidadDocg->GetUsuaLogin());
		
		$GLOBALS['SafiRequestVars']['usuario'] = $usario['empl_nombres']." ".$usario['empl_apellidos'];
		
	

    	$GLOBALS['SafiRequestVars']['pmod'] = $pmod;
    	
	include(SAFI_VISTA_PATH ."/modificacionesPresupuestarias/mpresupuestaria_PDF.php");

	}



	 
	public function  ProcesarPmod(){
		
		

		
		

		$params	= array(
     	'revisiones_doc_id' => '',
     	'revisiones_doc_documento_id' =>  $_REQUEST['pmod'],
     	'revisiones_doc_usua_login' =>   $_SESSION['login'],
     	'revisiones_doc_perfil_id' =>  $_SESSION['user_perfil_id'],
     	'revisiones_doc_fecha_revision' => '',
     	'revisiones_doc_wfopcion_id' => $_REQUEST['idopcion'],
     	'revisiones_doc_firma_revision' => ''
     	
     	);

     	$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['pmod']);
     	
		
		if($entidadDocg->GetFecha()){
			
		$fechahora = explode (' ',$entidadDocg->GetFecha());

				$fecha = explode ('/',$fechahora[0]);

				$fecha  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' '.$fechahora[1];
				
				
				$entidadDocg->SetFecha($fecha);
				
		}
		     	
     	$cadenaIdGrupo =  SafiModeloWFCadena::GetCadenaIdGrupo($_REQUEST['idCadenaSigiente']);

     	$params['CadenaGrupo'] = $cadenaIdGrupo;

     	$params['CadenaIdcadena'] = $_REQUEST['idCadenaSigiente'];

     	$perfilSiguiente = SafiModeloWFCadena::GetPerfilSiguiente($params);


     	if($_REQUEST['accRealizar'] == 'Enviar'){
     		

     		$entidadDocg->SetIdWFObjeto(0);
     		$entidadDocg->SetIdWFCadena($_REQUEST['idCadenaSigiente']);
     		$entidadDocg->SetIdPerfilActual($perfilSiguiente);
     		

     		$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg,true);

     		$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
     		SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);

     		$val === false?
     		$GLOBALS['SafiErrors']['general'][] = "Error al enviar la modificaci&oacute;n presupuestaria (".$_REQUEST['pmod'].")" :
     		$GLOBALS['SafiInfo']['general'][] = "modificaci&oacute;n presupuestaria (".$_REQUEST['pmod'].") enviada  satisfactoriamente.";

     	}
     	
     

    
     	

     	if($_REQUEST['accRealizar'] == 'Anular'){

     		$param = array();


     		 $entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['pmod']);
     		 

     			$entidadDocg->SetIdEstatus(15);
     			$entidadDocg->SetIdPerfilActual('');

     			$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
     			 
     			$param['id'] = $_REQUEST['pmod'];
     			$param['estaid'] = 15;
     			$param['observacion'] = $_REQUEST['memo'];
     			$param['perfil'] =$_SESSION['user_perfil_id'];
     			$param['opcion'] = $_REQUEST['opcion'];

     			SafiModeloObservacionesDoc::InsertarObservacionesDoc($param);
     			$estaid = SafiModeloMPresupuestarias::UpdatePmodEstaId($param);
     			


     			$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
     			SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);

     			$val === false?
     			 
     			$GLOBALS['SafiErrors']['general'][] = "Error al anular la modificaci&oacute;n presupuestaria (".$_REQUEST['pmod'].")" :
     			$GLOBALS['SafiInfo']['general'][] = "modificaci&oacute;n presupuestaria (".$_REQUEST['pmod'].") anulada  satisfactoriamente.";
     			 
     			 


     	}

     	if($_REQUEST['accRealizar'] == 'Devolver'){
     		
     		
     	   $entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['pmod']);


     	   $idCadenaActual = $entidadDocg->GetIdWFCadena();
  
     		$entidadDocg->SetIdWFObjeto(1);
     		$entidadDocg->SetIdWFCadena($_REQUEST['idCadenaSigiente']);
     		$entidadDocg->SetIdPerfilActual($entidadDocg->GetIdPerfil());
     		$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
 				 
     		$param['id'] = $_REQUEST['pmod'];
     		$param['estaid'] = 7;
     		$param['observacion'] = $_REQUEST['memo'];
     		$param['perfil'] =$_SESSION['user_perfil_id'];
     		$param['opcion'] = $_REQUEST['opcion'];
     		
     		
     		SafiModeloObservacionesDoc::InsertarObservacionesDoc($param);
     	
     		SafiModeloMPresupuestarias::UpdatePmodEstaId($param);
     		
     		$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
     		SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
  
     		
     		$val === false?
     		$GLOBALS['SafiErrors']['general'][] = "Error al devolver la modificaci&oacute;n presupuestaria (".$_REQUEST['pmod'].")" :
     		$GLOBALS['SafiInfo']['general'][] = "modificaci&oacute;n presupuestaria (".$_REQUEST['pmod'].") devuelto  satisfactoriamente.";

  
     	}



     	if($_REQUEST['accRealizar'] == 'Aprobar y Finalizar'){
     		
     		$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['pmod']);
 
     		$entidadDocg->SetIdEstatus(13);
     		$entidadDocg->SetIdWFObjeto(99);
     		$entidadDocg->SetIdPerfilActual('');
     		$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
     		 
     		
     		$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
     		
     		
     		SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);

     		$param['id'] = $_REQUEST['pmod'];
     		$param['estaid'] = 13;
     		
     		SafiModeloMPresupuestarias::UpdatePmodEstaId($param);
     		

     	  		
     		$val === false?
     	     $GLOBALS['SafiErrors']['general'][] = "Error al aprobar la modificaci&oacute;n presupuestaria (".$_REQUEST['pmod'].")" :
     		$GLOBALS['SafiInfo']['general'][] = "modificaci&oacute;n presupuestaria (".$_REQUEST['pmod'].") aprobado  satisfactoriamente.";
     	
     		
     	}

      $this->Bandeja(true);

	}
	
	
   public function Modificar(){


		$key = trim(utf8_decode($_REQUEST["pmod"]));
		 
		$pmod =  SafiModeloMPresupuestarias::GetMpresupuesto(array("idPmod" => $key));

		$this->Ingresar($pmod);

	}
	
	
	
	
public function Buscar(){
	

		$proyectos = SafiModeloProyecto::GetAllProyectosAprobados();
		$GLOBALS['SafiRequestVars']['proyectos'] = $proyectos;

		$acc =SafiModeloAccionCentralizada::GetAllAccionesCentralizadasAprobadas();
		$GLOBALS['SafiRequestVars']['acc'] = $acc;


		include(SAFI_VISTA_PATH ."/modificacionesPresupuestarias/mpresupuestariaBuscar.php");	

	}
	
	
	public function BuscarPmodAccion(){
		
	$params = array();

	$params ['agno'] = $_REQUEST['agnoComp'];
	$params ['nPmod'] = $_REQUEST['nPmod'];
	$params ['tipo'] = $_REQUEST['tipo'];
	$params ['PartidaBusqueda'] = $_REQUEST['PartidaBusqueda'];
	$params ['ProyAccVal'] = $_REQUEST['compProyAccVal'];
	$params ['palabraClave'] = $_REQUEST['palabraClave'];	
	$params['txt_inicio']  = $_REQUEST['txt_inicio'];	
    $params['hid_hasta_itin']  = $_REQUEST['hid_hasta_itin'];	
		
	$pmod =  SafiModeloMPresupuestarias::GetMpresupuestarias($params,true);
	
	
	$params = array();
	
	if($pmod){
		
		foreach ($pmod as $index => $valor){
			
			$params[$index] = $index;

		}

		$usuarios = SafiModeloDocGenera::GetCedulaDocGenera($params);

		
		}
$GLOBALS['SafiRequestVars']['pmod'] = $pmod;
$GLOBALS['SafiRequestVars']['usuarios'] = $usuarios;

 self::Buscar();
	
	}
	
	
	
	
} 

new Mpresupuestarias();