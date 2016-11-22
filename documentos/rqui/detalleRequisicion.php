<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");
if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();
require("../../includes/constantes.php");
$codigo = "";
if (isset($_GET['codigo']) && $_GET['codigo'] != "") {
	$codigo = $_GET['codigo'];
}
$idRequ = "";
if (isset($_GET['idRequ']) && $_GET['idRequ'] != "") {
	$idRequ = $_GET['idRequ'];
}
$tipoBusq = TIPO_BUSQUEDA_REQUISICIONES;	
if (isset($_GET['tipoBusq']) && $_GET['tipoBusq'] != "") {
	$tipoBusq = $_GET['tipoBusq'];
}
$tipoRequ = TIPO_REQUISICION_TODAS;
if (isset($_GET['tipoRequ']) && $_GET['tipoRequ'] != "") {
	$tipoRequ = $_GET['tipoRequ'];
}
$pagina = "1";
if (isset($_GET['pagina']) && $_GET['pagina'] != "") {
	$pagina = $_GET['pagina'];
}
$proyAcc = "";
if (isset($_GET['proyAcc']) && $_GET['proyAcc'] != "") {
	$proyAcc = $_GET['proyAcc'];
}
$radioProyAcc = "";
if (isset($_GET['radioProyAcc']) && $_GET['radioProyAcc'] != "") {
	$radioProyAcc = $_GET['radioProyAcc'];
}
$proyecto = "";
$accionCentralizada = "";
if($radioProyAcc=="proyecto"){
	if (isset($_GET['proyecto']) && $_GET['proyecto'] != "") {
		$proyecto = $_GET['proyecto'];
	}
}else if($radioProyAcc=="accionCentralizada"){
	if (isset($_GET['accionCentralizada']) && $_GET['accionCentralizada'] != "") {
		$accionCentralizada = $_GET['accionCentralizada'];
	}
}else{
	$proyAcc = "";
}
$dependencia = "";
if (isset($_GET['dependencia']) && $_GET['dependencia'] != "") {
	$dependencia = $_GET['dependencia'];
}
$user_perfil_id = $_SESSION['user_perfil_id'];
if($user_perfil_id == "15456" || $user_perfil_id == "42456"){
	$estado = ESTADO_REQUISICION_PENDIENTES;	
}else if(substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150"){
	$estado = ESTADO_REQUISICION_DEVUELTAS;
}else if(substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46" || $user_perfil_id == "47350" || $user_perfil_id == "65150"){
	$estado = ESTADO_REQUISICION_NO_REVISADAS;
}
if (isset($_GET['estado']) && $_GET['estado'] != "") {
	$estado = $_GET['estado'];
}
$controlFechas = "";
if (isset($_GET['controlFechas']) && $_GET['controlFechas'] != "") {
	$controlFechas = $_GET['controlFechas'];
}
$fechaInicio = "";
if (isset($_GET['fechaInicio']) && $_GET['fechaInicio'] != "") {
	$fechaInicio = $_GET['fechaInicio'];
}
$fechaFin = "";
if (isset($_GET['fechaFin']) && $_GET['fechaFin'] != "") {
	$fechaFin = $_GET['fechaFin'];
}
$accion = "";
if (isset($_GET['accion']) && $_GET['accion'] != "") {
	$accion = $_GET['accion'];
}
$estadia = "";
if (isset($_GET['estadia']) && $_GET['estadia'] != "") {
	$estadia = $_GET['estadia'];
}
$bandeja = "";
if (isset($_GET['bandeja']) && $_GET['bandeja'] != "") {
	$bandeja = $_GET['bandeja'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:. Detalle de Requisici&oacute;n</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css"/>
<script language="JavaScript" src="../../js/funciones.js"></script>
<script><!--
	function irARequisiciones(){
		codigo = '<?= $codigo?>';
		tipoBusq = '<?= $tipoBusq?>';
		tipoRequ = '<?= $tipoRequ?>';
		proyAcc = '<?= $proyAcc?>';
		radioProyAcc = '<?= $radioProyAcc?>';
		proyecto = '<?= $proyecto?>';
		accionCentralizada = '<?= $accionCentralizada?>';
		dependencia = '<?= $dependencia?>';		
		estado = '<?= $estado?>';
		controlFechas = '<?= $controlFechas?>';
		fechaInicio = '<?= $fechaInicio?>';
		fechaFin = '<?= $fechaFin?>';
		pagina = '<?= $pagina?>';
		location.href = "../rqui/busquedas.php?codigo="+codigo+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&estado="+estado+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&proyAcc="+proyAcc+"&radioProyAcc="+radioProyAcc+"&proyecto="+proyecto+"&accionCentralizada="+accionCentralizada+"&dependencia="+dependencia+"&tipoBusq="+tipoBusq;
	}
<?php
if($idRequ && $idRequ!=""){
?>
	function generarPdf1(){
		<?php
			if(substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150"){
		?>
		location.href = "requisicion_PDF.php?tipo=L&idRequ=<?= $idRequ?>";
		<?php
			}
		?>
	}

	function generarPdf2(){
		<?php
			if(substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150"){
		?>
		location.href = "requisicion_PDF.php?tipo=F&idRequ=<?= $idRequ?>";
		<?php
			}
		?>
	}
<?php
}
?>
--></script>
</head>
<body class="normal">
<?php
if($idRequ && $idRequ!=""){
	$query = 	"SELECT ".
					"srbms.rebms_tipo, ".
					"srbms.rebms_tipo_imputa, ".
					"srbms.rebms_imp_p_c, ".
					"srbms.rebms_imp_esp, ".
					"srbms.rebms_prov_sugerido1, ".
					"srbms.rebms_prov_sugerido2, ".
					"srbms.rebms_prov_sugerido3, ".
					"srbms.rebms_calidad_sugerida, ".
					"srbms.rebms_tiempo_entrega_sugerida, ".
					"srbms.rebms_garantia_sugerida, ".
					"srbms.rebms_observaciones, ".
					"srbms.observaciones_almacen, ".
					"se.esta_nombre, ".
					"se.esta_id, ".
					"sd.depe_nombre, ".
					"sd.depe_id, ".
					"srbms.pcta_id, ".
					"to_char(srbms.fecha,'DD/MM/YYYY') as fecha, ".
					"srbms.pcta_justificacion, ".
					"srbms.descripcion_general, ".
					"srbms.justificacion, ".
					"sda.depe_nombre as gerencia_adscripcion ".
				"FROM sai_req_bi_ma_ser srbms, sai_estado se, sai_dependenci sd, sai_dependenci sda ".
				"WHERE ".
				"srbms.rebms_id = '".$idRequ."' AND ".
				"srbms.esta_id = se.esta_id AND ".
				"srbms.depe_id = sd.depe_id AND ".
				"srbms.gerencia_adscripcion = sda.depe_id ";
	
	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado, 0);
	$rebms_tipo = $row["rebms_tipo"];
	$rebms_tipo_imputa = $row["rebms_tipo_imputa"];
	$rebms_imp_p_c = $row["rebms_imp_p_c"];
	$rebms_imp_esp = $row["rebms_imp_esp"];
	$rebms_prov_sugerido1 = $row["rebms_prov_sugerido1"];
	$rebms_prov_sugerido2 = $row["rebms_prov_sugerido2"];
	$rebms_prov_sugerido3 = $row["rebms_prov_sugerido3"];
	$rebms_calidad_sugerida = $row["rebms_calidad_sugerida"];
	$rebms_tiempo_entrega_sugerida = $row["rebms_tiempo_entrega_sugerida"];
	$rebms_garantia_sugerida = $row["rebms_garantia_sugerida"];
	$rebms_observaciones = $row["rebms_observaciones"];
	$rebms_observaciones_almacen = $row["observaciones_almacen"];
	$esta_nombre = $row["esta_nombre"];
	$esta_id = $row["esta_id"];
	$depe_id = $row["depe_id"];
	$depe_rqui = $depe_id;
	$depe_nombre_req = $row["depe_nombre"];
	$pcta_id = $row["pcta_id"];
	$fecha = $row["fecha"];
	$pcta_justificacion = $row["pcta_justificacion"];
	$descripcion_general = $row["descripcion_general"];
	$justificacion = $row["justificacion"];
	$gerencia_adscripcion = $row["gerencia_adscripcion"];
		
	if($rebms_tipo_imputa==TIPO_IMPUTACION_PROYECTO){//Proyecto
		$query = "SELECT proy_titulo FROM sai_proyecto WHERE proy_id = '".$rebms_imp_p_c."'";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$proy_titulo = $row["proy_titulo"];
		$query = "SELECT paes_nombre,centro_gestor,centro_costo FROM sai_proy_a_esp WHERE proy_id = '".$rebms_imp_p_c."' AND paes_id = '".$rebms_imp_esp."'";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$aces_nombre = $row["paes_nombre"]."(".$row["centro_gestor"]."-".$row["centro_costo"].")";
	}else if($rebms_tipo_imputa==TIPO_IMPUTACION_ACCION_CENTRALIZADA){//Accion centralizada
		$query = "SELECT aces_nombre,centro_gestor,centro_costo FROM sai_acce_esp WHERE acce_id = '".$rebms_imp_p_c."' AND aces_id = '".$rebms_imp_esp."'";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$aces_nombre = $row["aces_nombre"]."(".$row["centro_gestor"]."-".$row["centro_costo"].")";
		$proy_titulo = $rebms_imp_p_c."-".$row["aces_nombre"];
	}
	
	if($accion!="generar" && $accion!="generar_borrador" && $estadia!="true" && $bandeja!="true"){
?>
<p align="center">
	<a href='javascript: irARequisiciones();'>Volver a los resultados de la b&uacute;squeda</a>
</p>
<?php
	}//else{ echo "<br/>";}
if($esta_id==7 || $esta_id==15){
	$query = 	"SELECT ".
					"sm.memo_id, ".
					"to_char(sm.memo_fecha_crea,'DD/MM/YYYY') as fecha, ".
					"sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante, ".
					"sm.depe_id, ".
					"sm.memo_asunto, ".
					"sm.memo_contenido ".
				"FROM ".
					"sai_docu_sopor sds, sai_memo sm, sai_usuario su, sai_empleado sem ".
				"WHERE ".
					"sds.doso_doc_fuente = '".$idRequ."' AND ".
					"sds.doso_doc_soport = sm.memo_id AND ".
					"sm.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula ".
				"ORDER BY sm.memo_fecha_crea DESC LIMIT 1";
	$resultado = pg_exec($conexion, $query);
	$memos = pg_numrows($resultado);
	if($memos>0){
		$row = pg_fetch_array($resultado, 0);
		$sql=	"SELECT swfg.wfgr_descrip, swfg.wfgr_perf ".
				"FROM sai_doc_genera sdg, sai_wfcadena swfc, sai_wfgrupo swfg ".
				"WHERE ".
					"sdg.docg_id = '".$idRequ."' AND ".
					"sdg.wfca_id = swfc.wfca_id AND ".
					"swfc.wfgr_id = swfg.wfgr_id";
		$resultadoInstancia = pg_exec($conexion,$sql);
		$rowInstancia = pg_fetch_array($resultadoInstancia,0);
		$wfgr_descrip=trim($rowInstancia["wfgr_descrip"]);
		$wfgr_perf=trim($rowInstancia["wfgr_perf"]);
		$pos = strpos($wfgr_perf, '15456');
		if($pos !== false){//Analista de Compras
			$depeIdInstancia = '456';
		}else{
			$pos = strpos($wfgr_perf, '42456');
			if($pos !== false){//Coordinador de Compras
				$depeIdInstancia = '456';
			}else{	
				$depeIdInstancia = $row["depe_id"];
			}
		}
		$query = 	"SELECT depe_nombre FROM sai_dependenci ".
					"WHERE ".
						"depe_id = '".$depeIdInstancia."'";
		$resultadoInstancia = pg_exec($conexion, $query);
		$rowInstancia = pg_fetch_array($resultadoInstancia, 0);
		$depe_nombre = $rowInstancia["depe_nombre"];
?>
<table width="900px" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray">
		<td colspan="2" class="normalNegroNegrita">
			&Uacute;ltimo Memo: <?= $row["memo_id"]?>
		</td>
	</tr>
	<tr>
		<td width="80px;">
			Fecha:
		</td>
		<td class="normalNegro">
			<?= $row["fecha"]?>
		</td>
	</tr>
	<tr>
		<td>
			De:
		</td>
		<td class="normalNegro">
			<?= $row["solicitante"]?>
		</td>
	</tr>
	<tr>
		<td>
			Instancia:
		</td>
		<td class="normalNegro">
			<?= $wfgr_descrip." en ".$depe_nombre?>
		</td>
	</tr>
	<tr>
		<td>
			Asunto:
		</td>
		<td class="normalNegro">
			<?= $row["memo_asunto"]?>
		</td>
	</tr>
	<tr>
		<td>
			Contenido:
		</td>
		<td class="normalNegro">
			<?= $row["memo_contenido"]?>
		</td>
	</tr>
</table>
<br/>
<?php
	}
}
?>
<form name="form" method="post" action="" id="form">
	<table align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td colspan="2" class="normalNegroNegrita">
				Detalle de Requisici&oacute;n: <?= $idRequ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="tablaalertas" width="900px">
					<tr>
						<td width="20%">
							Procedente de:
						</td>
						<td width="80%" class="normalNegro">
							<?= $depe_nombre_req?>
						</td>
					</tr>
					<tr>
						<td>
							Tipo de requisici&oacute;n:
						</td>
						<td class="normalNegro">
							<?php
								if($rebms_tipo == TIPO_REQUISICION_COMPRA){ echo "Compra";}
								else if($rebms_tipo == TIPO_REQUISICION_SERVICIO){ echo "Servicio";}
							?>
						</td>
					</tr>
					<tr>
						<td>
							Fecha:
						</td>
						<td class="normalNegro">
							<?= $fecha?>
						</td>
					</tr>
					<tr>
						<td>
							Estado:
						</td>
						<td class="normalNegro">
							<?= $esta_nombre?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="tablaalertas" width="900px">
					<tr class="td_gray normalNegrita">
						<td align="center">
							Categor&iacute;a
						</td>
						<td align="center">
							C&oacute;digo
						</td>
						<td align="center">
							Denominaci&oacute;n
						</td>
					</tr>
					<tr class="normalNegro">
						<td align="left">
							<?php
								if($rebms_tipo_imputa==TIPO_IMPUTACION_PROYECTO){echo "Proyecto";}
								else if($rebms_tipo_imputa==TIPO_IMPUTACION_ACCION_CENTRALIZADA){echo "Acci&oacute;n Cent.";}
							?>
						</td>
						<td align="left">
							<?= $rebms_imp_p_c?>
						</td>
						<td align="left">
							<?= $proy_titulo?>
						</td>
					</tr>
					<tr class="normalNegro">
						<td align="left">
							Acci&oacute;n Espec&iacute;fica
						</td>
						<td align="left">
							<?= $rebms_imp_esp?>
						</td>
						<td align="left">
							<?= $aces_nombre?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="tablaalertas" width="900px">
					<tr>
						<td width="20%">
							Unidad de adscripci&oacute;n
						</td>
						<td width="80%" align="left" class="normalNegro">
							<?= $gerencia_adscripcion?>
						</td>
					</tr>
					<tr>
						<td>
							Descripci&oacute;n del producto o servicio solicitado
						</td>
						<td class="normalNegro">
							<?= $descripcion_general?>
						</td>
					</tr>
					<tr>
						<td>
							Justificaci&oacute;n:
						</td>
						<td class="normalNegro">
							<?= $justificacion?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="tablaalertas" width="900px">
					<tr>
						<td width="20%">
							Punto de cuenta:
						</td>
						<td width="80%" class="normalNegro">
							<?= (($pcta_id!="")?$pcta_id:"no aplica")?>
						</td>
					</tr>
					<tr>
						<td>
							Justificaci&oacute;n:
						</td>
						<td class="normalNegro">
							<?= $pcta_justificacion?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table width="900px" class="tablaalertas" id="tbl_mod">
					<tr class="td_gray normalNegrita">
						<td align="center" width="10%">C&oacute;digo</td>
						<td align="center" width="20%">Nombre</td>
						<td align="center" width="10%">Partida</td>
						<td align="center" width="20%">Denominaci&oacute;n</td>
						<td align="center" width="30%">Especificaciones</td>
						<td align="center" width="10%">Cantidad</td>
					</tr>
					<tbody id="item">
					<?php
						$query = 	"SELECT DISTINCT ".
										"si.id, ".
										"si.nombre, ".
										"sri.rbms_item_cantidad as cantidad, ".
										"sri.rbms_item_desc as descripcion, ".
										"sp.part_id as id_partida, ".
										"sp.part_nombre as nombre_partida ".
									"FROM ".
										"sai_rqui_items sri, sai_item si, sai_item_partida sip, sai_partida sp ".
									"WHERE ".
										"sri.rebms_id = '".$idRequ."' AND ".
										"sri.rbms_item_arti_id = si.id AND ".
										"sri.rbms_item_arti_id = sip.id_item AND ".
										"sip.part_id = sp.part_id AND ".
										"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ";
						
						$resultado = pg_exec($conexion, $query);
						$elementos = pg_numrows($resultado);
						for($i=0;$i<$elementos;$i++){
							$row = pg_fetch_array($resultado, $i);
					?>
					<tr>
						<td valign="top" align="center" class="normalNegro">
							<?=$row["id"]?>
						</td>
						<td valign="top" align="left" class="normalNegro">
							<?=$row["nombre"]?>
						</td>
						<td valign="top" align="center" class="normalNegro">
							<?=$row["id_partida"]?>
						</td>
						<td valign="top" align="left" class="normalNegro">
							<?=$row["nombre_partida"]?>
						</td>
						<td valign="top" align="left" class="normalNegro">
							<?=$row["descripcion"]?>
						</td>
						<td valign="top" align="center" class="normalNegro">
							<?=$row["cantidad"]?>
						</td>
					</tr>
					<?php
						}
					?>
					</tbody>
					<tr>
						<td height="19" colspan="6">&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table class="tablaalertas" width="900px">
					<tr>
						<td style="width: 260px;">Proveedor sugerido (RIF o nombre)</td>
						<td>
							<table>
								<tr>
									<td style="width: 80px;">sugerencia 1: </td>
									<?php
									$query = "SELECT prov_id_rif,prov_nombre FROM sai_proveedor_nuevo WHERE prov_id_rif = '".$rebms_prov_sugerido1."'";
									$resultado = pg_exec($conexion, $query);
									$elementos = pg_numrows($resultado);
									if($elementos>0){
										$row = pg_fetch_array($resultado, 0);
										$rebms_prov_sugerido1 = $row["prov_nombre"]." (RIF ".strtoupper(substr(trim($row["prov_id_rif"]),0,1))."-".substr(trim($row["prov_id_rif"]),1).")";
									}
									?>									
									<td class="normalNegro"><?=$rebms_prov_sugerido1?></td>
								</tr>
								<tr>
									<td>sugerencia 2: </td>
									<?php
									$query = "SELECT prov_id_rif,prov_nombre FROM sai_proveedor_nuevo WHERE prov_id_rif = '".$rebms_prov_sugerido2."'";
									$resultado = pg_exec($conexion, $query);
									$elementos = pg_numrows($resultado);
									if($elementos>0){
										$row = pg_fetch_array($resultado, 0);
										$rebms_prov_sugerido2 = $row["prov_nombre"]." (RIF ".strtoupper(substr(trim($row["prov_id_rif"]),0,1))."-".substr(trim($row["prov_id_rif"]),1).")";
									}
									?>
									<td class="normalNegro"><?=$rebms_prov_sugerido2?></td>
								</tr>
								<tr>
									<td>sugerencia 3: </td>
									<?php
									$query = "SELECT prov_id_rif,prov_nombre FROM sai_proveedor_nuevo WHERE prov_id_rif = '".$rebms_prov_sugerido3."'";
									$resultado = pg_exec($conexion, $query);
									$elementos = pg_numrows($resultado);
									if($elementos>0){
										$row = pg_fetch_array($resultado, 0);
										$rebms_prov_sugerido3 = $row["prov_nombre"]." (RIF ".strtoupper(substr(trim($row["prov_id_rif"]),0,1))."-".substr(trim($row["prov_id_rif"]),1).")";
									}
									?>
									<td class="normalNegro"><?=$rebms_prov_sugerido3?></td>
								</tr>
							</table>								
						</td>
					</tr>
					<tr>
						<td>Caracter&iacute;sticas sugeridas para seleccionar proveedor</td>
						<td >
							<table>
								<tr>
									<td>Tiempo de Entrega sugerido: </td>
									<td class="normalNegro">
										<?php
											if($rebms_tiempo_entrega_sugerida==TIEMPO_ENTREGA_MENOR_7_DIAS) echo "Menor a 7 D&iacute;as";
											else if($rebms_tiempo_entrega_sugerida==TIEMPO_ENTREGA_MENOR_2_SEMANAS) echo "Menor a 2 Semanas";
											else if($rebms_tiempo_entrega_sugerida==TIEMPO_ENTREGA_MENOR_1_MES) echo "Menor a 1 Mes";
											else if($rebms_tiempo_entrega_sugerida==TIEMPO_ENTREGA_MAYOR_1_MES) echo "Mayor a 1 Mes";
										?>
									</td>
								</tr>
								<tr>
									<td>Garant&iacute;a sugerida: </td>
									<td class="normalNegro">
										<?php
											if($rebms_garantia_sugerida==GARANTIA_SI) echo "Si";
											else if($rebms_garantia_sugerida==GARANTIA_NO) echo "No";
										?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>Observaciones:</td>
						<td class="normalNegro">
							<?=$rebms_observaciones?>
						</td>
					</tr>
					<?php if($rebms_observaciones_almacen && $rebms_observaciones_almacen!=""){ ?>
					<tr>
						<td>Observaciones Almac&eacute;n:</td>
						<td class="normalNegro">
							<?=$rebms_observaciones_almacen?>
						</td>
					</tr>
					<?php } ?>
				</table>
			</td>
		</tr>
		<?php
			//if($accion=="generar" || $accion=="modificar" || $accion=="almacen" || $accion=="compra"){
		if($estadia!="true" && (substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150")){
		?>
		<tr>
			<td height="16" colspan="2">
				<div align="center" class="normal" style="height: 50px;margin-top: 20px;">
					<div style="margin-top: 12px;float: left;text-align: right;width: 50%;">Para generar su documento en formato PDF haga clic</div>
					<div style="float: right;width: 50%;">
						<table align="left">
							<tr>
								<td align="center" width="80px">
									<a href="javascript: generarPdf1();">aqu&iacute;</a>
									<a href="javascript: generarPdf1();"><img src="../../imagenes/pdf_ico.jpg" border="0" align="center"/></a>
								</td>
								<!-- <td align="center" width="150px">
									<a href="javascript: generarPdf2();">aqu&iacute;</a>
									<a href="javascript: generarPdf2();"><img src="../../imagenes/pdf_ico.jpg" border="0" align="center"/></a>
								</td> -->
							</tr>
							<?php /* ?>
							<tr>
								<td align="center"><b>Formato 1</b></td>
								<!-- <td align="center"><b>Formato 2 (firmas por hoja)</b></td> -->
							</tr>
							*/?>
						</table>
					</div>
				</div>
				<br/>
			</td>
		</tr>
		<?php }?>
	</table>
	<br/>
</form>
<?php
	if($accion!=""){
?>
<div class="normalNegrita" align="center">
	<?php
		if($accion=="generar"){
			$opcion = 1;
	?>
		<p align="center">Usted ha generado con &eacute;xito el documento: <span class="resultados"><?= $idRequ?></span></p>
	<?php 
		}else if($accion=="generar_borrador"){
	?>
		<p align="center">Usted ha generado con &eacute;xito el documento borrador: <span class="resultados"><?= $idRequ?></span></p>
	<?php 
		}else if($accion=="modificar"){
			$opcion = 2;
	?>
		<p align="center">Usted ha modificado con &eacute;xito el documento: <span class="resultados"><?= $idRequ?></span></p>
	<?php
		}else if($accion=="modificar_borrador"){
	?>
		<p align="center">Usted ha modificado con &eacute;xito el documento borrador: <span class="resultados"><?= $idRequ?></span></p>
	<?php
		}else if($accion=="aprobar"){
			$opcion = 6;
			if(substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46" || $user_perfil_id == "47350" || $user_perfil_id == "65150"){//GERENTE O DIRECTOR
				$objeto = 3;
				$depe_id = "400";
			}else if($user_perfil_id == "30400"){//ANALISTA DE PRESUPUESTO
				$objeto = 4;
				$depe_id = "456";	
			}
	?>
		<p align="center">Usted ha aprobado con &eacute;xito el documento: <span class="resultados"><?= $idRequ?></span></p>
	<?php
		}else if($accion=="devolver"){
			if($user_perfil_id == "15456" || $user_perfil_id == "42456"){
				$objetoSiguiente = 2;
			}
			$opcion = 5;
	?>
		<p align="center">Usted ha devuelto con &eacute;xito el documento: <span class="resultados"><?= $idRequ?></span></p>
	<?php
		}else if($accion=="anular"){
			$opcion = 5;
	?>
		<p align="center">Usted ha anulado con &eacute;xito el documento: <span class="resultados"><?= $idRequ?></span></p>
	<?php
		}
		
		if($accion!="anular" && $accion!="generar_borrador" && $accion!="modificar_borrador" && $depe_id!=""){
			
			//OBTENER GRUPO E INDICAR OPERACION
			if(substr($user_perfil_id, 0, 2)=="37"){//ASISTENTE ADMINISTRATIVO
				$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
							"WHERE ".
								"(swfg.wfgr_perf = '".substr($user_perfil_id, 0, 2)."000' ".
								"OR swfg.wfgr_perf like '".substr($user_perfil_id, 0, 2)."000%' ".
								"OR swfg.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000' ".
								"OR swfg.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000%') ";		
				$resultado = pg_exec($conexion, $query);
				$row = pg_fetch_array($resultado, 0);
				$idGrupo = $row["wfgr_id"];
			}else if($user_perfil_id == "38350" || $user_perfil_id == "68150"){//ASISTENTE EJECUTIVO, SECRETARIA PRESIDENCIA
				$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
							"WHERE ".
								"(swfg.wfgr_perf = '".$user_perfil_id."' ".
								"OR swfg.wfgr_perf like '".$user_perfil_id."%' ".
								"OR swfg.wfgr_perf like '%/".$user_perfil_id."' ".
								"OR swfg.wfgr_perf like '%/".$user_perfil_id."%') ";		
				$resultado = pg_exec($conexion, $query);
				$row = pg_fetch_array($resultado, 0);
				$idGrupo = $row["wfgr_id"];
			}else if(substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46"){//GERENTE O DIRECTOR
				$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
							"WHERE ".
								"(swfg.wfgr_perf = '".substr($user_perfil_id, 0, 2)."000' ".
								"OR swfg.wfgr_perf like '".substr($user_perfil_id, 0, 2)."000%' ".
								"OR swfg.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000' ".
								"OR swfg.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000%') ";		
				$resultado = pg_exec($conexion, $query);
				$row = pg_fetch_array($resultado, 0);
				$idGrupo = $row["wfgr_id"];
			}else if($user_perfil_id == "47350" || $user_perfil_id == "65150"){//DIRECTOR EJECUTIVO, PRESIDENTE
				$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
							"WHERE ".
								"(swfg.wfgr_perf = '".$user_perfil_id."' ".
								"OR swfg.wfgr_perf like '".$user_perfil_id."%' ".
								"OR swfg.wfgr_perf like '%/".$user_perfil_id."' ".
								"OR swfg.wfgr_perf like '%/".$user_perfil_id."%') ";		
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
			}else if($user_perfil_id == "15456" || $user_perfil_id == "42456"){//ANALISTA DE COMPRAS, COORDINADOR DE COMPRAS
				/*$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
							"WHERE ".
								"swfg.wfgr_perf = '".$user_perfil_id."' ";*/
				$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
							"WHERE ".
								"(swfg.wfgr_perf = '".$user_perfil_id."' ".
								"OR swfg.wfgr_perf like '".$user_perfil_id."%' ".
								"OR swfg.wfgr_perf like '%/".$user_perfil_id."' ".
								"OR swfg.wfgr_perf like '%/".$user_perfil_id."%') ";
				$resultado = pg_exec($conexion, $query);
				$row = pg_fetch_array($resultado, 0);
				$idGrupo = $row["wfgr_id"];
			}
			
			$sql=	"SELECT swfg.wfgr_descrip ".
					"FROM sai_wfcadena swfc,sai_wfcadena swfch, sai_wfgrupo swfg ".
					"WHERE ".
						"swfc.docu_id = 'rqui' AND ".
						"swfc.wfop_id = ".$opcion." AND ".
						"swfc.wfgr_id = ".$idGrupo." AND ".
						(($objeto && $objeto!=0)?"swfc.wfob_id_ini = ".$objeto." AND ":"").
						(($objetoSiguiente && $objetoSiguiente!=0)?"swfc.wfob_id_sig = ".$objetoSiguiente." AND ":"").
						(($depe_rqui=="350" || $depe_rqui=="150")?"swfc.depe_id = '".$depe_rqui."' AND ":" (swfc.depe_id IS NULL OR swfc.depe_id = '') AND ").
						"swfc.wfca_id_hijo = swfch.wfca_id AND ".
						"swfch.wfgr_id = swfg.wfgr_id";
			$resultado = pg_exec($conexion,$sql);
			if($resultado){
				$row = pg_fetch_array($resultado,0);
				$wfgr_descrip=trim($row["wfgr_descrip"]);
				
				$query = 	"SELECT depe_nombre FROM sai_dependenci ".
							"WHERE ".
								"depe_id = '".$depe_id."'";
				$resultado = pg_exec($conexion, $query);
				$row = pg_fetch_array($resultado, 0);
				$depe_nombre = $row["depe_nombre"];

	?>
	<p align="center">El Documento fue enviado a la instancia: <span class="resultados"><?= $wfgr_descrip.(($depe_id != "456")?" en ".$depe_nombre:"")?></span></p>
	<?php 	}
		} ?>
</div>
<?php
	}
}?>
</body>
</html>
<?php pg_close($conexion); ?>