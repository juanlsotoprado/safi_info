<?php 
  ob_start();
  session_start();
  include(dirname(__FILE__) . '/../../../init.php');
  
  require_once("../../../includes/conexion.php");
 
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../index.php',false);
   ob_end_flush(); 
   exit;
  }
  ob_end_flush();
  
  //Modelos cadena de amat(disminucion de materiales)
   
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
   
  //obtener id de cadena inicial
  $lugar = 'amat';
  $id_cadena_inicial = SafiModeloWFCadena::GetWfca_id_inicial($lugar);
  $GLOBALS['SafiRequestVars']['idCadenaActual'] = $id_cadena_inicial;
  //obtener cadena hijo
  $id_HijosCadena = SafiModeloWFCadena::GetId_cadena_hijo_id_cadena($id_cadena_inicial);
  $GLOBALS['SafiRequestVars']['opciones'] = $id_HijosCadena;
  /*
  echo "<pre>";
  echo print_r($id_HijosCadena);
  echo "</pre>";
  */
  $param['lugar'] = "amat";
  $param['ano'] = substr($_SESSION['an_o_presupuesto'],2);
  $niveles = array(3,4);
  $param['Dependencia']= '';
  
  $queryautocompletar =
  "
  SELECT
	tablas.esta_id,
	tablas.arti_id,
	tablas.nombre,
	tablas.medida,
	tablas.precio,
	sum(tablas.cantidad) as cantidad
	FROM
	(
		SELECT
			esp.arti_id,
			nombres.nombre,
			esp.medida,
			esp.precio,
			general.usua_login,
			general.esta_id,
			sum(esp.cantidad) as cantidad
		FROM
			sai_arti_inco general
			inner join sai_arti_almacen esp on(esp.acta_id=general.acta_id)
			inner join sai_item nombres on(nombres.id = esp.arti_id)
		WHERE
			general.esta_id = 1
		GROUP BY
			esp.precio,
			general.usua_login,
			general.esta_id,
			esp.arti_id,
			nombres.nombre,
			esp.medida
	
	UNION
		SELECT
			esp.arti_id,
			nombres.nombre,
			esp.medida,
			esp.precio,
			general.usua_login,
			general.esta_id,
			sum(-esp.cantidad) as cantidad
		FROM
			sai_arti_acta_almacen general
			inner join sai_arti_salida esp on(esp.n_acta=general.amat_id)
			inner join sai_item nombres on(nombres.id = esp.arti_id)
		WHERE
			general.esta_id = 1
		GROUP BY
			general.usua_login,
			esp.precio,
			general.esta_id,
			esp.arti_id,
			nombres.nombre,
			esp.medida
	)AS tablas
	GROUP BY
		tablas.esta_id,
		tablas.nombre,
		tablas.precio,
		tablas.medida,
		tablas.arti_id
	having
		sum(tablas.cantidad) > 0
	order by
		tablas.nombre		
  "; 
  
  $resultado = pg_exec ( $conexion, $queryautocompletar );
  $indice = 0;
  $stringobj;
  
  while ( $row = pg_fetch_array ( $resultado ) ) {
  	$stringobj [$indice] ['cantidad'] = $row ['cantidad'];
  	$stringobj [$indice] ['arti_id'] = $row ['arti_id'];
  	$stringobj [$indice] ['nombre'] = utf8_encode($row ['nombre']) ;
  	$stringobj [$indice] ['precio'] = $row ['precio'];
  	$stringobj [$indice] ['medida'] = utf8_encode ( $row ['medida'] );
  
  	$indice ++;
  }
  /*
  echo "<pre>";
  echo print_r($stringobj);
  echo "</pre>";
  */
  $queryemplados=
  "
	select
		empl_nombres,
		empl_apellidos,
		empl_cedula
	from
		sai_empleado
	order by
		empl_nombres
