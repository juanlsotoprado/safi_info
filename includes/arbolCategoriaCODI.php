<?php
ob_start();
session_start();
require_once("conexion.php");

if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();

$campo_opcion=$_REQUEST['opcion'];//Solo mostrar para sopg
$campo_gestor=$_REQUEST['centrog'];// Campo para el Centro Gestor
$campo_costo=$_REQUEST['centroc'];// Campo para el Centro de Costo
$dependencia=$_REQUEST['dependencia']; //Dependencia
$campo_nom_supe=$_REQUEST['campo_nom_supe']; // Campo para el Nombre de la Acci�n Centralizada o Proyecto
$campo_cod_supe=$_REQUEST['campo_cod_supe']; // Campo para el CODIGO o ID de la Acci�n Centralizada o Proyecto
$campo_cod_supe2=$_REQUEST['campo_cod_supe2']; // Campo para el CODIGO o ID de la Acci�n Centralizada o Proyecto
$campo_nombre_accion=$_REQUEST['campo_nombre_accion']; // Campo para el Nombre de la Acci�n Especifica
$campo_cod_accion=$_REQUEST['campo_cod_accion']; // Campo para el CODIGO o ID de laAcci�n Especifica
$campo_cod_accion2=$_REQUEST['campo_cod_accion2']; // Campo para el CODIGO o ID de laAcci�n Especifica
$tipo=$_REQUEST['tipo']; // Nombre del radiobotton que indica Si es Proyecto o Accion Centralizada
$formulario=$_REQUEST['formulario']; // Nombre del Formulario Padre
$codigo_origen=$_REQUEST['codigo_origen'];  // prefijo del documento origen. Ejemplo: pcta, vnac, comp, etc.
$anno_pres=$_REQUEST['anno_pres'];
$campo_fte=$_REQUEST['id_fte'];//Solo mostrar para sopg
$campo_nombre_fte=$_REQUEST['nombre_fte'];// Campo para el Centro Gestor

if($anno_pres==''){
	$an_o_presupuesto= $_SESSION['an_o_presupuesto']; // A�o Presupuestario
}else{
	$an_o_presupuesto=$anno_pres;
}
// Permite desplegar el árbolCategoria para el año específicado
// en los documentos específicados
/*
$anno_pres=2012;
$an_o_presupuesto=2012;
if (
	$campo_opcion=="codi" || strcmp($campo_opcion,"codi")==0
	|| $codigo_origen=="comp" || strcmp($codigo_origen,"comp")==0
	|| $codigo_origen=="pmod" || strcmp($codigo_origen,"pmod")==0
){
	$anno_pres=2011;
	$an_o_presupuesto=2011;
}
*/
/*if ($campo_opcion=="codi" || strcmp($campo_opcion,"codi")==0){
 $an_o_presupuesto=2009;
 }*/
//echo "AO PRESUPUESTO $an_o_presupuesto";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Proyectos / Acciones Centralizadas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<script language="JavaScript" type="text/javascript">
var anMenu = 300;
var totalMen = 3;

var anImas = 17;
var alImas = 15;
var direc = '../imagenes/arbol';
var mas = '/mast.gif';
var menos = '/menost.gif';
var puntos = '/puntost.gif';
var puntosv = '/puntosvt.gif';
var carpeab = '/carpabiertat.gif';
var carpece = '/carpcerradat.gif';
var puntosu = '/puntosut.gif';
var doc = '/doct.gif';
var docsel = '/docselt.gif';
var carpeabsel = '/carpabiertasel.gif';
var carpecesel = '/carpcerradasel.gif';
var icHome = '/home.gif';
var puntosh = '/puntosh.gif';

