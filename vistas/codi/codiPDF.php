<?php
require_once(dirname(__FILE__) . '/../../init.php');
require_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
require_once(SAFI_INCLUDE_PATH.'/funciones.php');
require_once(SAFI_INCLUDE_PATH.'/fechas.php');
require_once(SAFI_LIB_PATH.'/fpdf/fpdf.php');
require_once(SAFI_INCLUDE_PATH.'/monto_a_letra.php');

$salidaEstandar = false;
if( isset($GLOBALS['SafiRequestVars']['salidaEstandar']) &&  $GLOBALS['SafiRequestVars']['salidaEstandar'] === true){
	$salidaEstandar = true;
}

$GLOBALS['indice'] = 0;
$GLOBALS['usuario'] = array();
$GLOBALS['fecha_emis'] = array();
$GLOBALS['comp_fec'] = array();
$GLOBALS['comentario'] = array();
$GLOBALS['edo'] = array();
$GLOBALS['hora_emis'] = array();
$GLOBALS['memo_fecha'] = array();
$GLOBALS['memo_contenido'] = array();
$GLOBALS['memo_responsable'] = array();
$GLOBALS['id_comp'] = array();
$GLOBALS['documento'] = array();
$GLOBALS['referencia'] = array();
$GLOBALS['compromiso'] = array();
$GLOBALS['fuente'] = array();

/*Identificador de variables del header*/
if(is_array($GLOBALS['SafiRequestVars']['listaCodi'])){
	foreach ($GLOBALS['SafiRequestVars']['listaCodi'] as $listaCodi) {
		$GLOBALS['id_comp'][] = $listaCodi['comp_id'];
		$GLOBALS['comp_fec'][] = cambia_esp($listaCodi['comp_fec']);
		$GLOBALS['comentario'][] = $listaCodi['comp_comen'];
		$GLOBALS['fecha_emis'][] = cambia_esp($listaCodi['comp_fec_emis']);
		$GLOBALS['hora_emis'][] = substr(trim($listaCodi['comp_fec_emis']),10);
		$GLOBALS['edo'][] = $listaCodi['esta_id'];
		$GLOBALS['documento'][] = $listaCodi['comp_doc_id'];
		$GLOBALS['referencia'][] = $listaCodi['nro_referencia'];
		$GLOBALS['compromiso'][] = $listaCodi['nro_compromiso'];
		$GLOBALS['usuario'][] = $listaCodi['usuario'];
		$GLOBALS['fuente'][] = $listaCodi['fuente_financiamiento'];
		if($listaCodi['memo_id'] && $listaCodi['memo_id'] != ""){
			$GLOBALS['memo_fecha'][] = $listaCodi['memo_fecha_crea'];
			$GLOBALS['memo_contenido'][] = $listaCodi['memo_contenido'];
			$GLOBALS['memo_responsable'][] = $listaCodi['memo_responsable'];
		}else{
			$GLOBALS['memo_fecha'][] = "No Registrado";
			$GLOBALS['memo_contenido'][] = "No Registrado";
			$GLOBALS['memo_responsable'][] = "No Registrado";
		}
	}
}

$GLOBALS['comp_reng'] = array();
$GLOBALS['id_cta'] = array();
$GLOBALS['nom_cta'] = array();
$GLOBALS['debe'] = array();
$GLOBALS['haber'] = array();
$GLOBALS['total_db'] = array();
$GLOBALS['total_hb'] = array();
$GLOBALS['total_items'] = array();
/*Presupuesto*/
$GLOBALS['apde_partida']=array();
$GLOBALS['cpat_id']=array();
$GLOBALS['apde_monto']=array();
$GLOBALS['centrog']=array();
$GLOBALS['centroc']=array();
$GLOBALS['total_imputacion']=array();


/*Cuentas contables y partidas presupuestaria*/
$codiAnterior = "";
$indiceCodi=-1;

