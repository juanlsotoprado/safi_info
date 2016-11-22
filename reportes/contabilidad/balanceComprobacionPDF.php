<?php
require_once(dirname(__FILE__) . '/../../init.php');
require_once (SAFI_LIB_PATH . '/general.php');
// Modelo
require_once(SAFI_MODELO_PATH. '/firma.php');


require("../../includes/reporteBasePdf.php");
require("../../lib/fpdf/fpdf.php");
require_once("../../includes/conexion.php");

if (isset($_POST["txt_inicio"])) {
	$fecha_inicio = $_POST["txt_inicio"];
	
class PDF extends FPDF {
    //Cabecera de pagina
	function Header() 	{
		////Logo
		//$this->Sety(30);
		//$this->Setx(0);
		//$this->Image('../../imagenes/encabezado.jpg',3,4,210,15);
		$this->Image('../../imagenes/encabezado.jpg',3,4,210,0);
		
		$this->SetFont('Arial','B',10);
		
		$this->Sety(20);
		$this->Cell(120);
		//Titulo
		$this->Setx(80);
		$this->Cell(30,10,utf8_decode("BALANCE DE COMPROBACIÓN AL ".$_POST["txt_inicio"]),27,27,'C');
		//Salto de linea
		$this->Ln(2);
		//$this->Sety(17);
		$this->SetFillColor(69,69,159);
    	$this->SetTextColor(255);
    	$this->SetDrawColor(128,0,0);
    	$this->SetLineWidth(.1);
    	$this->SetFont('Arial','B',8);
    	//Cabecera
    	$header=array('CUENTAS',utf8_decode('DESCRIPCIÓN'),utf8_decode('DÉBITO'),utf8_decode('CRÉDITO'));
    	$w=array(45,85,30,30);
    	for($i=0;$i<count($header);$i++){
    		$this->Cell($w[$i],5,$header[$i],1,0,'C',1);
    		//$this->Ln();
    	}
    	$this->Ln();
	}     
	function Footer(){  
		global $firmasSeleccionadas;
		$firmasSeleccionadas = array();

		$firmas = array();
		
		$firmas[0] = '46450';
		$firmas[1] = '65150';
		
		
		$firmasSeleccionadas = SafiModeloFirma::GetFirmaByPerfiles($firmas);		
				  	
		$this->SetY(-34);
		//Arial italic 8
		$this->SetFont('Arial','I',7);
		$this->Cell(0,10,utf8_decode('     '),0,0,'C');

		$this->SetY(-23);
		//Arial italic 8
		$this->SetFont('Arial','I',7);
		$this->Cell(0,6,utf8_decode('______________________________                             ______________________________'),0,0,'C');

		$this->SetY(-20);
		//Arial italic 8
		$this->SetFont('Arial','I',7);
		$this->Cell(0,6,utf8_decode('          '.$firmasSeleccionadas['46450']['nombre_empleado'].'                                                            '.utf8_encode($firmasSeleccionadas['65150']['nombre_empleado']).'      '),0,0,'C');

		$this->SetY(-18);
		//Arial italic 8
		$this->SetFont('Arial','I',7);
		$this->Cell(0,6,utf8_decode($firmasSeleccionadas['46450']['nombre_cargo'].'                                                                                '.utf8_encode($firmasSeleccionadas['65150']['nombre_cargo']).' '),0,0,'C');

		$this->SetY(-16);
		//Arial italic 8
		$this->SetFont('Arial','I',7);
		$this->Cell(0,6,$firmasSeleccionadas['46450']['nombre_dependencia'].'                                                                                     ',0,0,'C');

		$this->SetY(-14);
		//Arial italic 8
		$this->SetFont('Arial','I',7);
		$this->Cell(0,10,utf8_decode('                   SAFI-Fundación Infocentro').'  '.$this->PageNo().'/{nb}',0,0,'C');

		$this->SetY(-12);
		$this->Cell(0,10,'               '.fecha(),0,0,'C');
	}
	function PDF($orientation='P',$unit='mm',$format='A4')	{
    //Call parent constructor
    	$this->FPDF($orientation,$unit,$format);
	}
	
	function FancyTable($data) {
  
    	$this->SetDrawColor(128,0,0);
    	$this->SetLineWidth(.1);
  
	    //	Cabecera
    	$w=array(45,85,30,30);
   
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
    	
    				$this->Cell($w[0],$this->FontSize+0.75,'','',0,'R');
        
	            	$this->Cell($w[1],$this->FontSize+0.75,'','',0,'L');
            
    				$this->Cell(225,$this->FontSize+0.75,$nombreAnterior,'',0,'L');  

    				$this->Ln();
    			//}
    		
        		$this->Cell($w[0],$this->FontSize+0.75,$row[0],'',0,'C',$row[9]);
        
        		$this->Cell($w[1],$this->FontSize+0.75,$row[1],'',0,'L',$row[9]);
        
        		$this->Cell($w[2],$this->FontSize+0.75,$row[2],'',0,'R',$row[9]);
        
        		$this->Cell($w[3],$this->FontSize+0.75,$row[3],'',0,'R',$row[9]);
        
        		$imprimetitulo=1;
    		   
			$pagina=$paginaActual+1;
        	$this->Ln();
		}
    	$this->Cell(array_sum($w),0,'','T');
	}
}		

