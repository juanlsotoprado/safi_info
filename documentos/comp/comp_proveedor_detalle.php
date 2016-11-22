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
  $press_anno = $_SESSION['an_o_presupuesto'];
  //$press_anno = 2014;
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
     (document.form.tipo_compromiso.value=='0') && 
	 (document.form.proyac.value=='0') && (document.form.rif_proveedor.value=='') && 
	 (document.form.estatus_compromiso.value=='0') && (document.form.txt_cod.value=='')  )	{
	alert("Debe indicar el tipo de b\u00fasqueda");
	return;
 }

 if ( ((document.form.txt_inicio.value=='')  && (document.form.hid_hasta_itin.value!='')) || ((document.form.txt_inicio.value!='')&& (document.form.hid_hasta_itin.value=='')))  {
	  alert ('Debe especificar el rango completo de fechas a buscar');
		return;
 }
 
 document.form.hid_validar.value=2;
 document.form.action="comp_proveedor_detalle.php";
 document.form.submit();
}


function ejecutar2() 
{ 
 if ((document.form.txt_inicio.value=='') && (document.form.hid_hasta_itin.value=='') &&
     (document.form.tipo_compromiso.value=='0') && 
	 (document.form.proyac.value=='0')&& (document.form.txt_cod.value==''))	{
	alert("Debe indicar el tipo de b\u00fasqueda");
	return;
 }

 if ( ((document.form.txt_inicio.value=='')  && (document.form.hid_hasta_itin.value!='')) || ((document.form.txt_inicio.value!='')&& (document.form.hid_hasta_itin.value=='')))  {
	  alert ('Debe especificar el rango completo de fechas a buscar');
		return;
 }
 
 document.form.hid_validar.value=2;
 document.form.action="comp_proveedor_detalleXLS.php";
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
<table width="635" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
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
	<td><span class="normalNegrita">
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
	<td class="normalNegrita" align="left">
            Tipo de actividad:
          </td>
	<td><span class="normalNegrita">
<select name='tipo_actividad' class="normalNegro">
<option value="--">Seleccione</option>
<?php
$sql = "select id, nombre from sai_tipo_actividad where esta_id=1  order by nombre";
	$resultado_set=pg_query($conexion,$sql) or die("Error al consultar los tipos de compromisos");  
	while($rowor=pg_fetch_array($resultado_set)) {
		?> 
		<option value="<?php echo $rowor['id']?>">
<?php echo $rowor['nombre']?></option>
		
		<?php		
	}
?>
</select>
	</span></td>
</tr>
  <tr>
	<td class="normalNegrita" align="left">Estatus del compromiso:</td>
	<td><span class="normalNegrita">
	  <select name='estatus_compromiso' class="normalNegro">
		<option value="0">Seleccione</option>
		<option value="N/A"><?php echo "N/A";?></option>
		<option value="Por Rendir"><?php echo "Por Rendir";?></option>
		<option value="Reportado"><?php echo "Reportado";?></option>
	  </select></span></td>
  </tr>
  
  <tr>
	<td class="normalNegrita" align="left">Proyecto/Acc espec&iacute;fica:</td>
	<td><span >
	  <select name='proyac' class="normalNegro">
		<option value="0" class="normal">Todos</option>
		<?php
		// $sql = "select acce_id as proyecto, aces_id as especifica, centro_gestor, centro_costo from sai_acce_esp where pres_anno=".$press_anno." union select proy_id as proyecto, paes_id as especifica, centro_gestor, centro_costo from sai_proy_a_esp where pres_anno=".$press_anno." order by centro_gestor, centro_costo ";
		  $sql = "select pres_anno, acce_id as proyecto, aces_id as especifica, centro_gestor, centro_costo from sai_acce_esp where pres_anno>2010 union select pres_anno, proy_id as proyecto, paes_id as especifica, centro_gestor, centro_costo from sai_proy_a_esp where pres_anno>2010 order by pres_anno DESC,centro_gestor, centro_costo ";  
	      $resultado_set=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");  
	      while($rowor=pg_fetch_array($resultado_set)) {	?> 
		<!--   <option value="<?php echo $rowor['proyecto'].":::".$rowor['especifica']?>"  class="normal"><?php echo $rowor['centro_gestor'].'/'.$rowor['centro_costo']?></option> -->
		  <option value="<?php echo $rowor['proyecto'].":::".$rowor['especifica']?>"  class="normal"><?php echo ($rowor['pres_anno'].'-'.$rowor['centro_gestor'].'/'.$rowor['centro_costo']);?></option>
		  
		  <?php } ?>
	  </select></span></td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">RIF o Nombre Proveedor:</td>
	<td><span class="normalNegrita"><input name="rif_proveedor" type="text" class="peq" id="rif_proveedor" value="" size="25" /> </span></td>
  </tr>
    <tr>
	<td class="normalNegrita" align="left">Palabra Clave:</td>
	<td><span class="normalNegrita"><input name="txt_clave" type="text" class="normalNegro" id="txt_clave" value="" size="25" />
	</span><span class="normal">(Incluida en la descripci&oacute;n del compromiso)</span></td>
  </tr>
</table>
<br></br>

<div align="center"><input type="button" value="Buscar" onclick="javascript:ejecutar();">&nbsp;&nbsp;
<input type="button" value="Exportar" onclick="javascript:ejecutar2();"></div>
</form>
<br></br>


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
    
if (strlen($_POST['txt_cod'])>5) {
	$wheretipo7 = " and sc.comp_id='".$_POST['txt_cod']."' ";
}
	
if (strlen($_POST['tipo_compromiso'])>2) {
	$wheretipo2 = " and sc.comp_asunto='".$_POST['tipo_compromiso']."' ";
}

if ($_POST['tipo_actividad']<>"--") {
	$wheretipo8 = " and sc.id_actividad='".$_POST['tipo_actividad']."' ";
}

if (strlen($_POST['proyac'])>8) {
	list( $proy, $especif ) = split( ':::', $_POST['proyac'] );
	$wheretipo3 = " and t1.comp_acc_pp='".$proy."' and comp_acc_esp='".$especif."' ";
}

if (strlen($_POST['rif_proveedor'])>2) {
	$wheretipo4 = " and (upper(sc.rif_sugerido) like upper('%".$_POST['rif_proveedor']."%') or upper(sc.beneficiario) like upper('%".$_POST['rif_proveedor']."%'))";
}

if (strlen($_POST['txt_clave'])>0) {
	$wheretipo5 = " and upper(sc.comp_descripcion) like '%".cadenaAMayusculas($_POST['txt_clave'])."%' ";
}

if ($_POST['estatus_compromiso']<>'0'){
	$wheretipo6 = " and sc.comp_estatus='".$_POST['estatus_compromiso']."' ";
}

if (strlen($fecha_ini)>3) {
	$wheretipo1 = "and to_date(to_char(sc.comp_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini."' and to_date(to_char(sc.comp_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fin."' ";
	$wheretipo9=" AND caus_fecha>='".$fecha_ini."' AND caus_fecha<='".$fecha_fin."' ";

	$comp_asociados="select t1.comp_id,max(t1.comp_fecha) as fecha_mod
from sai_comp_imputa_traza t1, sai_comp sc 
where t1.comp_id=sc.comp_id and 
to_date(to_char(t1.comp_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini."' and
to_date(to_char(t1.comp_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fin."' 
".$wheretipo2.$wheretipo3.$wheretipo5.$wheretipo6.$wheretipo7.$wheretipo8." group by t1.comp_id 
order by fecha_mod";


}else{
$comp_asociados=" 
select distinct(t1.comp_id) as comp_id,sc.comp_fecha as fecha_doc,
to_date(to_char(sc.comp_fecha, 'YYYY/MM/DD'),'YYYY/MM/DD') as fecha,sc.pcta_id as pcta,
coalesce(t8.centro_gestor, ' ') || coalesce(t9.centro_gestor, ' ') as centrogestor, to_char(sc.fecha_reporte, 'DD/MM/YYYY') as fecha_reporte,
coalesce(t8.centro_costo, ' ') || coalesce(t9.centro_costo, ' ') as centrocosto,sc.esta_id,0 as infocentro, '' as tipo_obra,comp_sub_espe,comp_monto as monto,comp_acc_pp,comp_acc_esp,
cpas_nombre as asunto,empl_nombres,empl_apellidos,t4.depe_nombre as dependencia,t5.nombre as actividad,comp_estatus,rif_sugerido,sc.esta_id,comp_observacion,comp_monto_solicitado,localidad,sc.comp_documento,sc.comp_descripcion,sc.beneficiario
,t6.nombre as evento,t7.nombre as control, to_date(to_char(sc.fecha_inicio, 'YYYY/MM/DD'),'YYYY/MM/DD') as fecha_inicio,to_date(to_char(sc.fecha_fin, 'YYYY/MM/DD'),'YYYY/MM/DD') as fecha_fin
from sai_comp_imputa t1 
left outer join sai_comp sc on (t1.comp_id=sc.comp_id ".$wheretipo4.")
left outer join sai_acce_esp t8 on (t1.comp_acc_pp=t8.acce_id and t1.comp_acc_esp=t8.aces_id and t8.pres_anno='".$press_anno."')
left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id and t10.pres_anno='".$press_anno."') 
left outer join sai_proy_a_esp t9 on(t1.comp_acc_pp=t9.proy_id and t1.comp_acc_esp=t9.paes_id and t9.pres_anno='".$press_anno."') 
left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id and pre_anno='".$press_anno."')
left outer join sai_compromiso_asunt t2 on (sc.comp_asunto=t2.cpas_id)
left outer join sai_empleado t3 on (sc.usua_login=t3.empl_cedula)
left outer join sai_dependenci t4 on (sc.comp_gerencia=t4.depe_id )
left outer join sai_tipo_actividad t5 on (t5.id=sc.id_actividad)
left outer join sai_tipo_evento t6 on (t6.id=sc.id_evento)
left outer join sai_control_comp t7 on (t7.id=sc.control_interno)
where sc.esta_id=10 ".$wheretipo2.$wheretipo3.$wheretipo5.$wheretipo6.$wheretipo7.$wheretipo8." 
order by comp_sub_espe";
}
//echo $comp_asociados."<br><br>";

$resultado_set_most_or=pg_query($conexion,$comp_asociados) or die("Error al consultar la descripcion del compromiso");  
?>
<table width="100%" border="0" align="center">
  <tr>
    <td width="495" height="27" class="normalNegro"><div align="center">Resultado de la b&uacute;squeda de compromisos </div></td>
  </tr>
</table>

<table width="100%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray">
	<td class="normalNegroNegrita" align="center">C&oacute;digo del Documento </td>
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
	<td class="normalNegroNegrita" align="center">Partida</td>
	<td class="normalNegroNegrita" align="center">Monto Solicitado</td>
	<td class="normalNegroNegrita" align="center">Descripci&oacute;n</td>
	<td class="normalNegroNegrita" align="center">Tipo Actividad</td>
	<td class="normalNegroNegrita" align="center">Tipo Evento</td>		
	<td class="normalNegroNegrita" align="center">Control Interno</td>
	<td class="normalNegroNegrita" align="center">Estado</td>
	<td class="normalNegroNegrita" align="center">Infocentro</td>
	<td class="normalNegroNegrita" align="center">N&deg; Participantes</td>
	<td class="normalNegroNegrita" align="center">Duraci&oacute;n de la actividad</td>
	<td class="normalNegroNegrita" align="center">Observaci&oacute;n</td>
	<td class="normalNegroNegrita"  align="center">Fecha de Reporte</td>
	<td class="normalNegroNegrita" align="center">Monto Causado</td>
	<td class="normalNegroNegrita" align="center">Sopg Causado</td>
	
  </tr>
  <?
  if (strlen($fecha_ini)<3) {
  while ($rowor=pg_fetch_array($resultado_set_most_or))  {
	$pcta=$rowor['pcta'];
	$comp=$rowor['comp_id'];

	 if ($rowor['localidad']>0){
	   $edo_vzla="select nombre from safi_edos_venezuela where id='".$rowor['localidad']."'";
	   $resultado_info=pg_exec($conexion,$edo_vzla);
	   if ($rowi=pg_fetch_array($resultado_info)){
	      $edo_id=$rowi['nombre'];
	   }
	  }else{$edo_id="N/A";}
	  $proveedor=explode(":",$rowor['rif_sugerido']);
      $rif=$rowor['rif_sugerido'];
	  $nombre=$rowor['beneficiario'];
	  $info_adicional=$rowor['comp_observacion'];
	  $longitud=strlen($info_adicional);
	  $info_adicional=substr($info_adicional,1,$longitud);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  
	  $infocentro=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
	  $info_adicional=substr($info_adicional,$posicion2+1);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  
	  $participante=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
	  $info_adicional=substr($info_adicional,$posicion2+1);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  $observacion=substr($info_adicional,$posicion+1);
	?>
	<tr class="normal">
	  <td align="center">
	  <a href="javascript:abrir_ventana('comp_detalle.php?codigo=<?php echo trim($rowor['comp_id']); ?>')" class="copyright"><?php echo $rowor['comp_id'] ;?></a></td>
	  <td align="center"><span class="peq"><?php echo cambia_esp(trim($rowor['fecha_doc']));?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['empl_nombres']." ".$rowor['empl_apellidos'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['dependencia'];?></span></td>
	  <td align="center"><span class="peq"><?php if ($rowor['pcta']=='0'){echo "N/A";}else{ echo $rowor['pcta'];}?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['asunto'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['comp_estatus'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['comp_documento'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $nombre;?></span></td>
	  <td align="center"><span class="peq"><?php echo $rif;?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['centrogestor'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['centrocosto'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['comp_sub_espe'];?></span></td>
	<?php 
	  $contador=0;
	  ?>
	  <td align="center"><span class="peq"><?php echo (number_format($rowor['monto'],2,',','.')); ?>
	  <td align="justify"><span class="peq"><?php echo $rowor['comp_descripcion'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['actividad'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['evento'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['control'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $edo_id;?></span></td>
	  <td align="center"><span class="peq"><?php echo $infocentro;?></span></td>
	  <td align="center"><span class="peq"><?php echo $participante;?></span></td>
	  <td align="center"><span class="peq"><?php echo cambia_esp($row['fecha_inicio'])."-".cambia_esp($row['fecha_fin']);?></span></td>
	  <td align="center"><span class="peq"><?php echo $observacion;?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['fecha_reporte'];?></span></td>
	  
	  <?php 
	  $monto_causado=0;
	  $anno_pres=$press_anno;
	  $sopgs_id="";
  		//MONTOS CAUSADOS
		$query_causado_sopg="SELECT ".
					"COALESCE(SUM(scd.cadt_monto),0) AS monto_causado,sopg_id  ".	
					"FROM sai_sol_pago sp ".
					"left outer join sai_causado sc on (sc.pres_anno = ".$anno_pres." AND sc.esta_id <> 15 AND sc.esta_id <> 2 AND sopg_id=caus_docu_id ".$wheretipo9.") ".
					"left outer join sai_causad_det scd on (sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno AND scd.cadt_id_p_ac = '".$rowor['comp_acc_pp']."' AND scd.cadt_cod_aesp = '".$rowor['comp_acc_esp']."' 
					AND scd.cadt_abono='1' AND scd.part_id NOT LIKE '4.11.0%' AND scd.part_id ='".$rowor['comp_sub_espe']."') ".
					"WHERE  sp.esta_id<>15 and comp_id='".$rowor['comp_id']."' GROUP BY sopg_id";
	//echo $query_causado_sopg;
        $resultadoMontosCausados=pg_query($query_causado_sopg) or die("Error en los montos causados sopg");
	    while ($row_causado=pg_fetch_array($resultadoMontosCausados)){
 	      $sopgs_id=  $sopgs_id." ".$row_causado['sopg_id'];	    	
	      $monto_causado=$monto_causado+$row_causado['monto_causado'];
	    }
		
		$query_causado_codi="SELECT monto_causado,documento FROM (
					SELECT ".
					"COALESCE(SUM(scd.cadt_monto),0) AS monto_causado,sci.comp_id as documento ".
					"FROM sai_codi sci ".	
					"left outer join sai_causado sc on (sc.pres_anno = ".$anno_pres." AND sc.esta_id <> 15 AND sc.esta_id <> 2 AND sci.comp_id=caus_docu_id ".$wheretipo9.") ".
					"left outer join sai_causad_det scd on (sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno AND scd.cadt_id_p_ac = '".$rowor['comp_acc_pp']."' AND scd.cadt_cod_aesp = '".$rowor['comp_acc_esp']."' AND 
					 scd.cadt_abono='1' AND scd.part_id NOT LIKE '4.11.0%' AND scd.part_id ='".$rowor['comp_sub_espe']."' ) ".
					" WHERE  sci.esta_id<>15  and nro_compromiso='".$rowor['comp_id']."' GROUP BY sci.comp_id
					) AS A WHERE monto_causado<>0";
		//echo $query_causado_codi;	
		$resultadoMontosCausadosCodi=pg_query($query_causado_codi) or die("Error en los montos causados codi");
	    while ($row_causadoCodi=pg_fetch_array($resultadoMontosCausadosCodi)){
	      $sopgs_id=  $sopgs_id." ".$row_causadoCodi['documento'];
       	  $monto_causado=$monto_causado+$row_causadoCodi['monto_causado'];
	    }
	  ?>
	  <td align="center"><span class="peq"><?php echo (number_format($monto_causado,2,',','.')); ?></span></td>
	  <td align="center"><span class="peq"><?php echo $sopgs_id; ?></span></td>
	  
	</tr>
	
	<?php 
   
  }//fin del while que obtiene los datos de la consulta
  }else{
  	
   while ($row=pg_fetch_array($resultado_set_most_or))  {
   	$fechacomp=$row['fecha_mod'];
   	$idcomp=$row['comp_id'];
   	$comp_asociados=" 
select distinct(t1.comp_id) as comp_id,sc.comp_fecha as fecha_doc,
to_date(to_char(sc.comp_fecha, 'YYYY/MM/DD'),'YYYY/MM/DD') as fecha,sc.pcta_id as pcta,
coalesce(t8.centro_gestor, ' ') || coalesce(t9.centro_gestor, ' ') as centrogestor, to_char(sc.fecha_reporte, 'DD/MM/YYYY') as fecha_reporte,
coalesce(t8.centro_costo, ' ') || coalesce(t9.centro_costo, ' ') as centrocosto,sc.esta_id,0 as infocentro, '' as tipo_obra,comp_sub_espe,comp_monto as monto,comp_acc_pp,comp_acc_esp,
cpas_nombre as asunto,empl_nombres,empl_apellidos,t4.depe_nombre as dependencia,t5.nombre as actividad,comp_estatus,rif_sugerido,sc.esta_id,comp_observacion,comp_monto_solicitado,localidad,sc.comp_documento,sc.comp_descripcion,t6.nombre as evento,
to_date(to_char(sc.fecha_inicio, 'YYYY/MM/DD'),'YYYY/MM/DD') as fecha_i,to_date(to_char(sc.fecha_fin, 'YYYY/MM/DD'),'YYYY/MM/DD') as fecha_f,sc.beneficiario
,t7.nombre as control
from sai_comp_imputa_traza t1 
left outer join sai_comp sc on (t1.comp_id=sc.comp_id ".$wheretipo4.")
left outer join sai_acce_esp t8 on (t1.comp_acc_pp=t8.acce_id and t1.comp_acc_esp=t8.aces_id and t8.pres_anno='".$press_anno."')
left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id and t10.pres_anno='".$press_anno."') 
left outer join sai_proy_a_esp t9 on(t1.comp_acc_pp=t9.proy_id and t1.comp_acc_esp=t9.paes_id and t9.pres_anno='".$press_anno."') 
left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id and pre_anno='".$press_anno."')
left outer join sai_compromiso_asunt t2 on (sc.comp_asunto=t2.cpas_id)
left outer join sai_empleado t3 on (sc.usua_login=t3.empl_cedula)
left outer join sai_dependenci t4 on (sc.comp_gerencia=t4.depe_id )
left outer join sai_tipo_actividad t5 on (t5.id=sc.id_actividad)
left outer join sai_tipo_evento t6 on (t6.id=sc.id_evento)
left outer join sai_control_comp t7 on (t7.id=sc.control_interno)
where sc.esta_id=10  and t1.comp_fecha='".$fechacomp."' and t1.comp_id='".$idcomp."'".$wheretipo1.$wheretipo2.$wheretipo3.$wheretipo5.$wheretipo6.$wheretipo7.$wheretipo8." 
order by comp_sub_espe";
   	 //echo $comp_asociados."<br>";
   	$resultado_comp_asociados=pg_query($conexion,$comp_asociados) or die("Error al consultar detalle del compromiso");
  
  while ($rowor=pg_fetch_array($resultado_comp_asociados))  {
   	
	$pcta=$rowor['pcta'];
	$comp=$rowor['comp_id'];

	 if ($rowor['localidad']>0){
	   $edo_vzla="select nombre from safi_edos_venezuela where id='".$rowor['localidad']."'";
	   $resultado_info=pg_exec($conexion,$edo_vzla);
	   if ($rowi=pg_fetch_array($resultado_info)){
	      $edo_id=$rowi['nombre'];
	   }
	  }else{$edo_id="N/A";}
	  $proveedor=explode(":",$rowor['rif_sugerido']);
      $rif=$rowor['rif_sugerido'];
	  $nombre=$rowor['beneficiario'];
	  $info_adicional=$rowor['comp_observacion'];
	  $longitud=strlen($info_adicional);
	  $info_adicional=substr($info_adicional,1,$longitud);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  
	  $infocentro=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
	  $info_adicional=substr($info_adicional,$posicion2+1);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  
	  $participante=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
	  $info_adicional=substr($info_adicional,$posicion2+1);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  $observacion=substr($info_adicional,$posicion+1);
	?>
	<tr class="normal">
	  <td align="center">
	  <a href="javascript:abrir_ventana('comp_detalle.php?codigo=<?php echo trim($rowor['comp_id']); ?>')" class="copyright"><?php echo $rowor['comp_id'] ;?></a></td>
	  <td align="center"><span class="peq"><?php echo cambia_esp(trim($rowor['fecha_doc']));?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['empl_nombres']." ".$rowor['empl_apellidos'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['dependencia'];?></span></td>
	  <td align="center"><span class="peq"><?php if ($rowor['pcta']=='0'){echo "N/A";}else{ echo $rowor['pcta'];}?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['asunto'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['comp_estatus'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['comp_documento'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $nombre;?></span></td>
	  <td align="center"><span class="peq"><?php echo $rif;?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['centrogestor'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['centrocosto'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['comp_sub_espe'];?></span></td>
	<?php 
	  $contador=0;
	  ?>
	  <td align="center"><span class="peq"><?php echo (number_format($rowor['monto'],2,',','.')); ?>
	  <td align="justify"><span class="peq"><?php echo $rowor['comp_descripcion'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['actividad'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['evento'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['control'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $edo_id;?></span></td>
	  <td align="center"><span class="peq"><?php echo $infocentro;?></span></td>
	  <td align="center"><span class="peq"><?php echo $participante;?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['fecha_i']."-".$rowor['fecha_f'];?></span></td>
	  <td align="center"><span class="peq"><?php echo $observacion;?></span></td>
	  <td align="center"><span class="peq"><?php echo $rowor['fecha_reporte'];?></span></td>
	  
	  <?php 
	  $monto_causado=0;
	  $anno_pres=$press_anno;
	  $sopgs_id="";
  		//MONTOS CAUSADOS
		$query_causado_sopg="SELECT ".
					"COALESCE(SUM(scd.cadt_monto),0) AS monto_causado,sopg_id  ".	
					"FROM sai_sol_pago sp ".
					"left outer join sai_causado sc on (sc.pres_anno = ".$anno_pres." AND sc.esta_id <> 15 AND sc.esta_id <> 2 AND sopg_id=caus_docu_id ".$wheretipo9." ) ".
					"left outer join sai_causad_det scd on (sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno AND scd.cadt_id_p_ac = '".$rowor['comp_acc_pp']."' AND scd.cadt_cod_aesp = '".$rowor['comp_acc_esp']."' 
					AND scd.cadt_abono='1' AND scd.part_id NOT LIKE '4.11.0%' AND scd.part_id ='".$rowor['comp_sub_espe']."') ".
					"WHERE  sp.esta_id<>15 and comp_id='".$rowor['comp_id']."' GROUP BY sopg_id ";
	
        $resultadoMontosCausados=pg_query($query_causado_sopg) or die("Error en los montos causados sopg");
	    while ($row_causado=pg_fetch_array($resultadoMontosCausados)){
	      $sopgs_id=  $sopgs_id." ".$row_causado['sopg_id'];	  
	      $monto_causado=$monto_causado+$row_causado['monto_causado'];
	    }
		
		$query_causado_codi="SELECT monto_causado,documento FROM (SELECT ".
					"COALESCE(SUM(scd.cadt_monto),0) AS monto_causado,sci.comp_id as documento ".
					"FROM sai_codi sci ".	
					"left outer join sai_causado sc on (sc.pres_anno = ".$anno_pres." AND sc.esta_id <> 15 AND sc.esta_id <> 2 AND sci.comp_id=caus_docu_id ".$wheretipo9." ) ".
					"left outer join sai_causad_det scd on (sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno AND scd.cadt_id_p_ac = '".$rowor['comp_acc_pp']."' AND scd.cadt_cod_aesp = '".$rowor['comp_acc_esp']."' AND 
					 scd.cadt_abono='1' AND scd.part_id NOT LIKE '4.11.0%' AND scd.part_id ='".$rowor['comp_sub_espe']."' ) ".
					" WHERE  sci.esta_id<>15  and nro_compromiso='".$rowor['comp_id']."' GROUP BY sci.comp_id
					) AS A WHERE monto_causado<>0";
			
		$resultadoMontosCausadosCodi=pg_query($query_causado_codi) or die("Error en los montos causados codi");
	    while ($row_causadoCodi=pg_fetch_array($resultadoMontosCausadosCodi)){
	      $sopgs_id=  $sopgs_id." ".$row_causadoCodi['documento'];
       	  $monto_causado=$monto_causado+$row_causadoCodi['monto_causado'];
	    }
	  ?>
	  <td align="center"><span class="peq"><?php echo (number_format($monto_causado,2,',','.')); ?></span></td>
	  <td align="center"><span class="peq"><?php echo $sopgs_id;?></span></td>
	</tr>
	
	<?php 
  }
  }//fin aki

  	
  	
  	
  	
  }
  
}
?> 
 </table> 
</form>
</body>
</html>