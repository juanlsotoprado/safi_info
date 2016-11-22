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

require_once(SAFI_INCLUDE_PATH.'/tabmenu/tabmenuItems.php');
require_once(SAFI_MODELO_PATH. '/partida.php');
require_once(SAFI_MODELO_PATH. '/disponibilidadPcuenta.php');
require_once(SAFI_MODELO_PATH. '/cargo.php');
require_once(SAFI_MODELO_PATH. '/firma.php');
require_once(SAFI_MODELO_PATH. '/estatus.php');
require_once(SAFI_MODELO_PATH. '/wfgrupo.php');
require_once(SAFI_MODELO_PATH. '/wfopcion.php');
require_once(SAFI_MODELO_PATH. '/proyecto.php');
require_once(SAFI_MODELO_PATH. '/accioncentralizada.php');
require_once(SAFI_MODELO_PATH. '/proyectoespecifica.php');
require_once(SAFI_MODELO_PATH. '/accioncentralizadaespecifica.php');
require_once(SAFI_MODELO_PATH. '/empleado.php');
require_once(SAFI_MODELO_PATH. '/dependencia.php');
require_once(SAFI_MODELO_PATH. '/puntoCuentaAsunto.php');
require_once(SAFI_MODELO_PATH. '/general.php');
require_once(SAFI_MODELO_PATH. '/proyecto.php');
require_once(SAFI_MODELO_PATH. '/accioncentralizada.php');
require_once(SAFI_MODELO_PATH. '/docgenera.php');
require_once(SAFI_MODELO_PATH. '/wfcadena.php');
require_once(SAFI_MODELO_PATH. '/puntoCuenta.php');
require_once(SAFI_MODELO_PATH. '/puntoCuentaImputa.php');
require_once(SAFI_MODELO_PATH. '/puntoCuentaRespaldo.php');
require_once(SAFI_MODELO_PATH. '/revisionesDoc.php');
require_once(SAFI_MODELO_PATH. '/observacionesDoc.php');
require_once(SAFI_MODELO_PATH. '/compromiso.php');



if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../index.php',false);
	ob_end_flush();
	exit;
}

class Pcta extends Acciones
{
	public $_yearPresupuestario = null;
	
	public function __construct()
	{
		$this->_yearPresupuestario = $_SESSION['an_o_presupuesto'];
		
		/* Parche para que los pctas se resgistren con fecha y código del año anterior */
		//$this->_yearPresupuestario = '2014';
		
		parent::__construct();
	}
	
	public function Ingresar($puntosCuentaModificar = null)
	{
		$params =array();

		$user_perfil_id = $_SESSION['user_perfil_id'];

		$id_depe = substr($_SESSION['user_perfil_id'],2,3);

		$preparadoPara = SafiModeloFirma::GetFirmaByPerfiles(array(PERFIL_DIRECTOR_EJECUTIVO,PERFIL_PRESIDENTE));


		$i= 0;
		foreach($preparadoPara as $index => $valor){

			$param[$i]['id'] =  $valor['cedula_empleado'];
			$param[$i]['nombre'] = $valor['nombre_cargo'];

			$i++;
		}

		 
		$param[$i]['id'] = $param[0]['id'].'/'.$param[1]['id'];
		$param[$i]['nombre'] = $param[0]['nombre'].'/'.$param[1]['nombre'];

		$GLOBALS['SafiRequestVars']['preparado_para']= $param;
		$lugar = 'pcta';


		$id_cadena_inicial = SafiModeloWFCadena::GetWfca_id_inicial($lugar);
		$GLOBALS['SafiRequestVars']['idCadenaActual'] = $id_cadena_inicial;

		$id_HijosCadena = SafiModeloWFCadena::GetId_cadena_hijo_id_cadena($id_cadena_inicial);

		$preparadoPara= SafiModeloEmpleado::nombre_apellido_perfil($id_depe,1);
		$presentadoPor= SafiModeloEmpleado::nombre_apellido_perfil($id_depe,2);
		$DependenciaQueTramita= SafiModeloDependencia::GetDependenciaById($id_depe);
		$PctaAsusnto= SafiModeloPuntoCuentaAsunto::GetPctaAsusnto();
		$proyectos = SafiModeloProyecto::GetAllProyectosAprobados();
		$acc =SafiModeloAccionCentralizada::GetAllAccionesCentralizadasAprobadas();



		$GLOBALS['SafiRequestVars']['idCadenaPadre'] = $id_cadena;


		$GLOBALS['SafiRequestVars']['opciones'] = $id_HijosCadena;
		$GLOBALS['SafiRequestVars']['proyectos'] = $proyectos;
		$GLOBALS['SafiRequestVars']['acc'] = $acc;
		$GLOBALS['SafiRequestVars']['preparadoPara'] = $preparadoPara;
		$GLOBALS['SafiRequestVars']['presentadoPor'] = $presentadoPor;
		$GLOBALS['SafiRequestVars']['DependenciaQueTramita'] = $DependenciaQueTramita;

		$GLOBALS['SafiRequestVars']['PctaAsusnto'] = $PctaAsusnto;


		$pctaAsociado = SafiModeloPuntoCuenta::GetPuntosCuentaAsociados($id_depe);

		$GLOBALS['SafiRequestVars']['pctaAsociado'] = $pctaAsociado;

		if($puntosCuentaModificar){
				
			$GLOBALS['SafiRequestVars']['puntosCuenta'] =  $puntosCuentaModificar;

		}

		include(SAFI_VISTA_PATH ."/pcta/puntoCuenta.php");

	}


	
	
	
	
	
	
	
	
	public function DependenciaSolicitadoPor(){
			
		$ci_empleado = trim($_REQUEST["ci_empleado"]);

		$Dependencia = SafiModeloEmpleado::GetEmpleadoActivoByCedula($ci_empleado);
			
		$DependenciaSolicitado = SafiModeloDependencia::GetDependenciaById($Dependencia['depe_cosige']);

		$GLOBALS['SafiRequestVars']['DependenciaSolicitado'] = $DependenciaSolicitado;

		include(SAFI_VISTA_PATH ."/json/DependenciaSolicitadoPor.php");

	}


	public function  SearchProveedorSugerido(){
			
	$key = trim($_REQUEST["key"]);
			

	 $ProveedorSugerido  = SafiModeloEmpleado::GetProveedorSugerido(utf8_decode($key));



	 $GLOBALS['SafiRequestVars']['ProveedorSugerido'] = $ProveedorSugerido;
	  

	 include(SAFI_VISTA_PATH ."/json/ProveedorSugerido.php");
	  


	}

	public function  Searchcategoria(){
			
		$key = trim(utf8_decode($_REQUEST["key"]));
		
		$id_depe = substr($_SESSION['user_perfil_id'],2,3);
		
		if (DEPENDENCIA_OFICINA_TALENTO_HUMANO != $id_depe ){
			$dependencia = trim($_REQUEST['dependencia']);
		}else{
			$dependencia = '';
		}
				
		$restrictivo = trim($_REQUEST['restrictivo']);
		$idproyAcc = trim($_REQUEST['idproyAcc']);
		$tipoproacc = trim($_REQUEST['tipoproacc']);
		$yearPresupuestario = trim($_REQUEST['anno']);
		
		if($yearPresupuestario == null || $yearPresupuestario == "")
			$yearPresupuestario = $this->_yearPresupuestario;

		$params = array(
			'key'=> $key,
			'dependencia'=> $dependencia,
			'idproyAcc' => $idproyAcc,
			'tipoproacc' => $tipoproacc,
			'restrictivo' => $restrictivo,
			'yearPresupuestario' => $yearPresupuestario
		);

		$categorias = SafiModeloGeneral::GetAllAccionesEspecificasCortas($params);

		$GLOBALS['SafiRequestVars']['categorias'] = $categorias;
	  
		include(SAFI_VISTA_PATH ."/json/Categoria.php");
	  
	}

