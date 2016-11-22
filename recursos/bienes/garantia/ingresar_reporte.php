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

  $descripcion=trim($_REQUEST['descripcion']);?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI:INGRESAR ART&Iacute;CULO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
<script language="JavaScript" src="../../../js/lib/actb.js"></script>
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet" />
<script type="text/javascript" 	src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" 	src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">	g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script>


//FunciOn que valida el llenado de todos los campos 
function revisar()
{
	if(document.form1.fecha_reporte.value==""){
	  alert("Debe seleccionar la fecha del reporte de la falla");
	  document.form1.fecha_reporte.focus();
	  return;
	}

	if (document.form1.ticket.value=="")
	{
		alert("Debe especificar el n\u00FAmero de ticket o caso")
		document.form1.ticket.focus();
		return;
	}	

	if (document.form1.servicio_tecnico.value=="")
	{
		alert("Debe especificar los datos del servicio t\u00E9cnico")
		document.form1.servicio_tecnico.focus();
		return;
	}
	
	if(confirm("Estos datos ser\u00E1n registrados. \u00BFEst\u00E1 seguro que desea continuar?"))
	{
	  document.form1.submit()
	}
}	

/*FunciiOn que valida que solo se introduzcan digitos caracteres y numericos en el campo*/
function validar_digito(objeto)
{
	var checkOK = "ABCDEFGHIJKLMN\u00D1OPQRSTUVWXYZabcdefghijklmn\u00F1opqrstuvwxyz0123456789\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA -_.,;:()\n";
	var checkStr = objeto.value;
	var allValid = true;
	for (i = 0;  i < checkStr.length;  i++)
	{
		ch = checkStr.charAt(i);
		for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
		if (j == checkOK.length)
		{
			var cambio=checkStr.substring(-1,i) 
			objeto.value=cambio;
			alert("Estos caracteres no est\u00E1n permitidos");
			break;
		}
	}
}

</script>
</head>
<body>
<form name="form1" method="post" action="ingresar_reporteAccion.php">
 <table width="550" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
	<td height="15" colspan="4" valign="midden" class="normalNegroNegrita">Reportar </span></td>
    </tr>
    <tr>
      <td height="33"><div class="normalNegrita">N&deg; de acta:</div></td>
      <td height="33" valign="midden" class="normalNegroNegrita"><?php echo $_REQUEST['codigo'];?>
	  <input name="id_acta" value="<?php echo $_REQUEST['codigo'];?>" type="hidden" />
	  </td>
    </tr>
    <tr>
      <td height="33"><div class="normalNegrita">Serial del activo:</div></td>
      <td height="33" valign="midden" class="normalNegroNegrita"><?php echo $_REQUEST['serial'];?>
	  <input name="serial" value="<?php echo $_REQUEST['serial'];?>" type="hidden"/> </td>
	   
    </tr>
<tr>
<td height="34" align="left" class="normalNegrita">Fecha de reporte:</td>

<td class="normal">
<input NAME="fecha_reporte" value=""  id="fecha_reporte" TYPE="text" size="10"  class="dateparse" value="" onClick="cal1xx4.select(this,'anchor1xx4','dd/MM/yyyy'); return false;" readonly> 
 <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha_reporte');" title="Show popup calendar">
	   <img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
	   </a>

</script>
</td>
</tr>
    <tr>
      <td height="33"><div class="normalNegrita">N&deg; de ticket o reporte:</div></td>
      <td height="33" valign="midden">
	  <input name="ticket" value="" type="text" class="normalNegro" size="30" maxsize="100" onchange="validar_digito(ticket)"/> <span class="peq_naranja">(*)</span></td>
    </tr>
    <tr> 
      <td height="31" valign="midden"> <div class="normalNegrita">Datos del servicio t&eacute;cnico:</div></td>
      <td height="31" valign="midden" class="normal">
	  <textarea name="servicio_tecnico" class="normalNegro" rows="3" cols="32" onchange="validar_digito(servicio_tecnico)"></textarea><span class="peq_naranja"> (*)</span></td>
    </tr>
    <tr>
      <td height="32"><div class="normalNegrita">Fecha de visita del soporte t&eacute;cnico:</div></td>
      <td height="32" valign="midden">
	<input NAME="fecha_visita" value=""  id="fecha_visita" TYPE="text" size="10"  class="dateparse" value="" onClick="cal1xx4.select(this,'anchor1xx4','dd/MM/yyyy'); return false;" readonly> 
 <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha_visita');" title="Show popup calendar"><img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a><span class="peq_naranja"> (*)</span> </td>
    </tr>
     <tr>
      <td height="15" colspan="4"><br>
	  <div align="center">
	  <input class="normalNegro" type="button" value="Reportar" onclick="javascript:revisar();"/>
	  <br><br></div>	  </td>
    </tr>
</table>
</form>
</body>
</html>
<?php pg_close($conexion);?>
