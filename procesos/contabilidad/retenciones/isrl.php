<?php
require("../../../includes/conexion.php");
require("../../../includes/fechas.php");
require("../../../lib/fpdf/fpdf.php");

/*
header("Pragma: ");
header('Cache-control: ');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: attachment; filename='islr.csv'");*/

?>
<html>
<head>
      <title>RETENCI&OACUTE;N I.S.L.R</title>
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
		<!--<tr><TD colspan="12" align="center" width="280"><IMG src="../../../imagenes/encabezado.jpg"></TD></tr>-->
 	<tr>
     	 <td height="14" colspan="12" bgcolor="#FFFFFF"><div align="center" class="normalNegroNegrita"><strong>REPORTE DE RETENCI&Oacute;N DEL I.S.L.R DESDE <?echo $fecha_i;?> AL <?echo $fecha_f;?><br></strong></div></td>
   	</tr>

	<tr class="td_gray">
     	 <td class="normalNegroNegrita"><div align="center"><strong>Beneficiario</strong></div></td>
	 <td  class="normalNegroNegrita"><div align="center"><strong>Orden de Pago</strong></div></td>
     	
	   <td class="normalNegroNegrita" width="50%"><strong>P. JUR&Iacute;DICA</strong></td>
	   <td class="normalNegroNegrita" width="50%"><strong>P. NATURAL</strong></td>
	  
	
         <td  class="normalNegroNegrita"><div align="center"><strong>Fuente Financiamiento</strong></div></td>
         <td  class="normalNegroNegrita"><div align="center"><strong>Fecha Ordenaci&oacute;n</strong></div></td>
     	 <td  class="normalNegroNegrita"><div align="center"><strong>Mes</strong></div></td>
     	 <td  class="normalNegroNegrita"><div align="center"><strong>Factura</strong></div></td>
     	 <td  class="normalNegroNegrita"><div align="center"><strong>Control</strong></div></td>
     	 <td  class="normalNegroNegrita"><div align="center"><strong>Monto Pagado o Abonado en Cuenta</strong></div></td>
         <td  class="normalNegroNegrita"><div align="center"><strong>Base Imponible</strong></div></td>
     	 <td  class="normalNegroNegrita"><div align="center"><strong>% Ret.</strong></div></td>
        <td  class="normalNegroNegrita"><div align="center"><strong>Impuesto Retenido</strong></div></td>
	</tr>
<?

$total_pagar=0;
/*Listado de solicitudes pagadas sin las retenciones*/
$query_pgch="SELECT * FROM sai_consultar_pago_retenciones_islr('".$fecha_ia."','".$fecha_fa."','".$rif."','".$tipo_persona."') as (codigo_sopg varchar(20))";
$res_pgch = pg_exec($query_pgch);
  while ($row_ret=pg_fetch_array($res_pgch)){

/*  Lista las ordenes de pago por los parametros obtenidos del formulario anterior */
$query="SELECT * FROM sai_tesoreria_ordenes_pago('".$row_ret['codigo_sopg']."','".$rif."','ISLR') as (op_id varchar(20),op_fecha timestamp,op_monto float8, op_rif varchar(20), op_tp_ben smallint, numero_reserva varchar(100))";
//echo $query."<br>";
$res_op = pg_exec($query);

  if ($row_op=pg_fetch_array($res_op)){
  	//Ultimo parametro no se usa en el procedimiento
  $query_islr = "SELECT * FROM sai_tesoreria_islr_opago('".$row_op['op_id']."','".trim($row_op['op_rif'])."',".$row_op['op_tp_ben'].",".$row_op['op_monto'].",'".$row_op['op_tipo']."') as resultado";
 // echo $query_islr."<br>";
  $res_islr = pg_exec($query_islr);
    
  if($row_islr=pg_fetch_array($res_islr)){  

	$cad_islr = explode('*',$row_islr['resultado']); 
	
  
  /** Acomodar RIF  */  
	
  
    if(isset( $cad_islr[3]) &&  $cad_islr[3] != '  '){
    	
    	
    	
  	$string = str_replace("-","",$cad_islr[3]);

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

 
    
  $cad_islr[3] =  $string;
    
     }
	
	?>

	<tr class="normalNegro">
   	 <td width="45" align="center" ><?php echo $cad_islr[0];?></td>
  	 <td><div align="center" ><?php echo $cad_islr[1];?></div></td>
	 <td  width="15" class="normalNegro"><?php echo $cad_islr[2];?>&nbsp;</td>
	 <td width="15" class="normalNegro"><?php echo $cad_islr[3];?>&nbsp;</td>
	     <td width="145" align="center"  ><?php echo $row_op['numero_reserva'];?></td>
    	 <td width="145" align="center" ><?php echo cambia_esp($cad_islr[4]);?></td>
    	 <td width="145" align="center"  ><?php echo $cad_islr[5];?></td>
    	 <td width="145" align="center"  ><?php echo $cad_islr[6];?></td>
    	 <td width="145" align="center"  ><?php echo $cad_islr[7];?></td>
         <td width="65" align="center"  ><?php 
	 $monto=str_replace(",","",$cad_islr[8]);
	 $monto=str_replace(".",",",$monto);
 	 echo $monto;
	 ?></td>
         <td width="65" align="center" ><?php 
	 $monto=str_replace(",","",$cad_islr[9]);
	 $monto=str_replace(".",",",$monto);
 	 echo $monto;
	 ?></td>
     	 <td width="90" align="center" ><?php echo $cad_islr[10];?></td>
	 <td width="145" align="center" ><?php 
	 $monto=str_replace(",","",$cad_islr[11]);
	 $monto=str_replace(".",",",$monto);
 	 echo $monto;
	?></td>
	</tr>
	<?	$total_pagar = $total_pagar + $cad_islr[11];
	 } // end if
     }
   } //end if


	?>
	<tr>
	 <td class="normalNegro" colspan="10" align="right"><b>TOTAL:</b></td>
	 <td class="normalNegro" align="center"><b><?
	 $monto=str_replace(",","",$total_pagar);
	 $monto=str_replace(".",",",$monto);
 	 echo $monto;
	 ?></b></td>
	</tr>
</table>
</body>
</html>

