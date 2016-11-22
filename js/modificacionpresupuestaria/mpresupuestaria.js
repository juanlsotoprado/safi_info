var	tamanoFile;	
var dependencia = false,
    pctaAsociado = null,
    disponibilidad = null,
    dependencia  = null,
	compImputa  = null,
    tamanoFile = 0,
	modificarComp = null,
    pmodImputa = null,
    pmodRespaldo = null,
	pctaAsociado  = false,
	pctaAsociadoval  = false,
	respaldosDigitales = 0,
    respaldosFisicos = 0,
	idCadenaSigiente = null,
	compModMontoTotal = 0,
    params = new Array();
    regisFisDigiEli = new Array();
    regisNombreDigital = new Array();
		$().ready(function(){
			
// /////////////////////////////////////////////////////////////////////////////////////
// Funcion auto// ////////////////////////////////////////////////////////

				
			       dependencia = $("#unidadDependencia").val();

					$('#divPartida').hide();
					$('#tablacategoria').hide();
					$('#trcategoriaesp').hide();
					$('.patidaseleccionada').hide();
					$("#cuerpoCategoriaPartida").hide();
					$("#cuerpoCategoriaPartidaReceptora").hide();
					$("#cuerpoCategoriaPartidaCredito").hide();
					$("#cuerpoCategoriaPartidaDisminuir").hide();
					$("#cuerpoRespaldoFisico").hide();
					$("#AgregarRespaldoFisico").hide();
					$('#trActualesFis').hide();
					$('#trActualesDigital').hide();
					$('#opcionModificar').hide('');

					if($("#accionMP").val() == 2){
						
						$("#nombreAccion").html('Monto a ceder:');

					}else if($("#accionMP").val() == 1){
						
						$("#accionpartida").parent('div').hide('blind',120);
						
						$("#nombreAccion").html('Monto a acreditar:');
						
					}else{
						
						$("#accionpartida").parent('div').hide('blind',120);
						
						$("#nombreAccion").html('Monto a disminuir:');
						
					}

					
					$("#accionpartida").change(function(){
						
						if($("#accionpartida").val() == '1'){
							
							  $("#accionArealizar").html("<b id='accionArealizar'>Seleccione las partidas que ceder\u00e1n monto</b>");  
							
						}else{
							
							  $("#accionArealizar").html("<b id='accionArealizar'>Seleccione las partidas a acreditarle monto</b>");   

							
						}
						
						
					});
					
					
					
					if($("#pmodAmodificar").html() != null){
						
		
						$('#pmodAmodificar').css({'color':'white'});
                         
						data = $("#pmodAmodificar").html();

						$('#accionesEjecutar').hide(100);

							
				         	input5 = document.createElement("input");
					 		input5.setAttribute("type","button");
					 		input5.setAttribute("name","modificar");
					 		input5.setAttribute("onclick",'Revisar();');
					 		input5.value= 'Modificar';
					 		$('#opcionModificar')[0].appendChild(input5);
					 		
					 		
					 		input5 = document.createElement("input");
					 		input5.setAttribute("type","button");
					 		input5.setAttribute("name","cancelar");
					 		input5.setAttribute("id","cancelar");
					 		input5.value= 'Cancelar';
					 		$('#opcionModificar')[0].appendChild(input5);
					 		
					 		
					 		input5 = document.createElement("input");
					 		input5.setAttribute("type","hidden");
					 		input5.setAttribute("name","idPmod");
					 		input5.value= data;
					 		
					 		$('#opcionModificar')[0].appendChild(input5);
					 		
					 		$("#cancelar").click(function() {
					 		    event.preventDefault();
					 		    history.back(1);
					 		});
							
					 	
							$('#opcionModificar').show(100);
							
			

				
						

					}
				
					
					
					if(pmodImputa){  
						
						if($("#accionMP").val() == 2){
							
							
							$.each(pmodImputa,function(id,val){
								

								
								if(val.tipo == '0'){
								

								   var tbody = $('#tablaCategoriaPartida')[0];

								 
									
								    if(val.tipoImpu > 0){
			                        	   
										var dato1= 'Proyecto';
										var dato2= val.proyecto.nombre;
										var dato4= val.proyectoEspecifica.nombre;
										var dato3= val.proyectoEspecifica.centroGestor+'/'+val.proyectoEspecifica.centroCosto;
										
										var proyAcc = val.proyecto.id;
										var proyAccEsp =val.proyectoEspecifica.id;
										
			                           }else{

			                        	var dato1= 'Ac.Centralizada';

			                                 
			        						var dato2= val.accionCentralizada.nombre;
			    							var dato4= val.CentralizadaEspecifica.nombre;
			    							var dato3= val.CentralizadaEspecifica.centroGestor+'/'+val.CentralizadaEspecifica.centroCosto;
			    							
			    							var proyAcc = val.accionCentralizada.id;
			    							var proyAccEsp =val.CentralizadaEspecifica.id;
			                           }

			                           
			                           

										var dato5= val.partida.id;
										var dato6= val.partida.nombre;
									    var dato9 = GetDisponibilidad(val.tipoImpu,proyAcc,proyAccEsp,val.partida.id);
									
									

								    dato11 =  number_format(dato9,2,',','.');
								    if(parseInt(dato9) <= 0){

								          alert("Esta partida no tiene disponibilidad");
								          $('#Partida').val('');
								          if($('#Partidadenominacion').html() != ''){

												 $('#Partidadenominacion').html('');
												 $('#Partidamonto').val('');
											     $('.patidaseleccionada').hide();

							         }
								          $('#Partida').focus();
								          
								          return;
								          
								            }
								    
									var fila = document.createElement("tr");
									fila.className='normalNegro trCaso';
								         
									var columna1 = document.createElement("td");
									columna1.setAttribute("valign","top");
									columna1.appendChild(document.createTextNode(dato1));
									var input1 = document.createElement("input");
									input1.setAttribute("type","hidden");
									input1.setAttribute("name","mpresupuestariadisp[tipo][]");
									input1.value=val.tipoImpu;
									columna1.appendChild(input1);
									
									var columna2 = document.createElement("td");
									columna2.setAttribute("valign","top");
									columna2.appendChild(document.createTextNode(dato2));
									var input2 = document.createElement("input");
									input2.setAttribute("type","hidden");
									input2.setAttribute("name","mpresupuestariadisp[codProyAcc][]");
									input2.value= proyAcc;
									columna2.appendChild(input2);



									var columna3 = document.createElement("td");
									columna3.setAttribute("valign","top");
									columna3.appendChild(document.createTextNode(dato3));
									

									var columna4 = document.createElement("td");
									columna4.setAttribute("valign","top");
									columna4.appendChild(document.createTextNode(dato4));
									var input4 = document.createElement("input");
									input4.setAttribute("type","hidden");
									input4.setAttribute("name","mpresupuestariadisp[codProyAccEsp][]");
									input4.value=proyAccEsp;
									columna4.appendChild(input4);

									var columna5 = document.createElement("td");
									columna5.setAttribute("valign","top");
									columna5.setAttribute("class","tdPartida");
									columna5.appendChild(document.createTextNode(dato5));
									var input5 = document.createElement("input");
									input5.setAttribute("type","hidden");
									input5.setAttribute("name","mpresupuestariadisp[codPartida][]");
									input5.value= val.partida.id;
									columna5.appendChild(input5);

									var columna6 = document.createElement("td");
									columna6.setAttribute("valign","top");
									columna6.appendChild(document.createTextNode(dato6));

							 		
									var columna9 = document.createElement("td");
									columna9.setAttribute("class","tdDisponibilidad");
									columna9.setAttribute("align","right");
									columna9.setAttribute("style","color:red; font-weight:bold;");
									columna9.appendChild(document.createTextNode(dato11));
									

		
									
									
									

									var columna7 = document.createElement("td");
							 		columna7.setAttribute("valign","baseline");
							 		columna7.setAttribute("class","tdMonto");
							 		columna7.setAttribute("style","padding:0;");
							 		var input7 = document.createElement("input");
							 		input7.setAttribute("type","text");
							 		input7.setAttribute("name","mpresupuestariadisp[monto][]");
							 		input7.setAttribute("autocomplete","off");
							 		input7.setAttribute("style","height:22px;width:70;margin:0;border:2px #D8D8D8 dotted solid; font-size:10px" );
							 		input7.value= val.monto;
							 		columna7.appendChild(input7);
							 		
					                $(input7).keyup(function(){
							 			
							 			formato_num($(this));

							 		});
									
									
									
									// OPCION DE CAMBIAR
									var columna8 = document.createElement("td");
									columna8.setAttribute("valign","top");
									columna8.setAttribute("align","center");
									columna8.className = 'link';
									deleteLink = document.createElement("a");
									deleteLink.setAttribute("href","javascript:void(0);");
									linkText = document.createTextNode("Eliminar");
									deleteLink.appendChild(linkText);
									columna8.appendChild(deleteLink);


									
									$(deleteLink).bind('click', function(){
										
										eliminarPartidaCede(this);
										
									});


									fila.appendChild(columna1);				
									fila.appendChild(columna3);
									fila.appendChild(columna2);				
									fila.appendChild(columna4);
									fila.appendChild(columna5);				
									fila.appendChild(columna6);
									fila.appendChild(columna9);
									fila.appendChild(columna7);
									fila.appendChild(columna8);
									
									tbody.appendChild(fila);


									
										$('#tablaCategoriaPartida').show('fade',300);
										$("#cuerpoCategoriaPartida").show('fade',300);
										$("#accionArealizar").effect('highlight',1000 ,function(){
										$("#accionArealizar").html("<b id='accionArealizar'>Seleccione las partidas que recibir\u00e1n monto</b>");	
											
											
										});
										
										

										$("#accionArealizar").effect('pulsate', 100);	
										$('#divPartida').hide();
										$('#tablacategoria').hide();
										$('#trcategoriaesp').hide();
										$('.patidaseleccionada').hide();
									    $('#selectProyAcc').val('');
										$('#Partida').val('');

										
									
								}else{
									
									   var tbody = $('#tablaCategoriaPartidaReceptora')[0];

									 
										
									    if(val.tipoImpu > 0){
				                        	   
											var dato1= 'Proyecto';
											var dato2= val.proyecto.nombre;
											var dato4= val.proyectoEspecifica.nombre;
											var dato3= val.proyectoEspecifica.centroGestor+'/'+val.proyectoEspecifica.centroCosto;
											
											var proyAcc = val.proyecto.id;
											var proyAccEsp =val.proyectoEspecifica.id;
											
				                           }else{

				                        	var dato1= 'Ac.Centralizada';

				        						var dato2= val.accionCentralizada.nombre;
				    							var dato4= val.CentralizadaEspecifica.nombre;
				    							var dato3= val.CentralizadaEspecifica.centroGestor+'/'+val.CentralizadaEspecifica.centroCosto;
				    							
				    							var proyAcc = val.accionCentralizada.id;
				    							var proyAccEsp =val.CentralizadaEspecifica.id;
				                           }

				                           
				                           

											var dato5= val.partida.id;
											var dato6= val.partida.nombre;
										    var dato9 = GetDisponibilidad(val.tipoImpu,proyAcc,proyAccEsp,val.partida.id);
										    

										
								    dato11 =  number_format(dato9,2,',','.');
				 
									var fila = document.createElement("tr");
									fila.className='normalNegro trCaso2';
								         
									var columna1 = document.createElement("td");
									columna1.setAttribute("valign","top");
									columna1.appendChild(document.createTextNode(dato1));
									var input1 = document.createElement("input");
									input1.setAttribute("type","hidden");
									input1.setAttribute("name","mpresupuestaria[tipo][]");
									input1.value=val.tipoImpu;
									columna1.appendChild(input1);
									
									var columna2 = document.createElement("td");
									columna2.setAttribute("valign","top");
									columna2.appendChild(document.createTextNode(dato2));
									var input2 = document.createElement("input");
									input2.setAttribute("type","hidden");
									input2.setAttribute("name","mpresupuestaria[codProyAcc][]");
									input2.value= proyAcc;
									columna2.appendChild(input2);



									var columna3 = document.createElement("td");
									columna3.setAttribute("valign","top");
									columna3.appendChild(document.createTextNode(dato3));
									

									var columna4 = document.createElement("td");
									columna4.setAttribute("valign","top");
									columna4.appendChild(document.createTextNode(dato4));
									var input4 = document.createElement("input");
									input4.setAttribute("type","hidden");
									input4.setAttribute("name","mpresupuestaria[codProyAccEsp][]");
									input4.value=proyAccEsp;
									columna4.appendChild(input4);

									var columna5 = document.createElement("td");
									columna5.setAttribute("valign","top");
									columna5.setAttribute("class","tdPartida");
									columna5.appendChild(document.createTextNode(dato5));
									var input5 = document.createElement("input");
									input5.setAttribute("type","hidden");
									input5.setAttribute("name","mpresupuestaria[codPartida][]");
									input5.value= val.partida.id;
									columna5.appendChild(input5);

									var columna6 = document.createElement("td");
									columna6.setAttribute("valign","top");
									columna6.appendChild(document.createTextNode(dato6));
									
							
									var columna7 = document.createElement("td");
							 		columna7.setAttribute("valign","baseline");
							 		columna7.setAttribute("class","tdMonto");
							 		columna7.setAttribute("style","padding:0;");
							 		var input7 = document.createElement("input");
							 		input7.setAttribute("type","text");
							 		input7.setAttribute("name","mpresupuestaria[monto][]");
							 		input7.setAttribute("autocomplete","off");
							 		input7.setAttribute("style","height:22px;width:70;margin:0;border:2px #D8D8D8 dotted solid; font-size:10px" );
							 		input7.value= val.monto;
							 		columna7.appendChild(input7);
							 		
							 	

	                                 $(input7).keyup(function(){
	 						 			
	 						 			formato_num($(this));

	 						 		});

							 		
									var columna9 = document.createElement("td");
									columna9.setAttribute("class","tdDisponibilidad");
									columna9.setAttribute("align","right");
									columna9.appendChild(document.createTextNode(dato11));
									

									// OPCION DE CAMBIAR
									var columna8 = document.createElement("td");
									columna8.setAttribute("valign","top");
									columna8.setAttribute("align","center");
									columna8.className = 'link';
									deleteLink = document.createElement("a");
									deleteLink.setAttribute("href","javascript:void(0);");
									linkText = document.createTextNode("Eliminar");
									deleteLink.appendChild(linkText);
									columna8.appendChild(deleteLink);


									
									$(deleteLink).bind('click', function(){
										EliminarPartidaReceptora(this);
									});


									fila.appendChild(columna1);				
									fila.appendChild(columna3);
									fila.appendChild(columna2);				
									fila.appendChild(columna4);
									fila.appendChild(columna5);				
									fila.appendChild(columna6);
									fila.appendChild(columna9);
									fila.appendChild(columna7);	
									fila.appendChild(columna8);
									
									tbody.appendChild(fila);

									
							          

									if($("#tablaCategoriaPartidaReceptora > tr").length < 3){
									
										$('#tablaCategoriaPartidaReceptora').show('fade',300);
										$("#cuerpoCategoriaPartidaReceptora").show('fade',300);

									}
									
									

												
									 $Obj3 = $("#tablaCategoriaPartida").children("tr.trCaso").children('.tdDisponibilidad');
					 
									 montoTotal1 = 0;
					    	
									 $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {
										 

						      	            if(this.value != ''){
						      	            	
						      	         	   dato1  = parseFloat(this.value);
						      	         	   
						      	               montoTotal1 = (montoTotal1 + dato1);	
						      	                
						      	            }
						      	            
						      	 	 	 });
									 
					    	        	 
					    	        	 var dispon = $($Obj3).html();
					 
					    	        	 var num = parseInt(dispon.split(".").length -1);
					    	        	 var i=0;
					    	        	 
					    	        	 while(i<= num){
					    	        	 
					    	        	    dispon = dispon.replace('.','');
					    	        	 
					    	        	    i++;
					    	        	    
					    	              }
					    	        	 

					    	        		 var montoTotal1 = 0;
					    	                 var dato1 = 0 ;
					    	                 $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {

					    	                    if(this.value != ''){
					    	                 	   
					    	                 	  
					    	                    dato1  = parseFloat(this.value);
					    	                    	
					    	                    montoTotal1 = (montoTotal1+dato1);	
					    	                    
					    	                 
					    	                    }
					    	                     
					    	         		    
					    	         	 		 
					    	         	 	 });
					    	                 
					    	               
					    	                var  montoTotal2 = number_format(montoTotal1,2,',','.');
					    	             
					    	                
					    	                
					    	                $('#totalDisp').html(montoTotal2);	   
					    	        		$('#montoTotal').html(montoTotal2);
					    	        		

					    	        		
					    	        		
								}
								
								
							});
							
						

						}else if($("#accionMP").val() == 1){
							
							
							
							
							$.each(pmodImputa,function(id,val){
								
							
						
			    			   var tbody = $('#tablaCategoriaPartidaCredito')[0];

							    if(val.tipoImpu > 0){
		                        	   
									var dato1= 'Proyecto';
									var dato2= val.proyecto.nombre;
									var dato4= val.proyectoEspecifica.nombre;
									var dato3= val.proyectoEspecifica.centroGestor+'/'+val.proyectoEspecifica.centroCosto;
									
									var proyAcc = val.proyecto.id;
									var proyAccEsp =val.proyectoEspecifica.id;
									
		                           }else{

		                        	var dato1= 'Ac.Centralizada';

		                                 
		        						var dato2= val.accionCentralizada.nombre;
		    							var dato4= val.CentralizadaEspecifica.nombre;
		    							var dato3= val.CentralizadaEspecifica.centroGestor+'/'+val.CentralizadaEspecifica.centroCosto;
		    							
		    							var proyAcc = val.accionCentralizada.id;
		    							var proyAccEsp =val.CentralizadaEspecifica.id;
		                           }

		                           
		                           

									var dato5= val.partida.id;
									var dato6= val.partida.nombre;
								    var dato9 = GetDisponibilidad(val.tipoImpu,proyAcc,proyAccEsp,val.partida.id);

							    dato11 =  number_format(dato9,2,',','.');

								var fila = document.createElement("tr");
								fila.className='normalNegro trCaso';
							         
								var columna1 = document.createElement("td");
								columna1.setAttribute("valign","top");
								columna1.appendChild(document.createTextNode(dato1));
								var input1 = document.createElement("input");
								input1.setAttribute("type","hidden");
								input1.setAttribute("name","mpresupuestaria[tipo][]");
								input1.value=val.tipoImpu;
								columna1.appendChild(input1);
								
								var columna2 = document.createElement("td");
								columna2.setAttribute("valign","top");
								columna2.appendChild(document.createTextNode(dato2));
								var input2 = document.createElement("input");
								input2.setAttribute("type","hidden");
								input2.setAttribute("name","mpresupuestaria[codProyAcc][]");
								input2.value= proyAcc;
								columna2.appendChild(input2);



								var columna3 = document.createElement("td");
								columna3.setAttribute("valign","top");
								columna3.appendChild(document.createTextNode(dato3));
								

								var columna4 = document.createElement("td");
								columna4.setAttribute("valign","top");
								columna4.appendChild(document.createTextNode(dato4));
								var input4 = document.createElement("input");
								input4.setAttribute("type","hidden");
								input4.setAttribute("name","mpresupuestaria[codProyAccEsp][]");
								input4.value=proyAccEsp;
								columna4.appendChild(input4);

								var columna5 = document.createElement("td");
								columna5.setAttribute("valign","top");
								columna5.setAttribute("class","tdPartida");
								columna5.appendChild(document.createTextNode(dato5));
								var input5 = document.createElement("input");
								input5.setAttribute("type","hidden");
								input5.setAttribute("name","mpresupuestaria[codPartida][]");
								input5.value= val.partida.id;
								columna5.appendChild(input5);

								var columna6 = document.createElement("td");
								columna6.setAttribute("valign","top");
								columna6.appendChild(document.createTextNode(dato6));
								
						
								var columna7 = document.createElement("td");
						 		columna7.setAttribute("valign","baseline");
						 		columna7.setAttribute("class","tdMonto");
						 		columna7.setAttribute("style","padding:0;");
						 		var input7 = document.createElement("input");
						 		input7.setAttribute("autocomplete","off");
						 		input7.setAttribute("type","text");
						 		input7.setAttribute("name","mpresupuestaria[monto][]");
						 		input7.setAttribute("style","height:22px;width:70;margin:0;border:2px #D8D8D8 dotted solid; font-size:10px" );
						 		input7.value= val.monto;
						 		columna7.appendChild(input7);
						 		
                                 $(input7).keyup(function(){
						 			
						 			formato_num($(this));

						 		});

						 		
								var columna9 = document.createElement("td");
								columna9.setAttribute("class","tdDisponibilidad");
								columna9.setAttribute("align","right");
								columna9.appendChild(document.createTextNode(dato11));
								

								// OPCION DE CAMBIAR
								var columna8 = document.createElement("td");
								columna8.setAttribute("valign","top");
								columna8.setAttribute("align","center");
								columna8.className = 'link';
								deleteLink = document.createElement("a");
								deleteLink.setAttribute("href","javascript:void(0);");
								linkText = document.createTextNode("Eliminar");
								deleteLink.appendChild(linkText);
								columna8.appendChild(deleteLink);


								
								$(deleteLink).bind('click', function(){
									CambiarPartida(this);
								});

								

								if($("#tablaCategoriaPartidaCredito > tr").length < 3){
								
									$('#tablaCategoriaPartidaCredito').show('fade',300);
									$("#cuerpoCategoriaPartidaCredito").show('fade',300);

								}


								fila.appendChild(columna1);				
								fila.appendChild(columna3);
								fila.appendChild(columna2);				
								fila.appendChild(columna4);
								fila.appendChild(columna5);				
								fila.appendChild(columna6);
								fila.appendChild(columna9);
								fila.appendChild(columna7);	
								fila.appendChild(columna8);
								
								tbody.appendChild(fila);

								
								$('#fieldset2').hide();
								
							
								
				       });	 

						}else{
							
							$.each(pmodImputa,function(id,val){
							
			    			   var tbody = $('#tablaCategoriaPartidaDisminuir')[0];

							    if(val.tipoImpu > 0){
		                        	   
									var dato1= 'Proyecto';
									var dato2= val.proyecto.nombre;
									var dato4= val.proyectoEspecifica.nombre;
									var dato3= val.proyectoEspecifica.centroGestor+'/'+val.proyectoEspecifica.centroCosto;
									
									var proyAcc = val.proyecto.id;
									var proyAccEsp =val.proyectoEspecifica.id;
									
		                           }else{

		                        	var dato1= 'Ac.Centralizada';
                                       
		                             
		        						var dato2= val.accionCentralizada.nombre;
		    							var dato4= val.CentralizadaEspecifica.nombre;
		    							var dato3= val.CentralizadaEspecifica.centroGestor+'/'+val.CentralizadaEspecifica.centroCosto;
		    							
		    							var proyAcc = val.accionCentralizada.id;
		    							var proyAccEsp =val.CentralizadaEspecifica.id;
		                           }

		                           
		                           

									var dato5= val.partida.id;
									var dato6= val.partida.nombre;
									
								    var dato9 = GetDisponibilidad(val.tipoImpu,proyAcc,proyAccEsp,val.partida.id);
								    
							    dato11 =  number_format(dato9,2,',','.');
							    if(parseInt(dato9) <= 0){

							          alert("Esta partida no tiene disponibilidad");
							          $('#Partida').val('');
							          if($('#Partidadenominacion').html() != ''){

											 $('#Partidadenominacion').html('');
											 $('#Partidamonto').val('');
										     $('.patidaseleccionada').hide();

						         }
							          $('#Partida').focus();
							          
							          return;
							          
							            }

								var fila = document.createElement("tr");
								fila.className='normalNegro trCaso';
							         
								var columna1 = document.createElement("td");
								columna1.setAttribute("valign","top");
								columna1.appendChild(document.createTextNode(dato1));
								var input1 = document.createElement("input");
								input1.setAttribute("type","hidden");
								input1.setAttribute("name","mpresupuestaria[tipo][]");
								input1.value=val.tipoImpu;
								columna1.appendChild(input1);
								
								var columna2 = document.createElement("td");
								columna2.setAttribute("valign","top");
								columna2.appendChild(document.createTextNode(dato2));
								var input2 = document.createElement("input");
								input2.setAttribute("type","hidden");
								input2.setAttribute("name","mpresupuestaria[codProyAcc][]");
								input2.value= proyAcc;
								columna2.appendChild(input2);



								var columna3 = document.createElement("td");
								columna3.setAttribute("valign","top");
								columna3.appendChild(document.createTextNode(dato3));
								

								var columna4 = document.createElement("td");
								columna4.setAttribute("valign","top");
								columna4.appendChild(document.createTextNode(dato4));
								var input4 = document.createElement("input");
								input4.setAttribute("type","hidden");
								input4.setAttribute("name","mpresupuestaria[codProyAccEsp][]");
								input4.value=proyAccEsp;
								columna4.appendChild(input4);

								var columna5 = document.createElement("td");
								columna5.setAttribute("valign","top");
								columna5.setAttribute("class","tdPartida");
								columna5.appendChild(document.createTextNode(dato5));
								var input5 = document.createElement("input");
								input5.setAttribute("type","hidden");
								input5.setAttribute("name","mpresupuestaria[codPartida][]");
								input5.value= val.partida.id;
								columna5.appendChild(input5);

								var columna6 = document.createElement("td");
								columna6.setAttribute("valign","top");
								columna6.appendChild(document.createTextNode(dato6));
								
						
								var columna7 = document.createElement("td");
						 		columna7.setAttribute("valign","baseline");
						 		columna7.setAttribute("class","tdMonto");
						 		columna7.setAttribute("style","padding:0;");
						 		var input7 = document.createElement("input");
						 		input7.setAttribute("type","text");
						 		input7.setAttribute("autocomplete","off");
						 		input7.setAttribute("name","mpresupuestaria[monto][]");
						 		input7.setAttribute("style","height:22px;width:70;margin:0;border:2px #D8D8D8 dotted solid; font-size:10px" );
						 		input7.value= val.monto;
						 		columna7.appendChild(input7);
						 		
						 		
                                 $(input7).keyup(function(){
						 			
						 			formato_num($(this));

						 		});

						 		
								var columna9 = document.createElement("td");
								columna9.setAttribute("class","tdDisponibilidad");
								columna9.setAttribute("align","right");
								columna9.appendChild(document.createTextNode(dato11));
								

								// OPCION DE CAMBIAR
								var columna8 = document.createElement("td");
								columna8.setAttribute("valign","top");
								columna8.setAttribute("align","center");
								columna8.className = 'link';
								deleteLink = document.createElement("a");
								deleteLink.setAttribute("href","javascript:void(0);");
								linkText = document.createTextNode("Eliminar");
								deleteLink.appendChild(linkText);
								columna8.appendChild(deleteLink);


								
								$(deleteLink).bind('click', function(){
									CambiarPartida(this);
								});

								

								if($("#tablaCategoriaPartidaDisminuiro > tr").length < 3){
								
									$('#tablaCategoriaPartidaDisminuir').show('fade',300);
									$("#cuerpoCategoriaPartidaDisminuir").show('fade',300);

								}


								fila.appendChild(columna1);				
								fila.appendChild(columna3);
								fila.appendChild(columna2);				
								fila.appendChild(columna4);
								fila.appendChild(columna5);				
								fila.appendChild(columna6);
								fila.appendChild(columna9);
								fila.appendChild(columna7);	
								fila.appendChild(columna8);
								
								tbody.appendChild(fila);

								$('#fieldset2').hide();
							 
							});
							
							
							
							
							
							
							 
			       
			        		 $Obj3 = $("#tablaCategoriaPartidaDisminuir").children("tr.trCaso").children('.tdDisponibilidad');	
			        		  
			        		  
			 				 montoTotal1 = 0;
			     	
			 				 $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {
			 					 

			 	      	            if(this.value != ''){
			 	      	            	
			 	      	         	   dato1  = parseFloat(this.value);
			 	      	         	   
			 	      	               montoTotal1 = (montoTotal1 + dato1);	
			 	      	                
			 	      	            }
			 	      	            
			 	      	 	 	 });
			 				 
			     	        	 
			     	        	 var dispon = $($Obj3).html();
			  
			     	        	 var num = parseInt(dispon.split(".").length -1);
			     	        	 var i=0;
			     	        	 
			     	        	 while(i<= num){
			     	        	 
			     	        	    dispon = dispon.replace('.','');
			     	        	 
			     	        	    i++;
			     	        	    
			     	              }
			         	        	 
			 	     	        	totaldisp =  parseFloat(dispon) - parseFloat(montoTotal1);

			     	                var  montoTotal2 = number_format(montoTotal1,2,',','.');
			     	                var  totaldisp2 = number_format(totaldisp,2,',','.');
     	                
			     	                
			     	                $('#totalDisp').html(totaldisp2);	   
			     	        		$('#montoTotal').html(montoTotal2);
							
							
						}
						
						
					
					}
					
					
					
					

					
				
					
					//	alert(JSON.stringify(pmodImputa));	
					
					
					//	alert(JSON.stringify(pmodRespaldo));	
						
						if(pmodRespaldo){  
							

							$.each(pmodRespaldo,function(id,val){
					           
						
							if(val.respTipo == 'Digital'){
								
								$('#trActualesDigital').show();
								
								
								
								var tbody = $('#tbodyRespDigitales')[0];	
								var fila = document.createElement("tr");
							    fila.setAttribute("class","trDigitalEliminar");
								var columna = document.createElement("td");
								    columna.setAttribute("style","border-bottom: 1px solid #D8D8D8");
								    columna.setAttribute("registrosEliminar","pcta[codProyAccEsp][]");
								    columna.setAttribute("style","border-bottom: 1px solid #D8D8D8");
								   
					         var alink = document.createElement("a");
					             alink.setAttribute("href","descargarImagen.php?file="+val.respNombre+""); 
					             alink.appendChild(document.createTextNode(val.respNombre));
					             columna.appendChild(alink);	
					             
					           //OPCION DE ELIMINAR
							 		var columna8 = document.createElement("td");
							 		columna8.setAttribute("valign","top");
							 		columna8.setAttribute("align","left");
							 		columna8.className = 'link';
							 		deleteLink = document.createElement("a");
							 		deleteLink.setAttribute("href","javascript:void(0);");
							 		linkText = document.createTextNode("Eliminar");
							 		deleteLink.appendChild(linkText);
							 		columna8.appendChild(deleteLink);


							 		$(deleteLink).bind('click', function(){
							 			
							 			EliminarRegisDigitFisicActual(this,0,val.id,val.respNombre);
							 		
							 			
							 			
							 		});
							 		
							 		
					              
								 fila.appendChild(columna);	
								 fila.appendChild(columna8); 
							     tbody.appendChild(fila);


							}else{

								
								   $('#trActualesFis').show();
								   
									var tbody = $('#tbodyRespFisico')[0];	
									var fila = document.createElement("tr");
								    fila.setAttribute("class","trFisicoEliminar");
									var columna = document.createElement("td");
									    columna.setAttribute("style","border-bottom: 1px solid #D8D8D8");
						            var alink = document.createElement("a");
						             alink.setAttribute("href","javascript:void(0);"); 
						             alink.appendChild(document.createTextNode(val.respNombre));
						             columna.appendChild(alink);	
						             
						           //OPCION DE ELIMINAR
								 		var columna8 = document.createElement("td");
								 		columna8.setAttribute("valign","top");
								 		columna8.setAttribute("align","left");
								 		columna8.className = 'link';
								 		deleteLink = document.createElement("a");
								 		deleteLink.setAttribute("href","javascript:void(0);");
								 		linkText = document.createTextNode("Eliminar");
								 		deleteLink.appendChild(linkText);
								 		columna8.appendChild(deleteLink);


								 		$(deleteLink).bind('click', function(){
								 		
								 			EliminarRegisDigitFisicActual(this,1,val.id,val.respNombre);

								 			
								 		});
								 		
								 		
						              
									 fila.appendChild(columna);	
									 fila.appendChild(columna8); 
								     tbody.appendChild(fila);

									


								}
							
							
							 });
							
							
					
							function EliminarRegisDigitFisicActual(obj,tipo,id,nombre) {
								
								
								
								if(tipo == 0 ){
									
									  
									regisFisDigiEli[regisFisDigiEli.length] = id; 
									regisNombreDigital[regisNombreDigital.length] = nombre;
										
										
							
								objTrs = $(obj).parents("tr.trDigitalEliminar");
					 			objTrs.hide(100).remove();
					 			
					 			
					 			
								if($("#tbodyRespDigitales > tr").length < 1){
									
									$('#trActualesDigital').hide(300);
									
								}
					 			
								}else{
								
									regisFisDigiEli[regisFisDigiEli.length] = id; 
								
									
									objTrs = $(obj).parents("tr.trFisicoEliminar");
						 			objTrs.hide(100).remove();
						 			
						 			
						 			
									if($("#tbodyRespFisico > tr").length < 1){
						
										$('#trActualesFis').hide(300);
								}
								
								}
							}
					

					     }
						
					
						
					 $('#file_upload').uploadify({
							'formData'     : {
								'accion' : 'GuardarImg',
								'PHPSESSID' : PHPSESSID
							},
						    'swf'      	: '../../js/lib/uploadify/uploadify/uploadify.swf',
							'uploader' 	: '../../acciones/modificacionespresupuestarias/mpresupuestarias.php',
							'buttonText' : '',
							   'fileTypeExts' : '*.gif; *.jpg; *.png; *.odt; *.pdf',
							   'uploadLimit' : 5,
							   'queueSizeLimit' : 5,
							   'UploadLimit'  :  5, 
							   'progressData' : 'speed',
							   'width'    : 104,	
							   'height'   : 32,			   
							'auto'		: false,
							'onSelect' : function(file) {
								tamanoFile++ ;	
					        },
					       'onCancel' : function(file) {
					        	tamanoFile-- ;
					        },
							'onQueueComplete' : function(queueData) {

								$('#formMP')[0].submit();
								
					        }
							

						});
			

			
