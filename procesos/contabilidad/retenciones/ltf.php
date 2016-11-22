<?php
require("../../../includes/conexion.php");
require("../../../includes/fechas.php");
require("../../../lib/fpdf/fpdf.php");
require("../../../lib/fpdf/fpdf_limpia.php");


/*header("Pragma: ");
header('Cache-control: ');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: attachment; filename='ltf.csv'");*/

?>
<html>
<head><title>RETENCI&OACUTE;N L.T.F</title>
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style2 {color: #FFFFFF}
-->
</style>
</head>
<body>
<br>
<?
/*     DATOS POST     */
$fecha_sw=true;
$fecha_i=$_POST['txt_fecha_i'];
$fecha_f=$_POST['txt_fecha_f'];

$rif_sw=$_POST['chk_rif'];
$rif=strtoupper($_POST['txt_rif']);
$fecha_ia = cambia_fecha_iso($fecha_i);
$fecha_fa = cambia_fecha_iso($fecha_f);
?>

    <table width="900" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<!-- <tr><TD colspan="12" align="center" width="280"><IMG src="../../../imagenes/encabezado.jpg"></TD></tr> -->
 	<tr>
     	 <td height="14" colspan="12" bgcolor="#FFFFFF"><div align="center" class="normalNegroNegrita"><strong>REPORTE DE RETENCI&Oacute;N DEL L.T.F DESDE <?echo $fecha_i;?> AL <?echo $fecha_f;?><br></strong></div></td>
   	</tr>

	<tr class="td_gray">
     <td  class="normalNegroNegrita"><div align="center"><strong>Beneficiario</strong></div></td>
	 <td class="normalNegroNegrita"><div align="center"><strong>Orden de Pago</strong></div></td>
    
  <td class="normalNegroNegrita" width="15"><strong>P. JUR&Iacute;DICA</strong></td>
	       <td class="normalNegroNegrita" width="15"><strong>P. NATURAL</strong></td>  
	
	 <td  class="normalNegroNegrita"><div align="center"><strong>Fuente Financiamiento</strong></div></td>
     	 <td  class="normalNegroNegrita"><div align="center"><strong>Mes</strong></div></td>
     	 <td class="normalNegroNegrita"><div align="center"><strong>Monto Pagado o Abonado en Cuenta</strong></div></td>
         <td class="normalNegroNegrita"><div align="center"><strong>Base Imponible</strong></div></td>
     	 <td class="normalNegroNegrita"><div align="center"><strong>% Ret.</strong></div></td>
        <td class="normalNegroNegrita"><div align="center"><strong>Impuesto Retenido</strong></div></td>
	</tr>
<?
$total_pagar=0;
/*Listado de solicitudes pagadas sin las retenciones*/
$query_pgch="SELECT * FROM sai_consultar_pago_retenciones('".$fecha_ia."','".$fecha_fa."','".$rif."') as (codigo_sopg varchar(20))";
//echo $query_pgch."<br>";
$res_pgch = pg_exec($query_pgch);
  while ($row_ret=pg_fetch_array($res_pgch)){

/*  Lista las ordenes de pago por los parametros obtenidos del formulario anterior */
$query="SELECT * FROM sai_tesoreria_ordenes_pago('".$row_ret['codigo_sopg']."','".$rif."','LTF') as (op_id varchar(20),op_fecha timestamp,op_monto float8, op_rif varchar(20), op_tp_ben smallint, numero_reserva varchar(100))";
//echo $query."<br><br>";
$res_op = pg_exec($query);

 if ($row_op=pg_fetch_array($res_op)){  
  $query_ltf = "SELECT * FROM sai_tesoreria_ltf_opago('".$row_op['op_id']."','".trim($row_op['op_rif'])."',".$row_op['op_tp_ben'].",".$row_op['op_monto'].",'".$row_op['op_tipo']."') as resultado";
  //echo $query_ltf."<br><br>";
  $res_ltf = pg_exec($query_ltf);
    
  if($row_ltf=pg_fetch_array($res_ltf)){  
     
	 $cad_ltf = explode('*',$row_ltf['resultado']); ?>
         
   	<tr class="normalNegro">
   	 <td width="45" align="center"><?php echo $cad_ltf[0];?></td>
  	 <td><div align="center"><?php echo $cad_ltf[1];?></div></td>
     <td class="normalNegro"><?php echo $cad_ltf[2];?>&nbsp;</td>
     <td class="normalNegro"><?php echo $cad_ltf[3];?>&nbsp;</td>
	

	 <td width="145" align="center" ><?php echo $row_op['numero_reserva'];?></td>
     <td width="145" align="center" ><?php echo $cad_ltf[4];?></td>
     <td width="65" align="center" ><?php 
	 $monto=str_replace(",","",$cad_ltf[5]);
	 $monto=str_replace(".",",",$monto);
 	 echo $monto;
	 ?></td>
     <td width="65" align="center"  ><?php 
	 $monto=str_replace(",","",$cad_ltf[6]);
	 $monto=str_replace(".",",",$monto);
 	 echo $monto;
	 ?></td>
     <td width="90" align="center"  ><?php echo $cad_ltf[7];?></td>
	 <td width="145" align="center" ><?php 
	 $monto=str_replace(",","",$cad_ltf[8]);
	 $monto=str_replace(".",",",$monto);
 	 echo $monto;
	?></td>
	</tr>

 
	<?$total_pagar = $total_pagar + $cad_ltf[8];	
  }
}
}
?>
	<tr>
	 <td class="normalNegroNegrita" colspan="8" align="right"><b>TOTAL:</b></td>
	 <td class="normalNegroNegrita" align="center"><b><?echo $total_pagar;?></b></td>
	</tr>
</table>
</body>
</html>