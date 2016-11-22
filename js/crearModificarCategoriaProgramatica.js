var accionesEspecificas = new Array();
var indiceAnioPresupuestarioAnterior = 0;
var TIPO_PROYECTO = 1;
var TIPO_ACCION_CENTRALIZADA = 0;
var tipoImputacion = TIPO_PROYECTO;

function mostrarCampos(tipo){
	tipoImputacion = tipo;
	if(tipo==TIPO_PROYECTO){//Proyecto
		document.getElementById("descripcion").disabled=false;
		document.getElementById("objetivos").disabled=false;
		document.getElementById("resultado").disabled=false;
	}else if(tipo==TIPO_ACCION_CENTRALIZADA){//Acción centralizada
		document.getElementById("descripcion").disabled=true;
		document.getElementById("objetivos").disabled=true;
		document.getElementById("resultado").disabled=true;
	}
}

function validarAnio(){
	if(accionesEspecificas.length>0){
		document.getElementById("anioPresupuestario").selectedIndex = indiceAnioPresupuestarioAnterior;
		alert("No puede cambiar el a"+nTILDE+"o presupuestario porque ya se han agregado acciones espec"+iACUTE+"ficas de un a"+nTILDE+"o diferente. Si desea cambiar el a"+nTILDE+"o presupuestario debe eliminar todas las acciones espec"+iACUTE+"ficas.");
		return;
	}
	indiceAnioPresupuestarioAnterior = document.getElementById("anioPresupuestario").selectedIndex;
}

function limpiarAccionEspecifica(){
	document.getElementById("nombreAccionEspecifica").value="";
	document.getElementById("nombreAccionEspecifica").focus();
	document.getElementById("codigoAccionEspecifica").value="";
	document.getElementById("centroGestor").value="";
	document.getElementById("centroCostos").value="";
}

function estaEnCodigos(codigo){
	var i = 0;
	while(i<accionesEspecificas.length){
		if(accionesEspecificas[i][2]==codigo){
			return true;
		}
		i++;
	}
	return false;
}

function estaEnCentrosGestores(centroGestor){
	var i = 0;
	while(i<accionesEspecificas.length){
		if(accionesEspecificas[i][3]==centroGestor){
			return true;
		}
		i++;
	}
	return false;
}

function estaEnCentrosCostos(centroCostos){
	var i = 0;
	while(i<accionesEspecificas.length){
		if(accionesEspecificas[i][4]==centroCostos){
			return true;
		}
		i++;
	}
	return false;
}

