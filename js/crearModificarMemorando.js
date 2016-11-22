var personalPara = new Array();
var personalCc = new Array();
var ACCION_MODIFICAR = "modificar";
var ACCION_ANULAR = "anular";

function limpiarPersonal(){
	document.getElementById("personal").value="";
	document.getElementById("personal").focus();
}

function estaEnPersonalPara(cedula){
	var i = 0;
	while(i<personalPara.length){
		if(personalPara[i][0]==cedula){
			return i;
		}
		i++;
	}
	return -1;
}

function estaEnPersonalCc(cedula){
	var i = 0;
	while(i<personalCc.length){
		if(personalCc[i][0]==cedula){
			return i;
		}
		i++;
	}
	return -1;
}

function estaEnPersonal(nombre, cedula){
	for(j = 0; j < nombres.length; j++){
		if(trim(nombre)==trim(nombres[j]) && trim(cedula)==trim(cedulas[j])){
			return j;
		}
	}
	return -1;
}

function agregarUnPersonal(registro, indice, paraCc, tbody, enArreglo){
	var fila = document.createElement("tr");
	fila.className='normalNegro';
	
	//CEDULA
	var columna1 = document.createElement("td");
	columna1.setAttribute("align","center");
	columna1.setAttribute("valign","top");
	var inputHidden = document.createElement("input");
	inputHidden.setAttribute("type","hidden");
	if(paraCc=="para"){
		inputHidden.setAttribute("name","cedulaPara"+indice);
	}else if(paraCc=="cc"){
		inputHidden.setAttribute("name","cedulaCc"+indice);
	}
	inputHidden.value=registro[0];
	columna1.appendChild(inputHidden);
	columna1.appendChild(document.createTextNode(registro[0]));
	
	//NOMBRE
	var columna2 = document.createElement("td");
	columna2.setAttribute("align","left");
	columna2.setAttribute("valign","top");
	columna2.appendChild(document.createTextNode(registro[1]));
	
	//CARGO
	var columna3 = document.createElement("td");
	columna3.setAttribute("align","center");
	columna3.setAttribute("valign","top");
	columna3.appendChild(document.createTextNode(registro[2]));
	
	//DEPENDENCIA
	var columna4 = document.createElement("td");
	columna4.setAttribute("align","center");
	columna4.setAttribute("valign","top");
	columna4.appendChild(document.createTextNode(registro[3]));
								
	//OPCION DE ELIMINAR
	var columna5 = document.createElement("td");
	columna5.setAttribute("align","center");
	columna5.setAttribute("valign","top");
	columna5.className = 'link';
	editLink = document.createElement("a");
	linkText = document.createTextNode("Eliminar");
	editLink.setAttribute("href", "javascript:eliminarPersonal('"+(indice+1)+"','"+paraCc+"')");
	editLink.appendChild(linkText);
	columna5.appendChild(editLink);
	
	fila.appendChild(columna1);
	fila.appendChild(columna2);
	fila.appendChild(columna3);
	fila.appendChild(columna4);
	fila.appendChild(columna5);
	tbody.appendChild(fila);
	if(enArreglo==true){
		if(paraCc=="para"){
			personalPara[personalPara.length]=registro;
			document.getElementById('tamanoPersonalPara').value=personalPara.length;
		}else if(paraCc=="cc"){
			personalCc[personalCc.length]=registro;
			document.getElementById('tamanoPersonalCc').value=personalCc.length;
		}
	}
}

