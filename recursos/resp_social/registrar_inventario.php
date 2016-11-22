<?php 
  ob_start();
  session_start();
  require_once("../../includes/conexion.php");
  include(dirname(__FILE__) . '/../../init.php');
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../index.php',false);
   ob_end_flush(); 
   exit;
  }
  ob_end_flush(); 
  
  
  /////////////////////////////////////////////////////////////////////////////////////////  Cadena de entrada resp social ///////////////////////
  
  //Modelos

  include(SAFI_MODELO_PATH. '/estado.php');
  require_once(SAFI_MODELO_PATH. '/dependencia.php');
  require_once(SAFI_MODELO_PATH. '/empleado.php');
  require_once(SAFI_MODELO_PATH. '/general.php');
  require_once(SAFI_MODELO_PATH. '/docgenera.php');
  require_once(SAFI_MODELO_PATH. '/estatus.php');
  require_once(SAFI_MODELO_PATH. '/observacionesDoc.php');
  require_once(SAFI_MODELO_PATH. '/wfgrupo.php');
  require_once(SAFI_MODELO_PATH. '/wfopcion.php');
  require_once(SAFI_MODELO_PATH. '/wfcadena.php');
  require_once(SAFI_MODELO_PATH. '/revisionesDoc.php');
  require_once(SAFI_MODELO_PATH. '/cargo.php');
  
  $lugar = 'ers';
  $id_cadena_inicial = SafiModeloWFCadena::GetWfca_id_inicial($lugar);
  $GLOBALS['SafiRequestVars']['idCadenaActual'] = $id_cadena_inicial;
  
  $id_HijosCadena = SafiModeloWFCadena::GetId_cadena_hijo_id_cadena($id_cadena_inicial);
  $GLOBALS['SafiRequestVars']['opciones'] = $id_HijosCadena;
  
  
  $param['lugar'] = "ers";
  $param['ano'] = substr($_SESSION['an_o_presupuesto'],2);
  $niveles = array(3,4);
  $param['Dependencia']= '';

  ///////////////////////////////////////////////////////////////////////////////////////  FIN Cadena de entrada resp social FIN ///////////////////////
  
  //array de serial
  $queryserial=
  "SELECT
   t1.id,
   t1.nombre,
   t2.unidad_medida,
   t3.modelo,
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
   GROUP BY
   t1.id,
   t1.nombre,
   t2.unidad_medida,
   t3.modelo,
   t3.serial,
   t3.ubicacion,
   t4.bmarc_nombre,
   t4.bmarc_id";
  
  $resultado = pg_exec ( $conexion, $queryserial );
  $numeroFilas = pg_num_rows ( $resultado );
  $indice = 0;
  $stringobj;
  
  while ( $row = pg_fetch_array ( $resultado ) ) {
  	// array de seriales
  
  	$stringobj [strtoupper($row ['serial'])] ['nombre'] = utf8_encode ( $row ['nombre'] );
  	$stringobj [strtoupper($row ['serial'])] ['modelo'] = utf8_encode ( $row ['modelo'] );
  	$stringobj [strtoupper($row ['serial'])] ['idmarca'] = ($row ['bmarc_id']);
  	$stringobj [strtoupper($row ['serial'])] ['marca'] = utf8_encode ( $row ['bmarc_nombre'] );
  
  	// serial
  	$stringobj [strtoupper($row ['serial'])] ['serial'] = strtoupper(utf8_encode( $row ['serial'] ));
  
  	$stringobj [strtoupper($row ['serial'])] ['id'] = $row ["id"];
  	$stringobj [strtoupper($row ['serial'])] ['unidad'] = utf8_encode ( $row ["unidad_medida"] );
  
  
  	$indice ++;
  }
  
  ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all" />