	public function  SearchPctaProyAcc(){
		$id_depe = substr($_SESSION['user_perfil_id'],2,3);
			
		$key = trim(utf8_decode($_REQUEST["key"]));
		$dependencia = trim($id_depe);
		$anno = trim($_REQUEST['anno']);
		$yearPresupuestario = trim($_REQUEST['anno']);
		
		if($yearPresupuestario == null || $yearPresupuestario == "")
			$yearPresupuestario = $this->_yearPresupuestario;

		$params = array(
			'key'=> $key,
			'dependencia'=> $dependencia,
			'idproyAcc' => null,
			'tipoproacc' => 2,
			'yearPresupuestario' => $yearPresupuestario
		);

		$categorias = SafiModeloGeneral::GetAllAccionesEspecificasCortas($params);

		$GLOBALS['SafiRequestVars']['categorias'] = $categorias;

		include(SAFI_VISTA_PATH ."/json/Categoria.php");
	}

	public function  Registrar(){


		$params = array();
		$params['fecha'] = trim($_REQUEST['fecha']);
		$params['preparado_para']=trim($_REQUEST['preparado_para']);
		$params['SolicitadoPor'] = $_REQUEST['SolicitadoPor'];
		$params['presentado_por'] = $_REQUEST['presentado_por'];
		$params['DependenciaTramita']=$_REQUEST['DependenciaTramita'];
		$params['DependenciaSolicitante'] = $_REQUEST['DependenciaSolicitante'];
		$params['pctaAsunto'] = $_REQUEST['pctaAsunto'];
		$params['pctaAsociado'] = $_REQUEST['pctaAsociado'] != null? $_REQUEST['pctaAsociado']: '';

		$params['pcuenta_descripcion'] = $_REQUEST['pcuenta_descripcionVal'];
		$params['justificacion'] = $_REQUEST['justificacion'];
		$params['convenio'] = $_REQUEST['convenio'];
		$params['garantia'] = $_REQUEST['garantia'];

		 
		$params['ProveedorSugeridoval'] = $_REQUEST['ProveedorSugeridoval'] == null ? '~'.$_REQUEST['ProveedorSugerido']: $_REQUEST['ProveedorSugeridoval'];

		$params['observaciones'] = $_REQUEST['observaciones'];
		$params['recursos'] = $_REQUEST['op_recursos'];


		// condiciones de pago
		 
		$params['cond_pago'] = $_REQUEST['cond_pago'];
		$params['montoTotal'] = $_REQUEST['montoTotalHidden'];
		$params['partida_pcta'] = $_REQUEST['pcta'];
		 
		// respaldos fisicos

		$params['Fisico'] = $_REQUEST['RegistroFisico'];


		// respaldos dig
		 
		if(isset($_SESSION['SafiRequestVars']['nameFile'])){

		 
			$i = 0;
	 	foreach ( $_SESSION['SafiRequestVars']['nameFile'] as $index => $valor){
	 		
	 		

	 		$targetFolder = SAFI_UPLOADS_PATH.'/pcta/'.$valor;
	 		$tempFile =  SAFI_TMP_PATH.'/'. $valor;
	 		copy($tempFile,$targetFolder);
	    
	 		$params['Digital'][] = $valor;

	 	}
	 
	 }

	 unset($_SESSION['SafiRequestVars']['nameFile']);

	 $param =array();

	 $params['PerfilSiguiente'] = $_SESSION['user_perfil_id'];
	 $param['lugar'] = "pcta";
	 $param['ano'] = substr ($_SESSION['an_o_presupuesto'],2);
	 $param['Dependencia'] = $params['DependenciaTramita'];



	 $params['pcta_id'] = SafiModeloGeneral::GetNexId($param);


	 $params['docu_id'] = 'pcta';

	 $params['CadenaIdcadena'] = trim($_REQUEST['idCadenaSigiente']);

	 $cadenaIdGrupo =  SafiModeloWFCadena::GetCadenaIdGrupo($params['CadenaIdcadena']);

	 $params['CadenaGrupo'] = $cadenaIdGrupo;

	 $insertPcuenta = SafiModeloPuntoCuenta::InsertPcuenta($params);

	 if(!$insertPcuenta === false){

	 	$GLOBALS['SafiInfo']['general'][] = "Punto de cuenta (<a href='#dialog' docgId='".$params['pcta_id']."'class='detalleOpcion' opcion='null'>".$params['pcta_id']." </a>) guardado  satisfactoriamente.";

	 	
	 }else{

	 	$GLOBALS['SafiErrors']['general'][] = "Error al insertar punto de cuenta";

	 }
	 	

	 $this->Bandeja();


	}

