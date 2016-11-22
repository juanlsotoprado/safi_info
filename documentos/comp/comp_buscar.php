<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
require_once("../../includes/funciones.php");	 
require("../../includes/perfiles/constantesPerfiles.php");
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
		   header('Location:../../index.php',false);
	   	   ob_end_flush(); 	
		   exit;
}	ob_end_flush(); 
	  
$usuario = $_SESSION['login'];
$user_perfil_id = $_SESSION['user_perfil_id'];
$pres_anno=$_SESSION['an_o_presupuesto'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar Documentos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link type="text/css"
href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
	<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');
	</script>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script>

function ejecutar() { 
	
	if ((document.form.txt_inicio.value=='') && (document.form.hid_hasta_itin.value=='') 
			&& (document.form.txt_cod.value=='')  && (document.form.tipo_compromiso.value=='0') && 
			(document.form.proyac.value=='0') && (document.form.txt_partida.value==''))	{
		  alert("Debe indicar el tipo de b\u00fasqueda");
		  return;
	}
	
	document.form.hid_validar.value=2;
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
<form name="form" action="comp_buscar.php" method="post">
<input type="hidden" value="0" name="hid_validar" />
<?php
  $sql_perf_tmp="SELECT * FROM sai_buscar_cargo_depen('".$user_perfil_id."') as carg_nombre ";
  $resultado_perf_tmp=pg_query($conexion,$sql_perf_tmp) or die("Error al mostrar");
  $row_perf_tmp=pg_fetch_array($resultado_perf_tmp);
?>
<br />
<table width="555" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
	<td height="21" colspan="2" class="normalNegroNegrita" align="left">B&uacute;squeda de Compromisos</td>
  </tr>
  <tr>
    <td height="10" colspan="2"></td>
  </tr>
  <tr>
	<td width="175" height="29" class="normalNegrita" align="left">Solicitados entre:</td>
	<td>
	 <input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?php echo $_POST["txt_inicio"];?>"/>
	 <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar">
	 <img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a>
	 <input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?php echo $_POST["hid_hasta_itin"];?>"/>
	 <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
	 <img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a></td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">C&oacute;digo del documento:</td>
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
		while($rowor=pg_fetch_array($resultado_set)) {
		?> 
		<option value="<?php echo $rowor['cpas_id']?>">
		<?php echo $rowor['cpas_nombre']?></option>
		<?php }?>
	  </select></span></td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">Tipo de actividad:</td>
	<td><span class="normalNegrita">
	  <select name='tipo_actividad' class="normalNegro">
		<option value="--">Seleccione</option>
		<?php
		$sql = "select id, nombre from sai_tipo_actividad where esta_id=1 order by nombre";
		$resultado_set=pg_query($conexion,$sql) or die("Error al consultar los tipos de compromisos");  
		while($rowor=pg_fetch_array($resultado_set)) {
		?> 
		<option value="<?php echo $rowor['id']?>">
		<?php echo $rowor['nombre']?></option>
		<?php } ?>
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
	<td class="normalNegrita" align="left">Palabra Clave:</td>
	<td><span class="normalNegrita"><input name="txt_clave" type="text" class="normalNegro" id="txt_clave" value="" size="25" />
	</span><span class="normal">(Incluida en la descripci&oacute;n del compromiso)</span></td>
  </tr>
</table><br></br>
<div align="center">
<input type="button" value="Buscar" onclick="javascript:ejecutar();">
</div>

</form>
<br/>
<form name="form3" action="" method="post">
<?php 
if ($_POST['hid_validar']==2) {
	$fecha_in=trim($_POST['txt_inicio']);
	$fecha_fi=trim($_POST['hid_hasta_itin']); 
	$fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	$fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2);
	$fecha_ini2=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	$fecha_inis2=substr($fecha_in,6,4).substr($fecha_in,3,2).substr($fecha_in,0,2);
	$fecha_fin2=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2);
	$wheretipo1="";
	$wheretipo2="";
	$wheretipo3="";
	$wheretipo4="";
	$wheretipo5="";
	$wheretipo6="";
	$wheretipo7="";
	$wheretipo8="";

if (strlen($fecha_ini2)>2) {
	$wheretipo1 = "and to_date(to_char(sc.comp_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini2."' and to_date(to_char(sc.comp_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fin2."' ";
}

if (strlen($_POST['txt_cod'])>5) {
	$wheretipo2 = " and t7.comp_id='".$_POST['txt_cod']."' ";
}

if (strlen($_POST['proyac'])>8) {
	list( $proy, $especif ) = split( ':::', $_POST['proyac'] );
	$wheretipo4 = " and t7.comp_acc_pp='".$proy."' and comp_acc_esp='".$especif."' ";
}

if (strlen($_POST['txt_partida'])>2) {
	$wheretipo5 = " and t7.comp_sub_espe='".$_POST['txt_partida']."' ";
}

if (strlen($_POST['tipo_compromiso'])>2) {
	$wheretipo6 = " and sc.comp_asunto='".$_POST['tipo_compromiso']."' ";
}

if ($_POST['tipo_actividad']<>"--") {
	$wheretipo8 = " and sc.id_actividad='".$_POST['tipo_actividad']."' ";
}

if (strlen($_POST['txt_clave'])>0) {
	$wheretipo7 = " and upper(sc.comp_descripcion) like '%".cadenaAMayusculas($_POST['txt_clave'])."%' ";
}


$sql_or="select distinct t7.comp_id, to_char(sc.comp_fecha, 'DD/MM/YYYY') as comp_fecha, 
coalesce(t8.centro_gestor, ' ') || coalesce(t9.centro_gestor, ' ') as centrogestor, 
coalesce(t8.centro_costo, ' ') || coalesce(t9.centro_costo, ' ') as centrocosto,sc.esta_id 
from sai_comp_imputa t7 left outer join sai_comp sc on (t7.comp_id=sc.comp_id) 
left outer join sai_acce_esp t8 on (t7.comp_acc_pp=t8.acce_id and t7.comp_acc_esp=t8.aces_id )
left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id) 
left outer join sai_proy_a_esp t9 on(t7.comp_acc_pp=t9.proy_id and t7.comp_acc_esp=t9.paes_id) 
left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id ) 
where sc.esta_id<>2 ".$wheretipo1. $wheretipo2.$wheretipo4.$wheretipo5.$wheretipo6.$wheretipo7.$wheretipo8."
order by comp_fecha"; 

