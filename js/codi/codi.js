var tipo = 0;
var resultado = 0;
var codis=new Array();
var codisIndice = -1;
var dependencia = null;
var tipoproacc = null;
var i = 1;
var r = 1;

$().ready(function() {
	$("#cuerpoCategoriaCuenta").hide();
	$("#categoriaCuentaContable").hide();
	$("#tablaCategoriaCuenta").hide(); //tbody?
	$("#cuerpoRegistro").hide(); //tbody?
	$("#tablaRegistro").hide();	
	$("#divCuentaMonto").hide();
	$("#accionEspecifica").hide();	
	
	if ($('#comp_id').val()!='' && $('#comp_id').val()!= null) {
		$('#categoriaCuentaContable').show('fade',300);
		$("#cuerpoRegistro").show('fade',300);
		$("#divCuentaMonto").show('fade',300);
		$("#tablaRegistro").show('fade',300);
		
		agregarCategoriaCuentaMod();		
	}
	
	$('#comp_id').autocomplete({
		source: function(request, response){
			$.ajax({
				url: "../../acciones/comp/comp.php",
				dataType: "json",
				data: {
					accion: "SearchCompromiso",
					key: request.term,
					yearPresupuestario: yearPresupuestario,
					tipoRespuesta: 'json'
				},
				success: function(json){
					//alert(JSON.stringify(json));
					var index = 0;
					var items = new Array();

					

                   /* var val = $(json).length ;
                    if(val < 1){
                    	$('#comp_id').val('');
                     }*/

					$.each(json, function(idComp, objCuenta){

						var label = idComp;
						items[index++] = {
								id: idComp,
								value: idComp
						};
					});
					response(items);
				}
			});
		},
		minLength: 1,
		select: function(event, ui)
		{
			//alert($('#comp_id').val().length);
			//if ($('#comp_id').val().length<2) 
			$('#comp_id').val(ui.item.id);
			
			$("#cuerpoCategoriaCuenta").hide();
			$("#categoriaCuentaContable").hide();
			$("#tablaCategoriaCuenta").hide();
			SearchCompImputa($("#comp_id").val(),$("#acc_esp").val());
			
			return true;
		}
 });	
	
	
	
	$("#debe_cuenta").keyup(function(event)
	{
		formato_num($(this));
	});	
	
	$("#haber_cuenta").keyup(function()
	{
		formato_num($(this));
	});
	
/*Autocomplete Categoria*/	
	$('#categoria').autocomplete({
		autoFocus: false,
		delay: 100,
		 source: function(request, response){
				$.ajax({
					url: "../../acciones/general.php",
					dataType: "json",
					data: {
						accion: "SearchCategoria",
						tipoRespuesta: 'json',
						key: request.term,
						dependencia: '',
						idproyAcc: $('#selectProyAcc').val(),
						tipoproacc :  tipoproacc,
						yearPresupuestario: yearPresupuestario
					},
					success: function(json){
						var index = 0;
						var items = new Array();

						if(json == ""){
							
	                    	if($('#c1').html() != ''){

	                    		$('#c1').html('');
								$('#c2').html('');
								$('#c3').html('');
								$('#c4').html('');
								$('#tablaCategoria').hide(200);
	                        }
	                    	
	                     }

						$.each(json,function(id,params){

							
							
	                        if(params.tipo == 1){
	                              
	                             var tipo = "Proyecto";
	                              }else{

	                            	  var tipo = "Ac. Centralizada";
	                                  }
							
							var value = params.centro+ ' '+params.nombre;

	                        categoriaTipo = params.tipo;
	                        id_especifica = params.id_especifica;
							id_proy_accion = params.id_proy_accion;
							
							 items[index++] = {
									 
									value: value,
									tipo: tipo,
									id_especifica: params.id_especifica,
									id_proy_accion: params.id_proy_accion,
									centro: params.centro,
									nombre: params.nombre,
									proy_titulo: params.proy_titulo
									

							}; 

						});
						response(items);
					}
				});
			},
			minLength: 1,
			select: function(event, ui)
			{ 
				$('#tablaCategoria').css('margin-top','10px');
				$('#c1').html(ui.item.tipo);
				$('#c2').html(ui.item.centro);
				$('#c3').html(ui.item.proy_titulo);
				$('#c4').html(ui.item.nombre+"<input type=hidden id=cod_proy value='"+ui.item.id_proy_accion+"'/><input type=hidden id=cod_aesp value='"+ui.item.id_especifica+"'/><input type=hidden id=tipo_proy value='"+ui.item.tipo+"'/>");
				$('#tablaCategoria').show('fade',300);
				$('#divCuentaMonto').show('fade',300);
				
				$('#tablaRegistro tr td.proy_acc').each(function()
				{
					$(this).find('span.nombreProyAcc').empty().append(document.createTextNode(ui.item.centro));
					$(this).find('input[name=\'proy_acc\[\]\']').val(ui.item.id_proy_accion);
					$(this).find('input[name=\'proy_aesp\[\]\']').val(ui.item.id_especifica);
					$(this).find('input[name=\'proy_tipo\[\]\']').val(ui.item.tipo=='Proyecto' ? '1' : '0');
				});
				
			}
				//return true;
		}); 

/*On Change Proy/Acc*/
	$('#selectProyAcc').change(function(){
	   tipoproacc = $('#selectProyAcc').val();
	   $('#categoria').val('');
	   $('#tablaCategoria').hide();
	   if(isNaN(tipoproacc)){
		 tipoproacc = 0; 
		}
	   else{
		   tipoproacc = 1;
	 }
	   $('#accionEspecifica').show('fade',300);
	});
	
	$('#cuentaContable').autocomplete({
		source: function(request, response){
			$.ajax({
				url: "../../acciones/convertidor.php",
				dataType: "json",
				data: {
					accion: "BuscarAsociacionConvertidorCodi",
					key: request.term,
					tipoRespuesta: 'json'
				},
				success: function(json){
					var index = 0;
					var items = new Array();

                    var val = $(json['listaConvertidor']).length ;
                    
                   // alert(JSON.stringify(json));
                   // console.info(json);
					$.each(json.listaConvertidor, function(idCuenta, objCuenta){
						
						var label = objCuenta.cuentaContable.id + ": " + objCuenta.partida.id + ": " + objCuenta.cuentaContable.nombre;
						items[index++] = {
								id: objCuenta.cuentaContable.id,
								label: label,
								value: label,
								nombreCuenta: objCuenta.cuentaContable.nombre,
								idPartida: objCuenta.partida.id
						};
					});
					response(items);
				}
			});
		},
		minLength: 1,
		select: function(event, ui)
		{
			var valido = true;
			
			$('#_spanCuentaContable').empty();
			$('#_spanPartida').empty();
			$('#_spanDenominacion').empty();
			
			$('#_inputCuentaContable').val('');
			$('#_inputPartida').val('');
			$('#_inputDenominacion').val('');
			
			if ($('input[name=\'cuentas_\[\]\'][value="' + (ui.item.id) + '"]').length > 0) {
				 if ($('input[name=\'partidas_\[\]\'][value="' + (ui.item.idPartida) +'"]').length > 0) {
					 valido = false;
					 alert("Esta cuenta contable asociada a esa partida presupuestaria ya fue seleccionada ");
					 
					 valido =false;
				}
			}
			
			if(valido) {
				$('#_spanCuentaContable').append(document.createTextNode(ui.item.id));
				$('#_spanPartida').append(document.createTextNode(ui.item.idPartida));
				$('#_spanDenominacion').append(document.createTextNode(ui.item.nombreCuenta));
				
				$('#_inputCuentaContable').val(ui.item.id);
				$('#_inputPartida').val(ui.item.idPartida);
				$('#_inputDenominacion').val(ui.item.nombreCuenta);
				
				$('#debe_cuenta').focus();
			}
			
			$('#cuentaContable').val('');
			
			return false;
		}
	});
	
/*Agregar cuenta contable*/	
	$('#confirmarCuenta').click(function()
	{
		confirmarCuenta();
	});
	
});

