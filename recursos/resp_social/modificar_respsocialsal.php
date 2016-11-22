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

$key=$_REQUEST['ne'];

if ($_SESSION ['user_perfil_id'] == PERFIL_ALMACENISTA) {
	$ubicaciont3 = 2;
	$ubicaciont5 = 2;
	$ubicacionserial = 2;
} else {
	$ubicaciont3 = "2 or t3.ubicacion= 1";
	$ubicaciont5 = "2 or t5.ubicacion= 1";
	$ubicacionserial = "2 or t3.ubicacion= 1";
}

/*
 echo "<pre>";
print_r($query);
echo "</pre>";
*/
	
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
								and (t5.serial is not null)

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
	
//echo $query;
	
$resultado = pg_exec ( $conexion, $query );
$numeroFilas = pg_num_rows ( $resultado );
$arregloProveedores = "";
$cedulasProveedores = "";
$nombresProveedores = "";
$indice = 0;
$stringobj;
// serial
$resultado2 = pg_exec ( $conexion, $query2 );
$numeroFilas2 = pg_num_rows ( $resultado2 );
$indice2 = 0;
$stringobj2;
while ( $row = pg_fetch_array ( $resultado ) ) {
	$arregloProveedores .= "'" . $row ["id"] . " " . strtoupper ( str_replace ( "\n", " ", $row ["nombre"] ) ) . "(" . $row ["unidad_medida"] . ") (" . $row ['modelo'] . ") Disp: " . $row ['disponible'] . "',";
	$cedulasProveedores .= "'" . $row ["id"] . "',";
	$nombresProveedores .= "'" . str_replace ( "\n", " ", strtoupper ( $row ["nombre"] ) ) . "',";

	$idss = $row ["id"] . " " . strtoupper ( str_replace ( "\n", " ", $row ["nombre"] ) ) . "(" . $row ["unidad_medida"] . ") (" . $row ['modelo'] . ") Disp: " . $row ['disponible'];

	$stringobj [$indice] ['cantidad'] = $row ['disponible'];
	$stringobj [$indice] ['modelo'] = utf8_encode ( $row ['modelo'] );

	$stringobj [$indice] ['marca'] = utf8_encode ( $row ['bmarc_nombre'] );
	$stringobj [$indice] ['idmarca'] = ($row ['bmarc_id']);
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
	
$arregloProveedores = substr ( $arregloProveedores, 0, - 1 );
$cedulasProveedores = substr ( $cedulasProveedores, 0, - 1 );
$nombresProveedores = substr ( $nombresProveedores, 0, - 1 );
	
/*
 echo "<pre>"; print_r($stringobj2); echo "</pre>";
*/
	
//query de actas
$queryacta=
"
		SELECT
		distinct(arti_id) as articulos,
	    t2.oid,
		t2.sec_id,
		t2.ubicacion,
		t3.destino,
		nombre,
		t2.medida,
		t2.cantidad,
		t5.bmarc_nombre,
		t5.bmarc_id,
		modelo,
		serial
		FROM
		sai_arti_salida_rs t3,
		sai_arti_salida_rs_item t2,
		sai_item t1,
		sai_proveedor_nuevo t4,
		sai_bien_marca t5
		WHERE
		t3.acta_id='".$key."' and
		t3.acta_id=t2.acta_id and
		arti_id=t1.id and
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
	$stringobj3  ['ubicacion'][$indice3] = $row3 ['ubicacion'];
	$stringobj3  ['destino'] = $row3['destino'];
	$stringobj3  ['oid']  [$indice3] = $row3['oid'];
	$stringobj3  ['idarticulo']  [$indice3] = $row3['articulos'];
	$stringobj3  ['sec_id']  [$indice3] = $row3['sec_id'];
	$stringobj3  ['nombre'] [$indice3] = $row3['nombre'];
	$stringobj3  ['unidad'] [$indice3] = $row3['medida'];
	$stringobj3  ['cantidad'] [$indice3] = $row3['cantidad'];
	$stringobj3  ['marca_nombre'] [$indice3] = $row3['bmarc_nombre'];
	$stringobj3  ['marca_id'] [$indice3] = $row3['bmarc_id'];
	$stringobj3  ['modelo'] [$indice3] = $row3['modelo'];
	$stringobj3  ['serial'] [$indice3] = $row3['serial'];
	$indice3 ++;
}

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

