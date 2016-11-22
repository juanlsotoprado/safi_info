<?
ob_start();
session_start();
require_once("../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
		   header('Location:../../index.php',false);
	   	   ob_end_flush(); 
		   exit;
}
	
ob_end_flush(); 
$partida=$_GET['partida'];
$idProyectoAccion=$_GET['proy'];
$idAccionEspecifica=$_GET['aesp'];
$fechaInicio=$_GET['fecha_inicio'];
$fechaFin=$_GET['fecha_fin'];
$tipoImputacion=$_GET['tipo'];
$opcionConsolidar=$_GET['consolidado'];
$centroGestor=$_GET['centroGestor'];
$monto=$_GET['monto'];	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>SAFI:Detalle Causado</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../js/funciones.js"> </script>
</head>
<body>
<?
list($diaInicio,$mesInicio,$anioInicio) = split( '[/.-]',$fechaInicio);
list($diaFin,$mesFin,$anioFin) = split( '[/.-]', $fechaFin);

/*$sql_str="SELECT * FROM sai_pres_reporte_mcausados_det(".$anioFin.",'".$tipo."','".$proy."','".$id_aesp."','".$partida."','','".$opcionConsolidar."','".$anioInicio."/".$mesInicio."/".$diaInicio."','".$anioFin."/".$mesFin."/".$diaFin."') as resultado";//
$res=pg_exec($sql_str);
$row_dif=pg_fetch_array($res);
$apartados = explode(",", $row_dif['resultado']);*/

