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

$key = trim ( utf8_decode ( $_REQUEST ["amat"] ) );

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

$queryarti = "
	select
		t1.id,
		t1.nombre,
		t2.unidad_medida,
		t3.precio,
		t3.alm_id
	from
		sai_item t1
		inner join sai_item_articulo t2 on (t2.id = t1.id)
		inner join sai_arti_almacen t3	on (t3.arti_id = t1.id)	
	where
		id_tipo = 1
";
$resultado5 = pg_exec ( $conexion, $queryarti );
$indice5 = 0;
$stringobj5;
while ( $row5 = pg_fetch_array ( $resultado5 ) ) {
	$stringobj5 [$indice5] ['id_arti'] = $row5 ['id'];
	$stringobj5 [$indice5] ['nombre_arti'] = utf8_encode($row5 ['nombre']);
	$stringobj5 [$indice5] ['unidad_medida'] = utf8_encode($row5 ['unidad_medida']);
	$stringobj5 [$indice5] ['precio'] = $row5 ['precio'];
	$stringobj5 [$indice5] ['alm_id'] = $row5 ['alm_id'];
	$indice5 ++;
}

$queryacta = "
		select
			general.amat_id,
			general.fecha_acta,
			general.depe_entregada,
			depe.depe_nombre,
			depe.depe_id,
			general.observaciones,
			general.entregado_a,
			emple.empl_nombres,
			emple.empl_apellidos,
			emple.empl_cedula,
			esp.alm_id,
			esp.medida,
			esp.arti_id,
			nom.nombre,
			esp.cantidad,
			esp.precio
		from
			sai_arti_acta_almacen general
			inner join sai_arti_salida esp on(general.amat_id = esp.n_acta)
			inner join sai_dependenci depe on (depe.depe_id = general.depe_entregada)
			inner join sai_empleado emple on (emple.empl_cedula = general.entregado_a)
			inner join sai_item nom on (nom.id = esp.arti_id)
		where
			general.amat_id = '".$key."'
";
$resultado3 = pg_exec ( $conexion, $queryacta );
$indice3 = 0;
$stringobj3;
while ( $row3 = pg_fetch_array ( $resultado3 ) ) {
	
	
	$stringobj3 ['acta'] = $key;
	$stringobj3 ['fecha_acta'] = $row3 ['fecha_acta'];
	$stringobj3 ['depe_nombre'] = utf8_encode($row3 ['depe_nombre']);
	$stringobj3 ['depe_id'] = utf8_encode($row3 ['depe_id']);
	$stringobj3 ['observaciones'] = utf8_encode($row3 ['observaciones']);
	$stringobj3 ['empl_nombres'] = utf8_encode($row3 ['empl_nombres'])." ".utf8_encode($row3 ['empl_apellidos']);
	$stringobj3 ['empl_cedula'] = $row3 ['empl_cedula'];
	
	$stringobj3 ['alm_id'] [$indice3] = $row3 ['alm_id'];
	$stringobj3 ['arti_id'] [$indice3] = $row3 ['arti_id'];
	$stringobj3 ['medida'] [$indice3] = $row3 ['medida'];
	$stringobj3 ['nombre'] [$indice3] = $row3 ['nombre'];
	$stringobj3 ['precio'] [$indice3] = $row3 ['precio'];
	$stringobj3 ['cantidad'] [$indice3] = utf8_encode($row3 ['cantidad']);

	$indice3 ++;
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

var stringobj1 = <?php echo json_encode($stringobj1,JSON_FORCE_OBJECT); ?>;
var stringobj4 = <?php echo json_encode($stringobj4,JSON_FORCE_OBJECT); ?>;
var stringobj5 = <?php echo json_encode($stringobj5,JSON_FORCE_OBJECT); ?>;

var items2 = new Array();
var index2 = 0;
var items = new Array();
var index = 0;
var items1 = new Array();
var index1 = 0;
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
		

	$("#dependencia").autocomplete({
		source: items, 
		minLength: 1,
	    select: function(event,ui)
	    {
		    
	    	$("#dependenciaid").val(ui.item.iddependencia);

	    return true;
	            
	    }
	});

	$.each(stringobj1,function(id, params){
		items1[index1++] = {
				id: params.id,
				value: params.empl_nombres,
				cedula : params.empl_cedula
		};

	});

	$("#entregadoa").autocomplete({
		source: items1, 
		minLength: 1,
	    select: function(event,ui)
	    {
		    
	    	$("#cedula").val(ui.item.cedula);

	    return true;
	            
	    }
	});

	
});


$.each(stringobj5,function(id, params){
	items2[index2++] = {
			id: params.id,
			value: params.nombre_arti+" ("+params.unidad_medida+") Precio: "+params.precio,
			artinombre: params.nombre_arti,
			idnombre: params.id_arti,
			unidad: params.unidad_medida,
			precio: params.precio,
			alm_id: params.alm_id
	};
});

