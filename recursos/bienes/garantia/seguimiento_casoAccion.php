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

  $obs=trim($_POST['observacion_anterior'])."\n".$_POST['observacion'];
  $rev=trim($_POST['revision_anterior'])."\n".$_POST['revision'];
  
  
  $query="UPDATE sai_bien_garantia SET observaciones='".$obs."', datos_revision='".$rev."' where acta_id='".$_POST['id_acta']."'";
  $resultado=pg_query($conexion,$query);
  $query="SELECT t1.acta_id,nro_caso,serial,observaciones,datos_revision FROM sai_bien_garantia t1,sai_biin_items t2 
  WHERE t1.acta_id='".$_POST['id_acta']."' and t1.clave_bien=t2.clave_bien";
  $resultado=pg_query($conexion,$query);
	 
	if ($row=pg_fetch_array($resultado))
	{
		$codigo_acta=$row['acta_id'];
		$serial=$row['serial'];
		$ticket=$row['nro_caso'];
		$observaciones=$row['observaciones'];
		$reporte=$row['datos_revision'];
		$valido=true;
	}
	$hoy = date("Y-m-d H:i:s"); 
  $query="INSERT into sai_seguimiento_garantia (revi_doc,usua_login,revi_fecha,comentario,comentario_revision) VALUES ('".$row['acta_id']."','".$_SESSION['login']."','".$hoy."','".$_POST['observacion']."','".$_POST['revision']."')";
  $resultado=pg_query($conexion,$query);
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
	  <td><div align="left" class="normalNegro"><?echo $codigo_acta;?></div></td>
    </tr>
    <tr>
      <td class="normalNegrita">Serial del activo:</td>
	  <td><div align="left" class="normalNegro"><?php echo $serial;?></div></td>
    </tr>
    <tr>
      <td  height="15" class="normalNegrita">N&deg; de ticket o reporte:</td>
	  <td><div align="left" class="normalNegro"><?echo $ticket;?></div></td>
    </tr>
    <tr>
      <td class="normalNegrita">Observaciones: </td>
      <td height="20"><div align="left" class="normalNegro">
      
      <?php $sql="SELECT * FROM sai_seguimiento_garantia t1, sai_empleado t2, sai_bien_garantia t3
       WHERE revi_doc='".$codigo_acta."' and empl_cedula=t1.usua_login and revi_doc=acta_id and comentario<>''";
	    $resultado = pg_query($conexion,$sql) or die("Error al consultar las observaciones");
		    while ($row=pg_fetch_array($resultado)){
		     echo cambia_esp($row['revi_fecha']).":".substr($row['empl_nombres'],0,1).substr($row['empl_apellidos'],0,1)." ".$row['comentario']."<br><br>";
	    }
      ?><br><br></div></td>
    </tr>
    <tr>
	  <td class="normalNegrita">Reporte de la revisi&oacute;n:</td>
      <td><div align="left" class="normalNegro">
            <?php 
      $sql="SELECT * FROM sai_seguimiento_garantia t1, sai_empleado t2, sai_bien_garantia t3
       WHERE revi_doc='".$codigo_acta."' and empl_cedula=t1.usua_login and revi_doc=acta_id and comentario_revision<>''";
      $resultado = pg_query($conexion,$sql) or die("Error al consultar las observaciones");
		    while ($row=pg_fetch_array($resultado)){
		     echo cambia_esp($row['revi_fecha']).":".substr($row['empl_nombres'],0,1).substr($row['empl_apellidos'],0,1)." ".$row['comentario_revision']."<br><br>";
		    }
      ?>
      </div></td>
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
       <img src="imagenes/vineta_azul.gif" width="11" height="7" />No se pudo actualizar el caso.
    	</tr>
    <tr><tr><TD height="10"></TD>
    </tr>
  </table>
<?php }?>

</form>
</body>
</html>