if(is_array($GLOBALS['SafiRequestVars']['listaCodiDetalle'])){
	foreach ($GLOBALS['SafiRequestVars']['listaCodiDetalle'] as $listaCodi) {
		foreach ($listaCodi as $listaCodi2) {
			if($codiAnterior=="" || $codiAnterior != $listaCodi2['comp_id']){
				$codiAnterior = $listaCodi2['comp_id'];
				$indiceCodi++;
				/*Inf contable*/
				$GLOBALS['comp_reng'][$indiceCodi] = array();
				$GLOBALS['id_cta'][$indiceCodi] = array();
				$GLOBALS['nom_cta'][$indiceCodi] = array();
				$GLOBALS['debe'][$indiceCodi] = array();
				$GLOBALS['haber'][$indiceCodi] = array();
				$GLOBALS['total_db'][$indiceCodi] = 0;
				$GLOBALS['total_hb'][$indiceCodi] = 0;
				$GLOBALS['total_items'][$indiceCodi] = 0;
				/*Inf presupuestaria*/
				$GLOBALS['apde_partida'][$indiceCodi] = array();
				$GLOBALS['cpat_id'][$indiceCodi] = array();
				$GLOBALS['apde_monto'][$indiceCodi] = array();
				$GLOBALS['centrog'][$indiceCodi] = array();
				$GLOBALS['centroc'][$indiceCodi] = array();
				$GLOBALS['total_imputacion'][$indiceCodi] = 0;				
			}
			
			$GLOBALS['comp_reng'][$indiceCodi][sizeof($GLOBALS['comp_reng'][$indiceCodi])] = $listaCodi2['reng_comp'];
			$GLOBALS['id_cta'][$indiceCodi][sizeof($GLOBALS['id_cta'][$indiceCodi])] = $listaCodi2['cpat_id'];
			$GLOBALS['nom_cta'][$indiceCodi][sizeof($GLOBALS['nom_cta'][$indiceCodi])] = $listaCodi2['cpat_nombre'];
			$GLOBALS['debe'][$indiceCodi][sizeof($GLOBALS['debe'][$indiceCodi])] = $listaCodi2['rcomp_debe'];
			$GLOBALS['haber'][$indiceCodi][sizeof($GLOBALS['haber'][$indiceCodi])] = $listaCodi2['rcomp_haber'];
			$GLOBALS['total_db'][$indiceCodi] += $listaCodi2['rcomp_debe'];
			$GLOBALS['total_hb'][$indiceCodi] += $listaCodi2['rcomp_haber'];			
			$GLOBALS['total_items'][$indiceCodi] ++;

			$GLOBALS['apde_partida'][$indiceCodi][sizeof($GLOBALS['apde_partida'][$indiceCodi])] = $listaCodi2['part_id'];
			$GLOBALS['cpat_id'][$indiceCodi][sizeof($GLOBALS['cpat_id'][$indiceCodi])] = $listaCodi2['cpat_id'];
			$GLOBALS['apde_monto'][$indiceCodi][sizeof($GLOBALS['apde_monto'][$indiceCodi])] = $listaCodi2['rcomp_debe']-$listaCodi2['rcomp_haber'];
			$GLOBALS['centrog'][$indiceCodi][sizeof($GLOBALS['centrog'][$indiceCodi])] = $listaCodi2['centros'];
			$GLOBALS['centroc'][$indiceCodi][sizeof($GLOBALS['centroc'][$indiceCodi])] = '';
			$GLOBALS['total_imputacion'][$indiceCodi] ++;			
		}
	}
}
$codiAnterior = "";
/*
if(is_array($GLOBALS['SafiRequestVars']['listaCodiPresupuesto'])){
	foreach ($GLOBALS['SafiRequestVars']['listaCodiPresupuesto'] as $listaCodi) {
		foreach ($listaCodi as $listaCodi2) {
			if($codiAnterior=="" || $codiAnterior != $listaCodi2['comp_id']){
				$codiAnterior = $listaCodi2['comp_id'];
				$indiceCodi= $listaCodi2['comp_id'];
				$GLOBALS['apde_partida'][$indiceCodi] = array();
				$GLOBALS['cpat_id'][$indiceCodi] = array();
				$GLOBALS['apde_monto'][$indiceCodi] = array();
				$GLOBALS['centrog'][$indiceCodi] = array();
				$GLOBALS['centroc'][$indiceCodi] = array();
				$GLOBALS['total_imputacion'][$indiceCodi] = 0;
			}
//error_log($listaCodi2['part_id']);
			$GLOBALS['apde_partida'][$indiceCodi][sizeof($GLOBALS['apde_partida'][$indiceCodi])] = $listaCodi2['part_id'];
			$GLOBALS['cpat_id'][$indiceCodi][sizeof($GLOBALS['cpat_id'][$indiceCodi])] = $listaCodi2['cpat_id'];
			$GLOBALS['apde_monto'][$indiceCodi][sizeof($GLOBALS['apde_monto'][$indiceCodi])] = $listaCodi2['cadt_monto'];
			$GLOBALS['centrog'][$indiceCodi][sizeof($GLOBALS['centrog'][$indiceCodi])] = $listaCodi2['centro_gestor'];
			$GLOBALS['centroc'][$indiceCodi][sizeof($GLOBALS['centroc'][$indiceCodi])] = $listaCodi2['centro_costo'];
			$GLOBALS['total_imputacion'][$indiceCodi] ++;
		}
	}
}*/



