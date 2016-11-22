<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ) {
	header('Location:../../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAfI:Disponibilidad Presupuestaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="javascript">
function imprimir(){
	window.print();
}
</script>
</head>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"/>
<body>
<div align="center" class="normalNegrita">DISPONIBILIDAD PRESUPUESTARIA</div>
<br/></br>
<table width="60%">
			<?php
			/*$anno_pres = $_SESSION['an_o_presupuesto'];*/
			$anno_pres = 2011;			
			$fechaInicio = "01/01/".$anno_pres;
			$fechaFin = date('d/m/Y');
			
			$query=	"SELECT ".
						"s.id_proyecto_accion, ".
						"s.id_accion_especifica, ".
						"s.nombre_proyecto_accion, ".
						"s.nombre_accion_especifica, ".
						"s.tipo, ".
						"sp.part_nombre, ".
						"s.centro_gestor, ".
						"s.centro_costo, ".
						"sf1125d.part_id, ".
						"COALESCE(SUM(sf1125d.fodt_monto),0) as monto_programado ".
					"FROM sai_forma_1125 sf1125, sai_fo1125_det sf1125d, sai_partida sp, ".
						"(".
							"(SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica, ".
								"sp.proy_titulo as nombre_proyecto_accion, ".
								"spae.paes_nombre as nombre_accion_especifica, ".
								"spae.centro_gestor, ".
								"spae.centro_costo, ".
								"cast(1 as bit) as tipo ".
							"FROM sai_proyecto sp, sai_proy_a_esp spae ".
							"WHERE ".
								"sp.esta_id <> 13 AND ".
								"sp.pre_anno = ".$anno_pres." AND ".
								"sp.proy_id = spae.proy_id AND ".
								"spae.pres_anno = ".$anno_pres." ".
							"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id) ".
							"UNION ".
							"(SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica, ".
								"sac.acce_denom as nombre_proyecto_accion, ".
								"sae.aces_nombre as nombre_accion_especifica, ".
								"sae.centro_gestor, ".
								"sae.centro_costo, ".
								"cast(0 as bit) as tipo ".
							"FROM sai_ac_central sac, sai_acce_esp sae ".
							"WHERE ".
								"sac.esta_id <> 13 AND ".
								"sac.pres_anno = ".$anno_pres." AND ".
								"sac.acce_id = sae.acce_id AND ".
								"sae.pres_anno = ".$anno_pres." ".
							"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id) ".
						") as s ".
					"WHERE ".
						"sf1125.pres_anno = ".$anno_pres." AND ".
						"sf1125.form_tipo = s.tipo AND ".
						"sf1125.form_id_p_ac = s.id_proyecto_accion AND ".
						"sf1125.form_id_aesp = s.id_accion_especifica AND ".
						"sf1125.form_id = sf1125d.form_id AND ".
						"sf1125.pres_anno = sf1125d.pres_anno AND ".
						"sf1125.esta_id <> 15 AND sf1125.esta_id <> 2 AND ".
						"sf1125d.fodt_mes BETWEEN 1 AND 12 AND ".
						"sf1125d.part_id NOT LIKE '4.11.0%' AND ".
						"sf1125d.part_id = sp.part_id AND ".
						"sf1125d.pres_anno = sp.pres_anno ".
					"GROUP BY s.id_proyecto_accion, s.id_accion_especifica, s.nombre_proyecto_accion, s.nombre_accion_especifica, s.tipo, sp.part_nombre, s.centro_gestor, s.centro_costo, sf1125d.part_id ".
					"ORDER BY s.tipo DESC, s.centro_gestor, s.centro_costo, s.id_proyecto_accion, s.id_accion_especifica, sf1125d.part_id";
			$resultadoMontosProgramados=pg_query($query) or die("Error en los montos programados");

			//MONTOS RECIBIDOS
			$query=	"SELECT ".
						"s.id_proyecto_accion, ".
						"s.id_accion_especifica, ".
						"s.tipo, ".
						"s.centro_gestor, ".
						"s.centro_costo, ".
						"sf0305d.part_id, ".
						"COALESCE(SUM(sf0305d.f0dt_monto),0) as monto_recibido ".
					"FROM sai_forma_0305 sf0305, sai_fo0305_det sf0305d, ".
						"(".
							"(SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica, ".
								"spae.centro_gestor, ".
								"spae.centro_costo, ".
								"cast(1 as bit) as tipo ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.pres_anno = ".$anno_pres." ".
							"ORDER BY spae.centro_gestor, spae.centro_costo, spae.proy_id, spae.paes_id) ".
							"UNION ".
							"(SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica, ".
								"sae.centro_gestor, ".
								"sae.centro_costo, ".
								"cast(0 as bit) as tipo ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.pres_anno = ".$anno_pres." ".
							"ORDER BY sae.centro_gestor, sae.centro_costo, sae.acce_id, sae.aces_id)".
						") as s ".
					"WHERE ".
						"sf0305.pres_anno = ".$anno_pres." AND ".
						"sf0305.esta_id <> 15 AND sf0305.esta_id <> 2 AND ".
						"sf0305.f030_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
						"sf0305.f030_id = sf0305d.f030_id AND ".
						"sf0305.pres_anno = sf0305d.pres_anno AND ".
						"sf0305d.f0dt_id_p_ac = s.id_proyecto_accion AND ".
						"sf0305d.f0dt_id_acesp = s.id_accion_especifica AND ".
						"sf0305d.f0dt_proy_ac = s.tipo AND ".
						"sf0305d.f0dt_tipo='1' AND ".
						"sf0305d.part_id NOT LIKE '4.11.0%' ".
					"GROUP BY s.id_proyecto_accion, s.id_accion_especifica, s.tipo, s.centro_gestor, s.centro_costo, sf0305d.part_id ".
					"ORDER BY s.tipo DESC, s.centro_gestor, s.centro_costo, s.id_proyecto_accion, s.id_accion_especifica, sf0305d.part_id";
			$resultadoMontosRecibidos=pg_query($query) or die("Error en los montos recibidos");
			
			//MONTOS CEDIDOS
			$query=	"SELECT ".
						"s.id_proyecto_accion, ".
						"s.id_accion_especifica, ".
						"s.tipo, ".
						"s.centro_gestor, ".
						"s.centro_costo, ".		
						"sf0305d.part_id, ".
						"COALESCE(SUM(sf0305d.f0dt_monto),0) as monto_cedido ".
					"FROM sai_forma_0305 sf0305, sai_fo0305_det sf0305d, ".
						"(".
							"(SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica, ".
								"spae.centro_gestor, ".
								"spae.centro_costo, ".
								"cast(1 as bit) as tipo ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.pres_anno = ".$anno_pres." ".
							"ORDER BY spae.centro_gestor, spae.centro_costo, spae.proy_id, spae.paes_id) ".
							"UNION ".
							"(SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica, ".
								"sae.centro_gestor, ".
								"sae.centro_costo, ".
								"cast(0 as bit) as tipo ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.pres_anno = ".$anno_pres." ".
							"ORDER BY sae.centro_gestor, sae.centro_costo, sae.acce_id, sae.aces_id)".
						") as s ".
					"WHERE ".
						"sf0305.pres_anno = ".$anno_pres." AND ".
						"sf0305.esta_id <> 15 AND sf0305.esta_id <> 2 AND ".
						"sf0305.f030_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
						"sf0305.f030_id = sf0305d.f030_id AND ".
						"sf0305.pres_anno = sf0305d.pres_anno AND ".
						"sf0305d.f0dt_id_p_ac = s.id_proyecto_accion AND ".
						"sf0305d.f0dt_id_acesp = s.id_accion_especifica AND ".
						"sf0305d.f0dt_proy_ac = s.tipo AND ".
						"sf0305d.f0dt_tipo='0' AND ".
						"sf0305d.part_id NOT LIKE '4.11.0%' ".
					"GROUP BY s.id_proyecto_accion, s.id_accion_especifica, s.tipo, s.centro_gestor, s.centro_costo, sf0305d.part_id ".
					"ORDER BY s.tipo DESC, s.centro_gestor, s.centro_costo, s.id_proyecto_accion, s.id_accion_especifica, sf0305d.part_id";
			$resultadoMontosCedidos=pg_query($query) or die("Error en los montos cedidos");
			
			//MONTOS DIFERIDOS
			$query=	"SELECT ".
						"se.id_proyecto_accion, ".
						"se.id_accion_especifica, ".
						"se.tipo, ".
						"se.centro_gestor, ".
						"se.centro_costo, ".	
						"scit.comp_sub_espe as part_id, ".
						"COALESCE(SUM(scit.comp_monto),0) as monto_diferido ".
					"FROM sai_comp_imputa_traza scit, ".
					"(".
						"SELECT scit.comp_id, MAX(scit.comp_fecha) as fecha ".
						"FROM sai_comp_traza sct, sai_comp_imputa_traza scit, ".
						"(".
							"(SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica, ".
								"spae.centro_gestor, ".
								"spae.centro_costo, ".
								"cast(1 as bit) as tipo ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.pres_anno = ".$anno_pres." ".
							"ORDER BY spae.centro_gestor, spae.centro_costo, spae.proy_id, spae.paes_id) ".
							"UNION ".
							"(SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica, ".
								"sae.centro_gestor, ".
								"sae.centro_costo, ".
								"cast(0 as bit) as tipo ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.pres_anno = ".$anno_pres." ".
							"ORDER BY sae.centro_gestor, sae.centro_costo, sae.acce_id, sae.aces_id) ".
						") as si ".
						"WHERE ".
							"scit.pres_anno = ".$anno_pres." AND ".
							"length(sct.pcta_id) > 4 AND ".
							"scit.comp_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
							"sct.esta_id <> 15 AND sct.esta_id <> 2 AND ".
							"sct.comp_id NOT IN ".
								"(SELECT comp_id ".
								"FROM sai_comp_traza ".
								"WHERE ".
									"(esta_id=15 OR esta_id=2) AND ".
									"comp_fecha2 < to_date('".$fechaFin."', 'DD/MM/YYYY')+1) AND ".
							"sct.comp_id = scit.comp_id AND ".
							"scit.comp_tipo_impu = si.tipo AND ".
							"scit.comp_acc_pp = si.id_proyecto_accion AND ".
							"scit.comp_acc_esp = si.id_accion_especifica ".
						"GROUP BY scit.comp_id ".
					") as s, ".
						"(".
							"(SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica, ".
								"spae.centro_gestor, ".
								"spae.centro_costo, ".
								"cast(1 as bit) as tipo ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.pres_anno = ".$anno_pres." ".
							"ORDER BY spae.centro_gestor, spae.centro_costo, spae.proy_id, spae.paes_id) ".
							"UNION ".
							"(SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica, ".
								"sae.centro_gestor, ".
								"sae.centro_costo, ".
								"cast(0 as bit) as tipo ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.pres_anno = ".$anno_pres." ".
							"ORDER BY sae.centro_gestor, sae.centro_costo, sae.acce_id, sae.aces_id) ".
						") as se ".
					"WHERE ".
						"scit.comp_id = s.comp_id AND ".
						"scit.comp_fecha = s.fecha AND ".
						"scit.comp_tipo_impu = se.tipo AND ".
						"scit.comp_acc_pp = se.id_proyecto_accion AND ".
						"scit.comp_acc_esp = se.id_accion_especifica AND ".
						"scit.comp_sub_espe NOT LIKE '4.11.0%' ".
					"GROUP BY se.id_proyecto_accion, se.id_accion_especifica, se.tipo, se.centro_gestor, se.centro_costo, scit.comp_sub_espe ".
					"ORDER BY se.tipo DESC, se.centro_gestor, se.centro_costo, se.id_proyecto_accion, se.id_accion_especifica, scit.comp_sub_espe";
			$resultadoMontosDiferidos=pg_query($query) or die("Error en los montos diferidos");
						
			//MONTOS COMPROMETIDOS AISLADOS
			$query=	"SELECT ".
						"se.id_proyecto_accion, ".
						"se.id_accion_especifica, ".
						"se.tipo, ".
						"se.centro_gestor, ".
						"se.centro_costo, ".
						"sci.comp_sub_espe as part_id, ".
						"COALESCE(SUM(sci.comp_monto),0) as monto_comprometido_aislado ".
					"FROM sai_comp_imputa sci, ".
					"(".
						//"SELECT sci.comp_id, MAX(sci.comp_fecha) as fecha ".
						"SELECT sci.comp_id ".
						"FROM sai_comp sc, sai_comp_imputa sci, ".
						"(".
							"(SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica, ".
								"cast(1 as bit) as tipo ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.pres_anno = ".$anno_pres." ".
							"ORDER BY spae.centro_gestor, spae.centro_costo, spae.proy_id, spae.paes_id) ".
							"UNION ".
							"(SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica, ".
								"cast(0 as bit) as tipo ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.pres_anno = ".$anno_pres." ".
							"ORDER BY sae.centro_gestor, sae.centro_costo, sae.acce_id, sae.aces_id) ".
						") as si ".
						"WHERE ".
							"sci.pres_anno = ".$anno_pres." AND ".
							"length(sc.pcta_id) < 4 AND ".
							//"sci.comp_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
							"sc.esta_id <> 15 AND sc.esta_id <> 2 AND ".
							/*"sc.comp_id NOT IN ".
								"(SELECT comp_id ".
								"FROM sai_comp ".
								"WHERE ".
									"(esta_id=15 OR esta_id=2) ) AND ".//AND ".
									//"comp_fecha2 < to_date('".$fechaFin."', 'DD/MM/YYYY')+1) AND ".*/
							"sc.comp_id = sci.comp_id AND ".
							"sci.comp_tipo_impu = si.tipo AND ".
							"sci.comp_acc_pp = si.id_proyecto_accion AND ".
							"sci.comp_acc_esp = si.id_accion_especifica ".
						"GROUP BY sci.comp_id ".
					") as s, ".
						"(".
							"(SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica, ".
								"spae.centro_gestor, ".
								"spae.centro_costo, ".
								"cast(1 as bit) as tipo ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.pres_anno = ".$anno_pres." ".
							"ORDER BY spae.centro_gestor, spae.centro_costo, spae.proy_id, spae.paes_id) ".
							"UNION ".
							"(SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica, ".
								"sae.centro_gestor, ".
								"sae.centro_costo, ".
								"cast(0 as bit) as tipo ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.pres_anno = ".$anno_pres." ".
							"ORDER BY sae.centro_gestor, sae.centro_costo, sae.acce_id, sae.aces_id) ".
						") as se ".
					"WHERE ".
						"sci.comp_id = s.comp_id AND ".
						//"sci.comp_fecha = s.fecha AND ".
						"sci.comp_tipo_impu = se.tipo AND ".
						"sci.comp_acc_pp = se.id_proyecto_accion AND ".
						"sci.comp_acc_esp = se.id_accion_especifica AND ".
						"sci.comp_sub_espe NOT LIKE '4.11.0%' ".
					"GROUP BY se.id_proyecto_accion, se.id_accion_especifica, se.tipo, se.centro_gestor, se.centro_costo, sci.comp_sub_espe ".
					"ORDER BY se.tipo DESC, se.centro_gestor, se.centro_costo, se.id_proyecto_accion, se.id_accion_especifica, sci.comp_sub_espe";
			
			$resultadoMontosComprometidosAislados=pg_query($query) or die("Error en los montos comprometidos aislados");

			$totalDisponible = 0;
			$totalPrimerOrdenDisponible = 0;
			$totalSegundoOrdenDisponible = 0;
			
			$programado = 0;
			$recibido = 0;
			$cedido = 0;
			$diferido = 0;
			$comprometidoAislado = 0;
			$montoAjustado = 0;
			$montoDisponible = 0;
			
			$accionEspecificaAnterior = "";
			$partidaAnteriorPrimerOrden = "";
			$partidaAnteriorSegundoOrden = "";
			
			$tamanoResultado = pg_num_rows($resultadoMontosProgramados);
			
			if($tamanoResultado){
				
			while($filaProgramados=pg_fetch_array($resultadoMontosProgramados)) {
				if($partidaAnteriorSegundoOrden==""){
					$partidaAnteriorSegundoOrden=substr($filaProgramados["part_id"], 0, 7).".00.00";
				}else if(	$partidaAnteriorSegundoOrden!=(substr($filaProgramados["part_id"], 0, 7).".00.00") ||
							$accionEspecificaAnterior!=$filaProgramados["id_accion_especifica"]){
				?>
					<tr class="normalNegroNegrita">
						<td><?= $partidaAnteriorSegundoOrden?></td>
						<td align="right"><?= number_format($totalSegundoOrdenDisponible,2,',','.')?></td>
					</tr>
				<?	
					$partidaAnteriorSegundoOrden=substr($filaProgramados["part_id"], 0, 7).".00.00";
					$totalSegundoOrdenDisponible = 0;
				}
				
				if($partidaAnteriorPrimerOrden==""){
					$partidaAnteriorPrimerOrden=substr($filaProgramados["part_id"], 0, 4).".00.00.00";
				}else if(	$partidaAnteriorPrimerOrden!=(substr($filaProgramados["part_id"], 0, 4).".00.00.00") ||
							$accionEspecificaAnterior!=$filaProgramados["id_accion_especifica"]){
				?>
					<tr class="normalNegroNegrita" style="color: #35519B;">
						<td><?= $partidaAnteriorPrimerOrden?></td>
						<td align="right"><?= number_format($totalPrimerOrdenDisponible,2,',','.')?></td>
					</tr>
				<?	
					$partidaAnteriorPrimerOrden=substr($filaProgramados["part_id"], 0, 4).".00.00.00";
					$totalPrimerOrdenDisponible = 0;
				}
				
				if($accionEspecificaAnterior!=$filaProgramados["id_accion_especifica"]){
					if($accionEspecificaAnterior!=""){
					?>
						<tr class="normalNegroNegrita" style="color: #35519B;">
							<td>Total Bs.</td>
							<td align="right"><?= number_format($totalDisponible,2,',','.')?></td>
						</tr>
					</table>
					<?php
						$totalDisponible = 0;
					}
					$accionEspecificaAnterior=$filaProgramados["id_accion_especifica"];
					$descripcionProyecto=$filaProgramados["nombre_proyecto_accion"]."(".$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"].")";
					?>
					<br/>
					<table width="90%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
						<tr class="td_gray">
							<td colspan="10" class="normalNegroNegrita"><?= $descripcionProyecto?></td>
						</tr>
						<tr class="td_gray">
							<td class="normalNegroNegrita">Partida</td>
							<td class="normalNegroNegrita">Disponible</td>
						</tr>
			<?php
				}
				
				$programado = $filaProgramados['monto_programado'];
				$recibido = 0;
				$cedido = 0;
				$diferido = 0;
				$comprometidoAislado = 0;
				
				if($filaRecibidos==null){
					$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
				}
				if(	$filaProgramados["part_id"]==$filaRecibidos["part_id"] &&
					$filaProgramados["id_accion_especifica"]==$filaRecibidos["id_accion_especifica"] &&
					$filaProgramados["tipo"]==$filaRecibidos["tipo"]){
					$recibido = $filaRecibidos["monto_recibido"];
					$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
				}else if($filaProgramados["id_accion_especifica"]==$filaRecibidos["id_accion_especifica"] &&
					$filaProgramados["tipo"]==$filaRecibidos["tipo"] &&
					$filaProgramados["part_id"]>$filaRecibidos["part_id"]){
					do{
						$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
					}while(	$filaProgramados["id_accion_especifica"]==$filaRecibidos["id_accion_especifica"] &&
							$filaProgramados["part_id"]>$filaRecibidos["part_id"]);
					if($filaProgramados["part_id"]==$filaRecibidos["part_id"]){
						$recibido = $filaRecibidos["monto_recibido"];
						$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);					
					}
				}else if($filaProgramados["id_accion_especifica"]>$filaRecibidos["id_accion_especifica"] &&
					$filaProgramados["tipo"]==$filaRecibidos["tipo"]){
					do{
						$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
					}while(	$filaRecibidos && $filaProgramados["id_accion_especifica"]>$filaRecibidos["id_accion_especifica"] &&
							$filaProgramados["tipo"]==$filaRecibidos["tipo"]);
					if(	$filaProgramados["id_accion_especifica"]==$filaRecibidos["id_accion_especifica"] &&
						$filaProgramados["tipo"]==$filaRecibidos["tipo"]){
						if($filaProgramados["part_id"]==$filaRecibidos["part_id"]){
							$recibido = $filaRecibidos["monto_recibido"];
							$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
						}else if($filaProgramados["part_id"]>$filaRecibidos["part_id"]){
							do{
								$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
							}while(	$filaRecibidos && $filaProgramados["id_accion_especifica"]==$filaRecibidos["id_accion_especifica"] &&
									$filaProgramados["tipo"]==$filaRecibidos["tipo"] &&
									$filaProgramados["part_id"]>$filaRecibidos["part_id"]);
							if($filaProgramados["part_id"]==$filaRecibidos["part_id"]){
								$recibido = $filaRecibidos["monto_recibido"];
								$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);					
							}
						}
					}
				}
				
				if($filaCedidos==null){
					$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
				}
				if($filaProgramados["part_id"]==$filaCedidos["part_id"] &&
					$filaProgramados["id_accion_especifica"]==$filaCedidos["id_accion_especifica"] &&
					$filaProgramados["tipo"]==$filaCedidos["tipo"]){				
					$cedido = $filaCedidos["monto_cedido"];
					$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
				}else if(	$filaProgramados["id_accion_especifica"]==$filaCedidos["id_accion_especifica"] &&
							$filaProgramados["tipo"]==$filaCedidos["tipo"] &&
							$filaProgramados["part_id"]>$filaCedidos["part_id"]){
					do{
						$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
					}while(	$filaProgramados["id_accion_especifica"]==$filaCedidos["id_accion_especifica"] &&
							$filaProgramados["tipo"]==$filaCedidos["tipo"] &&
							$filaProgramados["part_id"]>$filaCedidos["part_id"]);
					if($filaProgramados["part_id"]==$filaCedidos["part_id"]){
						$cedido = $filaCedidos["monto_cedido"];
						$filaCedidos=pg_fetch_array($resultadoMontosCedidos);					
					}
				}else if(	$filaProgramados["id_accion_especifica"]>$filaCedidos["id_accion_especifica"] &&
							$filaProgramados["tipo"]==$filaCedidos["tipo"]){
					do{
						$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
					}while(	$filaCedidos && $filaProgramados["id_accion_especifica"]>$filaCedidos["id_accion_especifica"] &&
							$filaProgramados["tipo"]==$filaCedidos["tipo"]);
					if(	$filaProgramados["id_accion_especifica"]==$filaCedidos["id_accion_especifica"] &&
						$filaProgramados["tipo"]==$filaCedidos["tipo"]){
						if($filaProgramados["part_id"]==$filaCedidos["part_id"]){
							$cedido = $filaCedidos["monto_cedido"];
							$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
						}else if($filaProgramados["part_id"]>$filaCedidos["part_id"]){
							do{
								$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
							}while(	$filaCedidos && $filaProgramados["id_accion_especifica"]==$filaCedidos["id_accion_especifica"] &&
									$filaProgramados["tipo"]==$filaCedidos["tipo"] &&
									$filaProgramados["part_id"]>$filaCedidos["part_id"]);
							if($filaProgramados["part_id"]==$filaCedidos["part_id"]){
								$cedido = $filaCedidos["monto_cedido"];
								$filaCedidos=pg_fetch_array($resultadoMontosCedidos);					
							}
						}
					}
				}
				
				$montoAjustado=($programado+$recibido)-$cedido;
				
				if($filaDiferidos==null){
					$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
				}
				if(	$filaProgramados["part_id"]==$filaDiferidos["part_id"] &&
					$filaProgramados["id_accion_especifica"]==$filaDiferidos["id_accion_especifica"] &&
					$filaProgramados["tipo"]==$filaDiferidos["tipo"]){
					$diferido = $filaDiferidos["monto_diferido"];
					$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
				}else if($filaProgramados["id_accion_especifica"]==$filaDiferidos["id_accion_especifica"] &&
					$filaProgramados["tipo"]==$filaDiferidos["tipo"] &&
					$filaProgramados["part_id"]>$filaDiferidos["part_id"]){
					do{
						$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
					}while(	$filaProgramados["id_accion_especifica"]==$filaDiferidos["id_accion_especifica"] &&
							$filaProgramados["tipo"]==$filaDiferidos["tipo"] &&
							$filaProgramados["part_id"]>$filaDiferidos["part_id"]);
					if($filaProgramados["part_id"]==$filaDiferidos["part_id"]){
						$diferido = $filaDiferidos["monto_diferido"];
						$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);					
					}
				}else if(	$filaProgramados["id_accion_especifica"]>$filaDiferidos["id_accion_especifica"] &&
							$filaProgramados["tipo"]==$filaDiferidos["tipo"]){
					do{
						$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
					}while(	$filaDiferidos && $filaProgramados["id_accion_especifica"]>$filaDiferidos["id_accion_especifica"] &&
							$filaProgramados["tipo"]==$filaDiferidos["tipo"]);
					if(	$filaProgramados["id_accion_especifica"]==$filaDiferidos["id_accion_especifica"] &&
						$filaProgramados["tipo"]==$filaDiferidos["tipo"]){
						if($filaProgramados["part_id"]==$filaDiferidos["part_id"]){
							$diferido = $filaDiferidos["monto_diferido"];
							$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
						}else if($filaProgramados["part_id"]>$filaDiferidos["part_id"]){
							do{
								$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
							}while(	$filaDiferidos && $filaProgramados["id_accion_especifica"]==$filaDiferidos["id_accion_especifica"] &&
									$filaProgramados["tipo"]==$filaDiferidos["tipo"] &&
									$filaProgramados["part_id"]>$filaDiferidos["part_id"]);
							if($filaProgramados["part_id"]==$filaDiferidos["part_id"]){
								$diferido = $filaDiferidos["monto_diferido"];
								$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);					
							}
						}
					}
				}
				
				if($filaComprometidosAislados==null){
					$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
				}
				if(	$filaProgramados["part_id"]==$filaComprometidosAislados["part_id"] &&
					$filaProgramados["id_accion_especifica"]==$filaComprometidosAislados["id_accion_especifica"] &&
					$filaProgramados["tipo"]==$filaComprometidosAislados["tipo"]){
					$comprometidoAislado = $filaComprometidosAislados["monto_comprometido_aislado"];
					$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
				}else if($filaProgramados["id_accion_especifica"]==$filaComprometidosAislados["id_accion_especifica"] &&
					$filaProgramados["tipo"]==$filaComprometidosAislados["tipo"] &&
					$filaProgramados["part_id"]>$filaComprometidosAislados["part_id"]){
					do{
						$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
					}while(	$filaProgramados["id_accion_especifica"]==$filaComprometidosAislados["id_accion_especifica"] &&
							$filaProgramados["tipo"]==$filaComprometidosAislados["tipo"] &&
							$filaProgramados["part_id"]>$filaComprometidosAislados["part_id"]);
					if($filaProgramados["part_id"]==$filaComprometidosAislados["part_id"]){
						$comprometidoAislado = $filaComprometidosAislados["monto_comprometido_aislado"];
						$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);					
					}
				}else if(	$filaProgramados["id_accion_especifica"]>$filaComprometidosAislados["id_accion_especifica"] &&
							$filaProgramados["tipo"]==$filaComprometidosAislados["tipo"]){
					do{
						$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
					}while(	$filaComprometidosAislados && $filaProgramados["id_accion_especifica"]>$filaComprometidosAislados["id_accion_especifica"] &&
							$filaProgramados["tipo"]==$filaComprometidosAislados["tipo"]);
					if(	$filaProgramados["id_accion_especifica"]==$filaComprometidosAislados["id_accion_especifica"] &&
						$filaProgramados["tipo"]==$filaComprometidosAislados["tipo"]){
						if($filaProgramados["part_id"]==$filaComprometidosAislados["part_id"]){
							$comprometidoAislado = $filaComprometidosAislados["monto_comprometido_aislado"];
							$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
						}else if($filaProgramados["part_id"]>$filaComprometidosAislados["part_id"]){
							do{
								$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
							}while(	$filaComprometidosAislados && $filaProgramados["id_accion_especifica"]==$filaComprometidosAislados["id_accion_especifica"] &&
									$filaProgramados["tipo"]==$filaComprometidosAislados["tipo"] &&
									$filaProgramados["part_id"]>$filaComprometidosAislados["part_id"]);
							if($filaProgramados["part_id"]==$filaComprometidosAislados["part_id"]){
								$comprometidoAislado = $filaComprometidosAislados["monto_comprometido_aislado"];
								$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);					
							}
						}
					}
				}
				
				if($diferido>0){
					$montoDisponible=($montoAjustado)-($diferido)-$comprometidoAislado;
				}else if($comprometidoAislado>0){
					$montoDisponible=($montoAjustado)-($comprometidoAislado);
				}else{
					$montoDisponible=($montoAjustado);
				}
				$totalDisponible += $montoDisponible;
				$totalPrimerOrdenDisponible += $montoDisponible;
				$totalSegundoOrdenDisponible += $montoDisponible;
			?>
				<tr class="normal">
					<td><?=trim($filaProgramados['part_id']." ".$filaProgramados['part_nombre']);?></td>
					<td align="right"><?= number_format($montoDisponible,2,',','.')?></td>
				</tr>
			<?
			}
			
			if($partidaAnteriorSegundoOrden!=""){
			?>
				<tr class="normalNegroNegrita">
					<td><?= $partidaAnteriorSegundoOrden?></td>
					<td align="right"><?= number_format($totalSegundoOrdenDisponible,2,',','.')?></td>
				</tr>
			<?	
			}
			
			if($partidaAnteriorPrimerOrden!=""){
			?>
				<tr class="normalNegroNegrita" style="color: #35519B;">
					<td><?= $partidaAnteriorPrimerOrden?></td>
					<td align="right"><?= number_format($totalPrimerOrdenDisponible,2,',','.')?></td>
				</tr>
			<?	
			}
		?>
				<tr class="normalNegroNegrita" style="color: #35519B;">
					<td>Total Bs.</td>
					<td align="right"><?= number_format($totalDisponible,2,',','.')?></td>
				</tr>
			</table>
		<?php 
		}else{
			?>
			<br/>
			<table width="90%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
				<tr class="normalNegroNegrita">
					<td colspan="10" align="center" height="40px" valign="middle">No se encontraron resultados</td>
				</tr>
			</table>
			<?php 
		}
		pg_close($conexion);
?>
<div align="center">
	<a href="javascript:imprimir();" class="normal">
		<img src="../../../imagenes/bot_imprimir.gif" width="23" height="20" border="0"/>
	</a>
</div>
</body>
</html>