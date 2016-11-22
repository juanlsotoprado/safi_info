<?php
require("../../../includes/conexion.php");
require("../../../includes/fechas.php");
require("../../../lib/fpdf/fpdf.php");


/*header("Pragma: ");
header('Cache-control: ');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: attachment; filename='iva.csv'");*/

?>
<html>
<head><title>RETENCI&OACUTE;N DEL IVA</title>
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />

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
	<!--<tr><TD colspan="12" align="center" width="280"><IMG src="../../../imagenes/encabezado.jpg"></TD></tr>!-->
 	<tr >
     	 <td height="14" colspan="12" bgcolor="#FFFFFF"><div align="center" class="normalNegroNegrita"><strong>REPORTE DE RETENCI&Oacute;N DEL IVA DESDE <?echo $fecha_i;?> AL <?echo $fecha_f;?><br></strong></div></td>
   	</tr>
	<tr class="td_gray">
     	 <td  class="normalNegroNegrita"><div align="center"><strong>Orden de Pago</strong></div></td>
     	 <td  class="normalNegroNegrita"><div align="center"><strong>Beniciario</strong></div></td>
     	 <td  class="normalNegroNegrita"><div align="center"><strong>R.I.F.</strong></div></td>
          <td class="normalNegroNegrita"><div align="center"><strong>Fuente Financiamiento</strong></div></td>
     	 <td  class="normalNegroNegrita"><div align="center"><strong>Fecha Factura</strong></div></td>
     	 <td  class="normalNegroNegrita"><div align="center"><strong>N&uacute;mero Factura</strong></div></td>
     	 <td  class="normalNegroNegrita"><div align="center"><strong>Num. Control Factura </strong></div></td>
     	 <td  class="normalNegroNegrita"><div align="center"><strong>Monto Facturado</strong></div></td>
        <td  class="normalNegroNegrita"><div align="center"><strong>Base Imponible</strong></div></td>
	<td  class="normalNegroNegrita"><div align="center"><strong>% Alic.</strong></div></td>
     	 <td  class="normalNegroNegrita"><div align="center"><strong>Impuesto IVA </strong></div></td>
     	 <td  class="normalNegroNegrita"><div align="center"><strong>% Ret.</strong></div></td>
        <td  class="normalNegroNegrita"><div align="center"><strong>IVA Retenido</strong></div></td>
	</tr>
	<? 
$total_pagar=0;
/*Listado de solicitudes pagadas sin las retenciones*/
$query_pgch="SELECT * FROM sai_consultar_pago_retenciones('".$fecha_ia."','".$fecha_fa."','".$rif."') as (codigo_sopg varchar(20))";

$res_pgch = pg_exec($query_pgch);

