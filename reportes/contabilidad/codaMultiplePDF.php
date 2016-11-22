<?php
require("../../includes/conexion.php");
require("../../lib/fpdf/fpdf.php");
require_once("../../includes/fechas.php");

$codas = $_REQUEST['codas'];
$anulado = $_REQUEST['anulado'];

$comp_id = array();
$comp_fec = array();
$comp_comen = array();
$comp_fec_emis = array();
$esta_id = array();
$depe_id = array();
$comp_doc_id = array();
$reserva = array();

if($codas!=""){
	$tok = strtok($codas, ",");
	$codas = "(";
	while ($tok !== false) {
	    $codas .= "'".$tok."',";
	    $tok = strtok(",");
	}
	$codas = substr($codas, 0, -1).")";
}

$sql = 	"SELECT ".
			"scd.comp_id, ".
			"scd.comp_fec, ".
			"scd.comp_comen, ".
			"scd.comp_fec_emis, ".
			"scd.esta_id, ".
			"scd.depe_id, ".
			"scd.comp_doc_id, ".
			"sdg.numero_reserva, ".
			"ssp.comp_id as compromiso ".
		"FROM sai_comp_diario scd, sai_doc_genera sdg, sai_sol_pago ssp ".
		"WHERE ".
			"scd.comp_id IN ".$codas." AND ".
			"scd.comp_doc_id = sdg.docg_id ".
			"and sdg.docg_id=ssp.sopg_id and ssp.sopg_id=scd.comp_doc_id ".
		"ORDER BY scd.comp_fec_emis, scd.comp_doc_id";

$resultado=pg_query($conexion,$sql) or die("Error al mostrar");

while($row=pg_fetch_array($resultado)){
	$comp_id[sizeof($comp_id)]=trim($row['comp_id']);
	$comp_fec[sizeof($comp_fec)]=trim($row['comp_fec']);
	$comp_comen[sizeof($comp_comen)]=trim($row['comp_comen']);
	$comp_fec_emis[sizeof($comp_fec_emis)]=trim($row['comp_fec_emis']);
	$esta_id[sizeof($esta_id)]=trim($row['esta_id']);
	$depe_id[sizeof($depe_id)]=trim($row['depe_id']);
	$comp_doc_id[sizeof($comp_doc_id)]=trim($row['comp_doc_id']);
	$reserva[sizeof($reserva)]=trim($row['numero_reserva']);
	$compromiso[sizeof($compromiso)]=trim($row['compromiso']);
}

$sql= 	"SELECT ".
			"src.comp_id, ".
			"src.reng_comp, ".
			"src.cpat_id, ".
			"src.cpat_nombre, ".
			"src.rcomp_debe, ".
			"src.rcomp_haber, ".
			"src.rcomp_tot_db, ".
			"src.rcomp_tot_hab ".
		"FROM sai_comp_diario scd, sai_reng_comp src ".
		"WHERE ".
			"src.comp_id IN ".$codas." AND ".
			"src.comp_id = scd.comp_id ".
		"ORDER BY scd.comp_fec_emis, scd.comp_doc_id, src.rcomp_id,src.reng_comp ";

$resultado=pg_query($conexion,$sql) or die("Error al mostrar");

$comp_reng = array();
$id_cta = array();
$nom_cta = array();
$rcomp_debe = array();
$rcomp_haber = array();
$total_debe = array();
$total_haber = array();
$total_items = array();

$codaAnterior = "";
$indiceCoda=-1;
while($row=pg_fetch_array($resultado)){
	if($codaAnterior=="" || $codaAnterior!=$row['comp_id']){
		$codaAnterior = $row['comp_id'];
		$indiceCoda++;
		$comp_reng[$indiceCoda] = array();
		$id_cta[$indiceCoda] = array();
		$nom_cta[$indiceCoda] = array();
		$rcomp_debe[$indiceCoda] = array();
		$rcomp_haber[$indiceCoda] = array();
		$total_debe[$indiceCoda] = 0;
		$total_haber[$indiceCoda] = 0;
		$total_items[$indiceCoda] = 0;
	}
	
	$comp_reng[$indiceCoda][sizeof($comp_reng[$indiceCoda])] = $row['reng_comp'];
	$id_cta[$indiceCoda][sizeof($id_cta[$indiceCoda])] = $row['cpat_id'];
	$nom_cta[$indiceCoda][sizeof($nom_cta[$indiceCoda])] = $row['cpat_nombre'];
	$rcomp_debe[$indiceCoda][sizeof($rcomp_debe[$indiceCoda])] = $row['rcomp_debe'];
	$rcomp_haber[$indiceCoda][sizeof($rcomp_haber[$indiceCoda])] = $row['rcomp_haber'];
	$total_debe[$indiceCoda] += $row['rcomp_debe'];
	$total_haber[$indiceCoda] += $row['rcomp_haber'];
	$total_items[$indiceCoda] ++;
}

