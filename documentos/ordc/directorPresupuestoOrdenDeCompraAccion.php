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
				"swc.wfca_id_hijo, ".
				"swc.wfob_id_sig ".
			"FROM sai_wfcadena swc, sai_wfgrupo swfg ".
			"WHERE ".
				$queryCadena." AND ".
				"swfg.wfgr_id = swc.wfgr_id AND ".
				"swc.docu_id = '".$documentoOrdenDeCompra."' AND ".
				"swc.wfop_id = ".$vistoBueno." ";
	$resultado = pg_exec($conexion ,$sql);
	if($resultado){
		$row = pg_fetch_array($resultado,0);
		$wfca_id=trim($row["wfca_id_hijo"]);
		$wfob_id_ini=trim($row["wfob_id_sig"]);
		$perfil="";
		
		//Se actualiza el documento generado con el nivel correspondiente en la cadena	
		$sql="UPDATE sai_doc_genera SET wfob_id_ini = ".$wfob_id_ini.", wfca_id = ".$wfca_id.", perf_id_act = '".$perfil."' WHERE docg_id = '".$idOrdc."'";
		$resultado = pg_exec($conexion ,$sql);

		//Insertar la revision
		$sql = " SELECT * FROM sai_insert_revision_doc('$idOrdc', '$user_login', '$user_perfil_id', '$vistoBueno', '') as resultado ";
		$resultado = pg_query($conexion,$sql);
	}
	
	/*GENERAR COMP AUTOMATICAMENTE si la requisicion no viene de punto de cuenta*/
	/*$sql =	"SELECT * ".
			"FROM ".
				"sai_insert_comp(".
									"'028',".
									"'".$descripcion. "',".//descripcion de la requisicion
									"'".$_SESSION['login']."',".
									"'".$dependencia."',".//de quien creó la requisicion/ o para quien
									"'".$observaciones."',".// de la orden de compra
									"'".$justificacion."',".// de la orden de compra
									"'".$lapso."',".//fecha de entrega de la orden de compra
									"'".$cond_pago."',".//forma de pago de la orden de compra
									"'".$monto_solicitado."',".//monto total de la orden de compra
									"'".$prioridad."',".//1
									"'".$reserva."',".//nada
									"'400',".
									"'".$recursos."',".//1
									"'".$garantia."',".//Garantia de anticipo con otras garantias
									"'".$dependencia."',".//de quien creó la requisicion/ o para quien
									"'".$fc."',".
									"'".$codigo."',".//ordc
									"'".$rif_sugerido."') AS resultado_set(VARCHAR)";//rif del proveedor
    $resultado_set = pg_exec($conexion ,$sql) or die("Error al ingresar el compromiso");
    $row = pg_fetch_array($resultado_set);
	$codigo_comp=$row[0];
    
    $sql =	"SELECT * ".
    		"FROM ".
    			"sai_insert_doc_generado(".
    									"'".$codigo_comp."',".
    									"99,".
    									"0,".
    									"'".$_SESSION['login']."',".
    									"'30400',".
    									"13,".
    									"'1',".
    									"'',".
    									"'') AS resultado";
    $resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	if ($row = pg_fetch_array($resultado)) {
		$inserto_doc = $row["resultado"];
	}
	
	$sqlt =	"SELECT * ".
			"FROM ".
				"sai_insert_comp_traza(".
										"'".trim($asunto_id)."',".
										"'".$descripcion."',".
										"'".$_SESSION['login']."',".
										"'".$dependencia ."',".
										"'".$observaciones."',".
										"'".$justificacion."',".
										"'".$lapso."',".
										"'".$cond_pago."',".
										"'".$monto_solicitado."',".
										"'".$prioridad."',".
										"'".$reserva."',".
										"'400',".
										"'".$recursos."',".
										"'".$garantia."',".
										"'".$dependencia."',".
										"'".$fc."',".
										"'".$codigo."',".
										"'".$rif_sugerido."',".
										"'".$codigo_comp."') AS resultado_set(VARCHAR)";
	$resultado_set = pg_exec($conexion ,$sqlt) or die("Error al ingresar la traza del compromiso");

	$sql_imputa = 	"SELECT * ".
					"FROM ".
					"sai_insert_comp_imputa(".
											"'".trim($asunto_id)."',".
											"'".$codigo_comp."',".
											"'".$anno_pres."',".
											"'".$arreglo_acc_pp."',".
											"'".$arreglo_acc_esp."',".
											"'".$arreglo_tipo_impu."',".
											"'".$arreglo_sub_esp."',".
											"'".$arreglo_monto."',".
											"'".$arreglo_uel."') AS resultado_ser(VARCHAR)";//depe de la requisicion
	$resultado_set = pg_exec($conexion ,$sql_imputa) or die("Error al ingresar las Imputaciones Presupuestarias");

	$sql_imputa = 	"SELECT * ".
					"FROM ".
						"sai_insert_comp_imputa_traza(".
														"'".trim($asunto_id)."',".
														"'".$codigo_comp."',".
														"'".$anno_pres."',".
														"'".$arreglo_acc_pp."',".
														"'".$arreglo_acc_esp."',".
														"'".$arreglo_tipo_impu."',".
														"'".$arreglo_sub_esp."',".
														"'".$arreglo_monto."',".
														"'".$arreglo_uel."') AS resultado_ser(VARCHAR)";
	$resultado_set = pg_exec($conexion ,$sql_imputa) or die("Error al ingresar las Imputaciones Presupuestarias");
	//FIN GENERAR COMP AUTOMATICAMENTE*/
	
	header("Location:detallePresupuestoOrdenDeCompra.php?codigo=".$codigo."&codigoCR=".$codigoCR."&idOrdc=".$idOrdc."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&accion=".$accionDetalles."&dependencia=".$dependencia."&rifProveedor=".$rifProveedor."&nombreProveedor=".$nombreProveedor."&idItem=".$idItem."&nombreItem=".$nombreItem."&bandeja=".$bandeja,false);
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
			
			header("Location:detallePresupuestoOrdenDeCompra.php?codigo=".$codigo."&codigoCR=".$codigoCR."&idOrdc=".$idOrdc."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&accion=".$accionDetalles."&dependencia=".$dependencia."&rifProveedor=".$rifProveedor."&nombreProveedor=".$nombreProveedor."&idItem=".$idItem."&nombreItem=".$nombreItem."&bandeja=".$bandeja,false);		
		}
	}else{
		header("Location:directorPresupuestoOrdenDeCompra.php?msg=".$error."&codigo=".$codigo."&codigoCR=".$codigoCR."&idOrdc=".$idOrdc."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&accion=".$accionDetalles."&dependencia=".$dependencia."&rifProveedor=".$rifProveedor."&nombreProveedor=".$nombreProveedor."&idItem=".$idItem."&nombreItem=".$nombreItem."&bandeja=".$bandeja,false);
	}
}
ob_end_flush();
pg_close($conexion);
?>