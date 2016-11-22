<?php 

    ob_start();
	session_start();

		include(dirname(__FILE__).'/../../init.php');
    require_once("../../includes/conexion.php");
    include("../../includes/monto_a_letra.php");
	 	  
	if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ) {
		   header('Location:../../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	}
	$mostrar=true;
    $pres_anno=$_SESSION['an_o_presupuesto'];
    $pres_anno=2014;
    
	$user_perfil_id = $_SESSION['user_perfil_id'];
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

	$codigo= $_REQUEST['id'];

$sql = "select comp_descripcion, comp_asunto, comp_gerencia,  fecha_reporte,
comp_monto_solicitado, comp_justificacion, comp_observacion, to_char(comp_fecha, 'DD/MM/YYYY') as fecha, control_interno,
pcta_id ,rif_sugerido,usua_login,comp_lapso,comp_estatus,pcta_id,comp_documento,comp_tipo_doc,id_actividad,pcta_id,localidad,beneficiario,id_evento,fecha_inicio,fecha_fin
 from sai_comp where comp_id='".$codigo."'";
$result=pg_query($conexion,$sql);

if($row=pg_fetch_array($result)) {
	$descripcion_comp=$row["comp_descripcion"]; 
	$fecha=$row["fecha"];
	$asunto_id=trim($row["comp_asunto"]);
	$dependencia=trim($row['comp_gerencia']);
	if (strcmp($asunto_id,"026")==0)
	$monto=$row["comp_monto_solicitado"]*-1;
	else
    $monto=$row["comp_monto_solicitado"];
    $monto_total=$monto;
	$justificacion=trim($row['comp_justificacion']);
	$observaciones=trim($row['comp_observacion']);
	$pcta_id=trim($row['pcta_id']);
	$rif_sugerido=trim($row['rif_sugerido']);
	$nombre_sugerido=$row['beneficiario'];
	$elaborado_por=trim($row['usua_login']);
	$estatus=trim($row['comp_estatus']);
	$asunto_id2=trim($row["comp_asunto"]);
	$documento=$row['comp_documento'];
    $tipo_doc=$row['comp_tipo_doc'];
    $actividad=$row['id_actividad'];
    $id_evento=$row['id_evento'];
    $fecha_reporte=$row["fecha_reporte"];
    $ubicacion=$row["localidad"];
    $fecha_i=$row['fecha_inicio'];
    $fecha_f=$row['fecha_fin'];
    $control=$row['control_interno'];
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<script language="javascript" src="../../js/func_montletra.js"></script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript"> g_Calendar.setDateFormat('dd/mm/yyyy');	</script>

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
var asuntos = new Array();
partidas = new Array();
partidas_pcta = new Array();
monto_tot=new Array();
arreglo= new Array();
var monto_total=new Array();
var listado_pcta = new Array();
var contador_partidas=0;
var _chardecimal = '.';    //separador de la parte decimal


function inputFloat(e,minus){
    var menos = minus || false;
    if(e==null){
        e=event;
    }
    if(e==null){
        e=window.event;
    }

    var tecla = (document.all) ? e.keyCode : e.which;
    //48=0,57=9, 45=menos

    if(tecla==0 && !document.all)return true;//solo FF en keypress de flechas
    if(tecla==8)return true;//backs
    if(tecla==_chardecimal.charCodeAt(0)) return true; //punto decimal
    if (tecla==45){
        if (!menos){
            return false;
        }
    }else if(tecla < 48 || tecla > 57){
        return false;
    }
    return true;
}

function llenar_datos(campo) {
	if(campo.value!="") 	{
		for(i=0;i<asuntos.length;i++) 		{
			if(asuntos[i][0]==campo.value) 	{//alert(datos_persona[i][1])
				document.form1.nom_asunto.value=asuntos[i][1];
				break;
			}
		}
	}
	else 	{
		document.form1.nom_asunto.value="";
	}
		mostrar(campo);
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


function reintegrar(){
	document.form1.operacion.value="reintegrar";
	if(confirm("Est\u00E1 seguro que desea REINTEGRAR TOTALMENTE este compromiso?"))
	{
		document.form1.submit();
	}
}
function ejecutar() {
	document.form1.operacion.value="modificar";
	revisar();
}

function anular() {
	document.form1.operacion.value="anular";
	if(confirm("Est\u00E1 seguro que desea anular este compromiso?"))
	{
		contenido=prompt("Indique el motivo de la anulaci\u00F3n: ","");
		if (contenido!=null){
	  	 document.getElementById('contenido_memo').value=contenido;
	  	 document.form1.submit();
	 	}
		
	}
}
</script>

<?php 
if ($pcta<>"0"){//TIENE PCTA ASOCIADO, POR LO QUE SE BUSCA LAS PARTIDAS ASOCIADAS LA DISPONIBILIDAD
  $sql_pcta= " Select partida,monto,part_nombre from sai_disponibilidad_pcta t3,sai_partida t4 where pcta_id='".$pcta_id."' 
  and partida=t4.part_id and pres_anno='".$pres_anno."' and monto>0 and partida not in
 (select comp_sub_espe from sai_comp_imputa t2,sai_comp t1 where t1.comp_id=t2.comp_id and t1.pcta_id='".$pcta_id."' and t1.comp_id='".$codigo."')
 order by partida";
  $resultado_pcta=pg_query($conexion,$sql_pcta);

  	while($row=pg_fetch_array($resultado_pcta)) 
	{    
      echo("<script language='JavaScript' type='text/JavaScript'>"); ?>
	  var registro = new Array(3);
	  registro[0] = <?php echo("'".trim($row['partida'])."';"); ?>
	  registro[1] = <?php echo("'".trim($row['monto'])."';"); ?>
	  registro[2] = <?php echo("'".trim($row['part_nombre'])."';"); ?>
	  partidas_pcta[partidas_pcta.length]=registro;
	 <?php	 
	  echo('</script>'); 
	}	//Del IF rowsu
	
}


  $sql_pr= "Select sai_comp_imputa.comp_id,sai_comp_imputa.comp_acc_pp,sai_comp_imputa.comp_acc_esp,
			   sai_comp_imputa.depe_id, sai_comp_imputa.comp_sub_espe, sai_comp_imputa.comp_monto,
			   sai_comp_imputa.comp_tipo_impu as tipo
 			   From sai_comp_imputa,sai_comp
			   Where  trim(sai_comp_imputa.comp_id)=trim('".trim($_REQUEST['id'])."')
			   and   sai_comp_imputa.comp_id=sai_comp.comp_id order by comp_sub_espe";
  
	$resultado_set_most_pr=pg_query($conexion,$sql_pr);
	$valido=$resultado_set_most_pr;
	$i=0;
	$total_exento=0;
	while($rowp=pg_fetch_array($resultado_set_most_pr)) {
	$i=1;
	$subpartida=trim($rowp['comp_sub_espe']);  
	$submonto=trim($rowp['comp_monto']);
	$sopg_tp_imputacion=trim($rowp['tipo']);
	$pp=trim($rowp['comp_acc_pp']);
	$esp=trim($rowp['comp_acc_esp']);
	$parti=trim($rowp['comp_sub_espe']);
	$tp=trim($rowp['tipo']);
	
	$sql_pcta= " Select sum(monto) as monto,partida,part_nombre from sai_disponibilidad_pcta t3,sai_partida t4 where pcta_asociado='".$pcta_id."' 
  and partida=t4.part_id and pres_anno='".$pres_anno."' and partida='".$parti."' group by partida,part_nombre";
  $resultado_pcta=pg_query($conexion,$sql_pcta);
 	while($row=pg_fetch_array($resultado_pcta)) 
	{    
      echo("<script language='JavaScript' type='text/JavaScript'>"); ?>
	  var registro = new Array(3);
	  registro[0] = <?php echo("'".trim($row['partida'])."';"); ?>
	  registro[1] = <?php echo("'".($row['monto']+$submonto)."';"); ?>
	  registro[2] = <?php echo("'".trim($row['part_nombre'])."';"); ?>
	  partidas_pcta[partidas_pcta.length]=registro;
	 <?php	 
	  echo('</script>'); 
	}
	
	$sql_su="SELECT * FROM sai_seleccionar_campo('sai_partida','part_nombre','part_id='||'''$subpartida''','',2) 
	resultado_set(part_nombre varchar)";
	$monto=0; 
	$resultado_set_most_su=pg_query($conexion,$sql_su) or die("Error al consultar partida");
	if($rowsu=pg_fetch_array($resultado_set_most_su))
	{    
		if(strcmp($asunto_id,"026")==0) $monto=trim($rowp['comp_monto'])*-1;
		else $monto=trim($rowp['comp_monto']);
			$part_nombre=trim($rowsu['part_nombre']);
			echo("<script language='JavaScript' type='text/JavaScript'>"); ?>
			var registro = new Array(6);
			registro[0] = <?php echo("'".trim($rowp['tipo'])."';"); ?>
			registro[1] = <?php echo("'".trim($rowp['comp_acc_pp'])."';"); ?>
			registro[2] = <?php echo("'".trim($rowp['comp_acc_esp'])."';"); ?>
			registro[3] = <?php echo("'".trim($rowp['depe_id'])."';"); ?>
			registro[4] = <?php echo("'".$subpartida."';"); ?>
			registro[5] = <?php echo("'".$part_nombre."';"); ?>
			registro[6] = <?php echo("'".$monto."';"); ?>
			partidas[partidas.length]=registro;
			arreglo[arreglo.length]=registro[4];
			<?php	 
			echo('</script>'); 
			
		}	//Del IF rowsu
	
	 	 $matriz_imputacion[$i]=trim($rowp['tipo']);
		 $matriz_acc_pp[$i]=trim($rowp['comp_acc_pp']); // proy o acc
		 $matriz_acc_esp[$i]=trim($rowp['comp_acc_esp']); // acc esp
		 $matriz_sub_esp[$i]=trim($rowp['comp_sub_espe']); //sub-part
		 $matriz_uel[$i]=trim($rowp['depe_id']); //depe
		 $matriz_monto[$i]=trim($rowp['comp_monto']); //monto
		 $i++;
	} //Del While

if ($i>0) {
	 
$total_imputacion=$i;	

$sql="select * from sai_consulta_proyecto_accion('". $matriz_acc_pp[($i-1)] ."','".$matriz_acc_esp[($i-1)]."','".$matriz_imputacion[($i-1)]."',".$pres_anno.") as resultado (nombre varchar, especifica varchar, cg varchar, cc varchar)"; 
$resultado_set = pg_exec($conexion ,$sql) or die("Error al visualizar datos");
if ($resultado_set)
 {
  $valido=true;
  $row_impu = pg_fetch_array($resultado_set,0); 
  $p_a_impu_nomb=$row_impu['nombre'];
  $a_esp_impu_nomb=$row_impu['especifica'];
  $sopg_imp_p_c =$matriz_acc_pp[($i-1)];  
  $sopg_imputa=$matriz_acc_esp[($i-1)];
  $centrog=$row_impu['cg'];
  $centroc=$row_impu['cc'];
 }
}
?>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />	
</head>
<body onload="inicial();">
<form name="form1" method="post" action="comp_e2.php" id="form1" enctype="multipart/form-data" >
<input type="hidden" name="contenido_memo" id="contenido_memo">	
<input type="hidden" value="0" name="reintegro">
<table align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
  <tr class="td_gray"> 
	<td colspan="2" class="normalNegroNegrita" align="center">MODIFICAR COMPROMISO <?php echo $codigo;?></td>
  </tr>
  <tr>
	<td class="normal"><strong>Fecha del compromiso:</strong> </td>
	<td><input type="text" size="10" id="fecha" name="fecha" class="dateparse" readonly="readonly" value="<?php echo $fecha;?>"/>
	<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha');" title="Show popup calendar">
	<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a></td>
  </tr>
  <tr>
	<td><div align="left" class="normal"><b>Elaborado Por:</b></div></td>
	<td class="normalNegro"><input type="text" name="pcuenta_remit" value="<? echo($elaborado_por);?>" readonly="true"></td>
  </tr>
  <tr>
	<td class="normal" align="left"><strong>Punto cuenta asociado:</strong></td>
	<td class="normalNegro">
	<select name="pcta_id" id="pcta_id" class="normalNegro">
	<?php if (($pcta_id<>'0') && ($pcta_id<>'')){?>
	<option value="<?php echo $pcta_id;?>"><?php echo $pcta_id;?></option>
	<?php }else{?>
	<option value="0">N/A</option>
	<?php }?>
	</select></td>
  </tr>
  <tr>
	<td class="normal" align="left"><strong>Unidad/Dependencia:</strong></td>
	<td>
		<?php
		  $sql_str="SELECT depe_id,depe_nombrecort,depe_nombre FROM sai_dependenci WHERE depe_nivel in (3,4) order by depe_nombre";
		  $res_q=pg_exec($sql_str);		  
	    ?>
          <select class="normalNegro" name="opt_depe"  id="opt_depe">
	    <?php while($depe_row=pg_fetch_array($res_q)){
	      $seleccion=""; ?>
	    <?php if($dependencia==trim($depe_row['depe_id'])){ $seleccion="selected"; } else {$seleccion="";}?>             
	      <option value="<?php echo(trim($depe_row['depe_id'])); ?>" <?php echo $seleccion;?>><?php echo(trim($depe_row['depe_nombre'])); ?></option>
        <?php }?>
          </select></td>
  </tr>
  <tr>
	<td align="left" class="normal"><strong>Asunto:</strong></td>
	<td>
	<select  class="normalNegro" name="pcuenta_asunto" onchange="javascript: llenar_datos(this)">
    <?
	 $sql_asu = "SELECT * FROM sai_compromiso_asunt WHERE esta_id<>15 order by cpas_nombre";					
	 $result=pg_query($conexion,$sql_asu);
	 while($row=pg_fetch_array($result))	{
	  $seleccion="";
	  $docu_nombre=$row["cpas_nombre"]; 
	  $docu_id=$row["cpas_id"]; 
	 ?> 
	 <script language="javascript">
		var registro = new Array(2);
	    registro[0]='<?php echo ($docu_id); ?>'
		registro[1]='<?php echo ($docu_nombre); ?>'
		asuntos[asuntos.length]=registro;
	</script>
	<?php if($asunto_id2==$docu_id){ $seleccion="selected"; } else {$seleccion="";}?>       
	<? echo("<option value='$docu_id' $seleccion>$docu_nombre</option>");}?>
    </select><input name="nom_asunto" type="hidden" id="hid_nomape"/></td></tr>
  <tr>
  <tr>
	<td><div align="left" class="normal"><strong>Rif del Proveedor Sugerido:</strong></div></Td>
	<td class="normalNegro">
	 <input type="text" class="normalNegro" name="rif_sugerido"  id="rif_sugerido" value="<?php echo $rif_sugerido." : ".$nombre_sugerido; ?>" size="70" onChange="validarRif(this)">
	 <?php 	
	   $query ="SELECT prov_id_rif as id,prov_nombre as nombre 
			    FROM sai_proveedor_nuevo 
			    WHERE prov_esta_id=1 
			    UNION
				SELECT benvi_cedula as id, (benvi_nombres || ' ' || benvi_apellidos)  as nombre
				FROM sai_viat_benef WHERE benvi_esta_id=1
				UNION
				SELECT empl_cedula as id, (empl_nombres || ' ' || empl_apellidos) as nombre
				FROM sai_empleado WHERE esta_id=1
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
	<td><select name="tipo_act"  class="normalNegro">
        <option value="">Seleccione...</option>
        <? $sql_asu = "SELECT * FROM sai_tipo_actividad WHERE esta_id=1 order by nombre";					
		   $result=pg_query($conexion,$sql_asu);
		   while($row=pg_fetch_array($result))	{
			if ($row['id']==$actividad){?> 
			<option value="<?php echo $row['id'];?>" selected><?php echo $row['nombre'];?></option>
			<?}else{?>
			<option value="<?php echo $row['id'];?>"><?php echo $row['nombre'];?></option>
			<? }}?>
          </select></td></tr>
  <tr>
	<td align="left" class="normal"><strong>Tipo Evento:</strong></td>
	<td><select name="tipo_evento"  class="normalNegro">
        <option value="">Seleccione...</option>
        <? $sql_asu = "SELECT * FROM sai_tipo_evento order by nombre";					
		   $result=pg_query($conexion,$sql_asu);
		   while($row=pg_fetch_array($result))	{
			if ($row['id']==$id_evento){?> 
			<option value="<?php echo $row['id'];?>" selected><?php echo $row['nombre'];?></option>
			<?}else{?>
			<option value="<?php echo $row['id'];?>"><?php echo $row['nombre'];?></option>
			<? }}?>
          </select></td></tr>
  <tr>
	<td class="normal"><strong>Duraci&oacute;n de la actividad:</strong> </td>
	<td><input type="text" size="10" id="fecha_i" name="fecha_i" class="dateparse" readonly="readonly" value="<?php echo $fecha_i;?>"/>
	<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha_i');" title="Show popup calendar">
	<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a>&nbsp;
	<input type="text" size="10" id="fecha_f" name="fecha_f" class="dateparse" readonly="readonly" value="<?php echo $fecha_f;?>"/>
	<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha_f');" title="Show popup calendar">
	<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a></td>
  </tr>      
  <tr>
	<td align="left" class="normal"><strong>C&oacute;digo Documento:</strong></td>
	<td><input type="text" name="documento" size="15" class="normalNegro" value="<?php echo $documento;?>"></input></td></tr>	
  <tr>
  	<td align="left" class="normal"><strong>Estatus:</strong></td>
	<td> <select name="estatus"  class="normalNegro">
    <?
	  if ($estatus=="Por Rendir"){?> 
	    <option value="Por Rendir" selected><?php echo "Por Rendir";?></option>
		<option value="Reportado"><?php echo "Reportado";?></option>
		<option value="N/A"><?php echo "N/A";?></option>
	<?}else{
	    if ($estatus=="Reportado"){?>
		<option value="Reportado" selected><?php echo "Reportado";?></option>
		<option value="Por Rendir"><?php echo "Por Rendir";?></option>
		<option value="N/A" ><?php echo "N/A";?></option>
		<? }else{?>
		<option value="N/A" selected><?php echo "N/A";?></option>
		<option value="Reportado" ><?php echo "Reportado";?></option>
		<option value="Por Rendir"><?php echo "Por Rendir";?></option>
		<?php }}	?>
        </select> </td></tr>	
  <tr>
	<td class="normal"><strong>Fecha de reporte:</strong> </td>
	<td><input type="text" size="10" id="fecha_reporte" name="fecha_reporte" class="dateparse" readonly="readonly" value="<?php echo $fecha_reporte;?>"/>
	<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha_reporte');" title="Show popup calendar">
	<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
    </a></td>
  </tr>
  <tr>
	<td align="left" class="normal"><strong>Control interno:</strong></td>
	<td><select name="control_interno"  class="normalNegro">
        <option value="">Seleccione...</option>
        <? $sql_asu = "SELECT * FROM sai_control_comp order by nombre";					
		   $result=pg_query($conexion,$sql_asu);
		   while($row=pg_fetch_array($result))	{
			 if ($row['id']==$control){?> 
			 <option value="<?php echo $row['id'];?>" selected><?php echo $row['nombre'];?></option>
			 <?}else{?>
			 <option value="<?php echo $row['id'];?>"><?php echo $row['nombre'];?></option>
			<? }}?>
          </select></td></tr>	
	<td align="left" class="normal"><strong>Descripci&oacute;n:</strong></td>
	<td align="left" class="normal" >
	  	
	  	
	  	
	  	<div id="pcuenta_descripcion" class="pcuenta_descripcion"><?php echo $descripcion_comp ?></div>
	    <input type="hidden" name="pcuenta_descripcionVal" id="pcuenta_descripcionVal" value="">
	  	
	  	

	  	</td>
  </tr>
  <tr>
	<td align="left" class="normal"><strong>Localidad:</strong></td>
	<td><select name="localidad" class="normalNegro" >
        <option value="0">Seleccione...</option>
        <? $sql_asu = "SELECT * FROM safi_edos_venezuela order by nombre";					
		   $result=pg_query($conexion,$sql_asu);
		   while($row=pg_fetch_array($result))	{
			if ($ubicacion==$row['id']){
		?>
		<option value="<?php echo $row['id'];?>" selected><?php echo $row['nombre'];?></option>
		<?php }else{?> 
		<option value="<?php echo $row['id'];?>"><?php echo $row['nombre'];?></option>
		<? }}?>
          </select></td></tr>
  <tr>
	<td><div align="left" class="normal"><strong>Observaciones:</strong></div></TD>
	<td class="normalNegro"><textarea rows="3" name="observaciones" cols="50"><?echo $observaciones;?></textarea></td></tr>
  <tr>
	<td colspan="2">
	  <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="tablaalertas">
		<tr class="td_gray">
		  <td><div align="center" class="peqNegrita" id="Categoria"  style="display:none">Categor&iacute;a</div></td>
		  <td><div align="center" class="normalNegrita">C&oacute;digo</div></td>
		  <td><div align="center"><span class="normalNegrita">Denominaci&oacute;n</span></div></td>
		</tr>
		<tr>
		  <td><div align="left"><input name="chk_tp_imputa" type="radio" class="peqNegrita_naranja" readonly="readonly"  value="1" <?php if($sopg_tp_imputacion==1) echo "checked";?>/>
			<span class="normalNegrita">Proyectos</span></div></td>
		  <td rowspan="2"><div align="center">
		    <input name="txt_cod_imputa" type="hidden" class="normalNegro" id="txt_cod_imputa" size="15" value="<?php echo $sopg_imp_p_c;?>" readonly="readonly"/>
			<input name="txt_cod_imputa2" type="text" class="normalNegro" id="txt_cod_imputa2" size="15" value="<?php echo $centrog;?>" readonly="readonly"/></div></td>
  		  <td rowspan="2">
			<div align="center"><input name="txt_nombre_imputa" type="text" class="normalNegro" id="txt_nombre_imputa" size="70" readonly="readonly" value="<?php echo $p_a_impu_nomb;?>"/></div></td>
	    </tr>
		<tr>
		  <td valign="top"><div align="left"><input name="chk_tp_imputa" type="radio" class="peqNegrita_naranja" value="0" <?php if($sopg_tp_imputacion==0) echo "checked";?> readonly="readonly"/>
			<span class="normalNegrita">Acc. Central</span></div></td>
		</tr>
		<tr>
		  <td><div align="left"><p><span class="normalNegrita">&nbsp;Acci&oacute;n Espec&iacute;fica</span></p></div>		</td>
		  <td><div align="center">
			<input name="txt_cod_accion" type="hidden" class="normalNegro" id="txt_cod_accion" size="15" readonly="readonly" value="<?php echo $sopg_imputa;?>"/>
			<input name="txt_cod_accion2" type="text" class="normalNegro" id="txt_cod_accion2" size="15" readonly value="<?php echo $centroc;?>"></div></td>
		  <td><div align="center"><input name="txt_nombre_accion" type="text" class="normalNegro" id="txt_nombre_accion" size="70" readonly="readonly" value="<?php echo $a_esp_impu_nomb;?>"/><input type="hidden" name="centro_gestor" id="centro_gestor"/><input type="hidden" name="centro_costo" id="centro_costo"/></div>		</td>
	   </tr>
      </table></td></tr>
  <tr>
  	<td colspan="2" height="10">&nbsp;</td></tr>
  <tr>
    <td colspan="2">
	 <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tablaalertas" id="tbl_part">
      <tr class="td_gray">
  		<td width="122" class="td_gray"><div align="center"><span class="normalNegrita">
    	<input type="hidden" name="hid_partida_actual" value=""/>
    	<input name="hid_largo" type="hidden" id="hid_largo"/>
    	<input name="hid_val" type="hidden" id="hid_val"/>		
		<input type="hidden" name="hid_acc_pp" value=""/>
		<input type="hidden" name="hid_acc_esp" value=""/>
		<div id="Partidas"  style="display:none"></div>
		<?php if ($pcta_id<>"0"){?>
   		<img src="../../imagenes/estadistic.gif" border="0" />Partida</span></div>		</td>
   		<?php }else {?>
   		<a href="javascript:abrir_ventana('../../documentos/comp/arbol_partidas.php?tipo=1&tipo_doc=1&gerencia='+document.form1.opt_depe.value+'&id_p_c='+document.form1.txt_cod_imputa.value+'&id_ac='+document.form1.txt_cod_accion.value+'&arre='+document.form1.hid_partida_actual.value,550)"><img src="../../imagenes/estadistic.gif" border="0" />Partida</a></span></div>		</td>
   		<?}?>
  		<td width="436" class="td_gray"><div align="center"><span class="peqNegrita">Denominaci&oacute;n</span></div></td>
  		<td width="132" class="td_gray"><div align="center"><span class="peqNegrita">Monto Pcta</span></div></td>
  		<td width="132" class="td_gray" colspan="2"><div align="center"><span class="peqNegrita">Monto Compromiso</span></div></td>
 	  </tr>
 	  <tbody id="ar_body"></tbody>
	</table>
   </td></tr>
  <tr>
	<td colspan="2" align="center"><input type="button" value="Confirmar" onclick="javascript:add_opciones('2')"></td></tr>
  <tr>
	<td colspan="2">
	 <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tablaalertas" id="tbl_mod">
	  <tr class="td_gray">
		<td height="22" colspan="2"><div align="center" class="normalNegrita"> ACC.C/PP</div></td>
		<td width="133" class="normalNegrita"><div align="center">Acci&oacute;n Especifica</div></td>
		<td width="113"><div align="center" class="normalNegrita">Dependencia</div></td>
		<td width="76"><div align="center" class="normalNegrita"> Partida</div></td>
		<td width="196"><div align="center" class="normalNegrita">Denominaci&oacute;n</div></td>
		<td width="110"><div align="center" class="normalNegrita">Monto</div></td>
		<td width="110"><div align="center" class="normalNegrita">Acci&oacute;n</div></td>
	  </tr>
		<tbody id="item"></tbody>
	  <tr>
		<td colspan="9">&nbsp;</td>
	  </tr>
	</table>
 	</td></tr>
  <tr> 
    <td  colspan="2" class="td_gray"><div align="left" class="normalNegrita"> TOTAL A SOLICITAR</div></td>
  </tr>
  <tr class="normal">
	<td colspan="2">En Bs.
	  <input type="text" name="txt_monto_tot" size="15" readonly="readonly" class="normalNegro" value=""/><? //echo $monto_total;?>
	  <input type="hidden" name="txt_monto_tot2" value=""/><? //echo $monto_total;?>
  	  <input type="hidden" name="login_destino" value="<?=$comp_destino?>"/></td> 
  </tr>
  <tr class="normal">
	<td colspan="2" align="left">En Letras:
	 <textarea name="txt_monto_letras" id="txt_monto_letras" rows="2" cols="70" class="normalNegro" readonly="readonly"></textarea></td>
  </tr>
  <tr><td colspan="2">&nbsp;</td></tr>
  <tr>
  	<td colspan="2"><? include("../../includes/respaldos.php");	?></td></tr>
  <tr>
	<td height="18">&nbsp;</td>
    <td height="18">&nbsp;</td>
  </tr>
  <tr> 
   	<td height="18">&nbsp;</td>
   	<td><input name="cod" type="hidden" id="cod" value="<? echo $codigo; ?>"/></td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="center">
      <input type="hidden" value="" name="operacion" id="operacion"/>
      <input type="hidden" value="<?php echo $codigo;?>" name="comp_id" id="comp_id"/>
      <input type="button" value="Modificar" onclick="javascript:ejecutar();">
      <input type="button" value="Anular" onclick="javascript:anular();">
      <input type="button" value="Reintegro Total" onclick="javascript:reintegrar();"></td>
  </tr>
  <tr> 
   	<td height="18"></td>
   	<td><input name="cod" type="hidden" id="cod" value="<? echo $codigo; ?>"></td>
  </tr>
  <tr height="10"><td></td></tr>
</table>
<input name="cod" type="hidden" id="cod" value="<? echo $codigo; ?>"/>
<input name="id" type="hidden" id="cod" value="<? echo $codigo; ?>"/>
<input name="pcta_id" type="hidden" id="pcta_id" value="<? echo $pcta_id; ?>"/>
<input name="asunto_id2" type="hidden" id="asunto_id2" value="<? echo $asunto_id2; ?>"/>
<input type="hidden" name="hid_depe_num" value="<?php echo $total_depe;?>"/>
<input type="hidden" name="hid_asunto_id" value="<?php echo $asunto_id;?>"/>
<input type="hidden" name="slc_prioridad" id="slc_prioridad" value="2"/>
<input type="hidden" name="pp" value="<?=$pp?>"/>
<input type="hidden" name="esp" value="<?=$esp?>"/>
<input type="hidden" name="parti" value="<?=$parti?>"/>
<input type="hidden" name="tp" value="<?=$tp?>"/>
</form>
</body>
</html>
