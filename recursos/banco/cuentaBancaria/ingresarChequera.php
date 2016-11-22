<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();    exit;
}ob_end_flush(); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Ingresar Chequera</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../../js/funciones.js"> </script>
<script language="JavaScript" type="text/JavaScript">

function regresar() {
	history.back();
}

function revisar(codigo1,codigo2,cuenta) {   
    if(document.form1.txt_cheq_inicio.value==""  ) {
	  alert("Debe ingresar el n\u00famero de cheque de inicio");
	  document.form1.txt_cheq_inicio.focus();
	  return;
    }
    if(document.form1.txt_cheq_fin.value==""  ) {
	  alert("Debe ingresar el n\u00famero de cheque fin");
	  document.form1.txt_cheq_fin.focus();
	  return;
    }
    if(parseInt(document.form1.txt_cheq_inicio.value,10)>parseInt(document.form1.txt_cheq_fin.value,10)) {
	  alert("El n\u00famero de cheque fin debe ser mayor al inicial");
	  document.form1.txt_cheq_inicio.focus();
	  return;
    }
    if(confirm("Datos introducidos de manera correcta. Est\u00e1 seguro que desea continuar?")) {
    	 var loriginal=document.form1.txt_cheq_inicio.value.length;
    	 var numero=document.form1.txt_cheq_inicio.value.replace(/^0+/, '');
    	 var lmodificado=numero.length;
    	 var resta = loriginal - lmodificado;
    	 var ceros="";
    	 for (i=0;i<resta;i++) {
			ceros += "0";
       	 }
    	document.form1.resta.value=ceros;
		document.form1.submit();
   }
}
</script>

</head>
<body>
<form name="form1" method="post" action="ingresarChequeraAccion.php">
<?php 
$codigo=trim($_GET['codigo']);  
$sql="SELECT cb.cpat_id, to_char(cb.ctab_fechacierrereg,'DD-MM-YYYY') as fecha_cierre_sistema, to_char(cb.ctab_fechaapert,'DD-MM-YYYY') as fecha_apertura, to_char(cb.ctab_fechareg,'DD-MM-YYYY') as fecha_registro, to_char(cb.ctab_fechacierrereg,'DD-MM-YYYY') as fecha_cierre, cb.ctab_numero, cb.banc_id, b.banc_nombre, e.esta_nombre, cb.ctab_ano, cb.ctab_descripcion, cb.ctab_estatus, tc.tipo_nombre, cb.tipo_id, ctab_saldoinicial from sai_ctabanco cb, sai_banco b, sai_estado e, sai_tipocuenta tc where cb.banc_id=b.banc_id and cb.tipo_id=tc.tipo_id and cb.ctab_estatus=e.esta_id and cb.ctab_numero='".$codigo."'";
$resultado=pg_query($conexion,$sql);
if($row=pg_fetch_array($resultado)) {
	$banc_id=trim($row['banc_id']);	
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
?>
<table width="40%" align="center" background="../../../imagenes/fondo_tabla.gif" bgcolor="#FFFFFF" class="tablaalertas" >
<tr class="td_gray">
  <td colspan="2" class="normalNegroNegrita">Ingresar chequera correlativa</td>
</tr>
    <tr class="normal">
      <td class="normalNegrita">Banco:</td>
      <td><?php echo $banco;?></td>
    </tr>
    <tr class="normal">
      <td class="normalNegrita">N&uacute;mero de Cuenta:</td>
      <td><?php echo $ctab_numero;?><input name="cuenta" type="hidden" id="cuenta" value="<?php echo $ctab_numero;?>" /></td>
    </tr>
    <tr class="normal">
      <td class="normalNegrita">Cuenta Contable:</td>
      <td><?php echo $cpat_id;?></td>
    </tr>
    <tr class="normal">
      <td class="normalNegrita">Descripci&oacute;n:</td>
      <td height="20"><?php echo $ctab_descripcion;?></td>
    </tr>
    <tr class="normal">
      <td class="normalNegrita">Tipo de Cuenta:</td>
      <td><?php echo $tipo_cuenta;?></td>
    </tr>
   <tr class="normal">
      <td class="normalNegrita">Monto de Apertura:</td>
      <td><?php echo number_format($saldo,2,'.',',');?></td>
    </tr>
    <tr class="normal">
      <td class="normalNegrita">Fecha de Registro: </td>
      <td><?php echo $fecha_registro;?></td>
    </tr>
    <tr class="normal">
      <td class="normalNegrita">Fecha de Apertura: </td>
      <td><?php echo $fecha_apertura;?></td>
    </tr>
    <tr class="normal">
      <td class="normalNegrita">Nro. Cheque inicio: </td>
      <td><input name="txt_cheq_inicio" type="text" class="normal" id="txt_cheq_inicio" size="10"/></td>
    </tr>
    <tr class="normal">
      <td class="normalNegrita">Nro. Cheque fin:</td>
     <td><input name="txt_cheq_fin" type="text" class="normal" id="txt_cheq_fin" size="10"/>
<input name="txt_cuenta" type="hidden" id="txt_cuenta" value="<?php echo $codigo;?>" />
<input name="txt_banc_id" type="hidden" id="txt_banc_id" value="<?php echo $banc_id;?>" />
</td>
  </tr>
<tr>
<td class="normal" colspan="2" align="center">
 <input type="hidden" name="resta" id="resta" value=""/>
<input class="normalNegro" type="button" value="Guardar" onclick="javascript:revisar(document.form1.txt_cheq_inicio.value,document.form1.txt_cheq_fin.value,document.form1.txt_cuenta.value);"/>
</td>
</tr>
</table>
	 <?php } pg_close($conexion); ?>
</form>
<br/>
</body>
</html>