function agregarPersonal(){
	if(trim(document.getElementById("personal").value)==""){
		alert("Seleccione un trabajador.");
		document.getElementById("personal").focus();
	}else{
		tokens = document.getElementById("personal").value.split( ":" );
		if(tokens[0] && tokens[1]){
			cedula = trim(tokens[0]);
			nombre = trim(tokens[1]);
			indiceCedula = estaEnPersonal(nombre,cedula);
			if(indiceCedula<0){
				alert("El trabajador indicado no es v"+aACUTE+"lido.");
			}else{
				if(estaEnPersonalPara(cedula)!=-1){
					alert("El trabajador "+nombre+" ya ha sido agregado en la secci"+oACUTE+"n 'Para:'.");
					document.getElementById("personal").focus();
				}else{
					if(estaEnPersonalCc(cedula)!=-1){
						alert("El trabajador "+nombre+" ya ha sido agregado en la secci"+oACUTE+"n 'Cc:'.");
						document.getElementById("personal").focus();
					}else{
						if(document.getElementById("paraCc").value=="para"){
							var tbody = document.getElementById('bodyParas');
							indiceGeneral = personalPara.length;
						}else if(document.getElementById("paraCc").value=="cc"){
							var tbody = document.getElementById('bodyCcs');
							indiceGeneral = personalCc.length;
						}						
						
						var registro = new Array(4);
						registro[0]=cedula;
						registro[1]=nombre;
						registro[2]=cargos[indiceCedula];
						registro[3]=dependencias[indiceCedula];
											
						agregarUnPersonal(registro, indiceGeneral, document.getElementById("paraCc").value, tbody, true);
				
						limpiarPersonal();
					}
				}
			}
		}else{
			alert("Seleccione un trabajador.");
		}
	}
}

function eliminarPersonal(indice,paraCc){
	var personalTemporal;
	if(paraCc=="para"){
		var tabla = document.getElementById('tbl_mod_paras');
		var tbody = document.getElementById('bodyParas');		
		for(i=0;i<personalPara.length;i++){
			tabla.deleteRow(1);
		}		
		for(i=indice;i<personalPara.length;i++){
			personalPara[i-1]=personalPara[i];
		}		
		personalPara.pop();
		document.getElementById('tamanoPersonalPara').value=personalPara.length;
		personalTemporal = personalPara;
	}else if(paraCc=="cc"){
		var tabla = document.getElementById('tbl_mod_ccs');
		var tbody = document.getElementById('bodyCcs');		
		for(i=0;i<personalCc.length;i++){
			tabla.deleteRow(1);
		}		
		for(i=indice;i<personalCc.length;i++){
			personalCc[i-1]=personalCc[i];
		}		
		personalCc.pop();
		document.getElementById('tamanoPersonalCc').value=personalCc.length;
		personalTemporal = personalCc;
	}
	
	hayPresidente = false;
	hayDirectorEjecutivo = false;
	hayGerenteDirector = false;
	hayJefe = false;
	hayCoordinador = false;
	for(i=0;i<personalTemporal.length;i++){
		agregarUnPersonal(personalTemporal[i], i, paraCc, tbody, false);
		k=0;
		while(k<cedulasPresidentes.length){
			if(k>19){
				break;
			}
			if(cedulasPresidentes[k]==personalTemporal[i][0]){
				hayPresidente = true;
				break;
			}
			k++;
		}
		
		k=0;
		while(k<cedulasDirectorEjecutivo.length){
			if(cedulasDirectorEjecutivo[k]==personalTemporal[i][0]){
				hayDirectorEjecutivo = true;
				break;
			}
			k++;
		}
		
		k=0;
		while(k<cedulasGerenteDirector.length){
			if(cedulasGerenteDirector[k]==personalTemporal[i][0]){
				hayGerenteDirector = true;
				break;
			}
			k++;
		}
		
		k=0;
		while(k<cedulasJefe.length){
			if(cedulasJefe[k]==personalTemporal[i][0]){
				hayJefe = true;
				break;
			}
			k++;
		}
		
		k=0;
		while(k<cedulasCoordinador.length){
			if(cedulasCoordinador[k]==personalTemporal[i][0]){
				hayCoordinador = true;
				break;
			}
			k++;
		}
	}
	
	if(hayPresidente==false){
		if(paraCc=="para"){
			document.getElementById("PRpara").checked = false;
			document.getElementById("PRcc").disabled = false;
		}else if(paraCc=="cc"){
			document.getElementById("PRcc").checked = false;
			document.getElementById("PRpara").disabled = false;
		}
	}
	
	if(hayDirectorEjecutivo==false){
		if(paraCc=="para"){
			document.getElementById("DEpara").checked = false;
			document.getElementById("DEcc").disabled = false;
		}else if(paraCc=="cc"){
			document.getElementById("DEcc").checked = false;
			document.getElementById("DEpara").disabled = false;
		}
	}
	
	if(hayGerenteDirector==false){
		if(paraCc=="para"){
			document.getElementById("GDpara").checked = false;
			document.getElementById("GDcc").disabled = false;
		}else if(paraCc=="cc"){
			document.getElementById("GDcc").checked = false;
			document.getElementById("GDpara").disabled = false;
		}
	}
	
	if(hayJefe==false){
		if(paraCc=="para"){
			document.getElementById("JEpara").checked = false;
			document.getElementById("JEcc").disabled = false;
		}else if(paraCc=="cc"){
			document.getElementById("JEcc").checked = false;
			document.getElementById("JEpara").disabled = false;
		}
	}
	
	if(hayCoordinador==false){
		if(paraCc=="para"){
			document.getElementById("COpara").checked = false;
			document.getElementById("COcc").disabled = false;
		}else if(paraCc=="cc"){
			document.getElementById("COcc").checked = false;
			document.getElementById("COpara").disabled = false;
		}
	}
}

