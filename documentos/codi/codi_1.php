<?php
  ob_start();
  session_start();
  require_once("../../includes/conexion.php");
  
  if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:index.php',false);
	ob_end_flush();
	exit;
  }
  ob_end_flush();

  $cont=1;
  $usuario = $_SESSION['login'];
  $user_perfil_id = $_SESSION['user_perfil_id'];
  //$anno= $_SESSION['an_o_presupuesto'];
  $anno = 2014;
  $user_depe=$_SESSION['user_depe_id'];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI:Comprobante Diario</title>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css"	media="all"/>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script language="JavaScript" src="../../js/funciones.js"></script>  
<script language="JavaScript" src="../../js/botones.js"></script>
<script type="text/javascript" src="../../js/funciones_codi.js"></script>
<link type="text/css"         href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet" />
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
	g_Calendar.setDateFormat('dd/mm/yyyy');
</script>

<script language="JavaScript" type="text/JavaScript">
function validar_digito(objeto)
{
	var checkOK = "\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA\u00F1\u00D1ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789 -_.,;()/:&[]*+";
	var checkStr = objeto.value;
	var allValid = true;
	for (i = 0;  i < checkStr.length;  i++)
	{
		ch = checkStr.charAt(i);
		for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
		if (j == checkOK.length)
		{
			var cambio=checkStr.substring(-1,i) 
			objeto.value=cambio;
			alert("Estos caracteres no est\u00E1n permitidos");
			break;
		}
	}
}


var contador_partidas=0;
function comp_blanco(){
	if (document.form.comp_id.value=='')
		alert("Debe especificar el numero del compromiso, de no aplicar escriba N/A");
	    document.getElementById('comp_id').focus();
	return
}