function retorna(tex){
	var vector = tex.split("||");
	var tipo;
	if(vector[4]== 0){
		tipo=" de la accion centralizada ";// + vector[1];
	}else{
		tipo=" del proyecto: "+ vector[1];
	}
	if(confirm("Desea seleccionar  " + vector[0] + tipo +"?")){
		window.opener.document.<?= $formulario?>.<?= $campo_nom_supe?>.value=vector[1];
		window.opener.document.<?= $formulario?>.<?= $campo_cod_supe?>.value=vector[2];
		window.opener.document.<?= $formulario?>.<?= $campo_nombre_accion?>.value=vector[0];
		window.opener.document.<?= $formulario?>.<?= $campo_cod_accion?>.value=vector[3];
		window.opener.document.<?= $formulario?>.<?= $campo_cod_supe2?>.value=vector[5];
		window.opener.document.<?= $formulario?>.<?= $campo_cod_accion2?>.value=vector[6];
	<?php
		if(($campo_opcion=="sopg") || ($campo_opcion=="pcta_lib")){
	?>
		window.opener.document.<?= $formulario?>.<?= $campo_gestor?>.value=vector[5];
		window.opener.document.<?= $formulario?>.<?= $campo_costo?>.value=vector[6];
	<?php }
	if ($campo_opcion=="codi"){?>
	    window.opener.document.<?= $formulario?>.<?= $campo_fte?>.value=vector[7];
	    window.opener.document.<?= $formulario?>.<?= $campo_nombre_fte?>.value=vector[8];
	<?}?>
		if(vector[4]== 0){
			window.opener.document.<?= $formulario?>.<?= $tipo?>[1].checked=true;
		}else{
			window.opener.document.<?= $formulario?>.<?= $tipo?>[0].checked=true;
		}
		window.close();
	}
}

function tunMen(tex,enl,dest,subOp,an){
	this.tex = tex;
	this.enl = enl;
	this.dest = dest;
	this.subOp = subOp;
	this.an = an;
	this.secAc = false;
}

var i = 0;
var Op_0 = new tunMen(<?= $an_o_presupuesto?>,null, null,0);
<?php
//PROYECTOS******
//$sql_proyecto="select * from sai_buscar_proy_accion('".$_SESSION['user_depe_id']."' ,".$an_o_presupuesto.",1) as resultado_set(proy_id char,proy_titulo varchar)";
//$sql_proyecto="select * from sai_buscar_proy_accion('127' ,".$an_o_presupuesto.",1) as resultado_set(proy_id char,proy_titulo varchar)";

/*$sql_proyecto="select * from sai_proyecto sp, sai_proy_dep spd where spd.proy_id = sp.proy_id and spd.depe_id=127 and sp.esta_id <> 13 and sp.pre_anno = ".$an_o_presupuesto;*/

/*$dependencia = substr($dependencia,0,2);*/
$sql_proyecto=	"SELECT * ".
				"FROM sai_proyecto sp ".
				"WHERE ".
					"sp.esta_id <> 15 AND ".
					"sp.pre_anno = ".$an_o_presupuesto." AND ".
					"sp.proy_id IN ".
						"(SELECT spae.proy_id ".
						"FROM sai_proy_a_esp spae, sai_forma_1125 sf1125 ".
						"WHERE ".
							"spae.pres_anno = ".$an_o_presupuesto." AND ".
							"sf1125.esta_id = 1 AND ".
							"spae.pres_anno = sf1125.pres_anno AND ".
							"spae.proy_id = sf1125.form_id_p_ac AND ".
							"sf1125.form_id_aesp = spae.paes_id ".
							/*"position('".$dependencia."' in sf1125.depe_id) > 0)"*/
							(($dependencia=='550')?
								"AND 
									(
										(sf1125.depe_cosige = '".$dependencia."') OR 
										(sf1125.form_tipo = 1::BIT AND sf1125.form_id_p_ac = '111721' AND sf1125.form_id_aesp = '111721 E-1')
									) ":
								(($dependencia!="150" && $dependencia!="350" && $dependencia!="400" && $dependencia!="450" && $dependencia!="200" && $dependencia!="500" && $dependencia!="050" && $dependencia!="452")?"AND sf1125.depe_cosige = '".$dependencia."'":"")
							).")";

$resultado_set_most_proy=pg_query($conexion,$sql_proyecto) or die("Error al mostrar");
$filas=pg_num_rows($resultado_set_most_proy);
?>
var Op_1 = new tunMen("PROYECTOS",null,null,<?= $filas?>);

