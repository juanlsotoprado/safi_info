<?
ob_start();
require_once("../../includes/conexion.php");
require("../../includes/perfiles/constantesPerfiles.php");
 
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../../index.php',false);
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
?>
<script language="javascript">
	var listado_bienes = new Array();
	var listado_mobiliario = new Array();
	var ori = new Array();
	var factura_head = new Array();
	var listado_disponibilidad = new Array();
	var listado_activos = new Array();
</script>
<?php

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



$i=0;
 if ($_SESSION['user_perfil_id'] == PERFIL_ALMACENISTA)
  $sql_e="select cantidad,id from sai_item_distribucion where ubicacion=2 order by id";
 else
  $sql_e="select cantidad,id from sai_item_distribucion where ubicacion=1 order by id";
  
     $resultado_entrada=pg_query($conexion,$sql_e) or die("Error al consultar edisponibilidad");  
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
			
						
$i=0;
$sql_e="select etiqueta,serial from sai_biin_items where esta_id=41 order by etiqueta";
$resultado_entrada=pg_query($conexion,$sql_e) or die("Error al consultar disponibilidad");  
	while ($rowe=pg_fetch_array($resultado_entrada)) {
		$etiqueta = $rowe["etiqueta"];
		$serial= $rowe["serial"];
		echo("
			<script language='javascript'>
				var registro = new Array(); 
				registro[0]='$etiqueta';
				registro[1]='$serial';
				listado_activos[$i]=registro;
			</script>
			");
		$i++;
	}			
		?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all" />

		<script type="text/javascript" src="../../js/funciones.js"> </script>
		<script type="text/javascript" src="../../js/lib/actb.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script language="javascript">
		/*
			var listado_bienes = new Array();
			var listado_mobiliario = new Array();
			var ori = new Array();
			var factura_head = new Array();
			var listado_disponibilidad = new Array();
			var listado_activos = new Array();
		*/
		</script>
<script language="javascript">

function validar_disponibilidad(){

 document.form.disponibilidad.value="";
 var posicion=document.form.material.value.indexOf(':');
 var articulo=trim(document.form.material.value.substring(0,posicion));

 for(i=0;i<listado_disponibilidad.length;i++)
 {
   var id_arti = listado_disponibilidad[i][0];
   if (articulo==id_arti){
    document.form.disponibilidad.value=listado_disponibilidad[i][1];
   }
 }

}

function validarRif(rif){
	var encuentra=0;
	for(j= 0; j < arreglo_rif.length; j++){
		if(rif==arreglo_rif[j]){
			return true;
			encuentra=1;
		}
	}
	/*if (encuentra==0){
	
	alert("Este INFOCENTRO no es v"+aACUTE+"lido");
	document.form.infocentro.focus();
	return false;		
	}*/
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
		window.alert("Debe seleccionar el destino de los activos/materiales");
		document.form.ubicacion.focus();
		return
	}
	
  	if(document.form.destino.value=='')
	{
		window.alert("Debe especificar el detalle del destino de los activos/materiales");
		document.form.destino.focus();
		return
	}

	if ((factura_head.length==0) && (listado_bienes.length==0) && (listado_mobiliario.length==0))
	{
		window.alert("No se registr\u00F3 ning\u00FAn activo/material en el inventario");
		return
	}
		
  	document.form.txt_arreglo_activos.value="";
	//Listado de Activos
	for(i=0;i<listado_bienes.length;i++)
	{
		for (j=0; j<1; j++)//4
		{
			document.form.txt_arreglo_activos.value+=listado_bienes[i][j];
			document.form.txt_arreglo_activos.value+="�";
		}	
	}
  	
	document.form.txt_arreglo_articulos.value="";
	//Listado de Artículos
	for(i=0;i<factura_head.length;i++)
	{
		for (j=0; j<3; j++)//4
		{
			document.form.txt_arreglo_articulos.value+=factura_head[i][j];
		   	
			if  ( (i<(factura_head.length-1)  ) ||  (i==(factura_head.length-1) &&  (j!=2) ) )
			{
			document.form.txt_arreglo_articulos.value+="�";
			}
		}	
	}


	document.form.txt_arreglo_mobiliario.value="";
	//Listado de Mobiliario
	for(i=0;i<listado_mobiliario.length;i++)
	{
		for (j=0; j<1; j++)
		{
			document.form.txt_arreglo_mobiliario.value+=listado_mobiliario[i][j];
			document.form.txt_arreglo_mobiliario.value+="�";
		}	
	}

	
	if(confirm("Datos introducidos de manera correcta. \u00BFEst\u00E1 seguro que desea continuar?."))
  {
	 document.form.submit();
  }	
  
}


