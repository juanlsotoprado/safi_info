<?php
ob_start();
session_start();
require("../../includes/conexion.php");

if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	ob_end_flush();
	exit;
}
ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Reporte presupestario causado</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
	<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
	<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
	<link href="../../css/plantilla.css" rel="stylesheet" type="text/css"/>
	<script language="JavaScript" src="../../../includes/js/funciones.js"></script>
	<script>
	function validar(){//REVISAR
		if(	document.getElementById("hid_desde_itin").value!="" && 
			document.getElementById("hid_hasta_itin").value!=""){
			document.form1.submit();			
		}else{
			alert("Debe indicar la fecha inicial y la fecha final"); 
		}
	}

	function comparar_fechas(elemento){ //Formato dd/mm/yyyy
		var fecha_inicial=document.getElementById("hid_desde_itin").value;
		var fecha_final=document.getElementById("hid_hasta_itin").value;

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
	//-->
	</script>
</head>
<body>
<br/>
<form name="form1" method="post" action="reporte_causadopagado.php">
	<table width="50%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td height="21" class="normalNegroNegrita" align="left">Reporte presupuestario</td>
		</tr>
		<tr>
			<td>
				<font class="normalNegrita">
					Tipo de reporte:
				</font>
				&nbsp;&nbsp;
				<select name='tipoReporte' class="normalNegro">
					<option	value="1" <?=(("1"==$_REQUEST['tipoReporte'])?"selected='selected'":"")?>>Causado</option>
					<option	value="2" <?=(("2"==$_REQUEST['tipoReporte'])?"selected='selected'":"")?>>Pagado</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<font class="normalNegrita">
					Proyecto/Acci&oacute;n Central-Acci&oacute;n Espec&iacute;fica:
				</font>
				&nbsp;&nbsp;
				<select name='proyac' class="normalNegro">
					<option value="0">Todos</option>
					<?php
					$sql = "SELECT ".
								"acce_id AS proyecto, ".
								"aces_id AS especifica, ".
								"centro_gestor, ".
								"centro_costo, ".
								"'0' AS tipo ".
							"FROM sai_acce_esp ".
							"WHERE ".
								"pres_anno=".$_SESSION['an_o_presupuesto']." ".
							"UNION ".
							"SELECT ".
								"proy_id AS proyecto, ".
								"paes_id AS especifica, ".
								"centro_gestor, ".
								"centro_costo, ".
								"'1' AS tipo ".
							"FROM sai_proy_a_esp ".
							"WHERE ".
								"pres_anno=".$_SESSION['an_o_presupuesto']." ".
							"ORDER BY centro_gestor,centro_costo";
					$resultado_set=pg_query($conexion,$sql);
					while($row=pg_fetch_array($resultado_set)){
					?>
						<option	value="<?= $row['tipo'].":::".$row['proyecto'].":::".$row['especifica']?>" <?=(($row['tipo'].":::".$row['proyecto'].":::".$row['especifica']==$_REQUEST['proyac'])?"selected='selected'":"")?>><?= $row['centro_gestor'].'/'.$row['centro_costo']?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<font class="normalNegrita">
					Fuente financiamiento:
				</font>
				&nbsp;&nbsp;
				<select name='fuenteFinanciamiento' class="normalNegro">
					<option value="0">Todas</option>
					<?php
					$sql = "SELECT
								TRIM(UPPER(sf1125.fuente_financiamiento)) AS fuente_financiamiento
							FROM 
								sai_forma_1125 sf1125
								INNER JOIN 						
									(SELECT 
										acce_id AS proyecto, 
										aces_id AS especifica, 
										'0'::BIT AS tipo 
									FROM sai_acce_esp 
									WHERE 
										pres_anno IN (".$_SESSION['an_o_presupuesto'].",2013,2012,2011) 
									UNION 
									SELECT 
										proy_id AS proyecto, 
										paes_id AS especifica, 
										'1'::BIT AS tipo 
									FROM sai_proy_a_esp 
									WHERE 
										pres_anno IN (".$_SESSION['an_o_presupuesto'].",2013,2012,2011)
									) AS s ON 
										(
											sf1125.form_tipo = s.tipo AND
											sf1125.form_id_p_ac = s.proyecto AND
											sf1125.form_id_aesp = s.especifica
										)
							WHERE 
								sf1125.pres_anno IN (".$_SESSION['an_o_presupuesto'].",2013,2012,2011)
							GROUP BY sf1125.fuente_financiamiento
							ORDER BY sf1125.fuente_financiamiento
							";
					$resultado_set=pg_query($conexion,$sql);
					while($row=pg_fetch_array($resultado_set)){
					?>
						<option	value="<?= $row['fuente_financiamiento']?>" <?=(($row['fuente_financiamiento']==$_REQUEST['fuenteFinanciamiento'])?"selected='selected'":"")?>><?= $row['fuente_financiamiento']?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">
				Partida:&nbsp;&nbsp;
				<input type="text" class="normalNegro" value="<?= $_POST["partida"]?>" name="partida" id="partida"/>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">
				Fecha Inicio:&nbsp;&nbsp;
				<input value="<?= $_POST["hid_desde_itin"]?>" type="text" size="10" id="hid_desde_itin" name="hid_desde_itin" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly"/>
				<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_desde_itin');" title="Show popup calendar">
					<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
				</a>&nbsp;&nbsp;
				Fecha Fin:&nbsp;&nbsp;
				<input value="<?= $_POST["hid_hasta_itin"]?>" type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly"/>
				<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
					<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
				</a>
			</td>
		</tr>
		<tr>
			<td align="center">
				<input type="button" value="Buscar"	onclick="validar();" class="normalNegro"/>
			</td>
		</tr>
	</table>
	<br/>
</form>
<font class="normal">
<?php 
if(isset($_POST["hid_desde_itin"]) && isset($_POST["hid_hasta_itin"])){
	$tipoCategoria[]=null;
	$categoria[]=null;
	$accionEspecifica[]=null;
	$centroGestor[]=null;
	$centroCosto[]=null;
	$contador=0;
	$fechaInicio= $_POST["hid_desde_itin"];
	$fechaFin=$_POST["hid_hasta_itin"];
	$fecha_ini=substr($fechaInicio,6,4)."-".substr($fechaInicio,3,2)."-".substr($fechaInicio,0,2);
	$fecha_fin=substr($fechaFin,6,4)."-".substr($fechaFin,3,2)."-".substr($fechaFin,0,2);
	$fecha_fin_dia=substr($fechaFin,6,4)."-".substr($fechaFin,3,2)."-".substr($fechaFin,0,2)." 23:59:59";
	$ano_ini=substr($fecha_ini,0,4);
	
	$partida=$_REQUEST["partida"];
	$tipoReporte=$_REQUEST["tipoReporte"];
	$fuenteFinanciamiento=$_REQUEST["fuenteFinanciamiento"];
	if($_REQUEST['proyac']!=null && $_REQUEST['proyac']!='0'){
		list($tipo,$proy,$especif) = split(':::',$_REQUEST['proyac']);
	}

	$sql = 	"SELECT ".
				"acce_id AS proyecto, ".
				"aces_id AS especifica, ".
				"centro_gestor, ".
				"centro_costo, ".
				"0 AS tipo ".
			"FROM sai_acce_esp ".
			"WHERE ".
				"pres_anno=".$ano_ini." ".
				(($proy && $especif)?"AND acce_id='".$proy."' AND aces_id='".$especif."' ":"").
			"UNION ".
			"SELECT ".
				"proy_id AS proyecto, ".
				"paes_id AS especifica, ".
				"centro_gestor, ".
				"centro_costo, ".
				"1 AS tipo ".
			"FROM sai_proy_a_esp ".
			"WHERE ".
				"pres_anno=".$ano_ini." ".
				(($proy && $especif)?"AND proy_id='".$proy."' AND paes_id='".$especif."' ":"").
			"ORDER BY tipo, proyecto, especifica";
	$resultado=pg_query($conexion,$sql);
	
	while($row=pg_fetch_array($resultado)){
		$tipoCategoria[$contador]=$row['tipo'];
		$categoria[$contador]=$row['proyecto'];
		$accionEspecifica[$contador]=$row['especifica'];
		$centroGestor[$contador]=$row['centro_gestor'];
		$centroCosto[$contador]=$row['centro_costo'];
		$contador++;
	}

	$totalproy=count($categoria);
	$montototalcausado=0;
	$causado;
	$causadopres;
	$totalByProy = array();
	$totalByProyPres = array();
	
	for($i = 0; $i < $totalproy; $i++){
		$totalByProy[$i] = 0;
	}
	
	if($tipoReporte=="1"){//CAUSADO
		$sql = 	"SELECT ".
					"se.part_id, ".
					"SUM(se.monto_debe) AS monto_debe, ".
					"SUM(se.monto_haber) AS monto_haber, ".
					"SUM(se.monto_pres) AS monto_pres, ".
					"SUM(se.monto_pres_anulado) AS monto_pres_anulado, ".
					"se.tipo_categoria, ".
					"se.categoria, ".
					"se.accion_especifica ".
				"FROM ".
				"(".
				"SELECT ".
					"s.sopg, ".
					"s.part_id, ".
					"SUM(s.monto_debe) AS monto_debe, ".
					"SUM(s.monto_haber) AS monto_haber, ".
					"SUM(s.monto_pres) AS monto_pres, ".
					"SUM(s.monto_pres_anulado) AS monto_pres_anulado, ".
					"s.tipo_categoria, ".
					"s.categoria, ".
					"s.accion_especifica ".
				"FROM ".
				"(".
				"SELECT ".
					"scd.comp_doc_id as sopg, ".
					"scd.comp_id, ".
					"sc.caus_id, ".
					"src.part_id, ".
					"src.rcomp_debe AS monto_debe, ".
					"0.0 AS monto_haber, ".
					"COALESCE(scdt.cadt_monto,0.0) AS monto_pres, ".
					"0.0 AS monto_pres_anulado, ".
					"src.pr_ac_tipo AS tipo_categoria, ".
					"src.pr_ac AS categoria, ".
					"src.a_esp AS accion_especifica ".
				"FROM ".
					"sai_comp_diario scd ".
					"INNER JOIN sai_reng_comp src ON (scd.comp_id = src.comp_id AND src.pres_anno=".$ano_ini." AND src.part_id IS NOT NULL AND TRIM(src.part_id) <>'' AND src.part_id NOT LIKE '4.11.0%' ".(($partida!=null && $partida!='')?"AND src.part_id = '".$partida."' ":"").") ".
					"LEFT OUTER JOIN sai_causado sc ON (sc.caus_docu_id = scd.comp_doc_id AND to_char(sc.caus_fecha,'YYYY-MM-DD HH24:MI') = to_char(scd.comp_fec_emis,'YYYY-MM-DD HH24:MI') AND sc.pres_anno=".$ano_ini." AND sc.esta_id <> 2) ".
					"LEFT OUTER JOIN ".
						"sai_causad_det scdt ON ".
							"(".
								"sc.caus_id = scdt.caus_id AND ".
								"src.part_id = scdt.part_id AND ".
								"src.pr_ac = scdt.cadt_id_p_ac AND ".
								"src.a_esp = scdt.cadt_cod_aesp AND ".
								"src.pr_ac_tipo = scdt.cadt_tipo AND ".
								"src.pres_anno = scdt.pres_anno AND ".
								"scdt.pres_anno=".$ano_ini." ".
								(($tipo && $proy && $especif)?
									"AND scdt.cadt_tipo=".$tipo."::BIT AND scdt.cadt_id_p_ac='".$proy."' AND scdt.cadt_cod_aesp='".$especif."' "
								:
									"AND CAST(scdt.pres_anno AS TEXT)||'-'||CAST(CAST(scdt.cadt_tipo AS INT) AS TEXT)||'-'||scdt.cadt_id_p_ac||'-'||scdt.cadt_cod_aesp ".
										"IN (".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'0'||'-'||acce_id||'-'||aces_id ".
												"FROM sai_acce_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
												"UNION ".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'1'||'-'||proy_id||'-'||paes_id ".
												"FROM sai_proy_a_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
											")"
								)." ".
							") ".
				"WHERE ".
					"scd.comp_fec BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."' AND ".
					"scd.esta_id<>15 AND ".
					"scd.comp_id LIKE 'coda%' AND ".
					"scd.comp_comen LIKE 'C-%' ".
				"UNION ".
				"SELECT ".
					"scd.comp_doc_id as sopg, ".
					"scd.comp_id, ".
					"sc.caus_id, ".
					"src.part_id, ".
					"0.0 AS monto_debe, ".
					"0.0 AS monto_haber, ".
					"0.0 AS monto_pres, ".
					"scdt.cadt_monto AS monto_pres_anulado, ".
					"src.pr_ac_tipo AS tipo_categoria, ".
					"src.pr_ac AS categoria, ".
					"src.a_esp AS accion_especifica ".
				"FROM ".
					"sai_comp_diario scd ".
					"INNER JOIN sai_reng_comp src ON (scd.comp_id = src.comp_id AND src.pres_anno=".$ano_ini." AND src.part_id IS NOT NULL AND TRIM(src.part_id) <>'' AND src.part_id NOT LIKE '4.11.0%' ".(($partida!=null && $partida!='')?"AND src.part_id = '".$partida."' ":"").") ".
					"INNER JOIN sai_causado sc ON (sc.caus_docu_id = scd.comp_doc_id AND to_char(sc.caus_fecha,'YYYY-MM-DD HH24:MI') = to_char(scd.comp_fec_emis,'YYYY-MM-DD HH24:MI') AND sc.pres_anno=".$ano_ini." AND sc.esta_id <> 2 AND sc.fecha_anulacion IS NOT NULL AND sc.fecha_anulacion BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."') ".
					"INNER JOIN ".
						"sai_causad_det scdt ON ".
							"(".
								"sc.caus_id = scdt.caus_id AND ".
								"src.part_id = scdt.part_id AND ".
								"src.pr_ac = scdt.cadt_id_p_ac AND ".
								"src.a_esp = scdt.cadt_cod_aesp AND ".
								"src.pr_ac_tipo = scdt.cadt_tipo AND ".
								"src.pres_anno = scdt.pres_anno AND ".
								"scdt.pres_anno=".$ano_ini." ".
								(($tipo && $proy && $especif)?
									"AND scdt.cadt_tipo=".$tipo."::BIT AND scdt.cadt_id_p_ac='".$proy."' AND scdt.cadt_cod_aesp='".$especif."' "
								:
									"AND CAST(scdt.pres_anno AS TEXT)||'-'||CAST(CAST(scdt.cadt_tipo AS INT) AS TEXT)||'-'||scdt.cadt_id_p_ac||'-'||scdt.cadt_cod_aesp ".
										"IN (".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'0'||'-'||acce_id||'-'||aces_id ".
												"FROM sai_acce_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
												"UNION ".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'1'||'-'||proy_id||'-'||paes_id ".
												"FROM sai_proy_a_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
											")"
								)." ".
							") ".
				"WHERE ".
					"scd.esta_id<>15 AND ".
					"scd.comp_id LIKE 'coda%' AND ".
					"scd.comp_comen LIKE 'C-%' ".
				"UNION ".
				"SELECT ".
					"scd.comp_doc_id as sopg, ".
					"scd.comp_id, ".
					"sc.caus_id, ".
					"src.part_id, ".
					"0.0 AS monto_debe, ".
					"src.rcomp_haber*-1 AS monto_haber, ".
					"0.0 AS monto_pres, ".
					"0.0 AS monto_pres_anulado, ".
					"src.pr_ac_tipo AS tipo_categoria, ".
					"src.pr_ac AS categoria, ".
					"src.a_esp AS accion_especifica ".
				"FROM ".
					"sai_comp_diario scd ".
					"INNER JOIN sai_reng_comp src ON (scd.comp_id = src.comp_id AND src.pres_anno=".$ano_ini." AND src.part_id IS NOT NULL AND TRIM(src.part_id) <>'' AND src.part_id NOT LIKE '4.11.0%' ".(($partida!=null && $partida!='')?"AND src.part_id = '".$partida."' ":"").") ".
					"LEFT OUTER JOIN sai_causado sc ON (sc.caus_docu_id = scd.comp_doc_id AND to_char(sc.fecha_anulacion,'YYYY-MM-DD HH24:MI') = to_char(scd.comp_fec_emis,'YYYY-MM-DD HH24:MI') AND sc.pres_anno=".$ano_ini." AND sc.esta_id = 15) ".
					"LEFT OUTER JOIN ".
						"sai_causad_det scdt ON ".
							"(".
								"sc.caus_id = scdt.caus_id AND ".
								"src.part_id = scdt.part_id AND ".
								"src.pr_ac = scdt.cadt_id_p_ac AND ".
								"src.a_esp = scdt.cadt_cod_aesp AND ".
								"src.pr_ac_tipo = scdt.cadt_tipo AND ".
								"src.pres_anno = scdt.pres_anno AND ".
								"scdt.pres_anno=".$ano_ini." ".
								(($tipo && $proy && $especif)?
									"AND scdt.cadt_tipo=".$tipo."::BIT AND scdt.cadt_id_p_ac='".$proy."' AND scdt.cadt_cod_aesp='".$especif."' "
								:
									"AND CAST(scdt.pres_anno AS TEXT)||'-'||CAST(CAST(scdt.cadt_tipo AS INT) AS TEXT)||'-'||scdt.cadt_id_p_ac||'-'||scdt.cadt_cod_aesp ".
										"IN (".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'0'||'-'||acce_id||'-'||aces_id ".
												"FROM sai_acce_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
												"UNION ".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'1'||'-'||proy_id||'-'||paes_id ".
												"FROM sai_proy_a_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
											")"
								)." ".
							") ".
				"WHERE ".
					"scd.comp_fec BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."' AND ".
					"scd.esta_id<>15 AND ".
					"scd.comp_id LIKE 'coda%' AND ".
					"(scd.comp_comen LIKE 'A-%' OR scd.comp_comen LIKE 'A_C-%') AND ".
					"scd.comp_doc_id LIKE 'sopg%' ".
				"UNION ".
				"SELECT ".
					"spc.docg_id as sopg, ".
					"scd.comp_id, ".
					"sc.caus_id, ".
					"src.part_id, ".
					"0.0 AS monto_debe, ".
					"src.rcomp_haber*-1 AS monto_haber, ".
					"0.0 AS monto_pres, ".
					"0.0 AS monto_pres_anulado, ".
					"src.pr_ac_tipo AS tipo_categoria, ".
					"src.pr_ac AS categoria, ".
					"src.a_esp AS accion_especifica ".
				"FROM ".
					"sai_comp_diario scd ".
					"INNER JOIN sai_reng_comp src ON (scd.comp_id = src.comp_id AND src.pres_anno=".$ano_ini." AND src.part_id IS NOT NULL AND TRIM(src.part_id) <>'' AND src.part_id NOT LIKE '4.11.0%' ".(($partida!=null && $partida!='')?"AND src.part_id = '".$partida."' ":"").") ".
					"INNER JOIN sai_pago_cheque spc ON (scd.comp_doc_id = spc.pgch_id) ".
					"LEFT OUTER JOIN sai_causado sc ON (sc.caus_docu_id = spc.docg_id AND to_char(sc.fecha_anulacion,'YYYY-MM-DD HH24:MI') = to_char(scd.comp_fec_emis,'YYYY-MM-DD HH24:MI') AND sc.pres_anno=".$ano_ini." AND sc.esta_id = 15) ".
					"LEFT OUTER JOIN ".
						"sai_causad_det scdt ON ".
							"(".
								"sc.caus_id = scdt.caus_id AND ".
								"src.part_id = scdt.part_id AND ".
								"src.pr_ac = scdt.cadt_id_p_ac AND ".
								"src.a_esp = scdt.cadt_cod_aesp AND ".
								"src.pr_ac_tipo = scdt.cadt_tipo AND ".
								"src.pres_anno = scdt.pres_anno AND ".
								"scdt.pres_anno=".$ano_ini." ".
								(($tipo && $proy && $especif)?
									"AND scdt.cadt_tipo=".$tipo."::BIT AND scdt.cadt_id_p_ac='".$proy."' AND scdt.cadt_cod_aesp='".$especif."' "
								:
									"AND CAST(scdt.pres_anno AS TEXT)||'-'||CAST(CAST(scdt.cadt_tipo AS INT) AS TEXT)||'-'||scdt.cadt_id_p_ac||'-'||scdt.cadt_cod_aesp ".
										"IN (".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'0'||'-'||acce_id||'-'||aces_id ".
												"FROM sai_acce_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
												"UNION ".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'1'||'-'||proy_id||'-'||paes_id ".
												"FROM sai_proy_a_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
											")"
								)." ".
							") ".
				"WHERE ".
					"scd.comp_fec BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."' AND ".
					"scd.esta_id<>15 AND ".
					"scd.comp_id LIKE 'coda%' AND ".
					"scd.comp_comen LIKE 'A_C-%' AND ".
					"scd.comp_doc_id LIKE 'pgch%' ".
				"UNION ".
				"SELECT ".
					"spt.docg_id as sopg, ".
					"scd.comp_id, ".
					"sc.caus_id, ".
					"src.part_id, ".
					"0.0 AS monto_debe, ".
					"src.rcomp_haber*-1 AS monto_haber, ".
					"0.0 AS monto_pres, ".
					"0.0 AS monto_pres_anulado, ".
					"src.pr_ac_tipo AS tipo_categoria, ".
					"src.pr_ac AS categoria, ".
					"src.a_esp AS accion_especifica ".
				"FROM ".
					"sai_comp_diario scd ".
					"INNER JOIN sai_reng_comp src ON (scd.comp_id = src.comp_id AND src.pres_anno=".$ano_ini." AND src.part_id IS NOT NULL AND TRIM(src.part_id) <>'' AND src.part_id NOT LIKE '4.11.0%' ".(($partida!=null && $partida!='')?"AND src.part_id = '".$partida."' ":"").") ".
					"INNER JOIN sai_pago_transferencia spt ON (scd.comp_doc_id = spt.trans_id) ".
					"LEFT OUTER JOIN sai_causado sc ON (sc.caus_docu_id = spt.docg_id AND to_char(sc.fecha_anulacion,'YYYY-MM-DD HH24:MI') = to_char(scd.comp_fec_emis,'YYYY-MM-DD HH24:MI') AND sc.pres_anno=".$ano_ini." AND sc.esta_id = 15) ".
					"LEFT OUTER JOIN ".
						"sai_causad_det scdt ON ".
							"(".
								"sc.caus_id = scdt.caus_id AND ".
								"src.part_id = scdt.part_id AND ".
								"src.pr_ac = scdt.cadt_id_p_ac AND ".
								"src.a_esp = scdt.cadt_cod_aesp AND ".
								"src.pr_ac_tipo = scdt.cadt_tipo AND ".
								"src.pres_anno = scdt.pres_anno AND ".
								"scdt.pres_anno=".$ano_ini." ".
								(($tipo && $proy && $especif)?
									"AND scdt.cadt_tipo=".$tipo."::BIT AND scdt.cadt_id_p_ac='".$proy."' AND scdt.cadt_cod_aesp='".$especif."' "
								:
									"AND CAST(scdt.pres_anno AS TEXT)||'-'||CAST(CAST(scdt.cadt_tipo AS INT) AS TEXT)||'-'||scdt.cadt_id_p_ac||'-'||scdt.cadt_cod_aesp ".
										"IN (".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'0'||'-'||acce_id||'-'||aces_id ".
												"FROM sai_acce_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
												"UNION ".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'1'||'-'||proy_id||'-'||paes_id ".
												"FROM sai_proy_a_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
											")"
								)." ".
							") ".
				"WHERE ".
					"scd.comp_fec BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."' AND ".
					"scd.esta_id<>15 AND ".
					"scd.comp_id LIKE 'coda%' AND ".
					"scd.comp_comen LIKE 'A_C-%' AND ".
					"scd.comp_doc_id LIKE 'tran%' ".
				"UNION ".
				"SELECT ".
					"scd.comp_id as sopg, ".
					"scd.comp_id, ".
					"sc.caus_id, ".
					"src.part_id, ".
					"src.rcomp_debe AS monto_debe, ".
					"src.rcomp_haber*-1 AS monto_haber, ".
					"COALESCE(scdt.cadt_monto,0.0) AS monto_pres, ".
					"CASE ".
						"WHEN (sc.fecha_anulacion IS NOT NULL AND sc.fecha_anulacion BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."') THEN scdt.cadt_monto ".
						"ELSE 0.0 END AS monto_pres_anulado, ".
					"src.pr_ac_tipo AS tipo_categoria, ".
					"src.pr_ac AS categoria, ".
					"src.a_esp AS accion_especifica ".
				"FROM ".
					"sai_comp_diario scd ".
					"INNER JOIN sai_reng_comp src ON (scd.comp_id = src.comp_id AND src.pres_anno=".$ano_ini." AND src.part_id IS NOT NULL AND TRIM(src.part_id) <>'' AND src.part_id NOT LIKE '4.11.0%' ".(($partida!=null && $partida!='')?"AND src.part_id = '".$partida."' ":"").") ".
					"LEFT OUTER JOIN sai_causado sc ON (sc.caus_docu_id = scd.comp_id AND to_char(sc.caus_fecha,'YYYY-MM-DD') = to_char(scd.comp_fec,'YYYY-MM-DD') AND sc.pres_anno=".$ano_ini." AND sc.esta_id <> 2) ".
					"LEFT OUTER JOIN ".
						"sai_causad_det scdt ON ".
							"(".
								"sc.caus_id = scdt.caus_id AND ".
								"src.part_id = scdt.part_id AND ".
								"src.pr_ac = scdt.cadt_id_p_ac AND ".
								"src.a_esp = scdt.cadt_cod_aesp AND ".
								"src.pr_ac_tipo = scdt.cadt_tipo AND ".
								"src.pres_anno = scdt.pres_anno AND ".
								"scdt.pres_anno=".$ano_ini." ".
								(($tipo && $proy && $especif)?
									"AND scdt.cadt_tipo=".$tipo."::BIT AND scdt.cadt_id_p_ac='".$proy."' AND scdt.cadt_cod_aesp='".$especif."' "
								:
									"AND CAST(scdt.pres_anno AS TEXT)||'-'||CAST(CAST(scdt.cadt_tipo AS INT) AS TEXT)||'-'||scdt.cadt_id_p_ac||'-'||scdt.cadt_cod_aesp ".
										"IN (".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'0'||'-'||acce_id||'-'||aces_id ".
												"FROM sai_acce_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
												"UNION ".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'1'||'-'||proy_id||'-'||paes_id ".
												"FROM sai_proy_a_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
											")"
								)." ".
							") ".
				"WHERE ".
					"scd.comp_fec BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."' AND ".
					"scd.esta_id<>15 AND ".
					"scd.comp_id LIKE 'codi%' AND scd.comp_id NOT IN ('codi-452155011','codi-452155111','codi-452155211','codi-452281411') ".
				") AS s ".
				"GROUP BY s.sopg, s.part_id, s.tipo_categoria, s.categoria, s.accion_especifica ".
				") AS se 
				".(($fuenteFinanciamiento != null && $fuenteFinanciamiento != '' && $fuenteFinanciamiento != '0')?
					"INNER JOIN 		
						(SELECT
							sf1125.form_tipo,
							sf1125.form_id_p_ac,
							sf1125.form_id_aesp						
						FROM 
							sai_forma_1125 sf1125
						WHERE 
							sf1125.pres_anno=".$ano_ini." AND
							TRIM(UPPER(sf1125.fuente_financiamiento)) = '".$fuenteFinanciamiento."'
						GROUP BY 
							sf1125.form_tipo,
							sf1125.form_id_p_ac,
							sf1125.form_id_aesp
						) AS s ON
							(s.form_tipo = se.tipo_categoria AND
							s.form_id_p_ac = se.categoria AND
							s.form_id_aesp = se.accion_especifica) ":"")."
				GROUP BY se.part_id, se.tipo_categoria, se.categoria, se.accion_especifica ".
				"ORDER BY se.part_id, se.tipo_categoria, se.categoria, se.accion_especifica ";
	}else { //PAGADO
		$sql = 	"SELECT ".
					"se.part_id, ".
					"SUM(se.monto_debe) AS monto_debe, ".
					"SUM(se.monto_haber) AS monto_haber, ".
					"SUM(se.monto_pres) AS monto_pres, ".
					"SUM(se.monto_pres_anulado) AS monto_pres_anulado, ".
					"se.tipo_categoria, ".
					"se.categoria, ".
					"se.accion_especifica ".
				"FROM ".
				"(".
				"SELECT ".
					"s.sopg, ".
					"s.part_id, ".
					"SUM(s.monto_debe) AS monto_debe, ".
					"SUM(s.monto_haber) AS monto_haber, ".
					"SUM(s.monto_pres) AS monto_pres, ".
					"SUM(s.monto_pres_anulado) AS monto_pres_anulado, ".
					"s.tipo_categoria, ".
					"s.categoria, ".
					"s.accion_especifica ".
				"FROM ".
				"(".
				"SELECT ".
					"scd.comp_doc_id as sopg, ".
					"scd.comp_id, ".
					"sp.paga_id, ".
					"src.part_id, ".
					"src.rcomp_debe AS monto_debe, ".
					"src.rcomp_haber AS monto_haber, ".
					"COALESCE(spdt.padt_monto,0.0) AS monto_pres, ".
					"0.0 AS monto_pres_anulado, ".
					"src.pr_ac_tipo AS tipo_categoria, ".
					"src.pr_ac AS categoria, ".
					"src.a_esp AS accion_especifica ".
				"FROM ".
					"sai_comp_diario scd ".
					"INNER JOIN sai_reng_comp src ON (scd.comp_id = src.comp_id AND src.pres_anno=".$ano_ini." AND src.part_id IS NOT NULL AND TRIM(src.part_id) <>'' AND src.part_id NOT LIKE '4.11.0%' ".(($partida!=null && $partida!='')?"AND src.part_id = '".$partida."' ":"").") ".
					"INNER JOIN ".
						"(".
						"SELECT ".
							"spc.pgch_id AS id_pago, ".
							"spc.docg_id AS sopg ".
						"FROM sai_pago_cheque spc ".
						"INNER JOIN sai_pagado pg ON (pg.paga_docu_id = spc.pgch_id) ".						
						"WHERE (spc.esta_id <> 2 OR (spc.esta_id = 2 AND pg.fecha_anulacion > '".$fecha_fin_dia."')) ".
						"UNION ".
						"SELECT ".
							"spt.trans_id AS id_pago, ".
							"spt.docg_id AS sopg ".
						"FROM sai_pago_transferencia spt ".
						"WHERE spt.esta_id <> 2 ".
						") AS spct ON (spct.sopg = scd.comp_doc_id) ".
					"LEFT OUTER JOIN sai_pagado sp ON (sp.paga_docu_id = spct.id_pago AND sp.pres_anno=".$ano_ini." AND sp.esta_id <> 2) ".
					"LEFT OUTER JOIN ".
						"sai_pagado_dt spdt ON ".
							"(".
								"sp.paga_id = spdt.paga_id AND ".
								"src.part_id = spdt.part_id AND ".
								"src.pr_ac = spdt.padt_id_p_ac AND ".
								"src.a_esp = spdt.padt_cod_aesp AND ".
								"src.pr_ac_tipo = spdt.padt_tipo AND ".
								"src.pres_anno = spdt.pres_anno AND ".
								"spdt.pres_anno=".$ano_ini." ".
								(($tipo && $proy && $especif)?
									"AND spdt.padt_tipo=".$tipo."::BIT AND spdt.padt_id_p_ac='".$proy."' AND spdt.padt_cod_aesp='".$especif."' "
								:
									"AND CAST(spdt.pres_anno AS TEXT)||'-'||CAST(CAST(spdt.padt_tipo AS INT) AS TEXT)||'-'||spdt.padt_id_p_ac||'-'||spdt.padt_cod_aesp ".
										"IN (".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'0'||'-'||acce_id||'-'||aces_id ".
												"FROM sai_acce_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
												"UNION ".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'1'||'-'||proy_id||'-'||paes_id ".
												"FROM sai_proy_a_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
											")"
								)." ".
							") ".
				"WHERE ".
					//"scd.comp_fec BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."' AND ".
					"scd.comp_fec <= '".$fecha_fin_dia."' AND ".
					"sp.paga_fecha BETWEEN to_timestamp('".$fecha_ini." 00:00:00', 'YYYY-MM-DD HH24:MI:SS')  AND to_timestamp('".$fecha_fin_dia." 23:59:59', 'YYYY-MM-DD HH24:MI:SS') AND ".
					//"sp.esta_id != 15 AND ".
					"scd.esta_id<>15 AND ".
					"scd.comp_id LIKE 'coda%' AND ".
					"(scd.comp_comen LIKE 'C-%')". //OR scd.comp_comen LIKE 'A-%'
				"UNION ".
				/*"SELECT ".
					"s.sopg, ".
					"s.comp_id, ".
					"s.paga_id, ".
					"s.part_id, ".
					"s.monto_debe, ".
					"s.monto_haber, ".
					"s.monto_pres, ".
					"s.monto_pres_anulado, ".
					"s.tipo_categoria, ".
					"s.categoria, ".
					"s.accion_especifica ".
				"FROM ".
				"(SELECT ".
					"scd.comp_doc_id AS sopg, ".
					"MAX(scd.comp_id) AS comp_id, ".
					"sp.paga_id, ".
					"src.part_id, ".
					"0.0 AS monto_debe, ".
					"0.0 AS monto_haber, ".
					"0.0 AS monto_pres, ".
					"spdt.padt_monto AS monto_pres_anulado, ".
					"src.pr_ac_tipo AS tipo_categoria, ".
					"src.pr_ac AS categoria, ".
					"src.a_esp AS accion_especifica ".
				"FROM ".
					"sai_comp_diario scd ".
					"INNER JOIN sai_reng_comp src ON (scd.comp_id = src.comp_id AND src.pres_anno=".$ano_ini." AND src.part_id IS NOT NULL AND TRIM(src.part_id) <>'' AND src.part_id NOT LIKE '4.11.0%' ".(($partida!=null && $partida!='')?"AND src.part_id = '".$partida."' ":"").") ".
					"INNER JOIN ".
						"(".
						"SELECT ".
							"spc.pgch_id AS id_pago, ".
							"spc.docg_id AS sopg ".
						"FROM sai_pago_cheque spc ".
						"WHERE spc.esta_id <> 2 ".
						"UNION ".
						"SELECT ".
							"spt.trans_id AS id_pago, ".
							"spt.docg_id AS sopg ".
						"FROM sai_pago_transferencia spt ".
						"WHERE spt.esta_id <> 2 ".
						") AS spct ON (spct.sopg = scd.comp_doc_id) ".
					"INNER JOIN sai_pagado sp ON (sp.paga_docu_id = spct.id_pago AND sp.pres_anno=".$ano_ini." AND sp.esta_id <> 2 AND sp.fecha_anulacion IS NOT NULL AND sp.fecha_anulacion BETWEEN to_timestamp('".$fecha_ini." 00:00:00', 'YYYY-MM-DD HH24:MI:SS')  AND to_timestamp('".$fecha_fin_dia." 23:59:59', 'YYYY-MM-DD HH24:MI:SS')) ".
					"INNER JOIN ".
						"sai_pagado_dt spdt ON ".
							"(".
								"sp.paga_id = spdt.paga_id AND ".
								"src.part_id = spdt.part_id AND ".
								"src.pr_ac = spdt.padt_id_p_ac AND ".
								"src.a_esp = spdt.padt_cod_aesp AND ".
								"src.pr_ac_tipo = spdt.padt_tipo AND ".
								"src.pres_anno = spdt.pres_anno AND ".
								"spdt.pres_anno=".$ano_ini." ".
								(($tipo && $proy && $especif)?
									"AND spdt.padt_tipo=".$tipo."::BIT AND spdt.padt_id_p_ac='".$proy."' AND spdt.padt_cod_aesp='".$especif."' "
								:
									"AND CAST(spdt.pres_anno AS TEXT)||'-'||CAST(CAST(spdt.padt_tipo AS INT) AS TEXT)||'-'||spdt.padt_id_p_ac||'-'||spdt.padt_cod_aesp ".
										"IN (".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'0'||'-'||acce_id||'-'||aces_id ".
												"FROM sai_acce_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
												"UNION ".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'1'||'-'||proy_id||'-'||paes_id ".
												"FROM sai_proy_a_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
											")"
								)." ".
							") ".
				"WHERE ".
					"scd.esta_id<>15 AND ".
					"sp.paga_fecha BETWEEN to_timestamp('".$fecha_ini." 00:00:00', 'YYYY-MM-DD HH24:MI:SS')  AND to_timestamp('".$fecha_fin_dia." 23:59:59', 'YYYY-MM-DD HH24:MI:SS') AND ".
					//"sp.esta_id != 15 AND ".
					"scd.comp_id LIKE 'coda%' AND ".
					"scd.comp_comen LIKE 'C-%' ".
				"GROUP BY ".
					"scd.comp_doc_id, ".
					"sp.paga_id, ".
					"src.part_id, ".
					"monto_debe, ".
					"monto_haber, ".
					"monto_pres, ".
					"spdt.padt_monto, ".
					"src.pr_ac_tipo, ".
					"src.pr_ac, ".
					"src.a_esp ".
				") AS s ".
				"UNION ".*/
				"SELECT ".
					"scd.comp_doc_id as sopg, ".
					"scd.comp_id, ".
					"sp.paga_id, ".
					"src.part_id, ".
					"src.rcomp_debe*-1 AS monto_debe, ".
					"src.rcomp_haber*-1 AS monto_haber, ".
					"COALESCE(spdt.padt_monto,0.0)*-1 AS monto_pres, ".
					"0.0 AS monto_pres_anulado, ".
					"src.pr_ac_tipo AS tipo_categoria, ".
					"src.pr_ac AS categoria, ".
					"src.a_esp AS accion_especifica ".
				"FROM ".
					"sai_comp_diario scd ".
					"INNER JOIN sai_reng_comp src ON (scd.comp_id = src.comp_id AND src.pres_anno=".$ano_ini." AND src.part_id IS NOT NULL AND TRIM(src.part_id) <>'' AND src.part_id NOT LIKE '4.11.0%' ".(($partida!=null && $partida!='')?"AND src.part_id = '".$partida."' ":"").") ".
					"INNER JOIN ".
						"(".
						"SELECT ".
							"spc.pgch_id AS id_pago, ".
							"spc.docg_id AS sopg ".
						"FROM sai_pago_cheque spc ".
						"INNER JOIN sai_pagado pg ON (pg.paga_docu_id = spc.pgch_id) ".
						"WHERE (spc.esta_id <> 2 OR (spc.esta_id = 2 AND pg.fecha_anulacion > '".$fecha_fin_dia."')) ".
						"UNION ".
						"SELECT ".
							"spt.trans_id AS id_pago, ".
							"spt.docg_id AS sopg ".
						"FROM sai_pago_transferencia spt ".
						"WHERE spt.esta_id <> 2 ".
						") AS spct ON (spct.sopg = scd.comp_doc_id) ".
					"LEFT OUTER JOIN sai_pagado sp ON (sp.paga_docu_id = spct.id_pago AND sp.pres_anno=".$ano_ini.") ".
					"LEFT OUTER JOIN ".
						"sai_pagado_dt spdt ON ".
							"(".
								"sp.paga_id = spdt.paga_id AND ".
								"src.part_id = spdt.part_id AND ".
								"src.pr_ac = spdt.padt_id_p_ac AND ".
								"src.a_esp = spdt.padt_cod_aesp AND ".
								"src.pr_ac_tipo = spdt.padt_tipo AND ".
								"src.pres_anno = spdt.pres_anno AND ".
								"spdt.pres_anno=".$ano_ini." ".
								(($tipo && $proy && $especif)?
									"AND spdt.padt_tipo=".$tipo."::BIT AND spdt.padt_id_p_ac='".$proy."' AND spdt.padt_cod_aesp='".$especif."' "
								:
									"AND CAST(spdt.pres_anno AS TEXT)||'-'||CAST(CAST(spdt.padt_tipo AS INT) AS TEXT)||'-'||spdt.padt_id_p_ac||'-'||spdt.padt_cod_aesp ".
										"IN (".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'0'||'-'||acce_id||'-'||aces_id ".
												"FROM sai_acce_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
												"UNION ".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'1'||'-'||proy_id||'-'||paes_id ".
												"FROM sai_proy_a_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
											")"
								)." ".
							") ".
				"WHERE ".
					//"scd.comp_fec BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."' AND ".
					"scd.comp_fec <= '".$fecha_fin_dia."' AND ".
					"sp.paga_fecha BETWEEN to_timestamp('".$fecha_ini." 00:00:00', 'YYYY-MM-DD HH24:MI:SS')  AND to_timestamp('".$fecha_fin_dia." 23:59:59', 'YYYY-MM-DD HH24:MI:SS') AND ".
					//"sp.esta_id != 15 AND ".
					"scd.esta_id<>15 AND ".
					"scd.comp_id LIKE 'coda%' AND ".
					"scd.comp_comen LIKE 'A-%' ".
				"UNION ".
				"SELECT ".
					"spct.sopg, ".
					"scd.comp_id, ".
					"sp.paga_id, ".
					"src.part_id, ".
					"src.rcomp_debe*-1 AS monto_debe, ".
					"src.rcomp_haber*-1 AS monto_haber, ".
					"0.0 AS monto_pres, ".
					"COALESCE(spdt.padt_monto,0.0) AS monto_pres_anulado, ".
					"src.pr_ac_tipo AS tipo_categoria, ".
					"src.pr_ac AS categoria, ".
					"src.a_esp AS accion_especifica ".
				"FROM ".
					"sai_comp_diario scd ".
					"INNER JOIN sai_reng_comp src ON (scd.comp_id = src.comp_id AND src.pres_anno=".$ano_ini." AND src.part_id IS NOT NULL AND TRIM(src.part_id) <>'' AND src.part_id NOT LIKE '4.11.0%' ".(($partida!=null && $partida!='')?"AND src.part_id = '".$partida."' ":"").") ".
					"INNER JOIN ".
						"(".
						"SELECT ".
							"spc.pgch_id AS id_pago, ".
							"spc.docg_id AS sopg ".
						"FROM sai_pago_cheque spc ".
						"INNER JOIN sai_pagado pg ON (pg.paga_docu_id = spc.pgch_id) ".
						"WHERE (spc.esta_id <> 2 OR (spc.esta_id = 2 AND pg.fecha_anulacion > '".$fecha_fin_dia."')) ".
						"UNION ".
						"SELECT ".
							"spt.trans_id AS id_pago, ".
							"spt.docg_id AS sopg ".
						"FROM sai_pago_transferencia spt ".
						"WHERE spt.esta_id <> 2 ".
						") AS spct ON (spct.id_pago = scd.comp_doc_id) ".
					"INNER JOIN sai_pagado sp ON (sp.paga_docu_id = scd.comp_doc_id 
							--AND (to_char(sp.paga_fecha,'YYYY-MM-DD') = to_char(scd.comp_fec,'YYYY-MM-DD') or to_char(sp.fecha_anulacion,
						--'YYYY-MM-DD') = to_char(scd.comp_fec,
						--'YYYY-MM-DD')) 
						AND sp.pres_anno=".$ano_ini.") ".
					"INNER JOIN ".
						"sai_pagado_dt spdt ON ".
							"(".
								"sp.paga_id = spdt.paga_id AND ".
								"src.part_id = spdt.part_id AND ".
								"src.pr_ac = spdt.padt_id_p_ac AND ".
								"src.a_esp = spdt.padt_cod_aesp AND ".
								"src.pr_ac_tipo = spdt.padt_tipo AND ".
								"src.pres_anno = spdt.pres_anno AND ".
								"spdt.pres_anno=".$ano_ini." ".
								(($tipo && $proy && $especif)?
									"AND spdt.padt_tipo=".$tipo."::BIT AND spdt.padt_id_p_ac='".$proy."' AND spdt.padt_cod_aesp='".$especif."' "
								:
									"AND CAST(spdt.pres_anno AS TEXT)||'-'||CAST(CAST(spdt.padt_tipo AS INT) AS TEXT)||'-'||spdt.padt_id_p_ac||'-'||spdt.padt_cod_aesp ".
										"IN (".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'0'||'-'||acce_id||'-'||aces_id ".
												"FROM sai_acce_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
												"UNION ".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'1'||'-'||proy_id||'-'||paes_id ".
												"FROM sai_proy_a_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
											")"
								)." ".
							") ".
				"WHERE ".
					"scd.comp_fec BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."' AND ".
					"sp.fecha_anulacion BETWEEN to_timestamp('".$fecha_ini." 00:00:00', 'YYYY-MM-DD HH24:MI:SS')  AND to_timestamp('".$fecha_fin_dia." 23:59:59', 'YYYY-MM-DD HH24:MI:SS') AND ".
					//"sp.esta_id != 15 AND ".
					"scd.esta_id<>15 AND ".
					"scd.comp_id LIKE 'coda%' AND ".
					"scd.comp_comen LIKE 'A_C-%' AND ".
					"(scd.comp_doc_id LIKE 'pgch%' OR scd.comp_doc_id LIKE 'tran%') ".
				"UNION ".
				"SELECT ".
					"scd.comp_id as sopg, ".
					"scd.comp_id, ".
					"sp.paga_id, ".
					"src.part_id, ".
					"src.rcomp_debe AS monto_debe, ".
					"src.rcomp_haber*-1 AS monto_haber, ".
					"COALESCE(spdt.padt_monto,0.0) AS monto_pres, ".
					"CASE ".
						"WHEN (sp.fecha_anulacion IS NOT NULL AND sp.fecha_anulacion BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."') THEN spdt.padt_monto ".
						"ELSE 0.0 END AS monto_pres_anulado, ".
					"src.pr_ac_tipo AS tipo_categoria, ".
					"src.pr_ac AS categoria, ".
					"src.a_esp AS accion_especifica ".
				"FROM ".
					"sai_comp_diario scd ".
					"INNER JOIN sai_reng_comp src ON (scd.comp_id = src.comp_id AND src.pres_anno=".$ano_ini." AND src.part_id IS NOT NULL AND TRIM(src.part_id) <>'' AND src.part_id NOT LIKE '4.11.0%' ".(($partida!=null && $partida!='')?"AND src.part_id = '".$partida."' ":"").") ".
					"LEFT OUTER JOIN sai_pagado sp ON (sp.paga_docu_id = scd.comp_id AND to_char(sp.paga_fecha,'YYYY-MM-DD') = to_char(scd.comp_fec,'YYYY-MM-DD') AND sp.pres_anno=".$ano_ini." AND sp.esta_id <> 2) ".
					"LEFT OUTER JOIN ".
						"sai_pagado_dt spdt ON ".
							"(".
								"sp.paga_id = spdt.paga_id AND ".
								"src.part_id = spdt.part_id AND ".
								"src.pr_ac = spdt.padt_id_p_ac AND ".
								"src.a_esp = spdt.padt_cod_aesp AND ".
								"src.pr_ac_tipo = spdt.padt_tipo AND ".
								"src.pres_anno = spdt.pres_anno AND ".
								"spdt.pres_anno=".$ano_ini." ".
								(($tipo && $proy && $especif)?
									"AND spdt.padt_tipo=".$tipo."::BIT AND spdt.padt_id_p_ac='".$proy."' AND spdt.padt_cod_aesp='".$especif."' "
								:
									"AND CAST(spdt.pres_anno AS TEXT)||'-'||CAST(CAST(spdt.padt_tipo AS INT) AS TEXT)||'-'||spdt.padt_id_p_ac||'-'||spdt.padt_cod_aesp ".
										"IN (".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'0'||'-'||acce_id||'-'||aces_id ".
												"FROM sai_acce_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
												"UNION ".
												"SELECT ".
													"CAST(pres_anno AS TEXT)||'-'||'1'||'-'||proy_id||'-'||paes_id ".
												"FROM sai_proy_a_esp ".
												"WHERE ".
													"pres_anno=".$ano_ini." ".
											")"
								)." ".
							") ".
				"WHERE ".
					"scd.comp_fec BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."' AND ".
					//"sp.paga_fecha BETWEEN to_timestamp('".$fecha_ini." 00:00:00', 'YYYY-MM-DD HH24:MI:SS')  AND to_timestamp('".$fecha_fin_dia." 23:59:59', 'YYYY-MM-DD HH24:MI:SS') AND ".
					//"sp.esta_id != 15 AND ".
					"scd.esta_id<>15 AND ".
					"scd.comp_id LIKE 'codi%' AND scd.comp_id NOT IN ('codi-452155011','codi-452155111','codi-452155211','codi-452281411') ".
				") AS s ".
				"GROUP BY s.sopg, s.part_id, s.tipo_categoria, s.categoria, s.accion_especifica ".
				") AS se 
				".(($fuenteFinanciamiento != null && $fuenteFinanciamiento != '' && $fuenteFinanciamiento != '0')?
					"INNER JOIN 		
						(SELECT
							sf1125.form_tipo,
							sf1125.form_id_p_ac,
							sf1125.form_id_aesp						
						FROM 
							sai_forma_1125 sf1125
						WHERE 
							sf1125.pres_anno=".$ano_ini." AND
							TRIM(UPPER(sf1125.fuente_financiamiento)) = '".$fuenteFinanciamiento."'
						GROUP BY 
							sf1125.form_tipo,
							sf1125.form_id_p_ac,
							sf1125.form_id_aesp
						) AS s ON
							(s.form_tipo = se.tipo_categoria AND
							s.form_id_p_ac = se.categoria AND
							s.form_id_aesp = se.accion_especifica) ":"")."
				GROUP BY se.part_id, se.tipo_categoria, se.categoria, se.accion_especifica ".
				"ORDER BY se.part_id, se.tipo_categoria, se.categoria, se.accion_especifica ";
			}
	
	//echo "<pre>".$sql."</pre>";//COLOCAR EN LAS ANULACIONES sc.fecha_anulacion = scd.comp_fec_emis
//	error_log(print_r($sql, true));
	$resultado=pg_query($conexion,$sql);

	if($tipoReporte=="1"){
		echo "<b>REPORTE PRESUPUESTARIO DEL CAUSADO ";		
	}else if($tipoReporte=="2"){
		echo "<b>REPORTE PRESUPUESTARIO DEL PAGADO ";
	}
	if($_POST['partida']!=null && $_POST['partida']!=''){
		echo "PARTIDA: ".$_POST["partida"];
	}
	if($_POST['hid_desde_itin']!=null && $_POST['hid_desde_itin']!='' && $_POST['hid_hasta_itin']!=null && $_POST['hid_hasta_itin']!=''){
		echo " ENTRE: ".$fechaInicio." y ".$fechaFin;
	}
	echo "</b>";
	?>
	<br/><br/>
	<table border="1" class="tablaalertas">
		<tr>
			<td bgcolor="#E4E4E4" class="peq" align="center">
				<strong>Partida</strong>
			</td>
			<?php
			for($i=0;$i<$totalproy;$i++){
			?>
			<td width="85" height="14" bgcolor="#E4E4E4" align="center" class="peq">
				<strong><?= $centroGestor[$i]."/".$centroCosto[$i]?></strong>
			</td>
			<?php
			}
			?>
			<td bgcolor="#E4E4E4" class="peq" align="center">
				<strong>Total</strong>
			</td>
		</tr>
		<?php
		$montototalcausado=0;
		$montototalcausadopres=0;
		$indiceCategoria=0;
		$partidaAnterior="";
		while($row=pg_fetch_array($resultado)){
			if(	$row['part_id']!="" &&
				$row['tipo_categoria']!="" &&
				$row['categoria']!="" &&
				$row['accion_especifica']!=""){
				if($partidaAnterior!=$row['part_id']){
					if($partidaAnterior!=""){
						while($indiceCategoria<$totalproy){
						?>
							<td align="right" class="peq" width="100">0,00</td>
						<?php
							$indiceCategoria++;
						}
						?>
							<td align="right" class="peq" width="100">
								<b>
						<?php
						if(($montototalcausado==$montototalcausadopres) || (round($montototalcausado-$montototalcausadopres)==0)){
							echo number_format($montototalcausado,2,',','.');
						}else{
							echo number_format($montototalcausado,2,',','.')." <span style='color: red;'>(R" .number_format($montototalcausadopres,2,',','.').")</span>
							<span style='color: red;'>Dif: " .number_format($montototalcausado-$montototalcausadopres,2,',','.')."</span>
							";
						}
		?>
								</b>
							</td>
						</tr>
		<?php
					}
					$partidaAnterior=$row['part_id'];
					$montototalcausado=0;
					$montototalcausadopres=0;
					$indiceCategoria=0;
		?>
						<tr>
							<td colspan="<?= $contador+2?>">
								<hr size="1" width="100%"/>
							</td>
						</tr>
						<tr>
							<td width="45" align="center" valign="middle" class="peq">
								<a href="reporte_causadopagadoDetalle.php?partida=<?= $row['part_id']?>&hid_desde_itin=<?= $_POST['hid_desde_itin']?>&hid_hasta_itin=<?= $_POST['hid_hasta_itin']?>&proyac=<?= $_REQUEST['proyac']?>&tipoReporte=<?= $tipoReporte?>&fuenteFinanciamiento=<?= $fuenteFinanciamiento?>" target="_blanc"><?= $row['part_id']?></a>
							</td>	
		<?php
				}
				while(($tipoCategoria[$indiceCategoria]!=$row['tipo_categoria'] || $categoria[$indiceCategoria]!=$row['categoria'] || $accionEspecifica[$indiceCategoria]!=$row['accion_especifica']) && $indiceCategoria<$totalproy){
		?>
					<td align="right" class="peq" width="100">0,00</td>
		<?php
					$indiceCategoria++;
				}
				
				if($indiceCategoria<$totalproy){
					$debe=$row['monto_debe'];
					$haber=$row['monto_haber'];
					$montopres=$row['monto_pres'];
					$montopresanulado=$row['monto_pres_anulado'];
					$causado=$debe+$haber;
					$causadopres=$montopres-$montopresanulado;
					$montototalcausado+=$causado;
					$montototalcausadopres+=$causadopres;
					$totalByProy[$indiceCategoria] += $causado;
		?>
					<td align="right" class="peq" width="100">
		<?php 
					if(($causado==$causadopres) || (round($causado-$causadopres)==0)){
					//if($causado-$causadopres==0){
						echo number_format($causado,2,',','.');
						$totalByProyPres[$indiceCategoria] += $causado;
					}else{
						echo number_format($causado,2,',','.')." <span style='color: red;'>(".number_format($causadopres,2,',','.').")</span>
						<span style='color: red;'>Dif: ".number_format($causado-$causadopres,2,',','.')."</span>";
						$totalByProyPres[$indiceCategoria] += $causadopres;
					}
		?>
					</td>
		<?php
					 $indiceCategoria++;
				}
			}
		}
		
		while($indiceCategoria<$totalproy){
		?>
			<td align="right" class="peq" width="100">0,00</td>
		<?php
			$indiceCategoria++;
		}
		?>
			<td align="right" class="peq" width="100">
				<b>
		<?php
		if(($montototalcausado==$montototalcausadopres) || (round($montototalcausado-$montototalcausadopres)==0)){
			echo number_format($montototalcausado,2,',','.');
		}else{
			echo number_format($montototalcausado,2,',','.')." <span style='color: red;'>(R" .number_format($montototalcausadopres,2,',','.').")</span>
			<span style='color: red;'>Dif: " .number_format($montototalcausado-$montototalcausadopres,2,',','.')."</span>
			";
		}
		?>
				</b>
			</td>
		</tr>
		<tr>
			<td bgcolor="#E4E4E4" class="peq" align="center">
				<strong>Total</strong>
			</td>
			<?php
			for($i=0;$i<$totalproy;$i++){
			?>
			<td width="85" height="14" bgcolor="#E4E4E4" align="center" class="peq">
				<strong><?php
					if(($totalByProy[$i]==$totalByProyPres[$i]) || (round($totalByProy[$i]-$totalByProyPres[$i])==0)){
						echo number_format($totalByProy[$i],2,',','.');
					}else{
						echo number_format($totalByProy[$i],2,',','.')." <span style='color: red;'>(".number_format($totalByProyPres[$i],2,',','.').")</span>
						<span style='color: red;'>Dif: ".number_format($totalByProy[$i]-$totalByProyPres[$i],2,',','.')."</span>
						";
					}
				?></strong>
			</td>
			<?php
			}
			?>
			<td bgcolor="#E4E4E4" class="peq" align="center">
				<strong><?php
					$totalTotal = 0;
					$totalTotalPres = 0;
					foreach ($totalByProy as $montoTmp){
						$totalTotal += $montoTmp;	
					}
					foreach ($totalByProyPres as $montoTmp){
						$totalTotalPres += $montoTmp;	
					}
					if(($totalTotal==$totalTotalPres) || (round($totalTotal-$totalTotalPres)==0)){
						echo number_format($totalTotal,2,',','.');
					}else{
						echo number_format($totalTotal,2,',','.')."
						<span style='color: red;'>(".number_format($totalTotalPres,2,',','.').")</span>
						<span style='color: red;'>Dif: ".number_format($totalTotal-$totalTotalPres,2,',','.')."</span>
						";
					}
				?></strong>
			</td>
		</tr>
		<?php
	}
	pg_close($conexion);
	?>
	</table>
</font>
<table width="667" border="0" align="center">
	<tr>
		<td width="632" scope="col">&nbsp;</td>
	</tr>
</table>
<p>&nbsp;</p>
</body>
</html>