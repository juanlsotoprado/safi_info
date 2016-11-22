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
?>
<?php $descripcion=trim($_REQUEST['descripcion']);?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI: INGRESAR BIEN NACIONAL</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
<script>
//Funci�n que permite verficar que la descripcion no se encuentre repetida
function verificar_descripcion()
{ 
   palabra=document.form1.txt_nombre.value;
   <?php
   $sql_p="SELECT * FROM sai_seleccionar_campo('sai_item','nombre','id_tipo=2','',2) resultado_set(nombre varchar)"; 
   $resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
   while($row=pg_fetch_array($resultado_set_most_p)) 
   {?>
    palabra1="<?php echo trim($row['nombre']); ?>"
	if (palabra.toUpperCase()==palabra1.toUpperCase())
        {
	 alert("Esta descripci\u00F3n ya existe en la base de datos...");
	 document.form1.descrip.value='';
	}
    <?php
   }
  ?> 
}
//Funci�n que valida el llenado de todos los campos 
function revisar()
{
    //Verificamos que el usaurio ingrese la descripci�n del articulo
	if (document.form1.txt_nombre.value=="")
	{
		alert("Debe especificar el nombre del activo")
		document.form1.txt_nombre.select();
		document.form1.txt_nombre.focus();
		return;
	}	

	if (document.form1.tipo_activo.value==0)
	{
		alert("Debe seleccionar una clasificaci\u00F3n para el activo.")
		document.form1.tipo_activo.focus();
		return;
	}
	
	//Verificamos que el usuario este seguro de la operaci�n
	if(confirm("Estos datos ser\u00E1n registrados. \u00BFEst\u00E1 seguro que desea continuar?"))
	{
	  document.form1.submit()
	}
}		
/*Funcii�n que valida que solo se introduzcan digitos caracteres y numericos en el campo*/
function validar_digito(objeto)
{
	var checkOK = "ABCDEFGHIJKLMN�OPQRSTUVWXYZ�����abcdefghijklmn�opqrstuvwxyz�����0123456789 -_.,;()";
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
</head><body>
<form name="form1" method="post" action="bien_e1.php">
<br />
<table width="680" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr  class="td_gray"> 
	<td height="15" colspan="2" valign="midden"  class="normalNegroNegrita"><strong>Registrar activos</strong></td>
    </tr>
    <tr>
      <td height="34" valign="midden" class="normalNegrita">Nombre:</td>
      <td height="34" valign="midden"><span class="peq_naranja">

        <input name="txt_nombre" type="text" class="normalNegro" id="txt_nombre" onblur="javascript:verificar_descripcion()" onchange="validar_digito(txt_nombre)" value="" size="30" maxlength="100" />(*)</span></td>
    </tr>
	<tr>
	  <td height="38" valign="midden" class="normalNegrita">Descripci&oacute;n:</td>
	  <td height="38" valign="midden"><span class="normal">
	    <textarea name="txt_bien_descripcion" class="normalNegro" rows="3" cols="32" onchange="validar_digito(txt_bien_descripcion)"></textarea>
	    </span></td>
    </tr>
        <tr>
      <td height="32" class="normalNegrita">Clasificaci&oacute;n </td>
      <td height="32" colspan="3" valign="midden">
	 <select name="tipo_activo" class="normalNegro" id="tipo_activo">
	   <option value="0">[Seleccione]</option>
	  <?php
	  $sql="SELECT * FROM bien_categoria order by nombre"; 
	  $resultado=pg_query($conexion,$sql) or die("Error al mostrar");
	  
	  while($row=pg_fetch_array($resultado))
	    { 
   	     $id_tipo=trim($row['id']);
	     $descrip_tipo=$row['nombre'];
	    
	  ?>
	  <option value="<?=$id_tipo;?>"><?php echo"$descrip_tipo";?></option> 
     
	  <?php 
	  }
	  ?>
	  </select> <span class="peq_naranja">(*)</span> </td>
    </tr>
	<tr>
      <td height="33" valign="midden" class="normalNegrita">Existencia m&iacute;nima:</td>
      <td height="33" valign="midden">
	  <input name="txt_exist_min" type="text" class="normalNegro" size="10" maxlength="6"  value=""  onkeypress="return acceptNum(event);" />
	  </td>
    </tr>
     <tr>
      <td height="33" valign="midden" class="normalNegrita">Vida &uacute;til(A&#241;os):</td>
      <td height="33" valign="midden">
	  <input name="txt_vida_util" type="text" class="normalNegro" size="10" maxlength="6"  value="" onkeypress="return acceptNum(event);" />
	  </td>
    </tr>
     <tr>
       <td height="15" colspan="2">&nbsp;</td>
     </tr>
     <tr>
      <td height="15" colspan="2"><br>
	  <div align="center">
	  <input type="button" value="Registrar" onclick="javascript:revisar()" class="normalNegro"></input>
	  
    </tr>
</table>
</form>
</body>
</html>
