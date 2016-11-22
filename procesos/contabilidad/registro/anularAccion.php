<?php 
ob_start();
require_once("../../../includes/conexion.php");

$id_registro = trim($_GET['idRegistro']);
$nro_documento =  trim($_GET['idDocumento']);
$fecha = date("Y/m/d H:i:s");

$sql =  "UPDATE registro_documento SET id_estado=2, fecha_anulacion='".$fecha."' where id_registro=".$id_registro;
$resultado = pg_exec($conexion ,$sql) or die("Error al intentar anular el registro del documento");
$mensaje = "El registro del documento ".$nro_documento." ha sido anulado";			
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::Anulaci&oacute;n Registro Documento</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="JavaScript" src="../../../js/funciones.js"> </script>
</head>
<body>
<br/>
<div class="normal" align="center"><?php echo $mensaje;?></div>
<br></br>
<table width="80%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray"> 
		<td colspan="2" class="normalNegroNegrita" align="center">ANULACI&Oacute;N REGISTRO DOCUMENTO</td>
	</tr>
<tr>
	<td class="normal"><strong>Fecha de la recepci&oacute;n en OP:</strong></td>
	<td class="normalNegro"><?php echo (date("d/m/Y H:i:s")); ?></td>
</tr>
<tr>
   	<td><div align="left" class="normal"><b>Nro. Documento:</b></div></td>
	<td class="normalNegro"><?php echo $nro_documento;?></td>
</tr>
<!-- <tr>
<td class="normal" align="left"><strong>Unidad/Dependencia:</strong></td>
<td class="normalNegro">
	<?php
	  //  $sql_str="SELECT depe_id,depe_nombrecort,depe_nombre FROM sai_dependenci WHERE depe_id=".$id_dependencia." order by depe_nombre";
	 //   $res_q=pg_exec($sql_str);
	 //   if ($depe_row=pg_fetch_array($res_q))		  
	//	echo (trim($depe_row['depe_id']).":".trim($depe_row['depe_nombre']));
	?>
</td>
</tr>
<tr>
	<td><div align="left" class="normal"><strong>Beneficiario:</strong></div></td>
	<td class="normalNegro"><?php echo $nombre_beneficiario;?>	</td></tr>
<tr>
   	<td><div align="left" class="normal"><b>Monto:</b></div></td>
	<td class="normalNegro"><?php echo $monto;?></td>
</tr>
<tr>
   	<td><div align="left" class="normal"><b>Nro Compromiso:</b></div></td>
	<td class="normalNegro"><?php echo $nro_compromiso;?></td>
</tr>	
<tr>
   	<td><div align="left" class="normal"><b>Observaciones:</b></div></td>
	<td class="normalNegro"><?php echo $observaciones;?></td>
</tr>	
<tr>
   	<td><div align="left" class="normal"><b>Firmas:</b></div></td>
	<td class="normalNegro">Director Ejecutivo:<?php echo $firma_de;?>&nbsp;&nbsp; Presidencia:<?php echo $firma_presidencia;?></td>
</tr>-->
</table>
</body>
</html>