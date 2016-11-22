<?
ob_start();
require_once("../../../includes/conexion.php");
require("../../../includes/perfiles/constantesPerfiles.php");
 
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../../../index.php',false);
	ob_end_flush();
	exit;
}

ob_end_flush();

function completar_digitos($cadena)
{
	for ($tamano= strlen($cadena); $tamano<7; $tamano++)
	{
		$cadena = "0".$cadena;
	}
	return $cadena;
}

// Obtener la distribución de los artículos en torre (ubicacion = 1)
$sql = "
	SELECT
		item_distribucion.id AS id_articulo,
		item_distribucion.cantidad AS cantidad_articulo
	FROM
		sai_item_distribucion item_distribucion
	WHERE
		item_distribucion.ubicacion = 1
	ORDER BY
		item_distribucion.id
";

$resultado = pg_query($conexion, $sql);
$distribucionArticulos = array();

if($resultado === false){
	echo "Error al realizar la consulta de la distribuci&oacute;n de los art&iacute;culos.";
	error_log("Error al realizar la consulta de la disponibilidad de los articulos. Detalles: " . pg_last_error());
} else {
	while($row = pg_fetch_array($resultado))
	{
		$distribucionArticulos[$row['id_articulo']] = $row;
	}
}

// Obtener los activos disponibles en inventario
$sql = "
	SELECT
		biin_items.etiqueta AS serial_bien_nacional,
		biin_items.serial AS serial_articulo
	FROM
		sai_biin_items biin_items
	WHERE
		biin_items.esta_id = 41
	ORDER BY
		biin_items.etiqueta
";

$resultado = pg_query($conexion, $sql);
$activosInventario = array();

if($resultado === false){
	echo "Error al realizar la consulta de los activos disponibles en inventario";
	error_log("Error al realizar la consulta de los activos disponibles en inventario. Detalles: " . pg_last_error());
} else {
	while($row = pg_fetch_array($resultado))
	{
		$activosInventario[$row['serial_bien_nacional']] = $row;
	}
}

// Obtener las dependencias solicitantes

$sql = "
	SELECT
		dependencia.depe_id AS id_dependencia,
		dependencia.depe_nombre AS nombre_dependencia
	FROM
		sai_dependenci dependencia
	WHERE
		dependencia.depe_nivel = 4
		OR dependencia.depe_id = 150
	ORDER BY
		dependencia.depe_nombre
";

$resultado = pg_query($conexion, $sql);
$dependenciasSolicitantes = array();

if($resultado === false){
	echo "Error al realizar la consulta de las dependencias solicitantes";
	error_log("Error al realizar la consulta de las dependencias solicitantes. Detalles: " . pg_last_error());
} else {
	while($row = pg_fetch_array($resultado))
	{
		$dependenciasSolicitantes[$row['id_dependencia']] = $row;
	}
}

// Obtener las unidades a las que se les cargara el gastos
/*
$sql = "
	SELECT
		dependencia.depe_id AS id_dependencia,
		dependencia.depe_nombre AS nombre_dependencia
	FROM
		sai_dependenci dependencia
	WHERE
		(
			dependencia.depe_nivel <> 6
			AND dependencia.depe_id like '45%'
		)
		OR dependencia.depe_nivel = 4
		OR dependencia.depe_id = 150
	ORDER BY
		dependencia.depe_nombre
";

$resultado = pg_query($conexion, $sql);
$unidadesGastos = array();

if($resultado === false){
	echo "Error al realizar la consulta de las unidades de gastos";
	error_log("Error al realizar la consulta de las unidades de gastos. Detalles: " . pg_last_error());
} else {
	while($row = pg_fetch_array($resultado))
	{
		$unidadesGastos[$row['id_dependencia']] = $row;
	}
}
*/
// Obtener las ubicaciones de los bienes
// bubica_id = 2 => Galpón
// bubica_id = 6 => Otro

