<?
ob_start();
require_once("../../includes/conexion.php");
require("../../includes/perfiles/constantesPerfiles.php");
require("../../includes/fechas.php");
 
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}

ob_end_flush();
$acta = $_GET['codigo'];
 
 //$_GET['accion']=="Anular";
 $accion = $_GET['accion'];
 
if (substr($acta,0,1) == 'a') $tipo = 'A'; //ASIGNACIÓN
else $tipo = 'R'; //REASIGNACIÓN
 
 if ($accion == "Anular")
 {
 	// Verificar si se puede anular el acta de salida/reasignación.
 	// Solo es posible la anulación si ninguno de los bienes en el acta,
 	// se encuentra en un acta posterior a la que se desea anular.
	 	
	$query = "
		SELECT
			datos.acta_id,
			datos.fecha,
			to_char(datos.fecha, 'DD/MM/YYYY HH24:MI:SS') AS str_fecha,
			datos.clave_bien,
			item.nombre,
			marca.bmarc_nombre AS marca,
			entrada_detalle.modelo AS modelo,
			entrada_detalle.serial AS serial_activo,
			entrada_detalle.etiqueta AS serial_bien_nacional
		FROM
			(
				-- Entradas
				SELECT
					entrada.acta_id AS acta_id,
					entrada.esta_id,
					entrada_detalle.clave_bien,
					entrada.fecha_registro AS fecha
				FROM
					sai_bien_inco entrada
					INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.acta_id = entrada.acta_id)
		
				UNION
				-- Salidas
				SELECT
					salida.asbi_id AS acta_id,
					salida.esta_id,
					salida_detalle.clave_bien,
					salida.asbi_fecha AS fecha
				FROM
					sai_bien_asbi salida
					INNER JOIN sai_bien_asbi_item salida_detalle ON (salida_detalle.asbi_id = salida.asbi_id)					
		
				UNION
				--Reasignaciones
		
				SELECT
					reasignacion.acta_id AS acta_id,
					reasignacion.esta_id,
					reasignacion_detalle.clave_bien,
					reasignacion.fecha_acta AS fecha
				FROM
					sai_bien_reasignar reasignacion
					INNER JOIN sai_bien_reasignar_item reasignacion_detalle ON (reasignacion_detalle.acta_id = reasignacion.acta_id)
		
			) AS acta
			INNER JOIN
			(
				-- Salidas
				SELECT
					salida.asbi_id AS acta_id,
					salida.esta_id,
					salida_detalle.clave_bien AS clave_bien,
					salida.asbi_fecha AS fecha
				FROM
					sai_bien_asbi salida
					INNER JOIN sai_bien_asbi_item salida_detalle ON (salida_detalle.asbi_id = salida.asbi_id)
		
				UNION
				--Reasignaciones
		
				SELECT
					reasignacion.acta_id AS acta_id,
					reasignacion.esta_id,
					reasignacion_detalle.clave_bien AS clave_bien,
					reasignacion.fecha_acta AS fecha
				FROM
					sai_bien_reasignar reasignacion
					INNER JOIN sai_bien_reasignar_item reasignacion_detalle ON (reasignacion_detalle.acta_id = reasignacion.acta_id)
					
			) AS datos ON (
				datos.clave_bien = acta.clave_bien
				AND datos.fecha > acta.fecha
			)
			INNER JOIN sai_biin_items entrada_detalle
				ON (entrada_detalle.clave_bien = datos.clave_bien)
			INNER JOIN sai_item item ON (item.id = entrada_detalle.bien_id)
			INNER JOIN sai_bien_marca marca
				ON (marca.bmarc_id = entrada_detalle.marca_id)
		WHERE
			datos.esta_id <> 15
			AND acta.acta_id = '".$acta."'
		ORDER BY
			datos.fecha
	";
	
	$datosAnulacion = null;
	
	if(($resultado = pg_exec($conexion, $query)) === false)
	{
		echo "Problemas al verificar la posibilidad de anular el acta.";
		error_log("Problemas al verificar la posibilidad de anular el acta. Detalles: " . pg_last_error($conexion));
		exit;
	} else {
		$datosAnulacion = array();
		while($row = pg_fetch_array($resultado))
		{
			$datosAnulacion[] = $row;
		}
	}
	
	
	// Borrar
	/*
	echo "Exito";
	echo "<pre>";
	print_r($datosAnulacion);
	echo "</pre>";
	exit;
	*/
 	
 	if($tipo == 'A') // Asignación
 	{
 		
 		
 	} else if ($tipo == 'R') // Reasignación
 	{
 		
 	}
 }
 
 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all" />
