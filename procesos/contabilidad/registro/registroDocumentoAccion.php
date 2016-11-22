<?php 
ob_start();
require_once("../../../includes/conexion.php");
if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:../../../index.php',false);
	ob_end_flush();
	exit;
}
$elaborado_por = $_SESSION['login'];
$tipo_documento = trim($_POST['tipoDocumento']);
$nro_documento =  trim($_POST['idDocumento']);
$fecha_documento =  explode("-",$_POST['fechaDocumento']);
$dia_documento=$fecha_documento[0];
$mes_documento=$fecha_documento[1];
$ano_documento=$fecha_documento[2];
$fecha_documento2 = $ano_documento.'/'.$mes_documento."/".$dia_documento;
$id_dependencia = trim($_POST['dependencia']);
$beneficiario_ci=explode(",",$_POST['hid_bene_ci_rif']);
$beneficiario_nombre=explode("*",$_POST['hid_beneficiario']);
$monto = trim($_POST['monto']);
$nro_compromiso = trim($_POST['compromiso']);
$observaciones = trim($_POST['observaciones']);
if (trim($_POST['firma_de'])=="on") $firma_de="S";
else $firma_de="N";
if (trim($_POST['firma_presidencia'])=="on") $firma_presidencia="S";
else $firma_presidencia="N";
$fecha = date("Y/m/d H:i:s");

$sql =  "INSERT INTO registro_documento	 (
			fecha_recepcion,
		  	elaborado_por,
		  	tipo_documento,
		  	nro_documento,
		  	id_dependencia,
		  	monto,
		  	nro_compromiso,
		  	observaciones,
		  	firma_de,
		  	firma_presidencia,
		  	id_estado,
		  	fecha_documento,
		  	user_depe
		  )
		VALUES (
			'".$fecha."',
			'".$elaborado_por."',
			'".$tipo_documento."',
			'".$nro_documento."',
			".$id_dependencia.",
			".$monto.",
			'".$nro_compromiso."',
			'".$observaciones."',
			'".$firma_de."',
			'".$firma_presidencia."'
			,1,
			'".$fecha_documento2."',
			'".$_SESSION['user_depe_id']."')";
$resultado = pg_exec($conexion ,$sql) or die("Error al intentar registrar el documento");
$mensaje = "El documento ".$numero_documento." ha sido registrado";

$sql =  "SELECT MAX(id_registro) AS id 
		FROM registro_documento";
$resultado = pg_exec($conexion ,$sql) or die("Error al intentar conseguir el máximo id del documento");
if ($row=pg_fetch_array($resultado)) $maximo = $row['id'];

for ($i=0;$i<count($beneficiario_ci);$i++) {
	$sql =  "INSERT INTO registro_documento_beneficiario (
				id_registro,
				id_beneficiario,
				nombre_beneficiario
			)
			VALUES (
				".$maximo.",
				'".$beneficiario_ci[$i]."',
				'".$beneficiario_nombre[$i]."')";
	$resultado = pg_exec($conexion ,$sql) or die("Error al intentar registrar los beneficiarios");
}

?>
<html>
<head>
<title>.:SAFI::Registro Documento</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
</head>
<body>
<br/>
<table width="80%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
		<td colspan="2" class="normalNegroNegrita">Registro documento</td>
	</tr>
<tr>
	<td class="normal"><strong>Fecha de la recepci&oacute;n:</strong></td>
	<td class="normalNegro"><?php echo (date("d/m/Y H:i:s")); ?></td>
</tr>
<tr>
	<td><div align="left" class="normal"><b>Elaborado por:</b></div></td>
	<td class="normalNegro"><?php echo $_SESSION['solicitante'];?></td>
</tr>
<tr>
	<td class="normal" align="left"><strong>Tipo de documento: </strong></td>
	<td class="normalNegro"><?php echo $tipo_documento;?></td>
</tr>
<tr>
   	<td><div align="left" class="normal"><b>Nro. Documento:</b></div></td>
	<td class="normalNegro"><?php echo $nro_documento;?></td>
</tr>
<tr>
   	<td><div align="left" class="normal"><b>Fecha documento:</b></div></td>
	<td width="80%" class="normalNegro"><?php echo $dia_documento."-".$mes_documento."-".$ano_documento;?></td>
</tr>
<tr>
<td class="normal" align="left"><strong>Dependencia:</strong></td>
<td class="normalNegro">
	<?php
	    $sql_str="SELECT depe_id,
	    			depe_nombrecort,
	    			depe_nombre
	    		FROM sai_dependenci
	    		WHERE depe_id=".$id_dependencia."
	    		ORDER BY depe_nombre";
	    $res_q=pg_exec($sql_str);
	    if ($depe_row=pg_fetch_array($res_q))		  
		echo (trim($depe_row['depe_id']).":".trim($depe_row['depe_nombre']));
	?>
</td>
</tr>
<tr>
	<td><div align="left" class="normal"><strong>Beneficiario:</strong></div></td>
	<td class="normalNegro">
	<?php 
	for ($i=0;$i<count($beneficiario_ci);$i++) {
		echo $beneficiario_ci[$i].":".$beneficiario_nombre[$i].",  ";
	}	
	
	?>	</td></tr>
<tr>
   	<td><div align="left" class="normal"><b>Monto:</b></div></td>
	<td class="normalNegro"><?php echo $monto;?></td>
</tr>
<tr>
   	<td><div align="left" class="normal"><b>Nro. Compromiso:</b></div></td>
	<td class="normalNegro"><?php echo $nro_compromiso;?></td>
</tr>	
<tr>
   	<td><div align="left" class="normal"><b>Observaciones:</b></div></td>
	<td class="normalNegro"><?php echo $observaciones;?></td>
</tr>	
<tr>
   	<td><div align="left" class="normal"><b>Firmas:</b></div></td>
	<td class="normalNegro">Director Ejecutivo:<?php echo $firma_de;?>&nbsp;&nbsp; Presidencia:<?php echo $firma_presidencia;?></td>
</tr>
</table>
</body>
</html>