function confirmarCuenta()
{
	 valido = true;
	 
	 if($("#categoria").val().length < 4){
    	 valido = false;
    	 alert("Debe seleccionar una acci\u00f3n espec\u00edfica");
    	 $('#categoria').focus();
    	 return;
     }
	 
	 if ($('#_inputCuentaContable').val().length < 17) {
		 valido = false;
         alert("Debe seleccionar una cuenta contable");
         $('#cuentaContable').focus();
         return;
	 }
	 
	 if ($('input[name=\'cuentas_\[\]\'][value="' + ($('#_inputCuentaContable').val()) + '"]').length > 0) {
		 if ($('input[name=\'partidas_\[\]\'][value="' + ($('#_inputPartida').val()) +'"]').length > 0) {
			 valido = false;
			 alert("Esta cuenta contable asociada a esa partida presupuestaria ya fue seleccionada ");
			 $('#cuentaContable').focus();
			 return;
		}
	 }

      if($("#debe_cuenta").val().length <1){
    	  $("#debe_cuenta").val('0');
      }
      if($("#haber_cuenta").val().length <1){
    	  $("#haber_cuenta").val('0');
      }
      
      if(($("#debe_cuenta").val()!=0 && $("#haber_cuenta").val()!=0) || ($("#debe_cuenta").val()==0 && $("#haber_cuenta").val()==0)){
	         valido = false;
	         $("#debe_cuenta").val('');
	         $("#haber_cuenta").val('');
	         
	         var algo = alert("Un s\u00f3lo campo (debe/haber) debe estar en cero.");
	         
	         $("#debe_cuenta").focus();
	         
	         return;
      }

    if(valido){
    	agregarCategoriaCuenta(); //Agregar cuenta contable
    	
    	$('#_spanCuentaContable').empty();
		$('#_spanPartida').empty();
		$('#_spanDenominacion').empty();
		
		$('#_inputCuentaContable').val('');
		$('#_inputPartida').val('');
		$('#_inputDenominacion').val('');
		$("#debe_cuenta").val('');
        $("#haber_cuenta").val('');
    	
    	$('#cuentaContable').focus();
    }
}

function limpiarContadorRegistro () {
	var g = 0;
	$("#tablaRegistro > tr").each(
               function(index) {
                       $(this).children("td.numero").html(g);
                       g++;
               });
}

function limpiarContadorCategoria () {
	var g = 0;
	$("#tablaCategoriaCuenta > tr").each(
               function(index) {
                       $(this).children("td.numero").html(g);
                       g++;
               });
}

function buscarAccion() {
	$("#pagina").val(1);
	$("#formCodiFiltro").attr('action','codi.php?accion=BuscarAccion');
	$("#formCodiFiltro").submit();	
}

function limpiarFem() {
	$("#fecha_inicio").val('');
	$("#fecha_fin").val('');	
}

function limpiarFel() {
	$("#fechae_inicio").val('');
	$("#fechae_fin").val('');	
}

	
function buscar(pagina) {
	$("#pagina").val(pagina);
	$("#formCodiFiltro").attr('action','codi.php?accion=BuscarAccion');
	$("#formCodiFiltro").submit();	
}	

function verificarCheckboxControl(){
	inputs = document.getElementsByTagName("input");
	todosMarcados = true;
	totalCheckboxs = 0;
	for(var i = 0; i < inputs.length; i++) {
		if(inputs[i].getAttribute("type")=="checkbox"){
			totalCheckboxs++;
			if(	strStartsWith(inputs[i].getAttribute("name"),"codis")==true
				&& inputs[i].checked == false){
				todosMarcados = false;
			}
		}
	}
	if(totalCheckboxs>1){
		checkboxControl = document.getElementById("controlCodis");
		checkboxControl.checked = todosMarcados;
	}
}

function marcarTodosNinguno(){
	checkbox = document.getElementById("controlCodis");
	inputs = document.getElementsByTagName("input");
	for(var i = 0; i < inputs.length; i++) {
		if(inputs[i].getAttribute("type")=="checkbox"
			&& strStartsWith(inputs[i].getAttribute("name"),"codis")==true){
			inputs[i].checked = checkbox.checked;
			agregarQuitarCodi(inputs[i]);
		}
	}
	verificarCheckboxControl();
}


