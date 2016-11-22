

function revisar() {
	if(document.getElementById("idDocumento").value=="") {	
		alert("Debe especificar el documento");
		return;
	}
	else if(document.getElementById("dependencia").value=="") {	
		alert("Debe especificar la dependencia");
		return;
	}	
	/*else if(document.getElementById("beneficiario").value=="") {	
		alert("Debe especificar el beneficiario");
		return;
	}*/
	else if(document.getElementById("monto").value=="") {	
		alert("Debe especificar el monto");
		return;
	}	
	else if(document.getElementById("compromiso").value=="") {	
		alert("Debe especificar el compromiso");
		return;
	}	
	else if(confirm("Est\u00E1 seguro que desea registrar este documento?"))	{
		cedulasBeneficiariosCadena = "";
		nombresBeneficiariosCadena = "";
		tiposBeneficiariosCadena = "";
		for(i=0;i<beneficiarios.length;i++){
			cedulasBeneficiariosCadena += beneficiarios[i][0]+",";
			nombresBeneficiariosCadena += beneficiarios[i][1]+"*";
		}
		cedulasBeneficiariosCadena = cedulasBeneficiariosCadena.substring(0,cedulasBeneficiariosCadena.length-1);
		nombresBeneficiariosCadena = nombresBeneficiariosCadena.substring(0,nombresBeneficiariosCadena.length-1);
		document.getElementById("hid_bene_ci_rif").value=cedulasBeneficiariosCadena;
		document.getElementById("hid_beneficiario").value=nombresBeneficiariosCadena;
		document.getElementById("hid_contador").value=beneficiarios.length;		
		document.form1.submit()
	}	
}

function buscarDocumento(tipoDocumento, dependencia, idDocumento){
	if (dependencia=="-1") {
		alert ("Debe seleccionar la dependencia");
	} else {
		if (tipoDocumento.value=="-1") {
			//alert ("Debe seleccionar el tipo de documento");
		}
		else {

			var objInputCodigoDocumento = $("#idDocumento");
			var objInputBeneficiario = $("#beneficiario");
			var objInputMonto = $("#monto");
			var objInputObservaciones = $("#observaciones");
			var objInputCompromiso = $("#compromiso");
			var objInputFecha = $("#fechaDocumento");
			var errorIdCodigoDocumento = "#errorAsunto";
			$(objInputBeneficiario)[0].value = "";
			$(objInputMonto)[0].value = "";
			$(objInputObservaciones)[0].value = "";
			$(objInputCompromiso)[0].value = "";
			$(objInputFecha)[0].value = "";
			
			//objInputCodigoDocumento.autocomplete("destroy");
			
			objInputCodigoDocumento.autocomplete({
				source: function(request, response){
					$.ajax({
						url: "../../../acciones/registro/registroDocumento.php",
						dataType: "json",
						data: {
							accion: "Buscar",
							codigoDocumento: request.term,
							dependencia:dependencia,
							tipoDocumento:tipoDocumento
						},
						success: function(json){
							
							var index = 0;
							var items = new Array();
							//$(objInputCodigoDocumento)[0].value="";
							$(errorIdCodigoDocumento)[0].innerHTML="";							
											
							$.each(json.listaCodigoDocumento, function(codigoDocumento, objCodigoDocumento){
								items[index++] = {
										id: codigoDocumento,
										label: objCodigoDocumento.idDocumento,
										value: objCodigoDocumento.idDocumento
								};
							});
							if(items.length==0){
								$(errorIdCodigoDocumento)[0].innerHTML="C&oacute;digo inv&aacute;lido.";
							}else{
								response(items);
							}							
						}
					});
				},
				minLength: 1,
				select: function(event, ui) {
					$(objInputCodigoDocumento)[0].value=ui.item.value;
					$id = ui.item.value;

					/*Nueva incorporacion*/
					$.ajax({
						url: "../../../acciones/registro/registroDocumento.php",
						dataType: "json",
						data: {
							accion: "Completar",
							codigoDocumento: $id,
							tipoDocumento:tipoDocumento
						},
						success: function(json){
							var index = 0;
							var items = new Array();
							$(errorIdCodigoDocumento)[0].innerHTML="";		
							
							j=0;
							$.each(json.listaCodigoDocumento, function(codigoDocumento, objCodigoDocumento){
								if(j==1){
									$(objInputBeneficiario)[0].value=objCodigoDocumento;
								}else if(j==2){
									$(objInputMonto)[0].value=objCodigoDocumento;
								}else if(j==3){
									$(objInputObservaciones)[0].value=objCodigoDocumento;	
								}
								else if(j==4){
									$(objInputCompromiso)[0].value=objCodigoDocumento;	
								}
								else if(j==5){
									$(objInputFecha)[0].value=objCodigoDocumento;	
								}
								
								j++;
							});
						}
					});
					return false;	
				}
			});
		} 
	}
}