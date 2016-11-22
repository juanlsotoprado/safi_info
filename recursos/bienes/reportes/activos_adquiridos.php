<?php 
ob_start();
session_start();

require_once(dirname(__FILE__) . '/../../../init.php');
require_once(SAFI_INCLUDE_PATH . '/conexion.php');
require_once(SAFI_MODELO_PATH . '/item.php');
require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');
	 
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../../../index.php',false);
	ob_end_flush();   exit;
}
ob_end_flush();

$tipoMovimiento = trim($_POST['tipo_fec']); // Entrada/Salida
$fecha_in = trim($_POST['txt_inicio']); 
$fecha_fi = trim($_POST['hid_hasta_itin']); 
$fecha_ini = substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
$fecha_fin = substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
$depe = trim($_POST['opt_depe']);
$marca = trim($_POST['marca']);
$descripcion = trim($_POST['descripcion']);
$tipo_bien = trim($_POST['tp_bien']);
$tipoReporte = trim($_POST['tp_reporte']);
$paramIdEstatus = trim($_POST['estatus']);
$paramIdSerialBienNacional = trim($_POST['sbn']);
$paramIdSerialBien = trim($_POST['serial']);

// Obtener los activos
$query = "
	SELECT
		item.id AS id_item,
		item.nombre AS nombre_item
	FROM
		sai_item item
	WHERE
		item.id_tipo = " .EntidadItem::TIPO_BIEN. "
		AND item.esta_id = 1
	ORDER BY
		item.nombre
";

$resultado = pg_query($conexion, $query);
$bienes = array();

if($resultado === false){
	echo "Error al realizar la consulta de los bienes.";
	error_log("Error al realizar la consulta de de los bines. Detalles: " . pg_last_error());
} else {
	while($row = pg_fetch_array($resultado))
	{
		$bienes[$row['id_item']] = $row;
	}
}

// Obtener las marcas

$query = "
	SELECT
		marca.bmarc_id AS id_marca,
		marca.bmarc_nombre AS nombre_marca
	FROM
		sai_bien_marca marca
	ORDER BY
		marca.bmarc_nombre
";

$resultado = pg_query($conexion, $query);
$marcas = array();

if($resultado === false){
	echo "Error al realizar la consulta de las marcas.";
	error_log("Error al realizar la consulta de de las marcas. Detalles: " . pg_last_error());
} else {
	while($row = pg_fetch_array($resultado))
	{
		$marcas[$row['id_marca']] = $row;
	}
}

// Obtener las posibles dependencias solicitantes de la compra

$query = "
	SELECT
		dependencia.depe_id AS id_dependencia,
		dependencia.depe_nombre AS nombre_dependencia
	FROM
		sai_dependenci dependencia
	WHERE
		dependencia.depe_nivel = 4
		OR dependencia.depe_id = 150
	ORDER BY
		dependencia.depe_nombre
";	

$resultado = pg_query($conexion, $query);
$dependenciaSolicitantes = array();

if($resultado === false){
	echo "Error al realizar la consulta de las dependencias solicitantes de la compra.";
	error_log("Error al realizar la consulta de de las dependencias solicitantes de la compra. Detalles: " . pg_last_error());
} else {
	while($row = pg_fetch_array($resultado))
	{
		$dependenciaSolicitantes[$row['id_dependencia']] = $row;
	}
}

// Obtener las categorías de los materiales
$sql = "
	SELECT
		categoria.id AS id_categoria,
		categoria.nombre AS nombre_categoria
	FROM
		bien_categoria categoria
	ORDER BY
		categoria.nombre
";

$resultado = pg_query($conexion, $sql);
$categorias = array();

if($resultado === false){
	echo "Error al realizar la consulta de las categor&iacute;as de los bienes.";
	error_log("Error al realizar la consulta de de las categorías de los bienes. Detalles: " . pg_last_error());
} else {
	while($row = pg_fetch_array($resultado))
	{
		$categorias[$row['id_categoria']] = $row;
	}
}

// Obtener los estatus
$query = "
	SELECT DISTINCT
		estatus.esta_id AS id_estatus,
		estatus.esta_nombre as nombre_estatus
	FROM
		sai_biin_items item_particular
		INNER JOIN sai_estado estatus ON (estatus.esta_id = item_particular.esta_id)
	ORDER BY
		estatus.esta_nombre
"; 

$resultado = pg_query($conexion, $query);
$estatus = array();

