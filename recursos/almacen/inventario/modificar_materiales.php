<?php
ob_start ();
session_start ();
require_once ("../../../includes/conexion.php");
include (dirname ( __FILE__ ) . '/../../../init.php');
if (empty ( $_SESSION ['login'] ) || ($_SESSION ['registrado'] != "registrado")) {
	header ( 'Location:../../../index.php', false );
	ob_end_flush ();
	exit ();
}
ob_end_flush ();

$key = trim ( utf8_decode ( $_REQUEST ["emat"] ) );

// dependencias
$querydependencias = "
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

$resultado4 = pg_exec ( $conexion, $querydependencias );
$indice4 = 0;
$stringobj4;
while ( $row4 = pg_fetch_array ( $resultado4 ) ) {
	$stringobj4 [$indice4] ['depe_id'] = $row4 ['depe_id'];
	$stringobj4 [$indice4] ['depe_nombre'] = utf8_encode ( $row4 ['depe_nombre'] );
	$indice4 ++;
}

/*
 * echo "<pre>"; echo print_r($stringobj4); echo "</pre>";
 */

// query de actas
$queryacta = "
		SELECT
		distinct(arti_id) as articulos,
		t1.nombre,
		t6.unidad_medida,
		t2.cantidad,
		t2.precio,
		t5.depe_nombre,
		t5.depe_id,
		to_char(t2.alm_fecha_recepcion, 'DD/MM/YYYY') AS alm_fecha_recepcion,
		t2.alm_id,
		t2.ubicacion,
		t4.prov_nombre
		FROM
		sai_item t1,
		sai_arti_inco t3,
		sai_arti_almacen t2,
		sai_proveedor_nuevo t4,
		sai_dependenci t5,
		sai_item_articulo t6
		WHERE
		t3.acta_id='" . $key . "' and
		t3.acta_id=t2.acta_id and
		t2.arti_id=t1.id and
  		t4.prov_id_rif=t3.proveedor and
  		t5.depe_id = t2.depe_solicitante and
  		t1.id = t6.id
		order by
		t1.nombre
";
$resultado3 = pg_exec ( $conexion, $queryacta );
$indice3 = 0;
$stringobj3;
while ( $row3 = pg_fetch_array ( $resultado3 ) ) {
	// datos generales
	
	$stringobj3 ['acta'] = $key;
	$stringobj3 ['ubicacion'] = $row3 ['ubicacion'];
	$stringobj3 ['prov_nombre'] = utf8_encode($row3 ['prov_nombre']);
	$stringobj3 ['depe_nombre']  = utf8_encode($row3 ['depe_nombre']);
	$stringobj3 ['depe_id']  = $row3 ['depe_id'];
	
	// materiales
	
	$stringobj3 ['idarticulo'] [$indice3] = $row3 ['articulos'];
	$stringobj3 ['unidad_medida'] [$indice3] = $row3 ['unidad_medida'];
	$stringobj3 ['alm_id'] [$indice3] = $row3 ['alm_id'];
	$stringobj3 ['nombre'] [$indice3] = utf8_encode($row3 ['nombre']);
	$stringobj3 ['cantidad'] [$indice3] = $row3 ['cantidad'];
	$stringobj3 ['precio'] [$indice3] = $row3 ['precio'];
	$stringobj3 ['alm_fecha_recepcion'] [$indice3] = $row3 ['alm_fecha_recepcion'];
	$indice3 ++;
}
/*
 * echo "<pre>"; echo print_r($stringobj3); echo "</pre>";
 */
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
<link rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all" />
<title>SAFI::Registro del Almac&eacute;n:.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../../../css/safi0.2.css" type="text/css" media="all" />
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </script>
<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />
<script language="JavaScript" src="../../../js/lib/actb.js"></script>
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet" />
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript" src="../../../js/lib/jquery/plugins/jquery.min.js"></script>
<script type="text/javascript" src="../../../js/lib/jquery/plugins/ui.min.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>

<script language="JavaScript" type="text/JavaScript">
var factura = new Array();
var factura_head = new Array();
var dependencia = new Array();
var ubicacion = <?=$stringobj3['ubicacion'];?>;

var stringobj4 = <?php echo json_encode($stringobj4,JSON_FORCE_OBJECT); ?>;
var stringobj5 = <?php echo json_encode($stringobj5,JSON_FORCE_OBJECT); ?>;

