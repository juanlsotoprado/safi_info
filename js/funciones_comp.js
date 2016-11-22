function blanquear_campos(evento){
	if (evento.value=="036"){
	  document.form1.tipo_evento.value=0;
	  document.form1.tipo_act.value=0;
	}
}

function redondear_dos_decimal(valor) {
	   float_redondeado=Math.round(valor * 100) / 100;
	   return float_redondeado;
	} 

function totalizar(){
	  var monto_total=0;
	  for(i=0;i<partidas.length;i++) {
	    monto_total=parseFloat(monto_total)+parseFloat(document.getElementById('txt_monto_pda'+i).value)
	  }
	  document.form1.txt_monto_tot.value=redondear_dos_decimal(monto_total);
	}

function comparar_fechas(fecha_inicial,fecha_final) //Formato dd/mm/yyyy
{ 	
	var fecha_inicial=document.form1.fecha_i.value;
	var fecha_final=document.form1.fecha_f.value;
		
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
	  document.form1.fecha_f.value='';
	  return;
	}
}

function buscar_pcta()
{
   var m=document.getElementById('pcta_id');  
   var num=document.form1.pcta_id.options.length;

   if (num>1){

   var contador=1;
   while(contador<num_select){
	document.form1.pcta_id.options[1] = null;
	contador++;
   }
   }
	   
	var valor=document.form1.opt_depe.value;
	var info_id = 'pcta_id';
	unidad = document.getElementById(info_id); 
	
	for(i=0;i<pcta_gerencia.length;i++)
	{
		var crear_opcion=document.createElement('option');
		crear_opcion.text=pcta_gerencia[i][0];
		crear_opcion.value=pcta_gerencia[i][0];
		var depe_pcta=pcta_gerencia[i][1];
		if (depe_pcta==valor){
		var vieja_opcion = unidad.options[unidad.selectedIndex];
		try {
			unidad.add(crear_opcion, null); // standards compliant; doesn't work in IE
		}
		catch(ex) {
			unidad.add(crear_opcion); // IE only
		 	  }
		}
	   }
	num_select=document.form1.pcta_id.options.length;
}

var contador_partidas=0;

