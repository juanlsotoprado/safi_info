<?php 
    ob_start();
	session_start();
	 require_once("../../includes/conexion.php");
	 
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
  $id_p_c=$_REQUEST['id_p_c']; 
  $id_ac=$_REQUEST['id_ac'];   
  $arreglo=trim($_REQUEST['arre']);
 // echo "Arreglo Actual: ".$arreglo;
 
 ?>

<?PHP /*Consulto los impuesto  IVA*/
	$sql= "select * from sai_consulta_impuestos ('0','IVA') as resultado ";
	$sql.= "(id varchar, nombre varchar, porcetaje float4,  principal bit, tipo bit)";
	$resultado_set= pg_exec($conexion ,$sql);
	$valido=$resultado_set;
		if ($resultado_set)
  		{
			$elem_impuesto=pg_num_rows($resultado_set);
			$id_impuesto=array($elem_impuesto);
			$porce_impuesto=array($elem_impuesto);
			$impu_nombre=array($elem_impuesto);
			$impu_prici=array($elem_impuesto);
			$ii=0;
 			while($row_rete=pg_fetch_array($resultado_set))	
			 {
			   $id_impuesto[$ii]=strtoupper(trim($row_rete['id']));
			   $porce_impuesto[$ii]=trim($row_rete['porcetaje']);
			   $impu_prici[$ii]=trim($row_rete['principal']);
			   $impu_nombre[$ii]=trim($row_rete['nombre']);
			   $ii++; 
			 }
		} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<title>.:Datos Factura:.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
	<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');
	</script>
