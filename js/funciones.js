// JavaScript Document
var aACUTE = "\u00e1";//á
var eACUTE = "\u00e9"; //é
var iACUTE = "\u00ed"; //í
var oACUTE = "\u00f3"; //ó
var uACUTE = "\u00fa"; //ú
var AACUTE = "\u00c1"; //Á
var EACUTE = "\u00c9"; //É
var IACUTE = "\u00cd"; //Í
var OACUTE = "\u00d3"; //Ó
var UACUTE = "\u00da"; //Ú
var nTILDE = "\u00f1"; //ñ
var NTILDE = "\u00d1"; //Ñ
var pACUTE = "\u00bf"; //¿
var gACUTE = "\u00a1"; //¡

/****************************************************************************************
*             Funcion que compara dos fecha una fecha de inicio y una fecha final		*
* 				Retorna TRUE si la fecha inicial es menos igual a la final				*
*****************************************************************************************/ 
function comparar_fechas(fecha_inicial,fecha_final) //Formato dd/mm/yyyy
{ 	
	var validar = true;
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
 
	if (anio1 > anio2)
	{
	  validar = false;	  
	} 
	if ((anio1 == anio2) && (mes1>mes2)) 
	{
  	  validar = false;	  
	}
	if ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2))
	{
  	  validar = false;	  
	}
return validar; 
}

/****************************************************************************************
*             Funcion que compara dos fecha una fecha de inicio y una fecha final		*
* 				Elimina los datos del input indicado con el tercer parámetro            *
*****************************************************************************************/
function compararFechasYBorrarById(idFechaInicio, idFechaFin, idFechaActual)
{
	var objFechaInicio = document.getElementById(idFechaInicio);
	var objFechaFin = document.getElementById(idFechaFin);
	var objFechaActual = document.getElementById(idFechaActual);
	
	compararFechasYBorrarByObj(objFechaInicio, objFechaFin, objFechaActual);
}

function compararFechasYBorrarByObj(objFechaInicio, objFechaFin, objFechaActual){
	
	var fecha_inicial = objFechaInicio.value;
	var fecha_final = objFechaFin.value;
	
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
		alert("La fecha inicial no debe ser mayor a la fecha final."); 
		objFechaActual.value = '';
		return;
	}
}


/****************************************************************************************
*       Funcion que retira todos los espacio a la derecha e izquierda de una cadena		*
*																						*
*****************************************************************************************/ 
function trim(cadena)
{
	for(i=0; i<cadena.length; )
	{
		if(cadena.charAt(i)==" ")
			cadena=cadena.substring(i+1, cadena.length);
		else
			break;
	}

	for(i=cadena.length-1; i>=0; i=cadena.length-1)
	{
		if(cadena.charAt(i)==" ")
			cadena=cadena.substring(0,i);
		else
			break;
	}
	
	return cadena;
}


/****************************************************************************************
*       Funcion que limita los cararteres de un campo									*
*																						*
*****************************************************************************************/ 
function LimitText(fieldObj,maxChars)
{
  var result = true;
  if (fieldObj.value.length >= maxChars)
    {
    result = false;
	}
	
	if (result==false)
	{
	  alert("M\u00e1ximo " + maxChars + " Caracteres");
	  fieldObj.value = fieldObj.value.substring(0, maxChars);
	  fieldObj.focus();
	}
}

