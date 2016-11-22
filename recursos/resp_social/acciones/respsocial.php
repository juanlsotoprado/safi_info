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
	header('Location:../index.php',false);
	ob_end_flush();
	exit;
}

class Respsocial extends Acciones{
	
	public function  ProcesarErs(){
	
		$params	= array(
				'revisiones_doc_id' => '',
				'revisiones_doc_documento_id' =>  $_REQUEST['ers'],
				'revisiones_doc_usua_login' =>   $_SESSION['login'],
				'revisiones_doc_perfil_id' =>  $_SESSION['user_perfil_id'],
				'revisiones_doc_fecha_revision' => '',
				'revisiones_doc_wfopcion_id' => $_REQUEST['idopcion'],
				'revisiones_doc_firma_revision' => ''
	
		);
		
		$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['ers']);
		

		

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
			$GLOBALS['SafiErrors']['general'][] = "Error al enviar la entrada de responsabilidad social (".$_REQUEST['ers'].")" :
			$GLOBALS['SafiInfo']['general'][] = "entrada de responsabilidad social (".$_REQUEST['ers'].") enviada  satisfactoriamente.";
			
			
			
			?>
			<script>
			url = "../../../recursos/resp_social/respsocialBandeja.php";
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
			$GLOBALS['SafiErrors']['general'][] = "Error al enviar la entrada de responsabilidad social (".$_REQUEST['ers'].")" :
			$GLOBALS['SafiInfo']['general'][] = "entrada de responsabilidad social (".$_REQUEST['ers'].") aprobada  satisfactoriamente.";
			
