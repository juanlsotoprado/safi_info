<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 
$an=$_SESSION['an_o_presupuesto'];
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar Partida</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../../js/funciones.js"> </script>
</head>
<body> 

<?php 
	$sql="SELECT id, nombre, part_id, 
	case when (id_tipo='1') then 'MATERIALES'
		 when (id_tipo='2') then 'ACTIVOS'
		 else 'SERVICIOS' end as tipo
	FROM sai_item, sai_item_partida 
	where id=id_item and esta_id<>0 order by id_tipo,nombre";
	$resultado=pg_query($conexion,$sql) or die("Error al consultar las entidades bancarias");
?>
<div align="center" class="normalNegroNegrita">Listado de partidas asociadas a los materiales, activos y servicios</div>
		  <table width="80%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
			  <tr class="td_gray">
			  <td width="10%" class="normalNegroNegrita"><div align="center">C&oacute;digo</div></td>
			  <td width="50%" class="normalNegroNegrita"><div align="center">Nombre </div></td>
			  <td width="5%" class="normalNegroNegrita"><div align="center">Partida</div></td>
			  <td width="5%" class="normalNegroNegrita"><div align="center">Tipo</div></td>
			  </tr>
		 <?php 	while($row=pg_fetch_array($resultado)) {  ?>
	
			  <tr class="normal">
			    <td width="10%" align='center'><?=$row['id']?></td> 
			    <td width="50%"><?=$row['nombre']?></td>
			    <td width="5%"><?=$row['part_id']?></td>
 				<td width="5%"><?=$row['tipo']?></td>
			  </tr>
<?php  } 
pg_close($conexion);
?>
</body>
</html>