<title>SAFI::Registro del Almac&eacute;n:.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet" />
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>" charset="utf-8"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/jquery-ui-1.8.13.custom.min.js';?>" charset="utf-8"></script>
<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script language="javascript">
  
	var stringobj = <?php echo json_encode($stringobj); ?>
	
$().ready(function(){

	$("#serial").keyup(function(){
	
	$("#cantidad").attr("readonly","readonly").val(1);

	if($("#serial").val().length < 1){
	
		$("#cantidad").attr("readonly",false).val("");

	}
		
	});

	$("#cantidad").keyup(function(){
		
		if($("#cantidad").val()=='0'){

			alert("Cantidad debe ser mayor a 0");
			$("#cantidad").attr("readonly",false).val("");			
			
		}	
			
	});

});

var factura = new Array();
var factura_head = new Array();
var marcas = new Array();
var ori = new Array();
var idCadenaSigiente;
////////////funciones cadena
function GenerarSigienteCadena(valor)
{

if(valor)
{        
                                              
idCadenaSigiente = valor;

//alert(idCadenaSigiente);
revisar();  

}
                                          
}

function LlenarCadenaSigiente(){

$("#idCadenaSigiente").remove();
 var div = $("#accionesEjecutar")[0];
  var input1 = document.createElement("input");
 input1.setAttribute("type","hidden");
  input1.setAttribute("id","idCadenaSigiente");
  input1.setAttribute("name","idCadenaSigiente");
input1.value= idCadenaSigiente;
  div.appendChild(input1);
}
/////////////fin funciones cadena


function acceptFloat(evt){	
	// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57	
	var nav4 = window.Event ? true : false;
	var key = nav4 ? evt.which : evt.keyCode;	
	return (key <= 13 || (key >= 48 && key <= 57) || key == 46);
}

