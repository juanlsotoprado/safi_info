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
	function Header() 	{
		//Logo
		$this->Image('../../imagenes/encabezado.jpg',0,5,260,15);
		
		$this->SetFont('Arial','B',10);
		//Movernos a la derecha
		
		$this->Sety(28);
		$this->Cell(120);
		//Titulo
		$this->Setx(130);
		$this->Cell(22,7,utf8_decode("Plan de cuentas"),27,27,'C');
		//Salto de linea
		$this->Ln(2);
		 $this->SetFillColor(69,69,159);
    	$this->SetTextColor(255);
    	$this->SetDrawColor(128,0,0);
    	$this->SetLineWidth(.1);
    	$this->SetFont('Arial','B',6);
    //Cabecera
    $this->Setx(5);
    	$header=array(utf8_decode("Código"),"Nombre","Nivel","Grupo","SubGrupo","Rubro", "Cta",utf8_decode('S_Cta1°'),utf8_decode('S_Cta 2°'),utf8_decode('S_Cta3°'),"Mov");
    	$w=array(18,90,10,20,25,38,10,12,13,13,13,13);
    	for($i=0;$i<count($header);$i++)
    		$this->Cell($w[$i],5,$header[$i],1,0,'C',1);
    		$this->Ln();
	}     
	function Footer() 	{
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
     	$w=array(18,90,10,20,25,38,10,12,13,13,13,13);
   
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
    			    		
        		$this->Cell(html_entity_decode(htmlentities($w[0])),$this->FontSize+0.75,$row[0],'',0,'L');

        		$this->Cell($w[1],$this->FontSize+0.75,$row[1],'',0,'L');
        		
        		$this->Cell($w[2],$this->FontSize+0.75,$row[2],'',0,'C');
        
        		$this->Cell($w[3],$this->FontSize+0.75,$row[3],'',0,'C');
        
        		$this->Cell(html_entity_decode(htmlentities($w[4])),$this->FontSize+0.75,$row[4],'',0,'C');
		
        		$posy= $this->gety();

        		$this->Cell($w[5],$this->FontSize+0.75,$row[5],'',0,'C');
        		$this->Cell($w[6],$this->FontSize,$row[6],'',0,'C');

        		$this->Cell($w[7],$this->FontSize+0.75,$row[7],'',0,'C');
		
        		$this->Cell($w[8],$this->FontSize+0.75,$row[8],'',0,'C');
        		
        		$this->Cell($w[9],$this->FontSize+0.75,$row[9],'',0,'C');
        		$this->Cell($w[10],$this->FontSize+0.75,$row[10],'',0,'C');
        		
        	$this->Ln();

		}
    	$this->Cell(array_sum($w),0,'','T');
	}

}		

$pdf=new PDF('L','mm','Letter');

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',6);
$pdf->Ln(); 

$sql="select cpat_id, upper(cpat_nombre) as nombre, cpat_nivel as nivel, upper(cpat_grupo) as grupo, upper(cpat_sub_grupo) as subgrupo, upper(cpat_rubro) as rubro, 
CASE cpat_nivel 
           WHEN 4 THEN '*'
           END  as cuenta,
           CASE cpat_nivel 
           WHEN 5 THEN '*'
           END  as subcuenta1,
           CASE cpat_nivel 
           WHEN 6 THEN '*'
           END  as subcuenta2,
           CASE cpat_nivel 
           WHEN 7 THEN '*'
           END  as subcuenta3,
           CASE cpat_nivel 
           WHEN 7 THEN 'Si'
           ELSE 'No'
           END as movimiento
from sai_cue_pat 
order by cpat_id";

$resultado_set=pg_query($conexion,$sql) or die("Error al consultar las cuentas contables");
$contador=0;
while($row=pg_fetch_array($resultado_set))  {
$cpat_id=$row["cpat_id"];
$nombre=$row["nombre"];
$nivel=$row["nivel"];
$grupo=$row["grupo"];
$subgrupo=$row["subgrupo"];
$rubro=$row["rubro"];
$cuenta=$row["cuenta"];
$subcuenta1=$row["subcuenta1"];
$subcuenta2=$row["subcuenta2"];
$subcuenta3=$row["subcuenta3"];
$movimiento=$row["movimiento"];
$saldo=$row["saldo"];
$valor=$cpat_id.'||'.$nombre.'||'.$nivel.'||'.$grupo.'||'.$subgrupo.'||'.$rubro.'||'.$cuenta.'||'.$subcuenta1.'||'.$subcuenta2.'||'.$subcuenta3.'||'.$movimiento.'||';

$data[$contador]=explode("||",$valor);
$contador=$contador+1;
}

$pdf->FancyTable($data);
pg_close($conexion);
$pdf->Output();
?>