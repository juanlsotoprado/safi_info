var idRequ = "";
var proveedores = new Array();
var nombresProveedores = new Array();
var fechaCotizacionProveedores = new Array();
var articulos = new Array();
var numeroItems = new Array();
var articulosOrdenDeCompra = new Array();
var nombresArticulos = new Array();
var proveedoresArticulos = new Array();
var ivas = new Array();
var ivasBandera = new Array();
var proveedoresIva = new Array();
var proveedoresRedondear = new Array();
var proveedoresTotal = new Array();
var i = 0;
function limpiar(){
	i = 0;
	while(i<articulos.length){
		document.getElementById("unidad"+numeroItems[i]).value="";
		document.getElementById("cantidadUnitaria"+numeroItems[i]).value="";
		document.getElementById("precioUnitario"+numeroItems[i]).value="";
		i++;
	}
	i = 0;
	while(i<articulosOrdenDeCompra.length){
		document.getElementById("unidad"+articulosOrdenDeCompra[i][0]+"-"+articulosOrdenDeCompra[i][5]).value="";
		document.getElementById("cantidadUnitaria"+articulosOrdenDeCompra[i][0]+"-"+articulosOrdenDeCompra[i][5]).value="";
		document.getElementById("precioUnitario"+articulosOrdenDeCompra[i][0]+"-"+articulosOrdenDeCompra[i][5]).value="";
		i++;
	}
	i = 0;
	while(i<ivas.length){
		document.getElementById("base"+ivas[i]).value = "";
		i++;
	}
}
function ivaRegistrado(nuevoIva){
	i = 0;
	while(i<ivas.length){
		if(ivas[i]==nuevoIva){
			return i;
		}
		i++;
	}
	return -1;
}
function limpiarIvasBandera(){
	i = 0;
	while(i<ivasBandera.length){
		ivasBandera[i] = false;
		i++;
	}
}
function confirmar(){
	/*var cadena = "proveedoresTotal.length: "+proveedoresTotal.length+"\n"
	+"proveedoresIva.length: "+proveedoresIva.length+"\n"
	+"proveedoresRedondear.length: "+proveedoresRedondear.length+"\n";
	
	cadena += "\nproveedoresTotal\n\n";
	i=0;
	while(i<proveedoresTotal.length){
		cadena += i+" "+proveedoresTotal[i]+"\n";
		i++;
	}
	
	cadena += "\nproveedoresIva\n\n";
	i=0;
	while(i<proveedoresIva.length){
		cadena += i+" "+proveedoresIva[i]+"\n";
		i++;
	}
	
	cadena += "\nproveedoresRedondear\n\n";
	i=0;
	while(i<proveedoresRedondear.length){
		cadena += i+" "+proveedoresRedondear[i]+"\n";
		i++;
	}
	
	i=0;
	alert(cadena);*/
	
	if(document.getElementById("proveedor").value!=""){
		error = "";
		j=0;
		while(j<proveedores.length){
			if(document.getElementById("proveedor").value==proveedores[j]){
				error = "Ya existe una cotizaci"+oACUTE+"n para este proveedor";
				break;
			}
			j++;
		}
		if(error==""){
			if(document.getElementById("fechaCotizacion").value==""){
				error = "Debe indicar la fecha de la cotizaci"+oACUTE+"n";
			}
		}
		if(error==""){
			articulosAuxiliar = new Array();
			proveedoresTotal[proveedoresTotal.length] = 0;
			proveedoresIva[proveedoresIva.length] = new Array();
			proveedoresRedondear[proveedoresRedondear.length] = document.getElementById("redondear").checked;
			j = 0;
			while(j<ivas.length){
				base = (document.getElementById("base"+ivas[j]).value!="" && document.getElementById("base"+ivas[j]).value!="0")?parseFloat(document.getElementById("base"+ivas[j]).value):parseFloat(0);
				proveedoresIva[proveedoresIva.length-1][j] = base;
				if(base!=parseFloat(0)){
					indiceIva = ivaRegistrado(ivas[j]);
					if(indiceIva!=-1){
						if(proveedoresRedondear[proveedoresRedondear.length-1]==true){
							proveedoresTotal[proveedoresTotal.length-1] += base*(ivas[j]/100);
						}else{
							textStr = (base*(ivas[j]/100))+"";
							if(textStr.indexOf(".")>-1 && textStr.indexOf(".")+3<textStr.length){
								textStr = textStr.substring(0,textStr.indexOf(".")+3);
							}
							proveedoresTotal[proveedoresTotal.length-1] += parseFloat(textStr);
						}
						ivasBandera[indiceIva] = true;
					}
				}
				j++;
			}
			j = 0;
			while(j<articulos.length){
				unidad = ((document.getElementById("unidad"+numeroItems[j]).value!="" && document.getElementById("unidad"+numeroItems[j]).value>0)?document.getElementById("unidad"+numeroItems[j]).value:1);
				cantidad = document.getElementById("cantidadUnitaria"+numeroItems[j]).value;
				precio = document.getElementById("precioUnitario"+numeroItems[j]).value;
				if(cantidad!="" && precio!=""){
					articulosAuxiliar[articulosAuxiliar.length]=new Array(articulos[j],cantidad,precio,unidad,numeroItems[j],"rqui");
					if(proveedoresRedondear[proveedoresRedondear.length-1]==true){
						proveedoresTotal[proveedoresTotal.length-1] += unidad*cantidad*precio;
					}else{
						textStr = (unidad*cantidad*precio)+"";
						if(textStr.indexOf(".")>-1 && textStr.indexOf(".")+3<textStr.length){
							textStr = textStr.substring(0,textStr.indexOf(".")+3);
						}
						proveedoresTotal[proveedoresTotal.length-1] += parseFloat(textStr);
					}
				}else{
					if(cantidad!="" || precio!=""){
						error = "Si desea incorporar el rubro "+nombresArticulos[j]+" (c"+oACUTE+"digo "+articulos[j]+") a la cotizaci"+oACUTE+"n debe indicar la cantidad unitaria y el precio unitario";
						proveedoresIva.splice(proveedoresIva.length-1,1);
						proveedoresRedondear.splice(proveedoresRedondear.length-1,1);
						proveedoresTotal.splice(proveedoresTotal.length-1,1);
						break;
					}
				}
				j++;
			}
			
			j = 0;
			while(j<articulosOrdenDeCompra.length){
				unidad = ((document.getElementById("unidad"+articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]).value!="" && document.getElementById("unidad"+articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]).value>0)?document.getElementById("unidad"+articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]).value:1);
				cantidad = document.getElementById("cantidadUnitaria"+articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]).value;
				precio = document.getElementById("precioUnitario"+articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]).value;
				if(cantidad!="" && precio!=""){
					articulosAuxiliar[articulosAuxiliar.length]=new Array(articulosOrdenDeCompra[j][0],cantidad,precio,unidad,articulosOrdenDeCompra[j][5],"ordc");
					if(proveedoresRedondear[proveedoresRedondear.length-1]==true){
						proveedoresTotal[proveedoresTotal.length-1] += unidad*cantidad*precio;
					}else{
						textStr = (unidad*cantidad*precio)+"";
						if(textStr.indexOf(".")>-1 && textStr.indexOf(".")+3<textStr.length){
							textStr = textStr.substring(0,textStr.indexOf(".")+3);
						}
						proveedoresTotal[proveedoresTotal.length-1] += parseFloat(textStr);
					}
				}else{
					if(cantidad!="" || precio!=""){
						error = "Si desea incorporar el rubro "+articulosOrdenDeCompra[j][1]+" (c"+oACUTE+"digo "+articulosOrdenDeCompra[j][0]+") a la cotizaci"+oACUTE+"n debe indicar la cantidad unitaria y el precio unitario";
						proveedoresIva.splice(proveedoresIva.length-1,1);
						proveedoresRedondear.splice(proveedoresRedondear.length-1,1);
						proveedoresTotal.splice(proveedoresTotal.length-1,1);
						break;
					}
				}
				j++;
			}
		}
		if(error==""){
			if(articulosAuxiliar.length<1){
				error = "Debe indicar la cantidad unitaria y el precio unitario de al menos un (1) rubro";
			}
		}
		if(error!=""){
			alert(error);
			return;
		}
		
		if(confirm("Los datos han sido ingresados de manera correcta. "+pACUTE+"Desea continuar?")){
			proveedores[proveedores.length] = document.getElementById("proveedor").value;
			nombresProveedores[nombresProveedores.length] = document.getElementById("proveedor").options[document.getElementById("proveedor").options.selectedIndex].text;
			fechaCotizacionProveedores[fechaCotizacionProveedores.length]=document.getElementById("fechaCotizacion").value;
			proveedoresArticulos[proveedoresArticulos.length] = articulosAuxiliar;

			var tbody = document.getElementById('cotizaciones');
			var tbodyArticulosOrdenDeCompra = document.getElementById('articulosOrdenDeCompra');
			var tbodyTotales = document.getElementById('totales');
			var header1 = document.getElementById('header1');
			var header2 = document.getElementById('header2');
			var header3 = document.getElementById('header3');
			var subTotal = document.getElementById('subTotal');
			var total = document.getElementById('filaTotal');
			//var footer = document.getElementById('filaFooter');

			columna = document.createElement("td");
			columna.setAttribute("id","header1td"+proveedores[proveedores.length-1]);
			columna.setAttribute("align","center");
			columna.setAttribute("colspan",3);
			radio = document.createElement("input");
			radio.setAttribute("id","proveedorSeleccionado"+proveedores[proveedores.length-1]);
			radio.setAttribute("type","radio");
			radio.setAttribute("value",proveedores[proveedores.length-1]);
			radio.setAttribute("name","proveedorSeleccionado");
			radio.setAttribute("onclick","habiProveedor(this);");
			radio.value=proveedores[proveedores.length-1];
			columna.appendChild(radio);
			header1.appendChild(columna);
			
			columna = document.createElement("td");
			columna.setAttribute("id","header2td"+proveedores[proveedores.length-1]);
			columna.setAttribute("class","normalNegro");
			columna.setAttribute("colspan",3);
			text = document.createTextNode(nombresProveedores[nombresProveedores.length-1]);
			/*date = new Date();
			date.setDate(date.getDate()-1);
			fecha = document.createTextNode("Fecha de cot.: "+formatDate(date,'dd/MM/yyyy'));*/
			fecha = document.createTextNode("Fecha de cot.: "+document.getElementById("fechaCotizacion").value);
			a = document.createElement("a");
			a.setAttribute("href","javascript:eliminarCotizacion('"+proveedores[proveedores.length-1]+"')");
			img = document.createElement("img");
			img.setAttribute("src","../../imagenes/delete.png");
			img.setAttribute("border","0");
			a.appendChild(img);
			a.appendChild(document.createTextNode(" Eliminar"));
			columna.appendChild(text);
			columna.appendChild(document.createElement("br"));
			columna.appendChild(fecha);
			columna.appendChild(document.createElement("br"));
			columna.appendChild(a);
			header2.appendChild(columna);
			
			columna = document.createElement("td");
			columna.setAttribute("id","header3cctd"+proveedores[proveedores.length-1]);
			columna.setAttribute("align","center");
			columna.setAttribute("width","60px");
			text = document.createTextNode("Cantidad Cotizada");
			columna.appendChild(text);
			header3.appendChild(columna);

			columna = document.createElement("td");
			columna.setAttribute("id","header3prtd"+proveedores[proveedores.length-1]);
			columna.setAttribute("align","center");
			columna.setAttribute("width","60px");
			text = document.createTextNode("Precio");
			columna.appendChild(text);
			header3.appendChild(columna);

			columna = document.createElement("td");
			columna.setAttribute("id","header3totd"+proveedores[proveedores.length-1]);
			columna.setAttribute("align","center");
			columna.setAttribute("width","60px");
			text = document.createTextNode("Total");
			columna.appendChild(text);
			header3.appendChild(columna);

			montoSubTotal = 0;
			j=0;
			k=0;
			while(j<articulos.length){
				fila = document.getElementById('articulo'+numeroItems[j]);
				if(k<articulosAuxiliar.length){
					if(articulosAuxiliar[k][4]==numeroItems[j]){
						
						columna = document.createElement("td");
						columna.setAttribute("id",numeroItems[j]+"cctd"+proveedores[proveedores.length-1]);
						columna.setAttribute("align","center");
						text = document.createTextNode(articulosAuxiliar[k][1]*articulosAuxiliar[k][3]);
						columna.appendChild(text);
						fila.appendChild(columna);

						columna = document.createElement("td");
						columna.setAttribute("id",numeroItems[j]+"prtd"+proveedores[proveedores.length-1]);
						columna.setAttribute("align","center");
						text = document.createTextNode(articulosAuxiliar[k][2]);
						columna.appendChild(text);
						fila.appendChild(columna);
						
						columna = document.createElement("td");
						columna.setAttribute("id",numeroItems[j]+"totd"+proveedores[proveedores.length-1]);
						columna.setAttribute("align","center");
						
						if(proveedoresRedondear[proveedores.length-1]==true){
							text = document.createTextNode(roundNumber(articulosAuxiliar[k][1]*articulosAuxiliar[k][2]*articulosAuxiliar[k][3],2));
						}else{
							textStr = (articulosAuxiliar[k][1]*articulosAuxiliar[k][2]*articulosAuxiliar[k][3])+"";
							if(textStr.indexOf(".")>-1 && textStr.indexOf(".")+3<textStr.length){
								textStr = textStr.substring(0,textStr.indexOf(".")+3);
							}
							text = document.createTextNode(textStr);
						}
						columna.appendChild(text);
						fila.appendChild(columna);

						if(proveedoresRedondear[proveedores.length-1]==true){
							montoSubTotal+=articulosAuxiliar[k][1]*articulosAuxiliar[k][2]*articulosAuxiliar[k][3];
						}else{
							textStr = (articulosAuxiliar[k][1]*articulosAuxiliar[k][2]*articulosAuxiliar[k][3])+"";
							if(textStr.indexOf(".")>-1 && textStr.indexOf(".")+3<textStr.length){
								textStr = textStr.substring(0,textStr.indexOf(".")+3);
							}
							montoSubTotal+=parseFloat(textStr);
						}
						k++;
					}else{			
						columna = document.createElement("td");
						columna.setAttribute("id",numeroItems[j]+"cctd"+proveedores[proveedores.length-1]);
						text = document.createTextNode(" ");
						columna.appendChild(text);
						fila.appendChild(columna);
						
						columna = document.createElement("td");
						columna.setAttribute("id",numeroItems[j]+"prtd"+proveedores[proveedores.length-1]);
						text = document.createTextNode(" ");
						columna.appendChild(text);
						fila.appendChild(columna);

						columna = document.createElement("td");
						columna.setAttribute("id",numeroItems[j]+"totd"+proveedores[proveedores.length-1]);
						text = document.createTextNode(" ");
						columna.appendChild(text);
						fila.appendChild(columna);
					}
				}else{
					columna = document.createElement("td");
					columna.setAttribute("id",numeroItems[j]+"cctd"+proveedores[proveedores.length-1]);
					text = document.createTextNode(" ");
					columna.appendChild(text);
					fila.appendChild(columna);

					columna = document.createElement("td");
					columna.setAttribute("id",numeroItems[j]+"prtd"+proveedores[proveedores.length-1]);
					text = document.createTextNode(" ");
					columna.appendChild(text);
					fila.appendChild(columna);

					columna = document.createElement("td");
					columna.setAttribute("id",numeroItems[j]+"totd"+proveedores[proveedores.length-1]);
					text = document.createTextNode(" ");
					columna.appendChild(text);
					fila.appendChild(columna);
				}
				j++;
			}
			j=0;
			while(j<articulosOrdenDeCompra.length){
				fila = document.getElementById("articuloCotizacion"+articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]);
				if(k<articulosAuxiliar.length){
					if(articulosAuxiliar[k][0]==articulosOrdenDeCompra[j][0] && articulosAuxiliar[k][4]==articulosOrdenDeCompra[j][5]){
						
						columna = document.createElement("td");
						columna.setAttribute("id",articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]+"cctd"+proveedores[proveedores.length-1]);
						columna.setAttribute("align","center");
						text = document.createTextNode(articulosAuxiliar[k][1]*articulosAuxiliar[k][3]);
						columna.appendChild(text);
						fila.appendChild(columna);

						columna = document.createElement("td");
						columna.setAttribute("id",articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]+"prtd"+proveedores[proveedores.length-1]);
						columna.setAttribute("align","center");
						text = document.createTextNode(articulosAuxiliar[k][2]);
						columna.appendChild(text);
						fila.appendChild(columna);
						
						columna = document.createElement("td");
						columna.setAttribute("id",articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]+"totd"+proveedores[proveedores.length-1]);
						columna.setAttribute("align","center");
						
						if(proveedoresRedondear[proveedores.length-1]==true){
							text = document.createTextNode(roundNumber(articulosAuxiliar[k][1]*articulosAuxiliar[k][2]*articulosAuxiliar[k][3],2));
						}else{
							textStr = (articulosAuxiliar[k][1]*articulosAuxiliar[k][2]*articulosAuxiliar[k][3])+"";
							if(textStr.indexOf(".")>-1 && textStr.indexOf(".")+3<textStr.length){
								textStr = textStr.substring(0,textStr.indexOf(".")+3);
							}
							text = document.createTextNode(textStr);
						}
						columna.appendChild(text);
						fila.appendChild(columna);

						if(proveedoresRedondear[proveedores.length-1]==true){
							montoSubTotal+=articulosAuxiliar[k][1]*articulosAuxiliar[k][2]*articulosAuxiliar[k][3];
						}else{
							textStr = (articulosAuxiliar[k][1]*articulosAuxiliar[k][2]*articulosAuxiliar[k][3])+"";
							if(textStr.indexOf(".")>-1 && textStr.indexOf(".")+3<textStr.length){
								textStr = textStr.substring(0,textStr.indexOf(".")+3);
							}
							montoSubTotal+=parseFloat(textStr);
						}
						k++;
					}else{			
						columna = document.createElement("td");
						columna.setAttribute("id",articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]+"cctd"+proveedores[proveedores.length-1]);
						text = document.createTextNode(" ");
						columna.appendChild(text);
						fila.appendChild(columna);
						
						columna = document.createElement("td");
						columna.setAttribute("id",articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]+"prtd"+proveedores[proveedores.length-1]);
						text = document.createTextNode(" ");
						columna.appendChild(text);
						fila.appendChild(columna);

						columna = document.createElement("td");
						columna.setAttribute("id",articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]+"totd"+proveedores[proveedores.length-1]);
						text = document.createTextNode(" ");
						columna.appendChild(text);
						fila.appendChild(columna);
					}
				}else{
					columna = document.createElement("td");
					columna.setAttribute("id",articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]+"cctd"+proveedores[proveedores.length-1]);
					text = document.createTextNode(" ");
					columna.appendChild(text);
					fila.appendChild(columna);

					columna = document.createElement("td");
					columna.setAttribute("id",articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]+"prtd"+proveedores[proveedores.length-1]);
					text = document.createTextNode(" ");
					columna.appendChild(text);
					fila.appendChild(columna);

					columna = document.createElement("td");
					columna.setAttribute("id",articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]+"totd"+proveedores[proveedores.length-1]);
					text = document.createTextNode(" ");
					columna.appendChild(text);
					fila.appendChild(columna);
				}
				j++;
			}
			
			columna = document.createElement("td");
			columna.setAttribute("id","subTotaltd"+proveedores[proveedores.length-1]);
			columna.setAttribute("align","right");
			columna.setAttribute("class","normalNegro");
			columna.setAttribute("colspan",3);
			
			//SUBTOTAL
			if(proveedoresRedondear[proveedores.length-1]==true){
				text = document.createTextNode(roundNumber(montoSubTotal,2));
			}else{
				textStr = montoSubTotal+"";
				if(textStr.indexOf(".")>-1 && textStr.indexOf(".")+3<textStr.length){
					textStr = textStr.substring(0,textStr.indexOf(".")+3);
				}
				text = document.createTextNode(textStr);
			}
			columna.appendChild(text);
			subTotal.appendChild(columna);
			
			j=0;
			while(j<ivas.length){
				filaIva = document.getElementById("iva"+ivas[j]);
				if(filaIva){
					//tbody.removeChild(filaIva);
					tbodyTotales.removeChild(filaIva);
				}
				j++;
			}
			//tbody.removeChild(total);
			tbodyTotales.removeChild(total);
			//tbody.removeChild(footer);

			//IVAs
			j=0;
			while(j<ivas.length){
				if(ivasBandera[j]==true){
					filaIva = document.createElement("tr");
					filaIva.setAttribute("id","iva"+ivas[j]);
					
					columna = document.createElement("td");
					columna.setAttribute("align","right");
					columna.setAttribute("colspan","2");
					text = document.createTextNode("IVA "+ivas[j]+"%");
					columna.appendChild(text);
					filaIva.appendChild(columna);
					
					columna = document.createElement("td");
					columna.setAttribute("align","right");
					text = document.createTextNode(" ");
					columna.appendChild(text);
					filaIva.appendChild(columna);

					k=0;
					while(k<proveedores.length){
						columna = document.createElement("td");
						columna.setAttribute("id","iva"+ivas[j]+proveedores[k]);
						columna.setAttribute("align","right");
						columna.setAttribute("colspan",3);
						columna.setAttribute("class","normalNegro");
						if(proveedoresRedondear[k]==true){
							text = document.createTextNode(roundNumber(proveedoresIva[k][j]*(ivas[j]/100),2));							
						}else{
							textStr = (proveedoresIva[k][j]*(ivas[j]/100))+"";
							if(textStr.indexOf(".")>-1 && textStr.indexOf(".")+3<textStr.length){
								textStr = textStr.substring(0,textStr.indexOf(".")+3);
							}
							text = document.createTextNode(textStr);
						}
						columna.appendChild(text);
						filaIva.appendChild(columna);
						k++;
					}
					//tbody.appendChild(filaIva);
					tbodyTotales.appendChild(filaIva);
				}
				j++;
			}
			
			total = document.createElement("tr");
			total.setAttribute("id","filaTotal");
			
			columna = document.createElement("td");
			columna.setAttribute("align","right");
			columna.setAttribute("colspan","2");
			text = document.createTextNode("Total");
			columna.appendChild(text);
			total.appendChild(columna);
			
			columna = document.createElement("td");
			columna.setAttribute("align","right");
			text = document.createTextNode(" ");
			columna.appendChild(text);
			total.appendChild(columna);
			
			j=0;
			while(j<proveedores.length){
				columna = document.createElement("td");
				columna.setAttribute("id","totaltd"+proveedores[j]);
				columna.setAttribute("align","right");
				columna.setAttribute("colspan",3);
				columna.setAttribute("class","normalNegro");
				if(proveedoresRedondear[j]==true){
					text = document.createTextNode(roundNumber(proveedoresTotal[j],2));							
				}else{
					textStr = (proveedoresTotal[j])+"";
					if(textStr.indexOf(".")>-1 && textStr.indexOf(".")+3<textStr.length){
						textStr = textStr.substring(0,textStr.indexOf(".")+3);
					}
					text = document.createTextNode(textStr);
				}
				columna.appendChild(text);
				total.appendChild(columna);
				j++;
			}
			//tbody.appendChild(total);
			tbodyTotales.appendChild(total);

			/*footer = document.createElement("tr");
			footer.setAttribute("id","filaFooter");
			footer.setAttribute("class","td_gray");
			columna = document.createElement("td");
			columna.setAttribute("class","normal");
			columna.setAttribute("height","15px");
			columna.setAttribute("colspan",(proveedores.length*3)+2);
			text = document.createTextNode(" ");
			columna.appendChild(text);
			footer.appendChild(columna);
			tbody.appendChild(footer);*/
		}
	}else{
		alert("Debe seleccionar un proveedor");
	}
}