function consulta_presupuesto(){
	document.form.fuente.value='';
	document.form.numero_reserva.value='';
    document.getElementById('Categoria').style.display='none';
	document.getElementById('itemContainerTemp').style.display='';
	var tbody = document.getElementById('body_partidas');

    for(i=0;i<contador_partidas;i++){
		tbody.deleteRow(0);	
	}

	    contador_partidas=0;
	    var valor=document.form.comp_id.value;
		for(i=0;i<listado_comp.length;i++)
		{
			var comp = listado_comp[i][0];
			if (valor==comp){
			//	alert(listado_comp[i]);
			
			document.form.fuente.value=listado_comp[i][14];
			document.form.numero_reserva.value=listado_comp[i][13];
			var fila = document.createElement("tr");
			contador_partidas++;

			//SECUENCIAL
			var columna0 = document.createElement("td");
			columna0.setAttribute("align","center");
			columna0.className = 'titularMedio';
			name="txt_seq"+(contador_partidas-1);
			var imp_0 = document.createElement("INPUT");
			imp_0.setAttribute("type","text");
			imp_0.setAttribute("readOnly","true");
			imp_0.setAttribute("name",name);
			imp_0.setAttribute("Id",name);
			imp_0.value=contador_partidas;
			imp_0.size='3';
			imp_0.className='normalNegro';
			columna0.appendChild(imp_0);
			
			//CODIGO DE LA CTA
			var columna1 = document.createElement("td");
			columna1.setAttribute("align","center");
			columna1.className = 'titularMedio';
			name="txt_cta"+(contador_partidas-1);
			var imp_1 = document.createElement("INPUT");
			imp_1.setAttribute("type","text");
			imp_1.setAttribute("readOnly","true");
			imp_1.setAttribute("name",name);
			imp_1.setAttribute("Id",name);
			imp_1.value=listado_comp[i][8];
			imp_1.size='13';
			imp_1.className='normalNegro';
			columna1.appendChild(imp_1);

			//CODIGO TIPO IMPUTACION PROY O ACC
			var columna10 = document.createElement("td");
			columna10.setAttribute("align","center");
			columna10.className = 'titularMedio';
			name="txt_tipo_impu"+(contador_partidas-1);
			var imp_10 = document.createElement("INPUT");
			imp_10.setAttribute("type","hidden");
			imp_10.setAttribute("readOnly","true");
			imp_10.setAttribute("name",name);
			imp_10.setAttribute("Id",name);
			imp_10.value=listado_comp[i][3];
			imp_10.size='1';
			imp_10.className='normalNegro';
			columna10.appendChild(imp_10);
			
			//CODIGO DEL PROY/ACC OCULTO
			var columna8 = document.createElement("td");
			columna8.setAttribute("align","center");
			columna8.className = 'titularMedio';			
			name="txt_proy"+(contador_partidas-1);
			var imp_8 = document.createElement("INPUT");
			imp_8.setAttribute("type","hidden");
			imp_8.setAttribute("name",name);
			imp_8.setAttribute("Id",name);
			imp_8.value=listado_comp[i][2];
			imp_8.size='5';
			imp_8.className='normalNegro';
			columna8.appendChild(imp_8);


			//CODIGO DEL PROY/ACC 
			var columna11 = document.createElement("td");
			columna11.setAttribute("align","center");
			columna11.className = 'titularMedio';
			name="txt_proy2"+(contador_partidas-1);
			var imp_11 = document.createElement("INPUT");
			imp_11.setAttribute("type","text");
			imp_11.setAttribute("readOnly","true");
			imp_11.setAttribute("name",name);
			imp_11.setAttribute("Id",name);
			imp_11.value=listado_comp[i][11];
			imp_11.size='5';
			imp_11.className='normalNegro';
			columna11.appendChild(imp_11);
		
			
			//CODIGO DEL ACC ESP OCULTO
			var columna9 = document.createElement("td");
			name="txt_acc"+(contador_partidas-1);
			var imp_9 = document.createElement("INPUT");
			imp_9.setAttribute("type","hidden");
			imp_9.setAttribute("name",name);
			imp_9.setAttribute("Id",name);
			imp_9.value=listado_comp[i][1];
			columna9.appendChild(imp_9);


			//CODIGO DEL ACC ESP
			var columna12 = document.createElement("td");
			columna12.setAttribute("align","center");
			columna12.className = 'titularMedio';
			name="txt_acc2"+(contador_partidas-1);
			var imp_12 = document.createElement("INPUT");
			imp_12.setAttribute("type","text");
			imp_12.setAttribute("readOnly","true");
			imp_12.setAttribute("name",name);
			imp_12.setAttribute("Id",name);
			imp_12.value=listado_comp[i][12];
			imp_12.size='8';
			imp_12.className='normalNegro';
			columna12.appendChild(imp_12);
			
			//CODIGO DE LA PARTIDA
			var columna2 = document.createElement("td");
			columna2.setAttribute("align","center");
			columna2.className = 'titularMedio';
			name="txt_partida"+(contador_partidas-1);
			var imp_2 = document.createElement("INPUT");
			imp_2.setAttribute("type","text");
			imp_2.setAttribute("readOnly","true");
			imp_2.setAttribute("name",name);
			imp_2.setAttribute("Id",name);
			imp_2.value=listado_comp[i][4];
			imp_2.size='10';
			imp_2.className='normalNegro';
			columna2.appendChild(imp_2);

			//NOMBRE DE LA PARTIDA
			var columna3 = window.opener.document.createElement("td");
			columna3.setAttribute("align","Center");
			columna3.className = 'titularMedio';
			var imp_3 = document.createElement("INPUT");
			imp_3.setAttribute("type","text");
	      	name="txt_desc"+(contador_partidas-1);
	    	imp_3.setAttribute("name",name);
	    	imp_3.setAttribute("readOnly","true");
			imp_3.className = "normalNegro";
			imp_3.setAttribute("value",listado_comp[i][7]);
			imp_3.setAttribute("id",name);
			imp_3.setAttribute("size","30");
			columna3.appendChild (imp_3);	

			//MONTO COMPROMISO
			var columna4 = document.createElement("td");
			columna4.setAttribute("align","right");
			columna4.className = 'titularMedio';
			name="monto_comp"+(contador_partidas-1);
			var imp_4 = document.createElement("INPUT");
			imp_4.setAttribute("type","text");
			imp_4.setAttribute("name",name);
			imp_4.setAttribute("Id",name);
			imp_4.setAttribute("readOnly","true");
			imp_4.setAttribute("value",listado_comp[i][9]);
			imp_4.size='10';
			imp_4.className='normalNegro';
			columna4.appendChild(imp_4);
			
			//MONTO SUJETO
			var columna5 = document.createElement("td");
			columna5.setAttribute("align","right");
			columna5.className = 'titularMedio';
			name="txt_debe"+(contador_partidas-1);
			var imp_5 = document.createElement("INPUT");
			imp_5.setAttribute("type","text");
			imp_5.setAttribute("name",name);
			imp_5.setAttribute("Id",name);
			imp_5.setAttribute("onkeypress","return inputFloat(event,true)");
			imp_5.value='0.0';
			imp_5.size='10';
			imp_5.className='normalNegro';
			columna5.appendChild(imp_5);
			
			//MONTO EXENTO
			var columna6 = document.createElement("td");
			columna6.setAttribute("align","right");
			columna6.className = 'titularMedio';
			name="txt_haber"+(contador_partidas-1);
			var imp_6 = document.createElement("INPUT");
			imp_6.setAttribute("type","text");
			imp_6.setAttribute("name",name);
			imp_6.setAttribute("Id",name);
			imp_6.setAttribute("onkeypress","return inputFloat(event,true)");
			imp_6.value='0.0';
			imp_6.size='10';
			imp_6.className='normalNegro';
			columna6.appendChild(imp_6);


			//Enlace Agregar
			var columna7 = document.createElement("td");
			columna7.setAttribute("align","center");
			columna7.className = 'link';
			editLink = document.createElement("a");
			linkText = document.createTextNode("Agregar");
 			editLink.setAttribute("href", "javascript:add_itine2('servicios','0','"+(contador_partidas-1)+"')");
 			editLink.appendChild(linkText);
			columna7.appendChild (editLink);
			
			fila.appendChild(columna0); 
			fila.appendChild(columna1);
			fila.appendChild(columna11);
			fila.appendChild(columna12);
			fila.appendChild(columna2);
			fila.appendChild(columna3);
			fila.appendChild(columna4);
			fila.appendChild(columna5); 
			fila.appendChild(columna6);
			fila.appendChild(columna7);
			fila.appendChild(columna10);
			fila.appendChild(columna8);
			fila.appendChild(columna9);  
			tbody.appendChild(fila);
			document.form.numero_reserva.value=listado_comp[i][10];
			document.form.centro_gestor.value=listado_comp[i][11];
			document.form.centro_costo.value=listado_comp[i][12];
			document.form.txt_cod_imputa.value=listado_comp[i][2];
			document.form.txt_cod_accion.value=listado_comp[i][1];
			document.form.txt_cod_imputa2.value=listado_comp[i][11];
			document.form.txt_cod_accion2.value=listado_comp[i][12];
			if (listado_comp[i][3]==0){
				  document.form.txt_nombre_accion.value=listado_comp[i][6];
				  document.form.txt_nombre_imputa.value=listado_comp[i][5];
				  document.form.chk_tp_imputa[0].checked=false;
				  document.form.chk_tp_imputa[1].checked=true;}
				else{
					document.form.txt_nombre_accion.value=listado_comp[i][6];
					document.form.txt_nombre_imputa.value=listado_comp[i][5];
					document.form.chk_tp_imputa[0].checked=true;
					document.form.chk_tp_imputa[1].checked=false;
					}
	   }}

		
		if(contador_partidas==0){
			//N/A
			document.getElementById('Categoria').style.display='';
			document.form.txt_nombre_accion.value='';
			document.form.txt_nombre_imputa.value='';

			document.form.txt_cod_imputa.value='';
		    document.form.txt_cod_accion.value='';
			document.form.txt_cod_imputa2.value='';
		    document.form.txt_cod_accion2.value='';
		    document.form.numero_reserva.value='0';
			document.form.txt_nombre_accion.value='';
			document.form.txt_nombre_imputa.value='';
			document.form.chk_tp_imputa[0].checked=false;
			document.form.chk_tp_imputa[1].checked=false;

		}
	
		tbody2=document.getElementById('servicios');
		
		// borrar las partidas que hayan sido agregadas
		for(i=0;i<acciones.length;i++){
		   tbody2.deleteRow(3);
		   acciones.pop();
		}

			acciones.pop();
			document.form.total_debe.value=0;
			document.form.total_haber.value=0;
		 	document.form.diferencia.value=0;	   
}