<?php
$contador_p = 0;
while($rowpr=pg_fetch_array($resultado_set_most_proy)){
	// $sql_ace="SELECT * FROM sai_proy_a_esp WHERE proy_id='" . $rowpr['proy_id'] . "'"; 
	/*$sql_ace="SELECT spae.* FROM sai_proy_a_esp spae, sai_forma_1125 sf1125
	where spae.proy_id = '" . $rowpr['proy_id'] . "' AND sf1125.form_id_aesp = spae.paes_id and
	position('".$dependencia."' in sf1125.depe_id) >0";*/
	$sql_ace =	"SELECT spae.*,fuente_financiamiento,fuef_descripcion ".
				"FROM sai_proy_a_esp spae, sai_forma_1125 sf1125, sai_fuente_fin ".
				"WHERE ".
					"spae.pres_anno = ".$an_o_presupuesto." AND ".
					"sf1125.esta_id = 1 AND ". 
					"sf1125.pres_anno = spae.pres_anno AND ".
					"spae.proy_id = sf1125.form_id_p_ac AND ".
					"spae.proy_id = '". $rowpr['proy_id']."' AND ".
					"sf1125.form_id_aesp = spae.paes_id AND fuente_financiamiento=fuef_id ".
					/*"position('".$dependencia."' in sf1125.depe_id)>0"*/
					(($dependencia=='550')?
						"AND 
							(
								(sf1125.depe_cosige = '".$dependencia."') OR 
								(sf1125.form_tipo = 1::BIT AND sf1125.form_id_p_ac = '111721' AND sf1125.form_id_aesp = '111721 E-1')
							) ":
						(($dependencia!="150" && $dependencia!="350" && $dependencia!="400" && $dependencia!="450" && $dependencia!="200" && $dependencia!="500" && $dependencia!="050" && $dependencia!="452")?"AND sf1125.depe_cosige = '".$dependencia."'":"")
					);

	$resultado_set_most_ace=pg_query($conexion,$sql_ace) or die("Error al mostrar");
	$filas_ace=pg_num_rows($resultado_set_most_ace); 
	if($filas_ace>0){
?>
	var Op_1_<?= $contador_p?> = new tunMen('<?= $rowpr['proy_titulo']?>',null,null,<?= $filas_ace?>);
<?php
		$contador_ace = 0;
		while($rowace=pg_fetch_array($resultado_set_most_ace)){
?>
		var Op_1_<?= $contador_p?>_<?= $contador_ace?> = new tunMen('<?= $rowace['centro_gestor']?>-<?= $rowace['centro_costo']?>:<?= $rowace['paes_nombre']?>','<?= $rowace['paes_nombre']?>||<?= $rowpr['proy_titulo']?>||<?= $rowpr['proy_id']?>||<?= $rowace['paes_id']?>||1||<?= $rowace['centro_gestor']?>||<?= $rowace['centro_costo']?>||<?= $rowace['fuente_financiamiento']?>||<?= $rowace['fuef_descripcion']?>',null,0);
<?php
	
			$contador_ace = $contador_ace +1;
		}
		$contador_p = $contador_p +1;
	}
}

//Acciones Centralizadas
//$sql_accion="select * from sai_buscar_proy_accion('".$dependencia."',".$an_o_presupuesto.",0) as resultado_set(acce_id char,acce_denom varchar)";

$sql_accion =	"SELECT * ".
				"FROM sai_ac_central sac ".
				"WHERE ".
					"sac.esta_id <> 15 AND ".
					"sac.pres_anno = ".$an_o_presupuesto." AND ".
					"sac.acce_id IN ".
						"(SELECT spae.acce_id ".
						"FROM sai_acce_esp spae, sai_forma_1125 sf1125 ".
						"WHERE ".
							"spae.pres_anno = ".$an_o_presupuesto." AND ".
							"sf1125.esta_id=1 AND ". 
							"spae.pres_anno = sf1125.pres_anno AND ".							
							"sf1125.form_id_aesp = spae.aces_id AND ".
							"sf1125.form_id_p_ac = spae.acce_id AND ".
							"sf1125.esta_id = 1 ".
						")";
							/*"position('".$dependencia."' in sf1125.depe_id) >0)"*/
							//(($dependencia!="150" && $dependencia!="350" && $dependencia!="400" && $dependencia!="450" && $dependencia!="200" && $dependencia!="500" && $dependencia!="050" && $dependencia!="452")?"AND sf1125.depe_cosige = '".$dependencia."'":"").")";

