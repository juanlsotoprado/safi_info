<?php
	
	
	/*
	echo "lugar: ".$lugar;
	echo "   cadena inicial: ".$id_cadena_inicial;
	
	echo"<pre>";
	echo print_r($id_HijosCadena , true);
	echo"<pre>";
	*/
	
	
?>
<html>
	<head>
		<title>.:SAFI:. Desincoporraci&oacute;n</title>
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
				font-size: 10px;
			}
			/* IE 6 doesn't support max-height
			 * we use height instead, but this forces the menu to always be this tall
			 */
			* html .ui-autocomplete {
				height: 120px;
				font-size: 10px;
			}
		</style>
		
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript" src="../../js/constantes.js"></script>
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
		<script>

		var idArticulo='';
		var nombreArticulo;
		var serial;
		var marca;
		var precio;
		var modelo;
		var ubicacion;
		var idCadenaSiguiente;

		//funciones cadena
		function GenerarSiguienteCadena(valor)
		{

		if(valor)
		{        
		                                              
		idCadenaSiguiente = valor;

		//alert(idCadenaSiguiente);
		revisar();  

		}
		                                          
		}

		function LlenarCadenaSigiente(){

		$("#idCadenaSiguiente").remove();
		 var div = $("#accionesEjecutar")[0];
		  var input1 = document.createElement("input");
		 input1.setAttribute("type","hidden");
		  input1.setAttribute("id","idCadenaSiguiente");
		  input1.setAttribute("name","idCadenaSiguiente");
		input1.value= idCadenaSiguiente;
		  div.appendChild(input1);
		}
		//fin funciones cadena

		
			$().ready(function(){

				var objInputActivos = $("#inputSelectActivos");

				
				g_Calendar.setDateFormat('dd/mm/yyyy');

				if($('a[name="mensajes"]').length > 0){
					location.href = "#mensajes";
				}

				objInputActivos.autocomplete({
					source: function(request, response){

						$.ajax({
							url: "../../acciones/bienes/desincorporacion.php",
							Type: "post",
							dataType: "json",
							data: {
								accion: "BuscarListas",
								tipoRespuesta: 'json',
								key: request.term
							},
							success: function(json){
								var index = 0;
								var items = new Array();

								$.each(json, function(id, params){
							
									items[index++] = {
											label: 'Nombre: '+params.nombre+' -- Serial: '+params.serial+' -- Modelo: '+params.modelo,
											value: params.nombre,
											id: params.clave_bien,
											acta: params.acta_id,
											marca: params.marca_id,
											precio: params.precio,
											modelo: params.modelo,
											serial: params.serial,
											nombre: params.nombre,
											ubicacion: params.ubicacion
									};
								});
								response(items);
							}
						});
					},
					minLength: 1,
					select: function(event, ui)
					{
				    	//$("#cantidad").attr("readonly","readonly").val(1);
				   		idArticulo = ui.item.id;
						nombreArticulo = ui.item.nombre;
						serial = ui.item.serial;
						marca = ui.item.marca;
						precio = ui.item.precio;
						modelo = ui.item.modelo;
						ubicacion = ui.item.ubicacion;

						return true;
					}
				});



				
			});

			function eliminarArticulo(objA){

					$("#total").val($("#total").val()-1);

				   	objTrs = $(objA).parents("tr.trCaso");

				    objTrs.hide(200).remove(); 

				if($("#body_factura_head > tr").length < 2){
					
					$("#table_factura_head").hide(200);
							
				}  

			}

			function add_factura_head()
			{ 	
				
			  	if(idArticulo=='')
				{
					window.alert("No ha seleccionado ning\u00FAn articulo");
					return;
				}

				  var inputArticulo = $('input[name=\'ArrayDesincorporacion[id][]\'][value=\''+idArticulo+'\']');
				  
				  //revisar codigo
				  var inputModeloarticulo = inputArticulo.parents('tr.trCaso').find('input[name=\'ArrayDesincorporacion[serial][]\'][value=\''+serial+'\']');

				  if(inputModeloarticulo.length != 0){
					  
					  alert("El articulo ya fue agregado");
					    return;
				  }
						
					  var tbody = $('#body_factura_head')[0];
						
						var fila = document.createElement("tr");
						fila.className='normalNegro trCaso';

						//id del articulo

						var columna1 = document.createElement("td");
						columna1.setAttribute("valign","top");
						columna1.setAttribute("style","font-size:10px");
						columna1.appendChild(document.createTextNode(idArticulo));
						
						var input1 = document.createElement("input");
				 		input1.setAttribute("type","hidden");
				 		input1.setAttribute("name","ArrayDesincorporacion[id][]");
				 		input1.value= idArticulo;
				 		columna1.appendChild(input1);

				 		//nombre del articulo

				 		var columna2 = document.createElement("td");
				 		columna2.setAttribute("valign","top");
				 		columna2.setAttribute("style","font-size:10px");
				 		columna2.appendChild(document.createTextNode(nombreArticulo));

						//input hidden para el serial
						
						var columna9 = document.createElement("td");
						columna9.setAttribute("valign","top");
						columna9.setAttribute("style","font-size:10px");
						columna9.appendChild(document.createTextNode(serial));
					 		
				 		var input9 = document.createElement("input");
				 		input9.setAttribute("type","hidden");
				 		input9.setAttribute("name","ArrayDesincorporacion[serial][]");
				 		input9.value= serial;
				 		columna9.appendChild(input9);

						//input hidden para el modelo
					 		
				 		var input3 = document.createElement("input");
				 		input3.setAttribute("type","hidden");
				 		input3.setAttribute("name","ArrayDesincorporacion[modelo][]");
				 		input3.value= modelo;
				 		columna9.appendChild(input3);

						//input hidden para la marca_id
					 		
				 		var input4 = document.createElement("input");
				 		input4.setAttribute("type","hidden");
				 		input4.setAttribute("name","ArrayDesincorporacion[marca][]");
				 		input4.value= marca;
				 		columna9.appendChild(input4);

						//input hidden para el precio
				 		
				 		var input5 = document.createElement("input");
				 		input5.setAttribute("type","hidden");
				 		input5.setAttribute("name","ArrayDesincorporacion[precio][]");
				 		input5.value= precio;
				 		columna9.appendChild(input5);

						//input hidden para la ubicacion
				 		
				 		var input6 = document.createElement("input");
				 		input6.setAttribute("type","hidden");
				 		input6.setAttribute("name","ArrayDesincorporacion[ubicacion][]");
				 		input6.value= ubicacion;
				 		columna9.appendChild(input6);

						//OPCION DE ELIMINAR
				 		var columna8 = document.createElement("td");
				 		columna8.setAttribute("valign","top");
				 		columna8.setAttribute("align","center");
				 		columna8.className = 'link';
				 		deleteLink = document.createElement("a");
				 		deleteLink.setAttribute("href","javascript:void(0);");
				 		//linkText = document.createTextNode("Eliminar");
				 		//deleteLink.appendChild(linkText);
				 		columna8.appendChild(deleteLink);

				 		objDiv = document.createElement("div");
				 		objDiv.setAttribute("class","botonEliminar");
				 		columna8.appendChild(objDiv);
				 		
				 		$(objDiv).bind('click', function(){
							
				 			eliminarArticulo($(this));
							
				 		});			
						

						fila.appendChild(columna1);				
						fila.appendChild(columna2);
						fila.appendChild(columna9);
						fila.appendChild(columna8);	
											
						tbody.appendChild(fila);

						suma = parseInt($("#total").val())+1;
						$("#total").val(suma);

						suma2 = parseInt($("#total2").val())+1;
						$("#total2").val(suma2);

						$("#table_factura_head").show(400);

						
						idArticulo='';
						$("#inputSelectActivos").val("");
						
			}

			function ModificarSudebip()
			{	
			    $('#desincorporacionForm').attr('action','desincorporacion.php?accion=ModificarSudebip');
			    document.desincorporacionForm.submit();      
			}

			function ModificarActa()
			{
				$('#desincorporacionForm').attr('action','desincorporacion.php?accion=ModificarActa');
				document.desincorporacionForm.submit();
			}
			
			function revisar()
			{			  
				if($("#body_factura_head > tr").length < 2){
					
				   alert("No se registr\u00F3 ning\u00FAn activo para desincorporar");
				   return;		
				} 
			  	if($("#observaciones").val()=='')
				{
					window.alert("Debe especificar las observaciones");
					return;
				}
			  	if($("#hid_desde_itin").val()==''/*document.desincorporacionForm.hid_desde_itin.value==''*/)
				{
					window.alert("Indique la fecha");
					return;
				}
				if(confirm("Datos introducidos de manera correcta. \u00BFEst\u00e1 seguro que desea continuar?"))
			  	{
					LlenarCadenaSigiente();////////////////////////////llamar funcion de cadena
						  	
					document.desincorporacionForm.submit();
			  	}	
			  
			}
			
		</script>
	</head>
	
	<body class="normal">
		<form id="desincorporacionForm" name="desincorporacionForm" action="desincorporacion.php" method="post">
			<?php if(!$stringobj and !$stringobj2){?>
			<input
				id="hiddenAccion"
				type="hidden"
				name="accion"
				value="<?php 
					if($form->GetTipoOperacion() == FormularioDesincorporacionForm::TIPO_OPERACION_INSERTAR){
						echo 'guardar';
					} else if ($form->GetTipoOperacion() == FormularioDesincorporacionForm::TIPO_OPERACION_MODIFICAR){
						echo 'actualizar';
					}
				?>"
			/>
			<?php
			if ($form->GetTipoOperacion() == FormularioDesincorporacionForm::TIPO_OPERACION_MODIFICAR){
				echo '
			<input
				type="hidden"
				name="idDesincorporacion"
				value=".$desincorporacion->GetId()."
			>
				';
			}
			}
			?>
			<table align="center">
				<tr>
					<td><?php include(SAFI_VISTA_PATH . '/mensajes.php');?></td>
				</tr>
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" align="center" background="../../imagenes/fondo_tabla.gif"
						class="tablaalertas content" width="600px;" border="0">
							<tr>
								<td colspan="2" class="normalNegroNegrita header documentTitle">.: Desincorporaci&oacute;n de activos
											<?php 
											if($stringobj or $stringobj2){
											echo "Acta: ".$stringobj['acta_id']."".$stringobj2['acta_id'];
											}
											?>
									<?php /*
									if (
										$form->GetTipoOperacion() == FormularioDesincorporacionForm::TIPO_OPERACION_MODIFICAR
										&& $desincorporacionBien !== null
									){
										$desincorporacionBien->GetId() . " ";
									}*/

									?>:.
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<?php if(!$stringobj){?>
									<table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
										<tr >
											<td><div align="center" class="normalNegrita">Descripci&oacute;n</div></td>
											<td><div align="center" class="normalNegrita">Opci&oacute;n</div></td>
										</tr>
										<tr>
											<td>
												<div align="center">
													<span>
														<input type="text" name="material" id="inputSelectActivos" class="normalNegro" size="60">
													</span>
												</div>
											</td>
				
											<td>
												<div align="center" class="normal">
													<a href="javascript: add_factura_head(); " class="normal">Agregar
													</a>
												</div></td>
										</tr>
									</table>
									<?php }?>
									<br>
									<table class="tablaalertas" id="table_factura_head"  style="width: 100%; display: <?php if(!$stringobj and !$stringobj2){echo "none";}else{echo"display";}?>;" >
										<tbody id="body_factura_head" class="normal">
											<tr>
												<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Id</span></th>
												<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Nombre del art&iacuteculo</span></th>
												<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Serial</span></th>
												<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Opci&oacuten</span></th>
											</tr>
											<input type="hidden" name="total2" id="total2" value="0" />
											<?php 
											if($stringobj){
												for($i=0;$i<=$stringobj['total'];$i++){
											?>
												<tr>
													<script type="text/javascript">
													
													var tbody = $('#body_factura_head')[0];
													var fila = document.createElement("tr");
													fila.className='normalNegro trCaso';

													//id del articulo
													
													var columna1 = document.createElement("td");
													columna1.setAttribute("valign","top");
													columna1.setAttribute("align","center");
													columna1.setAttribute("style","font-size:10px");
													columna1.appendChild(document.createTextNode("<?=$stringobj['arti_id'][$i];?>"));

													var input1 = document.createElement("input");
											 		input1.setAttribute("type","hidden");
											 		input1.setAttribute("name","ArrayDesincorporacion[id][]");
											 		input1.value= <?=$stringobj['bien_id'][$i];?>;
											 		columna1.appendChild(input1);

													//nombre

													var columna2 = document.createElement("td");
													columna2.setAttribute("valign","top");
													columna2.setAttribute("align","center");
													columna2.setAttribute("style","font-size:10px");
													columna2.appendChild(document.createTextNode("<?=$stringobj['nombre'][$i];?>"));

													//serial

													var columna3 = document.createElement("td");
													columna3.setAttribute("valign","top");
													columna3.setAttribute("align","center");
													columna3.setAttribute("style","font-size:10px");
													columna3.appendChild(document.createTextNode("<?=$stringobj['serial'][$i];?>"));
													
													
													//OPCION DE ELIMINAR
											 		var columna8 = document.createElement("td");
											 		columna8.setAttribute("valign","top");
											 		columna8.setAttribute("align","center");
											 		columna8.className = 'link';
											 		deleteLink = document.createElement("a");
											 		deleteLink.setAttribute("href","javascript:void(0);");
											 		//linkText = document.createTextNode("Eliminar");
											 		//deleteLink.appendChild(linkText);
											 		columna8.appendChild(deleteLink);

											 		objDiv = document.createElement("div");
											 		objDiv.setAttribute("class","botonEliminar");
											 		columna8.appendChild(objDiv);

											 		fila.appendChild(columna1);
											 		fila.appendChild(columna2);
											 		fila.appendChild(columna3);
											 		fila.appendChild(columna8);
											 		tbody.appendChild(fila);

													$(objDiv).bind('click', function(){
														if($("#body_factura_head").find("tr.trCaso").length > 1)
														{
															$("#total").val($("#total").val()-1);
											 				eliminarArticulo($(this));
														}
														else
														{
															alert("El acta debe contener al menos un art\u00edculo");
														}
														
											 		});

				 									</script>
												</tr>
											<?php
												}
											}
											
											if($stringobj2){
												for($i=0;$i<=$stringobj2['total'];$i++){
											?>
												<tr>
													<script type="text/javascript">
													
													var tbody = $('#body_factura_head')[0];
													var fila = document.createElement("tr");
													fila.className='normalNegro trCaso';

													//id del articulo
													
													var columna1 = document.createElement("td");
													columna1.setAttribute("valign","top");
													//columna1.setAttribute("align","center");
													columna1.setAttribute("style","font-size:10px");
													columna1.appendChild(document.createTextNode("<?=$stringobj2['bien_id'][$i];?>"));

													var input1 = document.createElement("input");
											 		input1.setAttribute("type","hidden");
											 		input1.setAttribute("name","ArrayDesincorporacion[id][]");
											 		input1.value= "<?=$stringobj2['bien_id'][$i];?>";
											 		columna1.appendChild(input1);

													//nombre

													var columna2 = document.createElement("td");
													columna2.setAttribute("valign","top");
													columna2.setAttribute("style","font-size:10px");
													columna2.appendChild(document.createTextNode("<?=$stringobj2['nombre'][$i];?>"));

													//serial

													var columna9 = document.createElement("td");
													columna9.setAttribute("valign","top");
													columna9.setAttribute("style","font-size:10px");
													columna9.appendChild(document.createTextNode("<?=$stringobj2['serial'][$i];?>"));

											 		var input9 = document.createElement("input");
											 		input9.setAttribute("type","hidden");
											 		input9.setAttribute("name","ArrayDesincorporacion[serial][]");
											 		input9.value= "<?=$stringobj2['serial'][$i];?>";
											 		columna9.appendChild(input9);

													//input hidden para el modelo
											 		
											 		var input3 = document.createElement("input");
											 		input3.setAttribute("type","hidden");
											 		input3.setAttribute("name","ArrayDesincorporacion[modelo][]");
											 		input3.value= "<?=$stringobj2['modelo'][$i];?>";
											 		columna9.appendChild(input3);

													//input hidden para la marca_id
												 		
											 		var input4 = document.createElement("input");
											 		input4.setAttribute("type","hidden");
											 		input4.setAttribute("name","ArrayDesincorporacion[marca][]");
											 		input4.value= "<?=$stringobj2['marca_id'][$i];?>";
											 		columna9.appendChild(input4);

													//input hidden para el precio
											 		
											 		var input5 = document.createElement("input");
											 		input5.setAttribute("type","hidden");
											 		input5.setAttribute("name","ArrayDesincorporacion[precio][]");
											 		input5.value= "<?=$stringobj2['precio'][$i];?>";
											 		columna9.appendChild(input5);

													//input hidden para el ubicacion
											 		
											 		var input6 = document.createElement("input");
											 		input6.setAttribute("type","hidden");
											 		input6.setAttribute("name","ArrayDesincorporacion[ubicacion][]");
											 		input6.value= "<?=$stringobj2['ubicacion'][$i];?>";
											 		columna9.appendChild(input6);
													
													//OPCION DE ELIMINAR
											 		var columna8 = document.createElement("td");
											 		columna8.setAttribute("valign","top");
											 		columna8.setAttribute("align","center");
											 		columna8.className = 'link';
											 		deleteLink = document.createElement("a");
											 		deleteLink.setAttribute("href","javascript:void(0);");
											 		columna8.appendChild(deleteLink);

											 		objDiv = document.createElement("div");
											 		objDiv.setAttribute("class","botonEliminar");
											 		columna8.appendChild(objDiv);

											 		fila.appendChild(columna1);
											 		fila.appendChild(columna2);
											 		fila.appendChild(columna9);
											 		fila.appendChild(columna8);
											 		tbody.appendChild(fila);

											 		suma = parseInt($("#total2").val())+1;
													$("#total2").val(suma);

													$(objDiv).bind('click', function(){
														if($("#body_factura_head").find("tr.trCaso").length > 1)
														{
															$("#total2").val($("#total2").val()-1);
											 				eliminarArticulo($(this));
														}
														else
														{
															alert("El acta debe contener al menos un art\u00edculo");
														}
														
											 		});

				 									</script>
												</tr>
											<?php
												}
											}?>
										</tbody>
									</table>
									<table align="center" border = "0">
										<tr>
											<td align="center">
												<div class="normal">
													<strong>Observaciones</strong>
												</div>
												<textarea name="observaciones" id="observaciones" cols="50" rows="5"><?php if($stringobj){echo $stringobj['observaciones'];}else if($stringobj2){echo $stringobj2['observaciones'];}?></textarea>
												<input type="hidden" name="total" id="total" value="0" />
											</td>
										</tr>
										<?php 
										if(!$stringobj and !$stringobj2){
										?>
										<tr align="center">
											<td class="normalNegrita">&ensp;&ensp;Fecha del acta:
											<input type="text" size="8" id="hid_desde_itin"
												name="hid_desde_itin" class="normalNegro" readonly /> 
												<a
												href="javascript:void(0);"
												onclick="g_Calendar.show(event, 'hid_desde_itin');"
												title="Mostrar Calendario" style="display: on" id="fecha"> <img
												src="../../js/lib/calendarPopup/img/calendar.gif"
												class="cp_img" width="25" height="20" alt="Open popup calendar" />
												</a>
											</td>
										</tr>
										<?php }?>
										<tr>
											<td align="center">
												<div align="center" id="accionesEjecutar">
											    	<?php
											    	if(!$stringobj and !$stringobj2){//si es registro
											    	//boton de cadena generar documento 
											     		foreach ($GLOBALS['SafiRequestVars']['opciones'] as $index){ ?>
											           	<span class="cadena"> <input type="button"
															value="<?php echo $index['wfop_nombre'];?>"
															id="<?php echo $index['wfop_descrip'];?>"
															onclick="GenerarSiguienteCadena(<?php echo $index['id_cadena_hijo'];?>,0)" />
														</span>
											        <?php }
											        }//FIN si es registro
											        if($stringobj)
													{
											        ?>
											        	<input type="hidden" name="acta_id" id="acta_id" value="<?php echo $stringobj['acta_id'];?>" />
											           	<input type="button" value="Modificar" onclick="ModificarSudebip()" />
											        <?php 
													}
													if($stringobj2)
													{
											        ?>
        												<input type="hidden" name="acta_id" id="acta_id" value="<?php echo $stringobj2['acta_id'];?>" />
									           			<input type="button" value="Modificar Acta" onclick="ModificarActa()" />
											        <?php 
													}
											        ?>
											        
											    </div>
											    
											    
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>