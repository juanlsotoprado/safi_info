<?php 
ob_start();
session_start();
 
require_once(dirname(__FILE__) . '/../../init.php');
require_once(SAFI_INCLUDE_PATH . '/conexion.php');
require_once(SAFI_INCLUDE_PATH . '/funciones.php');
require_once(SAFI_INCLUDE_PATH . '/perfiles/constantesPerfiles.php');
require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');

  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../index.php',false);
   ob_end_flush(); 
   exit;
  }
  ob_end_flush(); 

  $user_perfil_id = $_SESSION['user_perfil_id'];
	
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>.:SAFI:Buscar Documentos PCTA</title>
	<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
	<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
	<link type="text/css" href="../../css/safi0.2.css" rel="stylesheet" />

	<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
	<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
	<script type="text/javascript"src="../../js/funciones.js"></script>
	<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');
	</script>

	
<script>
function ejecutar() { 
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
<form name="form" action="pcta_buscar.php" method="post">
<input type="hidden" value="0" name="hid_validar" />
<br />
<table width="640" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
    <td height="21" colspan="2" class="normalNegroNegrita" align="left">B&uacute;squeda de solicitudes de puntos de cuenta</td>
  </tr>
  <tr>
    <td height="10" colspan="2"></td>
  </tr>
  <tr>
	<td width="175" height="29" class="normalNegrita" align="left">Solicitados entre:</td>
	<td>
	<!-- Agregar los accesos rapidos de las fechas (Hoy, ayer, semana, semana pasada, etc.) -->
	<?php VistaFechas::ConstruirAccesosRapidosFechas("txt_inicio", "hid_hasta_itin", "dd/mm/yy") ?>
	<input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse"
	onfocus="javascript: comparar_fechas(this);" readonly="readonly" />
	<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar">
	<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a>
	<input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse"
	onfocus="javascript: comparar_fechas(this);" readonly="readonly" value=""/>
	<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
	<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
	</a>	</td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">C&oacute;digo del documento:</td>
	<td><span class="normalNegrita"><input name="txt_cod" type="text" class="normalNegro" id="txt_cod" value="pcta-" size="12" /></span>
	<span class="normalNegrita"> A&ntilde;o Presupuestario: </span>
	 <select name="a_o" id="a_o"  class="normalNegro">
	 <option value="09">2009</option>
	 <option value="10">2010</option>
	 <option value="11">2011</option>
	 <option value="12">2012</option>
	 <option value="13">2013</option>
	 <option value="0" selected="selected">Seleccione</option>	 		 	 
	 </select>
	</td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">Asunto del punto cuenta:</td>
	<td>
	 <select name="tipo_pcta" id="tipo_pcta" class="normalNegro">
	  <option value="0">Seleccione</option>
	  <?php
	    $sql = "select pcas_id, pcas_nombre from sai_pcta_asunt order by pcas_nombre";
		$resultado_set=pg_query($conexion,$sql) or die("Error al consultar los tipos de pctas");  
		while($rowor=pg_fetch_array($resultado_set))
		{
		?> 
		<option value="<?php echo $rowor['pcas_id']?>">
		<?php echo $rowor['pcas_nombre']?></option>
		<?php 		
		}
		?>
	 </select>
	</td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">Partida:</td>
	<td><span class="normalNegrita">
	  <input name="txt_partida" type="text" class="normalNegro" id="txt_partida" value="" size="25" /></span></td>
  </tr>
   <tr>
	<td class="normalNegrita" align="left">Proyecto/Acc espec&iacute;fica:</td>
	<td>
	 <select name='proyac' class="normalNegro">
	  <option value="0" >Todos</option>
	  <?php
		$sql = "select acce_id as proyecto, aces_id as especifica, centro_gestor, centro_costo from sai_acce_esp where pres_anno=".$_SESSION['an_o_presupuesto']." union select proy_id as proyecto, paes_id as especifica, centro_gestor, centro_costo from sai_proy_a_esp where pres_anno=".$_SESSION['an_o_presupuesto']." order by centro_gestor, centro_costo";
		$resultado_set=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");  
		while($rowor=pg_fetch_array($resultado_set))
		{
		?> 
		<option value="<?php echo $rowor['proyecto'].":::".$rowor['especifica'].":::".$rowor['centro_gestor'].":::".$rowor['centro_costo']?>" ><?php echo $rowor['centro_gestor'].'/'.$rowor['centro_costo']?></option>
		<?php		
		}?>
	 </select></td>
  </tr>
  <?php //}?>
  <tr>
	<td class="normalNegrita" align="left">Palabra Clave:</td>
	<td><span class="normalNegrita"><input name="txt_clave" type="text" class="normalNegro" id="txt_clave" value="" size="25" />
	</span><span class="normal">(Incluida en la descripci&oacute;n del punto de cuenta)</span></td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">Estatus:</td> 
	<td><span class="normalNegrita">
	<?php
	$sql_str="SELECT esta_id,esta_nombre FROM sai_estado WHERE esta_id='10' or esta_id='7' or esta_id='13' or esta_id='15' order by esta_nombre";
	$res_q=pg_exec($sql_str);
	?>
	<select name="esatus" class="normalNegro" id="estatus">
		<option value="0" selected="selected">--</option>
		<?php
			while($depe_row=pg_fetch_array($res_q)){
		?>
		<option value="<?=(trim($depe_row['esta_id']))?>"><?=(trim($depe_row['esta_nombre']))?></option>
	   <?php
			}
		?>
		</select>
	
	</td>
  </tr>

</table><br>
<div align="center"><input type="button" value="Buscar" onclick="javascript:ejecutar();"></div>
</form>
<br>
<form name="form3" method="post">
<?php 
  if ($_POST['hid_validar']==$_SESSION['dos']) 
  {
	$fecha_in=trim($_POST['txt_inicio']);
	$fecha_fi=trim($_POST['hid_hasta_itin']); 
	$fecha_ini2=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	$fecha_fin2=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2);

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
$wheretipo13="";

$from="";

if (strlen($_POST['txt_inicio'])>$_SESSION['dos']) {
$wheretipo1 = "and to_date(to_char(sc.pcta_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini2."' and to_date(to_char(sc.pcta_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fin2."' ";
$wheretipo11 = " order by 2,1";
$wheretipo12= " sc.pcta_id, ";
}

if ($_POST['a_o']<>0){
	$wheretipo13=" and sc.pcta_id like '%".$_POST['a_o']."'";
	$wheretipo12= " sc.pcta_id, ";
}
if (strlen($_POST['txt_cod'])>$_SESSION['cinco']) {
	if ($_POST['a_o']<>0){
$wheretipo2 = " and (sc.pcta_id like '%".$_POST['txt_cod']."%".$_POST['a_o']."' 
				or sc.pcta_id like '%".$_POST['txt_cod']."') ";
	}else{
$wheretipo2 = " and sc.pcta_id like '%".$_POST['txt_cod']."' ";
		
	}
$wheretipo11 = " order by 2,1";
$wheretipo12= " sc.pcta_id, ";
}

