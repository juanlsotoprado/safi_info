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

  $codigo=trim($_REQUEST['codigo']);
  $accion=$_REQUEST['accion'];
  
  
  $sql_salida="SELECT falla,nro_caso,sbn FROM sai_bien_garantia WHERE acta_id='".$codigo."' ";
  $resultado_salida=pg_query($conexion,$sql_salida) or die("Error al consultar información general del acta");
  if($rowd=pg_fetch_array($resultado_salida)) 
  { 
	$falla=$rowd['falla'];
    $caso=$rowd['nro_caso'];
    $sbn=$rowd['sbn'];
  }
  
  $sql_salida="SELECT orden,direccion,observaciones FROM sai_nota_salida WHERE acta_garantia='".$codigo."' ";
  $resultado_salida=pg_query($conexion,$sql_salida) or die("Error al consultar información general del acta");
  while($rowd=pg_fetch_array($resultado_salida)) 
  { 
	$orden=$rowd['orden'];
    $direccion=$rowd['direccion'];
    $observaciones=$rowd['observaciones'];
  }
  ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI:GENERAR NOTA DE SALIDA</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
<script>


//FunciOn que valida el llenado de todos los campos 
function revisar()
{
	if(document.form1.orden.value==""){
	  alert("Debe indicar el n\u00FAmero de orden ");
	  document.form1.orden.focus();
	  return;
	}

	if(document.form1.direccion.value==""){
		  alert("Debe indicar la direcci\u00F3n del servicio t\u00E9cnico");
		  document.form1.direccion.focus();
		  return;
	}
		
	if(confirm("Estos datos ser\u00E1n registrados. Est\u00E1 seguro que desea continuar?"))
	{
	 document.form1.submit()
	}
}	

/*FunciiOn que valida que solo se introduzcan digitos caracteres y numericos en el campo*/
function validar_digito(objeto)
{
	var checkOK = "ABCDEFGHIJKLMN\u00D1OPQRSTUVWXYZabcdefghijklmn\u00F1opqrstuvwxyz0123456789\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA -_.,;:()\n";
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
<body>
<?php if ($accion=="iniciar"){?>
<form name="form1" method="post" action="nota_salida.php?accion=guardar&codigo=<?=$codigo;?>">
 <table width="550" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
	<td height="15" colspan="4" valign="midden" class="normalNegroNegrita">Generar nota de salida </span></td>
    </tr>
     <tr>
      <td height="33"><div class="normalNegrita">N&deg; de acta:</div></td>
      <td height="33" valign="midden" class="normalNegroNegrita"><?php echo $_REQUEST['codigo'];?>
	  <input name="id_acta" value="<?php echo $_REQUEST['codigo'];?>" type="hidden" />
	  </td>
    </tr>
    <tr>
      <td height="33"><div class="normalNegrita">N&deg; de ticket o caso:</div></td>
      <td height="33" valign="midden" class="normalNegroNegrita"><?php echo $caso;?></td>
    </tr>
    <tr>
      <td height="33"><div class="normalNegrita">Serial del activo:</div></td>
      <td height="33" valign="midden" class="normalNegroNegrita"><?php echo $sbn;?></td>
    </tr>
    <tr>
      <td height="33"><div class="normalNegrita">Falla presentada:</div></td>
      <td height="33" valign="midden" class="normalNegroNegrita"><?php echo $falla;?></td>
    </tr>
    <tr>
      <td height="33"><div class="normalNegrita">N&deg; de orden:</div></td>
      <td height="33" valign="midden" class="normalNegroNegrita"><input type="text" name="orden" size="30" value="<?php echo $orden;?>"></input></td>
    </tr>
    <tr>
	  <td><div align="left" class="normal"><strong>Direcci&oacute;n: <strong></strong></td>
	  <td><textarea name="direccion" cols="50"><?php echo $direccion;?></textarea></div></td>
	</tr>
	<tr>
	  <td><div align="left" class="normal"><strong>Observaciones: <strong></strong></td>
	  <td><textarea name="observaciones" cols="50"><?php echo $observaciones;?></textarea></div></td>
	</tr>
  </table> <br>
      <div align="center">
	  <input class="normalNegro" type="button" value="Nota de salida" onclick="javascript:revisar();"/>
	  <br><br></div>	
</form>
<?php }else{

$sql="	Select * From sai_insert_nota_salida(
		    '".$_SESSION['login']."',		   
		    '".$codigo."',
		    '".$_SESSION['user_depe_id']."',
		    '".$_POST['orden']."',
		    '".$_POST['direccion']."',
		    '".$_POST['observaciones']."'
			 )";

//echo "<br>".$sql;
$resultado_set = pg_exec($conexion ,$sql) or die(utf8_decode("Error al ingresar la nota de salida"));
$row = pg_fetch_array($resultado_set,0); 
	
	if ($row[0] <> null)
	{
	  $codigo_acta=$row[0];
	}
	?> 
<div align="center" class="normal">El acta se gener&oacute; satisfactoriamente<br><br><a href="../garantia/actas/servicio_tecnico_pdf.php?codigo=<?php echo $codigo_acta;?>">Nota Salida T&eacute;cnico<img src="../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a>
</div>
<br> 
 <?php }?>

</body>
</html>
<?php pg_close($conexion);?>
