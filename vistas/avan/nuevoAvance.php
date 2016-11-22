<?php
	$form = FormManager::GetForm(FORM_NUEVO_AVANCE);
	$formUTF8 = clone $form;
	$formUTF8->UTF8Encode();
	
	$avance = $form->GetAvance();
	
	$TIPO_PROYECTO = EntidadProyectoAccionCentralizada::TIPO_PROYECTO;
	$TIPO_ACCION_CENTRALIZADA = EntidadProyectoAccionCentralizada::TIPO_ACCION_CENTRALIZADA;
	
	$categorias = $GLOBALS['SafiRequestVars']['categorias'];
	$redes = $GLOBALS['SafiRequestVars']['redes'];
	$estados = $GLOBALS['SafiRequestVars']['estados'];
?>
<html>
	<head>
		<title>.:SAFI:. Ingresar Avance</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		<link href="../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />
		<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
		<style>
			.nombreResponsable{
				background-color: inherit;
			}
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
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript" src="../../js/constantes.js"></script>
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<script>
			<?php
				$estadosJson = array();
				foreach ($estados as $estado)
				{
					$estado->UTF8Encode();
					$estadosJson[$estado->Getid()] =  "\"" . $estado->GetId() . "\": ". $estado->ToJson();
				}
				
				echo "
			var formAvance = " . $formUTF8->ToJson() . ";
			var estados = { ".implode(", ", $estadosJson)." };
			var avance = (formAvance != null) ? ((formAvance.avance) ? formAvance.avance  : null) : null;
				";
			?>
			
			var responsableIdSeq = 0;
			g_Calendar.setDateFormat('dd/mm/yyyy');

			function onLoad()
			{
				location.href = "#mensajes";

				// Variables de los infocentros
				var objInputInfocentros = $("#inputSelectInfocentros");
				var idUlInfocentros = "ulListaInfocentro";
				var sendInputNameInfocentros = "infocentros[]";
				// Variables del punto de cuenta
				var objInputPuntoCuenta = $("#idPuntoCuenta");

				// Cargar los datos de los proyectos o acciones centralizadas
				cargarProyectoAccionCentralizada({tipo: avance.tipoProyectoAccionCentralizada});

				if(avance.responsablesAvancePartidas != null)
				{
					$.each(avance.responsablesAvancePartidas, function(indexResponsable, objResponsableAvancePartidas){
						agregarResponsable({objResponsableAvancePartidas: objResponsableAvancePartidas});
					});

					calcularTodosSubtotalesResponsables();
					calcularTotalResponsable();
				}
				
				var countRutas = 0;
				if(avance.rutasAvance != null){
					$.each(avance.rutasAvance, function(indexRuta, objRutaAvance){
						agregarRuta({rutaAvance: objRutaAvance});
						countRutas++;
					});
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
				if(avance.infocentros != null){
					$.each(avance.infocentros, function(idInfocentro, objInfocentro){
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

				// Configurar el autocomplete del punto de cuenta
				objInputPuntoCuenta.autocomplete({
					source: function(request, response){
						$.ajax({
							url: "../puntoCuenta.php",
							dataType: "json",
							data: {
								accion: "Search",
								key: request.term,
								tipoRespuesta: 'json'
							},
							success: function(json){
								var index = 0;
								var items = new Array();

								$.each(json.listaPuntoCuenta, function(idPuntoCuenta, objPuntoCuenta){

									var label = objPuntoCuenta.id;
									
									items[index++] = {
											id: objPuntoCuenta.id,
											label: label,
											value: objPuntoCuenta.id
									};
								});
								response(items);
							}
						});
					},
					minLength: 1,
					select: function(event, ui)
					{
						objInputPuntoCuenta.val(ui.item.value);
						return false;
					}
				});
			}

			function cargarResponsables(params){

				var tipo = params['tipo'];
				var objInputResponsables = params['objInputResponsables'];
				var objInputTipoEmpleado = params['objInputTipoEmpleado'];
				var objInputTipoBeneficiario = params['objInputTipoBeneficiario'];
				var focus = params['focus'];
				var cedulasResponsablesName = params['cedulasResponsablesName'];
				var tiposResponsablesName = params['tiposResponsablesName'];
				
				if(tipo == '<?php echo EntidadResponsable::TIPO_EMPLEADO ?>'){
					autoCompleteResponsables({
						tipo: tipo, 
						objInputResponsables: objInputResponsables,
						focus: focus,
						cedulasResponsablesName: cedulasResponsablesName,
						tiposResponsablesName: tiposResponsablesName
					});
					objInputTipoEmpleado.attr('checked', 'checked');
				} else if(tipo == '<?php echo EntidadResponsable::TIPO_BENEFICIARIO ?>'){
					autoCompleteResponsables({
						tipo: tipo,
						objInputResponsables: objInputResponsables,
						focus: focus,
						cedulasResponsablesName: cedulasResponsablesName,
						tiposResponsablesName: tiposResponsablesName
					});
					objInputTipoBeneficiario.attr('checked', 'checked');
				}
			}

			function autoCompleteResponsables(params)
			{
				var tipo = params['tipo'];
				var objInputResponsables = params['objInputResponsables'];
				var cedulasResponsablesName = params['cedulasResponsablesName'];
				var tiposResponsablesName = params['tiposResponsablesName'];
				var focus = params['focus'];

				if(cedulasResponsablesName != null){
					cedulasResponsablesName.replace("[", "\[");
					cedulasResponsablesName.replace("]", "\]");
				}
				if(tiposResponsablesName != null){
					tiposResponsablesName.replace("[", "\[");
					tiposResponsablesName.replace("]", "\]");
				}
				
				var objTable = objInputResponsables.parents(".wrapperResponsablesAvance");
				var objNombreResponsable = objTable.find(".nombreResponsable");
				var objCedulaResponsable = objTable.find(".cedulaResponsable");
				var objInputHiddenCedulas = objTable.find("input[name='"+cedulasResponsablesName+"']");
				var objInputHiddenTiposResponsables = objTable.find("input[name='"+tiposResponsablesName+"']");
				
				var numItems = 10;
				
				objInputResponsables.val('');
				//objInputResponsables.autocomplete("destroy");
				
				if(tipo == '<?php echo EntidadResponsable::TIPO_EMPLEADO?>'){
					objInputResponsables.autocomplete({
						source: function(request, response){
							$.ajax({
								url: "../empleado.php",
								dataType: "json",
								data: {
									accion: "Search",
									tipoRespuesta: 'json',
									key: request.term,
									numItems: numItems
								},
								success: function(json){
									var index = 0;
									var items = new Array();
		
									$.each(json.listaempleado, function(cedula, objEmpleado){
										var value = objEmpleado.cedula + ' : ' + objEmpleado.nombres.toUpperCase() + ' ' + objEmpleado.apellidos.toUpperCase();
										items[index++] = {
												id: cedula,
												label: value,
												value: value,
												tipo: tipo,
												nombres: objEmpleado.nombres.toUpperCase(),
												apellidos: objEmpleado.apellidos.toUpperCase()
										};
									});
									response(items);
								}
							});
						},
						minLength: 1,
						select: function(event, ui)
						{
							objNombreResponsable.empty();
							objCedulaResponsable.empty();

							objNombreResponsable.append(document.createTextNode(ui.item.nombres + " " + ui.item.apellidos));
							objCedulaResponsable.append(document.createTextNode(ui.item.id));
							objInputHiddenCedulas.val(ui.item.id);
							objInputHiddenTiposResponsables.val(ui.item.tipo);
							
							objInputResponsables.val('');
							return false;
						}
					});
				} else if (tipo == '<?php echo EntidadResponsable::TIPO_BENEFICIARIO?>') {
					objInputResponsables.autocomplete({
						source: function(request, response){
							$.ajax({
								url: "../beneficiarioviatico.php",
								dataType: "json",
								data: {
									accion: "Search",
									tipoRespuesta: 'json',
									key: request.term,
									numItems: numItems
								},
								success: function(json){
									var index = 0;
									var items = new Array();
		
									$.each(json.listabeneficiarioviatico, function(cedula, objBeneficiario){
										var value = objBeneficiario.id + ' : ' + objBeneficiario.nombres.toUpperCase() + ' ' + objBeneficiario.apellidos.toUpperCase();
										items[index++] = {
												id: objBeneficiario.id,
												label: value,
												value: value,
												tipo: tipo,
												nombres: objBeneficiario.nombres.toUpperCase(),
												apellidos: objBeneficiario.apellidos.toUpperCase(),
												tipoEmpleado: objBeneficiario.tipo
										};
									});
									response(items);
								}
							});
						},
						minLength: 1,
						select: function(event, ui)
						{
							objNombreResponsable.empty();
							objCedulaResponsable.empty();

							objNombreResponsable.append(document.createTextNode(ui.item.nombres + " " + ui.item.apellidos));
							objCedulaResponsable.append(document.createTextNode(ui.item.id));
							objInputHiddenCedulas.val(ui.item.id);
							objInputHiddenTiposResponsables.val(ui.item.tipo);
							
							objInputResponsables.val('');
							return false;
						}
					});
				}

				if(focus){
					objInputResponsables.focus();
				}
			}

			function cargarProyectoAccionCentralizada(params){
				var tipo = params['tipo'];

				if(tipo == '<?php echo $TIPO_PROYECTO ?>'){
					mostrarProyectoAccionCentralizada({
						tipo: tipo
					});
					$('#inputTipoProyecto').attr('checked', 'checked');
				} else if(tipo == '<?php echo $TIPO_ACCION_CENTRALIZADA ?>'){
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
				
				if(tipo == '<?php echo $TIPO_PROYECTO ?>'){
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
				} else if (tipo == '<?php echo $TIPO_ACCION_CENTRALIZADA ?>') {
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

					if (avance != null && avance.proyecto != null && avance.proyecto.id == objProyecto.id.idproyecto){
						objOption.setAttribute('selected', 'selected');
						loadAccionEspecifica = true;
					}
					
					objOption.appendChild(document.createTextNode(objProyecto.nombre));
					objSelectProyectoAccionCentralizada.append(objOption);
				});

				objSelectProyectoAccionCentralizada = $(objSelectProyectoAccionCentralizada);
				objSelectProyectoAccionCentralizada.unbind('change');
				objSelectProyectoAccionCentralizada.bind('change', function(event){
					changeAccionesEspecificasBy('<?php echo $TIPO_PROYECTO ?>', 
							objSelectProyectoAccionCentralizada[0], objSelectAccionEspecifica[0]);
				});
				if(loadAccionEspecifica){
					changeAccionesEspecificasBy('<?php echo $TIPO_PROYECTO ?>', 
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

					if (avance != null && avance.accionCentralizada != null
						&& avance.accionCentralizada.id == objAccion.id.idaccioncentralizada
					) {
						objOption.setAttribute('selected', 'selected');
						loadAccionEspecifica = true;
					}
					
					objOption.appendChild(document.createTextNode(objAccion.nombre));
					objSelectProyectoAccionCentralizada.append(objOption);
				});

				objSelectProyectoAccionCentralizada = $(objSelectProyectoAccionCentralizada);
				objSelectProyectoAccionCentralizada.unbind('change');
				objSelectProyectoAccionCentralizada.bind('change', function(event){
					changeAccionesEspecificasBy('<?php echo $TIPO_ACCION_CENTRALIZADA ?>', 
							objSelectProyectoAccionCentralizada[0], objSelectAccionEspecifica[0]);
				});
				if(loadAccionEspecifica){
					changeAccionesEspecificasBy('<?php echo $TIPO_ACCION_CENTRALIZADA ?>', 
						objSelectProyectoAccionCentralizada[0], objSelectAccionEspecifica[0]);
				}
			}

			function changeAccionesEspecificasBy(tipo, objProyectoAccionCentralizada, objAccionEspecifica){

				$(objAccionEspecifica).find('option[value!="0"]').remove();
				
				var indexProyectoAccionCentralizada = objProyectoAccionCentralizada.selectedIndex;
				var idProyectoAccionCentralizada = 
						objProyectoAccionCentralizada.options[indexProyectoAccionCentralizada].value;

				if(idProyectoAccionCentralizada != 0){
					if(tipo == '<?php echo $TIPO_PROYECTO ?>'){
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

									if (avance != null && avance.proyectoEspecifica != null
										&& avance.proyectoEspecifica.id == objAccion.id.idaccionespecifica
									){
										objOption.setAttribute('selected', 'selected');
									}
									
									objOption.appendChild(document.createTextNode(label));
									objSelectAccionEspecifica.append(objOption);
								});
							}
						});	
					} 
					else if(tipo == '<?php echo $TIPO_ACCION_CENTRALIZADA ?>') {
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

									if (avance != null && avance.accionCentralizadaEspecifica != null
											&& avance.accionCentralizadaEspecifica.id == objAccion.id.idaccionespecifica
										){
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

			function agregarResponsable()
			{
				responsableIdSeq++;

				var params = arguments[0] || null;
				var objResponsableAvancePartidas = params != null ? (params.objResponsableAvancePartidas || null) : null;
				var focus = params != null ? (params.focus || null) : null;

				var cedulaResponsable = "";
				var nombreResponsable = "";
				var tipoResponsable = "";
				var idEstadoSelected = "";
				var numeroCuenta = "";
				var tipoCuenta = "";
				var banco = "";
				var objAvancePartidas = null;
				
				if(objResponsableAvancePartidas != null)
				{
					if(objResponsableAvancePartidas.responsableAvance != null){
						var objResponsableAvance = objResponsableAvancePartidas.responsableAvance;
					
						if(
							objResponsableAvance.tipoResponsable == '<?php echo EntidadResponsable::TIPO_EMPLEADO?>'
							&& objResponsableAvance.empleado != null
						){
							tipoResponsable = objResponsableAvance.tipoResponsable;
							var objEmpleado = objResponsableAvance.empleado;
							cedulaResponsable = objEmpleado.id;
							nombreResponsable = objEmpleado.nombres.toUpperCase() + ' ' + objEmpleado.apellidos.toUpperCase();
						} else if(
							objResponsableAvance.tipoResponsable == '<?php echo EntidadResponsable::TIPO_BENEFICIARIO?>'
							&& objResponsableAvance.beneficiario != null
						){
							tipoResponsable = objResponsableAvance.tipoResponsable;
							var objBeneficiario = objResponsableAvance.beneficiario;
							cedulaResponsable = objBeneficiario.id;
							nombreResponsable = objBeneficiario.nombres.toUpperCase() + ' ' + objBeneficiario.apellidos.toUpperCase(); 
						}
						
						if(objResponsableAvance.estado != null){
							idEstadoSelected = objResponsableAvance.estado.id; 
						}
	
						if(objResponsableAvance.numeroCuenta != null){
							numeroCuenta = objResponsableAvance.numeroCuenta; 
						}
	
						if(objResponsableAvance.numeroCuenta != null){
							tipoCuenta = objResponsableAvance.tipoCuenta; 
						}
	
						if(objResponsableAvance.banco != null){
							banco = objResponsableAvance.banco; 
						}
					}

					if(objResponsableAvancePartidas.avancePartidas != null)
					{
						objAvancePartidas = objResponsableAvancePartidas.avancePartidas;
					}
				}
				
				var tipoResponsableName = "tipoResponsable_"+responsableIdSeq;
				var correlativosResponsablesName = "correlativosResponsables[]";
				var tiposResponsablesName = "tiposResponsables[]";
				var cedulasResponsablesName = "cedulasResponsables[]";
				var estadosResponsablesName = "estadosResponsables[]";
				var nrosCuentasResponsablesName = "nrosCuentasResponsables[]";
				var tiposCuentasResponsablesName = "tiposCuentasResponsables[]";
				var bancosResponsablesName = "bancosResponsables[]";
				var partidasName = "partidas[" + responsableIdSeq + "][]";
				var partidasMontosName = "partidasMontos[" + responsableIdSeq + "][]";
				
				var tdResponsables = "tdResponsables";
				var objTdResponsables = $('#' + tdResponsables);

				var objTable = document.createElement('table');
				objTable.setAttribute('class', 'wrapperResponsablesAvance');

				var objTbody = document.createElement('tbody');
				objTable.appendChild(objTbody);

				// tr input tipo responsable, autocompletar responsable 
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);

				var objTd = document.createElement('td');
				objTr.appendChild(objTd);

				// span con texto responsable
				var objSpan = document.createElement('span');
				objSpan.setAttribute('class', 'normalNegrita');
				objSpan.appendChild(document.createTextNode('Responsable: '));
				objTd.appendChild(objSpan);

				// input radio tipo empleado
				var objInputTipoEmpleado = document.createElement('input');
				objInputTipoEmpleado.setAttribute('type', 'radio');
				objInputTipoEmpleado.setAttribute('name', tipoResponsableName);
				objTd.appendChild(objInputTipoEmpleado);

				objTd.appendChild(document.createTextNode(' Empleado '));

				// input radio tipo beneficiario
				var objInputTipoBeneficiario = document.createElement('input');
				objInputTipoBeneficiario.setAttribute('type', 'radio');
				objInputTipoBeneficiario.setAttribute('name', tipoResponsableName);
				objTd.appendChild(objInputTipoBeneficiario);

				objTd.appendChild(document.createTextNode(' Beneficiario\u00A0\u00A0'));

				// input text autocompletar responsable
				var objInputResponsables = document.createElement('input');
				objInputResponsables.setAttribute('type', 'text');
				objInputResponsables.setAttribute('class', 'normalNegro autocompletarResponsable');
				objTd.appendChild(objInputResponsables);

				// tr datos del responsable 
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);

				var objTd = document.createElement('td');
				objTr.appendChild(objTd);

				// table de datos del responsable
				var objTableSub = document.createElement('table');
				objTableSub.setAttribute('class', 'tableSub');
				objTableSub.setAttribute('cellspacing', '0');
				objTableSub.setAttribute('cellpadding', '0');
				objTd.appendChild(objTableSub);

				var objTbodySub = document.createElement('tbody');
				objTableSub.appendChild(objTbodySub);

				// tr de datos de responsable: nombre, cédula, estado
				var objTrSub = document.createElement('tr');
				objTbodySub.appendChild(objTrSub);

				// td de etiqueta de nombre del responsable
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode('Nombre:'));
				objTrSub.appendChild(objTdSub);

				// td de nombre del responsable
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'nombreResponsable');
				objTdSub.appendChild(document.createTextNode(nombreResponsable));
				objTrSub.appendChild(objTdSub);

				// td de etiqueta de cédula del responsable
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode('C'+eACUTE+'dula:'));
				objTrSub.appendChild(objTdSub);

				// td de cédula del responsable
				var objTdSub = document.createElement('td');
				objTrSub.appendChild(objTdSub);

				var objInputHidden = document.createElement('input');
				objInputHidden.setAttribute('type', 'hidden');
				objInputHidden.setAttribute('name', correlativosResponsablesName);
				objInputHidden.setAttribute('value', responsableIdSeq);
				objTdSub.appendChild(objInputHidden);

				var objInputHidden = document.createElement('input');
				objInputHidden.setAttribute('type', 'hidden');
				objInputHidden.setAttribute('name', tiposResponsablesName);
				objInputHidden.setAttribute('value', tipoResponsable);
				objTdSub.appendChild(objInputHidden);
				
				var objInputHidden = document.createElement('input');
				objInputHidden.setAttribute('type', 'hidden');
				objInputHidden.setAttribute('name', cedulasResponsablesName);
				objInputHidden.setAttribute('value', cedulaResponsable);
				objTdSub.appendChild(objInputHidden);

				var objSpan = document.createElement("span");
				objSpan.setAttribute("class", "cedulaResponsable");
				objSpan.appendChild(document.createTextNode(cedulaResponsable));

				objTdSub.appendChild(objSpan);

				// td de etiqueta de estado del responsable
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode('Estado(*):'));
				objTrSub.appendChild(objTdSub);

				// td de etiqueta de estado del responsable
				var objTdSub = document.createElement('td');
				objTrSub.appendChild(objTdSub);

				// select de estado
				var objSelectEstados = document.createElement('select');
				objSelectEstados.setAttribute('name', estadosResponsablesName);
				objSelectEstados.setAttribute('class', 'normalNegro');
				objTdSub.appendChild(objSelectEstados);
				
				var objOption = document.createElement('option');
				objOption.value = "0";
				objOption.appendChild(document.createTextNode('Seleccionar...'));
				objSelectEstados.appendChild(objOption);
				
				$.each(estados, function(idEstado, objEstado){
					var objOption = document.createElement('option');
					objOption.appendChild(document.createTextNode(objEstado.nombre));
					objOption.value = idEstado;
					if(idEstadoSelected == idEstado){
						objOption.setAttribute('selected', 'selected');
					}
					objSelectEstados.appendChild(objOption);
				});
				
				// tr de datos de responsable: número de cuenta, tipo de cuenta, banco
				var objTrSub = document.createElement('tr');
				objTbodySub.appendChild(objTrSub);

				// td de etiqueta del número de cuenta del responsable
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');				
				objTdSub.appendChild(document.createTextNode('Nro. cuenta:'));
				objTrSub.appendChild(objTdSub);

				// td de input número de cuenta
				var objTdSub = document.createElement('td');				
				objTrSub.appendChild(objTdSub);
				
				// input text número de cuenta
				var objInput = document.createElement('input');
				objInput.setAttribute('type', 'text');
				objInput.setAttribute('name', nrosCuentasResponsablesName);
				objInput.setAttribute('class', 'normalNegro');
				objInput.setAttribute('value', numeroCuenta);
				objTdSub.appendChild(objInput);
				
				// td de etiqueta de tipo de cuenta
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');				
				objTdSub.appendChild(document.createTextNode('Tipo cuenta:'));
				objTrSub.appendChild(objTdSub);
				
				// td de select tipo cuenta
				var objTdSub = document.createElement('td');
				objTrSub.appendChild(objTdSub);

				// select tipo cuenta
				var objSelect = document.createElement('select');
				objSelect.setAttribute('name', tiposCuentasResponsablesName);
				objSelect.setAttribute('class', 'normalNegro');
				objTdSub.appendChild(objSelect);

				objOption = document.createElement('option');
				objOption.appendChild(document.createTextNode('Seleccionar...'));
				objOption.value = 0;
				objSelect.appendChild(objOption);

				objOption = document.createElement('option');
				objOption.appendChild(document.createTextNode('Ahorro'));
				objOption.value = '<?php echo EntidadTipoCuentabancaria::CUENTA_DE_AHORRO?>';
				if(objOption.value == tipoCuenta){
					objOption.setAttribute('selected', 'selected');
				}
				objSelect.appendChild(objOption);

				objOption = document.createElement('option');
				objOption.appendChild(document.createTextNode('Corriente'));
				objOption.value = '<?php echo EntidadTipoCuentabancaria::CUENTA_CORRRIENTE?>';
				if(objOption.value == tipoCuenta){
					objOption.setAttribute('selected', 'selected');
				}
				objSelect.appendChild(objOption);				
				
				// td de etiqueta de banco
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode('Banco:'));
				objTrSub.appendChild(objTdSub);
				
				// td de input banco
				var objTdSub = document.createElement('td');
				objTrSub.appendChild(objTdSub);
				
				// input text banco
				var objInput = document.createElement('input');
				objInput.setAttribute('type', 'text');
				objInput.setAttribute('name', bancosResponsablesName);
				objInput.setAttribute('class', 'normalNegro');
				objInput.setAttribute('value', banco);
				objTdSub.appendChild(objInput);

				// tr datos datos de las partidas por responsable
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);

				var objTd = document.createElement('td');
				objTr.appendChild(objTd);
				
				// table de datos de las partidas por responsable
				var objTableSub = document.createElement('table');
				objTableSub.setAttribute('class', 'tablePartidasMontos tableSub');
				objTableSub.setAttribute('cellspacing', '0');
				objTableSub.setAttribute('cellpadding', '0');
				objTd.appendChild(objTableSub);

				var objTbodySub = document.createElement('tbody');
				objTableSub.appendChild(objTbodySub);

				// tr de etiquetas de partida y monto
				var objTrSub = document.createElement('tr');
				objTbodySub.appendChild(objTrSub);

				// td de etiqueta de partidas de la izquierda
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode('Partida'));
				objTrSub.appendChild(objTdSub);

				// td de etiqueta de montos de la izquierda
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode('Monto'));
				objTrSub.appendChild(objTdSub);

				// td de etiqueta de partidas de la derecha
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode('Partida'));
				objTrSub.appendChild(objTdSub);

				// td de etiqueta de montos de la derecha
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode('Monto'));
				objTrSub.appendChild(objTdSub);

				// td de boton de agregar tupla partida/monto
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'botonesPartidasYMontos');
				objTrSub.appendChild(objTdSub);

				// Div de boton agregar tupla partida/monto
				var objDiv = document.createElement('div');
				objDiv.setAttribute('class', 'botonAgregarPartidasYMontos');
				objDiv.setAttribute('title', 'Agregar una fila de partidas/montos.');
				objTdSub.appendChild(objDiv);

				objDiv = $(objDiv);
				objDiv.unbind('click');
				objDiv.bind('click', {objTbodySub: objTbodySub},function(event)
				{
					agregarFilaPartidasMontos({
						objTbodySub: event.data.objTbodySub,
						objTable: objTable,
						partidasName: partidasName,
						partidasMontosName: partidasMontosName,
						arrObjAvancePartida: null
					});
				});

				var existePartidasMonto = false;
				// Agregar todos los datos de partidas/montos presentes, para el responsable actual del avance 
				if(objAvancePartidas != null)
				{
					var indexElement = 0;
					var lstObjAvancePartida = null;

					$(objAvancePartidas).each(function (index, objAvancePartida)
					{
						existePartidasMonto = true;
						
						if(indexElement % 2 == 0)
						{
							arrObjAvancePartida = [objAvancePartida];

							if((objAvancePartidas.length-1) == indexElement)
							{
								agregarFilaPartidasMontos({
									objTbodySub: objTbodySub,
									objTable: objTable,
									partidasName: partidasName,
									partidasMontosName: partidasMontosName,
									arrObjAvancePartida: arrObjAvancePartida
								});
							}
						}
						else
						{
							arrObjAvancePartida[1] = objAvancePartida;

							agregarFilaPartidasMontos({
								objTbodySub: objTbodySub,
								objTable: objTable,
								partidasName: partidasName,
								partidasMontosName: partidasMontosName,
								arrObjAvancePartida: arrObjAvancePartida
							});
						}
						indexElement++;
					});
				}

				if(!existePartidasMonto)
				{
					agregarFilaPartidasMontos({
						objTbodySub: objTbodySub,
						objTable: objTable,
						partidasName: partidasName,
						partidasMontosName: partidasMontosName,
						arrObjAvancePartida: null
					});
				}

				// Tr Subtotal
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);

				var objTd = document.createElement('td');
				objTd.setAttribute('class', 'subtotal');
				objTr.appendChild(objTd);

				// Subtotal
				objSpan = document.createElement('span');
				objSpan.appendChild(document.createTextNode('Subtotal:'+NON_BREAKING_SPACE));
				objTd.appendChild(objSpan);

				objSpanSubtotal = document.createElement('span');
				objSpanSubtotal.setAttribute('class', 'montoSubtotal');
				objSpanSubtotal.appendChild(document.createTextNode('0.0'));
				objTd.appendChild(objSpanSubtotal);
				
				// Tr link eliminar
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);

				var objTd = document.createElement('td');
				objTd.setAttribute('class', 'footer');
				objTr.appendChild(objTd);
				
				// Link eliminar
				var objA = document.createElement('a');
				objA.setAttribute('href', 'javascript:void(0);');
				objA.appendChild(document.createTextNode('Eliminar responsable'));
				objTd.appendChild(objA);

				objA = $(objA);
				objA.unbind('click');
				objA.bind('click', function(){					
					countResponsables = objTdResponsables.find(">table").length;
					if(countResponsables > 1){
						$(objTable).remove();
						calcularTotalResponsable();
					} else {
						alert("No se puede eliminar. Debe existir al menos un responsable.");
					}
				});

				objInputResponsables = $(objInputResponsables);
				objInputTipoEmpleado = $(objInputTipoEmpleado);
				objInputTipoBeneficiario = $(objInputTipoBeneficiario);
				
				// Cargar los datos de los responsable
				cargarResponsables({
					tipo: '<?php echo EntidadResponsable::TIPO_EMPLEADO?>',
					objInputResponsables: $(objInputResponsables),
					objInputTipoEmpleado: objInputTipoEmpleado,
					objInputTipoBeneficiario: objInputTipoBeneficiario,
					focus: false,
					cedulasResponsablesName: cedulasResponsablesName,
					tiposResponsablesName: tiposResponsablesName
				});
				
				objInputTipoEmpleado.unbind('click');
				objInputTipoEmpleado.bind('click', {objInputResponsables: objInputResponsables}, function(event){
					autoCompleteResponsables({
						tipo: '<?php echo EntidadResponsable::TIPO_EMPLEADO?>',
						objInputResponsables: event.data.objInputResponsables,
						focus: true,
						cedulasResponsablesName: cedulasResponsablesName,
						tiposResponsablesName: tiposResponsablesName
					});
				});

				objInputTipoBeneficiario = $(objInputTipoBeneficiario);
				objInputTipoBeneficiario.unbind('click');
				objInputTipoBeneficiario.bind('click', {objInputResponsables: objInputResponsables}, function(event){
					autoCompleteResponsables({
						tipo: '<?php echo EntidadResponsable::TIPO_BENEFICIARIO?>',
						objInputResponsables: event.data.objInputResponsables,
						focus: true,
						cedulasResponsablesName: cedulasResponsablesName,
						tiposResponsablesName: tiposResponsablesName
					});
				});

				objTdResponsables.append(objTable);
				if(focus){
					objInputResponsables.focus();
				}
			}

			function agregarFilaPartidasMontos()
			{
				var params = arguments[0] || null;
				var objTbodySub = params != null ? (params.objTbodySub || null) : null;
				var objTable = params != null ? (params.objTable || null) : null;
				var partidasName = params != null ? (params.partidasName || null) : null;
				var partidasMontosName = params != null ? (params.partidasMontosName || null) : null;
				var arrObjAvancePartida = params != null ? (params.arrObjAvancePartida || null) : null;

				if(params == null) return;
				if(partidasName == null) return;
				if(partidasMontosName == null) return;

				// tr de id's de partidas y valores de monto
				var objTrSub = document.createElement('tr');
				objTbodySub.appendChild(objTrSub);
				
				// Contruir la partida/monto
				agregarPartidaMonto({
					objAvancePartida: arrObjAvancePartida != null ? arrObjAvancePartida[0] : null,
					objTable: objTable,
					objTrSub: objTrSub,
					partidasName: partidasName,
					partidasMontosName: partidasMontosName
				});

				// Contruir la partida/monto
				agregarPartidaMonto({
					objAvancePartida: arrObjAvancePartida != null ? arrObjAvancePartida[1] : null,
					objTable: objTable,
					objTrSub: objTrSub,
					partidasName: partidasName,
					partidasMontosName: partidasMontosName
				});

				// Td de Div de boton eliminar tupla partida/monto
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'botonesPartidasYMontos');
				objTrSub.appendChild(objTdSub);

				// Div de boton eliminar tupla partida/monto
				var objDiv = document.createElement('div');
				objDiv.setAttribute('class', 'botonEliminarPartidasYMontos');
				objDiv.setAttribute('title', 'Eliminar esta fila de partidas/montos.');
				objTdSub.appendChild(objDiv);

				objDiv = $(objDiv);
				objDiv.unbind('click');
				objDiv.bind('click', {objContainer: objTbodySub, objTrSub: objTrSub},function(event){
					var objTrSub = $(event.data.objTrSub); 
					countDatosPartidasMontos = $(event.data.objContainer).find(">tr").length;
					if(countDatosPartidasMontos > 2){
						objTrSub.remove();
					} else {
						// Limpiar la información de las tuplas partida/monto de la fila actual
						objTrSub.find(
								'input[name="'+partidasName+'"], input[name="'+partidasMontosName+'"]'
						).val("");

					}
					calcularSubtotalResponsable(objTable);
					calcularTotalResponsable();
				});
			}

			function agregarPartidaMonto()
			{
				var params = arguments[0] || null;
				var objAvancePartida = params != null ? (params.objAvancePartida || null) : null;
				var objTable = params != null ? (params.objTable || null) : null;
				var objTrSub = params != null ? (params.objTrSub || null) : null;
				var partidasName = params != null ? (params.partidasName || null) : null;
				var partidasMontosName = params != null ? (params.partidasMontosName || null) : null;
				
				if(objTable == null) return;
				if(objTrSub == null) return;
				if(partidasName == null) return;
				if(partidasMontosName == null) return;
				
				// td de id de partida
				var objTdSub = document.createElement('td');
				objTrSub.appendChild(objTdSub);

				var idPartida = "";
				var montoPartida = "";
				
				if(objAvancePartida != null && objAvancePartida.partida != null){
					idPartida = objAvancePartida.partida.id;
					montoPartida = objAvancePartida.monto;
				}

				var objInput = document.createElement('input');
				objInput.setAttribute('class', 'normalNegro');
				objInput.setAttribute('type', "text");
				objInput.setAttribute('name', partidasName);
				objInput.setAttribute('value', idPartida);
				setAutocompletePartidas($(objInput));
				objTdSub.appendChild(objInput);

				// td de monto de la partida
				var objTdSub = document.createElement('td');
				objTrSub.appendChild(objTdSub);

				var objInput = document.createElement('input');
				objInput.setAttribute('class', 'normalNegro');
				objInput.setAttribute('name', partidasMontosName);
				objInput.setAttribute('value', montoPartida);
				objInput.setAttribute('autocomplete', 'off');
				objTdSub.appendChild(objInput);

				objInput = $(objInput);
				objInput.unbind('keyup');
				objInput.bind('keyup', {objInput: objInput[0]}, function(event){
					validarDecimal(event.data.objInput);
					calcularSubtotalResponsable(objTable);
					calcularTotalResponsable();
				});
			}
			
			function setAutocompletePartidas(objInputPartidas)
			{
				// Configurar autocomplete de partidas
				objInputPartidas.autocomplete({
					source: function(request, response){
						
						var seleccionados = new Array();
						
						objInputPartidas.parents('.tablePartidasMontos').find('input[type="text"][name^="partidas\["]').each(
							function(index, objInputPartida){
								var value = $.trim(objInputPartida.value);
								if(value != ''){
									seleccionados[index] = value;
								}
							}
						);
						
						$.ajax({
							url: "../partida.php",
							dataType: "json",
							data: {
								accion: "Search",
								key: request.term,
								tipoRespuesta: 'json',
								seleccionados: seleccionados
							},
							success: function(json){
								var index = 0;
								var items = new Array();

								$.each(json.listaPartida, function(idPartida, objPartida){

									var label = idPartida + " : " + objPartida.nombre;
									items[index++] = {
											id: idPartida,
											label: label,
											value: idPartida
									};
								});
								response(items);
							}
						});
					},
					minLength: 1,
					select: function(event, ui)
					{
						objInputPartidas.val(ui.item.value);
						return false;
					}
				});
			}

			function calcularTodosSubtotalesResponsables()
			{
				objTdResponsables = $('#tdResponsables');
				objListaTablesResponsables = objTdResponsables.find('.wrapperResponsablesAvance'); 

				objListaTablesResponsables.each(function(index, objTableResponsable){
					calcularSubtotalResponsable(objTableResponsable);
				});
			}

			function calcularSubtotalResponsable(objTableResponsable)
			{
				var objTableResponsable = $(objTableResponsable);
				var objListaMontosPartidas = objTableResponsable.find('input[name^="partidasMontos"]');
				var objMontoSubtotal = objTableResponsable.find(".montoSubtotal");

				var montoSubTotal = 0.0;

				objListaMontosPartidas.each(function(index, objMontoPartida){
					var montoPartida = parseFloat(objMontoPartida.value);
					montoPartida = (isNaN(montoPartida)) ? 0 : montoPartida;
					montoSubTotal += montoPartida;
				});
				
				objMontoSubtotal.empty();
				objMontoSubtotal.append(montoSubTotal);
			}

			function calcularTotalResponsable()
			{
				objTdResponsables = $('#tdResponsables');
				objMontoTotal = $('#montoTotal');
				objListaMontosSubtotales = objTdResponsables.find('.montoSubtotal');

				var montoTotal = 0.0;
				objListaMontosSubtotales.each(function(index, objMontoSubtotal){
					var montoSubtotal = parseFloat($(objMontoSubtotal).text());
					montoSubtotal = (isNaN(montoSubtotal)) ? 0 : montoSubtotal;
					montoTotal += montoSubtotal;
				});

				objMontoTotal.empty();
				objMontoTotal.append(montoTotal);
			}

			function agregarRuta()
			{
				var params = arguments[0] || null;
				var rutaAvance = params != null ? (params.rutaAvance || null) : null;
				var tdRutas = "tdRutas";
				var objTdRutas = $('#' + tdRutas);
				var idRutaName = 'idRutasAvance[]';
				var estadosName = 'estados[]';
				var ciudadesName = 'ciudades[]';
				var municipiosName = 'municipios[]';
				var parroquiasName = 'parroquias[]';
				var direccionesName = 'direcciones[]';

				var objTable = document.createElement('table');
				objTable.setAttribute('class', 'wrapperRutas');

				var objTbody = document.createElement('tbody');
				objTable.appendChild(objTbody);

				// tr etiquetas 
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);

				// td de etiqueta de estado
				var objTd = document.createElement('td');
				// Id de ruta
				var objInputText = document.createElement('input');
				objInputText.setAttribute('name', idRutaName);
				objInputText.setAttribute('type', 'hidden');
				if(rutaAvance != null){
					objInputText.value = rutaAvance.id;
				} else {
					objInputText.value = "0";
				}
				objTd.appendChild(objInputText);
				// Etiqueta de estado
				objTd.setAttribute('class', 'normalNegrita');
				objTd.appendChild(document.createTextNode('Estado(*)'));
				objTr.appendChild(objTd);

				// td de etiqueta de ciudad
				var objTd = document.createElement('td');
				objTd.setAttribute('class', 'normalNegrita');
				objTd.appendChild(document.createTextNode('Ciudad'));
				objTr.appendChild(objTd);

				// td de etiqueta de municipio
				var objTd = document.createElement('td');
				objTd.setAttribute('class', 'normalNegrita');
				objTd.appendChild(document.createTextNode('Municipio'));
				objTr.appendChild(objTd);

				// td de etiqueta de parroquia
				var objTd = document.createElement('td');
				objTd.setAttribute('class', 'normalNegrita');
				objTd.appendChild(document.createTextNode('Parroquia'));
				objTr.appendChild(objTd);

				// td de etiqueta de parroquia
				var objTd = document.createElement('td');
				objTd.setAttribute('class', 'normalNegrita');
				objTd.appendChild(document.createTextNode('Direcci'+oACUTE+'n'));
				objTr.appendChild(objTd);

				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);				
				
				// td de estado
				var objTd = document.createElement('td');
				objTd.setAttribute('class', 'normalNegro');
				objTr.appendChild(objTd);

				// select de estados
				var objSelectEstados = document.createElement('select');
				objSelectEstados.setAttribute('name', estadosName);
				objSelectEstados.setAttribute('class', 'normalNegro');
				objTd.appendChild(objSelectEstados);
				
				var objOption = document.createElement('option');
				objOption.value = "0";
				objOption.appendChild(document.createTextNode('Seleccionar...'));
				objSelectEstados.appendChild(objOption);
				
				$.each(estados, function(idEstado, objEstado){ 
					var objOption = document.createElement('option');
					objOption.appendChild(document.createTextNode(objEstado.nombre));
					objOption.value = idEstado;
					if(rutaAvance != null && rutaAvance.estado != null && rutaAvance.estado.id == idEstado){
						objOption.setAttribute('selected', 'selected');
					}
					objSelectEstados.appendChild(objOption);
				});

				// td de ciudades
				var objTd = document.createElement('td');
				objTd.setAttribute('class', 'normalNegro');
				objTr.appendChild(objTd);

				// select de ciudades
				var objSelectCiudades = document.createElement('select');
				objSelectCiudades.setAttribute('name', ciudadesName);
				objSelectCiudades.setAttribute('class', 'normalNegro');
				objTd.appendChild(objSelectCiudades);
				
				var objOption = document.createElement('option');
				objOption.value = "0";
				objOption.appendChild(document.createTextNode('Seleccionar...'));
				objSelectCiudades.appendChild(objOption);

				// td de municipios
				var objTd = document.createElement('td');
				objTd.setAttribute('class', 'normalNegro');
				objTr.appendChild(objTd);

				// select de municipios
				var objSelectMunicipios = document.createElement('select');
				objSelectMunicipios.setAttribute('name', municipiosName);
				objSelectMunicipios.setAttribute('class', 'normalNegro');
				objTd.appendChild(objSelectMunicipios);
				
				var objOption = document.createElement('option');
				objOption.value = "0";
				objOption.appendChild(document.createTextNode('Seleccionar...'));
				objSelectMunicipios.appendChild(objOption);

				// td de parroquias
				var objTd = document.createElement('td');
				objTd.setAttribute('class', 'normalNegro');
				objTr.appendChild(objTd);

				// select de parroquias
				var objSelectParroquias = document.createElement('select');
				objSelectParroquias.setAttribute('name', parroquiasName);
				objSelectParroquias.setAttribute('class', 'normalNegro');
				objTd.appendChild(objSelectParroquias);
				
				var objOption = document.createElement('option');
				objOption.value = "0";
				objOption.appendChild(document.createTextNode('Seleccionar...'));
				objSelectParroquias.appendChild(objOption);

				// td de dirección
				var objTd = document.createElement('td');
				objTd.setAttribute('class', 'normalNegro');
				objTr.appendChild(objTd);

				// textarea de dirección
				var objTextarea = document.createElement('textarea');
				objTextarea.setAttribute('name', direccionesName);
				objTextarea.setAttribute('rows', '1');
				objTextarea.setAttribute('cols', '15');
				if(rutaAvance != null){
					objTextarea.appendChild(document.createTextNode(rutaAvance.direccion));
				}
				objTd.appendChild(objTextarea);

				// Tr link eliminar
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);

				var objTd = document.createElement('td');
				objTd.setAttribute('colspan', '5');
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
					} else {
						alert("No se puede eliminar. Debe existir al menos una ruta.");
					}
				});

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
				if(rutaAvance != null && rutaAvance.estado != null && rutaAvance.estado.id != 0)
				{
					var idCiudad = (rutaAvance.ciudad != null) ? rutaAvance.ciudad.id : null;
					
					changeCiudadesByEstado({
						objEstados: objSelectEstados[0], objCiudades: objSelectCiudades[0], idCiudad: idCiudad 
					});

					var idMunicipio = (rutaAvance.municipio != null) ? rutaAvance.municipio.id : null;
					
					changeMunicipiosByEstado({
						objEstados: objSelectEstados[0],
						objMunicipios: objSelectMunicipios[0], 
						objParroquias: objSelectParroquias[0],
						idMunicipio: idMunicipio,
						async: false
					});
				}

				// Evento de la carga de parroquias dado un municipio
				objSelectMunicipios.unbind("change");
				objSelectMunicipios.bind("change", function(event){
					changeParroquiasByMunicipio({objMunicipios: objSelectMunicipios[0], objParroquias: objSelectParroquias[0]});
				});

				if(rutaAvance != null && rutaAvance.municipio != null && rutaAvance.municipio.id != 0)
				{
					idParroquia = (rutaAvance.parroquia != null) ? rutaAvance.parroquia.id : null;
					
					changeParroquiasByMunicipio({
						objMunicipios: objSelectMunicipios[0],
						objParroquias: objSelectParroquias[0],
						idParroquia: idParroquia
					});
				}
				
				objTdRutas.append(objTable);
		
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

			function desactivarBotones()
			{
				$('#btnMultiple').attr('disabled', 'disabled');
				$('#btnGuardarYEnviar').attr('disabled', 'disabled');
			}

			function accionGuardar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea registrar este avance? ")) 
				{
					desactivarBotones();
					var objForm = $('#avanceForm');
					objForm.submit();
				}
			}

			function guardarYEnviar(objAccion)
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea registrar y enviar este avance? ")) 
				{
					desactivarBotones();
					$('#' + objAccion).val('GuardarYEnviar');
					var objForm = $('#avanceForm');
					objForm.submit();
				}
			}
			
			function accionActualizar(){
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea modificar este avance? ")) 
				{
					desactivarBotones();
					var objForm = $('#avanceForm');
					objForm.submit();
				}
			}

			function accionActualizarYEnviar(objAccion)
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea modificar y enviar este avance? ")) 
				{
					desactivarBotones();
					$('#' + objAccion).val('ActualizarYEnviar');
					var objForm = $('#avanceForm');
					objForm.submit();
				}
			}
		</script>
	</head>
	<body class="normal" onload="onLoad();">
		<form id="avanceForm" action="avance.php" method="post">
			<input
				id="hiddenAccion"
				type="hidden"
				name="accion"
				value="<?php 
					if($form->GetTipoOperacion() == NuevoAvanceForm::TIPO_OPERACION_INSERTAR){
						echo 'guardar';
					} else if ($form->GetTipoOperacion() == NuevoAvanceForm::TIPO_OPERACION_MODIFICAR){
						echo 'actualizar';
					}
				?>"
			>
			<?php
			if ($form->GetTipoOperacion() == NuevoAvanceForm::TIPO_OPERACION_MODIFICAR){
				echo '
			<input
				type="hidden"
				name="idAvance"
				value="'.$avance->GetId().'"
			>
				';
			}
		?>
			<table align="center">
				<tr>
					<td><?php include(SAFI_VISTA_PATH . '/mensajes.php');?></td>
				</tr>
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" align="center" background="../../imagenes/fondo_tabla.gif"
							class="tablaalertas content" width="800px;"
						>
							<tr>
								<td colspan="2" class="normalNegroNegrita header documentTitle">.: Avance <?php 
								if (
									$form->GetTipoOperacion() == NuevoAvanceForm::TIPO_OPERACION_MODIFICAR
									&& $avance !== null
								){
									echo $avance->GetId() . " ";
								}
							?>:.</td>
							</tr>
							<!--
							<tr>
								<td class="normalNegrita">Fecha de registro</td>
								<td class="normalNegro"><?php echo $avance->GetFechaRegistro()?></td>
							</tr>
							 -->
							<tr>
								<td class="normalNegrita">Fecha del avance: </td>
								<td class="normalNegro">
									<input
										type="text"
										size="10"
										id="fechaAvance"
										name="fechaAvance"
										class="dateparse"
										readonly="readonly"
										value="<?php
											echo $avance->GetFechaAvance()
										?>"
									/>
									<!-- Descomentar para que se pueda elegir la fecha del avance -->
									<a
										href="javascript:void(0);"
										onclick="g_Calendar.show(event, 'fechaAvance');"
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
								<td class="normalNegrita">Punto de cuenta: </td>
								<td class="normalNegro">
									<input
										type="text"
										id="idPuntoCuenta"
										name="idPuntoCuenta"
										class="normalNegro"
										value="<?php
											echo $avance != null && $avance->GetPuntoCuenta() != null
												? $avance->GetPuntoCuenta()->GetId() : "";
										?>"
									/>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<table style="width: 100%;" cellpadding="0" cellspacing="0">
										<tr>
											<td class="normalNegrita" style="width: 25%">Categor&iacute;a(*):
												<a
													href="avance.php?accion=VerCategoriaAvanceInfo"
													onclick="openWindow(this.href); return false;"
												>
												(M&aacute;s Informaci&oacute;n)
												</a>
											</td>
											<td style="width: 35%">
												<select id="selectCategoria" name="categoria" class="normalNegro"
													onchange="changeCategoria();"
												>
													<option value="0">Seleccionar...</option>
													<?php
													$idCategoriaActual = 
														$avance->GetCategoria() != null ? $avance->GetCategoria()->GetId() : null;
													
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
											<td style="width: 5%";><span class="normalNegrita" id="labelRed" style="<?php
												echo $display
											?>">Red: </span></td>
											<td style="width: 35%";>
												<select id="selectRed" name="red" class="normalNegro" style="<?php echo $display?>">
													<option value="0">Selecionar...</option>
													<?php 
													
													$idRedActual = 
														$avance->GetRed() != null ? $avance->GetRed()->GetId() : null;
													
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
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="normalNegroNegrita header">Proyecto/Acci&oacute;n centralizada</td>
							</tr>
							<tr>
								<td colspan="2" style="text-align: center;">
									<span class="NormalNegrita">Tipo(*):</span>
									&nbsp;
									<input
										id="inputTipoProyecto"
										type="radio"
										name="tipoProyectoAccionCentralizada"
										value="<?php echo $TIPO_PROYECTO ?>"
										onclick="javascript:mostrarProyectoAccionCentralizada({tipo: '<?php echo $TIPO_PROYECTO ?>'});"
									>
										Proyecto &nbsp;
									<input
										id="inputTipoAccionCentralizada"
										type="radio"
										name="tipoProyectoAccionCentralizada"
										value="<?php echo $TIPO_ACCION_CENTRALIZADA ?>"
										onclick="
											javascript:
											mostrarProyectoAccionCentralizada({tipo: '<?php echo $TIPO_ACCION_CENTRALIZADA ?>'});
										"
									>
										Acci&oacute;n centralizada
								</td>
							</tr>
							<tr>
								<td colspan="2" class="normalNegrita">Proyecto/Acci&oacute;n centralizada(*):</td>
							</tr>
							<tr>
								<td colspan="2">
									<select
										id="proyectoAccionCentralizada"
										name="proyectoAccionCentralizada"
										class="normalNegro"
										style="margin-left: 10px; width: 775px;"
									>
										<option value="0">Seleccionar...</option>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="normalNegrita">Acci&oacute;n espec&iacute;fica(*):</td>
							</tr>
							<tr>
								<td colspan="2">
									<select
										id="accionEspecifica"
										name="accionEspecifica"
										class="normalNegro"
										style="margin-left: 10px; width: 775px;"
									>
										<option value="0">Seleccionar...</option>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="normalNegroNegrita header">Datos de la actividad</td>
							</tr>
							<tr>
								<td colspan="2" class="normalNegrita">
									Fecha inicio(*):
									<input
										type="text"
										id="txt_inicio"
										name="fechaInicioActividad"
										size="10"
										class="dateparse"
										onfocus="javascript: compararFechasYBorrarById('txt_inicio', 'hid_hasta_itin', 'txt_inicio');"
										readonly="readonly"
										value="<?php echo ($avance != null) ? $avance->GetFechaInicioActividad() : ""?>"
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
									Fecha fin(*):
									<input
										type="text"
										id="hid_hasta_itin"
										name="fechaFinActividad"
										size="10"
										class="dateparse"
										onfocus="javascript: compararFechasYBorrarById('txt_inicio', 'hid_hasta_itin', 'hid_hasta_itin');"
										readonly="readonly"
										value="<?php echo ($avance != null) ? $avance->GetFechaFinActividad() : ""?>"
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
								<td class="normalNegrita">Objetivos(*): </td>
								<td>
									<textarea
										name="objetivos"
										class="normalNegro"
										rows="2"
										style="width: 500px;"
									><?php echo ($avance != null) ? $avance->GetObjetivos() : "" ?></textarea>
								</td>
							</tr>
							<tr>
								<td class="normalNegrita">Descripci&oacute;n: </td>
								<td>
									<textarea
										name="descripcion"
										class="normalNegro"
										rows="2"
										style="width: 500px;"
									><?php echo ($avance != null) ? $avance->GetDescripcion() : "" ?></textarea>
								</td>
							</tr>
							<tr>
								<td class="normalNegrita">Justificaci&oacute;n: </td>
								<td>
									<textarea
										name="justificacion"
										class="normalNegro"
										rows="2"
										style="width: 500px;"
									><?php echo ($avance != null) ? $avance->GetJustificacion() : "" ?></textarea>
								</td>
							</tr>
							<tr>
								<td class="normalNegrita">Nro. participantes(*): </td>
								<td>
									<input
										id="nroParticipantes"
										type="text"
										name="nroParticipantes"
										onkeyup="validarNumero(this, true);"
										class="normalNegro"
										style="width: 500px;"
										value="<?php echo ($avance != null) ? $avance->GetNroParticipantes() : "" ?>"
									>
								</td>
							</tr>
							<tr>
								<td class="normalNegrita" style="vertical-align: top;">Infocentros: </td>
								<td>
									<input
										id="inputSelectInfocentros"
										class="normalNegro"
										style="width: 500px;"
									>
									<div class="listaDiamante">
										<ul id="ulListaInfocentro"></ul>
									</div>
								</td>
							</tr>
							<!-- 
							<tr>
								<td class="normalNegrita">Anexos</td>
								<td>
									
								</td>
							</tr>
							 -->
							<tr>
								<td colspan="2" class="normalNegroNegrita header">Responsables</td>
							</tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
							<tr>
								<td id="tdResponsables" colspan="2"></td>
							</tr>
							<tr>
								<td colspan="2"><table class="wrapperResponsablesAvance">
									<tr>
										<td class="normalNegrita total" style="text-align: right;">
											Total: <span id="montoTotal" class="montoTotal">0.0</span>
										</td>
									</tr>
								</table></td>
							</tr>
							<tr>
								<td colspan="2">
									<input
										type="button"
										value="Nuevo responsable"
										class="normalNegro"
										onclick="javascript:agregarResponsable({focus: true});"
									>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="normalNegroNegrita header">Rutas</td>
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
								<td colspan="2"><hr /></td>
							</tr>
							<tr>
								<td colspan="2" class="normalNegroNegrita header">Observaciones</td>
							</tr>
							<tr>
								<td colspan="2">
									<textarea
										name="observaciones"
										class="normalNegro"
										rows="3"
										style="width: 785px;"
									><?php echo ($avance != null) ? $avance->GetObservaciones() : "" ?></textarea>
								</td>
							</tr>
						</table>
						<br/>
						<div id="divAcciones" style="text-align: center;">
							<input
								id="btnMultiple"
								type="button"
								class="normalNegro"
								<?php
									if($form->GetTipoOperacion() == NuevoAvanceForm::TIPO_OPERACION_INSERTAR){
										echo 'value="Registar"
											onclick="accionGuardar();"';
									} else if ($form->GetTipoOperacion() == NuevoAvanceForm::TIPO_OPERACION_MODIFICAR){
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
									if($form->GetTipoOperacion() == NuevoAvanceForm::TIPO_OPERACION_INSERTAR){
										echo 'value="Registar y enviar"
											onclick="guardarYEnviar(\'hiddenAccion\');"';
									} else if ($form->GetTipoOperacion() == NuevoAvanceForm::TIPO_OPERACION_MODIFICAR){
										echo 'value="Modificar y enviar"
											onclick="accionActualizarYEnviar(\'hiddenAccion\');"
										';
									}
								?>
							>
							
							
						</div>
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>