function add_itine2(id,accion,tipo){   
	if(accion==0){
		if((document.getElementById('txt_debe'+tipo).value==0) && (document.getElementById('txt_haber'+tipo).value==0)){
			alert("Debe especificar el monto correspondiente en la columna (debe/haber) que desea afectar ");
		    document.getElementById('txt_debe'+tipo).focus();
			return;
		}

        if((document.getElementById('txt_debe'+tipo).value!=0) && (document.getElementById('txt_haber'+tipo).value!=0)){
			alert("Las columnas del debe y haber no pueden ser diferentes de cero (0) al mismo tiempo ");
		    document.getElementById('txt_debe'+tipo).focus();
			return;
		}

        var monto_compromiso=parseFloat(MoneyToNumber(document.getElementById('monto_comp'+tipo).value));
        var montodebe=parseFloat(MoneyToNumber(document.getElementById('txt_debe'+tipo).value));
        var montohaber=parseFloat(MoneyToNumber(document.getElementById('txt_haber'+tipo).value));
        
        if (monto_compromiso<0){
        	monto_compromiso=monto_compromiso*(-1);
        }
        
        if ((montodebe > monto_compromiso) || (montohaber > monto_compromiso)){
		 alert("El monto introducido, no puede ser superior al monto del compromiso");
		 document.getElementById('txt_debe'+tipo).focus();
		 return;
        }
 		
		//Verificamos si esta ya registrada con el mismo proyecto y acción
		for(l=0;l<acciones.length;l++)
		{
		 if ((acciones[l][5]==document.getElementById('txt_partida'+tipo).value) && (acciones[l][6]==document.getElementById('txt_proy'+tipo).value) && (acciones[l][7]==document.getElementById('txt_acc'+tipo).value))
		 {
			alert("Partida ya seleccionada con el mismo proyecto y acci\u00F3n...");
			return;
		 }
		}
        
		var registro = new Array(11);//8
		
		registro[0] = document.getElementById('txt_seq'+tipo).value;
		registro[1] = document.getElementById('txt_cta'+tipo).value;
		registro[2] = document.getElementById('txt_desc'+tipo).value;
		registro[3] = document.getElementById('txt_debe'+tipo).value;
		registro[4] = document.getElementById('txt_haber'+tipo).value;
		if (document.getElementById('txt_partida'+tipo).value==""){
			registro[5] = 0;
		}else{
			registro[5] = document.getElementById('txt_partida'+tipo).value;
		}
		registro[6] = document.getElementById('txt_proy2'+tipo).value;
		registro[7] = document.getElementById('txt_acc2'+tipo).value;
		registro[8] = document.getElementById('txt_tipo_impu'+tipo).value;
		registro[9] = document.getElementById('txt_proy'+tipo).value;
		registro[10] = document.getElementById('txt_acc'+tipo).value;		
		acciones[acciones.length]=registro;
	
	}

	
	var tbody = document.getElementById('body');
	var tbody2 = document.getElementById(id);

	for(i=0;i<acciones.length-1;i++){
		tbody2.deleteRow(3);
	}
	
	if(accion!=0){
		tbody2.deleteRow(3);
		for(i=tipo;i<acciones.length;i++){
			acciones[i-1]=acciones[i];
	   }

		acciones.pop();
	}
	var subtotal = 0;
 	var cant_debe= 0;
	var cant_haber= 0;	
    var dife_dh= 0; 
	var reng =1;
	var a=1;
	
	for(i=0;i<acciones.length;i++){
		acciones[i][0] = i+1;
    	var row = document.createElement("tr")
		var td1=document.createElement("td")
		td1.setAttribute("align","justify")
		td1.appendChild(document.createTextNode(acciones[i][0]))
		
		var td2=document.createElement("td")
		td2.setAttribute("align","justify")
		td2.appendChild(document.createTextNode(acciones[i][1]))
		
		var td6=document.createElement("td")
		td6.setAttribute("align","left")
        td6.appendChild(document.createTextNode(acciones[i][6]))
        
        var td8=document.createElement("td")
		td8.setAttribute("align","left")
        td8.appendChild(document.createTextNode(acciones[i][7]))
        
		var td3=document.createElement("td")
		td3.setAttribute("align","justify")
		td3.appendChild(document.createTextNode(acciones[i][2]))

		var td4=document.createElement("td")
		td4.setAttribute("align","right")
        td4.appendChild(document.createTextNode(acciones[i][3]))
		
		var td5=document.createElement("td")
		td5.setAttribute("align","right")
        td5.appendChild(document.createTextNode(acciones[i][4]))
		
		var td26 = document.createElement("td");				
		td26.setAttribute("align","Center");
        td26.className = 'link';
		editLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		//editLink.setAttribute("href", "javascript:add_itine2('eliminar','"+(i+1)+"')");
		editLink.setAttribute("href", "javascript:add_itine2('"+id+"','"+(i+1)+"','"+(i+1)+"')");
		editLink.appendChild(linkText);
		td26.appendChild (editLink);

		var td7=document.createElement("td")
		td7.setAttribute("align","justify")
		td7.appendChild(document.createTextNode(acciones[i][5]))
		
		row.appendChild(td1);
		row.appendChild(td2);
		row.appendChild(td6);
		row.appendChild(td8);
		row.appendChild(td7);
		row.appendChild(td3);
		row.appendChild(td4);
		row.appendChild(td5);
		row.appendChild(td26);
		tbody.appendChild(row);
	
		cant_debe=eval(eval(cant_debe)+eval(acciones[i][3]));
		cant_haber=eval(cant_haber)+(eval(acciones[i][4]));
        dife_dh=eval(dife_dh)+eval(eval(acciones[i][3])-eval(acciones[i][4]));
		reng= reng + 1;
	   }	
		   
	document.form.txt_cuenta.value = 0;
	document.form.txt_cuenta2.value = 0;
	document.form.descripcion.value = "";
	document.form.txt_debe.value = 0;
	document.form.txt_haber.value = 0;
	document.form.largo.value = acciones.length;
    document.form.total_debe.value=redondear_dos_decimal(cant_debe);
	document.form.total_haber.value=redondear_dos_decimal(cant_haber);
 	document.form.diferencia.value=redondear_dos_decimal(dife_dh);
	document.form.txt_renglon.value = reng;

}













