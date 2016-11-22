	
var dependencia = false,
    pctaAsociado = null,
    disponibilidad = null,
    dependencia  = null,
	compImputa  = null,
	modificarComp = null
	pctaAsociado  = false,
	pctaAsociadoval  = false,
	compModMontoTotal = 0,
    params = new Array();



		$().ready(function(){
			
			

// ///////////////////////////////////////////////////////////////////////////////////// Funcion auto// ////////////////////////////////////////////////////////

			    dependencia = $("#unidadDependencia").val();
			       
				
				if($('#compromiso_descripcion').length > 0){
					
					var opts = {
							doctype  :	' <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">',
							cssClass : 'el-rte',
							lang     : 'es',
							height   : 600,
							toolbar  : 'maxi',
							cssfiles : ['js/editorlr/css/elrte-inner.css']
				
						};
				}
				

					$('#compromiso_descripcion').elrte(opts);
					$('#compromiso_descripcion2').hide();
					$('#imgDescrip2').hide();
					$("#trCompAsociado").hide();
					$('#divPartida').hide();
					$('#tablacategoria').hide();
					$('#trcategoriaesp').hide();
					$('.patidaseleccionada').hide();
					$("#cuerpoCategoriaPartida").hide();
					$("#cuerpoCategoriaPartidaEliminada").hide();
					
					
					
					$( "#dialogisss" ).dialog({ autoOpen: false });
					$( "#dialogisss").dialog({ show: "puff" });
					$( "#dialogisss").dialog({ hide: "explode" });
					$( "#dialogisss").dialog({ width: 400});
					$( "#dialogisss").dialog({ position: { my: "center top", at: "center top "}});
					

                   	if((!modificarComp) || ($('#asuntoVal').val() != "001" && $('#asuntoVal').val() != "002" && $('#asuntoVal').val() != "023") ){
                   		
						$("#estatuscomp" ).hide();
						$("#estatusfecha" ).hide();
					}
					
					 
					 if(typeof window.ver_monto_letra == 'function') {
								
						 ver_monto_letra(0, 'monto_letras','');
		          	}


					if($('#compromiso_descripcion').elrte('val')){

					  $('#spanDescripval').val(0);
					  
						mostrarOcultarEditor();
						
					}


	
	            if(modificarComp == true){

	            $('#registrar').val('Modificar');
	           
	            }

// ///////////////////////////////////////////////////////////////////////////////////// Funciones clik////////////////////////////////////////////////////////
		
			
			
			 $('#OpcionesPdfComp').click(function(event) {

				 url = "../../acciones/comp/comp.php?accion=DetalleCompPdf&comp="+$('span[detalle=\'compromiso\']').html()+"";
	       	   // window.location = url;
	       	  window.open(url, '_blank'); 

				 });
			 
			 
			 
               $("#BuscarComp").click(function() {


		          if(
		        		  
		        	$("#txt_inicio").val().length < 1  && 
				    $("#hid_hasta_itin").val().length < 1   &&
				    $("#asunto").val().length < 1   && 
				    $("#tipoActividad").val().length < 1   && 
				    $("#PartidaBusqueda").val().length < 1  && 
				    $("#compProyAccVal").val().length < 1      && 
				    $("#controlinterno").val().length < 1     && 
				    $("#palabraClave").val().length < 1     && 
				    $("#ncompromiso").val().length < 1         

		 ){
			 
			 
			 
			 alert('Debe seleccionar un campo'); 
			 
		   }else{
			   
			   if(($("#txt_inicio").val().length > 1 && $("#hid_hasta_itin").val().length < 1) ||
					($("#hid_hasta_itin").val().length > 1 && $("#txt_inicio").val().length < 1)){
				   
				   
				   alert('Debe seleccionar un rango de fecha');
				   
			   }else{

				   $('#formCompFiltro').attr('action','../../acciones/comp/comp.php?accion=BuscarCompAccion');
				   $('#formCompFiltro')[0].submit();				
		   }
		   }

	
	  
 });

			
               
               $("#BuscarCompVariacion").click(function() {


 		          if(	  
 		        	$("#txt_inicio").val().length < 1  && 
 				    $("#hid_hasta_itin").val().length < 1   &&
 				    $("#ncompromiso").val().length < 1         

 		           ){

 			         alert('Debe seleccionar un campo'); 
 			 
 		          }else{
 			   
 		        	  if(($("#txt_inicio").val().length > 1 && $("#hid_hasta_itin").val().length < 1) ||
 						($("#hid_hasta_itin").val().length > 1 && $("#txt_inicio").val().length < 1)){
 					   
 		        		  	alert('Debe seleccionar un rango de fecha');
 					   
 		        		  	return;   
 		        }
 			   
 			   
 			   if($("#txt_inicio").val().length > 1 && $("#hid_hasta_itin").val().length > 1){
 				
 				
 				
 				   	if(!comparar_fechas($("#txt_inicio").val(),$("#hid_hasta_itin").val())){
 				   		
 				   	$("#hid_hasta_itin").val('');
 				   	
 				   		return;
 	 				   
 	 				 }
 				   	
 		      }
 			 
 			   
 			  $('#formCompFiltro').attr('action','../../acciones/comp/comp.php?accion=BuscarCompVariacionAccion');
				 $('#formCompFiltro')[0].submit();
		   
 		   }
 	
 	  
  });
               
               $("#BuscarCompCausadoPagado").click(function(){


            	   if($("#hid_hasta_itin").val().length < 1){

  			         alert('Debe seleccionar la fecha de cierre');
  			         
  			       $("#hid_hasta_itin").focus();


            	   }else if($("#tipo_reporte").val().length < 1 ){

  	  			         alert('Debe seleccionar el tipo de reporte'); 
  	  			         
  	  			     $("#tipo_reporte").focus();
  			 
  		          }else{

  			 
  			   
  			  $('#formCompFiltro').attr('action','../../acciones/comp/comp.php?accion=CausadoPagadoAccion');
 			  $('#formCompFiltro')[0].submit();
 		   
  		   }
  	
  	  
   });
               
               
               
               
               
               
               

               
			  $("#ncompromiso").click(function() {
				  
				  
   				  
				  if($("#ncompromiso").val() == ''){
					  
					 $("#ncompromiso").val('comp-');
					 
				    
				  
				  
				  }
				  
			  });
				  

			
		 $('#spanDescrip').click(function(){
				 
	              mostrarOcultarEditor();
	 
          });
			 			

		 
		 
		 $('#confirmarPartida').click(function(){
			 
       	  AgregarCategoriaPartida();
	          		
		});
			
			
// /////////////////////////////////////////////////////////////////////////////////
// Funciones keypress////////////////////////////////////////////////////////
		 
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
		 
		
		 
		   $("#unidadDependencia").change(function(){

				
					if( $("#unidadDependencia").val() != '' ){
						
						if($("#tablaCategoriaPartida > tr").length < 2){
							
							$('#compAsociado').val('');
							$("#trCompAsociado").show(100);
					        dependencia = $("#unidadDependencia").val();
					        
					   	 $("#cuerpoCategoriaPartidaEliminada").hide(100);
				  		 $('#tablaCategoriaPartidaAgregar').children("tr.trsinregistro").remove();
				  		 $('#tablaCategoriaPartidaAgregar').children("tr.caso").remove();
				  		 $("#cuerpoCategoriaPartida").hide(100);
				  		 $('#compAsociado').val('');
							
							
						}else{

						if(confirm("¿Est\u00E1 seguro que desea cambiar la Unidad/Dependencia \n Al aceptar se eliminar\u00E1n todas las partidas registradas.")){
			    	 
			    	     $("#trCompAsociado").show(100);
				         dependencia = $("#unidadDependencia").val();
				       
				         $('tr.trCaso').remove();
						 $('#tablaCategoriaPartida').hide(100);
						 $("#filsed1").show(100);
						 $("#fieldset2").show(100);
						 $('#trselectProyAcc').show(100);
						 
						 $("#cuerpoCategoriaPartidaEliminada").hide(100);
				  		 $('#tablaCategoriaPartidaAgregar').children("tr.trsinregistro").remove();
				  		 $('#tablaCategoriaPartidaAgregar').children("tr.caso").remove();
				  		 $("#cuerpoCategoriaPartida").hide(100);
				  		 $('#compAsociado').val('');
			    	    
						
						}else{
							
							$("#unidadDependencia").val(dependencia);
							return;

						
						}
						
					}
						
			          }else{
			        	  
			        	$('#compAsociado').val('');
			        	    
			        	$("#trCompAsociado").hide(100);
			        	
			        	dependencia = $("#unidadDependencia").val();

			        	$('tr.trCaso').remove();
						 $('#tablaCategoriaPartida').hide(100);
						 $("#filsed1").show(100);
						 $("#fieldset2").show(100);
						 $('#trselectProyAcc').show(100);
						 
						 $("#cuerpoCategoriaPartidaEliminada").hide(100);
				  		 $('#tablaCategoriaPartidaAgregar').children("tr.trsinregistro").remove();
				  		 $('#tablaCategoriaPartidaAgregar').children("tr.caso").remove();
				  		 $("#cuerpoCategoriaPartida").hide(100);
				  		 $('#compAsociado').val('');
			        	
			        	  
			          }

			           });
			
		   
			 
		   $("#compAsociado").change(function(){
			   
				 if($("#compAsociado").val() != false ){
					 
					 $('tr.trCaso').remove();
					 $('#tablaCategoriaPartida').hide(100);
					 $("#cuerpoCategoriaPartidaEliminada").hide(100);
					 $("#cuerpoCategoriaPartida").hide(100);
					 $("#filsed1").show(100);
					 $("#fieldset2").show(100);
					 $('#trselectProyAcc').show(100);
					 $('#tablaCategoriaPartidaAgregar').children("tr.trCaso").remove();

					
				 }

			 });
			 
			 
				
				$('#selectProyAcc').change(function(){

					 var entro = false;
					 var respuenta = false;
					 var proyacc = false;

				 if($('#selectProyAcc').val() != ''){
					 
				     $('input[name=\'pcta[monto][]\']').each(function (index) {

				    	 obj = $(this).parent("td").parent("tr.trCaso");
				    	 
				    	 proyacc =   obj.find('input[name=\'pcta[codProyAcc][]\']');
				    	 
				    	
				    	 if($('#selectProyAcc').val() != proyacc.val() && entro == false){
				    		 respuenta = CentroGestoryCostoDifenente('ProyAcc'); 
				    		 entro = true; 
				    		 
				    	 }

			 	 	   });	
				     
					 }
				     
				    if(entro){
				    
							if ($('#selectProyAcc').val() == '' || respuenta == false) {

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
				
			
// /////////////////////////////////////////////////////////////////////////////////// Funciones focus ////////////////////////////////////////////////////////
				
				
				
				   
				  $('#compAsociado').focusout(function() {
					   
					   if(pctaAsociadoval){
						   
						   $("#compAsociado").val('');
						   
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
				

				 $("#ncompromiso").focusout(function() {
					  
					  if( ($("#ncompromiso").val() == 'comp-')  ||
						  ($("#ncompromiso").val().length < 5) || 
						  ($("#ncompromiso").val().substring(0,5) != 'comp-')){
						  
						  $("#ncompromiso").val(''); 

					  }
					  
				  });
				 
				 
				 $('#PartidaBusqueda').focusout(function() {

						if($('#PartidaBusqueda').val().length < 13){
							
							$('#PartidaBusqueda').val('');
							
						
						}
						});
// /////////////////////////////////////////////////////////////////////////////////
// Funciones ajax////////////////////////////////////////////////////////
			
			
			
    
     
     
     
      $('#asunto').autocomplete({
			autoFocus: false,
			delay: 100,
			 source: function(request, response){
					$.ajax({
						url: "../../acciones/comp/comp.php",
						dataType: "json",
						data: {
							accion: "SearchCompromisoAsunto",
							tipoRespuesta: 'json',
							key: request.term
							
						},
						success: function(json){

                        	$('#asuntoVal').val('');
							var index = 0;
							var items = new Array();

							
							
							$.each(json,function(id,params){

								var value = params.nombre;
								var id = params.id;
								
								items[index++] = {
										id: params.id,
										value: value
								};

							});
							
						
							response(items);

							
							  if(json == false){
	                            	
	                            	$('#asunto').val('');

	                            	
	                                }
						}
						
					});
					
					
				},
				minLength: 1,
				select: function(event, ui)
				{
					
				$('#asunto').val(ui.item.value);
				$('#asuntoVal').val(ui.item.id);
				
            	if((!modificarComp) || (ui.item.id != "001" && ui.item.id != "002" && ui.item.id != "023") ){
                   		
						
 		
					$("#estatuscomp" ).hide();
					$("#estatusfecha" ).hide();
					
					$("#fechaReporte" ).val('');
					$("#estatus" ).val('');
					
					// no actualiza al cambiar el asunto
					
				}else{
					
					$("#estatuscomp" ).show(100);
					$("#estatusfecha" ).show(100);
					
					$("#fechaReporte" ).val('');
					$("#estatus" ).val('');

				
				}
				
				
				return true;
					
				}

				
			}); 
      
      
     
		$('#compAsociado').autocomplete({
			autoFocus: false,
			delay: 100,
			 source: function(request, response){
					$.ajax({
						url: "../../acciones/comp/comp.php",
						dataType: "json",
						data: {
							accion: "SearchPcuentaAsociado",
							tipoRespuesta: 'json',
							key: request.term,
							Dependencia: $("#unidadDependencia").val()
						},
						success: function(json){
							
						      pctaAsociadoval = true;
						      
						     $('#tablaCategoriaPartida').hide(100);
							 $("#filsed1").show(100);
							 $("#fieldset2").show(100);
							 $('#trselectProyAcc').show(100);
							 $("#cuerpoCategoriaPartida").hide(100);
							 $("#cuerpoCategoriaPartidaEliminada").hide(100);
					  		 $('#tablaCategoriaPartidaAgregar').children("tr.trsinregistro").remove();
					  		 $('#tablaCategoriaPartidaAgregar').children("tr.caso").remove();

							var index = 0;
							var items = new Array();

							
							
							$.each(json,function(id,params){

								var value = params;
								var id = params;
								
								items[index++] = {
										id: id,
										value: value
								};

							});
							
						
							  
							  response(items);
						}
						
					});
					
					
				},
				minLength: 1,
				select: function(event, ui)
				{

				pctaAsociadoval = false;
				
				$('#compAsociado').val(ui.item.value);
				
				SearchPctaAsociado(ui.item.value);

				 $("#filsed1").show(100);
				 $("#fieldset2").show(100);
				 
				$('#tablaCategoriaPartidaAgregar').children("tr.trsinregistro").remove();
				
				tbody = $("#tablaCategoriaPartidaAgregar")[0];
				
				
				 var fila = document.createElement("tr");
					 fila.className='normalNegro trsinregistro';

					var columna1 = document.createElement("td");
					columna1.setAttribute("valign","top");
					columna1.setAttribute("colspan","8");
					columna1.appendChild(document.createTextNode('No se encontraron registros')); 
				 
					fila.appendChild(columna1);
					tbody.appendChild(fila);

						$("#cuerpoCategoriaPartidaEliminada").show(100);
						
				return true;
					
				}

				
			}); 
     
     
     
		$('#ProveedorSugerido').autocomplete({
			autoFocus: false,
			delay: 100,
			 source: function(request, response){
					$.ajax({
						url: "../../acciones/pcta/pcta.php",
						dataType: "json",
						data: {
							accion: "SearchProveedorSugerido",
							tipoRespuesta: 'json',
							key: request.term
						},
						success: function(json){

							var index = 0;
							var items = new Array();

                            if(json == ""){                       	
                            	$('#ProveedorSugeridoValor').val('');	
                            	
                                }
							
						
							$.each(json,function(id,params){
								
								var value = params.id + ' : '+ params.nombre;
								items[index++] = {
										id: params.id,
										value: value
								};

							});
							
							response(items);
							
						}
					});
				},
				minLength: 1,
				select: function(event, ui)
				{
					$('#ProveedorSugerido').val(ui.item.value);
					$('#ProveedorSugeridoValor').val(ui.item.id);	

					return true;
					
				}

				
			}); 
		
     
     
		$('#tipoActividad').autocomplete({
			autoFocus: false,
			delay: 100,
			 source: function(request, response){
					$.ajax({
						url: "../../acciones/comp/comp.php",
						dataType: "json",
						data: {
							accion: "SearchCompromisoTipoActividad",
							tipoRespuesta: 'json',
							key: request.term
						},
						success: function(json){
							
                        	$('#tipoActividadVal').val('');

							var index = 0;
							var items = new Array();

							
							
							$.each(json,function(id,params){

								var value = params.nombre;
								var id = params.id;
								
								items[index++] = {
										id: params.id,
										value: value
								};

							});
							
						
							response(items);

							
							  if(json == false){
	                            	
	                            	$('#tipoActividad').val('');
	                            	
	                                }
						}
						
					});
					
					
				},
				minLength: 1,
				select: function(event, ui)
				{
				$('#tipoActividad').val(ui.item.value);
				$('#tipoActividadVal').val(ui.item.id);
				
				return true;
					
				}

				
			}); 
		
		
		$('#tipoEvento').autocomplete({
			autoFocus: false,
			delay: 100,
			 source: function(request, response){
					$.ajax({
						url: "../../acciones/comp/comp.php",
						dataType: "json",
						data: {
							accion: "SearchCompromisoTipoEvento",
							tipoRespuesta: 'json',
							key: request.term
						},
						success: function(json){
							
                        	$('#tipoEventoVal').val('');
	
							var index = 0;
							var items = new Array();

							
							
							$.each(json,function(id,params){

								var value = params.nombre;
								var id = params.id;
								
								items[index++] = {
										id: params.id,
										value: value
								};

							});
							
						
							response(items);

							
							  if(json == false){
	                            	
	                            	$('#tipoEvento').val('');

	                            	
	                                }
						}
						
					});
					
					
				},
				minLength: 1,
				select: function(event, ui)
				{
				$('#tipoEvento').val(ui.item.value);
				$('#tipoEventoVal').val(ui.item.id);
				
				return true;
					
				}

				
			}); 
		
		
		
		
		
		
		$('#infocentro').autocomplete({
			autoFocus: false,
			delay: 100,
			 source: function(request, response){
					$.ajax({
						url: "../../acciones/comp/comp.php",
						dataType: "json",
						data: {
							accion: "SearchCompromisoInfocentros",
							tipoRespuesta: 'json',
							key: request.term
						},
						success: function(json){
							
                        	$('#infocentroVal').val('');
							var index = 0;
							var items = new Array();

							
							
							$.each(json.listainfocentro,function(id,params){

								var value = params.nombre;
								var id = params.id;
								
								items[index++] = {
										id: params.id,
										value: value
								};

							});
							
						
							response(items);

							
							  if(json == false){
	                            	
	                            	$('#infocentro').val('');

	                            	
	                                }
						}
						
					});
					
					
				},
				minLength: 2,
				select: function(event, ui)
				{
				$('#infocentro').val(ui.item.value);
				$('#infocentroVal').val(ui.item.id);
				
				return true;
					
				}

				
			}); 
		
		


		
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
					$('#c2').html(ui.item.centro);
					$('#c3').html(ui.item.proy_titulo);
					$('#c4').html(ui.item.nombre);
					$('#tablacategoria').show('fade',300);
					$('#divPartida').show('fade',300);

					 categoriaTipo = ui.item.tipoVal;
                     id_especifica= ui.item.id_especifica;
					 id_proy_accion = ui.item.id_proy_accion;

					 
					 var entro = false;
					 var respuenta = false;
					 var proyacc = false;
					 
					 	 if ($('#Categoria').val() != '') {
					 		 
						
						  $('input[name=\'pcta[monto][]\']').each(function (index) {

						    	 obj = $(this).parent("td").parent("tr.trCaso");
						    	 
						    	 codProyAccEsp =   obj.find('input[name=\'pcta[codProyAccEsp][]\']');
						    	 
						    	 if(ui.item.id_especifica != codProyAccEsp.val() && entro == false){
						    		 respuenta = CentroGestoryCostoDifenente('ProyAccEsp'); 
						    		 entro = true; 
						    		 
						    	 }

						    	
	
				
					 	 });	
						  
						  
						  if (respuenta == false && entro == true){
							 
							    
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

								
					     
						 }

						
						 
			          }
						    	 
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
		
		
		function SearchPctaAsociado(id){
			
			 $("#filsed1").hide(200);
			 $("#fieldset2").hide(200);
			 $('#trselectProyAcc').hide(100);
			 $('#trcategoriaesp').hide(100);
		     $('.patidaseleccionada').hide(100);
			 $('#divPartida').hide(200);
			 $('#tablacategoria').hide(100);
			 $("#cuerpoCategoriaPartida").hide(100);
			 $("#cuerpoCategoriaPartidaEliminada").hide(100);
			 $('#c1').html('');
		     $('#c2').html('');
			 $('#c3').html('');
			 $('#c4').html('');
			 $('#Partida').val('');
			 $('#selectProyAcc').val('');
			 $('#Categoria').val('');
			 $('tr.trCaso').remove();

			$.ajax({
				
				async:false,
				url: "../../acciones/pcta/pcta.php",
				dataType: "json",
				data: {
					accion: "SearchImputasPcuentas",
					tipoRespuesta: 'json',
					key: id

				},
					success: function(json){	

					var DisponPctaCompr =  GetDisponibilidadCompromiso(id);
					
				
					        var count = 0;
							$.each(json,function(id,val){
								
							 if(val.puntoCuenta.puntoCuentaImputa){
								  
					      	$('#tablaCategoriaPartida').children("tr.trCaso").remove();
					      	
					    
							  $.each(val.puntoCuenta.puntoCuentaImputa ,function(id2,val2){
								  
							   var tbody = $('#tablaCategoriaPartida')[0];
							   
		
	                           if(val2.tipoImpu > 0){
	                        	   
								var dato1= 'Proyecto';
								var dato2= val2.proyecto.nombre;
								var dato4= val2.proyectoEspecifica.nombre;
								var dato3= val2.proyectoEspecifica.centroGestor+'/'+val2.proyectoEspecifica.centroCosto;
								
								var proyAcc = val2.proyecto.id;
								var proyAccEsp =val2.proyectoEspecifica.id;
								
	                           }else{

	                        	var dato1= 'Ac.Centralizada';

	                                 
	        						var dato2= val2.accionCentralizada.nombre;
	    							var dato4= val2.CentralizadaEspecifica.nombre;
	    							var dato3= val2.CentralizadaEspecifica.centroGestor+'/'+val2.CentralizadaEspecifica.centroCosto;
	    							
	    							var proyAcc = val2.accionCentralizada.id;
	    							var proyAccEsp =val2.CentralizadaEspecifica.id;
	                           }

	                           
	                           

								var dato5= val2.partida.id;
								var dato6= val2.partida.nombre;
								
								var dato9 = 0;
								
								 $.each(DisponPctaCompr ,function(ids,vals){
	
									 if(vals.partida == dato5){
			
										 dato9 = vals.disponibilidad;
										
									 }
		
								 });
								 
							    dato11 =  number_format(dato9,2,',','.');
							   
								if(parseInt(dato9) > 0){
	               
								
						 		var fila = document.createElement("tr");
						 		fila.className='normalNegro trCaso';

						 		var columna1 = document.createElement("td");
						 		columna1.setAttribute("valign","top");
						 		columna1.appendChild(document.createTextNode(dato1));
						 		var input1 = document.createElement("input");
						 		input1.setAttribute("type","hidden");
						 		input1.setAttribute("name","pcta[tipo][]");
						 		input1.value=val2.tipoImpu;
						 		columna1.appendChild(input1);
						 		
						 		
						 		var columna2 = document.createElement("td");
						 		columna2.setAttribute("valign","top");
						 		columna2.appendChild(document.createTextNode(dato2));
						 		var input2 = document.createElement("input");
						 		input2.setAttribute("type","hidden");
						 		input2.setAttribute("name","pcta[codProyAcc][]");
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
						 		input4.setAttribute("name","pcta[codProyAccEsp][]");
						 		input4.value=proyAccEsp;
						 		columna4.appendChild(input4);

						 		var columna5 = document.createElement("td");
						 		columna5.setAttribute("valign","top");
						 		columna5.setAttribute("class","tdPartida");
						 		columna5.appendChild(document.createTextNode(dato5));
						 		var input5 = document.createElement("input");
						 		input5.setAttribute("type","hidden");
						 		input5.setAttribute("name","pcta[codPartida][]");
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
						 		input7.setAttribute("autocomplete","off");
						 		input7.setAttribute("type","text");
						 		input7.setAttribute("name","pcta[monto][]");
						 		input7.setAttribute("style","height:22px;width:70;margin:0;border:2px #D8D8D8 dotted solid; font-size:10px");
						 		input7.value= '';
						 		columna7.appendChild(input7);

						 		var columna9 = document.createElement("td");
						 		columna9.setAttribute("class","tdDisponibilidad");
								columna9.setAttribute("valign","top");
								columna9.appendChild(document.createTextNode(dato11));
								
								// OPCION DE ELIMINAR
						 		var columna8 = document.createElement("td");
						 		columna8.setAttribute("valign","top");
						 		columna8.className = 'link';
						 		deleteLink = document.createElement("a");
						 		deleteLink.setAttribute("href","javascript:void(0);");
						 		linkText = document.createTextNode("Quitar");
						 		deleteLink.appendChild(linkText);
						 		columna8.appendChild(deleteLink);


						 		
						 		$(deleteLink).bind('click', function(){
						 			eliminarCategoriaPartida(this);
						 		});

						 		
						 		
						 		$(input7).keyup(function(){
						 			
						 			formato_num($(this));

						 		});
	                            
						 		fila.appendChild(columna1);				
						 		fila.appendChild(columna2);
						 		fila.appendChild(columna3);				
						 		fila.appendChild(columna4);
						 		fila.appendChild(columna5);				
						 		fila.appendChild(columna6);
						 		fila.appendChild(columna9);
						 		fila.appendChild(columna7);	
						 		fila.appendChild(columna8);
						 		
						 		tbody.appendChild(fila);
						 		
						 		if($("#tablaCategoriaPartida > tr").length < 2){
									 $("#filsed1").hide(100);
									 $("#fieldset2").hide(100);

									}
						 		
						 		if($("#tablaCategoriaPartida > tr").length < 3){
						 			$('#tablaCategoriaPartida').show('fade',300);
						 			$("#cuerpoCategoriaPartida").show('fade',300);
						 			
						 		}

							  }
						 		
							  });
							  

						  }
					  
							  
							  

					    });
					}

			  });

		}
		
		

// /////////////////////////////////////////////////////////////////////////////////
// Funciones////////////////////////////////////////////////////////
		 
		 
		 
			
		   function AgregarCategoriaPartida(){


			   valido = true;
			   
			      $('input[name=\'pcta[codPartida][]\']').each(function (index) {

		            if(this.value == partida){

		          valido = false;
		          
		          alert("Esta partida ya fue seleccionada");
		            }
				    
			 		 
			 	 });
			      
			      
			     
				 
		     if(!valido === false ){   
		     
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
				input1.setAttribute("name","pcta[tipo][]");
				input1.value=categoriaTipo;
				columna1.appendChild(input1);
				
				var columna2 = document.createElement("td");
				columna2.setAttribute("valign","top");
				columna2.appendChild(document.createTextNode(dato2));
				var input2 = document.createElement("input");
				input2.setAttribute("type","hidden");
				input2.setAttribute("name","pcta[codProyAcc][]");
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
				input4.setAttribute("name","pcta[codProyAccEsp][]");
				input4.value=id_especifica;
				columna4.appendChild(input4);

				var columna5 = document.createElement("td");
				columna5.setAttribute("valign","top");
				columna5.setAttribute("class","tdPartida");
				columna5.appendChild(document.createTextNode(dato5));
				var input5 = document.createElement("input");
				input5.setAttribute("type","hidden");
				input5.setAttribute("name","pcta[codPartida][]");
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
		 		input7.setAttribute("autocomplete","off");
		 		input7.setAttribute("type","text");
		 		input7.setAttribute("name","pcta[monto][]");
		 		input7.setAttribute("style","height:22px;width:70;margin:0;border:2px #D8D8D8 dotted solid; font-size:10px" );
		 		input7.value= '';
		 		columna7.appendChild(input7);

		 		$(input7).keyup(function(){
		 			
		 			formato_num($(this));

		 		});
		 		
				var columna9 = document.createElement("td");
				columna9.setAttribute("class","tdDisponibilidad");
				columna9.appendChild(document.createTextNode(dato11));
				

				
				
				// OPCION DE ELIMINAR
				var columna8 = document.createElement("td");
				columna8.setAttribute("valign","top");
				columna8.className = 'link';
				deleteLink = document.createElement("a");
				deleteLink.setAttribute("href","javascript:void(0);");
				linkText = document.createTextNode("Eliminar");
				deleteLink.appendChild(linkText);
				columna8.appendChild(deleteLink);


				
				$(deleteLink).bind('click', function(){
					eliminarCategoriaPartida(this);
				});


				fila.appendChild(columna1);				
				fila.appendChild(columna2);
				fila.appendChild(columna3);				
				fila.appendChild(columna4);
				fila.appendChild(columna5);				
				fila.appendChild(columna6);
				fila.appendChild(columna9);
				fila.appendChild(columna7);	
				fila.appendChild(columna8);
				
				tbody.appendChild(fila);

		          

				if($("#tablaCategoriaPartida > tr").length < 3){
					$('#tablaCategoriaPartida').show('fade',300);
					$("#cuerpoCategoriaPartida").show('fade',300);
					
				}

		     } 
				   
		   }

		
		});

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		function mostrarOcultarEditor(){

	      	  if($('#spanDescripval').val() == 0){
		                
		                $('#spanDescrip').children("b").html('Ocultar editor');
		                $('#imgDescrip2').show();
		                $('#imgDescrip').hide();
		             	$('#compromiso_descripcion2').show('fade',600); 
						$('#spanDescripval').val('1');

						
								
		                }else{

		                	 $('#spanDescrip').children("b").html('Mostrar editor');
		                	 $('#compromiso_descripcion2').hide(450); 
		                	 $('#imgDescrip').show();
				                $('#imgDescrip2').hide();
		                	 $('#spanDescripval').val('');
		                	 
		                    }               

	            };
	            
	            
	            

	            
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
	    
	     	   
	     	   

	     	   

	     	     function  MontoPartida(obj){
	     	    	 $Obj2 = $(obj).parent("td").parent("tr.trCaso").children('.tdPartida').children('input');
	     	    	 $Obj3 = $(obj).parent("td").parent("tr.trCaso").children('.tdDisponibilidad');

	     	    	 
	     	    	var  monto =  QuitarCaracter($(obj).val(),".");
	     	       
	     	    	 if(!validarNumerosfloat(monto.toString())){
	     	    		 
	     	         alert("Debe indicar un monto disponible  para la partida("+$Obj2.val()+"). Nota: El separador de decimal debe ser ','");
	     	         $(obj).val('');
	     	         $(obj).focus();
	     	         
	     	         var montoTotal1 = 0;
	     	         var dato1 = 0 ;
	     	         
	     	         $('input[name=\'pcta[monto][]\']').each(function (index) {

	     	            if((this.value != '')){
	     	         	   
	     	         	  
	     	            	 dato1  = QuitarCaracter(this.value,".");
	     	            	
	     	                montoTotal1 = (montoTotal1+dato1);	
	     	               
	     	            }
	     	             
	     	 		    
	     	 	 		 
	     	 	 	 });

	     	     
	     	        var montoTotal2 = number_format(montoTotal1,2,',','.');
	     			    ver_monto_letra(montoTotal1, 'monto_letras','');

	     			   $('#montoTotal').html(montoTotal2);
	     			   
	     			 return;
	     	 		
	     	         }else{
	     	        	
	     	        	 var dispon = $($Obj3).html();
	     	        	 var num = parseInt(dispon.split(".").length -1);
	     	        	 var i=0;
	     	        	 
	     	        	 while(i<= num){
	     	        	 
	     	        	    dispon = dispon.replace('.','');
	     	        	 
	     	        	    i++;
	     	              }
	     	        	 
	     	        	 dispon = dispon.replace(',','.');

	     	        	if(parseFloat(dispon) < monto){
	     	        		
	     	        		alert("Debe indicar un monto disponible  para la partida("+$Obj2.val()+"). Dicho monto debe tener separador de decimal ','");
	     	        		
	     	                $(obj).val('');
	     	                $(obj).focus();
	     	                
	     	                var montoTotal1 = 0;
	     	                var dato1 = 0 ;
	     	                
	     	                $('input[name=\'pcta[monto][]\']').each(function (index) {

	     	                   if((this.value != '')){
	     	                	   
	     	                	  
	     	                 	 dato1  = QuitarCaracter(this.value,".");
	     	                   	
	     	                       montoTotal1 = (montoTotal1+dato1);	
	     	                   
	     	                   }
	     	                    
	     	        		    
	     	        	 		 
	     	        	 	 });

	     	            
	     	               var  montoTotal2 = number_format(montoTotal1,2,',','.');
	     	        		   
	     	       		   ver_monto_letra(montoTotal1, 'monto_letras','');

	     	       		   $('#montoTotal').html(montoTotal2);
	     	       		   
	     	       		 return;
	     	        		
	     	        	}else{

	     	        		 var montoTotal1 = 0;
	     	                 var dato1 = 0 ;
	     	                 $('input[name=\'pcta[monto][]\']').each(function (index) {

	     	                    if(this.value != ''){
	     	                 	   
	     	                 	  
	     	                  	 dato1  = QuitarCaracter(this.value,".");
	     	                    	
	     	                    montoTotal1 = (montoTotal1+dato1);	
	     	                    
	     	                 
	     	                    }
	     	                     
	     	         		    
	     	         	 		 
	     	         	 	 });

	     	             
	     	                var  montoTotal2 = number_format(montoTotal1,2,',','.');
	     	         		   
	     	        		   ver_monto_letra(montoTotal1, 'monto_letras','');

	     	        		   $('#montoTotal').html(montoTotal2);

	     	        	}
	     	        	
	     	         }

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
	     	   
	     	   
	     	  function CentroGestoryCostoDifenente(params){
	         	 
	     	    	
	         	 
	         	 if(params == 'ProyAcc'){
	         		 params= 'el proyecto o acci\u00F3n centralizada'; 
	         	 }else{
	         		 
	         		  params= 'la acci\u00F3n espec\u00EDfica '; 
	         		 
	         	 }
	         	 
	

	     				 if(confirm("¿Est\u00E1 seguro que desea cambiar "+params+"? \n Al aceptar se eliminar\u00E1n todas las partidas registradas.")){

	     					$('#tablaCategoriaPartida').children("tr.trCaso").remove();
	     					$('#Partida').val('');

	     					$('#cuerpoCategoriaPartida').hide(100); 
	     					$('#Partidadenominacion').parent('span').hide(100); 
	     					ver_monto_letra(0, 'monto_letras','');
	     					$('#montoTotal').html('0'); 
	     					
	     					 if(params == 'ProyAcc'){

	     				     $('#tablaCategoriaPartida').hide(100); 	
	     				     
	     					 $('#divPartida').hide(200); 
	     					 
	     					 }
	     					 
	     					
	     					 
	     					 return true;
	     					 
	     				 }else{
	                            
	     					 
	     					 
	     					 return false;
	     					 
	     					 }

	     	 }	  
	     	  
	     	  
	     	  
	     	  
	     	  
	     	  
	     	  function GetDisponibilidadCompromiso(pcta){

	     		  if(pcta != ''){

	    		      $.ajax({
	    					
	    				    async:	false,
	    				    data: {
	    						accion: "GetDisponibilidadCompromisoPartida",
	    						pcta: pcta,
	    						tipoRespuesta: "json"
	    					},
	    					url: "../../acciones/comp/comp.php",
	    					type:	"post",
	    					dataType:"json",
	    					success: function(json){
	    						
	    						
	    						 
	    						if(json){

									var count = 0;
	    							$.each(json,function(index,valor){
	    								
	    							if(valor.montoApartado != undefined){
	    								
	    								if(valor.montoComprometido != undefined ){
	    									
	    									disponibilidad = (valor.montoApartado - valor.montoComprometido);
	    									
	    								}else{
	    									
	    									disponibilidad = valor.montoApartado;
	    									
	    								}
	    								
	    							}else{
	    								
	    								disponibilidad = 0;
	
	    							}
	    							
	    							params[count++] = {
	    									
	    									     disponibilidad: disponibilidad,
	    									     partida : index
										}; 

	    							});
	    							
	    							 
	    							
	    							}
	
	    				        }
	    			        });
	    		     
	    		      return params;
	    		      
	    		      
	     		  }
	     		 return 0;
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
				  alert("Debe seleccionar la fecha del compromiso.");
				  $('#fecha').focus();
				  return;
			    }
			    
			    
			   
			    if($('#unidadDependencia').val() == "")
				{
					alert("Debe seleccionar la dependencia");
					 $('#unidadDependencia').focus();
					return;
				}
			    
			  

				 if((!validarcaracterespecial($("#ProveedorSugerido").val()) && $("#ProveedorSugeridoValor").val() =='') || (trimDato($("#ProveedorSugerido").val()) == false)){
					 alert("Debe indicar el proveedor sugerido y \u00e9ste s\u00f3lo puede tener caracteres alfanum\u00E9ricos"); 
					 
					 $("#ProveedorSugerido").val('');
					 $("#ProveedorSugeridoValor").val('');
					 
					 $('#ProveedorSugerido').focus(); 
					 return; 
					 
				 }

	  			
			     if($('#asuntoVal').val() == "" || $('#asunto').val() == "")
					{
				    	alert("Debe seleccionar el asunto");
				    	 $('#asunto').val('');
						 $('#asunto').focus();
						 
						return;
					}
			      
			  /*   if($('#tipoActividadVal').val() == "" || $('#tipoActividad').val() == "")
					{
				    	alert("Debe seleccionar el tipo de actividad");
				    	 $('#tipoActividad').val('');
						 $('#tipoActividad').focus();
						 
						return;
					} */ // campo comentado inhabilitado por presupuesto 01/04/13  
			     
			/*     if($('#tipoEventoVal').val() == "" || $('#tipoEvento').val() == "")
					{
				    	alert("Debe seleccionar el tipo de evento");
				    	 $('#tipoEvento').val('');
						 $('#tipoEvento').focus();
						 
						return;
					} */ // campo comentado inhabilitado por presupuesto 01/04/13  
	  			
	  			 
	  			
	  			 if( (!validarcaracterespecial($("#CodigoDocumento").val())))
					{
						alert("Debe indicar el c\u00f3digo del documento y \u00e9ste s\u00f3lo puede tener caracteres alfanum\u00E9ricos");
						 $('#CodigoDocumento').focus();
						return;
					}

	  			 descripcionlegth = $('#compromiso_descripcion').elrte('val').length;

					if(descripcionlegth < 5 )
					{  	
						alert("Debe especificar la descripci\u00f3n  y \u00E9sta s\u00f3lo puede tener caracteres alfanum\u00E9ricos");
						$('#spanDescrip').children("b").html('Ocultar editor');
		                $('#imgDescrip2').show();
		                $('#imgDescrip').hide();
		             	$('#compromiso_descripcion2').show('fade',600); 
						$('#spanDescripval').val('1');	
						return;
					}
					
					
					
				/*	  	
					
	  		    if($('#localidad').val() == "")
				{
					alert("Debe seleccionar la localidad");
					 $('#localidad').focus();
					return;
				}
	  		    
	  		    */
					
			      if($('#controlinterno').val() == "")
					{
				    	alert("Debe seleccionar el control interno");
						 $('#controlinterno').focus();
						return;
					} 
				
			  /*   if($('#infocentroVal').val() == "" || $('#infocentro').val() == "")
					{
				    	alert("Debe seleccionar el infocentro");
				    	 $('#infocentro').val('');
						 $('#infocentro').focus();
						 
						return;
					} */ // campo comentado inhabilitado por presupuesto 01/04/13  
			     
			       
	  			
	  		/*	 if((!validarNumerosInt($("#numeroParticipantes").val())))
					{
						alert("Debe indicar el n\u00famero de  y participantes \u00e9ste s\u00f3lo puede tener n\u00famero enteros");
						 $('#numeroParticipantes').focus();
						return;
					} */ // campo comentado inhabilitado por presupuesto 01/04/13  

	  			
	  			if( (!validarcaracterespecial2($("#observaciones").val())))
				{
					alert("Debe indicar las observaciones y \u00e9stas s\u00f3lo pueden tener caracteres alfanum\u00E9ricos");
					 $('#observaciones').focus();
					return;
				}
				
				
			
	  			
				   
				 

	  			if($("#tablaCategoriaPartida > tr").length < 2){

					 alert('Debe indicar la categor\u00eda program\u00e1tica con sus partidas asociadas');
					 
				 }else{

					 var salir = false;
					 
					 if($("#compAsociado").val() != false ){
					 
					  var DisponPctaCompr =  GetDisponibilidadCompromiso($("#compAsociado").val());
					  
					 }

				     $('input[name=\'pcta[monto][]\']').each(function (index) {
				    	 
				    	var obj = $(this).parent("td").parent("tr.trCaso");
				    	var  partida = obj.find('td.tdPartida input');
				    	var  dispon = obj.find('td.tdDisponibilidad');
				    	var  tipo =   obj.find('input[name=\'pcta[tipo][]\']');
				    	var  proyacc =   obj.find('input[name=\'pcta[codProyAcc][]\']');
				    	var  proyAccEsp = obj.find('input[name=\'pcta[codProyAccEsp][]\']');
				    	
				    	
				    	

				            if(($(this).val() == '' && salir == false)){

						    alert("El monto de la  partida("+partida.val()+") debe ser un n\u00famero real con separador decimal '.'");
	
				            $(this).focus(); 
							 salir = true;
				            }
				            
				            if(salir == false){
				          /*  
				            if(DisponPctaCompr){

							 $.each(DisponPctaCompr ,function(ids,vals){


								 if(vals.partida == partida.val()){

								    disponibilidad = vals.disponibilidad;
	
								 }
								 
								
	
							 });    
				            
				            }else{
				            	
				              disponibilidad = GetDisponibilidad(tipo.val(),proyacc.val(),proyAccEsp.val(),partida.val());

				            
				              
				            }
				            
				            
				            */
				            	
				            	monto = dispon.html();
				            	
			      				var num = parseInt(monto.split(".").length -1);
			      				
			      	        	 var i= 0;
			      	        	 
			      	        	 while(i<= num){
			      	        		 
			      	        		monto = monto.replace('.','');
			      	        	    i++;
			      	        	    
			      	              }
			      	        	  
			      	        	monto = parseFloat(monto.replace(',','.'));
			  
				            	 
				     if(monto <  QuitarCaracter(this.value,".")){
				    	 
				    	 alert("Debe indicar un monto disponible  para la partida("+partida.val()+"). Dicho monto debe tener separador de decimal ','");
				         $(this).focus();
						 salir = true;
	 
				     }
				     
				     }
		
			 	 	 });	
				     
				     
				    
				     if(salir)
				     {
				    	 return;
				    	 
				     }else{
				    	 
				    	 if(parseFloat($('#montoTotal').html()) < 1){
				    		 
				    		 alert('El monto total del compromiso no puede ser cero');
				    		 
				    		 return;}
				     }
				     
				 	var motTotal =  $('#montoTotal').html();	
      				
      				var num = parseInt(motTotal.split(".").length -1);
      	        	 var i=0;
      	        	 
      	        	 while(i<= num){
      	        		 
      	        		motTotal = motTotal.replace('.','');
      	        	    i++;
      	        	    
      	              }
      	        	 
                    motTotal = motTotal.replace(',','.');
                    motTotal = parseFloat(motTotal);
                    

					 var data = $('#modificarcompromiso').html();
		                
		      			if(data != '' && data != null){

		      				 if(confirm("¿Est\u00E1 seguro que desea modificar este Punto de Cuenta ?")){
		      					motTotal > 0 ?$('#montoTotalHidden').val(motTotal):$('#montoTotalHidden').val('0');
			  					  $('#pcuenta_descripcionVal').val($('#pcuenta_descripcion').elrte('val'));
			  	            	  
			  					// Modificar();
			  					
			  	      			}              
		      				
		                  
		      			}else{
		      				
		      				if(!modificarComp){
		      					
		      					var text = "¿Est\u00E1 seguro que desea generar este Compromiso ?";
		      					
		      				}else{
		      					
		      					var text = "¿Est\u00E1 seguro que desea modificar este Compromiso ?";
		      					
		      				}
		      				
		      		
					 
					  if(confirm(text)){
						  
						     motTotal > 0 ?$('#montoTotalHidden').val(motTotal):$('#montoTotalHidden').val('0');
							 $('#compromiso_descripcionVal').val($('#compromiso_descripcion').elrte('val'));
							 
		                    Enviar();
							
					  }
		            	
		            	
		      			}           

					 }
					

			}
	  		
	  		
	  		function Enviar(){
	  			
	  		  $('input[name=\'pcta[monto][]\']').each(function (index) {

	        	   if(this.value != ''){
    	            	
    	            	 dato1  = QuitarCaracter(this.value,".");
    	            	 
    	            	$(this).val(dato1);

    	            }
	  	 		
	  	 	 });
			
	  			
	  			
	  			
	  			
	  			if(!modificarComp){
	  				
	  				
	  				
	  			
				$('#formcompromiso').attr('action','../../acciones/comp/comp.php?accion=Registrar');
				
	  			}else{
	  				
	  				var comp = $('span[detalle=\'compromiso\']').html();
	  				
	  				$('#formcompromiso').attr('action',"../../acciones/comp/comp.php?accion=ModificarAccion");
	  			}

	  			$('#formcompromiso')[0].submit();

			}
	  		

	  		function SearchDetalleComp(id,opcion){
	  			
	  			$('td[detalle=\'estado\']').css('color','#005E20');
	  			
	  			$.ajax({
					url: "../../acciones/comp/comp.php",
	  				dataType: "json",
	  				data: {
	  					accion: "SearchCompromisoDetalle",
	  					tipoRespuesta: 'json',
	  					key: id

	  				},
	  					success: function(json){
	  						var reintegro = true;

	  						var menuAnularModificar = true;
	  						
	  						$('.opcionesCompRemove').remove();
	  						
	  	               
	  							$.each(json,function(id,val){
	
		  							
		  							if(valor = json.compromiso.motivoAnulacion){ 

		  								if(valor = valor.opcion == 1){ 
		  									
		  									reintegro = false;
		  									
		  									}
		  								
	 									
	 								  }
	
	  								  var myString = val.rifProveedorSugerido;

	  								  var proveedorSugerido = myString.split(':');

	  								  if(proveedorSugerido[1]){
	  									  
	  									  proveedorSugerido = proveedorSugerido[1] ;
	  									  
	  								  }else{
	  									  
	  									
	  									  proveedorSugerido = val.rifProveedorSugerido;
	  								  
	  								  }
	  								  
	  								   
	  								  var fI = val.fechaInicio != undefined  ? val.fechaInicio :'';
	  				
	  								  var fF = val.fechaFin != undefined ? val.fechaFin :'';


	  								 if(val.fechaInicio == '' && val.fechaInicio == ''){
	  									  
	  									 var fechaFinal = '';
	  										 
	  								 }else{
	  									 
	  									var  fechaFinal =  "Fecha inicio: "+ fI+"  <br/>  Fecha fin:  "+fF;
	  									 
	  									 
	  								 }
	  			
	  								 
	  							if(reintegro == false){
	  								$('#reintegro').remove();
	  								 
	  								$('span[detalle=\'compromiso\']').after("<div id='reintegro' style ='color:white;float:right;' >(Se ha reintegrado totalmente este compromiso) </div>"); 
	  	
	  							}
	  							  $('span[detalle=\'compromiso\']').html(val.id);
							      $('td[detalle=\'fecha\']').html(val.fecha);
	  							  $('td[detalle=\'usuarioDetalle\']').html(val.usuario.nombres+" "+val.usuario.apellidos);
	  						      var gerencia = val.gerencia != null ? val.gerencia.nombre :'';
	  							  $('td[detalle=\'UnidadDependencia\']').html(gerencia);
	  							
	  							  $('td[detalle=\'proveedorSugerido\']').html(proveedorSugerido);
	  							  $('td[detalle=\'asunto\']').html(val.asunto.nombre);
	  							  
	  							  $('td[detalle=\'estado\']').html(val.estatus.nombre);
	  
	  							  
	  							  if(val.estatus.id == 15){
	  								  
	  								  menuAnularModificar = false;
	  								  
	  								 motivo = "No especificado";
	  								  
	  								  if(val.motivoAnulacion){
	  									  
	  									 motivo = val.motivoAnulacion.observacion;
	  								  }
	  								  
	  								  
	  								$('td[detalle=\'estado\']').css('color','red');
	  								  
	  								$('td[detalle=\'motivoAnulacion\']').html(motivo);
	  								  
	  							  }else{
	  								  
	  								$('#trPctaMotivoAnulacion').hide();
	  								
	  								$('td[detalle=\'estado\']').html('Activo');
	  								  
	  							  }
	  							  
	  							
		  						$('td[detalle=\'estatus\']').html(val.compEstatus);
		  						
	  							$('td[detalle=\'codigoDocumento\']').html(val.documento);
	  							
	  							$('td[detalle=\'fechaReporte\']').html(val.fechaReporte);
	  							  
	  							
	  						 if(val.controlInterno){ $('td[detalle=\'controlInterno\']').html(val.controlInterno.nombre)};
	  						//	  $('td[detalle=\'tipoActividad\']').html(val.actividad.nombre);
	  						//	  $('td[detalle=\'duracionActividad\']').html(fechaFinal);
	  						//	  $('td[detalle=\'tipoEvento\']').html(val.evento.nombre);
	  							  $('span[detalle=\'descripcion\']').html(val.descripcion);

	  							  if( val.pcta.length > 8){
	  								  
	  								 $('td[detalle=\'pctaAsociado\']').html(val.pcta); 
	  								  
	  							  }else{
	  								  
	  								$('td[detalle=\'pctaAsociado\']').parent('tr').hide();  
	  								  
	  							  }
	  							 

	  							  var localidad = val.localidad != null ? val.localidad.nombre :'';
	  							  $('td[detalle=\'localidad\']').html(localidad);
	  						//	  var infocentro = val.infocentro != null ? val.infocentro.nombre :'';
	  						//	  $('td[detalle=\'infocentro\']').html(infocentro);
	  							  
	  						//	  $('td[detalle=\'nParticipantes\']').html(val.participante);
	  					    	  $('td[detalle=\'observacion\']').html(val.Observacion);
	  					    	  var monto=  number_format(val.montoSolicitado,2,',','.');
	  					       	  $('td[detalle=\'montoSolicitado\']').html(monto);
	  					       	  


	  							
	  							  if(val.compromisoImputas){
	
	  								
	  					      	$('#tablaCategoriaPartidaComp').children("tr.trCaso").remove();   

	  	    
	  							  $.each(val.compromisoImputas ,function(id2,val2){
	  								  
	  								

	  							  
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
	  								 
	  								 
	  								// alert(dato1+'\n'+dato2+'\n'+dato3+'\n'+dato4+'\n'+dato5+'\n'+dato6+'\n'+dato7);
	  						
	  								 
	  								var tbody = $('#tablaCategoriaPartidaComp')[0];
	  								
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
	  			


	  								fila.appendChild(columna1);				
	  								fila.appendChild(columna2);
	  								fila.appendChild(columna3);				
	  								fila.appendChild(columna4);
	  								fila.appendChild(columna5);				
	  								fila.appendChild(columna6);
	  								fila.appendChild(columna7);				
	  															
	  								tbody.appendChild(fila);
	  	
	  							  });


	  							  }

	  					       });
	  							
	  							

	  							
	  							if(opcion == 'true' &&  menuAnularModificar == true && reintegro == true){

	  			  					
					    				
						    			 var fila = $('#trOpcionesDetallesComp')[0];	
						    			 
											var columna = document.createElement("td");
											    columna.setAttribute("class","opcionespcta opcionesCompRemove");
											    var alink = document.createElement("a");
				                                alink.setAttribute("href","#");
				                                alink.setAttribute("onclick",'AccionComp("modificar")');
				                                alink.appendChild(document.createTextNode("Modificar"));
				                                
				                          	var columna2 = document.createElement("td");
										    columna2.setAttribute("class","opcionespcta opcionesCompRemove");
										    var alink2 = document.createElement("a");
			                                alink2.setAttribute("href","#");
			                                alink2.setAttribute("onclick",'AccionComp("Anular")');
			                                alink2.appendChild(document.createTextNode("Anular"));
			                                
			                              var columna3 = document.createElement("td");
									    columna3.setAttribute("class","opcionespcta opcionesCompRemove");
									    var alink3 = document.createElement("a");
		                                alink3.setAttribute("href","#");
		                                alink3.setAttribute("onclick",'AccionComp("Reintegro")');
		                                alink3.appendChild(document.createTextNode("Reintegro Total"));
		                                
		                                
		                   
				                             columna.appendChild(alink);
				                             columna2.appendChild(alink2);	
				                             columna3.appendChild(alink3);

				                             fila.appendChild(columna);		
				                             fila.appendChild(columna2);		
				                             fila.appendChild(columna3);

					    		  $('#OpcionesPdfComp').hover(function(){

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

	  			  });

	  		}
	  			  		
	  		
	  		
	  		function AccionComp(accion){

	  			if(accion == 'modificar'){

	      		  url = "../../acciones/comp/comp.php?accion=Modificar&comp="+$('span[detalle=\'compromiso\']').html()+"";
	       		  window.location = url; 
	                
	              }
	  			
	  			if(accion == 'Anular'){
	  				
	  				var comp = $('span[detalle=\'compromiso\']').html();
	  				
	  				 $.ajax({
	    					
	    				    async:	false,
	    				    data: {
	    						accion:"GetAnular",
                                comp: comp,
	    						tipoRespuesta: "json"
	    					},
	    					url: "../../acciones/comp/comp.php",
	    					type:	"post",
	    					dataType:"json",
	    					success: function(json){
	    						
	    						 if(json == 1){
		    							
	 	    							alert("El compromiso ("+comp+"), no puede ser anulado porque tiene solicitudes de pago asociadas");
	 	
	 	    						}else if(json == 2){
		    							
	 		    							alert("El compromiso ("+comp+"), no puede ser anulado porque tiene comprobantes manuales asociados");

	 		    						}else{
	 		    							
	 		    							Anular("span[detalle=\'compromiso\']");
	 		    							
	 		    						}
	    						
	    				        }
	    					
	    			        });

		              }
	  			
	  			if(accion == 'Reintegro'){
	  				
	  				
	  				Reintegro("span[detalle=\'compromiso\']");
	  				
	  			}
	  			
	  		}
	  		
	  		
	  		
	  		
	  		 function CategoriaPartidaAgregar(objA){
	  			 
	  	 	   if($("#tablaCategoriaPartida > tr").length < 3){

	  	 		$('#tablaCategoriaPartida').children("tr.trsinregistro").remove();
					
			  	}
	  			

	  			objTrs = $(objA).parents("tr.trCaso");
	  			objTrs.find('input').prop('disabled', false);
	  			
	  			
	  			objTrs.children("td.tdMonto").show();
	  			
	  			objTrs.find('a').html('Quitar');
				objTrs.find('a').unbind('click');
                objTrs.find('a').bind('click', function(){
                	
                	  eliminarCategoriaPartida(this);
                	  
			 		});
	  		
	  			tdMonto = objTrs.children("td.tdMonto")[0];
	  			
	  			var input = document.createElement("input");
		 		input.setAttribute("type","text");
		 		input.setAttribute("name","pcta[monto][]");
		 		input.setAttribute("autocomplete","off");
		 		input.setAttribute("style","height:22px;width:70;margin:0;border:2px #D8D8D8 dotted solid; font-size:10px");
		 		input.value= '';
		 		tdMonto.appendChild(input);
		 		
		 		$(input).keyup(function(){
		 			
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
			      		
					         $('input[name=\'pcta[monto][]\']').each(function (index) {

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
	  		
	  		
	  		
	  		$(function(){
	  			
	  			if(pctaAsociado != false || pctaAsociado != 0 || pctaAsociado != ''){
	  				  				$("#trCompAsociado").show(100);
	  				$("#compAsociado").val(pctaAsociado);
	  				$('#trselectProyAcc').hide(100)
	  				
	  				var DisponPctaCompr =  GetDisponibilidadCompromiso(pctaAsociado);
	  				
	  				
	  				
	  				
	  			}
	  			
	  		
					 
	  				dependencia = $("#unidadDependencia").val();
	  				
	  				
				
					
					
	  			 if(compImputa){

	  				 
                     	partidas = new Object();
				     	$('#tablaCategoriaPartida').children("tr.trCaso").remove(); 
				     	
						  $.each(compImputa,function(id,val){
  
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
									var dato7= val.monto ;
									
									 var dato7 =  number_format(val.monto,2,',','.');

									partidas[dato5] = dato5; 
									
									
									if((pctaAsociado != false && pctaAsociado != 0)){
									
								    var dato9 = 0;
								    if(DisponPctaCompr){
									 $.each(DisponPctaCompr ,function(ids,vals){

										 if(vals.partida == dato5){

											 dato9 = vals.disponibilidad;
											
										 }

									 });
									 
								    }
									 
									}else{
										 var dato9 = 0;
										
										
										 dato9 = GetDisponibilidad(val.tipoImpu,proyAcc,proyAccEsp,dato5);

									}
							        
							            dato10 = (parseFloat(dato9) + parseFloat(val.monto));
							            dato11 =  number_format(dato10,2,',','.');
							            
							          // alert('Disponibilidad: '+dato9+'
										// monto: '+parseFloat(val.monto)+'
										// total: '+dato10);

							        var tbody = $('#tablaCategoriaPartida')[0];
							 		var fila = document.createElement("tr");
							 		fila.className='normalNegro trCaso';

		  
							 		var columna1 = document.createElement("td");
							 		columna1.setAttribute("valign","top");
							 		columna1.appendChild(document.createTextNode(dato1));
							 		var input1 = document.createElement("input");
							 		input1.setAttribute("type","hidden");
							 		input1.setAttribute("name","pcta[tipo][]");
							 		input1.value=val.tipoImpu;
							 		columna1.appendChild(input1);
							 		
							 		
							 		var columna2 = document.createElement("td");
							 		columna2.setAttribute("valign","top");
							 		columna2.appendChild(document.createTextNode(dato2));
							 		var input2 = document.createElement("input");
							 		input2.setAttribute("type","hidden");
							 		input2.setAttribute("name","pcta[codProyAcc][]");
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
							 		input4.setAttribute("name","pcta[codProyAccEsp][]");
							 		input4.value=proyAccEsp;
							 		columna4.appendChild(input4);

							 		var columna5 = document.createElement("td");
							 		columna5.setAttribute("valign","top");
							 		columna5.setAttribute("class","tdPartida");
							 		columna5.appendChild(document.createTextNode(dato5));
							 		var input5 = document.createElement("input");
							 		input5.setAttribute("type","hidden");
							 		input5.setAttribute("name","pcta[codPartida][]");
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
							 		input7.setAttribute("name","pcta[monto][]");
							 		input7.setAttribute("autocomplete","off");
							 		input7.setAttribute("style","height:22px;width:70;margin:0;border:2px #D8D8D8 dotted solid; font-size:10px");
							 		input7.value= dato7;
							 		columna7.appendChild(input7);
							 		
							 	
							 		// OPCION DE ELIMINAR
							 		var columna8 = document.createElement("td");
							 		columna8.setAttribute("valign","top");
							 		columna8.setAttribute("align","center");
							 		columna8.className = 'link';
							 		deleteLink = document.createElement("a");
							 		deleteLink.setAttribute("href","javascript:void(0);");
							 		
							 		

							 		if(pctaAsociado == false || pctaAsociado == 0 || pctaAsociado == ''){
							 			
							 			linkText = document.createTextNode("Eliminar");
							 		
							 		}else{
							 			


							 			linkText = document.createTextNode("Quitar");
							 			
							 			
							 		}
							 		
							 		deleteLink.appendChild(linkText);
							 		columna8.appendChild(deleteLink);
							 		
							 		var columna9 = document.createElement("td");
							 		columna9.setAttribute("class","tdDisponibilidad");
									columna9.setAttribute("valign","top");
									columna9.appendChild(document.createTextNode(dato11));

									
									$(input7).keyup(function(){
							 			
							 			formato_num($(this));

							 		});
									
									$(deleteLink).bind('click', function(){
											
										eliminarCategoriaPartida(this);
							 		});


							 		fila.appendChild(columna1);				
							 		fila.appendChild(columna2);
							 		fila.appendChild(columna3);				
							 		fila.appendChild(columna4);
							 		fila.appendChild(columna5);				
							 		fila.appendChild(columna6);
							 		fila.appendChild(columna9);
							 		fila.appendChild(columna7);				
							 		fila.appendChild(columna8);
							 		
							 		
							 		tbody.appendChild(fila);
	  								
							 		if($("#tablaCategoriaPartida > tr").length < 3){
							 			$('#tablaCategoriaPartida').show('fade',300);
							 			$("#cuerpoCategoriaPartida").show('fade',300);
							 			
							 		}

							 		 compModMontoTotal = compModMontoTotal + parseFloat(dato7);
							 		
							 	
							 	
						  });
						  
						  ver_monto_letra(compModMontoTotal, 'monto_letras','');

						  var  montoTotal2 = number_format(compModMontoTotal,2,',','.');
						  
						  
						  $('#montoTotal').html(montoTotal2);


							if(!pctaAsociado == false || pctaAsociado == 0 || pctaAsociado == ''){
						  
							$.ajax({
								url: "../../acciones/pcta/pcta.php",
								dataType: "json",
								data: {
									accion: "SearchImputasPcuentas",
									tipoRespuesta: 'json',
									key: pctaAsociado

								},
									success: function(json){	

									var DisponPctaCompr =  GetDisponibilidadCompromiso(pctaAsociado);
									
								
									        var count = 0;
											$.each(json,function(id,val){
												
											 if(val.puntoCuenta.puntoCuentaImputa){
												 
											var temp = false;
												  
									      	$('#tablaCategoriaPartidaAgregar').children("tr.trCaso").remove();
									      	
									    
											  $.each(val.puntoCuenta.puntoCuentaImputa ,function(id2,val2){
												  
												  if(partidas[val2.partida.id] == undefined ){ 
												
													  temp = true;	  
													  
											   var tbody = $('#tablaCategoriaPartidaAgregar')[0];
											   
						
					                           if(val2.tipoImpu > 0){
					                        	   
												var dato1= 'Proyecto';
												var dato2= val2.proyecto.nombre;
												var dato4= val2.proyectoEspecifica.nombre;
												var dato3= val2.proyectoEspecifica.centroGestor+'/'+val2.proyectoEspecifica.centroCosto;
												
												var proyAcc = val2.proyecto.id;
												var proyAccEsp =val2.proyectoEspecifica.id;
												
					                           }else{

					                        	var dato1= 'Ac.Centralizada';

					        						var dato2= val2.accionCentralizada.nombre;
					    							var dato4= val2.CentralizadaEspecifica.nombre;
					    							var dato3= val2.CentralizadaEspecifica.centroGestor+'/'+val2.CentralizadaEspecifica.centroCosto;
					    							
					    							var proyAcc = val2.accionCentralizada.id;
					    							var proyAccEsp =val2.CentralizadaEspecifica.id;
					                           }

					                           
					                           

												var dato5= val2.partida.id;

												var dato6= val2.partida.nombre;
												
												var dato9 = 0;
												
												 $.each(DisponPctaCompr ,function(ids,vals){
					
													 if(vals.partida == dato5){
							
														 dato9 = vals.disponibilidad;
														
													 }
						
												 });
												 
											    dato11 =  number_format(dato9,2,',','.');

										 		var fila = document.createElement("tr");
										 		fila.className='normalNegro trCaso';

										 		var columna1 = document.createElement("td");
										 		columna1.setAttribute("valign","top");
										 		columna1.appendChild(document.createTextNode(dato1));
										 		var input1 = document.createElement("input");
										 		input1.setAttribute("type","hidden");
										 		input1.setAttribute("disabled","disabled");
										 		input1.setAttribute("name","pcta[tipo][]");
										 		input1.value=val2.tipoImpu;
										 		columna1.appendChild(input1);
										 		
										 		
										 		var columna2 = document.createElement("td");
										 		columna2.setAttribute("valign","top");
										 		columna2.appendChild(document.createTextNode(dato2));
										 		var input2 = document.createElement("input");
										 		input2.setAttribute("type","hidden");
										 		input2.setAttribute("name","pcta[codProyAcc][]");
										 		input2.setAttribute("disabled","disabled");
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
										 		input4.setAttribute("name","pcta[codProyAccEsp][]");
										 		input4.setAttribute("disabled","disabled");
										 		input4.value=proyAccEsp;
										 		columna4.appendChild(input4);

										 		var columna5 = document.createElement("td");
										 		columna5.setAttribute("valign","top");
										 		columna5.setAttribute("class","tdPartida");
										 		columna5.appendChild(document.createTextNode(dato5));
										 		var input5 = document.createElement("input");
										 		input5.setAttribute("type","hidden");
										 		input5.setAttribute("name","pcta[codPartida][]");
										 		input5.setAttribute("disabled","disabled");
										 		input5.value= dato5;
										 		columna5.appendChild(input5);

										 		var columna6 = document.createElement("td");
										 		columna6.setAttribute("valign","top");
										 		columna6.appendChild(document.createTextNode(dato6));
										 		

										 		var columna7 = document.createElement("td");
										 		columna7.setAttribute("valign","baseline");
										 		columna7.setAttribute("class","tdMonto");
										 		columna7.setAttribute("style","padding:0;");
										 	    $(columna7).hide();	
										 	    
										 		var columna9 = document.createElement("td");
										 		columna9.setAttribute("class","tdDisponibilidad");
												columna9.setAttribute("valign","top");
												columna9.appendChild(document.createTextNode(dato11));
												
												// OPCION DE Agregar
										 		var columna8 = document.createElement("td");
										 		columna8.setAttribute("valign","top");
										 		columna8.setAttribute('align','center');
										 		columna8.className = 'link';
										 		deleteLink = document.createElement("a");
										 		deleteLink.setAttribute("href","javascript:void(0);");
										 		linkText = document.createTextNode("Insertar");
										 		deleteLink.appendChild(linkText);
										 		columna8.appendChild(deleteLink);

										 		$(deleteLink).bind('click', function(){
										 			CategoriaPartidaAgregar(this);
										 		});


										 		fila.appendChild(columna1);				
										 		fila.appendChild(columna2);
										 		fila.appendChild(columna3);				
										 		fila.appendChild(columna4);
										 		fila.appendChild(columna5);				
										 		fila.appendChild(columna6);
										 		fila.appendChild(columna9);
										 		fila.appendChild(columna7);	
										 		fila.appendChild(columna8);
										 		
										 		tbody.appendChild(fila);
										 		
												  }
											  });
											  
										  }
											 
		
											 if(!temp){
												 
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
											 
											 $("#cuerpoCategoriaPartidaEliminada").show(100);
											
											 
											});
									}

							  });
						  
	  
	  			 }
						  
						  
						  
						  
						  
			      }
	  			 

	  			
	  			
	  			 	

	  		});
	  		
	  		
	  		function comparar_fechas(fecha_inicial,fecha_final) // Formato
																// dd/mm/yyyy
	  		{ 
	  			
	  		  var dia1 =fecha_inicial.substring(0,2);
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
	  		
	  		
	  		 
	        function  LlenarMemo(obj,lugar){
	        
	        if(memo=prompt("Especifique un motivo por el cual desea "+obj+" este "+lugar)){
	      	  
	      	  
	      	  return memo; 
	  		  
	  		    }else{

	  		       if (memo == null)
	  		       {
	  		    	   if(confirm("¿Est\u00E1 seguro que desea (Cancelar) la operaci\u00F3n ?")){    
	  		    		   
	  		    		  return false; 
	        			
	  		    	   }else{
	  		    		   
	  		    		return  LlenarMemo(obj,lugar);  
	  		    		   
	  		    	   }
	  		    	   
	  		       }else{

	  		    	   if(confirm("¿El motivo por el cual desea "+obj+" este "+lugar+" est\u00E1 vac\u00EDo. ¿Desea (Cancelar) la operaci\u00F3n.?")){    
	  		    		   
	  			    		  return false; 
	  	      			
	  			    	   }else{
	  			    		   
	  			    		return  LlenarMemo(obj,lugar);  
	  			    		   
	  			    	   } 
	  		          }

	  		      }
	        
	         
	            }
	        	  
	        
	        function  Anular(obj){

	            
	      	  if(confirm("¿Est\u00E1 seguro que desea anular este compromiso ("+$(obj).html()+")?")){
	          	  
	      		  
	      		 var lugar = 'compromiso';
	            	 var memo =  LlenarMemo('anular',lugar);
	            	 
	            	if(memo){
	            		 
	            	  url = "../../acciones/comp/comp.php?accion=Anular&memo="+memo+"&comp="+$(obj).html(); 
	                  window.location = url;
	            	  
	            	}else{
	  					 
	  				  return;
	  					 
	  			}

	    				 }else{
	    					 
	    					return;
	    					 
	    					}
	            
	            }
	        
	        
	        
	        function  Reintegro(obj){

	            
		      	  if(confirm("¿Est\u00E1 seguro que desea Reintegrar totalmente este compromiso ("+$(obj).html()+")?")){
		          	  
		      		  
		      		 var lugar = 'compromiso';
		            	 var memo =  LlenarMemo('Reintegrar totalmente',lugar);
		            	 
		            	if(memo){
		            	 
		            	  url = "../../acciones/comp/comp.php?accion=ReintegroTotal&memo="+memo+"&comp="+$(obj).html(); 
		                 window.location = url;
		            	  
		            	}else{
		  					 
		  				  return;
		  					 
		  			}

		    				 }else{
		    					 
		    					return;
		    					 
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
	     			
	     			obj.val('');
	     			
	     		      var montoTotal1 = 0;
	     		         var dato1 = 0 ;
	     		         
	     		         $('input[name=\'pcta[monto][]\']').each(function (index) {
	     		        	 
	     		        
	     		            if((this.value != '')){
	     		         	   
	     		         	  
	     		            	 dato1  = QuitarCaracter(this.value,".");
	     		            	
	     		                montoTotal1 = (montoTotal1+dato1);	
	     		               
	     		            }

	     		 	 		 
	     		 	 	 });
	     		         
	     		
	     		        var montoTotal2 = number_format(montoTotal1,2,',','.');
	     		        

	     		        ver_monto_letra(montoTotal1, 'monto_letras','');

	     				   $('#montoTotal').html(montoTotal2);
	     				   
	     			

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
	      
	      function CambioTraza(obj)
	      {
	    	  
	    	   if(event = window.event){

	  	    	 if (jQuery.browser.msie) {    
	  	    	      event.cancelBubble = true;    
	  	    	        } else {  
	                     event.stopPropagation();
	    
	  			}
	    	   }
	    	   
	    	   
	    	   $.ajax({
					
				    async:	false,
				    data: {
						accion: "GetUltimaTrazaReporte",
						Comp: $(obj).attr('comp')
						
					},
					url:"../../acciones/comp/comp.php",
					type:	"post",
					dataType:"json",
					success: function(json){
						
						
						 fechaac = json;
						
					}
	    	   });
	  		
	    	   
	    	   
	    	   monthNames = new Array();
   			 	
               var elem = fechaac.split('/');
	      		mesActual = parseInt(elem[1]);

	      		
	      		
	      		if(mesActual > 1){
	    	   
	  							$('.caso').remove();

	  							var tbody = $('#tablaTraza')[0];

	  	      			 		var fila = document.createElement("tr");
	  	      			 		fila.className='normalNegro  caso';

	  	      			 		var columna1 = document.createElement("td");
	  	      			 		columna1.setAttribute("valign","top");
	  	      			 		columna1.setAttribute("class","top");
	  	      			    	columna1.setAttribute("id","comptrazanueva");
	  	      			 		columna1.className='normalNegroNegrita tablaTraza';
	  	      			 		columna1.appendChild(document.createTextNode($(obj).attr('comp')));
	  	      			 		
	  	      			 		
		  	      			 	var columna2 = document.createElement("td");
			  	      	 		columna2.setAttribute("valign","top");
	  	      			    	columna2.className='normalNegro  caso';
	  	      			 		columna2.className='normalNegroNegrita tablaTraza';
	  	      			 		columna2.appendChild(document.createTextNode(fechaac));

	  	      			 	
	  	      			 	
	  	      			 	
	  	      			 		
	  	      			        monthNames = [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre","Octubre", "Noviembre", "Diciembre" ];

						 		var columna3 = document.createElement("td");
						 		columna3.setAttribute("valign","top");
	  	      			 		columna3.setAttribute("class","top");
						
						 		var select = document.createElement("select");
						 		select.setAttribute("type","text");
						 		select.setAttribute("id","fechatrazanueva");
						 		select.setAttribute("class","dateparse");
						 		select.setAttribute("name","fechatrazanueva");
						 		
						 		i = 1;
						 		
						 while(i < mesActual){
							 
						 		var option = document.createElement("option");

						 		option.setAttribute("class","dateparse");
						 		option.setAttribute("value",i);
						 		option.appendChild(document.createTextNode(monthNames[i-1])); 
						 		select.appendChild(option);
						 		i++;
	  	      			   }
						 		
						 		columna3.appendChild(select);

						 	

	  	      			 		fila.appendChild(columna1);				
	  	      			 		fila.appendChild(columna2);
	  	      			 		fila.appendChild(columna3);
	  	      			 		tbody.appendChild(fila);


	  			
	    		$( "#dialogisss" ).dialog({ title: 'Comprimiso a cambiarle la fecha: '+$(obj).html()});
	    		
	    		 $( "#dialogisss" ).dialog( "open" );
	    		 
	    		 
	      		}else{
	      			
	      			alert("No se puede cambiar la fecha ya que la misma esta en el mes de enero");
	      			
	      		}
	    	  
	      }   
	      
	      
	      function ModificarFechaTraza()
	      {
	    	  if($('#fechatrazanueva').val() == "")
				{
				  alert("Debe seleccionar la nueva fecha del compromiso.");
				  $('#fechatrazanueva').focus();
				  return;
				  
			    }else{
	    	  
				if(confirm("¿Est\u00E1 seguro que desea cambiar la fecha del compromiso.")){

	    	   $.ajax({
					
				    async:	false,
				    data: {
						accion: "ModificarTrazaReporte",
						Comp: $('#comptrazanueva').html(),
						fechanueva: $('#fechatrazanueva').val(),
						tipoRespuesta: "json"
					},
					url:"../../acciones/comp/comp.php",
					type:	"post",
					dataType:"json",
					success: function(json){
						
						if(json){
							
							if(confirm("¿Se cambio la fecha de las trazas para el compromiso ("+$('#comptrazanueva').html()+"). Desea actualizar la busqueda.")){
								
								$("#txt_inicio").val('');
							    $("#hid_hasta_itin").val('');
							    $("#asunto").val('');
							    $("#tipoActividad").val('');
							    $("#PartidaBusqueda").val('');
							    $("#compProyAccVal").val('');
							    $("#palabraClave").val('');
							    $("#ncompromiso").val($('#comptrazanueva').html());  
							    
							    $('#formCompFiltro').attr('action','../../acciones/comp/comp.php?accion=BuscarCompAccion');
								$('#formCompFiltro')[0].submit();				
								
								
							}else{
								
								 $("#dialogisss").dialog("close");
							}
							
						}else{
							
							alert('se produjo un error al cambier la fecha de las trazas');
							
						}
						
				        }
			        });
	    	   
	    	   
				}else{
					
					return;
				}
	    	  }
	      }
	      
	   