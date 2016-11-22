<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Anular Conciliaci&oacute;n Bancaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../../js/funciones.js"> </script>
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript" src="../../../js/lib/jquery/plugins/jquery.min.js"></script>
<script type="text/javascript">	g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script language="JavaScript" type="text/JavaScript">
function abrir_selecion() {
	url = "sel_fondo.php";
	<?  if (!isset($_SESSION["var_fondo"])) { ?>
	newwindow=window.open(url,'fondo','height=210,width=520,scrollbars=yes,resizable=yes,status=no');
	if (window.focus) {newwindow.focus()}
	<? } ?>
}

function validar() {

	if(document.form1.fecha.value=="") {	
		alert(" Debe seleccionar la fecha real del documento ");
		return;
	}
	else {
		var documentos = "";		
		anoFechaConciliacion = parseInt(document.form1.fecha.value.substring(6, 10), 10);
		mesFechaConciliacion = parseInt(document.form1.fecha.value.substring(3, 5), 10);
		diaFechaConciliacion = parseInt(document.form1.fecha.value.substring(0, 2), 10);
		
		i=0;		
		resultado = true;
		$('input[name="solicitud[]"]:checked').each(function (id,element){
			i++;
			fechaElemento = new Date($(element).parents('tr').find('td.fecha').html());
			mesFechaElemento = parseInt($(element).parents('tr').find('td.fecha').html().substring(3, 5), 10);
			anoFechaElemento = parseInt($(element).parents('tr').find('td.fecha').html().substring(6, 10), 10);

			if((anoFechaElemento == anoFechaConciliacion && mesFechaConciliacion<mesFechaElemento) || (anoFechaElemento > anoFechaConciliacion)) {
				resultado = false;
				documentos = documentos+" "+element.value;
			}
			

		});

		if (resultado && i>0) {
			document.form1.action = "conciliarChequeAccion.php";
			document.form1.submit();
		}
		else if (i<1){
			alert("Debe seleccionar al menos un documento para conciliar");
		}
		else{
			alert("No debe seleccionar documentos con meses posteriores al mes en que se desea conciliar: "+documentos);
		}
		
	}
}

function validar2() {

	if(document.form1.fecha.value=="") {	
		alert(" Debe seleccionar la fecha real del documento ");
		return;
	}
	else {
		var documentos = "";
		i=0;		
		anoFechaConciliacion = parseInt(document.form1.fecha.value.substring(6, 10), 10);
		mesFechaConciliacion = parseInt(document.form1.fecha.value.substring(3, 5), 10);
		diaFechaConciliacion = parseInt(document.form1.fecha.value.substring(0, 2), 10);
		
		i=0;		
		resultado = true;
		$('input[name="solicitud[]"]:checked').each(function (id,element){
			i++;			
			fechaElemento = new Date($(element).parents('tr').find('td.fecha').html());
			mesFechaElemento = parseInt($(element).parents('tr').find('td.fecha').html().substring(3, 5), 10);
			
			if(mesFechaConciliacion<mesFechaElemento) {
				resultado = false;
				documentos = documentos+" "+element.value;
			}

		});
		if (resultado && i>0) {
			document.form1.action = "conciliarTransferenciaAccion.php";
			document.form1.submit();
		}
		else if (i<1){
			alert("Debe seleccionar al menos un documento para conciliar");
		}
		else {
			alert("No debe seleccionar documentos con meses posteriores al mes en que se desea conciliar: "+documentos);
		}
		
	}
}