/****************************************************************************************
*       Funcion que Valida campo numerico												*
*																						*
*****************************************************************************************/ 
//aceptar solo numeros
function acceptNum(evt){	
	// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57	
	var nav4 = window.Event ? true : false;
	
	var key = nav4 ? evt.which : evt.keyCode;	

	return (key <= 13 || (key >= 48 && key <= 57));
}
//fin de aceptar solo numeros
/****************************************************************************************
*       Funcion que Valida campo												*
*																						*
*****************************************************************************************/ 
function validar(objeto,tipo)
{
	if(tipo==0) // Aceptar solo numeros
	{
		//alert(objeto.value);
		var checkOK = "0123456789";
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
			 	var cambio=checkStr.substring(-1,i);
				objeto.value=cambio;
				alert("Debe escribir s\u00f3lo caracteres num\u00E9ricos");
			  	break;
			}
		}
	} 
	if(tipo==1) // Aceptar solo caracteres
	{
		//alert(objeto.value);
		var checkOK = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghijklmnñopqrstuvwxyz. ";
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
			 	var cambio=checkStr.substring(-1,i);
				objeto.value=cambio;
				alert("En el campo s\u00f3lo es permitido escribir letras ");
			  	break;
			}
		}
	} 
	
	if(tipo==2) // Aceptar  numeros, Letras Y GUION (-)
	{
		//alert(objeto.value);
		var checkOK = "0123456789ABCDEFGHIJKLMN�OPQRSTUVWXYZ�����abcdefghijklmn�opqrstuvwxyz�����- ";
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
			 	var cambio=checkStr.substring(-1,i);
				objeto.value=cambio;
				alert("Escriba s\u00f3lo Caracteres validos en el campo");
			  	break;
			}
		}
	} 
	
}
	
/****************************************************************************************
*     Funcion para abrir otra ventana 													*
*																						*
****************************************************************************************/
function abrir_ventana(url,x){
	if (x==null) {
		x=530;
	}
	newwindow=window.open(url,'name','height=470,width='+x+',scrollbars=yes,resizable=yes');
	if (window.focus) {
		newwindow.focus();
	}
	//return newwindow;
}
// fin de abrir ventana
/****************************************************************************************
*     Funcion para colocar formato de moneda  000,000,000.00 													*
*																						*
****************************************************************************************/
function FormatCurrency(objNum)
 {
	  /*if(objNum=='txt_subtotal_bs')
	  {
		//alert(document.getElementById(objNum).value);		
		num = document.getElementById(objNum).value
	  }
	  else*/
	  var num = objNum.value;
	  var ent, dec;
	  if (num != '' && num != objNum.oldvalue)
	  {
		   num = MoneyToNumber(num);
		   if (isNaN(num))
		   {
				if(isNaN(objNum.oldvalue))
				objNum.value="";
				else
				objNum.value = objNum.oldvalue;
				
				if (num==0)
					objNum.value = 0;
		   }
		   else
		   {//alert(num.split('.')[0])
				var ev = (navigator.appName.indexOf('Netscape') != -1)?Event:event;
				if (ev.keyCode == 190 || !isNaN(num.split('.')[1]))
				{//alert(num.split('.')[0])
					 objNum.value = AddCommas(num.split('.')[0])+'.'+num.split('.')[1].substring(0,2);
					 //objNum.value = AddCommas(num.split('.')[0]);
				}
				else
				{//alert(num.split('.')[0])
					 objNum.value = AddCommas(num.split('.')[0]);
				}
				objNum.oldvalue = objNum.value;
		   }
	  }
	return
 }
// fin de FormatCurrency
/****************************************************************************************
*     Funcion para pasar de formato de moneda a numerico 													*
*																						*
****************************************************************************************/
 function MoneyToNumber(num)
 {
	  return (num.replace(/,/g, ''));
	  
 }
// fin de MoneyToNumber 
/****************************************************************************************
*     Funcion para pasar agregar comas al formato moneda 													*
*																						*
****************************************************************************************/
 function AddCommas(num)
 {
	  numArr=new String(num).split('').reverse();
	  for (i=3;i<numArr.length;i+=3)
	  {
		   numArr[i]+=',';
	  }
	  return numArr.reverse().join('');
 }
 // fin de AddCommas
 
 /************************************************************************************/
 function validar_objeto(objeto)
{
	var checkOK = "0123456789-";
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
			var cambio=checkStr.substring(-1,i);
			objeto.value=cambio;
			alert("Estos caracteres no est\u00e1n permitidos");
			break;
		}
	}
}
 /************************************************************************************/
 
 /****************************************************************************************/
 /*                                 Abrir Nueva ventana                                  */
 /****************************************************************************************/
