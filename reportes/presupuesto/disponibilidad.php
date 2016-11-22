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

$dependenciaUsuario = $_SESSION['user_depe_id'];
$user_perfil_id = $_SESSION['user_perfil_id'];
if((substr($user_perfil_id, 0, 2)=="62" || substr($user_perfil_id, 0, 2)=="80") && substr($user_perfil_id, 2, 3)!=substr(PERFIL_DIRECTOR_PRESUPUESTO, 2, 3)){
	$query = 	"SELECT depe_id_sup
				FROM sai_dependenci 
				WHERE 
					depe_id = '".$dependenciaUsuario."'";
	
	$resultado = pg_exec($conexion, $query);
	if($rowDependencia=pg_fetch_array($resultado)){
		$dependenciaUsuario = $rowDependencia["depe_id_sup"];
	}
}
$mostrarTodasLasPartidas = $_POST['mostrarTodasLasPartidas'];

$tipoImputacion=substr($_POST['categoriaProgramatica'],0,strrpos($_POST['categoriaProgramatica'], ";"));//antes del ;
$idProyectoAccion=substr($_POST['categoriaProgramatica'],strrpos($_POST['categoriaProgramatica'], ";")+1,strlen($_POST['categoriaProgramatica']));//despues del ;
$idAccionEspecifica=$_POST['accionEspecifica'];
$centroGestor=$_POST['centroGestor'];
$opcionConsolidar=$_POST['opcionConsolidar'];
if(!$opcionConsolidar){
	$opcionConsolidar=$_POST['opcionConsolidarProyectos'];
	if(!$opcionConsolidar){
		$opcionConsolidar=$_POST['opcionConsolidarOrganismo'];	
	}
}

$partida = $_POST['partida'];

$codigoPartida = "";
if(strlen($partida)>0){
	$codigoPartida = trim($partida);
	$codigoPartida = str_replace(".00", "", $codigoPartida);
	$tok = strtok(trim($codigoPartida), ":");
	$codigoPartida = trim($tok);
}

$anno_pres=$_SESSION['an_o_presupuesto'];
// Descomentar para que muestre el reporte de disponibilidad del año presupuestario especificado
//$anno_pres=2014;

$fechaInicio=$_POST['txt_inicio'];
$fechaFin=$_POST['hid_hasta_itin'];

if(!$fechaInicio || $fechaInicio==""){
	$fechaInicio = "01/01/".$anno_pres;
}

if(!$fechaFin || $fechaFin==""){
	$fechaFin = date('d/m/Y');
}

list($diaInicio,$mesInicio,$anoInicio) = split( '[/.-]', $fechaInicio);
list($diaFin,$mesFin,$anoFin) = split( '[/.-]', $fechaFin);
$estadoActivo = 1;

$accionesEspecificasAdicionalesDeAccionesCentralizadas = "";
/*if ( $dependenciaUsuario == "ALGUNA DEPENDENCIA" ) {*/
//	$accionesEspecificasAdicionalesDeAccionesCentralizadas = "('ID1', 'ID2')";
	$accionesEspecificasAdicionalesDeAccionesCentralizadas = "('2013-AC2')";
/*}*/

$accionesEspecificasAdicionalesDeProyectos = "";
if ( $dependenciaUsuario == "500" ) {
	$accionesEspecificasAdicionalesDeProyectos = "('117659 A-1', '117659 A-2', '117659 A-6')";
}
if ( $dependenciaUsuario == "550" ) {
	$accionesEspecificasAdicionalesDeProyectos = "('117659 A-1','120670 A-3-1')";
}
if ( $dependenciaUsuario == "600" ) {
	$accionesEspecificasAdicionalesDeProyectos = "('117580 B-3')";
}

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

function enviar(opcion){
	fechaInicio = document.getElementById("txt_inicio").value;
	fechaFin = document.getElementById("hid_hasta_itin").value;
	if(fechaInicio=="" || fechaFin==""){
		alert('Debe indicar la fecha de inicio y de fin');
		return;
	}
	if(opcion==3){
		document.form.action="disponibilidad_PDF.php";
	}else if(opcion==2){
		document.form.action="disponibilidadXLS.php";
	}else{
		document.form.action="disponibilidad.php";
	}
	document.form.submit();
}

function comparar_fechas(fecha_inicial,fecha_final){
	var fecha_inicial=document.form.txt_inicio.value;
	var fecha_final=document.form.hid_hasta_itin.value;
	
	var dia1 =fecha_inicial.substring(0,2);
	var mes1 =fecha_inicial.substring(3,5);
	var anio1=fecha_inicial.substring(6,10);
	
	var dia2 =fecha_final.substring(0,2);
	var mes2 =fecha_final.substring(3,5);
	var anio2=fecha_final.substring(6,10);

	dia1 = parseInt(dia1,10);
	mes1 = parseInt(mes1,10);
	anio1= parseInt(anio1,10);

	dia2 = parseInt(dia2,10);
	mes2 = parseInt(mes2,10);
	anio2= parseInt(anio2,10);
	
	if((anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2))){
		alert("La fecha inicial no debe ser mayor a la fecha final"); 
		document.form.hid_hasta_itin.value='';
		return;
	}
}

function cambiarAccionEspecifica(){
	indiceCategoria = document.getElementById("categoriaProgramatica").selectedIndex;
	selectAccionEspecifica = document.getElementById("accionEspecifica");
	selectCentroGestor = document.getElementById("centroGestor");
	while(selectAccionEspecifica.options.length><?= (($dependenciaUsuario=="400" || $dependenciaUsuario=="450" || $dependenciaUsuario=="150" || $dependenciaUsuario=="350" || $dependenciaUsuario=="050")?1:0)?>){
		selectAccionEspecifica.remove(selectAccionEspecifica.options.length-1);
	}
	if(selectCentroGestor != null){
		while(selectCentroGestor.options.length><?= (($dependenciaUsuario=="400" || $dependenciaUsuario=="452" || $dependenciaUsuario=="450" || $dependenciaUsuario=="150" || $dependenciaUsuario=="350" || $dependenciaUsuario=="050")?1:0)?>){
			selectCentroGestor.remove(selectCentroGestor.options.length-1);
		}
	}
	i = 0;
	while(i<accionEspecifica[indiceCategoria].length){
		var option = document.createElement('option');
		option.value = accionEspecifica[indiceCategoria][i][0];
		option.text = accionEspecifica[indiceCategoria][i][1];
		try {
			selectAccionEspecifica.add(option,null);
		}catch(e){alert(e);}
		i++;
	}
	i = 0;
	if(selectCentroGestor != null){
		while(i<centroGestor[indiceCategoria].length){
			var option = document.createElement('option');
			option.value = centroGestor[indiceCategoria][i][0];
			option.text = centroGestor[indiceCategoria][i][1];
			try {
				selectCentroGestor.add(option,null);
			}catch(e){alert(e);}
			i++;
		}
	}
}

function habilitarDeshabilitarConsolidar(opcion){
	selectCategoriaProgramatica = document.getElementById("categoriaProgramatica");
	opcionConsolidar = document.getElementById("opcionConsolidar");
	opcionConsolidarProyectos = document.getElementById("opcionConsolidarProyectos");
	opcionConsolidarOrganismo = document.getElementById("opcionConsolidarOrganismo");
	if(opcion=='1'){
		marcado = opcionConsolidar.checked;
	}else if(opcion=='2'){
		marcado = opcionConsolidarProyectos.checked;
	}else if(opcion=='3'){
		marcado = opcionConsolidarOrganismo.checked;
	}
	divCategoriaProgramaticaLabel = document.getElementById("divCategoriaProgramaticaLabel");
	divCategoriaProgramaticaField = document.getElementById("divCategoriaProgramaticaField");
	divCentroGestorLabel = document.getElementById("divCentroGestorLabel");
	divCentroGestorField = document.getElementById("divCentroGestorField");
	divAccionEspecificaLabel = document.getElementById("divAccionEspecificaLabel");
	divAccionEspecificaField = document.getElementById("divAccionEspecificaField");
	selectCentroGestor = document.getElementById("centroGestor");
	selectCentroGestor.selectedIndex = 0;
	selectAccionEspecifica = document.getElementById("accionEspecifica");
	selectAccionEspecifica.selectedIndex = 0;
	if(marcado == true){
		if(opcion=='1'){
			divCategoriaProgramaticaLabel.style.display = "block";
			divCategoriaProgramaticaField.style.display = "block";
			selectCategoriaProgramatica.disabled = false;
			opcionConsolidarProyectos.checked = false;
			opcionConsolidarOrganismo.checked = false;
		}else if(opcion=='2'){
			divCategoriaProgramaticaLabel.style.display = "none";
			divCategoriaProgramaticaField.style.display = "none";
			selectCategoriaProgramatica.disabled = true;
			opcionConsolidar.checked = false;
			opcionConsolidarOrganismo.checked = false;
		}else if(opcion=='3'){
			divCategoriaProgramaticaLabel.style.display = "none";
			divCategoriaProgramaticaField.style.display = "none";
			selectCategoriaProgramatica.disabled = true;
			opcionConsolidar.checked = false;
			opcionConsolidarProyectos.checked = false;
		}
		selectAccionEspecifica.disabled = true;
		selectCentroGestor.disabled = true;
		divCentroGestorLabel.style.display = "none";
		divCentroGestorField.style.display = "none";
		divAccionEspecificaLabel.style.display = "none";
		divAccionEspecificaField.style.display = "none";	
	}else{
		selectCategoriaProgramatica.disabled = false;
		selectAccionEspecifica.disabled = false;
		selectCentroGestor.disabled = false;
		divCategoriaProgramaticaLabel.style.display = "block";
		divCategoriaProgramaticaField.style.display = "block";
		divCentroGestorLabel.style.display = "block";
		divCentroGestorField.style.display = "block";
		divAccionEspecificaLabel.style.display = "block";
		divAccionEspecificaField.style.display = "block";
	}
}

function habilitarDeshabilitarCentroGestor(){
	selectAccionEspecifica = document.getElementById("accionEspecifica");
	if(document.getElementById("centroGestor").value != ""){
		selectAccionEspecifica.selectedIndex = 0;
		selectAccionEspecifica.disabled = true;
		document.getElementById("divAccionEspecificaLabel").style.display = "none";
		document.getElementById("divAccionEspecificaField").style.display = "none";
	}else{
		selectAccionEspecifica.disabled = false;
		document.getElementById("divAccionEspecificaLabel").style.display = "block";
		document.getElementById("divAccionEspecificaField").style.display = "block";
	}
}
</script>
<style type="text/css">
.drag{
	padding-right: 2px;
	background: orange;/*#0038A6;*/ /* red background */
	width: 1000px;
	height: 22px;
	position: absolute; /* we want to set an absolute position so the div can be moved in reference to the screen */
	top: 0; /* this sets the positioning of the element in reference to the top left of your window. it will space it 50 pixels from the top */
	left: 2%; /* this will set the position of the element 50 pixels to right of the top left of your window. */
	z-index: 5; /* this will position it above anything with a lower z-index, sort of like layers. So you could layer these divs using z-index and have them stack on each other. */
	margin-left: 205px;
	cursor: pointer;
}
.columna{
	font-size: 7pt;
	font-weight: bold;
	background: #C3ECCC;
	margin-top: 4px;
	margin-left: 5px;
	padding-left: 3px;
	height: 14px;
	float:left;
}
</style>
<script type="text/javascript">
//var x;
var y;
var element;
var being_dragged = false;
function mouser(event){
	if(event.offsetX || event.offsetY) {
		//x=event.offsetX-5;
		y=event.offsetY-5;
	}else{
		//x=event.pageX-5;
		y=event.pageY-5;
	}
	if(being_dragged == true) {
		//document.getElementById(element).style.left = x +'px';
		document.getElementById(element).style.top = y +'px';
	}
}

function mouse_down(ele_name) {
	being_dragged = true;
	element = ele_name;
	//document.getElementById(element).style.cursor = 'move';
}
function mouse_up() {
	being_dragged = false;
	document.getElementById(element).style.top = y +'px';
	//document.getElementById(element).style.left = x +'px';
	//document.getElementById(element).style.cursor = 'auto';
}

function oculta(id){
    var elDiv = document.getElementById(id); //se define la variable "elDiv" igual a nuestro div
    elDiv.style.display='none'; //damos un atributo display:none que oculta el div     
   }

function muestra(id){
    var elDiv = document.getElementById(id); //se define la variable "elDiv" igual a nuestro div
    elDiv.style.display='block';//damos un atributo display:block que  el div     
   }
window.onscroll=function(){//cuando activen el scroll

	if(window.pageYOffset < 350){
		

		 oculta("divScroll");
		  oculta("divEspacio");

		}else{

	
			 muestra("divScroll");
			  muestra("divEspacio");

			} ;
	}
</script> 
</head>
<body class="normal" onMouseMove="mouser(event);">


<div  id="divScroll" align="center" style="display:none;width:100%; margin:0px; position: fixed; ">
<table    style=" margin-left: auto; margin-right: auto;margin-top:0px;   width:98%; height: 30px;" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
					<tr class="td_gray">
						<td colspan="14" class="normalNegroNegrita"><?= $descripcionProyecto?></td>
					</tr>
					<tr class="td_gray">
						<td class="normalNegroNegrita" width="200px">Denominaci&oacute;n</td>
						<td class="normalNegroNegrita" width="70px">Partida</td>
						<td class="normalNegroNegrita" width="110px">Presupuesto Ley</td>
						<td class="normalNegroNegrita" width="60px">Recibido</td>
						<td class="normalNegroNegrita" width="60px">Cedido</td>
						<td class="normalNegroNegrita" width="110px">Presupuesto modif.</td>
						<td class="normalNegroNegrita" width="60px">Apartado</td>
						<td class="normalNegroNegrita" width="60px">Apartado en tr&aacute;nsito (actual)</td>
						<td class="normalNegroNegrita" width="90px">Comprometido</td>
						<td class="normalNegroNegrita" width="90px">Compr. aislado</td>
						<td class="normalNegroNegrita" width="60px">Causado</td>
						<td class="normalNegroNegrita" width="60px">Pagado</td>
						<td class="normalNegroNegrita" width="60px">Disponible</td>
						<td class="normalNegroNegrita" width="70px">% Ejecuci&oacute;n</td>
					</tr>
					
					</table>
</div>
					<div id="divEspacio" style="display:none; height: 40px; width:89%; margin-bottom:20px; "></div>

