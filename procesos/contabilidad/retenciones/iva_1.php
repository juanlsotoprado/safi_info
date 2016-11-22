<?php
require("../../../includes/conexion.php");
require("../../../includes/fechas.php");
require("../../../lib/fpdf/fpdf.php");


//Colocamos la imagen del ministerio del lado derecho
class PDF extends FPDF
{
    //Cabecera de p�gina
	function Header()
	{
		//Logo
		$this->SetX(35);
		$this->SetY(15);
		$this->Image('../../../imagenes/encabezado.jpg',3.5,14.5,280,12);
		//Arial bold 15
		$this->SetFont('Arial','B',12);
		//Movernos a la derecha
		$this->Cell(180);
		$this->SetXY(140,35);
		//T�tulo
		$this->Cell(30,5,utf8_decode(" "),0,0,'C');
		//Salto de l�nea
		//$this->Ln(5);
	}     
}		

$pdf=new PDF('L','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',8);
$pdf->Ln(); 

/*************   REPORTE    ******************/
$alto=4;

/*     DATOS POST     */
$fecha_sw=true;
$fecha_i=$_POST['txt_fecha_i'];
$fecha_f=$_POST['txt_fecha_f'];

$rif_sw=$_POST['chk_rif'];
$rif=strtoupper($_POST['txt_rif']);

/*   PARTE SUPERIOR   */
/*    PRIMERA FILA    */


if($fecha_sw==true){
		$pdf->SetFont('Arial','B',12);
		//Movernos a la derecha
		$pdf->Cell(180);
		$pdf->SetXY(140,35);
		//T�tulo
		$pdf->Cell(17,5,utf8_decode(" REPORTE DE RETENCIÓN DE IVA "),0,0,'C');
$pdf->Ln();
$pdf->Cell(0,$alto," DESDE ".$fecha_i."  AL ".$fecha_f,0,0,'C');
//
//$pdf->Cell(0,$alto,"  AL ".$fecha_f,0,0,'C');
$pdf->Ln();
}

$pdf->Ln();
$pdf->Ln();

/* Modifica el formato de la fecha */

$fecha_ia = cambia_fecha_iso($fecha_i);
$fecha_fa = cambia_fecha_iso($fecha_f);

$pdf->SetFont('Arial','B',6);

  /* Fila dode se coloca la descripcion de la tabla */
  $pdf->SetX(20);
  $pdf->Cell(18,$alto*2," Orden de Pago ",1,0,'C');
  $pdf->Cell(55,$alto*2," Beneficiario ",1,0,'C');
  $pdf->Cell(14,$alto*2," R.I.F ",1,0,'C');
 
  $posx= $pdf->getx();
  $posy= $pdf->gety();

  $pdf->Cell(18,$alto,"  Fuente ",0,2,'C');
  $pdf->Cell(18,$alto," Financiamiento ",0,0,'C');

  $pdf->SetXY($posx,$posy);
  
  $pdf->Cell(18,$alto*2,"",1,0,'C');

  $posx= $pdf->getx();
  $posy= $pdf->gety();
  
  $pdf->Cell(14,$alto,"  Fecha ",0,2,'C');
  $pdf->Cell(14,$alto," Factura ",0,0,'C');
  
  $pdf->SetXY($posx,$posy);
  
  $pdf->Cell(14,$alto*2,"",1,0,'C');
  
  $posx= $pdf->getx();
  $posy= $pdf->gety();  
  
  $pdf->Cell(20,$alto,utf8_decode("  Número "),0,2,'C');
  $pdf->Cell(20,$alto," Factura ",0,0,'C');
   
  $pdf->SetXY($posx,$posy);
  
  $pdf->Cell(20,$alto*2,"",1,0,'C');
    
  $posx= $pdf->getx();
  $posy= $pdf->gety();
  
  $pdf->Cell(18,$alto,"  Num. Control ",0,2,'C');
  $pdf->Cell(18,$alto," de la Factura ",0,0,'C');
  
  $pdf->SetXY($posx,$posy);
  
  $pdf->Cell(18,$alto*2,"",1,0,'C');
  
  $pdf->Cell(20,$alto*2," Monto Facturado ",1,0,'C');
  $pdf->Cell(18,$alto*2," Base imponible ",1,0,'C');
  $pdf->Cell(10,$alto*2,utf8_decode(" % Alíc. "),1,0,'C');
  $pdf->Cell(15,$alto*2," Impuesto IVA ",1,0,'C');
  $pdf->Cell(10,$alto*2," % Ret. ",1,0,'C');
  $pdf->Cell(15,$alto*2," IVA Retenido ",1,1,'C');