function abrir_nueva_ventana(url,x,nombre)
{
   if (x==null)
   {x=530;}
   nueva=window.open(url,nombre,'height=470,width='+x+',scrollbars=yes,resizable=yes');
  if (window.focus) {nueva.focus();}
}


  /****************************************************************************************/
 /*                                Limita el numero de decimales de un float                                 */
 /****************************************************************************************/

function fix(fixNumber, decimalPlaces)
{
	var div = Math.pow(10,decimalPlaces);
	fixNumber = Math.round(fixNumber * div) / div;
	return fixNumber;
}
 /****************************************************************************************/
 /*********************************************************************************************/
//ELIMINA TODOS TIPO DE ESPACIO
/**********************************************************************************************/
  function lTrim(sStr){
     while (sStr.charAt(0) == " ") 
      sStr = sStr.substr(1, sStr.length - 1);
     return sStr;
    }

    function rTrim(sStr){
     while (sStr.charAt(sStr.length - 1) == " ") 
      sStr = sStr.substr(0, sStr.length - 1);
     return sStr;
    }

    function allTrim(sStr){
     return rTrim(lTrim(sStr));
    } 
/***************************************************************************************************/
//Funcion Formato Numerico
/*****************************************************************************************************/
function number_format (number, decimals, dec_point, thousands_sep)
{
  var exponent = "";
  var numberstr = number.toString ();
  var eindex = numberstr.indexOf ("e");
  if (eindex > -1)
  {
    exponent = numberstr.substring (eindex);
    number = parseFloat (numberstr.substring (0, eindex));
  }
  
  if (decimals != null)
  {
    var temp = Math.pow (10, decimals);
    number = Math.round (number * temp) / temp;
  }
  var sign = number < 0 ? "-" : "";
  var integer = (number > 0 ? 
      Math.floor (number) : Math.abs (Math.ceil (number))).toString ();
  
  var fractional = number.toString ().substring (integer.length + sign.length);
  dec_point = dec_point != null ? dec_point : ".";
  fractional = decimals != null && decimals > 0 || fractional.length > 1 ? 
               (dec_point + fractional.substring (1)) : "";
  if (decimals != null && decimals > 0)
  {
    for (i = fractional.length - 1, z = decimals; i < z; ++i)
      fractional += "0";
  }
  
  thousands_sep = (thousands_sep != dec_point || fractional.length == 0) ? 
                  thousands_sep : null;
  if (thousands_sep != null && thousands_sep != "")
  {
	for (i = integer.length - 3; i > 0; i -= 3)
      integer = integer.substring (0 , i) + thousands_sep + integer.substring (i);
  }
  
  return sign + integer + fractional + exponent;
}


//Para verificar que se haya firmado
function confirmar_firma(firmaTextField)
{
	//var firmaTextField = document.getElementById('firma');
	/*if (firmaTextField.value == "") {
		alert ("Debe Firmar primero antes de Aprobar");		
		return 0;		
	}*/
	return 1;
}

function strStartsWith(str, prefix) {
    return str.indexOf(prefix) === 0;
}

function strEndsWith(str, suffix) {
    return str.match(suffix+"$")==suffix;
}

function textCounter(field, countfield, maxlimit) {
	if (field.value.length > maxlimit) field.value = field.value.substring(0, maxlimit);
	else document.getElementById(countfield).value = maxlimit - field.value.length;
}

function verificarMenorQue(field, maximoValor, msg, multipliedByField) {
	if(!msg || msg==""){
		msg = "El valor del campo cantidad a comprar no puede ser superior a la cantidad solicitada en la requisici"+oACUTE+"n para ese art"+iACUTE+"culo";
	}
	valor = Number(field.value);
	if(multipliedByField && multipliedByField!=""){
		multipliedByFieldValue = document.getElementById(multipliedByField).value;
		valor *= Number(multipliedByFieldValue);
	}
	if(valor > maximoValor){
		alert(msg);
		while (valor > maximoValor){
			field.value = field.value.substring(0, field.value.length-1);
			valor = Number(field.value);
			if(multipliedByField && multipliedByField!=""){
				multipliedByFieldValue = document.getElementById(multipliedByField).value;
				valor *= Number(multipliedByFieldValue);
			}
		}
	}
}

