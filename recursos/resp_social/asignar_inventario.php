<?
ob_start ();
require_once ("../../includes/conexion.php");
include (dirname ( __FILE__ ) . '/../../init.php');
require ("../../includes/perfiles/constantesPerfiles.php");

if (empty ( $_SESSION ['login'] ) || ($_SESSION ['registrado'] != "registrado")) {
	header ( 'Location:../../index.php', false );
	ob_end_flush ();
	exit ();
}
ob_end_flush ();

/////////////////////////////////////////////////////////////////////////////////////////  Cadena de Salida resp social ///////////////////////

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

$lugar = 'ne';
$id_cadena_inicial = SafiModeloWFCadena::GetWfca_id_inicial($lugar);
$GLOBALS['SafiRequestVars']['idCadenaActual'] = $id_cadena_inicial;

$id_HijosCadena = SafiModeloWFCadena::GetId_cadena_hijo_id_cadena($id_cadena_inicial);
$GLOBALS['SafiRequestVars']['opciones'] = $id_HijosCadena;


$param['lugar'] = "ne";
$param['ano'] = substr($_SESSION['an_o_presupuesto'],2);
$niveles = array(3,4);
$param['Dependencia']= '';

///////////////////////////////////////////////////////////////////////////////////////  FIN Cadena de entrada resp social FIN ///////////////////////

if ($_SESSION ['user_perfil_id'] == PERFIL_ALMACENISTA) {
	$ubicaciont3 = 2;
	$ubicaciont5 = 2;
	$ubicacionserial = 2;
} else {
	$ubicaciont3 = "2 or t3.ubicacion= 1";
	$ubicaciont5 = "2 or t5.ubicacion= 1";
	$ubicacionserial = "2 or t3.ubicacion= 1";
}
	
$query = "
						SELECT
							tablas.id,
							tablas.nombre,
							tablas.modelo,
							tablas.bmarc_id,
							sum(tablas.cantidad) as disponible,
							tablas.bmarc_nombre,
							tablas.unidad_medida,
							tablas.ubicacion
			
						FROM
						(
								SELECT
								t1.id,
								t1.nombre,
								t2.unidad_medida,
								t3.modelo,
								t4.bmarc_id,
								sum(t3.cantidad) as cantidad,
								t4.bmarc_nombre,
								t3.ubicacion
					
								FROM
								sai_item  t1
								inner join sai_item_articulo t2 on(t1.id=t2.id)
								inner join sai_arti_inco_rs_item t3 on(t1.id=t3.arti_id)
								inner join sai_bien_marca t4 on(t3.marca_id=t4.bmarc_id)
								inner join sai_arti_inco_rs t7 on(t3.acta_id=t7.acta_id)
					
								WHERE
								t7.esta_id!=15 and
								t1.esta_id=1 and
								id_tipo=4 and
								(t3.serial ='' or t3.serial is null) and
								(t3.ubicacion=" . $ubicaciont3 . ")
								" . $condicion . "

								group BY
								t1.id,
								t1.nombre,
								t2.unidad_medida,
								t3.modelo,
								t4.bmarc_id,
								t4.bmarc_nombre,
 								t3.ubicacion
			
						UNION
							SELECT
								t1.id,
								t1.nombre,
								t2.unidad_medida,
								t5.modelo,
								t4.bmarc_id,
								sum(-t5.cantidad) as cantidad,
								t4.bmarc_nombre,
 								t5.ubicacion
			
							FROM
								sai_item  t1
								inner join sai_item_articulo t2 on(t1.id=t2.id)
								inner join sai_arti_salida_rs_item t5 on(t1.id=t5.arti_id)
								inner join sai_bien_marca t4 on(t5.marca_id=t4.bmarc_id)
								left join sai_arti_salida_rs t6 on(t5.acta_id=t6.acta_id)

							WHERE
								(t6.esta_id IS NULL or t6.esta_id!=15) and
								t1.esta_id=1 and
								id_tipo=4 and
								(t5.serial ='' or t5.serial is null) and
								(t5.ubicacion=" . $ubicaciont5 . ")
								" . $condicion . "

						GROUP BY
							t1.id,
							t1.nombre,
							t2.unidad_medida,
							t5.modelo,
							t4.bmarc_id,
							t4.bmarc_nombre,
							t5.ubicacion
						) AS tablas

			
					                group by
									tablas.nombre,
									tablas.bmarc_nombre,
					                tablas.id,
					                tablas.modelo,
					                tablas.bmarc_id,
					                tablas.unidad_medida,
									tablas.ubicacion
							having
								sum(tablas.cantidad) > 0
							order by
								tablas.id,
								tablas.nombre
						";
	
