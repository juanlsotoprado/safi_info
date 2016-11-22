<?php 
/*ob_start();
session_start();*/
include(dirname(__FILE__) . '/../../init.php');
require_once("../../includes/conexion.php");
if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
       <style>
            body {
                font-family: verdana,helvetica,arial,sans-serif;
                font-size: 13px;
            }

            /* input box width */
            div.ei, input.ei { width: 300px; }
        </style>
<title>.:SAFI:Ingresar Proveedor</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<link rel="stylesheet" href="../../js/lib/jquery/plugins/emailInput/jquery.emailinput.min.css">
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script type="text/javascript" src="../../js/funciones.js"> </script>
<script type="text/javascript" src="../../js/crearModificarProveedor.js"> </script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>" charset="utf-8"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/maskedinput/jquery.maskedinput.js';?>" charset="utf-8"></script>
<script type="text/javascript" src="../../js/lib/jquery/plugins/emailInput/jquery.emailinput.min.js"></script>
<script>

function validarRIF(campoForm){
	campo = campoForm.value;
	var ubicacion = '';
	var caracteres = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	for (var i=0; i < campo.length; i++) {
		ubicacion = campo.substring(i, i + 1);
		if (caracteres.indexOf(ubicacion) == -1) {
	    	campoForm.value = campoForm.value.replace(ubicacion, "");
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

function limpiarFecha(fecha){
	fecha.value="";
}

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
		if (document.form.estado.value=='') {
			  alert("Debe colocar el estado");
			  document.form.estado.focus();
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
	if (document.form.estado.value=='') {
		  alert("Debe colocar el estado");
		  document.form.estado.focus();
		  return;
	}
	}
	if(confirm("Datos introducidos de manera correcta. Desea Continuar?."))   
	{
		document.form.submit();
	}
}

$().ready(function(){

	$(".asterisco2").hide()
	$( "#temporal1" ).click(function() {
		if($("#temporal").val()==0)
		{
			$("#temporal").val(1);
			$(".asterisco").hide();
			$(".asterisco2").show();
			
		}
		else
		{
			$("#temporal").val(0);
			$(".asterisco").show();
			$(".asterisco2").hide();
		}
			
	});

});
</script>
</head>
<body>
<form name="form" method="post" action="ingresarAccion.php" enctype="multipart/form-data" id="form1">
	<br/>
	<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" border= "0">
		<tr class="td_gray">
			<td colspan="4" class="normal">
				<span class="normalNegroNegrita">INGRESAR PROVEEDOR</span>
			</td>
		</tr>
		<tr>
			<td height="24" colspan="4" class="normalNegrita" align="center">Temporal
			<input type="checkbox" name="temporal1" id="temporal1"/>
			<input type="hidden" name="temporal" id="temporal" value="0"/>
			</td>
		</tr>
		<tr>
			<td height="24" colspan="4" class="peq_naranja" align="center">Los campos que tienen asterisco ( * ) son obligatorios </td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita">RIF: </td>
			<td> <input name="txt_rif" type="text" class="normal" id="txt_rif" value="" size="20" maxlength="20" onkeyup="validarRIF(this);"/><span class="peq_naranja">(*) Ej. g200077280</span></td>
			<td width="200" class="normalNegrita">Fecha vencimiento(Rif): </td>
			<td>
				<input type="text" size="10" name="txt_fecha_vencimiento3" id="txt_fecha_vencimiento3" class="dateparse" readonly="readonly"/>
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
		<script>
			jQuery(function($){
				$.mask.definitions['n'] = "[VEJGvejg]"; 
				$("#txt_rif").mask("n999999999");
			});
		</script>
		<tr>
			<td class="normalNegrita"> Raz&oacute;n social o Nombre : </td>
			<td class="peq_naranja"> <input name="txt_nombre" type="text" class="normal" id="txt_nombre" onkeyup="validarTexto(this);" size="30" maxlength="80"/><div class="asterisco" style="display: inline;">(*)</div></td>
			<td class="normalNegrita">Estado: </td>
			<td class="peq_naranja">
				<select id="estado" name="estado" class="normal">
					<option value="" selected="selected">[Seleccione]</option>
					<?php
					$sql="SELECT id, nombre FROM safi_edos_venezuela WHERE estatus_actividad = '1' ORDER BY nombre"; 
					$resultado=pg_query($conexion,$sql) or die("Error al mostrar los estados");
					while($row=pg_fetch_array($resultado)){ 
					?>
						<option value="<?= $row['id']?>"><?= $row['nombre']?></option>
					<?php 
					}
					?>
				</select>(*)
			</td>
		</tr>
		<tr class="normal">
			<td class="normalNegrita"> Tipo de Proveedor: </td>
			<td class="peq_naranja">
				<select name="cmb_tipo" class="normal" id="cmb_tipo">
					<option value="0" selected="selected">[Seleccione]</option>
					<?php
					$sql="SELECT prtp_id, prtp_nombre FROM sai_tipo_proveedor"; 
					$resultado=pg_query($conexion,$sql) or die("Error al mostrar el tipo de proveedor");
					while($row=pg_fetch_array($resultado)){ 
						$prtp_id=$row['prtp_id'];
						$prtp_nombre=$row['prtp_nombre'];
					?>
						<option value="<?=$prtp_id?>"><?php echo"$prtp_nombre";?></option>
					<?php 
					}
					?>
				</select><div class="asterisco" style="display: inline;">(*)</div>
			</td>
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
					?>
						<option value="<?=$prtp_id?>"><?php echo"$prtp_nombre";?></option>
					<?php 
					}
					?>
				</select><div class="asterisco" style="display: inline;">(*)</div>
			</td>
		</tr>
		<tr>
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
					?>
						<option value="<?=$prtp_id?>"><?php echo"$prtp_nombre";?></option>
					<?php 
					}
					?>
				</select><div class="asterisco" style="display: inline;">(*)</div>
			</td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr class="normal">
			<td class="normalNegrita"> Solvencia laboral: </td>
			<td class="peq_naranja"> 
				<select name="cmb_solvencia_laboral" class="normal" id="cmb_solvencia_laboral">
					<option value="0">[Seleccione]</option>
					<option value="Si">Si</option>
					<option value="No">No</option>
				</select><!-- (*) -->
			</td>
			<td class="normalNegrita">Fecha vencimiento(Solvencia Laboral): </td>
			<td>
				<input type="text" size="10" name="txt_fecha_vencimiento2" id="txt_fecha_vencimiento2" class="dateparse" readonly="readonly"/>
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
		<tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td class="normalNegrita">Ramos:</td>
			<?php /* ?>
			<td colspan="3" class="peq_naranja">
				<select name="ramo_secundario[]" multiple="multiple" class="normal">
				<?php
				$sql="SELECT * FROM sai_partida where pres_anno=".$_SESSION['an_o_presupuesto']." and  (part_id like '4.02%' or part_id like '4.03%' or part_id like '4.04%' or part_id like '4.07.12.01.01' or part_id like '4.07.01.02.01') and part_id not like '%.00.00' order by part_id";
				$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
				$selected="";
				$i=1;
				while($row=pg_fetch_array($resultado)){ 
					$part_id=$row['part_id'];
					$part_nombre=$row['part_nombre'];
					if(strcmp($i,1)==0) $selected="selected";
					else $selected="";
				?>
					<option value="<?=$part_id?>" <?=$selected?>><?php echo $part_id."-".$part_nombre;?></option>
				<?php 
					$i=$i+1;
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
			<td class="peq_na*ranja"> 
				<textarea name="txt_domicilio" cols="38" rows="3" class="normal" id="txt_domicilio" onkeyup="validarTexto(this);"></textarea><div class="asterisco" style="display: inline;">(*)</div>
			</td>
			<td class="normalNegrita">Direcci&oacute;n de dep&oacute;sito:</td>
			<td><textarea name="txt_deposito" cols="38" rows="3" class="normal" id="txt_deposito" onkeyup="validarTexto(this);"></textarea></td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita">Tel&eacute;fonos:</td>
			<td class="peq_naranja"> 
				<input name="txt_telefonos" type="text" class="normal" id="txt_telefonos" value="" size="40" maxlength="40"  onkeyup="validarTexto(this);"/><div class="asterisco" style="display: inline;">(*)</div>
			</td>
			<td class="normalNegrita">Fax:</td>
			<td>
				<input name="txt_fax" type="text" class="normal" id="txt_fax" size="15" maxlength="12"  onkeyup="validarTexto(this);"/>
			</td>
		</tr> 
		<tr class="normal">
			<td class="normalNegrita">C&oacute;digo Postal: </td>
			<td><input name="txt_postal" type="text" class="normal" id="txt_postal" onkeypress="return acceptNum(event);" value="" size="5" maxlength="5"/></td>
			<td class="normalNegrita"> Correo electr&oacute;nico:  </td>
			<td>
				<input name="txt_email" type="text" class="emailinput ei" id="txt_email" size="30" maxlength="50"/>
				<div class="asterisco2" style="display: inline;">(*)</div>
			</td>
			<script language="javascript" type="text/javascript">
        		$('.emailinput').emailinput({ onlyValidValue: true, delim: ',' }); // initialize
			</script>
		</tr>
		<tr class="normal"> <td class="normalNegrita"> P&aacute;gina Web: </td>
			<td> 
				<input name="txt_paginaweb" type="text" class="normal" id="txt_paginaweb" onkeyup="validarTexto(this);" size="50" maxlength="50"/>
			</td>
			<td class="normalNegrita"> Marcado: </td>
			<td class="peq_naranja"> 
				<select name="cmb_marcado" id="cmb_marcado" class="normal">
					<option value="0">[Seleccione]</option>
					<option value="Si">Si</option>
					<option value="No" selected="selected">No</option>
				</select><!--(*)-->
			</td>
		</tr>
		<tr><td height="40" colspan="4" class="normalNegroNegrita">Registro Nacional de Contratista (R.N.C)</td></tr>
		<tr>
			<td class="normalNegrita"> C&oacute;digo: </td>
			<td><input name="txt_codigo_rnc" type="text" class="normal" id="txt_codigo_rnc" onkeyup="validarTexto(this);" size="15" maxlength="30" /></td>
			<td class="normalNegrita"> Nivel de financiamiento: </td>
			<td> 
				<select name="cmb_nivel_financiamiento" class="normal" id="cmb_nivel_financiamiento">
					<option value="0">[Seleccione]</option>
					<?php
					$sql="SELECT id_nivel FROM sai_provee_nivel WHERE id_nivel <> '0' "; 
					$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
					while($row=pg_fetch_array($resultado)){ 
						$prtp_id=$row['id_nivel'];
					?>
						<option value="<?=$prtp_id?>"><?=$prtp_id?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr class="normal">
			<td class="normalNegrita"> Fecha inscripci&oacute;n:</td>
			<td>
				<input type="text" size="10" name="txt_fecha_inscripcion" id="txt_fecha_inscripcion" class="dateparse" readonly="readonly"/>
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
				<input type="text" size="10" name="txt_fecha_vencimiento" id="txt_fecha_vencimiento" class="dateparse" readonly="readonly"/>
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
		<tr>
			<td class="normalNegrita"> Actualizaci&oacute;n:</td>
			<td>
				<select name="cmb_actualizado_rnc" id="cmb_actualizado_rnc" class="normal">
				<option value="-">[Seleccione]</option>
				<option value="Si">Si</option>
				<option value="No">No</option>
				</select>
			</td>
			<td class="normalNegrita"> Suspendida: </td>
			<td>
				<select name="cmb_suspendida_rnc" class="normal" id="cmb_suspendida_rnc">
					<option value="0">[Seleccione]</option>
					<option value="Si">Si</option>
					<option value="No">No</option>
				</select>
			</td>
		</tr>
		<tr><td height="40" colspan="4" class="normalNegroNegrita">Representante(s) legal(es) (RL) y Contacto(s) (C)</td></tr>
		<tr class="normal"> 
			<td class="normalNegrita"> Nombre de los RL:</td>
			<td class="peq_naranja"> 
				<textarea name="txt_nombre_representante" cols="38" rows="3" class="normal" id="txt_nombre_representante" onkeyup="validarTexto(this);"></textarea><!--(*)--></td>
			<td class="normalNegrita"> CI de los RL:</td>
			<td height="23" colspan="2" class="peq_naranja"> 
			<textarea name="txt_ci_representante" cols="38" rows="3" class="normal" id="txt_ci_representante" onkeyup="validarCodigo(this);"></textarea><!--(*)--></td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita"> Tel&eacute;fonos de RL:</td>
			<td class="peq_naranja"> 
				<textarea name="txt_tel_representante" cols="38" rows="3" class="normal" id="txt_tel_representante" onkeyup="validarTexto(this);"></textarea><!--(*)-->
			</td>
			<td class="normalNegrita"> Correo electr&oacute;nico de los RL:</td>
			<td> 
				<textarea name="txt_email_representante" cols="38" rows="3" class="normal" id="txt_email_representante" onchange="validar_email2(txt_email_representante)"></textarea>
			</td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita"> Nombre de los C:</td>
			<td class="peq_naranja"> 
				<textarea name="txt_nombre_contacto" cols="38" rows="3" class="normal" id="txt_nombre_contacto" onkeyup="validarTexto(this);"></textarea><!--(*)-->
			</td>
			<td class="normalNegrita"> Tel&eacute;fonos de los C:</td>
			<td class="peq_naranja"> 
				<textarea name="txt_tel_contacto" cols="38" rows="3" class="normal" id="txt_tel_contacto" onkeyup="validarTexto(this);"></textarea><!--(*)-->
			</td>
		</tr>
		<tr>
			<td class="normalNegrita"> Correo electr&oacute;nico de los C:</td>
			<td colspan="2"> 
				<textarea name="txt_email_contacto" cols="38" rows="3" class="normal" id="txt_email_contacto" onchange="validar_email2(txt_email_contacto)"></textarea>
			</td>
		</tr>
		<tr><td height="40" colspan="4" class="normalNegroNegrita">Comentarios/Observaciones</td></tr>
		<tr>
			<td class="normalNegrita"> Comentarios</td>
			<td colspan="2"> 
				<textarea name="txt_comentario" cols="38" rows="3" class="normal" id="txt_comentario" onkeyup="validarTexto(this);"></textarea>
			</td>
		</tr>
		<tr>
			<td height="16" colspan="4">
				<div align="center">
				<input class="normalNegro" type="button" value="Ingresar" onclick="javascript:revisar();"/>		  
				</div>
			</td>
		</tr>
		<tr><td height="16" colspan="3">&nbsp;</td></tr>
	</table>
</form>
</body>
</html>
