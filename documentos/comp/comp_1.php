<?
	ob_start();
	include(dirname(__FILE__).'/../../init.php');
	require_once("../../includes/conexion.php");
	 	  
	if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ) {
		   header('Location:../../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	}
	$mostrar=true;

	$user_perfil_id = $_SESSION['user_perfil_id'];
	$pres_anno = $_SESSION['an_o_presupuesto'];
	$pres_anno = 2014;
	//Buscar a cual grupo pertenece el perfil 	
	$sql = "SELECT * FROM sai_buscar_grupos_perfil('".$user_perfil_id."') as resultado(grupo_id int4)";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	while ($row = pg_fetch_array($resultado)) {
		$lista_grupos_usuario .= ",".trim($row["grupo_id"]);
	}
	//Borrar 1era coma
	$lista_grupos_usuario = "(".substr($lista_grupos_usuario,1).")";
	
	
	//Buscar cuales tipo de cadena (asunto del pcta) puede iniciar segun su grupo
	$sql_t = "SELECT DISTINCT wfca_tipo FROM sai_wfcadena WHERE docu_id='comp' AND wfgr_id IN ".$lista_grupos_usuario;
	$resultado_t = pg_query($conexion,$sql_t) or die("Error al mostrar");
	while ($row_t = pg_fetch_array($resultado_t)) {
		$lista_tipos_cadena .= ",".trim($row_t["wfca_tipo"]);
	}
	//Borrar 1era coma
	$lista_tipos_cadena = "(".substr($lista_tipos_cadena,1).")";

	//Se busca las direcciones de la Oficina de apoyo administrativo
    $total_depe=0;
	$sql="select * from sai_any_tabla ('sai_dependenci','depe_id,depe_nombre,depe_nombrecort','depe_id_sup=103') as result (depe_id varchar,depe_nombre varchar,depe_nombrecort varchar)";
	$resultado_set = pg_exec($conexion ,$sql);
	$valido=$resultado_set;
	if ($valido) {
	   $total_depe=pg_num_rows($resultado_set);
	   $depe_id=array($total_depe);
	   $depe_nombre=array($total_depe);
	   $depe_nombrecort=array($total_depe);
	   $i=0;
	   while($row=pg_fetch_array($resultado_set))
    	{
		$depe_id[$i]=trim($row['depe_id']);
		$depe_nombre[$i]=trim($row['depe_nombre']);
	    $depe_nombrecort[$i]=trim($row['depe_nombrecort']);
		$i++;
		}
	 }
?><script>
var listado_pcta = new Array();
var num_select;
var pcta_gerencia = new Array();
var nombres_partida = new Array();</script>
<?php 
      $documento = "pcta-%".substr($pres_anno,2,2);
  	  $sql = "SELECT pcta_id, 
					pcta_gerencia, 
					CAST(substr(pcta_id,6) as integer) 
			FROM sai_pcuenta p
			INNER JOIN sai_doc_genera d ON (pcta_id = docg_id) 
			WHERE wfob_id_ini = 99 AND 
				(LENGTH(perf_id_act)<2 OR perf_id_act IS NULL) AND
				p.esta_id!=15 AND
				docg_id LIKE '".$documento."' AND
				pcta_asunto!='013' AND
				pcta_asunto!='039' 
			ORDER BY 3";

  	  $resultado_set=pg_query($conexion,$sql) or die("Error al consultar los puntos de cuentas");
	  $j=0;  
	while($rowor=pg_fetch_array($resultado_set)) {
		 	$gerencia_id=$rowor['pcta_gerencia'];
		 	$id_pcta =  $rowor['pcta_id'];
		   	echo("
				<script language='javascript'>
				var registro = new Array(); 
				registro[0]='$id_pcta';
				registro[1]='$gerencia_id';
				pcta_gerencia[$j]=registro;
				</script>
				");
				$j++;
	}
   ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::Ingresar Compromiso</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript"> g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script language="javascript" src="../../js/func_montletra.js"></script>

<!-- 
<script src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>" type="text/javascript" charset="utf-8"></script>
<script src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/ui.min.js';?>" type="text/javascript" charset="utf-8"></script>
<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/themes/ui.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />
-->
	
<!-- JQuery -->
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/jquery-1.6.1.min.js';?>" charset="utf-8"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/jquery-ui-1.8.13.custom.min.js';?>" charset="utf-8"></script>
<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />

<!-- elRTE -->
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/elrte.min.js';?>" charset="utf-8"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/elRTE.options.js';?>" charset="utf-8"></script>
<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/elrte.min.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />
<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/elrte-inner.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />
<!-- elRTE translation messages -->

<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/i18n/elrte.es.js';?>" charset="utf-8"></script>
<script language="JavaScript" src="../../js/funciones_comp.js"> </script>
<script type="text/javascript" charset="utf-8">

   $().ready(function() {

	   var opts = {
				doctype  :	' <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">',
				cssClass : 'el-rte',
				lang     : 'es',
				height   : 400,
				toolbar  : 'maxi',
				cssfiles : ['js/editorlr/css/elrte-inner.css']
	
			}
		

		$('#pcuenta_descripcion').elrte(opts);

  });


</script>		









<script language="javascript">

$(document).ready(function(){
	$("#pcta_id").change(function(){
	  $.ajax({
	    data: {sendValue:$(this).val()},
	    type: "POST",
	    dataType: "json",
	    url: "genera-select.php",
	    success: function(json){
	     var index = 0;
		 $.each(json.listaPartida, function(idObjPartida, objPartida){
			var registro = new Array();
		    registro[0]=objPartida.id_pcta;
			registro[1]=objPartida.acc_esp;
			registro[2]=objPartida.acc_pp;
			registro[3]=objPartida.imputacion;
			registro[4]=objPartida.partida;					
			registro[5]=objPartida.titulo;
			registro[6]=objPartida.accion;
			registro[7]=objPartida.part_nombre;
			registro[8]=objPartida.gestor;
			registro[9]=objPartida.costo;
			registro[10]=objPartida.monto_pcta;
			registro[11]=objPartida.gerencia_id;
			registro[12]=objPartida.asunto_id;			
			registro[13]=objPartida.rif;		
			registro[14]=objPartida.asunto_desc;
			registro[15]=objPartida.gerencia_nombre;
			listado_pcta[index]=registro;
			index++;
			});  
		 consulta_presupuesto();
	    } 
	   });
	  });
	});


function verifica_partida()
{
 abrir_ventana('../../includes/arbolCategoria.php?dependencia=<?php echo $_SESSION['user_depe_id'];?>&campo_nom_supe=txt_nombre_imputa&campo_cod_supe=txt_cod_imputa&campo_nombre_accion=txt_nombre_accion&campo_cod_accion=txt_cod_accion&tipo=chk_tp_imputa&formulario=form1&tipo_docu=0&centrog=centro_gestor&centroc=centro_costo&opcion=pcta&codigo_origen=comp&campo_cod_supe2=txt_cod_imputa2&campo_cod_accion2=txt_cod_accion2');
}

var asuntos = new Array();
partidas = new Array();
monto_tot=new Array();
arreglo= new Array();
var monto_total=new Array();
	
function validarRif(rif){
	campo = rif.value;
	var ubicacion = '';
	var caracteres = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 :";
	for (var i=0; i < campo.length; i++) {
		ubicacion = campo.substring(i, i + 1);
		if (caracteres.indexOf(ubicacion) == -1) {
			rif.value = rif.value.replace(ubicacion, "");
		}
	}

	/*if (encuentra==0){
		//return false;
		alert("Este RIF indicado no es v"+aACUTE+"lido");
		document.form1.rif_sugerido.focus();
				
		}*/
}
</script>
</head>

<body><br/>
<form name="form1" method="post" action="comp_e1.php" id="form1" enctype="multipart/form-data" >
<table align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
  <tr class="td_gray"> 
	  <td colspan="2" class="normalNegroNegrita" align="center">INGRESAR COMPROMISO</td>
  </tr>
  <tr>
	<td class="normal"><strong>Fecha del compromiso:</strong></td>
	<td class="normal">
      <input type="text" size="10" id="fecha" name="fecha" class="dateparse" readonly="readonly" value="<?php echo $_POST["fecha"];?>"/>
  	  <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha');" title="Show popup calendar">
  	  <img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a></td>
  </tr>
  <tr>
	<td><div id="display"></div><div align="left" class="normal"><b>Elaborado Por:</b></div></td>
	<td><input type="text" name="pcuenta_remit" class="normalNegro" value="<? echo($_SESSION['solicitante']);?>" readonly="true"></td>
  </tr>
  <tr>
	<td class="normal" align="left"><strong>Unidad/Dependencia:</strong></td>
	<td>
	<?php
	  $sql_str="SELECT depe_id,depe_nombrecort,depe_nombre FROM sai_dependenci WHERE depe_nivel in (3,4) order by depe_nombre";
	  $res_q=pg_exec($sql_str);		  
	?>
      <select name="opt_depe" class="normalNegro" id="opt_depe" onchange="javascript:buscar_pcta()">
        <option value="">Seleccione...</option>
	    <?php while($depe_row=pg_fetch_array($res_q)){ ?>
        <option value="<?php echo(trim($depe_row['depe_id'])); ?>"><?php echo(trim($depe_row['depe_nombre'])); ?></option>
        <?php }?>
      </select></td>
  </tr>
  <tr>
	<td class="normal" align="left"><strong>Punto cuenta asociado: </strong></td>
	<td class="normalNegro">
	  <select name="pcta_id" id="pcta_id" class="normalNegro" >
		<option value="-1">Seleccione</option>
		<option value="<?php echo $rowor['pcta_id']?>">
		<option value="0">N/A</option>
	  </select></td>
  </tr>
  <tr>
    <td align="left" class="normal"><strong>Asunto:</strong></td>
	<td class="normal">
	  <select name="pcuenta_asunto" class="normalNegro" onchange="javascript:blanquear_campos(this)">
        <option value="">Seleccione...</option> 
        <?
	 	 $sql_asu = "SELECT * FROM sai_compromiso_asunt where esta_id=1 order by cpas_nombre";					
		 $result=pg_query($conexion,$sql_asu);
		 while($row=pg_fetch_array($result))
		 {
		  $docu_nombre=$row["cpas_nombre"]; 
		  $docu_id=$row["cpas_id"]; 
		 ?><script language="javascript">
			 var registro = new Array(2);
			 registro[0]='<?php echo ($docu_id); ?>'
			 registro[1]='<?php echo ($docu_nombre); ?>'
			 asuntos[asuntos.length]=registro;
			</script>
		 <? echo("<option value='$docu_id'>$docu_nombre</option>");
		  }?>
      </select><input name="nom_asunto" type="hidden" id="hid_nomape"/></td></tr>
  <tr>
  <tr>
    <td><div align="left" class="normal"><strong>Rif del Proveedor Sugerido:</strong></div></td>
	<td><input type="text" name="rif_sugerido" id="rif_sugerido" class="normalNegro" size="70" onkeyup="validarRif(this)">
	<?php 	
  	  $query ="SELECT prov_id_rif as id,prov_nombre as nombre 
  	  		   FROM sai_proveedor_nuevo 
			   WHERE prov_esta_id=1 
			   UNION
			   SELECT benvi_cedula as id, (benvi_nombres || ' ' || benvi_apellidos)  as nombre
			   FROM sai_viat_benef 
			   UNION
			   SELECT empl_cedula as id, (empl_nombres || ' ' || empl_apellidos) as nombre
			   FROM sai_empleado
			   ORDER BY 2";
		
		        $resultado = pg_exec($conexion, $query);
				$numeroFilas = pg_num_rows($resultado);
				$arregloProveedores = "";
				$cedulasProveedores = "";
				$nombresProveedores = "";
				$indice=0;
				while($row=pg_fetch_array($resultado)){
					$arregloProveedores .= "'".$row["id"]." : ".strtoupper(str_replace("\n"," ",$row["nombre"]))."',";
					$cedulasProveedores .= "'".$row["id"]."',";
					$nombresProveedores .= "'".str_replace("\n"," ",strtoupper($row["nombre"]))."',";
					$indice++;
				}
					$arregloProveedores = substr($arregloProveedores, 0, -1);
					$cedulasProveedores = substr($cedulasProveedores, 0, -1);
					$nombresProveedores = substr($nombresProveedores, 0, -1);
			?>
				<script>			
					var proveedor = new Array(<?= $arregloProveedores?>);
					var arreglo_rif = new Array(<?= $cedulasProveedores?>);
					var nombre_proveedor= new Array(<?= $nombresProveedores?>);
					actb(document.getElementById('rif_sugerido'),proveedor);
				</script></td>
  </tr>
  <tr>
  	<td align="left" class="normal"><strong>Tipo Actividad:</strong></td>
	<td><select name="tipo_act" class="normalNegro" >
          <option value="">Seleccione...</option>
          <?
			$sql_asu = "SELECT * FROM sai_tipo_actividad WHERE esta_id=1 order by nombre";					
			$result=pg_query($conexion,$sql_asu);
			while($row=pg_fetch_array($result))	{
		  ?> 
			<option value="<?php echo $row['id'];?>"><?php echo $row['nombre'];?></option>
		  <? } ?>
         </select></td></tr>
  <tr>
    <td align="left" class="normal"><strong>Tipo Evento:</strong></td>
	<td><select name="tipo_evento" class="normalNegro" >
          <option value="">Seleccione...</option>
          <?
			$sql_asu = "SELECT * FROM sai_tipo_evento order by nombre";					
			$result=pg_query($conexion,$sql_asu);
			while($row=pg_fetch_array($result))	{
		  ?> 
			<option value="<?php echo $row['id'];?>"><?php echo $row['nombre'];?></option>
		  <?}?>
        </select></td></tr>   
 
  <tr>
  	<td align="left" class="normal"><strong>C&oacute;digo Documento:</strong></td>
	<td><input type="text" name="documento" size="15" class="normalNegro"></input></td></tr>
  <tr>
  	<td align="left" class="normal"><strong>Control interno:</strong></td>
	<td><select name="control_interno" class="normalNegro" >
         <option value="">Seleccione...</option>
         <?
		  $sql_asu = "SELECT * FROM sai_control_comp WHERE esta_id=1 ORDER BY nombre ";					
		  $result=pg_query($conexion,$sql_asu);
		  while($row=pg_fetch_array($result))	{
		 ?> 
		 <option value="<?php echo $row['id'];?>"><?php echo $row['nombre'];?></option>
		<?}?>
        </select></td></tr>   
  <tr>
  	 <td align="left" class="normal"><strong>Descripci&oacute;n:</strong></td>
	 <td align="left" class="normal">
	 
	 
	 
	  <div id="pcuenta_descripcion" class="pcuenta_descripcion"><?php echo $descripcion ?></div>
	    <input type="hidden" name="pcuenta_descripcionVal" id="pcuenta_descripcionVal" value="">
	    
	    
	    
	    </td></tr>
	     <tr>
  	<td align="left" class="normal"><strong>Duraci&oacute;n de la actividad:</strong></td>
	<td>
	 <input type="text" size="10" id="fecha_i" name="fecha_i" class="dateparse" onfocus="javascript:comparar_fechas(this);" readonly="readonly" value="<?php echo $_POST["fecha"];?>"/>
  	 <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha_i');" title="Show popup calendar">
  	 <img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a>&nbsp; 
 	 <input type="text" size="10" id="fecha_f" name="fecha_f" class="dateparse" onfocus="javascript:comparar_fechas(this);" readonly="readonly" value="<?php echo $_POST["fecha"];?>"/>
  	 <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha_f');" title="Show popup calendar">
  	 <img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a></td>
  </tr>          
  <tr>
  	<td align="left" class="normal"><strong>Localidad:</strong></td>
	<td><select name="ubica_info" class="normalNegro" >
          <option value="0">Seleccione...</option>
          <?
			$sql_asu = "SELECT * FROM safi_edos_venezuela order by nombre";					
			$result=pg_query($conexion,$sql_asu);
			while($row=pg_fetch_array($result))	{
		  ?> 
			<option value="<?php echo $row['id'];?>"><?php echo $row['nombre'];?></option>
		  <? }?>
        </select></td></tr>
  <tr>
  	<td align="left" class="normal"><strong>Observaciones:</strong></td><?php $texto="*Infocentro:\n*Participantes:\n*Otras Observaciones:";?>
	<td><textarea rows="4" name="observaciones" cols="50"><?php echo $texto;?></textarea></td></tr>
  <tr>
	<td colspan="2">
	 <table align="center"  class="tablaalertas">
	   <tr class="td_gray">						
		<td><div align="center" class="peqNegrita" id="Categoria"  style="display:none">
			<a href="javascript:verifica_partida();">
			<img src="../../imagenes/estadistic.gif" width="24" height="24" border="0"  />Categor&iacute;a</a></div></td>
		<td><div align="center" class="peqNegrita">C&oacute;digo</div></td>
		<td width="100"><div align="center"><span class="peqNegrita">Denominaci&oacute;n</span></div></td>
	   </tr>
	   <tr>
		<td><div align="left"><input name="chk_tp_imputa" type="radio" class="peq" value="1" readonly="readonly"/>
			<span class="peqNegrita">Proyectos</span></div>		</td>
		<td rowspan="2">
		  <div align="center"><input name="txt_cod_imputa" type="hidden" id="txt_cod_imputa" value="" >
		  <input name="txt_cod_imputa2" type="text" class="normalNegro" id="txt_cod_imputa2" size="15" value="" readonly="readonly"></div>		</td>
		<td rowspan="2">
			<div align="center">
			<input name="txt_nombre_imputa" type="text" class="normalNegro" id="txt_nombre_imputa" size="40" value="" readonly="readonly"/>
			</div></td>
		</tr>
		<tr><td valign="top"><div align="left">
			<input name="chk_tp_imputa" type="radio" class="peq" value="0" readonly="readonly"/>
			<span class="peqNegrita">Acci&oacute;n Cent. </span></div>		</td>
		</tr>
		<tr>
		<td>
			<div align="left"><p><span class="peqNegrita">&nbsp;Acci&oacute;n Espec&iacute;fica</span></p></div>		</td>
		<td>
			<div align="center">
			<input name="txt_cod_accion" type="hidden" id="txt_cod_accion">
		    <input name="txt_cod_accion2" type="text" class="normalNegro" id="txt_cod_accion2" size="15" readonly>
			</div>		</td>
		<td>
			<div align="center">
			<input name="txt_nombre_accion" type="text" id="txt_nombre_accion" class="normalNegro" size="40" readonly="readonly"/>
			</div></td>
		</tr>
		<tr><td class="peqNegrita" align="left">
	   <input type="hidden" name="centro_gestor" id="centro_gestor" size="5" readonly="readonly" class="ptotal"/>
	   <input type="hidden" name="centro_costo" id="centro_costo" size="5" readonly="readonly" class="ptotal"/>
	   <input type="hidden" name="slc_prioridad" id="slc_prioridad" value="1"/></td>
     </tr>
	</table></td>
  </tr>
  <tr>
	<td colspan="2">
 	 <table  align="center" class="tablaalertas" id="tbl_part"> 
  	   <tr class="td_gray">
  		<td width="122" class="td_gray"><div align="center"><span class="normalNegrita">
    	<input type="hidden" name="hid_partida_actual" value=""/>
    	<input name="hid_largo" type="hidden" id="hid_largo"/>
    	<input name="hid_val" type="hidden" id="hid_val"/>		
		<input type="hidden" name="hid_acc_pp" value=""/>
		<input type="hidden" name="hid_acc_esp" value=""/>
		<div id="Partidas"  style="display:none">
   		<a href="javascript:abrir_ventana('arbol_partidas.php?anno_pres=<?php echo $pres_anno;?>&tipo=1&tipo_doc=1&gerencia='+document.form1.opt_depe.value+'&id_p_c='+document.form1.txt_cod_imputa.value+'&id_ac='+document.form1.txt_cod_accion.value+'&arre='+document.form1.hid_partida_actual.value,550)"><img src="../../imagenes/estadistic.gif" border="0" />Partida</a></div></span></div>		</td>
  		<td width="436" class="td_gray"><div align="center"><span class="peqNegrita">Denominaci&oacute;n</span></div></td>
  		<td width="132" class="td_gray"><div align="center"><span class="peqNegrita">Monto Pcta</span></div></td>
  		<td width="132" class="td_gray"><div align="center"><span class="peqNegrita">Monto Compromiso</span></div></td>
 	   </tr>
 		<tbody id="ar_body"></tbody>
	 </table>
	</td></tr>
  <tr>
	<td colspan="2">
  <tr>
	<td colspan="2" align="center"><input type="button" value="Confirmar" onclick="javascript:add_opciones('0')"></td>
  </tr>
  <tr>
	<td colspan="2" align="center">	
	 <table width="100%" align="center" border="0" cellpadding="0" cellspacing="3" class="tablaalertas" id="tbl_mod">
	  <tr class="td_gray">
		<td width="80" class="peqNegrita" align="center">Proyecto o Acci&oacute;n Centralizada </td>
		<td align="center" class="peqNegrita">&nbsp;&nbsp;ACC.C/P.P </td>
		<td align="center" class="peqNegrita">&nbsp;&nbsp;ACC.ESP </td>
		<td align="center" class="peqNegrita">Dependencia</td>
		<td align="center" class="peqNegrita"> Partida</td>
		<td align="center" class="peqNegrita">Denominaci&oacute;n</td>
		<td align="center" class="peqNegrita">&nbsp;&nbsp;Monto </td>
        <td colspan="2"><div align="center" class="peqNegrita">&nbsp;&nbsp;Accion </td>
        <td>&nbsp;</td>
	  </tr>
		<tbody id="item"></tbody>
	 </table></td></tr>
  <tr>
	<td align="left" class="normalNegrita" colspan="3"> TOTAL A SOLICITAR</td>
  </tr>
  <tr><td class="normal">En Bs.</td>
	  <td colspan="2">
		<input type="text" name="txt_monto_tot" size="15" readonly="readonly" class="normalNegro" value="0"/>
		<input type="hidden" name="txt_monto_tot2" value="0"/></td>
  </tr> 
  <tr>	 
	 <td class="normal" align="left">En Letras:</td>
	 <td colspan="2">
	   <textarea name="txt_monto_letras" id="txt_monto_letras" rows="2" cols="70" class="normalNegro" readonly="readonly"></textarea>
	   <script>ver_monto_letra(0, 'txt_monto_letras','');</script>
	  </td>
  </tr>
  <tr>
	<td colspan="3">
	<table align="center" class="tablaalertas"><tr><td>
	 <? include("../../includes/respaldos.php"); ?>
	</td></tr></table>
	</td>
  </tr>
  <tr>
	<td colspan="3"><div align="center">
	  <input type="button" value="Ingresar" onclick="javascript:revisar();"></div>
	  <input type="hidden" name="hid_depe_num" value="<?php echo $total_depe;?>"/>
	</td>
  </tr>
  </td></tr>
</table>
</form>
</body>
</html>