<?php 
require("../../lib/fpdf/fpdf.php");
require("../../includes/reporteBasePdf.php"); 
require_once("../../includes/conexion.php");
 //Login del usuario
$usuario = $_POST['usuario'];
//Perfil del usuario
$user_perfil_id = $_POST['user_perfil_id'];


class PDF extends FPDF {
    //Cabecera de pagina
	function Header() {
		//Logo
		$this->Image('../../imagenes/encabezado.jpg',0,5,260,15);
		
		$this->SetFont('Arial','B',10);
		//Movernos a la derecha
		
		$this->Sety(28);
		$this->Cell(120);
		//Titulo
		$this->Setx(10);
		$this->Cell(10,7,utf8_decode("Convertidor SAFI"),27,27,'L');
		//Salto de linea
		$this->Ln(2);
		 $this->SetFillColor(69,69,159);
    	$this->SetTextColor(255);
    	$this->SetDrawColor(128,0,0);
    	$this->SetLineWidth(.1);
    	$this->SetFont('Arial','B',12);
    //Cabecera
    $this->Setx(5);
    	$header=array("Partida","Cuenta contable","Nombre Cuenta Contable","Cuenta pasivo");
    	$w=array(30,40,155,40);
    	for($i=0;$i<count($header);$i++)
    		$this->Cell($w[$i],5,$header[$i],1,0,'C',1);
    		$this->Ln();
	}     
	function Footer() {
    	$this->SetY(-15);
	    //	Arial italic 8
    	$this->SetFont('Arial','I',6);
    	$this->Cell(0,10,utf8_decode('SAFI-Fundación Infocentro'),0,0,'C');
	    //	Número de página
	    $this->Cell(0,10,$this->PageNo().'/{nb}',0,0,'R');
	    
	    $this->SetY(-12);
	    $this->Cell(0,10,fecha(),0,0,'C');
	}
	
	function PDF($orientation='L',$unit='mm',$format='Letter') 	{
    //Call parent constructor
    	$this->FPDF($orientation,$unit,$format);
	}
	
	function FancyTable($datas) {
  
    	$this->SetDrawColor(128,0,0);
    	$this->SetLineWidth(.1);
  
	    //	Cabecera
    	$w=array(30,40,155,40);
   
    	$this->SetTextColor(0);
    	$this->SetFont('Arial');
	    //	Datos
    	$xx=0;
    	$yy=5;
    	$yy2=5;
    	$pagina=2;
    	
    $this->setx($xx);
	    $nombreAnterior="";
	    foreach($datas as $row) {
    		$this->Setx(5);
    			    		
        		$this->Cell(html_entity_decode(htmlentities($w[0])),$this->FontSize+0.75,$row[0],'',0,'C');

        		$this->Cell($w[1],$this->FontSize+0.75,$row[1],'',0,'C');
        		
        		$this->Cell($w[2],$this->FontSize+0.75,$row[2],'',0,'L');
        		
        		$this->Cell($w[3],$this->FontSize+0.75,$row[3],'',0,'C');
        	$this->Ln();

		}
    	$this->Cell(array_sum($w),0,'','T');
	}

}		

$pdf=new PDF('L','mm','Letter');

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',12);
$pdf->Ln(); 



$sql="select part_id, t1.cpat_id, upper(cpat_nombre) as cpat_nombre, cpat_pasivo_id
from sai_convertidor t1,sai_cue_pat t2 where t1.cpat_id=t2.cpat_id
order by part_id";

$resultado_set=pg_query($conexion,$sql) or die("Error al consultar el convertidor");
$contador=0;
while($row=pg_fetch_array($resultado_set))  {
$part_id=$row["part_id"];
$cpat_id=$row["cpat_id"];//.": 
$cpat_nombre=$row["cpat_nombre"];
$cpat_pasivo_id=$row["cpat_pasivo_id"];
$valor=$part_id.'||'.$cpat_id.'||'.$cpat_nombre.'||'.$cpat_pasivo_id.'||';

$data[$contador]=explode("||",$valor);
$contador=$contador+1;
}

$pdf->FancyTable($data);
pg_close($conexion);
$pdf->Output();
?>