<?php 
  ob_start();
  session_start();
  require_once("../../../includes/conexion.php");
	    
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
    header('Location:../../../index.php',false);
	ob_end_flush(); 
	exit;
  }
	ob_end_flush(); 
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI::Anulaci&oacute;n o Devoluci&oacute;n de Actas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');
</script>
<script lenguaje="JavaScript" SRC="../../../includes/js/funciones.js"> </SCRIPT>
<script language="JavaScript" type="text/JavaScript">

function accion(){

 if(document.form1.hid_desde_itin.value=="")
 {
	window.alert("Debe seleccionar la fecha de devoluci\u00F3n");
	document.form1.hid_desde_itin.focus();
	return
  }
		
 if(document.form1.observacion.value=="")
 {
	window.alert("Debe especificar el motivo de la devoluci\u00F3n");
	document.form1.observacion.focus();
	return
 }

 contador=0;
 Form = document.forms["form1"]
 for(i = 0; i < Form.elements.length; i++)
     if(Form.elements[i].type == "checkbox")
         if(Form.elements[i].checked)
             contador++
 if(contador==0)
	 alert('Debe indicar el art\u00EDculo a devolver');

  if(contador!=0)
 document.form1.submit();
}
</script>
	
</head>
<body>
<br/><br/>
<form name="form1" method="post" action="devolverAccion.php">
<?php 
$acta=$_GET['codigo'];
$tipo=$_GET['tipo'];

$sql="SELECT * FROM sai_seleccionar_campo('sai_arti_salida','salida_id,alm_id,arti_id,cantidad,precio','n_acta = '||'''$acta'' ORDER BY salida_id ASC','',2) 
resultado_set(salida_id int4,alm_id int4,arti_id varchar,cantidad int4,precio float8)";    
$resultado=pg_query($conexion,$sql) or die("Error al consultar");  

?>
<table width="508" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
 <tr class="td_gray">
   <td colspan="3" class="normalNegroNegrita"><b>Devolver acta N&deg; <?=$acta?></b></td>
 </tr>
 <tr>
   <td width="50%" class="normal"><div align="left"><b>Fecha acta de devoluci&oacute;n</b> </div></td>
   <td align="left" colspan="2" class="normalNegrita">
     <input type="text" size="10" id="hid_desde_itin" name="hid_desde_itin" class="dateparse" readonly="readonly" />
	 <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_desde_itin');" title="Show popup calendar">
	 <img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a>	</td>
	</tr>
 <tr>
   <td height="21" width="150" class="normal" align="left"><b>Justificaci&oacute;n: </b></td><td colspan="2"><textarea cols="50" rows="3" name="observacion"></textarea></td>
 </tr>
 <tr class="td_gray">
   <td class="titularMedio"><div align="center"><strong> &nbsp; </strong></div></td>
   <td class="normalNegroNegrita"><div align="center"><strong>Art&iacute;culo</strong></div></td>
   <td class="normalNegroNegrita"><div align="center"><strong>Cantidad</strong></div></td>
 </tr>
 <?php 
   $i=0;
   while($rowor=pg_fetch_array($resultado))
   {
    $i++;
   ?>
  <tr>
    <td align="center" class="peq" ><input type="checkbox" name="solicitud[]" value="<?php echo $rowor['salida_id'];?>" /> </td>
	 <?
	   $id=$rowor['arti_id'];
	   $alm_id=$rowor['salida_id'];
	   $cantidad=$rowor['cantidad'];
	   $sql_d="SELECT * FROM sai_seleccionar_campo('sai_item','id,nombre','id_tipo=1 and id=''$id''','',1) resultado_set(id varchar,nombre varchar)"; 
	   $resultado_set_most_d=pg_query($conexion,$sql_d) or die("Error al consultar lista de articulos");
	   $rowd=pg_fetch_array($resultado_set_most_d);
	 ?>
    <td><div align="left" class="normal"><?php echo $rowd['nombre'];?></div></td>
    <td><div align="center" class="normal">
	  <select name="<?=$alm_id;?>" class="normal">
	   <?for ($i=0; $i<=$cantidad;$i++){?>
		 <option value="<?=$i;?>"><?=$i;?></option>
	   <?}?>
	  </select></div><input type="hidden" name="acta" value="<?=$acta?>" /><input type="hidden" name="tipo" value="<?=$tipo?>" /></td>
  </tr>
<?php }?>
   <tr><td><input type="hidden" name="contador" id="contador" value="<?echo $i;?>" /></td></tr>
   <tr>
     <td colspan="3" height="50"><div align="center">
      <input type="button" value="Devolver" onclick="javascript:accion();">
  </tr>
</table>
<br/>
</form>
</body>
</html>
<?php pg_close($conexion);?>
