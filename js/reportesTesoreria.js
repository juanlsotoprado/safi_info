var tipo = 0;
var resultado = 0;

function buscar() {
	if ($("#opcionBusquedaNumero:checked").val()=='1') {
		if ($("#nroReferencia").val().length < 2) {
			alert("Debe indicar el n\u00famero de cheque o referencia");
			return;
		}
		else {
			resultado = 1
			};
	}	
	else if ($("#opcionBusquedaFecha1:checked").val()=='2') {
		if ($("#fechaInicioEmisionAnulado").val().length < 4 || $("#fechaFinEmisionAnulado").val().length < 4) {
			alert("Debe seleccionar las fechas de emisi\u00f3n y anulaci\u00f3n del cheque");
			return;
		}
		else resultado = 1;
	}	
	else if ($("#opcionBusquedaFecha2:checked").val()=='3') {
		if ($("#fechaInicioAnulado").val().length < 4 || $("#fechaFinAnulado").val().length < 4) {
			alert("Debe seleccionar un rango de fecha de anulaci\u00f3n del cheque");
			return;
		}
		else resultado = 1;
	}
	else if ($("#opcionBusquedaFecha3:checked").val()=='4') {
		if ($("#anoInicio").val() == -1 || $("#anoFin").val() == -1) {
			alert("Debe seleccionar los a\u00f1os de  la b\u00fasqueda");
			return;
		}
		else resultado = 1;
	}
	else if ($("#fechaInicio").val().length > 4 && $("#fechaFin").val().length > 4) {
		resultado = 1;
	}
	else if ($("#beneficiario").val().length > 6) 
		resultado = 1;
	if (resultado == 1) {
		$("#form").attr('action','reportesTesoreria.php?accion=BuscarAccion');
		$("#form").submit();
	}
	else {
		alert("Debe seleccionar un rango de fecha de fecha emisi\u00f3n");
		return;
		
	}
}

function limpiar(tipo) {
	if (tipo=='1') {
		$("#fechaInicio").val('');
		$("#fechaFin").val('');
	}
	else if (tipo=='2') {
		$("#fechaInicioEmisionAnulado").val('');
		$("#fechaFinEmisionAnulado").val('');
		$("#opcionBusquedaFecha1").removeAttr('checked');		
	}
	else {
		$("#fechaInicioAnulado").val('');
		$("#fechaFinAnulado").val('');
		$("#opcionBusquedaFecha2").removeAttr('checked');		
	}
	
}

function limpiarOpcion() {
	$("#nroReferencia").val('');			
	$("#opcionBusquedaNumero").removeAttr('checked');
	$("#nroReferencia").attr('disabled', 'disabled');
}

function deshabilitar_c(valor) {
	//Si es el nro de cheque
	if (valor=='c')  {
		$("#opcionBusquedaFecha1").removeAttr('disabled');
		$("#opcionBusquedaFecha2").removeAttr('disabled');
		$("#opcionBusquedaFecha3").removeAttr('disabled');		
								
		tipo = 'c';	
	}
	else if (valor=='t')  {
		$("#opcionBusquedaFecha1").attr('disabled', 'disabled');
		$("#opcionBusquedaFecha2").attr('disabled', 'disabled');
		$("#opcionBusquedaFecha3").attr('disabled', 'disabled');

		$("#nroReferencia").removeAttr('disabled');
		$("#anoInicio").val('-1');
		$("#anoFin").val('-1');
		$("#fechaInicioAnulado").val('');
		$("#fechaFinAnulado").val('');
		$("#fechaInicioEmisionAnulado").val('');
		$("#fechaFinEmisionAnulado").val('');	
		$("#anoInicio").attr('disabled', 'disabled');
		$("#anoFin").attr('disabled', 'disabled');
		$("#fechaInicioAnulado").attr('disabled', 'disabled');
		$("#fechaFinAnulado").attr('disabled', 'disabled');
		$("#fechaInicioEmisionAnulado").attr('disabled', 'disabled');
		$("#fechaFinEmisionAnulado").attr('disabled', 'disabled');				
							
		tipo = 't';			
	}else{//Si es Cheque y Transferencia

		tipo = 'cyt';		

		}
	//document.form.tipo_c.value = tipo;
}

