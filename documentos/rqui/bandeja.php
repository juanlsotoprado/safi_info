<?php
ob_start();
session_start();
require("../../includes/constantes.php");
require_once("../../includes/funciones.php");
$login = $_SESSION['login'];
$user_perfil_id = $_SESSION['user_perfil_id'];
$user_depe_id = $_SESSION['user_depe_id'];
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
$estadoBorrador = "60";

$vistoBueno = "6";

$opcionDevolver = "5";

$queryDependencia = "";
$queryUsuario = "";
$queryCadenaPorDefecto =	"rebms_id = sdg.docg_id AND ".
							"sdg.wfca_id = swfc.wfca_id AND ".
							"swfc.wfgr_id = swfg.wfgr_id AND ".
							"swfc.wfca_id_hijo = swfch.wfca_id AND ".
							"swfch.wfgr_id = swfgh.wfgr_id ";
$fromCadena = ", sai_doc_genera sdg, sai_wfcadena swfc, sai_wfgrupo swfg, sai_wfcadena swfch, sai_wfgrupo swfgh ";
$queryCadenaDevueltas = "";
$queryCadenaPendientes = "";
$queryCadenaTransito = "";

if(substr($user_perfil_id, 0, 2)=="37" || substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46"){//ASISTENTE ADMINISTRATIVO, GERENTE O DIRECTOR
	/*$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
				"WHERE ".
					"(swfg.wfgr_perf = '".substr($user_perfil_id, 0, 2)."000' ".
					"OR swfg.wfgr_perf like '".substr($user_perfil_id, 0, 2)."000%' ".
					"OR swfg.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000' ".
					"OR swfg.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000%') ";
	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado, 0);
	$idGrupo = $row[0];*/
	
	$queryDependencia = "srbms.depe_id = '".$user_depe_id."' ";
	if (substr($user_perfil_id, 0, 2)=="37") {
		$queryUsuario = "srbms.usua_login = '".$login."' ";
	}
}else if($user_perfil_id == "38350" || $user_perfil_id == "68150" || $user_perfil_id == "47350" || $user_perfil_id == "65150"){//ASISTENTE EJECUTIVO, SECRETARIA PRESIDENCIA, DIRECTOR EJECUTIVO, PRESIDENTE
	/*$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
				"WHERE ".
					"(swfg.wfgr_perf = '".$user_perfil_id."' ".
					"OR swfg.wfgr_perf like '".$user_perfil_id."%' ".
					"OR swfg.wfgr_perf like '%/".$user_perfil_id."' ".
					"OR swfg.wfgr_perf like '%/".$user_perfil_id."%') ";		
	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado, 0);
	$idGrupo = $row[0];*/
	
	$queryDependencia = "srbms.depe_id = '".$user_depe_id."' ";
	if ($user_perfil_id == "38350" || $user_perfil_id == "68150") {
		$queryUsuario = "srbms.usua_login = '".$login."' ";	
	}
}else if($user_perfil_id == "30400"){//ANALISTA DE PRESUPUESTO
	/*$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
				"WHERE ".
					"swfg.wfgr_perf = '".$user_perfil_id."' ";
	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado, 0);
	$idGrupo = $row["wfgr_id"];*/
}else if($user_perfil_id == "15456" || $user_perfil_id == "42456"){//ANALISTA DE COMPRAS, COORDINADOR DE COMPRAS
	/*$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
				"WHERE ".
					"(swfg.wfgr_perf = '".$user_perfil_id."' ".
					"OR swfg.wfgr_perf like '".$user_perfil_id."%' ".
					"OR swfg.wfgr_perf like '%/".$user_perfil_id."' ".
					"OR swfg.wfgr_perf like '%/".$user_perfil_id."%') ";
	
	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado, 0);
	$idGrupo = $row["wfgr_id"];*/
}

