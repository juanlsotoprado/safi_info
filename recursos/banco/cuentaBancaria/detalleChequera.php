<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush(); 
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar Chequera</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">
function regresar() {
	history.back();
}
function ejecutar_varios(codigo1,codigo2) {
  window.location="buscar.php?slc_provee="+codigo1
}
function detalle(codigo) {
    url="acta_detalle_chequera.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}
</script>
</head>
<body>
<?php 
$codigo=trim($_GET["codigo"]);
$separar = explode(',',$codigo);
$chequera=trim($separar[0]);
//$banco=trim($_GET["banco"]);
$banco=trim($separar[1]);
//$cuenta=trim($_GET["cuenta"]);
$cuenta=trim($separar[2]);
$sql="SELECT b.banc_nombre, e.esta_nombre, cq.ctab_numero, ch.beneficiario_cheque, ch.nro_cheque,ch.estatus_cheque,ch.monto_cheque,ch.nro_chequera,to_char(ch.fechaemision_cheque,'DD-MM-YYYY') as fecha_emision FROM sai_cheque ch, sai_chequera cq, sai_banco b, sai_estado e where ch.nro_chequera=cq.nro_chequera and cq.banc_id=b.banc_id and ch.estatus_cheque=e.esta_id and ch.nro_chequera='".$chequera."' order by ch.nro_cheque";
$resultado=pg_query($conexion,$sql) or die("Error al realizar la consulta de la cuenta bancaria");
$nroFilas = pg_num_rows($resultado);
if (($nroFilas<=0) && (($_GET['codigo']!=0))) echo "<center><font color='#003399'>"."Actualmente no se tienen cheques asociados a esta chequera"."</font></center>";
//else echo "<div class='normalNegrita' align='center'>"."Chequera Nro.".$chequera."<br>";
if ($nroFilas>0) {	?>
			<div align="center" class="normalNegroNegrita">Cheques de la chequera <?php echo $chequera;?> de la cuenta nro:<?=$cuenta;?> del <?php echo $banco;?></div>
						<br></br>
	<table width="80%" align="center" background="../../../imagenes/fondo_tabla.gif" bgcolor="#FFFFFF" class="tablaalertas" >
	 <tr class="td_gray"> 
	  <td class="normalNegroNegrita">N&uacute;mero de Cheque</td>
	         <td class="normalNegroNegrita">Nro. Chequera</td>
			  <td class="normalNegroNegrita">Beneficiario</td>
			  <td class="normalNegroNegrita">Monto</td>
			  <td class="normalNegroNegrita">Fecha de Emisi&oacute;n</td>
			  <td class="normalNegroNegrita">Estado</td>
			  </tr>
				<?php
  				while ($row=pg_fetch_array($resultado)) 				{   
				    $nro_cheque=trim($row['nro_cheque']);
				    $chequera=trim($row['nro_chequera']);
				    $beneficiario=trim($row['beneficiario_cheque']);				    
				    $estado=trim($row['esta_nombre']);
				    $monto_emi=number_format($row['monto_cheque'],2,',','.');
				    $fecha=trim($row['fecha_emision']);
					if (($monto=='') and ($fecha=='')) {
						$monto_emi="No emitido";
						$fecha="No emitido";
						$beneficiario="No emitido";
					}
	 		?>
					<tr class="normal"> 
  					<td align="center"><?=$nro_cheque?></td>
  					<td align="center"><?=$chequera?></td>
  					<td><?=$beneficiario?></td>
  					<td align="right"><?=$monto_emi?></td>
  					<td align="center"><?=$fecha?></td>
					<td align="center"><?=$estado?></td>
					</tr>
	   <?php } ?>  
<tr>
<td colspan="6" align="center"><br/>
<input class="normalNegro" type="button" value="Regresar" onclick="javascript:regresar();"/></td>
</tr>
  </table>
   <?php } pg_close($conexion); ?>
</form>
</body>
</html>