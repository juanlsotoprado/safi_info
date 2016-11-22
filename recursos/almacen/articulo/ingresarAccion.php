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
  
 
  $descrip=trim($_POST['descrip']);	
  $unidad=trim($_POST['unidad']);	  
  $esta_id=trim($_POST['opt_estado']);
  $usua_login=$_SESSION['login'];
  $tipo_art=trim($_POST['tipo_art']);
  
  //Buscamos el nombre de la Unidad de Medida seleccionada
  $sql_uni = "select * from sai_seleccionar_campo('sai_uni_medida','unme_descrip','unme_id='||'''$unidad''','',0) Resultado_set(unme_descrip varchar)";
  $resultado_uni=pg_query($conexion,$sql_uni) or die("Error al conseguir el Nombre de la Unidad");
  $row_uni=pg_fetch_array($resultado_uni);
		
  //Buscamos el nombre de la Clasificación del artículo
  $sql_clasif = "select * from sai_seleccionar_campo('sai_arti_tipo','tp_desc','tp_id='||'''$tipo_art''','',0) Resultado_set(tp_desc varchar)";
  $resultado_clasif=pg_query($conexion,$sql_clasif) or die("Error al conseguir el nombre de la Clasificacion");
  $row_clasif=pg_fetch_array($resultado_clasif);

	        
  //Buscamos el mayor del campo de arti_id
  $sql = "select MAX(CAST(id as integer)) as codigo from sai_item";
  $resultado=pg_query($conexion,$sql) or die("Error al conseguir el C\u00F3digo del Item");
  if($row=pg_fetch_array($resultado))
  {  	 
	$codi=$row['codigo'];
	$codi_new=$codi+1;
  }
		
  $sql_edo="select * from sai_consulta_desc_estado($esta_id) as descripcion"; 
  $resultado_set_edo=pg_query($conexion,$sql_edo);
  if($rowedo=pg_fetch_array($resultado_set_edo))
  {$estado=$rowedo['descripcion'];}  
?>
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<title>.:SAFI:EJECUCION DE INGRESAR ARTICULO </title>
<link  rel="stylesheet"   href="../../../css/plantilla.css" type="text/css" media="all"  />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php
  $sql_in = "select * from  sai_insert_articulo('".$codi_new."', '".$descrip."' , '".$unidad. "', '". $usua_login."','".$tipo_art."','".$esta_id."') As resultado_set(int)";
  $resultado=pg_query($conexion,$sql_in);
  if ($row_in = pg_fetch_array($resultado))
  {
?>
<br/><br/>

<table width="550" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
 <tr class="td_gray"> 
  <td height="15" colspan="2" valign="midden" class="normalNegroNegrita"><span class="Estilo1">Registro</font></span></span></td>
 </tr>
 <tr>
   <td height="21" colspan="2" valign="midden">&nbsp;</td>
  </tr>
  <tr>
    <td width="223" height="28" valign="midden"><span class="normalNegrita">C&oacute;digo del art&iacute;culo:</span></td>
	<td width="315" height="28" valign="midden" class="normal"><?php echo $codi_new;?></td>
  </tr>
  <tr>
	<td height="30" valign="midden"> <div class="normalNegrita">Art&iacute;culo:</div></td>
	<td height="30" valign="midden"  class="normal"><?php echo strtoupper($descrip); ?> </td>
  </tr>
  <tr> 
	<td height="35" valign="midden"> <div class="normalNegrita">Unidad de medida: </div></td>
	<td height="35" valign="midden"  class="normal"><?php echo strtoupper($row_uni['unme_descrip']); ?> </td>
  </tr>
  <tr> 
	<td height="28" valign="midden"> <div class="normalNegrita">Clasificaci&oacute;n: </div></td>
	<td height="28" valign="midden" class="normal"><?echo $row_clasif['tp_desc'];?> </td>
  </tr>
  <tr> 
	<td height="25" valign="midden"> <div class="normalNegrita">Estado: </div></td>
	<td height="25" valign="midden" class="normal"><?php echo strtoupper($estado);?></td>
  </tr>
  <tr>
	<td height="15" colspan="2" align="center" class="normal"><div align="center"><br>
	Registro generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br><br>
	<br>
	<a href="javascript:window.print()"><img src="../../../imagenes/boton_imprimir.gif" border="0"></a><br><br>
    <br></div></td>
  </tr>
</table>
<?php  		
} else {    ?>

<table width="550" align="center" background="../../../imagenes/fondo_tabla.gif" >
 <tr class="td_gray"> 
   <td height="16" colspan="3" valign="midden" class="normalNegroNegrita"><div align="left" class="Estilo1"  >Registrar</div></td>
 </tr>
 <tr>
   <td colspan="4" class="normal"><br><div align="center">
   	 Ha ocurrido un error al ingresar los datos del art&iacute;culo <br>
	 <?php echo(pg_errormessage($conexion)); ?><br><br>
	 <img src="../../../imagenes/mano_bad.gif" width="31" height="38"><br><br>
	</div></td>
  </tr>
</table>
		<?php
   }
?>
</body>
</html>
