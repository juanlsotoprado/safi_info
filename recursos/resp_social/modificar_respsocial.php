<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
include(dirname(__FILE__) . '/../../init.php');
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush();
  
$key = trim(utf8_decode($_REQUEST["ers"]));

$querymarca="
select
bmarc_id,
bmarc_nombre
from
sai_bien_marca
where
esta_id=1
order by
bmarc_nombre
";
$resultado4 = pg_exec ( $conexion, $querymarca );
$indice4 = 0;
$stringobj4;
while ($row4 = pg_fetch_array ( $resultado4 ))
{
	$stringobj4  [$indice4]['id_marca'] = $row4['bmarc_id'];
	$stringobj4  [$indice4]['nombre_marca'] = utf8_encode($row4['bmarc_nombre']);
	$indice4 ++;
}
  
$queryarti = "
	select
		id,
		nombre
		from
		sai_item
	where
		id_tipo = 4
";
$resultado5 = pg_exec ( $conexion, $queryarti );
$indice5 = 0;
$stringobj5;
while ($row5 = pg_fetch_array ( $resultado5 ))
{
	$stringobj5  [$indice5]['id_arti'] = $row5['id'];
	$stringobj5  [$indice5]['nombre_arti'] = utf8_encode($row5['nombre']);
	$indice5 ++;
}

//query de actas
$queryacta=
"
		SELECT
		distinct(arti_id) as articulos,
		to_char(t3.fecha_registro, 'DD/MM/YYYY') AS fecha_recepcion,
		t2.sec_id,
		t2.ubicacion,
		monto_recibido,
		t4.prov_nombre,
		t3.observaciones,
		nombre,
		t2.cantidad,
		t5.bmarc_nombre,
		t5.bmarc_id,
		modelo,
		serial
		FROM
		sai_arti_inco_rs t3,
		sai_arti_inco_rs_item t2,
		sai_item t1,
		sai_proveedor_nuevo t4,
		sai_bien_marca t5
		WHERE
		t3.acta_id='".$key."' and
		t3.acta_id=t2.acta_id and
		arti_id=t1.id and
  		t4.prov_id_rif=t3.proveedor and
  		t5.bmarc_id=t2.marca_id
		order by
		nombre
";
$resultado3 = pg_exec ( $conexion, $queryacta );
$indice3 = 0;
$stringobj3;
while ($row3 = pg_fetch_array ( $resultado3 ))
{


	$stringobj3  ['acta'] = $key;
	$stringobj3  ['fecha_recepcion'] = $row3['fecha_recepcion'];
	$stringobj3  ['ubicacion'] = $row3 ['ubicacion'];
	$stringobj3  ['monto_recibido'] = $row3['monto_recibido'];
	$stringobj3  ['prov_nombre'] = $row3['prov_nombre'];
	$stringobj3  ['observaciones'] = utf8_encode($row3['observaciones']);



	$stringobj3  ['idarticulo']  [$indice3] = $row3['articulos'];
	$stringobj3  ['sec_id']  [$indice3] = $row3['sec_id'];
	$stringobj3  ['nombre'] [$indice3] = $row3['nombre'];
	$stringobj3  ['cantidad'] [$indice3] = $row3['cantidad'];
	$stringobj3  ['marca_nombre'] [$indice3] = $row3['bmarc_nombre'];
	$stringobj3  ['marca_id'] [$indice3] = $row3['bmarc_id'];
	$stringobj3  ['modelo'] [$indice3] = $row3['modelo'];
	$stringobj3  ['serial'] [$indice3] = $row3['serial'];
	$indice3 ++;

}

//array de serial
$queryserial=
"SELECT
   t1.id,
   t1.nombre,
   t2.unidad_medida,
   t3.modelo,
   t3.sec_id,	
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
   and (t3.serial!='')
   and t3.sec_id not in ('".implode($stringobj3['sec_id'], "','")."')
   GROUP BY
   t1.id,
   t1.nombre,
   t2.unidad_medida,
   t3.modelo,
   t3.sec_id,
   t3.serial,
   t3.ubicacion,
   t4.bmarc_nombre,
   t4.bmarc_id";