function deshabilitarUbicar() {
		$("#documento").removeAttr('disabled');
		$("#documento").focus();
}

function deshabilitar(valor) {
	//Si es el nro de referencia
	if (valor=='1')  {
		$("#nroReferencia").removeAttr('disabled');
		$("#nroReferencia").focus();		
		$("#anoInicio").val('-1');
		$("#anoFin").val('-1');
		$("#fechaInicioAnulado").val('');
		$("#fechaFinAnulado").val('');
		$("#fechaInicioEmisionAnulado").val('');
		$("#fechaFinEmisionAnulado").val('');	
		$("#anoInicio").attr('disabled', 'disabled');
		$("#anoFin").attr('disabled', 'disabled');
		$("#fechaInicioAnulado").attr('disabled', 'disabled');
		$("#fechaFinAnulado").attr('disabled', 'disabled');
		$("#fechaInicioEmisionAnulado").attr('disabled', 'disabled');
		$("#fechaFinEmisionAnulado").attr('disabled', 'disabled');		
	}
	
	//Fecha inicio emisión anulado
	if (valor=='2') {
		$("#nroReferencia").val('');		
		$("#fechaInicio").val('');
		$("#fechaFin").val('');		
		$("#fechaInicioAnulado").val('');
		$("#fechaFinAnulado").val('');		
		$("#fechaInicioEmisionAnulado").removeAttr('disabled');
		$("#fechaFinEmisionAnulado").removeAttr('disabled');
		$("#anoInicio").val('-1');
		$("#anoFin").val('-1');
		
	}
	
	//Fechas de anulación
	if (valor=='3') {
		$("#nroReferencia").val('-1');		
		$("#fechaInicio").val('');
		$("#fechaFin").val('');	
		$("#fechaInicioEmisionAnulado").val('');
		$("#fechaFinEmisionAnulado").val('');		
		$("#fechaInicioAnulado").removeAttr('disabled');
		$("#fechaFinAnulado").removeAttr('disabled');	
		$("#anoInicio").val('-1');
		$("#anoFin").val('-1');
	}
	
	//Años de anulación
	if (valor=='4') {
		$("#nroReferencia").val('-1');		
		$("#anoInicio").removeAttr('disabled');
		$("#anoFin").removeAttr('disabled');
		$("#fechaInicio").val('');
		$("#fechaFin").val('');		
		$("#fechaInicioEmisionAnulado").val('');
		$("#fechaFinEmisionAnulado").val('');	
		$("#fechaInicioAnulado").val('');
		$("#fechaFinAnulado").val('');		
	}
}

function anular(idCheque, sopg) {
	if (confirm("\u00BFEst\u00e1 seguro que desea ANULAR el cheque asociado al "+sopg+"?")) {
		//document.location.href = _JS_SAFI_RAIZ+"/documentos/pgch/anularCheque.php?id="+idCheque+"&pgch="+nroCheque;
		document.location.href = SAFI_URL_BASE_PATH+"/acciones/reportes/tesoreria/reportesTesoreria.php?accion=AnularCheque&idCheque="+idCheque+"&sopg="+sopg;
	}
}

function reimprimir(idCheque, sopg) {
	if (confirm("\u00BFEst\u00e1 seguro que desea REIMPRIMIR el cheque asociado al "+sopg+"?")) {
		window.location = SAFI_URL_BASE_PATH+"/acciones/reportes/tesoreria/reportesTesoreria.php?accion=reimprimirCheque&idCheque="+idCheque+"&sopg="+sopg;
		//document.location.href = SAFI_URL_BASE_PATH+"/acciones/reportes/tesoreria/reportesTesoreria.php?accion=AnularCheque&idCheque="+idCheque+"&sopg="+sopg;
	}
}