function enviar(){
	var submitForm = document.getElementById("submitForm");
	var input;
	
	fechaorden = trim(document.getElementById("fechaOrden").value);//fecha de orden
	
	seleccionados = document.getElementsByName("proveedorSeleccionado");
	criterio = trim(document.getElementById("criterio").value);
	observaciones = trim(document.getElementById("observaciones").value);
	formaPago = trim(document.getElementById("formaPago").value);
	lugarEntrega = trim(document.getElementById("lugarEntrega").value);
	justificacion = trim(document.getElementById("justificacion").value);

	fechaEntrega = trim(document.getElementById("fechaEntrega").value);
	garantiaAnticipo = trim(document.getElementById("garantiaAnticipo").value);
	condicionesEntrega = trim(document.getElementById("condicionesEntrega").value);
	otrasGarantias = trim(document.getElementById("otrasGarantias").value);
	otrasCondiciones = trim(document.getElementById("otrasCondiciones").value);
	bandeja = "";
	
	if(fechaorden==""){
		alert("Debe seleccionar fecha de la orden de compra");
		return;
	}
	if ( document.getElementById("bandeja") && document.getElementById("bandeja").value ) {
		bandeja = trim(document.getElementById("bandeja").value);
	}
	
	if(proveedores.length<1){
		alert("Debe ingresar al menos una (1) cotizaci"+oACUTE+"n");
		return;
	}

	j = 0;
	proveedor = "";
	while(j<seleccionados.length && proveedor==""){
		if(seleccionados[j].checked==true){
			proveedor = seleccionados[j].value;
		}
		j++;
	}
	if(proveedor==""){
		alert("Debe seleccionar al menos un (1) proveedor para generar la orden de compra");
		return;
	}

	if(criterio==""){
		alert("Debe debe indicar el criterio de selecci"+oACUTE+"n del proveedor");
		return;
	}else if(criterio=="6" && observaciones==""){
		alert("El criterio de selecci"+oACUTE+"n indicado es \"Otros\", por lo tanto debe especificarlo en las Observaciones.");
		return;
	}
	
	if(formaPago==""){
		alert("Debe debe indicar la forma de pago de la orden de compra");
		return;
	}

	if(lugarEntrega==""){
		alert("Debe debe indicar el lugar de entrega de la orden de compra");
		return;
	}

	if(justificacion==""){
		alert("Debe debe indicar la justificaci"+oACUTE+"n de la orden de compra");
		return;
	}

	if(confirm("Los datos han sido ingresados de manera correcta. "+pACUTE+"Desea continuar?")){
		parametros = "";
		if(idRequ!=""){
			
			//fecha de orden de compra agregado el 22/04/2015
			input = document.createElement("input");
			input.setAttribute("id","fechadeOrden");
			input.setAttribute("name","fechadeOrden");
			input.setAttribute("value",fechaorden);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","idRequ");
			input.setAttribute("name","idRequ");
			input.setAttribute("value",idRequ);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","proveedor");
			input.setAttribute("name","proveedor");
			input.setAttribute("value",proveedor);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","criterio");
			input.setAttribute("name","criterio");
			input.setAttribute("value",criterio);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","observaciones");
			input.setAttribute("name","observaciones");
			input.setAttribute("value",observaciones);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","justificacion");
			input.setAttribute("name","justificacion");
			input.setAttribute("value",justificacion);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","formaPago");
			input.setAttribute("name","formaPago");
			input.setAttribute("value",formaPago);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","lugarEntrega");
			input.setAttribute("name","lugarEntrega");
			input.setAttribute("value",lugarEntrega);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","fechaEntrega");
			input.setAttribute("name","fechaEntrega");
			input.setAttribute("value",fechaEntrega);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","garantiaAnticipo");
			input.setAttribute("name","garantiaAnticipo");
			input.setAttribute("value",garantiaAnticipo);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","condicionesEntrega");
			input.setAttribute("name","condicionesEntrega");
			input.setAttribute("value",condicionesEntrega);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","otrasGarantias");
			input.setAttribute("name","otrasGarantias");
			input.setAttribute("value",otrasGarantias);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","otrasCondiciones");
			input.setAttribute("name","otrasCondiciones");
			input.setAttribute("value",otrasCondiciones);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","bandeja");
			input.setAttribute("name","bandeja");
			input.setAttribute("value",bandeja);
			submitForm.appendChild(input);
			
			/*parametros = "idRequ="+idRequ;
			parametros += "&proveedor="+proveedor;
			parametros += "&criterio="+criterio;
			parametros += "&observaciones="+observaciones;
			parametros += "&justificacion="+justificacion;
			parametros += "&formaPago="+formaPago;
			parametros += "&lugarEntrega="+lugarEntrega;
			parametros += "&fechaEntrega="+fechaEntrega;
			parametros += "&garantiaAnticipo="+garantiaAnticipo;
			parametros += "&condicionesEntrega="+condicionesEntrega;
			parametros += "&otrasGarantias="+otrasGarantias;
			parametros += "&otrasCondiciones="+otrasCondiciones;
			parametros += "&bandeja="+bandeja;
			
			accion = "ordenDeCompraAccion.php?";*/
		}else if(idOrdc!=""){
			
			input = document.createElement("input");
			input.setAttribute("id","codigo");
			input.setAttribute("name","codigo");
			input.setAttribute("value",codigo);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","tipoRequ");
			input.setAttribute("name","tipoRequ");
			input.setAttribute("value",tipoRequ);
			submitForm.appendChild(input);

			input = document.createElement("input");
			input.setAttribute("id","pagina");
			input.setAttribute("name","pagina");
			input.setAttribute("value",pagina);
			submitForm.appendChild(input);

			input = document.createElement("input");
			input.setAttribute("id","controlFechas");
			input.setAttribute("name","controlFechas");
			input.setAttribute("value",controlFechas);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","fechaInicio");
			input.setAttribute("name","fechaInicio");
			input.setAttribute("value",fechaInicio);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","fechaFin");
			input.setAttribute("name","fechaFin");
			input.setAttribute("value",fechaFin);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","tipoBusq");
			input.setAttribute("name","tipoBusq");
			input.setAttribute("value",tipoBusq);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","dependencia");
			input.setAttribute("name","dependencia");
			input.setAttribute("value",dependencia);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","codigoCR");
			input.setAttribute("name","codigoCR");
			input.setAttribute("value",codigoCR);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","estado");
			input.setAttribute("name","estado");
			input.setAttribute("value",estado);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","idOrdc");
			input.setAttribute("name","idOrdc");
			input.setAttribute("value",idOrdc);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","proveedor");
			input.setAttribute("name","proveedor");
			input.setAttribute("value",proveedor);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","criterio");
			input.setAttribute("name","criterio");
			input.setAttribute("value",criterio);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","observaciones");
			input.setAttribute("name","observaciones");
			input.setAttribute("value",observaciones);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","justificacion");
			input.setAttribute("name","justificacion");
			input.setAttribute("value",justificacion);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","formaPago");
			input.setAttribute("name","formaPago");
			input.setAttribute("value",formaPago);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","lugarEntrega");
			input.setAttribute("name","lugarEntrega");
			input.setAttribute("value",lugarEntrega);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","fechaEntrega");
			input.setAttribute("name","fechaEntrega");
			input.setAttribute("value",fechaEntrega);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","garantiaAnticipo");
			input.setAttribute("name","garantiaAnticipo");
			input.setAttribute("value",garantiaAnticipo);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","condicionesEntrega");
			input.setAttribute("name","condicionesEntrega");
			input.setAttribute("value",condicionesEntrega);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","otrasGarantias");
			input.setAttribute("name","otrasGarantias");
			input.setAttribute("value",otrasGarantias);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","otrasCondiciones");
			input.setAttribute("name","otrasCondiciones");
			input.setAttribute("value",otrasCondiciones);
			submitForm.appendChild(input);
			
			input = document.createElement("input");
			input.setAttribute("id","bandeja");
			input.setAttribute("name","bandeja");
			input.setAttribute("value",bandeja);
			submitForm.appendChild(input);
			
			/*
			parametros = "codigo="+codigo;
			parametros += "&tipoRequ="+tipoRequ;
			parametros += "&pagina="+pagina;
			parametros += "&controlFechas="+controlFechas;
			parametros += "&fechaInicio="+fechaInicio;
			parametros += "&fechaFin="+fechaFin;
			parametros += "&tipoBusq="+tipoBusq;
			parametros += "&dependencia="+dependencia;
			parametros += "&codigoCR="+codigoCR;
			parametros += "&estado="+estado;
			parametros += "&idOrdc="+idOrdc;
			parametros += "&proveedor="+proveedor;
			parametros += "&criterio="+criterio;
			parametros += "&observaciones="+observaciones;
			parametros += "&justificacion="+justificacion;
			parametros += "&formaPago="+formaPago;
			parametros += "&lugarEntrega="+lugarEntrega;
			parametros += "&fechaEntrega="+fechaEntrega;
			parametros += "&garantiaAnticipo="+garantiaAnticipo;
			parametros += "&condicionesEntrega="+condicionesEntrega;
			parametros += "&otrasGarantias="+otrasGarantias;
			parametros += "&otrasCondiciones="+otrasCondiciones;
			parametros += "&bandeja="+bandeja;
			
			accion = "modificarOrdenDeCompraAccion.php?";
			*/
		}
		//if(parametros!=""){
			var items = "";
			k=0;
			while(k<articulosOrdenDeCompra.length){
				items += articulosOrdenDeCompra[k][0]+"~"+articulosOrdenDeCompra[k][5]+"~"+articulosOrdenDeCompra[k][4]+"|";
				k++;
			}
			input = document.createElement("input");
			input.setAttribute("id","items");
			input.setAttribute("name","items");
			input.setAttribute("value",items);
			submitForm.appendChild(input);
			
			var proveedoresCadena = "";
			j=0;
			while(j<proveedores.length){
				proveedoresCadena += proveedores[j]+";";
				//parametros += "&fechaCotizacion"+proveedores[j]+"="+fechaCotizacionProveedores[j];
				input = document.createElement("input");
				input.setAttribute("id","fechaCotizacion"+proveedores[j]);
				input.setAttribute("name","fechaCotizacion"+proveedores[j]);
				input.setAttribute("value",fechaCotizacionProveedores[j]);
				submitForm.appendChild(input);
				
				//parametros += "&redondear"+proveedores[j]+"="+proveedoresRedondear[j];
				input = document.createElement("input");
				input.setAttribute("id","redondear"+proveedores[j]);
				input.setAttribute("name","redondear"+proveedores[j]);
				input.setAttribute("value",proveedoresRedondear[j]);
				submitForm.appendChild(input);
				
				var base = "";
				k=0;
				while(k<ivas.length){
					if(proveedoresIva[j][k]>0){
						base += ivas[k]+","+proveedoresIva[j][k]+";";						
					}
					k++;
				}
				input = document.createElement("input");
				input.setAttribute("id","base"+proveedores[j]);
				input.setAttribute("name","base"+proveedores[j]);
				input.setAttribute("value",base);
				submitForm.appendChild(input);
				
				var cotizacion = "";
				articulosAuxiliar = proveedoresArticulos[j];
				k=0;
				while(k<articulosAuxiliar.length){
					cotizacion += articulosAuxiliar[k][0]+","+articulosAuxiliar[k][1]+","+articulosAuxiliar[k][2]+","+articulosAuxiliar[k][3]+","+articulosAuxiliar[k][4]+","+articulosAuxiliar[k][5]+";";
					k++;
				}
				input = document.createElement("input");
				input.setAttribute("id","cotizacion"+proveedores[j]);
				input.setAttribute("name","cotizacion"+proveedores[j]);
				input.setAttribute("value",cotizacion);
				submitForm.appendChild(input);
				
				j++;
			}
			input = document.createElement("input");
			input.setAttribute("id","proveedores");
			input.setAttribute("name","proveedores");
			input.setAttribute("value",proveedoresCadena);
			submitForm.appendChild(input);
			
			//location.href = accion+parametros+proveedoresCadena;
			submitForm.submit();
		/*}else{
			alert("No se puede realizar la operaci"+oACUTE+"n. Refresque la p"+aACUTE+"gina e intente nuevamente.");
		}*/
	}
}

