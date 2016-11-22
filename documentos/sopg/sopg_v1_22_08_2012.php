<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");
require("../../includes/funciones.php");

if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();

$unificar=trim($_REQUEST['hid_seleccion']);
$documento=trim($_REQUEST['doc']);
$otro=0;
$total_definitivo=0;
$partida_IVA=trim($_SESSION['part_iva']);



/*Consulto los impuesto IVA*/
$sql= "select * from sai_consulta_impuestos ('0','IVA') as resultado ";
$sql.= "(id varchar, nombre varchar, porcetaje float4,  principal bit, tipo bit)";
$resultado_set= pg_exec($conexion ,$sql);
$valido=$resultado_set;
if ($resultado_set){
	$elem_impuesto=pg_num_rows($resultado_set);
	$id_impuesto=array($elem_impuesto);
	$porce_impuesto=array($elem_impuesto);
	$impu_nombre=array($elem_impuesto);
	$impu_prici=array($elem_impuesto);
	$ii=0;
	while($row_rete=pg_fetch_array($resultado_set)){
		$id_impuesto[$ii]=strtoupper(trim($row_rete['id']));
		$porce_impuesto[$ii]=trim($row_rete['porcetaje']);
		$impu_prici[$ii]=trim($row_rete['principal']);
		$impu_nombre[$ii]=trim($row_rete['nombre']);
		$ii++;
	}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI:Ingresar Solicitud de Pago</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
	<script language="javascript" src="../../js/func_montletra.js"></script>
	<script language="JavaScript" src="../../js/lib/actb.js"></script>
	<script language="JavaScript" src="../../js/funciones.js"></script>
	<script language="JavaScript" src="../../js/funciones_sopg.js"></script>
	<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
	<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');
	</script>
<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
<script language="JavaScript">

$(document).ready(function(){
	$("#comp_id").change(function(){	//revisar nombre del campo
	  $.ajax({
	    data: {sendValue:$(this).val()},
	    type: "POST",
	    dataType: "json",
	    //dataType: "html",
	    url: "genera-select.php",
	    success: 

	    /*function(data){
	    	$("#display").text(data);
        }*/
		    function(json){
	     var index = 0;
		 $.each(json.listaPartida, function(idObjPartida, objPartida){
			var registro = new Array();
		    registro[0]=objPartida.id_comp;
			registro[1]=objPartida.acc_esp;
			registro[2]=objPartida.acc_pp;
			registro[3]=objPartida.imputacion;
			registro[4]=objPartida.partida;					
			registro[5]=objPartida.titulo;
			registro[6]=objPartida.accion;
			registro[7]=objPartida.part_nombre;
			registro[8]=objPartida.gestor;
			registro[9]=objPartida.costo;
			registro[10]=objPartida.monto_comp;
			registro[11]=objPartida.fuente;
			listado_comp[index]=registro;
			index++;
			});  
		    consulta_presupuesto();	
	    } 
	   });
	  });
	});


	beneficiarios = new Array();
	//para los apendchild
	partidas = new Array();
	validar_compromiso = new Array();
	todas_pdas = new Array();
	monto_tot=new Array();
	monto_tot_exento=new Array();
	monto_total=new Array();
	monto_total_exento=new Array();
	arreglo= new Array();
	partidas_orden=new Array();
	arreglo_partidas=new Array();
	var listado_comp = new Array();
	listado_estados = new Array();
	var facturas = new Array();
	
	function pago_ordc(){

		<?php 
		$datos_ordc="SELECT prov_id_rif,prov_nombre,t1.justificacion,t2.depe_id
		FROM sai_orden_compra t1, sai_req_bi_ma_ser t2, sai_proveedor_nuevo
		WHERE ordc_id='".$documento."' and t1.rebms_id=t2.rebms_id and prov_id_rif=rif_proveedor_seleccionado";
		$resultado_ordc=pg_exec($conexion,$datos_ordc);
		if ($row_ordc=pg_fetch_array($resultado_ordc)){
			$rif_prov=$row_ordc['prov_id_rif']." : ".$row_ordc['prov_nombre'];
		?>
			document.form.opt_bene[1].checked = true;
			mostrarBeneficiarios(2);
			document.getElementById('beneficiarioProveedor').value='<?=$rif_prov; ?>';


		<?php
		 
		}?>}

	function verifica_partida(){
		var partida_num= document.getElementById('item').getElementsByTagName('tr').length; 
		if (partida_num>0){
			alert("Para cambiar de Categor"+iACUTE+"a Proyecto o Acci"+oACUTE+"n Centralizada \n se requiere no tener asociadas partidas");
			}else{
			abrir_ventana('../../includes/arbolCategoria.php?dependencia=<?= $_SESSION['user_depe_id'];?>&campo_nom_supe=txt_nombre_imputa&campo_cod_supe=txt_cod_imputa&campo_nombre_accion=txt_nombre_accion&campo_cod_accion=txt_cod_accion&tipo=chk_tp_imputa&formulario=form&tipo_docu=0&centrog=centro_gestor&centroc=centro_costo&opcion=sopg&campo_cod_supe2=txt_cod_imputa2&campo_cod_accion2=txt_cod_accion2');
		}
	}
	function mostrarBeneficiarios(valor){
		if(valor=='1'){
			div = document.getElementById("empleadoInputContainer");
			if(div.hasChildNodes()){
			    while(div.childNodes.length >= 1 ){
			    	div.removeChild(div.firstChild);       
			    }
			}
			input = document.createElement('INPUT');
			input.setAttribute("autocomplete","off");
			input.setAttribute("size","70");
			input.setAttribute("type","text");
			input.setAttribute("id","beneficiarioEmpleado");
			input.setAttribute("name","beneficiarioEmpleado");
			input.setAttribute("value","");
			input.setAttribute("class","normal");
			div.appendChild(input);
			actb(document.getElementById('beneficiarioEmpleado'),empleadosAMostrar);
			document.getElementById('contenedorEmpleados').style.display='block';
			document.getElementById('contenedorProveedores').style.display='none';
			document.getElementById('contenedorOtros').style.display='none';
			document.getElementById('beneficiarioEmpleado').focus();
		}else if(valor=='2'){
			div = document.getElementById('proveedorInputContainer');
			if(div.hasChildNodes()){
			    while(div.childNodes.length >= 1 ){
			    	div.removeChild(div.firstChild);       
			    }
			}
			input = document.createElement('INPUT');
			input.setAttribute("autocomplete","off");
			input.setAttribute("size","70");
			input.setAttribute("type","text");
			input.setAttribute("id","beneficiarioProveedor");
			input.setAttribute("name","beneficiarioProveedor");
			input.setAttribute("value","");
			input.setAttribute("class","normal");
			div.appendChild(input);
			actb(document.getElementById('beneficiarioProveedor'),proveedoresAMostrar);
			document.getElementById('contenedorProveedores').style.display='block';
			document.getElementById('contenedorEmpleados').style.display='none';
			document.getElementById('contenedorOtros').style.display='none';
			document.getElementById('beneficiarioProveedor').focus();
		}else if(valor=='3'){
			div = document.getElementById('otroInputContainer');
			if(div.hasChildNodes()){
			    while(div.childNodes.length >= 1 ){
			    	div.removeChild(div.firstChild);       
			    }
			}
			input = document.createElement('INPUT');
			input.setAttribute("autocomplete","off");
			input.setAttribute("size","70");
			input.setAttribute("type","text");
			input.setAttribute("id","beneficiarioOtro");
			input.setAttribute("name","beneficiarioOtro");
			input.setAttribute("value","");
			input.setAttribute("class","normal");
			div.appendChild(input);
			actb(document.getElementById('beneficiarioOtro'),otrosAMostrar);
			document.getElementById('contenedorOtros').style.display='block';
			document.getElementById('contenedorEmpleados').style.display='none';
			document.getElementById('contenedorProveedores').style.display='none';
			document.getElementById('beneficiarioOtro').focus();
		}else if(valor=='4'){
			div = document.getElementById('itemContainerTemp');
			if(div.hasChildNodes()){
			    while(div.childNodes.length >= 1 ){
			    	div.removeChild(div.firstChild);       
			    }
			}
			input = document.createElement('INPUT');
			input.setAttribute("autocomplete","off");
			input.setAttribute("size","70");
			input.setAttribute("type","text");
			input.setAttribute("id","itemCompletarTemp");
			input.setAttribute("name","itemCompletarTemp");
			input.setAttribute("value","");
			input.setAttribute("class","normal");
			div.appendChild(input);
			actb(document.getElementById('itemCompletarTemp'),arregloItemsTemp);  
			document.getElementById('itemContainerTemp').style.display='block';
			document.getElementById('contenedorOtros').style.display='none';
			document.getElementById('contenedorEmpleados').style.display='none';
			document.getElementById('contenedorProveedores').style.display='none';
			document.getElementById('itemCompletarTemp').focus();
		}

	}	
</script>
<?php 
   $i=0;
   $sql_estados="select * from sai_edos_venezuela order by edo_nombre";
   $resultado_estados=pg_exec($conexion,$sql_estados);
   while($row=pg_fetch_array($resultado_estados)){
   	$edo_nombre=$row['edo_nombre'];
   	$edo_id=$row['edo_id'];
   
   	echo("
				<script language='javascript'>
				var registro = new Array(); 
				registro[0]='$edo_id';
				registro[1]='$edo_nombre';
				listado_estados[$i]=registro;
				</script>
				");
				$i++;
   }

$a_o="comp-%".substr($_SESSION['an_o_presupuesto'],2,2);
 ?>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css"	media="all" />
</head>
<body onLoad="pago_ordc();">
<form name="form" method="post" action="" enctype="multipart/form-data"	id="form1">
<input type="hidden" name="hid_monto_letras" id="hid_monto_letras">
<input type="hidden" name="hid_tp_imp" id="hid_tp_imp" value="">
<input type="hidden" name="hid_nombre_imp" value="">
<input type="hidden" name="hid_monto_tot" value="<?= $or_orde_total_general?>">
<table align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
	<td colspan="3" class="normalNegroNegrita" align="center">INGRESAR SOLICITUD DE PAGO</td>
  </tr>
  <tr>
	<td height="223">
	  <table width="100%">
		<tr>
		  <td height="21" colspan="3" valign="middle" class="td_gray"><span class="normalNegroNegrita"><strong>DATOS DEL SOLICITANTE</strong></span></td>
		</tr>
		<tr>
		  <td height="28" colspan="2"><div id="display"></div><div  class="normalNegrita">Solicitante:</div></td>
		  <td width="465" class="normalNegro"><?= $_SESSION['solicitante']?></td>
		</tr>
		<tr>
		  <td height="28" colspan="2"><div  class="normalNegrita">C&eacute;dula de Identidad:</div></td>
		  <td class="normalNegro"><?= $_SESSION['cedula']?></td>
		</tr>
		<tr>
		  <td height="28" colspan="2"><div  class="normalNegrita">Email:</div></td>
		  <td class="normalNegro"><?= $_SESSION['email']?></td>
		</tr>
		<tr>
		  <td height="28" colspan="2"><div  class="normalNegrita">Cargo:</div></td>
		  <td class="normalNegro"><?= $_SESSION['cargo']?></td>
        </tr>
		<tr>
		  <td height="30" colspan="2"><div class="normalNegrita">Dependencia Solicitante:</div></td>
		  <td>
		  <?php
			$sql_str="SELECT depe_id,depe_nombrecort,depe_nombre FROM sai_dependenci WHERE depe_nivel='4' or depe_nivel='3' order by depe_nombre";
			$res_q=pg_exec($sql_str);
		   ?>
			<select name="dependencia" class="normalNegro" id="dependencia">
			  <option value="0" selected="selected">--</option>
			  <?php
				while($depe_row=pg_fetch_array($res_q)){
				?>
			  <option value="<?=(trim($depe_row['depe_id']))?>"><?=(trim($depe_row['depe_nombre']))?></option>
			  <?php }?>
			</select></td>
		</tr>
		<tr>
		  <td height="30" colspan="2"><div class="normalNegrita">Tipo de Solicitud:</div></td>
		  <td>
		  <?php
			$sql_str="SELECT * FROM  sai_seleccionar_campo('sai_tipo_solicitud','id_sol,nombre_sol','esta_id=1','nombre_sol',1) resultado_set(id_sol int4,nombre_sol varchar)";
			$res_q=pg_exec($sql_str);
		  ?>
			<select name="tipo_sol" class="normalNegro" id="tipo_sol">
			  <option value="0" selected="selected">--</option>
			  <?php
				while($depe_row=pg_fetch_array($res_q)){
				?>
			  <option value="<?= (trim($depe_row['id_sol']))?>"><?=(trim($depe_row['nombre_sol']))?></option>
			  <?php	}?>
			</select></td>
		</tr>
		<tr>
		  <td height="30" colspan="2"><div  class="normalNegrita">Tel&eacute;fono de Ofic.:</div></td>
		  <td class="normalNegro"><?= $_SESSION['tlf_ofic']?></td>
		</tr>
	  </table>
	  </td>
	</tr>
	<tr>
	 <td>
	  <table width="100%">
		<tr class="td_gray">
		  <td height="21" colspan="3" valign="middle" class="td_gray"><div align="left" class="normalNegroNegrita">DATOS DEL BENEFICIARIO</div></td>
		</tr>
		<tr>
		  <td colspan="3">
			<table class="normal">
			 <tr>
			  <td width="120"><input name="opt_bene" type="radio" value="" onClick="javascript:mostrarBeneficiarios(1)">
							 Empleado</td>
			  <td width="120"><input name="opt_bene" type="radio" value="" onClick="javascript:mostrarBeneficiarios(2)">
							 Proveedor</td>
			  <td width="120"><input name="opt_bene" type="radio" value="" onClick="javascript:mostrarBeneficiarios(3)">
							 Otro</td>
			</tr>
			</table></td>
		 </tr>
		 <tr>
			<td colspan="3">
			 <div id="contenedorEmpleados" style="display: none;">
			 <div id="empleadoInputContainer" style="width: 504px;float: left;">
			 <input autocomplete="off" size="70" type="text" id="beneficiarioEmpleado" name="beneficiarioEmpleado" value="" class="normalNegro"/></div>
			 <div style="float: left;"><input type="button" value="Agregar" onclick="javascript:agregarBeneficiario('1');" class="normalNegrita"/></div>
			 <div style="width: 500px"><br/>&nbsp;<span class="normalNegrita">(*)</span><span class="normalNegro">Introduzca el n&uacute;mero de c&eacute;dula o una palabra contenida en el nombre del empleado.</span></div>
			<?php
			  $query = 	"SELECT * from sai_empleado where esta_id=1";
			  $resultado = pg_exec($conexion, $query);
			  $numeroFilas = pg_num_rows($resultado);
			  $arregloEmpleados = "";
			  $cedulasEmpleados = "";
			  $nombresEmpleados = "";
			  while($row=pg_fetch_array($resultado)){
				$arregloEmpleados .= "'".$row["empl_cedula"]." : ".strtoupper(str_replace("\n"," ",$row["empl_nombres"]." ".$row["empl_apellidos"]))."',";
				$cedulasEmpleados .= "'".$row["empl_cedula"]."',";
				$nombresEmpleados .= "'".str_replace("\n"," ",strtoupper($row["empl_nombres"]." ".$row["empl_apellidos"]))."',";
			  }
				$arregloEmpleados = substr($arregloEmpleados, 0, -1);
				$cedulasEmpleados = substr($cedulasEmpleados, 0, -1);
				$nombresEmpleados = substr($nombresEmpleados, 0, -1);
			 ?>
			<script>
				var cedulasEmpleados = new Array(<?= $cedulasEmpleados?>);
				var nombresEmpleados = new Array(<?= $nombresEmpleados?>);
				var empleadosAMostrar = new Array(<?= $arregloEmpleados?>);
			</script></div>
			<div id="contenedorProveedores" style="display: none;">
			<div id="proveedorInputContainer" style="width: 504px;float: left;">
			<input autocomplete="off" size="70" type="text" id="beneficiarioProveedor" name="beneficiarioProveedor" value="" class="normalNegro"/></div>
			<div style="float: left;"><input type="button" value="Agregar" onclick="javascript:agregarBeneficiario('2');" class="normal"/></div>
			<div style="width: 500px"><br/>&nbsp;<span class="normalNegrita">(*)</span><span class="normalNegro">Introduzca el RIF o una palabra contenida en el nombre del proveedor.</span></div>
			<?php
			  $query = 	"SELECT prov_id_rif,prov_nombre 
						 FROM sai_proveedor_nuevo 
						 WHERE prov_esta_id=1 ORDER BY prov_nombre";
			  $resultado = pg_exec($conexion, $query);
			  $numeroFilas = pg_num_rows($resultado);
			  $arregloProveedores = "";
			  $cedulasProveedores = "";
			  $nombresProveedores = "";
			  while($row=pg_fetch_array($resultado)){
				$arregloProveedores .= "'".$row["prov_id_rif"]." : ".strtoupper(str_replace("\n"," ",$row["prov_nombre"]))."',";
				$cedulasProveedores .= "'".$row["prov_id_rif"]."',";
				$nombresProveedores .= "'".str_replace("\n"," ",strtoupper($row["prov_nombre"]))."',";
			  }
				$arregloProveedores = substr($arregloProveedores, 0, -1);
				$cedulasProveedores = substr($cedulasProveedores, 0, -1);
				$nombresProveedores = substr($nombresProveedores, 0, -1);
			  ?>
			 <script>
				var cedulasProveedores = new Array(<?= $cedulasProveedores?>);
				var nombresProveedores = new Array(<?= $nombresProveedores?>);
				var proveedoresAMostrar = new Array(<?= $arregloProveedores?>);
			</script></div>
			<div id="contenedorOtros" style="display: none;">
			<div id="otroInputContainer" style="width: 504px;float: left;">
			<input autocomplete="off" size="70" type="text" id="beneficiarioOtro" name="beneficiarioOtro" value="" class="normalNegro"/></div>
			<div style="float: left;"><input type="button" value="Agregar" onclick="javascript:agregarBeneficiario('3');" class="normal"/></div>
			<div style="width: 500px"><br/>&nbsp;<span class="normalNegrita">(*)</span><span class="normalNegro">Introduzca la c&eacute;dula o una palabra contenida en el nombre de la persona.</span></div>
			<?php
			  $query = 	"SELECT * from sai_viat_benef where benvi_esta_id=1";
			  $resultado = pg_exec($conexion, $query);
			  $numeroFilas = pg_num_rows($resultado);
			  $arregloOtros = "";
			  $cedulasOtros = "";
			  $nombresOtros = "";
			  while($row=pg_fetch_array($resultado)){
				$arregloOtros .= "'".$row["benvi_cedula"]." : ".strtoupper(str_replace("\n"," ",$row["benvi_nombres"]." ".$row["benvi_apellidos"]))."',";
				$cedulasOtros .= "'".$row["benvi_cedula"]."',";
				$nombresOtros .= "'".str_replace("\n"," ",strtoupper($row["benvi_nombres"]." ".$row["benvi_apellidos"]))."',";
			  }
				$arregloOtros = substr($arregloOtros, 0, -1);
				$cedulasOtros = substr($cedulasOtros, 0, -1);
				$nombresOtros = substr($nombresOtros, 0, -1);
			?>
			<script>
				var cedulasOtros = new Array(<?= $cedulasOtros?>);
				var nombresOtros = new Array(<?= $nombresOtros?>);
				var otrosAMostrar = new Array(<?= $arregloOtros?>);
			</script></div></td>
		</tr>
		<tr>
		  <td colspan="3"><br/>
			<table id="beneficiariosTable">
			  <tr valign="middle">
				<td width="20" class="normalNegrita">N&deg;</td>
				<td width="60" class="normalNegrita">C&eacute;dula/RIF</td>
				<td width="400" class="normalNegrita">Nombre</td>
				<td width="60" class="normalNegrita"><div align="center">Tipo</div></td>
				<td width="60" class="normalNegrita">Observaci&oacute;n</td>
				<td width="60" class="normalNegrita">Estado</td>										
				<td width="60" class="normalNegrita"><div align="center">Opci&oacute;n</div></td>
			  </tr>
			  <tbody id="beneficiariosBody" class="normal"></tbody>
			</table></td>
		</tr>
		</table>
		<table>
		  <tr>
			<td><p>&nbsp;</p></td>
		  </tr>
		</table>
		<input type="hidden" id="hid_bene_tp" name="hid_bene_tp" value="">
		<input type="hidden" id="hid_beneficiario" name="hid_beneficiario" value="">
		<input type="hidden" id="hid_bene_ci_rif" name="hid_bene_ci_rif" value="">
		<input type="hidden" id="hid_contador" name="hid_contador" value="">
	   </td>
	  </tr>
	  <tr>
		<td>
		 <table width="100%" class="tablaalertas">
		   <tr>
			<td height="21" colspan="8" valign="middle" class="td_gray"><div align="left" class="normalNegroNegrita">DOCUMENTOS ANEXOS</div></td>
		   </tr>
		   <tr class="normalNegro">
			 <td height="21"><input name="chk_factura" type="checkbox" id="chk_factura"/></td>
			 <td height="21" >Factura</td>
			 <td height="21" ><input name="chk_ordc" type="checkbox" id="chk_ordc"/></td>
			 <td height="21">Orden de Compra</td>
			 <td height="21" ><input name="chk_contrato" type="checkbox" id="chk_contrato"/></td>
			 <td height="21" >Contrato</td>
			 <td height="21" ><input name="chk_certificacion"	type="checkbox" id="chk_certificacion"/></td>
			 <td height="21" class="peq">Certificaci&oacute;n del Control Perceptivo</td>
		   </tr>
		   <tr class="normalNegro">
			 <td height="21" ><input name="chk_informe" type="checkbox" id="chk_informe"/></td>
			 <td height="21" class="normalNegro">Informe o Solicitud de Pago a Cuentas</td>
			 <td height="21" ><input name="chk_ords" type="checkbox" id="chk_ords"/></td>
			 <td height="21" >Orden de Servicio</td>
			 <td height="21" class="peq"><input name="chk_pcta" type="checkbox" id="chk_pcta"/></td>
			 <td height="21" >Punto de Cuenta</td>
		  </tr>
		  <tr class="normalNegro">
 			 <td height="21" ><input name="chk_otro" type="checkbox" id="chk_otro" onclick="javascript:act_desact()"/></td>
			 <td height="21" >Otro (Especifique)</td>
			 <td height="21" ></td>
			 <td height="21"><input type="text" name="txt_otro" id="txt_otro" size="25" maxlength="25" value="" disabled="disabled"></td>
  		   </tr>
		  </table>
			</td>
		</tr>
		<tr>
		  <td>
			<table width="100%">
			  <tr class="td_gray">
				<td height="21" colspan="3" valign="middle" class="td_gray"><div align="left" class="normalNegroNegrita">DETALLES DE LA SOLICITUD</div></td>
			  </tr>
			  <tr>
				<td height="28" valign="middle" colspan="2" class="normalNegrita">Factura N&deg;:</td>
				<td><input name="txt_factura" type="text" class="normalNegro" id="txt_factura" size="20" maxlength="20" align="right" onkeyup="validar_digito(txt_factura)"/>
					<font class="normalNegrita">Fecha:</font>
					<input type="text" size="10" id="txt_fecha_factura"	name="txt_fecha_factura" class="dateparse" readonly />
					<a href="javascript:void(0);" onclick="g_Calendar.show(event,'txt_fecha_factura');" title="Show popup calendar">
					<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a>
					<font class="normalNegrita">N&deg; de Control:</font>
					<input name="txt_factura_num_control" type="text" class="normalNegro" id="txt_factura_num_control" size="11" maxlength="10" align="right" onkeyup="validar_digito(txt_factura_num_control);"/></td>
			  </tr>
			  <tr>
				<td height="28" colspan="2" valign="middle" class="normalNegrita">Prioridad:</td>
				<td valign="middle">
				<select name="slc_prioridad" class="normalNegro">
				  <option value="3" selected>Alta</option>
				</select>
			   <tr class="normal">
				<td height="33" colspan="2" class="normalNegrita">N&uacute;mero del Compromiso:</td>
				<td>
				<?php
				  $sql_str="SELECT distinct(docg_id) as comp_id ,docg_fecha FROM sai_doc_genera where esta_id<>15 and docg_id like '".$a_o."' order by docg_fecha";
				  $res_q=pg_exec($sql_str);
				?>
				<select name="comp_id" class="normalNegro" id="comp_id" onKeypress="buscar_op(this,text2)" onblur="borrar_buffer()" onclick="borrar_buffer()" onChange="consulta_presupuesto()">
				  <option value="0" selected="selected">--</option>
				  <option value="N/A">N/A</option>
				  <?php
					while($depe_row=pg_fetch_array($res_q)){
					$comp_id=substr($depe_row['comp_id'],5);
				  ?>
				  <option value="<?=(trim($comp_id))?>"><?= (trim($comp_id))?></option>
				  <?php	} ?>
				 </select><input type="hidden"  name="text2"></td>
				</tr>
				<tr class="normal">
				  <td height="33" colspan="2" class="normalNegrita">Fuente de Financiamiento:</td>
				  <td>
					<select name="numero_reserva" class="normalNegro">
					<?php 
					  $sql_ff="SELECT * FROM  sai_seleccionar_campo('sai_fuente_fin','fuef_id,fuef_descripcion','esta_id<>15','fuef_descripcion',1) resultado_set(fuef_id varchar,fuef_descripcion varchar)";
					  $res_ff=pg_exec($sql_ff);
					?>
					 <option value="0">--</option>
					 <option value="N/A">N/A</option>
					<?php while($row_ff=pg_fetch_array($res_ff)){?>
					 <option value="<?php echo $row_ff['fuef_id']?>"><?php echo $row_ff['fuef_descripcion']?></option>
					<?php }?>
					</select></td>
				 </tr>
				<tr>
				  <td height="75" colspan="2"><div  class="normalNegrita">Motivo del Pago:</div></td>
				  <td class="normal">
				  <textarea name="txt_detalle" cols="70" rows="3" class="normalNegro" onKeyDown="contador(this.form.txt_detalle,this.form.remLen,60);" onKeyUp="contador(this.form.txt_detalle,this.form.remLen,60);"><?=$ordm_motivo?></textarea>
				  <input type="text" name="remLen" size="3" maxlength="3" value="60" readonly></td>
				</tr>
				<tr>
  				  <td height="78" colspan="2"><div  class="normalNegrita">Observaci&oacute;n:</div></td>
				  <td class="normal">
				  <textarea name="txt_observa" cols="70" rows="3"	class="normalNegro" onBlur="javascript:LimitText(this,600)"></textarea></td>
				</tr>
			 </table>
			</td>
		</tr>
		<tr>
			  <td><input name="hid_val" type="hidden" id="hid_val"/>
				  <!--<input type="hidden" name="txt_arreglo_part" value=""/>
				  <input type="hidden" name="txt_arreglo_mont" value=""/>-->
				  <input type="hidden" name="hid_partida_actual" value=""/>
				  <input type="hidden" name="hid_comprobar"/></td>
		</tr>
		<tr>
		  <td>
			<table align="center" class="tablaalertas">
				<tr class="td_gray">
					<td><div align="center" class="peqNegrita" id="Categoria"  style="display:none">
						<a href="javascript:verifica_partida();">
						<img src="../../imagenes/estadistic.gif" width="24" height="24" border="0"/>
						Categor&iacute;a</a></div></td>
					<td><div align="center" class="normalNegro">C&oacute;digo</div></td>
					<td><div align="center"><span class="normalNegro">Denominaci&oacute;n</span></div></td>
				</tr>
				<tr>
					<td><div align="left"><input name="chk_tp_imputa" type="radio" class="normalNegro" value="1" disabled="disabled">
					<span class="peqNegrita">Proyectos</span></div></td>
					<td rowspan="2"><div align="center">
						<input name="txt_cod_imputa" type="hidden" id="txt_cod_imputa" value="" >
		 				<input name="txt_cod_imputa2" type="text" class="normalNegro" id="txt_cod_imputa2" size="15" value="" readonly="readonly"></div></td>
					<td rowspan="2"><div align="center">
					<input name="txt_nombre_imputa" type="text" class="normalNegro" id="txt_nombre_imputa" size="70" readonly="readonly"	value=""></div></td>
				</tr>
				<tr>
					<td valign="top"><div align="left">
					<input name="chk_tp_imputa" type="radio" class="normalNegro" value="0" disabled="disabled"/>
					<span class="peqNegrita">Acci&oacute;n Cent.</span></div></td>
				</tr>
				<tr>
					<td><div align="left"><p><span class="peqNegrita">&nbsp;Acci&oacute;n Espec&iacute;fica</span></p></div></td>
					<td><div align="center">
						<input name="txt_cod_accion" type="hidden" id="txt_cod_accion">
						<input name="txt_cod_accion2" type="text" class="normalNegro" id="txt_cod_accion2" size="15" readonly></div></td>
					<td><div align="center">
						<input name="txt_nombre_accion" type="text"	class="normalNegro" id="txt_nombre_accion" size="70" readonly="readonly"/></div></td>
				</tr>
				<tr>
					<td class="peqNegrita" align="right">Dependencia</td>
					<td>
					<?php
					  $id_depe=$_SESSION['user_depe_id'];
					  $sql_str="SELECT * FROM  sai_seleccionar_campo('sai_dependenci','depe_id,depe_nombrecort,depe_nombre','depe_id='||'''$id_depe''','',2) resultado_set(depe_id varchar,depe_nombrecort varchar, depe_nombre varchar)";
					  $res_q=pg_exec($sql_str);
					 ?>
					  <select name="opt_depe" class="normalNegro" id="opt_depe">
					  <?php
						while($depe_row=pg_fetch_array($res_q)){
						?>
						<option value="<?= (trim($depe_row['depe_id']))?>"><?= (trim($depe_row['depe_nombrecort']))?></option>
						<?php 
						}
						?>
						</select>
					</td>
					<td class="peqNegrita" align="left">Centro Gestor:
						<input type="text" name="centro_gestor" id="centro_gestor" size="5"	readonly="readonly" class="normalNegro"/>
						&nbsp;&nbsp;Centro Costo:
						<input type="text" name="centro_costo" id="centro_costo" size="5" readonly="readonly" class="normalNegro"></td>
				</tr>
			 </table>
		  </td>
	  </tr>
	  <tr>
		<td colspan="2"><br/><br/>
		<input type="hidden" name="hid_largo" id="hid_largo"/>
		<input type="hidden" name="hid_val" id="hid_val"/>
	    <div id="PartidasTemporales" style="display:none">
		<table align="center" width="700px" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		  <tr>
			<td colspan="2" align="center">
			<input name="opt_bene" type="radio" value="" onClick="javascript:mostrarBeneficiarios(4)">
			&nbsp;<font  class="normalNegrita">Cuentas contables</font><font class="peq_naranja">(*)</font> 
			<div id="itemContainerTemp" style="width: 560px;float: center;">
			<input autocomplete="off" size="70" type="text" id="itemCompletarTemp" name="itemCompletarTemp" value="" class="normalNegro"/></div>
			<div style="width: 700px; float: left;text-align: left;margin-top: -10px;" class="normalNegro">
			<br/><div align="center"><span class="peq_naranja">(*)</span>Introduzca la cuenta contable o una palabra contenida en el nombre.</div>
			</div><br></div>					
			<?php
		 	  $query_partidas_temp="SELECT t2.part_id, t2.part_nombre,t1.cpat_id,cpat_nombre 
			  FROM sai_convertidor t1,  sai_partida t2,sai_cue_pat t3 
			  WHERE t1.part_id=t2.part_id and t2.pres_anno='".$_SESSION['an_o_presupuesto']."'
			  and t2.part_id like '4.11%' and t1.cpat_id=t3.cpat_id order by 3";
								
			  $resultado = pg_exec($conexion, $query_partidas_temp);
			  $numeroFilas = pg_num_rows($resultado);
								
			  $arregloItems = "";
			  $idsPartidasItems = "";
			  $nombresPartidasItems = "";
			  $nombresItems = "";
			  while($row=pg_fetch_array($resultado)){
				$arregloItems .= "'".$row["part_id"]." : ".strtoupper(str_replace("\n"," ",$row["part_nombre"]))."',";
				$idsPartidasItems .= "'".$row["part_id"]."',";
				$arregloCtas .= "'".$row["cpat_id"]." : ".strtoupper(str_replace("\n"," ",$row["cpat_nombre"]))."',";
				$idsCtasItems .= "'".$row["cpat_id"]."',";										
				$nombresPartidasItems .= "'".str_replace("\n"," ",strtoupper($row["part_nombre"]))."',";
			  }
			  //$arregloItems = quitarAcentosMayuscula(substr($arregloItems, 0, -1));
			  $arregloItems = quitarAcentosMayuscula(substr($arregloCtas, 0, -1));
			  $idsPartidasItems = substr($idsPartidasItems, 0, -1);
			  $idsCtasItems = substr($idsCtasItems, 0, -1);
			  $nombresPartidasItems = quitarAcentosMayuscula(substr($nombresPartidasItems, 0, -1));
			  ?>
			  <script>
				var arregloItemsTemp = new Array(<?= $arregloItems?>);
				var idsPartidasItemsTemp = new Array(<?= $idsPartidasItems?>);
				var idsCtasItems = new Array(<?= $idsCtasItems?>);
				var nombresPartidasItems = new Array(<?= $nombresPartidasItems?>);
				actb(document.getElementById('itemCompletarTemp'),arregloItemsTemp);
			  </script></td>
			 </tr>
			 <tr>
			   <td class="normal" width="200px" align="center"><b>Monto Sujeto: </b><input type="text" id="sujeto_temp" name="sujeto_temp" size="20" class="normalNegro" value="0"  onkeypress="return inputFloat(event,true);"/></td>
			   <td class="normal" width="200px" align="center"><b>Monto Exento: </b><input type="text" id="exento_temp" name="exento_temp" class="normalNegro" size="20" value="0"  onkeypress="return inputFloat(event,true);"></input>
			   <input type="button" value="Agregar" onclick="javascript:agregarItem(itemCompletarTemp,sujeto_temp,exento_temp,idsPartidasItemsTemp,idsCtasItems),add_monto(),calcular_iva();" class="normal"/></td></tr>
	       </table></div>
		   <br>
		   <div id="PartidasAutomaticas" style="display: none">
		    <table align="center" width="700px" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" id="tbl_part">
		     <tr>
			   <td class="normal" align="center"><b></b></td>
			   <td class="normal" align="center"><b>Partida</b></td>
			   <td class="normal" align="center"><b>Nombre Partida</b></td>
			   <td class="normal" align="center"><b>Proy/ACC</b></td>
			   <td class="normal" align="center"><b>Acc. Esp.</b></td>
			   <td class="normal" align="center"><b>Monto Compromiso</b></td>
			   <td class="normal" align="center"><b>Monto Sujeto</b></td>
			   <td class="normal" align="center"><b>Monto Exento</b></td>
			 </tr>
			 <tbody id="item2"></tbody>
			</table>
			</div></td>	
			</tr>
		    <tr>
			  <td>&nbsp;</td>
			</tr>
			<tr>
			 <td><div align="center" id="Boton" style="display: none">
			    <input type="button" value="Confirmar" onclick="javascript:add_opciones(),add_monto(),calcular_iva()"></div></td>
			</tr>
			<tr>
			  <td height="133">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tablaalertas" id="tbl_mod">
				<!--DWLayoutTable-->
				<tr class="td_gray">
					<td class="peqNegrita" colspan="2">Proyecto o Acci&oacute;n	Centralizada</td>
					<td><div align="center" class="peqNegrita">&nbsp;&nbsp;ACC.C/P.P</div></td>
					<td><div align="center" class="peqNegrita">&nbsp;&nbsp;ACC.ESP</div></td>
					<td><div align="center" class="peqNegrita">Dependencia</div></td>
					<td><div align="center" class="peqNegrita">Partida/Cuenta contable</div></td>
					<td><div align="center" class="peqNegrita">Denominaci&oacute;n</div></td>
					<td><div align="center" class="peqNegrita">&nbsp;&nbsp;Monto Sujeto</div></td>
					<td><div align="center" class="peqNegrita">&nbsp;&nbsp;&nbsp;&nbsp;Monto Exento</div></td>
					<td><div align="center" class="peqNegrita">&nbsp;&nbsp;Accion</div></td>
				</tr>
				<tbody id="item"></tbody>
				<tr>
					<td height="19" colspan="5">&nbsp;</td>
					<td width="57">&nbsp;</td>
					<td width="52"><!--DWLayoutEmptyCell-->&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
				    <td colspan="9">
					  <table width="600" align="center" class="tablaalertas" id="tbl_fact"> 
						<tr class="td_gray">
						  <td width="122" class="normalNegro" colspan="8" valign="middle">
        				  <a href="javascript:abrir_ventana('arbol_facturas.php?tipo=1&tipo_doc=1&id_p_c='+document.form.txt_cod_imputa.value+'&id_ac='+document.form.txt_cod_accion.value+'&arre='+document.form.hid_partida_actual.value,550)">
     	 				  <img src="../../imagenes/estadistic.gif" border="0" />Agregar</a>
 						  <div align="center" class="normalNegroNegrita">DETALLES DE LA(S) FACTURA(S)</div></td>
						</tr>
						<tr class="td_gray">
				  		  <td align="center" class="normalNegro">N&#176; Factura</span></div></td>
						  <td align="center" class="normalNegro">Fecha</span></div></td>
						  <td align="center" class="normalNegro">N&#176; Control</span></div></td>
						  <td align="center" class="normalNegro">&nbsp;&nbsp;Monto Base </div></td>
						  <td align="center" class="normalNegro">% IVA</span></div></td>
	    				  <td align="center" class="normalNegro">Monto IVA</span></div></td>
	    				  <td align="center" class="normalNegro">&nbsp;&nbsp;Accion </div> </td>
					   </tr>
					  <tbody id="ar_fact"></tbody>
					</table></td>
			   </tr>
			  <tr>
			   <td colspan="9">&nbsp;</td>
			  </tr>
				
				<tr>
					<td height="21" colspan="10" valign="middle" class="td_gray">
					<div align="left" class="normalNegroNegrita">
					<div align="center">DETALLES DEL IMPUESTO AL VALOR AGREGADO (IVA)</div></div></td>
				</tr>
				<tr>
				<td height="24" colspan="3" align="center" valign="top"	class="normalNegrita">Monto Base:
					<input name="txt_monto_subtotal" type="text" class="normalNegro" id="txt_monto_subtotal" value="0.00" size="20" maxlength="20" readonly="readonly" align="right">
				</td>
				<td height="24" colspan="3" align="center" valign="top"	class="normalNegrita">Monto Exento:
					<input name="txt_monto_subtotal_exento" type="text" class="normalNegro" id="txt_monto_subtotal_exento" value="0.00" size="20" maxlength="20" readonly="readonly" align="right">
				</td>
				<td align="left" valign="top" class="normalNegrita">&nbsp;&nbsp;Porcentaje:
				<span class="normal">
				<select name="opc_por_iva" id="opc_por_iva"	class="normalNegro" onChange="javacript:calcular_iva()">
				<?php for ($ii=0; $ii <$elem_impuesto; $ii++){?>
				  <option value="<?= $porce_impuesto[$ii]?>" <?php if($porce_impuesto[$ii]==0){echo "selected";}?> title="<?php echo $porce_impuesto[$ii]."% ". $impu_nombre[$ii];?>"><?= $porce_impuesto[$ii]?></option>
				<?php }?>
				</select></span></td>
				<td colspan="3" align="center" valign="top" class="normalNegrita"><div align="left">Monto IVA:
				<input name="txt_monto_iva_tt" type="text" class="normalNegro" id="txt_monto_iva_tt" value="0.00" size="25" maxlength="25" readonly="readonly" align="right"></div></td>
			</tr>
		</table>
	   </td>
	</tr>
	<tr>
	  <td height="21" colspan="9" valign="middle" class="td_gray"><div align="left" class="normalNegroNegrita">TOTAL A PAGAR</div></td>
	</tr>
	<tr class="normal">
		<td class="normal" align="left">En Bs.
		<input type="text" name="txt_monto_tot" id="txt_monto_tot" value="<?= (number_format($total_definitivo,2,'.',','))?>" size="15" readonly="readonly" class="normalNegro"/></td>
	</tr>
	<tr class="normal">
		<td class="normal" align="left">En Letras:
		<textarea name="txt_monto_letras" id="txt_monto_letras" rows="2" cols="70" class="normalNegro" readonly><?= $total_definitivo?></textarea></td>
	</tr>
	<script language="JavaScript" type="text/JavaScript" id="js1x1">
	    ver_monto_letra(<?= str_replace(",","", $total_definitivo)?>, 'txt_monto_letras','');
	    ver_monto_letra(<?= str_replace(",","", $total_definitivo)?>,'hid_monto_letras','');
	</script>
	<tr>
  	  <td height="18" colspan="3">
		<table width="420" align="center">
		<?php include("includes/respaldos.php");?>
		</table></td>
	</tr>
	<tr>
		<td height="18" colspan="3">&nbsp;</td>
	</tr>
	<tr>
	  <td height="18" colspan="3">
		<? include("documentos/opciones_1.php");?></td>
	</tr>
</table>
</form>
</body>
</html>
<?php pg_close($conexion);?>