$query2 = "
			 SELECT
						t1.id,
						t1.nombre,
						t2.unidad_medida,
						t3.modelo,
						t3.cantidad,
						t3.serial,
						t4.bmarc_id,
						t4.bmarc_nombre,
						t3.ubicacion
					FROM
				 		sai_item  t1
						inner join sai_item_articulo t2 on(t1.id=t2.id)
						inner join sai_arti_inco_rs_item t3 on(t1.id=t3.arti_id)
						inner join sai_bien_marca t4 on(t3.marca_id=t4.bmarc_id)
					WHERE
						t1.esta_id=1
						and id_tipo=4
						and (t3.serial!='' or t3.serial!= null)
						and (t3.ubicacion =" . $ubicacionserial . ")
						and t3.sec_id not in
						(
							select
								t5.sec_id
							from
								sai_arti_salida_rs_item t5
								inner join sai_arti_salida_rs t6 on(t5.acta_id=t6.acta_id)
							where
								t6.esta_id!=15
								and (t5.sec_id is not null)

						)
					GROUP BY
						t1.id,
						t1.nombre,
						t2.unidad_medida,
						t3.modelo,
						t3.cantidad,
						t3.serial,
						t4.bmarc_id,
						t4.bmarc_nombre,
						t3.ubicacion
			";
	
//echo $query2;
	
$resultado = pg_exec ( $conexion, $query );
$indice = 0;
$stringobj;

while ( $row = pg_fetch_array ( $resultado ) ) {
	$stringobj [$indice] ['cantidad'] = $row ['disponible'];
	$stringobj [$indice] ['modelo'] = utf8_encode ( $row ['modelo'] );

	$stringobj [$indice] ['marca'] = utf8_encode ( $row ['bmarc_nombre'] );
	$stringobj [$indice] ['idmarca'] = $row ['bmarc_id'];
	// serial
	$stringobj [$indice] ['serial'] = utf8_encode ( $row ['serial'] );

	$stringobj [$indice] ['id'] = $row ["id"];
	$stringobj [$indice] ['unidad'] = utf8_encode ( $row ["unidad_medida"] );
	$stringobj [$indice] ['value'] = utf8_encode ( str_replace ( "\n", " ", strtoupper ( $row ["nombre"] ) ) );
	//ubicacion
	if($row ['ubicacion']==1){
		$ubicacionarray = "Torre";
	}else{
		$ubicacionarray = "Galpón";
	}
	$stringobj [$indice] ['ubicacion'] = $ubicacionarray;
	$stringobj [$indice] ['idubicacion'] = $row ['ubicacion'];

	$indice ++;
}

// serial
$resultado2 = pg_exec ( $conexion, $query2 );
$indice2 = 0;
$stringobj2;
	
while ( $row = pg_fetch_array ( $resultado2 ) ) {
	// array de seriales

	$stringobj2 [$row ['serial'].$row ['bmarc_id'].$row ['modelo']] ['cantidad'] = $row ['disponible'];
	$stringobj2 [$row ['serial'].$row ['bmarc_id'].$row ['modelo']] ['modelo'] = utf8_encode ( $row ['modelo'] );

	$stringobj2 [$row ['serial'].$row ['bmarc_id'].$row ['modelo']] ['marca'] = utf8_encode ( $row ['bmarc_nombre'] );
	$stringobj2 [$row ['serial'].$row ['bmarc_id'].$row ['modelo']] ['idmarca'] = ($row ['bmarc_id']);
	// serial
	$stringobj2 [$row ['serial'].$row ['bmarc_id'].$row ['modelo']] ['serial'] = utf8_encode ( $row ['serial'] );

	$stringobj2 [$row ['serial'].$row ['bmarc_id'].$row ['modelo']] ['id'] = $row ["id"];
	$stringobj2 [$row ['serial'].$row ['bmarc_id'].$row ['modelo']] ['unidad'] = utf8_encode ( $row ["unidad_medida"] );
	$stringobj2 [$row ['serial'].$row ['bmarc_id'].$row ['modelo']] ['value'] = utf8_encode ( str_replace ( "\n", " ", strtoupper ( $row ["nombre"] ) ) );

	if($row ['ubicacion']==1){
		$ubicacionarray2 = "Torre";
	}else{
		$ubicacionarray2 = "Galpón";
	}
	$stringobj2 [$row ['serial'].$row ['bmarc_id'].$row ['modelo']] ['ubicacion'] = $ubicacionarray2;
	$stringobj2 [$row ['serial'].$row ['bmarc_id'].$row ['modelo']] ['idubicacion'] = $row ['ubicacion'];
	$indice2 ++;
}
	