if ($_POST['tipo_pcta']<>"0") {
$wheretipo6 = " and sc.pcta_asunto='".$_POST['tipo_pcta']."' ";
$wheretipo11 = " order by 2,1";
$wheretipo12= " sc.pcta_id, ";
}

if (strlen($_POST['txt_clave'])>$_SESSION['cero']) {
$wheretipo8 = " and upper(sc.pcta_descripcion) like '%".cadenaAMayusculas($_POST['txt_clave'])."%' ";
$wheretipo11 = " order by 2,1";
$wheretipo12= " sc.pcta_id, ";
}
if (strlen($_POST['estatus'])<>$_SESSION['cero']) {
$wheretipo9 = " and dg.esta_id= '".$_POST['esatus']."' ";
$wheretipo11 = " order by 2,1";
$wheretipo12= " sc.pcta_id, ";
}

$where_sin_partidas=" union
select sc.pcta_id,sc.pcta_fecha,to_char(sc.pcta_fecha, 'DD/MM/YYYY') as pcta_fecha, pcta_monto_solicitado as monto,pcas_nombre,depe_nombre,pcta_descripcion,esta_nombre,'N/A' AS centro_gestor,'N/A' AS centro_costo,sc.esta_id
from sai_pcuenta sc,sai_pcta_asunt,sai_dependenci sd,sai_doc_genera dg, sai_estado se 
where pcta_asunto=pcas_id and sc.depe_id=sd.depe_id and docg_id=sc.pcta_id and dg.esta_id=se.esta_id and recursos=0 ".$wheretipo1.$wheretipo2.$wheretipo6.$wheretipo8.$wheretipo9.$wheretipo10.$wheretipo13;

