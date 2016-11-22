<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI:Ejecutar modificacion de  partida</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="../../../js/funciones.js"> </script>
</head>
<body>
<?php
$part_id=trim($_POST['txt_cod_partida']);
$pres_anno=trim($_POST['txt_pres_anno']);
$part_nombre=trim($_POST['txt_nombre']);
$part_observa=trim($_POST['txt_observa']);
$esta_id=trim($_POST['opt_estado']);
	
$sql_reg="select * from sai_modificar_partida('".$part_id."', ".$pres_anno.", '".$part_nombre."', '1','".$part_observa."',".$esta_id.")"; 
$resultado_reg=pg_query($conexion,$sql_reg);
if($row_reg=pg_fetch_array($resultado_reg)) {
?>
	 <table width="50%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray"> 
	  <td colspan="2" class="normalNegroNegrita">PARTIDA MODIFICADA</td>
	</tr>
	<tr>
	<td class="normalNegrita">C&oacute;digo:</td>
	<td class="normal"><?php echo $part_id; ?></td>
	</tr>
	<tr>
	<td class="normalNegrita">Nombre:</td>
	<td class="normal"><?php echo $part_nombre; ?></td>
	</tr>
	<tr> 
	<td class="normalNegrita">A&ntilde;o de Presupuesto:</td>
	<td class="normal"><?php echo $pres_anno; ?></td>
	</tr>
	<?php if($part_observa!=""){?>
	<tr> 
	<td class="normalNegrita">Observaciones:</td>
	<td class="normal"><?php echo $part_observa; ?></td>
	</tr>
	<?php }?>
	<tr> 
	<td class="normalNegrita">Estado del recurso:</td>
	<td class="normal">
	   <?php
	    if($esta_id==1) echo "Activo";
	    else echo "Inactivo";
	   ?>	
	</td>
	</tr>
	<tr>
	<td colspan="2" align="center" class="normal">
	<br>
	Solicitud generada el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br><br>
	<a href="javascript:window.print()" class="normal"><img src="../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br><br>
	</td>
	</tr>
	</table>
	<?php
	}
	 else  {   ?>
		<table width="50%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="normal"> 
		<td class="normalNegroNegrita">MODIFICAR PARTIDA PRESUPUESTARIA </td>
		</tr>
		<tr>
		<td class="normal" align="center">
		<img src="../../imagenes/vineta_azul.gif" width="11" height="7"></img>
		Ha ocurrido un error al ingresar los datos de la partida<br>
		<?php echo(pg_errormessage($conexion)); ?><br>
		<img src="../../../imagenes/mano_bad.gif" width="31" height="38">
		</td>
		</tr>
		</table>
<?php } pg_close($conexion); ?>   
</body>
</html>