<?php

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


if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../index.php',false);
	ob_end_flush();
	exit;
}

class CompromisoAccion extends Acciones{

      
	public function Ingresar($compModificar = null){
	
			

		$niveles = array(3,4);

		$unidadDependencia = SafiModeloDependencia::GetDependenciasByNivels($niveles);
		$GLOBALS['SafiRequestVars']['unidadDependencia'] = $unidadDependencia;

		$estadosVenezuela= SafiModeloEstadosVenezuela::GetEstadosVenezuela(array(1));
		$GLOBALS['SafiRequestVars']['estadosVenezuela'] = $estadosVenezuela;
	
		$proyectos = SafiModeloProyecto::GetAllProyectosAprobados();
		$GLOBALS['SafiRequestVars']['proyectos'] = $proyectos;

		
		$acc =SafiModeloAccionCentralizada::GetAllAccionesCentralizadasAprobadas();
		$GLOBALS['SafiRequestVars']['acc'] = $acc;
		
		
		$controlInterno = SafiModeloControlinterno::GetcontrolInternos();
		$GLOBALS['SafiRequestVars']['controlInterno'] = $controlInterno;

		if($compModificar){

			$GLOBALS['SafiRequestVars']['compromiso'] =  $compModificar;
		}

	
		
		include(SAFI_VISTA_PATH ."/compromiso/compromiso.php");
		
	}

	public function Registrar(){

       
		
		$params = array();
		$params['fecha'] = trim($_REQUEST['fecha']);
		$params['unidadDependencia']=trim($_REQUEST['unidadDependencia']);
		$params['compAsociado'] = $_REQUEST['compAsociado'];
		$params['ProveedorSugeridoval'] = $_REQUEST['ProveedorSugeridoval'] == null ? '~'.$_REQUEST['ProveedorSugerido']: $_REQUEST['ProveedorSugeridoval'];
		$params['asuntoVal'] = $_REQUEST['asuntoVal'];
		$params['tipoActividadVal']= $_REQUEST['tipoActividadVal'];
		$params['txt_inicio'] = $_REQUEST['txt_inicio'];
		$params['hid_hasta_itin'] = $_REQUEST['hid_hasta_itin'];
		$params['tipoEventoVal'] = $_REQUEST['tipoEventoVal'];
		$params['CodigoDocumento'] = $_REQUEST['CodigoDocumento'];
		$params['compromiso_descripcionVal'] = $_REQUEST['compromiso_descripcionVal'];
		$params['localidad'] = $_REQUEST['localidad'];
		$params['infocentroVal'] = $_REQUEST['infocentroVal'];
		$params['numeroParticipantes'] = $_REQUEST['numeroParticipantes'];
		$params['observaciones'] = $_REQUEST['observaciones'];
		$params['montoTotal'] = $_REQUEST['montoTotalHidden'];
		$params['DependenciaTramita'] = substr($_SESSION['user_perfil_id'],2,3);
		$params['DependenciaTramita'] = substr($_SESSION['user_perfil_id'],2,3);
		$params['imputa'] = $_REQUEST['pcta'];
		$params['controlinterno'] = $_REQUEST['controlinterno'];
		

		if (($_POST['asuntoVal']=='001') || ($_POST['asuntoVal']=='002') || ($_POST['asuntoVal']=='023')){
			$params['esta']  ="Por Rendir";
		}else{
			$params['esta'] ="N/A";
		}

	 $param['lugar'] = "comp";
	 $param['ano'] = substr ($_SESSION['an_o_presupuesto'],2);
	 $param['Dependencia'] = $params['DependenciaTramita'];

	 $params['comp_id'] = SafiModeloGeneral::GetNexId($param);

	 $insert =  SafiModeloCompromiso::InsertCompromiso($params);


	 if(!$insert === false){
	 	
	 		 

	 	$GLOBALS['SafiInfo']['general'][] = "Compromiso (	<a href='#dialog' docgId='".$params['comp_id']."'class='detalleOpcion' opcion='null' tipoDetalle='compromiso' >".$params['comp_id']." </a>) guardado  satisfactoriamente.";

	 }else{

	 	$GLOBALS['SafiErrors']['general'][] = "Error al insertar Compromiso (".$params['comp_id'].")";

	 }
	  

	 $this->Ingresar();


	}