//PARA LAS PENDIENTES
if(substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46"){//GERENTE O DIRECTOR
	$queryCadenaPendientes =	"(swfgh.wfgr_perf = '".substr($user_perfil_id, 0, 2)."000' ".
								"OR swfgh.wfgr_perf like '".substr($user_perfil_id, 0, 2)."000%' ".
								"OR swfgh.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000' ".
								"OR swfgh.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000%') ";
}
if($user_perfil_id == "47350" || $user_perfil_id == "65150"){//DIRECTOR EJECUTIVO, PRESIDENTE
	$queryCadenaPendientes =	"(swfgh.wfgr_perf = '".$user_perfil_id."' ".
								"OR swfgh.wfgr_perf like '".$user_perfil_id."%' ".
								"OR swfgh.wfgr_perf like '%/".$user_perfil_id."' ".
								"OR swfgh.wfgr_perf like '%/".$user_perfil_id."%') ";
}
if($user_perfil_id == "30400"){//ANALISTA DE PRESUPUESTO
	$queryCadenaPendientes = 	"swfgh.wfgr_perf = '".$user_perfil_id."' AND ".
								"swfch.wfop_id=".$vistoBueno." ";
}
if($user_perfil_id == "15456" || $user_perfil_id == "42456"){//ANALISTA DE COMPRAS
	/*$queryCadenaPendientes = 	"swfgh.wfgr_perf = '".$user_perfil_id."' ";*/
	$queryCadenaPendientes =	"(swfgh.wfgr_perf = '".$user_perfil_id."' ".
								"OR swfgh.wfgr_perf like '".$user_perfil_id."%' ".
								"OR swfgh.wfgr_perf like '%/".$user_perfil_id."' ".
								"OR swfgh.wfgr_perf like '%/".$user_perfil_id."%') ";			
}