/////////////////////////////////////
function cargarCuenta()
{
 if(trim(document.getElementById("cuenta").value)==""){
   alert("Debe introducir el n"+uACUTE+"mero de cuenta o una palabra contenida en la descripci"+oACUTE+"n.");
   document.getElementById("cuenta").focus();
 }else{
  	   if((document.form.txt_cod_imputa.value=="") && (document.form.txt_cod_accion.value=="")){
		alert('Debe seleccionar el Proyecto o Accion Centralizada');
		return;
	   }
		
	   tokens = document.getElementById("cuenta").value.split( ":" );
	   cuenta = (tokens[0])?trim(tokens[0]):"";
	   partida = (tokens[1])?trim(tokens[1]):"";
	   descripcion = "";
		
	   if(partida.length>0 && parseInt(partida.substring(0,1),10)){
		 tokens = partida.split(" ");
		 partida = tokens[0];
		}else{
			partida = "";
		}

		descripcion = estaEnCuentas(cuenta, partida);
		if(descripcion!=""){
			document.getElementById('txt_cuenta').value=cuenta;
			var cta= cuenta.substring(0,11);
			if (cta=="1.1.1.01.02"){
			  document.getElementById('validar_ref').value=1;
			}
			
			var cta_gasto= cuenta.substring(0,1);
			if ((cta_gasto=="6")&& (cuenta!="6.3.1.01.02.01.98")&&(cuenta!="6.3.1.01.02.01.99")&&(cuenta!="6.1.4.01.99.01.01")){
				  document.getElementById('validar_comp').value=1;
				}
			
			document.getElementById('txt_descripcion').value=descripcion;
			document.getElementById('txt_cuenta2').value=partida;
			document.form.txt_proyecto.value=document.getElementById('txt_cod_imputa').value;
			document.form.txt_acc_esp.value=document.getElementById('txt_cod_accion').value;
			document.form.txt_proyecto2.value=document.getElementById('txt_cod_imputa2').value;
			document.form.txt_acc_esp2.value=document.getElementById('txt_cod_accion2').value;
			limpiarCuenta();
		}else{
			  alert("Debe seleccionar una cuenta contable registrada en el sistema");
		}
	}
}

function revisar_doc(id_tipo_documento,id_opcion,objeto_siguiente_id,objeto_siguiente_id_proy,cadena_siguiente_id,cadena_siguiente_id_proy,id_objeto_actual){ 
  document.form.action="accion_ejecutar.php?tipo="+id_tipo_documento+"&accion="+id_objeto_actual+"&accion_sig=" + objeto_siguiente_id + "&hijo="+cadena_siguiente_id+"&opcion="+id_opcion;
  revisar();
}

function verifica_partida(){
  abrir_ventana('../../includes/arbolCategoria.php?dependencia=<?= $_SESSION['user_depe_id']?>&campo_nom_supe=txt_nombre_imputa&campo_cod_supe=txt_cod_imputa&campo_nombre_accion=txt_nombre_accion&campo_cod_accion=txt_cod_accion&tipo=chk_tp_imputa&formulario=form&tipo_docu=0&opcion=codi&campo_cod_supe2=txt_cod_imputa2&campo_cod_accion2=txt_cod_accion2&nombre_fte=fuente&id_fte=numero_reserva');
}