function validarTexto(campoForm){
	var enter = "\n";
	var caracteres = " abcdefghijklmnopqrstuvwxyz"+aACUTE+eACUTE+iACUTE+oACUTE+uACUTE+nTILDE+"ABCDEFGHIJKLMNOPQRSTUVWXYZ"+AACUTE+EACUTE+IACUTE+OACUTE+UACUTE+NTILDE+"0123456789<>|@;:,.º/!¡$%&()=+-*%¿?_" + String.fromCharCode(13) + enter;
	var ubicacion = '';

	if (typeof campoForm == 'string'){
		campo = campoForm;
		for (var i=0; i < campo.length; i++) {
			ubicacion = campo.substring(i, i + 1);
			if (caracteres.indexOf(ubicacion) == -1) {
		    	campoForm = campoForm.replace(ubicacion, "");
			}
		}
		return campoForm;
	}else{
		campo = campoForm.value;
		for (var i=0; i < campo.length; i++) {
			ubicacion = campo.substring(i, i + 1);
			if (caracteres.indexOf(ubicacion) == -1) {
		    	campoForm.value = campoForm.value.replace(ubicacion, "");
			}
		}
	}
}

function validarNumero(){
	var campoForm = arguments[0]; 
	var campo = campoForm.value;
	//campo = campoForm.value;
	var permitirCeroInicial = arguments[1] || false;
	var ubicacion = '';
	var caracteres = "0123456789";
	for (var i=0; i < campo.length; i++) {
		ubicacion = campo.charAt(i);
		if (caracteres.indexOf(ubicacion) == -1) {
	    	campoForm.value = campoForm.value.replace(ubicacion, "");
		}
	}
	while((permitirCeroInicial == false && campoForm.value.indexOf("0")==0) || 
			(permitirCeroInicial == true && campoForm.value.indexOf("0")==0 && campoForm.value.length > 1)){
		if(permitirCeroInicial){
			if(campoForm.value.length > 1){	
				campoForm.value = campoForm.value.substring(1, campoForm.value.length);
			}
		} else {
			campoForm.value = campoForm.value.substring(1, campoForm.value.length);
		}
	}
}

function validarDecimal(campoForm){
	campo = campoForm.value;
	var ubicacion = '';
	var caracteres = "0123456789.";
	for (var i=0; i < campo.length; i++) {
		ubicacion = campo.substring(i, i + 1);
		if (caracteres.indexOf(ubicacion) == -1) {
	    	campoForm.value = campoForm.value.replace(ubicacion, "");
		}
	}
	while(campoForm.value.indexOf("0")==0 && campoForm.value.length>1 && campoForm.value.charAt(1)!='.'){
		campoForm.value = campoForm.value.substring(1, campoForm.value.length);
	}
	if(campoForm.value.indexOf(".")!=-1){
		primerPunto = campoForm.value.indexOf(".");
		primerPunto++;
		cadena = campoForm.value.substring(0,primerPunto);
		while(primerPunto<campoForm.value.length){
			if(campoForm.value.charAt(primerPunto)!='.'){
				cadena += campoForm.value.charAt(primerPunto);
			}
			primerPunto++;
		}
		campoForm.value = cadena;
	}
}

function validarInteger(campoForm){
	validarNumero(campoForm);
	if(Number(campoForm.value)>2147483647){
		alert("El valor del campo cantidad no puede ser superior a 2147483647");
		campoForm.value = campoForm.value.substring(0, campoForm.value.length-1);
	}
}

function validarCodigo(campoForm){
	campo = campoForm.value;
	var ubicacion = '';
	var caracteres = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789- ";
	for (var i=0; i < campo.length; i++) {
		ubicacion = campo.substring(i, i + 1);
		if (caracteres.indexOf(ubicacion) == -1) {
	    	campoForm.value = campoForm.value.replace(ubicacion, "");
		}
	}
}

function  trimDato(dato){

	dato2 = trim(dato);


	if(dato2 != ''){
		return "1";
           
    }else{
    	return false;
    	
        }
	
    }

