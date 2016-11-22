<?php 
	$form = FormManager::GetForm('viaticoNacional');
	$formUTF8 = clone $form;
	$formUTF8->UTF8Encode();
?>
<html>
	<head>
		<title>.:SAFI:. Ingresar Vi&aacute;tico Nacional</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		<link href="../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />
		<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
		<style>
			.ui-autocomplete {
				max-height: 110px;
				overflow-y: auto;
				/* prevent horizontal scrollbar */
				overflow-x: hidden;
				/* add padding to account for vertical scrollbar */
				padding-right: 20px;
			}
			/* IE 6 doesn't support max-height
			 * we use height instead, but this forces the menu to always be this tall
			 */
			* html .ui-autocomplete {
				height: 120px;
			}
		</style>
		
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript" src="<? echo SAFI_URL_JAVASCRIPT_PATH?>/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript" src="../../js/lib/actb.js"></script>
		<script type="text/javascript" src="../../js/constantes.js"></script>
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<?php
		
			$jsMontoAsignaciones = array();
			foreach($GLOBALS['SafiRequestVars']['asignaciones'] as $asignacion){
				$jsMontoAsignaciones[] = '"' . $asignacion->GetCodigo() . '": parseFloat(' . $asignacion->GetMontoFijo() . ')';
			}
		
			echo '
				<script type="text/javascript">
					var formViatico = ' . $formUTF8->ToJson() . ';
					var montoAsignaciones = {'. implode(',', $jsMontoAsignaciones) .'};
					
					// Códigos de asignaciones de viaticos fijas
					var COD_ALIMENTACION = "'. EntidadAsignacionViatico::COD_ALIMENTACION .'";
					var COD_HOSPEDAJE = "'. EntidadAsignacionViatico::COD_HOSPEDAJE .'";
					var COD_TRANSPORTE_INTERURBANO = "'. EntidadAsignacionViatico::COD_TRANSPORTE_INTERURBANO .'";
					var COD_TASA_AEROPORTUARIA = "'. EntidadAsignacionViatico::COD_TASA_AEROPORTUARIA .'";
					var COD_SERVICIO_COMUNICACIONES = "'. EntidadAsignacionViatico::COD_SERVICIO_COMUNICACIONES .'";
					var COD_ASIGNACION_TRANSPORTE = "'. EntidadAsignacionViatico::COD_ASIGNACION_TRANSPORTE .'";
					var COD_AEROPUERTO_RESIDENCIA = "'. EntidadAsignacionViatico::COD_AEROPUERTO_RESIDENCIA.'";
					var COD_RESIDENCIA_AEROPUERTO = "'. EntidadAsignacionViatico::COD_RESIDENCIA_AEROPUERTO.'";
					
					// Tipos de tipo de transporte
					var TIPO_AEREO = "'. EntidadTipoTransporte::TIPO_AEREO .'";
				</script>
			'
		?>
		<script type="text/javascript">
			var rutaIdSeq = 0;
			
			g_Calendar.setDateFormat('dd/mm/yyyy');

			var labelRespEmpleados = new Array();
			var labelRespBeneficiarios = new Array();
			var estados = new Object();
			var tipoTransportes = new Object();
			
			// obtener todos los empleados activos
			$.ajax({
				async: false,
				type: "POST",
				dataType: "json",
				url: "../empleado.php",
				data: {
					accion: "GetEmpleadosActivos",
					tipoRespuesta: "json"
				},
				success: function(json){
					var index = 0;
					
					$.each(json.listaempleado, function(cedula, objEmpleado){
						var label = cedula + ' : ' + objEmpleado.nombres.toUpperCase() +
									' ' + objEmpleado.apellidos.toUpperCase();
						labelRespEmpleados[index++] = label; 
					});
				}
			});

			// obtener todos los beneficiarios de viaticos activos
			$.ajax({
				async: false,
				type: "POST",
				dataType: "json",
				url: "../beneficiarioviatico.php",
				data: {
					accion: "GetBeneficiarioViaticosActivos",
					tipoRespuesta: "json"
				},
				success: function(json){
					var index = 0;
					$.each(json.listabeneficiarioviatico, function(cedula, objBeneficiario){
						var label = cedula + ' : ' + objBeneficiario.nombres.toUpperCase() +
									' ' + objBeneficiario.apellidos.toUpperCase();
						labelRespBeneficiarios[index++] = label; 
					});
				}
			});

			// Obtener las lista de todos los estados
			$.ajax({
				async: false,
				type: "POST",
				dataType: "xml",
				url: "../estado.php",
				data: {
					accion: "GetAllEstados"
				},
				success: function(xml){
					$(xml).find('estado').each(function(){
						var tagEstado = $(this);
						var id = tagEstado.attr("id");
						var nombre = $.trim(tagEstado.find('nombre').text());
						estados[id] = {id: id, nombre: nombre};
					});
				}
			});
			
			// Obtener los tipo transportes
			$.ajax({
				async: false,
				type: "POST",
				dataType: "json",
				url: "../tipotransporte.php",
				data: {
					accion: "GetTipoTransportesActivos",
					tipoRespuesta: "json"
				},
				success: function(json){
					$.each(json.listatipotransporte, function(idTipoTransporte, objTipoTransporte){
						tipoTransportes[idTipoTransporte] = {
							id: idTipoTransporte,
							tipo: objTipoTransporte.tipo,
							nombre: objTipoTransporte.nombre
						};
					});
				}
			});
			
			function onLoad(){
				
				var objInputInfocentros = $("#inputSelectInfocentros");
				var idUlInfocentros = "ulListaInfocentro";
				var sendInputNameInfocentros = "infocentros[]";

				// Cargar los datos de los proyectos o acciones centralizadas
				cargarProyectoAccionCentralizada({tipo: formViatico.tipoProyectoAccionCentralizada});

				// Cargar los datos de las rutas
				var countRutas = 0;
				$.each(formViatico.rutas, function(indexRuta, objRuta){
					agregarRuta({ruta: objRuta});
					countRutas++;
				});
				
				// Cargar los datos de los responsable
				cargarResponsables({
					tipo: formViatico.tipoResponsable,
					divInputResponsable: 'divInputResponsable',
					inputResponsable: 'inputResponsable',
					btnAdd: 'btnAdd',
					focus: false
				});
				
				//mostrarResponsables2({tipo: 'empleado', inputResponsables: 'inputResponsables2', focus: false});
				if(formViatico.responsable && formViatico.responsable != null && 
						formViatico.responsable.cedula && formViatico.responsable.cedula != null &&
						formViatico.responsable.cedula != '')
				{

					if(formViatico.responsable.tipoResponsable == 'empleado'){
						// Crear el contenedor de la cuenta bancaria
						crearContenedorCuantaBancaria();

						$.ajax({
							type: "POST",
							dataType: "xml",
							url: "../empleado.php",
							data: {
								accion: "GetEmpleadoActivoByCedula",
								tipoRespuesta: "xml",
								cedula: formViatico.responsable.cedula
							},
							success: function(xml){
								xml = $(xml);
								var xmlListResponsable = xml.find('empleado');
								var cedula = xmlListResponsable.find("cedula").text().trim();
								var bancoNomina = xmlListResponsable.find("banconomina").text().trim();
								var tipoCuentaNomina = xmlListResponsable.find("tipocuentanomina").text().trim();
								var cuentaNomina = xmlListResponsable.find("cuentanomina").text().trim();

								if (cedula != null && cedula != '')
								{
									var cuentaBancaria = new Object();
									cuentaBancaria.origen = "N"+oACUTE+"mina"; 
									cuentaBancaria.numeroCuenta = cuentaNomina;
									cuentaBancaria.banco = bancoNomina;
									cuentaBancaria.tipoCuenta = tipoCuentaNomina;

									crearCuentaBancaria(cuentaBancaria);
								}
							}
						});
						
						crearResponsable({
							tipo: formViatico.responsable.tipoResponsable,
							cedula: formViatico.responsable.cedula,
							nombres: formViatico.responsable.nombres,
							apellidos: formViatico.responsable.apellidos
						});
					} else if(formViatico.responsable.tipoResponsable == 'beneficiario'){
						// Crear el contenedor de la cuenta bancaria
						crearContenedorCuantaBancaria();

						$.ajax({
							type: "POST",
							dataType: "xml",
							url: "../beneficiarioviatico.php",
							data: {
								accion: "GetBeneficiarioViaticoActivoByCedula",
								tipoRespuesta: "xml",
								cedula: formViatico.responsable.cedula
							},
							success: function(xml){
								xml = $(xml);
								var xmlListResponsable = xml.find('beneficiarioviatico');
								var cedula = xmlListResponsable.find("cedula").text().trim();
								var bancoNomina = xmlListResponsable.find("banconomina").text().trim();
								var tipoCuentaNomina = xmlListResponsable.find("tipocuentanomina").text().trim();
								var cuentaNomina = xmlListResponsable.find("cuentanomina").text().trim();

								if (cedula != null && cedula != '')
								{
									var cuentaBancaria = new Object();
									cuentaBancaria.origen = "N"+oACUTE+"mina"; 
									cuentaBancaria.numeroCuenta = cuentaNomina;
									cuentaBancaria.banco = bancoNomina;
									cuentaBancaria.tipoCuenta = tipoCuentaNomina;

									crearCuentaBancaria(cuentaBancaria);
								}
							}
						});
						
						crearResponsable({
							tipo: formViatico.responsable.tipoResponsable,
							cedula: formViatico.responsable.cedula,
							nombres: formViatico.responsable.nombres,
							apellidos: formViatico.responsable.apellidos,
							tipoEmpleado: formViatico.responsable.tipoEmpleado
						});
					}

					if(formViatico.responsable.tipoResponsable == 'empleado' || formViatico.responsable.tipoResponsable == 'beneficiario'){
						// Obtener el último número de cuenta bancaria asociado a un viático para este responsable.
						$.ajax({
							type: "POST",
							dataType: "json",
							url: "../responsableViatico.php",
							data: {
								accion: "GetUltimaCuentaBancaria",
								cedula: formViatico.responsable.cedula
							},
							success: function(json){
								$.each(json.listaResponsables, function(idResponsable, objResponsable)
								{
									var cuentaBancaria = new Object();
									cuentaBancaria.origen = "Del "+uACUTE+"ltimo vi"+aACUTE+"tico"; 
									cuentaBancaria.numeroCuenta = objResponsable.numeroCuenta;
									cuentaBancaria.banco = objResponsable.banco;
									cuentaBancaria.tipoCuenta = objResponsable.tipoCuenta;
	
									crearCuentaBancaria(cuentaBancaria);
								});
							}
						});
					}
				}
				
				// Configurar el autocomplete de infocentros
				objInputInfocentros.autocomplete({
					source: function(request, response){
						var seleccionados = new Array();
						$('#ulListaInfocentro input[type="hidden"][name="infocentros\[\]"]').each(function(index, objInput){
							seleccionados[index] = objInput.value;
						});
						$.ajax({
							url: "../infocentro.php",
							dataType: "json",
							data: {
								accion: "Search",
								key: request.term,
								seleccionados: seleccionados
							},
							success: function(json){
								var index = 0;
								var items = new Array();

								$.each(json.listainfocentro, function(idInfocentro, objInfocentro){

									var label = objInfocentro.nombre;
									if((parroquia = objInfocentro.parroquia) != null){
										if((municipio = parroquia.municipio) != null){
											if((estado = municipio.estado)!= null){
												nombreEstado = estado.nombre;
												label += ' - ' + estado.nombre;
											}
										} 
									}
									if(objInfocentro.etapa != null && objInfocentro.etapa != ''){
										label += ' - ' + objInfocentro.etapa; 
									}
									
									items[index++] = {
											id: idInfocentro,
											label: label,
											value: objInfocentro.nombre,
											nombreParroquia: nombreEstado
									};
								});
								response(items);
							}
						});
					},
					minLength: 1,
					select: function(event, ui)
					{
						llenarListaDiamante({
							id: ui.item.id,
							label: ui.item.label,
							idUl: idUlInfocentros,
							sendInputName: sendInputNameInfocentros
						});
						
						objInputInfocentros.val('');
						return false;
					}
				});

				// Llenar la lista de infocentros
				$.each(formViatico.infocentros, function(idInfocentro, objInfocentro){
					
					var label = objInfocentro.nombre;
					if((parroquia = objInfocentro.parroquia) != null){
						if((municipio = parroquia.municipio) != null){
							if((estado = municipio.estado)!= null){
								label += ' - ' + estado.nombre;
							}
						} 
					}
					if(objInfocentro.etapa != null && objInfocentro.etapa != ''){
						label += ' - ' + objInfocentro.etapa; 
					}
					
					llenarListaDiamante({
						id: idInfocentro,
						label: label,
						idUl: idUlInfocentros,
						sendInputName: sendInputNameInfocentros
					});
				});
				
			}

			function llenarListaDiamante(params)
			{
				var objUl = $('#' + params['idUl']);
				var sendInputName = params['sendInputName'];
				var idItem = params['id'];
				var labelItem = params['label'];

				var objLi = document.createElement("li");

				// crear el tag input hidden con el id del item, que será enviado al servidor
				var objInputHidden = document.createElement("input");
				objInputHidden.setAttribute('type', 'hidden');
				objInputHidden.setAttribute('name', sendInputName);
				objInputHidden.setAttribute('value', idItem);
				objLi.appendChild(objInputHidden);

				// crear el div con el label del item
				var objDiv = document.createElement('div');
				objDiv.setAttribute('class', 'col1');
				objDiv.appendChild(document.createTextNode(labelItem));
				objLi.appendChild(objDiv);

				// crear el div con el link eliminar
				var objA = document.createElement('a');
				objA.setAttribute('href', 'javascript:void(0);');
				objA.appendChild(document.createTextNode('Eliminar'));

				var objDiv = document.createElement('div');
				objDiv.setAttribute('class', 'col2');
				objDiv.appendChild(objA);
				objLi.appendChild(objDiv);

				// asociar el evento click(eliminar)
				objA = $(objA);
				objA.unbind('click');
				objA.bind('click', function(){
					$(objLi).remove();
				});
				
				// div con las clase clear
				var objDiv = document.createElement('div');
				objDiv.setAttribute('class', 'clear');
				objLi.appendChild(objDiv);
				
				// agregar el tag li al tag ul
				objUl.append(objLi);
			}

			function mostrarResponsables2(params){
				var tipo = params['tipo'];
				var objInputResponsables = $('#' + params['inputResponsables']);
				var focus = params['focus'];

				if(tipo == 'empleado'){
					objInputResponsables.autocomplete({
						source: function(request, response){
							$.ajax({
								url: "../empleado.php",
								dataType: "json",
								data: {
									accion: "Search",
									tipoRespuesta: 'json',
									key: request.term
								},
								success: function(json){
									var index = 0;
									var items = new Array();
		
									$.each(json.listaempleado, function(cedula, objEmpleado){
										var value = objEmpleado.cedula + ' : ' + objEmpleado.nombres.toUpperCase() + ' ' + objEmpleado.apellidos.toUpperCase();
										items[index++] = {
												id: cedula,
												label: value,
												value: value
										};
									});
									response(items);
								}
							});
						},
						minLength: 1,
						select: function(event, ui) {
							objInputResponsables.val('');
							return false;
						}
					});
				} else if (tipo == 'beneficiario') {

				}
				
			}

			function cargarResponsables(params){

				var tipo = params['tipo'];
				var divInputResponsable = params['divInputResponsable'];
				var inputResponsable = params['inputResponsable'];
				var btnAdd = params['btnAdd'];
				var focus = params['focus'];
				
				if(tipo == 'empleado'){
					mostrarResponsables(tipo, divInputResponsable, inputResponsable, btnAdd, focus);
					$('#inputTipoEmpleado').attr('checked', 'checked');
				} else if(tipo == 'beneficiario'){
					mostrarResponsables(tipo, divInputResponsable, inputResponsable, btnAdd, focus);
					$('#inputTipoBeneficiario').attr('checked', 'checked');
				}
			}
			
			function mostrarResponsables(tipo, divInputResponsable, inputResponsable, btnAdd, focus)
			{
				var objInput = $("#" + inputResponsable);
				var objDiv = $("#" + divInputResponsable);
				var objButton = $("#" + btnAdd);

				objInput.remove();

				input = document.createElement('input');
				input.setAttribute("autocomplete","off");
				input.setAttribute("size","70");
				input.setAttribute("type","text");
				input.setAttribute("id",inputResponsable);
				input.setAttribute("name",inputResponsable);
				input.setAttribute("value","");
				input.setAttribute("class","normal");

				objDiv.append(input);
				objInput = $("#" + inputResponsable);
				
				if(tipo == 'empleado'){
					actb(objInput[0], labelRespEmpleados);
				} else if (tipo == 'beneficiario') {
					actb(objInput[0], labelRespBeneficiarios);
				}
				if(focus){
					objInput.focus();
				}

				objButton.unbind("click");
				objButton.bind("click", {tipo: tipo, inputResponsable: inputResponsable}, function(event){
					agregarResponsables(event.data.tipo, event.data.inputResponsable);
				});
			}

			function agregarResponsables(tipo, inputResponsable)
			{
				var objInput = $("#" + inputResponsable);
				var objInputNueroCuenta = $('#numeroCuenta');
				var objSelectTipoCuenta = $('#tipoCuenta');
				var objBanco = $('#banco');
				var objTbodyCuentaBancaria = $("#tbodyDatosCuentaBancaria");

				var value = objInput.val();
				
				objInputNueroCuenta.val('');
				objSelectTipoCuenta.prop('selectedIndex', 0);
				objBanco.val('');

				if(value == ""){
					alert("Introduzca el n"+uACUTE+"mero de c"+eACUTE+"dula o una palabra contenida en el nombre del responsable.");
					objInput.focus();
					return;
				}

				// Crear el contenedor de la cuenta bancaria
				crearContenedorCuantaBancaria();
				
				var tokens = value.split( ":" );
				var cedula = (tokens[0]) ? $.trim(tokens[0]) : "";

				objInput.val("");
				
				if(tipo == 'empleado'){
					$.ajax({
						type: "POST",
						dataType: "xml",
						url: "../empleado.php",
						data: {
							accion: "GetEmpleadoActivoByCedula",
							tipoRespuesta: "xml",
							cedula: cedula
						},
						success: function(xml){
							xml = $(xml);
							var xmlListResponsable = xml.find('empleado');
							var cedula = xmlListResponsable.find("cedula").text().trim();
							var nombres = xmlListResponsable.find("nombres").text().trim();
							var apellidos = xmlListResponsable.find("apellidos").text().trim();
							var bancoNomina = xmlListResponsable.find("banconomina").text().trim();
							var tipoCuentaNomina = xmlListResponsable.find("tipocuentanomina").text().trim();
							var cuentaNomina = xmlListResponsable.find("cuentanomina").text().trim();

							if (cedula != null && cedula != ''){
								crearResponsable({
									tipo: tipo,
									cedula: cedula,
									nombres: nombres,
									apellidos: apellidos
								});

								var cuentaBancaria = new Object();
								cuentaBancaria.origen = "N"+oACUTE+"mina"; 
								cuentaBancaria.numeroCuenta = cuentaNomina;
								cuentaBancaria.banco = bancoNomina;
								cuentaBancaria.tipoCuenta = tipoCuentaNomina;

								crearCuentaBancaria(cuentaBancaria);
							}
						}
					});

				} else if (tipo == 'beneficiario') {
					$.ajax({
						type: "POST",
						dataType: "xml",
						url: "../beneficiarioviatico.php",
						data: {
							accion: "GetBeneficiarioViaticoActivoByCedula",
							tipoRespuesta: "xml",
							cedula: cedula
						},
						success: function(xml){
							xml = $(xml);
							var xmlListResponsable = xml.find('beneficiarioviatico');
							var cedula = xmlListResponsable.find("cedula").text().trim();
							var nombres = xmlListResponsable.find("nombres").text().trim();
							var apellidos = xmlListResponsable.find("apellidos").text().trim();
							var tipoEmpleado = xmlListResponsable.find("tipo").text().trim();
							var bancoNomina = xmlListResponsable.find("banconomina").text().trim();
							var tipoCuentaNomina = xmlListResponsable.find("tipocuentanomina").text().trim();
							var cuentaNomina = xmlListResponsable.find("cuentanomina").text().trim();

							if (cedula != null && cedula != ''){
								crearResponsable({
									tipo: tipo,
									cedula: cedula,
									nombres: nombres,
									apellidos: apellidos,
									tipoEmpleado: tipoEmpleado
								});

								var cuentaBancaria = new Object();
								cuentaBancaria.origen = "N"+oACUTE+"mina"; 
								cuentaBancaria.numeroCuenta = cuentaNomina;
								cuentaBancaria.banco = bancoNomina;
								cuentaBancaria.tipoCuenta = tipoCuentaNomina;

								crearCuentaBancaria(cuentaBancaria);
							}
						}
					});
				}

				// Obtener el último número de cuenta bancaria asociado a un viático para este responsable.
				$.ajax({
					type: "POST",
					dataType: "json",
					url: "../responsableViatico.php",
					data: {
						accion: "GetUltimaCuentaBancaria",
						cedula: cedula
					},
					success: function(json){
						$.each(json.listaResponsables, function(idResponsable, objResponsable)
						{
							var cuentaBancaria = new Object();
							cuentaBancaria.origen = "Del "+uACUTE+"ltimo vi"+aACUTE+"tico"; 
							cuentaBancaria.numeroCuenta = objResponsable.numeroCuenta;
							cuentaBancaria.banco = objResponsable.banco;
							cuentaBancaria.tipoCuenta = objResponsable.tipoCuenta;

							crearCuentaBancaria(cuentaBancaria);
						});
					}
				});
			}

			function crearResponsable(params)
			{
				var tipo = params.tipo || '';
				var cedula = params.cedula || '';
				var nombres = params.nombres || '';
				var apellidos = params.apellidos || '';
				var tipoEmpleado = params.tipoEmpleado || '';

				var tbody = "tbodyInfoResponsable";
				var trHead = "trheadInfoResponsable";
				var objTbody = $("#" + tbody);
				var objTrHead = $("#" + trHead);

				// eliminar todos los responsables que se hayan agragado con anterioridad
				$("#" + tbody + " > tr:not(#" + trHead + ")").remove();

				var tr = document.createElement("tr");
				
				var tdCedula = document.createElement("td");
				tdCedula.setAttribute("align","justify");

				var objInputHidden = document.createElement("input");
				objInputHidden.setAttribute("type", "hidden");
				objInputHidden.setAttribute("name", "responsable");
				objInputHidden.setAttribute("value", cedula);
				tdCedula.appendChild(objInputHidden);

				tdCedula.appendChild(document.createTextNode(cedula));
				
				var tdNombre =  document.createElement("td");
				tdNombre.setAttribute("align","justify");
				tdNombre.appendChild(document.createTextNode(nombres.toUpperCase() + " " + apellidos.toUpperCase()));

				var tdTipo = document.createElement("td");
				tdTipo.setAttribute("align","justify");

				var objInputHidden = document.createElement("input");
				objInputHidden.setAttribute("type", "hidden");
				objInputHidden.setAttribute("name", "tipoResponsable");
				objInputHidden.setAttribute("value", tipo);
				tdTipo.appendChild(objInputHidden);

				if(tipo == 'empleado'){
					tdTipo.appendChild(document.createTextNode('Empleado'));
				} else {
					tdTipo.appendChild(document.createTextNode(tipoEmpleado));
				}

				var objA = document.createElement('a');
				objA.setAttribute('href', 'javascript:void(0);');
				objA.appendChild(document.createTextNode("Eliminar"));
				
				var tdOpcion = document.createElement("td");
				tdOpcion.setAttribute("align","justify");
				tdOpcion.appendChild(objA);

				objA = $(objA);
				objA.unbind('click');
				objA.bind('click', {tbody: tbody, trHead: trHead}, function(event){
					$("#" + event.data.tbody + " > tr:not(#" + event.data.trHead + ")").remove();
					$("#tdDatosCuentaBancaria").empty();
				});
				
				tr.appendChild(tdCedula);
				tr.appendChild(tdNombre);
				tr.appendChild(tdTipo);
				tr.appendChild(tdOpcion);
				objTbody.append(tr);				
			}

			function crearContenedorCuantaBancaria()
			{
				var objTdDatosCuentaBancaria = $("#tdDatosCuentaBancaria");

				objTdDatosCuentaBancaria.empty();

				var objFieldset = document.createElement("fieldset");
				objFieldset.setAttribute("style", "background-color: #e0e0ff; border-color: #aaaaaa; border-style: solid; border-width: 1px;");
				objTdDatosCuentaBancaria.append(objFieldset);

				var objLegend = document.createElement("legend");
				objLegend.appendChild(document.createTextNode("Cuentas bancarias precargadas:"));
				objFieldset.appendChild(objLegend);

				var objTable = document.createElement("table");
				objTable.setAttribute("style", "width: 100%;");
				objFieldset.appendChild(objTable);

				var objThead = document.createElement("thead");
				objTable.appendChild(objThead);

				var tr = document.createElement("tr");
				objThead.appendChild(tr);

				var td = document.createElement("td");
				td.appendChild(document.createTextNode("Origen"));
				tr.appendChild(td);

				var td = document.createElement("td");
				td.appendChild(document.createTextNode("Nro. cuenta"));
				tr.appendChild(td);

				var td = document.createElement("td");
				td.appendChild(document.createTextNode("Tipo cuenta"));
				tr.appendChild(td);

				var td = document.createElement("td");
				td.appendChild(document.createTextNode("Banco"));
				tr.appendChild(td);

				var td = document.createElement("td");
				td.appendChild(document.createTextNode("Opciones"));
				tr.appendChild(td);

				var objTbody = document.createElement("tbody");
				objTbody.setAttribute("id", "tbodyDatosCuentaBancaria");
				objTable.appendChild(objTbody);
			}
			
			function crearCuentaBancaria(cuentaBancaria)
			{
				var tbody = "tbodyDatosCuentaBancaria";
				var objTbody = $("#" + tbody);

				tr = document.createElement("tr");

				// Origen
				var tdOrigen = document.createElement("td");
				tdOrigen.setAttribute("class", "normalNegro");
				tdOrigen.appendChild(document.createTextNode(cuentaBancaria != undefined ? cuentaBancaria.origen : "" ));
				
				tr.appendChild(tdOrigen);

				// Número de cuenta
				var tdNroCuenta = document.createElement("td");

				var objInputText = document.createElement("input");
				objInputText.setAttribute("type", "text");
				objInputText.setAttribute("class", "normalNegro");
				objInputText.setAttribute("name", "nroCuenta[]");
				objInputText.setAttribute("size", "20");
				objInputText.setAttribute("disabled", "disabled");
				if(cuentaBancaria != undefined) objInputText.setAttribute("value", cuentaBancaria.numeroCuenta);
				tdNroCuenta.appendChild(objInputText);

				tr.appendChild(tdNroCuenta);
				
				// Tipo de cuenta
				tdTipoCuenta = document.createElement("td");

				var objSelect = document.createElement("select");
				objSelect.setAttribute("class", "normalNegro");
				objSelect.setAttribute("disabled", "disabled");

				var objOption = document.createElement("option");
				objOption.setAttribute("value", "0");
				objOption.appendChild(document.createTextNode("Seleccionar..."));
				objSelect.appendChild(objOption);
				
				var objOption = document.createElement("option");
				objOption.setAttribute("value", "<?php echo EntidadTipoCuentabancaria::CUENTA_DE_AHORRO?>");
				objOption.appendChild(document.createTextNode("Ahorro"));
				if(cuentaBancaria != undefined && cuentaBancaria.tipoCuenta == "<?php echo EntidadTipoCuentabancaria::CUENTA_DE_AHORRO?>")
					objOption.setAttribute("selected", "selected");
				objSelect.appendChild(objOption);

				var objOption = document.createElement("option");
				objOption.setAttribute("value", "<?php echo EntidadTipoCuentabancaria::CUENTA_CORRRIENTE?>");
				if(cuentaBancaria != undefined && cuentaBancaria.tipoCuenta == "<?php echo EntidadTipoCuentabancaria::CUENTA_CORRRIENTE?>")
					objOption.setAttribute("selected", "selected");
				objOption.appendChild(document.createTextNode("Corriente"));
				objSelect.appendChild(objOption);
				
				tdTipoCuenta.appendChild(objSelect);

				tr.appendChild(tdTipoCuenta);

				// Banco
				var tdBanco = document.createElement("td");

				var objInputText = document.createElement("input");
				objInputText.setAttribute("type", "text");
				objInputText.setAttribute("class", "normalNegro");
				objInputText.setAttribute("name", "banco[]");
				objInputText.setAttribute("disabled", "disabled");
				if(cuentaBancaria != undefined) objInputText.setAttribute("value", cuentaBancaria.banco);
				tdBanco.appendChild(objInputText);

				tr.appendChild(tdBanco);

				// Seleccionar
				var tdSeleccionar = document.createElement("td");

				var objButton = document.createElement("button");
				objButton.setAttribute("type", "button");
				objButton.setAttribute("class", "normalNegro");
				objButton.appendChild(document.createTextNode("Establecer"));
				tdSeleccionar.appendChild(objButton);				

				tr.appendChild(tdSeleccionar);

				if(
					cuentaBancaria != undefined && cuentaBancaria.numeroCuenta != "" && cuentaBancaria.banco != "" &&
					(	cuentaBancaria.tipoCuenta == "<?php echo EntidadTipoCuentabancaria::CUENTA_DE_AHORRO?>" ||
						cuentaBancaria.tipoCuenta == "<?php echo EntidadTipoCuentabancaria::CUENTA_CORRRIENTE?>"
					)
				){
					$(objButton).bind('click', {cuentaBancaria: cuentaBancaria}, function(event){
						var objSelectTipoCuenta = $('#tipoCuenta');
						$('#numeroCuenta').val(cuentaBancaria.numeroCuenta);
						$('#banco').val(cuentaBancaria.banco);
						objSelectTipoCuenta.prop('selectedIndex', 0);
						var objOption = objSelectTipoCuenta.find('option[value="'+cuentaBancaria.tipoCuenta+'"]');
						if(objOption.length > 0){
							objSelectTipoCuenta.prop('selectedIndex', objOption.prop('index'));
						}
					});
				} else {
					objButton.setAttribute("disabled", "disabled");
				}
				
				objTbody.append(tr);

				if(
					cuentaBancaria != undefined && cuentaBancaria.origen == "N"+oACUTE+"mina" &&
					(	cuentaBancaria.numeroCuenta == "" || cuentaBancaria.banco == "" ||
						(
							cuentaBancaria.tipoCuenta != "<?php echo EntidadTipoCuentabancaria::CUENTA_DE_AHORRO?>" &&
							cuentaBancaria.tipoCuenta != "<?php echo EntidadTipoCuentabancaria::CUENTA_CORRRIENTE?>"
						)
					)
				){
					var tr = document.createElement("tr");
					
					// Datos faltantes en cuenta nómina
					var tdMensaje = document.createElement("td");
					tdMensaje.setAttribute("style", "color: #FF0000");
					tdMensaje.setAttribute("colspan", "5");
					tdMensaje.appendChild(document.createTextNode("Datos de cuenta de n"+oACUTE+"mina imcompletos. Comun"+iACUTE+"quese "+
						"con la oficina de talento humano."));

					tr.appendChild(tdMensaje);
					
					objTbody.append(tr);
				}
				
			}

			function changeMunicipiosByEstadoFromIds(estados, municipios, parroquias){

				var objEstados = $('#'+estados)[0];
				var objMunicipios = $('#'+municipios)[0];
				var objParroquias = $('#'+parroquias)[0];

				changeMunicipiosByEstado({
					objEstados: objEstados,
					objMunicipios: objMunicipios,
					objParroquias: objParroquias
				});				
			}

			function changeMunicipiosByEstado()
			{
				var params = arguments[0] || null;
				if(params != null){
					var objEstados = params.objEstados || null;
					var objMunicipios = params.objMunicipios || null;
					var objParroquias = params.objParroquias || null;
					var idMunicipioSelected = params.idMunicipio || null;
					if(params.async == undefined){
						var async = true;
					} else {
						var async = params.async;
					}
				}
				
				deleteMunicipios(objMunicipios);
				deleteParroquias(objParroquias);
				
				var indexEstado = objEstados.selectedIndex;
				var idEstado = objEstados.options[indexEstado].value;

				if(idEstado != 0){
					$.ajax({
						type: "POST",
						dataType: "xml",
						async: async,
						url: "../municipio.php",
						data: {
							accion: "GetMunicipiosByEstado",
							idEstado: idEstado
						},
						success: function(xml){
							$(xml).find('municipio').each(function(){
								var tagMunicipio = $(this);
								var idMunicipio = tagMunicipio.attr("id");
								var option = document.createElement('option');
								option.value = idMunicipio;
								option.text = tagMunicipio.find('nombre').text();
								if(idMunicipioSelected != null && idMunicipioSelected == idMunicipio){
									option.setAttribute('selected', 'selected');
								}
								try {
									objMunicipios.appendChild(option);
								}catch(e){alert(e);}
							});
						}
					});
				}
			}

			function changeParroquiasByMunicipioFromIds(municipios, parroquias)
			{
				var objMunicipios = $('#'+municipios)[0];
				var objParroquias = $('#'+parroquias)[0];

				changeParroquiasByMunicipio({objMunicipios: objMunicipios, objParroquias: objParroquias});
			}
			
			function changeParroquiasByMunicipio()
			{
				var params = arguments[0] || null;
				if(params != null){
					var objMunicipios = params.objMunicipios || null;
					var objParroquias = params.objParroquias || null;
					var idParroquiaSelected = params.idParroquia || null;
				}
				
				deleteParroquias(objParroquias);
				var indexMunicipio = objMunicipios.selectedIndex;
				var idMunicipio = objMunicipios.options[indexMunicipio].value;

				if(idMunicipio != 0){
					$.ajax({
						type: "POST",
						dataType: "xml",
						url: "../parroquia.php",
						data: {
							accion: "GetParroquiasByMunicipio",
							idMunicipio: idMunicipio
						},
						success: function(xml){
							$(xml).find('parroquia').each(function(){
								var tagParroquia = $(this);
								var idParroquia =  tagParroquia.attr("id");
								var option = document.createElement('option');
								option.value = idParroquia;
								option.text = tagParroquia.find('nombre').text();
								if(idParroquiaSelected != null && idParroquiaSelected == idParroquia){
									option.setAttribute('selected', 'selected');
								}
								try {
									objParroquias.appendChild(option);
								}catch(e){alert(e);}
							});
						}
					});
				}
			}

			function changeCiudadesByEstado(){
				var params = arguments[0] || null;
				if(params != null){
					var objEstados = params.objEstados || null;
					var objCiudades = params.objCiudades || null;
					var idCiudadSelected = params.idCiudad || null;
				}
				
				deleteCiudades(objCiudades);
				
				var indexEstado = objEstados.selectedIndex;
				var idEstado = objEstados.options[indexEstado].value;
				
				if(idEstado != 0){
					$.ajax({
						type: "POST",
						dataType: "json",
						url: "../ciudad.php",
						data: {
							accion: "GetCiudadesActivasByEstado",
							tipoRespuesta: "json",
							idEstado: idEstado
						},
						success: function(json){
							$.each(json.listaciudad, function(idCiudad, objCiudad){
								var tagCiudad = $(this);
								var option = document.createElement('option');
								option.value = idCiudad;
								if(idCiudadSelected != null && idCiudadSelected == idCiudad){
									option.setAttribute('selected', 'selected');
								}
								option.appendChild(document.createTextNode(objCiudad.nombre));
								try {
									objCiudades.appendChild(option);
								}catch(e){alert(e);}
							});
						}
					});
				}
			}
			
			function deleteMunicipios(objMunicipios)
			{
				$(objMunicipios).find("option[value!='0']").remove();
			}

			function deleteParroquias(objParroquias)
			{
				$(objParroquias).find("option[value!='0']").remove();
			}

			function deleteCiudades(objCiudades){
				$(objCiudades).find("option[value!='0']").remove();
			}

			function cargarProyectoAccionCentralizada(params){
				var tipo = params['tipo'];

				if(tipo == 'proyecto'){
					mostrarProyectoAccionCentralizada({
						tipo: tipo
					});
					$('#inputTipoProyecto').attr('checked', 'checked');
				} else if(tipo == 'accionCentralizada'){
					mostrarProyectoAccionCentralizada({
						tipo: tipo
					});
					$('#inputTipoAccionCentralizada').attr('checked', 'checked');
				}
			}
			
			function mostrarProyectoAccionCentralizada(params){
				$('#proyectoAccionCentralizada > option[value!="0"]').remove();
				$('#accionEspecifica > option[value!="0"]').remove();

				var tipo = params['tipo'];
				
				if(tipo == 'proyecto'){
					$.ajax({
						type: "POST",
						dataType: "json",
						url: "../proyecto.php",
						data: {
							accion: "GetAllProyectosAprobados"
						},
						success: function(json){
							crearSelectProyecto(json);
						}
					});
				} else if (tipo == 'accionCentralizada') {
					$.ajax({
						type: "POST",
						dataType: "json",
						url: "../accioncentralizada.php",
						data: {
							accion: "GetAllAccionesCentralizadasAprobadas"
						},
						success: function(json){
							crearSelectAccionCentralizada(json);
						}
					});
				}
			}

			function crearSelectProyecto(json){
				var objSelectProyectoAccionCentralizada = $('#proyectoAccionCentralizada');
				var objSelectAccionEspecifica = $('#accionEspecifica');
				var loadAccionEspecifica = false;
				$.each(json.listaproyecto, function(index, objProyecto){
					var objOption = document.createElement("option");
					objOption.setAttribute('value', objProyecto.id.idproyecto);	
					if (formViatico.idProyectoAccionCentralizada == objProyecto.id.idproyecto) {
						objOption.setAttribute('selected', 'selected');
						loadAccionEspecifica = true;
					}
					objOption.appendChild(document.createTextNode(objProyecto.nombre));
					objSelectProyectoAccionCentralizada.append(objOption);
				});

				objSelectProyectoAccionCentralizada = $(objSelectProyectoAccionCentralizada);
				objSelectProyectoAccionCentralizada.unbind('change');
				objSelectProyectoAccionCentralizada.bind('change', function(event){
					changeAccionesEspecificasBy('proyecto', 
							objSelectProyectoAccionCentralizada[0], objSelectAccionEspecifica[0]);
				});
				if(loadAccionEspecifica){
					changeAccionesEspecificasBy('proyecto', 
						objSelectProyectoAccionCentralizada[0], objSelectAccionEspecifica[0]);
				}
			}

			function crearSelectAccionCentralizada(json){
				var objSelectProyectoAccionCentralizada = $('#proyectoAccionCentralizada');
				var objSelectAccionEspecifica = $('#accionEspecifica');
				var loadAccionEspecifica = false;
				$.each(json.listaaccioncentralizada, function(index, objAccion){
					var objOption = document.createElement("option");
					objOption.setAttribute('value', objAccion.id.idaccioncentralizada);
					if (formViatico.idProyectoAccionCentralizada == objAccion.id.idaccioncentralizada) {
						objOption.setAttribute('selected', 'selected');
						loadAccionEspecifica = true;
					}
					objOption.appendChild(document.createTextNode(objAccion.nombre));
					objSelectProyectoAccionCentralizada.append(objOption);
				});

				objSelectProyectoAccionCentralizada = $(objSelectProyectoAccionCentralizada);
				objSelectProyectoAccionCentralizada.unbind('change');
				objSelectProyectoAccionCentralizada.bind('change', function(event){
					changeAccionesEspecificasBy('acccionCentralizada', 
							objSelectProyectoAccionCentralizada[0], objSelectAccionEspecifica[0]);
				});
				if(loadAccionEspecifica){
					changeAccionesEspecificasBy('acccionCentralizada', 
						objSelectProyectoAccionCentralizada[0], objSelectAccionEspecifica[0]);
				}
			}

			function changeAccionesEspecificasBy(tipo, objProyectoAccionCentralizada, objAccionEspecifica){

				$(objAccionEspecifica).find('option[value!="0"]').remove();
				
				var indexProyectoAccionCentralizada = objProyectoAccionCentralizada.selectedIndex;
				var idProyectoAccionCentralizada = 
						objProyectoAccionCentralizada.options[indexProyectoAccionCentralizada].value;

				if(idProyectoAccionCentralizada != 0){
					if(tipo == 'proyecto'){
						$.ajax({
							type: "POST",
							dataType: "json",
							url: "../proyecto.php",
							data: {
								accion: "GetAccionesEspecificasBy",
								idProyecto: idProyectoAccionCentralizada
							},
							success: function(json){
								var objSelectAccionEspecifica = $('#accionEspecifica');
								
								$.each(json.listaaccionespecifica, function(index, objAccion){
									var label = "(" + objAccion.centrogestor + "/" + objAccion.centrocosto + 
													") " + objAccion.nombre;
									var objOption = document.createElement("option");
									objOption.setAttribute('value', objAccion.id.idaccionespecifica);
									if (formViatico.idAccionEspecifica == objAccion.id.idaccionespecifica) {
										objOption.setAttribute('selected', 'selected');
									}
									objOption.appendChild(document.createTextNode(label));
									objSelectAccionEspecifica.append(objOption);
								});
							}
						});	
					} 
					else if(tipo == 'acccionCentralizada') {
						$.ajax({
							type: "POST",
							dataType: "json",
							url: "../accioncentralizada.php",
							data: {
								accion: "GetAccionesEspecificasBy",
								idAccionCentralizada: idProyectoAccionCentralizada
							},
							success: function(json){
								var objSelectAccionEspecifica = $('#accionEspecifica');
								
								$.each(json.listaaccionespecifica, function(index, objAccion){
									var label = "(" + objAccion.centrogestor + "/" + objAccion.centrocosto + 
													") " + objAccion.nombre;
									var objOption = document.createElement("option");
									objOption.setAttribute('value', objAccion.id.idaccionespecifica);
									if (formViatico.idAccionEspecifica == objAccion.id.idaccionespecifica) {
										objOption.setAttribute('selected', 'selected');
									}
									objOption.appendChild(document.createTextNode(label));
									objSelectAccionEspecifica.append(objOption);
								});
							}
						});
					}
				}
			}

			
			function agregarRuta()
			{
				rutaIdSeq++;

				var params = arguments[0] || null;
				var ruta = params != null ? (params.ruta || null) : null;
				var rutasName = 'rutas[]';
				var idRutaName = 'idRuta[]';
				var fechaInicioRutaName = 'fechaInicioRuta[]';
				var fechaFinRutaName = 'fechaFinRuta[]';
				var diasAlimentacionName = 'diasAlimentacion[]';
				var diasHospedajeName = 'diasHospedaje[]';
				var unidadTransporteInterurbanoName = 'unidadTransporteInterurbano[]';
				var tipoTransporteName = 'tipoTransporte[]';
				var pasajeIdaVueltaName = 'pasajeIdaVuelta[]';
				var aeropuertoResidenciaName = 'aeropuertoResidencia[]';
				var residenciaAeropuertoName = 'residenciaAeropuerto[]';
				var tasaAeroportuariaIdaName = 'tasaAeroportuariaIda[]';
				var tasaAeroportuariaVueltaName = 'tasaAeroportuariaVuelta[]';
				var origenEstadosName = 'fromEstados[]';
				var origenCiudadesName = 'fromCiudades[]';
				var origenMunicipiosName = 'fromMunicipios[]';
				var origenParroquiasName = 'fromParroquias[]';
				var origenDireccionName = 'fromDireccion[]';
				var destinoEstadosName = 'toEstados[]';
				var destinoCiudadesName = 'toCiudades[]';
				var destinoMunicipiosName = 'toMunicipios[]';
				var destinoParroquiasName = 'toParroquias[]';
				var destinoDireccionName = 'toDireccion[]';
				var observacionesName = 'observacionesRutas[]';
				
				var tdRutas = "tdRutas";
				var objTdRutas = $('#' + tdRutas);

				var objTable = document.createElement('table');
				objTable.setAttribute('class', 'wrapperRutas');

				var objTbody = document.createElement('tbody');
				objTable.appendChild(objTbody);

				// tr rutas name, id de ruta, fecha de inicio y fecha de fin
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);

				var objTd = document.createElement('td');
				objTr.appendChild(objTd);

				// rutasName
				var objInputText = document.createElement('input');
				objInputText.setAttribute('name', rutasName);
				objInputText.setAttribute('type', 'hidden');
				objTd.appendChild(objInputText);
				
				// Id de ruta
				var objInputText = document.createElement('input');
				objInputText.setAttribute('name', idRutaName);
				objInputText.setAttribute('type', 'hidden');
				if(ruta != null){
					objInputText.value = ruta.id;
				} else {
					objInputText.value = "0";
				}
				objTd.appendChild(objInputText);

				// Fecha de inicio
				var objSpan = document.createElement('span');
				objSpan.appendChild(document.createTextNode('Fecha inicio(*)'));
				objSpan.setAttribute('class', 'label1');
				objTd.appendChild(objSpan);

				var id = 'fechaInicioRuta_' + (rutaIdSeq);
				var objInputFechaInicioRuta = document.createElement('input');
				objInputFechaInicioRuta.setAttribute('id', id);
				objInputFechaInicioRuta.setAttribute('name', fechaInicioRutaName);
				objInputFechaInicioRuta.setAttribute('type', 'text');
				objInputFechaInicioRuta.setAttribute('readonly', 'readonly');
				objInputFechaInicioRuta.setAttribute('class', 'dateparse');
				objInputFechaInicioRuta.setAttribute('size', '10');
				if(ruta != null){
					objInputFechaInicioRuta.value = ruta.fechaInicio;
				}
				objInputFechaInicioRuta.setAttribute("autocomplete", "off");
				objTd.appendChild(objInputFechaInicioRuta);

				var objA = document.createElement('a');
				objA.setAttribute('href', 'javascript:void(0);');
				objTd.appendChild(objA);
				
				var objImg = document.createElement('img');
				objImg.setAttribute('src', '../../js/lib/calendarPopup/img/calendar.gif');
				objImg.setAttribute('class', 'cp_img');
				objImg.setAttribute('alt', 'Open popup calendar');
				objA.appendChild(objImg);

				objA = $(objA);
				objA.unbind('click');
				objA.bind('click', {id: id}, function(event){
					g_Calendar.show(event, event.data.id);
				});
				
				// Fecha de fin
				var objSpan = document.createElement('span');
				objSpan.setAttribute('class', 'fechaFin label1');
				objSpan.appendChild(document.createTextNode('Fecha fin(*)'));
				objTd.appendChild(objSpan);

				var id = 'fechaFinRuta_' + (rutaIdSeq);
				var objInputFechaFinRuta = document.createElement('input');
				objInputFechaFinRuta.setAttribute('id', id);
				objInputFechaFinRuta.setAttribute('name', fechaFinRutaName);
				objInputFechaFinRuta.setAttribute('type', 'text');
				objInputFechaFinRuta.setAttribute('readonly', 'readonly');
				objInputFechaFinRuta.setAttribute('class', 'dateparse');
				objInputFechaFinRuta.setAttribute('size', '10');
				if(ruta != null){
					objInputFechaFinRuta.value = ruta.fechaFin;
				}
				objInputFechaFinRuta.setAttribute("autocomplete", "off");
				objTd.appendChild(objInputFechaFinRuta);

				$(objInputFechaInicioRuta).unbind();
				$(objInputFechaInicioRuta).bind('focus', {objInputFechaFinRuta: objInputFechaFinRuta}, function(event){
					compararFechasYBorrarByObj(this, objInputFechaFinRuta, this);
				});

				$(objInputFechaFinRuta).unbind();
				$(objInputFechaFinRuta).bind('focus', {objInputFechaInicioRuta: objInputFechaInicioRuta}, function(event){
					compararFechasYBorrarByObj(objInputFechaInicioRuta, this, this);
				});

				var objA = document.createElement('a');
				objA.setAttribute('href', 'javascript:void(0);');
				objTd.appendChild(objA);
				
				var objImg = document.createElement('img');
				objImg.setAttribute('src', '../../js/lib/calendarPopup/img/calendar.gif');
				objImg.setAttribute('class', 'cp_img');
				objImg.setAttribute('alt', 'Open popup calendar');
				objA.appendChild(objImg);

				objA = $(objA);
				objA.unbind('click');
				objA.bind('click', {id: id}, function(event){
					g_Calendar.show(event, event.data.id);
				});
	
				// tr días a cancelar por hospedaje, alimentación y transporte
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);

				var objTd = document.createElement('td');
				objTr.appendChild(objTd);

				// Días a cancelar por hospedaje
				var objSpan = document.createElement('span');
				objSpan.appendChild(document.createTextNode('Noches hospedaje'));
				objSpan.setAttribute('class', 'label1');
				objTd.appendChild(objSpan);

				var objInputText = document.createElement('input');
				objInputText.setAttribute('name', diasHospedajeName);
				objInputText.setAttribute('type', 'text');
				objInputText.setAttribute('class', 'normalNegro asignacion');
				if(ruta != null){
					objInputText.value = ruta.diasHospedaje;
				} else {
					objInputText.value = 0;
				}
				objTd.appendChild(objInputText);

				objInputText = $(objInputText);
				objInputText.unbind('keyup');
				objInputText.bind('keyup', function(){
					validarNumero(this, true);
					calcularHospedaje();
				});

				// Días a cancelar por alimentación
				var objSpan = document.createElement('span');
				objSpan.appendChild(document.createTextNode('D'+iACUTE+'as alimentaci'+oACUTE+'n'));
				objSpan.setAttribute('class', 'label1');
				objTd.appendChild(objSpan);

				var objInputText = document.createElement('input');
				objInputText.setAttribute('name', diasAlimentacionName);
				objInputText.setAttribute('type', 'text');
				objInputText.setAttribute('class', 'normalNegro asignacion');
				if(ruta != null){
					objInputText.value = ruta.diasAlimentacion;
				}  else {
					objInputText.value = 0;
				}
				objTd.appendChild(objInputText);

				objInputText = $(objInputText);
				objInputText.unbind('keyup');
				objInputText.bind('keyup', function(){					
					validarNumero(this, true);
					calcularAlimentacion();
				});

				// Días a cancelar por transporte interurbano
				var objSpan = document.createElement('span');
				objSpan.appendChild(document.createTextNode('D'+iACUTE+'as transporte interurbano'));
				objSpan.setAttribute('class', 'label1');
				objTd.appendChild(objSpan);

				var objInputText = document.createElement('input');
				objInputText.setAttribute('name', unidadTransporteInterurbanoName);
				objInputText.setAttribute('type', 'text');
				objInputText.setAttribute('class', 'normalNegro asignacion');
				if(ruta != null){
					objInputText.value = ruta.unidadTransporteInterurbano;
				} else {
					objInputText.value = 0;
				}
				objTd.appendChild(objInputText);

				objInputText = $(objInputText);
				objInputText.unbind('keyup');
				objInputText.bind('keyup', function(){
					objInput = this;
					validarNumero(objInput, true);
					calcularTransporteInterurbano();
				});

				// tr del mensaje temporal sobre la tasa aeroportuaria, en el caso de que el tipo de transporte sea aéreo
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);

				var objTd = document.createElement('td');
				objTr.appendChild(objTd);
				
				var objSpan = document.createElement('span');
				objSpan.setAttribute('class', 'mensajeError');
				objSpan.appendChild(document.createTextNode('En caso de que el tipo de transporte sea a'+eACUTE+'reo, ' +
					'se debe especificar si se desean las tasas aeroportuarias de ida o vuelta, explicitamente.'));
				objTd.appendChild(objSpan);
				
				// tr tipo de transporte, pasaje ida y vuelta, trasp. aeropuert-residencia y residencia-aeropuerto.
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);
				
				var objTd = document.createElement('td');
				objTr.appendChild(objTd);
				
				// Tipo de transporte
				var objSpan = document.createElement('span');
				objSpan.appendChild(document.createTextNode('Tipo de transporte(*)'));
				objSpan.setAttribute('class', 'label1');
				objTd.appendChild(objSpan);
				
				var objSelectTipoTransporte = document.createElement('select');
				objSelectTipoTransporte.setAttribute('name', tipoTransporteName);
				objSelectTipoTransporte.setAttribute('class', 'normalNegro tipoTransporte');
				objTd.appendChild(objSelectTipoTransporte);
				
				var objOption = document.createElement('option');
				objOption.appendChild(document.createTextNode('Seleccionar...'));
				objOption.value = '0';
				objSelectTipoTransporte.appendChild(objOption);
				
				$.each(tipoTransportes, function(idTipoTransporte, objTipoTransporte){
					var objOption = document.createElement('option');
					objOption.appendChild(document.createTextNode(objTipoTransporte.nombre));
					objOption.value = idTipoTransporte;
					if(ruta != null && ruta.idTipoTransporte == idTipoTransporte){
						objOption.setAttribute('selected', 'selected');
					}
					objSelectTipoTransporte.appendChild(objOption);
				});
				objSelectTipoTransporte = $(objSelectTipoTransporte);

				// Div Pasaje ida y vuelta
				var objDivIdaVuelta = document.createElement('div');
				objDivIdaVuelta.setAttribute('class', 'pasajes');
				objTd.appendChild(objDivIdaVuelta);
				
				// Pasaje ida y vuelta
				var objSpan = document.createElement('span');
				objSpan.appendChild(document.createTextNode(
					NON_BREAKING_SPACE + NON_BREAKING_SPACE + NON_BREAKING_SPACE + 'Pasaje ida y vuelta'
				));
				objDivIdaVuelta.appendChild(objSpan);

				var objInputHiddenIdaVuelta = document.createElement('input');
				objInputHiddenIdaVuelta.setAttribute('name', pasajeIdaVueltaName);
				objInputHiddenIdaVuelta.setAttribute('type', 'hidden');
				if(ruta != null){
					if(ruta.pasajeIdaVuelta == true){
						objInputHiddenIdaVuelta.value = 'true';
					} else {
						objInputHiddenIdaVuelta.value = 'false';
					}
				}
				objDivIdaVuelta.appendChild(objInputHiddenIdaVuelta);
				
				var objInputIdaVuelta = document.createElement('input');
				objInputIdaVuelta.setAttribute('type', 'checkbox');
				if(ruta != null && ruta.pasajeIdaVuelta == true){
					objInputIdaVuelta.checked = true;
				}
				objDivIdaVuelta.appendChild(objInputIdaVuelta);

				// Div Transporte residencia-aeropuerto
				var objDivResidenciaAeropuerto = document.createElement('div');
				objDivResidenciaAeropuerto.setAttribute('class', 'pasajes');
				objTd.appendChild(objDivResidenciaAeropuerto);
				
				// Transporte residencia-aeropuerto
				var objSpan = document.createElement('span');
				objSpan.appendChild(document.createTextNode(
					NON_BREAKING_SPACE + NON_BREAKING_SPACE + NON_BREAKING_SPACE + 'Transp. residencia-aeropuerto'
				));
				objDivResidenciaAeropuerto.appendChild(objSpan);

				var objInputText = document.createElement('input');
				objInputText.setAttribute('name', residenciaAeropuertoName);
				objInputText.setAttribute('type', 'hidden');
				if(ruta != null){
					if(ruta.residenciaAeropuerto == true){
						objInputText.value = 'true';
					} else {
						objInputText.value = 'false';
					}
				}
				objDivResidenciaAeropuerto.appendChild(objInputText);

				var objInputCheckbox = document.createElement('input');
				objInputCheckbox.setAttribute('type', 'checkbox');
				if(ruta != null && ruta.residenciaAeropuerto == true){
					objInputCheckbox.checked = true;
				}
				objDivResidenciaAeropuerto.appendChild(objInputCheckbox);

				objInputCheckbox = $(objInputCheckbox);
				objInputCheckbox.unbind('click');
				objInputCheckbox.bind('click', {objInputHidden: objInputText}, function(event){					
					event.data.objInputHidden.value = this.checked ? 'true' : 'false';

					changeTransporteARRA({
						objTrIdaVuelta: objDivIdaVuelta,
						objTrTransporte1: objDivResidenciaAeropuerto,
						objTrTransporte2: objDivAeropuertoResidencia
					});

					calcularResidenciaAeropuerto(false);
					calcularAeropuertoResidencia(false);
					calcularTotal();
				});
				
				// Div Transporte aeropuerto-residencia
				var objDivAeropuertoResidencia = document.createElement('div');
				objDivAeropuertoResidencia.setAttribute('class', 'pasajes');
				objTd.appendChild(objDivAeropuertoResidencia);

				// Transporte aeropuerto-residencia
				var objSpan = document.createElement('span');
				objSpan.appendChild(document.createTextNode(
					NON_BREAKING_SPACE + NON_BREAKING_SPACE + NON_BREAKING_SPACE + 'Transp. aeropuerto-residencia'
				));
				objDivAeropuertoResidencia.appendChild(objSpan);

				var objInputText = document.createElement('input');
				objInputText.setAttribute('name', aeropuertoResidenciaName);
				objInputText.setAttribute('type', 'hidden');
				if(ruta != null){
					if(ruta.aeropuertoResidencia == true){
						objInputText.value = 'true';
					} else {
						objInputText.value = 'false';
					}
				}
				objDivAeropuertoResidencia.appendChild(objInputText);
				
				var objInputCheckbox = document.createElement('input');
				objInputCheckbox.setAttribute('type', 'checkbox');
				if(ruta != null && ruta.aeropuertoResidencia == true){
					objInputCheckbox.checked = true;
				}
				objDivAeropuertoResidencia.appendChild(objInputCheckbox);

				objInputCheckbox = $(objInputCheckbox);
				objInputCheckbox.unbind('click');
				objInputCheckbox.bind('click', {objInputHidden: objInputText}, function(event){					
					event.data.objInputHidden.value = this.checked ? 'true' : 'false';

					changeTransporteARRA({
						objTrIdaVuelta: objDivIdaVuelta,
						objTrTransporte1: objDivAeropuertoResidencia,
						objTrTransporte2: objDivResidenciaAeropuerto
					});

					calcularResidenciaAeropuerto(false);
					calcularAeropuertoResidencia(false);
					calcularTotal();
				});

				// Div tasa aeroportuaria ida
				var objDivTasaAeroportuariaIda = document.createElement('div');
				objDivTasaAeroportuariaIda.setAttribute('class', 'pasajes');
				objTd.appendChild(objDivTasaAeroportuariaIda);

				// Tasa aeroportuaria ida
				var objSpan = document.createElement('span');
				objSpan.appendChild(document.createTextNode(
					NON_BREAKING_SPACE + NON_BREAKING_SPACE + NON_BREAKING_SPACE + 'Tasa aeroportuaria ida')
				);
				objDivTasaAeroportuariaIda.appendChild(objSpan);

				var objInputText = document.createElement('input');
				objInputText.setAttribute('name', tasaAeroportuariaIdaName);
				objInputText.setAttribute('type', 'hidden');
				
				if(ruta != null){
					if(ruta.tasaAeroportuariaIda == true){
						objInputText.value = 'true';
					} else {
						objInputText.value = 'false';
					}
				}
				
				objDivTasaAeroportuariaIda.appendChild(objInputText);

				var objInputCheckbox = document.createElement('input');
				objInputCheckbox.setAttribute('type', 'checkbox');
				
				if(ruta != null && ruta.tasaAeroportuariaIda == true){
					objInputCheckbox.checked = true;
				}
				
				objDivTasaAeroportuariaIda.appendChild(objInputCheckbox);

				objInputCheckbox = $(objInputCheckbox);
				objInputCheckbox.unbind('click');
				objInputCheckbox.bind('click', {objInputHidden: objInputText}, function(event){					
					event.data.objInputHidden.value = this.checked ? 'true' : 'false';
					
					changeTasaAeroporturiaIdaVuelta({
						objTrIdaVuelta: objDivIdaVuelta,
						objTrTasaAeroportuaria1: objDivTasaAeroportuariaIda,
						objTrTasaAeroportuaria2: objDivTasaAeroportuariaVuelta
					});

					calcularTasaAeroportuaria(false);
					calcularTotal();
				});

				// Div tasa aeroportuaria vuelta
				var objDivTasaAeroportuariaVuelta = document.createElement('div');
				objDivTasaAeroportuariaVuelta.setAttribute('class', 'pasajes');
				objTd.appendChild(objDivTasaAeroportuariaVuelta);

				// Tasa aeroportuaria ida
				var objSpan = document.createElement('span');
				objSpan.appendChild(document.createTextNode(
					NON_BREAKING_SPACE + NON_BREAKING_SPACE + NON_BREAKING_SPACE + 'Tasa aeroportuaria vuelta')
				);
				objDivTasaAeroportuariaVuelta.appendChild(objSpan);

				var objInputText = document.createElement('input');
				objInputText.setAttribute('name', tasaAeroportuariaVueltaName);
				objInputText.setAttribute('type', 'hidden');
				
				if(ruta != null){
					if(ruta.tasaAeroportuariaVuelta == true){
						objInputText.value = 'true';
					} else {
						objInputText.value = 'false';
					}
				}
				
				objDivTasaAeroportuariaVuelta.appendChild(objInputText);

				var objInputCheckbox = document.createElement('input');
				objInputCheckbox.setAttribute('type', 'checkbox');
				
				if(ruta != null && ruta.tasaAeroportuariaVuelta == true){
					objInputCheckbox.checked = true;
				}
				
				objDivTasaAeroportuariaVuelta.appendChild(objInputCheckbox);

				objInputCheckbox = $(objInputCheckbox);
				objInputCheckbox.unbind('click');
				objInputCheckbox.bind('click', {objInputHidden: objInputText}, function(event){					
					event.data.objInputHidden.value = this.checked ? 'true' : 'false';
					
					changeTasaAeroporturiaIdaVuelta({
						objTrIdaVuelta: objDivIdaVuelta,
						objTrTasaAeroportuaria1: objDivTasaAeroportuariaVuelta,
						objTrTasaAeroportuaria2: objDivTasaAeroportuariaIda
					});

					calcularTasaAeroportuaria(false);
					calcularTotal();
				});

				// Eventos de Pasaje Ida y Vuelta
				objInputIdaVuelta = $(objInputIdaVuelta);
				objInputIdaVuelta.unbind('click');
				objInputIdaVuelta.bind('click', {objInputHidden: objInputHiddenIdaVuelta}, function(event){
					event.data.objInputHidden.value = this.checked ? 'true' : 'false';
					changePasajeIdaVuelta({
						objTrIdaVuelta: objDivIdaVuelta,
						objTrAeropuertoResidencia: objDivAeropuertoResidencia,
						objTrResidenciaAeropuerto: objDivResidenciaAeropuerto,
						objTrTasaAeroportuariaIda: objDivTasaAeroportuariaIda,
						objTrTasaAeroportuariaVuelta: objDivTasaAeroportuariaVuelta
					});
					calcularTasaAeroportuaria(false);
					calcularResidenciaAeropuerto(false);
					calcularAeropuertoResidencia(false);
					calcularTotal();
				});

				// Tr text area de observaciones
				var objTrObservaciones = document.createElement('tr');
				objTbody.appendChild(objTrObservaciones);

				var objTd = document.createElement('td');
				objTrObservaciones.appendChild(objTd);

				// Label text Area de observaciones
				var objSpan = document.createElement('span');
				objSpan.appendChild(document.createTextNode('Observaciones: '));
				objTd.appendChild(objSpan);

				//
				var objSpan = document.createElement('span');
				objSpan.setAttribute('class', 'mensajeError');
				objSpan.appendChild(document.createTextNode('(Estas observaciones solo ser'+aACUTE+'n visibles en la requisici'
							+oACUTE+'n.)'));
				objTd.appendChild(objSpan);
				
				// Text Area de observaciones
				var objTextArea = document.createElement('textarea');
				objTextArea.setAttribute('name', observacionesName);
				objTextArea.setAttribute('rows', '1');
				objTextArea.setAttribute('cols', '105');
				objTextArea.setAttribute('class', 'normalNegro');
				if(ruta != null){
					objTextArea.appendChild(document.createTextNode(ruta.observaciones));
				}
				objTd.appendChild(objTextArea);
				
				// Validar el tipo de transporte para decidir si mostrar o no
				// el checkbox de pasaje Ida y Vuelta
				validarTipoTransporte({
					objSelectTipoTransporte: objSelectTipoTransporte[0],
					objTrIdaVuelta: objDivIdaVuelta,
					objTrAeropuertoResidencia: objDivAeropuertoResidencia,
					objTrResidenciaAeropuerto: objDivResidenciaAeropuerto,
					objTrTasaAeroportuariaIda: objDivTasaAeroportuariaIda,
					objTrTasaAeroportuariaVuelta: objDivTasaAeroportuariaVuelta,
					objTrObservaciones: objTrObservaciones
				});
				
				// Evento de verificación de tipo de transporte
				objSelectTipoTransporte.unbind('change');
				objSelectTipoTransporte.bind('change',
					function(event)
					{
						validarTipoTransporte({
							objSelectTipoTransporte: this,
							objTrIdaVuelta: objDivIdaVuelta,
							objTrAeropuertoResidencia: objDivAeropuertoResidencia,
							objTrResidenciaAeropuerto: objDivResidenciaAeropuerto,
							objTrTasaAeroportuariaIda: objDivTasaAeroportuariaIda,
							objTrTasaAeroportuariaVuelta: objDivTasaAeroportuariaVuelta,
							objTrObservaciones: objTrObservaciones
						});

						calcularTasaAeroportuaria(false);
						calcularResidenciaAeropuerto(false);
						calcularAeropuertoResidencia(false);
						calcularTotal();
					}
				);

				//Tr origen/destino
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);
				
				var objTd = document.createElement('td');
				objTr.appendChild(objTd);

				// Table origen / destino 
				objTableOD = document.createElement('table');
				objTableOD.setAttribute('class', 'origenDestino');
				objTd.appendChild(objTableOD);

				objTBodyOD = document.createElement('tbody');
				objTableOD.appendChild(objTBodyOD);

				objTrOD = document.createElement('tr');
				objTBodyOD.appendChild(objTrOD);

				objTdOD = document.createElement('td');
				objTrOD.appendChild(objTdOD);
				objTdOD.appendChild(document.createTextNode(''));

				objTdOD = document.createElement('td');
				objTrOD.appendChild(objTdOD);
				objTdOD.appendChild(document.createTextNode('Estado (*)'));

				objTdOD = document.createElement('td');
				objTrOD.appendChild(objTdOD);
				objTdOD.appendChild(document.createTextNode('Ciudad'));
				
				objTdOD = document.createElement('td');
				objTrOD.appendChild(objTdOD);
				objTdOD.appendChild(document.createTextNode('Municipio'));

				objTdOD = document.createElement('td');
				objTrOD.appendChild(objTdOD);
				objTdOD.appendChild(document.createTextNode('Parroquia'));

				objTdOD = document.createElement('td');
				objTrOD.appendChild(objTdOD);
				objTdOD.appendChild(document.createTextNode('Direcci'+oACUTE+'n'));

				objTrOD = document.createElement('tr');
				objTBodyOD.appendChild(objTrOD);

				var rutaUbicacion = null;
				if (ruta != null){
					rutaUbicacion = {
						idEstado: ruta.idFromEstado,
						idCiudad: ruta.idFromCiudad,
						idMunicipio: ruta.idFromMunicipio,
						idParroquia: ruta.idFromParroquia,
						direccion: ruta.fromDireccion
					};
				}
				agregarRutaUbicacion({
					rutaUbicacion: rutaUbicacion,
					objTr: objTrOD,
					label: 'Origen:',
					nameEstados: origenEstadosName,
					nameCiudades: origenCiudadesName,
					nameMunicipios: origenMunicipiosName,
					nameParroquias: origenParroquiasName,
					nameDireccion: origenDireccionName
				});


				objTrOD = document.createElement('tr');
				objTBodyOD.appendChild(objTrOD);
				
				var rutaUbicacion = null;
				if (ruta != null){
					rutaUbicacion = {
						idEstado: ruta.idToEstado,
						idCiudad: ruta.idToCiudad,
						idMunicipio: ruta.idToMunicipio,
						idParroquia: ruta.idToParroquia,
						direccion: ruta.toDireccion
					};
				}
				agregarRutaUbicacion({
					rutaUbicacion: rutaUbicacion,
					objTr: objTrOD,
					label: 'Destino:',
					nameEstados: destinoEstadosName,
					nameCiudades: destinoCiudadesName,
					nameMunicipios: destinoMunicipiosName,
					nameParroquias: destinoParroquiasName,
					nameDireccion: destinoDireccionName
				});
				
				// Tr link eliminar
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);

				var objTd = document.createElement('td');
				objTd.setAttribute('class', 'footer');
				objTr.appendChild(objTd);
				
				// Link eliminar
				var objA = document.createElement('a');
				objA.setAttribute('href', 'javascript:void(0);');
				objA.appendChild(document.createTextNode('Eliminar ruta'));
				objTd.appendChild(objA);

				objA = $(objA);
				objA.unbind('click');
				objA.bind('click', function(){					
					countRutas = objTdRutas.find(">table").length;
					if(countRutas > 1){
						$(objTable).remove();

						calcularHospedaje(false);
						calcularAlimentacion(false);
						calcularTransporteInterurbano(false);
						calcularTasaAeroportuaria(false);
						calcularAeropuertoResidencia(false);
						calcularResidenciaAeropuerto(false);
						calcularTotal();
						
					} else {
						alert("No se puede eliminar. Debe existir al menos una ruta.");
					}
				});
				
				objTdRutas.append(objTable);
			}


			function agregarRutaUbicacion(params)
			{
				var rutaUbicacion = params['rutaUbicacion'] || null;
				var objTr = params['objTr'];
				var label = params['label'];
				var nameEstados = params['nameEstados'];
				var nameCiudades = params['nameCiudades'];
				var nameMunicipios = params['nameMunicipios'];
				var nameParroquias = params['nameParroquias'];
				var nameDireccion = params['nameDireccion'];

				// Label
				var objTd = document.createElement('td');
				objTr.appendChild(objTd);

				objTd.appendChild(document.createTextNode(label));
				
				// Estado
				var objTd = document.createElement('td');
				objTr.appendChild(objTd);

				var objSelectEstados = document.createElement('select');
				objSelectEstados.setAttribute('name', nameEstados);
				objSelectEstados.setAttribute('class', 'normalNegro');
				objTd.appendChild(objSelectEstados);
				objTd.appendChild(document.createElement('br'));
				
				var objOption = document.createElement('option');
				objOption.value = "0";
				objOption.appendChild(document.createTextNode('Seleccionar...'));
				objSelectEstados.appendChild(objOption);
				for(idEstado in estados){
					var objOption = document.createElement("option");
					objOption.appendChild(document.createTextNode(estados[idEstado]['nombre']));
					objOption.value = idEstado;
					if(rutaUbicacion != null && rutaUbicacion.idEstado == idEstado){
						objOption.setAttribute('selected', 'selected');
					}
					objSelectEstados.appendChild(objOption);
				}

				// Ciudades	
				var objTd = document.createElement('td');
				objTr.appendChild(objTd);

				var objSelectCiudades = document.createElement('select');
				objSelectCiudades.setAttribute('name', nameCiudades);
				objSelectCiudades.setAttribute('class', 'normalNegro');
				objTd.appendChild(objSelectCiudades);
				objTd.appendChild(document.createElement('br'));

				var objOption = document.createElement('option');
				objOption.value = "0";
				objOption.appendChild(document.createTextNode('Seleccionar...'));
				objSelectCiudades.appendChild(objOption);


				// Municipios
				var objTd = document.createElement('td');
				objTr.appendChild(objTd);

				var objSelectMunicipios = document.createElement('select');
				objSelectMunicipios.setAttribute('name', nameMunicipios);
				objSelectMunicipios.setAttribute('class', 'normalNegro');
				objTd.appendChild(objSelectMunicipios);
				objTd.appendChild(document.createElement('br'));

				var objOption = document.createElement('option');
				objOption.value = "0";
				objOption.appendChild(document.createTextNode('Seleccionar...'));
				objSelectMunicipios.appendChild(objOption);

				// Parroquias
				var objTd = document.createElement('td');
				objTr.appendChild(objTd);

				var objSelectParroquias = document.createElement('select');
				objSelectParroquias.setAttribute('name', nameParroquias);
				objSelectParroquias.setAttribute('class', 'normalNegro');
				objTd.appendChild(objSelectParroquias);
				objTd.appendChild(document.createElement('br'));

				var objOption = document.createElement('option');
				objOption.value = "0";
				objOption.appendChild(document.createTextNode('Seleccionar...'));
				objSelectParroquias.appendChild(objOption);

				// Dirección
				var objTd = document.createElement('td');
				objTr.appendChild(objTd);
				
				var objTextArea = document.createElement('textarea');
				objTextArea.setAttribute('name', nameDireccion);
				objTextArea.setAttribute('rows', '1');
				objTextArea.setAttribute('cols', '15');
				objTextArea.setAttribute('class', 'normalNegro');
				if(rutaUbicacion != null){
					objTextArea.appendChild(document.createTextNode(rutaUbicacion.direccion));
				}
				objTd.appendChild(objTextArea);

				objSelectEstados = $(objSelectEstados);
				objSelectMunicipios = $(objSelectMunicipios);
				objSelectParroquias = $(objSelectParroquias);
				objSelectCiudades = $(objSelectCiudades);

				// Evento de la carga de municipios y las ciudades dado un estado
				objSelectEstados.unbind("change");
				objSelectEstados.bind("change", function(event){
					changeMunicipiosByEstado({
						objEstados: objSelectEstados[0],
						objMunicipios: objSelectMunicipios[0], 
						objParroquias: objSelectParroquias[0]
					});
					changeCiudadesByEstado({
						objEstados: objSelectEstados[0], objCiudades: objSelectCiudades[0]
					});
				});

				// Si hay un estado seleccionado cargar las ciudades y municipios
				// correspondientes para ese estado. También se debe indicar si hay
				// una ciudad y/o una municipio seleccionado para
				// realizar el procedimiento correspondiente 
				if(rutaUbicacion != null && rutaUbicacion.idEstado != 0)
				{
					changeCiudadesByEstado({
						objEstados: objSelectEstados[0], objCiudades: objSelectCiudades[0], idCiudad: rutaUbicacion.idCiudad 
					});

					changeMunicipiosByEstado({
						objEstados: objSelectEstados[0],
						objMunicipios: objSelectMunicipios[0], 
						objParroquias: objSelectParroquias[0],
						idMunicipio: rutaUbicacion.idMunicipio,
						async: false
					});
				}
				
				// Evento de la carga de parroquias dado un municipio
				objSelectMunicipios.unbind("change");
				objSelectMunicipios.bind("change", function(event){
					changeParroquiasByMunicipio({objMunicipios: objSelectMunicipios[0], objParroquias: objSelectParroquias[0]});
				});

				if(rutaUbicacion != null && rutaUbicacion.idMunicipio != 0)
				{
					changeParroquiasByMunicipio({
						objMunicipios: objSelectMunicipios[0],
						objParroquias: objSelectParroquias[0],
						idParroquia: rutaUbicacion.idParroquia
					});
				}
				
			}

			function calcularAlimentacion()
			{
				var callCalcularTotal = (arguments[0] == true || arguments[0] == false) ? arguments[0] : true;
				
				var idTotalUnidades = 'diasAlimentacion';
				var nameUnidad = 'diasAlimentacion';
				var idSubMonto = 'tMontoAlimentacion';
				var codigoAsignacion = COD_ALIMENTACION;
				var totalUnidades = 0;
				
				var objUnidades = $('#' + idTotalUnidades);
				var objTotalMonto = $('#' + idSubMonto);
				
				$('input[name="' + nameUnidad + '\[\]"]').each(function(){
					var unidades = parseInt(this.value);
					unidades = (isNaN(unidades)) ? 0 : unidades;
					totalUnidades += unidades;
				});
				objUnidades.empty();
				objUnidades.append(totalUnidades);

				objTotalMonto.empty();
				objTotalMonto.append(totalUnidades * montoAsignaciones[codigoAsignacion]);

				if(callCalcularTotal) calcularTotal();
			}

			function calcularHospedaje()
			{
				var callCalcularTotal = (arguments[0] == true || arguments[0] == false) ? arguments[0] : true;
				
				var idTotalUnidades = 'diasHospedaje';
				var nameUnidad = 'diasHospedaje';
				var idSubMonto = 'tMontoHospedaje';
				var codigoAsignacion = COD_HOSPEDAJE;
				var totalUnidades = 0;
				
				var objUnidades = $('#' + idTotalUnidades);
				var objTotalMonto = $('#' + idSubMonto);
				
				$('input[name="' + nameUnidad + '\[\]"]').each(function(){
					var unidades = parseInt(this.value);
					unidades = (isNaN(unidades)) ? 0 : unidades;
					totalUnidades += unidades;
				});
				objUnidades.empty();
				objUnidades.append(totalUnidades);

				objTotalMonto.empty();
				objTotalMonto.append(totalUnidades * montoAsignaciones[codigoAsignacion]);

				if(callCalcularTotal) calcularTotal();
			}

			function calcularTransporteInterurbano()
			{
				var callCalcularTotal = (arguments[0] == true || arguments[0] == false) ? arguments[0] : true;
				
				var idTotalUnidades = 'totalUnidadTransporteInterurbano';
				var nameUnidad = 'unidadTransporteInterurbano';
				var idSubMonto = 'subMontoTransporteInterurbano';
				var codigoAsignacion = COD_TRANSPORTE_INTERURBANO;
				var totalUnidades = 0;
				
				var objUnidades = $('#' + idTotalUnidades);
				var objTotalMonto = $('#' + idSubMonto);
				
				$('input[name="' + nameUnidad + '\[\]"]').each(function(){
					var unidades = parseInt(this.value);
					unidades = (isNaN(unidades)) ? 0 : unidades;
					totalUnidades += unidades;
				});
				objUnidades.empty();
				objUnidades.append(totalUnidades);

				objTotalMonto.empty();
				objTotalMonto.append(totalUnidades * montoAsignaciones[codigoAsignacion]);

				if(callCalcularTotal) calcularTotal();
			}

			function calcularServicioComunicaciones()
			{
				var callCalcularTotal = (arguments[0] == true || arguments[0] == false) ? arguments[0] : true;
				
				var idInputUnidades = 'unidadServicioComunicaciones';
				var idTotalMonto = 'subMontoServicioComunicaciones';
				var codigoAsignacion = COD_SERVICIO_COMUNICACIONES;
				var totalUnidades = 0;
				
				var objInputUnidades = $('#' + idInputUnidades);
				var objTotalMonto = $('#' + idTotalMonto);
				
				validarNumero(objInputUnidades[0], true);
				
				totalUnidades = parseInt(objInputUnidades.val());
				totalUnidades = (isNaN(totalUnidades)) ? 0 : totalUnidades;

				objTotalMonto.empty();
				objTotalMonto.append(totalUnidades * montoAsignaciones[codigoAsignacion]);

				if(callCalcularTotal) calcularTotal();
			}

			function calcularAsignacionTransporte()
			{
				var callCalcularTotal = (arguments[0] == true || arguments[0] == false) ? arguments[0] : true;
				
				var idInputUnidades = 'unidadAsignacionTransporte';
				var idTotalMonto = 'subMontoAsignacionTransporte';
				var codigoAsignacion = COD_ASIGNACION_TRANSPORTE;
				var totalUnidades = 0;
				
				var objInputUnidades = $('#' + idInputUnidades);
				var objTotalMonto = $('#' + idTotalMonto);

				validarNumero(objInputUnidades[0], true);
				
				totalUnidades = parseInt(objInputUnidades.val());
				totalUnidades = (isNaN(totalUnidades)) ? 0 : totalUnidades;

				objTotalMonto.empty();
				objTotalMonto.append(totalUnidades * montoAsignaciones[codigoAsignacion]);

				if(callCalcularTotal) calcularTotal();
			}

			function calcularTransporteExtraurbano()
			{
				var callCalcularTotal = (arguments[0] == true || arguments[0] == false) ? arguments[0] : true;
				
				var idInputMonto = 'montoTransporteExtraurbano';
				var idInputUnidades = 'unidadTransporteExtraurbano';
				var idTotalMonto = 'subMontoTransporteExtraurbano';
				var monto = 0;
				var totalUnidades = 0;

				var objInputMonto = $('#' + idInputMonto);
				var objInputUnidades = $('#' + idInputUnidades);
				var objTotalMonto = $('#' + idTotalMonto);

				validarNumero(objInputMonto[0], true);
				validarNumero(objInputUnidades[0], true);

				monto = parseInt(objInputMonto.val());
				monto = (isNaN(monto)) ? 0 : monto;

				totalUnidades = parseInt(objInputUnidades.val());
				totalUnidades = (isNaN(totalUnidades)) ? 0 : totalUnidades;

				objTotalMonto.empty();
				objTotalMonto.append(totalUnidades * monto);

				if(callCalcularTotal) calcularTotal();
			}
			
			function calcularTransporteCiudades()
			{
				var callCalcularTotal = (arguments[0] == true || arguments[0] == false) ? arguments[0] : true;
				
				var idInputMonto = 'montoTransporteCiudades';
				var idInputUnidades = 'unidadTransporteCiudades';
				var idTotalMonto = 'subMontoTransporteCiudades';
				var monto = 0;
				var totalUnidades = 0;

				var objInputMonto = $('#' + idInputMonto);
				var objInputUnidades = $('#' + idInputUnidades);
				var objTotalMonto = $('#' + idTotalMonto);

				validarNumero(objInputMonto[0], true);
				validarNumero(objInputUnidades[0], true);

				monto = parseInt(objInputMonto.val());
				monto = (isNaN(monto)) ? 0 : monto;

				totalUnidades = parseInt(objInputUnidades.val());
				totalUnidades = (isNaN(totalUnidades)) ? 0 : totalUnidades;

				objTotalMonto.empty();
				objTotalMonto.append(totalUnidades * monto);

				if(callCalcularTotal) calcularTotal();
			}
			
			function getTipoTransporteObjects(objTr){
				var objects = new Object();
				objTr = $(objTr);
				objects['tr'] = objTr; 
				objects['inputHidden'] = objTr.find('input[type="hidden"]')[0];
				objects['inputCheckbox'] = objTr.find('input[type="checkbox"]')[0];

				return objects;
			}

			function getObservacionesRutaObjects(objTr){
				var objects = new Object();
				objTr = $(objTr);
				objects['tr'] = objTr; 
				objects['textarea'] = objTr.find('textarea');

				return objects;
			}
			
			function validarTipoTransporte(params)
			{
				var objSelectTipoTransporte = params.objSelectTipoTransporte;
				var objTrIdaVuelta = params.objTrIdaVuelta;
				var objTrAeropuertoResidencia = params.objTrAeropuertoResidencia;
				var objTrResidenciaAeropuerto = params.objTrResidenciaAeropuerto;
				var objTrTasaAeroportuariaIda = params.objTrTasaAeroportuariaIda;
				var objTrTasaAeroportuariaVuelta = params.objTrTasaAeroportuariaVuelta;
				var objTrObservaciones = params.objTrObservaciones;
				
				var objectsIdaVuelta = getTipoTransporteObjects(objTrIdaVuelta);
				var objectsAeropuertoResidencia = getTipoTransporteObjects(objTrAeropuertoResidencia);
				var objectsResidenciaAeropuerto = getTipoTransporteObjects(objTrResidenciaAeropuerto);
				var objectsTasaAeroportuariaIda = getTipoTransporteObjects(objTrTasaAeroportuariaIda);
				var objectsTasaAeroportuariaVuelta = getTipoTransporteObjects(objTrTasaAeroportuariaVuelta);
				var objectsObservaciones = getObservacionesRutaObjects(objTrObservaciones);
				
				var indexTipoTransporte = objSelectTipoTransporte.selectedIndex;
				var idTipoTransporte = objSelectTipoTransporte.options[indexTipoTransporte].value;

				if(idTipoTransporte != 0){
					var tipo = tipoTransportes[idTipoTransporte].tipo;
					if (tipo == TIPO_AEREO) {
						objectsIdaVuelta['tr'].show();
						objectsAeropuertoResidencia['tr'].show();
						objectsResidenciaAeropuerto['tr'].show();
						objectsTasaAeroportuariaIda['tr'].show();
						objectsTasaAeroportuariaVuelta['tr'].show();
						objectsObservaciones['tr'].show();
					} else {
						objectsIdaVuelta['inputCheckbox'].checked = false;
						objectsIdaVuelta['inputHidden'].value = 'false';
						objectsIdaVuelta['tr'].hide();

						objectsAeropuertoResidencia['inputCheckbox'].checked = false;
						objectsAeropuertoResidencia['inputHidden'].value = 'false';
						objectsAeropuertoResidencia['tr'].hide();

						objectsResidenciaAeropuerto['inputCheckbox'].checked = false;
						objectsResidenciaAeropuerto['inputHidden'].value = 'false';
						objectsResidenciaAeropuerto['tr'].hide();

						objectsTasaAeroportuariaIda['inputCheckbox'].checked = false;
						objectsTasaAeroportuariaIda['inputHidden'].value = 'false';
						objectsTasaAeroportuariaIda['tr'].hide();

						objectsTasaAeroportuariaVuelta['inputCheckbox'].checked = false;
						objectsTasaAeroportuariaVuelta['inputHidden'].value = 'false';
						objectsTasaAeroportuariaVuelta['tr'].hide();

						objectsObservaciones['tr'].hide();
						objectsObservaciones['textarea'].empty();
						objectsObservaciones['textarea'].val('');
					}
				} else {
					objectsIdaVuelta['inputCheckbox'].checked = false;
					objectsIdaVuelta['inputHidden'].value = 'false';
					objectsIdaVuelta['tr'].hide();

					objectsAeropuertoResidencia['inputCheckbox'].checked = false;
					objectsAeropuertoResidencia['inputHidden'].value = 'false';
					objectsAeropuertoResidencia['tr'].hide();

					objectsResidenciaAeropuerto['inputCheckbox'].checked = false;
					objectsResidenciaAeropuerto['inputHidden'].value = 'false';
					objectsResidenciaAeropuerto['tr'].hide();

					objectsTasaAeroportuariaIda['inputCheckbox'].checked = false;
					objectsTasaAeroportuariaIda['inputHidden'].value = 'false';
					objectsTasaAeroportuariaIda['tr'].hide();

					objectsTasaAeroportuariaVuelta['inputCheckbox'].checked = false;
					objectsTasaAeroportuariaVuelta['inputHidden'].value = 'false';
					objectsTasaAeroportuariaVuelta['tr'].hide();

					objectsObservaciones['tr'].hide();
					objectsObservaciones['textarea'].empty();
					objectsObservaciones['textarea'].val('');
				}
			}

			function calcularTasaAeroportuaria()
			{
				var callCalcularTotal = (arguments[0] == true || arguments[0] == false) ? arguments[0] : true;
				
				var idTotalUnidades = 'totalUnidadTasaAeroportuaria';
				var nameUnidad = 'tipoTransporte\[\]';
				var idSubMonto = 'subMontoTasaAeroportuaria';
				var classTableRuta = 'wrapperRutas';
				var nameIdaVuelta = 'pasajeIdaVuelta\[\]';
				var tasaAeroportuariaIdaName = 'tasaAeroportuariaIda\[\]';
				var tasaAeroportuariaVueltaName = 'tasaAeroportuariaVuelta\[\]';				
				var codigoAsignacion = COD_TASA_AEROPORTUARIA;
				var totalUnidades = 0;

				var objTotalUnidades = $('#' + idTotalUnidades);
				var objSubMonto = $('#' + idSubMonto);

				$('.' + classTableRuta).each(function(){
					var objTable = $(this);
					var objSelectTipoTransporte = objTable.find('select[name="' + nameUnidad + '"]')[0];
					var objInputTasaAeroportuariaIda = objTable.find('input[name="' + tasaAeroportuariaIdaName + '"]');
					var objInputTasaAeroportuariaVuelta = objTable.find('input[name="' + tasaAeroportuariaVueltaName + '"]');
					
					if(objSelectTipoTransporte && objInputTasaAeroportuariaIda.length == 1
						&& objInputTasaAeroportuariaVuelta.length == 1
					){
						var indexTipoTransporte = objSelectTipoTransporte.selectedIndex;
						var idTipoTransporte = objSelectTipoTransporte.options[indexTipoTransporte].value;

						if(idTipoTransporte != 0){
							var tipo = tipoTransportes[idTipoTransporte].tipo;
							
							if (tipo == TIPO_AEREO) {

								if(objInputTasaAeroportuariaIda.val() == 'true' || objInputTasaAeroportuariaVuelta.val() == 'true')
									totalUnidades++;

								var objInputIdaVuelta = objTable.find('input[name="' + nameIdaVuelta + '"]')[0];

								if(objInputIdaVuelta && objInputIdaVuelta.value == 'true' 
									&& objInputTasaAeroportuariaIda.val() == 'true' && objInputTasaAeroportuariaVuelta.val() == 'true'
								)
									totalUnidades++;
							}							
						}
					}
				});

				objTotalUnidades.empty();
				objTotalUnidades.append(totalUnidades);
				
				objSubMonto.empty(totalUnidades);
				objSubMonto.append(totalUnidades * montoAsignaciones[codigoAsignacion]);

				if(callCalcularTotal) calcularTotal();
			}

			function changePasajeIdaVuelta(params)
			{
				var objTrIdaVuelta = params.objTrIdaVuelta;
				var objTrAeropuertoResidencia = params.objTrAeropuertoResidencia;
				var objTrResidenciaAeropuerto = params.objTrResidenciaAeropuerto;
				var objTrTasaAeroportuariaIda = params.objTrTasaAeroportuariaIda;
				var objTrTasaAeroportuariaVuelta = params.objTrTasaAeroportuariaVuelta;
				
				var objectsIdaVuelta = getTipoTransporteObjects(objTrIdaVuelta);
				var objectsAeropuertoResidencia = getTipoTransporteObjects(objTrAeropuertoResidencia);
				var objectsResidenciaAeropuerto = getTipoTransporteObjects(objTrResidenciaAeropuerto);
				var objectsTasaAeroportuariaIda = getTipoTransporteObjects(objTrTasaAeroportuariaIda);
				var objectsTasaAeroportuariaVuelta = getTipoTransporteObjects(objTrTasaAeroportuariaVuelta);

				if(objectsIdaVuelta['inputCheckbox'].checked == false){
					objectsAeropuertoResidencia['inputCheckbox'].checked = false;
					objectsAeropuertoResidencia['inputHidden'].value = 'false';
	
					objectsResidenciaAeropuerto['inputCheckbox'].checked = false;
					objectsResidenciaAeropuerto['inputHidden'].value = 'false';

					objectsTasaAeroportuariaIda['inputCheckbox'].checked = false;
					objectsTasaAeroportuariaIda['inputHidden'].value = 'false';

					objectsTasaAeroportuariaVuelta['inputCheckbox'].checked = false;
					objectsTasaAeroportuariaVuelta['inputHidden'].value = 'false';
				}
			}

			function changeTransporteARRA(params)
			{
				var objTrIdaVuelta = params.objTrIdaVuelta;
				var objTrTransporte1 = params.objTrTransporte1;
				var objTrTransporte2 = params.objTrTransporte2;

				var objectsIdaVuelta = getTipoTransporteObjects(objTrIdaVuelta);
				var objectsTransporte1 = getTipoTransporteObjects(objTrTransporte1);
				var objectsTransporte2 = getTipoTransporteObjects(objTrTransporte2);

				if(objectsIdaVuelta['inputCheckbox'].checked == false &&
						objectsTransporte1['inputCheckbox'].checked == true){
					objectsTransporte2['inputCheckbox'].checked = false;
					objectsTransporte2['inputHidden'].value = 'false';
				}
			}

			function changeTasaAeroporturiaIdaVuelta(params)
			{
				var objTrIdaVuelta = params.objTrIdaVuelta;
				var objTrTasaAeroportuaria1 = params.objTrTasaAeroportuaria1;
				var objTrTasaAeroportuaria2 = params.objTrTasaAeroportuaria2;

				var objectsIdaVuelta = getTipoTransporteObjects(objTrIdaVuelta);
				var objectsTasaAeroportuaria1 = getTipoTransporteObjects(objTrTasaAeroportuaria1);
				var objectsTasaAeroportuaria2 = getTipoTransporteObjects(objTrTasaAeroportuaria2);

				if(objectsIdaVuelta['inputCheckbox'].checked == false &&
						objectsTasaAeroportuaria1['inputCheckbox'].checked == true){
					objectsTasaAeroportuaria2['inputCheckbox'].checked = false;
					objectsTasaAeroportuaria2['inputHidden'].value = 'false';
				}
			}

			function calcularAeropuertoResidencia()
			{
				var callCalcularTotal = (arguments[0] == true || arguments[0] == false) ? arguments[0] : true;
				
				var idTotalUnidades = 'totalUnidadAeropuertoResidencia';
				var nameUnidad = 'aeropuertoResidencia\[\]';
				var idSubMonto = 'subMontoAeropuertoResidencia';
				var codigoAsignacion = COD_AEROPUERTO_RESIDENCIA;
				var totalUnidades = 0;

				var objTotalUnidades = $('#' + idTotalUnidades);
				var objSubMonto = $('#' + idSubMonto);
				
				$('input[name="' + nameUnidad +'"]').each(function(){
					objUnidad = this;
					if(objUnidad.value == 'true'){
						totalUnidades++;
					}
					
				});

				objTotalUnidades.empty();
				objTotalUnidades.append(totalUnidades);
				
				objSubMonto.empty(totalUnidades);
				objSubMonto.append(totalUnidades * montoAsignaciones[codigoAsignacion]);

				if(callCalcularTotal) calcularTotal();
			}

			function calcularResidenciaAeropuerto()
			{
				var callCalcularTotal = (arguments[0] == true || arguments[0] == false) ? arguments[0] : true;
				
				var idTotalUnidades = 'totalUnidadResidenciaAeropuerto';
				var nameUnidad = 'residenciaAeropuerto\[\]';
				var idSubMonto = 'subMontoResidenciaAeropuerto';
				var codigoAsignacion = COD_RESIDENCIA_AEROPUERTO;
				var totalUnidades = 0;

				var objTotalUnidades = $('#' + idTotalUnidades);
				var objSubMonto = $('#' + idSubMonto);
				
				$('input[name="' + nameUnidad +'"]').each(function(){
					objUnidad = this;
					if(objUnidad.value == 'true'){
						totalUnidades++;
					}
					
				});

				objTotalUnidades.empty();
				objTotalUnidades.append(totalUnidades);
				
				objSubMonto.empty(totalUnidades);
				objSubMonto.append(totalUnidades * montoAsignaciones[codigoAsignacion]);

				if(callCalcularTotal) calcularTotal();
			}

			function calcularTotal()
			{
				var objTMontoHospedaje = $('#tMontoHospedaje');
				var objTotalMonto = $('#totalMonto');

				var objSubtotales = new Array();
				objSubtotales[0] = $('#tMontoHospedaje');
				objSubtotales[1] = $('#tMontoAlimentacion');
				objSubtotales[2] = $('#subMontoTransporteInterurbano');
				objSubtotales[3] = $('#subMontoResidenciaAeropuerto');
				objSubtotales[4] = $('#subMontoAeropuertoResidencia');
				objSubtotales[5] = $('#subMontoTasaAeroportuaria');
				objSubtotales[6] = $('#subMontoServicioComunicaciones');
				objSubtotales[7] = $('#subMontoAsignacionTransporte');
				objSubtotales[8] = $('#subMontoTransporteExtraurbano');
				objSubtotales[9] = $('#subMontoTransporteCiudades');
								
				var total = 0;
				$.each(objSubtotales, function(index, objSubtotal){
						total += parseFloat(objSubtotal.text()); 
				});
				
				objTotalMonto.empty();
				objTotalMonto.append(total);	
			}

			function guardarYEnviar(objAccion)
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea registrar y enviar este vi"+aACUTE+"tico nacional? ")) 
				{
					desativarBotones();
					$('#' + objAccion).val('guardarYEnviar');
					$('#viaticoNacionalForm').submit();
				}
			}
			
			function desativarBotones()
			{
				$('#btnMultiple').attr('disabled', 'disabled');
				$('#btnGuardarYEnviar').attr('disabled', 'disabled');
			}

			function accionGuardar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea registrar este vi"+aACUTE+"tico nacional? ")) 
				{
					desativarBotones();
					var objForm = $('#viaticoNacionalForm');
					objForm.submit();
				}
			}

			function accionActualizar(){
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea modificar este vi"+aACUTE+"tico nacional? ")) 
				{
					desativarBotones();
					var objForm = $('#viaticoNacionalForm');
					objForm.submit();
				}
			}

			function accionActualizarYEnviar(objAccion)
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea modificar y enviar este vi"+aACUTE+"tico nacional? ")) 
				{
					desativarBotones();
					$('#' + objAccion).val('actualizarYEnviar');
					$('#viaticoNacionalForm').submit();
				}
			}

			function changeCategoria()
			{
				var objSelectCategoria = $('#selectCategoria');
				var objLabelRed = $('#labelRed');
				var objSelectRed = $('#selectRed');

				if(objSelectCategoria.length > 0){
					var indexCategoria = objSelectCategoria[0].selectedIndex;
					var idCategoria = objSelectCategoria[0].options[indexCategoria].value;
					
					if(idCategoria == '2'){
						objLabelRed.show();
						objSelectRed.show();
					} else {
						objLabelRed.hide();
						objSelectRed.hide();
					}
				}
			}

			function openWindow (path) {
				var options= " toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=yes," +
					" width=508, height=365, top=85, left=140";
				window.open(path, "Nombre ventana", options);
			}
		</script>
	</head>
	<body class="normal" onload="onLoad();">
		<form name="viaticoNacionalForm" id="viaticoNacionalForm" method="post" action="viaticonacional.php">
			<input
				id="hiddenAccion"
				type="hidden"
				name="accion"
				value="<?php 
					if($form->GetTipoOperacion() == ViaticoNacionalForm::TIPO_OPERACION_INSERTAR){
						echo 'guardar';
					} else if ($form->GetTipoOperacion() == ViaticoNacionalForm::TIPO_OPERACION_MODIFICAR){
						echo 'actualizar';
					}
				?>"
			>
			<?php 
				if ($form->GetTipoOperacion() == ViaticoNacionalForm::TIPO_OPERACION_MODIFICAR){
					echo '
						<input
							type="hidden"
							name="idViatico"
							value="'.$form->GetIdViatico().'"
						>
					';
				}
			?>
			<table align="center">
				<tr>
					<td><?php include(SAFI_VISTA_PATH . '/mensajes.php');?></td>
				</tr>
				<tr>
					<td><table
						align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas content" width="800px;"
						cellpadding="0" cellspacing="0"
						>
						<tr>
							<td colspan="2" class="header normalNegroNegrita documentTitle">.: Vi&aacute;tico nacional <?php 
								if ($form->GetTipoOperacion() == ViaticoNacionalForm::TIPO_OPERACION_MODIFICAR)
								{
									echo $form->GetIdViatico()." ";
								}
							?>:.</td>
						</tr>
						<tr>
							<td>Fecha del vi&aacute;tico</td>
							<td class="normalNegro">
								<!-- Descomentar para obligar a que el viático tenga la fecha del día que se generó -->
								<!-- 
								<input
									type="text"
									size="10"
									id="txt_viatico"
									class="normalNegro"
									readonly="readonly"
									value="<?php
										//$fecha = explode(' ', $form->GetFechaViatico());
										//echo $fecha[0];
									?>"
								/>
								 -->
								<!-- Descomentar para que se pueda elegir la fecha del viático --> 
								<input
									type="text"
									size="10"
									id="txt_viatico"
									name="fechaViatico"
									class="dateparse"
									readonly="readonly"
									value="<?php
										$fecha = explode(' ', $form->GetFechaViatico());
										echo $fecha[0];
									?>"
								/>
								<a 
									href="javascript:void(0);" 
									onclick="g_Calendar.show(event, 'txt_viatico');" 
									title="Show popup calendar"
								><img 
										src="../../js/lib/calendarPopup/img/calendar.gif" 
										class="cp_img" 
										alt="Open popup calendar"
								/></a>
								<!-- Fin de Descomentar para que se pueda elegir la fecha del viático -->
							</td>
						</tr>
						<tr>
							<td>Estado(*)</td>
							<td>
								<select id="estado" name="estado" class="normalNegro">
									<option value="0">Seleccionar...</option>
									<?php
										$estados = $GLOBALS['SafiRequestVars']['estados'];
										$idEstadoActual = 
													$form->GetEstado() != null ? $form->GetEstado()->GetId() : null;
										if(is_array($estados)){
											foreach($estados as $estado){
												if($estado->GetId() != 25){
													$selected = '';
													if(	$idEstadoActual != null && 
														strcmp($idEstadoActual, $estado->GetId()) == 0
													){
														$selected = 'selected="selected"';
													}
													echo '
														<option
															value="'.$estado->GetId().'"
															'.$selected.'
														>'.$estado->GetNombre().'</option>';
												}
											}
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2"><table style="width: 100%;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width: 20%">Categor&iacute;a(*)
										<a
											href="viaticonacional.php?accion=VerCategoriaViaticoInfo"
											onclick="openWindow(this.href); return false;"
										>
										(M&aacute;s Informaci&oacute;n)
										</a>
									</td>
									<td style="width: 30%">
										<select id="selectCategoria" name="categoria" class="normalNegro" onchange="changeCategoria();">
											<option value="0">Seleccionar...</option>
											<?php
												$categorias = $GLOBALS['SafiRequestVars']['categorias'];
												$idCategoriaActual = 
													$form->GetCategoriaViatico() != null ? $form->GetCategoriaViatico()->GetId() : null; 
												if(is_array($categorias)){
													foreach($categorias as $categoria){
														$selected = '';
														if(	$idCategoriaActual != null && 
															strcmp($idCategoriaActual, $categoria->GetId()) == 0
														){
															$selected = 'selected="selected"';
														}
														
														echo '
															<option
																value="'.$categoria->GetId().'"
																'.$selected.'
															>
																'.$categoria->GetNombre().'
															</option>
														';
													}
												}
												if(strcmp($idCategoriaActual, "2") != 0){
													$display = 'display: none';
												} else $display = '';
											?>
										</select>
									</td>
									<td style="width: 20%";><span id="labelRed" style="<?php echo $display?>">Red</span></td>
									<td style="width: 30%";>
										<select id="selectRed" name="red" class="normalNegro" style="<?php echo $display?>">
											<option value="0">Selecionar...</option>
											<?php 
												$redes = $GLOBALS['SafiRequestVars']['redes'];
												$idRedActual = 
													$form->GetRed() != null ? $form->GetRed()->GetId() : null;
												if(is_array($redes)){
													foreach($redes as $red){
														$selected = '';
														if($idRedActual != null && strcmp($idRedActual, $red->GetId()) == 0){
															$selected = 'selected="selected"';
														}
														echo '
															<option
																value="'.$red->GetId().'"
																'.$selected.'
															>
																'.$red->GetNombre().'
															</option>
														';
													}
												}
											?>
										</select>
									</td>
								</tr>
							</table></td>
						</tr>
						<tr>
							<td colspan="2" class="header normalNegroNegrita">Datos del responsable</td>
						</tr>
						<tr>
							<td>Tipo(*)</td>
							<td>
								<input
									id="inputTipoEmpleado"
									name="tipoBusquedaResponsable"
									type="radio"
									value="empleado"
									onclick="javascript:mostrarResponsables('empleado', 'divInputResponsable', 'inputResponsable', 'btnAdd', true);"
								>
									Empleado &nbsp;
								<input
									id="inputTipoBeneficiario"
									name="tipoBusquedaResponsable"
									type="radio"
									value="beneficiario"
									onclick="javascript:mostrarResponsables('beneficiario', 'divInputResponsable', 'inputResponsable', 'btnAdd', true);"
								>
									Beneficiario
							</td>
						</tr>
						<tr>
							<td>Responsable(*)</td>
							<td>
								<div id="divInputResponsable" style="float: left;">
									<input
										<?php echo 'autocomplete="off"' ?>
										size="70"
										type="text"
										id="inputResponsable"
										name="inputResponsable"
										value=""
										class="normalNegro"
									/>
								</div>
								<input id="btnAdd" type="button" value="Agregar" class="normalNegro">
							 </td>
						</tr>
						<!-- 
						<tr>
							<td colspan="2">
								<input
									name="responsable2"
									type="radio"
									onclick="javascript:mostrarResponsables2({tipo: 'empleado', inputResponsables: 'inputResponsables2', focus: true});"
									checked="checked"
								>
									Empleado &nbsp;
								<input
									name="responsable2"
									type="radio"
									onclick="javascript:mostrarResponsables2({tipo: 'beneficiario', inputResponsables: 'inputResponsable2', focus: true});"
								>
									Beneficiario
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="text" id="inputResponsables2" style="width: 500px;"></input>
							</td>
						</tr>
						 -->
						<tr>
							<td colspan="2">
								<table style="width: 100%;">
									<tbody id="tbodyInfoResponsable">
										<tr id="trheadInfoResponsable">
											<td width="60px">C&eacute;dula</td>
											<td width="400px">Nombre</td>
											<td width="120px">Tipo</td>
											<td width="60px">Opci&oacute;n</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td id="tdDatosCuentaBancaria" colspan="2"></td>
						</tr>
						<tr>
							<td colspan="2"><table>
								<tbody class="">
									<tr>
										<td>Nro. cuenta</td>
										<td>
											<input 
												type="text"
												id="numeroCuenta"
												name="numeroCuenta"
												class="normalNegro"
												value="<?php echo $form->GetResponsable()->GetNumeroCuenta()?>"
											>
										</td>
										<td>&nbsp;&nbsp;Tipo cuenta</td>
										<td>
											<select id="tipoCuenta" name="tipoCuenta" class="normalNegro">
												<option value="0">Seleccionar...</option>
												<option
													value="<?php echo EntidadTipoCuentabancaria::CUENTA_DE_AHORRO?>"
													<?php
														echo (strcasecmp($form->GetResponsable()->GetTipoCuenta(), "A") == 0) ? 
															' selected="selected"' : '';
													?>
												>Ahorro</option>
												<option
													value="<?php echo EntidadTipoCuentabancaria::CUENTA_CORRRIENTE?>"
													<?php
														echo (strcasecmp($form->GetResponsable()->GetTipoCuenta(), "C") == 0) ? 
															' selected="selected"' : '';
													?>
												>Corriente</option>
											</select>
										</td>
										<td>&nbsp;&nbsp;Banco</td>
										<td>
											<input 
												type="text"
												id="banco"
												name="banco"
												class="normalNegro"
												value="<?php echo $form->GetResponsable()->GetBanco()?>"
											>
										</td>
									</tr>
								</tbody>
							</table></td>
						</tr>
						<tr>
							<td colspan="2" class="header normalNegroNegrita">Proyecto/Acci&oacute;n centralizada</td>
						</tr>
						<tr>
							<td>Tipo(*)</td>
							<td class="normalNegro">
								<input
									id="inputTipoProyecto"
									type="radio"
									name="tipoProyectoAccionCentralizada"
									value="proyecto"
									onclick="javascript:mostrarProyectoAccionCentralizada({tipo: 'proyecto'});"
									<?php 
										if ($form->GetTipoOperacion() == ViaticoNacionalForm::TIPO_OPERACION_MODIFICAR){
											//echo 'disabled="disabled"';
										} else {
											//echo 'onclick="javascript:mostrarProyectoAccionCentralizada({tipo: \'proyecto\'});"';
										}
									?>
								>
									Proyecto &nbsp;
								<input
									id="inputTipoAccionCentralizada"
									type="radio"
									name="tipoProyectoAccionCentralizada"
									value="accionCentralizada"
									onclick="javascript:mostrarProyectoAccionCentralizada({tipo: 'accionCentralizada'});"
									<?php 
										if ($form->GetTipoOperacion() == ViaticoNacionalForm::TIPO_OPERACION_MODIFICAR){
											//echo 'disabled="disabled"';
										} else {
											//echo 'onclick="javascript:mostrarProyectoAccionCentralizada({tipo: \'accionCentralizada\'});"';
										}
									?>
								>
									Acci&oacute;n centralizada
							</td>
						</tr>
						<tr>
							<td>Proyecto/Acci&oacute;n centralizada(*)</td>
							<td>
								<select
									id="proyectoAccionCentralizada"
									name="proyectoAccionCentralizada"
									class="normalNegro"
									style="width: 660px;"
									<?php 
										if ($form->GetTipoOperacion() == ViaticoNacionalForm::TIPO_OPERACION_MODIFICAR){
											//echo 'disabled="disabled"';
										}
									?>
								>
									<option value="0">Seleccionar...</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>Acci&oacute;n espec&iacute;fica(*)</td>
							<td>
								<select
									id="accionEspecifica"
									name="accionEspecifica"
									class="normalNegro"
									style="width: 660px;"
									<?php 
										if ($form->GetTipoOperacion() == ViaticoNacionalForm::TIPO_OPERACION_MODIFICAR){
											//echo 'disabled="disabled"';
										}
									?>
								>
									<option value="0">Seleccionar...</option>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="header normalNegroNegrita">Datos del viaje</td>
						</tr>
						<tr>
							<td colspan="2">
								Fecha inicio del viaje(*)
								<input
									type="text"
									id="txt_inicio"
									name="fechaInicioViaje"
									size="10"
									class="dateparse"
									onfocus="javascript: compararFechasYBorrarById('txt_inicio', 'hid_hasta_itin', 'txt_inicio');"
									readonly="readonly"
									value="<?php echo $form->GetFechaInicioViaje()?>"
								/>
								<a 
									href="javascript:void(0);" 
									onclick="g_Calendar.show(event, 'txt_inicio');" 
									title="Show popup calendar"
								><img 
										src="../../js/lib/calendarPopup/img/calendar.gif" 
										class="cp_img" 
										alt="Open popup calendar"
								/></a>
								&nbsp;
								&nbsp;
								&nbsp;
								Fecha fin del viaje(*)
								<input
									type="text"
									id="hid_hasta_itin"
									name="fechaFinViaje"
									size="10"
									class="dateparse"
									onfocus="javascript: compararFechasYBorrarById('txt_inicio', 'hid_hasta_itin', 'hid_hasta_itin');"
									readonly="readonly"
									value="<?php echo $form->GetFechaFinViaje()?>"
								/>
								<a href="javascript:void(0);" 
									onclick="g_Calendar.show(event, 'hid_hasta_itin');" 
									title="Show popup calendar"
								>
									<img 
										src="../../js/lib/calendarPopup/img/calendar.gif" 
										class="cp_img" 
										alt="Open popup calendar"
									/>
								</a>
							</td>
						</tr>
						<tr>
							<td>Objetivos del viaje(*)</td>
							<td>
								<textarea
									name="objetivosViaje"
									class="normalNegro"
									rows="2"
									cols="70"
								><?php echo $form->GetObjetivosViaje()?></textarea>
							</td>
						</tr>
						<tr>
							<td colspan="2">Infocentros</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<input
									id="inputSelectInfocentros"
									class="normalNegro"
									style="width: 500px;"
								>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<div class="listaDiamante">
									<ul id="ulListaInfocentro"></ul>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="header normalNegroNegrita">Rutas</td>
						</tr>
						<tr>
							<td colspan="2"><hr/></td>
						</tr>
						<tr>
							<td id="tdRutas" colspan="2"></td>
						</tr>
						<tr>
							<td colspan="2">
								<input
									type="button"
									value="Nueva ruta"
									class="normalNegro"
									onclick="javascript:agregarRuta();"
								>
							</td>
						</tr>
						<tr>
							<td colspan="2"><table cellpadding="0" cellspacing="0" class="tablaalertas content" style="width: 100%;">
								<tr>
									<td class="header normalNegroNegrita">Asignaci&oacute;n</td>
									<td class="header normalNegroNegrita">Monto</td>
									<td class="header normalNegroNegrita">Unidad de medida</td>
									<td class="header normalNegroNegrita">Unidades</td>
									<td class="header normalNegroNegrita">Subtotal</td>
								</tr>
								<?php
									$totalMonto = 0;
									
									$asignaciones = $GLOBALS['SafiRequestVars']['asignaciones'];
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_HOSPEDAJE];
									$viaticoRespAsig = $form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_HOSPEDAJE);
									$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
									echo '
										<tr>
											<td>' . $asignacion->GetNombre() . '</td>
											<td>' . $viaticoRespAsig->GetMonto() . '</td>
											<td>Por noche</td>
											<td id="diasHospedaje">'. $viaticoRespAsig->GetUnidades() .'</td>
											<td id="tMontoHospedaje">'.
												($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades())
											.'</td>
										</tr>
									';
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_ALIMENTACION];
									$viaticoRespAsig = $form->GetViaticoResponsableAsignacion(EntidadAsignacionViatico::COD_ALIMENTACION);
									$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
									echo '
										<tr>
											<td>' . $asignacion->GetNombre() . '</td>
											<td>' . $viaticoRespAsig->GetMonto() . '</td>
											<td>Diario</td>
											<td id="diasAlimentacion">'. $viaticoRespAsig->GetUnidades() .'</td>
											<td id="tMontoAlimentacion">'.
												($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades())
											.'</td>
										</tr>
									';
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_TRANSPORTE_INTERURBANO];
									$viaticoRespAsig = $form->GetViaticoResponsableAsignacion(
										EntidadAsignacionViatico::COD_TRANSPORTE_INTERURBANO);
									$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
									echo '
										<tr>
											<td>' . $asignacion->GetNombre() . '</td>
											<td>' . $viaticoRespAsig->GetMonto() . '</td>
											<td>Diario</td>
											<td id="totalUnidadTransporteInterurbano">'. $viaticoRespAsig->GetUnidades() .'</td>
											<td id="subMontoTransporteInterurbano">'.
												($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades())
											.'</td>
										</tr>
									';
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_RESIDENCIA_AEROPUERTO];
									$viaticoRespAsig = $form->GetViaticoResponsableAsignacion(
										EntidadAsignacionViatico::COD_RESIDENCIA_AEROPUERTO);
									$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
									echo '
										<tr>
											<td>' . $asignacion->GetNombre() . '</td>
											<td>' . $viaticoRespAsig->GetMonto() . '</td>
											<td>Por traslado</td>
											<td id="totalUnidadResidenciaAeropuerto">'. $viaticoRespAsig->GetUnidades() .'</td>
											<td id="subMontoResidenciaAeropuerto">'.
												($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades())
											.'</td>
										</tr>
									';
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_AEROPUERTO_RESIDENCIA];
									$viaticoRespAsig = $form->GetViaticoResponsableAsignacion(
										EntidadAsignacionViatico::COD_AEROPUERTO_RESIDENCIA);
									$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
									echo '
										<tr>
											<td>' . $asignacion->GetNombre() . '</td>
											<td>' . $viaticoRespAsig->GetMonto() . '</td>
											<td>Por traslado</td>
											<td id="totalUnidadAeropuertoResidencia">'. $viaticoRespAsig->GetUnidades() .'</td>
											<td id="subMontoAeropuertoResidencia">'.
												($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades())
											.'</td>
										</tr>
									';
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_TASA_AEROPORTUARIA];
									$viaticoRespAsig = $form->GetViaticoResponsableAsignacion(
										EntidadAsignacionViatico::COD_TASA_AEROPORTUARIA);
									$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
									echo '
										<tr>
											<td>' . $asignacion->GetNombre() . '</td>
											<td>' . $viaticoRespAsig->GetMonto() . '</td>
											<td>Por viaje</td>
											<td id="totalUnidadTasaAeroportuaria">'. $viaticoRespAsig->GetUnidades() .'</td>
											<td id="subMontoTasaAeroportuaria">'.
												($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades())
											.'</td>
										</tr>
									';
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_SERVICIO_COMUNICACIONES];
									$viaticoRespAsig = $form->GetViaticoResponsableAsignacion(
										EntidadAsignacionViatico::COD_SERVICIO_COMUNICACIONES);
									$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
									echo '
										<tr>
											<td>' . $asignacion->GetNombre() . '</td>
											<td>' . $viaticoRespAsig->GetMonto() . '</td>
											<td>Por viaje</td>
											<td>
												<input
													id="unidadServicioComunicaciones"
													name="unidadServicioComunicaciones"
													type="text"
													class="normalNegro"
													value="'.$viaticoRespAsig->GetUnidades().'"
													style="width: 50px;"
													onkeyup="calcularServicioComunicaciones();"
												>
											</td>
											<td id="subMontoServicioComunicaciones">'.
												($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades())
											.'</td>
										</tr>
									';
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_ASIGNACION_TRANSPORTE];
									$viaticoRespAsig = $form->GetViaticoResponsableAsignacion(
										EntidadAsignacionViatico::COD_ASIGNACION_TRANSPORTE);
									$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
									echo '
										<tr>
											<td>' . $asignacion->GetNombre() . '</td>
											<td>' . $viaticoRespAsig->GetMonto() . '</td>
											<td>Por Km</td>
											<td>
												<input
													id="unidadAsignacionTransporte"
													name="unidadAsignacionTransporte"
													type="text"
													class="normalNegro"
													value="'.$viaticoRespAsig->GetUnidades().'"
													style="width: 50px;"
													onkeyup="calcularAsignacionTransporte();"
												>
											</td>
											<td id="subMontoAsignacionTransporte">'.
												($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades())
											.'</td>
										</tr>
									';
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_TRANSPORTE_EXTRAURBANO];
									$viaticoRespAsig = $form->GetViaticoResponsableAsignacion(
										EntidadAsignacionViatico::COD_TRANSPORTE_EXTRAURBANO);
									$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
									echo '
										<tr>
											<td>' . $asignacion->GetNombre() . '</td>
											<td>
												<input
													type="text"
													id="montoTransporteExtraurbano"
													name="montoTransporteExtraurbano"
													class="normalNegro"
													style="width: 100px;"
													onkeyup="calcularTransporteExtraurbano();"
													value="'.$viaticoRespAsig->GetMonto().'"
												>
											</td>
											<td>Diario</td>
											<td>
												<input
													id="unidadTransporteExtraurbano"
													name="unidadTransporteExtraurbano"
													type="text"
													class="normalNegro"
													value="'.$viaticoRespAsig->GetUnidades().'"
													style="width: 50px;"
													onkeyup="calcularTransporteExtraurbano();"
												>
											</td>
											<td id="subMontoTransporteExtraurbano">'.
												($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades())
											.'</td>
										</tr>
									';
									
									$asignacion = $asignaciones[EntidadAsignacionViatico::COD_TRANSPORTE_ENTRE_CIUDADES];
									$viaticoRespAsig = $form->GetViaticoResponsableAsignacion(
										EntidadAsignacionViatico::COD_TRANSPORTE_ENTRE_CIUDADES);
									$totalMonto += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
									echo '
										<tr>
											<td>' . $asignacion->GetNombre() . '</td>
											<td>
												<input
													type="text"
													id="montoTransporteCiudades"
													name="montoTransporteCiudades"
													style="width: 100px;"
													class="normalNegro"
													value="'.$viaticoRespAsig->GetMonto().'"
													onkeyup="calcularTransporteCiudades();"
												>
											</td>
											<td>Por viaje</td>
											<td>
												<input
													id="unidadTransporteCiudades"
													name="unidadTransporteCiudades"
													type="text"
													class="normalNegro"
													value="'.$viaticoRespAsig->GetUnidades().'"
													style="width: 50px;"
													onkeyup="calcularTransporteCiudades();"
												>
											</td>
											<td id="subMontoTransporteCiudades">'.
												($viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades())
											.'</td>
										</tr>
									';
								?>
								<tr>
									<td colspan="3"></td>
									<td>Total</td>
									<td id="totalMonto"><?php echo $totalMonto ?></td>
								</tr>
							</table></td>
						</tr>
						<tr>
							<td colspan="2" class="header normalNegroNegrita">Observaciones</td>
						</tr>
						<tr>
							<td colspan="2">
								<textarea
									name="observaciones"
									class="normalNegro"
									rows="3"
									cols="80"
								><?php echo $form->GetObservaciones()?></textarea>
							</td>
						</tr>
					</table></td>
				</tr>
				<tr>
					<td class="normal"><div class="ptotal"><span class="peq_naranja">(*)</span> Campo obligatorio</div></td>
				</tr>
			</table>
			<br/>
			<div id="divAcciones" style="text-align: center;">
				<input
					id="btnMultiple"
					type="button"
					class="normalNegro"
					<?php 
						if($form->GetTipoOperacion() == ViaticoNacionalForm::TIPO_OPERACION_INSERTAR){
							echo 'value="Registrar"
								onclick="accionGuardar();"';
						} else if ($form->GetTipoOperacion() == ViaticoNacionalForm::TIPO_OPERACION_MODIFICAR){
							echo 'value="Modificar"
								onclick="accionActualizar();"
							';
						}
					?>
				>
				<input
					id="btnGuardarYEnviar"
					type="button"
					class="normalNegro"
					<?php 
						if($form->GetTipoOperacion() == ViaticoNacionalForm::TIPO_OPERACION_INSERTAR){
							echo 'value="Registrar y enviar"
								onclick="guardarYEnviar(\'hiddenAccion\');"';
						} else if ($form->GetTipoOperacion() == ViaticoNacionalForm::TIPO_OPERACION_MODIFICAR){
							echo 'value="Modificar y enviar"
								onclick="accionActualizarYEnviar(\'hiddenAccion\');"
							';
						}
					?>
				>
			</div>
		</form>
	</body>
</html>