	public function SearchPcuentaAsociado(){

		$key = trim(utf8_decode($_REQUEST["key"]));
		$dependencia = trim(utf8_decode($_REQUEST["Dependencia"]));

		$params = array(
   	    'key' => $key,
   	    'Dependencia' => $dependencia 
		);


		$pctaAsociado = SafiModeloPuntoCuenta::GetPuntosCuentasAsociadosId($params);


		if($pctaAsociado){

			$GLOBALS['SafiRequestVars']['pctaAsociado'] = $pctaAsociado;

		}else{

			$GLOBALS['SafiRequestVars']['pctaAsociado'] = false;
		}
		include(SAFI_VISTA_PATH ."/json/compromisoPcuentaAsociado.php");

	}


	public function SearchCompromiso()
	{
		/* parche para que los codis funcionen con el año presupuestario actual */
		//$_SESSION['an_o_presupuesto'] = '2015';
		
		$key = trim($_REQUEST["key"]);
		$ff = trim($_REQUEST["tipoff"]);
		$yearPresupuestario = trim($_REQUEST["yearPresupuestario"]);
		
		if($yearPresupuestario == null || $yearPresupuestario == "")
			$yearPresupuestario = $_SESSION['an_o_presupuesto'];
		
		$GLOBALS['SafiRequestVars']['compFiltro'] = SafiModeloCompromiso::GetCompromisosFiltro($key, $ff, false, $yearPresupuestario);
		
		include(SAFI_VISTA_PATH ."/json/listaComp.php");
	}

	public function SearchCompromisoAsunto(){

		$key = trim(utf8_decode($_REQUEST["key"]));


			
		$asunto = SafiModeloCompromisoAsunto::GetAsuntosEstasIds(array(1),$key);


		if($asunto){

			$GLOBALS['SafiRequestVars']['asunto'] = $asunto;

		}else{

			$GLOBALS['SafiRequestVars']['asunto'] =  false;

		}
		include(SAFI_VISTA_PATH ."/json/compromisoAsunto.php");

	}


	public function SearchCompromisoTipoActividad(){

		$key = trim(utf8_decode($_REQUEST["key"]));
		$actividad = SafiModeloTipoActividadCompromiso::GetActividadEstasIds(array(1),$key);



		if($actividad){

			$GLOBALS['SafiRequestVars']['actividad'] = $actividad;

		}else{

			$GLOBALS['SafiRequestVars']['actividad'] =  false;

		}


		include(SAFI_VISTA_PATH ."/json/compromisoActividad.php");



	}



	public function SearchCompromisoTipoEvento(){

		$key = trim(utf8_decode($_REQUEST["key"]));

		$evento = SafiModeloTipoEvento::GetEventosEstasIds(null,$key);

		if($evento){

			$GLOBALS['SafiRequestVars']['evento'] = $evento;

		}else{

			$GLOBALS['SafiRequestVars']['evento'] =  false;

		}


		include(SAFI_VISTA_PATH ."/json/compromisoEvento.php");

	}



	public function SearchCompromisoInfocentros(){

		$key = trim(utf8_decode($_REQUEST["key"]));

		$infocentros = SafiModeloInfocentro::GetAllInfocentros2($key);

		if($infocentros){

			$GLOBALS['SafiRequestVars']['infocentros'] = $infocentros;

		}else{

			$GLOBALS['SafiRequestVars']['infocentros'] =  false;

		}

			

		include(SAFI_VISTA_PATH ."/json/infocentros.php");

	}



