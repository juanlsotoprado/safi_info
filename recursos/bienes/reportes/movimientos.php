<?php

require_once(dirname(__FILE__) . '/../../../init.php');
require_once(SAFI_MODELO_PATH . '/item.php');
require_once(SAFI_MODELO_PATH . '/estado.php');
require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');

$paramAccion = trim($_POST['accion']);

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

$activos = array();
if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
	echo "Error al realizar la consulta de los activos.";
	error_log("Error al realizar la consulta de de los activos. Detalles: " . pg_last_error());
} else {
	while($row = $GLOBALS['SafiClassDb']->Fetch($result))
	{
		$activos[$row['id_item']] = $row;
	}
}

// Obtener los estados de Venezuela
$estados = SafiModeloEstado::GetAllEstados2();

// Verificar si es la primera vez que se entra en el reporte o si ya se realizó el submit con los filtros de búsqueda.
if($paramAccion == null || $paramAccion != "generarReporte"){
	$paramTipoActaEntrada = true;
	$paramTipoActaSalida = true;
	$paramTipoActaReasignacion = true;
	
} else {
	// Verificar los filtros seleccionados para hacer el reporte
	$paramFechaInicio = trim($_POST['fechaInicio']);
	$paramFechaFin = trim($_POST['fechaFin']);
	$paramTipoActaEntrada = isset($_POST['tipoActaEntrada']) ? true : false;
	$paramTipoActaSalida = isset($_POST['tipoActaSalida']) ? true : false;
	$paramTipoActaReasignacion = isset($_POST['tipoActaReasignacion']) ? true : false;
	$paramIdActivo = trim($_POST['idActivo']);
	$paramIdActa = trim($_POST['idActa']);
	$paramIdSerialBienNacional = trim($_POST['sbn']);
	$paramIdSerialActivo = trim($_POST['serialActivo']);
	$paramIdEstado = trim($_POST['idEstado']);
	$preMsg = "";
	$where = "";
	$datosReporte = null;
	
	// Verificar si existe la fecha de inicio
	if($paramFechaInicio !== null && $paramFechaInicio != "" && strlen($paramFechaInicio) > 2)
		$existeFechaInicio = true;
	else 
		$existeFechaInicio = false;
		
	// Verificar si existe la fecha de fin
	if($paramFechaFin !== null && $paramFechaFin != "" && strlen($paramFechaFin) > 2)
		$existeFechaFin = true;
	else 
		$existeFechaFin = false;
	
	// Construir el where del query del reporte con las fechas indicadas (si existen)
	if($existeFechaInicio && $existeFechaFin){
		$where .= "
			reporte.fecha BETWEEN TO_TIMESTAMP('".$paramFechaInicio."', 'DD/MM/YYYY')
				AND TO_TIMESTAMP('".$paramFechaFin." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
		";
	} else if ($existeFechaInicio){
		$where .= "
			reporte.fecha >= TO_TIMESTAMP('".$paramFechaInicio."', 'DD/MM/YYYY')
		";
	} else if ($existeFechaFin){
		$where .= "
			reporte.fecha <= TO_TIMESTAMP('".$paramFechaFin." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
		";
	}
	
	if($paramIdActivo !== null && $paramIdActivo != "" && $paramIdActivo != "0"){
		if($where != "") $where .= " AND";
		$where .= "
			reporte.item_id = '".$paramIdActivo."'
		";
	}
	
	if($paramIdActa !== null && $paramIdActa != ""){
		if($where != "") $where .= " AND";
		$where .= "
			reporte.acta_id = '".$paramIdActa."'
		";
	}
	
	if($paramIdSerialActivo !== null && $paramIdSerialActivo != ""){
		if($where != "") $where .= " AND";
		$where .= "
			reporte.serial_activo = '".$paramIdSerialActivo."'
		";
	}
	
	if($paramIdSerialBienNacional !== null && $paramIdSerialBienNacional != ""){
		if($where != "") $where .= " AND";
		$where .= "
			reporte.serial_bien_nacional = '".$paramIdSerialBienNacional."'
		";
	}
	
	if($paramIdEstado !== null && $paramIdEstado != "" && $paramIdEstado != "0"){
		if($where != "") $where .= " AND";
		$where .= "
			reporte.id_estado_infocentro = '".$paramIdEstado."'
		";
	}
	
	if($where != ""){
	
		$queryEntrada = "
				--Entradas
				(
					SELECT
						1 AS tipo, -- 1 = entrada
						NULL::INTEGER AS reasignacion_tipo,
						entrada_detalle.bien_id AS item_id,
						entrada_detalle.serial AS serial_activo,
						entrada_detalle.marca_id,
						entrada_detalle.modelo,
						entrada_detalle.etiqueta AS serial_bien_nacional,
						entrada.acta_id AS acta_id,
						entrada_detalle.fecha_entrada AS fecha,
						entrada.depe_solicitante AS ids_dependencias_solicitantes,
						CASE
							WHEN entrada.ubicacion IS NOT NULL THEN
								(
									SELECT
										ubicacion.bubica_nombre
									FROM
										sai_bien_ubicacion ubicacion
									WHERE
										ubicacion.bubica_id = entrada.ubicacion
								)
							ELSE
								NULL
						END AS destino,
						NULL AS direccion_infocentro,
						NULL::BIGINT AS id_estado_infocentro,
						NULL AS detalle_destino,
						entrada.usua_login AS usua_login_elaborado_por,
						entrada.esta_id AS id_estatus,
						NULL AS quien_recibe,
						NULL AS fecha_entrega,
						NULL AS observaciones_finalizacion
					FROM
						sai_bien_inco entrada
						INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.acta_id = entrada.acta_id)
				)
		";
		
		$querySalida = "
				--Salidas
				(
					SELECT
						2 AS tipo, -- 2 = salida
						NULL::INTEGER AS reasignacion_tipo,
						entrada_detalle.bien_id AS item_id,
						entrada_detalle.serial AS serial_activo,
						entrada_detalle.marca_id,
						entrada_detalle.modelo,
						entrada_detalle.etiqueta AS serial_bien_nacional,
						salida.asbi_id AS acta_id,
						salida.asbi_fecha AS fecha,
						salida.solicitante AS ids_dependencias_solicitantes,
						CASE
							WHEN salida.ubicacion IS NOT NULL AND salida.ubicacion <> 3 THEN
								(
									SELECT
										ubicacion.bubica_nombre
									FROM
										sai_bien_ubicacion ubicacion
									WHERE
										ubicacion.bubica_id = salida.ubicacion
								)
							WHEN salida.ubicacion = 3 THEN
								(
									SELECT
										infocentro.nemotecnico || ': ' || infocentro.nombre
									FROM
										safi_infocentro infocentro
									WHERE
										infocentro.nemotecnico = salida.infocentro
								)
							ELSE
								NULL
						END AS destino,
						CASE
							WHEN salida.ubicacion = 3 AND salida.infocentro IS NOT NULL THEN
								(
									SELECT
										infocentro.direccion
									FROM
										safi_infocentro infocentro
									WHERE
										infocentro.nemotecnico = salida.infocentro
								)
							ELSE
								NULL
						END AS direccion_infocentro,
						CASE
							WHEN salida.ubicacion = 3 AND salida.infocentro IS NOT NULL THEN
								(
									SELECT
										infocentro.edo_id
									FROM
										safi_infocentro infocentro
									WHERE
										infocentro.nemotecnico = salida.infocentro
								)
							ELSE
								NULL
						END AS id_estado_infocentro,
						salida.asbi_destino AS detalle_destino,
						(
							SELECT
								revisiones.usua_login
							FROM
								sai_revisiones_doc revisiones
							WHERE
								revisiones.revi_doc = salida.asbi_id
								AND revisiones.wfop_id = 6
							LIMIT
								1
						) AS usua_login_elaborado_por,
						salida.esta_id AS id_estatus,
						(
							SELECT
								revision_detalle.cedula || ' - ' || revision_detalle.nombre
							FROM
								sai_revisiones_doc revision
								INNER JOIN sai_revisiones_detalle revision_detalle ON (revision.revi_id = revision_detalle.revi_id)
							WHERE
								revision.revi_doc = salida.asbi_id
								AND revision.wfop_id = 99
							LIMIT
								1
						) AS quien_recibe,
						(
							SELECT
								revision_detalle.fecha
							FROM
								sai_revisiones_doc revision
								INNER JOIN sai_revisiones_detalle revision_detalle ON (revision.revi_id = revision_detalle.revi_id)
							WHERE
								revision.revi_doc = salida.asbi_id
								AND revision.wfop_id = 99
							LIMIT
								1
						) AS fecha_entrega,
						(
							SELECT
								revision_detalle.observaciones
							FROM
								sai_revisiones_doc revision
								INNER JOIN sai_revisiones_detalle revision_detalle ON (revision.revi_id = revision_detalle.revi_id)
							WHERE
								revision.revi_doc = salida.asbi_id
								AND revision.wfop_id = 99
							LIMIT
								1
						) AS observaciones_finalizacion
					FROM
						sai_bien_asbi salida
						INNER JOIN sai_bien_asbi_item salida_detalle ON (salida_detalle.asbi_id = salida.asbi_id)
						INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.clave_bien = salida_detalle.clave_bien)
				)
		
		";
		
		$queryReasignacion = "
				--Reasignaciones (Devolución al inventario, comodato, reasignación)
				(
					SELECT
						3 AS tipo, -- 3 = reasignacion
						reasignacion.tipo AS reasignacion_tipo,
						entrada_detalle.bien_id AS item_id,
						entrada_detalle.serial AS serial_activo,
						entrada_detalle.marca_id,
						entrada_detalle.modelo,
						entrada_detalle.etiqueta AS serial_bien_nacional,
						reasignacion.acta_id AS acta_id,
						reasignacion.fecha_acta AS fecha,
						reasignacion.solicitante AS ids_dependencias_solicitantes,
						CASE
							WHEN reasignacion.ubicacion IS NOT NULL AND reasignacion.ubicacion <> 3 THEN
								(
									SELECT
										ubicacion.bubica_nombre
									FROM
										sai_bien_ubicacion ubicacion
									WHERE
										ubicacion.bubica_id = reasignacion.ubicacion
								)
							WHEN reasignacion.ubicacion = 3 THEN
								(
									SELECT
										infocentro.nemotecnico || ': ' || infocentro.nombre
									FROM
										safi_infocentro infocentro
									WHERE
										infocentro.nemotecnico = reasignacion.infocentro
								)
							ELSE
								NULL
						END AS destino,
						CASE
							WHEN reasignacion.ubicacion = 3 AND reasignacion.infocentro IS NOT NULL THEN
								(
									SELECT
										infocentro.direccion
									FROM
										safi_infocentro infocentro
									WHERE
										infocentro.nemotecnico = reasignacion.infocentro
								)
							ELSE
								NULL
						END AS direccion_infocentro,
						CASE
							WHEN reasignacion.ubicacion = 3 AND reasignacion.infocentro IS NOT NULL THEN
								(
									SELECT
										infocentro.edo_id
									FROM
										safi_infocentro infocentro
									WHERE
										infocentro.nemotecnico = reasignacion.infocentro
								)
							ELSE
								NULL
						END AS id_estado_infocentro,
						reasignacion.destino AS detalle_destino,
						(
							SELECT
								revisiones.usua_login
							FROM
								sai_revisiones_doc revisiones
							WHERE
								revisiones.revi_doc = reasignacion.acta_id
								AND revisiones.wfop_id = 6
							LIMIT
								1
						) AS usua_login_elaborado_por,
						reasignacion.esta_id AS id_estatus,
						(
							SELECT
								revision_detalle.cedula || ' - ' || revision_detalle.nombre
							FROM
								sai_revisiones_doc revision
								INNER JOIN sai_revisiones_detalle revision_detalle ON (revision.revi_id = revision_detalle.revi_id)
							WHERE
								revision.revi_doc = reasignacion.acta_id
								AND revision.wfop_id = 99
							LIMIT
								1
						) AS quien_recibe,
						(
							SELECT
								revision_detalle.fecha
							FROM
								sai_revisiones_doc revision
								INNER JOIN sai_revisiones_detalle revision_detalle ON (revision.revi_id = revision_detalle.revi_id)
							WHERE
								revision.revi_doc = reasignacion.acta_id
								AND revision.wfop_id = 99
							LIMIT
								1
						) AS fecha_entrega,
						(
							SELECT
								revision_detalle.observaciones
							FROM
								sai_revisiones_doc revision
								INNER JOIN sai_revisiones_detalle revision_detalle ON (revision.revi_id = revision_detalle.revi_id)
							WHERE
								revision.revi_doc = reasignacion.acta_id
								AND revision.wfop_id = 99
							LIMIT
								1
						) AS observaciones_finalizacion
					FROM
						sai_bien_reasignar reasignacion
						INNER JOIN sai_bien_reasignar_item reasignacion_detalle ON (reasignacion_detalle.acta_id = reasignacion.acta_id)
						INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.clave_bien = reasignacion_detalle.clave_bien)
				)
		";
		
		$query = "
		SELECT
			reporte.tipo,
			reporte.reasignacion_tipo,
			reporte.acta_id,
			item.nombre AS nombre_item,
			marca.bmarc_nombre AS nombre_marca,
			reporte.modelo,
			reporte.serial_activo,
			reporte.serial_bien_nacional,
			to_char(reporte.fecha, 'DD/MM/YYYY') AS fecha,
			ARRAY_TO_STRING(
				ARRAY (
					(
						SELECT
							dependencia.depe_nombre
						FROM
							sai_dependenci dependencia
						WHERE
							POSITION (dependencia.depe_id IN  reporte.ids_dependencias_solicitantes) > 0
							/*
							-- Otra forma de hacer este query
							dependencia.depe_id =
								ANY (STRING_TO_ARRAY(reporte.ids_dependencias_solicitantes, ','))
							*/
					)
				), ', '
			) AS nombre_dependencia_solicitante,
			reporte.destino,
			reporte.direccion_infocentro,
			--reporte.estado_infocentro,
			estado.nombre AS nombre_estado_infocentro,
			reporte.detalle_destino,
			empleado.empl_nombres AS nombres_elaborado_por,
			empleado.empl_apellidos AS apellidos_elaborado_por,
			estatus.esta_nombre AS nombre_estatus,
			reporte.quien_recibe,
			".(
				$paramTipoActaEntrada && !$paramTipoActaSalida && !$paramTipoActaReasignacion
					? "NULL AS str_fecha_entrega," : "to_char(reporte.fecha_entrega, 'DD/MM/YYYY') AS str_fecha_entrega,"
			)."
			reporte.observaciones_finalizacion
		FROM
			(
				".($paramTipoActaEntrada ? $queryEntrada : "")."
				".($paramTipoActaEntrada && $paramTipoActaSalida ? "UNION" : "")."
				".($paramTipoActaSalida ? $querySalida : "")."
				".( ($paramTipoActaEntrada || $paramTipoActaSalida) && $paramTipoActaReasignacion ? "UNION" : "")."
				".($paramTipoActaReasignacion ? $queryReasignacion : "")."
			) AS reporte
			INNER JOIN sai_bien_marca marca ON (marca.bmarc_id = reporte.marca_id)
			INNER JOIN sai_item item ON (item.id = reporte.item_id)
			LEFT JOIN sai_dependenci AS dependencia ON (dependencia.depe_id = reporte.ids_dependencias_solicitantes)
			LEFT JOIN safi_edos_venezuela estado ON (estado.id = reporte.id_estado_infocentro)
			LEFT JOIN sai_usuario usuario ON (usuario.usua_login = reporte.usua_login_elaborado_por)
			LEFT JOIN sai_empleado empleado ON (empleado.empl_cedula = usuario.empl_cedula)
			INNER JOIN sai_estado estatus ON (estatus.esta_id = reporte.id_estatus)
		WHERE
			".$where."
		ORDER BY
			reporte.fecha,
			reporte.acta_id,
			item.nombre
		";
		
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
			echo "Error al realizar la consulta de movimientos.".pg_last_error();
			error_log("Error al realizar la consulta de movimientos de bienes. Detalles: " . pg_last_error());
		} else {
			$datosReporte = array();
			while($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$datosReporte[] = $row;
			}
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>.:SAFI:. Reporte de movimeintos de activos</title>
		
		<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
		<link href="../../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript" src="../../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript" src="../../../js/funciones.js"></script>
		<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
		
		<script type="text/javascript">
		
			g_Calendar.setDateFormat('dd/mm/yyyy');

			function comparar_fechas(elemento){
				
				var fecha_inicial=document.getElementById("fechaInicio").value;
				var fecha_final=document.getElementById("fechaFin").value;
				
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
					
				if ( (anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || 
				 ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2)) ){
				  alert("La fecha inicial no debe ser mayor a la fecha final"); 
				  elemento.value='';
				  return;
				}
			}

			function ejecutar()
			{
				if(!$("#tipoActaEntrada").is(':checked') && !$("#tipoActaSalida").is(':checked') && !$("#tipoActaReasignacion").is(':checked'))
				{
					alert("Debe indicar al menos un tipo de acta.");
					return;
				}
				
				if( ($("#fechaInicio").val() == '') && ($("#fechaFin").val() == '') && ($("#idActivo").val() == '0')
					&& ($("#idActa").val() == '') && ($("#serialActivo").val() == '') && ($("#sbn").val() == ''
					&& $("#idEstado").val() == '0')
				){
					alert("Debe seleccionar un criterio de b\u00fasqueda.");
					return;
				}

				$("#movimientos").submit();
			}
			
			function limpiarFormulario()
			{
				$("#fechaInicio").val('');
				$("#fechaFin").val('');
				$("#idActivo").val('0');
				$("#idActa").val('');
				$("#serialActivo").val('');
				$("#sbn").val('');
				$("#idEstado").val('0');
				$("#tipoActaEntrada").attr("checked", true);
				$("#tipoActaSalida").attr("checked", true);
				$("#tipoActaReasignacion").attr("checked", true);
				
			}
			
		</script>
		
	</head>
	
	<body class="normal">
		
		<form name="movimientos" id="movimientos" action="movimientos.php" method="post">
		
		<input type="hidden" name="accion" value="generarReporte"/>
			<table cellpadding="0" cellspacing="0" width="640" align="center"
				class="tablaalertas fondoPantalla"
			>
				<tr> 
    				<td height="21" colspan="2" class="normalNegroNegrita header" align="left">
    					Movimientos
    				</td>
				</tr>
				<tr>
					<td height="5" colspan="2"></td>
				</tr>
				<tr>
  					<td class="normalNegrita">Tipo de acta:(*)</td>
  					<td>
  						<input type="checkbox" id="tipoActaEntrada" name="tipoActaEntrada" value="true"
  							<?php echo $paramTipoActaEntrada ? ' checked="checked"' : ''?> 
  						/>
  						<label for="tipoActaEntrada">Entrada</label>&nbsp;&nbsp;&nbsp;
  						<input type="checkbox" id="tipoActaSalida" name="tipoActaSalida" value="true"
  							<?php echo $paramTipoActaSalida ? ' checked="checked"' : ''?>
  						/>
  						<label for="tipoActaSalida">Salida</label>&nbsp;&nbsp;&nbsp;
  						<input type="checkbox" id="tipoActaReasignacion" name="tipoActaReasignacion" value="true"
  							<?php echo $paramTipoActaReasignacion ? ' checked="checked"' : ''?>
  						/>
  						<label for="tipoActaReasignacion">Reasignaci&oacute;n</label>
  					</td>
  				</tr>
  				<tr>
  					<td class="normalNegrita">Fecha:</td>
  					<td style="padding-top: 10px; padding-bottom: 10px;">
  						<!-- Agregar los accesos rapidos de las fechas (Hoy, ayer, semana, semana pasada, etc.) -->
						<?php VistaFechas::ConstruirAccesosRapidosFechas("fechaInicio", "fechaFin", "dd/mm/yy") ?>
  						<input
  							type="text"
  							size="10"
  							id="fechaInicio"
  							name="fechaInicio"
  							class="dateparse"
							onfocus="javascript: comparar_fechas(this);"
							readonly="readonly"
							value="<?php echo $paramFechaInicio !== null && $paramFechaInicio != "" ? $paramFechaInicio : ""; ?>"
						/>
						<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaInicio');" title="Show popup calendar"><img
							src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"
						/></a>
						
						<input
							type="text"
							size="10"
							id="fechaFin"
							name="fechaFin"
							class="dateparse"
							onfocus="javascript: comparar_fechas(this);"
							readonly="readonly"
							value="<?php echo $paramFechaFin !== null && $paramFechaFin != "" ? $paramFechaFin : ""; ?>"
						/>
						<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaFin');" title="Show popup calendar"><img
							src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"
						/></a>
  					</td>
  				</tr>
  				<tr>
  					<td class="normalNegrita">Activo:</td>
  					<td>
  						<select id="idActivo" name="idActivo" class="normalNegro">
							<option value="0">[Seleccione]</option>
							<?php
								foreach ($activos AS $row)
								{
									echo '
							<option value="'.$row['id_item'].'"
								'.($paramIdActivo != null && $paramIdActivo != "" && $row['id_item'] == $paramIdActivo
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
  					<td class="normalNegrita">Acta:</td>
  					<td>
  						<input type="text" name="idActa" id="idActa" size="10" value="<?php
  							echo $paramIdActa !== null && $paramIdActa != "" ? $paramIdActa : "";
  						?>"/>
  					</td>
  				</tr>
  				<tr>
  					<td class="normalNegrita">Serial del Activo:</td>
  					<td>
  						<input type="text" name="serialActivo" id="serialActivo" size="10" value="<?php 
  							echo $paramIdSerialActivo !== null && $paramIdSerialActivo != "" ? $paramIdSerialActivo : "";
  						?>"/>
  					</td>
  				</tr>
  				<tr>
  					<td class="normalNegrita">Serial de Bien Nacional:</td>
  					<td>
  						<input type="text" name="sbn" id="sbn" size="10" value="<?php
  							echo $paramIdSerialBienNacional !== null && $paramIdSerialBienNacional != "" ? $paramIdSerialBienNacional : "";
  						?>"/>
  					</td>
  				</tr>
  				<tr>
  					<td class="normalNegrita">Estado Infocentro:</td>
  					<td>
  						<select id="idEstado" name="idEstado" class="normalNegro">
							<option value="0">[Seleccione]</option>
							<?php
								foreach ($estados AS $estado)
								{
									echo '
							<option value="'.$estado->GetId().'"
								'.($paramIdEstado != null && $paramIdEstado != "" && $estado->GetId() == $paramIdEstado
								? ' selected="selected"' : "").'
							>
								'. mb_strtoupper($estado->GetNombre(), 'ISO-8859-1').'
							</option>
									';
								}
							?>
						</select>
  					</td>
  				</tr>
  				<tr>
  					<td style="padding-top: 20px;">(*) Campo obligatorio</td>
  				</tr>
  				<tr>
					<td colspan="2" align="center">
						<input type="button" value="Buscar" onclick="javascript:ejecutar()" class="normalNegro" />
						<input type="button" value="Limpiar" class="normalNegro" onclick="limpiarFormulario();"/>
					</td>
				</tr>
  			</table>
		
		</form>
		
		<?php
			if (isset($datosReporte) && is_array($datosReporte)){
		
				echo '
		<br/><br/>
		<table border="0" align="center" cellpadding="0" cellspacing="0" class="tablaalertas">
			<tr>
				<td class="header normalNegroNegrita">N&ordm;</td>
				<td class="header normalNegroNegrita">Activo</td>
				<td class="header normalNegroNegrita">Marca</td>
				<td class="header normalNegroNegrita">Modelo</td>
				<td class="header normalNegroNegrita">Serial Activo</td>
				<td class="header normalNegroNegrita">Bien Nacional</td>
				<td class="header normalNegroNegrita">N&ordm; Acta</td>
				<td class="header normalNegroNegrita">Fecha</td>
				<td class="header normalNegroNegrita">Dependencias Solicitantes</td>
				<td class="header normalNegroNegrita">Destino</td>
				<td class="header normalNegroNegrita">Direcci&oacute;n Infocentro</td>
				<td class="header normalNegroNegrita">Estado Infocentro</td>
				<td class="header normalNegroNegrita">Detalle destino</td>
				<td class="header normalNegroNegrita">Elaborado por (EP) / Revisado por (RP)</td>
				<td class="header normalNegroNegrita">Estatus</td>
				<td class="header normalNegroNegrita">Quien recibe</td>
				<td class="header normalNegroNegrita">Fecha entrega</td>
				<td class="header normalNegroNegrita">Observaciones finalizaci&oacute;n</td>
			</tr>
				';
				$count = 0;
				foreach ($datosReporte AS $datoReporte)
				{
					$tdClass = ($tdClass == "even") ? "odd" : "even";
					$strIdActa = "";
					if($datoReporte["tipo"] == "1"){ // Entrada
						$strIdActa = '
							<a
								href="javascript:abrir_ventana(\'../inco_pdf.php?codigo='.$datoReporte["acta_id"].'\')" class="copyright"
							>'.$datoReporte["acta_id"].'</a>
						';
					} elseif ($datoReporte["tipo"] == "2"){ // Salida
						$strIdActa = '
							<a
								href="javascript:abrir_ventana(\'../salida_activos_pdf.php?codigo='.$datoReporte["acta_id"].'&tipo=a\')"
								class="copyright"
							>'.$datoReporte["acta_id"].'</a>
						';
					} elseif ($datoReporte["tipo"] == "3"){ // Reasignación
						
						$strReasignacionTipo = "";
						if($datoReporte["reasignacion_tipo"] == 1){
							$strReasignacionTipo = "Comodato";
						} else if($datoReporte["reasignacion_tipo"] == 2){
							$strReasignacionTipo = "Reasignaci&oacute;n";
						} else if($datoReporte["reasignacion_tipo"] == 3){
							$strReasignacionTipo = "Retorno al inventario";
						}
							
						$strIdActa = '
							<a
								href="javascript:abrir_ventana(\'../reasignar_activos_pdf.php?codigo='.$datoReporte["acta_id"].'\')"
								class="copyright"
							>'.$datoReporte["acta_id"].'</a>
							'.($strReasignacionTipo != '' ? '<br/>('.$strReasignacionTipo.')' : "").'
						';
					} else {
						$strIdActa = $datoReporte["acta_id"]; 
					}
					
					echo '
			<tr class="'.$tdClass.'" onclick="Registroclikeado(this);">
				<td>'.(++$count).'</td>
				<td>'.$datoReporte["nombre_item"].'</td>
				<td>'.$datoReporte["nombre_marca"].'</td>
				<td>'.$datoReporte["modelo"].'</td>
				<td>'.$datoReporte["serial_activo"].'</td>
				<td>'.$datoReporte["serial_bien_nacional"].'</td>
				<td>'.$strIdActa.'</td>
				<td>'.$datoReporte["fecha"].'</td>
				<td>'.$datoReporte["nombre_dependencia_solicitante"].'</td>
				<td>'.$datoReporte["destino"].'</td>
				<td>'.$datoReporte["direccion_infocentro"].'</td>
				<td>'.$datoReporte["nombre_estado_infocentro"].'</td>
				<td>'.$datoReporte["detalle_destino"].'</td>
				<td>
					'.($datoReporte["tipo"] == "1" ? "EP" : "RP").': '
					.$datoReporte["nombres_elaborado_por"]." ".$datoReporte["apellidos_elaborado_por"].'
				</td>
				<td>'.$datoReporte["nombre_estatus"].'</td>
				<td>'.$datoReporte["quien_recibe"].'</td>
				<td>'.$datoReporte["str_fecha_entrega"].'</td>
				<td>'.$datoReporte["observaciones_finalizacion"].'</td>
			</tr>
					';
					
				}
				
				echo '
		</table>
				';
			}
		?>
		
	</body>
	
</html>