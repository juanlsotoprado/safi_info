<?php 
ob_start();
session_start();
require("../../includes/conexion.php");
require("../../includes/constantes.php");
if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}
$id=$_GET["id"];
ob_end_flush();
if($id!=""){
	$sql=	"SELECT ".
				"si.nombre, ".
				"si.esta_id, ".
				"sp.part_id, ".
				"sp.part_nombre ".
			"FROM 	sai_item si
					LEFT OUTER JOIN sai_item_partida sip ON (si.id = sip.id_item)
					LEFT OUTER JOIN sai_partida sp ON (sip.part_id = sp.part_id AND sip.pres_anno = sp.pres_anno) ".
			"WHERE ".
				"si.id = '".$id."' ";
	
	$resultado=pg_query($conexion,$sql);
	if($row=pg_fetch_array($resultado)){
		$servi_nombre = $row["nombre"];
		$esta_id = $row["esta_id"];
		$part_id = $row["part_id"];
		$part_nombre = $row["part_nombre"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Detalle de Servicio</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css"/>
</head>
<body class="normal">
	<br/>
	<form name="form" action="" method="post">
		<table width="550" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray normalNegroNegrita">
				<td height="21" colspan="2">Detalle del servicio</td>
			</tr>
			<tr>
				<td width="150" height="30">C&oacute;digo del Servicio:</td>
				<td width="400" height="30" class="normalNegro"><?= $id?></td>
			</tr>
			<tr>
				<td height="30">C&oacute;digo de la Partida:</td>
				<td height="30" class="normalNegro"><?= $part_id?></td>
			</tr>
			<tr>
				<td height="30">Nombre de la Partida:</td>
				<td height="30" class="normalNegro"><?= $part_nombre?></td>
			</tr>
			<tr>
				<td height="30">Nombre del Servicio:</td>
				<td height="30" class="normalNegro"><?= $servi_nombre?></td>
			</tr>
			<!-- <tr class="normal">
				<td height="30" valign="middle"><div align="right" class="normalNegrita">Descripci&oacute;n del Servicio:</div></td>
				<td height="30" valign="middle"><?= $descripcion?></td>
			</tr> -->
			<tr> 
				<td height="30">Estado del Recurso:</td>
				<td height="30" class="normalNegro"><?= (($esta_id==ESTADO_ACTIVO)?"Activo":"Inactivo")?></td>
			</tr>
			<tr>
				<td height="16" colspan="2" align="center">
					<br/>Detalle Generado el D&iacute;a <?=date("d/m/y")?> a las <?=date("H:i:s")?><br/><br/>
					<input class="normalNegro" type="button" onclick="javascript:window.print()" value="Imprimir"/>
					<input class="normalNegro" type="button" onclick="javascript:window.close()" value="Cerrar"/>
					<br/><br/>
				</td>
			</tr>
		</table>
	</form>
</body>
</html>
<?php
	}
}
pg_close($conexion);
?>