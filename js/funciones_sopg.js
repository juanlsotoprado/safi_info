	function contador (campo, cuentacampo, limite) {
		if (campo.value.length > limite) campo.value = campo.value.substring(0, limite);
		else cuentacampo.value = limite - campo.value.length;
		}
	
    function validar_digito(objeto){
		var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		var checkStr = objeto.value;
		var allValid = true;
		for (i = 0;  i < checkStr.length;  i++){
			ch = checkStr.charAt(i);
			for (j = 0;  j < checkOK.length;  j++){
				if (ch == checkOK.charAt(j))
					break;
				if (j == checkOK.length){
					var cambio=checkStr.substring(-1,i) 
					objeto.value=cambio;
					alert("Escriba solo caracteres o n\u00FAmeros, adem\u00E1s no debe contener caracteres especiales");
					break;
				}
			}
		}
	}

	function redondear_dos_decimal(valor) {
		float_redondeado=Math.round(valor * 100) / 100;
		return float_redondeado;
	}

	function calcular_iva(){ 
		var ivaxx=0;
		var subtotalx=parseFloat(MoneyToNumber(document.form.txt_monto_subtotal.value));
		var exento=parseFloat(MoneyToNumber(document.form.txt_monto_subtotal_exento.value));

		var tt_neto=0;
		var porce=parseFloat(MoneyToNumber( document.form.opc_por_iva.value));

		var IVA=redondear_dos_decimal((subtotalx*porce)/100);
		document.form.txt_monto_iva_tt.value=IVA;
	
		var objeto = document.getElementById('txt_monto_iva_tt');
		FormatCurrency(objeto);
	
		var tt_total=(subtotalx+IVA+exento);
		var xx1=number_format(tt_total,2,'.',','); 

		document.form.txt_monto_tot.value=xx1;
		ver_monto_letra(tt_total, 'txt_monto_letras','');
		ver_monto_letra(tt_total,'hid_monto_letras','');
		return
	}    

function validar_digito(objeto){
		var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		var checkStr = objeto.value;
		var allValid = true;
		for (i = 0;  i < checkStr.length;  i++){
			ch = checkStr.charAt(i);
			for (j = 0;  j < checkOK.length;  j++){
				if (ch == checkOK.charAt(j))
					break;
				if (j == checkOK.length){
					var cambio=checkStr.substring(-1,i) 
					objeto.value=cambio;
					alert("Escriba solo caracteres o n\u00FAmeros, adem\u00E1s no debe contener caracteres especiales");
					break;
				}
			}
		}
	}
    
    
    var contador_partidas=0;

	function consulta_presupuesto(){
		nave=new String(navigator.appName);
		var pos_nave=nave.indexOf("Explorer");
		document.form.opt_bene[3].checked=false;
		document.getElementById('Categoria').style.display='none';
		document.getElementById('itemContainerTemp').style.display='none';
		document.getElementById('PartidasTemporales').style.display='';
		document.getElementById('PartidasAutomaticas').style.display='';
    	document.getElementById('Boton').style.display='';
    	var tbody = document.getElementById('item2');
	   //Lo primero que debe hacerse es borrar las partidas existentes
	    for(i=0;i<contador_partidas;i++){
			tbody.deleteRow(0);	
		}
		    contador_partidas=0;
		    var valor=document.form.comp_id.value;
			
			for(i=0;i<listado_comp.length;i++)
			{
				var fila = document.createElement("tr");
				var comp = listado_comp[i][0];
				if (valor==comp){
				contador_partidas++;

				//LOS RADIO BUTTONS
				var td1 = document.createElement("td");
				td1.setAttribute("align","Center");
				td1.className = 'normalNegro';
				//creamos una radio button
				var name="rb_ac_proy"+(contador_partidas-1);
				if(pos_nave>0){
					 var rad_1 = document.createElement('<input type="radio" name="'+name+'">'); 
				}else{ 
					var rad_1 = document.createElement('INPUT');
					rad_1.type="radio";
					rad_1.name=name; 
				}
				
				if(listado_comp[i][3]==1){
					rad_1.setAttribute("value",1);
					rad_1_text = document.createTextNode('PR');
					rad_1.defaultChecked = true;
				}else{		    
					rad_1.setAttribute("value",0);
					rad_1_text = document.createTextNode('AC');
					rad_1.defaultChecked = true;
				}
					
				rad_1.setAttribute("id",name);
				//rad_1.setAttribute("disabled","true");
				rad_1.setAttribute("readOnly","true");
				td1.appendChild(rad_1);			
				td1.appendChild(rad_1_text);
				
				//CODIGO DE LA PARTIDA
				var columna1 = document.createElement("td");
				columna1.setAttribute("align","center");
				columna1.className = 'titularMedio';
				name="txt_codigo"+(contador_partidas-1);
				var imp_1 = document.createElement("INPUT");
				imp_1.setAttribute("type","text");
				imp_1.setAttribute("readOnly","true");
				imp_1.setAttribute("name",name);//txt_id_pda
				imp_1.setAttribute("Id",name);
				imp_1.value=listado_comp[i][4];
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
				imp_2.setAttribute("value",listado_comp[i][7]);
				imp_2.setAttribute("id",name);
				imp_2.setAttribute("size","30");
				columna2.appendChild (imp_2);	


				//CODIGO DEL centro gestor
				var columna8 = document.createElement("td");
				columna8.setAttribute("align","center");
				columna8.className = 'titularMedio';
				name="txt_id_p_ac2"+(contador_partidas-1);
				var imp_8 = document.createElement("INPUT");
				imp_8.setAttribute("type","text");
				imp_8.setAttribute("readOnly","true");
				imp_8.setAttribute("name",name);//txt_id_pda
				imp_8.setAttribute("Id",name);
				imp_8.value=listado_comp[i][8];
				imp_8.size='15';
				imp_8.className='normalNegro';
				columna8.appendChild(imp_8);

				//CODIGO DEL PROYECTO O ACCION 
				var columna6 = document.createElement("td");
				columna6.setAttribute("align","center");
				columna6.className = 'titularMedio';
				name="txt_id_p_ac"+(contador_partidas-1);
				var imp_6 = document.createElement("INPUT");
				imp_6.setAttribute("type","hidden");
				imp_6.setAttribute("readOnly","true");
				imp_6.setAttribute("name",name);//txt_id_pda
				imp_6.setAttribute("Id",name);
				imp_6.value=listado_comp[i][2];
				imp_6.size='15';
				imp_6.className='normalNegro';
				columna6.appendChild(imp_6);


				//CODIGO DE centro costo
				var columna9 = document.createElement("td");
				columna9.setAttribute("align","center");
				columna9.className = 'titularMedio';
				name="txt_id_acesp2"+(contador_partidas-1);
				var imp_9 = document.createElement("INPUT");
				imp_9.setAttribute("type","text");
				imp_9.setAttribute("readOnly","true");
				imp_9.setAttribute("name",name);//txt_id_pda
				imp_9.setAttribute("Id",name);
				imp_9.value=listado_comp[i][9];
				imp_9.size='15';
				imp_9.className='normalNegro';
				columna9.appendChild(imp_9);

				
				//CODIGO DE LA ACCION ESPECIFICA
				var columna7 = document.createElement("td");
				columna7.setAttribute("align","center");
				columna7.className = 'titularMedio';
				name="txt_id_acesp"+(contador_partidas-1);
				var imp_7 = document.createElement("INPUT");
				imp_7.setAttribute("type","hidden");
				imp_7.setAttribute("readOnly","true");
				imp_7.setAttribute("name",name);//txt_id_pda
				imp_7.setAttribute("Id",name);
				imp_7.value=listado_comp[i][1];
				imp_7.size='15';
				imp_7.className='normalNegro';
				columna7.appendChild(imp_7);
				
				//MONTO COMPROMISO
				var columna5 = document.createElement("td");
				columna5.setAttribute("align","right");
				columna5.className = 'titularMedio';
				name="monto_comp"+(contador_partidas-1);
				var imp_5 = document.createElement("INPUT");
				imp_5.setAttribute("type","text");
				imp_5.setAttribute("name",name);
				imp_5.setAttribute("Id",name);
				imp_5.setAttribute("readOnly","true");
				imp_5.setAttribute("value",listado_comp[i][10]);
				imp_5.size='10';
				imp_5.className='normalNegro';
				columna5.appendChild(imp_5);
				
				//MONTO SUJETO
				var columna3 = document.createElement("td");
				columna3.setAttribute("align","right");
				columna3.className = 'titularMedio';
				name="txt_monto"+(contador_partidas-1);
				var imp_3 = document.createElement("INPUT");
				imp_3.setAttribute("type","text");
				imp_3.setAttribute("name",name);
				imp_3.setAttribute("Id",name);
				imp_3.setAttribute("onkeypress","return inputFloat(event,true)");
				//imp_3.setAttribute("onKeyUp","FormatCurrency(this)");
				imp_3.value='0.0';
				imp_3.size='10';
				imp_3.className='normalNegro';
				columna3.appendChild(imp_3);
				
				//MONTO EXENTO
				var columna4 = document.createElement("td");
				columna4.setAttribute("align","right");
				columna4.className = 'titularMedio';
				name="txt_monto_exento"+(contador_partidas-1);
				var imp_4 = document.createElement("INPUT");
				imp_4.setAttribute("type","text");
				imp_4.setAttribute("name",name);
				imp_4.setAttribute("Id",name);
				imp_4.setAttribute("onkeypress","return inputFloat(event,true)");
				//imp_4.setAttribute("onKeyUp","FormatCurrency(this)");
				imp_4.value='0.0';
				imp_4.size='10';
				imp_4.className='normalNegro';
				columna4.appendChild(imp_4);

				fila.appendChild(td1); 
				fila.appendChild(columna1); 
				fila.appendChild(columna2);
				fila.appendChild(columna8);
				fila.appendChild(columna9);
				fila.appendChild(columna5);
				fila.appendChild(columna3);
				fila.appendChild(columna4);
				fila.appendChild(columna6);
				fila.appendChild(columna7);
				tbody.appendChild(fila); 
				
		        document.form.txt_cod_imputa.value=listado_comp[i][2];
			    document.form.txt_cod_accion.value=listado_comp[i][1];
			    document.form.centro_gestor.value=listado_comp[i][8];
			    document.form.centro_costo.value=listado_comp[i][9];
			    document.form.numero_reserva.value=listado_comp[i][11];
			    document.form.txt_cod_accion2.value=listado_comp[i][9];
			    document.form.txt_cod_imputa2.value=listado_comp[i][8];
				    
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
				}else{

					}
			   }
			if(contador_partidas==0){
				document.getElementById('Categoria').style.display='';
				document.getElementById('PartidasAutomaticas').style.display='none';
				document.getElementById('PartidasTemporales').style.display='';
				document.getElementById('Boton').style.display='none';
				document.form.txt_nombre_accion.value='';
				document.form.txt_nombre_imputa.value='';
				document.form.chk_tp_imputa[0].checked=false;
				document.form.chk_tp_imputa[1].checked=false;
				document.form.txt_cod_imputa.value='';
			    document.form.txt_cod_accion.value='';
			    document.form.centro_gestor.value='';
			    document.form.centro_costo.value='';
			    document.form.numero_reserva.value='0';
				document.form.txt_cod_imputa2.value='';
			    document.form.txt_cod_accion2.value='';
	
				
		}
		
		   
	}

	function elimina_pda(tipo){ 
		nave=new String(navigator.appName);
		var pos_nave=nave.indexOf("Explorer");
		
		var tabla = document.getElementById('tbl_mod');
		var tbody = document.getElementById('item');
		
		for(i=0;i<partidas.length;i++){
			tabla.deleteRow(1);
		}

		for(i=tipo;i<partidas.length;i++){
			partidas[i-1]=partidas[i];
			validar_compromiso[i-1]=validar_compromiso[i];
			arreglo[i-1]=partidas[i][3];
			monto_tot[i-1]=monto_tot[i];
			monto_tot_exento[i-1]=monto_tot_exento[i];
		}

		monto_tot[partidas.length-1]=0;
 	    monto_tot_exento[partidas.length-1]=0;
 	    partidas.pop(); 
		arreglo.pop();
		document.form.hid_partida_actual.value=arreglo;
		nave=new String(navigator.appName);
		var pos_nave=nave.indexOf("Explorer");
		
		document.getElementById('hid_largo').value=partidas.length;
		cg = trim(document.form.txt_cod_imputa2.value);
		cc = trim(document.form.txt_cod_accion2.value);
		//agrega los elementos
 		for(i=0;i<partidas.length;i++){
			var row = document.createElement("tr");
			//LOS RADIO BUTTONS
			var td1 = document.createElement("td");
			td1.setAttribute("align","Center");
			td1.setAttribute("colspan","2");
			td1.className = 'normalNegro';
			//creamos una radio button
			var name="rb_ac_proy"+i;
			if(pos_nave>0){
				 var rad_1 = document.createElement('<input type="radio" name="'+name+'">'); 
			}else{ 
				var rad_1 = document.createElement('INPUT');
				rad_1.type="radio";
				rad_1.name=name; 
			}
			  
			if(partidas[i][0]==1){
				rad_1.setAttribute("value",1);
				rad_1_text = document.createTextNode('PR');
				rad_1.defaultChecked = true;
			}else{		    
				rad_1.setAttribute("value",0);
				rad_1_text = document.createTextNode('AC');
				rad_1.defaultChecked = true;
			}
				
			rad_1.setAttribute("id",name);
			rad_1.setAttribute("readOnly","true");
			td1.appendChild(rad_1);			
			td1.appendChild(rad_1_text);

			  
			 //CODIGO DEL PROYECTO O ACCION
			  var td22 = document.createElement("td");
			  td22.setAttribute("align","Center");
			  td22.className = 'titularMedio';
			  //creamos una radio button
			  var txt_id_p_ac = document.createElement("INPUT");
			  txt_id_p_ac.setAttribute("type","text");
			  name="txt_id_p_ac2"+i;
		      txt_id_p_ac.setAttribute("name",name);
			  txt_id_p_ac.setAttribute("readonly","true"); 
			  txt_id_p_ac.value=cg;	 
			  txt_id_p_ac.size='15'; 
			  txt_id_p_ac.className='normalNegro';
			  td22.appendChild(txt_id_p_ac);
			
			//CODIGO DEL PROYECTO O ACCION OCULTO
			var td2 = document.createElement("td");
			td2.setAttribute("align","Center");
			td2.className = 'titularMedio';
			//creamos una radio button
			var txt_id_p_ac = document.createElement("INPUT");
			txt_id_p_ac.setAttribute("type","hidden");
			name="txt_id_p_ac"+i;
			txt_id_p_ac.setAttribute("name",name);
			txt_id_p_ac.setAttribute("readonly","true"); 
			txt_id_p_ac.value=partidas[i][1];	 
			txt_id_p_ac.size='8'; 
			txt_id_p_ac.className='normalNegro';
			td2.appendChild(txt_id_p_ac);

			  //CODIGO DE LA ACCION ESPECIFICA
			  var td33 = document.createElement("td");
			  td33.setAttribute("align","Center");
			  td33.className = 'titularMedio';
			  //creamos una radio button
			  var txt_id_acesp = document.createElement("INPUT");
			  txt_id_acesp.setAttribute("type","text");
			  name="txt_id_acesp2"+i;
		      txt_id_acesp.setAttribute("name",name); 
			  txt_id_acesp.setAttribute("readonly","true"); 
			  txt_id_acesp.value=cc;	 
			  txt_id_acesp.size='8'; 
			  txt_id_acesp.className='normalNegro';
			  td33.appendChild(txt_id_acesp);
			  
			//CODIGO DE LA ACCION ESPECIFICA OCULTO
			var td3 = document.createElement("td");
			td3.setAttribute("align","Center");
			td3.className = 'titularMedio';
			//creamos una radio button
			var txt_id_acesp = document.createElement("INPUT");
			txt_id_acesp.setAttribute("type","hidden");
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
			txt_den_pda.size='25'; 
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
			
			//MONTO EXENTO
			var td9 = document.createElement("td");
			td9.setAttribute("align","Center");
			td9.className = 'normalNegro';
		
			//creamos una radio button
			var txt_monto_exento = document.createElement("INPUT");
			txt_monto_exento.setAttribute("type","text");
			name="txt_monto_pda_exento"+i;
			txt_monto_exento.setAttribute("name",name);
			txt_monto_exento.setAttribute("readonly","true");
			var mon2=MoneyToNumber(partidas[i][7]);
			txt_monto_exento.value=mon2;	 
			txt_monto_exento.size='10'; 
			txt_monto_exento.className='normalNegro';
			td9.appendChild(txt_monto_exento);	
			/**************************************/
			monto_total_exento[monto_total_exento.length]=mon2;
			/***************************************/
		
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
			row.appendChild(td22);
			row.appendChild(td33); 
			row.appendChild(td4);
			row.appendChild(td5);
			row.appendChild(td6);
			row.appendChild(td8);
			row.appendChild(td9);
		    row.appendChild(td10);
			row.appendChild(td2);
			row.appendChild(td3);			
			tbody.appendChild(row); 	
		}
		/****************************************/
		mo=0;
		me=0;

		if(monto_tot.length==0){document.form.txt_monto_tot.value=0;}

		for(i=0;i<monto_total.length;i++){
			mo=parseFloat(mo) + parseFloat(monto_total[i]);
			document.form.txt_monto_tot.value=mo;
		}  

		for(i=0;i<monto_total_exento.length;i++){
			me=parseFloat(me) + parseFloat(monto_total_exento[i]);
			document.form.txt_monto_subtotal_exento.value=me;
		}  

		if (partidas.length==0){
			document.form.hid_monto_tot.value=0;
			document.form.txt_monto_subtotal.value=0;
			document.form.txt_monto_subtotal_exento.value=0;
			document.form.txt_monto_tot.value=0;
			diner=0;
			monto_tot=new Array();
			monto_tot_exento=new Array();
		}else{
			document.form.hid_monto_tot.value=mo;
			document.form.txt_monto_subtotal.value=document.form.txt_monto_tot.value;
			diner= number_format(mo,2,'.','');
		}
		calcular_iva();
		monto_total=new Array();
		monto_total_exento=new Array();
		diner=parseFloat(diner);
		ver_monto_letra(diner, 'txt_monto_letras','');
		ver_monto_letra(diner,'hid_monto_letras','');
	}
	


	function limpiarBeneficiario(tipo){
		if(tipo=='1'){
			document.getElementById("beneficiarioEmpleado").value="";
			document.getElementById("beneficiarioEmpleado").focus();
		}else if(tipo=='2'){
			document.getElementById("beneficiarioProveedor").value="";
			document.getElementById("beneficiarioEmpleado").focus();
		}else if(tipo=='3'){
			document.getElementById("beneficiarioOtro").value="";
			document.getElementById("beneficiarioOtro").focus();
		}
	}

	function estaEnBeneficiarios(tipo, cedula){
		if(tipo=='1'){
			for(j = 0; j < cedulasEmpleados.length; j++){
				if(cedula==cedulasEmpleados[j]){
					return nombresEmpleados[j];
				}
			}
		}else if(tipo=='2'){
			for(j = 0; j < cedulasProveedores.length; j++){
				if(cedula==cedulasProveedores[j]){
					return nombresProveedores[j];
				}
			}
		}else if(tipo=='3'){
			for(j = 0; j < cedulasOtros.length; j++){
				if(cedula==cedulasOtros[j]){
					return nombresOtros[j];
				}
			}
		}
		return "";
	}

	function estaEnBeneficiariosTemporales(cedula){
		for(j = 0; j < beneficiarios.length; j++){
			if(cedula==beneficiarios[j][0]){
				return true;
			}
		}
		return false;
	}
	
	function accionBeneficiario(id, tipo, cedula, nombre, edo, obs){
		if(id==0){
			var registro = new Array(6);			
			registro[0] = cedula;
			registro[1] = nombre;
			if(tipo=='1'){
				registro[2] = "Empleado";
			}else if(tipo=='2'){
				registro[2] = "Proveedor";
			}else if(tipo=='3'){
				registro[2] = "Otro";
			}
			registro[3] = tipo;
			registro[4]=edo;
			registro[5]=obs;
			beneficiarios[beneficiarios.length]=registro;
			
		}
		var tbody = document.getElementById('beneficiariosBody');
		var table = document.getElementById('beneficiariosTable');
		for(i=0;i<beneficiarios.length-1;i++){
			table.deleteRow(1);

			if (beneficiarios[i][4]==""){
			beneficiarios[i][4]=beneficiarios[i+1][4];
			beneficiarios[i][5]=beneficiarios[i+1][5];
			}
		}
		beneficiarios[beneficiarios.length-1][4]="";
		beneficiarios[beneficiarios.length-1][5]="";

		if(id!=0){
			table.deleteRow(1);
			for(i=id;i<beneficiarios.length;i++){
				beneficiarios[i-1]=beneficiarios[i];
			}
			beneficiarios.pop();
		}
		
		for(i=0;i<beneficiarios.length;i++){
	    	var row = document.createElement("tr");
			row.setAttribute("class","normalNegro");
	    	var td0=document.createElement("td");
			td0.setAttribute("align","justify");
			td0.appendChild(document.createTextNode(i+1));
	    	
			var td1=document.createElement("td");
			td1.setAttribute("align","justify");
			td1.appendChild(document.createTextNode(beneficiarios[i][0]));
			
			var td2=document.createElement("td");
			td2.setAttribute("align","justify");
			td2.appendChild(document.createTextNode(beneficiarios[i][1]));
			
			var td3=document.createElement("td");
			td3.setAttribute("align","left");
			td3.appendChild(document.createTextNode(beneficiarios[i][2]));

			var td5 = document.createElement("td");
			td5.setAttribute("align","Center");
			td5.className = 'titularMedio';
			var edo = document.createElement('select');
			name="estado"+i;
			edo.setAttribute("name",name);
			edo.setAttribute("id",name);

			for (h=0; h<listado_estados.length; h++){
			 opt=document.createElement('option');
			 opt.setAttribute("class","normalNegro");
			 opt.value=listado_estados[h][0];
			 nombre=listado_estados[h][1];
			 opt.innerHTML = nombre;
			 //(beneficiarios.length>1)&&
			 if ( (beneficiarios[i][4]==listado_estados[h][0])){
			 opt.setAttribute("selected", "selected")
			 }
			 edo.appendChild(opt); 
			}
			  td5.appendChild(edo);

			 var td6 = document.createElement("td");
			 td6.setAttribute("align","Center");
			 td6.className = 'titularMedio';
			 var obs_extra = document.createElement('INPUT');
			 obs_extra.setAttribute("type","text");
			 name="obs_extra"+i;
			 obs_extra.setAttribute("name",name);
			 obs_extra.setAttribute("id",name);

			// if (beneficiarios.length>1){
				// h=i+1;
			 obs_extra.value=beneficiarios[i][5];
			 //}
			 obs_extra.size='15';
			 obs_extra.className='normalNegro';
			 td6.appendChild(obs_extra);
			 
			
			var td4 = document.createElement("td");
			td4.setAttribute("align","center");
	        td4.className = 'link';
			editLink = document.createElement("a");
			linkText = document.createTextNode("Eliminar");
			editLink.setAttribute("href", "javascript:accionBeneficiario('"+(i+1)+"')");
			editLink.appendChild(linkText);
			td4.appendChild (editLink);

			row.appendChild(td0);
			row.appendChild(td1);
			row.appendChild(td2);
			row.appendChild(td3);
			row.appendChild(td6);
			row.appendChild(td5);
			row.appendChild(td4);
			tbody.appendChild(row);
		}
	}

	function obtenerPrimerBeneficiario(){
		if(beneficiarios.length>0){
			return beneficiarios[0][3];
		}
		return "";
	}

		function limpiarItem(){
			document.getElementById("itemCompletarTemp").value="";
			document.getElementById("itemCompletarTemp").focus();
			document.getElementById("sujeto_temp").value="0";
			document.getElementById("exento_temp").value="0";
			document.getElementById("itemCompletarTemp").value="";
		}

