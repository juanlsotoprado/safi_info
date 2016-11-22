<?php 
ob_start();
session_start();
require_once("includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI:Modificaciones presupuestarias</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="js/funciones.js"> </script>
<link type="text/css" href="js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">	g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script language="JavaScript" type="text/JavaScript">
<!--
//para los apendchild
partidas = new Array();
todas_pdas = new Array();

function validar_pri(elem){
  for(i=0;i<elem;i++)  {
	if((document.getElementsByName('chk_cedente'+i)[1].checked==false) && (document.getElementsByName('chk_cedente'+i)[0].checked==false)) 	{
	 alert('Seleccione el tipo de operaci\u00f3n a realizar.');
	 return false;
	}
	else
	if(document.getElementById('txt_monto'+i).value=='') {
	  alert('Revise los montos ingresados.');
	  return false;
	 }	
  }	 
}

function add_opciones() {   
        nave=new String(navigator.appName);
	    var pos_nave=nave.indexOf("Explorer");
        var index;
		   
		if(document.form.txt_cod_imputa.value==""){	
		    alert(" Seleccione la categor\u00eda program\u00e1tica");
			return
		}
		else{
           index=document.form.opt_depe.value;
		} 
				
		element_otros = document.getElementById('tbl_part').getElementsByTagName('tr').length;
	    element_otros = element_otros -1;
		
	    var tbody2 = document.getElementById('tbl_part');
									
		//se agregan ahora los elementos a la tabla inferior
		var tabla = document.getElementById('tbl_mod');
		element_todos = document.getElementById('tbl_mod').getElementsByTagName('tr').length;
		element_todos = element_todos-3;
		
	    var tbody = document.getElementById('item');
		var id='item';
		
		var valido=validar_pri(element_otros);
      
	    if(valido==false){
		  return;
		}
		
		var j=0;
		
		for(i=0;i<element_otros;i++) {
		   
		    j=eval(element_todos)+eval(i);
		
		    var registro = new Array(8);  	        

			registro[4]=document.getElementById('txt_codigo'+i).value;
		    registro[5]=document.getElementById('txt_den'+i).value;
		
		    var row = document.createElement("tr")
		  
			  //LOS RADIO BUTTONS
			  var td1 = document.createElement("td");
			  td1.setAttribute("align","Center");
			  td1.className = 'normalNegroNegrita';
			  //creamos una radio button
			  
			 var name="rb_ac_proy"+j;
			 
			  if(pos_nave>0){
			    var rad_1 = document.createElement('<input type="radio" id="'+name+'" name="'+name+'">'); }
			  else{ 
			    var rad_1 = document.createElement('INPUT');
			    rad_1.type="radio";
				rad_1.id=name;
	            rad_1.name=name; }
			 
			 //rad_1.setAttribute("id",name);
			 //alert(name);
	         rad_1.setAttribute("disabled","true");
		  
		  if(document.form.chk_tp_imputa[0].checked==true){
		    registro[0]=1;
		    rad_1.setAttribute("value",1);
			rad_1_text = document.createTextNode('PR');
			rad_1.defaultChecked = true
		  }
		  else{
		    registro[0]=0;		    
		    rad_1.setAttribute("value",0);
			rad_1_text = document.createTextNode('AC');
			rad_1.defaultChecked = true
		  }
			
		 
		  
		  td1.appendChild(rad_1);			
		  td1.appendChild(rad_1_text);
		  
		  
		 //CODIGO DEL PROYECTO O ACCION
		  var td2 = document.createElement("td");
		  td2.setAttribute("align","Center");
		  td2.className = 'normalNegroNegrita';
		  //creamos una radio button
		  var txt_id_p_ac = document.createElement("INPUT");
		  txt_id_p_ac.setAttribute("type","text");
		  name="txt_id_p_ac"+j;
	      txt_id_p_ac.setAttribute("name",name);
		  txt_id_p_ac.readOnly=true; 
		  registro[1]=document.form.txt_cod_imputa.value;
		  txt_id_p_ac.value=registro[1];
		  
		  	 
		  txt_id_p_ac.size='15'; 
		  txt_id_p_ac.className='normal';
		  td2.appendChild(txt_id_p_ac);
		  
		  //CODIGO DE LA ACCION ESPECIFICA
		  var td3 = document.createElement("td");
		  td3.setAttribute("align","Center");
		  td3.className = 'normalNegroNegrita';
		  //creamos una radio button
		  var txt_id_acesp = document.createElement("INPUT");
		  txt_id_acesp.setAttribute("type","text");
		  name="txt_id_acesp"+j;
	      txt_id_acesp.setAttribute("name",name);
		  txt_id_acesp.setAttribute("readOnly","true"); 
		  registro[2]=document.form.txt_cod_accion.value;
		  txt_id_acesp.value=registro[2];	 
		  txt_id_acesp.size='8'; 
		  txt_id_acesp.className='normal';
		  td3.appendChild(txt_id_acesp);
		  
		  //CODIGO DE LA DEPENDENCIA
		  var td4 = document.createElement("td");
		  td4.setAttribute("align","Center");
		  td4.className = 'normalNegroNegrita';
		  //creamos una radio button
		  var txt_id_depe = document.createElement("INPUT");
		  txt_id_depe.setAttribute("type","text");
		  name="txt_id_depe"+j;
	      txt_id_depe.setAttribute("name",name);
		  txt_id_depe.setAttribute("readOnly","true");
		  registro[3]=document.form.opt_depe.value
		  txt_id_depe.value=registro[3];	 
		  txt_id_depe.size='8'; 
		  txt_id_depe.className='normal';
		  td4.appendChild(txt_id_depe);
		  	    
		  //CODIGO DE LA PARTIDA
		  var td5 = document.createElement("td");
		  td5.setAttribute("align","Center");
		  td5.className = 'normalNegroNegrita';
		  //creamos una radio button
		  var txt_id_pda = document.createElement("INPUT");
		  txt_id_pda.setAttribute("type","text");
		  txt_id_pda.setAttribute("readOnly","true");
		  name="txt_id_pda"+j;
	      txt_id_pda.setAttribute("name",name);
		  txt_id_pda.value=registro[4];	 
		  txt_id_pda.size='15'; 
		  txt_id_pda.className='normal';
		  td5.appendChild(txt_id_pda);
		  
		  //DENOMINACION
		  var td6 = document.createElement("td");
		  td6.setAttribute("align","Center");
		  td6.className = 'normalNegroNegrita';
		  //creamos una radio button
		  var txt_den_pda = document.createElement("INPUT");
		  txt_den_pda.setAttribute("type","text");
		  name="txt_den_pda"+j;
		  txt_den_pda.setAttribute("readOnly","true"); 
	      txt_den_pda.setAttribute("name",name);
		  txt_den_pda.value=registro[5];	 
		  txt_den_pda.size='20'; 
		  txt_den_pda.className='normal';
		  td6.appendChild(txt_den_pda);
		  
		  //TIPO
		  var td7 = document.createElement("td");
		  td7.setAttribute("align","Center");
		  td7.className = 'normalNegroNegrita';
		  //creamos un radio button
		  var name="rb_ced"+j;
			  if(pos_nave>0){
			    var rad_2 = document.createElement('<input type="radio" id="'+name+'" name="'+name+'">'); }
			  else{ 
			    var rad_2 = document.createElement('INPUT');
			    rad_2.type="radio";
				rad_2.id=name;
	            rad_2.name=name; }
			 
			 //rad_2.setAttribute("id",name);

		  		  
		   if(document.getElementsByName('chk_cedente'+i)[1].checked==true){
		     registro[6]=1;
		     rad_2.setAttribute("value",1);
			 rad_2_text = document.createTextNode('R');
			 rad_2.defaultChecked = true
		   }
		   else{
		     registro[6]=0;		    
		     rad_2.setAttribute("value",0);
   		   	 rad_2_text = document.createTextNode('C');
			 rad_2.defaultChecked = true
		   }
			
		   rad_2.setAttribute("id",name);
	       rad_2.setAttribute("disabled","true");
		   	   	  
		   td7.appendChild(rad_2);			
		   td7.appendChild(rad_2_text);
		  	
		  //MONTO
		  var td8 = document.createElement("td");
		  td8.setAttribute("align","Center");
		  td8.className = 'normalNegroNegrita';
		  //creamos una radio button
		  var txt_monto = document.createElement("INPUT");
		  txt_monto.setAttribute("type","text");
		  name="txt_monto_pda"+j;
	      txt_monto.setAttribute("name",name);
		  txt_monto.setAttribute("readOnly","true");
		  registro[7]=document.getElementById('txt_monto'+i).value;
		  txt_monto.value=registro[7];	 
		  txt_monto.size='10'; 
		  txt_monto.className='normal';
		  td8.appendChild(txt_monto);	
		  		  		  			
		  //OPCION DE ELIMINAR
		  var td9 = document.createElement("td");				
		  td9.setAttribute("align","Center");
		  td9.className = 'normal';
		  editLink = document.createElement("a");
		  linkText = document.createTextNode("Eliminar");
		  editLink.setAttribute("href", "javascript:elimina_pda('"+(j+1)+"')");
		  editLink.appendChild(linkText);
		  td9.appendChild (editLink);
				  	  
		  row.appendChild(td1); 
		  row.appendChild(td2);
		  row.appendChild(td3); 
		  row.appendChild(td4);
		  row.appendChild(td5);
		  row.appendChild(td6);
		  row.appendChild(td7);
		  row.appendChild(td8);
		  row.appendChild(td9);
	      tbody.appendChild(row); 	
		  
		  partidas[partidas.length]=registro;
        }
		
		element_mod = document.getElementById('tbl_mod').getElementsByTagName('tr').length;
	    element_mod = element_mod -3;		
		
		document.getElementById('hid_largo').value=element_mod;
		
		for(i=0;i<element_otros;i++){	 
		  tbody2.deleteRow(1);
	    }
					
				
}


function elimina_pda(tipo) {   
    nave=new String(navigator.appName);
	var pos_nave=nave.indexOf("Explorer");
	
    var tabla = document.getElementById('tbl_mod');
    var tbody = document.getElementById('item');
		
	for(i=0;i<partidas.length;i++) {
		tabla.deleteRow(1);
		
	}
   
    for(i=tipo;i<partidas.length;i++) {
		partidas[i-1]=partidas[i];
	}
	partidas.pop();
	
		
	nave=new String(navigator.appName);
	var pos_nave=nave.indexOf("Explorer");
    
	document.getElementById('hid_largo').value=partidas.length;
	   
    //agrega los elementos
	for(i=0;i<partidas.length;i++) {

		      var row = document.createElement("tr")
		  
			  //LOS RADIO BUTTONS
			  var td1 = document.createElement("td");
			  td1.setAttribute("align","Center");
			  td1.className = 'normalNegroNegrita';
			  //creamos una radio button
			  var name="rb_ac_proy"+i;
			  if(pos_nave>0){
			    var rad_1 = document.createElement('<input type="radio" id="'+name+'" name="'+name+'">'); }
			  else{ 
			    var rad_1 = document.createElement('INPUT');
			    rad_1.type="radio";
				rad_1.id=name;
	            rad_1.name=name; }

		  
		    if(partidas[i][0]==1){
		       rad_1.setAttribute("value",1);
			   rad_1_text = document.createTextNode('PR');
			   rad_1.defaultChecked = true
		    }
		    else{		    
		       rad_1.setAttribute("value",0);
			   rad_1_text = document.createTextNode('AC');
			   rad_1.defaultChecked = true
		    }
		  	
		    
	        rad_1.setAttribute("disabled","true");
		  
		    td1.appendChild(rad_1);			
		    td1.appendChild(rad_1_text);
		  
		  
		 //CODIGO DEL PROYECTO O ACCION
		  var td2 = document.createElement("td");
		  td2.setAttribute("align","Center");
		  td2.className = 'normalNegroNegrita';
		  //creamos una radio button
		  var txt_id_p_ac = document.createElement("INPUT");
		  txt_id_p_ac.setAttribute("type","text");
		  name="txt_id_p_ac"+i;
	      txt_id_p_ac.setAttribute("name",name);
		  txt_id_p_ac.setAttribute("readonly","true"); 
		  txt_id_p_ac.value=partidas[i][1];	 
		  txt_id_p_ac.size='15'; 
		  txt_id_p_ac.className='normal';
		  td2.appendChild(txt_id_p_ac);
		  
		  //CODIGO DE LA ACCION ESPECIFICA
		  var td3 = document.createElement("td");
		  td3.setAttribute("align","Center");
		  td3.className = 'normalNegroNegrita';
		  //creamos una radio button
		  var txt_id_acesp = document.createElement("INPUT");
		  txt_id_acesp.setAttribute("type","text");
		  name="txt_id_acesp"+i;
	      txt_id_acesp.setAttribute("name",name); 
		  txt_id_acesp.setAttribute("readonly","true"); 
		  txt_id_acesp.value=partidas[i][2];	 
		  txt_id_acesp.size='8'; 
		  txt_id_acesp.className='normal';
		  td3.appendChild(txt_id_acesp);
		  
		  //CODIGO DE LA DEPENDENCIA
		  var td4 = document.createElement("td");
		  td4.setAttribute("align","Center");
		  td4.className = 'normalNegroNegrita';
		  //creamos una radio button
		  var txt_id_depe = document.createElement("INPUT");
		  txt_id_depe.setAttribute("type","text");
		  txt_id_depe.setAttribute("readonly","true");
		  name="txt_id_depe"+i;
	      txt_id_depe.setAttribute("name",name); 
		  txt_id_depe.value=partidas[i][3];	 
		  txt_id_depe.size='8'; 
		  txt_id_depe.className='normal';
		  td4.appendChild(txt_id_depe);
		  	    
		  //CODIGO DE LA PARTIDA
		  var td5 = document.createElement("td");
		  td5.setAttribute("align","Center");
		  td5.className = 'normalNegroNegrita';
		  //creamos una radio button
		  var txt_id_pda = document.createElement("INPUT");
		  txt_id_pda.setAttribute("type","text");
		  txt_id_pda.setAttribute("readonly","true");
		  name="txt_id_pda"+i;
	      txt_id_pda.setAttribute("name",name);
		  txt_id_pda.value=partidas[i][4];	 
		  txt_id_pda.size='15'; 
		  txt_id_pda.className='normal';
		  td5.appendChild(txt_id_pda);
		  
		  //DENOMINACION
		  var td6 = document.createElement("td");
		  td6.setAttribute("align","Center");
		  td6.className = 'normalNegroNegrita';
		  //creamos una radio button
		  var txt_den_pda = document.createElement("INPUT");
		  txt_den_pda.setAttribute("type","text");
		  txt_den_pda.setAttribute("readonly","true");
		  name="txt_den_pda"+i;
	      txt_den_pda.setAttribute("name",name);
		  txt_den_pda.value=partidas[i][5];	 
		  txt_den_pda.size='20'; 
		  txt_den_pda.className='normal';
		  td6.appendChild(txt_den_pda);
		  
		  //TIPO
		  var td7 = document.createElement("td");
		  td7.setAttribute("align","Center");
		  td7.className = 'normalNegroNegrita';
		  //creamos una radio button
		  var name="rb_ced"+i;
		  if(pos_nave>0){
			    var rad_2 = document.createElement('<input type="radio" id="'+name+'" name="'+name+'">'); }
			  else{ 
			    var rad_2 = document.createElement('INPUT');
			    rad_2.type="radio";
				rad_2.id=name;
	            rad_2.name=name; }
	  
		  		  
		   if(partidas[i][6]==1){
		     rad_2.value=1;
			 rad_2_text = document.createTextNode('R');
			 rad_2.defaultChecked = true
		   }
		   else{		    
		     rad_2.value=0;
   		   	 rad_2_text = document.createTextNode('C');
			 rad_2.defaultChecked = true
		   }
			
		   //rad_2.setAttribute("id",name);
	       rad_2.setAttribute("disabled","true");
		   	   	  
		   td7.appendChild(rad_2);			
		   td7.appendChild(rad_2_text);
		  	
		  //MONTO
		  var td8 = document.createElement("td");
		  td8.setAttribute("align","Center");
		  td8.className = 'normalNegroNegrita';
		  //creamos una radio button
		  var txt_monto = document.createElement("INPUT");
		  txt_monto.setAttribute("type","text");
		  name="txt_monto_pda"+i;
	      txt_monto.setAttribute("name",name);
		  txt_monto.setAttribute("readonly","true");
		  txt_monto.value=partidas[i][7];	 
		  txt_monto.size='10'; 
		  txt_monto.className='normal';
		  td8.appendChild(txt_monto);	
			
		  //OPCION DE ELIMINAR
		  var td9 = document.createElement("td");				
		  td9.setAttribute("align","Center");
		  td9.className = 'normal';
		  editLink = document.createElement("a");
		  linkText = document.createTextNode("Eliminar");
		  editLink.setAttribute("href", "javascript:elimina_pda('"+(i+1)+"')");
		  editLink.appendChild(linkText);
		  td9.appendChild (editLink);

					  	  
		  row.appendChild(td1); 
		  row.appendChild(td2);
		  row.appendChild(td3); 
		  row.appendChild(td4);
		  row.appendChild(td5);
		  row.appendChild(td6);
		  row.appendChild(td7);
		  row.appendChild(td8);
		  row.appendChild(td9);
	      tbody.appendChild(row); 	
        }
}
//-->
</script>
<script LANGUAGE="JavaScript"><!--

function revisar_doc(id_tipo_documento,id_opcion,objeto_siguiente_id,objeto_siguiente_id_proy,cadena_siguiente_id,cadena_siguiente_id_proy,id_objeto_actual) { 
	document.form.action="accion_ejecutar.php?tipo="+id_tipo_documento+"&accion="+id_objeto_actual+"&accion_sig=" + objeto_siguiente_id + "&hijo="+cadena_siguiente_id+"&opcion="+id_opcion;
	 validar(document.form);
}

function enviar(){
  validar(document.form);
}

function confirmar_cancelacion(tipo_doc){

	document.form.action="documentos.php?tipo="+tipo_doc;
	if(confirm("Est\u00e1 seguro que desea cancelar la carga de partidas?")){
	  document.form.submit();
	}
}



function formatCurrency(num) {
num = num.toString().replace(/\$|\,/g,'');
if(isNaN(num))
num = "0";
sign = (num == (num = Math.abs(num)));
num = Math.floor(num*100+0.50000000001);
cents = num%100;
num = Math.floor(num/100).toString();
if(cents<10)
cents = "0" + cents;
for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
num = num.substring(0,num.length-(4*i+3))+','+
num.substring(num.length-(4*i+3));
return (((sign)?'':'-') + '$' + num + '.' + cents);
}

function validar(x) {
    var aux=true;
    var msg="";
	

	if(document.getElementsByName('rb_tp')[0].checked==false && document.getElementsByName('rb_tp')[1].checked==false && document.getElementsByName('rb_tp')[2].checked==false ){  
	      aux=false;
	      msg='Seleccione el tipo de modificaci\u00f3n';
	}	
    
    if(document.form.tipoModificacion.value=='')
	 {
	  alert('Seleccione si es una Modificaci\u00f3n o Ajuste');
	  return false;
	 }	

    if(document.form.hid_hasta_itin.value=='')
	 {
	  alert('Seleccione la fecha en que se afectar\u00E1 Modificaci\u00f3n o Ajuste');
	  return false;
	 }	
    
	
	
	var cant_pdas;
	
	cant_pdas = document.getElementById('tbl_mod').getElementsByTagName('tr').length;
	cant_pdas = cant_pdas -3;		
	
	if(cant_pdas<=0){
	      
	      aux=false;
	      msg='Este documento no posee partidas asociadas';
	}
	
	var cont_ced=0;
	var cont_rec=0;
	var mto_ced=0;
	var mto_rec=0;
	
	   if(document.getElementsByName('rb_tp')[1].checked==true){
	    
	         var ttCed=0;
  			var ttRed=0;	
  			var mtoCed=0;
  			var mtoRed=0;
  			var mtoCedt=0;
  			var mtoRedt=0;
  			var valorAct=""
  	  			var partIguales=0;
		    for (i=0;i<partidas.length; i++){
		    	
			if(i>0){
				
				if(document.getElementsByName('txt_id_pda'+i)[0].value.substring(0,4)!=valorAct ){
					partIguales=partIguales+1;
				}
			}	
		  	if (document.getElementsByName('rb_ced'+i)[0].value==0){
		  	   valmto=document.getElementsByName('txt_monto_pda'+i)[0].value.replace(/\$|\,/g,'');
		  	    mtoCedt=valmto*1;
		  	    mtoCed=mtoCed+mtoCedt;
	 			ttCed=ttCed+1;
	 		}
	 		if (document.getElementsByName('rb_ced'+i)[0].value==1){
	 		    valmto=document.getElementsByName('txt_monto_pda'+i)[0].value.replace(/\$|\,/g,'');
	 		    mtoRedt=valmto*1;
	 		    mtoRed=mtoRed+mtoRedt;
	 			ttRed=ttRed+1;
	 		}
           valorAct=document.getElementsByName('txt_id_pda'+i)[0].value.substring(0,4) ;	 		


	 		
	 	}

		 	if(partIguales>0){
			 	aux=false;
			 	msg='Las partidas entre las que se est\u00e1 realizando el traspaso deben tener la misma ra\u00edz';
		 	}
	 	
	 	if (ttCed==0 || ttRed==0){
	 		aux=false;
		   msg='Debe existir al menos una partida cedente y una receptora.';
	 	}
else{
	 	var mtoCedround=Math.round(mtoCed*100)/100;
	 	var mtoRedround=Math.round(mtoRed*100)/100;

var cantidad = parseFloat(mtoCed);
var decimales = parseFloat(2);
decimales = (!decimales ? 2 : decimales);
mtoCedround= Math.round(cantidad * Math.pow(10, decimales)) / Math.pow(10, decimales);

cantidad = parseFloat(mtoRed);
decimales = parseFloat(2);
decimales = (!decimales ? 2 : decimales);
mtoRedround= Math.round(cantidad * Math.pow(10, decimales)) / Math.pow(10, decimales);
	 	
	 		if(mtoCedround!=mtoRedround){
	 		   aux=false;
		   		msg='Debe existir un equilibrio entre los montos de las partidas cedentes y las receptoras'+ mtoCedround+'  '+mtoRedround;
	 		}	 		
	 	}




	 	
	 	 
	 }else{
	 	if(document.getElementsByName('rb_tp')[0].checked==true){
	 		
	 		 var ttCed=0;
  			var ttRed=0;	
  			var mtoCed=0;
  			var mtoRed=0;
  			var mtoCedt=0;
  			var mtoRedt=0;
		    for (i=0;i<partidas.length; i++){
		  	if (document.getElementsByName('rb_ced'+i)[0].value==0){
		  	   valmto=document.getElementsByName('txt_monto_pda'+i)[0].value.replace(',','');
		  	    mtoCedt=valmto*1;
		  	    mtoCed=mtoCed+mtoCedt;
	 			ttCed=ttCed+1;
	 		}
	 		if (document.getElementsByName('rb_ced'+i)[0].value==1){
	 		    valmto=document.getElementsByName('txt_monto_pda'+i)[0].value.replace(',','');
	 		    mtoRedt=valmto*1;
	 		    mtoRed=mtoRed+mtoRedt;
	 			ttRed=ttRed+1;
	 		}
	 		}
	 		if(ttRed==0 || ttCed>0){
	 			aux=false;
	 			msg='Si es cr\u00e9dito debe recibir dinero';
	 		}
	 	}else{
	 	 var ttCed=0;
  			var ttRed=0;	
  			var mtoCed=0;
  			var mtoRed=0;
  			var mtoCedt=0;
  			var mtoRedt=0;
		    for (i=0;i<partidas.length; i++){
		  	if (document.getElementsByName('rb_ced'+i)[0].value==0){
		  	   valmto=document.getElementsByName('txt_monto_pda'+i)[0].value.replace(',','');
		  	    mtoCedt=valmto*1;
		  	    mtoCed=mtoCed+mtoCedt;
	 			ttCed=ttCed+1;
	 		}
	 		if (document.getElementsByName('rb_ced'+i)[0].value==1){
	 		    valmto=document.getElementsByName('txt_monto_pda'+i)[0].value.replace(',','');
	 		    mtoRedt=valmto*1;
	 		    mtoRed=mtoRed+mtoRedt;
	 			ttRed=ttRed+1;
	 		}
	 		}
	 		
	 		if (ttCed==0 || ttRed!=0){
	 			aux=false;
	 			msg='Si es disminuci\u00f3n debe ceder, no recibir';
	 		}
	 	  }
	 }
		  
		
	if (aux==true){ 
	 for (i=0;i<partidas.length; i++)
  	 { 
	 	
                //alert(document.getElementById('hid_largo').value);
				var r1=document.getElementById('rb_ac_proy'+i);
				var r2=document.getElementById('rb_ced'+i);
				
																						
				r1.disabled=false;
				r2.disabled=false;
	 } 
	 
	var texto=crear();
	document.form.txt_arreglo_f.value=texto;
	 
	 x.submit(); 
	}
	else{
	alert(msg);
    }
}


function comparar_fechas(fecha_inicial,fecha_final) //Formato dd/mm/yyyy
{ 	
	/*var fecha_inicial=document.form.txt_inicio.value;
	var fecha_final=document.form.hid_hasta_itin.value;
	*/
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
		
	if ( (anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || 
	 ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2)) )
	{
	  alert("La fecha inicial no debe ser mayor a la fecha final"); 
	  document.form.hid_hasta_itin.value='';
	  return;
	}
}