function revisar(){
  if(document.form.hid_desde_itin.value==""){
	alert("Debe seleccionar la fecha del documento");
	document.form.hid_desde_itin.focus();
	return;
  }
	var valor_comp=document.form.comp_id.value;
  if((valor_comp=='') ||( (valor_comp.substring(0,4)!='comp') && (valor_comp!='N/A') && (valor_comp!='n/a'))){
	alert("Debe indicar el n\u00FAmero del compromiso, de no aplicar colocar N/A");
	document.form.comp_id.focus();
	return;
  }
  /*
  if(((document.form.validar_comp.value=="1") && (document.form.comp_id.value==0)) ||
     ((document.form.validar_comp.value=="1") && (document.form.comp_id.value=="N/A"))	  ){
		alert("Debe indicarar el n\u00FAmero del compromiso asociado al gasto");
		document.form.comp_id.focus();
		return;
	  }*/

  if(document.form.cod_doc.value==""){
	alert("Debe indicar el documento al cual est\u00E1 asociado el comprobante diario manual, de no aplicar coloque N/A");
	document.form.cod_doc.focus();
	return;
  }

  if((document.form.validar_ref.value=="1") && (document.form.num_ref.value=="")){
	alert("Debe indicar el n\u00FAmero de referencia bancaria");
	document.form.num_ref.focus();
	return;
  }

  if(document.form.txt_comentario.value==""){
	alert("Debe especificar la justificaci\u00F3n del comprobante");
	document.form.txt_comentario.focus();
	return;
  }

  if(acciones.length<1){
	alert("Debe colocar al menos dos registros que contengan: \n cuenta contable, justificaci\u00F3n, monto en la columna debe y haber");
	return;
  }
  
  if((document.form.comp_id.value=='N/A') && (document.form.txt_cod_imputa.value=="") && (document.form.txt_cod_accion.value=="")){
	alert('Debe seleccionar el proyecto o accion centralizada');
	return;
  }
	  
  if(document.form.diferencia.value!=0){
	alert(" Debe revisar las diferencias en los montos del comprobante. No se puede generar un comprobante diario descuadrado");
	return;
  }
  
	document.form.txt_arreglo.value=crear_txt_arreglo('document.form.txt_arreglo.value',0); 
	document.form.txt_arreglo1.value=crear_txt_arreglo('document.form.txt_arreglo1.value',1); 
	document.form.txt_arreglo2.value=crear_txt_arreglo2('document.form.txt_arreglo2.value',2);
	document.form.txt_arreglo3.value=crear_txt_arreglo('document.form.txt_arreglo3.value',3);
	document.form.txt_arreglo4.value=crear_txt_arreglo('document.form.txt_arreglo4.value',4);
	document.form.txt_arreglo5.value=crear_txt_arreglo('document.form.txt_arreglo5.value',5);
	document.form.txt_arreglo6.value=crear_txt_arreglo('document.form.txt_arreglo6.value',6);
	document.form.txt_arreglo7.value=crear_txt_arreglo('document.form.txt_arreglo7.value',7);
	document.form.txt_arreglo8.value=crear_txt_arreglo('document.form.txt_arreglo8.value',8);
	document.form.txt_arreglo9.value=crear_txt_arreglo('document.form.txt_arreglo9.value',9);
	document.form.txt_arreglo10.value=crear_txt_arreglo('document.form.txt_arreglo10.value',10);

	if(confirm("Est\u00e1 seguro que desea registrar la informaci\u00f3n suministrada ?"))
		document.form.action="codi_e1.php";
		document.form.submit();
}


</script>