function estaEnItemsTemporales(idItem){
	
	for(j = 0; j < arreglo_partidas.length; j++){
		if(idItem==arreglo_partidas[j][4]){
			return true;
		}
	}
	return false;
}

function estaEnItems(idItem,arreglop){
	for(j = 0; j < arreglop.length; j++){
		if(idItem==arreglop[j]){
			return j;
		}
	}
	return -1;
}

function agregarItem(objeto,montos,montoe,arreglo_partidas,arreglo_cuentas){
	nave=new String(navigator.appName);
	var pos_nave=nave.indexOf("Explorer");
	if(document.form.txt_cod_imputa.value==""){	
		alert("Seleccione el c"+oACUTE+"digo del Proyecto o Acci"+oACUTE+"n Centralizada !.");
		return;
	}
	
	if(trim(objeto.value)==""){
		alert("Introduzca la cuenta contable o una palabra contenida en el nombre.");
		document.getElementById("itemCompletarTemp").focus();
	}else{
				tokens = objeto.value.split( ":" );
				if(tokens[0] && tokens[1]){
					idPartida = trim(tokens[0]);
					nombreItem = trim(tokens[1]);
					if (idPartida.substring(0,1)=="4")
						indiceIdItem = estaEnItems(idPartida,arreglo_partidas);
					else
						indiceIdItem = estaEnItems(idPartida,arreglo_cuentas);
										
					if(indiceIdItem>-1){
						var tbody = document.getElementById('item');
						idItem = idsPartidasItemsTemp[indiceIdItem];
						esta = estaEnItemsTemporales(idItem);
						if(esta==false){
							indiceGeneral = partidas.length;
							nombrePartida = nombresPartidasItems[indiceIdItem];
					
							monto_sujeto = (trim(montos.value));
							monto_exento = (trim(montoe.value));
							proyecto = trim(document.form.txt_cod_imputa.value);
							accion = trim(document.form.txt_cod_accion.value);
							cg = trim(document.form.txt_cod_imputa2.value);
							cc = trim(document.form.txt_cod_accion2.value);
							
							//Verificamos si esta ya registrada
							for(l=0;l<partidas.length;l++)
							{
							 if ((partidas[l][4]==idPartida) )
							 {
							//	alert("Partida ya seleccionada...");
								return;
							 }
							}
							
							if((montos.value=='') || (montos.value<=0)){
							       if((montoe.value=='') || (montoe.value<=0)){
										alert('Revise los montos ingresados.');
										return false;
									}}
							
							var registro = new Array(8);
							registro[1]=proyecto;
							registro[2]=accion;
							registro[4]=idPartida;
							registro[5]=nombrePartida;
							registro[6]=monto_sujeto;
							registro[7]=monto_exento;
						
							var fila = document.createElement("tr");

							//LOS RADIO BUTTONS
							var td1 = document.createElement("td");
							td1.setAttribute("align","Center");
							td1.setAttribute("colspan","2");
							td1.className = 'normalNegro';
		
							//creamos una radio button
							var name="rb_ac_proy"+indiceGeneral;
							if(pos_nave>0){
						 		var rad_1 = document.createElement('<input type="radio" name="'+name+'">'); 
							}else{
								var rad_1 = document.createElement('INPUT');
								rad_1.type="radio";
								rad_1.name=name;
							}
				 
							rad_1.setAttribute("id",name);
							rad_1.setAttribute("readOnly","true");
			  
							if(document.form.chk_tp_imputa[0].checked==true){
							registro[0]=1;
							rad_1.setAttribute("value",1);
							rad_1_text = document.createTextNode('PR');
							rad_1.defaultChecked = true;
							}else{
							registro[0]=0;		    
							rad_1.setAttribute("value",0);
							rad_1_text = document.createTextNode('AC');
							rad_1.defaultChecked = true
							}
		
							td1.appendChild(rad_1);			
							td1.appendChild(rad_1_text);

							//CODIGO DEL PROYECTO O ACCION
							var td22 = document.createElement("td");
							td22.setAttribute("align","Center");
							td22.className = 'titularMedio';
							//creamos una radio button
							var txt_id_p_ac = document.createElement("INPUT");
							txt_id_p_ac.setAttribute("type","text");
							name="txt_id_p_ac2"+indiceGeneral;
							txt_id_p_ac.setAttribute("name",name);
							txt_id_p_ac.readOnly=true; 
							//registro[1]=document.form.txt_cod_imputa.value;
							txt_id_p_ac.value=cg;
			 				txt_id_p_ac.size='15'; 
							txt_id_p_ac.className='normalNegro';
							td22.appendChild(txt_id_p_ac);
							 
							//CODIGO DEL PROYECTO O ACCION OCULTA
							var td2 = document.createElement("td");
							td2.setAttribute("align","Center");
							td2.className = 'titularMedio';
							//creamos una radio button
							var txt_id_p_ac = document.createElement("INPUT");
							txt_id_p_ac.setAttribute("type","hidden");
							name="txt_id_p_ac"+indiceGeneral;
							txt_id_p_ac.setAttribute("name",name);
							txt_id_p_ac.readOnly=true; 
							registro[1]=document.form.txt_cod_imputa.value;
							txt_id_p_ac.value=registro[1];
							txt_id_p_ac.size='8'; 
							txt_id_p_ac.className='normalNegro';
							td2.appendChild(txt_id_p_ac);

							//CODIGO DE LA ACCION ESPECIFICA
							var td33 = document.createElement("td");
							td33.setAttribute("align","Center");
							td33.className = 'titularMedio';
							//creamos una radio button
							var txt_id_acesp = document.createElement("INPUT");
							txt_id_acesp.setAttribute("type","text");
							name="txt_id_acesp2"+indiceGeneral;
							txt_id_acesp.setAttribute("name",name);
							txt_id_acesp.setAttribute("readOnly","true"); 
							//registro[2]=document.form.txt_cod_accion.value;
							txt_id_acesp.value=cc;	 
							txt_id_acesp.size='8'; 
							txt_id_acesp.className='normalNegro';
							td33.appendChild(txt_id_acesp);
							  
							//CODIGO DE LA ACCION ESPECIFICA OCULTA
							var td3 = document.createElement("td");
							td3.setAttribute("align","Center");
							td3.className = 'titularMedio';

							//creamos una radio button
							var txt_id_acesp = document.createElement("INPUT");
							txt_id_acesp.setAttribute("type","hidden");
							name="txt_id_acesp"+indiceGeneral;
							txt_id_acesp.setAttribute("name",name);
							txt_id_acesp.setAttribute("readOnly","true"); 
							registro[2]=document.form.txt_cod_accion.value;
							txt_id_acesp.value=registro[2];	 
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
							name="txt_id_depe"+indiceGeneral;
							txt_id_depe.setAttribute("name",name);
							txt_id_depe.setAttribute("readOnly","true");
							registro[3]=document.form.opt_depe.value;
							txt_id_depe.value=registro[3];	 
							txt_id_depe.size='8'; 
							txt_id_depe.className='normalNegro';
							td4.appendChild(txt_id_depe);
							
							//CODIGO DE LA PARTIDA
							var columna3 = document.createElement("td");
							columna3.setAttribute("align","center");
							columna3.className = 'titularMedio';
							var inputIdPartida = document.createElement("INPUT");
							inputIdPartida.setAttribute("type","text");
							inputIdPartida.setAttribute("readOnly","true");
							inputIdPartida.setAttribute("name","txt_id_pda"+indiceGeneral);
							inputIdPartida.value=registro[4];
							inputIdPartida.size='15';
							inputIdPartida.className='normalNegro';
							columna3.appendChild(inputIdPartida);
							
							//DENOMINACION DE LA PARTIDA
							var columna4 = document.createElement("td");
							columna4.setAttribute("align","center");
							columna4.className = 'titularMedio';
							var inputNombrePartida = document.createElement("INPUT");
							inputNombrePartida.setAttribute("type","text");
							inputNombrePartida.setAttribute("name","txt_den_pda"+indiceGeneral);
							inputNombrePartida.setAttribute("readOnly","true");
							inputNombrePartida.value=registro[5];
							inputNombrePartida.size='25';
							inputNombrePartida.className='normalNegro';
							columna4.appendChild(inputNombrePartida);
							
							//DESCRIPCION
							var columna5 = document.createElement("td");
							columna5.setAttribute("align","center");
							columna5.className = 'titularMedio';
							var inputEspecificaciones = document.createElement("INPUT");
							inputEspecificaciones.setAttribute("type","text");
							inputEspecificaciones.setAttribute("name","txt_monto_pda"+indiceGeneral);
							inputEspecificaciones.setAttribute("readOnly","true");
							inputEspecificaciones.value=registro[6];
							inputEspecificaciones.size='10';
							inputEspecificaciones.className='normalNegro';
							columna5.appendChild(inputEspecificaciones);
							
							//CANTIDAD
							var columna6 = document.createElement("td");
							columna6.setAttribute("align","center");
							columna6.className = 'titularMedio';
							var inputCantidad = document.createElement("INPUT");
							inputCantidad.setAttribute("type","text");
							inputCantidad.setAttribute("name","txt_monto_pda_exento"+indiceGeneral);
							inputCantidad.setAttribute("readOnly","true");
							inputCantidad.value=registro[7];
							inputCantidad.size='10';
							inputCantidad.className='normalNegro';
							columna6.appendChild(inputCantidad);
							
							monto_tot[monto_tot.length]= registro[6];
							monto_tot_exento[monto_tot_exento.length]= registro[7];

							
							//OPCION DE ELIMINAR
							var columna7 = document.createElement("td");
							columna7.setAttribute("align","center");
							columna7.className = 'normal';
							editLink = document.createElement("a");
							linkText = document.createTextNode("Eliminar");
							editLink.setAttribute("href", "javascript:elimina_pda('"+(indiceGeneral+1)+"')");
							editLink.appendChild(linkText);
							columna7.appendChild (editLink);

							fila.appendChild(td1); 
							fila.appendChild(td22);
							fila.appendChild(td33);
							fila.appendChild(td4);  
							fila.appendChild(columna3);
							fila.appendChild(columna4);
							fila.appendChild(columna5);
							fila.appendChild(columna6);
							fila.appendChild(columna7);
							fila.appendChild(td2);
							fila.appendChild(td3);
							tbody.appendChild(fila); 

							partidas[partidas.length]=registro;
						
							var temporal=registro[4];
							if ((temporal.substring(0,6)!='4.11.0') && (temporal.substring(0,1)=='4')) {
							
							 validar_compromiso[partidas.length-1]=1;
							 
							}else{
								validar_compromiso[partidas.length-1]=0;
								}

							document.getElementById('hid_largo').value=partidas.length;
							limpiarItem();
						}else{
							alert("La partida ya se ha agregado a la solicitud.");
							document.getElementById("itemCompletarTemp").value="";
							document.getElementById("sujeto_temp").value="0";
							document.getElementById("exento_temp").value="0";
							
							
						}
					}
					else{
						alert("La partida indicada no es v"+aACUTE+"lido");
					}
				}else{
					alert("Seleccione una partida");
				}
			}	
}
var _charmiles = ',';    //separador de miles
var _chardecimal = '.';    //separador de la parte decimal

