<?php

require_once(dirname(__FILE__) . '/../../init.php');

// Acciones
require_once(SAFI_ACCIONES_PATH. '/acciones.php');

// Includes
require_once(SAFI_INCLUDE_PATH. '/perfiles/constantesPerfiles.php');

// Modelo
require_once(SAFI_MODELO_PATH. '/desincorporacionBien.php');

//Modelos

include(SAFI_MODELO_PATH. '/estado.php');
require_once(SAFI_MODELO_PATH. '/dependencia.php');
require_once(SAFI_MODELO_PATH. '/empleado.php');
require_once(SAFI_MODELO_PATH. '/general.php');
require_once(SAFI_MODELO_PATH. '/docgenera.php');
require_once(SAFI_MODELO_PATH. '/estatus.php');
require_once(SAFI_MODELO_PATH. '/observacionesDoc.php');
require_once(SAFI_MODELO_PATH. '/wfgrupo.php');
require_once(SAFI_MODELO_PATH. '/wfopcion.php');
require_once(SAFI_MODELO_PATH. '/wfcadena.php');
require_once(SAFI_MODELO_PATH. '/revisionesDoc.php');
require_once(SAFI_MODELO_PATH. '/cargo.php');

if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}



class DesincorporacionAccion extends Acciones
{
	public function  ProcesarDesi(){
	
		$params	= array(
				'revisiones_doc_id' => '',
				'revisiones_doc_documento_id' =>  $_REQUEST['desi'],
				'revisiones_doc_usua_login' =>   $_SESSION['login'],
				'revisiones_doc_perfil_id' =>  $_SESSION['user_perfil_id'],
				'revisiones_doc_fecha_revision' => '',
				'revisiones_doc_wfopcion_id' => $_REQUEST['idopcion'],
				'revisiones_doc_firma_revision' => ''
	
		);
	
		$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['desi']);
	
	
	
	
		if($entidadDocg->GetFecha()){
	
	
			$entidadDocg->SetFecha($entidadDocg->GetFecha());
				
		}
	
	
		//error_log(print_r($entidadDocg,true));
	
		$cadenaIdGrupo =  SafiModeloWFCadena::GetCadenaIdGrupo($_REQUEST['idCadenaSigiente']);
	
		$params['CadenaGrupo'] = $cadenaIdGrupo;
	
	
		$params['CadenaIdcadena'] = $_REQUEST['idCadenaSigiente'];
	
		$perfilSiguiente = SafiModeloWFCadena::GetPerfilSiguiente($params);
		$perfilgrupo = SafiModeloWFGrupo::GetWFPerfilbyGrupo($cadenaIdGrupo);
	
		if($_REQUEST['accRealizar'] == 'Enviar'){
	
	
			$entidadDocg->SetIdWFObjeto(0);
			$entidadDocg->SetIdWFCadena($_REQUEST['idCadenaSigiente']);
			$entidadDocg->SetIdPerfilActual($perfilgrupo);
	
			$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
	
			$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
				
				
			SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
			
	
			if(!$val === false){
		
			$GLOBALS['SafiInfo']['general'][] = "Acta enviada satisfactoriamente";
		
		
			}else{
		
			$GLOBALS['SafiErrors']['general'][] = "Error al Enviar acta";
		
			}
			$this->Bandeja();
				
			}
			
			if($_REQUEST['accRealizar'] == 'Aprobar'){
			
			
				$entidadDocg->SetIdWFObjeto(0);
				$entidadDocg->SetIdWFCadena($_REQUEST['idCadenaSigiente']);
				$entidadDocg->SetIdPerfilActual($perfilSiguiente);
			
				$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
			
				$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
				SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
			
				if(!$val === false){
			
				$GLOBALS['SafiInfo']['general'][] = "Acta aprobada satisfactoriamente";
			
			
				}else{
			
				$GLOBALS['SafiErrors']['general'][] = "Error al aprobar acta";
			
				}
				$this->Bandeja();
			
			}

			if($_REQUEST['accRealizar'] == 'Anular'){
		
				$param = array();
		
		
				$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['desi']);
		
		
				$entidadDocg->SetIdEstatus(15);
				$entidadDocg->SetIdPerfilActual('');
		
				$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
				 
				$param['id'] = trim($_REQUEST['desi']);
				$param['estaid'] = 15;
				$param['observacion'] = $_REQUEST['memo'];
				$param['perfil'] =$_SESSION['user_perfil_id'];
				$param['opcion'] = $_REQUEST['opcion'];
				
				SafiModeloObservacionesDoc::InsertarObservacionesDoc($param);
				