function agregarQuitarCodi(elemento, manual){
	if(elemento.checked==true){
		if(existeCodi(elemento.value+"")==-1){
			codisIndice++;
			codis[codisIndice] = new String(elemento.value+'');
		}
	}else{
		codis[existeCodi(elemento.value+"")] = null;
		codisIndice--;
	}
	if(manual && manual==true){
		verificarCheckboxControl();
	}
}

function existeCodi(codi){
	i = 0;
	while(i<codis.length){
		if(codis[i]==codi){
			return i;	
		}
		i++;
	}
	return -1;
}

/*function imprimir(){
	cadenaCodis = "";
	for(i=0; i<codis.length; i++){
		if(codis[i]!=null){
			cadenaCodis += "'"+codis[i]+"',";
		}
	}
	if(cadenaCodis!=""){
		cadenaCodis = cadenaCodis.substring(0,cadenaCodis.length - 1);
		//location.href="../../documentos/codi/codiMultiplePDF.php?codis="+cadenaCodis;
		location.href="codi.php?accion=GenerarPDF&codis="+cadenaCodis;
		return;
	}else{
		alert("Debe seleccionar al menos un comprobante diario para generar documento PDF.");
		return;
	}
}*/

/*function anular(codigo) {
    document.location.href = "../../documentos/codi/codi_anular.php?codigo="+codigo;
}*/

function ingresar() {
	var ruta;
	ruta = "codi.php?accion=IngresarAccion&fechaEfectiva="+$("#fecha").val()+"&comp_id="+$("#comp_id").val()+"&docAsociado="+$("#docAsociado").val()+"&refBancaria="+$("#refBancaria").val()+"&justificacion="+$("#justificacion").val();
	
	$("#formCodi").attr('action',ruta);
	$("#formCodi").submit();	

}


function anular(codigo) {
	/*  contenido=prompt("Indique el motivo de la anulaci\u00F3n para el "+codigo,"");
	  if ((contenido!=null) && (contenido!='')){
	   	if (confirm("Est\u00e1 seguro que desea ANULAR el codi "+codigo)) {
	   		$("#formCodiFiltro").attr('action','codi.php?accion=Anular&idCodi='+codigo+"&justificacion="+contenido);
			$("#formCodiFiltro").submit();		   		
		}
	   	else {
	   		alert("Debe Indicar el motivo de la anulaci\u00F3n");
	   		return;
		}
	}*/
	
	$("#formCodiFiltro").attr('action','codi.php?accion=Anular&codis='+codigo);
	$("#formCodiFiltro").submit();	

}

function anularAccion(codi,motivo) {
	  if ((motivo!=null) && (motivo!='')){
	   	if (confirm("Est\u00e1 seguro que desea ANULAR el "+codi)) {
	   		$("#formCodiFiltro").attr('action','codi.php?accion=AnularAccion&idCodi='+codi+"&justificacion="+motivo);
			$("#formCodiFiltro").submit();		   		
		}
	  } 	
	  else {
		  alert("Debe indicar el motivo de la anulaci\u00F3n");
		  return;
	  }
}	  

function GenerarSiguienteCadena(valor){

    if(valor){	
        
 		idCadenaSiguiente = valor;
 		var sumaDebe = 0;
 		var sumaHaber = 0; 		
 		/*Revisión de cuadre*/
 		
	      $('input[name=\'debe_[]\']').each(function (index) {
	    	  sumaDebe = (parseFloat(sumaDebe) + parseFloat((this.value.replace(/\./g,"")).replace(",","."))); 
	 	 }); 		
	      $('input[name=\'haber_[]\']').each(function (index) {
	    	  sumaHaber = (parseFloat(sumaHaber) + parseFloat((this.value.replace(/\./g,"")).replace(",","."))); 
	 	 });
	      sumaDebe = Number((sumaDebe).toFixed(2));
	      sumaHaber = Number((sumaHaber).toFixed(2));
 		
	      if (sumaDebe != sumaHaber) 
	    	  alert("Asiento no cuadrado. Sumatoria de montos por el Debe: "+sumaDebe+" vs. Sumatoria de montos por el haber:"+sumaHaber);
 		/*Fin de revisión cuadre*/
	      else
 		
 		Revisar();  
    }
}

function Revisar() {
	
    if($('#fecha').val() == "") {
    	alert("Debe seleccionar la fecha efectiva del comprobante diario");
    	$('#fecha').focus();
    	return;
    }

    if($('#comp_id').val() == "") {
    	alert("Debe especificar el compromiso");
    	$('#comp_id').focus();
		return;
	}

    if( (!validarcaracterespecial($("#docAsociado").val()))) {
    	alert("Debe indicar el documento asociado sin caracteres especiales");
    	$('#docAsociado').focus();
    	return;
	}
    /*if( (!validarcaracterespecial($("#refBancaria").val())))
	{
		alert("Debe indicar la referencia bancaria");
		 $('#refBancaria').focus();
		return;
	}*/
	 
    if( (!validarcaracterespecial($("#justificacion").val()))) {
			alert("Debe indicar la justificaci\u00F3n del comprobante manual  y \u00e9sta s\u00f3lo puede tener caracteres alfanum\u00E9ricos");
			 $('#justificacion').focus();
			return;
	}
		
    if(confirm("¿Est\u00E1 seguro que desea generar este comprobante manual?")){
			  	Enviar();
	}
}           
            

function Enviar() {
	var cuenta ='';
    $('input[name=\'cuentas_[]\']').each(function (index) {
    	cuenta = cuenta + "'"+this.value +"',";
	 });	
	
    var monto_debe = '';
    $('input[name=\'debe_[]\']').each(function (index) {
    	monto_debe = monto_debe + "'"+this.value +"',";
	 });	    

    var monto_haber = '';
    $('input[name=\'haber_[]\']').each(function (index) {
    	monto_haber = monto_haber + "'"+this.value +"',";
	 });	    

    var partida = '';
    $('input[name=\'partidas[]\']').each(function (index) {
    	partida = partida + "'"+this.value +"',";
	 });	    
    
	var ruta;
	$('#accion').val('IngresarAccion');
	
	//ruta = "codi.php?accion=IngresarAccion&fechaEfectiva="+$("#fecha").val()+"&comp_id="+$("#comp_id").val()+"&docAsociado="+$("#docAsociado").val()+"&refBancaria="+$("#refBancaria").val()+"&justificacion="+$("#justificacion").val()+"cuenta="+cuenta+"monto_debe"+;
	//$("#formCodi").attr('action',ruta);	
	
	LlenarCadenaSiguiente();
	$('#formCodi')[0].submit();
}