/*
 echo "<pre>"; print_r($stringobj2); echo "</pre>";
*/

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
.ui-autocomplete {
	max-height: 110px;
	font-size: 12px;
	overflow-y: auto;
	/* prevent horizontal scrollbar */
	overflow-x: hidden;
	/* add padding to account for vertical scrollbar */
	padding-right: 30px;
}
/* IE 6 doesn't support max-height
                        * we use height instead, but this forces the menu to always be this tall
                        */
* html .ui-autocomplete {
	font-size: 10px;
}

.ui-menu-item a {
	font-size: 10px;
}
</style>


<link rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all" />
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>" charset="utf-8"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/jquery-ui-1.8.13.custom.min.js';?>" charset="utf-8"></script>
<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />
<script language="javascript">

	var stringobj = <?php echo json_encode($stringobj,JSON_FORCE_OBJECT); ?>;
	//serial
	var stringobj2 = <?php echo json_encode($stringobj2,JSON_FORCE_OBJECT); ?>;  
	
	var listado_bienes = new Array();
	var listado_mobiliario = new Array();
	var ori = new Array();
	var factura_head = new Array();
	var listado_disponibilidad = new Array();
	var listado_activos = new Array();
	var idArticulo;
	var nombreArticulo;
	var uniArticulo;
	var modeloArticulo;
	var marcaArticulo;
	var idmarcaArticulo;
	var dispArticulo;
	var cantArticulo;
	var serial;
	var serialSioNo;
	var buscador;
	var ubicacion;
	var idubicacion;
			
$().ready(function(){
	var items = new Array();
	var index = 0;

	var items2 = new Array();
	var index2 = 0;

	//si escriben en el campo serial lo demas se borra
	$("#material").keyup(function(){
		
		idArticulo='';//limpiar variable 
		$("#serial").val("");
		$("#cantidad").attr("readonly",false).val("");
		serialSioNo = 0;
	});
	
	
	$("#serial").keyup(function(){

		$("#material").val("");
		$("#disponibilidad").val("");
		serialSioNo = 1;
		if($("#serial").val().length < 1){
			
			$("#cantidad").attr("readonly",false).val("");

		}	
	});	
	
	$.each(stringobj,function(id, params){
		items[index++] = {
				id: params.id,
				value: params.id+": "+params.value+" ("+params.unidad+") marca:"+params.marca+" "+(trim(params.modelo)==''?'':'modelo:')+" "+params.modelo+" ("+params.ubicacion+")",
				modelo: params.modelo,
				marca: params.marca,
				idmarca: params.idmarca,
				nombre: params.value,
				unidad: params.unidad,
				disponibilidad: params.cantidad,
				//serial
				serial:params.serial,
				ubicacion:params.ubicacion,
				idubicacion:params.idubicacion
		};

	});
	

	$("#material" ).autocomplete({
		source: items, 
		minLength: 1,
	    select: function(event,ui)
	    {

			$("#disponibilidad").val(ui.item.disponibilidad);
	   		//alert("id: " + ui.item.id + ", Modelo: " + ui.item.modelo + ", Disponibilidad: " + ui.item.disponibilidad);
	   		idArticulo = ui.item.id;
			nombreArticulo = ui.item.nombre;
			uniArticulo = ui.item.unidad;
			modeloArticulo = ui.item.modelo;
			marcaArticulo = ui.item.marca;
			idmarcaArticulo = ui.item.idmarca;
			dispArticulo = ui.item.disponibilidad;
			serial= ui.item.serial;
			ubicacion= ui.item.ubicacion;
			idubicacion=ui.item.idubicacion;
	    
	    return true;
	            
	    }
	});


		$.each(stringobj2,function(id, params){
			items2[index2++] = {
					id: params.id,
					value: params.serial+" "+params.marca+" ("+params.ubicacion+")",
					modelo: params.modelo,
					marca: params.marca,
					idmarca: params.idmarca,
					nombre: params.value,
					unidad: params.unidad,
					disponibilidad: params.cantidad,
					//serial
					serial:params.serial,
					ubicacion:params.ubicacion,
					idubicacion:params.idubicacion
					
			};

		});
		

		$("#serial").autocomplete({
			source: items2, 
			minLength: 1,
		    select: function(event,ui)
		    {
		    	$("#cantidad").attr("readonly","readonly").val(1);
		   		idArticulo = ui.item.id;
				nombreArticulo = ui.item.nombre;
				uniArticulo = ui.item.unidad;
				modeloArticulo = ui.item.modelo;
				marcaArticulo = ui.item.marca;
				idmarcaArticulo = ui.item.idmarca;
				dispArticulo = ui.item.disponibilidad;
				serial= ui.item.serial;
		    	buscador= ui.item.serial+ui.item.idmarca+ui.item.modelo;
		    	ubicacion= ui.item.ubicacion;
		    	idubicacion=ui.item.idubicacion;
		    return true;
		            
		    }
		});

	
});	

