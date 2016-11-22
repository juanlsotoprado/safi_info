<?php 
  ob_start();
  session_start();
  require_once("../../includes/conexion.php");
  include(dirname(__FILE__) . '/../../init.php');
  require ("../../includes/perfiles/constantesPerfiles.php");
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../index.php',false);
   ob_end_flush(); 
   exit;
  }
  ob_end_flush(); 
  
  //Modelos
  
  include(SAFI_MODELO_PATH. '/estado.php');
  require_once(SAFI_MODELO_PATH. '/dependencia.php');
  require_once(SAFI_MODELO_PATH. '/empleado.php');
  require_once(SAFI_MODELO_PATH. '/general.php');
  require_once(SAFI_MODELO_PATH. '/docgenera.php');
  require_once(SAFI_MODELO_PATH. '/estatus.php');
  require_once(SAFI_MODELO_PATH. '/observacionesDoc.php');
  require_once(SAFI_MODELO_PATH. '/wfgrupo.php');
  require_once(SAFI_MODELO_PATH. '/wfopcion.php');
  require_once(SAFI_MODELO_PATH. '/wfcadena.php');
  require_once(SAFI_MODELO_PATH. '/revisionesDoc.php');
  require_once(SAFI_MODELO_PATH. '/cargo.php');
  
  

 function  FormatBandeja($valor){
  
  	if($valor){
  		$i = 0;
  
  		foreach ($valor as $index){
  
  				
  				
  			$fechahora = explode (' ',$index['docg_fecha']);
  
  			$fecha = explode ('-',$fechahora[0]);
  
  			$fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' '.$fechahora[1];
  				
  			$valor[$i]['docg_fecha'] = $fecha2;
  
  			$perf_id_act = SafiModeloWFGrupo::GetWFGrupoByIdPerfil($index['perf_id_act']);
  
  			$valor[$i]['perf_id_act'] = $perf_id_act->GetDescripcion();
  				
  			if($valor[$i]['wfob_id_ini'] == 1 ){
  
  				$_SESSION['SafiRequestVars']['pctaDevuelto'][] = $valor[$i];
  
  				unset($valor[$i]);
  
  			}
  
  			$i++;
  
  
  		}
  
  	}
  
  	return $valor;
  	 
  }
  
  function  FormatBandejaTransito($valor){

  
  	if($valor){
  		$i = 0;
  
  		foreach ($valor as $index){
  
  
  
  			$fechahora = explode (' ',$index['docg_fecha']);
  
  			$fecha = explode ('-',$fechahora[0]);
  
  			$fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' '.$fechahora[1];
  
  			$valor[$i]['docg_fecha'] = $fecha2;
  			
  			
  			
  			$id_grupo = SafiModeloWFCadena::GetCadenaIdGrupo($index['wfca_id']);

  			$perf_id_act = SafiModeloWFGrupo::GetWFGrupoByIdPerfilResSocial($id_grupo);
  			
  			$valor[$i]['perf_id_act'] = $perf_id_act->GetDescripcion();
  
  			$i++;
  
  
  		}
  
  	}
  
  	return $valor;
  
  }
  
  
  
  $params =array();
  $Idcadena =array();
  $lugar = 'ers';
  
  $_GLOBALS['SafiRequestVars']['pctaPorEnviar'] = SafiModeloDocGenera::GetRegistrosEnBandeja($lugar);
  
  

  if($_GLOBALS['SafiRequestVars']['pctaPorEnviar']){
  
  	foreach ($_GLOBALS['SafiRequestVars']['pctaPorEnviar'] as $index ){
  			
  		$Idcadena[$index['wfca_id']]= $index['wfca_id'];
  			
  	}
  }
  

  $_GLOBALS['SafiRequestVars']['opciones'] = SafiModeloWFCadena::GetId_cadena_hijos_id_cadenas($Idcadena);
  

  $_GLOBALS['SafiRequestVars']['pctaEnTransito'] =  SafiModeloDocGenera::GetRegistrosEnTransitoRespSocial($lugar);

  
  if((!$_GLOBALS['SafiRequestVars']['pctaPorEnviar']) && (!$_GLOBALS['SafiRequestVars']['pctaEnTransito']) && ($param == null)){
  
  	if(!$condicion) 
  	{
  		
  		$_GLOBALS['SafiInfo']['general'][] = "No se han encontrado registros";
  
  	}
  
  }else{
		
	  $_GLOBALS['SafiRequestVars']['pctaPorEnviar'] =  FormatBandeja($_GLOBALS['SafiRequestVars']['pctaPorEnviar']);		
	  
	  $_GLOBALS['SafiRequestVars']['pctaEnTransito'] =  FormatBandejaTransito($_GLOBALS['SafiRequestVars']['pctaEnTransito']);
	  
	  $_GLOBALS['SafiRequestVars']['pctaDevuelto'] = $_SESSION['SafiRequestVars']['pctaDevuelto'];
	  
	  unset($_SESSION['SafiRequestVars']['pctaDevuelto']);
		
   }
   
   /*echo" devueltos <pre>";
   echo print_r($_GLOBALS['SafiRequestVars']['opciones'] , true);
   echo"<pre>";*/

