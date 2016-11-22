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

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>.:SAFI:Memo</title>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"></script>
<?php	
$cont=trim($_GET['cont']);
$A= '             ';
$codigo=$_REQUEST['codigo']; 

$sql="SELECT * FROM  sai_seleccionar_campo ('sai_memo','memo_id,usua_login,memo_asunto,memo_contenido,memo_fecha_crea','memo_id=''$codigo''','',2)
resultado_set(memo_id varchar, usua_login varchar,memo_asunto varchar,memo_contenido text,memo_fecha_crea timestamp)"; 
$resultado=pg_query($conexion,$sql) or die("Error al mostrar"); 
while($row=pg_fetch_array($resultado))
			{ 
				$id_memo=trim($row['memo_id']);
				$usua_login=$row['usua_login'];
				$asunto=$row['memo_asunto'];
				$comentario=$row['memo_contenido'];
				$fecha_emis=$row['memo_fecha_crea'];
			} 
?>
</head>
<body >
<form name="form" method="post" action="codi_e1.php">
<table width="450" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaCentral" >
  <tr class="td_gray">
    <td colspan="2" class="normalNegrita">DETALLE DE LA DEVOLUCI&Oacute;N </td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="normalNegrita">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="normalNegrita"><div align="center">Comprobante N&uacute;mero: <?php echo $codigo;?></div></td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="normalNegrita">&nbsp;</td>
  </tr>
  <tr>
    <td width="81"  class="normalNegrita"><strong>Fecha:</strong></td>
    <td width="671"  align="right" class="normal"><div align="left"><?php echo $fecha_emis?></div></td>
  </tr>
  <tr>
    <td class="normalNegrita"><strong>Asunto:</strong></td>
    <td  align="right" class="normal"><div align="left"><?php echo $asunto?></div></td>
  </tr>
  <tr>
    <td class="normalNegrita"><strong>Comentario:</strong></td>
    <td align="right" class="normal"><div align="left"><?php echo $comentario?></div></td>
  </tr>
  <tr>
    <td  align="right" class="normalNegrita">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" class="normalNegrita">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center" class="normalNegrita"><a href="javascript:window.print()" class="normal"><img src="../../imagenes/boton_imprimir.gif" alt="impresora" width="23" height="20" border="0" /> </a></td>
  </tr>
  <tr>
    <td colspan="2" align="center" class="normalNegrita">
     <input type="button" value="Cerrar" onclick="javascript:window.close();"></input>
    </td>
  </tr>
</table>
</form>
</body>
</html>
