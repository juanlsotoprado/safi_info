<?php 
  ob_start();
  session_start();
  require_once("../../includes/conexion.php");
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../index.php',false);
   ob_end_flush(); 
   exit;
  }
  ob_end_flush(); 
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAI:MODIFICAR ART&Iacute;CULO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />

</head>

<body>
<?php
//Datos proveniente del formulario origen
$arti_id=trim($_POST['txt_articulo']); 
$descripcion=trim($_POST['txt_nombre_arti']);
$esta_id=trim($_POST['opt_estado']);
$arti_tp=trim($_POST['tp_art']);
$unidad=trim($_POST['unidad']);
 
$query="UPDATE sai_item set nombre= '".addslashes($descripcion)."', esta_id= ".$esta_id."  WHERE id='".$arti_id."'";
$resultado_reg=pg_query($conexion,$query);
$query="UPDATE sai_item_articulo set tipo='".$arti_tp."',unidad_medida='".$unidad."'  WHERE id='".$arti_id."'";
$resultado_reg=pg_query($conexion,$query);

$sql_uni = "select * from sai_seleccionar_campo('sai_uni_medida','unme_descrip','unme_id='||'''$unidad''','',0) Resultado_set(unme_descrip varchar)";
$resultado_uni=pg_query($conexion,$sql_uni) or die("Error al conseguir el Nombre de la Unidad");
if($row_uni=pg_fetch_array($resultado_uni))
{$medida=$row_uni['unme_descrip']; } 
   //consulta a la tabla de sai_estado
	$sql_edo="select * from sai_consulta_desc_estado($esta_id) as descripcion"; 
    $resultado_set_edo=pg_query($conexion,$sql_edo);
    if($rowedo=pg_fetch_array($resultado_set_edo))
	{$estado=$rowedo['descripcion'];}  
   ?>
<br />
<br />
<table width="550" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr  class="td_gray"> 
	<td height="15" colspan="2" valign="midden" class="normalNegroNegrita">MODIFICAR </td>
	</tr>
	
	<tr>
	  <td height="21" colspan="2" valign="midden">&nbsp;</td>
  </tr>
	<tr class="normal">
	  <td height="31" valign="midden"><span class="normalNegrita">Art&iacute;culo:</span></td>
	  <td height="31" valign="midden"><span class="normal">
	    <?=$arti_id.":".$descripcion;?>
	  </span></td></tr>
	  	<tr class="normal"> 
	<td height="32" valign="midden"> <div class="normalNegrita">Unidad de medida: </div>	</td>
	<td height="32" valign="midden"><?=$medida?>	</td>
	</tr>
	 <tr class="normal"> 
	 <td height="31" valign="midden"><div class="normalNegrita">Clasificaci&oacute;n: </div>	 </td>
	 <?
	   //Buscamos el nombre de la Clasificación del artículo
	   $sql_clasif = "select * from sai_seleccionar_campo('sai_arti_tipo','tp_desc','tp_id='||'''$arti_tp''','',0) Resultado_set(tp_desc varchar)";

	   $resultado_clasif=pg_query($conexion,$sql_clasif) or die("Error al conseguir el nombre de la Clasificacion");
	        $row_clasif=pg_fetch_array($resultado_clasif);
	?>
	 <td height="31" valign="midden"><?=$row_clasif['tp_desc'];?>	 </td>
	 </tr>
	<tr class="normal"> 
	<td height="31" valign="midden" class="normalNegrita">Estado:</td>
	<td height="31" colspan="4" valign="midden"><?=$estado?></td>
	</tr>
	<tr>
	<td height="15" colspan="4" align="center" class="normal"><div align="center">
	<br>
	Registro modificado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br>
	<br>
	<br><a href="javascript:window.print()"><img src="../../imagenes/boton_imprimir.gif" border="0"></a><br><br>
	 
	<a href="buscar.php?<?=$_POST['criterio']?>=<?=$_POST['valor']?>">
	 <input class="normalNegro" type="button" value="Regresar"/>
	</a>	</div></td>
	</tr>
   </table>
</body>
</html>
