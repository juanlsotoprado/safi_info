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

  $query="UPDATE sai_bien_garantia SET esta_id=15 where acta_id='".$_POST['id_acta']."'";
  $resultado=pg_query($conexion,$query);
  
  $memo_contenido=trim($_POST['motivo']);
	  if ($memo_contenido==""){
 	   $memo_contenido="No Especificado";
      }
      
	  $query_memo=utf8_decode("select * from sai_insert_memo('".$_SESSION['login']."','".$_SESSION['user_depe_id']."','".$memo_contenido."', 'Anulación Garantía','0', '0','0','',0, 0, '0', '','".$_POST['id_acta']."') as memo_id");
	  $resultado_set = pg_exec($conexion ,$query_memo);
	   
	  $valido=resultado_set;
	  if($resultado_set)
	  {
		$row = pg_fetch_array($resultado_set,0); 
		if ($row[0] <> null)
		{
		  $memo_id=$row[0];
		}
	  }
	// echo "El caso ". $_POST['id_acta']. " se ha anulado satisfactoriamente";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
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
	  <td><div align="left" class="normalNegro"><?echo $_POST['id_acta'];?></div></td>
    </tr>
    <tr>
      <td class="normalNegrita">Serial del activo:</td>
	  <td><div align="left" class="normalNegro"><?php echo $_POST['serial'];?></div></td>
    </tr>
    <tr>
	  <td class="normalNegrita">Motivo de la anulaci&oacute;n:</td>
      <td><div align="left" class="normalNegro"><?php echo $memo_contenido;?></div></td>
    </tr>
  </table><br><br>
  <div align="center" class="normalNegrita">El caso <?php echo $_POST['id_acta'];?> se ha anulado satisfactoriamente</div>
<?}
   if ($valido==false)	
    {?> <br><br><br>
   <table width="76%" border="0" align="center"  class="tablaalertas">
    <tr>
       <td height="18" colspan="3" class="normal"><div align="center">
       <img src="imagenes/mano_bad.gif" width="31" height="38">
		 <br><br>
       <img src="imagenes/vineta_azul.gif" width="11" height="7" />No se pudo anular el caso.
    	</tr>
    <tr><tr><TD height="10"></TD>
    </tr>
  </table>
<?php }?>

</form>
</body>
</html>