function inputFloat(e,minus){

    var menos = minus || false;
    if(e==null){
        e=event;
    }
    if(e==null){
        e=window.event;
    }

    var tecla = (document.all) ? e.keyCode : e.which;
    //48=0,57=9, 45=menos

    if(tecla==0 && !document.all)return true;//solo FF en keypress de flechas
    if(tecla==8)return true;//backs
    if(tecla==_chardecimal.charCodeAt(0)) return true; //punto decimal
    if (tecla==45){
        if (!menos){
            return false;
        }
    }else if(tecla < 48 || tecla > 57){
        return false;
    }
    return true;
}



function validar_pri(elem)
{
	montos_pdas = new Array();
	for(i=0;i<elem;i++)
	{
		if( ((document.getElementById('txt_monto'+i).value=='') || (document.getElementById('txt_monto'+i).value<=0))
		&& ((document.getElementById('txt_monto_exento'+i).value=='') || (document.getElementById('txt_monto_exento'+i).value<=0)))
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
		    alert('Revise los montos ingresados, debe especificar un monto sujeto o exento en alguna partida');
		    return false;
		  }
		  
}

function add_opciones()
{   
		nave=new String(navigator.appName);
		var pos_nave=nave.indexOf("Explorer");
		var index;

		element_otros = document.getElementById('tbl_part').getElementsByTagName('tr').length;
		element_otros = element_otros -1;
		var tbody2 = document.getElementById('tbl_part');
										
		//se agregan ahora los elementos a la tabla inferior
		var tabla = document.getElementById('tbl_mod');
		element_todos = document.getElementById('tbl_mod').getElementsByTagName('tr').length;
		element_todos = element_todos -3;
		
		var tbody = document.getElementById('item');
		var id='item';
		var valido=validar_pri(element_otros);
				  
		if(valido==false){return;}
		
		if(element_otros<1) 
		{
			alert("Este documento no posee partidas asociadas");
			return;
		}
		cg = trim(document.form.txt_cod_imputa2.value);
		cc = trim(document.form.txt_cod_accion2.value);
		
		for(i=0;i<element_otros;i++)
		{
		  //la partida que tenga algun valor <>0 en sujeto o exento, si se pued agregar
		  if ((document.getElementById('txt_monto_exento'+i).value>0)||(document.getElementById('txt_monto'+i).value>0))
			{
			  var numero_pagos=document.getElementById('beneficiariosTable').rows.length-1;
			  var monto_compromiso=parseFloat(MoneyToNumber(document.getElementById('monto_comp'+i).value));
			  var montoexento=parseFloat(MoneyToNumber(document.getElementById('txt_monto_exento'+i).value))*numero_pagos;
			  var montosujeto=parseFloat(MoneyToNumber(document.getElementById('txt_monto'+i).value))*numero_pagos;
			  
			  if (montosujeto+montoexento>monto_compromiso){
				  alert("El monto introducido, no puede ser superior al monto del compromiso");
				  document.getElementById('txt_monto_exento'+i).focus();
				  return;
			  }			
			var registro = new Array(7); 
			registro[0]=document.getElementById('rb_ac_proy'+i).value;//form.txt_cod_imputa.value;
			registro[1]=document.getElementById('txt_id_p_ac'+i).value;//form.txt_cod_imputa.value; 	      
			registro[2]=document.getElementById('txt_id_acesp'+i).value;//form.txt_cod_accion.value; 
			registro[3]=document.form.opt_depe.value; 
			registro[4]=document.getElementById('txt_codigo'+i).value;
			registro[5]=document.getElementById('txt_den'+i).value;
			registro[6]=document.getElementById('txt_monto'+i).value;
			registro[7]=document.getElementById('txt_monto_exento'+i).value;
			var row = document.createElement("tr")
		
			//Verificamos si esta ya registrada
			for(l=0;l<partidas.length;l++)
			{
			 if ((partidas[l][4]==registro[4]) && (partidas[l][1]==registro[1]) && (partidas[l][2]==registro[2]) ) 
			 {
				alert("Partida ya seleccionada...");
				return;
			 }
			}
		    j=partidas.length;
		   //LOS RADIO BUTTONS
			var td1 = document.createElement("td");
			td1.setAttribute("align","Center");
			td1.setAttribute("colspan","2");
			td1.className = 'normalNegro';
			//creamos una radio button
			var name="rb_ac_proy"+j;
			if(pos_nave>0)
			{
				 var rad_1 = document.createElement('<input type="radio" name="'+name+'">'); 
			}
			else
				{ 
					var rad_1 = document.createElement('INPUT');
					rad_1.type="radio";
					rad_1.name=name; }
				 
					rad_1.setAttribute("id",name);
					rad_1.setAttribute("readOnly","true");
			  
				//	if(document.form.chk_tp_imputa[0].checked==true)
					if(registro[0]==1)
					{
						//registro[0]=1;
						rad_1.setAttribute("value",1);
						rad_1_text = document.createTextNode('PR');
						rad_1.defaultChecked = true;
					}
					else
						{
							//registro[0]=0;		    
							rad_1.setAttribute("value",0);
							rad_1_text = document.createTextNode('AC');
							rad_1.defaultChecked = true
						}
				
			  td1.appendChild(rad_1);			
			  td1.appendChild(rad_1_text);

			  //CODIGO DEL PROYECTO O ACCION
			  var td22 = document.createElement("td");
			  td22.setAttribute("align","Center");
			  td22.className = 'titularMedio';
			  //creamos una radio button
			  var txt_id_p_ac = document.createElement("INPUT");
			  txt_id_p_ac.setAttribute("type","text");
			  name="txt_id_p_ac2"+i;
		      txt_id_p_ac.setAttribute("name",name);
			  txt_id_p_ac.setAttribute("readonly","true"); 
			  txt_id_p_ac.value=cg;	 
			  txt_id_p_ac.size='15'; 
			  txt_id_p_ac.className='normalNegro';
			  td22.appendChild(txt_id_p_ac);
				 
			 //CODIGO DEL PROYECTO O ACCION OCULTO
			  var td2 = document.createElement("td");
			  td2.setAttribute("align","Center");
			  td2.className = 'titularMedio';
			  //creamos una radio button
			  var txt_id_p_ac = document.createElement("INPUT");
			  txt_id_p_ac.setAttribute("type","hidden");
			  name="txt_id_p_ac"+j;
			  txt_id_p_ac.setAttribute("name",name);
			  txt_id_p_ac.readOnly=true; 
			  txt_id_p_ac.value=registro[1];
			  txt_id_p_ac.size='8'; 
			  txt_id_p_ac.className='normalNegro';
			  td2.appendChild(txt_id_p_ac);

			  //CODIGO DE LA ACCION ESPECIFICA
			  var td33 = document.createElement("td");
			  td33.setAttribute("align","Center");
			  td33.className = 'titularMedio';
			  //creamos una radio button
			  var txt_id_acesp = document.createElement("INPUT");
			  txt_id_acesp.setAttribute("type","text");
			  name="txt_id_acesp2"+i;
		      txt_id_acesp.setAttribute("name",name); 
			  txt_id_acesp.setAttribute("readonly","true"); 
			  txt_id_acesp.value=cc;	 
			  txt_id_acesp.size='8'; 
			  txt_id_acesp.className='normalNegro';
			  td33.appendChild(txt_id_acesp);
			  
			  //CODIGO DE LA ACCION ESPECIFICA OCULTO
			  var td3 = document.createElement("td");
			  td3.setAttribute("align","Center");
			  td3.className = 'titularMedio';
			  //creamos una radio button
			  var txt_id_acesp = document.createElement("INPUT");
			  txt_id_acesp.setAttribute("type","hidden");
			  name="txt_id_acesp"+j;
			  txt_id_acesp.setAttribute("name",name);
			  txt_id_acesp.setAttribute("readOnly","true"); 
			  txt_id_acesp.value=registro[2];	 
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
			  name="txt_id_depe"+j;
			  txt_id_depe.setAttribute("name",name);
			  txt_id_depe.setAttribute("readOnly","true");
			  txt_id_depe.value=registro[3];	 
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
			  td6.className = 'titularMedio';
			  //creamos una radio button
			  var txt_den_pda = document.createElement("INPUT");
			  txt_den_pda.setAttribute("type","text");
			  name="txt_den_pda"+j;
			  txt_den_pda.setAttribute("readOnly","true"); 
			  txt_den_pda.setAttribute("name",name);
			  txt_den_pda.value=registro[5];	 
			  txt_den_pda.size='25'; 
			  txt_den_pda.className='normalNegro';
			  td6.appendChild(txt_den_pda);
			  
			  //MONTO
			  var td8 = document.createElement("td");
			  td8.setAttribute("align","Center");
			  td8.className = 'titularMedio';
			  //creamos una radio button
			  var txt_monto = document.createElement("INPUT");
			  txt_monto.setAttribute("type","text"); 
			  name="txt_monto_pda"+j;
			  txt_monto.setAttribute("name",name);
			  txt_monto.setAttribute("id",name);
			  txt_monto.setAttribute("readOnly","true");
			  var mon=MoneyToNumber(registro[6]);
              txt_monto.value=mon;	 
			  txt_monto.size='10'; 
			  txt_monto.className='normalNegro';
			  td8.appendChild(txt_monto);
			  
			  //MONTO EXENTO
			  var td9 = document.createElement("td");
			  td9.setAttribute("align","Center");
			  td9.className = 'titularMedio';
			  //creamos una radio button
			  var txt_monto_exento = document.createElement("INPUT");
			  txt_monto_exento.setAttribute("type","text"); 
			  name="txt_monto_pda_exento"+j;
			  txt_monto_exento.setAttribute("name",name);
			  txt_monto_exento.setAttribute("readOnly","true");
			  var mon2=MoneyToNumber(registro[7]);
			  txt_monto_exento.value=mon2;	 
			  txt_monto_exento.size='10'; 
			  txt_monto_exento.className='normalNegro';
			  td9.appendChild(txt_monto_exento);

			  /**************************************/
			   monto_tot[monto_tot.length]= mon;
			   monto_tot_exento[monto_tot_exento.length]= mon2;
			  /***************************************/
			  
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
			  row.appendChild(td22);
			  row.appendChild(td33); 
			  row.appendChild(td4);
			  row.appendChild(td5);
			  row.appendChild(td6);
			  row.appendChild(td8);
			  row.appendChild(td9);
			  row.appendChild(td10);
			  row.appendChild(td2);
			  row.appendChild(td3);			  
			  tbody.appendChild(row); 	
			  
			  partidas[partidas.length]=registro;
			  /*****************************************************/
			  arreglo[arreglo.length]=registro[4];
			  document.form.hid_partida_actual.value=arreglo;
			  document.getElementById('txt_monto_exento'+i).value=0.0;
			  document.getElementById('txt_monto'+i).value=0.0;
			}
		  }
			document.getElementById('hid_largo').value=partidas.length;
		
	}

	function agregarBeneficiario(tipo){
		if(tipo=='1'){
			if(trim(document.getElementById("beneficiarioEmpleado").value)==""){
				alert("Introduzca el n"+uACUTE+"mero de c"+eACUTE+"dula o una palabra contenida en el nombre del empleado.");
				document.getElementById("beneficiarioEmpleado").focus();
			}else{
				if(obtenerPrimerBeneficiario()=='2'){
					alert("Ya usted indic"+oACUTE+" un proveedor como beneficiario, no se puede combinar empleados con proveedores en una misma Solicitud de Pago.");
				}else{
					tokens = document.getElementById("beneficiarioEmpleado").value.split( ":" );
					cedula = (tokens[0])?trim(tokens[0]):"";
	
					nombre = estaEnBeneficiarios(tipo, cedula);
					if(nombre!=""){
						esta = estaEnBeneficiariosTemporales(cedula);
						indice=beneficiarios.length;
						if (indice>0){
						 indice=indice-1;
						 edo_beneficiario=document.getElementById('estado'+indice).value;
						 obs_beneficiario=document.getElementById("obs_extra"+indice).value;
						}else{edo_beneficiario="";obs_beneficiario="";}
						if(esta==false){
							accionBeneficiario(0, tipo, cedula, nombre,edo_beneficiario,obs_beneficiario);
						}else{
							alert("El empleado "+nombre+" ya est"+aACUTE+" agregado como beneficiario.");
						}	
					}else{
						alert("La c"+eACUTE+"dula o el nombre indicado no es v"+aACUTE+"lido");
					}
				}
			}
			limpiarBeneficiario(tipo);
		}else if(tipo=='2'){
			if(trim(document.getElementById("beneficiarioProveedor").value)==""){
				alert("Introduzca el RIF o una palabra contenida en el nombre del proveedor.");
				document.getElementById("beneficiarioProveedor").focus();
			}else{
				/*if(obtenerPrimerBeneficiario()=='2'){
					alert("Ya usted indic"+oACUTE+" un proveedor como beneficiario, no se puede agregar varios proveedores como beneficiarios de una misma Solicitud de Pago.");
				}else*/
				 if(obtenerPrimerBeneficiario()=='1' || obtenerPrimerBeneficiario()=='3'){
					alert("Ya usted indic"+oACUTE+" personas naturales como beneficiarios, no se puede combinar personas naturales con proveedores en una misma Solicitud de Pago.");
				}else{				
					tokens = document.getElementById("beneficiarioProveedor").value.split( ":" );
					cedula = (tokens[0])?trim(tokens[0]):"";
	
					nombre = estaEnBeneficiarios(tipo, cedula);
					if(nombre!=""){
						esta = estaEnBeneficiariosTemporales(cedula);
						indice=beneficiarios.length;
						if (indice>0){
						 indice=indice-1;
						 edo_beneficiario=document.getElementById('estado'+indice).value;
						 obs_beneficiario=document.getElementById("obs_extra"+indice).value;
						}else{edo_beneficiario="";obs_beneficiario="";}
						if(esta==false){
							//accionBeneficiario(0, tipo, cedula, nombre);	
							accionBeneficiario(0, tipo, cedula, nombre,edo_beneficiario,obs_beneficiario);
						}else{
							alert("El proveedor "+nombre+" ya est"+aACUTE+" agregado como beneficiario.");
						}
					}else{
						alert("El RIF o nombre indicado no es v"+aACUTE+"lido");
					}
				}
			}
			limpiarBeneficiario(tipo);
		}else if(tipo=='3'){
			if(trim(document.getElementById("beneficiarioOtro").value)==""){
				alert("Introduzca la c"+eACUTE+"dula o una palabra contenida en el nombre de la persona.");
				document.getElementById("beneficiarioOtro").focus();
			}else{
				if(obtenerPrimerBeneficiario()=='2'){
					alert("Ya usted indic"+oACUTE+" un proveedor como beneficiario, no se puede combinar personas del renglon \"Otros\" con proveedores en una misma Solicitud de Pago.");
				}else{			
					tokens = document.getElementById("beneficiarioOtro").value.split( ":" );
					cedula = (tokens[0])?trim(tokens[0]):"";
	
					nombre = estaEnBeneficiarios(tipo, cedula);
					if(nombre!=""){
						esta = estaEnBeneficiariosTemporales(cedula);
						indice=beneficiarios.length;
						if (indice>0){
							 indice=indice-1;
							 edo_beneficiario=document.getElementById('estado'+indice).value;
							 obs_beneficiario=document.getElementById("obs_extra"+indice).value;
							}else{edo_beneficiario="";obs_beneficiario="";}
						if(esta==false){
							//accionBeneficiario(0, tipo, cedula, nombre);
							accionBeneficiario(0, tipo, cedula, nombre,edo_beneficiario,obs_beneficiario);	
						}else{
							alert("La persona "+nombre+" ya est"+aACUTE+" agregado como beneficiario.");
						}
					}else{
						alert("La c"+eACUTE+"dula o el nombre indicado no es v"+aACUTE+"lido");
					}
				}
			}
			limpiarBeneficiario(tipo);
		}
	}


	/************************** Validar Datos *******************************/
	function enviar(){   
		if(document.form.dependencia.value==0){
			alert("Debe seleccionar la Dependencia Solicitante...");
			document.form.dependencia.focus();
			return;
		}

		if(document.form.tipo_sol.value==0){
			alert("Debe seleccionar el Tipo de la Solicitud...");
			document.form.tipo_sol.focus();
			return;
		}

		if(beneficiarios.length==0){
			alert('Debe seleccionar un beneficiario');
			return;
		}
		
		cedulasBeneficiariosCadena = "";
		nombresBeneficiariosCadena = "";
		tiposBeneficiariosCadena = "";
		for(i=0;i<beneficiarios.length;i++){
			cedulasBeneficiariosCadena += beneficiarios[i][0]+",";
			nombresBeneficiariosCadena += beneficiarios[i][1]+",";
			tiposBeneficiariosCadena += beneficiarios[i][3]+",";
		}
		cedulasBeneficiariosCadena = cedulasBeneficiariosCadena.substring(0,cedulasBeneficiariosCadena.length-1);
		nombresBeneficiariosCadena = nombresBeneficiariosCadena.substring(0,nombresBeneficiariosCadena.length-1);
		tiposBeneficiariosCadena = tiposBeneficiariosCadena.substring(0,tiposBeneficiariosCadena.length-1);
		document.getElementById("hid_bene_ci_rif").value=cedulasBeneficiariosCadena;
		document.getElementById("hid_beneficiario").value=nombresBeneficiariosCadena;
		document.getElementById("hid_bene_tp").value=tiposBeneficiariosCadena;
		document.getElementById("hid_contador").value=beneficiarios.length;

		if((document.form.txt_factura.value!="") && (document.form.txt_fecha_factura.value=="")){
			alert("Debe indicar la fecha de la factura.");
			document.form.txt_fecha_factura.focus();
			return;
		}
		
		if(trim(document.form.numero_reserva.value)=="0"){
			alert('Debe especificar la fuente de financiamiento para la solicitud, de no tener colocar N/A');
			document.form.numero_reserva.focus();
			return;
		}
			
		if(document.form.comp_id.value==0){
			alert("Debe indicar el n"+uACUTE+"mero del compromiso asociado a la solicitud de pago.");
			document.form.comp_id.focus();
			return;
		}


		for(i=0;i<partidas.length;i++){
			if ((document.form.comp_id.value=='N/A') && (validar_compromiso[i]==1)){
				alert("Debe indicar el n"+uACUTE+"mero del compromiso asociado a la solicitud de pago, no puede ser N/A ya que contiene partidas que no son temporales.");
				document.form.comp_id.focus();
				return;
			}
		}


		if(trim(document.form.txt_detalle.value)==""){
			alert('Debe especificar el Motivo del Pago');
			document.form.txt_detalle.focus();
			return;
		}
		
		if((document.form.txt_cod_imputa.value=="") && (document.form.txt_cod_accion.value=="")){
			alert('Debe seleccionar la categor'+iACUTE+'a para la cual desea hacer la imputaci'+oACUTE+'n');
			return;
		}
			
		if((document.form.hid_largo.value<1) || (partidas=="")){
			alert("Este documento no posee partidas asociadas");
			return;
		}
		
		document.form.hid_monto_tot.value=MoneyToNumber(document.form.txt_monto_tot.value);
	
        if(document.form.txt_observa.length>220){
			alert("Las observaciones no deben exceder de 220 caracteres");
			return;
		}
		
		if(confirm("Datos introducidos de manera correcta. "+pACUTE+"Est"+aACUTE+" seguro que desea continuar?.")){
			var texto=crear();
			document.form.txt_arreglo_f.value=texto;
			document.form.chk_tp_imputa[0].disabled=false;
			document.form.chk_tp_imputa[1].disabled=false;
			document.form.submit();		
    	}
	}

	function verifica_fechas(fecha){ 
		var op=false;
		var fecha_actual = document.getElementById(fecha.id).value;
		if(fecha_actual.value!=""){
			var arreglo_f_desde = fecha_actual.split("/");
			var desde = new Date(arreglo_f_desde[2]+"/"+arreglo_f_desde[1]+"/"+arreglo_f_desde[0]);
			var hoy = new Date("<?=(date('Y/m/d'))?>");
			if(desde.getTime() > hoy.getTime()){
				alert("La Fecha no Puede ser Mayor a "+ "<?=(date('d/m/Y'))?>");
				document.getElementById(fecha.id).value="";
				return;
			}
		}
	}

	function colocar(){
		document.form.hid_cod_imputa.value=document.form.txt_cod_imputa.value;
	}

	function add_monto(){
		var m=0;
		var m2=0;
		var m3=0;

		for(i=0;i<monto_tot.length;i++){
			m=parseFloat(m) + parseFloat(monto_tot[i]);
		}

		for(i=0;i<monto_tot_exento.length;i++){
			m3=parseFloat(m3) + parseFloat(monto_tot_exento[i]);
		}
	 
     	m2=parseFloat(m2) + parseFloat(m) + parseFloat(m3);

		document.form.txt_monto_tot.value=number_format(m2,2,'.',',');
		document.form.txt_monto_subtotal.value=number_format(m,2,'.',',');
		document.form.txt_monto_subtotal_exento.value=number_format(m3,2,'.',',');
	 
		diner= number_format(m,2,'.','');
		diner=parseFloat(diner);
	 
		ver_monto_letra(diner, 'txt_monto_letras','');
	}


	function act_desact(){
		document.form.txt_otro.disabled = !(document.form.chk_otro.checked);
		if(document.form.chk_otro.checked){
			document.form.txt_otro.value="";
			document.form.txt_otro.focus();
		}else{
			document.form.txt_otro.value="";
		}
	}
	function mostrarBeneficiarios(valor){
		if(valor=='1'){
			div = document.getElementById("empleadoInputContainer");
			if(div.hasChildNodes()){
			    while(div.childNodes.length >= 1 ){
			    	div.removeChild(div.firstChild);       
			    }
			}
			input = document.createElement('INPUT');
			input.setAttribute("autocomplete","off");
			input.setAttribute("size","70");
			input.setAttribute("type","text");
			input.setAttribute("id","beneficiarioEmpleado");
			input.setAttribute("name","beneficiarioEmpleado");
			input.setAttribute("value","");
			input.setAttribute("class","normal");
			div.appendChild(input);
			actb(document.getElementById('beneficiarioEmpleado'),empleadosAMostrar);
			document.getElementById('contenedorEmpleados').style.display='block';
			document.getElementById('contenedorProveedores').style.display='none';
			document.getElementById('contenedorOtros').style.display='none';
			document.getElementById('beneficiarioEmpleado').focus();
		}else if(valor=='2'){
			div = document.getElementById('proveedorInputContainer');
			if(div.hasChildNodes()){
			    while(div.childNodes.length >= 1 ){
			    	div.removeChild(div.firstChild);       
			    }
			}
			input = document.createElement('INPUT');
			input.setAttribute("autocomplete","off");
			input.setAttribute("size","70");
			input.setAttribute("type","text");
			input.setAttribute("id","beneficiarioProveedor");
			input.setAttribute("name","beneficiarioProveedor");
			input.setAttribute("value","");
			input.setAttribute("class","normal");
			div.appendChild(input);
			actb(document.getElementById('beneficiarioProveedor'),proveedoresAMostrar);
			document.getElementById('contenedorProveedores').style.display='block';
			document.getElementById('contenedorEmpleados').style.display='none';
			document.getElementById('contenedorOtros').style.display='none';
			document.getElementById('beneficiarioProveedor').focus();
		}else if(valor=='3'){
			div = document.getElementById('otroInputContainer');
			if(div.hasChildNodes()){
			    while(div.childNodes.length >= 1 ){
			    	div.removeChild(div.firstChild);       
			    }
			}
			input = document.createElement('INPUT');
			input.setAttribute("autocomplete","off");
			input.setAttribute("size","70");
			input.setAttribute("type","text");
			input.setAttribute("id","beneficiarioOtro");
			input.setAttribute("name","beneficiarioOtro");
			input.setAttribute("value","");
			input.setAttribute("class","normal");
			div.appendChild(input);
			actb(document.getElementById('beneficiarioOtro'),otrosAMostrar);
			document.getElementById('contenedorOtros').style.display='block';
			document.getElementById('contenedorEmpleados').style.display='none';
			document.getElementById('contenedorProveedores').style.display='none';
			document.getElementById('beneficiarioOtro').focus();
		}else if(valor=='4'){
			div = document.getElementById('itemContainerTemp');
			if(div.hasChildNodes()){
			    while(div.childNodes.length >= 1 ){
			    	div.removeChild(div.firstChild);       
			    }
			}
			input = document.createElement('INPUT');
			input.setAttribute("autocomplete","off");
			input.setAttribute("size","70");
			input.setAttribute("type","text");
			input.setAttribute("id","itemCompletarTemp");
			input.setAttribute("name","itemCompletarTemp");
			input.setAttribute("value","");
			input.setAttribute("class","normal");
			div.appendChild(input);
			actb(document.getElementById('itemCompletarTemp'),arregloItemsTemp);  
			document.getElementById('itemContainerTemp').style.display='block';
			document.getElementById('contenedorOtros').style.display='none';
			document.getElementById('contenedorEmpleados').style.display='none';
			document.getElementById('contenedorProveedores').style.display='none';
			document.getElementById('itemCompletarTemp').focus();
		}
	
	}

	function limpiarBeneficiario(tipo){
		if(tipo=='1'){
			document.getElementById("beneficiarioEmpleado").value="";
			document.getElementById("beneficiarioEmpleado").focus();
		}else if(tipo=='2'){
			document.getElementById("beneficiarioProveedor").value="";
			document.getElementById("beneficiarioEmpleado").focus();
		}else if(tipo=='3'){
			document.getElementById("beneficiarioOtro").value="";
			document.getElementById("beneficiarioOtro").focus();
		}
	}

	function estaEnBeneficiarios(tipo, cedula){
		if(tipo=='1'){
			for(j = 0; j < cedulasEmpleados.length; j++){
				if(cedula==cedulasEmpleados[j]){
					return nombresEmpleados[j];
				}
			}
		}else if(tipo=='2'){
			for(j = 0; j < cedulasProveedores.length; j++){
				if(cedula==cedulasProveedores[j]){
					return nombresProveedores[j];
				}
			}
		}else if(tipo=='3'){
			for(j = 0; j < cedulasOtros.length; j++){
				if(cedula==cedulasOtros[j]){
					return nombresOtros[j];
				}
			}
		}
		return "";
	}

	function estaEnBeneficiariosTemporales(cedula){
		for(j = 0; j < beneficiarios.length; j++){
			if(cedula==beneficiarios[j][0]){
				return true;
			}
		}
		return false;
	}
	
	function accionBeneficiario(id, tipo, cedula, nombre, edo, obs){
		if(id==0){
			var registro = new Array(6);			
			registro[0] = cedula;
			registro[1] = nombre;
			if(tipo=='1'){
				registro[2] = "Empleado";
			}else if(tipo=='2'){
				registro[2] = "Proveedor";
			}else if(tipo=='3'){
				registro[2] = "Otro";
			}
			registro[3] = tipo;
			registro[4]=edo;
			registro[5]=obs;
			beneficiarios[beneficiarios.length]=registro;
			
		}
		var tbody = document.getElementById('beneficiariosBody');
		var table = document.getElementById('beneficiariosTable');
		for(i=0;i<beneficiarios.length-1;i++){
			table.deleteRow(1);

			if (beneficiarios[i][4]==""){
			beneficiarios[i][4]=beneficiarios[i+1][4];
			beneficiarios[i][5]=beneficiarios[i+1][5];
			}
		}
		beneficiarios[beneficiarios.length-1][4]="";
		beneficiarios[beneficiarios.length-1][5]="";

		if(id!=0){
			table.deleteRow(1);
			for(i=id;i<beneficiarios.length;i++){
				beneficiarios[i-1]=beneficiarios[i];
			}
			beneficiarios.pop();
		}
		
		for(i=0;i<beneficiarios.length;i++){
	    	var row = document.createElement("tr");
			row.setAttribute("class","normalNegro");
	    	var td0=document.createElement("td");
			td0.setAttribute("align","justify");
			td0.appendChild(document.createTextNode(i+1));
	    	
			var td1=document.createElement("td");
			td1.setAttribute("align","justify");
			td1.appendChild(document.createTextNode(beneficiarios[i][0]));
			
			var td2=document.createElement("td");
			td2.setAttribute("align","justify");
			td2.appendChild(document.createTextNode(beneficiarios[i][1]));
			
			var td3=document.createElement("td");
			td3.setAttribute("align","left");
			td3.appendChild(document.createTextNode(beneficiarios[i][2]));

			var td5 = document.createElement("td");
			td5.setAttribute("align","Center");
			td5.className = 'titularMedio';
			var edo = document.createElement('select');
			name="estado"+i;
			edo.setAttribute("name",name);
			edo.setAttribute("id",name);

			for (h=0; h<listado_estados.length; h++){
			 opt=document.createElement('option');
			 opt.setAttribute("class","normalNegro");
			 opt.value=listado_estados[h][0];
			 nombre=listado_estados[h][1];
			 opt.innerHTML = nombre;
			 //(beneficiarios.length>1)&&
			 if ( (beneficiarios[i][4]==listado_estados[h][0])){
			 opt.setAttribute("selected", "selected")
			 }
			 edo.appendChild(opt); 
			}
			  td5.appendChild(edo);

			 var td6 = document.createElement("td");
			 td6.setAttribute("align","Center");
			 td6.className = 'titularMedio';
			 var obs_extra = document.createElement('INPUT');
			 obs_extra.setAttribute("type","text");
			 name="obs_extra"+i;
			 obs_extra.setAttribute("name",name);
			 obs_extra.setAttribute("id",name);

			// if (beneficiarios.length>1){
				// h=i+1;
			 obs_extra.value=beneficiarios[i][5];
			 //}
			 obs_extra.size='15';
			 obs_extra.className='normalNegro';
			 td6.appendChild(obs_extra);
			 
			
			var td4 = document.createElement("td");
			td4.setAttribute("align","center");
	        td4.className = 'link';
			editLink = document.createElement("a");
			linkText = document.createTextNode("Eliminar");
			editLink.setAttribute("href", "javascript:accionBeneficiario('"+(i+1)+"')");
			editLink.appendChild(linkText);
			td4.appendChild (editLink);

			row.appendChild(td0);
			row.appendChild(td1);
			row.appendChild(td2);
			row.appendChild(td3);
			row.appendChild(td6);
			row.appendChild(td5);
			row.appendChild(td4);
			tbody.appendChild(row);
		}
	}

	function obtenerPrimerBeneficiario(){
		if(beneficiarios.length>0){
			return beneficiarios[0][3];
		}
		return "";
	}

		function limpiarItem(){
			document.getElementById("itemCompletarTemp").value="";
			document.getElementById("itemCompletarTemp").focus();
			document.getElementById("sujeto_temp").value="0";
			document.getElementById("exento_temp").value="0";
			document.getElementById("itemCompletarTemp").value="";
		}