//echo $queryserial; 

$resultado = pg_exec ( $conexion, $queryserial );
$numeroFilas = pg_num_rows ( $resultado );
$indice = 0;
$stringobj;

while ( $row = pg_fetch_array ( $resultado ) ) {
	$stringobj [strtoupper(utf8_encode($row['serial'])).$row['id'].$row['bmarc_id'].utf8_encode($row['modelo'])] = strtoupper(utf8_encode( $row ['serial'] ));
	$indice ++;
}


 //echo "<pre>"; print_r($stringobj); echo "</pre>";

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
		<title>SAFI::Registro del Almac&eacute;n:.</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

		<link rel="stylesheet" href="../../css/safi0.2.css" type="text/css" media="all" />
		<link rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all" />
		<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet" />
		<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>" rel="stylesheet" type="text/css" charset="utf-8"/>
		<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </script>
		<script language="JavaScript" src="../../js/lib/actb.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>" charset="utf-8"></script>
		<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/jquery-ui-1.8.13.custom.min.js';?>" charset="utf-8"></script>
		<script type="text/javascript">
		
			g_Calendar.setDateFormat('dd/mm/yyyy');
			var stringobj4 = <?php echo json_encode($stringobj4,JSON_FORCE_OBJECT); ?>;
			var stringobj5 = <?php echo json_encode($stringobj5,JSON_FORCE_OBJECT); ?>;
			
			var items2 = new Array();
			var index2 = 0;
			var items = new Array();
			var index = 0;
			
			var listado_bienes = new Array();
			var listado_mobiliario = new Array();
			var ori = new Array();
			var factura_head = new Array();
			var listado_disponibilidad = new Array();
			var listado_activos = new Array();
			var idArticulo = 1;
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
			var idmarca;
			var nombremarca;
			var idarti;
			var nombrearti;

			var ubicacion = <?=$stringobj3['ubicacion'];?>;
			var stringobj = <?php echo json_encode($stringobj); ?>;

			$.each(stringobj4,function(id, params){
				items[index++] = {
						id: params.id,
						value: params.nombre_marca,
						marca: params.nombre_marca,
						idmarca: params.id_marca
				};

			});
		
			$.each(stringobj5,function(id, params){
				items2[index2++] = {
						id: params.id,
						value: params.nombre_arti,
						artinombre: params.nombre_arti,
						idnombre: params.id_arti
				};
			});

$().ready(function(){

	$("#txt_ubica").val(ubicacion);
	$("[name='arraymodi[articulo][]']").keyup(function(e){
		idArticulo=0;
		    if(e.keyCode == 46 || e.keyCode == 8)
			{
		    	$(this).val("");
			}
	});
	$("[name='arraymodi[articulo][]']").focus(function() {
		  //alert($(this).val());
	});

});

	function autocompletarArti(objInput)
	{
		
		$(objInput).autocomplete({
			source: items2, 
			minLength: 1,
		    select: function(event,ui)
		    {
			    	$(this).parents("tr.trCaso").find("input[type='hidden'][name = 'arraymodi\[idarticulo\]\[\]']").val(ui.item.idnombre);
			    	idArticulo = 1;
			    	return true;
		    }
		});
	}

	function autocompletarMarca(objInput)
	{
		
		$(objInput).autocomplete({
			source: items, 
			minLength: 1,
		    select: function(event,ui)
		    {
		    	$(this).parents("tr.trCaso").find("input[type='hidden'][name = 'arraymodi\[marca_id\]\[\]']").val(ui.item.idmarca);
		   		idmarca = ui.item.idmarca;
				nombremarca = ui.item.marca;

		    return true;
		            
		    }
		});
	}

var factura = new Array();
var factura_head = new Array();
var marcas = new Array();
var ori = new Array();

