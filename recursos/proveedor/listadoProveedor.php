<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
$rif = "";
if (isset($_REQUEST['rif']) && $_REQUEST['rif'] != "") {
	$rif = $_REQUEST['rif'];
}
$codigo = "";
if (isset($_REQUEST['codigo']) && $_REQUEST['codigo'] != "") {
	$codigo = $_REQUEST['codigo'];
}
$nombre = "";
if (isset($_REQUEST['nombre']) && $_REQUEST['nombre'] != "") {
	$nombre = $_REQUEST['nombre'];
}
$estado = "";
if (isset($_REQUEST['estado']) && $_REQUEST['estado'] != "") {
	$estado = $_REQUEST['estado'];
}
$tipo = "";
if (isset($_REQUEST['tipo']) && $_REQUEST['tipo'] != "") {
	$tipo = $_REQUEST['tipo'];
}
$pagina = "1";
if (isset($_REQUEST['pagina']) && $_REQUEST['pagina'] != "") {
	$pagina = $_REQUEST['pagina'];
}
ob_end_flush(); 
$usuario = $_SESSION['login'];
$user_perfil_id = $_SESSION['user_perfil_id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Listar Proveedor</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script language="JavaScript" src="../../js/CalendarPopup.js"> </script>
<script language="JavaScript">document.write(getCalendarStyles());</script>
<script>
function regresar(){
	rif = '<?= $rif?>';
	codigo = '<?= $codigo?>';
	nombre = '<?= $nombre?>';	
	estado = '<?= $estado?>';
	tipo = '<?= $tipo?>';
	pagina = '<?= $pagina?>';
	location.href = "buscar.php?rif="+rif+"&codigo="+codigo+"&nombre="+nombre+"&estado="+estado+"&tipo="+tipo+"&pagina="+pagina;
}
function irPdf() {
	document.form1.action="cuentas_contables_PDF.php";
	document.form1.submit();
}
</script>
</head>
<body>
<?
$sql="select prov_codigo, prov_id_rif,prov_nombre, prov_telefonos, prov_email from sai_proveedor_nuevo where prov_id_tp=3 and prov_prtp_id=2 order by prov_nombre";
$resultado_set=pg_query($conexion,$sql) or die("Error al consultar los proveedores");
?>
<form name="form1" method="post" action="">
<div align="center">
</div>
<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
		<td class="normalNegroNegrita" align="center">C&oacute;digo  </td>
		<td class="normalNegroNegrita" align="center">RIF  </td>
		<td class="normalNegroNegrita" align="center">Nombre  </td>
		<td class="normalNegroNegrita" align="center">Tel&eacute;fonos  </td>
		<td class="normalNegroNegrita" align="center">Email  </td>
		<td class="normalNegroNegrita" align="center">Ramo(s) </td>
	</tr>
<?php
while($row=pg_fetch_array($resultado_set))  {?>
	<tr>
		<td align="center"><span class="normal"><?php echo $row["prov_codigo"];?></span></td>
		<td align="center"><span class="normal"><?php echo $row["prov_id_rif"];?></span></td>
		<td><span class="normal"><?php echo $row["prov_nombre"];?></span></td>
		<td><span class="normal"><?php echo $row["prov_telefonos"];?></span></td>
		<td><span class="normal"><?php echo $row["prov_email"];?></span></td>
		<td>
			<span class="normal">
<?php
	$id_ramo="";
	$sql2="select * from sai_prov_ramo_secundario where upper(prov_id_rif)=upper('".$row["prov_id_rif"]."')"; 
	$resultado2=pg_query($conexion,$sql2) or die("Error al mostrar");
	while($row2=pg_fetch_array($resultado2)){ 
		$id_ramo=trim($row2['id_ramo']);
		$sql_partida="select part_nombre from sai_partida where part_id='".$id_ramo."'";
		$resultado_partida = pg_exec($conexion ,$sql_partida);
		if  ($row_partida = pg_fetch_array($resultado_partida) )
		echo $id_ramo.": ".$row_partida[0]."; ";
	} 
?>
			</span>
		</td>
	</tr>
<?php
}
?>
</table> 
</form>
</body>
</html>