<?php 
  $a_o="comp-%".substr($anno,2,2);
  $i=0;
  $sql_p="Select comp_sub_espe,t1.comp_acc_esp,t1.comp_acc_pp,comp_tipo_impu,t1.comp_id,dc.monto as comp_monto,
  case comp_tipo_impu when CAST(1 AS BIT) then (select proy_titulo  from sai_proyecto where t1.comp_acc_pp=proy_id and pre_anno='".$anno."') else
  (select acce_denom from sai_ac_central where t1.comp_acc_pp=acce_id and pres_anno='".$anno."') end as titulo_proy,
  case comp_tipo_impu when CAST(1 AS BIT) then (select paes_nombre from sai_proy_a_esp where t1.comp_acc_pp=proy_id and paes_id=t1.comp_acc_esp and pres_anno='".$anno."') else
  (select aces_nombre from sai_acce_esp where t1.comp_acc_pp=acce_id and t1.comp_acc_esp=aces_id and pres_anno='".$anno."') end as titulo_accion,
  case comp_tipo_impu when CAST(1 AS BIT) then (select centro_gestor from sai_proy_a_esp where t1.comp_acc_pp=proy_id and paes_id=t1.comp_acc_esp and pres_anno='".$anno."') else
  (select centro_gestor from sai_acce_esp where t1.comp_acc_pp=acce_id and t1.comp_acc_esp=aces_id and pres_anno='".$anno."') end as centro_gestor,
   case comp_tipo_impu when CAST(1 AS BIT) then (select centro_costo from sai_proy_a_esp where t1.comp_acc_pp=proy_id and paes_id=t1.comp_acc_esp and pres_anno='".$anno."') else
  (select centro_costo from sai_acce_esp where t1.comp_acc_pp=acce_id and t1.comp_acc_esp=aces_id and pres_anno='".$anno."') end as centro_costo,
		
  part_nombre,cpat_id,fuente_financiamiento,fuef_descripcion
  from sai_comp_imputa t1,sai_partida t2, sai_convertidor t3, sai_forma_1125 t4, sai_disponibilidad_comp dc,sai_fuente_fin t5 
  where dc.comp_id=t1.comp_id and dc.partida=t2.part_id and t4.pres_anno='".$anno."' and form_id_p_ac=t1.comp_acc_pp and form_id_aesp=t1.comp_acc_esp and 
  t4.esta_id=1 and t2.part_id=t3.part_id and t2.part_id=comp_sub_espe and t1.pres_anno=t2.pres_anno and t5.fuef_id=fuente_financiamiento";
  //echo $sql_p;
  $resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
  while($row=pg_fetch_array($resultado_set_most_p)) 
  {
	$partida = $row['comp_sub_espe'];
  	$acc_esp = $row['comp_acc_esp'];
  	$acc_pp = $row['comp_acc_pp'];
  	$imputacion = $row['comp_tipo_impu'];
  	$id_comp =  $row['comp_id'];
  	$titulo = $row['titulo_proy'];
  	$accion = $row['titulo_accion'];
  	$descripcion = $row['part_nombre'];
  	$cue_pat=$row['cpat_id'];
  	$monto_comp=$row['comp_monto'];
  	$fuente=$row['fuente_financiamiento'];
  	$gestor = $row['centro_gestor'];
  	$costo = $row['centro_costo'];
  	$nombre_fuente= $row['fuef_descripcion'];
  	echo("
				<script language='javascript'>
				var registro = new Array(); 
				registro[0]='$id_comp';
				registro[1]='$acc_esp';
				registro[2]='$acc_pp';
				registro[3]='$imputacion';
				registro[4]='$partida';					
				registro[5]='$titulo';
				registro[6]='$accion';
				registro[7]='$descripcion';
				registro[8]='$cue_pat';
				registro[9]='$monto_comp';
				registro[10]='$fuente';
				registro[11]='$gestor';
				registro[12]='$costo';
				registro[13]='$fuente';
				registro[14]='$nombre_fuente';
				listado_comp[$i]=registro;
				</script>
				");
				$i++;
   }	

?>

</head>
<body>
<form name="form" method="post" action="codi_1.php?tipo=recarga">
<p align="center" class="normal"><input type="hidden" value="0" name="hid_validar"/>
<input type="hidden" value="0" name="opt_validar"/><br/>
<?php
$sql_perf_tmp="SELECT * FROM sai_buscar_cargo_depen('".$user_perfil_id."') AS carg_nombre ";
$resultado_perf_tmp=pg_query($conexion,$sql_perf_tmp) or die("Error al mostrar");
$row_perf_tmp=pg_fetch_array($resultado_perf_tmp);
?> 
</p>

<table width="712" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
	<td colspan="2" class="normalNegroNegrita">Registrar comprobante diario</td>
  </tr>
  <tr>
	<td width="92" align="left" class="normalNegrita">Fecha<strong>:</strong></td>
	<td width="608" align="right" class="normalNegro">
	   <div align="left"><input type="text" size="10" id="txt_inicio" name="hid_desde_itin" class="dateparse" value="<?php echo $_REQUEST['hid_desde_itin'];?>" readonly/>
	   <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar">
	   <img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
	   </a></div></td>
  </tr>
  <tr class="normal">
	<td height="33" class="normal"><div align="left" class="normalNegrita">N&deg; compromiso:</div></td>
	<td class="normalNegro"><input type="text" name="comp_id" id="comp_id" class="normalNegro" size="15" onblur="comp_blanco()">
	<?php
	  $a_o="comp-%".substr($anno,2,2);
	  $sql_str="SELECT docg_id as comp_id FROM sai_doc_genera where esta_id<>15 and docg_id like '".$a_o."' order by docg_fecha";
	  $resultado = pg_exec($conexion, $sql_str);
	  $numeroFilas = pg_num_rows($resultado);
	  $codigosComp = "";
	  $indice=0;
	  while($row=pg_fetch_array($resultado)){
		$codigosComp .= "'".$row["comp_id"]."',";
		$indice++;
	  }
			$codigosComp = substr($codigosComp, 0, -1);
			$dirInfo = substr($dirInfo, 0, -1);
			?> <script>
					var arreglo_comp = new Array(<?= $codigosComp?>);
					obj2 = new actb(document.getElementById('comp_id'),arreglo_comp);
			   </script>			  
<input type="hidden"  name="text2"> </td>
   </tr>
   <tr>
	 <td class="normalNegrita"><div align="left">Documento asociado</div></td>
	 <td align="left">
		<span  class="normalNegro">
			<input type="text" name="cod_doc" id="cod_doc" size="15" maxlength="20" onfocus="consulta_presupuesto()" onchange="validar_digito(cod_doc)">&nbsp;
		</span>
	 </td>
   </tr>
   <tr>
	 <td class="normalNegrita" width="110"><div align="left">N&deg; Referencia bancaria:</div></td>
	 <td colspan="3" align="left">
		<span  class="normalNegro">
	 	   <input type="text" name="num_ref" id="num_ref" size="15" maxlength="20" onchange="validar_digito(num_ref)">
		   <input type="hidden" name="validar_ref" id="validar_ref" value="0">&nbsp;
		   <input type="hidden" name="validar_comp" id="validar_comp" value="0">
		</span>
	 </td>
   </tr>
   <tr class="normal">
	 <td height="33" class="normalNegrita"><div align="left">Fuente de financiamiento:</div></td>
	 <td class="normal">
	 <input type="text" name="fuente" id="fuente" readonly>
	 <input type="hidden" name="numero_reserva" id="numero_reserva">
	 </td></tr>
   <tr>
	 <td align="left" class="normalNegrita">Justificaci&oacute;n:</td>
	 <td align="right"  class="normalNegro">
	  <div align="left">
		<textarea class="normalNegro" name="txt_comentario" cols="50" id="txt_comentario" onkeydown="textCounter(this,'comentarioLen',60);"
		  onkeyup="textCounter(this,'comentarioLen',60);"  onkeypress="validar_digito(txt_comentario)"></textarea>
		<div style="text-align: right;width: 380px"><input type="text" value="60" class="peqNegrita" maxlength="3" size="3" id="comentarioLen" name="comentarioLen" readonly="readonly"/></div></div>
	 </td>
   </tr>
   <tr>
	 <td colspan="2">

<table width="710" align="center" background="imagenes/fondo_tabla.gif"	class="tablaCentral">
 <tr > 
	<td colspan="2" bgcolor="#F0F0F0" class="normalNegroNegrita" align="center">Informaci&oacute;n presupuestaria</td>
 </tr>
 <tr>
   <td><br>
	 <table align="center" class="tablaalertas">
	  <tr bgcolor="#F0F0F0">
		<td>
		<div align="center" class="normalNegrita" id="Categoria" style="display:none">
		  <a href="javascript:verifica_partida();">
		  <img src="../../imagenes/estadistic.gif" width="24" height="24" border="0"/>Categor&iacute;a
		  </a></div>
		</td>
		<td>
		  <div align="center" class="normalNegrita">C&oacute;digo</div></td>
		<td>
		  <div align="center"><span class="normalNegrita">Denominaci&oacute;n</span></div></td>
	  </tr>
	  <tr>
		<td>
		  <div align="left"><input name="chk_tp_imputa" type="radio" class="peq" value="1"><span class="normalNegrita">Proyecto</span></div></td>
		<td rowspan="2">
		  <div align="center">
		  <input name="txt_cod_imputa" type="hidden" id="txt_cod_imputa" value="" >
		  <input name="txt_cod_imputa2" type="text" class="normalNegro" id="txt_cod_imputa2" size="15" value="" readonly="readonly"></div></td>
		<td rowspan="2">
		  <div align="center"><input name="txt_nombre_imputa" type="text"	class="normalNegro" id="txt_nombre_imputa" size="60" value="" readonly></div></td>
	  </tr>
	  <tr>
		<td valign="top">
		  <div align="left"><input name="chk_tp_imputa" type="radio" class="peq" value="0">
		  <span class="normalNegrita">Acci&oacute;n Centralizada</span>
		  </div></td>
	  </tr>
	  <tr>
		<td><div align="left"><p><span class="normalNegrita">&nbsp;Acci&oacute;n espec&iacute;fica</span></p></div></td>
		<td>
		<input name="txt_cod_accion" type="hidden" id="txt_cod_accion">
		<input name="txt_cod_accion2" type="text" class="normalNegro" id="txt_cod_accion2" size="15" readonly></td>
		<td><input name="txt_nombre_accion" type="text" class="normalNegro" id="txt_nombre_accion" size="60" readonly></td>
  	 </tr>
	 <tr>
	   <td class="normalNegrita">Dependencia</td>
	   <td colspan="2">
		<?php																										
		  $sql_str="SELECT * FROM  sai_seleccionar_campo ('sai_dependenci','depe_id,depe_nombrecort,depe_nombre','depe_id='||'''$user_depe''','',2) resultado_set(depe_id VARCHAR, depe_nombrecort VARCHAR,depe_nombre VARCHAR)";
		  $res_q=pg_exec($sql_str);
		?>
		  <select name="opt_depe" class="normalNegro" id="opt_depe">
		    <?php while($depe_row=pg_fetch_array($res_q)){?>
			<option value="<?= trim($depe_row['depe_id']) ?>"><?= trim($depe_row['depe_nombre'])?></option>
			<?php }?>
		  </select></td></tr>
		  <tr >
		  	<td class="peqNegrita" align="center" colspan="3">
					
						<input type="hidden" name="centro_gestor" id="centro_gestor" size="5" readonly="readonly" class="normalNegro"/>
						
						<input type="hidden" name="centro_costo" id="centro_costo" size="5" readonly="readonly" class="normalNegro">
					</td>
	 </tr>
</table><br>
   </td>
  </tr>
  <tr>
    <td>
	  <table width="700" align="center" id="totales"  class="tablaalertas">
		<tr bgcolor="#F0F0F0">
		  <td ><div align="center" class="normalNegrita">N&deg; Reng</div></td>
		  <td class="normalNegrita"><div align="center">Cuenta</div></td>
		  <td class="normalNegrita"><div align="center">Proy/ACC </div></td>
		  <td class="normalNegrita"><div align="center">Acci&oacute;n Esp.</div></td>
		  <td class="normalNegrita"><div align="center">Partida</div></td>
		  <td class="normalNegrita"><div align="center">Descripci&oacute;n</div></td>
		  <td class="normalNegrita"><div align="center">Monto compromiso</div></td>
		  <td class="normalNegrita"><div align="center">Debe</div></td>
		  <td class="normalNegrita"><div align="center">Haber</div></td>
		  <td class="normalNegrita"><div align="center">Opci&oacute;n</div></td>
		</tr>
		 <tbody id="body_partidas" class="normal">
		 </tbody>
	   </table><br></br>
		 </td>
   </tr>
   <tr>
	 <td class="normalNegrita"><div align="center" id="itemContainerTemp" style="display:none">&nbsp;Cuenta
	   <input autocomplete="off" size="70" type="text" id="cuenta" name="cuenta" value="" class="normalNegro"/><input type="button" value="cargar" class="normal" onclick="cargarCuenta();"/>
	   <br/>&nbsp;<span class="peq_naranja">(*)</span>Introduzca el n&uacute;mero de cuenta o una palabra contenida en la descripci&oacute;n.</div>
		<?php
		  if ($_REQUEST['comp_id']=='N/A')
		   $condicion="";
		  else
		  $condicion="";
		   //$condicion=" and (part_id like '4.11%' or scp.cpat_id like '5%' or scp.cpat_id like '4%') ";
		   $query ="SELECT ".
						   "scp.cpat_id, ".
						   "scp.cpat_nombre, ".
						   "sc.part_id ".
					"FROM ".
						   "sai_cue_pat scp LEFT OUTER JOIN sai_convertidor sc ".
						   "ON (scp.cpat_id=sc.cpat_id) ".
					"WHERE ".
						   "substring(trim(scp.cpat_id)from 16 for 17)<>'00'".$condicion.
					"UNION ".
					"SELECT ".
							"scp.cpat_id, ".
							"scp.cpat_nombre, ".
							"t2.sopg_sub_espe ". 
					"FROM ".
						    "sai_sol_pago t1,sai_sol_pago_imputa t2 ,sai_cue_pat scp ".
					        "LEFT OUTER JOIN sai_convertidor sc2 ON (scp.cpat_id=sc2.cpat_id) ".
 					"WHERE ".
							"t1.sopg_id=t2.sopg_id and comp_id='".$_REQUEST['comp_id']."' and t2.sopg_sub_espe=sc2.part_id ".
					"ORDER BY cpat_id";
 
			$resultado = pg_exec($conexion, $query);
			$numeroFilas = pg_num_rows($resultado);
			$arreglo = "";
			$cuentas = "";
			$partidas = "";
			$nombres = "";
			while($row=pg_fetch_array($resultado)){
			  $arreglo .= "'".$row["cpat_id"]." : ".$row["part_id"]." ".str_replace("\n"," ",$row["cpat_nombre"])."',";
			  $cuentas .= "'".$row["cpat_id"]."',";
			  $partidas .= "'".$row["part_id"]."',";
			  $nombres .= "'".str_replace("\n"," ",$row["cpat_nombre"])."',";
			}
			  $arreglo = substr($arreglo, 0, -1);
			  $cuentas = substr($cuentas, 0, -1);
			  $partidas = substr($partidas, 0, -1);
			  $nombres = substr($nombres, 0, -1);
			?>
			<script>
			  var cuentas = new Array(<?= $cuentas?>);
			  var partidas = new Array(<?= $partidas?>);
			  var nombres = new Array(<?= $nombres?>);
			  var cuentasAMostrar = new Array(<?= $arreglo?>);
			  //actb(document.getElementById('cuenta'),cuentasAMostrar);
			  obj = new actb(document.getElementById('cuenta'),cuentasAMostrar);
			</script><br/>&nbsp;
			
	<table width="698" height="87" background="Imagenes/fondo_tabla.PNG" id="servicios">
	  <tr valign="middle" class="Estilo4" bgcolor="#F0F0F0">
		<td width="31" class="titularMedio"><div align="center">N&deg; Reng</div></td>
		<td width="70" class="titularMedio"><div align="center">Cuenta</div></td>
		<td width="70" class="titularMedio"><div align="center">Proyecto/Acc</div></td>
		<td width="70" class="titularMedio"><div align="center">Acci&oacute;n Esp.</div></td>
		<td width="70" class="titularMedio"><div align="center">Partida</div></td>
		<td width="300" class="titularMedio"><div align="center">Descripci&oacute;n</div></td>
		<td class="titularMedio"><div align="center">Debe</div></td>
		<td class="titularMedio"><div align="center">Haber</div></td>
		<td width="75" class="Estilo3 normalNegrita"><div align="center"><span class="titularMedio">Opci&oacute;n</span></div></td>
	  </tr>
	  <tr valign="top" class="normal">
		<td valign="top" class="normal"><div align="left"><input name="txt_renglon" type="text" disabled class="normalNegro" id="txt_renglon" onBlur="javascript:LimitText(this,1000)"	value="<?= $cont?>" size="3"/></div></td>
		<td align="left" class="normal"><input name="txt_cuenta" type="text" class="normalNegro" id="txt_cuenta" size="14" readonly></td>
		<td align="left" class="normal"><input name="txt_proyecto" type="hidden" id="txt_proyecto">
		<input name="txt_proyecto2" type="text" class="normalNegro" id="txt_proyecto2" size="5" readonly></td>
		<td align="left" class="normal"><input name="txt_acc_esp" type="hidden" id="txt_acc_esp">
		<input name="txt_acc_esp2" type="text" class="normalNegro" id="txt_acc_esp2" size="8" readonly></td>
		<td align="left" class="normal"><input name="txt_cuenta2" type="text" class="normalNegro" id="txt_cuenta2" size="14" readonly></td>						
		<td valign="top" class="normal"><input name="descripcion" type="text" disabled class="normalNegro" id="txt_descripcion" onBlur="javascript:LimitText(this,1000)" size="30"/></td>
		<td width="72" valign="top" class="normal"><input name="txt_debe" type="text" class="normalNegro" id="txt_debe"	onBlur="javascript:LimitText(this,1000)" value="0" size="12" onKeyPress="return acceptFloat(event)"/></td>
		<td width="72" height="34" valign="top" class="normalNegro"><input name="txt_haber" align="left" type="text" class="normalNegro"	id="txt_haber" onBlur="javascript:LimitText(this,1000)" value="0" size="12" onKeyPress="return acceptFloat(event)"/></td>
		<td valign="top" class="normal"><div align="center"><a href="javascript: add_itine('servicios','0')" class="normal" id="agregarpart">Agregar</a></div></td>
	  </tr>
	  <tr>
		<td height="15" colspan="7" align="center" class="normal"></td>
	  </tr>
		
		<tbody id="body" class="normal">
		</tbody>
	</table>
	
	<table width="331" height="69" align="right" background="Imagenes/fondo_tabla.PNG" id="totales">
	  <tr valign="top" class="normal">
		<td width="72" class="normal">&nbsp;</td>
		<td height="17" colspan="2" class="normal">&nbsp;</td>
	  </tr>
	  <tr valign="top" class="normal">
		<td class="normal"><div align="right"><strong>Total:</strong></div></td>
		<td width="98" class="normal"><div align="right"><span class="normal Estilo13"><input name="total_debe" type="text" class="normal" id="total_debe"	onKeyPress="return acceptFloat(event)" value="0" size="12" align="right" readonly/></span></div></td>
		<td width="134" height="15" class="normal"><span class="normal Estilo13"><input name="total_haber" type="text" class="normal" id="total_haber" onKeyPress="return acceptFloat(event)" onKeyUp="javascript: FormatCurrency(total_debe)" value="0" size="12" align="right" readonly/></span></td>
	  </tr>
	  <tr valign="top" class="normal">
		<td class="normal">&nbsp;</td>
		<td><div align="right"><strong>Diferencia:</strong></div></td>
		<td height="15"><label id="existepart"><strong><span class="normal Estilo13">
		  <input name="diferencia" type="text" class="normal" id="diferencia4" onKeyUp="javascript: FormatCurrency(form.diferencia)" value="0" size="12" readonly/>
			</span></strong></label></td>
	   </tr>
		<tbody id="body">
		</tbody>
	</table>
      </td>
	</tr>
 </table>
   
   <p align="center"/>
	<input type="hidden" name="txt_arreglo" value=""/>
	<input type="hidden" name="txt_arreglo1" value=""/>
	<input type="hidden" name="txt_arreglo2" value=""/>
	<input name="txt_arreglo3" type="hidden" id="txt_arreglo3" value=""/>
	<input name="txt_arreglo4" type="hidden" id="txt_arreglo4" value=""/>
	<input type="hidden" name="txt_arreglo5" value=""/>
 	<input type="hidden" name="txt_arreglo6" value=""/>
	<input type="hidden" name="txt_arreglo7" value=""/>
	<input type="hidden" name="txt_arreglo8" value=""/>
	<input type="hidden" name="txt_arreglo9" value=""/>
	<input type="hidden" name="txt_arreglo10" value=""/>    
	<input name="largo" type="hidden" id="largo3"/>
	  </td>
    </tr>
	<tr>
	  <td height="18" colspan="5"><div align="center">
		<input type="button" value="Registrar" onclick="javascript:revisar(<? echo $opciones_def; ?>)"></div>
	  </td>
	</tr>
  </table>

</form>
</body>
</html>
<?php pg_close($conexion);?>
