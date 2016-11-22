<?php
require("../../includes/reporteBasePdf.php");
require("../../lib/fpdf/fpdf.php");


if (isset($_POST["hid_desde_itin"]) && isset($_POST["hid_hasta_itin"])) {
	$fecha_inicio = $_POST["hid_desde_itin"];
	
class PDF extends FPDF {
    //Cabecera de pagina
	function Header() 	{
		//Logo
		$this->Image('../../imagenes/encabezado.jpg',5,5,260,15);
		
		$this->SetFont('Arial','B',10);
		//Movernos a la derecha
		
		$this->Sety(28);
		$this->Cell(120);
		//Titulo
		$this->Cell(30,5,utf8_decode("MAYOR ANALÍTICO ENTRE ".$_POST["hid_desde_itin"]." Y ".$_POST["hid_hasta_itin"]),27,27,'C');
		//Salto de linea
		$this->Ln(2);
		$this->SetFillColor(69,69,159);
    	$this->SetTextColor(255);
    	$this->SetDrawColor(128,0,0);
    	$this->SetLineWidth(.1);
    	$this->SetFont('Arial','B',8);
    //Cabecera
    	$header=array('Fecha','Identif.','Docg.','Ref.','Cta Contable','S. Inicial','Debe','Haber','S. Final','Comentario');
    	$w=array(15,25,22,20,28,18,18,18,18,90);
    	for($i=0;$i<count($header);$i++)
    		$this->Cell($w[$i],5,$header[$i],1,0,'C',1);
    		$this->Ln();
	}     
	function Footer() {
		$this->SetY(-26);
	    //	Arial italic 8
    	$this->SetFont('Arial','I',7);
    	$this->Cell(0,10,utf8_decode('     '),0,0,'C');
		$this->SetY(-21);
	    //	Arial italic 8
    	$this->SetFont('Arial','I',7);
    	$this->Cell(0,10,utf8_decode('______________________________                       ______________________________'),0,0,'C');
		$this->SetY(-18);
	    //	Arial italic 8
    	$this->SetFont('Arial','I',7);
    	$this->Cell(0,10,utf8_decode('          Rudexy Riveros                                                      Ramón David Parra      '),0,0,'C');
		$this->SetY(-15);
	    //	Arial italic 8
    	$this->SetFont('Arial','I',7);
    	$this->Cell(0,10,utf8_decode('Director Oficina de Gestión                                                 Presidente            '),0,0,'C');
		$this->SetY(-12);
	    //	Arial italic 8  
    	$this->SetFont('Arial','I',7);
    	$this->Cell(0,10,utf8_decode('Administrativa y Financiera                                                                               '),0,0,'C');
		
    	$this->SetY(-15);
	    //	Arial italic 8
    	$this->SetFont('Arial','I',7);
    	$this->Cell(0,10,utf8_decode('SAI-Fundación Infocentro').'  '.$this->PageNo().'/{nb}',0,0,'R');
	    $this->SetY(-12);
	    $this->Cell(0,10,fecha(),0,0,'R');
	}
	
	function PDF($orientation='P',$unit='mm',$format='letter') {
    //Call parent constructor
    	$this->FPDF($orientation,$unit,$format);
	}
	
	function FancyTable($data) {
    	$this->SetDrawColor(128,0,0);
    	$this->SetLineWidth(.1);
  
	    //	Cabecera
    	$w=array(15,25,22,20,28,18,18,18,18,90);
        
	    //	Restauración de colores y fuentes
    	$this->SetFillColor(224,235,255);
    	$this->SetTextColor(0);
    	$this->SetFont('Arial','',8);
	    //	Datos
    	$xx=10;
    	$yy=30;
    	$pagina=2;
    	$imprimetitulo=0;
    
	    $nombreAnterior="";
	    foreach($data as $row) {
    		$this->setx($xx);
    		$paginaActual=$this->PageNo();
    	
    		if($row[3]=='' && $row[4]=='' && $row[5]=='' && $row[6]==''){
    			$this->Cell($w[0],$this->FontSize+0.75,'','',0,'R',$row[9]);
        
            	$this->Cell($w[1],$this->FontSize+0.75,'','',0,'L',$row[9]);
            
    			$this->Cell(225,$this->FontSize+0.75,$row[2],'',0,'L',$row[9]);
    		
    			$nombreAnterior=$row[2];
    			$imprimetitulo=0;
    		} else{
    			if($pagina==$paginaActual && $imprimetitulo==1){
    				$this->Cell($w[0],$this->FontSize+0.75,'','',0,'R');
        
	            	$this->Cell($w[1],$this->FontSize+0.75,'','',0,'L');
            
    				$this->Cell(225,$this->FontSize+0.75,$nombreAnterior,'',0,'L');  

    				$this->Ln();
    			}
    		
        		$this->Cell($w[0],$this->FontSize+0.75,$row[0],'',0,'R',$row[9]);
        
        		$this->Cell($w[1],$this->FontSize+0.75,$row[1],'',0,'L',$row[9]);
        
        		$this->Cell($w[2],$this->FontSize+0.75,$row[2],'',0,'L',$row[9]);
        
        		$this->Cell($w[3],$this->FontSize+0.75,$row[10],'',0,'R',$row[9]);
        
        		$this->Cell($w[4],$this->FontSize+0.75,$row[3],'',0,'R',$row[9]);
        
        		$this->Cell($w[5],$this->FontSize+0.75,$row[4],'',0,'R',$row[9]);
        
        		$this->Cell($w[6],$this->FontSize+0.75,$row[5],'',0,'R',$row[9]);
        
        		$this->Cell($w[7],$this->FontSize+0.75,$row[6],'',0,'R',$row[9]);
        		
        		$this->Cell($w[8],$this->FontSize+0.75,$row[7],'',0,'R',$row[9]);
        		
        		$this->Cell($w[10],$this->FontSize+0.75,$row[8],'',0,'L',$row[9]);
    			
        		$imprimetitulo=1;
    		   
    		}
			$pagina=$paginaActual+1;
        	$this->Ln();
		}
    	$this->Cell(array_sum($w),0,'','T');
	}
}		

$pdf=new PDF('L','mm','Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',8);
$pdf->Ln(); 

$fecha_inicio = $_POST["hid_desde_itin"];
$dia = substr($fecha_inicio, 0, 2);
$dia1 = $dia - 1;
$resto = substr($fecha_inicio, 2, 8);
$fecha_inicio_antes = date("d/m/Y",mktime(0,0,0,substr($fecha_inicio, 3, 2),(substr($fecha_inicio, 0, 2) - 1),substr($fecha_inicio, 6)));;
$fecha_fin = $_POST["hid_hasta_itin"];
$fechaIinicio = '30/06/2008';
$fechaFfin = $fecha_inicio_antes;

$login=$_POST["login"];
require_once("saldoDiarioActualizadoMayorAnalitico.php");

/*Búsqueda de movimientos en las fechas registradas*/
if (isset($_POST["cuenta"]) && strlen($_POST["cuenta"]) > 1){

$sql = "SELECT src.comp_id, src.reng_comp, sc.cpat_id, sc.cpat_nombre, src.rcomp_debe, src.rcomp_haber, src.rcomp_tot_db, src.rcomp_tot_hab, EXTRACT(DAY FROM comp_fec)||'/'||EXTRACT(month FROM comp_fec)||'/'||EXTRACT(Year FROM comp_fec) as fecha_emision, scd.comp_comen, scp.saldo,scd.comp_doc_id,scd.nro_referencia
FROM sai_cue_pat sc 
left outer join  sai_reng_comp src on (src.cpat_id = sc.cpat_id)
left outer join  sai_cue_pat_saldodiario_".$_SESSION['login']." scp on (scp.cpat_id = sc.cpat_id )
left outer join  sai_comp_diario scd on (scd.comp_id = src.comp_id)
where scd.esta_id<>'15' and sc.cpat_id = '".$_POST["cuenta"].trim()."' and substring(trim(sc.cpat_id) from 16 for 17) != '00' 
and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') >= to_date('".$_POST["hid_desde_itin"]."', 'DD MM YYYY') and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') <= to_date('".$_POST["hid_hasta_itin"]."', 'DD MM YYYY') order by sc.cpat_id, scd.comp_fec";
}
else{


$sql = "SELECT src.comp_id, src.reng_comp, sc.cpat_id, sc.cpat_nombre, src.rcomp_debe, src.rcomp_haber, src.rcomp_tot_db, src.rcomp_tot_hab, EXTRACT(DAY FROM comp_fec)||'/'||EXTRACT(month FROM comp_fec)||'/'||EXTRACT(Year FROM comp_fec) as fecha_emision, scd.comp_comen, scp.saldo,scd.comp_doc_id,scd.nro_referencia
FROM sai_cue_pat sc 
left outer join  sai_reng_comp src on (src.cpat_id = sc.cpat_id) 
left outer join  sai_cue_pat_saldodiario_".$_SESSION['login']." scp on (scp.cpat_id = sc.cpat_id )
left outer join  sai_comp_diario scd on (scd.comp_id = src.comp_id)
where scd.esta_id<>'15' and substring(trim(sc.cpat_id) from 16 for 17) != '00' 
and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') >= to_date('".$_POST["hid_desde_itin"]."', 'DD MM YYYY') and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') <= to_date('".$_POST["hid_hasta_itin"]."', 'DD MM YYYY')
order by sc.cpat_id, scd.comp_fec";
}
$ctaAnt="0";
$resultado_set_most_or=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");  
$titulopdf="MAYOR ANALÍTICO ENTRE ".$_POST["hid_desde_itin"]." Y ".$_POST["hid_hasta_itin"];