$sql = "SELECT ".
			"s.comp_id, ".
			"s.cadt_id_p_ac, ".
			"s.cadt_cod_aesp, ".
			"s.nombre_categ, ".
			"s.nombre_esp, ".
			"s.comp_fec_emis, ".
			"s.comp_doc_id ".
		"FROM ".
			"(".
				"(SELECT ".
					"scd.comp_id, ".
					"scde.cadt_id_p_ac, ".
					"scde.cadt_cod_aesp, ".
					"sp.proy_titulo as nombre_categ, ".
					"spae.paes_nombre as nombre_esp, ".
					"scd.comp_fec_emis, ".
					"scd.comp_doc_id ".
				"FROM sai_comp_diario scd, sai_causad_det scde, sai_causado sc, sai_comp_diario scdi, sai_proyecto sp, sai_proy_a_esp spae ".
				"WHERE ".
					"scd.comp_id IN ".$codas." AND ".
					"sc.caus_docu_id = scd.comp_doc_id AND ".
					"sc.esta_id <> 15 AND ".
					"sc.caus_docu_id = scdi.comp_doc_id AND ".
					"sc.caus_id = scde.caus_id AND ".
					"scde.pres_anno = SUBSTR(scdi.comp_fec,0,5) AND ".
					"scde.cadt_tipo = '1' AND ".
					"sp.proy_id = scde.cadt_id_p_ac AND ".
					"sp.pre_anno = sc.pres_anno AND ".
					"spae.proy_id = sp.proy_id AND ".
					"spae.paes_id = scde.cadt_cod_aesp AND ".
					"spae.pres_anno = sc.pres_anno) ".
			"UNION ".
				"(SELECT ".
					"scd.comp_id, ".
					"scde.cadt_id_p_ac, ".
					"scde.cadt_cod_aesp, ".
					"sac.acce_denom as nombre_categ, ".
					"sae.aces_nombre as nombre_esp, ".
					"scd.comp_fec_emis, ".
					"scd.comp_doc_id ".
				"FROM sai_comp_diario scd, sai_causad_det scde, sai_causado sc, sai_comp_diario scdi, sai_ac_central sac, sai_acce_esp sae ".
				"WHERE ".
					"scd.comp_id IN ".$codas." AND ".
					"sc.caus_docu_id = scd.comp_doc_id AND ".
					"sc.esta_id <> 15 AND ".
					"sc.caus_docu_id = scdi.comp_doc_id AND ".
					"sc.caus_id = scde.caus_id AND ".
					"scde.pres_anno = SUBSTR(scdi.comp_fec,0,5) AND ".
					"scde.cadt_tipo = '0' AND ".
					"sac.acce_id = scde.cadt_id_p_ac AND ".
					"sac.pres_anno = sc.pres_anno AND ".
					"sae.acce_id = sac.acce_id AND ".
					"sae.aces_id = scde.cadt_cod_aesp AND ".
					"sae.pres_anno = sc.pres_anno) ".
			") AS s ".
		"ORDER BY s.comp_fec_emis, s.comp_doc_id ";

$categoria = array();
$aesp = array();
$nom_categoria = array();
$nom_aesp = array();

$resultado=pg_query($conexion,$sql) or die ("Error al mostrar datos presupuestarios");
while($row=pg_fetch_array($resultado)){
	$categoria[sizeof($categoria)] = $row['cadt_id_p_ac'];
	$aesp[sizeof($aesp)] = $row['cadt_cod_aesp'];
	$nom_categoria[sizeof($nom_categoria)] = $row['nombre_categ'];
	$nom_aesp[sizeof($nom_aesp)] = $row['nombre_esp'];
}