function validar3() {

	if(document.form1.fecha.value=="") {	
		alert(" Debe seleccionar la fecha real del documento ");
		return;
	}
	else {
		var documentos = "";		
		anoFechaConciliacion = parseInt(document.form1.fecha.value.substring(6, 10), 10);
		mesFechaConciliacion = parseInt(document.form1.fecha.value.substring(3, 5), 10);
		diaFechaConciliacion = parseInt(document.form1.fecha.value.substring(0, 2), 10);
		
		i=0;	
		resultado = true;
		$('input[name="solicitud[]"]:checked').each(function (id,element){
			i++;
			fechaElemento = new Date($(element).parents('tr').find('td.fecha').html());
			mesFechaElemento = parseInt($(element).parents('tr').find('td.fecha').html().substring(3, 5), 10);
			
			if(mesFechaConciliacion<mesFechaElemento) {
				resultado = false;
				documentos = documentos+" "+element.value;
			}

		});
		if (resultado && i>0) {
			document.form1.action = "conciliarCodiAccion.php";
			document.form1.submit();
		}
		else if (i<1){
			alert("Debe seleccionar al menos un documento para conciliar");
		}
		else{
			alert("No debe seleccionar documentos con meses posteriores al mes en que se desea conciliar: "+documentos);
		}
		
	}
}

</script>	
</head>
<body>
<br />
<br />
<form name="form1" method="post" action="conciliar.php">
<table width="50%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
    <tr class="td_gray">
      <td colspan="2" class="normalNegroNegrita" align="left">CONCILIACI&Oacute;N BANCARIA </td>
    </tr>
    <tr>
      <td align="center" class="normalNegrita">Cuenta Bancaria:</td>
      <td>
 <select name="txt_cuenta" class="normal" id="txt_cuenta">
     <option value="0">::: Seleccione :::</option>
      <?php 
	        //busqueda de las cuentas 
			$sql="SELECT ctab_numero, ctab_descripcion FROM  sai_ctabanco"; 
			$resultado=pg_query($conexion,$sql) or die("Error al mostrar"); 
			while($row=pg_fetch_array($resultado)) { 
				$tifo=trim($row['ctab_numero']);
				$nombre_tifo=$row['ctab_descripcion'];
				echo $tifo;
			?>
      <option value="<?php echo $tifo;?>"><?php echo $tifo;?>-<?php echo $nombre_tifo;?></option>
      <?php 
			} //Finaliza el while*/
	  ?>
    </select>
      </td>
      <tr><td colspan="2">&nbsp;</td></tr>
  </tr>
<tr>
<td colspan="2" align="center">
<input type="hidden" name="chkboton" id="chkboton" value="" />
<input type="button" value="Listar Cheque" onclick="document.form1.chkboton.value='cheque';document.form1.submit();"/>
<input type="button" value="Listar Transferencia" onclick="document.form1.chkboton.value='transferencia'; document.form1.submit();"/>
<input type="button" value="Listar Codi" onclick="document.form1.chkboton.value='codi';document.form1.submit();"/>
 </td>
    </tr>
  </table>