function eliminarArticulo(objA){

	var borrar = $("#sec_id").val();
	
	
	   objTrs = $(objA).parents("tr.trCaso");

	   objTrs.hide(200).remove();

	if($("#body_factura_head > tr").length < 2){
		
		$("#table_factura_head").hide(200);
				
	}  

}
function cambiarcantidad(objeto){
	$(objeto).parents("tr.trCaso").find("input[type='text'][name = 'arraymodi\[cantidad\]\[\]']").val(1);
	$(objeto).parents("tr.trCaso").find("input[type='text'][name = 'arraymodi\[cantidad\]\[\]']").attr("readonly","readonly");
	if($(objeto).parents("tr.trCaso").find("input[type='text'][name = 'arraymodi\[serial\]\[\]']").val() == "")
	{
		$(objeto).parents("tr.trCaso").find("input[type='text'][name = 'arraymodi\[cantidad\]\[\]']").attr("readonly",false).val("");
	}	
}

function acceptFloat(evt){	
	// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57	
	var nav4 = window.Event ? true : false;
	var key = nav4 ? evt.which : evt.keyCode;	
	return (key <= 13 || (key >= 48 && key <= 57) || key == 46);
}

function revisar()
{
	var idart = new Array();
	var seri = new Array();
	var aux = 0;
	var aux2= 0;
	var cant = 0;
	var idarti = 0;
	var marc = 0;
	var seri2 = 0;
	var cual ="";
	$("tr.trCaso").each(function(){
		if($(this).find("input[type='text'][name = 'arraymodi[serial][]']").val() != "") 
		{
			$(this).find("input[type='text'][name = 'arraymodi[cantidad][]']").val(1);
		}	
	});

	if(idArticulo == 0){
		alert("Seleccione un art\u00EDculo");
		return;
	}
	$("tr.trCaso").each(function(){
		if(stringobj[$(this).find("input[type='text'][name = 'arraymodi\[serial\]\[\]']").val().toUpperCase() + $(this).find("input[type='hidden'][name = 'arraymodi\[idarticulo\]\[\]']").val() + $(this).find("input[type='hidden'][name = 'arraymodi\[marca_id\]\[\]']").val() + $(this).find("input[type='text'][name = 'arraymodi\[modelo\]\[\]']").val()])
		{
			seri2++;
			cual = $(this).find("input[type='text'][name = 'arraymodi\[serial\]\[\]']").val().toUpperCase();
		}
	});
	if(seri2 > 0){
		alert("Serial "+cual+" existe en la base de datos");
		return;
	}
	
	$("tr.trCaso").each(function(index){
		var objSerial = $(this).find("input[type='text'][name = 'arraymodi[serial][]']");
		objSerial.val(objSerial.val().toUpperCase());	
		idart[index] = $(this).find("input[type='hidden'][name = 'arraymodi[idarticulo][]']").val() + $(this).find("input[type='hidden'][name = 'arraymodi[marca_id][]']").val() + $(this).find("input[type='text'][name = 'arraymodi[modelo][]']").val() + $(this).find("input[type='text'][name = 'arraymodi[serial][]']").val();
	});

	for (var i=0;i<idart.length;i++)
	{
		for (var j=i+1 ;j<idart.length;j++){
			if (idart[i] === idart[j] ) 
			{
				aux++;
			}
		}
	}
	
	if(aux > 0){
		alert("No pueden agregarse art\u00EDculos repetidos");
		return;
	}

	$("tr.trCaso").each(function(){
		if($(this).find("input[type='hidden'][name = 'arraymodi[idarticulo][]']").val() == "")
		{
			idarti = 1;
		}	
	});

	if(idarti > 0){
		alert("Seleccione el nombre del art\u00EDculo");
		return;
	}
	
	$("tr.trCaso").each(function(){
		if($(this).find("input[type='text'][name = 'arraymodi[cantidad][]']").val() == "" || $(this).find("input[type='text'][name = 'arraymodi[cantidad][]']").val() == 0 || isNaN($(this).find("input[type='text'][name = 'arraymodi[cantidad][]']").val()) == true)
		{
			cant = 1;
		}	
	});

	if(cant > 0){
		alert("Indique la cantidad");
		return;
	}
	
	$("tr.trCaso").each(function(){
		if($(this).find("input[type='hidden'][name = 'arraymodi[marca_id][]']").val() == "")
		{
			marc = 1;
		}	
	});

	if(marc > 0){
		alert("Seleccione el nombre de la marca");
		return;
	}
	
	if(document.form.hid_desde_itin.value=="")
	{
  		alert("Debe seleccionar la fecha de recepci\u00F3n del art\u00EDculo");
  		document.form.hid_desde_itin.focus();
  		return;
	}
	
	if(document.form.txt_ubica.value=="0")
	{
		alert("Debe seleccionar la ubicaci\u00F3n del art\u00EDculo");
		document.form.txt_ubica.focus();
		return
	}

	if(document.form.monto_recibido.value=="")
	{
		window.alert("Debe especificar el monto recibido por parte del proveedor");
		document.form.monto_recibido.focus();
		return
	}

	if (document.form.proveedor.value==""){
		   alert("Debe indicar el nombre del proveedor");
		   document.form.proveedor.focus();
		   return
	}
	if(!stringobj2[$("#proveedor").val()]){
		
		alert("Seleccione un proveedor v\u00e1lido");
		return
		
	}

	if(document.form.observaciones.value=='')
	{
		window.alert("Debe indicar observaciones del registro");
		document.form.observaciones.focus();
		return
	}
		
	document.form.txt_arreglo_factura_head.value="";
	
	for(i=0;i<factura_head.length;i++)
	{
		for (j=0; j<8; j++)
		{
			document.form.txt_arreglo_factura_head.value+=factura_head[i][j];
		   	
			if  ( (i<(factura_head.length-1)  ) ||  (i==(factura_head.length-1) &&  (j!=7) ) )
			{
			document.form.txt_arreglo_factura_head.value+="�";
			}
		}	
			
	}

	
	if(confirm("Datos introducidos de manera correcta. \u00BFEst\u00e1 seguro que desea continuar?"))
  {	
	 document.form.action="modificar_respsocialAccion.php";
	 document.form.submit();
  }	
  
}

