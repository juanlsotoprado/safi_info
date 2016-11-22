var partidas = new Array();

function eliminarPartida(indice){
	var tabla = document.getElementById('tablaPartidas');
	var tbody = document.getElementById('bodyPartidas');
	
	for(i=0;i<partidas.length;i++){
		tabla.deleteRow(1);
	}
	
	for(i=indice;i<partidas.length;i++){
		partidas[i-1]=partidas[i];
	}
	
	partidas.pop();
	
	for(i=0;i<partidas.length;i++){
		var fila = document.createElement("tr");
		
		//CODIGO DE LA PARTIDA
		var columna1 = document.createElement("td");
		var ramoSecundario = document.createElement("input");
		ramoSecundario.setAttribute("type", "hidden");
		ramoSecundario.setAttribute("name", "ramo_secundario[]");
		ramoSecundario.setAttribute("value", partidas[i][0]);
		columna1.appendChild(ramoSecundario);
		columna1.appendChild(document.createTextNode(partidas[i][0]));
		
		//DENOMINACION DE LA PARTIDA
		var columna2 = document.createElement("td");
		columna2.appendChild(document.createTextNode(partidas[i][1]));
		
		//OPCION DE ELIMINAR
		var columna3 = document.createElement("td");
		columna3.className = 'link';
		deleteLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		deleteLink.setAttribute("href", "javascript:eliminarPartida('"+(i+1)+"')");
		deleteLink.appendChild(linkText);
		columna3.appendChild (deleteLink);

		fila.appendChild(columna1);
		fila.appendChild(columna2);
		fila.appendChild(columna3);
		tbody.appendChild(fila); 	
	}
}

function estaEnPartidasTemporales(idPartida){
	for(j = 0; j < partidas.length; j++){
		if(idPartida==partidas[j][0]){
			return true;
		}
	}
	return false;
}

function estaEnPartidas(idPartida){
	for(j = 0; j < partidasAMostrar.length; j++){
		partidaAMostrar = partidasAMostrar[j].split(":");
		if(trim(idPartida)==trim(partidaAMostrar[0])){
			return j;
		}
	}
	return -1;
}

function agregarPartida(){
	if(trim(document.getElementById("partida").value)==""){
		alert("Introduzca el rubro o una palabra contenida en el nombre del mismo.");
		document.getElementById("partida").focus();
	}else{
		tokens = document.getElementById("partida").value.split(":");
		if(tokens[0]){
			idPartida = trim(tokens[0]);
			indiceIdPartida = estaEnPartidas(idPartida);
			if(indiceIdPartida>-1){
				if( estaEnPartidasTemporales(idPartida) == false ){
					var tbody = document.getElementById('bodyPartidas');
					indiceGeneral = partidas.length;
					nombrePartida = nombresPartidas[indiceIdPartida];
					
					var registro = new Array(2);
					registro[0]=idPartida;
					registro[1]=nombrePartida;
					
					var fila = document.createElement("tr");
					
					//CODIGO DE LA PARTIDA
					var columna1 = document.createElement("td");
					var ramoSecundario = document.createElement("input");
					ramoSecundario.setAttribute("type", "hidden");
					ramoSecundario.setAttribute("name", "ramo_secundario[]");
					ramoSecundario.setAttribute("value", registro[0]);
					columna1.appendChild(ramoSecundario);
					columna1.appendChild(document.createTextNode(registro[0]));
					
					//DENOMINACION DE LA PARTIDA
					var columna2 = document.createElement("td");
					columna2.appendChild(document.createTextNode(registro[1]));
					
					//OPCION DE ELIMINAR
					var columna3 = document.createElement("td");
					columna3.className = 'link';
					deleteLink = document.createElement("a");
					linkText = document.createTextNode("Eliminar");
					deleteLink.setAttribute("href", "javascript:eliminarPartida('"+(indiceGeneral+1)+"')");
					deleteLink.appendChild(linkText);
					columna3.appendChild(deleteLink);
					
					fila.appendChild(columna1);
					fila.appendChild(columna2);
					fila.appendChild(columna3);
					tbody.appendChild(fila); 
	
					partidas[partidas.length]=registro;
	
					limpiarPartida();
				}else{
					alert("Esta partida ya ha sido agregada");
				}
			}else{
				alert("La partida o el nombre del rubro indicado no es v"+aACUTE+"lido");
			}
		}else{
			alert("Seleccione un rubro");
		}
	}
}

function limpiarPartida(){
	document.getElementById("partida").value="";
	document.getElementById("partida").focus();
}
