<?php
include(dirname(__FILE__) . '/../../../init.php');
require("../../../lib/fpdf/fpdf.php");
require("../../../includes/reporteBasePdf.php");
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
$form = FormManager::GetForm(FORM_OPERACIONES_EMITIDAS_TESORERIA);

class PDF extends FPDF {
	function Header() {
		global $cuenta;
		global $tipo;
		global $fecha_in;
		global $fecha_fi;
		global $fecha_inicio_antes;
		global $monto_inicial;
		
		$this->Setx(18);
		$this->Image('../../../imagenes/encabezado.jpg',0,5,260,15);
		
		$this->SetFont('Arial','B',10);
		$this->Sety(28);
		$this->Cell(120);
		//Titulo
		$this->Setx(95);
		$this->Cell(95,7,utf8_decode($GLOBALS['SafiRequestVars']['titulo'].$GLOBALS['SafiRequestVars']['titulo2']),27,27,'C');
		
		$this->Ln(2);
		$this->SetFillColor(69,69,159);
		$this->SetTextColor(255);
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.1);
		$this->SetFont('Arial','B',8);
		
		//Cabecera
		$this->Setx(15);
		$header=array("sopg/codi","pgch/tran","Cuenta","Fecha",utf8_decode('N° cheq'),"Est","Beneficiario","Concepto",utf8_decode('Debe'),utf8_decode('Haber'),"Saldo final");
		$w=array(18,19,15,15,22,6,35,35,30,30,30,30);
		for($i=0;$i<count($header);$i++)
			$this->Cell($w[$i],5,$header[$i],1,0,'C',1);
		$this->Ln();
	}
	
	function Footer() {
		$this->SetY(-15);
		$this->SetFont('Arial','I',7);
		$this->Cell(0,10,utf8_decode('SAFI-Fundación Infocentro'),0,0,'C');

		//	Número de página
		$this->Cell(0,10,$this->PageNo().'/{nb}',0,0,'R');
		 
		$this->SetY(-12);
		$this->Cell(0,10,fecha(),0,0,'C');
	}

	function PDF($orientation='L',$unit='mm',$format='Letter')
	{
		//Call parent constructor
		$this->FPDF($orientation,$unit,$format);
	}

	function FancyTable($datas)
	{

		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.1);

		//	Cabecera
		$w=array(18,19,15,15,22,6,35,35,30,30,30,30);
		 
		$this->SetTextColor(0);
		$this->SetFont('Arial');
		$xx=20;
		$yy=5;
		$yy2=5;
		$pagina=2;
		 
		$this->setx($xx);
		$nombreAnterior="";
		foreach($datas as $row)
		{
			$this->Setx(18);
			$this->Cell(html_entity_decode(htmlentities($w[0])),$this->FontSize+0.75,$row[0],'',0,'L');
			$this->Cell($w[1],$this->FontSize+0.75,$row[1],'',0,'L');

			$this->Cell($w[2],$this->FontSize+0.75,$row[2],'',0,'R');
			$this->Cell($w[3],$this->FontSize+0.75,$row[3],'',0,'R');

			$this->Cell(html_entity_decode(htmlentities($w[4])),$this->FontSize+0.75,$row[4],'',0,'R');

			$posy= $this->gety();

			$this->Cell($w[5],$this->FontSize+0.75,$row[5],'',0,'R');

			$this->Cell($w[6],$this->FontSize,$row[6],'',0,'L');

			$this->Cell($w[7],$this->FontSize+0.75,$row[7],'',0,'L');

			$this->Cell($w[8],$this->FontSize+0.75,$row[8],'',0,'R');

			$this->Cell($w[9],$this->FontSize+0.75,$row[9],'',0,'R');
			 
			$this->Cell($w[10],$this->FontSize+0.75,$row[10],'',0,'R');

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

$valor = ' || ||'.substr($GLOBALS['SafiRequestVars']['cuentaBancaria'],10).'||'.$GLOBALS['SafiRequestVars']['fecha_inicio_antes'].'|| ||'.'I ||  ||  ||'.number_format($GLOBALS['SafiRequestVars']['saldo_banco'],2,',','.').'|| ||'.number_format($GLOBALS['SafiRequestVars']['saldo_banco'],2,',','.');
$data[$cuentadata]= explode("||",$valor);
$cuentadata=$cuentadata+1;
$saldo_inicial = $GLOBALS['SafiRequestVars']['saldo_banco'];
$saldo_final = $GLOBALS['SafiRequestVars']['saldo_banco'];
foreach ($GLOBALS['SafiRequestVars']['listaDocumentos'] as $listaDocumento) {

	$valor= substr($$listaDocumento["sopg_id"], 0, 1).substr($listaDocumento["sopg_id"], 5).'||'.substr($listaDocumento["pago_id"], 0, 1).substr($listaDocumento["pago_id"], 5).'||'.substr($listaDocumento["nro_cuenta_bancaria"],10).'||'.$listaDocumento["fecha_pagado"].'||'.$listaDocumento["referencia"].'||'.$listaDocumento["condicion"].'||'.substr(trim(ucwords(strtolower($listaDocumento["beneficiario"]))),0,23).'||'.substr(trim(ucwords(strtolower($listaDocumento["comentario"]))),0,25).'||';
	if ($listaDocumento["monto"]>0) {
		$monto=$listaDocumento["monto"]*-1;
		$saldo_inicial = $saldo_inicial + $monto;
		$saldo_final = $saldo_final + $monto;
	}
	else {
		$monto="";
		$saldo_inicial = $saldo_inicial - $listaDocumento["monto"];
		$saldo_final = $saldo_final - $listaDocumento["monto"];
	}
	$valor.=number_format($monto*-1,2,',','.').'||';
	if ($listaDocumento["monto"]<0) $monto=$listaDocumento["monto"];
	else $monto="";
	$valor.=number_format($monto*-1,2,',','.').'||';
	$valor .= number_format($saldo_final,2,',','.');

	$data[$cuentadata]= explode("||",$valor);
	$cuentadata=$cuentadata+1;
	$valor='';
}

$pdf->FancyTable($data);
$pdf->Output();