function add_factura_head(id,tipo)
{ 	var disponible=0;
	var iva_total =0;
	var factura_new="";

	if(tipo==0)
	{
	
	  	if(document.form.material.value=='')
		{
			window.alert("No ha seleccionado ning\u00FAn material");
			document.form.articulo.focus();
			return
		}
		
		if((document.form.cantidad.value=="") || (document.form.cantidad.value<="0"))
		{
			window.alert("Debe especificar la cantidad del material");
			document.form.cantidad.focus();
			return
		}


		  if (parseInt(document.form.cantidad.value)>parseInt(document.form.disponibilidad.value)){
			    document.form.cantidad.value="";
			    alert("La cantidad a ser entregada no puede ser mayor a la disponible");
			    return;
			  }
		//cambiar por el resultado del autocompletar codigo:nombre:estado
		
		codi=document.form.material.value;
		var elem = codi.split(':');
		id_arti = elem[0];
		nombre_arti = elem[1];
		edo_arti = elem[2];
		
		for(i=0;i<factura_head.length;i++)
	      {
		   	if (factura_head[i][0]==id_arti)
		      {
			    alert("Este material ya ha sido ingresado");
			    return;
	          }
	      }
	
		if (id_arti==1165){//Kit infocentro en sai_item
			var tbody_factura = document.getElementById('body_factura_head');
			for(i=0;i<factura_head.length;i++)
			{
				tbody_factura.deleteRow(0);
			}
			<?php 
			$query = "SELECT t1.*,nombre FROM sai_articulo_kit t1, sai_item t2 WHERE t1.id=t2.id";
			$resultado = pg_exec($conexion, $query);
			$id_arti_kit = "";
			$cant_arti_kit = "";
					
			while($row=pg_fetch_array($resultado)){
				$id_arti_kit = "'".$row["id"]."'";
				$nombre_arti_kit = "'".$row["nombre"]."'";
				$cant_arti_kit = "'".$row["cantidad"]."'";
			?>
			var registro_factura_head = new Array(3);
			registro_factura_head[0] = <?= $id_arti_kit?>;//document.form.articulo.value;
			registro_factura_head[1] = <?= $nombre_arti_kit?>;//document.form.arti_nombre.value;
			registro_factura_head[2] = <?= $cant_arti_kit?>;
			factura_head[factura_head.length]=registro_factura_head;	
			<?php 	
			}?>
		 }
		else{
		var registro_factura_head = new Array(3);
		registro_factura_head[0] = id_arti;//document.form.articulo.value;
		registro_factura_head[1] = nombre_arti;//document.form.arti_nombre.value;
		registro_factura_head[2] = document.form.cantidad.value;
		factura_head[factura_head.length]=registro_factura_head;	

		var tbody_factura = document.getElementById('body_factura_head');
		var tbody_factura2 = document.getElementById(id);
  	   	for(i=0;i<factura_head.length-1;i++)
		 {
			tbody_factura.deleteRow(0);
		 }
	   }
	}




	

	if(tipo!=0)
	{
		var tbody_factura = document.getElementById('body_factura_head');
		var tbody_factura2 = document.getElementById(id);
		for(i=0;i<factura_head.length-1;i++)
		{
			tbody_factura.deleteRow(0);
		}
		
		tbody_factura.deleteRow(0);
		for(i=tipo;i<factura_head.length;i++)
		{
			factura_head[i-1]=factura_head[i];
		}
		factura_head.pop();
	}

	//var factura_actual= document.form.articulo.value;
	for(i=0;i<factura_head.length;i++)
	{
		//alert("agrega");
		var row = document.createElement("tr")
		if((i%2)==0)
			row.className = "reci2"
		else
			row.className = "reci"
					
		//Nombre del Artículo
		var td3 = document.createElement("td")
		td3.setAttribute("align","Center");
		td3.className = "normalNegro";
		td3.appendChild (document.createTextNode(factura_head[i][1]))

		//Cantidad
		var td4 = document.createElement("td")
		td4.setAttribute("align","Center");
		td4.className = "normalNegro";
		td4.appendChild (document.createTextNode(factura_head[i][2]))

		var td8 = document.createElement("td")
		td8.setAttribute("align","Center");
		td8.className = 'normal';
		editLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		editLink.setAttribute("href", "javascript:add_factura_head('"+id+"','"+(i+1)+"');");
		editLink.appendChild(linkText);  
		td8.appendChild (editLink)
		
		row.appendChild(td3);
		row.appendChild(td4);
		row.appendChild(td8);
	 	tbody_factura.appendChild(row);
	    document.form.material.value='';
	    document.form.cantidad.value="";

	}	
	    document.form.material.value='';
        document.form.cantidad.value="";
	 
}