$sql =	"SELECT ".
			"scd.comp_id, ".
			"sspi.sopg_sub_espe, ".
			"sspi.sopg_monto, ".
			"sspi.sopg_monto_exento, ".
			"sc.cpat_id, ".
			"scd.comp_doc_id ".
		"FROM sai_comp_diario scd, sai_sol_pago_imputa sspi, sai_sol_pago ssp, sai_convertidor sc ".
		"WHERE ".
			"scd.comp_id IN ".$codas." AND ".	
			"trim(sspi.sopg_id) = trim(scd.comp_doc_id) AND ".
			"sspi.sopg_id = ssp.sopg_id AND ".
			"sspi.sopg_sub_espe = sc.part_id ".
		"ORDER BY scd.comp_fec_emis, scd.comp_doc_id, sspi.sopg_sub_espe";
$resultado= pg_exec($conexion, $sql);

$matriz_sub_esp = array();
$matriz_monto = array();
$cuenta = array();
$total_imputacion = array();

$codaAnterior = "";
$indiceCoda=-1;
while($row=pg_fetch_array($resultado)){
	if($codaAnterior=="" || $codaAnterior!=$row['comp_id']){
		$codaAnterior = $row['comp_id'];
		$indiceCoda++;
		$matriz_sub_esp[$indiceCoda] = array();
		$matriz_monto[$indiceCoda] = array();
		$cuenta[$indiceCoda] = array();
		$total_imputacion[$indiceCoda] = 0;
	}
	$matriz_sub_esp[$indiceCoda][sizeof($matriz_sub_esp[$indiceCoda])] = $row['sopg_sub_espe'];	
	$matriz_monto[$indiceCoda][sizeof($matriz_monto[$indiceCoda])] = trim($row['sopg_monto']+$row['sopg_monto_exento']);
	$cuenta[$indiceCoda][sizeof($cuenta[$indiceCoda])] = $row['cpat_id'];
	$total_imputacion[$indiceCoda] ++;
}

$indice = 0;

class PDF extends FPDF{

	function Header(){
		$alto=4;
		global $indice;
		global $comp_fec;
		global $comp_id;
		global $comp_comen;
		global $comp_doc_id;
		global $nom_categoria;
		global $nom_aesp;
		global $categoria;
		global $aesp;
		global $reserva;
		global $anulado;
		global $compromiso;		

		$this->SetX(50);
		$this->SetY(45);
		$this->Image('../../imagenes/encabezado.jpg',3,22,260,15);
		$this->Ln(3);
		
		$this->SetFont('Arial','B',14);
		$posy= $this->gety();
		$this->SetX(25);
		$this->Cell(250,15,utf8_decode('COMPROBANTE DE DIARIO AUTOMÁTICO '),0,1,'C');
		
		$this->Ln(1);
		$this->Setx(10);
		$this->SetFont('Arial','',9);
		$this->Cell(250,$alto,utf8_decode("N° Comprobante:  ").$comp_id[$indice],1,2,'L');
		$this->Setx(10);
		$this->Cell(83,$alto,utf8_decode("N° Solicitud de Pago: ").$comp_doc_id[$indice],1,0,'L');
		$this->Cell(83,$alto,utf8_decode("Número de Reserva:  ").$reserva[$indice],1,0,'L');
		$this->Cell(84,$alto,utf8_decode("Número Compromiso:  ").$compromiso[$indice],1,2,'L');
		$this->SetX(10);
		$this->Cell(250,$alto,"Fecha del Comprobante:  ".$comp_fec[$indice],1,1,'L');
		$this->SetX(10);
		$this->Cell(250,$alto,"Comentario:  ".$comp_comen[$indice],1,2,'L');
		$this->Cell(250,$alto,utf8_decode("Categoría Programática:         ").$categoria[$indice].":".$nom_categoria[$indice],1,2,'L');
		$this->SetX(10);
		$this->Cell(250,$alto,utf8_decode("Acc. Específica:  ").$aesp[$indice]." : ".$nom_aesp[$indice],1,2,'L');
		$this->Ln();
		$this->SetX(10);
		$this->SetFont('Arial','B',7);
	}
	