<form name="form" method="post">
<table border="0" width="80%" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="3" align="center">
			<a href="javascript:abrir_ventana('disponibilidadTotal.php','')">
				Listado de todos los proy/acc a la fecha actual
			</a>
		</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">
		<table border="0" width="80%" align="center" cellpadding="0" cellspacing="0" class="tablaalertas">
			<tr class="td_gray">
				<td colspan="3" height="10px" class="normalNegroNegrita">DISPONIBILIDAD PRESUPUESTARIA</td>
			</tr>
			<?php
			if($dependenciaUsuario==substr(PERFIL_DIRECTOR_PRESUPUESTO, 2, 3)){
			?>
			<tr>
				<td class="normal" colspan="3" align="center" valign="middle" height="30px">
					<input type="checkbox" id="mostrarTodasLasPartidas" name="mostrarTodasLasPartidas" value="true" <?php if($mostrarTodasLasPartidas=="true"){echo "checked='checked'";}?>/>
					Mostrar <b>Todas</b> las <b>Partidas</b>
				</td>
			</tr>
			<?php 
			}
			?>
			<tr>
				<td class="normalNegrita" width="22%">
					<div id="divCategoriaProgramaticaLabel" <?php if($opcionConsolidar=="2" || $opcionConsolidar=="3"){echo "style='display: none;'";}else{echo "style='display: block;'";}?>>
						&nbsp;Categor&iacute;a program&aacute;tica:
					</div>
				</td>
				<td colspan="2" >
					<div id="divCategoriaProgramaticaField" <?php if($opcionConsolidar=="2" || $opcionConsolidar=="3"){echo "style='display: none;'";}else{echo "style='display: block;'";}?>>
				<?php
					$query = 	"SELECT * ".
									"FROM ".
									"( ".
								 		"SELECT ".
											"sp.proy_id as id_proyecto_accion, ".
											"sp.proy_titulo as nombre, ".
											"'1' as tipo ".
										"FROM sai_proyecto sp ".
										"WHERE ".
											"sp.esta_id <> 13 AND ".
											"sp.pre_anno = ".$anno_pres." AND ".
											"sp.proy_id IN ".
												"( ".
													"SELECT spae.proy_id ".
													"FROM sai_proy_a_esp spae, sai_forma_1125 sf1125 ".
													"WHERE ".
														"spae.pres_anno = sf1125.pres_anno AND ".
														"spae.proy_id = sf1125.form_id_p_ac AND ".
														"sf1125.form_id_aesp = spae.paes_id AND ".
														"spae.pres_anno = ".$anno_pres." AND ".
														"sf1125.pres_anno = ".$anno_pres." ".
														(($dependenciaUsuario!="400" && $dependenciaUsuario!="450" && $dependenciaUsuario!="452" && $dependenciaUsuario!="150" && $dependenciaUsuario!="350")?
															(($accionesEspecificasAdicionalesDeProyectos!='')?"AND (sf1125.depe_cosige = '".$dependenciaUsuario."' OR spae.paes_id IN ".$accionesEspecificasAdicionalesDeProyectos.")":"AND sf1125.depe_cosige = '".$dependenciaUsuario."' ")
															:""
														).
												") ".
										"UNION ".
										"SELECT ".
											"sac.acce_id as id_proyecto_accion, ".
											"sac.acce_denom as nombre, ".
											"'0' as tipo ".
										"FROM sai_ac_central sac ".
										"WHERE ".
											"sac.esta_id <> 13 AND ".
											"sac.pres_anno = ".$anno_pres." AND ".
											"sac.acce_id IN ".
												"(".
													"SELECT spae.acce_id ".
													"FROM sai_acce_esp spae, sai_forma_1125 sf1125 ".
													"WHERE ".
														"spae.pres_anno = sf1125.pres_anno AND ".
														"spae.pres_anno = ".$anno_pres." AND ".
														"sf1125.form_id_aesp = spae.aces_id AND ".
														"sf1125.form_id_p_ac = spae.acce_id AND ".
														"sf1125.pres_anno = ".$anno_pres." ".
														(($dependenciaUsuario!="400" && $dependenciaUsuario!="450" && $dependenciaUsuario!="452" && $dependenciaUsuario!="150" && $dependenciaUsuario!="350")?
															(($accionesEspecificasAdicionalesDeAccionesCentralizadas!='')?"AND (sf1125.depe_cosige = '".$dependenciaUsuario."' OR spae.aces_id IN ".$accionesEspecificasAdicionalesDeAccionesCentralizadas.")":"AND sf1125.depe_cosige = '".$dependenciaUsuario."' ")
															:""
														).
												")".
									") as s ".
									"ORDER BY s.tipo DESC, s.nombre ASC";
					$resultado = pg_exec($conexion, $query);
				?> <select id="categoriaProgramatica" name="categoriaProgramatica" class="normalNegro" onchange="cambiarAccionEspecifica();" <?php if($opcionConsolidar=="2" || $opcionConsolidar=="3"){echo "disabled='disabled'";}?>>
					<?php
					while($row=pg_fetch_array($resultado)){
						if($row["id_proyecto_accion"]==$idProyectoAccion && $row["tipo"]==$tipoImputacion){
							$descripcionProyectoAccion=$row["nombre"];
							echo "<option value='".$row["tipo"].";".$row["id_proyecto_accion"]."' selected='selected'>".(($row["tipo"]=="1")?"Proy: ":"Acc: ").$row["nombre"]."</option>";
						}else{
							echo "<option value='".$row["tipo"].";".$row["id_proyecto_accion"]."'>".(($row["tipo"]=="1")?"Proy: ":"Acc: ").$row["nombre"]."</option>";
						}
					}
					?>
					</select>
					</div>
				</td>
			</tr>
			<?php 
			if($dependenciaUsuario=="400" || $dependenciaUsuario=="450" || $dependenciaUsuario=="452" || $dependenciaUsuario=="150" || $dependenciaUsuario=="350" || $dependenciaUsuario=="050"){
			?>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2">
					<input type="checkbox" id="opcionConsolidar" name="opcionConsolidar" value="1" onclick="habilitarDeshabilitarConsolidar('1');" <?php if($opcionConsolidar=="1"){echo "checked='checked'";}?>/>
					Consolidar todas las <b>Acciones Espec&iacute;ficas</b> de esta categor&iacute;a program&aacute;tica
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2">
					<input type="checkbox" id="opcionConsolidarProyectos" name="opcionConsolidarProyectos" value="2" onclick="habilitarDeshabilitarConsolidar('2');" <?php if($opcionConsolidar=="2"){echo "checked='checked'";}?>/>
					Consolidar todos los <b>Proyectos</b>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2">
					<input type="checkbox" id="opcionConsolidarOrganismo" name="opcionConsolidarOrganismo" value="3" onclick="habilitarDeshabilitarConsolidar('3');" <?php if($opcionConsolidar=="3"){echo "checked='checked'";}?>/>
					Consolidar por <b>Organismo</b>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td class="normalNegrita">
					<div id="divCentroGestorLabel" <?php if($opcionConsolidar!=""){echo "style='display: none;'";}else{echo "style='display: block;'";}?>>
						&nbsp;Consolidar por centro gestor:
					</div>
				</td>
				<td colspan="2" class="normalNegro">
					<div id="divCentroGestorField" <?php if($opcionConsolidar!=""){echo "style='display: none;'";}else{echo "style='display: block;'";}?>>
				<?php 
				$query = 	"SELECT ".
								/*"id_proyecto_accion, ".
								"tipo, ".
								"nombre, ".
								"centro_gestor, ".
								"nombre_categoria ".*/
								"id_proyecto_accion, ".
								"tipo, ".
								"centro_gestor ".
							"FROM ".
							"(".
								"SELECT ".
									"spae.proy_id as id_proyecto_accion, ".
									"'1' as tipo, ".
									"spae.paes_nombre as nombre, ".
									"spae.centro_gestor, ".
									"sp.proy_titulo as nombre_categoria ".
								"FROM sai_proyecto sp, sai_proy_a_esp spae, sai_forma_1125 sf1125 ".
								"WHERE ".
									"sp.pre_anno = spae.pres_anno AND ".
									"sp.proy_id = spae.proy_id AND ".
									"spae.pres_anno = sf1125.pres_anno AND ".
									"spae.proy_id = sf1125.form_id_p_ac AND ".
									"sf1125.form_id_aesp = spae.paes_id AND ".
									"sf1125.pres_anno = ".$anno_pres." ".
									(($dependenciaUsuario!="400" && $dependenciaUsuario!="450" && $dependenciaUsuario!="452" && $dependenciaUsuario!="150" && $dependenciaUsuario!="350" && $dependenciaUsuario!="050")?"AND sf1125.depe_cosige = '".$dependenciaUsuario."' ":"").
								"UNION ".
								"SELECT ".
									"sae.acce_id as id_proyecto_accion, ".
									"'0' as tipo, ".
									"sae.aces_nombre as nombre, ".
									"sae.centro_gestor, ".
									"sac.acce_denom as nombre_categoria ".
								"FROM sai_ac_central sac, sai_acce_esp sae, sai_forma_1125 sf1125 ".
								"WHERE ".
									"sac.pres_anno = sae.pres_anno AND ".
									"sac.acce_id = sae.acce_id AND ".
									"sae.pres_anno = sf1125.pres_anno AND ".
									"sf1125.form_id_aesp = sae.aces_id AND ".
									"sf1125.form_id_p_ac = sae.acce_id AND ".
									"sf1125.pres_anno = ".$anno_pres." ".
									(($dependenciaUsuario!="400" && $dependenciaUsuario!="450" && $dependenciaUsuario!="452" && $dependenciaUsuario!="150" && $dependenciaUsuario!="350" && $dependenciaUsuario!="050")?"AND sf1125.depe_cosige = '".$dependenciaUsuario."' ":"").
							") as s ".
							/*"GROUP BY s.id_proyecto_accion, s.tipo, s.nombre_categoria, s.nombre, s.centro_gestor ".*/
							"GROUP BY s.id_proyecto_accion, s.tipo, s.nombre_categoria, s.centro_gestor ".
							"ORDER BY s.tipo DESC, s.nombre_categoria ASC, s.centro_gestor";
				$resultado = pg_exec($conexion, $query);
				$idProyectoAccionAnterior = "";
				$tipoProyectoAccionAnterior = "";
				$idPrimerProyectoAccion = $idProyectoAccion;
				$tipoPrimerProyectoAccion = $tipoImputacion;
				?> <script>
						var proyectoCentroGestor = new Array();
						var tipoProyectoCentroGestor = new Array();
						var centroGestor = new Array();
					</script>
					<select id="centroGestor" name="centroGestor" class="normalNegro" <?php if($opcionConsolidar!=""){echo "disabled='disabled'";}?> onchange="habilitarDeshabilitarCentroGestor();">
					<?php
					if($dependenciaUsuario=="400" || $dependenciaUsuario=="450" || $dependenciaUsuario=="452" || $dependenciaUsuario=="150" || $dependenciaUsuario=="350" || $dependenciaUsuario=="050"){
						echo '<option value="">Todos</option>';
					}
					while($row=pg_fetch_array($resultado)){
						if(($idProyectoAccionAnterior=="" && $tipoProyectoAccionAnterior=="") || $idProyectoAccionAnterior!=$row["id_proyecto_accion"] || $tipoProyectoAccionAnterior!=$row["tipo"]){
							if($idPrimerProyectoAccion=="" && $tipoPrimerProyectoAccion==""){
								$idPrimerProyectoAccion = $row["id_proyecto_accion"];
								$tipoPrimerProyectoAccion = $row["tipo"];
							}
							?>
							<script>
								proyectoCentroGestor[proyectoCentroGestor.length] = '<?= $row["id_proyecto_accion"]?>';
								tipoProyectoCentroGestor[tipoProyectoCentroGestor.length] = '<?= $row["tipo"]?>';
								centroGestor[centroGestor.length] = new Array();
							</script>
							<?php
							$idProyectoAccionAnterior = $row["id_proyecto_accion"];
							$tipoProyectoAccionAnterior = $row["tipo"];
						}
						$nombre = $row["nombre"];
						if(strlen($row["nombre"])>70){
							$nombre = substr($row["nombre"], 0, 70)."...";
						}
						if($idPrimerProyectoAccion==$row["id_proyecto_accion"] && $tipoPrimerProyectoAccion==$row["tipo"]){
							/*if($centroGestor==$row["centro_gestor"]){
								$descripcionCentroGestor="(".$row["centro_gestor"].") ".$row["nombre"];
								echo "<option value='".$row["centro_gestor"]."' selected='selected'>"."(".$row["centro_gestor"].") ".$nombre."</option>";
							}else{
								echo "<option value='".$row["centro_gestor"]."'>"."(".$row["centro_gestor"].") ".$nombre."</option>";
							}*/
							if($centroGestor==$row["centro_gestor"]){
								$descripcionCentroGestor=$row["centro_gestor"];
								echo "<option value='".$row["centro_gestor"]."' selected='selected'>".$row["centro_gestor"]."</option>";
							}else{
								echo "<option value='".$row["centro_gestor"]."'>".$row["centro_gestor"]."</option>";
							}
						}
						?>
					<script>
						centroGestor[centroGestor.length-1][centroGestor[centroGestor.length-1].length] = new Array(2);
						centroGestor[centroGestor.length-1][centroGestor[centroGestor.length-1].length-1][0] = '<?= $row["centro_gestor"]?>';
						//centroGestor[centroGestor.length-1][centroGestor[centroGestor.length-1].length-1][1] = '<?= "(".$row["centro_gestor"].") ".$nombre?>';
						centroGestor[centroGestor.length-1][centroGestor[centroGestor.length-1].length-1][1] = '<?= $row["centro_gestor"]?>';
					</script>
					<?php
					}
					?>
				</select></div></td>
			</tr>
			<?php 
			}
			?>
			<tr>
				<td class="normalNegrita">
					<div id="divAccionEspecificaLabel" <?php if($opcionConsolidar!="" || $centroGestor!=""){echo "style='display: none;'";}else{echo "style='display: block;'";}?>>
						&nbsp;Acci&oacute;n espec&iacute;fica:
					</div>
				</td>
				<td colspan="2" class="normalNegro">
					<div id="divAccionEspecificaField" <?php if($opcionConsolidar!="" || $centroGestor!=""){echo "style='display: none;'";}else{echo "style='display: block;'";}?>>
				<?php 
				$query = 	"SELECT ".
								"id_proyecto_accion, ".
								"tipo, ".
								"id_accion_especifica, ".
								"nombre, ".
								"centro_gestor, ".
								"centro_costo ".
							"FROM ".
							"(".
								"SELECT ".
									"spae.proy_id as id_proyecto_accion, ".
									"'1' as tipo, ".
									"spae.paes_id as id_accion_especifica, ".
									"spae.paes_nombre as nombre, ".
									"spae.centro_gestor, ".
									"spae.centro_costo, ".
									"sp.proy_titulo as nombre_categoria ".
								"FROM sai_proyecto sp, sai_proy_a_esp spae, sai_forma_1125 sf1125 ".
								"WHERE ".
									"sp.pre_anno = spae.pres_anno AND ".
									"sp.proy_id = spae.proy_id AND ".
									"spae.pres_anno = sf1125.pres_anno AND ".
									"spae.proy_id = sf1125.form_id_p_ac AND ".
									"sf1125.form_id_aesp = spae.paes_id AND ".
									"sf1125.pres_anno = ".$anno_pres." ".
									(($dependenciaUsuario!="400" && $dependenciaUsuario!="450" && $dependenciaUsuario!="452" && $dependenciaUsuario!="150" && $dependenciaUsuario!="350")?
										(($accionesEspecificasAdicionalesDeProyectos!='')?"AND (sf1125.depe_cosige = '".$dependenciaUsuario."' OR spae.paes_id IN ".$accionesEspecificasAdicionalesDeProyectos.")":"AND sf1125.depe_cosige = '".$dependenciaUsuario."' ")
										:""
									).
								"UNION ".
								"SELECT ".
									"sae.acce_id as id_proyecto_accion, ".
									"'0' as tipo, ".
									"sae.aces_id as id_accion_especifica, ".
									"sae.aces_nombre as nombre, ".
									"sae.centro_gestor, ".
									"sae.centro_costo, ".
									"sac.acce_denom as nombre_categoria ".
								"FROM sai_ac_central sac, sai_acce_esp sae, sai_forma_1125 sf1125 ".
								"WHERE ".
									"sac.pres_anno = sae.pres_anno AND ".
									"sac.acce_id = sae.acce_id AND ".
									"sae.pres_anno = sf1125.pres_anno AND ".
									"sf1125.form_id_aesp = sae.aces_id AND ".
									"sf1125.form_id_p_ac = sae.acce_id AND ".
									"sf1125.pres_anno = ".$anno_pres." ".
									(($dependenciaUsuario!="400" && $dependenciaUsuario!="450" && $dependenciaUsuario!="452" && $dependenciaUsuario!="150" && $dependenciaUsuario!="350")?
										(($accionesEspecificasAdicionalesDeAccionesCentralizadas!='')?"AND (sf1125.depe_cosige = '".$dependenciaUsuario."' OR sae.aces_id IN ".$accionesEspecificasAdicionalesDeAccionesCentralizadas.")":"AND sf1125.depe_cosige = '".$dependenciaUsuario."' ")
										:""
									).
							") as s ".
							"ORDER BY s.tipo DESC, s.nombre_categoria ASC, s.centro_gestor, s.centro_costo, s.id_accion_especifica";
				$resultado = pg_exec($conexion, $query);
				$idProyectoAccionAnterior = "";
				$tipoProyectoAccionAnterior = "";
				$idPrimerProyectoAccion = $idProyectoAccion;
				$tipoPrimerProyectoAccion = $tipoImputacion;
				?> <script>
						var proyectoAccion = new Array();
						var tipoProyectoAccion = new Array();
						var accionEspecifica = new Array();
					</script> 
					<select id="accionEspecifica" name="accionEspecifica" class="normalNegro" <?php if($opcionConsolidar!="" || $centroGestor!=""){echo "disabled='disabled'";}?>>
					<?php
					if($dependenciaUsuario=="400" || $dependenciaUsuario=="450" || $dependenciaUsuario=="452" || $dependenciaUsuario=="150" || $dependenciaUsuario=="350" && $dependenciaUsuario!="050"){
						echo '<option value="">Todas</option>';
					}
					while($row=pg_fetch_array($resultado)){
						if(($idProyectoAccionAnterior=="" && $tipoProyectoAccionAnterior=="") || $idProyectoAccionAnterior!=$row["id_proyecto_accion"] || $tipoProyectoAccionAnterior!=$row["tipo"]){
							if($idPrimerProyectoAccion=="" && $tipoPrimerProyectoAccion==""){
								$idPrimerProyectoAccion = $row["id_proyecto_accion"];
								$tipoPrimerProyectoAccion = $row["tipo"];
							}
							?>
							<script>
								proyectoAccion[proyectoAccion.length] = '<?= $row["id_proyecto_accion"]?>';
								tipoProyectoAccion[tipoProyectoAccion.length] = '<?= $row["tipo"]?>';
								accionEspecifica[accionEspecifica.length] = new Array();
							</script>
							<?php
							$idProyectoAccionAnterior = $row["id_proyecto_accion"];
							$tipoProyectoAccionAnterior = $row["tipo"];
						}
						$nombre = $row["nombre"];
						if(strlen($row["nombre"])>70){
							$nombre = substr($row["nombre"], 0, 70)."...";
						}
						if($idPrimerProyectoAccion==$row["id_proyecto_accion"] && $tipoPrimerProyectoAccion==$row["tipo"]){
							if($idAccionEspecifica==$row["id_accion_especifica"]){
								$descripcionAccionEspecifica="(".$row["centro_gestor"]."/".$row["centro_costo"].") ".$row["nombre"];
								echo "<option value='".$row["id_accion_especifica"]."' selected='selected'>"."(".$row["centro_gestor"]."/".$row["centro_costo"].") ".$nombre."</option>";
							}else{
								echo "<option value='".$row["id_accion_especifica"]."'>"."(".$row["centro_gestor"]."/".$row["centro_costo"].") ".$nombre."</option>";
							}
						}
						?>
					<script>
						accionEspecifica[accionEspecifica.length-1][accionEspecifica[accionEspecifica.length-1].length] = new Array(2);
						accionEspecifica[accionEspecifica.length-1][accionEspecifica[accionEspecifica.length-1].length-1][0] = '<?= $row["id_accion_especifica"]?>';
						accionEspecifica[accionEspecifica.length-1][accionEspecifica[accionEspecifica.length-1].length-1][1] = '<?= "(".$row["centro_gestor"]."/".$row["centro_costo"].") ".$nombre?>';
					</script>
					<?php
					}
					?>
				</select></div></td>
			</tr>
			<tr>
				<td colspan="3" class="normalNegrita" height="60px;">
					&nbsp;Partida: <input	autocomplete="off" size="83" type="text" id="partida" name="partida" value="<?php if($partida!=null){echo $partida;} ?>" class="normalNegro"/> <br/>
					&nbsp;Introduzca el n&uacute;mero de partida o una palabra contenida en la descripci&oacute;n de la misma.
					<?php
					$query = 	"SELECT ".
									"sp.part_id, ".
									"sp.part_nombre ".
								"FROM sai_partida sp ".
								"WHERE ".
									"sp.esta_id <> 15 AND ".
									"sp.pres_anno = ".$anno_pres." ".
								"ORDER BY sp.part_id";
					$resultado = pg_exec($conexion, $query);
					$arreglo = "";
					while($row=pg_fetch_array($resultado)){
						$arreglo .= "'".$row["part_id"]." : ".str_replace("\n"," ",$row["part_nombre"])."',";
					}
					$arreglo = substr($arreglo, 0, -1);
					?>
					<script>
						var partidasAMostrar = new Array(<?= $arreglo?>);
						actb(document.getElementById('partida'),partidasAMostrar);
					</script>
				</td>
			</tr>
			<tr>
				<td colspan="3">
				<table border="0" align="left" cellpadding="0" cellspacing="0">
					<tr>
						<td>&nbsp;</td>
						<td class="normalNegrita">
							Fecha Inicio:
							<input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly" <?php if($fechaInicio!=null && $fechaInicio!=""){echo "value='".$fechaInicio."'";}?>/>
							<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar">
								<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
							</a>
						</td>
						<td>&nbsp;</td>
						<td class="normalNegrita">
							Fecha Fin:
							<input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly" <?php if($fechaFin!=null && $fechaFin!=""){echo "value='".$fechaFin."'";}?>/>
							<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
								<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
							</a>
						</td>
					</tr>
				</table>
				<div align="center"></div>
				</td>
			</tr>
			<tr>
				<td colspan="3" valign="middle" height="50" align="center">
					<input type="button" value="Buscar" onclick="enviar(1)" class="normalNegro"/>
					<input type="button" value="Hoja de c&aacute;lculo" onclick="enviar(2)" class="normalNegro"/>
					<?php 
					//if($dependenciaUsuario=="400"){
						?>
						<input type="button" value="PDF" onclick="enviar(3)" class="normalNegro"/>
						<?php 
				//	}
					?>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>
