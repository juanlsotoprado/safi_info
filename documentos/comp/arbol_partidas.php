<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");
pg_query($conexion,"set client_encoding to 'LATIN1'");

if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}

ob_end_flush();
?>
<?php
$dependencia="1"; //Dependencia
$tipo_doc=$_REQUEST['tipo_doc']; // Indica el tipo de Requisicion Compra o Almacen
$an_o_presupuesto= $_SESSION['an_o_presupuesto']; // A�o Presupuestario
$perfil=$_SESSION['user_perfil_id'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css"
	media="all" />
<title>.:SAFI::Plan de Cuentas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style type="text/css">
<!--
.Estilo1 {
	color: #FFFFFF
}
-->
</style>
<script language="JavaScript" src="../../includes/js/funciones.js"> </script>
<script language="JavaScript" type="text/javascript">


var anMenu = 300
var totalMen =3
var anImas = 17
var alImas = 15
var direc = '../../imagenes/arbol'
var mas = '/mast.gif'
var menos = '/menost.gif'
var puntos = '/puntost.gif'
var puntosv = '/puntosvt.gif'
var carpeab = '/carpabiertat.gif'
var carpece = '/carpcerradat.gif'
var puntosu = '/puntosut.gif'
var doc = '/doct.gif'
var docsel = '/docselt.gif'
var carpeabsel = '/carpabiertasel.gif'
var carpecesel = '/carpcerradasel.gif'
var icHome = '/home.gif'
var puntosh = '/puntosh.gif'

var partidas = new Array();

function retorna(tex)
{
var vector = tex.split("||");

if(confirm('Desea agregar la partida: '+vector[1])){
    document.form.txt_cod_pda.value=vector[0];
    document.form.txt_des_pda.value=vector[1];  
    add_pda('tbl_pda','0');
	
  }
}

function tunMen(tex,enl,dest,subOp,an){
this.tex = tex;
this.enl = enl;
this.dest = dest;
this.subOp = subOp;
this.an = an;
this.secAc = false
}
<?php

$anno_pres=$_REQUEST['anno_pres'];
$anno_pres=2014;
  
  if($anno_pres==''){
    $an_o_presupuesto= $_SESSION['an_o_presupuesto']; // A�o Presupuestario
  }else{
    $an_o_presupuesto=$anno_pres;
  }

?>
var i=0
var Op_0 = new tunMen("<?php echo $an_o_presupuesto; ?>",null, null,0)

<?php
$sql="SELECT * FROM sai_consulta_partidas0305(".$an_o_presupuesto.",'4',1) as resultado(a varchar,b int4,c varchar,d bool)  ";
$rs_pdas=pg_exec($conexion,$sql) or die("Error al mostrar");
$filas=pg_num_rows($rs_pdas);
$contador_n = 1;
?>
var totalMen = <? echo $filas+1;?>
<?php
	$contador_p = 0;
	while($rowpr=pg_fetch_array($rs_pdas))
	{ 
 	 $sql_2="SELECT * FROM sai_consulta_partidas0305(".$an_o_presupuesto.",'".substr(trim($rowpr['a']),0, 4)."',2) as resultado(a varchar,b int4,c varchar,d bool)";
	 $rs_subpdas=pg_exec($conexion,$sql_2) or die("Error al mostrar");
	 $filas_ace=pg_num_rows($rs_subpdas); 
 ?>  
	var Op_<?php echo($contador_n) ?> = new tunMen('<?php echo(trim($rowpr['a']));?>',null,null,<?php echo($filas_ace); ?>)
<?php
  $contador_ace = 0;
  while($rowace=pg_fetch_array($rs_subpdas))
  {
   $estado= 1; //Indica que el articulo esta activo
   $sql_3="SELECT * FROM sai_consulta_partidas0305(".$an_o_presupuesto.",'".substr(trim($rowace['a']),0, 7)."',3) as resultado(a varchar,b int4,c varchar,d bool)";
   $rs_subesp=pg_exec($conexion,$sql_3) or die("Error al mostrar");
   $filas_art=pg_num_rows($rs_subesp);
   ?>
	var Op_<?php echo($contador_n) ?>_<?php echo($contador_ace) ?>=new tunMen('<?php echo(trim($rowace['a']));?>',null,null,<?php echo($filas_art); ?>)   
   <?php
   $contador_art = 0;
   
   while($rowart=pg_fetch_array($rs_subesp))
   { 
	 $exist=1;
     //Consultamos la descripcion de la partida
	 $sql_4="SELECT * FROM sai_consulta_desc_pda('".trim($rowart['a'])."',".$an_o_presupuesto.") as a"; 
	 $despda=pg_exec($conexion,$sql_4) or die("Error al mostrar");
     if($despda){
	   $rs_despda=pg_fetch_array($despda);
	 }
     ?>
     var Op_<?php echo($contador_n) ?>_<?php echo($contador_ace) ?>_<?php echo($contador_art) ?>=new tunMen('<?php echo(trim($rowart['a']));?>','<?=$rowart['a']?>||<?=$rs_despda['a']?>||<?=$rowart['a']?>||<?=$rowart['a']?>||<?=$rowart['a']?>',null,0)
	 <?php
	   $contador_art = $contador_art +1;
	 }
      $contador_ace = $contador_ace +1;
  } 
  $contador_n = $contador_n +1;
} 
?>
var anchoTotal = 912
var tunIex=navigator.appName=="Microsoft Internet Explorer"?true:false;
if(tunIex && navigator.userAgent.indexOf('Opera')>=0){tunIex = false}
var manita = tunIex ? 'hand' : 'pointer'
var subOps = new Array()
function construye(){
cajaMenu = document.createElement('div')
cajaMenu.style.width = anMenu + "px"
document.getElementById('tunMe').appendChild(cajaMenu)
for(m=0; m < totalMen; m++){
	opchon = eval('Op_'+m)
	ultimo = false
	try{
	eval('Op_' + (m+1))
	}
	catch(error){
	ultimo = true
	}
	boton = document.createElement('div')
	boton.style.position = 'relative'
	boton.className = 'botones'
	boton.style.paddingLeft= 0
	carp = document.createElement('img')
	carp.style.marginRight = 5 + 'px'	
	carp.style.verticalAlign = 'middle'
	carp2 = document.createElement('img')
	carp2.style.verticalAlign = 'middle'
	enla = document.createElement('a')
	if(opchon.subOp > 0){
		carp2.style.cursor = manita
		carp2.src = direc + mas
		boton.secAc = opchon.secAc
		}
	else{
		carp2.style.cursor = 'default'
		enla.className = 'enls'
		if(ultimo){carp2.src = direc + puntosu}
		else{carp2.src = direc + puntos}
		}
		if(m == 0){
		carp.src = direc + icHome
		carp2.src = direc + puntosh
		}
	else{
		carp.src = direc + carpece
		}
	boton.appendChild(carp2)
	boton.appendChild(carp)
	enla.className = 'enls'
	enla.style.cursor = manita
	boton.appendChild(enla)
	enla.appendChild(document.createTextNode(opchon.tex))
	if(tunIex){
		enla.onmouseover = function(){this.className = 'botonesHover'}
		enla.onmouseout = function(){this.className = 'enls'}
		}
	if(opchon.enl != null && opchon.subOp == 0){
			enla.href = opchon.enl
			}
		if(opchon.dest != null && opchon.subOp == 0){
			enla.target = opchon.dest;
			}
	boton.id = 'op_' + m
	cajaMenu.appendChild(boton)
	if(opchon.subOp > 0 ){
		carp2.onclick= function(){
		abre(this.parentNode,this,this.nextSibling)
		}
		subOps[subOps.length] = boton.id.replace(/o/,"O")
		enla.onclick = function(){
			abre(this.parentNode,this.parentNode.firstChild,this.previousSibling)
			}
		}
	}
if(subOps.length >0){subMes()}
}
function subMes(){
lar = subOps.length
for(t=0;t<subOps.length;t++){
	opc =eval(subOps[t])
	for(v=0;v<opc.subOp;v++){
		if(eval(subOps[t] + "_" + v + ".subOp") >0){
			subOps[subOps.length] = subOps[t] + "_" + v
			}
		}
	}
construyeSub()
}
var fondo = true
function construyeSub(){
for(y=0; y<subOps.length;y++){
opchon = eval(subOps[y])
capa = document.createElement('div')
capa.className = 'subMe'
capa.style.position = 'relative'
capa.style.display = 'none'
if(!fondo){capa.style.backgroundImage = 'none'}
document.getElementById(subOps[y].toLowerCase()).appendChild(capa)
	for(s=0;s < opchon.subOp; s++){
		sopchon = eval(subOps[y] + "_" + s)
		ultimo = false
		try{
			eval(subOps[y] + "_" + (s+1))
			}
		catch(error){
			ultimo = true
			}
			if(ultimo && sopchon.subOp > 0){
			fondo = false
			}
		opc = document.createElement('div')
		opc.className = 'botones'
		opc.id = subOps[y].toLowerCase() + "_" + s
		if(tunIex){
			}
		enla = document.createElement('a')
		enla.className = 'enls'
		enla.style.cursor = manita
		if(sopchon.enl != null && sopchon.subOp == 0){
			enla.Id = sopchon.enl;
			var vector1 =sopchon.enl.split("||");
		    enla.title=vector1[1];	
			}
		
		enla.appendChild(document.createTextNode(sopchon.tex))
		capa.appendChild(opc)
		carp = document.createElement('img')
		carp.src = direc + carpece
		carp.style.verticalAlign = 'middle'
		carp.style.marginRight = 5 + 'px'
		carp2 = document.createElement('img')
		carp2.style.verticalAlign = 'middle'
		if(sopchon.subOp > 0){
			opc.secAc = sopchon.secAc
			carp2.style.cursor = manita
			carp2.src = direc + mas
				enla.onclick = function(){
				abre(this.parentNode,this.parentNode.firstChild,this.previousSibling)
				}
			carp2.onclick= function(){
			abre(this.parentNode,this,this.nextSibling)
			}
			if(tunIex){
			enla.onmouseover = function(){this.className = 'botonesHover'}
			enla.onmouseout = function(){this.className = 'enls'}
			}
			}
		else{
		    enla.onclick = function(){if(this.Id != null){javascript:retorna (this.Id);}}
			carp2.style.cursor = 'default'
			carp.src = direc + doc
			if(ultimo){carp2.src = direc + puntosu; 
			if(sopchon.subOp > 0){capa.style.backgroundImage = 'none'}
			}
			else{carp2.src = direc + puntos}
				}
		opc.appendChild(carp2)
		opc.appendChild(carp)
		opc.appendChild(enla)
		
		}
	}
Seccion()
}
function abre(cual,im,car){
abierta = cual.lastChild.style.display != 'none'? true:false;
if(abierta){
	cual.lastChild.style.display = 'none'
	im.src = direc + mas
	if(cual.secAc){
		car.src = direc + carpecesel
		
		}
	else{car.src = direc + carpece}
	}
else{
	cual.lastChild.style.display = 'block'
	im.src = direc + menos
	if(cual.secAc){car.src = direc + carpeabsel}
	else{car.src = direc + carpeab}
	}
}


var seccion = null

function Seccion(){
if (seccion != null){
	if(seccion.length == 4){
		document.getElementById(seccion.toLowerCase()).firstChild.nextSibling.src = direc + carpeabsel
		document.getElementById(seccion.toLowerCase()).lastChild.className = 'secac2'
		document.getElementById(seccion.toLowerCase()).lastChild.onmouseover = function(){
			this.className = 'enls'
			}
		document.getElementById(seccion.toLowerCase()).lastChild.onmouseout = function(){
			this.className = 'secac2'
			}
		}
	else{
		document.getElementById(seccion.toLowerCase()).firstChild.nextSibling.src = direc + docsel
		document.getElementById(seccion.toLowerCase()).firstChild.nextSibling.nextSibling.className = 'secac'
		document.getElementById(seccion.toLowerCase()).parentNode.parentNode.lastChild.previousSibling.className = 'secac2' 
		
			document.getElementById(seccion.toLowerCase()).parentNode.parentNode.lastChild.previousSibling.onmouseout = function(){
			this.className = 'secac2'
			}
			if(!tunIex){
			document.getElementById(seccion.toLowerCase()).parentNode.parentNode.lastChild.previousSibling.onmouseover = function(){
			this.className = 'enls'
			}
		}
		document.getElementById(seccion.toLowerCase()).parentNode.parentNode.secAc = true
		seccion = seccion.substring(0,seccion.length - 2)
		seccionb = document.getElementById(seccion.toLowerCase())
		abre(seccionb,seccionb.firstChild,seccionb.firstChild.nextSibling)
		if(seccion.length > 4){
		lar = seccion.length
			for(x = lar; x > 4; x-=2){
				seccion = seccion.substring(0,seccion.length - 2)
				seccionb = document.getElementById(seccion.toLowerCase())
				abre(seccionb,seccionb.firstChild,seccionb.firstChild.nextSibling)
				}
			}
		}
	}
}
onload = construye
</script>
<script language="JavaScript" type="text/javascript"><!--


function add_pda(id,tipo)
{   
	if(tipo==0){
		if(document.form.txt_cod_pda.value==""){
			alert("Debe seleccionar un c\u00F3digo de partida");
			return
		}
		var registro = new Array(2);
		registro[0] = document.form.txt_cod_pda.value;
		registro[1] = document.form.txt_des_pda.value;
		partidas[partidas.length]=registro;
	}
	
	var tbody = document.getElementById('body');
	var tbody2 = document.getElementById(id);

	for(i=0;i<partidas.length-1;i++)
	{
		tbody2.deleteRow(2);
	}
	if(tipo!=0)
	{
		tbody2.deleteRow(2);
		for(i=tipo;i<partidas.length;i++)
		{
			partidas[i-1]=partidas[i];
		}
		partidas.pop();
	}
	var subtotal = 0;
	for(i=0;i<partidas.length;i++)
	{

		var row = document.createElement("tr")
		
		if((i%2)==0)
			row.className = "reci2"
		else
			row.className = "reci"
            
			//creamos una columna
		   	var td1 = document.createElement("td");
			td1.setAttribute("align","Center");
			td1.className = 'titularMedio';
			//creamos una variable oculta
			var imp_1 = document.createElement("INPUT");
			imp_1.setAttribute("type","hidden");
			var name="txt_codigo"+i;
	        imp_1.setAttribute("name",name);
			imp_1.className = "normal";
	        imp_1.setAttribute("value",partidas[i][0]);
			imp_1.setAttribute("id",name);	      
			hid_1_text = document.createTextNode(partidas[i][0]);
			td1.appendChild(imp_1);			
		    td1.appendChild(hid_1_text);	
			
			//Creamos la segunda columna
			var td2 = document.createElement("td");
			td2.setAttribute("align","Center");
			td2.className = 'titularMedio';
			//Creamos la segunda variable oculta
			var imp_2 = document.createElement("INPUT");
			imp_2.setAttribute("type","hidden");
	        name="txt_den"+i;
	        imp_2.setAttribute("name",name);
			imp_2.className = "normal";
			imp_2.setAttribute("value",partidas[i][1]);
			imp_2.setAttribute("id",name);
			imp_2.setAttribute("size","30");
			hid_2_text = document.createTextNode(partidas[i][1]);						
		    td2.appendChild (imp_2);		
		    td2.appendChild(hid_2_text);	
		    
			//Se crea la tercera columna
			var td3 = document.createElement("td");				
			td3.setAttribute("align","Center");
			td3.className = 'link';
			editLink = document.createElement("a");
			linkText = document.createTextNode("Eliminar");
			editLink.setAttribute("href", "javascript:add_pda('"+id+"','"+(i+1)+"')");
			editLink.appendChild(linkText);
			td3.appendChild (editLink);
			row.appendChild(td1);
			row.appendChild(td2);
			row.appendChild(td3);
			tbody.appendChild(row);
	}	
	document.form.txt_cod_pda.value = "";
	document.form.txt_des_pda.value = "";
	document.form.largo.value = partidas.length;	
}


function enviar()
{   
    var nave;
	var j;
	
	nave=new String(navigator.appName);
	var pos_nave=nave.indexOf("Explorer");
    
	if(partidas.length==0){
	   alert("No se ha agregado ninguna partida al formato !!");
	   return
	}
	
	 var largo_partidas=window.opener.document.getElementById('hid_largo').value;	
	 var op_tbody = window.opener.document.getElementById('ar_body');
	 var tbody2 = window.opener.document.getElementById('tbl_part');	 
	 var element_otros=0;	 
	 element_otros = window.opener.document.getElementById('tbl_part').getElementsByTagName('tr').length;
	 element_otros = element_otros -1;	       
     j=element_otros;  
	
	//agrega los elementos
	for(i=0;i<partidas.length;i++)
	{
    	   	 	    	 
		var row = window.opener.document.createElement("tr")
				
		if((i%2)==0)
			row.className = "reci2"
		else
			row.className = "reci"
            
			//creamos una columna
		   	var td1 = window.opener.document.createElement("td");
			td1.setAttribute("align","Center");
			td1.className = 'normalNegro';
			//creamos una variable oculta
			var hid_1 = window.opener.document.createElement("INPUT");
			hid_1.setAttribute("type","hidden");
			var name="txt_codigo"+j;
	        hid_1.setAttribute("name",name);
			hid_1.className = "normalNegro";
	        hid_1.setAttribute("value",partidas[i][0]);
			hid_1.setAttribute("id",name);	       
			hid_1_text = window.opener.document.createTextNode(partidas[i][0]);								
			td1.appendChild(hid_1);			
		    td1.appendChild(hid_1_text);
		    			
			//Creamos la segunda columna
			var td2 = window.opener.document.createElement("td");
			td2.setAttribute("align","Center");
			td2.className = 'normalNegro';
			//Creamos la segunda variable oculta
			var imp_2 = window.opener.document.createElement("INPUT");
			imp_2.setAttribute("type","hidden");
	        name="txt_den"+j;
	        imp_2.setAttribute("name",name);
			imp_2.className = "normalNegro";
			imp_2.setAttribute("value",partidas[i][1]);
			imp_2.setAttribute("id",name);
			imp_2.setAttribute("size","30");
			hid_2_text = window.opener.document.createTextNode(partidas[i][1]);						
		    td2.appendChild (imp_2);		
		    td2.appendChild(hid_2_text);	
		    					
			//Creo la tercera columna		
			var td3 = window.opener.document.createTextNode("");
			//Creamos la segunda variable oculta	
					
			//Se crea la cuarta columna
			var td4 = window.opener.document.createElement("td");
			td4.setAttribute("align","Center");
			//Creamos la segunda variable oculta
		    name="txt_monto"+j;
			if(pos_nave>0){
			    var imp_4 = window.opener.document.createElement('<input type="text" name="'+name+'" onKeyUp="FormatCurrency(this)" >');		
			}
			else{
			    var imp_4 = window.opener.document.createElement("INPUT");
			    imp_4.setAttribute("type","text");
	            imp_4.setAttribute("name",name);
				//imp_4.setAttribute("onKeyUp","FormatCurrency(this)");
			} 

			imp_4.className = "normalNegro";
			imp_4.setAttribute("id",name);
			imp_4.setAttribute("size","10");
		    td4.appendChild (imp_4);	

			//Se crea la cuarta columna
			var td5 = window.opener.document.createElement("td");
			td5.setAttribute("align","Center");
			//Creamos la segunda variable oculta
		    name="monto_comp"+j;
		    var imp_5 = window.opener.document.createElement("INPUT");
			imp_5.setAttribute("type","text");
	        imp_5.setAttribute("name",name);
			imp_5.setAttribute("onKeyUp","FormatCurrency(this)");
			imp_5.className = "normalNegro";
			imp_5.setAttribute("id",name);
			imp_5.setAttribute("readOnly","true");
			imp_5.setAttribute("size","10");
		    td5.appendChild (imp_5);
 
			row.appendChild(td1);
			row.appendChild(td2);
			row.appendChild(td3);
			row.appendChild(td5);
			row.appendChild(td4);
			op_tbody.appendChild(row);			
			 j=j+1;
	}	
  	        
			window.opener.document.getElementById('hid_val').value='true';
            window.close();
}


//
--></script>
<style type="text/css">
<!--
a.enls:link,a.enls:visited {
	color: #3366CC;
	text-decoration: none;
}

a.enls:hover {
	color: #CC0000;
	background-color: #eeeeee;
}

a.secac2 {
	color: #B87070;
	text-decoration: none;
}

a.secac {
	color: #FFFFFF;
	text-decoration: none;
	background-color: #CC0000;
}

a.secac:hover {
	color: #B87070;
	text-decoration: none;
	background-color: #ffffff;
}

.botones {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-weight: bold;
	color: #3366CC;
	margin: 0;
	padding-left: 18px;
	text-decoration: none;
	text-align: left;
}

.botonesHover {
	text-decoration: none;
	color: #CC0000;
	background-color: #eeeeee;
}

/* Atenci�n, evitar alterar la clase .subMe */
.subMe {
	display: none;
	margin: 0;
	background-image: url(imasmenu/puntosvt.gif);
	background-repeat: repeat-y;
}

/* Atenci�n, evitar alterar la clase .subMe */
body {
	font-family: verdana, tahoma, arial, sans serif;
	font-size: 11px;
}
-->
</style>
</head>
<body>
<div id="tunMe"></div>
<table width="200" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>
<form name="form" method="post" action="">
<div align="center">
<table width="480" border="0" class="tablaCentral" id="tbl_pda">
	<!--DWLayoutTable-->
	<tr>
		<td bgcolor="#0099CC" width="90" class="normal" align="right">
		<div align="center"><span class="Estilo1"><strong> C&oacute;d. Partida
		</strong></span></div>
		</td>
		<td width="350" valign="middle" bgcolor="#0099CC" class="normal">
		<div align="center"><span class="Estilo1"><strong> Descripci&oacute;n</strong></span>
		</div>
		</td>
		<td width="40" valign="middle" bgcolor="#0099CC" class="normalNegro"></td>
	</tr>
	 <tr>
		<td class="normal" valign="middle"><input name="txt_cod_pda"
			type="hidden" disabled="disabled" class="normalNegro" id="txt_cod_pda" size="15"
			maxlength="15" /></td>
		<td valign="middle" class="Normal">
		<div align="center"><input name="txt_des_pda" type="hidden"
			disabled="disabled" class="normalNegro" id="txt_des_pda" size="50" /></div>
		</td>
	<!-- 	<td valign="middle" class="normalNegro">
		<div align="center"><a href="javascript: add_pda('tbl_pda','0')"
			class="link">Agregar</a></div>
		</td> -->
	</tr> 
	<tbody id="body" class="normalNegro">
	</tbody>
	<tr>
		<td align="center"></td>
		<td>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
				<div align="right">
				<input type="button" value="Confirmar" onclick="javascript:enviar()"></div>
				</td>
				<td>
				<input type="button" value="Cerrar" onclick="javascript:window.close()">
			</td>
			</tr>
		</table>
		</td>
		<td width="132" height="50%" align="center" class="normal"><!--DWLayoutEmptyCell-->&nbsp;
		</td>
	</tr>
</table>
</div>
<p><input type="hidden" name="largo" id="largo" /></p>
</form>
</body>
</html>

