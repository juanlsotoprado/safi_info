<?php
ob_start();
session_start();
require("../../../includes/conexion.php");
require('../../../includes/arreglos_pg.php');
require('../../../includes/constantes.php');
if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:../../../bienvenida.php',false);
	ob_end_flush();
	exit;
}
$error = "";
$tipo=trim($_REQUEST['tipoImputacion']);
if($tipo==""){
	$error = "0";//Debe indicar el tipo de categoría programática (Proyecto o Acción centralizada).
}
if($error==""){
	$anioPresupuestario=trim($_REQUEST['anioPresupuestario']);
	if($anioPresupuestario==""){
		$error = "1";//Debe indicar el año presupuestario.
	}
}
if($error==""){
	$codigo=trim($_REQUEST['codigo']);
	if($codigo==""){
		$error = "2";//Debe indicar el código de la categoría programática.
	}
}
if($error==""){
	$titulo=trim($_REQUEST['titulo']);
	if($titulo==""){
		$error = "3";//Debe indicar el título de la categoría programática.
	}
}
if($tipo==TIPO_IMPUTACION_PROYECTO){
	if($error==""){
		$descripcion=trim($_REQUEST['descripcion']);
		if($descripcion==""){
			$error = "4";//Debe indicar la descripción.
		}
	}
	if($error==""){
		$objetivos=trim($_REQUEST['objetivos']);
		if($objetivos==""){
			$error = "5";//Debe indicar los objetivos.
		}
	}
}else{
	$descripcion = "";
	$objetivos = "";
}
if($error==""){
	$tamano=trim($_REQUEST['tamanoAccionesEspecificas']);
	if($tamano==""){
		$error = "6";//Falta la cantidad de acciones específicas.
	}
}
for($i=0; ($i<$tamano && $error==""); $i++){
	$matriz_fecha_inicio[$i]=trim($_REQUEST['fechaInicio'.$i]);
	$matriz_fecha_fin[$i]="31/12/".$anioPresupuestario;
	$matriz_nombre_accion_especifica[$i]=trim($_REQUEST['nombreAccionEspecifica'.$i]);
	$matriz_codigo_accion_especifica[$i]=trim($_REQUEST['codigoAccionEspecifica'.$i]);
	$matriz_centro_gestor[$i]=trim($_REQUEST['centroGestor'.$i]);
	$matriz_centro_costos[$i]=trim($_REQUEST['centroCostos'.$i]);
}
if($error=="" && (sizeof($matriz_fecha_inicio)<1 || sizeof($matriz_fecha_fin)<1 || sizeof($matriz_nombre_accion_especifica)<1 || sizeof($matriz_codigo_accion_especifica)<1 || sizeof($matriz_centro_gestor)<1 || sizeof($matriz_centro_costos)<1)){
	$error = "7";//Debe indicar al menos una (1) acción específica para la categoría programática.
}
if($error==""){
	if($tipo==TIPO_IMPUTACION_PROYECTO){
		$query = 	"SELECT ".
						"COUNT(sp.proy_id) ".
					"FROM ".
						"sai_proyecto sp ".
					"WHERE ".
						"sp.proy_id = '".$codigo."' AND sp.pre_anno = ".$anioPresupuestario;
		$resultado=pg_query($conexion,$query);
		$row=pg_fetch_array($resultado);
		if($row[0]>0){
			$error = "8";//Ya existe un proyecto con el mismo código y el mismo año presupuestario.
		}
	}else if($tipo==TIPO_IMPUTACION_ACCION_CENTRALIZADA){
		$query = 	"SELECT ".
						"COUNT(sac.acce_id) ".
					"FROM ".
						"sai_ac_central sac ".
					"WHERE ".
						"sac.acce_id = '".$codigo."' AND sac.pres_anno = ".$anioPresupuestario;
		$resultado=pg_query($conexion,$query);
		$row=pg_fetch_array($resultado);
		if($row[0]>0){
			$error = "9";//Ya existe una acción centralizada con el mismo código y el mismo año presupuestario.
		}
	}
}
if($error == ""){
	$arreglo_fecha_inicio=convierte_arreglo($matriz_fecha_inicio);
	$arreglo_fecha_fin=convierte_arreglo($matriz_fecha_fin);
	$arreglo_nombre_accion_especifica=convierte_arreglo($matriz_nombre_accion_especifica);
	$arreglo_codigo_accion_especifica=convierte_arreglo($matriz_codigo_accion_especifica);
	$arreglo_centro_gestor=convierte_arreglo($matriz_centro_gestor);
	$arreglo_centro_costos=convierte_arreglo($matriz_centro_costos);
	
	$resultado=trim($_REQUEST['resultado']);//resultado
	$observaciones=trim($_REQUEST['observaciones']);//observaciones
	
	$esta_id="32";
	$usua_login=$_SESSION['login'];
	$usua_login_responsable="saiadmin";
	$codigo_onapre=$codigo;
	
	$sql="SELECT * FROM sai_ingresar_categoria_programatica(".
															"".$tipo.",".
															"".$anioPresupuestario.",".
															"'".$codigo."',".
															"'".$titulo."',".
															"'".$descripcion."',".
															"'".$objetivos."',".
															"'".$resultado."',".
															"'".$observaciones."',".
															"".$esta_id.",".
															"'".$usua_login."',".
															"'".$usua_login_responsable."',".
															"'".$codigo_onapre."',".
															"'".$arreglo_fecha_inicio."',".
															"'".$arreglo_fecha_fin."',".
															"'".$arreglo_nombre_accion_especifica."',".
															"'".$arreglo_codigo_accion_especifica."',".
															"'".$arreglo_centro_gestor."',".
															"'".$arreglo_centro_costos."') AS resultado_set(TEXT)";

	$resultado_insert = pg_exec($conexion, $sql);
	header("Location:detalle.php?codigo=".$codigo."&anioPress=".$anioPresupuestario."&tipo=".$tipo."&accion=generar",false);
}else{
	header("Location:ingresar.php?msg=".$error,false);
}
ob_end_flush();
pg_close($conexion);
?>