function estaEnItemsTemporales(idItem){
	
	for(j = 0; j < arreglo_partidas.length; j++){
		if(idItem==arreglo_partidas[j][4]){
			return true;
		}
	}
	return false;
}

function estaEnItems(idItem,arreglop){
	for(j = 0; j < arreglop.length; j++){
		if(idItem==arreglop[j]){
			return j;
		}
	}
	return -1;
}

function agregarItem(objeto,montos,montoe,arreglo_partidas,arreglo_cuentas){
	nave=new String(navigator.appName);
	var pos_nave=nave.indexOf("Explorer");
	if(document.form.txt_cod_imputa.value==""){	
		alert("Seleccione el c"+oACUTE+"digo del Proyecto o Acci"+oACUTE+"n Centralizada !.");
		return;
	}
	
	if(trim(objeto.value)==""){
		alert("Introduzca la cuenta contable o una palabra contenida en el nombre.");
		document.getElementById("itemCompletarTemp").focus();
	}else{
				tokens = objeto.value.split( ":" );
				if(tokens[0] && tokens[1]){
					idPartida = trim(tokens[0]);
					nombreItem = trim(tokens[1]);
					if (idPartida.substring(0,1)=="4")
						indiceIdItem = estaEnItems(idPartida,arreglo_partidas);
					else
						indiceIdItem = estaEnItems(idPartida,arreglo_cuentas);
										
					if(indiceIdItem>-1){
						var tbody = document.getElementById('item');
						idItem = idsPartidasItemsTemp[indiceIdItem];
						esta = estaEnItemsTemporales(idItem);
						if(esta==false){
							indiceGeneral = partidas.length;
							nombrePartida = nombresPartidasItems[indiceIdItem];
					
							monto_sujeto = (trim(montos.value));
							monto_exento = (trim(montoe.value));
							proyecto = trim(document.form.txt_cod_imputa.value);
							accion = trim(document.form.txt_cod_accion.value);
							cg = trim(document.form.txt_cod_imputa2.value);
							cc = trim(document.form.txt_cod_accion2.value);
							
							//Verificamos si esta ya registrada
							for(l=0;l<partidas.length;l++)
							{
							 if ((partidas[l][4]==idPartida) )
							 {
							//	alert("Partida ya seleccionada...");
								return;
							 }
							}
							
							if((montos.value=='') || (montos.value<=0)){
							       if((montoe.value=='') || (montoe.value<=0)){
										alert('Revise los montos ingresados.');
										return false;
									}}
							
							var registro = new Array(8);
							registro[1]=proyecto;
							registro[2]=accion;
							registro[4]=idPartida;
							registro[5]=nombrePartida;
							registro[6]=monto_sujeto;
							registro[7]=monto_exento;
						
							var fila = document.createElement("tr");

							//LOS RADIO BUTTONS
							var td1 = document.createElement("td");
							td1.setAttribute("align","Center");
							td1.setAttribute("colspan","2");
							td1.className = 'normalNegro';
		
							//creamos una radio button
							var name="rb_ac_proy"+indiceGeneral;
							if(pos_nave>0){
						 		var rad_1 = document.createElement('<input type="radio" name="'+name+'">'); 
							}else{
								var rad_1 = document.createElement('INPUT');
								rad_1.type="radio";
								rad_1.name=name;
							}
				 
							rad_1.setAttribute("id",name);
							rad_1.setAttribute("readOnly","true");
			  
							if(document.form.chk_tp_imputa[0].checked==true){
							registro[0]=1;
							rad_1.setAttribute("value",1);
							rad_1_text = document.createTextNode('PR');
							rad_1.defaultChecked = true;
							}else{
							registro[0]=0;		    
							rad_1.setAttribute("value",0);
							rad_1_text = document.createTextNode('AC');
							rad_1.defaultChecked = true
							}
		
							td1.appendChild(rad_1);			
							td1.appendChild(rad_1_text);

							//CODIGO DEL PROYECTO O ACCION
							var td22 = document.createElement("td");
							td22.setAttribute("align","Center");
							td22.className = 'titularMedio';
							//creamos una radio button
							var txt_id_p_ac = document.createElement("INPUT");
							txt_id_p_ac.setAttribute("type","text");
							name="txt_id_p_ac2"+indiceGeneral;
							txt_id_p_ac.setAttribute("name",name);
							txt_id_p_ac.readOnly=true; 
							//registro[1]=document.form.txt_cod_imputa.value;
							txt_id_p_ac.value=cg;
			 				txt_id_p_ac.size='15'; 
							txt_id_p_ac.className='normalNegro';
							td22.appendChild(txt_id_p_ac);
							 
							//CODIGO DEL PROYECTO O ACCION OCULTA
							var td2 = document.createElement("td");
							td2.setAttribute("align","Center");
							td2.className = 'titularMedio';
							//creamos una radio button
							var txt_id_p_ac = document.createElement("INPUT");
							txt_id_p_ac.setAttribute("type","hidden");
							name="txt_id_p_ac"+indiceGeneral;
							txt_id_p_ac.setAttribute("name",name);
							txt_id_p_ac.readOnly=true; 
							registro[1]=document.form.txt_cod_imputa.value;
							txt_id_p_ac.value=registro[1];
							txt_id_p_ac.size='8'; 
							txt_id_p_ac.className='normalNegro';
							td2.appendChild(txt_id_p_ac);

							//CODIGO DE LA ACCION ESPECIFICA
							var td33 = document.createElement("td");
							td33.setAttribute("align","Center");
							td33.className = 'titularMedio';
							//creamos una radio button
							var txt_id_acesp = document.createElement("INPUT");
							txt_id_acesp.setAttribute("type","text");
							name="txt_id_acesp2"+indiceGeneral;
							txt_id_acesp.setAttribute("name",name);
							txt_id_acesp.setAttribute("readOnly","true"); 
							//registro[2]=document.form.txt_cod_accion.value;
							txt_id_acesp.value=cc;	 
							txt_id_acesp.size='8'; 
							txt_id_acesp.className='normalNegro';
							td33.appendChild(txt_id_acesp);
							  
							//CODIGO DE LA ACCION ESPECIFICA OCULTA
							var td3 = document.createElement("td");
							td3.setAttribute("align","Center");
							td3.className = 'titularMedio';

							//creamos una radio button
							var txt_id_acesp = document.createElement("INPUT");
							txt_id_acesp.setAttribute("type","hidden");
							name="txt_id_acesp"+indiceGeneral;
							txt_id_acesp.setAttribute("name",name);
							txt_id_acesp.setAttribute("readOnly","true"); 
							registro[2]=document.form.txt_cod_accion.value;
							txt_id_acesp.value=registro[2];	 
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
							name="txt_id_depe"+indiceGeneral;
							txt_id_depe.setAttribute("name",name);
							txt_id_depe.setAttribute("readOnly","true");
							registro[3]=document.form.opt_depe.value;
							txt_id_depe.value=registro[3];	 
							txt_id_depe.size='8'; 
							txt_id_depe.className='normalNegro';
							td4.appendChild(txt_id_depe);
							
							//CODIGO DE LA PARTIDA
							var columna3 = document.createElement("td");
							columna3.setAttribute("align","center");
							columna3.className = 'titularMedio';
							var inputIdPartida = document.createElement("INPUT");
							inputIdPartida.setAttribute("type","text");
							inputIdPartida.setAttribute("readOnly","true");
							inputIdPartida.setAttribute("name","txt_id_pda"+indiceGeneral);
							inputIdPartida.value=registro[4];
							inputIdPartida.size='15';
							inputIdPartida.className='normalNegro';
							columna3.appendChild(inputIdPartida);
							
							//DENOMINACION DE LA PARTIDA
							var columna4 = document.createElement("td");
							columna4.setAttribute("align","center");
							columna4.className = 'titularMedio';
							var inputNombrePartida = document.createElement("INPUT");
							inputNombrePartida.setAttribute("type","text");
							inputNombrePartida.setAttribute("name","txt_den_pda"+indiceGeneral);
							inputNombrePartida.setAttribute("readOnly","true");
							inputNombrePartida.value=registro[5];
							inputNombrePartida.size='25';
							inputNombrePartida.className='normalNegro';
							columna4.appendChild(inputNombrePartida);
							
							//DESCRIPCION
							var columna5 = document.createElement("td");
							columna5.setAttribute("align","center");
							columna5.className = 'titularMedio';
							var inputEspecificaciones = document.createElement("INPUT");
							inputEspecificaciones.setAttribute("type","text");
							inputEspecificaciones.setAttribute("name","txt_monto_pda"+indiceGeneral);
							inputEspecificaciones.setAttribute("readOnly","true");
							inputEspecificaciones.value=registro[6];
							inputEspecificaciones.size='10';
							inputEspecificaciones.className='normalNegro';
							columna5.appendChild(inputEspecificaciones);
							
							//CANTIDAD
							var columna6 = document.createElement("td");
							columna6.setAttribute("align","center");
							columna6.className = 'titularMedio';
							var inputCantidad = document.createElement("INPUT");
							inputCantidad.setAttribute("type","text");
							inputCantidad.setAttribute("name","txt_monto_pda_exento"+indiceGeneral);
							inputCantidad.setAttribute("readOnly","true");
							inputCantidad.value=registro[7];
							inputCantidad.size='10';
							inputCantidad.className='normalNegro';
							columna6.appendChild(inputCantidad);
							
							monto_tot[monto_tot.length]= registro[6];
							monto_tot_exento[monto_tot_exento.length]= registro[7];

							
							//OPCION DE ELIMINAR
							var columna7 = document.createElement("td");
							columna7.setAttribute("align","center");
							columna7.className = 'normal';
							editLink = document.createElement("a");
							linkText = document.createTextNode("Eliminar");
							editLink.setAttribute("href", "javascript:elimina_pda('"+(indiceGeneral+1)+"')");
							editLink.appendChild(linkText);
							columna7.appendChild (editLink);

							fila.appendChild(td1); 
							fila.appendChild(td22);
							fila.appendChild(td33);
							fila.appendChild(td4);  
							fila.appendChild(columna3);
							fila.appendChild(columna4);
							fila.appendChild(columna5);
							fila.appendChild(columna6);
							fila.appendChild(columna7);
							fila.appendChild(td2);
							fila.appendChild(td3);
							tbody.appendChild(fila); 

							partidas[partidas.length]=registro;
						
							var temporal=registro[4];
							if ((temporal.substring(0,6)!='4.11.0') && (temporal.substring(0,1)=='4')) {
							
							 validar_compromiso[partidas.length-1]=1;
							 
							}else{
								validar_compromiso[partidas.length-1]=0;
								}

							document.getElementById('hid_largo').value=partidas.length;
							limpiarItem();
						}else{
							alert("La partida ya se ha agregado a la solicitud.");
							document.getElementById("itemCompletarTemp").value="";
							document.getElementById("sujeto_temp").value="0";
							document.getElementById("exento_temp").value="0";
							
							
						}
					}
					else{
						alert("La partida indicada no es v"+aACUTE+"lido");
					}
				}else{
					alert("Seleccione una partida");
				}
			}	
}
var _charmiles = ',';    //separador de miles
var _chardecimal = '.';    //separador de la parte decimal