function cargarRequisicion(){
	idRequ = document.getElementById("idRequ").value;
	if(idRequ != ""){
		location.href = "ordenDeCompra.php?idRequ="+idRequ;
	}else{
		alert("Debe indicar el c"+oACUTE+"digo de la requisici"+oACUTE+"n");
	}
}

function habiProveedor(element){
	cantidades = document.getElementsByTagName("input");
	if(element){
		j=0;
		while(j<cantidades.length){
			if(strStartsWith(cantidades[j].name,"cantidadASolicitar")==true){
				if(strStartsWith(cantidades[j].name,"cantidadASolicitar"+element.value)){
					cantidades[j].removeAttribute("readonly");
				}else{
					cantidades[j].setAttribute("readonly","readonly");
					cantidades[j].value = "";
				}
			}
			j++;
		}
	}else{
		j=0;
		while(j<cantidades.length){
			if(strStartsWith(cantidades[j].name,"cantidadASolicitar")==true){
				cantidades[j].setAttribute("readonly","readonly");
				cantidades[j].value = "";
			}
			j++;
		}
	}
}

function eliminarCotizacion(proveedor){
	/*var cadena = "proveedoresTotal.length: "+proveedoresTotal.length+"\n"
	+"proveedoresIva.length: "+proveedoresIva.length+"\n"
	+"proveedoresRedondear.length: "+proveedoresRedondear.length+"\n";
	
	cadena += "\nproveedoresTotal\n\n";
	i=0;
	while(i<proveedoresTotal.length){
		cadena += i+" "+proveedoresTotal[i]+"\n";
		i++;
	}
	
	cadena += "\nproveedoresIva\n\n";
	i=0;
	while(i<proveedoresIva.length){
		cadena += i+" "+proveedoresIva[i]+"\n";
		i++;
	}
	
	cadena += "\nproveedoresRedondear\n\n";
	i=0;
	while(i<proveedoresRedondear.length){
		cadena += i+" "+proveedoresRedondear[i]+"\n";
		i++;
	}
	
	i=0;
	alert(cadena);*/
	
	j=0;
	while(j<proveedores.length){
		if(proveedor==proveedores[j]){
			break;
		}
		j++;
	}
	if(confirm(pACUTE+"Est"+aACUTE+" seguro que desea eliminar la cotizaci"+oACUTE+"n del proveedor "+nombresProveedores[j]+"?")){
		j=0;
		proveedoresTamano = proveedores.length;
		while(j<proveedoresTamano){
			if(proveedor==proveedores[j]){
				proveedores.splice(j,1);
				proveedoresArticulos.splice(j,1);
				nombresProveedores.splice(j,1);
				proveedoresIva.splice(j,1);
				proveedoresRedondear.splice(j,1);
				proveedoresTotal.splice(j,1);
				fechaCotizacionProveedores.splice(j,1);
				break;
			}
			j++;
		}
		limpiarIvasBandera();
		/*j=0;
		while(j<proveedoresArticulos.length){
			k=0;
			while(k<proveedoresArticulos[j].length){
				indiceIva = ivaRegistrado(proveedoresArticulos[j][k][3]);
				if(indiceIva!=-1){
					ivasBandera[indiceIva]=true;
				}
				k++;
			}
			j++;
		}*/
		j=0;
		while(j<ivas.length){
			k=0;
			while(k<proveedoresIva.length){
				if(proveedoresIva[k][j]!=0){
					indiceIva = ivaRegistrado(ivas[j]);
					if(indiceIva!=-1){
						ivasBandera[indiceIva]=true;
					}
				}
				k++;
			}
			j++;
		}

		var tbody = document.getElementById('cotizaciones');
		var tbodyTotales = document.getElementById('totales');
		var header1 = document.getElementById('header1');
		var header2 = document.getElementById('header2');
		var header3 = document.getElementById('header3');
		var subTotal = document.getElementById('subTotal');
		var total = document.getElementById('filaTotal');
		//var footer = document.getElementById('filaFooter');

		header1.removeChild(document.getElementById("header1td"+proveedor));
		header2.removeChild(document.getElementById("header2td"+proveedor));

		header3.removeChild(document.getElementById("header3cctd"+proveedor));
		header3.removeChild(document.getElementById("header3prtd"+proveedor));
		header3.removeChild(document.getElementById("header3totd"+proveedor));

		j=0;
		while(j<articulos.length){
			fila = document.getElementById('articulo'+numeroItems[j]);
			fila.removeChild(document.getElementById(numeroItems[j]+"cctd"+proveedor));
			fila.removeChild(document.getElementById(numeroItems[j]+"prtd"+proveedor));
			fila.removeChild(document.getElementById(numeroItems[j]+"totd"+proveedor));
			j++;
		}
		
		j=0;
		while(j<articulosOrdenDeCompra.length){
			fila = document.getElementById("articuloCotizacion"+articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]);
			fila.removeChild(document.getElementById(articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]+"cctd"+proveedor));
			fila.removeChild(document.getElementById(articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]+"prtd"+proveedor));
			fila.removeChild(document.getElementById(articulosOrdenDeCompra[j][0]+"-"+articulosOrdenDeCompra[j][5]+"totd"+proveedor));
			j++;
		}

		subTotal.removeChild(document.getElementById("subTotaltd"+proveedor));
		
		j=0;
		while(j<ivas.length){
			filaIva = document.getElementById("iva"+ivas[j]);
			if(filaIva){
				tbodyTotales.removeChild(filaIva);					
			}
			j++;
		}
		tbodyTotales.removeChild(total);
		//tbody.removeChild(footer);

		j=0;
		while(j<ivas.length){
			if(ivasBandera[j]==true){
				filaIva = document.createElement("tr");
				filaIva.setAttribute("id","iva"+ivas[j]);
				
				columna = document.createElement("td");
				columna.setAttribute("align","right");
				columna.setAttribute("colspan","2");
				text = document.createTextNode("IVA "+ivas[j]+"%");
				columna.appendChild(text);
				filaIva.appendChild(columna);
				
				columna = document.createElement("td");
				columna.setAttribute("align","right");
				text = document.createTextNode(" ");
				columna.appendChild(text);
				filaIva.appendChild(columna);

				k=0;
				while(k<proveedores.length){
					columna = document.createElement("td");
					columna.setAttribute("id","iva"+ivas[j]+proveedores[k]);
					columna.setAttribute("align","right");
					columna.setAttribute("colspan",3);
					columna.setAttribute("class","normalNegro");
					if(proveedoresRedondear[k]==true){
						text = document.createTextNode(roundNumber(proveedoresIva[k][j]*(ivas[j]/100),2));							
					}else{
						textStr = (proveedoresIva[k][j]*(ivas[j]/100))+"";
						if(textStr.indexOf(".")>-1 && textStr.indexOf(".")+3<textStr.length){
							textStr = textStr.substring(0,textStr.indexOf(".")+3);
						}
						text = document.createTextNode(textStr);
					}
					text = document.createTextNode(roundNumber(proveedoresIva[k][j]*(ivas[j]/100),2));
					columna.appendChild(text);
					filaIva.appendChild(columna);
					k++;
				}
				tbodyTotales.appendChild(filaIva);
			}
			j++;
		}
		
		total = document.createElement("tr");
		total.setAttribute("id","filaTotal");
		
		columna = document.createElement("td");
		columna.setAttribute("align","right");
		columna.setAttribute("colspan","2");
		text = document.createTextNode("Total");
		columna.appendChild(text);
		total.appendChild(columna);
		
		columna = document.createElement("td");
		columna.setAttribute("align","right");
		text = document.createTextNode(" ");
		columna.appendChild(text);
		total.appendChild(columna);
		
		j=0;
		while(j<proveedores.length){
			columna = document.createElement("td");
			columna.setAttribute("id","totaltd"+proveedores[j]);
			columna.setAttribute("align","right");
			columna.setAttribute("colspan",3);
			columna.setAttribute("class","normalNegro");
			text = document.createTextNode(roundNumber(proveedoresTotal[j],2));
			columna.appendChild(text);
			total.appendChild(columna);
			j++;
		}
		tbodyTotales.appendChild(total);

		/*footer = document.createElement("tr");
		footer.setAttribute("id","filaFooter");
		footer.setAttribute("class","td_gray");
		columna = document.createElement("td");
		columna.setAttribute("class","normal");
		columna.setAttribute("height","15px");
		columna.setAttribute("colspan",(proveedores.length*3)+2);
		text = document.createTextNode(" ");
		columna.appendChild(text);
		footer.appendChild(columna);
		tbody.appendChild(footer);*/
	}
	/*i = 0;
	cadena = "";
	while(i<proveedoresArticulos.length){
		j=0;
		cadena += "\n\nProveedor "+nombresProveedores[i]+"\trif: "+proveedores[i]+"\tiva: "+ivaProveedores[i]+"\tfecha: "+fechaCotizacionProveedores[i];
		while(j<proveedoresArticulos[i].length){
			cadena += "\narticulo: "+proveedoresArticulos[i][j][0];
			cadena += "\tcantidad: "+proveedoresArticulos[i][j][1];
			cadena += "\tprecio: "+proveedoresArticulos[i][j][2];
			j++;
		}
		i++;
	}
	i=0;
	alert(cadena);*/
}