	public function GetDisponibilidadCompromisoPartida(){

		$params["anoPresupuesto"] = trim($_REQUEST["agnoPcta"]);
		$params["pcta_id"] = trim($_REQUEST["pcta"]);
		$params["proy_acc"] = trim($_REQUEST["pctaProyAccVal2"]);


		$GLOBALS['SafiRequestVars']['pctaDisponibilidad'] = SafiModeloPuntoCuenta::GetPctaDisponibilidad($params);


		if($GLOBALS['SafiRequestVars']['pctaDisponibilidad']){

			$GLOBALS['SafiRequestVars']['monto_disp'] = $GLOBALS['SafiRequestVars']['pctaDisponibilidad'][$params["pcta_id"]]['partidas'];

		}else{

			$GLOBALS['SafiRequestVars']['monto_disp'] = null;

		}


	 include(SAFI_VISTA_PATH ."/json/disponibilidad.php");

	}


	public function GetAnular(){


		$numSolPago = SafiModeloSolicitudPago::GetSolPagoIdComp($_REQUEST['comp']); //eje: 'comp-4005413'
		$numSaiCodi = SafiModeloCompromiso::GetSaiCodiIdComp($_REQUEST['comp']); //eje: 'comp-40036811'

		if($numSolPago > 0){

			$GLOBALS['SafiRequestVars']['Anular'] = 1;

				
		}else if($numSaiCodi > 0){
			 
			$GLOBALS['SafiRequestVars']['Anular'] = 2;
			 
		}else{

			$GLOBALS['SafiRequestVars']['Anular'] = false;

		}


	 include(SAFI_VISTA_PATH ."/json/compromisoAnulacion.php");

	}
	 



	public function ReporteActividad(){
		$form = FormManager::GetForm(FORM_REPORTE_ACTIVIDAD);

		$listaTipoActividades = SafiModeloTipoActividadCompromiso::GetAllTipoActividadCompromiso();
		$GLOBALS['SafiRequestVars']['listaTipoActividades'] = $listaTipoActividades;
		$listaEstados = SafiModeloEstado::GetAllEstados2();
		$GLOBALS['SafiRequestVars']['listaEstados'] = $listaEstados;
		$listaTipoEventos = SafiModeloTipoEvento::GetAllTipoEventos();
		$GLOBALS['SafiRequestVars']['listaTipoEventos'] = $listaTipoEventos;
		$listaCentroGestorCostos = SafiModeloCentroGestorCosto::GetAllCentroGestorCosto($_SESSION['an_o_presupuesto']);
		$GLOBALS['SafiRequestVars']['listaCentroGestorCostos'] = $listaCentroGestorCostos;

		$click = (isset($_REQUEST["click"]))?(boolean)trim($_REQUEST["click"]):false;
		if ( $click == true ) {
			$parametros = array();
			if(isset($_REQUEST['idTipoActividadCompromiso']) && trim($_REQUEST['idTipoActividadCompromiso']) != ''){
				$form->GetTipoActividad()->SetId(trim($_REQUEST['idTipoActividadCompromiso']));
				$parametros['idTipoActividadCompromiso'] = $form->GetTipoActividad()->GetId();
			}
			if(isset($_REQUEST['idTipoEvento']) && trim($_REQUEST['idTipoEvento']) != ''){
				$form->GetTipoEvento()->SetId(trim($_REQUEST['idTipoEvento']));
				$parametros['idTipoEvento'] = $form->GetTipoEvento()->GetId();
			}
			if(isset($_REQUEST['idEstado']) && trim($_REQUEST['idEstado']) != ''){
				$form->GetEstado()->SetId(trim($_REQUEST['idEstado']));
				$parametros['idEstado'] = $form->GetEstado()->GetId();
			}
			if(isset($_REQUEST['centroGestorCosto']) && trim($_REQUEST['centroGestorCosto']) != ''){
				$form->SetCentroGestorCosto(trim($_REQUEST['centroGestorCosto']));
				$arregloCentroGestorCosto = explode("/", $form->GetCentroGestorCosto());
				$parametros['centroGestor'] = $arregloCentroGestorCosto[0];
				$parametros['centroCosto'] = $arregloCentroGestorCosto[1];
			}
			if(isset($_REQUEST['fechaInicio']) && trim($_REQUEST['fechaInicio']) != ''){
				$form->SetFechaInicio(trim($_REQUEST['fechaInicio']));
				$parametros['fechaInicio'] = $form->GetFechaInicio();
			}
			if(isset($_REQUEST['fechaFin']) && trim($_REQUEST['fechaFin']) != ''){
				$form->SetFechaFin(trim($_REQUEST['fechaFin']));
				$parametros['fechaFin'] = $form->GetFechaFin();
			}
			$listaActividades = SafiModeloCompromiso::ReporteActividad($parametros);
			$GLOBALS['SafiRequestVars']['listaActividades'] = $listaActividades;
		}
		include(SAFI_VISTA_PATH ."/reportes/presupuesto/reporteActividad.php");
	}