$resultado_set_accion=pg_query($conexion,$sql_accion) or die("Error al mostrar Accion");
$filas_accion=pg_num_rows($resultado_set_accion);
?>
var Op_2 = new tunMen("ACCIONES CENTRALIZADAS",null,null,<?= $filas_accion?>);
<?php
$contador_accion = 0;
while($row_accion=pg_fetch_array($resultado_set_accion)){
	//$sql_aces="SELECT * FROM sai_acce_esp WHERE acce_id='" . $row_accion['acce_id'] . "'  AND pres_anno=".$an_o_presupuesto; 

	$sql_aces =	"SELECT spae.*,fuente_financiamiento,fuef_descripcion ".
				"FROM sai_acce_esp spae, sai_forma_1125 sf1125, sai_fuente_fin ".
				"WHERE ".
					"spae.pres_anno = ".$an_o_presupuesto." AND ".
					"sf1125.esta_id = 1 AND ". 
					"sf1125.pres_anno = spae.pres_anno AND ".
					"spae.acce_id = '".$row_accion['acce_id']."' AND ".
					"sf1125.form_id_aesp = spae.aces_id AND ".
					"sf1125.form_id_p_ac = spae.acce_id AND fuente_financiamiento=fuef_id ";
					/*"position('".$dependencia."' in sf1125.depe_id) >0"*/
					(($dependencia=='550')?
						"AND 
							(
								(sf1125.depe_cosige = '".$dependencia."') OR 
								(sf1125.form_tipo = 1::BIT AND sf1125.form_id_p_ac = '111721' AND sf1125.form_id_aesp = '111721 E-1')
							) ":
						(($dependencia!="150" && $dependencia!="350" && $dependencia!="400" && $dependencia!="450" && $dependencia!="200" && $dependencia!="500" && $dependencia!="050" && $dependencia!="452")?"AND sf1125.depe_cosige = '".$dependencia."'":"")
					);

	$resultado_set_aces=pg_query($conexion,$sql_aces) or die("Error al mostrar Accion Especifica");
	$filas_aces=pg_num_rows($resultado_set_aces); 
	if($filas_aces>0){
?>
	var Op_2_<?= $contador_accion?> = new tunMen('<?= $row_accion['acce_denom']?>',null,null,<?= $filas_aces?>);
<?php
		$contador_aces = 0;
		while($rowaces=pg_fetch_array($resultado_set_aces)){
	
?>
		var Op_2_<?= $contador_accion?>_<?= $contador_aces?> = new tunMen('<?= $rowaces['centro_gestor']?>-<?= $rowaces['centro_costo']?>:<?= $rowaces['aces_nombre']?>','<?= $rowaces['aces_nombre']?>||<?= $row_accion['acce_denom']?>||<?= $row_accion['acce_id']?>||<?= $rowaces['aces_id']?>||0||<?= $rowaces['centro_gestor']?>||<?= $rowaces['centro_costo']?>||<?= $rowaces['fuente_financiamiento']?>||<?= $rowaces['fuef_descripcion']?>',null,0);
<?
			$contador_aces = $contador_aces +1;
		}
		$contador_accion = $contador_accion +1;
	}
}
?>
var anchoTotal = 912;
var tunIex=navigator.appName=="Microsoft Internet Explorer"?true:false;
if(tunIex && navigator.userAgent.indexOf('Opera')>=0){tunIex = false}
var manita = tunIex ? 'hand' : 'pointer';
var subOps = new Array();

