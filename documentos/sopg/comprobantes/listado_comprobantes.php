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

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<br>
<body>
<form name="form" method="post" action="">
<?
 
$tipo=$_REQUEST['tipo'];

if ($tipo=='IVA'){
$sql="SELECT * FROM sai_seleccionar_campo('sai_comprobante_iva','*','','compr_fecha',0)
resultado_set(compr_id varchar, compr_fecha timestamp, compr_docu_id varchar,prov_rif varchar, prov_nombre varchar,compr_fecha_causado date)";
$pagina="comprobante_iva_pdf.php"; 
}

if ($tipo=='LTF'){
$sql="SELECT * FROM sai_seleccionar_campo('sai_comprobante_ltf','*','','compr_fecha',0)
resultado_set(compr_id varchar, compr_fecha timestamp, compr_docu_id varchar,prov_rif varchar, prov_nombre varchar,compr_fecha_causado date)";
$pagina="comprobante_ltf_pdf.php"; 
}

if ($tipo=='ISLR'){
 $sql="SELECT * FROM sai_seleccionar_campo('sai_comprobante_islr','*','','compr_fecha',0)
resultado_set(compr_id varchar, compr_fecha timestamp, compr_docu_id varchar,prov_rif varchar, prov_nombre varchar,compr_fecha_causado date)";
 $pagina="comprobante_islr_pdf.php"; 
}


?>
  <table width="600" align="center" border="0" class="tablaalertas" background="../../../imagenes/fondo_tabla.gif">
  <tr><td colspan="7" class="normalNegrita" align="center">Listado de Comprobantes <?php echo $tipo;?></td></tr>
          <tr >
            <td class="td_gray"><div align="center" class="normalNegroNegrita">N&#176; Comprobante</div></td>
            <td class="td_gray"><div align="center" class="normalNegroNegrita">Fecha Causado</div></td>
            <td class="td_gray"><div align="center" class="normalNegroNegrita">N&#176; Sopg</div></td>
            <td class="td_gray"><div align="center" class="normalNegroNegrita">CI/RIF</div></td>
            <td class="td_gray"><div align="center" class="normalNegroNegrita">Nombre Beneficiario</div></td>
            <td class="td_gray"><div align="center" class="normalNegroNegrita">Fecha emisi&oacute;n del Comprobante </div></td>
	        <td class="td_gray"><div align="center" class="normalNegroNegrita">Opci&oacute;n </div></td>
          </tr>
	<?$resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
	while($row=pg_fetch_array($resultado_set_most_p))
	{?>
	   <tr>
            <td class="normal" align="center"><?=$row['compr_id']?></td>
            <td class="normal" align="center"><?=$row['compr_fecha_causado']?></td>
            <td class="normal" align="center"><?=$row['compr_docu_id']?></td>
            <td class="normal" align="center"><?=$row['prov_rif']?></td>
            <td class="normal" align="center"><?=$row['prov_nombre']?></td>
            <td class="normal" align="center"><?=$row['compr_fecha']?></td>
	        <td class="normal" align="center"><a href="eliminar_comprobante.php?tipo=<?=$tipo?>&id=<?=$row['compr_id'];?>">Eliminar</a><br>
	        <a href="<?=$pagina;?>?sopg=<?=$row['compr_docu_id'];?>&id=<?=$row['compr_id'];?>">Consultar</a></td>
          </tr>
<?}?>
  </table>
<br>
<div align="center" class="normal"><a href="index_comprobantes.php">Volver</a></div>
</form>
</body>
</html>
