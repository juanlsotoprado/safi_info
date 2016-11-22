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
var listado_activos = new Array();
</script>

<?php 
$i=0;
$sql_e="select etiqueta,serial from sai_biin_items where esta_id=53 order by etiqueta";
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
		?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all" />
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<script language="JavaScript" src="../../js/lib/actb.js"></script>

<script language="javascript">

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
	if(document.form.tipo_r.value=='0')
	{
		window.alert("Debe indicar el tipo de re-asignaci\u00F3n");
		document.form.tipo_r.focus();
		return
	}
	
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
		window.alert("Debe especificar el detalle del destino de los activos");
		document.form.destino.focus();
		return
	}

	if ((listado_bienes.length==0) && (listado_mobiliario.length==0))
	{
		window.alert("No se registr\u00F3 ning\u00FAn activo en el inventario");
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

	 if (encontrado==0 && id != 1){
		document.form.serial.value=""; 
		alert("El serial del activo ingresado no est\u00E1 previamente asignado");
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
			alert("El serial de bien nacional ingresado no est\u00E1 previamente asignado");
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
<form action="reasignar_activos_Accion.php" name="form" id="form"
	method="post"><input type="hidden" value="0" name="hid_validar" />
<table width="700" align="center" background="../../imagenes/fondo_tabla.gif"
	class="tablaalertas" id="sol_via">
	<tr>
		<td height="15" colspan="2" valign="midden" class="td_gray"><span
			class="normalNegroNegrita"> Re-asignar activos </span></td>
	</tr>
	<tr bgcolor="#F0F0F0" class="normalNegrita">
		<td colspan="2">Datos generales</td>
	</tr>
	<tr>
		<td class="normal"><strong>Tipo</strong></td>
		<td>
		<select class="normalNegro" name="tipo_r" id="tipo_r" > <!-- onchange="javascript:activar_campo()" -->
		<option value="0">[Seleccione]</option>
		<option value="1">Comodato</option>
		<option value="2">Re-asignar</option>
		<option value="3">Retornar Inventario</option>
		</select>
		</td>
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
	<tr>
		<td class="normal"><strong>Destino</strong></td>
		<td><select class="normalNegro" name="ubicacion" id="ubicacion" onchange="javascript:activar_campo()"> 
		<option value="0">[Seleccione]</option>
		<?php 
	    $sql_p="Select * from sai_bien_ubicacion where
			esta_id=1  order by bubica_nombre";
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
		<td><input type="text" name="infocentro" id="infocentro" class="normalNegro" size="70" onChange="validarRif(this)" autocomplete="off">
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
		<div align="left" class="normal"><strong>Detalle destino/Observaciones: <strong></strong>
		
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
		<td colspan="2" align="center"><input type="button" value="Re-asignar" onclick="javascript:revisar();"  class="normalNegro"></input></td>
	</tr>
	<input type="hidden" name="txt_arreglo_activos" id="txt_arreglo_activos" />
	<input type="hidden" name="txt_arreglo_mobiliario" id="txt_arreglo_mobiliario" />
	</form>

</body>
</html>
