<?php 
  ob_start();
  session_start();
  require_once("../../includes/conexion.php");
  require_once("../../includes/fechas.php");
  require_once("../../includes/funciones.php");
    
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush(); 	
	exit;
  }
  ob_end_flush(); 
  $usuario = $_SESSION['login'];
  $user_perfil_id = $_SESSION['user_perfil_id'];
  $pres_anno = $_SESSION['an_o_presupuesto'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar Compromisos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>

<script>
function detalle(codigo) {
    url="detalle.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}

function ejecutar() 
{ 
 if ((document.form.txt_inicio.value=='') && (document.form.hid_hasta_itin.value=='') &&
     (document.form.txt_cod.value=='')  && (document.form.tipo_compromiso.value=='0') && 
	 (document.form.proyac.value=='0') && (document.form.txt_partida.value==''))	{
	alert("Debe indicar el tipo de b\u00fasqueda");
	return;
 }

 if ( ((document.form.txt_inicio.value=='')  && (document.form.hid_hasta_itin.value!='')) || ((document.form.txt_inicio.value!='')&& (document.form.hid_hasta_itin.value=='')))  {
	  alert ('Debe especificar el rango completo de fechas a buscar');
		return;
 }
 
 document.form.hid_validar.value=2;
 document.form.action="comp_reporte.php";
 document.form.submit();
}


function ejecutar2() 
{ 
 if ((document.form.txt_inicio.value=='') && (document.form.hid_hasta_itin.value=='') &&
     (document.form.txt_cod.value=='')  && (document.form.tipo_compromiso.value=='0') && 
	 (document.form.proyac.value=='0') && (document.form.txt_partida.value==''))	{
	alert("Debe indicar el tipo de b\u00fasqueda");
	return;
 }

 if ( ((document.form.txt_inicio.value=='')  && (document.form.hid_hasta_itin.value!='')) || ((document.form.txt_inicio.value!='')&& (document.form.hid_hasta_itin.value=='')))  {
	  alert ('Debe especificar el rango completo de fechas a buscar');
		return;
 }
 
 document.form.hid_validar.value=2;
 document.form.action="comp_reporteXLS.php";
 document.form.submit();
}

function comparar_fechas(fecha_inicial,fecha_final) //Formato dd/mm/yyyy
{ 	
	var fecha_inicial=document.form.txt_inicio.value;
	var fecha_final=document.form.hid_hasta_itin.value;
		
	var dia1 =fecha_inicial.substring(0,2);
	var mes1 =fecha_inicial.substring(3,5);
	var anio1=fecha_inicial.substring(6,10);
	
	var dia2 =fecha_final.substring(0,2);
	var mes2 =fecha_final.substring(3,5);
	var anio2=fecha_final.substring(6,10);

	dia1 = parseInt(dia1,10);
	mes1 = parseInt(mes1,10);
	anio1= parseInt(anio1,10);

	dia2 = parseInt(dia2,10);
	mes2 = parseInt(mes2,10);
	anio2= parseInt(anio2,10); 
		
	if ( (anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || 
	 ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2)) )
	{
	  alert("La fecha inicial no debe ser mayor a la fecha final"); 
	  document.form.hid_hasta_itin.value='';
	  return;
	}
}
</script>
</head>
<body>
<form name="form"  method="post">
<input type="hidden" value="0" name="hid_validar" />
<table width="635" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
    <td height="21" colspan="2" class="normalNegroNegrita" align="left">Control Interno de Apartados y Compromisos</td>
  </tr>
  <tr>
    <td height="10" colspan="2"></td>
  </tr>
  <tr>
	<td width="175" height="29" class="normalNegrita" align="left">Modificados entre:</td>
	<td>
	 <input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly" value=""/>
	 <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar">
	 <img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a>
	 <input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly" value=""/>
	 <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
	 <img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a>	</td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">C&oacute;digo:</td>
	<td><span class="normalNegrita"><input name="txt_cod" type="text" class="peq" id="txt_cod" value="comp-" size="12" /></span></td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">Asunto del compromiso:</td>
	<td><span >
	  <select name='tipo_compromiso' class="normalNegro">
		<option value="0">Seleccione</option>
		<?php
		  $sql = "select cpas_id, cpas_nombre from sai_compromiso_asunt where esta_id=1 order by cpas_nombre";
		  $resultado_set=pg_query($conexion,$sql) or die("Error al consultar los tipos de compromisos");  
		  while($rowor=pg_fetch_array($resultado_set)) {?> 
		  <option value="<?php echo $rowor['cpas_id']?>"><?php echo $rowor['cpas_nombre']?></option>
		<?php }?>
	  </select></span></td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">Proyecto/Acc espec&iacute;fica:</td>
	<td><span class="normalNegrita">
	  <select name='proyac' >
		<option value="0" class="normal">Todos</option>
		<?php
		  $sql = "select acce_id as proyecto, aces_id as especifica, centro_gestor, centro_costo from sai_acce_esp where pres_anno=".$pres_anno." union select proy_id as proyecto, paes_id as especifica, centro_gestor, centro_costo from sai_proy_a_esp where pres_anno=".$pres_anno." order by centro_gestor, centro_costo ";
		  
	      $resultado_set=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");  
	      while($rowor=pg_fetch_array($resultado_set)) {	?> 
		  <option value="<?php echo $rowor['proyecto'].":::".$rowor['especifica']?>"  class="normal"><?php echo $rowor['centro_gestor'].'/'.$rowor['centro_costo']?></option>
		  <?php } ?>
	  </select></span></td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">Partida:</td>
	<td><span class="normalNegrita"><input name="txt_partida" type="text" class="peq" id="txt_partida" value="" size="25" /></span></td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">RIF o Nombre Proveedor:</td>
	<td><span class="normalNegrita"><input name="rif_proveedor" type="text" class="peq" id="rif_proveedor" value="" size="25" /> </span></td>
  </tr>
    <tr>
	<td class="normalNegrita" align="left">Palabra Clave:</td>
	<td><span class="normalNegrita"><input name="txt_clave" type="text" class="normalNegro" id="txt_clave" value="" size="25" />
	</span><span class="normal">(Incluida en la descripci&oacute;n del compromiso y punto de cuenta)</span></td>
  </tr>
</table>
<br></br>

<div align="center"><input type="button" value="Buscar" onclick="javascript:ejecutar();">&nbsp;&nbsp;<input type="button" value="Exportar" onclick="javascript:ejecutar2();"></div>
</form>
<br></br>
<div align="center" class="normalNegrita"><a href="comp_reporte_pcta.php">Compromisos provenientes de puntos de cuenta</a></div>
<br/>


<form name="form3" action="" method="post">
<?php 
if ($_POST['hid_validar']==2) {

	$fecha_ini=substr($_POST['txt_inicio'],6,4)."-".substr($_POST['txt_inicio'],3,2)."-".substr($_POST['txt_inicio'],0,2);
	$fecha_fin=substr($_POST['hid_hasta_itin'],6,4)."-".substr($_POST['hid_hasta_itin'],3,2)."-".substr($_POST['hid_hasta_itin'],0,2)." 23:59:59";

	$wheretipo1="";
	$wheretipo2="";
	$wheretipo3="";
	$wheretipo4="";
	$wheretipo5="";
	$wheretipo6="";
	$wheretipo7="";
	$wheretipo8="";
	$wheretipo9="";
	$wheretipo10="";
	$wheretipo11="";
	$wheretipo12="";
    $where1="";
    $where2="";
if (strlen($fecha_ini)>2) {
	$wheretipo1 = "and to_date(to_char(t1.comp_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini."' and to_date(to_char(t1.comp_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fin."' ";
	$wheretipo6 = "and to_date(to_char(t1.pcta_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini."' and to_date(to_char(t1.pcta_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fin."' ";
}

if (strlen($_POST['txt_cod'])>5) {
	$wheretipo2 = " and sc.comp_id='".$_POST['txt_cod']."' ";
}

if (strlen($_POST['proyac'])>8) {
	list( $proy, $especif ) = split( ':::', $_POST['proyac'] );
	$wheretipo3 = " and t1.comp_acc_pp='".$proy."' and comp_acc_esp='".$especif."' ";
	$wheretipo9 = " and t1.pcta_acc_pp='".$proy."' and pcta_acc_esp='".$especif."' ";
}

if (strlen($_POST['txt_partida'])>2) {
	$wheretipo4 = " and t1.comp_sub_espe='".$_POST['txt_partida']."' ";
	$wheretipo10 = " and t1.pcta_sub_espe='".$_POST['txt_partida']."' ";
}

if (strlen($_POST['tipo_compromiso'])>2) {
	$wheretipo5 = " and sc.comp_asunto='".$_POST['tipo_compromiso']."' ";
}

if (strlen($_POST['rif_proveedor'])>2) {
	$wheretipo7 = " and upper(pc.rif_sugerido) like upper('%".$_POST['rif_proveedor']."%') ";
	$wheretipo8 = " and upper(sc.rif_sugerido) like upper('%".$_POST['rif_proveedor']."%') ";
}

if (strlen($_POST['txt_clave'])>0) {
	$wheretipo11 = " and upper(pc.pcta_descripcion) like '%".cadenaAMayusculas($_POST['txt_clave'])."%' ";
	$wheretipo12 = " and upper(sc.comp_descripcion) like '%".cadenaAMayusculas($_POST['txt_clave'])."%' ";
}
//Deberìa separarse en Compromisos Aislados y no aislados
//los comp no aislados o asociados a pcta (sc.pcta_id=t1.pcta_id)


if ($wheretipo2<>""){
$comp_asociados=" select null as max_fecha, t1.comp_id as comp_id,t1.comp_fecha as fecha_doc,
to_date(to_char(t1.comp_fecha, 'YYYY/MM/DD'),'YYYY/MM/DD') as fecha,sc.pcta_id as pcta,
coalesce(t8.centro_gestor, ' ') || coalesce(t9.centro_gestor, ' ') as centrogestor, to_char(sc.fecha_reporte, 'DD/MM/YYYY') as fecha_reporte,
coalesce(t8.centro_costo, ' ') || coalesce(t9.centro_costo, ' ') as centrocosto,sc.esta_id,'N/A' as infocentro, '' as tipo_obra,
comp_sub_espe as partida,comp_monto as monto,localidad
from sai_comp_traza_reporte t1 
left outer join sai_comp sc on (t1.comp_id=sc.comp_id and sc.pcta_id=0)
left outer join sai_acce_esp t8 on (t1.comp_acc_pp=t8.acce_id and t1.comp_acc_esp=t8.aces_id and t8.pres_anno='".$pres_anno."')
left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id and t10.pres_anno='".$pres_anno."') 
left outer join sai_proy_a_esp t9 on(t1.comp_acc_pp=t9.proy_id and t1.comp_acc_esp=t9.paes_id and t9.pres_anno='".$pres_anno."') 
left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id and pre_anno='".$pres_anno."')
where sc.esta_id<>2 ".$wheretipo1.$wheretipo2.$wheretipo3.$wheretipo4.$wheretipo5.$wheretipo8.$wheretipo12." order by fecha";
	
}else{
	$comp_asociados="  select (t1.pcta_fecha) as max_fecha, t1.pcta_id as comp_id, to_date(to_char((t1.pcta_fecha), 'DD-MM-YYYY'), 'DD MM YYYY') as fecha_doc, to_date(to_char((t1.pcta_fecha), 'YYYY-MM-DD'), 'YYYY MM DD') as fecha, 
 t1.pcta_id as pcta,
coalesce(t8.centro_gestor, ' ') || coalesce(t9.centro_gestor, ' ') as centrogestor, '' as fecha_reporte,
coalesce(t8.centro_costo, ' ') || coalesce(t9.centro_costo, ' ') as centrocosto,pc.esta_id,infocentro_id as infocentro,tipo_obra,
pcta_sub_espe as partida,pcta_monto as monto,0 as localidad
from sai_pcta_imputa_traza t1 
left outer join sai_pcuenta pc on (pc.pcta_id=t1.pcta_id)
left outer join sai_acce_esp t8 on (t1.pcta_acc_pp=t8.acce_id and t1.pcta_acc_esp=t8.aces_id and t8.pres_anno='".$pres_anno."')
left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id and t10.pres_anno='".$pres_anno."') 
left outer join sai_proy_a_esp t9 on(t1.pcta_acc_pp=t9.proy_id and t1.pcta_acc_esp=t9.paes_id and t9.pres_anno='".$pres_anno."') 
left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id and pre_anno='".$pres_anno."')
where pc.esta_id<>2 ".$wheretipo10.$wheretipo9.$wheretipo6.$wheretipo7.$wheretipo11."
group by t1.pcta_id, t8.centro_gestor,t9.centro_gestor,t8.centro_costo,t9.centro_costo, pc.esta_id,infocentro_id, tipo_obra,pcta_sub_espe,pcta_monto,t1.pcta_fecha 