function agregarGrupo(elemento, paraCc){
	var prefijo = "";
	var cedulasTemporal;
	var nombresTemporal;
	var cargosTemporal;
	var dependenciasTemporal;
	if(elemento.value=="PR"){
		cedulasTemporal = cedulasPresidentes;
		nombresTemporal = nombresPresidentes;
		cargosTemporal = cargosPresidentes;
		dependenciasTemporal = dependenciasPresidentes;
		prefijo = "PR";
	}else if(elemento.value=="DE"){
		cedulasTemporal = cedulasDirectorEjecutivo;
		nombresTemporal = nombresDirectorEjecutivo;
		cargosTemporal = cargosDirectorEjecutivo;
		dependenciasTemporal = dependenciasDirectorEjecutivo;
		prefijo = "DE";
	}else if(elemento.value=="GD"){
		cedulasTemporal = cedulasGerenteDirector;
		nombresTemporal = nombresGerenteDirector;
		cargosTemporal = cargosGerenteDirector;
		dependenciasTemporal = dependenciasGerenteDirector;
		prefijo = "GD";
	}else if(elemento.value=="JE"){
		cedulasTemporal = cedulasJefe;
		nombresTemporal = nombresJefe;
		cargosTemporal = cargosJefe;
		dependenciasTemporal = dependenciasJefe;
		prefijo = "JE";
	}else if(elemento.value=="CO"){
		cedulasTemporal = cedulasCoordinador;
		nombresTemporal = nombresCoordinador;
		cargosTemporal = cargosCoordinador;
		dependenciasTemporal = dependenciasCoordinador;
		prefijo = "CO";
	}
	
	if(elemento.checked==true){
		if(paraCc=="para"){
			document.getElementById(prefijo+"cc").disabled = true;
			var tbody = document.getElementById('bodyParas');
		}else if(paraCc=="cc"){
			document.getElementById(prefijo+"para").disabled = true;
			var tbody = document.getElementById('bodyCcs');
		}
		for(j=0;j<cedulasTemporal.length;j++){
			if(estaEnPersonalPara(cedulasTemporal[j])==-1 && estaEnPersonalCc(cedulasTemporal[j])==-1){
				var registro = new Array(4);
				registro[0]=cedulasTemporal[j];
				registro[1]=nombresTemporal[j];
				registro[2]=cargosTemporal[j];
				registro[3]=dependenciasTemporal[j];
				if(paraCc=="para"){
					agregarUnPersonal(registro, personalPara.length, paraCc, tbody, true);
				}else if(paraCc=="cc"){
					agregarUnPersonal(registro, personalCc.length, paraCc, tbody, true);
				}
			}
		}
	}else{
		for(j=0;j<cedulasTemporal.length;j++){
			if(paraCc=="para"){
				indiceTemporal = estaEnPersonalPara(cedulasTemporal[j]);
			}else if(paraCc=="cc"){
				indiceTemporal = estaEnPersonalCc(cedulasTemporal[j]);
			}
			if(indiceTemporal!=-1){
				eliminarPersonal(indiceTemporal+1,paraCc);
			}
		}
		if(paraCc=="para"){
			document.getElementById(prefijo+"cc").disabled = false;
		}else if(paraCc=="cc"){
			document.getElementById(prefijo+"para").disabled = false;
		}
	}
}

