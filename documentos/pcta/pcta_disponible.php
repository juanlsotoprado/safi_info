<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");
require_once("../../includes/perfiles/constantesPerfiles.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:../../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();
$dependencia = substr($_SESSION['user_depe_id'],0,2);
$dependenciaUsuario = $_SESSION['user_depe_id'];
$anno_pres=$_SESSION['an_o_presupuesto'];
$anno_pres=2014;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:Reporte</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script language="JavaScript" type="text/JavaScript">
function imprimir(){
	window.print();
}

function enviar(){
	documento = document.getElementById("txt_cod").value;
	if((documento=="pcta-") && (document.form.pcta_disponibles.value=='0'))
		{
		alert('Debe indicar el criterio de b\u00fasqueda');
		return;
	}
	document.form.submit();
}

</script>
</head>
<body class="normal">
<form name="form" method="post" action="pcta_disponible.php">
<br>
<table border="0" width="500" align="center" cellpadding="0" cellspacing="0" class="tablaalertas">
  <tr class="td_gray">
	<td colspan="2" class="normalNegroNegrita">DISPONIBILIDAD PRESUPUESTARIA DEL PUNTO DE CUENTA</td>
  </tr>
   <?php 
  	 if (   ($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO) || 
  	        ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO) || 
	 		($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_PRESUPUESTO)
	 		
	 		){?>
    <tr>
	<td width="175" height="29" class="normalNegrita" align="left">Aprobados hasta:</td>
	<td>
	<select name="pcta_disponibles" class="normalNegro"><option value="0">Seleccione</option>
	<option value="1">El d&iacute;a de hoy</option></select></td>
  </tr>
  <?php }?>
  <tr>
	<td class="normalNegrita" height="40" align="left">C&oacute;digo del documento:</td>
	<td ><input name="txt_cod" align="left" class="normalNegro" type="text" id="txt_cod" value="pcta-" size="18" /></td>
  </tr>
  <tr>
	<td colspan="2" valign="middle" height="50" align="center">
	 <input type="button" value="Buscar" onclick="enviar()" class="normalNegro"/>
	</td>
  </tr>
</table>
<br><div align="center">
<a href="../../acciones/pcta/pcta.php?accion=reporteDisponibleIntegrado">Por Proyecto/Acciones Centralizadas</a>
</div>
</form>



<?php
    $pcta=$_POST['txt_cod'];
    $num_filas=0;
	$sql_previo="select * from sai_pcuenta where pcta_id='".$_POST['txt_cod']."' and (pcta_asunto='013' or pcta_asunto='039')";
	$resultado_previo=pg_query($conexion,$sql_previo);
	if ($resultado_previo){
		if (pg_num_rows($resultado_previo) >0){
			$error=1;
		}
	}
	
