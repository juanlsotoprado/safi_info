<?php

	if (isset($_REQUEST['PHPSESSID'])) { 

               $_COOKIE['PHPSESSID'] = $_REQUEST['PHPSESSID'];
         }
                   
include(dirname(__FILE__) . '/../../../init.php');
// Acciones
require_once(SAFI_ACCIONES_PATH. '/acciones.php');
// Includes
require_once(SAFI_INCLUDE_PATH. '/perfiles/constantesPerfiles.php');
require_once(SAFI_INCLUDE_PATH. '/conexion.php');

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

if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}


class DisminuirMat extends Acciones{
	
	public function  ProcesarAmat(){
	
		$params	= array(
				'revisiones_doc_id' => '',
				'revisiones_doc_documento_id' =>  $_REQUEST['amat'],
				'revisiones_doc_usua_login' =>   $_SESSION['login'],
				'revisiones_doc_perfil_id' =>  $_SESSION['user_perfil_id'],
				'revisiones_doc_fecha_revision' => '',
				'revisiones_doc_wfopcion_id' => $_REQUEST['idopcion'],
				'revisiones_doc_firma_revision' => ''
	
		);
		
		$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['amat']);
		

		

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
	
			$val === false?
			$GLOBALS['SafiErrors']['general'][] = "Error al enviar documento (".$_REQUEST['amat'].")" :
			$GLOBALS['SafiInfo']['general'][] = "(".$_REQUEST['amat'].") enviado  satisfactoriamente.";
			
			
			