function inputFloat(e,minus){

    var menos = minus || false;
    if(e==null){
        e=event;
    }
    if(e==null){
        e=window.event;
    }

    var tecla = (document.all) ? e.keyCode : e.which;
    //48=0,57=9, 45=menos

    if(tecla==0 && !document.all)return true;//solo FF en keypress de flechas
    if(tecla==8)return true;//backs
    if(tecla==_chardecimal.charCodeAt(0)) return true; //punto decimal
    if (tecla==45){
        if (!menos){
            return false;
        }
    }else if(tecla < 48 || tecla > 57){
        return false;
    }
    return true;
}



function validar_pri(elem)
{
	montos_pdas = new Array();
	for(i=0;i<elem;i++)
	{
		if( ((document.getElementById('txt_monto'+i).value=='') || (document.getElementById('txt_monto'+i).value<=0))
		&& ((document.getElementById('txt_monto_exento'+i).value=='') || (document.getElementById('txt_monto_exento'+i).value<=0)))
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
		    alert('Revise los montos ingresados, debe especificar un monto sujeto o exento en alguna partida');
		    return false;
		  }
		  
}

function add_opciones()
{   
		nave=new String(navigator.appName);
		var pos_nave=nave.indexOf("Explorer");
		var index;

		element_otros = document.getElementById('tbl_part').getElementsByTagName('tr').length;
		element_otros = element_otros -1;
		var tbody2 = document.getElementById('tbl_part');
										
		//se agregan ahora los elementos a la tabla inferior
		var tabla = document.getElementById('tbl_mod');
		element_todos = document.getElementById('tbl_mod').getElementsByTagName('tr').length;
		element_todos = element_todos -3;
		
		var tbody = document.getElementById('item');
		var id='item';
		var valido=validar_pri(element_otros);
				  
		if(valido==false){return;}
		
		if(element_otros<1) 
		{
			alert("Este documento no posee partidas asociadas");
			return;
		}
		cg = trim(document.form.txt_cod_imputa2.value);
		cc = trim(document.form.txt_cod_accion2.value);
		
		for(i=0;i<element_otros;i++)
		{
		  //la partida que tenga algun valor <>0 en sujeto o exento, si se pued agregar
		  if ((document.getElementById('txt_monto_exento'+i).value>0)||(document.getElementById('txt_monto'+i).value>0))
			{
			  var numero_pagos=document.getElementById('beneficiariosTable').rows.length-1;
			  var monto_compromiso=parseFloat(MoneyToNumber(document.getElementById('monto_comp'+i).value));
			  var montoexento=parseFloat(MoneyToNumber(document.getElementById('txt_monto_exento'+i).value))*numero_pagos;
			  var montosujeto=parseFloat(MoneyToNumber(document.getElementById('txt_monto'+i).value))*numero_pagos;
			  
			  if (montosujeto+montoexento>monto_compromiso){
				  alert("El monto introducido, no puede ser superior al monto del compromiso");
				  document.getElementById('txt_monto_exento'+i).focus();
				  return;
			  }			
			var registro = new Array(7); 
			registro[0]=document.getElementById('rb_ac_proy'+i).value;//form.txt_cod_imputa.value;
			registro[1]=document.getElementById('txt_id_p_ac'+i).value;//form.txt_cod_imputa.value; 	      
			registro[2]=document.getElementById('txt_id_acesp'+i).value;//form.txt_cod_accion.value; 
			registro[3]=document.form.opt_depe.value; 
			registro[4]=document.getElementById('txt_codigo'+i).value;
			registro[5]=document.getElementById('txt_den'+i).value;
			registro[6]=document.getElementById('txt_monto'+i).value;
			registro[7]=document.getElementById('txt_monto_exento'+i).value;
			var row = document.createElement("tr")
		
			//Verificamos si esta ya registrada
			for(l=0;l<partidas.length;l++)
			{
			 if ((partidas[l][4]==registro[4]) && (partidas[l][1]==registro[1]) && (partidas[l][2]==registro[2]) ) 
			 {
				alert("Partida ya seleccionada...");
				return;
			 }
			}
		    j=partidas.length;
		   //LOS RADIO BUTTONS
			var td1 = document.createElement("td");
			td1.setAttribute("align","Center");
			td1.setAttribute("colspan","2");
			td1.className = 'normalNegro';
			//creamos una radio button
			var name="rb_ac_proy"+j;
			if(pos_nave>0)
			{
				 var rad_1 = document.createElement('<input type="radio" name="'+name+'">'); 
			}
			else
				{ 
					var rad_1 = document.createElement('INPUT');
					rad_1.type="radio";
					rad_1.name=name; }
				 
					rad_1.setAttribute("id",name);
					rad_1.setAttribute("readOnly","true");
			  
				//	if(document.form.chk_tp_imputa[0].checked==true)
					if(registro[0]==1)
					{
						//registro[0]=1;
						rad_1.setAttribute("value",1);
						rad_1_text = document.createTextNode('PR');
						rad_1.defaultChecked = true;
					}
					else
						{
							//registro[0]=0;		    
							rad_1.setAttribute("value",0);
							rad_1_text = document.createTextNode('AC');
							rad_1.defaultChecked = true
						}
				
			  td1.appendChild(rad_1);			
			  td1.appendChild(rad_1_text);

			  //CODIGO DEL PROYECTO O ACCION
			  var td22 = document.createElement("td");
			  td22.setAttribute("align","Center");
			  td22.className = 'titularMedio';
			  //creamos una radio button
			  var txt_id_p_ac = document.createElement("INPUT");
			  txt_id_p_ac.setAttribute("type","text");
			  name="txt_id_p_ac2"+i;
		      txt_id_p_ac.setAttribute("name",name);
			  txt_id_p_ac.setAttribute("readonly","true"); 
			  txt_id_p_ac.value=cg;	 
			  txt_id_p_ac.size='15'; 
			  txt_id_p_ac.className='normalNegro';
			  td22.appendChild(txt_id_p_ac);
				 
			 //CODIGO DEL PROYECTO O ACCION OCULTO
			  var td2 = document.createElement("td");
			  td2.setAttribute("align","Center");
			  td2.className = 'titularMedio';
			  //creamos una radio button
			  var txt_id_p_ac = document.createElement("INPUT");
			  txt_id_p_ac.setAttribute("type","hidden");
			  name="txt_id_p_ac"+j;
			  txt_id_p_ac.setAttribute("name",name);
			  txt_id_p_ac.readOnly=true; 
			  txt_id_p_ac.value=registro[1];
			  txt_id_p_ac.size='8'; 
			  txt_id_p_ac.className='normalNegro';
			  td2.appendChild(txt_id_p_ac);

			  //CODIGO DE LA ACCION ESPECIFICA
			  var td33 = document.createElement("td");
			  td33.setAttribute("align","Center");
			  td33.className = 'titularMedio';
			  //creamos una radio button
			  var txt_id_acesp = document.createElement("INPUT");
			  txt_id_acesp.setAttribute("type","text");
			  name="txt_id_acesp2"+i;
		      txt_id_acesp.setAttribute("name",name); 
			  txt_id_acesp.setAttribute("readonly","true"); 
			  txt_id_acesp.value=cc;	 
			  txt_id_acesp.size='8'; 
			  txt_id_acesp.className='normalNegro';
			  td33.appendChild(txt_id_acesp);
			  
			  //CODIGO DE LA ACCION ESPECIFICA OCULTO
			  var td3 = document.createElement("td");
			  td3.setAttribute("align","Center");
			  td3.className = 'titularMedio';
			  //creamos una radio button
			  var txt_id_acesp = document.createElement("INPUT");
			  txt_id_acesp.setAttribute("type","hidden");
			  name="txt_id_acesp"+j;
			  txt_id_acesp.setAttribute("name",name);
			  txt_id_acesp.setAttribute("readOnly","true"); 
			  txt_id_acesp.value=registro[2];	 
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
			  name="txt_id_depe"+j;
			  txt_id_depe.setAttribute("name",name);
			  txt_id_depe.setAttribute("readOnly","true");
			  txt_id_depe.value=registro[3];	 
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
			  td6.className = 'titularMedio';
			  //creamos una radio button
			  var txt_den_pda = document.createElement("INPUT");
			  txt_den_pda.setAttribute("type","text");
			  name="txt_den_pda"+j;
			  txt_den_pda.setAttribute("readOnly","true"); 
			  txt_den_pda.setAttribute("name",name);
			  txt_den_pda.value=registro[5];	 
			  txt_den_pda.size='25'; 
			  txt_den_pda.className='normalNegro';
			  td6.appendChild(txt_den_pda);
			  
			  //MONTO
			  var td8 = document.createElement("td");
			  td8.setAttribute("align","Center");
			  td8.className = 'titularMedio';
			  //creamos una radio button
			  var txt_monto = document.createElement("INPUT");
			  txt_monto.setAttribute("type","text"); 
			  name="txt_monto_pda"+j;
			  txt_monto.setAttribute("name",name);
			  txt_monto.setAttribute("id",name);
			  txt_monto.setAttribute("readOnly","true");
			  var mon=MoneyToNumber(registro[6]);
              txt_monto.value=mon;	 
			  txt_monto.size='10'; 
			  txt_monto.className='normalNegro';
			  td8.appendChild(txt_monto);
			  
			  //MONTO EXENTO
			  var td9 = document.createElement("td");
			  td9.setAttribute("align","Center");
			  td9.className = 'titularMedio';
			  //creamos una radio button
			  var txt_monto_exento = document.createElement("INPUT");
			  txt_monto_exento.setAttribute("type","text"); 
			  name="txt_monto_pda_exento"+j;
			  txt_monto_exento.setAttribute("name",name);
			  txt_monto_exento.setAttribute("readOnly","true");
			  var mon2=MoneyToNumber(registro[7]);
			  txt_monto_exento.value=mon2;	 
			  txt_monto_exento.size='10'; 
			  txt_monto_exento.className='normalNegro';
			  td9.appendChild(txt_monto_exento);

			  /**************************************/
			   monto_tot[monto_tot.length]= mon;
			   monto_tot_exento[monto_tot_exento.length]= mon2;
			  /***************************************/
			  
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
			  row.appendChild(td22);
			  row.appendChild(td33); 
			  row.appendChild(td4);
			  row.appendChild(td5);
			  row.appendChild(td6);
			  row.appendChild(td8);
			  row.appendChild(td9);
			  row.appendChild(td10);
			  row.appendChild(td2);
			  row.appendChild(td3);			  
			  tbody.appendChild(row); 	
			  
			  partidas[partidas.length]=registro;
			  /*****************************************************/
			  arreglo[arreglo.length]=registro[4];
			  document.form.hid_partida_actual.value=arreglo;
			  document.getElementById('txt_monto_exento'+i).value=0.0;
			  document.getElementById('txt_monto'+i).value=0.0;
			}
		  }
			document.getElementById('hid_largo').value=partidas.length;
		
	}

	function agregarBeneficiario(tipo){
		if(tipo=='1'){
			if(trim(document.getElementById("beneficiarioEmpleado").value)==""){
				alert("Introduzca el n"+uACUTE+"mero de c"+eACUTE+"dula o una palabra contenida en el nombre del empleado.");
				document.getElementById("beneficiarioEmpleado").focus();
			}else{
				if(obtenerPrimerBeneficiario()=='2'){
					alert("Ya usted indic"+oACUTE+" un proveedor como beneficiario, no se puede combinar empleados con proveedores en una misma Solicitud de Pago.");
				}else{
					tokens = document.getElementById("beneficiarioEmpleado").value.split( ":" );
					cedula = (tokens[0])?trim(tokens[0]):"";
	
					nombre = estaEnBeneficiarios(tipo, cedula);
					if(nombre!=""){
						esta = estaEnBeneficiariosTemporales(cedula);
						indice=beneficiarios.length;
						if (indice>0){
						 indice=indice-1;
						 edo_beneficiario=document.getElementById('estado'+indice).value;
						 obs_beneficiario=document.getElementById("obs_extra"+indice).value;
						}else{edo_beneficiario="";obs_beneficiario="";}
						if(esta==false){
							accionBeneficiario(0, tipo, cedula, nombre,edo_beneficiario,obs_beneficiario);
						}else{
							alert("El empleado "+nombre+" ya est"+aACUTE+" agregado como beneficiario.");
						}	
					}else{
						alert("La c"+eACUTE+"dula o el nombre indicado no es v"+aACUTE+"lido");
					}
				}
			}
			limpiarBeneficiario(tipo);
		}else if(tipo=='2'){
			if(trim(document.getElementById("beneficiarioProveedor").value)==""){
				alert("Introduzca el RIF o una palabra contenida en el nombre del proveedor.");
				document.getElementById("beneficiarioProveedor").focus();
			}else{
				/*if(obtenerPrimerBeneficiario()=='2'){
					alert("Ya usted indic"+oACUTE+" un proveedor como beneficiario, no se puede agregar varios proveedores como beneficiarios de una misma Solicitud de Pago.");
				}else*/
				 if(obtenerPrimerBeneficiario()=='1' || obtenerPrimerBeneficiario()=='3'){
					alert("Ya usted indic"+oACUTE+" personas naturales como beneficiarios, no se puede combinar personas naturales con proveedores en una misma Solicitud de Pago.");
				}else{				
					tokens = document.getElementById("beneficiarioProveedor").value.split( ":" );
					cedula = (tokens[0])?trim(tokens[0]):"";
	
					nombre = estaEnBeneficiarios(tipo, cedula);
					if(nombre!=""){
						esta = estaEnBeneficiariosTemporales(cedula);
						indice=beneficiarios.length;
						if (indice>0){
						 indice=indice-1;
						 edo_beneficiario=document.getElementById('estado'+indice).value;
						 obs_beneficiario=document.getElementById("obs_extra"+indice).value;
						}else{edo_beneficiario="";obs_beneficiario="";}
						if(esta==false){
							//accionBeneficiario(0, tipo, cedula, nombre);	
							accionBeneficiario(0, tipo, cedula, nombre,edo_beneficiario,obs_beneficiario);
						}else{
							alert("El proveedor "+nombre+" ya est"+aACUTE+" agregado como beneficiario.");
						}
					}else{
						alert("El RIF o nombre indicado no es v"+aACUTE+"lido");
					}
				}
			}
			limpiarBeneficiario(tipo);
		}else if(tipo=='3'){
			if(trim(document.getElementById("beneficiarioOtro").value)==""){
				alert("Introduzca la c"+eACUTE+"dula o una palabra contenida en el nombre de la persona.");
				document.getElementById("beneficiarioOtro").focus();
			}else{
				if(obtenerPrimerBeneficiario()=='2'){
					alert("Ya usted indic"+oACUTE+" un proveedor como beneficiario, no se puede combinar personas del renglon \"Otros\" con proveedores en una misma Solicitud de Pago.");
				}else{			
					tokens = document.getElementById("beneficiarioOtro").value.split( ":" );
					cedula = (tokens[0])?trim(tokens[0]):"";
	
					nombre = estaEnBeneficiarios(tipo, cedula);
					if(nombre!=""){
						esta = estaEnBeneficiariosTemporales(cedula);
						indice=beneficiarios.length;
						if (indice>0){
							 indice=indice-1;
							 edo_beneficiario=document.getElementById('estado'+indice).value;
							 obs_beneficiario=document.getElementById("obs_extra"+indice).value;
							}else{edo_beneficiario="";obs_beneficiario="";}
						if(esta==false){
							//accionBeneficiario(0, tipo, cedula, nombre);
							accionBeneficiario(0, tipo, cedula, nombre,edo_beneficiario,obs_beneficiario);	
						}else{
							alert("La persona "+nombre+" ya est"+aACUTE+" agregado como beneficiario.");
						}
					}else{
						alert("La c"+eACUTE+"dula o el nombre indicado no es v"+aACUTE+"lido");
					}
				}
			}
			limpiarBeneficiario(tipo);
		}
	}


	/************************** Validar Datos *******************************/
	function enviar(){   
		if(document.form.dependencia.value==0){
			alert("Debe seleccionar la Dependencia Solicitante...");
			document.form.dependencia.focus();
			return;
		}

		if(document.form.tipo_sol.value==0){
			alert("Debe seleccionar el Tipo de la Solicitud...");
			document.form.tipo_sol.focus();
			return;
		}

		if(beneficiarios.length==0){
			alert('Debe seleccionar un beneficiario');
			return;
		}
		
		cedulasBeneficiariosCadena = "";
		nombresBeneficiariosCadena = "";
		tiposBeneficiariosCadena = "";
		for(i=0;i<beneficiarios.length;i++){
			cedulasBeneficiariosCadena += beneficiarios[i][0]+",";
			nombresBeneficiariosCadena += beneficiarios[i][1]+",";
			tiposBeneficiariosCadena += beneficiarios[i][3]+",";
		}
		cedulasBeneficiariosCadena = cedulasBeneficiariosCadena.substring(0,cedulasBeneficiariosCadena.length-1);
		nombresBeneficiariosCadena = nombresBeneficiariosCadena.substring(0,nombresBeneficiariosCadena.length-1);
		tiposBeneficiariosCadena = tiposBeneficiariosCadena.substring(0,tiposBeneficiariosCadena.length-1);
		document.getElementById("hid_bene_ci_rif").value=cedulasBeneficiariosCadena;
		document.getElementById("hid_beneficiario").value=nombresBeneficiariosCadena;
		document.getElementById("hid_bene_tp").value=tiposBeneficiariosCadena;
		document.getElementById("hid_contador").value=beneficiarios.length;

		if((document.form.txt_factura.value!="") && (document.form.txt_fecha_factura.value=="")){
			alert("Debe indicar la fecha de la factura.");
			document.form.txt_fecha_factura.focus();
			return;
		}
		
		if(trim(document.form.numero_reserva.value)=="0"){
			alert('Debe especificar la fuente de financiamiento para la solicitud, de no tener colocar N/A');
			document.form.numero_reserva.focus();
			return;
		}
			
		if(document.form.comp_id.value==0){
			alert("Debe indicar el n"+uACUTE+"mero del compromiso asociado a la solicitud de pago.");
			document.form.comp_id.focus();
			return;
		}


		for(i=0;i<partidas.length;i++){
			if ((document.form.comp_id.value=='N/A') && (validar_compromiso[i]==1)){
				alert("Debe indicar el n"+uACUTE+"mero del compromiso asociado a la solicitud de pago, no puede ser N/A ya que contiene partidas que no son temporales.");
				document.form.comp_id.focus();
				return;
			}
		}


		if(trim(document.form.txt_detalle.value)==""){
			alert('Debe especificar el Motivo del Pago');
			document.form.txt_detalle.focus();
			return;
		}
		
		if((document.form.txt_cod_imputa.value=="") && (document.form.txt_cod_accion.value=="")){
			alert('Debe seleccionar la categor'+iACUTE+'a para la cual desea hacer la imputaci'+oACUTE+'n');
			return;
		}
			
		if((document.form.hid_largo.value<1) || (partidas=="")){
			alert("Este documento no posee partidas asociadas");
			return;
		}
		
		document.form.hid_monto_tot.value=MoneyToNumber(document.form.txt_monto_tot.value);
	
        if(document.form.txt_observa.length>220){
			alert("Las observaciones no deben exceder de 220 caracteres");
			return;
		}
		
		if(confirm("Datos introducidos de manera correcta. "+pACUTE+"Est"+aACUTE+" seguro que desea continuar?.")){
			var texto=crear();
			document.form.txt_arreglo_f.value=texto;
			document.form.chk_tp_imputa[0].disabled=false;
			document.form.chk_tp_imputa[1].disabled=false;
			document.form.submit();		
    	}
	}

	function verifica_fechas(fecha){ 
		var op=false;
		var fecha_actual = document.getElementById(fecha.id).value;
		if(fecha_actual.value!=""){
			var arreglo_f_desde = fecha_actual.split("/");
			var desde = new Date(arreglo_f_desde[2]+"/"+arreglo_f_desde[1]+"/"+arreglo_f_desde[0]);
			var hoy = new Date("<?=(date('Y/m/d'))?>");
			if(desde.getTime() > hoy.getTime()){
				alert("La Fecha no Puede ser Mayor a "+ "<?=(date('d/m/Y'))?>");
				document.getElementById(fecha.id).value="";
				return;
			}
		}
	}

	function colocar(){
		document.form.hid_cod_imputa.value=document.form.txt_cod_imputa.value;
	}

	function revisar_doc(id_tipo_documento,id_opcion,objeto_siguiente_id,objeto_siguiente_id_proy,cadena_siguiente_id,cadena_siguiente_id_proy,id_objeto_actual){ 
		document.form.action="accion_ejecutar.php?tipo="+id_tipo_documento+"&accion="+id_objeto_actual+"&accion_sig=" + objeto_siguiente_id + "&hijo="+cadena_siguiente_id+"&opcion="+id_opcion;
		enviar();
	}
	
