<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado"))  {
	ob_end_flush();    
	exit;
	}
ob_end_flush(); 

$perfil = $_SESSION['user_perfil_id'];
$sql = " SELECT * FROM sai_permiso_reporte('cont_docu','$perfil') as resultado ";
$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
if ($row = pg_fetch_array($resultado)) {
	$tiene_permiso = $row["resultado"];
}

if ($tiene_permiso == 0) {
?>
	<script>
		document.location.href = "../../mensaje.php?pag=principal.php";
	</script>
	<?
		header('Location:index.php',false);	
	}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Codas anulados</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script language="javascript">
function comparar_fechas(fecha_inicial,fecha_final) { 	
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
<br/>
<form name="form" action="codaAnulados.php" method="post">
<table width="50%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
  <td colspan="4" class="normalNegroNegrita">CODAS ANULADOS</td>
</tr>
<tr>
	<td width="100" height="29" class="normalNegrita" align="left">Elaborados entre:</td>
	<td width="304" class="normalNegrita" colspan="2">
<input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse"
onfocus="javascript: comparar_fechas(this);" readonly="readonly" value=""/>
<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar">
<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
</a>
<input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse"
onfocus="javascript: comparar_fechas(this);" readonly="readonly" value=""/>
<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
</a>	</td></td>
</tr>
<tr><td colspan="4" class="normal"><center>
<input type="hidden" name="hid_validar" value="2"><input type="button" value="Buscar" onclick="javascript:form.submit();"/>
</center></td></tr>
</table><br></br>
</form>

<?php
if ($_POST['hid_validar']==2) {
	$fecha_in=trim($_POST['txt_inicio']);
	$fecha_fi=trim($_POST['hid_hasta_itin']); 
	$fecha_ini2=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	$fecha_fin2=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
	
$sql = "SELECT src.comp_id, src.reng_comp, src.cpat_id, src.cpat_nombre, src.rcomp_debe, src.rcomp_haber, EXTRACT(DAY FROM scd.comp_fec)||'/'||EXTRACT(month FROM scd.comp_fec)||'/'||EXTRACT(Year FROM scd.comp_fec) as fecha_emision, scd.comp_comen
FROM sai_reng_comp src, sai_comp_diario scd
where esta_id=15 and scd.comp_id like 'coda%' and scd.comp_fec >='".$fecha_ini2."' and scd.comp_fec <='".$fecha_fin2."' and trim(src.comp_id) = trim(scd.comp_id) and src.mostrar is TRUE order by src.fecha_emis";
$resultado_asientos=pg_query($conexion,$sql) or die("Error al consultar");  
?>
 <table width="90%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray">
     	 <td class="normalNegroNegrita">Fecha </td>
     	 <td class="normalNegroNegrita">Identif.</td>
     	 <td class="normalNegroNegrita">Rengl&oacute;n</td>
     	 <td class="normalNegroNegrita">Cta Contable </td>
     	 <td class="normalNegroNegrita">Nombre </td>
     	 <td class="normalNegroNegrita">Debe </td>
     	 <td class="normalNegroNegrita">Haber </td>
     	 <td class="normalNegroNegrita">Comentario </td>
        <td class="normalNegroNegrita">Opci&oacute;n </td>
	</tr>
	<? 
	 $total_debe=0;
	 $total_haber=0;
 	 $elementos=pg_num_rows($resultado_asientos);
     $contador=0;
	 while($rowor=pg_fetch_array($resultado_asientos)) {
		$query="select count (comp_id) as cantidad from sai_reng_comp where comp_id='".$rowor['comp_id']."'";
		$resultado_query=pg_query($conexion,$query) or die("Error al consultar los registros contables"); if ($rowq=pg_fetch_array($resultado_query)) {
		$cont_registros=$rowq['cantidad'];
	}
	?>
	<tr>
   	 <td width="45" class="normal" ><?php echo $rowor['fecha_emision'];?></td>
  	 <td><div class="normal"><?php echo $rowor['comp_id'];?></div></td>
 	 <td width="45" class="normal" ><?php echo $rowor['reng_comp'];?></td>
    	 <td width="75" class="normal" ><?php echo $rowor['cpat_id'];?></td>
    	 <td width="145" class="normal" ><?php echo $rowor['cpat_nombre'];?></td>
         <td width="65" class="normal" ><?php echo $rowor['rcomp_debe'];?></td>
         <td width="65" class="normal" ><?php echo $rowor['rcomp_haber'];?></td>
     	 <td width="90" class="normal" ><?php echo $rowor['comp_comen'];?></td>
        <?
	if (($contador==$cont_registros-1) or ($contador==$elementos-1)){
          $contador=-1;?>
	<td width="90"><div align="center" class="normal">
		<img src="imagenes/vineta_azul.gif" width="11" height="7">
		<a href="javascript:abrir_ventana('codaDetalle.php?anulado=1&codigo=<?php echo trim($rowor['comp_id']); ?>')" class="copyright"><?php echo "Ver Detalle"; ?></a>
		
		</div></td>
	<?}?>
 	</tr>
	<?$contador=$contador+1;
	  $total_debe=$total_debe+$rowor['rcomp_debe'];
	  $total_haber=$total_haber+$rowor['rcomp_haber'];
	  }
?>	
<tr>
	 <td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td width="65" class="normal"><b><?echo $total_debe;?></b></td>
<td width="65" class="normal"><b><?echo $total_haber;?></b></td>
<td></td>
	</tr>
</table>
<?php }?>
</html>