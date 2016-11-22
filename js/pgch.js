function buscarAccion() {
	if ($("#sopgId").val().length < 7) { 
		alert("Debe indicar el n\u00famero de solicitud de pago");
		return;
	}
	else {
		$("#form").attr('action','pgch.php?accion=BuscarReiniciar');
		$("#form").submit();
	}
}

function reiniciarPago() {
	if ($("#idPgch").val().length < 7 && $("#idSopg").val().length < 7) { 
		alert("Debe especificar el pago a reiniciar");
		return;
	}
	else {
		$("#form").attr('action','pgch.php?accion=ReiniciarPago');
		$("#form").submit();
	}
}

function buscarBeneficiario() {
	if ($("#beneficiario").val().length < 7) { 
		alert("Debe indicar un beneficiario");
		return;
	}
	else {
		$("#form").attr('action','pgch.php?accion=BuscarBeneficiario');
		$("#form").submit();
	}
}

function actualizarBeneficiario() {
		$("#form").attr('action','pgch.php?accion=actualizarBeneficiario');
		$("#form").submit();
}
