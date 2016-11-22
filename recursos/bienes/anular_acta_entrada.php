<?
ob_start();
require_once("../../includes/conexion.php");
 
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}

ob_end_flush();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all" />
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>

<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?php 
$acta=$_GET['codigo'];

$memo_contenido=trim($_POST['contenido_memo']);
if ($memo_contenido==""){
 	$memo_contenido="No Especificado";
 }

  $sql  = "select * from anular_acta_entrada('".$_SESSION['login']."','".$acta."','".$memo_contenido."','".$_SESSION['user_depe_id']."') as memo_id";
  $resultado_set = pg_query($conexion ,$sql) or die ("NO SE PUDO REALIZAR LA ANULACION DEL ACTA ".$sql);
      	
?>
<form action="anular_custodia_Accion.php" name="form" id="form" method="post">
<table width="300" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" id="sol_via">
  <tr>
	<td height="15" colspan="2" valign="midden" class="td_gray"><span class="normalNegroNegrita">ANULACI&Oacute;N Acta de Entrada  </span></td>
  </tr>
  <tr>
	<td class="normal"><strong>ACTA No: </strong><?php echo $acta;?></td>
  </tr>
  <tr>
	<td class="normalNegro">Fue anulada el acta de entrada</td>
	
  </tr>
		</table>
	  </td>
	</tr>
	</form>
</body>
</html>