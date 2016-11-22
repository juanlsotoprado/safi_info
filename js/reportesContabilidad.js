function detalle(codigo) {
    url="detalle.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}

function ejecutar() {
	valor = "";
	valor_estado ='0';
	for(i=0;i<document.form.reporte.length;i++)
		if(document.form.reporte[i].checked) valor=document.form.reporte[i].value;

	for(i=0;i<document.form.estado.length;i++)
		if(document.form.estado[i].checked) 
			valor_estado=document.form.estado[i].value;	
	
	if(valor!='C' && valor!='P') 
		alert("Debe seleccionar el tipo de reporte");
	else if (valor=='C' && document.form.fecha_inicio.value.length<4) 
		alert("Debe seleccionar la fecha inicio del reporte");	
	else if (document.form.fecha_fin.value.length<4) 
		alert("Debe seleccionar la fecha del reporte");
	else if (document.form.cuenta.options[document.form.cuenta.selectedIndex].value=='0') 
		alert("Debe seleccionar la cuenta bancaria");
	else if (valor=='P' && valor_estado=='C' ) 
		alert("Debe seleccionar la categor\u00eda");	
	else {
		$("#hid_validar").val('1');
		$("#form").attr('action','reportesContabilidad.php?accion=cuentaBancariaPagadoAccion');
		$("#form").submit();
	}
		
}

function ejecutar2() {
	if ($("#cuenta").val()=='-1') {
		alert("Debe seleccionar una cuenta bancaria");
	}
	else if ($("#fecha_inicio").val().length < 4 || $("#fecha_fin").val().length < 4) {
		 alert("Debe seleccionar un rango de fechas");
		 return false;
	}
	else {
		$("#form").attr('action','reportesContabilidad.php?accion=cuentaCausadoPagadoAccion');
		$("#form").submit();
	}
		
}
function deshabilitar_combo(valor) {
	if(valor=='1') { 
		//document.getElementById('seleccion_causado').style.display=""; 	
		//document.getElementById('fecha').style.display="";		
		document.getElementById('seleccion_pagado').style.display="none";  
 	}
	else  {
	//	document.getElementById('fecha').style.display="none";		
		//$("#fecha_inicio").val('01/01/'+currentTime.getFullYear());
		document.getElementById('seleccion_causado').style.display="none"; 		
		document.getElementById('seleccion_pagado').style.display=""; 	
	}
}



