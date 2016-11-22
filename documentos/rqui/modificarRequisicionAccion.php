<?php
ob_start();
session_start();
require("../../includes/conexion.php");
require('../../includes/arreglos_pg.php');
require("../../includes/fechas.php");
require("../../includes/constantes.php");
require("../../includes/funciones.php");
if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush();
	exit;
}
$pres_anno=$_SESSION['an_o_presupuesto'];
$usua_login=$_SESSION['login'];
$depe_id=$_SESSION['user_depe_id'];

$codigo = "";
if (isset($_POST['codigo']) && $_POST['codigo'] != "") {
	$codigo = $_POST['codigo'];
}
$idRequ = "";
if (isset($_POST['idRequ']) && $_POST['idRequ'] != "") {
	$idRequ = $_POST['idRequ'];
}
$tipoRequ = TIPO_REQUISICION_TODAS;
if (isset($_POST['tipoRequ']) && $_POST['tipoRequ'] != "") {
	$tipoRequ = $_POST['tipoRequ'];
}
$pagina = "1";
if (isset($_POST['pagina']) && $_POST['pagina'] != "") {
	$pagina = $_POST['pagina'];
}
$proyAcc = "";
if (isset($_GET['proyAcc']) && $_GET['proyAcc'] != "") {
	$proyAcc = $_GET['proyAcc'];
}
$radioProyAcc = "";
if (isset($_GET['radioProyAcc']) && $_GET['radioProyAcc'] != "") {
	$radioProyAcc = $_GET['radioProyAcc'];
}
$proyecto = "";
$accionCentralizada = "";
if($radioProyAcc=="proyecto"){
	if (isset($_GET['proyecto']) && $_GET['proyecto'] != "") {
		$proyecto = $_GET['proyecto'];
	}		
}else if($radioProyAcc=="accionCentralizada"){
	if (isset($_GET['accionCentralizada']) && $_GET['accionCentralizada'] != "") {
		$accionCentralizada = $_GET['accionCentralizada'];
	}		
}else{
	$proyAcc = "";
}
$dependencia = "";
if (isset($_GET['dependencia']) && $_GET['dependencia'] != "") {
	$dependencia = $_GET['dependencia'];
}
$estado = ESTADO_REQUISICION_DEVUELTAS;
if (isset($_POST['estado']) && $_POST['estado'] != "") {
	$estado = $_POST['estado'];
}
$controlFechas = "";
if (isset($_POST['controlFechas']) && $_POST['controlFechas'] != "") {
	$controlFechas = $_POST['controlFechas'];
}
$fechaInicio = "";
if (isset($_POST['fechaInicio']) && $_POST['fechaInicio'] != "") {
	$fechaInicio = $_POST['fechaInicio'];
}
$fechaFin = "";
if (isset($_POST['fechaFin']) && $_POST['fechaFin'] != "") {
	$fechaFin = $_POST['fechaFin'];
}
$bandeja = "";
if (isset($_POST['bandeja']) && $_POST['bandeja'] != "") {
	$bandeja = $_POST['bandeja'];
}
$accion = "";
if (isset($_POST['accion']) && $_POST['accion'] != "") {
	$accion = $_POST['accion'];
}