<?php
if(	(($tipoImputacion!=null && $tipoImputacion!="" &&
	$idProyectoAccion!=null && $idProyectoAccion!="") || 
	$opcionConsolidar!="") &&
	$fechaInicio!=null && $fechaInicio!="" &&
	$fechaFin!=null && $fechaFin!=""){
?>
<br/>
<table width="70%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<?php
	if($tipoImputacion!=null && $tipoImputacion!="" && $idProyectoAccion!=null && $idProyectoAccion!=""){
	?>
	<tr>
		<td class="normalNegrita">Proyecto/Acci&oacute;n centralizada:</td>
		<td class="normalNegro"><?= trim($descripcionProyectoAccion)?></td>
	</tr>
	<?php
	}else if($opcionConsolidar=="2"){
	?>
	<tr>
		<td class="normalNegrita" colspan="2">Todos los proyectos consolidados</td>
	</tr>
	<?php
	}else if($opcionConsolidar=="3"){
	?>
	<tr>
		<td class="normalNegrita" colspan="2">Todo el organismo consolidado</td>
	</tr>
	<?php
	}
	if($centroGestor!=null && $centroGestor!=""){
	?>
	<tr>
		<td class="normalNegrita">Centro gestor:</td>
		<td class="normalNegro"><?= $descripcionCentroGestor?></td>
	</tr>
	<?php
	}
	?>
	<tr>
		<td class="normalNegrita">A&ntilde;o Presupuesto:</td>
		<td class="normalNegro"><?= $anno_pres?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Ejecuci&oacute;n:</td>
		<td class="normalNegro"><?= $fechaInicio." al ".$fechaFin?></td>
	</tr>
</table>
	<?
	$query= "SELECT ".
				"sp.part_id, ".
				"sp.part_nombre AS partida ".	
			"FROM sai_partida sp ".
			"WHERE ".
				"sp.pres_anno = ".$anno_pres." AND ".
				"sp.part_id NOT LIKE '4.11.0%' AND ".
				"sp.part_id LIKE '%.00.00' ".
			"ORDER BY sp.part_id";
	$resultadoPartidasPrimariasYSecundarias=pg_query($query) or die("Error en las partidas");
	$tamanoPartidasPrimariasYSecundarias = pg_num_rows($resultadoPartidasPrimariasYSecundarias);
	$arregloPartidas = array();
	while($filaPartidasPrimariasYSecundarias=pg_fetch_array($resultadoPartidasPrimariasYSecundarias)) {
		$arregloPartidas[]= array($filaPartidasPrimariasYSecundarias["part_id"],$filaPartidasPrimariasYSecundarias["partida"]);
	}
	
	if(!$opcionConsolidar && $centroGestor==""){
		//MONTOS PROGRAMADOS
		if($mostrarTodasLasPartidas=="true"){
			$query=	"SELECT ".
						"id_accion_especifica, ".
						"nombre, ".
						"centro_gestor, ".
						"centro_costo, ".
						"part_id, ".
						"partida, ".
						"COALESCE(SUM(monto_programado),0) as monto_programado ".
					"FROM ".
					"(".
					"SELECT ".
						"s.id_accion_especifica, ".
						"s.nombre, ".
						"s.centro_gestor, ".
						"s.centro_costo, ".
						"sf1125d.part_id, ".
						"sp.part_nombre AS partida, ".
						"COALESCE(SUM(sf1125d.fodt_monto),0) as monto_programado ".
					"FROM sai_forma_1125 sf1125, sai_fo1125_det sf1125d, sai_partida sp, ".
						"(";
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica, ".
								"spae.paes_nombre as nombre, ".
								"spae.centro_gestor, ".
								"spae.centro_costo ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($idAccionEspecifica && $idAccionEspecifica!=""){
					$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ".
							"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica, ".
								"sae.aces_nombre as nombre, ".
								"sae.centro_gestor, ".
								"sae.centro_costo ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($idAccionEspecifica && $idAccionEspecifica!=""){
					$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ".
							"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
			}
			$query.=	") as s ".
					"WHERE ".
						"sf1125.pres_anno = ".$anno_pres." AND ".
						"sf1125.form_tipo = '".$tipoImputacion."' AND ".
						"sf1125.form_id_p_ac = s.id_proyecto_accion AND ".
						"sf1125.form_id_aesp = s.id_accion_especifica AND ".
						"sf1125.form_id = sf1125d.form_id AND ".
						"sf1125.pres_anno = sf1125d.pres_anno AND ".
						"sf1125.esta_id <> 15 AND sf1125.esta_id <> 2 AND ".
						"sf1125d.fodt_mes BETWEEN 1 AND 12 AND ".
						"sf1125d.part_id LIKE '".$codigoPartida."%' AND ".
						"sf1125d.part_id NOT LIKE '4.11.0%' AND ".
						"sf1125d.part_id = sp.part_id AND ".
						"sp.esta_id = ".$estadoActivo." AND ".
						"sf1125d.pres_anno = sp.pres_anno AND ".
						"sf1125.form_fecha < to_date('".$fechaFin."', 'DD/MM/YYYY')+1 ".
					"GROUP BY s.id_accion_especifica, s.nombre, s.centro_gestor, s.centro_costo, sf1125d.part_id, sp.part_nombre ".
					"UNION ".
					"SELECT ".
						"s.id_accion_especifica, ".
						"s.nombre, ".
						"s.centro_gestor, ".
						"s.centro_costo, ".
						"sp.part_id, ".
						"sp.part_nombre AS partida, ".
						"0 as monto_programado ".
					"FROM sai_partida sp, ".
						"(";
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica, ".
								"spae.paes_nombre as nombre, ".
								"spae.centro_gestor, ".
								"spae.centro_costo ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($idAccionEspecifica && $idAccionEspecifica!=""){
					$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ".
							"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica, ".
								"sae.aces_nombre as nombre, ".
								"sae.centro_gestor, ".
								"sae.centro_costo ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($idAccionEspecifica && $idAccionEspecifica!=""){
					$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ".
							"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
			}
			$query.=	") as s ".
					"WHERE ".
						"sp.pres_anno = ".$anno_pres." AND ".
						"sp.part_id LIKE '".$codigoPartida."%' AND ".
						"sp.part_id NOT LIKE '4.11.0%' AND ".
						"sp.esta_id = ".$estadoActivo." AND ".
						"sp.part_id NOT LIKE '%.00.00' ".
					"GROUP BY s.id_accion_especifica, s.nombre, s.centro_gestor, s.centro_costo, sp.part_id, sp.part_nombre ".
					") AS s ".
					"GROUP BY id_accion_especifica, nombre, centro_gestor, centro_costo, part_id, partida ".
					"ORDER BY centro_gestor, centro_costo, id_accion_especifica, part_id";
			$resultadoMontosProgramados=pg_query($query) or die("Error en los montos programados");
		}else{
			$query=	"SELECT ".
						"s.id_accion_especifica, ".
						"s.nombre, ".
						"s.centro_gestor, ".
						"s.centro_costo, ".
						"sf1125d.part_id, ".
						"sp.part_nombre AS partida, ".
						"COALESCE(SUM(sf1125d.fodt_monto),0) as monto_programado ".
					"FROM sai_forma_1125 sf1125, sai_fo1125_det sf1125d, sai_partida sp, ".
						"(";
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica, ".
								"spae.paes_nombre as nombre, ".
								"spae.centro_gestor, ".
								"spae.centro_costo ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($idAccionEspecifica && $idAccionEspecifica!=""){
					$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ".
							"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica, ".
								"sae.aces_nombre as nombre, ".
								"sae.centro_gestor, ".
								"sae.centro_costo ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($idAccionEspecifica && $idAccionEspecifica!=""){
					$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ".
							"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
			}
			$query.=	") as s ".
					"WHERE ".
						"sf1125.pres_anno = ".$anno_pres." AND ".
						"sf1125.form_tipo = '".$tipoImputacion."' AND ".
						"sf1125.form_id_p_ac = s.id_proyecto_accion AND ".
						"sf1125.form_id_aesp = s.id_accion_especifica AND ".
						"sf1125.form_id = sf1125d.form_id AND ".
						"sf1125.pres_anno = sf1125d.pres_anno AND ".
						"sf1125.esta_id <> 15 AND sf1125.esta_id <> 2 AND ".
						"sf1125d.fodt_mes BETWEEN 1 AND 12 AND ".
						"sf1125d.part_id LIKE '".$codigoPartida."%' AND ".
						"sf1125d.part_id NOT LIKE '4.11.0%' AND ".
						"sf1125d.part_id = sp.part_id AND ".
						"sf1125d.pres_anno = sp.pres_anno AND ".
						"sf1125.form_fecha < to_date('".$fechaFin."', 'DD/MM/YYYY')+1 ".
					"GROUP BY s.id_accion_especifica, s.nombre, s.centro_gestor, s.centro_costo, sf1125d.part_id, sp.part_nombre ".
					"ORDER BY s.centro_gestor, s.centro_costo, s.id_accion_especifica, sf1125d.part_id";
			$resultadoMontosProgramados=pg_query($query) or die("Error en los montos programados");
		}
		
		//MONTOS RECIBIDOS
		$query=	"SELECT ".
					"s.id_accion_especifica, ".
					"s.centro_gestor, ".
					"s.centro_costo, ".
					"sf0305d.part_id, ".
					"COALESCE(SUM(sf0305d.f0dt_monto),0) as monto_recibido ".
				"FROM sai_doc_genera sdg, sai_forma_0305 sf0305, sai_fo0305_det sf0305d, ".
					"(";
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica, ".
							"spae.centro_gestor, ".
							"spae.centro_costo ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ".
						"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica, ".
							"sae.centro_gestor, ".
							"sae.centro_costo ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ".
						"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
		}
		$query.=	") as s ".
				"WHERE ".
					"sf0305.pres_anno = ".$anno_pres." AND ".
					"sf0305.f030_id = sdg.docg_id AND sdg.wfob_id_ini = 99 AND ".
					"sf0305.esta_id <> 15 AND sf0305.esta_id <> 2 AND ".
					"sf0305.f030_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
					"sf0305.f030_id = sf0305d.f030_id AND ".
					"sf0305.pres_anno = sf0305d.pres_anno AND ".
					"sf0305d.f0dt_id_p_ac = s.id_proyecto_accion AND ".
					"sf0305d.f0dt_id_acesp = s.id_accion_especifica AND ".
					"sf0305d.f0dt_proy_ac = '".$tipoImputacion."' AND ".
					"sf0305d.f0dt_tipo='1' AND ".
					"sf0305d.part_id LIKE '".$codigoPartida."%' AND ".
					"sf0305d.part_id NOT LIKE '4.11.0%' ".
				"GROUP BY s.id_accion_especifica, s.centro_gestor, s.centro_costo, sf0305d.part_id ".
				"ORDER BY s.centro_gestor, s.centro_costo, s.id_accion_especifica, sf0305d.part_id";
		$resultadoMontosRecibidos=pg_query($query) or die("Error en los montos recibidos");
		
		//MONTOS CEDIDOS
		$query=	"SELECT ".
					"s.id_accion_especifica, ".
					"s.centro_gestor, ".
					"s.centro_costo, ".		
					"sf0305d.part_id, ".
					"COALESCE(SUM(sf0305d.f0dt_monto),0) as monto_cedido ".
				"FROM sai_doc_genera sdg, sai_forma_0305 sf0305, sai_fo0305_det sf0305d, ".
					"(";
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica, ".
							"spae.centro_gestor, ".
							"spae.centro_costo ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ".
						"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica, ".
							"sae.centro_gestor, ".
							"sae.centro_costo ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ".
						"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
		}
		$query.=	") as s ".
				"WHERE ".
					"sf0305.pres_anno = ".$anno_pres." AND ".
					"sf0305.f030_id = sdg.docg_id AND sdg.wfob_id_ini = 99 AND ".
					"sf0305.esta_id <> 15 AND sf0305.esta_id <> 2 AND ".
					"sf0305.f030_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
					"sf0305.f030_id = sf0305d.f030_id AND ".
					"sf0305.pres_anno = sf0305d.pres_anno AND ".
					"sf0305d.f0dt_id_p_ac = s.id_proyecto_accion AND ".
					"sf0305d.f0dt_id_acesp = s.id_accion_especifica AND ".
					"sf0305d.f0dt_proy_ac = '".$tipoImputacion."' AND ".
					"sf0305d.f0dt_tipo='0' AND ".
					"sf0305d.part_id LIKE '".$codigoPartida."%' AND ".
					"sf0305d.part_id NOT LIKE '4.11.0%' ".
				"GROUP BY s.id_accion_especifica, s.centro_gestor, s.centro_costo, sf0305d.part_id ".
				"ORDER BY s.centro_gestor, s.centro_costo, s.id_accion_especifica, sf0305d.part_id";
		$resultadoMontosCedidos=pg_query($query) or die("Error en los montos cedidos");

		//MONTOS DIFERIDOS
		$query=	"SELECT ".
					"se.id_accion_especifica, ".
					"se.centro_gestor, ".
					"se.centro_costo, ".	
					"spit.pcta_sub_espe as part_id, ".
					"     CASE 
      WHEN COALESCE(SUM(spit.pcta_monto),0)  < 0.000001 AND COALESCE(SUM(spit.pcta_monto),0)  > -0.000001 THEN 0
      ELSE COALESCE(SUM(spit.pcta_monto),0) 
     END  as monto_diferido ".
				"FROM sai_doc_genera sdg, sai_pcuenta sp, sai_pcta_imputa_traza spit, ".
					"(";
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica, ".
							"spae.centro_gestor, ".
							"spae.centro_costo ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ".
						"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica, ".
							"sae.centro_gestor, ".
							"sae.centro_costo ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ".
						"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
		}
		$query.=	") AS se ".
				"WHERE ".
					"sdg.docg_id = spit.pcta_id AND ".
					"spit.pcta_id = sp.pcta_id AND ".
					"spit.pres_anno = ".$anno_pres." AND ".
					//"spit.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
					"spit.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days' AND ".
					 " spit.pcta_id = sp.pcta_id AND ".
					/*"spt.esta_id <> 15 AND spt.esta_id <> 2 AND ".*/
					"sp.esta_id <> 2 AND ".
	            	"sp.esta_id <> 59 AND ".
					"(sp.pcta_asunto <> '020' OR sp.esta_id <> 15) AND ".
		
					/*"spt.pcta_id NOT IN ".
						"(SELECT pcta_id ".
						"FROM sai_pcta_traza ".
						"WHERE ".
							//"(esta_id=15 OR esta_id=2) AND ".
							"(esta_id=2) AND ".
		
							"pcta_fecha2 < to_date('".$fechaFin."', 'DD/MM/YYYY')+1) AND ".
					"(".
						"(sdg.wfob_id_ini = 99) OR ".
						"(spit.depe_id = '350' AND sdg.perf_id_act IN ('".PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS."','".PERFIL_PRESIDENTE."')) OR ".
						"(spit.depe_id = '150' AND sdg.perf_id_act IN ('".PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS."','".PERFIL_DIRECTOR_EJECUTIVO."')) OR ".
						"(spit.depe_id <> '350' AND spit.depe_id <> '150' AND sdg.perf_id_act IN ('".PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS."','".PERFIL_DIRECTOR_EJECUTIVO."','".PERFIL_PRESIDENTE."')) ".
					") AND ".*/
					"spit.pcta_tipo_impu = '".$tipoImputacion."' AND ".
					"spit.pcta_acc_pp = se.id_proyecto_accion AND ".
					"spit.pcta_acc_esp = se.id_accion_especifica AND ".
					"spit.pcta_sub_espe LIKE '".$codigoPartida."%' AND ".
					"spit.pcta_sub_espe NOT LIKE '4.11.0%' ".
				"GROUP BY se.id_accion_especifica, se.centro_gestor, se.centro_costo, spit.pcta_sub_espe ".
				"ORDER BY se.centro_gestor, se.centro_costo, se.id_accion_especifica, spit.pcta_sub_espe";
                                                                                                                                                                              
	//error_log(print_r($query,true));
		$resultadoMontosDiferidos=pg_query($query) or die("Error en los montos diferidos");
		
		
	
		
		
		// montos en transito 
		
			   $query ="
			   
				 /* TODAS SEPARADAS*/           (SELECT b.pcta_acc_esp AS id_accion_especifica,
				       se.centro_gestor,
				       se.centro_costo,
				       b.pcta_sub_espe AS part_id,
				       COALESCE(SUM(b.pcta_monto),0) AS monto_diferido
				FROM sai_pcuenta AS a,
				     sai_pcta_imputa AS b,
				     sai_doc_genera d,";
				     
			   if($tipoImputacion=="1"){
			 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
          spae.paes_id AS id_accion_especifica,
          spae.centro_gestor,
          spae.centro_costo
   FROM sai_proy_a_esp spae
   WHERE spae.proy_id =  '".$idProyectoAccion."'
     AND spae.pres_anno = ".$anno_pres."
   ORDER BY spae.centro_gestor,
            spae.centro_costo,
            spae.paes_id ) AS se";
			 
			   }
			 
			 
			  if($tipoImputacion=="0"){
			 $query .="(SELECT sae.acce_id AS id_proyecto_accion,
				          sae.aces_id AS id_accion_especifica,
				          sae.centro_gestor,
				          sae.centro_costo
				   FROM sai_acce_esp sae
				   WHERE sae.acce_id = '".$idProyectoAccion."'
				     AND sae.pres_anno = ".$anno_pres."
				   ORDER BY sae.centro_gestor,
				            sae.centro_costo,
				            sae.aces_id) AS se";
			  }   
				
				 $query .="
				WHERE a.pcta_id=d.docg_id
				  AND a.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days'
				  AND se.id_proyecto_accion = b.pcta_acc_pp
				  AND se.id_accion_especifica = b.pcta_acc_esp
				  AND b.pres_anno=".$anno_pres."
				  AND a.esta_id <>15
				  AND d.esta_id <>15
				  AND d.esta_id <>13
				  AND d.perf_id_act not in ('".PERFIL_PRESIDENTE."','".PERFIL_DIRECTOR_EJECUTIVO."')
				  AND a.esta_id<>14
				  AND a.esta_id<>2
				  AND a.pcta_id=b.pcta_id
				  AND b.pcta_tipo_impu=".$tipoImputacion."::BIT
				  AND b.pcta_acc_pp=  '".$idProyectoAccion."'";
			   
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"AND b.pcta_acc_esp = '".$idAccionEspecifica."' ";
			}
			
			$query .= "
				AND b.pcta_sub_espe LIKE '".$codigoPartida."%'
				
				GROUP BY b.pcta_sub_espe,
				         pcta_acc_esp,
				         se.centro_gestor,
				         se.centro_costo
				ORDER BY se.centro_gestor,
				         se.centro_costo,
				         b.pcta_acc_esp,
				         b.pcta_sub_espe)
				         
				         UNION
				         
				     /* .00.00*/           (SELECT b.pcta_acc_esp AS id_accion_especifica,
       se.centro_gestor,
       se.centro_costo,
       substring(b.pcta_sub_espe FROM 0 FOR 8)||'.00.00' AS part_id,     
       COALESCE(SUM(b.pcta_monto),0) AS monto_diferido
				FROM sai_pcuenta AS a,
				     sai_pcta_imputa AS b,
				     sai_doc_genera d,";
				     
			   if($tipoImputacion=="1"){
			 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
          spae.paes_id AS id_accion_especifica,
          spae.centro_gestor,
          spae.centro_costo
   FROM sai_proy_a_esp spae
   WHERE spae.proy_id =  '".$idProyectoAccion."'
     AND spae.pres_anno = ".$anno_pres."
   ORDER BY spae.centro_gestor,
            spae.centro_costo,
            spae.paes_id ) AS se";
			 
			   }
			 
			 
			  if($tipoImputacion=="0"){
			 $query .="(SELECT sae.acce_id AS id_proyecto_accion,
				          sae.aces_id AS id_accion_especifica,
				          sae.centro_gestor,
				          sae.centro_costo
				   FROM sai_acce_esp sae
				   WHERE sae.acce_id = '".$idProyectoAccion."'
				     AND sae.pres_anno = ".$anno_pres."
				   ORDER BY sae.centro_gestor,
				            sae.centro_costo,
				            sae.aces_id) AS se";
			  }   
				
				 $query .="
				WHERE a.pcta_id=d.docg_id
				  AND a.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days'
				  AND se.id_proyecto_accion = b.pcta_acc_pp
				  AND se.id_accion_especifica = b.pcta_acc_esp
				  AND b.pres_anno=".$anno_pres."
				  AND a.esta_id <>15
				  AND d.esta_id <>15
				  AND d.esta_id <>13
				   AND d.perf_id_act not in ('".PERFIL_PRESIDENTE."','".PERFIL_DIRECTOR_EJECUTIVO."')		  
				  AND a.esta_id<>14
				  AND a.esta_id<>2
				  AND a.pcta_id=b.pcta_id
				  AND b.pcta_tipo_impu=".$tipoImputacion."::BIT
				  AND b.pcta_acc_pp=  '".$idProyectoAccion."'";
			   
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"AND b.pcta_acc_esp = '".$idAccionEspecifica."' ";
			}
			
			$query .= "
				AND b.pcta_sub_espe LIKE '".$codigoPartida."%'
				
				GROUP BY substring(b.pcta_sub_espe FROM 0 FOR 8),
				         pcta_acc_esp,
				         se.centro_gestor,
				         se.centro_costo
				ORDER BY se.centro_gestor,
				         se.centro_costo,
				         b.pcta_acc_esp)
				         
				         
				         
				       UNION
				         
	 /* .00.00.00*/           (SELECT b.pcta_acc_esp AS id_accion_especifica,
				       se.centro_gestor,
				       se.centro_costo,
				      substring(b.pcta_sub_espe FROM 0 FOR 5)||'.00.00.00' AS part_id, 
				       COALESCE(SUM(b.pcta_monto),0) AS monto_diferido
				FROM sai_pcuenta AS a,
				     sai_pcta_imputa AS b,
				     sai_doc_genera d,";
				     
			   if($tipoImputacion=="1"){
			 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
          spae.paes_id AS id_accion_especifica,
          spae.centro_gestor,
          spae.centro_costo
   FROM sai_proy_a_esp spae
   WHERE spae.proy_id =  '".$idProyectoAccion."'
     AND spae.pres_anno = ".$anno_pres."
   ORDER BY spae.centro_gestor,
            spae.centro_costo,
            spae.paes_id ) AS se";
			 
			   }
			 
			 
			  if($tipoImputacion=="0"){
			 $query .="(SELECT sae.acce_id AS id_proyecto_accion,
				          sae.aces_id AS id_accion_especifica,
				          sae.centro_gestor,
				          sae.centro_costo
				   FROM sai_acce_esp sae
				   WHERE sae.acce_id = '".$idProyectoAccion."'
				     AND sae.pres_anno = ".$anno_pres."
				   ORDER BY sae.centro_gestor,
				            sae.centro_costo,
				            sae.aces_id) AS se";
			  }   
				
				 $query .="
				WHERE a.pcta_id=d.docg_id
				  AND a.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days'
				  AND se.id_proyecto_accion = b.pcta_acc_pp
				  AND se.id_accion_especifica = b.pcta_acc_esp
				  AND b.pres_anno=".$anno_pres."
				  AND a.esta_id <>15
				  AND d.esta_id <>15
				  AND d.esta_id <>13
				  AND d.perf_id_act not in ('".PERFIL_PRESIDENTE."','".PERFIL_DIRECTOR_EJECUTIVO."')
				  AND a.esta_id<>14
				  AND a.esta_id<>2
				  AND a.pcta_id=b.pcta_id
				  AND b.pcta_tipo_impu=".$tipoImputacion."::BIT
				  AND b.pcta_acc_pp=  '".$idProyectoAccion."'";
			   
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"AND b.pcta_acc_esp = '".$idAccionEspecifica."' ";
			}
			
			$query .= "
				AND b.pcta_sub_espe LIKE '".$codigoPartida."%'
				
				GROUP BY substring(b.pcta_sub_espe FROM 0 FOR 5),
				         pcta_acc_esp,
				         se.centro_gestor,
				         se.centro_costo
				ORDER BY se.centro_gestor,
				         se.centro_costo,
				         b.pcta_acc_esp) 

				         UNION
				         
				    /*MONTO TOTAL*/     (SELECT b.pcta_acc_esp AS id_accion_especifica,
				       se.centro_gestor,
				       se.centro_costo,
				      'monto total' AS part_id,
				       COALESCE(SUM(b.pcta_monto),0) AS monto_diferido
				FROM sai_pcuenta AS a,
				     sai_pcta_imputa AS b,
				     sai_doc_genera d,";
				     
			   if($tipoImputacion=="1"){
			 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
          spae.paes_id AS id_accion_especifica,
          spae.centro_gestor,
          spae.centro_costo
   FROM sai_proy_a_esp spae
   WHERE spae.proy_id =  '".$idProyectoAccion."'
     AND spae.pres_anno = ".$anno_pres."
   ORDER BY spae.centro_gestor,
            spae.centro_costo,
            spae.paes_id ) AS se";
			 
			   }
			 
			 
			  if($tipoImputacion=="0"){
			 $query .="(SELECT sae.acce_id AS id_proyecto_accion,
				          sae.aces_id AS id_accion_especifica,
				          sae.centro_gestor,
				          sae.centro_costo
				   FROM sai_acce_esp sae
				   WHERE sae.acce_id = '".$idProyectoAccion."'
				     AND sae.pres_anno = ".$anno_pres."
				   ORDER BY sae.centro_gestor,
				            sae.centro_costo,
				            sae.aces_id) AS se";
			  }   
				
				 $query .="
				WHERE a.pcta_id=d.docg_id
				  AND a.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days'
				  AND se.id_proyecto_accion = b.pcta_acc_pp
				  AND se.id_accion_especifica = b.pcta_acc_esp
				  AND b.pres_anno=".$anno_pres."
				  AND a.esta_id <>15
				  AND d.esta_id <>15
				  AND d.esta_id <>13
				  AND d.perf_id_act not in ('".PERFIL_PRESIDENTE."','".PERFIL_DIRECTOR_EJECUTIVO."')
				  AND a.esta_id<>14
				  AND a.esta_id<>2
				  AND a.pcta_id=b.pcta_id
				  AND b.pcta_tipo_impu=".$tipoImputacion."::BIT
				  AND b.pcta_acc_pp=  '".$idProyectoAccion."'";
			   
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"AND b.pcta_acc_esp = '".$idAccionEspecifica."' ";
			}
			
			$query .= "
				AND b.pcta_sub_espe LIKE '".$codigoPartida."%'
				
				GROUP BY part_id,
				         pcta_acc_esp,
				         se.centro_gestor,
				         se.centro_costo
				ORDER BY se.centro_gestor,
				         se.centro_costo,
				         b.pcta_acc_esp)
				         ";
            
                // 	error_log(print_r($query,true));
           
			
               
          
				$resultadoMontosEnTransito=pg_query($query) or die("Error en los montos Apartados en transito");

	             
	             while($filaProgramadostransito = pg_fetch_array($resultadoMontosEnTransito)) {
	             	
	             	  $params[$filaProgramadostransito["centro_gestor"]."/".$filaProgramadostransito["centro_costo"]."/".$filaProgramadostransito['part_id']]=$filaProgramadostransito["monto_diferido"];
	             	
	             }
	   /*         
echo "<pre>";
echo print_r($params);
echo "</pre>";
*/
$filaProgramadostransito = $params;
		
	  //MONTOS COMPROMETIDOS
		$query=	"SELECT ".
					"se.id_accion_especifica, ".
					"se.centro_gestor, ".
					"se.centro_costo, ".
					"scit.comp_sub_espe as part_id, ".
					"COALESCE(SUM(scit.comp_monto),0) as monto_comprometido ".
				"FROM sai_comp_imputa_traza scit, ".
				"(".
					"SELECT scit.comp_id, MAX(scit.comp_fecha) as fecha ".
					"FROM sai_comp_traza sct, sai_comp_imputa_traza scit, ".
					"(";
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ".
						"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ".
						"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
		}
		$query.=	") as si ".
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
						"scit.comp_tipo_impu = '".$tipoImputacion."' AND ".
						"scit.comp_acc_pp = si.id_proyecto_accion AND ".
						"scit.comp_acc_esp = si.id_accion_especifica ".
					"GROUP BY scit.comp_id ".
				") as s, ".
					"(";
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica, ".
							"spae.centro_gestor, ".
							"spae.centro_costo ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ".
						"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica, ".
							"sae.centro_gestor, ".
							"sae.centro_costo ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ".
						"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
		}
		$query.=	") as se ".
				"WHERE ".
					"scit.comp_id = s.comp_id AND ".
					"scit.comp_fecha = s.fecha AND ".
					"scit.comp_tipo_impu = '".$tipoImputacion."' AND ".
					"scit.comp_acc_pp = se.id_proyecto_accion AND ".
					"scit.comp_acc_esp = se.id_accion_especifica AND ".
					"scit.comp_sub_espe LIKE '".$codigoPartida."%' AND ".
					"scit.comp_sub_espe NOT LIKE '4.11.0%' ".
				"GROUP BY se.id_accion_especifica, se.centro_gestor, se.centro_costo, scit.comp_sub_espe ".
				"ORDER BY se.centro_gestor, se.centro_costo, se.id_accion_especifica, scit.comp_sub_espe";
		$resultadoMontosComprometidos=pg_query($query) or die("Error en los montos comprometidos");

		//MONTOS COMPROMETIDOS AISLADOS
		$query=	"SELECT ".
					"se.id_accion_especifica, ".
					"se.centro_gestor, ".
					"se.centro_costo, ".
					"scit.comp_sub_espe as part_id, ".
					"COALESCE(SUM(scit.comp_monto),0) as monto_comprometido_aislado ".
				"FROM sai_comp_imputa_traza scit, ".
				"(".
					"SELECT scit.comp_id, MAX(scit.comp_fecha) as fecha ".
					"FROM sai_comp_traza sct, sai_comp_imputa_traza scit,sai_comp sc ,".
					"(";
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ".
						"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ".
						"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
		}
		$query.=	") as si ".
					"WHERE ".
						"scit.pres_anno = ".$anno_pres." AND ".
						"length(sc.pcta_id) < 4 AND ".
						"scit.comp_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
						"sct.esta_id <> 15 AND sct.esta_id <> 2 AND ".
						"sct.comp_id NOT IN ".
							"(SELECT comp_id ".
							"FROM sai_comp_traza ".
							"WHERE ".
								"(esta_id=15 OR esta_id=2) AND ".
								"comp_fecha2 < to_date('".$fechaFin."', 'DD/MM/YYYY')+1) AND ".
						"sct.comp_id = scit.comp_id AND 
						sc.comp_id = scit.comp_id AND ".
						"scit.comp_tipo_impu = '".$tipoImputacion."' AND ".
						"scit.comp_acc_pp = si.id_proyecto_accion AND ".
						"scit.comp_acc_esp = si.id_accion_especifica ".
					"GROUP BY scit.comp_id ".
				") as s, ".
					"(";
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica, ".
							"spae.centro_gestor, ".
							"spae.centro_costo ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ".
						"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica, ".
							"sae.centro_gestor, ".
							"sae.centro_costo ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ".
						"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
		}
		$query.=	") as se ".
				"WHERE ".
					"scit.comp_id = s.comp_id AND ".
					"scit.comp_fecha = s.fecha AND ".
					"scit.comp_tipo_impu = '".$tipoImputacion."' AND ".
					"scit.comp_acc_pp = se.id_proyecto_accion AND ".
					"scit.comp_acc_esp = se.id_accion_especifica AND ".
					"scit.comp_sub_espe LIKE '".$codigoPartida."%' AND ".
					"scit.comp_sub_espe NOT LIKE '4.11.0%' ".
				"GROUP BY se.id_accion_especifica, se.centro_gestor, se.centro_costo, scit.comp_sub_espe ".
				"ORDER BY se.centro_gestor, se.centro_costo, se.id_accion_especifica, scit.comp_sub_espe";
		
		 //	error_log(print_r($query,true));
		
		$resultadoMontosComprometidosAislados=pg_query($query) or die("Error en los montos comprometidos aislados");

		//MONTOS CAUSADOS
		$query=	
				"SELECT ".
				"id_accion_especifica, ".
				"centro_gestor, ".
				"centro_costo, ".
				"part_id, ".
				"SUM(monto_causado) AS monto_causado ".
				"FROM ( ".
				
					/*Primer Query No anulados*/
					"(SELECT ".
						"s.id_accion_especifica, ".
						"s.centro_gestor, ".
						"s.centro_costo, ".
						"scd.part_id, ".
						"COALESCE(SUM(scd.cadt_monto),0) AS monto_causado ".
					"FROM sai_causado sc, sai_causad_det scd, ".
						"(";
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica, ".
								"spae.centro_gestor, ".
								"spae.centro_costo ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($idAccionEspecifica && $idAccionEspecifica!=""){
					$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ".
							"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica, ".
								"sae.centro_gestor, ".
								"sae.centro_costo ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($idAccionEspecifica && $idAccionEspecifica!=""){
					$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ".
							"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
			}
			$query.=	") as s ".
					"WHERE ".
						"sc.pres_anno = ".$anno_pres." AND ".
						"sc.esta_id <> 2 AND ".
						/*"sc.esta_id <> 15 AND ".*/
	
						" (( ".
						"sc.fecha_anulacion IS NULL AND ".
						"CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".
						") ".

						"OR ".
						"(sc.fecha_anulacion IS NOT NULL ".
						"AND CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".
						"AND CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".
						")) ".
						
											
						"AND ".
					
						//"CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ".
						//"(sc.fecha_anulacion IS NULL OR (CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) AND ".
						"sc.caus_id = scd.caus_id AND ".
						"sc.pres_anno = scd.pres_anno AND ".
						"scd.cadt_tipo = '".$tipoImputacion."'::BIT AND ".
						"scd.cadt_id_p_ac = s.id_proyecto_accion AND ".
						"scd.cadt_cod_aesp = s.id_accion_especifica AND ".
						"scd.cadt_abono='1' AND ".
						"scd.part_id LIKE '".$codigoPartida."%' AND ".
						"scd.part_id NOT LIKE '4.11.0%' ".
					"GROUP BY s.id_accion_especifica, s.centro_gestor, s.centro_costo, scd.part_id) ".
					//"ORDER BY s.centro_gestor, s.centro_costo, s.id_accion_especifica, scd.part_id";
			
	/*Segundo Query Anulados*/
			"UNION ".
			/*Primer Query No anulados*/
			"(SELECT ".
			"s.id_accion_especifica, ".
			"s.centro_gestor, ".
			"s.centro_costo, ".
			"scd.part_id, ".
			"COALESCE(SUM(scd.cadt_monto*-1),0) AS monto_causado ".
			"FROM sai_causado sc, sai_causad_det scd, ".
			"(";
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica, ".
						"spae.centro_gestor, ".
						"spae.centro_costo ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
						"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($idAccionEspecifica && $idAccionEspecifica!=""){
					$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ".
						"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica, ".
						"sae.centro_gestor, ".
						"sae.centro_costo ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
						"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($idAccionEspecifica && $idAccionEspecifica!=""){
					$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ".
						"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
			}
			$query.=	") as s ".
					"WHERE ".
					"sc.pres_anno = ".$anno_pres." AND ".
					"sc.esta_id <> 2 AND ".
					/*"sc.esta_id <> 15 AND ".*/
			
					"(sc.fecha_anulacion IS NOT NULL ".
					"AND CAST(sc.caus_fecha AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".					
					"AND CAST(sc.fecha_anulacion AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".
					") AND ".				
			//"CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ".
			//"(sc.fecha_anulacion IS NULL OR (CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) AND ".
			"sc.caus_id = scd.caus_id AND ".
			"sc.pres_anno = scd.pres_anno AND ".
			"scd.cadt_tipo = '".$tipoImputacion."'::BIT AND ".
			"scd.cadt_id_p_ac = s.id_proyecto_accion AND ".
			"scd.cadt_cod_aesp = s.id_accion_especifica AND ".
			"scd.cadt_abono='1' AND ".
			"scd.part_id LIKE '".$codigoPartida."%' AND ".
			"scd.part_id NOT LIKE '4.11.0%' ".
			"GROUP BY s.id_accion_especifica, s.centro_gestor, s.centro_costo, scd.part_id) ".
			
			") AS s ".
			"GROUP BY ".
			"id_accion_especifica, ".
			"centro_gestor, ".
			"centro_costo, ".
			"part_id ".			
			"ORDER BY centro_gestor, centro_costo, id_accion_especifica, part_id";
			


		$resultadoMontosCausados=pg_query($query) or die("Error en los montos causados");
				

		
		
		
		//MONTOS PAGADOS
		$query=	"SELECT ".
		"s.id_accion_especifica, ".
					"s.centro_gestor, ".
					"s.centro_costo, ".
					"spd.part_id, ".
					"COALESCE(SUM(spd.padt_monto),0) AS monto_pagado ".
				"FROM sai_pagado sp, sai_pagado_dt spd, ".
					"(";
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica, ".
							"spae.centro_gestor, ".
							"spae.centro_costo ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ".
						"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica, ".
							"sae.centro_gestor, ".
							"sae.centro_costo ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ".
						"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
		}
		$query.=	") as s ".
				"WHERE ".
					"sp.pres_anno = ".$anno_pres." AND ".
					"sp.esta_id <> 2 AND ".
					/*"sp.esta_id <> 15 AND ".*/
					"CAST(sp.paga_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ".
					"(sp.fecha_anulacion IS NULL OR (CAST(sp.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) AND ".
					"sp.paga_id = spd.paga_id AND ".
					"sp.pres_anno = spd.pres_anno AND ".
					"spd.padt_tipo = '".$tipoImputacion."'::BIT AND ".
					"spd.padt_id_p_ac = s.id_proyecto_accion AND ".
					"spd.padt_cod_aesp = s.id_accion_especifica AND ".
					//"spd.padt_abono='1' AND ".
					"spd.part_id LIKE '".$codigoPartida."%' AND ".
					"spd.part_id NOT LIKE '4.11.0%' ".
				"GROUP BY s.id_accion_especifica, s.centro_gestor, s.centro_costo, spd.part_id ".
				"ORDER BY s.centro_gestor, s.centro_costo, s.id_accion_especifica, spd.part_id";

	
		
		$resultadoMontosPagados=pg_query($query) or die("Error en los montos pagados");
		
		$totalProgramados = 0;
		$totalRecibidos = 0;
		$totalCedidos = 0;
		$totalDiferidos = 0;
		$totalComprometidos = 0;
		$totalComprometidosAislados = 0;
		$totalCausados = 0;
		$totalPagados = 0;
		$totalDisponible = 0;
	
		$totalPrimerOrdenProgramados = 0;
		$totalPrimerOrdenRecibidos = 0;
		$totalPrimerOrdenCedidos = 0;
		$totalPrimerOrdenDiferidos = 0;
		$totalPrimerOrdenComprometidos = 0;
		$totalPrimerOrdenComprometidosAislados = 0;
		$totalPrimerOrdenCausados = 0;
		$totalPrimerOrdenPagados = 0;
		$totalPrimerOrdenDisponible = 0;
	
		$totalSegundoOrdenProgramados = 0;
		$totalSegundoOrdenRecibidos = 0;
		$totalSegundoOrdenCedidos = 0;
		$totalSegundoOrdenDiferidos = 0;
		$totalSegundoOrdenComprometidos = 0;
		$totalSegundoOrdenComprometidosAislados = 0;
		$totalSegundoOrdenCausados = 0;
		$totalSegundoOrdenPagados = 0;
		$totalSegundoOrdenDisponible = 0;
	
		$programado = 0;
		$recibido = 0;
		$cedido = 0;
		$diferido = 0;
		$comprometido = 0;
		$comprometidoAislado = 0;
		$causado = 0;
		$pagado = 0;
		$montoAjustado = 0;
		$montoDisponible = 0;
	
		$accionEspecificaAnterior = "";
		$partidaAnteriorPrimerOrden = "";
		$partidaAnteriorSegundoOrden = "";
	
		$tamanoResultado = pg_num_rows($resultadoMontosProgramados);
		$diferencias = "";
		if($tamanoResultado){
			while($filaProgramados=pg_fetch_array($resultadoMontosProgramados)) {
				if($partidaAnteriorPrimerOrden==""){
					$partidaAnteriorPrimerOrden=substr($filaProgramados["part_id"], 0, 4).".00.00.00";
				}else if(	$partidaAnteriorPrimerOrden!=(substr($filaProgramados["part_id"], 0, 4).".00.00.00") ||
							$accionEspecificaAnterior!=$filaProgramados["id_accion_especifica"]){
					$nombrePartida = "";
					$iPartidasPrimariasYSecundarias = 0;						
					while($iPartidasPrimariasYSecundarias<$tamanoPartidasPrimariasYSecundarias) {
						if($partidaAnteriorPrimerOrden==$arregloPartidas[$iPartidasPrimariasYSecundarias][0]){
							$nombrePartida = $arregloPartidas[$iPartidasPrimariasYSecundarias][1];
							break;
						}
						$iPartidasPrimariasYSecundarias++;
					}
					
					if(round($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos)!=0){
						$ejecucion = number_format(($totalPrimerOrdenDiferidos+$totalPrimerOrdenComprometidosAislados + $filaProgramadostransito[$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"]."/".$partidaAnteriorPrimerOrden])*100/($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos),2,',','.')."%";
					}else{
						$ejecucion = "0,00%";	
					}
					
					if ( 	$totalPrimerOrdenProgramados!=0 ||
							$totalPrimerOrdenRecibidos!=0 ||
							$totalPrimerOrdenCedidos!=0 ||
							$totalPrimerOrdenDiferidos!=0 ||
							$totalPrimerOrdenComprometidos!=0 ||
							$totalPrimerOrdenComprometidosAislados!=0 ||
							$totalPrimerOrdenCausados!=0 ||
							$totalPrimerOrdenPagados!=0 ||
							$totalPrimerOrdenDisponible!=0) {
				?>
				<script>
					document.getElementById('nombre-partida-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= $nombrePartida?>';
					document.getElementById('partida-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= $partidaAnteriorPrimerOrden?>';
					document.getElementById('programado-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenProgramados,2,',','.')?>';
					document.getElementById('recibido-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenRecibidos,2,',','.')?>';
					document.getElementById('cedido-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenCedidos,2,',','.')?>';
					document.getElementById('presupuesto-modificado-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos,2,',','.')?>';
					document.getElementById('apartado-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenDiferidos,2,',','.')?>';
					document.getElementById('comprometido-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenComprometidos,2,',','.')?>';
					document.getElementById('comprometido-aislado-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenComprometidosAislados,2,',','.')?>';
					document.getElementById('causado-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenCausados,2,',','.')?>';
					document.getElementById('pagado-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenPagados,2,',','.')?>';
					document.getElementById('disponible-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenDisponible - $filaProgramadostransito[$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"]."/".$partidaAnteriorPrimerOrden],2,',','.') ?>';
					document.getElementById('ejecucion-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= $ejecucion?>';
				</script>
				<?php
					} else {
				?>
				<script>
					var element = document.getElementById('tr-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>');
					if ( element!=null ){
						element.parentNode.removeChild(element);
					}
				</script>
				<?php		
					}
					$partidaAnteriorPrimerOrden=substr($filaProgramados["part_id"], 0, 4).".00.00.00";
					if($accionEspecificaAnterior==$filaProgramados["id_accion_especifica"]){
				?>
				<tr class="normalNegroNegrita" style="color: #35519B;" id="tr-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorPrimerOrden?>">
					<td id="nombre-partida-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorPrimerOrden?>"></td>
					<td id="partida-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorPrimerOrden?>"></td>
					<td align="right" id="programado-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorPrimerOrden?>"></td>
					<td align="right" id="recibido-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorPrimerOrden?>"></td>
					<td align="right" id="cedido-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorPrimerOrden?>"></td>
					<td align="right" id="presupuesto-modificado-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorPrimerOrden?>"></td>
					<td align="right" id="apartado-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorPrimerOrden?>"></td>
					<td align="right">	<?php echo number_format($filaProgramadostransito[$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"]."/".$partidaAnteriorPrimerOrden],2,',','.');?>

					<td align="right" id="comprometido-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorPrimerOrden?>"></td>
					<td align="right" id="comprometido-aislado-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorPrimerOrden?>"></td>
					<td align="right" id="causado-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorPrimerOrden?>"></td>
					<td align="right" id="pagado-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorPrimerOrden?>"></td>
					<td align="right" id="disponible-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorPrimerOrden?>"></td>
					<td align="right" id="ejecucion-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorPrimerOrden?>"> </td>
				</tr>
				<?php 
					}
					$totalPrimerOrdenProgramados = 0;
					$totalPrimerOrdenRecibidos = 0;
					$totalPrimerOrdenCedidos = 0;
					$totalPrimerOrdenDiferidos = 0;
					$totalPrimerOrdenComprometidos = 0;
					$totalPrimerOrdenComprometidosAislados = 0;
					$totalPrimerOrdenCausados = 0;
					$totalPrimerOrdenPagados = 0;
					$totalPrimerOrdenDisponible = 0;
				}
				
				if($partidaAnteriorSegundoOrden==""){
					$partidaAnteriorSegundoOrden=substr($filaProgramados["part_id"], 0, 7).".00.00";
				}else if(	$partidaAnteriorSegundoOrden!=(substr($filaProgramados["part_id"], 0, 7).".00.00") ||
							$accionEspecificaAnterior!=$filaProgramados["id_accion_especifica"]){	

								
								
								

								
						if($accionEspecificaAnterior==$filaCausados["id_accion_especifica"]){
							do{
								if(	$accionEspecificaAnterior==$filaPagados["id_accion_especifica"] &&
									$filaProgramados["part_id"]>$filaCausados["part_id"] && 
									$filaCausados["part_id"]==$filaPagados["part_id"]){
									//IMPRIMIR CAUSADO Y PAGADO
									$causado = $filaCausados["monto_causado"];
									$pagado = $filaPagados["monto_pagado"];
									if($partidaAnteriorSegundoOrden==substr($filaCausados["part_id"], 0, 7).".00.00"){
										$totalSegundoOrdenCausados += $causado;
										$totalSegundoOrdenPagados += $pagado;
									}
									if($partidaAnteriorPrimerOrden==substr($filaCausados["part_id"], 0, 4).".00.00.00"){
										$totalPrimerOrdenCausados += $causado;
										$totalPrimerOrdenPagados += $pagado;
									}
									$totalCausados += $causado;
									$totalPagados += $pagado;
									
									$diferencias .= "<tr class='normal' style='color: red;'>
														<td>".trim($filaProgramados['partida'])."</td>
														<td align='right'>".trim($filaCausados['part_id'])."</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>
															<a style='color: red;' target='_blank' href='detalleCausado.php?partida=".$filaCausados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$causado."&aesp=".$filaCausados['id_accion_especifica']."'>".number_format($causado,2,',','.')."</a>
														</td>
														<td align='right'>
															<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
														</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
													</tr>";
									$filaCausados=pg_fetch_array($resultadoMontosCausados);
									$filaPagados=pg_fetch_array($resultadoMontosPagados);
								}else if($accionEspecificaAnterior==$filaPagados["id_accion_especifica"] &&
									$filaProgramados["part_id"]>$filaPagados["part_id"] &&
									$filaCausados["part_id"]>$filaPagados["part_id"]){
									//IMPRIMIR PAGADO
									$pagado = $filaPagados["monto_pagado"];
									if($partidaAnteriorSegundoOrden==substr($filaPagados["part_id"], 0, 7).".00.00"){
										$totalSegundoOrdenPagados += $pagado;
									}
									if($partidaAnteriorPrimerOrden==substr($filaPagados["part_id"], 0, 4).".00.00.00"){
										$totalPrimerOrdenPagados += $pagado;
									}
									$totalPagados += $pagado;
									
									$diferencias .= "<tr class='normal' style='color: red;'>
														<td>".trim($filaProgramados['partida'])."</td>
														<td align='right'>".trim($filaPagados['part_id'])."</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>
															<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
														</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
													</tr>";							
									$filaPagados=pg_fetch_array($resultadoMontosPagados);
								}else if($accionEspecificaAnterior==$filaPagados["id_accion_especifica"] &&
									$filaProgramados["part_id"]>$filaCausados["part_id"] && 
									$filaCausados["part_id"]<$filaPagados["part_id"]){
									//IMPRIMIR CAUSADO
									$causado = $filaCausados["monto_causado"];
									if($partidaAnteriorSegundoOrden==substr($filaCausados["part_id"], 0, 7).".00.00"){
										$totalSegundoOrdenCausados += $causado;
									}
									if($partidaAnteriorPrimerOrden==substr($filaCausados["part_id"], 0, 4).".00.00.00"){
										$totalPrimerOrdenCausados += $causado;
									}
									$totalCausados += $causado;
									
									$diferencias .= "<tr class='normal' style='color: red;'>
														<td>".trim($filaProgramados['partida'])."</td>
														<td align='right'>".trim($filaCausados['part_id'])."</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>
															<a style='color: red;' target='_blank' href='detalleCausado.php?partida=".$filaCausados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$causado."&aesp=".$filaCausados['id_accion_especifica']."'>".number_format($causado,2,',','.')."</a>
														</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
														<td align='right'>0,00</td>
													</tr>";
									$filaCausados=pg_fetch_array($resultadoMontosCausados);
								}
							}while(	$accionEspecificaAnterior==$filaCausados["id_accion_especifica"] && $filaProgramados["part_id"]>$filaCausados["part_id"]);
						}
						if($accionEspecificaAnterior==$filaPagados["id_accion_especifica"] && $filaProgramados["part_id"]>$filaPagados["part_id"]){
							do{
								//IMPRIMIR PAGADO
								$pagado = $filaPagados["monto_pagado"];
								if($partidaAnteriorSegundoOrden==substr($filaPagados["part_id"], 0, 7).".00.00"){
									$totalSegundoOrdenPagados += $pagado;
								}
								if($partidaAnteriorPrimerOrden==substr($filaPagados["part_id"], 0, 4).".00.00.00"){
									$totalPrimerOrdenPagados += $pagado;
								}
								$totalPagados += $pagado;
								
								$diferencias .= "<tr class='normal' style='color: red;'>
													<td>".trim($filaProgramados['partida'])."</td>
													<td align='right'>".trim($filaPagados['part_id'])."</td>
													<td align='right'>0,00</td>
													<td align='right'>0,00</td>
													<td align='right'>0,00</td>
													<td align='right'>0,00</td>
													<td align='right'>0,00</td>
													<td align='right'>0,00</td>
													<td align='right'>0,00</td>
													<td align='right'>0,00</td>
													<td align='right'>
														<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
													</td>
													<td align='right'>0,00</td>
													<td align='right'>0,00</td>
												</tr>";
								$filaPagados=pg_fetch_array($resultadoMontosPagados);
							}while(	$accionEspecificaAnterior==$filaPagados["id_accion_especifica"] && $filaProgramados["part_id"]>$filaPagados["part_id"]);
						}
						echo $diferencias;
						$diferencias = "";
						$nombrePartida = "";
						$iPartidasPrimariasYSecundarias = 0;						
						while($iPartidasPrimariasYSecundarias<$tamanoPartidasPrimariasYSecundarias) {
							if($partidaAnteriorSegundoOrden==$arregloPartidas[$iPartidasPrimariasYSecundarias][0]){
								$nombrePartida = $arregloPartidas[$iPartidasPrimariasYSecundarias][1];
								break;
							}
							$iPartidasPrimariasYSecundarias++;
						}
						
						if(round($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos)!=0){
							$ejecucion = number_format(($totalSegundoOrdenDiferidos+$totalSegundoOrdenComprometidosAislados + $filaProgramadostransito[$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"]."/".$partidaAnteriorSegundoOrden])*100/($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos),2,',','.')."%"; 
						}else{
							$ejecucion = "0,00%";	
						}
						
						if ( 	$totalSegundoOrdenProgramados!=0 ||
								$totalSegundoOrdenRecibidos!=0 ||
								$totalSegundoOrdenCedidos!=0 ||
								$totalSegundoOrdenDiferidos!=0 ||
								$totalSegundoOrdenComprometidos!=0 ||
								$totalSegundoOrdenComprometidosAislados!=0 ||
								$totalSegundoOrdenCausados!=0 ||
								$totalSegundoOrdenPagados!=0 ||
								$totalSegundoOrdenDisponible!=0) {
									
										
					?>
					
			
					<script>
						document.getElementById('nombre-partida-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= $nombrePartida?>';
						document.getElementById('partida-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= $partidaAnteriorSegundoOrden?>';
						document.getElementById('programado-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenProgramados,2,',','.')?>';
						document.getElementById('recibido-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenRecibidos,2,',','.')?>';
						document.getElementById('cedido-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenCedidos,2,',','.')?>';
						document.getElementById('presupuesto-modificado-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos,2,',','.')?>';
						document.getElementById('apartado-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenDiferidos,2,',','.')?>';
						document.getElementById('comprometido-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenComprometidos,2,',','.')?>';
						document.getElementById('comprometido-aislado-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenComprometidosAislados,2,',','.')?>';
						document.getElementById('causado-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenCausados,2,',','.')?>';
						document.getElementById('pagado-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenPagados,2,',','.')?>';
						document.getElementById('disponible-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenDisponible - $filaProgramadostransito[$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"]."/".$partidaAnteriorSegundoOrden] ,2,',','.')?>';
						document.getElementById('ejecucion-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= $ejecucion?>';
					</script>
					<?
						} else {
					?>
					<script>
						var element = document.getElementById('tr-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>');
						if ( element!=null ){
							element.parentNode.removeChild(element);
						}
					</script>
					<?php	
						}
						
					$partidaAnteriorSegundoOrden=substr($filaProgramados["part_id"], 0, 7).".00.00";
					if($accionEspecificaAnterior==$filaProgramados["id_accion_especifica"]){
					?>
					<tr class="normalNegroNegrita" style="color: #000000;" id="tr-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorSegundoOrden?>">
						<td id="nombre-partida-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorSegundoOrden?>"></td>
						<td id="partida-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorSegundoOrden?>" align="center"></td>
						<td align="right" id="programado-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="recibido-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="cedido-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="presupuesto-modificado-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="apartado-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorSegundoOrden?>"></td>
						<td align="right">	<?php echo number_format($filaProgramadostransito[$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"]."/".$partidaAnteriorSegundoOrden],2,',','.');?></td>

						<td align="right" id="comprometido-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="comprometido-aislado-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="causado-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="pagado-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="disponible-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="ejecucion-<?= $filaProgramados["id_accion_especifica"]."-".$partidaAnteriorSegundoOrden?>"></td>
					</tr>
					<?php 
					}
					$totalSegundoOrdenProgramados = 0;
					$totalSegundoOrdenRecibidos = 0;
					$totalSegundoOrdenCedidos = 0;
					$totalSegundoOrdenDiferidos = 0;
					$totalSegundoOrdenComprometidos = 0;
					$totalSegundoOrdenComprometidosAislados = 0;
					$totalSegundoOrdenCausados = 0;
					$totalSegundoOrdenPagados = 0;
					$totalSegundoOrdenDisponible = 0;
				}
				
				
								
								
						$params3[$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"] ]	= $filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"];
	/*					
echo "<pre>";
echo print_r($params3);
echo "</pre>";			
							*/		

				if($accionEspecificaAnterior!=$filaProgramados["id_accion_especifica"]){
					if($accionEspecificaAnterior!=""){
		
							 foreach ($params3 as $key => $valor){
								 $dato = $valor;

                     break;
						  }
								
			?>
						<tr class="normalNegrita">
							<td>Total Bs.</td>
							<td></td>
							<td align="right"><?= number_format($totalProgramados,2,',','.')?></td>
							<td align="right"><?= number_format($totalRecibidos,2,',','.')?></td>
							<td align="right"><?= number_format($totalCedidos,2,',','.')?></td>
							<td align="right"><?= number_format($totalProgramados+$totalRecibidos-$totalCedidos,2,',','.')?></td>
							<td align="right"><?= number_format($totalDiferidos,2,',','.')?></td>
							<td align="right"><?= number_format($filaProgramadostransito[$dato."/"."monto total"],2,',','.')?></td>
							<td align="right"><?= number_format($totalComprometidos,2,',','.')?></td>
							<td align="right"><?= number_format($totalComprometidosAislados,2,',','.')?></td>
							<td align="right"><?= number_format($totalCausados,2,',','.')?></td>
							<td align="right"><?= number_format($totalPagados,2,',','.')?></td>
							<td align="right"><?= number_format($totalDisponible - $filaProgramadostransito[$dato."/"."monto total"],2,',','.')?></td>
							<td align="right">
							<?php
							
							unset($params3[$dato]);
								if(round($totalProgramados+$totalRecibidos-$totalCedidos)!=0){
									echo number_format(($totalDiferidos+$totalComprometidosAislados +$filaProgramadostransito[$dato."/"."monto total"])*100/($totalProgramados+$totalRecibidos-$totalCedidos),2,',','.')."%"; 
								}else{
									echo "0,00%";	
								}					
							?>
							</td>
						</tr>
					</table>
						<?php
						$totalProgramados = 0;
						$totalRecibidos = 0;
						$totalCedidos = 0;
						$totalDiferidos = 0;
						$totalComprometidos = 0;
						$totalComprometidosAislados = 0;
						$totalCausados = 0;
						$totalPagados = 0;
						$totalDisponible = 0;
					}
					$accionEspecificaAnterior=$filaProgramados["id_accion_especifica"];
					$descripcionProyecto=$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"].": ".$filaProgramados["nombre"];
					?>
				<br />
				<table width="98%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
					<tr class="td_gray">
						<td colspan="14" class="normalNegroNegrita"><?= $descripcionProyecto?></td>
					</tr>
					<tr class="td_gray">
						<td class="normalNegroNegrita" width="200px">Denominaci&oacute;n</td>
						<td class="normalNegroNegrita" width="70px">Partida</td>
						<td class="normalNegroNegrita" width="110px">Presupuesto Ley</td>
						<td class="normalNegroNegrita" width="60px">Recibido</td>
						<td class="normalNegroNegrita" width="60px">Cedido</td>
						<td class="normalNegroNegrita" width="110px">Presupuesto modif.</td>
						<td class="normalNegroNegrita" width="60px">Apartado</td>
						<td class="normalNegroNegrita" width="60px">Apartado en tr&aacute;nsito (actual)</td>
						<td class="normalNegroNegrita" width="90px">Comprometido</td>
						<td class="normalNegroNegrita" width="90px">Compr. aislado</td>
						<td class="normalNegroNegrita" width="60px">Causado</td>
						<td class="normalNegroNegrita" width="60px">Pagado</td>
						<td class="normalNegroNegrita" width="60px">Disponible</td>
						<td class="normalNegroNegrita" width="70px">% Ejecuci&oacute;n</td>
					</tr>
			
					
				
					<tr class="normalNegroNegrita" style="color: #35519B;" id="tr-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 4).".00.00.00"?>">
						<td id="nombre-partida-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 4).".00.00.00"?>"></td>
						<td id="partida-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 4).".00.00.00"?>"></td>
						<td align="right" id="programado-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 4).".00.00.00"?>"></td>
						<td align="right" id="recibido-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 4).".00.00.00"?>"></td>
						<td align="right" id="cedido-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 4).".00.00.00"?>"></td>
						<td align="right" id="presupuesto-modificado-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 4).".00.00.00"?>"></td>
						<td align="right" id="apartado-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 4).".00.00.00"?>"></td>
						<td align="right">	<?php echo number_format($filaProgramadostransito[$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"]."/".substr($filaProgramados["part_id"], 0, 4).".00.00.00"],2,',','.');?>
</td>
						
						<td align="right" id="comprometido-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 4).".00.00.00"?>"></td>
						<td align="right" id="comprometido-aislado-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 4).".00.00.00"?>"></td>
						<td align="right" id="causado-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 4).".00.00.00"?>"></td>
						<td align="right" id="pagado-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 4).".00.00.00"?>"></td>
						<td align="right" id="disponible-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 4).".00.00.00"?>"></td>
						<td align="right" id="ejecucion-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 4).".00.00.00"?>"></td>
					</tr>
					<tr class="normalNegroNegrita" style="color: #000000;" id="tr-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 7).".00.00"?>">
						<td id="nombre-partida-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 7).".00.00"?>"></td>
						<td id="partida-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 7).".00.00"?>" align="center"></td>
						<td align="right" id="programado-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 7).".00.00"?>"></td>
						<td align="right" id="recibido-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 7).".00.00"?>"></td>
						<td align="right" id="cedido-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 7).".00.00"?>"></td>
						<td align="right" id="presupuesto-modificado-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 7).".00.00"?>"></td>
						<td align="right" id="apartado-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 7).".00.00"?>"></td>
						<td align='right'><?php echo number_format($filaProgramadostransito[$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"]."/".substr($filaProgramados["part_id"], 0, 7).".00.00"],2,',','.');?></td>
						<td align="right" id="comprometido-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 7).".00.00"?>"></td>
						<td align="right" id="comprometido-aislado-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 7).".00.00"?>"></td>
						<td align="right" id="causado-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 7).".00.00"?>"></td>
						<td align="right" id="pagado-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 7).".00.00"?>"></td>
						<td align="right" id="disponible-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 7).".00.00"?>"></td>
						<td align="right" id="ejecucion-<?= $filaProgramados["id_accion_especifica"]."-".substr($filaProgramados["part_id"], 0, 7).".00.00"?>"></td>
					</tr>
			
			<?php
				}

				$programado = $filaProgramados['monto_programado'];
				$recibido = 0;
				$cedido = 0;
				$diferido = 0;
				$comprometido = 0;
				$comprometidoAislado = 0;
				$causado = 0;
				$pagado = 0;
				
				//se cambio id_accion_especifica por centro_costo.
				if($filaRecibidos==null){
					$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
				}
				if(	$filaProgramados["part_id"]==$filaRecibidos["part_id"] &&
					$filaProgramados["centro_costo"]==$filaRecibidos["centro_costo"]){
					$recibido = $filaRecibidos["monto_recibido"];
					$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
				}else if($filaProgramados["part_id"]>$filaRecibidos["part_id"] &&
						$filaProgramados["centro_costo"]==$filaRecibidos["centro_costo"]){
					do{
						$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
					}while(	$filaProgramados["centro_costo"]==$filaRecibidos["centro_costo"] &&
							$filaProgramados["part_id"]>$filaRecibidos["part_id"]);
					if($filaProgramados["part_id"]==$filaRecibidos["part_id"]){
						$recibido = $filaRecibidos["monto_recibido"];
						$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
					}
				}
				
				if($filaCedidos==null){
					$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
				}
				if($filaProgramados["part_id"]==$filaCedidos["part_id"] &&
				$filaProgramados["centro_costo"]==$filaCedidos["centro_costo"]){
					$cedido = $filaCedidos["monto_cedido"];
					$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
				}else if($filaProgramados["centro_costo"]==$filaCedidos["centro_costo"] &&
				$filaProgramados["part_id"]>$filaCedidos["part_id"]){
					do{
						$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
					}while(	$filaProgramados["centro_costo"]==$filaCedidos["centro_costo"] &&
					$filaProgramados["part_id"]>$filaCedidos["part_id"]);
					if($filaProgramados["part_id"]==$filaCedidos["part_id"]){
						$cedido = $filaCedidos["monto_cedido"];
						$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
					}
				}

				$montoAjustado=($programado+$recibido)-$cedido;

				if($filaDiferidos==null){
					
					$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
				}/** buscar error
				
			if($filaDiferidosEnTransito==null){
					
					$filaDiferidosEnTransito=pg_fetch_array($resultadoMontosEnTransito);
				}
*/
				
				if($filaProgramados["part_id"]==$filaDiferidos["part_id"] &&
				$filaProgramados["centro_costo"]==$filaDiferidos["centro_costo"]){
					$diferido = $filaDiferidos["monto_diferido"];
					$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
				}else if($filaProgramados["centro_costo"]==$filaDiferidos["centro_costo"] &&
				$filaProgramados["part_id"]>$filaDiferidos["part_id"]){
					do{
						$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
					}while(	$filaProgramados["centro_costo"]==$filaDiferidos["centro_costo"] &&
					$filaProgramados["part_id"]>$filaDiferidos["part_id"]);
					if($filaProgramados["part_id"]==$filaDiferidos["part_id"]){
						$diferido = $filaDiferidos["monto_diferido"];
						$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
					}
				}
				
				if($filaComprometidos==null){
					$filaComprometidos=pg_fetch_array($resultadoMontosComprometidos);
				}
				if($filaProgramados["part_id"]==$filaComprometidos["part_id"] &&
				$filaProgramados["centro_costo"]==$filaComprometidos["centro_costo"]){
					$comprometido = $filaComprometidos["monto_comprometido"];
					$filaComprometidos=pg_fetch_array($resultadoMontosComprometidos);
				}else if($filaProgramados["centro_costo"]==$filaComprometidos["centro_costo"] &&
				$filaProgramados["part_id"]>$filaComprometidos["part_id"]){
					do{
						$filaComprometidos=pg_fetch_array($resultadoMontosComprometidos);
					}while(	$filaProgramados["centro_costo"]==$filaComprometidos["centro_costo"] &&
					$filaProgramados["part_id"]>$filaComprometidos["part_id"]);
					if($filaProgramados["part_id"]==$filaComprometidos["part_id"]){
						$comprometido = $filaComprometidos["monto_comprometido"];
						$filaComprometidos=pg_fetch_array($resultadoMontosComprometidos);
					}
				}

				if($filaComprometidosAislados==null){
					$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
				}
				if($filaProgramados["part_id"]==$filaComprometidosAislados["part_id"] &&
				$filaProgramados["centro_costo"]==$filaComprometidosAislados["centro_costo"]){
					$comprometidoAislado = $filaComprometidosAislados["monto_comprometido_aislado"];
					$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
				}else if($filaProgramados["centro_costo"]==$filaComprometidosAislados["centro_costo"] &&
				$filaProgramados["part_id"]>$filaComprometidosAislados["part_id"]){
					do{
						$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
					}while(	$filaProgramados["centro_costo"]==$filaComprometidosAislados["centro_costo"] &&
					$filaProgramados["part_id"]>$filaComprometidosAislados["part_id"]);
					if($filaProgramados["part_id"]==$filaComprometidosAislados["part_id"]){
						$comprometidoAislado = $filaComprometidosAislados["monto_comprometido_aislado"];
						$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
					}
				}

				if($filaCausados==null){
					$filaCausados=pg_fetch_array($resultadoMontosCausados);
				}		
				if($filaPagados==null){
					$filaPagados=pg_fetch_array($resultadoMontosPagados);
				}
				if($filaProgramados["centro_costo"]==$filaCausados["centro_costo"] &&
				$filaProgramados["part_id"]==$filaCausados["part_id"]){
					$causado = $filaCausados["monto_causado"];
					$filaCausados=pg_fetch_array($resultadoMontosCausados);
				}else if($filaProgramados["centro_costo"]==$filaCausados["centro_costo"] &&
				$filaProgramados["part_id"]>$filaCausados["part_id"]){
					do{
						//IMPRIMIR CAUSADO CON CEROS EN LAS DEMAS COLUMNAS
						if($filaProgramados["centro_costo"]==$filaCausados["centro_costo"] && $filaProgramados["centro_costo"]==$filaPagados["centro_costo"] &&
							$filaCausados["part_id"]==$filaPagados["part_id"]){
							//IMPRIMIR CAUSADO Y PAGADO
							$causado = $filaCausados["monto_causado"];
							$pagado = $filaPagados["monto_pagado"];
							if($partidaAnteriorSegundoOrden==substr($filaCausados["part_id"], 0, 7).".00.00"){
								$totalSegundoOrdenCausados += $causado;
								$totalSegundoOrdenPagados += $pagado;
							}
							if($partidaAnteriorPrimerOrden==substr($filaCausados["part_id"], 0, 4).".00.00.00"){
								$totalPrimerOrdenCausados += $causado;
								$totalPrimerOrdenPagados += $pagado;
							}
							$totalCausados += $causado;
							$totalPagados += $pagado;
							
							$diferencias .= "<tr class='normal' style='color: red;'>
												<td>".trim($filaProgramados['partida'])."</td>
												<td align='right'>".trim($filaCausados['part_id'])."</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>
													<a style='color: red;' target='_blank' href='detalleCausado.php?partida=".$filaCausados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$causado."&aesp=".$filaCausados['id_accion_especifica']."'>".number_format($causado,2,',','.')."</a>
												</td>
												<td align='right'>
													<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
												</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
											</tr>";
							$filaCausados=pg_fetch_array($resultadoMontosCausados);
							$filaPagados=pg_fetch_array($resultadoMontosPagados);
						}else if($filaProgramados["centro_costo"]==$filaPagados["centro_costo"] &&
							$filaProgramados["part_id"]>$filaPagados["part_id"]){
							//IMPRIMIR PAGADO
							$pagado = $filaPagados["monto_pagado"];
							if($partidaAnteriorSegundoOrden==substr($filaPagados["part_id"], 0, 7).".00.00"){
								$totalSegundoOrdenPagados += $pagado;
							}
							if($partidaAnteriorPrimerOrden==substr($filaPagados["part_id"], 0, 4).".00.00.00"){
								$totalPrimerOrdenPagados += $pagado;
							}
							$totalPagados += $pagado;
							
							$diferencias .= "<tr class='normal' style='color: red;'>
												<td>".trim($filaProgramados['partida'])."</td>
												<td align='right'>".trim($filaPagados['part_id'])."</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>
													<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
												</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
											</tr>";							
							$filaPagados=pg_fetch_array($resultadoMontosPagados);
						}else if($filaProgramados["centro_costo"]==$filaCausados["centro_costo"] &&
							$filaProgramados["part_id"]>$filaCausados["part_id"]){
							//IMPRIMIR CAUSADO
							$causado = $filaCausados["monto_causado"];
							if($partidaAnteriorSegundoOrden==substr($filaCausados["part_id"], 0, 7).".00.00"){
								$totalSegundoOrdenCausados += $causado;
							}
							if($partidaAnteriorPrimerOrden==substr($filaCausados["part_id"], 0, 4).".00.00.00"){
								$totalPrimerOrdenCausados += $causado;
							}
							$totalCausados += $causado;

							$diferencias .= "<tr class='normal' style='color: red;'>
												<td>".trim($filaProgramados['partida'])."</td>
												<td align='right'>".trim($filaCausados['part_id'])."</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>
													<a style='color: red;' target='_blank' href='detalleCausado.php?partida=".$filaCausados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$causado."&aesp=".$filaCausados['id_accion_especifica']."'>".number_format($causado,2,',','.')."</a>
												</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
											</tr>";
							$filaCausados=pg_fetch_array($resultadoMontosCausados);
						}
					}while(	$filaProgramados["centro_costo"]==$filaCausados["centro_costo"] &&
					$filaProgramados["part_id"]>$filaCausados["part_id"]);
					if($filaProgramados["part_id"]==$filaCausados["part_id"]){
						$causado = $filaCausados["monto_causado"];
						$filaCausados=pg_fetch_array($resultadoMontosCausados);
					}
				}

				if($filaProgramados["part_id"]==$filaPagados["part_id"] &&
				$filaProgramados["centro_costo"]==$filaPagados["centro_costo"]){
					$pagado = $filaPagados["monto_pagado"];
					$filaPagados=pg_fetch_array($resultadoMontosPagados);
				}else if($filaProgramados["centro_costo"]==$filaPagados["centro_costo"] &&
				$filaProgramados["part_id"]>$filaPagados["part_id"]){
					do{
						if($filaProgramados["centro_costo"]==$filaPagados["centro_costo"] &&
							$filaProgramados["part_id"]>$filaPagados["part_id"]){
							//IMPRIMIR PAGADO
							$pagado = $filaPagados["monto_pagado"];
							if($partidaAnteriorSegundoOrden==substr($filaPagados["part_id"], 0, 7).".00.00"){
								$totalSegundoOrdenPagados += $pagado;
							}
							if($partidaAnteriorPrimerOrden==substr($filaPagados["part_id"], 0, 4).".00.00.00"){
								$totalPrimerOrdenPagados += $pagado;
							}
							$totalPagados += $pagado;
							$diferencias .= "<tr class='normal' style='color: red;'>
												<td>".trim($filaProgramados['partida'])."</td>
												<td align='right'>".trim($filaPagados['part_id'])."</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>
													<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
												</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
											</tr>";
							$filaPagados=pg_fetch_array($resultadoMontosPagados);
						}
					}while(	$filaProgramados["centro_costo"]==$filaPagados["centro_costo"] &&
					$filaProgramados["part_id"]>$filaPagados["part_id"]);
					if($filaProgramados["part_id"]==$filaPagados["part_id"]){
						$pagado = $filaPagados["monto_pagado"];
						$filaPagados=pg_fetch_array($resultadoMontosPagados);
					}
				}

				if($diferido>0){
					$montoDisponible=($montoAjustado)-($diferido)-$comprometidoAislado;
				}else if($comprometidoAislado>0){
					$montoDisponible=($montoAjustado)-($comprometidoAislado);
				}else{
					$montoDisponible=($montoAjustado);
				}
				
				if ( 	$programado!=0 ||
						$recibido!=0 ||
						$cedido!=0 ||
						$diferido!=0 ||
						$comprometido!=0 ||
						$comprometidoAislado!=0 ||
						$causado!=0 ||
						$pagado!=0 ||
						$montoDisponible!=0 ||
						$mostrarTodasLasPartidas=="true" ) {

					$totalProgramados += $programado;
					$totalRecibidos +=  $recibido;
					$totalCedidos += $cedido;
					$totalDiferidos += $diferido;
					$totalComprometidos += $comprometido;
					$totalComprometidosAislados += $comprometidoAislado;
					$totalCausados += $causado;
					$totalPagados += $pagado;
					$totalDisponible += $montoDisponible;
					
					$totalPrimerOrdenProgramados += $programado;
					$totalPrimerOrdenRecibidos +=  $recibido;
					$totalPrimerOrdenCedidos += $cedido;
					$totalPrimerOrdenDiferidos += $diferido;
					$totalPrimerOrdenComprometidos += $comprometido;
					$totalPrimerOrdenComprometidosAislados += $comprometidoAislado;
					$totalPrimerOrdenCausados += $causado;
					$totalPrimerOrdenPagados += $pagado;
					$totalPrimerOrdenDisponible += $montoDisponible;
					
					$totalSegundoOrdenProgramados += $programado;
					$totalSegundoOrdenRecibidos +=  $recibido;
					$totalSegundoOrdenCedidos += $cedido;
					$totalSegundoOrdenDiferidos += $diferido;
					$totalSegundoOrdenComprometidos += $comprometido;
					$totalSegundoOrdenComprometidosAislados += $comprometidoAislado;
					$totalSegundoOrdenCausados += $causado;
					$totalSegundoOrdenPagados += $pagado;
					$totalSegundoOrdenDisponible += $montoDisponible;
			?>
			<tr class="normal">
				<td><?= trim($filaProgramados['partida'])?></td>
				<td align="right"><?=trim($filaProgramados['part_id'])?></td>
				<td align="right"><?= number_format($programado,2,',','.')?></td>
				<td align="right"><a target="_blank"
					href="detalleRecibido.php?partida=<?=$filaProgramados['part_id']?>&proy=<?=$idProyectoAccion?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFin?>&tipo=<?=$tipoImputacion?>&consolidado=0&monto=<?=$recibido?>&aesp=<?=$filaProgramados["id_accion_especifica"]?>"><?= number_format($recibido,2,',','.')?></a>
				</td>
				<td align="right"><a target="_blank"
					href="detalleCedido.php?partida=<?=$filaProgramados['part_id']?>&proy=<?=$idProyectoAccion?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFin?>&tipo=<?=$tipoImputacion?>&consolidado=0&monto=<?=$cedido?>&aesp=<?=$filaProgramados["id_accion_especifica"]?>"><?= number_format($cedido,2,',','.')?></a>
				</td>
				<td align="right"><?= number_format($programado+$recibido-$cedido,2,',','.')?></td>
				<td align="right"><a target="_blank"
					href="detalleCompromisoApartado.php?partida=<?=$filaProgramados['part_id']?>&proy=<?=$idProyectoAccion?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFin?>&tipo=<?=$tipoImputacion?>&consolidado=0&monto=<?=$diferido?>&aesp=<?=$filaProgramados["id_accion_especifica"]?>"><?= number_format($diferido,2,',','.')?></a>
				</td>
				
				
				<td align="right">
				
				<a target="_blank"
					href="detalleApartadoTransitoPcta.php?partida=<?=$filaProgramados['part_id']?>&proy=<?=$idProyectoAccion?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFin?>&tipo=<?=$tipoImputacion?>&aesp=<?=$filaProgramados["id_accion_especifica"]?>">
			
				<?php echo number_format($filaProgramadostransito[$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"]."/".$filaProgramados['part_id']],2,',','.');?>
				</a>
				</td>

				
				
				<td align="right"><a target="_blank"
					href="detalleCompromiso.php?partida=<?=$filaProgramados['part_id']?>&proy=<?=$idProyectoAccion?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFin?>&tipo=<?=$tipoImputacion?>&consolidado=0&monto=<?=$comprometido?>&aesp=<?=$filaProgramados["id_accion_especifica"]?>"><?= number_format($comprometido,2,',','.')?></a>
				</td>
				<td align="right"><a target="_blank"
					href="detalleCompromisoAislado.php?partida=<?=$filaProgramados['part_id']?>&proy=<?=$idProyectoAccion?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFin?>&tipo=<?=$tipoImputacion?>&consolidado=0&monto=<?=$comprometidoAislado?>&aesp=<?=$filaProgramados["id_accion_especifica"]?>"><?= number_format($comprometidoAislado,2,',','.')?></a>
				</td>
				<td align="right"><a target="_blank"
					href="detalleCausado.php?partida=<?=$filaProgramados['part_id']?>&proy=<?=$idProyectoAccion?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFin?>&tipo=<?=$tipoImputacion?>&consolidado=0&monto=<?=$causado?>&aesp=<?=$filaProgramados["id_accion_especifica"]?>"><?= number_format($causado,2,',','.')?></a>
				</td>
				<td align="right"><a target="_blank"
					href="detallePagado.php?partida=<?=$filaProgramados['part_id']?>&proy=<?=$idProyectoAccion?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFin?>&tipo=<?=$tipoImputacion?>&consolidado=0&monto=<?=$pagado?>&aesp=<?=$filaProgramados["id_accion_especifica"]?>"><?= number_format($pagado,2,',','.')?></a>
				</td>
				<td align="right"><?= number_format($montoDisponible - $filaProgramadostransito[$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"]."/".$filaProgramados['part_id']] ,2,',','.')?></td>
				<td align="right">
				<?php
					if(round($programado+$recibido-$cedido)!=0){
						echo number_format(($diferido+$comprometidoAislado+$filaProgramadostransito[$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"]."/".$filaProgramados['part_id']] )*100/($programado+$recibido-$cedido),2,',','.')."%";
					}else{
						echo "0,00%";	
					}					
				?>
				</td>
			</tr>
			<?
				}
			}
			
			if($partidaAnteriorPrimerOrden!=""){
				$nombrePartida = "";
				$iPartidasPrimariasYSecundarias = 0;						
				while($iPartidasPrimariasYSecundarias<$tamanoPartidasPrimariasYSecundarias) {
					if($partidaAnteriorPrimerOrden==$arregloPartidas[$iPartidasPrimariasYSecundarias][0]){
						$nombrePartida = $arregloPartidas[$iPartidasPrimariasYSecundarias][1];
						break;
					}
					$iPartidasPrimariasYSecundarias++;
				}
				
				if(round($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos)!=0){
					$ejecucion = number_format(($totalPrimerOrdenDiferidos+$totalPrimerOrdenComprometidosAislados)*100/($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos),2,',','.')."%";
				}else{
					$ejecucion = "0,00%";	
				}
				
				if ( 	$totalPrimerOrdenProgramados!=0 ||
						$totalPrimerOrdenRecibidos!=0 ||
						$totalPrimerOrdenCedidos!=0 ||
						$totalPrimerOrdenDiferidos!=0 ||
						$totalPrimerOrdenComprometidos!=0 ||
						$totalPrimerOrdenComprometidosAislados!=0 ||
						$totalPrimerOrdenCausados!=0 ||
						$totalPrimerOrdenPagados!=0 ||
						$totalPrimerOrdenDisponible!=0 ||
						$mostrarTodasLasPartidas=="true" ) {
			?>
			<script>
				document.getElementById('nombre-partida-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= $nombrePartida?>';
				document.getElementById('partida-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= $partidaAnteriorPrimerOrden?>';
				document.getElementById('programado-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenProgramados,2,',','.')?>';
				document.getElementById('recibido-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenRecibidos,2,',','.')?>';
				document.getElementById('cedido-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenCedidos,2,',','.')?>';
				document.getElementById('presupuesto-modificado-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos,2,',','.')?>';
				document.getElementById('apartado-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenDiferidos,2,',','.')?>';
				document.getElementById('comprometido-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenComprometidos,2,',','.')?>';
				document.getElementById('comprometido-aislado-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenComprometidosAislados,2,',','.')?>';
				document.getElementById('causado-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenCausados,2,',','.')?>';
				document.getElementById('pagado-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenPagados,2,',','.')?>';
				document.getElementById('disponible-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenDisponible,2,',','.')?>';
				document.getElementById('ejecucion-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>').innerHTML = '<?= $ejecucion?>';
			</script>
			<?php 
				} else {
			?>
			<script>
				var element = document.getElementById('tr-<?= $accionEspecificaAnterior."-".$partidaAnteriorPrimerOrden?>');
				if ( element!=null ){
					element.parentNode.removeChild(element);
				}
			</script>
			<?php	
				}
			}

			if($partidaAnteriorSegundoOrden!=""){
				if($accionEspecificaAnterior==$filaCausados["id_accion_especifica"]){
					do{
						if(	$accionEspecificaAnterior==$filaPagados["id_accion_especifica"] &&
							$filaProgramados["part_id"]>$filaCausados["part_id"] && 
							$filaCausados["part_id"]==$filaPagados["part_id"]){
							//IMPRIMIR CAUSADO Y PAGADO
							$causado = $filaCausados["monto_causado"];
							$pagado = $filaPagados["monto_pagado"];
							if($partidaAnteriorSegundoOrden==substr($filaCausados["part_id"], 0, 7).".00.00"){
								$totalSegundoOrdenCausados += $causado;
								$totalSegundoOrdenPagados += $pagado;
							}
							if($partidaAnteriorPrimerOrden==substr($filaCausados["part_id"], 0, 4).".00.00.00"){
								$totalPrimerOrdenCausados += $causado;
								$totalPrimerOrdenPagados += $pagado;
							}
							$totalCausados += $causado;
							$totalPagados += $pagado;
							
							$diferencias .= "<tr class='normal' style='color: red;'>
												<td>".trim($filaProgramados['partida'])."</td>
												<td align='right'>".trim($filaCausados['part_id'])."</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>
													<a style='color: red;' target='_blank' href='detalleCausado.php?partida=".$filaCausados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$causado."&aesp=".$filaCausados['id_accion_especifica']."'>".number_format($causado,2,',','.')."</a>
												</td>
												<td align='right'>
													<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
												</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
											</tr>";
							$filaCausados=pg_fetch_array($resultadoMontosCausados);
							$filaPagados=pg_fetch_array($resultadoMontosPagados);
						}else if($accionEspecificaAnterior==$filaPagados["id_accion_especifica"] &&
							$filaProgramados["part_id"]>$filaPagados["part_id"] &&
							$filaCausados["part_id"]>$filaPagados["part_id"]){
							//IMPRIMIR PAGADO
							$pagado = $filaPagados["monto_pagado"];
							if($partidaAnteriorSegundoOrden==substr($filaPagados["part_id"], 0, 7).".00.00"){
								$totalSegundoOrdenPagados += $pagado;
							}
							if($partidaAnteriorPrimerOrden==substr($filaPagados["part_id"], 0, 4).".00.00.00"){
								$totalPrimerOrdenPagados += $pagado;
							}
							$totalPagados += $pagado;
							
							$diferencias .= "<tr class='normal' style='color: red;'>
												<td>".trim($filaProgramados['partida'])."</td>
												<td align='right'>".trim($filaPagados['part_id'])."</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>
													<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
												</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
											</tr>";							
							$filaPagados=pg_fetch_array($resultadoMontosPagados);
						}else if($accionEspecificaAnterior==$filaPagados["id_accion_especifica"] &&
							$filaProgramados["part_id"]>$filaCausados["part_id"] && 
							$filaCausados["part_id"]<$filaPagados["part_id"]){
							//IMPRIMIR CAUSADO
							$causado = $filaCausados["monto_causado"];
							if($partidaAnteriorSegundoOrden==substr($filaCausados["part_id"], 0, 7).".00.00"){
								$totalSegundoOrdenCausados += $causado;
							}
							if($partidaAnteriorPrimerOrden==substr($filaCausados["part_id"], 0, 4).".00.00.00"){
								$totalPrimerOrdenCausados += $causado;
							}
							$totalCausados += $causado;
							
							$diferencias .= "<tr class='normal' style='color: red;'>
												<td>".trim($filaProgramados['partida'])."</td>
												<td align='right'>".trim($filaCausados['part_id'])."</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>
													<a style='color: red;' target='_blank' href='detalleCausado.php?partida=".$filaCausados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$causado."&aesp=".$filaCausados['id_accion_especifica']."'>".number_format($causado,2,',','.')."</a>
												</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
											</tr>";
							$filaCausados=pg_fetch_array($resultadoMontosCausados);
						}
					}while(	$accionEspecificaAnterior==$filaCausados["id_accion_especifica"] && $filaProgramados["part_id"]>$filaCausados["part_id"]);
				}
				if($accionEspecificaAnterior==$filaPagados["id_accion_especifica"] && $filaProgramados["part_id"]>$filaPagados["part_id"]){
					do{
						//IMPRIMIR PAGADO
						$pagado = $filaPagados["monto_pagado"];
						if($partidaAnteriorSegundoOrden==substr($filaPagados["part_id"], 0, 7).".00.00"){
							$totalSegundoOrdenPagados += $pagado;
						}
						if($partidaAnteriorPrimerOrden==substr($filaPagados["part_id"], 0, 4).".00.00.00"){
							$totalPrimerOrdenPagados += $pagado;
						}
						$totalPagados += $pagado;
						
						$diferencias .= "<tr class='normal' style='color: red;'>
											<td>".trim($filaProgramados['partida'])."</td>
											<td align='right'>".trim($filaPagados['part_id'])."</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>
												<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
											</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
										</tr>";
						$filaPagados=pg_fetch_array($resultadoMontosPagados);
					}while(	$accionEspecificaAnterior==$filaPagados["id_accion_especifica"] && $filaProgramados["part_id"]>$filaPagados["part_id"]);
				}
				echo $diferencias;
				$diferencias = "";
				$nombrePartida = "";
				$iPartidasPrimariasYSecundarias = 0;						
				while($iPartidasPrimariasYSecundarias<$tamanoPartidasPrimariasYSecundarias) {
					if($partidaAnteriorSegundoOrden==$arregloPartidas[$iPartidasPrimariasYSecundarias][0]){
						$nombrePartida = $arregloPartidas[$iPartidasPrimariasYSecundarias][1];
						break;
					}
					$iPartidasPrimariasYSecundarias++;
				}
				
				if(round($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos)!=0){
					$ejecucion = number_format(($totalSegundoOrdenDiferidos+$totalSegundoOrdenComprometidosAislados)*100/($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos),2,',','.')."%"; 
				}else{
					$ejecucion = "0,00%";	
				}
				
				if ( 	$totalSegundoOrdenProgramados!=0 ||
						$totalSegundoOrdenRecibidos!=0 ||
						$totalSegundoOrdenCedidos!=0 ||
						$totalSegundoOrdenDiferidos!=0 ||
						$totalSegundoOrdenComprometidos!=0 ||
						$totalSegundoOrdenComprometidosAislados!=0 ||
						$totalSegundoOrdenCausados!=0 ||
						$totalSegundoOrdenPagados!=0 ||
						$totalSegundoOrdenDisponible!=0 ||
						$mostrarTodasLasPartidas=="true" ) {
				?>
				<script>
					document.getElementById('nombre-partida-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= $nombrePartida?>';
					document.getElementById('partida-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= $partidaAnteriorSegundoOrden?>';
					document.getElementById('programado-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenProgramados,2,',','.')?>';
					document.getElementById('recibido-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenRecibidos,2,',','.')?>';
					document.getElementById('cedido-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenCedidos,2,',','.')?>';
					document.getElementById('presupuesto-modificado-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos,2,',','.')?>';
					document.getElementById('apartado-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenDiferidos,2,',','.')?>';
					document.getElementById('comprometido-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenComprometidos,2,',','.')?>';
					document.getElementById('comprometido-aislado-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenComprometidosAislados,2,',','.')?>';
					document.getElementById('causado-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenCausados,2,',','.')?>';
					document.getElementById('pagado-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenPagados,2,',','.')?>';
					document.getElementById('disponible-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenDisponible,2,',','.')?>';
					document.getElementById('ejecucion-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>').innerHTML = '<?= $ejecucion?>';
				</script>
			<?
				} else {
			?>
			<script>
				var element = document.getElementById('tr-<?= $accionEspecificaAnterior."-".$partidaAnteriorSegundoOrden?>');
				if ( element!=null ){
					element.parentNode.removeChild(element);
				}
			</script>
			<?php	
				}
			}
			
				$params3[$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"] ]	= $filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"];
			
					foreach ($params3 as $key => $valor){
							
				if($valor == '/'){
					break;
				}
                      $dato = $valor;
						  }
											
					
			
			?>
			<tr class="normalNegrita">
				<td>Total Bs.</td>
				<td></td>
				<td align="right"><?= number_format($totalProgramados,2,',','.')?></td>
				<td align="right"><?= number_format($totalRecibidos,2,',','.')?></td>
				<td align="right"><?= number_format($totalCedidos,2,',','.')?></td>
				<td align="right"><?= number_format($totalProgramados+$totalRecibidos-$totalCedidos,2,',','.')?></td>
				<td align="right"><?= number_format($totalDiferidos,2,',','.')?></td>
				
				<td align="right"><?= number_format($filaProgramadostransito[$dato."/"."monto total"],2,',','.')?></td>
				<td align="right"><?= number_format($totalComprometidos,2,',','.')?></td>
				<td align="right"><?= number_format($totalComprometidosAislados,2,',','.')?></td>
				<td align="right"><?= number_format($totalCausados,2,',','.')?></td>
				<td align="right"><?= number_format($totalPagados,2,',','.')?></td>
				<td align="right"><?= number_format($totalDisponible - $filaProgramadostransito[$dato."/"."monto total"],2,',','.')?></td>
				<td align="right">
				<?php
					if(round($totalProgramados+$totalRecibidos-$totalCedidos)!=0){
						echo number_format(($totalDiferidos+$totalComprometidosAislados +$filaProgramadostransito[$dato."/"."monto total"])*100/($totalProgramados+$totalRecibidos-$totalCedidos),2,',','.')."%"; 
					}else{
						echo "0,00%";	
					}					
				?>
				</td>
			</tr>
		</table>
			<?php
		}else{
			?>
		<br/>
		<table width="98%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="titular">
				<td colspan="13" align="center" height="40px" valign="middle">No se encontraron resultados</td>
			</tr>
		</table>
		<?php
		}
	}else{// if($opcionConsolidar=="1"){
		?>
		<br/>
		<table width="98%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<?php
		//MONTOS PROGRAMADOS
		if($mostrarTodasLasPartidas=="true"){
			$query=	"SELECT ".
						"part_id, ".
						"partida, ".
						"COALESCE(SUM(monto_programado),0) as monto_programado ".
					"FROM ".
					"( ".
					"SELECT ".
						"sf1125d.part_id, ".
						"sp.part_nombre AS partida, ".
						"COALESCE(SUM(sf1125d.fodt_monto),0) as monto_programado ".
					"FROM sai_fo1125_det sf1125d, sai_forma_1125 sf1125, sai_partida sp, ".
						"(";
			if($opcionConsolidar=="1" || $centroGestor!=""){
				if($tipoImputacion=="1"){//proyecto
					$query .= 	"SELECT ".
									"spae.proy_id as id_proyecto_accion, ".
									"spae.paes_id as id_accion_especifica ".
								"FROM sai_proy_a_esp spae ".
								"WHERE ".
									"spae.proy_id = '".$idProyectoAccion."' AND ";
					if($centroGestor && $centroGestor!=""){
						$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
					}
					$query .=		"spae.pres_anno = ".$anno_pres." ";
				}else if($tipoImputacion=="0"){//accion centralizada
					$query .= 	"SELECT ".
									"sae.acce_id as id_proyecto_accion, ".
									"sae.aces_id as id_accion_especifica ".
								"FROM sai_acce_esp sae ".
								"WHERE ".
									"sae.acce_id = '".$idProyectoAccion."' AND ";
					if($centroGestor && $centroGestor!=""){
						$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
					}
					$query .=		"sae.pres_anno = ".$anno_pres." ";
				}			
			}else if($opcionConsolidar=="2"){
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.pres_anno = ".$anno_pres." ";
			}else if($opcionConsolidar=="3"){
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.pres_anno = ".$anno_pres." ".
							"UNION ".
							"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.pres_anno = ".$anno_pres." ";
			}
			$query.=	") as s ".
					"WHERE ";
			if($opcionConsolidar=="1" || $centroGestor!=""){
				$query.=	"sf1125.form_tipo = '".$tipoImputacion."' AND ".
							"sf1125.form_id_p_ac = '".$idProyectoAccion."' AND ";
			}
			$query.=	"sf1125.form_id_p_ac = s.id_proyecto_accion AND ".
						"sf1125.form_id_aesp = s.id_accion_especifica AND ".
						"sf1125.pres_anno = ".$anno_pres." AND ".
						"sf1125.form_id = sf1125d.form_id AND ".
						"sf1125.pres_anno = sf1125d.pres_anno AND ".
						"sf1125.esta_id <> 15 AND sf1125.esta_id <> 2 AND ".
						"sf1125d.fodt_mes BETWEEN 1 AND 12 AND ".
						"sf1125d.part_id LIKE '".$codigoPartida."%' AND ".
						"sf1125d.part_id NOT LIKE '4.11.0%' AND ".
						"sf1125d.part_id = sp.part_id AND ".
						"sp.esta_id = ".$estadoActivo." AND ".
						"sf1125d.pres_anno = sp.pres_anno AND ".
						"sf1125.form_fecha < to_date('".$fechaFin."', 'DD/MM/YYYY')+1 ".
					"GROUP BY sf1125d.part_id, sp.part_nombre ".
					"UNION ".
					"SELECT ".
						"sp.part_id, ".
						"sp.part_nombre AS partida, ".
						"0 as monto_programado ".
					"FROM sai_partida sp, ".
						"(";
			if($opcionConsolidar=="1" || $centroGestor!=""){
				if($tipoImputacion=="1"){//proyecto
					$query .= 	"SELECT ".
									"spae.proy_id as id_proyecto_accion, ".
									"spae.paes_id as id_accion_especifica ".
								"FROM sai_proy_a_esp spae ".
								"WHERE ".
									"spae.proy_id = '".$idProyectoAccion."' AND ";
					if($centroGestor && $centroGestor!=""){
						$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
					}
					$query .=		"spae.pres_anno = ".$anno_pres." ";
				}else if($tipoImputacion=="0"){//accion centralizada
					$query .= 	"SELECT ".
									"sae.acce_id as id_proyecto_accion, ".
									"sae.aces_id as id_accion_especifica ".
								"FROM sai_acce_esp sae ".
								"WHERE ".
									"sae.acce_id = '".$idProyectoAccion."' AND ";
					if($centroGestor && $centroGestor!=""){
						$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
					}
					$query .=		"sae.pres_anno = ".$anno_pres." ";
				}			
			}else if($opcionConsolidar=="2"){
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.pres_anno = ".$anno_pres." ";
			}else if($opcionConsolidar=="3"){
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.pres_anno = ".$anno_pres." ".
							"UNION ".
							"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.pres_anno = ".$anno_pres." ";
			}
			$query.=	") as s ".
					"WHERE ".
						"sp.pres_anno = ".$anno_pres." AND ".
						"sp.part_id LIKE '".$codigoPartida."%' AND ".
						"sp.part_id NOT LIKE '4.11.0%' AND ".
						"sp.esta_id = ".$estadoActivo." AND ".
						"sp.part_id NOT LIKE '%.00.00' ".
					"GROUP BY sp.part_id, sp.part_nombre ".
					") AS s ".
					"GROUP BY s.part_id, s.partida ".
					"ORDER BY s.part_id";
			$resultadoMontosProgramados=pg_query($query) or die("Error en los montos programados");
		}else{
			$query=	"SELECT ".
						"sf1125d.part_id, ".
						"sp.part_nombre AS partida, ".
						"COALESCE(SUM(sf1125d.fodt_monto),0) as monto_programado ".
					"FROM sai_fo1125_det sf1125d, sai_forma_1125 sf1125, sai_partida sp, ".
						"(";
			if($opcionConsolidar=="1" || $centroGestor!=""){
				if($tipoImputacion=="1"){//proyecto
					$query .= 	"SELECT ".
									"spae.proy_id as id_proyecto_accion, ".
									"spae.paes_id as id_accion_especifica ".
								"FROM sai_proy_a_esp spae ".
								"WHERE ".
									"spae.proy_id = '".$idProyectoAccion."' AND ";
					if($centroGestor && $centroGestor!=""){
						$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
					}
					$query .=		"spae.pres_anno = ".$anno_pres." ";
				}else if($tipoImputacion=="0"){//accion centralizada
					$query .= 	"SELECT ".
									"sae.acce_id as id_proyecto_accion, ".
									"sae.aces_id as id_accion_especifica ".
								"FROM sai_acce_esp sae ".
								"WHERE ".
									"sae.acce_id = '".$idProyectoAccion."' AND ";
					if($centroGestor && $centroGestor!=""){
						$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
					}
					$query .=		"sae.pres_anno = ".$anno_pres." ";
				}			
			}else if($opcionConsolidar=="2"){
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.pres_anno = ".$anno_pres." ";
			}else if($opcionConsolidar=="3"){
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.pres_anno = ".$anno_pres." ".
							"UNION ".
							"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.pres_anno = ".$anno_pres." ";
			}
			$query.=	") as s ".
					"WHERE ";
			if($opcionConsolidar=="1" || $centroGestor!=""){
				$query.=	"sf1125.form_tipo = '".$tipoImputacion."' AND ".
							"sf1125.form_id_p_ac = '".$idProyectoAccion."' AND ";
			}
			$query.=	"sf1125.form_id_p_ac = s.id_proyecto_accion AND ".
						"sf1125.form_id_aesp = s.id_accion_especifica AND ".
						"sf1125.pres_anno = ".$anno_pres." AND ".
						"sf1125.form_id = sf1125d.form_id AND ".
						"sf1125.pres_anno = sf1125d.pres_anno AND ".
						"sf1125.esta_id <> 15 AND sf1125.esta_id <> 2 AND ".
						"sf1125d.fodt_mes BETWEEN 1 AND 12 AND ".
						"sf1125d.part_id LIKE '".$codigoPartida."%' AND ".
						"sf1125d.part_id NOT LIKE '4.11.0%' AND ".
						"sf1125d.part_id = sp.part_id AND ".
						"sf1125d.pres_anno = sp.pres_anno AND ".
						"sf1125.form_fecha < to_date('".$fechaFin."', 'DD/MM/YYYY')+1 ".
					"GROUP BY sf1125d.part_id, sp.part_nombre ".
					"ORDER BY sf1125d.part_id";
			$resultadoMontosProgramados=pg_query($query) or die("Error en los montos programados");
		}

		//MONTOS RECIBIDOS
		$query=	"SELECT ".
					"sf0305d.part_id, ".
					"COALESCE(SUM(sf0305d.f0dt_monto),0) as monto_recibido ".
				"FROM sai_forma_0305 sf0305, sai_fo0305_det sf0305d,sai_doc_genera dg, ".
					"(";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ";
			}			
		}else if($opcionConsolidar=="2"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ";
		}else if($opcionConsolidar=="3"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ".
						"UNION ".
						"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.pres_anno = ".$anno_pres." ";
		}
		$query.=	") as s ".
				"WHERE ";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			$query.=	"sf0305d.f0dt_proy_ac = '".$tipoImputacion."' AND ".
						"sf0305d.f0dt_id_p_ac = '".$idProyectoAccion."' AND ";
		}
		$query.=	"sf0305.pres_anno = ".$anno_pres." AND ".
		             					"
					 sf0305.f030_id = dg.docg_id
  						AND dg.esta_id = 13  AND 
					sf0305.esta_id <> 15 AND sf0305.esta_id <> 2 AND ".
					"sf0305.f030_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
					"sf0305.f030_id = sf0305d.f030_id AND ".
					"sf0305.pres_anno = sf0305d.pres_anno AND ".
					"sf0305d.f0dt_id_p_ac = s.id_proyecto_accion AND ".
					"sf0305d.f0dt_id_acesp = s.id_accion_especifica AND ".
					"sf0305d.f0dt_tipo='1' AND ".
					"sf0305d.part_id LIKE '".$codigoPartida."%' ".
				"GROUP BY sf0305d.part_id ".
				"ORDER BY sf0305d.part_id";
		$resultadoMontosRecibidos=pg_query($query) or die("Error en los montos recibidos");

		//MONTOS CEDIDOS
		$query=	"SELECT ".
					"sf0305d.part_id, ".
					"COALESCE(SUM(sf0305d.f0dt_monto),0) as monto_cedido ".
				"FROM sai_forma_0305 sf0305, sai_fo0305_det sf0305d,sai_doc_genera dg, ".
					"(";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ";
			}			
		}else if($opcionConsolidar=="2"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ";
		}else if($opcionConsolidar=="3"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ".
						"UNION ".
						"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.pres_anno = ".$anno_pres." ";
		}
		$query.=	") as s ".
				"WHERE ";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			$query.=	"sf0305d.f0dt_proy_ac = '".$tipoImputacion."' AND ".
						"sf0305d.f0dt_id_p_ac = '".$idProyectoAccion."' AND ";
		}
		$query.=	"sf0305.pres_anno = ".$anno_pres." AND ".
					"
					 sf0305.f030_id = dg.docg_id
  						AND dg.esta_id = 13  AND 
					sf0305.esta_id <> 15 AND sf0305.esta_id <> 2 AND ".
					"sf0305.f030_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
					"sf0305.f030_id = sf0305d.f030_id AND ".
					"sf0305.pres_anno = sf0305d.pres_anno AND ".
					"sf0305d.f0dt_id_p_ac = s.id_proyecto_accion AND ".
					"sf0305d.f0dt_id_acesp = s.id_accion_especifica AND ".
					"sf0305d.f0dt_tipo='0' AND ".
					"sf0305d.part_id LIKE '".$codigoPartida."%' ".
				"GROUP BY sf0305d.part_id ".
				"ORDER BY sf0305d.part_id";
		$resultadoMontosCedidos=pg_query($query) or die("Error en los montos cedidos");

		
		
		
		
		
		
		//MONTOS DIFERIDOS
		$query=	"SELECT ".
					"spit.pcta_sub_espe as part_id, ".
					"     CASE 
      WHEN COALESCE(SUM(spit.pcta_monto),0)  < 0.000001 AND COALESCE(SUM(spit.pcta_monto),0)  > -0.000001 THEN 0
      ELSE COALESCE(SUM(spit.pcta_monto),0) 
     END  as monto_diferido ".
				"FROM sai_pcuenta sp,  sai_pcta_imputa_traza spit, ".
					"(";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ";
			}			
		}else if($opcionConsolidar=="2"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ";
		}else if($opcionConsolidar=="3"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ".
						"UNION ".
						"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.pres_anno = ".$anno_pres." ";
		}
		$query.=	") AS s ".
				"WHERE ";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			$query.=	"spit.pcta_tipo_impu = '".$tipoImputacion."' AND ".
						"spit.pcta_acc_pp = '".$idProyectoAccion."' AND ";
		}
		$query.=	"spit.pres_anno = ".$anno_pres." AND ".
					//"sdg.docg_id = spit.pcta_id AND ".
					//"length(spt.pcta_id) > 4 AND ".
					//"spit.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
					"spit.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days' AND ".
					 " spit.pcta_id = sp.pcta_id AND ".
					/*"spt.esta_id <> 15 AND spt.esta_id <> 2 AND ".*/
					"sp.esta_id <> 2 AND ".
					"(sp.pcta_asunto <> '020' OR sp.esta_id <> 15) AND ".
		
					/*"spt.pcta_id NOT IN ".
						"(SELECT pcta_id ".
						"FROM sai_pcta_traza ".
						"WHERE ".
							//"(esta_id=15 OR esta_id=2) AND ".
							"(esta_id=2) AND ".
		
							"pcta_fecha2 < to_date('".$fechaFin."', 'DD/MM/YYYY')+1) AND ".
					"(".
						"(sdg.wfob_id_ini = 99) OR ".
						"(spit.depe_id = '350' AND sdg.perf_id_act IN ('".PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS."','".PERFIL_PRESIDENTE."')) OR ".
						"(spit.depe_id = '150' AND sdg.perf_id_act IN ('".PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS."','".PERFIL_DIRECTOR_EJECUTIVO."')) OR ".
						"(spit.depe_id <> '350' AND spit.depe_id <> '150' AND sdg.perf_id_act IN ('".PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS."','".PERFIL_DIRECTOR_EJECUTIVO."','".PERFIL_PRESIDENTE."')) ".
					") AND ".*/
					"spit.pcta_acc_pp = s.id_proyecto_accion AND ".
					"spit.pcta_acc_esp = s.id_accion_especifica AND ".
					"spit.pcta_sub_espe LIKE '".$codigoPartida."%' ".
				"GROUP BY spit.pcta_sub_espe ".
				"ORDER BY spit.pcta_sub_espe";
		
		//error_log(print_r($query,true));		
		$resultadoMontosDiferidos=pg_query($query) or die("Error en los montos diferidos");
		

		
			// montos en transito 
		
			   $query ="
			   
				 /* TODAS SEPARADAS */          
				 (SELECT 

				       b.pcta_sub_espe AS part_id,
				       COALESCE(SUM(b.pcta_monto),0) AS monto_diferido
				FROM sai_pcuenta AS a,
				     sai_pcta_imputa AS b,
				     sai_doc_genera d,";
			   
			   if($opcionConsolidar=="1" || $centroGestor!=""){
				     
			   if($tipoImputacion=="1"){
			 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
          spae.paes_id AS id_accion_especifica

   FROM sai_proy_a_esp spae
   WHERE spae.proy_id =  '".$idProyectoAccion."'
     AND spae.pres_anno = ".$anno_pres;
     if($centroGestor && $centroGestor!=""){
					$query .=	" AND spae.centro_gestor = '".$centroGestor."'";
				}
$query .=") AS se";
			 
			   }else if ($tipoImputacion=="0"){
			 $query .="(SELECT sae.acce_id AS id_proyecto_accion,
				          sae.aces_id AS id_accion_especifica
				         
				   FROM sai_acce_esp sae
				   WHERE sae.acce_id = '".$idProyectoAccion."'
				     AND sae.pres_anno = ".$anno_pres;
     if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
     					}
$query .=") AS se";
			  }   
			 
			   }else  if($opcionConsolidar=="2"){
			   	
						   	 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
			          spae.paes_id AS id_accion_especifica
			
			   FROM sai_proy_a_esp spae
			   WHERE  spae.pres_anno = ".$anno_pres;
			  
			$query .=") AS se";
			   	
			   }else  if($opcionConsolidar=="3"){
			   	
			   		   	 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
			          spae.paes_id AS id_accion_especifica
			
			   FROM sai_proy_a_esp spae
			   WHERE  spae.pres_anno = ".$anno_pres;
			   		   	 
			   		   	  $query .=" UNION SELECT sae.acce_id AS id_proyecto_accion,
				          sae.aces_id AS id_accion_especifica
				         
				   FROM sai_acce_esp sae
				   WHERE  sae.pres_anno = ".$anno_pres;
			  
			$query .="
			
			) AS se";
			   	
			   }
				
				 $query .="
				WHERE ";
				
				if($opcionConsolidar=="1" || $centroGestor!=""){
			$query.="  b.pcta_tipo_impu=".$tipoImputacion."::BIT
				  AND b.pcta_acc_pp=  '".$idProyectoAccion."' AND";
		}
				  $query .="   a.pcta_id=d.docg_id
				  AND a.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days'
				  AND se.id_proyecto_accion = b.pcta_acc_pp
				  AND se.id_accion_especifica = b.pcta_acc_esp
				  AND b.pres_anno=".$anno_pres."
				  AND a.esta_id <>15
				  AND d.esta_id <>15
				  AND d.esta_id <>13
				  AND d.perf_id_act not in ('".PERFIL_PRESIDENTE."','".PERFIL_DIRECTOR_EJECUTIVO."')
				  AND a.esta_id<>14
				  AND a.esta_id<>2
				  AND a.pcta_id=b.pcta_id";
				 
			
			$query .= "
				AND b.pcta_sub_espe LIKE '".$codigoPartida."%'
				
				GROUP BY b.pcta_sub_espe
				     
				ORDER BY 
				         b.pcta_sub_espe)
				         
				         UNION
				         
				     /* .00.00*/         (SELECT substring(b.pcta_sub_espe FROM 0 FOR 8)||'.00.00' AS part_id,     
       COALESCE(SUM(b.pcta_monto),0) AS monto_diferido
				FROM sai_pcuenta AS a,
				     sai_pcta_imputa AS b,
				     sai_doc_genera d,";
		
	 if($opcionConsolidar=="1" || $centroGestor!=""){
				     
			   if($tipoImputacion=="1"){
			 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
          spae.paes_id AS id_accion_especifica

   FROM sai_proy_a_esp spae
   WHERE spae.proy_id =  '".$idProyectoAccion."'
     AND spae.pres_anno = ".$anno_pres;
     if($centroGestor && $centroGestor!=""){
					$query .=	"AND spae.centro_gestor = '".$centroGestor."'";
				}
$query .=") AS se";
			 
			   }else if ($tipoImputacion=="0"){
			 $query .="(SELECT sae.acce_id AS id_proyecto_accion,
				          sae.aces_id AS id_accion_especifica
				         
				   FROM sai_acce_esp sae
				   WHERE sae.acce_id = '".$idProyectoAccion."'
				     AND sae.pres_anno = ".$anno_pres;
     if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
     					}
$query .=") AS se";
			  }   
			 
			   }else  if($opcionConsolidar=="2"){
			   	
						   	 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
			          spae.paes_id AS id_accion_especifica
			
			   FROM sai_proy_a_esp spae
			   WHERE  spae.pres_anno = ".$anno_pres;
			  
			$query .=") AS se";
			   	
			   }else  if($opcionConsolidar=="3"){
			   	
			   		   	 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
			          spae.paes_id AS id_accion_especifica
			
			   FROM sai_proy_a_esp spae
			   WHERE  spae.pres_anno = ".$anno_pres;
			   		   	 
			   		   	  $query .=" UNION SELECT sae.acce_id AS id_proyecto_accion,
				          sae.aces_id AS id_accion_especifica
				         
				   FROM sai_acce_esp sae
				   WHERE  sae.pres_anno = ".$anno_pres;
			  
			$query .="
			
			) AS se";
			   	
			   }
				
				 $query .="
				WHERE ";
				
				if($opcionConsolidar=="1" || $centroGestor!=""){
			$query.="  b.pcta_tipo_impu=".$tipoImputacion."::BIT
				  AND b.pcta_acc_pp=  '".$idProyectoAccion."' AND";
		}
				 $query .="
				  a.pcta_id=d.docg_id
				  AND a.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days'
				  AND se.id_proyecto_accion = b.pcta_acc_pp
				  AND se.id_accion_especifica = b.pcta_acc_esp
				  AND b.pres_anno=".$anno_pres."
				  AND a.esta_id <>15
				  AND d.esta_id <>15
				  AND d.esta_id <>13
				   AND d.perf_id_act not in ('".PERFIL_PRESIDENTE."','".PERFIL_DIRECTOR_EJECUTIVO."')		  
				  AND a.esta_id<>14
				  AND a.esta_id<>2
				  AND a.pcta_id=b.pcta_id";
			   
			
			
			$query .= "
				AND b.pcta_sub_espe LIKE '".$codigoPartida."%'
				
				GROUP BY substring(b.pcta_sub_espe FROM 0 FOR 8))
				         
				         
				         
				       UNION
				         
	 /* .00.00.00*/           (SELECT substring(b.pcta_sub_espe FROM 0 FOR 5)||'.00.00.00' AS part_id, 
				       COALESCE(SUM(b.pcta_monto),0) AS monto_diferido
				FROM sai_pcuenta AS a,
				     sai_pcta_imputa AS b,
				     sai_doc_genera d,";
				     
	 if($opcionConsolidar=="1" || $centroGestor!=""){
				     
			   if($tipoImputacion=="1"){
			 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
          spae.paes_id AS id_accion_especifica

   FROM sai_proy_a_esp spae
   WHERE spae.proy_id =  '".$idProyectoAccion."'
     AND spae.pres_anno = ".$anno_pres;
     if($centroGestor && $centroGestor!=""){
					$query .=	"AND spae.centro_gestor = '".$centroGestor."'";
				}
$query .=") AS se";
			 
			   }else if ($tipoImputacion=="0"){
			 $query .="(SELECT sae.acce_id AS id_proyecto_accion,
				          sae.aces_id AS id_accion_especifica
				         
				   FROM sai_acce_esp sae
				   WHERE sae.acce_id = '".$idProyectoAccion."'
				     AND sae.pres_anno = ".$anno_pres;
     if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
     					}
$query .=") AS se";
			  }   
			 
			   }else  if($opcionConsolidar=="2"){
			   	
						   	 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
			          spae.paes_id AS id_accion_especifica
			
			   FROM sai_proy_a_esp spae
			   WHERE  spae.pres_anno = ".$anno_pres;
			  
			$query .=") AS se";
			   	
			   }else  if($opcionConsolidar=="3"){
			   	
			   		   	 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
			          spae.paes_id AS id_accion_especifica
			
			   FROM sai_proy_a_esp spae
			   WHERE  spae.pres_anno = ".$anno_pres;
			   		   	 
			   		   	  $query .=" UNION SELECT sae.acce_id AS id_proyecto_accion,
				          sae.aces_id AS id_accion_especifica
				         
				   FROM sai_acce_esp sae
				   WHERE  sae.pres_anno = ".$anno_pres;
			  
			$query .="
			
			) AS se";
			   	
			   }
				
				 $query .="
				WHERE ";
				
				if($opcionConsolidar=="1" || $centroGestor!=""){
			$query.="  b.pcta_tipo_impu=".$tipoImputacion."::BIT
				  AND b.pcta_acc_pp=  '".$idProyectoAccion."' AND";
		}
				
				 $query .="
				 a.pcta_id=d.docg_id
				  AND a.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days'
				  AND se.id_proyecto_accion = b.pcta_acc_pp
				  AND se.id_accion_especifica = b.pcta_acc_esp
				  AND b.pres_anno=".$anno_pres."
				  AND a.esta_id <>15
				  AND d.esta_id <>15
				  AND d.esta_id <>13
				  AND d.perf_id_act not in ('".PERFIL_PRESIDENTE."','".PERFIL_DIRECTOR_EJECUTIVO."')
				  AND a.esta_id<>14
				  AND a.esta_id<>2
				  AND a.pcta_id=b.pcta_id
				 ";
			   
		
			$query .= "
				AND b.pcta_sub_espe LIKE '".$codigoPartida."%'
				
				GROUP BY substring(b.pcta_sub_espe FROM 0 FOR 5)) 

				         UNION
				         
				    /*MONTO TOTAL*/     (SELECT 'monto total' AS part_id,
				       COALESCE(SUM(b.pcta_monto),0) AS monto_diferido
				FROM sai_pcuenta AS a,
				     sai_pcta_imputa AS b,
				     sai_doc_genera d,";
				     
	 if($opcionConsolidar=="1" || $centroGestor!=""){
				     
			   if($tipoImputacion=="1"){
			 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
          spae.paes_id AS id_accion_especifica

   FROM sai_proy_a_esp spae
   WHERE spae.proy_id =  '".$idProyectoAccion."'
     AND spae.pres_anno = ".$anno_pres;
     if($centroGestor && $centroGestor!=""){
					$query .=	"AND spae.centro_gestor = '".$centroGestor."'";
				}
$query .=") AS se";
			 
			   }else if ($tipoImputacion=="0"){
			 $query .="(SELECT sae.acce_id AS id_proyecto_accion,
				          sae.aces_id AS id_accion_especifica
				         
				   FROM sai_acce_esp sae
				   WHERE sae.acce_id = '".$idProyectoAccion."'
				     AND sae.pres_anno = ".$anno_pres;
     if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
     					}
$query .=") AS se";
			  }   
			 
			   }else  if($opcionConsolidar=="2"){
			   	
						   	 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
			          spae.paes_id AS id_accion_especifica
			
			   FROM sai_proy_a_esp spae
			   WHERE  spae.pres_anno = ".$anno_pres;
			  
			$query .=") AS se";
			   	
			   }else  if($opcionConsolidar=="3"){
			   	
			   		   	 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
			          spae.paes_id AS id_accion_especifica
			
			   FROM sai_proy_a_esp spae
			   WHERE  spae.pres_anno = ".$anno_pres;
			   		   	 
			   		   	  $query .=" UNION SELECT sae.acce_id AS id_proyecto_accion,
				          sae.aces_id AS id_accion_especifica
				         
				   FROM sai_acce_esp sae
				   WHERE  sae.pres_anno = ".$anno_pres;
			  
			$query .="
			
			) AS se";
			   	
			   }
				
				 $query .="
				WHERE ";
				
				if($opcionConsolidar=="1" || $centroGestor!=""){
			$query.="  b.pcta_tipo_impu=".$tipoImputacion."::BIT
				  AND b.pcta_acc_pp=  '".$idProyectoAccion."' AND";
		}
				
				 $query .="
				a.pcta_id=d.docg_id
				  AND a.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days'
				  AND se.id_proyecto_accion = b.pcta_acc_pp
				  AND se.id_accion_especifica = b.pcta_acc_esp
				  AND b.pres_anno=".$anno_pres."
				  AND a.esta_id <>15
				  AND d.esta_id <>15
				  AND d.esta_id <>13
				  AND d.perf_id_act not in ('".PERFIL_PRESIDENTE."','".PERFIL_DIRECTOR_EJECUTIVO."')
				  AND a.esta_id<>14
				  AND a.esta_id<>2
				  AND a.pcta_id=b.pcta_id
				  GROUP BY part_id)";
			   

			$resultadoMontosEnTransito=pg_query($query) or die("Error en los montos Apartados en transito");

	             
	             while($filaProgramadostransito = pg_fetch_array($resultadoMontosEnTransito)) {
	             	
	             	  $params[$filaProgramadostransito['part_id']]=$filaProgramadostransito["monto_diferido"];
	             	
	             }
	   /*         
echo "<pre>";
echo print_r($params);
echo "</pre>";
*/
$filaProgramadostransito = $params;
				 
			//echo $query;

		//MONTOS COMPROMETIDOS
		$query=	"SELECT ".
					"scit.comp_sub_espe as part_id, ".
					"COALESCE(SUM(scit.comp_monto),0) as monto_comprometido ".
				"FROM sai_comp_imputa_traza scit, ".
					"(";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ";
			}			
		}else if($opcionConsolidar=="2"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ";
		}else if($opcionConsolidar=="3"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ".
						"UNION ".
						"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.pres_anno = ".$anno_pres." ";
		}
		$query.=	") as s ".
				", (".
					"SELECT scit.comp_id, MAX(scit.comp_fecha) as fecha ".
					"FROM sai_comp_traza sct, sai_comp_imputa_traza scit, ".
						"(";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ";
			}			
		}else if($opcionConsolidar=="2"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ";
		}else if($opcionConsolidar=="3"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ".
						"UNION ".
						"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.pres_anno = ".$anno_pres." ";
		}
		$query.=	") as s ".
					"WHERE ";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			$query.=	"scit.comp_tipo_impu = '".$tipoImputacion."' AND ".
						"scit.comp_acc_pp = '".$idProyectoAccion."' AND ";
		}
		$query.=		"scit.pres_anno = ".$anno_pres." AND ".
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
						"scit.comp_acc_pp = s.id_proyecto_accion AND ".
						"scit.comp_acc_esp = s.id_accion_especifica ".		
					"GROUP BY scit.comp_id ".
				") as ss ".
				"WHERE ";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			$query.=	"scit.comp_tipo_impu = '".$tipoImputacion."' AND ".
						"scit.comp_acc_pp = '".$idProyectoAccion."' AND ";
		}
		$query.=	"scit.comp_id = ss.comp_id AND ".
					"scit.comp_fecha = ss.fecha AND ".
					"scit.comp_acc_pp = s.id_proyecto_accion AND ".
					"scit.comp_acc_esp = s.id_accion_especifica AND ".
					"scit.comp_sub_espe LIKE '".$codigoPartida."%' ".
				"GROUP BY scit.comp_sub_espe ".
				"ORDER BY scit.comp_sub_espe";
		$resultadoMontosComprometidos=pg_query($query) or die("Error en los montos comprometidos");
		
		//MONTOS COMPROMETIDOS AISLADOS
		$query=	"SELECT ".
					"scit.comp_sub_espe as part_id, ".
					"COALESCE(SUM(scit.comp_monto),0) as monto_comprometido_aislado ".
				"FROM sai_comp_imputa_traza scit, ".
					"(";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ";
			}			
		}else if($opcionConsolidar=="2"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ";
		}else if($opcionConsolidar=="3"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ".
						"UNION ".
						"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.pres_anno = ".$anno_pres." ";
		}
		$query.=	") as s ".
				", (".
					"SELECT scit.comp_id, MAX(scit.comp_fecha) as fecha ".
					"FROM sai_comp_traza sct, sai_comp_imputa_traza scit,sai_comp sc, ".
						"(";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ";
			}			
		}else if($opcionConsolidar=="2"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ";
		}else if($opcionConsolidar=="3"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ".
						"UNION ".
						"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.pres_anno = ".$anno_pres." ";
		}
		$query.=	") as s ".
					"WHERE ";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			$query.=	"scit.comp_tipo_impu = '".$tipoImputacion."' AND ".
						"scit.comp_acc_pp = '".$idProyectoAccion."' AND ";
		}
		$query.=		"scit.pres_anno = ".$anno_pres." AND ".
						"length(sc.pcta_id) < 4 AND ".
				   "sc.comp_id = scit.comp_id AND ".
		
						"scit.comp_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
						"sct.esta_id <> 15 AND sct.esta_id <> 2 AND ".
						"sct.comp_id NOT IN ".
							"(SELECT comp_id ".
							"FROM sai_comp_traza ".
							"WHERE ".
								"(esta_id=15 OR esta_id=2) AND ".
								"comp_fecha2 < to_date('".$fechaFin."', 'DD/MM/YYYY')+1) AND ".
						"sct.comp_id = scit.comp_id AND ".
						"scit.comp_acc_pp = s.id_proyecto_accion AND ".
						"scit.comp_acc_esp = s.id_accion_especifica ".
					"GROUP BY scit.comp_id ".
				") as ss ".
				"WHERE ";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			$query.=	"scit.comp_tipo_impu = '".$tipoImputacion."' AND ".
						"scit.comp_acc_pp = '".$idProyectoAccion."' AND ";
		}
		$query.=	"scit.comp_id = ss.comp_id AND ".
					"scit.comp_fecha = ss.fecha AND ".
					"scit.comp_acc_pp = s.id_proyecto_accion AND ".
					"scit.comp_acc_esp = s.id_accion_especifica AND ".
					"scit.comp_sub_espe LIKE '".$codigoPartida."%' ".
				"GROUP BY scit.comp_sub_espe ".
				"ORDER BY scit.comp_sub_espe";
		
		//echo $query;
		$resultadoMontosComprometidosAislados=pg_query($query) or die("Error en los montos comprometidos aislados");

		//MONTOS CAUSADOS
		$query= "SELECT ".
					"part_id, ".
					"SUM(monto_causado) AS monto_causado ".
				"FROM ".
				"(".
				"SELECT ".
					"scd.part_id, ".
					"COALESCE(SUM(scd.cadt_monto),0) AS monto_causado ".
				"FROM sai_causado sc, sai_causad_det scd, ".
					"(";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ";
			}			
		}else if($opcionConsolidar=="2"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ";
		}else if($opcionConsolidar=="3"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ".
						"UNION ".
						"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.pres_anno = ".$anno_pres." ";
		}
		$query.=	") as s ".
				"WHERE ";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			$query.=	"scd.cadt_tipo = '".$tipoImputacion."'::BIT AND ".
						"scd.cadt_id_p_ac = '".$idProyectoAccion."' AND ";
		}
		$query.=	"sc.pres_anno = ".$anno_pres." AND ".
					"sc.esta_id <> 2 AND ".
					/*"sc.esta_id <> 15 AND ".*/
				//	"CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ".
				//	"(sc.fecha_anulacion IS NULL OR (CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) AND ".
		
					" (( ".
					"sc.fecha_anulacion IS NULL AND ".
					"CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".
					") ". 

					"OR ".
					"(sc.fecha_anulacion IS NOT NULL ".
					"AND CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".
					"AND CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".
					")) ".
							
							
					"AND ".
		
		
					"sc.caus_id = scd.caus_id AND ".
					"sc.pres_anno = scd.pres_anno AND ".
					"scd.cadt_id_p_ac = s.id_proyecto_accion AND ".
					"scd.cadt_cod_aesp = s.id_accion_especifica AND ".
					"scd.cadt_abono='1' AND ".
					"scd.part_id LIKE '".$codigoPartida."%' AND ".
					"scd.part_id NOT LIKE '4.11.0%' ".
				"GROUP BY scd.part_id ".

// Segunda parte
		"UNION ".
		"SELECT ".
		"scd.part_id, ".
		"COALESCE(SUM(scd.cadt_monto*-1),0) AS monto_causado ".
		"FROM sai_causado sc, sai_causad_det scd, ".
		"(";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
						"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
						"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ";
			}
		}else if($opcionConsolidar=="2"){
			$query .= 	"SELECT ".
					"spae.proy_id as id_proyecto_accion, ".
					"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
					"spae.pres_anno = ".$anno_pres." ";
		}else if($opcionConsolidar=="3"){
			$query .= 	"SELECT ".
					"spae.proy_id as id_proyecto_accion, ".
					"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
					"spae.pres_anno = ".$anno_pres." ".
					"UNION ".
					"SELECT ".
					"sae.acce_id as id_proyecto_accion, ".
					"sae.aces_id as id_accion_especifica ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
					"sae.pres_anno = ".$anno_pres." ";
		}
		$query.=	") as s ".
				"WHERE ";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			$query.=	"scd.cadt_tipo = '".$tipoImputacion."'::BIT AND ".
					"scd.cadt_id_p_ac = '".$idProyectoAccion."' AND ";
		}
		$query.=	"sc.pres_anno = ".$anno_pres." AND ".
				"sc.esta_id <> 2 AND ".
				/*"sc.esta_id <> 15 AND ".*/
		//	"CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ".
		//	"(sc.fecha_anulacion IS NULL OR (CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) AND ".
		

		"(sc.fecha_anulacion IS NOT NULL ".
		"AND CAST(sc.caus_fecha AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".		
		"AND CAST(sc.fecha_anulacion AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ".
		") AND ".
		
		
		"sc.caus_id = scd.caus_id AND ".
		"sc.pres_anno = scd.pres_anno AND ".
		"scd.cadt_id_p_ac = s.id_proyecto_accion AND ".
		"scd.cadt_cod_aesp = s.id_accion_especifica AND ".
		"scd.cadt_abono='1' AND ".
		"scd.part_id LIKE '".$codigoPartida."%' AND ".
		"scd.part_id NOT LIKE '4.11.0%' ".
		"GROUP BY scd.part_id ".
		
				
				
				

				
				") AS s ".
				"WHERE s.monto_causado <> 0 ".
				"GROUP BY part_id ".
				"ORDER BY part_id";
		//echo $query;
		$resultadoMontosCausados=pg_query($query) or die("Error en los montos causados");
		
		//MONTOS PAGADOS
		$query= "SELECT ".
					"part_id, ".
					"monto_pagado ".
				"FROM ".
				"(".
				"SELECT ".
					"spd.part_id, ".
					"COALESCE(SUM(spd.padt_monto),0) AS monto_pagado ".
				"FROM sai_pagado sp, sai_pagado_dt spd, ".
					"(";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ";
			}			
		}else if($opcionConsolidar=="2"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ";
		}else if($opcionConsolidar=="3"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ".
						"UNION ".
						"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.pres_anno = ".$anno_pres." ";
		}
		$query.=	") as s ".
				"WHERE ";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			$query.=	"spd.padt_tipo = '".$tipoImputacion."'::BIT AND ".
						"spd.padt_id_p_ac = '".$idProyectoAccion."' AND ";
		}
		$query.=	"sp.pres_anno = ".$anno_pres." AND ".
					"sp.esta_id <> 2 AND ".
					/*"sp.esta_id <> 15 AND ".*/
					"CAST(sp.paga_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ".
					"(sp.fecha_anulacion IS NULL OR (CAST(sp.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) AND ".
					"sp.paga_id = spd.paga_id AND ".
					"sp.pres_anno = spd.pres_anno AND ".
					"spd.padt_id_p_ac = s.id_proyecto_accion AND ".
					"spd.padt_cod_aesp = s.id_accion_especifica AND ".
					//"spd.padt_abono='1' AND ".
					"spd.part_id LIKE '".$codigoPartida."%' AND ".
					"spd.part_id NOT LIKE '4.11.0%' ".
				"GROUP BY spd.part_id ".
				") AS s ".
				"WHERE s.monto_pagado <> 0 ".
				"ORDER BY part_id";
		$resultadoMontosPagados=pg_query($query) or die("Error en los montos pagados");
		
		$totalProgramados = 0;
		$totalRecibidos = 0;
		$totalCedidos = 0;
		$totalDiferidos = 0;
		$totalComprometidos = 0;
		$totalComprometidosAislados = 0;
		$totalCausados = 0;
		$totalPagados = 0;
		$totalDisponible = 0;
	
		$totalPrimerOrdenProgramados = 0;
		$totalPrimerOrdenRecibidos = 0;
		$totalPrimerOrdenCedidos = 0;
		$totalPrimerOrdenDiferidos = 0;
		$totalPrimerOrdenComprometidos = 0;
		$totalPrimerOrdenComprometidosAislados = 0;
		$totalPrimerOrdenCausados = 0;
		$totalPrimerOrdenPagados = 0;
		$totalPrimerOrdenDisponible = 0;
	
		$totalSegundoOrdenProgramados = 0;
		$totalSegundoOrdenRecibidos = 0;
		$totalSegundoOrdenCedidos = 0;
		$totalSegundoOrdenDiferidos = 0;
		$totalSegundoOrdenComprometidos = 0;
		$totalSegundoOrdenComprometidosAislados = 0;
		$totalSegundoOrdenCausados = 0;
		$totalSegundoOrdenPagados = 0;
		$totalSegundoOrdenDisponible = 0;
	
		$programado = 0;
		$recibido = 0;
		$cedido = 0;
		$diferido = 0;
		$comprometido = 0;
		$comprometidoAislado = 0;
		$causado = 0;
		$pagado = 0;
		$montoAjustado = 0;
		$montoDisponible = 0;
	
		$partidaAnteriorPrimerOrden = "";
		$partidaAnteriorSegundoOrden = "";
	
		$tamanoResultado = pg_num_rows($resultadoMontosProgramados);

		if($tamanoResultado>0){
		?>
					<tr class="td_gray">
						<td class="normalNegroNegrita" width="200px">Denominaci&oacute;n</td>
						<td class="normalNegroNegrita" width="70px">Partida</td>
						<td class="normalNegroNegrita" width="110px">Presupuesto Ley</td>
						<td class="normalNegroNegrita" width="60px">Recibido</td>
						<td class="normalNegroNegrita" width="60px">Cedido</td>
						<td class="normalNegroNegrita" width="110px">Presupuesto modif.</td>
						<td class="normalNegroNegrita" width="120px">Apartado</td>
						<td class="normalNegroNegrita" width="60px">Apartado en tr&aacute;nsito (actual)</td>
						<td class="normalNegroNegrita" width="90px">Comprometido</td>
						<td class="normalNegroNegrita" width="90px">Compr. aislado</td>
						<td class="normalNegroNegrita" width="60px">Causado</td>
						<td class="normalNegroNegrita" width="60px">Pagado</td>
						<td class="normalNegroNegrita" width="60px">Disponible</td>
						<td class="normalNegroNegrita" width="70px">% Ejecuci&oacute;n</td>
					</tr>
		<?php
			while($filaProgramados=pg_fetch_array($resultadoMontosProgramados)){
				if($partidaAnteriorPrimerOrden==""){
					$partidaAnteriorPrimerOrden=substr($filaProgramados["part_id"], 0, 4).".00.00.00";
		?>
					<tr class="normalNegroNegrita" style="color: #35519B;" id="tr-<?= $partidaAnteriorPrimerOrden?>">
						<td id="nombre-partida-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td id="partida-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="programado-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="recibido-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="cedido-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="presupuesto-modificado-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="apartado-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right"><?=  number_format($filaProgramadostransito[$partidaAnteriorPrimerOrden],2,',','.')?></td>
						<td align="right" id="comprometido-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="comprometido-aislado-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="causado-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="pagado-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="disponible-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="ejecucion-<?= $partidaAnteriorPrimerOrden?>"></td>
					</tr>					
				<?php 
				}else if($partidaAnteriorPrimerOrden!=(substr($filaProgramados["part_id"], 0, 4).".00.00.00")){
					$nombrePartida = "";
					$iPartidasPrimariasYSecundarias = 0;						
					while($iPartidasPrimariasYSecundarias<$tamanoPartidasPrimariasYSecundarias) {
						if($partidaAnteriorPrimerOrden==$arregloPartidas[$iPartidasPrimariasYSecundarias][0]){
							$nombrePartida = $arregloPartidas[$iPartidasPrimariasYSecundarias][1];
							break;
						}
						$iPartidasPrimariasYSecundarias++;
					}
					if(round($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos)!=0){
						$ejecucion = number_format(($totalPrimerOrdenDiferidos+$totalPrimerOrdenComprometidosAislados+ $filaProgramadostransito[$partidaAnteriorPrimerOrden])*100/($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos),2,',','.')."%"; 
					}else{
						$ejecucion = "0,00%";	
					}

					if ( 	$totalPrimerOrdenProgramados!=0 ||
							$totalPrimerOrdenRecibidos!=0 ||
							$totalPrimerOrdenCedidos!=0 ||
							$totalPrimerOrdenDiferidos!=0 ||
							$totalPrimerOrdenComprometidos!=0 ||
							$totalPrimerOrdenComprometidosAislados!=0 ||
							$totalPrimerOrdenCausados!=0 ||
							$totalPrimerOrdenPagados!=0 ||
							$totalPrimerOrdenDisponible!=0 ||
							$mostrarTodasLasPartidas=="true" ) {
				?>
					<script>
						document.getElementById('nombre-partida-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= $nombrePartida?>';
						document.getElementById('partida-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= $partidaAnteriorPrimerOrden?>';
						document.getElementById('programado-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenProgramados,2,',','.')?>';
						document.getElementById('recibido-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenRecibidos,2,',','.')?>';
						document.getElementById('cedido-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenCedidos,2,',','.')?>';
						document.getElementById('presupuesto-modificado-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos,2,',','.')?>';
						document.getElementById('apartado-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenDiferidos,2,',','.')?>';
						document.getElementById('comprometido-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenComprometidos,2,',','.')?>';
						document.getElementById('comprometido-aislado-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenComprometidosAislados,2,',','.')?>';
						document.getElementById('causado-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenCausados,2,',','.')?>';
						document.getElementById('pagado-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenPagados,2,',','.')?>';
						document.getElementById('disponible-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenDisponible - $filaProgramadostransito[$partidaAnteriorPrimerOrden],2,',','.')?>';
						document.getElementById('ejecucion-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= $ejecucion?>';
					</script>
				<?
					} else {
				?>
					<script>
						var element = document.getElementById('tr-<?= $partidaAnteriorPrimerOrden?>');
						if ( element!=null ){
							element.parentNode.removeChild(element);
						}
					</script>
				<?php	
					}
					$partidaAnteriorPrimerOrden=substr($filaProgramados["part_id"], 0, 4).".00.00.00";
				?>
					<tr class="normalNegroNegrita" style="color: #35519B;" id="tr-<?= $partidaAnteriorPrimerOrden?>">
						<td id="nombre-partida-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td id="partida-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="programado-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="recibido-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="cedido-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="presupuesto-modificado-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="apartado-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right"><?=  number_format($filaProgramadostransito[$partidaAnteriorPrimerOrden],2,',','.')?></td>
						<td align="right" id="comprometido-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="comprometido-aislado-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="causado-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="pagado-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="disponible-<?= $partidaAnteriorPrimerOrden?>"></td>
						<td align="right" id="ejecucion-<?= $partidaAnteriorPrimerOrden?>"></td>
					</tr>
				<?php
					$totalPrimerOrdenProgramados = 0;
					$totalPrimerOrdenRecibidos = 0;
					$totalPrimerOrdenCedidos = 0;
					$totalPrimerOrdenDiferidos = 0;
					$totalPrimerOrdenComprometidos = 0;
					$totalPrimerOrdenComprometidosAislados = 0;
					$totalPrimerOrdenCausados = 0;
					$totalPrimerOrdenPagados = 0;
					$totalPrimerOrdenDisponible = 0;
				}
				
				if($partidaAnteriorSegundoOrden==""){
					$partidaAnteriorSegundoOrden=substr($filaProgramados["part_id"], 0, 7).".00.00";
					?>
					<tr class="normalNegroNegrita" style="color: #000000;" id="tr-<?= $partidaAnteriorSegundoOrden?>">
						<td id="nombre-partida-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="center" id="partida-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="programado-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="recibido-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="cedido-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="presupuesto-modificado-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="apartado-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right"><?=  number_format($filaProgramadostransito[$partidaAnteriorSegundoOrden],2,',','.')?></td>
						<td align="right" id="comprometido-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="comprometido-aislado-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="causado-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="pagado-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="disponible-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="ejecucion-<?= $partidaAnteriorSegundoOrden?>"></td>
					</tr>					
				<?php
				}else if($partidaAnteriorSegundoOrden!=(substr($filaProgramados["part_id"], 0, 7).".00.00")){
					$nombrePartida = "";
					$iPartidasPrimariasYSecundarias = 0;						
					while($iPartidasPrimariasYSecundarias<$tamanoPartidasPrimariasYSecundarias) {
						if($partidaAnteriorSegundoOrden==$arregloPartidas[$iPartidasPrimariasYSecundarias][0]){
							$nombrePartida = $arregloPartidas[$iPartidasPrimariasYSecundarias][1];
							break;
						}
						$iPartidasPrimariasYSecundarias++;
					}
					if(round($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos)!=0){
						$ejecucion = number_format(($totalSegundoOrdenDiferidos+$totalSegundoOrdenComprometidosAislados + $filaProgramadostransito[$partidaAnteriorSegundoOrden])*100/($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos),2,',','.')."%"; 
					}else{
						$ejecucion = "0,00%";	
					}

					if ( 	$totalSegundoOrdenProgramados!=0 ||
							$totalSegundoOrdenRecibidos!=0 ||
							$totalSegundoOrdenCedidos!=0 ||
							$totalSegundoOrdenDiferidos!=0 ||
							$totalSegundoOrdenComprometidos!=0 ||
							$totalSegundoOrdenComprometidosAislados!=0 ||
							$totalSegundoOrdenCausados!=0 ||
							$totalSegundoOrdenPagados!=0 ||
							$totalSegundoOrdenDisponible!=0 ||
							$mostrarTodasLasPartidas=="true" ) {
				?>
					<script>
						document.getElementById('nombre-partida-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= $nombrePartida?>';
						document.getElementById('partida-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= $partidaAnteriorSegundoOrden?>';
						document.getElementById('programado-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenProgramados,2,',','.')?>';
						document.getElementById('recibido-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenRecibidos,2,',','.')?>';
						document.getElementById('cedido-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenCedidos,2,',','.')?>';
						document.getElementById('presupuesto-modificado-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos,2,',','.')?>';
						document.getElementById('apartado-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenDiferidos,2,',','.')?>';
						document.getElementById('comprometido-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenComprometidos,2,',','.')?>';
						document.getElementById('comprometido-aislado-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenComprometidosAislados,2,',','.')?>';
						document.getElementById('causado-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenCausados,2,',','.')?>';
						document.getElementById('pagado-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenPagados,2,',','.')?>';
						document.getElementById('disponible-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenDisponible - $filaProgramadostransito[$partidaAnteriorSegundoOrden],2,',','.')?>';
						document.getElementById('ejecucion-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= $ejecucion?>';
					</script>
				<?
					} else {
				?>
					<script>
						var element = document.getElementById('tr-<?= $partidaAnteriorSegundoOrden?>');
						if ( element!=null ){
							element.parentNode.removeChild(element);
						}
					</script>
				<?php	
					}
					$partidaAnteriorSegundoOrden=substr($filaProgramados["part_id"], 0, 7).".00.00";
				?>
					<tr class="normalNegroNegrita" style="color: #000000;" id="tr-<?= $partidaAnteriorSegundoOrden?>">
						<td id="nombre-partida-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="center" id="partida-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="programado-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="recibido-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="cedido-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="presupuesto-modificado-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="apartado-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right"><?=  number_format($filaProgramadostransito[$partidaAnteriorSegundoOrden],2,',','.')?></td>

						<td align="right" id="comprometido-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="comprometido-aislado-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="causado-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="pagado-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="disponible-<?= $partidaAnteriorSegundoOrden?>"></td>
						<td align="right" id="ejecucion-<?= $partidaAnteriorSegundoOrden?>"></td>
					</tr>
				<?php
					$totalSegundoOrdenProgramados = 0;
					$totalSegundoOrdenRecibidos = 0;
					$totalSegundoOrdenCedidos = 0;
					$totalSegundoOrdenDiferidos = 0;
					$totalSegundoOrdenComprometidos = 0;
					$totalSegundoOrdenComprometidosAislados = 0;
					$totalSegundoOrdenCausados = 0;
					$totalSegundoOrdenPagados = 0;
					$totalSegundoOrdenDisponible = 0;
				}
			
				$programado = $filaProgramados['monto_programado'];
				$recibido = 0;
				$cedido = 0;
				$diferido = 0;
				$comprometido = 0;
				$comprometidoAislado = 0;
				$causado = 0;
				$pagado = 0;
			
				if($filaRecibidos==null){
					$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
				}
				if($filaProgramados["part_id"]==$filaRecibidos["part_id"]){
					$recibido = $filaRecibidos["monto_recibido"];
					$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
				}
					
				if($filaCedidos==null){
					$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
				}
				if($filaProgramados["part_id"]==$filaCedidos["part_id"]){
					$cedido = $filaCedidos["monto_cedido"];
					$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
				}
					
				$montoAjustado=($programado+$recibido)-$cedido;
					
				if($filaDiferidos==null){
					$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
				}
				if($filaProgramados["part_id"]==$filaDiferidos["part_id"]){
					$diferido = $filaDiferidos["monto_diferido"];
					$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
				}
					
				if($filaComprometidos==null){
					$filaComprometidos=pg_fetch_array($resultadoMontosComprometidos);
				}
				if($filaProgramados["part_id"]==$filaComprometidos["part_id"]){
					$comprometido = $filaComprometidos["monto_comprometido"];
					$filaComprometidos=pg_fetch_array($resultadoMontosComprometidos);
				}
		
				if($filaComprometidosAislados==null){
					$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
				}
				if($filaProgramados["part_id"]==$filaComprometidosAislados["part_id"]){
					$comprometidoAislado = $filaComprometidosAislados["monto_comprometido_aislado"];
					$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
				}
		
				if($filaCausados==null){
					$filaCausados=pg_fetch_array($resultadoMontosCausados);
				}
				if($filaProgramados["part_id"]==$filaCausados["part_id"]){
					$causado = $filaCausados["monto_causado"];
					$filaCausados=pg_fetch_array($resultadoMontosCausados);
				}
					
				if($filaPagados==null){
					$filaPagados=pg_fetch_array($resultadoMontosPagados);
				}
				if($filaProgramados["part_id"]==$filaPagados["part_id"]){
					$pagado = $filaPagados["monto_pagado"];
					$filaPagados=pg_fetch_array($resultadoMontosPagados);
				}
					
				if($diferido>0){
					$montoDisponible=($montoAjustado)-($diferido)-$comprometidoAislado;
				}else if($comprometidoAislado>0){
					$montoDisponible=($montoAjustado)-($comprometidoAislado);
				}else{
					$montoDisponible=($montoAjustado);
				}
				
				if ( 	$programado!=0 ||
						$recibido!=0 ||
						$cedido!=0 ||
						$diferido!=0 ||
						$comprometido!=0 ||
						$comprometidoAislado!=0 ||
						$causado!=0 ||
						$pagado!=0 ||
						$montoDisponible!=0 ||
						$mostrarTodasLasPartidas=="true" ) {

					$totalProgramados += $programado;
					$totalRecibidos +=  $recibido;
					$totalCedidos += $cedido;
					$totalDiferidos += $diferido;
					$totalComprometidos += $comprometido;
					$totalComprometidosAislados += $comprometidoAislado;
					$totalCausados += $causado;
					$totalPagados += $pagado;
					$totalDisponible += $montoDisponible;
						
					$totalPrimerOrdenProgramados += $programado;
					$totalPrimerOrdenRecibidos +=  $recibido;
					$totalPrimerOrdenCedidos += $cedido;
					$totalPrimerOrdenDiferidos += $diferido;
					$totalPrimerOrdenComprometidos += $comprometido;
					$totalPrimerOrdenComprometidosAislados += $comprometidoAislado;
					$totalPrimerOrdenCausados += $causado;
					$totalPrimerOrdenPagados += $pagado;
					$totalPrimerOrdenDisponible += $montoDisponible;
						
					$totalSegundoOrdenProgramados += $programado;
					$totalSegundoOrdenRecibidos +=  $recibido;
					$totalSegundoOrdenCedidos += $cedido;
					$totalSegundoOrdenDiferidos += $diferido;
					$totalSegundoOrdenComprometidos += $comprometido;
					$totalSegundoOrdenComprometidosAislados += $comprometidoAislado;
					$totalSegundoOrdenCausados += $causado;
					$totalSegundoOrdenPagados += $pagado;
					$totalSegundoOrdenDisponible += $montoDisponible;
			?>
			<tr class="normal">
				<td><?=trim($filaProgramados['partida']);?></td>
				<td align="right"><?=trim($filaProgramados['part_id']);?></td>
				<td align="right"><?= number_format($programado,2,',','.')?></td>
				
				<?php 
				/*<td align="right"><?= number_format($recibido,2,',','.')?></td>
				<td align="right"><?= number_format($cedido,2,',','.')?></td>*/
				?>
				<td align="right"><a target="_blank"
					href="detalleRecibido.php?partida=<?=$filaProgramados['part_id']?>&proy=<?=$idProyectoAccion?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFin?>&tipo=<?=$tipoImputacion?>&consolidado=<?= $opcionConsolidar?>&monto=<?=$recibido?>&aesp=&centroGestor=<?=$centroGestor?>"><?= number_format($recibido,2,',','.')?></a>
				</td>
				<td align="right"><a target="_blank"
					href="detalleCedido.php?partida=<?=$filaProgramados['part_id']?>&proy=<?=$idProyectoAccion?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFin?>&tipo=<?=$tipoImputacion?>&consolidado=<?= $opcionConsolidar?>&monto=<?=$cedido?>&aesp=&centroGestor=<?=$centroGestor?>"><?= number_format($cedido,2,',','.')?></a>
				</td>
				
				<td align="right"><?= number_format($programado+$recibido-$cedido,2,',','.')?></td>
				<td align="right"><a target="_blank"
					href="detalleCompromisoApartado.php?partida=<?=$filaProgramados['part_id']?>&proy=<?=$idProyectoAccion?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFin?>&tipo=<?=$tipoImputacion?>&consolidado=<?= $opcionConsolidar?>&monto=<?=$diferido?>&aesp=&centroGestor=<?=$centroGestor?>"><?= number_format($diferido,2,',','.')?></a>
				</td>
				
					
				<td align="right">
				
				<a target="_blank"
					href="detalleApartadoTransitoPcta.php?partida=<?=$filaProgramados['part_id']?>&proy=<?=$idProyectoAccion?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFin?>&tipo=<?=$tipoImputacion?>&consolidado=<?= $opcionConsolidar?>">
			
				<?php echo number_format($filaProgramadostransito[$filaProgramados['part_id']],2,',','.');?>
				</a>
				</td>
				
				
				
				
				
				<td align="right"><a target="_blank"
					href="detalleCompromiso.php?partida=<?=$filaProgramados['part_id']?>&proy=<?=$idProyectoAccion?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFin?>&tipo=<?=$tipoImputacion?>&consolidado=<?= $opcionConsolidar?>&monto=<?=$comprometido?>&aesp=&centroGestor=<?=$centroGestor?>"><?= number_format($comprometido,2,',','.')?></a>
				</td>
				<td align="right"><a target="_blank"
					href="detalleCompromisoAislado.php?partida=<?=$filaProgramados['part_id']?>&proy=<?=$idProyectoAccion?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFin?>&tipo=<?=$tipoImputacion?>&consolidado=<?= $opcionConsolidar?>&monto=<?=$comprometidoAislado?>&aesp=&centroGestor=<?=$centroGestor?>"><?= number_format($comprometidoAislado,2,',','.')?></a>
				</td>
				<td align="right"><a target="_blank"
					href="detalleCausado.php?partida=<?=$filaProgramados['part_id']?>&proy=<?=$idProyectoAccion?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFin?>&tipo=<?=$tipoImputacion?>&consolidado=<?= $opcionConsolidar?>&monto=<?=$causado?>&aesp=&centroGestor=<?=$centroGestor?>"><?= number_format($causado,2,',','.')?></a>
				</td>
				<td align="right"><a target="_blank"
					href="detallePagado.php?partida=<?=$filaProgramados['part_id']?>&proy=<?=$idProyectoAccion?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFin?>&tipo=<?=$tipoImputacion?>&consolidado=<?= $opcionConsolidar?>&monto=<?=$pagado?>&aesp=&centroGestor=<?=$centroGestor?>"><?= number_format($pagado,2,',','.')?></a>
				</td>				
				<td align="right"><?= number_format($montoDisponible-$filaProgramadostransito[$filaProgramados['part_id']],2,',','.')?></td>
				<td align="right">
				<?php
					if(round($programado+$recibido-$cedido)!=0){
						echo number_format(($diferido+$comprometidoAislado + $filaProgramadostransito[$filaProgramados['part_id']])*100/($programado+$recibido-$cedido),2,',','.')."%"; 
					}else{
						echo "0,00%";	
					}					
				?>
				</td>
			</tr>
		<?
				}
			}

			if($partidaAnteriorPrimerOrden!=""){
				$nombrePartida = "";
				$iPartidasPrimariasYSecundarias = 0;						
				while($iPartidasPrimariasYSecundarias<$tamanoPartidasPrimariasYSecundarias) {
					if($partidaAnteriorPrimerOrden==$arregloPartidas[$iPartidasPrimariasYSecundarias][0]){
						$nombrePartida = $arregloPartidas[$iPartidasPrimariasYSecundarias][1];
						break;
					}
					$iPartidasPrimariasYSecundarias++;
				}
				if(round($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos)!=0){
					$ejecucion = number_format(($totalPrimerOrdenDiferidos+$totalPrimerOrdenComprometidosAislados)*100/($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos),2,',','.')."%"; 
				}else{
					$ejecucion = "0,00%";	
				}
				if ( 	$totalPrimerOrdenProgramados!=0 ||
						$totalPrimerOrdenRecibidos!=0 ||
						$totalPrimerOrdenCedidos!=0 ||
						$totalPrimerOrdenDiferidos!=0 ||
						$totalPrimerOrdenComprometidos!=0 ||
						$totalPrimerOrdenComprometidosAislados!=0 ||
						$totalPrimerOrdenCausados!=0 ||
						$totalPrimerOrdenPagados!=0 ||
						$totalPrimerOrdenDisponible!=0 ||
						$mostrarTodasLasPartidas=="true" ) {
							/*
								$params3[$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"] ]	= $filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"];
						
							 foreach ($params3 as $key => $valor){
								 $dato = $valor;

                     break;
						  }
							<td align="right"><?= number_format($totalDisponible - $filaProgramadostransito[$dato."/"."monto total"],2,',','.')?></td>
						*/  
				?>
				<script>
					document.getElementById('nombre-partida-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= $nombrePartida?>';
					document.getElementById('partida-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= $partidaAnteriorPrimerOrden?>';
					document.getElementById('programado-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenProgramados,2,',','.')?>';
					document.getElementById('recibido-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenRecibidos,2,',','.')?>';
					document.getElementById('cedido-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenCedidos,2,',','.')?>';
					document.getElementById('presupuesto-modificado-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos,2,',','.')?>';
					document.getElementById('apartado-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenDiferidos,2,',','.')?>';
					document.getElementById('comprometido-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenComprometidos,2,',','.')?>';
					document.getElementById('comprometido-aislado-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenComprometidosAislados,2,',','.')?>';
					document.getElementById('causado-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenCausados,2,',','.')?>';
					document.getElementById('pagado-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenPagados,2,',','.')?>';
					document.getElementById('disponible-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= number_format($totalPrimerOrdenDisponible,2,',','.')?>';
					document.getElementById('ejecucion-<?= $partidaAnteriorPrimerOrden?>').innerHTML = '<?= $ejecucion?>';
				</script>
				<?
				} else {
				?>
					<script>
						var element = document.getElementById('tr-<?= $partidaAnteriorPrimerOrden?>');
						if ( element!=null ){
							element.parentNode.removeChild(element);
						}
					</script>
				<?php	
				}
			}
				
			if($partidaAnteriorSegundoOrden!=""){
				$nombrePartida = "";
				$iPartidasPrimariasYSecundarias = 0;						
				while($iPartidasPrimariasYSecundarias<$tamanoPartidasPrimariasYSecundarias) {
					if($partidaAnteriorSegundoOrden==$arregloPartidas[$iPartidasPrimariasYSecundarias][0]){
						$nombrePartida = $arregloPartidas[$iPartidasPrimariasYSecundarias][1];
						break;
					}
					$iPartidasPrimariasYSecundarias++;
				}
				if(round($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos)!=0){
					$ejecucion = number_format(($totalSegundoOrdenDiferidos+$totalSegundoOrdenComprometidosAislados)*100/($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos),2,',','.')."%"; 
				}else{
					$ejecucion = "0,00%";	
				}
				if ( 	$totalSegundoOrdenProgramados!=0 ||
						$totalSegundoOrdenRecibidos!=0 ||
						$totalSegundoOrdenCedidos!=0 ||
						$totalSegundoOrdenDiferidos!=0 ||
						$totalSegundoOrdenComprometidos!=0 ||
						$totalSegundoOrdenComprometidosAislados!=0 ||
						$totalSegundoOrdenCausados!=0 ||
						$totalSegundoOrdenPagados!=0 ||
						$totalSegundoOrdenDisponible!=0 ||
						$mostrarTodasLasPartidas=="true" ) {				
			?>
				<script>
					document.getElementById('nombre-partida-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= $nombrePartida?>';
					document.getElementById('partida-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= $partidaAnteriorSegundoOrden?>';
					document.getElementById('programado-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenProgramados,2,',','.')?>';
					document.getElementById('recibido-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenRecibidos,2,',','.')?>';
					document.getElementById('cedido-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenCedidos,2,',','.')?>';
					document.getElementById('presupuesto-modificado-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos,2,',','.')?>';
					document.getElementById('apartado-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenDiferidos,2,',','.')?>';
					document.getElementById('comprometido-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenComprometidos,2,',','.')?>';
					document.getElementById('comprometido-aislado-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenComprometidosAislados,2,',','.')?>';
					document.getElementById('causado-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenCausados,2,',','.')?>';
					document.getElementById('pagado-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenPagados,2,',','.')?>';
					document.getElementById('disponible-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= number_format($totalSegundoOrdenDisponible,2,',','.')?>';
					document.getElementById('ejecucion-<?= $partidaAnteriorSegundoOrden?>').innerHTML = '<?= $ejecucion?>';
				</script>
			<?
				} else {
				?>
					<script>
						var element = document.getElementById('tr-<?= $partidaAnteriorSegundoOrden?>');
						if ( element!=null ){
							element.parentNode.removeChild(element);
						}
					</script>
				<?php	
				}
			}
			?>
			<tr class="normalNegrita">
				<td>Total Bs.</td>
				<td></td>
				<td align="right"><?= number_format($totalProgramados,2,',','.')?></td>
				<td align="right"><?= number_format($totalRecibidos,2,',','.')?></td>
				<td align="right"><?= number_format($totalCedidos,2,',','.')?></td>
				<td align="right"><?= number_format($totalProgramados+$totalRecibidos-$totalCedidos,2,',','.')?></td>
				<td align="right"><?= number_format($totalDiferidos,2,',','.')?></td>
				<td align="right"><?= number_format($filaProgramadostransito["monto total"],2,',','.')?></td>
				<td align="right"><?= number_format($totalComprometidos,2,',','.')?></td>
				<td align="right"><?= number_format($totalComprometidosAislados,2,',','.')?></td>
				<td align="right"><?= number_format($totalCausados,2,',','.')?></td>
				<td align="right"><?= number_format($totalPagados,2,',','.')?></td>
				<td align="right"><?= number_format($totalDisponible - $filaProgramadostransito["monto total"],2,',','.')?></td>
				<td align="right">
				<?php
					if(round($totalProgramados+$totalRecibidos-$totalCedidos)!=0){
						echo number_format(($totalDiferidos+$totalComprometidosAislados + $filaProgramadostransito["monto total"])*100/($totalProgramados+$totalRecibidos-$totalCedidos),2,',','.')."%"; 
					}else{
						echo "0,00%";	
					}
				?>
				</td>
			</tr>
	<?
		}else{
	?>
		<tr class="titular">
			<td colspan="13" align="center" height="40px" valign="middle">
				No se encontraron resultados
			</td>
		</tr>
	<?php 
		}
		?>
</table>
<?php 
	}
}
pg_close($conexion);
?>

<script>

if( document.getElementById("opcionConsolidar").checked == true ||
		document.getElementById("opcionConsolidarProyectos").checked == true ||  
		     document.getElementById("opcionConsolidarOrganismo").checked == true ){

	  var elDiv = document.getElementById('td1'); //se define la variable "elDiv" igual a nuestro div
      elDiv.style.display='none'; //damos un atributo display:none que oculta el div  

      var elDiv2 = document.getElementById('td2'); //se define la variable "elDiv" igual a nuestro div
      elDiv2.style.display='none'; //damos un atributo display:none que oculta el div  
   
}
  
</script>
</body>
</html>