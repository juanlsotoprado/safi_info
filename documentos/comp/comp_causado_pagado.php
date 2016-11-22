<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
require_once("../../includes/funciones.php");	 
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
		   header('Location:../../index.php',false);
	   	   ob_end_flush(); 	
		   exit;
}	ob_end_flush(); 
	  
$usuario = $_SESSION['login'];
$user_perfil_id = $_SESSION['user_perfil_id'];
if(
	!isset($_POST["hid_hasta_itin"])
	|| $_POST["hid_hasta_itin"] == null
	|| ($fecha_hasta=trim($_POST["hid_hasta_itin"])) == ''
	|| strlen($fecha_hasta) != 10
){
	$anno_pres = $_SESSION['an_o_presupuesto'];
} else {
	$anno_pres = substr($fecha_hasta, 6, 4);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar Documentos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
g_Calendar.setDateFormat('dd/mm/yyyy');
</script>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script>
function ejecutar() 
{ 
	if ((document.form.hid_hasta_itin.value=='') || (document.form.tipo_reporte.value=='0') )	{
		  alert("Debe indicar el tipo de b\u00fasqueda");
		  return;
	}
	
	document.form.hid_validar.value=2;
	document.form.submit();
}
</script>
</head>
<body>
<form name="form" action="comp_causado_pagado.php" method="post">
  <div align="center">
  <input type="hidden" value="0" name="hid_validar" />
<?php
  $sql_perf_tmp="SELECT * FROM sai_buscar_cargo_depen('".$user_perfil_id."') as carg_nombre ";
  $resultado_perf_tmp=pg_query($conexion,$sql_perf_tmp) or die("Error al mostrar");
  $row_perf_tmp=pg_fetch_array($resultado_perf_tmp);
?>
  <br /></div>
<table width="555" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
	<td height="21" colspan="2" class="normalNegroNegrita" align="left">B&uacute;squeda de Compromisos</td>
  </tr>
  <tr>
	<td height="10" colspan="2"></td>
  </tr>
  <tr>
	<td width="175" height="29" class="normalNegrita" align="left">Fecha de Cierre:</td>
	<td>
	 <input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse" readonly="readonly" value="<?php echo $_POST["hid_hasta_itin"];?>"/>
	 <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
	<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a>	</td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">Tipo de Reporte:</td>
	<td><span class="normalNegrita">
	 <select name="tipo_reporte" id="tipo_reporte" class="normalNegro">
		<option value="0">Seleccione</option>
		<option value="1"><?php echo "NO causados NI pagados";?></option>
		<option value="2"><?php echo "causados y NO pagados";?></option>
	 </select></span></td>
  </tr>
</table><br></br>
<div align="center"><input type="button" value="Buscar" onclick="javascript:ejecutar();"></div>
</form>
<br/>
<form name="form3" action="" method="post">
<?php 
if ($_POST['hid_validar']==2) {
	$fecha_fi=trim($_POST['hid_hasta_itin']); 
	$fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2);
	$fecha_fin2=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2);
	$fecha_ini2=substr($fecha_fi,6,4)."-01-01";
	$ano2=substr($fecha_fin,2,2);
	
	$wheretipo1="";
	$wheretipo2="";
	$wheretipo3="";
	$wheretipo4="";
	$wheretipo5="";
	$wheretipo6="";
	$wheretipo7="";

if (strlen($fecha_fin2)>2) {
	$wheretipo1 = "and to_date(to_char(sc.comp_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini2."' and to_date(to_char(sc.comp_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fin2."' ";
}