<link rel="stylesheet" href="../../css/safi0.2.css" type="text/css" media="all" />
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
var articulos = new Array(<?= $arregloProveedores?>);
	
var arreglo_rif = new Array(<?= $cedulasProveedores?>);
var nombre_proveedor= new Array(<?= $nombresProveedores?>);

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
var items = new Array();
var index = 0;
var items2 = new Array();
var index2 = 0;

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

$.each(stringobj2,function(id, params){
	items2[index2++] = {
			id: params.id,
			value: params.serial,
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
			
$().ready(function(){
	$("[name='material']").keyup(function(e){
		idArticulo=0;
	    if(e.keyCode == 46 || e.keyCode == 8)
		{
	    	$(this).val("");
		}
	});
});

function cambiarcantidad1(objeto){
	$(objeto).parents("tr.trCaso").find("input[type='text'][name='arraymodi\[cantidad\]\[\]']").val("");
	$(objeto).parents("tr.trCaso").find("input[type='text'][name='serial']").val("");
	$(objeto).parents("tr.trCaso").find("input[type='text'][name = 'arraymodi\[cantidad\]\[\]']").attr("readonly",false).val("");
}

function cambiarcantidad(objeto){
	$(objeto).parents("tr.trCaso").find("input[type='text'][name='arraymodi\[cantidad\]\[\]']").val(1);
	$(objeto).parents("tr.trCaso").find("input[type='text'][name='material']").val("");
	$(objeto).parents("tr.trCaso").find("input[type='text'][name='disponibilidad']").val("");
	$(objeto).parents("tr.trCaso").find("input[type='text'][name = 'arraymodi\[cantidad\]\[\]']").attr("readonly","readonly");
	if($(objeto).parents("tr.trCaso").find("input[type='text'][name = 'serial']").val() == "")
	{
		$(objeto).parents("tr.trCaso").find("input[type='text'][name = 'arraymodi\[cantidad\]\[\]']").attr("readonly",false).val("");
	}
}

function validarcantidad(objeto){
	//alert($(objeto).parents("tr.trCaso").find("input[type='text'][name='disponibilidad']").val());
	//alert($(objeto).parents("tr.trCaso").find("input[type='text'][name='cantidad']").val());
	if(parseInt($(objeto).parents("tr.trCaso").find("input[type='text'][name='arraymodi\[cantidad\]\[\]']").val()) > parseInt($(objeto).parents("tr.trCaso").find("input[type='text'][name='disponibilidad']").val()))
	{
		alert("La cantidad no debe ser mayor a la diponibilidad");
		$(objeto).parents("tr.trCaso").find("input[type='text'][name='arraymodi\[cantidad\]\[\]']").val("");
	}
}

function autocompletarMaterial(objInput)
{
	$(objInput).autocomplete({
		source: items, 
		minLength: 1,
	    select: function(event,ui)
	    {
	    		    	
		    $(objInput).parents("tr.trCaso").find("input[type='text'][name='disponibilidad']").val(ui.item.disponibilidad);
	   		//alert(ui.item.id + ui.item.nombre + ui.item.unidad + ui.item.modelo + ui.item.marca + ui.item.idmarca + ui.item.serial + ui.item.ubicacion + ui.item.idubicacion);
	   		$(objInput).parents("tr.trCaso").find("input[type='hidden'][name='arraymodi\[idarticulo\]\[\]']").val(ui.item.id);
	   		$(objInput).parents("tr.trCaso").find("input[type='hidden'][name='arraymodi\[unidad\]\[\]']").val(ui.item.unidad);
	   		$(objInput).parents("tr.trCaso").find("input[type='hidden'][name='arraymodi\[modelo\]\[\]']").val(ui.item.modelo);
	   		$(objInput).parents("tr.trCaso").find("input[type='hidden'][name='arraymodi\[marca_id\]\[\]']").val(ui.item.idmarca);
	   		$(objInput).parents("tr.trCaso").find("input[type='hidden'][name='arraymodi\[ubicacion\]\[\]']").val(ui.item.idubicacion);
	   		$(objInput).parents("tr.trCaso").find("input[type='hidden'][name='arraymodi\[serial\]\[\]']").val("");
	   		idArticulo = 1;
	   		//borrar
			/*nombreArticulo = ui.item.nombre;
			uniArticulo = ui.item.unidad;
			modeloArticulo = ui.item.modelo;
			marcaArticulo = ui.item.marca;
			idmarcaArticulo = ui.item.idmarca;
			dispArticulo = ui.item.disponibilidad;
			serial= ui.item.serial;
			ubicacion= ui.item.ubicacion;
			idubicacion=ui.item.idubicacion;
	    */
	    return true;
	            
	    }
	});
}

function autocompletarSerial(objInput)
{
	$(objInput).autocomplete({
		source: items2, 
		minLength: 1,
	    select: function(event,ui)
	    {
	    	
	    	//alert(ui.item.id + ui.item.nombre + ui.item.unidad + ui.item.modelo + ui.item.marca + ui.item.idmarca + ui.item.serial + ui.item.ubicacion + ui.item.idubicacion);
	   		
	   		
	   		var objTr = $(objInput).parents("tr.trCaso");
	   		
	   		objTr.find("input[type='hidden'][name='arraymodi\[idarticulo\]\[\]']").val(ui.item.id);
	   		objTr.find("input[type='hidden'][name='arraymodi\[unidad\]\[\]']").val(ui.item.unidad);
	   		objTr.find("input[type='hidden'][name='arraymodi\[modelo\]\[\]']").val(ui.item.modelo);
	   		objTr.find("input[type='hidden'][name='arraymodi\[marca_id\]\[\]']").val(ui.item.idmarca);
	   		objTr.find("input[type='hidden'][name='arraymodi\[ubicacion\]\[\]']").val(ui.item.idubicacion);
	   		objTr.find("input[type='hidden'][name='arraymodi\[serial\]\[\]']").val(ui.item.serial);
	   		objTr.find("input[type='text'][name='material']").val(ui.item.id+": "+ui.item.nombre +" ("+ui.item.unidad +") marca: "+ ui.item.marca + ui.item.modelo );
			//borrar
	   		/*
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
	    	idubicacion=ui.item.idubicacion;*/
	    return true;
	            
	    }
	});
}

function revisar()
{
	var idart = new Array();
	var cant = 0;
	var aux = 0;
	var vacio = 0;
	$("tr.trCaso").each(function(){
		if($(this).find("input[type='hidden'][name = 'arraymodi[serial][]']").val() != "") 
		{
			$(this).find("input[type='text'][name = 'arraymodi[cantidad][]']").val(1);
		}	
	});	
	$("tr.trCaso").each(function(){
		if($(this).find("input[type='text'][name = 'material']").val() == "") 
		{
			vacio++;
		}	
	});	
	if(vacio > 0){
		alert("Seleccione un art\u00EDculo");
		return;
	}
	$("tr.trCaso").each(function(index){
		idart[index] = $(this).find("input[type='hidden'][name = 'arraymodi[idarticulo][]']").val() + $(this).find("input[type='hidden'][name = 'arraymodi[marca_id][]']").val() + $(this).find("input[type='hidden'][name = 'arraymodi[modelo][]']").val() + $(this).find("input[type='hidden'][name = 'arraymodi[serial][]']").val();
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
		if($(this).find("input[type='text'][name = 'arraymodi[cantidad][]']").val() == "" || $(this).find("input[type='text'][name = 'arraymodi[cantidad][]']").val() == 0 || isNaN($(this).find("input[type='text'][name = 'arraymodi[cantidad][]']").val()) == true)
		{
			cant = 1;
		}	
	});

	if(cant > 0){
		alert("Indique la cantidad");
		return;
	}
	if(idArticulo == 0){
		alert("Seleccione un art\u00EDculo");
		return;
	}
	
	if(confirm("Datos introducidos de manera correcta. \u00BFEst\u00e1 seguro que desea continuar?"))
	{ 	
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
	input1.setAttribute("size",80);
	input1.setAttribute("name","material");
	input1.setAttribute("id","material");
	//input1.setAttribute("class","autocomplematerial");
	columna1.appendChild(input1);

	autocompletarMaterial(input1);

	//id
	var input12 = document.createElement("input");
	input12.setAttribute("type","hidden");
	input12.setAttribute("name","arraymodi[idarticulo][]");
	columna1.appendChild(input12);
	//medida
	var input13 = document.createElement("input");
	input13.setAttribute("type","hidden");
	input13.setAttribute("name","arraymodi[unidad][]");
	columna1.appendChild(input13);
	//modelo
	var input15 = document.createElement("input");
	input15.setAttribute("type","hidden");
	input15.setAttribute("name","arraymodi[modelo][]");
	columna1.appendChild(input15);
	//id marca
	var input16 = document.createElement("input");
	input16.setAttribute("type","hidden");
	input16.setAttribute("name","arraymodi[marca_id][]");
	columna1.appendChild(input16);
	//ubicacion
	var input17 = document.createElement("input");
	input17.setAttribute("type","hidden");
	input17.setAttribute("name","arraymodi[ubicacion][]");
	columna1.appendChild(input17);
	//serial
	var columna2 = document.createElement("td");
	columna2.setAttribute("valign","top");
	columna2.setAttribute("align","center");
	columna2.setAttribute("style","font-size:10px");
	
	var input2 = document.createElement("input");
	input2.setAttribute("type","text");
	input2.setAttribute("size",20);
	input2.setAttribute("name","arraymodi[serial][]");
	input2.setAttribute("id","serial");
	columna2.appendChild(input2);

	autocompletarSerial(input2);

	var input22 = document.createElement("input");
 	input22.setAttribute("type","hidden");
 	input22.setAttribute("name","arraymodi[serial][]");
 	input22.value="<?=$stringobj3['serial'][$i];?>";
 	columna2.appendChild(input22);

	//disponible
	var columna3 = document.createElement("td");
	columna3.setAttribute("valign","top");
	columna3.setAttribute("align","center");
	columna3.setAttribute("style","font-size:10px");
	
	var input3 = document.createElement("input");
	input3.setAttribute("type","text");
	input3.setAttribute("size",1);
	input3.setAttribute("name","disponibilidad");
	input3.setAttribute("id","disponibilidad");
	input3.setAttribute("disabled","disabled");
		columna3.appendChild(input3);
	
	//cantidad
	var columna4 = document.createElement("td");
	columna4.setAttribute("valign","top");
	columna4.setAttribute("align","center");
	columna4.setAttribute("style","font-size:10px");
	
	var input4 = document.createElement("input");
	input4.setAttribute("type","text");
	input4.setAttribute("size",1);
	input4.setAttribute("name","arraymodi[cantidad][]");
	input4.setAttribute("id","cantidad");
	columna4.appendChild(input4);

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
	$(input1).bind('keyup', function(){
	cambiarcantidad1($(this));
	});
	$(input2).bind('keyup', function(){
	cambiarcantidad($(this));
	});
	$(input4).bind('keyup', function(){
	validarcantidad($(this));
	});
	$("[name='material']").keyup(function(){
		idArticulo=0;
	});		
}

</script>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br/>
	<form action="modificar_respsocialSalAccion.php" name="form" id="form"
		method="post">
		<!-- borrar  <input type="hidden" value="0" name="hid_validar" />-->
		<table width="750" align="center"
			background="../../imagenes/fondo_tabla.gif" class="tablaalertas"
			id="sol_via">
			<tr>
				<td height="15" colspan="2" valign="middle" class="td_gray"><span
					class="normalNegroNegrita"> Nota de entrega responsabilidad social <?php echo $key;?></span></td>
			</tr>
			<tr>
				<td colspan="2">
					<table width="100%" border="0" align="center" cellpadding="1"
						cellspacing="1" class="tablaalertas" id="factura_head">
						<tr bgcolor="#F0F0F0">
							<td colspan='5'><div align="left" class="normalNegrita">Art&iacute;culos</div></td>
						</tr>
						<tr >
							<td><div align="center" class="normalNegrita">Descripci&oacute;n</div></td>
							<td><div align="center" class="normalNegrita">Serial del activo</div></td>
							<td><div align="center" class="normalNegrita">Disponibilidad</div></td>
							<td><div align="center" class="normalNegrita">Cantidad</div></td>
							<td><div class="botonAgregar" onclick="add_articulo();"></div></td>
						</tr>
					
						<tbody id="body_factura_head" class="normal">
						
						<script type="text/javascript">

						<?php for($i=0;$i<$indice3;$i++){?>
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
					 		input1.setAttribute("name","material");
					 		input1.value="<?=$stringobj3['idarticulo'][$i].": ".$stringobj3['nombre'][$i]." (".$stringobj3['unidad'][$i].") marca:".$stringobj3['marca_nombre'][$i]." ".trim($stringobj3['modelo'][$i]==''?'':'modelo:')."".$stringobj3['modelo'][$i]." (".utf8_decode(trim($stringobj3['ubicacion'][$i]==1?'Torre':'Galpón')).")";?>";
					 		input1.setAttribute("id","material");
					 		//input1.setAttribute("class","autocomplematerial");
					 		columna1.appendChild(input1);

					 		autocompletarMaterial(input1);

							//id
							var input12 = document.createElement("input");
							input12.setAttribute("type","hidden");
							input12.setAttribute("name","arraymodi[idarticulo][]");
							input12.value="<?=$stringobj3['idarticulo'][$i];?>";
					 		columna1.appendChild(input12);
							//medida
							var input13 = document.createElement("input");
							input13.setAttribute("type","hidden");
							input13.setAttribute("name","arraymodi[unidad][]");
							input13.value="<?=$stringobj3['unidad'][$i];?>";
					 		columna1.appendChild(input13);
							//modelo
							var input15 = document.createElement("input");
							input15.setAttribute("type","hidden");
							input15.setAttribute("name","arraymodi[modelo][]");
							input15.value="<?=$stringobj3['modelo'][$i];?>";
					 		columna1.appendChild(input15);
					 		//id marca
					 		var input16 = document.createElement("input");
					 		input16.setAttribute("type","hidden");
					 		input16.setAttribute("name","arraymodi[marca_id][]");
					 		input16.value="<?=$stringobj3['marca_id'][$i];?>";
					 		columna1.appendChild(input16);
					 		//ubicacion
					 		var input17 = document.createElement("input");
					 		input17.setAttribute("type","hidden");
					 		input17.setAttribute("name","arraymodi[ubicacion][]");
					 		input17.value="<?=$stringobj3['ubicacion'][$i];?>";
					 		columna1.appendChild(input17);
							//serial
							var columna2 = document.createElement("td");
							columna2.setAttribute("valign","top");
							columna2.setAttribute("align","center");
							columna2.setAttribute("style","font-size:10px");
							
							var input2 = document.createElement("input");
							input2.setAttribute("type","text");
							input2.setAttribute("size",20);
							input2.setAttribute("name","serial");
							input2.value="<?=$stringobj3['serial'][$i];?>";
							input2.setAttribute("id","serial");
					 		columna2.appendChild(input2);

					 		autocompletarSerial(input2);

					 		var input22 = document.createElement("input");
					 		input22.setAttribute("type","hidden");
					 		input22.setAttribute("name","arraymodi[serial][]");
					 		input22.value="<?=$stringobj3['serial'][$i];?>";
					 		columna2.appendChild(input22);

							//disponible
							var columna3 = document.createElement("td");
							columna3.setAttribute("valign","top");
							columna3.setAttribute("align","center");
							columna3.setAttribute("style","font-size:10px");
							
							var input3 = document.createElement("input");
							input3.setAttribute("type","text");
							input3.setAttribute("size",1);
							input3.setAttribute("name","disponibilidad");
							input3.setAttribute("id","disponibilidad");
							input3.setAttribute("disabled","disabled");
					 		columna3.appendChild(input3);

							//cantidad
							var columna4 = document.createElement("td");
							columna4.setAttribute("valign","top");
							columna4.setAttribute("align","center");
							columna4.setAttribute("style","font-size:10px");
							
							var input4 = document.createElement("input");
							input4.setAttribute("type","text");
							input4.setAttribute("size",1);
							input4.setAttribute("name","arraymodi[cantidad][]");
							input4.value="<?=$stringobj3['cantidad'][$i];?>";
							input4.setAttribute("id","cantidad");
							columna4.appendChild(input4);

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
					 		$(input1).bind('keyup', function(){
								cambiarcantidad1($(this));
							});
					 		$(input2).bind('keyup', function(){
								cambiarcantidad($(this));
							});
					 		$(input4).bind('keyup', function(){
								validarcantidad($(this));
							});		
					 		<?php } ?>
					 		</script>
					 							
						</tbody>
						
				</table>
			<tr>
				<td align="center">
				<br/>
				<div class="normal">
				
					<strong>Detalle del destino</strong>
					</div>
				
				<textarea name="destino" cols="50" rows="5"><?echo $stringobj3['destino'];?></textarea>
				<input type="hidden" name="codigo" value="<?=$key;?>"/>
				<input type="hidden" name="total" id="total" value="<?=$i;?>" />
				
				
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="button"
					value="Modificar" onclick="javascript:revisar();"
					class="normalNegro"></input></td>
			</tr>
			<!-- borrar <input type="hidden" name="txt_arreglo_articulos" id="txt_arreglo_articulos" />    -->
			</table>
</form>

</body>
</html>