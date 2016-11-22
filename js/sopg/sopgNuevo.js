var tipoGet = false;
var tipoff = '';
var tipoimput = '';
var tipoProveedor = false;
var i = 0;
var incrementoFecha = 1;
var estados = new Array();
var tdClass = true;
var ivaPorDefecto = 12; // iva por defecto
var ivas;
var montoTotal1 = 0;
var dato1 = 0;
var idCadenaSigiente = 0;
var idMultComp = '';
var coniva = true;
var tamanoFile = 0;
// var compromisos = new Object();

$()
		.ready(
				function() {

					$(function() {

						$('#Agregarnuevo').hide();
						$('#AgregarnuevoCompromiso').hide();
						$('#tablabeneficiarios').hide();
						$('#CategoriaPartidasopgPadre').hide();
						$('#tablacompselec').hide();
						$('#compfieldset').hide();
						$('#facturas').hide();
						$("#cuerpoRespaldoFisico").hide();
						$("#AgregarRespaldoFisico").hide();
						$('#trActualesFis').hide();
						$('#trActualesDigital').hide();

						$("#mutilizar").hide();
						$("#facturasFilset").hide();

					});

					$(".cb-enable").click(
							function() {
								var parent = $(this).parents('.switch');
								$('.cb-disable', parent)
										.removeClass('selected');
								$(this).addClass('selected');
								$('.checkbox', parent).attr('checked', true);

								$("#mutilizar").show(100);
								$("#facturasFilset").show(100);

								$("#montopordefecto").val('');
								$("#montopordefecto").hide(100);
								$("#montopordefectoSpan").hide(100);

								$("#tbodyneficiarios").children("tr.trCaso")
										.remove();

								$("#tablabeneficiarios").hide(200);
								$('#ProveedorSugeridoValor').val('');
								$('#ProveedorSugeridoNombre').val('');
								$('#ProveedorSugerido').val('');

								$("#benefieldset").animate({
									width : '50%'
								});

								/** compromiso* */

								$("#tbodycompselec").children("tr.trCaso")
										.remove();

								$("#tablacompselec").hide(100);

								$('#compromiso').val('');
								$('#compromisoVal').val('');
								$('#compromisoff').val('');

								$("#compfieldset").hide();

								$("#tablaCategoriaPartidasopg").children(
										"tr.trCaso").remove();

								$("#tablaCategoriaPartidasopgPadre").hide(100);

							});
					$(".cb-disable")
							.click(
									function() {
										coniva = true;

										var parent = $(this).parents('.switch');
										$('.cb-enable', parent).removeClass(
												'selected');
										$(this).addClass('selected');
										$('.checkbox', parent).attr('checked',
												false);

										$("#mutilizar").hide(100);
										$("#facturasFilset").hide(100);
										$("#facturas").hide();

										$("#montopordefecto").show(100);
										$("#montopordefectoSpan").show(100);

										$("#tbodyfacturas > tr").remove();
										$("#facturasFilset").animate({
											width : '15%'

										});

										$("#tbodyneficiarios").children(
												"tr.trCaso").remove();

										$("#tablabeneficiarios").hide(200);
										$('#ProveedorSugeridoValor').val('');
										$('#ProveedorSugeridoNombre').val('');
										$('#ProveedorSugerido').val('');

										$("#benefieldset").animate({
											width : '50%'
										});

										/** compromiso* */

										$("#tbodycompselec").children(
												"tr.trCaso").remove();

										$("#tablacompselec").hide(100);

										$('#compromiso').val('');
										$('#compromisoVal').val('');
										$('#compromisoff').val('');

										$("#compfieldset").hide();

										$("#tablaCategoriaPartidasopg")
												.children("tr.trCaso").remove();

										$("#tablaCategoriaPartidasopgPadre")
												.hide(100);

									});

					$('#file_upload')
							.uploadify(
									{
										'formData' : {
											'accion' : 'GuardarImg',
											'PHPSESSID' : PHPSESSID
										},
										'swf' : '../../js/lib/uploadify/uploadify/uploadify.swf',
										'uploader' : '../../acciones/sopg/sopg.php',
										'buttonText' : '',
										'fileTypeExts' : '*.gif; *.jpg; *.png; *.odt; *.pdf',
										'uploadLimit' : 5,
										'queueSizeLimit' : 5,
										'UploadLimit' : 5,
										'progressData' : 'speed',
										'width' : 104,
										'height' : 32,
										'auto' : false,
										'onSelect' : function(file) {
											tamanoFile++;
										},
										'onCancel' : function(file) {
											tamanoFile--;
										},
										'onQueueComplete' : function(queueData) {

											$('#formspg').attr('action',
											'../../acciones/sopg/sopg.php?accion=IngresarAccion');
											$('#formspg')[0].submit();

										}

									});

					$("#montopordefecto").keyup(function() {

						formato_num($(this), false);

					});

					$("#pctaRespaldoFisico")
							.keyup(
									function(event) {

										val = trim($("#pctaRespaldoFisico")
												.val());

										if (val != ''
												&& $("#pctaRespaldoFisico")
														.val().length > 4) {

											$("#AgregarRespaldoFisico").show(
													'fade', 300);

										} else {

											$("#AgregarRespaldoFisico").hide(
													200);

										}

									});

					$('#AgregarRespaldoFisico').click(function() {

						AgregarRegistroFisico();

					});

					$('.classname')
							.click(
									function() {

										/*
										 * if($('#catPago').val() == ''){
										 * 
										 * alert("seleccione la categoria del
										 * pago");
										 * 
										 * $('#catPago').focus();
										 * 
										 * 
										 * return;
										 *  }
										 */

										$('#facturas').show();

										var tbody = $('#tbodyfacturas')[0];

										var fila = document.createElement("tr");
										fila.className = 'normalNegro trCaso';

										var columna1 = document
												.createElement("td");
										columna1.setAttribute("style",
												"font-size: 12px;");
										var span = document
												.createElement("span");
										span
												.appendChild(document
														.createTextNode($("#tbodyfacturas > tr").length + 1));
										columna1.appendChild(span);
										columna1.className = 'numFactura';

										var columna12 = document
												.createElement("td");

										columna12.setAttribute("valign", "top");

										var input12 = document
												.createElement("select");

										input12.setAttribute("style",
												"width : 200px;");

										input12
												.setAttribute("class",
														"beneficiarioFactura normalNegrita");
										input12.setAttribute("name",
												"factura[beneficiario][]");

										var option = document
												.createElement("option");

										option.setAttribute("value", '');

										option
												.appendChild(document
														.createTextNode(".:Seleccionar:."));

										input12.appendChild(option);

										$(
												'input[name=\'beneficiario[nombre][]\']')
												.each(
														function() {

															codigo = $(this)
																	.parent(
																			'td')
																	.parent(
																			'tr')
																	.find(
																			'input[name=\'beneficiario[codigo][]\']')
																	.val();

															nombre = $(this)
																	.val();

															var option = document
																	.createElement("option");

															option
																	.setAttribute(
																			"value",
																			codigo);

															option
																	.appendChild(document
																			.createTextNode(nombre));

															input12
																	.appendChild(option);

														});

										columna12.appendChild(input12);

										var columna2 = document
												.createElement("td");
										var input2 = document
												.createElement("input");
										input2.setAttribute("type", "tex");
										input2.setAttribute("size", "14");
										fila.className = 'normalNegro';
										input2.setAttribute("name",
												"factura[id][]");
										input2.value = "";
										columna2.appendChild(input2);

										var columna3 = document
												.createElement("td");
										var input3 = document
												.createElement("input");
										input3.setAttribute("type", "tex");
										input3.setAttribute("size", "18");
										input3.className = 'normalNegro';
										input3.setAttribute("name",
												"factura[codigo][]");
										input3.value = "";
										columna3.appendChild(input3);

										var columna4 = document
												.createElement("td");
										columna4.setAttribute("style",
												"width : 200px;");
										var input4 = document
												.createElement("input");
										input4.setAttribute("type", "tex");
										input4.setAttribute("size", "9");
										input4.setAttribute("id",
												"fechaCalendario"
														+ incrementoFecha);
										input4.setAttribute("readonly",
												"readonly");
										input4.className = 'dateparse';
										input4.value = "";
										input4.setAttribute("name",
												"factura[fecha][]");
										columna4.appendChild(input4);

										Link = document.createElement("a");
										Link.setAttribute("href",
												"javascript:void(0);");
										Link.setAttribute("onclick",
												"g_Calendar.show(event, 'fechaCalendario"
														+ incrementoFecha
														+ "' );");
										img = document.createElement("img");
										img.className = 'cp_img';
										img
												.setAttribute("src",
														"../../js/lib/calendarPopup/img/calendar.gif");
										Link.appendChild(img);
										fila.appendChild(Link);
										columna4.appendChild(Link);
										incrementoFecha++;

										var columna5 = document
												.createElement("td");
										var input5 = document
												.createElement("input");
										input5.setAttribute("type", "tex");
										input5.className = 'normalNegro';
										input5.setAttribute("name",
												"factura[montoExento][]");
										input5.value = "";
										columna5.appendChild(input5);

										$(input5).keyup(function() {

											formato_num($(this), 0);

										});

										var columna10 = document
												.createElement("td");
										var input10 = document
												.createElement("input");
										input10.setAttribute("type", "tex");
										input10.className = 'normalNegro';
										input10.setAttribute("name",
												"factura[montoSugeto][]");
										input10.value = "";
										columna10.appendChild(input10);

										$(input10).keyup(function() {

											formato_num($(this), 1);

										});

										var columna6 = document
												.createElement("td");
										var select = document
												.createElement("select");
										select.setAttribute("name",
												"factura[iva][]");

										var option;

										$(select).bind('click', function() {
											ivaFactura(fila);
										});

										if (ivas) {
											$
													.each(
															ivas,
															function(id, params) {

																option = document
																		.createElement("option");
																linkText = document
																		.createTextNode(id
																				+ "%");

																option.value = id;

																if (id == ivaPorDefecto) {

																	option
																			.setAttribute(
																					"selected",
																					"selected");

																}

																option
																		.appendChild(linkText);
																select
																		.appendChild(option);

															});

										}

										columna6.appendChild(select);

										var columna7 = document
												.createElement("td");
										columna7.setAttribute("style",
												"font-size: 12px;");
										var span = document
												.createElement("span");
										span.className = 'montoDispon';
										span.appendChild(document
												.createTextNode("0,00"));
										columna7.appendChild(span);

										var columna9 = document
												.createElement("td");
										columna9.setAttribute("style",
												"font-size: 12px;");
										var span9 = document
												.createElement("span");
										span9.className = 'ivaMonto';
										span9.appendChild(document
												.createTextNode("0,00"));
										columna9.appendChild(span9);

										var input9 = document
												.createElement("input");
										input9.setAttribute("type", "hidden");
										input9.className = 'normalNegro';
										input9.setAttribute("name",
												"factura[ivaMonto][]");
										input9.value = "";
										columna9.appendChild(input9);

										// OPCION DE ELIMINAR
										var columna8 = document
												.createElement("td");
										columna8.className = 'link';
										deleteLink = document
												.createElement("a");
										deleteLink.setAttribute("href",
												"javascript:void(0);");
										linkText = document
												.createTextNode("Eliminar");
										deleteLink.appendChild(linkText);
										columna8.appendChild(deleteLink);

										$(deleteLink).bind('click', function() {
											eliminarfactua(fila);
										});

										var columna11 = document
												.createElement("td");
										columna11.setAttribute("style",
												"font-size: 12px;");
										var span = document
												.createElement("span");
										span.className = 'montoDisponIva';
										span.appendChild(document
												.createTextNode("0,00"));
										columna11.appendChild(span);

										fila.appendChild(columna1);
										fila.appendChild(columna12);
										fila.appendChild(columna2);
										fila.appendChild(columna3);
										fila.appendChild(columna4);
										fila.appendChild(columna7);
										fila.appendChild(columna5);
										fila.appendChild(columna10);
										fila.appendChild(columna11);
										fila.appendChild(columna6);
										fila.appendChild(columna9);
										fila.appendChild(columna8);

										if ($("#tbodyfacturas > tr").length < 3) {

											$("#facturasFilset").animate({
												width : '95%'

											});
										}

										tbody.appendChild(fila);

										calcularMontoDisponible();

									});

					$('#compromiso')
							.autocomplete(
									{
										autoFocus : false,
										delay : 50,
										source : function(request, response) {

											var compromisosActuales = new Array();
											$(
													'input[name=\'compromisoCod[compromiso][]\']')
													.each(
															function(index) {
																compromisosActuales[index] = this.value;
															});

											$
													.ajax({
														type : "POST",

														url : "../../acciones/sopg/sopg.php",
														dataType : "json",
														data : {
															accion : "SearchCompromiso",
															tipoRespuesta : 'json',
															key : request.term,
															tipoff : tipoff,
															tipoimput : tipoimput,
															tipoProveedor : tipoProveedor,
															compromisosActuales : compromisosActuales

														},

														success : function(json) {
																	
															var index = 0;
															var items = new Array();

															if (json == "") {

																$('#compromiso')
																		.val('');

																$(
																		'#compromisoVal')
																		.val('');

																$(
																		'#compromisoff')
																		.val('');

																$(
																		'#AgregarnuevoCompromiso')
																		.hide(
																				300);

															}

															$
																	.each(
																			json,
																			function(
																					id,
																					params) {

																				var value = id;

																				// console.log("nos
																				// que
																				// van
																				// "+value);
																				items[index++] = {
																					id : id,
																					fufi : params.fuente,
																					tipoProveedor : params.proveedor,
																					value : value,
																					tipoimpu : params.tipo
																				};

																			});

															response(items);

														}
													});
										},
										minLength : 1,
										select : function(event, ui) {

											$('#compromiso').val(ui.item.value);
											$('#compromisoVal').val(
													ui.item.value);
											$('#compromisoff')
													.val(ui.item.fufi);
											$('#compromisoproveedor').val(
													ui.item.tipoProveedor);
											$('#compromisotipo').val(
													ui.item.tipoimpu);
											$('#AgregarnuevoCompromiso').show(
													200);

											tipoimput = ui.item.tipoimpu;
											tipoff = ui.item.fufi;
											tipoProveedor = ui.item.tipoProveedor;

											return true;

										}

									});

					$('#AgregarnuevoCompromiso')
							.click(
									function() {
										/*
										 * if($('#catPago').val() == ''){
										 * 
										 * alert("seleccione la categoria del
										 * pago");
										 * 
										 * $('#catPago').focus();
										 * 
										 * return;
										 *  }
										 */

										if ($('#compromiso').val() == $(
												'#compromisoVal').val()
												&& $('#compromisoVal').val() != "") {

											var tbody = $('#tbodycompselec')[0];

											var fila = document
													.createElement("tr");
											fila.className = 'normalNegro trCaso';

											var columna5 = document
													.createElement("td");

											columna5.setAttribute("valign",
													"top");
											columna5.setAttribute("style",
													"font-size:10px");
											columna5.className = 'classcomp';
											columna5.appendChild(document
													.createTextNode($(
															'#compromiso')
															.val()));
											var input5 = document
													.createElement("input");
											input5.setAttribute("type",
													"hidden");
											input5
													.setAttribute("name",
															"compromisoCod[compromiso][]");
											input5.value = $('#compromiso')
													.val();
											columna5.appendChild(input5);

											var columna6 = document
													.createElement("td");
											columna6.setAttribute("valign",
													"top");
											columna6.setAttribute("style",
													"font-size:10px");
											columna6.appendChild(document
													.createTextNode($(
															'#compromisoff')
															.val()));

											var columna7 = document
													.createElement("td");
											columna7.setAttribute("valign",
													"top");
											columna7.setAttribute("style",
													"font-size:10px");
											columna7
													.appendChild(document
															.createTextNode($(
																	'#compromisoproveedor')
																	.val()));

											// OPCION DE ELIMINAR
											var columna8 = document
													.createElement("td");
											columna8.setAttribute("valign",
													"top");
											columna8.className = 'link';
											deleteLink = document
													.createElement("a");
											deleteLink.setAttribute("href",
													"javascript:void(0);");
											linkText = document
													.createTextNode("Eliminar");
											deleteLink.appendChild(linkText);
											columna8.appendChild(deleteLink);

											$(deleteLink).bind('click',
													function() {

														eliminarComp($(this));

													});

											fila.appendChild(columna5);
											fila.appendChild(columna6);
											fila.appendChild(columna7);
											fila.appendChild(columna8);

											if ($("#tbodycompselec > tr").length < 3) {

												$('#tablacompselec')
														.show(
																100,
																function() {

																	$(
																			'#compfieldset')
																			.show();

																	$(
																			"#compfieldset")
																			.animate(
																					{
																						width : '60%'
																					});
																});

											}

											$.ajax({
														url : "../../acciones/comp/comp.php",
														dataType : "json",
														data : {
															accion : "SearchCompromisoDetalle",
															tipoRespuesta : 'json',
															key : $(
																	'#compromisoVal')
																	.val()

														},
														success : function(json) {
															
															//alert(json['compromisoImputas'][0]['tipoImpu']);
															//alert(JSON.stringify(json.compromiso.compromisoImputas[0].tipoImpu));
															var validar = false;

															$
																	.each(
																			json,
																			function(id,val) {

																				if (val.compromisoImputas) {

																					if ($("#tbodyneficiarios > tr").length > 2) {

																						if ($(
																								'#checkbox')
																								.attr(
																										'checked')) {

																							$
																									.each(
																											val.compromisoImputas,
																											function(id2,val2) {

																												var dato5 = val2.partida.id;

																												if (val2.tipoImpu > 0) {

																													var dato3 = val2.proyectoEspecifica.centroGestor
																															+ '/'
																															+ val2.proyectoEspecifica.centroCosto;

																												} else {

																													var dato3 = val2.CentralizadaEspecifica.centroGestor
																															+ '/'
																															+ val2.CentralizadaEspecifica.centroCosto;
																												}

																												// alert($('input[name=\'partidasCompromiso[partidaProyAcc]['+
																												// dato5
																												// +
																												// dato3
																												// +
																												// ']\']').val()
																												// ==
																												// undefined);

																												// alert($('#tablaCategoriaPartidasopg').children('tr:has(td)').not($('#tablaCategoriaPartidasopg
																												// >
																												// tr:has(td:has(input:[value^=\'4.03.18.01.00\']))')).length
																												// >=
																												// 1);

																												// $('#tablaCategoriaPartidasopg').children('tr:has(td)').not($('#tablaCategoriaPartidasopg
																												// >
																												// tr:has(td:has(input:[value^=\'4.03.18.01.00\']))')).each(function(){

																												// alert($(this).find('input').val());

																												// });

																												if (($(
																														'input[name=\'partidasCompromiso[partidaProyAcc]['
																																+ dato5
																																+ dato3
																																+ ']\']')
																														.val() == undefined)
																														&& ($(
																																'#tablaCategoriaPartidasopg')
																																.children(
																																		'tr:has(td)')
																																.not(
																																		$('#tablaCategoriaPartidasopg > tr:has(td:has(input:[value^=\'4.03.18.01.00\']))')).length >= 1)) {

																													alert("tienes mas de un beneficiario y la partida es diferente a la que esta actualmente");

																													validar = true;

																													return;

																												}

																											});

																						} else {

																							if (val.compromisoImputas.length > 1) {

																								alert("El compromiso seleccionado contiene mas de una partida para mas de un beneficiario");
																								return;

																							} else {
																								
																								

																								$
																										.each(
																												val.compromisoImputas,
																												function(
																														id2,
																														val2) {

																													var dato5 = val2.partida.id;

																													if (val2.tipoImpu > 0) {

																														var dato3 = val2.proyectoEspecifica.centroGestor
																																+ '/'
																																+ val2.proyectoEspecifica.centroCosto;

																													} else {

																														var dato3 = val2.CentralizadaEspecifica.centroGestor
																																+ '/'
																																+ val2.CentralizadaEspecifica.centroCosto;
																													}

																													if (($(
																															'input[name=\'partidasCompromiso[partidaProyAcc]['
																																	+ dato5
																																	+ dato3
																																	+ ']\']')
																															.val() == undefined)
																															&& ($('#tablaCategoriaPartidasopg  > tr').length == 2)) {

																														alert("tienes mas de un beneficiario y la partida es diferente a la que esta actualmente");

																														validar = true;
																														return;

																													}

																												});

																							}

																						}

																						if (validar) {
																							return;

																						}

																					}

																					tbody
																							.appendChild(fila);

																					$
																							.each(
																									val.compromisoImputas,
																									function(id2,val2) {

																										var dato5 = val2.partida.id;
																										var dato6 = val2.partida.nombre;

																										if (val2.tipoImpu > 0) {

																											var dato2 = val2.proyecto.id;
																											var dato4 = val2.proyectoEspecifica.id;
																											var dato3 = val2.proyectoEspecifica.centroGestor
																													+ '/'
																													+ val2.proyectoEspecifica.centroCosto;

																										} else {

																											var dato1 = 'Ac.Centralizada';

																											var dato2 = val2.accionCentralizada.id;
																											var dato4 = val2.CentralizadaEspecifica.id;
																											var dato3 = val2.CentralizadaEspecifica.centroGestor
																													+ '/'
																													+ val2.CentralizadaEspecifica.centroCosto;
																										}

																										var dato7 = number_format(
																												val2.monto,
																												2,
																												',',
																												'.');

																										// alert(dato1+'\n'+dato2+'\n'+dato3+'\n'+dato4+'\n'+dato5+'\n'+dato6+'\n'+dato7);

																										if ($('input[name=\'partidasCompromiso[partidaProyAcc]['+ dato5 + dato3 + ']\']').val() == undefined) {

																											var tbody1 = $('#tablaCategoriaPartidasopg')[0];

																											var fila1 = document.createElement("tr");
																											fila1.className = 'normalNegro trCaso';
																											var columna5 = document.createElement("td");
																											columna5.setAttribute("valign","top");
																											columna5.setAttribute("style","font-size:10px");
																											columna5.appendChild(document.createTextNode(dato5));
																											var input5 = document.createElement("input");
																											input5.setAttribute("type","hidden");
																											input5.setAttribute("name","partidasCompromiso[partida][]");
																											input5.value = dato5;
																											columna5.appendChild(input5);

																											var columna6 = document.createElement("td");
																											columna6.setAttribute("valign","top");
																											columna6.setAttribute("style","font-size:10px");
																											columna6.appendChild(document.createTextNode(dato6));

																											var columna8 = document.createElement("td");
																											columna8.setAttribute("valign","top");
																											columna8.setAttribute("style","font-size:10px");
																											columna8.appendChild(document.createTextNode(dato3));

																											var input8 = document.createElement("input");
																											input8.setAttribute("type","hidden");
																											input8.setAttribute("name","partidasCompromiso[proyAcc][]");
																											input8.value = dato2;
																											columna8.appendChild(input8);

																											var input9 = document.createElement("input");
																											input9.setAttribute("type","hidden");
																											input9.setAttribute("name","partidasCompromiso[proyAccEspe][]");
																											input9.value = dato4;
																											
																											columna8.appendChild(input9);

																											var columna7 = document.createElement("td");
																											columna7.setAttribute("valign","top");
																											columna7.setAttribute("style","font-size:10px");
																											var span = document.createElement("span");
																											span.appendChild(document.createTextNode(dato7));
																											columna7.appendChild(span);

																											var input7 = document.createElement("input");
																											input7.setAttribute("type","hidden");
																											input7.setAttribute("name","partidasCompromiso[monto][]");
																											input7.value = val2.monto;

																											if (dato5 == "4.03.18.01.00") {

																												input7.className = 'imputacionPresupuestaria';

																											} else {

																												input7.className = 'montoTotal';

																											}

																											columna7.appendChild(input7);

																											var input12 = document.createElement("input");
																											input12.setAttribute("type","hidden");
																											input12.setAttribute("name","partidasCompromiso[partidaProyAcc]["+ dato5 + dato3 + "]");
																											input12.value = dato5;
																											columna7.appendChild(input12);
																											
																											var input13 = document.createElement("input");
																											input13.setAttribute("type","hidden");
																											input13.setAttribute("name","partidasCompromiso[tipoimpu]");
																											input13.value = json.compromiso.compromisoImputas[0].tipoImpu;
																											columna5.appendChild(input13);

																											fila1.appendChild(columna5);
																											fila1.appendChild(columna6);
																											fila1.appendChild(columna8);
																											fila1.appendChild(columna7);
																											tbody1.appendChild(fila1);

																											// alert($('input[name=\'partidasCompromiso[partidaProyAcc][4.03.11.02.002010/20213]\']').val());

																											$(
																													'#CategoriaPartidasopgPadre')
																													.show();

																										} else {

																											// alert($('input[name=\'partidasCompromiso[partidaProyAcc]['+dato5+dato3+']\']').parent("td").children('input[name=\'partidasCompromiso[monto][]\']').val());

																											base = QuitarCaracter(
																													$(
																															'input[name=\'partidasCompromiso[partidaProyAcc]['
																																	+ dato5
																																	+ dato3
																																	+ ']\']')
																															.parent(
																																	"td")
																															.children(
																																	'input[name=\'partidasCompromiso[monto][]\']')
																															.val(),
																													".");
																											asumar = QuitarCaracter(val2.monto);

																											montototal = base
																													+ asumar;

																											$(
																													'input[name=\'partidasCompromiso[partidaProyAcc]['
																															+ dato5
																															+ dato3
																															+ ']\']')
																													.parent(
																															"td")
																													.children(
																															'input[name=\'partidasCompromiso[monto][]\']')
																													.val(
																															montototal);

																											$(
																													'input[name=\'partidasCompromiso[partidaProyAcc]['
																															+ dato5
																															+ dato3
																															+ ']\']')
																													.parent(
																															"td")
																													.children(
																															'input[name=\'partidasCompromiso[monto][]\']')
																													.parent(
																															'td')
																													.children(
																															'span')
																													.html(
																															number_format(
																																	montototal,
																																	2,
																																	',',
																																	'.'));

																										}

																									});

																					calcularMontoDisponible();

																				}

																			});

															$('#compromiso')
																	.val('');
															$('#compromisoVal')
																	.val('');
														}

													});

											$('#AgregarnuevoCompromiso').hide(
													300);

										} else {

											alert('Debe seleccionar un compromiso');

											$('#compromiso').val('');
											$('#compromisoVal').val('');

											$('#compromiso').focus();

										}

										if (idMultComp != '1') {

											$("#mexento , #msujeto").hide();

										}

									});

					$('#Agregarnuevo')
							.click(
									function() {

										if ($('#checkbox').attr('checked') != 'checked') {
											if ($('#montopordefecto').val() == "") {
												alert('Monto no puede ser vacio!');

												return;
											}

										}

										/*
										 * if($('#catPago').val() == ''){
										 * 
										 * alert("seleccione la categoria del
										 * pago");
										 * 
										 * $('#catPago').focus();
										 * 
										 * return;
										 *  }
										 */

										if ($("#tbodyneficiarios > tr:has(td)").length == 1) {

											if ($('#checkbox').attr('checked')) {
												alert($(
														'#tablaCategoriaPartidasopg')
														.children('tr:has(td)')
														.not(
																$('#tablaCategoriaPartidasopg > tr:has(td:has(input:[value^=\'4.03.18.01.00\']))')).length);

												if ($(
														'#tablaCategoriaPartidasopg')
														.children('tr:has(td)')
														.not(
																$('#tablaCategoriaPartidasopg > tr:has(td:has(input:[value^=\'4.03.18.01.00\']))')).length > 1) {

													alert("no puedes solo una partida aparte de la del iva");

													return;

												}

											} else {

												if ($('input[name=\'partidasCompromiso[partida][]\']').length > 1) {

													alert("no puedes solo una partida");

													return;

												}

											}

										}

										if ($('#ProveedorSugerido').val() != ""
												&& $('#ProveedorSugeridoValor')
														.val() != "") {

											$("#benefieldset").animate({
												width : '80%'
											});

											var tbody = $('#tbodyneficiarios')[0];

											var fila = document
													.createElement("tr");
											fila.className = 'normalNegro trCaso '
													+ tdClass;

											var columna1 = document
													.createElement("td");
											columna1.setAttribute("valign",
													"top");
											columna1.setAttribute("style",
													"font-size:10px");
											columna1.className = 'numBeneficiario';

											columna1
													.appendChild(document
															.createTextNode($("#tbodyneficiarios > tr").length));

											var columna2 = document
													.createElement("td");
											columna2.setAttribute("valign",
													"top");
											columna2
													.appendChild(document
															.createTextNode($(
																	'#ProveedorSugeridoValor')
																	.val()));

											var input2 = document
													.createElement("input");
											input2.setAttribute("type",
													"hidden");
											input2.setAttribute("name",
													"beneficiario[codigo][]");
											input2.value = $(
													'#ProveedorSugeridoValor')
													.val();
											columna2.appendChild(input2);

											var columna3 = document
													.createElement("td");
											columna3.setAttribute("valign",
													"top");
											columna3
													.appendChild(document
															.createTextNode($(
																	'#ProveedorSugeridoNombre')
																	.val()));

											var input3 = document
													.createElement("input");
											input3.setAttribute("type",
													"hidden");
											input3.setAttribute("name",
													"beneficiario[nombre][]");
											input3.value = $(
													'#ProveedorSugeridoNombre')
													.val();
											columna3.appendChild(input3);

											var columna4 = document
													.createElement("td");
											columna4.setAttribute("valign",
													"top");
											var input4 = document
													.createElement("textarea");

											input4.setAttribute("type", "text");
											input4
													.setAttribute("name",
															"beneficiario[observacion][]");

											if (tdClass != false) {

												input4
														.setAttribute("style",
																"height:40px;width:300;background:#F2F2F2;");

												tdClass = false;

											} else {

												input4
														.setAttribute("style",
																"height:40px;width:300;");

												tdClass = true;

											}

											columna4.appendChild(input4);

											var columna5 = document
													.createElement("td");
											columna5.setAttribute("valign",
													"top");
											columna5.setAttribute("class",
													"tdPartida");
											var input5 = document
													.createElement("select");
											input5.setAttribute("name",
													"beneficiario[estado][]");
											input5.setAttribute("class",
													"normalNegrita");

											$
													.each(
															estados,
															function(id, params) {

																var option = document
																		.createElement("option");
																option
																		.setAttribute(
																				"value",
																				params.id);
																option
																		.appendChild(document
																				.createTextNode(params.nombre));

																input5
																		.appendChild(option);

															});

											columna5.appendChild(input5);

											var columna6 = document
													.createElement("td");
											columna6.setAttribute("valign",
													"top");
											columna6.appendChild(document
													.createTextNode(tipoGet));
											var input6 = document
													.createElement("input");
											input6.setAttribute("type",
													"hidden");
											input6.setAttribute("name",
													"beneficiario[tipo][]");
											input6.value = tipoGet;
											columna6.appendChild(input6);

											// ////////////////////////////
											// terminar //////////////////// que
											// se desaparesca el moto cuando sea
											// solo un beneficiario

											if ($('#checkbox').attr('checked')) {

												$("#MontoasignadoBeneficiario")
														.hide();

											} else {

												if ($("#tbodyneficiarios > tr:has(td)").length < 1) {

													$(
															"#MontoasignadoBeneficiario")
															.hide();

												} else {

													$(
															"#MontoasignadoBeneficiario")
															.show();

												}

											}

											if (!$('#checkbox').attr('checked')) {

												var columna7 = document
														.createElement("td");
												columna7.setAttribute("valign",
														"top");
												columna7
														.appendChild(document
																.createTextNode($(
																		"#montopordefecto")
																		.val()));
												var input7 = document
														.createElement("input");
												input7.setAttribute("type",
														"hidden");
												input7
														.setAttribute("name",
																"beneficiario[monto][]");
												input7.value = $(
														"#montopordefecto")
														.val();
												columna7.appendChild(input7);

												if ($("#tbodyneficiarios > tr:has(td)").length < 1) {

													$(columna7).hide();

												} else {

													$(
															'input[name=\'beneficiario[monto][]\']')
															.parent('td')
															.show();

												}

											}
											// OPCION DE ELIMINAR
											var columna8 = document
													.createElement("td");

											columna8.setAttribute("valign",
													"top");

											columna8.className = 'link';

											deleteLink = document
													.createElement("a");

											deleteLink.setAttribute("href",
													"javascript:void(0);");

											linkText = document
													.createTextNode("Eliminar");

											deleteLink.appendChild(linkText);

											columna8.appendChild(deleteLink);

											$(deleteLink)
													.bind(
															'click',
															function() {

																eliminarBeneficiario($(this));

															});

											fila.appendChild(columna1);

											fila.appendChild(columna2);
											fila.appendChild(columna3);
											fila.appendChild(columna6);
											fila.appendChild(columna4);

											fila.appendChild(columna5);

											if (!$('#checkbox').attr('checked')) {

												fila.appendChild(columna7);

											}

											fila.appendChild(columna8);

											tbody.appendChild(fila);

											$('#tablabeneficiarios').show();

											$(fila).show(500);

											$('#Agregarnuevo').hide(300);

										} else {

											alert('Debe seleccionar los datos del beneficiario');
											$('#ProveedorSugerido').focus()

										}

										$('.beneficiarioFactura')
												.each(
														function() {

															var select = $(this)[0];

															var option = document
																	.createElement("option");

															option
																	.setAttribute(
																			"value",
																			$(
																					'#ProveedorSugeridoValor')
																					.val());

															option
																	.appendChild(document
																			.createTextNode($(
																					'#ProveedorSugeridoNombre')
																					.val()));

															select
																	.appendChild(option);

														});

										$('#ProveedorSugeridoValor').val('');
										$('#ProveedorSugeridoNombre').val('');
										$('#ProveedorSugerido').val('');

									});

					$('#catPago').autocomplete(
							{
								autoFocus : false,
								delay : 100,
								source : function(request, response) {

									$.ajax({
										url : "../../acciones/sopg/sopg.php",
										dataType : "json",
										data : {
											accion : "SearchTipoSoliciud",
											tipoRespuesta : 'json',
											key : request.term
										},
										success : function(json) {

											var index = 0;
											var items = new Array();

											if (json == false) {

												$('#catpago').val('');
												$('#catPagoVal').val('');
												idMultComp = '';

											}

											$.each(json, function(id, params) {

												var value = params.nombre;
												items[index++] = {
													id : params.idMultComp,
													id : params.id,
													value : value
												};

											});

											response(items);

										}

									});

								},
								minLength : 1,
								select : function(event, ui) {

									$('#catPago').val(ui.item.value);
									$('#catPagoVal').val(ui.item.id);
									// alert('valor de input
									// '+$('#catPagoVal').val());
									$('#tipocatpago').val(ui.item.id);

									if (coniva == true) {

										$("#mutilizar").hide();
										$("#facturasFilset").hide();

									} else {

										$("#mutilizar").show();
										$("#facturasFilset").show();

									}

									$("#tbodyneficiarios")
											.children("tr.trCaso").remove();

									$("#tablabeneficiarios").hide(200);
									$('#ProveedorSugeridoValor').val('');
									$('#ProveedorSugeridoNombre').val('');
									$('#ProveedorSugerido').val('');

									$("#benefieldset").animate({
										width : '50%'
									});

									$("#tbodycompselec").children("tr.trCaso")
											.remove();

									$("#tablacompselec").hide(100);

									$('#compromiso').val('');
									$('#compromisoVal').val('');
									$('#compromisoff').val('');

									$("#compfieldset").hide();

									$("#tablaCategoriaPartidasopg").children(
											"tr.trCaso").remove();

									$("#tablaCategoriaPartidasopgPadre").hide(
											100);

									$("#tbodyfacturas").children("tr.trCaso")
											.remove();

									$("#facturas").hide(100);

									$("#tbodyfacturas > tr").remove();

									$("#facturasFilset").animate({
										width : '15%'

									});

									return true;

								},
								change : function(event, ui) {
									if (!ui.item) {

										$('#catPago').val('');
										$('#catPagoVal').val('');
										idMultComp = '';

									}

									return true;
								}

							});

					$('#ProveedorSugerido')
							.autocomplete(
									{
										autoFocus : false,
										delay : 100,
										source : function(request, response) {
											$
													.ajax({
														url : "../../acciones/sopg/sopg.php",
														dataType : "json",
														data : {
															accion : "SearchProveedorSugerido",
															tipoRespuesta : 'json',
															key : request.term,
															tipoGet : tipoGet
														},
														success : function(json) {

															var index = 0;
															var items = new Array();

															if (json == "") {

																$(
																		'#ProveedorSugeridoValor')
																		.val('');
																$(
																		'#ProveedorSugeridoNombre')
																		.val('');
																$(
																		'#ProveedorSugerido')
																		.val('');
																$(
																		'#Agregarnuevo')
																		.hide(
																				300);

															}

															$
																	.each(
																			json,
																			function(
																					id,
																					params) {

																				condicion = true;
																				$(
																						'input[name=\'beneficiario[codigo][]\']')
																						.each(
																								function(
																										index) {

																									if (this.value == params.id) {

																										condicion = false;

																									}

																								});

																				if (condicion == true) {

																					var value = params.id
																							+ ':'
																							+ params.nombre;
																					items[index++] = {
																						id : params.id,
																						tipo : params.tipo,
																						nombre : params.nombre,
																						value : value
																					};

																				}

																			});

															response(items);

														}
													});
										},
										minLength : 1,
										select : function(event, ui) {

											$('#ProveedorSugerido').val(
													ui.item.value);
											$('#ProveedorSugeridoValor').val(
													ui.item.id);
											$('#ProveedorSugeridoNombre').val(
													ui.item.nombre);

											tipoGet = ui.item.tipo;

											$('#Agregarnuevo').show(200);

											return true;

										}

									});

					function eliminarBeneficiario(objA) {

						objTrs = $(objA).parent("td").parent("tr.trCaso");

						codigo = objTrs.find(
								'input[name=\'beneficiario[codigo][]\']').val();

						$('.beneficiarioFactura').each(
								function() {

									var select = this;

									if ($(select).val() == codigo) {

										$(select).val('');

									}

									$(this).children(
											'option[value="' + codigo + '"]')
											.remove();

								});

						objTrs.hide(100).remove();

						i = 1;

						$("#tbodyneficiarios > tr.trCaso").each(
								function(index) {

									$(this).children("td.numBeneficiario")
											.html(i);

									i++;

								});

						if ($("#tbodyneficiarios > tr").length < 2) {

							$("#tablabeneficiarios").hide(200);
							$('#ProveedorSugeridoValor').val('');
							$('#ProveedorSugeridoNombre').val('');
							$('#ProveedorSugerido').val('');
							$('#Agregarnuevo').hide(200, function() {

								$("#benefieldset").animate({
									width : '70%'
								}, 10);

							});

							tipoGet = false;

						}

						if ($("#tbodyneficiarios > tr:has(td)").length == 1) {

							$("#MontoasignadoBeneficiario").hide(50);

						}

						if ($("#tbodyneficiarios > tr:has(td)").length == 1) {

							$('input[name=\'beneficiario[monto][]\']').parent(
									'td').hide(50);

						}

					}

		
					function eliminarComp(obj) {

						comp = obj.parent('td').parent('tr').children(
								'td.classcomp').children('input').val();

						if ($("#tbodyfacturas > tr").length > 0
								&& !confirm("¿Est\u00E1 seguro que desea eliminar este compromiso("
										+ comp
										+ ")?. \nAl eliminarlo se eliminar\u00E1n todas las facturas registradas.")) {

							return;
						}

						$
								.ajax({
									url : "../../acciones/comp/comp.php",
									dataType : "json",
									data : {
										accion : "SearchCompromisoDetalle",
										tipoRespuesta : 'json',
										key : comp

									},
									success : function(json) {

										$
												.each(
														json,
														function(id, val) {

															if (val.compromisoImputas) {

																$
																		.each(
																				val.compromisoImputas,
																				function(
																						id2,
																						val2) {

																					// alert(JSON.stringify(val.compromisoImputas
																					// ));

																					var dato5 = val2.partida.id;
																					var dato6 = val2.partida.nombre;

																					if (val2.tipoImpu > 0) {

																						var dato2 = val2.proyecto.id;
																						var dato4 = val2.proyectoEspecifica.id;
																						var dato3 = val2.proyectoEspecifica.centroGestor
																								+ '/'
																								+ val2.proyectoEspecifica.centroCosto;

																					} else {

																						var dato1 = 'Ac.Centralizada';

																						var dato2 = val2.accionCentralizada.id;
																						var dato4 = val2.CentralizadaEspecifica.id;
																						var dato3 = val2.CentralizadaEspecifica.centroGestor
																								+ '/'
																								+ val2.CentralizadaEspecifica.centroCosto;
																					}

																					var dato7 = number_format(
																							val2.monto,
																							2,
																							',',
																							'.');

																					if ($(
																							'input[name=\'partidasCompromiso[partidaProyAcc]['
																									+ dato5
																									+ dato3
																									+ ']\']')
																							.val() != undefined) {

																						base = $(
																								'input[name=\'partidasCompromiso[partidaProyAcc]['
																										+ dato5
																										+ dato3
																										+ ']\']')
																								.parent(
																										"td")
																								.children(
																										'input[name=\'partidasCompromiso[monto][]\']')
																								.val();

																						base = QuitarCaracter(
																								base,
																								".");

																						asumar = QuitarCaracter(val2.monto);
																						montototal = base
																								- asumar;

																						if (montototal <= 0) {

																							$(
																									'input[name=\'partidasCompromiso[partidaProyAcc]['
																											+ dato5
																											+ dato3
																											+ ']\']')
																									.parent(
																											'td')
																									.parent(
																											'tr')
																									.hide(
																											100)
																									.remove();

																						}

																						$(
																								'input[name=\'partidasCompromiso[partidaProyAcc]['
																										+ dato5
																										+ dato3
																										+ ']\']')
																								.parent(
																										"td")
																								.children(
																										'input[name=\'partidasCompromiso[monto][]\']')
																								.val(
																										montototal);
																						$(
																								'input[name=\'partidasCompromiso[partidaProyAcc]['
																										+ dato5
																										+ dato3
																										+ ']\']')
																								.parent(
																										"td")
																								.children(
																										'input[name=\'partidasCompromiso[monto][]\']')
																								.parent(
																										'td')
																								.children(
																										'span')
																								.html(
																										number_format(
																												montototal,
																												2,
																												',',
																												'.'));

																					}

																				});

															}

														});

										if ($("#tablaCategoriaPartidasopg > tr").length < 2) {

											$("#compfieldset").hide(100);

											$('#compromiso').val('');
											$('#compromisoVal').val('');
											$('#compromisoff').val('');

										}

										$("#facturas").hide('Drop');

										$("#facturasFilset").animate({
											width : '15%'

										});

										$("#tbodyfacturas").children("tr")
												.remove();

										// scalcularMontoDisponible();
									}

								});

						obj.parent('td').parent('tr').hide(100).remove();
						if ($("#tbodycompselec > tr").length < 2) {
							$("#tablacompselec").hide(100);

							$('#compromiso').val('');
							$('#compromisoVal').val('');
							$('#compromisoff').val('');

						}

						// alert($("#tablaCategoriaPartidasopg > tr").length);
						// calcularMontoDisponible();

					}

					function eliminarfactua(obj) {

						$(obj).hide(100).remove();

						if ($("#tbodyfacturas > tr").length < 1) {

							$("#facturas").hide('Drop');

							$("#facturasFilset").animate({
								width : '15%'

							});

						} else {

							i = 1;

							$("#tbodyfacturas > tr").each(
									function(index) {

										$(this).children("td.numFactura")
												.children("span").html(i);

										i++;

									});

						}

						calcularMontoDisponible();

					}
					;

					function calcularMontoDisponible(obj) {

						montoTotal1 = 0;

						$(
								'input[name=\'partidasCompromiso[monto][]\'][class=\'montoTotal\']')
								.each(function(index) {

									// console.log(this.value);

									dato1 = parseFloat(this.value);

									// console.log(dato1);
									montoTotal1 = (montoTotal1 + dato1);

									// console.log(montoTotal1);

								});

						var ivaMonto = 0;
						var dato1 = 0;

						$('input[name=\'factura[ivaMonto][]\']').each(
								function(index) {

									if ((this.value != '')) {

										dato1 = parseFloat(this.value);

										ivaMonto = (ivaMonto + dato1);

									}

								});

						// console.log(ivaMonto);

						montoTotal2 = 0;
						dato1 = 0;

						$(
								'input[name=\'partidasCompromiso[monto][]\'][class=\'imputacionPresupuestaria\']')
								.each(function(index) {

									// console.log(this.value);

									dato1 = parseFloat(this.value);
									montoTotal2 = (montoTotal2 + dato1);

								});

						console.log('monto = ' + montoTotal2 + " / iva = "
								+ ivaMonto);

						if (montoTotal2 < ivaMonto) {

							obj.val("");
							obj.focus();
							ivaMonto = 0;

							trobj = obj.parent("td").parent("tr");

							trobj.find('.ivaMonto').html("0,00");

							trobj.find('input[name=\'factura[ivaMonto][]\']')
									.val("0");

							alert("La suma de los ivas no debe ser mayor al monto comprometido del iva");

						}

						montoTotal2 = montoTotal2 - ivaMonto;

						montoTotal2 = number_format(parseFloat(montoTotal2), 2,
								',', '.');

						$('.montoDisponIva').html(montoTotal2);

						// -------------------------
						// alert(montoTotal2);

						var montoExento = 0;
						var dato1 = 0;

						$('input[name=\'factura[montoExento][]\']')
								.each(
										function(index) {

											if ((this.value != '')) {

												dato1 = QuitarCaracter(
														this.value, ".");
												montoExento = (montoExento + dato1);

											}

										});

						var montoSugeto = 0;
						var dato1 = 0;

						$('input[name=\'factura[montoSugeto][]\']')
								.each(
										function(index) {

											if ((this.value != '')) {

												dato1 = QuitarCaracter(
														this.value, ".");
												montoSugeto = (montoSugeto + dato1);

											}

										});

						montoTotal1 = montoTotal1 - (montoSugeto + montoExento);

						montoTotal1 = number_format(parseFloat(montoTotal1), 2,
								',', '.');

						$('.montoDispon').html(montoTotal1);

					}
					;

					function formato_num(obj, tipo) {

						monto = obj.val();

						if (parseInt(monto.split(",").length - 1) > 1) {

							alert("El separador de decimal debe ser ','");

							obj.val('');

							if (tipo != 3 || tipo != false) {

								trobj = obj.parent("td").parent("tr");

								trobj.find('.montototal').html("0,00");

								trobj.find(
										'input[name=\'factura[ivaMonto][]\']')
										.val("0,00");

							}

						} else {

							array = monto.split(",");

							if (array[1] != undefined) {

								num = format(array[0]) + "," + array[1];

							} else {

								num = format(array[0]);

							}

							obj.val(num);

							if (tipo !== false) {

								var montoExento = 0;
								var dato1 = 0;

								$('input[name=\'factura[montoExento][]\']')
										.each(
												function(index) {

													if ((this.value != '')) {

														dato1 = QuitarCaracter(
																this.value, ".");
														montoExento = (montoExento + dato1);

													}

												});

								var montoSugeto = 0;
								var dato1 = 0;

								$('input[name=\'factura[montoSugeto][]\']')
										.each(
												function(index) {

													if ((this.value != '')) {

														dato1 = QuitarCaracter(
																this.value, ".");
														montoSugeto = (montoSugeto + dato1);

													}

												});

								montoTotal1 = 0;

								$(
										'input[name=\'partidasCompromiso[monto][]\'][class=\'montoTotal\']')
										.each(
												function(index) {

													dato1 = parseFloat(this.value);

													montoTotal1 = (montoTotal1 + dato1);

												});

								// console.log(montoTotal1+" < "+montoExento + "
								// + "+montoSugeto);

								// console.log(montoTotal1+" < "+ (
								// montoExento+montoSugeto));

								if (montoTotal1 < montoExento + montoSugeto) {

									obj.val('');
									obj.focus();

									trobj = obj.parent("td").parent("tr");
									trobj.find('.ivaMonto').html("0,00");
									trobj
											.find(
													'input[name=\'factura[ivaMonto][]\']')
											.val("0");

									calcularMontoDisponible();

									alert("El monto de la factura no debe ser mayor al monto total de los compromisos");

									return;

								}

								obj.val(num);

								var montoExento = 0;
								var dato1 = 0;

								$('input[name=\'factura[montoExento][]\']')
										.each(
												function(index) {

													if ((this.value != '')) {

														dato1 = QuitarCaracter(
																this.value, ".");
														montoExento = (montoExento + dato1);

													}

												});

								var montoSugeto = 0;
								var dato1 = 0;

								$('input[name=\'factura[montoSugeto][]\']')
										.each(
												function(index) {

													if ((this.value != '')) {

														dato1 = QuitarCaracter(
																this.value, ".");
														montoSugeto = (montoSugeto + dato1);

													}

												});

								// MontoPartida(obj);

								if (tipo == 1) {

									trobj = obj.parent("td").parent("tr");

									ivaPorcentaje = trobj.find(
											'select[name=\'factura[iva][]\']')
											.val();

									// num = num.toString().replace('.',',');

									var monto = QuitarCaracter(num, ".");

									porsentaje = parseFloat((ivaPorcentaje * monto) / 100);

									porsentaje = porsentaje.toString().replace(
											'.', ',');

									array = porsentaje.split(",");

									if (array[1] != undefined) {

										porsentaje = format(array[0]) + ","
												+ array[1];

									} else {

										porsentaje = format(array[0]);

									}

									trobj.find('.ivaMonto').html(porsentaje);

									trobj
											.find(
													'input[name=\'factura[ivaMonto][]\']')
											.val(QuitarCaracter(porsentaje));

								}

							}

						}

						calcularMontoDisponible(obj);
					}

					function format(input) {
						var num = input.replace(/\./g, '');

						if (!isNaN(num)) {

							num = num.toString().split('').reverse().join('')
									.replace(/(?=\d*\.?)(\d{3})/g, '$1.');

							num = num.split('').reverse().join('').replace(
									/^[\.]/, '');

							return num;

						}

					}

					function ivaFactura(fila) {

						trobj = $(fila);

						formato_num(
								trobj
										.find('input[name=\'factura[montoSugeto][]\']'),
								1);

					}

					function AgregarRegistroFisico() {

						var tbody = $('#tablaBodyRespaldoFisico')[0];
						var dato1 = $("#pctaRespaldoFisico").val();

						var fila = document.createElement("tr");
						fila.className = 'normalNegro trRegfisico';

						var columna1 = document.createElement("td");
						columna1.setAttribute("valign", "top");
						columna1.appendChild(document.createTextNode(dato1));
						var input1 = document.createElement("input");
						input1.setAttribute("type", "hidden");
						input1.setAttribute("name", "RegistroFisico[]");
						input1.value = dato1;
						columna1.appendChild(input1);

						// OPCION DE ELIMINAR
						var columna2 = document.createElement("td");
						columna2.setAttribute("valign", "top");
						columna2.className = 'link';
						deleteLink = document.createElement("a");
						deleteLink.setAttribute("href", "javascript:void(0);");
						linkText = document.createTextNode("Eliminar");
						deleteLink.appendChild(linkText);
						columna2.appendChild(deleteLink);

						$(deleteLink).bind('click', function() {
							eliminarRegistroFisico(this);
						});

						fila.appendChild(columna1);
						fila.appendChild(columna2);
						tbody.appendChild(fila);

						if ($("#tablaBodyRespaldoFisico > tr").length < 3) {
							$('#tablaBodyRespaldoFisico').show('fade', 300);
							$("#cuerpoRespaldoFisico").show('fade', 300);
						}

					}

					function eliminarRegistroFisico(objA) {

						objTrs = $(objA).parents("tr.trRegfisico");

						objTrs.hide(100).remove();

						if ($("#tablaBodyRespaldoFisico > tr").length < 2) {

							$("#cuerpoRespaldoFisico").hide(200);

						}
					}

				});