/*  
 echo" devueltos <pre>";
  echo print_r($_GLOBALS['SafiRequestVars']['pctaEnTransito'] , true);
  echo"<pre>"; */

 ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>


<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />
<link href="<?=GetConfig("siteURL").'/css/safi0.2.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />

<link rel="stylesheet" href="../../css/plantilla.css" type="text/css"
	media="all" />
<title>SAFI::Responsabilidad Social:.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css"
	media="screen" rel="stylesheet" />
<script type="text/javascript"
	src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript"
	src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript"
	src="../../js/lib/calendarPopup/js/dateparse.js"></script>

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"
	charset="utf-8"></script>

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/jquery-ui-1.8.13.custom.min.js';?>"
	charset="utf-8"></script>
<link
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />

<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');
</script>
<!-- pop  up -->
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/DetalleCompletoDocumento.js';?>"
	charset="utf-8"></script>
	
<script type="text/javascript">
var ersPorEnviarOpciones = <?php echo json_encode($_GLOBALS['SafiRequestVars']['opciones'],JSON_FORCE_OBJECT); ?>;
</script>
<script type="text/javascript">
$().ready(function(){
		$("#pedro").show("drop",{ direction: "left" }, 300);

	$("#bsalida").click(function() {


		$("#pedro").hide("drop",300,function(){

			window.location = "rsbandejasalida.php";
			
		  });
				
			

		});
			
	
$("#RevisionesYMemosErs").click(function() {
	
    
	if($("#RevisionesYMemosErs").attr('opcion') == 0 ){

		$("#RevisionesYMemosErs").html('.:Detalle ers :.');
		$("#RevisionesYMemosErs").attr('opcion',1);
		
		$("#window5").hide();
    	$("#window6").show('blind',300);
    	
    	

	}else{

		
		$("#RevisionesYMemosErs").html('.:Revisiones y memos :.');
		$("#RevisionesYMemosErs").attr('opcion',0);
		
		$("#window6").hide();
    	$("#window5").show('blind',300);
    	
  
	}
	
});

});

