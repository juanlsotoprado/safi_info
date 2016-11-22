<?php 
    ob_start();
	session_start();
	 require_once("../../../includes/perfiles/constantesPerfiles.php");
	 require_once("../../../includes/conexion.php");
	 
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	     {
		   header('Location:../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	     }
	$idPerfil = $_SESSION['user_perfil_id'];
	ob_end_flush(); 
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
<script>
function deshabilitar_combo(valor)
{
 if(valor=='1')
 { 
  document.form.cod_bien.disabled=false;
  document.form.des_bien.disabled=true;
  document.form.des_bien.value='0';
 }
 
 if(valor=='2')
 { 
  document.form.cod_bien.disabled=true;
  document.form.des_bien.disabled=false;
  document.form.cod_bien.value='0';
 }
 
}
function detalle(tipo)
{
    url="detalle_categoria.php?cat="+tipo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}
function ejecutar()
{
  if ((document.form.cod_bien.value=='0') && (document.form.des_bien.value=='0') )
  {
     document.form.hid_buscar1.value=1;
  }
   else {document.form.hid_buscar1.value=2;}
 document.form.submit();   
}

</script>

<style type="text/css">
<!--
.style9 {
	color: #FFFFFF;
	font-weight: bold;
}
.style10 {color: #FFFFFF}
-->
</style>
</head>
<body>
<form name="form" method="post" action="buscar_categoria.php">
<input type="hidden" name="hid_buscar1" value="0" />
<br />
<br />
<table width="447" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
      <td height="15" colspan="3" valign="midden" class="normalNegroNegrita">B&uacute;squeda de categor&iacute;as de materiales</td>
</tr>
<tr>
  <td width="38"><div align="right">
    <input name="opt_articulo" align="right" type="radio" value="1" onClick="javascript:deshabilitar_combo(1)" />
  </div></td>
  <td width="233" height="33" class="normalNegrita">C&oacute;digo:</td>
  <td width="280" height="33" class="normal">
  <select name="cod_bien" id="cod_bien" class="normalNegro" disabled="true">
	   <option value="0">[Seleccione]</option>
	   <?php
	    $sqlc="SELECT * FROM sai_seleccionar_campo('sai_arti_tipo','tp_id','id_tipo=1','tp_id',1) resultado_set(tp_id varchar)"; 
		$resultado_set_c=pg_query($conexion,$sqlc) or die("Error al mostrar los Tipos");
	    while($rowc=pg_fetch_array($resultado_set_c)) 
	    { 
 		 $marc_id=$rowc['tp_id'];
		?>
   	     <option value="<?=$marc_id?>"><?=$marc_id?></option> 
  <?php } ?>
  </select>  </td>
</tr>
<tr>
  <td><div align="right">
    <input name="opt_articulo" type="radio" value="2" onClick="javascript:deshabilitar_combo(2)" />
  </div></td>
  <td width="233" height="32" class="normalNegrita">Nombre:</td>
  <td width="280" height="32" class="normal">
  <select name="des_bien" id="des_bien" class="normalNegro" disabled="true">
	   <option value="0">[Seleccione]</option>
	   <?php
	    $sql_d="SELECT * FROM sai_seleccionar_campo('sai_arti_tipo','tp_id,tp_desc','id_tipo=1','tp_desc',1) resultado_set(tp_id varchar, tp_desc varchar)"; 
	    $resultado_set_d=pg_query($conexion,$sql_d) or die("Error al mostrar");
	    while($rowd=pg_fetch_array($resultado_set_d)) 
	    { 
 		 $marc_id=$rowd['tp_id'];
		 $marc_descripcion=$rowd['tp_desc'];
	   ?>
   	     <option value="<?=$marc_id?>"><?=$marc_descripcion?></option> 
  <?php } ?>
  </select>  </td>
</tr>
<tr>
  <td height="44" colspan="3" align="center">
  <input type="button" class="normalNegro" value="Buscar" onclick="javascript:ejecutar()"></input>
  
  </td>
</tr>
</table>
<br />
</form>
<form name="form2" method="post" action="">
<?php 
if ($_POST['hid_buscar1']==1)
{
    echo "<SCRIPT LANGUAGE='JavaScript'>"."alert ('Debe seleccionar una opci\u00F3n de b\u00FAsqueda');"."</SCRIPT>";
}
else
   {
	   if ( (($_POST['cod_bien'])!=0) or (($_POST['des_bien'])!=0) )
	   {
		   if (($_POST['cod_bien'])!=0) { $codigo=trim($_POST['cod_bien']); }
		   else { $codigo=trim($_POST['des_bien']); }
		   $sql_tab="SELECT * FROM sai_seleccionar_campo('sai_arti_tipo','tp_desc','tp_id=''$codigo''','',2)
		   resultado_set(tp_desc varchar)";  
		   $resultado_set_tab=pg_query($conexion,$sql_tab) or die("Error al mostrar"); 
		   if($rowa=pg_fetch_array($resultado_set_tab)) 
		   {   
		   		?>
		   		<table width="647" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
				<tr class="td_gray">
				<td width="84" align="center" class="normalNegroNegrita"><strong>Codigo de la categor&iacute;a</strong></td>
    			<td width="131" align="center" class="normalNegroNegrita"><strong>Nombre de la categor&iacute;a</strong></td>
				<td width="93" align="center" class="normalNegroNegrita"><strong>Opciones</strong></td>
  				</tr>
  				<tr class="normal">
				<td align="center"><?=$codigo?></td>
    				<td align="center"><?=$rowa['tp_desc'];?></td>
    				<td align="left" class="normal">
				  <img src="../../../imagenes/vineta_azul.gif" width="11" height="7"><a href="javascript:detalle('<?=$codigo?>')" class="normal"> Ver Detalle</a><br>
				  <?php if($idPerfil != PERFIL_ANALISTA_I_PASANTE_BIENES){?>
				  <img src="../../../imagenes/vineta_azul.gif" width="11" height="7"><a href="modificar_categoria.php?cat=<?=$codigo?>" class="normal"> Modificar</a>
				  <?php }?>
				</td>
 				</tr>
 			 </table> 
<?php
		  } 	//fin del row de la tabla
		}	//fin del post del articulo
	 }// fin del else, que comprueba que existe una opcion de busqueda
?>
</form>
</body>
</html>