//PARA LAS EN TRANSITO
if(substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46"){//GERENTE O DIRECTOR
	$queryCadenaTransito = 	"(swfgh.wfgr_perf = '30400' OR swfgh.wfgr_perf LIKE '%15456%' OR swfgh.wfgr_perf LIKE '%42456%') AND ".
							"swfch.wfop_id=".$vistoBueno." ";
}
if($user_perfil_id == "47350" || $user_perfil_id == "65150"){//DIRECTOR EJECUTIVO, PRESIDENTE
	$queryCadenaTransito = 	"(swfgh.wfgr_perf = '30400' OR swfgh.wfgr_perf LIKE '%15456%' OR swfgh.wfgr_perf LIKE '%42456%') AND ".
							"swfch.wfop_id=".$vistoBueno." ";
}
if($user_perfil_id == "30400"){//ANALISTA DE PRESUPUESTO
	$queryCadenaTransito = 	"(swfgh.wfgr_perf = '15456' OR swfgh.wfgr_perf = '42456') AND ".
							"swfch.wfop_id=".$vistoBueno." ";
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
		function verDetalle(idRequ){
			location.href = "detalleRequisicion.php?bandeja=true&idRequ="+idRequ;
		}

		function revisar(idRequ){
			<?php if($user_perfil_id=="15456" || $user_perfil_id=="42456"){?>
				location.href = "requisicionAnalistaCompras.php?bandeja=true&idRequ="+idRequ;
			<?php }else	if(substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46" || $user_perfil_id == "47350" || $user_perfil_id == "65150"){?>
				location.href = "requisicionGerenteDirector.php?bandeja=true&idRequ="+idRequ;
			<?php }else	if($user_perfil_id=="30400"){?>
				location.href = "requisicionAnalistaPresupuesto.php?bandeja=true&idRequ="+idRequ;
			<?php }?>
		}

		function modificar(idRequ){
			<?php if(substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150"){?>
			location.href = "modificarRequisicion.php?bandeja=true&idRequ="+idRequ;
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
if(substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150"){
	$queryDevueltas =	"SELECT ".
							"rebms_id, ".
							"to_char(rebms_fecha,'DD/MM/YYYY') as rebms_fecha_cadena, ".
							"rebms_fecha, ".
							"rebms_tipo, ".
							"imputacion_nombre ,".
							"srbms.esta_id, ".
							"esta_nombre, ".
							"depe_nombre ".
			 			"FROM ( ".
							"SELECT ".
								"srbms.rebms_id, ".
								"srbms.rebms_fecha, ".
								"srbms.rebms_tipo, ".
								"spae.centro_gestor || '/' || spae.centro_costo as imputacion_nombre, ".
								"srbms.esta_id, ".
								"se.esta_nombre, ".
								"sd.depe_nombre ".
							"FROM sai_req_bi_ma_ser srbms,sai_dependenci sd,sai_estado se, sai_proy_a_esp spae ".
							"WHERE ".
								(($queryDependencia!="")?$queryDependencia." AND ":"")." ".
								(($queryUsuario!="")?$queryUsuario." AND ":"").
								"srbms.esta_id = ".$estadoDevuelto." AND ".
								"srbms.depe_id = sd.depe_id AND ".
								"srbms.esta_id = se.esta_id AND ".
								"srbms.rebms_imp_p_c = spae.proy_id AND ".
								"srbms.rebms_imp_esp = spae.paes_id AND ".
								"srbms.pres_anno = ".$pres_anno." ".
							"UNION ".
							"SELECT ".
								"srbms.rebms_id, ".
								"srbms.rebms_fecha, ".
								"srbms.rebms_tipo, ".
								"sae.centro_gestor || '/' || sae.centro_costo as imputacion_nombre, ".
								"srbms.esta_id, ".
								"se.esta_nombre, ".
								"sd.depe_nombre ".
							"FROM sai_req_bi_ma_ser srbms,sai_dependenci sd,sai_estado se, sai_acce_esp sae ".
							"WHERE ".
								(($queryDependencia!="")?$queryDependencia." AND ":"")." ".
								(($queryUsuario!="")?$queryUsuario." AND ":"").
								"srbms.esta_id = ".$estadoDevuelto." AND ".
								"srbms.depe_id = sd.depe_id AND ".
								"srbms.esta_id = se.esta_id AND ".
								"srbms.rebms_imp_p_c = sae.acce_id AND ".
								"srbms.rebms_imp_esp = sae.aces_id AND ".
								"srbms.pres_anno = ".$pres_anno." ".
							") AS srbms ".$fromCadena." ".
						"WHERE ".$queryCadenaPorDefecto.(($queryCadenaDevueltas!="")?" AND ".$queryCadenaDevueltas:"")." ".
						"ORDER BY srbms.rebms_fecha DESC, srbms.rebms_id DESC ";
	$resultado = pg_exec($conexion, $queryDevueltas);
	$numeroFilas = pg_numrows($resultado);
?>
			<table>
				<tr>
					<td colspan="1" class="normal peq_verde_bold" style="text-align: center;">
						<p><?= $numeroFilas?> Requisiciones devueltas</p>
					</td>
				</tr>
<?php 
	if($numeroFilas>0){
		$columnas = 7;
?>
				<tr>
					<td>
						<table class="tablaalertas" background="../../imagenes/fondo_tabla.gif" >
							<tr class="td_gray normalNegroNegrita">
								<th><div style="margin-left: 20px;margin-right: 20px;">C&oacute;digo requisici&oacute;n</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Elaborada en fecha</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Tipo requisici&oacute;n</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Proy/Acc</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Dependencia</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Estado</div></th>
								<th style="width: 100px;"><div style="margin-left: 20px;margin-right: 20px;">&nbsp;</div></th>			
							</tr>
<?
		$modificar = "2";
		$vistoBueno = "6";
		for($ri = 0; $ri < $numeroFilas; $ri++) {
	    	$row = pg_fetch_array($resultado, $ri);
?>
		    				<tr class='resultados'>
				   				<td align='center'><?= $row["rebms_id"]?></td>
					   			<td align='center'><?= $row["rebms_fecha_cadena"]?></td>
						   		<td align='center'><?= ($row["rebms_tipo"]==TIPO_REQUISICION_COMPRA)?"Compra":(($row["rebms_tipo"]==TIPO_REQUISICION_SERVICIO)?"Servicio":"")?></td>
						   		<td align='center'><?= $row["imputacion_nombre"]?></td>
						   		<td align='center'><?= $row["depe_nombre"]?></td>
						    	<td align='center' style='color: red;'><?= $row["esta_nombre"]?></td>	
								<td align='center'>
									<a href='javascript: verDetalle("<?=$row["rebms_id"] ?>");' title='Ver detalle'>Ver detalle</a>
									<br/><a href='javascript: modificar("<?=$row["rebms_id"] ?>");' title='Modificar Requisici&oacute;n'>Modificar</a>
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
		echo "<tr><td>Actualmente no hay requisiciones devueltas</td></tr>";
	}
?>
			</table>
			<br/>
<?php
}
//PENDIENTES
$queryPendientes =	"SELECT ".
						"rebms_id, ".
						"to_char(rebms_fecha,'DD/MM/YYYY') as rebms_fecha_cadena, ".
						"rebms_fecha, ".
						"rebms_tipo, ".
						"imputacion_nombre ,".
						"srbms.esta_id, ".
						"esta_nombre, ".
						"depe_nombre ";
if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150"){
	$queryPendientes.= ", srbms.usua_login, srbms.solicitante ";
}
$queryPendientes .=	"FROM ( ".
						"SELECT ".
							"srbms.rebms_id, ".
							"srbms.rebms_fecha, ".
							"srbms.rebms_tipo, ".
							"spae.centro_gestor || '/' || spae.centro_costo as imputacion_nombre, ".
							"srbms.esta_id, ".
							"se.esta_nombre, ".
							"sd.depe_nombre ";
if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150"){
	$queryPendientes.= 		", srbms.usua_login ".
							", sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante ";
}
$queryPendientes .= 	"FROM sai_req_bi_ma_ser srbms,sai_dependenci sd,sai_estado se, sai_proy_a_esp spae ";
if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150"){
	$queryPendientes.= 			", sai_empleado sem ";
}
$queryPendientes .= 	"WHERE ";
if(substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150"){//ASISTENTE ADMINISTRATIVO
	$queryPendientes .=		"srbms.esta_id = ".$estadoBorrador." AND ";
}else if(substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46" || $user_perfil_id == "47350" || $user_perfil_id == "65150"){
	$queryPendientes .=		"srbms.esta_id = ".$estadoTransito." AND ";
}
$queryPendientes .= 		(($queryDependencia!="")?$queryDependencia." AND ":"")." ".
							(($queryUsuario!="")?$queryUsuario." AND ":"").
							"srbms.depe_id = sd.depe_id AND ".
							"srbms.esta_id = se.esta_id AND ";
if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150"){
	$queryPendientes.= 		"srbms.usua_login = sem.empl_cedula AND ";
}
$queryPendientes.= 			"srbms.rebms_imp_p_c = spae.proy_id AND ".
							"srbms.rebms_imp_esp = spae.paes_id AND ".
							"srbms.pres_anno = ".$pres_anno." ".
						"UNION ".
						"SELECT ".
							"srbms.rebms_id, ".
							"srbms.rebms_fecha, ".
							"srbms.rebms_tipo, ".
							"sae.centro_gestor || '/' || sae.centro_costo as imputacion_nombre, ".
							"srbms.esta_id, ".
							"se.esta_nombre, ".
							"sd.depe_nombre ";
