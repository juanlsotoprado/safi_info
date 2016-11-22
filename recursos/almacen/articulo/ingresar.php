<?php 
  ob_start();
  session_start();
  require_once("../../../includes/conexion.php");
	 
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../../index.php',false);
   ob_end_flush(); 
   exit;
  }
  ob_end_flush(); 

  $descripcion=trim($_REQUEST['descripcion']);?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI:INGRESAR ART&Iacute;CULO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
<script language="JavaScript" src="../../../js/lib/actb.js"></script>
<script>

function estaEnPartidas(nombre, idPartida){
	for(j = 0; j < nombresPartidas.length; j++){
		if(trim(nombre)==trim(nombresPartidas[j]) && trim(idPartida)==trim(idsPartidas[j])){
			return j;
		}
	}
	return -1;
}

function descripcion_compras()
{ 
   palabra="<?php $descripcion;?>";
   if(palabra!="")
   {
	   <?php
	   $sql_p="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','nombre','t1.id=t2.id','',2) resultado_set(nombre varchar)"; 
	   $resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
	   while($row=pg_fetch_array($resultado_set_most_p)) 
	   {?>
			palabra1="<?php echo trim($row['arti_descripcion']); ?>"
			if (palabra.toUpperCase()==palabra1.toUpperCase())
			{
				 alert("Esta descripci\u00F3n ya existe en la base de datos...");
				 document.form1.descrip.value='';
			}
			<?php
	   }
	  ?> 
	}
}

function validarRif(){
	rif=document.form1.partida.value;
	var encuentra=0;
	for(j= 0; j<arreglo_id_partidas.length; j++){
		if(rif==arreglo_id_partidas[j]){
			return true;
			encuentra=1;
		}else{
			  
			  document.form1.partida.value='';
			  //document.form1.partida.focus();
			  alert("Esta partida no es v\u00E1lida");
			  document.form1.partida.focus();
			  return;
			}
	}
	
}

//FunciOn que permite verficar que la descripcion no se encuentre repetida
function verificar_descripcion()
{ 
   palabra=document.form1.descrip.value;
   <?php
   $sql_p="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','nombre','t1.id=t2.id','',2) resultado_set(nombre varchar)"; 
   $resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
   while($row=pg_fetch_array($resultado_set_most_p)) 
   {?>
    palabra1="<?php echo trim($row['arti_descripcion']); ?>"
	if (palabra.toUpperCase()==palabra1.toUpperCase())
    {
	 alert("Esta descripci\u00F3n ya existe en la base de datos...");
	 document.form1.descrip.value='';
	 return;
	}
    <?php
   }
  ?> 
}

//FunciOn que valida el llenado de todos los campos 
function revisar()
{
	
	if (document.form1.descrip.value=="")
	{
		alert("Debe especificar el art\u00EDculo")
		document.form1.descrip.select();
		document.form1.descrip.focus();
		return;
	}	

	if (document.form1.unidad.value==0)
	{
		alert("Debe seleccionar la unidad de medida")
		document.form1.unidad.focus();
		return;
	}

	if (document.form1.tipo_art.value==0)
	{
		alert("Debe seleccionar una clasificaci\u00F3n para el art\u00EDculo")
		document.form1.tipo_art.focus();
		return;
	}


	if(confirm("Estos datos ser\u00E1n registrados. \u00BFEst\u00E1 seguro que desea continuar?"))
	{
	  document.form1.submit()
	}
}	

/*FunciiOn que valida que solo se introduzcan digitos caracteres y numericos en el campo*/
function validar_digito(objeto)
{
	var checkOK = "ABCDEFGHIJKLMN\u00D1OPQRSTUVWXYZabcdefghijklmn\u00F1opqrstuvwxyz0123456789áéíóúÁÉÍÓÚ -_.,;'()";
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
			alert("Estos caracteres no est\u00E1n permitidos");
			break;
		}
	}
}

