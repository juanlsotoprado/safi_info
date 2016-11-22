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
<title>.:SAFI:Buscar Otro Trabajador</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../includes/js/funciones.js"> </script>
</head>
<body>
<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
  <td height="15" colspan="6" align="center" class="titularMedio" class="titularMedio"><span>
  OTROS TRABAJADORES ACTIVOS </span></td>
</tr>
 <tr class="td_gray">
						<td align="center" class="titularMedio"><span >Documento de Identificaci&oacute;n</span></td>
						<td align="center" class="titularMedio">Nombres</td>
						<td align="center" class="titularMedio"><span >Apellidos</span></td>
						<td align="center" class="titularMedio"><span >Tipo</span></td>
						<td align="center" class="titularMedio"><span >Dependencia</span></td>
						<td align="center" class="titularMedio"><span >Estado</span></td>

 </tr>
<?php
$sql="select v.nacionalidad, v.benvi_cedula, v.benvi_nombres, v.benvi_apellidos, v.tipo, d.depe_nombre, 
CASE WHEN v.benvi_esta_id =1 THEN 'Activo'
             ELSE 'Inactivo'
       END AS benvi_esta_id
from sai_viat_benef v, sai_dependenci d
where v.depe_id=d.depe_id and benvi_esta_id=1
order by v.benvi_esta_id, v.depe_id, v.benvi_apellidos, tipo";
$resultado_set=pg_query($conexion,$sql);
while($row=pg_fetch_array($resultado_set))  {	 ?>
	<tr class="normal" >
						<td class="normal"><?php echo $row['nacionalidad']."-".$row['benvi_cedula'];?></td>
						<td><?php echo $row['benvi_nombres'];?></td>
						<td><?php echo $row['benvi_apellidos'];?></td>
						<td><?php echo $row['tipo'];?></td>
						<td><?php echo $row['depe_nombre'];?></td>
						<td><?php echo $row['benvi_esta_id'];?></td>
					  </tr>
<?php } pg_close($conexion);?>
  </table> 
</body>
</html>