if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150"){
	$queryPendientes.= 		", srbms.usua_login ".
							", sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante ";
}
$queryPendientes .= 	"FROM sai_req_bi_ma_ser srbms,sai_dependenci sd,sai_estado se, sai_acce_esp sae ";
if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150"){
	$queryPendientes.= 			", sai_empleado sem ";
}
$queryPendientes .= 	"WHERE ";
if(substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150"){//ASISTENTE ADMINISTRATIVO
	$queryPendientes .=		"srbms.esta_id = ".$estadoBorrador." AND ";
}else if(substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46" || $user_perfil_id == "47350" || $user_perfil_id == "65150"){
	$queryPendientes .=		"srbms.esta_id = ".$estadoTransito." AND ";
}
$queryPendientes .= 		(($queryDependencia!="")?$queryDependencia." AND ":"")." ".
							(($queryUsuario!="")?$queryUsuario." AND ":"").
							"srbms.depe_id = sd.depe_id AND ".
							"srbms.esta_id = se.esta_id AND ";
if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150"){
	$queryPendientes.= 		"srbms.usua_login = sem.empl_cedula AND ";
}
$queryPendientes.= 			"srbms.rebms_imp_p_c = sae.acce_id AND ".
							"srbms.rebms_imp_esp = sae.aces_id AND ".
							"srbms.pres_anno = ".$pres_anno." ".
						") AS srbms ".$fromCadena." ".
					"WHERE ".$queryCadenaPorDefecto.(($queryCadenaPendientes!="")?" AND ".$queryCadenaPendientes:"")." ".
					"ORDER BY srbms.rebms_fecha DESC, srbms.rebms_id DESC ";