function habiDesabiFechas(elemento){
    txt_inicio = document.getElementById("txt_inicio");
    hid_hasta_itin = document.getElementById("hid_hasta_itin");
    if (elemento.checked==true){ 
            txt_inicio.disabled=false;
            hid_hasta_itin.disabled=false;
    }else{ 
            txt_inicio.disabled=true;
            hid_hasta_itin.disabled=true; 
            txt_inicio.value="";
            hid_hasta_itin.value="";
    }
}
//
--></SCRIPT>
<style type="text/css">
<!--
.Estilo1 {color: #FFFFFF}
-->
</style>
</head>
<body>
<form name="form" method="post" action="pmod_e1.php" enctype="multipart/form-data" id="form1">
<table width="850" border="0" class="tablaalertas">
  <tr>
    <td><div align="center">
      <table width="850" border="0" cellpadding="0" cellspacing="0">
          <tr class="td_gray">
            <td><div align="center" class="normalNegroNegrita">INGRESAR MODIFICACI&Oacute;N PRESUPUESTARIA<br><a href="javascript:abrir_ventana('reportes/presupuesto/disponibilidadTotal.php')">Disponibilidad</a></div></td>          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
    </div></td>
  </tr>
  <tr>
    <td><table width="850" border="0" cellpadding="0" cellspacing="0" class="tablaalertas">
      <tr>
        <td width="158"><input type="hidden" name="tipoModificacion" value="M"></td>
         <td width="193"><span class="normal"><input name="rb_tp" id="rb_tp" type="radio" value="3"> Cr&eacute;dito</span></td>
         <td width="193"><span class="mpeqNegrita_naranja"><span class="normal"> <input name="rb_tp" id="rb_tp" type="radio" value="5" checked >Traspaso</span> </span></td>
         <td width="193"><span class="normal"><input name="rb_tp" id="rb_tp" type="radio" value="2"> Disminuci&oacute;n</span></td>
  
        
      </tr>
      <tr>
      	<td><div align="left" class="normalNegrita">Fecha: <!-- (S&oacute;lo en caso de Cr&eacute;dito) --></div>
      					<input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse"
readonly="readonly"/>
				
				
					<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" 	title="Show popup calendar" >
						<img src="js/lib/calendarPopup/img/calendar.gif"  class="cp_img"  alt="Open popup calendar"/>
					</a>	
					
					
					</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><input name="hid_largo" type="hidden" id="hid_largo">
      <input name="hid_val" type="hidden" id="hid_val"></td>
  </tr>
  <tr>
    <td><table width="850" border="0" align="left" cellpadding="0" cellspacing="0" class="tablaalertas">
      <tr class="td_gray">
        <td width="300"><div align="center" class="localizadornegrita"><span class="Estilo1"><strong>
        <a href="javascript:abrir_ventana('includes/arbolCategoria.php?dependencia=<?php echo $_SESSION['user_depe_id'];?>&campo_nom_supe=txt_nombre_imputa&campo_cod_supe=txt_cod_imputa&campo_nombre_accion=txt_nombre_accion&campo_cod_accion=txt_cod_accion&tipo=chk_tp_imputa&formulario=form&tipo_docu=0&campo_cod_supe2=txt_cod_imputa2&campo_cod_accion2=txt_cod_accion2')">
        <img src="imagenes/estadistic.gif" width="24" height="24" border="0"  /></a></strong></span>
        <span class="normal"><a href="javascript:abrir_ventana('includes/arbolCategoria.php?dependencia=<?php echo $_SESSION['user_depe_id'];?>&campo_nom_supe=txt_nombre_imputa&campo_cod_supe=txt_cod_imputa&campo_nombre_accion=txt_nombre_accion&campo_cod_accion=txt_cod_accion&tipo=chk_tp_imputa&formulario=form&tipo_docu=0&opcion=pcta&codigo_origen=pmod&campo_cod_supe2=txt_cod_imputa2&campo_cod_accion2=txt_cod_accion2')">Categor&iacute;a</a></span></div></td>
        <td width="100"><div align="center" class="normal">C&oacute;digo</div></td>
        <td width="700"><div align="center"><span class="normal">Denominaci&oacute;n</span></div></td>
        </tr>
      <tr>
        <td><div align="left">
            <input name="chk_tp_imputa" type="radio" class="normalNegrita" value="1">
            <span class="normal">Proyectos</span></div></td>
        <td rowspan="2"><div align="center">
            <input name="txt_cod_imputa" type="hidden" id="txt_cod_imputa" value="" >
		  <input name="txt_cod_imputa2" type="text" class="normalNegro" id="txt_cod_imputa2" size="15" value="" readonly="readonly">
        </div></td>
        <td rowspan="2">
          <div align="center">
            <input name="txt_nombre_imputa" type="text" class="normal" id="txt_nombre_imputa" size="80" readonly="readonly">
        </div></td>
        </tr>
      <tr>
        <td valign="top"><div align="left">
            <input name="chk_tp_imputa" type="radio" class="normalNegrita" value="0">
            <span class="normal">Acciones Cent. </span></div></td>
      </tr>
      <tr>
        <td><div align="left">
            <p><span class="normal">&nbsp;Acci&oacute;n Espec&iacute;fica</span> </p>
        </div></td>
        <td><div align="center">
            <input name="txt_cod_accion" type="hidden" id="txt_cod_accion">
		    <input name="txt_cod_accion2" type="text" class="normalNegro" id="txt_cod_accion2" size="15" readonly>
        </div></td>
        <td>
          <div align="center">
            <input name="txt_nombre_accion" type="text" class="normal" id="txt_nombre_accion" size="80" readonly="readonly">
        </div></td>
        </tr>
      <tr>
        <td></td>
        <td><div align="center">
        <input type='hidden' name='opt_depe' value='<?php echo $_SESSION['user_depe_id']?>'>
		  
</div></td>
        <td><input name="txt_depend" type="hidden" id="txt_depend" value="<?php echo(trim($_SESSION['user_depe_id'])); ?>">
		</td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="850" border="0" cellpadding="0" cellspacing="0" class="tablaalertas" id="tbl_part">
      <tr class="td_gray">
        <td width="138"><div align="center"><span class="normal"><a href="javascript:abrir_ventana('documentos/pmod/arbol_partidas.php?tipo=1&tipo_doc=1',550)"><img src="imagenes/estadistic.gif" border="0" />Partida</a></span></div></td>
        <td width="386"><div align="center"><span class="normal">Denominaci&oacute;n</span></div></td>
        <td width="62"><div align="center" class="normal">Tipo</div></td>
        <td width="182"><div align="center"><span class="normal">Monto</span></div></td>
      </tr>
      <tbody id="ar_body">
	  </tbody>
    </table>
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center"><input type="button" value="agregar" onClick="javascript:add_opciones()"/></div></td>
  </tr>
  <tr>
    <td>
	  <table width="850" border="0" cellpadding="0" cellspacing="0" class="tablaalertas" id="tbl_mod">
       <tr class="td_gray">
        <td colspan="2"><p align="center" class="normal"><span class="localizadornegrita"><span class="Estilo1"><strong></strong></span></span>Proyecto o Acci&oacute;n Centralizada<span class="localizadornegrita"><span class="Estilo1"><strong></strong></span></span></p></td>
        <td width="70" class="normal"><div align="center">Acci&oacute;n Espec&iacute;fica </div></td>
        <td width="80" class="normal"><div align="center">Dependencia</div></td>
        <td width="95"><div align="center" class="normal"> Partida</div></td>
        <td width="195"><div align="center" class="normal">Denominaci&oacute;n</div></td>
        <td width="45" class="normal"><div align="center">Tipo</div></td>
        <td width="100"><div align="center" class="normal">Monto</div></td>
        <td width="70">&nbsp;</td>
      </tr>
      <tbody id="item">
	  </tbody>
      <tr>
        <td colspan="9">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="9"><div align="center"></div></td>
      </tr>
    </table>
	</td>
   </tr>
    <tr>
     <td>
   		  <table width="420" align="center">
			<?
			include("includes/respaldos.php");
			?>
		 </table>
		</td>
	   </tr>	 
      <tr>
     <td>
	    <div align="center" class="normalNegrita">Exposici&oacute;n de Motivos </div>
	 </td>
   </tr>
   
   <tr>
     <td>
	    <div align="center">
	    <textarea name="txt_motivos" cols="80" rows="15" class="normal" id="txt_motivos"></textarea>
        </div>
	 </td>
   </tr>
    <tr>
      <td>  <div align="center">
	  
<?
	
	$id_opcion =  1;	
	$objeto_siguiente_id = 0;
	$cadena_padre_id = 0;
	$cadena_siguiente_id = 0;
	
	//Por proyecto
	$objeto_siguiente_id_proy = 0;
	$cadena_siguiente_id_proy = 0;
	$cadena_padre_id_proy = 0;
	
	
	$sql = "select * from sai_buscar_opcion('$id_opcion') as (nombre_opcion varchar, desc_opcion varchar)";
	
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	if ($row = pg_fetch_array($resultado)) {
		$nombre_opcion = $row["nombre_opcion"];
		$nombre_boton = strtolower($row["desc_opcion"]);
	}
	$opciones_def = "'$request_id_tipo_documento',$id_opcion,$objeto_siguiente_id,$objeto_siguiente_id_proy,$cadena_siguiente_id,$cadena_siguiente_id_proy,$request_id_objeto";
	?>
<input type="button" value="Procesar" onClick="javascript:revisar_doc(<? echo $opciones_def; ?>)"></input>
		<?php $tipo_doc="'".$request_id_tipo_documento."'"?>
        </div></td>
    </tr>
    <tr>
     <td>
	  <div align="center">
	  </div>
	 </td>
  </tr>
</table>
</form>
</body>
<?php //  pg_close($conexion); ?>
</html>