	public function  GuardarImg(){
		
		//	error_log('entro');

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
			$fileTypes = array('jpg','jpeg','gif','png','odt','txt','ods','xls','bmp','pdf','pdt','odp'); // File extensions
			$fileParts = pathinfo($_FILES['Filedata']['name']);

			if (in_array($fileParts['extension'],$fileTypes)) {
				copy($tempFile, $targetFile);

			//	error_log($name .' bien');
			} else {

			//	error_log($name .' mal');
			}

		}

	}



	public function Bandeja($param = null){
		 
		$params =array();
		$pctaIdcadena =array();
		$lugar = 'pcta';
	
		
		$_GLOBALS['SafiRequestVars']['pctaPorEnviar'] = SafiModeloDocGenera::GetRegistrosEnBandeja($lugar);
		
			if($_GLOBALS['SafiRequestVars']['pctaPorEnviar']){
						foreach ($_GLOBALS['SafiRequestVars']['pctaPorEnviar'] as $index ){
									
					$pctaIdcadena[$index['wfca_id']]= $index['wfca_id'];		
							
			}
		}
		
		
        $GLOBALS['SafiRequestVars']['opciones'] = SafiModeloWFCadena::GetId_cadena_hijos_id_cadenas($pctaIdcadena);
		
        $_GLOBALS['SafiRequestVars']['pctaEnTransito'] =  SafiModeloDocGenera::GetRegistrosEnTransito($lugar);
        
		if((!$_GLOBALS['SafiRequestVars']['pctaPorEnviar']) && (!$_GLOBALS['SafiRequestVars']['pctaEnTransito']) && ($param == null)){

			$GLOBALS['SafiInfo']['general'][] = "No se han encontrado registros";

		}else{
	
			
	  $_GLOBALS['SafiRequestVars']['pctaPorEnviar'] =  $this->FormatBandeja($_GLOBALS['SafiRequestVars']['pctaPorEnviar']);		
	  $_GLOBALS['SafiRequestVars']['pctaEnTransito'] =   $this->FormatBandeja($_GLOBALS['SafiRequestVars']['pctaEnTransito']);
	  $_GLOBALS['SafiRequestVars']['pctaDevuelto'] = $_SESSION['SafiRequestVars']['pctaDevuelto'];
	  unset($_SESSION['SafiRequestVars']['pctaDevuelto']);

		}
		
				 
		include(SAFI_VISTA_PATH ."/pcta/puntoCuentaBandeja.php");

	}


	public function  SearchDetallePcta(){

		$key = trim(utf8_decode($_REQUEST["key"]));

		$puntoCuen =  SafiModeloPuntoCuenta::GetPuntoCuenta(array("idPuntoCuenta" => $key));

		
		
		$ciUsuario = $puntoCuen->GetUsuario() != null? $puntoCuen->GetUsuario()->GetId() : null;
		
		$entidadEmpledo = $ciUsuario != null? SafiModeloEmpleado::GetEmpleadoByCedula($ciUsuario) : null;

        $entidadEmpledo != null? $puntoCuen->GetUsuario()->SetId($entidadEmpledo->GetNombres().' '.$entidadEmpledo->GetApellidos()): '';
		
		$GLOBALS['SafiRequestVars']['puntosCuenta'] = $puntoCuen;
		
		
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
       
		$alcances =   SafiModeloPuntoCuenta::GetIdPctaAlcance($key);
		
		$GLOBALS['SafiRequestVars']['alcances'] = $alcances;

		$devueltoFinalizado = SafiModeloObservacionesDoc::GetObservacionesDocDevueltoFinalizado($key);
		$GLOBALS['SafiRequestVars']['devueltoFinalizado'] = $devueltoFinalizado;
		// error_log(print_r($data,true));

	 include(SAFI_VISTA_PATH ."/json/puntoCuenta.php");

	}
	
	
	
  public function  SearchImputasPcuentas(){
  	
		$alcances = array();
		$datas = array();

		$key = trim(utf8_decode($_REQUEST["key"]));	
	    $alcances =   SafiModeloPuntoCuenta::GetIdPctaAlcance($key,true/* */);
        $alcances[] = $key;
        
      $imputas =   SafiModeloPuntoCuentaImputa::GetPctaImputasPctaId($alcances);
  
      foreach($imputas as $valor){

      	 foreach($valor as $puntoCuentaImputa){
        if($puntoCuentaImputa){
        	
			   $puntoCuentaImputa->UTF8Encode();
               $datas[$puntoCuentaImputa->GetPartida()->GetId()]  = $puntoCuentaImputa->ToArray();

	      }
	      
      	 }   

       }

       $GLOBALS['SafiRequestVars']['puntosCuentaImputa'] = $datas;

        include(SAFI_VISTA_PATH ."/json/puntoCuentaImputa.php");

	}

	public function  FormatBandeja($valor){

		if($valor){
			$i = 0;
			 
			foreach ($valor as $index){

				$PctaAsusnto = SafiModeloPuntoCuentaAsunto::GetPctaAsusntoId($index['pcta_asunto']);
				 
				$fechahora = explode (' ',$index['docg_fecha']);

				$fecha = explode ('-',$fechahora[0]);

				$fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' '.$fechahora[1];
				 
				$valor[$i]['docg_fecha'] = $fecha2;

				$valor[$i]['pcta_asunto'] =  $PctaAsusnto[0]->GetNombre();


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



	 
	 
	 
	public function  ProcesarPcta(){

		$params	= array(
     	'revisiones_doc_id' => '',
     	'revisiones_doc_documento_id' =>  $_REQUEST['pcta'],
     	'revisiones_doc_usua_login' =>   $_SESSION['login'],
     	'revisiones_doc_perfil_id' =>  $_SESSION['user_perfil_id'],
     	'revisiones_doc_fecha_revision' => '',
     	'revisiones_doc_wfopcion_id' => $_REQUEST['idopcion'],
     	'revisiones_doc_firma_revision' => ''
     	
     	);

     	$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['pcta']);
     	$cadenaIdGrupo =  SafiModeloWFCadena::GetCadenaIdGrupo($_REQUEST['idCadenaSigiente']);

     	$params['CadenaGrupo'] = $cadenaIdGrupo;

     	$params['CadenaIdcadena'] = $_REQUEST['idCadenaSigiente'];

     	$perfilSiguiente = SafiModeloWFCadena::GetPerfilSiguiente($params);


     	if($_REQUEST['accRealizar'] == 'Enviar'){
     		 
     		 

     		 $entidadDocg->SetIdWFObjeto(0);
     		$entidadDocg->SetIdWFCadena($_REQUEST['idCadenaSigiente']);
     		$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
     		SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
     		$entidadDocg->SetIdPerfilActual($perfilSiguiente);
     		$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);


     		$val === false?
     		$GLOBALS['SafiErrors']['general'][] = "Error al enviar el punto de cuenta (<a href='#dialog' docgId='".$_REQUEST['pcta']."'class='detalleOpcion' opcion='null'>".$_REQUEST['pcta']." </a>)" :
     		$GLOBALS['SafiInfo']['general'][] = "Punto de cuenta (<a href='#dialog' docgId='".$_REQUEST['pcta']."'class='detalleOpcion' opcion='null'>".$_REQUEST['pcta']." </a>) enviado  satisfactoriamente.";
     		
     	}

     	if($_REQUEST['accRealizar'] == 'Aprobar'){
     		
     		$idCadenaSigiente = $_REQUEST['idCadenaSigiente'];
     		
     		
            $perfilSiguiente2 = SafiModeloWFCadena::GetPerfilSiguiente2($params);

            
				if(substr($perfilSiguiente,2,3) == substr($_REQUEST['pcta'],5,3) AND $perfilSiguiente2[$perfilSiguiente] == false){
						
					$opciones = SafiModeloWFCadena::GetId_cadena_hijos_id_cadenas(array( $_REQUEST['idCadenaSigiente'] => $_REQUEST['idCadenaSigiente']));

					
					
					foreach ($opciones[$idCadenaSigiente] as $ndex => $valor){
					
					      if($valor['wfop_descrip'] == 'Aprobar'){
					      
					     $idCadenaSigiente = $valor['id_cadena_hijo'];
					      
					      }else{
					      	
					      	if($valor['wfop_descrip'] == 'Aprobar y Finalizar'){
					      
					         $idCadenaSigiente = 'Aprobar y Finalizar';
					      
					      	}
					      
					      }
					
					}
					
          if($perfilSiguiente){  
          	
          
     	$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['pcta']);
     	
     	$cadenaIdGrupo =  SafiModeloWFCadena::GetCadenaIdGrupo($idCadenaSigiente);

     	$params['CadenaGrupo'] = $cadenaIdGrupo;

     	$params['CadenaIdcadena'] = $_REQUEST['idCadenaSigiente'];

     	$perfilSiguiente = SafiModeloWFCadena::GetPerfilSiguiente($params);

          }
    
				    }
				    
			if($perfilSiguiente){    
				    
				    
            $entidadDocg->SetIdWFCadena($idCadenaSigiente);
     		$entidadDocg->SetIdPerfilActual($perfilSiguiente);
     		$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
     		
     		
     		
     		$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
     		SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);

     		
     		$cargo = substr($_SESSION['user_perfil_id'],0,2);
     		if($cargo == substr(PERFIL_JEFE_PRESUPUESTO,0,2)){

     			$partida_pcta = array();
     			 
     			$pctaImputa = SafiModeloPuntoCuentaImputa::GetPctaImputaPctaId($_REQUEST['pcta']);
     			

     			if($pctaImputa){
         foreach ($pctaImputa as $index => $valor){

         	$partida_pcta['tipo'][$index] = $valor->GetTipoImpu();
         	$partida_pcta['codPartida'][$index] = $valor->GetPartida()->GetId();
         	$partida_pcta['monto'][$index] = $valor->GetMonto();

          if($valor->GetTipoImpu() == 1){

          	$partida_pcta['codProyAcc'][$index] = $valor->GetProyecto()->GetId();
          	$partida_pcta['codProyAccEsp'][$index] = $valor->GetProyectoEspecifica()->GetId();

          } else{

          	$partida_pcta['codProyAcc'][$index] = $valor->GetAccionCentralizada()->GetId();
          	$partida_pcta['codProyAccEsp'][$index] = $valor->GetAccionCentralizadaEspecifica()->GetId();

          }
         }
   			



         if($partida_pcta){

         	$params['partida_pcta'] = $partida_pcta;
         	$params['pcta_id'] = $_REQUEST['pcta'];
         	$params['DependenciaTramita'] = $valor->GetDependencia()->GetId();

         	SafiModeloPuntoCuentaImputa::InsertPctaImputaTraza($params);

         }

     			}

     		}
     		 
     		
     	
			}else{
			
			 $entidadDocg->SetIdEstatus(13);
     		$entidadDocg->SetIdWFObjeto(99);
     		$entidadDocg->SetIdPerfilActual('');
     		$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
     		 
     		
     		$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
     		SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);

     		$params['idPcta'] = $_REQUEST['pcta'];
     		$params['estaid'] = 13;
     		SafiModeloPuntoCuenta::UpdatePcuentaEstaId($params);
			
			
			}
			
			$val === false?
     		$GLOBALS['SafiErrors']['general'][] = "Error al aprobar el punto de cuenta (<a href='#dialog' docgId='".$_REQUEST['pcta']."'class='detalleOpcion' opcion='null'>".$_REQUEST['pcta']." </a>)" :
     		$GLOBALS['SafiInfo']['general'][] = "Punto de cuenta (<a href='#dialog' docgId='".$_REQUEST['pcta']."'class='detalleOpcion' opcion='null'>".$_REQUEST['pcta']." </a>) aprobado  satisfactoriamente.";
			

			
     	}

     	if($_REQUEST['accRealizar'] == 'Anular'){

     		$param = array();


     		$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['pcta']);
     		 
     		$result =   SafiModeloPuntoCuenta::GetIdPctaAlcance($_REQUEST['pcta']);


     		if($result){

     			$result[] = $_REQUEST['pcta'];
     			 
     			$entidadDocg2 = SafiModeloDocGenera::GetDocGeneraByIdsDocuments($result);

     			 
     			foreach ($result as $value){


     				$params['PuntoCuenta'][$value]['idPcta'] = $value;
     				$params['PuntoCuenta'][$value]['estaid'] = 15;
     				$params['observacion'] = $_REQUEST['memo'];
     				$params['perfil'] = $_SESSION['user_perfil_id'];
     				$params['opcion'] = $_REQUEST['opcion'];
     				 

     				$entidadDocg2['docGenera'][$value]->SetIdEstatus(15);
     				$entidadDocg2['docGenera'][$value]->SetIdPerfilActual('');

        }


        $val = SafiModeloDocGenera::ActualizarDocGeneras($entidadDocg2['docGenera'],$params);



        $val === false?
        $GLOBALS['SafiErrors']['general'][] = "Error al anular el punto de cuenta (<a href='#dialog' docgId='".$_REQUEST['pcta']."'class='detalleOpcion' opcion='null'>".$_REQUEST['pcta']." </a>)" :
        $GLOBALS['SafiInfo']['general'][] = "Punto de cuenta (<a href='#dialog' docgId='".$_REQUEST['pcta']."'class='detalleOpcion' opcion='null'>".$_REQUEST['pcta']." </a>) anulado  satisfactoriamente en conjunto con todos sus alcances.";





     		}else{
     			

     			$entidadDocg->SetIdEstatus(15);
     			$entidadDocg->SetIdPerfilActual('');

     			$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
     			 
     			$param['idPcta'] = $_REQUEST['pcta'];
     			$param['estaid'] = 15;
     			$param['observacion'] = $_REQUEST['memo'];
     			$param['perfil'] =$_SESSION['user_perfil_id'];
     			$param['opcion'] = $_REQUEST['opcion'];

     			SafiModeloObservacionesDoc::InsertarObservacionesDoc($param);
     			$estaid = SafiModeloPuntoCuenta::UpdatePcuentaEstaId($param);



     			$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
     			SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);

     			$val === false?
     			 
     			$GLOBALS['SafiErrors']['general'][] = "Error al anular el punto de cuenta (<a href='#dialog' docgId='".$_REQUEST['pcta']."'class='detalleOpcion' opcion='null'>".$_REQUEST['pcta']." </a>)" :
     			$GLOBALS['SafiInfo']['general'][] = "Punto de cuenta (<a href='#dialog' docgId='".$_REQUEST['pcta']."'class='detalleOpcion' opcion='null'>".$_REQUEST['pcta']." </a>) anulado  satisfactoriamente.";
     			 
     			 
     		}
     		 

     	}


     	if($_REQUEST['accRealizar'] == 'Devolver'){
     	
        $idCadenaActual = $entidadDocg->GetIdWFCadena();
  
        $idCadenaActual != false? $data = SafiModeloDocGenera::GetCadenasSigientes($idCadenaActual) : '';

        
         if($data){
         	
         	if(PERFIL_JEFE_PRESUPUESTO != $_SESSION['user_perfil_id']){
         		
         		 foreach ($data AS $valor){

     	  	$IdCadenaSiguientes[] = $valor['idcadenasigiente'];


     	  }
     	  
     	  $result =  SafiModeloWFCadena::GetCadenasIdGrupos($IdCadenaSiguientes);
     	  $result != false? $result2 = SafiModeloWFGrupo::GetIdPerfilWFGrupoBy($result): '' ;
     	  

     	  if($result2){
     	  	$result =false;
     	  	foreach ($result2 AS $valor){

     	  		$valor == PERFIL_JEFE_PRESUPUESTO? $result = true :'';
     	  		Break;
     	  		 
     	  		 
     	  	}
     	  	
     	  	

     	  	 if($result != true){
     	  	 	
     	  	 	
     	  	   $partida_pcta = array();
        		$pctaImputa = SafiModeloPuntoCuentaImputa::GetPctaImputaPctaId($_REQUEST['pcta']);

        		if($pctaImputa){
        			foreach ($pctaImputa as $index => $valor){

        				$partida_pcta['tipo'][$index] = $valor->GetTipoImpu();
        				$partida_pcta['codPartida'][$index] = $valor->GetPartida()->GetId();
        				$partida_pcta['monto'][$index] = ((-1 )*($valor->GetMonto()));

        				if($valor->GetTipoImpu() == 1){

        					$partida_pcta['codProyAcc'][$index] = $valor->GetProyecto()->GetId();
        					$partida_pcta['codProyAccEsp'][$index] = $valor->GetProyectoEspecifica()->GetId();

        				} else{

        					$partida_pcta['codProyAcc'][$index] = $valor->GetAccionCentralizada()->GetId();
        					$partida_pcta['codProyAccEsp'][$index] = $valor->GetAccionCentralizadaEspecifica()->GetId();

        				}
        			}

        			if($partida_pcta){

        				$params['partida_pcta'] = $partida_pcta;
        				$params['pcta_id'] = $_REQUEST['pcta'];
        				$params['DependenciaTramita'] = $valor->GetDependencia()->GetId();
        				SafiModeloPuntoCuentaImputa::InsertPctaImputaTraza($params);

        			}

        		} 
     	  	 	
     	  	 	
     	  	 }

     	    }	
       	}
         		

         }else{

                $partida_pcta = array();
        		$pctaImputa = SafiModeloPuntoCuentaImputa::GetPctaImputaPctaId($_REQUEST['pcta']);

        		if($pctaImputa){
        			foreach ($pctaImputa as $index => $valor){

        				$partida_pcta['tipo'][$index] = $valor->GetTipoImpu();
        				$partida_pcta['codPartida'][$index] = $valor->GetPartida()->GetId();
        				$partida_pcta['monto'][$index] = ((-1 )*($valor->GetMonto()));

        				if($valor->GetTipoImpu() == 1){

        					$partida_pcta['codProyAcc'][$index] = $valor->GetProyecto()->GetId();
        					$partida_pcta['codProyAccEsp'][$index] = $valor->GetProyectoEspecifica()->GetId();

        				} else{

        					$partida_pcta['codProyAcc'][$index] = $valor->GetAccionCentralizada()->GetId();
        					$partida_pcta['codProyAccEsp'][$index] = $valor->GetAccionCentralizadaEspecifica()->GetId();

        				}
        			}

        			if($partida_pcta){

        				$params['partida_pcta'] = $partida_pcta;
        				$params['pcta_id'] = $_REQUEST['pcta'];
        				$params['DependenciaTramita'] = $valor->GetDependencia()->GetId();
        				SafiModeloPuntoCuentaImputa::InsertPctaImputaTraza($params);

        			}

        		}

         
         }

     		$entidadDocg->SetIdWFObjeto(1);
     		$entidadDocg->SetIdWFCadena($_REQUEST['idCadenaSigiente']);
     		$entidadDocg->SetIdPerfilActual($entidadDocg->GetIdPerfil());
     		$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
     		$params['idPcta'] = $_REQUEST['pcta'];
     		$params['estaid'] = 7;
     		$params['observacion'] = $_REQUEST['memo'];
     		$params['perfil'] =$_SESSION['user_perfil_id'];
     		$params['opcion'] = $_REQUEST['opcion'];

     		SafiModeloObservacionesDoc::InsertarObservacionesDoc($params);
     		SafiModeloPuntoCuenta::UpdatePcuentaEstaId($params);
     		$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
     		SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
  
     		
     		$val === false?
     		$GLOBALS['SafiErrors']['general'][] = "Error al devolver el punto de cuenta (<a href='#dialog' docgId='".$_REQUEST['pcta']."'class='detalleOpcion' opcion='null'>".$_REQUEST['pcta']." </a>)" :
     		$GLOBALS['SafiInfo']['general'][] = "Punto de cuenta (<a href='#dialog' docgId='".$_REQUEST['pcta']."'class='detalleOpcion' opcion='null'>".$_REQUEST['pcta']." </a>) devuelto  satisfactoriamente.";


     	}




     	if($_REQUEST['accRealizar'] == 'DevolverAprobado'){

     		
     		$entidadDocg->SetIdEstatus(7);
     		$entidadDocg->SetIdWFObjeto(1);
     		$entidadDocg->SetIdWFCadena(401);
     		$entidadDocg->SetIdPerfilActual($entidadDocg->GetIdPerfil());
     		$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
     		$params['idPcta'] = $_REQUEST['pcta'];
     		$params['estaid'] = 7;
     		$params['observacion'] = $_REQUEST['memo'];
     		$params['perfil'] =$_SESSION['user_perfil_id'];
     		$params['opcion'] = $_REQUEST['opcion'];
     		$params['revisiones_doc_wfopcion_id'] = 107;

       SafiModeloObservacionesDoc::InsertarObservacionesDoc($params);



       SafiModeloPuntoCuenta::UpdatePcuentaEstaId($params);



       $objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
       SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);

        
     	 $result =   SafiModeloPuntoCuenta::GetIdPctaAlcance($params['idPcta']);


     	 if($result){
     	 	 
        $entidadDocg2 = SafiModeloDocGenera::GetDocGeneraByIdsDocuments($result);
         
        foreach ($result as $value){

        	$alcances .= "* ".$value."<br/> ";
        	 
        	$param['PuntoCuenta'][$value]['idPcta'] = $value;
        	$param['PuntoCuenta'][$value]['estaid'] = 15;

        	 
        	$entidadDocg2['docGenera'][$value]->SetIdEstatus(15);
        	$entidadDocg2['docGenera'][$value]->SetIdPerfilActual('');

        }

        $val = SafiModeloDocGenera::ActualizarDocGeneras($entidadDocg2['docGenera'],$param);

     	 }
     	
     	      	$partida_pcta = array();
        		$pctaImputa = SafiModeloPuntoCuentaImputa::GetPctaImputaPctaId($_REQUEST['pcta']);

        		if($pctaImputa){
        			foreach ($pctaImputa as $index => $valor){

        				$partida_pcta['tipo'][$index] = $valor->GetTipoImpu();
        				$partida_pcta['codPartida'][$index] = $valor->GetPartida()->GetId();
        				$partida_pcta['monto'][$index] = ((-1 )*($valor->GetMonto()));

        				if($valor->GetTipoImpu() == 1){

        					$partida_pcta['codProyAcc'][$index] = $valor->GetProyecto()->GetId();
        					$partida_pcta['codProyAccEsp'][$index] = $valor->GetProyectoEspecifica()->GetId();

        				} else{

        					$partida_pcta['codProyAcc'][$index] = $valor->GetAccionCentralizada()->GetId();
        					$partida_pcta['codProyAccEsp'][$index] = $valor->GetAccionCentralizadaEspecifica()->GetId();

        				}
        			}

        			if($partida_pcta){

        				$params['partida_pcta'] = $partida_pcta;
        				$params['pcta_id'] = $_REQUEST['pcta'];
        				$params['DependenciaTramita'] = $valor->GetDependencia()->GetId();
        				SafiModeloPuntoCuentaImputa::InsertPctaImputaTraza($params);

        			}

        		} 


     	 $val === false?
     		$GLOBALS['SafiErrors']['general'][] = "Error al devolver el punto de cuenta (<a href='#dialog' docgId='".$_REQUEST['pcta']."'class='detalleOpcion' opcion='null'>".$_REQUEST['pcta']." </a>)" :
     		$GLOBALS['SafiInfo']['general'][] = "Punto de cuenta (<a href='#dialog' docgId='".$_REQUEST['pcta']."'class='detalleOpcion' opcion='null'>".$_REQUEST['pcta']." </a>) devuelto  satisfactoriamente.";
     	}


     	if($_REQUEST['accRealizar'] == 'Aprobar y Finalizar'){
 
     		$entidadDocg->SetIdEstatus(13);
     		$entidadDocg->SetIdWFObjeto(99);
     		$entidadDocg->SetIdPerfilActual('');
     		$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
     		 
     		$val === false?
     		$GLOBALS['SafiErrors']['general'][] = "Error al aprobar el punto de cuenta (<a href='#dialog' docgId='".$_REQUEST['pcta']."'class='detalleOpcion' opcion='null'>".$_REQUEST['pcta']." </a>)" :
     		$GLOBALS['SafiInfo']['general'][] = "Punto de cuenta (<a href='#dialog' docgId='".$_REQUEST['pcta']."'class='detalleOpcion' opcion='null'>".$_REQUEST['pcta']." </a>) aprobado  satisfactoriamente.";
     		$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
     		SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);

     		$params['idPcta'] = $_REQUEST['pcta'];
     		$params['estaid'] = 13;
     		SafiModeloPuntoCuenta::UpdatePcuentaEstaId($params);
     		
     	}


      $this->Bandeja(true);

	}
	 
	 
	public function DetallePctaPdf(){


		$key = trim(utf8_decode($_REQUEST["key"]));

		$objPcta =  SafiModeloPuntoCuenta::GetPuntoCuenta(array("idPuntoCuenta" => $key));
		
		$pctaAsociado = $objPcta->GetPuntoCuentaAsociado()  != null? $objPcta->GetPuntoCuentaAsociado()->GetId() : '';
		
		if($pctaAsociado){
		
				$objPcta2 =  SafiModeloPuntoCuenta::GetPuntoCuenta(array("idPuntoCuenta" => $pctaAsociado));
				
					$objPctaasociado =  $asunto = $objPcta2->GetAsunto() != null?$objPcta2->GetAsunto()->GetNombre(): '';
								
				/*	echo "<pre>";
					echo print_r($objPctaasociado);
					echo "</pre>";
					*/
				
		         $GLOBALS['SafiRequestVars']['AsuntoPctaAsociado'] = $objPctaasociado;
		}
		
	

		$ci = $objPcta->GetPresentadoPor() != null? $objPcta->GetPresentadoPor()->GetId(): '';


		$GLOBALS['SafiRequestVars']['empleado'] =  SafiModeloEmpleado::GetEmpleadoActivoByCedula(/*$ci*/ 13649530);
		
		
		
		$ciUsuario = $objPcta->GetUsuario() != null? $objPcta->GetUsuario()->GetId() : null;
		
		$entidadEmpledo = $ciUsuario != null? SafiModeloEmpleado::GetEmpleadoByCedula($ciUsuario) : null;

        $entidadEmpledo != null? $objPcta->GetUsuario()->SetId($entidadEmpledo->GetNombres().' '.$entidadEmpledo->GetApellidos()): '';
		

		$dependenciaId = $objPcta->GetDependencia() != null?$objPcta->GetDependencia()->GetId(): '';

		$concatenar= $GLOBALS['SafiRequestVars']['empleado']['carg_fundacion'].$dependenciaId;

		$firmasSeleccionadas = SafiModeloFirma::GetFirmaByPerfiles(array($concatenar,PERFIL_DIRECTOR_PRESUPUESTO,PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS,PERFIL_DIRECTOR_EJECUTIVO,PERFIL_PRESIDENTE));
		 
		$GLOBALS['SafiRequestVars']['concatenar'] = $concatenar ;

		$GLOBALS['SafiRequestVars']['firmasSeleccionadas'] = $firmasSeleccionadas ;
		 

		if(strpos($objPcta->GetDestinatario(),'/') == false){
				
			$destinatario = SafiModeloCargo::GetCargoByEmpleado($objPcta->GetDestinatario());
				
			$destinatario = $destinatario->GetNombre();

		}else{

			$lista = explode('/',$objPcta->GetDestinatario());

			$destinatario = SafiModeloCargo::GetCargoByEmpleado($lista[0]);
				
			$destinatario1 = $destinatario->GetNombre();
				
			$destinatario = SafiModeloCargo::GetCargoByEmpleado($lista[1]);
				
			$destinatario2 = $destinatario->GetNombre();

			$destinatario =  $destinatario1."/".$destinatario2;

			 
		}

			
			
		$objPcta->SetDestinatario($destinatario);
		 
		$val =  $objPcta->GetRifProveedorSugerido();

		$val2 = explode (':',$val);
		 
		if($val2[1]){
			 
			$objPcta->SetRifProveedorSugerido($val2[1]);

		}else{
			 
			$objPcta->SetRifProveedorSugerido($val);

		}

		 
		$GLOBALS['SafiRequestVars']['puntosCuenta'] = $objPcta;

		   ;

	
		include(SAFI_VISTA_PATH ."/pcta/pcta_PDF.php");

	}
	 
	public function Modificar(){


		$key = trim(utf8_decode($_REQUEST["pcta"]));
		 
		$puntoCuenta =  SafiModeloPuntoCuenta::GetPuntoCuenta(array("idPuntoCuenta" => $key));

		$GLOBALS['SafiRequestVars']['puntosCuenta'] = $puntoCuenta;

		$objPcta = $GLOBALS['SafiRequestVars']['puntosCuenta'];
		
		$this->Ingresar($objPcta);

	}
	 
	public function RegistrarModificar(){

		$params = array();
		 
		$params['idPcta'] = trim($_REQUEST['idPcta']);
		$params['pcta_id'] = trim($_REQUEST['idPcta']);
		$params['fecha'] = trim($_REQUEST['fecha']);
		$params['preparado_para']=trim($_REQUEST['preparado_para']);
		$params['pcuenta_remit']=trim($_REQUEST['pcuenta_remit']);
		$params['SolicitadoPor'] = $_REQUEST['SolicitadoPor'];
		$params['presentado_por'] = $_REQUEST['presentado_por'];
		$params['DependenciaTramita']=$_REQUEST['DependenciaTramita'];
		$params['DependenciaSolicitante'] = $_REQUEST['DependenciaSolicitante'];
		$params['pctaAsunto'] = $_REQUEST['pctaAsunto'];
		$params['pctaAsociado'] = $_REQUEST['pctaAsociado'] != null? $_REQUEST['pctaAsociado']: '';
		$params['pcuenta_descripcion'] = $_REQUEST['pcuenta_descripcionVal'];
		$params['justificacion'] = $_REQUEST['justificacion'];
		$params['convenio'] = $_REQUEST['convenio'];
		$params['garantia'] = $_REQUEST['garantia'];
		$params['ProveedorSugeridoval'] = $_REQUEST['ProveedorSugeridoval'] == null ? '~'.$_REQUEST['ProveedorSugerido']: $_REQUEST['ProveedorSugeridoval'];
		$params['observaciones'] = $_REQUEST['observaciones'];
		$params['recursos'] = $_REQUEST['op_recursos'];
		$params['regisFisDigiEli'] = $_REQUEST['regisFisDigiEli'];

		// condiciones de pago
		 
		$params['cond_pago'] = $_REQUEST['cond_pago'];
		$params['montoTotal'] = $_REQUEST['montoTotalHidden'];
		$params['partida_pcta'] = $_REQUEST['pcta'];
		 
		// respaldos fisicos

		$params['Fisico'] = $_REQUEST['RegistroFisico'];


		// respaldos dig
		 
		if(isset($_SESSION['SafiRequestVars']['nameFile'])){

			$i = 0;
	 	foreach ( $_SESSION['SafiRequestVars']['nameFile'] as $index => $valor){

	 		$targetFolder = SAFI_UPLOADS_PATH.'/pcta/'.$valor;
	 		$tempFile =  SAFI_TMP_PATH.'/'. $valor;
	 		copy($tempFile,$targetFolder);
	 		$params['Digital'][] = $valor;

	 	}


	 }

	 unset($_SESSION['SafiRequestVars']['nameFile']);

	 $param =array();

	 $param['lugar'] = "pcta";
	 $param['ano'] = substr ($_SESSION['an_o_presupuesto'],2);
	 $param['Dependencia'] = $params['DependenciaTramita'];

	 $updatePcuenta = SafiModeloPuntoCuenta::UpdatePcuenta($params);

	 if(!UpdatePcuenta === false){


	 	$GLOBALS['SafiInfo']['general'][] = "Punto de cuenta  (<a href='#dialog' docgId='".$params['idPcta']."'class='detalleOpcion' opcion='null'>".$params['idPcta']." </a>) modificado  satisfactoriamente.";

	 	if($_REQUEST['regisNombreDigital']){

	 		$registro = explode(',',$_REQUEST['regisNombreDigital']);
	 		 
	 		foreach ($registro as $valor){

	 			unlink(SAFI_UPLOADS_PATH.'/pcta/'.$valor);

	 		}

	 	}

	 }else{

	 	$GLOBALS['SafiErrors']['general'][] = "Error al modificar punto de cuenta (".$params['idPcta'].").";

	 }

		$this->Bandeja();

	}


	public function Buscar(){
		$id_depe = substr($_SESSION['user_perfil_id'],2,3);

		$PctaAsusnto= SafiModeloPuntoCuentaAsunto::GetPctaAsusnto();
		$GLOBALS['SafiRequestVars']['PctaAsusnto'] = $PctaAsusnto;

		$EstadoPcta= SafiModeloEstatus::GetEstadoPcta();
		$GLOBALS['SafiRequestVars']['EstadoPcta'] = $EstadoPcta;
		
		$DependenciaPcta = SafiModeloDependencia::GetDependenciasByNivel(4);
		
		$GLOBALS['SafiRequestVars']['DependenciaPcta'] = $DependenciaPcta;

		include(SAFI_VISTA_PATH ."/pcta/puntoCuentaBuscar.php");

	}



	public function  SearchPcta(){

		
		$instActual =array();
		$params =array();
		$codigPctaBusqueda = trim(utf8_decode($_REQUEST["codigPctaBusqueda"]));
		$dependencia = trim($_REQUEST['dependencia']);
		
		$puntosCuentaFiltro =  SafiModeloPuntoCuenta::GetPuntosCuenta($_REQUEST,true);

		$GLOBALS['SafiRequestVars']['puntoCuentaFiltro'] = $puntosCuentaFiltro;

		$arrPcuanta = array();
		$estaDescrip = array();
        $pcuantaAprobando = array();

		if($puntosCuentaFiltro){
			foreach ($puntosCuentaFiltro as $index => $valor){
				$arrPcuanta[$index] = $index;
				
	
				
			 if($valor->GetEstatus() != null && $valor->GetEstatus()->GetId() == 13 && $valor->GetAsunto()->Getid() != '013'){
			 	
		    	  $pcuantaAprobando[$index] = $index;
		    	 
		    }
				
			}

			if($pcuantaAprobando){
					
			$pcuantaDevolver = SafiModeloPuntoCuenta::GetCompromisoIdPcuenta($pcuantaAprobando);

			$GLOBALS['SafiRequestVars']['pcuantaDevolver'] = $pcuantaDevolver;

			}


			$arrPcuanta != null? $entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdsDocuments($arrPcuanta):'';


			if($entidadDocg['arrIdActual']){
				$idAct = SafiModeloWFGrupo::GetWFGrupoByIdsPerfil($entidadDocg['arrIdActual']);
			}
			 
			
			foreach ($puntosCuentaFiltro as $index => $valor){


				if(($entidadDocg['docGenera'][$index] != null) &&
				($entidadDocg['docGenera'][$index]->GetIdPerfilActual() != '') &&
				($idAct[$entidadDocg['docGenera'][$index]->GetIdPerfilActual()] != null)){
							
					$estaDescrip = $idAct[$entidadDocg['docGenera'][$index]->GetIdPerfilActual()]->GetDescripcion();
					$estadia[$index]= $estaDescrip != null? $estaDescrip : '';

				}
				 
			}
		}
		
		


		$GLOBALS['SafiRequestVars']['instActual'] = $estadia;
		


		$id_depe = substr($_SESSION['user_perfil_id'],2,3);
		$PctaAsusnto= SafiModeloPuntoCuentaAsunto::GetPctaAsusnto();
		$GLOBALS['SafiRequestVars']['PctaAsusnto'] = $PctaAsusnto;

		$EstadoPcta= SafiModeloEstatus::GetEstadoPcta();
		$GLOBALS['SafiRequestVars']['EstadoPcta'] = $EstadoPcta;

		$params['txt_inicio'] = $_REQUEST["txt_inicio"];
		$params['hid_hasta_itin'] = $_REQUEST["hid_hasta_itin"];
		$params['codigPctaBusqueda'] = $_REQUEST["codigPctaBusqueda"];

		$params['agnoPcta'] = $_REQUEST["agnoPcta"];
		$params['ncompromiso'] = $_REQUEST["ncompromiso"];
		$params['pctaAsunto'] = $_REQUEST["pctaAsunto"];
		$params['PartidaBusqueda'] = $_REQUEST["PartidaBusqueda"];
		$params['pctaProyAcc'] = $_REQUEST["pctaProyAcc"];
		$params['pctaProyAccVal'] = $_REQUEST["pctaProyAccVal"];
		$params['palabraClave'] = $_REQUEST["palabraClave"];
		$params['DependenciaPcta'] = $_REQUEST["DependenciaPcta"];
		
		$params['estatusPcta'] = $_REQUEST["estatusPcta"];

		
		$GLOBALS['SafiRequestVars']['puntoCuentaFiltroData'] = $params;

	    $disponiblePcta =  SafiModeloPuntoCuenta::GetPctaDisponibilidadPcuanta($arrPcuanta);
	
		$GLOBALS['SafiRequestVars']['disponiblePcta'] = str_replace(".",",",$disponiblePcta);
		
		$DependenciaPcta = SafiModeloDependencia::GetDependenciasByNivel(4);
		
		$GLOBALS['SafiRequestVars']['DependenciaPcta'] = $DependenciaPcta;

		$pctaALiberar = SafiModeloPuntoCuenta::GetPuntosCuentaALiberar($arrPcuanta);

		$GLOBALS['SafiRequestVars']['pctaALiberar'] = $pctaALiberar;
	    include(SAFI_VISTA_PATH ."/pcta/puntoCuentaBuscar.php");
	    
	 
	}


	public function GetDisponibilidadPartida(){
		

		if( $_REQUEST['id_proy_accion'] != null ||
		$_REQUEST['tipo']           != null ||
		$_REQUEST['id_especifica']  != null ||
		$_REQUEST['partida']        != null ){


			$parmas =array();
			
			
            $parmas['pmod'] = $_REQUEST['pmod'];
			$parmas['pres_anno'] = $_SESSION['an_o_presupuesto'];
			$parmas['form_id_p_ac'] = $_REQUEST['id_proy_accion'];
			$parmas['form_tipo'] = $_REQUEST['tipo'];
			$parmas['form_id_aesp'] = $_REQUEST['id_especifica'];
			$parmas['part_id'] = $_REQUEST['partida'];
				
			$monto_disp =  $this->Disponibilidad($parmas);
			 
			$GLOBALS['SafiRequestVars']['monto_disp'] = $monto_disp;

		}else{

			$GLOBALS['SafiRequestVars']['monto_disp'] = null;

		}


	 include(SAFI_VISTA_PATH ."/json/disponibilidad.php");

	}
		

	public function Disponibilidad(array $params = null){
		

		$GetMontosProgramados = SafiModeloDisponibilidadPcuenta::GetMontosProgramados($params);
		
		
		
		$GetMontosRecibidos = SafiModeloDisponibilidadPcuenta::GetMontosRecibidos($params);
		$GetMontosCedidos = SafiModeloDisponibilidadPcuenta::GetMontosCedidos($params);
		$GetMontosApartado = SafiModeloDisponibilidadPcuenta::GetMontosDiferidos($params);
		
		
		
		//	$GetMontosComprometidos = SafiModeloDisponibilidadPcuenta::GetMontosComprometidos($params);
		$GetMontosComprometidosAislados = SafiModeloDisponibilidadPcuenta::GetMontosComprometidosAislados($params);
		
		
		
		//	$GetMontosCausados = SafiModeloDisponibilidadPcuenta::GetMontosCausados($params);

		$monto_disp = ((($GetMontosProgramados+$GetMontosRecibidos)-($GetMontosCedidos))-$GetMontosComprometidosAislados)-$GetMontosApartado;
		
		
	//eror_log($GetMontosComprometidosAislados);
		
		//error_log($monto_disp);
		
		
	//	error_log("GetMontosProgramados =  $GetMontosProgramados /GetMontosRecibidos = $GetMontosRecibidos /GetMontosCedidos = $GetMontosCedidos /GetMontosComprometidosAislados = $GetMontosComprometidosAislados /GetMontosApartado = $GetMontosApartado" );
		
		//compromiso =$GetMontosApartado -$GetMontosComprometidos;

		return $monto_disp;

	}


  public function reporteDisponibleIntegrado() {
 	
		include(SAFI_VISTA_PATH ."/pcta/reporteDisponibleIntegrado.php");
	 }
	 
	 
	 
	public function  SearchPcuantaLiberacion(){
			
	$key = trim($_REQUEST["key"]);
	
	    $data = array();
	 	$data["anoPresupuesto"] = trim($_REQUEST["agnoPcta"]);
	 	$data["pcta_id"] = $key;

	$GLOBALS['SafiRequestVars']['pctaDisponibilidad'] = SafiModeloPuntoCuenta::GetPctaDisponibilidad($data);
	

    $params = array();
    
    if($partidas = $GLOBALS['SafiRequestVars']['pctaDisponibilidad'][$key]['partidas']){
	 	foreach ($partidas as $index => $valor){
	 		
	 		$monto = $valor['montoApartado'] != false ? 
                               $valor['montoComprometido'] != false?
                                         ($valor['montoApartado'] - $valor['montoComprometido']):
                                                   $valor['montoApartado']:0;
		$params[$index] = 	$monto;
	 		
	 	   }
        
	 	}else{
	 		
	 	$params = false;
	 	
	 	}

	 $GLOBALS['SafiRequestVars']['PcuantaLiberacion'] = $params;
	  

	 include(SAFI_VISTA_PATH ."/json/pCuentaLiberacion.php");
	  


	}
	 
	 
	 public function reporteDisponibleIntegradoAccion(){
	 	

	 	$params = array();
	 	$params["anoPresupuesto"] = trim($_REQUEST["agnoPcta"]);
	 	$params["pcta_id"] = trim($_REQUEST["pcta_id"]);
		$params["proy_acc"] = trim($_REQUEST["pctaProyAccVal2"]);

		
	$GLOBALS['SafiRequestVars']['pctaDisponibilidad'] = SafiModeloPuntoCuenta::GetPctaDisponibilidad($params);


	 	include(SAFI_VISTA_PATH ."/pcta/reporteDisponibleIntegrado.php");
	 }	 
	
	 public function reporteDisponibleIntegradoAccionDetalle(){
	 	 
	 	$params = array();
	 	$params["partida"] = $_REQUEST["partida"];
	 	$params["pcta"] = $_REQUEST["pcta"];
	 	$params["ano"] = $_REQUEST["aopres"];
	 	$params["monto"] = $_REQUEST["monto"];
	 	
	 	$GLOBALS['SafiRequestVars']['partida'] = $params["partida"];
	 	$GLOBALS['SafiRequestVars']['pcta'] = $params["pcta"];
	 	$GLOBALS['SafiRequestVars']['monto'] = $params["monto"];
	 	
	 	

	 	if (strcmp($_REQUEST["tipo"],'Apartado')==0)
		 	$GLOBALS['SafiRequestVars']['pctaDisponibilidad'] = SafiModeloPuntoCuenta::GetDetalleApartadoPcta($params);
	 	elseif (strcmp($_REQUEST["tipo"],'Compromiso')==0)
		 	$GLOBALS['SafiRequestVars']['pctaDisponibilidad'] = SafiModeloPuntoCuenta::GetDetalleCompromisoPcta($params);
	 	elseif (strcmp($_REQUEST["tipo"],'Causado')==0)
	 	$GLOBALS['SafiRequestVars']['pctaDisponibilidad'] = SafiModeloPuntoCuenta::GetDetalleCausadoPcta($params);
	 	elseif (strcmp($_REQUEST["tipo"],'Pagado')==0)
	 	$GLOBALS['SafiRequestVars']['pctaDisponibilidad'] = SafiModeloPuntoCuenta::GetDetallePagadoPcta($params);
	 	
	 	
	 	
	 	
            $sumatoria = 0;
            $contador = 0;
            $detalleDisponibilidad = array(); 
            
            if($GLOBALS['SafiRequestVars']['pctaDisponibilidad']){

			foreach ($GLOBALS['SafiRequestVars']['pctaDisponibilidad'] as $pcta){
				
		    $sumatoria = $sumatoria + $pcta->GetMontoSolicitado();
		    
			$detalleDisponibilidad[$contador]['monto'] = number_format($pcta->GetMontoSolicitado(),2,',','.');
			$detalleDisponibilidad[$contador]['pcta'] = $pcta -> GetId();
			$detalleDisponibilidad[$contador]['partida'] = $GLOBALS['SafiRequestVars']['partida'];
			$detalleDisponibilidad[$contador]['montoTotal'] =  number_format($sumatoria,2,',','.');

			$contador++;

			  }
			  
            }
	 	
	 	 $GLOBALS['SafiRequestVars']['pctaDisponibilidad'] = $detalleDisponibilidad;
	 	 
	 	 include(SAFI_VISTA_PATH ."/json/pCuentaDetalleDisponibilidad.php");
	 	 
	 }
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 	 public function liberarPcuenta(){
	 	 	
	 	$datas = array();
	 	$datas["anoPresupuesto"] = trim($_REQUEST["agnoPcta"]);
	 	$datas["pcta_id"] = $_REQUEST['pcta'];

	$GLOBALS['SafiRequestVars']['pctaDisponibilidad'] = SafiModeloPuntoCuenta::GetPctaDisponibilidad($datas);
	
     


    $paramPartida = array();
    $paramPartida2 = array();
    
    if($partidas = $GLOBALS['SafiRequestVars']['pctaDisponibilidad'][$_REQUEST['pcta']]['partidas']){
	 	foreach ($partidas as $index => $valor){
	 		
	 		$monto = $valor['montoApartado'] != false ? 
                               $valor['montoComprometido'] != false?
                                         ($valor['montoApartado'] - $valor['montoComprometido']):
                                                   $valor['montoApartado']:0;
		$paramPartida[$index] = 	$monto;
	 		
	 	   }
        
	 	}else{
	 		
	 	$paramPartida = false;
	 	
	 	}
	 	

      $alcances =   SafiModeloPuntoCuenta::GetIdPctaAlcance($_REQUEST['pcta']);
      $alcances[] = $_REQUEST['pcta'];
      
      if($paramPartida){
      	
      	foreach ($paramPartida as $index => $valor){
      	
      	   $paramPartida2[] = $index;
      	
      	}
      }


    
      $pctaImputa =  SafiModeloPuntoCuentaImputa::GetImputasPartidaPcta($alcances,$paramPartida2);


		   $objPcta =  SafiModeloPuntoCuenta::GetPuntoCuenta(array("idPuntoCuenta" => $_REQUEST['pcta'] ));
		   
  
 

	 	 if($objPcta){

	 	 	
	 $destinatario = $objPcta  != null? $objPcta->GetDestinatario()  : '';
	 $solicitadoPor = $objPcta->GetRemitente() != null? $objPcta->GetRemitente()->GetId() : '';	 	
	 
    $asunto = '020';
    $proveedorSugerido = SafiModeloPuntoCuenta::GetProveedorSugerido($_REQUEST['pcta']);
    $descripcion = $objPcta != null? $objPcta->GetDescripcion() : '';
    $fechas = $objPcta  != null? $objPcta->GetFecha()  : '';
    $dependencia = $objPcta->GetDependencia() != null? $objPcta->GetDependencia()->GetId() : '';
    
    
    
    $estatus = $objPcta->GetEstatus() != null? $objPcta->GetEstatus()->GetId() : '';
    $usuario = $objPcta->GetUsuario() != null? $objPcta->GetUsuario()->GetId(): '';
    $observacion = $objPcta  != null? $objPcta->GetObservacion()  : '';
    $justificacion = $objPcta != null? $objPcta->GetJustificacion() : '';
    $lapso = $objPcta != null? $objPcta->GetLapso() : '';
    $condicionPago =  $objPcta != null? $objPcta->GetCondicionPago() : '';
    $montoSolicitado =  $objPcta != null? $objPcta->GetMontoSolicitado()  : 0;
    $presentadoPor = $objPcta->GetPresentadoPor() != null? $objPcta->GetPresentadoPor()->GetId() : '';
    $recursos = $objPcta != null? $objPcta->GetRecursos() : '';
 	$garantia = $objPcta != null? $objPcta->GetGarantia()  : '';
    $pctaAsociado = $_REQUEST['pcta'];
    
   
    $fecha = explode ('/',$fechas);
	$fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
    
 }
 
 $justificacion = $justificacion != ''? $_REQUEST['justificacion']." (".$justificacion.")" : $_REQUEST['justificacion'] ;
 
 			
        $dateTime = new DateTime();	
        $entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['pcta']);
     	    $entidadDocg->SetIdEstatus(13);
     		$entidadDocg->SetIdWFObjeto(99);
     		$entidadDocg->SetIdPerfilActual('');

	 	$params = array();
		$params['usuario']= $usuario;
		$params['preparado_para']= $destinatario;
		$params['estatus']= $estatus;
		$params['SolicitadoPor'] = $solicitadoPor;
		$params['presentado_por'] = $presentadoPor;
		$params['DependenciaTramita']=$dependencia;
		$params['DependenciaSolicitante'] = $dependencia;
		$params['pctaAsunto'] = $asunto;
		$params['pctaAsociado'] = $pctaAsociado;
		$params['pcuenta_descripcion'] = $descripcion;
		$params['justificacion'] = $justificacion;
		$params['convenio'] = $lapso;
		$params['garantia'] = $garantia;
		$params['ProveedorSugeridoval'] = $proveedorSugerido;
		$params['observaciones'] = $observacion;
		$params['recursos'] = $recursos;


		// condiciones de pago
		 
		$params['cond_pago'] = $condicionPago;
		$params['montoTotal'] = $montoSolicitado;
		
		$param['lugar'] = "pcta";
	    $param['ano'] = substr ($_SESSION['an_o_presupuesto'],2);
	    $param['Dependencia'] = substr($_SESSION['user_perfil_id'],2,3);
	   	
		$params['pcta_id'] = SafiModeloGeneral::GetNexId($param);
		
		$entidadDocg->SetIdEstatus(7);
     		
		
		$params['docg_wfob_id_ini']  = $entidadDocg->GetIdWFObjeto() ;
		$params['CadenaIdcadena']  = $entidadDocg->GetIdWFCadena();
		$params['IdPerfil']  = $entidadDocg->GetIdPerfil();
		$params['docg_esta_id']  = 13;
		$params['PerfilSiguiente']  = '';
		
		
		
		if($pctaImputa){
         foreach ($pctaImputa as $index => $valor){

         	$partida_pcta['tipo'][$index] = $valor['pcta_tipo_impu'];
         	$partida_pcta['codPartida'][$index] = $valor['pcta_sub_espe'];
         	$partida_pcta['monto'][$index] = (($paramPartida[$valor['pcta_sub_espe']])*(-1));
          	$partida_pcta['codProyAcc'][$index] = $valor['pcta_acc_pp'];
          	$partida_pcta['codProyAccEsp'][$index] = $valor['pcta_acc_esp'];

         }
   			



         if($partida_pcta){
         
         
            $params2 = array();
         	$params2['partida_pcta'] = $partida_pcta;
         	$params2['pcta_id'] = $params['pcta_id'];
         	$params2['DependenciaTramita'] = $dependencia;
            $params['partida_pcta'] = $params2['partida_pcta'];	
        

         $insertPcuenta = SafiModeloPuntoCuenta::InsertPcuenta($params);
		 SafiModeloPuntoCuentaImputa::InsertPctaImputaTraza($params2);

         }

     			}
     
     
      
	  if(!$insertPcuenta === false){


	 	$GLOBALS['SafiInfo']['general'][] = "Punto de cuenta (<a href='#dialog' docgId='".$params['pcta_id']."'class='detalleOpcion' opcion='null'>".$params['pcta_id']." </a>) guardado  satisfactoriamente.";

	 }else{

	 	$GLOBALS['SafiErrors']['general'][] = "Error al insertar punto de cuenta";

	 }


	 	    $this->Buscar(); 
	 	 }
	 
	 
	 
	 




}
new Pcta();