function construye(){
	cajaMenu = document.createElement('div');
	cajaMenu.style.width = anMenu + "px";
	document.getElementById('tunMe').appendChild(cajaMenu);
	for(m=0; m < totalMen; m++){
		opchon = eval('Op_'+m);
		ultimo = false;
		try{
			eval('Op_' + (m+1));
		}catch(error){
			ultimo = true;
		}
		boton = document.createElement('div');
		boton.style.position = 'relative';
		boton.className = 'botones';
		boton.style.paddingLeft= 0;
		carp = document.createElement('img');
		carp.style.marginRight = 5 + 'px';
		carp.style.verticalAlign = 'middle';
		carp2 = document.createElement('img');
		carp2.style.verticalAlign = 'middle';

		enla = document.createElement('a');
		if(opchon.subOp > 0){
			carp2.style.cursor = manita;
			carp2.src = direc + mas;
			boton.secAc = opchon.secAc;
		}else{
			carp2.style.cursor = 'default';
			enla.className = 'enls';
			if(ultimo){carp2.src = direc + puntosu}
			else{carp2.src = direc + puntos}
		}
		if(m == 0){
			carp.src = direc + icHome;
			carp2.src = direc + puntosh;
		}else{
			carp.src = direc + carpece;
		}
		boton.appendChild(carp2);
		boton.appendChild(carp);
		enla.className = 'enls';
		enla.style.cursor = manita;
		boton.appendChild(enla);
		enla.appendChild(document.createTextNode(opchon.tex));
		if(tunIex){
			enla.onmouseover = function(){this.className = 'botonesHover'}
			enla.onmouseout = function(){this.className = 'enls'}
		}
		/*if(opchon.enl != null && opchon.subOp == 0){  //validan enlace
			//	enla.href = opchon.enl
		}*/
		/*if(opchon.dest != null && opchon.subOp == 0){   //validan destino
			enla.target = opchon.dest;
			}*/
		boton.id = 'op_' + m;
	
		cajaMenu.appendChild(boton);
		if(opchon.subOp > 0 ){
			carp2.onclick= function(){ abre(this.parentNode,this,this.nextSibling); }
			subOps[subOps.length] = boton.id.replace(/o/,"O");
			enla.onclick = function(){ abre(this.parentNode,this.parentNode.firstChild,this.previousSibling); }
		}
	}
	if(subOps.length >0){subMes()}
}

function subMes(){
	lar = subOps.length;
	for(t=0;t<subOps.length;t++){
		opc =eval(subOps[t]);
		for(v=0;v<opc.subOp;v++){
			if(eval(subOps[t] + "_" + v + ".subOp") >0){
				subOps[subOps.length] = subOps[t] + "_" + v;
			}
		}
	}
	construyeSub();
}

var fondo = true;

function construyeSub(){
	for(y=0; y<subOps.length;y++){
		opchon = eval(subOps[y]);
		capa = document.createElement('div');
		capa.className = 'subMe';
		capa.style.position = 'relative';
		capa.style.display = 'none';
		if(!fondo){capa.style.backgroundImage = 'none'}
		document.getElementById(subOps[y].toLowerCase()).appendChild(capa);
		for(s=0;s < opchon.subOp; s++){
			sopchon = eval(subOps[y] + "_" + s);
			ultimo = false;
			try{
				eval(subOps[y] + "_" + (s+1));
			}catch(error){
				ultimo = true;
			}
			if(ultimo && sopchon.subOp > 0){
				fondo = false;
			}
			opc = document.createElement('div');
			opc.className = 'botones';
			opc.id = subOps[y].toLowerCase() + "_" + s;

			//if(tunIex){}
			enla = document.createElement('a');
			enla.className = 'enls';
			enla.style.cursor = manita;

			/*Quite esta condicion para mostrar los enlaces en las carpetas(&& sopchon.subOp == 0)*/
			if(sopchon.enl != null && sopchon.subOp == 0){    
				enla.Id = sopchon.enl;
				var vector1 = sopchon.enl.split("||");
				enla.title=vector1[0];
				 /*Quite esta condicion para mostrar el destino de los enlaces (&& sopchon.subOp == 0)*/
				//if(sopchon.dest != null )
				/* {
					enla.target = sopchon.dest
				 }*/
			}

			enla.appendChild(document.createTextNode(sopchon.tex));
			capa.appendChild(opc);
			carp = document.createElement('img');
			carp.src = direc + carpece;
			carp.style.verticalAlign = 'middle';
			carp.style.marginRight = 5 + 'px';
			carp2 = document.createElement('img');
			carp2.style.verticalAlign = 'middle';
			if(sopchon.subOp>0){
				opc.secAc = sopchon.secAc;
				carp2.style.cursor = manita;
				carp2.src = direc + mas;
				enla.onclick = function(){ abre(this.parentNode,this.parentNode.firstChild,this.previousSibling); }
				carp2.onclick= function(){ abre(this.parentNode,this,this.nextSibling); }
				if(tunIex){
					enla.onmouseover = function(){this.className = 'botonesHover'}
					enla.onmouseout = function(){this.className = 'enls'}
				}
			}else{
	    		enla.onclick = function(){if(this.Id != null){javascript:retorna (this.Id);}}
				carp2.style.cursor = 'default';
				carp.src = direc + doc;
				if(ultimo){
					carp2.src = direc + puntosu; 
					if(sopchon.subOp > 0){capa.style.backgroundImage = 'none'}
				}else{carp2.src = direc + puntos}
			}
			opc.appendChild(carp2);
			opc.appendChild(carp);
			opc.appendChild(enla);
		}
	}
	Seccion();
}

