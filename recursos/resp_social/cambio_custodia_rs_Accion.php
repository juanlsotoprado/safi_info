<?php
ob_start();
require("../../includes/conexion.php");
require("../../includes/perfiles/constantesPerfiles.php");
if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<?php

  $facturas_partida=trim($_POST['txt_arreglo_factura_head']);
  $vector=explode ("�" , $facturas_partida);
  $elem_vector=count($vector);
  $tt_factura= ($elem_vector/3);
  $id=array($elem_vector);
  $nombre=array($elem_vector);
  $cantidad=array($elem_vector);
  $motivo=trim($_POST['motivo']);   
  $medida=array($elem_vector);
  $x=0;
  $tt=0;
  $cantidad_articulos=$tt_factura;
  
  while ($x< $elem_vector)
  {	
	$id[$tt]=trim($vector[$x]);
	$nombre[$tt]=trim($vector[++$x]);
	$cantidad[$tt]=trim($vector[++$x]);
	$id_art=$id[$tt];
	$sql_d="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','unidad_medida','t1.id=t2.id and t1.id='||'''$id_art''','',2) resultado_set(unidad_medida varchar)";
	$resultado_set_d=pg_query($conexion,$sql_d) or die("Error al mostrar");
	      
	if($rowd=pg_fetch_array($resultado_set_d)) 
	{ 
	 $medida[$tt]=$rowd['unidad_medida'];
	}
	$tt++;
	$x++;
   }
   
   require_once("../../includes/arreglos_pg.php");
	$arreglo_id = convierte_arreglo ($id);
	if ($cantidad_articulos>0.5){
	$arreglo_cantidad = convierte_arreglo ($cantidad);
	}else{
	$arreglo_cantidad = '{0}';
	}
	
	$arreglo_medida = convierte_arreglo ($medida);

	if ($_SESSION['user_perfil_id'] == PERFIL_ALMACENISTA)
	 $ubicacion=2;
	 else
	 $ubicacion=1;
	////////////////////////////OJOO////////////////////////////////
	//VALIDAR DISPONIBILIDAD DEL ALMACEN ANTES DE REGISTRAR EL CAMBIO DE CUSTODIA
	
    $sql =  "Select * from sai_insert_articulos_custodia ('".$arreglo_id."','".$arreglo_cantidad."','";
	$sql .= $motivo."','".$_SESSION['login']."','".$arreglo_medida."', '".$_SESSION['user_depe_id']."','".$ubicacion."') as ingresado ";
	$resultado = pg_query($conexion,$sql);
	$valido=$resultado;
	
    if ($row=pg_fetch_array($resultado))
	{
     $codigo=$row['ingresado'];
	}
?>
<body>
<?php if ($codigo<>""){?>
<form action="" name="form" id="form" method="post" >
 <table width="700" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" id="sol_via">
   <tr>
      <td height="15" colspan="3" valign="midden" class="td_gray"><span class="normalNegroNegrita">Custodia de activos y/o materiales</span> </td>
   </tr>
   <tr>
	  <td class="normal"><strong>N&deg; acta</strong></td>
	  <td class="normalNegro"><b><?php echo($codigo); ?></b></td></tr>
   <tr>
	 <td class="normal"><strong>Material</strong></td>
	 <td class="normal"><strong>Cantidad enviada</strong></td>
   </tr>
 <?php
  for ($i=0; $i< $cantidad_articulos;$i++)
  {	
  	$query_custodia="SELECT cantidad FROM sai_arti_item_custodia WHERE id='".$id[$i]."' and acta_id='".$codigo."'";
    $resultado_custodia = pg_query($conexion,$query_custodia);
	
	$cant_custodia=0;
	$cantidad_custodia_real=0;
    if ($row_custodia=pg_fetch_array($resultado_custodia))
	{
     $cant_custodia=$row_custodia['cantidad'];
     $cantidad_custodia_real= $cant_custodia;
	}
	$cantidad_faltante=$cantidad[$i]-$cantidad_custodia_real
  	?>
   <tr>
	  <td class="normal"><?php echo $nombre[$i];?></td>
	  <td class="normalNegro"><?php echo $cantidad_custodia_real;//$cantidad[$i];?> </td>
	 </tr>
	  <? 
   }?>
   	<tr>
	  <td class="normal"><strong>Observaciones</strong></td>
	  <td class="normalNegro"><?php echo($motivo); ?></td></tr>
	
	
</table>
  <br><br>
  <div align='center'>
  <a target="_blank" class="normal" href="custodia_rs_pdf.php?codigo=<?=$codigo;?>" class="link">
  <img src="../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"><br>Acta</a></div><br>
  	
</form>
<?php } else{?>
<br></br><div class="normal" align="center"><b>No se puede emitir el Acta de Custodia con los art&iacute;culos seleccionados, Comun&iacute;quese con el Departamento de Sistemas</b></div>
<?php }?>
</body>
</html>
<?php pg_close($conexion);?>
	