union

select null as max_fecha, t1.comp_id as comp_id,t1.comp_fecha as fecha_doc,
to_date(to_char(t1.comp_fecha, 'YYYY/MM/DD'),'YYYY/MM/DD') as fecha,sc.pcta_id as pcta,
coalesce(t8.centro_gestor, ' ') || coalesce(t9.centro_gestor, ' ') as centrogestor, to_char(sc.fecha_reporte, 'DD/MM/YYYY') as fecha_reporte,
coalesce(t8.centro_costo, ' ') || coalesce(t9.centro_costo, ' ') as centrocosto,sc.esta_id,'N/A' as infocentro, '' as tipo_obra,
comp_sub_espe as partida,comp_monto as monto,localidad
from sai_comp_traza_reporte t1 
left outer join sai_comp sc on (t1.comp_id=sc.comp_id and sc.pcta_id=0)
left outer join sai_acce_esp t8 on (t1.comp_acc_pp=t8.acce_id and t1.comp_acc_esp=t8.aces_id and t8.pres_anno='".$pres_anno."')
left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id and t10.pres_anno='".$pres_anno."') 
left outer join sai_proy_a_esp t9 on(t1.comp_acc_pp=t9.proy_id and t1.comp_acc_esp=t9.paes_id and t9.pres_anno='".$pres_anno."') 
left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id and pre_anno='".$pres_anno."')
where sc.esta_id<>2 ".$wheretipo1.$wheretipo2.$wheretipo3.$wheretipo4.$wheretipo5.$wheretipo8.$wheretipo12." order by fecha";
}
//echo $comp_asociados;
$resultado_set_most_or=pg_query($conexion,$comp_asociados) or die("Error al consultar la descripcion del compromiso");  
?>
<table width="100%" border="0" align="center">
  <tr>
    <td width="495" height="27" class="normalNegro"><div align="center">Resultado de la b&uacute;squeda de compromisos </div></td>
  </tr>