$resultado = pg_exec($conexion, $queryPendientes);
$numeroFilas = pg_numrows($resultado);
?>
			<table>
				<tr>
					<td colspan="1" class="normal peq_verde_bold" style="text-align: center;">
						<p><?= $numeroFilas?> Requisiciones pendientes</p>
					</td>
				</tr>
<?php 
if($numeroFilas>0){
	$columnas = 7;
?>
				<tr>
					<td>
						<table class="tablaalertas" background="../../imagenes/fondo_tabla.gif" >
							<tr class="td_gray normalNegroNegrita">
								<th><div style="margin-left: 20px;margin-right: 20px;">C&oacute;digo Requisici&oacute;n</div></th>
<?php
	if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150") {
		$columnas++;
		echo "<th><div style='margin-left: 20px;margin-right: 20px;'>Elaborada por</div></th>";
	}
		?>					<th><div style="margin-left: 20px;margin-right: 20px;">Elaborada en Fecha</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Tipo Requisici&oacute;n</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Proy/Acc</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Dependencia</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Estado</div></th>
								<th style="width: 100px;"><div style="margin-left: 20px;margin-right: 20px;">&nbsp;</div></th>			
							</tr>
<?
	$modificar = "2";
	$vistoBueno = "6";
	for($ri = 0; $ri < $numeroFilas; $ri++) {
	    $row = pg_fetch_array($resultado, $ri);
?>
		    				<tr class='resultados'>
				   				<td align='center'><?= $row["rebms_id"]?></td>
<?php 		   						
		if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150"){
?>
								<td align='center'><?= $row["solicitante"]?></td>
<?php 
		}
?>	
					   			<td align='center'><?= $row["rebms_fecha_cadena"]?></td>
						   		<td align='center'><?= ($row["rebms_tipo"]==TIPO_REQUISICION_COMPRA)?"Compra":(($row["rebms_tipo"]==TIPO_REQUISICION_SERVICIO)?"Servicio":"")?></td>
						   		<td align='center'><?= $row["imputacion_nombre"]?></td>
						   		<td align='center'><?= $row["depe_nombre"]?></td>
						    	<td align='center'><?= $row["esta_nombre"]?></td>
								<td align='center'>
<?php 
		if(substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150") {
?>
									<a href='javascript: verDetalle("<?=$row["rebms_id"] ?>");' title='Ver detalle'>Ver detalle</a>
									<a href='javascript: modificar("<?=$row["rebms_id"] ?>");' title='Modificar Requisici&oacute;n'>Modificar</a>
<?php 
		}else{
?>
									<a href='javascript: revisar("<?=$row["rebms_id"] ?>");' title='Revisar'>Revisar</a>
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
	echo "<tr><td>Actualmente no hay requisiciones pendientes</td></tr>";
}
?>
			</table>
			<br/>