function Modificar()  {
		var sumaDebe = 0;
 		var sumaHaber = 0; 		
 		/*Revisión de cuadre*/
 		
	      $('input[name=\'debe_[]\']').each(function (index) {
	    	  sumaDebe = (parseFloat(sumaDebe) + parseFloat((this.value.replace(/\./g,"")).replace(",","."))); 
	 	 }); 		
	      $('input[name=\'haber_[]\']').each(function (index) {
	    	  sumaHaber = (parseFloat(sumaHaber) + parseFloat((this.value.replace(/\./g,"")).replace(",","."))); 
	 	 });
	      sumaDebe = Number((sumaDebe).toFixed(2));
	      sumaHaber = Number((sumaHaber).toFixed(2));
 		
	      if (sumaDebe != sumaHaber) { 
	    	  alert("Asiento no cuadrado. Sumatoria de montos por el Debe: "+sumaDebe+" vs. Sumatoria de montos por el haber:"+sumaHaber);
	      }
	      else {
	    		$('#accion').val('ModificarAccion');
	    		$('#formCodi')[0].submit();
	      }
}

function CurrencyFormatted(amount)
{
    var i = parseFloat(amount);
    if(isNaN(i)) { i = 0.00; }
    var minus = '';
    if(i < 0) { minus = '-'; }
    i = Math.abs(i);
    i = parseInt((i + .005) * 100);
    i = i / 100;
    s = new String(i);
    if(s.indexOf('.') < 0) { s += '.00'; }
    if(s.indexOf('.') == (s.length - 2)) { s += '0'; }
    s = minus + s;
    return s;
}

/*function formatNumber3(input, 50, 2, 1) {
	  var num = input.value.replace(/\./g, ',');
	  var numparts = num.split(',');
	  numparts[0] = numparts[0].replace(/\D/g, '');
	  if (numparts[0].length > numberOfDigitsBeforeComma)
	   numparts[0] = numparts[0].substring(0,numberOfDigitsBeforeComma);
	  if (numparts.length > 1) {
	   numparts[1] = numparts[1].replace(/\D/g, '');
	   if (numparts[1].length > numberOfDigitsAfterComma)
	    numparts[1] = numparts[1].substring(0,numberOfDigitsAfterComma);
	   if (numparts[1].length == 0 && deleteCommaIfNotDecimals == true) {
	    num = numparts[0];
	   }
	   else {
	    num = numparts[0] + ',' + numparts[1];
	   }
	  }
	  else {
	   num = numparts[0];
	  }
	  input.value = num;
	 }*/



function LlenarCadenaSiguiente(){
	$("#idCadenaSiguiente").remove();
	var	div = $("#accionesEjecutar")[0];
	var input1 = document.createElement("input");
	input1.setAttribute("type","hidden");
	input1.setAttribute("id","idCadenaSiguiente");
	input1.setAttribute("name","idCadenaSiguiente");
	input1.value= idCadenaSiguiente;
	div.appendChild(input1);
}