function consulta_presupuesto(){
	
	var tbody2 = document.getElementById('tbl_mod');
	element_otros = document.getElementById('tbl_mod').getElementsByTagName('tr').length;
	if (element_otros>1){
	for(i=0;i<element_otros-1;i++)	{	 
	  tbody2.deleteRow(1);
	}
	document.form1.txt_monto_tot.value=0;
	document.form1.txt_monto_tot2.value=0;
	ver_monto_letra(0, 'txt_monto_letras','');	
    }
	document.getElementById('Categoria').style.display='none';
	document.getElementById('Partidas').style.display='none';
	
	var tbody = document.getElementById('ar_body');
   //Lo primero que debe hacerse es borrar las partidas existentes

    for(i=0;i<contador_partidas;i++){
		tbody.deleteRow(0);	
	}
    var pcta= document.form1.pcta_id.value;
	    contador_partidas=0;
	    var valor=document.form1.pcta_id.value;
		for(i=0;i<listado_pcta.length;i++)
		{
			var fila = document.createElement("tr");
			var comp = listado_pcta[i][0];
			//if (valor==comp){
			contador_partidas++;
			//CODIGO DE LA PARTIDA
			var columna1 = document.createElement("td");
			columna1.setAttribute("align","center");
			columna1.className = 'normalNegro';
			name="txt_codigo"+(contador_partidas-1);
			var imp_1 = document.createElement("INPUT");
			imp_1.setAttribute("type","text");
			imp_1.setAttribute("readOnly","true");
			imp_1.setAttribute("name",name);
			imp_1.setAttribute("Id",name);
			imp_1.value=listado_pcta[i][4];
			imp_1.size='15';
			imp_1.className='normalNegro';
			columna1.appendChild(imp_1);

			//NOMBRE DE LA PARTIDA
			var columna2 = window.opener.document.createElement("td");
			columna2.setAttribute("align","Center");
			columna2.className = 'titularMedio';
			var imp_2 = document.createElement("INPUT");
			imp_2.setAttribute("type","text");
	      	name="txt_den"+(contador_partidas-1);
	    	imp_2.setAttribute("name",name);
	    	imp_2.setAttribute("readOnly","true");
			imp_2.className = "normalNegro";
			imp_2.setAttribute("value",listado_pcta[i][7]);
			imp_2.setAttribute("id",name);
			imp_2.setAttribute("size","40");
			columna2.appendChild (imp_2);	

			//MONTO PCTA
			var columna5 = document.createElement("td");
			columna5.setAttribute("align","right");
			columna5.className = 'titularMedio';
			name="monto_comp"+(contador_partidas-1);
			var imp_5 = document.createElement("INPUT");
			imp_5.setAttribute("type","text");
			imp_5.setAttribute("name",name);
			imp_5.setAttribute("Id",name);
			imp_5.setAttribute("readOnly","true");
			imp_5.setAttribute("value",listado_pcta[i][10]);
			imp_5.size='10';
			imp_5.className='normalNegro';
			columna5.appendChild(imp_5);

			//MONTO PCTA MOSTRAR
			var columna51 = document.createElement("td");
			columna51.setAttribute("align","right");
			columna51.className = 'titularMedio';
			name="monto_comp2"+(contador_partidas-1);
			var imp_51 = document.createElement("INPUT");
			imp_51.setAttribute("type","text");
			imp_51.setAttribute("name",name);
			imp_51.setAttribute("Id",name);
			imp_51.setAttribute("readOnly","true");
			imp_51.setAttribute("value",listado_pcta[i][10]);
			imp_51.size='15';
			imp_51.className='normalNegro';
			columna51.appendChild(imp_51);
			
			//MONTO COMPROMISO
			var columna3 = document.createElement("td");
			columna3.setAttribute("align","right");
			columna3.className = 'titularMedio';
			name="txt_monto"+(contador_partidas-1);
			var imp_3 = document.createElement("INPUT");
			imp_3.setAttribute("type","text");
			imp_3.setAttribute("name",name);
			imp_3.setAttribute("Id",name);
			imp_3.value='0.0';
			imp_3.size='10';
			imp_3.className='normalNegro';
			columna3.appendChild(imp_3);
			

			fila.appendChild(columna1); 
			fila.appendChild(columna2);
			fila.appendChild(columna5);		
			fila.appendChild(columna3);
			tbody.appendChild(fila); 
			
	        document.form1.txt_cod_imputa.value=listado_pcta[i][2];
		    document.form1.txt_cod_accion.value=listado_pcta[i][1];
		    document.form1.centro_gestor.value=listado_pcta[i][8];
		    document.form1.centro_costo.value=listado_pcta[i][9];
		    document.form1.txt_cod_accion2.value=listado_pcta[i][9];
		    document.form1.txt_cod_imputa2.value=listado_pcta[i][8];
		    
			    
			if (listado_pcta[i][3]==0){
			  document.form1.txt_nombre_accion.value=listado_pcta[i][6];
			  document.form1.txt_nombre_imputa.value=listado_pcta[i][5];
			  document.form1.chk_tp_imputa[0].checked=false;
			  document.form1.chk_tp_imputa[1].checked=true;}
			else{
				document.form1.txt_nombre_accion.value=listado_pcta[i][6];
				document.form1.txt_nombre_imputa.value=listado_pcta[i][5];
				document.form1.chk_tp_imputa[0].checked=true;
				document.form1.chk_tp_imputa[1].checked=false;
				}

			
			if (pcta!=0){
		    document.form1.rif_sugerido.value=listado_pcta[i][13];
			document.form1.documento.value=pcta;
			}
		//	}
		   }
		if (pcta!=0){
			for(j=0;j<contador_partidas;j++){
			 var objeto = document.getElementById('monto_comp2'+j);
		     }
		}
		if(contador_partidas==0){
			document.getElementById('Categoria').style.display='';
			document.getElementById('Partidas').style.display='';
			document.form1.txt_nombre_accion.value='';
			document.form1.txt_nombre_imputa.value='';
			document.form1.chk_tp_imputa[0].checked=false;
			document.form1.chk_tp_imputa[1].checked=false;
			document.form1.txt_cod_imputa.value='';
		    document.form1.txt_cod_accion.value='';
		    document.form1.centro_gestor.value='';
		    document.form1.centro_costo.value='';
		    document.form1.documento.value='';
			document.form1.rif_sugerido.value='';
			document.form1.txt_cod_imputa2.value='';
			document.form1.txt_cod_accion2.value='';
	   
			var opt = document.form1.pcuenta_asunto.options;
			opt[0] = new Option("Seleccione...", '',"defaultSelected"); 
	}
}



function validar_pri(elem) {
	montos_pdas = new Array();
	for(i=0;i<elem;i++) {
		if((document.getElementById('txt_monto'+i).value=='') || (document.getElementById('txt_monto'+i).value==0)) 
			 {
			      montos_pdas[i]=1;
			  }else{
				    montos_pdas[i]=0;
				  }
	}	

	cont=0;
	for(i=0;i<elem;i++)
	{
	 if (montos_pdas[i]==1){
	  cont++;
	 }
	}
		  if (cont==elem){
		    alert('Revise los montos ingresados, debe especificar un monto en alguna partida');
		    return false;
		  }
		 
}