var idCadenaSigiente;
////////////funciones cadena
function GenerarSigienteCadena(valor)
{
if(valor)
{        
                                  
idCadenaSigiente = valor;
revisar();  

}
                              
}

function LlenarCadenaSigiente(){
$("#idCadenaSigiente").remove();
var div = $("#accionesEjecutar")[0];
var input1 = document.createElement("input");
input1.setAttribute("type","hidden");
input1.setAttribute("id","idCadenaSigiente");
input1.setAttribute("name","idCadenaSigiente");
input1.value= idCadenaSigiente;
div.appendChild(input1);
}
/////////////fin funciones cadena

function revisar()
{
  	if(document.form.destino.value=='')
	{
		window.alert("Debe especificar los datos antes de asignar");
		document.form.destino.focus();
		return;
	}
  
   if($("#body_factura_head > tr").length < 2){
		
	   alert("No se registr\u00F3 ning\u00FAn activo/material en el inventario");
	   return;		
	}  

	if(confirm("Datos introducidos de manera correcta. \u00BFEst\u00e1 seguro que desea continuar?"))
  {
	LlenarCadenaSigiente();////////////////////////////llamar funcion de cadena	  	
	document.form.submit();
  }	
  
}


function eliminarArticulo(objA){

	   objTrs = $(objA).parents("tr.trCaso");

	    objTrs.hide(200).remove(); 

	if($("#body_factura_head > tr").length < 2){
		
		$("#table_factura_head").hide(200);
				
	}  

}  



