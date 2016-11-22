<?php 
    ob_start();
	session_start();
	 require_once("../../../includes/conexion.php");
	 
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	     {
		   header('Location:../../../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	     }
	ob_end_flush(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Detalle del Bien</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />

</script>

</head>
<body>
<?php $codigo=trim($_GET['codigo']);?> 
<form name="form" action="" method="post">
<?php
#Efectuamos la consulta SQL  
$sql_art="SELECT nombre,descripcion,esta_id,existencia_minima,vida_util,tipo FROM sai_item t1, sai_item_bien t2
WHERE t1.id=t2.id and t1.id='".$codigo."'"; 
$resultado_set_art=pg_query($conexion,$sql_art);
if($row=pg_fetch_array($resultado_set_art)) 
{ 
 	$bien_nombre=$row['nombre'];
	$bien_descripcion=$row['descripcion'];
	$esta_id=$row['esta_id'];
	$bien_exi_min=$row['existencia_minima'];
	$bien_vida_util=$row['vida_util'];
	$clasificacion=$row['tipo'];
	
 	//Buscamos el nombre de la Clasificación del artículo
	$sql_clasif = "select * from bien_categoria where id=".$clasificacion."";
	$resultado_clasif=pg_query($conexion,$sql_clasif) or die("Error al conseguir el nombre de la Clasificacion");
	$row_clasif=pg_fetch_array($resultado_clasif);
	
	$sql_arp = "select * from sai_seleccionar_campo('sai_item_partida','pres_anno, part_id','id_item=$codigo','',0) Resultado_set(pres_anno int4, part_id varchar)";
	$resultado_arp=pg_query($conexion,$sql_arp) or die("Error al consultar en sai_bien_part_anno");
	if($row_arp=pg_fetch_array($resultado_arp)) 
	{   
	    $pres_anno=$row_arp['pres_anno']; 
		$part_id=$row_arp['part_id']; 
		
		//Buscamos nombre de partida
		$sql_parti = "select * from sai_seleccionar_campo('sai_partida','part_nombre','part_id='||'''$part_id''','',0) Resultado_set(part_nombre varchar)";
		$resultado_parti=pg_query($conexion,$sql_parti) or die("Error al conseguir el Nombre de la Partida");
		if($row_parti=pg_fetch_array($resultado_parti))
		{ $partida_nom=$row_parti['part_nombre']; }
	}
	
	//Buscamos nombre del estado
	$sql_esta = "select * from sai_seleccionar_campo('sai_estado','esta_nombre','esta_id=$esta_id','',0) Resultado_set(esta_nombre varchar)";
	$resultado_esta=pg_query($conexion,$sql_esta) or die("Error al conseguir el Estado");
	if($row_edo=pg_fetch_array($resultado_esta))
	{ $esta_nombre=$row_edo['esta_nombre']; }

	//Mostramos la tabla que contiene los datos del art�culo
	?>

<br />
<table width="500" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
    <td height="15" colspan="3" valign="midden"  class="normalNegroNegrita">Detalles del activo</td>
	</tr>
	<tr>
	  <td width="168" height="28" valign="midden"><span class="normalNegrita">C&oacute;digo del activo:</span></td>
	  <td width="320" height="28" valign="midden" class="normal"><span class="normal">
	    <?=$codigo?>
	  </span></td>
	  </tr>
	<tr>
	<td height="32" valign="midden">
	<span class="normalNegrita">Partida:</span></td>
	<td height="32" valign="midden" class="normal"><?=$part_id.":".$partida_nom?>	</td>
	</tr>
	<tr>
	<td height="33" valign="midden" class="normalNegrita">Nombre:</td>
	<td height="33" valign="midden" class="normal"><?=$bien_nombre?></td>
	</tr>
	<tr class="normal"> 
	<td height="33" valign="midden" class="normalNegrita">Descripci&oacute;n:	</td>
	<td height="33" valign="midden"><?=$bien_descripcion?>	</td>
	</tr>
	<tr class="normal"> 
	<td height="33" valign="midden" class="normalNegrita">Clasificaci&oacute;n:	</td>
	<td height="33" valign="midden"><?=$row_clasif['nombre'];?>	</td>
	</tr>
	<tr class="normal"> 
	<td height="34" valign="midden" class="normalNegrita">Existencia m&iacute;nima: </td>
	<td height="34" valign="midden"><?=$bien_exi_min?>	</td>
	</tr>
	<tr class="normal"> 
	<td height="35" valign="midden" class="normalNegrita">Vida &uacute;til(A&ntilde;os): </td>
	<td height="35" valign="midden"><?php echo $bien_vida_util;?></td>
	</tr>
    <?php if($observa != ""){ ?>
	 
	<?php } ?>
	<tr class="normal"> 
	<td height="34" valign="midden" class="normalNegrita">Estado: </td>
	<td height="34" valign="midden"><?=$esta_nombre?>	</td>
	</tr>
	<tr>
	<td height="16" colspan="3" align="center" class="normal">
	<br><div align="center">
	Detalle generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("H:i:s")?><br><br>
	<a href="javascript:window.print()"><img src="../../../imagenes/boton_imprimir.gif" border="0"></a><br><br>
	<input type="button" onclick="javascript:window.close()" value="Cerrar"></input><br><br>	
		</div>
	<br>	</td>
	</tr>
	</table>
	<?php
}
else
   {
     ?>
	    <table width="500" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray"> 
		<td height="16" colspan="3" valign="midden"><div align="left" class="normalNegrita"> 
		  ADMINISTRAR BIENES NACIONALES 
		  </div></td>
		</tr>
		<tr>
		<td colspan="4" class="normal"><br>
		<div align="center">
		<img src="../../../imagenes/vineta_azul.gif" width="11" height="7">
		Ha ocurrido al consultar detalles del Bien <br>
		<?php echo(pg_errormessage($conexion)); ?><br>
		<img src="../../../imagenes/mano_bad.gif" width="31" height="38">
		<br>
		<br>
		<input type="button" onclick="javascript:window.close()" value="Cerrar"></input>
        <br>
		<br></div>
		</td>
		</tr>
		</table>
	 <?php
   }
?>	
</form>
</body>
</html>