	public function BuscarCompAccion(){
		
		

		$compFiltro = SafiModeloCompromiso::GetCompromisos($_REQUEST,true);

		$GLOBALS['SafiRequestVars']['compFiltro']= 	$compFiltro;
		
			
		$this->Buscar();
	}

	public function Buscar(){
$controlInterno = SafiModeloControlinterno::GetcontrolInternos();
		$GLOBALS['SafiRequestVars']['controlInterno'] = $controlInterno;

		include(SAFI_VISTA_PATH ."/compromiso/compromisoBuscar.php");


	}


	public function SearchCompromisoDetalle(){




		$key = trim(utf8_decode($_REQUEST["key"]));



		$Comp = SafiModeloCompromiso::GetCompromiso(array("idCompromiso" => $key));

		$data = SafiModeloObservacionesDoc::GetObservacionesDoc(array($key => $key));

		if($Comp){

			$GLOBALS['SafiRequestVars']['compromisoDetalle'] = $Comp;
			$GLOBALS['SafiRequestVars']['observacionesDoc'] = $data;

		}else{

			$GLOBALS['SafiRequestVars']['compromisoDetalle'] =  false;

		}


		include(SAFI_VISTA_PATH ."/json/compromisoDetalle.php");



	}

	public function Modificar(){
		
		

		if($_REQUEST['comp']){

			$compModificar = SafiModeloCompromiso::GetCompromiso(array("idCompromiso" => $_REQUEST['comp']));

			if($compModificar){
				$this->Ingresar($compModificar);
						
			}else{
					
				$this->Buscar();
			}

		}else{

			$this->Buscar();

		}

	}


	public function ModificarAccion(){

		$params = array();

		$params['comp'] = trim($_REQUEST['comp']);
		$params['fecha'] = trim($_REQUEST['fecha']);
		$params['unidadDependencia']=trim($_REQUEST['unidadDependencia']);
		$params['compAsociado'] = $_REQUEST['compAsociado'];
		$params['ProveedorSugeridoval'] = $_REQUEST['ProveedorSugeridoval'] == null ? '~'.$_REQUEST['ProveedorSugerido']: $_REQUEST['ProveedorSugeridoval'];
		$params['asuntoVal'] = $_REQUEST['asuntoVal'];
		$params['tipoActividadVal']=$_REQUEST['tipoActividadVal'];
		$params['txt_inicio'] = $_REQUEST['txt_inicio'];
		$params['hid_hasta_itin'] = $_REQUEST['hid_hasta_itin'];
		$params['tipoEventoVal'] = $_REQUEST['tipoEventoVal'];
		$params['CodigoDocumento'] = $_REQUEST['CodigoDocumento'];
		$params['compromiso_descripcionVal'] = $_REQUEST['compromiso_descripcionVal'];
		$params['localidad'] = $_REQUEST['localidad'];
		$params['infocentroVal'] = $_REQUEST['infocentroVal'];
		$params['numeroParticipantes'] = $_REQUEST['numeroParticipantes'];
		$params['observaciones'] = $_REQUEST['observaciones'];
		$params['montoTotal'] = $_REQUEST['montoTotalHidden'];
		$params['DependenciaTramita'] = substr($_SESSION['user_perfil_id'],2,3);
		$params['imputa'] = $_REQUEST['pcta'];
		$params['controlinterno'] = $_REQUEST['controlinterno'];
	
	    $params['esta'] = $_REQUEST['estatus'];
		$params['fechaReporte'] = $_REQUEST['fechaReporte'];



		if (($_POST['asuntoVal'] != '001') && ($_POST['asuntoVal'] != '002') && ($_POST['asuntoVal'] != '023')){
			
           $params['esta'] ="N/A";
			
		}



		$compModificar = SafiModeloCompromiso::UpdateComp($params);

		if(!$compModificar === false){

	 	$GLOBALS['SafiInfo']['general'][] = "Compromiso (	<a href='#dialog' docgId='".$params['comp']."'class='detalleOpcion' opcion='null' tipoDetalle='compromiso' >".$params['comp']." </a>) modificado  satisfactoriamente.";

	 }else{

	 	$GLOBALS['SafiErrors']['general'][] = "Error al modificar Compromiso";

	 }
	  

	 $this->Buscar();



	}


