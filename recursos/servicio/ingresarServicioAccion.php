<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}
$tipoServicio = 3;
$part_id=trim($_POST['partida']);
$nombre=strtoupper(trim($_POST['nombre']));
//$descripcion=trim($_POST['descripcion']);

//VALIDAR QUE EL NOMBRE NO SE ENCUENTRE EN LA TABLA DE ITEMS EN UN SERVICIO
$sql="SELECT nombre FROM sai_item WHERE nombre = upper('".$nombre."') AND id_tipo = ".$tipoServicio;
$resultado=pg_query($conexion,$sql);
$numeroFilas = pg_numrows($resultado);
if($numeroFilas>0){
	header('Location:ingresarServicio.php?msg=0',false);
	ob_end_flush();
	exit;
}else{
	ob_end_flush();
	//Antes de insertar, buscamos el año de la partida seleccionada
	/*$sql = "select * from sai_seleccionar_campo('sai_partida','part_nombre','part_id='||'''$part_id''','',0) Resultado_set(part_nombre varchar)";
	$resultado=pg_query($conexion,$sql);
	$row_part=pg_fetch_array($resultado);
	
	//Antes de insertar, buscamos el año del presupuesto
	$sql = "select * from sai_seleccionar_campo('sai_presupuest','pres_anno','esta_id=23','',0) Resultado_set(pres_anno int2)";
	$resultado=pg_query($conexion,$sql);
	$row_ano=pg_fetch_array($resultado);*/
	
	//Buscamos el mayor del campo de id en la tabla sai_item
	$sql = "SELECT MAX(CAST(id AS integer)) as codigo FROM sai_item";
	$resultado=pg_query($conexion,$sql);
	if($row=pg_fetch_array($resultado)){  	 
	   $codi=$row['codigo'];
	   $codi_new=$codi+1;
	}
	//Ahora insertamos los campos en la tabla de sai_item
	$estadoActivo = 1;
	pg_exec($conexion,	"INSERT INTO sai_item ".
						"(id, nombre, esta_id, usua_login, id_tipo) ".
						"VALUES ('".$codi_new."',upper('".$nombre."'),".$estadoActivo.",'".$_SESSION['login']."',".$tipoServicio.")");
	
	/*//Insertamos los campos en la tabla de sai_item_partida
	pg_exec($conexion,	"INSERT INTO sai_item_partida ".
						"(id_item, pres_anno, part_id) ".
						"VALUES ('".$codi_new."',".$row_ano['pres_anno'].",'".$part_id."')");*/
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<title>.:SAI:EJECUCION DE INGRESAR SERVICIO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css"/>
<script language="JavaScript" src="../../js/funciones.js"></script>
</head>
<body class="normal">
<br/>
<table width="550" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray normalNegroNegrita">
		<td height="21" colspan="2">Registro del nuevo servicio</td>
	</tr>
	<tr>
		<td width="150" height="30">C&oacute;digo del Servicio:</td>
		<td width="400" height="30" class="normalNegro"><?= $codi_new?></td>
	</tr>
	<?php 
	/*
	<tr>
		<td height="30">C&oacute;digo de la Partida:</td>
		<td height="30" class="normalNegro"><?= $part_id?></td>
	</tr>
	<tr>
		<td height="30">Nombre de la Partida:</td>
		<td height="30" class="normalNegro"><?= $row_part['part_nombre']?></td>
	</tr>
	*/
	?>
	<tr>
		<td height="30">Nombre del Servicio:</td>
		<td height="30" class="normalNegro"><?= $nombre?></td>
	</tr>
	<tr>
		<td height="30">Estado del Recurso:</td>
		<td height="30" class="normalNegro">Activo</td>
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