<link rel="stylesheet" href="../../css/safi0.2.css" type="text/css" media="all" />
<script type="text/javascript" src="../../js/funciones.js"> </script>
<script type="text/javascript" src="../../js/lib/actb.js"></script>
<script type="text/javascript" src="../../js/funciones.js"></script>

<script language="javascript">
var listado_bienes = new Array();
var listado_mobiliario = new Array();
var factura_head = new Array();
var listado_activos = new Array();
var listado_disponibilidad = new Array();



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
	//return false;
	alert("Este RIF indicado no es v"+aACUTE+"lido");
	document.form1.rif_sugerido.focus();
			
	}*/
}


function enviar(tipo)
{
	var contenido = null;
	document.form.opcion.value=tipo;
	
	while ((contenido = prompt("Indique el motivo de la anulaci\u00F3n: ","")) != null)
	{
		if(contenido != ""){
			document.getElementById('contenido_memo').value=contenido;
			document.form.submit();
			break;
		} else {
			alert("Debe indicar el motivo de la anulaci"+oACUTE+"n.");
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
		
		if(document.form.cantidad.value=="")
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
	
	  /*  for (i=0;i<ori.length;i++)
		{
			if(ori[i][0]==document.form.articulo.value)
			{	*/
				//document.form.arti_nombre.value=nombre_arti;//ori[i][1];
		
		/*	}
		}*/

		var registro_factura_head = new Array(3);
		registro_factura_head[0] = id_arti;
		registro_factura_head[1] = nombre_arti;
		registro_factura_head[2] = document.form.cantidad.value;
		factura_head[factura_head.length]=registro_factura_head;
	}
	
	var tbody_factura = document.getElementById('body_factura_head');
	var tbody_factura2 = document.getElementById(id);


	for(i=0;i<factura_head.length-1;i++)
	{
		tbody_factura.deleteRow(0);
	}
	
	if(tipo!=0)
	{
		tbody_factura.deleteRow(0);
		for(i=tipo;i<factura_head.length;i++)
		{
			factura_head[i-1]=factura_head[i];
		}
		factura_head.pop();
	}

	for(i=0;i<factura_head.length;i++)
	{
		
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

	 if (encontrado==0){
		document.form.serial.value=""; 
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
	    alert("Este Art\u00EDculo Ya Ha Sido Ingresado");
	}
    document.form.serial.value="";
    document.form.serial.focus();
	return
  
}


function leer_codigo(id,tipo){	
	    var cadena=document.form.etiqueta.value;

		 var encontrado=0;	  
	     var cadena=document.form.etiqueta.value;

			 for(i=0;i<listado_activos.length;i++)
			 {
			   var sbn = listado_activos[i][0];
			   if (cadena==sbn){
				   encontrado=1;  
			   }
			 }

			 if (encontrado==0){
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
	    alert("Este Art\u00EDculo Ya Ha Sido Ingresado");
		}
	    	    
	    document.form.etiqueta.value="";
	    document.form.etiqueta.focus();
		return
	  
	}

</script>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?php 
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
			
		

 if (substr($acta,0,1)=='a'){//ASIGNACION
 
   $sql_salida="SELECT asbi_destino, infocentro,solicitante,ubicacion,acta_almacen,asbi_fecha,
   				to_char(asbi_fecha, 'DD/MM/YYYY HH24:MI:SS') as str_fecha,
    			case infocentro when null then '' else 
 				(select info_nombre from sai_infocentro where info_id=infocentro) end as info
				FROM sai_bien_asbi t1
 				WHERE t1.asbi_id='".$acta."'";
   $sql_e="select etiqueta,serial from sai_biin_items where esta_id=41 order by etiqueta";
 }else{//REASIGNACION
 	$sql_salida="SELECT destino as asbi_destino, infocentro,solicitante,ubicacion,'' as acta_almacen,fecha_acta as asbi_fecha,
 				to_char(fecha_acta, 'DD/MM/YYYY HH24:MI:SS') as str_fecha,
 				 case infocentro when null then '' else 
 				 (select info_nombre from sai_infocentro where info_id=infocentro) end as info
 				 FROM sai_bien_reasignar t1
 				 WHERE t1.acta_id='".$acta."'";
 	$sql_e="select etiqueta,serial from sai_biin_items where esta_id=53 order by etiqueta";
 }
 
 					
$i=0;
$resultado_entrada=pg_query($conexion,$sql_e) or die("Error al consultar edisponibilidad");  
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
 
 
 $resultado_salida=pg_query($conexion,$sql_salida) or die("Error al consultar el acta");
 if($rowd=pg_fetch_array($resultado_salida)) 
 {
 $info="";
  $destino=$rowd['ubicacion'];	 
  $detalle_destino=$rowd['asbi_destino'];
  if ($rowd['infocentro']<>'')
  $info=$rowd['infocentro'].":".$rowd['info'];
  $solicita=$rowd['solicitante'];
  $vector_depe=explode ("," , $solicita);
  $acta_almacen=$rowd['acta_almacen'];
  $fecha_acta=cambia_esp($rowd['asbi_fecha']);
  $strFecha = $rowd['str_fecha'];
 }
 $buscar_art="SELECT * FROM sai_bien_asbi_item WHERE asbi_id='".$acta."' and arti_id<>''";
 $result_art=pg_query($conexion,$buscar_art);
 $nroFilas = pg_num_rows($result_art);
 $almacen=0;
 if($nroFilas>0) {
  $almacen=1;		
 }
 if ($_GET['accion']=="Anular"){
  $titulo="Anular acta salida";
  $activar="readonly";
  $desactivar_select="onFocus='Javascript:this.blur()'";
 } elseif ($_GET['accion']=="Visto Bueno") {
  $activar="";$desactivar_select="multiple";
  $titulo="Revisar salida de activos";
 }else{
 	$titulo="Modificar salida de activos";
 }
?>
<form action="modificar_salida_activos_Accion.php" name="form" id="form" method="post">
<input type="hidden" value="<?=$acta;?>" name="codigo">
<input type="hidden" value="0" name="hid_validar" />
<input type="hidden" value="<?=$acta_almacen;?>" name="acta_almacen">
<input type="hidden" value="<?=$fecha_acta;?>" name="fecha_acta">
<input type="hidden" name="opcion"></input>
<input type="hidden" name="contenido_memo" id="contenido_memo">
<table width="700" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" id="sol_via">
  <tr>
	<td height="15" colspan="2" valign="midden" class="td_gray"><span class="normalNegroNegrita"> 
	  <?=$titulo;?><input type="hidden" value="<?=$titulo;?>" name="titulo"></span></td>
  </tr>
  <tr>
	<td class="normal"><strong>N&deg; acta</strong></td>
	<td class="normalNegro"><strong></strong><?php echo $acta;?> 
	<?php if (substr($acta,0,1)=='a'){?>
	<a target="_blank" class="normal" href="salida_activos_pdf.php?codigo=<?=$acta;?>&tipo=s">Consultar Detalle</a>
	<?php }else{?>
	<a target="_blank" class="normal" href="reasignar_activos_pdf.php?codigo=<?=$acta;?>&tipo=s">Consultar Detalle</a>
<?php }?>
	</td>
  </tr>
  <tr>
  	<td class="normalNegrita">Fecha</td>
  	<td><?=$strFecha;?></td>
  </tr>
  <tr>
	<td class="normal"><strong>Dependencia solicitante</strong></td>
	<td><select name="opt_depe[]" class="normalNegro" id="opt_depe"	<?=$desactivar_select;?>>
	     <option value="" class="normalNegrita"><b>[Selecci&oacute;n M&uacute;ltiple]</b></option>
		 <?php
			$sql_str="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_id,depe_nombre','depe_nivel=4 or depe_id=150','depe_nombre',1) resultado_set (depe_id varchar, depe_nombre varchar)";
			$res_q=pg_exec($sql_str) or die("Error al mostrar");
			$i=0;
			while($depe_row=pg_fetch_array($res_q))
			{
			  $depe_id=$depe_row['depe_id'];
			  $depe_nombre=$depe_row['depe_nombre'];
			  $buscar=0;	
			  for ($i=0;$i<count($vector_depe);$i++)    
			  {     
 			   if ($vector_depe[$i]==$depe_row['depe_id']){?>
                <option value="<?php echo(trim($depe_row['depe_id'])); ?>" selected="selected"><?php echo(trim($depe_row['depe_nombre'])); ?></option>
 			    <?php 
			      $buscar=1;
 			   }
 			  }
 			  if ($buscar==0){
 			  ?>
				<option value="<?php echo(trim($depe_row['depe_id'])); ?>"><?php echo(trim($depe_row['depe_nombre'])); ?></option>
			<?php }
		   }?>
		</select></td>
  </tr>
  <tr>
	<td class="normal"><strong>Destino</strong></td>
	<td><select class="normalNegro" name="ubicacion" id="ubicacion" onchange="javascript:activar_campo()" <?=$desactivar_select;?>> 
	  	 <option value="0">[Seleccione]</option>
		 <?php 
		  if (substr($acta,0,1)=='a')
	       $sql_p="Select * from sai_bien_ubicacion where esta_id=1  and bubica_id<>2 order by bubica_nombre";
	      else 
	       $sql_p="Select * from sai_bien_ubicacion where esta_id=1 order by bubica_nombre";
		   $resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
		   while($row=pg_fetch_array($resultado_set_most_p))
		   {
	    	if ($destino==$row['bubica_id']){?>
		    <option value="<?=$row['bubica_id']; ?>" selected="selected"><?php echo $row['bubica_nombre'];?></option>
		    <?php } else { ?>
		    <option value="<?=$row['bubica_id']; ?>" ><?php echo $row['bubica_nombre'];?></option>
		<?php }}?>
		</select>
		</td>
  </tr>
  <tr>
	<td><div align="left" class="normal"><strong>Infocentro:</strong></div></td>
	<td><input type="text" name="infocentro" id="infocentro" class="normalNegro" size="70" onChange="validarRif(this)" value="<?=$info;?>" <?=$activar;?>>
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
			//	$dirInfo .= "'".$row["direccion"]."',";
				$indice++;
			}
			$arregloProveedores = substr($arregloProveedores, 0, -1);
			$cedulasProveedores = substr($cedulasProveedores, 0, -1);
		//	$dirInfo = substr($dirInfo, 0, -1);
			?> <script>
					var proveedor = new Array(<?= $arregloProveedores?>);
					var arreglo_rif = new Array(<?= $cedulasProveedores?>);
					//var dir_info= new Array(<?= $dirInfo?>);
					this.actb_delimiter = new Array(' ',',');
					obj = new actb(document.getElementById('infocentro'),proveedor);
				</script></td>
  </tr>
  <tr>
	<td><div align="left" class="normal"><strong>Detalle destino: <strong></strong></td>
	<td><textarea name="destino" cols="50" rows="6" <?=$activar;?>><?php echo $detalle_destino;?></textarea></div></td>
  </tr>
  <?php if ($_GET['accion']=="Visto Bueno" || $_GET['accion']=="Modificar"){?>
   <tr>
	<td><div align="left" class="normal"><strong>Activos Agregados: </strong><br><font color="FF0000">* Marque la casilla para Eliminar</font></td>
	<td width="10%" class="normalNegro">
	<?php 
	if (substr($acta,0,1)=='a'){
		$query_activos="
			SELECT
				t1.clave_bien,
				etiqueta
			FROM
				sai_bien_asbi_item t1,
				sai_biin_items t2
			WHERE
				t1.asbi_id='".$acta."'
				AND t1.clave_bien<>0
				AND t1.clave_bien=t2.clave_bien
		";
	}
	else
	$query_activos="Select t1.clave_bien,etiqueta FROM sai_bien_reasignar_item t1,sai_biin_items t2 WHERE t1.acta_id='".$acta."' and t1.clave_bien<>0 and t1.clave_bien=t2.clave_bien";
	$resultado_activos = pg_exec($conexion, $query_activos);
		  
		  while ($row_activos=pg_fetch_array($resultado_activos)){
	?>
	<input type="checkbox" name="activos_eliminar[]" value="<?php echo $row_activos['clave_bien'];?>" ><?php echo $row_activos['etiqueta'];?><br>
    <?php }?>
	</td>
  </tr>
  
  
	<?php }
	  $motivo_devolucion="";
	  $memo="SELECT max(oid) as oid_memo FROM sai_docu_sopor t1 WHERE  doso_doc_fuente='".$acta."' group by doso_doc_fuente";
	  $result_memo=pg_query($conexion,$memo);
	  if ($row_memo=pg_fetch_array($result_memo)){
	  	
	   $motivo="SELECT memo_contenido FROM sai_docu_sopor t1,sai_memo t2 WHERE memo_id=doso_doc_soport and
       t1.oid='".$row_memo['oid_memo']."'";
	   $result_motivo=pg_query($conexion,$motivo);
	   if ($row_motivo=pg_fetch_array($result_motivo)){
	    $motivo_devolucion=strtoupper($row_motivo['memo_contenido']);	
	   }  
	  }
	  
	  if ($motivo_devolucion<>""){
	?>
  <tr>
	<td><div align="left" class="normal"><strong>Motivo devoluci&oacute;n: <strong></strong></td>
	<td><div align='left'><font color='Red'><STRONG><?php echo $motivo_devolucion;?></STRONG></font></div></td>
  </tr>
	<?php }
	 if ($_GET['accion']!="Anular"){?>
  <tr>
	<td colspan="2">
	 <table width="700" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
	  <tr></tr>
	  <tr bgcolor="#F0F0F0" width="100%">
		<td colspan="2"><div align="left" class="normalNegrita">Agregar activos</div></td>
	  </tr>
	  <tr>
		<td height="10"><div align="center" class="normal"></div></td>
	  </tr>
	  <tr>
		<td><div align="center" width="10" class="normal"><strong>Serial activo: <strong> 
			<input name="serial" type="text" class="normalNegro" id="serial" size="15" onChange="leer_serial(0,0)"> </div></td>
      </tr>
		<tbody id="body_bienes" class="normal" align="center"></tbody>
		 <input type="hidden" name="txt_arreglo_bienes_head" id="txt_arreglo_bienes_head" />
  </table>
   </td>
  </tr>
  <tr>
	<td colspan="2">
	  <table width="700" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
		<tr></tr>
		<tr bgcolor="#F0F0F0" width="100%">
		  <td colspan='3'><div align="left" class="normalNegrita">Agregar Materiales</div></td>
		</tr>
		<tr width="100%">
		  <td><div align="center" class="normalNegrita">Descripci&oacute;n</div></td>
		  <td><div align="center" class="normalNegrita">Disponibilidad</div></td>
		  <td><div align="center" class="normalNegrita">Cantidad</div></td>
		  <td><div align="center" class="normalNegrita">Opci&oacute;n</div></td>
		</tr>
		<tr>
		  <td><div align="center"><span><input type="text" class="normalNegro" name="material" id="material" value="" size="40"></input>
		  <?php 	
			   if ($_SESSION['user_perfil_id'] == PERFIL_ALMACENISTA )
					//$condicion=" and tipo in ('3','8') ";
					$condicion=" ";
					else
					$condicion=" ";
					
				$query = "SELECT * ".
				 "FROM ".
				 "sai_item t1,sai_item_articulo t2 ".
				 "WHERE t1.id=t2.id and esta_id=1 and id_tipo=1 ".$condicion." ORDER BY nombre";
			
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
				
				</script>
				
				</span></div></td>
		 <td>
			<div align="center" class="peq"><input name="disponibilidad" type="text"
				class="normalNegro" id="disponibilidad" size="6" maxlength="6" value="" disabled></div>
		</td>
		  <td>
			<div align="center" class="peq"><input name="cantidad" type="text" class="normalNegro" id="cantidad"
					onKeyUp="javascript: validar(this,0)" size="6" maxlength="6" onfocus="validar_disponibilidad()"></div></td>
          <td>
			<div align="center" class="normal"><a href="javascript: add_factura_head('factura_head','0'); "
					class="normal">Agregar </a></div></td>
		</tr>
			<tbody id="body_factura_head" class="normal">
			</tbody>
		</table>
	  </td>
	</tr>
	<tr>
	  <td colspan="2">
		<table width="700" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
		  <tr></tr>
		  <tr bgcolor="#F0F0F0">
			 <td colspan='3'><div align="left" class="normalNegrita">Agregar Mobiliario</div></td>
 		  </tr>
		  <tr>
			 <td height="10"><div align="center" class="normal"></div></td>
		  </tr>
		  <tr>
			 <td><div align="center" class="normal"><strong>Serial Bien Nacional: </strong>
				<input name="etiqueta" type="text" class="normalNegro" id="etiqueta" size="15" onChange="leer_codigo(0)">
				</div></td>
			</tr>
			<tbody id="body_bienes2" class="normal" align="center">
			</tbody>
		</table>
		</td>
	</tr>
	<?php }?>
	<tr>
	<td colspan="2" align="center">
		<?php
			if ($_GET['accion']=="Anular"){
				if(count($datosAnulacion) == 0){
		?>
		<input type="button" value="Anular" class="normalNegro" onclick="enviar(15)">
		<?php
			}
			} elseif ($_GET['accion']=="Visto Bueno"){ ?>
		<input type="button" value="Visto Bueno" onclick="javascript:revisar();"  class="normalNegro"/>
		<?php 
			}else { ?>
		<input type="button" value="Modificar" onclick="javascript:revisar();"  class="normalNegro"></input>	
		<?php
			}
		?>
		</td>
	</tr>
	</table>
	<input type="hidden" name="txt_arreglo_activos" id="txt_arreglo_activos"/>
	<input type="hidden" name="txt_arreglo_articulos" id="txt_arreglo_articulos" />
	<input type="hidden" name="txt_arreglo_mobiliario" id="txt_arreglo_mobiliario" />
	</form>
	<?php
		if ($_GET['accion']=="Anular"){
			if(count($datosAnulacion) > 0)
			{
				$tdClass = "even";
				
				echo '
	<table border="0" align="center" cellpadding="0" cellspacing="0" class="tablaalertas">
		<tr>
			<td class="normal" style="color: red;">
				El acta no puede ser anulada. Los siguientes activos se encuentran en actas con fecha
				posterior a la fecha del acta actual.
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" align="center" cellpadding="0" cellspacing="0" class="tablaalertas" id="factura_head"
					style="background-image: url(\'../../imagenes/fondo_tabla.gif\');"
				>
					<tr>
						<td class="header normalNegroNegrita">Acta</td>
						<td class="header normalNegroNegrita">Fecha</td>
						<td class="header normalNegroNegrita">Nombre</td>
						<td class="header normalNegroNegrita">Marca</td>
						<td class="header normalNegroNegrita">Modelo</td>
						<td class="header normalNegroNegrita">Serial activo</td>
						<td class="header normalNegroNegrita">Serial bien nacional</td>
					</tr>
				';
				foreach ($datosAnulacion AS $row)
				{
					$tdClass = ($tdClass == "even") ? "odd" : "even";
					
					echo '
					<tr class="'.$tdClass.'" onclick="Registroclikeado(this);">
						<td>'.$row['acta_id'].'</td>
						<td>'.$row['str_fecha'].'</td>
						<td>'.$row['nombre'].'</td>
						<td>'.$row['marca'].'</td>
						<td>'.$row['modelo'].'</td>
						<td>'.$row['serial_activo'].'</td>
						<td>'.$row['serial_bien_nacional'].'</td>
					</tr>	
					';
				}
				echo '
				</table>
			</td>
		</tr>
	</table>
				';
			}
		}
	?>
</body>
</html>