function add_articulo()
{
	var tbody = $('#body_factura_head')[0];
	var fila = document.createElement("tr");
	fila.className='normalNegro trCaso';

	//nombre del articulo

	var columna1 = document.createElement("td");
	columna1.setAttribute("valign","top");
	columna1.setAttribute("align","center");
	columna1.setAttribute("style","font-size:10px");
	
	var input1 = document.createElement("input");
	input1.setAttribute("type","text");
	input1.setAttribute("size",40);
	input1.setAttribute("name","arraymodi[articulo][]");
	input1.setAttribute("id","arti_nombre");
	columna1.appendChild(input1);

	autocompletarArti(input1);

	var input12 = document.createElement("input");
	input12.setAttribute("type","hidden");
	input12.setAttribute("name","arraymodi[idarticulo][]");
	//input12.setAttribute("class","hidden_idarti");
	//input12.setAttribute("id","arti_id[]");
	columna1.appendChild(input12);

	//cantidad del articulo
		
	var columna2 = document.createElement("td");
	columna2.setAttribute("valign","top");
	columna2.setAttribute("align","center");
	columna2.setAttribute("style","font-size:10px");
	
	var input2 = document.createElement("input");
	input2.setAttribute("type","text");
	input2.setAttribute("size",4);
	input2.setAttribute("name","arraymodi[cantidad][]");
	input2.setAttribute("id","cantidad");
	input2.setAttribute("class","normalNegro");
	columna2.appendChild(input2);

	//marca nombre y id
		
	var columna3 = document.createElement("td");
	columna3.setAttribute("valign","top");
	columna3.setAttribute("align","center");
	columna3.setAttribute("style","font-size:10px");
	
	var input3 = document.createElement("input");
	input3.setAttribute("type","text");
	input3.setAttribute("name","arraymodi[marca_nombre][]");
	input3.setAttribute("id","marca");
	columna3.appendChild(input3);

	autocompletarMarca(input3);

	var input32 = document.createElement("input");
	input32.setAttribute("type","hidden");
	input32.setAttribute("name","arraymodi[marca_id][]");
	input32.setAttribute("id","marca_nombre");
	columna3.appendChild(input32);

	//modelo
		
	var columna4 = document.createElement("td");
	columna4.setAttribute("valign","top");
	columna4.setAttribute("align","center");
	columna4.setAttribute("style","font-size:10px");
	
	var input4 = document.createElement("input");
	input4.setAttribute("type","text");
	input4.setAttribute("name","arraymodi[modelo][]");
	input4.setAttribute("id","modelo");
	input4.setAttribute("class","normalNegro");
	columna4.appendChild(input4);

	//serial y sec_id
	
	var columna5 = document.createElement("td");
	columna5.setAttribute("valign","top");
	columna5.setAttribute("align","center");
	columna5.setAttribute("style","font-size:10px");
	
	var input5 = document.createElement("input");
	input5.setAttribute("type","text");
	input5.setAttribute("name","arraymodi[serial][]");
	input5.setAttribute("id","serial");
	columna5.appendChild(input5);

	var input52 = document.createElement("input");
	input52.setAttribute("type","hidden");
	input52.setAttribute("name","arraymodi[sec_id][]");
	input52.setAttribute("id","sec_id");
	columna5.appendChild(input52);
	
	//eliminar
	
	var columna8 = document.createElement("td");
	columna8.setAttribute("valign","top");
	columna8.setAttribute("align","center");
	columna8.className = 'link';


	objDiv = document.createElement("div");
	objDiv.setAttribute("class","botonEliminar");
	columna8.appendChild(objDiv);					 		

	fila.appendChild(columna1);
	fila.appendChild(columna2);
	fila.appendChild(columna3);
	fila.appendChild(columna4);
	fila.appendChild(columna5);
	fila.appendChild(columna8);	
	tbody.appendChild(fila);

	suma = parseInt($("#total").val())+1;
	$("#total").val(suma);
	
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
	$(input5).bind('keyup', function(){
		cambiarcantidad($(this));
	});
	$("[name='arraymodi[articulo][]']").keyup(function(){
		idArticulo=0;
	});		
}


