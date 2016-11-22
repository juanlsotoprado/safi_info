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


	if(confirm("Datos introducidos de manera correcta. Esta seguro que desea continuar?."))
  {
	 document.form.submit();
  }	
  
}

</script>
</head>
<body>
<form name="form" method="post" enctype="multipart/form-data" id="form1" action="cambio_custodia_rs_Accion.php">

<br /><br />
<table width="600" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
  <tr class="td_gray"> 
    <td width="600" height="15" valign="midden"><span class="normalNegroNegrita">Custodia de activos y/o materiales</span></td>
  </tr>
  <tr>
    <td>
     <table width="600" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
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
	    		 WHERE t1.id=t2.id and esta_id=1 and id_tipo=4 ORDER BY nombre";
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
	   $query = "SELECT t1.id as id,nombre FROM sai_item t1,sai_item_articulo t2 WHERE t1.id=t2.id and id_tipo=4 ORDER BY nombre";
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


  <tr><td>&nbsp;</td></tr>
  <tr bgcolor="#0099CC" width="100%"> 
    <td bgcolor="#F0F0F0"><div align="center" class="normalNegrita">Observaciones </div></td>
  </tr>
  <tr>
    <td><div align="center"  class="normal"><textarea name="motivo" id="motivo" class="normalNegro" cols="50" rows="3" onkeyup="validar_digito(motivo)"></textarea></div>	</td></tr>
  <tr><td>	</td></tr>

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