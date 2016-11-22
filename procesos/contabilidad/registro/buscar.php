<?php 
	ob_start();
	require_once("../../../includes/conexion.php");
	if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
		header('Location:../../../index.php',false);
		ob_end_flush();
		exit;
	}
	ob_end_flush();	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::Buscar Documento</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="JavaScript" src="../../../js/funciones.js"> </script>
<script language="JavaScript" src="../../../js/lib/actb.js"></script>
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('yyyy-mm-dd');</script>

<script language="JavaScript" type="text/JavaScript">

function buscar() {
	var mensaje = "";
	if(document.form1.documento.value=="" && document.form1.tipoDocumento.value=="-1" && document.form1.dependencia.value=="" && document.form1.beneficiario.value && document.form1.monto.value && document.form1.compromiso.value) {	
		alert("Debe especificar al menos un criterio de B\u00FAsqueda");
		return;
	}
	else {
		document.form1.validar.value=1;
		document.form1.submit()
	}	
}
</script>
</head>
<body>
<br/>
<form name="form1" method="post" action="buscar.php" id="form1" enctype="multipart/form-data" >
<table align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray"> 
		<td colspan="2" class="normalNegroNegrita">Buscar documento</td>
	</tr>
<tr>
	<td><div align="left" class="normal"><b>Elaborado Por:</b></div></td>
	<td class="normalNegro"><?php echo $_SESSION['solicitante'];?></td>
</tr>
<tr>
	<td class="normal" align="left"><strong>Tipo de documento: </strong></td>
	<td class="normalNegro">
		<select name="tipoDocumento" class="normalNegro">
			<option value="-1">Seleccione</option>
			<option value="pcta">Punto de cuenta (pcta)</option>
			<option value="memr">Memorando (memr)</option>
			<option value="vnac">Vi&aacute;tico Nacional (vnac)</option>
			<option value="viin">Vi&aacute;tico Internacional</option>
			<option value="ordc">Orden de compra (ordc)</option>
			<option value="otro">Otro</option>
		</select>
	</td>
</tr>
<tr>
   	<td><div align="left" class="normal"><b>Fecha de recepci&oacute;n/fecha del documento:</b></div></td>
	<td width="80%" class="normalNegro">
	
								<input type="text" size="10" id="fechaInicio" name="fechaInicio" class="dateparse" readonly="readonly"/>
							<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaInicio');" title="Show popup calendar">
								<img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
							</a>
							<input type="text" size="10" id="fechaFin" name="fechaFin" class="dateparse" readonly="readonly"/>
							<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaFin');" title="Show popup calendar">
								<img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
							</a>							
	
	<!-- 	<input id="fechaDocumento" class="normalNegro" size="50" maxlength="200" name="fechaDocumento"/> -->
	</td>
</tr>
<tr>
   	<td><div align="left" class="normal"><b>Nro. Documento:</b></div></td>
	<td><input type="text" name="documento" class="normalNegro" value="" id="documento"/></td>
</tr>
<tr>
<td class="normal" align="left"><strong>Unidad/Dependencia:</strong></td>
<td>
	<?php
	    $sql_str="SELECT depe_id,
	    				depe_nombrecort,
	    				depe_nombre
	    		 FROM sai_dependenci
	    		 WHERE depe_nivel = '4' or depe_nivel='3' 
	    		 ORDER BY depe_nombre";
	    $res_q=pg_exec($sql_str);		  
	?>
        <select name="dependencia" class="normalNegro" id="dependencia">
    	    <option value="-1">Seleccione...</option>
	    	<?php while($depe_row=pg_fetch_array($res_q)){ ?>
             <option value="<?php echo(trim($depe_row['depe_id'])); ?>"><?php echo(trim($depe_row['depe_nombre'])); ?></option>
        	<?php }?>
        </select>		   
</td>
</tr>
<tr>
	<td><div align="left" class="normal"><strong>Beneficiario:</strong></div></td>
	<td><input type="text" name="beneficiario" id="beneficiario" class="normalNegro" size="70"/>
	</td></tr>
<tr>
   	<td><div align="left" class="normal"><b>Nro Compromiso:</b></div></td>
	<td>
	<input type="text" name="compromiso" class="normalNegro" value="comp-" id="compromiso"/>
	<input type="hidden" name="validar" value="0" id="validar"/>
	</td>
</tr>	
<tr align="center">
		<td colspan="2" class="normal" align="center"><br></br>
		<input type="button" value="Buscar" onclick="javascript:buscar();"/></td>
