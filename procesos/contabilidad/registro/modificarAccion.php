<?php 
ob_start();
require_once("../../../includes/conexion.php");

$id_registro = trim($_POST['idRegistro']);
$nro_documento =  trim($_POST['idDocumento']);
$responsable=explode(":",$_POST['beneficiario']);
$responsable_id= $responsable[0];
$responsable_nombre= $responsable[1];
$dependencia=explode(":",$_POST['dependencia']);
$dependencia_id= $dependencia[0];
$dependencia_nombre= $dependencia[1];
$fecha = date("Y/m/d H:i:s");

$sql =  "INSERT INTO registro_documento_responsable (
			id_registro,
			id_responsable,
			nombre_responsable, 
			id_dependencia, 
			nombre_dependencia, 
			fecha
		) VALUES (
			".$id_registro.",
			'".$responsable_id."',
			'".$responsable_nombre."',
			'".$dependencia_id."',
			'".$dependencia_nombre."', 
			'".$fecha."')";

$resultado = pg_exec($conexion ,$sql) or die("Error al intentar modificar el documento");
?>
<html>
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::Modificaci&oacute;n Registro Documento</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
<br/>
<table width="80%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray"> 
		<td colspan="2" class="normalNegroNegrita">Entrega documento</td>
	</tr>
<tr>
	<td class="normal"><strong>Dependencia:</strong></td>
	<td class="normalNegro"><?php echo $dependencia_nombre; ?></td>
</tr>
<tr>
	<td class="normal"><strong>Responsable:</strong></td>
	<td class="normalNegro"><?php echo $responsable_nombre; ?></td>
</tr>	
<tr>
	<td class="normal"><strong>Fecha recepci&oacute;n:</strong></td>
	<td class="normalNegro"><?php echo (date("d/m/Y H:i:s")); ?></td>
</tr>
<tr>
   	<td><div align="left" class="normal"><b>Nro. Documento:</b></div></td>
	<td class="normalNegro"><?php echo $nro_documento;?></td>
</tr>
</table>
</body>
</html>