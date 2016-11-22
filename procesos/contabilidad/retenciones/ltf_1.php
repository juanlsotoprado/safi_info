<?php
require("../../../includes/conexion.php");
require("../../../includes/fechas.php");
require("../../../lib/fpdf/fpdf.php");
require("../../../lib/fpdf/fpdf_limpia.php");


//Colocamos la imagen del ministerio del lado derecho
class PDF extends FPDF
{
    //Cabecera de p�gina
	function Header()
	{
		//Logo
		$this->SetX(45);
		$this->SetY(15);
		$this->Image('../../../imagenes/encabezado.jpg',15,14.5,260,12);
		//Arial bold 15
		$this->SetFont('Arial','B',12);
		//Movernos a la derecha
		$this->Cell(180);
		$this->SetXY(140,35);
		//T�tulo
		$this->Cell(10,15,utf8_decode("  "),0,0,'C');
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
$pdf->Cell(18,15,utf8_decode(" REPORTE DE RETENCIÓN DE LTF "),0,0,'C');
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
    $pdf->SetX(30);
  $pdf->Cell(60,$alto*4," BENEFICIARIO ",1,0,'C');
  
  $posx= $pdf->getx();
  $posy= $pdf->gety();
  
  $pdf->Cell(25,$alto*2," SOLICITUD ",0,2,'C');
  $pdf->Cell(25,$alto*2,utf8_decode(" DE PAGO Nº "),0,0,'C');
  
  $pdf->SetXY($posx,$posy);
  
  $pdf->Cell(25,$alto*4,"",1,0,'C');
  
  $posx= $pdf->getx();
  $posy= $pdf->gety();
  
  $pdf->Cell(40,$alto*2," R.I.F. ",1,2,'C');
  $pdf->Cell(20,$alto*2,"P.JURIDICA",1,0,'C');
  $pdf->Cell(20,$alto*2,"P.NATURAL",1,0,'C');
  
  $pdf->SetXY($posx,$posy);
  
  $pdf->Cell(40,$alto*4,"",0,0,'C');
    
  $posx= $pdf->getx();
  $posy= $pdf->gety();

  $pdf->Cell(18,$alto,"  Fuente ",0,2,'C');
  $pdf->Cell(18,$alto," Financiamiento ",0,0,'C');

  $pdf->SetXY($posx,$posy);
  
  $pdf->Cell(18,$alto*2,"",1,0,'C');

  $posx= $pdf->getx();
  $posy= $pdf->gety();

  $pdf->Cell(20,$alto*4," MES ",1,0,'C');
  
  $posx= $pdf->getx();
  $posy= $pdf->gety();
  
  $pdf->Cell(25,$alto," MONTO PAGADO",0,2,'C');
  $pdf->Cell(25,$alto," O ABONADO EN",0,2,'C');
  $pdf->Cell(25,$alto," CUENTA ",0,2,'C');
  $pdf->Cell(25,$alto," ",0,0,'C');
  
  $pdf->SetXY($posx,$posy);
  $pdf->Cell(25,$alto*4," ",1,0,'C');
  
  $posx= $pdf->getx();
  $posy= $pdf->gety();
  
  $pdf->Cell(25,$alto*2," BASE ",0,2,'C');
  $pdf->Cell(25,$alto*2," IMPONIBLE ",0,0,'C');
  
  $pdf->SetXY($posx,$posy);
  
  $pdf->Cell(25,$alto*4," ",1,0,'C');
  
  $posx= $pdf->getx();
  $posy= $pdf->gety();
  
  $pdf->Cell(10,$alto*2," % ",0,2,'C');
  $pdf->Cell(10,$alto*2," RET ",0,0,'C');
  
  $pdf->SetXY($posx,$posy);
  
  $pdf->Cell(10,$alto*4," ",1,0,'C');
  
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
$query_pgch="SELECT * FROM sai_consultar_pago_retenciones('".$fecha_ia."','".$fecha_fa."','".$rif."') as (codigo_sopg varchar(20))";

$res_pgch = pg_exec($query_pgch);
  while ($row_ret=pg_fetch_array($res_pgch)){

/*  Lista las ordenes de pago por los parametros obtenidos del formulario anterior */
$query="SELECT * FROM sai_tesoreria_ordenes_pago('".$row_ret['codigo_sopg']."','".$rif."','LTF') as (op_id varchar(20),op_fecha timestamp,op_monto float8, op_rif varchar(20), op_tp_ben smallint, numero_reserva varchar(100))";
$res_op = pg_exec($query);

 if ($row_op=pg_fetch_array($res_op)){  
  $query_ltf = "SELECT * FROM sai_tesoreria_ltf_opago('".$row_op['op_id']."','".trim($row_op['op_rif'])."',".$row_op['op_tp_ben'].",".$row_op['op_monto'].",'".$row_op['op_tipo']."') as resultado";
 // echo $query_ltf;
  $res_ltf = pg_exec($query_ltf);
    
  if($row_ltf=pg_fetch_array($res_ltf)){  
     
	 $cad_ltf = explode('*',$row_ltf['resultado']); 
     $pdf->SetX(30);
	 $pdf->Cell(60,$nvoalto,trim($cad_ltf[0]),1,0,'C');
	 $pdf->Cell(25,$nvoalto,trim($cad_ltf[1]),1,0,'C');
	 $pdf->Cell(20,$nvoalto,trim($cad_ltf[2]),1,0,'C');
     $pdf->Cell(20,$nvoalto,trim($cad_ltf[3]),1,0,'C');
     $pdf->Cell(18,$nvoalto,trim($row_op['numero_reserva']),1,0,'C');	 
	 $pdf->Cell(20,$nvoalto,trim($cad_ltf[4]),1,0,'C');
	 $pdf->Cell(25,$nvoalto,trim($cad_ltf[5]),1,0,'C');
     $pdf->Cell(25,$nvoalto,trim($cad_ltf[6]),1,0,'C');
	 $pdf->Cell(10,$nvoalto,trim($cad_ltf[7]),1,0,'C');
	 $pdf->Cell(20,$nvoalto,trim($cad_ltf[8]),1,1,'C');
     $total_pagar = $total_pagar + $cad_ltf[8];	
  }
}
}

	$pdf->SetX(30);
        $pdf->SetFont('Arial','B',6);
	$pdf->Cell(205,$nvoalto,"TOTAL:" ,1,0,'R');
   	$pdf->Cell(20,$nvoalto,$total_pagar,1,1,'C');

//**** Determinamos un nombre temporal para el pdf

CleanFiles(getcwd());


$archivo = basename(tempnam(getcwd(),'tmp'));
rename($archivo,$archivo.'.pdf');
$archivo.='.pdf';
//Guardar el pdf en un fichero
$pdf->Output($archivo);?>
<script>
document.write('Cargando archivo PDF, por favor espere ... ');
</script>
<?php 
//Redirecci�n con JavaScript a una ventana nueva por la session
echo("<HTML><SCRIPT>window.open('$archivo')</SCRIPT></HTML>");
?>