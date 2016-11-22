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
$categoriaProgramatica=trim($_REQUEST['categoriaProgramatica']);
if($categoriaProgramatica==""){
	$error = "0";//Debe indicar la categoría programática.
}
if($error==""){
	$tok = strtok($categoriaProgramatica, "|");
	$i=0;
	$tipoImputacion = "";
	$anioPresupuestario = "";
	$proyAcc = "";
	while($tok !== false){
		if($i==0){
			$tipoImputacion = $tok;		
		}else if($i==1){
			$anioPresupuestario = $tok;		
		}else if($i==2){
			$proyAcc = $tok;		
		}
	    $tok = strtok("|");
	    $i++;
	}
	if($tipoImputacion=="" || $anioPresupuestario=="" || $proyAcc==""){
		$error = "1";//Error en el formato de la categoría programática.
	}
}
if($error==""){
	$accionEspecifica=trim($_REQUEST['accionEspecifica']);
	if($accionEspecifica==""){
		$error = "2";//Debe indicar la acción específica.
	}
}
if($error==""){
	$fuenteFinanciamiento=trim($_REQUEST['fuenteFinanciamiento']);
	if($fuenteFinanciamiento==""){
		$error = "3";//Debe indicar la fuente de financiamiento.
	}
}
if($error==""){
	$dependencia=trim($_REQUEST['dependencia']);
	if($dependencia==""){
		$error = "4";//Debe indicar la dependencia.
	}
}
if($error==""){
	$tamano=trim($_REQUEST['tamanoPartidas']);
	if($tamano==""){
		$error = "5";//Falta la cantidad de partidas.
	}
}
for($i=0; ($i<$tamano && $error==""); $i++){
	$matriz_partida[$i]=trim($_REQUEST['partida'.$i]);
	if(isset($_REQUEST['1erTrimestre'.$i]) && $_REQUEST['1erTrimestre'.$i]!=""){
		$matriz_primer_trimestre[$i]=trim($_REQUEST['1erTrimestre'.$i]);
	}else{
		$matriz_primer_trimestre[$i]="0.0";
	}
	if(isset($_REQUEST['2doTrimestre'.$i]) && $_REQUEST['2doTrimestre'.$i]!=""){
		$matriz_segundo_trimestre[$i]=trim($_REQUEST['2doTrimestre'.$i]);
	}else{
		$matriz_segundo_trimestre[$i]="0.0";
	}
	if(isset($_REQUEST['3erTrimestre'.$i]) && $_REQUEST['3erTrimestre'.$i]!=""){
		$matriz_tercer_trimestre[$i]=trim($_REQUEST['3erTrimestre'.$i]);
	}else{
		$matriz_tercer_trimestre[$i]="0.0";
	}
	if(isset($_REQUEST['4toTrimestre'.$i]) && $_REQUEST['4toTrimestre'.$i]!=""){
		$matriz_cuarto_trimestre[$i]=trim($_REQUEST['4toTrimestre'.$i]);
	}else{
		$matriz_cuarto_trimestre[$i]="0.0";
	}
}

if($error=="" && (sizeof($matriz_partida)<1 || sizeof($matriz_primer_trimestre)<1 || sizeof($matriz_segundo_trimestre)<1 || sizeof($matriz_tercer_trimestre)<1 || sizeof($matriz_cuarto_trimestre)<1)){
	$error = "6";//Debe indicar al menos una (1) partida para la asingación presupuestaria.
}
if($error==""){
	$query = 	"SELECT ".
					"COUNT(sf.form_id) ".
				"FROM ".
					"sai_forma_1125 sf ".
				"WHERE ".
					"sf.form_id_p_ac = '".$categoriaProgramatica."' AND ".
					"sf.pres_anno = ".$anioPresupuestario." AND ".
					"sf.form_tipo = ".$tipoImputacion."::BIT(1) AND ".
					"sf.form_id_aesp = '".$accionEspecifica."'";
	$resultado=pg_query($conexion,$query);
	$row=pg_fetch_array($resultado);
	if($row[0]>0){
		$error = "7";//La acción específica seleccionada ya tiene una asignación presupuestaria.
	}
}
if($error == ""){
	$arreglo_partida=convierte_arreglo($matriz_partida);
	$arreglo_primer_trimestre=convierte_arreglo($matriz_primer_trimestre);
	$arreglo_segundo_trimestre=convierte_arreglo($matriz_segundo_trimestre);
	$arreglo_tercer_trimestre=convierte_arreglo($matriz_tercer_trimestre);
	$arreglo_cuarto_trimestre=convierte_arreglo($matriz_cuarto_trimestre);
	
	$dependencia_elabora = "400";
	$esta_id="1";
	$usua_login=$_SESSION['login'];
	
	$sql="SELECT * FROM sai_ingresar_asignacion_presupuestaria(".
															"".$tipoImputacion.",".
															"".$anioPresupuestario.",".
															"'".$proyAcc."',".
															"'".$accionEspecifica."',".
															"'".$fuenteFinanciamiento."',".
															"'".$dependencia."',".
															"'".$dependencia_elabora."',".
															"".$esta_id.",".
															"'".$usua_login."',".
															"'".$arreglo_partida."',".
															"'".$arreglo_primer_trimestre."',".
															"'".$arreglo_segundo_trimestre."',".
															"'".$arreglo_tercer_trimestre."',".
															"'".$arreglo_cuarto_trimestre."') AS resultado_set(TEXT)";
	
	$resultado_insert = pg_exec($conexion, $sql);
	$row = pg_fetch_array($resultado_insert,0);
	$codigo = $row[0]; 
	header("Location:detalle.php?codigo=".$codigo."&accion=generar",false);
}else{
	header("Location:ingresar.php?msg=".$error,false);
}
ob_end_flush();
pg_close($conexion);
?>