function SearchCompImputa(id, _accEsp){
	i = 0;
	$.ajax({
		url: "../../acciones/codi/codi.php",
		dataType: "json",
		data: {
			accion: "SearchImputasComp",
			tipoRespuesta: 'json',
			key: id,
			accEsp: _accEsp
		},
			success: function(json){
				//alert(JSON.stringify(json));
				$('#tablaCategoriaCuenta').children("tr.trCaso").remove();
				
				var tbody = $('#tablaCategoriaCuenta')[0];

				$('#categoriaCuentaContable').show('fade',300);	
				$('#selectProyAcc').val('');
		 		$('#selectProyAcc').prop( "disabled", false );
		 		$('#categoria').val('');
		 		$('#categoria').prop( "disabled", false );
		 		$('#tablaCategoria').hide();
		 		$("#accionEspecifica").show('fade',300);
		 		$('#divCuentaMonto').show('fade',300);
		 		
		 		i=1;
				if (json != null) {
					$.each(json,function(id,val){
				 		var fila = document.createElement("tr");
				 		fila.className='normalNegro trCaso';
				 		
				 		if(i==1) {
					 		$('#selectProyAcc').val(val.id_proy);
					 		$('#selectProyAcc').attr('disabled','disabled');
					 		$('#categoria').val(val.proy_acc);
					 		$('#categoria').attr('disabled','disabled');
				 		}

				 		var columna1 = document.createElement("td");
				 		columna1.setAttribute("class","numero");
				 		columna1.appendChild(document.createTextNode(i));
				 		
				 		var columna2 = document.createElement("td");
				 		columna2.appendChild(document.createTextNode(val.cpat_id));
				 		columna2.className='cuenta';			 		
				 		
				 		var columna3 = document.createElement("td");
				 		//columna3.appendChild(document.createTextNode(val.proy_acc));
				 		columna3.className='proy_acc';
				 		
				 		var span = document.createElement("span");
						span.appendChild(document.createTextNode(val.proy_acc));
						span.className='nombreProyAcc';
						columna3.appendChild(span);

				 		var columna4 = document.createElement("td");
				 		columna4.appendChild(document.createTextNode(id));
				 		columna4.className='partida';

				 		var columna5 = document.createElement("td");
				 		columna5.appendChild(document.createTextNode(val.nombre_partida));
				 		columna5.className='denominacion';

				 		var columna6 = document.createElement("td");
				 		if (val.monto<1 && val.monto>0 || val.monto>-1 && val.monto<0)
				 			monto = 0,00;
				 		else
				 		 monto =  number_format(val.monto,2,',','.');
				 		columna6.appendChild(document.createTextNode(monto));
				 		columna6.className='montoCompromiso';
				 		
				 		var columna7 = document.createElement("td");
				 		var input1 = document.createElement("input");
				 		input1.setAttribute("type","text");
				 		input1.setAttribute("size","15");
				 		input1.setAttribute("name","debe[]");
				 		input1.setAttribute("value","");
				 		columna7.appendChild(input1);				 		
				 		
				 		var columna8 = document.createElement("td");
				 		var input2 = document.createElement("input");
				 		input2.setAttribute("type","text");
				 		input2.setAttribute("size","15");				 		
				 		input2.setAttribute("name","haber[]");
				 		input2.setAttribute("value","");
				 		
				 		var input3 = document.createElement("input");
				 		input3.setAttribute("type","hidden");
				 		
				 		input3.setAttribute("name","proy[]");
				 		input3.setAttribute("value",val.id_proy);

				 		var input4 = document.createElement("input");
				 		input4.setAttribute("type","hidden");
				 		
				 		input4.setAttribute("name","aesp[]");
				 		input4.setAttribute("value",val.id_aesp);
				 		
				 		var input5 = document.createElement("input");
				 		input5.setAttribute("type","hidden");
				 		
				 		input5.setAttribute("name","tipo[]");
				 		input5.setAttribute("value",val.proy_tipo);
				 		
				 		columna8.appendChild(input2);		
				 		columna8.appendChild(input3);
				 		columna8.appendChild(input4);
				 		columna8.appendChild(input5);
				 		
				 		//OPCION DE AGREGAR
				 		var columna9 = document.createElement("td");
				 		columna9.setAttribute("valign","top");
				 		columna9.className = 'link';
				 		deleteLink = document.createElement("a");
				 		deleteLink.setAttribute("href","javascript:void(0);");
				 		linkText = document.createTextNode("Agregar");
				 		deleteLink.appendChild(linkText);
				 		columna9.appendChild(deleteLink);
				 		
				 		$(deleteLink).bind('click', function(){
				 			agregarRegistro($(this));
				 			/*$("#tablaCategoriaCuenta > tr").each(
		                            function(index) {
		                                    $(this).children("td.numero").html(i);
		                                    i++;
		                            });	*/					 			
				 							 			
				 		});

				 		$(input1).keyup(function(){
				 			formato_num($(this));
				 		});
				 		$(input1).focusout(function(){
				 			formato_num($(this));
				 		});
				 		$(input2).keyup(function(){
				 			formato_num($(this));
				 		});
				 		$(input2).focusout(function(){
				 			formato_num($(this));
				 		});

				 		fila.appendChild(columna1);				
				 		fila.appendChild(columna2);
				 		fila.appendChild(columna3);				
				 		fila.appendChild(columna4);
				 		fila.appendChild(columna5);				
				 		fila.appendChild(columna6);
				 		fila.appendChild(columna7);
				 		fila.appendChild(columna8);	
				 		fila.appendChild(columna9);
				 		
				 		tbody.appendChild(fila);
				 		limpiarContadorCategoria();
				 
				 		if($("#tablaCategoriaCuenta > tr").length < 3){
				 			$('#tablaCategoriaCuenta').show('fade',300);
				 			$("#cuerpoCategoriaCuenta").show('fade',300);
				 		}
					i++;	
					});}
			}

	  });
}

function formato_num(obj){
		
	   monto = obj.val();
	   
	   if (monto.length < 1) {
		   obj.val('');
	   }
	   
	   
	   
	   else {
		   
		   array = monto.toString().split('').reverse();
			
			 if (array[0] == '.'){
				 
			 array[0] = ',';
				 
			 }

			 monto = array.reverse().join('');
	   
		   if(parseInt(monto.split(",").length -1) > 1 || monto == ''){
			alert("El separador de decimal debe ser ','");
			
			obj.val('');
			
		      var montoTotal1 = 0;
		         var dato1 = 0 ;
		         
		         $('input[name=\'debe[]\']').each(function (index) {
		        	 
		            if((this.value != '')){
		         	  
		            	 dato1  = QuitarCaracter(this.value,".");
		               
		            }
		 	 		 
		         });
		
		   } 
		   else{

				array =  monto.split(",");
				
				if(array[1] != undefined){
					
				   num = format(array[0])+","+formatcoma(array[1]);
					
				}else{
					
				num = format(array[0]);
					
				}

				obj.val(num);
		   } 
	   }
}

function QuitarCaracter(params,caracter)
{

var num = parseInt(params.split(caracter).length -1);
	 var i=0;
	 
	   monto = params;
	 
	 while(i<= num){
		monto = monto.replace('.','');
	    i++;
   }
	 
	monto = monto.replace(',','.');
	monto = parseFloat(monto);
	
	return monto;
	
	
}
function setCharAt(str,index,chr) {
    if(index > str.length-1) return str;
    return str.substr(0,index) + chr + str.substr(index+1);
}

function format(input)
{
	var num = input.replace(/\./g,'');
	
	if(!isNaN(num))
	{

		num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
	
		num = num.split('').reverse().join('').replace(/^[\.]/,'');
		
		return num;

	}

	
}

function formatcoma(input)
{
	 
	 var num = input;

	
	if(!isNaN(num))
	{

		num = num.toString().split('').reverse().join('').replace(/(?=\d*\?)(\d{3})/g,'$1.');
	
		num = num.split('').reverse().join('').slice(0,2) ;
		

		if(num == undefined){
			
			 num = '';	
			
		}
		
		return num;

	}

	num = '';	
	return num;
	
	
}



