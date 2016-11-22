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
	
	public function  ProcesarNe(){
	
		$params	= array(
				'revisiones_doc_id' => '',
				'revisiones_doc_documento_id' =>  $_REQUEST['ne'],
				'revisiones_doc_usua_login' =>   $_SESSION['login'],
				'revisiones_doc_perfil_id' =>  $_SESSION['user_perfil_id'],
				'revisiones_doc_fecha_revision' => '',
				'revisiones_doc_wfopcion_id' => $_REQUEST['idopcion'],
				'revisiones_doc_firma_revision' => ''
	
		);
		
		$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['ne']);
		

		

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
			$GLOBALS['SafiErrors']['general'][] = "Error al enviar la salida de responsabilidad social (".$_REQUEST['ne'].")" :
			$GLOBALS['SafiInfo']['general'][] = "Salida de responsabilidad social (".$_REQUEST['ne'].") enviada  satisfactoriamente.";
			
			
			
			?>
			<script>
			url = "../../../recursos/resp_social/rsbandejasalida.php";
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
			$GLOBALS['SafiErrors']['general'][] = "Error al enviar la salida de responsabilidad social (".$_REQUEST['ne'].")" :
			$GLOBALS['SafiInfo']['general'][] = "Salida de responsabilidad social (".$_REQUEST['ne'].") aprobada  satisfactoriamente.";
			
			?>
				<script>
				url = "../../../recursos/resp_social/rsbandejasalida.php";
				window.location = url;
				</script>
			<?php 
		
		}
	
		 
	
	
	
	
		if($_REQUEST['accRealizar'] == 'Anular'){
	
			$param = array();
	
	
			$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['ne']);
	
	
			$entidadDocg->SetIdEstatus(15);
			$entidadDocg->SetIdPerfilActual('');
	
			$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
			 
			$param['id'] = trim($_REQUEST['ne']);
			$param['estaid'] = 15;
			$param['observacion'] = $_REQUEST['memo'];
			$param['perfil'] =$_SESSION['user_perfil_id'];
			$param['opcion'] = $_REQUEST['opcion'];
			
			SafiModeloObservacionesDoc::InsertarObservacionesDoc($param);
			
			$query=
			"
  			update
				sai_arti_salida_rs
			set
				esta_id=15
			where
			acta_id= trim('".$_REQUEST['ne']."')
  			";
			
			//error_log(print_r($query,true));
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) != false)
	
	
	
			$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
			SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
	
			$val === false?
			 
			$GLOBALS['SafiErrors']['general'][] = "Error al anular la salida de responsabilidad social (".$_REQUEST['ne'].")" :
			$GLOBALS['SafiInfo']['general'][] = "Salida de responsabilidad social (".$_REQUEST['ne'].") anulada  satisfactoriamente.";
			 
			 
			?>
			<script>
			url = "../../../recursos/resp_social/rsbandejasalida.php";
			window.location = url;
			</script>
			<?php 
	
		}
		
			
	
	
		if($_REQUEST['accRealizar'] == 'Devolver'){
			 
			 
			$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['ne']);
	
	
			$idCadenaActual = $entidadDocg->GetIdWFCadena();
			$perfildelacadena = SafiModeloWFCadena::GetCadenaIdGrupo($_REQUEST['idCadenaSigiente']);
			$perfilporelgrupo= SafiModeloWFGrupo::GetWFPerfilbyGrupo($perfildelacadena);
	
			$entidadDocg->SetIdWFObjeto(1);
			$entidadDocg->SetIdWFCadena($_REQUEST['idCadenaSigiente']);
			//$entidadDocg->SetIdPerfilActual($entidadDocg->GetIdPerfil());
			$entidadDocg->SetIdPerfilActual($perfilporelgrupo);
			$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
			
	
			$param['id'] = trim($_REQUEST['ne']);
			$param['estaid'] = 7;
			$param['observacion'] = $_REQUEST['memo'];
			$param['perfil'] =$_SESSION['user_perfil_id'];
			$param['opcion'] = $_REQUEST['opcion'];
			 
			SafiModeloObservacionesDoc::InsertarObservacionesDoc($param);
	
			//SafiModeloMPresupuestarias::UpdatePmodEstaId($param);
			 
			$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
			SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
	
			 
			$val === false?
			$GLOBALS['SafiErrors']['general'][] = "Error al devolver la salida de responsabilidad social (".$_REQUEST['ne'].")" :
			$GLOBALS['SafiInfo']['general'][] = "Salida de responsabilidad social (".$_REQUEST['ne'].") devuelto  satisfactoriamente.";
	
			?>
			<script>
			url = "../../../recursos/resp_social/rsbandejasalida.php";
			window.location = url;
			</script>
			<?php 
		}
		
		
		if($_REQUEST['accRealizar'] == 'Fin'){

		$acta = trim($_REQUEST['ne']);
		$entregado_a = $_REQUEST['entregado'];
		$fecha = $_REQUEST['fecha'];
		
		$sql =
		"
		update
		sai_arti_salida_rs
		set
		datos_entregado = '".$entregado_a."',
		fecha_entrega = '".$fecha."'
		where
		acta_id = '".$acta."'
		";
			
		//error_log(print_r($sql,true));
		
		$result = $GLOBALS['SafiClassDb']->Query($sql);
			
		
		$entidadDocg = SafiModeloDocGenera::GetDocGeneraByIdDocument($_REQUEST['ne']);
		
		$entidadDocg->SetIdEstatus(13);
		$entidadDocg->SetIdWFObjeto(99);
		$entidadDocg->SetIdPerfilActual('');
		$val = SafiModeloDocGenera::ActualizarDocGenera($entidadDocg);
		
		

		
		$param['id'] = $_REQUEST['ne'];
		$param['estaid'] = 13;
		
		
		$val === false?
		$GLOBALS['SafiErrors']['general'][] = "Error al aprobar la salida de responsabilidad social (".$_REQUEST['ne'].")" :
		$GLOBALS['SafiInfo']['general'][] = "Salida de responsabilidad social (".$_REQUEST['ne'].") aprobado  satisfactoriamente.";
		
		?>
		<script>
		url = "../../../recursos/resp_social/rsbandejasalida.php";
		window.location = url;
		</script>
		<?php
		
		}
	
		if($_REQUEST['accRealizar'] == 'Aprobar y Finalizar'){
			$actaid = trim($_REQUEST['ne']);
			
			$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
			
			
			SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);
			?>
			<script>
			url = "../../../recursos/resp_social/asignacion_finalizar.php?ne=<?php echo $actaid; ?>";
			window.location = url;
			</script>
			<?php 
			
		}
	
		//$this->Bandeja(true);

	}
	
	
	
	public function  SearchNeDetalle(){
	
		$key = trim(utf8_decode($_REQUEST["key"]));
		 
		//error_log(print_r($key,true));
		
		/*
		 
	include(SAFI_VISTA_PATH ."/json/pmod.php");
	*/

	//query de los detalles

		$querydetalle=
		"
		SELECT
		distinct(arti_id) as articulos,
		to_char(t3.fecha_acta, 'DD/MM/YYYY') AS fecha_recepcion,
		t2.ubicacion,
		t3.destino,
		nombre,
		t2.cantidad,
		t5.bmarc_nombre,
		modelo,
		serial
		FROM
		sai_arti_salida_rs t3,
		sai_arti_salida_rs_item t2,
		sai_item t1,
		sai_proveedor_nuevo t4,
		sai_bien_marca t5
		WHERE
		t3.acta_id='".$key."' and
		t3.acta_id=t2.acta_id and
		arti_id=t1.id and
  		t5.bmarc_id=t2.marca_id
		order by
		nombre
		";
		
		
		
		if(($result = $GLOBALS['SafiClassDb']->Query($querydetalle)) != false){
		
		$indice = 0;
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
		{
			
       
			$stringobj  ['acta'] = $key;
			$stringobj  ['fecha_recepcion'] = $row['fecha_recepcion'];
			$stringobj  ['destino'] = utf8_encode($row['destino']);
			if($row ['ubicacion']==1){
				$ubicacionarray = "Torre";
			}else{
				$ubicacionarray = "Galpón";
			}
			$stringobj  ['ubicacion'] = $ubicacionarray;
			
			
			
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
	
	$obsevacionesdoc = SafiModeloObservacionesDoc::GetObservacionesDocrs(array($key => $key)); // getobservaciones modificado con array map utf_8

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