<?php
//TRANSITO
if($user_perfil_id != "15456" && $user_perfil_id != "42456"){
	$queryTransito =	"SELECT ".
							"rebms_id, ".
							"to_char(rebms_fecha,'DD/MM/YYYY') as rebms_fecha_cadena, ".
							"rebms_fecha, ".
							"rebms_tipo, ".
							"imputacion_nombre ,".
							"srbms.esta_id, ".
							"esta_nombre, ".
							"depe_nombre ";
	if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150"){
		$queryTransito.= ", srbms.usua_login, srbms.solicitante ";
	}
	$queryTransito .=	"FROM ( ".
							"SELECT ".
								"srbms.rebms_id, ".
								"srbms.rebms_fecha, ".
								"srbms.rebms_tipo, ".
								"spae.centro_gestor || '/' || spae.centro_costo as imputacion_nombre, ".
								"srbms.esta_id, ".
								"se.esta_nombre, ".
								"sd.depe_nombre ";
	if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150"){
		$queryTransito.= 		", srbms.usua_login ".
								", sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante ";
	}
	$queryTransito .= 		"FROM sai_req_bi_ma_ser srbms,sai_dependenci sd,sai_estado se, sai_proy_a_esp spae ";
	if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150"){
		$queryTransito.= 			", sai_empleado sem ";
	}
	$queryTransito .= 		"WHERE ";
	if(substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150"){//ASISTENTE ADMINISTRATIVO
		$queryTransito .=		"(srbms.esta_id = ".$estadoTransito." OR srbms.esta_id = ".$estadoAprobado.") AND ";
	}else if(substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46" || $user_perfil_id == "47350" || $user_perfil_id == "65150"){
		$queryTransito .=		"srbms.esta_id = ".$estadoAprobado." AND ";
	}else if($user_perfil_id == "30400" || $user_perfil_id == "15456" || $user_perfil_id == "42456"){
		$queryTransito .=		"srbms.esta_id = ".$estadoAprobado." AND ";
	}
	$queryTransito .= 			(($queryDependencia!="")?$queryDependencia." AND ":"")." ".
								(($queryUsuario!="")?$queryUsuario." AND ":"").
								"srbms.rebms_id NOT IN (SELECT rebms_id FROM sai_sol_coti) AND ".
								"srbms.depe_id = sd.depe_id AND ".
								"srbms.esta_id = se.esta_id AND ";
	if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150"){
		$queryTransito.= 		"srbms.usua_login = sem.empl_cedula AND ";
	}
	$queryTransito.= 			"srbms.rebms_imp_p_c = spae.proy_id AND ".
								"srbms.rebms_imp_esp = spae.paes_id AND ".
								"srbms.pres_anno = ".$pres_anno." ".
							"UNION ".
							"SELECT ".
								"srbms.rebms_id, ".
								"srbms.rebms_fecha, ".
								"srbms.rebms_tipo, ".
								"sae.centro_gestor || '/' || sae.centro_costo as imputacion_nombre, ".
								"srbms.esta_id, ".
								"se.esta_nombre, ".
								"sd.depe_nombre ";
	if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150"){
		$queryTransito.= 		", srbms.usua_login ".
								", sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante ";
	}
	$queryTransito .= 		"FROM sai_req_bi_ma_ser srbms,sai_dependenci sd,sai_estado se, sai_acce_esp sae ";
	if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150"){
		$queryTransito.= 			", sai_empleado sem ";
	}
	$queryTransito .= 		"WHERE ";
	if(substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150"){//ASISTENTE ADMINISTRATIVO
		$queryTransito .=		"(srbms.esta_id = ".$estadoTransito." OR srbms.esta_id = ".$estadoAprobado.") AND ";
	}else if(substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46" || $user_perfil_id == "47350" || $user_perfil_id == "65150"){
		$queryTransito .=		"srbms.esta_id = ".$estadoAprobado." AND ";
	}else if($user_perfil_id == "30400" || $user_perfil_id == "15456" || $user_perfil_id == "42456"){
		$queryTransito .=		"srbms.esta_id = ".$estadoAprobado." AND ";
	}
	$queryTransito .= 			(($queryDependencia!="")?$queryDependencia." AND ":"")." ".
								(($queryUsuario!="")?$queryUsuario." AND ":"").
								"srbms.rebms_id NOT IN (SELECT rebms_id FROM sai_sol_coti) AND ".
								"srbms.depe_id = sd.depe_id AND ".
								"srbms.esta_id = se.esta_id AND ";
	if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150"){
		$queryTransito.= 		"srbms.usua_login = sem.empl_cedula AND ";
	}
	$queryTransito.= 			"srbms.rebms_imp_p_c = sae.acce_id AND ".
								"srbms.rebms_imp_esp = sae.aces_id AND ".
								"srbms.pres_anno = ".$pres_anno." ".
							") AS srbms ".$fromCadena." ".
						"WHERE ".$queryCadenaPorDefecto.(($queryCadenaTransito!="")?" AND ".$queryCadenaTransito:"")." ".
						"ORDER BY srbms.rebms_fecha DESC, srbms.rebms_id DESC ";
	$resultado = pg_exec($conexion, $queryTransito);
	$numeroFilas = pg_numrows($resultado);