// /////////////////////////////////////////////////////////////////////////////////////
// Funciones clik////////////////////////////////////////////////////////
					 
					  $("#nPmod").click(function() {

		   				  
						  if($("#nPmod").val() == ''){
							  
							 $("#nPmod").val('pmod-');
							 
						    
						  
						  
						  }
						  
					  });
					  
					  
			
            $("#RevisionesYMemosPmod").click(function() {
            	
                
            	if($("#RevisionesYMemosPmod").attr('opcion') == 0 ){
    		
            		$("#RevisionesYMemosPmod").html('.:Detalle pmod :.');
            		$("#RevisionesYMemosPmod").attr('opcion',1);
            		
            		$("#window4").hide();
                	$("#window3").show('blind',300);
                	
                	

            	}else{

            		
            		$("#RevisionesYMemosPmod").html('.:Revisiones y memos :.');
            		$("#RevisionesYMemosPmod").attr('opcion',0);
            		
            		$("#window3").hide();
                	$("#window4").show('blind',300);
                	
              
            	}
            	
		  });
			
			
			
			$('#OpcionesPdfPmod').click(function(event) {
				
				 url = "../../acciones/modificacionespresupuestarias/mpresupuestarias.php?accion=DetallePmodPdf&pmod="+$('span[detalle=\'pmod\']').html()+"";
	       	   // window.location = url;
	       	  window.open(url, '_blank'); 

				 });
			
	    $('#AgregarRespaldoFisico').click(function(){


				 AgregarRegistroFisico();   
				 

		 });
			 
		
		$("#accionMP").change(function(){
			
			if($("#accionMP").val() == 2){
				
				$("#nombreAccion").html('Monto a ceder:');
				
				   $("#accionArealizar").html("<b id='accionArealizar'>Seleccione la partida que ceder\u00e1 monto</b>");   
				 
				   $("#accionpartida").parent('div').show('blind',120);

					$('#fieldset2').show(200);
					  
				   
			}else if($("#accionMP").val() == 1){
				
				$("#accionpartida").parent('div').hide('blind',120);
				
			
				$("#nombreAccion").html('Monto disponible:');
				
				  $("#accionArealizar").html("<b id='accionArealizar'>Seleccione la partida a acreditarle monto presupuestario</b>");   
				  

					$('#fieldset2').hide(200);
			}else{
				
				$("#accionpartida").parent('div').hide('blind',120);
				
				$("#nombreAccion").html('Monto a disponible:');
				
				$("#accionArealizar").html("<b id='accionArealizar'>Seleccione la partida a disminuirle monto presupuestario</b>"); 
				$('#fieldset2').hide(200);
			}
			
			

			
			
		
			   $("#cuerpoCategoriaPartida").hide('fade',300);   
			   $('#tablaCategoriaPartida').children('tr.trCaso').remove();
			   
			   $("#cuerpoCategoriaPartidaReceptora").hide('fade',300);   
			   $('#tablaCategoriaPartidaReceptora').children('tr.trCaso2').remove();
			  
			   
			   $("#cuerpoCategoriaPartidaReceptora").hide('fade',300);   
			   $('#tablaCategoriaPartidaReceptora').children('tr.trCaso2').remove();
			   
			   $("#cuerpoCategoriaPartidaCredito").hide('fade',300);   
			   $('#tablaCategoriaPartidaCredito').children('tr.trCaso').remove();
			   
			   $("#cuerpoCategoriaPartidaDisminuir").hide('fade',300);   
			   $('#tablaCategoriaPartidaDisminuir').children('tr.trCaso').remove();
			   
			    $('#totalDisp').html('0,00');	   
	      		$('#montoTotal').html('0,00');

	      		 $('#Partida').val('');
	      		$('#filsed1').show(200);
				$('#divPartida').hide();
				$('#tablacategoria').hide();
				$('#trcategoriaesp').hide();
				$('.patidaseleccionada').hide();
			    $('#selectProyAcc').val('');
			    $('#selectProyAcc').show();
			    $('#selectProyAcc').focus();
			   
			

				
		});
			

		 $('#confirmarPartida').click(function(){
			 
       	  AgregarCategoriaPartida();
	          		
		});
			
		 
		 
         $("#BuscarPmod").click(function() {


	  if(
	        		  
	        	$("#txt_inicio").val().length < 1  && 
			    $("#hid_hasta_itin").val().length < 1   &&
			    $("#tipo").val().length < 1   && 
			    $("#PartidaBusqueda").val().length < 1  && 
			    $("#compProyAccVal").val().length < 1      && 
			    $("#palabraClave").val().length < 1     && 
			    $("#nPmod").val().length < 1         

	 ){

		 alert('Debe seleccionar un campo'); 
		 
	   }else{
		   
		   if(($("#txt_inicio").val().length > 1 && $("#hid_hasta_itin").val().length < 1) ||
				($("#hid_hasta_itin").val().length > 1 && $("#txt_inicio").val().length < 1)){
			   
			   
			   alert('Debe seleccionar un rango de fecha');
			   
		   }else{

			   $('#formCompFiltro').attr('action','../../acciones/modificacionespresupuestarias/mpresupuestarias.php?accion=BuscarPmodAccion');
			   $('#formCompFiltro')[0].submit();				
	   }
	   }


 
});


			
// /////////////////////////////////////////////////////////////////////////////////
// Funciones key////////////////////////////////////////////////////////
		 
		 $("#pctaRespaldoFisico").keyup(function(event) {

             val = trim($("#pctaRespaldoFisico").val());
			 
            if( val != '' &&  $("#pctaRespaldoFisico").val().length > 4){
			 
            $("#AgregarRespaldoFisico").show('fade',300);

            }else{
                
          	  $("#AgregarRespaldoFisico").hide(200);
          	  
                }

		 });
		 
		 
		 $('#Categoria').keypress(function(event){
			 
			if(dependencia == false){
		    	 if (jQuery.browser.msie) {    
		    	      event.cancelBubble = true;    
		    	        } else {  
                         event.stopPropagation();        
				}
		    	 
				alert("Debe seleccionar la unidad/dependencia");
				$('#unidadDependencia').focus();
				
			}
			 
		 });
		 
