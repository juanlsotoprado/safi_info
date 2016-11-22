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

 if(document.form1.observacion.value=="")
 {
	window.alert("Debe Especificar el motivo de la Anulaci\u00F3n");
	document.form1.observacion.focus();
	return
 }

 document.form1.submit();
}
</script>
	
</head>
<body>
<br/>
<form name="form1" method="post" action="anularAccion.php">
<?php 
$acta=$_GET['codigo'];
$tipo=$_GET['tipo'];

$sql="SELECT * FROM sai_seleccionar_campo('sai_arti_salida','salida_id,alm_id,arti_id,cantidad,precio','n_acta = '||'''$acta'' ORDER BY salida_id ASC','',2) 
resultado_set(salida_id int4,alm_id int4,arti_id varchar,cantidad int4,precio float8)";    
$resultado=pg_query($conexion,$sql) or die("Error al consultar");  

?>
  <table width="508" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
  <td height="21" colspan="3" class="normalNegroNegrita" align="left">Anular acta N&deg;: <?php echo $acta;?> </td>
</tr>
 <tr class="td_gray">
   <td class="normalNegroNegrita"><div align="center"><strong>#</strong></div></td>
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
	 <?
	   $id=$rowor['arti_id'];
	   $alm_id=$rowor['salida_id'];
	   $cantidad=$rowor['cantidad'];
	   $sql_d="SELECT * FROM sai_seleccionar_campo('sai_item','id,nombre','id_tipo=1 and id=''$id''','',1) resultado_set(id varchar,nombre varchar)"; 
	   $resultado_set_most_d=pg_query($conexion,$sql_d) or die("Error al consultar lista de articulos");
	   $rowd=pg_fetch_array($resultado_set_most_d);
	 ?>
	<td><div align="center" class="normal"><?php echo $i;?></div></td>
    <td><div align="left" class="normal"><?php echo $rowd['nombre'];?></div></td>
    <td><div align="right" class="normal"><?php echo $cantidad;?></div>
    <input type="hidden" name="acta" value="<?=$acta?>" /><input type="hidden" name="tipo" value="<?=$tipo?>" /></td>
  </tr>
<?php }?>
 <tr>
   <td height="21" width="100" class="normal" align="left"><b>Justificaci&oacute;n: </b></td><td colspan="2"><textarea cols="50" rows="3" name="observacion"></textarea></td>
 </tr>

   <tr><td><input type="hidden" name="contador" id="contador" value="<?echo $i;?>" /></td></tr>
   <tr>
     <td colspan="3" height="50"><div align="center">
      <input type="button" value="Anular" onclick="javascript:accion();">
  </tr>
</table>
<br/>
</form>
</body>
</html>
<?php pg_close($conexion);?>