$detalleCausado = '';
if($opcionConsolidar == '0' && $centroGestor == ''){
	$query =		"SELECT ".
						"sc.caus_docu_id AS documento, ".
						//"scd.cadt_monto AS monto ".
						"CASE ".
						"WHEN (CHAR_LENGTH(TO_CHAR(sc.fecha_anulacion, 'DD/MM/YYYY')) <1 OR 
								sc.fecha_anulacion IS NULL OR
								CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') 
								) THEN ".
						"scd.cadt_monto ".
						"ELSE scd.cadt_monto*-1 ".
						"END AS monto, ".
	
						"CAST(SUBSTR(sc.caus_docu_id,6) as bigint) ".	
					"FROM sai_causado sc, sai_causad_det scd ".
					"WHERE ".
						"sc.pres_anno = ".$anioInicio." AND ".
						"sc.esta_id <> 2 AND ".
						/*"sc.esta_id <> 15 AND ".*/

						" ( ".
						"( ".
						"sc.fecha_anulacion IS NULL AND ".
						"CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".
						
						") ".
						
						"OR ".
						"(sc.fecha_anulacion IS NOT NULL ".
						"AND CAST(sc.caus_fecha AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".						
						"AND CAST(sc.fecha_anulacion AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".
						") ".
						
						"OR ".
						"(sc.fecha_anulacion IS NOT NULL ".
						"AND CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".
						"AND CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".
						") ".
						
						
						") AND ".	
						//"CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ".
						//"(sc.fecha_anulacion IS NULL OR (CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) AND ".
						"sc.caus_id = scd.caus_id AND ".
						"sc.pres_anno = scd.pres_anno AND ".
						"scd.cadt_abono='1' AND ".
						"scd.cadt_tipo = ".$tipoImputacion."::BIT AND ".
						"scd.cadt_id_p_ac = '".$idProyectoAccion."' AND ".
						"scd.cadt_cod_aesp = '".$idAccionEspecifica."' AND ";
	if(substr($partida,-8) == '00.00.00'){
		$query .= 		"scd.part_id LIKE '".substr($partida,0,4)."' ";
	}else if(substr($partida,-5) == '00.00.00'){
		$query .= 		"scd.part_id LIKE '".substr($partida,0,7)."' ";
	}else{
		$query .= 		"scd.part_id LIKE '".$partida."' ";
	}
	$query .=		"ORDER BY 3";
	$resultado=pg_exec($query);
	while($fila=pg_fetch_array($resultado)){
		$detalleCausado .= ','.$fila["documento"].':'.$fila["monto"];
	}
}else{
	$query =		"SELECT ".
						"sc.caus_docu_id AS documento, ".
						//"scd.cadt_monto AS monto ".
						"CASE ".
						"WHEN (CHAR_LENGTH(TO_CHAR(sc.fecha_anulacion, 'DD/MM/YYYY')) <1 OR 
								sc.fecha_anulacion IS NULL OR
								CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') 
								) THEN ".
						"scd.cadt_monto ".
						"ELSE scd.cadt_monto*-1 ".
						"END AS monto, ".
						"CAST(substr(sc.caus_docu_id,6) as bigint) ".
					"FROM sai_causado sc, sai_causad_det scd, ".
					"(";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anioInicio." ";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anioInicio." ";
		}			
	}else if($opcionConsolidar=="2"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anioInicio." ";
	}else if($opcionConsolidar=="3"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anioInicio." ".
					"UNION ".
					"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.pres_anno = ".$anioInicio." ";
	}
	$query.=		") AS s ".
					"WHERE ";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		$query.=		"scd.cadt_tipo = ".$tipoImputacion."::BIT AND ".
						"scd.cadt_id_p_ac = '".$idProyectoAccion."' AND ";
	}
	$query.=			"sc.pres_anno = ".$anioInicio." AND ".
						"sc.esta_id <> 2 AND ".
						/*"sc.esta_id <> 15 AND ".*/
	
						" ( ".
								"( ".
										"sc.fecha_anulacion IS NULL AND ".
										"CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".
						
								") ".
						
								"OR ".
								"(sc.fecha_anulacion IS NOT NULL ".
										"AND CAST(sc.caus_fecha AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".								
										"AND CAST(sc.fecha_anulacion AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".
								") ".
								
								"OR ".
								"(sc.fecha_anulacion IS NOT NULL ".
								"AND CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".
								"AND CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".
								") ".
								
						") AND ".
	
	
	
	
	
						//"CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ".
						//"(sc.fecha_anulacion IS NULL OR (CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) AND ".
						"sc.caus_id = scd.caus_id AND ".
						"sc.pres_anno = scd.pres_anno AND ".
						"scd.cadt_abono='1' AND ".
						"scd.cadt_id_p_ac = s.id_proyecto_accion AND ".
						"scd.cadt_cod_aesp = s.id_accion_especifica AND ";
	if(substr($partida,-8) == '00.00.00'){
		$query .= 		"scd.part_id LIKE '".substr($partida,0,4)."' ";
	}else if(substr($partida,-5) == '00.00.00'){
		$query .= 		"scd.part_id LIKE '".substr($partida,0,7)."' ";
	}else{
		$query .= 		"scd.part_id LIKE '".$partida."' ";
	}
	$query .=	"ORDER BY 3";
	//echo $query;
	$resultado=pg_exec($query);
	while($fila=pg_fetch_array($resultado)){
		$detalleCausado .= ','.$fila["documento"].':'.$fila["monto"];
	}
}
?>
<div class="normalNegroNegrita" align="center">Detalle causado - Monto total Bs.:<?= number_format($monto,2,',','.');?></div>
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
		<td class="normalNegroNegrita">C&oacute;digo</td>
		<td class="normalNegroNegrita">Monto Bs.</td>
		<td class="normalNegroNegrita">Partida</td>
	</tr>
<?php
$causados = explode(",", $detalleCausado);
$sumatoria=0; 
for($i=1;$i<count($causados);$i++){
?>
	<tr class="normal">
	<?php
	list($codigo,$monto) = split( '[:]', $causados[$i]);
	$sumatoria+=$monto;
	?>
		<td>
		<?php
			if(strcmp(substr(trim($codigo), 0, 4),"sopg")==0){
		?>
			<a href="javascript:abrir_ventana('../../documentos/sopg/sopg_detalle.php?codigo=<?= trim($codigo)?>&amp;esta_id=10')" class="copyright"><?= $codigo?></a>
		<?
			}else if(strcmp(substr(trim($codigo), 0, 4),"codi")==0){
		?>
			<a href="javascript:abrir_ventana('../../documentos/codi/codi_detalle.php?codigo=<?= trim($codigo)?>&amp;esta_id=10')" class="copyright"><?= $codigo?></a>
		<?
			}else{
				echo $codigo;
			}
		?>
		</td>	
		<td><?= number_format($monto,2,',','.');?></td>
		<td><?= $partida?></td>
	</tr>
<?php 
}
pg_close($conexion);
?>
	<tr class="td_gray">
		<td class="normalNegroNegrita">Total Bs.:</td>
		<td colspan="2" class="normalNegroNegrita"><?= number_format($sumatoria,2,',','.');?></td>
	</tr>
</table>