function limpiarItem(){
	document.getElementById("itemCompletar").value="";
	document.getElementById("itemCompletar").focus();
	document.getElementById("articuloEspecificaciones").value="";
	document.getElementById("articuloEspecificacionesLen").value="10000";
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

function agregarItem(){
	if(trim(document.getElementById("itemCompletar").value)==""){
		alert("Introduzca la partida o una palabra contenida en el nombre del art"+iACUTE+"culo, activo o servicio.");
		document.getElementById("itemCompletar").focus();
	}else{
		if(trim(document.getElementById("articuloEspecificaciones").value)==""){
			alert("Introduzca las especificaciones del art"+iACUTE+"culo, activo o servicio.");
			document.getElementById("articuloEspecificaciones").focus();	
		}else{
			tokens = document.getElementById("itemCompletar").value.split( ":" );
			if(tokens[0] && tokens[1]){
				idPartida = trim(tokens[0]);
				nombreItem = trim(tokens[1]);
				indiceIdItem = estaEnItems(nombreItem,idPartida);
				if(indiceIdItem>-1){
					var tbody = document.getElementById('item');
					idItem = idsItems[indiceIdItem];

					indiceGeneral = articulosOrdenDeCompra.length;
					nombrePartida = nombresPartidasItems[indiceIdItem];
					especificaciones = trim(document.getElementById("articuloEspecificaciones").value);
					
					var registro = new Array(6);
					registro[0]=idItem;
					registro[1]=nombreItem;
					registro[2]=idPartida;
					registro[3]=nombrePartida;
					registro[4]=especificaciones;
					registro[5]=new Date().getTime();
					
					var fila = document.createElement("tr");
					fila.className='normalNegro';
					
					//CODIGO DEL PRODUCTO
					var columna1 = document.createElement("td");
					columna1.setAttribute("align","center");
					columna1.setAttribute("valign","top");
					columna1.className='normalNegro';
					/*var inputIdItem = document.createElement("INPUT");
					inputIdItem.setAttribute("type","hidden");
					inputIdItem.setAttribute("id","txt_id_art"+registro[5]);
					inputIdItem.setAttribute("name","txt_id_art"+registro[5]);
					inputIdItem.value=registro[0];
					columna1.appendChild(inputIdItem);*/
					editLink = document.createElement("a");
					linkText = document.createTextNode("Eliminar");
					editLink.setAttribute("href", "javascript:eliminarItem('"+(indiceGeneral+1)+"',"+registro[0]+","+registro[5]+")");
					editLink.appendChild(linkText);
					columna1.appendChild (editLink);
					columna1.appendChild(document.createElement("br"));
					columna1.appendChild(document.createTextNode(registro[0]));
					
					//DENOMINACION DEL PRODUCTO
					var columna2 = document.createElement("td");
					columna2.setAttribute("align","left");
					columna2.setAttribute("valign","top");
					columna2.className='normalNegro';
					/*var inputNombreItem = document.createElement("INPUT");
					inputNombreItem.setAttribute("type","hidden");
					inputNombreItem.setAttribute("id","txt_nb_art"+registro[5]);
					inputNombreItem.setAttribute("name","txt_nb_art"+registro[5]);
					inputNombreItem.value=registro[1];
					columna2.appendChild(inputNombreItem);*/
					columna2.appendChild(document.createTextNode(registro[1]));
					
					//CODIGO DE LA PARTIDA
					var columna3 = document.createElement("td");
					columna3.setAttribute("align","center");
					columna3.setAttribute("valign","top");
					columna3.className='normalNegro';
					/*var inputIdPartida = document.createElement("INPUT");
					inputIdPartida.setAttribute("type","hidden");
					inputIdPartida.setAttribute("id","txt_id_pda"+registro[5]);
					inputIdPartida.setAttribute("name","txt_id_pda"+registro[5]);
					inputIdPartida.value=registro[2];
					columna3.appendChild(inputIdPartida);*/
					columna3.appendChild(document.createTextNode(registro[2]));
					
					//DENOMINACION DE LA PARTIDA
					var columna4 = document.createElement("td");
					columna4.setAttribute("align","left");
					columna4.setAttribute("valign","top");
					columna4.className='normalNegro';
					/*var inputNombrePartida = document.createElement("INPUT");
					inputNombrePartida.setAttribute("type","hidden");
					inputNombrePartida.setAttribute("id","txt_nb_pda"+registro[5]);
					inputNombrePartida.setAttribute("name","txt_nb_pda"+registro[5]);
					inputNombrePartida.value=registro[3];
					columna4.appendChild(inputNombrePartida);*/
					columna4.appendChild(document.createTextNode(registro[3]));
					
					//DESCRIPCION
					var columna5 = document.createElement("td");
					columna5.setAttribute("align","left");
					columna5.setAttribute("valign","top");
					columna5.className='normalNegro';
					/*var inputEspecificaciones = document.createElement("INPUT");
					inputEspecificaciones.setAttribute("type","hidden");
					inputEspecificaciones.setAttribute("id","especificaciones"+registro[0]+"-"+registro[5]);
					inputEspecificaciones.setAttribute("name","especificaciones"+registro[0]+"-"+registro[5]);
					inputEspecificaciones.value=registro[4];
					columna5.appendChild(inputEspecificaciones);*/
					columna5.appendChild(document.createTextNode(registro[4]));
					
					//CANTIDAD
					var columna6 = document.createElement("td");
					columna6.setAttribute("align","center");
					columna6.setAttribute("valign","top");
					columna6.className='normalNegro';
					columna6.appendChild(document.createTextNode(" "));
					
					//CANTIDAD SOLICITADA
					var columna7 = document.createElement("td");
					columna7.setAttribute("align","center");
					columna7.setAttribute("valign","top");
					columna7.className='normalNegro';
					columna7.appendChild(document.createTextNode(" "));
					
					//CANTIDAD REQUERIDA
					var columna8 = document.createElement("td");
					columna8.setAttribute("align","center");
					columna8.setAttribute("valign","top");
					columna8.className='normalNegro';
					columna8.appendChild(document.createTextNode(" "));
					
					//UNIDAD
					var columna9 = document.createElement("td");
					columna9.setAttribute("align","center");
					columna9.setAttribute("valign","top");
					columna9.className='normalNegro';
					var inputUnidad = document.createElement("INPUT");
					inputUnidad.setAttribute("type","text");
					inputUnidad.setAttribute("id","unidad"+registro[0]+"-"+registro[5]);
					inputUnidad.setAttribute("name","unidad"+registro[0]+"-"+registro[5]);
					inputUnidad.setAttribute("size","8");
					inputUnidad.setAttribute("maxlength","10");
					inputUnidad.setAttribute("onkeyup","validarInteger(this)");
					inputUnidad.value="";
					inputUnidad.className='normalNegro';
					columna9.appendChild(inputUnidad);
					
					//CANTIDAD UNITARIA
					var columna10 = document.createElement("td");
					columna10.setAttribute("align","center");
					columna10.setAttribute("valign","top");
					columna10.className='normalNegro';
					var inputUnidad = document.createElement("INPUT");
					inputUnidad.setAttribute("type","text");
					inputUnidad.setAttribute("id","cantidadUnitaria"+registro[0]+"-"+registro[5]);
					inputUnidad.setAttribute("name","cantidadUnitaria"+registro[0]+"-"+registro[5]);
					inputUnidad.setAttribute("size","8");
					inputUnidad.setAttribute("maxlength","10");
					inputUnidad.setAttribute("onkeyup","validarInteger(this)");
					inputUnidad.value="";
					inputUnidad.className='normalNegro';
					columna10.appendChild(inputUnidad);
					
					//PRECIO UNITARIO
					var columna11 = document.createElement("td");
					columna11.setAttribute("align","center");
					columna11.setAttribute("valign","top");
					columna11.className='normalNegro';
					var inputUnidad = document.createElement("INPUT");
					inputUnidad.setAttribute("type","text");
					inputUnidad.setAttribute("id","precioUnitario"+registro[0]+"-"+registro[5]);
					inputUnidad.setAttribute("name","precioUnitario"+registro[0]+"-"+registro[5]);
					inputUnidad.setAttribute("size","8");
					inputUnidad.setAttribute("maxlength","10");
					inputUnidad.setAttribute("onkeyup","validarDecimal(this)");
					inputUnidad.value="";
					inputUnidad.className='normalNegro';
					columna11.appendChild(inputUnidad);

					fila.appendChild(columna1);
					fila.appendChild(columna2);
					fila.appendChild(columna3);
					fila.appendChild(columna4);
					fila.appendChild(columna5);
					fila.appendChild(columna6);
					fila.appendChild(columna7);
					fila.appendChild(columna8);
					fila.appendChild(columna9);
					fila.appendChild(columna10);
					fila.appendChild(columna11);
					tbody.appendChild(fila);

					articulosOrdenDeCompra[articulosOrdenDeCompra.length]=registro;

					document.getElementById('hid_largo').value=articulosOrdenDeCompra.length.toString(10);
					limpiarItem();
					
					var tbodyArticulosOrdenDeCompra = document.getElementById('articulosOrdenDeCompra');
					var fila = document.createElement("tr");
					fila.setAttribute("id","articuloCotizacion"+registro[0]+"-"+registro[5]);
					fila.className='normalNegro';
					
					columna1 = document.createElement("td");
					columna1.setAttribute("align","left");
					columna1.setAttribute("valign","top");
					columna1.appendChild(document.createTextNode(registro[1]));
					
					columna2 = document.createElement("td");
					columna2.setAttribute("align","left");
					columna2.setAttribute("valign","top");
					columna2.appendChild(document.createTextNode(registro[4]));
					
					columna3 = document.createElement("td");
					columna3.setAttribute("align","center");
					columna3.setAttribute("valign","top");
					columna3.appendChild(document.createTextNode(" "));
					
					fila.appendChild(columna1);
					fila.appendChild(columna2);
					fila.appendChild(columna3);

					j=0;
					while(j<proveedores.length){
						columna = document.createElement("td");
						columna.setAttribute("id",registro[0]+"-"+registro[5]+"cctd"+proveedores[j]);
						text = document.createTextNode(" ");
						columna.appendChild(text);
						fila.appendChild(columna);

						columna = document.createElement("td");
						columna.setAttribute("id",registro[0]+"-"+registro[5]+"prtd"+proveedores[j]);
						text = document.createTextNode(" ");
						columna.appendChild(text);
						fila.appendChild(columna);

						columna = document.createElement("td");
						columna.setAttribute("id",registro[0]+"-"+registro[5]+"totd"+proveedores[j]);
						text = document.createTextNode(" ");
						columna.appendChild(text);
						fila.appendChild(columna);
						j++;
					}
					
					tbodyArticulosOrdenDeCompra.appendChild(fila);
				}else{
					alert("La partida o el nombre del rubro indicado no es v"+aACUTE+"lido");
				}
			}else{
				alert("Seleccione un rubro");
			}
		}
	}
}

function eliminarItem(indice, idItem, currenTimeItem){
	var estaEnCotizacion = false;

	j=0;
	while(j<proveedoresArticulos.length && estaEnCotizacion==false){
		k=0;
		while(k<proveedoresArticulos[j].length && estaEnCotizacion==false){
			if(proveedoresArticulos[j][k][0]==idItem && proveedoresArticulos[j][k][4]==currenTimeItem){
				estaEnCotizacion = true;
			}
			k++;
		}
		j++;
	}
	
	if(estaEnCotizacion == true){
		alert("No puede eliminar este rubro, una o varias de las cotizaciones agregadas lo incluyen.");
	}else{
		var tabla = document.getElementById('tbl_mod');
		var tbody = document.getElementById('item');
		var tbodyArticulosOrdenDeCompra = document.getElementById('articulosOrdenDeCompra');
		
		var fila = document.getElementById("articuloCotizacion"+idItem+"-"+currenTimeItem);
		tbodyArticulosOrdenDeCompra.removeChild(fila);
		
		for(i=0;i<articulosOrdenDeCompra.length;i++){
			tabla.deleteRow(articulos.length+1);
		}
		
		for(i=indice;i<articulosOrdenDeCompra.length;i++){
			articulosOrdenDeCompra[i-1]=articulosOrdenDeCompra[i];
		}
		
		articulosOrdenDeCompra.pop();
		
		document.getElementById('hid_largo').value=articulosOrdenDeCompra.length;
		
		for(i=0;i<articulosOrdenDeCompra.length;i++){
			var registro = articulosOrdenDeCompra[i];
			
			var fila = document.createElement("tr");
			fila.className='normalNegro';
			
			//CODIGO DEL PRODUCTO
			var columna1 = document.createElement("td");
			columna1.setAttribute("align","center");
			columna1.setAttribute("valign","top");
			columna1.className='normalNegro';
			/*var inputIdItem = document.createElement("INPUT");
			inputIdItem.setAttribute("type","hidden");
			inputIdItem.setAttribute("id","txt_id_art"+i);
			inputIdItem.setAttribute("name","txt_id_art"+i);
			inputIdItem.value=registro[0];
			columna1.appendChild(inputIdItem);*/
			editLink = document.createElement("a");
			linkText = document.createTextNode("Eliminar");
			editLink.setAttribute("href", "javascript:eliminarItem('"+(i+1)+"',"+registro[0]+","+registro[5]+")");
			editLink.appendChild(linkText);
			columna1.appendChild (editLink);
			columna1.appendChild(document.createElement("br"));
			columna1.appendChild(document.createTextNode(registro[0]));
			
			//DENOMINACION DEL PRODUCTO
			var columna2 = document.createElement("td");
			columna2.setAttribute("align","left");
			columna2.setAttribute("valign","top");
			columna2.className='normalNegro';
			/*var inputNombreItem = document.createElement("INPUT");
			inputNombreItem.setAttribute("type","hidden");
			inputNombreItem.setAttribute("id","txt_nb_art"+i);
			inputNombreItem.setAttribute("name","txt_nb_art"+i);
			inputNombreItem.value=registro[1];
			columna2.appendChild(inputNombreItem);*/
			columna2.appendChild(document.createTextNode(registro[1]));
			
			//CODIGO DE LA PARTIDA
			var columna3 = document.createElement("td");
			columna3.setAttribute("align","center");
			columna3.setAttribute("valign","top");
			columna3.className='normalNegro';
			/*var inputIdPartida = document.createElement("INPUT");
			inputIdPartida.setAttribute("type","hidden");
			inputIdPartida.setAttribute("id","txt_id_pda"+i);
			inputIdPartida.setAttribute("name","txt_id_pda"+i);
			inputIdPartida.value=registro[2];
			columna3.appendChild(inputIdPartida);*/
			columna3.appendChild(document.createTextNode(registro[2]));
			
			//DENOMINACION DE LA PARTIDA
			var columna4 = document.createElement("td");
			columna4.setAttribute("align","left");
			columna4.setAttribute("valign","top");
			columna4.className='normalNegro';
			/*var inputNombrePartida = document.createElement("INPUT");
			inputNombrePartida.setAttribute("type","hidden");
			inputNombrePartida.setAttribute("id","txt_nb_pda"+i);
			inputNombrePartida.setAttribute("name","txt_nb_pda"+i);
			inputNombrePartida.value=registro[3];
			columna4.appendChild(inputNombrePartida);*/
			columna4.appendChild(document.createTextNode(registro[3]));
			
			//DESCRIPCION
			var columna5 = document.createElement("td");
			columna5.setAttribute("align","left");
			columna5.setAttribute("valign","top");
			columna5.className='normalNegro';
			/*var inputEspecificaciones = document.createElement("INPUT");
			inputEspecificaciones.setAttribute("type","hidden");
			inputEspecificaciones.setAttribute("id","especificaciones"+registro[0]+"-"+registro[5]);
			inputEspecificaciones.setAttribute("name","especificaciones"+registro[0]+"-"+registro[5]);
			inputEspecificaciones.value=registro[4];
			columna5.appendChild(inputEspecificaciones);*/
			columna5.appendChild(document.createTextNode(registro[4]));
			
			//CANTIDAD
			var columna6 = document.createElement("td");
			columna6.setAttribute("align","center");
			columna6.setAttribute("valign","top");
			columna6.className='normalNegro';
			columna6.appendChild(document.createTextNode(" "));
			
			//CANTIDAD SOLICITADA
			var columna7 = document.createElement("td");
			columna7.setAttribute("align","center");
			columna7.setAttribute("valign","top");
			columna7.className='normalNegro';
			columna7.appendChild(document.createTextNode(" "));
			
			//CANTIDAD REQUERIDA
			var columna8 = document.createElement("td");
			columna8.setAttribute("align","center");
			columna8.setAttribute("valign","top");
			columna8.className='normalNegro';
			columna8.appendChild(document.createTextNode(" "));
			
			//UNIDAD
			var columna9 = document.createElement("td");
			columna9.setAttribute("align","center");
			columna9.setAttribute("valign","top");
			columna9.className='normalNegro';
			var inputUnidad = document.createElement("INPUT");
			inputUnidad.setAttribute("type","text");
			inputUnidad.setAttribute("id","unidad"+registro[0]+"-"+registro[5]);
			inputUnidad.setAttribute("name","unidad"+registro[0]+"-"+registro[5]);
			inputUnidad.setAttribute("size","8");
			inputUnidad.setAttribute("maxlength","10");
			inputUnidad.setAttribute("onkeyup","validarInteger(this)");
			inputUnidad.value="";
			inputUnidad.className='normalNegro';
			columna9.appendChild(inputUnidad);
			
			//CANTIDAD UNITARIA
			var columna10 = document.createElement("td");
			columna10.setAttribute("align","center");
			columna10.setAttribute("valign","top");
			columna10.className='normalNegro';
			var inputUnidad = document.createElement("INPUT");
			inputUnidad.setAttribute("type","text");
			inputUnidad.setAttribute("id","cantidadUnitaria"+registro[0]+"-"+registro[5]);
			inputUnidad.setAttribute("name","cantidadUnitaria"+registro[0]+"-"+registro[5]);
			inputUnidad.setAttribute("size","8");
			inputUnidad.setAttribute("maxlength","10");
			inputUnidad.setAttribute("onkeyup","validarInteger(this)");
			inputUnidad.value="";
			inputUnidad.className='normalNegro';
			columna10.appendChild(inputUnidad);
			
			//PRECIO UNITARIO
			var columna11 = document.createElement("td");
			columna11.setAttribute("align","center");
			columna11.setAttribute("valign","top");
			columna11.className='normalNegro';
			var inputUnidad = document.createElement("INPUT");
			inputUnidad.setAttribute("type","text");
			inputUnidad.setAttribute("id","precioUnitario"+registro[0]+"-"+registro[5]);
			inputUnidad.setAttribute("name","precioUnitario"+registro[0]+"-"+registro[5]);
			inputUnidad.setAttribute("size","8");
			inputUnidad.setAttribute("maxlength","10");
			inputUnidad.setAttribute("onkeyup","validarDecimal(this)");
			inputUnidad.value="";
			inputUnidad.className='normalNegro';
			columna11.appendChild(inputUnidad);

			fila.appendChild(columna1);
			fila.appendChild(columna2);
			fila.appendChild(columna3);
			fila.appendChild(columna4);
			fila.appendChild(columna5);
			fila.appendChild(columna6);
			fila.appendChild(columna7);
			fila.appendChild(columna8);
			fila.appendChild(columna9);
			fila.appendChild(columna10);
			fila.appendChild(columna11);
			tbody.appendChild(fila);
		}
	}
}