function entregar(id_cheque_p, nro_cheque_p) {
		document.location.href = SAFI_URL_BASE_PATH+"/documentos/pgch/entregarCheque.php?id="+id_cheque_p+"&pgch="+nro_cheque_p;	
}

function imprimir(codigo) {
	//var ruta;	
	if (confirm('Prepare el cheque correspondiente en la impresora')) {
		document.location.href = SAFI_URL_BASE_PATH+"/acciones/reportes/tesoreria/reportesTesoreria.php?accion=ImprimirCheque&sopg="+codigo;		
		//ruta = _JS_SAFI_RAIZ+"/acciones/reportes/tesoreria/reportesTesoreria.php?accion=ImprimirCheque&sopg="+codigo;

		//$(document.location).attr('href',ruta);
		//$(location).href(ruta);
		//$jq(window).attr("location",ruta);		
	}
}

function anularCheque() {
	var mensaje = "";
	if (document.form.motivo.value.substring(0,3) == 'AT-') mensaje = "\u00BFEst\u00e1 seguro que desea anular el cheque junto con sus movimientos presupuestarios y contables?";
	else mensaje = "\u00BFEst\u00e1 seguro que desea anular el cheque y emitir otro con la misma informaci\u00f3n de beneficiario y monto?";
	if (confirm(mensaje)) { 
		var otro = 1;
		if (document.form.motivo.value.substring(0,3)=='AT-') otro = 0;
			document.form.action = SAFI_URL_BASE_PATH+"/acciones/reportes/tesoreria/reportesTesoreria.php?accion=AnularChequeAccion&idCheque="+$("#idCheque")+"&sopg="+$("#idSopg").val()+"&otro="+otro+"&motivo="+$("#motivo").val()+"&observaciones="+$("#observacionesAnulacion").val();
			document.form.submit();			
			//document.form.action= "anularChequeAccion.php?pg=<?php echo $idPgch; ?>&otro="+otro+"&motivo="+document.form.motivo.value;

	}
	else return;
}	

function ubicarDocumento() {
	if ((document.form.tipo[0].checked==false) && (document.form.tipo[1].checked==false) && (document.form.tipo[2].checked==false)  && (document.form.tipo[3].checked==false) && (document.form.tipo[4].checked==false))	{
		alert("Debe indicar el tipo de documento para la b\u00fasqueda");
		return;
	}
	else if (document.form.documento.value == "" && document.form.fechaInicio.value == "" && document.form.fechaFin.value == "") {
			alert("Debe indicar el n\u00famero del documento o seleccionar un rango de fechas");
			return;
	}	
	else resultado = 1;
	if (resultado == 1) {
		 document.form.action="reportesTesoreria.php?accion=UbicarDocumentoAccion";
		 if (document.form.tipo[0].checked==true) tipo="sopg-";
		 if (document.form.tipo[1].checked==true) tipo="pgch-";
		 if (document.form.tipo[2].checked==true) tipo="tran-";
		 if (document.form.tipo[3].checked==true) tipo="cheq";
		 if (document.form.tipo[4].checked==true) tipo="ntran";
		 document.form.submit();
	 }		
}	

function operacionesEmitidas(op) { 
	if ($("#fechaInicio").val().length < 4 || $("#fechaFin").val().length < 4) {
		 alert("Debe seleccionar un rango de fechas");
		 return false;
	}/*else if ($("#cuentaBancaria").val()=='-1') {
		 alert("Debe seleccionar una cuenta bancaria");//
		 return false;
	}*/else if(op==1){
		$("#form").attr('action','reportesTesoreria.php?accion=operacionesEmitidasAccion');
		$("#form").submit();		
	}else{
		$("#form").attr('action','reportesTesoreria.php?accion=operacionesEmitidasPDFAccion');
		$("#form").submit();	
	}
}

