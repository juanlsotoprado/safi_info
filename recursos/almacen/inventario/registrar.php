<?php 
  ob_start();
  session_start();
  require_once("../../../includes/conexion.php");
  include(dirname(__FILE__) . '/../../../init.php');
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../../index.php',false);
   ob_end_flush(); 
   exit;
  }
  ob_end_flush(); 
    
	//Modelos cadena de emat(entrada de materiales)
	  
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
	$lugar = 'emat';
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
	$param['lugar'] = "emat";
	$param['ano'] = substr($_SESSION['an_o_presupuesto'],2);
	$niveles = array(3,4);
	$param['Dependencia']= '';
	
	
	$queryarti = "
	select
		t1.id,
		t1.nombre,
		t2.unidad_medida
		from
		sai_item t1,
		sai_item_articulo t2
	where
		id_tipo = 1 and
		t1.id = t2.id
";
	$resultado5 = pg_exec ( $conexion, $queryarti );
	$indice5 = 0;
	$stringobj5;
	while ( $row5 = pg_fetch_array ( $resultado5 ) ) {
		$stringobj5 [$indice5] ['id_arti'] = $row5 ['id'];
		$stringobj5 [$indice5] ['nombre_arti'] = utf8_encode($row5 ['nombre']);
		$stringobj5 [$indice5] ['unidad_medida'] = utf8_encode($row5 ['unidad_medida']);
		$indice5 ++;
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
<title>SAFI::Registro del Almac&eacute;n:.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </script>
<script language="JavaScript" src="../../../js/lib/actb.js"></script>
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript" src="../../../js/lib/jquery/plugins/jquery.min.js"></script>
<script type="text/javascript" src="../../../js/lib/jquery/plugins/ui.min.js"></script>
<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>

<script language="JavaScript" type="text/JavaScript">
var stringobj5 = <?php echo json_encode($stringobj5,JSON_FORCE_OBJECT); ?>;
var factura = new Array();
var factura_head = new Array();
var dependencia = new Array();
var ori = new Array();
var idCadenaSigiente;
var idArticulo;
var precioarti;
var nombreArticulo;
var uniArticulo;
var items2 = new Array();
var index2 = 0;
var hay = 0;
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

$().ready(function(){
$.each(stringobj5,function(id, params){
	items2[index2++] = {
			id: params.id,
			value: params.nombre_arti,
			artinombre: params.nombre_arti,
			idnombre: params.id_arti,
			unidad: params.unidad_medida
	};
});

$("#articulo" ).autocomplete({
	source: items2, 
	minLength: 1,
    select: function(event,ui)
    {
   		idArticulo = ui.item.idnombre;
   		nombreArticulo = ui.item.artinombre;
   		uniArticulo = ui.item.unidad;
    
    return true;
            
    }
});
});

function acceptFloat(evt){	
	// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57	
	var nav4 = window.Event ? true : false;
	var key = nav4 ? evt.which : evt.keyCode;	
	return (key <= 13 || (key >= 48 && key <= 57) || key == 46);
}

function add_factura_head(id,tipo)
{
	if (document.form.nombre.value==""){
		   alert("Debe indicar el nombre del proveedor");
		   document.form.nombre.focus();
		   return
	}

	if(document.form.txt_ubica.value=="0")
	{
		alert("Debe seleccionar la ubicaci\u00F3n del art\u00EDculo");
		document.form.txt_ubica.focus();
		return
	}
	if(document.form.opt_depe.value=='0')
	{
		window.alert("Debe seleccionar la dependencia solicitante");
		document.form.opt_depe.focus();
		return
	}
  	if(document.form.articulo.value=='')
	{
		window.alert("No ha seleccionado ning\u00FAn art\u00EDculo");
		document.form.articulo.focus();
		return
	}
	if(document.form.cantidad.value=="")
	{
		window.alert("Debe especificar la cantidad del art\u00EDculo");
		document.form.cantidad.focus();
		return
	}
	if(document.form.cantidad.value < 1)
	{
		window.alert("La cantidad del art\u00EDculo debe ser mayor a 0");
		document.form.cantidad.focus();
		return
	}
	if(document.form.precio.value=="")
	{
		window.alert("Debe especificar el precio del art\u00EDculo");
		document.form.precio.focus();
		return
	}
	if(document.form.precio.value < 1)
	{
		window.alert("El precio del art\u00EDculo debe ser mayor a 0");
		document.form.precio.focus();
		return
	}
	if(document.form.hid_desde_itin.value == "")
	{
		window.alert("Debe seleccionar la fecha de recepci\u00f3n");
		document.form.hid_desde_itin.focus();
		return
	}

	//verificar si articulo ya fue agregado
	
	precioarti = $("#precio").val();
	
	  var inputArticulo = $('input[name=\'ArrayRegistro[id][]\'][value=\''+idArticulo+'\']');
	
	  var inputPrecioarticulo = inputArticulo.parents('tr.trCaso').find('input[name=\'ArrayRegistro[precio][]\'][value=\''+precioarti+'\']');

	  if(inputPrecioarticulo.length != 0){
		  
		  alert("El articulo con ese precio ya fue agregado a la lista");
		    return;
	  }

	//FIN verificar si articulo ya fue agregado

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
 		input1.setAttribute("name","ArrayRegistro[id][]");
 		input1.value= idArticulo;
 		columna1.appendChild(input1);

 		//nombre del articulo

 		var columna2 = document.createElement("td");
 		columna2.setAttribute("valign","top");
 		columna2.setAttribute("align","center");
 		columna2.setAttribute("style","font-size:10px");
 		columna2.appendChild(document.createTextNode(nombreArticulo));

		var input2 = document.createElement("input");
		input2.setAttribute("type","hidden");
		input2.setAttribute("name","ArrayRegistro[nombre][]");
		input2.value= nombreArticulo;
 		columna2.appendChild(input2);

 		
 		//hidden de unidad
		
		var input12 = document.createElement("input");
		input12.setAttribute("type","hidden");
		input12.setAttribute("name","ArrayRegistro[unidad][]");
		input12.value= uniArticulo;
 		columna1.appendChild(input12);

 		//hidden de dependencia
		
		var input13 = document.createElement("input");
		input13.setAttribute("type","hidden");
		input13.setAttribute("name","ArrayRegistro[depe][]");
		input13.value= $("#opt_depe").val();
 		columna1.appendChild(input13);

		//cantidad

 		var columna5 = document.createElement("td");
 		columna5.setAttribute("valign","top");
 		columna5.setAttribute("align","center");
 		columna5.setAttribute("style","font-size:10px");
 		columna5.appendChild(document.createTextNode( $("#cantidad").val() ));
		
		var input5 = document.createElement("input");
		input5.setAttribute("type","hidden");
		input5.setAttribute("name","ArrayRegistro[cantidad][]");
		input5.value= $("#cantidad").val();
		columna5.appendChild(input5);

		//precio

 		var columna6 = document.createElement("td");
 		columna6.setAttribute("valign","top");
 		columna6.setAttribute("align","center");
 		columna6.setAttribute("style","font-size:10px");
 		columna6.appendChild(document.createTextNode( $("#precio").val() ));
		
		var input6 = document.createElement("input");
		input6.setAttribute("type","hidden");
		input6.setAttribute("name","ArrayRegistro[precio][]");
		input6.value= $("#precio").val();
		columna6.appendChild(input6);

		//fecha

 		var columna7 = document.createElement("td");
 		columna7.setAttribute("valign","top");
 		columna7.setAttribute("align","center");
 		columna7.setAttribute("style","font-size:10px");
 		columna7.appendChild(document.createTextNode( $("#hid_desde_itin").val() ));
		
		var input7 = document.createElement("input");
		input7.setAttribute("type","hidden");
		input7.setAttribute("name","ArrayRegistro[fecha][]");
		input7.value= $("#hid_desde_itin").val();
		columna7.appendChild(input7);
		

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

 		$("#table_factura_head").show(400);

 		$(deleteLink).bind('click', function(){
 			$("#total").val($("#total").val()-1);
 			eliminarArticulo($(this));
			
 		});			
		

		fila.appendChild(columna1);				
		fila.appendChild(columna2);
		fila.appendChild(columna5);
		fila.appendChild(columna6);
		fila.appendChild(columna7);
		fila.appendChild(columna8);	
							
		tbody.appendChild(fila);

		suma = parseInt($("#total").val())+1;
		$("#total").val(suma);
		
		 document.form.articulo.value="";
		 document.form.cantidad.value="";
		 document.form.precio.value="0.00";
		 document.form.hid_desde_itin.value="";

		$("#nombre").attr("disabled","disabled");
		$("#txt_ubica").attr("disabled","disabled");
		$("#opt_depe").attr("disabled","disabled");
		
		hay = 1;
}

function eliminarArticulo(objA){

	   objTrs = $(objA).parents("tr.trCaso");

	    objTrs.hide(200).remove(); 

	if($("#body_factura_head > tr").length < 2){
		hay = 0;
		$("#table_factura_head").hide(200);
		$("#nombre").removeAttr("disabled");
		$("#txt_ubica").removeAttr("disabled");
		$("#opt_depe").removeAttr("disabled");
				
	}  

}

function relacion(){
 arti=document.form.articulo.value;
 document.form.action="carga_1.php?id_arti="+arti;
 document.form.submit();
}


function no_coma(evt){ /*NO ACEPTA LA COMA EN EL TEXT_BOX*/	
		
	var nav4 = window.Event ? true : false;
	var key = nav4 ? evt.which : evt.keyCode;	
	return (key != 44);
}


function revisar()
{

	if (hay == 0)
	{
		alert("Debe agregar un art\u00EDculo");
		return
	}
	/*
	if (factura_head.length==0)
	{
		window.alert("Debe seleccionar al menos un art\u00EDculo en el inventario");
		return
	}


		
	document.form.txt_arreglo_factura_head.value="";
	
	for(i=0;i<table_factura_head.length;i++)
	{
		for (j=0; j<7; j++)
		{
			document.form.txt_arreglo_factura_head.value+=factura_head[i][j];
		   	
			if  ( (i<(factura_head.length-1)  ) ||  (i==(factura_head.length-1) &&  (j!=6) ) )
			{
			document.form.txt_arreglo_factura_head.value+="�";
			}
		}	
			
	}*/
	if(confirm("Datos introducidos de manera correcta. \u00BFEst\u00E1 seguro que desea continuar?."))
  {
	$("#nombre").removeAttr("disabled");
	$("#txt_ubica").removeAttr("disabled");
	$("#opt_depe").removeAttr("disabled");
	document.form.action="registrarAccion.php";
	LlenarCadenaSigiente();////////////////////////////llamar funcion de cadena
	document.form.submit();
  }	
  
}
</script>

</head>
<body>

<form name="form" method="post" enctype="multipart/form-data" id="form1">
<br/><br/>
<table width="1020" border="0"  align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
    <tr class="td_gray"> 
      <td width="1020" height="15" valign="middle" ><span class="normalNegroNegrita">Registro </span></td>
    </tr>

    <tr>
	  <td>
	  
       <table  width="1040" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas">
       <tr class="normalNegroNegrita" align="center" style="background-color: #F0F0F0;">
       	<td colspan="4" height="5">Datos Generales</td>
       </tr>
         <tr width="100%">
          <td class="normalNegrita">Proveedor:</td>
          <td><input class="normalNegro" autocomplete="off" size="40" type="text" id="nombre" name="nombre" value="<?= $nombre?>" <?php if($rif!="" || $codigo!=""){echo "disabled='disabled'";}?> /></td>
		  <?php
			$query = "SELECT prov_nombre FROM sai_proveedor_nuevo ORDER BY prov_nombre";
			$resultado = pg_exec($conexion, $query);
			$numeroFilas = pg_num_rows($resultado);
			$arreglo = "";
			while($row=pg_fetch_array($resultado)){
			 $arreglo .= "'".utf8_encode($row["prov_nombre"])."',";	
			}
			 $arreglo = substr($arreglo, 0, -1);
			?>
			<script>
				var proveedores = new Array(<?= $arreglo?>);
				actb(document.getElementById('nombre'),proveedores);
			</script>
		   <td class="normalNegrita">Ubicaci&oacute;n:</td>
		   <td><div align="left" class="normal">
               <select name="txt_ubica" id="txt_ubica" class="normalNegro">
 				<option value="0" selected>Seleccione...</option>
 				<option value="1" >Torre</option>
				<option value="2" >Galp&oacute;n</option>
		       </select></div></td>
         </tr>
         
         <tr>
         
         	<td class="normalNegrita">Dependencia Solicitante:</td>
             <td>
      <select name="opt_depe" class="normalNegro" id="opt_depe">
	   <option value="0">[Seleccione]</option>
		<?php
	    
	    $sql_str = "
	    	SELECT
	    		depe_id,
	    		depe_nombre
	    	FROM
	    		sai_dependenci dependencia
	    	WHERE
	    		depe_nivel = 4
	    		OR depe_id = 150
	    	ORDER BY
	    		depe_nombre
	    ";
	    
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
      </select>	<input name="depe_nombre" type="hidden" id="depe_nombre" />	   
      </td>
      </tr>
       </table>
       
       
<table width="1040" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
       <tr class="normalNegroNegrita" align="center" style="background-color: #F0F0F0;">
       	<td colspan="6" height="5">Art&iacute;culos</td>
       </tr>
 <tr>
	<td><div align="center" class="normalNegrita">Art&iacute;culo</div></td>
	<td><div align="center" class="normalNegrita">Cantidad</div></td>
	<td><div align="center" class="normalNegrita">Precio </div></td>
    <td><div align="center" class="normalNegrita">Fecha Recepci&oacute;n</div></td>
    <td><div align="center" class="normalNegrita">Opci&oacute;n</div></td>
 </tr>

 <tr>
    <td>
	<div align="center" class="peq">
	<span class="normal">
	<input type="text" name="articulo" id="articulo" class="normalNegro" size="60">
	</span>
	</div>	
		</td>
 	<td><div align="center" class="peq"><input name="cantidad" type="text" class="normalNegro" id="cantidad" onKeyUp="javascript: validar(this,0)" size="4" maxlength="6"></div></td>
	<td><div align="center"><input name="precio" align="right" type="text" class="normalNegro" id="precio" onKeyPress="return acceptFloat(event)" value="0.00" size="8" maxlength="10"/></div></td>
    <td  align="center" class="normal"><div align="center">
        <input type="text" size="9" id="hid_desde_itin" name="hid_desde_itin" class="normalNegro" readonly/>
		<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_desde_itin');" title="Show popup calendar">
		<img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" width="25" height="20" alt="Open popup calendar"/></a></div></td>
	<td><div align="center" class="normal"><a href="javascript: add_factura_head('factura_head','0'); " class="normal">Agregar	</a></div></td>
  </tr>
  </table>
<table class="tablaalertas" id="table_factura_head"  border="0" style="width: 100%; display: none;" >
	<tbody id="body_factura_head" class="normal">
	<tr>
		<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Id</span></th>
		<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Nombre del
			art&iacute;culo</span></th>
		<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Cantidad</span></th>
		<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Precio</span></th>
		<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Fecha</span></th>
		<th class="header" style="background-color:#C3ECCC;"><span class="normalNegroNegrita">Opci&oacute;n</span></th>
	</tr>	
	</tbody> 
</table>
 
 
 	</td>
  </tr>
  
  
  <tr>
    <td height="10" valign="middle" >	  </td>
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
     <input  type="hidden" name="total" id="total" value="0" />
</form>
</body>
</html>
<?php pg_close($conexion);?>





<?php /*


	<select name="articulo" id="articulo" class="normalNegro">
      <option value="0">[Seleccione]</option>
      <?php
		$i=0;
     	if (isset($_REQUEST['id_arti'])){   
        $sql_d="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','t1.id,nombre,unidad_medida','t1.id=t2.id and esta_id=1 and id_tipo=1 and t1.id<>'||'''$arti''','nombre',1) resultado_set(id varchar, nombre varchar, unidad_medida varchar)";
 	    echo("
			 <script language='javascript'>
				var arti = new Array(); 
				arti[0]='$arti_id';
				arti[1]='$arti_descripcion';
				arti[2]='$medida';
				ori[$i]=arti;
				</script>
			");
			$i++;
	?>
	 <option value="<?=$arti_id?>" selected="selected"><?=$arti_descripcion?></option>
	<?}else{	
	      $sql_d="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','t1.id,nombre,unidad_medida','t1.id=t2.id and esta_id=1 and id_tipo=1','nombre',1) resultado_set(id varchar, nombre varchar, unidad_medida varchar)";
	} 
	    echo "<br>";
	    $resultado_set_d=pg_query($conexion,$sql_d) or die("Error al mostrar");
	    #Mostramos los resultados obtenidos
	   
	    while($rowd=pg_fetch_array($resultado_set_d)) 
	    { 
 		 $arti_id=$rowd['id'];
		 $arti_descripcion=$rowd['nombre'];
		 $unme_id=$rowd['unidad_medida'];
		 
		 echo("
				<script language='javascript'>
				var arti = new Array(); 
				arti[0]='$arti_id';
				arti[1]='$arti_descripcion';
				arti[2]='$unme_id';
				ori[$i]=arti;
				</script>
			");
			$i++;	?>
        <option value="<?=$arti_id?>"><?=$arti_descripcion?>:<?=$unme_id?></option>
   <?php } ?>
       </select> */ ?>