function add_opciones(valor) {   
	   var factor=valor ;
		nave=new String(navigator.appName);
		var pos_nave=nave.indexOf("Explorer");
		var index;
		var monto_inicial =document.form1.txt_monto_tot2.value

		if(document.form1.opt_depe.value=="") 		{	
			alert(" Debe seleccionar la Unidad o Dependencia.");
			return;
		}
		
		if(document.form1.txt_cod_imputa.value=="") 		{	
			alert(" Debe seleccionar el c\u00F3digo del Proyecto o Accion Centralizada.");
			return;
		}
	
		element_otros = document.getElementById('tbl_part').getElementsByTagName('tr').length;
		element_otros = element_otros -1;
		var tbody2 = document.getElementById('tbl_part');
										
		//se agregan ahora los elementos a la tabla inferior
		var tabla = document.getElementById('tbl_mod');
		element_todos = document.getElementById('tbl_mod').getElementsByTagName('tr').length-2;
		
		var tbody = document.getElementById('item');
		var id='item';
			
		var valido=validar_pri(element_otros);
		  
		if(valido==false){return;}
		
		if(element_otros<1) {
			alert("Este documento no posee partidas asociadas");
			return;
		}

		var pcta =document.form1.pcta_id.value;
		for(i=0;i<element_otros;i++) {

			//la partida que tenga algun valor <>0 si se pued agregar
			if (document.getElementById('txt_monto'+i).value!=0)
			{
			  if (pcta!=0){

			  var monto_pcta=parseFloat(MoneyToNumber(document.getElementById('monto_comp'+i).value));
			  var montosujeto=parseFloat(MoneyToNumber(document.getElementById('txt_monto'+i).value));
			  
			  if (montosujeto>monto_pcta){
				  alert("El monto introducido, no puede ser superior al monto disponible del punto de cuenta");
				  document.getElementById('txt_monto'+i).focus();
				  return;
			  }	
			}
	
			var registro = new Array(7);  	
			registro[4]=document.getElementById('txt_codigo'+i).value;
			registro[5]=document.getElementById('txt_den'+i).value;
			
			var row = document.createElement("tr")

			//Verificamos si esta ya registrada la partida
			for(l=0;l<partidas.length;l++)
			{
			 if ((partidas[l][4]==registro[4]) && (partidas[l][1]==registro[2]) && (partidas[l][2]==registro[1]))  
			 {
				alert("Partida ya seleccionada...");
				return;
			 }
			}
		    j=partidas.length;
			
		   //LOS RADIO BUTTONS
			var td1 = document.createElement("td");
			td1.setAttribute("align","Center");
			td1.className = 'normalNegro';
			//creamos una radio button
			var name="rb_ac_proy"+j;
			if(pos_nave>0) {
				 var rad_1 = document.createElement('<input type="radio" name="'+name+'">'); 
			}
			else { 
				var rad_1 = document.createElement('INPUT');
				rad_1.type="radio";
				rad_1.name=name; 
			}
					rad_1.setAttribute("id",name);
					rad_1.setAttribute("disabled","true");
			  
					if(document.form1.chk_tp_imputa[0].checked==true) {
						registro[0]=1;
						rad_1.setAttribute("value",1);
						rad_1_text = document.createTextNode('PR');
						rad_1.defaultChecked = true;
					}
					else {
							registro[0]=0;		    
							rad_1.setAttribute("value",0);
							rad_1_text = document.createTextNode('AC');
							rad_1.defaultChecked = true
						}
				
			  td1.appendChild(rad_1);			
			  td1.appendChild(rad_1_text);
				 //TIPO IMPUTACION: PROYECTO O ACC
			  var td7 = document.createElement("td");
			  td7.setAttribute("align","Center");
			  td7.className = 'normalNegro';
			  //creamos una radio button
			  var txt_tipo_p_ac = document.createElement("INPUT");
			  txt_tipo_p_ac.setAttribute("type","hidden");
			  name="txt_tipo_p_ac"+j;
			  txt_tipo_p_ac.setAttribute("name",name);
			  txt_tipo_p_ac.readOnly=true; 
			  txt_tipo_p_ac.value=registro[0];
				 
			  txt_tipo_p_ac.size='6'; 
			  txt_tipo_p_ac.className='normalNegro';
			  td7.appendChild(txt_tipo_p_ac);	 
			  row.appendChild(td7);	 

			 //CODIGO DEL PROYECTO O ACCION
			  var td2 = document.createElement("td");
			  td2.setAttribute("align","Center");
			  td2.className = 'normalNegro';
			  //creamos una radio button
			  var txt_id_p_ac = document.createElement("INPUT");
			  txt_id_p_ac.setAttribute("type","text");
			  name="txt_id_p_ac"+j;
			  txt_id_p_ac.setAttribute("name",name);
			  txt_id_p_ac.readOnly=true; 
			  registro[1]=document.form1.txt_cod_imputa.value;
			  txt_id_p_ac.value=registro[1];
			  txt_id_p_ac.size='6'; 
			  txt_id_p_ac.className='normalNegro';
			  td2.appendChild(txt_id_p_ac);
			  
			  //CODIGO DE LA ACCION ESPECIFICA
			  var td3 = document.createElement("td");
			  td3.setAttribute("align","Center");
			  td3.className = 'normalNegro';
			  //creamos una radio button
			  var txt_id_acesp = document.createElement("INPUT");
			  txt_id_acesp.setAttribute("type","text");
			  name="txt_id_acesp"+j;
			  txt_id_acesp.setAttribute("name",name);
			  txt_id_acesp.setAttribute("readOnly","true"); 
			  registro[2]=document.form1.txt_cod_accion.value;
			  txt_id_acesp.value=registro[2];	 
			  txt_id_acesp.size='8'; 
			  txt_id_acesp.className='normalNegro';
			  td3.appendChild(txt_id_acesp);
			  
			  //CODIGO DE LA DEPENDENCIA
			  var td4 = document.createElement("td");
			  td4.setAttribute("align","Center");
			  td4.className = 'normalNegro';
			  //creamos una radio button
			  var txt_id_depe = document.createElement("INPUT");
			  txt_id_depe.setAttribute("type","text");
			  name="txt_id_depe"+j;
			  txt_id_depe.setAttribute("name",name);
			  txt_id_depe.setAttribute("readOnly","true");
			  registro[3]=document.form1.opt_depe.value;
			  txt_id_depe.value=registro[3];	 
			  txt_id_depe.size='8'; 
			  txt_id_depe.className='normalNegro';
			  td4.appendChild(txt_id_depe);
			  
			  //CODIGO DE LA PARTIDA
			  var td5 = document.createElement("td");
			  td5.setAttribute("align","Center");
			  td5.className = 'normalNegro';
			  //creamos una radio button
			  var txt_id_pda = document.createElement("INPUT");
			  txt_id_pda.setAttribute("type","text");
			  txt_id_pda.setAttribute("readOnly","true");
			  name="txt_id_pda"+j;
			  txt_id_pda.setAttribute("name",name);
			  txt_id_pda.value=registro[4];	 
			  txt_id_pda.size='15'; 
			  txt_id_pda.className='normalNegro';
			  td5.appendChild(txt_id_pda);

			  //DENOMINACION
			  var td6 = document.createElement("td");
			  td6.setAttribute("align","Center");
			  td6.className = 'normalNegro';
			  //creamos una radio button
			  var txt_den_pda = document.createElement("INPUT");
			  txt_den_pda.setAttribute("type","text");
			  name="txt_den_pda"+j;
			  txt_den_pda.setAttribute("readOnly","true"); 
			  txt_den_pda.setAttribute("name",name);
			  txt_den_pda.value=registro[5];	 
			  txt_den_pda.size='30'; 
			  txt_den_pda.className='normalNegro';
			  td6.appendChild(txt_den_pda);

			  //MONTO
			  var td8 = document.createElement("td");
			  td8.setAttribute("align","Center");
			  td8.className = 'normalNegro';
			  //creamos una radio button
			  var txt_monto = document.createElement("INPUT");
			  txt_monto.setAttribute("type","text"); 
			  name="txt_monto_pda"+j;
			  txt_monto.setAttribute("name",name);
			  txt_monto.align='right';
			  txt_monto.setAttribute("readOnly","true");
			  registro[6]=document.getElementById('txt_monto'+i).value;
			  var mon=MoneyToNumber(registro[6]);
              txt_monto.value=mon;	 
			  txt_monto.size='10'; 
			  txt_monto.className='normalNegro';
			  td8.appendChild(txt_monto);
			 
			
			   monto_tot[monto_tot.length]= mon;
			   monto_inicial=parseFloat(monto_inicial) + parseFloat(mon);
			
			  
			  //OPCION DE ELIMINAR
			  var td10 = document.createElement("td");				
			  td10.setAttribute("align","Center");
			  td10.className = 'normal';
			  editLink = document.createElement("a");
			  linkText = document.createTextNode("Eliminar");
			  editLink.setAttribute("href", "javascript:elimina_pda('"+(j+1)+"')");
			  editLink.appendChild(linkText);
			  td10.appendChild (editLink);

			  
			  row.appendChild(td1);
			  row.appendChild(td2);
			  row.appendChild(td3); 
			  row.appendChild(td4);
			  row.appendChild(td5);
			  row.appendChild(td6);
			  
			  row.appendChild(td8);
			  row.appendChild(td10);
			  row.appendChild(td7);  
			  tbody.appendChild(row); 	
			  
			  partidas[partidas.length]=registro;
			  
			
			  arreglo[arreglo.length]=registro[4];
			  document.form1.hid_partida_actual.value=arreglo;
			  document.getElementById('txt_monto'+i).value=0.0;
			}
           }

			element_mod = document.getElementById('tbl_mod').getElementsByTagName('tr').length;
			document.getElementById('hid_largo').value=element_mod-factor;

	document.form1.txt_monto_tot.value=monto_inicial;
	document.form1.txt_monto_tot2.value=monto_inicial;
	ver_monto_letra(monto_inicial, 'txt_monto_letras','');	
	}


