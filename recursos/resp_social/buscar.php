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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:BUSCAR ART&Iacute;CULO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script LANGUAGE="JavaScript" SRC="../../includes/js/funciones.js"> </SCRIPT>

<script>
function deshabilitar_combo(valor)
{
 
 if(valor=='3')
 { 
  document.form.des_articulo.disabled=false;
  document.form.edo.disabled=true;
  document.form.tp_art.disabled=true;
  document.form.edo.value=0;
 }
 if(valor=='4')
 { 
  document.form.des_articulo.disabled=true;
  document.form.tp_art.disabled=true;
  document.form.edo.disabled=false;
  document.form.des_articulo.value=0;
 }
 if(valor=='5')
 { 
  document.form.des_articulo.disabled=true;
  document.form.edo.disabled=true;
  document.form.tp_art.disabled=false;
  document.form.des_articulo.value=0;
 }
 if(valor=='6')
 { 
  document.form.txt_clave.disabled=false;
  document.form.des_articulo.disabled=true;
  document.form.edo.disabled=true;
  document.form.tp_art.disabled=true;
  document.form.des_articulo.value=0;
 }
}

function detalle(codigo)
{
    url="detalle.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}

function ejecutar()
{
  if ((document.form.des_articulo.value=='0') && 
		  (document.form.edo.value=='0') && (document.form.tp_art.value=='0') && (document.form.txt_clave.value==''))
  {
     document.form.hid_buscar1.value=1;
  }
   else {document.form.hid_buscar1.value=2;}
   document.form.submit();
}

</script>
</head>
<body>
<form name="form" method="post" action="buscar.php">
<input type="hidden" name="hid_buscar1" value="0" /><br/><br/>
<table width="525" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" border="0">
  <tr class="td_gray"> 
    <td height="16" colspan="3" valign="middle" class="normalNegroNegrita">&ensp;B&uacute;squeda</td>
  </tr>
  <tr>
    <td><div align="right"><input name="opt_articulo" type="radio" value="3" onClick="javascript:deshabilitar_combo(3)" /></div></td>
    <td width="136" height="37" class="normalNegrita">Art&iacute;culo:</td>
    <td width="443" height="37" colspan="2" class="normal">
      <select name="des_articulo" id="des_articulo" class="normal" disabled="true">
	    <option value="0">[Seleccione]</option>
	    <?php
	      $sql_d="SELECT * FROM sai_seleccionar_campo('sai_item','id,nombre','id_tipo=4','nombre',1) resultado_set(arti_id varchar, arti_descripcion varchar)"; 
  	      $resultado_set_d=pg_query($conexion,$sql_d) or die("Error al mostrar");
	   
	      while($rowd=pg_fetch_array($resultado_set_d)) 
	      { 
 		   $arti_id=$rowd['arti_id'];
		   $arti_descripcion=$rowd['arti_descripcion'];
	    ?>
   	     <option value="<?=$arti_id?>"><?=$arti_descripcion?></option> 
        <?php } ?>
      </select>  </td>
  </tr>
  <tr>
    <td height="41" width="35" align="center" background="../../imagenes/fondo_tabla.gif"><div align="right"><span class="normalNegrita"><input name="opt_articulo" type="radio" value="4" onclick="javascript:deshabilitar_combo(4)" /></span></div></td>
    <td background="../../imagenes/fondo_tabla.gif" class="normalNegrita">Estado:</td>
    <td height="44" align="center"><div align="left">
      <select name="edo" class="normal" id="edo" disabled="disabled">
        <option value="0">[Seleccione]</option>
        <option value="1">Activo</option>
        <option value="2">Inactivo</option>
      </select></div></td>
  </tr>
  <tr>
    <td height="41" align="center" background="../../imagenes/fondo_tabla.gif"><div align="right"><span class="normalNegrita"><input name="opt_articulo" type="radio" value="4" onclick="javascript:deshabilitar_combo(5)" /></span></div></td>
    <td background="../../imagenes/fondo_tabla.gif" class="normalNegrita">Clasificaci&oacute;n:</td>
    <td height="36" colspan="2"class="normal">
	  <select name="tp_art" class="normal" id="tp_art" disabled="true">
        <option value="0">[Seleccione]</option>
	    <?php
	      $sql="SELECT * FROM sai_seleccionar_campo('sai_arti_tipo','tp_id,tp_desc','id_tipo=4 and tp_id<>'||'''$tipo_art''','tp_desc',1) resultado_set(tp_id varchar,tp_desc varchar)"; 
   	      $resultado=pg_query($conexion,$sql) or die("Error al mostrar");
	      while($row=pg_fetch_array($resultado))
	      { 
   	  	   $id=$row['tp_id'];
	  	   $descrip=$row['tp_desc'];
	   	?>
	  	<option value="<?=$id?>"><?php echo"$descrip";?></option> 
	  	<?php 
	  } ?>
	  </select> </td>
  </tr>
  <tr>
    <td height="44" colspan="3" align="center">
     <input class="normalNegro" type="button" value="Buscar" onclick="javascript:ejecutar();"/>
 </tr>
</table>
</form>
<br>
<br>
<form name="form2" method="post" action="">
<?php 
if ($_POST['hid_buscar1']==1)
{
 echo utf8_decode("<SCRIPT LANGUAGE='JavaScript'>"."alert ('Debe seleccionar una opci\u00F3n de b\u00FAsqueda');"."</SCRIPT>");
}
elseif ($_POST['hid_buscar1']==2)
   {
   	
   	if (($_POST['des_articulo'])!=0) 
	{
	 $titulo="Art&iacute;culos con nombre: ".$descrip;
	 $sql_tab="SELECT nombre,id,esta_nombre FROM sai_item t1,sai_estado t3 
	 WHERE id_tipo=4 and id='".$_POST['des_articulo']."' AND t1.esta_id=t3.esta_id";
	 }
     elseif (($_POST['edo'])!=0)
     {
	  $edo=trim($_POST['edo']);
      $sql_edo="select * from sai_consulta_desc_estado($edo) as descripcion"; 
	  $resultado_set_edo=pg_query($conexion,$sql_edo) or die("Error al consultar estado");
	  if($rowedo=pg_fetch_array($resultado_set_edo))
	  {$nombre_edo=trim($rowedo['descripcion']);}
	  $titulo="Art&iacute;culos con estado: ".$nombre_edo;
	  $sql_tab="SELECT nombre,id,esta_nombre FROM sai_item t1,sai_estado t3 
	  WHERE id_tipo=4 and t3.esta_id='".$_POST['edo']."' AND t1.esta_id=t3.esta_id";
	  
     }
     elseif (($_POST['tp_art'])!=0)
     {
      $tipo=trim($_POST['tp_art']);
      $sql_clasi="SELECT * FROM sai_seleccionar_campo('sai_arti_tipo','tp_desc','tp_id=''$tipo''','',2) resultado_set(tp_desc varchar)"; 
      $resultado_clasi=pg_query($conexion,$sql_clasi) or die("Error al consultar la categoria");
	  $row=pg_fetch_array($resultado_clasi);
      $titulo="Art&iacute;culos asociados a la clasificaci&oacute;n: ".$row['tp_desc'];
	  $sql_tab="SELECT nombre,t1.id,esta_nombre FROM sai_item t1, sai_item_articulo t2,sai_estado t3 
	  WHERE id_tipo=4 and t1.id=t2.id and tipo='".$_POST['tp_art']."' AND t1.esta_id=t3.esta_id";
     }
//echo $sql_tab;
	 $resultado_set_tab=pg_query($conexion,$sql_tab) or die("Error al mostrar 2"); 
	 $nroFila= pg_num_rows($resultado_set_tab);
	 if($nroFila<=0)
     {
     echo utf8_decode("<center><font class='normalNegrita'>"."Actualmente no se tienen artículos registrados con la categoría seleccionada"."</font></center>");
     }
     else 
     { 
	 ?>

    <table width="624" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
     <tr>
	   <td height="15" colspan="5" ><div align="center" ><span class="normalNegroNegrita">
	   <?php echo $titulo;?></span></div></td>
     </tr>
     <tr class="td_gray">
       <td width="60" align="center" class="normalNegroNegrita">C&oacute;digo</td>
       <td width="180" align="center" class="normalNegroNegrita">Nombre </td>
	   <td width="20" align="center" class="normalNegroNegrita">Estado</td>
	   <td width="84" align="center" class="normalNegroNegrita">Opciones</td>
     </tr>
     <?php 
      while($rowa=pg_fetch_array($resultado_set_tab)) 
	  { ?>
     <tr class="normal">
      <td align="center"><?=$rowa['id']?></td>
      <td align="left"><?=$rowa['nombre']?></td>
	  <td align="center"><?=$rowa['esta_nombre']?></td>
      <td align="center" class="normal">
	   <a href="javascript:detalle('<?=$rowa['id']?>')" class="normal"> Ver Detalle</a><br>
	   <a href="modificar_item.php?codigo=<?=$rowa['id']?>" class="normal"> Modificar</a></td>
     </tr>
     <?php }?>
   </table> 
	
<?php }
	 }// fin del else, que comprueba que existe una opcion de busqueda
?>
</form>
</body>
</html>
<?php pg_close($conexion);?>