$resultado_set_most_or=pg_query($conexion,$sql_or) or die("Error al consultar la descripcion del compromiso");  
?>

<div align="center" class="normalNegrita">Resultado de la b&uacute;squeda de compromisos </div>
<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray">
	<td width="137" class="normalNegroNegrita" align="center">C&oacute;digo del Documento </td>
	<td width="102" class="normalNegroNegrita" align="center">Proy/Acc</td>
	<td width="102" class="normalNegroNegrita" align="center">Acc_Espec&iacute;fica</td>
	<td width="128" class="normalNegroNegrita" align="center">Fecha de la Solicitud </td>
 	<?php if (($_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_PRESUPUESTO)  || ($_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_PRESUPUESTO) || ($_SESSION['user_perfil_id'] ==PERFIL_JEFE_PRESUPUESTO)){ ?>
 	<td width="128" class="normalNegroNegrita" align="center">Opciones </td>
 	<?php }?>
  </tr>
  <?
   while ($rowor=pg_fetch_array($resultado_set_most_or)) 
   {
	$beneficiario_nombre= "";
	$beneficiario = "";
	$comp=$rowor['comp_id'];
	$sql="select * from sai_seleccionar_campo('sai_comp','pcta_id','comp_id=''$comp''','',2) resultado_set (pcta_id varchar)";
	$resultado=pg_query($conexion,$sql);
	if ($row=pg_fetch_array($resultado)){
	 $pcta=substr($row['pcta_id'],0,4);
	}?>
  <tr class="normal">
	<td height="28" align="center"><span class="link">
	<a href="javascript:abrir_ventana('comp_detalle.php?codigo=<?php echo trim($rowor['comp_id']); ?>&amp;esta_id=<?php echo($rowor['esta_id']);?>')" class="copyright"><?php echo $rowor['comp_id'] ;?></a></span></td>
	<td align="center" class="peq"><?php echo $rowor['centrogestor'];?></td>
	<td align="center" class="peq"><?php echo $rowor['centrocosto'];?></td>
	<td align="center" class="peq"><?php echo $rowor['comp_fecha'];?></td>
	<?php  if (($_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_PRESUPUESTO)  || ($_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_PRESUPUESTO) || ($_SESSION['user_perfil_id'] ==PERFIL_JEFE_PRESUPUESTO)){ ?>
	<td align="center" class="peq">
	<?php if($rowor['esta_id']==15) {?>
    <a href="comp_detalle.php?codigo=<?php echo trim($rowor['comp_id']);?>">Ver Detalle</a>
	<?php }else{?>
	<a href="comp_2.php?id=<?php echo trim($rowor['comp_id']);?>">Modificar/Anular/Reintegro Total</a>
	<?php }?>
	</td><?php }?>
  </tr>
  <?php	 
	}//fin del while que obtiene los datos de la consulta
  }?> 
 </table> 
</form>
</body>
</html>