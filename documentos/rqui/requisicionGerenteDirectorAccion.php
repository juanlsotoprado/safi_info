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
$estado = ESTADO_REQUISICION_NO_REVISADAS;
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
$memo = "";
if (isset($_POST['memo']) && $_POST['memo'] != "") {
	$memo = $_POST['memo'];
}
$user_perfil_id = $_SESSION['user_perfil_id'];
$depe_id=$_SESSION['user_depe_id'];
$user_login = $_SESSION['login'];
$vistoBueno = 6;
$devolver = 5;
$estadoAprobado = 13;
$estadoDevuelto = 7;
$documentoRequisicion = "rqui";
if($accion==ACCION_APROBAR_REQUISICION){
	$accionDetalles = "aprobar";
	
	$objeto = 3;//Agregar informacion
	$perfil = "30400";//ANALISTA DE PRESUPUESTO

	if (substr($user_perfil_id, 0, 2) == "60" || substr($user_perfil_id, 0, 2) == "46")  {
		$queryCadena = "(swfg.wfgr_perf = '".substr($user_perfil_id, 0, 2)."000' ".
						"OR swfg.wfgr_perf like '".substr($user_perfil_id, 0, 2)."000%' ".
						"OR swfg.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000' ".
						"OR swfg.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000%') ";
	}else if($user_perfil_id == "47350" || $user_perfil_id == "65150"){
		$queryCadena = "(swfg.wfgr_perf = '".$user_perfil_id."' ".
						"OR swfg.wfgr_perf like '".$user_perfil_id."%' ".
						"OR swfg.wfgr_perf like '%/".$user_perfil_id."' ".
						"OR swfg.wfgr_perf like '%/".$user_perfil_id."%') ";
	}
	
	$sql=	"SELECT ".
				"swc.wfca_id, ".
				"swc.wfob_id_ini ".
			"FROM sai_wfcadena swc, sai_wfgrupo swfg ".
			"WHERE ".
				$queryCadena." AND ".
				"swfg.wfgr_id = swc.wfgr_id AND ".
				"swc.docu_id = '".$documentoRequisicion."' AND ".
				"swc.wfop_id = ".$vistoBueno." AND ".
				"swc.wfob_id_ini = ".$objeto;
	$resultado = pg_exec($conexion ,$sql);
	if($resultado){
		$row = pg_fetch_array($resultado,0);
		$wfca_id=trim($row["wfca_id"]);
		$wfob_id_ini=trim($row["wfob_id_ini"]);
		
		//Se actualiza el documento generado con el nivel correspondiente en la cadena	
		$sql="UPDATE sai_doc_genera SET wfob_id_ini = ".$wfob_id_ini.", wfca_id = ".$wfca_id.", perf_id_act = '".$perfil."', esta_id = ".$estadoAprobado." WHERE docg_id = '".$idRequ."'";
		$resultado = pg_exec($conexion ,$sql);
		
		//Se actualiza el estado en la requisicion
		
		$sql="UPDATE sai_req_bi_ma_ser SET esta_id = ".$estadoAprobado." WHERE rebms_id = '".$idRequ."'";
		$resultado = pg_exec($conexion ,$sql);
		
		//Insertar la revision
		$sql = " SELECT * FROM sai_insert_revision_doc('$idRequ', '$user_login', '$user_perfil_id', '$vistoBueno', '') as resultado ";
		$resultado = pg_query($conexion,$sql);
	}
	/*
	//EMAIL
	$sql = 	"SELECT depe_nombre FROM sai_dependenci ".
			"WHERE ".
				"depe_id = '".$_SESSION['user_depe_id']."'";
	$resultado = pg_exec($conexion, $sql);
	$row = pg_fetch_array($resultado, 0);
	$depe_nombre = $row["depe_nombre"];
	
	$emailCargo = substr($perfil,0,2);
	$emailDependencia = substr($perfil,2);
	$sql="SELECT sem.empl_email, sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante FROM sai_empleado sem WHERE sem.carg_fundacion = '".$emailCargo."' AND sem.depe_cosige = '".$emailDependencia."'";
	$resultado = pg_exec($conexion ,$sql);
	$numeroFilas = pg_numrows($resultado);
	if($numeroFilas>0){
		require("../../includes/funciones.php");
		$de = "info@infocentro.gob.ve";
		$nombreDe = utf8_decode("SISTEMA ADMINISTRATIVO DE LA FUNDACION INFOCENTRO");
		$copiaOculta = "";
		$nombreCopiaOculta = "";
		$asunto = utf8_decode("Nueva solicitud de requisición");
		$message = wordwrap(utf8_decode("Se ha aprobado una nueva solicitud de requisición con código ".$idRequ." procedente de ").$depe_nombre, 70);
		while($row = pg_fetch_array($resultado)){
			$para = $row['empl_email'];
			$nombrePara = utf8_decode($row['solicitante']);
			enviarEmail($de, $nombreDe, $para, $nombrePara, $copiaOculta, $nombreCopiaOculta, $asunto, $message, null);
		}
	}*/
	header("Location:detalleRequisicion.php?codigo=".$codigo."&idRequ=".$idRequ."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&accion=".$accionDetalles."&proyAcc=".$proyAcc."&radioProyAcc=".$radioProyAcc."&proyecto=".$proyecto."&accionCentralizada=".$accionCentralizada."&dependencia=".$dependencia."&bandeja=".$bandeja,false);
}else if($accion==ACCION_DEVOLVER_REQUISICION){
	$error="";
	if($memo==""){
		$error = "0";//Debe indicar el memo de la devolucion
	}
	if($error==""){
		$accionDetalles = "devolver";
		
		if (substr($user_perfil_id, 0, 2) == "60" || substr($user_perfil_id, 0, 2) == "46")  {
			$queryCadena = "(swfg.wfgr_perf = '".substr($user_perfil_id, 0, 2)."000' ".
							"OR swfg.wfgr_perf like '".substr($user_perfil_id, 0, 2)."000%' ".
							"OR swfg.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000' ".
							"OR swfg.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000%') ";
		}else if($user_perfil_id == "47350" || $user_perfil_id == "65150"){
			$queryCadena = "(swfg.wfgr_perf = '".$user_perfil_id."' ".
							"OR swfg.wfgr_perf like '".$user_perfil_id."%' ".
							"OR swfg.wfgr_perf like '%/".$user_perfil_id."' ".
							"OR swfg.wfgr_perf like '%/".$user_perfil_id."%') ";
		}
		
		$sql=	"SELECT ".
					"swc.wfca_id,".
					"swc.wfob_id_ini, ".
					"swfgh.wfgr_perf ".
				"FROM sai_wfcadena swc, sai_wfgrupo swfg, sai_wfcadena swch, sai_wfgrupo swfgh ".
				"WHERE ".
					$queryCadena." AND ".
					"swfg.wfgr_id = swc.wfgr_id AND ".
					"swc.docu_id = '".$documentoRequisicion."' AND ".
					"swc.wfop_id = ".$devolver." AND ".
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
			$sql="UPDATE sai_doc_genera SET wfob_id_ini = ".$wfob_id_ini.", wfca_id = ".$wfca_id.", perf_id_act = '".$perfil."', esta_id = ".$estadoDevuelto." WHERE docg_id = '".$idRequ."'";
			$resultado = pg_exec($conexion ,$sql);
			//Se actualiza el estado en la requisicion
			$sql="UPDATE sai_req_bi_ma_ser SET esta_id = ".$estadoDevuelto." WHERE rebms_id = '".$idRequ."'";
			$resultado = pg_exec($conexion ,$sql);
			//Se inserta un memo por la devolucion de la requisicion
			$sql="SELECT * FROM sai_insert_memo('".$_SESSION['login']."', '".$_SESSION['user_depe_id']."','".$memo."','".utf8_decode("Devolucion de Requisición")."','0','0','0','',0, 0, '0','','".$idRequ."') as resultado_set(text)";
			$resultado = pg_exec($conexion ,$sql);
			$row = pg_fetch_array($resultado, 0);
			
			//Insertar la revision
			$sql = " SELECT * FROM sai_insert_revision_doc('$idRequ', '$user_login', '$user_perfil_id', '$devolver', '') as resultado ";
			$resultado = pg_query($conexion,$sql);
			header("Location:detalleRequisicion.php?codigo=".$codigo."&idRequ=".$idRequ."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&accion=".$accionDetalles."&proyAcc=".$proyAcc."&radioProyAcc=".$radioProyAcc."&proyecto=".$proyecto."&accionCentralizada=".$accionCentralizada."&dependencia=".$dependencia."&bandeja=".$bandeja,false);
		}
	}else{
		header("Location:requisicionGerenteDirector.php?msg=".$error."&codigo=".$codigo."&idRequ=".$idRequ."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&accion=".$accionDetalles."&proyAcc=".$proyAcc."&radioProyAcc=".$radioProyAcc."&proyecto=".$proyecto."&accionCentralizada=".$accionCentralizada."&dependencia=".$dependencia."&bandeja=".$bandeja,false);
	}
}
ob_end_flush();
pg_close($conexion);
?>