";
  
  $resultado1 = pg_exec ( $conexion, $queryemplados );
  $indice1 = 0;
  $stringobj1;
  while ( $row1 = pg_fetch_array ( $resultado1 ) ) {
  	$stringobj1 [$indice1] ['empl_nombres'] = strtoupper(utf8_encode($row1 ['empl_nombres'])." ".utf8_encode($row1 ['empl_apellidos']));
  	$stringobj1 [$indice1] ['empl_cedula'] = $row1 ['empl_cedula'];
  	$indice1 ++;
  }
 ?>
	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::Disminuci&oacute;n del Inventario.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </script>
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript" src="../../../js/lib/jquery/plugins/jquery.min.js"></script>
<script type="text/javascript" src="../../../js/lib/jquery/plugins/ui.min.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />
<script language="JavaScript" type="text/JavaScript">
var factura = new Array();
var factura_head = new Array();
var dependencia = new Array();
var ori = new Array();
var listado_disponibilidad = new Array();
var stringobj = <?php echo json_encode($stringobj,JSON_FORCE_OBJECT); ?>;
var stringobj1 = <?php echo json_encode($stringobj1,JSON_FORCE_OBJECT); ?>;
var idArticulo;
var nombreArticulo;
var precio;
var uniArticulo;
var items = new Array();
var index = 0;
var items1 = new Array();
var index1 = 0;
var hay = 0;

