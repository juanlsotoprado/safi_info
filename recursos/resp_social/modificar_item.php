<?php 
  ob_start();
  session_start();
  require_once("../../includes/conexion.php");
  include (dirname ( __FILE__ ) . '/../../init.php');
	 
   if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
   {
     header('Location:../../index.php',false);
	 ob_end_flush(); 
	 exit;
   }
   ob_end_flush(); 
?>
<?php $codigo=trim($_GET['codigo']);?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:MODIFICAR ART&Iacute;CULO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script LANGUAGE="JavaScript" SRC="../../includes/js/funciones.js"> </SCRIPT>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"
	charset="utf-8"></script>

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/jquery-ui-1.8.13.custom.min.js';?>"
	charset="utf-8"></script>
<link
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />

<script language="javascript">

function revisar()
{ 
   if (document.form2.txt_nombre_arti.value=="")
   {
	alert("Debe especificar la descripci\u00F3n del articulo");
	document.form2.txt_nombre_arti.select();
    document.form2.txt_nombre_arti.focus();
	return;
   }	
   if(confirm("Estos datos ser\u00E1n modificados. Esta seguro que desea continuar?"))
   {
	 document.form2.submit();
   }
}	

function verificar_descripcion()
{ 
   palabra=document.form2.txt_nombre_arti.value;
   <?php
   $sql_p="SELECT * FROM sai_seleccionar_campo('sai_item','nombre','id_tipo=1','',2) resultado_set(nombre varchar)"; 
   $resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
   while($row=pg_fetch_array($resultado_set_most_p)) 
   {?>
    palabra1='<?php echo trim($row['nombre']); ?>';
	if (palabra.toUpperCase()==palabra1.toUpperCase())
    {
	 alert("Esta descripci\u00F3n ya existe en la base de datos...");
	 document.form2.txt_nombre_arti.value='';
	}
    <?php
   }
  ?> 
}
function rela(part_id) 
{
  document.form2.txt_cod_part.value=document.form2.partida.value;
}
 
/*Funcii�n que valida que solo se introduzcan digitos caracteres y numericos en el campo*/
function validar_digito(objeto)
{
	var checkOK = "ABCDEFGHIJKLMN�OPQRSTUVWXYZ�����abcdefghijklmn�opqrstuvwxyz�����0123456789áéíóúÁÉÍÓÚ -_.;',()¾/";
	var checkStr = objeto.value;
	var allValid = true;
	for (i = 0;  i < checkStr.length;  i++)
	{
		ch = checkStr.charAt(i);
		for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
		if (j == checkOK.length)
		{
			var cambio=checkStr.substring(-1,i) 
			objeto.value=cambio;
			alert("Estos caracteres no est\u00E1n permitidos");;
			break;
		}
	}
}
</script>
</head>
<body>
<form name="form2" action="modificar_itemAccion.php" method="post">
<?php 
#Efectuamos la consulta SQL  

