<?php
ob_start();
session_start();
require("../../includes/conexion.php");

if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	//header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Reporte presupestario causado</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body class="normal">
<br/>
<?php 
$fechaInicio= $_REQUEST["hid_desde_itin"];
$fechaFin=$_REQUEST["hid_hasta_itin"];
$partida=$_REQUEST["partida"];
$tipoReporte=$_REQUEST["tipoReporte"];
$fuenteFinanciamiento=$_REQUEST["fuenteFinanciamiento"];
if($_REQUEST['proyac']!=null && $_REQUEST['proyac']!='0') {
	list($tipo,$proy,$especif) = split(':::',$_REQUEST['proyac']);
}

$fecha_ini=substr($fechaInicio,6,4)."-".substr($fechaInicio,3,2)."-".substr($fechaInicio,0,2);
$ano_ini=substr($fecha_ini,0,4);
$fecha_fin_dia=substr($fechaFin,6,4)."-".substr($fechaFin,3,2)."-".substr($fechaFin,0,2)." 23:59:59";

$montototalcausado=0;
$montototalcausadopres=0;
$causado;

if($tipoReporte=="1"){//CAUSADO
	$sql = 	"SELECT ".
				"s.sopg, ".
				"s.comp_id, ".
				"s.part_id, ".
				"SUM(s.monto_debe) AS monto_debe, ".
				"SUM(s.monto_haber) AS monto_haber, ".
				"SUM(s.monto_pres) AS monto_pres, ".
				"SUM(s.monto_pres_anulado) AS monto_pres_anulado, ".
				"s.tipo_categoria, ".
				"s.categoria, ".
				"s.accion_especifica, ".
				"scp.centro_gestor, ".
				"scp.centro_costo, ".
				"sdg.numero_reserva ".
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
			"INNER JOIN sai_doc_genera sdg ON (s.sopg=sdg.docg_id) ".
			"INNER JOIN (".
						"SELECT ".
							"pres_anno, ".
							"acce_id AS categoria, ".
							"aces_id AS accion_especifica, ".
							"0::BIT AS tipo_categoria, ".
							"centro_gestor, ".
							"centro_costo ".
						"FROM sai_acce_esp ".
						//"WHERE ".
							//"pres_anno=".$ano_ini." ".
							//(($proy && $especif)?"AND acce_id='".$proy."' AND aces_id='".$especif."' ":"").
							(($proy && $especif)?"WHERE acce_id='".$proy."' AND aces_id='".$especif."' ":"").
						"UNION ".
						"SELECT ".
							"pres_anno, ".
							"proy_id AS categoria, ".
							"paes_id AS accion_especifica, ".
							"1::BIT AS tipo_categoria, ".
							"centro_gestor, ".
							"centro_costo ".
						"FROM sai_proy_a_esp ".
						//"WHERE ".
							//"pres_anno=".$ano_ini." ".
							//(($proy && $especif)?"AND proy_id='".$proy."' AND paes_id='".$especif."' ":"").
							(($proy && $especif)?"WHERE proy_id='".$proy."' AND paes_id='".$especif."' ":"").
						") AS scp ON (s.tipo_categoria = scp.tipo_categoria AND s.categoria = scp.categoria AND s.accion_especifica = scp.accion_especifica) 
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
						) AS sff ON
							(sff.form_tipo = s.tipo_categoria AND
							sff.form_id_p_ac = s.categoria AND
							sff.form_id_aesp = s.accion_especifica) ":"")."
			GROUP BY s.sopg, s.comp_id, s.part_id, s.tipo_categoria, s.categoria, s.accion_especifica, scp.centro_gestor, scp.centro_costo, sdg.numero_reserva ".
			"ORDER BY s.part_id, s.tipo_categoria, s.categoria, s.accion_especifica, s.sopg, s.comp_id ";
}else{ //PAGADO
	$sql = 	"SELECT ".
				"s.sopg, ".
				"s.comp_id, ".
				"s.part_id, ".
				"SUM(s.monto_debe) AS monto_debe, ".
				"SUM(s.monto_haber) AS monto_haber, ".
				"SUM(s.monto_pres) AS monto_pres, ".
				"SUM(s.monto_pres_anulado) AS monto_pres_anulado, ".
				"s.tipo_categoria, ".
				"s.categoria, ".
				"s.accion_especifica, ".
				"scp.centro_gestor, ".
				"scp.centro_costo, ".
				"sdg.numero_reserva ".
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
				"scd.esta_id<>15 AND ".
				"scd.comp_id LIKE 'coda%' AND ".
				"(scd.comp_comen LIKE 'C-%')". ///*OR scd.comp_comen LIKE 'A-%'*/
			/*"UNION ".
			"SELECT ".
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
				"INNER JOIN sai_pagado sp ON (sp.paga_docu_id = spct.id_pago AND sp.pres_anno=".$ano_ini." AND sp.esta_id <> 2 AND sp.fecha_anulacion IS NOT NULL AND sp.fecha_anulacion BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."') ".
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
				"sp.paga_fecha BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."' AND ".
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
			") AS s ".*/
			"UNION ".
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
				"sp.paga_fecha BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."' AND ".
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
				"COALESCE(spdt.padt_monto,0.0) AS monto_pres_anulado,  ".
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
				"LEFT OUTER JOIN sai_pagado sp ON (sp.paga_docu_id = scd.comp_doc_id AND sp.pres_anno=".$ano_ini.") ".
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
				"sp.fecha_anulacion BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."' AND ".
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
				//"sp.paga_fecha BETWEEN '".$fecha_ini."' AND '".$fecha_fin_dia."' AND ".
				"scd.esta_id<>15 AND ".
				"scd.comp_id LIKE 'codi%' AND scd.comp_id NOT IN ('codi-452155011','codi-452155111','codi-452155211','codi-452281411') ".
			") AS s ".
			"INNER JOIN sai_doc_genera sdg ON (s.sopg=sdg.docg_id) ".
			"INNER JOIN (".
						"SELECT ".
							"pres_anno, ".
							"acce_id AS categoria, ".
							"aces_id AS accion_especifica, ".
							"0::BIT AS tipo_categoria, ".
							"centro_gestor, ".
							"centro_costo ".
						"FROM sai_acce_esp ".
						//"WHERE ".
							//"pres_anno=".$ano_ini." ".
							//(($proy && $especif)?"AND acce_id='".$proy."' AND aces_id='".$especif."' ":"").
							(($proy && $especif)?"WHERE acce_id='".$proy."' AND aces_id='".$especif."' ":"").
						"UNION ".
						"SELECT ".
							"pres_anno, ".
							"proy_id AS categoria, ".
							"paes_id AS accion_especifica, ".
							"1::BIT AS tipo_categoria, ".
							"centro_gestor, ".
							"centro_costo ".
						"FROM sai_proy_a_esp ".
						//"WHERE ".
							//"pres_anno=".$ano_ini." ".
							//(($proy && $especif)?"AND proy_id='".$proy."' AND paes_id='".$especif."' ":"").
							(($proy && $especif)?"WHERE proy_id='".$proy."' AND paes_id='".$especif."' ":"").
						") AS scp ON (s.tipo_categoria = scp.tipo_categoria AND s.categoria = scp.categoria AND s.accion_especifica = scp.accion_especifica) 
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
						) AS sff ON
							(sff.form_tipo = s.tipo_categoria AND
							sff.form_id_p_ac = s.categoria AND
							sff.form_id_aesp = s.accion_especifica) ":"")."
			GROUP BY s.sopg, s.comp_id, s.part_id, s.tipo_categoria, s.categoria, s.accion_especifica, scp.centro_gestor, scp.centro_costo, sdg.numero_reserva ".
			"ORDER BY s.part_id, s.tipo_categoria, s.categoria, s.accion_especifica, s.sopg, s.comp_id ";
}