?>
			<table>
				<tr>
					<td colspan="1" class="normal peq_verde_bold" style="text-align: center;">
						<p><?= $numeroFilas?> Requisiciones en tr&aacute;nsito</p>
					</td>
				</tr>
<?php 
	if($numeroFilas>0){
		$columnas = 7;
?>
				<tr>
					<td>
						<table class="tablaalertas" background="../../imagenes/fondo_tabla.gif" >
							<tr class="td_gray normalNegroNegrita">
								<th><div style="margin-left: 20px;margin-right: 20px;">C&oacute;digo Requisici&oacute;n</div></th>
<?php
		if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150") {
			$columnas++;
			echo "<th><div style='margin-left: 20px;margin-right: 20px;'>Elaborada por</div></th>";
		}
?>					
								<th><div style="margin-left: 20px;margin-right: 20px;">Elaborada en Fecha</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Tipo Requisici&oacute;n</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Proy/Acc</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Dependencia</div></th>
								<th><div style="margin-left: 20px;margin-right: 20px;">Estado</div></th>
								<th style="width: 100px;"><div style="margin-left: 20px;margin-right: 20px;">&nbsp;</div></th>			
							</tr>
<?
		$modificar = "2";
		$vistoBueno = "6";
		for($ri = 0; $ri < $numeroFilas; $ri++) {
	    	$row = pg_fetch_array($resultado, $ri);
?>
		    				<tr class='resultados'>
				   				<td align='center'><?= $row["rebms_id"]?></td>
<?php 
			if(substr($user_perfil_id, 0, 2)!="37" && $user_perfil_id != "38350" && $user_perfil_id != "68150"){
?>
								<td align='center'><?= $row["solicitante"]?></td>
<?php 
			}
?>	
					   			<td align='center'><?= $row["rebms_fecha_cadena"]?></td>
						   		<td align='center'><?= ($row["rebms_tipo"]==TIPO_REQUISICION_COMPRA)?"Compra":(($row["rebms_tipo"]==TIPO_REQUISICION_SERVICIO)?"Servicio":"")?></td>
						   		<td align='center'><?= $row["imputacion_nombre"]?></td>
						   		<td align='center'><?= $row["depe_nombre"]?></td>
						    	<td align='center'><?= $row["esta_nombre"]?></td>
								<td align='center'><a href='javascript: verDetalle("<?=$row["rebms_id"] ?>");' title='Ver detalle'>Ver detalle</a></td>
					  		</tr>
<?php 
		}
?>
				  		</table>
					</td>
				</tr>
<?php
	}else{
		echo "<tr><td>Actualmente no hay requisiciones en tr&aacute;nsito</td></tr>";
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