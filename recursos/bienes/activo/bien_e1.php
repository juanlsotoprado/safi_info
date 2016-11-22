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
	    //Datos proveniente del formulario origen
		$nombre=trim($_POST['txt_nombre']);	 
		$descrip=trim($_POST['txt_bien_descripcion']);
		$tipo_activo=trim($_POST['tipo_activo']);
		$bien_exi_min=trim($_POST['txt_exist_min']);
		$vida_util=trim($_POST['txt_vida_util']);	
		$usua_login=$_SESSION['login'];
		
		if ($vida_util=="" or $bien_exi_min==""){
			$vida_util=0;
			$bien_exi_min=0;
		}
		
		  //Buscamos el nombre de la Clasificación del artículo
  		$sql_clasif = "select * from bien_categoria where id=".$tipo_activo."";
  		$resultado_clasif=pg_query($conexion,$sql_clasif) or die("Error al conseguir el nombre de la Clasificacion");
  		$row_clasif=pg_fetch_array($resultado_clasif);
		
		//Buscamos el mayor del campo de bien_id
		$sql = "select MAX(CAST(id as integer)) as codigo from sai_item";
		$resultado=pg_query($conexion,$sql) or die(utf8_decode("Error al conseguir el Código del Bien"));
	    if($row=pg_fetch_array($resultado))
		{  	 
		   $codi=$row['codigo'];
		   $codi_new=$codi+1;
		}
		
?>
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<title>.:SAFI:EJECUCION DE INGRESAR BIEN</title>
<link  rel="stylesheet"   href="../../../css/plantilla.css" type="text/css" media="all"  />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body>
<p>
<?php
$sql_in = "select * from  sai_insert_bien('" . $codi_new. "', '". $nombre . "','". $descrip . "' , '". $usua_login . "', ". $bien_exi_min . ", ".$vida_util.",".$tipo_activo.") As resultado_set(int)";
$resultado=pg_query($conexion,$sql_in);

if ($row_in = pg_fetch_array($resultado))
{
?>
 </p>
 <br />
 <table width="647" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
	<td height="15" colspan="2" valign="midden" class="normalNegroNegrita">Registro de activos</td>
			</tr>
			<tr>
			<td width="209" height="11" valign="midden"></td>
			<td width="426" height="11" valign="midden"></td>
			</tr>
			<tr>
			<td height="29" valign="midden"><span class="normalNegrita">C&oacute;digo del activo:</span></td>
			<td height="29" valign="midden" class="normalNegro"><?php echo $codi_new;?>&nbsp;</td>
			</tr>
			<tr class="normal">
			<td height="31" valign="midden" class="normalNegrita">Nombre del activo:</td>
			<td height="31" valign="midden" class="normalNegro"><?php echo $nombre;?></td>
			</tr>
			<tr class="normal">
			  <td height="33" valign="midden" class="normalNegrita">Descripci&oacute;n:</td>
			  <td height="33" valign="midden" class="normalNegro"><?php echo $descrip;?></td>
			  </tr>
			<tr class="normal"> 
			<td height="35" valign="midden" class="normalNegrita">Existencia m&iacute;nima:</td>
			<td height="35" valign="midden" class="normalNegro"><?php echo $bien_exi_min;?></td>
			</tr>
			<tr class="normal"> 
			<td height="35" valign="midden" class="normalNegrita">Vida &uacute;til(A&#241;os): </td>
			<td height="35" valign="midden" class="normalNegro"><?php echo $vida_util;?></td>
			</tr>
			<tr class="normal"> 
			<td height="38" valign="midden" class="normalNegrita">Estado: </td>
			<td height="38" valign="midden" class="normalNegro">Activo</td>
			</tr>
			<tr>
			<td height="15" colspan="2" align="center" class="normal">
			<br>
<div align="center">
			Registro generado  el d&iacute;a <?=date("d/m/y")?> a las <?=date("H:i:s")?><br><br>
			<br> <a href="javascript:window.print()"><img src="../../../imagenes/boton_imprimir.gif" border="0"></a><br><br>
			<a href="index.php"><img src="../../../imagenes/boton_reg.gif" name="regresar" width="90" height="31" border="0" id="regresar" /></a><br>		</div>	</td>
			</tr>  <br />
</table>
            <?php 		
}
else
   {    ?>
            <br />
            <br />
            <table width="647" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray"> 
		<td width="639" height="16" colspan="3" valign="midden" class="normalNegroNegrita"> 
		Administrar bienes</td>
		</tr>
		<tr>
		<td colspan="4" class="normal"><br>
		<div align="center">
		<img src="../../../imagenes/vineta_azul.gif" width="11" height="7">
		Ha ocurrido un error al ingresar los datos 
		del Bien <br>
		<?php echo(pg_errormessage($conexion)); ?><br>
		<img src="../../../imagenes/mano_bad.gif" width="31" height="38">
		<br>
		<br>
		</div>
		</td>
		</tr>
</table>
		<?php   } ?>
</body>
</html>
