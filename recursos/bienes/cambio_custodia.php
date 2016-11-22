<?php 
  ob_start();
  session_start();
  require_once("../../includes/conexion.php");
  require("../../includes/perfiles/constantesPerfiles.php");
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../index.php',false);
   ob_end_flush(); 
   exit;
  }
  ob_end_flush(); 
 ?>
	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::Disminuci&oacute;n del Inventario.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>

<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');

</script>
<script language="JavaScript" type="text/JavaScript">

var factura = new Array();
var factura_head = new Array();
var dependencia = new Array();
var ori = new Array();
var listado_mobiliario = new Array();
var listado_disponibilidad = new Array();
var listado_activos = new Array();
</script>
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
					
								
		$i=0;
		$sql_e="select etiqueta,serial from sai_biin_items where esta_id=41 order by etiqueta";
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
<script language="JavaScript" type="text/JavaScript">

function validar_digito(objeto)
{
	var checkOK = "ABCDEFGHIJKLMN\u00D1OPQRSTUVWXYZabcdefghijklmn\u00F1opqrstuvwxyz0123456789 -_.,;()*{}[]/+";
	var checkStr = objeto.value;
	var allValid = true;
	for (i = 0;  i < checkStr.length;  i++)
	{
		ch = checkStr.charAt(i);
		for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
		if (j == checkOK.length)
		{
			var cambio=checkStr.substring(-1,i) 
			objeto.value=cambio;
			alert("Estos caracteres no est\u00E1n permitidos");
			break;
		}
	}
}

function validar_disponibilidad(){

	 document.form.disponibilidad.value="";
	// var posicion=document.form.material.value.indexOf(':');
	 var articulo=trim(document.form.articulo.value);

	 for(i=0;i<listado_disponibilidad.length;i++)
	 {
	   var id_arti = listado_disponibilidad[i][0];
	   if (articulo==id_arti){
	    document.form.disponibilidad.value=listado_disponibilidad[i][1];
	   }
	 }

	}

function add_factura_head(id,tipo)
{ 	var disponible=0;
	var iva_total =0;
	var factura_new="";

	if(tipo==0)
	{
	
	  	if(document.form.articulo.value=='0')
		{
			window.alert("Debe seleccionar al menos un material");
			document.form.articulo.focus();
			document.form.articulo.select();
			return
		}
		
		if ((document.form.cantidad.value=="") || (document.form.cantidad.value<='0'))
		{
			window.alert("Debe especificar la cantidad del material");
			document.form.cantidad.focus();
			document.form.cantidad.select();
			return
		}

		  if (parseInt(document.form.cantidad.value)>parseInt(document.form.disponibilidad.value)){
			    document.form.cantidad.value="";
			    alert("La cantidad a ser entregada no puede ser mayor a la disponible");
			    return;
			  }
		
		codi=document.form.articulo.value;
		for(i=0;i<factura_head.length;i++)
	      {
		   	if (factura_head[i][0]==codi)
		      {
			    alert("Este material ya ha sido ingresado");
			    return;
	          }
	      }
	
	    for (i=0;i<ori.length;i++)
		{
			if(ori[i][0]==document.form.articulo.value)
			{	
				document.form.arti_nombre.value=ori[i][1];
		
			}
		}


	    for (i=0;i<dependencia.length;i++)
		{
			if(dependencia[i][0]==document.form.opt_depe.value)
			{	
				document.form.depe_nombre.value=dependencia[i][1];
				break
			}
		}
		
		var registro_factura_head = new Array(3);
		registro_factura_head[0] = document.form.articulo.value;
		registro_factura_head[1] = document.form.arti_nombre.value;
		registro_factura_head[2] = document.form.cantidad.value;
		factura_head[factura_head.length]=registro_factura_head;
	}
	
	var tbody_factura = document.getElementById('body_factura_head');
	var tbody_factura2 = document.getElementById(id);
	
	for(i=0;i<factura_head.length-1;i++)
	{
		tbody_factura2.deleteRow(3);
	}
	
	if(tipo!=0)
	{
		tbody_factura2.deleteRow(3);
		for(i=tipo;i<factura_head.length;i++)
		{
			factura_head[i-1]=factura_head[i];
		}
		factura_head.pop();
	}

	var factura_actual= document.form.articulo.value;
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
		td3.className = 'normalNegro';
		td3.appendChild (document.createTextNode(factura_head[i][1]))

		//Cantidad
		var td4 = document.createElement("td")
		td4.setAttribute("align","Center");
		td4.className = 'normalNegro';
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
	    document.form.articulo.value='0';
	    document.form.cantidad.value="";

	}	
	    document.form.articulo.value='0';
        document.form.cantidad.value="";
	 
}


function no_coma(evt){ /*NO ACEPTA LA COMA EN EL TEXT_BOX*/	
		
	var nav4 = window.Event ? true : false;
	
	var key = nav4 ? evt.which : evt.keyCode;	

	return (key != 44);
}