function Imprime($pdf){
	global $id_comp;
	global $edo;
	global $indice;
	global $comp_reng;
	global $id_cta;
	global $nom_cta;
	global $debe;
	global $haber;
	global $cpat_id;
	global $total_db;
	global $total_hb;
	global $total_items;
	global $apde_partida;
	global $cpat_id;
	global $apde_monto;
	global $centrog;
	global $centroc;
	global $total_imputacion;	
		
	$pdf->Ln(3);
	$pdf->SetX(140);
	
	$indice = 0;
	
	/**/
	
	$i = 0;
	while($i < sizeof($GLOBALS['SafiRequestVars']['listaCodiDetalle'])){
		$alto=4;
		$pdf->AddPage();
		$pdf->AliasNbPages();
		$posy= $pdf->gety();
		$pdf->SetFont('Arial','B',6);
		$pdf->SetXY( 10,($posy+1));
		$pdf->Cell(50,$alto,"",0,0,'l');
		$pdf->SetXY( 10,($posy+1));
		$pdf->SetFont('Arial','B',9);
		$pdf->SetX(10);
		$posy= $pdf->gety();
		$pdf->Cell(24,$alto,utf8_decode("Nº Registro"),0,0,'C');
		$pdf->Cell(30,$alto,utf8_decode("Cuenta contable"),0,0,'C');
		$pdf->Cell(136,$alto,utf8_decode("Descripción"),0,0,'C');
		$pdf->Cell(30,$alto,"Debe",0,0,'C');
		$pdf->Cell(30,$alto,"Haber",0,2,'C');
		$pdf->SetX(5.5);
		$pdf->Cell(24,$alto,"",0,0,'C');
		$pdf->Cell(30,$alto,"",0,0,'C');
		$pdf->Cell(136,$alto,"",0,0,'C');
		$pdf->Cell(30,$alto,"",0,0,'C');
		$pdf->Cell(30,$alto,"",0,2,'C');
		$pdf->SetXY(10,$posy);
		$pdf->Cell(24,($alto),"",1,0,'C');
		$pdf->Cell(30,($alto),"",1,0,'C');
		$pdf->Cell(136,($alto),"",1,0,'C');
		$pdf->Cell(30,($alto),"",1,0,'C');
		$pdf->Cell(30,($alto),"",1,2,'C');
		$pdf->SetFont('Arial','',8);

	
		for($ii=0; $ii< $total_items[$indice]; $ii++){
			$pdf->SetX(10);
			$pdf->Cell(24,$alto,($comp_reng[$indice][$ii]),1,0,'R');
			$pdf->Cell(30,$alto,trim($id_cta[$indice][$ii]),1,0,'L');
			$pdf->Cell(136,$alto,$nom_cta[$indice][$ii],1,0,'L');
			$pdf->Cell(30,$alto,(number_format($debe[$indice][$ii],2,'.',',')),1,0,'R');
			$pdf->Cell(30,$alto,(number_format($haber[$indice][$ii],2,'.',',')),1,2,'R');
		}
		
		$pdf->Ln();
		$posy= $pdf->gety();
		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY( 5.5,($posy+1));
	
		$pdf->SetX(170);
		$pdf->Cell(30,$alto,"Total: ",1,0,'C');
		$pdf->Cell(30,$alto,(number_format($total_db[$indice],2,'.',',')),1,0,'R');
		$pdf->Cell(30,$alto,(number_format($total_hb[$indice],2,'.',',')),1,2,'R');
		$pdf->SetX(80);
		$pdf->Ln();
	
		$pdf->SetFont('Arial','B',9);
		$pdf->SetX(65);
		$pdf->Cell(111,$alto,utf8_decode("Imputación presupuestaria"),1,2,'C');
		$pdf->SetX(65);
		$pdf->SetFont('Arial','',8);
		$posy= $pdf->gety();
	
		$pdf->Cell(30,$alto,"Proyecto/Acc",1,0,'C');
		//$pdf->Cell(25,$alto,utf8_decode("Acción específica"),1,0,'C');
		$pdf->Cell(27,$alto,"Partida",1,0,'C');
		$pdf->Cell(28,$alto,utf8_decode("Cuenta"),1,0,'C');
		$pdf->Cell(26,$alto,utf8_Decode("Monto"),1,2,'C');
	
		
		for($ii=0; $ii< $total_items[$indice]; $ii++){
			$pdf->SetX(65);
			$pdf->gety();
			$pdf->Cell(30,$alto,$centrog[$indice][$ii],1,0,'C');
			//$pdf->Cell(25,$alto,$centroc[$indice][$ii],1,0,'C');
			$pdf->Cell(27,$alto,$apde_partida[$indice][$ii],1,0,'C');
			$pdf->Cell(28,$alto,$cpat_id[$indice][$ii],1,0,'C');
			$pdf->Cell(26,$alto,(number_format($apde_monto[$indice][$ii],2,'.',',')),1,2,'R');
		}
	
		$posy= $pdf->gety();
		$pdf->SetX(10);
		$pdf->SetXY( 5.5,$posy);
		$pdf->Cell(250,8,"",0,2,'R');
		$posy= $pdf->gety();
	
		$pdf->Ln();
		if($edo[$indice]==15){
			$pdf->Image('../../imagenes/anulado.jpg',210,152,46,35);
		}
		$pdf->Ln();
		$pdf->setAutoPagebreak(true,'');
		$i++;
		$indice = $i;
	}	
}