/*  Finaliza la tabla y salta a la linea siguiente  */
//$nvoalto = $alto*2;
$nvoalto = 6;
$pdf->SetFont('Arial','',6);

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
 $res_iva = pg_exec($query_iva);
  
  if($row_iva=pg_fetch_array($res_iva)){  
  
    $cad_iva_bene = explode('*',$row_iva['resultado']); 
    
  /* Ahora, cuantas retenciones de IVA tiene esa orden de pago */
	 $sql_det_iva = "SELECT * FROM sai_tesoreria_det_iva_opago('".trim($row_op['op_id'])."') as (sopa_id varchar(20),monto_iva float8,por_rete real,por_monto float8,por_imp real)";			
	 $res_det_iva = pg_exec($sql_det_iva);
	 
	if($row_det_iva=pg_fetch_array($res_det_iva)){
		
		    
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
    
		
	 	   	   
	 	   /* Los primeros datos se mantienen */
		   /* Codigo de la orden de pago */ 
		   $pdf->SetX(20);
		   $pdf->Cell(18,$nvoalto,trim($row_op['op_id']),1,0,'C');
		   $pdf->Cell(55,$nvoalto,trim($cad_iva_bene[0]),1,0,'C');
		   $pdf->Cell(14,$nvoalto,trim($cad_iva_bene[1]),1,0,'C');
		   $pdf->Cell(18,$nvoalto,trim($row_op['numero_reserva']),1,0,'C');	           
		   $pdf->Cell(14,$nvoalto,trim($cad_iva_bene[2]),1,0,'C');
                   $pdf->Cell(20,$nvoalto,trim($cad_iva_bene[3]),1,0,'C');
		   $pdf->Cell(18,$nvoalto,trim($cad_iva_bene[4]),1,0,'C');
		   $pdf->Cell(20,$nvoalto,trim($cad_iva_bene[5]),1,0,'C');
		   $pdf->Cell(18,$nvoalto,trim($cad_iva_bene[6]),1,0,'C');
		   /* Detalles del IVA  (Varian)  */
		   $pdf->Cell(10,$nvoalto,trim($row_det_iva['por_imp']),1,0,'C');
		   $pdf->Cell(15,$nvoalto,trim($row_det_iva['monto_iva']),1,0,'C');
		   $pdf->Cell(10,$nvoalto,trim($row_det_iva['por_rete']),1,0,'C');
		   $pdf->Cell(15,$nvoalto,trim($row_det_iva['por_monto']),1,1,'C');
	   	   $total_pagar = $total_pagar + $row_det_iva['por_monto'];	
	 } // end if
     }
   } //end if

}

	$pdf->SetX(20);
        $pdf->SetFont('Arial','B',6);
	$pdf->Cell(230,$nvoalto,"TOTAL:" ,1,0,'R');
   	$pdf->Cell(15,$nvoalto,$total_pagar,1,1,'C');

//**** Determinamos un nombre temporal para el pdf

$archivo = basename(tempnam(getcwd(),'tmp'));
rename($archivo,$archivo.'.pdf');
$archivo.='.pdf';
//Guardar el pdf en un fichero
$pdf->Output($archivo);
?>
<script>
document.write('Cargando archivo PDF, por favor espere ... ');
</script>
<?php 
//Redirecci�n con JavaScript
echo("<HTML><SCRIPT>window.open('$archivo')</SCRIPT></HTML>");
?>