function elimina_pda(tipo) {  

	nave=new String(navigator.appName);
	var pos_nave=nave.indexOf("Explorer");
	var monto_inicial =document.form1.txt_monto_tot2.value;

	var tabla = document.getElementById('tbl_mod');
	var tbody = document.getElementById('item');
	monto_inicial=redondear_dos_decimal(parseFloat(monto_inicial) - parseFloat(monto_tot[tipo-1]));
    if (monto_inicial<0){
    	document.form1.txt_monto_tot.value=0;
        }else{
        	document.form1.txt_monto_tot.value=monto_inicial;
        }
	document.form1.txt_monto_tot.value=monto_inicial;
	document.form1.txt_monto_tot2.value=monto_inicial;
	ver_monto_letra(monto_inicial, 'txt_monto_letras','');	
      	
	for(i=0;i<partidas.length;i++) 	{
	 tabla.deleteRow(1);
	}

	for(i=tipo;i<partidas.length;i++) {
		partidas[i-1]=partidas[i];
		arreglo[i-1]=partidas[i][3];
		monto_tot[i-1]=monto_tot[i];
	}
	monto_tot[partidas.length-1]=0;
	monto_tot.pop();
	partidas.pop(); 
	arreglo.pop();
	document.form1.hid_partida_actual.value=arreglo;
		
	nave=new String(navigator.appName);
	var pos_nave=nave.indexOf("Explorer");
	
	document.getElementById('hid_largo').value=partidas.length;
	//agrega los elementos
	for(i=0;i<partidas.length;i++) 	{
		var row = document.createElement("tr")
		//LOS RADIO BUTTONS
		var td1 = document.createElement("td");
		td1.setAttribute("align","Center");
		td1.className = 'normalNegro';
		//creamos una radio button
		var name="rb_ac_proy"+i;
		if(pos_nave>0) 	{
			 var rad_1 = document.createElement('<input type="radio" name="'+name+'">'); 
		}
		else { 
				var rad_1 = document.createElement('INPUT');
				rad_1.type="radio";
				rad_1.name=name; 
		}
		if(document.form1.chk_tp_imputa[0].checked==true)
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
			
			rad_1.setAttribute("id",name);
			rad_1.setAttribute("disabled","true");
			td1.appendChild(rad_1);			
			td1.appendChild(rad_1_text);


			 //TIPO IMPUTACION: PROYECTO O ACC
			  var td7 = document.createElement("td");
			  td7.setAttribute("align","Center");
			  td7.className = 'titularMedio';
			  //creamos una radio button
			  var txt_tipo_p_ac = document.createElement("INPUT");
			  txt_tipo_p_ac.setAttribute("type","hidden");
			  name="txt_tipo_p_ac"+i;
			  txt_tipo_p_ac.setAttribute("name",name);
			  txt_tipo_p_ac.readOnly=true; 
			  txt_tipo_p_ac.value=partidas[i][0];
			  txt_tipo_p_ac.size='6'; 
			  txt_tipo_p_ac.className='normalNegro';
			  td7.appendChild(txt_tipo_p_ac);	 

			
			  
		 //CODIGO DEL PROYECTO O ACCION
		  var td2 = document.createElement("td");
		  td2.setAttribute("align","Center");
		  td2.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_p_ac = document.createElement("INPUT");
		  txt_id_p_ac.setAttribute("type","text");
		  name="txt_id_p_ac"+i;
		  txt_id_p_ac.setAttribute("name",name);
		  txt_id_p_ac.setAttribute("readonly","true"); 
		  txt_id_p_ac.value=partidas[i][1];	 
		  txt_id_p_ac.size='6'; 
		  txt_id_p_ac.className='normalNegro';
		  td2.appendChild(txt_id_p_ac);
		  
		  //CODIGO DE LA ACCION ESPECIFICA
		  var td3 = document.createElement("td");
		  td3.setAttribute("align","Center");
		  td3.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_acesp = document.createElement("INPUT");
		  txt_id_acesp.setAttribute("type","text");
		  name="txt_id_acesp"+i;
		  txt_id_acesp.setAttribute("name",name); 
		  txt_id_acesp.setAttribute("readonly","true"); 
		  txt_id_acesp.value=partidas[i][2];	 
		  txt_id_acesp.size='8'; 
		  txt_id_acesp.className='normalNegro';
		  td3.appendChild(txt_id_acesp);
		  
		   //CODIGO DE LA DEPENDENCIA
		  var td4 = document.createElement("td");
		  td4.setAttribute("align","Center");
		  td4.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_depe = document.createElement("INPUT");
		  txt_id_depe.setAttribute("type","text");
		  txt_id_depe.setAttribute("readonly","true");
		  name="txt_id_depe"+i;
		  txt_id_depe.setAttribute("name",name); 
		  txt_id_depe.value=partidas[i][3];	 
		  txt_id_depe.size='8'; 
		  txt_id_depe.className='normalNegro';
		  td4.appendChild(txt_id_depe);
				
		  //CODIGO DE LA PARTIDA
		  var td5 = document.createElement("td");
		  td5.setAttribute("align","Center");
		  td5.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_pda = document.createElement("INPUT");
		  txt_id_pda.setAttribute("type","text");
		  txt_id_pda.setAttribute("readonly","true");
		  name="txt_id_pda"+i;
		  txt_id_pda.setAttribute("name",name);
		  txt_id_pda.value=partidas[i][4];	 
		  txt_id_pda.size='15'; 
		  txt_id_pda.className='normalNegro';
		  td5.appendChild(txt_id_pda);
		  
		  //DENOMINACION
		  var td6 = document.createElement("td");
		  td6.setAttribute("align","Center");
		  td6.className = 'titularMedio';
		  //creamos una radio button
		  var txt_den_pda = document.createElement("INPUT");
		  txt_den_pda.setAttribute("type","text");
		  txt_den_pda.setAttribute("readonly","true");
		  name="txt_den_pda"+i;
		  txt_den_pda.setAttribute("name",name);
		  txt_den_pda.value=partidas[i][5];	 
		  txt_den_pda.size='30'; 
		  txt_den_pda.className='normalNegro';
		  td6.appendChild(txt_den_pda);
		  
		  //MONTO
		  var td8 = document.createElement("td");
		  td8.setAttribute("align","Center");
		  td8.className = 'titularMedio';
		  //creamos una radio button
		  var txt_monto = document.createElement("INPUT");
		  txt_monto.setAttribute("type","text");
		  name="txt_monto_pda"+i;
		  txt_monto.setAttribute("name",name);
		  txt_monto.setAttribute("readonly","true");
		  var mon=MoneyToNumber(partidas[i][6]);
		  txt_monto.value=mon;	 
		  txt_monto.size='10'; 
		  txt_monto.className='normalNegro';
		  td8.appendChild(txt_monto);	
		  
		  monto_total[monto_total.length]=mon;
		
		  //OPCION DE ELIMINAR
		  var td10 = document.createElement("td");				
		  td10.setAttribute("align","Center");
		  td10.className = 'normal';
		  editLink = document.createElement("a");
		  linkText = document.createTextNode("Eliminar");
		  editLink.setAttribute("href", "javascript:elimina_pda('"+(i+1)+"')");
		  editLink.appendChild(linkText);
		  td10.appendChild (editLink);

		
		  row.appendChild(td1); 
		  row.appendChild(td2);
		  row.appendChild(td3); 
		  row.appendChild(td4);
		  row.appendChild(td5);
		  row.appendChild(td6);
		  row.appendChild(td8);
		  row.appendChild(td10);
		  row.appendChild(td7);
		  tbody.appendChild(row); 	
		}
		
		mo=0;
		me=0;

		if(monto_tot.length==0){document.form1.txt_monto_tot.value=0;}

		mo= monto_inicial;
		if (partidas.length==0) {
		document.form1.hid_monto_tot.value=0;
		document.form1.txt_monto_tot.value=0;
		diner=0;
		monto_tot=new Array();
		monto_tot_exento=new Array();
		}
		else {
		document.form1.hid_monto_tot.value=mo;
		document.form1.txt_monto_subtotal.value=mo;
		diner= number_format(mo,2,'.','');
		}
		
		monto_total=new Array();
		diner=parseFloat(diner);
		ver_monto_letra(diner, 'txt_monto_letras','');
		ver_monto_letra(diner,'hid_monto_letras','');
		document.form1.txt_monto_tot.value=monto_inicial;
		document.form1.txt_monto_tot2.value=monto_inicial;
		ver_monto_letra(monto_inicial, 'txt_monto_letras','');	
}