function SearchDetalleErs(id,opcion,idcadenaactual){

			$.ajax({
			url: "../../recursos/resp_social/acciones/respsocial.php",
				Type: "post",
				dataType: "json",
				data: {
					accion: "SearchErsDetalle",
					tipoRespuesta: 'json',
					key: id

				},

				
				success: function(json){

					//alert(JSON.stringify(json));

					if(observacionesDoc = json.observacionesDoc){

						$('#noDocumentosAsociadosErs').hide();
						$('#tablaDocumentosAsociadosErs').children('tr').remove();
						num = 1;
						
						tdClass ='even';

						$.each(observacionesDoc,function(id,val){
							
							var fecha = val.fecha; 
					        var cadena1 = fecha.split(' ');
					        var cadena= cadena1[0].split('-');
					        
					        
						
					//		alert(val.perfilNombre+" / "+val.observacion+" / "+cadena[2]+"-"+cadena[1]+"-"+cadena[0]);
				    
				tdClass = (tdClass == "even") ? "odd" : "even";
					    var tbody = $('#tablaDocumentosAsociadosErs')[0];	
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
						
						$('#tablaDocumentosAsociadosErs').children('tr').remove();
						$('#noDocumentosAsociadosErs').show();
						
					}


					if(revisiones = json.revicionesDoc){
							
							
							
							$('#noRegistrosRevisionErs').hide();
							$('#tablaRevisionErs').children('tr').remove();
							num = 1;
							
							tdClass ='even';
							
							$.each(revisiones,function(id,val){
								
							var fecha = val.fecha;
							
					         var cadena = fecha.split('/');

								
								
					      tdClass = (tdClass == "even") ? "odd" : "even";
					 	   var tbody = $('#tablaRevisionErs')[0];	
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
							$('#tablaRevisionErs').children('tr').remove();
							$('#noRegistrosRevisionErs').show();
							
							
						}	


					 $('span[detalle=\'ers\']').html(id);

					$('td[detalle=\'rsfecha\']').html(json.fecha_recepcion);

					$('td[detalle=\'rsubicacion\']').html(json.ubicacion);

					$('td[detalle=\'rsmonto\']').html(json.monto_recibido);

					$('td[detalle=\'rsproveedor\']').html(json.prov_nombre);

					$('td[detalle=\'rsobservacion\']').html(json.observaciones);

					//alert(JSON.stringify(json.fecha));
					
					 $('#ersTbody').find('.trCaso').remove();
					
					var tbody = $('#ersTbody')[0];
					$.each(json.idarticulo,function(id,val){

					//alert(JSON.stringify(val)+" serial:"+json.serial[id]);
					
			
								var fila = document.createElement("tr");
								fila.className='normalNegro trCaso';
						
						 		//nombre del articulo
					
						 		var columna2 = document.createElement("td");
						 		columna2.setAttribute("valign","top");
						 		columna2.setAttribute("style","font-size:10px");
						 		columna2.appendChild(document.createTextNode(json.nombre[id]));
						 			 		
						 		//cantidad
								
						 		var columna5 = document.createElement("td");
						 		columna5.setAttribute("valign","top");
						 		columna5.setAttribute("style","font-size:10px");
						 		columna5.appendChild(document.createTextNode(json.cantidad[id]));
							
						 		//marca
						 		
						 		var columna6 = document.createElement("td");
						 		columna6.setAttribute("valign","top");
						 		columna6.setAttribute("style","font-size:10px");
						 		columna6.appendChild(document.createTextNode(json.marca_id[id]));

								//modelo	 		
								
						 		var columna4 = document.createElement("td");
						 		columna4.setAttribute("valign","top");
						 		columna4.setAttribute("style","font-size:10px");
						 		columna4.appendChild(document.createTextNode(json.modelo[id]));
								
				
								//input hidden para el serial
								
								var columna9 = document.createElement("td");
								columna9.setAttribute("valign","top");
								columna9.setAttribute("style","font-size:10px");
								columna9.appendChild(document.createTextNode(json.serial[id]));
									

								fila.appendChild(columna2);
								fila.appendChild(columna5);
								fila.appendChild(columna6);
								fila.appendChild(columna4);
								fila.appendChild(columna9);	
									
								

			

					 tbody.appendChild(fila);


					});

					}

				

			  });
			
			
	

$('.opcionesErscerrar').remove();

	if(opcion == 'ersPorEnviarOpciones'){

		if(ersPorEnviarOpciones){

	    	 $.each(ersPorEnviarOpciones[idcadenaactual], function(index, value) {
	    		 
	   
	    		          var fila = $('#trOpcionesDetallesErs')[0];	
						  var columna = document.createElement("td");
						    columna.setAttribute("class","opcionesErscerrar");
						    var alink = document.createElement("a");
							alink.setAttribute("href","#");
                         alink.setAttribute("idCadenaSigiente",value.id_cadena_hijo);
                         alink.setAttribute("idOpcion",value.opcion);
                         alink.setAttribute("id",value.wfop_descrip);
                         alink.setAttribute("onclick",'AccionesErs(this)');
                         alink.appendChild(document.createTextNode(value.wfop_nombre));


                      columna.appendChild(alink);	
                      fila.appendChild(columna);	


	    		});

	      }
	      
	
 } 

 		  $('#OpcionesPdfErs').hover(function(){
	    		    $(this).css({
		    		    
	    		    	'margin-top':-7,
	    		    	'cursor':'move'
	
	    		    });

 		    }).mouseleave(function(){

	    		    $(this).css({

			    		  'margin-top':-9


 		    });


	    	});                   


	   

	$('#OpcionesPdfErs').click(function(event) {
		
		 url = "entradas_rs_pdf.php?id="+$('span[detalle=\'ers\']').html()+"";
  	   // window.location = url;
  	  window.open(url, '_blank'); 

		 });
	

}

function AccionesErs(obj){
	  obj =  $(obj);
	  
	  var url = false ;

     //alert(obj.attr('id'));
 	  
	  if(obj.attr('id') == 'Modificar'){

		  //url = "../../recursos/resp_social/acciones/respsocial.php?accion=Modificar";
		  url = "../../recursos/resp_social/modificar_respsocial.php?ers="+$('span[detalle=\'ers\']').html()+"";
		  window.location = url; 
      
    }else{
        
    	 if(confirm("\u00BFEst\u00E1 seguro que desea ("+obj.attr('id')+") este 'ers'?")){

    	 if(obj.attr('id') == 'Anular' || obj.attr('id') == 'Devolver'){

         var lugar = 'responsabilidad social';
    	 var	memo =  LlenarMemo(obj.attr('id'),lugar);

    	 
    	 

    	 
    	if(memo){
    	 
         url = "../../recursos/resp_social/acciones/respsocial.php?accion=ProcesarErs&opcion=0&memo="+memo+"&ers="+$('span[detalle=\'ers\']').html()+"&idCadenaSigiente="+obj.attr('idCadenaSigiente')+"&idopcion="+obj.attr('idopcion')+"&accRealizar="+obj.attr('id')+"";
    	}
         
    	 }else{
    		 
    		 
	         url = "../../recursos/resp_social/acciones/respsocial.php?accion=ProcesarErs&ers="+$('span[detalle=\'ers\']').html()+"&idCadenaSigiente="+obj.attr('idCadenaSigiente')+"&idopcion="+obj.attr('idopcion')+"&accRealizar="+obj.attr('id')+"";	   

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
<body>

<?php include(SAFI_VISTA_PATH . '/detalleCompletoDocumeto.php'); ?>
<div id="pedro" style="display: none;">
<?php 
if ($_SESSION ['user_perfil_id'] != PERFIL_ALMACENISTA) {
?>
	<table align="right">
			<tr>
			<td style="text-align: center;" class="normalNegroNegrita">
		<a href="javascript:void(0);" id="bsalida" >.:Bandeja de salida:.</a>
			</td>
		</tr>
	</table>
<?php 
}
include(SAFI_VISTA_PATH . '/mensajes.php'); if($_GLOBALS['SafiRequestVars']['pctaPorEnviar']){ ?>

	<table style="width: 100%;">
		<tr>
			<td style="text-align: center;" class="normalNegroNegrita"><span
				style="padding-bottom: 20px; display: block;">Documentos de Responsabilidad Social por Enviar/Aprobar/Finalizar</span>
			</td>
		</tr>
		<tr>
			<td><table cellpadding="0" cellspacing="0" align="center"
					style="width: 100%;" background="../../imagenes/fondo_tabla.gif"
					class="tablaalertas">
					<tr>
						<th width="5%" class="header"><span class="normalNegroNegrita">#</span>
						</th>
						</th>
						<th width="15%" class="header"><span class="normalNegroNegrita">C&oacute;digo</span>
						</th>
						<th width="15%" class="header"><span class="normalNegroNegrita">Fecha</span>
						<th width="54%" class="header"><span class="normalNegroNegrita">Observaciones</span>
						</th>
						<td width="10%" class="header"><span class="normalNegroNegrita">Opciones</span>
						
						</th>
					</tr>


					<?php
					$tdClass ='even';
					$i = 1;

					if($_GLOBALS['SafiRequestVars']['pctaPorEnviar']){
						foreach ($_GLOBALS['SafiRequestVars']['pctaPorEnviar'] as $index ){

							$tdClass = ($tdClass == "even") ? "odd" : "even";

							?>


					<tr onclick="Registroclikeado(this);"
						class="<?php echo $tdClass;?>">

						<td style="font-weight: bold;"><?php echo $i ?></td>
						<td><a href="#dialog" docgId="<?php echo $index['docg_id']; ?>"
							class="detalleOpcion" opcion="null"  tipoDetalle="respsocial"> <?php echo $index['docg_id'] ?>
						</a>
						</td>
						<td><?php echo $index['docg_fecha'] ?></td>

						<td>
						<?php //echo $index['f030_motivo'] 
						$queryobser=
						"
  						select
  							observaciones
  						from
  							sai_arti_inco_rs
  						where
  							acta_id='".$index['docg_id']."'
  						";
						$resul=pg_exec ( $conexion, $queryobser );
						$row = pg_fetch_array ( $resul );
						$obser=$row['observaciones'];
						echo $obser;
						?></td>
						<td><script type="text/javascript">
					var pctaPorEnviarOpciones = <?php echo json_encode($GLOBALS['SafiRequestVars']['opciones'],JSON_FORCE_OBJECT); ?>;


					
					
				 </script> <a style="margin-right: 5px;" href="#dialog"
							class="detalleOpcion" opcion="ersPorEnviarOpciones"
							docgId="<?php echo $index['docg_id']; ?> "
							idCadenaActual="<?php echo $index['wfca_id'] ?>" tipoDetalle="respsocial">Seleccionar</a>
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

	<br />

	<br />
	<?php } if($_GLOBALS['SafiRequestVars']['pctaDevuelto']){?>
	<table style="width: 100%;">

		<tr>
			<td style="text-align: center;" class="normalNegroNegrita"><span
				style="padding-bottom: 20px; display: block;">Documentos de Responsabilidad Social Devueltos</span>
			</td>
		</tr>
	<tr>
			<td><table cellpadding="0" cellspacing="0" align="center"
					style="width: 100%;" background="../../imagenes/fondo_tabla.gif"
					class="tablaalertas">
					<tr>
						<th width="5%" class="header"><span class="normalNegroNegrita">#</span>
						</th>
						</th>
						<th width="15%" class="header"><span class="normalNegroNegrita">C&oacute;digo</span>
						</th>
						<th width="15%" class="header"><span class="normalNegroNegrita">Fecha</span>
						<th width="54%" class="header"><span class="normalNegroNegrita">Observaciones</span>
						</th>
						<td width="10%" class="header"><span class="normalNegroNegrita">Opciones</span>
						
						</td>
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
							class="detalleOpcion" opcion="null"  tipoDetalle="respsocial"> <?php echo $index['docg_id'] ?>
						</a>
						</td>
						<td><?php echo $index['docg_fecha'] ?></td>

						<td>
						<?php //echo $index['f030_motivo'] 
						$queryobser=
						"
  						select
  							observaciones
  						from
  							sai_arti_inco_rs
  						where
  							acta_id='".$index['docg_id']."'	
  						";	
						$resul=pg_exec ( $conexion, $queryobser );
						$row = pg_fetch_array ( $resul );
						$obser=$row['observaciones'];
						echo $obser;
						?>
						</td>
						<td><script type="text/javascript">
					var pctaPorEnviarOpciones = <?php echo json_encode($GLOBALS['SafiRequestVars']['opciones'],JSON_FORCE_OBJECT); ?>;


					
					
				 </script> <a style="margin-right: 5px;" href="#dialog"
							class="detalleOpcion" opcion="ersPorEnviarOpciones"
							docgId="<?php echo $index['docg_id']; ?> "
							idCadenaActual="<?php echo $index['wfca_id'] ?>" tipoDetalle="respsocial">Seleccionar</a>
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

	<br />
	<br />
		<?php }

	if($_GLOBALS['SafiRequestVars']['pctaEnTransito']){

		?>

	<table style="width: 100%;">

		<tr>
			<td style="text-align: center;" class="normalNegroNegrita"><span
				style="padding-bottom: 20px; display: block;">Documentos de Responsabilidad Social en
					Tr&aacute;nsito</span>
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
							class="detalleOpcion" opcion="null"  tipoDetalle="respsocial"> <?php echo $index['docg_id'] ?>
						</a>
						</td>
						<td><?php echo $index['docg_fecha'] ?></td><!-- fecha -->

						<td>
						<?php //echo $index['f030_motivo'] 
						$queryobser=
						"
  						select
  							observaciones
  						from
  							sai_arti_inco_rs
  						where
  							acta_id='".$index['docg_id']."'
  						";
						$resul=pg_exec ( $conexion, $queryobser );
						$row = pg_fetch_array ( $resul );
						$obser=$row['observaciones'];
						echo $obser;
						?></td>
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
	</div>
</body>
</html>
