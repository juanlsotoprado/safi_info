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
<?php $descripcion=trim($_REQUEST['descripcion']);?>

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
<script>

function revisar()
{
	//Debe especificar la marca del bien
	if (trim(document.form1.tipo.value)=="")
	{
		alert("Debe escribir el nombre de la categoria a registrar.")
		document.form1.tipo.focus();
		return;
	}

        palabra=document.form1.tipo.value;
   <?$sql_p="SELECT * FROM sai_seleccionar_campo('sai_arti_tipo','tp_desc','','',2) resultado_set(tp_desc varchar)"; 
   $resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
   while($row=pg_fetch_array($resultado_set_most_p)) 
   {?>
    palabra1="<?php echo trim($row['tp_desc']); ?>"
	if (palabra.toUpperCase()==palabra1.toUpperCase())
    {
	 alert("Esta categor\u00EDa ya se encuentra registrada");
	 document.form1.descrip.value='';
	}
    <?php
   }
  	?>
	
	//Verificamos que el usuario este seguro de la operaci�n
	if(confirm("Estos datos ser\u00E1n registrados. Esta seguro que desea continuar?"))
	{
	  document.form1.submit()
	}
}		
/*Funcii�n que valida que solo se introduzcan digitos caracteres y numericos en el campo*/
function validar_digito(objeto)
{
	var checkOK = "ABCDEFGHIJKLMN�OPQRSTUVWXYZ�����abcdefghijklmn�opqrstuvwxyz�����0123456789 -_.,;()@";
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
<form name="form1" method="post" action="registrar_categoria_Accion.php">
<br />
<table width="466" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr  class="td_gray"> 
	<td height="15" colspan="2" valign="midden" class="normalNegroNegrita"><strong>Registrar categor&iacute;a de materiales</strong></td>
    </tr>
	<tr>
	  <td height="38" valign="midden"><div align="right"><span class="normalNegrita">Nombre: </span></div></td>
	  <td height="38" valign="midden"><span class="peq_naranja">
		<input name="tipo" type="text" class="normalNegro" id="tipo" onkeyup="validar_digito(this);">
	  </span><span class="peq_naranja">(*)</span></td>
    </tr>
     <tr>
      <td height="45" colspan="2"><br>
	  <div align="center">
	  <input type="button" onclick="javascript:revisar()" value="Registrar" class="normalNegro"></input>
	  
	  <br>
	  <br></div>	  </td>
    </tr>
</table>
</form>
</body>
</html>
