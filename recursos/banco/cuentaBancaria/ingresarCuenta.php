<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../..../index.php',false);
	ob_end_flush();    exit;
}ob_end_flush(); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Ingresar Cuenta Bancaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">	g_Calendar.setDateFormat('dd/mm/yyyy'); </script>

<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../../js/funciones.js"> </script>
<!-- <script language="JavaScript" src="../../../js/lib/CalendarPopup.js"> </script>-->
<!-- <script language="JavaScript">document.write(getCalendarStyles());</script>-->
<script language="JavaScript" type="text/JavaScript">

function revisar() { 
    if(document.form.txt_banco.value==0) 	{
	  alert("Debe seleccionar el nombre del banco");
	  document.form.txt_banco.focus();
	  return;
    }
	
	if(document.form.txt_num_cta.value=="") {
	  alert("Debe escribir el n\u00famero de la cuenta");
	  document.form.txt_num_cta.focus();
	  return;
    }
    
	var cuenta=document.form.txt_num_cta.value.length;
	if(cuenta < 16) {
		alert ("El n\u00famero de la cuenta debe ser de 16 d\u00edgitos");
		document.form.txt_num_cta.select();
		document.form.txt_num_cta.focus();
		document.form.txt_num_cta.value="";
		return;
	}
	if(document.form.txt_catalogo.value==0)	{
	  alert("Debe seleccionar la cuenta contable");
	  document.form.txt_catalogo.focus();
	  return;
    }
	if(document.form.txt_des_cta.value=="") {
	  alert("Debe indicar la descripci\u00f3n de la cuenta");
	  document.form.txt_des_cta.focus();
	  return;
    }
    if(document.form.txt_inicio.value=="")	{
	  alert("Debe seleccionar la fecha de apertura de la cuenta");
	  document.form.txt_inicio.focus();
	  return;
    }
	if((document.form.txt_inicio.value!='') && (document.form.fecha_reg.value!='')) {
		var txt_inicio=document.form.txt_inicio.value;
		var fecha_reg=document.form.fecha_reg.value;
			 
		var anoi=txt_inicio.substr(6,9);
		var mesi=txt_inicio.substr(3,2);
		var diai=txt_inicio.substr(0,2);
		 
		var anof=fecha_reg.substr(6,9);
		var mesf=fecha_reg.substr(3,2);
		var diaf=fecha_reg.substr(0,2);
		
		if (anoi > anof) { alert("La fecha de apertura no puede ser posterior a la fecha de registro"); return; }
		if ((anoi == anof) && (mesi > mesf)) { alert("La fecha de apertura no puede ser posterior a la fecha de registro"); return;}
		if ((anoi == anof) && (mesi == mesf) && (diai > diaf)){ alert("La fecha de apertura no puede ser posterior a la fecha de registro"); return;}
	}
	
    if(document.form.txt_tipo_cta.value==0) {
	  alert("Debe seleccionar el tipo de cuenta"); 
	  document.form.txt_tipo_cta.focus();
	  return;
    }
 
	if(document.form.txt_monto.value=="") {
	  alert("Debe escribir el monto de apertura de la cuenta");
	  document.form.txt_monto.focus();
	  return;
    }
    
	if(confirm("Est\u00e1 seguro que desea continuar?")) {  
			document.form.submit();
	}
}	

function buscar_cuenta() { 
   var codigo;
   var codigof; 
   var banco;
   var cuenta;
   codigo=document.form.txt_num_cta.value;
   banco=document.form.txt_ban.value;
   <?php
   $sql_p="SELECT ctab_numero FROM sai_ctabanco"; 
   $resultado_set_most_p=pg_query($conexion,$sql_p);
   while($row=pg_fetch_array($resultado_set_most_p)) {?>
      codigof="<?php echo trim($row['ctab_numero']); ?>"
	  cuenta=banco+codigo;
	  if (cuenta.toUpperCase()==codigof.toUpperCase()) {
	 	  alert("Este numero de cuenta ya existe...");
		  document.form.txt_num_cta.value='';
		   document.form.txt_num_cta.focus();
		  return;
	  }
    <?php } ?>
}
function limpiarFecha(fecha){
	fecha.value="";
}

function obtener_banco(){
	var txt_banco; var txt_ban; var cuenta;
	txt_banco=document.form.txt_banco.value
	if(txt_banco==""){txt_banco=0}
	txt_ban=txt_banco
	cuenta=txt_banco
	document.form.txt_ban.value=txt_ban;
}
</script>
</head>

<body>
<form name="form" method="post" action="ingresarCuentaAccion.php">
<table width="60%" align="center" background="../../../imagenes/fondo_tabla.gif" bgcolor="#FFFFFF" class="tablaalertas" >
<tr class="td_gray">
  <td height="23" colspan="2" class="normalNegroNegrita">Registrar cuenta bancaria</td>