$pdf=new PDF('P','mm','Letter');
$pdf->SetAutoPageBreak(true,38);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',8);
//$pdf->Ln(); 




$ctaAnt="0"; 
$saldo_inicial=0;
 
	$fecha_inicio = $_POST["txt_inicio"];
	$dia = substr($fecha_inicio, 0, 2);
	$mes = substr($fecha_inicio, 3, 2)+1-1;
	$ano = substr($fecha_inicio, 6, 4);
	$ano_antes = $ano-1;
	$max_mes = 0;
	$max_mes_antes = 0;	
	$error="0";
	$consulta_basica=0;

	$sql_saldo= "select cpat_id, saldo from safi_saldo_contable where mes=".$mes." and ano=".$ano;

	$resultado=pg_query($conexion,$sql_saldo);
	$nro = 0;
	$nro = pg_num_rows($resultado);
	if ($nro<1) {
			$sql_saldo= "SELECT COALESCE(MAX(mes),0) AS mes FROM safi_saldo_contable WHERE ano=".$ano." AND mes<".$mes;
			$resultado=pg_query($conexion,$sql_saldo);
			$row=pg_fetch_array($resultado);	
			$nro = $row["mes"];
			if ($nro<1) {
				$sql_saldo= "select coalesce(max(mes),0)  as mes from safi_saldo_contable where ano=".$ano_antes;
				$resultado=pg_query($conexion,$sql_saldo);
				$row=pg_fetch_array($resultado);	
				$nro = $row["mes"];
				if ($nro<1) {
					$max_mes_antes=0;
					$error=1;
				} 	
				else {
					$max_mes_antes=$nro;
					$sql_saldo= "select cpat_id, saldo from safi_saldo_contable where mes=".$max_mes_antes." and ano=".$ano_antes;
				}
			}
			else {
				$max_mes=$row["mes"];
				$sql_saldo= "select cpat_id, saldo from safi_saldo_contable where mes=".$max_mes." and ano=".$ano;
			}
	}
	else {
		
		$max_mes = $mes;
		//if ($dia=="01") $consulta_basica=1;
	}

	if ($max_mes!=0) {
		if ($max_mes == 1 || $max_mes == 2 || $max_mes == 3 || $max_mes == 4 || $max_mes == 5 || $max_mes == 6 || $max_mes == 7 || $max_mes == 8 || $max_mes == 9) {
			$mes_x=$max_mes;
			$max_mes= "0".$max_mes;

		}
		$mes_total = $max_mes;
		$ano_total = $ano;  
	}
	else {
		if ($max_mes_antes!=0) {
			if ($max_mes_antes == 1 || $max_mes_antes == 2 || $max_mes_antes == 3 || $max_mes_antes == 4 || $max_mes_antes == 5 || $max_mes_antes == 6 || $max_mes_antes == 7 || $max_mes_antes == 8 || $max_mes_antes == 9) {
				$mes_x=$max_mes_antes;
				$max_mes_antes= "0".$max_mes_antes;
				
			}
			$mes_total = $max_mes_antes;
			$ano_total = $ano-1;  
		}
	}

