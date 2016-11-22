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

<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
</head>

<body>

<?php

function completar_digitos($cadena,$longitud)
{
 for ($tamano= strlen($cadena); $tamano<$longitud; $tamano++)
 { 
  $cadena = "0".$cadena;
 }
 return $cadena;
}


require("../../../includes/conexion.php");
require("../../../includes/fechas.php");
require("../../../lib/fpdf/fpdf.php");
require("../../../lib/fpdf/fpdf_limpia.php");


/*     DATOS POST     */
$fecha_sw=true;
$fecha_i=$_POST['txt_fecha_i'];
$fecha_f=$_POST['txt_fecha_f'];
$rif_sw=$_POST['chk_rif'];

$archivo = fopen ("seniat.txt","w+");

list($dia,$mes,$an_o) = explode ("/",$fecha_i);

if ($dia<=15)
{
$quincena="1RA";
}else
{
$quincena="2DA";
}


/* Modifica el formato de la fecha */

$fecha_ia = cambia_fecha_iso($fecha_i);
$fecha_fa = cambia_fecha_iso($fecha_f);

$total_pagar = 0;
$sopg_bene_tp = 2;
$vacio = "";


 $query_rif = "select rif from sai_rif_fundacion";
 $res_rif = pg_exec($query_rif);
 if ($row_rif=pg_fetch_array($res_rif)){
 $rif_fundacion = $row_rif['rif'];
 }

$sopg_bene_nit = "";
$sopg_detalle = "CORRESPONDIENTE A LAS RETENCIONES ".$quincena." QUINCENA DEL MES DE ".convertir_mes_letras($mes);

/*Listado de solicitudes pagadas sin las retenciones*/
$query_pgch="SELECT * FROM sai_consultar_pago_retenciones('".$fecha_ia."','".$fecha_fa."','".$rif."') as (codigo_sopg varchar(20))";
//echo $query_pgch;
$res_pgch = pg_exec($query_pgch);
$contador = 0;
 while ($row_ret=pg_fetch_array($res_pgch))
{
 /*  Lista las ordenes de pago por los parametros obtenidos del formulario anterior */
 $query="SELECT * FROM sai_tesoreria_ordenes_pago_iva('".$row_ret['codigo_sopg']."','".$rif."','IVA') as (op_id varchar(20),op_fecha timestamp,op_monto float8, op_rif varchar(20), op_tp_ben smallint,op_monto_base float8, numero_reserva varchar(100))";
 $res_op = pg_exec($query);

$suma_exento="select sum(sopg_monto_exento) as suma_exento from sai_sol_pago_imputa where sopg_id='".$row_ret['codigo_sopg']."'";
$res_suma=pg_exec($suma_exento);
if ($row_suma=pg_fetch_array($res_suma)){
 $exento=$row_suma['suma_exento'];
}
    
 if ($row_op=pg_fetch_array($res_op))
 {
 	
  $query_iva = "SELECT * FROM sai_tesoreria_iva_opago('".$row_op['op_id']."','".trim($row_op['op_rif'])."',".$row_op['op_tp_ben'].",".$row_op['op_monto'].",'".$row_op['op_tipo']."',".$row_op['op_monto_base'].") as resultado";
 //echo $query_iva;
 $res_iva = pg_exec($query_iva);
  
    if($row_iva=pg_fetch_array($res_iva))
    {  
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
      
     	$string =  "Rif no valido = (V".$string.")";
      	
      }else if(strlen($string) == 8){
      	
      	$string =  "V0".$string;
      	
      }else{
      	
      $string =  "V".$string;
      	
      }
    	
     }

    
  $cad_iva_bene[1] =  $string;
    
     }
  
      /**  ***************** */  
    
     
     
     
		   $rif_prov = trim($cad_iva_bene[1]);
		   $fecha_fac = trim($cad_iva_bene[2]);
		   $num_fac = trim($cad_iva_bene[3]);
		   $num_control = trim($cad_iva_bene[4]);
                   $monto_facturado=number_format(trim($cad_iva_bene[5]),2,'.',',');
		   $monto_facturado=str_replace(',','',$monto_facturado);
		   $monto_base = number_format($cad_iva_bene[6],2,'.',',');
		   $monto_base=str_replace(',','',$monto_base);	


     /* Ahora, cuantas retenciones de IVA tiene esa orden de pago */
     $sql_det_iva = "SELECT * FROM sai_tesoreria_det_iva_opago('".trim($row_op['op_id'])."') as (sopa_id varchar(20),monto_iva float8,por_rete real,por_monto float8,por_imp real)";			
   
     $res_det_iva = pg_exec($sql_det_iva);
	if($row_det_iva=pg_fetch_array($res_det_iva)){ 
                   $documento = trim($row_det_iva['sopa_id']);
                   $porcentaje_imp = trim($row_det_iva['por_imp']);
		   $monto_iva      = trim($row_det_iva['monto_iva']);
		//   $porcentaje_ret = trim($row_det_iva['por_rete']);
		   $monto_rete     = number_format(trim($row_det_iva['por_monto']),2,'.',',');
		   $monto_rete=str_replace(',','',$monto_rete);	
	   	  	
	$query_exento = "SELECT sum(sopg_monto_exento) as exento FROM sai_sol_pago_imputa where sopg_id='".$documento."' ";
        $res_exento = pg_exec($query_exento);
	if($row_det_exen=pg_fetch_array($res_exento)){ 
                  $exento = number_format(trim($row_det_exen['exento']),2,'.',',');
		  $exento=str_replace(',','',$exento);	
        }
		$comprobante = $contador; 
  
$texto = $rif_fundacion."\t".$an_o.$mes."\t".$fecha_fac."\t"."C"."\t"."01"."\t".completar_digitos($rif_prov,10)."\t";
$texto = $texto.completar_digitos($num_fac,20)."\t".completar_digitos($num_control,20)."\t";
$texto = $texto.completar_digitos($monto_facturado,15)."\t".completar_digitos($monto_base,15)."\t";
$texto = $texto.completar_digitos($monto_rete,15)."\t"."0"."\t";
$texto = $texto.$an_o.$mes.completar_digitos($comprobante,8)."\t".completar_digitos($exento,15)."\t";
$texto = $texto.completar_digitos($porcentaje_imp,5)."\t".completar_digitos($expediente,15)."\t";

fwrite ($archivo,$texto);
fwrite ($archivo,"\n");

	} // end if
     } // end if
   } //end if
?>
<?  $contador = $contador +1;}// end while
fclose ($archivo);
?>
<br>
<div align="center"><span class="titular"> El disquete fue Generado Exitosamente </span></div>
<br>
      <table width="420" align="center" border="0">
	<tr>
	    <td colspan="15">
		   <div align="center"><a href="../../../procesos/contabilidad/retenciones/seniat.txt"><img src="../../../imagenes/vb.gif" width="32" height="32" border="0"></a>		   </div>		</td>
	   </tr>	
	<tr><TD align="center"><span class="normalNegroNegrita">CONSULTAR</span></TD></tr>	
	</table>
</body>
</html>