	public function Variacion(){


		include(SAFI_VISTA_PATH ."/compromiso/compromisoVariacion.php");

	}


	public function BuscarCompVariacionAccion(){

		$compVariacion = SafiModeloCompromiso::GetCompTrazaVariacion($_REQUEST);
		$GLOBALS['SafiRequestVars']['compVariacion'] = $compVariacion;

		include(SAFI_VISTA_PATH ."/compromiso/compromisoVariacion.php");


	}

	public function CausadoPagado(){


		include(SAFI_VISTA_PATH ."/compromiso/compromisoCausadoPagado.php");

	}

	public function CausadoPagadoAccion(){

		$causadoPagado = SafiModeloCompromiso::GetCompCausadoPagado($_REQUEST);

		$GLOBALS['SafiRequestVars']['causadoPagado'] = $causadoPagado;
		$GLOBALS['SafiRequestVars']['causadoPagado']['tipo'] = $_POST['tipo_reporte'];

		include(SAFI_VISTA_PATH ."/compromiso/compromisoCausadoPagado.php");

			

	}

	public function Anular(){

		$anular =  SafiModeloCompromiso::AnularComp($_REQUEST['comp'],$_REQUEST['memo']);



		if(!$anular === false){

	 	$GLOBALS['SafiInfo']['general'][] = "Compromiso (	<a href='#dialog' docgId='".$_REQUEST['comp']."'class='detalleOpcion' opcion='null' tipoDetalle='compromiso' >".$_REQUEST['comp']." </a>) anulado  satisfactoriamente.";

	 }else{

	 	$GLOBALS['SafiErrors']['general'][] = "Error al anulado Compromiso (<a href='#dialog' docgId='".$_REQUEST['comp']."'class='detalleOpcion' opcion='null' tipoDetalle='compromiso' >".$_REQUEST['comp']." </a>).";

	 }
	  

	 $this->Buscar();


	}
	
	
  public function 	ReintegroTotal(){

   	$anular =  SafiModeloCompromiso::ReintegroTotal($_REQUEST['comp'],$_REQUEST['memo']);

		if(!$anular === false){

	 	$GLOBALS['SafiInfo']['general'][] = "Se ha reintegrado totalmente el compromiso (<a href='#dialog' docgId='".$_REQUEST['comp']."'class='detalleOpcion' opcion='null' tipoDetalle='compromiso' >".$_REQUEST['comp']." </a>), de manera satisfactoriamente.";

	 }else{

	 	$GLOBALS['SafiErrors']['general'][] = "Error al reintegrar totalmente el  Compromiso (	<a href='#dialog' docgId='".$_REQUEST['comp']."'class='detalleOpcion' opcion='null' tipoDetalle='compromiso' >".$_REQUEST['comp']." </a>).";

	 }
	  

	 $this->Buscar();


	}
	
	
	