if (strlen($_POST['txt_partida'])>$_SESSION['dos']) {
$wheretipo5 = " and t7.pcta_sub_espe like '".$_POST['txt_partida']."%' ";
$from=", sai_pcta_imputa t7 ";
$wheretipo7 = " and sc.pcta_id=t7.pcta_id and docg_id=t7.pcta_id ";
$wheretipo11 = " order by 2,1";
$wheretipo12= " sc.pcta_id, ";
$where_sin_partidas="";
}

 if (strlen($_POST['proyac'])>$_SESSION['ocho']) {
 $from=", sai_pcta_imputa t7 ";
list( $proy, $especif) = split( ':::', $_POST['proyac'] );
$wheretipo10 = " and sc.pcta_id=t7.pcta_id and t7.pcta_acc_pp='".$proy."' and pcta_acc_esp='".$especif."' ";
$wheretipo12= " distinct(sc.pcta_id), ";
$where_sin_partidas="";
}



$sql_or="select ".$wheretipo12." sc.pcta_fecha, to_char(sc.pcta_fecha, 'DD/MM/YYYY') as pcta_fecha, pcta_monto_solicitado as monto,pcas_nombre,depe_nombre,pcta_descripcion,esta_nombre,centro_gestor,centro_costo,sc.esta_id
from sai_pcuenta sc,sai_pcta_asunt,sai_dependenci sd,sai_doc_genera dg, sai_estado se ,sai_proy_a_esp, sai_pcta_imputa t7  
where t7.pcta_id=sc.pcta_id and pcta_asunto=pcas_id and sc.depe_id=sd.depe_id and docg_id=sc.pcta_id and dg.esta_id=se.esta_id and pcta_acc_pp=proy_id and pcta_acc_esp=paes_id ".$wheretipo1.$wheretipo2.$wheretipo5.$wheretipo6.$wheretipo7.$wheretipo8.$wheretipo9.$wheretipo10.$wheretipo13."
union
select ".$wheretipo12." sc.pcta_fecha, to_char(sc.pcta_fecha, 'DD/MM/YYYY') as pcta_fecha, pcta_monto_solicitado as monto,pcas_nombre,depe_nombre,pcta_descripcion,esta_nombre,centro_gestor,centro_costo,sc.esta_id
from sai_pcuenta sc,sai_pcta_asunt,sai_dependenci sd,sai_doc_genera dg, sai_estado se ,sai_acce_esp, sai_pcta_imputa t7  
where t7.pcta_id=sc.pcta_id and pcta_asunto=pcas_id and sc.depe_id=sd.depe_id and docg_id=sc.pcta_id and dg.esta_id=se.esta_id and pcta_acc_pp=acce_id and pcta_acc_esp=aces_id ".$wheretipo1.$wheretipo2.$wheretipo5.$wheretipo6.$wheretipo7.$wheretipo8.$wheretipo9.$wheretipo10.$wheretipo13.$where_sin_partidas.$wheretipo11;
//echo $sql_or;
$resultado_set_most_or=pg_query($conexion,$sql_or) or die("Error al consultar la descripcion del pcta");
if  ($rowor=pg_fetch_array($resultado_set_most_or))  {

}
?>
<table width="100%" border="0" align="center">
  <tr>
    <td width="495" height="27" class="normal"><div align="center">Resultado de la b&uacute;squeda de pctas </div></td>
  </tr>
