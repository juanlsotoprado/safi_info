function limpiarCuenta(){
  document.getElementById("cuenta").value="";
  document.getElementById("cuenta").focus();
}

function estaEnCuentas(cuenta, partida){
 for(j = 0; j < cuentas.length; j++){
  if(cuenta==cuentas[j] && partida==partidas[j]){
	return nombres[j];
  }
 }
  return "";
}

function redondear_dos_decimal(valor) {
 float_redondeado=Math.round(valor * 100) / 100;
 return float_redondeado;
}

// Genera el apendchild
var acciones = new Array()

function add_itine(id,tipo){   

  if(tipo==0){
	if(document.form.txt_cuenta.value==0){
	 alert("Debe seleccionar una cuenta contable");
	 return;
	}
		
  if(document.form.descripcion.value==""){
	alert("Descripci\u00f3n no definida...");
	return;
  }

  if((document.form.txt_debe.value==0) && (document.form.txt_haber.value==0)){
	alert("Debe especificar el monto correspondiente en la columna (debe/haber) que desea afectar");
	document.form.txt_debe.focus();
	return;
  }

  if((document.form.txt_debe.value!=0) && (document.form.txt_haber.value!=0)){
	alert("Las columnas del debe y haber no pueden ser diferentes a cero (0) al mismo tiempo");
	document.form.txt_debe.focus();
	return;
  }
        
  //Verificamos si esta ya registrada
  for(l=0;l<acciones.length;l++)
  {
   if ((acciones[l][5]==document.getElementById('txt_cuenta2').value) && (acciones[l][1]==document.getElementById('txt_cuenta').value))
   {
	alert("Debe seleccionar una cuenta distinta a la(s) seleccionada(s)");
	return;
	}
  }

	var registro = new Array(11);
		
	registro[0] = document.form.txt_renglon.value;
	registro[1] = document.form.txt_cuenta.value;
	registro[2] = document.form.descripcion.value;
	registro[3] = document.form.txt_debe.value;
	registro[4] = document.form.txt_haber.value;
		
	if (document.form.txt_cuenta2.value==""){
	  registro[5] = 0;
	}else{
		  registro[5] = document.form.txt_cuenta2.value;
		 }

	registro[6] = document.getElementById('txt_proyecto2').value;
	registro[7] = document.getElementById('txt_acc_esp2').value;

	if(document.form.chk_tp_imputa[0].checked==true) {
	  registro[8]=1;
	}else{ 
	  registro[8]=0;}		    

	registro[9] =  document.getElementById('txt_proyecto').value;
	registro[10] = document.getElementById('txt_acc_esp').value;

	
	  acciones[acciones.length]=registro;
	}
	
	var tbody = document.getElementById('body');
	var tbody2 = document.getElementById(id);
	for(i=0;i<acciones.length-1;i++){
		tbody2.deleteRow(3);
	}
	
	if(tipo!=0){
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
		editLink.setAttribute("href", "javascript:add_itine('"+id+"','"+(i+1)+"')");
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


function crear_txt_arreglo(elemento,pos){
	elemento='';
	for(i=0;i<acciones.length;i++){
		elemento+=acciones[i][pos];
		if(i!=(acciones.length-1))
			elemento+=",";
		else
			elemento;
	}
	return elemento;
}

function crear_txt_arreglo2(elemento,pos){
	elemento='';
	for(i=0;i<acciones.length;i++){
		elemento+=acciones[i][pos];
		if(i!=(acciones.length-1))
			elemento+="*";
		else
			elemento;
	}
	return elemento;
}
function no_coma(evt){ /*NO ACEPTA LA COMA EN EL TEXT_BOX*/	
	var nav4 = window.Event ? true : false;
	var key = nav4 ? evt.which : evt.keyCode;	
	return (key != 44);
}

function acceptFloat(evt){	
	// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57	
	var nav4 = window.Event ? true : false;
	var key = nav4 ? evt.which : evt.keyCode;	
	return (key <= 13 || (key >= 48 && key <= 57) || key == 46);
}

var listado_comp = new Array();
var digitos=15 //cantidad de digitos buscados
var puntero=0
var buffer=new Array(digitos) 
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



