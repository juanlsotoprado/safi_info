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
<title>SAFI:Detalle Pagado</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../js/funciones.js"> </script>
</head>
<body>
<?
list($diaInicio,$mesInicio,$anioInicio) = split( '[/.-]', $fechaInicio);
list($diaFin,$mesFin,$anioFin) = split( '[/.-]', $fechaFin);

/*
$query=	"SELECT ".
					"spd.part_id as partida, ".
					"spd.padt_monto AS monto, ".
					"sp.paga_docu_id as codigo ".	
				"FROM sai_pagado sp, sai_pagado_dt spd ".
				"WHERE ".
					"sp.pres_anno = ".$anioFin." AND ".
					"sp.esta_id <> 15 AND sp.esta_id <> 2 AND ".
					"sp.paga_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
					"sp.paga_id = spd.paga_id AND ".
					"sp.pres_anno = spd.pres_anno AND ".
					//"spd.padt_tipo = '".$tipoImputacion."' AND ".
					"padt_cod_aesp = '".$id_aesp."' AND ".
					//"spd.padt_abono='1' AND ".
					"spd.part_id LIKE '".$partida."%' ".
				"ORDER BY sp.paga_docu_id";
$resultadoMontosPagados=pg_query($query) ;
*/

$detallePagado = '';
if($opcionConsolidar == '0' && $centroGestor == ''){
	$query =	"SELECT ".
					"spd.padt_monto AS monto, ".
					"sp.paga_docu_id as documento ".	
					",cast(substr(sp.paga_docu_id,6) as integer) ".		
				"FROM sai_pagado sp, sai_pagado_dt spd ".
				"WHERE ".
					"sp.pres_anno = ".$anioInicio." AND ".
					"sp.esta_id <> 2 AND ".
					/*"sp.esta_id <> 15 AND ".*/
					"CAST(sp.paga_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ".
					"(sp.fecha_anulacion IS NULL OR (CAST(sp.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) AND ".
					"sp.paga_id = spd.paga_id AND ".
					"sp.pres_anno = spd.pres_anno AND ".
					"spd.padt_tipo = '".$tipoImputacion."'::BIT AND ".
					"spd.padt_id_p_ac = '".$idProyectoAccion."' AND ".
					"spd.padt_cod_aesp = '".$idAccionEspecifica."' AND ";
	if(substr($partida,-8) == '00.00.00'){
		$query .= 	"spd.part_id LIKE '".substr($partida,0,4)."' ";
	}else if(substr($partida,-5) == '00.00.00'){
		$query .= 	"spd.part_id LIKE '".substr($partida,0,7)."' ";
	}else{
		$query .= 	"spd.part_id LIKE '".$partida."' ";
	}
	$query .=	"ORDER BY 3";
	$resultado=pg_exec($query);
	while($fila=pg_fetch_array($resultado)){
		$detallePagado .= ','.$fila["documento"].':'.$fila["monto"];
	}
}else{
	$query =	"SELECT ".
					"spd.padt_monto AS monto, ".
					"sp.paga_docu_id AS documento ".	
					",cast(substr(sp.paga_docu_id,6) as integer) ".	
				"FROM sai_pagado sp, sai_pagado_dt spd, ".
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
		$query.=	"spd.padt_tipo = '".$tipoImputacion."'::BIT AND ".
					"spd.padt_id_p_ac = '".$idProyectoAccion."' AND ";
	}
	$query.=		"sp.pres_anno = ".$anioInicio." AND ".
					"sp.esta_id <> 2 AND ".
					/*"sp.esta_id <> 15 AND ".*/
					"CAST(sp.paga_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ".
					"(sp.fecha_anulacion IS NULL OR (CAST(sp.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) AND ".
					"sp.paga_id = spd.paga_id AND ".
					"sp.pres_anno = spd.pres_anno AND ".
					"spd.padt_id_p_ac = s.id_proyecto_accion AND ".
					"spd.padt_cod_aesp = s.id_accion_especifica AND ";
	if(substr($partida,-8) == '00.00.00'){
		$query .= 	"spd.part_id LIKE '".substr($partida,0,4)."' ";
	}else if(substr($partida,-5) == '00.00.00'){
		$query .= 	"spd.part_id LIKE '".substr($partida,0,7)."' ";
	}else{
		$query .= 	"spd.part_id LIKE '".$partida."' ";
	}
	$query .=	"ORDER BY 3";
	$resultado=pg_exec($query);
	while($fila=pg_fetch_array($resultado)){
		$detallePagado .= ','.$fila["documento"].':'.$fila["monto"];
	}
}
?>
<div class="normalNegroNegrita" align="center">Detalle pagado - Monto total Bs.:<?=number_format($monto,2,',','.');?></div>
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
		<td class="normalNegroNegrita">C&oacute;digo</td>
		<td class="normalNegroNegrita">Monto Bs.</td>
		<td class="normalNegroNegrita">Partida</td>
	</tr>
<?php
$pagados = explode(",",$detallePagado);
$sumatoria=0;
for ($i=1;$i<count($pagados);$i++) {
?>
	<tr class="normal">
	<?php
	list($codigo,$monto) = split('[:]', $pagados[$i]);
	$sumatoria+=$monto;
	?>
		<td>
		<?php 
			if(strcmp(substr($codigo,0,4),"pgch")==0){
		?>
			<a href="javascript:abrir_ventana('../../documentos/pgch/pgch_detalle.php?codigo=<?= $codigo?>&amp;esta_id=10')" class="copyright"><?= $codigo?></a>
		<?
			}else if(strcmp(substr($codigo,0,4),"codi")==0){
		?>
			<a href="javascript:abrir_ventana('../../documentos/codi/codi_detalle.php?codigo=<?= $codigo?>&amp;esta_id=10')" class="copyright"><?= $codigo?></a>
		<?
			}else{
				echo $codigo;
			}
		?>
		</td>
		<td><?= number_format($monto,2,',','.')?></td>
		<td><?= $partida?></td>
	</tr>
	<?php 
}
pg_close($conexion);
?>
	<tr class="td_gray">
		<td class="normalNegroNegrita">Total Bs.:</td>
		<td colspan="2" class="normalNegroNegrita"><?= number_format($sumatoria,2,',','.')?></td>
	</tr>
</table>