// /////////////////////////////////////////////////////////////////////////////////
// Funciones change////////////////////////////////////////////////////////
		 
		 
		 
		 
		 $('#Categoria').change(function(){
			 
				if ($('#Categoria').val() == '') {
				 $('#tablacategoria').hide();
				 $('#c1').html('');
			     $('#c2').html('');
				 $('#c3').html('');
				 $('#c4').html('');
				 $('#Partidadenominacion').html('');
				 $('#Partidamonto').val('');
				 $('#Partida').val('');
			     $('.patidaseleccionada').hide();
				 $('#divPartida').hide(200);

	          		
			}

		 });
		 

				$('#selectProyAcc').change(function(){
					
					 var entro = false;
					 var proyacc = false;

				 if($('#selectProyAcc').val() != ''){
					 
				     $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {

				    	 obj = $(this).parent("td").parent("tr.trCaso");
				    	 
				    	 proyacc =   obj.find('input[name=\'mpresupuestaria[codProyAcc][]\']');
				    	 
				    	
				    	 if($('#selectProyAcc').val() != proyacc.val() && entro == false){
				    	
				    		 entro = true; 
				    		 
				    	 }

			 	 	   });	
				     
					 }
				     
				    if(entro){
				    	
							if ($('#selectProyAcc').val() == '') {
							 $('#selectProyAcc').val('');
							 $('#trcategoriaesp').hide();
							 $('#tablacategoria').hide();
							 $('#c1').html('');
						     $('#c2').html('');
							 $('#c3').html('');
							 $('#c4').html('');

							 $('#Partidadenominacion').html('');
							 $('#Partidamonto').val('');
						     $('.patidaseleccionada').hide();
						     $('#Partida').val('');
							 $('#divPartida').hide(200);
							 
					 
						}else{
						
							 $('#trcategoriaesp').show('fade',300);

							 tipoproacc =  $('#selectProyAcc option[value="'+$('#selectProyAcc').val()+'"]').parent('optgroup').attr('label');
								
							 if(tipoproacc != 'Proyectos'){

								 tipoproacc = 0; 

								 }else{

							     tipoproacc = 1;

								 }

							 $('#Categoria').val('');
							
							 
							}
				     
				     
					 }else{
					 
					if ($('#selectProyAcc').val() == '') {
						
	

					 $('#trcategoriaesp').hide();
					 $('#tablacategoria').hide();
					 $('#c1').html('');
				     $('#c2').html('');
					 $('#c3').html('');
					 $('#c4').html('');

					 $('#Partidadenominacion').html('');
					 $('#Partidamonto').val('');
				     $('.patidaseleccionada').hide();
				     $('#Partida').val('');
					 $('#divPartida').hide(200);
					 
			 
				}else{
					
					 $('#trcategoriaesp').show('fade',300);

					 tipoproacc =  $('#selectProyAcc option[value="'+$('#selectProyAcc').val()+'"]').parent('optgroup').attr('label');
						
					 if(tipoproacc != 'Proyectos'){

						 tipoproacc = 0; 

						 }else{

					     tipoproacc = 1;

						 }

					 $('#Categoria').val('');
					
					}
					
				
			 }

		});	
				
			
// ///////////////////////////////////////////////////////////////////////////////////
// Funciones focus ////////////////////////////////////////////////////////
				
				 $("#nPmod").focusout(function() {
					  
					  if( ($("#nPmod").val() == 'pmod-')  ||
						  ($("#nPmod").val().length < 5) || 
						  ($("#nPmod").val().substring(0,5) != 'pmod-')){
						  
						  $("#nPmod").val(''); 

					  }
					  
				  });
				
			
				$('#Partida').focusout(function() {

					if($('#Partida').val().length < 13){
						
						$('#Partida').val('');
						  
	             	if($('#Partidadenominacion').html() != ''){

								 $('#Partidadenominacion').html('');
								 $('#Partidamonto').val('');
							     $('.patidaseleccionada').hide();
     

	                 }
					
				}
			 
			 });	
				

				 $('#PartidaBusqueda').focusout(function() {

						if($('#PartidaBusqueda').val().length < 13){
							
							$('#PartidaBusqueda').val('');
							
						
						}
						});
// ///////////////////////////////////////////////////////////////////////////////////
// Funciones ajax////////////////////////////////////////////////////////
			
				
				 
			  		
			
				 
		$('#Categoria').autocomplete({
			autoFocus: false,
			delay: 100,
			 source: function(request, response){
					$.ajax({
						url: "../../acciones/pcta/pcta.php",
						dataType: "json",
						data: {
							accion: "Searchcategoria",
							tipoRespuesta: 'json',
							key: request.term,
							dependencia: dependencia,
							restrictivo: true,
							idproyAcc: $('#selectProyAcc').val(),
							 tipoproacc :  tipoproacc
						},
						success: function(json){

		
							var index = 0;
							var items = new Array();

							if(json == ""){
								
                            	if($('#c1').html() != ''){

                            		$('#c1').html('');
        							$('#c2').html('');
        							$('#c3').html('');
        							$('#c4').html('');
        							$('#tablacategoria').hide(200);
        							$('#divPartida').hide(200);
                                }
                            	
                             }

							$.each(json,function(id,params){

								
								
                                if(params.tipo == 1){
                                      
                                     var tipo = "Proyecto";
                                      }else{

                                    	  var tipo = "Ac. Centralizada";
                                          }
								
								var value = params.centro+ ' '+params.nombre;

                                categoriaTipo = params.tipo;
                                id_especifica = params.id_especifica;
								id_proy_accion = params.id_proy_accion;
								
								 items[index++] = {
										 
										value: value,
										tipo: tipo,
										tipoVal :params.tipo,
										id_especifica: params.id_especifica,
										id_proy_accion: params.id_proy_accion,
										centro: params.centro,
										nombre: params.nombre,
										proy_titulo: params.proy_titulo
										

								}; 

							});

							
							response(items);
							
						}
					});
				},
				minLength: 1,
				select: function(event, ui)
				{ 
					
					$('#tablacategoria').css('margin-top','10px');
					$('#c1').html(ui.item.tipo);
					$('#c3').html(ui.item.centro);
					$('#c2').html(ui.item.proy_titulo);
					$('#c4').html(ui.item.nombre);
					$('#tablacategoria').show('fade',300);
					$('#divPartida').show('fade',300);

					 categoriaTipo = ui.item.tipoVal;
                     id_especifica= ui.item.id_especifica;
					 id_proy_accion = ui.item.id_proy_accion;

					 
					 var entro = false;
					 var proyacc = false;

					return true;
				}

				
			}); 
		
		
		
		
		$('#Partida').autocomplete({
			source: function(request, response){
				$.ajax({
					url: "../../acciones/partida.php",
					dataType: "json",
					data: {
						accion: "Search",
						tipoDocumento: "pcta",
						key: request.term,
						tipoRespuesta: 'json'

					},
					success: function(json){

						
						var index = 0;
						var items = new Array();

                        var val = $(json['listaPartida']).length ;


						if(val < 1){

							  $('#Partida').val('');
							  
                        	if($('#Partidadenominacion').html() != ''){

									 $('#Partidadenominacion').html('');
									 $('#Partidamonto').val('');
								     $('.patidaseleccionada').hide();
								   
								     

                            }
                        	
                         }

						



						

						$.each(json.listaPartida, function(idPartida, objPartida){

							var label = idPartida + " : " + objPartida.nombre;
							items[index++] = {
									id: idPartida,
									label: label,
									value: idPartida,
									nombre: objPartida.nombre
							};
						});
						response(items);
					}
				});
			},
			minLength: 1,
			select: function(event, ui)
			{

				partida_denom = ui.item.nombre;
				partida = ui.item.id;
				$('#Partida').val(ui.item.id);
				$('#Partidadenominacion').html(ui.item.nombre);
				$('.patidaseleccionada').show('fade',300);
				$('#Partidamonto').focus();

				
				return true;
			}
	    });
		
		
		
		
		$('#PartidaBusqueda').autocomplete({
			
			source: function(request, response){
				$.ajax({
					url: "../../acciones/partida.php",
					dataType: "json",
					data: {
						accion: "Search",
						key: request.term,
						anno: $('#agnoComp').val(),
						tipoRespuesta: 'json'

					},
					success: function(json){
						
						var index = 0;
						var items = new Array();
						

                        var val = $(json['listaPartida']).length ;

                       
						if(val < 1){
							$('#PartidaBusqueda').val('');
                         }

						

						$.each(json.listaPartida, function(idPartida, objPartida){

							var label = idPartida + " : " + objPartida.nombre;
							items[index++] = {
									id: idPartida,
									label: label,
									value: idPartida,
									nombre: objPartida.nombre
							};
						});
						response(items);
					}
				});
			},
			minLength: 1,
			select: function(event, ui)
			{

				partida_denom = ui.item.nombre;
				partida = ui.item.id;
				$('#PartidaBusqueda').val(ui.item.id);
				return true;
			}
	 });
		
		
		$('#compProyAcc').autocomplete({
			autoFocus: false,
			delay: 100,
			 source: function(request, response){
					$.ajax({
						url: "../../acciones/pcta/pcta.php",
						dataType: "json",
						data: {
							accion: "SearchPctaProyAcc",
							tipoRespuesta: 'json',
							key: request.term,
							anno: $('#agnoComp').val()!=null?$('#agnoComp').val():''
						},
						success: function(json){
					
							
							var index = 0;
							var items = new Array();

							if(json == ""){
								
								 $('#compProyAccVal').val('');
								 $('#compProyAccVal2').val('');
                                }
                            	
                          
							$.each(json,function(id,params){

           
							    var value = params.centro;

                                categoriaTipo = params.tipo;
                                
                               
                                
                              id_especifica= params.id_especifica;
                                
                              
                              
								id_proy_accion = params.id_proy_accion;
								
								 items[index++] = {
										 
										value: value,

										id_especifica: params.id_especifica,
										id_proy_accion: params.id_proy_accion,
										centro: params.centro,
										nombre: params.nombre,
										proy_titulo: params.proy_titulo
										

								}; 

							});

							
							response(items);
						}
					});
				},
				minLength: 1,
				select: function(event, ui)
				{ 
					
					
		// alert(ui.item.id_proy_accion+'/'+ui.item.id_especifica);
			 $('#compProyAccVal').val(ui.item.id_especifica);
			 $('#compProyAccVal2').val(ui.item.id_proy_accion+'/'+ui.item.id_especifica);

					return true;
					
				}

				
			});
		
		
		
		});