</tr>
</table>
</form>
<?php 
$validar = $_POST["validar"];
if ($validar ==1) {
	$fecha_inicial=$_POST['fechaInicio']." 00:00:00";
	$fecha_final=$_POST['fechaFin']." 23:59:59";;	
	$tipo_documento = trim($_POST['tipoDocumento']);
	if (strlen($tipo_documento)>2) $condicion_tipo_documento = " AND r.tipo_documento='".$tipo_documento."'";
	$nro_documento =  trim($_POST['documento']);
	if (strlen($nro_documento)>2) $condicion_nro_documento = " AND r.nro_documento='".$nro_documento."'";	
	$id_dependencia = trim($_POST['dependencia']);
	if ($id_dependencia>-1) $condicion_id_dependencia = " AND r.id_dependencia=".$id_dependencia;	
	$beneficiario=$_POST['beneficiario'];
	if (strlen($beneficiario)>2) $condicion_nombre_beneficiario = " AND (UPPER(rdb.nombre_beneficiario) LIKE '%".strtoupper($beneficiario)."%' OR rdb.id_beneficiario='".$beneficiario."')";
	$nro_compromiso = trim($_POST['compromiso']);
	if (strlen($nro_compromiso)>6) $condicion_compromiso = " AND r.nro_compromiso='".$nro_compromiso."'";
	$observaciones = trim($_POST['observaciones']);	
	if (strlen($observaciones)>2) $condicion_observaciones = " AND upper(r.observaciones) LIKE '%".strtoupper($observaciones)."%'";
	if (strlen($fecha_inicial)>2) $condicion_fecha = " AND (fecha_recepcion BETWEEN TO_TIMESTAMP('".$fecha_inicial."', 'YYYY-MM-DD HH:MI:SS') AND TO_TIMESTAMP('".$fecha_final."', 'YYYY-MM-DD HH:MI:SS') OR fecha_documento BETWEEN '".$fecha_inicial."' AND '".$fecha_final."')";
		



$sql= "SELECT 
		r.tipo_documento AS tipo_documento,
 		r.monto AS monto,
		TO_CHAR(r.fecha_documento, 'DD/MM/YYYY') AS fecha_documento, 
		r.nro_documento AS nro_documento,
		r.firma_de AS firma_de,
		r.firma_presidencia AS firma_presidencia,
		r.id_registro AS id_registro,
		r.nro_compromiso, 
		d.depe_nombre AS dependencia,
		TO_CHAR(r.fecha_anulacion, 'DD/MM/YYYY') AS fecha_anulacion,
		r.nro_compromiso AS nro_comp,
		array_to_string(array(SELECT 'Trab: '|| nombre_responsable || '. Dep: '||nombre_dependencia ||'. Fecha:'|| TO_CHAR(fecha, 'DD/MM/YYYY') FROM registro_documento_responsable rr WHERE rr.id_registro=r.id_registro),', ') AS responsables,
		array_to_string(array(SELECT id_beneficiario || ':' || nombre_beneficiario FROM registro_documento_beneficiario rb WHERE rb.id_registro=r.id_registro ),', ') AS beneficiarios
	FROM registro_documento r
	INNER JOIN sai_dependenci d ON (d.depe_id::integer=r.id_dependencia)
	LEFT OUTER JOIN registro_documento_beneficiario rdb ON (rdb.id_registro = r.id_registro)
	WHERE r.user_depe='".$_SESSION['user_depe_id']."'" .$condicion_tipo_documento.$condicion_nro_documento.$condicion_id_dependencia.
			$condicion_nombre_beneficiario.$condicion_compromiso.$condicion_observaciones.$condicion_fecha;

	$resultado=pg_query($conexion,$sql) or die("Error al consultar el registro de documentos");	
	$total_busq=pg_num_rows($resultado);
	if ($total_busq>0) {	?>
	<br></br>
		<table width="100%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray" align="center">
			<td colspan="12" class="normalNegroNegrita">Proceso administrativo</td>
			</tr>
			<tr class="td_gray">
				<td class="normalNegroNegrita" align="center">Tipo  </td>
				<td class="normalNegroNegrita" align="center">Monto </td>				
				<td class="normalNegroNegrita" align="center">Beneficiario(s) </td>
				<td class="normalNegroNegrita" align="center">Fecha documento</td>
				<td class="normalNegroNegrita" align="center">C&oacute;digo doc</td>
				<td class="normalNegroNegrita" align="center">Dependencia</td>
				<td class="normalNegroNegrita" align="center">Firma DE</td>								
				<td class="normalNegroNegrita" align="center">Firma Presidencia</td>				
				<td class="normalNegroNegrita" align="center">Nro compromiso</td>				
				<td class="normalNegroNegrita" align="center">N&uacute;mero</td>
				<td class="normalNegroNegrita" align="center">Entregas</td>
				<td class="normalNegroNegrita" align="center">Opciones</td>				
             </tr>
                <?php while ($row=pg_fetch_array($resultado)) { ?>
                <tr class="normal">
                <td><?php echo $row["tipo_documento"];?></td>	
                <td><?php echo number_format($row["monto"],2,',','.');?></td>                
                <td><?php echo $row["beneficiarios"];?></td>
                <td><?php echo $row["fecha_documento"];?></td>
                <td><?php echo $row["nro_documento"];?></td>
                <td><?php echo $row["dependencia"];?></td>
                <td><?php echo $row["firma_de"];?></td>                                                                                                
                <td><?php echo $row["firma_presidencia"];?></td>
                <td><?php echo $row["nro_compromiso"];?></td>
                <td><?php echo $row["id_registro"];?></td>     
                <td><?php echo $row["responsables"];?></td>
                <td><a href="modificar.php?idRegistro=<?php echo $row["id_registro"];?>&idDocumento=<?php echo $row["nro_documento"];?>&fechaRecepcion=<?php echo $row["fecha_recepcion"];?>&tipoDocumento=<?php echo $row["tipo_documento"];?>&dependencia=<?php echo $row["dependencia"];?>&beneficiario=<?php echo $row["beneficiario"];?>&monto=<?php echo $row["monto"];?>&compromiso=<?php echo $row["nro_compromiso"];?>&observaciones=<?php echo $row["observaciones"];?>">Entregar</a>&nbsp;<a href="anularAccion.php?idRegistro=<?php echo $row["id_registro"];?>&idDocumento=<?php echo $row["nro_documento"];?>">Anular</a></td>                                                                                  
				</tr>
				<?php } ?>
			</table>	
<?php 
		} 	
}
?>
</body>
</html>