<style type="text/css">
<!--
.Estilo1 {color: #FFFFFF}
-->
</style>
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<script language="JavaScript" type="text/javascript">
function verifica_fechas(fecha){ 
  var op=false;
  var fecha_actual = document.getElementById(fecha.id).value;
  if(fecha_actual.value!=""){
	var arreglo_f_desde = fecha_actual.split("/");
	var desde = new Date(arreglo_f_desde[2]+"/"+arreglo_f_desde[1]+"/"+arreglo_f_desde[0]);
	var hoy = new Date("<?php echo(date('Y/m/d')); ?>");
	if(desde.getTime() > hoy.getTime()){
	  alert("La Fecha no Puede ser Mayor a "+ "<?php echo(date('d/m/Y')); ?>");
	  document.getElementById(fecha.id).value="";
	  return;
	}
  }
}

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

var facturas = new Array();
var arreglo = new Array();
comprobar=new Array();

function retorna(tex)
{
var vector = tex.split("||");

if(confirm('Desea agregar la partida: '+vector[1])){
    document.form.txt_cod_pda.value=vector[0];
    document.form.txt_des_pda.value=vector[1];  
  }
}
/////////////////////

function tunMen(tex,enl,dest,subOp,an){
this.tex = tex;
this.enl = enl;
this.dest = dest;
this.subOp = subOp;
this.an = an;
this.secAc = false
}


var i=0
var Op_0 = new tunMen("<?php echo($_SESSION['an_o_presupuesto']); ?>",null, null,0)

<?php
//PARTIDAS PADRES
//$sql="select *  from sai_buscar_art_bien_comp(".$an_o_presupuesto.",".$tipo.",0) as resultado_set(partida_id char,partida_nombre varchar)";

$sql="SELECT * FROM sai_consulta_partidas(".$_SESSION['an_o_presupuesto'].",'4',1) as resultado(a varchar,b int4,c varchar,d bool)";
//echo($sql);
$rs_pdas=pg_exec($conexion,$sql) or die("Error al mostrar");
$filas=pg_num_rows($rs_pdas);
$contador_n = 1;
?>
var totalMen = <? echo $filas+1;?>
<?php
	$contador_p = 0;

	while($rowpr=pg_fetch_array($rs_pdas))
	{ 

 	 $sql_2="SELECT * FROM sai_consulta_partidas(".$_SESSION['an_o_presupuesto'].",'".substr(trim($rowpr['a']),0, 4)."',2) as resultado(a varchar,b int4,c varchar,d bool)";
	 //echo($sql_2);
	 $rs_subpdas=pg_exec($conexion,$sql_2) or die("Error al mostrar");
	 $filas_ace=pg_num_rows($rs_subpdas);
 
 ?>
  
	var Op_<?php echo($contador_n) ?> = new tunMen('<?php echo(trim($rowpr['a']));?>',null,null,<?php echo($filas_ace); ?>)

<?php
  $contador_ace = 0;
  while($rowace=pg_fetch_array($rs_subpdas))
  {
   $estado= 1; //Indica que el articulo esta activo
   $sql_3="SELECT * FROM sai_consulta_partidas(".$_SESSION['an_o_presupuesto'].",'".substr(trim($rowace['a']),0, 7)."',3) as resultado(a varchar,b int4,c varchar,d bool)";
   //echo($sql_3);
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
	 $sql_4="SELECT * FROM sai_consulta_desc_pda('".trim($rowart['a'])."',".$_SESSION['an_o_presupuesto'].") as a"; 
	 //echo($sql_4);
	 $despda=pg_exec($conexion,$sql_4) or die("Error al mostrar");
     if($despda){
	   $rs_despda=pg_fetch_array($despda);
	   //echo($rs_despda['a']);
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

/*function construye()
{   
	var v=window.opener.document.getElementById('txt_cod_imputa').value;
	var c=window.opener.document.getElementById('txt_cod_accion').value;
	if((v=='') && (c==''))
	{
	   alert('Seleccione una categoria de imputaci�n')
	   window.close()
	}

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
}*/

/*function subMes(){
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
}*/

var fondo = true
/*
function construyeSub()
{
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
		   //CODIGO ORIGINAL
			//enla.href = sopchon.enl
			//if(sopchon.dest != null && sopchon.subOp == 0){
				//enla.target = sopchon.dest
				//}
			//ESTO LO CAMBIO ANGELICA	
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
		    //ESTO LO CAMBIO ANGELICA:
		    enla.onclick = function(){if(this.Id != null){javascript:retorna (this.Id);}}
			//CODIGO ORIGINAL:
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
}*/

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

/*function Seccion(){
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
		//
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
onload = construye*/
</script>
<script language="JavaScript" type="text/javascript">

function add_pda(id,tipo)
{   
	if(tipo==0)
	{
	    if(document.form.txt_num_fac.value=="")
	    {
		alert("Debe especificar el n\u00FAmero de la factura");
		return;
	    }
	  
	   if(document.form.txt_fec_fac.value=="")
	   {
	       alert("Debe especificar la fecha de la factura");
	       return;
	   }
	
	   if(document.form.txt_num_cont.value=="")
	   {
	       alert("Debe especificar el n\u00FAmero de control de la factura");
	       return;
	   }


		var codigo_part=document.form.txt_num_fac.value;
		//Verificamos si esta ya registrada
		for(i=tipo;i<facturas.length;i++)
		{
			if (facturas[i][0]==codigo_part)
			{
					alert("Factura ya registrada...");
					document.form.txt_num_fac.value='';
					document.form.txt_fec_fac.value='';
					document.form.txt_num_cont.value='';
					document.form.txt_mont_iva.value='';
					document.form.txt_mont_base.value='';
					return;
			}
		}
		//Verificaci�n despues de confirmada		
		var variable=document.form.hid_arreglo.value;
	        arreglo = variable.split(",");
		var k;
		for(k=0; k<arreglo.length; k++)
		{
			if (trim(arreglo[k])==trim(codigo_part))
			{
					alert("Factura ya se encuentra confirmada...");
					document.form.txt_num_fac.value='';
					document.form.txt_fec_fac.value='';
					document.form.txt_num_cont.value='';
					document.form.txt_mont_iva.value='';
					document.form.txt_mont_base.value='';
					return;
			}
		}
		//////////////////////////////////////////////////
		var registro = new Array(6);
		registro[0] = document.form.txt_num_fac.value;
		registro[1] = document.form.txt_fec_fac.value;
	 	registro[2] = document.form.txt_num_cont.value;
		registro[3] = document.form.opc_por_iva.value;
		registro[4] = document.form.txt_mont_iva.value;
		registro[5] = document.form.txt_mont_base.value;
		facturas[facturas.length]=registro;
	}
	///////////////////////////////
	var tbody = document.getElementById('body');
	var tbody2 = document.getElementById(id);
	
	for(i=0;i<facturas.length-1;i++)
	{
		tbody2.deleteRow(2);
	}
	
	if(tipo!=0)
	{
		tbody2.deleteRow(2);
		for(i=tipo;i<facturas.length;i++)
		{
			facturas[i-1]=facturas[i];
		}
		//QUE HACE ESTO ?
		facturas.pop();
	}
	/////////////////////////////////////	
	var subtotal = 0;
	for(i=0;i<facturas.length;i++)
	{
		var row = document.createElement("tr")
		//PARA SABER SI ES UNA FILA PAR ? 
		if((i%2)==0)
			row.className = "reci2"
		else
			row.className = "reci"
            
			//creamos una columna
		   	var td1 = document.createElement("td");
			td1.setAttribute("align","Center");
			td1.className = 'titularMedio';
			//creamos una variable oculta
			var hid_1 = document.createElement("INPUT");
			hid_1.setAttribute("type","hidden");
			var name="txt_codigo"+i;
	       		hid_1.setAttribute("name",name);
			hid_1.className = "normal";
	       		hid_1.setAttribute("value",facturas[i][0]);
			hid_1.setAttribute("id",name);
	        	hid_1.setAttribute("disabled","true");
			hid_1_text = document.createTextNode(facturas[i][0]);
					
			td1.appendChild(hid_1);			
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
			imp_2.setAttribute("value",facturas[i][1]);
			imp_2.setAttribute("id",name);
			imp_2.setAttribute("size","30");
			hid_2_text = document.createTextNode(facturas[i][1]);
						
		   	td2.appendChild (imp_2);		
		    	td2.appendChild(hid_2_text);	
					
			//Creamos la tercera columna
			var td3 = document.createElement("td");
			td3.setAttribute("align","Center");
			td3.className = 'titularMedio';
			//Creamos la segunda variable oculta
			var imp_3 = document.createElement("INPUT");
			imp_3.setAttribute("type","hidden");
	        	name="txt_cont"+i;
	        	imp_3.setAttribute("name",name);
			imp_3.className = "normal";
			imp_3.setAttribute("value",facturas[i][2]);
			imp_3.setAttribute("id",name);
			imp_3.setAttribute("size","30");
			hid_3_text = document.createTextNode(facturas[i][2]);
						
		   	td3.appendChild (imp_3);		
		    	td3.appendChild(hid_3_text);	

			//Creamos la cuarta columna
			var td7 = document.createElement("td");
			td7.setAttribute("align","Center");
			td7.className = 'titularMedio';
			//Creamos la segunda variable oculta
			var imp_7 = document.createElement("INPUT");
			imp_7.setAttribute("type","hidden");
	        	name="txt_porc"+i;
	        	imp_7.setAttribute("name",name);
			imp_7.className = "normal";
			imp_7.setAttribute("value",facturas[i][5]);
			imp_7.setAttribute("id",name);
			imp_7.setAttribute("size","30");
			hid_7_text = document.createTextNode(facturas[i][5]);
						
		   	td7.appendChild (imp_7);		
		    	td7.appendChild(hid_7_text);	



			//Creamos la cuarta columna
			var td4 = document.createElement("td");
			td4.setAttribute("align","Center");
			td4.className = 'titularMedio';
			//Creamos la segunda variable oculta
			var imp_4 = document.createElement("INPUT");
			imp_4.setAttribute("type","hidden");
	        	name="txt_porc"+i;
	        	imp_4.setAttribute("name",name);
			imp_4.className = "normal";
			imp_4.setAttribute("value",facturas[i][3]);
			imp_4.setAttribute("id",name);
			imp_4.setAttribute("size","30");
			hid_4_text = document.createTextNode(facturas[i][3]);
						
		   	td4.appendChild (imp_4);		
		    	td4.appendChild(hid_4_text);	


			//Creamos la quinta columna
			var td5 = document.createElement("td");
			td5.setAttribute("align","Center");
			td5.className = 'titularMedio';
			//Creamos la segunda variable oculta
			var imp_5 = document.createElement("INPUT");
			imp_5.setAttribute("type","hidden");
	        	name="txt_porc"+i;
	        	imp_5.setAttribute("name",name);
			imp_5.className = "normal";
			imp_5.setAttribute("value",facturas[i][4]);
			imp_5.setAttribute("id",name);
			imp_5.setAttribute("size","30");
			hid_5_text = document.createTextNode(facturas[i][4]);
						
		   	td5.appendChild (imp_5);		
		    	td5.appendChild(hid_5_text);	

			//Se crea la sexta columna
			var td6 = document.createElement("td");				
			td6.setAttribute("align","Center");
			
			td6.className = 'link';
			editLink = document.createElement("a");
			linkText = document.createTextNode("Eliminar");
			editLink.setAttribute("href", "javascript:add_pda('"+id+"','"+(i+1)+"')");
			editLink.appendChild(linkText);
			td6.appendChild (editLink);
		
			row.appendChild(td1);
			row.appendChild(td2);
			row.appendChild(td3);
			row.appendChild(td7);
			row.appendChild(td4);
			row.appendChild(td5);
			row.appendChild(td6);
			tbody.appendChild(row);
	}	
	//document.form1.cmb_origen.value = "";
	document.form.txt_num_fac.value='';
	document.form.txt_fec_fac.value='';
	document.form.txt_num_cont.value='';
	document.form.txt_mont_iva.value='';
	document.form.txt_mont_base.value='';
	document.form.largo.value = facturas.length;
}

function enviar()
{   
	var nave;
	var j;
	nave=new String(navigator.appName);
	var pos_nave=nave.indexOf("Explorer");
	
	/*if(facturas.length==0)
	{
	   alert("No se ha agregado ninguna factura !!");
	   return
	}*/
	
	 var largo_facturas=window.opener.document.getElementById('hid_largo').value;
	
	 var op_tbody = window.opener.document.getElementById('ar_fact');
	 var tbody2 = window.opener.document.getElementById('tbl_fact');
	 
	 var element_otros=0;
	 
	 element_otros = window.opener.document.getElementById('tbl_fact').getElementsByTagName('tr').length;
	 element_otros = element_otros -1;
     
    	j=element_otros; 
	 

	//agrega los elementos
	for(i=0;i<facturas.length;i++)
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
			//hid_1.className = "normal";
	        hid_1.setAttribute("value",facturas[i][0]);
			hid_1.setAttribute("id",name);
	       	hid_1.setAttribute("disabled","true");
			hid_1.setAttribute("colspan","2");
			hid_1_text = window.opener.document.createTextNode(facturas[i][0]);
								
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
			//imp_2.className = "normal";
			imp_2.setAttribute("value",facturas[i][1]);
			imp_2.setAttribute("id",name);
			imp_2.setAttribute("size","30");
			hid_2_text = window.opener.document.createTextNode(facturas[i][1]);
						
			td2.appendChild (imp_2);		
		        td2.appendChild(hid_2_text);	
		
			//Creamos la tercera columna
			var td3 = window.opener.document.createElement("td");
			td3.setAttribute("align","Center");
			td3.className = 'normalNegro';
			//Creamos la segunda variable oculta
			var imp_3 = window.opener.document.createElement("INPUT");
			imp_3.setAttribute("type","hidden");
	      		name="txt_den"+j;
	    		imp_3.setAttribute("name",name);
			//imp_3.className = "normal";
			imp_3.setAttribute("value",facturas[i][2]);
			imp_3.setAttribute("id",name);
			imp_3.setAttribute("size","30");
			hid_3_text = window.opener.document.createTextNode(facturas[i][2]);
						
			td3.appendChild (imp_3);		
		        td3.appendChild(hid_3_text);	

			//Creamos la cuarta columna
			var td4 = window.opener.document.createElement("td");
			td4.setAttribute("align","Center");
			td4.className = 'normalNegro';
			//Creamos la segunda variable oculta
			var imp_4 = window.opener.document.createElement("INPUT");
			imp_4.setAttribute("type","hidden");
	      		name="txt_den"+j;
	    		imp_4.setAttribute("name",name);
			//imp_4.className = "normal";
			imp_4.setAttribute("value",facturas[i][5]);
			imp_4.setAttribute("id",name);
			imp_4.setAttribute("size","30");
			hid_4_text = window.opener.document.createTextNode(facturas[i][5]);
						
			td4.appendChild (imp_4);		
		        td4.appendChild(hid_4_text);

			//Creamos la quinta columna
			var td5 = window.opener.document.createElement("td");
			td5.setAttribute("align","Center");
			td5.className = 'normalNegro';
			//Creamos la segunda variable oculta
			var imp_5 = window.opener.document.createElement("INPUT");
			imp_5.setAttribute("type","hidden");
	      	name="txt_den"+j;
	    	imp_5.setAttribute("name",name);
			//imp_5.className = "normal";
			imp_5.setAttribute("value",facturas[i][3]);
			imp_5.setAttribute("id",name);
			imp_5.setAttribute("size","30");
			hid_5_text = window.opener.document.createTextNode(facturas[i][3]);
						
			td5.appendChild (imp_5);		
		    td5.appendChild(hid_5_text);

			//Creamos la sexta columna
			var td6 = window.opener.document.createElement("td");
			td6.setAttribute("align","Center");
			td6.className = 'normalNegro';
			//Creamos la segunda variable oculta
			var imp_6 = window.opener.document.createElement("INPUT");
			imp_6.setAttribute("type","hidden");
	      	name="txt_monto_base"+j;
	    	imp_6.setAttribute("name",name);
			//imp_6.className = "normal";
			imp_6.setAttribute("value",facturas[i][4]);
			imp_6.setAttribute("id",name);
			imp_6.setAttribute("size","30");
			hid_6_text = window.opener.document.createTextNode(facturas[i][4]);
						
			td6.appendChild (imp_6);		
		    td6.appendChild(hid_6_text);
			
 			 //OPCION DE ELIMINAR
			  var td8 = document.createElement("td");				
			  td8.setAttribute("align","Center");
			  td8.className = 'normal';
			  editLink = document.createElement("a");
			  linkText = document.createTextNode("Eliminar");
			 // editLink.setAttribute("href", "javascript:elimina_pda('"+(j+1)+"')");
 			  editLink.setAttribute("href", "javascript:elimina_fac('"+(j)+"')");
			  editLink.appendChild(linkText);
			  td8.appendChild (editLink);

			row.appendChild(td1);
			row.appendChild(td2);
			row.appendChild(td3);
			row.appendChild(td4);
			row.appendChild(td5);
			row.appendChild(td6);
			row.appendChild(td8);
			op_tbody.appendChild(row);
			
			j=j+1;
	}	
	window.opener.document.getElementById('hid_val').value='true';
	//window.opener.document.getElementById('hid_comprobar').value=partidas;
    window.close();
}


function redondear_dos_decimal(valor) {
   float_redondeado=Math.round(valor * 100) / 100;
   return float_redondeado;
} 

function calcular_iva()
{ 
 var monto_base=parseFloat(MoneyToNumber(document.form.txt_mont_base.value));
 var porce=parseFloat(MoneyToNumber(document.form.opc_por_iva.value));

 var IVA=redondear_dos_decimal((monto_base*porce)/100);
   
     document.form.txt_mont_iva.value=IVA;
	
   return
}




//-->
</script>
<style type="text/css">
<!--
a.enls:link, a.enls:visited{
color: #3366CC;
text-decoration: none;
}
a.enls:hover{
color: #CC0000;
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
.subMe{
	display: none;
	margin: 0;
	background-image: url(imasmenu/puntosvt.gif);
	background-repeat:  repeat-y;
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
<div id="tunMe" >
</div> 
<table width="200" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<form name="form" method="post" action="">
 <div align="center">
   <table  width="100%" border="0" class="tablaCentral" id="tbl_pda">
      <!--DWLayoutTable-->
    <tr class="td_gray"> 
     <td class="normalNegroNegrita" align="center">N&#176; Factura </td>
     <td width="155" class="normalNegroNegrita" align="center">Fecha</td>
	 <td class="normalNegroNegrita" align="center">N&#176; Control </td>
	 <td class="normalNegroNegrita" align="center">Monto&nbsp;Base:</td>
 	 <td class="normalNegroNegrita" align="center">% IVA:</td>
	 <td class="normalNegroNegrita" align="center">Monto IVA:</td>
	 <td width="35" class="normalNegroNegrita" align="center">Opciones</td>
    </tr>
    <tr> 
     <td class="normal" valign="middle" ><input name="txt_num_fac" type="text" class="normal" id="txt_num_fac" size="10" maxlength="10"/></td>
	 <td><input type="text" size="10" id="txt_fec_fac"	name="txt_fec_fac" class="dateparse" readonly />
	   <a href="javascript:void(0);" onclick="g_Calendar.show(event,'txt_fec_fac');" title="Show popup calendar">
	   <img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a></td>
  	<td class="normal" valign="middle" > <input name="txt_num_cont" type="text" class="normal" id="txt_num_cont" size="10" maxlength="10"/></td>
	<td class="Normal" align="center">
	<input name="txt_mont_base" type="text" class="normal" id="txt_mont_base" onkeyup="FormatCurrency(this)" size="10"/></div></td>
	<td align="center">
	  <select name="opc_por_iva" id="opc_por_iva" class ="normal" onChange="javacript:calcular_iva()">
    <?php
		/*onChange="javacript:calcular_iva()"*/
			  	for ($ii=0; $ii <$elem_impuesto; $ii++)
				{
				?>
                <option value="<?php echo $porce_impuesto[$ii];?>"  <?php if  ($porce_impuesto[$ii]==0)  {echo "selected";}?> title="<?php echo $porce_impuesto[$ii]."% ". $impu_nombre[$ii];?>"><?php echo $porce_impuesto[$ii];?></option>
                <?PHP
				
				}
				?>
              </select></td>
		<td class="Normal" align="center"><input name="txt_mont_iva"  readonly="true" type="text" class="normal" id="txt_mont_iva" size="12"/></div></td>
	    <td class="Normal" align="center"><a href="javascript: add_pda('tbl_pda','0')" class="link" >Agregar</a></div></td>
      </tr>
	  <tbody id="body"  class="normal">
	  </tbody>
      <tr> 
        
        <td colspan="6">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td> <div align="right"><input type="button" value="Confirmar" onclick="javascript:enviar()"></div></td>
            <td><input type="button" value="Cerrar" onclick="javascript:window.close()"></td>
          </tr>
        </table>
		</td>
        <td width="132" height="50%" align="center" class="normal"><!--DWLayoutEmptyCell-->&nbsp; </td>
      </tr>
    </table>
  </div>
  <p>
    <input type="hidden" name="largo" id="largo" />
	<input type="hidden" name="hid_arreglo" value="<?php echo $arreglo;?>" />
  </p>
</form>
	
</body>
</html>

