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
		$this->Cell(30,5,utf8_decode("  "),0,0,'C');
		//Salto de l�nea
		//$this->Ln(20);
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
$tipo_persona=$_POST['tp'];

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
		$pdf->Cell(18,5,utf8_decode(" REPORTE DE RETENCIÓN I.S.L.R "),0,0,'C');
$pdf->Ln();
$pdf->Cell(0,$alto," DESDE ".$fecha_i."  AL ".$fecha_f,0,0,'C');

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
  $pdf->SetX(15);
  $pdf->Cell(55,$alto*4," BENEFICIARIO ",1,0,'C');
  
  $posx= $pdf->getx();
  $posy= $pdf->gety();
  
  $pdf->Cell(20,$alto*2," SOLICITUD ",0,2,'C');
  $pdf->Cell(20,$alto*2,utf8_decode(" DE PAGO Nº "),0,0,'C');
  
  $pdf->SetXY($posx,$posy);
  
  $pdf->Cell(20,$alto*4,"",1,0,'C');
  
  $posx= $pdf->getx();
  $posy= $pdf->gety();
  
  $pdf->Cell(40,$alto*2," R.I.F. ",1,2,'C');
  $pdf->Cell(20,$alto*2,"P.JURIDICA",1,0,'C');
  $pdf->Cell(20,$alto*2,"P.NATURAL",1,0,'C');
  
  /*$pdf->SetXY($posx,$posy);
  
  $pdf->Cell(60,$alto*4,"",0,0,'C');*/
  $pdf->SetXY($posx,$posy);
  
  $pdf->Cell(40,$alto*4,"",0,0,'C');
    
  $posx= $pdf->getx();
  $posy= $pdf->gety();

  $pdf->Cell(18,$alto,"  Fuente ",0,2,'C');
  $pdf->Cell(18,$alto," Financiamiento ",0,0,'C');

  $pdf->SetXY($posx,$posy);
  
  $pdf->Cell(18,$alto*4,"",1,0,'C');

  $posx= $pdf->getx();
  $posy= $pdf->gety();
    
  $pdf->Cell(30,$alto*4," MES ",1,0,'C');
  
  $posx= $pdf->getx();
  $posy= $pdf->gety();
  
  $pdf->Cell(30,$alto," MONTO PAGADO ",0,2,'C');
  $pdf->Cell(30,$alto," O ABONADO EN",0,2,'C');
  $pdf->Cell(30,$alto," CUENTA ",0,2,'C');
  $pdf->Cell(30,$alto," ",0,0,'C');
  
  $pdf->SetXY($posx,$posy);
  $pdf->Cell(30,$alto*4," ",1,0,'C');
  
  $posx= $pdf->getx();
  $posy= $pdf->gety();
  
  $pdf->Cell(25,$alto*2," BASE ",0,2,'C');
  $pdf->Cell(25,$alto*2," IMPONIBLE ",0,0,'C');
  
  $pdf->SetXY($posx,$posy);
  
  $pdf->Cell(25,$alto*4," ",1,0,'C');
  
  $posx= $pdf->getx();
  $posy= $pdf->gety();
  
  $pdf->Cell(20,$alto*2," % ",0,2,'C');
  $pdf->Cell(20,$alto*2," RET ",0,0,'C');
  
  $pdf->SetXY($posx,$posy);
  
  $pdf->Cell(20,$alto*4," ",1,0,'C');
  
  $posx= $pdf->getx();
  $posy= $pdf->gety();
  
  $pdf->Cell(20,$alto*2," IMPUESTO ",0,2,'C');
  $pdf->Cell(20,$alto*2," RETENIDO ",0,0,'C');
  
  $pdf->SetXY($posx,$posy);
  
  $pdf->Cell(20,$alto*4," ",1,1,'C');
  
/*  Finaliza la tabla y salta a la linea siguiente  */

$nvoalto = $alto*2;
$pdf->SetFont('Arial','',6);
/****************************************************/
$total_pagar=0;
/*Listado de solicitudes pagadas sin las retenciones*/
$query_pgch="SELECT * FROM sai_consultar_pago_retenciones_islr('".$fecha_ia."','".$fecha_fa."','".$rif."','".$tipo_persona."') as (codigo_sopg varchar(20))";

$res_pgch = pg_exec($query_pgch);

  while ($row_ret=pg_fetch_array($res_pgch)){
 
/*  Lista las ordenes de pago por los parametros obtenidos del formulario anterior */
$query="SELECT * FROM sai_tesoreria_ordenes_pago('".$row_ret['codigo_sopg']."','".$rif."','ISLR') as (op_id varchar(20),op_fecha timestamp,op_monto float8, op_rif varchar(20), op_tp_ben smallint, numero_reserva varchar(100))";
$res_op = pg_exec($query);

  if ($row_op=pg_fetch_array($res_op)){
  $query_islr = "SELECT * FROM sai_tesoreria_islr_opago('".$row_op['op_id']."','".trim($row_op['op_rif'])."',".$row_op['op_tp_ben'].",".$row_op['op_monto'].",'".$row_op['op_tipo']."') as resultado";
 
  $res_islr = pg_exec($query_islr);
    
  if($row_islr=pg_fetch_array($res_islr)){  
     
	 $cad_islr = explode('*',$row_islr['resultado']); 
	 
	 
	 		    
  /** Acomodar RIF  */  
  
  
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
      
     	$string =  "Rif no valido = (V".$string.")";
      	      	
      }else if(strlen($string)== 8){
      	
      	$string =  "V0".$string;
      	
      }else{
      	
      $string =  "V".$string;
      	
      }
    	
     }

 
    
  $cad_islr[3] =  $string;
    
     }
  
      /**  ***************** */  
    
		
	 
  	 $pdf->SetX(15);
	 $pdf->Cell(55,$nvoalto,trim($cad_islr[0]),1,0,'C');
	 $pdf->Cell(20,$nvoalto,trim($cad_islr[1]),1,0,'C');
	 $pdf->Cell(20,$nvoalto,trim($cad_islr[2]),1,0,'C');
     $pdf->Cell(20,$nvoalto,trim($cad_islr[3]),1,0,'C');
	 $pdf->Cell(18,$nvoalto,trim($row_op['numero_reserva']),1,0,'C');	
   	 $pdf->Cell(30,$nvoalto,trim($cad_islr[5]),1,0,'C');
	 $pdf->Cell(30,$nvoalto,trim($cad_islr[8]),1,0,'C');
     $pdf->Cell(25,$nvoalto,trim($cad_islr[9]),1,0,'C');
	 $pdf->Cell(20,$nvoalto,trim($cad_islr[10]),1,0,'C');
	 $pdf->Cell(20,$nvoalto,trim($cad_islr[11]),1,1,'C');
  	 $total_pagar = $total_pagar + $cad_islr[11];
  }
}

}      
	$pdf->SetX(15);
        $pdf->SetFont('Arial','B',6);
	$pdf->Cell(245,$nvoalto,"TOTAL:" ,1,0,'R');
   	$pdf->Cell(20,$nvoalto,$total_pagar,1,1,'C');
/***************************************************/
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
//Redirecci�n con JavaScript a una ventana nueva por la session
echo("<HTML><SCRIPT>window.open('$archivo')</SCRIPT></HTML>");
?>