function add_factura_head(id,tipo)
{ 	var disponible=0;
	var iva_total =0;
	var factura_new="";
	//verificar que no agregen un serial que pertenezca a una marca y un modelo especifico
	var filtroserial = $("#serial").val().toUpperCase();
	var filtromarca = $("#marca").val();
	var filtromodelo = $("#modelo").val();
	var filtroid = $("#articulo").val();

	//var filtroproveedor = $("#proveedor").val();
	
	if(stringobj[filtroserial] 
	&& stringobj[filtroserial]['id']==filtroid 
	&& stringobj[filtroserial]['idmarca']==filtromarca 
	&& stringobj[filtroserial]['modelo']==filtromodelo)
	{
		alert("El serial ya existe");
		document.form.serial.focus();
		return;
	}

	if(!stringobj2[$("#proveedor").val()]){
		
		alert("Seleccione un proveedor v\u00e1lido");
		return
		
	}
	if(document.form.txt_ubica.value=='0')
	{
		window.alert("Debe indicar la ubicaci\u00f3n");
		document.form.txt_ubica.focus();
		return
	}
	if(document.form.monto_recibido.value=='' || document.form.monto_recibido.value=='0')
	{
		window.alert("Debe indicar el monto recibido");
		document.form.monto_recibido.focus();
		return
	}
	if (document.form.proveedor.value==""){
		   alert("Debe indicar el nombre del proveedor");
		   document.form.proveedor.focus();
		   return
	}
	if(document.form.observaciones.value=='')
	{
		window.alert("Debe indicar observaciones del registro");
		document.form.observaciones.focus();
		return
	}
	if(document.form.hid_desde_itin.value=="")
	{
  		alert("Debe seleccionar la fecha de recepci\u00F3n del art\u00EDculo");
  		document.form.hid_desde_itin.focus();
  		return;
	}
	

	if(tipo==0)
	{
		
	/*	if(document.form.txt_ubica.value=="0")
		{
			alert("Debe seleccionar la ubicaci\u00F3n del art\u00EDculo");
			document.form.txt_ubica.focus();
			return
		}*/
		
	  	if(document.form.articulo.value=='0')
		{
			window.alert("No ha seleccionado ning\u00FAn art\u00EDculo");
			document.form.articulo.focus();
			return
		}
		
		if(document.form.cantidad.value=="")
		{
			window.alert("Debe especificar la cantidad del art\u00EDculo");
			document.form.cantidad.focus();
			return
		}
		
		codi=document.form.articulo.value;
		for(i=0;i<factura_head.length;i++)
	      {
		   	if ((factura_head[i][0]==codi) && (factura_head[i][3]==document.form.marca.value)
		   		&& (factura_head[i][4]==document.form.modelo.value) && (factura_head[i][5]==$("#serial").val().toUpperCase()))
		      {
			    alert("Art\u00EDculo ya seleccionado");
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

	    for (i=0;i<marcas.length;i++)
		{
			if(marcas[i][0]==document.form.marca.value)
			{	
				document.form.marca_nombre.value=marcas[i][1];
				break
			}
		}
    	
		var registro_factura_head = new Array(8);
		registro_factura_head[0] = document.form.articulo.value;
		registro_factura_head[1] = document.form.arti_nombre.value;
		registro_factura_head[2] = document.form.cantidad.value;
		registro_factura_head[3] = document.form.marca.value;
		registro_factura_head[4] = document.form.modelo.value;
		registro_factura_head[5] = $("#serial").val().toUpperCase();
		registro_factura_head[6] = document.form.hid_desde_itin.value;
		registro_factura_head[7] = document.form.marca_nombre.value;
		factura_head[factura_head.length]=registro_factura_head;
	}

	
	
	var tbody_factura = document.getElementById('body_factura_head');
	var tbody_factura2 = document.getElementById(id);

	
	for(i=0;i<factura_head.length-1;i++)
	{
		tbody_factura2.deleteRow(2);
		
	}
	
	if(tipo!=0)
	{
		tbody_factura2.deleteRow(2);
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
		var td1 = document.createElement("td")
		td1.setAttribute("align","Center");
		td1.appendChild (document.createTextNode(factura_head[i][1]))
		//Cantidad
		var td2 = document.createElement("td")
		td2.setAttribute("align","Center");
		td2.appendChild (document.createTextNode(factura_head[i][2]))
/*		//Marca
		var td3 = document.createElement("td")
		td3.setAttribute("align","Center");
		td3.appendChild (document.createTextNode(factura_head[i][3]))*/
		//Modelo
		var td4 = document.createElement("td")
		td4.setAttribute("align","Center");
		td4.appendChild (document.createTextNode(factura_head[i][4]))
		//Serial
		var td5 = document.createElement("td")
		td5.setAttribute("align","Center");
		td5.appendChild (document.createTextNode(factura_head[i][5]))
		//Fecha
		var td6 = document.createElement("td")
		td6.setAttribute("align","Center");
		td6.appendChild (document.createTextNode(factura_head[i][6]))
		
		var td8 = document.createElement("td")
		td8.setAttribute("align","Center");
		td8.appendChild (document.createTextNode(factura_head[i][7]))
		
		var td7 = document.createElement("td")
		td7.setAttribute("align","Center");
		td7.className = 'normal';
		editLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		editLink.setAttribute("href", "javascript:add_factura_head('"+id+"','"+(i+1)+"');");
		editLink.appendChild(linkText);  
		td7.appendChild (editLink)
		
		row.appendChild(td1);
		row.appendChild(td2);
		row.appendChild(td8);
		row.appendChild(td4);
		row.appendChild(td5);
		row.appendChild(td6);$(a).attr("disabled","disabled");
		row.appendChild(td7);
		//	row.appendChild(td3);		
	 	tbody_factura.appendChild(row);
		 
		document.form.articulo.value='0';
		document.form.cantidad.value="";
		document.form.marca.value='0';
		document.form.marca_nombre.value="";
		document.form.modelo.value="";
		document.form.serial.value="";
		// document.form.hid_desde_itin.value=""; 
		}	

		document.form.articulo.value='0';
		document.form.cantidad.value="";
		document.form.marca.value='0';
		document.form.marca_nombre.value="";
		document.form.modelo.value="";
		document.form.serial.value="";
		// document.form.hid_desde_itin.value=""; 
	
		$("#cantidad").attr("readonly",false).val("");
		//datos generales
		$("#monto_recibido").attr("readonly",true);
		$("#observaciones").attr("readonly",true);
		$("#proveedor").attr("disabled","disabled");
		$("#txt_ubica").attr("disabled","disabled");
		$("#fecha").css("display","none");


		if($("#body_factura_head > tr").length < 1){

		$("#monto_recibido").removeAttr("readonly");
		$("#observaciones").removeAttr("readonly");
		$("#proveedor").removeAttr("disabled");
		$("#txt_ubica").removeAttr("disabled");
		$("#fecha").css("display","inline");	
	}
	 
}

function revisar()
{

	
	if(document.form.hid_desde_itin.value=="")
	{
  		alert("Debe seleccionar la fecha de recepci\u00F3n del art\u00EDculo");
  		document.form.hid_desde_itin.focus();
  		return;
	}
	
	if(document.form.txt_ubica.value=="0")
	{
		alert("Debe seleccionar la ubicaci\u00F3n del art\u00EDculo");
		document.form.txt_ubica.focus();
		return
	}

	if(document.form.monto_recibido.value=="")
	{
		window.alert("Debe especificar el monto recibido por parte del proveedor");
		document.form.monto_recibido.focus();
		return
	}

	if (document.form.proveedor.value==""){
		   alert("Debe indicar el nombre del proveedor");
		   document.form.proveedor.focus();
		   return
	}

	
	if (factura_head.length==0)
	{
		window.alert("No se registro ning\u00FAn art\u00EDculo en el inventario de responsabilidad social");
		return
	}
		
	document.form.txt_arreglo_factura_head.value="";
	
	for(i=0;i<factura_head.length;i++)
	{
		for (j=0; j<8; j++)
		{
			document.form.txt_arreglo_factura_head.value+=factura_head[i][j];
		   	
			if  ( (i<(factura_head.length-1)  ) ||  (i==(factura_head.length-1) &&  (j!=7) ) )
			{
			document.form.txt_arreglo_factura_head.value+="�";
			}
		}	
			
	}

	
	if(confirm("Datos introducidos de manera correcta. \u00BFEst\u00e1 seguro que desea continuar?"))
  {
		$("#txt_ubica").removeAttr("disabled");
		$("#proveedor").removeAttr("disabled");		
	 document.form.action="registrar_inventarioAccion.php";
	 LlenarCadenaSigiente();////////////////////////////llamar funcion de cadena
	 document.form.submit();
  }	
  
}
	
</script>
</head>
<body>
	<form name="form" method="post" enctype="multipart/form-data"
		id="form1">
		<br /> <br />
		<table width="1000" border="0" align="center"
			background="../../imagenes/fondo_tabla.gif" class="tablaalertas"
			style="padding-bottom: 10px;">
			<tr class="td_gray">
				<td width="1000" height="15" valign="middle"><span
					class="normalNegroNegrita">Registro </span></td>
			</tr>
			<tr>
				<td>
					<table width="1020" border="0" align="center" cellpadding="1"
						cellspacing="1" class="tablaalertas" style="padding-bottom: 15px;">
						<div class="normalNegroNegrita" align="center"
							style="background-color: #F0F0F0;">Datos generales</div>
						<tr>
							<td colspan="6" height="5"></td>
						</tr>
						<tr width="100%">
							<td class="normalNegrita">&ensp;&ensp;Fecha de recepci&oacute;n</td>
							<td><input type="text" size="8" id="hid_desde_itin"
								name="hid_desde_itin" class="normalNegro" readonly /> <a
								href="javascript:void(0);"
								onclick="g_Calendar.show(event, 'hid_desde_itin');"
								title="Mostrar Calendario" style="display: on" id="fecha"> <img
									src="../../js/lib/calendarPopup/img/calendar.gif"
									class="cp_img" width="25" height="20" alt="Open popup calendar" /></a></td>
							<td class="normalNegrita">Ubicaci&oacute;n:</td>
							<td class="normal"><select name="txt_ubica" id="txt_ubica"
								class="normalNegro">
									<option value="0" selected>Seleccione...</option>
									<option value="1">Torre</option>
									<option value="2">Galp&oacute;n</option>
							</select></td>
							<td class="normalNegrita">Monto recibido:</td>
							<td><div align="left" class="normal">
									<input name="monto_recibido" align="right" type="text"
										class="normalNegro" id="monto_recibido"
										onKeyPress="return acceptFloat(event)" size="8" maxlength="10" />
								</div></td>
						</tr>
						<tr width="100%">
							<td class="normalNegrita">&ensp;&ensp;Proveedor:</td>
							<td><input class="normalNegro" autocomplete="off" size="40"
								type="text" id="proveedor" name="proveedor"
								value="<?= $nombre?>"
								<?php if($rif!="" || $codigo!=""){echo "disabled='disabled'";}?> /></td>
		  <?php
			$query = "SELECT prov_nombre FROM sai_proveedor_nuevo ORDER BY prov_nombre";
			$resultado = pg_exec($conexion, $query);
			$resultado2 = pg_exec($conexion, $query);
			$numeroFilas = pg_num_rows($resultado);
			$arreglo = "";
			while($row=pg_fetch_array($resultado)){
			 $arreglo .= "'".$row["prov_nombre"]."',";	
			}
			 $arreglo = substr($arreglo, 0, -1);
			 
			 
			 //creando arreglo json para proveedores
			 $indice2 = 0;
			 $stringobj2;
			 while($row2 = pg_fetch_array($resultado2)) {
			 	// array de seriales
			 
			 	$stringobj2 [utf8_encode($row2 ['prov_nombre']) ] = utf8_encode ( $row2 ['prov_nombre'] );
			 				 
			 	
			 	$indice2 ++;
			 }
			 //echo "<pre>"; print_r($stringobj2); echo "</pre>"; 
			?>
			<script>
				var proveedores = new Array(<?= $arreglo?>);
				var stringobj2 = <?php echo json_encode($stringobj2); ?>;
				//alert(JSON.stringify(stringobj2));
				actb(document.getElementById('proveedor'),proveedores);
			</script>

							<td class="normalNegrita">Observaciones:</td>
							<td colspan="3"><div align="left" class="normal">
									<input type="text" name="observaciones" id="observaciones"
										size="25"></input>
								</div></td>

						</tr>
					</table>

					<table width="1020" border="0" align="center" cellpadding="1"
						cellspacing="1" class="tablaalertas" id="factura_head"
						style="padding: 10px 0px 15px 0px;">

						<div class="normalNegroNegrita" align="center"
							style="background-color: #F0F0F0;">Listado de art&iacute;culos</div>
						<tr width="100%">
							<td><div align="center" class="normalNegrita">Art&iacute;culo</div></td>
							<td><div align="center" class="normalNegrita">Cantidad</div></td>
							<td><div align="center" class="normalNegrita">Marca</div></td>
							<td><div align="center" class="normalNegrita">Modelo</div></td>
							<td><div align="center" class="normalNegrita">Serial</div></td>
							<td><div align="center" class="normalNegrita">Opci&oacute;n</div></td>
						</tr>
						<tr>
							<td align="center"><select name="articulo" id="articulo"
								class="normalNegro">
									<option value="0">[Seleccione]</option>
      <?php
		$i=0;
     	if (isset($_REQUEST['id_arti'])){   
        $sql_d="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','t1.id,nombre,unidad_medida','t1.id=t2.id and esta_id=1 and id_tipo=4 and t1.id<>'||'''$arti''','nombre',1) resultado_set(id varchar, nombre varchar, unidad_medida varchar)";
 	    echo("
			 <script language='javascript'>
				var arti = new Array(); 
				arti[0]='".utf8_encode($arti_id)."';
				arti[1]=escape(\"".utf8_encode($arti_descripcion)."\");
				arti[2]='$medida';
				ori[$i]=arti;
				</script>
			");
			$i++;
	?>
	 <option value="<?=$arti_id?>" selected="true"><?=$arti_descripcion?></option>
	<?}else{	
	      $sql_d="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','t1.id,nombre,unidad_medida','t1.id=t2.id and esta_id=1 and id_tipo=4','nombre',1) resultado_set(id varchar, nombre varchar, unidad_medida varchar)";
	} 
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
				arti[1]=\"".addslashes(utf8_encode($arti_descripcion))."\";
				arti[2]='$unme_id';
				ori[$i]=arti;
				</script>
			");
			$i++;	?>
        <option value="<?=$arti_id?>"><?=$arti_descripcion?>:<?=$unme_id?></option>
   <?php } ?>
       </select>
								<div>
									<input name="arti_nombre" type="hidden" id="arti_nombre" />
								</div></td>
							<td><div align="center" class="peq">
									<input name="cantidad" type="text" class="normalNegro"
										id="cantidad" onKeyUp="javascript: validar(this,0)" size="4"
										maxlength="6">
								</div></td>
							<td align="center"><select name="marca" id="marca"
								class="normalNegro">
	<?php $sql_p="Select * from sai_bien_marca where esta_id=1 order by bmarc_nombre";
	      $resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
	      $i=0;
	      while($depe_row=pg_fetch_array($resultado_set_most_p)) {
	      	$marca_id=$depe_row['bmarc_id'];
		    $marca_nombre=$depe_row['bmarc_nombre'];
	      	 echo("
			<script language='javascript'>
			var marca = new Array(); 
			marca[0]='$marca_id';
			marca[1]='$marca_nombre';
			marcas[$i]=marca;
			</script>
		    ");
		    $i++;
	      	
	       if ($marca_id==0){?>
	 <option value="<?=$marca_id?>" selected><?=$marca_nombre;?></option>
	<?php }else{?>
		<option value="<?=$marca_id;?>"><?=$marca_nombre;?></option>
	<?php }
	      
	      }?></select> <input name="marca_nombre" type="hidden"
								id="marca_nombre" /></td>
							<td align="center"><input type="text" name="modelo" id="modelo"></input>
							</td>
							<td align="center" class="normal"><input type="text"
								name="serial" id="serial"></input></td>
							<td><div align="center" class="normal">
									<a href="javascript: add_factura_head('factura_head','0'); "
										class="normal">Agregar </a>
								</div></td>
						</tr>
						<tbody id="body_factura_head" class="normal">
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td height="10" valign="middle"></td>
			</tr>
			<tr>
				<td height="16" colspan="3">
				
				<div align="center" id="accionesEjecutar">
    
    <?php
    ////////////////////////////boton de cadena generar documento 
     foreach ($GLOBALS['SafiRequestVars']['opciones'] as $index){ ?>
           <span class="cadena"> <input type="button"
							value="<?php echo $index['wfop_nombre'];?>"
							id="<?php echo $index['wfop_descrip'];?>"
							onclick="GenerarSigienteCadena(<?php echo $index['id_cadena_hijo'];?>,0)" />
						</span>
               <?php }?>
    
    
    </div></td>
			</tr>
		</table>
		<input type="hidden" name="txt_arreglo_factura_head"
			id="txt_arreglo_factura_head" />
	</form>
</body>
</html>
<?php pg_close($conexion);?>