<?php
ob_start();
session_start();
require("../../includes/conexion.php");
require('../../includes/arreglos_pg.php');
require('../../includes/constantes.php');
if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:../../bienvenida.php',false);
	ob_end_flush();
	exit;
}
$error = "";
$tipo=trim($_REQUEST['tipo']);
if(!$tipo || $tipo==""){
	$error = "0";//Falta el tipo de comunicación.
	$tipo = "memo";
}
if($error==""){
	$de=trim($_REQUEST['de']);
	if($de==""){
		$error = "1";//Debe indicar la dependencia remitente.
	}
}
if($error==""){
	$asunto=trim($_REQUEST['asunto']);
	if($asunto==""){
		$error = "2";//Debe indicar el asunto.
	}
}
if($error==""){
	$fecha=trim($_REQUEST['fecha']);
	if($fecha==""){
		$error = "3";//Debe indicar la fecha.
	}
}
if($error==""){
	$descripcion=trim($_REQUEST['descripcion']);
	if($descripcion==""){
		$error = "4";//Debe indicar la descripción.
	}
}
if($error==""){
	$despedida=trim($_REQUEST['despedida']);
	if($despedida==""){
		$error = "5";//Debe indicar la despedida.
	}
}
if($error==""){
	$alineacionDespedida=trim($_REQUEST['alineacionDespedida']);
	if($alineacionDespedida==""){
		$error = "6";//Debe indicar la alineación de la despedida.
	}
}
if($error==""){
	$coletilla=trim($_REQUEST['coletilla']);
}
if($error==""){
	$anexos=trim($_REQUEST['anexos']);
}
if($error==""){
	$tamanoPersonalPara=trim($_REQUEST['tamanoPersonalPara']);
	if($tamanoPersonalPara==""){
		$error = "7";//Falta la cantidad de personal del Para.
	}
}
if($error==""){
	$tamanoPersonalCc=trim($_REQUEST['tamanoPersonalCc']);
	if($tamanoPersonalCc==""){
		$error = "8";//Falta la cantidad de personal del Cc.
	}
}
for($i=0; ($i<$tamanoPersonalPara && $error==""); $i++){
	$matriz_personal_para[$i]=trim($_REQUEST['cedulaPara'.$i]);
}
for($i=0; ($i<$tamanoPersonalCc && $error==""); $i++){
	$matriz_personal_cc[$i]=trim($_REQUEST['cedulaCc'.$i]);
}
if($error=="" && sizeof($matriz_personal_para)<1){
	$error = "9";//Debe indicar al menos un (1) personal en la sección Para.
}
if($error == ""){
	$firmaPresidencia=trim($_REQUEST['firmaPresidencia']);
	$firmaAdministracion=trim($_REQUEST['firmaAdministracion']);
	
	$arreglo_personal_para=convierte_arreglo($matriz_personal_para);
	$arreglo_personal_cc=convierte_arreglo($matriz_personal_cc);
	
	$dependencia_elabora = $_SESSION['user_depe_id'];
	$esta_id="10";
	$usua_login=$_SESSION['login'];
	
	
	$descrip_sin_tags=  str_replace( "'", '"', $descripcion );

	$sql="SELECT * FROM sai_ingresar_comunicacion(".
												"'".$tipo."',".
												"'".$fecha."',".
												"'".$usua_login."',".
												"'".$dependencia_elabora."',".
												"'".$de."',".
												"'".$asunto."',".
												"'".$descrip_sin_tags."',".
												"'".$anexos."',".
												"'".$despedida."',".
												"'".$alineacionDespedida."',".
												"'".$coletilla."',".
												"".$esta_id.",".
												"'".(($firmaPresidencia=="on")?"true":"false")."',".
												"'".(($firmaAdministracion=="on")?"true":"false")."',".
												"'".$arreglo_personal_para."',".
												"'".(($arreglo_personal_cc!="")?$arreglo_personal_cc:"{}")."') AS resultado_set(TEXT)";
	$resultado_insert = pg_exec($conexion, $sql);
	$row = pg_fetch_array($resultado_insert,0);
	$codigo = $row[0];
	header("Location:detalle.php?tipo=".$tipo."&codigo=".$codigo."&accion=generar",false);
}else{
	header("Location:ingresar.php?tipo=".$tipo."&msg=".$error,false);
}
ob_end_flush();
pg_close($conexion);
?>