function relacionContabilidad(op) { 
	var valor = false;
	$("#tipoOpcion").val($("#opcion").val());	

	//alert ($('input[name=\'opcion[0]\']').val());
	if (op=='cheque') {
		$("#tipoBusqueda").val('cheque');
		$("#referencia").val('');
		$("#numeroActa").val('');
		$("#form").attr('action','reportesTesoreria.php?accion=relacionContabilidadAccion');
		if (document.form.opcion[0].checked == false && document.form.opcion[1].checked == false && document.form.opcion[2].checked == false) {
			alert("Debe seleccionar una opci\u00f3n");
		}
		else {		
			valor = true;
		}
	}	
	else if (op=='transferencia') {
		$("#referencia").val('');
		$("#numeroActa").val('');
		
		$("#tipoBusqueda").val('transferencia');	
		$("#form").attr('action','reportesTesoreria.php?accion=relacionContabilidadAccion');
		if (document.form.opcion[0].checked == false && document.form.opcion[1].checked == false && document.form.opcion[2].checked == false) {
			alert("Debe seleccionar una opci\u00f3n");
		}
		else {		
			valor = true;
		}		
	}	
	else if (op=='buscarActa') {
		$("#tipoBusqueda").val('buscarActa');	
		$("#referencia").val('');	
		$("#form").attr('action','reportesTesoreria.php?accion=relacionContabilidadAccion');
		if ($("#numeroActa").val().length < 1) {
			alert("Debe introducir un n\u00famero de acta");
		}
		else {
			valor = true;
		}
	}	
	else if (op=='imprimirActa') {
		$("#tipoBusqueda").val('imprimirActa');
		$("#referencia").val('');	
		$("#form").attr('action','reportesTesoreria.php?accion=relacionContabilidadAccion');
		if ($("#numeroActa").val().length < 1) {
			alert("Debe introducir un n\u00famero de acta a ser generada");
		}
		else {		
			valor = true;
		}
	}	
	else if (op=='buscarReferencia') {
		$("#tipoBusqueda").val('buscarReferencia');	
		$("#numeroActa").val('');	
		$("#form").attr('action','reportesTesoreria.php?accion=relacionContabilidadAccion');
		if ($("#referencia").val().length < 1) {
			alert("Debe introducir un n\u00famero de referencia");
		}
		else {		
			valor = true;
		}
	}	
	
	else if (op=='buscarSopg') {
		$("#tipoBusqueda").val('buscarSopg');	
		$("#numeroActa").val('');	
		$("#form").attr('action','reportesTesoreria.php?accion=relacionContabilidadAccion');
		if ($("#sopg").val().length < 1) {
			alert("Debe introducir un n\u00famero de sopg");
		}
		else {		
			valor = true;
		}
	}	
	if (valor) $("#form").submit();
}

function validarSeleccion(tipo) {
	$("#form").attr('action','reportesTesoreria.php?accion=relacionContabilidadSeleccionAccion');
	$("#form").submit();
	
}

function ordenar(valor) {
	if(valor.checked == true) {
		orden = document.getElementById('orden').value;
		if (orden.length<3) orden = "ch.id_cheque="+valor.value+ " desc";
		else orden = document.getElementById('orden').value+","+"ch.id_cheque="+valor.value+ " desc";
		document.getElementById('orden').value = orden;
	}
	else {
		//reemplazar orden
		orden = document.getElementById('orden').value;
		orden = orden.replace("ch.id_cheque="+valor.value+ " desc,",'');
		orden = orden.replace("ch.id_cheque="+valor.value+ " desc",'');
		orden = orden.replace(',,',',');
		if (orden.indexOf(',')==0) orden = orden.replace(',','');
			document.getElementById('orden').value = orden;
		}
}

function saldosCorrectos(op) { 
	if ($("#fecha").val().length < 4) {
		 alert("Debe seleccionar una fecha");
		 return false;
	}else if ($("#cuentaBancaria").val()=='-1') {
		 alert("Debe seleccionar una cuenta bancaria");
		 return false;
	}else if(op==1){
		$("#form").attr('action','reportesTesoreria.php?accion=saldosCorrectosAccion');
		$("#form").submit();		
	}else{
		$("#form").attr('action','reportesTesoreria.php?accion=saldosCorrectosPDFAccion');
		$("#form").submit();	
	}
}	
	
	
	
	

