<?php
require_once(dirname(__FILE__) . '/../../../init.php');
require_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
require_once(SAFI_LIB_PATH.'/fpdf/fpdf.php');
require_once(SAFI_INCLUDE_PATH.'/monto_a_letra.php');

class PDF extends FPDF { //Definir el encabezado y pie de pagina del cheque
  
	function Header() { 
		global $sopg; 
		global $pgch;		
		global $monto_cheque;
		global $fecha_emision;
		global $nro_cuenta_bancaria;
		global $nombre_banco;
		global $asunto;
	    global $observaciones;
		global $monto_cheque_float;
		global $beneficiario_cheque;
		global $monto_letras;
		global $fecha_letras;	
		global $nombre_cuenta_pasivo;
	}
	
	function Imprime(){
		global $sopg; 
		global $pgch;		
		global $monto_cheque;
		global $nro_cheque;
		global $fecha_emision;
		global $nro_cuenta_bancaria;
		global $nombre_banco;
		global $asunto;
	    global $observaciones;
		global $monto_cheque_float;
		global $beneficiario_cheque;
		global $monto_letras;
		global $fecha_letras;	
		global $nombre_cuenta_pasivo;
		global $listaCheque;

		//Fecha del cheque
		$dia = date("d");
		$mes = date("n");
		$anno = date("Y");
		$fecha_numero = $dia.'/'.$mes.'/'.$anno;
		/*$dia = '30';
		$mes = '12';
		$anno = '2014';
		$fecha_numero = '30/12/2014';*/
		if ($mes==1) $mes= 'enero';
		if ($mes==2) $mes= 'febrero';
		if ($mes==3) $mes= 'marzo';
		if ($mes==4) $mes= 'abril';
		if ($mes==5) $mes= 'mayo';
		if ($mes==6) $mes= 'junio';
		if ($mes==7) $mes= 'julio';
		if ($mes==8) $mes= 'agosto';
		if ($mes==9) $mes= 'septiembre';
		if ($mes==10) $mes= 'octubre';
		if ($mes==11) $mes= 'noviembre';
		if ($mes==12) $mes= 'diciembre';
		
		$fecha_letras = 'Caracas, '. $dia . ' de ' . $mes . '         '.$anno;
		
		$sopg = $GLOBALS['SafiRequestVars']['listaCheques']['sopg'];
		$pgch = $GLOBALS['SafiRequestVars']['listaCheques']['pgch_id'];
		$monto_chequefloat = $GLOBALS['SafiRequestVars']['listaCheques']['monto_cheque'];
		$monto_cheque = number_format(trim($monto_chequefloat),2,',','.');
		$nro_cheque = $GLOBALS['SafiRequestVars']['listaCheques']['nro_cheque'];
		$fecha_emision = $GLOBALS['SafiRequestVars']['listaCheques']['fecha_emision'];
		$nro_cuenta_bancaria = $GLOBALS['SafiRequestVars']['listaCheques']['nro_cuenta_bancaria'];
		$nombre_banco = $GLOBALS['SafiRequestVars']['listaCheques']['nombre_banco'];
		$asunto = $GLOBALS['SafiRequestVars']['listaCheques']['asunto'];
		$observaciones = $GLOBALS['SafiRequestVars']['listaCheques']['observaciones'];
		$beneficiario_cheque = $GLOBALS['SafiRequestVars']['listaCheques']['beneficiario_cheque'];
		$nombre_cuenta_pasivo = $GLOBALS['SafiRequestVars']['listaCheques']['pasivo_nombre'];
		$monto_letras = monto_letra($monto_chequefloat, " ");
		
		$tipo_fuente_letras = "Arial";
		$tipo_fuente_nros = "Arial";
		$size_fuente_letras = 10;
		$size_fuente_nros = 11;
		$negrita_fuente_letras = '';
		$negrita_fuente_nros = '';
	
		$Ye = 10; 
		$Xe = 0;
		$this->SetXY(120,$Ye+2);
		$this->SetFont($tipo_fuente_nros,$negrita_fuente_nros,$size_fuente_nros);
		$this->Cell($Xe+120,$Ye+2,$monto_cheque);

		$this->SetXY(120,$Ye+7);
		$this->SetFont($tipo_fuente_nros,$negrita_fuente_nros,$size_fuente_nros-1);
		//$this->Cell($Xe+120,$Ye+7,utf8_encode('Caduca a los 90 dias'));

		$this->SetXY(17,$Ye+13);
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,$size_fuente_letras);
		$this->Cell($Xe+17,$Ye+13,$beneficiario_cheque);
		