function inicial() {

	nave=new String(navigator.appName);
    var pos_nave=nave.indexOf("Explorer");
    var tbody2=document.getElementById('ar_body');
	
	for (i=0; i<partidas_pcta.length; i++){
		
	
		 var row1 = document.createElement("tr")
     	  //CODIGO DE LA PARTIDA
		  var td01 = document.createElement("td");
		  td01.setAttribute("align","Center");
		  td01.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_pda = document.createElement("INPUT");
		  
		  txt_id_pda.setAttribute("type","text");
		  txt_id_pda.setAttribute("readonly","true");
		  name="txt_codigo"+i;
	      txt_id_pda.setAttribute("name",name);
	      txt_id_pda.setAttribute("id",name);	   
		  txt_id_pda.value=partidas_pcta[i][0];	 
		  txt_id_pda.size='15'; 
		  txt_id_pda.className='normalNegro';
		  td01.appendChild(txt_id_pda);

		  //DENOMINACION
		  var td02 = document.createElement("td");
		  td02.setAttribute("align","Center");
		  td02.className = 'titularMedio';
		  //creamos una radio button
		  var txt_den_pda = document.createElement("INPUT");
		  txt_den_pda.setAttribute("type","text");
		  txt_den_pda.setAttribute("readonly","true");
		  name="txt_den"+i;
	      txt_den_pda.setAttribute("name",name);
	      txt_den_pda.setAttribute("id",name);	  
		  txt_den_pda.value=partidas_pcta[i][2];	 
		  txt_den_pda.size='20'; 
		  txt_den_pda.className='normalNegro';
		  td02.appendChild(txt_den_pda);

		  //MONTO OCULTO
		  var td03 = document.createElement("td");
		  td03.setAttribute("align","Center");
		  td03.className = 'titularMedio';
		  //creprincipal.phpamos una radio button
		  var txt_monto = document.createElement("INPUT");
		  txt_monto.setAttribute("type","hidden");
		  name="monto_comp"+i;
	      txt_monto.setAttribute("name",name);//totalizar()
	      txt_monto.setAttribute("Id",name);
		  txt_monto.setAttribute("readonly","true");
	      txt_monto.setAttribute("onChange","javascript:totalizar()");
		  txt_monto.value=partidas_pcta[i][1];	 
		  txt_monto.size='1'; 
		  txt_monto.className='normalNegro';
		  td03.appendChild(txt_monto);	
		  
		  //MONTO MOSTRAR
		  var td031 = document.createElement("td");
		  td031.setAttribute("align","right");
		  td031.className = 'titularMedio';
		  
		  //creprincipal.phpamos una radio button
		  var txt_monto1 = document.createElement("INPUT");
		  txt_monto1.setAttribute("type","text");
		  name="monto_comp2"+i;
	      txt_monto1.setAttribute("name",name);//totalizar()
	      txt_monto1.setAttribute("Id",name);
	      txt_monto1.setAttribute("align","right");
		  txt_monto1.setAttribute("readonly","true");
	      txt_monto1.setAttribute("onChange","javascript:totalizar()");
	      txt_monto1.value=partidas_pcta[i][1];	
		  txt_monto1.size='15'; 
		  
		  txt_monto1.className='normalNegro';
		  td031.appendChild(txt_monto1);	

		  //MONTO COMPROMISO
		  var td04 = document.createElement("td");
		  td04.setAttribute("align","right");
		  td04.className = 'titularMedio';
		  name="txt_monto"+i;
		  var monto_comp = document.createElement("INPUT");
		  monto_comp.setAttribute("type","text");
		  monto_comp.setAttribute("name",name);
		  monto_comp.setAttribute("Id",name);
		  monto_comp.setAttribute("onkeypress","return inputFloat(event,true)");
		  monto_comp.value='0.0';
		  monto_comp.size='10';
		  monto_comp.className='normalNegro';
		  td04.appendChild(monto_comp);
		  
		  row1.appendChild(td01);
	      row1.appendChild(td02);
		
		  row1.appendChild(td031);
		  row1.appendChild(td04);
		  row1.appendChild(td03);
	      tbody2.appendChild(row1); 
	}
	
	for (i=0; i<partidas_pcta.length; i++){
		 var objeto = document.getElementById('monto_comp2'+i);
		 FormatCurrency(objeto);
	}
    var tabla = document.getElementById('tbl_mod');
    var tbody = document.getElementById('item');
    var monto_inicial=0.0;
    document.getElementById('hid_largo').value=partidas.length;
    
    document.form1.hid_partida_actual.value=arreglo;  
    //agrega los elementos
	for(i=0;i<partidas.length;i++) {

	    var row = document.createElement("tr")
		  //LOS RADIO BUTTONS
		  var td1 = document.createElement("td");
		  td1.setAttribute("align","Center");
		  td1.className = 'normalNegro';
		  //creamos una radio button
		  var name="rb_ac_proy"+i;
		  if(pos_nave>0){
		    var rad_1 = document.createElement('<input type="radio" name="'+name+'">'); }
		  else{ 
		    var rad_1 = document.createElement('INPUT');
		    rad_1.type="radio";
	            rad_1.name=name; }
	
		    if(partidas[i][0]==1){
		       rad_1.setAttribute("value",1);
			   rad_1_text = document.createTextNode('PR');
			   rad_1.defaultChecked = true
		    }
		    else{		    
		       rad_1.setAttribute("value",0);
			   rad_1_text = document.createTextNode('AC');
			   rad_1.defaultChecked = true; }
		  	
		    rad_1.setAttribute("id",name);
	            rad_1.setAttribute("disabled","true");
		  
		    td1.appendChild(rad_1);			
		    td1.appendChild(rad_1_text);

			 //TIPO IMPUTACION: PROYECTO O ACC
			  var td7 = document.createElement("td");
			  td7.setAttribute("align","Center");
			  td7.className = 'titularMedio';
			  //creamos una radio button
			  var txt_tipo_p_ac = document.createElement("INPUT");
			  txt_tipo_p_ac.setAttribute("type","hidden");
			  name="txt_tipo_p_ac"+i;
			  txt_tipo_p_ac.setAttribute("name",name);
			  txt_tipo_p_ac.readOnly=true; 
			  txt_tipo_p_ac.value=partidas[i][0];
			  txt_tipo_p_ac.size='15'; 
			  txt_tipo_p_ac.className='normalNegro';
			  td7.appendChild(txt_tipo_p_ac);	
			  
		 //CODIGO DEL PROYECTO O ACCION
		  var td2 = document.createElement("td");
		  td2.setAttribute("align","Center");
		  td2.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_p_ac = document.createElement("INPUT");
		  txt_id_p_ac.setAttribute("type","text");
		  name="txt_id_p_ac"+i;
	      txt_id_p_ac.setAttribute("name",name);
		  txt_id_p_ac.setAttribute("readonly","true"); 
		  txt_id_p_ac.value=partidas[i][1];	 
		  txt_id_p_ac.size='6'; 
		  txt_id_p_ac.className='normalNegro';
		  td2.appendChild(txt_id_p_ac);
		  
		  //CODIGO DE LA ACCION ESPECIFICA
		  var td3 = document.createElement("td");
		  td3.setAttribute("align","Center");
		  td3.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_acesp = document.createElement("INPUT");
		  txt_id_acesp.setAttribute("type","text");
		  name="txt_id_acesp"+i;
	      txt_id_acesp.setAttribute("name",name); 
		  txt_id_acesp.setAttribute("readonly","true"); 
		  txt_id_acesp.value=partidas[i][2];	 
		  txt_id_acesp.size='8'; 
		  txt_id_acesp.className='normalNegro';
		  td3.appendChild(txt_id_acesp);
		  
		  //CODIGO DE LA DEPENDENCIA
		  var td4 = document.createElement("td");
		  td4.setAttribute("align","Center");
		  td4.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_depe = document.createElement("INPUT");
		  txt_id_depe.setAttribute("type","text");
		  txt_id_depe.setAttribute("readonly","true");
		  name="txt_id_depe"+i;
	      txt_id_depe.setAttribute("name",name); 
		  txt_id_depe.value=partidas[i][3];	 
		  txt_id_depe.size='8'; 
		  txt_id_depe.className='normalNegro';
		  td4.appendChild(txt_id_depe);
		  	    
		  //CODIGO DE LA PARTIDA
		  var td5 = document.createElement("td");
		  td5.setAttribute("align","Center");
		  td5.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_pda = document.createElement("INPUT");
		  txt_id_pda.setAttribute("type","text");
		  txt_id_pda.setAttribute("readonly","true");
		  name="txt_id_pda"+i;
	      txt_id_pda.setAttribute("name",name);
		  txt_id_pda.value=partidas[i][4];	 
		  txt_id_pda.size='15'; 
		  txt_id_pda.className='normalNegro';
		  td5.appendChild(txt_id_pda);
		  
		  //DENOMINACION
		  var td6 = document.createElement("td");
		  td6.setAttribute("align","Center");
		  td6.className = 'titularMedio';
		  //creamos una radio button
		  var txt_den_pda = document.createElement("INPUT");
		  txt_den_pda.setAttribute("type","text");
		  txt_den_pda.setAttribute("readonly","true");
		  name="txt_den_pda"+i;
	      txt_den_pda.setAttribute("name",name);
		  txt_den_pda.value=partidas[i][5];	 
		  txt_den_pda.size='30'; 
		  txt_den_pda.className='normalNegro';
		  td6.appendChild(txt_den_pda);
		  
		  //MONTO
		  var td8 = document.createElement("td");
		  td8.setAttribute("align","Center");
		  td8.className = 'titularMedio';
		  //creprincipal.phpamos una radio button
		  var txt_monto = document.createElement("INPUT");
		  txt_monto.setAttribute("type","text");
		  name="txt_monto_pda"+i;
	      txt_monto.setAttribute("name",name);//totalizar()
	      txt_monto.setAttribute("Id",name);
		  txt_monto.setAttribute("readonly","true");
	      txt_monto.setAttribute("onChange","javascript:totalizar()");
		  txt_monto.value=partidas[i][6];	 
		  txt_monto.size='10'; 
		  txt_monto.className='normalNegro';
		  td8.appendChild(txt_monto);	
		  monto_inicial=parseFloat(monto_inicial) + parseFloat(txt_monto.value);
		  monto_tot[monto_tot.length]= partidas[i][6];
		 
			  //OPCION DE ELIMINAR
			  var td10 = document.createElement("td");				
			  td10.setAttribute("align","Center");
			  td10.className = 'normal';
			  editLink = document.createElement("a");
			  linkText = document.createTextNode("Eliminar");
			  editLink.setAttribute("href", "javascript:elimina_pda('"+(i+1)+"')");
			  editLink.appendChild(linkText);
			  td10.appendChild (editLink);

		  row.appendChild(td1); 
		  row.appendChild(td2);
		  row.appendChild(td3);
		  row.appendChild(td4); 
		  row.appendChild(td5);
		  row.appendChild(td6);
		  row.appendChild(td8);
	      row.appendChild(td10);
		  row.appendChild(td7);
	      tbody.appendChild(row); 	
        }
	var xx1=number_format(monto_inicial,2,'.',','); 
	document.form1.txt_monto_tot.value=monto_inicial;
	document.form1.txt_monto_tot2.value=monto_inicial;
	ver_monto_letra(monto_inicial, 'txt_monto_letras','');	
	} 