if( (isset($pcta)) && ($_POST['txt_cod']!="pcta-") ){
	if ($error==0){
	
	$sql="select * from sai_pcta_imputa where pcta_id='".$_POST['txt_cod']."'";
	$resultado=pg_query($conexion,$sql);
	if ($row=pg_fetch_array($resultado)){
		$tipoImputacion=$row['pcta_tipo_impu'];
	}
	$cod_pcta=$_POST['txt_cod'];
	$longitud_pcta=strlen($cod_pcta);
//Consulta comp de cualquier año
$anno_pres="20".substr($cod_pcta,$longitud_pcta-2);//$_SESSION['an_o_presupuesto'];
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica, ".
							"spae.paes_nombre as nombre, ".
							"spae.centro_gestor as gestor, ".
							"spae.centro_costo as costo ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$row['pcta_acc_pp']."' AND ".
							"spae.paes_id = '".$row['pcta_acc_esp']."' AND ".
				   		    "spae.pres_anno = ".$anno_pres." ".
						"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
			}
			
		else {//accion centralizada
		
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica, ".
							"sae.aces_nombre as nombre, ".
							"sae.centro_gestor as gestor, ".
							"sae.centro_costo as costo ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$row['pcta_acc_pp']."' AND ".
						    "sae.aces_id = '".$row['pcta_acc_esp']."' AND ".
							"sae.pres_anno = ".$anno_pres." ".
						"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
		}
		$resultado = pg_exec($conexion, $query);
		if($row=pg_fetch_array($resultado)){
			$descripcionProyectoAccion=$row["nombre"];
			$idProyectoAccion=$row['id_proyecto_accion'];
			$idAccionEspecifica=$row['id_accion_especifica'];
			$centro_gestor=$row['gestor'];
			$centro_costo=$row['costo'];
		}
		$query_alcances="select distinct(pcta_id) as pcta from sai_disponibilidad_pcta where pcta_asociado='".$_POST['txt_cod']."'";
		$result_alcance=pg_exec($conexion,$query_alcances);
		$i=0;
		
	?>
<br/>
<table width="60%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr>
		<td class="normalNegrita">Punto de Cuenta:</td>
		<td class="normalNegro"><?= trim($pcta)?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Proyecto/Acci&oacute;n centralizada:</td>
		<td class="normalNegro"><?= trim($descripcionProyectoAccion)?></td>
	</tr>
	<tr>
		<td class="normalNegrita">A&ntilde;o Presupuesto:</td>
		<td class="normalNegro"><?= $anno_pres?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Centro Gestor - Centro Costo:</td>
		<td class="normalNegro"><?= $centro_gestor."-".$centro_costo?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Alcances al Punto de Cuenta:</td>
		<td class="normalNegro">
		<? while ($row_alcance=pg_fetch_array($result_alcance)){
			echo $row_alcance['pcta']." , ";
		} 
		?></td>
	</tr>
</table>
		<br/>
		<table width="90%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<?php
	    //MONTO APARTADO DEL PCTA MAS SUS ALCANCES
		$query=	
		"SELECT part_id,sum(monto_apartado) as monto_apartado ".
		"FROM ( ".
				"SELECT ".
					"spi.pcta_sub_espe as part_id, ".
					"SUM(spi.pcta_monto) as monto_apartado ".
				"FROM sai_pcta_imputa spi ".
 				"WHERE ".
					"spi.pcta_id ='".$pcta."' and ".
					"spi.pcta_id IN ".
					"(SELECT docg_id ".
					 "FROM sai_doc_genera sdg ".
					 "WHERE ".
					 "esta_id=13 AND ".
					 "sdg.docg_id = spi.pcta_id) ".
				"GROUP BY 1 ".
		"UNION ALL ".
				"SELECT ".
					"spi.pcta_sub_espe as part_id, ".
					"SUM(spi.pcta_monto) as monto_apartado ".
				"FROM sai_pcta_imputa spi, sai_pcuenta sp  ".
 				"WHERE ".
					"sp.pcta_id=spi.pcta_id and sp.pcta_asociado='".$pcta."' and ".
					"spi.pcta_id IN ".
					"(SELECT docg_id ".
					 "FROM sai_doc_genera sdg ".
					 "WHERE ".
					 "esta_id=13 AND ".
					 "sdg.docg_id = spi.pcta_id) ".
				"GROUP BY 1 ".
				"order by part_id ".
		") as aparta GROUP BY 1 order by part_id";
		
		$resultadoMontoApartado=pg_query($query) or die("Error en el monto apartado");

		$totalApartado = 0;
		$totalComprometidos = 0;
		$totalCausados = 0;
		$totalPagados = 0;
		$totalDisponible = 0;

		$totalPrimerOrdenApartado = 0;
		$totalPrimerOrdenComprometidos = 0;
		$totalPrimerOrdenCausados = 0;
		$totalPrimerOrdenPagados = 0;
		$totalPrimerOrdenDisponible = 0;

		$totalSegundoOrdenApartado = 0;
		$totalSegundoOrdenComprometidos = 0;
		$totalSegundoOrdenCausados = 0;
		$totalSegundoOrdenPagados = 0;
		$totalSegundoOrdenDisponible = 0;
	
		$programado = 0;
		$causado = 0;
		$pagado = 0;
		$montoAjustado = 0;
		$montoDisponible = 0;
	
		$partidaAnteriorPrimerOrden = "";
		$partidaAnteriorSegundoOrden = "";
	
		$tamanoResultado = pg_num_rows($resultadoMontoApartado);

		if($tamanoResultado>0){
		?>
		<tr class="td_gray">
			<td class="normalNegroNegrita">Partida</td>
			<td class="normalNegroNegrita">Apartado</td>
			<td class="normalNegroNegrita">Comprometido</td>
			<td class="normalNegroNegrita">Causado</td>
			<td class="normalNegroNegrita">Pagado</td>
			<td class="normalNegroNegrita">Disponible</td>
		</tr>
		<?php
			while($filaApartado=pg_fetch_array($resultadoMontoApartado)){
				
			$codigoPartida=$filaApartado['part_id'];
			
				//MONTOS COMPROMETIDOS
				$query=	"SELECT ".
					"scit.comp_sub_espe as part_id, ".
					"SUM(scit.comp_monto) as monto_comprometido ".
					"FROM sai_comp sct, sai_comp_imputa scit ".
					"WHERE ".
						"scit.pres_anno = ".$anno_pres." AND ".
						"sct.pcta_id ='".$pcta."' AND ".
						"sct.esta_id <> 15 AND sct.esta_id <> 2 AND ".
						"sct.comp_id = scit.comp_id  AND ".
						"scit.comp_sub_espe LIKE '".$codigoPartida."%' ".
				"GROUP BY scit.comp_sub_espe ".
				"ORDER BY 1";
				$resultadoMontosComprometidos=pg_query($query) or die("Error en los montos comprometidos");
		
			
		
				//MONTOS CAUSADOS
				$query_causado=	"SELECT ".
					"scd.part_id as part_id, ".
					"COALESCE(SUM(scd.cadt_monto),0) AS monto_causado ".
				"FROM sai_causado sc, sai_causad_det scd, sai_sol_pago ssp, sai_comp  scomp ".
				"WHERE ".
					"ssp.sopg_id=sc.caus_docu_id and scomp.pcta_id='".$pcta."' and ".
					"ssp.comp_id=scomp.comp_id and sc.pres_anno = ".$anno_pres." AND ".
					"sc.esta_id <> 15 AND sc.esta_id <> 2 AND ".
					"sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno AND ".
					"scd.part_id LIKE '".$codigoPartida."%' ".
				"GROUP BY scd.part_id ".
				"UNION ALL ".
				"SELECT scd.part_id as part_id, COALESCE(SUM(scd.cadt_monto),0) AS monto_causado ". 
				"FROM sai_causado sc, sai_causad_det scd, sai_codi scodi, sai_comp scomp ". 
				"WHERE scodi.comp_id=sc.caus_docu_id and scodi.nro_compromiso=scomp.comp_id ".
				"and scomp.pcta_id='".$pcta."' and sc.pres_anno = ".$anno_pres." AND ".
					"sc.esta_id <> 15 AND sc.esta_id <> 2 AND ".
					"sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno AND ".
				"scd.part_id LIKE '".$codigoPartida."%' ".
				"GROUP BY scd.part_id ". 
				"ORDER BY 1";
			//	echo $query_causado."<br><br><br>";
		$resultadoMontosCausados=pg_query($query_causado) or die("Error en los montos causados");
				
		//MONTOS PAGADOS
		//1ero los cheques
		$query=	"SELECT ".
					"spd.part_id as part_id, ".
					"COALESCE(SUM(spd.padt_monto),0) AS monto_pagado ".
				"FROM sai_pagado sp, sai_pagado_dt spd, sai_pago_cheque spc, sai_sol_pago ssp, sai_comp sc ".
				"WHERE ".
					"spc.pgch_id=sp.paga_docu_id and spc.docg_id=ssp.sopg_id and ssp.comp_id=sc.comp_id and ".
					"sc.pcta_id= '".$pcta."' and ".
					"sp.pres_anno = ".$anno_pres." AND ".
					"sp.esta_id <> 15 AND sp.esta_id <> 2 AND ".
					"sp.paga_id = spd.paga_id AND ".
					"sp.pres_anno = spd.pres_anno AND ".
					"spd.part_id LIKE '".$codigoPartida."%' ".
				"GROUP BY spd.part_id ".
				"UNION ALL ".
				"SELECT ".
					"spd.part_id as part_id, ".
					"COALESCE(SUM(spd.padt_monto),0) AS monto_pagado ".
				"FROM sai_pagado sp, sai_pagado_dt spd, sai_pago_transferencia spt, sai_sol_pago ssp, sai_comp sc ".
				"WHERE ".
					"spt.trans_id=sp.paga_docu_id and spt.docg_id=ssp.sopg_id and ssp.comp_id=sc.comp_id and ".
					"sc.pcta_id= '".$pcta."' and ".
					"sp.pres_anno = ".$anno_pres." AND ".
					"sp.esta_id <> 15 AND sp.esta_id <> 2 AND ".
					"sp.paga_id = spd.paga_id AND ".
					"sp.pres_anno = spd.pres_anno AND ".
					"spd.part_id LIKE '".$codigoPartida."%' ".
				"GROUP BY spd.part_id ".
				"UNION ALL ".
				"SELECT ".
					"spd.part_id as part_id, ".
					"COALESCE(SUM(spd.padt_monto),0) AS monto_pagado ".
				"FROM sai_pagado sp, sai_pagado_dt spd, sai_codi sco,sai_comp sc ".
				"WHERE ".
					"sco.comp_id=sp.paga_docu_id and sco.nro_compromiso=sc.comp_id and ".
					"sc.pcta_id= '".$pcta."' and ".
					"sp.pres_anno = ".$anno_pres." AND ".
					"sp.esta_id <> 15 AND sp.esta_id <> 2 AND ".
					"sp.paga_id = spd.paga_id AND ".
					"sp.pres_anno = spd.pres_anno AND ".
					"spd.part_id LIKE '".$codigoPartida."%' ".
				"GROUP BY spd.part_id ".
		
				"ORDER BY 1";

		$resultadoMontosPagados=pg_query($query) or die("Error en los montos pagados");
		
		
		
		
		
				if($partidaAnteriorSegundoOrden==""){
					$partidaAnteriorSegundoOrden=substr($filaApartado["part_id"], 0, 7).".00.00";
				}else if($partidaAnteriorSegundoOrden!=(substr($filaApartado["part_id"], 0, 7).".00.00")){
					?>
					<tr class="normalNegroNegrita">
						<td><?= $partidaAnteriorSegundoOrden?></td>
						<td align="right"><?= number_format($totalSegundoOrdenApartado,2,',','.')?></td>
						<td align="right"><?= number_format($totalSegundoOrdenComprometidos,2,',','.')?></td>
						<td align="right"><?= number_format($totalSegundoOrdenCausados,2,',','.')?></td>
						<td align="right"><?= number_format($totalSegundoOrdenPagados,2,',','.')?></td>
						<td align="right"><?= number_format($totalSegundoOrdenDisponible,2,',','.')?></td>
					</tr>
	<?
					$partidaAnteriorSegundoOrden=substr($filaApartado["part_id"], 0, 7).".00.00";
					$totalSegundoOrdenApartado = 0;
					$totalSegundoOrdenComprometidos = 0;
					
					$totalSegundoOrdenCausados = 0;
					$totalSegundoOrdenPagados = 0;
					$totalSegundoOrdenDisponible = 0;
				}
			
				if($partidaAnteriorPrimerOrden==""){
					$partidaAnteriorPrimerOrden=substr($filaApartado["part_id"], 0, 4).".00.00.00";
				}else if($partidaAnteriorPrimerOrden!=(substr($filaApartado["part_id"], 0, 4).".00.00.00")){
			?>
					<tr class="normalNegroNegrita" style="color: #35519B;">
						<td><?= $partidaAnteriorPrimerOrden?></td>
						<td align="right"><?= number_format($totalPrimerOrdenApartado,2,',','.')?></td>
						<td align="right"><?= number_format($totalPrimerOrdenComprometidos,2,',','.')?></td>
						<td align="right"><?= number_format($totalPrimerOrdenCausados,2,',','.')?></td>
						<td align="right"><?= number_format($totalPrimerOrdenPagados,2,',','.')?></td>
						<td align="right"><?= number_format($totalPrimerOrdenDisponible,2,',','.')?></td>
					</tr>
			<?
					$partidaAnteriorPrimerOrden=substr($filaApartado["part_id"], 0, 4).".00.00.00";
					$totalPrimerOrdenApartado = 0;
					$totalPrimerOrdenComprometidos = 0;
					$totalPrimerOrdenCausados = 0;
					$totalPrimerOrdenPagados = 0;
					$totalPrimerOrdenDisponible = 0;
				}
			
				$programado = $filaApartado['monto_apartado'];
				$recibido = 0;
				$comprometido = 0;
				$causado = 0;
				$pagado = 0;
			

				if($filaComprometidos==null){
					$filaComprometidos=pg_fetch_array($resultadoMontosComprometidos);
				}
				if($filaApartado["part_id"]==$filaComprometidos["part_id"]){
					$comprometido = $filaComprometidos["monto_comprometido"];
					$filaComprometidos=pg_fetch_array($resultadoMontosComprometidos);
				}
		
	
				if($filaCausados==null){
					$filaCausados=pg_fetch_array($resultadoMontosCausados);
				}
//echo "APART".$filaApartado["part_id"]."CAUS".$filaCausados["part_id"]."<BR><BR>";
	//			if($filaApartado["part_id"]==$filaCausados["part_id"]){
					$resultadoCausados=pg_query($query_causado) or die("Error en los montos causados");
					while($row_causado=pg_fetch_array($resultadoCausados)){
					 $causado = $causado+$row_causado["monto_causado"];
					}
		//		}
				
				if($filaPagados==null){
					$filaPagados=pg_fetch_array($resultadoMontosPagados);
				}
				
			
			//	if($filaApartado["part_id"]==$filaPagados["part_id"]){
					$resultadoPagados=pg_query($query) or die("Error en los montos pagados");
					while($row_pagado=pg_fetch_array($resultadoPagados)){
					$pagado = $pagado+$row_pagado["monto_pagado"];
					//$filaPagados=pg_fetch_array($resultadoMontosPagados);
					}
			//	}
					
			
				$montoDisponible=$programado-$comprometido;
				
				$totalProgramados += $programado;
				$totalApartado += $diferido;
				$totalComprometidos += $comprometido;
				$totalCausados += $causado;
				$totalPagados += $pagado;
				$totalDisponible += $montoDisponible;
					
				$totalPrimerOrdenProgramados += $programado;
				$totalPrimerOrdenApartado += $programado;
				$totalPrimerOrdenComprometidos += $comprometido;
				$totalPrimerOrdenCausados += $causado;
				$totalPrimerOrdenPagados += $pagado;
				$totalPrimerOrdenDisponible += $montoDisponible;
					
				$totalSegundoOrdenProgramados += $programado;
				
				$totalSegundoOrdenApartado += $programado;
				$totalSegundoOrdenComprometidos += $comprometido;
				$totalSegundoOrdenCausados += $causado;
				$totalSegundoOrdenPagados += $pagado;
				$totalSegundoOrdenDisponible += $montoDisponible;
			?>
			<tr class="normal">
				<td><?=trim($filaApartado['part_id']);?></td>
				<td align="right"><a target="_blank"
					href="../../reportes/presupuesto/detalleApartadoPcta.php?partida=<?=$filaApartado['part_id']?>&pcta=<?=$pcta?>&aopres=<?=$anno_pres?>&monto=<?=$programado?>&aesp="><?= number_format($programado,2,',','.')?></a>
				</td>
				<td align="right"><a target="_blank"
					href="../../reportes/presupuesto/detalleCompromisoPcta.php?partida=<?=$filaApartado['part_id']?>&pcta=<?=$pcta?>&aopres=<?=$anno_pres?>&monto=<?=$comprometido?>&aesp="><?= number_format($comprometido,2,',','.')?></a>
				</td>
				<td align="right"><a target="_blank"
					href="../../reportes/presupuesto/detalleCausadoPcta.php?partida=<?=$filaApartado['part_id']?>&pcta=<?=$pcta?>&aopres=<?=$anno_pres?>&monto=<?=$causado?>"><?= number_format($causado,2,',','.')?></a>
				</td>
				<td align="right"><a target="_blank"
					href="../../reportes/presupuesto/detallePagadoPcta.php?partida=<?=$filaApartado['part_id']?>&pcta=<?=$pcta?>&aopres=<?=$anno_pres?>&monto=<?=$pagado?>"><?= number_format($pagado,2,',','.')?></a>
			</td>
				<td align="right"><?= number_format($montoDisponible,2,',','.')?></td>
			</tr>
		<?
			}

			if($partidaAnteriorSegundoOrden!=""){
		?>
			<tr class="normalNegroNegrita">
				<td><?= $partidaAnteriorSegundoOrden?></td>
				<td align="right"><?= number_format($totalSegundoOrdenApartado,2,',','.')?></td>
				<td align="right"><?= number_format($totalSegundoOrdenComprometidos,2,',','.')?></td>
				<td align="right"><?= number_format($totalSegundoOrdenCausados,2,',','.')?></td>
				<td align="right"><?= number_format($totalSegundoOrdenPagados,2,',','.')?></td>
				<td align="right"><?= number_format($totalSegundoOrdenDisponible,2,',','.')?></td>
			</tr>
		<?
			}

			if($partidaAnteriorPrimerOrden!=""){
		?>
			<tr class="normalNegroNegrita" style="color: #35519B;">
				<td><?= $partidaAnteriorPrimerOrden?></td>
				<td align="right"><?= number_format($totalPrimerOrdenApartado,2,',','.')?></td>
				<td align="right"><?= number_format($totalPrimerOrdenComprometidos,2,',','.')?></td>
				<td align="right"><?= number_format($totalPrimerOrdenCausados,2,',','.')?></td>
				<td align="right"><?= number_format($totalPrimerOrdenPagados,2,',','.')?></td>
				<td align="right"><?= number_format($totalPrimerOrdenDisponible,2,',','.')?></td>
			</tr>
		<?	
			}
		?>
			<tr class="normalNegrita">
				<td>Total Bs.</td>
				<td align="right"><?= number_format($totalProgramados,2,',','.')?></td>
				<td align="right"><?= number_format($totalComprometidos,2,',','.')?></td>
				<td align="right"><?= number_format($totalCausados,2,',','.')?></td>
				<td align="right"><?= number_format($totalPagados,2,',','.')?></td>
				<td align="right"><?= number_format($totalDisponible,2,',','.')?></td>
			</tr>
	<?
		}else{
	?>
		<tr class="titular">
			<td colspan="10" align="center" height="40px" valign="middle">
				No se encontraron resultados,
		Verifique que el punto de cuenta este finalizado para consultar su disponibilidad
	
			</td>
		</tr>
	<?php 
		}
		?>
</table>
<?php 
}else{?>
<br><br>
<div align="center" >
	No se premite consultar un Alcance al Punto de Cuenta, se debe colocar el Punto de Cuenta Inicial, para saber su disponibilidad
</div>
		
<?php
	
	
}

}elseif ($_POST['pcta_disponibles']==1) {
	$ao_pcta="pcta-%".substr($anno_pres,2,2);
	
	$wheretipo1 = "and to_date(to_char(sc.comp_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini2."' and to_date(to_char(sc.comp_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fin2."' ";
	
	$query_pcta="select disponible,pcta,fecha from(
				 select sum(monto) as disponible,t3.pcta_asociado as pcta,to_char(t2.pcta_fecha,'DD/MM/YYYY') as fecha, 
				 cast(substr(t3.pcta_asociado,6) as integer)
		 		 from sai_doc_genera t1,sai_pcuenta t2,sai_disponibilidad_pcta t3
				 where docg_id=t2.pcta_id and wfob_id_ini=99 and t2.esta_id<>15 and docg_id like '".$ao_pcta."'
				 and t3.pcta_asociado=t1.docg_id and t3.pcta_asociado=t2.pcta_id 
				 group by t3.pcta_asociado,t2.pcta_fecha order by 4) as total where disponible<>0";
	$resultado_pcta=pg_query($conexion,$query_pcta);
	$tamanoResultado = pg_num_rows($resultado_pcta);
	
	if($tamanoResultado>0){
		?>
    <table width="60%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td class="normalNegroNegrita">N&deg; Pcta (Inicial)</td>
			<td class="normalNegroNegrita">Centro gestor</td>
			<td class="normalNegroNegrita">Centro de Costo</td>
			<td class="normalNegroNegrita">Monto Disponible</td>
			<td class="normalNegroNegrita">Fecha Apartado</td>
		</tr>
		<?php
	while ($row_pcta=pg_fetch_array($resultado_pcta)){
	
		
 	  $query_centros="select 
	  case pcta_tipo_impu when CAST(1 AS BIT) then (select centro_gestor from sai_proy_a_esp where pcta_acc_pp=proy_id and paes_id=pcta_acc_esp and pres_anno='".$anno_pres."') 
	  else (select centro_gestor from sai_acce_esp where pcta_acc_pp=acce_id and pcta_acc_esp=aces_id and pres_anno='".$anno_pres."') end as centro_gestor, 
	  case pcta_tipo_impu when CAST(1 AS BIT) then (select centro_costo from sai_proy_a_esp where pcta_acc_pp=proy_id and paes_id=pcta_acc_esp and pres_anno='".$anno_pres."') 
	  else (select centro_costo from sai_acce_esp where pcta_acc_pp=acce_id and pcta_acc_esp=aces_id and pres_anno='".$anno_pres."') end as centro_costo
	  from sai_pcta_imputa where pcta_id='".$row_pcta['pcta']."'";
 	  $resultado_centros=pg_query($conexion,$query_centros);
 	  if ($row_centros=pg_fetch_array($resultado_centros)){
 	  	$cg=$row_centros['centro_gestor'];
 	  	$cc=$row_centros['centro_costo'];
 	  }
		?>
			<tr class="normalNegroNegrita" style="color: #35519B;">
				<td><?=$row_pcta['pcta'];?></td>
				<td align="right"><?= $cg;?></td>
				<td align="right"><?= $cc;?></td>
				<td align="right"><?= number_format($row_pcta['disponible'],2,',','.')?></td>
				<td align="right"><?= $row_pcta['fecha'];?></td>
		<?php 
	}
	?></table><?php 
	}
	
}
pg_close($conexion);
?>
</body>
</html>