/*Agregar cuenta contable*/
function agregarCategoriaCuenta()
{	
	$('#cuerpoRegistro').show('fade',300);	
	$("#tablaRegistro").show('fade',300);
				
	//Tabla registro definitivo cuentas codi
	var tbody = $('#tablaRegistro')[0];
				
	var fila = document.createElement("tr");
	fila.className='normalNegrita trCaso2';

	//Número de registro
	var columna1 = document.createElement("td");
	columna1.setAttribute("class","numero");
	columna1.appendChild(document.createTextNode(i));

	//Código cuenta contable
	var columna2 = document.createElement("td");
	columna2.appendChild(document.createTextNode($('#_inputCuentaContable').val()));
	/*Arreglo cuenta contable*/
	var input2 = document.createElement("input");
	input2.setAttribute("type","hidden");
	input2.setAttribute("name","cuentas_[]");
	input2.value = $('#_inputCuentaContable').val();
	columna2.appendChild(input2);				 		
	columna2.className='cuenta';

	//Categoría programática
	var columna3 = document.createElement("td");
	//columna3.appendChild(document.createTextNode(($('#categoria').val()).split(' ')[0]));
	columna3.className='proy_acc';
	
	var span = document.createElement("span");
	span.appendChild(document.createTextNode(($('#categoria').val()).split(' ')[0]));
	span.className='nombreProyAcc';
	columna3.appendChild(span);
	
	//Arreglo Proyecto/Acc
	var input3_1 = document.createElement("input");
	input3_1.setAttribute("type","hidden");
	input3_1.setAttribute("name","proy_acc[]");
	input3_1.value= $('#cod_proy').val();
	//input3_1.value= null;
	//Arreglo Acción específica
	var input3_2 = document.createElement("input");
	input3_2.setAttribute("type","hidden");
	input3_2.setAttribute("name","proy_aesp[]");
	input3_2.value= $('#cod_aesp').val();
	//input3_2.value= null;
	//Arreglo tipo imputación
	var input3_3 = document.createElement("input");						
	input3_3.setAttribute("type","hidden");
	input3_3.setAttribute("name","proy_tipo[]");
	if ($('#tipo_proy').val()=='Proyecto')
		input3_3.value= 1;
	else
		input3_3.value= 0;		
	//input3_3.value = '0';	
	columna3.appendChild(input3_1);
	columna3.appendChild(input3_2);
	columna3.appendChild(input3_3);						
	
	//Código partida presupuestaria
	var columna4 = document.createElement("td");
	columna4.appendChild(document.createTextNode($('#_inputPartida').val()));
	//Arreglo partidas presupuestarias
	var input4 = document.createElement("input");
	input4.setAttribute("type","hidden");
	input4.setAttribute("name","partidas_[]");
	input4.value = $('#_inputPartida').val();
	columna4.appendChild(input4);
	columna4.className='partida';

	//Denominación de la cuenta contable
	var columna5 = document.createElement("td");
	columna5.appendChild(document.createTextNode($('#_inputDenominacion').val()));
	columna5.className='denominacion';

	//Monto debe
	var columna6 = document.createElement("td");
	columna6.appendChild(document.createTextNode($('#debe_cuenta').val()));
	//Arreglo monto debe
	var input6 = document.createElement("input");
	input6.setAttribute("type","hidden");
	input6.setAttribute("name","debe_[]");
	input6.value= $('#debe_cuenta').val();
	columna6.appendChild(input6);
	columna6.className='monto_debe';				 		
	
	//Monto Haber
	var columna7 = document.createElement("td");
	columna7.appendChild(document.createTextNode($('#haber_cuenta').val()));
	//Arreglo monto haber
	var input7 = document.createElement("input");
	input7.setAttribute("type","hidden");
	input7.setAttribute("name","haber_[]");
	input7.value= $('#haber_cuenta').val();
	columna7.appendChild(input7);				 		
	columna7.className='monto_haber';				 		

	//Opción eliminar
	var columna8 = document.createElement("td");
	columna8.setAttribute("valign","top");
	columna8.className = 'link';
	deleteLink = document.createElement("a");
	deleteLink.setAttribute("href","javascript:void(0);");
	linkText = document.createTextNode("Eliminar");
	deleteLink.appendChild(linkText);
	columna8.appendChild(deleteLink);

					 		

	$(deleteLink).bind('click', function(){
		eliminarRegistro($(this));
		i = 0;
        $("#tablaCategoriaCuenta > tr").each(
                        function(index) {
                                $(this).children("td.numero").html(i);
                                i++;
                        });				 			
	});
	
	fila.appendChild(columna1);				
	fila.appendChild(columna2);
	fila.appendChild(columna3);				
	fila.appendChild(columna4);
	fila.appendChild(columna5);				
	fila.appendChild(columna6);
	fila.appendChild(columna7);
	fila.appendChild(columna8);	
	
	tbody.appendChild(fila);
	
	limpiarContadorRegistro();				 		
	 $("#debe_cuenta").val('');
	 $("#haber_cuenta").val('');
	 $("#cuentaContable").val('');		 	   

 
 		if($("#tablaCategoriaCuenta > tr").length > 2){
		$('#tablaCategoriaCuenta').show('fade',300);
		$("#cuerpoCategoriaCuenta").show('fade',300);
		
	}
i++;	
	//limpiarContador(); -- No funciona la llamada en este punto
	//return true;
}