function acceptFloat(evt){	
	// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57	'.'=46
	var nav4 = window.Event ? true : false;
	
	var key = nav4 ? evt.which : evt.keyCode;	

	return (key <= 13 || (key >= 48 && key <= 57) || key == 46);
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
     pri=1;}
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

	return (key <= 13 || (key >= 48 && key <= 57));

}

var titulo=1;
//-->

function leer_serial(id,tipo){	
    var cadena=document.form.serial.value;

    var encontrado=0;	  
    
	 for(i=0;i<listado_activos.length;i++)
	 {
	   var sactivo = listado_activos[i][1];
	   if (cadena==sactivo){
		   encontrado=1;  
	   }
	 }

	 if (encontrado==0  && id != 1){
		document.form.etiqueta.value=""; 
		alert("El serial del activo ingresado no est\u00E1 disponible para su asignaci\u00F3n");
		return
	  }
    
	var tbody_bien = document.getElementById('body_bienes');
	var tbody_bien2 = document.getElementById('listado_bienes');

	for(i=0;i<listado_bienes.length;i++)
	{
	 tbody_bien.deleteRow(0);
	}
	
	if(id==1)
	{
		for(i=tipo;i<listado_bienes.length;i++)
		{
			listado_bienes[i-1]=listado_bienes[i];
		}
		listado_bienes.pop();
	}


/*
 * VALIDAR QUE NO SE INGRESEN SERIALES REPETIDOS*/
	codi=document.form.serial.value;
	var encontrado=0;
	for(i=0;i<listado_bienes.length;i++)
   {
	   	if (listado_bienes[i][0]==codi)
	      {
		    encontrado=1;
       }
   }
 
	if (encontrado==0){
      var registro_bien = new Array(1);
	  registro_bien[0] = document.form.serial.value;
	  longitud = cadena.length;
	  if (cadena.length>0){
	   listado_bienes[listado_bienes.length]=registro_bien;
	  }
	}
	for(i=0;i<listado_bienes.length;i++)
	{
		
	var row = document.createElement("tr")
	var td1 = window.opener.document.createElement("td");
	td1.setAttribute("align","center");
	var imp_1 = document.createElement("INPUT");
	imp_1.setAttribute("type","text");
	name="serial"+i;
	imp_1.setAttribute("name",name);
	imp_1.setAttribute("value",listado_bienes[i][0]);
	imp_1.setAttribute("readOnly","true");
	imp_1.className = "normalNegro";
	imp_1.setAttribute("id",name);
	imp_1.setAttribute("size","20");
	td1.appendChild (imp_1);	

	editLink = document.createElement("a");
	linkText = document.createTextNode("Eliminar");
	editLink.setAttribute("href", "javascript:leer_serial('"+(1)+"','"+(i+1)+"');");
	editLink.appendChild(linkText);  
	td1.appendChild (editLink)
	row.appendChild(td1);
 	tbody_bien.appendChild(row);
	}	

	if (encontrado==1){
	    alert("Este Activo ya ha sido ingresado");
	}

    document.form.serial.value="";
    document.form.serial.focus();
	return
  
}


 function leer_codigo(id,tipo){	

	 var encontrado=0;	  
     var cadena=document.form.etiqueta.value;

		 for(i=0;i<listado_activos.length;i++)
		 {
		   var sbn = listado_activos[i][0];
		   if (cadena==sbn){
			   encontrado=1;  
		   }
		 }

		 if (encontrado==0 && id != 1){
			document.form.etiqueta.value=""; 
			alert("El serial de bien nacional ingresado no est\u00E1 disponible para su asignaci\u00F3n");
			return
		  }
	 

		var tbody_bien = document.getElementById('body_bienes2');
		var tbody_bien2 = document.getElementById('listado_mobiliario');

		for(i=0;i<listado_mobiliario.length;i++)
		{
		 tbody_bien.deleteRow(0);
		}
		
		if(id==1)
		{
			for(i=tipo;i<listado_mobiliario.length;i++)
			{
				listado_mobiliario[i-1]=listado_mobiliario[i];
			}

			listado_mobiliario.pop();

		}

		/*
		 * VALIDAR QUE NO SE INGRESEN SERIALES REPETIDOS*/
			codi=document.form.etiqueta.value;
			var encontrado=0;
			for(i=0;i<listado_mobiliario.length;i++)
		   {
			   	if (listado_mobiliario[i][0]==codi)
			      {
				    encontrado=1;
	    	      }
		   }
		if (encontrado==0){
	      var registro_bien = new Array(1);
		  registro_bien[0] = document.form.etiqueta.value;
		  longitud = cadena.length;
		  if (cadena.length>0){
			listado_mobiliario[listado_mobiliario.length]=registro_bien;
		  }
		}
		for(i=0;i<listado_mobiliario.length;i++)
		{
			
		var row = document.createElement("tr")
		var td1 = window.opener.document.createElement("td");
		td1.setAttribute("align","center");
		var imp_1 = document.createElement("INPUT");
		imp_1.setAttribute("type","text");
		name="etiqueta"+i;
		imp_1.setAttribute("name",name);
		imp_1.setAttribute("value",listado_mobiliario[i][0]);
		imp_1.setAttribute("readOnly","true");
		imp_1.className = "normalNegro";
		imp_1.setAttribute("id",name);
		imp_1.setAttribute("size","20");
		td1.appendChild (imp_1);	

		editLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		editLink.setAttribute("href", "javascript:leer_codigo('"+(1)+"','"+(i+1)+"');");
		editLink.appendChild(linkText);  
		td1.appendChild (editLink)
		row.appendChild(td1);
	 	tbody_bien.appendChild(row);
		}	
		if (encontrado==1){
	    alert("Este activo ya ha sido ingresado");
		}
	    	    
	    document.form.etiqueta.value="";
	    document.form.etiqueta.focus();
		return
	  
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
<form action="salida_activos_Accion.php" name="form" id="form"
	method="post"><input type="hidden" value="0" name="hid_validar" />
<table width="700" align="center" background="imagenes/fondo_tabla.gif"
	class="tablaalertas" id="sol_via">
	<tr>
		<td height="15" colspan="2" valign="midden" class="td_gray"><span
			class="normalNegroNegrita"> Salida de activos y/o materiales </span></td>
	</tr>
	<tr bgcolor="#F0F0F0" class="normalNegrita">
		<td colspan="2">Datos generales</td>
	</tr>
	<tr>
		<td class="normal"><strong>Dependencia solicitante</strong></td>
		<td><select name="opt_depe[]" class="normalNegro" id="opt_depe"	multiple>
			<option value="" class="normalNegrita"><b>[Selecci&oacute;n M&uacute;ltiple]</b></option>
			<?php
			$sql_str="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_id,depe_nombre','depe_nivel=4 or depe_id=150','depe_nombre',1) resultado_set (depe_id varchar, depe_nombre varchar)";
			$res_q=pg_exec($sql_str) or die("Error al mostrar");
			$i=0;
			while($depe_row=pg_fetch_array($res_q)){
				$depe_id=$depe_row['depe_id'];
				$depe_nombre=$depe_row['depe_nombre'];
				?>
			<option value="<?php echo(trim($depe_row['depe_id'])); ?>"><?php echo(trim($depe_row['depe_nombre'])); ?></option>
			<?php }?>
		</select></td>
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
		<td><select class="normalNegro" name="ubicacion" id="ubicacion" onchange="javascript:activar_campo()"> 
		<option value="0">[Seleccione]</option>
		<?php 
	    $sql_p="Select * from sai_bien_ubicacion where
			esta_id=1  and bubica_id<>2 order by bubica_nombre";
	 $resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
	 while($row=pg_fetch_array($resultado_set_most_p))
	 {
	 	?>
		<option value="<?=$row['bubica_id']; ?>"><?php echo $row['bubica_nombre'];?></option>
		<?php }?>
		</select>
		

		</td>
	</tr>
	<tr>
		<td>
		<div align="left" class="normal"><strong>Infocentro:</strong></div>
		</td>
		<td><input type="text" name="infocentro" id="infocentro" class="normalNegro" size="70" onChange="validarRif(this)"  autocomplete="off">
		<!-- 	<input type="text" name="dir_info" id="dir_info"> -->
			<?php 	
			
			$query = "SELECT nemotecnico,t1.nombre,t2.nombre as n_edo,direccion ".
				 "FROM ".
				 "safi_infocentro t1,safi_estatus_general t2 ".
				 "WHERE t2.id=id_estatus_general ".
				 "ORDER BY nombre";
			
			$resultado = pg_exec($conexion, $query);
			$numeroFilas = pg_num_rows($resultado);
			$arregloProveedores = "";
			$cedulasProveedores = "";
			$nombresProveedores = "";
			$indice=0;
			while($row=pg_fetch_array($resultado)){
				$arregloProveedores .= "'".$row["nemotecnico"]." : ".strtoupper(str_replace("\n"," ",$row["nombre"]))."(".$row["n_edo"].")"."',";
				$cedulasProveedores .= "'".$row["nemotecnico"]."',";
				//$dirInfo .= "'".$row["direccion"]."',";
				$indice++;
			}
			$arregloProveedores = substr($arregloProveedores, 0, -1);
			$cedulasProveedores = substr($cedulasProveedores, 0, -1);
			$dirInfo = substr($dirInfo, 0, -1);
			?> <script>
					var proveedor = new Array(<?= $arregloProveedores?>);
					var arreglo_rif = new Array(<?= $cedulasProveedores?>);
					this.actb_delimiter = new Array(' ',',');
					obj = new actb(document.getElementById('infocentro'),proveedor);
					//obj11= new actb(document.getElementById('dir_info'),dir_info);
					
					//actb(document.getElementById('infocentro'),proveedor);
				</script></td>
	</tr>

	<tr>
		<td>
		<div align="left" class="normal"><strong>Detalle destino: <strong></strong>
		
		</td>
		<td><textarea name="destino" cols="50"></textarea>
		</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<table width="700" border="0" align="center" cellpadding="1"
			cellspacing="1" class="tablaalertas" id="factura_head">
			<tr>
			</tr>
			<!--  -->
			<tr bgcolor="#F0F0F0" width="100%">
				<td colspan="2">
				<div align="left" class="normalNegrita">Activos</div>
				</td>
			</tr>
			<tr>
				<td height="10">
				<div align="center" class="normal"></div>
				</td>
			</tr>
			<tr>
				<td>
				<div align="center" width="10" class="normal"><strong>Serial
				activo: <strong> <input name="serial" type="text"
					class="normalNegro" id="serial" size="15"
					onChange="leer_serial(0,0)"> <!-- 	<a href="javascript: add_bienes('listado_bienes','0'); " class="normal">
				Agregar Serial    
			</a> --></div>
				</td>

			</tr>

			<?
			//}
			?>

			<tbody id="body_bienes" class="normal" align="center">

			</tbody>

			<input type="hidden" name="txt_arreglo_bienes_head"
				id="txt_arreglo_bienes_head" />

		</table>
		</td>
	</tr>

	<tr>
		<td colspan="2">
		<table width="700" border="0" align="center" cellpadding="1"
			cellspacing="1" class="tablaalertas" id="factura_head">
			<tr>
			</tr>
			<tr bgcolor="#F0F0F0" width="100%">
				<td colspan='4'>
				<div align="left" class="normalNegrita">Materiales</div>
				</td>
			</tr>
			<tr width="100%">
				<td>
				<div align="center" class="normalNegrita">Descripci&oacute;n</div>
				</td>
				<td>
				<div align="center" class="normalNegrita">Disponibilidad</div>
				</td>
				<td>
				<div align="center" class="normalNegrita">Cantidad</div>
				</td>
				<td>
				<div align="center" class="normalNegrita">Opci&oacute;n</div>
				</td>
			</tr>
			<tr>
				<td>
				<div align="center"><span>
				<input type="text" name="material"   id="material"   class="normalNegro" size="40">
			<?php 	
					$condicion=" ";
					
				$query = "SELECT * ".
				 "FROM ".
				 "sai_item t1,sai_item_articulo t2 ".
				 "WHERE t1.id=t2.id and esta_id=1 and id_tipo=1 ORDER BY nombre";
			
			$resultado = pg_exec($conexion, $query);
			$numeroFilas = pg_num_rows($resultado);
			$arregloProveedores = "";
			$cedulasProveedores = "";
			$nombresProveedores = "";
			$indice=0;
			while($row=pg_fetch_array($resultado)){
				$arregloProveedores .= "'".$row["id"]." : ".strtoupper(str_replace("\n"," ",$row["nombre"]))."(".$row["unidad_medida"].")"."',";
				$cedulasProveedores .= "'".$row["id"]."',";
				$nombresProveedores .= "'".str_replace("\n"," ",strtoupper($row["nombre"]))."',";
				$indice++;
			}
			$arregloProveedores = substr($arregloProveedores, 0, -1);
			$cedulasProveedores = substr($cedulasProveedores, 0, -1);
			$nombresProveedores = substr($nombresProveedores, 0, -1);
			?> <script>
					var articulos = new Array(<?= $arregloProveedores?>);
					var arreglo_rif = new Array(<?= $cedulasProveedores?>);
					var nombre_proveedor= new Array(<?= $nombresProveedores?>);
					this.actb_delimiter = new Array(' ',',');
					obj2 = new actb(document.getElementById('material'),articulos);
					//actb(document.getElementById('material'),articulos);
				</script>
				
				<!--  <input name="arti_nombre" type="hidden" id="arti_nombre" /> --></span></div>
				</td>
				<td>
				<div align="center" class="peq"><input name="disponibilidad" type="text"
					class="normalNegro" id="disponibilidad" size="6" maxlength="6" value="" disabled></div>
				</td>
				<td>
				<div align="center" class="peq"><input name="cantidad" type="text"
					class="normalNegro" id="cantidad"
					onKeyUp="javascript: validar(this,0)" size="6" maxlength="6" onfocus="validar_disponibilidad()"></div>
				</td>
				<td>
				<div align="center" class="normal"><a
					href="javascript: add_factura_head('factura_head','0'); "
					class="normal">Agregar </a></div>
				</td>
			</tr>
			<tbody id="body_factura_head" class="normal">
			</tbody>
		</table>
		</td>
	</tr>

	<tr>
		<td colspan="2">
		<table width="700" border="0" align="center" cellpadding="1"
			cellspacing="1" class="tablaalertas" id="factura_head">
			<tr>
			</tr>
			<tr bgcolor="#F0F0F0" width="100%">
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
				<div align="center" class="normal"><strong>Serial Bien Nacional: <strong>
				<input name="etiqueta" type="text" class="normalNegro" id="etiqueta" size="15" onChange="leer_codigo(0)"> 
					<!-- 	<a href="javascript: add_bienes('listado_bienes','0'); " class="normal">
				Agregar Serial    
			</a> --></div>
				</td>
			</tr>
			<tbody id="body_bienes2" class="normal" align="center">

			</tbody>



		</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="button"
			value="Generar Salida" onclick="javascript:revisar();"  class="normalNegro"></input></td>
	</tr>
	<input type="hidden" name="txt_arreglo_activos"
		id="txt_arreglo_activos" />
	<input type="hidden" name="txt_arreglo_articulos"
		id="txt_arreglo_articulos" />
	<input type="hidden" name="txt_arreglo_mobiliario"
		id="txt_arreglo_mobiliario" />
	</form>

</body>
</html>