// /////////////////////////////////////////////////////////////////////////////////
// Funciones////////////////////////////////////////////////////////
		 
		 
		 
			
		   function AgregarCategoriaPartida(){

			   valido = true;
			   
			   
			   if($('input[name=\'mpresupuestariadisp[codPartida][]\']').length > 0){
				   
				  
				   
				  if($('input[name=\'mpresupuestariadisp[codPartida][]\']').val().slice(0,4) !=  partida.slice(0,4)){
					  
					  valido = false;

					  alert("No puedes combinar distintos tipos de partidas");
					  
				  }
				   
				   
				   
				   if (valido){
					   
					   $('input[name=\'mpresupuestariadisp[codPartida][]\']').each(function (index) {
					    	  
					    	  
				            if(this.value == partida){

				          valido = false;
				          
				            alert("Esta partida ya fue seleccionada");
				            
				            }
						    
					 		 
					 	 });
					   
				   }
				  
				      
				   
			   }
			   
			   
			   if (valido){
			 
			   if($('input[name=\'mpresupuestaria[codPartida][]\']').length > 0){
				   
					  
				   
					  if($('input[name=\'mpresupuestaria[codPartida][]\']').val().slice(0,4) !=  partida.slice(0,4)){
						  
						  valido = false;

						  alert("No puedes combinar distintos tipos de partidas");
						  
					  }
					   
					   
					   
					   if (valido){
						   
						   $('input[name=\'mpresupuestaria[codPartida][]\']').each(function (index) {
						    	  
						    	  
					            if(this.value == partida){

					          valido = false;
					          
					            alert("Esta partida ya fue seleccionada");
					            
					            }
							    
						 		 
						 	 });
						   
					   }
					  
					      
					   
				   }
				   
			   }
			   
			 if(!valido === false ){     
			      
			      
			if($("#accionMP").val() == 2){
				
		    if($("#accionpartida").val() ==  '1'){

			   var tbody = $('#tablaCategoriaPartida')[0];

			 
				var dato1= $('#c1').html();
				var dato2= $('#c3').html();
				var dato3= $('#c2').html();
				var dato4= $('#c4').html();
				var dato5= partida ;
				var dato6= partida_denom;
				
		
				
			    var dato9 = GetDisponibilidad(categoriaTipo,id_proy_accion,id_especifica,partida);

			    dato11 =  number_format(dato9,2,',','.');
			    if(parseInt(dato9) <= 0){

			          alert("Esta partida no tiene disponibilidad");
			          $('#Partida').val('');
			          if($('#Partidadenominacion').html() != ''){

							 $('#Partidadenominacion').html('');
							 $('#Partidamonto').val('');
						     $('.patidaseleccionada').hide();

		         }
			          $('#Partida').focus();
			          
			          return;
			          
			            }
			    
				var fila = document.createElement("tr");
				fila.className='normalNegro trCaso';
			         
				var columna1 = document.createElement("td");
				columna1.setAttribute("valign","top");
				columna1.appendChild(document.createTextNode(dato1));
				var input1 = document.createElement("input");
				input1.setAttribute("type","hidden");
				input1.setAttribute("name","mpresupuestariadisp[tipo][]");
				input1.value=categoriaTipo;
				columna1.appendChild(input1);
				
				var columna2 = document.createElement("td");
				columna2.setAttribute("valign","top");
				columna2.appendChild(document.createTextNode(dato2));
				var input2 = document.createElement("input");
				input2.setAttribute("type","hidden");
				input2.setAttribute("name","mpresupuestariadisp[codProyAcc][]");
				input2.value= id_proy_accion;
				columna2.appendChild(input2);



				var columna3 = document.createElement("td");
				columna3.setAttribute("valign","top");
				columna3.appendChild(document.createTextNode(dato3));
				

				var columna4 = document.createElement("td");
				columna4.setAttribute("valign","top");
				columna4.appendChild(document.createTextNode(dato4));
				var input4 = document.createElement("input");
				input4.setAttribute("type","hidden");
				input4.setAttribute("name","mpresupuestariadisp[codProyAccEsp][]");
				input4.value=id_especifica;
				columna4.appendChild(input4);

				var columna5 = document.createElement("td");
				columna5.setAttribute("valign","top");
				columna5.setAttribute("class","tdPartida");
				columna5.appendChild(document.createTextNode(dato5));
				var input5 = document.createElement("input");
				input5.setAttribute("type","hidden");
				input5.setAttribute("name","mpresupuestariadisp[codPartida][]");
				input5.value= dato5;
				columna5.appendChild(input5);

				var columna6 = document.createElement("td");
				columna6.setAttribute("valign","top");
				columna6.appendChild(document.createTextNode(dato6));

		 		
				var columna9 = document.createElement("td");
				columna9.setAttribute("class","tdDisponibilidad");
				columna9.setAttribute("align","right");
				columna9.setAttribute("style","color:red; font-weight:bold;");
				columna9.appendChild(document.createTextNode(dato11));
				

				var columna7 = document.createElement("td");
		 		columna7.setAttribute("valign","baseline");
		 		columna7.setAttribute("class","tdMonto");
		 		columna7.setAttribute("style","padding:0;");
		 		var input7 = document.createElement("input");
		 		input7.setAttribute("type","text");
		 		input7.setAttribute("autocomplete","off");
		 		input7.setAttribute("name","mpresupuestariadisp[monto][]");
		 		input7.setAttribute("style","height:22px;width:70;margin:0;border:2px #D8D8D8 dotted solid; font-size:10px" );
		 		input7.value= '';
		 		columna7.appendChild(input7);
		 		
                $(input7).keyup(function(){
		 			
		 			formato_num($(this));

		 		});
				
				
				
				// OPCION DE CAMBIAR
				var columna8 = document.createElement("td");
				columna8.setAttribute("valign","top");
				columna8.setAttribute("align","center");
				columna8.className = 'link';
				deleteLink = document.createElement("a");
				deleteLink.setAttribute("href","javascript:void(0);");
				linkText = document.createTextNode("Eliminar");
				deleteLink.appendChild(linkText);
				columna8.appendChild(deleteLink);


				
				$(deleteLink).bind('click', function(){
					
					eliminarPartidaCede(this);
					
				});


				fila.appendChild(columna1);				
				fila.appendChild(columna3);
				fila.appendChild(columna2);				
				fila.appendChild(columna4);
				fila.appendChild(columna5);				
				fila.appendChild(columna6);
				fila.appendChild(columna9);
				fila.appendChild(columna7);
				fila.appendChild(columna8);
				
				tbody.appendChild(fila);


				$('#cedeMonto').html(dato11);	

				
					$('#tablaCategoriaPartida').show('fade',300);
					$("#cuerpoCategoriaPartida").show('fade',300);
					$("#accionArealizar").effect('highlight',1000 ,function(){
				//	$("#accionArealizar").html("<b id='accionArealizar'>Seleccione las partidas que recibir\u00e1n monto</b>");	
						
						
					});
					
					

				/*	$("#accionArealizar").effect('pulsate', 100);	
					$('#divPartida').hide();
					$('#tablacategoria').hide();
					$('#trcategoriaesp').hide();
					$('.patidaseleccionada').hide();
				    $('#selectProyAcc').val('');
				*/	$('#Partida').val('');
				
				

		     }else{
		    	
                    var tbody = $('#tablaCategoriaPartidaReceptora')[0];
					var dato1= $('#c1').html();
					var dato2= $('#c3').html();
					var dato3= $('#c2').html();
					var dato4= $('#c4').html();
					var dato5= partida ;
					var dato6= partida_denom;
					
					obj = $("#tablaCategoriaPartida").children("tr.trCaso");
					
				    var dato9 = GetDisponibilidad(categoriaTipo,id_proy_accion,id_especifica,partida);

				    dato11 =  number_format(dato9,2,',','.');
 
					var fila = document.createElement("tr");
					fila.className='normalNegro trCaso2';
				         
					var columna1 = document.createElement("td");
					columna1.setAttribute("valign","top");
					columna1.appendChild(document.createTextNode(dato1));
					var input1 = document.createElement("input");
					input1.setAttribute("type","hidden");
					input1.setAttribute("name","mpresupuestaria[tipo][]");
					input1.value=categoriaTipo;
					columna1.appendChild(input1);
					
					var columna2 = document.createElement("td");
					columna2.setAttribute("valign","top");
					columna2.appendChild(document.createTextNode(dato2));
					var input2 = document.createElement("input");
					input2.setAttribute("type","hidden");
					input2.setAttribute("name","mpresupuestaria[codProyAcc][]");
					input2.value= id_proy_accion;
					columna2.appendChild(input2);



					var columna3 = document.createElement("td");
					columna3.setAttribute("valign","top");
					columna3.appendChild(document.createTextNode(dato3));
					

					var columna4 = document.createElement("td");
					columna4.setAttribute("valign","top");
					columna4.appendChild(document.createTextNode(dato4));
					var input4 = document.createElement("input");
					input4.setAttribute("type","hidden");
					input4.setAttribute("name","mpresupuestaria[codProyAccEsp][]");
					input4.value=id_especifica;
					columna4.appendChild(input4);

					var columna5 = document.createElement("td");
					columna5.setAttribute("valign","top");
					columna5.setAttribute("class","tdPartida");
					columna5.appendChild(document.createTextNode(dato5));
					var input5 = document.createElement("input");
					input5.setAttribute("type","hidden");
					input5.setAttribute("name","mpresupuestaria[codPartida][]");
					input5.value= dato5;
					columna5.appendChild(input5);

					var columna6 = document.createElement("td");
					columna6.setAttribute("valign","top");
					columna6.appendChild(document.createTextNode(dato6));
					
			
					var columna7 = document.createElement("td");
			 		columna7.setAttribute("valign","baseline");
			 		columna7.setAttribute("class","tdMonto");
			 		columna7.setAttribute("style","padding:0;");
			 		var input7 = document.createElement("input");
			 		input7.setAttribute("type","text");
			 		input7.setAttribute("autocomplete","off");
			 		input7.setAttribute("name","mpresupuestaria[monto][]");
			 		input7.setAttribute("style","height:22px;width:70;margin:0;border:2px #D8D8D8 dotted solid; font-size:10px" );
			 		input7.value= '';
			 		columna7.appendChild(input7);
			 		
			 	

                    $(input7).keyup(function(){
			 			
			 			formato_num($(this));

			 		});

			 		
					var columna9 = document.createElement("td");
					columna9.setAttribute("class","tdDisponibilidad");
					columna9.setAttribute("align","right");
					columna9.appendChild(document.createTextNode(dato11));
					

					// OPCION DE CAMBIAR
					var columna8 = document.createElement("td");
					columna8.setAttribute("valign","top");
					columna8.setAttribute("align","center");
					columna8.className = 'link';
					deleteLink = document.createElement("a");
					deleteLink.setAttribute("href","javascript:void(0);");
					linkText = document.createTextNode("Eliminar");
					deleteLink.appendChild(linkText);
					columna8.appendChild(deleteLink);


					
					$(deleteLink).bind('click', function(){
						EliminarPartidaReceptora(this);
					});


					fila.appendChild(columna1);				
					fila.appendChild(columna3);
					fila.appendChild(columna2);				
					fila.appendChild(columna4);
					fila.appendChild(columna5);				
					fila.appendChild(columna6);
					fila.appendChild(columna9);
					fila.appendChild(columna7);	
					fila.appendChild(columna8);
					
					tbody.appendChild(fila);

					
			          

					if($("#tablaCategoriaPartidaReceptora > tr").length < 3){
					
						$('#tablaCategoriaPartidaReceptora').show('fade',300);
						$("#cuerpoCategoriaPartidaReceptora").show('fade',300);

					}
		    	 
 	 
		    	 
		     }
		     
		    
		     

               }else if($("#accionMP").val() == 1){

            
    			   var tbody = $('#tablaCategoriaPartidaCredito')[0];

					var dato1= $('#c1').html();
					var dato2= $('#c3').html();
					var dato3= $('#c2').html();
					var dato4= $('#c4').html();
					var dato5= partida ;
					var dato6= partida_denom;
					
			
					
					
				    var dato9 = GetDisponibilidad(categoriaTipo,id_proy_accion,id_especifica,partida);
				    dato11 =  number_format(dato9,2,',','.');
			
					var fila = document.createElement("tr");
					fila.className='normalNegro trCaso';
				         
					var columna1 = document.createElement("td");
					columna1.setAttribute("valign","top");
					columna1.appendChild(document.createTextNode(dato1));
					var input1 = document.createElement("input");
					input1.setAttribute("type","hidden");
					input1.setAttribute("name","mpresupuestaria[tipo][]");
					input1.value=categoriaTipo;
					columna1.appendChild(input1);
					
					var columna2 = document.createElement("td");
					columna2.setAttribute("valign","top");
					columna2.appendChild(document.createTextNode(dato2));
					var input2 = document.createElement("input");
					input2.setAttribute("type","hidden");
					input2.setAttribute("name","mpresupuestaria[codProyAcc][]");
					input2.value= id_proy_accion;
					columna2.appendChild(input2);



					var columna3 = document.createElement("td");
					columna3.setAttribute("valign","top");
					columna3.appendChild(document.createTextNode(dato3));
					

					var columna4 = document.createElement("td");
					columna4.setAttribute("valign","top");
					columna4.appendChild(document.createTextNode(dato4));
					var input4 = document.createElement("input");
					input4.setAttribute("type","hidden");
					input4.setAttribute("name","mpresupuestaria[codProyAccEsp][]");
					input4.value=id_especifica;
					columna4.appendChild(input4);

					var columna5 = document.createElement("td");
					columna5.setAttribute("valign","top");
					columna5.setAttribute("class","tdPartida");
					columna5.appendChild(document.createTextNode(dato5));
					var input5 = document.createElement("input");
					input5.setAttribute("type","hidden");
					input5.setAttribute("name","mpresupuestaria[codPartida][]");
					input5.value= dato5;
					columna5.appendChild(input5);

					var columna6 = document.createElement("td");
					columna6.setAttribute("valign","top");
					columna6.appendChild(document.createTextNode(dato6));
					
			
					var columna7 = document.createElement("td");
			 		columna7.setAttribute("valign","baseline");
			 		columna7.setAttribute("class","tdMonto");
			 		columna7.setAttribute("style","padding:0;");
			 		var input7 = document.createElement("input");
			 		input7.setAttribute("type","text");
			 		input7.setAttribute("autocomplete","off");
			 		input7.setAttribute("name","mpresupuestaria[monto][]");
			 		input7.setAttribute("style","height:22px;width:70;margin:0;border:2px #D8D8D8 dotted solid; font-size:10px" );
			 		input7.value= '';
			 		columna7.appendChild(input7);
			 		
                    $(input7).keyup(function(){
			 			
			 			formato_num($(this));

			 		});
			 		
					var columna9 = document.createElement("td");
					columna9.setAttribute("class","tdDisponibilidad");
					columna9.setAttribute("align","right");
					columna9.appendChild(document.createTextNode(dato11));
					

					// OPCION DE CAMBIAR
					var columna8 = document.createElement("td");
					columna8.setAttribute("valign","top");
					columna8.setAttribute("align","center");
					columna8.className = 'link';
					deleteLink = document.createElement("a");
					deleteLink.setAttribute("href","javascript:void(0);");
					linkText = document.createTextNode("Eliminar");
					deleteLink.appendChild(linkText);
					columna8.appendChild(deleteLink);


					
					$(deleteLink).bind('click', function(){
						eliminarPartidaRecibir(this);
					});

					

					if($("#tablaCategoriaPartidaCredito > tr").length < 3){
					
						$('#tablaCategoriaPartidaCredito').show('fade',300);
						$("#cuerpoCategoriaPartidaCredito").show('fade',300);

					}


					fila.appendChild(columna1);				
					fila.appendChild(columna3);
					fila.appendChild(columna2);				
					fila.appendChild(columna4);
					fila.appendChild(columna5);				
					fila.appendChild(columna6);
					fila.appendChild(columna9);
					fila.appendChild(columna7);	
					fila.appendChild(columna8);
					
					tbody.appendChild(fila);


					
					$('#fieldset2').hide(200);
				}else{
	
	    			   var tbody = $('#tablaCategoriaPartidaDisminuir')[0];

						var dato1= $('#c1').html();
						var dato2= $('#c3').html();
						var dato3= $('#c2').html();
						var dato4= $('#c4').html();
						var dato5= partida ;
						var dato6= partida_denom;
						
				
						
						
					    var dato9 = GetDisponibilidad(categoriaTipo,id_proy_accion,id_especifica,partida);
					    dato11 =  number_format(dato9,2,',','.');
					    if(parseInt(dato9) <= 0){

					          alert("Esta partida no tiene disponibilidad");
					          $('#Partida').val('');
					          if($('#Partidadenominacion').html() != ''){

									 $('#Partidadenominacion').html('');
									 $('#Partidamonto').val('');
								     $('.patidaseleccionada').hide();

				         }
					          $('#Partida').focus();
					          
					          return;
					          
					            }

						var fila = document.createElement("tr");
						fila.className='normalNegro trCaso';
					         
						var columna1 = document.createElement("td");
						columna1.setAttribute("valign","top");
						columna1.appendChild(document.createTextNode(dato1));
						var input1 = document.createElement("input");
						input1.setAttribute("type","hidden");
						input1.setAttribute("name","mpresupuestaria[tipo][]");
						input1.value=categoriaTipo;
						columna1.appendChild(input1);
						
						var columna2 = document.createElement("td");
						columna2.setAttribute("valign","top");
						columna2.appendChild(document.createTextNode(dato2));
						var input2 = document.createElement("input");
						input2.setAttribute("type","hidden");
						input2.setAttribute("name","mpresupuestaria[codProyAcc][]");
						input2.value= id_proy_accion;
						columna2.appendChild(input2);



						var columna3 = document.createElement("td");
						columna3.setAttribute("valign","top");
						columna3.appendChild(document.createTextNode(dato3));
						

						var columna4 = document.createElement("td");
						columna4.setAttribute("valign","top");
						columna4.appendChild(document.createTextNode(dato4));
						var input4 = document.createElement("input");
						input4.setAttribute("type","hidden");
						input4.setAttribute("name","mpresupuestaria[codProyAccEsp][]");
						input4.value=id_especifica;
						columna4.appendChild(input4);

						var columna5 = document.createElement("td");
						columna5.setAttribute("valign","top");
						columna5.setAttribute("class","tdPartida");
						columna5.appendChild(document.createTextNode(dato5));
						var input5 = document.createElement("input");
						input5.setAttribute("type","hidden");
						input5.setAttribute("name","mpresupuestaria[codPartida][]");
						input5.value= dato5;
						columna5.appendChild(input5);

						var columna6 = document.createElement("td");
						columna6.setAttribute("valign","top");
						columna6.appendChild(document.createTextNode(dato6));
						
				
						var columna7 = document.createElement("td");
				 		columna7.setAttribute("valign","baseline");
				 		columna7.setAttribute("class","tdMonto");
				 		columna7.setAttribute("style","padding:0;");
				 		var input7 = document.createElement("input");
				 		input7.setAttribute("type","text");
				 		input7.setAttribute("name","mpresupuestaria[monto][]");
				 		input7.setAttribute("autocomplete","off");
				 		input7.setAttribute("style","height:22px;width:70;margin:0;border:2px #D8D8D8 dotted solid; font-size:10px" );
				 		input7.value= '';
				 		columna7.appendChild(input7);
				 		
                        $(input7).keyup(function(){
				 			
				 			formato_num($(this));

				 		});

				 		
						var columna9 = document.createElement("td");
						columna9.setAttribute("class","tdDisponibilidad");
						columna9.setAttribute("align","right");
						columna9.appendChild(document.createTextNode(dato11));
						

						// OPCION DE CAMBIAR
						var columna8 = document.createElement("td");
						columna8.setAttribute("valign","top");
						columna8.setAttribute("align","center");
						columna8.className = 'link';
						deleteLink = document.createElement("a");
						deleteLink.setAttribute("href","javascript:void(0);");
						linkText = document.createTextNode("Eliminar");
						deleteLink.appendChild(linkText);
						columna8.appendChild(deleteLink);


						
						$(deleteLink).bind('click', function(){
							eliminarPartidaDisminuir(this);
						});

						

						if($("#tablaCategoriaPartidaDisminuiro > tr").length < 3){
						
							$('#tablaCategoriaPartidaDisminuir').show('fade',300);
							$("#cuerpoCategoriaPartidaDisminuir").show('fade',300);

						}


						fila.appendChild(columna1);				
						fila.appendChild(columna3);
						fila.appendChild(columna2);				
						fila.appendChild(columna4);
						fila.appendChild(columna5);				
						fila.appendChild(columna6);
						fila.appendChild(columna9);
						fila.appendChild(columna7);	
						fila.appendChild(columna8);
						
						tbody.appendChild(fila);
						
						
					
						$('#fieldset2').hide(200);
					 

				}
			  	
			      
		     
		     
				     }

				   
		   }
		   
		   

		   function CambiarPartida(obj) {
			   
				if($("#tablaCategoriaPartidaCredito > tr").length < 2){
					
					  $("#cuerpoCategoriaPartidaCredito").hide('fade',300);   
					  
					   $('#tablaCategoriaPartidaCredito').children('tr.trCaso').remove();

					  $("#cuerpoCategoriaPartidaDisminuir").hide('fade',300);   
					 // $('#tablaCategoriaPartidaDisminuir').children('tr.trCaso').remove();
					   
					   
					    $('#totalDisp').html('0,00');	   
			      		$('#montoTotal').html('0,00');
	
			      		
			      		    $('#Partida').val('');
				      		$('#filsed1').show(200);
							$('#divPartida').hide();
						    $('#tablacategoria').hide();
							$('#trcategoriaesp').hide();
							$('.patidaseleccionada').hide();
						    $('#selectProyAcc').val('');
						    $('#selectProyAcc').show();
						    $('#selectProyAcc').focus();

				}
			}
			 
		   
		   
		   
		   function CambiarPartidaReceptora(obj) {

		   objTrs = $(obj).parents("tr.trCaso").html();
			   
		   $("#accionArealizar").html("<b id='accionArealizar'>Seleccione la partidas que ceder\u00e1n monto</b>");   
		   $('#selectProyAcc').focus();
		   $("#cuerpoCategoriaPartida").hide('fade',300);   
		   $('#tablaCategoriaPartida').children('tr.trCaso').remove();
		   
		   $("#cuerpoCategoriaPartidaReceptora").hide('fade',300);   
		   $('#tablaCategoriaPartidaReceptora').children('tr.trCaso2').remove();
		   
		   
		    $('#totalDisp').html('0,00');	   
      		$('#montoTotal').html('0,00');

      		
			$('#divPartida').hide();
			$('#tablacategoria').hide();
			$('#trcategoriaesp').hide();
			$('.patidaseleccionada').hide();
		    $('#selectProyAcc').val('');
			$('#Partida').val('');

		}
		   
		   

		   function eliminarPartidaCede(obj) {

			   objTrs = $(obj).parents("tr.trCaso");
			   objTrs.hide(100).remove(); 
			   
 			  
	        if($("#accionMP").val() == 2){   
	        
			   
				if($("#tablaCategoriaPartida > tr").length < 2){
					
					  
					  
					   $("#cuerpoCategoriaPartida").hide('fade',300); 
		        	   $('#montoTotal').html('0,00');	
		   
	
				}else{
					
					
					 var montoTotal1 = 0;
	                 var dato1 = 0 ;
	                 $('input[name=\'mpresupuestariadisp[monto][]\']').each(function (index) {
	         	         

	                    if(this.value != ''){

	                    dato1  = parseFloat(this.value);
	                    	
	                    montoTotal1 = (montoTotal1+dato1);	
	                    
	                 
	                    }
	                     
				
	
	                 });
	                 
					
		     	       totaldisp = parseFloat(montoTotal1);

		                var  totaldisp2 = number_format(totaldisp,2,',','.');

		                $('#montoTotal').html(totaldisp2);	   
					
				}
			   
	
	        	
	        	
	        }else{
	        	
	        	

		   if($("#tablaCategoriaPartida > tr").length < 2){
		   $('#selectProyAcc').focus();
		   $("#cuerpoCategoriaPartida").hide('fade',300); 

			}

		   
	        }

		}
		   
		   
		   
		   
		   
		   function eliminarPartidaDisminuir(obj) {

			   objTrs = $(obj).parents("tr.trCaso");
			   objTrs.hide(100).remove(); 
	
		   if($("#tablaCategoriaPartidaDisminuir > tr").length < 2){
		   $('#selectProyAcc').focus();
		   $("#tablaCategoriaPartidaDisminuir").hide('fade',100); 
		   $("#cuerpoCategoriaPartidaDisminuir").hide('fade',100); 
			}

		   
	        

		}
		   
		   function eliminarPartidaRecibir(obj) {

			   objTrs = $(obj).parents("tr.trCaso");
			   objTrs.hide(100).remove(); 
	
		   if($("#tablaCategoriaPartidaCredito > tr").length < 2){
		   $('#selectProyAcc').focus();
		   $("#tablaCategoriaPartidaCredito").hide('fade',100); 
		   $("#cuerpoCategoriaPartidaCredito").hide('fade',100); 
		   
			}

		   
	        

		}
		   
		   
		   
		   function CambiarPartida(obj) {
			   
			  
			 
			  $("#cuerpoCategoriaPartidaCredito").hide('fade',300);   
			   $('#tablaCategoriaPartidaCredito').children('tr.trCaso').remove();
			   
			  $("#cuerpoCategoriaPartidaDisminuir").hide('fade',300);   
			   $('#tablaCategoriaPartidaDisminuir').children('tr.trCaso').remove();
			   
			   
			    $('#totalDisp').html('0,00');	   
	      		$('#montoTotal').html('0,00');

	      		    $('#Partida').val('');
		      		$('#filsed1').show(200);
					$('#divPartida').hide();
				    $('#tablacategoria').hide();
					$('#trcategoriaesp').hide();
					$('.patidaseleccionada').hide();
				    $('#selectProyAcc').val('');
				    $('#selectProyAcc').show();
				    $('#selectProyAcc').focus();
			   
				
			}
	
		   function EliminarPartidaReceptora(obj) {
			   
			   
			   objTrs = $(obj).parents("tr.trCaso2");
			   objTrs.hide(100).remove(); 
			   
				if($("#tablaCategoriaPartidaReceptora > tr").length < 2){
					
					

					   $("#cuerpoCategoriaPartidaReceptora").hide('fade',300); 
  
		        		$('#totalDisp').html('0,00');	
		        		
		        		
					   				   
	
				}else{
					
					
					 var montoTotal1 = 0;
	                 var dato1 = 0 ;
	                 $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {

	                    if(this.value != ''){

	                    dato1  = parseFloat(this.value);
	                    	
	                    montoTotal1 = (montoTotal1+dato1);	
	                    
	                 
	                    }
	                     
				
	
	                 });
	                 
					
		     	       totaldisp = parseFloat(montoTotal1);
		     	       
		     	       

		                var  totaldisp2 = number_format(totaldisp,2,',','.');
		                
		             
		                
		                $('#totalDisp').html(totaldisp2);	   
					
				}
			   

				
				
				
				
			   
		   }

	            
	     	   function GetDisponibilidad(categoriaTipo,id_proy_accion,id_especifica,partida){
	    		   
	    		   var disponibilidad = null;
	    		   
	    		      $.ajax({
	    					
	    				    async:	false,
	    				    data: {
	    						accion: "GetDisponibilidadPartida",
	    						tipo: categoriaTipo,
	    						id_proy_accion: id_proy_accion,
	    						id_especifica: id_especifica,
	    						partida: partida,
	    						pmod:"true",
	    						tipoRespuesta: "json"
	    					},
	    					url:	"../../acciones/pcta/pcta.php",
	    					type:	"post",
	    					dataType:"json",
	    					success: function(json){
	    						
	    						disponibilidad = json;
	    						
	    				        }
	    			        });
	    		      
	    		      return disponibilidad;
	                   
	    		   }
	    
	     	   
	     	   

	     	   

	     	  
	     	     
	     	     
	     	     
	     	     
	     	    function validarNumerosfloat(campo) {

	    			
	    			var price = campo;
	    			
	    			var intRegex = /^([0-9])*[.]?[0-9]*$/;

	    			campo2 = trimDato(campo);
	    	    	 
	    		    if ((price.match(intRegex)) && (campo2 != false)) {
	    		       
	    		    	return 1;
	    		    	
	    		    } else {
	    		    	

	    		    	return false;
	    		    } 
	    		    
	    		  
	    		}
	     	    
	     	    
	     	    
	     	   function  trimDato(dato){

	            	dato2 = trim(dato);


	            	if(dato2 != ''){
	            		return "1";
	                       
	                }else{
	                	return false;
	                	
	                    }
	            	
	                }
	     	   

	     	  
	     		function validarcaracterespecial(campo) {

	    			var price = campo;
	    			
	    			var intRegex = /^[^'"@?¿·ª~!\¿?=)]*$/;

	    			campo2 = trimDato(campo);
	    	    	 
	    		    if ((price.match(intRegex)) && (campo2 != false)) {
	    		       
	    		    	return 1;
	    		    	
	    		    } else {
	    		    	

	    		    	return false;
	    		    } 
	    		    
	    		  
	    		}
	     	  
	     	  
	     		 function validarNumerosInt(campo) {

	     			
	     			var price = campo;
	     			
	     			var intRegex = /^([0-9])*$/;

	     			campo2 = trimDato(campo);
	     	    	 
	     		    if ((price.match(intRegex)) && (campo2 != false)) {
	     		       
	     		    	return 1;
	     		    	
	     		    } else {
	     		    	

	     		    	return false;
	     		    } 
	     		    
	     		  
	     		}
	     	  
	     		function validarcaracterespecial2(campo) {

	    			var price = campo;
	    			
	    			var intRegex = /^[^'"@?¿·ª~!\¿?=)]*$/;

	    			campo2 = trimDato(campo);
	    	    	 
	    		    if (price.match(intRegex)) {
	    		       
	    		    	return 1;
	    		    	
	    		    } else {
	    		    	

	    		    	return false;
	    		    } 
	    		    
	    		  
	    		}
	     	  

	     		
	  		function Revisar()
			{

	          if($('#fecha').val() == "")
				{
				  alert("Debe seleccionar la fecha.");
				  $('#fecha').focus();
				  return;
			    }
			    

	  			if( (!validarcaracterespecial($("#observaciones").val())))
				{
					alert("Debe indicar un motivo y \u00e9ste s\u00f3lo puede tener caracteres alfanum\u00E9ricos");
					 $('#observaciones').focus();
					return;
				}
			
	  			
	  			if($("#accionMP").val() == 2){
                  
	  			salir = false;	  
	  				
	             totalDisp  = $('#totalDisp').html();			
		  		 var num = parseInt(totalDisp.split(".").length -1);

   	        	 var i=0;
   	        	 
   	        	 
   	        	 while(i < num){
   	        	 
   	        	    dispon = totalDisp.replace('.','');
   	        	 
   	        	    i++;
   	        	    
   	              }
   	        	 

   	        	if(i >= num){
   	        		
   	        		dispon = totalDisp;
   	        	}
   	        	 
	  				
   	        	totalDisp =	parseFloat(dispon);
   	        	montoTotal  = 	$('#montoTotal').html();	
				
 	  	    	var num = parseInt(montoTotal.split(".").length -1);
 	  		
 	  		
    	        	 var i=0;
    	        	 
    	        	 while(i< num){
    	        	 
    	        	    dispon = montoTotal.replace('.','');
    	        	 
    	        	    i++;
    	        	    
    	              }
 	  				
    	        	 if(i >= num){
    	   	        		
    	   	        		dispon = montoTotal;
    	   	        	}
    	   	        	 
    	        	 montoTotal	 = parseFloat(dispon);
    	        	 
    	        	 if($("#tablaCategoriaPartida > tr").length < 2){

						 alert('Debe indicar las partidas que ceder\u00e1n monto presupuestario');
						 return;
						 
						 salir = true;	   

					 }else if($("#tablaCategoriaPartidaReceptora > tr").length < 2){

							 alert('Debe indicar al menos una partida que recibir\u00e1 monto presupuestario');
							 return;
							 salir = true;	   
	 
					}else if(totalDisp <= 0){
    	        		 
    	        		 alert("El monto a recibir debe ser mayor a cero");
    	        		 
						 salir = true;	   
						 
    	        		 
    	        	 }else if(montoTotal < totalDisp || montoTotal > totalDisp ) {
    	        		 
    	        		 alert('El monto que cede y el monto que recibe deben ser iguales');
    	        		 
						 salir = true;	   
						 
    	        		 
    	        	 }else{

	    	        	  montoTotal1 = 0;

						 $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {
							 
							 $Obj2 = $(this).parent("td").parent("tr.trCaso2").children('.tdPartida').children('input');		
		      	            	
				      	            if(this.value != ''){

				      	            	 dato1  = QuitarCaracter(this.value,".");
				      	            	 
				      	         	   
				      	             if(dato1 == "0" && salir == false ){
				      	            	 
				      	           	alert("Debe indicar un monto v\u00e1lido para la partida ("+$Obj2.val()+"), Nota: El separador de decimal debe ser','");
				      	            	 
				      	                $Obj2.focus();
				      	            	return;
				      	            	salir = true;   
						      	      	
				      	             }  
				      	            
				      	                
				      	            }else{
				      	            	
					      	     	alert("Debe indicar un monto v\u00e1lido para la partida ("+$Obj2.val()+"), Nota: El separador de decimal debe ser','");

					      	     	$Obj2.focus()
				      	            	return;
					      	     	
					      	     	salir = true;	  
					      	     	
				      	            }
				      	            
				      	  });

    	        	 
    	        	 montoTotal1 = 0;

					 $('input[name=\'mpresupuestariadisp[monto][]\']').each(function (index) {
						 
						 $Obj2 = $(this).parent("td").parent("tr.trCaso").children('.tdPartida').children('input');		
	      	            	
			      	            if(this.value != ''){

			      	            	 dato1  = QuitarCaracter(this.value,".");
			      	            	 
			      	         	   
			      	             if(dato1 == "0" && salir == false){
			      	            	 
			      	           	alert("Debe indicar un monto v\u00e1lido para la partida ("+$Obj2.val()+"), Nota: El separador de decimal debe ser','");
			      	            	 
			      	           	
			      	          $Obj2.focus();
			      	            	return;
			      	            	
			      	            	salir = true;	  
					      	      	
			      	             }  
			      	            
			      	                
			      	            }else{
			      	            	
				      	     	alert("Debe indicar un monto v\u00e1lido para la partida ("+$Obj2.val()+"), Nota: El separador de decimal debe ser','");

				      	     	$Obj2.focus()
			      	            	return;
				      	     	
				      	     	salir = true;	  
			      	            }
			      	            
			      	  });
					 
	        	 }
    	        	 

    	        	 
    	        	 if(salir == false){
		    	         
		        		 Enviar();
		        		 
		        		 }

				}else if($("#accionMP").val() == 1){
					

					if($("#tablaCategoriaPartidaCredito > tr").length < 2){

						 alert('Debe indicar la partida que aumentar\u00e1 el monto presupuestario');
						 return;
					 }else{
						
						 var salir = false;
						 
				    	 $Obj3 = $("#tablaCategoriaPartidaCredito").children("tr.trCaso").children('.tdDisponibilidad');
					    	
					     $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {
					    	 
					    	 if(salir == false){

						    	var obj = $(this).parent("td").parent("tr.trCaso");

						    	var  partida = obj.find('td.tdPartida input');

					
				    	    	 if(!validarNumerosfloat(QuitarCaracter(this.value,".").toString()) || QuitarCaracter(this.value,".") <= 0)
				    	         {
				    	    		  
				    	    		alert("Debe indicar un monto v\u00e1lido para la partida ("+partida.val()+"), Nota: El separador de decimal debe ser ','");
				    	    		$(this).val(''); 
						            $(this).focus(); 
									salir = true;	   
									return;

				    	         }
					     }

					     });

					 }
					
					
					
					
					 if(salir == false){
		    	         
			       	 Enviar();
			        		 
			        		 }
					
					
					
				}else{
					
					 if($("#tablaCategoriaPartidaDisminuir > tr").length < 2){

						 alert('Debe indicar al menos una partida que recibir\u00e1 monto presupuestario');
						 return;
 
					 }else{
						 
						 var salir = false;
						 
				    	 $Obj3 = $("#tablaCategoriaPartidaDisminuir").children("tr.trCaso").children('.tdDisponibilidad');
					    	
					     $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {
					    	 
					    	 if(salir == false){

						    	var obj = $(this).parent("td").parent("tr.trCaso");

						    	var  partida = obj.find('td.tdPartida input');

					
				    	    	 if(!validarNumerosfloat(QuitarCaracter(this.value,".").toString()) || QuitarCaracter(this.value,".") <= 0)
				    	         {
				    	    		  
				    	    		alert("Debe indicar un monto v\u00e1lido para la partida ("+partida.val()+"), Nota: El separador de decimal debe ser ','");
				    	    		$(this).val(''); 
						            $(this).focus(); 
									salir = true;	   
									return;

				    	         }else{
				    	        	 
				    	        	 var dispon = $($Obj3).html();
				    	        	 
				    	        	 var num = parseInt(dispon.split(".").length -1);
				    	        	 var i=0;
				    	        	 
				    	        	 while(i<= num){
				    	        	 
				    	        	    dispon = dispon.replace('.','');
				    	        	 
				    	        	    i++;
				    	        	    
				    	              }
				    	        	 
				    	        	 
				    	        	 montoTotal1 = 0;

				    	        	if(parseFloat(dispon) <  parseFloat(montoTotal1)){
				    	        		
				    	        		 alert("El monto a disminuir, no debe de ser mayor a la disponibilidad, Nota: El separador de decimal debe ser ','");
				    	        		
				    	                $(obj).val('');
				    	                $(obj).focus();	 
				    	                return false;
				    	        	 
				    	        	 
				    	        	 
				    	         }
				    	      }
				    	    	 
					     }

					     });
					     

			    	         
			        		 if(salir == false){
				    	         
				        		 Enviar();
				        		 
				        		 }
							 					 }
					 
				}

	  			
			}
	  		
	  		
	  		
	  		
	  		function Enviar(){
	  			


	  			 var montoTotal = $('#totalDisp').html();
	        	 var num = parseInt(montoTotal.split(".").length -1);
	        	 var i=0;
	        	 
	        	 while(i<= num){
	        	 
	        		 montoTotal = montoTotal.replace('.','');
	        	 
	        	    i++;
	        	    
	              }	
	        	 
	  			$('#montoTotalHidden').val(parseFloat(montoTotal));
	
	  	

	  			if($("#pmodAmodificar").html() != null){
	  				

	  				$('#regisFisDigiEli').val(regisFisDigiEli);
	  				$('#regisNombreDigital').val(regisNombreDigital);
	  				
	  	  		  if(confirm("¿Est\u00E1 seguro que desea actualizar esta modificaci\u00f3n presupuestaria?")){
	  	  			  
	  	  			  
	  	  		  montoTotal1 = 0;

					 $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {

		      	            if(this.value != ''){
		      	            	
		      	            	 dato1  = QuitarCaracter(this.value,".");
		      	            	 
		      	            	$(this).val(dato1);
		      	         	   
		      	            }
		      	            
		      	 	 	 });
					 
					 if($("#accionMP").val() == 2){
					 
					  montoTotal1 = 0;

						 $('input[name=\'mpresupuestariadisp[monto][]\']').each(function (index) {

			      	            if(this.value != ''){
			      	            	
			      	            	 dato1  = QuitarCaracter(this.value,".");
			      	            	 
			      	            	$(this).val(dato1);
			      	         	   
			      	            }
			      	            
			      	 	 	 });
					 };  
	  	  			  
	  	  			
	  	  				$('#formMP').attr('action',"../../acciones/modificacionespresupuestarias/mpresupuestarias.php?accion=RegistrarModificar");

	  	  				if(tamanoFile > 0 ){
	  	  					
	  	  	           	$('#file_upload').uploadify('upload','*');

	  	  					}else{
	  	  						
	  	  						$('#formMP')[0].submit();
	  	  						
	  	  				   }
	  	  		  }
	  	  		  
	  	 
	  				
	  				
	  			}else{	
	  			
	  		  if(confirm("¿Est\u00E1 seguro que desea generar esta modificaci\u00f3n presupuestaria?")){
	  			  
	  			  montoTotal1 = 0;
	  			  
					 $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {

		      	            if(this.value != ''){
		      	            	
		      	            	 dato1  = QuitarCaracter(this.value,".");
		      	            	 
		      	            	$(this).val(dato1);

		      	            }
		      	            
		      	 	 	 });
					 
					 if($("#accionMP").val() == 2){
					  montoTotal1 = 0;

						 $('input[name=\'mpresupuestariadisp[monto][]\']').each(function (index) {

			      	            if(this.value != ''){
			      	            	
			      	            	 dato1  = QuitarCaracter(this.value,".");
			      	            	 
			      	            	$(this).val(dato1);
			      	         	   
			      	            }
			      	            
			      	 	 	 });
						 
					 };
	  			  
	  				$('#formMP').attr('action',"../../acciones/modificacionespresupuestarias/mpresupuestarias.php?accion=Registrar");

	  				LlenarCadenaSigiente();
	  				
	  				if(tamanoFile > 0 ){
	  					
	  	           	$('#file_upload').uploadify('upload','*');

	  					}else{
	  						
	  						$('#formMP')[0].submit();
	  						
	  				   }
	  				
	  		  }
	  		  
	  			}

	  		}
	  		

	  	
	  		
	  		 function CategoriaPartidaAgregar(objA){
	  			
	  	 	   if($("#tablaCategoriaPartida > tr").length < 3){

	  	 		$('#tablaCategoriaPartida').children("tr.trsinregistro").remove();
					
			  	}
	  			

	  			objTrs = $(objA).parents("tr.trCaso");
	  			objTrs.find('input').attr('disabled','');
	  			objTrs.children("td.tdMonto").show();
	  			
	  			objTrs.find('a').html('Quitar');
				objTrs.find('a').unbind('click');
                objTrs.find('a').bind('click', function(){
                	
                	  eliminarCategoriaPartida(this);
                	  
			 		});
	  		
	  			tdMonto = objTrs.children("td.tdMonto")[0];
	  			
	  			var input = document.createElement("input");
		 		input.setAttribute("type","text");
		 		input.setAttribute("name","mpresupuestaria[monto][]");
		 		input.setAttribute("style","height:22px;width:70;margin:0;border:2px #D8D8D8 dotted solid; font-size:10px");
		 		input.value= '';
		 		tdMonto.appendChild(input);

                $(input7).keyup(function(){
		 			
		 			formato_num($(this));

		 		});
	  			
	  			
	  			 var tbody = $('#tablaCategoriaPartida')[0];
		  		 var fila = objTrs[0];
		  	     tbody.appendChild(fila);  
		  	     
		  		if($("#tablaCategoriaPartidaAgregar > tr").length < 2){

		  			tbody = $("#tablaCategoriaPartidaAgregar")[0];

					 var fila = document.createElement("tr");
						 fila.className='normalNegro trsinregistro';

						var columna1 = document.createElement("td");
						columna1.setAttribute("valign","top");
						columna1.setAttribute("colspan","8");
						columna1.appendChild(document.createTextNode('No se encontraron registros')); 
					 
						fila.appendChild(columna1);
						tbody.appendChild(fila);
					
			    }
		  		 
	  		 }
	  		
	  		
	  		 function CategoriaPartidaDesagregar(objTrs){
	  			 
	  			 
	  			
	  			if($("#tablaCategoriaPartidaAgregar > tr").length < 2){

					$("#cuerpoCategoriaPartidaEliminada").show(100);
					
			    }
	  			
	  			
	  		 	$('#tablaCategoriaPartidaAgregar').children("tr.trsinregistro").remove();
	  			 
	  			 
	  		   objTrs.find('input').attr('disabled','disabled');
			   objTrs.children("td.tdMonto").find('input').remove();
			   objTrs.children("td.tdMonto").hide();
			   
			   objTrs.find('a').parent('td').attr('align','center');
			   objTrs.find('a').html('Insertar');
			   objTrs.find('a').unbind('click');
			   
			   objTrs.find('a').bind('click', function(){
				   CategoriaPartidaAgregar(this);
		 		});
			   
			   
			   var tbody = $('#tablaCategoriaPartidaAgregar')[0];
	  		   var fila = objTrs[0];
	  		   
		 	   tbody.appendChild(fila);   
	  			 
		 	  $("#cuerpoCategoriaPartidaEliminada").show(100);
		 	  
	  		 }
	  		
			   function eliminarCategoriaPartida(objA){

				   objTrs = $(objA).parents("tr.trCaso");
	
				   if($("#compAsociado").val() != ''){

					   CategoriaPartidaDesagregar(objTrs); 
					   
					   
				   }else{
					   
					   objTrs.hide(100).remove(); 
					   
				   }
				   
				   objTd = objTrs.children("td.tdMonto");
				   objInput = objTd.children("input");
				   
		          

				if($("#tablaCategoriaPartida > tr").length < 2){

					 if($("#compAsociado").val() != ''){
						 
						 tbody = $("#tablaCategoriaPartida")[0];

						 var fila = document.createElement("tr");
							 fila.className='normalNegro trsinregistro';

							var columna1 = document.createElement("td");
							columna1.setAttribute("valign","top");
							columna1.setAttribute("colspan","9");
							columna1.appendChild(document.createTextNode('No se encontraron registros')); 
						 
							fila.appendChild(columna1);
							tbody.appendChild(fila);
						
					
					 }else{
						 
					    $("#cuerpoCategoriaPartida").hide(200);
					 
					 }
					
					
			         var montoTotal2 = number_format(0,2,',','.');
			 		 ver_monto_letra(0, 'monto_letras','');
			 		 $('#montoTotal').html(montoTotal2);

					}else{
						
						  var montoTotal1 = 0;
			      	      var dato1 = 0 ;
			      		
					         $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {

					             if((this.value != '')){

					          	   dato1  = parseFloat(this.value);
					               montoTotal1 = (montoTotal1+dato1);	
					         
					                 
					                 
					             }
					              
					  		    
					  	 		
					  	 	 });

					      
					         var montoTotal2 = number_format(montoTotal1,2,',','.');
					 		 ver_monto_letra(montoTotal1, 'monto_letras','');
					 		 $('#montoTotal').html(montoTotal2);  
				      			
				   

					}

				}  
	  		
	  		

	  		function comparar_fechas(fecha_inicial,fecha_final) // Formato
																// dd/mm/yyyy
	  		{ 
	  			
	  		  var dia1 = fecha_inicial.substring(0,2);
	  		  var mes1 =fecha_inicial.substring(3,5);
	  		  var anio1=fecha_inicial.substring(6,10);
	  			
	  		  var dia2 =fecha_final.substring(0,2);
	  		  var mes2 =fecha_final.substring(3,5);
	  		  var anio2=fecha_final.substring(6,10);

	  		  dia1 = parseInt(dia1,10);
	  		  mes1 = parseInt(mes1,10);
	  		  anio1= parseInt(anio1,10);

	  		  dia2 = parseInt(dia2,10);
	  		  mes2 = parseInt(mes2,10);
	  		  anio2= parseInt(anio2,10); 
	  				
	  		  if ( (anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2)) )
	  		  {
	  			alert("La fecha inicial no debe ser mayor a la fecha final"); 
	  			return false;
	  			
	  		  }
	  		  
	  		    return true;
	  		}
	  		
	  		
	  		
    	     function  MontoPartida(obj){
    	    	  
	        	 if($("#accionMP").val() == 2){
	        		 
	      

	        	if($(obj).parent("td").parent("tr").attr('class') == "normalNegro trCaso" ){
	        		

	        		 $Obj2 = $(obj).parent("td").parent("tr.trCaso").children('.tdPartida').children('input');		
	        		 
	        		 Obj3 = $(obj).parent("td").parent("tr.trCaso").children('.tdDisponibilidad');		
	        		 

	        		 montoTotal1 = 0;

					 $('input[name=\'mpresupuestariadisp[monto][]\']').each(function (index) {

   	      	            if(this.value != ''){
   	      	            	
   	      	               dato1  = QuitarCaracter(this.value,".");
   	      	               montoTotal1 = (montoTotal1 + dato1);
   	      	                
   	      	            }
   	      	            
   	      	 	 	 });
					 
				

					  	var  monto =  QuitarCaracter($(obj).val(),"."); 
					  	
						var  montodisp =  QuitarCaracter(Obj3.html(),"."); 
						
						

					 if(!validarNumerosfloat(monto.toString())){
	     	    		 
		    	    	alert("Debe indicar un monto v\u00e1lido para la partida ("+$Obj2.val()+"), Nota: El separador de decimal debe ser','");
		    	    	
		    	         $(obj).val('');
		    	         $(obj).focus();

		    	         
		    	         montoTotal1 = 0;

		    	         $('input[name=\'mpresupuestariadisp[monto][]\']').each(function (index) {
							 

			      	            if(this.value != ''){
			      	            	
			      	            	dato1  = QuitarCaracter(this.value,".");
			      	         	   
			      	               montoTotal1 = (montoTotal1 + dato1);	
			      	                
			      	            }
			      	            
			      	 	 	 });

		    	   
		                var  montoTotal2 = number_format(montoTotal1,2,',','.');
		        		$('#montoTotal').html(montoTotal2);
		    	         
		    	         
		    			   
		    			 return;
		    	 		
		    	         }else if(montodisp < monto){
		    	        	 

				    	    	alert("Debe indicar un monto para la partida ("+$Obj2.val()+"), menor a su disponibilidad. Nota: El separador de decimal debe ser ','");
				    	    	
				    	         $(obj).val('');
				    	         $(obj).focus();

				    	         
				    	         montoTotal1 = 0;

				    	         $('input[name=\'mpresupuestariadisp[monto][]\']').each(function (index) {
									 

					      	            if(this.value != ''){
					      	            	
					      	            	dato1  = QuitarCaracter(this.value,".");
					      	         	   
					      	               montoTotal1 = (montoTotal1 + dato1);	
					      	                
					      	            }
					      	            
					      	 	 	 });

				    	   
				                var  montoTotal2 = number_format(montoTotal1,2,',','.');

				        		$('#montoTotal').html(montoTotal2);
				    	         
				    	         
				    			   
				    			 return;
		    	        	 
		    	         }else{
			     	        	
		    	                var  montoTotal2 = number_format(montoTotal1,2,',','.');
		    	                $('#montoTotal').html(montoTotal2);		
		    	          }
				    
					 
	        		
	        		
	        	}else{
	        		
	    	    	 $Obj2 = $(obj).parent("td").parent("tr.trCaso2").children('.tdPartida').children('input');		

	        	
				 montoTotal1 = 0;
    	
				 $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {
					 

	      	            if(this.value != ''){
	      	            	
	      	            	dato1  = QuitarCaracter(this.value,".");
	      	         	   
	      	               montoTotal1 = (montoTotal1 + dato1);	
	      	                
	      	            }
	      	            
	      	 	 	 });
				 
				
				  	var  monto =  QuitarCaracter($(obj).val(),".");

					
	     	    	 if(!validarNumerosfloat(monto.toString())){
	     	    		 
    	    		alert("Debe indicar un monto v\u00e1lido para la partida ("+$Obj2.val()+"), Nota: El separador de decimal debe ser','");
    	         $(obj).val('');
    	         $(obj).focus();

    	         
    	         montoTotal1 = 0;

				 $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {
					 

	      	            if(this.value != ''){
	      	            	
	      	            	dato1  = QuitarCaracter(this.value,".");
	      	         	   
	      	               montoTotal1 = (montoTotal1 + dato1);	
	      	                
	      	            }
	      	            
	      	 	 	 });

    	   
                var  montoTotal2 = number_format(montoTotal1,2,',','.');
        		$('#totalDisp').html(montoTotal2);
    	         
    	         
    			   
    			 return;
    	 		
    	         }else{

    	                var  montoTotal2 = number_format(montoTotal1,2,',','.');

    	        		$('#totalDisp').html(montoTotal2);
    	        	
    	          }
    	        	
    	         } 	 
	            }else if($("#accionMP").val() == 1){
	        		 

	        		 
	        		 $Obj2 = $(obj).parent("td").parent("tr.trCaso").children('.tdPartida').children('input');		
	        		  
	        		  
	 				 montoTotal1 = 0;
	     	
	 				 $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {
	 					 

	 	      	            if(this.value != ''){
	 	      	            	
	 	      	            	dato1  = QuitarCaracter(this.value,".");
	 	      	         	   
	 	      	               montoTotal1 = (montoTotal1 + dato1);	
	 	      	                
	 	      	            }
	 	      	            
	 	      	 	 	 });
	 				 
	 				var  monto =  QuitarCaracter($(obj).val(),".");

	     	    	 if(!validarNumerosfloat(monto.toString())){
	     	    		 
	     	    		alert("Debe indicar un monto v\u00e1lido para la partida("+$Obj2.val()+"), Nota: El separador de decimal debe ser ','");
	     	         $(obj).val('');
	     	         $(obj).focus();

	     	         
	     	         montoTotal1 = 0;

	 				 $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {

	 	      	            if(this.value != ''){
	 	      	            	
	 	      	            	dato1  = QuitarCaracter(this.value,".");
	 	      	         	   
	 	      	               montoTotal1 = (montoTotal1 + dato1);	
	 	      	                
	 	      	            }
	 	      	            
	 	      	 	 	 });
	 				 
	     	         
	     	      

	 				totaldisp = parseFloat(montoTotal1);

	                 var  montoTotal2 = number_format(montoTotal1,2,',','.');

	                $('#totalDisp').html(montoTotal2);	   
	     	         
	     	         
	     			   
	     			 return;
	     			 
	     			 }else{
	     				 
	     	        		 var montoTotal1 = 0;
	     	                 var dato1 = 0 ;
	     	                 $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {

	     	                    if(this.value != ''){
	     	                 	   
	     	                 	  
	     	                    	dato1  = QuitarCaracter(this.value,".");
	     	                    	
	     	                    montoTotal1 = (montoTotal1+dato1);	
	     	                    
	     	                 
	     	                    }
	     	                     
	     	         		    
	     	         	 		 
	     	         	 	 });
	     	                 

	     	 				totaldisp = parseFloat(montoTotal1);

	     	                 var  montoTotal2 = number_format(montoTotal1,2,',','.');

	     	                $('#totalDisp').html(montoTotal2);	   
	     	     	         

	     	        	
	     	         }
	     	    	 
	 
	        	 }else{
	        		 
	        
	        		 
	        		 $Obj2 = $(obj).parent("td").parent("tr.trCaso").children('.tdPartida').children('input');		
	        		// $Obj3 = $("#tablaCategoriaPartidaDisminuir").children("tr.trCaso").children('.tdDisponibilidad');
	        		 
	        		 $Obj3 = $(obj).parent("td").parent("tr.trCaso").children('.tdDisponibilidad');		
	        		 
	        		 
	        		  
	        	
	 				 
	 				var  montoTotal1 =  QuitarCaracter($(obj).val(),".");
		     	       
	     	    	 if(!validarNumerosfloat(montoTotal1.toString())){
	     	    		 
	     	    		alert("Debe indicar un monto v\u00e1lido para la partida("+$Obj2.val()+"), Nota: El separador de decimal debe ser ','");
	     	         $(obj).val('');
	     	         $(obj).focus();

	     			 return;
	     			 
	     			 }else{
	     				 
	     	          var dispon = $($Obj3).html();
	     	        
	  
	     	        	dispon  = QuitarCaracter(dispon,".");
	     	        	 
	     	        	
	     	  
	     	        	
	     	         // alert( parseFloat(dispon) +" / " + parseFloat(montoTotal1));
	     	        	 
	     	        	if(parseFloat(dispon) <  parseFloat(montoTotal1)){
	    	        		
	    	        		 alert("El monto a disminuir no debe ser mayor a la disponibilidad. Nota: El separador de decimal debe ser ','");
	    	        		
	    	                $(obj).val('');
	    	                $(obj).focus();
	    	                
	    	       		   
	    	       		 return;
	    	        		
	    	        	}else{

	     	        		 var montoTotal1 = 0;
	     	                 var dato1 = 0 ;
	     	                 $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {

	     	                    if(this.value != ''){
	     	                 	   
	     	                 	  
	     	                    	dato1  = QuitarCaracter(this.value,".");
	     	                    	
	     	                    montoTotal1 = (montoTotal1+dato1);	
	     	                    
	     	                 
	     	                    }
	     	                     
	     	         		    
	     	         	 		 
	     	         	 	 });
	     	                 

	     	 				totaldisp = parseFloat(montoTotal1);

	     	                 var  montoTotal2 = number_format(montoTotal1,2,',','.');

	     	                $('#totalDisp').html(montoTotal2);	   
	     	     	         

	     	        	
	     	         }
	     	    	 
	     			 }
 
	        		 
	        	 }
    	         
    
    	      }	
    	     	
    	     
    	     
    			function AgregarRegistroFisico(){
    				
    				

    				  
    				   var tbody = $('#tablaBodyRespaldoFisico')[0];

    				 
    					var dato1= $("#pctaRespaldoFisico").val();

    					
    					var fila = document.createElement("tr");
    					fila.className='normalNegro trRegfisico';

  
    					var columna1 = document.createElement("td");
    					columna1.setAttribute("valign","top");
    					columna1.appendChild(document.createTextNode(dato1));
    					var input1 = document.createElement("input");
    					input1.setAttribute("type","hidden");
    					input1.setAttribute("name","RegistroFisico[]");
    					input1.value=dato1;
    					columna1.appendChild(input1);
    				
    					// OPCION DE ELIMINAR
    					var columna2 = document.createElement("td");
    					columna2.setAttribute("valign","top");
    					columna2.className = 'link';
    					deleteLink = document.createElement("a");
    					deleteLink.setAttribute("href","javascript:void(0);");
    					linkText = document.createTextNode("Eliminar");
    					deleteLink.appendChild(linkText);
    					columna2.appendChild(deleteLink);


    					
    					$(deleteLink).bind('click', function(){
    						eliminarRegistroFisico(this);
    					});


    					fila.appendChild(columna1);				
    					fila.appendChild(columna2);
    				
    					
    					tbody.appendChild(fila);

    			          

    					if($("#tablaBodyRespaldoFisico > tr").length < 3){
    						$('#tablaBodyRespaldoFisico').show('fade',300);
    						$("#cuerpoRespaldoFisico").show('fade',300);
    					}

    				
    			   }

    				   function eliminarRegistroFisico(objA){

    	                    objTrs = $(objA).parents("tr.trRegfisico");
    	                    
    	                    objTrs.hide(100).remove();


    					    if($("#tablaBodyRespaldoFisico > tr").length < 2){

    							$("#cuerpoRespaldoFisico").hide(200);

    					  }
    					}  
    				   
    				   function GenerarSigienteCadena(valor){

 				          if(valor){	
 				              
 					        idCadenaSigiente = valor;
 					       	Revisar();  

 				          }
 				          
 				     }
    				   
    				   
    				   function LlenarCadenaSigiente(){

    				    	  $("#idCadenaSigiente").remove();
    							var	div = $("#accionesEjecutar")[0];
    							var input1 = document.createElement("input");
    							input1.setAttribute("type","hidden");
    							input1.setAttribute("id","idCadenaSigiente");
    							input1.setAttribute("name","idCadenaSigiente");
    							input1.value= idCadenaSigiente;
    							div.appendChild(input1);
    				    }

    				   
  
    				   
    				   function SearchDetallePmod(id,opcion,idcadenaactual){
    					   
 

   			  			$.ajax({
   							url: "../../acciones/modificacionespresupuestarias/mpresupuestarias.php",
   			  				dataType: "json",
   			  				data: {
   			  					accion: "SearchPmodDetalle",
   			  					tipoRespuesta: 'json',
   			  					key: id

   			  				},
   			  					success: function(json){
   			  						
   			  				
   			  						var menuAnularModificar = true;
   			  						
   			  						$('.opcionesPmodRemove').remove();
   			  						
   			  							$.each(json,function(id,val){
    	
   			  						if(observacionesDoc = val.observacionesDoc){

   			 							$('#noDocumentosAsociadosPmod').hide();
   			 							$('#tablaDocumentosAsociadosPmod').children('tr').remove();
   			 							num = 1;
   			 							
   			 							tdClass ='even';

   			 							$.each(observacionesDoc,function(id,val){
   			 								
   			 								var fecha = val.fecha; 
   			 						        var cadena1 = fecha.split(' ');
   			 						        var cadena= cadena1[0].split('-');
   			 						        
   			 						        
   			 							
   			 						//		alert(val.perfilNombre+" / "+val.observacion+" / "+cadena[2]+"-"+cadena[1]+"-"+cadena[0]);
   			 					    
   			 					tdClass = (tdClass == "even") ? "odd" : "even";
   			 						   var tbody = $('#tablaDocumentosAsociadosPmod')[0];	
   			 						   var fila = document.createElement("tr");
   			 						   fila.className=tdClass;	

   			 						   var columna1 = document.createElement("td");
   			 							columna1.setAttribute("valign","top");
   			 							columna1.setAttribute("style","font-size:10px");
   			 							columna1.appendChild(document.createTextNode(num));
   			 							
   			 							var columna2 = document.createElement("td");
   			 							columna2.setAttribute("valign","top");
   			 							columna2.setAttribute("style","font-size:10px");
   			 							columna2.appendChild(document.createTextNode(val.perfilNombre));
   			 											
   			 							var columna3 = document.createElement("td");
   			 							columna3.setAttribute("valign","top");
   			 							columna3.setAttribute("style","font-size:10px");
   			 							columna3.appendChild(document.createTextNode(val.observacion));
   			 							
   			 							var columna4 = document.createElement("td");
   			 							columna4.setAttribute("valign","top");
   			 							columna4.setAttribute("style","font-size:10px");
   			 							columna4.appendChild(document.createTextNode(cadena[2]+"-"+cadena[1]+"-"+cadena[0]));
   			 							

   			 						  //  alert(val.cargoDependencia);	
   			 								
   			 							fila.appendChild(columna1);	
   			 							fila.appendChild(columna2);
   			 							fila.appendChild(columna3);
   			 							fila.appendChild(columna4);
   			 							tbody.appendChild(fila);
   			 							num++;

   			 							});
   			 							
   			 							
   			 						}else{
   			 							
   			 							$('#tablaDocumentosAsociadosPmod').children('tr').remove();
   			 							$('#noDocumentosAsociadosPmod').show();
   			 							
   			 						}
   			  					    	
   			  					    	
   			 						
   			 						if(revisiones = val.revisiones){
   			 							
   			 							
   			 							
   			 							$('#noRegistrosRevisionPmod').hide();
   			 							$('#tablaRevisionPmod').children('tr').remove();
   			 							num = 1;
   			 							
   			 							tdClass ='even';
   			 							
   			 							$.each(revisiones,function(id,val){
   			 								
   			 							var fecha = val.fecha;
   			 							
   			 					         var cadena = fecha.split('/');

   			 								
   			 								
   			 					      tdClass = (tdClass == "even") ? "odd" : "even";
   			 					 	   var tbody = $('#tablaRevisionPmod')[0];	
   			 						   var fila = document.createElement("tr");
   			 						   fila.className=tdClass;	

   			 						   var columna1 = document.createElement("td");
   			 							columna1.setAttribute("valign","top");
   			 							columna1.setAttribute("style","font-size:10px");
   			 							columna1.appendChild(document.createTextNode(num));
   			 											
   			 							
   			 							
   			 							var columna3 = document.createElement("td");
   			 							columna3.setAttribute("valign","top");
   			 							columna3.setAttribute("style","font-size:10px");
   			 							columna3.appendChild(document.createTextNode(val.nombreApellido));
   			 							

   			 							var columna4 = document.createElement("td");
   			 							columna4.setAttribute("valign","top");
   			 							columna4.setAttribute("style","font-size:10px");
   			 							columna4.appendChild(document.createTextNode(val.cargoDependencia));
   			 						

   			 							var columna5 = document.createElement("td");
   			 							columna5.setAttribute("valign","top");
   			 							columna5.setAttribute("style","font-size:10px");
   			 							columna5.appendChild(document.createTextNode(cadena[0]+"-"+cadena[1]+"-"+cadena[2]));
   			 							
   			 							var columna6 = document.createElement("td");
   			 							columna6.setAttribute("valign","top");
   			 							columna6.setAttribute("style","font-size:10px");
   			 							columna6.appendChild(document.createTextNode(val.opcion));
   			 						  
   			 						 
   			 						  //  alert(val.cargoDependencia);	
   			 								
   			 							fila.appendChild(columna1);	
   			 							fila.appendChild(columna3);
   			 							fila.appendChild(columna4);
   			 							fila.appendChild(columna5);
   			 							fila.appendChild(columna6);
   			 							tbody.appendChild(fila);
   			 							num++;
   			 							});
   			 							
   			 							
   			 						}else{
   			 							$('#tablaRevisionPmod').children('tr').remove();
   			 							$('#noRegistrosRevisionPmod').show();
   			 							
   			 							
   			 						}	
   			  					    	
   			  					    
   			  							  $('span[detalle=\'pmod\']').html(val.id);
   									      $('td[detalle=\'fecha\']').html(val.fecha);
   									   	  $('td[detalle=\'UnidadDependencia\']').html(val.dependencia.nombre);
   			  							  $('td[detalle=\'estado\']').html(val.estatus.nombre);

   			  							  
   			  							  if(val.tipoDoc == '1'){
   			  								  
   			  								tipo ="Cr&eacute;dito";
   			  								
   			  							$('td[detalle=\'montoSolicitado\']').parent('tr').hide();
   			  								  
   			  							  }else if(val.tipoDoc == '2'){
   			  								
   			  								tipo ="Traspaso";
   			  								
   			  							
   			  								  
   			  							  }else{
   			  								  
   			  								tipo ="Disminuci&oacute;n";
   			  								  
   	   			  							$('td[detalle=\'montoSolicitado\']').parent('tr').hide(); 
   			  							  }
   			  							
   			  							  
   			  						   $('td[detalle=\'tipo\']').html(tipo);
   			  
 
   			  							  if(val.estatus.id == 15){
   			  								  
   			  								  menuAnularModificar = false;
   			  								  
   			  								$('#trPmodMotivoAnulacion').show();
   			  								  
   			  								 motivo = "No especificado";
   			  								  
   			  								  if(val.motivoAnulacion){
   			  									  
   			  									 motivo = val.motivoAnulacion.observacion;
   			  								  }
   			  								  
   			  								  
   			  								$('td[detalle=\'estado\']').css('color','red');
   			  								  
   			  								$('td[detalle=\'motivoAnulacion\']').html(motivo);
   			  								  
   			  							  }else{
   			  								  
   			  								$('#trPmodMotivoAnulacion').hide();
   			  								
   			  								$('td[detalle=\'estado\']').html('Activo');
   			  								  
   			  							  }
   			  							 
   			  						 	  $('td[detalle=\'observacion\']').html(val.Observacion);
   			  						 	  
   			  					       
   			  							  if(val.mpresupuestariaImputas){

   			  					      	$('#tablaCategoriaPartidaPmod').children("tr.trCaso").remove();   

   			  							  $.each(val.mpresupuestariaImputas ,function(id2,val2){
   			  								  
   			  	                           if(val2.tipoImpu > 0){

   			  								var dato1= 'Proyecto';
   			  								var dato2= val2.proyecto.nombre;
   			  								var dato4= val2.proyectoEspecifica.nombre;
   			  								var dato3= val2.proyectoEspecifica.centroGestor+'/'+val2.proyectoEspecifica.centroCosto;


   			  	                           }else{

   			  	                        	var dato1= 'Ac.Centralizada';


   			  	        						var dato2= val2.accionCentralizada.nombre;
   			  	    							var dato4= val2.CentralizadaEspecifica.nombre;
   			  	    							var dato3= val2.CentralizadaEspecifica.centroGestor+'/'+val2.CentralizadaEspecifica.centroCosto;
   			  	                           }
   			  										var dato5= val2.partida.id;
   			  										var dato6= val2.partida.nombre;

   			  								 var dato7 =  number_format( val2.monto ,2,',','.');
   			  								 
   			  							     if(val.tipoDoc == '1'){
   			  			   			  		
   			  							        var monto=  dato7;
	     			  							  
   	 	  
   	     			  							  }else if(val.tipoDoc == '2'){
   	     			  				
   	     			  							 if(val2.tipo == '0'){	  
   	     			  								  
   	     			  							 
   	     			  							 var monto=  dato7;
  	     			  							  
   	     			  								
   	     			  							 }
   	     	
   	     			  							  }else{
   	     			  								  
   	     			  							 var monto=  dato7;
  	     			  							  
   	     			  								  
   	     			  							  }
   			  								 
   			  							     
   			  							$('td[detalle=\'montoSolicitado\']').html(monto);
   			  								 
   			  								 if(val2.tipo == '1'){
   			  									 
   			  								var dato8 = 'Recibe';
   			  									 
   			  								
   			  									 
   			  								 }else{
   			  									 
   			  								  var dato8 = 'Cede';
   			  									 
   			  								 }
   			  								 
   			  								// alert(dato1+'\n'+dato2+'\n'+dato3+'\n'+dato4+'\n'+dato5+'\n'+dato6+'\n'+dato7);
   			  						
   			  								 
   			  								var tbody = $('#tablaCategoriaPartidaPmod')[0];
   			  								
   			  								var fila = document.createElement("tr");
   			  								fila.className='normalNegro trCaso';

   			  	  
   			  								var columna1 = document.createElement("td");
   			  								columna1.setAttribute("valign","top");
   			  								columna1.setAttribute("style","font-size:10px");
   			  								columna1.appendChild(document.createTextNode(dato1));
   			  												
   			  								var columna2 = document.createElement("td");
   			  								columna2.setAttribute("valign","top");
   			  								columna2.setAttribute("style","font-size:10px");
   			  								columna2.appendChild(document.createTextNode(dato2));
   			  							
   			  								var columna3 = document.createElement("td");
   			  								columna3.setAttribute("valign","top");
   			  								columna3.setAttribute("style","font-size:10px");
   			  								columna3.appendChild(document.createTextNode(dato3));
   			  								

   			  								var columna4 = document.createElement("td");
   			  								columna4.setAttribute("valign","top");
   			  								columna4.setAttribute("style","font-size:10px");
   			  								columna4.appendChild(document.createTextNode(dato4));
   			  							

   			  								var columna5 = document.createElement("td");
   			  								columna5.setAttribute("valign","top");
   			  								columna5.setAttribute("style","font-size:10px");
   			  								columna5.appendChild(document.createTextNode(dato5));

   			  								var columna6 = document.createElement("td");
   			  								columna6.setAttribute("valign","top");
   			  								columna6.setAttribute("style","font-size:10px");
   			  								columna6.appendChild(document.createTextNode(dato6));
   			  								

   			  								var columna7 = document.createElement("td");
   			  								columna7.setAttribute("valign","top");
   			  								columna7.setAttribute("style","font-size:10px");
   			  								columna7.appendChild(document.createTextNode(dato7));
   			  								
   			  						    	var columna8 = document.createElement("td");
			  								columna8.setAttribute("valign","top");
			  								columna8.setAttribute("style","font-size:10px");
			  								columna8.appendChild(document.createTextNode(dato8));
   			  			


   			  								fila.appendChild(columna1);				
   			  								fila.appendChild(columna3);
   			  								fila.appendChild(columna2);				
   			  								fila.appendChild(columna4);
   			  								fila.appendChild(columna5);				
   			  								fila.appendChild(columna6);
   			  							  	fila.appendChild(columna7);	
   			  							    fila.appendChild(columna8);	
   			  															
   			  								tbody.appendChild(fila);
   			  								
   			  								
   			  	
   			  							  });


   			  							  }
 
   			  							  
   			  					  $('#tbodyRespFisicosPmod').children().remove();
   			 					   $('#tbodyRespDigitalesPmod').children().remove();
   			 						respaldosDigitales = 0;
   			 						respaldosFisicos = 0;
   			 				
   			                   if(val.respaldos){
   			                     	 
  
   			 						$.each(val.respaldos,function(id3,val3){

   			 						if(val3.respTipo == 'Digital'){
   			 							respaldosDigitales++;
   			 							var tbody = $('#tbodyRespDigitalesPmod')[0];	
   			 							var fila = document.createElement("tr");
   			 							var columna = document.createElement("td");
   			 							    columna.setAttribute("style","border-bottom: 1px solid #D8D8D8");
   			                          var alink = document.createElement("a");
   			                              alink.setAttribute("href","descargarImagen.php?file="+val3.respNombre+""); 
   			                              alink.appendChild(document.createTextNode(val3.respNombre));

   			                           columna.appendChild(alink);	    
   			 							 fila.appendChild(columna);					
   			 						     tbody.appendChild(fila);


   			 						}else{
   			 							respaldosFisicos++;
   			 							var tbody = $('#tbodyRespFisicosPmod')[0];	
   			 							var fila = document.createElement("tr");
   			 							var columna = document.createElement("td");
   			 							    columna.setAttribute("style","border-bottom: 1px solid #D8D8D8");
   			                              columna.appendChild(document.createTextNode(val3.respNombre));
   			 	    
   			 							 fila.appendChild(columna);					
   			 						     tbody.appendChild(fila);
   			                        

   			 							}
   			 						
   			 						 });
   			 						 
   			 						
   			                      }

   			                    
   			 						if(respaldosDigitales > 0 ){

   			 								
   			 								$('#respDigPmod').show();

   			 							}else{

   			 							  $('#respDigPmod').hide(); 
   			 						}	
   			 						if(respaldosFisicos > 0 ){

   			 							
   			 							$('#respFisicPmod').show();

   			 						}else{

   			 						 $('#respFisicPmod').hide(); 
   			 					     }	
   			 											
   			 					

   			  					       });

   			  						
   			  					}

   			  			  });
   			  			
   			  			
   			  	
					
   			  			  
   			  	
                    $('.opcionesPmodcerrar').remove();
 					
 					if(opcion == 'pmodPorEnviarOpciones'){
 						

 						if(pctaPorEnviarOpciones){

 						

 					    	 $.each(pctaPorEnviarOpciones[idcadenaactual], function(index, value) {
 					    		 
 					   
 					    		          var fila = $('#trOpcionesDetallesPmod')[0];	
 										  var columna = document.createElement("td");
 										    columna.setAttribute("class","opcionespcta opcionesPmodcerrar");
 										    var alink = document.createElement("a");
 											alink.setAttribute("href","#");
 			                                alink.setAttribute("idCadenaSigiente",value.id_cadena_hijo);
 			                                alink.setAttribute("idOpcion",value.opcion);
 			                                alink.setAttribute("id",value.wfop_descrip);
 			                                alink.setAttribute("onclick",'AccionesPmod(this)');
 			                                alink.appendChild(document.createTextNode(value.wfop_nombre));
 				

 			                             columna.appendChild(alink);	
 			                             fila.appendChild(columna);	
	

 					    		});

 					      }
 					      
 					
 				    } else{
 	
 				    		  $('#OpcionesPdfPmod').hover(function(){

 					    		    $(this).css({
 						    		    
 					    		    	'margin-top':-7,
 					    		    	'cursor':'move'
 					
 					    		    });

 				    		    }).mouseleave(function(){

 					    		    $(this).css({

 							    		  'margin-top':-9


 				    		    });


 					    	});                   


 					   }
						

   			  		}

    				   
    				      function AccionesPmod(obj){
    				    	  obj =  $(obj);
    				    	  
    				    	  var url = false ;

    						   	  
    				    	  if(obj.attr('id') == 'Modificar'){
    						 
    				    		  url = "../../acciones/modificacionespresupuestarias/mpresupuestarias.php?accion=Modificar&pmod="+$('span[detalle=\'pmod\']').html()+"";
    				     		  window.location = url; 
    				              
    				            }else{
    				            	 if(confirm("¿Est\u00E1 seguro que desea ("+obj.attr('id')+") este pmod?")){

    				            	 if(obj.attr('id') == 'Anular' || obj.attr('id') == 'Devolver'){

    				                 var lugar = 'modificacion presuspuestaria';
    				            	 var	memo =  LlenarMemo(obj.attr('id'),lugar);	 
    				            	 

    				            	 
    				            	if(memo){
    				            	 
    					             url = "../../acciones/modificacionespresupuestarias/mpresupuestarias.php?accion=ProcesarPmod&opcion=0&memo="+memo+"&pmod="+$('span[detalle=\'pmod\']').html()+"&idCadenaSigiente="+obj.attr('idCadenaSigiente')+"&idopcion="+obj.attr('idopcion')+"&accRealizar="+obj.attr('id')+"";	   
    					             
    				            	}
    					             
    				            	 }else{
    				            		 
    				            		 
    				    	         url = "../../acciones/modificacionespresupuestarias/mpresupuestarias.php?accion=ProcesarPmod&pmod="+$('span[detalle=\'pmod\']').html()+"&idCadenaSigiente="+obj.attr('idCadenaSigiente')+"&idopcion="+obj.attr('idopcion')+"&accRealizar="+obj.attr('id')+"";	   

    				            	 }	 
    				            	 
    				            	if(url){
    				            		
    				                 window.location = url;
    				            		
    				            	}

    				   				 }else{
    				   					 
    				   					return;
    				   					 
    				   					 }

    				          	  
    				                }

    				         }
    				      
    				      function  LlenarMemo(obj,lugar){
    				          
    				          if(memo=prompt("Especifique un motivo por el cual desea "+obj+" este "+lugar)){
    				        	  
    				        	  
    				        	  return memo; 
    				    		  
    				    		    }else{

    				    		       if (memo == null)
    				    		       {
    				    		    	   if(confirm("¿Est\u00E1 seguro que desea (cancelar) la operaci\u00F3n ?")){    
    				    		    		   
    				    		    		  return false; 
    				          			
    				    		    	   }else{
    				    		    		   
    				    		    		return  LlenarMemo(obj,lugar);  
    				    		    		   
    				    		    	   }
    				    		    	   
    				    		       }else{

    				    		    	   if(confirm("¿El motivo por el cual desea "+obj+" este "+lugar+" est\u00E1 vac\u00EDo. ¿Desea (cancelar) la operaci\u00F3n.?")){    
    				    		    		   
    				    			    		  return false; 
    				    	      			
    				    			    	   }else{
    				    			    		   
    				    			    		return  LlenarMemo(obj,lugar);  
    				    			    		   
    				    			    	   } 
    				    		          }

    				    		      }
    				          
    				           
    				              }
    				      
    				        
    				        
    				        function formato_num(obj){
    				        	
    				        	
    				     	   monto = obj.val();
    				     	   
    				     		array = monto.toString().split('').reverse();
    			    			
    				   			 if (array[0] == '.'){
    				   				 
    				   			 array[0] = ',';
    				   				 
    				   			 }

    				   			 monto = array.reverse().join('');
    				     	   
    				     		if(parseInt(monto.split(",").length -1) > 1 || monto == ''){
    				     			
    				     			alert("El separador de decimal debe ser ','");
    				     			 $(obj).val('');
				        	         $(obj).focus();

				        	         
				        	         montoTotal1 = 0;

				    				 $('input[name=\'mpresupuestaria[monto][]\']').each(function (index) {

				    	      	            if(this.value != ''){
				    	      	            	
				    	      	            	dato1  = QuitarCaracter(this.value,".");
				    	      	         	   
				    	      	               montoTotal1 = (montoTotal1 + dato1);	
				    	      	                
				    	      	            }
				    	      	            
				    	      	 	 	 });
				    				 
				    				 
				        	         
    				     			 if($("#accionMP").val() == 2){

    				     				if($(obj).parent("td").parent("tr").attr('class') == "normalNegro trCaso" ){
    				     					
    				     					montoTotal1 = 0;
    				     					
    				     					 $('input[name=\'mpresupuestariadisp[monto][]\']').each(function (index) {

    					    	      	            if(this.value != ''){
    					    	      	            	
    					    	      	            dato1  = QuitarCaracter(this.value,".");
    					    	      	         	   
    					    	      	               montoTotal1 = (montoTotal1 + dato1);	
    					    	      	                
    					    	      	            }
    					    	      	            
    					    	      	 	 	 });
    				     					 
    				     				    var  montoTotal2 = number_format(montoTotal1,2,',','.');
    					                    $('#montoTotal').html(montoTotal2);	   
    				     					
    				     					
    				     				}else{    				     				 

    					                    var  montoTotal2 = number_format(montoTotal1,2,',','.');
    					                    $('#totalDisp').html('0,00');	   
  
    					                    
    				     				} 
    					                    

    				     				 
    					        		 $Obj3 = $("#tablaCategoriaPartidaCredito").children("tr.trCaso").children('.tdDisponibilidad');
    					        		 
    					        		 
    					        		  var dispon = $($Obj3).html();
    					    	        	 var num = parseInt(dispon.split(".").length -1);
    					    	        	 var i=0;
    					    	        	 
    					    	        	 while(i<= num){
    					    	        	 
    					    	        	    dispon = dispon.replace('.','');
    					    	        	 
    					    	        	    i++;
    					    	        	    
    					    	              }

    					    					totaldisp =  parseFloat(dispon) + parseFloat(montoTotal1);

    					                    var  montoTotal2 = number_format(montoTotal1,2,',','.');
    					                    var  totaldisp2 = number_format(totaldisp,2,',','.');
    					                    
    					                    
    					                    $('#totalDisp').html(totaldisp2);	   
    					            		$('#montoTotal').html(montoTotal2);
    					     				 
    				     				 
    				     			 }

    				     		}else{
    				     			
    	
    				     			array =  monto.split(",");
    				     			
    				     			if(array[1] != undefined){
    				     				
    				     			   num = format(array[0])+","+formatcoma(array[1]);
    				     				
    				     			}else{
    				     				
    				         			num =	format(array[0]);
    				     				
    				     			}

    				             obj.val(num);
    				             

    				             MontoPartida(obj);
    				     		    

    				     		} 
    				     	 }
    				   
    				  
    				      function format(input)
    				      {
    				  		var num = input.replace(/\./g,'');
    				  		
    				  		if(!isNaN(num))
    				  		{

    				  			num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
    				  		
    				  			num = num.split('').reverse().join('').replace(/^[\.]/,'');
    				  			
    				  			return num;

    				  		}

    				  		
    				      }
    				      
    				      function formatcoma(input)
    				      {
    				     	 
    				     	 var num = input;
    				  
    				  		
    				  		if(!isNaN(num))
    				  		{

    				  			num = num.toString().split('').reverse().join('').replace(/(?=\d*\?)(\d{3})/g,'$1.');
    				  		
    				  			num = num.split('').reverse().join('').slice(0,2) ;
    				  			

    				  			if(num == undefined){
    				  				
    				  				 num = '';	
    				  				
    				  			}
    				  			
    				  			return num;

    				  		}

    				  		num = '';	
    				 		return num;
    				  		
    				  		
    				      }
    				      
    				      
    				      
    				      function QuitarCaracter(params,caracter)
    				      {
    				      
    				 	 var num = parseInt(params.split(caracter).length -1);
    				   	 var i=0;
    				   	 
    				   	   monto = params;
    				   	 
    				   	 while(i<= num){
    				   		 
    				   		monto = monto.replace('.','');
    				   	    i++;
    				   	    
    				         }
    				   	 
    				   	monto = monto.replace(',','.');
    				   	monto = parseFloat(monto);
    				   	
    				   	return monto;
    				   	
    				   	
    				      }				      
    				      
    				          
    				      