<?	
if (isset($_POST["txt_cuenta"]) && strcmp($_POST["chkboton"],"cheque")==0) {
?>
<br /><br />
<div align="center">
<font class="normal">Seleccione la fecha efectiva en que se realiz&oacute; el pago: 
 <input type="text" size="10" id="fecha" name="fecha" class="dateparse"
readonly="readonly" value="<?php echo $_POST["fecha"];?>"/>
					<a href="javascript:void(0);" 

onclick="g_Calendar.show(event, 'fecha');" 
						title="Show popup calendar">
						<img src="../../../js/lib/calendarPopup/img/calendar.gif" 
							class="cp_img" 
							alt="Open popup calendar"/>
					</a>
<br />
 </font>

<br />
<input type="button" value="Conciliar" onclick="javascript:validar();"/>
<?php 

$sql = "SELECT pc.docg_id AS docg_id, 
			TO_CHAR(mb.fechaemision_cheque, 'DD/MM/YYYY') AS fecha, 
			pc.pgch_id AS pgch_id, 
			sh.nro_cheque AS nro_cheque, 
			sh.monto_cheque AS monto,
			conciliado
	FROM sai_pago_cheque pc
	INNER JOIN sai_cheque sh ON (sh.id_cheque = pc.id_nro_cheque)
	INNER JOIN sai_mov_cta_banco mb ON (mb.docg_id = pc.docg_id)
	WHERE  sh.nro_cheque = mb.nro_cheque AND
		pc.docg_id = sh.docg_id AND
		mb.conciliado = 51 AND 
		pc.esta_id != 15 AND
		mb.ctab_numero = '".$_POST["txt_cuenta"]."'
	ORDER BY mb.fechaemision_cheque";

$resultado=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");  
?>
<div class="normalNegroNegrita">Cuenta bancaria nro.<?php echo $_POST["txt_cuenta"];?></div>
<input type="hidden" name="hid_cuenta" id="hid_cuenta" value="<?php echo $_POST["txt_cuenta"];?>"></input>
<table width="70%" border="0" class="tablaalertas">

   <tr class="td_gray">
     <td class="normalNegroNegrita"> &nbsp;</td>
     <td class="normalNegroNegrita">Nro. Cheque</td>
     <td class="normalNegroNegrita">Monto</td>
     <td class="normalNegroNegrita">C&oacute;digo sopg</td>
     <td class="normalNegroNegrita">C&oacute;digo pgch</td>
     <td class="normalNegroNegrita">Fecha pagado</td>
   </tr>
<?php //Inicio del While 
$i=0;
while($rowor=pg_fetch_array($resultado)) {
	$i++;
?>
  <tr >
    <td align="center" class="normal"><?echo $i;?><input type="checkbox" name="solicitud[]" value="<?php echo $rowor['pgch_id'];?>" /> </td>
 <td><div align="center" class="normal"><?php echo $rowor['nro_cheque'];?></div></td>
 <td align="right" class="normal"><?php echo number_format($rowor['monto'],2,',','.');?></td>
    <td><div align="center" class="normal"><?php echo $rowor['docg_id'];?></div></td>
<td align="center" class="normal"><?php echo $rowor['pgch_id'];?></td>
 <td align="right" class="normal fecha"><?php echo $rowor['fecha'];?></td>

  </tr>
  <?php }?>
<tr><td><input type="hidden" name="contador" id="contador" value="<?echo $i;?>" /></td></tr>
</table>
<br />
<input type="button" value="Conciliar" onclick="javascript:validar();"/>
</div>
<?} else if (isset($_POST["txt_cuenta"]) && strcmp($_POST["chkboton"],"transferencia")==0){?>
<br /><br />
<div align="center">
<font class="normal">Seleccione la fecha efectiva en que se realiz&oacute; la transferencia: 
 <input type="text" size="10" id="fecha" name="fecha" class="dateparse"
readonly="readonly" value="<?php echo $_POST["fecha"];?>"/>
					<a href="javascript:void(0);" 

onclick="g_Calendar.show(event, 'fecha');" 
						title="Show popup calendar">
						<img src="../../../js/lib/calendarPopup/img/calendar.gif" 
							class="cp_img" 
							alt="Open popup calendar"/>
					</a>
<br />
 </font>

<br />
<input type="button" value="Conciliar" onclick="javascript:validar2();"/>
<?php 

$sql = "SELECT pt.docg_id AS docg_id, 
		TO_CHAR(mb.fechaemision_cheque, 'DD/MM/YYYY') AS fecha, 
		pt.trans_id AS trans_id, 
		pt.nro_referencia, 
		pt.trans_monto AS monto
	FROM sai_pago_transferencia pt
	INNER JOIN sai_mov_cta_banco mb ON (mb.docg_id = pt.docg_id)
	WHERE pt.esta_id != 15 AND
		mb.conciliado = 51 AND
		pt.nro_referencia = mb.nro_cheque AND
		pt.nro_cuenta_emisor  = '".$_POST["txt_cuenta"]."' AND
		mb.ctab_numero  = '".$_POST["txt_cuenta"]."'
		ORDER BY mb.fechaemision_cheque";
$resultado=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");  
?>

<table width="60%" border="0" class="tablaalertas">
   <tr class="td_gray">
     <td class="normalNegroNegrita"> &nbsp;</td>
     <td class="normalNegroNegrita">Nro. Referencia</td>
     <td class="normalNegroNegrita">Monto</td>
     <td class="normalNegroNegrita">C&oacute;digo sopg</td>
     <td class="normalNegroNegrita">C&oacute;digo tran</td>
     <td class="normalNegroNegrita">Fecha pagado</td>
   </tr>
<?php //Inicio del While 
$i=0;
while($rowor=pg_fetch_array($resultado)) {
	$i++;
?>
  <tr>
    <td align="center" class="normal"><?echo $i;?><input type="checkbox" name="solicitud[]" value="<?php echo $rowor['trans_id'];?>" /> </td>
    <td><div align="center" class="normal"><?php echo $rowor['nro_referencia'];?></div></td>
    <td><div align="center" class="normal"><?php echo number_format($rowor['monto'], 2);?></div></td>
    <td><div align="center" class="normal"><?php echo $rowor['docg_id'];?></div></td>
    <td align="center" class="normal"><?php echo $rowor['trans_id'];?></td>
    <td align="center" class="normal fecha"><?php echo $rowor['fecha'];?></td>
  </tr>
  <?php }?>
<tr><td><input type="hidden" name="contador" id="contador" value="<?echo $i;?>" /></td></tr>
</table>
<br />
<input type="button" value="Conciliar" onclick="javascript:validar2();"/>
</div>
<?}
 else if (isset($_POST["txt_cuenta"]) && strcmp($_POST["chkboton"],"codi")==0){?>
<br /><br />
<div align="center">
<font class="normal">Seleccione la fecha efectiva en que se realiz&oacute; el asiento manual: 
 <input type="text" size="10" id="fecha" name="fecha" class="dateparse"
	readonly="readonly" value="<?php echo $_POST["fecha"];?>"/>
	<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha');" 
						title="Show popup calendar">
						<img src="../../../js/lib/calendarPopup/img/calendar.gif" 
							class="cp_img" 
							alt="Open popup calendar"/></a>
<br />
 </font>
<br />
<input type="button" value="Conciliar" onclick="javascript:validar3();"/>
<?php 
$sql = "SELECT c.comp_id, 
			c.nro_referencia, 
			TO_CHAR(c.comp_fec, 'DD/MM/YYYY') AS fecha, 
			r.rcomp_debe + r.rcomp_haber AS monto
		FROM sai_comp_diario c
		INNER JOIN sai_reng_comp r ON (c.comp_id = r.comp_id)
		INNER JOIN sai_mov_cta_banco mb ON (mb.docg_id = c.comp_id)
		WHERE  c.comp_id like 'codi-%' AND
			r.cpat_id like '1.1.1.01.02%' AND
			r.cpat_id NOT LIKE '1.1.1.01.02.02.04%' AND 
			c.esta_id != 15 AND 
			mb.conciliado=51 AND
			r.cpat_id IN (
					SELECT cpat_id 
					FROM sai_ctabanco
					WHERE ctab_numero='".$_POST["txt_cuenta"]."')
		ORDER BY c.comp_fec";
$resultado=pg_query($conexion,$sql) or die("Error al consultar el asiento manual");  
?>
<table width="60%" border="0" class="tablaalertas">
   <tr class="td_gray">
     <td class="normalNegroNegrita"> &nbsp;</td>
     <td class="normalNegroNegrita">Nro. Referencia</td>
     <td class="normalNegroNegrita">Monto</td>
     <td class="normalNegroNegrita">C&oacute;digo asiento</td>
     <td class="normalNegroNegrita">Fecha pagado</td>
   </tr>
<?php //Inicio del While 
$i=0;
while($rowor=pg_fetch_array($resultado)) {
	$i++;
?>
  <tr>
    <td align="center" class="normal"><?echo $i;?><input type="checkbox" id="solicitud[]" name="solicitud[]" value="<?php echo $rowor['comp_id'];?>" /> </td>
    <td><div align="center" class="normal"><?php echo $rowor['nro_referencia'];?></div></td>
    <td><div align="center" class="normal"><?php echo number_format($rowor['monto'], 2);?></div></td>
    <td><div align="center" class="normal"><?php echo $rowor['comp_id'];?></div></td>
    <td align="center" class="normal fecha" ><?php echo $rowor['fecha'];?></td>
  </tr>
  <?php }?>
<tr><td><input type="hidden" name="contador" id="contador" value="<?echo $i;?>" /></td></tr>
</table>
<br />
<input type="button" value="Conciliar" onclick="javascript:validar3();"/>
</div>
<?} pg_close($conexion);?>
</form>
</body>
</html>