var items2 = new Array();
var index2 = 0;
var items = new Array();
var index = 0;
var idArticulo = 1;
var a = 0;

function eliminarArticulo(objA){

	
	   objTrs = $(objA).parents("tr.trCaso");

	   objTrs.hide(200).remove();

	if($("#body_factura_head > tr").length < 2){
		
		$("#table_factura_head").hide(200);
				
	}  

}


$().ready(function(){

	//alert(JSON.stringify(stringobj5));

	$("#txt_ubica").val(ubicacion);
	$("[name='arraymodi[articulo][]']").keyup(function(e){
		idArticulo=0;
		    if(e.keyCode == 46 || e.keyCode == 8)
			{
		    	$(this).val("");
			}
	});

	$.each(stringobj4,function(id, params){
		items[index++] = {
				id: params.id,
				value: params.depe_nombre,
				dependencia: params.depe_nombre,
				iddependencia: params.depe_id
		};

	});


	$("#depe_nombre").autocomplete({
		source: items, 
		minLength: 1,
	    select: function(event,ui)
	    {
	    	$("#depe_id").val(ui.item.iddependencia);
	    	//iddependencia = ui.item.depe_id;
	    	//dependencias = ui.item.dependencia;

	    return true;
	            
	    }
	});

});

$.each(stringobj5,function(id, params){
	items2[index2++] = {
			id: params.id,
			value: params.nombre_arti,
			artinombre: params.nombre_arti,
			idnombre: params.id_arti,
			unidad: params.unidad_medida
	};
});

function acceptFloat(evt){	
	// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57	
	var nav4 = window.Event ? true : false;
	var key = nav4 ? evt.which : evt.keyCode;	
	return (key <= 13 || (key >= 48 && key <= 57) || key == 46);
}

function autocompletarArti(objInput)
{
	$(objInput).autocomplete({
		source: items2, 
		minLength: 1,
	    select: function(event,ui)
	    {
		    	$(this).parents("tr.trCaso").find("input[type='hidden'][name = 'arraymodi\[idarticulo\]\[\]']").val(ui.item.idnombre);
		    	$(this).parents("tr.trCaso").find("input[type='hidden'][name = 'arraymodi\[unidad\]\[\]']").val(ui.item.unidad);
		    	idArticulo = 1;
		    	return true;
	    }
	});
}

function add_articulo(id,tipo)
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
	input1.setAttribute("size",50);
	input1.setAttribute("name","arraymodi[articulo][]");
	input1.setAttribute("id","arti_nombre");
	columna1.appendChild(input1);

	autocompletarArti(input1);

	var input12 = document.createElement("input");
	input12.setAttribute("type","hidden");
	input12.setAttribute("name","arraymodi[idarticulo][]");
	columna1.appendChild(input12);

	var input13 = document.createElement("input");
	input13.setAttribute("type","hidden");
	input13.setAttribute("name","arraymodi[unidad][]");
	columna1.appendChild(input13);

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

	//precio
		
	var columna3 = document.createElement("td");
	columna3.setAttribute("valign","top");
	columna3.setAttribute("align","center");
	columna3.setAttribute("style","font-size:10px");
	
	var input3 = document.createElement("input");
	input3.setAttribute("type","text");
	input3.setAttribute("size",5);
	input3.setAttribute("name","arraymodi[precio][]");
	input3.setAttribute("onKeyPress","return acceptFloat(event)");
	input3.setAttribute("id","precio");
	columna3.appendChild(input3);

	//imagen
	
	var columna6 = document.createElement("img");
	columna6.setAttribute("src","../../../js/lib/calendarPopup/img/calendar.gif");
	columna6.setAttribute("class","cp_img");
	columna6.setAttribute("width","25");
	columna6.setAttribute("onclick","g_Calendar.show(event, 'alm_fecha_recepcion"+(a)+"');");
	columna6.setAttribute("height","20");
	columna6.setAttribute("alt","Open popup calendar");

	//fecha
		
	var columna4 = document.createElement("td");
	columna4.setAttribute("valign","top");
	columna4.setAttribute("align","center");
	columna4.setAttribute("style","font-size:10px");
	
	var input4 = document.createElement("input");
	input4.setAttribute("type","text");
	input4.setAttribute("size",12);
	input4.setAttribute("name","arraymodi[alm_fecha_recepcion][]");
	input4.setAttribute("id","alm_fecha_recepcion"+(a++));
	input4.setAttribute("class","normalNegro");
	input4.setAttribute("readonly","readonly");
	columna4.appendChild(input4);

	var input52 = document.createElement("input");
	input52.setAttribute("type","hidden");
	input52.setAttribute("name","arraymodi[alm_id][]");
	input52.setAttribute("id","alm_id");
	columna4.appendChild(input52);

	
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
	fila.appendChild(columna6);
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
	$("[name='arraymodi[articulo][]']").keyup(function(){
		idArticulo=0;
	});
}

