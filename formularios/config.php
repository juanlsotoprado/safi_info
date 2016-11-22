<?php

define('FORM_NUEVA_RENDICION_VIATICO_NACIONAL', 4);
define('FORM_REPORTE_1_VIATICO_NACIONAL', 5);
define('FORM_BUSCAR_RENDICON_VIATICO_NACIONAL', 6);
define('FORM_BANDEJA_RENDICON_VIATICO_NACIONAL', 7);
define('FORM_NUEVO_AVANCE', 8);
define('FORM_BUSCAR_AVANCE', 9);
define('FORM_ARCHIVOS_PAGOS_AVANCE', 10);
define('FORM_ARCHIVOS_PAGOS_VIATICO_NACIONAL', 11);
define('FORM_NUEVA_RENDICION_AVANCE', 12);
define('FORM_BUSCAR_RENDICION_AVANCE', 13);
define('FORM_REPORTE_RESPONSABLES_AVANCE', 14);
define('FORM_BUSCAR_TESORERIA', 15);
define('FORM_UBICAR_DOCUMENTO_TESORERIA', 16);
define('FORM_CAJA_CHICA_NUEVA_APERTURA', 17);
define('FORM_CUENTA_BANCARIA_PAGADO_CONTABILIDAD', 18);
define('FORM_REPORTE_ACTIVIDAD', 19);
define('FORM_AUXILIAR', 20);
define('FORM_BUSCAR_CODI',21);
define('FORM_OPERACIONES_EMITIDAS_TESORERIA',22);
define('FORM_RELACION_CONTABILIDAD_TESORERIA',23);
define('FORM_SALDOS_CORRECTOS_TESORERIA',24);
define('FORM_TRANSFERENCIA',25);
define('FORM_FORMULARIO_DESINCORPORACION', 26);
define('FORM_REPORTE_RESPONSABLES_VIATICO', 27);

$GLOBALS['Safi']['__Forms']['__List'] = array();

