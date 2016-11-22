var partidas = new Array();
var articulos= new Array();
var pctaPartidas= new Array();
var pctaImputaciones= new Array();
var dependencia = "";
var tipoReqActual = "";
var myTime;
var indicePctaActual = 0;

function estaEnArticulos(articulo){
	var i = 0;
	while(i<articulos.length){
		if(articulos[i]==articulo){
			return true;
		}
		i++;
	}
	return false;
}

function elimina_pda(indice){
	var tabla = document.getElementById('tbl_mod');
	var tbody = document.getElementById('item');
	
	for(i=0;i<partidas.length;i++){
		tabla.deleteRow(1);
	}
	
	for(i=indice;i<partidas.length;i++){
		partidas[i-1]=partidas[i];
		articulos[i-1]=partidas[i][0];
	}
	
	partidas.pop();
	articulos.pop();
	
	document.getElementById('hid_largo').value=partidas.length;
	
	for(i=0;i<partidas.length;i++){
		var fila = document.createElement("tr");
		fila.className='normalNegro';
		
		//CODIGO DEL PRODUCTO
		var columna1 = document.createElement("td");
		columna1.setAttribute("align","center");
		columna1.setAttribute("valign","top");
		columna1.setAttribute("width","10%");
		var inputIdItem = document.createElement("INPUT");
		inputIdItem.setAttribute("type","hidden");
		inputIdItem.setAttribute("name","txt_id_art"+i);
		inputIdItem.setAttribute("readOnly","true");
		inputIdItem.value=partidas[i][0];
		inputIdItem.size='5';
		inputIdItem.className='normalNegro';
		columna1.appendChild(inputIdItem);
		columna1.appendChild(document.createTextNode(partidas[i][0]));
		
		//DENOMINACION DEL PRODUCTO
		var columna2 = document.createElement("td");
		columna2.setAttribute("align","left");
		columna2.setAttribute("valign","top");
		columna2.setAttribute("width","15%");
		var inputNombreItem = document.createElement("INPUT");
		inputNombreItem.setAttribute("type","hidden");
		inputNombreItem.setAttribute("name","txt_nb_art"+i);
		inputNombreItem.setAttribute("readOnly","true");
		inputNombreItem.value=partidas[i][1];
		inputNombreItem.size='25';
		inputNombreItem.className='normalNegro';
		columna2.appendChild(inputNombreItem);
		columna2.appendChild(document.createTextNode(partidas[i][1]));
		
		//CODIGO DE LA PARTIDA
		var columna3 = document.createElement("td");
		columna3.setAttribute("align","center");
		columna3.setAttribute("valign","top");
		columna3.setAttribute("width","10%");
		var inputIdPartida = document.createElement("INPUT");
		inputIdPartida.setAttribute("type","hidden");
		inputIdPartida.setAttribute("readOnly","true");
		inputIdPartida.setAttribute("name","txt_id_pda"+i);
		inputIdPartida.value=partidas[i][2];
		inputIdPartida.size='9';
		inputIdPartida.className='normalNegro';
		columna3.appendChild(inputIdPartida);
		columna3.appendChild(document.createTextNode(partidas[i][2]));
		
		//DENOMINACION DE LA PARTIDA
		var columna4 = document.createElement("td");
		columna4.setAttribute("align","left");
		columna4.setAttribute("valign","top");
		columna4.setAttribute("width","15%");
		var inputNombrePartida = document.createElement("INPUT");
		inputNombrePartida.setAttribute("type","hidden");
		inputNombrePartida.setAttribute("name","txt_nb_pda"+i);
		inputNombrePartida.setAttribute("readOnly","true");
		inputNombrePartida.value=partidas[i][3];
		inputNombrePartida.size='25';
		inputNombrePartida.className='normalNegro';
		columna4.appendChild(inputNombrePartida);
		columna4.appendChild(document.createTextNode(partidas[i][3]));
		
		//DESCRIPCION
		var columna5 = document.createElement("td");
		columna5.setAttribute("align","left");
		columna5.setAttribute("valign","top");
		columna5.setAttribute("width","30%");
		var inputEspecificaciones = document.createElement("INPUT");
		inputEspecificaciones.setAttribute("type","hidden");
		inputEspecificaciones.setAttribute("id","txt_prod"+i);
		inputEspecificaciones.setAttribute("name","txt_prod"+i);
		inputEspecificaciones.setAttribute("readOnly","true");
		inputEspecificaciones.value=partidas[i][4];
		inputEspecificaciones.size='34';
		inputEspecificaciones.className='normalNegro';
		columna5.appendChild(inputEspecificaciones);
		var divEspecificaciones = document.createElement("div");
		divEspecificaciones.setAttribute("id","divEspecificaciones"+i);
		divEspecificaciones.className="especificaciones";
		divEspecificaciones.appendChild(document.createTextNode(partidas[i][4]));
		columna5.appendChild(divEspecificaciones);
		
		//CANTIDAD
		var columna6 = document.createElement("td");
		columna6.setAttribute("align","center");
		columna6.setAttribute("valign","top");
		columna6.setAttribute("width","10%");
		var inputCantidad = document.createElement("INPUT");
		inputCantidad.setAttribute("type","hidden");
		inputCantidad.setAttribute("id","txt_cantidad"+i);
		inputCantidad.setAttribute("name","txt_cantidad"+i);
		inputCantidad.setAttribute("readOnly","true");
		inputCantidad.value=partidas[i][5];
		inputCantidad.size='5';
		inputCantidad.className='normalNegro';
		columna6.appendChild(inputCantidad);
		var divCantidad = document.createElement("div");
		divCantidad.setAttribute("id","divCantidad"+i);
		divCantidad.appendChild(document.createTextNode(partidas[i][5]));
		columna6.appendChild(divCantidad);
		
		//OPCION DE ELIMINAR
		var columna7 = document.createElement("td");
		columna7.setAttribute("align","center");
		columna7.setAttribute("valign","top");
		columna7.setAttribute("width","10%");
		columna7.className = 'link';
		deleteLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		deleteLink.setAttribute("href", "javascript:elimina_pda('"+(i+1)+"')");
		deleteLink.appendChild(linkText);
		columna7.appendChild (deleteLink);
		columna7.appendChild(document.createElement("br"));
		editLink = document.createElement("a");
		linkText = document.createTextNode("Modificar");
		editLink.setAttribute("href", "javascript:modificar('"+(i+1)+"')");
		editLink.appendChild(linkText);
		columna7.appendChild (editLink);

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

function validar_pri(elem){
	for(i=0;i<elem;i++){
		if(document.getElementById('txt_cantidad'+i).value=='' || document.getElementById('txt_prod'+i).value==''){
			alert('Debe especificar la descripci'+oACUTE+'n y la cantidad para cada producto');
		    return false;
		}
	}
}

function enviar(accion) {
	document.getElementById("accion").value = accion;
	if( (document.getElementById("txt_cod_imputa").value=="") && (document.getElementById("txt_cod_accion").value=="") ) {
		alert('Debe seleccionar la categor'+iACUTE+'a program'+aACUTE+'tica');
		return;
	}
	
	if(document.getElementsByName("chk_tp_imputa")[0].checked==true){
		document.getElementById("txt_id_tp_p_ac").value = "1";
	}else{
		document.getElementById("txt_id_tp_p_ac").value = "0";
	}
	
	justificacion = document.getElementById("justificacion").value;
	if(justificacion=="") {
		alert('Debe indicar la justificaci'+oACUTE+'n de la requisici'+oACUTE+'n');
		document.getElementById("justificacion").focus();
		return;
	}

	pcta = trim(document.getElementById("pcta").value);
	
	pctaJustificacion = document.getElementById("pctaJustificacion").value;
	if(pctaJustificacion=="") {
		if(pcta==""){
			alert('Usted ha indicado que para esta requisici'+oACUTE+'n no aplica punto de cuenta. Debe indicar la raz'+oACUTE+'n en el campo justificaci'+oACUTE+'n.');	
		}else{
			alert('Usted ha seleccionado el punto de cuenta '+pcta+' para esta requisici'+oACUTE+'n. Debe indicar la raz'+oACUTE+'n en el campo justificaci'+oACUTE+'n.');
		}
		document.getElementById("pctaJustificacion").focus();
		return;
	}
	
	if(partidas.length==0) {
		alert('Debe asociar al menos una partida al documento');
		return;
	}
	
	if(confirm('Datos introducidos de manera correcta. '+pACUTE+'Desea continuar?.')){
		document.form.submit();
	}
}

function verifica_partida(){
	abrir_ventana('../../includes/arbolCategoria.php?dependencia='+dependencia+'&campo_nom_supe=txt_nombre_imputa&campo_cod_supe=txt_cod_imputa&campo_nombre_accion=txt_nombre_accion&campo_cod_accion=txt_cod_accion&tipo=chk_tp_imputa&formulario=form&tipo_docu=0&centrog=txt_id_p_ac&centroc=txt_id_acesp&opcion=rqui&campo_cod_supe2=txt_cod_imputa2&campo_cod_accion2=txt_cod_accion2');
}

function limpiarItem(){
	document.getElementById("itemCompletar").value="";
	document.getElementById("itemCompletar").focus();
	document.getElementById("cantidad").value="";
	document.getElementById("articuloEspecificaciones").value="";
	document.getElementById("articuloEspecificacionesLen").value="10000";
}

function estaEnItemsTemporales(idItem){
	for(j = 0; j < articulos.length; j++){
		if(idItem==articulos[j]){
			return true;
		}
	}
	return false;
}

function estaEnItems(nombreItem, idPartida){
	for(j = 0; j < nombresItems.length; j++){
		if(idPartida){
			if(trim(nombreItem)==trim(nombresItems[j]) && trim(idPartida)==trim(idsPartidasItems[j])){
				return j;
			}
		}else{
			if(trim(nombreItem)==trim(nombresItems[j])){
				return j;
			}
		}
	}
	return -1;
}

function estaAsociadaAPcta(partida){
	var indicePcta = document.getElementById("pcta").selectedIndex;
	if(indicePcta!=0){
		for(j = 0; j < pctaPartidas[indicePcta-1].length; j++){
			if(trim(partida)==trim(pctaPartidas[indicePcta-1][j])){
				return true;
			}
		}
	}else{
		return true;
	}
	return false;
}

function validarPcta(){
	partidasTemporal = new Array();
	tienePartida = true;
	mensajeTemporal = "";
	indiceNuevoPcta = document.getElementById("pcta").selectedIndex;
	if(partidas.length>0 && indiceNuevoPcta!=0){
		k = 0;
		while(k < partidas.length){
			tienePartida = false;
			j = 0;
			while(j < pctaPartidas[indiceNuevoPcta-1].length){
				if(trim(partidas[k][2])==trim(pctaPartidas[indiceNuevoPcta-1][j])){
					tienePartida = true;
				}
				j++;
			}
			if(tienePartida==false){
				partidasTemporal[partidasTemporal.length]=partidas[k][2];
			}
			k++;
		}
		if(partidasTemporal.length>0){
			j=0;
			while(j<partidasTemporal.length){
				if(mensajeTemporal.indexOf(partidasTemporal[j], 0)==-1){
					mensajeTemporal+=partidasTemporal[j]+", ";
				}
				j++;
			}
			mensajeTemporal = mensajeTemporal.substring(0, mensajeTemporal.length-2);
			alert("Usted ya ha agregado rubros asociados a partidas que no contempla el punto de cuenta "+document.getElementById("pcta").value+". Debe eliminar los rubros asociados a las siguientes partidas: "+mensajeTemporal);
			document.getElementById("pcta").selectedIndex = indicePctaActual;
			return;
		}else{
			indicePctaActual = document.getElementById("pcta").selectedIndex;
		}
	}else{
		indicePctaActual = document.getElementById("pcta").selectedIndex;
	}
	if ( indiceNuevoPcta != 0 ) {
		i = 0;
		if (i < pctaImputaciones[indiceNuevoPcta-1].length) {
			imputacion = pctaImputaciones[indiceNuevoPcta-1][0];
			
			//$pctaImputaciones .= "['".$row["tipo"]."','".$row["id_proyecto_accion"]."','".$row["id_accion_especifica"]."','".$row["nombre_proyecto_accion"]."','".$row["nombre_accion_especifica"]."','".$row["centro_gestor"]."','".$row["centro_costo"]."'],";
			if ( imputacion[0] == '0' ) {
				document.getElementById("tipo_proyecto").checked = false;//tipo proyecto
				document.getElementById("tipo_accion").checked = true;//tipo accion centralizada
			} else if ( imputacion[0] == '1' ) {
				document.getElementById("tipo_proyecto").checked = true;//tipo proyecto
				document.getElementById("tipo_accion").checked = false;//tipo accion centralizada
			}
			
			document.getElementById("txt_cod_imputa").value = imputacion[1];//codigo proyecto/accion centralizada
			document.getElementById("txt_cod_imputa2").value = imputacion[5];//centro gestor			
			document.getElementById("txt_nombre_imputa").value = imputacion[3];//nombre proyecto/accion centralizada
			document.getElementById("txt_cod_accion").value = imputacion[2];//codigo accion especifica
			document.getElementById("txt_cod_accion2").value = imputacion[6];//centro costo
			document.getElementById("txt_nombre_accion").value = imputacion[4];//nombre accion especifica
		}
		document.getElementById("mostrarCategorias").setAttribute("href","#");
	} else {
		document.getElementById("tipo_proyecto").checked = false;//tipo proyecto
		document.getElementById("tipo_accion").checked = false;//tipo accion centralizada
		document.getElementById("txt_cod_imputa").value = "";//codigo proyecto/accion centralizada
		document.getElementById("txt_cod_imputa2").value = "";//centro gestor			
		document.getElementById("txt_nombre_imputa").value = "";//nombre proyecto/accion centralizada
		document.getElementById("txt_cod_accion").value = "";//codigo accion especifica
		document.getElementById("txt_cod_accion2").value = "";//centro costo
		document.getElementById("txt_nombre_accion").value = "";//nombre accion especifica
		document.getElementById("mostrarCategorias").setAttribute("href","javascript:verifica_partida();");
	}
}

function agregarItem(){
	if(trim(document.getElementById("itemCompletar").value)==""){
		alert("Introduzca la partida o una palabra contenida en el nombre del art"+iACUTE+"culo, bien o servicio.");
		document.getElementById("itemCompletar").focus();
	}else{
		if(trim(document.getElementById("cantidad").value)==""){
			alert("Introduzca la cantidad para el art"+iACUTE+"culo, bien o servicio.");
			document.getElementById("cantidad").focus();
		}else{
			if(trim(document.getElementById("articuloEspecificaciones").value)==""){
				alert("Introduzca las especificaciones del art"+iACUTE+"culo, bien o servicio.");
				document.getElementById("articuloEspecificaciones").focus();	
			}else{
				tokens = document.getElementById("itemCompletar").value.split( ":" );
				indiceMarca = document.getElementById("itemCompletar").value.indexOf(":", 0);
				if(indiceMarca>-1){
					nombreAuxiliar = document.getElementById("itemCompletar").value.substring(indiceMarca+1,document.getElementById("itemCompletar").value.length);
					if(nombreAuxiliar.indexOf(":", 0)>-1){
						tokens[1] = nombreAuxiliar;
					}
				}
				if(tokens[0] && tokens[1]){
					idPartida = trim(tokens[0]);
					nombreItem = trim(tokens[1]);
					indiceIdItem = estaEnItems(nombreItem,idPartida);
					if(indiceIdItem>-1){
						var tbody = document.getElementById('item');
						idItem = idsItems[indiceIdItem];
						if(estaAsociadaAPcta(idPartida)==true){
							indiceGeneral = articulos.length;
							nombrePartida = nombresPartidasItems[indiceIdItem];
							cantidad = trim(document.getElementById("cantidad").value);
							especificaciones = trim(document.getElementById("articuloEspecificaciones").value);
							
							var registro = new Array(6);
							registro[0]=idItem;
							registro[1]=nombreItem;
							registro[2]=idPartida;
							registro[3]=nombrePartida;
							registro[4]=especificaciones;
							registro[5]=cantidad;
							
							var fila = document.createElement("tr");
							fila.className='normalNegro';
							
							//CODIGO DEL PRODUCTO
							var columna1 = document.createElement("td");
							columna1.setAttribute("align","center");
							columna1.setAttribute("valign","top");
							columna1.setAttribute("width","10%");
							var inputIdItem = document.createElement("INPUT");
							inputIdItem.setAttribute("type","hidden");
							inputIdItem.setAttribute("name","txt_id_art"+indiceGeneral);
							inputIdItem.setAttribute("readOnly","true");
							inputIdItem.value=registro[0];
							inputIdItem.size='5';
							inputIdItem.className='normalNegro';
							columna1.appendChild(inputIdItem);
							columna1.appendChild(document.createTextNode(registro[0]));
							
							//DENOMINACION DEL PRODUCTO
							var columna2 = document.createElement("td");
							columna2.setAttribute("align","left");
							columna2.setAttribute("valign","top");
							columna2.setAttribute("width","15%");
							var inputNombreItem = document.createElement("INPUT");
							inputNombreItem.setAttribute("type","hidden");
							inputNombreItem.setAttribute("name","txt_nb_art"+indiceGeneral);
							inputNombreItem.setAttribute("readOnly","true");
							inputNombreItem.value=registro[1];
							inputNombreItem.size='25';
							inputNombreItem.className='normalNegro';
							columna2.appendChild(inputNombreItem);
							columna2.appendChild(document.createTextNode(registro[1]));
							
							//CODIGO DE LA PARTIDA
							var columna3 = document.createElement("td");
							columna3.setAttribute("align","center");
							columna3.setAttribute("valign","top");
							columna3.setAttribute("width","10%");
							var inputIdPartida = document.createElement("INPUT");
							inputIdPartida.setAttribute("type","hidden");
							inputIdPartida.setAttribute("readOnly","true");
							inputIdPartida.setAttribute("name","txt_id_pda"+indiceGeneral);
							inputIdPartida.value=registro[2];
							inputIdPartida.size='9';
							inputIdPartida.className='normalNegro';
							columna3.appendChild(inputIdPartida);
							columna3.appendChild(document.createTextNode(registro[2]));
							
							//DENOMINACION DE LA PARTIDA
							var columna4 = document.createElement("td");
							columna4.setAttribute("align","left");
							columna4.setAttribute("valign","top");
							columna4.setAttribute("width","15%");
							var inputNombrePartida = document.createElement("INPUT");
							inputNombrePartida.setAttribute("type","hidden");
							inputNombrePartida.setAttribute("name","txt_nb_pda"+indiceGeneral);
							inputNombrePartida.setAttribute("readOnly","true");
							inputNombrePartida.value=registro[3];
							inputNombrePartida.size='25';
							inputNombrePartida.className='normalNegro';
							columna4.appendChild(inputNombrePartida);
							columna4.appendChild(document.createTextNode(registro[3]));
							
							//DESCRIPCION
							var columna5 = document.createElement("td");
							columna5.setAttribute("align","left");
							columna5.setAttribute("valign","top");
							columna5.setAttribute("width","30%");
							var inputEspecificaciones = document.createElement("INPUT");
							inputEspecificaciones.setAttribute("type","hidden");
							inputEspecificaciones.setAttribute("id","txt_prod"+indiceGeneral);
							inputEspecificaciones.setAttribute("name","txt_prod"+indiceGeneral);
							inputEspecificaciones.setAttribute("readOnly","true");
							inputEspecificaciones.value=registro[4];
							inputEspecificaciones.size='34';
							inputEspecificaciones.className='normalNegro';
							columna5.appendChild(inputEspecificaciones);
							var divEspecificaciones = document.createElement("div");
							divEspecificaciones.setAttribute("id","divEspecificaciones"+indiceGeneral);
							divEspecificaciones.className="especificaciones";
							divEspecificaciones.appendChild(document.createTextNode(registro[4]));
							columna5.appendChild(divEspecificaciones);
							
							//CANTIDAD
							var columna6 = document.createElement("td");
							columna6.setAttribute("align","center");
							columna6.setAttribute("valign","top");
							columna6.setAttribute("width","10%");
							var inputCantidad = document.createElement("INPUT");
							inputCantidad.setAttribute("type","hidden");
							inputCantidad.setAttribute("id","txt_cantidad"+indiceGeneral);
							inputCantidad.setAttribute("name","txt_cantidad"+indiceGeneral);
							inputCantidad.setAttribute("readOnly","true");
							inputCantidad.value=registro[5];
							inputCantidad.size='5';
							inputCantidad.className='normalNegro';
							columna6.appendChild(inputCantidad);
							var divCantidad = document.createElement("div");
							divCantidad.setAttribute("id","divCantidad"+indiceGeneral);
							divCantidad.appendChild(document.createTextNode(registro[5]));
							columna6.appendChild(divCantidad);
							
							//OPCION DE ELIMINAR
							var columna7 = document.createElement("td");
							columna7.setAttribute("align","center");
							columna7.setAttribute("valign","top");
							columna7.setAttribute("width","10%");
							columna7.className = 'link';
							deleteLink = document.createElement("a");
							linkText = document.createTextNode("Eliminar");
							deleteLink.setAttribute("href", "javascript:elimina_pda('"+(indiceGeneral+1)+"')");
							deleteLink.appendChild(linkText);
							columna7.appendChild (deleteLink);
							columna7.appendChild(document.createElement("br"));
							editLink = document.createElement("a");
							linkText = document.createTextNode("Modificar");
							editLink.setAttribute("href", "javascript:modificar('"+(indiceGeneral+1)+"')");
							editLink.appendChild(linkText);
							columna7.appendChild (editLink);

							fila.appendChild(columna1);
							fila.appendChild(columna2);
							fila.appendChild(columna3);
							fila.appendChild(columna4);
							fila.appendChild(columna5);
							fila.appendChild(columna6);
							fila.appendChild(columna7);
							tbody.appendChild(fila); 

							partidas[partidas.length]=registro;
							articulos[articulos.length]=registro[0];

							document.getElementById('hid_largo').value=partidas.length.toString(10);
							limpiarItem();
						}else{
							alert("El rubro que desea agregar est"+aACUTE+" asociado a una partida que no est"+aACUTE+" contemplada en el punto de cuenta "+document.getElementById("pcta").value+".");
						}
					}else{
						alert("La partida o el nombre del rubro indicado no es v"+aACUTE+"lido");
					}
				}else{
					alert("Seleccione un rubro");
				}
			}	
		}
	}
}

function modificar(indice){
	url = 'modificarItem.php?';
	url += 'indice='+indice;
	url += '&id='+partidas[indice-1][0];
	url += '&nombre='+partidas[indice-1][1];
	url += '&partida='+partidas[indice-1][2];
	url += '&denominacion='+partidas[indice-1][3];
	//url += '&especificaciones='+partidas[indice-1][4];
	url += '&cantidad='+partidas[indice-1][5];
	abrir_ventana(url);
}

var cX = 0; var cY = 0; var rX = 0; var rY = 0;
function UpdateCursorPosition(e){ cX = e.pageX; cY = e.pageY;}
function UpdateCursorPositionDocAll(e){ cX = event.clientX; cY = event.clientY;}

if(document.all) { document.onmousemove = UpdateCursorPositionDocAll; }
else { document.onmousemove = UpdateCursorPosition; }

function AssignPosition(d) {
	if(self.pageYOffset) {
		rX = self.pageXOffset;
		rY = self.pageYOffset;
	}else if(document.documentElement && document.documentElement.scrollTop) {
		rX = document.documentElement.scrollLeft;
		rY = document.documentElement.scrollTop;
	}else if(document.body) {
		rX = document.body.scrollLeft;
		rY = document.body.scrollTop;
	}
	if(document.all) {
		cX += rX;
		cY += rY;
	}
	d.style.left = (cX+10) + "px";
	d.style.top = (cY+10) + "px";
}
function HideContent(d) {
	if(d.length < 1) { return; }
	document.getElementById(d).style.display = "none";
	document.getElementById("detalle").innerHTML = "";
}
function ShowContent(d, pcta, contenido) {
	if(d.length < 1) { return; }
	var dd = document.getElementById(d);
	document.getElementById("detalle").innerHTML = "<b>"+pcta+"</b><br/><br/>"+contenido;
	dd.style.display = "block";
	if(myTime) { clearTimeout(myTime); }
}
function ReverseContentDisplay(d) {
	if(d.length < 1) { return; }
	var dd = document.getElementById(d);
	AssignPosition(dd);
	if(dd.style.display == "none") { dd.style.display = "block"; }
	else { dd.style.display = "none"; }
}