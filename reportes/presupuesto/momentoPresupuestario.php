<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");

if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();

$usuario = $_SESSION['login'];
$user_perfil_id = $_SESSION['user_perfil_id'];
$pres_anno = $_SESSION['an_o_presupuesto'];
//$pres_anno = 2014;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Momento Presupuestario</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet" />
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script>
function detalle(codigo) {
 url="detalle.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}

function ejecutar() { 
	document.form.hid_validar.value=2;
 	document.form.submit();
}

function validar(op){
	document.form1.hid_validar.value=2;
	if(op==1){
		document.form1.action="momentoPresupuestario.php";		
	}else if(op==3){
		document.form1.action="momentoPresupuestarioPDF.php";
	}else if(op==4){
		document.form1.action="momentoPresupuestarioXLS.php";
	}
	document.form1.submit();
}
</script>
</head>
<body>
<form name="form1" method="post">
	<input type="hidden" value="0" name="hid_validar" />
	<table width="515" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td height="21" colspan="2" class="normalNegroNegrita" align="left">
				B&Uacute;SQUEDA DE MOMENTOS PRESUPUESTARIOS
			</td>
		</tr>
	<tr>
		<td height="10" colspan="3"></td>
	</tr>
	<tr>
		<td width="175" height="29" class="normalNegrita" align="left">
			Solicitados entre:
		</td>
		<td>
			<input type="text" size="10" id="fechaInicio" name="fechaInicio" class="dateparse"
				onfocus="javascript: comparar_fechas(this.value,document.getElementById('fechaFin').value);"
				readonly="readonly" value="<?= $_REQUEST["fechaInicio"];?>"/>
			<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaInicio');" title="Show popup calendar">
				<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar" /></a>
			<input type="text" size="10" id="fechaFin" name="fechaFin" class="dateparse"
				onfocus="javascript: comparar_fechas(this.value,document.getElementById('fechaInicio').value);"
				readonly="readonly" value="<?= $_REQUEST["fechaFin"];?>" />
			<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaFin');" title="Show popup calendar">
				<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar" /></a>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita" align="left">Compromiso:</td>
		<td>
			<span class="normalNegrita">
				<input name="compromiso" type="text" class="normal" id="compromiso" value="comp-<?= substr($_REQUEST["compromiso"],5);?>" size="15" />
			</span>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita" align="left">Punto de cuenta:</td>
		<td>
			<span class="normalNegrita">
				<input name="pcta" type="text" class="normal" id="pcta" value="<?= $_REQUEST["pcta"];?>" size="15" />
			</span>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita" align="left">A&ntilde;o Presupuestario:</td>
		<td class="normalNegrita">
			<?= $pres_anno;?>
			<input type="hidden" name="ano" id="ano" value="<?= $pres_anno;?>"/>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita" align="left">
			Proyecto/Acc espec&iacute;fica:
		</td>
		<td>
			<span class="normalNegrita">
				<select name='proyac' class="normal">
					<option value="0">Todos</option>
					<?php
						$sql = "SELECT 
									pres_anno, 
									acce_id AS proyecto, 
									aces_id AS especifica, 
									centro_gestor, 
									centro_costo 
								FROM sai_acce_esp 
								WHERE 
									pres_anno = ".$pres_anno." 
								UNION
								SELECT 
									pres_anno, 
									proy_id AS proyecto, 
									paes_id AS especifica, 
									centro_gestor, 
									centro_costo 
								FROM sai_proy_a_esp 
								WHERE 
									pres_anno=".$pres_anno." 
								ORDER BY 
									pres_anno DESC, 
									centro_gestor, 
									centro_costo";
						$resultado_set=pg_query($conexion,$sql) or die("Error al consultar los proyectos");
						while($rowor=pg_fetch_array($resultado_set)) {
							if($rowor['proyecto'].":::".$rowor['especifica']==$_REQUEST['proyac']){
					?>
								<option value="<?= $rowor['proyecto'].":::".$rowor['especifica']?>" selected="selected"><?= ($rowor['centro_gestor'].'/'.$rowor['centro_costo']);?></option>
					<?php
							}else{
					?>
								<option value="<?= $rowor['proyecto'].":::".$rowor['especifica']?>"><?= ($rowor['centro_gestor'].'/'.$rowor['centro_costo']);?></option>
					<?php
							}
						}
					?>
				</select>
			</span>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita" align="left">Partida:</td>
		<td>
			<span class="normalNegrita">
				<input name="partida" type="text" class="normal" id="partida" value="<?= $_REQUEST['partida'];?>" size="25"/>
			</span>
		</td>
	</tr>
	<tr>
		<td colspan=5 class="normal" align="center">
			<input type="button" value="Buscar" onclick="validar(1);" /><!-- <input type="button" value="PDF" onclick="validar(3);"/> -->
		 <input type="button" value="Hoja de C&aacute;lculo" onclick="validar(4);"/> </center></td>
		</td>
	</tr>
</table>
</form>
<br />
<form name="form3" action="" method="post">
<?php 




if ($_REQUEST['hid_validar']==2) {
	if (strlen($_REQUEST['fechaInicio'])>5) {
		$fechaInicio=trim($_REQUEST['fechaInicio']);
		$fechaFin=trim($_REQUEST['fechaFin']);
		$fechaInicio=substr($fechaInicio,6,4)."-".substr($fechaInicio,3,2)."-".substr($fechaInicio,0,2);
		$fechaFin=substr($fechaFin,6,4)."-".substr($fechaFin,3,2)."-".substr($fechaFin,0,2);
	}

	$suma_apart_monto=0;
	$suma_comp_monto=0;
	$suma_caus_monto=0;
	$suma_pag_monto=0;

	$ano = $_REQUEST['ano'];

	if (strlen($_REQUEST['fechaInicio'])<5) {
		$fechaInicio=$_REQUEST['ano']."-01-01";
		$fechaFin=$_REQUEST['ano']."-12-31";
	}

	if (strlen($_REQUEST['proyac'])>8) {
		list( $idProyectoAccion, $idAccionEspecifica ) = split( ':::', $_REQUEST['proyac'] );
	}

	$sql_apart="	SELECT 
						nucleo.codigo, 
						nucleo.partida AS partida, 
						nucleo.pcta_asociado,
						nucleo.monto, 
						nucleo.fecha, 
						COALESCE(p.centro_gestor,'')||COALESCE(a.centro_gestor,'') || '/'|| COALESCE(p.centro_costo,'')||COALESCE(a.centro_costo,'') AS centro
					FROM 
						(
							SELECT 
								a.pcta_id AS codigo, 
								b.pcta_asociado AS pcta_asociado, 
								a.pcta_monto AS monto, 
								a.pcta_acc_pp, 
								a. pcta_acc_esp, 
								a.pcta_sub_espe AS partida, 
								TO_CHAR(b.pcta_fecha, 'DD/MM/YYYY') AS fecha
							FROM 
								sai_pcta_imputa_traza a, 
								sai_pcta_traza b
							WHERE 
								a.pres_anno=".$ano." AND 
								a.pcta_id=b.pcta_id AND 
								a.pres_anno=".$ano. 
								(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" AND a.pcta_id LIKE '%".$_REQUEST['compromiso']."%' ":"").
								(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?" AND a.pcta_id LIKE '%".$_REQUEST['pcta']."%' ":"").
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND TO_TIMESTAMP(b.pcta_fecha, 'YYYY/MM/DD HH24:MI:SS') BETWEEN TO_TIMESTAMP('".$fechaInicio." 00:00:00', 'YYYY/MM/DD HH24:MI:SS') AND TO_TIMESTAMP('".$fechaFin." 24:59:59', 'YYYY/MM/DD HH24:MI:SS') ":"").
								(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" AND a.pcta_acc_pp='".$idProyectoAccion."' AND a.pcta_acc_esp='".$idAccionEspecifica."' ":"").
								(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" AND a.pcta_sub_espe LIKE '".$_REQUEST['partida']."%' ":"").
							"ORDER BY 
								a.pcta_id, 
								a.pcta_sub_espe
						) AS nucleo
						LEFT OUTER JOIN sai_proy_a_esp p ON (nucleo.pcta_acc_pp=p.proy_id AND nucleo.pcta_acc_esp=p.paes_id)
						LEFT OUTER JOIN sai_acce_esp a ON (nucleo.pcta_acc_esp = a.aces_id AND nucleo.pcta_acc_pp=a.acce_id)";
	$resultado_set_most_apart=pg_query($conexion,$sql_apart) or die("Error al consultar la descripcion del apartado");

	$sql_comp="	SELECT 
					nucleo.codigo, 
					nucleo.partida AS partida, 
					nucleo.monto, 
					nucleo.fecha, 
					COALESCE(p.centro_gestor,'')||COALESCE(a.centro_gestor,'') || '/'|| COALESCE(p.centro_costo,'')||COALESCE(a.centro_costo,'') AS centro, 
					nucleo.pcta_id AS pcta_id
				FROM
					(
						SELECT 
							a.comp_id AS codigo, 
							a.comp_monto AS monto, 
							a.comp_acc_pp, 
							a.comp_acc_esp, 
							a.comp_sub_espe AS partida, 
							TO_CHAR(a.comp_fecha, 'DD/MM/YYYY') AS fecha, 
							c.pcta_id AS pcta_id
						FROM 
							sai_comp_traza_reporte a, 
							sai_comp c
						WHERE 
							length(c.pcta_id)>4 AND 
							c.comp_id=a.comp_id AND 
							a.pres_anno=".$ano." AND 
							a.pres_anno=".$ano. 
							(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" AND a.comp_id LIKE '%".$_REQUEST['compromiso']."' ":"").
							(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?" AND c.pcta_id LIKE '%".$_REQUEST['pcta']."%' ":"").
							(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND a.comp_fecha BETWEEN TO_TIMESTAMP('".$fechaInicio." 00:00:00','YYYY/MM/DD HH24:MI:SS') AND TO_TIMESTAMP('".$fechaFin." 24:59:59', 'YYYY/MM/DD HH24:MI:SS')":"").
							(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" AND a.comp_acc_pp='".$idProyectoAccion."' AND a.comp_acc_esp='".$idAccionEspecifica."' ":"").
							(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" AND a.comp_sub_espe LIKE '".$_REQUEST['partida']."%' ":"").
						"ORDER BY 
							a.comp_id, 
							a.comp_sub_espe
					) AS nucleo
				LEFT OUTER JOIN sai_proy_a_esp p ON (nucleo.comp_acc_pp=p.proy_id AND nucleo.comp_acc_esp=p.paes_id)
				LEFT OUTER JOIN sai_acce_esp a ON (nucleo.comp_acc_esp = a.aces_id AND nucleo.comp_acc_pp=a.acce_id)";
	$resultado_set_most_comp=pg_query($conexion,$sql_comp) or die("Error al consultar la descripcion del compromiso");

	$sql_comp_ais="	SELECT 
						nucleo.codigo, 
						nucleo.partida AS partida, 
						nucleo.monto, 
						nucleo.fecha, 
						COALESCE(p.centro_gestor,'')||COALESCE(a.centro_gestor,'') || '/'|| COALESCE(p.centro_costo,'')||COALESCE(a.centro_costo,'') AS centro, 
						nucleo.pcta_id AS pcta_id
					FROM 
						(
							SELECT 
								a.comp_id AS codigo, 
								a.comp_monto AS monto, 
								a.comp_acc_pp, 
								a. comp_acc_esp, 
								a.comp_sub_espe AS partida, 
								TO_CHAR(a.comp_fecha, 'DD/MM/YYYY') AS fecha, 
								c.pcta_id AS pcta_id
							FROM 
								sai_comp_traza_reporte a, 
								sai_comp c
							WHERE 
								length(c.pcta_id)<4 AND 
								c.comp_id=a.comp_id AND 
								a.pres_anno=".$ano." AND 
								a.pres_anno=".$ano. 
								(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" AND a.comp_id LIKE '%".$_REQUEST['compromiso']."' ":"").
								(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?" AND c.pcta_id LIKE '%".$_REQUEST['pcta']."%' ":"").
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND a.comp_fecha BETWEEN TO_TIMESTAMP('".$fechaInicio." 00:00:00','YYYY/MM/DD HH24:MI:SS') AND TO_TIMESTAMP('".$fechaFin." 24:59:59', 'YYYY/MM/DD HH24:MI:SS')":"").
								(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" AND a.comp_acc_pp='".$idProyectoAccion."' AND a.comp_acc_esp='".$idAccionEspecifica."' ":"").
								(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" AND a.comp_sub_espe LIKE '".$_REQUEST['partida']."%' ":"").
							"ORDER BY 
								a.comp_id, a
								.comp_sub_espe
						) AS nucleo
					LEFT OUTER JOIN sai_proy_a_esp p ON (nucleo.comp_acc_pp=p.proy_id AND nucleo.comp_acc_esp=p.paes_id)
					LEFT OUTER JOIN sai_acce_esp a ON (nucleo.comp_acc_esp = a.aces_id AND nucleo.comp_acc_pp=a.acce_id)";
	$resultado_set_most_comp_ais=pg_query($conexion,$sql_comp_ais) or die("Error al consultar la descripcion del compromiso");

	$sql_caus2="	SELECT 
						c.caus_docu_id AS causado, 
						s.sopg_id AS codigo, 
						COALESCE(s.comp_id,'') AS comp_id,
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') AS fecha, 
						cd.part_id AS partida, 
						cd.cadt_monto AS monto,
						c.caus_fecha AS fecha_date 
					FROM 
						(
							SELECT * 
							FROM sai_causado c
							WHERE 
								c.pres_anno='".$ano."' AND 
								c.esta_id<>15 AND 
								c.esta_id<>2 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?"AND c.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").
						") AS c
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd
								WHERE
								 	".((strlen($_REQUEST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_REQUEST['partida'])>1)?"cd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM 
									sai_sol_pago s
								WHERE 
									".(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?"s.comp_id LIKE '".$_REQUEST['compromiso']."' AND ":"")."
									".(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										"s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											) AND "
										:"")."
									s.esta_id<>15
							) AS s ON (c.caus_docu_id = s.sopg_id), 
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						cd.cadt_id_p_ac = pa.id_proyecto_accion AND 
						cd.cadt_cod_aesp = pa.id_accion_especifica
					UNION
					SELECT 
						c.caus_docu_id AS causado,
						cdi.comp_id AS codigo, 
						codi.nro_compromiso AS comp_id,
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						dg.numero_reserva AS reserva, 
						TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') AS fecha, 
						cd.part_id AS partida, 
						cd.cadt_monto AS monto,
						c.caus_fecha AS fecha_date  
					FROM 
						(
							SELECT * 
							FROM sai_causado c
							WHERE 
								c.pres_anno='".$ano."' AND 
								c.esta_id<>15 AND 
								c.esta_id<>2 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?"AND c.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").
						") AS c
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd
								WHERE
								 	".((strlen($_REQUEST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_REQUEST['partida'])>1)?"cd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN
							(
								SELECT *
								FROM sai_comp_diario cdi
								WHERE 
									cdi.esta_id<>15 ".
									((strlen($_REQUEST['pcta'])>5)?
										"AND (
												cdi.comp_doc_id IN 
												(
													SELECT sopg_id 
													FROM sai_sol_pago 
													WHERE comp_id IN 
														(
															SELECT comp_id 
															FROM sai_comp_traza 
															WHERE pcta_id LIKE '%".$_REQUEST['pcta']."%'
														)
												) OR 
												cdi.comp_doc_id IN 
												(
													SELECT comp_id 
													FROM sai_comp 
													WHERE pcta_id = '%".$_REQUEST['pcta']."%'
												)
											)":"")."
							) AS cdi ON (c.caus_docu_id = cdi.comp_id)
						INNER JOIN sai_codi codi ON (c.caus_docu_id = codi.comp_id)
						INNER JOIN sai_doc_genera dg ON (c.caus_docu_id = dg.docg_id AND dg.esta_id<>15), 
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						cd.cadt_id_p_ac = pa.id_proyecto_accion AND 
						cd.cadt_cod_aesp = pa.id_accion_especifica ".
						((strlen($_REQUEST['compromiso'])>5)?
							"AND (
									cdi.comp_doc_id IN 
									(
										SELECT sopg_id 
										FROM sai_sol_pago 
										WHERE comp_id LIKE '%".$_REQUEST['compromiso']."'
									) OR 
									codi.nro_compromiso LIKE '%".$_REQUEST['compromiso']."'
								)
							":"")."
					UNION
					SELECT 
						c.caus_docu_id AS causado,
						s.sopg_id AS codigo,
						COALESCE(s.comp_id,'') AS comp_id,
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						--TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') AS fecha,
						TO_CHAR(c.fecha_anulacion, 'DD/MM/YYYY') AS fecha, 
						cd.part_id AS partida, 
						cd.cadt_monto*-1 AS monto,
						c.fecha_anulacion AS fecha_date  
					FROM 
						(
							SELECT * 
							FROM sai_causado c
							WHERE 
								c.pres_anno='".$ano."' AND 
								c.esta_id=15 AND
								SUBSTRING(TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') FROM 4 for 7) <> SUBSTRING(TO_CHAR(c.fecha_anulacion, 'DD/MM/YYYY') FROM 4 for 7) ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?
									"AND c.caus_fecha <= TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND 
									c.fecha_anulacion BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").								
						") AS c
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd 
								WHERE
								 	".((strlen($_REQUEST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_REQUEST['partida'])>1)?"cd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM 
									sai_sol_pago s
								WHERE 
									".(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?"s.comp_id LIKE '".$_REQUEST['compromiso']."' AND ":"")."
									".(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										"s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											) AND ":"")."
									(s.esta_id=15 OR s.esta_id=7)
							) AS s ON (c.caus_docu_id = s.sopg_id), 
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						cd.cadt_id_p_ac = pa.id_proyecto_accion AND 
						cd.cadt_cod_aesp = pa.id_accion_especifica						 
					UNION
					SELECT 
						c.caus_docu_id AS causado,
						s.sopg_id AS codigo, 
						COALESCE(s.comp_id,'') AS comp_id,
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') AS fecha,
						cd.part_id AS partida, 
						cd.cadt_monto AS monto,
						c.caus_fecha AS fecha_date  
					FROM 
						(
							SELECT * 
							FROM sai_causado c
							WHERE 
								c.pres_anno='".$ano."' AND 
								c.esta_id=15 AND 
								SUBSTRING(TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') FROM 4 for 7) <> SUBSTRING(TO_CHAR(c.fecha_anulacion, 'DD/MM/YYYY') FROM 4 for 7) ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?
									"AND c.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') AND
									c.fecha_anulacion > TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":""). 
						") AS c 
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd 
								WHERE
								 	".((strlen($_REQUEST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_REQUEST['partida'])>1)?"cd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM 
									sai_sol_pago s
								WHERE 
									".(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?"s.comp_id LIKE '".$_REQUEST['compromiso']."' AND ":"")."
									".(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										"s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											) AND ":"")."
									(s.esta_id=15 OR s.esta_id=7)
							) AS s ON (c.caus_docu_id = s.sopg_id), 
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						cd.cadt_id_p_ac = pa.id_proyecto_accion AND 
						cd.cadt_cod_aesp = pa.id_accion_especifica
					UNION
					SELECT 
						c.caus_docu_id AS causado,
						s.sopg_id AS codigo, 
						COALESCE(s.comp_id,'') AS comp_id,
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') AS fecha,
						cd.part_id AS partida, 
						cd.cadt_monto AS monto,
						c.caus_fecha AS fecha_date  
					FROM 
						(
							SELECT * 
							FROM sai_causado c
							WHERE 
								c.pres_anno='".$ano."' AND 
								c.esta_id=15 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?
									"AND c.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') AND 
									c.fecha_anulacion <= TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").
						") AS c 
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd 
								WHERE
								 	".((strlen($_REQUEST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_REQUEST['partida'])>1)?"cd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM 
									sai_sol_pago s
								WHERE 
									".(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?"s.comp_id LIKE '".$_REQUEST['compromiso']."' AND ":"")."
									".(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										"s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											) AND ":"")."
									(s.esta_id=15 OR s.esta_id=7)
							) AS s ON (c.caus_docu_id = s.sopg_id), 
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						cd.cadt_id_p_ac = pa.id_proyecto_accion AND 
						cd.cadt_cod_aesp = pa.id_accion_especifica 
					UNION
					SELECT 
						c.caus_docu_id AS causado,
						s.sopg_id AS codigo, 
						COALESCE(s.comp_id,'') AS comp_id,
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						--TO_CHAR(c.caus_fecha, 'DD/MM/YYYY') AS fecha, 
						TO_CHAR(c.fecha_anulacion, 'DD/MM/YYYY') AS fecha,
						cd.part_id AS partida, 
						cd.cadt_monto*-1 AS monto,
						c.fecha_anulacion AS fecha_date  
					FROM 
						(
							SELECT * 
							FROM sai_causado c
							WHERE 
								c.pres_anno='".$ano."' AND 
								c.esta_id=15 ". 
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?
									"AND c.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') AND 
									c.fecha_anulacion <= TO_DATE('".$fechaFin."', 'YYYY-MM-DD')":""). 
						") AS c 
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd 
								WHERE
								 	".((strlen($_REQUEST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_REQUEST['partida'])>1)?"cd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM 
									sai_sol_pago s
								WHERE 
									".(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?"s.comp_id LIKE '".$_REQUEST['compromiso']."' AND ":"")."
									".(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										"s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											) AND ":"")."
									(s.esta_id=15 OR s.esta_id=7)
							) AS s ON (c.caus_docu_id = s.sopg_id), 
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						cd.cadt_id_p_ac = pa.id_proyecto_accion AND 
						cd.cadt_cod_aesp = pa.id_accion_especifica ";
	$sql_caus = "SELECT * FROM (".$sql_caus2.") AS s ORDER BY fecha_date,partida";//causado,partida
	$resultado_set_most_caus=pg_query($conexion,$sql_caus) or die("Error al consultar la descripcion del causado");

	if (strlen($_REQUEST['fechaInicio'])>1) {
		$sql_pag2="	SELECT 
						p.paga_docu_id AS pagado, 
						pc.pgch_id AS codigo, 
						s.comp_id AS comp_id, 
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
						pd.part_id AS partida, 
						pd.padt_monto AS monto,
						p.paga_fecha AS fecha_date
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id<>15 AND 
								p.esta_id<>2 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."','YYYY-MM-DD') AND TO_DATE('".$fechaFin."','YYYY-MM-DD') ":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
							) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM sai_pago_cheque pc
							) AS pc ON (p.paga_docu_id = pc.pgch_id)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM sai_sol_pago s
								WHERE 
									s.esta_id <> 15 ".
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										"AND s.comp_id IN 
										(
											SELECT comp_id 
											FROM sai_comp_traza 
											WHERE 
												esta_id<>2 AND 
												pcta_id LIKE '%".$_REQUEST['pcta']."%'
										)":""
									).
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" AND s.comp_id LIKE '%".$_REQUEST['compromiso']."'":"").
							") AS s ON (pc.docg_id = s.sopg_id), 
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND
						pd.padt_cod_aesp = pa.id_accion_especifica 
					UNION
					SELECT 
						p.paga_docu_id AS pagado, 
						ptr.trans_id AS codigo, 
						s.comp_id AS comp_id, 
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
						pd.part_id AS partida, 
						pd.padt_monto AS monto,
						p.paga_fecha AS fecha_date
					FROM
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id<>15 AND 
								p.esta_id<>2 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."','YYYY-MM-DD') AND TO_DATE('".$fechaFin."','YYYY-MM-DD') ":"").
						") AS p 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
							) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pago_transferencia ptr 
							WHERE
								ptr.esta_id<>15 ".
						") AS ptr ON (p.paga_docu_id = ptr.trans_id)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM sai_sol_pago s
								WHERE 
									s.esta_id <> 15 ".
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										" AND s.comp_id IN 
										(
											SELECT comp_id 
											FROM sai_comp_traza 
											WHERE 
												esta_id<>2 AND 
												pcta_id LIKE '%".$_REQUEST['pcta']."%'
										)":""
									).
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" AND s.comp_id LIKE '%".$_REQUEST['compromiso']."'":"").
							") AS s ON (ptr.docg_id = s.sopg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica  
					UNION
					SELECT 
						p.paga_docu_id AS pagado, 
						cdi.comp_id AS codigo, 
						codi.nro_compromiso AS comp_id, 
 						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
 						dg.numero_reserva AS reserva, 
 						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
 						pd.part_id AS partida, 
 						pd.padt_monto AS monto,
						p.paga_fecha AS fecha_date
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id<>15 AND 
								p.esta_id<>2 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."','YYYY-MM-DD') AND TO_DATE('".$fechaFin."','YYYY-MM-DD') ":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_comp_diario cdi 
								WHERE
									cdi.esta_id<>15 ".
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
									" AND (
											cdi.comp_doc_id IN 
											(
												SELECT sopg_id 
												FROM sai_sol_pago 
												WHERE comp_id IN 
													(
														SELECT comp_id 
														FROM sai_comp_traza 
														WHERE pcta_id LIKE '%".$_REQUEST['pcta']."%'
													)
											) OR 
											cdi.comp_doc_id IN 
											(
												SELECT comp_id 
												FROM sai_comp 
												WHERE pcta_id = '%".$_REQUEST['pcta']."%'
											)
									)":"").
						") AS cdi ON (p.paga_docu_id = cdi.comp_id)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_codi codi ".
						") AS codi ON (cdi.comp_id = codi.comp_id)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_doc_genera dg 
								WHERE
									dg.esta_id<>15 ".
						") AS dg ON (p.paga_docu_id = dg.docg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica ".
						(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?
						" AND (
								cdi.comp_doc_id IN 
								(
									SELECT sopg_id 
									FROM sai_sol_pago 
									WHERE comp_id LIKE '%".$_REQUEST['compromiso']."'
								) OR 
								codi.nro_compromiso LIKE '%".$_REQUEST['compromiso']."'
						) ":"").
					"UNION
					SELECT 
						p.paga_docu_id AS pagado, 
						pc.pgch_id AS codigo, 
						s.comp_id AS comp_id, 
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						--TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
						TO_CHAR(p.fecha_anulacion, 'DD/MM/YYYY') AS fecha,
						pd.part_id AS partida, 
						pd.padt_monto*-1 AS monto,
						p.fecha_anulacion AS fecha_date
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id=15 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND SUBSTRING(TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') FROM 4 for 7) <> SUBSTRING(TO_CHAR(p.fecha_anulacion, 'DD/MM/YYYY') FROM 4 for 7) AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaFin."'":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pago_cheque pc 
								WHERE
									(pc.esta_id=15 OR pc.esta_id=7) ".
						") AS pc ON (p.paga_docu_id = pc.pgch_id)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_sol_pago s ".
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" WHERE s.comp_id LIKE '%".$_REQUEST['compromiso']."'":"").
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										" WHERE s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											)":"").
						") AS s ON (pc.docg_id = s.sopg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica
					UNION
					SELECT 
						p.paga_docu_id AS pagado,
						ptr.trans_id AS codigo, 
						s.comp_id AS comp_id, 
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						--TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha,
 						TO_CHAR(p.fecha_anulacion, 'DD/MM/YYYY') AS fecha, 
						pd.part_id AS partida, 
						pd.padt_monto*-1 AS monto,
						p.fecha_anulacion AS fecha_date
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id=15 ". 
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND SUBSTRING(TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') FROM 4 for 7) <> SUBSTRING(TO_CHAR(p.fecha_anulacion, 'DD/MM/YYYY') FROM 4 for 7) AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaFin."'":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pago_transferencia ptr
								WHERE 
									(ptr.esta_id=15 OR ptr.esta_id=7)
						) AS ptr ON (p.paga_docu_id = ptr.trans_id)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_sol_pago s ".
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" WHERE s.comp_id LIKE '%".$_REQUEST['compromiso']."' ":"").
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										" WHERE s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											) ":"").
						") AS s ON (ptr.docg_id = s.sopg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica 
					UNION 
					SELECT 
						p.paga_docu_id AS pagado,
						pc.pgch_id AS codigo, 
						s.comp_id AS comp_id, 
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
						pd.part_id AS partida, 
						pd.padt_monto AS monto,
						p.paga_fecha AS fecha_date
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id=15 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND SUBSTRING(TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') FROM 4 for 7) <> SUBSTRING(TO_CHAR(p.fecha_anulacion, 'DD/MM/YYYY') FROM 4 for 7) AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaFin."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')>'".$fechaFin."'":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pago_cheque pc
								WHERE 
									(pc.esta_id=15 OR pc.esta_id=7)
						) AS pc ON (p.paga_docu_id = pc.pgch_id)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_sol_pago s ".
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" WHERE s.comp_id LIKE '%".$_REQUEST['compromiso']."'":"").
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										" WHERE s.comp_id IN 
											(
											SELECT comp_id 
											FROM sai_comp_traza 
											WHERE 
												esta_id<>2 AND 
												pcta_id LIKE '%".$_REQUEST['pcta']."%'
											)":"").
						") AS s ON (pc.docg_id = s.sopg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica 
					UNION
					SELECT 
						p.paga_docu_id AS pagado,
						ptr.trans_id AS codigo, 
						s.comp_id AS comp_id, 
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
						pd.part_id AS partida, 
						pd.padt_monto AS monto,
						p.paga_fecha AS fecha_date
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id=15 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND SUBSTRING(TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') FROM 4 for 7) <> SUBSTRING(TO_CHAR(p.fecha_anulacion, 'DD/MM/YYYY') FROM 4 for 7) AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaFin."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')>'".$fechaFin."'":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pago_transferencia ptr
								WHERE 
									(ptr.esta_id=15 OR ptr.esta_id=7)
						) AS ptr ON (p.paga_docu_id = ptr.trans_id) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_sol_pago s ".
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" WHERE s.comp_id LIKE '%".$_REQUEST['compromiso']."'":"").
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										" WHERE s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											)":"").
						") AS s ON (ptr.docg_id = s.sopg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica 
					ORDER BY 
						fecha_date,
						centro,
						partida";//centro, partida
	} else {
		
		$sql_pag2="	SELECT 
						p.paga_docu_id AS pagado,
						s.sopg_id AS codigo, 
						s.comp_id AS comp_id, 
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
						pd.part_id AS partida, 
						pd.padt_monto AS monto,
						p.paga_fecha AS fecha_date
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id<>15 AND p.esta_id<>2 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").
						") AS p 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pago_cheque pc
						) AS pc ON (p.paga_docu_id = pc.pgch_id)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_sol_pago s 
								WHERE
									s.esta_id<>15 ".
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" AND s.comp_id LIKE '%".$_REQUEST['compromiso']."'":"").
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										" AND s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											)":"").
						") AS s ON (pc.docg_id = s.sopg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica
					UNION 
					SELECT 
						p.paga_docu_id AS pagado,
						ptr.trans_id AS codigo, 
						s.comp_id AS comp_id, 
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						s.numero_reserva AS reserva, 
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
						pd.part_id AS partida, 
						pd.padt_monto AS monto,
						p.paga_fecha AS fecha_date
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id<>15 AND p.esta_id<>2 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."','YYYY-MM-DD') AND TO_DATE('".$fechaFin."','YYYY-MM-DD') ":"").
						") AS p 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pago_transferencia ptr
						) AS ptr ON (p.paga_docu_id = ptr.trans_id)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_sol_pago s 
								WHERE 
									s.esta_id<>15 ".
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" AND s.comp_id LIKE '%".$_REQUEST['compromiso']."'":"").
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
										" AND s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_REQUEST['pcta']."%'
											)":"").
						") AS s ON (ptr.docg_id = s.sopg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa					
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica 
					UNION
					SELECT 
						p.paga_docu_id AS pagado,
						cdi.comp_id AS codigo, 
						codi.nro_compromiso AS comp_id,
						COALESCE(pa.centro_gestor, '') || '/'|| COALESCE(pa.centro_costo, '') AS centro, 
						dg.numero_reserva AS reserva, 
						TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') AS fecha, 
						pd.part_id AS partida, 
						pd.padt_monto AS monto,
						p.paga_fecha AS fecha_date 
					FROM 
						(
							SELECT * 
							FROM sai_pagado p
							WHERE
								p.pres_anno='".$ano."' AND 
								p.esta_id<>15 AND p.esta_id<>2 ".
								(($_REQUEST['fechaInicio'] && strlen($_REQUEST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").
						") AS p 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
									(($_REQUEST['proyac'] && strlen($_REQUEST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
									(($_REQUEST['partida'] && strlen($_REQUEST['partida'])>1)?" pd.part_id LIKE '".$_REQUEST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_comp_diario cdi ".
									(($_REQUEST['pcta'] && strlen($_REQUEST['pcta'])>5)?
									" WHERE (
										cdi.comp_doc_id IN 
											(
												SELECT sopg_id 
												FROM sai_sol_pago 
												WHERE comp_id IN 
													(
														SELECT comp_id 
														FROM sai_comp_traza 
														WHERE pcta_id LIKE '%".$_REQUEST['pcta']."%'
													)
											) OR 
										cdi.comp_doc_id IN 
											(
												SELECT comp_id 
												FROM sai_comp 
												WHERE pcta_id = '%".$_REQUEST['pcta']."%'
											)
										)":"").
						") AS cdi ON (cdi.comp_id = p.paga_docu_id)  
						INNER JOIN 
							(
								SELECT * 
								FROM sai_codi codi ".
									(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?" WHERE codi.nro_compromiso LIKE '%".$_REQUEST['compromiso']."' ":"").
						") AS codi ON (cdi.comp_id = codi.comp_id)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_doc_genera dg
								WHERE 
									dg.esta_id<>15
						) AS dg ON (p.paga_docu_id = dg.docg_id),
						(
							SELECT 
								spae.proy_id AS id_proyecto_accion,
								spae.paes_id AS id_accion_especifica,
								spae.pres_anno,
								spae.centro_gestor,
								spae.centro_costo
							FROM 
								sai_proyecto sp
								INNER JOIN sai_proy_a_esp spae ON (sp.proy_id = spae.proy_id AND sp.pre_anno = spae.pres_anno) 
							WHERE 
								sp.pre_anno = '".$ano."'
							UNION
							SELECT 
								sae.acce_id AS id_proyecto_accion,
								sae.aces_id AS id_accion_especifica,
								sae.pres_anno,
								sae.centro_gestor,
								sae.centro_costo
							FROM 
								sai_ac_central sac
								INNER JOIN sai_acce_esp sae ON (sac.acce_id = sae.acce_id AND sac.pres_anno = sae.pres_anno) 
							WHERE 
								sac.pres_anno = '".$ano."'
						) AS pa	
					WHERE 
						pd.padt_id_p_ac = pa.id_proyecto_accion AND 
						pd.padt_cod_aesp = pa.id_accion_especifica ".
						(($_REQUEST['compromiso'] && strlen($_REQUEST['compromiso'])>5)?
						" AND (
							cdi.comp_doc_id IN 
								(
									SELECT sopg_id 
									FROM sai_sol_pago 
									WHERE comp_id LIKE '%".$_REQUEST['compromiso']."'
								) OR 
								codi.nro_compromiso LIKE '%".$_REQUEST['compromiso']."'
							)":"").
					"ORDER BY 
						fecha_date,
						centro, 
						partida";//centro, partida
	}
	$sql_pag = "SELECT * FROM (".$sql_pag2. ") AS b ORDER BY pagado,partida";
	$resultado_set_most_pag=pg_query($conexion,$sql_pag) or die("Error al consultar la descripcion del pagado");

	$ano1=substr($fechaInicio,0,4);
	$mes1=substr($fechaInicio,5,2);
	$dia1=substr($fechaInicio,8,2);

	$ano2=substr($fechaFin,0,4);
	$mes2=substr($fechaFin,5,2);
	$dia2=substr($fechaFin,8,2);

	
	?>
<table width="100%" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr>
		<td valign="top"><!--****************APARTADO******************-->
		<table border=0>
			<tr>
				<td valign="top">
				<table width="100%" border="0">
					<tr>
						<td colspan="5" class="normalNegroNegrita">APARTADO</td>
					</tr>
					<tr class="td_gray">
						<td width="137" class="normalNegroNegrita">C&oacute;digo</td>
						<td width="137" class="normalNegroNegrita">Pcta Asociado</td>
						<td width="102" class="normalNegroNegrita">Proy/Acc</td>
						<!-- <td width="102" class="normalNegroNegrita">Compromiso</td>-->
						<td width="128" class="normalNegroNegrita">Fecha</td>
						<td width="115" class="normalNegroNegrita">Partida</td>
						<td width="102" class="normalNegroNegrita">Monto</td>
					</tr>
					
					
					
					<?while ($rowapart=pg_fetch_array($resultado_set_most_apart)) {
						$suma_apart_monto+=$rowapart['monto'];
						

						
						?>
						<tr class="normal">
							<td align="center"><span class="normal"><?= $rowapart['codigo'];?></span></td>
							<td align="center"><span class="normal"><? echo $rowapart['pcta_asociado'] != '' ?  $rowapart['pcta_asociado'] : '-';?></span></td>
							<td align="center"><span class="normal"><?= $rowapart['centro'];?></span></td>
							<!-- <td align="center"><span class="normal"><?= '';?></span></td>-->
							<td align="center"><span class="normal"><?= $rowapart['fecha'];?></span></td>
							<td align="center"><span class="normal"><?= $rowapart['partida'];?></span></td>
							<td align="right"><span class="normal"><?= number_format($rowapart['monto'],2,',','.');?></span></td>
						</tr>
						
						
						
					<?php $i++; } ?>
				</table>
				</td>
			</tr>
		</table>
		</td>
		<!--****************COMPROMISO******************-->
		<td valign="top">
		<table border=0>
			<tr>
				<td valign="top">
				<table width="100%" border="0">
					<tr>
						<td colspan="5" class="normalNegroNegrita">COMPROMISO</td>
					</tr>
					<tr class="td_gray">
						<td width="137" class="normalNegroNegrita" align="center">C&oacute;digo</td>
						<td width="137" class="normalNegroNegrita" align="center">Pcta Asociado</td>
						<td width="102" class="normalNegroNegrita" align="center">Proy/Acc</td>
						<td width="128" class="normalNegroNegrita" align="center">Fecha</td>
						<td width="115" class="normalNegroNegrita" align="center">Partida
						</td>
						<td width="102" class="normalNegroNegrita" align="center">Monto</td>
					</tr>
					<?
					$valor=0;
					$i=0;
					$monto=0;
					$partida=	'';
					$comp= '';


					while ($rowacomp=pg_fetch_array($resultado_set_most_comp)) {
						

						/*if ($i==0) {
							$valor= $rowacomp['monto']; $monto=$rowacomp['monto'];
						}
						if ($rowacomp['partida']<>$partida || $comp<>$rowacomp['codigo']) {
							$partida=	$rowacomp['partida'];
							$comp=$rowacomp['codigo'];
							$valor= $rowacomp['monto']; $monto=$rowacomp['monto'];
						} else {

							$monto=$rowacomp['monto']-$valor;
							$valor=$rowacomp['monto'];
						}
						// $suma_comp_monto+=$monto;
						if ($monto<>0) {*/?>
						<tr class="normal">
							<td align="center"><span class="peq"><?= $rowacomp['codigo'];?></span></td>
							<td align="center"><span class="peq"><?= $rowacomp['pcta_id'];?></span></td>
							<td align="center"><span class="peq"><?= $rowacomp['centro'];?></span></td>
							<td align="center"><span class="peq"><?= $rowacomp['fecha'];?></span></td>
							<td align="center"><span class="peq"><?= $rowacomp['partida'];?></span></td>
							<td align="center"><span class="peq"><?= number_format($rowacomp['monto'],2,',','.');?></span></td>
							<!-- <td align="right"><span class="peq">
							<?php //echo number_format($monto,2,',','.');?>
							</td>-->
						</tr>
						<?php
						/*	$suma_comp_monto+=$rowacomp['monto'];
						}
						$i++;
						*/
						$suma_comp_monto+=$rowacomp['monto'];
					$i++; }
					?>
				</table>
				</td>
			</tr>
		</table>
		</td>
		<!--****************COMPROMISO******************-->
		<td valign="top">
		<table border=0>
			<tr>
				<td valign="top">
				<table width="100%" border="0">
					<tr>
						<td colspan="5" class="normalNegroNegrita">COMPROMISO AISLADO</td>
					</tr>
					<tr class="td_gray">
						<td width="137" class="normalNegroNegrita" align="center">C&oacute;digo</td>
						<td width="102" class="normalNegroNegrita" align="center">Proy/Acc</td>
						<td width="128" class="normalNegroNegrita" align="center">Fecha</td>
						<td width="115" class="normalNegroNegrita" align="center">Partida
						</td>
						<td width="102" class="normalNegroNegrita" align="center">Monto</td>
					</tr>
					<?
					$valor=0;
					$i=0;
					$monto=0;
					$partida=	'';
					$comp= '';
							    

					while ($rowacomp=pg_fetch_array($resultado_set_most_comp_ais)) {
						if ($i==0) {
							$valor= $rowacomp['monto']; $monto=$rowacomp['monto'];
						}
						if ($rowacomp['partida']<>$partida || $comp<>$rowacomp['codigo']) {
							$partida=	$rowacomp['partida'];
							$comp=$rowacomp['codigo'];
							$valor= $rowacomp['monto']; $monto=$rowacomp['monto'];
						} else {

							$monto=$rowacomp['monto']-$valor;
							$valor=$rowacomp['monto'];
						}
						// $suma_comp_monto+=$monto;
						if ($monto<>0) {
	
						?>
						<tr class="normal">
							<td align="center"><span class="peq"><?= $rowacomp['codigo'];?></span></td>
							<td align="center"><span class="peq"><?= $rowacomp['centro'];?></span></td>
							<td align="center"><span class="peq"><?= $rowacomp['fecha'];?></span></td>
							<td align="center"><span class="peq"><?= $rowacomp['partida'];?></span></td>
							<td align="center"><span class="peq"><?= number_format($rowacomp['monto'],2,',','.');?></span></td>
							<!-- <td align="right"><span class="peq">
							<?php //echo number_format($monto,2,',','.');?>
							</td>-->
						</tr>
						<?php
					 	$suma_comp_monto_ais+=$rowacomp['monto'];
					 	$i++;
						}
						
					}
					?>
				</table>
				</td>
			</tr>
		</table>
		</td>
		<!--****************CAUSADO******************-->
		<td valign="top">
		<table border=0>
			<tr>
				<td valign="top">
				<table width="100%" border="0">
					<tr>
						<td colspan="6" class="normalNegroNegrita">CAUSADO</td>
					</tr>
					<tr class="td_gray">
						<td width="117" class="normalNegroNegrita" align="center">C&oacute;digo</td>
						<td width="102" class="normalNegroNegrita" align="center">Comp</td>
						<td width="82" class="normalNegroNegrita" align="center">Proy/Acc</td>
						<td width="82" class="normalNegroNegrita" align="center">Nro. Reserva</td>
						<td width="82" class="normalNegroNegrita" align="center">Fecha</td>
						<td width="100" class="normalNegroNegrita" align="center">Partida</td>
						<td width="100" class="normalNegroNegrita" align="center">Monto</td>
					</tr>
					<?
					$compromiso="";

					while ($rowacaus=pg_fetch_array($resultado_set_most_caus)) {
				 		$suma_caus_monto+=$rowacaus['monto'];
				 		   
				 	?>
					<tr class="normal">
						<td align="center"><span class="normal"><?= $rowacaus['codigo'];?></span></td>
						<td align="center"><span class="normal"><?= $rowacaus['comp_id'];?></span></td>
						<td align="center"><span class="normal"><?= $rowacaus['centro'];?></span></td>
						<td align="center"><span class="normal"><?= $rowacaus['reserva'];?></span></td>
						<td align="center"><span class="normal"><?= $rowacaus['fecha'];?></span></td>
						<td align="center"><span class="normal"><?= $rowacaus['partida'];?></span></td>
						<td align="right"><span class="normal"><?= number_format($rowacaus['monto'],2,',','.');?></span></td>
					</tr>
					<?php
					$i++; }
					?>
				</table>
				</td>
			</tr>
		</table>
		</td>
		<!--****************PAGADO******************-->
		<td valign="top">
		<table border=0>
			<tr>
				<td valign="top">
				<table width="100%" border="0">
					<tr>
						<td colspan="6" class="normalNegroNegrita">PAGADO</td>
					</tr>
					<tr class="td_gray">
						<td width="117" class="normalNegroNegrita" align="center">C&oacute;digo</td>
						<td width="102" class="normalNegroNegrita" align="center">Comp</td>
						<td width="82" class="normalNegroNegrita" align="center">Proy/Acc</td>
						<td width="82" class="normalNegroNegrita" align="center">Nro. Reserva</td>
						<td width="82" class="normalNegroNegrita" align="center">Fecha</td>
						<td width="100" class="normalNegroNegrita" align="center">Partida</td>
						<td width="100" class="normalNegroNegrita" align="center">Monto</td>
					</tr>
					<?
					

					
					while ($rowpag=pg_fetch_array($resultado_set_most_pag)) {
				 		$suma_pag_monto+=$rowpag['monto'];
				 		
				 		
				 	?>
					<tr class="normal">
						<td align="center"><span class="normal"><?= $rowpag['codigo'];?></span></td>
						<td align="center"><span class="normal"><?= $rowpag['comp_id'];?></span></td>
						<td align="center"><span class="normal"><?= $rowpag['centro'];?></span></td>
						<td align="center"><span class="normal"><?= $rowpag['reserva'];?></span></td>
						<td align="center"><span class="normal"><?= $rowpag['fecha'];?></span></td>
						<td align="center"><span class="normal"><?= $rowpag['partida'];?></span></td>
						<td align="right"><span class="normal"><?= number_format($rowpag['monto'],2,',','.');?></span></td>
					</tr>
					<?php
					$i++;}
					?>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<!--nuevo-->
	<tr class="normalNegroNegrita" align="right">
		<td><?= number_format($suma_apart_monto,2,',','.');?></td>
		<td><?= number_format($suma_comp_monto,2,',','.');?></td>
		<td><?= number_format($suma_comp_monto_ais,2,',','.');?></td>
		<td><?= number_format($suma_caus_monto,2,',','.');?></td>
		<td><?= number_format($suma_pag_monto,2,',','.');?></td>
	</tr>
</table>
<?}
pg_close($conexion);

?></form>
</body>
</html>