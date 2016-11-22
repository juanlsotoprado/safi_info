<?php 
ob_start();
session_start();
include(dirname(__FILE__) . '/../../init.php');
require_once("../../includes/conexion.php");
require_once("../../includes/fechas.php");
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();
$prov_rif = "";
if (isset($_REQUEST['prov']) && $_REQUEST['prov'] != "") {
	$prov_rif = $_REQUEST['prov'];
}
$rif = "";
if (isset($_REQUEST['rif']) && $_REQUEST['rif'] != "") {
	$rif = $_REQUEST['rif'];
}
$codigo = "";
if (isset($_REQUEST['codigo']) && $_REQUEST['codigo'] != "") {
	$codigo = $_REQUEST['codigo'];
}
$nombre = "";
if (isset($_REQUEST['nombre']) && $_REQUEST['nombre'] != "") {
	$nombre = $_REQUEST['nombre'];
}
$estado = "";
if (isset($_REQUEST['estado']) && $_REQUEST['estado'] != "") {
	$estado = $_REQUEST['estado'];
}
$tipo = "";
if (isset($_REQUEST['tipo']) && $_REQUEST['tipo'] != "") {
	$tipo = $_REQUEST['tipo'];
}
$pagina = "1";
if (isset($_REQUEST['pagina']) && $_REQUEST['pagina'] != "") {
	$pagina = $_REQUEST['pagina'];
} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Modificar Proveedor</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css"/>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<link rel="stylesheet" href="../../js/lib/jquery/plugins/emailInput/jquery.emailinput.min.css">
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script language="JavaScript" src="../../js/funciones.js"></script>
<script type="text/javascript" src="../../js/crearModificarProveedor.js"> </script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>" charset="utf-8"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/maskedinput/jquery.maskedinput.js';?>" charset="utf-8"></script>
<script type="text/javascript" src="../../js/lib/jquery/plugins/emailInput/jquery.emailinput.min.js"></script>
<script>
function regresar(){
	rif = '<?= $rif?>';
	codigo = '<?= $codigo?>';
	nombre = '<?= $nombre?>';	
	estado = '<?= $estado?>';
	tipo = '<?= $tipo?>';
	pagina = '<?= $pagina?>';
	location.href = "buscar.php?rif="+rif+"&codigo="+codigo+"&nombre="+nombre+"&estado="+estado+"&tipo="+tipo+"&pagina="+pagina;
}

function limpiarFecha(fecha){
	fecha.value="";
}

function validar_digito(objeto){
	var checkOK = "ABCDEFGHIJKLMN\u00E1\u00e9\u00ed\u00f3\u00fa\u00c1 OPQRSTUVWXYZabcdefghijklmn\u00F1opqrstuvwxyz0123456789 ()-_/.;',&";
	var checkStr = objeto.value;
	var allValid = true;
	for (i = 0;  i < checkStr.length;  i++){
		ch = checkStr.charAt(i);
		for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
		if (j == checkOK.length){
			var cambio=checkStr.substring(-1,i) 
			objeto.value=cambio;
			alert("Escriba solo caracteres(no acentuados) y num\u00E9ricos, adem\u00E1s no debe contener el caracter '-' ");
			break;
		}
	}
}
/***********************************************************************************/
function validar_objeto(objeto){
	var checkOK = "ABCDEFGHIJKLMN\u00E1\u00e9\u00ed\u00f3\u00fa\u00c1 OPQRSTUVWXYZabcdefghijklmn\u00F1opqrstuvwxyz0123456789 ()-_/.;',&";
	var checkStr = objeto.value;
	var allValid = true;
	for (i = 0;  i < checkStr.length;  i++){
		ch = checkStr.charAt(i);
		for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
		if (j == checkOK.length){
			var cambio=checkStr.substring(-1,i) 
			objeto.value=cambio;
			alert("Estos caracteres no est\u00E1n permitidos");
			break;
		}
	}
}
//Obtene nombre del proveedor
function obtener_texto(){
	if(document.form.slc_provee.value != '0'){
		document.form.txt_texto.value=document.form.slc_provee.options[document.form.slc_provee.selectedIndex].text;
	}
}
//funcion que se utiliza para validar si el correo esta escrito de forma correcta
function validar_email(){
	if(document.form.txt_email.value!=''){
    	if(document.form.txt_email.value.search(/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/ig)){
			alert("La cuenta de correo no es v\u00E1lida, debes escribirla de forma: nombre@servidor.dominio");
			document.form.txt_email.value='';
			document.form.txt_email.focus();
			return;
		}
	}
}