class PDF extends FPDF { //Definir el encabezado y pie de pagina del cheque
  
	function Header() { 
		global $id_comp;
		global $indice;
		global $comp_fec;
		global $comentario;
		global $edo;
		global $documento;
		global $referencia;
		global $compromiso;
		global $fuente;
		global $usuario;
		
		$alto = 4;
		
		$this->SetX(50);
		$this->SetY(65);
		$this->Image('../../imagenes/encabezado.jpg',3,22,260,15);
		$this->Ln(3);
		
		$this->SetFont('Arial','',8);
		$this->SetXY(43.5,40);
		$this->Cell(220,8,'Fecha: '.$comp_fec[$indice],0,1,'R');
		$this->SetFont('Arial','B',14);
		//Titulo
		$posy= $this->gety();
		$this->SetX(3.5);
		$this->SetY(41);
		$this->Cell(250,15,'COMPROBANTE DIARIO '.$id_comp[$indice],0,1,'C');
		
		$this->Ln(1);
		$this->Setx(10);
		$this->SetFont('Arial','',9);
		$this->SetX(10);
		$this->Cell(125,$alto,"Documento asociado:  ".$documento[$indice],1,0,'L');
		$this->Cell(125,$alto,utf8_decode("Nº Referencia bancaria:  ").$referencia[$indice],1,2,'L');
		$this->SetX(10);
		$this->Cell(125,$alto,utf8_decode("Fuente de financiamiento:  ").$fuente[$indice],1,0,'L');
		$this->Cell(125,$alto,utf8_decode("Nº Compromiso: ").$compromiso[$indice],1,2,'L');
		$this->SetX(10);
		$this->Cell(250,$alto,utf8_decode("Justificación:  ").$comentario[$indice],1,2,'L');
		
		$this->Ln();
		$this->SetX(10);
		$this->SetFont('Arial','B',7);
	}

/*********Pie de pagina***************************/

	function Footer() {
		global $indice;		
		global $memo_fecha;
		global $memo_contenido;
		global $memo_responsable;
		global $usuario;
		global $fecha_emis;
		global $hora_emis;	
		global $edo;	
		
			$tipo_fuente_letras = "Arial";
			$tipo_fuente_nros = "Arial";
			$size_fuente_letras = 9;
			$size_fuente_nros = 10;
			$negrita_fuente_letras = '';
			$negrita_fuente_nros = '';	
			$this->SetY(-40);

			$this->SetX(10);
			$this->Cell(260,7,utf8_decode("Este comprobante fue generado el día: ").trim($fecha_emis[$indice-1]).$hora_emis[$indice-1]."\n   "."por: ".$usuario[$indice-1],0,0,'C');
			
			if($edo[$indice-1]==15){
				$this->SetFont('Arial','B',7);
				$this->SetX(50);
				$this->SetY(159);
				$this->Cell(260,8,utf8_decode("Nota: Este comprobante fue anulado el día ").$memo_fecha[$indice-1]. " por ".utf8_decode($memo_responsable[$indice-1]),0,0,'C');
				$this->SetX(90);
				$this->SetY(167);
				$this->Cell(260,7,utf8_Decode("Justificación: ").$memo_contenido[$indice-1],0,0,'C');
			}
			$this->SetXY(53.5,-25);
			$this->SetFont('Arial','B',8);
			//Numero de pagina
			$this->Cell(165,10,utf8_decode('SAFI-Fundación Infocentro'),0,0,'C');
			$this->SetFont('Arial','',8);
			$this->SetXY(53.5,-25);
			
			$this->Cell(165,16,utf8_decode('Fecha de impresión:').'  '.actual_date(),0,0,'C');//.' a las '.date("H:i:s")			
			
	}
}		 

$pdf=new PDF('L','mm','Letter');
Imprime($pdf);

if($salidaEstandar === true)
{
	// Enviar el pdf a la salida estándar
	$pdf->Output("codi - ".date("Y_m_d H:i:s").".pdf", "I");
} else {
	$archivo = basename(tempnam(getcwd(),'tmp'));
	$archivo.='.pdf';
	//Guardar el pdf en un fichero
	$pdf->Output($archivo);
	
	//Redireccion con JavaScript a una ventana nueva por la session
	echo("<html><script>window.open('".$archivo."','pdf')</script></html>");
	
	require_once(SAFI_INCLUDE_PATH.'/funciones.php');
	limpiarTemporalesPdf(dirname(__FILE__));
}

?>