function revisar() {
	if(document.form1.fecha.value=="") {	
		alert(" Debe seleccionar la fecha real del compromiso");
		return;
	}

	if(document.form1.opt_depe.value=="")	{
		alert("Debe indicar la gerencia solicitante");
		document.form1.opt_depe.focus();
		return;
	}

	if(document.form1.pcuenta_asunto.value=="")	{
		alert("Debe seleccionar el asunto del compromiso");
		document.form1.pcuenta_asunto.focus();
		return;
	}


	if(document.form1.tipo_act.value=="")	{
		alert("Debe indicar el tipo de actividad asociada");
		document.form1.tipo_act.focus();
		return;
	}

	if(document.form1.tipo_evento.value=="")	{
		alert("Debe indicar el tipo de evento asociado");
		document.form1.tipo_evento.focus();
		return;
	}

	if((document.form1.tipo_evento.value!=0) && (document.form1.tipo_evento.value!=11) &&
	   (document.form1.tipo_evento.value!=20) && (document.form1.tipo_evento.value!=21) &&
	   (document.form1.tipo_evento.value!=22) && (document.form1.tipo_evento.value!=23) &&
	   ((document.form1.fecha_i.value=="")||(document.form1.fecha_f.value==""))	){
		alert("Debe indicar la duraci\u00F3n del evento");
		document.form1.fecha_i.focus();
		return;
	}
	
	if(document.form1.documento.value=="")	{
		alert("Debe indicar el n\u00FAmero del documento asociado");
		document.form1.documento.focus();
		return;
	}

	if(document.form1.control_interno.value=="")	{
		alert("Debe especificar el control interno");
		document.form1.control_interno.focus();
		return;
	}
		
	 descripcionlegth = $('#pcuenta_descripcion').elrte('val').length;

		if(descripcionlegth < 5 )
			
		{  	
			alert("Debe especificar la descripcion del Compromiso  y este s\u00f3lo puede tener caracteres alfanum\u00E9ricos");
			
			return;
		}

		if( (document.form1.txt_cod_imputa.value=="") && (document.form1.txt_cod_accion.value=="") ) {
		alert('Debe seleccionar la categor\u00EDa para la cual desea hacer la imputaci\u00F3n presupuestaria');
		return;
		}
		if((document.form1.hid_largo.value<1) || (partidas=="") )	{
		alert("Este documento no posee partidas asociadas");
		return;
		}
	
	if(confirm("Est\u00E1 seguro que desea generar este compromiso ?"))	{
		$('#pcuenta_descripcionVal').val($('#pcuenta_descripcion').elrte('val'));
		document.form1.submit()
	}
	
}		