function revisar()
{
	var cant = 0;
	var preci = 0;
	var fec = 0;
	var idarti = 0;
	if($("#nombre").val() == ""){
		   alert("Debe indicar el nombre del proveedor");
		   return
	}
	if($("#txt_ubica").val() == 0){
		   alert("Seleccione ubicaci\u00F3n");
		   return
	}
	if(idArticulo == 0){
		alert("Seleccione un art\u00EDculo");
		return;
	}
	$("tr.trCaso").each(function(){
		if($(this).find("input[type='text'][name = 'arraymodi[alm_fecha_recepcion][]']").val() == "")
		{
			fec = 1;
		}	
	});

	if(fec > 0){
		alert("Indique la fecha de recepci\u00f3n");
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
		if($(this).find("input[type='text'][name = 'arraymodi[precio][]']").val() == "" || $(this).find("input[type='text'][name = 'arraymodi[precio][]']").val() == 0 || isNaN($(this).find("input[type='text'][name = 'arraymodi[precio][]']").val()) == true)
		{
			preci = 1;
		}	
	});

	if(preci > 0){
		alert("Indique el Precio");
		return;
	}
	if(idArticulo == 0)
	{
		alert("Seleccione un art\u00EDculo");
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

	if(confirm("Datos introducidos de manera correcta. \u00BFEst\u00E1 seguro que desea continuar?."))
  	{
	 document.form.action="modificar_materialesAccion.php";
	 document.form.submit();
	}	
  
}
</script>