function abre(cual,im,car){
	abierta = cual.lastChild.style.display != 'none'? true:false;
	if(abierta){
		cual.lastChild.style.display = 'none';
		im.src = direc + mas;
		if(cual.secAc){
			car.src = direc + carpecesel;		
		}else{car.src = direc + carpece}
	}else{
		cual.lastChild.style.display = 'block';
		im.src = direc + menos;
		if(cual.secAc){car.src = direc + carpeabsel}
		else{car.src = direc + carpeab}
	}
}

var seccion = null;

function Seccion(){
	if(seccion != null){
		if(seccion.length == 4){
			document.getElementById(seccion.toLowerCase()).firstChild.nextSibling.src = direc + carpeabsel;
			document.getElementById(seccion.toLowerCase()).lastChild.className = 'secac2';
			document.getElementById(seccion.toLowerCase()).lastChild.onmouseover = function(){ this.className = 'enls'; }
			document.getElementById(seccion.toLowerCase()).lastChild.onmouseout = function(){ this.className = 'secac2'; }
		}else{
			document.getElementById(seccion.toLowerCase()).firstChild.nextSibling.src = direc + docsel;
			document.getElementById(seccion.toLowerCase()).firstChild.nextSibling.nextSibling.className = 'secac';
			document.getElementById(seccion.toLowerCase()).parentNode.parentNode.lastChild.previousSibling.className = 'secac2'; 
			document.getElementById(seccion.toLowerCase()).parentNode.parentNode.lastChild.previousSibling.onmouseout = function(){	this.className = 'secac2'; }
			if(!tunIex){
				document.getElementById(seccion.toLowerCase()).parentNode.parentNode.lastChild.previousSibling.onmouseover = function(){ this.className = 'enls'; }
			}
			document.getElementById(seccion.toLowerCase()).parentNode.parentNode.secAc = true;
			seccion = seccion.substring(0,seccion.length - 2);
			seccionb = document.getElementById(seccion.toLowerCase());
			abre(seccionb,seccionb.firstChild,seccionb.firstChild.nextSibling);
			if(seccion.length > 4){
				lar = seccion.length;
				for(x = lar; x > 4; x-=2){
					seccion = seccion.substring(0,seccion.length - 2);
					seccionb = document.getElementById(seccion.toLowerCase());
					abre(seccionb,seccionb.firstChild,seccionb.firstChild.nextSibling);
				}
			}
		}
	}
}
onload = construye;
</script>
<style type="text/css">
<!--
a.enls:link,a.enls:visited{
	color: #336699;
	text-decoration: none;
}
a.enls:hover{
	color: #FF0000;
	background-color: #eeeeee;
}
a.secac2{
	color: #B87070;
	text-decoration: none;
}
a.secac{
	color: #FFFFFF;
	text-decoration: none;
	background-color: #CC0000;
}
a.secac:hover{
	color: #B87070;
	text-decoration: none;
	background-color: #ffffff;
}
.botones{
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #3366CC;
	margin: 0;
	padding-left: 18px;
	text-decoration: none;
	text-align: left;
}
.botonesHover{
	text-decoration: none;
	color: #00CCFF;
	background-color: #eeeeee;
}
.subMe{
	display: none;
	margin: 0;
	background-image: url(imasmenu/puntosvt.gif);
	background-repeat: repeat-y;
}
body{
	background-color: #FFFFFF;
	font-family: verdana, tahoma, arial, sans serif;
	font-size: 12px;
}
-->
</style>
</head>
<body>
	<div id="tunMe"></div>
	<div align="center">
		<p class="Normal">Seleccione la acci&oacute;n espec&iacute;fica haciendo click sobre &eacute;sta</p>
		<p>
			<a href="javascript:window.close()">
				<img src="../../imagenes/boton_cerrar.gif" name="cerrar" width="90" height="31" border="0" id="cerrar"/>
			</a>
		</p>
	</div>
</body>
</html>
<?php pg_close($conexion);?>