function validarcaracterespecial(campo) {

	var price = campo;
	
	var intRegex = /^[^'"@?¿·ª~!\¿?=)]*$/;

	campo2 = trimDato(campo);
	 
    if ((price.match(intRegex)) && (campo2 != false)) {
       
    	return 1;
    	
    } else {
    	

    	return false;
    } 
    
  
}

function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}

/***********************************************************************************
 *  Selecciona un rango de texto dentro de un input (text area, input --text--)    *
 ***********************************************************************************/
function setSelectionRange(input, selectionStart, selectionEnd)
{
	if (input.setSelectionRange) {
		input.focus();
		input.setSelectionRange(selectionStart, selectionEnd);
	}
	else if (input.createTextRange) {
		var range = input.createTextRange();
		range.collapse(true);
		range.moveEnd('character', selectionEnd);
		range.moveStart('character', selectionStart);
		range.select();
	}
}

/***********************************************************************************
 *  Posiciona el cursor dentro de un input o text area                             *
 ***********************************************************************************/
function setCursorPosition(input, pos)
{
	setSelectionRange(input, pos, pos);
}

/*****************************************************************************************
 *  Controla el foco inicial (load de la página web) del input del código del documento  *
 *****************************************************************************************/
function establecerFocoInicialCodigoDocumento(idInput)
{
	var objInput = $('#'+idInput);
	var idLength = objInput.val().length;
	
	if(idLength < 5){
		setSelectionRange(objInput[0], 0, idLength);
	}
	else if(idLength == 5){
		setCursorPosition(objInput[0], 5);
	}
	else if(idLength > 5){
		setSelectionRange(objInput[0], 5, idLength);
	}
	
	objInput.focus();
}

function autoCompleteResponsables(params)
{
	
	
	var params = arguments[0] || null;
	var objInputResponsable = params != null ? (params.objInputResponsable || null) : null;
	var numItems = params != null ? (params.numItems || null) : null;
	var selectFunction = params != null ? (params.selectFunction || null) : null;
	
	if(objInputResponsable == null || selectFunction == null) return;
	
	objInputResponsable.autocomplete({
		source: function(request, response){
			$.ajax({
				url: SAFI_URL_ACCIONES_PATH + "/empleado.php",
				dataType: "json",
				data: {
					accion: "Search",
					tipoRespuesta: 'json',
					key: request.term,
					numItems: numItems
				},
				success: function(json){
					var index = 0;
					var items = new Array();

					$.each(json.listaempleado, function(cedula, objEmpleado){
						var value = objEmpleado.cedula + ' : ' + objEmpleado.nombres.toUpperCase() + ' ' + objEmpleado.apellidos.toUpperCase();
						items[index++] = {
								id: cedula,
								label: value,
								value: value,
								nombres: objEmpleado.nombres.toUpperCase(),
								apellidos: objEmpleado.apellidos.toUpperCase()
						};
					});
					response(items);
				}
			});
		},
		minLength: 1,
		select: selectFunction
	});
}


/*****************************************************************************************
 *  cambia el color cuando el tr es tocado  *
 *****************************************************************************************/

function Registroclikeado(obj){
	
	
	
	 var valor = $(obj).attr('style');
	 
	 if(valor == false || valor == null){
		 
		 $(obj).find("a").each(function (index){

	           $(this).animate({
	        	 color: 'white',
	        	 cursor: 'move'        
	  	            },1);

		 });

		 $(obj).animate({
   		 color: 'white'
            
        }, 50 );
   	 
		 $(obj).animate({
		 color: 'white',
        backgroundColor: "#516B55"  
    },200);

	 

	 }else{
		 
   	 
		 $(obj).attr("style","");

		 $(obj).find("a").each(function (index){

	           $(this).attr("style","");

		 });	

   

   }
}


/*****************************************************************************************************
* Funciones de la clase VistaFechas (vistas/classses/fechas.php)                                     *
******************************************************************************************************/

