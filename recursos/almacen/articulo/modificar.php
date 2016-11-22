<?php 
  ob_start();
  session_start();
  require_once("../../../includes/conexion.php");
  require("../../../includes/perfiles/constantesPerfiles.php");
   
   if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
   {
     header('Location:../../../index.php',false);
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
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script LANGUAGE="JavaScript" SRC="../../../includes/js/funciones.js"> </SCRIPT>

<script lenguage="javascript">

function revisar(tipo)
{ 
 if (tipo==1){
	  if (document.form2.partida.value=="")
	   {
		alert("Debe especificar la partida asociada al material");
		document.form2.partida.focus();
		return;
	   }	
	  document.form2.accion.value="presupuesto";
 }else{ 
   if (document.form2.txt_nombre_arti.value=="")
   {
	alert("Debe especificar la descripci\u00F3n del articulo");
	document.form2.txt_nombre_arti.select();
    document.form2.txt_nombre_arti.focus();
	return;
   }	
   document.form2.accion.value="bienes";
   }
   if(confirm("Estos datos ser\u00E1n modificados. \u00BFEst\u00E1 seguro que desea continuar?"))
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
<form name="form2" action="modificarAccion.php" method="post">
	  <input type="hidden" name="accion" value=""></input>
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

	$sql_art1="SELECT * FROM sai_seleccionar_campo('sai_item_partida','pres_anno, part_id','id_item='||'''$codigo''','',2) 
	resultado_set(pres_anno int4, part_id varchar)"; 
	$resultado_set_art1=pg_query($conexion,$sql_art1) or die(utf8_decode("Error al mostrar Artículos"));
	if($row1=pg_fetch_array($resultado_set_art1)) {
		$part_id=$row1['part_id'];
	}
	?>
	<input name="txt_articulo" type="hidden" value="<?=$row['id']?>" />
    <?php
	//Buscamos el nombre de la Unidad de Medida seleccionada
	$sql_uni = "select * from sai_seleccionar_campo('sai_uni_medida','unme_descrip','unme_id='||'''$unme_id''','',0) Resultado_set(unme_descrip varchar)";
	$resultado_uni=pg_query($conexion,$sql_uni) or die("Error al conseguir el Nombre de la Unidad");
	if($row_uni=pg_fetch_array($resultado_uni)) {
	$medida=$row_uni['unme_descrip']; }
	//Buscamos nombre de partida
	$sql_parti = "select * from sai_seleccionar_campo('sai_partida','part_nombre','part_id='||'''$part_id''','',0) Resultado_set(part_nombre varchar)";
	$resultado_parti=pg_query($conexion,$sql_parti) or die(utf8_decode("Error al conseguir el Año de la Partida"));
	if($row_parti=pg_fetch_array($resultado_parti))
	{$part_nom=$row_parti['part_nombre']; }

		//Buscamos el nombre de la Clasificación del artículo
		$sql_clasif = "select * from sai_seleccionar_campo('sai_arti_tipo','tp_desc','tp_id='||'''$tipo_art''','',0) Resultado_set(tp_desc varchar)";

		$resultado_clasif=pg_query($conexion,$sql_clasif) or die("Error al conseguir el nombre de la Clasificacion");
	        $row_clasif=pg_fetch_array($resultado_clasif);
	
	 if(($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO)){ ?>
	
    <table width="674" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
	<td height="15" colspan="4" valign="midden" class="normalNegroNegrita">Modificar</td>
	 </tr>
	 <tr>
  	 <td height="35" valign="midden"><div align="left" class="normalNegrita">
  	   <div align="left">Nombre de la Partida:</div>
  	 </div></td>
	 <td height="35" colspan="2" valign="midden"><select name="partida" id="partida" onchange="rela(form2.partida.options[form2.partida.selectedIndex].text)" class="normal">
       <option value="<?=$part_id?>"><?=$part_id.":".$part_nom;?></option>
       <?php
       $sql_part ="SELECT part_id,part_nombre  from sai_partida ".
				 "WHERE substring (TRIM(part_id) from 9 for  11)  <>'00.00' and  (trim(part_id) like '4.01.%' or trim(part_id) like '4.02.%' or trim(part_id)='4.07.01.02.01') and ".
				 "pres_anno= '".$_SESSION['an_o_presupuesto']."' Order by part_id asc";
	      $resultado_part=pg_query($conexion,$sql_part) or die("Error al mostrar");
	 	  while($row_part=pg_fetch_array($resultado_part))
	      { 
   	        $part_ide=$row_part['part_id'];
	        $part_nombre=$row_part['part_nombre'];
	       ?>
       <option value="<?=$part_ide?>"><?=$part_ide?>:<?php echo $part_nombre;?></option>
       <?php 
	      } ?>
     </select> <span class="peq_naranja">(*)</span></td>
	 </tr>
      <tr>
        <td height="32"><div align="left" class="normal"><b>Art&iacute;culo:</b></div></td>
        <td height="32" colspan="2"><span class="normal">
          <?=$codigo?> <input name="txt_nombre_arti" type="text" class="normal" size="50" value="<?=$arti_descripcion?>" readonly/> <span class="peq_naranja">(*)</span> 
        </span></td>
      </tr>
      <tr>
      <td height="15" colspan="3" align="center">
	  <br><div align="center">
	  <input type="hidden" name="unidad" value="<?=$unme_id;?>"></input>
	  <input type="hidden" name="tp_art" value="<?=$tipo_art;?>"></input>
	  <input type="hidden" name="opt_estado" value="<?=$esta_id;?>"></input>
	  <input class="normalNegro" type="button" value="Modificar" onclick="javascript:revisar(1)"/>
	   <br><br></div>	  </td>
	  </tr>
  </table>
  <?php }else{?>
  <table width="674" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
	<td height="15" colspan="4" valign="midden" class="normalNegroNegrita">Modificar</td>
	 </tr>
      <tr>
        <td height="32"><div align="left" class="normal"><b>Art&iacute;culo:</b></div></td>
        <td height="32" colspan="2"><span class="normal">
          <?=$codigo?> <input name="txt_nombre_arti" type="text" class="normal" size="50" value="<?=$arti_descripcion?>" onkeyup="validar_digito(txt_nombre_arti)" onchange="verificar_descripcion()"/> <span class="peq_naranja">(*)</span> 
        </span></td>
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
	  $sql="SELECT * FROM sai_seleccionar_campo('sai_arti_tipo','tp_id,tp_desc','id_tipo=1 and tp_id<>12 and tp_id<>'||'''$tipo_art''','tp_desc',1) resultado_set(tp_id varchar,tp_desc varchar)"; 

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
      <td height="27" valign="midden"><div align="left" class="normalNegrita">Existencia actual:</div></td>
      <td height="27" colspan="2" valign="midden">
	  <?php
	  $sql_tabla2="SELECT sum(disponible) as cantidad FROM sai_arti_almacen where arti_id='".$codigo."'"; 
	  $resultado_set_t2=pg_query($conexion,$sql_tabla2) or die("Error al mostrar existencia");
	  if($rowt2=pg_fetch_array($resultado_set_t2)){$existencia_act=$rowt2['cantidad']; }
	  ?>
	  <input name="txt_exist" type="text" class="normal" size="26" value="<?=$existencia_act?>" disabled/>	  </td>
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
      <td height="15" colspan="3" align="center">
	  <br><div align="center">
	  <input class="normalNegro" type="button" value="Modificar" onclick="javascript:revisar(0)"/>
	   <br><br></div>	  </td>
	  </tr>
  </table>
	  <?php }
}  //fin de la consulta principal
?>
</form>
</body>
</html>
<?php pg_close($conexion);?>