function add_factura_head()
{ 	

		if(serialSioNo == 0)
		{
	
	  	if(document.form.material.value=='' || idArticulo=='')
		{
			window.alert("No ha seleccionado ning\u00FAn material");
			document.form.destino.focus();
			return
		}
		
		if(($("#cantidad").val() =="") || ($("#cantidad").val() <="0"))
		{
			window.alert("Debe especificar la cantidad del art\u00edculo");
			document.form.cantidad.focus();
			return
		}

		if(isNaN($("#cantidad").val()))
		{
			window.alert("La cantidad debe ser un valor numerico");
			$("#cantidad").val("");
			document.form.cantidad.focus();
			return
		}

		
		if (parseInt($("#cantidad").val()) > parseInt(dispArticulo)){
			    document.form.cantidad.value="";
			    alert("La cantidad a ser entregada no puede ser mayor a la disponible");
			    return;
		}

		
		

		  var inputArticulo = $('input[name=\'ArrayRespSocial[id][]\'][value=\''+idArticulo+'\']');
		

		  //revisar codigo
		  var inputModeloarticulo = inputArticulo.parents('tr.trCaso').find('input[name=\'ArrayRespSocial[modelo][]\'][value=\''+modeloArticulo+'\']');

		  var inputModeloarticulo2 = inputArticulo.parents('tr.trCaso').find('input[name=\'ArrayRespSocial[idmarca][]\'][value=\''+idmarcaArticulo+'\']');

		  if(inputModeloarticulo.length != 0 && inputModeloarticulo2.length != 0){
			  
			  alert("El articulo ya fue agregado");
			    return;
		  }
	
		}
		else
		{
		
		if(($("#serial").val() =="") || ($("#serial").val() <="0"))
			{
		   window.alert("Debe especificar los datos antes de agregar");
	        document.form.serial.focus();
	       return
			}

			var inputArticulo = $('input[name=\'ArrayRespSocial[id][]\'][value=\''+idArticulo+'\']');

		var inputSerialarticulo = inputArticulo.parents('tr.trCaso').find('input[name=\'ArrayRespSocial[buscar][]\'][value=\''+buscador+'\']');

		  if(inputSerialarticulo.length != 0){
			  
			  alert("El articulo con ese serial ya fue agregado");
			    return;
		  }
		
		//buscar serial 
			if(!stringobj2[buscador]){
				
				alert("Seleccione un serial");
				return
				
			}
		  
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
	 		input1.setAttribute("name","ArrayRespSocial[id][]");
	 		input1.value= idArticulo;
	 		columna1.appendChild(input1);

	 		//nombre del articulo

	 		var columna2 = document.createElement("td");
	 		columna2.setAttribute("valign","top");
	 		columna2.setAttribute("style","font-size:10px");
	 		columna2.appendChild(document.createTextNode(nombreArticulo));

			//unidad de medida
	 		
	 		var columna3 = document.createElement("td");
	 		columna3.setAttribute("valign","top");
	 		columna3.setAttribute("style","font-size:10px");
	 		columna3.appendChild(document.createTextNode(uniArticulo));

	 		var input3 = document.createElement("input");
	 		input3.setAttribute("type","hidden");
	 		input3.setAttribute("name","ArrayRespSocial[unidad][]");
	 		input3.value= uniArticulo;
	 		columna3.appendChild(input3);

	 		//marca
	 		
	 		var columna6 = document.createElement("td");
	 		columna6.setAttribute("valign","top");
	 		columna6.setAttribute("style","font-size:10px");
	 		columna6.appendChild(document.createTextNode(marcaArticulo));
			
			var input6 = document.createElement("input");
			input6.setAttribute("type","hidden");
			input6.setAttribute("name","ArrayRespSocial[idmarca][]");
			input6.value= idmarcaArticulo;
			columna6.appendChild(input6);

	 		//input hidden para el nombre de la marca
	 			 		
	 		var input7 = document.createElement("input");
	 		input7.setAttribute("type","hidden");
	 		input7.setAttribute("name","ArrayRespSocial[marca][]");
	 		input7.value= marcaArticulo;
			columna6.appendChild(input7);

			//input hidden para el serial
			
			var columna9 = document.createElement("td");
			columna9.setAttribute("valign","top");
			columna9.setAttribute("style","font-size:10px");
			columna9.appendChild(document.createTextNode(serial));
		 		
	 		var input9 = document.createElement("input");
	 		input9.setAttribute("type","hidden");
	 		input9.setAttribute("name","ArrayRespSocial[serial][]");
	 		input9.value= serial;
	 		columna9.appendChild(input9);

	 		var input91 = document.createElement("input");
	 		input91.setAttribute("type","hidden");
	 		input91.setAttribute("name","ArrayRespSocial[buscar][]");
	 		input91.value= serial+idmarcaArticulo+modeloArticulo;
	 		columna9.appendChild(input91);

			//ubicacion
			var columna7 = document.createElement("td");
			columna7.setAttribute("valign","top");
			columna7.setAttribute("style","font-size:10px");
			columna7.appendChild(document.createTextNode(ubicacion));
			
	 		var input92 = document.createElement("input");
	 		input92.setAttribute("type","hidden");
	 		input92.setAttribute("name","ArrayRespSocial[idubicacion][]");
	 		input92.value= idubicacion;
	 		columna7.appendChild(input92);
	 		
			//modelo	 		

	 		var columna4 = document.createElement("td");
	 		columna4.setAttribute("valign","top");
	 		columna4.setAttribute("style","font-size:10px");
	 		columna4.appendChild(document.createTextNode(modeloArticulo));
			
			var input4 = document.createElement("input");
			input4.setAttribute("type","hidden");
			input4.setAttribute("name","ArrayRespSocial[modelo][]");
			input4.value= modeloArticulo;
	 		columna4.appendChild(input4);


	 		//cantidad

	 		var columna5 = document.createElement("td");
	 		columna5.setAttribute("valign","top");
	 		columna5.setAttribute("style","font-size:10px");
	 		columna5.appendChild(document.createTextNode( $("#cantidad").val() ));
			
			var input5 = document.createElement("input");
			input5.setAttribute("type","hidden");
			input5.setAttribute("name","ArrayRespSocial[cantidad][]");
			input5.value= $("#cantidad").val();
			columna5.appendChild(input5);

			//OPCION DE ELIMINAR
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
				
	 			eliminarArticulo($(this));
				
	 		});			
			

			fila.appendChild(columna1);				
			fila.appendChild(columna2);
			fila.appendChild(columna3);
			fila.appendChild(columna6);
			fila.appendChild(columna4);
			fila.appendChild(columna9);	
			fila.appendChild(columna5);
			fila.appendChild(columna7);
			fila.appendChild(columna8);	
								
			tbody.appendChild(fila);

			$("#table_factura_head").show(400);

			$("#material").val("");
			$("#disponibilidad").val("");
			$("#cantidad").val("");
			$("#serial").val("");
			
			idArticulo='';
			buscador='';
			
}