function agregarAccionEspecifica(){
	if(trim(document.getElementById("nombreAccionEspecifica").value)==""){
		alert("Introduzca el nombre de la acci"+oACUTE+"n espec"+iACUTE+"fica.");
		document.getElementById("nombreAccionEspecifica").focus();
	}else{
		if(validarFechaCategoria()==true){
			if(trim(document.getElementById("codigoAccionEspecifica").value)==""){
				alert("Introduzca el c"+oACUTE+"digo de la acci"+oACUTE+"n espec"+iACUTE+"fica.");
				document.getElementById("codigoAccionEspecifica").focus();
			}else{
				if(estaEnCodigos(trim(document.getElementById("codigoAccionEspecifica").value))==true){
					alert("No puede agregar m"+aACUTE+"s de una acci"+oACUTE+"n espec"+iACUTE+"fica con el mismo c"+oACUTE+"digo.");
					document.getElementById("codigoAccionEspecifica").focus();
				}else{
					if(trim(document.getElementById("centroGestor").value)==""){
						alert("Introduzca el centro gestor.");
						document.getElementById("centroGestor").focus();
					}else{
						/*if(estaEnCentrosGestores(trim(document.getElementById("centroGestor").value))==true){
							alert("No puede agregar m"+aACUTE+"s de una acci"+oACUTE+"n espec"+iACUTE+"fica con el mismo centro gestor.");
							document.getElementById("centroGestor").focus();
						}else{*/
							if(trim(document.getElementById("centroCostos").value)==""){
								alert("Introduzca el centro de costos.");
								document.getElementById("centroCostos").focus();	
							}else{
								if(estaEnCentrosCostos(trim(document.getElementById("centroCostos").value))==true){
									alert("No puede agregar m"+aACUTE+"s de una acci"+oACUTE+"n espec"+iACUTE+"fica con el mismo centro de costos.");
									document.getElementById("centroCostos").focus();
								}else{
									if(validarCodigoAccionEspecifica()==true){
										var tbody = document.getElementById('accionEspecifica');
										
										indiceGeneral = accionesEspecificas.length;
										nombre = trim(document.getElementById("nombreAccionEspecifica").value);
										fechaInicio = trim(document.getElementById("fechaInicio").value);
										codigo = trim(document.getElementById("codigoAccionEspecifica").value);
										centroGestor = trim(document.getElementById("centroGestor").value);
										centroCostos = trim(document.getElementById("centroCostos").value);
										
										var registro = new Array(5);
										registro[0]=fechaInicio;
										registro[1]=nombre;
										registro[2]=codigo;
										registro[3]=centroGestor;
										registro[4]=centroCostos;
										
										var fila = document.createElement("tr");
										fila.className='normalNegro';
										
										//FECHA INICIO
										var columna1 = document.createElement("td");
										columna1.setAttribute("align","center");
										columna1.setAttribute("valign","top");
										var inputHidden = document.createElement("input");
										inputHidden.setAttribute("type","hidden");
										inputHidden.setAttribute("name","fechaInicio"+indiceGeneral);
										inputHidden.value=registro[0];
										columna1.appendChild(inputHidden);
										columna1.appendChild(document.createTextNode(registro[0]));
										
										//NOMBRE ACCION ESPECIFICA
										var columna2 = document.createElement("td");
										columna2.setAttribute("align","left");
										columna2.setAttribute("valign","top");
										var inputHidden = document.createElement("input");
										inputHidden.setAttribute("type","hidden");
										inputHidden.setAttribute("name","nombreAccionEspecifica"+indiceGeneral);
										inputHidden.value=registro[1];
										columna2.appendChild(inputHidden);
										columna2.appendChild(document.createTextNode(registro[1]));
										
										//CODIGO ACCION ESPECIFICA
										var columna3 = document.createElement("td");
										columna3.setAttribute("align","center");
										columna3.setAttribute("valign","top");
										var inputHidden = document.createElement("input");
										inputHidden.setAttribute("type","hidden");
										inputHidden.setAttribute("name","codigoAccionEspecifica"+indiceGeneral);
										inputHidden.value=registro[2];
										columna3.appendChild(inputHidden);
										columna3.appendChild(document.createTextNode(registro[2]));
										
										//CENTRO GESTOR
										var columna4 = document.createElement("td");
										columna4.setAttribute("align","center");
										columna4.setAttribute("valign","top");
										var inputHidden = document.createElement("input");
										inputHidden.setAttribute("type","hidden");
										inputHidden.setAttribute("name","centroGestor"+indiceGeneral);
										inputHidden.value=registro[3];
										columna4.appendChild(inputHidden);
										columna4.appendChild(document.createTextNode(registro[3]));
										
										//CENTRO COSTOS
										var columna5 = document.createElement("td");
										columna5.setAttribute("align","center");
										columna5.setAttribute("valign","top");
										var inputHidden = document.createElement("input");
										inputHidden.setAttribute("type","hidden");
										inputHidden.setAttribute("name","centroCostos"+indiceGeneral);
										inputHidden.value=registro[4];
										columna5.appendChild(inputHidden);
										columna5.appendChild(document.createTextNode(registro[4]));
									
										//OPCION DE ELIMINAR
										var columna6 = document.createElement("td");
										columna6.setAttribute("align","center");
										columna6.setAttribute("valign","top");
										columna6.className = 'link';
										editLink = document.createElement("a");
										linkText = document.createTextNode("Eliminar");
										editLink.setAttribute("href", "javascript:eliminarAccionEspecifica('"+(indiceGeneral+1)+"')");
										editLink.appendChild(linkText);
										columna6.appendChild (editLink);
					
										fila.appendChild(columna1);
										fila.appendChild(columna2);
										fila.appendChild(columna3);
										fila.appendChild(columna4);
										fila.appendChild(columna5);
										fila.appendChild(columna6);
										tbody.appendChild(fila); 
					
										accionesEspecificas[accionesEspecificas.length]=registro;
										document.getElementById('tamanoAccionesEspecificas').value=accionesEspecificas.length;
										limpiarAccionEspecifica();
									}									
								}
							}	
						/*}*/
					}
				}
			}
		}
	}
}