</script>
</head>
<body>
	<form name="form" method="post" enctype="multipart/form-data"
		id="form1">
		<br /> <br />
		<table width="1000" border="0" align="center"
			background="../../imagenes/fondo_tabla.gif" class="tablaalertas"
			style="padding-bottom: 10px;">
			<tr class="td_gray">
				<td width="1000" height="15" valign="middle"><span
					class="normalNegroNegrita">Modificar acta: <?=$key;?> </span></td>
			</tr>
			<tr>
				<td>
					<table width="1020" border="0" align="center" cellpadding="1"
						cellspacing="1" class="tablaalertas" style="padding-bottom: 15px;">
						<div class="normalNegroNegrita" align="center"
							style="background-color: #F0F0F0;">Datos generales</div>
						<tr>
							<td colspan="6" height="5"></td>
						</tr>
						<tr width="100%">
							<td class="normalNegrita">&ensp;&ensp;Fecha de recepci&oacute;n</td>
							<td><input type="text" size="8" id="hid_desde_itin" value="<?=$stringobj3['fecha_recepcion'];?>"
								name="hid_desde_itin" class="normalNegro" readonly /> <a
								href="javascript:void(0);"
								onclick="g_Calendar.show(event, 'hid_desde_itin');"
								title="Mostrar Calendario" style="display: on" id="fecha"> <img
									src="../../js/lib/calendarPopup/img/calendar.gif"
									class="cp_img" width="25" height="20" alt="Open popup calendar" /></a></td>
							<td class="normalNegrita">Ubicaci&oacute;n:</td>
							<td class="normal"><select name="txt_ubica" id="txt_ubica"
								class="normalNegro">
									<option value="0" selected>Seleccione...</option>
									<option value="1">Torre</option>
									<option value="2">Galp&oacute;n</option>
							</select></td>
							<td class="normalNegrita">Monto recibido:</td>
							<td><div align="left" class="normal">
									<input name="monto_recibido" align="right" type="text"
										class="normalNegro" id="monto_recibido" value="<?=$stringobj3['monto_recibido'];?>"
										onKeyPress="return acceptFloat(event)" size="8" maxlength="10" />
								</div></td>
						</tr>
						<tr width="100%">
							<td class="normalNegrita">&ensp;&ensp;Proveedor:</td>
							<td>
							<input class="normalNegro" autocomplete="off" size="40"
								type="text" id="proveedor" name="proveedor" value="<?=$stringobj3['prov_nombre'];?>"
								<?php if($rif!="" || $codigo!=""){echo "disabled='disabled'";}?> />	
							</td>
		  <?php
			$query = "SELECT prov_nombre FROM sai_proveedor_nuevo ORDER BY prov_nombre";
			$resultado = pg_exec($conexion, $query);
			$resultado2 = pg_exec($conexion, $query);
			$numeroFilas = pg_num_rows($resultado);
			$arreglo = "";
			while($row=pg_fetch_array($resultado))
			{
				$arreglo .= "'".$row["prov_nombre"]."',";	
			}
			$arreglo = substr($arreglo, 0, -1);
			 
			 
			 //creando arreglo json para proveedores
			 $indice2 = 0;
			 $stringobj2;
			 while($row2 = pg_fetch_array($resultado2)) {
			 	// array de seriales
			 
			 	$stringobj2 [utf8_encode($row2 ['prov_nombre']) ] = utf8_encode ( $row2 ['prov_nombre'] );
			 
			 	$indice2 ++;
			 }
			 //echo "<pre>"; print_r($stringobj2); echo "</pre>"; 
			?>
			<script>
				var proveedores = new Array(<?= $arreglo?>);
				var stringobj2 = <?php echo json_encode($stringobj2); ?>;
				actb(document.getElementById('proveedor'),proveedores);
			</script>

							<td class="normalNegrita">Observaciones:</td>
							<td colspan="3"><div align="left" class="normal">
									<input type="text" name="observaciones" id="observaciones" value="<?=utf8_decode($stringobj3['observaciones']);?>" size="25"></input>
									<input type="hidden" name="key" id="key" value="<?=$key;?>"></input>
								</div></td>

						</tr>
					</table>

					<table width="1020" border="0" align="center" cellpadding="1"
						cellspacing="1" class="tablaalertas" id="factura_head"
						style="padding: 10px 0px 15px 0px;">

						<div class="normalNegroNegrita" align="center" style="background-color: #F0F0F0;">Listado de art&iacute;culos</div>
						
						<tr>
							<td><div align="center" class="normalNegrita">Art&iacute;culo</div></td>
							<td><div align="center" class="normalNegrita">Cantidad</div></td>
							<td><div align="center" class="normalNegrita">Marca</div></td>
							<td><div align="center" class="normalNegrita">Modelo</div></td>
							<td><div align="center" class="normalNegrita">Serial</div></td>
							
							<td><div class="botonAgregar" onclick="add_articulo();"></div></td>
							
						</tr>
						<tr>
							<td colspan="6" style="height: 5px;"></td>
						</tr>

						

						
						<tbody id="body_factura_head" class="normalNegro trCaso">
						<?php for($i=0;$i<$indice3;$i++){?>
						

							<script type="text/javascript">
							var tbody = $('#body_factura_head')[0];
							var fila = document.createElement("tr");
							fila.className='normalNegro trCaso';

							//nombre del articulo

							var columna1 = document.createElement("td");
							columna1.setAttribute("valign","top");
							columna1.setAttribute("align","center");
							columna1.setAttribute("style","font-size:10px");
							
							var input1 = document.createElement("input");
					 		input1.setAttribute("type","text");
					 		input1.setAttribute("size",40);
					 		input1.setAttribute("name","arraymodi[articulo][]");
					 		input1.value="<?=$stringobj3['nombre'][$i];?>";
					 		input1.setAttribute("id","arti_nombre");
					 		columna1.appendChild(input1);

					 		autocompletarArti(input1);

							var input12 = document.createElement("input");
							input12.setAttribute("type","hidden");
							input12.setAttribute("name","arraymodi[idarticulo][]");
							input12.value="<?=$stringobj3['idarticulo'][$i];?>";
					 		columna1.appendChild(input12);

					 		//cantidad del articulo
					 		
					 		var columna2 = document.createElement("td");
					 		columna2.setAttribute("valign","top");
					 		columna2.setAttribute("align","center");
					 		columna2.setAttribute("style","font-size:10px");
							
							var input2 = document.createElement("input");
							input2.setAttribute("type","text");
							input2.setAttribute("size",4);
							input2.setAttribute("name","arraymodi[cantidad][]");
							input2.value="<?=$stringobj3['cantidad'][$i];?>";
							input2.setAttribute("id","cantidad");
							input2.setAttribute("class","normalNegro");
					 		columna2.appendChild(input2);

					 		//marca nombre y id
					 		
					 		var columna3 = document.createElement("td");
					 		columna3.setAttribute("valign","top");
					 		columna3.setAttribute("align","center");
					 		columna3.setAttribute("style","font-size:10px");
							
							var input3 = document.createElement("input");
							input3.setAttribute("type","text");
							input3.setAttribute("name","arraymodi[marca_nombre][]");
							input3.value="<?=$stringobj3['marca_nombre'][$i];?>";
							input3.setAttribute("id","marca");
					 		columna3.appendChild(input3);

					 		autocompletarMarca(input3);

							var input32 = document.createElement("input");
							input32.setAttribute("type","hidden");
							input32.setAttribute("name","arraymodi[marca_id][]");
							input32.value="<?=$stringobj3['marca_id'][$i];?>";
							input32.setAttribute("id","marca_nombre");
					 		columna3.appendChild(input32);

					 		//modelo
					 		
					 		var columna4 = document.createElement("td");
					 		columna4.setAttribute("valign","top");
					 		columna4.setAttribute("align","center");
					 		columna4.setAttribute("style","font-size:10px");
							
							var input4 = document.createElement("input");
							input4.setAttribute("type","text");
							input4.setAttribute("name","arraymodi[modelo][]");
							input4.value="<?=$stringobj3['modelo'][$i];?>";
							input4.setAttribute("id","modelo");
							input4.setAttribute("class","normalNegro");
							columna4.appendChild(input4);

							//serial y sec_id
							
							var columna5 = document.createElement("td");
							columna5.setAttribute("valign","top");
							columna5.setAttribute("align","center");
							columna5.setAttribute("style","font-size:10px");
							
							var input5 = document.createElement("input");
							input5.setAttribute("type","text");
							input5.setAttribute("name","arraymodi[serial][]");
							input5.value="<?=$stringobj3['serial'][$i];?>";
							//input5.setAttribute("id","serial");
					 		columna5.appendChild(input5);

							var input52 = document.createElement("input");
							input52.setAttribute("type","hidden");
							input52.setAttribute("name","arraymodi[sec_id][]");
							input52.value="<?=$stringobj3['sec_id'][$i];?>";
							input52.setAttribute("id","sec_id");
							columna5.appendChild(input52);
							
					 		//eliminar
							
							var columna8 = document.createElement("td");
					 		columna8.setAttribute("valign","top");
					 		columna8.setAttribute("align","center");
					 		columna8.className = 'link';


					 		objDiv = document.createElement("div");
					 		objDiv.setAttribute("class","botonEliminar");
					 		columna8.appendChild(objDiv);					 		
					 		
					 		fila.appendChild(columna1);
					 		fila.appendChild(columna2);
					 		fila.appendChild(columna3);
					 		fila.appendChild(columna4);
					 		fila.appendChild(columna5);
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

							$(input5).bind('keyup', function(){
					 				cambiarcantidad($(this));	
					 		});
							</script>

							<input type="hidden" name="arraysec_id[]" value="<?=$stringobj3['sec_id'][$i];?>" />
						<?php } ?>

						</tbody>

						<input type="hidden" name="total" id="total" value="<?=$i;?>" />
					</table>
				</td>
			</tr>
			<tr>
				<td height="10" valign="middle"></td>
			</tr>
			<tr>
				<td height="16" colspan="3">
				
			</td>
			</tr>
							<tr>
				<td colspan="2" align="center"><input type="button"
					value="Modificar" onclick="javascript:revisar();"
					class="normalNegro"></input></td>
			</tr>
		</table>
		<input type="hidden" name="txt_arreglo_factura_head"
			id="txt_arreglo_factura_head" />
	</form>
</body>
</html>