			?>
				<script>
				url = "../../../recursos/resp_social/respsocialBandeja.php";
				window.location = url;
				</script>
			<?php 
		
		}
	
		 
	
	
	
	
		if($_REQUEST['accRealizar'] == 'Anular'){
	
			$param = array();
	
	
			$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['ers']);
	
	
			$entidadDocg->SetIdEstatus(15);
			$entidadDocg->SetIdPerfilActual('');
	
			$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
			 
			$param['id'] = trim($_REQUEST['ers']);
			$param['estaid'] = 15;
			$param['observacion'] = $_REQUEST['memo'];
			$param['perfil'] =$_SESSION['user_perfil_id'];
			$param['opcion'] = $_REQUEST['opcion'];
			
			SafiModeloObservacionesDoc::InsertarObservacionesDoc($param);
			
			$query=
			"
  			update
				sai_arti_inco_rs
			set
				esta_id=15
			where
			acta_id= trim('".$_REQUEST['ers']."')
  			";
			
			//error_log(print_r($query,true));
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) != false)
	
	
	
			$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
			SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
	
			$val === false?
			 
			$GLOBALS['SafiErrors']['general'][] = "Error al anular la entrada de responsabilidad social (".$_REQUEST['ers'].")" :
			$GLOBALS['SafiInfo']['general'][] = "entrada de responsabilidad social (".$_REQUEST['ers'].") anulada  satisfactoriamente.";
			 
			 
			?>
			<script>
			url = "../../../recursos/resp_social/respsocialBandeja.php";
			window.location = url;
			</script>
			<?php 
	
		}
		
			
	
	
		if($_REQUEST['accRealizar'] == 'Devolver'){
			 
			 
			$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['ers']);
	
	
			$idCadenaActual = $entidadDocg->GetIdWFCadena();
	
			$entidadDocg->SetIdWFObjeto(1);
			$entidadDocg->SetIdWFCadena($_REQUEST['idCadenaSigiente']);
			$entidadDocg->SetIdPerfilActual($entidadDocg->GetIdPerfil());
			$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
	
			$param['id'] = trim($_REQUEST['ers']);
			$param['estaid'] = 7;
			$param['observacion'] = $_REQUEST['memo'];
			$param['perfil'] =$_SESSION['user_perfil_id'];
			$param['opcion'] = $_REQUEST['opcion'];
			 
			SafiModeloObservacionesDoc::InsertarObservacionesDoc($param);
	
			//SafiModeloMPresupuestarias::UpdatePmodEstaId($param);
			 
			$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
			SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
	
			 
			$val === false?
			$GLOBALS['SafiErrors']['general'][] = "Error al devolver la entrada de responsabilidad social (".$_REQUEST['ers'].")" :
			$GLOBALS['SafiInfo']['general'][] = "entrada de responsabilidad social (".$_REQUEST['ers'].") devuelto  satisfactoriamente.";
	
			?>
			<script>
			url = "../../../recursos/resp_social/respsocialBandeja.php";
			window.location = url;
			</script>
			<?php 
		}

	
		if($_REQUEST['accRealizar'] == 'Aprobar y Finalizar'){
			 
			$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['ers']);
	
			$entidadDocg->SetIdEstatus(13);
			$entidadDocg->SetIdWFObjeto(99);
			$entidadDocg->SetIdPerfilActual('');
			$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
	
			 
			$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
			 
		
			SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
	
			$param['id'] = $_REQUEST['ers'];
			$param['estaid'] = 13;
			 
			//SafiModeloMPresupuestarias::UpdatePmodEstaId($param);
			 
	
	
			$val === false?
			$GLOBALS['SafiErrors']['general'][] = "Error al aprobar la entrada de responsabilidad social (".$_REQUEST['ers'].")" :
			$GLOBALS['SafiInfo']['general'][] = "entrada de responsabilidad social (".$_REQUEST['ers'].") aprobado  satisfactoriamente.";
	
			?>
			<script>
			url = "../../../recursos/resp_social/respsocialBandeja.php";
			window.location = url;
			</script>
			<?php 
		}
	
		//$this->Bandeja(true);

	}
	
	public function  SearchErsDetalle(){
	
		$key = trim(utf8_decode($_REQUEST["key"]));
		 
		//error_log(print_r($key,true));
		

//query de los detalles
//ejemplo de entrada en varios articulos: ers-3813

		$querydetalle=
		"
		SELECT
		distinct(arti_id) as articulos,
		to_char(t2.fecha_recepcion, 'DD/MM/YYYY') AS fecha_recepcion,
		t2.ubicacion,
		monto_recibido,
		t4.prov_nombre,
		t3.observaciones,
		nombre,
		t2.cantidad,
		t5.bmarc_nombre,
		modelo,
		serial
		FROM
		sai_arti_inco_rs t3,
		sai_arti_inco_rs_item t2,
		sai_item t1,
		sai_proveedor_nuevo t4,
		sai_bien_marca t5
		WHERE
		t3.acta_id='".$key."' and
		t3.acta_id=t2.acta_id and
		arti_id=t1.id and
  		t4.prov_id_rif=t3.proveedor and
  		t5.bmarc_id=t2.marca_id
		order by
		nombre
		";
		
		//error_log(print_r($querydetalle,true));
		
		if(($result = $GLOBALS['SafiClassDb']->Query($querydetalle)) != false){
		
		$indice = 0;
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
		{
       
			$stringobj  ['acta'] = $key;
			$stringobj  ['fecha_recepcion'] = $row['fecha_recepcion'];
			if($row ['ubicacion']==1){
				$ubicacionarray = "Torre";
			}else{
				$ubicacionarray = "Galpón";
			}
			$stringobj  ['ubicacion'] = $ubicacionarray;
			$stringobj  ['monto_recibido'] = $row['monto_recibido'];
			$stringobj  ['prov_nombre'] = utf8_encode($row['prov_nombre']);
			$stringobj  ['observaciones'] = utf8_encode($row['observaciones']);
			
			
			
			$stringobj  ['idarticulo']  [$indice] = $row['articulos'];
			$stringobj  ['nombre'] [$indice] = utf8_encode($row['nombre']);
			$stringobj  ['cantidad'] [$indice] = $row['cantidad'];
			$stringobj  ['marca_id'] [$indice] = utf8_encode($row['bmarc_nombre']);
			$stringobj  ['modelo'] [$indice] = utf8_encode($row['modelo']);
			$stringobj  ['serial'] [$indice] = utf8_encode($row['serial']);
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
new Respsocial();