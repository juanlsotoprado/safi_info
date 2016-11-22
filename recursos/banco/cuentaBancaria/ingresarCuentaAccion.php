<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();    exit;
}ob_end_flush(); 
 

$usua_login=$_SESSION['login'];
$txt_num_cta=trim($_POST['txt_num_cta']);
$ano=date(Y);
$mes=date(m);
$dia=date(d);
$fecha_reg=$ano."-".$mes."-".$dia;
$fecha_reg1=$dia."/".$mes."/".$ano;
$txt_banco=trim($_POST['txt_banco']);
$cuenta=$txt_banco.$txt_num_cta;
$txt_des_cta=trim($_POST['txt_des_cta']);
$fecha_salida  = trim($_POST['txt_inicio']);
$txt_tipo_cta=trim($_POST['txt_tipo_cta']);

if (!(empty($fecha_salida))){
	$ano=substr($fecha_salida,6,9);
	$mes=substr($fecha_salida,3,2);
	$dia=substr($fecha_salida,0,2);
	$fecha_ini=$ano."-".$mes."-".$dia;
}
else $fecha_ini='';


$txt_catalogo=trim($_POST['txt_catalogo']);
$monto=trim($_POST['txt_monto']);
$sql="select * from sai_insert_apertura_cuenta ('$cuenta','$txt_tipo_cta','$txt_banco','2','$txt_des_cta','$fecha_ini','$txt_catalogo','$ano','$monto','$fecha_reg','1','$usua_login') as resultado_set(text)";
$result=pg_query($conexion,$sql) or die("ERROR: No se pudo realizar el ingreso de la cuenta bancaria $sql"); 
if ($r=pg_fetch_array($result)) {
	$v=trim($r['text']);
    if ($v=='0')  {
?>
<script language="javascript">
		alert("La cuenta que introdujo ya se encuentra registrada");
		window.location="ingresarCuenta.php"
</script>
<?php }
else {
$sql_sal="insert into sai_ctabanco_saldo (ctab_numero, monto_debe, monto_haber, fecha_saldo, docg_id) values ('".$cuenta."',0,".$monto.",'".$fecha_reg."','sb-".$ano."') ";
$result_sal=pg_query($conexion,$sql_sal) or die("ERROR: No se pudo realizar la Consulta $sql_sal");
}

 }?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI:Ingresar Cuenta Bancaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="50%" align="center" background="../../../imagenes/fondo_tabla.gif" bgcolor="#FFFFFF" class="tablaalertas" >
<tr class="td_gray">
  <td colspan="2" class="normalNegroNegrita">Registro de cuenta bancaria </td>
</tr>
	   <?php
	    $sql="SELECT banc_id,banc_nombre FROM sai_banco where banc_id='".$txt_banco."'";
		$resultado=pg_query($conexion,$sql) or die("Error al consultar el nombre de la entidad bancaria");  
		if($row=pg_fetch_array($resultado)) 
			$banco=trim($row['banc_nombre']);  
		?>
<tr>
	<td class="normalNegrita">Banco:</td>
  <td class="normal"><?php echo $banco; ?></td>
  </tr>
<tr>
 <td class="normalNegrita">N&uacute;mero de Cuenta:</td>
  <td class="normal"><?php echo $cuenta?></td>
  </tr>
	   <?php
	    $sql="SELECT cpat_id,cpat_nombre FROM sai_cue_pat where cpat_id='".$txt_catalogo."'"; 
		$resultado=pg_query($conexion,$sql) or die("Error al consultar");  
		if($row=pg_fetch_array($resultado)) 
		$cuenta_contable=trim($row['cpat_nombre']); 
		?>
<tr>
      <td class="normalNegrita">Nombre Cuenta Contable: </td>
  <td class="normal"><?php echo $cuenta_contable; ?></td>
  </tr>
<tr>
  <td class="normalNegrita">Descripci&oacute;n:</td>
  <td class="normal"><?php echo $txt_des_cta; ?></td>
  </tr>
	   <?php
	    $sql="SELECT tipo_id,tipo_nombre FROM sai_tipocuenta where tipo_id='".$txt_tipo_cta."'"; 
		$resultado=pg_query($conexion,$sql) or die("Error al consultar");  
		if($row=pg_fetch_array($resultado))
		$tipo_cuenta=trim($row['tipo_nombre']);  
		?>
<tr>
  <td class="normalNegrita">Tipo de Cuenta:</td>
  <td class="normal"><?php echo $tipo_cuenta; ?></td>
  </tr>
<tr>
  <td class="normalNegrita">Monto de Apertura:</td>
  <td class="normal"><?php echo $monto; ?></td>
  </tr>
<tr>
  <td class="normalNegrita">Fecha de Registro:</td>
  <td class="normal"><?php echo $fecha_reg1;?></td>
  </tr>  
<tr>
 	<td class="normalNegrita">Fecha de apertura en la entidad bancaria:</td>
  <td class="normal"><?php echo $fecha_salida; ?></td>
  </tr>
</table>
</form>
</body>
</html>
<?php pg_close($conexion); ?>