$().ready(function(){
	$.each(stringobj,function(id, params){
		items[index++] = {
				id: params.arti_id,
				nombre:params.nombre,
				unidad: params.medida,
				precio: params.precio,
				value: "Id: "+params.arti_id+": "+params.nombre+" ("+params.medida+") Precio:"+params.precio,
				cantidad: params.cantidad
				
		};

	});

	$("#articulo" ).autocomplete({
		source: items, 
		minLength: 1,
	    select: function(event,ui)
	    {

			$("#disponibilidad").val(ui.item.cantidad);
			$("#arti_nombre").val(ui.item.nombre);
	   		idArticulo = ui.item.id;
	   		nombreArticulo = ui.item.nombre;
	   		uniArticulo = ui.item.unidad;
	   		precio = ui.item.precio;
	    
	    return true;
	            
	    }
	});
	$.each(stringobj1,function(id, params){
		items1[index1++] = {
				id: params.empl_cedula,
				value: params.empl_nombres
				
		};

	});

	$("#entregado_a" ).autocomplete({
		source: items1, 
		minLength: 1,
	    select: function(event,ui)
	    {

	    $("#solicita").val(ui.item.id);
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
//alert(idCadenaSigiente);
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
////////////fin funciones cadena

function eliminarArticulo(objA){

	   objTrs = $(objA).parents("tr.trCaso");

	    objTrs.hide(200).remove();


	if($("#body_factura_head > tr").length < 2){

		hay = 0;		
		$("#table_factura_head").hide(200);
				
	}


}  

function add_factura_head(id,tipo)
{
	if(document.form.articulo.value == "")
	{
		window.alert("Seleccione un Art\u00EDculo");
		return;
	}

	if(document.form.cantidad.value < 1)
	{
		window.alert("Cantidad del art\u00EDculo debe ser mayor a 0");
		document.form.cantidad.value="";
		document.form.cantidad.focus();
		return;
	}
	if(parseInt(document.form.cantidad.value)>parseInt(document.form.disponibilidad.value))
	{
		alert("La cantidad a ser entregada no puede ser mayor a la disponible");
		document.form.cantidad.value="";
		document.form.cantidad.focus();
		return;
	}
	if(document.form.cantidad.value=="")
	{
		window.alert("Debe especificar la cantidad del art\u00EDculo");
		document.form.cantidad.focus();
		document.form.cantidad.select();
		return;
	}

	//verificar si articulo ya fue agregado
	
	  var inputArticulo = $('input[name=\'ArrayDisminucion[id][]\'][value=\''+idArticulo+'\']');

	  var inputPrecioarticulo = inputArticulo.parents('tr.trCaso').find('input[name=\'ArrayDisminucion[precio][]\'][value=\''+precio+'\']');

	  if(inputPrecioarticulo.length != 0){
		  
		  alert("El articulo con ese precio ya fue agregado a la lista");
		    return;
	  }

	//FIN verificar si articulo ya fue agregado
	
/*
	if(tipo==0)
	{
	
	  	if(document.form.articulo.value=='0')
		{
			window.alert("Debe seleccionar alg\u00FAn art\u00EDculo");
			document.form.articulo.focus();
			document.form.articulo.select();
			return
		}


		

		
		
		codi=document.form.articulo.value;
		for(i=0;i<factura_head.length;i++)
	      {
		   	if (factura_head[i][0]==codi)
		      {
			    alert("Art\u00EDculo ya seleccionado");
			    return;
	          }
	      }



		
	    for (i=0;i<ori.length;i++)
		{
			if(ori[i][0]==document.form.articulo.value)
			{	
				document.form.arti_nombre.value=ori[i][1];
		
			}
		}


	    for (i=0;i<dependencia.length;i++)
		{
			if(dependencia[i][0]==document.form.opt_depe.value)
			{	
				document.form.depe_nombre.value=dependencia[i][1];
				break
			}
		}
		
		var registro_factura_head = new Array(3);
		registro_factura_head[0] = document.form.articulo.value;
		registro_factura_head[1] = document.form.arti_nombre.value;
		registro_factura_head[2] = document.form.cantidad.value;
		factura_head[factura_head.length]=registro_factura_head;
	}
	
	var tbody_factura = document.getElementById('body_factura_head');
	var tbody_factura2 = document.getElementById(id);
	
	for(i=0;i<factura_head.length-1;i++)
	{
		tbody_factura2.deleteRow(3);
	}
	
	if(tipo!=0)
	{
		tbody_factura2.deleteRow(3);
		for(i=tipo;i<factura_head.length;i++)
		{
			factura_head[i-1]=factura_head[i];
		}
		factura_head.pop();
	}

	var factura_actual= document.form.articulo.value;*/


	
	var tbody = $('#body_factura_head')[0];
		
		var fila = document.createElement("tr");
		fila.className='normalNegro trCaso';

		//id del articulo

		var columna1 = document.createElement("td");
		columna1.setAttribute("valign","top");
		columna1.setAttribute("align","center");
		columna1.setAttribute("style","font-size:10px");
		columna1.appendChild(document.createTextNode(idArticulo));
		
		var input1 = document.createElement("input");
		input1.setAttribute("type","hidden");
		input1.setAttribute("name","ArrayDisminucion[id][]");
		input1.value= idArticulo;
		columna1.appendChild(input1);

		//nombre del articulo

		var columna2 = document.createElement("td");
		columna2.setAttribute("valign","top");
		columna2.setAttribute("align","center");
		columna2.setAttribute("style","font-size:10px");
		columna2.appendChild(document.createTextNode(nombreArticulo));

		//unidad de medida
		
		var columna3 = document.createElement("td");
		columna3.setAttribute("valign","top");
		columna3.setAttribute("align","center");
		columna3.setAttribute("style","font-size:10px");
		columna3.appendChild(document.createTextNode(uniArticulo));

		var input3 = document.createElement("input");
		input3.setAttribute("type","hidden");
		input3.setAttribute("name","ArrayDisminucion[unidad][]");
		input3.value= uniArticulo;
		columna3.appendChild(input3);


		//cantidad

		var columna5 = document.createElement("td");
		columna5.setAttribute("valign","top");
		columna5.setAttribute("align","center");
		columna5.setAttribute("style","font-size:10px");
		columna5.appendChild(document.createTextNode( $("#cantidad").val() ));
		
		var input5 = document.createElement("input");
		input5.setAttribute("type","hidden");
		input5.setAttribute("name","ArrayDisminucion[cantidad][]");
		input5.value= $("#cantidad").val();
		columna5.appendChild(input5);

		//precio

		var columna6 = document.createElement("td");
		columna6.setAttribute("valign","top");
		columna6.setAttribute("align","center");
		columna6.setAttribute("style","font-size:10px");
		columna6.appendChild(document.createTextNode(precio));
		
		var input6 = document.createElement("input");
		input6.setAttribute("type","hidden");
		input6.setAttribute("name","ArrayDisminucion[precio][]");
		input6.value= precio;
		columna5.appendChild(input6);

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
		fila.appendChild(columna5);
		fila.appendChild(columna6);
		fila.appendChild(columna8);	
							
		tbody.appendChild(fila);

		$("#table_factura_head").show(400);	

		$("#articulo").val("");
		$("#disponibilidad").val("");
		$("#cantidad").val("");

		/*	
		idArticulo='';
		buscador='';	*/

		hay = 1;

	 
}

function acceptFloat(evt){	
	// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57	
	var nav4 = window.Event ? true : false;
	var key = nav4 ? evt.which : evt.keyCode;	
	return (key <= 13 || (key >= 48 && key <= 57) || key == 46);
}

function no_coma(evt){ /*NO ACEPTA LA COMA EN EL TEXT_BOX*/	
		
	var nav4 = window.Event ? true : false;
	
	var key = nav4 ? evt.which : evt.keyCode;	

	return (key != 34 &&  key != 39);
}

function revisar()
{
	if (hay==0)
	{
		alert("Debe agregar un art\u00EDculo");
		return
	}
	if(document.form.opt_depe.value=='0')
	{
		window.alert("Debe seleccionar la dependencia solicitante");
		document.form.opt_depe.focus();
		document.form.opt_depe.select();
		return
	}
	if(document.form.motivo.value=='')
	{
		window.alert("Indique los detalles u observaciones");
		document.form.opt_depe.focus();
		document.form.opt_depe.select();
		return
	}
  	if($("#solicita").val()=='')
	{
		window.alert("Debe indicar el receptor de los materiales");
		document.form.solicita.focus();
		document.form.solicita.select();
		return
	}
	if(document.form.hid_desde_itin.value=="")
	{
  		alert("Debe seleccionar la fecha del acta");
  		document.form.hid_desde_itin.focus();
  		return;
	}

/*
	document.form.txt_arreglo_factura_head.value="";
	
	for(i=0;i<factura_head.length;i++)
	{
		for (j=0; j<3; j++)//4
		{
			document.form.txt_arreglo_factura_head.value+=factura_head[i][j];
		   	
			if  ( (i<(factura_head.length-1)  ) ||  (i==(factura_head.length-1) &&  (j!=2) ) )
			{
			document.form.txt_arreglo_factura_head.value+="�";
			}
		}	
			
	}*/
	if(confirm("Datos introducidos de manera correcta. \u00BFEst\u00E1 seguro que desea continuar?"))
  	{
		LlenarCadenaSigiente();////////////////////////////llamar funcion de cadena
		document.form.submit();
  	}	
  
}

</script>
<?php 
$i=0;
$sql_e="select cantidad,id from sai_item_distribucion where ubicacion=1 order by id";
     $resultado_entrada=pg_query($conexion,$sql_e) or die("Error al consultar disponibilidad");  
	 while ($rowe=pg_fetch_array($resultado_entrada)) {
		$id_arti = $rowe["id"];
		$cant_disp= $rowe["cantidad"];
		
		echo("
				<script language='javascript'>
				var registro = new Array(); 
				registro[0]='$id_arti';
				registro[1]='$cant_disp';
				listado_disponibilidad[$i]=registro;
				</script>
				");
				$i++;
			}
?>
</head>
<body>
<form name="form" method="post" enctype="multipart/form-data" id="form1" action="disminuirAccion.php">
<br /><br />
<table width="700" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" border="0">
  <tr class="td_gray"> 
    <td width="700" height="15" valign="middle"><span class="normalNegroNegrita">Disminuci&oacute;n del inventario</span></td>
  </tr>
  <tr>
    <td>
     <table width="700" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
       <tr>
       </tr>
       <tr width="100%">
         <td><div align="center" class="normalNegrita">Art&iacute;culo</div></td>
         <td><div align="center" class="normalNegrita">Disponibilidad</div></td>
	     <td><div align="center" class="normalNegrita">Cantidad</div></td>
         <td><div align="center" class="normalNegrita">Opci&oacute;n</div></td>
       </tr>
	   <tr>
   		<td>
   			<div align="center">
   				<span><input type="text" name="articulo" id="articulo" class="normalNegro" size="60">
   				<input name="arti_nombre" type="hidden" id="arti_nombre" />
   				</span>
			</div>
		</td>
						<td>
				<div align="center" class="peq"><input name="disponibilidad" type="text"
					class="normalNegro" id="disponibilidad" size="6" maxlength="6" value="" disabled></div>
				</td>
	  <td><div align="center" class="peq">
	    <input name="cantidad" type="text"  class="normal" id="cantidad" size="6" maxlength="6" onKeyPress = "return acceptFloat(event)" ></div></td>
  	  <td>
	   <div align="center" class="normal"><a href="javascript: add_factura_head('factura_head','0'); " class="normal">Agregar	</a></div>	</td>
	</tr>
	
						

  </table>
  <table class="tablaalertas" id="table_factura_head"  border="0" style="width: 100%; display: none;" >
    <tbody id="body_factura_head" class="normal">
		<tr>
			<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Id</span></th>
			<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Nombre del art&iacuteculo</span></th>
			<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Unidad</span></th>
			<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Cantidad</span></th>
			<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Precio</span></th>
			<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Opci&oacuten</span></th>
		</tr>
	</tbody>
  </table>
  
  
  
  </td>
   </tr>

   
   
   <tr>
     <td height="10" valign="middle" >	  </td>
   </tr>
   <tr><td><div align="center" class="normalNegrita">Dependencia</div></td></TR>
   <tr><td align="center"  class="normal">  
      <select name="opt_depe" id="opt_depe" class="normal">
	    <option value="0">[Seleccione]</option>
		<?php
	      $sql_str="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_id,depe_nombre','depe_nivel<>6 and depe_id like ''45%'' or depe_nivel=4 or depe_id=150','depe_nombre',1) resultado_set (depe_id varchar, depe_nombre varchar)";
	      $res_q=pg_exec($sql_str) or die("Error al mostrar");	  
	      $i=0;
	      while($depe_row=pg_fetch_array($res_q)){ 
 		   $depe_id=$depe_row['depe_id'];
		   $depe_nombre=$depe_row['depe_nombre'];
		
		   echo("
			<script language='javascript'>
			var depe = new Array(); 
			depe[0]='$depe_id';
			depe[1]='$depe_nombre';
			dependencia[$i]=depe;
			</script>
		    ");
		   $i++;
		 ?>
         <option value="<?php echo(trim($depe_row['depe_id'])); ?>"><?php echo(trim($depe_row['depe_nombre'])); ?></option>
    <?php }?>
     </select>	<input name="depe_nombre" type="hidden" id="depe_nombre" />	   </td>
  </tr>
  <tr><td>&nbsp;</td></tr>
  <tr width="100%"> 
    <td><div align="center" class="normalNegrita">Observaciones </div></td>
  </tr>
  <tr>
    <td><div align="center"  class="normal"><textarea name="motivo" id="motivo" class="normal" cols="50" rows="3" onKeyPress = "return no_coma(event)"></textarea></div>	</td></tr>
  <tr><td>&nbsp;</td></tr>
  <tr> 
    <td>
    
<table border="0" width="700" align="center">
 <tr><td width="60%" ><div align="center" class="normalNegrita">Entregar a </div></td>
  <td width="40%"><div align="center" class="normalNegrita">Fecha del Acta</div></td>
 </tr>
 <tr>
  <td>
	<div align="center" class="peq">
		<input type="text" name="entregado_a" id="entregado_a" class="normalNegro" size="60">
		<input type="hidden" name="solicita" id="solicita" class="normalNegro" size="60">
	</div>
  </td>
  <td width="490" align="right" class="normalNegrita">
     <div align="center">
      <input type="text" size="10" id="hid_desde_itin" name="hid_desde_itin" class="dateparse" readonly/>
	  <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_desde_itin');" title="Show popup calendar">
	  <img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
	  </a>
     </div></td>
 </tr>

</table>
  </td>
 </tr>
 <tr>
   <td height="16" colspan="3">
			<div align="center" id="accionesEjecutar">
			<?php
			////////////////////////////boton de cadena generar documento 
				foreach ($GLOBALS['SafiRequestVars']['opciones'] as $index){ ?>
				<span class="cadena"> 
					<input 
							type="button" 
							value="<?php echo $index['wfop_nombre'];?>" 
							id="<?php echo $index['wfop_descrip'];?>" 
							onclick="GenerarSigienteCadena(<?php echo $index['id_cadena_hijo'];?>,0)" 
					/>
				</span>
			<?php }?>
			</div>
	</td>
</tr>
</table>
  <input  type="hidden" name="txt_arreglo_factura_head" id="txt_arreglo_factura_head" />
</form>
</body>
</html>
<?php pg_close($conexion);?>