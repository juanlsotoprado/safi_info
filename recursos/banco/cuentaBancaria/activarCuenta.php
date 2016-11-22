<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();    exit;
}
ob_end_flush(); 
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Habilitar Cuenta Bancaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">	g_Calendar.setDateFormat('dd/mm/yyyy'); </script>

<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../../js/funciones.js"> </script>
<script language="JavaScript" type="text/JavaScript">
function regresar() {
	history.back();
}

function revisar() { 
	if(document.form.fecha_apertura.value=='') {
		alert("Debe seleccionar la fecha apertura de la cuenta en el banco");
		 document.form.fecha_apertura.focus();
	}	
	else 
	if(confirm("Est\u00e1 seguro que desea activar nuevamente la cuenta?"))  
		document.form.submit();
}	
</script>
</head>
<body>
<form name="form" method="post" action="activarCuentaAccion.php">
<?php 
$codigo=$_GET["codigo"]; 
$sql="SELECT to_char(cb.ctab_fechacierrereg,'DD-MM-YYYY') as fecha_cierre_sistema, to_char(cb.ctab_fechaapert,'DD-MM-YYYY') as fecha_apertura, to_char(cb.ctab_fechareg,'DD-MM-YYYY') as fecha_registro, to_char(cb.ctab_fechacierrereg,'DD-MM-YYYY') as fecha_cierre, cb.ctab_numero, cb.banc_id, b.banc_nombre, e.esta_nombre, cb.ctab_ano, cb.ctab_descripcion, cb.ctab_estatus, tc.tipo_nombre, cb.tipo_id, cb.cpat_id, ctab_saldoinicial from sai_ctabanco cb, sai_banco b, sai_estado e, sai_tipocuenta tc where cb.banc_id=b.banc_id and cb.tipo_id=tc.tipo_id and cb.ctab_estatus=e.esta_id and cb.ctab_numero='".$codigo."'"; 
$resultado=pg_query($conexion,$sql) or die("Error al consultar saldo de Cuentas");  

$hab=0;
$deb=0;
$saldo=0;

if ($row=pg_fetch_array($resultado))  {   
	$banco=trim($row['banc_nombre']);
	$ctab_numero=trim($row['ctab_numero']);
	$cpat_id=trim($row['cpat_id']);
	$ctab_descripcion=trim($row['ctab_descripcion']);
	$fecha_apertura=trim($row['fecha_apertura']);
	$fecha_registro=trim($row['fecha_registro']);
	$fecha_cierre=trim($row['fecha_cierre']);
	$fecha_cierre_sistema=trim($row['fecha_cierre_sistema']);	
	$tipo_cuenta=trim($row['tipo_nombre']);
	$saldo=trim($row['ctab_saldoinicial']);
	$estado=$row['esta_nombre'];
	$esta_id=$row['ctab_estatus'];
	$acti="Activar cuenta bancaria";
?>

<table width="60%" align="center" background="../../../imagenes/fondo_tabla.gif" bgcolor="#FFFFFF" class="tablaalertas" >
<tr class="td_gray">
    <td colspan="2" class="normalNegroNegrita"><?php echo $acti;?></td>
</tr>
<tr class="normal"> 
<td class="normalNegrita"> Banco:</td>
<td><?php echo $banco;?></td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">N&uacute;mero de cuenta:</td>
<td><?php echo $ctab_numero;?></td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Cuenta contable:</td>
<td><?php echo $cpat_id;?></td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Descripci&oacute;n:</td>
<td><?php echo $ctab_descripcion;?></td>
</tr> 
	<tr class="normal"> 
	<td class="normalNegrita">Tipo de cuenta:</td>
	<td><?php echo $tipo_cuenta;?></td>
	</tr> 
<tr class="normal"> 
<td class="normalNegrita">Monto de apertura:</td>
<td><?php echo number_format($saldo,2,'.',',');?></td>
</tr>
<?php if($fecha_cierre!='') {?>
<tr class="normal"> 
	<td class="normalNegrita">Fecha de registro en el sistema:</td>
	<td><?php echo $fecha_registro;?><input name="cierre" type="hidden" id="cierre" value="<?php echo $fecha_reg1;?>" /></td>
</tr> 
<tr class="normal"> 
	<td class="normalNegrita">Fecha de Apertura en el Banco:</td>
		<td class="normalNegrita" colspan="2">
			<input type="text" size="10" id="fecha_apertura" name="fecha_apertura" class="dateparse"
 			readonly="readonly"/>
			<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha_apertura');" 	title="Show popup calendar" >
				<img src="../../../js/lib/calendarPopup/img/calendar.gif"  class="cp_img"  alt="Open popup calendar"/>
			</a>	
					
	</td></tr> 
<?php } if($fecha_cierre=='') {?>
<tr class="normal"> 
	<td class="normalNegrita"> Fecha de registro en el sistema:</td>
	<td><?php echo $fecha_registro;?><input name="registro" type="hidden" id="registro" value="<?php echo $fecha_registro;?>" /></td>
</tr> 
<tr class="normal"> 
	<td class="normalNegrita">Fecha de apertura en el banco:</td>
	<td><?php echo $fecha_apertura;?><input name="apertura" type="hidden" id="apertura" value="<?php echo $fecha_apertura;?>" /></td>
</tr> 
	<tr class="normal"> 
	<td class="normalNegrita">Fecha de cierre en el sistema:</td>
	<td><?php echo $fecha_cierre_sistema;?><input name="cierre" type="hidden" id="cierre" value="<?php echo $fecha_cierre_sistema;?>" /></td>
	</tr> 
<?php }?>
<tr>   
	<td colspan="2"><div style="visibility:hidden">
       	<input name="ctab_estatus" type="text" id="ctab_estatus" class="normal" value="<?php echo $ctab_estatus;?>" /> 
		<input name="cuenta" type="text" id="cuenta" class="normal" value="<?php echo $ctab_numero;?>" />
		<input name="saldo_act" type="text" id="saldo_act" class="normal" value="<?php echo $saldo_act;?>" />			
	 </div>
  </td>
</tr>
<tr>
  <td height="15" colspan="2" align="center">
<input class="normalNegro" type="button" value="Activar" onclick="javascript:revisar();"/>
<input class="normalNegro" type="button" value="Regresar" onclick="javascript:regresar();"/>
</td>
</tr>
</table>
	 <?php } pg_close($conexion);?>
</form></body>
</html>