/*Modificación*/
/*Agregar cuenta contable*/
function agregarCategoriaCuentaMod(){
	$.each(codiDetalleContable,function(i,element) {
		//alert(element.cpat_id);
		var idCuenta = element.cpat_id;
		var nombreCuenta = element.cpat_nombre;
		var idPartida = element.part_id;
		var proyTipo = element.pr_ac_tipo;
		var proyAcc = element.pr_ac;
		var aEsp = element.a_esp;
		var nombreAesp = element.a_esp_nombre;
		var nombreAproy = element.p_acc_nombre;
		var montoDebe = element.rcomp_debe;
		var montoHaber = element.rcomp_haber;
		var centroGestor = element.centros;
		var nombreTipoProyecto=null;
		
		if (proyTipo==1) nombreTipoProyecto="Proyecto";
		else nombreTipoProyecto="Acción Centralizada";
/*Setear valor de categoria programatica*/
		if (i=1)  {
			
			$('#tablaCategoria').css('margin-top','10px');
			$('#c1').html(nombreTipoProyecto);
			$('#c2').html(centroGestor);
			$('#c3').html(nombreAproy);
			$('#c4').html(nombreAesp+"<input type=hidden id=cod_proy value='"+proyAcc+"'/><input type=hidden id=cod_aesp value='"+aEsp+"'/><input type=hidden id=tipo_proy value='"+nombreTipoProyecto+"'/>");
		//	$('#tablaCategoria').show('fade',300);
		//	$('#divCuentaMonto').show('fade',300);
			$('#accionEspecifica').show('fade',300);			
			$("#categoria").val(centroGestor+" "+nombreAesp);
			//alert($("#selectProyAcc").find('option[value='+proyAcc+']').html());
			$("#selectProyAcc").find('option[value='+proyAcc+']').attr('selected','selected');
			tipoproacc = proyTipo;
			//alert($("#selectProyAcc").html());
		}
		
		$('#cuerpoRegistro').show('fade',300);	
		$("#tablaRegistro").show('fade',300);
					
		//Tabla registro definitivo cuentas codi
		var tbody = $('#tablaRegistro')[0];
					
		var fila = document.createElement("tr");
		fila.className='normalNegrita trCaso2';

		//Número de registro
		var columna1 = document.createElement("td");
		columna1.setAttribute("class","numero");
		columna1.appendChild(document.createTextNode(i));

		//Código cuenta contable
		var columna2 = document.createElement("td");
		columna2.appendChild(document.createTextNode(idCuenta));
		/*Arreglo cuenta contable*/
		var input2 = document.createElement("input");
		input2.setAttribute("type","hidden");
		input2.setAttribute("name","cuentas_[]");
		input2.value= idCuenta;
		columna2.appendChild(input2);				 		
		columna2.className='cuenta';

		//Categoría programática
		var columna3 = document.createElement("td");
		//columna3.appendChild(document.createTextNode(centroGestor));
		columna3.className='proy_acc';
		
		var span = document.createElement("span");
		span.appendChild(document.createTextNode((centroGestor)));
		span.className='nombreProyAcc';
		columna3.appendChild(span);
		
		//Arreglo Proyecto/Acc
		var input3_1 = document.createElement("input");
		input3_1.setAttribute("type","hidden");
		input3_1.setAttribute("name","proy_acc[]");
		input3_1.value= proyAcc;
		//input3_1.value= null;
		//Arreglo Acción específica
		var input3_2 = document.createElement("input");
		input3_2.setAttribute("type","hidden");
		input3_2.setAttribute("name","proy_aesp[]");
		input3_2.value= aEsp;
		//input3_2.value= null;
		//Arreglo tipo imputación
		var input3_3 = document.createElement("input");						
		input3_3.setAttribute("type","hidden");
		input3_3.setAttribute("name","proy_tipo[]");
		input3_3.value= proyTipo;
		columna3.appendChild(input3_1);
		columna3.appendChild(input3_2);
		columna3.appendChild(input3_3);						
		
		//Código partida presupuestaria
		var columna4 = document.createElement("td");
		columna4.appendChild(document.createTextNode(idPartida));
		//Arreglo partidas presupuestarias
		var input4 = document.createElement("input");
		input4.setAttribute("type","hidden");
		input4.setAttribute("name","partidas_[]");
		input4.value= idPartida;
		columna4.appendChild(input4);				 		
		columna4.className='partida';

		//Denominación de la cuenta contable
		var columna5 = document.createElement("td");
		columna5.appendChild(document.createTextNode(nombreCuenta));
		columna5.className='denominacion';

		//Monto debe
		var columna6 = document.createElement("td");
		columna6.appendChild(document.createTextNode(montoDebe.replace(".",",")));
		//Arreglo monto debe
		var input6 = document.createElement("input");
		input6.setAttribute("type","hidden");
		input6.setAttribute("name","debe_[]");
		input6.value= montoDebe.replace(".",",");
		columna6.appendChild(input6);
		columna6.className='monto_debe';				 		
		
		//Monto Haber
		var columna7 = document.createElement("td");
		columna7.appendChild(document.createTextNode(montoHaber.replace(".",",")));
		//Arreglo monto haber
		var input7 = document.createElement("input");
		input7.setAttribute("type","hidden");
		input7.setAttribute("name","haber_[]");
		input7.value= montoHaber.replace(".",",");
		columna7.appendChild(input7);				 		
		columna7.className='monto_haber';				 		

		//Opción eliminar
		var columna8 = document.createElement("td");
		columna8.setAttribute("valign","top");
		columna8.className = 'link';
		deleteLink = document.createElement("a");
		deleteLink.setAttribute("href","javascript:void(0);");
		linkText = document.createTextNode("Eliminar");
		deleteLink.appendChild(linkText);
		columna8.appendChild(deleteLink);

						 		

		$(deleteLink).bind('click', function(){
			eliminarRegistro($(this));
			i = 0;
	        $("#tablaCategoriaCuenta > tr").each(
	                        function(index) {
	                                $(this).children("td.numero").html(i);
	                                i++;
	                        });				 			
		});
		
		fila.appendChild(columna1);				
		fila.appendChild(columna2);
		fila.appendChild(columna3);				
		fila.appendChild(columna4);
		fila.appendChild(columna5);				
		fila.appendChild(columna6);
		fila.appendChild(columna7);
		fila.appendChild(columna8);	
		
		tbody.appendChild(fila);
		
		limpiarContadorRegistro();				 		
		 $("#debe_cuenta").val('');
		 $("#haber_cuenta").val('');
		 $("#cuentaContable").val('');		 	   

	 
	 		if($("#tablaCategoriaCuenta > tr").length > 2){
			$('#tablaCategoriaCuenta').show('fade',300);
			$("#cuerpoCategoriaCuenta").show('fade',300);
			
		}
	i++;	
		//limpiarContador(); -- No funciona la llamada en este punto
		//return true;
		
		
	});
	

	//alert(JSON.stringify(codiDetalleContable));
}