function eliminarAccionEspecifica(indice){
	var tabla = document.getElementById('tbl_mod');
	var tbody = document.getElementById('accionEspecifica');
	
	for(i=0;i<accionesEspecificas.length;i++){
		tabla.deleteRow(1);
	}
	
	for(i=indice;i<accionesEspecificas.length;i++){
		accionesEspecificas[i-1]=accionesEspecificas[i];
	}
	
	accionesEspecificas.pop();
	
	document.getElementById('tamanoAccionesEspecificas').value=accionesEspecificas.length;
	
	for(i=0;i<accionesEspecificas.length;i++){
		var registro = accionesEspecificas[i];
		var fila = document.createElement("tr");
		fila.className='normalNegro';
		
		//FECHA INICIO
		var columna1 = document.createElement("td");
		columna1.setAttribute("align","center");
		columna1.setAttribute("valign","top");
		var inputHidden = document.createElement("input");
		inputHidden.setAttribute("type","hidden");
		inputHidden.setAttribute("name","fechaInicio"+i);
		inputHidden.value=registro[0];
		columna1.appendChild(inputHidden);
		columna1.appendChild(document.createTextNode(registro[0]));
		
		//NOMBRE ACCION ESPECIFICA
		var columna2 = document.createElement("td");
		columna2.setAttribute("align","left");
		columna2.setAttribute("valign","top");
		var inputHidden = document.createElement("input");
		inputHidden.setAttribute("type","hidden");
		inputHidden.setAttribute("name","nombreAccionEspecifica"+i);
		inputHidden.value=registro[1];
		columna2.appendChild(inputHidden);
		columna2.appendChild(document.createTextNode(registro[1]));
		
		//CODIGO ACCION ESPECIFICA
		var columna3 = document.createElement("td");
		columna3.setAttribute("align","center");
		columna3.setAttribute("valign","top");
		var inputHidden = document.createElement("input");
		inputHidden.setAttribute("type","hidden");
		inputHidden.setAttribute("name","codigoAccionEspecifica"+i);
		inputHidden.value=registro[2];
		columna3.appendChild(inputHidden);
		columna3.appendChild(document.createTextNode(registro[2]));
		
		//CENTRO GESTOR
		var columna4 = document.createElement("td");
		columna4.setAttribute("align","center");
		columna4.setAttribute("valign","top");
		var inputHidden = document.createElement("input");
		inputHidden.setAttribute("type","hidden");
		inputHidden.setAttribute("name","centroGestor"+i);
		inputHidden.value=registro[3];
		columna4.appendChild(inputHidden);
		columna4.appendChild(document.createTextNode(registro[3]));
		
		//CENTRO COSTOS
		var columna5 = document.createElement("td");
		columna5.setAttribute("align","center");
		columna5.setAttribute("valign","top");
		var inputHidden = document.createElement("input");
		inputHidden.setAttribute("type","hidden");
		inputHidden.setAttribute("name","centroCostos"+i);
		inputHidden.value=registro[4];
		columna5.appendChild(inputHidden);
		columna5.appendChild(document.createTextNode(registro[4]));
	
		//OPCION DE ELIMINAR
		var columna6 = document.createElement("td");
		columna6.setAttribute("align","center");
		columna6.setAttribute("valign","top");
		columna6.className = 'link';
		editLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		editLink.setAttribute("href", "javascript:eliminarAccionEspecifica('"+(i+1)+"')");
		editLink.appendChild(linkText);
		columna6.appendChild(editLink);

		fila.appendChild(columna1);
		fila.appendChild(columna2);
		fila.appendChild(columna3);
		fila.appendChild(columna4);
		fila.appendChild(columna5);
		fila.appendChild(columna6);
		tbody.appendChild(fila);
	}
}

function crear(){
	if(document.getElementById("codigo").value==""){
		alert('Debe indicar el c'+oACUTE+'digo de la categor'+iACUTE+'a program'+aACUTE+'tica.');
		document.getElementById("codigo").focus();
		return;
	}
	
	if(document.getElementById("titulo").value==""){
		alert('Debe indicar el t'+iACUTE+'tulo de la categor'+iACUTE+'a program'+aACUTE+'tica.');
		document.getElementById("titulo").focus();
		return;
	}
	
	if(tipoImputacion==TIPO_PROYECTO){
		if(document.getElementById("descripcion").value==""){
			alert('Debe indicar la descripci'+oACUTE+'n de la categor'+iACUTE+'a program'+aACUTE+'tica.');
			document.getElementById("descripcion").focus();
			return;
		}
		if(document.getElementById("objetivos").value==""){
			alert('Debe indicar los objetivos de la categor'+iACUTE+'a program'+aACUTE+'tica.');
			document.getElementById("objetivos").focus();
			return;
		}
	}	
	
	if(accionesEspecificas.length==0) {
		alert('Debe asociar al menos una acci'+oACUTE+'n espec'+iACUTE+'fica a la categor'+iACUTE+'a program'+aACUTE+'tica');
		return;
	}
	
	if(confirm('Los datos han sido introducidos de manera correcta. '+pACUTE+'Desea continuar?.')){
		document.form.submit();
	}
}