</table>
<table width="100%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray">
	<td width="10" class="normalNegroNegrita" align="center">No.</td>
	<td width="137" class="normalNegroNegrita" align="center">C&oacute;digo del Documento </td>
	<td width="102" class="normalNegroNegrita" align="center">Asunto</td>
	<td width="102" class="normalNegroNegrita" align="center">Dependencia</td>
	<td width="102" class="normalNegroNegrita" align="center">Centro Gestor</td>
	<td width="102" class="normalNegroNegrita" align="center">Centro Costo</td>
	<td width="102" class="normalNegroNegrita" align="center">Estatus</td>
	<td width="243" class="normalNegroNegrita" align="center">Descripci&oacute;n </td>
	<td width="102" class="normalNegroNegrita" align="center">Monto</td>
	<td width="128" class="normalNegroNegrita" align="center">Fecha de la Solicitud </td>
	<td width="128" class="normalNegroNegrita" align="center">Monto Disponible </td>
	  <?php 
  	 if (($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO)){?>
  	<td width="128" class="normalNegroNegrita" align="center">Opciones</td> 	
  	 <?php }?>
  </tr>
  <?$cont=1;
  $resultado_set_most_or=pg_query($conexion,$sql_or) or die("Error al consultar la descripcion del pcta");
	while ($rowor=pg_fetch_array($resultado_set_most_or))  {
		$query_disponibilidad="select sum(monto) as disponible from sai_disponibilidad_pcta t1,sai_pcuenta t2 
		where t1.pcta_asociado='".$rowor['pcta_id']."' and t1.pcta_id=t2.pcta_id and t2.esta_id<>15";
		//echo $query_disponibilidad."<br>";
		$result_disponibilidad=pg_query($conexion,$query_disponibilidad) OR die ("Error de disponibilidad");
		$disponibilidad=0;
		if ($row_disp=pg_fetch_array($result_disponibilidad)){
		 $disponibilidad=$row_disp['disponible'];
		 if (($disponibilidad>-0) && ($disponibilidad<0.99))
		 	$disponibilidad=0;
		}
	$beneficiario_nombre= "";
	$beneficiario = "";?>
  <tr class="normal">
	<td align="center"><span class="peq"><?php echo $cont;?></span></td>
	<td height="28" align="center"><span class="link">
	<a href="javascript:abrir_ventana('pcta_detalle.php?codigo=<?php echo trim($rowor['pcta_id']); ?>&amp;esta_id=<?php echo($rowor['esta_id']);?>')" class="copyright"><?php echo $rowor['pcta_id'] ;?></a></span></td>
	<td align="center"><span class="normalNegro"><?php echo $rowor['pcas_nombre'];?></span></td>
	<td align="center"><span class="normalNegro"><?php echo $rowor['depe_nombre'];?></span></td>
	<td align="center"><span class="normalNegro"><?php echo $rowor['centro_gestor'];?></span></td>
	<td align="center"><span class="normalNegro"><?php echo $rowor['centro_costo'];?></span></td>
	<td align="center"><span class="normalNegro"><?php echo $rowor['esta_nombre'];?></span></td>
	<td align="center"><span class="normalNegro"><?php echo $rowor['pcta_descripcion'];?></span></td>
	<td align="center"><span class="normalNegro"><b><?php echo number_format($rowor['monto'],2,',','.');?></b></span></td>
	<td align="center"><span class="normalNegro"><?php echo $rowor['pcta_fecha'];?></span></td>
	<td align="center"><span class="normalNegro"><b><?php echo $disponibilidad;//number_format($disponibilidad,2,',','.');?></b></span></td>
		  <?php 
  	 if (($rowor['esta_id']<>15) &&(substr($rowor['pcta_id'],0,8)=="pcta-400") && (($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO))){?>
  	<td width="128" class="normalNegro" align="center">
  	<a href="javascript:abrir_ventana('pcta_liberacion_anula.php?codigo=<?php echo trim($rowor['pcta_id']); ?>')" class="copyright">Anular</a></td> 	
  	 <?php }?>
  </tr>
  <?php	 
    $cont++;
	}
}			?> 
 </table> 
</form>
</body>
</html>
