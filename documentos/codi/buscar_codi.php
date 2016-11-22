<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");

if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado"))  {
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();


$user_perfil_id = $_SESSION['user_perfil_id'];
$perfil = $_SESSION['user_perfil_id'];
$tipo=$_POST["tipo"];
$fecha_inicio=$_POST["txt_inicio"];
$fecha_fin=$_POST["txt_fin"];
$fi=$_POST["txt_fi"];
$ff=$_POST["txt_ff"];
$codigo=$_POST["txt_codi"];
$referencia=trim($_POST["txt_referencia"]);
$documento=trim(strtolower($_POST["txt_documento"]));
$detalle=strtolower($_POST["txt_detalle"]);
$analista=$_POST['analista'];
$cuenta=$_POST['cuenta'];
$pagina = "1";
if (isset($_POST['pagina']) && $_POST['pagina'] != "") {
	$pagina = $_POST['pagina'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar Codi</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css"/>
<script language="JavaScript" src="../../js/funciones.js"></script>
<script language="JavaScript" type="text/JavaScript">
var tipo = 0;
var resultado =0;

function buscar(pagina) {
	if ((document.form.rad_opcion[0].checked==false) && (document.form.rad_opcion[1].checked==false) && (document.form.rad_opcion[2].checked==false)  && (document.form.rad_opcion[3].checked==false) && (document.form.rad_opcion[4].checked==false) && (document.form.rad_opcion[5].checked==false))	{
		alert("Debe indicar el tipo de b"+uACUTE+"squeda");
		return;
	}else if (document.form.rad_opcion[0].checked==true) {
		tipo = 1;	
		if (document.form.txt_inicio.value.length<4 || document.form.txt_fin.value.length <4) {
		  alert("Debe seleccionar un rango de fecha de emisi"+oACUTE+"n del comprobante");
		  return;
		}	
		else resultado = 1;
	}else if (document.form.rad_opcion[1].checked==true) {
		tipo = 2;		
		if (document.form.txt_codi.value == "") {
		  alert("Debe indicar el c\u00F3digo del comprobante");
		  return;
		}
		else resultado = 1;				
	}else if (document.form.rad_opcion[2].checked==true) {
		tipo = 3;		
		if (document.form.txt_referencia.value == "") {
		  alert("Debe indicar el n"+uACUTE+"mero de referencia bancaria");
		  return;
		}		
		else resultado = 1;				
	}else if (document.form.rad_opcion[3].checked==true) {
		tipo = 4;			
		if (document.form.txt_documento.value == "") {
		  alert("Debe indicar el documento asociado al comprobante");
		  return;
		}
		else resultado = 1;	
	}else if (document.form.rad_opcion[4].checked==true) {
		tipo = 5;			
		if (document.form.txt_detalle.value == "") {
		  alert("Debe especificar un fragmento de la justificaci\u00F3n del comprobante");
		  return;
		}
		else resultado = 1;		
	}else if (document.form.rad_opcion[5].checked==true) {
		tipo = 6;			
		if (document.form.txt_fi.value.length<4 || document.form.txt_ff.value.length <4) {
		  alert("Debe seleccionar un rango de fecha de elaboraci"+oACUTE+"n del comprobante");
		  return;
		}
		else resultado = 1;		
	}

	if(trim(document.getElementById("cuentaActb").value)!=""){
		tokens = document.getElementById("cuentaActb").value.split( ":" );
		cuenta = (tokens[0])?trim(tokens[0]):"";
		document.getElementById("cuenta").value = cuenta;
	}
	
	if (resultado = 1) {
		document.form.pagina.value=pagina;
		document.form.action="buscar_codi.php";
		document.form.tipo.value=tipo;
		document.form.submit()
	}
}

function deshabilitar(valor) {
	//Si es fecha emision
	if (valor=='1')  {
		document.form.txt_inicio.disabled=false;
		document.form.txt_fin.disabled=false;	
		document.form.txt_codi.value=""; 
		document.form.txt_codi.disabled=true; 			
		document.form.txt_inicio.value="";
		document.form.txt_fin.value="";
		document.form.txt_referencia.value="";
		document.form.txt_referencia.disabled=true;
		document.form.txt_documento.value=""; 
		document.form.txt_documento.disabled=true; 
		document.form.txt_detalle.value=""; 
		document.form.txt_detalle.disabled=false;
		document.form.txt_fi.value="";
		document.form.txt_ff.value="";
		document.form.txt_fi.disabled=true;
		document.form.txt_ff.disabled=true;
		document.form.analista.disabled=false;	
		document.form.analista.value='';
		document.form.cuentaActb.value='';
		document.form.cuentaActb.disabled=false;
	}	
	//Por codi
	if (valor=='2') {
		document.form.txt_inicio.disabled=true;
		document.form.txt_fin.disabled=true;	
		document.form.txt_codi.value="codi-"; 
		document.form.txt_codi.disabled=false; 			
		document.form.txt_inicio.value="";
		document.form.txt_fin.value="";
		document.form.txt_referencia.value="";
		document.form.txt_referencia.disabled=true;
		document.form.txt_documento.value=""; 
		document.form.txt_documento.disabled=true; 
		document.form.txt_detalle.value=""; 
		document.form.txt_detalle.disabled=true; 
		document.form.txt_fi.value="";
		document.form.txt_ff.value="";
		document.form.txt_fi.disabled=true;
		document.form.txt_ff.disabled=true;
		document.form.analista.disabled=true;	
		document.form.analista.value='';
		document.form.cuentaActb.value='';
		document.form.cuentaActb.disabled=true;
	}	
	//Por referencia
	if (valor=='3') {
		document.form.txt_inicio.disabled=true;
		document.form.txt_fin.disabled=true;
		document.form.txt_codi.value=""; 
		document.form.txt_codi.disabled=true; 			
		document.form.txt_inicio.value="";
		document.form.txt_fin.value="";
		document.form.txt_referencia.value="";
		document.form.txt_referencia.disabled=false;
		document.form.txt_documento.value=""; 
		document.form.txt_documento.disabled=true; 
		document.form.txt_detalle.value=""; 
		document.form.txt_detalle.disabled=true; 	
		document.form.txt_fi.value="";
		document.form.txt_ff.value="";
		document.form.txt_fi.disabled=true;
		document.form.txt_ff.disabled=true;	
		document.form.analista.disabled=true;	
		document.form.analista.value='';
		document.form.cuentaActb.value='';
		document.form.cuentaActb.disabled=true;
	}	
	//Por documento
	if (valor=='4') {
		document.form.txt_inicio.disabled=true;
		document.form.txt_fin.disabled=true;
		document.form.txt_codi.value=""; 
		document.form.txt_codi.disabled=true; 			
		document.form.txt_inicio.value="";
		document.form.txt_fin.value="";
		document.form.txt_referencia.value="";
		document.form.txt_referencia.disabled=true;
		document.form.txt_documento.value=""; 
		document.form.txt_documento.disabled=false; 
		document.form.txt_detalle.value=""; 
		document.form.txt_detalle.disabled=true; 
		document.form.txt_fi.value="";
		document.form.txt_ff.value="";	
		document.form.txt_fi.disabled=true;
		document.form.txt_ff.disabled=true;	
		document.form.analista.disabled=true;	
		document.form.analista.value='';
		document.form.cuentaActb.value='';
		document.form.cuentaActb.disabled=true;
	}
	//Por detalle
	if (valor=='5') {
		document.form.txt_inicio.disabled=false;
		document.form.txt_fin.disabled=false;
		document.form.txt_codi.value=""; 
		document.form.txt_codi.disabled=true; 			
		document.form.txt_inicio.value="";
		document.form.txt_fin.value="";
		document.form.txt_referencia.value="";
		document.form.txt_referencia.disabled=true;
		document.form.txt_documento.value=""; 
		document.form.txt_documento.disabled=true; 
		document.form.txt_detalle.value=""; 
		document.form.txt_detalle.disabled=false; 	
		document.form.txt_fi.value="";
		document.form.txt_ff.value="";
		document.form.txt_fi.disabled=false;
		document.form.txt_ff.disabled=false;	
		document.form.analista.disabled=true;	
		document.form.analista.value='';
		document.form.cuentaActb.value='';
		document.form.cuentaActb.disabled=true;
	}
	//Fecha de Elaboración
	if (valor=='6') {
		document.form.txt_inicio.disabled=true;
		document.form.txt_fin.disabled=true;
		document.form.txt_codi.value=""; 
		document.form.txt_codi.disabled=true; 			
		document.form.txt_inicio.value="";
		document.form.txt_fin.value="";
		document.form.txt_referencia.value="";
		document.form.txt_referencia.disabled=true;
		document.form.txt_documento.value=""; 
		document.form.txt_documento.disabled=true; 
		document.form.txt_detalle.value=""; 
		document.form.txt_detalle.disabled=false; 	
		document.form.txt_fi.disabled=false;
		document.form.txt_ff.disabled=false;
		document.form.analista.disabled=false;	
		document.form.analista.value='';	
		document.form.cuentaActb.value='';
		document.form.cuentaActb.disabled=false;	
	}	
}

function anular(codigo) {
  /*contenido=prompt("Indique el motivo de la anulaci\u00F3n: ","");
  if ((contenido!=null) && (contenido!='')){
   	document.getElementById('contenido_memo').value=contenido;
   	if (confirm("Est\u00e1 seguro que desea ANULAR el codi "+codigo)) */
  	//  document.location.href = "codi_eanular.php?codigo="+codigo+"&motivo="+contenido;	
    document.location.href = "codi_anular.php?codigo="+codigo;
	/*}else{
 		  alert("Debe Indicar el motivo de la anulaci\u00F3n");
 		  return;
		 }*/
}

var codis=new Array();
var codisIndice = -1;

function compararFechas(elemento,fechaInicio, fechaFin){
	var fechaDesde=fechaInicio;
	var fechaHasta=fechaFin;
	
	var dia1 =fechaDesde.substring(0,2);
	var mes1 =fechaDesde.substring(3,5);
	var anio1=fechaDesde.substring(6,10);
	
	var dia2 =fechaHasta.substring(0,2);
	var mes2 =fechaHasta.substring(3,5);
	var anio2=fechaHasta.substring(6,10);

	dia1 = parseInt(dia1,10);
	mes1 = parseInt(mes1,10);
	anio1= parseInt(anio1,10);

	dia2 = parseInt(dia2,10);
	mes2 = parseInt(mes2,10);
	anio2= parseInt(anio2,10); 
		
	if( (anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) ||
		((anio1 == anio2) && (mes1==mes2) && (dia1>dia2)) ){
		alert("La fecha inicial no debe ser mayor a la fecha final");
		elemento.value='';
		return;
	}
}

function verificarCheckboxControl(){
	inputs = document.getElementsByTagName("input");
	todosMarcados = true;
	totalCheckboxs = 0;
	for(var i = 0; i < inputs.length; i++) {
		if(inputs[i].getAttribute("type")=="checkbox"){
			totalCheckboxs++;
			if(	strStartsWith(inputs[i].getAttribute("name"),"codis")==true
				&& inputs[i].checked == false){
				todosMarcados = false;
			}
		}
	}
	if(totalCheckboxs>1){
		checkboxControl = document.getElementById("controlCodis");
		checkboxControl.checked = todosMarcados;
	}
}

function marcarTodosNinguno(){
	checkbox = document.getElementById("controlCodis");
	inputs = document.getElementsByTagName("input");
	for(var i = 0; i < inputs.length; i++) {
		if(inputs[i].getAttribute("type")=="checkbox"
			&& strStartsWith(inputs[i].getAttribute("name"),"codis")==true){
			inputs[i].checked = checkbox.checked;
			agregarQuitarCodi(inputs[i]);
		}
	}
	verificarCheckboxControl();
}

function agregarQuitarCodi(elemento, manual){
	if(elemento.checked==true){
		if(existeCodi(elemento.value+"")==-1){
			codisIndice++;
			codis[codisIndice] = new String(elemento.value+'');
		}
	}else{
		codis[existeCodi(elemento.value+"")] = null;
		codisIndice--;
	}
	if(manual && manual==true){
		verificarCheckboxControl();
	}
}

function existeCodi(codi){
	i = 0;
	while(i<codis.length){
		if(codis[i]==codi){
			return i;	
		}
		i++;
	}
	return -1;
}

function imprimir(){
	cadenaCodis = "";
	for(i=0; i<codis.length; i++){
		if(codis[i]!=null){
			cadenaCodis += codis[i]+",";
		}
	}
	if(cadenaCodis!=""){
		cadenaCodis = cadenaCodis.substring(0,cadenaCodis.length - 1);
		location.href="codiMultiplePDF.php?codis="+cadenaCodis;
		return;
	}else{
		alert("Debe seleccionar al menos un comprobante diario para generar documento PDF.");
		return;
	}
}
</script>
</head>
<body class="normal">
<form name="form" action="" method="post">
<input type="hidden" id="pagina" name="pagina" value="<?= $pagina?>"/>
<table width="70%" align="center" class="tablaalertas">
	<tr class="td_gray">
		<td colspan="3" class="normalNegroNegrita">B&uacute;squeda de comprobante diario</td>
	</tr>
	<tr>
		<td><input id="rad_opcion1" name="rad_opcion" type="radio" value="1" onclick="javascript:deshabilitar(this.value)" <?php if(!$tipo || $tipo=="" || $tipo=="1"){echo 'checked="checked"';}?>/></td>
		<td class="normalNegrita">Por fecha de emisi&oacute;n:</td>
		<td>
			<?php $fecha_sistema= date("d")."/".date("m")."/".date("Y");?>
			<input
				type="text" size="10" id="txt_inicio" name="txt_inicio"
				class="dateparse" onfocus="javascript: compararFechas(this, this.value, document.getElementById('txt_fin').value);"
				readonly="readonly" value="<?= (((!$tipo || $tipo=="" || $tipo=="1"))?(($fecha_inicio && $fecha_inicio!="")?$fecha_inicio:$fecha_sistema):"")?>"/>
			<a href="javascript:void(0);" onclick="if(document.getElementById('rad_opcion1').checked==true){g_Calendar.show(event, 'txt_inicio');}" title="Show popup calendar">
				<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
			</a>
			<input
				type="text" size="10" id="txt_fin" name="txt_fin"
				class="dateparse" onfocus="javascript: compararFechas(this, document.getElementById('txt_inicio').value, this.value);"
				readonly="readonly"	value="<?= (((!$tipo || $tipo=="" || $tipo=="1"))?(($fecha_fin && $fecha_fin!="")?$fecha_fin:$fecha_sistema):"")?>"/>
			<a href="javascript:void(0);" onclick="if(document.getElementById('rad_opcion1').checked==true){g_Calendar.show(event, 'txt_fin');}" title="Show popup calendar">
				<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
			</a>
		</td>
	</tr>
	<tr>
		<td>	
			<input name="rad_opcion" type="radio" value="2"	onclick="javascript:deshabilitar(this.value)" <?php if($tipo=="2"){echo 'checked="checked"';}?>/>
		</td>
		<td class="normalNegrita">C&oacute;digo:</td>
		<td>
			<input name="txt_codi" type="text" class="normalNegro" id="txt_codi" value="<?= ($tipo=="2")?$codigo:""?>" size="20" disabled="disabled"/>
		</td>
	</tr>
	<tr>
		<td>
			<input name="rad_opcion" type="radio" value="3" onclick="javascript:deshabilitar(this.value)" <?php if($tipo=="3"){echo 'checked="checked"';}?>/>
		</td>
		<td class="normalNegrita">N&deg; Referencia bancaria:</td>
		<td>
			<input name="txt_referencia" type="text" class="normalNegro" id="txt_referencia" value="<?= ($tipo=="3")?$referencia:""?>" size="20" disabled="disabled"/>
		</td>
	</tr>
	<tr>
		<td>
			<input name="rad_opcion" type="radio" value="4" onclick="javascript:deshabilitar(this.value)" <?php if($tipo=="4"){echo 'checked="checked"';}?>/>
		</td>
		<td class="normalNegrita">Documento asociado:</td>
		<td>
			<input name="txt_documento" type="text" disabled="disabled"	class="normalNegro" id="txt_documento" value="<?= ($tipo=="4")?$documento:""?>" size="25" maxlength="20"/>
			<input name="contenido_memo" type="hidden" id="contenido_memo" value=""/>
		</td>
	</tr>
	<tr>
		<td>
			<input name="rad_opcion" type="radio" value="5" onclick="javascript:deshabilitar(this.value)" <?php if($tipo=="5"){echo 'checked="checked"';}?>/>
		</td>
		<td class="normalNegrita">Justificaci&oacute;n:</td>
		<td>
			<input name="txt_detalle" type="text" disabled="disabled" class="normalNegro" id="txt_detalle" value="<?= ($tipo=="5")?$detalle:""?>" size="25" maxlength="20"/>
		</td>
	</tr>
	<tr>
		<td>	
			<input id="rad_opcion6" name="rad_opcion" type="radio" value="6" onclick="javascript:deshabilitar(this.value)" <?php if($tipo=="6"){echo 'checked="checked"';}?>/>
		</td>
		<td class="normalNegrita">Por fecha de elaboraci&oacute;n:</td>
		<td>
			<input
				type="text" size="10" id="txt_fi" name="txt_fi" class="dateparse"
				onfocus="javascript: compararFechas(this, this.value, document.getElementById('txt_ff').value);" readonly="readonly"
				value="<?= (($tipo=="6")?(($fi && $fi!="")?$fi:$fecha_sistema):"")?>"/>
			<a href="javascript:void(0);" onclick="if(document.getElementById('rad_opcion6').checked==true){g_Calendar.show(event, 'txt_fi');}" title="Show popup calendar">
				<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
			</a>
			<input
				type="text" size="10" id="txt_ff" name="txt_ff" class="dateparse"
				onfocus="javascript: compararFechas(this, document.getElementById('txt_fi').value, this.value);" readonly="readonly"
				value="<?= (($tipo=="6")?(($ff && $ff!="")?$ff:$fecha_sistema):"")?>"/>
			<a href="javascript:void(0);" onclick="if(document.getElementById('rad_opcion6').checked==true){g_Calendar.show(event, 'txt_ff');}" title="Show popup calendar">
				<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
			</a>
		</td>
	</tr>
		<tr>
		<td>
		<!-- 	<input name="rad_opcion" type="radio" value="7" onclick="javascript:deshabilitar(this.value)" <?php if($tipo=="7"){echo 'checked="checked"';}?>/>-->
		</td> 
		<td class="normalNegrita">Analista:</td>
		<td>
		 <select name="analista" class="normalNegro" id="analista">
		<option value="">[Seleccione]</option>
		<?php
		$sql_e="SELECT empl_cedula as ci, initcap(empl_nombres)||' ' ||initcap(empl_apellidos) as analista
		FROM sai_empleado WHERE depe_cosige='452' and carg_fundacion='09' and esta_id=1"; 
		$resultado_set_e=pg_query($conexion,$sql_e) or die("Error al mostrar");
		while($rowe=pg_fetch_array($resultado_set_e)) 
		{ 
			$ci=trim($rowe['ci']);
			$nombre=$rowe['analista'];
			?>
			<option value= "<?php echo $ci;?>"><?php echo $nombre;?></option>
			<?php 
		} ?>
		</select>
		</td>
	</tr>
		<tr>
		<td>
		<!-- 	<input name="rad_opcion" type="radio" value="8" onclick="javascript:deshabilitar(this.value)" <?php if($tipo=="8"){echo 'checked="checked"';}?>/>-->
		</td>
		<td class="normalNegrita">Cuenta contable:</td>
		<td><span class="normal">
			<input type="hidden" value="" name="cuenta" id="cuenta" />
			<input autocomplete="off" size="70" type="text" id="cuentaActb" name="cuentaActb" value="<?= ($tipo=="8")?$cuenta:""?>" class="normal" />
			<?php
			$query = "SELECT scp.cpat_id, scp.cpat_nombre FROM sai_cue_pat scp WHERE cpat_nivel=7 ORDER BY scp.cpat_id";
			$resultado = pg_exec($conexion, $query);
			$numeroFilas = pg_num_rows($resultado);
			$arreglo = "";
			while($row=pg_fetch_array($resultado)) {
				$arreglo .= "'".$row["cpat_id"]." : ".str_replace("\n"," ",$row["cpat_nombre"])."',";
			}
			$arreglo = substr($arreglo, 0, -1);
			?>
			<script>
			var cuentasAMostrar = new Array(<?= $arreglo?>);
			actb(document.getElementById('cuentaActb'),cuentasAMostrar);
	</script>
</td>
		</td>
	</tr>
	<tr>
		<td colspan="3" align="center"><input type="hidden" value="<?= $tipo?>" name="tipo"/></td>
	</tr>
	<tr>
		<td height="30px" colspan="3" align="center">
			<input type="button" value="Buscar" onclick="javascript:buscar(1);" class="normalNegro"/>
		</td>
	</tr>
</table>
</form>
<br/>
<?
$condicion=0;
$condicion_analista='';
if ($analista<>'')
$condicion_analista=" AND d.usua_login='".$analista."' ";

$condicion_cuenta='';
if ($cuenta<>'')
$condicion_cuenta=" AND src.cpat_id='".$cuenta."' ";

$condicion_detalle='';
if ($detalle<>'')
 $condicion_detalle = " AND lower(c.comp_comen) LIKE '%".$detalle."%'";

if($tipo==1){
	$condicion_emision = " AND c.comp_fec BETWEEN to_date('".$fecha_inicio."', 'DD/MM/YYYY') AND to_date('".$fecha_fin."', 'DD/MM/YYYY') ";
	$condicion=1;
}else if($tipo==2){
	$condicion_codi = " AND c.comp_id = '".$codigo."'";
	$condicion=1;
}else if($tipo==3){
	$condicion_referencia = " AND trim(c.nro_referencia) like '%".$referencia."%'";
	$condicion=1;
}else if($tipo==4){
	$condicion_documento = " AND trim(c.comp_doc_id) LIKE '%".$documento."%'";
	$condicion=1;
}else if($tipo==5){
	$condicion_detalle = " AND lower(c.comp_comen) LIKE '%".$detalle."%'";
	$condicion=1;
}else if($tipo==6){
	$condicion_emision = " AND c.comp_fec_emis BETWEEN to_date('".$fi."', 'DD/MM/YYYY') AND to_date('".$ff."', 'DD/MM/YYYY')+1 ";
	$condicion=1;
}
else{
	$condicion_emision = " AND c.comp_fec BETWEEN to_date('".$fecha_sistema."', 'DD/MM/YYYY') AND to_date('".$fecha_sistema."', 'DD/MM/YYYY') ";
	$condicion=1;
}
if(	strlen($condicion_emision)>2 ||
	strlen($condicion_codi)>2 || 
	strlen($condicion_referencia)>2 ||
	strlen($condicion_documento)>2 
	//||strlen($condicion_detalle)>5
	){
	$sqlContador=	"SELECT ".
						"COUNT(DISTINCT(c.comp_id)) ".
					"FROM sai_comp_diario c, sai_doc_genera d, sai_empleado em ".
					"WHERE ".
						"c.comp_id = d.docg_id AND ".
						"d.usua_login = em.empl_cedula ".
						$condicion_emision.
						$condicion_codi.
						$condicion_referencia.
						$condicion_documento.
						$condicion_detalle.
						$condicion_analista." ";
				
	$tamanoPagina = 200;
	$tamanoVentana = 20;
	$desplazamiento = ($pagina-1)*$tamanoPagina;
		
	$sql=	"SELECT ".
				"distinct(c.comp_id) AS comp_id, ".
				"c.comp_doc_id AS comp_doc_id, ".
				"UPPER(c.comp_comen) AS comentario, ".
				"UPPER(em.empl_nombres)||' '||upper(em.empl_apellidos) AS usuario, ".
				"to_char(c.comp_fec, 'DD/MM/YYYY') AS fecha_emision, ".
				"c.nro_referencia AS nro_referencia, ".
				"CASE c.esta_id WHEN 15 THEN 'Anulado' ELSE 'Activo' END AS estado ".
			"FROM sai_comp_diario c, sai_doc_genera d, sai_empleado em, sai_reng_comp src ".
			"WHERE ".
				"c.comp_id = d.docg_id AND ".
				"src.comp_id=c.comp_id and src.comp_id=d.docg_id AND ".
				"d.usua_login = em.empl_cedula ".
				$condicion_emision.
				$condicion_codi.
				$condicion_referencia.
				$condicion_documento.
				$condicion_detalle.
				$condicion_cuenta.
				$condicion_analista." ".
		//	"ORDER BY c.comp_fec DESC, c.comp_id DESC ".
			"LIMIT ".$tamanoPagina." OFFSET ".$desplazamiento;
}
if($condicion==1){
	$resultadoContador = pg_exec($conexion, $sqlContador);
	$row = pg_fetch_array($resultadoContador, 0);
	$contador = $row[0];
	$totalPaginas = ($contador%$tamanoPagina == 0)?$contador/$tamanoPagina:intval($contador/$tamanoPagina)+1;
	$resultado=pg_query($conexion,$sql) or die("Error al mostrar la consulta de comprobantes diarios");
	$total_busq=pg_num_rows($resultado);
	if ($total_busq>0) {
?>
<table width="100%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr>
		<td colspan="8" class="normalNegroNegrita" align="center" valign="middle">
			Comprobante Diario
		</td>
		<td align="center">
			<a title="Generar archivo en formato PDF con los comprobantes seleccionados" href="javascript: imprimir();">
				<img border="0" src="../../imagenes/pdf_ico.jpg"/>
			</a>
		</td>
	</tr>
	<?php
	echo "<tr class='td_gray'><td colspan='9' align='center'>";
	$ventanaActual = ($pagina%$tamanoVentana==0)?$pagina/$tamanoVentana:intval($pagina/$tamanoVentana)+1;
	$i = (($ventanaActual-1)*$tamanoVentana)+1;
	while($i<=$ventanaActual*$tamanoVentana && $i<=$totalPaginas) {
		if($i==(($ventanaActual-1)*$tamanoVentana)+1 && $i!=1){
			echo "<a onclick='buscar(".($i-1).");' style='cursor: pointer;text-decoration: underline;'>&lt;</a> ";
		}
		if($i==$pagina){
			echo $i." ";
		}else{
			echo "<a onclick='buscar(".$i.");' style='cursor: pointer;text-decoration: underline;'>".$i."</a> ";
		}
		if($i==$ventanaActual*$tamanoVentana && $i<$totalPaginas){
			echo "<a onclick='buscar(".($i+1).");' style='cursor: pointer;text-decoration: underline;'>&gt;</a> ";
		}
		$i++;
	}
	echo "</td></tr>\n";
	?>
	<tr class="td_gray normalNegroNegrita">
		<td align="center">
			<input type="checkbox" id="controlCodis" name="controlCodis" onclick="marcarTodosNinguno();"/>
		</td>
		<td align="center">C&oacute;digo</td>
		<td align="center">N&deg; Referencia bancaria</td>
		<td align="center">Documento asociado</td>
		<td align="center">Justificaci&oacute;n</td>
		<td align="center">Fecha</td>
		<td align="center">Estado</td>
		<td align="center">Usuario</td>
		<td align="center">Opciones</td>
	</tr>
	<?php
		$i=$desplazamiento+1;
		while ($row=pg_fetch_array($resultado)) { 
	?>
	<tr class="normalNegro">
		<td align="center">
			<input type="checkbox" id="codis<?= $row['comp_id']?>" name="codis<?= $row['comp_id']?>" onclick='agregarQuitarCodi(this, true);' value="<?= $row['comp_id']?>"/>
		</td>
		<td class="link" align="center">
			<?= $i?>. <a href="javascript:abrir_ventana('codi_detalle.php?codigo=<?= $row["comp_id"]; ?>&amp;esta_id=<?= $row["estado"];?>')" class="copyright"><?= $row["comp_id"];?></a>
		</td>
		<td><?= $row["nro_referencia"] ;?></td>
		<td><?= $row["comp_doc_id"];?></td>
		<td><?= $row["comentario"];?></td>
		<td align="center"><?= $row["fecha_emision"];?></td>
		<td align="center"><?= $row["estado"];?></td>
		<td><?= $row["usuario"];?></td>
		<td align="center">
			<?php if (strcmp($row["estado"],'Activo')==0) {?>
				<a href="javascript:anular('<?= $row["comp_id"]; ?>');">Anular</a>
			<?php }?>
		</td>
	</tr>
	<?php
			$i++;
		}
		echo "<tr class='td_gray'><td colspan='9' align='center'>";
		$ventanaActual = ($pagina%$tamanoVentana==0)?$pagina/$tamanoVentana:intval($pagina/$tamanoVentana)+1;
		$i = (($ventanaActual-1)*$tamanoVentana)+1;
		while($i<=$ventanaActual*$tamanoVentana && $i<=$totalPaginas) {
			if($i==(($ventanaActual-1)*$tamanoVentana)+1 && $i!=1){
				echo "<a onclick='buscar(".($i-1).");' style='cursor: pointer;text-decoration: underline;'>&lt;</a> ";
			}
			if($i==$pagina){
				echo $i." ";
			}else{
				echo "<a onclick='buscar(".$i.");' style='cursor: pointer;text-decoration: underline;'>".$i."</a> ";
			}
			if($i==$ventanaActual*$tamanoVentana && $i<$totalPaginas){
				echo "<a onclick='buscar(".($i+1).");' style='cursor: pointer;text-decoration: underline;'>&gt;</a> ";
			}
			$i++;
		}
		echo "</td></tr>\n";
	?>
</table>
	<?}
else{?>
<table width="60%" border="0" align="center">
	<tr>
		<td>
			<div align="center" class="normalNegrita">No existen registros que cumplan con el criterio de b&uacute;squeda seleccionado</div>
			
		</td>
	</tr>
</table>
<?
}}
?>
</body>
</html>
<?php pg_close($conexion);?>