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
<title>Ejecuci&oacute;n de modificar un Bien</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />

</head>

<body>
<?php
//Datos proveniente del formulario origen
	    $accion=$_POST['accion'];
		$bien_id=trim($_POST['txt_cod_bien']); 
		$part_id=trim($_POST['partida']);   
		$nombre=trim($_POST['txt_nombre']);	 
		$descrip=trim($_POST['txt_bien_descripcion']);	 
		$bien_exi_min=trim($_POST['txt_exist_bien']);	
		$usua_login=$_SESSION['login'];
		$esta_id=trim($_POST['opt_estado']);
		$vida_util=trim($_POST['txt_vida_util']);
		$clasificacion=$_POST['tp_activo'];
		
		
	//Buscamos el nombre de la Clasificación del artículo
	$sql_clasif = "select * from bien_categoria where id='".$clasificacion."'";
	$resultado_clasif=pg_query($conexion,$sql_clasif) or die("Error al conseguir el nombre de la Clasificacion");
	$row_clasif=pg_fetch_array($resultado_clasif);
	
//buscamos a�o y nombre de la partida
$sql_parti = "select * from sai_seleccionar_campo('sai_partida','pres_anno,part_nombre','part_id='||'''$part_id''','',0) Resultado_set(pres_anno int4, part_nombre varchar)";
$resultado_parti=pg_query($conexion,$sql_parti) or die("Error al conseguir los datos de la Partida");
if($row_parti=pg_fetch_array($resultado_parti))
{
  $anno_partida=$row_parti['pres_anno'];
  $part_nom=$row_parti['part_nombre'];
}  

if ($accion=="presupuesto"){
 $query="SELECT * FROM sai_item_partida WHERE id_item='".$bien_id."'";
 $resultado=pg_query($conexion,$query);
 if ($row=pg_fetch_array($resultado)){
 	//$sql_reg="UPDATE sai_item_partida SET part_id='".$part_id."',pres_anno='".$_SESSION['an_o_presupuesto']."' WHERE id_item='".$bien_id."'";
 	$sql_reg=
 	"
	UPDATE
 		sai_item_partida
 	SET
 		part_id='".$part_id."',
 		pres_anno='".$_SESSION['an_o_presupuesto']."'
 	WHERE
 		id_item='".$bien_id."'
 		AND pres_anno =
 		(
 		SELECT MAX(pres_anno)
 		FROM sai_item_partida
 		WHERE
 		id_item = '".$bien_id."'
 		)
 	";
 }else{
 	$sql_reg="INSERT INTO sai_item_partida (id_item,pres_anno,part_id) VALUES ('".$bien_id."','".$_SESSION['an_o_presupuesto']."','".$part_id."')";
 }
 	
}else{
//Registro de las modificaciones a los campos de la tabla sai_articulo
$sql_reg="select * from sai_modificar_bien('".$bien_id."','".$descrip."','".$bien_exi_min."', ".$esta_id.",'".$nombre."',".$vida_util.",".$clasificacion.")";
}

$resultado_reg=pg_query($conexion,$sql_reg);
   //consulta a la tabla de sai_estado
    $sql_edo="select * from sai_consulta_desc_estado($esta_id) as descripcion"; 
    $resultado_set_edo=pg_query($conexion,$sql_edo);
    if($rowedo=pg_fetch_array($resultado_set_edo))
	{$estado=$rowedo['descripcion'];}  
   ?>
<br />
<br />
<table width="647" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray">
    <td height="15" colspan="2" valign="midden" class="normalNegroNegrita"> Modificaci&oacute;n de activos</td>
  </tr>
  <tr>
    <td width="209" height="11" valign="midden"></td>
    <td width="426" height="11" valign="midden"></td>
  </tr>
  <tr>
    <td height="29" valign="midden" class="normalNegrita">C&oacute;digo:</td>
    <td height="29" valign="midden" class="normal"><?php echo $bien_id;?>&nbsp;</td>
  </tr>
  <tr>
    <td height="30" valign="midden" class="normalNegrita">Partida:</td>
    <td height="30" valign="midden" class="normal"><?php echo $part_id.":".$part_nom;?></td>
  </tr>
  <tr class="normal">
    <td height="31" valign="midden" class="normalNegrita">Nombre:</td>
    <td height="31" valign="midden"><?php echo $nombre;?></td>
  </tr>
  <tr class="normal">
    <td height="33" valign="midden" class="normalNegrita">Descripci&oacute;n:</td>
    <td height="33" valign="midden"><?php echo $descrip;?></td>
  </tr>
    <tr class="normal">
    <td height="33" valign="midden" class="normalNegrita">Clasificaci&oacute;n:</td>
    <td height="33" valign="midden"><?php echo $row_clasif['nombre'];?></td>
  </tr>
   <tr class="normal">
    <td height="35" valign="midden" class="normalNegrita">Existencia m&iacute;nima:</td>
    <td height="35" valign="midden"><?php echo $bien_exi_min;?></td>
  </tr>
  <tr class="normal"> 
  <td height="35" valign="midden" class="normalNegrita">Vida &uacute;til(A&ntilde;os): </td>
  <td height="35" valign="midden"><?php echo $vida_util;?></td>
  </tr>
  <tr class="normal">
    <td height="38" valign="midden" class="normalNegrita">Estado: </td>
    <td height="38" valign="midden" class="normal">
    <?php if($_POST['opt_estado']==1){?>
           Activo
           <?php }else{?>
           Inactivo
           <?php }?></td>
  </tr>
  <tr>
    <td height="15" colspan="2" align="center" class="normal"><br /><div align="center">
      Registro modificado  el d&iacute;a
      <?=date("d/m/y")?>
      a las
      <?=date("H:i:s")?>
      <br />
      <br />
      <br />
      <a href="javascript:window.print()"><img src="../../../imagenes/boton_imprimir.gif" border="0" /></a><br />
      <br />   
    </td>
  </tr>
</table>

</body>
</html>