var digitos=15 //cantidad de digitos buscados
var puntero=0
var buffer=new Array(digitos) //declaración del array Buffer
var cadena=""

function buscar_op(obj,objfoco){

	var nav4 = window.Event ? true : false;
	var key = nav4 ? obj.which : obj.keyCode;	
	
   var letra = String.fromCharCode(key)
   if(puntero >= digitos){
       cadena="";
       puntero=0;
    }
   //si se presiona la tecla ENTER, borro el array de teclas presionadas y salto a otro objeto...

	   if (key == 13){
       borrar_buffer();
       if(objfoco!=0) objfoco.focus(); //evita foco a otro objeto si objfoco=0
    }
   //sino busco la cadena tipeada dentro del combo...
   else{
       buffer[puntero]=letra;
       //guardo en la posicion puntero la letra tipeada
       cadena=cadena+buffer[puntero]; //armo una cadena con los datos que van ingresando al array
       puntero++;

       //barro todas las opciones que contiene el combo y las comparo la cadena...
       for (var opcombo=0;opcombo < obj.length;opcombo++){
          if(obj[opcombo].text.substr(0,puntero).toLowerCase()==cadena.toLowerCase()){
          obj.selectedIndex=opcombo;
          }
       }
    }
   
	   obj.returnValue = false; //invalida la acción de pulsado de tecla para evitar busqueda del primer caracter
}