function crear(){
	if(personalPara.length==0){
		alert('Debe agregar en la secci'+oACUTE+'n \"Para:\" de la comunicaci'+oACUTE+'n al menos un (1) trabajador.');
		return;
	}

	 descripcionlegth = $('#pcuenta_descripcion').elrte('val').length;

		if(descripcionlegth < 5 )
			
		{  	
			alert("Debe especificar la descripcion del Punto de Cuenta  y este s\u00f3lo puede tener caracteres alfanum\u00E9ricos");
			
			return;
		}
		if(document.getElementById("fecha").value==""){
			alert("Debe especificar la fecha de la comunicaci"+oACUTE+"n.");
			document.getElementById("fecha").focus();
			return;
		}
		
		if(document.getElementById("asunto").value==""){
			alert("Debe especificar el asunto.");
			document.getElementById("inputSelectMemorandoAsuntos").focus();
			return;
		}
		
	
	if(document.getElementById("despedida").value==""){
		alert("Debe especificar la despedida de la comunicaci"+oACUTE+"n.");
		document.getElementById("despedida").focus();
		return;
	}
	
	if(confirm('Los datos han sido introducidos de manera correcta. '+pACUTE+'Desea continuar?.')){
		
		$('#pcuenta_descripcionVal').val($('#pcuenta_descripcion').elrte('val'));
		if(document.getElementById("accion")){
			document.getElementById("accion").value = ACCION_MODIFICAR;
		}
		document.form.submit();
	}
}

function cancelar(tipo){
	location.href="buscar.php?tipo="+tipo;
}

function anular(){
	if(confirm(''+pACUTE+'Est'+aACUTE+' seguro que desea anular la comunicaci'+oACUTE+'n?.')){
		document.getElementById("accion").value = ACCION_ANULAR;
		document.form.submit();
	}
}

function onLoad(){
	var objInputMemorandoAsuntos = $("#inputSelectMemorandoAsuntos");
	var sendInputIdMemorandoAsunto = "#asunto";
	var errorIdMemorandoAsunto = "#errorAsunto";

	objInputMemorandoAsuntos.autocomplete({
		source: function(request, response){
			$.ajax({
				url: "../../acciones/memo/memo.php",
				dataType: "json",
				data: {
					accion: "Search",
					key: request.term
				},
				success: function(json){
					var index = 0;
					var items = new Array();
					$(sendInputIdMemorandoAsunto)[0].value="";
					$(errorIdMemorandoAsunto)[0].innerHTML="";
					
					$.each(json.listaMemorandoAsuntos, function(idMemorandoAsunto, objMemorandoAsunto){
						items[index++] = {
								id: idMemorandoAsunto,
								label: objMemorandoAsunto.nombre,
								value: objMemorandoAsunto.nombre
						};
					});
					if(items.length==0){
						$(errorIdMemorandoAsunto)[0].innerHTML="Asunto inv&aacute;lido.";
					}
					response(items);
				}
			});
		},
		minLength: 1,
		select: function(event, ui) {
			seleccionarItem({
				id: ui.item.id,
				nombre: ui.item.value,
				sendInputId: sendInputIdMemorandoAsunto,
				objInput: objInputMemorandoAsuntos
			});
			return false;
		}
	});

	function seleccionarItem(params){
		$(params['sendInputId'])[0].value=params['id'];
		$(params['objInput'])[0].value=params['nombre'];
	}
}