function revisar()
{

	if ((factura_head.length==0) && (listado_mobiliario.length==0))
	{
		window.alert("Debe seleccionar al menos un material y/o activo para cambiar de custodia");
		return
	}


	document.form.txt_arreglo_factura_head.value="";
	
	for(i=0;i<factura_head.length;i++)
	{
		for (j=0; j<3; j++)//4
		{
			document.form.txt_arreglo_factura_head.value+=factura_head[i][j];
		   	
			if  ( (i<(factura_head.length-1)  ) ||  (i==(factura_head.length-1) &&  (j!=2) ) )
			{
			document.form.txt_arreglo_factura_head.value+="�";
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


function leer_codigo(id,tipo){	
	    var cadena=document.form.etiqueta.value;

	    var encontrado=0;	 

		 for(i=0;i<listado_activos.length;i++)
		 {
		   var sbn = listado_activos[i][0];
		   if (cadena==sbn){
			   encontrado=1;  
		   }
		 }

		 if (encontrado==0){
			document.form.etiqueta.value=""; 
			alert("El serial de bien nacional o serial del activo ingresado no est\u00E1 disponible para su asignaci\u00F3n");
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


</script>
</head>
<body>
<form name="form" method="post" enctype="multipart/form-data" id="form1" action="cambio_custodia_Accion.php">

<br /><br />
<table width="700" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
  <tr class="td_gray"> 
    <td width="700" height="15" valign="midden"><span class="normalNegroNegrita">Custodia de activos y/o materiales</span></td>
  </tr>
  <tr>
    <td>
     <table width="700" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
       <tr>
       </tr>

       <tr bgcolor="#0099CC" width="100%">
         <td bgcolor="#F0F0F0"><div align="center" class="normalNegrita">Material</div></td>
         <td bgcolor="#F0F0F0"><div align="center" class="normalNegrita">Disponibilidad</div></td>
	     <td bgcolor="#F0F0F0"><div align="center" class="normalNegrita">Cantidad</div></td>
         <td bgcolor="#F0F0F0"><div align="center" class="normalNegrita">Opci&oacute;n</div></td>
       </tr>
	   <tr>
	   	 <td><div align="center">
		  <select name="articulo" id="articulo" class="normalNegro">
            <option value="0">[Seleccione]</option>
            <?php
    
    	  	  $i=0;
			  $sql_d="SELECT *
	             FROM sai_item t1,sai_item_articulo t2
	    		 WHERE t1.id=t2.id and esta_id=1 and id_tipo=1 ORDER BY nombre";
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
			   $i++;
 			?>
           <option value="<?=$arti_id?>"><?=$arti_descripcion?>: <?=$unme_id;?></option>
         <?php } ?>
        </select>
		<input name="arti_nombre" type="hidden" id="arti_nombre" /></span></div>	
		
		
		
		<!--   <input autocomplete="off" size="60" type="text" id="des_articulo" name="des_articulo" value="<?= $nombre?>"  /> --></td>
	 <?php
	   $query = "SELECT t1.id as id,nombre FROM sai_item t1,sai_item_articulo t2 WHERE t1.id=t2.id and tipo in ('1','2','3','4','6') ORDER BY nombre";
	   $resultado = pg_exec($conexion, $query);
	   $numeroFilas = pg_num_rows($resultado);
	   $arreglo = "";
	   while($row=pg_fetch_array($resultado)){
		$arreglo .= "'".$row["nombre"]."',";	
	   }
		$arreglo = substr($arreglo, 0, -1);
	 ?>
	 <script>
		var articulos = new Array(<?= $arreglo?>);
		actb(document.getElementById('des_articulo'),articulos);
	</script>

	<input type="hidden" name="txt_articulo" value="">
  	<input type="hidden" name="hid_articulo" value="">
		
		
		
		</td>
						<td>
				<div align="center" class="peq"><input name="disponibilidad" type="text"
					class="normalNegro" id="disponibilidad" size="6" maxlength="6" value="" disabled></div>
				</td>
	  <td><div align="center">
	    <input name="cantidad" type="text"  class="normalNegro" id="cantidad" onKeyUp="javascript: validar(this,0)" size="6" maxlength="6" onfocus="validar_disponibilidad()"></div>						</td>
  	  <td>
	   <div align="center" class="normal"><a href="javascript: add_factura_head('factura_head','0'); " class="normal">Agregar	</a></div>	</td>
	</tr>
		<tbody id="body_factura_head" class="normal">
		</tbody>
  </table></td>
   </tr>
   <tr>
     <td height="10" valign="midden" >	  </td>
   </tr>
<tr>
		<td colspan="2">
		<table width="700" border="0" align="center" cellpadding="1"
			cellspacing="1" class="tablaalertas" id="factura_head">
			<tr>
			</tr>
			<tr bgcolor="#F0F0F0" width="100%">
				<td colspan='3'>
				<div align="center" class="normalNegrita">Serial Bien Nacional o Serial activo</div>
				</td>
			</tr>
			<tr>
				<td height="10">
				<div align="center" class="normal"></div>
				</td>
			</tr>
			<tr>
				<td>
				<div align="center" class="normal"><strong><strong>
				<input name="etiqueta" type="text" class="normalNegro" id="etiqueta"
					size="15" onChange="leer_codigo(0)"> <!-- 	<a href="javascript: add_bienes('listado_bienes','0'); " class="normal">
				Agregar Serial    
			</a> --></div>
				</td>
			</tr>
			<tbody id="body_bienes2" class="normal" align="center">

			</tbody>



		</table>
		</td>
	</tr>

  <tr><td>&nbsp;</td></tr>
  <tr bgcolor="#0099CC" width="100%"> 
    <td bgcolor="#F0F0F0"><div align="center" class="normalNegrita">Observaciones </div></td>
  </tr>
  <tr>
    <td><div align="center"  class="normal"><textarea name="motivo" id="motivo" class="normalNegro" cols="50" rows="3" onkeyup="validar_digito(motivo)"></textarea></div>	</td></tr>
  <tr><td>	<input type="hidden" name="txt_arreglo_mobiliario" id="txt_arreglo_mobiliario" /></td></tr>

 <tr>
   <td height="16" colspan="3">
	<div align="center">
	   <input class="normalNegro" type="button" value="Registrar" onclick="javascript:revisar();"/>
	</td>
</tr>
</table>
  <input  type="hidden" name="txt_arreglo_factura_head" id="txt_arreglo_factura_head" />
</form>
</body>
</html>
<?php pg_close($conexion);?>