if ($error!=1) {	
	$fechaIinicio = "01/".$mes_total."/".$ano_total;
	$fechaFfin = $fecha_inicio;

	
	if ($consulta_basica==1) {
	$sql_or="SELECT sc.cpat_id as cpat_id, sc.cpat_nombre as cpat_nombre, sc.cpat_nivel as cpat_nivel,
	case substring(trim(sc.cpat_id) from 1 for 1) when '1' then  scs.saldo when '6' then  scs.saldo else   case substring(trim(sc.cpat_id) from 1 for 1) when '4' then  scs.saldo else 0 end  end as debe,
	case substring(trim(sc.cpat_id) from 1 for 1) when '2' then  scs.saldo when '5' then  scs.saldo when '3' then  scs.saldo  else 0  end as haber  
	FROM sai_cue_pat sc, (".$sql_saldo.") scs 
	where sc.cpat_id = scs.cpat_id and substring(trim(sc.cpat_id) from 16 for 17) != '00'   order by sc.cpat_id ";
	}
	else {
		$login=$_SESSION['login'];
		require_once("saldoDiarioActualizado.php");	

		/*Búsqueda de movimientos en las fechas registradas*/
		$sql_or="SELECT sc.cpat_id as cpat_id, sc.cpat_nombre as cpat_nombre, sc.cpat_nivel as cpat_nivel,
		case substring(trim(sc.cpat_id) from 1 for 1) when '1' then  scs.saldo when '6' then  scs.saldo else   case substring(trim(sc.cpat_id) from 1 for 1) when '4' then  scs.saldo else 0 end  end as debe,
		case substring(trim(sc.cpat_id) from 1 for 1) when '2' then  scs.saldo when '5' then  scs.saldo when '3' then  scs.saldo  else 0  end as haber  
		FROM sai_cue_pat sc, (".$sql_total.") scs 
		where sc.cpat_id = scs.cpat_id and substring(trim(sc.cpat_id) from 16 for 17) != '00'   order by sc.cpat_id ";
	}
}	
$resultado_set_most_or=pg_query($conexion,$sql_or) or die ('Corra el reporte nuevamente y presione el boton pdf inmediatamente') ;

$cuentadata=0;
$totalDebe=0;
$totalHaber=0;
$debe=0;
$haber=0;
while($rowor=pg_fetch_array($resultado_set_most_or)) {
$saldo_inicial=0;	
	if(substr($rowor['debe'],0,1)=='-'){
    	$debe=0;
    	$haber=(-1)*$rowor['debe'];
    }
    else{
    	$debe=$rowor['debe'];
    	if(substr($rowor['haber'],0,1)=='-'){    			
    		$debe=(-1)*$rowor['haber'];
    		$haber=0;
    	}else{
    		$haber=$rowor['haber'];
    	}
    }
    //$saldo_inicial=$rowor['sal_inicial'];
    	
	$totalDebe=$totalDebe+$debe;
	// $totalDebe=$saldo_inicial;
	$totalHaber=$totalHaber+$haber;
	
	if(number_format($debe,2,',','.')=='0,00'&&number_format($haber,2,',','.')=='0,00'){
    	}else{
			$valore=$rowor['cpat_id'].'||'.strtolower((substr($rowor['cpat_nombre'],0,60))).'||'.number_format($debe,2,',','.').'||'.number_format($haber,2,',','.');
		
	$data[$cuentadata]= explode("||",$valore);    
	$cuentadata=$cuentadata+1;
	$ctaAnt= $rowor['cpat_id'];
    }
	}
	
	$valore=''.'||'.'Total'.'||'.number_format($totalDebe,2,',','.').'||'.number_format($totalHaber,2,',','.');
	$data[$cuentadata]= explode("||",$valore);    
	$cuentadata=$cuentadata+1;
	
$pdf->FancyTable($data);
$pdf->Output();
} else{
	echo "Error en la p&aacute;gina";
}
?>