while ($row_ret=pg_fetch_array($res_pgch)){

/*  Lista las ordenes de pago por los parametros obtenidos del formulario anterior */
$query="SELECT * FROM sai_tesoreria_ordenes_pago_iva('".$row_ret['codigo_sopg']."','".$rif."','IVA') as (op_id varchar(20),op_fecha timestamp,op_monto float8, op_rif varchar(20), op_tp_ben smallint, op_monto_base float8, numero_reserva varchar(100))";

$res_op = pg_exec($query);

$suma_exento="select sum(sopg_monto_exento) as suma_exento from sai_sol_pago_imputa where sopg_id='".$row_ret['codigo_sopg']."'";
$res_suma=pg_exec($suma_exento);
if ($row_suma=pg_fetch_array($res_suma)){
 $exento=$row_suma['suma_exento'];
}

 if ($row_op=pg_fetch_array($res_op)){
  $query_iva = "SELECT * FROM sai_tesoreria_iva_opago('".$row_op['op_id']."','".trim($row_op['op_rif'])."',".$row_op['op_tp_ben'].",".$row_op['op_monto'].",'".$row_op['op_tipo']."',".$row_op['op_monto_base'].") as resultado";
//echo $query_iva;
 $res_iva = pg_exec($query_iva);
  
  if($row_iva=pg_fetch_array($res_iva)){  
  
    $cad_iva_bene = explode('*',$row_iva['resultado']); 
   
    
  /** Acomodar RIF  */  
    
    if(isset($cad_iva_bene[1]) &&  $cad_iva_bene[1] != '  '){
    	
  	$string = str_replace("-","",$cad_iva_bene[1]);

    	$temp = $string[0];

    	if (ctype_alpha($temp) != 1) {

    		
    		$string =  "V".$string;
    		
    	}
   
    	
    
    if( strtoupper($string[0]) == 'V'){
    	
    	$string = substr($string,1);
    	
      if (strlen($string) <= 7){
      
     	$string =  "<b style='color:red'> Rif no valido = (V".$string.")</b>";
      	
      }else if(strlen($string)== 8){
      	
      	$string =  "V0".$string;
      	
      }else{
      	
      $string =  "V".$string;
      	
      }
    	
     }

 
    
  $cad_iva_bene[1] =  $string;
    
     }
  
      /**  ***************** */  
  /* Ahora, cuantas retenciones de IVA tiene esa orden de pago */
	 $sql_det_iva = "SELECT * FROM sai_tesoreria_det_iva_opago('".trim($row_op['op_id'])."') as (sopa_id varchar(20),monto_iva float8,por_rete real,por_monto float8,por_imp real)";			
//echo $sql_det_iva;	 
$res_det_iva = pg_exec($sql_det_iva);
	 
	if($row_det_iva=pg_fetch_array($res_det_iva)){?>
	 	   	   
	<tr class="normalNegro">
   	 <td width="45" align="center"  ><?php echo $row_op['op_id'];?></td>
  	 <td><div align="center" ><?php echo $cad_iva_bene[0];?></div></td>
 	 <td width="45" align="center"  ><?php echo $cad_iva_bene[1];?></td>
   	 <td width="45" align="center"  ><?php echo $row_op['numero_reserva'];?></td>
    	 <td width="75" align="center"  ><?php echo $cad_iva_bene[2];?></td>
    	 <td width="145" align="center" ><?php echo $cad_iva_bene[3];?></td>
         <td width="65" align="center"  ><?php echo $cad_iva_bene[4];?></td>
         <td width="65" align="center"  ><?php 
	 $monto=str_replace(",","",$cad_iva_bene[5]);
	 $monto=str_replace(".",",",$monto);
 	 echo $monto;
	 ?></td>
     	 <td width="90" align="center" ><?php
	 $monto=str_replace(",","",$cad_iva_bene[6]);
	 $monto=str_replace(".",",",$monto);
 	 echo $monto;
 	?></td>
	 <td width="145" align="center"  ><?php echo $row_det_iva['por_imp'];?></td>
         <td width="65" align="center"  ><?php
	 $monto=str_replace(",","",$row_det_iva['monto_iva']);
	 $monto=str_replace(".",",",$monto);
 	 echo $monto;
	 ?></td>
         <td width="65" align="center" ><?php echo $row_det_iva['por_rete'];?></td>
     	 <td width="90" align="center" ><?php 
	 $monto=str_replace(",","",$row_det_iva['por_monto']);
	 $monto=str_replace(".",",",$monto);
 	 echo $monto;
	 ?></td>
	</tr>
	<?	$total_pagar = $total_pagar + $row_det_iva['por_monto'];
	 } // end if
     }
   } //end if

}
	?>
	<tr>
	 <td class="normalNegroNegrita" colspan="12" align="right"><b>TOTAL:</b></td>
	 <td class="normalNegroNegrita" align="center"><b><?
	 $monto=str_replace(",","",$total_pagar);
	 $monto=str_replace(".",",",$monto);
 	 echo $monto;
?></b></td>
	</tr>
</table>

</body>


</html>

