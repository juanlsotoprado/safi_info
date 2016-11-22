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
<link href="../../css/estilos.css" rel="stylesheet" type="text/css" />
<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" src="../../js/funciones.js"> </script>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css"
	media="screen" rel="stylesheet" />
<script type="text/javascript"
	src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript"
	src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript"
	src="../../js/lib/calendarPopup/js/dateparse.js"></script>
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
		document.form1.action="diferenciasPresupuestarias.php";		
	}
	document.form1.submit();
}

function irMomentosPresupuestarios(compro){
	
   url ="momentoPresupuestario.php?hid_validar=2&ano="+<?php echo $pres_anno;?> +"&compromiso="+compro;		
   window.open( url,'_blank') ;
//alert(url);
	
}



</script>
</head>
<body>
	<form name="form1" method="post">
		<input type="hidden" value="0" name="hid_validar" />
		<table width="515" align="center"
			background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td height="21" colspan="2" class="normalNegroNegrita" align="left">
					DIFERECIAS ENTRE APARTADO, COMPROMETIDO Y CAUSADO</td>
			</tr>
			<tr>
				<td height="10" colspan="3"></td>
			</tr>

			<tr>
				<td class="normalNegrita" align="left">A&ntilde;o Presupuestario:</td>
				<td class="normalNegrita"><?= $pres_anno;?> <input type="hidden"
					name="ano" id="ano" value="<?= $pres_anno;?>" />
				</td>
			</tr>
			<tr>
				<td class="normalNegrita" align="left">Proyecto/Acc
					espec&iacute;fica:</td>
				<td><span class="normalNegrita"> <select name='proyac'
						class="normal">
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
								if($rowor['proyecto'].":::".$rowor['especifica']==$_POST['proyac']){
									?>
							<option
								value="<?= $rowor['proyecto'].":::".$rowor['especifica']?>"
								selected="selected">
								<?= ($rowor['centro_gestor'].'/'.$rowor['centro_costo']);?>
							</option>
							<?php
								}else{
									?>
							<option
								value="<?= $rowor['proyecto'].":::".$rowor['especifica']?>">
								<?= ($rowor['centro_gestor'].'/'.$rowor['centro_costo']);?>
							</option>
							<?php
								}
							}
							?>
					</select> </span>
				</td>
			</tr>
			<tr>
				<td class="normalNegrita" align="left">Partida:</td>
				<td><span class="normalNegrita"> <input name="partida" type="text"
						class="normal" id="partida" value="<?= $_POST['partida'];?>"
						size="25" /> </span>
				</td>
			</tr>
			<tr>
				<td colspan=5 class="normal" align="center"><input type="button"
					value="Buscar" onclick="validar(1);" /> <!-- <input type="button" value="PDF" onclick="validar(3);"/> -->
					<!-- <input type="button" value="Hoja de C&aacute;lculo" onclick="validar(4);"/> </center></td>-->
				</td>
			</tr>
		</table>
	</form>
	<br />
	<form name="form3" action="" method="post">
	<?php
	if ($_POST['hid_validar']==2) {
		if (strlen($_POST['fechaInicio'])>5) {
			$fechaInicio=trim($_POST['fechaInicio']);
			$fechaFin=trim($_POST['fechaFin']);
			$fechaInicio=substr($fechaInicio,6,4)."-".substr($fechaInicio,3,2)."-".substr($fechaInicio,0,2);
			$fechaFin=substr($fechaFin,6,4)."-".substr($fechaFin,3,2)."-".substr($fechaFin,0,2);
		}

		$suma_apart_monto=0;
		$suma_comp_monto=0;
		$suma_caus_monto=0;
		$suma_pag_monto=0;

		$ano = $_POST['ano'];

		if (strlen($_POST['fechaInicio'])<5) {
			$fechaInicio=$_POST['ano']."-01-01";
			$fechaFin=$_POST['ano']."-12-31";
		}

		if (strlen($_POST['proyac'])>8) {
			list( $idProyectoAccion, $idAccionEspecifica ) = split( ':::', $_POST['proyac'] );
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
								TO_CHAR(b.pcta_fecha, 'DD/MM/YYYY hh24:mi:ss') AS fecha
							FROM 
								sai_pcta_imputa a, 
								sai_pcuenta b
							WHERE 
								a.pres_anno=".$ano." AND 
								a.pcta_id=b.pcta_id AND 
								a.pres_anno=".$ano. 
		(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?" AND a.pcta_id LIKE '%".$_POST['compromiso']."%' ":"").
		(($_POST['pcta'] && strlen($_POST['pcta'])>5)?" AND a.pcta_id LIKE '%".$_POST['pcta']."%' ":"").
		(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?" AND TO_TIMESTAMP(b.pcta_fecha, 'YYYY/MM/DD HH24:MI:SS') BETWEEN TO_TIMESTAMP('".$fechaInicio." 00:00:00', 'YYYY/MM/DD HH24:MI:SS') AND TO_TIMESTAMP('".$fechaFin." 24:59:59', 'YYYY/MM/DD HH24:MI:SS') ":"").
		(($_POST['proyac'] && strlen($_POST['proyac'])>8)?" AND a.pcta_acc_pp='".$idProyectoAccion."' AND a.pcta_acc_esp='".$idAccionEspecifica."' ":"").
		(($_POST['partida'] && strlen($_POST['partida'])>1)?" AND a.pcta_sub_espe LIKE '".$_POST['partida']."%' ":"").
							"ORDER BY 
								a.pcta_id, 
								a.pcta_sub_espe
						) AS nucleo
						LEFT OUTER JOIN sai_proy_a_esp p ON (nucleo.pcta_acc_pp=p.proy_id AND nucleo.pcta_acc_esp=p.paes_id)
						LEFT OUTER JOIN sai_acce_esp a ON (nucleo.pcta_acc_esp = a.aces_id AND nucleo.pcta_acc_pp=a.acce_id)";

		//	echo $sql_apart;
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
							TO_CHAR(a.comp_fecha, 'DD/MM/YYYY hh24:mi:ss') AS fecha, 
							c.pcta_id AS pcta_id
						FROM 
							sai_comp_traza_reporte a, 
							sai_comp c
						WHERE 
							length(c.pcta_id)>4 AND 
							c.comp_id=a.comp_id AND 
							a.pres_anno=".$ano." AND 
							a.pres_anno=".$ano. 
		(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?" AND a.comp_id LIKE '%".$_POST['compromiso']."' ":"").
		(($_POST['pcta'] && strlen($_POST['pcta'])>5)?" AND c.pcta_id LIKE '%".$_POST['pcta']."%' ":"").
		(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?" AND a.comp_fecha BETWEEN TO_TIMESTAMP('".$fechaInicio." 00:00:00','YYYY/MM/DD HH24:MI:SS') AND TO_TIMESTAMP('".$fechaFin." 24:59:59', 'YYYY/MM/DD HH24:MI:SS')":"").
		(($_POST['proyac'] && strlen($_POST['proyac'])>8)?" AND a.comp_acc_pp='".$idProyectoAccion."' AND a.comp_acc_esp='".$idAccionEspecifica."' ":"").
		(($_POST['partida'] && strlen($_POST['partida'])>1)?" AND a.comp_sub_espe LIKE '".$_POST['partida']."%' ":"").
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
								TO_CHAR(a.comp_fecha, 'DD/MM/YYYY hh24:mi:ss') AS fecha, 
								c.pcta_id AS pcta_id
							FROM 
								sai_comp_traza_reporte a, 
								sai_comp c
							WHERE 
								length(c.pcta_id)<4 AND 
								c.comp_id=a.comp_id AND 
								a.pres_anno=".$ano." AND 
								a.pres_anno=".$ano. 
		(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?" AND a.comp_id LIKE '%".$_POST['compromiso']."' ":"").
		(($_POST['pcta'] && strlen($_POST['pcta'])>5)?" AND c.pcta_id LIKE '%".$_POST['pcta']."%' ":"").
		(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?" AND a.comp_fecha BETWEEN TO_TIMESTAMP('".$fechaInicio." 00:00:00','YYYY/MM/DD HH24:MI:SS') AND TO_TIMESTAMP('".$fechaFin." 24:59:59', 'YYYY/MM/DD HH24:MI:SS')":"").
		(($_POST['proyac'] && strlen($_POST['proyac'])>8)?" AND a.comp_acc_pp='".$idProyectoAccion."' AND a.comp_acc_esp='".$idAccionEspecifica."' ":"").
		(($_POST['partida'] && strlen($_POST['partida'])>1)?" AND a.comp_sub_espe LIKE '".$_POST['partida']."%' ":"").
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
						TO_CHAR(c.caus_fecha, 'DD/MM/YYYY hh24:mi:ss') AS fecha, 
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
		(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?"AND c.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").
						") AS c
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd
								WHERE
								 	".((strlen($_POST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_POST['partida'])>1)?"cd.part_id LIKE '".$_POST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM 
									sai_sol_pago s
								WHERE 
									".(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?"s.comp_id LIKE '".$_POST['compromiso']."' AND ":"")."
									".(($_POST['pcta'] && strlen($_POST['pcta'])>5)?
										"s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_POST['pcta']."%'
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
											(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?"AND c.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").
						") AS c
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd
								WHERE
								 	".((strlen($_POST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_POST['partida'])>1)?"cd.part_id LIKE '".$_POST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN
							(
								SELECT *
								FROM sai_comp_diario cdi
								WHERE 
									cdi.esta_id<>15 ".
											((strlen($_POST['pcta'])>5)?
										"AND (
												cdi.comp_doc_id IN 
												(
													SELECT sopg_id 
													FROM sai_sol_pago 
													WHERE comp_id IN 
														(
															SELECT comp_id 
															FROM sai_comp_traza 
															WHERE pcta_id LIKE '%".$_POST['pcta']."%'
														)
												) OR 
												cdi.comp_doc_id IN 
												(
													SELECT comp_id 
													FROM sai_comp 
													WHERE pcta_id = '%".$_POST['pcta']."%'
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
											((strlen($_POST['compromiso'])>5)?
							"AND (
									cdi.comp_doc_id IN 
									(
										SELECT sopg_id 
										FROM sai_sol_pago 
										WHERE comp_id LIKE '%".$_POST['compromiso']."'
									) OR 
									codi.nro_compromiso LIKE '%".$_POST['compromiso']."'
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
											(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?
									"AND c.caus_fecha <= TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND 
									c.fecha_anulacion BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").								
						") AS c
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd 
								WHERE
								 	".((strlen($_POST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_POST['partida'])>1)?"cd.part_id LIKE '".$_POST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM 
									sai_sol_pago s
								WHERE 
									".(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?"s.comp_id LIKE '".$_POST['compromiso']."' AND ":"")."
									".(($_POST['pcta'] && strlen($_POST['pcta'])>5)?
										"s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_POST['pcta']."%'
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
											(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?
									"AND c.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') AND
									c.fecha_anulacion > TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":""). 
						") AS c 
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd 
								WHERE
								 	".((strlen($_POST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_POST['partida'])>1)?"cd.part_id LIKE '".$_POST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM 
									sai_sol_pago s
								WHERE 
									".(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?"s.comp_id LIKE '".$_POST['compromiso']."' AND ":"")."
									".(($_POST['pcta'] && strlen($_POST['pcta'])>5)?
										"s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_POST['pcta']."%'
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
											(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?
									"AND c.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') AND 
									c.fecha_anulacion <= TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").
						") AS c 
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd 
								WHERE
								 	".((strlen($_POST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_POST['partida'])>1)?"cd.part_id LIKE '".$_POST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM 
									sai_sol_pago s
								WHERE 
									".(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?"s.comp_id LIKE '".$_POST['compromiso']."' AND ":"")."
									".(($_POST['pcta'] && strlen($_POST['pcta'])>5)?
										"s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_POST['pcta']."%'
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
											(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?
									"AND c.caus_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') AND 
									c.fecha_anulacion <= TO_DATE('".$fechaFin."', 'YYYY-MM-DD')":""). 
						") AS c 
						INNER JOIN  
							(
								SELECT * 
								FROM sai_causad_det cd 
								WHERE
								 	".((strlen($_POST['proyac'])>8)?"cd.cadt_id_p_ac='".$idProyectoAccion."' AND cd.cadt_cod_aesp='".$idAccionEspecifica."' AND ":"")."
								 	".((strlen($_POST['partida'])>1)?"cd.part_id LIKE '".$_POST['partida']."%' AND ":"")."
									cd.part_id NOT LIKE '4.11%'
							) AS cd ON (c.caus_id = cd.caus_id AND c.pres_anno = cd.pres_anno)
						INNER JOIN 
							(
								SELECT 
									* 
								FROM 
									sai_sol_pago s
								WHERE 
									".(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?"s.comp_id LIKE '".$_POST['compromiso']."' AND ":"")."
									".(($_POST['pcta'] && strlen($_POST['pcta'])>5)?
										"s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_POST['pcta']."%'
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

											if (strlen($_POST['fechaInicio'])>1) {
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
												(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."','YYYY-MM-DD') AND TO_DATE('".$fechaFin."','YYYY-MM-DD') ":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
												(($_POST['proyac'] && strlen($_POST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
												(($_POST['partida'] && strlen($_POST['partida'])>1)?" pd.part_id LIKE '".$_POST['partida']."%' AND ":"").
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
												(($_POST['pcta'] && strlen($_POST['pcta'])>5)?
										"AND s.comp_id IN 
										(
											SELECT comp_id 
											FROM sai_comp_traza 
											WHERE 
												esta_id<>2 AND 
												pcta_id LIKE '%".$_POST['pcta']."%'
										)":""
										).
										(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?" AND s.comp_id LIKE '%".$_POST['compromiso']."'":"").
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
										(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."','YYYY-MM-DD') AND TO_DATE('".$fechaFin."','YYYY-MM-DD') ":"").
						") AS p 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
										(($_POST['proyac'] && strlen($_POST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
										(($_POST['partida'] && strlen($_POST['partida'])>1)?" pd.part_id LIKE '".$_POST['partida']."%' AND ":"").
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
										(($_POST['pcta'] && strlen($_POST['pcta'])>5)?
										" AND s.comp_id IN 
										(
											SELECT comp_id 
											FROM sai_comp_traza 
											WHERE 
												esta_id<>2 AND 
												pcta_id LIKE '%".$_POST['pcta']."%'
										)":""
										).
										(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?" AND s.comp_id LIKE '%".$_POST['compromiso']."'":"").
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
										(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."','YYYY-MM-DD') AND TO_DATE('".$fechaFin."','YYYY-MM-DD') ":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
										(($_POST['proyac'] && strlen($_POST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
										(($_POST['partida'] && strlen($_POST['partida'])>1)?" pd.part_id LIKE '".$_POST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno)
						INNER JOIN 
							(
								SELECT * 
								FROM sai_comp_diario cdi 
								WHERE
									cdi.esta_id<>15 ".
										(($_POST['pcta'] && strlen($_POST['pcta'])>5)?
									" AND (
											cdi.comp_doc_id IN 
											(
												SELECT sopg_id 
												FROM sai_sol_pago 
												WHERE comp_id IN 
													(
														SELECT comp_id 
														FROM sai_comp_traza 
														WHERE pcta_id LIKE '%".$_POST['pcta']."%'
													)
											) OR 
											cdi.comp_doc_id IN 
											(
												SELECT comp_id 
												FROM sai_comp 
												WHERE pcta_id = '%".$_POST['pcta']."%'
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
										(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?
						" AND (
								cdi.comp_doc_id IN 
								(
									SELECT sopg_id 
									FROM sai_sol_pago 
									WHERE comp_id LIKE '%".$_POST['compromiso']."'
								) OR 
								codi.nro_compromiso LIKE '%".$_POST['compromiso']."'
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
										(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?" AND SUBSTRING(TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') FROM 4 for 7) <> SUBSTRING(TO_CHAR(p.fecha_anulacion, 'DD/MM/YYYY') FROM 4 for 7) AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaFin."'":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
										(($_POST['proyac'] && strlen($_POST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
										(($_POST['partida'] && strlen($_POST['partida'])>1)?" pd.part_id LIKE '".$_POST['partida']."%' AND ":"").
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
										(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?" WHERE s.comp_id LIKE '%".$_POST['compromiso']."'":"").
										(($_POST['pcta'] && strlen($_POST['pcta'])>5)?
										" WHERE s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_POST['pcta']."%'
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
										(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?" AND SUBSTRING(TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') FROM 4 for 7) <> SUBSTRING(TO_CHAR(p.fecha_anulacion, 'DD/MM/YYYY') FROM 4 for 7) AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaFin."'":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
										(($_POST['proyac'] && strlen($_POST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
										(($_POST['partida'] && strlen($_POST['partida'])>1)?" pd.part_id LIKE '".$_POST['partida']."%' AND ":"").
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
										(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?" WHERE s.comp_id LIKE '%".$_POST['compromiso']."' ":"").
										(($_POST['pcta'] && strlen($_POST['pcta'])>5)?
										" WHERE s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_POST['pcta']."%'
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
										(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?" AND SUBSTRING(TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') FROM 4 for 7) <> SUBSTRING(TO_CHAR(p.fecha_anulacion, 'DD/MM/YYYY') FROM 4 for 7) AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaFin."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')>'".$fechaFin."'":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
										(($_POST['proyac'] && strlen($_POST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
										(($_POST['partida'] && strlen($_POST['partida'])>1)?" pd.part_id LIKE '".$_POST['partida']."%' AND ":"").
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
										(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?" WHERE s.comp_id LIKE '%".$_POST['compromiso']."'":"").
										(($_POST['pcta'] && strlen($_POST['pcta'])>5)?
										" WHERE s.comp_id IN 
											(
											SELECT comp_id 
											FROM sai_comp_traza 
											WHERE 
												esta_id<>2 AND 
												pcta_id LIKE '%".$_POST['pcta']."%'
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
										(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?" AND SUBSTRING(TO_CHAR(p.paga_fecha, 'DD/MM/YYYY') FROM 4 for 7) <> SUBSTRING(TO_CHAR(p.fecha_anulacion, 'DD/MM/YYYY') FROM 4 for 7) AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fechaInicio."' AND TO_DATE(TO_CHAR(p.paga_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fechaFin."' AND TO_DATE(TO_CHAR(p.fecha_anulacion, 'YYYY-MM-DD'), 'YYYY-MM-DD')>'".$fechaFin."'":"").
						") AS p
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
										(($_POST['proyac'] && strlen($_POST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
										(($_POST['partida'] && strlen($_POST['partida'])>1)?" pd.part_id LIKE '".$_POST['partida']."%' AND ":"").
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
										(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?" WHERE s.comp_id LIKE '%".$_POST['compromiso']."'":"").
										(($_POST['pcta'] && strlen($_POST['pcta'])>5)?
										" WHERE s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_POST['pcta']."%'
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
												(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").
						") AS p 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
												(($_POST['proyac'] && strlen($_POST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
												(($_POST['partida'] && strlen($_POST['partida'])>1)?" pd.part_id LIKE '".$_POST['partida']."%' AND ":"").
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
												(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?" AND s.comp_id LIKE '%".$_POST['compromiso']."'":"").
												(($_POST['pcta'] && strlen($_POST['pcta'])>5)?
										" AND s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_POST['pcta']."%'
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
												(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."','YYYY-MM-DD') AND TO_DATE('".$fechaFin."','YYYY-MM-DD') ":"").
						") AS p 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
												(($_POST['proyac'] && strlen($_POST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
												(($_POST['partida'] && strlen($_POST['partida'])>1)?" pd.part_id LIKE '".$_POST['partida']."%' AND ":"").
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
												(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?" AND s.comp_id LIKE '%".$_POST['compromiso']."'":"").
												(($_POST['pcta'] && strlen($_POST['pcta'])>5)?
										" AND s.comp_id IN 
											(
												SELECT comp_id 
												FROM sai_comp_traza 
												WHERE 
													esta_id<>2 AND 
													pcta_id LIKE '%".$_POST['pcta']."%'
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
												(($_POST['fechaInicio'] && strlen($_POST['fechaInicio'])>1)?" AND p.paga_fecha BETWEEN TO_DATE('".$fechaInicio."', 'YYYY-MM-DD') AND TO_DATE('".$fechaFin."', 'YYYY-MM-DD') ":"").
						") AS p 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_pagado_dt pd
								WHERE ".
												(($_POST['proyac'] && strlen($_POST['proyac'])>8)?" pd.padt_id_p_ac='".$idProyectoAccion."' AND pd.padt_cod_aesp='".$idAccionEspecifica."' AND ":"").
												(($_POST['partida'] && strlen($_POST['partida'])>1)?" pd.part_id LIKE '".$_POST['partida']."%' AND ":"").
									"pd.part_id NOT LIKE '4.11%'
						) AS pd ON (p.paga_id = pd.paga_id AND p.pres_anno = pd.pres_anno) 
						INNER JOIN 
							(
								SELECT * 
								FROM sai_comp_diario cdi ".
												(($_POST['pcta'] && strlen($_POST['pcta'])>5)?
									" WHERE (
										cdi.comp_doc_id IN 
											(
												SELECT sopg_id 
												FROM sai_sol_pago 
												WHERE comp_id IN 
													(
														SELECT comp_id 
														FROM sai_comp_traza 
														WHERE pcta_id LIKE '%".$_POST['pcta']."%'
													)
											) OR 
										cdi.comp_doc_id IN 
											(
												SELECT comp_id 
												FROM sai_comp 
												WHERE pcta_id = '%".$_POST['pcta']."%'
											)
										)":"").
						") AS cdi ON (cdi.comp_id = p.paga_docu_id)  
						INNER JOIN 
							(
								SELECT * 
								FROM sai_codi codi ".
												(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?" WHERE codi.nro_compromiso LIKE '%".$_POST['compromiso']."' ":"").
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
												(($_POST['compromiso'] && strlen($_POST['compromiso'])>5)?
						" AND (
							cdi.comp_doc_id IN 
								(
									SELECT sopg_id 
									FROM sai_sol_pago 
									WHERE comp_id LIKE '%".$_POST['compromiso']."'
								) OR 
								codi.nro_compromiso LIKE '%".$_POST['compromiso']."'
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

		<!--****************APARTADO******************-->

											<?while ($rowapart=pg_fetch_array($resultado_set_most_apart)) {

												$codigo = $rowapart['codigo'];

												if ($rowapart['pcta_asociado'] != ''){

													$codigo = $rowapart['pcta_asociado'];

												}

												$rowapartado1[$codigo][$rowapart['partida']]['monto'][] =  $rowapart['monto'];


												$suma_apart_monto+=$rowapart['monto'];
												?>

												<?php }

												/*echo "<pre> compais <br/>";
												 echo print_r($rowapartado1);
												 echo "</pre>";*/

												?>

		<!--****************COMPROMISO******************-->

												<?
												$valor=0;
												$i=0;
												$monto=0;
												$partida=	'';
												$comp= '';

												while ($rowacomp=pg_fetch_array($resultado_set_most_comp)) {

													$rowacomp1comp[$rowacomp['codigo']][$rowacomp['partida']]['monto'][] =  $rowacomp['monto'];
													$rowacompapartado[$rowacomp['pcta_id']][$rowacomp['partida']]['monto'][] =  $rowacomp['monto'];

													/*

													echo "<pre> comp <br/>";
													echo print_r($rowacomp);
													echo "</pre>";

													*/

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

													<?php
													/*	$suma_comp_monto+=$rowacomp['monto'];
													 }
													 $i++;
													 */
													$suma_comp_monto+=$rowacomp['monto'];
												}
													

												?>



		<!--****************COMPROMISO aislado******************-->

												<?
												$valor=0;
												$i=0;
												$monto=0;
												$partida=	'';
												$comp= '';
												$rowacompcomp;

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
															
														$rowacompcomp[$rowacomp['codigo']][$rowacomp['partida']]['monto'][] =  $rowacomp['monto'];
															
														?>

														<?php

														$suma_comp_monto_ais+=$rowacomp['monto'];
													}
													$i++;
												}

												/* echo "<pre> compais <br/>";
												 echo print_r($rowacompcomp);
												 echo "</pre>";*/

												?>


		<!--****************CAUSADO******************-->

												<?
												$compromiso="";
												while ($rowacaus=pg_fetch_array($resultado_set_most_caus)) {


													$suma_caus_monto+=$rowacaus['monto'];


													$rowacauscaus[$rowacaus['comp_id']][$rowacaus['partida']]['monto'][] =  $rowacaus['monto'];


													?>

													<?php
												}
													
												/*	echo "<pre> caus <br/>";
												 echo print_r($rowacauscaus);
												 echo "</pre>"; */

												?>


		<!--****************PAGADO******************-->


												<?
												while ($rowpag=pg_fetch_array($resultado_set_most_pag)) {
													$suma_pag_monto+=$rowpag['monto'];
													?>

													<?php
												}
												?>








												<?php
												/*
												 echo "<pre> caus <br/>";
												 echo print_r($rowapartado1);
												 echo "</pre>";

												 echo "<pre> comp <br/>";
												 echo print_r($rowacompapartado);
												 echo "</pre>";
												 */


	?>

		<fieldset id="filsed1" style="width: 50%;">

			<legend>
				<b>Diferencias de Apartado con Comprometido</b>
			</legend>

			<table style="border: 1px solid #BEBEBE; width: 90%;"
				background="../../imagenes/fondo_tabla.gif" class="tablaalertas">

				<tr class="td_gray">
					<td align="center" class="  normalNegroNegrita">Punto de cuenta
						principal</td>
					<td align="center" class="normalNegroNegrita">Partida</td>
					<td align="center" class="normalNegroNegrita">Moto total del Punto
						de Cuenta</td>
					<td align="center" class="normalNegroNegrita">Monto Comprometido</td>
				</tr>



				<?php
													
												if($rowapartado1){
													$tdClass ='even';
													$i = 1;

												
				foreach ($rowapartado1 as  $index => $valor){
					foreach ($valor as  $index2 => $valor2){

						//echo array_sum($valor2['monto']). " - ". array_sum($rowacauscaus[$index][$index2]['monto'])."<br/>";




						$aparmonto = array_sum($rowacompapartado[$index][$index2]['monto']);
							
						if(!$rowacompapartado[$index][$index2]['monto']){

							$causmonto = 0;
						}

						if((array_sum($valor2['monto']) -  $aparmonto) > 0.000001 || (array_sum($valor2['monto']) -  $aparmonto)  < -0.000001){

							$tdClass = ($tdClass == "even") ? "odd" : "even";

							?>




				<tr class="normal  <?php echo $tdClass;?>">
					<td align="center"><span class="normal"><?=$index;?> </span></td>
					<td align="center"><span class="normal"></span> <?=$index2;?></td>

					<td align="center"><span class="normal"></span> <?=array_sum($valor2['monto']);?>
					</td>
					<td align="center"><span class="normal"></span> <?echo array_sum($rowacompapartado[$index][$index2]['monto']) != '' ?array_sum($rowacompapartado[$index][$index2]['monto']) : '-';?>
					</td>
				</tr>


				<?php
					

					
					
						}

							
					}
				}


					
			

												}


	?>

			</table>
			<br />
		</fieldset>
		<br /> <br />


		<?php
												

							?>


		<fieldset id="filsed1"
			style="width: 50%; margin-left: 0; margin-right: 0;">
			<legend>
				<b>Diferencias de Comprometido con Causado </b>
			</legend>
			<br />
			<table style="border: 1px solid #BEBEBE; width: 90%;"
				background="../../imagenes/fondo_tabla.gif" class="tablaalertas">

				<tr class="td_gray">
					<td align="center" class="  normalNegroNegrita">Compromiso</td>
					<td align="center" class="normalNegroNegrita">Partida</td>
					<td align="center" class="normalNegroNegrita">Moto total del
						Comprometido</td>
					<td align="center" class="normalNegroNegrita">Monto total del
						Causado</td>
				</tr>


				<?php
					
													
													

												if($rowacomp1comp){

													$tdClass ='even';
													$i = 1;
						
					

				foreach ($rowacomp1comp as  $index => $valor){
					foreach ($valor as  $index2 => $valor2){

						//echo array_sum($valor2['monto']). " - ". array_sum($rowacauscaus[$index][$index2]['monto'])."<br/>";




						$causmonto = array_sum($rowacauscaus[$index][$index2]['monto']);

						if(!$rowacauscaus[$index][$index2]['monto']){

							$causmonto = 0;
						}

						if((array_sum($valor2['monto']) -  $causmonto) > 0.000001 || (array_sum($valor2['monto']) -  $causmonto)  < -0.000001){

							$tdClass = ($tdClass == "even") ? "odd" : "even";



							?>




				<tr class="normal  <?php echo $tdClass;?>">
					<td align="center"><a href="javascript:void(0)"  class="normal" onclick="irMomentosPresupuestarios('<?php echo $index;?>')"><?=$index;?> </a> </td>
					<td align="center"><span class="normal"></span> <?=$index2;?></td>

					<td align="center"><span class="normal"></span> <?=array_sum($valor2['monto']);?>
					</td>
					<td align="center"><span class="normal"></span> <?echo array_sum($rowacauscaus[$index][$index2]['monto']) != '' ?array_sum($rowacauscaus[$index][$index2]['monto']) : '-';?>
					</td>
				</tr>


				<?php
					

					






				//	echo " El compromiso numero: ".$index." tiene diferencia en la partida numero: ".$index2. "<br/>";
				//	echo "comprometido =  ".array_sum($valor2['monto'])." - causado =  ".array_sum($rowacauscaus[$index][$index2]['monto']). "<br/><br/>";
					
					
						}


					}
				}
					
					
								}
													
	?>

			</table>
			<br />
		</fieldset>
<br /><br />

		<?php
			
							
													?>

		<fieldset id="filsed1"
			style="width: 50%; margin-left: 0; margin-right: 0;">
			<legend>
				<b>Diferencias de Comprometido Aislado con Causado </b>
			</legend>
			<br />
			<table style="border: 1px solid #BEBEBE; width: 90%;"
				background="../../imagenes/fondo_tabla.gif" class="tablaalertas">

				<tr class="td_gray">
					<td align="center" class="  normalNegroNegrita">Compromiso Aislado</td>
					<td align="center" class="normalNegroNegrita">Partida</td>
					<td align="center" class="normalNegroNegrita">Moto total del
						Comprometido Aislado</td>
					<td align="center" class="normalNegroNegrita">Monto total del
						Causado</td>
				</tr>


				<?php
													
													
												if($rowacompcomp){
													$tdClass ='even';
													$i = 1;

		
					



				foreach ($rowacompcomp as  $index => $valor){
					foreach ($valor as  $index2 => $valor2){

						//echo array_sum($valor2['monto']). " - ". array_sum($rowacauscaus[$index][$index2]['monto'])."<br/>";

							
							

						$causmonto = array_sum($rowacauscaus[$index][$index2]['monto']);
							
						if(!$rowacauscaus[$index][$index2]['monto']){

							$causmonto = 0;
						}



						if((array_sum($valor2['monto']) -  $causmonto) > 0.000001 || (array_sum($valor2['monto']) -  $causmonto)  < -0.000001){

							$tdClass = ($tdClass == "even") ? "odd" : "even";

							?>


				<tr class="normal <?php echo $tdClass;?>">
					<td align="center"><a href="javascript:void(0)"  class="normal" onclick="irMomentosPresupuestarios('<?php echo $index;?>')"><?=$index;?> </a></td>
					<td align="center"><span class="normal"></span> <?=$index2;?></td>

					<td align="center"><span class="normal"></span> <?=array_sum($valor2['monto']);?>
					</td>
					<td align="center"><span class="normal"></span> <?echo array_sum($rowacauscaus[$index][$index2]['monto']) != '' ?array_sum($rowacauscaus[$index][$index2]['monto']) : '-';?>
					</td>
				</tr>


				<?php
					


				//	echo"El compromiso aislado numero: ".$index." tiene diferencia en la partida numero: ".$index2. "<br/>";
				//	echo "comprometido =  ".array_sum($valor2['monto'])." - causado =  ".array_sum($rowacauscaus[$index][$index2]['monto']). "<br/><br/>";


						}

							
					}
				}

			
												}
													
	?>

			</table>
			<br />
		</fieldset>


		<?php
												?>


		<h1></h1>

		<?}
		pg_close($conexion);
		?>

	</form>
</body>
</html>