</tr>
<tr>
  <td colspan="2" class="peq_naranja" align="center">Los campos que tienen asterisco ( * ) son obligatorios</td>
</tr>
<tr>
	<td class="normalNegrita">Banco:</td>
    <td> 
		<select name="txt_banco" id="txt_banco" class="normal" onchange="javascript: obtener_banco()">
	   <option value="0">[Seleccione]</option>
	   <?php
	    $sql_o="SELECT banc_id,upper(banc_nombre) as banc_nombre FROM sai_banco where esta_id=1";
		$resultado_set_most_o=pg_query($conexion,$sql_o) or die("Error al consultar");  
		while($rowo=pg_fetch_array($resultado_set_most_o)) { 
		?>
   	     <option value="<?=$rowo['banc_id']?>"><?=$rowo['banc_nombre']?></option> 
  		<?php } ?>
 		 </select>
	<span class="peq_naranja">(*)</span>
	 </td>
</tr>
<tr>
  <td class="normalNegrita">N&uacute;mero de cuenta:</td>
  <td class="submenuNegrita"><input name="txt_ban" type="text" class="normal" id="txt_ban" size="4" maxlength="4" onkeypress="return acceptNum(event)" disabled="disabled"/>-</label><input name="txt_num_cta" type="text" class="normal" id="txt_num_cta" size="17" onkeypress="return acceptNum(event)" onblur="buscar_cuenta()"  maxlength="16"/>
    <span class="peq_naranja">(*)</span> 
    <input name="cuenta" type="hidden" id="cuenta" />  
    </span>
  </td>
  </tr>
<tr>
      <td class="normalNegrita">Cuenta contable: </td>
      <td> 
	<select name="txt_catalogo" class="normal">
	   <option value="0">[Seleccione]</option>
	   <?php
		$sql="select cpat_id,cpat_nombre from sai_cue_pat where cpat_id like '1.1.1.01.02%' and cpat_id not like '%.00'";
		$resultado_set_most=pg_query($conexion,$sql) or die("Error al consultar");  
		while($row=pg_fetch_array($resultado_set_most)) { 
		?>
   	     <option value="<?=$row['cpat_id']?>"><?=$row['cpat_nombre']?></option> 
  		<?php } ?>
  </select>
	<span class="peq_naranja">(*)</span>
	</td>
  </tr>
<tr>
  <td class="normalNegrita">Descripci&oacute;n:</td>
  <td class="titularMedio">
    <textarea name="txt_des_cta" cols="50" rows="3" class="normal" id="txt_des_cta"></textarea>
    <span class="peq_naranja">(*)</span>
  </td>
  </tr>
<tr>
  <td class="normalNegrita">Tipo de cuenta:</td>
      <td>	
	  <select name="txt_tipo_cta" class="normal">
	   <option value="0">[Seleccione]</option>
	   <?php
	    $sql="SELECT tipo_id,tipo_nombre  FROM sai_tipocuenta"; 
		$resultado_set_most=pg_query($conexion,$sql) or die("Error al consultar");  
		while($row=pg_fetch_array($resultado_set_most)) { 
		?>
   	     <option value="<?=$row['tipo_id']?>"><?=$row['tipo_nombre']?></option> 
  		<?php } ?>
  </select>
  <span class="peq_naranja">(*)</span>
  </td>
</tr>
<tr>
  <td class="normalNegrita">Monto de apertura:</td>
  <td class="submenuNegrita"><input name="txt_monto" type="text" class="normal" id="txt_monto" size="17" onkeypress="return validarDecimal(this)" maxlength="16" />   
    <span class="peq_naranja">(*)</span>
   </td>
</tr>
<?php
$ano=date(Y);
$mes=date(m);
$dia=date(d);
$fecha_reg=$dia."/".$mes."/".$ano;
pg_close($conexion);
?>
<tr>
  <td class="normalNegrita">Fecha de registro:</td>
  <td class="submenuNegrita"><input name="fecha_reg" type="text" size="10" class="normal" value="<?php echo $fecha_reg; ?>" readonly="readonly" />
    <span class="peq_naranja">(*)</span>
  </td>
  </tr>
	<tr>
		<td class="normalNegrita">Fecha de apertura en la entidad bancaria:</td>
		<td class="normal" colspan="2">
			<input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse"
 			readonly="readonly"/>
			<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" 	title="Show popup calendar" >
				<img src="../../../js/lib/calendarPopup/img/calendar.gif"  class="cp_img"  alt="Open popup calendar"/>
			</a>*
			<a href="javascript:limpiarFecha(document.form.txt_inicio);">Borrar fecha</a>
						
					
	</td>
	</tr>
<tr>
  <td colspan="2" align="center">
  <input class="normalNegro" type="button" value="Guardar" onclick="javascript:revisar();"/>
</td>
</tr>
</table>
</form></body>
</html>