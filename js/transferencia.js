function buscarAccion() {
	if ($("#numeroTransferencia").val().length < 7) { 
		alert("Debe indicar el n\u00famero de transferencia");
		return;
	}
	else {
		$("#form").attr('action','transferencia.php?accion=ModificarBuscar');
		$("#form").submit();
	}
}

function modificarAccion() {
	
	if ($("#nuevaReferencia").val().length < 1 && $("#nuevaFecha").val().length < 2 && $("#nuevaCuentaBancaria").val()==-1) { 
		alert("Debe realizar cambio en la fecha, referencia o cuenta bancaria");
		return;
	}
	else if ($("#nuevaFecha").val().length > 2){
		mesActual = parseInt($("#fechaVieja").val().substring(3, 5));
		anoActual = parseInt($("#fechaVieja").val().substring(6, 10));
		nuevaFechaMes = parseInt($("#nuevaFecha").val().substring(3, 5));
		nuevaFechaAno = parseInt($("#nuevaFecha").val().substring(6, 10));
		nuevaFechaDia = parseInt($("#nuevaFecha").val().substring(0, 2));
		nuevaFecha = nuevaFechaAno+"/"+nuevaFechaMes+"/"+nuevaFechaDia;
		if (mesActual == 12) fecha = anoActual+"/12/31";
		else if (mesActual <9) fecha = anoActual+"/"+(mesActual+1)+"/05";
		else fecha = anoActual+"/"+(mesActual+1)+"/05";
		fechaParametro=new Date(nuevaFecha);
		fechaComparacion=new Date(fecha);
		//validacion fecha
		if(fechaParametro<fechaComparacion) {
			$("#form").attr('action','transferencia.php?accion=ModificarAccion');
			$("#form").submit();
		}
		else alert ("Error en la fecha seleccionada. Supera los cinco d\u00EDas del mes siguiente de la fecha de elaboraci\u00F3n de la transferencia. Se debe rehacer la orden de pago.");
	}
	else alert ("Error en par\u00E1metros");
}

