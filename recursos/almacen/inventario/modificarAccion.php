<?php 
  ob_start();
  session_start();
  require_once("../../../includes/conexion.php");
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../index.php',false);
   ob_end_flush();    exit;
  }
  ob_end_flush(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Ejecutar Modificar Ingreso Almac&eacute;n<</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script LANGUAGE="JavaScript" SRC="../../../includes/js/funciones.js"> </SCRIPT>
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php
   $filas=$_POST['num_filas'];
   for ($i=0; $i<$filas; $i++)
   {
    $alm_id[$i]=$_POST['alm_id_'.$i];
    $cantidad[$i]=$_POST['cantidad_'.$i];
    $depe_id[$i]=$_POST['opt_depe_'.$i];
    $precio[$i]=$_POST['precio_'.$i];
    $fecha_recep[$i]=$_POST['fecha_'.$i];
    $codigo[$i]=$_POST['arti_id_'.$i];
    
	$arti=$codigo[$i];
    $sql_ar="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','unidad_medida,nombre','t1.id=t2.id and t1.id=''$arti''','',1) resultado_set(unidad_medida varchar, nombre varchar)"; 
 	$resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de articulos");  

  	if($rowa=pg_fetch_array($resultado_set_most_ar))
   	{	
 	 $medida[$i]=trim($rowa['unidad_medida']);
	 $nombre[$i]=trim($rowa['nombre']);
   	} 
   }
	
   	require_once("../../../includes/arreglos_pg.php");
	$arreglo_alm_id = convierte_arreglo ($alm_id);
	$arreglo_cantidad = convierte_arreglo ($cantidad);
	$arreglo_precio = convierte_arreglo ($precio);
	$arreglo_depe = convierte_arreglo ($depe_id);

	$sql="select * from sai_modificar_inventario('".$arreglo_alm_id."','".$arreglo_cantidad."','".$arreglo_precio."','".$arreglo_depe."') as resultado_set(int4)";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
?>
	<br /><br />

<table width="850" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
 <tr class="td_gray"> 
   <td height="15" colspan="5" valign="midden" class="normalNegroNegrita">Inventario modificado</td>
 </tr>
 <tr>
   <td height="11" colspan="5"></td>
 </tr>
 <tr>
   <td height="11" colspan="5"></td>
 </tr>
 <tr>
   <td height="48" colspan="5"><table width="750" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
 <tr>
   <td width="55" height="21"><div align="center" class="normalNegrita">C&oacute;digo</div></td>
   <td width="200"><div align="center" class="normalNegrita">Nombre</div></td>
   <td width="200"><div align="center" class="normalNegrita">Dependencia Solicitante</div></td>
   <td width="77"><div align="center" class="normalNegrita">Cantidad</div></td>
   <td width="59"><span class="normalNegrita">Unidad de medida</span></td>
   <td width="80"><div align="center" class="normalNegrita">Precio</div></td>
   <td width="100"><div align="center" class="normalNegrita">Fecha recepci&oacute;n</div></td>
 </tr>
 <?php
   for ($i=0; $i<$filas; $i++){?>
  <tr>
   <td><div align="center" class="normalNegro" ><?php echo $codigo[$i];?></div></td>
   <td><div align="center" class="normalNegro"><?php echo $nombre[$i];?></div></td>
   <td><div align="center" class="normalNegro">
 <?php
    $id_depe=$depe_id[$i];						   
	$sql="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_id,depe_nombre','depe_id='||'''$id_depe''','',1) resultado_set(depe_id varchar, depe_nombre varchar)"; 
	$resultado_part=pg_query($conexion,$sql) or die("Error al mostrar");
	if($row_part=pg_fetch_array($resultado_part))
	{ 
	  echo $row_part['depe_nombre'];?><?php 
	} 
	?>
	  </div></td>
   <td><div align="center" class="normalNegro"><?php echo $cantidad[$i]?></div></td>
   <td><div align="center" class="normalNegro"><?php echo $medida[$i];?></div></td>
   <td><div align="right" class="normalNegro"><?php echo $precio[$i];?></div></td>
   <td><div align="center" class="normalNegro"><?php echo $fecha_recep[$i];?></div></td>
  </tr>
  <?php
	}
  ?>
</table></td>
</tr>
<tr>
  <td height="85" colspan="5" class="normal">
	<div align="center"><br /><br>
	<a href="javascript:window.print()" class="normal"><img src="../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><a href="../../almacen/inventario/index.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('regresar','','../../../imagenes/boton_reg_blk.gif',1)"></a>
	<br /><br /><br /><br><br></div>	</td>
	</tr>
</table>
<br />
</body>
</html>
<?php pg_close($conexion);?>
