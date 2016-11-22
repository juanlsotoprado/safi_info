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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI: INGRESAR BIEN NACIONAL</title>
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
	$sql="Select * from sai_modifi_marcas( ".$_POST['marc_cod'].", '".$_POST['marc']."','".$_SESSION['login']."',".$_POST['opt_estado'].")";
   //echo($sql);
   $resultado=pg_query($conexion,$sql);
	if ($row = pg_fetch_array($resultado))
	{
?>
<table width="366" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
	<td height="15" colspan="2" valign="midden" class="normalNegroNegrita"><strong>Modificar marca de activos</strong></td>
    </tr>
	<tr>
	<td height="40" valign="midden" class="normalNegrita">C&oacute;digo:</td>
		<td height="40" valign="midden" class="peq_naranja">
		  <? echo($_POST['marc_cod']); ?>		   </td>
	</tr>
	<tr>
	  <td height="38" valign="midden"><span class="normalNegrita">Nombre: </span></td>
	  <td height="38" valign="midden"><span class="peq_naranja"><? echo($_POST['marc']); ?>
	  </span></td>
    </tr>
     <tr>
         <td class="normalNegrita" >Estado:</td>
         <td height="37" class="peq_naranja">
           <?php if($_POST['opt_estado']==1){?>
           Activo
           <?php }else{?>
           Inactivo
           <?php }?>         
		 </td>
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
		Modificar marca de activos</strong></div></td>
		</tr>
		<tr>
		<td colspan="4" class="normal"><br>
		<div align="center">
		<img src="../../../imagenes/vineta_azul.gif" width="11" height="7">
		Ha ocurrido un error al ingresar los datos 
		de la Marca de Bienes <br>
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