		$this->SetXY(18,$Ye+18);
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,$size_fuente_letras);
		$this->Cell($Xe+18,$Ye+18,$monto_letras); 
		
		$this->SetXY(4,$Ye+26);
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,12);
		$this->Cell($Xe+4,$Ye+27,$fecha_letras);

		$this->SetXY(60,$Ye+38);
		$this->SetFont($tipo_fuente_nros,$negrita_fuente_nros,$size_fuente_nros-1);
		$this->Cell($Xe+60,$Ye+38,utf8_encode('NO ENDOSABLE'));

		$Ye=$Ye+3;
		//Codigo Solicitud de pago
		$this->SetXY(5,$Ye+54);
		$this->SetFont($tipo_fuente_nros,$negrita_fuente_nros,$size_fuente_nros-1);
		$this->Cell($Xe+5,$Ye+54,$sopg);

		//Codigo Pago con Cheque
		$this->SetXY(126,$Ye+54);
		$this->SetFont($tipo_fuente_nros,$negrita_fuente_nros,$size_fuente_nros-1);
		$this->Cell($Xe+126,$Ye+54,$pgch);

		$Ye=$Ye+4;
		//Nro. Cheque
		$this->SetXY(8,$Ye+60);
		$this->SetFont($tipo_fuente_nros,$negrita_fuente_nros,$size_fuente_nros-1);
		$this->Cell($Xe+8,$Ye+60,$nro_cheque);

		//Banco
		$nro_cuenta_bancaria_ultimos_4 = substr($nro_cuenta_bancaria, -4);
		$nro_cuenta_bancaria_ultimos_4 = $nro_cuenta_bancaria_ultimos_4 ? $nro_cuenta_bancaria_ultimos_4 : "";
		$this->SetXY(56,$Ye+60);
		$this->SetFont($tipo_fuente_nros,$negrita_fuente_nros,$size_fuente_nros-1);
		$this->Cell($Xe+56,$Ye+60,$nombre_banco . " " . $nro_cuenta_bancaria_ultimos_4);

		//Fecha
		$this->SetXY(126,$Ye+60);
		$this->SetFont($tipo_fuente_nros,$negrita_fuente_nros,$size_fuente_nros-1);
		$this->Cell($Xe+126,$Ye+60,$fecha_emision);

		//Beneficiario
		$this->SetXY(8,$Ye+64);
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,$size_fuente_letras);
		$this->Cell($Xe+8,$Ye+64,$beneficiario_cheque);

		//Concepto
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,$size_fuente_letras);
		$this->SetXY(16,$Ye+108);		
		$this->MultiCell(130,5,$observaciones);

		$this->SetXY(22,$Ye+100);
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,$size_fuente_letras);
		$this->Cell($Xe+22,$Ye+100,$nombre_cuenta_pasivo);

		$this->SetXY(125,$Ye+100);
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,$size_fuente_letras);
		$this->Cell($Xe+125,$Ye+100,$monto_cheque);

		$this->SetXY(22,$Ye+108);
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,$size_fuente_letras);
		$this->Cell($Xe+22,$Ye+108,$nombre_banco);

		$this->SetXY(141,$Ye+108);
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,$size_fuente_letras);
		$this->Cell($Xe+141,$Ye+108,$monto_cheque);
	}

/*********Pie de pagina***************************/

	function Footer() {
			$tipo_fuente_letras = "Arial";
			$tipo_fuente_nros = "Arial";
			$size_fuente_letras = 9;
			$size_fuente_nros = 10;
			$negrita_fuente_letras = '';
			$negrita_fuente_nros = '';	
			$this->SetY(-100);
	}
		
	function SetCol($col) {
	    //Establecer la posición de una columna dada
	    $this->col=$col;
	    $x=10+$col*65;
	    $this->SetLeftMargin($x);
	    $this->SetX($x);
	}
	function AcceptPageBreak() {
	    //Método que acepta o no el salto automático de página
	    if($this->col<2)  {
	        //Ir a la siguiente columna
	        $this->SetCol($this->col+1);
	        //Establecer la ordenada al principio
	        $this->SetY($this->y0);
	        //Seguir en esta página
	        return false;
	    }
	    else  {
	        //Volver a la primera columna
	        $this->SetCol(0);
	        //Salto de página
	        return true;
	    }
	}
}		 

$pdf=new PDF();
$pdf->AddPage();

$pdf->Imprime();
$pdf->Ln(3);
$pdf->SetX(140);

$archivo = basename(tempnam(getcwd(),'tmp'));
rename($archivo, $archivo.'.pdf');
$archivo.='.pdf';
//Guardar el pdf en un fichero
$pdf->Output($archivo);

//Redireccion con JavaScript a una ventana nueva por la session
echo("<html><script>window.open('".$archivo."','pdf')</script></html>");

require_once(SAFI_INCLUDE_PATH.'/funciones.php');
limpiarTemporalesPdf(dirname(__FILE__));
?>