$().ready(function() {
	var codis=new Array();
	var codisIndice = -1;
	
	  $('.detalleOpcion').click(function(event) {
		 
			if (jQuery.browser.msie) {
				event.cancelBubble = true;
			} else {
				event.stopPropagation();
			}

			//alert($(this).attr("docgId"));
			BuscarDetalleCodi($(this).attr("docgId"));

			$('div.principalpop').show();
			$('div.principalpop').show();			
				
			Detalle(event,$(this));
			
			$('div.principalpop').show('fade', function() {
			$("div.secundariopop").show('fade');
			
		
			//});
	  });	
});

		$('#cuentaContable').autocomplete({
			source: function(request, response){
				$.ajax({
					url: "../../acciones/convertidor.php",
					dataType: "json",
					data: {
						accion: "BuscarAsociacionConvertidor",
						key: request.term,
						tipoRespuesta: 'json'

					},
					success: function(json){
						var index = 0;
						var items = new Array();

	                    var val = $(json['listaConvertidor']).length ;
	                    if(val < 1){
	                    	$('#cuentaContable').val('');
	                    	if($('#cuentaNombre').html() != ''){
									 $('#cuentaNombre').html('');
	                        }
	                     }
	                   // alert(JSON.stringify(json));
	                   // console.info(json);
						$.each(json.listaConvertidor, function(idCuenta, objCuenta){
							
							var label = idCuenta + ": " + objCuenta.partida.id + ": " + objCuenta.cuentaContable.nombre;
							items[index++] = {
									id: idCuenta,
									label: label,
									value: idCuenta + " : " + objCuenta.partida.id + ": " + objCuenta.cuentaContable.nombre,
									nombreCuenta: objCuenta.cuentaContable.nombre,
									idPartida: objCuenta.partida.id
							};
						});
						response(items);
					}
				});
			},
			minLength: 1
			/*select: function(event, ui)
			{

				partida_denom = ui.item.nombre;
				partida = ui.item.id;
				$('#cuentaContable').val(ui.item.id);
				//$('#cuentaNombre').html(ui.item.nombre);
				return true;
			}*/
	 });	

});

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

function imprimir(){
	cadenaCodis = "";
	for(i=0; i<codis.length; i++){
		if(codis[i]!=null){
			cadenaCodis += "%27"+codis[i]+"%27,";
		}
	}
	if(cadenaCodis!=""){
		cadenaCodis = cadenaCodis.substring(0,cadenaCodis.length - 1);
		//location.href="codiMultiplePDF.php?codis="+cadenaCodis;
		location.href="codi.php?accion=GenerarPDF&caso=multiple&codis="+cadenaCodis;
		return;
	}else{
		alert("Debe seleccionar al menos un comprobante diario para generar documento PDF.");
		return;
	}
}

function imprimirSalidaEstandar(){
	cadenaCodis = "";
	for(i=0; i<codis.length; i++){
		if(codis[i]!=null){
			cadenaCodis += "'"+codis[i]+"',";
		}
	}
	if(cadenaCodis!=""){
		cadenaCodis = cadenaCodis.substring(0,cadenaCodis.length - 1);
		$('#formImpresionMultiple').find('input[name=\'codis\']').val(cadenaCodis)
		$('#formImpresionMultiple').submit();
		return;
	}else{
		alert("Debe seleccionar al menos un comprobante diario para generar documento PDF.");
		return;
	}
}

function BuscarDetalleCodi(id){
	$.ajax({
			url: "../../acciones/codi/codi.php",
			dataType: "json",
			data: {
				accion: "Anular",
				key: id
			},
			success: function(json){
				
				var tbody = $('#codiDetalle')[0];
				var tbodyP = $('#codiPresupuesto')[0];
				
				/*Detalle inf. general del codi*/
				 $.each(json[0], function(index, value) {
					 $("#id_codi").val(value.comp_id);
					 $("#idCodi").html(value.comp_id);
					 $("#documentoAsociado").html("Documento asociado:"+value.comp_doc_id);
					 $("#referenciaBancaria").html("Nro. Referencia bancaria:"+value.nro_referencia);
					 $("#fuenteFinanciamiento").html("Fuente de financiamiento:"+value.fuente_financiamiento);
					 $("#compromiso").html("Nro. compromiso:"+value.nro_compromiso);
					 $("#justificacion").html("Justificaci&oacute;n:"+value.comp_comen);
				 });
				 //Detalle contable
				 i = 1;
				 $("tr.trCaso").remove();
				 $.each(json[1], function(index, value) {
					 var fila = document.createElement("tr");
					 fila.className='normalNegro trCaso';
					 		
					 var columna1 = document.createElement("td");
					 columna1.setAttribute("class","numero");
					 columna1.appendChild(document.createTextNode(i));
			 		
					 var columna2 = document.createElement("td");
					 columna2.appendChild(document.createTextNode(value.cpat_id));
					 columna2.className='cuenta';

					 var columna3 = document.createElement("td");
					 columna3.appendChild(document.createTextNode(value.cpat_nombre));
					 columna3.className='cuentaNombre';

					 var columna4 = document.createElement("td");
					 columna4.appendChild(document.createTextNode(value.rcomp_debe));
					 columna4.className='montoDebe';

					 var columna5 = document.createElement("td");
					 columna5.appendChild(document.createTextNode(value.rcomp_haber));
					 columna5.className='montoHaber';
			 		

					 fila.appendChild(columna1);				
					 fila.appendChild(columna2);
					 fila.appendChild(columna3);
					 fila.appendChild(columna4);
					 fila.appendChild(columna5);

					 tbody.appendChild(fila);
					// i++;
				// });
				 
				/*Detalle Presupuestario*/
				 //i = 1;
				// $.each(json[2], function(index, value) {
					 var filaP = document.createElement("tr");
					 filaP.className='normalNegro trCaso';
					 		
			 		var columna1P = document.createElement("td");
			 		columna1P.setAttribute("class","numero");
			 		columna1P.appendChild(document.createTextNode(i));
			 		
			 		var columna2P = document.createElement("td");
			 		columna2P.appendChild(document.createTextNode(value.centros));
			 		columna2P.className='centros';
			 		
			 		/*var columna3P = document.createElement("td");
			 		columna3P.appendChild(document.createTextNode(value.centro_costo));
			 		columna3P.className='centro_costo';*/

			 		var columna4P = document.createElement("td");
			 		columna4P.appendChild(document.createTextNode(value.part_id));
			 		columna4P.className='part_id';

			 		var columna5P = document.createElement("td");
			 		columna5P.appendChild(document.createTextNode(value.cpat_id));
			 		columna5P.className='cpat_id';
			 		
			 		var columna6P = document.createElement("td");
			 		columna6P.appendChild(document.createTextNode(value.rcomp_debe-value.rcomp_haber));
			 		columna6P.className='cadt_monto';

			 		
			 		filaP.appendChild(columna1P);				
			 		filaP.appendChild(columna2P);
			 		//filaP.appendChild(columna3P);
			 		filaP.appendChild(columna4P);
			 		filaP.appendChild(columna5P);
			 		filaP.appendChild(columna6P);
			 		tbodyP.appendChild(filaP);	
			 		i++;
				 });
			}
	});			
}