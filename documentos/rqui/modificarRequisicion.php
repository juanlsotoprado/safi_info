<?php
ob_start();
session_start();
require("../../includes/conexion.php");
require("../../includes/funciones.php");
if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush();
	exit;
}
require("../../includes/constantes.php");
$codigo = "";
if (isset($_GET['codigo']) && $_GET['codigo'] != "") {
	$codigo = $_GET['codigo'];
}
$idRequ = "";
if (isset($_GET['idRequ']) && $_GET['idRequ'] != "") {
	$idRequ = $_GET['idRequ'];
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
$estado = ESTADO_REQUISICION_DEVUELTAS;
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
$bandeja = "";
if (isset($_GET['bandeja']) && $_GET['bandeja'] != "") {
	$bandeja = $_GET['bandeja'];
}
$estadoBorrador = 60;
$sql="SELECT perf_id_act, esta_id FROM sai_doc_genera WHERE docg_id = '".$idRequ."'";
$resultado = pg_exec($conexion ,$sql);
$row = pg_fetch_array($resultado,0);
if($row["perf_id_act"]!=$_SESSION['user_perfil_id'] && $row["esta_id"]!=$estadoBorrador){
	header('Location:detalleRequisicion.php?codigo='.$codigo.'&idRequ='.$idRequ.'&tipoRequ='.$tipoRequ.'&tipoBusq='.$tipoBusq.'&pagina='.$pagina.'&estado='.$estado.'&controlFechas='.$controlFechas.'&fechaInicio='.$fechaInicio.'&fechaFin='.$fechaFin."&proyAcc=".$proyAcc."&radioProyAcc=".$radioProyAcc."&proyecto=".$proyecto."&accionCentralizada=".$accionCentralizada."&dependencia=".$dependencia,false);
	exit;
}else{
	if($idRequ && $idRequ!=""){
		$query = 	"SELECT esta_id ".
					"FROM sai_req_bi_ma_ser ".
					"WHERE ".
					"rebms_id = '".$idRequ."' ";
		
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$estadoAnulado = "15";
		if($row["esta_id"]==$estadoAnulado){
			header('Location:detalleRequisicion.php?codigo='.$codigo.'&idRequ='.$idRequ.'&tipoRequ='.$tipoRequ.'&tipoBusq='.$tipoBusq.'&pagina='.$pagina.'&estado='.$estado.'&controlFechas='.$controlFechas.'&fechaInicio='.$fechaInicio.'&fechaFin='.$fechaFin."&proyAcc=".$proyAcc."&radioProyAcc=".$radioProyAcc."&proyecto=".$proyecto."&accionCentralizada=".$accionCentralizada."&dependencia=".$dependencia,false);
			exit;
		}
	}
}
ob_end_flush();
$pres_anno=$_SESSION['an_o_presupuesto'];
$estatusDeshabilitado = "0";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:. Modificar Requisici&oacute;n</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css"/>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script language="JavaScript" src="../../js/funciones.js"></script>
<script language="JavaScript" src="../../js/crearModificarRequisicion.js"></script>
<script><!--
	dependencia = '<?= $_SESSION["user_depe_id"]?>';
	function bandeja(){
		location.href = "bandeja.php";
	}
	function irARequisiciones(){
		codigo = '<?= $codigo?>';
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
		location.href = "../rqui/busquedas.php?codigo="+codigo+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&estado="+estado+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&proyAcc="+proyAcc+"&radioProyAcc="+radioProyAcc+"&proyecto="+proyecto+"&accionCentralizada="+accionCentralizada+"&dependencia="+dependencia;
	}

	function anular(){
		if(confirm(pACUTE+'Est'+aACUTE+' seguro que desea anular esta requisici'+oACUTE+'n?.')){
			document.getElementById("accion").value = '<?= ACCION_ANULAR_REQUISICION?>';
			document.form.submit();
		}
	}
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
					"srbms.gerencia_adscripcion ".
				"FROM sai_req_bi_ma_ser srbms, sai_estado se, sai_dependenci sd ".
				"WHERE ".
				"srbms.rebms_id = '".$idRequ."' AND ".
				"srbms.esta_id = se.esta_id AND ".
				"srbms.depe_id = sd.depe_id ";
	
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
		//$aces_nombre = $row["paes_nombre"]."(".$row["centro_gestor"]."-".$row["centro_costo"].")";
		$aces_nombre = $row["paes_nombre"];
		$centroGestor = $row["centro_gestor"];
		$centroCosto = $row["centro_costo"];
	}else if($rebms_tipo_imputa==TIPO_IMPUTACION_ACCION_CENTRALIZADA){//Accion centralizada
		$query = "SELECT aces_nombre,centro_gestor,centro_costo FROM sai_acce_esp WHERE acce_id = '".$rebms_imp_p_c."' AND aces_id = '".$rebms_imp_esp."'";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		//$aces_nombre = $row["aces_nombre"]."(".$row["centro_gestor"]."-".$row["centro_costo"].")";
		$aces_nombre = $row["aces_nombre"];
		$proy_titulo = $rebms_imp_p_c."-".$row["aces_nombre"];
		$centroGestor = $row["centro_gestor"];
		$centroCosto = $row["centro_costo"];
	}