$sql_art="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','t1.id,nombre,esta_id,unidad_medida,usua_login,existencia_minima,tipo','t1.id=t2.id and t1.id='||'''$codigo''','',2) 
resultado_set(id varchar, nombre varchar,esta_id int4,unidad_medida varchar,usua_login varchar,existencia_minima int4,tipo varchar)"; 
$resultado_set_art=pg_query($conexion,$sql_art) or die(utf8_decode("Error al mostrar Artículos"));
if($row=pg_fetch_array($resultado_set_art)) 
{ 
 	$arti_descripcion=$row['nombre'];
 	$unme_id=$row['unidad_medida'];
	$esta_id=$row['esta_id'];
	$codigo=$row['id'];
	$tipo_art=$row['tipo'];

	?>
	<input name="txt_articulo" type="hidden" value="<?=$row['id']?>" />
    <?php
	//Buscamos el nombre de la Unidad de Medida seleccionada
	$sql_uni = "select * from sai_seleccionar_campo('sai_uni_medida','unme_descrip','unme_id='||'''$unme_id''','',0) Resultado_set(unme_descrip varchar)";
	$resultado_uni=pg_query($conexion,$sql_uni) or die("Error al conseguir el Nombre de la Unidad");
	if($row_uni=pg_fetch_array($resultado_uni)) {
	$medida=$row_uni['unme_descrip']; }
		//Buscamos el nombre de la Clasificación del artículo
		$sql_clasif = "select * from sai_seleccionar_campo('sai_arti_tipo','tp_desc','tp_id='||'''$tipo_art''','',0) Resultado_set(tp_desc varchar)";
		$resultado_clasif=pg_query($conexion,$sql_clasif) or die("Error al conseguir el nombre de la Clasificacion");
        $row_clasif=pg_fetch_array($resultado_clasif);
	?>
    <table width="674" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
	<td height="15" colspan="4" valign="middle" class="normalNegroNegrita">Modificar</td>
	 </tr>
      <tr>
        <td height="32"><div align="left" class="normal"><b>Art&iacute;culo:</b></div></td>
        <td height="32" colspan="2"><span class="normal">
          <?=$codigo?> <input name="txt_nombre_arti" type="text" class="normal" size="26" value="<?=$arti_descripcion?>" onchange="verificar_descripcion()"/> <span class="peq_naranja">(*)</span> 
        </span></td>
      </tr>
       <tr>
	  <td class="normalNegrita" align="left">Estado:</td>
	  <td height="42" colspan="4" class="normalNegrita">
	  <?php if($esta_id==1){?>
	  <input name="opt_estado" type="radio" value="1" checked="checked" />Activo
	  <input name="opt_estado" type="radio" value="2"/>Inactivo
	  <?php }else{?>
	  <input name="opt_estado" type="radio" value="1"/>Activo
	  <input name="opt_estado" type="radio" value="2" checked="checked"/>Inactivo
	  <?php }?><input type="hidden" name="criterio" value="<?=$_GET['criterio'];?>" />	 
		   <input type="hidden" name="valor" value="<?=$_GET['valor'];?>" /> </td>
	  </tr>
	      <tr> 
      <td height="36"> <div align="left" class="normalNegrita">Unidad de medida:</div></td>
      <td height="36" colspan="2"class="normal">
	  <select name="unidad" class="normal" id="unidad">
      <option value="<?=$unme_id?>"><?=$medida?></option>
	  <?php
	  $sql="SELECT * FROM sai_seleccionar_campo('sai_uni_medida','unme_id, unme_descrip','','',2) resultado_set(unme_id varchar,unme_descrip varchar)"; 
	  $resultado=pg_query($conexion,$sql) or die("Error al mostrar");
	  while($row=pg_fetch_array($resultado))
	  { 
   	  	$id_unidad=$row['unme_id'];
	  	$descrip_unidad=$row['unme_descrip'];
	   	?>
	  	<option value="<?=$id_unidad?>"><?php echo"$descrip_unidad";?></option> 
	  	<?php 
	  } ?>
	  </select> <span class="peq_naranja">(*)</span> </td>
      </tr>
      <tr> 
      <td height="36"> <div align="left" class="normalNegrita">Clasificaci&oacute;n:</div></td>
      <td height="36" colspan="2"class="normal">
	  <select name="tp_art" class="normal" id="tp_art">
      <option value="<?=$tipo_art;?>"><?=$row_clasif['tp_desc'];?></option>
	  <?php
	  $sql="SELECT * FROM sai_seleccionar_campo('sai_arti_tipo','tp_id,tp_desc','id_tipo=4 and tp_id<>12 and tp_id<>'||'''$tipo_art''','tp_desc',1) resultado_set(tp_id varchar,tp_desc varchar)"; 

	  $resultado=pg_query($conexion,$sql) or die("Error al mostrar");
	  while($row=pg_fetch_array($resultado))
	  { 
   	  	$id=$row['tp_id'];
	  	$descrip=$row['tp_desc'];
	   	?>
	  	<option value="<?=$id?>"><?php echo"$descrip";?></option> 
	  	<?php 
	  } ?>
	  </select> <span class="peq_naranja">(*)</span> </td>
      </tr>
       <tr>
      <td height="15" colspan="3" align="center">
	  <br><div align="center">
	  <input class="normalNegro" type="button" value="Modificar" onclick="javascript:revisar()"/>

	  															 	   <br><br></div>	  </td>
	  </tr>
  </table>
	  <?php
}  //fin de la consulta principal
?>
</form>
</body>
</html>
<?php pg_close($conexion);?>