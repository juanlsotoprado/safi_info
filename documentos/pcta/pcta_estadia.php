<?php 

ob_start();
session_start();


require_once(dirname(__FILE__) . '/../../init.php');
require_once(SAFI_INCLUDE_PATH . '/conexion.php');
require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');
	 
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	  {
		   header('Location:../../index.php',false);
	   	   ob_end_flush(); 	
		   exit;
	  }	ob_end_flush(); 
	  
	$usuario = $_SESSION['login'];
	$user_perfil_id = $_SESSION['user_perfil_id'];
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>.:SAFI:Buscar Documentos</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
	<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
	<link type="text/css" href="../../css/safi0.2.css" rel="stylesheet" />
	
	<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
	<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
	<script type="text/javascript" src="../../js/funciones.js"> </script>
	<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');
	</script>

<script>
function detalle(codigo)
{
    url="detalle.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}
function deshabilitar_combo(valor)
{
 if (valor=='1') 
 { 
   document.form.txt_inicio.disabled=false;
   document.form.hid_hasta_itin.disabled=false;
   document.form.txt_cod.value="";
   document.form.txt_cod.disabled=true;
   document.form.txt_reserva.value="";
   document.form.txt_reserva.disabled=true;
   document.form.tipo.value="1";
 }
 else
 if(valor=='2')
 { 
   document.form.txt_inicio.disabled=true;
   document.form.hid_hasta_itin.disabled=true; 
   document.form.txt_inicio.value="";
   document.form.hid_hasta_itin.value="";
   document.form.txt_cod.value="pcta-";
   document.form.txt_cod.disabled=false;
   document.form.txt_reserva.value="";
   document.form.txt_reserva.disabled=true;
   document.form.tipo.value="2";
   }
 else
 if (valor=='4') 
 { 
   document.form.txt_inicio.disabled=true;
   document.form.hid_hasta_itin.disabled=true; 
   document.form.txt_inicio.value="";
   document.form.hid_hasta_itin.value="";
   document.form.txt_cod.disabled=true;
   document.form.txt_reserva.disabled=false;
   document.form.txt_reserva.value="comp-";
   document.form.tipo.value="4";
  
 }
}

function ejecutar_varios(tipo,codigo1,codigo2,codigo3,codigo5) { 
	if (tipo==1 && (codigo1='' || codigo2=='')) {
		alert ('Debe seleccionar un rango de fechas');
		return;
	}
	else if (tipo==2 && codigo3=='') {
		alert ('Debe introducir el c\u00F3digo del punto de cuenta');
		return;
	}
	else if (tipo==4 && codigo5=='') {
		alert ('Debe introducir un n\u00FAmero de reserva');
		return;
	}
	else {
		  document.form.hid_validar.value=2;
	  	  document.form.submit();
	}
}



</script>
</head>
<body>
<form name="form" action="pcta_estadia.php" method="post">
  <input type="hidden" value="0" name="hid_validar" />
  <br />

  <table width="70%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
  <td height="21" colspan="4" class="normalNegroNegrita" align="left">B&uacute;squeda de solicitudes de puntos de cuenta</td>
</tr>
<tr>
  <td height="10" colspan="3"></td>
</tr>
<tr>
	<td colspan="2"></td>
	<td colspan="2">
		<!-- Agregar los accesos rapidos de las fechas (Hoy, ayer, semana, semana pasada, etc.) -->
	<?php VistaFechas::ConstruirAccesosRapidosFechas("txt_inicio", "hid_hasta_itin", "dd/mm/yy") ?>
	</td>
</tr>
<tr>
	<td width="20" align="center">
<input type="hidden" name="tipo" id="tipo" value="1" />
	<input name="opt_fecha" type="radio" value="1" onClick="javascript:deshabilitar_combo(1)" class="normal" checked/>	</td>
	<td width="175" height="29" class="normalNegrita" align="left">Solicitados entre:</td>
                <td width="110" class="titular"><div align="left" class="normalNegro">
                Fecha Inicio:</div>
<input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse"
onfocus="javascript: comparar_fechas(this);" readonly="readonly"/>
<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar">
<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"	alt="Open popup calendar"/>
</a>
</td>
  <td width="110" class="titular"><div align="left" class="normalNegro">Fecha Fin:</div>			
  <input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse"
onfocus="javascript: comparar_fechas(this);" readonly="readonly"/>
<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
</a>	
</td></tr>
<tr>
	<td height="30" align="center" class="normal">
	<input name="opt_fecha" type="radio" value="2" class="normal" onClick="javascript:deshabilitar_combo(2)" />	</td>
	<td class="normalNegrita" align="left">
            C&oacute;digo del documento:
          </td>
	<td><span class="normalNegrita">
	  <input name="txt_cod" type="text" class="peq" id="txt_cod" value="" size="12" disabled="disabled"/>
	</span></td>
</tr>
<tr>
	<td height="30" align="center" class="normal">
	<input name="opt_fecha" type="radio" value="4" class="normal" onClick="javascript:deshabilitar_combo(4)" />	</td>
	<td class="normalNegrita" align="left">
            Nro. Compromiso:
          </td>
	<td><span class="normalNegrita">
	  <input name="txt_reserva" type="text" class="peq" id="txt_reserva" value="comp-" size="15" disabled="disabled"/>
	</span></td>
</tr>
</table><br>
<div align="center">
 <input type="button" value="Buscar" onclick="javascript:ejecutar_varios(document.form.tipo.value, document.form.txt_inicio.value, document.form.hid_hasta_itin.value, document.form.txt_cod.value,  document.form.txt_reserva.value)">
</div>



</form>
<br>
<?php 
if ($_POST['hid_validar']==$_SESSION['dos']) {
	$fecha_in=trim($_POST['txt_inicio']);
	$fecha_fi=trim($_POST['hid_hasta_itin']); 
	$fecha_ini2=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	$fecha_fin2=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2);
	
$sql_or="select p.pcta_id, a.pcas_nombre as asunto, e.esta_nombre as estado, 
p.pcta_monto_solicitado as monto, case dg.perf_id_act when '' then 'Finalizado' else c.carg_nombre end as cargo, d.depe_nombre as dependencia, to_char(p.pcta_fecha, 'dd/mm/yyyy') as fecha,dd.depe_nombre as solicita,em.empl_nombres || ' ' || em.empl_apellidos as solicitante
from sai_empleado em,sai_dependenci dd,sai_estado e, sai_pcta_asunt a,sai_pcuenta p,  sai_doc_genera dg 
left outer join sai_cargo c on ( substr(dg.perf_id_act, 1,2)=c.carg_fundacion) 
left outer join  sai_dependenci d on(substr(dg.perf_id_act, 3,3)=d.depe_id)
where p.pcta_id=dg.docg_id and p.pcta_asunto=a.pcas_id and dg.esta_id=e.esta_id and p.pcta_gerencia=dd.depe_id 
and p.pcta_id_remit=em.empl_cedula "; 
	
if ($_POST['tipo']==$_SESSION['uno']) {
$sql_or=$sql_or." and p.pcta_fecha >= to_date('".$fecha_ini2."','YYYY MM DD') and p.pcta_fecha<=to_date('".$fecha_fin2."','YYYY MM DD')";
}

else if ($_POST['tipo']==$_SESSION['dos']) {
$sql_or=$sql_or." and p.pcta_id like '%".$_POST['txt_cod']."'";
}

else {
$sql_or=$sql_or." and p.numero_reserva like '%".$_POST['txt_reserva']."%'"; 
}
$sql_or=$sql_or." order by 1";

$resultado_set_most_or=pg_query($conexion,$sql_or) or die("Error al consultar la descripcion del punto de cuenta");  
?>
  <table width="100%" border="0" align="center">
   <tr>
     <td width="495" height="27" class="normalNegrita"><div align="center">Resultado de la b&uacute;squeda de puntos de cuenta </div></td>
   </tr>
  </table>
	  <table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
					<tr class="td_gray">
					  <td width="137" class="normalNegroNegrita" align="center">C&oacute;digo del Documento </td>
					  <td width="102" class="normalNegroNegrita" align="center">Asunto</td>
					  <td width="102" class="normalNegroNegrita" align="center">Estado</td>
					  <td width="115" class="normalNegroNegrita" align="center">Solicitante </td>
					  <td width="115" class="normalNegroNegrita" align="center">Gerencia Solicitante </td>
					  <td width="102" class="normalNegroNegrita" align="center">Ubicaci&oacute;n actual</td>
					<td width="128" class="normalNegroNegrita" align="center">Dependencia </td>
 					<td width="128" class="normalNegroNegrita" align="center">Fecha de la Solicitud </td>

					</tr>
					<?
		while ($rowor=pg_fetch_array($resultado_set_most_or))  {
		$beneficiario_nombre= "";
		$beneficiario = "";?>

					<tr class="normal">
					 <td height="28" align="center"><span class="link">
					 <? if (strcmp($user_perfil_id, $_SESSION['perfil_de'])==0) { ?>
					  <a href="javascript:abrir_ventana('pcta_detalle.php?codigo=<?php echo trim($rowor['pcta_id']); ?>&amp;esta_id=<?php echo($rowor['esta_id']);?>')" class="copyright"><?php echo $rowor['pcta_id'] ;?></a>
					<? } else {
 						  echo $rowor['pcta_id'] ;
					}?>
</span></td>
						<td align="center"><span class="peq"><?php echo $rowor['asunto'];?></span></td>
						<td align="center"><span class="peq"><?php echo $rowor['estado'];
						$estado=$rowor['estado'];?></span></td>
						<td align="center"><span class="peq"><?php echo $rowor['solicitante'];?></span></td>
						<td align="center"><span class="peq"><?php echo $rowor['solicita'];?></span></td>
					    <?if ($estado=="Anulado"){?>
						<td align="center"><span class="peq">--</td>
						<td align="center"><span class="peq">--</td>
					    <?}else{?>
						<td align="center"><span class="peq"><?php echo $rowor['cargo'];?></span></td>
						<td align="center"><span class="peq"><?php echo $rowor['dependencia'];?></span></td>
						<?}?>
						<td align="center"><span class="peq"><?php echo $rowor['fecha'];?></span></td>
						</tr>
			<?php	 
				}
			}
?> 
 </table> 

</body>
</html>
<?php pg_close($conexion);?>
