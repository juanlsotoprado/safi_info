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
<title>.:SAFI:DETALLE ART&Iacute;CULO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
</script>

</head>
<body>
<?php $codigo=trim($_GET['codigo']);?> 
<form name="form" action="" method="post">
<?php
#Efectuamos la consulta SQL  
$sql_art="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','t1.id,nombre,esta_id,unidad_medida,usua_login,existencia_minima,tipo','t1.id='||'''$codigo''','',2) 
resultado_set(id varchar, nombre varchar,esta_id int4,unidad_medida varchar,usua_login varchar,existencia_minima int4,tipo varchar)";
 
$resultado_set_art=pg_query($conexion,$sql_art);
if($row=pg_fetch_array($resultado_set_art)) 
{ 
 	$arti_descripcion=$row['nombre'];
	$unme_id=$row['unidad_medida'];
	$esta_id=$row['esta_id'];
	$arti_exi_min=$row['existencia_minima'];
	
	//Buscamos el nombre de la Unidad de Medida seleccionada
	$sql_uni = "select * from sai_seleccionar_campo('sai_uni_medida','unme_descrip','unme_id=''$unme_id''','',0) Resultado_set(unme_descrip varchar)";
	$resultado_uni=pg_query($conexion,$sql_uni) or die("Error al conseguir el Nombre de la Unidad");
	if($row_uni=pg_fetch_array($resultado_uni)) 
	{  $medida=$row_uni['unme_descrip']; }
	
	$sql_arp = "select * from sai_seleccionar_campo('sai_item_partida','pres_anno, part_id','id_item='||'''$codigo''','',0) Resultado_set(pres_anno int4, part_id varchar)";
	$resultado_arp=pg_query($conexion,$sql_arp) or die("Error al consultar en sai_arti_part_anno");
	if($row_arp=pg_fetch_array($resultado_arp)) 
	{   
	    $pres_anno=$row_arp['pres_anno']; 
		$part_id=$row_arp['part_id']; 
		//Buscamos nombre de partida
		$sql_parti = "select * from sai_seleccionar_campo('sai_partida','part_nombre','part_id='||'''$part_id''','',0) Resultado_set(part_nombre varchar)";
		$resultado_parti=pg_query($conexion,$sql_parti) or die (utf8_decode("Error al conseguir el Año de la Partida"));
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
  <br />
<table width="500" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
 <tr  class="td_gray"> 
   <td height="15" colspan="3" valign="midden" class="normalNegroNegrita">Detalle del art&iacute;culo </td>
 </tr>
 <tr>
   <td height="21" colspan="2" valign="midden">&nbsp;</td>
 </tr>
 <tr>
   <td width="189" height="28" valign="midden"><span class="normalNegrita">Art&iacute;culo:</span></td>
   <td width="299" height="28" valign="midden" class="normal"><span class="normal">
    <?=$codigo.":".$arti_descripcion?></span></td>
 </tr>
 <tr>
   <td height="27" valign="midden"><span class="normalNegrita">Partida:</span></td>
   <td height="27" valign="midden" class="normal"><?=$part_id.":".$partida_nom?>	</td>
 </tr>
 <tr class="normal"> 
   <td height="32" valign="midden"><div  class="normalNegrita">Unidad de medida: </div>	</td>
   <td height="32" valign="midden"><?=$medida?>	</td>
 </tr>
 <tr class="normal"> 
   <td height="31" valign="midden"><div class="normalNegrita">Estado: </div>	</td>
   <td height="31" valign="midden"><?=$esta_nombre?>	</td>
 </tr>
 <tr>
   <td height="16" colspan="3" align="center" class="normal"><br>
	Detalle generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br><br>
	<a href="javascript:window.print()"><img src="../../../imagenes/boton_imprimir.gif" border="0"></a><br><br>	
	<input class="normalNegro" type="button" value="Cerrar" onclick="javascript:window.close();"/>
		
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
		<td height="16" colspan="3" valign="midden" bgcolor="#0099CC"><div align="left" class="titularMedio style1"> 
		ADMINISTRAR ALMACEN</div></td>
		</tr>
		<tr>
		<td colspan="4" class="normal"><br>
		<div align="center">
		Ha ocurrido al consultar detalles del Art&iacute;culo<br>
		<?php echo(pg_errormessage($conexion)); ?><br>
		<img src="../../../imagenes/mano_bad.gif" width="31" height="38">
		<br><br>
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
<?php pg_close($conexion);?>