$sql = "
	SELECT
		bubica_id AS id_ubicacion,
		bubica_nombre AS nombre_ubicacion,
		bubica_descripcion AS descripcion_ubicacion,
		esta_id AS estatus_ubicacion,
		usua_login AS usua_login_ubicacion,
		tabla AS tabla_ubicacion
	FROM
		sai_bien_ubicacion ubicacion
	WHERE
		ubicacion.esta_id = 1
		AND ubicacion.bubica_id not in (2,6)
	ORDER BY
		ubicacion.bubica_nombre
";

$resultado = pg_query($conexion, $sql);
$ubicacionesBienes = array();

if($resultado === false){
	echo "Error al realizar la consulta de las ubicaciones de los bienes";
	error_log("Error al realizar la consulta de las ubicaciones de los bienes. Detalles: " . pg_last_error());
} else {
	while($row = pg_fetch_array($resultado))
	{
		$ubicacionesBienes[$row['id_ubicacion']] = $row;
	}
}

// Obtener los infocentros

$sql = "
	SELECT
		infocentro.id AS id_infocentro,
		infocentro.nemotecnico AS codigo_infocentro,
		infocentro.nombre AS nombre_infocentro,
		estatus_general.nombre AS nombre_estatus_infocentro,
		infocentro.direccion AS direccion_infocentro
	FROM
		safi_infocentro infocentro,
		safi_estatus_general estatus_general
	WHERE
		estatus_general.id = infocentro.id_estatus_general
	ORDER BY
		infocentro.nombre
";

$resultado = pg_query($conexion, $sql);
$infocentros = array();

if($resultado === false){
	echo "Error al realizar la consulta de los infocentros.";
	error_log("Error al realizar la consulta de de los infocentros. Detalles: " . pg_last_error());
} else {
	while($row = pg_fetch_array($resultado))
	{
		$infocentros[$row['id_infocentro']] = $row;
	}
}

// Obtener los materiales

$sql = "
	SELECT
		item.id AS id_item,
		item.nombre AS nombre_item,
		item_articulo.unidad_medida AS unidad_medida
	FROM
		sai_item item
		INNER JOIN sai_item_articulo item_articulo ON (item_articulo.id = item.id)
	WHERE
		item.esta_id = 1
		AND item.id_tipo = 1
		AND item_articulo.tipo NOT IN ('3'/*,'8'*/)
	ORDER BY
		item.nombre
";

$resultado = pg_query($conexion, $sql);
$materiales = array();

