<?
ob_start();
	 require_once("../../includes/conexion.php");
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

var bien_arreglo = new Array();
var ubica_arreglo = new Array();
var marca_arreglo = new Array();
var listado_bienes = new Array();
var listado_pcta = new Array();
var num_select;

</script>
<?
$i=0;
$sql_p="
		Select 
			*
		from 
			sai_item t1, sai_item_bien t2
		where
			esta_id=1 and id_tipo=2 and t1.id=t2.id
		Order by
			nombre
		"; 
$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
while($row=pg_fetch_array($resultado_set_most_p)) 
   {
	$bien_id = $row['id'];
	$bien_nombre = $row['nombre'];
  	$bien_descripcion = $row['descripcion'];
   	echo("
				<script language='javascript'>
				var registro = new Array(); 
				registro[0]='$bien_id';
				registro[1]='$bien_nombre';
				registro[2]='$bien_descripcion';
				bien_arreglo[$i]=registro;
				</script>
				");	
				$i++;
   }

$i=0;
$sql_p="
		Select 
			*
		from 
			sai_bien_marca
		where
			esta_id=1 order by bmarc_nombre

		";
$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
while($row=pg_fetch_array($resultado_set_most_p)) 
   {
	$bmarc_id = $row['bmarc_id'];
  	$bmarc_nombre = $row['bmarc_nombre'];
  	$bmarc_descripcion = $row['bmarc_descripcion'];
   	echo("
				<script language='javascript'>
				var registro = new Array(); 
				registro[0]='$bmarc_id';
				registro[1]='$bmarc_nombre';
				registro[2]='$bmarc_descripcion';
				marca_arreglo[$i]=registro;
				</script>
				");
				$i++;
   }


$i=0;
$sql_p="
		Select 
			*
		from 
			sai_bien_ubicacion
		where
			esta_id=1  and bubica_id<3 order by bubica_id

		";
$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
while($row=pg_fetch_array($resultado_set_most_p)) 
   {
	$bubica_id = $row['bubica_id'];
  	$bubica_nombre = $row['bubica_nombre'];
  	$bubica_descripcion = $row['bubica_descripcion'];
   	echo("
				<script language='javascript'>
				var registro = new Array(); 
				registro[0]='$bubica_id';
				registro[1]='$bubica_nombre';
				registro[2]='$bubica_descripcion';
				ubica_arreglo[$i]=registro;
				</script>
				");
				$i++;
   }
	
  
 $a_o_actual="pcta-%".substr($_SESSION['an_o_presupuesto'],2,2);
 $a_o="pcta-%11";
$i=0;
$sql_p="
		Select docg_id,depe_id,cast(substr(docg_id,6) as integer)
		from sai_doc_genera,sai_pcuenta where docg_id=pcta_id and (docg_id like '".$a_o."' or docg_id like '".$a_o_actual."')and wfob_id_ini=99 and pcta_asunto='012' order by 3";
$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
while($row=pg_fetch_array($resultado_set_most_p)) 
   {
	$pcta_id = $row['docg_id'];
  	$ubica_id =$row['depe_id'];
   	echo("
				<script language='javascript'>
				var registro = new Array(); 
				registro[0]='$pcta_id';
				registro[1]='$ubica_id';
				listado_pcta[$i]=registro;
				</script>
				");//registro[1]='$bubica_nombre';
				$i++;
   }	
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<script language="JavaScript" src="../../js/funciones.js"></script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet" />
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
	g_Calendar.setDateFormat('dd/mm/yyyy');
</script>
	
<script language="javascript">
var etiqueta1=0;
var etiqueta2=0;
var pri=0;

function llenar_bien(id)
{
	var bien_id='txt_cod_bien';
	bien=document.getElementById(bien_id);
	for(i=0;i<bien_arreglo.length;i++)
	{
		var crear_opcion=document.createElement('option');
		crear_opcion.text=bien_arreglo[i][1];
		crear_opcion.value=bien_arreglo[i][0];
		var vieja_opcion = bien.options[bien.selectedIndex];
		try {
		  bien.add(crear_opcion, null); // standards compliant; doesn't work in IE
		}
		catch(ex) {
		  bien.add(crear_opcion); // IE only
		}
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
	 cadena=document.form1.txt_identifi.value;
 else
	 cadena=document.form1.txt_identifi2.value;

 if ((texto=='fin') && (cadena!=0))
	document.form1.txt_cantidad.readOnly=true;
 else
	document.form1.txt_cantidad.readOnly=false;
 
 if (pri==0){
	 etiqueta1=cadena;
     pri=1;}
 else
	 etiqueta2=cadena;
 
 for (tamano= cadena.length; tamano<7; tamano++)
 { 
  cadena = "0"+cadena;
 }
 if (texto=='inicio')
 	document.form1.txt_identifi.value=cadena;
 else
	document.form1.txt_identifi2.value=cadena;

	
}

function acceptIntt(evt){	
	// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57	
	var nav4 = window.Event ? true : false;
	
	var key = nav4 ? evt.which : evt.keyCode;	

	return (key <= 13 || (key >= 48 && key <= 57));

}


function llenar_marca(id)
{
	var marca_id = 'cmb_marca';
	marca = document.getElementById(marca_id); 
	
	for(i=0;i<marca_arreglo.length;i++)
	{
		var crear_opcion=document.createElement('option');
		crear_opcion.text=marca_arreglo[i][1];
		crear_opcion.value=marca_arreglo[i][0];
		var vieja_opcion = marca.options[marca.selectedIndex];
		try {
		    marca.add(crear_opcion, null); // standards compliant; doesn't work in IE
		}
		catch(ex) {
			  marca.add(crear_opcion); // IE only
		 	  }
		
	}
}


function borrar_select()
{
   var m=document.getElementById('txt_pcta');  
   var num=document.form1.txt_pcta.options.length;

   if (num>1){

   var contador=1;
     while(contador<num_select){
	document.form1.txt_pcta.options[1] = null;
	contador++;
     }

   }
   llenar_pcta();
}




function llenar_pcta(id)
{
	var info_id = 'txt_pcta';
	jefatura = document.getElementById(info_id); 
	var valor=document.form1.opt_depe.value;

	for(i=0;i<listado_pcta.length;i++)
	{
		var crear_opcion=document.createElement('option');
		crear_opcion.text=listado_pcta[i][0];
		crear_opcion.value=listado_pcta[i][0];
		var depe_pcta=listado_pcta[i][1];
	
		if (depe_pcta==valor){
		try {
		    jefatura.add(crear_opcion, null); // standards compliant; doesn't work in IE
		}
		catch(ex) {
			  jefatura.add(crear_opcion); // IE only
		 	  }
		}
	   }
	num_select=document.form1.txt_pcta.options.length;
}

function llenar_ubica(id)
{
	var ubica_id = 'txt_ubica';
	ubica = document.getElementById(ubica_id); 
	
	for (i=0;i<ubica_arreglo.length;i++) 
	{
		var crear_opcion=document.createElement('option');
		crear_opcion.text=ubica_arreglo[i][1];
		crear_opcion.value=ubica_arreglo[i][0];
		var vieja_opcion = ubica.options[ubica.selectedIndex];
		try {
		    ubica.add(crear_opcion, null); // standards compliant; doesn't work in IE
		}
		catch(ex) {
			  ubica.add(crear_opcion); // IE only
		 	  }
		
	}
}

function revisar()
{
		campo_name='cmb_tipo_inc';
		campo=document.getElementById(campo_name);
		if(campo.value=="")
		{
			alert("Debe seleccionar el tipo de incorporaci\u00F3n");
			campo.focus();
			return
		}

		campo_name='opt_depe';
		campo=document.getElementById(campo_name);
		if(campo.value=="")
		{
			alert("Debe seleccionar la dependencia solicitante");
			campo.focus();
			return
		}

		campo_name='nombre';
		campo=document.getElementById(campo_name);
		if(trim(campo.value)=="")
		{
			alert("Debe indicar el proveedor");
			campo.focus();
			return
		}

		/*campo_name='txt_pcta';
		campo=document.getElementById(campo_name);
		if(campo.value=="")
		{
			document.form1.txt_pcta.value=0;
			alert("Debe indicar el n\u00FAmero del punto de cuenta asociado");
			campo.focus();
			return
		}*/

	if (listado_bienes.length==0)
	{
		window.alert("No se ha agregado ning\u00FAn activo");
		return
	}
		
	document.form1.txt_arreglo_bienes_head.value="";
	
	for(i=0;i<listado_bienes.length;i++)
	{
		for (j=0; j<13; j++)
		{
			document.form1.txt_arreglo_bienes_head.value+=listado_bienes[i][j];
		   	
			if  ( (i<(listado_bienes.length-1)  ) ||  (i==(listado_bienes.length-1) && (j!=12)))
			{
			document.form1.txt_arreglo_bienes_head.value+="�";
			}
		}	
			
	}


	
	if(confirm("\u00BFEst\u00E1 seguro que desea generar esta incorporacion de activos ?"))
	{
		document.form1.action="incorporar_activosAccion.php";
		document.form1.submit()
	}
}
var titulo=1;
function add_bienes(id,tipo)
{
   var tbody_bien = document.getElementById('body_bienes');
   var tbody_bien2 = document.getElementById('listado_bienes');

   
   
		for(i=0;i<listado_bienes.length;i++)
		{
		 tbody_bien.deleteRow(1);
		}
   if(id==0)
   {
	  if(document.form1.txt_cod_bien.value=="")
	  {
		alert("Debe indicar el nombre del activo");
		document.form1.txt_cod_bien.focus();
		return
	  }

		if(document.form1.cmb_marca.value=="")
		{
			alert("Debe seleccionar la marca del activo");
			document.form1.cmb_marca.focus();
			return
		}

		if(document.form1.txt_modelo.value=="")
		{
			alert("Debe indicar el modelo del activo, si no aplica indique N/A");
			document.form1.txt_modelo.focus();
			return
		}

		if(document.form1.txt_serial.value=="")
		{
			alert("Debe indicar el serial del activo, si no aplica indique N/A");
			document.form1.txt_serial.focus();
			return
		}

		if(document.form1.txt_bien_nacional.value=="")
		{
			alert("Debe indicar el Bien Nacional del activo");
			document.form1.txt_bien_nacional.focus();
			return
		}		
		
		if(document.form1.fecha_ing.value=="")
		{
	  		alert("Debe seleccionar la fecha de ingreso del activo");
	  		document.form1.fecha_ing.focus();
	  		return;
    		}

		if(document.form1.txt_garantia.value=="")
		{
	  		alert("Debe indicar la garant\u00EDa del activo, si no aplica indique 0");
	  		document.form1.txt_garantia.focus();
	  		return;
    		}

		if ((document.form1.periodo.value=="") && (document.form1.txt_garantia.value!=0))
		{
	  		alert("Debe indicar la garant\u00EDa del activo");
	  		document.form1.periodo.focus();
	  		return;
    		}

		if(document.form1.txt_ubica.value=="")
		{
			alert("Debe seleccionar la ubicaci\u00F3n del activo");
			document.form1.txt_ubica.focus();
			return
		}
		


		  
		for (i=0;i<bien_arreglo.length;i++)
		{
			if(bien_arreglo[i][0]==document.form1.txt_cod_bien.value)
			{	
				document.form1.bien_nombre.value=bien_arreglo[i][1];
				break
			}
		}

		for (i=0;i<marca_arreglo.length;i++)
		{
			if(marca_arreglo[i][0]==document.form1.cmb_marca.value)
			{	
				document.form1.marca_nombre.value=marca_arreglo[i][1];
				break
			}
		}

		for (i=0;i<ubica_arreglo.length;i++)
		{
			if(ubica_arreglo[i][0]==document.form1.txt_ubica.value)
			{	
				document.form1.ubica_nombre.value=ubica_arreglo[i][1];
				break
			}
		}
	    
		var cadena_serial_nacional=document.form1.txt_bien_nacional.value.split('\n');
		var long_serial_nacional=cadena_serial_nacional.length;
		
		var cadena_serial=document.form1.txt_serial.value.split('\n');
		var long_serial=cadena_serial.length;

		
		if (long_serial!=long_serial_nacional){
			  alert("Debe indicar la misma cantidad de 'seriales de art\u00EDculos' y 'seriales del activo' que concuerde con la cantidad de activos a registrar");
				document.form1.txt_serial.focus();
				return
			  }
			cantidad_activos=long_serial_nacional;
		for(j=0; j<cantidad_activos; j++){
			if (cadena_serial_nacional[j]!=''){
			var registro_bien = new Array(13);
			registro_bien[0] = document.form1.txt_cod_bien.value;
			registro_bien[1] = document.form1.bien_nombre.value;
			registro_bien[2] = cadena_serial_nacional[j];
			registro_bien[3] = document.form1.cmb_marca.value;
			registro_bien[4] = document.form1.marca_nombre.value;
			registro_bien[5] = document.form1.txt_modelo.value;
 		 	registro_bien[6] = cadena_serial[j];//arreglo_serial[j];
			registro_bien[7] = document.form1.txt_valor.value;
			registro_bien[8] = document.form1.txt_ubica.value;//9
			registro_bien[9] = document.form1.ubica_nombre.value;//10
			registro_bien[10] = document.form1.fecha_ing.value;//11
			registro_bien[11] = document.form1.txt_garantia.value;//12
			registro_bien[12] = document.form1.txt_observac.value;//13
			listado_bienes[listado_bienes.length]=registro_bien;
	
			}
		}
		
	
		if (titulo==1){

		 var row = document.createElement("tr")
		
		var td1 = document.createElement("td")
		td1.setAttribute("align","Center");		
		td1.setAttribute("class","td_gray");		
		td1.setAttribute("size","20");	
		td1.appendChild (document.createTextNode("Nombre del bien"))
		//Identificación
		var td2 = document.createElement("td")
		td2.setAttribute("align","Center");
		td2.setAttribute("class","td_gray");		
		td2.appendChild (document.createTextNode("Serial Bien Nacional"))

		//Nombre de la marca
		var td3 = document.createElement("td")
		td3.setAttribute("align","Center");
		td3.setAttribute("class","td_gray");		
		td3.appendChild (document.createTextNode("Marca"))
		//Modelo
		var td4 = document.createElement("td")
		td4.setAttribute("align","Center");
		td4.setAttribute("class","td_gray");		
		td4.appendChild (document.createTextNode("Modelo"))
		//Serial
		var td5 = document.createElement("td")
		td5.setAttribute("align","Center");
		td5.setAttribute("class","td_gray");		
		td5.appendChild (document.createTextNode("Serial activo"))
		//Valor Unitario
		var td6 = document.createElement("td")
		td6.setAttribute("align","Center");
		td6.setAttribute("class","td_gray");		
		td6.appendChild (document.createTextNode("Valor unitario"))

		//Fecha Ingreso
		var td9 = document.createElement("td")
		td9.setAttribute("align","Center");
		td9.setAttribute("class","td_gray");		
		td9.appendChild (document.createTextNode("Fecha ingreso"))
		//Garantía
		var td10 = document.createElement("td")
		td10.setAttribute("align","Center");
		td10.setAttribute("class","td_gray");		
		td10.appendChild (document.createTextNode("Garant\u00EDa"))

		//Ubicación
		var td8 = document.createElement("td")
		td8.setAttribute("align","Center");
		td8.setAttribute("class","td_gray");		
		td8.appendChild (document.createTextNode("Ubicaci\u00F3n"))
		//Observaciones
		var td11 = document.createElement("td")
		td11.setAttribute("align","Center");
		td11.setAttribute("class","td_gray");		
		td11.appendChild (document.createTextNode("Observaciones"))
  
		row.appendChild(td1);
		row.appendChild(td2);
		row.appendChild(td3);
		row.appendChild(td4);
		row.appendChild(td5);
		row.appendChild(td6);
		row.appendChild(td9);
		row.appendChild(td10);
		row.appendChild(td8);
		row.appendChild(td11);
		
	 	tbody_bien.appendChild(row);

		titulo=0;
		}
       }//id=0
       

		if(id!=0)
		{
		// tbody_bien.deleteRow(0);//2
		 for(i=tipo;i<=listado_bienes.length;i++)
		 {
			listado_bienes[i-1]=listado_bienes[i];
		 }
		 listado_bienes.pop();
		}
		
		for(i=0;i<listado_bienes.length;i++)
		{
		
			var row = document.createElement("tr")
			if((i%2)==0)
				row.className = "reci2"
			else
				row.className = "reci"

	
		//Nombre del Bien
		var td1 = document.createElement("td")
		td1.setAttribute("align","Center");		
		td1.appendChild (document.createTextNode(listado_bienes[i][1]))
		//Identificación
		var td2 = document.createElement("td")
		td2.setAttribute("align","Center");
		td2.appendChild (document.createTextNode(listado_bienes[i][2]))
		//Nombre de la marca
		var td3 = document.createElement("td")
		td3.setAttribute("align","Center");
		td3.appendChild (document.createTextNode(listado_bienes[i][4]))
		//Modelo
		var td4 = document.createElement("td")
		td4.setAttribute("align","Center");
		td4.appendChild (document.createTextNode(listado_bienes[i][5]))
		//Serial
		var td5 = document.createElement("td")
		td5.setAttribute("align","Center");
		td5.appendChild (document.createTextNode(listado_bienes[i][6]))
		//Valor Unitario
		var td6 = document.createElement("td")
		td6.setAttribute("align","Right");
		td6.appendChild (document.createTextNode(listado_bienes[i][7]))
		//Fecha Ingreso
		var td8 = document.createElement("td")
		td8.setAttribute("align","Center");
		td8.appendChild (document.createTextNode(listado_bienes[i][10]))
	
	  	//Garantía
		var td9 = document.createElement("td")
		td9.setAttribute("align","Center");
		td9.appendChild (document.createTextNode(listado_bienes[i][11]))
		
		//Ubicación
		if (listado_bienes[i][8]==1)
		 nombre_ubicacion="Torre";
		else
		 nombre_ubicacion="Galp\u00F3n";
		var td7 = document.createElement("td")
		td7.setAttribute("align","Center");
		td7.appendChild (document.createTextNode(nombre_ubicacion))
		//Observaciones
		var td10 = document.createElement("td")
		td10.setAttribute("align","Center");
		td10.appendChild (document.createTextNode(listado_bienes[i][12]))
		/*
		var td11 = document.createElement("td")
		td11.setAttribute("align","Center");
		td11.appendChild (document.createTextNode(listado_bienes[i][13]))*/

		var td12 = document.createElement("td")
		td12.setAttribute("align","Center");
		td12.className = 'normal';
		editLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		editLink.setAttribute("href", "javascript:add_bienes('"+1+"','"+(i+1)+"');");
		editLink.appendChild(linkText);  
		td12.appendChild (editLink)
		
		
		row.appendChild(td1);
		row.appendChild(td2);
		row.appendChild(td3);
		row.appendChild(td4);
		row.appendChild(td5);
		row.appendChild(td6);
		row.appendChild(td8);//fecha
		row.appendChild(td9);
		row.appendChild(td7);
		row.appendChild(td10);
		row.appendChild(td12);
	 	tbody_bien.appendChild(row);
	 
	 document.form1.txt_cod_bien.value="";
	 document.form1.cmb_marca.value="";
	// document.form1.txt_ubica.value="";
	 document.form1.txt_cantidad.value="";
	 document.form1.txt_modelo.value=""; 
	 document.form1.txt_serial.value="";
	 document.form1.txt_valor.value="0.00";
	 document.form1.fecha_ing.value=""; 
	 document.form1.txt_garantia.value=""; 
	 document.form1.txt_observac.value="";
	 document.form1.periodo.value="";
	 document.form1.txt_bien_nacional.value="";
	}	
	 document.form1.txt_cod_bien.value="";
	 document.form1.cmb_marca.value="";
	 //document.form1.txt_ubica.value="";
	 document.form1.txt_cantidad.value="";
	 document.form1.txt_modelo.value=""; 
	 document.form1.txt_serial.value="";
	 document.form1.txt_valor.value="0.00";
	 document.form1.fecha_ing.value=""; 
     document.form1.txt_garantia.value=""; 
	 document.form1.txt_observac.value="";  
	 document.form1.periodo.value="";
	 document.form1.txt_bien_nacional.value="";
}

function del_bienes()
{
	var contador=parseInt(document.form1.txt_total_cant.value);
	if(contador>1)
	{
		var tbody_tabla=document.getElementById('tabla_bienes');
		tbody_tabla.deleteRow(contador+1);
		document.form1.txt_total_cant.value=contador-1;
		total();
	}
	else
	{
		alert("Error al eliminar: La incorporacion debe tener al menos un activo");
	}
	
}

function total()
{
	val_total=0;
	for(i=1;i<=document.form1.txt_total_cant.value;i++)
	{
		valor_name='txt_valor';
		valor=document.getElementById(valor_name);
		val_total=val_total+parseFloat(MoneyToNumber(valor.value));
		
	}
	document.form1.txt_total.value=val_total;
	FormatCurrency(document.form1.txt_total);
}


//-->

</script>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body onLoad="llenar_bien('1');llenar_marca('1'); llenar_ubica('1');">
<form action="" name="form1" id="form1" method="post">
  <table width="90%"  align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" id="sol_via">
    <tr>
      <td height="15" colspan="4" valign="midden" class="td_gray"> <span class="normalNegroNegrita"> Registrar activos </span> </td>
    </tr>
         <tr bgcolor="#F0F0F0" class="normalNegrita">
      <td colspan="4">Datos generales</td></tr>
     <tr>
	<tr>
	  <td class="normal"><b>Tipo de incorporaci&oacute;n:</b></td>
      <td>
        <select name="cmb_tipo_inc" class="normalNegro" id="cmb_tipo_inc">
         <option value="" selected>Seleccione...</option>
         <?
		  $sql="SELECT binc_id,binc_nombre
				FROM sai_binc_tipo
				WHERE esta_id=1 ";
					
		  $resultado=pg_exec($conexion,$sql);
		  while($row=pg_fetch_array($resultado))
		  {
			$binc_id=$row['binc_id'];
			$binc_nombre=$row['binc_nombre'];
			echo("<option value='$binc_id'>$binc_nombre</option>");
		  }
		 ?>
         </select>
         <font  class="normal">*</font></td>
	 <td> <div  class="normal"><strong>Dependencia solicitante</strong></div></td>
	 <td>
      <select name="opt_depe" class="normalNegro" id="opt_depe" onchange="javascript:borrar_select()" >
	     <option value="">[Seleccione]</option>
		 <?php
	       $sql_str="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_id,depe_nombre','depe_nivel=4','depe_nombre',1) resultado_set (depe_id varchar, depe_nombre varchar)";	
	       $res_q=pg_exec($sql_str) or die("Error al mostrar");	  
	       $i=0;
	       while($depe_row=pg_fetch_array($res_q)){ 
 		    $depe_id=$depe_row['depe_id'];
		    $depe_nombre=$depe_row['depe_nombre'];
			?>
            <option value="<?php echo(trim($depe_row['depe_id'])); ?>"><?php echo(trim($depe_row['depe_nombre'])); ?></option>
            <?php }?>
       </select>
       <font  class="normal">*</font>	<input name="depe_nombre" type="hidden" id="depe_nombre" /></td>
   </tr>
   <tr>
  <tr><TD height="10" colspan="4"></TD></tr>
    <tr>
          <td class="normal"><b>Proveedor:</b></td>
          <td>
          <input class="normalNegro" autocomplete="off" size="30" type="text" id="nombre" name="nombre" value="<?= $nombre?>" <?php if($rif!="" || $codigo!=""){echo "disabled='disabled'";}?> /></td>
		  <?php
			$query = "SELECT prov_nombre FROM sai_proveedor_nuevo ORDER BY prov_nombre";
			$resultado = pg_exec($conexion, $query);
			$numeroFilas = pg_num_rows($resultado);
			$arreglo = "";
			while($row=pg_fetch_array($resultado)){
			 $arreglo .= "'".$row["prov_nombre"]."',";	
			}
			 $arreglo = substr($arreglo, 0, -1);
			?>
			<script>
				var proveedores = new Array(<?= $arreglo?>);
				actb(document.getElementById('nombre'),proveedores);
			</script>
			  <td class="normal"> <b> Ubicaci&oacute;n:</b></td>
	             <td><select name="txt_ubica" id="txt_ubica" class="normalNegro">
			<option value="" selected>Seleccione...</option>
			</select>
            <font class="normal">*</font></td>
         </tr>
         <tr><TD height="10" colspan="4"></TD></tr>
	<tr>
	  <td class="normal" width="75"><b>N&deg; licitaci&oacute;n/ N&deg; orden compra: </b></td>
	  <td><input type="text" name="num_doc" id="num_doc" size="20" class="normalNegro" maxlength="20"></td>
	 <td><div align="left" class="normal"><strong>N&deg; punto de cuenta: </strong></div></td>
<td><select name="txt_pcta" id="txt_pcta" class="normalNegro">
  <option value="N/A">[Seleccione]</option>
</select>
<div align="right" class="normal"><font size="-4">* Campos obligatorios</font></div></td>
  </tr>
     <tr bgcolor="#F0F0F0" class="normalNegrita">
      <td colspan="4">DATOS ESPECIFICOS</td></tr>
     <tr>
      <td colspan="4">
        <table width="100%" align="center" id="tabla_bienes" background="/imagenes/fondo_tabla.gif">
          <tr>
            <td><div class="normal"><strong> Nombre del activo</strong></div></td>
            <td><div class="normal"><strong> Marca</strong></div></td>
            <td><div class="normal"><strong> Modelo </strong></div></td>
           <td><div align="center" class="normal"><strong> Valor unitario </strong></div></td>
	     </tr>
	      <tr>
             <td width="42%"><div class="normal">
                <select name="txt_cod_bien" id="txt_cod_bien" class="normalNegro">
			<option value="" selected>Seleccione...</option> 
		</select>
         </div></td>
            <td width="19%">
              <div  class="normal">
                <select name="cmb_marca" id="cmb_marca" class="normalNegro" onChange="javascript: llenar_marca(this)">
			<option value="" selected>Seleccione...</option> 
		</select>
             </div>  <input name="txt_cantidad" type="hidden" class="normalNegro" id="txt_cantidad" onKeyPress="return acceptIntt(event)" size="5"></td>
             
            <td width="25%">
              <div align="center" class="normal">
		<input name="txt_modelo" class="normalNegro" id="txt_modelo" size="15"> 
            </div></td>
             <td width="14%">
           <div align="center" class="normal">
             <input name="txt_valor" type="text" class="normalNegro" id="txt_valor" size="10" value="0.0" onKeyPress="return acceptFloat(event)">
            </div></td>

           </tr>
	 <tr><TD height="10"></TD></tr>
	  <tr>
	  <td><div align="left" class="normal"><strong> Serial activo</strong></div></td>
	<td><div class="normal"><strong> Serial Bien Nacional  </strong></div></td>
	  <td colspan="2"><div class="normal"><strong> Fecha entrada </strong></div></td>
	  
	  <td></td>
	   </tr>
	  <tr>
	       <td width="100">
          <div align="left" class="normal">
               <textarea name="txt_serial" id="txt_serial" class="normalNegro" cols="40" rows="3"></textarea></div>	</td>
            </div></td>
                        <td  width="70">
              <div  class="normal"><!-- txt_identifi   txt_identifi2-->
		      <textarea name="txt_bien_nacional" id="txt_bien_nacional" class="normalNegro" cols="10" rows="3"></textarea>
            </div></td>

            <td  width="70" colspan="2">  
      		 <input type="text" size="10" id="fecha_ing" name="fecha_ing" value="" class="normalNegro" readonly/>
      		 <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha_ing');" title="Show popup calendar">
	   <img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
	   </a>
			</td>

        </tr>
<tr> <td height="10"> <div align="center" class="normal"> </div></td></tr>
	  <tr>
	   <td><div class="normal"><strong> Observaciones </strong></div></td>
	   <td><div class="normal"><strong> Garant&iacute;a </strong></div></td>
	   </tr>	
	  <tr>
	                 <td>
              <div class="normal">
                <input name="txt_observac" type="text" class="normalNegro" id="txt_observac" size="40" value=" ">
            </div>
  		<input name="bien_nombre" type="hidden" id="bien_nombre" />
		<input name="marca_nombre" type="hidden" id="marca_nombre" />		
		<input name="ubica_nombre" type="hidden" id="ubica_nombre" />	

	</td>
    <td>
              <div class="normal">
                <input name="txt_garantia" type="text" class="normalNegro" id="txt_garantia" size="2" value=""  onKeyPress="return acceptIntt(event)">
                <select name="periodo" id="periodo" class="normalNegro" >
		<option value="Mes(es)">Mes(es)</option>  
		</select>
            </div></td>
      <td colspan="2">
      	<div align="left"> 
        	<a href="javascript: add_bienes(0,0); " class="normal">
				Agregar activo    
			</a>
			<!--<a href="javascript: del_bienes(); " class="link">
				Eliminar un Bien    
			</a>-->
			<input name="cmb_orde" type="hidden" class="normal" id="cmb_orde" value="N/P">
      	</div>
	  </td>
</tr>
       
  

      </table></td>
    </tr>
<tr><TD align="center" colspan="10">
<table width="100%" align="center" id="listado_bienes" background="/imagenes/fondo_tabla.gif">
<div align="center">
  <tbody id="body_bienes" class="normal">
		  
  </tbody>
<input  type="hidden" name="txt_arreglo_bienes_head" id="txt_arreglo_bienes_head" />
</div>
</table></TD></tr>
	<tr>
		<td colspan="4">
			<div align="center">
			  <input type="button" value="guardar" onclick="javascript:revisar()" class="normalNegro">
				
			</div>		
		</td>
	</tr>

  </table>

</form>
</body>
</html>
