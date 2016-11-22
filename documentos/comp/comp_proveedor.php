<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
require_once("../../includes/fechas.php");
require_once("../../includes/funciones.php");

if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();
$pressAnno = $_SESSION['an_o_presupuesto'];
//$pressAnno = 2014;

$fechaInicio = $_POST['fechaInicio'];
$fechaFin = $_POST['fechaFin'];
$compId = $_POST['compId'];
$asunto = $_POST['asunto'];
//$tipoActividad = $_POST['tipoActividad'];
$proyectoAccion = $_POST['proyectoAccion'];
$rifProveedor = $_POST['rifProveedor'];
$palabraClave = $_POST['palabraClave'];
$estatus = $_POST['estatus'];
$centroGestor = $_POST['centroGestor'];
$mostrarPartidas = $_POST['mostrarPartidas'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar Compromisos</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet" />
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
	<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
	<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
	<style type="">
	.datos td{
		border-top: 1px #8BBF8E solid;
	}
	</style>
	<script language="JavaScript" src="../../js/funciones.js"> </script>

	<script>
	function detalle(codigo) {
	    url="detalle.php?codigo="+codigo;
		newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
		if (window.focus) {newwindow.focus()}
	}

	function ejecutar() { 
		if (
				document.form.fechaInicio.value=='' && 
				document.form.fechaFin.value=='' &&
	     		document.form.asunto.value=='' && 
		 		document.form.proyectoAccion.value=='' && 
		 		document.form.rifProveedor.value=='' && 
		 		document.form.estatus.value=='' && 
		 		document.form.compId.value==''
		 	) {
			alert("Debe especificar al menos 1 criterio para la b\u00fasqueda");
			return;
	 	}
	
		if ( 
				(
					document.form.fechaInicio.value=='' && 
					document.form.fechaFin.value!=''
				) || 
				(
					document.form.fechaInicio.value!='' && 
					document.form.fechaFin.value==''
				)
			)  {
			alert ('Debe especificar el rango completo de fechas a buscar');
			return;
		}

		document.form.buscar.value="true";
		document.form.action="comp_proveedor.php";
		document.form.submit();
	}

	function ejecutar2() { 
		if (
				document.form.fechaInicio.value=='' && 
				document.form.fechaFin.value=='' &&
	     		document.form.asunto.value=='' && 
		 		document.form.proyectoAccion.value=='' && 
		 		document.form.rifProveedor.value=='' && 
		 		document.form.estatus.value=='' && 
		 		document.form.compId.value==''
		 	) {
			alert("Debe especificar al menos 1 criterio para la b\u00fasqueda");
			return;
 		}

		if (
				(
					document.form.fechaInicio.value=='' && 
					document.form.fechaFin.value!=''
				) || 
				(
					document.form.fechaInicio.value!='' && 
					document.form.fechaFin.value==''
				)
			) {
	  		alert ('Debe especificar el rango completo de fechas a buscar');
			return;
 		}

		document.form.buscar.value="true";
		document.form.action="comp_proveedorXLS.php";
		document.form.submit();
	}

	function comparar_fechas(fecha_inicial,fecha_final) { //Formato dd/mm/yyyy 	
		var fecha_inicial=document.form.fechaInicio.value;
		var fecha_final=document.form.fechaFin.value;
			
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
			
		if (
				anio1>anio2 || 
				(anio1==anio2 && mes1>mes2) || 
		 		(anio1 == anio2 && mes1==mes2 && dia1>dia2)
		 	) {
		 	
			alert("La fecha inicial no debe ser mayor a la fecha final"); 
			document.form.fechaFin.value='';
			return;
		}
	}
	</script>
</head>
<body>
	<form name="form" method="post">
		<input type="hidden" value="" name="buscar" />
		<table width="635" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td height="21" colspan="2" class="normalNegroNegrita" align="left">
					Control Interno de Apartados y Compromisos
				</td>
			</tr>
			<tr>
				<td height="10" colspan="2"></td>
			</tr>
			<tr>
				<td width="175" height="29" class="normalNegrita" align="left">
					Modificados entre:
				</td>
				<td>
					<input 
						type="text" 
						size="10" 
						id="fechaInicio" 
						name="fechaInicio"
						class="dateparse" 
						onfocus="javascript: comparar_fechas(this);"
						readonly="readonly" 
						value="<?= $fechaInicio?>" />
					<a
						href="javascript:void(0);"
						onclick="g_Calendar.show(event, 'fechaInicio');"
						title="Show popup calendar" >
						<img
							src="../../js/lib/calendarPopup/img/calendar.gif"
							class="cp_img"
							alt="Open popup calendar" /></a>
					<input 
						type="text" 
						size="10" 
						id="fechaFin"
						name="fechaFin" 
						class="dateparse"
						onfocus="javascript: comparar_fechas(this);" 
						readonly="readonly"
						value="<?= $fechaFin?>" />
					<a 
						href="javascript:void(0);"
						onclick="g_Calendar.show(event, 'fechaFin');"
						title="Show popup calendar" >
						<img
							src="../../js/lib/calendarPopup/img/calendar.gif"
							class="cp_img"
							alt="Open popup calendar" /></a>
				</td>
			</tr>
			<tr>
				<td class="normalNegrita" align="left">C&oacute;digo:</td>
				<td>
					<span class="normalNegrita">
						<input 
							name="compId"
							type="text" 
							class="peq" 
							id="compId" 
							value="<?php if ( $compId!=null && $compId!='' ) { echo $compId; } else echo "comp-";?>"
							size="12" />
					</span>
				</td>
			</tr>
			<tr>
				<td class="normalNegrita" align="left">Asunto del compromiso:</td>
				<td>
					<span class="normalNegrita">
						<select name='asunto' class="normalNegro">
							<option value="">Todos</option>
							<?php
								$sql = "SELECT cpas_id, cpas_nombre FROM sai_compromiso_asunt ORDER BY cpas_nombre";
								$resultado=pg_query($conexion,$sql) or die("Error al consultar los tipos de compromisos");
								while($row=pg_fetch_array($resultado)) {
									if ( $asunto!=null && $asunto==$row['cpas_id'] ) {
							?>
										<option value="<?= $row['cpas_id']?>" selected="selected">
											<?= $row['cpas_nombre']?>
										</option>
							<?php 
									} else {
							?>
										<option value="<?= $row['cpas_id']?>">
											<?= $row['cpas_nombre']?>
										</option>
							<?php
									}
								}
							?>
						</select>
					</span>
				</td>
			</tr>
			<?php /*
			<tr>
				<td class="normalNegrita" align="left">Tipo de actividad:</td>
				<td>
					<span class="normalNegrita">
						<select name='tipoActividad' class="normalNegro">
							<option value="">Todas</option>
							<?php
								$sql = "SELECT id, nombre FROM sai_tipo_actividad ORDER BY nombre";
								$resultado=pg_query($conexion,$sql) or die("Error al consultar los tipos de compromisos");
								while($row=pg_fetch_array($resultado)) {
									if ( $tipoActividad!=null && $tipoActividad==$row['id'] ) {
							?>
										<option value="<?= $row['id']?>" selected="selected">
											<?= $row['nombre']?>
										</option>
							<?php
									} else {
							?>
										<option value="<?= $row['id']?>">
											<?= $row['nombre']?>
										</option>
							<?php
									}
								}
							?>
						</select>
					</span>
				</td>
			</tr>
			*/?>
			<tr>
				<td class="normalNegrita" align="left">Estatus del compromiso:</td>
				<td>
					<span class="normalNegrita">
						<select name='estatus' class="normalNegro">
							<option value="">Todos</option>
							<option value="N/A" <?php if ( $estatus == 'N/A' ) { echo 'selected="selected"'; } ?>>
								<?= "N/A";?>
							</option>
							<option value="Por Rendir" <?php if ( $estatus == 'Por Rendir' ) { echo 'selected="selected"'; } ?>>
								<?= "Por Rendir";?>
							</option>
							<option value="Reportado" <?php if ( $estatus == 'Reportado' ) { echo 'selected="selected"'; } ?>>
								<?= "Reportado";?>
							</option>
						</select>
					</span>
				</td>
			</tr>
			<tr>
				<td class="normalNegrita" align="left">Proyecto/Acc	espec&iacute;fica:</td>
				<td>
					<span>
						<select name='proyectoAccion' class="normalNegro">
							<option value="" class="normal">Todos</option>
							<?php
								$sql = "SELECT pres_anno, acce_id AS proyecto, aces_id AS especifica, centro_gestor, centro_costo FROM sai_acce_esp WHERE pres_anno = ".$pressAnno." union SELECT pres_anno, proy_id AS proyecto, paes_id AS especifica, centro_gestor, centro_costo FROM sai_proy_a_esp WHERE pres_anno = ".$pressAnno." order by pres_anno DESC,centro_gestor, centro_costo ";
	      						$resultado=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");
	      						while($row=pg_fetch_array($resultado)){
									if ( $proyectoAccion!=null && $proyectoAccion==$row['proyecto'].":::".$row['especifica'] ) {
							?>
										<option	value="<?= $row['proyecto'].":::".$row['especifica']?>"	class="normal" selected="selected">
											<?= ($row['pres_anno'].'-'.$row['centro_gestor'].'/'.$row['centro_costo']);?>
										</option>
							<?php 
									} else {
							?>
										<option value="<?= $row['proyecto'].":::".$row['especifica']?>" class="normal">
											<?= ($row['pres_anno'].'-'.$row['centro_gestor'].'/'.$row['centro_costo']);?>
										</option>
							<?php 
									}
								}
							?>
						</select>
					</span>
				</td>
			</tr>
			<tr>
				<td class="normalNegrita" align="left">Centro Gestor:</td>
				<td>
					<span>
						<select name='centroGestor' class="normalNegro">
							<option value="" class="normal">Todos</option>
							<?php
							$sql = "
										SELECT 
											s.id_proyecto_accion,
											s.tipo,
											s.centro_gestor
										FROM 
											(
												SELECT 
													spae.proy_id AS id_proyecto_accion,
													1 AS tipo,
													spae.centro_gestor, 
													sp.proy_titulo AS nombre_categoria
												FROM 
													sai_proyecto sp, 
													sai_proy_a_esp spae, 
													sai_forma_1125 sf1125
												WHERE 
													sp.pre_anno = spae.pres_anno AND 
													sp.proy_id = spae.proy_id AND 
													spae.pres_anno = sf1125.pres_anno AND
													spae.proy_id = sf1125.form_id_p_ac AND 
													sf1125.form_id_aesp = spae.paes_id AND
													sf1125.pres_anno = '".$pressAnno."'
												UNION ALL
												SELECT 
													sae.acce_id AS id_proyecto_accion, 
													'0' AS tipo,
													sae.centro_gestor,
													sac.acce_denom AS nombre_categoria
												FROM 
													sai_ac_central sac, 
													sai_acce_esp sae, 
													sai_forma_1125 sf1125
												WHERE 
													sac.pres_anno = sae.pres_anno AND 
													sac.acce_id = sae.acce_id AND 
													sae.pres_anno = sf1125.pres_anno AND
													sf1125.form_id_aesp = sae.aces_id AND 
													sf1125.form_id_p_ac = sae.acce_id AND 
													sf1125.pres_anno = '".$pressAnno."'
											) AS s
										GROUP BY 
											s.id_proyecto_accion, 
											s.tipo, 
											s.nombre_categoria, 
											s.centro_gestor
										ORDER BY 
											s.centro_gestor,
											s.tipo DESC, 
											s.nombre_categoria ASC ";
	      					$resultado=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");
	      					while($row=pg_fetch_array($resultado)) {
								if ( $centroGestor!=null && $centroGestor==$row['centro_gestor'] ) {
							?>
									<option value="<?= $row['centro_gestor'];?>" class="normal" selected="selected">
										<?= $row['centro_gestor'];?>
									</option>
							<?php
									
								} else {
							?>
									<option value="<?= $row['centro_gestor'];?>" class="normal">
										<?= $row['centro_gestor'];?>
									</option>
							<?php
								}
							}
							?>
						</select>
					</span>
				</td>
			</tr>
			<tr>
				<td class="normalNegrita" align="left">RIF o Nombre Proveedor:</td>
				<td>
					<span class="normalNegrita">
						<input name="rifProveedor" type="text" class="peq" id="rifProveedor" value="<?= $rifProveedor?>" size="25" />
					</span>
				</td>
			</tr>
			<tr>
				<td class="normalNegrita" align="left">Palabra Clave:</td>
				<td>
					<span class="normalNegrita">
						<input name="palabraClave" type="text" class="normalNegro" id="palabraClave" value="<?= $palabraClave?>" size="25" />
					</span>
					<span class="normal">(Incluida en la descripci&oacute;n del compromiso)</span>
				</td>
			</tr>
			<tr>
				<td class="normalNegrita" align="left" colspan="2"><input type="checkbox" id="mostrarPartidas" name="mostrarPartidas" value="true" <?php if ( $mostrarPartidas=='true' ) { echo 'checked="checked"'; } ?> />Mostrar partidas</td>
			</tr>
		</table>
		<br/>
		<div align="center">
			<input type="button" value="Buscar" onclick="javascript:ejecutar();" />&nbsp;&nbsp;
			<input type="button" value="Exportar" onclick="javascript:ejecutar2();" />
		</div>
	</form>
	<form name="form3" action="" method="post">
	<?php 
		if ($_POST['buscar']=='true') {
			$query = "
						SELECT
							s.comp_id, 
							s.comp_fecha,
							s.fecha,
							s.pcta_id,
							s.centro_gestor,
							s.centro_costo,
							s.fecha_reporte,
							s.estado,
							s.infocentro,
							s.tipo_obra,
							s.monto,
							s.id_proyecto_accion,
							s.id_accion_especifica,
							s.rif_proveedor_sugerido,
							s.beneficiario,
							s.asunto,
							s.empl_nombres,
							s.empl_apellidos,
							s.dependencia,
							s.comp_estatus,
							s.comp_documento,
							s.comp_descripcion,
							--s.actividad,
							--s.evento,
							s.control,
							s.localidad,
							s.comp_observacion,
							s.fecha_inicio,
							s.fecha_fin,
							ARRAY
							(
								(
									SELECT
										COALESCE(SUM(scd.cadt_monto),0)||'||'||COALESCE(sp.sopg_id,'')
									FROM
										sai_sol_pago sp
										LEFT OUTER JOIN sai_causado sc ON (
																			sc.pres_anno = ".$pressAnno." AND

																			--sc.esta_id <> 15 AND

																			sc.esta_id <> 2 AND
																			sp.sopg_id = sc.caus_docu_id 
										)
										LEFT OUTER JOIN sai_causad_det scd ON (
																				sc.caus_id = scd.caus_id AND
																				sc.pres_anno = scd.pres_anno AND
																				scd.cadt_id_p_ac = s.id_proyecto_accion AND
																				scd.cadt_cod_aesp = s.id_accion_especifica AND
																				scd.cadt_abono = '1' AND
																				scd.part_id NOT LIKE '4.11.0%' ";
			if ( $mostrarPartidas=='true' ) {
				$query .= 														" AND scd.part_id = s.part_id ";
			}
			$query .= 		"			)
									WHERE
										sp.comp_id = s.comp_id ";
			if ( $fechaInicio!=null && $fechaInicio!='' && $fechaFin!=null && $fechaFin!='' ) {				
				$query .= 	"			AND CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND 
										(sc.fecha_anulacion IS NULL OR (CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) ";
				//$query .= 	" 			AND sc.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY')+1 ";
			} else {
				$query .= 	"			AND sp.esta_id <> 15 AND
		 								sc.esta_id <> 15 ";
			}
			$query .= 				"GROUP BY sp.sopg_id
								)
								UNION 
								( 
									SELECT
	 									COALESCE(SUM(scd.cadt_monto),0)||'||'||COALESCE(sci.comp_id,'')
									FROM
										sai_codi sci
										LEFT OUTER JOIN sai_causado sc ON (
																			sc.pres_anno = ".$pressAnno." AND
					
																			--sc.esta_id <> 15 AND
					
																			sc.esta_id <> 2 AND
																			sci.comp_id = sc.caus_docu_id 
										)
										LEFT OUTER JOIN sai_causad_det scd ON (
																				sc.caus_id = scd.caus_id AND
																				sc.pres_anno = scd.pres_anno AND
																				scd.part_id NOT LIKE '4.11.0%' ";
			if ( $mostrarPartidas=='true' ) {
				$query .= 														" AND scd.part_id = s.part_id ";
			}
			$query .= 					")
									WHERE
										sci.nro_compromiso = s.comp_id ";			
			if ( $fechaInicio!=null && $fechaInicio!='' && $fechaFin!=null && $fechaFin!='' ) {
				$query .= 	"			AND CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND
										(sc.fecha_anulacion IS NULL OR (CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) AND 
										sci.comp_fec BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY')";
				/*$query .= 				"AND sc.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY')+1 
										AND sci.comp_fec BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY') ";*/
			} else {
				$query .= 	"			AND sci.esta_id <> 15 AND
		 								sc.esta_id <> 15 ";
			}
			$query .= 	"			GROUP BY sci.comp_id
								)
							) AS causados, ";
			if ( $mostrarPartidas=='true' ) {
				$query .= 	"s.part_id ";
			} else {
				$query .=	"ARRAY
							(
								SELECT
									CASE 
										WHEN ( spc.pgch_id IS NOT NULL AND spc.pgch_id <> '' ) THEN
											COALESCE(SUM(spd.padt_monto),0)||'||'||COALESCE(spc.nro_cuenta,' ')||'||'||COALESCE(spc.pgch_id,' ')	
										WHEN ( spt.trans_id IS NOT NULL AND spt.trans_id <> '' ) THEN
											COALESCE(SUM(spd.padt_monto),0)||'||'||COALESCE(spt.nro_cuenta_emisor,' ')||'||'||COALESCE(spt.trans_id,' ')
										ELSE ''
									END 
								FROM 
									(
										SELECT
											ssp.sopg_id,
											ssp.esta_id
										FROM
											sai_sol_pago ssp
										WHERE ";
				if ( $fechaInicio==null || $fechaInicio=='' || $fechaFin==null || $fechaFin=='' ) {
					$query .=				" ssp.esta_id <> 15 AND ";
				}
				$query .=					" ssp.comp_id = s.comp_id 
									) AS ssp
									LEFT OUTER JOIN
										(
											SELECT
												spc.pgch_id,
												spc.docg_id,
												spc.nro_cuenta,
												spc.esta_id
											FROM 
												sai_pago_cheque spc
											WHERE ";
				if ( $fechaInicio==null || $fechaInicio=='' || $fechaFin==null || $fechaFin=='' ) {
					$query .=					" spc.esta_id <> 15 AND ";
				}
				$query .=						" spc.esta_id <> 2 AND
												spc.pres_anno_docg = ".$pressAnno."
										) AS spc
										ON (spc.docg_id = ssp.sopg_id)
									LEFT OUTER JOIN
										(
											SELECT
												spt.trans_id,
												spt.docg_id,
												spt.nro_cuenta_emisor,
												spt.esta_id
											FROM 
												sai_pago_transferencia spt
											WHERE ";
				if ( $fechaInicio==null || $fechaInicio=='' || $fechaFin==null || $fechaFin=='' ) {
					$query .=					" spt.esta_id <> 15 AND ";
				}
				$query .=						" spt.esta_id <> 2 AND
												spt.pres_anno_docg = ".$pressAnno."
										) AS spt
										ON (spt.docg_id = ssp.sopg_id) 
									LEFT OUTER JOIN 
										(
											SELECT
												sp.paga_id,
												sp.paga_docu_id
											FROM 
												sai_pagado sp 
											WHERE
												sp.pres_anno = ".$pressAnno." AND
												sp.esta_id <> 2 ";
				if ( $fechaInicio!=null && $fechaInicio!='' && $fechaFin!=null && $fechaFin!='' ) {
					$query .=					" AND CAST(sp.paga_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND
												(sp.fecha_anulacion IS NULL OR (CAST(sp.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) ";
					//$query .= 					"AND sp.paga_fecha BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY')+1 ";
				} else {
					$query .=					" AND sp.esta_id <> 15 ";
				}
				$query .= 				") AS sp
										ON (sp.paga_docu_id = spc.pgch_id OR sp.paga_docu_id = spt.trans_id) 
									LEFT OUTER JOIN
										(
											SELECT
												spd.paga_id,
												spd.padt_id_p_ac,
												spd.padt_cod_aesp,
												spd.padt_monto
											FROM 
												sai_pagado_dt spd
											WHERE
												spd.pres_anno = ".$pressAnno." AND 
												spd.part_id NOT LIKE '4.11.0%' AND 
												spd.padt_id_p_ac = s.id_proyecto_accion AND 
												spd.padt_cod_aesp = s.id_accion_especifica
										) AS spd
										ON (sp.paga_id = spd.paga_id)
								GROUP BY 
									spc.nro_cuenta,
									spt.nro_cuenta_emisor,
									spc.pgch_id,
									spt.trans_id
					 			UNION
					 			SELECT 
									COALESCE(SUM(spd.padt_monto),0)||'|| ||'||COALESCE(sci.comp_id,' ')
					 			FROM 
									(
										SELECT
											sci.comp_id											
										FROM
											sai_codi sci
										WHERE
											sci.nro_compromiso = s.comp_id ";
				if ( $fechaInicio!=null && $fechaInicio!='' && $fechaFin!=null && $fechaFin!='' ) {
					$query .=				"AND sci.comp_fec BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY') ";
				}
				$query .= 			") AS sci
									LEFT OUTER JOIN
										(
											SELECT
												sp.paga_id,
												sp.paga_docu_id
											FROM 
												sai_pagado sp 
											WHERE
												sp.pres_anno = ".$pressAnno." AND
												sp.esta_id <> 2 ";
				if ( $fechaInicio!=null && $fechaInicio!='' && $fechaFin!=null && $fechaFin!='' ) {
					$query .=					" AND CAST(sp.paga_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND
												(sp.fecha_anulacion IS NULL OR (CAST(sp.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) ";
					//$query .= 					"AND sp.paga_fecha BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY')+1 ";
				} else {
					$query .=					" AND sp.esta_id <> 15 ";	
				}
				$query .= 				") AS sp
										ON (sci.comp_id = sp.paga_docu_id)
									LEFT OUTER JOIN
										(
											SELECT
												spd.paga_id,
												spd.padt_id_p_ac,
												spd.padt_cod_aesp,
												spd.padt_monto
											FROM 
												sai_pagado_dt spd
											WHERE
												spd.pres_anno = ".$pressAnno." AND 
												spd.part_id NOT LIKE '4.11.0%' AND 
												spd.padt_id_p_ac = s.id_proyecto_accion AND 
												spd.padt_cod_aesp = s.id_accion_especifica
										) AS spd
										ON (sp.paga_id = spd.paga_id)										
								GROUP BY sci.comp_id
							) AS pagados ";
			}
			$query .=	"	FROM
							(
							SELECT
								sci.comp_id,
								sc.comp_fecha,
								TO_CHAR(sc.comp_fecha, 'DD/MM/YYYY') AS fecha,
								sc.pcta_id AS pcta_id,
								COALESCE(sae.centro_gestor,'') AS centro_gestor,
								COALESCE(sae.centro_costo,'') AS centro_costo,
								TO_CHAR(sc.fecha_reporte, 'DD/MM/YYYY') AS fecha_reporte,
								sc.esta_id AS estado,
								'' AS infocentro, 
								'' AS tipo_obra,
								SUM(sci.comp_monto) AS monto,
								sae.id_proyecto_accion,
								sae.id_accion_especifica,
								sc.rif_sugerido AS rif_proveedor_sugerido,
								sc.beneficiario,
								sca.cpas_nombre AS asunto,
								se.empl_nombres,
								se.empl_apellidos,
								sd.depe_nombre AS dependencia,
								sc.comp_estatus,
								sc.comp_documento,
								sc.comp_descripcion,
								--sta.nombre AS actividad,
								--ste.nombre AS evento,
								scc.nombre AS control,
								sev.nombre AS localidad,
								sc.comp_observacion,
								TO_CHAR(sc.fecha_inicio, 'DD/MM/YYYY') AS fecha_inicio,
								TO_CHAR(sc.fecha_fin, 'DD/MM/YYYY') AS fecha_fin ";
			if ( $mostrarPartidas=='true' ) {
				$query .= 		", sci.part_id ";
			}
			$query .= 		"FROM ";
			if ( $fechaInicio!=null && $fechaInicio!='' && $fechaFin!=null && $fechaFin!='' ) {
				$query .= 		"(
									SELECT
										scit.comp_id,
										scit.comp_acc_pp,
										scit.comp_acc_esp, ";
				if ( $mostrarPartidas=='true' ) {
					$query .= 			"scit.comp_sub_espe AS part_id, ";
				}
				$query .= 				"SUM(scit.comp_monto) AS comp_monto
									FROM
										(
											SELECT
												scit.comp_id,
												MAX(scit.comp_fecha) AS comp_fecha
											FROM
												sai_comp_imputa_traza scit
											WHERE
												scit.comp_fecha BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY')+1
											GROUP BY 
												scit.comp_id
										) AS s
										INNER JOIN sai_comp_imputa_traza scit ON (scit.comp_id = s.comp_id AND scit.comp_fecha = s.comp_fecha)
									WHERE 
										scit.comp_monto > 0 AND
										s.comp_id NOT IN
											(
												SELECT
													sct.comp_id
												FROM 
													sai_comp_traza sct
												WHERE
													sct.comp_fecha2 BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY')+1 AND
													(sct.esta_id = 15 OR sct.esta_id = 2)  
											)
									GROUP BY 
										scit.comp_id,
										scit.comp_acc_pp,
										scit.comp_acc_esp ";
				if ( $mostrarPartidas=='true' ) {
					$query .= 			", scit.comp_sub_espe ";
				}
				$query .= 		") AS sci ";
			} else {
				$query .= 		"(
									SELECT
										sci.comp_id,
										sci.comp_acc_pp,
										sci.comp_acc_esp, ";
				if ( $mostrarPartidas=='true' ) {
					$query .= 			"sci.comp_sub_espe AS part_id, ";
				}
				$query .= 				"--SUM(sci.comp_monto) AS comp_monto
										 CASE
											WHEN (doc.esta_id = '15') THEN
												0
											ELSE
												SUM(sci.comp_monto)
										END AS comp_monto
									FROM
										sai_comp_imputa sci
										INNER JOIN sai_doc_genera doc ON (doc.docg_id = sci.comp_id)
									WHERE 
										sci.comp_monto > 0
									GROUP BY
										sci.comp_id,
										sci.comp_acc_pp,
										sci.comp_acc_esp,
										doc.esta_id ";
				if ( $mostrarPartidas=='true' ) {
					$query .= 			", sci.comp_sub_espe ";
				}
				$query .= 		") AS sci ";
			}
			$query .= 			"INNER JOIN sai_comp sc ON (sci.comp_id = sc.comp_id)
							--	INNER JOIN sai_comp_traza sc2 ON (sc2.comp_id = sc.comp_id)
								LEFT OUTER JOIN 
								(
									SELECT 
										s.id_proyecto_accion,
										s.id_accion_especifica,
										s.tipo,
										s.pres_anno,
										s.centro_gestor,
										s.centro_costo
									FROM 
										(
											SELECT 
												spae.proy_id AS id_proyecto_accion,
												spae.paes_id AS id_accion_especifica,
												1 AS tipo,
												spae.pres_anno,
												spae.centro_gestor, 
												spae.centro_costo
											FROM 
												sai_proy_a_esp spae 
											WHERE 
												spae.pres_anno = '".$pressAnno."'
											UNION
											SELECT 
												sae.acce_id AS id_proyecto_accion, 
												sae.aces_id AS id_accion_especifica,
												'0' AS tipo,
	     										sae.pres_anno,
												sae.centro_gestor,
												sae.centro_costo
											FROM 
												sai_acce_esp sae 
											WHERE 
												sae.pres_anno = '".$pressAnno."'
										) AS s
									GROUP BY 
										s.id_proyecto_accion,
										s.id_accion_especifica,
										s.tipo,
										s.pres_anno, 
										s.centro_gestor,
										s.centro_costo
								) AS sae ON (sci.comp_acc_pp = sae.id_proyecto_accion AND sci.comp_acc_esp = sae.id_accion_especifica)
								LEFT OUTER JOIN sai_empleado se ON (sc.usua_login = se.empl_cedula)
								LEFT OUTER JOIN sai_compromiso_asunt sca ON (sc.comp_asunto = sca.cpas_id)
								LEFT OUTER JOIN sai_dependenci sd ON (sc.comp_gerencia = sd.depe_id)
								--LEFT OUTER JOIN sai_tipo_actividad sta ON (sc.id_actividad = sta.id)
								--LEFT OUTER JOIN sai_tipo_evento ste ON (sc.id_evento = ste.id)
								LEFT OUTER JOIN sai_control_comp scc ON (sc.control_interno = scc.id)
								LEFT OUTER JOIN safi_edos_venezuela sev ON (sc.localidad = sev.id)
						 	";
			
			/*$hayCondicion = false;
			if ( $fechaInicio!=null && $fechaInicio!='' && $fechaFin!=null && $fechaFin!='' ) {
				$query .= " WHERE
								sc.comp_fecha BETWEEN TO_DATE('".$fechaInicio."', 'DD/MM/YYYY') AND TO_DATE('".$fechaFin."', 'DD/MM/YYYY')+1 ";
				$hayCondicion = true;
			}*/
			$query .= " WHERE
							DATE_PART('year', sc.comp_fecha) = ".$pressAnno." ";
			$hayCondicion = true;
			
			if ( $compId!=null && $compId!='' && $compId!='comp-') {
				if ( $hayCondicion == false ) {
					$query .= " WHERE ";
				} else {
					$query .= " AND ";
				}
				$query .= " sc.comp_id='".$compId."' ";
				$hayCondicion = true;
			}
			
			if ( $asunto != null && $asunto != '' ) {
				if ( $hayCondicion == false ) {
					$query .= " WHERE ";	
				} else {
					$query .= " AND ";
				}
				$query .= " sc.comp_asunto='".$asunto."' ";
				$hayCondicion = true;
			}			

			/*if ( $tipoActividad!=null && $tipoActividad!='' ) {
				if ( $hayCondicion == false ) {
					$query .= " WHERE ";
				} else {
					$query .= " AND ";
				}
				$query .= " sc.id_actividad='".$tipoActividad."' ";
				$hayCondicion = true;
			}*/			

			if ( $estatus!=null && $estatus!='' ){
				if ( $hayCondicion == false ) {
					$query .= " WHERE ";
				} else {
					$query .= " AND ";
				}
				$query .= " sc.comp_estatus='".$estatus."' ";
				$hayCondicion = true;
			}
			
			if ( $proyectoAccion!=null && $proyectoAccion!='' ) {
				list($proyecto,$accionEspecififica) = split(':::', $proyectoAccion);
				if ( $hayCondicion == false ) {
					$query .= " WHERE ";
				} else {
					$query .= " AND ";
				}
				$query .= " sci.comp_acc_pp='".$proyecto."' AND sci.comp_acc_esp='".$accionEspecififica."' ";
				$hayCondicion = true;
			}
			
			if ( $centroGestor!=null && $centroGestor!='' ){
				if ( $hayCondicion == false ) {
					$query .= " WHERE ";
				} else {
					$query .= " AND ";
				}
				$query .= " sae.centro_gestor LIKE '%".$centroGestor."%' ";//REVISAR
				$hayCondicion = true;
			}
			
			if ( $rifProveedor!=null && $rifProveedor!='' ) {
				if ( $hayCondicion == false ) {
					$query .= " WHERE ";
				} else {
					$query .= " AND ";
				}
				$query .= " ( UPPER(sc.rif_sugerido) LIKE UPPER('%".$rifProveedor."%') OR UPPER(sc.beneficiario) LIKE UPPER('%".$rifProveedor."%') ) ";
				$hayCondicion = true;
			}
			
			if ( $palabraClave!=null && $palabraClave!='' ) {
				if ( $hayCondicion == false ) {
					$query .= " WHERE ";
				} else {
					$query .= " AND ";
				}
				$query .= " LOWER(sc.comp_descripcion) LIKE '%'||LOWER('".$palabraClave."')||'%' ";
				$hayCondicion = true;				
			}
			
			$query .= " 	GROUP BY
								sci.comp_id,
								sc.comp_fecha,
								sc.pcta_id,
								sae.centro_gestor,
								sae.centro_costo,
								sc.fecha_reporte,
								sc.esta_id,
								infocentro, 
								tipo_obra,
								sae.id_proyecto_accion,
								sae.id_accion_especifica,
								sc.rif_sugerido,
								sc.beneficiario,
								sca.cpas_nombre,
								se.empl_nombres,
								se.empl_apellidos,
								sd.depe_nombre,
								sc.comp_estatus,
								sc.comp_documento,
								sc.comp_descripcion,
								--sta.nombre,
								--ste.nombre,
								scc.nombre,
								sev.nombre,
								sc.comp_observacion,
								sc.fecha_inicio,
								sc.fecha_fin ";
			if ( $mostrarPartidas=='true' ) {
				$query .= 		", sci.part_id ";
			}
			$query .= 		") AS s					
							ORDER BY 
								s.comp_fecha, s.comp_id ";
			if ( $mostrarPartidas=='true' ) {
				$query .= 		", s.part_id ";
			}
			$resultado=pg_query($conexion,$query) or die("Error al consultar la descripcion del compromiso");
			
?>
		<table width="100%" border="0" align="center">
			<!-- <tr>
				<td height="27" class="normalNegro">
					<div align="center">Resultado de la b&uacute;squeda de compromisos</div>
				</td>
			</tr> -->
			<tr>
				<td class="normalNegrita">
					<strong>Total <?= pg_num_rows($resultado);?> registros</strong>		
				</td>
			</tr>
		</table>		
		<table width="2000px" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td class="normalNegroNegrita" align="center" width="100px">C&oacute;digo del	Documento</td>
				<td class="normalNegroNegrita" align="center">Fecha</td>
				<td class="normalNegroNegrita" align="center">Elaborado Por</td>
				<td class="normalNegroNegrita" align="center">Unidad Solicitante</td>
				<td class="normalNegroNegrita" align="center">Punto de Cuenta</td>
				<td class="normalNegroNegrita" align="center">Asunto</td>
				<td class="normalNegroNegrita" align="center">Estatus</td>
				<td class="normalNegroNegrita" align="center" width="100px">N&deg; Documento</td>
				<td class="normalNegroNegrita" align="center">Proveedor</td>
				<td class="normalNegroNegrita" align="center">CI/RIF</td>
				<td class="normalNegroNegrita" align="center">Centro Gestor</td>
				<td class="normalNegroNegrita" align="center">Centro Costo</td>
		<?php 
			if ( $mostrarPartidas=='true' ) {
		?>
				<td class="normalNegroNegrita" align="center">Partida</td>
		<?php 
			}
		?>
				<td class="normalNegroNegrita" align="center">Monto Solicitado</td>
				<td class="normalNegroNegrita" align="center">Descripci&oacute;n</td>
				<?php /*<td class="normalNegroNegrita" align="center">Tipo Actividad</td>*/?>
				<?php /*<td class="normalNegroNegrita" align="center">Tipo Evento</td>*/?>
				<td class="normalNegroNegrita" align="center">Control Interno</td>
				<td class="normalNegroNegrita" align="center">Estado</td>
				<?php /*<td class="normalNegroNegrita" align="center">Infocentro</td>
				<td class="normalNegroNegrita" align="center">N&deg; Participantes</td>*/?>
				<td class="normalNegroNegrita" align="center">Duraci&oacute;n de la	actividad</td>
				<td class="normalNegroNegrita" align="center">Observaci&oacute;n</td>
				<td class="normalNegroNegrita" align="center">Fecha de Reporte</td>
				<td class="normalNegroNegrita" align="center">Monto Causado</td>
				<td class="normalNegroNegrita" align="center" width="100px">Documentos Causado</td>
		<?php 
			if ( $mostrarPartidas!='true' ) {
		?>
				<td class="normalNegroNegrita" align="center">Monto Pagado</td>
				<td class="normalNegroNegrita" align="center" width="100px">Documentos Pagado</td>
				<td class="normalNegroNegrita" align="center">N&uacute;mero de cuenta</td>		
		<?php 
			}
		?>
			</tr>
			<?
			$totalMontoSolicitado = 0.0;
			$totalMontoCausado = 0.0;
			$totalMontoPagado = 0.0;
			while ( $row=pg_fetch_array($resultado) ) {
				$info_adicional=$row['comp_observacion'];
				$longitud=strlen($info_adicional);
				$info_adicional=substr($info_adicional,1,$longitud);
				$posicion = strpos($info_adicional, ":");
				$posicion2 = strpos($info_adicional, "*");
				
				$infocentro=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
				$info_adicional=substr($info_adicional,$posicion2+1);
				$posicion = strpos($info_adicional, ":");
				$posicion2 = strpos($info_adicional, "*");
					
				$participante=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
				$info_adicional=substr($info_adicional,$posicion2+1);
				$posicion = strpos($info_adicional, ":");
				$posicion2 = strpos($info_adicional, "*");
				$observacion=substr($info_adicional,$posicion+1);
			?>
					<tr class="normal datos">
						<td align="center">
							<a href="javascript:abrir_ventana('comp_detalle.php?codigo=<?= trim($row['comp_id']); ?>')" class="copyright">
								<?= $row['comp_id'] ;?>
							</a>
						</td>
						<td align="center" class="peq"><?= $row['fecha'];?></td>
						<td class="peq"><?= $row['empl_nombres']." ".$row['empl_apellidos'];?></td>
						<td class="peq"><?= $row['dependencia'];?></td>
						<td align="center" class="peq">
			<?php 
							if ( $row['pcta_id'] == null || $row['pcta_id'] == '' || $row['pcta_id'] == '0' ){
								echo "N/A";
							} else {
								echo $row['pcta_id'];
							}
			?>
						</td>
						<td class="peq"><?= $row['asunto'];?></td>
						<td align="center" class="peq"><?= $row['comp_estatus'];?></td>
						<td align="center" class="peq"><?= str_replace("/", "<br/>", $row['comp_documento']);?></td>
						<td class="peq"><?= $row['beneficiario'];?></td>
						<td align="center" class="peq"><?= $row['rif_proveedor_sugerido'];?></td>
						<td align="center" class="peq"><?= $row['centro_gestor'];?></td>
						<td align="center" class="peq"><?= $row['centro_costo'];?></td>
			<?php 
				if ( $mostrarPartidas=='true' ) {
			?>
						<td align="center" class="peq"><?= $row['part_id']; ?></td>
			<?php 
				}
			?>
						<td align="right" class="peq"><?= (number_format($row['monto'],2,',','.')); ?></td>
						<td align="justify" class="peq"><?= $row['comp_descripcion'];?></td>
						<?php /*<td align="center" class="peq"><?= $row['actividad'];?></td>*/?>
						<?php /*<td align="center" class="peq"><?= $row['evento']; ?></td>*/?>
						<td align="center" class="peq"><?= $row['control'];?></td>
						<td align="center" class="peq">
			<?php
							if ( $row['localidad']==null || $row['localidad']=='' ){
								echo "N/A";
							} else {
								echo $row['localidad'];
							}
			?>
						</td>
						<?php /*<td align="center" class="peq"><?= $infocentro; ?></td>
						<td align="center" class="peq"><?= $participante;?></td>*/?>
						<td align="center" class="peq"><?= $row['fecha_inicio']."<br/>".$row['fecha_fin'];?></td>
						<td class="peq"><?= $observacion;?></td>
						<td align="center" class="peq"><?= $row['fecha_reporte'];?></td>
			<?php 
						$montoCausado=0;
						$montoPagado=0;
						$sopgsId="";
						$pagadosId="";
						$nroCuentas="";
						
						if ( isset($row['causados']) ) {
							$causados = pg_array_parse($row['causados'], $causados);
							$i = 0;
							while ( $i < sizeof($causados) ) {
								$causado = explode("||", $causados[$i]);
								if ( isset($causado[0]) ) {
									$montoCausado += $causado[0];
								}
								if ( isset($causado[1]) ) {
									$sopgsId .= $causado[1]."<br/>";
								}
								$i++;
							}
						}
						if ( isset($row['pagados']) ) {
							$pagados = pg_array_parse($row['pagados'], $pagados);
							$i = 0;
							while ( $i < sizeof($pagados) ) {
								$pagado = explode("||", $pagados[$i]);
								if ( isset($pagado[0]) ) {
									$montoPagado += $pagado[0];
								}
								if ( isset($pagado[1]) ) {
									$nroCuentas .= $pagado[1]."<br/>";
								}
								if ( isset($pagado[2]) ) {
									$pagadosId .= $pagado[2]."<br/>";
								}
								$i++;
							}
						}
	    ?>
						<td align="right" class="peq">
							<a target="_blank" href="detalleCausadoProv.php?comp=<?=$row['comp_id']?>&aopres=<?=$pressAnno?>&monto=<?=$montoCausado?>">
								<?= (number_format($montoCausado,2,',','.')); ?>
							</a>
						</td>
						<td align="center" class="peq"><?= $sopgsId; ?></td>
		<?php 
				if ( $mostrarPartidas!='true' ) {
		?>
						<td align="right" class="peq">
							<a target="_blank" href="detallePagadoProv.php?comp=<?=$row['comp_id']?>&aopres=<?=$pressAnno?>&monto=<?=$montoPagado?>">
								<?= (number_format($montoPagado,2,',','.')); ?>
							</a>
						</td>
						<td align="center" class="peq"><?= $pagadosId; ?></td>
						<td align="center" class="peq"><?= $nroCuentas;?></td>		
		<?php 
				}
		?>
					</tr>
		<?php 
				$totalMontoSolicitado += $row['monto'];
				$totalMontoCausado += $montoCausado;
				$totalMontoPagado += $montoPagado;
			}
		?>
					<tr class="normal datos">
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
			<?php 
				if ( $mostrarPartidas=='true' ) {
			?>
						<td align="right"></td>
			<?php 
				}
			?>
						<td align="center"><b>Total</b></td>
						<td align="right" class="peq"><b><?= (number_format($totalMontoSolicitado,2,',','.')); ?></b></td>
						<td align="justify" class="peq"></td>
						<?php /*<td align="center" class="peq"></td>*/?>
						<?php /*<td align="center" class="peq"></td>*/?>
						<td align="center" class="peq"></td>
						<td align="center" class="peq"></td>
						<?php /*<td align="center" class="peq"></td>
						<td align="center" class="peq"></td>*/?>
						<td align="center" class="peq"></td>
						<td class="peq"></td>
						<td align="center" class="peq"></td>
						<td align="right" class="peq"><b><?= (number_format($totalMontoCausado,2,',','.')); ?></b></td>
						<td align="center" class="peq"></td>
		<?php 
			if ( $mostrarPartidas!='true' ) {
		?>
						<td align="right" class="peq"><b><?= (number_format($totalMontoPagado,2,',','.')); ?></b></td>
						<td align="center" class="peq"></td>
						<td align="center" class="peq"></td>		
		<?php 
			}
		?>
					</tr>
		<?php 	
		}
?>
		</table>
	</form>
</body>
</html>