if($resultado === false){
	echo "Error al realizar la consulta de los estatus.";
	error_log("Error al realizar la consulta de los estatus. Detalles: " . pg_last_error());
} else {
	while($row = pg_fetch_array($resultado))
	{
		$estatus[$row['id_estatus']] = $row;
	}
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Reporte de Movimientos de Material</title>

		<link type="text/css" href="../../../css/plantilla.css" rel="stylesheet" />
		<link type="text/css" href="../../../css/safi0.2.css" rel="stylesheet" />
		<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" rel="stylesheet"/>
		
		<script type="text/javascript" src="../../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript" src="../../../js/funciones.js"></script>
		<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript">
	
			g_Calendar.setDateFormat('dd/mm/yyyy');

			$().ready(function()
			{
				
				$('a[name="displayReporte"]').each(function(index, objA){
					location.href = "#displayReporteAntes";
				});
			});
			
			function habiDesabiOpciones()
			{	 
				tipoReporte = document.form.tp_reporte[0].checked;
				estatus_activo=document.getElementById("estatus");
				if (tipoReporte==true){ 
					estatus_activo.disabled=true;
				}else{ 
					estatus_activo.disabled=false;
				}
			}
	
			function detalle(codigo1,codigo2,tp,tp_arti,depe,tp_fec)
			{
			    url = "alma_rep_e3.php?codigo1="+codigo1+"&codigo2="+codigo2+"&tp_mov="+tp+"&tp_arti="
			    	+tp_arti+"&depe="+depe+"&tipo_f="+tp_fec;
				newwindow = window.open(url,'name','height=500,width=700,scrollbars=yes');
				if (window.focus) {newwindow.focus();}
			}
			function ejecutar()
			{
			   
				if ((document.form.txt_inicio.value=='') && (document.form.hid_hasta_itin.value=='') 
					&& (document.form.opt_depe.value=='0')  && (document.form.marca.value=='0') 
					&& (document.form.descripcion.value=='0') && (document.form.tp_bien.value=='0')&& 
					(document.form.estatus.value=='0') && (document.form.sbn.value=='') && (document.form.serial.value=='')
				){
					alert("Debe seleccionar un criterio de b\u00fasqueda");
					return;
				}
			
				document.form.hid_buscar.value=2;
				document.form.submit();
			}
			
			function comparar_fechas(fecha_inicial,fecha_final) //Formato dd/mm/yyyy
			{ 	
				var fecha_inicial=document.form.txt_inicio.value;
				var fecha_final=document.form.hid_hasta_itin.value;
				
				var dia1 =fecha_inicial.substring(0,2);
				var mes1 =fecha_inicial.substring(3,5);
				var anio1=fecha_inicial.substring(6,10);
				
				var dia2 =fecha_final.substring(0,2);
				var mes2 =fecha_final.substring(3,5);
				var anio2=fecha_final.substring(6,10);
			
				dia1 = parseInt(dia1,10);
				mes1 = parseInt(mes1,10);
				anio1= parseInt(anio1,10);
				
				dia2 = parseInt(dia2,10);
				mes2 = parseInt(mes2,10);
				anio2= parseInt(anio2,10); 
				
				if ( (anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2)) )
				{
					alert("La fecha inicial no debe se mayor a la fecha final"); 
					document.form.hid_hasta_itin.value='';
					return;
				}
			}

			function limpiarFormulario()
			{
				$('input[type="radio"][name="tipo_fec"][value="0"]').each(function(index, objRadio){
					objRadio.checked = true;
				});
				$('#txt_inicio').val('');
				$('#hid_hasta_itin').val('');
				$('#descripcion').val('0');
				$('#marca :nth-child(1)').attr('selected', 'selected');
				$('#opt_depe').val('0');
				$('#tp_bien').val('0');
				$('#estatus').val('0');
				$('#sbn').val('');
				$('#serial').val('');
				$('input[type="radio"][name="tp_reporte"][value="2"]').each(function(index, objRadio){
					objRadio.checked = true;
				});
				
			}
		</script>
	</head>
	
	<body>
	
		<form name="form" action="activos_adquiridos.php" method="post">
  
			<table width="700" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
				<tr class="td_gray" > 
					<td height="15" colspan="3" valign="middle" class="normalNegroNegrita">Movimientos</td>
				</tr>
				<tr>
					<td height="34"  class="normalNegrita">Tipo de movimiento</td>
					<td class="normal">
						<input type="radio" name="tipo_fec" value="0"
							<?php echo ($tipoMovimiento == null || $tipoMovimiento == "" || $tipoMovimiento != "1")
							? ' checked="checked"' : "" ?>
						/>Entrada&nbsp;
						<input type="radio" name="tipo_fec" value="1"
							<?php echo ($tipoMovimiento != null && $tipoMovimiento != "" && $tipoMovimiento == "1")
							? ' checked="checked"' : "" ?>
						/>Salida
					</td>
				</tr>
				<tr>
					<td height="34" class="normalNegrita">Fecha:</td>
					<td width="406" class="normalNegrita">
						<!-- Agregar los accesos rapidos de las fechas (Hoy, ayer, semana, semana pasada, etc.) -->
						<?php VistaFechas::ConstruirAccesosRapidosFechas("txt_inicio", "hid_hasta_itin", "dd/mm/yy") ?>
						<input name="txt_inicio" id="txt_inicio" type="text" size="10" class="normalNegro"
							onclick="cal1xx3.select(this,'anchor1xx3','dd/MM/yyyy'); return false;" readonly="readonly"
							value="<?php echo $fecha_in == null || $fecha_in == "" ? "" : $fecha_in ?>"
						/>
						<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');"
						><img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a>
						&nbsp;
						<input name="hid_hasta_itin" id="hid_hasta_itin" type="text" size="10" class="normalNegro"
							onclick="cal1xx4.select(this,'anchor1xx4','dd/MM/yyyy'); return false;"
							onfocus="javascript: comparar_fechas(document.form.txt_inicio,document.form.hid_hasta_itin);"
							readonly="readonly" value="<?php echo $fecha_fi == null || $fecha_fi == "" ? "" : $fecha_fi ?>"
						/>
						<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');"
						><img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a>
					</td>
				</tr>
				<tr>
					<td class="normalNegrita">Activo:</td>
					<td>
						<select name="descripcion" class="normalNegro" id="descripcion">
							<option value="0">[Seleccione]</option>
							<?php
								foreach ($bienes AS $row)
								{
									echo '
							<option value="'.$row['id_item'].'"
								'.($descripcion != null && $descripcion != "" && $row['id_item'] == $descripcion
								? ' selected="selected"' : "").'
							>
								'. mb_strtoupper($row['nombre_item'], 'ISO-8859-1').'
							</option>
									';
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="normalNegrita">Marca:</td>
					<td>
						<select name="marca" class="normalNegro" id="marca">
							<option value="0">[Seleccione]</option>
							<?php
								foreach ($marcas AS $row)
								{
									echo '
							<option value="'.$row['id_marca'].'">
								'. $row['nombre_marca'].'
							</option>
									';
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td height="34" class="normalNegrita">Dependencia solicitante de la compra:</td>
					<td>
						<select name="opt_depe" class="normalNegro" id="opt_depe">
							<option value="0">[Seleccione]</option>
							<?php
								foreach ($dependenciaSolicitantes AS $row)
								{
									echo '
							<option value="'.$row['id_dependencia'].'"
								'.($depe != null && $depe != "" && $row['id_dependencia'] == $depe
								? ' selected="selected"' : "").'
							>
								'. $row['nombre_dependencia'].'
							</option>
									';
								}
							?>
						</select>
						<input type="hidden" name="hid_buscar" id="hid_buscar" value="0" />
					</td>
				
				</tr>
				<tr>
					<td height="34" class="normalNegrita">Categor&iacute;a:</td>
					<td>
						<select name="tp_bien" id="tp_bien"  class="normalNegro">
							<option value="0">[Seleccione]</option>
							<?php
								foreach ($categorias AS $row)
								{
									echo '
							<option value="'.$row['id_categoria'].'"
								'.($tipo_bien != null && $tipo_bien != "" && $row['id_categoria'] == $tipo_bien
								? ' selected="selected"' : "").'
							>
								'. $row['nombre_categoria'].'
							</option>
									';
								}
							?>
						</select>
  
					</td>
				</tr>
				<tr>
					<td height="34" class="normalNegrita">Estatus:</td>
					<td width="406" class="normalNegrita">
						<select name="estatus" id="estatus"  class="normalNegro">
							<option value="0">[Seleccione]</option>
							<?php
								foreach ($estatus AS $row)
								{
									echo '
							<option value="'.$row['id_estatus'].'"
								'.($paramIdEstatus != null && $paramIdEstatus != "" && $row['id_estatus'] == $paramIdEstatus
								? ' selected="selected"' : "").'
							>
								'. $row['nombre_estatus'].'
							</option>
									';
								}
							?>
								
						</select>
					</td>
				</tr>
				<tr>
					<td height="34" class="normalNegrita">Serial de Bien Nacional:</td>
					<td width="406" class="normalNegrita">
						<input type="text" name="sbn" id="sbn" size="10"
							value="<?php echo ($paramIdSerialBienNacional != null && $paramIdSerialBienNacional != ""
								? $paramIdSerialBienNacional : "") ?>"
						/>
					</td>
				</tr>
				<tr>
					<td height="34" class="normalNegrita">Serial del Activo:</td>
					<td width="406" class="normalNegrita">
						<input type="text" name="serial" id="serial" size="10"
							value="<?php echo ($paramIdSerialBien != null && $paramIdSerialBien != ""
								? $paramIdSerialBien : "") ?>"
						/>
					</td>
				</tr>
				<tr>
					<td height="34" class="normalNegrita"><a name="displayReporteAntes"></a>Tipo de Reporte:</td>
					<td width="406" class="normalNegrita">
						<input type="radio" name="tp_reporte" id="tp_reporte" value="1" onclick="habiDesabiOpciones();"
							<?php echo ($tipoReporte != null && $tipoReporte != "" && $tipoReporte == "1")
							? ' checked="checked"' : "" ?>
						/>
						General&nbsp;
						<input type="radio" name="tp_reporte" value="2" onclick="habiDesabiOpciones();"
							<?php echo ($tipoReporte == null || $tipoReporte == "" || $tipoReporte != "1")
							? ' checked="checked"' : "" ?>
						/>Detallado
					</td>
				</tr>
				<tr>
					<td height="52" colspan="3" align="center">
						<input type="button" value="Buscar" onclick="javascript:ejecutar()" class="normalNegro" />
						<input type="button" value="Limpiar" class="normalNegro" onclick="limpiarFormulario();"/>
					</td>
				</tr>
			</table>
		</form>
		<br />
		<?php


		if (($_POST['hid_buscar'])==1)
		{
			echo utf8_decode("<SCRIPT LANGUAGE='JavaScript'>"."alert ('Especifique un rango de fechas...');"."</SCRIPT>");
		}
		else if (($_POST['hid_buscar'])==2)
		{
			$seleccion='';
			$group='';
			$from1="";
			$wheretipo1="";
			$wheretipo2="";
			$wheretipo3="";
			$wheretipo4="";
			$wheretipo5="";
			$wheretipo6="";
			$wheretipo7="";
			$wheretipo8="";
			$wheretipo9="";
			$criterio2="";
			$criterio3="";
			$criterio4="";
			$criterio5="";
			
			if ($_POST['sbn']<>""){
				$wheretipo8 = " and etiqueta = '".$_POST['sbn']."' ";
			}
			
			if ($_POST['serial']<>""){
				$wheretipo9 = " and serial = '".$_POST['serial']."' ";
			}
			
			if (strlen($fecha_ini)>2) {
				if ($_POST['tipo_fec']==0){
					$wheretipo1 = " and fecha_entrada >= '".$fecha_ini."' and fecha_entrada <= '".$fecha_fin."' ";
					$group="fecha_entrada,";
				} else {
					$wheretipo1 = " and asbi_fecha >= '".$fecha_ini."' and asbi_fecha <= '".$fecha_fin."' ";
					$group="asbi_fecha,";
				}
			}
			
			if ($_POST['opt_depe']>0) {
				$wheretipo2 = " and depe_solicitante='".$depe."' ";
				$query_depe="SELECT depe_nombre FROM sai_dependenci WHERE depe_id='".$depe."'";
				$resultado_depe=pg_query($conexion,$query_depe);	
				if ($row=pg_fetch_array($resultado_depe)){
					$criterio2=" de la Dependencia ".$row['depe_nombre'];
				}
			}
			
			if ($_POST['marca']>0){ 
				$wheretipo3 = " and marca_id='".$marca."' ";
				$query="SELECT bmarc_nombre FROM sai_bien_marca WHERE bmarc_id='".$_POST['marca']."'";
				$resultado=pg_query($conexion,$query);	
				if ($row=pg_fetch_array($resultado)){
					$criterio3=" de la Marca ".$row['bmarc_nombre'];
				}
			}
			
			if ($_POST['descripcion']<>0) {
				$wheretipo4 = " and bien_id='".$descripcion."' ";
				$criterio4=" del Activo ".$descripcion;
			}
			
			if ($_POST['tp_bien']<>0){
				$from1=",sai_item_bien t5,bien_categoria t6 ";
				$wheretipo5 = " and t5.tipo=t6.id and t5.id=t1.bien_id and t6.id='".$tipo_bien."'";
				$query="SELECT nombre FROM bien_categoria WHERE id='".$tipo_bien."'";
				$resultado=pg_query($conexion,$query);	
				if ($row=pg_fetch_array($resultado)){
					$criterio5=" de la Clasificaci&oacute;n ".$row['nombre'];
				}
			}
			
			if ($_POST['estatus']>0) 
				$wheretipo7 = " and t1.esta_id='".$_POST['estatus']."' ";
				
			if (($_POST['tp_reporte'])==1){//GENERAL
				if ($_POST['tipo_fec']==0){ // Entrada
					$sql_tabla1 = "
						SELECT
							fecha_entrada as fecha,
							depe_solicitante as dependencia,
							t1.bien_id,
							precio as precio,
							count(t1.bien_id) as cantidad,
							t3.acta_id
						FROM
							sai_biin_items t1,
							sai_bien_inco t3
							".$from1."
						WHERE
							t3.acta_id = t1.acta_id
							".$wheretipo1."
							".$wheretipo2."
							".$wheretipo3."
							".$wheretipo4."
							".$wheretipo5."
							".$wheretipo8."
						group by
							".$group."
							depe_solicitante,
							t1.bien_id,
							fecha_entrada,
							precio,
							t3.acta_id
						order by
						1
					";
					
				} else { // Salida
					$sql_tabla1 = "
						SELECT
							asbi_fecha as fecha,
							solicitante as dependencia,
							t1.bien_id,
							precio as precio,
							count(t1.bien_id) as cantidad,
							t3.asbi_id as acta_id
						FROM
							sai_biin_items t1,
							sai_bien_asbi_item t2,
							sai_bien_asbi t3
							".$from1."
						WHERE
							t3.asbi_id = t2.asbi_id
							and t2.clave_bien = t1.clave_bien
							".$wheretipo1."
							".$wheretipo2."
							".$wheretipo3."
							".$wheretipo4."
							".$wheretipo5."
							".$wheretipo8."
							".$wheretipo9."
						group by
							".$group."
							solicitante,
							t1.bien_id,
							precio,
							t3.asbi_id
						order by
							1
					";	 	
				}
			} else {//DETALLADO
				
				if ($_POST['tipo_fec']==0){ // Entrada
					
					$sql_tabla1 = "
						SELECT
							fecha_entrada as fecha_e,
							0 as fecha_s,
							depe_solicitante as dependencia,
							t1.bien_id,
							t1.ubicacion,
							precio as precio,
							t1.esta_id,
							count(t1.bien_id) as cantidad,
							depe_nombre,
							modelo,
							etiqueta,
							serial,
							bmarc_nombre,
							t4.nombre as nombre_bien,
							esta_nombre,
							clave_bien,
							t1.acta_id as acta_e,
							0 as acta_s
						FROM
							sai_biin_items t1,
							sai_bien_inco t3,
							sai_bien_marca t2,
							sai_item t4,
							sai_estado t7,
							sai_dependenci t8
							".$from1."
						WHERE
							t8.depe_id = t3.depe_solicitante
							and t4.id = t1.bien_id
							and marca_id = bmarc_id
							and t3.acta_id = t1.acta_id
							and t7.esta_id = t1.esta_id
							".$wheretipo1."
							".$wheretipo2."
							".$wheretipo3."
							".$wheretipo4."
							".$wheretipo5."
							".$wheretipo7."
							".$wheretipo8."
							".$wheretipo9."
						group by
							".$group."
							depe_solicitante,
							t1.bien_id,
							fecha_entrada,
							t1.ubicacion,
							precio,
							t1.esta_id,
							modelo,
							etiqueta,
							serial,
							bmarc_nombre,
							t4.nombre,
							esta_nombre,
							clave_bien,
							t1.acta_id,
							depe_nombre
						ORDER BY
							t1.bien_id,
							etiqueta
					";
				} else { // Salida
					$sql_tabla1 = "
						SELECT
							fecha_registro as fecha_e,
							asbi_fecha as fecha_s,
							solicitante as dependencia,
							t1.bien_id,
							t1.ubicacion,
							precio as precio,
							t1.esta_id,
							count(t1.bien_id) as cantidad,
							depe_nombre,
							modelo,
							etiqueta,
							serial,
							bmarc_nombre,
							t4.nombre as nombre_bien,
							esta_nombre,
							t1.clave_bien,
							t10.acta_id as acta_e,
							t3.asbi_id as acta_s
						FROM
							sai_biin_items t1,
							sai_bien_asbi_item t9,
							sai_bien_asbi t3,
							sai_bien_marca t2,
							sai_item t4,
							sai_estado t7,
							sai_dependenci t8,
							sai_bien_inco t10
							".$from1."
						WHERE
							t10.acta_id = t1.acta_id
							and t8.depe_id = t3.solicitante
							and t4.id = t1.bien_id
							and marca_id = bmarc_id
							and t9.asbi_id = t3.asbi_id
							and t9.clave_bien = t1.clave_bien
							and t7.esta_id = t1.esta_id
							".$wheretipo1."
							".$wheretipo2."
							".$wheretipo3."
							".$wheretipo4."
							".$wheretipo5."
							".$wheretipo7."
							".$wheretipo8."
							".$wheretipo9."
						group by
							".$group."
							solicitante,
							t1.bien_id,
							asbi_fecha,
							t1.ubicacion,
							precio,
							t1.esta_id,
							modelo,
							etiqueta,
							serial,
							bmarc_nombre,
							t4.nombre,
							esta_nombre,
							t1.clave_bien,
							t3.asbi_id,
							depe_nombre,
							fecha_registro,
							t10.acta_id
						ORDER BY
							1,
							t1.bien_id,
							etiqueta
					";	   		
				}
			}
			
			$resultado_set_t1=pg_query($conexion,$sql_tabla1) or die("Error al mostrar consulta");	
			if (($rowt1=pg_fetch_array($resultado_set_t1)) == null)
			{
		?>
		<center>
  			<span style="color: #003399;" class="normalNegrita">
  				No existen registros que cumplan con el criterio de b&uacute;squeda seleccionado
  			</span>
		</center>
		<?php 
			} else {
		?>
		<a name="displayReporte"></a>
		<?php
				if (($_POST['tp_reporte'])==1) // General
				{
		?>
		<table width="678" background="../../../imagenes/fondo_tabla.gif" align="center" border="0" class="tablaalertas">
			<tr>
				<td class="normalNegroNegrita" style="text-align: center;">
					Movimiento de Activos del <?php echo $fecha_in ?> al <?php echo $fecha_fi ?> 
		  			<?php echo " " . $criterio2.$criterio3.$criterio4.$criterio5 ?>
				</td>
			</tr>
			<tr>
				<td height="49" colspan="7" align="center">
					<table width="729" border="0" class="tablaalertas">
						<tr class="td_gray">
							<td width="60"  align="center" class="normalNegroNegrita">Acta</td>
							<td width="203" align="center" class="normalNegroNegrita">Activo</td>
							<td width="59" align="center" class="normalNegroNegrita">
								Fecha <?php  if ($tipo_f=='true'){	?>adquisici&oacute;n <?php } else{?>ingreso<?php }?>
							</td>
							<td width="71" align="center" class="normalNegroNegrita">Cantidad</td>
							<td width="99" align="center" class="normalNegroNegrita">Valor unitario Bs. </td>
						</tr>
						<?php
					$resultado_set_t1=pg_query($conexion,$sql_tabla1) or die("Error al mostrar consulta en sai_articulo");
					$i=0;
					
					while ($rowt1=pg_fetch_array($resultado_set_t1))
					{
						$cantidad=$rowt1['cantidad'];
						$id=$rowt1['bien_id'];
						$monto=$rowt1['precio'];
						if (strlen($rowt1['fecha'])>4)
							$fec=substr($rowt1['fecha'],8,2).'/'.substr($rowt1['fecha'],5,2).'/'.substr($rowt1['fecha'],0,4);
						else
							$fec="--";
						?>
						<tr>
						<?
						$sql_d = "
							SELECT * FROM sai_seleccionar_campo('sai_item','id,nombre','id=''$id''','',1)
							resultado_set(id varchar,nombre varchar)
						"; 
						$resultado_set_most_d=pg_query($conexion,$sql_d) or die("Error al consultar lista de articulos");
						$rowd=pg_fetch_array($resultado_set_most_d);
						?>
							<td class="normal" style="text-align: left;">
								<a href="javascript:abrir_ventana('../inco_pdf.php?codigo=<?php echo $rowt1['acta_id']; ?>')"
									class="copyright"
								><?php echo trim($rowt1['acta_id']);?></a>
							</td>
							<td class="normal" style="text-align: left;"><?php echo strtoupper($rowd['nombre']);?></td>
							<td class="normal" style="text-align: center;"><?php echo $fec;?></td>
							<td class="normal" style="text-align: right;"><?php echo $cantidad; ?></td>
							<td class="normal" style="text-align: right;"><?php echo(number_format($monto, 2, ',', '.')); ?></td>
						</tr>        
						<?php
					}
						?>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="7" align="center" class="normal">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="7" align="center" class="normalNegrita">
					<br />
					<span class="normal">
						<span class="peq_naranja">Detalle generado  el d&iacute;a <?=date("d/m/y")?> a las <?=date("H:i:s")?></span>
	        			<br /><br />
						<a href="activos_adquiridos_pdf.php?txt_inicio=<?php echo $fecha_in;?>&hid_hasta_itin=<?php
							echo $fecha_fi?>&opt_depe=<?php echo $depe;?>&marca=<?php echo $marca;?>&descripcion=<?php
							echo $descripcion;?>&tp_bien=<?php echo $tipo_bien;?>&tp_reporte=<?php
							echo $_POST['tp_reporte'];?>&serial=<?php echo $_POST['serial']?>"
						><img src="../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0" /></a>
						<a href="activos_adquiridos_pdf_NUEVA_VERSION.php?txt_inicio=<?php
							echo $fecha_in;?>&hid_hasta_itin=<?php echo $fecha_fi?>&opt_depe=<?php
							echo $depe;?>&marca=<?php echo $marca;?>&descripcion=<?php
							echo $descripcion;?>&tp_bien=<?php echo $tipo_bien;?>&tp_reporte=<?php
							echo $_POST['tp_reporte'];?>&tipo_fec=<?=$_POST['tipo_fec']?>&estatus=<?=$_POST['estatus']?>&serial=<?php
							echo $_POST['serial']?>"
						><img src="../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0" /></a>
						<br />
						Imprimir Documento
						<br />
					</span>
				</td>
			</tr>
		</table>
		<?php
				} else { // Reporte Detallado
	   	?>
		<table width="678" background="../../../imagenes/fondo_tabla.gif" align="center" border="0" class="tablaalertas">
			<tr>
				<td class="normalNegroNegrita" style="text-align: center;">
					<?php
					if ($fecha_in == "")
					{
						echo "Movimientos de activos al " . $fecha_fi;
					} else {
						echo "Movimientos de activos del " . $fecha_in . " al " . $fecha_fi;
					}
					echo " " . $criterio2.$criterio3.$criterio4.$criterio5;
					?>
		  
				</td>
			</tr>
			<tr>
				<td height="49" colspan="7" align="center" class="normalNegrita">
					<table width="729" border="0" class="tablaalertas">
						<tr class="td_gray">
							<th width="42" align="center" class="normalNegroNegrita">#</th>
							<th width="203" align="center" class="normalNegroNegrita">Activo</th>
							<th width="86" align="center" class="normalNegroNegrita">Estatus</th>
							<th width="86" align="center" class="normalNegroNegrita">Acta entrada</th>
							<th width="86" align="center" class="normalNegroNegrita">Fecha de ingreso</th>
							<th width="86" align="center" class="normalNegroNegrita">Dependencia solicitante de la compra</th>
							<th width="86" align="center" class="normalNegroNegrita">Acta salida</th>
							<th width="86" align="center" class="normalNegroNegrita">Fecha de salida</th>
							<th width="59" align="center" class="normalNegroNegrita">Marca</th>
							<th width="115" align="center" class="normalNegroNegrita">Modelo</th>
							<th width="71" align="center" class="normalNegroNegrita">Serial activo</th>
							<th width="86" align="center" class="normalNegroNegrita">Serial Bien Nacional</th>
							<th width="142" align="center" class="normalNegroNegrita">Existencia torre </th>
							<th width="140" align="center" class="normalNegroNegrita">Existencia galp&oacute;n </th>
						</tr>
						<?php
					$resultado_set_t1=pg_query($conexion,$sql_tabla1) or die("Error al mostrar consulta en sai_articulo");
					$i=0;
					
					while ($rowt1=pg_fetch_array($resultado_set_t1))
					{
						$i++;
						$id_acta=$rowt1['acta_id'];
						
						$torre="-";
						$galpon="-";
						if ($rowt1['esta_id']=="41"){
							if ($rowt1['ubicacion']=="1")
								$torre="X";
							else if ($rowt1['ubicacion']=="2")
								$galpon="X";
						}
						?>
						<tr>
							<td class="normal" scope="row" style="text-align: center;"><?php echo $i;?></td>
							<td class="normal" style="text-align: left;"><?php echo strtoupper($rowt1['nombre_bien']);?></td>
							<td class="normal" style="text-align: left;"><?php echo trim($rowt1['esta_nombre']);?></td>
							<td class="normal" style="text-align: center;">
							<?php
	         			$id_acta = $rowt1['acta_e'];
	            		$fec = substr($rowt1['fecha_e'],8,2).'/'.substr($rowt1['fecha_e'],5,2).'/'.substr($rowt1['fecha_e'],0,4);
							?>
								<a href="javascript:abrir_ventana('../inco_pdf.php?codigo=<?php
									echo $id_acta; ?>')" class="copyright"
								>
									<?php echo $id_acta;?>
								</a>
							</td>
							<?php
						if ($_POST['tipo_fec']==0){
							if ($rowt1['esta_nombre']<>"Incorporado"){
								$sql_acta = "
									SELECT
										t1.asbi_id,
										asbi_fecha
									FROM
										sai_bien_asbi_item t1,
										sai_bien_asbi t2
									where
										t1.asbi_id = t2.asbi_id
										and t2.esta_id<>15
										and
										clave_bien = '".$rowt1['clave_bien']."'
								";
								
								$resultado_acta=pg_query($conexion,$sql_acta);
								
								if ($row_acta=pg_fetch_array($resultado_acta))
									$id_acta_salida=$row_acta['asbi_id'];
									
								$fecha_salida=$row_acta['asbi_fecha'];
								
								if (strlen($row_acta['asbi_fecha'])>4)
									$fecha_salida=substr($row_acta['asbi_fecha'],8,2).'/'.substr($row_acta['asbi_fecha'],5,2).'/'.substr($row_acta['asbi_fecha'],0,4);
								else
									$fecha_salida="";
							} else {
								$id_acta_salida="";
								$fecha_salida="";
							}
						} else {
								$fecha_salida=$fec=substr($rowt1['fecha_s'],8,2).'/'.substr($rowt1['fecha_s'],5,2)
									.'/'.substr($rowt1['fecha_s'],0,4);
								$id_acta_salida=$rowt1['acta_s'];
								
						}
							?>
							<td class="normal" style="text-align: center;"><?php echo $fec;?></td>
							<td class="normal" style="text-align: center;"><?php echo $rowt1['depe_nombre']; ?></td>
							<td class="normal" style="text-align: center;">
								<a href="javascript:abrir_ventana('../salida_activos_pdf.php?codigo=<?php
									echo $id_acta_salida; ?>&tipo=a')" class="copyright"
								>
									<?php echo $id_acta_salida; ?>
								</a>
							</td>
							<td class="normal" style="text-align: left;"><?php echo $fecha_salida; ?></td>
							<td class="normal" style="text-align: left;"><?php echo strtoupper($rowt1['bmarc_nombre']);?></td>
							<td height="24" class="normal" scope="row"><?php echo strtoupper($rowt1['modelo']);?></td>
							<td class="normal" style="text-align: left;"><?php echo $rowt1['serial']; ?></td>
							<td class="normal" style="text-align: center;"><?php echo $rowt1['etiqueta']; ?></td>
							<td align="center"class="normal" style="text-align: center;"><?php echo $torre; ?></td>
							<td align="center"class="normal"><?php echo $galpon; ?></td>	
						</tr>
						<?php
					}
						?>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="7" align="center" class="normal">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="7" align="center" class="normalNegrita">
				<br />
				<span class="normal">
					<span class="peq_naranja">Detalle generado  el d&iacute;a <?=date("d/m/y")?> a las <?=date("H:i:s")?></span>
					<br />
					<br />
					<br />
					<a href="activos_adquiridos_pdf.php?txt_inicio=<?php echo $fecha_in;?>&hid_hasta_itin=<?php
						echo $fecha_fi?>&opt_depe=<?php echo $depe;?>&marca=<?php echo $marca;?>&descripcion=<?php
						echo $descripcion;?>&tp_bien=<?php echo $tipo_bien;?>&tp_reporte=<?php
						echo $_POST['tp_reporte'];?>&tipo_fec=<?=$_POST['tipo_fec']?>&estatus=<?=$_POST['estatus']?>&serial=<?php
						echo $_POST['serial']?>"
					><img src="../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0" /></a>
					<a href="activos_adquiridos_pdf_NUEVA_VERSION.php?txt_inicio=<?php echo $fecha_in;?>&hid_hasta_itin=<?php
						echo $fecha_fi?>&opt_depe=<?php echo $depe;?>&marca=<?php echo $marca;?>&descripcion=<?php
						echo $descripcion;?>&tp_bien=<?php echo $tipo_bien;?>&tp_reporte=<?php
						echo $_POST['tp_reporte'];?>&tipo_fec=<?=$_POST['tipo_fec']?>&estatus=<?=$_POST['estatus']?>&serial=<?php
						echo $_POST['serial']?>"
					><img src="../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0" /></a>
					<br />
				</span>
			</td>
		</tr>
	</table>
	<?php 	
				} // Fin de Reporte detallado
			}
		}
	?>
	</body>
</html>