	function Footer(){
		global $comp_fec_emis;
		global $indice;
		$this->Cell(260,3,utf8_decode("ESTE COMPROBANTE FUE GENERADO EL DÍA: "). trim($comp_fec_emis[$indice-1])."\n   ",0,0,'C');
	}
}

$pdf=new PDF('L','mm','Letter');

$i = 0;
$indice = 0;
while($i<sizeof($comp_id)){
	$pdf->AddPage();
	$pdf->AliasNbPages();
	$alto=4;
	$posy= $pdf->gety();
	$pdf->SetFont('Arial','B',6);
	$pdf->SetXY(10,($posy+1));
	$pdf->Cell(50,$alto,"",0,0,'l');
	$pdf->SetXY(10,($posy+1));
	$pdf->SetFont('Arial','B',9);
	$pdf->SetX(10);
	$posy= $pdf->gety();
	$pdf->Cell(24,$alto,"RENG",1,0,'C');
	$pdf->Cell(30,$alto,utf8_decode("CÓDIGO"),1,0,'C'); 
	$pdf->Cell(136,$alto,utf8_Decode("DESCRIPCIÓN"),1,0,'C');
	$pdf->Cell(30,$alto,"DEBE",1,0,'C');
	$pdf->Cell(30,$alto,"HABER",1,2,'C');
	$pdf->SetX(10);
	
	$pdf->SetFont('Arial','',8);
	for ($ii=0; $ii<$total_items[$i]; $ii++){
		$pdf->SetX(10);
		$pdf->Cell(24,$alto,trim($comp_reng[$i][$ii]),1,0,'R');	
		$pdf->Cell(30,$alto,trim($id_cta[$i][$ii]),1,0,'L');
		$pdf->Cell(136,$alto,$nom_cta[$i][$ii],1,0,'L');
		$pdf->Cell(30,$alto,(number_format($rcomp_debe[$i][$ii],2,'.',',')),1,0,'R');
		$pdf->Cell(30,$alto,(number_format($rcomp_haber[$i][$ii],2,'.',',')),1,2,'R');
	}
	
	$pdf->Ln();
	$posy= $pdf->gety();
	$pdf->SetFont('Arial','B',6);
	$pdf->SetXY( 3.5,($posy+1));
	
	$pdf->SetX(170);
	$pdf->Cell(30,$alto,"TOTAL: ",1,0,'C');
	$pdf->Cell(30,$alto,(number_format($total_debe[$i],2,'.',',')),1,0,'R');
	$pdf->Cell(30,$alto,(number_format($total_haber[$i],2,'.',',')),1,2,'R');
	$pdf->SetX( 80);
	$posy= $pdf->gety();
	$pdf->SetX( 3.5);
	$pdf->SetXY( 3.5,$posy);
	$pdf->Cell(204,8,"",0,2,'R');
	$posy= $pdf->gety();
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetX(65);
	$pdf->Cell(136,$alto,utf8_decode("IMPUTACIÓN PRESUPUESTARIA"),1,2,'C'); 
	$pdf->SetX(65);
	$pdf->SetFont('Arial','',8);
	$posy= $pdf->gety();
	$pdf->Cell(45,$alto,"PARTIDA",1,0,'C');
	$pdf->Cell(45,$alto,utf8_decode("CUENTA"),1,0,'C');
	$pdf->Cell(46,$alto,utf8_Decode("MONTO"),1,2,'C');
	
	for ($ii=0; $ii<$total_imputacion[$i]; $ii++){
		$pdf->SetX(65);
		$pdf->gety();
		$pdf->Cell(45,$alto,$matriz_sub_esp[$i][$ii],1,0,'C');
		$pdf->Cell(45,$alto,$cuenta[$i][$ii],1,0,'C');
		$pdf->Cell(46,$alto,(number_format($matriz_monto[$i][$ii],2,'.',',')),1,2,'R');
	}
	
	$pdf->Ln();
	if ($anulado==1){
		$pdf->Image('../../imagenes/anulado.jpg',210,152,46,35);
	}
	$pdf->Ln();
	$pdf->setAutoPagebreak(true,'');
	$i++;
	$indice = $i;
}
$pdf->Output();
pg_close($conexion);
?>