$resultado=pg_query($conexion,$sql) or die("Error al consultar las Cuentas1");

$mesI=substr($fechaInicio, 3, 2);
$mesF=substr($fechaFin, 3, 2);

if($tipoReporte=="1"){
	echo "<b>REPORTE PRESUPUESTARIO DEL CAUSADO ";		
}else if($tipoReporte=="2"){
	echo "<b>REPORTE PRESUPUESTARIO DEL PAGADO ";
}
if($_REQUEST['partida']!=null && $_REQUEST['partida']!=''){
	echo "PARTIDA: ".$_REQUEST["partida"];
}
if($_REQUEST['hid_desde_itin']!=null && $_REQUEST['hid_desde_itin']!='' && $_REQUEST['hid_hasta_itin']!=null && $_REQUEST['hid_hasta_itin']!=''){
	echo " ENTRE: ".$fechaInicio." y ".$fechaFin."<br>";
}
echo "</b>";
?>
<br/>
<table width="70%" border="1" class="tablaalertas">
	<tr>
		<td bgcolor="#E4E4E4" class="peq" align="center">
			<strong>Documento</strong>
		</td>
		<td bgcolor="#E4E4E4" class="peq" align="center">
			<strong>Comprobante</strong>
		</td>
		<td bgcolor="#E4E4E4" class="peq" align="center">
			<strong>Centro Gestor/Centro Costo</strong>
		</td>
		<td bgcolor="#E4E4E4" align="center" class="peq">
			<strong>Monto Contable</strong>
		</td>
		<td bgcolor="#E4E4E4" align="center" class="peq">
			<strong>Monto Presupuesto</strong>
		</td>
		<td bgcolor="#E4E4E4" align="center" class="peq">
			<strong>N&uacute;mero Reserva</strong>
		</td>
	</tr>
	<?php
	$tipoAnterior="";
	$categoriaProgramaticaAnterior="";
	$accionEspecificaAnterior="";
	$centroGestorAnterior="";
	$centroCostoAnterior="";
	$numeroReservaAnterior="";
	$sopgAnterior="";
	$compAnterior="";
	$causadoAnterior=0;
	$causadoPresAnterior=0;	
	while($rowor=pg_fetch_array($resultado)){
		$debe=$rowor['monto_debe'];
		$haber=$rowor['monto_haber'];
		$causado=$debe+$haber;
		$montopres=$rowor['monto_pres'];
		$montopresanulado=$rowor['monto_pres_anulado'];
		$causadopres=$montopres-$montopresanulado;

		if(	$sopgAnterior!="" &&	($rowor['sopg']!=$sopgAnterior ||
									$tipoAnterior!=$rowor['tipo_categoria'] ||
									$categoriaProgramaticaAnterior!=$rowor['categoria'] ||
									$accionEspecificaAnterior!=$rowor['accion_especifica'])){
			$color = "";
			if($causadoAnterior!=$causadoPresAnterior && (round($causadoAnterior-$causadoPresAnterior)!=0)){
				$color = "style='color: red;'";	
			}		
	?>
			<tr <?= $color?>>
				<td><b><?= $sopgAnterior;?></b></td>
				<td align="center"><?= $compAnterior;?></td>
				<td align="center"><?= $centroGestorAnterior."/".$centroCostoAnterior;?></td>
				<td align="right"><?= number_format($causadoAnterior,2,',','.');?></td>
				<td align="right"><?= number_format($causadoPresAnterior,2,',','.');?></td>
				<td align="center"><?= $numeroReservaAnterior;?></td>
			</tr>
	<?php
			$causadoAnterior=0;
			$causadoPresAnterior=0;
		}
		
		if(	$tipoAnterior!="" && 
			$categoriaProgramaticaAnterior!="" && 
			$accionEspecificaAnterior!="" &&
			(	$tipoAnterior!=$rowor['tipo_categoria'] ||
				$categoriaProgramaticaAnterior!=$rowor['categoria'] ||
				$accionEspecificaAnterior!=$rowor['accion_especifica']	)
		){
			$color = "";
			if($montototalcausado!=$montototalcausadopres && (round($montototalcausado-$montototalcausadopres)!=0)){
				$color = "style='color: red;'";	
			}
	?>
			<tr>
				<td colspan="3" align="right"><b>Total</b></td>
				<td align="right" <?= $color?>><b><?= number_format($montototalcausado,2,',','.');?></b></td>
				<td align="right" <?= $color?>><b><?= number_format($montototalcausadopres,2,',','.');?></b></td>
				<td></td>
			</tr>
	<?php
			$montototalcausado=0;
			$montototalcausadopres=0;
		}
		$montototalcausado+=$causado;
		$montototalcausadopres+=$causadopres;
		$causadoAnterior+=$causado;
		$causadoPresAnterior+=$causadopres;
		$tipoAnterior=$rowor['tipo_categoria'];
		$categoriaProgramaticaAnterior=$rowor['categoria'];
		$accionEspecificaAnterior=$rowor['accion_especifica'];
		$centroGestorAnterior=$rowor['centro_gestor'];
		$centroCostoAnterior=$rowor['centro_costo'];
		$numeroReservaAnterior=$rowor['numero_reserva'];
		$sopgAnterior=$rowor['sopg'];
		$compAnterior=$rowor['comp_id'];
	}
	
	$color = "";
	if($causadoAnterior!=$causadoPresAnterior && (round($causadoAnterior-$causadoPresAnterior)!=0)){
		$color = "style='color: red;'";	
	}
	?>
		<tr <?= $color?>>
			<td><b><?= $sopgAnterior;?></b></td>
			<td align="center"><?= $compAnterior;?></td>
			<td align="center"><?= $centroGestorAnterior."/".$centroCostoAnterior;?></td>
			<td align="right"><?= number_format($causadoAnterior,2,',','.');?></td>
			<td align="right"><?= number_format($causadoPresAnterior,2,',','.');?></td>
			<td align="center"><?= $numeroReservaAnterior;?></td>
		</tr>
	<?php
	
		$color = "";
		if($montototalcausado!=$montototalcausadopres && (round($montototalcausado-$montototalcausadopres)!=0)){
			$color = "style='color: red;'";	
		}
	?>
	<tr>
		<td colspan="3" align="right"><b>Total</b></td>
		<td align="right" <?= $color?>><b><?= number_format($montototalcausado,2,',','.');?></b></td>
		<td align="right" <?= $color?>><b><?= number_format($montototalcausadopres,2,',','.');?></b></td>
		<td></td>
	</tr>
	<?php
	pg_close($conexion);
	?>
</table>
<table width="667" border="0" align="center">
	<tr>
		<td width="632" scope="col">&nbsp;</td>
	</tr>
</table>
<p>&nbsp;</p>
</body>
</html>