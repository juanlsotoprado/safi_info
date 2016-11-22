<?php 
    ob_start();
	session_start();
	 require_once("../../../includes/conexion.php");
	 
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	     {
		   header('Location:../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	     }
	
	ob_end_flush(); 
?>
<?php 
$marc=trim($_REQUEST['marc']);


$sql_marc = "select * from sai_seleccionar_campo('sai_bien_marca','bmarc_nombre,esta_id','bmarc_id=''$marc''','',0) Resultado_set(bmarc_nombre varchar, esta_id int4)";
$resultado_marc=pg_query($conexion,$sql_marc) or die("Error al conseguir los datos de la Marca");
$row_marc=pg_fetch_array($resultado_marc);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAI: DETALLES DE LA MARCA</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.Estilo1 {color: #FFFFFF}
-->
</style>
<script LANGUAGE="JavaScript" SRC="../../../includes/js/funciones.js"> </SCRIPT>


</head><body >
<form name="form1" method="post" action="marc_eanular.php">
<br />
<table width="369" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
	<td height="15" colspan="2" valign="midden" class="normalNegroNegrita"><strong>Detalle de marca de activos</strong></td>
    </tr>
	<tr>
	<td height="40" class="normalNegrita">Codigo:</td>
	<td height="40" class="normal"><? echo($marc); ?>      </td>
	</tr>
	<tr>
	  <td height="38" class="normalNegrita">Nombre: </td>
	  <td height="38"><span class="normal">
		<? echo($row_marc['bmarc_nombre']); ?>
	  </span></td>
    </tr>
	 <?php
			  $estado=$row_marc['esta_id'];
			  $sql_edo="select * from sai_consulta_desc_estado($estado) as descripcion"; 
			  $resultado_set_edo=pg_query($conexion,$sql_edo) or die("Error al consultar estado");
			  if($rowedo=pg_fetch_array($resultado_set_edo))
					 {
						$estado=$rowedo['descripcion'];
					 }  
	?>
     <tr>
       <td height="32" class="normalNegrita">Estado: </td>
       <td height="32" ><span class="normal"><? echo($estado); ?></span></td>
     </tr>
     <tr>
       <td height="15" colspan="2">&nbsp;</td>
     </tr>
     <tr>
      <td height="15" colspan="2"><br>
	  <div align="center">
	  <a href="javascript:window.print()"><img src="../../../imagenes/boton_imprimir.gif" border="0"></a><br><br>
<input type="button" value="Cerrar" onclick="javascript:window.close()"></input>


<br>
	  <br></div>	  </td>
    </tr>
</table>
</form>
</body>
</html>