			?>
			<script>
			url = "../../../recursos/almacen/inventario/disminuirMatBandeja.php";
			window.location = url;
			</script>
			<?php 
		}
		
		if($_REQUEST['accRealizar'] == 'Aprobar'){
		
		
			$entidadDocg->SetIdWFObjeto(0);
			$entidadDocg->SetIdWFCadena($_REQUEST['idCadenaSigiente']);
			$entidadDocg->SetIdPerfilActual($perfilSiguiente);
		
			$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
		
			$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
			SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
		
			$val === false?
			$GLOBALS['SafiErrors']['general'][] = "Error al enviar documento (".$_REQUEST['amat'].")" :
			$GLOBALS['SafiInfo']['general'][] = "(".$_REQUEST['ne'].") aprobado  satisfactoriamente.";
			
			?>
				<script>
				url = "../../../recursos/almacen/inventario/disminuirMatBandeja.php";
				window.location = url;
				</script>
			<?php 
		
		}
	
		 
	
	
	
	
		if($_REQUEST['accRealizar'] == 'Anular'){
	
			$param = array();
	
	
			$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['amat']);
	
	
			$entidadDocg->SetIdEstatus(15);
			$entidadDocg->SetIdPerfilActual('');
	
			$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
			 
			$param['id'] = trim($_REQUEST['amat']);
			$param['estaid'] = 15;
			$param['observacion'] = $_REQUEST['memo'];
			$param['perfil'] =$_SESSION['user_perfil_id'];
			$param['opcion'] = $_REQUEST['opcion'];
			
			SafiModeloObservacionesDoc::InsertarObservacionesDoc($param);
			
			$query=
			"
  			update
				sai_arti_inco
			set
				esta_id=15
			where
			acta_id= trim('".$_REQUEST['amat']."')
  			";
			
			//error_log(print_r($query,true));
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) != false)
	
	
	
			$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
			SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
	
			$val === false?
			 
			$GLOBALS['SafiErrors']['general'][] = "Error al anular (".$_REQUEST['amat'].")" :
			$GLOBALS['SafiInfo']['general'][] = "(".$_REQUEST['amat'].") anulado  satisfactoriamente.";
			 
			 
			?>
			<script>
			url = "../../../recursos/almacen/inventario/disminuirMatBandeja.php";
			window.location = url;
			</script>
			<?php 
	
		}
		
			
	
	
	if($_REQUEST['accRealizar'] == 'Devolver'){
			 
			 
			$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['amat']);
	
	
			$idCadenaActual = $entidadDocg->GetIdWFCadena();
	
			$entidadDocg->SetIdWFObjeto(1);
			$entidadDocg->SetIdWFCadena($_REQUEST['idCadenaSigiente']);
			$entidadDocg->SetIdPerfilActual($entidadDocg->GetIdPerfil());
			$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
	
			$param['id'] = trim($_REQUEST['amat']);
			$param['estaid'] = 7;
			$param['observacion'] = $_REQUEST['memo'];
			$param['perfil'] =$_SESSION['user_perfil_id'];
			$param['opcion'] = $_REQUEST['opcion'];
			 
			SafiModeloObservacionesDoc::InsertarObservacionesDoc($param);
	
			 
			$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
			SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
	
			 
			$val === false?
			$GLOBALS['SafiErrors']['general'][] = "Error al devolver (".$_REQUEST['amat'].")" :
			$GLOBALS['SafiInfo']['general'][] = "(".$_REQUEST['amat'].") devuelto  satisfactoriamente.";
	
			?>
			<script>
			url = "../../../recursos/almacen/inventario/disminuirMatBandeja.php";
			window.location = url;
			</script>
			<?php 
		}

	
		if($_REQUEST['accRealizar'] == 'Aprobar y Finalizar'){
			 
			$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['amat']);
	
			$entidadDocg->SetIdEstatus(13);
			$entidadDocg->SetIdWFObjeto(99);
			$entidadDocg->SetIdPerfilActual('');
			$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
	
			 
			$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
			 
		
			SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
	
			$param['id'] = $_REQUEST['amat'];
			$param['estaid'] = 13;
			 
			//SafiModeloMPresupuestarias::UpdatePmodEstaId($param);
			 
	
	
			$val === false?
			$GLOBALS['SafiErrors']['general'][] = "Error al aprobar (".$_REQUEST['amat'].")" :
			$GLOBALS['SafiInfo']['general'][] = "(".$_REQUEST['amat'].") aprobado  satisfactoriamente.";
	
			?>
			<script>
			url = "../../../recursos/almacen/inventario/disminuirMatBandeja.php";
			window.location = url;
			</script>
			<?php 
		}
	
		//$this->Bandeja(true);

	}
	
	public function  SearchAmatDetalle(){
	
		$key = trim(utf8_decode($_REQUEST["key"]));
		 
		//error_log(print_r($key,true));

		//query de los detalles

		$querydetalle=
		"
		select
			general.amat_id,
			general.fecha_acta,
			general.depe_entregada,
			depe.depe_nombre,
			general.observaciones,
			general.entregado_a,
			emple.empl_nombres,
			emple.empl_apellidos,
			esp.alm_id,
			esp.arti_id,
			nom.nombre,
			esp.cantidad
		from
			sai_arti_acta_almacen general
			inner join sai_arti_salida esp on(general.amat_id = esp.n_acta)
			inner join sai_dependenci depe on (depe.depe_id = general.depe_entregada)
			inner join sai_empleado emple on (emple.empl_cedula = general.entregado_a)
			inner join sai_item nom on (nom.id = esp.arti_id)
		where
			general.amat_id = '".$key."'
		";
		
		//error_log(print_r($querydetalle,true));
		
		if(($result = $GLOBALS['SafiClassDb']->Query($querydetalle)) != false){
		
		$indice = 0;
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
		{
			
       
			$stringobj  ['acta'] = $key;
			$stringobj  ['fecha_acta'] = $row['fecha_acta'];
			$stringobj  ['depe_entregada'] = utf8_encode($row['depe_nombre']);
			$stringobj  ['observaciones'] = utf8_encode($row['observaciones']);
			$stringobj  ['entregado_a'] = utf8_encode($row['empl_nombres'])." ".utf8_encode($row['empl_apellidos']);
			
			
			$stringobj  ['alm_id']  [$indice] = $row['alm_id'];
			$stringobj  ['arti_id'] [$indice] = $row['arti_id'];
			$stringobj  ['arti_nombre'] [$indice] = utf8_encode($row['nombre']);					
			$stringobj  ['cantidad'] [$indice] = $row['cantidad'];
			$indice ++;
			
		}
		
		
			
		
	}
	
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
	
	$obsevacionesdoc = SafiModeloObservacionesDoc::GetObservacionesDocrs(array($key => $key));	// metodo GetObservacionesDocrs soluciona los acentos
	
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
			//$obsevacionesdoc[$num]['observacion'] = utf8_encode($obsevacionesdoc[$num]['observacion']); //acentos para el jason
			$num++;
		}
	
					
	}
	

	$stringobj['revicionesDoc']= $params;
	$stringobj['observacionesDoc']=$obsevacionesdoc;
	
	//error_log(print_r($obsevacionesdoc[1]['observacion'],true));
	
	echo json_encode($stringobj);
	
	
	}

}
new DisminuirMat();
