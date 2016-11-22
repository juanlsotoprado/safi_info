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
$sql = "select MAX(CAST(tp_id as integer)) as codigo from sai_arti_tipo";
		$resultado=pg_query($conexion,$sql) or die("Error al conseguir los datos de la categoria del activo");
	    if($row=pg_fetch_array($resultado))
		{  	 
		   $codi=$row['codigo'];
		   $codi_new=$codi+1;
		   //echo($codi_new);
		}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI: INGRESAR MARCA - BIEN NACIONAL</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.Estilo1 {color: #FFFFFF}
-->
</style>
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>

</head><body>
<form name="form1" method="post" action="tipo_e1.php">
<br />
<?
	$sql="INSERT INTO sai_arti_tipo (tp_id,tp_desc,id_tipo) VALUES ($codi_new, '".strtoupper($_POST['tipo'])."',4)";
   //echo($sql);
   $resultado=pg_exec($conexion,$sql);
   $sql="SELECT * FROM sai_arti_tipo WHERE tp_id='".$codi_new."'";
   $resultado=pg_exec($conexion,$sql);
	if ($row = pg_fetch_array($resultado))
	{
?>
<table width="466" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
	<td height="15" colspan="2" align="midden"  class="normalNegroNegrita"><strong>Registro de categor&iacute;a de materiales</strong></td>
    </tr>
<tr>
	  <td height="38" valign="midden" width="250"><div align="right"><span class="normalNegrita">C&oacute;digo: </span></div></td>
	  <td height="38" valign="midden"><span class="normalNegro"><? echo($codi_new); ?>
		
	  </span></td>
    </tr>
<tr>
	  <td height="38" valign="midden" width="250"><div align="right"><span class="normalNegrita">Nombre: </span></div></td>
	  <td height="38" valign="midden"><span class="normalNegro"><? echo($_POST['tipo']); ?>
		
	  </span></td>
    </tr>
<tr>
	<td height="16" colspan="3" align="center" class="normal">
	<br><div align="center">
	Detalle modificado el d&iacute;a <?=date("d/m/y")?> a las <?=date("H:i:s")?><br><br>
	<a href="javascript:window.print()"><img src="../../../imagenes/boton_imprimir.gif" border="0"></a><br><br>
		</div>
	<br>	</td>
	</tr>
</table>
<?php  		
	}
	else
	{   
?>
<br />
            <br />
            <table width="647" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray"> 
		<td width="639" height="16" colspan="3" valign="midden"><div align="left" class="normalNegroNegrita"><strong> 
		Administrar categor&iacute;a activos </strong></div></td>
		</tr>
		<tr>
		<td colspan="4" class="normal"><br>
		<div align="center">
		<img src="../../../imagenes/vineta_azul.gif" width="11" height="7">
		Ha ocurrido un error al ingresar la categor&iacute;a de los activos<br>
		<?php echo(pg_errormessage($conexion)); ?><br>
		<img src="../../../imagenes/mano_bad.gif" width="31" height="38">
		<br>
		<br>
		</div>
		</td>
		</tr>
</table>
		<?php
   }
?>
</form>
</body>
</html>