</head>
<body>

	<form name="form" method="post" enctype="multipart/form-data" id="form1">
	
		<br />
		<br />
		<table width="1000" border="0" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td width="1000" height="15" valign="middle"><span class="normalNegroNegrita">Modificar Acta: <?=$key;?></span></td>
			</tr>
			<tr>
				<td>
					<table width="1020" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas">
						<tr>
							<td class="normalNegrita">Proveedor:</td>
							<td><input type="hidden" name="key" id="key" value="<?=$key;?>"></input><input class="normalNegro" size="40"
								type="text" id="nombre" name="nombre"
								value="<?=$stringobj3['prov_nombre'];?>"
								<?php if($rif!="" || $codigo!=""){echo "disabled='disabled'";}?> />
		  <?php
				$query = "SELECT prov_nombre FROM sai_proveedor_nuevo ORDER BY prov_nombre";
				$resultado = pg_exec ( $conexion, $query );
				$numeroFilas = pg_num_rows ( $resultado );
				$arreglo = "";
				while ( $row = pg_fetch_array ( $resultado ) ) {
					$arreglo .= "'" . $row ["prov_nombre"] . "',";
				}
				$arreglo = substr ( $arreglo, 0, - 1 );
				?>
			<script>
				var proveedores = new Array(<?= $arreglo?>);
				actb(document.getElementById('nombre'),proveedores);
			</script>
						</td>
							<td class="normalNegrita">Ubicaci&oacute;n:</td>
							<td><div align="left" class="normal">
									<select name="txt_ubica" id="txt_ubica" class="normalNegro">
										<option value="0" selected>Seleccione...</option>
										<option value="1">Torre</option>
										<option value="2">Galp&oacute;n</option>
									</select>
								</div></td>
						</tr>
				<tr>
				<td class="normalNegrita">Dependencia Solicitante:</td>
				<td>
					<div align="left" class="normal">
							<input name="depe_nombre" size = "45" type="text" class="normalNegro" 
							id="depe_nombre" value = "<?=utf8_decode($stringobj3['depe_nombre']);?>"  />
							<input type="hidden" name="depe_id" id="depe_id" value = "<?=$stringobj3['depe_id'];?>"  />
					</div>

				</td>
				</tr>
					</table>

					<table width="1020" border="0" align="center" cellpadding="1"
						cellspacing="1" class="tablaalertas" id="factura_head">
						<tr>
							<td><div align="center" class="normalNegrita">Art&iacute;culo</div></td>
							<td><div align="center" class="normalNegrita">Cantidad</div></td>
							<td><div align="center" class="normalNegrita">Precio</div></td>
							<td><div align="center" class="normalNegrita">Fecha Recepci&oacute;n</div></td>
							<td><div align="center" class="normalNegrita"></div></td>
							<td><div class="botonAgregar" onclick="add_articulo();"></div></td>
						</tr>

						<tbody id="body_factura_head" class="normalNegro trCaso">
						<tr>
						<td>
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
					 		input1.setAttribute("size",50);
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

							var input13 = document.createElement("input");
							input13.setAttribute("type","hidden");
							input13.setAttribute("name","arraymodi[unidad][]");
							input13.value="<?=$stringobj3['unidad_medida'][$i];?>";
					 		columna1.appendChild(input13);

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

					 		//precio
					 		
					 		var columna3 = document.createElement("td");
					 		columna3.setAttribute("valign","top");
					 		columna3.setAttribute("align","center");
					 		columna3.setAttribute("style","font-size:10px");
							
							var input3 = document.createElement("input");
							input3.setAttribute("type","text");
							input3.setAttribute("size",5);
							input3.setAttribute("name","arraymodi[precio][]");
							input3.setAttribute("onKeyPress","return acceptFloat(event)");
							input3.value="<?=$stringobj3['precio'][$i];?>";
							input3.setAttribute("id","precio");
					 		columna3.appendChild(input3);

							//imagen
							
							var columna6 = document.createElement("img");
							columna6.setAttribute("src","../../../js/lib/calendarPopup/img/calendar.gif");
							columna6.setAttribute("class","cp_img");
							columna6.setAttribute("width","25");
							columna6.setAttribute("onclick","g_Calendar.show(event, 'alm_fecha_recepcion"+(a)+"');");
							columna6.setAttribute("height","20");
							columna6.setAttribute("alt","Open popup calendar");

					 		//fecha
					 		
					 		var columna4 = document.createElement("td");
					 		columna4.setAttribute("valign","top");
					 		columna4.setAttribute("align","center");
					 		columna4.setAttribute("style","font-size:10px");
							
							var input4 = document.createElement("input");
							input4.setAttribute("type","text");
							input4.setAttribute("size",12);
							input4.setAttribute("name","arraymodi[alm_fecha_recepcion][]");
							input4.value="<?=$stringobj3['alm_fecha_recepcion'][$i];?>";
							input4.setAttribute("id","alm_fecha_recepcion"+(a++));
							input4.setAttribute("readonly","readonly");
							input4.setAttribute("class","normalNegro");
							columna4.appendChild(input4);


							
							//alm_id dependecia
							
							var columna5 = document.createElement("td");
							columna5.setAttribute("valign","top");
							columna5.setAttribute("align","center");
							columna5.setAttribute("style","font-size:10px");

							var input52 = document.createElement("input");
							input52.setAttribute("type","hidden");
							input52.setAttribute("name","arraymodi[alm_id][]");
							input52.value="<?=$stringobj3['alm_id'][$i];?>";
							input52.setAttribute("id","alm_id");
							columna4.appendChild(input52);
							
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
					 		fila.appendChild(columna6);
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

							<input type="hidden" name="arrayalm_id[]" value="<?=$stringobj3['alm_id'][$i];?>" />

						<?php } ?>
						<input type="hidden" name="total" id="total" value="<?=$i;?>" />
						</td>
						</tr>
						</tbody>	
					</table>
				</td>
			</tr>
			<tr>
				<td height="10" valign="middle"></td>
			</tr>
			<tr>
				<td height="16" colspan="3"></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="button"
					value="Modificar" onclick="javascript:revisar();"
					class="normalNegro"></input></td>
			</tr>
		</table>
	</form>
</body>
</html>
<?php pg_close($conexion);?>