function validar_email2(campo){
	if(campo.value!=''){
    	if(campo.value.search(/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/ig)){
			alert("La cuenta de correo no es v\u00E1lida, debes escribirla de forma: nombre@servidor.dominio");
			campo.value='';
			campo.focus();
			return;
		}
	}
}
// Validar que la placa no exista
function buscar_codigo(){ 
	var codigo;
	var codigo1;
	codigo=document.form.txt_rif.value;
	<?php
	$sql_p="SELECT prov_id_rif FROM sai_proveedor_nuevo"; 
	$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar el proveedor");
	while($row=pg_fetch_array($resultado_set_most_p)){
	?>
    	codigo1="<?php echo trim($row['prov_id_rif']); ?>"
		if (codigo==codigo1){
			alert("RIF ya existe en la base de datos...");
			document.form.txt_rif.value='';
		}
	<?php
	}
	?>
}
//Validar Campos
function revisar() {
	if($( "#temporal" ).val()==1)
	{
		alert("El proveedor es temporal");
		if (document.form.txt_rif.value=='') {
			alert("Debe colocar el RIF del proveedor");
			document.form.txt_rif.focus();
			return;
		}
		if (document.form.txt_nombre.value=='') {
			alert("Debe colocar el nombre del proveedor");
			document.form.txt_nombre.focus();
			return;
		}
		if (document.form.txt_email.value=='') {
			alert("Debe colocar el email");
			document.form.txt_email.focus();
			return;
		}
	}
	else
	{
	if (document.form.txt_rif.value=='') {
		alert("Debe colocar el RIF del proveedor");
		document.form.txt_rif.focus();
		return;
	}
	if (document.form.txt_nombre.value=='') {
		alert("Debe colocar el nombre del proveedor");
		document.form.txt_nombre.focus();
		return;
	}
	if (document.form.cmb_contribuyente.value=='') {
		  alert("Debe seleccionar el tipo de contribuyente");
		  document.form.cmb_contribuyente.focus();
		  return;
	}
	if (document.form.cmb_tipo_pers.value=='0') {
		  alert("Debe seleccionar el tipo de persona");
		  document.form.cmb_tipo_pers.focus();
		  return;
	}
	if (document.form.cmb_tipo.value=='0') {
		alert("Debe seleccionar un tipo de proveedor");
		document.form.cmb_tipo.focus();
		return;
	}
	if (document.form.txt_domicilio.value=='') {
		  alert("Debe colocar el domicilio fiscal");
		  document.form.txt_domicilio.focus();
		  return;
	}
	if (document.form.cmb_tipo.value!='8' && document.form.txt_telefonos.value=='') {//SI NO ES CONSEJO COMUNAL DEBE COLOCAR AL MENOS UN TELÉFONO
		  alert("Debe colocar al menos un telefono");
		  document.form.txt_telefonos.focus();
		  return;
	}
	}
	if(confirm("Datos introducidos de manera correcta. Desea continuar?")){
		document.form.submit();
   	}
}
$().ready(function(){
	//alert($("#temporal").val());
	$(".asterisco2").hide()
	$( "#temporal1" ).click(function() {
		if($("#temporal").val()==0)
		{
			$("#temporal").val(1);
			//alert($("#temporal").val());
			$(".asterisco").hide();
			$(".asterisco2").show();
			
		}
		else
		{
			$("#temporal").val(0);
			//alert($("#temporal").val());
			$(".asterisco").show();
			$(".asterisco2").hide();
		}
			
		//alert($("#temporal").val());
	});
});
</script>
</head>
<body>
<?php
$sql_p="SELECT *, to_char(prov_rnc_fecha_inscripcion, 'DD/MM/YYYY') as fechai,to_char(prov_rnc_fecha_vencimiento, 'DD/MM/YYYY') as fechav, to_char(fecha_venc_rif, 'DD/MM/YYYY') as fecha1,to_char(fecha_venc_solab, 'DD/MM/YYYY') as fecha2 FROM sai_proveedor_nuevo where lower(prov_id_rif) like '".strtolower($prov_rif)."'";
$resultado_set_most_p=pg_query($conexion,$sql_p);
if($row=pg_fetch_array($resultado_set_most_p)) {
$prov_codigo= trim($row['prov_codigo']);
$prov_nombre= trim($row['prov_nombre']);
$prov_id_rif= trim($row['prov_id_rif']);
$prov_tipo= trim($row['prov_prtp_id']);  
$tipo_pers= trim($row['prov_id_tp']);  
$solvencia_laboral= trim($row['prov_solvencia_laboral']);
$nivel_financiamiento= trim($row['prov_rnc_nivel_financ']);
$domicilio= trim($row['prov_domicilio']);
$deposito= trim($row['prov_deposito']);
$telefonos= trim($row['prov_telefonos']);
$fax= trim($row['prov_fax']);
$postal= trim($row['prov_codi_post']);
$email= trim($row['prov_email']);
$pagina_web= trim($row['prov_web']);
$marcado= trim($row['prov_marcado']);
$tipo_contribuyente= trim($row['prov_id_tc']);
$idEstado= trim($row['id_estado']);

$codigo_rnc= trim($row['prov_rnc_id']);
$fecha_inscripcion_rnc= trim($row['fechai']);
$fecha_vencimiento_rnc= trim($row['fechav']);
$actualizado_rnc= trim($row['prov_rnc_actualizada']);
$suspendida_rnc= trim($row['prov_rnc_suspendida']);

$nombre_representante= trim($row['prov_nombre_rl']);
$ci_representante= trim($row['prov_ci_rl']);
$tel_representante= trim($row['prov_tel_rl']);
$email_representante= trim($row['prov_email_rl']);

$nombre_contacto= trim($row['prov_nombre_c']);
$tel_contacto= trim($row['prov_tel_c']);
$email_contacto= trim($row['prov_email_c']);

$usua_login=$row['usua_login'];
$comentario=$row['prov_observaciones'];
$esta_id = $row['prov_esta_id'];

$fecha_venc_rif =$row['fecha1'];
$fecha_venc_solab=$row['fecha2'];
$temporal = $row['temporal'];

?>
<form name="form" method="post" action="modificarAccion.php" enctype="multipart/form-data" id="form1">
	<input type="hidden" id="rif" name="rif" value="<?= $rif?>"/>
	<input type="hidden" id="codigo" name="codigo" value="<?= $codigo?>"/>
	<input type="hidden" id="nombre" name="nombre" value="<?= $nombre?>"/>
	<input type="hidden" id="estado" name="estado" value="<?= $estado?>"/>
	<input type="hidden" id="tipo" name="tipo" value="<?= $tipo?>"/>
	<input type="hidden" id="pagina" name="pagina" value="<?= $pagina?>"/>
	<br/>
	<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td colspan="4" class="normal"><span class="normalNegroNegrita">MODIFICAR PROVEEDOR</span></td>
		</tr>
		<tr>
			<td height="24" colspan="4" class="normalNegrita" align="center">Temporal
			<input type="checkbox" name="temporal1" id="temporal1"  <?php echo $temporal == 1 ? 'checked="checked"' : "" ?> />
			<input type="hidden" name="temporal" id="temporal" value="<?= $temporal?>"/>
			</td>
		</tr>
		<tr>
			<td height="24" colspan="4" class="peq_naranja" align="center">Los campos que tienen asterisco ( * ) son obligatorios </td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita"> C&oacute;digo:</td>
			<td colspan="3"><?=$prov_codigo?></td>
		</tr>
		<tr class="normal">
			<td class="normalNegrita">RIF: </td>
			<td class="peq_naranja"><input name="txt_rif" type="text" class="normal" id="txt_rif" value="<?=$prov_id_rif?>" size="20" maxlength="20" readonly/>(*)</td>
			<td width="200" class="normalNegrita">Fecha vencimiento(Rif): </td>
			<td>
				<input type="text" size="10" name="txt_fecha_vencimiento3" id="txt_fecha_vencimiento3" class="dateparse" readonly="readonly" value="<?= $fecha_venc_rif;?>"/>
				<a href="javascript:void(0);" 
					onclick="g_Calendar.show(event, 'txt_fecha_vencimiento3');" 
					title="Show popup calendar">
					<img src="../../js/lib/calendarPopup/img/calendar.gif" 
						class="cp_img" 
						alt="Open popup calendar"/>
				</a>
				<div class="normal">
				<a href="javascript:limpiarFecha(document.form.txt_fecha_vencimiento3);">Borrar Fecha</a>
				</div>				
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">Raz&oacute;n social o Nombre :</td>
			<td class="peq_naranja"><input name="txt_nombre" type="text" class="normal" id="txt_nombre" onkeyup="validar_objeto(this)" size="30" maxlength="80" value="<?=$prov_nombre?>"/>(*)</td>
			<td class="normalNegrita"> Tipo persona: </td>
			<td class="peq_naranja">
				<select name="cmb_tipo_pers" id="cmb_tipo_pers" class="normal">
					<option value="0">[Seleccione]</option>
					<?php
				    $sql="SELECT * FROM sai_provee_tpers"; 
					$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
					while($row=pg_fetch_array($resultado)){ 
						$prtp_id=$row['ptpers_id'];
					    $prtp_nombre=$row['nombre'];
						if(strcmp($tipo_pers,$prtp_id)==0) $selected="selected";
					    else $selected="";
					?>
						<option value="<?=$prtp_id?>" <?=$selected?>><?php echo"$prtp_nombre";?></option>
					<?php 
					}
					?>
				</select><div class="asterisco" style="display: inline;">(*)</div>
			</td>
		</tr>
		<tr class="normal">
			<td class="normalNegrita"> Solvencia laboral: </td>
			<td>
				<select name="cmb_solvencia_laboral" class="normal" id="cmb_solvencia_laboral">
					<option value="0">[Seleccione]</option>
					<?
					$selecteds="";
					$selectedn="";
					if (strcmp($solvencia_laboral,"Si")==0) $selecteds="selected";
					else $selectedn="selected";
					?>
					<option <?=$selecteds?>>Si</option>
					<option <?=$selectedn?>>No</option>
				</select>
			</td>
			<td class="normalNegrita">Fecha vencimiento(Solvencia Laboral): </td>
			<td>
				<input type="text" size="10" name="txt_fecha_vencimiento2" id="txt_fecha_vencimiento2" class="dateparse" readonly="readonly" value="<?= $fecha_venc_solab;?>"/>
				<a href="javascript:void(0);" 
					onclick="g_Calendar.show(event, 'txt_fecha_vencimiento2');" 
					title="Show popup calendar">
					<img src="../../js/lib/calendarPopup/img/calendar.gif" 
						class="cp_img" 
						alt="Open popup calendar"/>
				</a>
				<div class="normal">
				<a href="javascript:limpiarFecha(document.form.txt_fecha_vencimiento2);">Borrar Fecha</a>
				</div>				
			</td>
		</tr>
		<tr class="normal">
			<td class="normalNegrita">Estado: </td>
			<td class="peq_naranja">
				<select id="estado" name="estado" class="normal">
					<option value="">[Seleccione]</option>
					<?php
					$sql="SELECT id, nombre FROM safi_edos_venezuela WHERE estatus_actividad = '1' ORDER BY nombre"; 
					$resultado=pg_query($conexion,$sql) or die("Error al mostrar los estados");
					while($row=pg_fetch_array($resultado)){ 
					?>
						<option value="<?= $row['id']?>" <?= (($idEstado == $row['id'])?"selected='selected'":"")?>><?= $row['nombre']?></option>
					<?php 
					}
					?>
				</select>
			</td>
			<td class="normalNegrita"> Tipo contribuyente: </td>
			<td class="peq_naranja">
				<select name="cmb_contribuyente" id="cmb_contribuyente" class="normal">
					<option value="">[Seleccione]</option>
					<?php
					$sql="SELECT * FROM sai_provee_tc"; 
					$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
					while($row=pg_fetch_array($resultado)){ 
						$prtp_id=$row['id_tc'];
						$prtp_nombre=$row['descripcion'];
						if (strcmp($tipo_contribuyente,$prtp_id)==0) $selected="selected";
						else $selected="";
					?>
						<option value="<?=$prtp_id?>" <?=$selected?>><?= "$prtp_nombre";?></option>
					<?php 
					}
					?>
				</select><div class="asterisco" style="display: inline;">(*)</div>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita"> Tipo de Proveedor: </td>
			<td class="peq_naranja" colspan="3">
				<select name="cmb_tipo" class="normal" id="cmb_tipo">
					<option value="0" selected>[Seleccione]</option>
					<?php
					$sql="SELECT prtp_id, prtp_nombre FROM sai_tipo_proveedor"; 
					$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
					while($row=pg_fetch_array($resultado)){ 
						$prtp_id=$row['prtp_id'];
						if(strcmp($prov_tipo,$prtp_id)==0) $selected="selected";
						else $selected="";
						$prtp_nombre=$row['prtp_nombre'];
					?>
						<option value="<?=$prtp_id?>" <?=$selected?>><?php echo "$prtp_nombre";?></option>
					<?php 
					}
					?>
				</select><div class="asterisco" style="display: inline;">(*)</div>
			</td>
		</tr>
		<tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td class="normalNegrita">Ramo(s):</td>
			<?php /*?>
			<td colspan="3" class="peq_naranja">
				<select name="ramo_secundario[]" multiple="multiple" class="normal">
				<?php
				$id_ramo="";
				$sql="select * from sai_prov_ramo_secundario where lower(prov_id_rif) like '".strtolower($prov_id_rif)."'"; 
				$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
				while($row=pg_fetch_array($resultado)){ 
					$id_ramo=$id_ramo.",".trim($row['id_ramo']);
				}
				$sql="SELECT * FROM sai_partida where pres_anno=".$_SESSION['an_o_presupuesto']." and (part_id like '4.02%' or part_id like '4.03%' or part_id like '4.04%' or part_id like '4.07.12.01.01' or part_id like '4.07.01.02.01') and part_id not like '%.00.00' order by part_id"; 
				$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
				while($row=pg_fetch_array($resultado)){ 
   	     			$part_id=$row['part_id'];
					if (stripos($id_ramo,$part_id)) $selected="selected";
					else $selected="";
	     			$part_nombre=$row['part_nombre'];
				?>
					<option value="<?=$part_id?>" <?=$selected?>><?php echo $part_id."-".$part_nombre;?></option>
				<?php 
				}
				?>
				</select>
			</td>
			<?php */?>
			<td colspan="3" class="peq_naranja">
				&nbsp;Rubro: <input autocomplete="off" size="68" type="text" id="partida" name="partida" class="normalNegro"/>
				<input type="button" onclick="agregarPartida();" class="normal" value="Agregar Partida"/>
				<br/>
				&nbsp;Introduzca el nombre del rubro o una palabra contenida en la descripci&oacute;n del mismo.
				<?php
				$query = 	"SELECT 
								sp.part_id,
								sp.part_nombre,  
								UPPER(si.nombre) as rubro 
							FROM 
								sai_item si
								INNER JOIN sai_item_partida sip ON (si.id = sip.id_item)
								INNER JOIN sai_partida sp ON (sip.part_id = sp.part_id)
							WHERE 
								sp.esta_id <> 15 AND 
								sp.pres_anno=".$_SESSION['an_o_presupuesto']." AND 
								(
									sp.part_id LIKE '4.01%' OR
									sp.part_id LIKE '4.02%' OR 
									sp.part_id LIKE '4.03%' OR 
									sp.part_id LIKE '4.04%' OR 
									sp.part_id LIKE '4.07.12.01.01' OR 
									sp.part_id LIKE '4.07.01.02.01'
								) AND 
								sp.part_id NOT LIKE '%.00.00' 
							ORDER BY sp.part_id, si.nombre ";
				
				$resultado = pg_exec($conexion, $query);
				$partidasAMostrar = "";
				$nombresPartidas = "";
				while($row=pg_fetch_array($resultado)){
					$partidasAMostrar .= "'".$row["part_id"]." : ".str_replace("\n"," ",$row["rubro"])."',";
					$nombresPartidas .= "'".str_replace("\n"," ",$row["part_nombre"])."',";
				}
				$partidasAMostrar = substr($partidasAMostrar, 0, -1);
				$nombresPartidas = substr($nombresPartidas, 0, -1);
				
				$query =	"SELECT 
								sp.part_id, 
								sp.part_nombre
							FROM 
								sai_partida sp 
								INNER JOIN sai_prov_ramo_secundario sprs ON (sp.part_id = sprs.id_ramo)
							WHERE 
								LOWER(sprs.prov_id_rif) LIKE '".strtolower($prov_id_rif)."'
							GROUP BY 
								sp.part_id, 
								sp.part_nombre
							ORDER BY sp.part_id";
				$resultado = pg_exec($conexion, $query);
				?>
				<br/>
				<br/>
				<table border="1" cellpadding="4" cellspacing="0" id="tablaPartidas">
					<tr>
						<td class="normalNegrita">Partida</td>
						<td class="normalNegrita">Denominaci&oacute;n</td>
						<td class="normalNegrita">Acci&oacute;n</td>
					</tr>
					<tbody id="bodyPartidas">
						<?php 
						$i = 0;
						while($row=pg_fetch_array($resultado)){
						?>
							<tr>
								<td><input type="hidden" name="ramo_secundario[]" value="<?= $row["part_id"]?>" /><?= $row["part_id"]?></td>
								<td><?= $row["part_nombre"]?></td>
								<td>
									<a href="javascript:eliminarPartida(<?= ($i+1)?>)">Eliminar</a>
									<script>
										var registro = new Array(2);
										registro[0]='<?= $row["part_id"]?>';
										registro[1]='<?= $row["part_nombre"]?>';
										partidas[partidas.length]=registro;
									</script>
								</td>
							</tr>
						<?php 
							$i++;
						}
						?>
					</tbody>
				</table>
				<script>
					var partidasAMostrar = new Array(<?= $partidasAMostrar?>);
					var nombresPartidas = new Array(<?= $nombresPartidas?>);
					actb(document.getElementById('partida'),partidasAMostrar);
				</script>
			</td>
		</tr>
		<tr><td colspan="4">&nbsp;</td></tr>
		<tr class="normal"> 
			<td class="normalNegrita"> Domicilio fiscal:</td>
			<td class="peq_naranja">
				<textarea name="txt_domicilio" cols="38" rows="3" class="normal" id="txt_domicilio" onkeyup="validar_objeto(txt_domicilio)"><?=$domicilio?></textarea><div class="asterisco" style="display: inline;">(*)</div>
			</td>
			<td class="normalNegrita">Direcci&oacute;n de dep&oacute;sito:</td>
			<td> <textarea name="txt_deposito" cols="38" rows="3" class="normal" id="txt_deposito" onkeyup="validar_objeto(txt_deposito)"><?=$deposito?></textarea></td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita">Tel&eacute;fonos:</td>
			<td class="peq_naranja"> 
				<input name="txt_telefonos" type="text" class="normal" id="txt_telefonos" value="<?=$telefonos?>" size="40" maxlength="40"  onkeyup="validar_objeto(txt_telefonos)"/>(*)</td>
			<td class="normalNegrita">Fax:</td>
			<td>
				<input name="txt_fax" type="text" class="normal" id="txt_fax" size="15" maxlength="12"  onkeyup="validar_objeto(txt_fax)" value="<?=$fax?>"/>
			</td>
		</tr>
		<tr class="normal">
			<td class="normalNegrita">C&oacute;digo Postal:</td>
			<td><input name="txt_postal" type="text" class="normal" id="txt_postal" onkeypress="return acceptNum(event);" value="<?=$postal?>" size="5" maxlength="5"/></td>
			<td class="normalNegrita"> Correo electr&oacute;nico: </td>
			<td>
				<input name="txt_email" type="text" class="emailinput ei" id="txt_email" size="30" maxlength="50" value="<?=$email?>"/>
				<div class="asterisco2" style="display: inline;">(*)</div>
			</td>
			<script language="javascript" type="text/javascript">
        		$('.emailinput').emailinput({ onlyValidValue: true, delim: ',' }); // initialize
			</script>
		</tr>
		<tr class="normal">
			<td class="normalNegrita"> P&aacute;gina Web: </td>
			<td>
				<input name="txt_paginaweb" type="text" class="normal" id="txt_paginaweb" onkeyup="return validar_objeto(txt_paginaweb,1)" size="50" maxlength="50" value="<?=$pagina_web?>"/>
			</td>
			<td class="normalNegrita"> Marcado: </td>
			<td> 
				<select name="cmb_marcado" id="cmb_marcado" class="normal">
					<option value="0">[Seleccione]</option>
					<?
					$selecteds="";
					$selectedn="";
					if (strcmp($marcado,"Si")==0) $selecteds="selected";
					else $selectedn="selected";
					?>
					<option <?=$selecteds?>>Si</option>
					<option <?=$selectedn?>>No</option>
				</select>
			</td>
		</tr>
		<tr>
			<td height="40" colspan="4" class="normalNegroNegrita">Registro Nacional de Contratista (R.N.C)</td>
		</tr>
		<tr>
			<td class="normalNegrita"> C&oacute;digo: </td>
			<td><input name="txt_codigo_rnc" type="text" class="normal" id="txt_codigo_rnc" onkeyup="validar_objeto(txt_registro)" size="15" maxlength="30" value="<?=$codigo_rnc?>"/></td>
			<td class="normalNegrita"> Nivel de financiamiento: </td>
			<td colspan="3"> 
				<select name="cmb_nivel_financiamiento" class="normal" id="cmb_nivel_financiamiento">
					<option value="0" <?php if($nivel_id=="0"){echo "selected='selected'";}?>>[Seleccione]</option>
					<?php
					$sql="SELECT id_nivel FROM sai_provee_nivel WHERE id_nivel <> '0'"; 
					$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
					while($row=pg_fetch_array($resultado)){ 
						$nivel_id=$row['id_nivel'];
					?>
						<option value="<?=$nivel_id?>" <?=(strcmp($nivel_financiamiento,$nivel_id)==0)?"selected='selected'":""?>><?= $nivel_id;?></option>
					<?php 
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita"> Fecha inscripci&oacute;n:</td>
			<td>
				<input type="text" size="10" name="txt_fecha_inscripcion" id="txt_fecha_inscripcion" class="dateparse" readonly="readonly" value="<?= $fecha_inscripcion_rnc;?>"/>
				<a href="javascript:void(0);" 
					onclick="g_Calendar.show(event, 'txt_fecha_inscripcion');" 
					title="Show popup calendar">
					<img src="../../js/lib/calendarPopup/img/calendar.gif" 
						class="cp_img" 
						alt="Open popup calendar"/>
				</a>
				<div class="normal">
				<a href="javascript:limpiarFecha(document.form.txt_fecha_inscripcion);">Borrar Fecha</a>
				</div>
			</td>
			<td class="normalNegrita"> Fecha vencimiento:</td>
			<td>
				<input type="text" size="10" name="txt_fecha_vencimiento" id="txt_fecha_vencimiento" class="dateparse" readonly="readonly" value="<?= $fecha_vencimiento_rnc;?>"/>
				<a href="javascript:void(0);" 
					onclick="g_Calendar.show(event, 'txt_fecha_vencimiento');" 
					title="Show popup calendar">
					<img src="../../js/lib/calendarPopup/img/calendar.gif" 
						class="cp_img" 
						alt="Open popup calendar"/>
				</a>
				<div class="normal">
				<a href="javascript:limpiarFecha(document.form.txt_fecha_vencimiento);">Borrar Fecha</a>
				</div>
			</td>
		</tr>
		<tr class="normal">
			<td class="normalNegrita"> Actualizaci&oacute;n:</td>
			<td> 
				<select name="cmb_actualizado_rnc" id="cmb_actualizado_rnc" class="normal">
					<option value="0">[Seleccione]</option>
						<?
						$selecteds="";
						$selectedn="";
						if (strcmp($actualizado_rnc,"Si")==0) $selecteds="selected";
						else $selectedn="selected";
						?>
					<option <?=$selecteds?>>Si</option>
					<option <?=$selectedn?>>No</option>
				</select>
			</td>
			<td class="normalNegrita">Suspendida:</td>
			<td>
				<select name="cmb_suspendida_rnc" id="cmb_suspendida_rnc" class="normal">
					<option value="0">[Seleccione]</option>
					<?
					$selecteds="";
					$selectedn="";
					if (strcmp($suspendida_rnc,"Si")==0) $selecteds="selected";
					else $selectedn="selected";
					?>
					<option <?=$selecteds?>>Si</option>
					<option <?=$selectedn?>>No</option>
				</select>
			</td>
		</tr>
		<tr><td height="40" colspan="4" class="normalNegroNegrita">Representante(s) legal(es) (RL) y Contacto(s) (C)</td></tr>
		<tr class="normal"> 
			<td class="normalNegrita"> Nombre de los RL:</td>
			<td> 
				<textarea name="txt_nombre_representante" cols="38" rows="3" class="normal" id="txt_nombre_representante" onkeyup="validar_objeto(txt_txt_nombre_representante)"><?=$nombre_representante?></textarea><!-- (*) -->
			</td>
			<td class="normalNegrita"> CI de los RL:</td>
			<td height="23" colspan="2"> 
				<textarea name="txt_ci_representante" cols="38" rows="3" class="normal" id="txt_ci_representante" onkeyup="validar_objeto(txt_ci_representante)"><?=$ci_representante?></textarea> <!-- (*) -->
			</td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita"> Tel&eacute;fonos de RL:</td>
			<td> 
				<textarea name="txt_tel_representante" cols="38" rows="3" class="normal" id="txt_tel_representante" onkeyup="validar_objeto(txt_tel_representante)"><?=$tel_representante?></textarea><!-- (*) -->
			</td>
			<td class="normalNegrita"> Correo electr&oacute;nico de los RL:</td>
			<td> 
				<textarea name="txt_email_representante" cols="38" rows="3" class="normal" id="txt_email_representante" onchange="validar_email2(txt_email_representante)"><?=$email_representante?></textarea>
			</td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita"> Nombre de los C:</td>
			<td> 
				<textarea name="txt_nombre_contacto" cols="38" rows="3" class="normal" id="txt_nombre_contacto" onkeyup="validar_objeto(txt_nombre_contacto)"><?=$nombre_contacto?></textarea><!-- (*) -->
			</td>
			<td class="normalNegrita"> Tel&eacute;fonos de los C:</td>
			<td> 
				<textarea name="txt_tel_contacto" cols="38" rows="3" class="normal" id="txt_tel_contacto" onkeyup="validar_objeto(txt_tel_contacto)"><?=$tel_contacto?></textarea><!-- (*) -->
			</td>
		</tr>
		<tr>
			<td class="normalNegrita"> Correo electr&oacute;nico de los C:</td>
			<td colspan="2"> 
				<textarea name="txt_email_contacto" cols="38" rows="3" class="normal" id="txt_email_contacto" onchange="validar_email2(txt_email_contacto)"><?=$email_contacto?></textarea>
			</td>
		</tr>
		<tr><td height="40" colspan="4" class="normalNegroNegrita">Comentarios/Observaciones</td></tr>
		<tr>
			<td class="normalNegrita"> Comentarios</td>
			<td colspan="2"> 
				<textarea name="txt_comentario" cols="38" rows="3" class="normal" id="txt_comentario" onkeyup="validar_objeto(txt_comentario)"><?=$comentario?></textarea>
			</td>
		</tr>
		<tr>
			<td height="16" colspan="4">
				<div align="center">
				<input class="normalNegro" type="button" value="Modificar" onclick="javascript:revisar();"/>		  
				</div>
			</td>
		</tr>
		<tr><td height="16" colspan="3">&nbsp;</td></tr>
	</table>
<?}?>
</form>
</body>
</html>