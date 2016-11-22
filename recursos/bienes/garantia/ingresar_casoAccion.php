<?
ob_start();
require_once("../../../includes/conexion.php");
require_once("../../../includes/fechas.php");
	  
 if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
 {
   header('Location:../../../index.php',false);
   ob_end_flush(); 
   exit;
 }
	
  ob_end_flush(); 

  $fec=trim($_POST['fecha_notificacion']);
  
$sql="	Select * From sai_insert_garantia_bienes(
		   '".$_POST['fecha_notificacion']."',
		   '".$_POST['persona_reporta']."',
			'".$_SESSION['login']."',		   
		    '".$_POST['codigos']."',
		    '".$_POST['falla']."',
		    '".$_POST['persona_contacto']."',
		    '".$_POST['tlf_contacto']."',
		    '".$_POST['sbn']."',
		    '".$_POST['clave_bien']."',
			'".$_SESSION['user_depe_id']."'
			 )";

//echo "<br>".$sql;
$resultado_set = pg_exec($conexion ,$sql) or die(utf8_decode("Error al ingresar el reporte de garantía, enviar esta informacion a Sistemas: <br><br>".$sql));

	$row = pg_fetch_array($resultado_set,0); 
	
	if ($row[0] <> null)
	{
		$codigo_acta=$row[0];
		$valido=true;
		
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<script LANGUAGE="JavaScript" SRC="../../../includes/js/funciones.js"> </SCRIPT>
<script LANGUAGE="JavaScript" SRC="../../../includes/js/CalendarPopup.js"> </SCRIPT>
<script LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?if ($valido){?>
<form action="" name="form1" id="form1" method="post">
  <table width="550" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" id="sol_via">
    <tr>
      <td height="15" colspan="2" valign="midden" class="td_gray"> <span class="normalNegroNegrita">Registro de caso</span> </td>
    </tr>
    <tr>
      <td class="normalNegrita">N&deg; acta:</td>
	  <td><div align="left" class="normalNegro"><?echo $codigo_acta;?></div></td>
    </tr>
    <tr>
      <td class="normalNegrita">Serial Bien Nacional:</td>
	  <td><div align="left" class="normalNegro"><?echo $_POST['sbn'];?></div></td>
    </tr>
    <tr>
      <td class="normalNegrita">Serial del activo:</td>
	  <td><div align="left" class="normalNegro"><?php echo $_POST['serial'];?></div></td>
    </tr>
    
    <tr>
    <tr>
      <td class="normalNegrita"> Fecha de notificaci&oacute;n:</td>
      <td><div align="left" class="normalNegro"><?php echo $_POST['fecha_notificacion'];?></div></td>
    </tr>
    <tr>
      <td class="normalNegrita">Persona quien reporta: </td>
      <td><div align="left" class="normalNegro"><?php echo $_POST['persona_reporta'];?></div></td>
    </tr>
    <tr>
	  <td class="normalNegrita"> C&oacute;digos adicionales:</td>
      <td><div align="left" class="normalNegro"><?php echo $_POST['codigos'];?></div></td>
    </tr>
    <tr>	  
      <td class="normalNegrita"> Falla que presenta:</td>
      <td><div align="left" class="normalNegro"><?php echo $_POST['falla'];?></div></td>
    </tr>
    <tr>          
      <td class="normalNegrita"> Persona contacto:</td>
      <td><div align="left" class="normalNegro"><?php echo $_POST['persona_contacto'];?></div></td>
    </tr>
    <tr>          
	  <td class="normalNegrita"> Tel&eacute;fono persona contacto</td>
	  <td><div align="left" class="normalNegro"><?php echo $_POST['tlf_contacto'];?></div></td>
   </tr>

  </table>
<?}
   if ($valido==false)	
    {?> <br><br><br>
   <table width="76%" border="0" align="center"  class="tablaalertas">
    <tr>
       <td height="18" colspan="3" class="normal"><div align="center">
       <img src="imagenes/mano_bad.gif" width="31" height="38">
		 <br><br>
       <img src="imagenes/vineta_azul.gif" width="11" height="7" />No se puede efectuar el reporte del caso.
    	</tr>
    <tr><tr><TD height="10"></TD>
    </tr>
  </table>
<?php }?>

</form>
</body>
</html>