</script>
</head>
<body onload="descripcion_compras()">
<form name="form1" method="post" action="ingresarAccion.php">
 <table width="850" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
	<td height="15" colspan="4" valign="midden" class="normalNegroNegrita">Registrar </span></td>
    </tr>
    <tr>
      <td height="33"><div class="normalNegrita">Art&iacute;culo:</div></td>
      <td height="33" colspan="3" valign="midden">
	  <?php if($_REQUEST['descripcion']!='')  
	    {?>
	  <input name="descrip" value="<?php echo $_REQUEST['descripcion'];?>" type="text" class="normal" size="26" onblur="javascript:verificar_descripcion()" onchange="validar_digito(descrip)"/> <span class="peq_naranja">(*)</span></td>
	  <? } else {?>
	  <input name="descrip" type="text" class="normal" size="26" onChange="javascript:verificar_descripcion()" onchange="validar_digito(descrip)"/>
	  <? } ?> <span class="peq_naranja">(*)</span>
    </tr>
    <tr> 
      <td height="31" valign="midden"> <div class="normalNegrita">Unidad de medida:</div></td>
      <td height="31" colspan="3" valign="midden" class="normal">
	  
      <select name="unidad" class="normal" id="unidad">
	  <?php 
	  if(($_REQUEST['unidad_med']) != '') 
	  { ?> <option value="<?php echo $_REQUEST['unidad_med'];?>">[Seleccione]</option>
   <? } else {?> <option value="0">[Seleccione]</option> <? } ?>
	  <?php
	  $sql="SELECT * FROM sai_seleccionar_campo('sai_uni_medida','unme_id, unme_descrip','','',2) resultado_set(unme_id varchar,unme_descrip varchar)"; 
	  $resultado=pg_query($conexion,$sql) or die("Error al mostrar");
	  $actual=trim($_REQUEST['unidad_med']);
	  while($row=pg_fetch_array($resultado))
	    { 
   	     $id_unidad=trim($row['unme_id']);
	     $descrip_unidad=$row['unme_descrip'];
	    
	  ?>
	  <option value="<?=$id_unidad;?>" <?php if ($id_unidad== $actual) { echo "selected"; } ?>  ><?php echo"$descrip_unidad";?></option> 
     
	  <?php 
	  }
	  ?>
	  </select> <span class="peq_naranja">(*)</span></td>
    </tr>
    <tr>
	  <td class="normalNegrita">Estado:</td>
	  <td height="42" colspan="4" class="normalNegrita">
	  <input name="opt_estado" type="radio" value="1" checked />Activo
	  <input name="opt_estado" type="radio" value="2" />Inactivo
	  </td>
	  </tr>
    
    <tr>
      <td height="32" ><div class="normalNegrita">Clasificaci&oacute;n </div></td>
      <td height="32" colspan="3" valign="midden">
	 <select name="tipo_art" class="normal" id="tipo_art">
	   <option value="0">[Seleccione]</option>
	  <?php
	  $sql="SELECT * FROM sai_seleccionar_campo('sai_arti_tipo','tp_id,tp_desc','tp_id<>12 and id_tipo=1','tp_desc',1) resultado_set(tp_id varchar,tp_desc varchar)"; 
	  $resultado=pg_query($conexion,$sql) or die("Error al mostrar");
	  $actual=trim($_REQUEST['unidad_med']);
	  while($row=pg_fetch_array($resultado))
	    { 
   	     $id_tipo=trim($row['tp_id']);
	     $descrip_tipo=$row['tp_desc'];
	    
	  ?>
	  <option value="<?=$id_tipo;?>"><?php echo"$descrip_tipo";?></option> 
     
	  <?php 
	  }
	  ?>
	  </select> <span class="peq_naranja">(*)</span> </td>
    </tr>
     <tr>
      <td height="15" colspan="4"><br>
	  <div align="center">
	  <input class="normalNegro" type="button" value="Registrar" onclick="javascript:revisar();"/>
	  <br><br></div>	  </td>
    </tr>
</table>
</form>
</body>
</html>
<?php pg_close($conexion);?>
