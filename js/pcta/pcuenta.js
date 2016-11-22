

      var 	  
      
          TABMENUITEM_PUNTO_CUENTA_BANDEJA = null,
	      TABMENUITEM_PUNTO_CUENTA_INSERTAR = null,
          tipoproacc,
          memo = null,
          categoriaTipo, 
          id_especifica, 
	      id_proy_accion,
          tamanoFile = 0,
          respaldosDigitales = 0,
          respaldosFisicos = 0,
          idCadenaSigiente = 0,
          pctaModRecursos = null,
          puntoCuentaImputa = null,
          pctaModMontoTotal = 0,
          puntoCuentaRespaldo = null,
          dependencia = null;
          pctaAsociado = null;
          PHPSESSID = '';
          regisFisDigiEli = new Array();
          regisNombreDigital = new Array();
          partidasMontoModificar = new Object();
          codProyAccEspMOD = null;
			codProyAccMOD = null;
			proyAccMOD = null;
 
	   
		$().ready(function() {

				$( "#dialogisss" ).dialog({ autoOpen: false });
				$( "#dialogisss").dialog({ show: "puff" });
				$( "#dialogisss").dialog({ hide: "explode" });
				$( "#dialogisss").dialog({ width: 400});
				$( "#dialogisss").dialog({ position: { my: "center top", at: "center top "}});
				
				
             if(TABMENUITEM_PUNTO_CUENTA_INSERTAR){
            	 
            	 padre = $(window.parent.document.getElementById(TABMENUITEM_PUNTO_CUENTA_BANDEJA));
            	 padre != null?  padre.attr('style','background-color:#8BBF8E') : '';
            	 
            	 padre = $(window.parent.document.getElementById(TABMENUITEM_PUNTO_CUENTA_INSERTAR));
            	 padre != null?  padre.attr('style','background-color:#rgb(246, 255, 213)') : '';

               }
             
             if($('#pctaAsunto').val() == '013'){
 				
            	 $("#trPctaAsociado").show();
            	 
 				}else{
 					  
 					$("#trPctaAsociado").hide();
 						

 					}
             
				
				if($('#pcuenta_descripcion').length > 0){
				
				var opts = {
						doctype  :	' <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">',
						cssClass : 'el-rte',
						lang     : 'es',
						height   : 400,
						toolbar  : 'maxi',
						cssfiles : ['js/editorlr/css/elrte-inner.css']
			
					};
							

				$('#pcuenta_descripcion').elrte(opts);
				
				 $('#pcuenta_descripcion2').hide();
				 $('#imgDescrip2').hide();
				 $('#divPartida').hide();
				 $('#tablacategoria').hide();
				 $('#trcategoriaesp').hide();
				 $('.patidaseleccionada').hide();
				 $("#cuerpoCategoriaPartida").hide();
				 $("#cuerpoRespaldoFisico").hide();
				 $("#AgregarRespaldoFisico").hide();

				 ver_monto_letra(0, 'monto_letras','');
				 
				 SolicitadoPor();
				 
				
				
			if($('#pcuenta_descripcion').elrte('val')){

				$('#spanDescripval').val(0);
				mostrarOcultarEditor();
				
				}

				 $('#AgregarRespaldoFisico').click(function(){

	                     
					 AgregarRegistroFisico();    

			     });
				 
			     
				}
				
				
				if($('.mensajeInformacion').children('li').html() == 'No se han encontrado registros'){

	                 // alert($('.mensajeInformacion').children('li').html());

	                 $('.mensajeInformacion').children('li').css({

	                     'color' : '#52865A',
	                     'font-size' : 18,
	                     'margin-top' :15
	                                              

	                     });

					}else{

		
						 $('.mensajeInformacion').children('li').css({'font-size' : 16});
						 
			            // $('.mensajeInformacion').children('li').show('slide').delay(3000).hide('scale');

						

						}
				if($('.mensajeError').children('li').html() != ''){
			  
			 // alert($('.mensajeError').children('li').html());
			   
			   $('.mensajeError').children('li').css({'font-size' : 16});
				 
             // $('.mensajeError').children('li').show('slide').delay(5000).hide('scale');
			   
				}

            $(".detalleApartadoPcta").click(function(event){
            	
           	 
		    	 if (jQuery.browser.msie) {    
		    	      event.cancelBubble = true;    
		    	        } else {  
                          event.stopPropagation();
				}
            	
            	var msj = $(this).attr('tipo') == 'Compromiso'? 'Comprometido' : $(this).attr('tipo') ;
            	
            	
          var tipo = $(this).attr('tipo');

            	$( "#dialogisss" ).dialog({ title: 'Monto '+msj});

            	var partida = $(this).attr('partida');
            	var montoTotal1 = $(this).html() != false? $(this).html() : 0;
            	var montoTotal2 = 0;
 

            	$.ajax({	
    			    async:	false,
    			    data: {
    			    	accion: "reporteDisponibleIntegradoAccionDetalle",
        				tipoRespuesta: 'json',
        				tipo: $(this).attr('tipo'),
        				partida: $(this).attr('partida'),
        				pcta: $(this).attr('pcta'),
        				aopres: $(this).attr('aopres'),
        				monto: $(this).attr('monto')
    				},
    				url:	"../../acciones/pcta/pcta.php",
    				type:	"post",
    				dataType:"json",
    				success: function(json){
    					
    					
    					 tdClass ='even';	
					     
    					 $('#tablaDetalleDisponibilidad').html('');   
    					 $.each(json,function(id,val){
    					tdClass = (tdClass == "even") ? "odd" : "even";
    						
					   var tbody = $('#tablaDetalleDisponibilidad')[0];

				 		var fila = document.createElement("tr");
				 		fila.className='normalNegro '+tdClass;	;

				 		var columna1 = document.createElement("td");
				 		columna1.setAttribute("valign","top");
				 		columna1.className='normalNegroNegrita';
				 		
				 		data = val.pcta;
				 		doc = data.substring(0,4);	
				 		
				 		
	                     if(tipo == 'Apartado'){
				 			
	                    	 var alink = document.createElement("a");
	                         alink.setAttribute("href","#dialog");
	                         alink.setAttribute("docgId",val.pcta);
	                         alink.setAttribute("opcion",'null');
	                         alink.className='detalleOpcion2';
	                         alink.appendChild(document.createTextNode(val.pcta));

					 		columna1.appendChild(alink);
					 		
					 		

				 			
				 		}else  if(tipo == 'Compromiso'){
				 			
				 			
				 			Link = document.createElement("a");
							Link.setAttribute("href","javascript:abrir_ventana('../../documentos/comp/comp_detalle.php?codigo="+val.pcta+"&esta_id=10')");
							linkText = document.createTextNode(val.pcta);
							Link.appendChild(linkText);
							columna1.appendChild(Link);
					 		
								
                        }else  if(tipo == 'Causado'){
				 			
				 			
				 			Link = document.createElement("a");
				 			
				 			 if(doc == 'sopg'){
				 			
							    Link.setAttribute("href","javascript:abrir_ventana('../../documentos/sopg/sopg_detalle.php?codigo="+val.pcta+"&esta_id=10')");
						 
				 			 }
				 			 
				 			 if(doc == 'codi'){
						 			
								    Link.setAttribute("href","javascript:abrir_ventana('../../documentos/codi/codi_detalle.php?codigo="+val.pcta+"&esta_id=10')");
							 
					 		}
				 			 
				 			 
							linkText = document.createTextNode(val.pcta);
							Link.appendChild(linkText);
							columna1.appendChild(Link);
					 		
	
				 			
				 		}else{
				 			
				 			Link = document.createElement("a");
				 			
				 			 if(doc == 'pgch'){
						 			
									Link.setAttribute("href","javascript:abrir_ventana('../../documentos/pgch/pgch_detalle.php?codigo="+val.pcta+"&esta_id=10')");
							 
					 		}
				 			 
				 			 
				 			 if(doc == 'tran'){
						 			
								    Link.setAttribute("href","javascript:abrir_ventana('../../documentos/tran/tran_detalle.php?codigo="+val.pcta+"&esta_id=10')");
							 
					 		}
				 			 
				 			 
				 			 if(doc == 'codi'){
						 			
								    Link.setAttribute("href","javascript:abrir_ventana('../../documentos/codi/codi_detalle.php?codigo="+val.pcta+"&esta_id=10')");
							 
					 		}
				 			 

							linkText = document.createTextNode(val.pcta);
							Link.appendChild(linkText);
							columna1.appendChild(Link);
				 			
				 		
				 		}
				 	
				 		

				 		var columna2 = document.createElement("td");
				 		columna2.setAttribute("valign","top");
				 		columna2.className='normalNegroNegrita';
				 		columna2.appendChild(document.createTextNode(val.monto));


				 		fila.appendChild(columna1);				
				 		fila.appendChild(columna2);

				 		tbody.appendChild(fila);
				 		 
    						 
				 		montoTotal2 = val.montoTotal;
				 		partida = val.partida;
    					
				 		 });

    					 montoTotal2 == 0 ?$( "#detalleDisponTr1" ).hide(): $( "#detalleDisponTr1" ).show();
    					 
    					
    					$( "#detalleDisponPartida " ).html('Partida.: '+partida+ ' (Bs.: '+montoTotal1+')');
    					 $( "#detalleDisponMontoTotal2" ).html(montoTotal2);
    					 
    					 
    					 $( "#dialogisss" ).dialog( "open" );
    					 
    					 
    			        }
    		        });
            	
      
            	$('.detalleOpcion2').click(function(event) {
          			
  
          			detalleOpcion(event,this);
    	
          		});
				 
			 });
			

			
			 $("#pctaAsociado").change(function(){
				 
				 if($("#pctaAsociado").val() != ''){
					 
					SearchPctaAlcance($("#pctaAsociado").val());

				 }	 
			
				 
			 });
			
			
			 $("#pctaAsunto").change(function(){
				 
				 
				 if($("#pctaAsunto").val() == '013'){
					 
					 $("#trPctaAsociado").show(200);
					 
				 }else{
					 
					 $("#trPctaAsociado").hide(100);
					 
				 }

				 
			 });
			
			
			 $("#BuscarPcta").click(function() {
			
				 

					          if(
					        		  
					        	$("#txt_inicio").val().length < 1  && 
							    $("#hid_hasta_itin").val().length < 1   &&
							    $("#pctaAsunto").val().length < 1   && 
							    $("#PartidaBusqueda").val().length < 1  && 
							    $("#estatusPcta").val().length < 1      && 
							    $("#pctaProyAccVal").val().length < 1      && 
							    $("#codigPctaBusqueda").val().length < 1&& 
							    $("#palabraClave").val().length < 1     && 
							    $("#DependenciaPcta").val().length < 1     && 
							    $("#ncompromiso").val().length < 1         

					 ){
						 
						 
						 
						 alert('Debe seleccionar un campo'); 
						 
					   }else{
						   
						   if(($("#txt_inicio").val().length > 1 && $("#hid_hasta_itin").val().length < 1) ||
								($("#hid_hasta_itin").val().length > 1 && $("#txt_inicio").val().length < 1)){
							   
							   
							   alert('Debe seleccionar un rango de fecha');
							   
						   }else{

							   $('#formPctaFiltro').attr('action','../../acciones/pcta/pcta.php?accion=SearchPcta');
							   $('#formPctaFiltro')[0].submit();				
					   }
					   }
	
				
				  
			  });
			 
            $("#RevisionesYMemos").click(function() {
            	
         
            	if($("#RevisionesYMemos").attr('opcion') == 0 ){
    		
            		$("#RevisionesYMemos").html('.:Detalle punto de cuenta :.');
            		$("#RevisionesYMemos").attr('opcion',1);
            		
            		$("#window1").hide();
                	$("#window2").show('blind',300);
                	
                	

            	}else{

            		
            		$("#RevisionesYMemos").html('.:Revisiones y memos :.');
            		$("#RevisionesYMemos").attr('opcion',0);
            		
            		$("#window2").hide();
                	$("#window1").show('blind',300);
                	
              
            	}
            	
				 });
			
			 
			 $("#BuscarPctaDisp").click(function() {
					
		          if(
		        	
				    ($("#codigPctaBusquedaDisp").val().length < 1)  || 
				   (!validarcaracterespecial($("#codigPctaBusquedaDisp").val()))
				    
		           ){
			 
			 
			 
			 alert('El punto de cuenta no puede estar vac\u00edo y debe ser un c\u00f3digo v\u00e1lido'); 
			 
		   }else{
			   
			  
		 $('#formPctaDisp').attr('action','../../acciones/pcta/pcta.php?accion=SearchDisponibilidad');
		  $('#formPctaDisp')[0].submit();	
			
		   }
	
	  
 });
			
			 $("#codigPctaDis").click(function() {
				  
	
				  if($("#codigPctaDis").val() == ''){
					  
					  $("#codigPctaDis").val('pcta-');
					  $("#pctaProyAcc").attr('disabled','disabled');
					  $("#agnoPcta").attr('disabled','disabled');
					 $("#codigPctaDis").attr('disabled',false);
					  $("#pctaProyAcc").val('');
					  $('#pctaProyAccVal2').val('');
				  }
				 
				  
			  });
			 
			 $("#codigPctaDis").focusout(function() {
				  
				   
				  if($("#codigPctaDis").val() == 'pcta-' ||
				     $("#codigPctaDis").val().length < 5 || 
 					 ($("#codigPctaDis").val().substring(0,5) != 'pcta-')){
					  
					  $("#codigPctaDis").val(''); 
					  
					  $("#codigPctaBusqueda").attr('disabled',false);
					  $("#pctaProyAcc").attr('disabled',false);
					  $("#agnoPcta").attr('disabled',false);

		
						  
				  }
				  
			  });
              
			 

			
			    $("#codigPctaBusqueda").click(function() {
				  
				  if($("#codigPctaBusqueda").val() == ''){
					  
				      $("#codigPctaBusqueda").val('pcta-');
				      $("#txt_inicio").attr('disabled','disabled');
				      $("#hid_hasta_itin").attr('disabled','disabled');
					  $("#ncompromiso").attr('disabled','disabled');
					  $("#pctaAsunto").attr('disabled','disabled');
					  $("#PartidaBusqueda").attr('disabled','disabled');
					  $("#txt_clave").attr('disabled','disabled');
					  $("#estatusPcta").attr('disabled','disabled');
					  $("#pctaProyAcc").attr('disabled','disabled');
					  $("#agnoPcta").attr('disabled','disabled');
					  $("#palabraClave").attr('disabled','disabled');
					  
				  }
				 
				  
			  });
			    
				
			  
                 $("#codigPctaBusqueda").focusout(function() {
				  
				  
				  if($("#codigPctaBusqueda").val() == 'pcta-' ||
				     $("#codigPctaBusqueda").val().length < 5 || 
  					 ($("#codigPctaBusqueda").val().substring(0,5) != 'pcta-')){
					  
					  $("#codigPctaBusqueda").val(''); 
					  
					      $("#txt_inicio").attr('disabled','');
					      $("#hid_hasta_itin").attr('disabled','');
						  $("#codigPctaBusqueda").attr('disabled','');
						  $("#agnoPcta").attr('disabled','');
						  $("#ncompromiso").attr('disabled','');
						  $("#pctaAsunto").attr('disabled','');
						  $("#PartidaBusqueda").attr('disabled','');
						  $("#txt_clave").attr('disabled','');
						  $("#estatusPcta").attr('disabled','');
						  $("#pctaProyAcc").attr('disabled','');
						  $("#palabraClave").attr('disabled','');
				  }
				  
			  });
                 
                 
                 
                 $("#codigPctaBusquedaDisp").click(function() {
   				  
   				  if($("#codigPctaBusquedaDisp").val() == ''){
   					  
   				      $("#codigPctaBusquedaDisp").val('pcta-');
   				  
   			     	  }
   				 
   				  
   			  });
                 
                
                 
                 $("#codigPctaBusquedaDisp").focusout(function() {
   				  
   				  
   				  if($("#codigPctaBusquedaDisp").val() == 'pcta-' ||
   				     $("#codigPctaBusquedaDisp").val().length < 5 || 
     					 ($("#codigPctaBusquedaDisp").val().substring(0,5) != 'pcta-')){
   					  
   					  $("#codigPctaBusquedaDisp").val(''); 
   					  
   					  
   				  }
   				  
   			  });
                 
                 
                 $("#ncompromiso").click(function() {
   				  
   				  if($("#ncompromiso").val() == ''){
   					  
   					 $("#ncompromiso").val('comp-');
   					 
   				      $("#txt_inicio").attr('disabled','disabled');
				      $("#hid_hasta_itin").attr('disabled','disabled');
					  $("#codigPctaBusqueda").attr('disabled','disabled');
					  $("#agnoPcta").attr('disabled','disabled');
					  $("#pctaAsunto").attr('disabled','disabled');
					  $("#PartidaBusqueda").attr('disabled','disabled');
					  $("#txt_clave").attr('disabled','disabled');
					  $("#estatusPcta").attr('disabled','disabled');
					  $("#pctaProyAcc").attr('disabled','disabled');
					  $("#palabraClave").attr('disabled','disabled');
   				  
   				       
   				  
   				  
   				  }
   				  
   				 $("#ncompromiso").focusout(function() {
   				  

   				  if( ($("#ncompromiso").val() == 'comp-')  ||
   					  ($("#ncompromiso").val().length < 5) || 
   					  ($("#ncompromiso").val().substring(0,5) != 'comp-')){
   					  
   					
   					  
   					  $("#ncompromiso").val(''); 
   					  
   					  $("#txt_inicio").attr('disabled','');
				      $("#hid_hasta_itin").attr('disabled','');
					  $("#codigPctaBusqueda").attr('disabled','');
					  $("#agnoPcta").attr('disabled','');
					  $("#ncompromiso").attr('disabled','');
					  $("#pctaAsunto").attr('disabled','');
					  $("#PartidaBusqueda").attr('disabled','');
					  $("#txt_clave").attr('disabled','');
					  $("#estatusPcta").attr('disabled','');
					  $("#pctaProyAcc").attr('disabled','');
					  $("#palabraClave").attr('disabled','');
   				  }
   				  
   			  });
   				  
   				  
   				 
   				  
   			  });
   			  
                   
			$('#trActualesFis').hide();
			
			var data = $('#modificarPcta').html();
			
			if(data != '' && data != null){
				
				$('#modificarPcta').parent('span').css({'color':'white'});
				
				$('#opcionModificar').html('');
				
				
				
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
		 		input5.setAttribute("name","idPcta");
		 		input5.value= data;
		 		$('#opcionModificar')[0].appendChild(input5);
		 		
		 		$("#cancelar").click(function() {
		 		    event.preventDefault();
		 		    history.back(1);
		 		});
				
			}

      		 $('#OpcionesPdf').click(function(event) {

				 url = "../../acciones/pcta/pcta.php?accion=DetallePctaPdf&key="+$('span[detalle=\'pcuenta\']').html()+"";
	       	   // window.location = url;
	       	  window.open(url, '_blank'); 

				 });


			 
			 $(function() {
				 
					$('#file_upload').uploadify({
						'formData'     : {
							'accion' : 'GuardarImg',
							'PHPSESSID' : PHPSESSID
						},
					    'swf'      	: '../../js/lib/uploadify/uploadify/uploadify.swf',
						'uploader' 	: '../../acciones/pcta/pcta.php',
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

							$('#formPcta')[0].submit();
							
				        }
						

					});
				});
			 
			 
			
			
	

			 
			 $("#pctaRespaldoFisico").keyup(function(event) {

	               val = trim($("#pctaRespaldoFisico").val());
				 
                  if( val != '' &&  $("#pctaRespaldoFisico").val().length > 4){
				 
                  $("#AgregarRespaldoFisico").show('fade',300);

                  }else{
                      
                	  $("#AgregarRespaldoFisico").hide(200);
                	  
                      }

			 });

		
			 $('#PartidaBusqueda').focusout(function() {

				if($('#PartidaBusqueda').val().length < 13){
					
					$('#PartidaBusqueda').val('');
					
				
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
				
				
			 $('#op_recursosNo').change(function(){
				
                          
				 op_recursosNo();
                           
					
				 });


			
			 $('#op_recursosSi').change(function(){
					

				 op_recursosSi();

				 });      
			 

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
						 var respuenta = false;
						 var proyacc = false;

					 if($('#selectProyAcc').val() != ''){
						 
						 
					     $('input[name=\'pcta[monto][]\']').each(function (index) {

					    	 obj = $(this).parent("td").parent("tr.trCaso");
					    	 
					    	 proyacc =   obj.find('input[name=\'pcta[codProyAcc][]\']');

					    	 
					    	 if($('#selectProyAcc').val() != proyacc.val() && entro == false){
					    		 entro = true;
					    		  
					    	 }
			
				 	  });	
					     
					   
						 }
					     
					    if(entro){
					    	
					        	respuenta = CentroGestoryCostoDifenente('ProyAcc'); 
					    
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
			         
					
					

					$('#confirmarPartida').click(function(){
						
			        	  AgregarCategoriaPartida();
				          		
					});
					         
			 
            


             
			 $('#spanDescrip').click(function(){
	 
		              mostrarOcultarEditor();
		 
	         });
	         
			 

			 $('#SolicitadoPor').change(function(){

				 SolicitadoPor();
						
						 
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

				$('#pctaProyAcc').autocomplete({
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
									anno: $('#agnoPcta').val()!=null?$('#agnoPcta').val():''
								},
								success: function(json){
							
									
									var index = 0;
									var items = new Array();

									if(json == ""){
										
										 $('#pctaProyAccVal').val('');
										 $('#pctaProyAccVal2').val('');
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
					 $('#pctaProyAccVal').val(ui.item.id_especifica);
					 $('#pctaProyAccVal2').val(ui.item.id_proy_accion+'/'+ui.item.id_especifica);

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
										
										 items[index++] = {
												 
												value: value,
												tipo: tipo,
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

							
                             id_especifica = ui.item.id_especifica;
						     id_proy_accion = ui.item.id_proy_accion;
							
							 
							 var entro = false;
							 var respuenta = false;
							 var proyacc = false;
							 
							 	 if ($('#Categoria').val() != '') {
					 		 
							 		if(codProyAccEspMOD == null){
							 		 
								  $('input[name=\'pcta[monto][]\']').each(function (index) {

								    	 obj = $(this).parent("td").parent("tr.trCaso");
								    	 
								    	 codProyAccEsp =   obj.find('input[name=\'pcta[codProyAccEsp][]\']');
								    	 					    	 
								    	 if(ui.item.id_especifica != codProyAccEsp.val() && entro == false){
								    		 respuenta = CentroGestoryCostoDifenente('ProyAccEsp'); 
								    		 entro = true; 
								    		 
								    	 }

								    	
			
						
							 	 });	
								  
								  
							 		}else{
							 			
							 			

								    	 obj = $(this).parent("td").parent("tr.trCaso");
								    	 
								    	 
								    	 if(ui.item.id_especifica != codProyAccEspMOD && entro == false){
								    		 respuenta = CentroGestoryCostoDifenente('ProyAccEsp'); 
								    		 entro = true; 
								    		 
								    	 }

							 			
							 		}
							 		
								  
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
												anno: $('#agnoPcta').val(),
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


	
	
	
	
	
	function SearchPctaAlcance(id){

		$.ajax({
			url: "../../acciones/pcta/pcta.php",
			dataType: "json",
			data: {
				accion: "SearchImputasPcuentas",
				tipoRespuesta: 'json',
				key: id

			},
				success: function(json){	
					
						$.each(json,function(id,val){
							
                        if(val.puntoCuenta.puntoCuentaImputa){
                        	  
                        	  op_recursosSi();
                		      $("#op_recursosSi").attr('checked', 'checked');
                          
                          }
                          else{

                        	  op_recursosNo();
                		      $("#op_recursosNo").attr('checked', 'checked');
                        	  
                              }


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
							
							
						    var dato9 = GetDisponibilidad(val2.tipoImpu,proyAcc,proyAccEsp,val2.partida.id);
						    
						    if(partidasMontoModificar[proyAcc+proyAccEsp+dato5]){
						    	
						    	num = parseFloat(partidasMontoModificar[proyAcc+proyAccEsp+dato5]);

						    	num2 =  parseFloat(dato9);
						    	disponibilidad = (num + num2);
						    	
						    }
					     
						    
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
					 		linkText = document.createTextNode("Eliminar");
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
					 		
					 	

							  ver_monto_letra(0, 'monto_letras','');
							  var  montoTotal2 = number_format(0,2,',','.');

							  $('#montoTotal').html(montoTotal2);
					 		
					 		
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
	


	
	if(pctaModRecursos != null){
		 
		
	   if(pctaModRecursos == 0){
		   
		  
		      op_recursosNo();
		      $("#op_recursosNo").attr('checked', 'checked');
		   
	   }else{
		   
		   
		   
		   if(puntoCuentaImputa){

		      	$('#tablaCategoriaPartida').children("tr.trCaso").remove();   
             

				  $.each(puntoCuentaImputa,function(id,val){
					  
					    
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
							
							 var dato7 =  number_format(val.monto,2,',','.');
							 
					        var dato9 = GetDisponibilidad(val.tipoImpu,proyAcc,proyAccEsp,val.partida.id);
					        
					        partidasMontoModificar[proyAcc+proyAccEsp+dato5] = dato7;
					        
					        dato10 = (parseFloat(dato9) + parseFloat(val.monto));
					        
					        dato11 =  number_format(dato10,2,',','.');
					        
					        
					        codProyAccEspMOD = 	proyAccEsp;
							codProyAccMOD = proyAcc;
							proyAccMOD = dato3;
						
					      // alert('Disponibilidad: '+dato9+' monto:
							// '+parseFloat(val.monto)+' total: '+dato10);
					        
					        
					     
					 	    var tbody = $('#tablaCategoriaPartida')[0];	 		
					 		dato11 =  number_format(dato10,2,',','.');
					 	  
					 	
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
					 		columna8.className = 'link';
					 		deleteLink = document.createElement("a");
					 		deleteLink.setAttribute("href","javascript:void(0);");
					 		linkText = document.createTextNode("Eliminar");
					 		deleteLink.appendChild(linkText);
					 		columna8.appendChild(deleteLink);
					 		
					 		var columna9 = document.createElement("td");
					 		columna9.setAttribute("class","tdDisponibilidad");
							columna9.setAttribute("valign","top");
							columna9.appendChild(document.createTextNode(dato11));

					
					 		$(deleteLink).bind('click', function(){
					 			eliminarCategoriaPartida(this);
					 		});
					 		
	                        $(input7).keyup(function(){
	                        	
	                     	//formato_num($(this));

					 			
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
					 		
					 		 pctaModMontoTotal = pctaModMontoTotal + parseFloat(val.monto);
					 		
					 	//	alert(pctaModMontoTotal);
				  });


				  ver_monto_letra(pctaModMontoTotal, 'monto_letras','');

				  var  montoTotal2 = number_format(pctaModMontoTotal,2,',','.');
				  
				  
				  $('#montoTotal').html(montoTotal2);
	 		         
	 	        	
	
	  
	      }

        	} 
	   }
	
	

	
	if(puntoCuentaRespaldo){  
		

		$.each(puntoCuentaRespaldo,function(id,val){
           
			
			
		if(val.respTipo == 'Digital'){
			
			$('#trActualesDigital').show();
			
			
			
			var tbody = $('#tbodyRespDigitalesPcuenta')[0];	
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
             
           // OPCION DE ELIMINAR
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
				
			 
				var tbody = $('#tbodyRespFisicoPcuenta')[0];	
				var fila = document.createElement("tr");
			    fila.setAttribute("class","trFisicoEliminar");
				var columna = document.createElement("td");
				    columna.setAttribute("style","border-bottom: 1px solid #D8D8D8");
	            var alink = document.createElement("a");
	             alink.setAttribute("href","javascript:void(0);"); 
	             alink.appendChild(document.createTextNode(val.respNombre));
	             columna.appendChild(alink);	
	             
	           // OPCION DE ELIMINAR
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

		function EliminarRegisDigitFisicActual(obj,tipo,id,nombre) {
			
			if(tipo == 0 ){
				
				  
				regisFisDigiEli[regisFisDigiEli.length] = id; 
				regisNombreDigital[regisNombreDigital.length] = nombre;
					
					
		
			objTrs = $(obj).parents("tr.trDigitalEliminar");
 			objTrs.hide(100).remove();
 			
 			
 			
			if($("#tbodyRespDigitalesPcuenta > tr").length < 1){
				
				$('#trActualesDigital').hide(300);
				
			}
 			
			}else{
			
				regisFisDigiEli[regisFisDigiEli.length] = id; 
			
				
				objTrs = $(obj).parents("tr.trFisicoEliminar");
	 			objTrs.hide(100).remove();
	 			
	 			
	 			
				if($("#tbodyRespFisicoPcuenta > tr").length < 1){
	
					$('#trActualesFis').hide(300);
			}
			
			}
		}
		
		 });
		

     }

   

	         	});    // ///////////////////////////////////////////////////////////////
						// cierre de ready//////////////////////////7777


		

		   function op_recursosSi(){

	         
				 
				 $('#condPagotr').show('fade',300);  
				 $('#filsed1').show('fade',300);  
				   
		 }
	       
		 function op_recursosNo(){
			 
			 
				 var part = parseFloat($('#montoTotal').html());  

				
				 
				 if(part < 1){

					 eliminarPartidasAsociadas();                 

					 }else{
						 if(confirm("¿Est\u00E1 seguro que este punto de cuenta no tiene imputaci\u00F3n presupuestaria ? \n Al aceptar se eliminar\u00E1n todas las partidas registradas ?")){

							eliminarPartidasAsociadas();  

						 }else{
	                                    
							 $("#op_recursosSi").attr('checked', 'checked');
							 }
						 
						 }              

				
			 
			 }	  

		 


		function Enviar(){
			
			  var montoTotal1 = 0;
      	      var dato1 = 0 ;
      		
		         $('input[name=\'pcta[monto][]\']').each(function (index) {

		        	   if(this.value != ''){
	      	            	
	      	            	 dato1  = QuitarCaracter(this.value,".");
	      	            	 
	      	            	$(this).val(dato1);

	      	            }
		              
		  		    
		  	 		
		  	 	 });
			
		
			$('#formPcta').attr('action','../../acciones/pcta/pcta.php?accion=Registrar');

			LlenarCadenaSigiente();

			if(tamanoFile > 0 ){
				
			$('#file_upload').uploadify('upload','*');

			}else{
				
				$('#formPcta')[0].submit();
				
				}
		}
		
		
		function Modificar(){
			
			  $('input[name=\'pcta[monto][]\']').each(function (index) {

	        	   if(this.value != ''){
     	            	
     	            	 dato1  = QuitarCaracter(this.value,".");
     	            	 
     	            	$(this).val(dato1);

     	            }
	  	 		
	  	 	 });
			
			

			$('#regisFisDigiEli').val(regisFisDigiEli);
			$('#regisNombreDigital').val(regisNombreDigital);
			
			
			$('#formPcta').attr('action','../../acciones/pcta/pcta.php?accion=RegistrarModificar');
			
			if(tamanoFile > 0 ){
				
				$('#file_upload').uploadify('upload','*');

				}else{

					$('#formPcta')[0].submit();

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
	    
	    
	    
	    if(partidasMontoModificar[id_proy_accion+id_especifica+dato5]){
	    	
	    	num = parseFloat(partidasMontoModificar[id_proy_accion+id_especifica+dato5]);

	    	num2 =  parseFloat(dato9);
	    	dato9 = (num + num2);
	    	
	    }

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
 		columna7.setAttribute("style","padding:0;");
 		var input7 = document.createElement("input");
 		input7.setAttribute("type","text");
 		input7.setAttribute("name","pcta[monto][]");
 		input7.setAttribute("autocomplete","off");
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
   
   
   
   
	   

	   function eliminarCategoriaPartida(objA){

		   objTrs = $(objA).parents("tr.trCaso");
		   objTd = objTrs.children("td.tdMonto");
		   objInput = objTd.children("input");
		   
          objTrs.hide(100).remove();


		if($("#tablaCategoriaPartida > tr").length < 2){

			$("#cuerpoCategoriaPartida").hide(200);
			

			}
				
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

	   function eliminarPartidasAsociadas(){


		   $('#condPagotr').hide(200);
	       $('#filsed1').hide(200);
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
			 $('#divPartida').hide();
			 $('#selectProyAcc').val('');
			 
			 $('#tablaCategoriaPartida').hide();
			 $('#tablaCategoriaPartida').children("tr.trCaso").remove();

			 ver_monto_letra(0, 'monto_letras','');
			 $('#montoTotal').html('0'); 
				
		   

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


		function SolicitadoPor(){

			if ($('#SolicitadoPor').val() != "") {


				$('#DependenciaSolicitantetr').show(600);

				$.ajax({
					
    			    async:	true,
    			    data: {
    					accion: "DependenciaSolicitadoPor",
    					ci_empleado: $('#SolicitadoPor').val(),
    					tipoRespuesta: "json"
    				},
    				url:	"../../acciones/pcta/pcta.php",
    				type:	"post",
    				dataType:"json",
    				success: function(data){	
        				
    			   $('#DependenciaSolicitante').children("option").val(data['id']);

    			   $('#DependenciaSolicitante').children("option").html(data['nombre']);
    			   
    			        }
    		        });
				
		      
						
				}else{
					
				$('#DependenciaSolicitantetr').hide();
			     

			}
	
		}
	
		function validarcaracterespecial(campo) {

			var price = campo;
			
			var intRegex = /^[^'"~]*$/;

			campo2 = trimDato(campo);
	    	 
		    if ((price.match(intRegex)) && (campo2 != false)) {
		       
		    	return 1;
		    	
		    } else {
		    	

		    	return false;
		    } 
		    
		  
		}
		
		function validarcaracterespecial2(campo) {

			var price = campo;
			
			var intRegex =  /^[^'"~]*$/;

			campo2 = trimDato(campo);
	    	 
		    if (price.match(intRegex)) {
		       
		    	return 1;
		    	
		    } else {
		    	

		    	return false;
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

		function mostrarOcultarEditor(){

      	  if($('#spanDescripval').val() == 0){
	                
	                $('#spanDescrip').children("b").html('Ocultar editor');
	                $('#imgDescrip2').show();
	                $('#imgDescrip').hide();
	             	$('#pcuenta_descripcion2').show('fade',600); 
					$('#spanDescripval').val('1');

						
	                }else{

	                	 $('#spanDescrip').children("b").html('Mostrar editor');
	                	 $('#pcuenta_descripcion2').hide(450); 
	                	 $('#imgDescrip').show();
			                $('#imgDescrip2').hide();
	                	 $('#spanDescripval').val('');
	                	 
	                    }               

            };

            function  trimDato(dato){

            	dato2 = trim(dato);


            	if(dato2 != ''){
            		return "1";
                       
                }else{
                	return false;
                	
                    }
            	
                }

		function Revisar()
		{
			
		    if($('#fecha').val() == "")
			{
			  alert("Debe seleccionar la fecha del punto de cuenta.");
			  $('#fecha').focus();
			  return;
		    }

		    if($('#preparado_para').val() == "")
			{
				alert("Debe seleccionar el destinatario del punto de cuenta");
				 $('#preparado_para').focus();
				return;
			}

		/*    if($('#SolicitadoPor').val() == "")
			{
		    	alert("Debe seleccionar la persona solicitante");
				 $('#SolicitadoPor').focus();
				return;
			} */


	    
		    if($('#pctaAsunto').val() == "")
			{
		    	alert("Debe seleccionar el asunto del punto de cuenta");
				 $('#pctaAsunto').focus();
				return;
			}else{
				
				
			
		
			if($('#pctaAsunto').val() == '013'){
				
				if($('#trPctaAsociado').find('span').html() == 'No hay puntos de cuenta aprobados.'){
					
					alert("No existe punto de cuenta finalizado para la selecci\u00f3n del alcance. Debe seleccionar otro asunto");
					$('#pctaAsunto').focus();
					return;	
					
				}else{
					  
					   if($('#pctaAsociado').val() == ''){
						alert("Debe seleccionar el punto de cuenta asociado");
						$('#pctaAsociado').focus();
						
						return;		
						

					}
				
				}
				
			  }	
				
		   }

		   
		    descripcionlegth = $('#pcuenta_descripcion').elrte('val').length;

				if(descripcionlegth < 5 )
					
				{  	
					alert("Debe especificar la descripci\u00f3n del punto de cuenta  y \u00E9ste s\u00f3lo puede tener caracteres alfanum\u00E9ricos");
					$('#spanDescrip').children("b").html('Ocultar editor');
	                $('#imgDescrip2').show();
	                $('#imgDescrip').hide();
	             	$('#pcuenta_descripcion2').show('fade',600); 
					$('#spanDescripval').val('1');	
					return;
				}

			 
			 if( (!validarcaracterespecial($("#justificacion").val())))
				{
					alert("Debe indicar la justificaci\u00F3n del Punto de Cuenta  y \u00e9sta s\u00f3lo puede tener caracteres alfanum\u00E9ricos");
					 $('#justificacion').focus();
					return;
				}
				
                   
			 if( (!validarcaracterespecial($("#convenio").val())))
			{
				alert("Debe indicar el lapso del convenio/contrato del punto de cuenta y \u00e9ste s\u00f3lo puede tener caracteres alfanum\u00E9ricos");
				 $('#convenio').focus();
				return;
			}
			
			 if( (!validarcaracterespecial($("#garantia").val())))
				{
					alert("Debe indicar la garant\u00EDa  del punto de cuenta  y \u00e9ste s\u00f3lo puede tener caracteres alfanum\u00E9ricos");
				    $('#garantia').focus();
				   
					return;
				}

			 if(trimDato($("#ProveedorSugeridoValor").val()) == false) 
				{

				 if(!validarcaracterespecial($("#ProveedorSugerido").val())){
					 
					 alert("Debe indicar el proveedor sugerido del punto de cuenta  y \u00e9ste s\u00f3lo puede tener caracteres alfanum\u00E9ricos"); 
					
					 $('#ProveedorSugerido').focus(); 
					 return; 
					 
				 }

				}

			 
			
			 if( (!validarcaracterespecial2($("#observaciones").val())))
				{
					alert("Las observaciones del punto de cuenta s\u00f3lo pueden tener caracteres alfanum\u00E9ricos");
				    $('#observaciones').focus();
				   
					return;
				}
			 
			   
			 if(!validarcaracterespecial($("#cond_pago").val()) && $('#op_recursosNo:checked').val() != '0')
				{
					alert("Debe indicar las condiciones de pago del punto de cuenta y \u00e9stas s\u00f3lo puede tener caracteres alfanum\u00E9ricos");
				    $('#cond_pago').focus();
				   
					return;
				}
			 

			 if($('#op_recursosNo:checked').val() != '0' && parseFloat($('#montoTotal').html()) < 1){
				 
				
				   var salir = false;

				   
				     $('input[name=\'pcta[monto][]\']').each(function (index) {
				    	 
				    	 obj = $(this).parent("td").parent("tr.trCaso");

				    	  partida = obj.find('td.tdPartida input');
				    	  tipo =   obj.find('input[name=\'pcta[tipo][]\']');
				    	  proyacc =   obj.find('input[name=\'pcta[codProyAcc][]\']');
				    	  proyAccEsp = obj.find('input[name=\'pcta[codProyAccEsp][]\']');

				            if((this.value == '' && salir == false)){

						    alert("El monto de la  partida("+partida.val()+") debe ser un n\u00famero real con separador decimal ','");
							
				           
				            $(this).focus(); 
							 salir = true;
				            }

				     disponibilidad = GetDisponibilidad(tipo.val(),proyacc.val(),proyAccEsp.val(),partida.val());
				     
				     if(partidasMontoModificar[proyacc.val()+proyAccEsp.val()+partida.val()]){
					    	
					    	num = parseFloat(partidasMontoModificar[proyacc.val()+proyAccEsp.val()+partida.val()]);

					    	num2 =  parseFloat(disponibilidad);
					    	disponibilidad = (num + num2);
					    	
					    }
				     
				    
				     if(disponibilidad < QuitarCaracter(this.value,".")){
			                alert("Debe indicar un monto disponible  para la partida("+partida.val()+"). Dicho monto debe tener separador de decimal '.'");

				         $(this).focus(); 
						 salir = true;
				    	 
				     }
			      
			 	 	 });	  
	
				        
				     if(salir){return;} 


				
					
					 if( (!validarcaracterespecial($("#selectProyAcc").val())))
				{
					alert("Debe indicar una categor\u00eda para el punto de cuenta");
   					 $('#selectProyAcc').focus();
   
					return;
				}

					 if( (!validarcaracterespecial($('#c1').html())))
						{
							alert("Debe indicar una acci\u00f3n espec\u00EDfica para el punto de cuenta y \u00e9sta s\u00f3lo puede tener caracteres alfanum\u00E9ricos");
		   					 $('#selectProyAcc').focus();
		   
							return;
						}
			
					

                

				if( (!validarcaracterespecial($('#Partidadenominacion').html())))
				{
					alert("Debe indicar una partida para el punto de cuenta y \u00e9sta s\u00f3lo puede tener caracteres alfanum\u00E9ricos");
					$('#Partida').focus();

					return;
				}

                var data = $('#modificarPcta').html();
                
      			if(data != '' && data != null){
                  
      				var motTotal =  $('#montoTotal').html();	
      				
      				var num = parseInt(motTotal.split(".").length -1);
      	        	 var i=0;
      	        	 
      	        	 while(i<= num){
      	        		 
      	        		motTotal = motTotal.replace('.','');
      	        	    i++;
      	        	    
      	              }
      	        	 
                    motTotal = motTotal.replace(',','.');
                    motTotal = parseFloat(motTotal);
  					
                  
                    
      				 if(confirm("¿Est\u00E1 seguro que desea modificar este Punto de Cuenta ?")){
      					 
      					 

      					motTotal > 0 ?$('#montoTotalHidden').val(motTotal):$('#montoTotalHidden').val('0');
      					
	  					  $('#pcuenta_descripcionVal').val($('#pcuenta_descripcion').elrte('val'));

	  					 Modificar();

	  	      			}           
      				
                  
      			}else{
      				
      				
      				
      				if(confirm("¿Est\u00E1 seguro que desea generar este Punto de Cuenta ?"))
	      			{
	                   var motTotal =  $('#montoTotal').html();	
	      				
	      				var num = parseInt(motTotal.split(".").length -1);
	      	        	 var i=0;
	      	        	 
	      	        	 while(i<= num){
	      	        		 
	      	        		motTotal = motTotal.replace('.','');
	      	        	    i++;
	      	        	    
	      	              }
	      	        	 
                        motTotal = motTotal.replace(',','.');
                        motTotal = parseFloat(motTotal);
      					
      					
                        motTotal > 0 ?$('#montoTotalHidden').val(motTotal):$('#montoTotalHidden').val('0');
                   $('#pcuenta_descripcionVal').val($('#pcuenta_descripcion').elrte('val'));
	                Enviar();
	      			}  
      				
      				
      			}

			 }else{
				 
				 var salir = false;

			     $('input[name=\'pcta[monto][]\']').each(function (index) {
			    	 
			    	 obj = $(this).parent("td").parent("tr.trCaso");

			    	  partida = obj.find('td.tdPartida input');
			    	  tipo =   obj.find('input[name=\'pcta[tipo][]\']');
			    	  proyacc =   obj.find('input[name=\'pcta[codProyAcc][]\']');
			    	  proyAccEsp = obj.find('input[name=\'pcta[codProyAccEsp][]\']');

			            if((this.value == '' && salir == false)){

					    alert("El monto de la  partida("+partida.val()+") debe ser un n\u00famero real con separador decimal ','");
						
			           
			            $(this).focus(); 
						 salir = true;
			            }

			     disponibilidad = GetDisponibilidad(tipo.val(),proyacc.val(),proyAccEsp.val(),partida.val());
			     
			     if(partidasMontoModificar[proyacc.val()+proyAccEsp.val()+partida.val()]){
				    	
				    	num = parseFloat(partidasMontoModificar[proyacc.val()+proyAccEsp.val()+partida.val()]);

				    	num2 =  parseFloat(disponibilidad);
				    	disponibilidad = (num + num2);
				    	
				    }
			     
			      
			
			     if(parseFloat(disponibilidad) < parseFloat(this.value)){
			    	 
			    	 alert("Debe indicar un monto disponible  para la partida("+partida.val()+"). Dicho monto debe tener separador de decimal '.'")
			         $(this).focus(); 
					 salir = true;
			    	 
			     }
		      
			     
		 	 	 });	  
			     if(salir){return;} 

				 var data = $('#modificarPcta').html();
	                
	      			if(data != '' && data != null){
	                  
	                  var motTotal =  $('#montoTotal').html();	
	      				
	      				var num = parseInt(motTotal.split(".").length -1);
	      	        	 var i=0;
	      	        	 
	      	        	 while(i<= num){
	      	        		 
	      	        		motTotal = motTotal.replace('.','');
	      	        	    i++;
	      	        	    
	      	              }
	      	        	 
                        motTotal = motTotal.replace(',','.');
                        motTotal = parseFloat(motTotal);
      					

	      				 if(confirm("¿Est\u00E1 seguro que desea modificar este Punto de Cuenta ?")){
	      					motTotal > 0 ?$('#montoTotalHidden').val(motTotal):$('#montoTotalHidden').val('0');
		  					  $('#pcuenta_descripcionVal').val($('#pcuenta_descripcion').elrte('val'));
		  	            	  
		  					  Modificar();
		  					
		  	      			}              
	      				
	                  
	      			}else{
	      				
	      				var motTotal =  $('#montoTotal').html();	
	      				
	      				var num = parseInt(motTotal.split(".").length -1);
	      	        	 var i=0;
	      	        	 
	      	        	 while(i<= num){
	      	        		 
	      	        		motTotal = motTotal.replace('.','');
	      	        	    i++;
	      	        	    
	      	              }
	      	        	 
                        motTotal = motTotal.replace(',','.');
                        motTotal = parseFloat(motTotal);
      					
				 
				  if(confirm("¿Est\u00E1 seguro que desea generar este Punto de Cuenta ?")){
					  
					  motTotal > 0 ?$('#montoTotalHidden').val(motTotal):$('#montoTotalHidden').val('0');
				
						 $('#pcuenta_descripcionVal').val($('#pcuenta_descripcion').elrte('val'));
	            	Enviar();
	            	
				  }
	            	
	            	
	      			}           
	                

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


      function Registroclikeado(obj){
          
    	 var valor = $(obj).attr('style');
    	 
    	 if(valor == false || valor == null){
      

    		 $(obj).find("a").each(function (index){

    	           $(this).animate({
    	        	 color: 'white',
    	        	 cursor: 'move'        
    	  	            },1);

    		 });

    		 $(obj).animate({
        		 color: 'white'
                 
             }, 50 );
        	 
    		 $(obj).animate({
    		 color: 'white',
             backgroundColor: "#516B55"
             
         },200);

    	 

    	 }else{
        	 
    		 $(obj).attr("style","");

    		 $(obj).find("a").each(function (index){

  	           $(this).attr("style","");

  		 });	

        

        }
    }


      function AccionesPcta(obj){
    	  obj =  $(obj);
    	  
    	  var url = false ;
    	  
    	

    	  if(obj.attr('id') == 'Modificar'){
		 
    		  url = "../../acciones/pcta/pcta.php?accion=Modificar&pcta="+$('span[detalle=\'pcuenta\']').html()+"";
     		  window.location = url; 
              
            }else{
            	 if(confirm("¿Est\u00E1 seguro que desea ("+obj.attr('id')+") este punto de cuenta ?")){

            	 if(obj.attr('id') == 'Anular' || obj.attr('id') == 'Devolver'){

                 var lugar = 'punto de cuenta';
            	 var	memo =  LlenarMemo(obj.attr('id'),lugar);	 
            	 

            	 
            	if(memo){
            	 
	             url = "../../acciones/pcta/pcta.php?accion=ProcesarPcta&opcion=0&memo="+memo+"&pcta="+$('span[detalle=\'pcuenta\']').html()+"&idCadenaSigiente="+obj.attr('idCadenaSigiente')+"&idopcion="+obj.attr('idopcion')+"&accRealizar="+obj.attr('id')+"";	   
	             
            	}
	             
            	 }else{
            		 
            		 
    	         url = "../../acciones/pcta/pcta.php?accion=ProcesarPcta&pcta="+$('span[detalle=\'pcuenta\']').html()+"&idCadenaSigiente="+obj.attr('idCadenaSigiente')+"&idopcion="+obj.attr('idopcion')+"&accRealizar="+obj.attr('id')+"";	   

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
      
      
      function  DevolverAprobado(obj){

          
    	  if(confirm("¿Est\u00E1 seguro que desea devolver este punto de cuenta ("+$(obj).html()+"), despu\u00e9s de finalizado?\n\n " +
    	  		"Si el punto de cuenta posee alcances asociados, \u00e9stos seran anulados.")){
        	  
    		  
    		 var lugar = 'punto de cuenta';
          	 var memo =  LlenarMemo('devolver despu\u00e9s de finalizado',lugar);	 
          	 
          	if(memo){
          	 
          	  url = "../../acciones/pcta/pcta.php?accRealizar=DevolverAprobado&opcion=1&accion=ProcesarPcta&memo="+memo+"&pcta="+$(obj).html(); 
              window.location = url;
          	  
          	}else{
					 
				return;
					 
			}


  				 }else{
  					 
  					return;
  					 
  					}
          
          }
      

      function  LiberarPcuenta(obj){
    	  
    	  
    	 
    	  
    	  var event;
    	
	    	 
         if(event = window.event){

	    	 if (jQuery.browser.msie) {    
	    	      event.cancelBubble = true;    
	    	        } else {  
                   event.stopPropagation();
  
			}
	    	 
         }
	    	
	
	    	var montodisppcta = $(obj).parent('td').parent('tr').find('td.montos').html();
	    	
	    	
          		  $( "#dialogisss" ).dialog({ title: 'Liberaci\u00f3n punto de cuenta ('+$(obj).html()+')'});
          		  
          		  
                	$.ajax({	
      			    async:	false,
      			    data: {
      			    	accion: "SearchPcuantaLiberacion",
          				tipoRespuesta: 'json',
          				key: $(obj).html()

      				},
      				url:	"../../acciones/pcta/pcta.php",
      				type:	"post",
      				dataType:"json",
      				success: function(json){
      					
      					
      					if(json != false && montodisppcta != '0,00'){
      				
      					 tdClass ='even';	
      				     
      					 $('#tablaLiberacion').html('');
      					 
      					 $.each(json,function(id,val){
      						 
      					tdClass = (tdClass == "even") ? "odd" : "even";
      						
      				   var tbody = $('#tablaLiberacion')[0];

      			 		var fila = document.createElement("tr");
      			 		fila.className='normalNegro '+tdClass;

      			 		var columna1 = document.createElement("td");
      			 		columna1.setAttribute("valign","top");
      			 		columna1.setAttribute("class","top");
      			 		columna1.className='normalNegroNegrita partidaLiberar';
      			 		columna1.appendChild(document.createTextNode(id));

      			 	

      			 		var columna2 = document.createElement("td");
      			 		columna2.setAttribute("valign","top");
      			 		columna2.className='normalNegroNegrita montoMaximoLiberar';
      			 		columna2.appendChild(document.createTextNode(number_format(parseFloat(val),2,',','.')));
      			 		
      			 		


      			 		fila.appendChild(columna1);				
      			 		fila.appendChild(columna2);
      			 		tbody.appendChild(fila);
      			 		 
      			
      			 		 });
      					 
      					$("#totalMontoDisponible").html(montodisppcta);
      					
      					 $( "#pctaLiberarId" ).val($(obj).html());
      					 $( "#dialogisss" ).dialog( "open" );
      					 $('#justificacionLiberacion').val('');
      					 $('#justificacionLiberacion').focus();
      					 
      					 
      					}else{


      						alert('Este punto de cuenta ('+$(obj).html()+')  no posee disponibilidad para liberar');


      					}
      					 
      			      }
      			        
      			        
      		        });

                }

            
           function RevisarLiberacion(){
          	 
        	   if( (!validarcaracterespecial($("#justificacionLiberacion").val())))
   			{
					alert("Debe indicar la justificaci\u00F3n de la liberaci\u00F3n y \u00e9sta s\u00f3lo puede tener caracteres alfanum\u00E9ricos");
   				 $('#justificacionLiberacion').focus();
   				return;
   			}else{
   				
   				if(confirm("¿Est\u00E1 seguro que desea liberar este punto de cuenta ("+$( "#pctaLiberarId" ).val()+")?")){


                    
                   url = "../../acciones/pcta/pcta.php?pcta="+$( "#pctaLiberarId" ).val()+"&accion=liberarPcuenta&justificacion="+$( "#justificacionLiberacion" ).val(); 
                    window.location = url;

        				 }else{
        					 
        					return;
        					 
        					}

   			}
          	 
          	 
          	 
           }


     function  MontoPartida(obj){

    	 $Obj2 = $(obj).parent("td").parent("tr.trCaso").children('.tdPartida').children('input');
    	 $Obj3 = $(obj).parent("td").parent("tr.trCaso").children('.tdDisponibilidad');
    	 
    	var  monto =  QuitarCaracter($(obj).val(),".");
      
    	 if(!validarNumerosfloat(monto.toString())){
    		 
    			
         alert("Debe indicar un monto v\u00e1lido  para la partida("+$Obj2.val()+"). Nota: El separador de decimal debe ser ','");
         // alert('llego');
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

        	if(parseFloat(dispon) < monto ){
        		
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

     function ReporteDisponibleIntegrado() {

    	 
			if ($("#codigPctaDis").val().length < 7 && $("#pctaProyAccVal2").val() == '') { 
				alert("Debe indicar el punto de cuenta o seleccionar la categor\u00EDa program\u00E1tica");
				return;
			}
			else {
				$("#formDisponibilidad").attr('action','pcta.php?accion=reporteDisponibleIntegradoAccion');
				$("#formDisponibilidad").submit();
			}
			
			
		}
     
     
     function CentroGestoryCostoDifenente(params){
    	 

    	 if(params == 'ProyAcc'){
    		 params= 'el proyecto o acci\u00F3n centralizada'; 
    		 
    	 }else{
    		 
    		  params= 'la acci\u00F3n espec\u00EDfica '; 
    		 
    	 }
    	 
    	 if(!puntoCuentaImputa){
    		 
    	      if($('#pctaAsunto').val() != 013){
    	    	  

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

				 }else{
                     
					 alert("No se puede cambiar "+params+" ya que este punto de cuenta es un alcance.");
					 return false;
					 
					 }
            

     }else{
    	 
    	 
		 confirm("No se puede modificar  "+params+" del  punto de cuenta, la cual es ("+proyAccMOD+").");

		 return false;
		 }
	 
	 }	  
     
     
     function SearchDetallePcta(id,opcion,idcadenaactual){
 		$.ajax({
 			url: "../../acciones/pcta/pcta.php",
 			dataType: "json",
 			data: {
 				accion: "SearchDetallePcta",
 				tipoRespuesta: 'json',
 				key: id

 			},
 				success: function(json){	
 					
 					
 					$('.opcionespctaremove').remove();
 					
 					if(opcion == 'pctaPorEnviarOpciones'){

 						if(pctaPorEnviarOpciones){
 							var devueltoFinalizado = json[0].puntoCuenta.devueltoFinalizado;
 							
 					    	 $.each(pctaPorEnviarOpciones[idcadenaactual], function(index, value) {

 					    		if(devueltoFinalizado !== false){
 					    			
 					    			if(value.opcion  == 104 ){
 					    				
 					    		
 					    				
 					    			 var fila = $('#trOpcionesDetalles')[0];	
 										var columna = document.createElement("td");
 										    columna.setAttribute("class","opcionespcta opcionespctaremove");
 										    var alink = document.createElement("a");
 			                                alink.setAttribute("href","#");
 			                                alink.setAttribute("idCadenaSigiente",value.id_cadena_hijo);
 			                                alink.setAttribute("idOpcion",value.opcion);
 			                                alink.setAttribute("id",value.wfop_descrip);
 			                                alink.setAttribute("onclick",'AccionesPcta(this)');
 			                                alink.appendChild(document.createTextNode(value.wfop_nombre));

 			                             columna.appendChild(alink);	
 			                             fila.appendChild(columna);		
 			                             
 					    			}
 			                             	
 					    		}else{
 					    			
 					    			 var fila = $('#trOpcionesDetalles')[0];	
 										var columna = document.createElement("td");
 										    columna.setAttribute("class","opcionespcta opcionespctaremove");
 										    var alink = document.createElement("a");
 			                                alink.setAttribute("href","#");
 			                                alink.setAttribute("idCadenaSigiente",value.id_cadena_hijo);
 			                                alink.setAttribute("idOpcion",value.opcion);
 			                                alink.setAttribute("id",value.wfop_descrip);
 			                                alink.setAttribute("onclick",'AccionesPcta(this)');
 			                                alink.appendChild(document.createTextNode(value.wfop_nombre));

 			                             columna.appendChild(alink);	
 			                             fila.appendChild(columna);	
 					    			
 					    			
 					    			
 					    			
 					    		}

 					    		});
 					    	 	 
 					    	 
 					    	 
 					      }
 				    } else{
 	
 				    		  $('#OpcionesPdf').hover(function(){

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
 					
 					
 							
                 
 						$.each(json,function(id,val){
 		
 							
 							  var myString = val.puntoCuenta.rifProveedorSugerido;

 							  var proveedorSugerido = myString.split(':');
 							  
 							  if(proveedorSugerido[1]){
 								  
 								  proveedorSugerido = proveedorSugerido[1] ;
 								  
 							  }else{
 								  
 								
 								  proveedorSugerido = val.puntoCuenta.rifProveedorSugerido;
 							  
 							  }

 						  $('span[detalle=\'pcuenta\']').html(val.puntoCuenta.id);
 						  $('td[detalle=\'preparadoPara\']').html(val.puntoCuenta.destinatario);
 						  
 							 						  
 				          var  fecha = val.puntoCuenta.fecha;// val.puntoCuenta.fecha
 				          
 				               cadena = fecha.split('/');
 		

 						  $('td[detalle=\'fecha\']').html(cadena[0]+"-"+cadena[1]+"-"+cadena[2]);

 						  if(val.puntoCuenta.remitente){
 						 $('td[detalle=\'solicitadoPor\']').html(val.puntoCuenta.remitente.nombres+" "+val.puntoCuenta.remitente.apellidos);
 						  }else{
 							  
 							  
 	 						 $('td[detalle=\'solicitadoPor\']').html('Direcci&oacute;n de Gesti&oacute;n Administrativa y Financiera');
  
 							  
 						  }
 						  
 						  
 						  
 						 if(val.puntoCuenta.presentadoPor){
 	 						  $('td[detalle=\'PresentadoPor\']').html(val.puntoCuenta.presentadoPor.nombres+" "+val.puntoCuenta.presentadoPor.apellidos);
 	 						  }else{
 	 							  
 	 							  
 	 	 						 $('td[detalle=\'PresentadoPor\']').html('Direcci&oacute;n de Gesti&oacute;n Administrativa y Financiera');
 	  
 	 							  
 	 						  }
 						  $('td[detalle=\'garantia\']').html(val.puntoCuenta.garantia);
 						  var montoPcta =  number_format(val.puntoCuenta.montoSolicitado,2,',','.');
 						  $('td[detalle=\'montoSolicitado\']').html(montoPcta);
 						  $('td[detalle=\'condicionPago\']').html(val.puntoCuenta.condicionPago);
 						  $('td[detalle=\'justificacion\']').html(val.puntoCuenta.justificacion);
 						  $('td[detalle=\'lapso\']').html(val.puntoCuenta.lapso);
 						  $('span[detalle=\'descripcion\']').html(val.puntoCuenta.descripcion);
 						  $('td[detalle=\'usuarioDetalle\']').html(val.puntoCuenta.usuario.id);
 						  $('td[detalle=\'asunto\']').html(val.puntoCuenta.asunto.nombre);

 					
 					
 						  if(val.puntoCuenta.asunto.id == '013'){

 							  
 						     $('td[detalle=\'pctaAsociado\']').html(val.puntoCuenta.puntoCuentaAsociado.id);
 						     $('#trPctaAsociadoDetalle').show();
 						     
 						  }else{
 							  
 							  $('td[detalle=\'pctaAsociado\']').html('');
 							  $('#trPctaAsociadoDetalle').hide();
 							  
 							  
 						  }
 						  
 						 
 						  
 						  $('td[detalle=\'proveedorSugerido\']').html(proveedorSugerido);
 						  $('td[detalle=\'observacion\']').html(val.puntoCuenta.observacion);
 						 
 						  $('td[detalle=\'dependencia\']').html(val.puntoCuenta.dependencia.nombre);



 							
                           if(val.puntoCuenta.recursos > 0){

                               $('td[detalle=\'recursos\']').html('Si'); 
                               $('#sinRecursos').show();
                               

                           }
                           else{

                         	  $('td[detalle=\'recursos\']').html('No');
                         	  $('#sinRecursos').hide();
                         	  $('#tablaCategoriaPartidaDetalles').children("tr.trCaso").remove();  

                               }


                           
 						
 						  if(val.puntoCuenta.puntoCuentaImputa){
 							  
 							
 				      	$('#tablaCategoriaPartidaDetalles').children("tr.trCaso").remove();   

     
 						  $.each(val.puntoCuenta.puntoCuentaImputa ,function(id2,val2){

 					 
 						   var tbody = $('#tablaCategoriaPartidaDetalles')[0];
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
 						  
 						  

 					
 						 $('#tbodyRespFisicos').children().remove();
 						 $('#tbodyRespDigitales').children().remove();
 						respaldosDigitales = 0;
 						respaldosFisicos = 0;
                         if(val.puntoCuenta.puntoCuentaRespaldo){                      
 						$.each(val.puntoCuenta.puntoCuentaRespaldo,function(id3,val3){

 						if(val3.respTipo == 'Digital'){
 							respaldosDigitales++;
 							var tbody = $('#tbodyRespDigitales')[0];	
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
 							var tbody = $('#tbodyRespFisicos')[0];	
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

 								
 								$('#respDig').show();

 							}else{

 							  $('#respDig').hide(); 
 						}	
 						if(respaldosFisicos > 0 ){

 							
 							$('#respFisic').show();

 						}else{

 						 $('#respFisic').hide(); 
 					     }	
 						
 						
 						
 						
 						if(revisiones = val.puntoCuenta.revisiones){
 							
 							
 							
 							$('#noRegistrosRevision').hide();
 							$('#tablaRevision').children('tr').remove();
 							num = 1;
 							
 							tdClass ='even';
 							
 							$.each(revisiones,function(id,val){
 								
 							var fecha = val.fecha;
 							
 					         var cadena = fecha.split('/');

 								
 								
 					      tdClass = (tdClass == "even") ? "odd" : "even";
 					 	   var tbody = $('#tablaRevision')[0];	
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
 						  
 						 
 						  // alert(val.cargoDependencia);
 								
 							fila.appendChild(columna1);	
 							fila.appendChild(columna3);
 							fila.appendChild(columna4);
 							fila.appendChild(columna5);
 							fila.appendChild(columna6);
 							tbody.appendChild(fila);
 							num++;
 							});
 							
 							
 						}else{
 							$('#tablaRevision').children('tr').remove();
 							$('#noRegistrosRevision').show();
 							
 							
 						}
 						
 					
 						
 						if(observacionesDoc = val.puntoCuenta.observacionesDoc){
 							
 							$('#noDocumentosAsociados').hide();
 							$('#tablaDocumentosAsociados').children('tr').remove();
 							num = 1;
 							
 							tdClass ='even';
 							
 							
 							$.each(observacionesDoc,function(id,val){
 	
 								var fecha = val.fecha; 
 						        var cadena1 = fecha.split(' ');
 						        var cadena= cadena1[0].split('-');
 						         
 					tdClass = (tdClass == "even") ? "odd" : "even";
 						   var tbody = $('#tablaDocumentosAsociados')[0];	
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
 							

 						  // alert(val.cargoDependencia);
 								
 							fila.appendChild(columna1);	
 							fila.appendChild(columna2);
 							fila.appendChild(columna3);
 							fila.appendChild(columna4);
 							tbody.appendChild(fila);
 							num++;
 							});
 							
 							
 						}else{
 							
 							$('#tablaDocumentosAsociados').children('tr').remove();
 							$('#noDocumentosAsociados').show();
 							
 						}
 						
 						


 						
 						
 						
                         if(alcances = val.puntoCuenta.alcances){
 							
 							$('#noTablaAlcancesPcuenta').hide();
 							$('#tablaAlcancesPcuenta').children('tr').remove();
 							num = 1;
 							
 							tdClass ='even';
 							
 							
 							$.each(alcances,function(id,val){
 	
 						
 					       tdClass = (tdClass == "even") ? "odd" : "even";
 						   var tbody = $('#tablaAlcancesPcuenta')[0];	
 						   var fila = document.createElement("tr");
 						   fila.className=tdClass;	

 						   var columna1 = document.createElement("td");
 							columna1.setAttribute("valign","top");
 							columna1.setAttribute("style","font-size:10px");
 							columna1.appendChild(document.createTextNode(num));
 							
 							var columna2 = document.createElement("td");
 							columna2.setAttribute("valign","top");
 							columna2.setAttribute("style","font-size:10px");
 							columna2.appendChild(document.createTextNode(val));
 											
 						

 						  // alert(val.cargoDependencia);
 							
 							fila.appendChild(columna1);	
 							fila.appendChild(columna2);
 			
 							tbody.appendChild(fila);
 							num++;
 							});
 							
 							
 						}else{
 							
 							$('#tablaAlcancesPcuenta').children('tr').remove();
 							
 							$('#noTablaAlcancesPcuenta').show();

 							
 						}
 	
 						
 						

 						


 				       });
 				}

 		  });
 		
 		
 	
 	}
     
     
     function formato_num(obj){

    	   monto = obj.val();
    	   

    	   
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
    			array = monto.toString().split('').reverse();
    			
    			 if (array[0] == '.'){
    				 
    			 array[0] = ',';
    				 
    			 }
    			
    			 
    			 
    			 monto = array.reverse().join('');
    			 
    			array =  monto.split(",");
    			
    			if(array[1] != undefined){
    				
    			   num = format(array[0])+","+ formatcoma(array[1]);
    				
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
