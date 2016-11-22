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
	if(document.form1.fecha_notificacion.value==""){
	  alert("Debe seleccionar la fecha de notificaci\u00F3n de la falla");
	  document.form1.fecha_notificacion.focus();
	  return;
	}

	if (document.form1.persona_reporta.value=="")
	{
		alert("Debe especificar la persona que report\u00F3 la falla")
		document.form1.persona_reporta.focus();
		return;
	}	

	if (document.form1.falla.value=="")
	{
		alert("Debe especificar la falla presentada")
		document.form1.falla.focus();
		return;
	}
	
	if (document.form1.persona_contacto.value=="")
	{
		alert("Debe especificar la persona contacto")
		document.form1.persona_contacto.focus();
		return;
	}	

	if (document.form1.tlf_contacto.value=="")
	{
		alert("Debe especificar el tel\u00E9fono de la persona contacto")
		document.form1.tlf_contacto.focus();
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
<form name="form1" method="post" action="ingresar_casoAccion.php">
 <table width="550" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
	<td height="15" colspan="4" valign="midden" class="normalNegroNegrita">Registrar </span></td>
    </tr>
    <tr>
      <td height="33"><div class="normalNegrita">Serial Bien Nacional:</div></td>
      <td height="33" valign="midden" class="normalNegroNegrita"><?php echo $_REQUEST['sbn'];?>
	  <input name="sbn" value="<?php echo $_REQUEST['sbn'];?>" type="hidden" />
	  <input name="clave_bien" value="<?php echo $_REQUEST['clave_bien'];?>" type="hidden" />
	  </td>
    </tr>
    <tr>
      <td height="33"><div class="normalNegrita">Serial del activo:</div></td>
      <td height="33" valign="midden" class="normalNegroNegrita"><?php echo $_REQUEST['serial'];?>
	  <input name="serial" value="<?php echo $_REQUEST['serial'];?>" type="hidden"/> </td>
	   
    </tr>
<tr>
<td height="34" align="left" class="normalNegrita">Fecha de notificaci&oacute;n:</td>

<td class="normal">
<input NAME="fecha_notificacion" value=""  id="fecha_notificacion" TYPE="text" size="10"  class="dateparse" value="" onClick="cal1xx4.select(this,'anchor1xx4','dd/MM/yyyy'); return false;" readonly> 
 <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha_notificacion');" title="Show popup calendar">
	   <img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
	   </a>

</script><input type="hidden" name="hid_buscar" id="hid_buscar" value="0">
</td>
</tr>
    <tr>
      <td height="33"><div class="normalNegrita">Persona quien reporta:</div></td>
      <td height="33" valign="midden">
	  <input name="persona_reporta" value="" type="text" class="normalNegro" size="30" maxsize="100" onchange="validar_digito(persona_reporta)"/> <span class="peq_naranja">(*)</span></td>
    </tr>
    <tr> 
      <td height="31" valign="midden"> <div class="normalNegrita">C&oacute;digos adicionales:</div></td>
      <td valign="midden" class="normal">
	  <textarea name="codigos" class="normalNegro" rows="3" cols="32" onchange="validar_digito(codigos)"></textarea></td>
    </tr>
        <tr> 
      <td height="31" valign="midden"> <div class="normalNegrita">Falla que presenta:</div></td>
      <td height="31" valign="midden" class="normal">
	  <textarea name="falla" class="normalNegro" rows="3" cols="32" onchange="validar_digito(falla)"></textarea><span class="peq_naranja">(*)</span></td>
    </tr>
    <tr>
	  <td class="normalNegrita">Persona contacto:</td>
	  <td height="42" class="normalNegrita">
	 <input name="persona_contacto" type="text" class="normalNegro" size="26" onchange="validar_digito(persona_contacto)"/><span class="peq_naranja">(*)</span> </td>
	  </tr>
    
    <tr>
      <td height="32"><div class="normalNegrita">Tel&eacute;fono persona contacto</div></td>
      <td height="32" valign="midden">
	<input name="tlf_contacto" type="text" class="normalNegro" size="26" onchange="validar_digito(tlf_contacto)" maxlength="19"/><span class="peq_naranja">(*)</span> </td>
    </tr>
     <tr>
      <td height="15" colspan="4"><br>
	  <div align="center">
	  <input class="normalNegro" type="button" value="Registrar" onclick="javascript:revisar();"/>
	  <br><br></div>	  </td>
    </tr>
</table>
</form>
</body>
</html>
<?php pg_close($conexion);?>
