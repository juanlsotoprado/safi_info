<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");
if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	pg_close($conexion);
	ob_end_flush();
	exit;
}
ob_end_flush();

$pagina = "1";

if ($_REQUEST['pagina'] && $_REQUEST['pagina'] != "") {
	$pagina = $_REQUEST['pagina'];
}
$tamanoPagina = 150;
$tamanoVentana = 40;
$desplazamiento = ($pagina-1)*$tamanoPagina;

$control = $_REQUEST['control'];
$fechaDesde = $_REQUEST['fechaDesde'];
$fechaHasta = $_REQUEST['fechaHasta'];
$codDoc = $_REQUEST['cod_doc'];

if(!$control || $control==""){
	$control = "1";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>.:SAFI:Contabilidad por Documento</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script>
	var codas=new Array();
	var codasIndice = -1;
    var esIE=(document.all);
    var esNS=(document.layers);

	function iniciar() {
		document.getElementById("cod_doc").focus();	
	}

	function enter(e) {
		       tecla=(esIE) ? event.keyCode : e.which;
		       if(tecla==13){
		               ejecutar();
				}
		
	} 
	
	function ejecutar(){
		controlFechas=document.getElementById("controlFechas");
		controlCodigo=document.getElementById("controlCodigo");
		fechaDesde=document.getElementById("fechaDesde").value;
		fechaHasta=document.getElementById("fechaHasta").value;
		codigo=document.getElementById("cod_doc").value;
		if(controlFechas.checked==true){
			if(fechaDesde=="" || fechaHasta==""){
				alert("Debe indicar el inicio y el final del per"+iACUTE+"odo.");
				return;
			}
		}else if(controlCodigo.checked==true){
			if(codigo==""){
				alert("Debe indicar el c"+oACUTE+"digo de un documento.");
				return;
			}
		}
		document.getElementById("form").submit();
	}
	
	function compararFechas(elemento){
		var fechaDesde=document.getElementById("fechaDesde").value;
		var fechaHasta=document.getElementById("fechaHasta").value;
		
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

	function habiDesabiOpciones(){
		controlFechas = document.getElementById("controlFechas");
		controlCodigo = document.getElementById("controlCodigo");
		fechaDesde = document.getElementById("fechaDesde");
		fechaHasta = document.getElementById("fechaHasta");
		codDoc = document.getElementById("cod_doc");

		if (controlFechas.checked==true){ 
			fechaDesde.disabled=false;
			fechaHasta.disabled=false;
			codDoc.disabled=true;
		}else{ 
			fechaDesde.disabled=true;
			fechaHasta.disabled=true;
			codDoc.disabled=false; 
		}
	}

	function verificarCheckboxControl(){
		inputs = document.getElementsByTagName("input");
		todosMarcados = true;
		totalCheckboxs = 0;
		for(var i = 0; i < inputs.length; i++) {
			if(inputs[i].getAttribute("type")=="checkbox"){
				totalCheckboxs++;
				if(	strStartsWith(inputs[i].getAttribute("name"),"codas")==true
					&& inputs[i].checked == false){
					todosMarcados = false;
				}
			}
		}
		if(totalCheckboxs>1){
			checkboxControl = document.getElementById("controlCodas");
			checkboxControl.checked = todosMarcados;
		}
	}
	
	function marcarTodosNinguno(){
		checkbox = document.getElementById("controlCodas");
		inputs = document.getElementsByTagName("input");
		for(var i = 0; i < inputs.length; i++) {
			if(inputs[i].getAttribute("type")=="checkbox"
				&& strStartsWith(inputs[i].getAttribute("name"),"codas")==true){
				inputs[i].checked = checkbox.checked;
				agregarQuitarCoda(inputs[i]);
			}
		}
		verificarCheckboxControl();
	}

	function agregarQuitarCoda(elemento, manual){
		if(elemento.checked==true){
			if(existeCoda(elemento.value+"")==-1){
				codasIndice++;
				codas[codasIndice] = new String(elemento.value+'');
			}
		}else{
			codas[existeCoda(elemento.value+"")] = null;
			codasIndice--;
		}
		if(manual && manual==true){
			verificarCheckboxControl();
		}
	}
	
	function existeCoda(coda){
		i = 0;
		while(i<codas.length){
			if(codas[i]==coda){
				return i;	
			}
			i++;
		}
		return -1;
	}

	function imprimir(){
		cadenaCodas = "";
		for(i=0; i<codas.length; i++){
			if(codas[i]!=null){
				cadenaCodas += codas[i]+",";
			}
		}
		if(cadenaCodas!=""){
			cadenaCodas = cadenaCodas.substring(0,cadenaCodas.length - 1);
			iniciarPDFLoading();
			//location.href="codaMultiplePDF.php?codas="+cadenaCodas;
			document.getElementById("codas").value = cadenaCodas;
			document.getElementById("formPDF").submit();
			return;
		}else{
			alert("Debe marcar al menos un (1) sopg para imprimir.");
			return;
		}
	}

	var pararTimer = false;
	var nivel = 3;
	var timerID;
	var pararTimerID;
	var direccion=0;
	function iniciarPDFLoading(){
		pararTimer = false;
		timerID = setTimeout("correrTimer();",300);
		document.getElementById("loadingPdf").style.display="block";
		pararTimerID = setTimeout("pararPDFLoading()",5000);
	}

	function correrTimer(){
		if(nivel==3){
			direccion=0;
			nivel=2;
			document.getElementById("loadingPdf").innerHTML="Por favor espere,<br/> se est&aacute; generando su PDF...";
		}else if(nivel==2){
			if(direccion==1){
				nivel=3;
			}else{
				nivel=1;
			}
			document.getElementById("loadingPdf").innerHTML="Por favor espere,<br/> se est&aacute; generando su PDF..";
		}else if(nivel==1){
			if(direccion==1){
				nivel=2;
			}else{
				nivel=0;
			}
			document.getElementById("loadingPdf").innerHTML="Por favor espere,<br/> se est&aacute; generando su PDF.";
		}else if(nivel==0){
			direccion=1;
			nivel=1;
			document.getElementById("loadingPdf").innerHTML="Por favor espere,<br/> se est&aacute; generando su PDF";
		}
		timerID = setTimeout("correrTimer();",300);
	}

	function pararPDFLoading(){
		document.getElementById("loadingPdf").style.display="none";
		clearTimeout(timerID);
		clearTimeout(pararTimerID);
	}
	
	function pagina(pagina){
		control = '<?= $control?>';
		fechaDesde = '<?= $fechaDesde?>';
		fechaHasta = '<?= $fechaHasta?>';
		cod_doc = '<?= $codDoc?>';
		location.href = "contabilidadDocumento.php?pagina="+pagina+"&control="+control+"&fechaDesde="+fechaDesde+"&fechaHasta="+fechaHasta+"&cod_doc="+cod_doc;
	}
	</script>
</head>
<body onload="iniciar();1">
<br/>
<form name="formPDF" id="formPDF" action="codaMultiplePDF.php" method="post" >
	<input type="hidden" id="codas" name="codas" value=""/>
</form>
<form name="form" id="form" action="contabilidadDocumento.php">
	<table width="645" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td height="15" colspan="4" align="left" class="normalNegroNegrita"><strong>B&Uacute;SQUEDA DE ASIENTOS CONTABLES </strong></td>
		</tr>
		<tr>
			<td class="normalNegrita" align="center"><input type="radio" id="controlFechas" value="2" name="control" onclick="habiDesabiOpciones();" <?php if($control=="2"){ echo 'checked="checked"'; }?>/></td>
			<td width="132" height="36" class="normalNegrita">En Fecha:</td>
			<td width="439" height="36" colspan="2" class="normal">
				<input type="text" size="10" id="fechaDesde" name="fechaDesde" value="<?= $fechaDesde?>" class="dateparse" onfocus="javascript: compararFechas(this);" readonly="readonly"/>
				<a href="javascript:void(0);" 
					onclick="if(document.getElementById('controlFechas').checked==true){g_Calendar.show(event, 'fechaDesde');}" 
					title="Show popup calendar">
					<img src="../../js/lib/calendarPopup/img/calendar.gif" 
						class="cp_img" 
						alt="Open popup calendar"/>
				</a>
				<input type="text" size="10" id="fechaHasta" name="fechaHasta" value="<?= $fechaHasta?>" class="dateparse" onfocus="javascript: compararFechas(this);" readonly="readonly"/>
				<a href="javascript:void(0);" 
					onclick="if(document.getElementById('controlFechas').checked==true){g_Calendar.show(event, 'fechaHasta');}" 
					title="Show popup calendar">
					<img src="../../js/lib/calendarPopup/img/calendar.gif" 
						class="cp_img" 
						alt="Open popup calendar"/>
				</a>
			</td>
		</tr>		
		<tr>
			<td class="normalNegrita" align="center"><input type="radio" id="controlCodigo" value="1" name="control" onclick="habiDesabiOpciones();" <?php if($control=="1"){ echo 'checked="checked"'; }?>/></td>
			<td width="132" height="36" class="normalNegrita">C&oacute;digo del Documento:</td>
			<td width="439" height="36" colspan="2" class="normal">
				<input name="cod_doc" type="text" class="normal" id="cod_doc" value="<?= $codDoc?>" <?php if($control!="1"){ echo 'disabled="disabled"'; }?>/>
				<script>
				document.getElementById("cod_doc").onkeypress=enter;
				</script>
			</td>
		</tr>

		<tr>
			<td colspan="4" align="center">
				<input type="button" value="Buscar" onclick="javascript:ejecutar();"/>
			</td>
		</tr>
	</table>
	<br/>
<?php
if($control=="1" && $codDoc!=""){
	$sql =	"SELECT ".
				"src.comp_id, ".
				"src.reng_comp, ".
				"src.cpat_id, ".
				"src.cpat_nombre, ".
				"src.rcomp_debe, ".
				"src.rcomp_haber, ".
				"EXTRACT(DAY FROM scd.comp_fec)||'/'||EXTRACT(month FROM scd.comp_fec)||'/'||EXTRACT(Year FROM scd.comp_fec) AS fecha_emision, ".
				"scd.comp_comen	".
			"FROM sai_reng_comp src, sai_comp_diario scd ".
			"WHERE ".
				"esta_id<>15 AND ".
				"trim(src.comp_id) = trim(scd.comp_id) AND ".
				"scd.comp_id LIKE 'coda-%' AND ".
				"scd.comp_doc_id='".$codDoc."' AND ".
				"src.mostrar IS TRUE ".
			"ORDER BY src.oid";
	$resultado_asientos=pg_query($conexion,$sql) or die("Error al consultar");
	?>
	<div align="center" style="margin-top: 30px;display: none;" id="loading">
		<img width="50px" src="../../imagenes/loading.gif"/>
	</div>
	<table id="resultados" width="900" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td class="normalNegroNegrita">
				<div align="center">Fecha </div>
			</td>
			<td width="85" height="14" class="normalNegroNegrita">
				<div align="center">Identif.</div>
			</td>
			<td class="normalNegroNegrita">
				<div align="center">Rengl&oacute;n</div>
			</td>
			<td class="normalNegroNegrita">
				<div align="center">Cta Contable</div>
			</td>
			<td class="normalNegroNegrita">
				<div align="center">Nombre</div>
			</td>
			<td class="normalNegroNegrita">
				<div align="center">Debe</div>
			</td>
			<td class="normalNegroNegrita">
				<div align="center">Haber</div>
			</td>
			<td class="normalNegroNegrita">
				<div align="center">Comentario</div>
			</td>
			<td class="normalNegroNegrita">
				<div align="center">Opci&oacute;n</div>
			</td>
		</tr>
	<?
	$total_debe=0;
	$total_haber=0;
	$elementos=pg_num_rows($resultado_asientos);
	$contador=0;
	$cont_registros=0;
	while($rowor=pg_fetch_array($resultado_asientos)){
		$query="select count (comp_id) as cantidad from sai_reng_comp where comp_id='".$rowor['comp_id']."'";
		$resultado_query=pg_query($conexion,$query) or die("Error al consultar los registros contables");
		if($rowq=pg_fetch_array($resultado_query)) {
			$cont_registros=$rowq['cantidad'];
		}
	?>
		<tr>
			<td width="45" align="center" class="normal"><?= $rowor['fecha_emision']?></td>
			<td>
				<div align="center" class="normal"><?= $rowor['comp_id']?></div>
			</td>
			<td width="45" align="center" class="normal"><?= $rowor['reng_comp']?></td>
			<td width="75" align="center" class="normal"><?= $rowor['cpat_id']?></td>
			<td width="145" align="center" class="normal"><?= $rowor['cpat_nombre']?></td>
			<td width="65" align="center" class="normal"><?= $rowor['rcomp_debe']?></td>
			<td width="65" align="center" class="normal"><?= $rowor['rcomp_haber']?></td>
			<td width="90" align="center" class="normal"><?= $rowor['comp_comen']?></td>
	<?
		if(($contador==$cont_registros-1) or ($contador==$elementos-1)){
			$contador=-1;
	?>
			<td width="90">
				<div align="center" class="normal">
					<img src="imagenes/vineta_azul.gif" width="11" height="7"/>
					<a href="javascript:abrir_ventana('codaDetalle.php?codigo=<?= trim($rowor['comp_id'])?>')" class="copyright">Ver Detalle</a>
				</div>
			</td>
	<?
		}
	?>
		</tr>
	<?
			$contador=$contador+1;
			$total_debe=$total_debe+$rowor['rcomp_debe'];
			$total_haber=$total_haber+$rowor['rcomp_haber'];
	}
	//BUSCAR LOS SOPORTES DEL PGCH ASOCIADOS AL SOPG
	$sql="select doso_doc_fuente from sai_docu_sopor where doso_doc_soport='".$codDoc."'";
	$resultado_pgch=pg_query($conexion,$sql) or die("Error al consultar");
	while ($rowor=pg_fetch_array($resultado_pgch)){
		$sql1 = "SELECT src.comp_id, src.reng_comp, src.cpat_id, src.cpat_nombre, src.rcomp_debe, src.rcomp_haber, EXTRACT(DAY FROM scd.comp_fec)||'/'||EXTRACT(month FROM scd.comp_fec)||'/'||EXTRACT(Year FROM scd.comp_fec) as fecha_emision, scd.comp_comen
				FROM sai_reng_comp src, sai_comp_diario scd where esta_id<>15 and src.comp_id = scd.comp_id AND scd.comp_doc_id='".$rowor["doso_doc_fuente"]."' order by src.oid"; 

	 	$resultado_asientos_pgch=pg_query($conexion,$sql1) or die("Error al consultar");
		$elementos=pg_num_rows($resultado_asientos_pgch);
		$contador=0;
		$cont_registros=0;
		while ($rowpg=pg_fetch_array($resultado_asientos_pgch)){
	 		$query="select count (comp_id) as cantidad from sai_reng_comp where comp_id='".$rowpg['comp_id']."'";
	 		$resultado_query=pg_query($conexion,$query) or die("Error al consultar los registros contables");
	 		if($rowq=pg_fetch_array($resultado_query)){
	 			$cont_registros=$rowq['cantidad'];
	 		}
	 	?>
		<tr>
			<td width="45" align="center" class="normal"><?= $rowpg['fecha_emision']?></td>
			<td>
				<div align="center" class="normal"><?= $rowpg['comp_id']?></div>
			</td>
			<td width="45" align="center" class="normal"><?= $rowpg['reng_comp']?></td>
			<td width="75" align="center" class="normal"><?= $rowpg['cpat_id']?></td>
			<td width="145" align="center" class="normal"><?= $rowpg['cpat_nombre']?></td>
			<td width="65" align="center" class="normal"><?= $rowpg['rcomp_debe']?></td>
			<td width="65" align="center" class="normal"><?= $rowpg['rcomp_haber']?></td>
			<td width="85" align="center" class="normal"><?= $rowpg['comp_comen']?></td>
		<?
			if ($contador==$cont_registros-1) {
				$contador=-1;
		?>
			<td width="90">
				<div align="center" class="normal">
					<img src="imagenes/vineta_azul.gif" width="11" height="7"/>
					<a href="javascript:abrir_ventana('codaDetalle.php?codigo=<?= trim($rowpg['comp_id'])?>')" class="copyright">Ver Detalle</a>
				</div>
			</td>
		<?
			}
		?>
		</tr>
	<?
			$contador=$contador+1;
			$elementos=$elementos-1;
			$total_debe=$total_debe+$rowor['rcomp_debe'];
			$total_haber=$total_haber+$rowor['rcomp_haber'];
		}
	}
	?>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td width="65" align="center" class="normal"><b><?= $total_debe?></b></td>
			<td width="65" align="center" class="normal"><b><?= $total_haber?></b></td>
			<td></td>
		</tr>
	</table>
<?php
}else if($control=="2" && $fechaDesde!="" && $fechaHasta!=""){
	$sql = "SELECT 
				COUNT(DISTINCT(src.comp_id)) 
			FROM 
				(SELECT
					scd.comp_doc_id,
					MAX(src.rcomp_id) AS rcomp_id 
				FROM 
					sai_reng_comp src
					INNER JOIN sai_comp_diario scd ON (scd.comp_id = src.comp_id)
					INNER JOIN sai_sol_pago ssp ON (ssp.sopg_id = scd.comp_doc_id)
				WHERE
					src.mostrar IS TRUE AND
					scd.esta_id <> 15 AND 
					scd.comp_id LIKE 'coda-%' AND 
					ssp.sopg_fecha BETWEEN to_date('".$fechaDesde."', 'DD/MM/YYYY') AND to_date('".$fechaHasta."', 'DD/MM/YYYY')+1 
				GROUP BY scd.comp_doc_id
				) AS s
				INNER JOIN sai_reng_comp src ON (s.rcomp_id = src.rcomp_id)
				INNER JOIN sai_comp_diario scd ON (s.comp_doc_id = scd.comp_doc_id AND src.comp_id = scd.comp_id)";
	$resultadoContador = pg_exec($conexion, $sql);
	$row = pg_fetch_array($resultadoContador, 0);
	$contador = $row[0];
	$totalPaginas = ($contador%$tamanoPagina == 0)?$contador/$tamanoPagina:intval($contador/$tamanoPagina)+1;
	
	$sql = "SELECT 
				scd.comp_doc_id, 
				src.comp_id, 
				src.rcomp_id, 
				src.reng_comp, 
				src.cpat_id, 
				src.cpat_nombre, 
				src.rcomp_debe, 
				src.rcomp_haber, 
				to_char(scd.comp_fec, 'DD/MM/YYYY') AS fecha_emision,
				scd.comp_comen, 
				scd.comp_fec_emis 
			FROM 
				(SELECT
					scd.comp_doc_id,
					MAX(src.rcomp_id) AS rcomp_id 
				FROM 
					sai_reng_comp src
					INNER JOIN sai_comp_diario scd ON (scd.comp_id = src.comp_id)
					INNER JOIN sai_sol_pago ssp ON (ssp.sopg_id = scd.comp_doc_id)
				WHERE
					src.mostrar IS TRUE AND
					scd.esta_id <> 15 AND 
					scd.comp_id LIKE 'coda-%' AND 
					ssp.sopg_fecha BETWEEN to_date('".$fechaDesde."', 'DD/MM/YYYY') AND to_date('".$fechaHasta."', 'DD/MM/YYYY')+1 
				GROUP BY scd.comp_doc_id
				) AS s
				INNER JOIN sai_reng_comp src ON (s.rcomp_id = src.rcomp_id)
				INNER JOIN sai_comp_diario scd ON (s.comp_doc_id = scd.comp_doc_id AND src.comp_id = scd.comp_id)
			ORDER BY scd.comp_fec_emis, scd.comp_doc_id 
			LIMIT ".$tamanoPagina." OFFSET ".$desplazamiento;
	$resultado_asientos=pg_query($conexion,$sql) or die("Error al consultar");
	?>

			<div align="center">
						
				</div>
				<div class="normalNegroNegrita" align="center">
				<a href="javascript: imprimir();" title="Generar archivo en formato PDF con los comprobantes seleccionados">
							<img border="0" src="../../imagenes/pdf_ico.jpg"/>
						</a>
				REPORTE POR DOCUMENTO DEL CAUSADO DESDE EL <?= $fechaDesde?> HASTA EL <?= $fechaHasta?></div>
				<div>
					<div style="width: 30%;float: left;">&nbsp;</div>
					<div align="left" id="loadingPdf" style="font-size: 8pt;width: 70%;float: left;display: none;">
						Por favor espere,<br/> se est&aacute; generando su PDF...
					</div>
				</div>	
	<table id="resultados" width="900" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<?php
		echo "<tr><td bgcolor='#FFFFFF' class='titular' colspan='11' align='center' valign='middle' height='20px'>";
		$ventanaActual = ($pagina%$tamanoVentana==0)?$pagina/$tamanoVentana:intval($pagina/$tamanoVentana)+1;
		$ri = (($ventanaActual-1)*$tamanoVentana)+1;
		while($ri<=$ventanaActual*$tamanoVentana && $ri<=$totalPaginas) {
			if($ri==(($ventanaActual-1)*$tamanoVentana)+1 && $ri!=1){
				echo "<a onclick='pagina(".($ri-1).");' style='cursor: pointer;text-decoration: underline;'>&lt;</a> ";
			}
			if($ri==$pagina){
				echo $ri." ";
			}else{
				echo "<a onclick='pagina(".$ri.");' style='cursor: pointer;text-decoration: underline;'>".$ri."</a> ";
			}
			if($ri==$ventanaActual*$tamanoVentana && $ri<$totalPaginas){
				echo "<a onclick='pagina(".($ri+1).");' style='cursor: pointer;text-decoration: underline;'>&gt;</a> ";
			}
			$ri++;
		}
		echo "</td></tr>\n";
		?>
		<tr class="td_gray">
			<td class="normal" align="center">
				<input type="checkbox" id="controlCodas" name="controlCodas" onclick="marcarTodosNinguno();"/>
			</td>
			<td class="normalNegroNegrita">
				<div align="center"><strong>Documento </strong></div>
			</td>
			<td class="normalNegroNegrita">
				<div align="center"><strong>Fecha </strong></div>
			</td>
			<td width="85" height="14" >
				<div align="center" class="normal"><strong>Identif.</strong></div>
			</td>
			<td class="normalNegroNegrita">
				<div align="center"><strong>Rengl&oacute;n</strong></div>
			</td>
			<td class="normalNegroNegrita">
				<div align="center"><strong>Cta Contable </strong></div>
			</td>
			<td class="normalNegroNegrita">
				<div align="center"><strong>Nombre </strong></div>
			</td>
			<td class="normal">
				<div align="center"><strong>Debe </strong></div>
			</td>
			<td class="normalNegroNegrita">
				<div align="center"><strong>Haber </strong></div>
			</td>
			<td class="normalNegroNegrita">
				<div align="center"><strong>Comentario </strong></div>
			</td>
			<td class="normalNegroNegrita">
				<div align="center"><strong>Opci&oacute;n </strong></div>
			</td>
		</tr>
	<?
	$elementos=pg_num_rows($resultado_asientos);
	$i=$desplazamiento+1;
	if($elementos>0){	
		while($rowor=pg_fetch_array($resultado_asientos)){
	?>
		<tr>
			<td>
				<div align="center" class="normal"><input type="checkbox" id="codas<?= $rowor['comp_id']?>" name="codas<?= $rowor['comp_id']?>" onclick='agregarQuitarCoda(this, true);' value="<?= $rowor['comp_id']?>"/></div>
			</td>
			<td>
				<div align="center" class="normal"><?= $i.".&nbsp;".$rowor['comp_doc_id']?></div>
			</td>
			<td width="45" align="center" class="normal"><?= $rowor['fecha_emision']?></td>
			<td>
				<div align="center" class="normal"><?= $rowor['comp_id']?></div>
			</td>
			<td width="45" align="center" class="normal"><?= $rowor['reng_comp']?></td>
			<td width="75" align="center" class="normal"><?= $rowor['cpat_id']?></td>
			<td width="145" align="center" class="normal"><?= $rowor['cpat_nombre']?></td>
			<td width="65" align="center" class="normal"><?= $rowor['rcomp_debe']?></td>
			<td width="65" align="center" class="normal"><?= $rowor['rcomp_haber']?></td>
			<td width="90" align="center" class="normal"><?= $rowor['comp_comen']?></td>
			<td width="90">
				<div align="center" class="normal">
					<img src="imagenes/vineta_azul.gif" width="11" height="7"/>
					<a href="javascript:abrir_ventana('codaDetalle.php?codigo=<?= trim($rowor['comp_id'])?>')" class="copyright">Ver Detalle</a>
				</div>
			</td>
		</tr>
	<?
			$i++;
		}
		echo "<tr><td bgcolor='#FFFFFF' class='titular' colspan='11' align='center' valign='middle' height='20px'>";
		$ventanaActual = ($pagina%$tamanoVentana==0)?$pagina/$tamanoVentana:intval($pagina/$tamanoVentana)+1;
		$ri = (($ventanaActual-1)*$tamanoVentana)+1;
		while($ri<=$ventanaActual*$tamanoVentana && $ri<=$totalPaginas) {
			if($ri==(($ventanaActual-1)*$tamanoVentana)+1 && $ri!=1){
				echo "<a onclick='pagina(".($ri-1).");' style='cursor: pointer;text-decoration: underline;'>&lt;</a> ";
			}
			if($ri==$pagina){
				echo $ri." ";
			}else{
				echo "<a onclick='pagina(".$ri.");' style='cursor: pointer;text-decoration: underline;'>".$ri."</a> ";
			}
			if($ri==$ventanaActual*$tamanoVentana && $ri<$totalPaginas){
				echo "<a onclick='pagina(".($ri+1).");' style='cursor: pointer;text-decoration: underline;'>&gt;</a> ";
			}
			$ri++;
		}
		echo "</td></tr>\n";
	}else{
	?>
		<tr>
			<td height="42" colspan="11" bgcolor="#FFFFFF">
				<div align="center" class="normal_naranja" style="color: gray;"><strong>No se encontraron resultados</strong></div>
			</td>
		</tr>
	<?php
	}
	?>
	</table>
	<br/>
<?php
}
?>
</form>
</body>
</html>
<?php pg_close($conexion);?>