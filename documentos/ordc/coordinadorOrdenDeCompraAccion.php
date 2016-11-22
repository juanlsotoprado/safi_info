<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");
if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush(); 
	exit;
}
require("../../includes/constantes.php");
$codigo = "";
if (isset($_POST['codigo']) && $_POST['codigo'] != "") {
	$codigo = $_POST['codigo'];
}
$codigoCR = "";
if (isset($_POST['codigoCR']) && $_POST['codigoCR'] != "") {
	$codigoCR = $_POST['codigoCR'];
}
$idOrdc = "";
if (isset($_POST['idOrdc']) && $_POST['idOrdc'] != "") {
	$idOrdc = $_POST['idOrdc'];
}
$tipoRequ = TIPO_REQUISICION_TODAS;
if (isset($_POST['tipoRequ']) && $_POST['tipoRequ'] != "") {
	$tipoRequ = $_POST['tipoRequ'];
}
$pagina = "1";
if (isset($_POST['pagina']) && $_POST['pagina'] != "") {
	$pagina = $_POST['pagina'];
}
$dependencia = "";
if (isset($_GET['dependencia']) && $_GET['dependencia'] != "") {
	$dependencia = $_GET['dependencia'];
}
$estado = ESTADO_REQUISICION_NO_REVISADAS;
if (isset($_POST['estado']) && $_POST['estado'] != "") {
	$estado = $_POST['estado'];
}
$rifProveedor = "";
if (isset($_POST['rifProveedor']) && $_POST['rifProveedor'] != "") {
	$rifProveedor = $_POST['rifProveedor'];
}
$nombreProveedor = "";
if (isset($_POST['nombreProveedor']) && $_POST['nombreProveedor'] != "") {
	$nombreProveedor = $_POST['nombreProveedor'];
}
$idItem = "";
if (isset($_GET['idItem']) && $_GET['idItem'] != "") {
	$idItem = $_GET['idItem'];
}
$nombreItem = "";
if (isset($_GET['nombreItem']) && $_GET['nombreItem'] != "") {
	$nombreItem = $_GET['nombreItem'];
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
$memo = "";
if (isset($_POST['memo']) && $_POST['memo'] != "") {
	$memo = $_POST['memo'];
}
$user_perfil_id = $_SESSION['user_perfil_id'];
$user_login = $_SESSION['login'];
$vistoBueno = 6;
$devolver = 5;
$estadoDevuelto = 7;
$documentoOrdenDeCompra = "ordc";
if($accion==ACCION_APROBAR_REQUISICION){
	$accionDetalles = "aprobar";
	
	$queryCadena = "swfg.wfgr_perf = '".$user_perfil_id."' ";
	$sql=	"SELECT ".
				"swc.wfca_id, ".
				"swc.wfob_id_ini, ".
				"swfgh.wfgr_perf ".
			"FROM sai_wfcadena swc, sai_wfgrupo swfg, sai_wfcadena swch, sai_wfgrupo swfgh ".
			"WHERE ".
				$queryCadena." AND ".
				"swfg.wfgr_id = swc.wfgr_id AND ".
				"swc.docu_id = '".$documentoOrdenDeCompra."' AND ".
				"swc.wfop_id = ".$vistoBueno." AND ".
				"swc.wfca_id_hijo = swch.wfca_id AND ".
				"swch.wfgr_id = swfgh.wfgr_id ";
	$resultado = pg_exec($conexion ,$sql);
	if($resultado){
		$row = pg_fetch_array($resultado,0);
		$wfca_id=trim($row["wfca_id"]);
		$wfob_id_ini=trim($row["wfob_id_ini"]);
		$perfil=trim($row["wfgr_perf"]);
		
		//Se actualiza el documento generado con el nivel correspondiente en la cadena	
		$sql="UPDATE sai_doc_genera SET wfob_id_ini = ".$wfob_id_ini.", wfca_id = ".$wfca_id.", perf_id_act = '".$perfil."' WHERE docg_id = '".$idOrdc."'";
		$resultado = pg_exec($conexion ,$sql);
		//Se actualiza el estado en la orden de compra
		//$sql="UPDATE sai_orden_compra SET esta_id = ".$estadoAprobado." WHERE ordc_id = '".$idOrdc."'";
		//$resultado = pg_exec($conexion ,$sql);
		
		//Insertar la revision
		$sql = " SELECT * FROM sai_insert_revision_doc('$idOrdc', '$user_login', '$user_perfil_id', '$vistoBueno', '') as resultado ";
		$resultado = pg_query($conexion,$sql);
	}
	header("Location:detalleOrdenDeCompra.php?codigo=".$codigo."&codigoCR=".$codigoCR."&idOrdc=".$idOrdc."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&accion=".$accionDetalles."&dependencia=".$dependencia."&rifProveedor=".$rifProveedor."&nombreProveedor=".$nombreProveedor."&idItem=".$idItem."&nombreItem=".$nombreItem."&bandeja=".$bandeja,false);
}else if($accion==ACCION_DEVOLVER_REQUISICION){
	$error="";
	if($memo==""){
		$error = "0";//Debe indicar el memo de la devolucion
	}
	if($error==""){
		$accionDetalles = "devolver";
		$queryCadena = "swfg.wfgr_perf = '".$user_perfil_id."' ";
		$sql=	"SELECT ".
					"swc.wfca_id,".
					"swc.wfob_id_ini, ".
					"swfgh.wfgr_perf ".
				"FROM sai_wfcadena swc, sai_wfgrupo swfg, sai_wfcadena swch, sai_wfgrupo swfgh ".
				"WHERE ".
					$queryCadena." AND ".
					"swfg.wfgr_id = swc.wfgr_id AND ".
					"swc.docu_id = '".$documentoOrdenDeCompra."' AND ".
					"swc.wfop_id = ".$devolver." AND ".
					"swc.wfca_id_hijo = swch.wfca_id AND ".
					"swch.wfgr_id = swfgh.wfgr_id";
		$resultado = pg_exec($conexion ,$sql);
		if($resultado){
			$row = pg_fetch_array($resultado,0);
			$wfca_id=trim($row["wfca_id"]);
			$wfob_id_ini=trim($row["wfob_id_ini"]);
			$perfil=trim($row["wfgr_perf"]);
			
			//Se actualiza el documento generado con el nivel correspondiente en la cadena		
			$sql="UPDATE sai_doc_genera SET wfob_id_ini = ".$wfob_id_ini.", wfca_id = ".$wfca_id.", perf_id_act = '".$perfil."', esta_id = ".$estadoDevuelto." WHERE docg_id = '".$idOrdc."'";
			$resultado = pg_exec($conexion ,$sql);
			//Se actualiza el estado en la orden de compra
			$sql="UPDATE sai_orden_compra SET esta_id = ".$estadoDevuelto." WHERE ordc_id = '".$idOrdc."'";
			$resultado = pg_exec($conexion ,$sql);
			//Se inserta un memo por la devolucion de la orden de compra
			$sql="SELECT * FROM sai_insert_memo('".$_SESSION['login']."', '".$_SESSION['user_depe_id']."','".$memo."','".utf8_decode("Devolución de Orden de Compra")."','0','0','0','',0, 0, '0','','".$idOrdc."') as resultado_set(text)";
			$resultado = pg_exec($conexion ,$sql);
			$row = pg_fetch_array($resultado, 0);
			
			//Insertar la revision
			$sql = " SELECT * FROM sai_insert_revision_doc('$idOrdc', '$user_login', '$user_perfil_id', '$devolver', '') as resultado ";
			$resultado = pg_query($conexion,$sql);
			header("Location:detalleOrdenDeCompra.php?codigo=".$codigo."&codigoCR=".$codigoCR."&idOrdc=".$idOrdc."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&accion=".$accionDetalles."&dependencia=".$dependencia."&rifProveedor=".$rifProveedor."&nombreProveedor=".$nombreProveedor."&idItem=".$idItem."&nombreItem=".$nombreItem."&bandeja=".$bandeja,false);		
		}
	}else{
		header("Location:coordinadorOrdenDeCompra.php?msg=".$error."&codigo=".$codigo."&codigoCR=".$codigoCR."&idOrdc=".$idOrdc."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&accion=".$accionDetalles."&dependencia=".$dependencia."&rifProveedor=".$rifProveedor."&nombreProveedor=".$nombreProveedor."&idItem=".$idItem."&nombreItem=".$nombreItem."&bandeja=".$bandeja,false);
	}
}
ob_end_flush();
pg_close($conexion);
?>