$GLOBALS['Safi']['__Forms']['__Config']['viaticoNacional'] = array(
	'File' => 'vina/viaticonacional.php',
	'ClassName' => 'ViaticoNacionalForm',
	'GlobalName' => 'ClassViaticoNacionalForm'
);
$GLOBALS['Safi']['__Forms']['__Config']['buscarViaticoNacional'] = array(
	'File' => 'vina/buscarviaticonacional.php',
	'ClassName' => 'BuscarViaticoNacionalForm',
	'GlobalName' => 'ClassBuscarViaticoNacionalForm'
);
$GLOBALS['Safi']['__Forms']['__Config']['bandejaViaticoNacional'] = array(
	'File' => 'vina/bandeja.php',
	'ClassName' => 'BandejaViaticoNacionalForm',
	'GlobalName' => 'ClassBandejaViaticoNacionalForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_NUEVA_RENDICION_VIATICO_NACIONAL] = array(
	'File' => 'vina/nuevaRendicion.php',
	'ClassName' => 'NuevaRendicionViaticoNacionalForm',
	'GlobalName' => 'ClassNuevaRendicionViaticoNacionalForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_REPORTE_1_VIATICO_NACIONAL] = array(
	'File' => 'vina/reporte1ViaticoNacional.php',
	'ClassName' => 'Reporte1ViaticoNacionalForm',
	'GlobalName' => 'ClassReporte1ViaticoNacionalForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_REPORTE_RESPONSABLES_VIATICO] = array(
	'File' => 'vina/reporteResponsablesViatico.php',
	'ClassName' => 'ReporteResponsablesViaticoForm',
	'GlobalName' => 'ClassReporteResponsablesViaticoForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_BUSCAR_RENDICON_VIATICO_NACIONAL] = array(
	'File' => 'vina/buscarRendicionViaticoNacional.php',
	'ClassName' => 'BuscarRendicionViaticoNacionalForm',
	'GlobalName' => 'ClassBuscarRendicionViaticoNacionalForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_BANDEJA_RENDICON_VIATICO_NACIONAL] = array(
	'File' => 'vina/bandejaRendicionViaticoNacional.php',
	'ClassName' => 'BandejaRendicionViaticoNacionalForm',
	'GlobalName' => 'ClassBandejaRendicionViaticoNacionalForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_NUEVO_AVANCE] = array(
	'File' => 'avan/nuevoAvance.php',
	'ClassName' => 'NuevoAvanceForm',
	'GlobalName' => 'ClassNuevoAvanceForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_BUSCAR_AVANCE] = array(
	'File' => 'avan/buscarAvance.php',
	'ClassName' => 'BuscarAvanceForm',
	'GlobalName' => 'ClassBuscarAvanceForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_ARCHIVOS_PAGOS_AVANCE] = array(
	'File' => 'avan/archivosPagosAvance.php',
	'ClassName' => 'ArchivosPagosAvanceForm',
	'GlobalName' => 'ClassArchivosPagosAvanceForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_ARCHIVOS_PAGOS_VIATICO_NACIONAL] = array(
	'File' => 'vina/archivosPagosViaticoNacional.php',
	'ClassName' => 'ArchivosPagosViaticoNacionalForm',
	'GlobalName' => 'ClassArchivosPagosViaticoNacionalForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_NUEVA_RENDICION_AVANCE] = array(
	'File' => 'avan/nuevaRendicion.php',
	'ClassName' => 'NuevaRendicionAvanceForm',
	'GlobalName' => 'ClassNuevaRendicionAvanceForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_BUSCAR_RENDICION_AVANCE] = array(
	'File' => 'avan/buscarRendicion.php',
	'ClassName' => 'BuscarRendicionAvanceForm',
	'GlobalName' => 'ClassBuscarRendicionAvanceForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_REPORTE_RESPONSABLES_AVANCE] = array(
	'File' => 'avan/reporteResponsablesAvance.php',
	'ClassName' => 'ReporteResponsablesAvanceForm',
	'GlobalName' => 'ClassReporteResponsablesAvanceForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_BUSCAR_TESORERIA] = array(
	'File' => 'reportes/tesoreria/buscar.php',
	'ClassName' => 'BuscarTesoreriaForm',
	'GlobalName' => 'ClassBuscarTesoreriaForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_UBICAR_DOCUMENTO_TESORERIA] = array(
	'File' => 'reportes/tesoreria/ubicarDocumento.php',
	'ClassName' => 'UbicarDocumentoTesoreriaForm',
	'GlobalName' => 'ClassUbicarDocumentoTesoreriaForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_CAJA_CHICA_NUEVA_APERTURA] = array(
	'File' => 'cajaChica/cajaChicaNuevaApertura.php',
	'ClassName' => 'CajaChicaNuevaAperturaForm',
	'GlobalName' => 'ClassCajaChicaNuevaAperturaForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_CUENTA_BANCARIA_PAGADO_CONTABILIDAD] = array(
	'File' => 'reportes/contabilidad/cuentaBancariaPagado.php',
	'ClassName' => 'CuentaBancariaPagadoContabilidadForm',
	'GlobalName' => 'CuentaBancariaPagadoContabilidadForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_REPORTE_ACTIVIDAD] = array(
		'File' => 'reportes/presupuesto/reporteActividad.php',
		'ClassName' => 'ReporteActividadForm',
		'GlobalName' => 'ReporteActividadForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_OPERACIONES_EMITIDAS_TESORERIA] = array(
	'File' => 'reportes/tesoreria/operacionesEmitidas.php',
	'ClassName' => 'OperacionesEmitidasTesoreriaForm',
	'GlobalName' => 'ClassOperacionesEmitidasTesoreriaForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_RELACION_CONTABILIDAD_TESORERIA] = array(
		'File' => 'reportes/tesoreria/relacionContabilidad.php',
		'ClassName' => 'RelacionContabilidadTesoreriaForm',
		'GlobalName' => 'ClassRelacionContabilidadTesoreriaForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_SALDOS_CORRECTOS_TESORERIA] = array(
		'File' => 'reportes/tesoreria/saldosCorrectos.php',
		'ClassName' => 'SaldosCorrectosTesoreriaForm',
		'GlobalName' => 'ClassSaldosCorrectosTesoreriaForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_TRANSFERENCIA] = array(
		'File' => 'tran/modificar.php',
		'ClassName' => 'ModificarTransferenciaForm',
		'GlobalName' => 'ClassModificarTransferenciaForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_AUXILIAR] = array(
		'File' => 'reportes/contabilidad/auxiliar.php',
		'ClassName' => 'AuxiliarForm',
		'GlobalName' => 'ClassAuxiliarForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_BUSCAR_CODI] = array(
		'File' => 'codi/buscar.php',
		'ClassName' => 'BuscarCodiForm',
		'GlobalName' => 'ClassBuscarCodiForm'
);
$GLOBALS['Safi']['__Forms']['__Config'][FORM_FORMULARIO_DESINCORPORACION] = array(
		'File' => 'bienes/desincorporacion/formularioDesincorporacion.php',
		'ClassName' => 'FormularioDesincorporacionForm',
		'GlobalName' => 'ClassFormularioDesincorporacionForm'
);
?>