if($resultado === false){
	echo "Error al realizar la consulta de los materiales.";
	error_log("Error al realizar la consulta de de los materiales. Detalles: " . pg_last_error());
} else {
	while($row = pg_fetch_array($resultado))
	{
		$materiales[$row['id_item']] = $row;
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all" />
		
		
		<script type="text/javascript" src="../../../js/funciones.js"> </script>
		<script type="text/javascript" src="../../../js/lib/actb.js"></script>
		<script type="text/javascript" src="../../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script language="javascript">

			var listado_bienes = new Array();
			var listado_mobiliario = new Array();
			var ori = new Array();
			var factura_head = new Array();

			<?php
			
			echo "							 
		var listado_disponibilidad = new Array(".
			implode(
				", "
				, array_map(
					function ($distribucionArticulo)
					{
						return("new Array('".$distribucionArticulo["id_articulo"]."', '".$distribucionArticulo["cantidad_articulo"]."')");
					}
					, $distribucionArticulos
				)
			).");
			";
			
			echo "							 
		var listado_activos = new Array(".
			implode(
				", "
				, array_map(
					function ($activoInventario)
					{
						return("new Array('".$activoInventario["serial_bien_nacional"]."', '".$activoInventario["serial_articulo"]."')");
					}
					, $activosInventario
				)
			).");
			";
			?>

			$().ready(function(){
				
				// Llenar el input de infocentros
				var listaInfocentros = new Array(<?php echo
					implode(
						", "
						, array_map(
							function ($infocentro){
								return "'".$infocentro['codigo_infocentro']." : ".mb_strtoupper($infocentro['nombre_infocentro'], "ISO-8859-1")
									." (".$infocentro['nombre_estatus_infocentro'].")'";
							}
							, $infocentros
						)
					) ?>);

				var obj1 = new actb(document.getElementById('infocentro'), listaInfocentros);
				
				// Llenar el input de materiales
				var listaMateriales = new Array(<?php echo
					implode(
						", "
						, array_map(
							function ($material){
								return "'".$material['id_item']." : ".mb_strtoupper($material['nombre_item'], "ISO-8859-1")
									." (".$material['unidad_medida'].")'";
							}
							, $materiales
						)
					) ?>);
				
				var obj2 = new actb(document.getElementById('material'), listaMateriales);
				
			});
			
			function validar_disponibilidad()
			{
				document.form.disponibilidad.value = "";
				var posicion = document.form.material.value.indexOf(':');
				var articulo = trim(document.form.material.value.substring(0,posicion));
			
				for(i=0;i<listado_disponibilidad.length;i++)
				{
					var id_arti = listado_disponibilidad[i][0];
					if (articulo==id_arti){
						document.form.disponibilidad.value=listado_disponibilidad[i][1];
					}
				}
			}

			function revisar()
			{
			
				if(document.form.opt_depe.value=='')
				{
					window.alert("Debe seleccionar la dependencia solicitante");
					document.form.opt_depe.focus();
					return
				}

				// Validar que se haya seleccionado una unidad de gastos
				/*
				var objSelectUnidadGastos = $('#unidadGastos');
				if(objSelectUnidadGastos.length > 0 && objSelectUnidadGastos.val() == "0")
				{
					alert("Debe seleccionar una unidad de gastos.");
					return;
				}
				*/
			
				if(document.form.ubicacion.value=='0')
				{
					window.alert("Debe seleccionar el destino de los activos");
					document.form.ubicacion.focus();
					return
				}
				
			  	if(document.form.destino.value=='')
				{
					window.alert("Debe especificar el destino de los activos");
					document.form.destino.focus();
					return
				}

			  	inputsSerialesActivos = $('#tbodyBienes').find('input[name="serialesActivos\[\]"]');
			  	inputsSerialesBienesNacionales = $('#tbodySerialBienNacional').find('input[name="serialesBienesNacionales\[\]"]');
			  	inputIdsMateriales = $('#tbodyMateriales').find('input[name="idsMateriales\[\]"]');
			
				if ((inputIdsMateriales.length == 0) && (inputsSerialesActivos.length == 0) && (inputsSerialesBienesNacionales.length == 0))
				{
					window.alert("No se registro ning\u00FAn activo y/o material en el inventario");
					return
				}
				
				if(confirm("Datos introducidos de manera correcta. \u00BFEst\u00E1 seguro que desea continuar?."))
				{
					document.form.submit();
				}	
			}


			function addMaterial()
			{

				var nameIdsMateriales = "idsMateriales[]";
				var nameCantidadesMateriales = "cantidadesMateriales[]";
				
				var objInputMaterial = $('#material');
				var objInputDisponibilidad = $('#disponibilidad');
				var objInputCantidad = $('#cantidad');
				var objTbody = $('#tbodyMateriales');

				var arrDatosMaterial = objInputMaterial.val().split(':');
				var idMaterial = $.trim(arrDatosMaterial[0]);
				var nombreMaterial = $.trim(arrDatosMaterial[1]);
				var statusMaterial = $.trim(arrDatosMaterial[2]);
				var cantidad = $.trim(objInputCantidad.val());
				
				
				var disponible = 0;
				var iva_total = 0;
				var factura_new = "";
				
				if(objInputMaterial.val() == '')
				{
					alert("No ha seleccionado ning\u00FAn material");
					objInputMaterial.focus();
					return;
				}
				
				if((objInputCantidad.val() == "")|| (objInputCantidad.val() <= "0"))
				{
					alert("Debe especificar la cantidad del material");
					objInputCantidad.val("");	
					objInputCantidad.focus();
					return;
				}
				
				if (parseInt(objInputCantidad.val()) > parseInt(objInputDisponibilidad.val())){
					objInputCantidad.val("");
					alert("La cantidad a ser entregada no puede ser mayor a la disponible.");
					return;
				}
				
				// Validar que el material no haya sido ingresado con anterioridad
				var encontrado = 0;
				$(objTbody).find('input[name="'+nameIdsMateriales+'"][value="'+idMaterial+'"]').each(function(index, objInput){
					encontrado = 1;
				});

				if (encontrado == 1){
					alert("El material ya ha sido ingresado");
					objInputMaterial.val("");
					objInputDisponibilidad.val("");
					objInputCantidad.val("");
					objInputMaterial.focus();
					return;
				}

				id_arti = arrDatosMaterial[0];
				
				if (id_arti == 1165) //Kit infocentro en sai_item
				{
					var tbody_factura = document.getElementById('tbodyMateriales');
					for(i = 0; i < factura_head.length; i++)
					{
						tbody_factura.deleteRow(0);
					}
					<?php 
					$query = "SELECT t1.*,nombre FROM sai_articulo_kit t1, sai_item t2 WHERE t1.id=t2.id";
					$resultado = pg_exec($conexion, $query);
					$id_arti_kit = "";
					$cant_arti_kit = "";
						
					while($row=pg_fetch_array($resultado))
					{
						$id_arti_kit = "'".$row["id"]."'";
						$nombre_arti_kit = "'".$row["nombre"]."'";
						$cant_arti_kit = "'".$row["cantidad"]."'";
					?>
							var registro_factura_head = new Array(3);
							registro_factura_head[0] = <?= $id_arti_kit?>;//document.form.articulo.value;
							registro_factura_head[1] = <?= $nombre_arti_kit?>;//document.form.arti_nombre.value;
							registro_factura_head[2] = <?= $cant_arti_kit?>;
							factura_head[factura_head.length] = registro_factura_head;	
					<?php 	
					}
					?>
				}
			
			
				// Crear la fila con la información del material
			
				var objTr = document.createElement("tr");
				objTr.className = "reci";
				/*
				if((i%2)==0)
					objTr.className = "reci2";
				else
					objTr.className = "reci";
				*/
							
				// Td del id y nombre del material
				var objTd = document.createElement("td");
				objTd.setAttribute("align", "left");
				objTd.setAttribute("colspan", "2");
				objTd.className = "normalNegro";
				objTr.appendChild(objTd);

				var objInput = document.createElement("input");
				objInput.setAttribute("type", "text");
				objInput.setAttribute("name", nameIdsMateriales);
				objInput.setAttribute("value", idMaterial);
				objInput.setAttribute("readonly", "readonly");
				objInput.setAttribute("size", "6");
				objInput.setAttribute("style", "text-align: right;");
				objInput.className = "normalNegro";
				objTd.appendChild (objInput);

				objTd.appendChild (document.createTextNode(nombreMaterial));
		
				// Td de cantidad del material
				var objTd = document.createElement("td");
				objTd.setAttribute("align", "center");
				objTd.className = "normalNegro";
				objTr.appendChild(objTd);

				var objInput = document.createElement("input");
				objInput.setAttribute("type", "text");
				objInput.setAttribute("name", nameCantidadesMateriales);
				objInput.setAttribute("value", cantidad);
				objInput.setAttribute("readonly", "readonly");
				objInput.setAttribute("size", "6");
				objInput.className = "normalNegro";
				objTd.appendChild (objInput);

				// Td del botón del eliminar
				var objTd = document.createElement("td");
				objTd.setAttribute("align", "center");
				objTd.className = 'normal';
				objTr.appendChild(objTd);

				// crear el link eliminar
				var objA = document.createElement("a");
				objA.setAttribute('href', 'javascript:void(0);');
				objA.appendChild(document.createTextNode("Eliminar"));
				
				objTd.appendChild (objA);

				// asociar el evento click(eliminar)
				objA = $(objA);
				objA.unbind('click');
				objA.bind('click', {objTr: objTr}, function(event){
					$(event.data.objTr).remove();
				});
				
				objTbody.append(objTr);

				objInputMaterial.val("");
				objInputDisponibilidad.val("");
				objInputCantidad.val("");
				objInputMaterial.focus();
			}


			function acceptFloat(evt){	
				// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57	'.'=46
				var nav4 = window.Event ? true : false;
				
				var key = nav4 ? evt.which : evt.keyCode;	
			
				return ((key <= 13) || (key >= 48 && key <= 57) || (key == 46));
			}

			function completar_digitos(texto)
			{
			 
				if (texto=='inicio')
					cadena=document.form.txt_identifi.value;
				else
					cadena=document.form.txt_identifi2.value;
			
				if ((texto=='fin') && (cadena!=0))
					document.form.txt_cantidad.readOnly=true;
				else
					document.form.txt_cantidad.readOnly=false;
			 
				if (pri==0){
					serial1=cadena;
					pri=1;
				}
				else
					serial2=cadena;
			 
				for (tamano= cadena.length; tamano<7; tamano++)
				{ 
					cadena = "0"+cadena;
				}
				if (texto=='inicio')
					document.form.txt_identifi.value=cadena;
				else
					document.form.txt_identifi2.value=cadena;
				
			}

			function acceptIntt(evt){	
				// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57	
				var nav4 = window.Event ? true : false;
				
				var key = nav4 ? evt.which : evt.keyCode;	
			
				return ((key <= 13) || (key >= 48 && key <= 57));
			}

			var titulo=1;
			//-->

			function leer_serial()
			{
				var nameSerialesActivos = "serialesActivos[]";
				
				var objInputSerial = $('#serial');
				var objTbody = $('#tbodyBienes');
				
				var serialActivo = objInputSerial.val();
				var encontrado = 0;	  

				// Validar que el activo este disponible para su asignación
				for(i = 0; i < listado_activos.length; i++)
				{
					var sactivo = listado_activos[i][1];
					if (serialActivo == sactivo)
					{
						encontrado = 1;  
					}
				}
				
				if (encontrado == 0){
					alert("El serial del activo ingresado no est\u00E1 disponible para su asignaci\u00F3n");
					return;
				}
				// Fin de validar que el activo este disponible para su asignación
				
				// Validar que no se ingresen seriales repetidos
				var encontrado = 0;
				$(objTbody).find('input[value="'+serialActivo+'"]').each(function(index, objInput){
					encontrado = 1;
				});

				if (encontrado == 1){
					alert("El serial del activo ya ha sido ingresado");
					objInputSerial.val("");
					objInputSerial.focus();
					return;
				}
				// Fin de validar que no se ingresen seriales repetidos
				
				// Tr del serial del activo
				var objTr = document.createElement("tr");

				// Td del serial del activo
				var objTd = document.createElement("td");
				objTd.setAttribute("align","center");
				
				// Input del serial del activo
				var objInput = document.createElement("input");
				objInput.setAttribute("type", "text");
				objInput.setAttribute("name", nameSerialesActivos);
				objInput.setAttribute("value", serialActivo);
				objInput.setAttribute("readonly", "readonly");
				objInput.setAttribute("size", "20");
				objInput.className = "normalNegro";
				objTd.appendChild (objInput);	

				// crear el link eliminar
				var objA = document.createElement("a");
				objA.setAttribute('href', 'javascript:void(0);');
				objA.appendChild(document.createTextNode("Eliminar"));
				
				objTd.appendChild (objA);

				// asociar el evento click(eliminar)
				objA = $(objA);
				objA.unbind('click');
				objA.bind('click', {objTr: objTr}, function(event){
					$(event.data.objTr).remove();
				});
				
				objTr.appendChild(objTd);
				objTbody.append(objTr);
				
				objInputSerial.val("");
				objInputSerial.focus();
			}


			function leer_codigo()
			{
				var nameSerialesBienesNaionales = "serialesBienesNacionales[]";

				var objInputSerialBienNacional = $('#etiqueta');
				var objTbody = $('#tbodySerialBienNacional');
				
				var serialBienNacional = objInputSerialBienNacional.val();
				var encontrado = 0;

				// Validar que el activo este disponible para su asignación
				for(i = 0; i < listado_activos.length; i++)
				{
					var sbn = listado_activos[i][0];
					if (serialBienNacional == sbn){
						encontrado = 1;  
					}
				}
			
				if (encontrado == 0){
					alert("El serial de bien nacional ingresado no est\u00E1 disponible para su asignaci\u00F3n");
					return;
				}
				// Fin de validar que el activo este disponible para su asignación
				
				// Validar que no se ingresen seriales repetidos
				var encontrado = 0;
				$(objTbody).find('input[value="'+serialBienNacional+'"]').each(function(index, objInput){
					encontrado = 1;
				});

				if (encontrado == 1){
					alert("El serial de bien nacional ya ha sido ingresado");
					objInputSerialBienNacional.val("");
					objInputSerialBienNacional.focus();
					return;
				}
				// Fin de validar que no se ingresen seriales repetidos
			
				// Tr del serial de bien nacional
				var objTr = document.createElement("tr");

				// Td del serial de bien nacional
				var objTd = document.createElement("td");
				objTd.setAttribute("align", "center");
				
				var objInput = document.createElement("input");
				objInput.setAttribute("type", "text");
				objInput.setAttribute("name", nameSerialesBienesNaionales);
				objInput.setAttribute("value", serialBienNacional);
				objInput.setAttribute("readonly", "serialBienNacional");
				objInput.setAttribute("size", "20");
				objInput.className = "normalNegro";
				objTd.appendChild (objInput);

				// crear el link eliminar
				var objA = document.createElement("a");
				objA.setAttribute('href', 'javascript:void(0);');
				objA.appendChild(document.createTextNode("Eliminar"));
				
				objTd.appendChild (objA);

				// asociar el evento click(eliminar)
				objA = $(objA);
				objA.unbind('click');
				objA.bind('click', {objTr: objTr}, function(event){
					$(event.data.objTr).remove();
				});

				objTr.appendChild(objTd);
				objTbody.append(objTr);	
				   	    
				objInputSerialBienNacional.val("");
				objInputSerialBienNacional.focus();
				
				return;
			}
	 
			function activar_campo(){
				if (document.form.ubicacion.value==3){
					document.form.infocentro.disabled=false;
				}else{
					document.form.infocentro.disabled=true;
					document.form.infocentro.value="";
				}
			}
		</script>
		<title>Documento sin t&iacute;tulo</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	</head>

	<body>
		<form action="salida_activos_almacen_Accion.php" name="form" id="form" method="post">
			<input type="hidden" value="0" name="hid_validar" />
			<table width="700" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" id="sol_via">
				<tr>
					<td height="15" colspan="2" valign="middle" class="td_gray">
						<span class="normalNegroNegrita"> Salida de activos y/o materiales </span>
					</td>
				</tr>
				<tr bgcolor="#F0F0F0" class="normalNegrita">
					<td colspan="2">Datos generales</td>
				</tr>
				<tr>
					<td class="normal"><strong>Dependencia solicitante</strong></td>
					<td>
						<select name="opt_depe[]" class="normalNegro" id="opt_depe"	multiple>
							<option value="" class="normalNegrita" style="font-weight: bold;">[Selecci&oacute;n M&uacute;ltiple]</option>
							<?php 
								foreach ($dependenciasSolicitantes AS $dependenciaSolicitante)
								{
									echo "
							<option value=\"".$dependenciaSolicitante['id_dependencia']."\">".$dependenciaSolicitante['nombre_dependencia']."</option>
									";
								}
							?>
						</select>
					</td>
				</tr>
				<!-- 
				<tr>
					<td class="normal"><strong>Unidad gastos</strong></td>
					<td>
						<select name="unidadGastos" class="normalNegro" id="unidadGastos">
							<option value="0">[Seleccione]</option>
							<?php
							/*
								foreach ($unidadesGastos AS $unidadGasto)
								{
									echo "
							<option value=\"".$unidadGasto['id_dependencia']."\">".$unidadGasto['nombre_dependencia']."</option>
									";
								}
							*/
							?>
						</select>
					</td>
				</tr>
				 -->
				<tr>
					<td class="normal"><strong>Destino</strong></td>
					<td>
						<select class="normalNegro" name="ubicacion" id="ubicacion" onchange="javascript:activar_campo()"> 
							<option value="0">[Seleccione]</option>
							<?php 
								foreach ($ubicacionesBienes AS $ubicacioneBien)
								{
									echo "
							<option value=\"".$ubicacioneBien['id_ubicacion']."\">".$ubicacioneBien['nombre_ubicacion']."</option>
									";
									
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<div align="left" class="normal"><strong>Infocentro:</strong></div>
					</td>
					<td>
						<input type="text" name="infocentro" id="infocentro" class="normalNegro" size="70">
						<!-- <input type="text" name="dir_info" id="dir_info"> -->
					</td>
				</tr>
				<tr>
						<td><div align="left" class="normal"><strong>Detalle destino: </strong></div></td>
					<td><textarea name="destino" cols="50"></textarea></td>
				</tr>
				<tr>
					<td colspan="2">
						<table width="700" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
							<tbody id="tbodyBienes" class="normal" align="center">
								<tr bgcolor="#F0F0F0">
									<td colspan="2"><div align="left" class="normalNegrita">Activos</div></td>
								</tr>
								<tr>
									<td height="10">
									<div align="center" class="normal"></div>
									</td>
								</tr>
								<tr>
									<td>
										<div align="center" width="10" class="normal">
											<strong>Serial activo: </strong>
											<input name="serial" type="text" class="normalNegro" id="serial" size="15" onchange="leer_serial();">
											<!-- <a href="javascript: add_bienes('listado_bienes','0'); " class="normal">Agregar Serial</a> -->
										</div>
										<input type="hidden" name="txt_arreglo_bienes_head" id="txt_arreglo_bienes_head" />
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<table width="700" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas"
							id="factura_head"
						>
							<tbody id="tbodyMateriales" class="normal">
								<tr bgcolor="#F0F0F0">
									<td colspan='4'>
										<div align="left" class="normalNegrita">Materiales</div>
									</td>
								</tr>
								<tr>
									<td><div align="center" class="normalNegrita">Descripci&oacute;n</div></td>
									<td><div align="center" class="normalNegrita">Disponibilidad</div></td>
									<td><div align="center" class="normalNegrita">Cantidad</div></td>
									<td><div align="center" class="normalNegrita">Opci&oacute;n</div></td>
								</tr>
								<tr>
									<td>
										<div align="center"><span>
											<input type="text" class="normalNegro" name="material" id="material" value="" size="40" />
											<!--  <input name="arti_nombre" type="hidden" id="arti_nombre" /> -->
										</span></div>
									</td>
									<td>
										<div align="center" class="peq"><input name="disponibilidad" type="text"
											class="normalNegro" id="disponibilidad" size="6" maxlength="6" value="" disabled>
										</div>
									</td>
									<td>
										<div align="center" class="peq"><input name="cantidad" type="text" class="normalNegro" id="cantidad"
											onKeyUp="javascript: validar(this,0)" size="6" maxlength="6" onfocus="validar_disponibilidad()">
										</div>
									</td>
									<td>
										<div align="center" class="normal">
											<a href="javascript: addMaterial(); " class="normal">Agregar</a>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<table width="700" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas"
							id="factura_head">
							<tbody id="tbodySerialBienNacional" class="normal" align="center">
								<tr bgcolor="#F0F0F0">
									<td colspan='3'>
										<div align="left" class="normalNegrita">Mobiliario</div>
									</td>
								</tr>
								<tr>
									<td height="10">
										<div align="center" class="normal"></div>
									</td>
								</tr>
								<tr>
									<td>
										<div align="center" class="normal"><strong>Serial Bien Nacional: </strong>
											<input type="text" class="normalNegro" id="etiqueta"
												size="15" onchange="leer_codigo();" />
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center"><input type="button"
						value="Generar Salida" onclick="javascript:revisar();"  class="normalNegro" />
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>
