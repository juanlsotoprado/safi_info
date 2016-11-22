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
$error = "";
$fecha=$_REQUEST['fecha'];
$tipo=trim($_REQUEST['typo']);
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
if($error == ""){
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

	$pres_anno=$_SESSION['an_o_presupuesto'];
	$usua_login=$_SESSION['login'];
	$depe_id=$_SESSION['user_depe_id'];
	if($_REQUEST["accion"]==ACCION_ENVIAR_REQUISICION){
		$estado = 10;
	}else{
		$estado = 60;
	}
	$sql="select * from sai_ingresar_rqui(".$tipo.",'".$depe_id."','".$usua_login."',".$pres_anno.",'".$tp_imputacion."','".$proy_central."','".$esp_proy_central."','".$prov_sugerencia1."','".$prov_sugerencia2."','".$prov_sugerencia3."','".$prov_calidad."','".$prov_entrega."','".$prov_garantia."','".$observaciones."','".$pcta."','".$pctaJustificacion."','".$gerenciaAdscripcion."','".$descripcionGeneral."','".$justificacion."','',".$estado.",'".$fecha."','".$arreglo_id_articulo."','".$arreglo_cantidad."','".$arreglo_descripcion."') as resultado_set(text)";
	$resultado_insert = pg_exec($conexion ,$sql);
	if($resultado_insert){
		$rowa = pg_fetch_array($resultado_insert,0);
		if ($rowa[0] <> null){
			$codigo=trim($rowa[0]);
			$documentoRequisicion = "rqui";
			$objeto = 1;
			$sql=	"SELECT ".
						"swc.wfca_id, ".
						"swfg.wfgr_perf ".
					"FROM sai_wfcadena swc, sai_wfcadena swch, sai_wfgrupo swfg ".
					"WHERE ".
						"swc.docu_id = '".$documentoRequisicion."' AND ".
						"swc.wfob_id_ini = ".$objeto." AND ".
						(($depe_id=="350" || $depe_id=="150")?"swc.depe_id = '".$depe_id."' AND ":" (swc.depe_id IS NULL OR swc.depe_id = '') AND ").
						"swc.wfca_id_hijo = swch.wfca_id AND ".
						"swch.wfgr_id = swfg.wfgr_id";
			$resultado = pg_exec($conexion ,$sql);
			if($resultado){
				$row = pg_fetch_array($resultado,0);
				$wfca_id=trim($row["wfca_id"]);
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
							"depe_id = '".$depe_id."' AND ".
							"carg_id IN ".$queryCargo;
				$resultado = pg_exec($conexion ,$sql);
				$row = pg_fetch_array($resultado,0);
				$perfil = $row["perfil"];
				
				$sql="select * from sai_insert_doc_generado('".$codigo."',1,".$wfca_id.",'".$_SESSION['login']."','".$_SESSION['user_perfil_id']."',".$estado.",1,'".$perfil."','N/A') as resultado_set(text)";
				$resultado_insert = pg_exec($conexion ,$sql);			
			}
		}
	}
	if($_REQUEST["accion"]==ACCION_ENVIAR_REQUISICION){
		header("Location:detalleRequisicion.php?idRequ=".$codigo."&accion=generar",false);
	}else{
		header("Location:detalleRequisicion.php?idRequ=".$codigo."&accion=generar_borrador",false);
	}
}else{
	header("Location:requisicion.php?msg=".$error,false);
}
ob_end_flush();
pg_close($conexion);
?>