$user_perfil_id = $_SESSION['user_perfil_id'];
$user_login = $_SESSION['login'];
$modificarDocumento = 2;
$estadoTransito = 10;
$estadoBorrador = 60;
$estadoAnulado = 15;
$opcionAnular = 24;
$documentoRequisicion = "rqui";
if($accion==ACCION_ANULAR_REQUISICION){
	//Se modifica el estado del documento generado a anulado	
	$sql="UPDATE sai_doc_genera SET esta_id = ".$estadoAnulado." WHERE docg_id = '".$idRequ."'";
	$resultado = pg_exec($conexion ,$sql);

	//Se modifica el estado de la requisicion a anulado
	$sql="UPDATE sai_req_bi_ma_ser SET esta_id = ".$estadoAnulado." WHERE rebms_id = '".$idRequ."'";
	$resultado = pg_exec($conexion ,$sql);
	
	//Insertar la revision
	$sql = " SELECT * FROM sai_insert_revision_doc('$idRequ', '$user_login', '$user_perfil_id', '$opcionAnular', '') as resultado ";
	$resultado = pg_query($conexion,$sql);
	
	header("Location:detalleRequisicion.php?codigo=".$codigo."&idRequ=".$idRequ."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&accion=anular&proyAcc=".$proyAcc."&radioProyAcc=".$radioProyAcc."&proyecto=".$proyecto."&accionCentralizada=".$accionCentralizada."&dependencia=".$dependencia,false);	
}else{
	$error = "";
	$fecha=$_REQUEST['fecha'];
	$tipo=trim($_POST['typo']);
	if($tipo==""){
		$error = "0";//Debe indicar el tipo de requisición (Compra o Servicio).
	}
	if($error==""){
		$tp_imputacion=trim($_REQUEST['txt_id_tp_p_ac']);
		if($tp_imputacion==""){
			$error = "1";//Debe indicar el Proyecto o Acción Centralizada
		}
	}
	if($error==""){
		$proy_central=(string)trim($_REQUEST['txt_cod_imputa']);
		if($proy_central==""){
			$error = "1";//Debe indicar el Proyecto o Acción Centralizada
		}
	}
	if($error==""){
		$esp_proy_central=trim($_REQUEST['txt_cod_accion']);
		if($esp_proy_central==""){
			$error = "2";//Debe indicar la Acción Específica
		}
	}
	if($error==""){
		$largo=trim($_POST['hid_largo']);
		if($largo==""){
			$error = "3";//Error interno. Falta la cantidad de articulos agregados.
		}
	}
	if($error==""){
		$gerenciaAdscripcion=trim($_POST['gerenciaAdscripcion']);
		if($gerenciaAdscripcion==""){
			$error = "8";//Debe indicar la gerencia de adscripcion.
		}
	}
	if($error==""){
		$justificacion=trim($_POST['justificacion']);
		if($justificacion==""){
			$error = "7";//Debe indicar la justificacion de la requisicion.
		}
	}
	if($error==""){
		$pctaJustificacion=trim($_POST['pctaJustificacion']);
		if($pctaJustificacion==""){
			$error = "6";//Debe indicar la justificacion del punto de cuenta.
		}
	}
	for($i=0; ($i<$largo && $error==""); $i++){
		if(is_numeric($_REQUEST['txt_cantidad'.$i])){
			$matriz_cantidad[$i]=trim($_REQUEST['txt_cantidad'.$i]);
			if(strlen(trim($_REQUEST['txt_prod'.$i]))>0){
				$matriz_descripcion[$i]=validarTexto(trim($_REQUEST['txt_prod'.$i]));
			}else{
				$matriz_descripcion[$i]="no";
			}
			$matriz_id_articulo[$i]=trim($_REQUEST['txt_id_art'.$i]);
		}else{
			$error = "4";//Las cantidades indicadas deben ser valores numericos
		}
	}
	if($error=="" && (sizeof($matriz_cantidad)<1 || sizeof($matriz_descripcion)<1 || sizeof($matriz_id_articulo)<1)){
		$error = "5";//Debe indicar al menos un (1) articulo, bien o servicio para la solicitud de requisicion
	}
	if($error==""){
		$arreglo_cantidad=convierte_arreglo($matriz_cantidad);
		$arreglo_descripcion=convierte_arreglo($matriz_descripcion);
		$arreglo_id_articulo=convierte_arreglo($matriz_id_articulo);
		
		$descripcionGeneral=trim($_REQUEST['descripcionGeneral']);//desctipcion general
	
		$prov_sugerencia1=trim($_REQUEST['prov_sug1']);//proveedor sugerido
		$prov_sugerencia2=trim($_REQUEST['prov_sug2']);//proveedor sugerido
		$prov_sugerencia3=trim($_REQUEST['prov_sug3']);//proveedor sugerido
		$prov_sugerencia1_otro=trim($_REQUEST['prov_sug1_otro']);//proveedor sugerido otro
		$prov_sugerencia2_otro=trim($_REQUEST['prov_sug2_otro']);//proveedor sugerido otro
		$prov_sugerencia3_otro=trim($_REQUEST['prov_sug3_otro']);//proveedor sugerido otro
		
		if($prov_sugerencia1_otro!=""){
			$prov_sugerencia1 = $prov_sugerencia1_otro;
		}
		if($prov_sugerencia2_otro!=""){
			$prov_sugerencia2 = $prov_sugerencia2_otro;
		}
		if($prov_sugerencia3_otro!=""){
			$prov_sugerencia3 = $prov_sugerencia3_otro;
		}
	
		$prov_calidad=CALIDAD_ALTA;//trim($_REQUEST['calidad']);//calidad
		$prov_entrega=trim($_REQUEST['entrega']);//entrega
		$prov_garantia=trim($_REQUEST['garantia']);//garantia
		$observaciones=trim($_REQUEST['txt_observaciones']);
		$pcta=trim($_REQUEST['pcta']);
		
		if($accion==ACCION_ENVIAR_REQUISICION){
			if(substr($user_perfil_id, 0, 2)=="37"){
				$queryCadena = "(swfg.wfgr_perf = '".substr($user_perfil_id, 0, 2)."000' ".
								"OR swfg.wfgr_perf like '".substr($user_perfil_id, 0, 2)."000%' ".
								"OR swfg.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000' ".
								"OR swfg.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000%') ";
			}else if($user_perfil_id == "38350" || $user_perfil_id == "68150"){
				$queryCadena = "(swfg.wfgr_perf = '".$user_perfil_id."' ".
								"OR swfg.wfgr_perf like '".$user_perfil_id."%' ".
								"OR swfg.wfgr_perf like '%/".$user_perfil_id."' ".
								"OR swfg.wfgr_perf like '%/".$user_perfil_id."%') ";
			}
			$sql=	"SELECT ".
						"swc.wfca_id, ".
						"swc.wfob_id_ini, ".
						"swfgh.wfgr_perf ".
					"FROM sai_wfcadena swc, sai_wfgrupo swfg, sai_wfcadena swch, sai_wfgrupo swfgh ".
					"WHERE ".
						$queryCadena." AND ".
						"swfg.wfgr_id = swc.wfgr_id AND ".
						"swc.docu_id = '".$documentoRequisicion."' AND ".
						"swc.wfop_id = ".$modificarDocumento." AND ".
						(($depe_id=="350" || $depe_id=="150")?"swc.depe_id = '".$depe_id."' AND ":" (swc.depe_id IS NULL OR swc.depe_id = '') AND ").
						"swc.wfca_id_hijo = swch.wfca_id AND ".
						"swch.wfgr_id = swfgh.wfgr_id";
			$resultado = pg_exec($conexion ,$sql);
			if($resultado){
				$row = pg_fetch_array($resultado,0);
				$wfca_id=trim($row["wfca_id"]);
				$wfob_id_ini=trim($row["wfob_id_ini"]);
				$wfgr_perf=trim($row["wfgr_perf"]);
				
				if($depe_id!="350" && $depe_id!="150"){
					$queryCargo = "(";
					$token = strtok($wfgr_perf, "/");
					while ($token !== false) {
					    $queryCargo.=$token.",";
					    $token = strtok("/");
					}
					$queryCargo = substr($queryCargo,0,-1).")";
				}else{
					$queryCargo = "(".substr($wfgr_perf, 0, 2)."000)";
				}
				$sql=	"SELECT substring(carg_id from 1 for 2)||depe_id as perfil ".
						"FROM sai_depen_cargo ".
						"WHERE ".
							"depe_id = '".$_SESSION['user_depe_id']."' AND ".
							"carg_id IN ".$queryCargo;
				$resultado = pg_exec($conexion ,$sql);
				$row = pg_fetch_array($resultado,0);
				$perfil = $row["perfil"];
				
				//Se actualiza el documento generado con el nivel correspondiente en la cadena	
				$sql="UPDATE sai_doc_genera SET wfob_id_ini = ".$wfob_id_ini.", wfca_id = ".$wfca_id.", perf_id_act = '".$perfil."', esta_id = ".$estadoTransito." WHERE docg_id = '".$idRequ."'";
				$resultado = pg_exec($conexion ,$sql);
			}
			//Se modifican los datos de la requisicion y el estado
			$sql="select * from sai_modificar_rqui('".$idRequ."',".$tipo.",".$estadoTransito.",'".$tp_imputacion."','".$proy_central."','".$esp_proy_central."','".$prov_sugerencia1."','".$prov_sugerencia2."','".$prov_sugerencia3."','".$prov_calidad."','".$prov_entrega."','".$prov_garantia."','".$observaciones."','".$pcta."','".$pctaJustificacion."','".$gerenciaAdscripcion."','".$descripcionGeneral."','".$justificacion."','','".$fecha."','".$arreglo_id_articulo."','".$arreglo_cantidad."','".$arreglo_descripcion."') as resultado_set(text)";
			$resultado_modificar = pg_exec($conexion ,$sql);
			header("Location:detalleRequisicion.php?codigo=".$codigo."&idRequ=".$idRequ."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&accion=modificar&proyAcc=".$proyAcc."&radioProyAcc=".$radioProyAcc."&proyecto=".$proyecto."&accionCentralizada=".$accionCentralizada."&dependencia=".$dependencia."&bandeja=".$bandeja,false);
		}else{
			//Se modifican los datos de la requisicion
			$sql="select * from sai_modificar_rqui('".$idRequ."',".$tipo.",".$estadoBorrador.",'".$tp_imputacion."','".$proy_central."','".$esp_proy_central."','".$prov_sugerencia1."','".$prov_sugerencia2."','".$prov_sugerencia3."','".$prov_calidad."','".$prov_entrega."','".$prov_garantia."','".$observaciones."','".$pcta."','".$pctaJustificacion."','".$gerenciaAdscripcion."','".$descripcionGeneral."','".$justificacion."','','".$fecha."','".$arreglo_id_articulo."','".$arreglo_cantidad."','".$arreglo_descripcion."') as resultado_set(text)";
			$resultado_modificar = pg_exec($conexion ,$sql);
			header("Location:detalleRequisicion.php?codigo=".$codigo."&idRequ=".$idRequ."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&accion=modificar_borrador&proyAcc=".$proyAcc."&radioProyAcc=".$radioProyAcc."&proyecto=".$proyecto."&accionCentralizada=".$accionCentralizada."&dependencia=".$dependencia."&bandeja=".$bandeja,false);
		}
	}else{
		header("Location:modificarRequisicion.php?msg=".$error."&codigo=".$codigo."&idRequ=".$idRequ."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&proyAcc=".$proyAcc."&radioProyAcc=".$radioProyAcc."&proyecto=".$proyecto."&accionCentralizada=".$accionCentralizada."&dependencia=".$dependencia."&bandeja=".$bandeja,false);
	}
}
ob_end_flush();
pg_close($conexion);
?>