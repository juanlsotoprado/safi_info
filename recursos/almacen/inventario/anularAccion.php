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
<title>.:SAFI::Ejecuci&oacute;n - Anulaci&oacute;n de actas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
</head>
<body>
<br /><br />
<div align="center">
<?php 
$memo_contenido=$_POST['observacion'];
$acta=$_POST['acta'];
$tipo=$_POST['tipo'];

$sql = "select * from sai_acta_anulacion('".trim($_SESSION['login'])."','".$acta."','".trim($memo_contenido)."','".trim($_SESSION['user_depe_id'])."','".$tipo."') as ingresado ";
$resultado=pg_query($conexion,$sql) or die(utf8_decode("No se generó ninguna devolución"));


/*	$sql  = "select * from sai_anular_sopg('".trim($_SESSION['login'])."','".trim($codigo)."','";
	$sql  .= trim($memo_contenido)."','";
	$sql  .= trim($_SESSION['user_depe_id'])."','";
	$sql  .= $cero."','".$cero."','".$causado;
	$sql  .= "') as memo_id";*/
?>
<div class="normal" align="center">
  
      Se proces&oacute; satisfactoriamente la anulaci&oacute;n del acta de almac&eacute;n: <strong><?=$acta?></strong>.<br />
      Registro generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?>
      <br /><br />
</div>
</body>
</html>
<?php pg_close($conexion);?>