?>
<script>
	tipoReqActual = "<?= $rebms_tipo?>";
</script>
<?php 
if ( $bandeja!="true" ) {
?>
<div align="center" style="margin-top: 20px; margin-bottom: 20px;">
	<a href='javascript: irARequisiciones();'>Volver a los resultados de la b&uacute;squeda</a>
</div>
<?php
}
$msg=$_GET['msg'];
if($msg=="0"){
	echo "<p class='normal' style='color: red;text-align: center;'>Debe indicar el tipo de requisici&oacute;n (Compra o Servicio).</p>";
}else if($msg=="1"){
	echo "<p class='normal' style='color: red;text-align: center;'>Debe indicar el Proyecto o Acci&oacute;n Centralizada.</p>";
}else if($msg=="2"){
	echo "<p class='normal' style='color: red;text-align: center;'>Debe indicar la Acci&oacute;n Espec&iacute;fica.</p>";
}else if($msg=="3"){
	echo "<p class='normal' style='color: red;text-align: center;'>Error interno. Falta la cantidad de art&iacute;culos agregados.</p>";
}else if($msg=="4"){
	echo "<p class='normal' style='color: red;text-align: center;'>Las cantidades indicadas deben ser valores num&eacute;ricos.</p>";
}else if($msg=="5"){
	echo "<p class='normal' style='color: red;text-align: center;'>Debe indicar al menos un (1) art&iacute;culo, bien o servicio para la solicitud de requisici&oacute;n.</p>";
}else if($msg=="6"){
	echo "<p class='normal' style='color: red;text-align: center;'>Debe indicar la justificaci&oacute;n del punto de cuenta.</p>";
}
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
	if($wfgr_perf == '15456'){//Analista de Compras
		$depeIdInstancia = '456';
	}else{
		$depeIdInstancia = $row["depe_id"];
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
		<td colspan="2"  class="normalNegroNegrita">
			C&Oacute;DIGO DEL &Uacute;LTIMO MEMO: <?= $row["memo_id"]?>
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
?>
<form id="form" name="form" method="post" action="modificarRequisicionAccion.php">
	<input type="hidden" name="idRequ" id="idRequ" value="<?=$idRequ?>"/>
	<input type="hidden" name="codigo" id="codigo" value="<?=$codigo?>"/>
	<input type="hidden" name="tipoRequ" id="tipoRequ" value="<?=$tipoRequ?>"/>
	<input type="hidden" name="controlFechas" id="controlFechas" value="<?=$controlFechas?>"/>
	<input type="hidden" name="fechaInicio" id="fechaInicio" value="<?=$fechaInicio?>"/>
	<input type="hidden" name="fechaFin" id="fechaFin" value="<?=$fechaFin?>"/>
	<input type="hidden" name="pagina" id="pagina" value="<?=$pagina?>"/>
	<input type="hidden" name="proyAcc" id="proyAcc" value="<?=$proyAcc?>"/>
	<input type="hidden" name="radioProyAcc" id="radioProyAcc" value="<?=$radioProyAcc?>"/>
	<input type="hidden" name="proyecto" id="proyecto" value="<?=$proyecto?>"/>
	<input type="hidden" name="accionCentralizada" id="accionCentralizada" value="<?=$accionCentralizada?>"/>
	<input type="hidden" name="dependencia" id="dependencia" value="<?=$dependencia?>"/>
	<input type="hidden" name="estado" id="estado" value="<?=$estado?>"/>
	<input type="hidden" name="bandeja" id="bandeja" value="<?=$bandeja?>"/>
	<input type="hidden" name="accion" id="accion" value=""/>
	<input type="hidden" name="txt_id_tp_p_ac" id="txt_id_tp_p_ac" value="<?=$rebms_tipo_imputa?>"/>
	<table align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td colspan="2" class="normalNegroNegrita">
				Modificar Requisici&oacute;n: <?= $idRequ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="tablaalertas" width="900px">
					<tr>
						<td width="20%">
							Tipo de requisici&oacute;n<span class="peq_naranja">(*)</span>
						</td>
						<td width="80%" class="normalNegro">
							<input type='radio' name='typo' id='typoCompra' value='<?= TIPO_REQUISICION_COMPRA?>' <?php if($rebms_tipo == TIPO_REQUISICION_COMPRA){ echo "checked='checked'";} ?>/>Compra
							<input type='radio' name='typo' id='typoServicio' value='<?= TIPO_REQUISICION_SERVICIO?>' <?php if($rebms_tipo == TIPO_REQUISICION_SERVICIO){ echo "checked='checked'";} ?>/>Servicio
							<div class="normal" style="float: right;">
								Fecha
								<input type="text" size="10" id="fecha" name="fecha" class="dateparse" readonly="readonly" value="<?= $fecha?>"/>
								<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha');" title="Establecer Fecha">
									<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Establecer Fecha"/>
								</a>
							</div>
						</td>
					</tr>	
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="tablaalertas" width="900px">
					<tr class="td_gray normalNegrita">
						<td>
							<div align="center">
								<a href="javascript:verifica_partida();" id="mostrarCategorias">
									<img src="../../imagenes/estadistic.gif" width="24" height="24" border="0" />
									Categor&iacute;a
								</a>
								<span class="peq_naranja">(*)</span>
							</div>
						</td>
						<td align="center">
							C&oacute;digo
						</td>
						<td align="center">
							Denominaci&oacute;n
						</td>
					</tr>
					<tr>
						<td  class="normalNegro">
							<div align="left">
								<input id="tipo_proyecto" name="chk_tp_imputa" type="radio" class="peq" value="<?= TIPO_IMPUTACION_PROYECTO?>" <?php if($rebms_tipo_imputa==TIPO_IMPUTACION_PROYECTO){echo "checked='checked'";}?> disabled="disabled"/>
								Proyectos
							</div>
						</td>
						<td rowspan="2">
							<div align="center">
								<input id="txt_cod_imputa" name="txt_cod_imputa" type="hidden" value="<?= $rebms_imp_p_c?>"/>
								<input id="txt_cod_imputa2" name="txt_cod_imputa2" type="text" class="ptotal" size="15" readonly="readonly" value="<?= $centroGestor?>"/>
							</div>
						</td>
						<td rowspan="2">
							<div align="center">
								<input name="txt_nombre_imputa" type="text" class="ptotal" id="txt_nombre_imputa" size="70" readonly="readonly" value="<?= $proy_titulo?>"/>
							</div>
						</td>
					</tr>
					<tr>
						<td valign="top" class="normalNegro">
							<div align="left">
								<input id="tipo_accion" name="chk_tp_imputa" type="radio" class="peq" value="<?= TIPO_IMPUTACION_ACCION_CENTRALIZADA?>" <?php if($rebms_tipo_imputa==TIPO_IMPUTACION_ACCION_CENTRALIZADA){echo "checked='checked'";}?> disabled="disabled"/>
								Acci&oacute;n Cent.
							</div>
						</td>
					</tr>
					<tr>
						<td class="normalNegro">
							&nbsp;Acci&oacute;n Espec&iacute;fica
						</td>
						<td>
							<div align="center">
								<input id="txt_cod_accion" name="txt_cod_accion" type="hidden" value="<?= $rebms_imp_esp?>"/>
								<input id="txt_cod_accion2" name="txt_cod_accion2" type="text" class="ptotal" size="15" readonly="readonly" value="<?= $centroCosto?>"/>
							</div>
						</td>
						<td>
							<div align="center"><input name="txt_nombre_accion" type="text" class="ptotal" id="txt_nombre_accion" size="70" readonly="readonly" value="<?= $aces_nombre?>"/></div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="tablaalertas" width="900px">
					<tr>
						<td>
							Unidad de adscripci&oacute;n
						</td>
						<td>
						<div align="left">
							<?php
							if($_SESSION['user_depe_id']!="150" && $_SESSION['user_depe_id']!="350"){
							?>
							<select id="gerenciaAdscripcion" name="gerenciaAdscripcion" class="normalNegro">
								<?php
								$nivelOficinaGerencia = "5";
								$estadoAnulado = "15";
								$query = 	"SELECT depe_id,depe_nombre ".
											"FROM sai_dependenci ".
											"WHERE ".
											"depe_nivel = ".$nivelOficinaGerencia." AND ".
											"depe_id_sup = '".$_SESSION['user_depe_id']."' AND ".
											"esta_id <> ".$estadoAnulado." ".
											"ORDER BY depe_nombre";
								$resultado = pg_exec($conexion, $query);
								$numeroFilas = pg_numrows($resultado);
								for($i = 0; $i < $numeroFilas; $i++) {
									$row = pg_fetch_array($resultado, $i);
								?>
									<option value="<?= $row["depe_id"]?>" <?php if($gerencia_adscripcion == $row["depe_id"]){ echo "selected='selected'";} ?>><?=$row["depe_nombre"]?></option>
								<?php 
									}
								?>
								<option value="<?= $_SESSION['user_depe_id']?>" <?php if($gerencia_adscripcion == $_SESSION['user_depe_id']){ echo "selected='selected'";} ?>><?=$_SESSION['user_depe']?></option>
							</select>
							<?php 
							}else{
								echo "<input type='hidden' id='gerenciaAdscripcion' name='gerenciaAdscripcion' value='".$_SESSION['user_depe_id']."'/><input type='text' class='ptotal' size='53' readonly='readonly' value='".$_SESSION['user_depe']."'/>";
							}
							?>
						</div>
						</td>
					</tr>
					<tr>
						<td>Descripci&oacute;n del producto o servicio solicitado</td>
						<td>
							<div style="width: 100%">
							<textarea class="normalNegro" id="descripcionGeneral" name="descripcionGeneral" cols="99" rows="2"
								onkeydown="textCounter(this,'descripcionGeneralLen',1000);"
								onkeyup="textCounter(this,'descripcionGeneralLen',1000);validarTexto(this);"><?= $descripcion_general?></textarea><br/>
							<div style="text-align: right;"><input type="text" value="<?= (1000-strlen($descripcion_general))?>" class="normalNegro" maxlength="3" size="3" id="descripcionGeneralLen" name="descripcionGeneralLen" readonly="readonly"/></div>
							</div>
						</td>
					</tr>
					<tr>
						<td>Justificaci&oacute;n de la requisici&oacute;n<span class="peq_naranja">(*)</span></td>
						<td>
							<div style="width: 100%">
							<textarea class="normalNegro" id="justificacion" name="justificacion" cols="99" rows="2"
								onkeydown="textCounter(this,'justificacionLen',1000);"
								onkeyup="textCounter(this,'justificacionLen',1000);validarTexto(this);"><?= $justificacion?></textarea><br/>
							<div style="text-align: right;"><input type="text" value="<?= (1000-strlen($justificacion))?>" class="normalNegro" maxlength="3" size="3" id="justificacionLen" name="justificacionLen" readonly="readonly"/></div>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="tablaalertas" width="900px">
					<?php /*
					<tr class="td_gray" width="644px">
						<td>
							<div align="center" class="peqNegrita">Punto de Cuenta<span class="peq_naranja">(*)</span></div>
						</td>
						<td>
						<div align="left" class="peqNegrita">
							<select id="pcta" name="pcta">
								<option value="" onmouseover="HideContent('contenedorDetalle')">No aplica</option>
								<?php
								$estadoAprobado = 10;
								$asuntoAlcance = "013";
								$query = 	"SELECT sp.pcta_id, to_char(sp.pcta_fecha,'DD/MM/YYYY') as pcta_fecha, sp.pcta_descripcion, spa.pcas_descrip ".
											"FROM sai_pcuenta sp, sai_pcta_asunt spa ".
											"WHERE ".
												"sp.depe_id = '".$_SESSION['user_depe_id']."' AND ".
												"sp.esta_id = ".$estadoAprobado." AND ".
												"sp.pcta_monto_solicitado > 0 AND ".
												"sp.pcta_asunto <> '".$asuntoAlcance."' AND ".
												"sp.pcta_asunto = spa.pcas_id ".
											"ORDER BY spa.pcas_descrip, sp.pcta_id ";
								$resultado = pg_exec($conexion, $query);
								$numeroFilas = pg_numrows($resultado);
								for($i = 0; $i < $numeroFilas; $i++) {
									$row = pg_fetch_array($resultado, $i);
								?>
									<option id="<?= $row["pcta_id"]?>" value="<?= $row["pcta_id"]?>" <?php if($pcta_id==$row["pcta_id"]){echo "selected='selected'";}?>><?= $row["pcta_id"]." - ".$row["pcas_descrip"].". Fecha: ".$row["pcta_fecha"]."."?></option>
									<script>
										document.getElementById("<?= $row["pcta_id"]?>").onmouseover = function() { ShowContent('contenedorDetalle','<?= $row["pcta_id"]?>','<?= str_replace("\n"," ", str_replace("\r"," ", $row["pcta_descripcion"]))?>'); }
										document.getElementById("<?= $row["pcta_id"]?>").onmouseout = function(){myTime = setTimeout('HideContent(\'contenedorDetalle\')', 20000);};
									</script>
								<?php 
									}
								?>
							</select>
						</div>
						</td>
					</tr>
					*/?>
					<tr>
						<td>
							Punto de Cuenta
						</td>
						<td>
							<select id="pcta" name="pcta" class="normalNegro" onchange="validarPcta();">
								<option value="">N/A</option>
								<?php
								$pctaPartidas = "[";
								$pctaImputaciones = "[";
								$pctaAnterior = "";
								$estadoAprobado = 10;
								$asuntoAlcance = "013";
								
								/*$query = 	"SELECT sp.pcta_id, spi.pcta_sub_espe ".
											"FROM sai_pcuenta sp, sai_pcta_imputa spi, sai_pcta_asunt spa, sai_doc_genera sdg ".
											"WHERE ".
												"sp.pcta_id LIKE '%".substr($pres_anno, -2)."' AND ".
												"sp.depe_id = '".$_SESSION['user_depe_id']."' AND ".
												//"sp.esta_id = ".$estadoAprobado." AND ".
												"sp.pcta_monto_solicitado > 0 AND ".
												"sp.pcta_asunto <> '".$asuntoAlcance."' AND ".
												"sp.pcta_asunto = spa.pcas_id AND ".
												"sp.pcta_id = spi.pcta_id AND ".
												"sp.pcta_id = sdg.docg_id AND ".
												"(sdg.wfob_id_ini = 99 OR sdg.perf_id_act = '47350' OR sdg.perf_id_act = '65150') ".
											"ORDER BY sp.pcta_fecha, sp.pcta_id, spi.pcta_sub_espe";*/
								$query = 	"SELECT
													sp.pcta_id,
													spi.pcta_sub_espe,
													s.tipo,
													s.id_proyecto_accion,
													s.id_accion_especifica,
													s.nombre_proyecto_accion,
													s.nombre_accion_especifica,
													s.centro_gestor,
													s.centro_costo
												FROM
													sai_pcuenta sp
													INNER JOIN sai_doc_genera sdg ON (sp.pcta_id = sdg.docg_id)
													INNER JOIN sai_pcta_asunt spa ON (sp.pcta_asunto = spa.pcas_id)
													INNER JOIN sai_pcta_imputa spi ON (sp.pcta_id = spi.pcta_id)
													INNER JOIN
														(
															SELECT
																'1'::BIT as tipo,
																spae.proy_id as id_proyecto_accion,
																spae.paes_id as id_accion_especifica,
																sp.proy_titulo as nombre_proyecto_accion,
																spae.paes_nombre as nombre_accion_especifica,
																spae.centro_gestor,
																spae.centro_costo
															FROM sai_proyecto sp, sai_proy_a_esp spae
															WHERE
																sp.pre_anno = spae.pres_anno AND
																sp.proy_id = spae.proy_id AND
																spae.pres_anno = ".$pres_anno."
															UNION
															SELECT
																'0'::BIT as tipo,
																sae.acce_id as id_proyecto_accion,
																sae.aces_id as id_accion_especifica,
																sac.acce_denom as nombre_proyecto_accion,
																sae.aces_nombre as nombre_accion_especifica,
																sae.centro_gestor,
																sae.centro_costo
															FROM sai_ac_central sac, sai_acce_esp sae
															WHERE
																sac.pres_anno = sae.pres_anno AND
																sac.acce_id = sae.acce_id AND
																sae.pres_anno = ".$pres_anno."
														) AS s ON (spi.pcta_tipo_impu = s.tipo AND spi.pcta_acc_pp = s.id_proyecto_accion AND spi.pcta_acc_esp = s.id_accion_especifica)
												WHERE
													sp.pcta_id LIKE '%".substr($pres_anno, -2)."' AND
													sp.depe_id = '".$_SESSION['user_depe_id']."' AND
													sp.pcta_monto_solicitado > 0 AND
													sp.pcta_asunto <> '".$asuntoAlcance."' AND
													(sdg.wfob_id_ini = 99 OR sdg.perf_id_act = '47350' OR sdg.perf_id_act = '65150')
												ORDER BY sp.pcta_fecha, sp.pcta_id, spi.pcta_sub_espe";
								$resultado = pg_exec($conexion, $query);
								$numeroFilas = pg_numrows($resultado);
								for($i = 0; $i < $numeroFilas; $i++) {
									$row = pg_fetch_array($resultado, $i);
									if($pctaAnterior!=$row["pcta_id"]){
										$pctaAnterior=$row["pcta_id"];
										if($i == 0){
											$pctaPartidas .= "[";
											$pctaImputaciones .= "[";
										}else{
											$pctaPartidas = substr($pctaPartidas, 0, -1)."],[";
											$pctaImputaciones = substr($pctaImputaciones, 0, -1)."],[";
										}
								?>
										<option id="<?= $row["pcta_id"]?>" value="<?= $row["pcta_id"]?>" <?php if($pcta_id==$row["pcta_id"]){echo "selected='selected'";}?>><?= $row["pcta_id"]?></option>
								<?php 
									}
									$pctaPartidas .= "'".$row["pcta_sub_espe"]."',";
									$pctaImputaciones .= "['".$row["tipo"]."','".$row["id_proyecto_accion"]."','".$row["id_accion_especifica"]."','".$row["nombre_proyecto_accion"]."','".$row["nombre_accion_especifica"]."','".$row["centro_gestor"]."','".$row["centro_costo"]."'],";
								}
								if($pctaPartidas!=""){
									$pctaPartidas = substr($pctaPartidas, 0, -1)."]]";
								}
								if($pctaImputaciones!=""){
									$pctaImputaciones = substr($pctaImputaciones, 0, -1)."]]";
								}
								?>
							</select>
							<script>
								pctaPartidas = <?= $pctaPartidas?>;
								pctaImputaciones = <?= $pctaImputaciones?>;
								indicePctaActual = document.getElementById("pcta").selectedIndex;
							</script>
						</td>
					</tr>
					<tr>
						<td>Justificaci&oacute;n del Punto de Cuenta<span class="peq_naranja">(*)</span></td>
						<td>
							<div style="width: 100%">
							<textarea class="normalNegro" id="pctaJustificacion" name="pctaJustificacion" cols="99" rows="2"
								onkeydown="textCounter(this,'pctaJustificacionLen',1000);"
								onkeyup="textCounter(this,'pctaJustificacionLen',1000);validarTexto(this);"><?= $pcta_justificacion?></textarea><br/>
							<div style="text-align: right;"><input type="text" value="<?= (1000-strlen($pcta_justificacion))?>" class="normalNegro" maxlength="3" size="3" id="pctaJustificacionLen" name="pctaJustificacionLen" readonly="readonly"/></div>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="hidden" name="hid_largo" id="hid_largo"/>
				<input type="hidden" name="hid_val" id="hid_val"/>
				<table width="900px" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
					<tr>
						<td>
							<div id="itemContainer" style="width: 84%;float: left;">
								&nbsp;Rubro<span class="peq_naranja">(*)</span> <input autocomplete="off" size="96" type="text" id="itemCompletar" name="itemCompletar" value="" class="normalNegro"/>
							</div>
							<div style="float: left;width: 16%;">
								&nbsp;Cantidad: <input maxlength="10" type="text" id="cantidad" name="cantidad" onkeyup="validarInteger(this);" size="10" class="normalNegro"/>
							</div>
							<div style="width: 100%; float: left;text-align: left;margin-top: -10px;">
								<br/>&nbsp;Introduzca la partida o una palabra contenida en el nombre del art&iacute;culo, bien o servicio.
							</div>
							<div style="width: 100%;margin-top: 50px;">
								&nbsp;Especificaciones:<br/>
								<textarea class="normalNegro" id="articuloEspecificaciones" name="articuloEspecificaciones" cols="123" rows="3"
									onkeydown="textCounter(this,'articuloEspecificacionesLen',10000);"
									onkeyup="textCounter(this,'articuloEspecificacionesLen',10000);validarTexto(this);"></textarea><br/>
								<div style="text-align: right;"><input type="text" value="10000" class="normalNegro" maxlength="5" size="5" id="articuloEspecificacionesLen" name="articuloEspecificacionesLen" readonly="readonly"/>&nbsp;<input type="button" value="Agregar" onclick="javascript:agregarItem();" class="normalNegro"/></div>
							</div>					
							<?php
								$query = 	"SELECT ".
												"sp.part_id, ".
												"sp.part_nombre, ".
												"sip.id_item, ".
												"UPPER(si.nombre) as nombre ".
											"FROM sai_item si, sai_item_partida sip, sai_partida sp ".
											"WHERE ".
												"sp.pres_anno='".$pres_anno."' AND ".
												"sp.part_id NOT LIKE '4.03.18.01.00' AND ".
												"sp.part_id=sip.part_id AND ".
												"sip.id_item=si.id AND ".
												"si.esta_id <> ".$estatusDeshabilitado." ".
											"ORDER BY sp.part_id, si.nombre ";
								$resultado = pg_exec($conexion, $query);
								$numeroFilas = pg_num_rows($resultado);
								
								$arregloItems = "";
								$idsPartidasItems = "";
								$nombresPartidasItems = "";
								$idsItems = "";
								$nombresItems = "";
								while($row=pg_fetch_array($resultado)){
									$arregloItems .= "'".$row["part_id"]." : ".strtoupper(str_replace("\n"," ",$row["nombre"]))."',";
									$idsPartidasItems .= "'".$row["part_id"]."',";
									$idsItems .= "'".$row["id_item"]."',";
									$nombresPartidasItems .= "'".str_replace("\n"," ",strtoupper($row["part_nombre"]))."',";
									$nombresItems .= "'".str_replace("\n"," ",strtoupper($row["nombre"]))."',";
								}
								$arregloItems = quitarAcentosMayuscula(substr($arregloItems, 0, -1));
								$idsPartidasItems = substr($idsPartidasItems, 0, -1);
								$nombresPartidasItems = quitarAcentosMayuscula(substr($nombresPartidasItems, 0, -1));
								$idsItems = substr($idsItems, 0, -1);
								$nombresItems = quitarAcentosMayuscula(substr($nombresItems, 0, -1));
							?>
							<script>
								var arregloItems = new Array(<?= $arregloItems?>);
								var idsPartidasItems = new Array(<?= $idsPartidasItems?>);
								var nombresPartidasItems = new Array(<?= $nombresPartidasItems?>);
								var idsItems = new Array(<?= $idsItems?>);
								var nombresItems = new Array(<?= $nombresItems?>);
								actb(document.getElementById('itemCompletar'),arregloItems);
							</script>
						</td>
					</tr>
					<tr>
						<td>
							<table width="100%" class="tablaalertas" id="tbl_mod">
								<tr class="td_gray normalNegrita">
									<td align="center" width="10%">C&oacute;digo</td>
									<td align="center" width="15%">Nombre</td>
									<td align="center" width="10%">Partida</td>
									<td align="center" width="15%">Denominaci&oacute;n</td>
									<td align="center" width="30%">Especificaciones</td>
									<td align="center" width="10%">Cantidad</td>
									<td align="center" width="10%">Acci&oacute;n</td>
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
								?>
								<script>
									document.getElementById("hid_largo").value='<?= $elementos?>';
								</script>
								<?php
									for($i=0;$i<$elementos;$i++){
										$row = pg_fetch_array($resultado, $i);
								?>
								<script>
									var registro = new Array(6);
									registro[0]='<?= $row["id"]?>';//ID ARTICULO
									registro[1]='<?= $row["nombre"]?>';//NOMBRE ARTICULO
									registro[2]='<?= $row["id_partida"]?>';//ID PARTIDA
									registro[3]='<?= $row["nombre_partida"]?>';//NOMBRE PARTIDA
									registro[4]='<?= str_replace("\n"," ",$row["descripcion"])?>';//DESCRIPCION
									registro[5]='<?= $row["cantidad"]?>';//CANTIDAD
									
									partidas[partidas.length]=registro;
									articulos[articulos.length]=registro[0];									
								</script>
								<tr class="normalNegro">
									<td align="center" valign="top">
										<input type="hidden" name="txt_id_art<?=$i?>" value="<?=$row["id"]?>"/>
										<?=$row["id"]?>
									</td>
									<td align="left" valign="top">
										<input type="hidden" name="txt_nb_art<?=$i?>" value="<?=$row["nombre"]?>"/>
										<?=$row["nombre"]?>
									</td>
									<td align="center" valign="top">
										<input type="hidden" name="txt_id_pda<?=$i?>" value="<?=$row["id_partida"]?>"/>
										<?=$row["id_partida"]?>
									</td>
									<td align="left" valign="top">
										<input type="hidden" name="txt_nb_pda<?=$i?>" value="<?=$row["nombre_partida"]?>"/>
										<?=$row["nombre_partida"]?>
									</td>
									<td align="left" valign="top">
										<input type="hidden" id="txt_prod<?=$i?>" name="txt_prod<?=$i?>" value="<?=$row["descripcion"]?>"/>
										<div id="divEspecificaciones<?=$i?>" class="especificaciones"><?=$row["descripcion"]?></div>
									</td>
									<td align="center" valign="top">
										<input type="hidden" id="txt_cantidad<?=$i?>" name="txt_cantidad<?=$i?>" value="<?=$row["cantidad"]?>"/>
										<div id="divCantidad<?=$i?>"><?=$row["cantidad"]?></div>
									</td>
									<td align="center" valign="top">
										<a href="javascript:elimina_pda('<?=($i+1)?>')">Eliminar</a>
										<br/>
										<a href="javascript:modificar('<?=($i+1)?>')">Modificar</a>
									</td>
								</tr>
								<?php
									}
								?>
								</tbody>
								<tr>
									<td height="19" colspan="7">&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table class="tablaalertas" width="900px">
					<tr>
						<td class="peq">Proveedor sugerido (RIF o nombre)</td>
						<?php
							$estadoAnulado = 15;
							$sql_e="SELECT prov_id_rif,prov_nombre FROM sai_proveedor_nuevo WHERE prov_esta_id <> '".$estadoAnulado."' ORDER BY prov_nombre";
							$resultado_set_e=pg_query($conexion,$sql_e) or die("Error al mostrar");
							$numeroFilas = pg_numrows($resultado_set_e);
						?>
						<td class="peq">
							Sugerencia 1:
							<select class="normalNegro" id="prov_sug1" name="prov_sug1">
							<option value="">Seleccione</option>
							<?php
								$i = 0;
								$estaProveedor = false;
								while($i<$numeroFilas){
									$rowe=pg_fetch_array($resultado_set_e,$i);
									$prov_id=trim($rowe['prov_id_rif']);
									if($rebms_prov_sugerido1==$prov_id){
										$estaProveedor = true;
									}
									$prov_nombre=$rowe['prov_nombre'];
									echo "<option value= '".$prov_id."' ".(($rebms_prov_sugerido1==$prov_id)?" selected='selected'":"").">".$prov_nombre." (RIF ".strtoupper(substr(trim($prov_id),0,1))."-".substr(trim($prov_id),1).")</option>";
									$i++;
								}
							?>
							</select><br/>
							<div align="right">Otro: <input class="normal" type=text name="prov_sug1_otro" size="35" maxlength="80" onkeyup="validarTexto(this);" <?= (($estaProveedor==false)?"value='".$rebms_prov_sugerido1."'":"") ?>/></div>
							<br/>
							Sugerencia 2:
							<select class="normalNegro" id="prov_sug2" name="prov_sug2">
							<option value="">Seleccione</option> 
							<?php
								$i = 0;
								$estaProveedor = false;
								while($i<$numeroFilas){
									$rowe=pg_fetch_array($resultado_set_e,$i);
									$prov_id=trim($rowe['prov_id_rif']);
									if($rebms_prov_sugerido2==$prov_id){
										$estaProveedor = true;
									}
									$prov_nombre=$rowe['prov_nombre'];
									echo "<option value= '".$prov_id."' ".(($rebms_prov_sugerido2==$prov_id)?" selected='selected'":"").">".$prov_nombre." (RIF ".strtoupper(substr(trim($prov_id),0,1))."-".substr(trim($prov_id),1).")</option>";
									$i++;
								}
							?>
							</select>
							<div align="right">Otro: <input class="normal" type=text name="prov_sug2_otro" size="35" maxlength="80" onkeyup="validarTexto(this);" <?= (($estaProveedor==false)?"value='".$rebms_prov_sugerido2."'":"") ?>/></div>
							<br/>
							Sugerencia 3:
							<select class="normalNegro" id="prov_sug3" name="prov_sug3">
							<option value="">Seleccione</option> 
							<?php
								$i = 0;
								$estaProveedor = false;
								while($i<$numeroFilas){
									$rowe=pg_fetch_array($resultado_set_e,$i);
									$prov_id=trim($rowe['prov_id_rif']);
									if($rebms_prov_sugerido3==$prov_id){
										$estaProveedor = true;
									}
									$prov_nombre=$rowe['prov_nombre'];
									echo "<option value= '".$prov_id."' ".(($rebms_prov_sugerido3==$prov_id)?" selected='selected'":"").">".$prov_nombre." (RIF ".strtoupper(substr(trim($prov_id),0,1))."-".substr(trim($prov_id),1).")</option>";
									$i++;
								}
							?>
							</select>
							<div align="right">Otro: <input class="normal" type=text name="prov_sug3_otro" size="35" maxlength="80" onkeyup="validarTexto(this);" <?= (($estaProveedor==false)?"value='".$rebms_prov_sugerido3."'":"") ?>/></div>
							<br/>
						</td>
					</tr>
					<tr>
						<td>Caracter&iacute;sticas sugeridas para seleccionar proveedor</td>
						<td>
							<table>
								<tr>
									<td>Tiempo de entrega</td>
									<td>
										<select name="entrega" class="normalNegro">
											<option value="<?= NA?>" <?php if($rebms_tiempo_entrega_sugerida==NA) echo "selected='selected'"?>>N/A</option>
											<option value="<?= TIEMPO_ENTREGA_MENOR_7_DIAS?>" <?php if($rebms_tiempo_entrega_sugerida==TIEMPO_ENTREGA_MENOR_7_DIAS) echo "selected='selected'"?>>Menor a 7 D&iacute;as</option>
											<option value="<?= TIEMPO_ENTREGA_MENOR_2_SEMANAS?>" <?php if($rebms_tiempo_entrega_sugerida==TIEMPO_ENTREGA_MENOR_2_SEMANAS) echo "selected='selected'"?>>Menor a 2 Semanas</option>
											<option value="<?= TIEMPO_ENTREGA_MENOR_1_MES?>" <?php if($rebms_tiempo_entrega_sugerida==TIEMPO_ENTREGA_MENOR_1_MES) echo "selected='selected'"?>>Menor a 1 Mes</option>
											<option value="<?= TIEMPO_ENTREGA_MAYOR_1_MES?>" <?php if($rebms_tiempo_entrega_sugerida==TIEMPO_ENTREGA_MAYOR_1_MES) echo "selected='selected'"?>>Mayor a 1 Mes</option>
										</select>
									</td>
								</tr>
								<tr>
									<td>Garant&iacute;a</td>
									<td>
										<select name="garantia" class="normalNegro">
											<option value="<?= NA?>" <?php if($rebms_garantia_sugerida==NA) echo "selected='selected'"?>>N/A</option>
											<option value="<?= GARANTIA_SI?>" <?php if($rebms_garantia_sugerida==GARANTIA_SI) echo "selected='selected'"?>>Si</option>
											<option value="<?= GARANTIA_NO?>" <?php if($rebms_garantia_sugerida==GARANTIA_NO) echo "selected='selected'"?>>No</option>
										</select>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>Observaciones</td>
						<td>
							<textarea class="normalNegro" id="txt_observaciones" name="txt_observaciones" cols="85" rows="6"
								onkeydown="textCounter(this,'remLen',1000);"
								onkeyup="textCounter(this,'remLen',1000);validarTexto(this);"><?=$rebms_observaciones?></textarea>
							<div style="text-align: right;width: 100%"><input type="text" value="<?= (1000-strlen($rebms_observaciones))?>" class="normalNegro" maxlength="3" size="3" id="remLen" name="remLen" readonly="readonly"/></div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<br/>
	<div id="divAcciones" style="text-align: center;">
		<?php 
			$estadoBorrador = 60;
			if($esta_id==$estadoBorrador){
		?>
			<input class="normalNegro" type="button" value="Modificar" onclick="enviar('<?= ACCION_GUARDAR_REQUISICION_EN_BORRADOR?>');"/>
		<?php
			}
		?>
		<input class="normalNegro" type="button" value="Enviar" onclick="enviar('<?= ACCION_ENVIAR_REQUISICION?>');"/>
		<input class="normalNegro" type="button" value="Anular" onclick="anular();"/>
		<?php 
			  if ( $bandeja!="true" ) {
		?>
				<input type="button" class="normalNegro" value="Cancelar" onclick="irARequisiciones();"/>
		<?php } else { ?>
				<input type="button" class="normalNegro" value="Cancelar" onclick="bandeja();"/>
		<?php } ?>
	</div>
	<br/>
	<div class="ptotal"><span class="peq_naranja">(*)</span> Campo obligatorio</div>
</form>
<?php }?>
</body>
</html>
<?php pg_close($conexion); ?>