</table>

<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray">
	<td class="normalNegroNegrita" align="center">C&oacute;digo del Documento </td>
	<td class="normalNegroNegrita" align="center">Estatus Documento </td>
	<td class="normalNegroNegrita" align="center">Fecha</td>
	<td class="normalNegroNegrita" align="center">Elaborado Por</td>
	<td class="normalNegroNegrita" align="center">Unidad Solicitante</td>
	<td class="normalNegroNegrita" align="center">Punto de Cuenta</td>
	<td class="normalNegroNegrita" align="center">Asunto</td>
	<td class="normalNegroNegrita" align="center">Estatus</td>
	<td class="normalNegroNegrita" align="center">N&deg; Documento</td>
	<td class="normalNegroNegrita" align="center">Proveedor</td>
	<td class="normalNegroNegrita" align="center">CI/RIF</td>
	<td class="normalNegroNegrita" align="center">Centro Gestor</td>
	<td class="normalNegroNegrita" align="center">Centro Costo</td>
	<td class="normalNegroNegrita" align="center">Partidas</td>
	<td class="normalNegroNegrita" align="center">Monto Solicitado</td>
	<td class="normalNegroNegrita" align="center">Descripci&oacute;n</td>
	<td class="normalNegroNegrita" align="center">Tipo Evento</td>	
	<td class="normalNegroNegrita" align="center">Tipo Actividad</td>
	<td class="normalNegroNegrita" align="center">Estado</td>
	<td class="normalNegroNegrita" align="center">Infocentro</td>
	<td class="normalNegroNegrita" align="center">N&deg; Participantes</td>
	<td class="normalNegroNegrita" align="center">Duraci&oacute;n de la actividad</td>
	<td class="normalNegroNegrita" align="center">Observaci&oacute;n</td>
	<td class="normalNegroNegrita"  align="center">Fecha de Reporte</td>
  </tr>
  <?
  $imprimir="";
  while ($rowor=pg_fetch_array($resultado_set_most_or))  {
	$pcta=$rowor['pcta'];
	$comp=$rowor['comp_id'];
	$sql="select t1.*,cpas_nombre as asunto,empl_nombres,empl_apellidos,t5.depe_nombre as dependencia,t7.nombre as actividad,comp_estatus,rif_sugerido,t1.esta_id,comp_observacion,localidad,t8.nombre as evento
	 	  from sai_comp t1,sai_compromiso_asunt, sai_empleado t4,sai_dependenci t5,sai_tipo_actividad t7,sai_tipo_evento t8
		  WHERE comp_id='".$comp."' and comp_asunto=cpas_id and t1.usua_login=t4.empl_cedula and comp_gerencia=t5.depe_id and t7.id=id_actividad and t8.id=id_evento";
	$resultado=pg_query($conexion,$sql);
	if ($row=pg_fetch_array($resultado))
	{  
  
	  $proveedor=explode(":",$row['rif_sugerido']);
	  $rif=$row['rif_sugerido'];//$proveedor[0];
	  $nombre=$row['beneficiario'];//$proveedor[1];
	  $info_adicional=$row['comp_observacion'];
	  $longitud=strlen($info_adicional);
	  $info_adicional=substr($info_adicional,1,$longitud);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  
	  if ($row['localidad']>0){
	   $edo_vzla="select nombre from safi_edos_venezuela where id='".$rowor['localidad']."'";
	   $resultado_info=pg_exec($conexion,$edo_vzla);
	   if ($rowi=pg_fetch_array($resultado_info)){
	      $edo_id=$rowi['nombre'];
	   }
	  }else{$edo_id=" ";}
	  
	  $infocentro=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
	  $info_adicional=substr($info_adicional,$posicion2+1);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  
	  $participante=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
	  $info_adicional=substr($info_adicional,$posicion2+1);
	  $posicion = strpos($info_adicional, ":");
	  
	  $observacion=substr($info_adicional,$posicion+1);

	  }else{
	  	    $sql="select t1.*,pcas_nombre as asunto,empl_nombres,empl_apellidos,t5.depe_nombre as dependencia,
	  	    'N/A' as comp_estatus,rif_sugerido,t1.esta_id,pcta_observacion, 'Punto de Cuenta' as tipo_doc,
	  	     '".$pcta."' as comp_documento,tipo_obra as actividad,pcta_justificacion as comp_descripcion,esta_nombre
	 	    from sai_pcuenta t1,sai_pcta_asunt,sai_empleado t4,sai_dependenci t5,sai_estado t6
		     WHERE pcta_id='".$pcta."' and pcta_asunto=pcas_id and t1.usua_login=t4.empl_cedula and pcta_gerencia=t5.depe_id and t6.esta_id=t1.esta_id";
	  	    $resultado=pg_query($conexion,$sql);
	        if ($row=pg_fetch_array($resultado))
			{
			$rif=$row['rif_sugerido'];//$proveedor[0];
	  		$nombre=$row['beneficiario'];//$proveedor[1];
	  	    if($rowor['infocentro']<>''){
	 	      $sql_infocentro="select t1.nombre as nombre_info,t2.nombre as nombre_edo 
	 	      from safi_infocentro t1,safi_edos_venezuela t2 
	 	      where edo_id=t2.id and nemotecnico='".$rowor['infocentro']."'";
	  	      $resultado_info=pg_exec($conexion,$sql_infocentro);
	  	      if ($rowi=pg_fetch_array($resultado_info)){
	  	      $edo_id=$rowi['nombre_edo'];
	  	      $infocentro=$rowi['nombre_info'];
	  	     
	  	      }
	  	    }else{
	  	    	 $estado="N/A";
	  	         $infocentro="N/A";
	  	         $edo_id="-";
	  	    }
	  	     $participante="";
	  	     $observacion="";
	         }
	  }
	  
	 if ($rowor['pcta']=='0'){
	  $query_partidas="select comp_sub_espe as partida,comp_monto as monto from sai_comp_traza_reporte t1 where comp_id='".$rowor['comp_id']."' and comp_fecha like '".$rowor['fecha']."%' ".$wheretipo4." order by comp_fecha,comp_sub_espe";
	  $descripcion=$row['comp_descripcion'];
	 if ($row['esta_id']==15){$estado="Anulado";}else {$estado="Activo";}
	 }else{
	  $query_partidas="select pcta_sub_espe as partida,pcta_monto as monto from sai_pcta_imputa_traza t1 where pcta_id='".$rowor['pcta']."' and to_date(pcta_fecha, 'YYYY/MM/DD')='".$rowor['fecha']."' ".$wheretipo10." order by pcta_fecha,pcta_sub_espe";
	  $descripcion=$row['descripcion_presupuesto'];	
	  $estado=$row['esta_nombre'];
	 }

	?>
	<tr class="normal">
	  <td align="center"><?php echo $rowor['comp_id'];?></td>
	  <td align="center"><?php echo $estado;?></td>
	  <td align="center"><span class="peq"><?php echo cambia_esp(trim($rowor['fecha_doc']));?></span></td>
	  <td align="center"><span class="peq"><?php echo $row['empl_nombres']." ".$row['empl_apellidos'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $row['dependencia'];?></span></td>
	  <td align="center"><span class="peq"><?php if ($row['pcta_id']=='0'){echo "N/A";}else{ echo $row['pcta_id'];}?></span></td>
	  <td align="center"><span class="peq"><?php echo $row['asunto'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $row['comp_estatus'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $row['comp_documento'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $nombre;?></span></td>
	  <td align="center"><span class="peq"><?php echo $rif;?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['centrogestor'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['centrocosto'];?></span></td>
	<?php 
	  $contador=0;
	  ?>
	  <td align="center"><span class="peq"><?php echo $rowor['partida']; //echo $row_partidas['partida'];?></span></td>
	  <td align="center"><span class="peq"><?php echo (number_format($rowor['monto'],2,',','.')); //echo (number_format($row_partidas['monto'],2,',','.')); ?></span></td>
	  	  	  <?php 
  	  
	  if ($imprimir<>$rowor['comp_id']){?>
	  <td align="justify"><span class="peq"><?php echo $descripcion;?></span></td>
	  <td align="center"><span class="peq"><?php  echo $row['evento']; ?></span></td>
	  <td align="center"><span class="peq"><?php  echo $row['actividad']; ?></span></td>
	  <td align="center"><span class="peq"><?php echo $edo_id;?></span></td>
	  <td align="center"><span class="peq"><?php echo $infocentro;?></span></td>
	  <td align="center"><span class="peq"><?php echo $participante;?></span></td>
	  <td align="center"><span class="peq"><?php echo cambia_esp($row['fecha_inicio'])."-".cambia_esp($row['fecha_fin']);?></span></td>
	  <td align="center"><span class="peq"><?php echo $observacion;?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['fecha_reporte'];?></span></td>
     
      
      <?php 
	  $imprimir=$rowor['comp_id'];
	  }else{?>
	  <td align="justify"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  
	  <?php }?>
	</tr>
	
	<?php 

   
  }//fin del while que obtiene los datos de la consulta



}
?> 
 </table> 
</form>
</body>
</html>