var partidas = new Array();
var articulos= new Array();
var dependencia = "";
var tipoReqActual = "";
var myTime;

function actualizarLocalidadDeInfocentro(){
	infocentro = document.getElementById('infocentro');
	document.getElementById('estadoInfocentro').value = arregloIdEstados[infocentro.selectedIndex];
	document.getElementById('estadoInfocentroNombre').value = arregloEstados[infocentro.selectedIndex];
}

function cambiarDestino(){
	var tipoDestino = document.getElementById('tipoDestino');
	var divOficinaPrincipal = document.getElementById('divOficinaPrincipal');
	var divInfocentro = document.getElementById('divInfocentro');
	var divInfomovil = document.getElementById('divInfomovil');
	var divOtro = document.getElementById('divOtro');
	if(tipoDestino.value == "1"){
		divOficinaPrincipal.style.display='block';
		divInfocentro.style.display='none';
		divInfomovil.style.display='none';
		divOtro.style.display='none';
	}else if(tipoDestino.value == "2"){
		divOficinaPrincipal.style.display='none';
		divInfocentro.style.display='block';
		divInfomovil.style.display='none';
		divOtro.style.display='none';
	}else if(tipoDestino.value == "3"){
		divOficinaPrincipal.style.display='none';
		divInfocentro.style.display='none';
		divInfomovil.style.display='block';
		divOtro.style.display='none';
	}else if(tipoDestino.value == "4"){
		divOficinaPrincipal.style.display='none';
		divInfocentro.style.display='none';
		divInfomovil.style.display='none';
		divOtro.style.display='block';
	}
}

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
		articulos[i-1]=partidas[i][3];
	}
	
	partidas.pop();
	articulos.pop();
	
	document.getElementById('hid_largo').value=partidas.length;
	
	for(i=0;i<partidas.length;i++){
		var fila = document.createElement("tr");
		
		//CODIGO DEL PRODUCTO
		var columna4 = document.createElement("td");
		columna4.setAttribute("align","center");
		columna4.className = 'titularMedio';
		var txt_id_art = document.createElement("INPUT");
		txt_id_art.setAttribute("type","text");
		txt_id_art.setAttribute("readonly","true");
		txt_id_art.setAttribute("name","txt_id_art"+i); 
		txt_id_art.value=partidas[i][4];	 
		txt_id_art.size='8'; 
		txt_id_art.className='ptotal';
		columna4.appendChild(txt_id_art);
	
		//DENOMINACION DEL PRODUCTO
		var columna5 = document.createElement("td");
		columna5.setAttribute("align","center");
		columna5.className = 'titularMedio';
		var txt_nb_art = document.createElement("INPUT");
		txt_nb_art.setAttribute("type","text");
		txt_nb_art.setAttribute("readonly","true");
		txt_nb_art.setAttribute("name","txt_nb_art"+i); 
		txt_nb_art.value=partidas[i][5];	 
		txt_nb_art.size='25'; 
		txt_nb_art.className='ptotal';
		columna5.appendChild(txt_nb_art);
		
		//CODIGO DE LA PARTIDA
		var columna6 = document.createElement("td");
		columna6.setAttribute("align","center");
		columna6.className = 'titularMedio';
		var txt_id_pda = document.createElement("INPUT");
		txt_id_pda.setAttribute("type","text");
		txt_id_pda.setAttribute("readonly","true");
		txt_id_pda.setAttribute("name","txt_id_pda"+i);
		txt_id_pda.value=partidas[i][6];	 
		txt_id_pda.size='15'; 
		txt_id_pda.className='ptotal';
		columna6.appendChild(txt_id_pda);
		
		//DENOMINACION DE LA PARTIDA
		var columna7 = document.createElement("td");
		columna7.setAttribute("align","center");
		columna7.className = 'titularMedio';
		var txt_den_pda = document.createElement("INPUT");
		txt_den_pda.setAttribute("type","text");
		txt_den_pda.setAttribute("readonly","true");
		txt_den_pda.setAttribute("name","txt_den_pda"+i);
		txt_den_pda.value=partidas[i][7];	 
		txt_den_pda.size='25'; 
		txt_den_pda.className='ptotal';
		columna7.appendChild(txt_den_pda);
		  
		//DESCRIPCION
		var columna8 = document.createElement("td");
		columna8.setAttribute("align","center");
		columna8.className = 'titularMedio';
		var txt_prod = document.createElement("INPUT");
		txt_prod.setAttribute("type","text");
		txt_prod.setAttribute("readonly","true");
		txt_prod.setAttribute("name","txt_prod"+i);
		txt_prod.value=partidas[i][9];	 
		txt_prod.size='20'; 
		txt_prod.className='ptotal';
		columna8.appendChild(txt_prod);
	
		//CANTIDAD
		var columna9 = document.createElement("td");
		columna9.setAttribute("align","center");
		columna9.className = 'titularMedio';
		var txt_cantidad = document.createElement("INPUT");
		txt_cantidad.setAttribute("type","text");
		txt_cantidad.setAttribute("name","txt_cantidad"+i);
		txt_cantidad.setAttribute("readonly","true");
		var mon=MoneyToNumber(partidas[i][8]);
		txt_cantidad.value=mon;	 
		txt_cantidad.size='8'; 
		txt_cantidad.className='ptotal';
		columna9.appendChild(txt_cantidad);	
		  
		//OPCION DE ELIMINAR
		var columna10 = document.createElement("td");				
		columna10.setAttribute("align","center");
		columna10.className = 'link';
		editLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		editLink.setAttribute("href", "javascript:elimina_pda('"+(i+1)+"')");
		editLink.appendChild(linkText);
		columna10.appendChild (editLink);

		fila.appendChild(columna4);
		fila.appendChild(columna5);
		fila.appendChild(columna6);
		fila.appendChild(columna7);
		fila.appendChild(columna8);
		fila.appendChild(columna9);
	    fila.appendChild(columna10);
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

function add_opciones(){
	var mensaje = "";
		
	nuevosElementos = document.getElementById('tbl_part').getElementsByTagName('tr').length-1;
	todosElementos = nuevosElementos+partidas.length;
	
	var tbody2 = document.getElementById('tbl_part');
	var tbody = document.getElementById('item');
	var valido=validar_pri(nuevosElementos);
	if(valido==false){return;}
	
	if(nuevosElementos<1){
		alert("Este documento no posee partidas asociadas");
		return;
	}
	
	j=0;
	for(var i=partidas.length;i<todosElementos;i++){
		if(!estaEnArticulos(document.getElementById("txt_codigo"+j).value)){			
			var registro = new Array(11);
			registro[4]=document.getElementById("txt_codigo"+j).value;
			registro[5]=document.getElementById('txt_den'+j).value;
			registro[6]=document.getElementById('txt_codigoa'+j).value;
			registro[7]=document.getElementById('txt_dena'+j).value;
			registro[8]=document.getElementById('txt_cantidad'+j).value;
			registro[9]=document.getElementById('txt_prod'+j).value;
			registro[10]=document.getElementById('txt_unidad'+j).value;
			
			var fila = document.createElement("tr");
			
			//CODIGO DEL PRODUCTO
			var columna4 = document.createElement("td");
			columna4.setAttribute("align","center");
			columna4.className = 'titularMedio';
			var txt_id_art = document.createElement("INPUT");
			txt_id_art.setAttribute("type","text");
			txt_id_art.setAttribute("name","txt_id_art"+i);
			txt_id_art.setAttribute("readOnly","true");
			txt_id_art.value=registro[4];	 
			txt_id_art.size='8'; 
			txt_id_art.className='ptotal';
			columna4.appendChild(txt_id_art);
		
			//DENOMINACION DEL PRODUCTO
			var columna5 = document.createElement("td");
			columna5.setAttribute("align","center");
			columna5.className = 'titularMedio';
			var txt_nb_art = document.createElement("INPUT");
			txt_nb_art.setAttribute("type","text");
			txt_nb_art.setAttribute("name","txt_nb_art"+i);
			txt_nb_art.setAttribute("readOnly","true");
			txt_nb_art.value=registro[5];	 
			txt_nb_art.size='25'; 
			txt_nb_art.className='ptotal';
			columna5.appendChild(txt_nb_art);
			  
			//CODIGO DE LA PARTIDA
			var columna6 = document.createElement("td");
			columna6.setAttribute("align","center");
			columna6.className = 'titularMedio';
			var txt_id_pda = document.createElement("INPUT");
			txt_id_pda.setAttribute("type","text");
			txt_id_pda.setAttribute("readOnly","true");
			txt_id_pda.setAttribute("name","txt_id_pda"+i);
			txt_id_pda.value=registro[6];	 
			txt_id_pda.size='15'; 
			txt_id_pda.className='ptotal';
			columna6.appendChild(txt_id_pda);
			
			//DENOMINACION DE LA PARTIDA
			var columna7 = document.createElement("td");
			columna7.setAttribute("align","center");
			columna7.className = 'titularMedio';
			var txt_nb_pda = document.createElement("INPUT");
			txt_nb_pda.setAttribute("type","text");
			txt_nb_pda.setAttribute("name","txt_nb_pda"+i);
			txt_nb_pda.setAttribute("readOnly","true");
			txt_nb_pda.value=registro[7];	 
			txt_nb_pda.size='25'; 
			txt_nb_pda.className='ptotal';
			columna7.appendChild(txt_nb_pda);
			  
			//DESCRIPCION
			var columna8 = document.createElement("td");
			columna8.setAttribute("align","center");
			columna8.className = 'titularMedio';
			var txt_prod = document.createElement("INPUT");
			txt_prod.setAttribute("type","text");
			txt_prod.setAttribute("name","txt_prod"+i);
			txt_prod.setAttribute("readOnly","true");
			txt_prod.value=registro[9];	 
			txt_prod.size='20'; 
			txt_prod.className='ptotal';
			columna8.appendChild(txt_prod);
		
			//CANTIDAD
			var columna9 = document.createElement("td");
			columna9.setAttribute("align","center");
			columna9.className = 'titularMedio';
			var txt_cantidad = document.createElement("INPUT");
			txt_cantidad.setAttribute("type","text"); 
			txt_cantidad.setAttribute("name","txt_cantidad"+i);
			txt_cantidad.setAttribute("readOnly","true");
			txt_cantidad.value=registro[8];	 
			txt_cantidad.size='8'; 
			txt_cantidad.className='ptotal';
			columna9.appendChild(txt_cantidad);
			
			//CANTIDAD
			var columna10 = document.createElement("td");
			columna10.setAttribute("align","center");
			columna10.className = 'titularMedio';
			var txt_unidad = document.createElement("INPUT");
			txt_unidad.setAttribute("type","text"); 
			txt_unidad.setAttribute("name","txt_unidad"+i);
			txt_unidad.setAttribute("readOnly","true");
			txt_unidad.value=registro[10];	 
			txt_unidad.size='8'; 
			txt_unidad.className='ptotal';
			columna10.appendChild(txt_unidad);
			
			//OPCION DE ELIMINAR
			var columna11 = document.createElement("td");				
			columna11.setAttribute("align","center");
			columna11.className = 'link';
			editLink = document.createElement("a");
			linkText = document.createTextNode("Eliminar");
			editLink.setAttribute("href", "javascript:elimina_pda('"+(i+1)+"')");
			editLink.appendChild(linkText);
			columna11.appendChild (editLink);

			fila.appendChild(columna4);
			fila.appendChild(columna5);
			fila.appendChild(columna6);
			fila.appendChild(columna7);
			fila.appendChild(columna8);
			fila.appendChild(columna9);
			fila.appendChild(columna10);
			fila.appendChild(columna11);
			tbody.appendChild(fila); 	
	
			partidas[partidas.length]=registro;
			articulos[articulos.length]=registro[4];
		}else{
			mensaje += document.getElementById('txt_den'+j).value+"\n";
		}
		
		j++;
	}
	
	document.getElementById('hid_largo').value=partidas.length.toString(10);
	for(i=0;i<nuevosElementos;i++){	 
		tbody2.deleteRow(1);
	}
	
	if(mensaje!=""){
		alert("Los siguientes art"+iACUTE+"culos han sido agregados previamente:\n\n"+mensaje+"\nSi desea cambiar su descripci"+oACUTE+"n o cantidad elim"+iACUTE+"nelos y vuelvalos a agregar.");
	}
}

function enviar() {
	justificacion = document.getElementById("justificacion").value;
	if(justificacion=="") {
		alert('Debe indicar la Justificaci'+oACUTE+'n de la Requisici'+oACUTE+'n');
		return;
	}

	tipo = document.getElementsByName("typo");
		
	if(partidas.length==0) {
		alert('Debe asociar al menos una partida al documento');
		return;
	}
	
	if(confirm('Datos introducidos de manera correcta. '+pACUTE+'Desea Continuar?.')){
		document.form.submit();
	}
}

function verArticulos(){
	abrir_ventana('arbol_partidas.php?tipo='+((document.getElementById("typoMaterial").checked==true)?document.getElementById("typoMaterial").value:((document.getElementById("typoBien").checked==true)?document.getElementById("typoBien").value:"")),750);
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
	//AssignPosition(dd);
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