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


class Materiales extends Acciones{
	
	public function  ProcesarEmat(){
	
		$params	= array(
				'revisiones_doc_id' => '',
				'revisiones_doc_documento_id' =>  $_REQUEST['emat'],
				'revisiones_doc_usua_login' =>   $_SESSION['login'],
				'revisiones_doc_perfil_id' =>  $_SESSION['user_perfil_id'],
				'revisiones_doc_fecha_revision' => '',
				'revisiones_doc_wfopcion_id' => $_REQUEST['idopcion'],
				'revisiones_doc_firma_revision' => ''
	
		);
		
		$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['emat']);
		

		

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
			$GLOBALS['SafiErrors']['general'][] = "Error al enviar documento de registro (".$_REQUEST['emat'].")" :
			$GLOBALS['SafiInfo']['general'][] = "Registro (".$_REQUEST['emat'].") enviado  satisfactoriamente.";
			
			
			
			?>
			<script>
			url = "../../../recursos/almacen/inventario/materialesBandeja.php";
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
			$GLOBALS['SafiErrors']['general'][] = "Error al enviar documento de registro (".$_REQUEST['emat'].")" :
			$GLOBALS['SafiInfo']['general'][] = "Registro (".$_REQUEST['ne'].") aprobado  satisfactoriamente.";
			
			?>
				<script>
				url = "../../../recursos/almacen/inventario/materialesBandeja.php";
				window.location = url;
				</script>
			<?php 
		
		}
	
		 
	
	
	
	
		if($_REQUEST['accRealizar'] == 'Anular'){
	
			$param = array();
	
	
			$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['emat']);
	
	
			$entidadDocg->SetIdEstatus(15);
			$entidadDocg->SetIdPerfilActual('');
	
			$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
			 
			$param['id'] = trim($_REQUEST['emat']);
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
			acta_id= trim('".$_REQUEST['emat']."')
  			";
			
			//error_log(print_r($query,true));
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) != false)
	
	
	
			$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
			SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
	
			$val === false?
			 
			$GLOBALS['SafiErrors']['general'][] = "Error al anular Registro (".$_REQUEST['emat'].")" :
			$GLOBALS['SafiInfo']['general'][] = "Registro (".$_REQUEST['emat'].") anulado  satisfactoriamente.";
			 
			 
			?>
			<script>
			url = "../../../recursos/almacen/inventario/materialesBandeja.php";
			window.location = url;
			</script>
			<?php 
	
		}
		
			
	
	
	if($_REQUEST['accRealizar'] == 'Devolver'){
			 
			 
			$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['emat']);
	
	
			$idCadenaActual = $entidadDocg->GetIdWFCadena();
	
			$entidadDocg->SetIdWFObjeto(1);
			$entidadDocg->SetIdWFCadena($_REQUEST['idCadenaSigiente']);
			$entidadDocg->SetIdPerfilActual($entidadDocg->GetIdPerfil());
			$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
	
			$param['id'] = trim($_REQUEST['emat']);
			$param['estaid'] = 7;
			$param['observacion'] = $_REQUEST['memo'];
			$param['perfil'] =$_SESSION['user_perfil_id'];
			$param['opcion'] = $_REQUEST['opcion'];
			 
			SafiModeloObservacionesDoc::InsertarObservacionesDoc($param);
	
			 
			$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
			SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
	
			 
			$val === false?
			$GLOBALS['SafiErrors']['general'][] = "Error al devolver registro (".$_REQUEST['emat'].")" :
			$GLOBALS['SafiInfo']['general'][] = "Registro (".$_REQUEST['emat'].") devuelto  satisfactoriamente.";
	
			?>
			<script>
			url = "../../../recursos/almacen/inventario/materialesBandeja.php";
			window.location = url;
			</script>
			<?php 
		}

	
		if($_REQUEST['accRealizar'] == 'Aprobar y Finalizar'){
			 
			$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['emat']);
	
			$entidadDocg->SetIdEstatus(13);
			$entidadDocg->SetIdWFObjeto(99);
			$entidadDocg->SetIdPerfilActual('');
			$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
	
			 
			$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
			 
		
			SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
	
			$param['id'] = $_REQUEST['emat'];
			$param['estaid'] = 13;
			 
			//SafiModeloMPresupuestarias::UpdatePmodEstaId($param);
			 
	
	
			$val === false?
			$GLOBALS['SafiErrors']['general'][] = "Error al aprobar Registro (".$_REQUEST['emat'].")" :
			$GLOBALS['SafiInfo']['general'][] = "Registro (".$_REQUEST['emat'].") aprobado  satisfactoriamente.";
	
			?>
			<script>
			url = "../../../recursos/almacen/inventario/materialesBandeja.php";
			window.location = url;
			</script>
			<?php 
		}
	
		//$this->Bandeja(true);

	}
	
	public function  SearchEmatDetalle(){
	
		$key = trim(utf8_decode($_REQUEST["key"]));
		 
		//error_log(print_r($key,true));

		//query de los detalles

		$querydetalle=
		"
		SELECT
			t1.acta_id,
			t1.proveedor,
			t4.prov_nombre,
			t2.ubicacion, 
			t2.arti_id,
			t3.nombre,
			t2.cantidad,
			t2.precio,
			t2.depe_solicitante,
			t5.depe_nombre,
			t2.alm_fecha_recepcion
		FROM
			sai_arti_inco t1
			inner join sai_arti_almacen t2 on(t1.acta_id =t2.acta_id)
			inner join sai_item t3 on(t3.id = t2.arti_id)
			inner join sai_proveedor_nuevo t4 on(t4.prov_id_rif = t1.proveedor)
			inner join sai_dependenci t5 on(t5.depe_id = t2.depe_solicitante)
		WHERE
			t1.acta_id= '".$key."'
		";
		
		//error_log(print_r($querydetalle,true));
		
		if(($result = $GLOBALS['SafiClassDb']->Query($querydetalle)) != false){
		
		$indice = 0;
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
		{
			
       
			$stringobj  ['acta'] = $key;
			$stringobj  ['proveedor'] = $row['proveedor'].", ".$row['prov_nombre'] ;
			if($row ['ubicacion']==1){
				$ubicacionarray = "Torre";
			}else{
				$ubicacionarray = "Galpón";
			}
			$stringobj  ['ubicacion'] = $ubicacionarray;
			
			
			
			$stringobj  ['idarticulo']  [$indice] = utf8_encode($row['nombre']);
			$stringobj  ['cantidad'] [$indice] = $row['cantidad'];
			$stringobj  ['precio'] [$indice] = $row['precio'];			
			$stringobj  ['depe_solicitante'] [$indice] = utf8_encode($row['depe_nombre']);
			$fec = $row['alm_fecha_recepcion'];
			$fecha = explode ('-',$fec);
			$fecha2 = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
			$stringobj  ['alm_fecha_recepcion'] [$indice]= $fecha2;
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
	
	$obsevacionesdoc = SafiModeloObservacionesDoc::GetObservacionesDoc(array($key => $key));	// metodo GetObservacionesDocrs soluciona los acentos
	
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
			$obsevacionesdoc[$num]['observacion'] = utf8_encode($obsevacionesdoc[$num]['observacion']);
			$num++;
		}
	
					
	}
	
	
	$stringobj['revicionesDoc']= $params;
	$stringobj['observacionesDoc']= $obsevacionesdoc;
	
	//error_log(print_r($stringobj,true));
	
	echo json_encode($stringobj);
	
	
	}

}
new Materiales();