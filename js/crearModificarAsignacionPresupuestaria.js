var partidas = new Array();

function cambiarAccionEspecifica(){
	indiceCategoria = document.getElementById("categoriaProgramatica").selectedIndex;
	selectAccionEspecifica = document.getElementById("accionEspecifica");
	while(selectAccionEspecifica.options.length>0){
		selectAccionEspecifica.remove(selectAccionEspecifica.options.length-1);	
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
}

function limpiarPartida(){
	document.getElementById("partida").value="";
	document.getElementById("partida").focus();
	document.getElementById("1erTrimestre").value="";
	document.getElementById("2doTrimestre").value="";
	document.getElementById("3erTrimestre").value="";
	document.getElementById("4toTrimestre").value="";
}

function estaEnPartidasTemporales(partida){
	var i = 0;
	while(i<partidas.length){
		if(partidas[i][0]==partida){
			return true;
		}
		i++;
	}
	return false;
}

function estaEnPartidas(nombre, idPartida){
	for(j = 0; j < nombresPartidas.length; j++){
		if(trim(nombre)==trim(nombresPartidas[j]) && trim(idPartida)==trim(idsPartidas[j])){
			return j;
		}
	}
	return -1;
}

function agregarPartida(){
	if(trim(document.getElementById("partida").value)==""){
		alert("Seleccione la partida.");
		document.getElementById("partida").focus();
	}else{
		tokens = document.getElementById("partida").value.split( ":" );
		if(tokens[0] && tokens[1]){
			idPartida = trim(tokens[0]);
			nombre = trim(tokens[1]);
			indiceIdPartida = estaEnPartidas(nombre,idPartida);
			if(indiceIdPartida<0){
				alert("La partida indicada no es v"+aACUTE+"lida.");
			}else{
				if(estaEnPartidasTemporales(idPartida)==true){
					alert("La partida "+idPartida+" ya ha sido agregada previamente.");
					document.getElementById("partida").focus();
				}else{
					if(	document.getElementById("1erTrimestre").value=="" &&
						document.getElementById("2doTrimestre").value=="" &&
						document.getElementById("3erTrimestre").value=="" &&
						document.getElementById("4toTrimestre").value==""){
						alert("Debe indicar un monto al menos para un (1) trimestre.");
						document.getElementById("1erTrimestre").focus();
					}else{
						var tbody = document.getElementById('partidas');
						
						indiceGeneral = partidas.length;
						var primerTrimestre = trim(document.getElementById("1erTrimestre").value);
						var segundoTrimestre = trim(document.getElementById("2doTrimestre").value);
						var tercerTrimestre = trim(document.getElementById("3erTrimestre").value);
						var cuartoTrimestre = trim(document.getElementById("4toTrimestre").value);
						
						var registro = new Array(6);
						registro[0]=idPartida;
						registro[1]=nombre;
						registro[2]=primerTrimestre;
						registro[3]=segundoTrimestre;
						registro[4]=tercerTrimestre;
						registro[5]=cuartoTrimestre;
													
						var fila = document.createElement("tr");
						fila.className='normalNegro';
													
						//PARTIDA
						var columna1 = document.createElement("td");
						columna1.setAttribute("align","center");
						columna1.setAttribute("valign","top");
						var inputHidden = document.createElement("input");
						inputHidden.setAttribute("type","hidden");
						inputHidden.setAttribute("name","partida"+indiceGeneral);
						inputHidden.value=registro[0];
						columna1.appendChild(inputHidden);
						columna1.appendChild(document.createTextNode(registro[0]));
													
						//DENOMINACION
						var columna2 = document.createElement("td");
						columna2.setAttribute("align","left");
						columna2.setAttribute("valign","top");
						columna2.appendChild(document.createTextNode(registro[1]));
													
						//1ERTRIMESTRE
						var columna3 = document.createElement("td");
						columna3.setAttribute("align","center");
						columna3.setAttribute("valign","top");
						var inputHidden = document.createElement("input");
						inputHidden.setAttribute("type","hidden");
						inputHidden.setAttribute("name","1erTrimestre"+indiceGeneral);
						inputHidden.value=registro[2];
						columna3.appendChild(inputHidden);
						columna3.appendChild(document.createTextNode(registro[2]));
													
						//2DOTRIMESTRE
						var columna4 = document.createElement("td");
						columna4.setAttribute("align","center");
						columna4.setAttribute("valign","top");
						var inputHidden = document.createElement("input");
						inputHidden.setAttribute("type","hidden");
						inputHidden.setAttribute("name","2doTrimestre"+indiceGeneral);
						inputHidden.value=registro[3];
						columna4.appendChild(inputHidden);
						columna4.appendChild(document.createTextNode(registro[3]));
													
						//3ERTRIMESTRE
						var columna5 = document.createElement("td");
						columna5.setAttribute("align","center");
						columna5.setAttribute("valign","top");
						var inputHidden = document.createElement("input");
						inputHidden.setAttribute("type","hidden");
						inputHidden.setAttribute("name","3erTrimestre"+indiceGeneral);
						inputHidden.value=registro[4];
						columna5.appendChild(inputHidden);
						columna5.appendChild(document.createTextNode(registro[4]));
						
						//4TOTRIMESTRE
						var columna6 = document.createElement("td");
						columna6.setAttribute("align","center");
						columna6.setAttribute("valign","top");
						var inputHidden = document.createElement("input");
						inputHidden.setAttribute("type","hidden");
						inputHidden.setAttribute("name","4toTrimestre"+indiceGeneral);
						inputHidden.value=registro[5];
						columna6.appendChild(inputHidden);
						columna6.appendChild(document.createTextNode(registro[5]));
												
						//OPCION DE ELIMINAR
						var columna7 = document.createElement("td");
						columna7.setAttribute("align","center");
						columna7.setAttribute("valign","top");
						columna7.className = 'link';
						editLink = document.createElement("a");
						linkText = document.createTextNode("Eliminar");
						editLink.setAttribute("href", "javascript:eliminarPartida('"+(indiceGeneral+1)+"')");
						editLink.appendChild(linkText);
						columna7.appendChild(editLink);
								
						fila.appendChild(columna1);
						fila.appendChild(columna2);
						fila.appendChild(columna3);
						fila.appendChild(columna4);
						fila.appendChild(columna5);
						fila.appendChild(columna6);
						fila.appendChild(columna7);
						tbody.appendChild(fila); 
				
						partidas[partidas.length]=registro;
						document.getElementById('tamanoPartidas').value=partidas.length;
						limpiarPartida();
					}
				}
			}
		}else{
			alert("Seleccione una partida.");
		}
	}
}

function eliminarPartida(indice){
	var tabla = document.getElementById('tbl_mod');
	var tbody = document.getElementById('partidas');
	
	for(i=0;i<partidas.length;i++){
		tabla.deleteRow(1);
	}
	
	for(i=indice;i<partidas.length;i++){
		partidas[i-1]=partidas[i];
	}
	
	partidas.pop();
	
	document.getElementById('tamanoPartidas').value=partidas.length;
	
	for(i=0;i<partidas.length;i++){
		var registro = partidas[i];
		if(!registro[2]) registro[2] = "";
		if(!registro[3]) registro[3] = "";
		if(!registro[4]) registro[4] = "";
		if(!registro[5]) registro[5] = "";
		
		var fila = document.createElement("tr");
		fila.className='normalNegro';
		
		//PARTIDA
		var columna1 = document.createElement("td");
		columna1.setAttribute("align","center");
		columna1.setAttribute("valign","top");
		var inputHidden = document.createElement("input");
		inputHidden.setAttribute("type","hidden");
		inputHidden.setAttribute("name","partida"+i);
		inputHidden.value=registro[0];
		columna1.appendChild(inputHidden);
		columna1.appendChild(document.createTextNode(registro[0]));
									
		//DENOMINACION
		var columna2 = document.createElement("td");
		columna2.setAttribute("align","left");
		columna2.setAttribute("valign","top");
		columna2.appendChild(document.createTextNode(registro[1]));
									
		//1ERTRIMESTRE
		var columna3 = document.createElement("td");
		columna3.setAttribute("align","center");
		columna3.setAttribute("valign","top");
		var inputHidden = document.createElement("input");
		inputHidden.setAttribute("type","hidden");
		inputHidden.setAttribute("name","1erTrimestre"+i);
		inputHidden.value=registro[2];
		columna3.appendChild(inputHidden);
		columna3.appendChild(document.createTextNode(registro[2]));
									
		//2DOTRIMESTRE
		var columna4 = document.createElement("td");
		columna4.setAttribute("align","center");
		columna4.setAttribute("valign","top");
		var inputHidden = document.createElement("input");
		inputHidden.setAttribute("type","hidden");
		inputHidden.setAttribute("name","2doTrimestre"+i);
		inputHidden.value=registro[3];
		columna4.appendChild(inputHidden);
		columna4.appendChild(document.createTextNode(registro[3]));
									
		//3ERTRIMESTRE
		var columna5 = document.createElement("td");
		columna5.setAttribute("align","center");
		columna5.setAttribute("valign","top");
		var inputHidden = document.createElement("input");
		inputHidden.setAttribute("type","hidden");
		inputHidden.setAttribute("name","3erTrimestre"+i);
		inputHidden.value=registro[4];
		columna5.appendChild(inputHidden);
		columna5.appendChild(document.createTextNode(registro[4]));
		
		//4TOTRIMESTRE
		var columna6 = document.createElement("td");
		columna6.setAttribute("align","center");
		columna6.setAttribute("valign","top");
		var inputHidden = document.createElement("input");
		inputHidden.setAttribute("type","hidden");
		inputHidden.setAttribute("name","4toTrimestre"+i);
		inputHidden.value=registro[5];
		columna6.appendChild(inputHidden);
		columna6.appendChild(document.createTextNode(registro[5]));
								
		//OPCION DE ELIMINAR
		var columna7 = document.createElement("td");
		columna7.setAttribute("align","center");
		columna7.setAttribute("valign","top");
		columna7.className = 'link';
		editLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		editLink.setAttribute("href", "javascript:eliminarPartida('"+(i+1)+"')");
		editLink.appendChild(linkText);
		columna7.appendChild(editLink);

		fila.appendChild(columna1);
		fila.appendChild(columna2);
		fila.appendChild(columna3);
		fila.appendChild(columna4);
		fila.appendChild(columna5);
		fila.appendChild(columna6);
		fila.appendChild(columna7);
		tbody.appendChild(fila);
	}
}

function crear(){
	if(partidas.length==0){
		alert('Debe agregar al menos una (1) partida a la asignaci'+oACUTE+'n presupuestaria.');
		return;
	}
	
	if(confirm('Los datos han sido introducidos de manera correcta. '+pACUTE+'Desea continuar?.')){
		document.form.submit();
	}
}

function validarCodigoAccionEspecifica(formId){
	myTokens = document.getElementById("categoriaProgramatica").value.tokenize("-");
	$.ajax({
        url: "validarCodigoAccionEspecifica.php",
        dataType: "text",
        async: false,
        type: "POST",
        data: {
				anioPres: myTokens[1],
				codigo: myTokens[2],
                tipo: myTokens[0],
                codigoAccionEspecifica: document.getElementById("accionEspecifica").value,
                formId: ((formId && formId!="")?formId:"")
        },
        success: function(text){
        	if(!formId || formId==""){
	            if(text=="true"){
	                alert("La acci"+oACUTE+"n espec"+iACUTE+"fica seleccionada ya tiene una asignaci"+oACUTE+"n presupuestaria.");
				}else{
					crear();
				}
        	}else{
        		if(text=="true"){
	                alert("La acci"+oACUTE+"n espec"+iACUTE+"fica seleccionada ya tiene una asignaci"+oACUTE+"n presupuestaria.");
				}else{
					crear();
				}
        	}
        }
	});
}

function cancelar(){
	location.href="buscar.php";
}