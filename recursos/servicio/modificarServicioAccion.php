<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
require_once("../../includes/constantes.php");
require("../../includes/perfiles/constantesPerfiles.php");
if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}
$user_perfil_id = $_SESSION['user_perfil_id'];
$tipoServicio = 3;
$id=$_POST["id"];
$nombre=strtoupper(trim($_POST['nombre']));
//$descripcion=trim($_POST['descripcion']);
$estadoServicio=$_POST['estadoServicio'];

$opcion = ($_POST["opcion"] && $_POST["opcion"]!="")? $_POST["opcion"]:"2";
$codigo = ($_POST["codigo"] && $_POST["codigo"]!="")? $_POST["codigo"]:"";
$estado = ($_POST["estado"] && $_POST["estado"]!="" && $_POST["estado"]!="0")? $_POST["estado"]:"";
$partida = ($_POST["partida"] && $_POST["partida"]!="" && $_POST["partida"]!="0")? $_POST["partida"]:"";
$palabraClave = ($_POST["palabraClave"] && $_POST["palabraClave"]!="")? trim($_POST["palabraClave"]):"";

//VALIDAR QUE EL NOMBRE NO SE ENCUENTRE EN LA TABLA DE SERVICIOS
$sql="SELECT nombre FROM sai_item WHERE nombre = upper('".$nombre."') AND id_tipo = ".$tipoServicio." AND id <> '".$id."'";
$resultado=pg_query($conexion,$sql);
$numeroFilas = pg_numrows($resultado);
if($numeroFilas>0){
	header('Location:modificarServicio.php?id='.$id.'&opcion='.$opcion.'&codigo='.$codigo.'&estado='.$estado.'&partida='.$partida.'&palabraClave='.$palabraClave.'&msg=0',false);
	ob_end_flush();
	exit;
}else{
	ob_end_flush();
	
	if ( $user_perfil_id != PERFIL_ANALISTA_PRESUPUESTO && $user_perfil_id != PERFIL_JEFE_PRESUPUESTO ) {
		//Se modifica el servicio
		pg_exec($conexion,	"UPDATE sai_item ".
							"SET nombre = upper('".$nombre."'), esta_id = ".$estadoServicio." ".
							"WHERE id = '".$id."'");
		
		$sql=	"SELECT ".
					"sp.part_id, ".
					"sp.part_nombre ".
				"FROM sai_item_partida sip, sai_partida sp ".
				"WHERE ".
					"sip.id_item = ".$id." AND ".
					"sip.pres_anno = ".$_SESSION['an_o_presupuesto']." AND ".
					"sip.part_id = sp.part_id AND ".
					"sip.pres_anno = sp.pres_anno ";
		$resultado=pg_query($conexion,$sql);
		$row=pg_fetch_array($resultado);
		$part_nombre = $row["part_nombre"];
		$partida = $row["part_id"];
	} else {
		if ( $partida != '' ) {
			/*$sql=	"SELECT ".
						"sip.part_id ".
					"FROM sai_item_partida sip ".
					"WHERE ".
						"sip.id_item = '".$id."' AND ".
						"sip.pres_anno = ".$_SESSION['an_o_presupuesto'];
			$resultado=pg_query($conexion,$sql);*/
			
			pg_exec($conexion,	"DELETE FROM sai_item_partida ".
								"WHERE id_item = ".$id);
			
			/*if ( $row=pg_fetch_array($resultado) ) {
				pg_exec($conexion,	"UPDATE sai_item_partida ".
									"SET part_id = '".$partida."' ".
									"WHERE id_item = '".$id."' AND pres_anno = ".$_SESSION['an_o_presupuesto']);
			} else {*/
				pg_exec($conexion,	"INSERT INTO sai_item_partida ".
								"(id_item, pres_anno, part_id) ".
								"VALUES ('".$id."',".$_SESSION['an_o_presupuesto'].",'".$partida."')");	
			/*}*/
			
			$sql=	"SELECT ".
					"sp.part_nombre ".
				"FROM sai_partida sp ".
				"WHERE ".
					"sp.part_id = '".$partida."'AND ".
					"sp.pres_anno = ".$_SESSION['an_o_presupuesto'];
			$resultado=pg_query($conexion,$sql);
			$row=pg_fetch_array($resultado);
			$part_nombre = $row["part_nombre"];
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<title>.:SAFI:EJECUCION DE MODIFICAR SERVICIO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css"/>
<script language="JavaScript" src="../../js/funciones.js"></script>
<script>
function regresar(){
<?php
	if($opcion=="1"){
		echo "location.href='buscar.php?opcion=".$opcion."&codigo=".$codigo."';";
	}else{
		echo "location.href='buscar.php?opcion=".$opcion."&estado=".$estado."&partida=".$partida."&palabraClave=".$palabraClave."';";
	}
?>
}
</script>
</head>
<body class="normal">
<br/>
<table width="550" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray normalNegroNegrita">
		<td height="21" colspan="2">Registro del servicio modificado</td>
	</tr>
	<tr>
		<td width="150" height="30">C&oacute;digo del Servicio:</td>
		<td width="400" height="30" class="normalNegro"><?= $id?></td>
	</tr>
	<tr>
		<td height="30">C&oacute;digo de la Partida:</td>
		<td height="30" class="normalNegro"><?= $partida?></td>
	</tr>
	<tr>
		<td height="30">Nombre de la Partida:</td>
		<td height="30" class="normalNegro"><?= $part_nombre?></td>
	</tr>
	<tr>
		<td height="30">Nombre del Servicio:</td>
		<td height="30" class="normalNegro"><?= $nombre?></td>
	</tr>
	<tr>
		<td height="30">Estado del Recurso:</td>
		<td height="30" class="normalNegro"><?= (($estadoServicio==ESTADO_ACTIVO)?"Activo":"Inactivo")?></td>
	</tr>
	<tr>
		<td height="16" colspan="2" align="center">
			<br/>Registro generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("H:i:s")?><br/><br/>
			<input class="normalNegro" type="button" onclick="javascript:window.print()" value="Imprimir"/>
			<br/><br/>
		</td>
	</tr>
</table>
</body>
</html>
<?php
pg_close($conexion);
?>