function borrar_buffer(){
   //inicializa la cadena buscada
    cadena="";
    puntero=0;
}

function elimina_fac(tipo)
{ 

		nave=new String(navigator.appName);
		var pos_nave=nave.indexOf("Explorer");
		
		var tabla = document.getElementById('tbl_fact');
		var tbody = document.getElementById('ar_fact');
			
		for(i=0;i<facturas.length;i++)
		{
		 tabla.deleteRow(1);
		}
	   
		for(i=tipo;i<facturas.length;i++)
		{
			facturas[i-1]=facturas[i];
			arreglo[i-1]=facturas[i][3];
			monto_tot[i-1]=monto_tot[i];
			monto_tot_exento[i-1]=monto_tot_exento[i];
		}
		monto_tot[facturas.length-1]=0;
 	    monto_tot_exento[facturas.length-1]=0;
		facturas.pop(); 
		arreglo.pop();
		document.form.hid_partida_actual.value=arreglo;
		//alert(document.form.hid_partida_actual.value);
			
		nave=new String(navigator.appName);
		var pos_nave=nave.indexOf("Explorer");
		
		document.getElementById('hid_largo').value=facturas.length;
		//agrega los elementos
		for(i=0;i<facturas.length;i++)
		{
			var row = document.createElement("tr")
						  
			 //NUMERO DE FACTURA
			  var td1 = document.createElement("td");
			  td1.setAttribute("align","Center");
			  td1.className = 'titularMedio';
			  //creamos una radio button
			  var txt_fac = document.createElement("INPUT");
			  txt_fac.setAttribute("type","text");
			  name="txt_id_p_ac"+i;
			  txt_fac.setAttribute("name",name);
			  txt_fac.setAttribute("readonly","true"); 
			  txt_fac.value=facturas[i][1];	 
			  txt_fac.size='15'; 
			  txt_fac.className='ptotal';
			  td1.appendChild(txt_fac);
			  
			  //FECHA FACTURA
			  var td2 = document.createElement("td");
			  td2.setAttribute("align","Center");
			  td2.className = 'titularMedio';
			  //creamos una radio button
			  var txt_fec_fac = document.createElement("INPUT");
			  txt_fec_fac.setAttribute("type","text");
			  name="txt_id_acesp"+i;
			  txt_fec_fac.setAttribute("name",name); 
			  txt_fec_fac.setAttribute("readonly","true"); 
			  txt_fec_fac.value=facturas[i][2];	 
			  txt_fec_fac.size='8'; 
			  txt_fec_fac.className='ptotal';
			  td2.appendChild(txt_fec_fac);
			  
			   //NUMERO DE CONTROL
			  var td3 = document.createElement("td");
			  td3.setAttribute("align","Center");
			  td3.className = 'titularMedio';
			  //creamos una radio button
			  var txt_control = document.createElement("INPUT");
			  txt_control.setAttribute("type","text");
			  txt_control.setAttribute("readonly","true");
			  name="txt_id_depe"+i;
			  txt_control.setAttribute("name",name); 
			  txt_control.value=facturas[i][3];	 
			  txt_control.size='8'; 
			  txt_control.className='ptotal';
			  td3.appendChild(txt_control);
					
			  //MONTO BASE
			  var td4 = document.createElement("td");
			  td4.setAttribute("align","Center");
			  td4.className = 'titularMedio';
			  //creamos una radio button
			  var txt_base = document.createElement("INPUT");
			  txt_base.setAttribute("type","text");
			  txt_base.setAttribute("readonly","true");
			  name="txt_id_pda"+i;
			  txt_base.setAttribute("name",name);
			  txt_base.value=facturas[i][4];	 
			  txt_base.size='15'; 
			  txt_base.className='ptotal';
			  td4.appendChild(txt_base);
			  
			  //% IVA
			  var td5 = document.createElement("td");
			  td5.setAttribute("align","Center");
			  td5.className = 'titularMedio';
			  //creamos una radio button
			  var txt_den_pda = document.createElement("INPUT");
			  txt_den_pda.setAttribute("type","text");
			  txt_den_pda.setAttribute("readonly","true");
			  name="txt_den_pda"+i;
			  txt_den_pda.setAttribute("name",name);
			  txt_den_pda.value=facturas[i][5];	 
			  txt_den_pda.size='20'; 
			  txt_den_pda.className='ptotal';
			  td5.appendChild(txt_den_pda);
			  
			  //MONTO IVA
			  var td6 = document.createElement("td");
			  td6.setAttribute("align","Center");
			  td6.className = 'titularMedio';
			  //creamos una radio button
			  var txt_monto = document.createElement("INPUT");
			  txt_monto.setAttribute("type","text");
			  name="txt_monto_pda"+i;
			  txt_monto.setAttribute("name",name);
			  txt_monto.setAttribute("readonly","true");
			  var mon=MoneyToNumber(facturas[i][6]);
			  txt_monto.value=mon;	 
			  txt_monto.size='10'; 
			  txt_monto.className='ptotal';
			  td6.appendChild(txt_monto);	
			  
			  monto_total[monto_total.length]=mon;
			

			  //OPCION DE ELIMINAR
			  var td7 = document.createElement("td");				
			  td7.setAttribute("align","Center");
			  td7.className = 'link';
			  editLink = document.createElement("a");
			  linkText = document.createTextNode("Eliminar");
			  editLink.setAttribute("href", "javascript:elimina_fac('"+(i+1)+"')");
			  editLink.appendChild(linkText);
			  td7.appendChild (editLink);
	
			  row.appendChild(td1); 
			  row.appendChild(td2);
			  row.appendChild(td3); 
			  row.appendChild(td4);
			  row.appendChild(td5);
			  row.appendChild(td6);
			  row.appendChild(td7);
			  tbody.appendChild(row); 	
			}
			/****************************************/
			mo=0;
			me=0;

			if(monto_tot.length==0){document.form.txt_monto_tot.value=0;}

			for(i=0;i<monto_total.length;i++)
			{
				mo=parseFloat(mo) + parseFloat(monto_total[i]);
				document.form.txt_monto_tot.value=mo;
			}  

			//if(monto_tot_exento.length==0){document.form.txt_monto_tot_exento.value=0;}
			for(i=0;i<monto_total_exento.length;i++)
			{
				me=parseFloat(me) + parseFloat(monto_total_exento[i]);
				document.form.txt_monto_subtotal_exento.value=me;
			}  

			if (facturas.length==0)
			{
			document.form.hid_monto_tot.value=0;
			document.form.txt_monto_subtotal.value=0;
			document.form.txt_monto_subtotal_exento.value=0;
			document.form.txt_monto_tot.value=0;
			diner=0;
			monto_tot=new Array();
			monto_tot_exento=new Array();
			}
			else
			{
			document.form.hid_monto_tot.value=mo;
			document.form.txt_monto_subtotal.value=document.form.txt_monto_tot.value;
			diner= number_format(mo,2,'.','');
			}
			calcular_iva();
			monto_total=new Array();
			monto_total_exento=new Array();
			diner=parseFloat(diner);
			ver_monto_letra(diner, 'txt_monto_letras','');
			ver_monto_letra(diner,'hid_monto_letras','');
	}
