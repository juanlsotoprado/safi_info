<?php 
  ob_start();
  session_start();
  require_once("../../../includes/conexion.php");
  require_once("../../../includes/arreglos_pg.php");
	   
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
<title>.:SAFI::Devoluci&oacute;n de Actas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
</head>
<body>
<br /><br />
<div align="center">
<?php 
$obs=$_POST['observacion'];
$acta=$_POST['acta'];
$tipo=$_POST['tipo'];
$fecha=$_POST['hid_desde_itin'];

$codigos = "";
if (isset($_POST['solicitud'])) {
	$cod = $_POST["solicitud"];
}
if (count($cod)>0) {

  $codigo_alm=array(count($cod));
  $cantidad=array(count($cod));
  $id_arti=array(count($cod));
  $precio=array(count($cod));
  $medida=array(count($cod));
  
 for ($x=0;$x<count($cod);$x++) {

   $salida_id = $cod[$x];
   $cantidad[$x] =$_POST[trim($salida_id)];

   $sql="SELECT * FROM sai_seleccionar_campo('sai_arti_almacen t1,sai_arti_salida t2','t1.arti_id,t1.precio,t1.medida,t1.alm_id','t1.alm_id=t2.alm_id and salida_id= '||'''$salida_id'' ','',2) resultado_set(arti_id varchar,precio float8,medida varchar, alm_id int4)"; 
   $resultado=pg_query($conexion,$sql) or die("Error al consultar los articulos");
   if($row=pg_fetch_array($resultado))
   {
    $id_arti[$x]=$row['arti_id'];
    $precio[$x]=$row['precio'];
    $medida[$x]=$row['medida'];
    $codigo_alm[$x] = $row['alm_id'];
   }
 }

require_once("../../../includes/arreglos_pg.php");
	$arreglo_codigo = convierte_arreglo ($codigo_alm);
	$arreglo_cantidad = convierte_arreglo ($cantidad);
	$arreglo_medida = convierte_arreglo ($medida);
	$arreglo_precio = convierte_arreglo ($precio);
	$arreglo_arti = convierte_arreglo ($id_arti);


$sql="SELECT * FROM sai_seleccionar_campo('sai_arti_acta_almacen','depe_entregada,entregado_a','amat_id= '||'''$acta''  ORDER BY fecha_acta ASC','',2) resultado_set(depe_entregada varchar,entregado_a int)"; 
$resultado=pg_query($conexion,$sql) or die("Error al consultar acta");  

if($row=pg_fetch_array($resultado))
{
 $depe_devuelve=$row['depe_entregada'];
 $devuelto_por=$row['entregado_a'];
}

$sql = "select * from sai_acta_devolucion('".$arreglo_arti."','".$arreglo_cantidad."','".$obs."','".$depe_devuelve."','".$_SESSION['login']."','".$arreglo_medida."','".$_SESSION['user_depe_id']."','".$devuelto_por."','".$arreglo_codigo."','".$arreglo_precio."','".$fecha."') as ingresado ";
$resultado=pg_query($conexion,$sql) or die(utf8_decode("No se generó ninguna devolución"));
$valido=$resultado;
    if ($row=pg_fetch_array($resultado))
 	{
     $codigo=$row['ingresado'];
	}
?>
<div class="normal" align="center">
   <br />
      Se proces&oacute; satisfactoriamente la devoluci&oacute;n del acta N&deg;: <strong><?=$codigo?></strong>.<br />
      Registro generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?>
      <br /><br />
       <a href="javascript:abrir_ventana('../../almacen/inventario/devoluciones_pdf.php?id=<?=$codigo;?>')" >
      <img src="../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br><br />
    
<?
} //end if
else {
	echo "<div class='peqNegrita' align='center'><strong>Error: Debe seleccionar al menos un art&iacute;culo para devolver</strong>";
}
?>

</div>
</body>
</html>
<?php pg_close($conexion);?>
