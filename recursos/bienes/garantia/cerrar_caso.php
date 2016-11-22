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
function mostrar_acciones(){

  if(document.getElementById('accion').value=="2"){ //REEMPLAZO
    document.getElementById('flotante').style.display='block';
    document.getElementById('flotante2').style.display='none';
	}else{
		document.getElementById('flotante').style.display='none';
		document.getElementById('flotante2').style.display='block';
		}
}

function cambiar_opcion(opc){
	if ((document.getElementById('paso3').checked == true) && (document.getElementById('paso4').checked == true)){
		
		document.getElementById('paso3').checked = false
		document.getElementById('paso4').checked = false;
	    alert("Debe seleccionar la opci\u00F3n 3 o 4, no ambas");
	    return;
	 }
}

//FunciOn que valida el llenado de todos los campos 
function revisar()
{
	if(document.form1.fecha_cierre.value==""){
	  alert("Debe seleccionar la fecha de cierre del caso");
	  document.form1.fecha_cierre.focus();
	  return;
	}

	if(document.form1.observaciones.value==""){
		  alert("Debe indicar alguna observaci\u00F3n sobre el cierre del caso");
		  document.form1.observaciones.focus();
		  return;
		}
	
	if(document.form1.accion.value=="0"){
		  alert("Debe indicar la acci\u00F3n realizada por el t\u00E9cnico");
		  document.form1.accion.focus();
		  return;
		}
	
		
	if(document.form1.accion.value=="2"){
	
		if(document.form1.txt_modelo.value=="")
		{
			alert("Debe indicar el modelo del activo, si no aplica indique N/A");
			document.form1.txt_modelo.focus();
			return
		}
		if(document.form1.txt_bien_nacional.value=="")
		{
			alert("Debe indicar el serial de Bien Nacional del activo");
			document.form1.txt_bien_nacional.focus();
			return
		}	
		if(document.form1.txt_serial.value=="")
		{
			alert("Debe indicar el serial del activo, si no aplica indique N/A");
			document.form1.txt_serial.focus();
			return
		}
		if(document.form1.fecha_ing.value=="")
		{
	  		alert("Debe seleccionar la fecha de ingreso del activo");
	  		document.form1.fecha_ing.focus();
	  		return;
    	}

		if ((document.getElementById('paso3').checked == false) && (document.getElementById('paso4').checked == false)){
			if(confirm("\u00BFEst\u00E1 seguro que desea continuar sin generar acta de salida o de custodia? "))
			{
			  document.form1.submit()
			}
		}else{
			if(confirm("Estos datos ser\u00E1n registrados. \u00BFEst\u00E1 seguro que desea continuar?"))
			{
			  document.form1.submit()
			}
		}
		
	}
	else{
	
		if(confirm("Estos datos ser\u00E1n registrados. Est\u00E1 seguro que desea continuar?"))
		{
		  document.form1.submit()
	     }}
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
<form name="form1" method="post" action="cerrar_casoAccion.php">
 <table width="550" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
	<td height="15" colspan="4" valign="midden" class="normalNegroNegrita">Cerrar </span></td>
    </tr>
     <tr>
      <td height="33"><div class="normalNegrita">N&deg; de acta:</div></td>
      <td height="33" valign="midden" class="normalNegroNegrita"><?php echo $_REQUEST['codigo'];?>
	  <input name="id_acta" value="<?php echo $_REQUEST['codigo'];?>" type="hidden" />
	  </td>
    </tr>
    <tr>
      <td height="33"><div class="normalNegrita">N&deg; de ticket o caso:</div></td>
      <td height="33" valign="midden" class="normalNegroNegrita"><?php echo $_REQUEST['ticket'];?>
	  <input name="ticket" value="<?php echo $_REQUEST['ticket'];?>" type="hidden" />
	  </td>
    </tr>
    <tr>
      <td height="33"><div class="normalNegrita">Serial del activo:</div></td>
      <td height="33" valign="midden" class="normalNegroNegrita"><?php echo $_REQUEST['serial'];?>
	  <input name="serial" value="<?php echo $_REQUEST['serial'];?>" type="hidden"/> 
	  <input type="hidden" name="clave_bien" value="<?php echo $_REQUEST['clave'];?>"></input></td>
    </tr>
	<tr>
	  <td height="34" align="left" class="normalNegrita">Fecha de cierre:</td>
	  <td class="normal"><input NAME="fecha_cierre" value="" id="fecha_cierre" TYPE="text" size="10"  class="dateparse" value="" onClick="cal1xx4.select(this,'anchor1xx4','dd/MM/yyyy'); return false;" readonly> 
 	  <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha_cierre');" title="Show popup calendar">
	  <img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a><span class="peq_naranja">(*)</span>
	  </script></td>
	</tr>
	<tr> 
      <td height="31" valign="midden"> <div class="normalNegrita">Observaciones del cierre:</div></td>
      <td height="31" valign="midden" class="normal">
	  <textarea name="observaciones" class="normalNegro" rows="3" cols="32" onchange="validar_digito(observaciones)"></textarea><span class="peq_naranja">(*)</span></td>
    </tr>
  
    <tr>
      <td height="33"><div class="normalNegrita">Acci&oacute;n realizada:</div></td>
      <td height="33" valign="midden">
	  <select name="accion" id="accion" class="normalNegro" onChange="javascript:mostrar_acciones()">
	  <option value="0">--</option>
	  <option value="1">Reparado</option>
	  <option value="2">Reemplazado</option>
	  </select>
	  <span class="peq_naranja">(*)</span></td>
    </tr>
  </table> 
  
   <div id="flotante" style="display:none;">
   <table name="datos_eemplazo" width="550" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
     <tr> 
      <td height="31" valign="midden"> <div class="normalNegrita">Procesos a ejecutar:</div></td>
      <td height="31" valign="midden" class="normalNegro">
     <input type="checkbox" name="paso1" id="paso1" checked disabled value="desincorporar"></input> 1.- Desincorporar activo (Serial de Bien Nacional <b><?php echo $_REQUEST['sbn'];?></b>)<br></br>
     <input type="checkbox" name="paso2" id="paso2" checked disabled value="ingresar"></input> 2.- Ingresar nuevo activo:<br></br>
       <table border="0" width="100%">
        <tr>
     	  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Modelo </td>
     	  <td><input name="txt_modelo" class="normalNegro" id="txt_modelo" size="10" value="<?php echo $_REQUEST['modelo'];?>"></input> </td>
     	 </tr>
     	 <tr>
     	  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Serial de Bien Nacional</td>
     <td><input name="txt_bien_nacional" id="txt_bien_nacional" class="normalNegro" length="10" ></input></td>
     </tr>
     <tr>
     <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Serial del activo</td>
     <td><input name="txt_serial" id="txt_serial" class="normalNegro" length="10" ></input></td>
     </tr>
     <tr>
     <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha de entrada</td>
     <td><input type="text" size="10" id="fecha_ing" name="fecha_ing" value="" class="normalNegro" readonly/>
      		 <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha_ing');" title="Show popup calendar">
	   <img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
	   </a></td>
     </tr>
	  </table>
	  <input type="checkbox" id="paso3" name="paso3" value="salida" onclick="cambiar_opcion('paso3')"></input> 3.- Generar acta de salida (mismo destino donde se encontraba el activo)<br></br>
	  <input type="checkbox" id="paso4" name="paso4" value="custodia" onclick="cambiar_opcion('paso4')"></input> 4.- Generar custodia<br></br>
      </td>
    </tr>  
</table> <br></br>
  </div>
  
   <div id="flotante2" style="display:none;">
   <table width="550" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
     <tr> 
      <td height="31" valign="midden" width="200"> <div class="normalNegrita">Proceso a ejecutar:</div></td>
      <td height="31" class="normalNegro" align="left"><input type="checkbox" id="paso5" name="paso5" value="custodia"></input> 1.- Generar custodia<br></br>
      </td>
    </tr>  
</table> <br></br>   </div>
<input type="hidden" name="sbn" value="<?php echo $_REQUEST['sbn'];?>"></input>
      <div align="center">
	  <input class="normalNegro" type="button" value="Cerrar caso" onclick="javascript:revisar();"/>
	  <br><br></div>	
     
</form>
</body>
</html>
<?php pg_close($conexion);?>