function validarCodigoCategoria(){
	$.ajax({
        url: "validarCodigoCategoria.php",
        dataType: "text",
        async: false,
        type: "POST",
        data: {
                anioPres: document.getElementById("anioPresupuestario").value,
                codigo: document.getElementById("codigo").value,
                tipo: tipoImputacion
        },
        success: function(text){
            if(text=="true" && tipoImputacion==TIPO_PROYECTO){
                alert("El c"+oACUTE+"digo ingresado ya est"+aACUTE+" asociado a otro proyecto, ingrese otro c"+oACUTE+"digo.");
                codigo: document.getElementById("codigo").focus();
			}else if(text=="true" && tipoImputacion==TIPO_ACCION_CENTRALIZADA){
                alert("El c"+oACUTE+"digo ingresado ya est"+aACUTE+" asociado a otra acci"+oACUTE+"n centralizada, ingrese otro c"+oACUTE+"digo.");
                codigo: document.getElementById("codigo").focus();
			}else{
				crear();
			}
        }
	});
}

function validarCodigoAccionEspecifica(){
	var resultado = true;
	$.ajax({
        url: "validarCodigoAccionEspecifica.php",
        dataType: "text",
        async: false,
        type: "POST",
        data: {
        		tipo: tipoImputacion,
        		codigo: ((document.getElementById("categoria") && document.getElementById("categoria").value!="")?document.getElementById("categoria").value:""),
                anioPres: document.getElementById("anioPresupuestario").value,
                codigoAccionEspecifica: document.getElementById("codigoAccionEspecifica").value,
                centroGestor: document.getElementById("centroGestor").value,
                centroCostos: document.getElementById("centroCostos").value
        },
        success: function(text){
            if(text=="codigo" && tipoImputacion==TIPO_PROYECTO){
                alert("Ya existe un proyecto del mismo a"+nTILDE+"o presupuestario con una acci"+oACUTE+"n espec"+iACUTE+"fica con el mismo c"+oACUTE+"digo, ingrese otro c"+oACUTE+"digo de acci"+oACUTE+"n espec"+iACUTE+"fica.");
                codigo: document.getElementById("codigoAccionEspecifica").focus();
                resultado = false;
			}else if(text=="centroGestor" && tipoImputacion==TIPO_PROYECTO){
                alert("Ya existe un proyecto del mismo a"+nTILDE+"o presupuestario con una acci"+oACUTE+"n espec"+iACUTE+"fica con el mismo centro gestor, ingrese otro centro gestor.");
                codigo: document.getElementById("centroGestor").focus();
                resultado = false;
			}else if(text=="centroCostos" && tipoImputacion==TIPO_PROYECTO){
                alert("Ya existe un proyecto del mismo a"+nTILDE+"o presupuestario con una acci"+oACUTE+"n espec"+iACUTE+"fica con el mismo centro de costos, ingrese otro centro de costos.");
                codigo: document.getElementById("centroCostos").focus();
                resultado = false;
			}else if(text=="codigo" && tipoImputacion==TIPO_ACCION_CENTRALIZADA){
                alert("Ya existe una acci"+oACUTE+"n centralizada del mismo a"+nTILDE+"o presupuestario con una acci"+oACUTE+"n espec"+iACUTE+"fica con el mismo c"+oACUTE+"digo, ingrese otro c"+oACUTE+"digo de acci"+oACUTE+"n espec"+iACUTE+"fica.");
                codigo: document.getElementById("codigoAccionEspecifica").focus();
                resultado = false;
			}else if(text=="centroGestor" && tipoImputacion==TIPO_ACCION_CENTRALIZADA){
                alert("Ya existe una acci"+oACUTE+"n centralizada del mismo a"+nTILDE+"o presupuestario con una acci"+oACUTE+"n espec"+iACUTE+"fica con el mismo centro gestor, ingrese otro centro gestor.");
                codigo: document.getElementById("centroGestor").focus();
                resultado = false;
			}else if(text=="centroCostos" && tipoImputacion==TIPO_ACCION_CENTRALIZADA){
                alert("Ya existe una acci"+oACUTE+"n centralizada del mismo a"+nTILDE+"o presupuestario con una acci"+oACUTE+"n espec"+iACUTE+"fica con el mismo centro de costos, ingrese otro centro de costos.");
                codigo: document.getElementById("centroCostos").focus();
                resultado = false;
			}
        }
	});
	return resultado;
}