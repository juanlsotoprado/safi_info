<?php
 
/*
echo" devueltos <pre>";
echo print_r($_GLOBALS['SafiRequestVars']['opciones'] , true);
echo"<pre>";


echo" devueltos <pre>";
echo print_r($_GLOBALS['SafiRequestVars']['pctaEnTransito'] , true);
echo"<pre>";
*/ 

?>
<html>
	<head>
		<title>.:SAFI:. Bandeja de Desincorporaci&oacute;n</title>
		<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		<link href="../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript" src="../../js/lib/actb.js"></script>
		
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript" src="../../js/constantes.js"></script>
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<script type="text/javascript">
		var desiPorEnviarOpciones = <?php echo json_encode($_GLOBALS['SafiRequestVars']['opciones'],JSON_FORCE_OBJECT); ?>;
		</script>
		<!-- pop  up -->
		<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/DetalleCompletoDocumento.js';?>" charset="utf-8"></script>
		
		<script type="text/javascript">
		$().ready(function(){

			$("#RevisionesYMemosDesi").click(function() {
				
			    
				if($("#RevisionesYMemosDesi").attr('opcion') == 0 ){

					$("#RevisionesYMemosDesi").html('.:Detalle ers :.');
					$("#RevisionesYMemosDesi").attr('opcion',1);
					
					$("#window13").hide();
			    	$("#window14").show('blind',300);
			    	
			    	

				}else{

					
					$("#RevisionesYMemosDesi").html('.:Revisiones y memos :.');
					$("#RevisionesYMemosDesi").attr('opcion',0);
					
					$("#window14").hide();
			    	$("#window13").show('blind',300);
			    	
			  
				}
				
			});

			//alert(JSON.stringify(desiPorEnviarOpciones));
			
		});


		function SearchDetalleDesi(id,opcion,idcadenaactual){

						//alert("entro");
			
						$.ajax({
						url: "../../acciones/bienes/desincorporacion.php",
							Type: "post",
							dataType: "json",
							data: {
								accion: "SearchDesiDetalle",
								tipoRespuesta: 'json',
								key: id
			
							},
			
							success: function(json){
			
								//alert(JSON.stringify(json));
			
								if(observacionesDoc = json.observacionesDoc){
			
									$('#noDocumentosAsociadosDesi').hide();
									$('#tablaDocumentosAsociadosDesi').children('tr').remove();
									num = 1;
									
									tdClass ='even';
			
									$.each(observacionesDoc,function(id,val){
										
										var fecha = val.fecha; 
								        var cadena1 = fecha.split(' ');
								        var cadena= cadena1[0].split('-');
								        
								        
									
								//		alert(val.perfilNombre+" / "+val.observacion+" / "+cadena[2]+"-"+cadena[1]+"-"+cadena[0]);
							    
							tdClass = (tdClass == "even") ? "odd" : "even";
								    var tbody = $('#tablaDocumentosAsociadosDesi')[0];	
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
									
									$('#tablaDocumentosAsociadosDesi').children('tr').remove();
									$('#noDocumentosAsociadosDesi').show();
									
								}
			
			
								if(revisiones = json.revicionesDoc){
										
										
										$('#noRegistrosRevisionDesi').hide();
										$('#tablaRevisionDesi').children('tr').remove();
										num = 1;
										
										tdClass ='even';
										
										$.each(revisiones,function(id,val){
											
										var fecha = val.fecha;
										
								         var cadena = fecha.split('/');
			
											
											
								      tdClass = (tdClass == "even") ? "odd" : "even";
								 	   var tbody = $('#tablaRevisionDesi')[0];	
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
										$('#tablaRevisionDesi').children('tr').remove();
										$('#noRegistrosRevisionDesi').show();
										
										
									}	
			
			
								$('span[detalle=\'desi\']').html(id);
			
								$('td[detalle=\'fechadesi\']').html(json.fecha_acta);
			
								$('td[detalle=\'observacionesdesi\']').html(json.observaciones);
			
								//alert(JSON.stringify(json.arti_id));
								
								 $('#desiTbody').find('.trCaso').remove();
								
								var tbody = $('#desiTbody')[0];
								$.each(json.arti_id,function(id,val){
			
								//alert(JSON.stringify(val)+" serial:"+json.serial[id]);
								
						
											var fila = document.createElement("tr");
											fila.className='normalNegro trCaso';

									 		//id del articulo
											
									 		var columna1 = document.createElement("td");
									 		columna1.setAttribute("valign","top");
									 		columna1.setAttribute("style","font-size:10px");
									 		columna1.appendChild(document.createTextNode(json.arti_id[id]));

									 		//nombre
											
									 		var columna11 = document.createElement("td");
									 		columna11.setAttribute("valign","top");
									 		columna11.setAttribute("style","font-size:10px");
									 		columna11.appendChild(document.createTextNode(json.nombre[id]));
									
									 		//modelo
								
									 		var columna2 = document.createElement("td");
									 		columna2.setAttribute("valign","top");
									 		columna2.setAttribute("style","font-size:10px");
									 		columna2.appendChild(document.createTextNode(json.modelo[id]));
									 			 		
									 		//id de la marca
											
									 		var columna5 = document.createElement("td");
									 		columna5.setAttribute("valign","top");
									 		columna5.setAttribute("style","font-size:10px");
									 		columna5.appendChild(document.createTextNode(json.bmarc_nombre[id]));
										
									 		//precio
									 		
									 		var columna6 = document.createElement("td");
									 		columna6.setAttribute("valign","top");
									 		columna6.setAttribute("style","font-size:10px");
									 		columna6.appendChild(document.createTextNode(json.precio[id]));
			
											//serial	 		
											
									 		var columna4 = document.createElement("td");
									 		columna4.setAttribute("valign","top");
									 		columna4.setAttribute("style","font-size:10px");
									 		columna4.appendChild(document.createTextNode(json.serial[id]));
									 						

									 		fila.appendChild(columna1);
									 		fila.appendChild(columna11);
											fila.appendChild(columna2);
											fila.appendChild(columna5);
											fila.appendChild(columna6);
											fila.appendChild(columna4);								

											
								 tbody.appendChild(fila);
			
			
								});
			
								}
			
							
			
						  });
						
						
				
			
			$('.opcionesDesicerrar').remove();
			
				if(opcion == 'desiPorEnviarOpciones'){
			
					if(desiPorEnviarOpciones){
			
				    	 $.each(desiPorEnviarOpciones[idcadenaactual], function(index, value) {
				    		 
				   
				    		          var fila = $('#trOpcionesDetallesDesi')[0];	
									  var columna = document.createElement("td");
									    columna.setAttribute("class","opcionesDesicerrar");
									    var alink = document.createElement("a");
										alink.setAttribute("href","#");
			                         alink.setAttribute("idCadenaSigiente",value.id_cadena_hijo);
			                         alink.setAttribute("idOpcion",value.opcion);
			                         alink.setAttribute("id",value.wfop_descrip);
			                         alink.setAttribute("onclick",'AccionesDesi(this)');
			                         alink.appendChild(document.createTextNode(value.wfop_nombre));
			
								
			                      columna.appendChild(alink);	
			                      fila.appendChild(columna);	
			
			
				    		});
			
				      }
				      
				
			 } 
			
			 		  $('#OpcionesPdfDesi').hover(function(){
				    		    $(this).css({
					    		    
				    		    	'margin-top':-7,
				    		    	'cursor':'move'
				
				    		    });
			
			 		    }).mouseleave(function(){
			
				    		    $(this).css({
			
						    		  'margin-top':-9
			
			
			 		    });
			
			
				    	});                   
			
			
				   
			
				$('#OpcionesPdfDesi').click(function(event) {
					
					 url = "../../vistas/bienes/desincorporacion/PDFDesincorporacion.php?id="+$('span[detalle=\'desi\']').html()+"";
			  	   // window.location = url;
			  	  window.open(url, '_blank'); 
			
					 });
				
			
			}
		

		function AccionesDesi(obj){
			  obj =  $(obj);
			  
			  var url = false ;

		     //alert(obj.attr('id'));
		 	  
			  if(obj.attr('id') == 'Modificar'){

				  

				  //url = "../../recursos/resp_social/acciones/respsocial.php?accion=Modificar";
				  //url = "../../acciones/bienes/desincorporacion.php?accion=ProcesarDesi&desi="+$('span[detalle=\'desi\']').html()+"&accRealizar="+obj.attr('id')+"";
				  url = "../../acciones/bienes/desincorporacion.php?desi="+$('span[detalle=\'desi\']').html()+"&accion="+obj.attr('id')+"";
				  window.location = url; 
		      
		    }else if(obj.attr('id') == 'Modificar2'){
				
		    	url = "../../acciones/bienes/desincorporacion.php?desi="+$('span[detalle=\'desi\']').html()+"&accion="+obj.attr('id')+"";
				  window.location = url;

			}else{
		        
		    	 if(confirm("\u00BFEst\u00E1 seguro que desea ("+obj.attr('id')+") esta acta?")){

		    	 if(obj.attr('id') == 'Anular' || obj.attr('id') == 'Devolver'){

		         var lugar = 'Desincorporacion';
		    	 var	memo =  LlenarMemo(obj.attr('id'),lugar);

		    	 
		    	 

		    	 
		    	if(memo){
		    	 
		         url = "../../acciones/bienes/desincorporacion.php?accion=ProcesarDesi&opcion=0&memo="+memo+"&desi="+$('span[detalle=\'desi\']').html()+"&idCadenaSigiente="+obj.attr('idCadenaSigiente')+"&idopcion="+obj.attr('idopcion')+"&accRealizar="+obj.attr('id')+"";
		    	}
		         
		    	 }else{
		    		 
		    		 
			         url = "../../acciones/bienes/desincorporacion.php?accion=ProcesarDesi&desi="+$('span[detalle=\'desi\']').html()+"&idCadenaSigiente="+obj.attr('idCadenaSigiente')+"&idopcion="+obj.attr('idopcion')+"&accRealizar="+obj.attr('id')+"";	   

		    	 }	 
		    	 
		    	if(url){
		    		
		         window.location = url;
		    		
		    	}

						 }else{
							 
							return;
							 
							 }

		  	  
		        }

		 }

		function validarcaracterespecial(campo) {

		    var price = campo;
		    
		    var intRegex = /^[^'"@?¿·ª~!\¿?=)]*$/;

		    campo2 = trimDato(campo);
		 
		if ((price.match(intRegex)) && (campo2 != false)) {
		   
		        return 1;
		        
		} else {
		        
				alert("El memo no debe contener caracteres especiales");
		        return false;
		} 


		}

		function  LlenarMemo(obj,lugar){
		    
		    if(memo=prompt("Especifique un motivo por el cual desea "+obj+" esta "+lugar)){

		    	if(validarcaracterespecial(memo) == 1)
		        {
		    		return memo;
		        } 
				  
				    }else{

				       if (memo == null)
				       {
				    	   if(confirm("¿Est\u00E1 seguro que desea (cancelar) la operaci\u00F3n ?")){    
				    		   
				    		  return false; 
		    			
				    	   }else{
				    		   
				    		return  LlenarMemo(obj,lugar);  
				    		   
				    	   }
				    	   
				       }else{

				    	   if(confirm("¿El motivo por el cual desea "+obj+" esta "+lugar+" est\u00E1 vac\u00EDo. ¿Desea (cancelar) la operaci\u00F3n.?")){    
				    		   
					    		  return false; 
			      			
					    	   }else{
					    		   
					    		return  LlenarMemo(obj,lugar);  
					    		   
					    	   } 
				          }

				      }
		    
		     
		        }

		
		</script>
	</head>
	<body class="normal">
	<?php include(SAFI_VISTA_PATH . '/detalleCompletoDocumeto.php'); ?>
	<?php include(SAFI_VISTA_PATH . '/mensajes.php');?>
				
		<!-- Documentos por enviar -->
		
		<?php if($_GLOBALS['SafiRequestVars']['pctaPorEnviar']){ ?>
		
		<table style="width: 100%;">
		<tr>
			<td style="text-align: center;" class="normalNegroNegrita"><span
				style="padding-bottom: 20px; display: block;">Documentos por Enviar/Aprobar/Finalizar</span>
			</td>
		</tr>
		<tr>
			<td><table cellpadding="0" cellspacing="0" align="center"
					style="width: 100%;" background="../../imagenes/fondo_tabla.gif"
					class="tablaalertas">
					<tr>
						<th width="5%" class="header"><span class="normalNegroNegrita">#</span></th>
						<th width="15%" class="header"><span class="normalNegroNegrita">C&oacute;digo</span></th>
						<th width="15%" class="header"><span class="normalNegroNegrita">Fecha</span></th>
						<th width="54%" class="header"><span class="normalNegroNegrita">Observaciones</span></th>
						<td width="10%" class="header"><span class="normalNegroNegrita">Opciones</span></td>
					</tr>


					<?php
					$tdClass ='even';
					$i = 1;

					if($_GLOBALS['SafiRequestVars']['pctaPorEnviar']){
						foreach ($_GLOBALS['SafiRequestVars']['pctaPorEnviar'] as $index ){

							$tdClass = ($tdClass == "even") ? "odd" : "even";

					?>


					<tr onclick="Registroclikeado(this);" class="<?php echo $tdClass;?>">
						<td style="font-weight: bold;"><?php echo $i ?></td>
						<td>
							<a href="#dialog" docgId="<?php echo $index['docg_id']; ?>"
							class="detalleOpcion" opcion="null"  tipoDetalle="desincorporacion"> <?php echo $index['docg_id'] ?>
							</a>
						</td>
						<td><?php echo $index['docg_fecha']; ?></td>
						<td>
						<?php echo $index['observaciones']; ?>
						</td>
						<td><script type="text/javascript">
					var pctaPorEnviarOpciones = <?php echo json_encode($GLOBALS['SafiRequestVars']['opciones'],JSON_FORCE_OBJECT); ?>;


					
					
				 </script> <a style="margin-right: 5px;" href="#dialog"
							class="detalleOpcion" opcion="desiPorEnviarOpciones"
							docgId="<?php echo $index['docg_id']; ?> "
							idCadenaActual="<?php echo $index['wfca_id'] ?>" tipoDetalle="desincorporacion">Seleccionar</a>
						</td>
					</tr>


					<?php $i++; }}else{ ?>

					<tr class="odd" onclick='Registroclikeado(this)'>
						<td colspan="7">No se han encontrado registros</td>

					</tr>


					<?php } ?>


				</table>
			</td>
		</tr>
	</table>
	
	<!-- Documentos por devueltos -->
	
	<br />
	<br />
	<?php } if($_GLOBALS['SafiRequestVars']['pctaDevuelto']){?>
	<table style="width: 100%;">

		<tr>
			<td style="text-align: center;" class="normalNegroNegrita"><span
				style="padding-bottom: 20px; display: block;">Documentos Devueltos</span>
			</td>
		</tr>
	<tr>
			<td><table cellpadding="0" cellspacing="0" align="center"
					style="width: 100%;" background="../../imagenes/fondo_tabla.gif"
					class="tablaalertas">
					<tr>
						<th width="5%" class="header"><span class="normalNegroNegrita">#</span></th>
						<th width="15%" class="header"><span class="normalNegroNegrita">C&oacute;digo</span></th>
						<th width="15%" class="header"><span class="normalNegroNegrita">Fecha</span></th>
						<th width="54%" class="header"><span class="normalNegroNegrita">Observaciones</span></th>
						<th width="10%" class="header"><span class="normalNegroNegrita">Opciones</span></th>
					</tr>


					<?php
					$tdClass ='even';
					$i = 1;

					if($_GLOBALS['SafiRequestVars']['pctaDevuelto']){
						foreach ($_GLOBALS['SafiRequestVars']['pctaDevuelto'] as $index ){

							$tdClass = ($tdClass == "even") ? "odd" : "even";


							?>
		     <tr onclick="Registroclikeado(this);"
						class="<?php echo $tdClass;?>">

						<td style="font-weight: bold;"><?php echo $i ?></td>
						<td><a href="#dialog"  docgId="<?php echo $index['docg_id']; ?>"
							class="detalleOpcion" opcion="null"  tipoDetalle="desincorporacion"> <?php echo $index['docg_id'] ?>
						</a>
						</td>
						<td><?php echo $index['docg_fecha'] ?></td>
						<td><?php echo $index['observaciones'];?></td>

						<td><script type="text/javascript">
					var pctaPorEnviarOpciones = <?php echo json_encode($GLOBALS['SafiRequestVars']['opciones'],JSON_FORCE_OBJECT); ?>;


					
					
				 </script> <a style="margin-right: 5px;" href="#dialog"
							class="detalleOpcion" opcion="desiPorEnviarOpciones"
							docgId="<?php echo $index['docg_id']; ?> "
							idCadenaActual="<?php echo $index['wfca_id'] ?>" tipoDetalle="desincorporacion">Seleccionar</a>
						</td>
					</tr>


					<?php $i++; }}else{ ?>

					<tr class="odd" onclick='Registroclikeado(this)'>
						<td colspan="7">No se han encontrado registros</td>

					</tr>



					<?php } ?>


				</table>
			</td>
		</tr>
	</table>
	
		<!-- Documentos por enviados a sudebib -->
	
	<br />
	<br />
	<?php } if($_GLOBALS['SafiRequestVars']['enviadoSudebip']){?>
	<table style="width: 100%;">

		<tr>
			<td style="text-align: center;" class="normalNegroNegrita"><span
				style="padding-bottom: 20px; display: block;">Documentos Enviados a Sudebip</span>
			</td>
		</tr>
	<tr>
			<td><table cellpadding="0" cellspacing="0" align="center"
					style="width: 100%;" background="../../imagenes/fondo_tabla.gif"
					class="tablaalertas">
					<tr>
						<th width="5%" class="header"><span class="normalNegroNegrita">#</span></th>
						<th width="15%" class="header"><span class="normalNegroNegrita">C&oacute;digo</span></th>
						<th width="15%" class="header"><span class="normalNegroNegrita">Fecha</span></th>
						<th width="54%" class="header"><span class="normalNegroNegrita">Observaciones</span></th>
						<th width="10%" class="header"><span class="normalNegroNegrita">Opciones</span></th>
					</tr>


					<?php
					$tdClass ='even';
					$i = 1;

					if($_GLOBALS['SafiRequestVars']['enviadoSudebip']){
						foreach ($_GLOBALS['SafiRequestVars']['enviadoSudebip'] as $index ){

							$tdClass = ($tdClass == "even") ? "odd" : "even";


							?>
		     <tr onclick="Registroclikeado(this);"
						class="<?php echo $tdClass;?>">

						<td style="font-weight: bold;"><?php echo $i ?></td>
						<td><a href="#dialog"  docgId="<?php echo $index['docg_id']; ?>"
							class="detalleOpcion" opcion="null"  tipoDetalle="desincorporacion"> <?php echo $index['docg_id'] ?>
						</a>
						</td>
						<td><?php echo $index['docg_fecha'] ?></td>
						<td><?php echo $index['observaciones'];?></td>

						<td><script type="text/javascript">
					var pctaPorEnviarOpciones = <?php echo json_encode($GLOBALS['SafiRequestVars']['opciones'],JSON_FORCE_OBJECT); ?>;


					
					
				 </script> <a style="margin-right: 5px;" href="#dialog"
							class="detalleOpcion" opcion="desiPorEnviarOpciones"
							docgId="<?php echo $index['docg_id']; ?> "
							idCadenaActual="<?php echo $index['wfca_id'] ?>" tipoDetalle="desincorporacion">Seleccionar</a>
						</td>
					</tr>


					<?php $i++; }}else{ ?>

					<tr class="odd" onclick='Registroclikeado(this)'>
						<td colspan="7">No se han encontrado registros</td>

					</tr>



					<?php } ?>


				</table>
			</td>
		</tr>
	</table>
	
	<!-- Documentos en transito -->
	<br />
	<br />
		<?php } if($_GLOBALS['SafiRequestVars']['pctaEnTransito']){

		?>

	<table style="width: 100%;">

		<tr>
			<td style="text-align: center;" class="normalNegroNegrita"><span
				style="padding-bottom: 20px; display: block;">Documentos Tr&aacute;nsito</span>
			</td>
		</tr>
		<tr>
			<td><table cellpadding="0" cellspacing="0" align="center"
					style="width: 100%;" background="../../imagenes/fondo_tabla.gif"
					class="tablaalertas">
					<tr>
						<th width="5%"  class="header"><span class="normalNegroNegrita">#</span>
						</th>
						</th>
						<th width="15%"  class="header"><span class="normalNegroNegrita">C&oacute;digo</span>
						</th>
						<th width="15%"  class="header"><span class="normalNegroNegrita">Fecha</span>
						<th width="40%"  class="header"><span class="normalNegroNegrita">Observaciones</span>
						</th>
						<th  class="header"><span class="normalNegroNegrita">Instancia
								actual</span>
						</th>
					</tr>


					<?php
					$tdClass ='even';
					$i = 1;

					if($_GLOBALS['SafiRequestVars']['pctaEnTransito']){
						foreach ($_GLOBALS['SafiRequestVars']['pctaEnTransito'] as $index ){

							$tdClass = ($tdClass == "even") ? "odd" : "even";


							?>


					<tr onclick="Registroclikeado(this);"
						class="<?php echo $tdClass;?>">

						<td style="font-weight: bold;"><?php echo $i ?></td>
							<td><a href="#dialog" docgId="<?php echo $index['docg_id']; ?>"
							class="detalleOpcion" opcion="null"  tipoDetalle="desincorporacion"> <?php echo $index['docg_id'] ?>
						</a>
						</td>
						<td><?php echo $index['docg_fecha'] ?></td><!-- fecha -->
						<td><?php echo $index['observaciones']; ?></td>

						<td><?php echo $index['perf_id_act'] ?></td>

					</tr>


					<?php $i++; }}else{ ?>

					<tr class="odd" onclick='Registroclikeado(this)'>
						<td colspan="7">No se han encontrado registros</td>

					</tr>


					<?php } ?>


				</table>
			</td>
		</tr>
	</table>

	<?php } ?>	

	</body>
</html>