</script>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br/>
	<form action="asignar_inventario_Accion.php" name="form" id="form"
		method="post">
		<!-- borrar  <input type="hidden" value="0" name="hid_validar" />-->
		<table width="750" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" id="sol_via">
			<tr>
				<td height="15" colspan="2" valign="middle" class="td_gray"><span
					class="normalNegroNegrita"> Nota de entrega responsabilidad social</span></td>
			</tr>
			<tr>
				<td colspan="2">
					<table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
						<tr bgcolor="#F0F0F0">
							<td colspan='5'><div align="center" class="normalNegrita">Art&iacute;culos</div></td>
						</tr>
						<tr >
							<td><div align="center" class="normalNegrita">Descripci&oacute;n</div></td>
							<td><div align="center" class="normalNegrita">Serial del activo</div></td>
							<td><div align="center" class="normalNegrita">Disponibilidad</div></td>
							<td><div align="center" class="normalNegrita">Cantidad</div></td>
							<td><div align="center" class="normalNegrita">Opci&oacute;n</div></td>
						</tr>
						<tr>
							<td><div align="center">
									<span><input type="text" name="material" id="material"
										class="normalNegro" size="60">
									</span>
								</div></td>
							<td><div align="center" class="peq">
									<input name="serial" type="text" class="normalNegro"
										id="serial" size="20" value="">
								</div></td>
							<td><div align="center" class="peq">
									<input name="disponibilidad" type="text" class="normalNegro"
										id="disponibilidad" size="6" maxlength="6" value="" disabled>
								</div></td>
							<td>
								<div align="center" class="peq">
									<input name="cantidad" type="text" class="normalNegro"
										id="cantidad" size="6" maxlength="6">
								</div>
							</td>
							<td><div align="center" class="normal">
									<a href="javascript: add_factura_head(); " class="normal">Agregar
									</a>
								</div></td>
						</tr>
						</tbody>
					</table>
					
					</br>
					
					<table class="tablaalertas" id="table_factura_head"  border="0" style="width: 100%; display: none;" >
						<tbody id="body_factura_head" class="normal">
							<tr>
								<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Id</span></th>
								<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Nombre del
										art&iacuteculo</span></th>
								<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Unidad</span></th>
								<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Marca</span></th>
								<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Modelo</span></th>
								<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Serial</span></th>
								<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Cantidad</span></th>
								<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Ubicaci&oacuten</span></th>
								<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Opci&oacuten</span></th>
							</tr>
						</tbody>
					</table>


				</td>
			</tr>
			<tr>
				<td align="center">
				<br/>
				<div class="normal">
				
					<strong>Detalle del destino</strong>
					</div>
				
				<textarea name="destino" cols="50" rows="5"></textarea>
				
				
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
				<div align="center" id="accionesEjecutar">
    
    			<?php
    				////////////////////////////boton de cadena generar documento 
     			foreach ($GLOBALS['SafiRequestVars']['opciones'] as $index){ ?>
           		<span class="cadena"> <input type="button"
							value="<?php echo $index['wfop_nombre'];?>"
							id="<?php echo $index['wfop_descrip'];?>"
							onclick="GenerarSigienteCadena(<?php echo $index['id_cadena_hijo'];?>,0)" />
						</span>
               <?php }?>
    
    
    </div>
				</td>
			</tr>
			<!-- borrar <input type="hidden" name="txt_arreglo_articulos" id="txt_arreglo_articulos" />    -->
			</table>
			</form>

</body>
</html>