function GenerarSigienteCadena(valor) {

	if (valor) {

		idCadenaSigiente = valor;
		Revisar();

	}

}

function QuitarCaracter(params, caracter) {

	if (params != "") {

		var num = parseInt(params.split(caracter).length - 1);
		var i = 0;

		monto = params;

		while (i <= num) {

			monto = monto.replace('.', '');
			i++;

		}

		monto = monto.replace(',', '.');
		monto = parseFloat(monto);

		return monto;

	} else {

		return 0;
	}
}

function Revisar() {
	var variable;
	var dato1;
	
	$('input[name=\'factura[montoExento][]\']')
	.each(
			function(index) {

				if (this.value != '') {

				 variable = QuitarCaracter(this.value, ".");
					$(this).val(variable);


				}

			});


$('input[name=\'factura[montoSugeto][]\']')
	.each(
			function(index) {

				if (this.value != '') {

					dato1 = QuitarCaracter(this.value, ".");
					$(this).val(dato1);
			}

			});

	Enviar();

}

function Enviar() {
	

	LlenarCadenaSigiente();
	
	if(tamanoFile > 0 )
	{
		$('#file_upload').uploadify('upload','*');
	}
	else
	{
		$('#formspg').attr('action',
		'../../acciones/sopg/sopg.php?accion=IngresarAccion');
		$('#formspg')[0].submit();
	}



}

function LlenarCadenaSigiente() {

	$("#idCadenaSigiente").remove();
	var div = $("#accionesEjecutar")[0];
	var input1 = document.createElement("input");
	input1.setAttribute("type", "hidden");
	input1.setAttribute("id", "idCadenaSigiente");
	input1.setAttribute("name", "idCadenaSigiente");
	input1.value = idCadenaSigiente;
	div.appendChild(input1);
}