if ($_POST['tipo_reporte']==1) {
	$wheretipo2 = " and t7.comp_id not in (select comp_id from sai_sol_pago where sopg_id like '%".$ano2."') and t7.comp_id not in (select nro_compromiso from sai_codi where comp_id like '%".$ano2."') ";
$sql_or="select distinct t7.comp_id, to_char(sc.comp_fecha, 'DD/MM/YYYY') as comp_fecha, 
coalesce(t8.centro_gestor, ' ') || coalesce(t9.centro_gestor, ' ') as centrogestor, 
coalesce(t8.centro_costo, ' ') || coalesce(t9.centro_costo, ' ') as centrocosto,sc.esta_id ,sc.comp_fecha as fecha,comp_monto_solicitado
from sai_comp_imputa t7 left outer join sai_comp sc on (t7.comp_id=sc.comp_id) 
left outer join sai_acce_esp t8 on (t7.comp_acc_pp=t8.acce_id and t7.comp_acc_esp=t8.aces_id )
left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id) 
left outer join sai_proy_a_esp t9 on(t7.comp_acc_pp=t9.proy_id and t7.comp_acc_esp=t9.paes_id) 
left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id ) 
where sc.esta_id<>2 and sc.esta_id<>15 ".$wheretipo1. $wheretipo2."
order by 3,4,sc.comp_fecha"; 

}else{
	 $ao_sopg="sopg-%".substr($anno_pres,2,2);
	 $ao_comp="comp-%".substr($anno_pres,2,2);
	 
$sql_or=" select compromiso.comp_id,compromiso.centrogestor,compromiso.centrocosto,compromiso.comp_monto_solicitado,sum(compromiso.monto_sopg) as causado,compromiso.comp_fecha 
from
(select sc.comp_id ,coalesce(sae.centro_gestor, ' ') || coalesce(spa.centro_gestor, ' ') as centrogestor, 
coalesce(sae.centro_costo, ' ') || coalesce(spa.centro_costo, ' ') as centrocosto,comp_monto_solicitado,sp.sopg_monto as monto_sopg, to_char(sc.comp_fecha, 'DD/MM/YYYY') as comp_fecha
from sai_sol_pago_imputa spi
left outer join sai_sol_pago sp on (spi.sopg_id=sp.sopg_id and sp.sopg_id like '".$ao_sopg."'  and sp.comp_id like '".$ao_comp."')
left outer join sai_doc_genera sdg on (sdg.docg_id=sp.sopg_id and sdg.esta_id=39 )
left outer join sai_acce_esp sae on (spi.sopg_acc_pp=sae.acce_id and spi.sopg_acc_esp=sae.aces_id )
left outer join sai_ac_central sac on(sae.acce_id=sac.acce_id) 
left outer join sai_proy_a_esp spa on(spi.sopg_acc_pp=spa.proy_id and spi.sopg_acc_esp=spa.paes_id) 
left outer join sai_proyecto spr on (spa.proy_id=spr.proy_id ) 
,sai_comp sc
where sc.comp_id=sp.comp_id and sdg.docg_id=sp.sopg_id and sdg.esta_id=39 
group by sc.comp_id,centrogestor,centrocosto,comp_monto_solicitado,monto_sopg,comp_fecha
) as compromiso
group by compromiso.comp_id,compromiso.centrogestor,compromiso.centrocosto,compromiso.comp_monto_solicitado,compromiso.comp_fecha 
";
 }
$resultado_set_most_or=pg_query($conexion,$sql_or) or die("Error al consultar la descripcion del compromiso");  
?>
<div align="center" class="normalNegrita">Resultado de la b&uacute;squeda de compromisos </div>
<table width="60%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray">
	<td width="137" class="normalNegroNegrita" align="center">C&oacute;digo del Documento </td>
	<td width="102" class="normalNegroNegrita" align="center">Proy/Acc</td>
	<td width="102" class="normalNegroNegrita" align="center">Acc_Espec&iacute;fica</td>
	<td width="102" class="normalNegroNegrita" align="center">Monto Compromiso Bs.</td>
	<?php if ($_POST['tipo_reporte']==2) {?>
	<td width="102" class="normalNegroNegrita" align="center">Monto Causado Bs.</td>
	<?php }?>
 	<td width="128" class="normalNegroNegrita" align="center">Fecha de la Solicitud </td>
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
	<td height="28" align="center"><span class="link"><a href="javascript:abrir_ventana('comp_detalle.php?codigo=<?php echo trim($rowor['comp_id']); ?>&amp;esta_id=<?php echo($rowor['esta_id']);?>')" class="copyright"><?php echo $rowor['comp_id'] ;?></a></span></td>
	<td align="center" class="peq"><?php echo $rowor['centrogestor'];?></td>
	<td align="center" class="peq"><?php echo $rowor['centrocosto'];?></td>
	<td align="center" class="peq"><?php echo (number_format($rowor['comp_monto_solicitado'],2,',','.'));?></td>
	<?php if ($_POST['tipo_reporte']==2) {?>
	<td width="102" class="peq" align="center">
	<a target="_blank" href="detalleCausadoComp.php?comp=<?=$rowor['comp_id']?>&aopres=<?=$anno_pres?>&monto=<?=$causado?>"><?php echo (number_format($rowor['causado'],2,',','.'));?></a></td>
   <?php }?>
	<td align="center" class="peq"><?php echo $rowor['comp_fecha'];?></td>
  </tr>
<?php	 
}//fin del while que obtiene los datos de la consulta
}
?> 
 </table> 
</form>
</body>
</html>