function setDateToday(objInputFechaInicio, objInputFechaFin, strFormato)
{
	var fechaHoy = $.datepicker.formatDate(strFormato, new Date());

	objInputFechaInicio.val(fechaHoy);
	objInputFechaFin.val(fechaHoy);
}

function setDateYestarday(objInputFechaInicio, objInputFechaFin)
{
	var date = new Date();
	date.setTime(date.getTime() - 86400000);
	var fechaAyer = $.datepicker.formatDate('dd/mm/yy', date);

	objInputFechaInicio.val(fechaAyer);
	objInputFechaFin.val(fechaAyer);
}

function setDateWeek(objInputFechaInicio, objInputFechaFin)
{
	var dateHoy = new Date();
	var dateLunes = new Date();
	dateLunes.setTime(dateHoy.getTime() - (86400000 * (dateHoy.getDay() == 0 ? 6 : dateHoy.getDay() -1)));

	objInputFechaInicio.val($.datepicker.formatDate('dd/mm/yy', dateLunes));
	objInputFechaFin.val($.datepicker.formatDate('dd/mm/yy', new Date()));
}

function setDateLastWeek(objInputFechaInicio, objInputFechaFin)
{
	var dateHoy = new Date();
	var dateLunesPasado = new Date();
	var dateDomingoPasado = new Date();
	
	dateLunesPasado.setTime(dateHoy.getTime() - (86400000 * ((dateHoy.getDay() == 0 ? 6 : dateHoy.getDay() -1) + 7)));
	dateDomingoPasado.setTime(dateHoy.getTime() - (86400000 * ((dateHoy.getDay() == 0 ? 6 : dateHoy.getDay() -1) + 1)));

	objInputFechaInicio.val($.datepicker.formatDate('dd/mm/yy', dateLunesPasado));
	objInputFechaFin.val($.datepicker.formatDate('dd/mm/yy', dateDomingoPasado));
}

function setDateMonth(objInputFechaInicio, objInputFechaFin)
{
	var dateHoy = new Date();
	var dateInicioMes = new Date();

	dateInicioMes.setDate(1);

	objInputFechaInicio.val($.datepicker.formatDate('dd/mm/yy', dateInicioMes));
	objInputFechaFin.val($.datepicker.formatDate('dd/mm/yy', dateHoy));
}

function setDateLastMonth(objInputFechaInicio, objInputFechaFin)
{
	var dateHoy = new Date();
	var dateInicioMesPasado = new Date(dateHoy.getFullYear(), dateHoy.getMonth() - 1, 1);
	var dateFinMesPasado = new Date(dateHoy.getFullYear(), dateHoy.getMonth(), 1);

	dateFinMesPasado.setTime(dateFinMesPasado.getTime() - 86400000);

	objInputFechaInicio.val($.datepicker.formatDate('dd/mm/yy', dateInicioMesPasado));
	objInputFechaFin.val($.datepicker.formatDate('dd/mm/yy', dateFinMesPasado));
}

function setDateYear(objInputFechaInicio, objInputFechaFin)
{
	var dateHoy = new Date();
	var dateInicioA_o = new Date(dateHoy.getFullYear(), 0, 1);

	objInputFechaInicio.val($.datepicker.formatDate('dd/mm/yy', dateInicioA_o));
	objInputFechaFin.val($.datepicker.formatDate('dd/mm/yy', dateHoy));
}

function setDateLastYear(objInputFechaInicio, objInputFechaFin)
{
	var dateHoy = new Date();
	var dateInicioA_oPasado = new Date(dateHoy.getFullYear() - 1, 0, 1);
	var dateFinA_oPasado = new Date(dateHoy.getFullYear(), 0, 1);

	dateFinA_oPasado.setTime(dateFinA_oPasado.getTime() - 86400000);

	objInputFechaInicio.val($.datepicker.formatDate('dd/mm/yy', dateInicioA_oPasado));
	objInputFechaFin.val($.datepicker.formatDate('dd/mm/yy', dateFinA_oPasado));
}

/*****************************************************************************************************
* Fin de funciones de la clase VistaFechas (vistas/classses/fechas.php)                              *
******************************************************************************************************/