function agregarRegistro(obj){
	var objTr = obj.parent("td").parent("tr");
	var valido = true;
	var validoPartida = true;

	$('input[name=\'partidas_[]\']').each(function (index, value) {
		/*Validación no sea seleccionada la misma cuenta cuentable asociada a la misma partida presupuestaria*/
		
        if(this.value == objTr.children("td.partida").html()){
		         validoPartida = false;
        }
   
	 });	
	if (!validoPartida) {
		$('input[name=\'cuentas_[]\']').each(function (index) {
	        if(this.value == objTr.children("td.cuenta").html()){
	        		alert("Esta partida ya fue seleccionada");		        	
			        valido = false;
	        }
	
		 });			
	}
	
	if (valido) {
		if(objTr.find("'input[name=\'debe[]\']'").val().length<1) {
			objTr.find("'input[name=\'debe[]\']'").val('0');
		}

		if(objTr.find("'input[name=\'haber[]\']'").val().length<1) {
			objTr.find("'input[name=\'haber[]\']'").val('0');
		}
		
		
		if((objTr.find("'input[name=\'debe[]\']'").val()!=0 && objTr.find("'input[name=\'haber[]\']'").val()!=0) || (objTr.find("'input[name=\'debe[]\']'").val()==0 && objTr.find("'input[name=\'haber[]\']'").val()==0)){
			valido = false;
		    alert("Un s\u00f3lo campo (debe/haber) debe estar en cero.");
		}
		else if (parseFloat(objTr.find("'input[name=\'debe_[]\']'").val()) > parseFloat(objTr.children("td.montoCompromiso").html().replace(".","").replace(",","."))){
			valido = false;
			alert("El monto por el debe no puede ser mayor al  compromiso");
		} // El monto por el haber si puede ser mayor al monto del compromiso,. pues se estaría acreditando
		else {
			$('#cuerpoRegistro').show('fade',300);	
			$("#tablaRegistro").show('fade',300);
			
			//Tabla registro definitivo cuentas codi
			var tbody = $('#tablaRegistro')[0];
			var fila = document.createElement("tr");
			fila.className='normalNegrita registro';
			//Número de registro
			var columna1 = document.createElement("td");
			columna1.setAttribute("class","numero");
			columna1.appendChild(document.createTextNode(r));

			//Código cuenta contable
			var columna2 = document.createElement("td");
			columna2.appendChild(document.createTextNode(objTr.children("td.cuenta").html()));
			/*Arreglo cuenta contable*/			
			var input2 = document.createElement("input");
			input2.setAttribute("type","hidden");
			input2.setAttribute("name","cuentas_[]");
			input2.value= objTr.children("td.cuenta").html();
			columna2.appendChild(input2);				 		
	 		columna2.className='cuenta';		
	
	 		//Categoría programática	
			var columna3 = document.createElement("td");
			//columna3.appendChild(document.createTextNode(objTr.children("td.proy_acc").html()));
			columna3.className='proy_acc';
			
			/*
			var span = document.createElement("span");
			span.appendChild(document.createTextNode(objTr.children("td.proy_acc").html()));
			span.className='nombreProyAcc';
			columna3.appendChild(span);
			*/
			
			columna3.appendChild(document.createTextNode(objTr.children("td.proy_acc").find('span').html()));
			
			//Arreglo Proyecto/Acc
	 		var input3_1 = document.createElement("input");
			input3_1.setAttribute("type","hidden");
			input3_1.setAttribute("name","proy_acc[]");
			input3_1.value= objTr.find("'input[name=\'proy[]\']'").val();
			//Arreglo Acción específica
			var input3_2 = document.createElement("input");
			input3_2.setAttribute("type","hidden");
			input3_2.setAttribute("name","proy_aesp[]");
			input3_2.value= objTr.find("'input[name=\'aesp[]\']'").val();
			//Arreglo tipo imputación
			var input3_3 = document.createElement("input");						
			input3_3.setAttribute("type","hidden");
			input3_3.setAttribute("name","proy_tipo[]");
			input3_3.value= objTr.find("'input[name=\'tipo[]\']'").val();
			columna3.appendChild(input3_1);
			columna3.appendChild(input3_2);
			columna3.appendChild(input3_3);						

	 		//Código partida presupuestaria			
			var columna4 = document.createElement("td");
			columna4.appendChild(document.createTextNode(objTr.children("td.partida").html()));
	 		//Arreglo partidas presupuestarias			
	 		var input4 = document.createElement("input");
	 		input4.setAttribute("type","hidden");
			input4.setAttribute("name","partidas_[]");
			input4.value= objTr.children("td.partida").html();
			columna4.appendChild(input4);				 		
	 		columna4.className='partida';

	 		//Denominación de la cuenta contable	 		
			var columna5 = document.createElement("td");
			columna5.appendChild(document.createTextNode(objTr.children("td.denominacion").html()));
			columna5.className='denominacion';

			//Monto debe
			var columna6 = document.createElement("td");
			columna6.appendChild(document.createTextNode(objTr.find("'input[name=\'debe[]\']'").val()));
			//Arreglo monto debe			
			var input6 = document.createElement("input");
			input6.setAttribute("type","hidden");
			input6.setAttribute("name","debe_[]");
			input6.value= objTr.find("'input[name=\'debe[]\']'").val();
			columna6.appendChild(input6);					
			columna6.className='debe';

			//Monto Haber
			var columna7 = document.createElement("td");
			columna7.appendChild(document.createTextNode(objTr.find("'input[name=\'haber[]\']'").val()));
			//Arreglo monto haber
			var input7 = document.createElement("input");
			input7.setAttribute("type","hidden");
			input7.setAttribute("name","haber_[]");
			input7.value= objTr.find("'input[name=\'haber[]\']'").val();
			columna7.appendChild(input7);					
			columna7.className='haber';

			//Opción eliminar
			var columna8 = document.createElement("td");
			columna8.setAttribute("valign","top");
			columna8.className = 'link';
			deleteLink = document.createElement("a");
			deleteLink.setAttribute("href","javascript:void(0);");
			linkText = document.createTextNode("Eliminar");
			deleteLink.appendChild(linkText);
			columna8.appendChild(deleteLink);
	
			r++;
			
			$(deleteLink).bind('click', function(){
	 			eliminarRegistro($(this));
	 			/*i=0;
	 			  $("#tablaRegistro > tr").each(
                          function(index) {
                                  $(this).children("td.numero").html(i);
                                  i++;
                          });	*/			 		 			
	 			
	 		});
	
			fila.appendChild(columna1);				
			fila.appendChild(columna2);
			fila.appendChild(columna3);				
			fila.appendChild(columna4);
			fila.appendChild(columna5);				
			fila.appendChild(columna6);
			fila.appendChild(columna7);
			fila.appendChild(columna8);	
			
			i=0;
			tbody.appendChild(fila);
			$("#tablaRegistro > tr").each(
                   function(index) {
                           $(this).children("td.numero").html(i);
                            i++;
                    });						
		}
	}
}
	
function eliminarRegistro(obj){
	var objTr = obj.parent("td").parent("tr");
	objTr.remove();
	i=0;
	  $("#tablaRegistro > tr").each(
            function(index) {
                    $(this).children("td.numero").html(i);
                    i++;
            });			
}