<?php 
ob_start();
session_start();
require("../../includes/constantes.php");
require_once("../../includes/funciones.php");
$login = $_SESSION['login'];
$user_perfil_id = $_SESSION['user_perfil_id'];
$pres_anno=$_SESSION['an_o_presupuesto'];
require("../../includes/conexion.php");
if	( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();
$estadoTransito = "10";
$estadoDevuelto = "7";
$estadoAprobado = "13";
$estadoAnulado = "15";

$vistoBueno = "6";

$opcionDevolver = "5";

$queryUsuario = "";

/*$queryCadenaPorDefecto =	"AND soc.ordc_id = sdg.docg_id AND ".
							"sdg.wfca_id = swfc.wfca_id AND ".
							"swfc.wfgr_id = swfg.wfgr_id AND ".
							"swfc.wfca_id_hijo = swfch.wfca_id AND ".
							"swfch.wfgr_id = swfgh.wfgr_id ";*/
/*$queryCadenaPorDefecto =	"AND soc.ordc_id = sdg.docg_id AND ".
							"sdg.wfca_id = swfc.wfca_id AND ".
							"swfc.wfca_id_hijo = swfch.wfca_id AND ".
							"swfch.wfgr_id = swfgh.wfgr_id ";*/
$queryCadenaPorDefecto =	"AND soc.ordc_id = sdg.docg_id ";
//$fromCadena = ", sai_doc_genera sdg, sai_wfcadena swfc, sai_wfcadena swfch, sai_wfgrupo swfg, sai_wfgrupo swfgh ";
//$fromCadena = ", sai_doc_genera sdg, sai_wfcadena swfc, sai_wfcadena swfch, sai_wfgrupo swfgh ";
$fromCadena = ", sai_doc_genera sdg ";
$queryCadena = "";
$queryCadenaPendientes = "";
$queryCadenaTransito = "";

$objetoRevisar = 4;
$opcionAprobar = 6;
$opcionFin = 99;
$opcionDevolver = 5;
$opcionAnular = 24;

if($user_perfil_id=="15456"){//ANALISTA DE COMPRAS
	/*$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
				"WHERE ".
					"swfg.wfgr_perf = '".$user_perfil_id."' ";
	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado, 0);
	$idGrupo = $row["wfgr_id"];*/
	$queryUsuario = "AND soc.usua_login = '".$login."' ";
}/*else if($user_perfil_id == "42456"){//COORDINADOR DE COMPRAS
	$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
				"WHERE ".
					"swfg.wfgr_perf = '".$user_perfil_id."' ";
	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado, 0);
	$idGrupo = $row["wfgr_id"];
}else if($user_perfil_id == "46450"){//DIRECTOR DE ADMIN Y FINANZAS
	$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
				"WHERE ".
					"swfg.wfgr_perf = '".$user_perfil_id."' ";
	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado, 0);
	$idGrupo = $row["wfgr_id"];
}else if($user_perfil_id == "30400"){//ANALISTA DE PRESUPUESTO
	$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
				"WHERE ".
					"swfg.wfgr_perf = '".$user_perfil_id."' ";
	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado, 0);
	$idGrupo = $row["wfgr_id"];
}else if($user_perfil_id == "46400"){//DIRECTOR DE PRESUPUESTO
	$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
				"WHERE ".
					"swfg.wfgr_perf = '".$user_perfil_id."' ";
	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado, 0);
	$idGrupo = $row["wfgr_id"];
}*/

//PARA LAS PENDIENTES
if($user_perfil_id == "42456"){//COORDINADOR DE COMPRAS
	/*$queryCadenaPendientes = 	"AND swfgh.wfgr_perf = '".$user_perfil_id."' AND ".
								"swfch.wfop_id=".$opcionAprobar." ";*/
	$queryCadenaPendientes = 	"AND sdg.perf_id_act = '".$user_perfil_id."' ";
}
if($user_perfil_id == "46450"){//DIRECTOR DE ADMIN Y FINANZAS
	/*$queryCadenaPendientes = 	"AND swfgh.wfgr_perf = '".$user_perfil_id."' AND ".
								"swfch.wfop_id=".$opcionAprobar." ";*/
	$queryCadenaPendientes = 	"AND sdg.perf_id_act = '".$user_perfil_id."' ";
}
if($user_perfil_id == "30400"){//ANALISTA DE PRESUPUESTO
	/*$queryCadenaPendientes = 	"AND swfgh.wfgr_perf = '".$user_perfil_id."' AND ".
								"swfch.wfop_id=".$opcionAprobar." ";*/
	$queryCadenaPendientes = 	"AND sdg.perf_id_act = '".$user_perfil_id."' ";
}
if($user_perfil_id == "46400"){//DIRECTOR DE PRESUPUESTO
	/*$queryCadenaPendientes = 	"AND swfgh.wfgr_perf = '".$user_perfil_id."' AND ".
								"swfch.wfop_id=".$opcionAprobar." ";*/
	$queryCadenaPendientes = 	"AND sdg.perf_id_act = '".$user_perfil_id."' ";
}

//PARA LAS EN TRANSITO
if($user_perfil_id == "15456"){//COORDINADOR DE COMPRAS
	/*$queryCadenaTransito = 	"AND (swfgh.wfgr_perf = '42456' OR swfgh.wfgr_perf = '46450' OR swfgh.wfgr_perf LIKE '30400' OR swfgh.wfgr_perf LIKE '46400') AND ".
							"swfch.wfop_id=".$vistoBueno." ";*/
	$queryCadenaTransito = 	"AND (sdg.perf_id_act = '42456' OR sdg.perf_id_act = '46450' OR sdg.perf_id_act = '30400' OR sdg.perf_id_act = '46400') AND ".
							"sdg.wfob_id_ini <> 99 ";
}
if($user_perfil_id == "42456"){//COORDINADOR DE COMPRAS
	/*$queryCadenaTransito = 	"AND (swfgh.wfgr_perf = '46450' OR swfgh.wfgr_perf LIKE '30400' OR swfgh.wfgr_perf LIKE '46400') AND ".
							"swfch.wfop_id=".$vistoBueno." ";*/
	$queryCadenaTransito = 	"AND (sdg.perf_id_act = '46450' OR sdg.perf_id_act = '30400' OR sdg.perf_id_act = '46400') AND ".
							"sdg.wfob_id_ini <> 99 ";
}
if($user_perfil_id == "46450"){//DIRECTOR OFICINA DE GESTIÓN ADMINISTRATIVA Y FINANCIERA
	/*$queryCadenaTransito = 	"AND (swfgh.wfgr_perf LIKE '30400' OR swfgh.wfgr_perf LIKE '46400') AND ".
							"swfch.wfop_id=".$vistoBueno." ";*/
	$queryCadenaTransito = 	"AND (sdg.perf_id_act = '30400' OR sdg.perf_id_act = '46400') AND ".
							"sdg.wfob_id_ini <> 99 ";
}
if($user_perfil_id == "30400"){//ANALISTA DE PRESUPUESTO
	/*$queryCadenaTransito = 	"AND swfgh.wfgr_perf LIKE '46400' AND ".
							"swfch.wfop_id=".$vistoBueno." ";*/
	$queryCadenaTransito = 	"AND sdg.perf_id_act = '46400' AND ".
							"sdg.wfob_id_ini <> 99 ";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>...:SAFI:Bandeja</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
	<script language="JavaScript" src="../../js/funciones.js"></script>
	<script>
		function verDetalle(idOrdc){
			<?php if($user_perfil_id=="30400" || $user_perfil_id=="46400"){?>
				location.href = "detallePresupuestoOrdenDeCompra.php?bandeja=true&idOrdc="+idOrdc;
			<?php }else{?>
				location.href = "detalleOrdenDeCompra.php?bandeja=true&idOrdc="+idOrdc;
			<?php }?>
		}

		function revisar(idOrdc){
			<?php if($user_perfil_id=="42456"){?>
				location.href = "coordinadorOrdenDeCompra.php?bandeja=true&idOrdc="+idOrdc;
			<?php }else	if($user_perfil_id=="46450"){?>
				location.href = "gerenteOrdenDeCompra.php?bandeja=true&idOrdc="+idOrdc;
			<?php }else	if($user_perfil_id=="30400"){?>
				location.href = "analistaPresupuestoOrdenDeCompra.php?bandeja=true&idOrdc="+idOrdc;
			<?php }else	if($user_perfil_id=="46400"){?>
				location.href = "directorPresupuestoOrdenDeCompra.php?bandeja=true&idOrdc="+idOrdc;
			<?php }?>
		}

		function modificar(idOrdc){
			<?php if($user_perfil_id=="15456"){?>
			location.href = "modificarOrdenDeCompra.php?bandeja=true&idOrdc="+idOrdc;
			<?php }?>
		}
	</script>
</head>
<body class="normal">
<table width="100%" align="center">
	<tr>
		<td align="center">
<?php
//DEVUELTAS
if($user_perfil_id == "15456"){
	$queryDevueltas= 	"SELECT ".
							"soc.ordc_id, ".
							"to_char(soc.fecha,'DD/MM/YYYY') as fecha, ".
							"soc.fecha as soc_fecha, ".
							"srbms.rebms_id, ".
							"srbms.rebms_tipo, ".
							"se.esta_id, ".
							"se.esta_nombre, ".
							"sd.depe_nombre, ".
							"sp.prov_nombre as nombre_proveedor ".
						"FROM ".
							"sai_req_bi_ma_ser srbms, ".
							"sai_dependenci sd,".
							"sai_estado se, ".
							"sai_orden_compra soc ".
							"INNER JOIN sai_proveedor_nuevo sp ON (LOWER(soc.rif_proveedor_seleccionado) LIKE LOWER(sp.prov_id_rif)) ".
							$fromCadena." ".
						"WHERE ".
							"soc.rebms_id = srbms.rebms_id AND ".
							"soc.esta_id = ".$estadoDevuelto." AND ".
							"srbms.depe_id = sd.depe_id AND ".
							"soc.esta_id = se.esta_id AND ".
							"srbms.pres_anno = ".$pres_anno." ".
							$queryCadenaPorDefecto.
							$queryUsuario.
						"GROUP BY soc.ordc_id, soc.fecha, srbms.rebms_id, srbms.rebms_tipo, se.esta_id, se.esta_nombre, sd.depe_nombre, sp.prov_nombre ".
						"ORDER BY soc_fecha DESC, soc.ordc_id DESC ";
	$resultado = pg_exec($conexion, $queryDevueltas);
	$numeroFilas = pg_numrows($resultado);
?>
			<table>
				<tr>
					<td colspan="1" class="normal peq_verde_bold" style="text-align: center;">
						<p>
						&Oacute;rdenes de compra / servicio	devueltas
						</p>
					</td>
				</tr>
<?php 
	if($numeroFilas>0){
?>
				<tr>
					<td>
						<table class="tablaalertas" background="../../imagenes/fondo_tabla.gif">
							<tr class="td_gray normalNegroNegrita">
								<th><div style="margin-left: 20px;margin-right: 20px;">C&oacute;digo orden<br/>compra / servicio</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">C&oacute;digo<br/>requisici&oacute;n</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Elaborada<br/>en fecha</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Proveedor seleccionado</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Tipo de<br/>requisici&oacute;n</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Dependencia</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Estado</div></th>
								<th style="width: 100px;"><div style="margin-left: 20px;margin-right: 20px;">Acci&oacute;n</div></th>
							</tr>
							<?
							for($ri = 0; $ri < $numeroFilas; $ri++) {
								$row = pg_fetch_array($resultado, $ri);
							?>
							    <tr class='resultados'>
							    	<td align='center'><?= $row["ordc_id"]?></td>
							    	<td align='center'><?= $row["rebms_id"]?></td>
							   		<td align='center'><?= $row["fecha"]?></td>
							   		<td align='center'><?= $row["nombre_proveedor"]?></td>
							   		<td align='center'><?= ($row["rebms_tipo"]==TIPO_REQUISICION_COMPRA)?"Compra":(($row["rebms_tipo"]==TIPO_REQUISICION_SERVICIO)?"Servicio":"")?></td>
							   		<td align='center'><?= $row["depe_nombre"]?></td>
							   		<td align='center' style='color: red;'><?= $row["esta_nombre"]?></td>
							   		<td align='center'>
										<a href='javascript: verDetalle("<?=$row["ordc_id"] ?>");' title='Ver detalle'>Ver detalle</a>
										<br/><a href='javascript: modificar("<?=$row["ordc_id"] ?>");' title='Modificar orden de compra / servicio'>Modificar</a>
									</td>
								</tr>
							<?php 
							}
							?>
						</table>
					</td>
				</tr>
<?php	
	}else{
		echo "<tr><td>Actualmente no hay &oacute;rdenes de compra / servicio devueltas</td></tr>";
	}
?>
			</table>
			<br/>
<?php 
}

//PENDIENTES
if($user_perfil_id!="15456") {
	$queryPendientes = 	"SELECT ".
							"soc.ordc_id, ".
							"to_char(soc.fecha,'DD/MM/YYYY') as fecha, ".
							"soc.fecha as soc_fecha, ".
							"srbms.rebms_id, ".
							"srbms.rebms_tipo, ".
							"se.esta_id, ".
							"se.esta_nombre, ".
							"sd.depe_nombre, ".
							"sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante, ".
							"sp.prov_nombre as nombre_proveedor ".
						"FROM ".
							"sai_req_bi_ma_ser srbms, ".
							"sai_dependenci sd,".
							"sai_estado se, ".
							"sai_orden_compra soc ".
							"INNER JOIN sai_proveedor_nuevo sp ON (LOWER(soc.rif_proveedor_seleccionado) LIKE LOWER(sp.prov_id_rif)), ".
							"sai_usuario su, sai_empleado sem ".
							$fromCadena." ".
						"WHERE ".
							"soc.rebms_id = srbms.rebms_id AND ".
							"soc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula AND ";
	if($user_perfil_id != "30400" && $user_perfil_id != "46400"){
		$queryPendientes .=	"soc.esta_id = ".$estadoTransito." AND ";
	}else{
		$queryPendientes .=	"(soc.esta_id = ".$estadoAprobado." OR soc.esta_id = ".$estadoDevuelto.") AND ";
	}
	$queryPendientes.= 		"srbms.depe_id = sd.depe_id AND ".
							"soc.esta_id = se.esta_id AND ".
							"srbms.pres_anno = ".$pres_anno." ".
							$queryCadenaPorDefecto.
							$queryCadenaPendientes.
						"GROUP BY soc.ordc_id, soc.fecha, srbms.rebms_id, srbms.rebms_tipo, se.esta_id, se.esta_nombre, sd.depe_nombre, solicitante, sp.prov_nombre ".
						"ORDER BY soc_fecha DESC, soc.ordc_id DESC ";
	$resultado = pg_exec($conexion, $queryPendientes);
	$numeroFilas = pg_numrows($resultado);
?>
			<table>
				<tr>
					<td colspan="1" class="normal peq_verde_bold" style="text-align: center;">
						<p>
						&Oacute;rdenes de compra / servicio	pendientes
						</p>
					</td>
				</tr>
<?php 
	if($numeroFilas>0){
?>
				<tr>
					<td>
						<table class="tablaalertas" background="../../imagenes/fondo_tabla.gif">
							<tr class="td_gray normalNegroNegrita">
								<th><div style="margin-left: 20px;margin-right: 20px;">C&oacute;digo orden<br/>compra / servicio</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">C&oacute;digo<br/>requisici&oacute;n</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Elaborada<br/>en fecha</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Proveedor seleccionado</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Tipo de<br/>requisici&oacute;n</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Dependencia</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Estado</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Elaborada por usuario</div></th>
								<th style="width: 100px;"><div style="margin-left: 20px;margin-right: 20px;">Acci&oacute;n</div></th>
							</tr>
							<?
							for($ri = 0; $ri < $numeroFilas; $ri++) {
								$row = pg_fetch_array($resultado, $ri);
							?>
							    <tr class='resultados'>
							    	<td align='center'><?= $row["ordc_id"]?></td>
							    	<td align='center'><?= $row["rebms_id"]?></td>
							   		<td align='center'><?= $row["fecha"]?></td>
							   		<td align='center'><?= $row["nombre_proveedor"]?></td>
							   		<td align='center'><?= ($row["rebms_tipo"]==TIPO_REQUISICION_COMPRA)?"Compra":(($row["rebms_tipo"]==TIPO_REQUISICION_SERVICIO)?"Servicio":"")?></td>
							   		<td align='center'><?= $row["depe_nombre"]?></td>
							   		<?php 
							   		if ( $row["esta_id"] == $estadoDevuelto ) {
							   		?>
							   			<td align='center' style="color: red;"><?= $row["esta_nombre"]?></td>
							   		<?php 
							   		} else {
							   		?>
							   			<td align='center'><?= $row["esta_nombre"]?></td>
							   		<?php 
							   		}
							   		?>
							   		
							   		<td align='center'><?= $row["solicitante"]?></td>
							   		<td align='center'>
							<?php 
								if($user_perfil_id=="15456") {
							?>
									<a href='javascript: verDetalle("<?=$row["ordc_id"] ?>");' title='Ver detalle'>Ver detalle</a>
									<a href='javascript: modificar("<?=$row["ordc_id"] ?>");' title='Modificar orde de compra / servicio'>Modificar</a>
							<?php 
								}else{
							?>
									<a href='javascript: revisar("<?=$row["ordc_id"] ?>");' title='Revisar'>Revisar</a>
							<?php 
								}
							?>
									</td>
								</tr>
							<?php 
							}
							?>
						</table>
					</td>
				</tr>
<?php	
	}else{
		echo "<tr><td>Actualmente no hay &oacute;rdenes de compra / servicio pendientes</td></tr>";
	}
?>
			</table>
			<br/>
<?php 
}

//EN TRANSITO
if( $user_perfil_id != "46400" ){
	$queryTransito = 	"SELECT ".
							"soc.ordc_id, ".
							"to_char(soc.fecha,'DD/MM/YYYY') as fecha, ".
							"soc.fecha as soc_fecha, ".
							"srbms.rebms_id, ".
							"srbms.rebms_tipo, ".
							"se.esta_id, ".
							"se.esta_nombre, ".
							"sd.depe_nombre, ";
	if ( $user_perfil_id != "15456" ) {
		$queryTransito .=	"sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante, ";
	}
	$queryTransito .=		"sp.prov_nombre as nombre_proveedor ".
						"FROM ".
							"sai_req_bi_ma_ser srbms, ".
							"sai_dependenci sd,".
							"sai_estado se, ".
							"sai_orden_compra soc ".
							"INNER JOIN sai_proveedor_nuevo sp ON (LOWER(soc.rif_proveedor_seleccionado) LIKE LOWER(sp.prov_id_rif)) ";
	if ( $user_perfil_id != "15456" ) {
		$queryTransito .=	", sai_usuario su, sai_empleado sem ";
	}
	$queryTransito .=		$fromCadena." ".
						"WHERE ".
							"soc.rebms_id = srbms.rebms_id AND ";
	if ( $user_perfil_id != "15456" ) {
		$queryTransito .=	"soc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula AND ";
	}
	if($user_perfil_id == "30400"){
		$queryTransito .=	"soc.esta_id = ".$estadoAprobado." AND ";
	}else{
		$queryTransito .=	"(soc.esta_id = ".$estadoTransito." OR soc.esta_id = ".$estadoAprobado.") AND ";
	}
	$queryTransito .=		"srbms.depe_id = sd.depe_id AND ".
							"soc.esta_id = se.esta_id AND ".
							"srbms.pres_anno = ".$pres_anno." ".
							$queryCadenaPorDefecto.
							$queryCadenaTransito.
							$queryUsuario.
						"GROUP BY soc.ordc_id, soc.fecha, srbms.rebms_id, srbms.rebms_tipo, se.esta_id, se.esta_nombre, sd.depe_nombre";
	if ( $user_perfil_id != "15456" ) {
		$queryTransito .=	", solicitante";
	}						
	$queryTransito .=		", sp.prov_nombre ".
						"ORDER BY soc_fecha DESC, soc.ordc_id DESC ";
	$resultado = pg_exec($conexion, $queryTransito);
	$numeroFilas = pg_numrows($resultado);
?>
			<table>
				<tr>
					<td colspan="1" class="normal peq_verde_bold" style="text-align: center;">
						<p>
						&Oacute;rdenes de compra / servicio	no finalizadas
						</p>
					</td>
				</tr>
<?php 
	if($numeroFilas>0){
?>
				<tr>
					<td>
						<table class="tablaalertas" background="../../imagenes/fondo_tabla.gif">
							<tr class="td_gray normalNegroNegrita">
								<th><div style="margin-left: 20px;margin-right: 20px;">C&oacute;digo orden<br/>compra / servicio</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">C&oacute;digo<br/>requisici&oacute;n</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Elaborada<br/>en fecha</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Proveedor seleccionado</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Tipo de<br/>requisici&oacute;n</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Dependencia</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Estado</div></th>
								<?php 
								if ( $user_perfil_id != "15456" ) {
								?>
									<th><div style="margin-left: 20px;margin-right: 20px;">Elaborada por usuario</div></th>
								<?php 
								}
								?>
								<th style="width: 100px;"><div style="margin-left: 20px;margin-right: 20px;">Acci&oacute;n</div></th>
							</tr>
							<?
							for($ri = 0; $ri < $numeroFilas; $ri++) {
								$row = pg_fetch_array($resultado, $ri);
							?>
							    <tr class='resultados'>
							    	<td align='center'><?= $row["ordc_id"]?></td>
							    	<td align='center'><?= $row["rebms_id"]?></td>
							   		<td align='center'><?= $row["fecha"]?></td>
							   		<td align='center'><?= $row["nombre_proveedor"]?></td>
							   		<td align='center'><?= ($row["rebms_tipo"]==TIPO_REQUISICION_COMPRA)?"Compra":(($row["rebms_tipo"]==TIPO_REQUISICION_SERVICIO)?"Servicio":"")?></td>
							   		<td align='center'><?= $row["depe_nombre"]?></td>
							   		<td align='center'><?= $row["esta_nombre"]?></td>
							   		<?php 
									if ( $user_perfil_id != "15456" ) {
									?>
										<td align='center'><?= $row["solicitante"]?></td>
									<?php 
									}
									?>
							   		<td align='center'>
										<a href='javascript: verDetalle("<?=$row["ordc_id"] ?>");' title='Ver detalle'>Ver detalle</a>
									</td>
								</tr>
							<?php 
							}
							?>
						</table>
					</td>
				</tr>
<?php	
	}else{
		echo "<tr><td>Actualmente no hay &oacute;rdenes de compra / servicio no finalizadas</td></tr>";
	}
?>
			</table>
			<br/>
<?php 
}
?>
		</td>
	</tr>
</table>
</body>
</html>
<?php pg_close($conexion); ?>