	public function DetalleCompPdf(){
		
		

		$comp = SafiModeloCompromiso::GetCompromiso(array("idCompromiso" => $_REQUEST['comp']));
		$data = SafiModeloObservacionesDoc::GetObservacionesDoc(array($_REQUEST['comp'] => $_REQUEST['comp']));
		
		if($comp){
		
       if($comp->GetCompromisoImputas()){
       	
       	
            $fuente = array();            
       	    foreach ($comp->GetCompromisoImputas() as $pctaImputa) {
	                
       	           if(!$pctaImputa->GetAccionCentralizada()){
	                
					$proyAccion =$pctaImputa->GetProyecto()->GetId();
					
					$proyAccionEspe =$pctaImputa->GetProyectoEspecifica()->GetId();
	                	
					
	                }else{
	                	
	                $proyAccion = $pctaImputa->GetAccionCentralizada()->GetId();
					$proyAccionEspe = $pctaImputa->GetAccionCentralizadaEspecifica()->GetId();

	                	           
	                }
	                
	             
	               
	              
	               
	                if(!$fuente[$proyAccion.'-'.$proyAccionEspe]){
	                	
	                 $dato =  SafiModeloGeneral::GetfuenteFinanciamiento($proyAccion,$proyAccionEspe);
	                 $fuente[$proyAccion.'-'.$proyAccionEspe] = $dato;
	                
	                }

              }   
             
		
        }
	        
            $GLOBALS['SafiRequestVars']['funteFinanciera'] =  $fuente;
			$GLOBALS['SafiRequestVars']['compromiso'] =  $comp;
			$GLOBALS['SafiRequestVars']['observacionesDoc'] =  $data;

			 include(SAFI_VISTA_PATH ."/compromiso/comp_PDF.php");
		}


	}
	
	public function ModificarTrazaReporte(){
		
	     $_REQUEST['datostabla'] =  SafiModeloCompromiso::GetTablaCampoTraza($_REQUEST);
		   
		//error_log(print_r( $_REQUEST['datostabla'],true));
		
	  //   $_REQUEST['datostabla']['fechaIni'] = '2015-01-30 08:00:00';
      
	      $cambiarTraza =  SafiModeloCompromiso::CambiarTraza2($_REQUEST);
       
		   
		   $ultima = SafiModeloCompromiso::GetUltimaTraza($_REQUEST);
		   
		 //  error_log(print_r($ultima,true));
		
	   	echo json_encode($cambiarTraza);

		
		
		
	}
	
public function ModificarTrazasPorLote(){
	
	
	//  direcccion:  http://tu-ip/safi1.0/acciones/comp/comp.php?accion=ModificarTrazasPorLote
	
   //  ej:  http://150.188.84.37/safi1.0/acciones/comp/comp.php?accion=ModificarTrazasPorLote
  
	

	$j = 1;
	
	// compromisos
	
	$comps =  array(    
	
	//aqui los comp ej: "comp-400115"

	

"comp-400126415",
"comp-400163115",
"comp-400163215",
"comp-400164115",
"comp-400164215",
"comp-400165315"






	
	);
	
	  foreach ($comps as $comp) {
	  	
	  	$_REQUEST['Comp'] = $comp;
	  	$_REQUEST['fechanueva'] =  2; // mes de la frecha ejemplo ( 1 enero , 2 febrero) etc
        $_REQUEST['memo'] = 'solicitud de presupuesto';
	  	

	     $_REQUEST['datostabla'] =  SafiModeloCompromiso::GetTablaCampoTraza($_REQUEST);

	     $_REQUEST['datostabla']['fechaIni'] = '2015-03-02 08:00:00';
      
	      $cambiarTraza =  SafiModeloCompromiso::CambiarTrazasPorGrupo($_REQUEST);
       
		   
		   $ultima = SafiModeloCompromiso::GetUltimaTraza($_REQUEST);
		   
		   
		   $mensaje .= $j .') se le ha cambiado la fecha de la ultima traza al Compromiso numero '. $comp.' por la fecha '.$ultima. ' <br/><br/>';
		   
		   $j++;
	  }
	  
	 echo  $mensaje;
		
	}
	
	public function GetUltimaTrazaReporte(){
		
			
		
	   $params = SafiModeloCompromiso::GetUltimaTraza($_REQUEST);
		
	   	echo json_encode($params);
		
	}
	



}



new CompromisoAccion();