$cuenta_actual = ""; //Mantiene cada grupo de cuenta dentro del while
$cambio = true;
$saldo_calc_inicial = 0;
$saldo_final = 0;
	
while($rowor=pg_fetch_array($resultado_set_most_or)) {
	if ($cuenta_actual != $rowor['cpat_id']) {
		$cambio = true;
		$cuenta_actual = $rowor['cpat_id'];
	}

	if ($cambio) {
		$saldo_calc_inicial = $rowor['saldo'];
		$sesiondebe = $rowor['rcomp_debe'];
		$sesionhaber= $rowor['rcomp_haber'];
		if (substr($rowor['cpat_id'], 0, 1)==6 || substr($rowor['cpat_id'], 0, 1)==1  || substr($rowor['cpat_id'], 0, 1)==4) 
			$saldo_final =  $saldo_calc_inicial + $rowor['rcomp_debe'] - $rowor['rcomp_haber'];
		else
			$saldo_final =  $saldo_calc_inicial - $rowor['rcomp_debe'] + $rowor['rcomp_haber'];
			
	}	else {
		if (substr($rowor['cpat_id'], 0, 1)==6 || substr($rowor['cpat_id'], 0, 1)==1 || substr($rowor['cpat_id'], 0, 1)==4)   
			$saldo_calc_inicial = $saldo_calc_inicial + $sesiondebe - $sesionhaber;
		else
			$saldo_calc_inicial = $saldo_calc_inicial - $sesiondebe + $sesionhaber;
		$sesiondebe = $rowor['rcomp_debe'];
		$sesionhaber = $rowor['rcomp_haber'];
		if (substr($rowor['cpat_id'], 0, 1)==6 || substr($rowor['cpat_id'], 0, 1)==1 || substr($rowor['cpat_id'], 0, 1)==4) 
			$saldo_final =  $saldo_calc_inicial + $rowor['rcomp_debe'] - $rowor['rcomp_haber'];
		else
			$saldo_final =  $saldo_calc_inicial - $rowor['rcomp_debe'] + $rowor['rcomp_haber'];
		}

	$cambio = false;
	$posSaldoFinal=$saldo_final;
	if($posSaldoFinal=="") $posSaldoFinal=0;
	$posSaldoFinal=str_replace("-",'',$posSaldoFinal);
	$posHaber=$rowor['rcomp_haber'];
	if ($posHaber=="") $posHaber=0;
	$posHaber=str_replace("-",'',$posHaber);
	$posDebe=$rowor['rcomp_debe'];
	if($posDebe=="") $posDebe=0;
	$posDebe=str_replace("-",'',$posDebe);
	$posCalcInicial=$saldo_calc_inicial;
	if ($posCalcInicial=="") $posCalcInicial=0;
	$posCalcInicial=str_replace("-",'',$posCalcInicial);
	$sumatoria=$posCalcInicial+$posDebe+$posHaber+$posSaldoFinal;
	if(($sumatoria)!=0){
	
    if ($ctaAnt != $rowor['cpat_id']) {
				$fill=!$fill;
				$valore='||'.'||'.ucwords(strtolower($rowor['cpat_nombre'])).'||'.'||'.'||'.'||'.'||'.'||'.$fill;
				$data[$cuentadata]= explode("||",$valore);    
				$cuentadata=$cuentadata+1;
				$fill=!$fill;
	}	

	$saldoInicial=number_format($saldo_calc_inicial,2,',','.');
	if($saldoInicial=="0,00"){
		$saldoInicial="";
	}
	$saldoDebe=number_format($rowor['rcomp_debe'],2,',','.');
    if($saldoDebe=="0,00"){
		$saldoDebe="";
	}
	$saldoHaber=number_format($rowor['rcomp_haber'],2,',','.');
	if($saldoHaber=="0,00"){
		$saldoHaber="";
	}
	$saldoFinal=number_format($saldo_final,2,',','.');
	if($saldoFinal=="0,00"){
		$saldoFinal="";
	}
	$fechaEmision=$rowor['fecha_emision'];;
	if($fechaEmision==null|$fechaEmision==""){
		$fechaEmision=$_POST["hid_desde_itin"];
	}
	
	$sopgg=substr($rowor['comp_doc_id'], 0, 1).substr($rowor['comp_doc_id'], 5);
	if(substr($rowor['comp_id'], 0, 4)=='codi'){
		$sopgg=substr($rowor['comp_doc_id'], 0, 1).substr($rowor['comp_doc_id'], 5);
	}
		$valore=$fechaEmision.'||'.$rowor['comp_id'].'||'.$sopgg.'||'.$rowor['cpat_id'].'||'.$saldoInicial.'||'.$saldoDebe.'||'.$saldoHaber.'||'.$saldoFinal.'||'.ucwords(strtolower(substr($rowor['comp_comen'], 0, 60))).'||'.$fill.'||'.$rowor['nro_referencia'];

	$data[$cuentadata]= explode("||",$valore);   
	$valore=""; 
	
	$cuentadata=$cuentadata+1;

	$ctaAnt= $rowor['cpat_id'];
	}
    }


$pdf->FancyTable($data);
$pdf->Output();
}else{
	echo "Error en la p&aacute;gina";
}
?>