				$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
				SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
				
				if(!$val === false){
					
					$codigo = SafiModeloDesincorporacionBien::AnularActa($param['id']);
					$GLOBALS['SafiInfo']['general'][] = "Acta anulada satisfactoriamente";

				}else{
			
				$GLOBALS['SafiErrors']['general'][] = "Error al anular acta";
			
				}
				$this->Bandeja(); 
		
			}
		
			if($_REQUEST['accRealizar'] == 'Devolver'){
				 
				 
				$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['desi']);
		
		
				$idCadenaActual = $entidadDocg->GetIdWFCadena();
		
				$entidadDocg->SetIdWFObjeto(1);
				$entidadDocg->SetIdWFCadena($_REQUEST['idCadenaSigiente']);
				$entidadDocg->SetIdPerfilActual($entidadDocg->GetIdPerfil());
				$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
		
				$param['id'] = trim($_REQUEST['desi']);
				$param['estaid'] = 7;
				$param['observacion'] = $_REQUEST['memo'];
				$param['perfil'] =$_SESSION['user_perfil_id'];
				$param['opcion'] = $_REQUEST['opcion'];
				
				SafiModeloObservacionesDoc::InsertarObservacionesDocDesi($param);
				 
				$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
				SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
		
				if(!$val === false){
						
					$GLOBALS['SafiInfo']['general'][] = "Acta devuelta satisfactoriamente";
						
						
				}else{
						
					$GLOBALS['SafiErrors']['general'][] = "Error al devolver acta";
						
				}
				$this->Bandeja();
				
			}
			
			if($_REQUEST['accRealizar'] == 'Enviar Sudebip'){
					
					
				$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['desi']);
			
			
				$idCadenaActual = $entidadDocg->GetIdWFCadena();
			
				$entidadDocg->SetIdWFObjeto(2);
				$entidadDocg->SetIdWFCadena($_REQUEST['idCadenaSigiente']);
				$entidadDocg->SetIdPerfilActual($entidadDocg->GetIdPerfil());
				$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
			
				/*$param['id'] = trim($_REQUEST['desi']);
				$param['estaid'] = 7;
				$param['observacion'] = $_REQUEST['memo'];
				$param['perfil'] =$_SESSION['user_perfil_id'];
				$param['opcion'] = $_REQUEST['opcion'];
			
				SafiModeloObservacionesDoc::InsertarObservacionesDocDesi($param);
					
				$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
				SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);*/
			
				if(!$val === false){
						
					$GLOBALS['SafiInfo']['general'][] = "Acta enviada al sudebip satisfactoriamente";
						
						
				}else{
						
					$GLOBALS['SafiErrors']['general'][] = "Error al enviar al sudebip acta";
						
				}
				$this->Bandeja();
			}

		
			if($_REQUEST['accRealizar'] == 'Aprobar y Finalizar'){
				 
				$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['desi']);
		
				$entidadDocg->SetIdEstatus(13);
				$entidadDocg->SetIdWFObjeto(99);
				$entidadDocg->SetIdPerfilActual('');
				$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
		
				 
				$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
				 
			
				SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
		
				$param['id'] = $_REQUEST['desi'];
				$param['estaid'] = 13;
				 
				if(!$val === false){
						
					$GLOBALS['SafiInfo']['general'][] = "Acta aprobada/finalizada satisfactoriamente";
						
						
				}else{
						
					$GLOBALS['SafiErrors']['general'][] = "Error al aprobar/finalizar al sudebip acta";
						
				}
				$this->Bandeja();
			}
		
			//$this->Bandeja(true);
	
	}
	public function Bandeja()//$condicion = false
	{
		$params =array();
		$Idcadena =array();
		$lugar = 'desi';
		
		$_GLOBALS['SafiRequestVars']['pctaPorEnviar'] = SafiModeloDocGenera::GetRegistrosEnBandeja($lugar);
		
		$_GLOBALS['SafiRequestVars']['enviadoSudebip'] = SafiModeloDocGenera::GetRegistrosEnviadosSudebip($lugar);
		
		if($_GLOBALS['SafiRequestVars']['pctaPorEnviar']){
		
			foreach ($_GLOBALS['SafiRequestVars']['pctaPorEnviar'] as $index ){
		
				$Idcadena[$index['wfca_id']]= $index['wfca_id'];
		
			}
		}
		
		if($_GLOBALS['SafiRequestVars']['enviadoSudebip']){
		
			foreach ($_GLOBALS['SafiRequestVars']['enviadoSudebip'] as $index ){
		
				$Idcadena[$index['wfca_id']]= $index['wfca_id'];
		
			}
		}		
		
		$_GLOBALS['SafiRequestVars']['opciones'] = SafiModeloWFCadena::GetId_cadena_hijos_id_cadenas($Idcadena);
		
		
		$_GLOBALS['SafiRequestVars']['pctaEnTransito'] =  SafiModeloDocGenera::GetRegistrosEnTransitoDesincorporacion($lugar);
		
		
		if((!$_GLOBALS['SafiRequestVars']['pctaPorEnviar']) && (!$_GLOBALS['SafiRequestVars']['pctaEnTransito']) && ($param == null)){
		
			if(!$condicion)
			{
		
				$_GLOBALS['SafiInfo']['general'][] = "No se han encontrado registros";
		
			}
		
		}else{
		
			$_GLOBALS['SafiRequestVars']['pctaPorEnviar'] =  $this->FormatBandeja($_GLOBALS['SafiRequestVars']['pctaPorEnviar']);
		
			$_GLOBALS['SafiRequestVars']['pctaEnTransito'] =  $this->FormatBandejaTransito($_GLOBALS['SafiRequestVars']['pctaEnTransito']);
			
			$_GLOBALS['SafiRequestVars']['enviadoSudebip'] =  $this->FormatBandejaTransito($_GLOBALS['SafiRequestVars']['enviadoSudebip']);
		
			$_GLOBALS['SafiRequestVars']['pctaDevuelto'] = $_SESSION['SafiRequestVars']['pctaDevuelto'];
		
			unset($_SESSION['SafiRequestVars']['pctaDevuelto']);
		
		}
		require(SAFI_VISTA_PATH ."/bienes/desincorporacion/bandejaDesincorporacion.php");
	}

	public function Registrar()
	{
		$form = FormManager::GetForm(FORM_FORMULARIO_DESINCORPORACION);
		
		$desincorporacionBien = $form->GetDesincorporacionBien();
		
		$lugar = 'desi';
		$id_cadena_inicial = SafiModeloWFCadena::GetWfca_id_inicial($lugar);
		$GLOBALS['SafiRequestVars']['idCadenaActual'] = $id_cadena_inicial;
		
		$id_HijosCadena = SafiModeloWFCadena::GetId_cadena_hijo_id_cadena($id_cadena_inicial);
		$GLOBALS['SafiRequestVars']['opciones'] = $id_HijosCadena;
		
		
		$param['lugar'] = "desi";
		$param['ano'] = substr($_SESSION['an_o_presupuesto'],2);
		$niveles = array(3,4);
		$param['Dependencia']= '';
		
		
		require(SAFI_VISTA_PATH ."/bienes/desincorporacion/formularioDesincorporacion.php");
	}
	
	public function Modificar2()
	{
		$key = trim($_REQUEST["desi"]);
		$stringobj = SafiModeloDesincorporacionBien::BuscarDetalle($key);
		require(SAFI_VISTA_PATH ."/bienes/desincorporacion/formularioDesincorporacion.php");
	}
	
	public function ModificarSudebip()
	{	
		$stringobj = SafiModeloDesincorporacionBien::ModificarSudebip($_REQUEST["ArrayDesincorporacion"],$_REQUEST["acta_id"]);
		if(!$stringobj === false){
		
			$GLOBALS['SafiInfo']['general'][] = "Acta modificada satisfactoriamente";
		
		
		}else{
		
			$GLOBALS['SafiErrors']['general'][] = "Error al modificar acta";
		
		}
		$this->Bandeja();
	}
	public function ModificarActa()
	{
		$stringobj = SafiModeloDesincorporacionBien::ModificarActa($_REQUEST,$_REQUEST["acta_id"]);
		if(!$stringobj === false){
	
			$GLOBALS['SafiInfo']['general'][] = "Acta modificada satisfactoriamente";
	
	
		}else{
	
			$GLOBALS['SafiErrors']['general'][] = "Error al modificar acta";
	
		}
		$this->Bandeja();
	}
	public function Modificar()
	{
		$key = trim($_REQUEST["desi"]);
		$stringobj2 = SafiModeloDesincorporacionBien::BuscarDetalle($key);
		require(SAFI_VISTA_PATH ."/bienes/desincorporacion/formularioDesincorporacion.php");	
	}
	
	public function Buscar()
	{
		$key = trim($_REQUEST["desi"]);
		$txt_inicio = $_REQUEST["txt_inicio"]; 
		$hid_hasta_itin= $_REQUEST["hid_hasta_itin"];
		//echo $txt_inicio." ".$hid_hasta_itin;
		$actadesi = SafiModeloDesincorporacionBien::BuscarDesi($key, $txt_inicio, $hid_hasta_itin);
		require(SAFI_VISTA_PATH ."/bienes/desincorporacion/buscarDesincorporacion.php");
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
	
	public function  FormatBandejaTransito($valor){
	
	
		if($valor){
			$i = 0;
	
			foreach ($valor as $index){
	
	
	
				$fechahora = explode (' ',$index['docg_fecha']);
	
				$fecha = explode ('-',$fechahora[0]);
	
				$fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' '.$fechahora[1];
	
				$valor[$i]['docg_fecha'] = $fecha2;
	
				$id_grupo = SafiModeloWFCadena::GetCadenaIdGrupo($index['wfca_id']);
	
				$perf_id_act = SafiModeloWFGrupo::GetWFGrupoByIdPerfilResSocial($id_grupo);
	
				$valor[$i]['perf_id_act'] = $perf_id_act->GetDescripcion();
	
				$i++;
	
	
			}
	
		}
	
		return $valor;
	
	}
	
	
	public function BuscarListas()
	{
		header("Content-type: application/json; charset=UTF-8");
		
		$key = trim($_REQUEST["key"]);
		
		$arrActivos = SafiModeloDesincorporacionBien::BuscarActivo($key);
		
		foreach ($arrActivos AS &$arrActivo){
			$arrActivo['nombre'] = utf8_encode($arrActivo['nombre']);
			$arrActivo['serial'] = utf8_encode($arrActivo['serial']);
			$arrActivo['clave_bien'] = utf8_encode($arrActivo['clave_bien']);
			$arrActivo['acta_id'] = utf8_encode($arrActivo['clave_bien']);
		}
		unset($arrActivo);
		
		echo json_encode($arrActivos);
	}
	public function Guardar()
	{		
		$codigo = SafiModeloDesincorporacionBien::GuardarActa($_REQUEST);
		if(!$codigo === false){
			$GLOBALS['SafiInfo']['general'][] = "Acta ".$codigo." Guardada satisfactoriamente";	
		}
		else
		{
			$GLOBALS['SafiErrors']['general'][] = "Error al Guardar acta";
		}
		$this->Bandeja();
	}
	public function SearchDesiDetalle()
	{
		$key = trim(utf8_decode($_REQUEST["key"]));
		//error_log(print_r($key,true));
		
		$stringobj = SafiModeloDesincorporacionBien::BuscarDetalle($key);
		
		$revisionesDoc = SafiModeloRevisionesDoc::GetRevisionesDoc(array('idDocumento' => $key));
		if($revisionesDoc){
			$cedula =  array();
			$dependenciaUsuario =  array();
			$cargoUsuario =  array();
			$params =  array();
			$opcion = array();
		
			foreach ($revisionesDoc as $valor){
		
				$dependenciaUsuario[substr($valor->GetIdPerfil(), -3)] = substr($valor->GetIdPerfil(), -3);
				$cargoUsuario["'".substr($valor->GetIdPerfil(),0,2)."'"] = "'".substr($valor->GetIdPerfil(),0,2)."'";
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
		
		}
		
		$obsevacionesdoc = SafiModeloObservacionesDoc::GetObservacionesDocrs(array($key => $key));// getobservaciones modificado con array map utf_8
		
		//error_log(print_r($obsevacionesdoc, true));
		
		if($obsevacionesdoc){
		
			$dependenciaUsuario =  array();
			$cargoUsuario =  array();
			foreach ($obsevacionesdoc as $valor2){
		
		
				$dependenciaUsuario[substr($valor2['perfil'], -3)] = substr($valor2['perfil'], -3);
				$cargoUsuario[substr($valor2['perfil'],0,2)] = substr($valor2['perfil'],0,2);
		
			}
		
			$dependencias= SafiModeloDependencia::GetDependenciaByIds($dependenciaUsuario);
			$cargos =  SafiModeloCargo::GetCargoByCargoFundaciones($cargoUsuario);
		
			$num = 0;
			foreach ($obsevacionesdoc as $valor2){
				$obsevacionesdoc[$num]['perfilNombre'] = utf8_encode($cargos[substr($valor2['perfil'],0,2)]->GetDescripcion()."(".$dependencias[substr($valor2['perfil'], -3)]->GetNombre().")");
		
				$num++;
			}
		
				
		}
		
		
		$stringobj['revicionesDoc']= $params;
		$stringobj['observacionesDoc']= $obsevacionesdoc;
		
		//error_log(print_r($stringobj,true));
		
		echo json_encode($stringobj);
		
	}
}

new DesincorporacionAccion();

?>