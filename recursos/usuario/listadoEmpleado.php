<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../index.php',false);
	ob_end_flush(); 		   
	exit;
}ob_end_flush(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar Empleado</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
</head>
<body>
<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
  <td colspan="5" class="titularMedio" class="titularMedio" align="center">EMPLEADOS ACTIVOS</td>
</tr>
 <tr class="td_gray">
	<td width="10%" align="center" class="titularMedio"><span >Documento de Identificaci&oacute;n</span></td>
	<td width="20%" align="center" class="titularMedio">Nombres</td>
	<td width="20%" align="center" class="titularMedio"><span >Apellidos</span></td>
	<td width="25%" align="center" class="titularMedio"><span >Cargo</span></td>
	<td width="25%" align="center" class="titularMedio"><span >Dependencia</span></td>
 </tr>
<?php
$sql="select e.nacionalidad, e.empl_cedula, upper(e.empl_nombres) as empl_nombres, upper(e.empl_apellidos) as empl_apellidos, c.carg_nombre, d.depe_nombre from sai_empleado e, sai_cargo c, sai_dependenci d where e.depe_cosige=d.depe_id and e.carg_fundacion=c.carg_fundacion and e.esta_id=1 order by depe_id, empl_apellidos, empl_nombres";
$resultado_set=pg_query($conexion,$sql);
while($row=pg_fetch_array($resultado_set))  {	 ?>
	<tr class="normal" >
		<td class="normal"><?php echo  $row['nacionalidad']."-".$row['empl_cedula'];?></td>
		<td><?php echo $row['empl_nombres'];?></td>
		<td><?php echo $row['empl_apellidos'];?></td>
		<td><?php echo $row['carg_nombre'];?></td>
		<td><?php echo $row['depe_nombre'];?></td>
	</tr> 
<?php } pg_close($conexion);?>
 </table> 
</body>
</html>