function autocompletarArti(objInput)
{
	
	$(objInput).autocomplete({
		source: items2, 
		minLength: 1,
	    select: function(event,ui)
	    {
		    	$(this).parents("tr.trCaso").find("input[type='hidden'][name = 'arraymodi\[idarticulo\]\[\]']").val(ui.item.idnombre);
		    	$(this).parents("tr.trCaso").find("input[type='hidden'][name = 'arraymodi\[unidad\]\[\]']").val(ui.item.unidad);
		    	$(this).parents("tr.trCaso").find("input[type='hidden'][name = 'arraymodi\[precio\]\[\]']").val(ui.item.precio);
		    	$(this).parents("tr.trCaso").find("input[type='hidden'][height = '10'][name = 'arraymodi\[alm_id\]\[\]']").val(ui.item.alm_id);
		    	$(this).parents("tr.trCaso").find("input[type='hidden'][name = 'arrayalm_id\[\]']").val(ui.item.alm_id);
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
	input1.setAttribute("size",80);
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

	
	var input52 = document.createElement("input");
	input52.setAttribute("type","hidden");
	input52.setAttribute("name","arraymodi[alm_id][]");
	input52.setAttribute("id","alm_id");
	columna2.appendChild(input52);

	var input53 = document.createElement("input");
	input53.setAttribute("type","hidden");
	input53.setAttribute("name","arraymodi[precio][]");
	input53.setAttribute("id","precio");
	columna2.appendChild(input53);

	var input54 = document.createElement("input");
	input54.setAttribute("type","hidden");
	input54.setAttribute("name","arrayalm_id[]");
	columna2.appendChild(input54);
	
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

}

function revisar()
{
	var cant = 0;
	var preci = 0;
	var idarti = 0;

	if(idArticulo == 0){
		alert("Seleccione un art\u00EDculo");
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
	 document.form.action="modificar_dismatAccion.php";
	 document.form.submit();
	}	
  
}
</script>

</head>
<body>

	<form name="form" method="post" enctype="multipart/form-data" id="form1">
		<br />
		<br />
		<table width="700" border="0" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td width="100%" height="15" valign="middle"><span class="normalNegroNegrita">Modificar Acta: <?=$key;?></span></td>
			</tr>
			<tr>
				<td>
					<table width="700" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas">
						<tr width="100%">
							<td class="normalNegrita">Dependencia:</td>
							<td>
							<input type="hidden" name="key" id="key" value="<?=$key;?>"></input>
							<input class="normalNegro" autocomplete="off" size="45" type="text" id="dependencia" name="dependencia" 
								value="<?=utf8_decode($stringobj3['depe_nombre']);?>" />
							<input type="hidden" name="dependenciaid" id="dependenciaid" value="<?=$stringobj3['depe_id'];?>"/>
							</td>
						</tr>
						<tr>
							<td class="normalNegrita">Entregado a:</td>
							<td>
							<input class="normalNegro" size="40" type="text" id="entregadoa" name="entregadoa" value="<?=utf8_decode($stringobj3['empl_nombres']);?>" />
							<input type="hidden" name="cedula" id="cedula" value="<?=$stringobj3['empl_cedula'];?>"></input>
							</td>
						</tr>
						<tr width="100%">
							<td class="normalNegrita">Fecha:</td>
							<td>
							<input class="normalNegro" autocomplete="off" size="10" type="text" id="fecha" name="fecha" 
								value="<?=$stringobj3['fecha_acta'];?>" readonly="readonly"/>
							<a
								href="javascript:void(0);"
								onclick="g_Calendar.show(event, 'fecha');"
								title="Mostrar Calendario" style="display: on" id="fecha"> 
							<img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" width="25" height="20" alt="Open popup calendar" />
							</a>
							</td>
						</tr>
						<tr width="100%">
							<td class="normalNegrita">Observaciones:</td>
							<td>
							<input class="normalNegro" size="80" type="text" id="observaciones" name="observaciones" value="<?=utf8_decode($stringobj3['observaciones']);?>" />
							</td>
						</tr>
					</table>

					<table width="700" border="0" align="center" cellpadding="1"
						cellspacing="1" class="tablaalertas" id="factura_head">
						<tr width="100%">
							<td><div align="center" class="normalNegrita">Art&iacute;culo</div></td>
							<td><div align="center" class="normalNegrita">Cantidad</div></td>
							<td><div align="center" class="normalNegrita"></div></td>
							<td><div class="botonAgregar" onclick="add_articulo();"></div></td>
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
					 		input1.setAttribute("size",80);
					 		input1.setAttribute("name","arraymodi[articulo][]");
					 		input1.setAttribute("readonly","readonly");
					 		input1.value="<?=$stringobj3['nombre'][$i]." (".$stringobj3['medida'][$i].") Precio:".$stringobj3['precio'][$i];?>";
					 		input1.setAttribute("id","arti_nombre");
					 		columna1.appendChild(input1);

					 		autocompletarArti(input1);

							var input12 = document.createElement("input");
							input12.setAttribute("type","hidden");
							input12.setAttribute("name","arraymodi[idarticulo][]");
							input12.value="<?=$stringobj3['arti_id'][$i];?>";
					 		columna1.appendChild(input12);

							var input13 = document.createElement("input");
							input13.setAttribute("type","hidden");
							input13.setAttribute("name","arraymodi[unidad][]");
							input13.value="<?=$stringobj3['medida'][$i];?>";
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

							
							var input52 = document.createElement("input");
							input52.setAttribute("type","hidden");
							input52.setAttribute("name","arraymodi[alm_id][]");
							input52.setAttribute("height","10");
							input52.value="<?=$stringobj3['alm_id'][$i];?>";
							input52.setAttribute("id","alm_id");
							columna2.appendChild(input52);

							var input53 = document.createElement("input");
							input53.setAttribute("type","hidden");
							input53.setAttribute("name","arraymodi[precio][]");
							input53.value="<?=$stringobj3['precio'][$i];?>";
							input53.setAttribute("id","precio");
							columna2.appendChild(input53);

							var input54 = document.createElement("input");
							input54.setAttribute("type","hidden");
							input54.setAttribute("name","arrayalm_id[]");
							input54.value="<?=$stringobj3['alm_id'][$i];?>";
							columna2.appendChild(input54);
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