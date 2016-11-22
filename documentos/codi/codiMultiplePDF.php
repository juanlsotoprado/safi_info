<?php
require("../../includes/conexion.php");
require("../../lib/fpdf/fpdf.php");
require_once("../../includes/fechas.php");
include("../../includes/funciones.php");

$codis = $_REQUEST['codis'];

$id_comp=array();
$comp_fec=array();
$comentario=array();
$fecha_emis=array();
$edo=array();
$documento=array();
$referencia=array();
$compromiso=array();
$usuario=array();
$reserva=array();
$fuente=array();
$memo_fecha=array();
$memo_contenido=array();
$memo_responsable=array();

if($codis!=""){
	$tok = strtok($codis, ",");
	$codis = "(";
	while ($tok !== false) {
	    $codis .= "'".$tok."',";
	    $tok = strtok(",");
	}
	$codis = substr($codis, 0, -1).")";
}

$sql =	"SELECT ".
			"sc.comp_id, ".
			"sc.comp_fec, ".
			"sc.comp_comen, ".
			"sc.comp_fec_emis, ".
			"sc.esta_id, ".
			"sc.comp_doc_id, ".
			"sc.nro_referencia, ".
			"sc.nro_compromiso, ".
			"sem.empl_nombres||' '||sem.empl_apellidos AS usuario, ".
			"sff.fuef_descripcion, ".
			"semm.empl_nombres||' '||semm.empl_apellidos AS memo_responsable, ".
			"sm.memo_id, ".
			"sm.memo_contenido, ".
			"sm.memo_fecha_crea ".
		"FROM ".
			"sai_codi sc ".
			"LEFT OUTER JOIN sai_docu_sopor sds ON (LOWER(TRIM(sds.doso_doc_fuente)) = LOWER(TRIM(sc.comp_id))) ".
			"LEFT OUTER JOIN sai_memo sm ON (sm.memo_id=sds.doso_doc_soport) ".
			"LEFT OUTER JOIN sai_empleado semm ON (sm.usua_login=semm.empl_cedula), ".
			"sai_empleado sem, ".
			"sai_doc_genera sdg, ".
			"sai_fuente_fin sff ".
		"WHERE ".
			"sc.comp_id IN ".$codis." AND ".
			"sdg.docg_id = sc.comp_id AND ".
			"sdg.usua_login = sem.empl_cedula AND ".
			"sff.fuef_id=sc.fte_financiamiento ".
		"ORDER BY sc.comp_fec, sc.comp_id";
$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
//echo $sql."<br><br>";
while($row=pg_fetch_array($resultado)){
	$id_comp[]=trim($row['comp_id']);
	$comp_fec[]=trim($row['comp_fec']);
	$comentario[]=trim($row['comp_comen']);
	$fecha_emis[]=cambia_esp(trim($row['comp_fec_emis']));
	$hora_emis[]=substr(trim($row['comp_fec_emis']),10);
	$edo[]=$row['esta_id'];
	$documento[]=$row['comp_doc_id'];
	$referencia[]=$row['nro_referencia'];
	$compromiso[]=$row['nro_compromiso'];
	$usuario[]=$row['usuario'];
	$fuente[]=$row['fuef_descripcion'];
	if($row['memo_id'] && $row['memo_id']!=""){
		$memo_fecha[]=$row['memo_fecha_crea'];
		$memo_contenido[]=$row['memo_contenido'];
		$memo_responsable[]=$row['memo_responsable'];
	}else{
		$memo_fecha[]="No Registrado";
		$memo_contenido[]="No Registrado";
		$memo_responsable[]="No Registrado";
	}
}

$sql_reng =	"SELECT ".
				"src.comp_id, ".
				"src.reng_comp, ".
				"src.cpat_id, ".
				"src.cpat_nombre, ".
				"src.rcomp_debe, ".
				"src.rcomp_haber, ".
				"src.rcomp_tot_db, ".
				"src.rcomp_tot_hab ".
			"FROM sai_reng_comp src, sai_codi sc ".
			"WHERE ".
				"src.comp_id IN ".$codis." AND ".
				"src.comp_id = sc.comp_id ".
			"ORDER BY sc.comp_fec, sc.comp_id, src.reng_comp";

$resultado=pg_query($conexion,$sql_reng) or die("Error al mostrar");

$comp_reng=array();
$id_cta=array();
$nom_cta=array();
$debe=array();	
$haber=array();		
$total_db=array();	
$total_haber=array();
$total_items=array();

$codiAnterior = "";
$indiceCodi=-1;
while($row=pg_fetch_array($resultado)){
	if($codiAnterior=="" || $codiAnterior!=$row['comp_id']){
		$codiAnterior = $row['comp_id'];
		$indiceCodi++;
		$comp_reng[$indiceCodi] = array();
		$id_cta[$indiceCodi] = array();
		$nom_cta[$indiceCodi] = array();
		$debe[$indiceCodi] = array();
		$haber[$indiceCodi] = array();
		$total_db[$indiceCodi] = 0;
		$total_haber[$indiceCodi] = 0;
		$total_items[$indiceCodi] = 0;
	}	
	$comp_reng[$indiceCodi][sizeof($comp_reng[$indiceCodi])] = $row['reng_comp'];
	$id_cta[$indiceCodi][sizeof($id_cta[$indiceCodi])] = $row['cpat_id'];
	$nom_cta[$indiceCodi][sizeof($nom_cta[$indiceCodi])] = $row['cpat_nombre'];
	$debe[$indiceCodi][sizeof($debe[$indiceCodi])] = $row['rcomp_debe'];
	$haber[$indiceCodi][sizeof($haber[$indiceCodi])] = $row['rcomp_haber'];
	$total_db[$indiceCodi] = $row['rcomp_tot_db'];
	$total_haber[$indiceCodi] = $row['rcomp_tot_hab'];
	$total_items[$indiceCodi] ++;
}

$sql_presupuesto =	"SELECT ".
						"s.comp_id, ".
						"s.centro_gestor, ".
						"s.centro_costo, ".
						"s.comp_fec, ".
						"s.part_id, ".
						"sc.cpat_id, ".
						"s.cadt_monto ".
					"FROM ".
						"(".
							"(SELECT ".
								"sc.comp_id, ".
								"sc.comp_fec, ".
								"spae.centro_gestor, ".
								"spae.centro_costo, ".
								"scad.part_id, ".
								"scad.cadt_monto ".
							"FROM sai_causad_det scad, sai_causado sca, sai_codi sc, sai_proyecto sp, sai_proy_a_esp spae ".
							"WHERE ".
								"sca.caus_docu_id IN ".$codis." AND ".
								"sca.esta_id<>15 AND ".
								"sca.caus_id = scad.caus_id AND ".
								"sca.caus_id = scad.caus_id AND ".
								"scad.pres_anno = SUBSTR(sc.comp_fec,0,5) AND ".
								"sca.caus_docu_id = sc.comp_id AND ".
								"scad.cadt_tipo = '1' AND ".
								"sp.proy_id = scad.cadt_id_p_ac AND ".
								"sp.pre_anno = sca.pres_anno AND ".
								"spae.proy_id = sp.proy_id AND ".
								"spae.paes_id = scad.cadt_cod_aesp AND ".
								"spae.pres_anno = sca.pres_anno) ".
							"UNION ".
							"(SELECT ".
								"sc.comp_id, ".
								"sc.comp_fec, ".
								"sae.centro_gestor, ".
								"sae.centro_costo, ".
								"scad.part_id, ".
								"scad.cadt_monto ".
							"FROM sai_causad_det scad, sai_causado sca, sai_codi sc, sai_ac_central sac, sai_acce_esp sae ".
							"WHERE ".
								"sca.caus_docu_id IN ".$codis." AND ".
								"sca.esta_id<>15 AND ".
								"sca.caus_id = scad.caus_id AND ".
								"sca.caus_id = scad.caus_id AND ".
								"scad.pres_anno = SUBSTR(sc.comp_fec,0,5) AND ".
								"sca.caus_docu_id = sc.comp_id AND ".
								"scad.cadt_tipo = '0' AND ".
								"sac.acce_id = scad.cadt_id_p_ac AND ".
								"sac.pres_anno = sca.pres_anno AND ".
								"sae.acce_id = sac.acce_id AND ".
								"sae.aces_id = scad.cadt_cod_aesp AND ".
								"sae.pres_anno = sca.pres_anno) ".
						") AS s, sai_convertidor sc ".
					"WHERE ".
						"s.part_id = sc.part_id ".
					"ORDER BY s.comp_fec, s.comp_id ";
//echo $sql_presupuesto."<br><br>";
$resultado=pg_query($conexion,$sql_presupuesto) or die("Error al mostrar");

$apde_partida=array();
$cpat_id=array();
$apde_monto=array();
$centrog=array();
$centroc=array();
$total_imputacion=array();

$codiAnterior = "";
while($row=pg_fetch_array($resultado)){
	if($codiAnterior=="" || $codiAnterior!=$row['comp_id']){
		$codiAnterior = $row['comp_id'];
		$indiceCodi=$row['comp_id'];
		$apde_partida[$indiceCodi] = array();
		$cpat_id[$indiceCodi] = array();
		$apde_monto[$indiceCodi] = array();
		$centrog[$indiceCodi] = array();
		$centroc[$indiceCodi] = array();
		$total_imputacion[$indiceCodi] = 0;
	}	
	$apde_partida[$indiceCodi][sizeof($apde_partida[$indiceCodi])] = $row['part_id'];
	$cpat_id[$indiceCodi][sizeof($cpat_id[$indiceCodi])] = $row['cpat_id'];
	$apde_monto[$indiceCodi][sizeof($apde_monto[$indiceCodi])] = $row['cadt_monto'];
	$centrog[$indiceCodi][sizeof($centrog[$indiceCodi])] = $row['centro_gestor'];
	$centroc[$indiceCodi][sizeof($centroc[$indiceCodi])] = $row['centro_costo'];
	$total_imputacion[$indiceCodi] ++;
}

$indice = 0;

class PDF extends FPDF{

	function Header(){
		$alto=4;
		global $indice;
		global $comp_fec;
		global $id_comp;
		global $comentario;
		global $fuente;
		global $documento;
		global $referencia;
		global $compromiso;

		$this->SetX(50);
		$this->SetY(65);
		$this->Image('../../imagenes/encabezado.jpg',3,22,260,15);
		$this->Ln(3);
		
	 	$this->SetFont('Arial','',8);
	 	$this->SetXY(43.5,40);
	    //N�mero de p�gina
	 //   $this->Cell(223,10,utf8_decode('Página ').$this->PageNo().' de '.'{nb}',0,0,'R');
	//    $this->SetXY(33.5,45);
	    $this->Cell(220,8,'Fecha: '.cambia_esp($comp_fec[$indice]),0,1,'R');
		$this->SetFont('Arial','B',14);
		//T�tulo
		$posy= $this->gety();
		$this->SetX(3.5);
		$this->SetY(41);
		$this->Cell(250,15,'COMPROBANTE DIARIO '.$id_comp[$indice],0,1,'C');
		
		$this->Ln(1);
		$this->Setx(10);
	 	$this->SetFont('Arial','',9);
		$this->SetX(10);
		$this->Cell(125,$alto,"Documento Asociado:  ".$documento[$indice],1,0,'L');
		$this->Cell(125,$alto,utf8_decode("Nº Referencia bancaria:  ").$referencia[$indice],1,2,'L');
        $this->SetX(10);
		$this->Cell(250,$alto,utf8_decode("Justificación:  ").$comentario[$indice],1,2,'L');
		$this->SetX(10);
		$this->Cell(250,$alto,utf8_decode("Fuente de financiamiento:  ").$fuente[$indice],1,2,'L');
		$this->SetX(10);
		$this->Cell(250,$alto,utf8_decode("Nº Compromiso: ").$compromiso[$indice],1,2,'L');

	  	$this->Ln();
		$this->SetX(10);
		$this->SetFont('Arial','B',7);
	}

	function Footer(){
		global $indice;
		global $fecha_emis;
		global $usuario;
		global $edo;
		global $memo_fecha;
		global $memo_responsable;
		global $memo_contenido;
		
		$this->SetX(10);
		$this->Cell(260,3,utf8_decode("Este comprobante fue generado el día: ").trim($fecha_emis[$indice-1]).$hora_emis[$indice-1]."\n   "."por: ".$usuario[$indice-1],0,0,'C');

		if($edo[$indice-1]==15){
			$this->SetFont('Arial','B',7);
			$this->SetX(50);
			$this->SetY(159);
			$this->Cell(260,4,"Nota: Este comprobante fue anulado el día ".$memo_fecha[$indice-1]. " por ".utf8_Decode($memo_responsable[$indice-1]),0,0,'C');
			$this->SetX(90);
			$this->SetY(167);
			$this->Cell(260,3,utf8_Decode("Justificación: ").$memo_contenido[$indice-1],0,0,'C');
		}
     $this->SetXY(53.5,-25);
	 $this->SetFont('Arial','B',8);
	 //N�mero de p�gina
	 $this->Cell(165,10,utf8_decode('SAFI-Fundación Infocentro'),0,0,'C');
	 $this->SetFont('Arial','',8);
	 $this->SetXY(53.5,-25);
    
	 $this->Cell(165,16,utf8_decode('Fecha de impresión:').'  '.actual_date(),0,0,'C');//.' a las '.date("H:i:s")
    
	}
}

$pdf=new PDF('L','mm','Letter');
$i = 0;
$indice = 0;
while($i<sizeof($id_comp)){
	$pdf->AddPage();
	$pdf->AliasNbPages();
	$alto=4;
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
	
	for($ii=0; $ii<$total_items[$indice]; $ii++){
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
	$pdf->Cell(30,$alto,(number_format($total_haber[$indice],2,'.',',')),1,2,'R');  
	$pdf->SetX( 80); 
	$pdf->Ln();
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetX(65);
	$pdf->Cell(136,$alto,utf8_decode("Imputación presupuestaria"),1,2,'C'); 
	$pdf->SetX(65);
	$pdf->SetFont('Arial','',8);
	$posy= $pdf->gety();
	
	$pdf->Cell(30,$alto,"Proyecto/Acc",1,0,'C');
	$pdf->Cell(25,$alto,utf8_decode("Acción específica"),1,0,'C');
	$pdf->Cell(27,$alto,"Partida",1,0,'C'); 
	$pdf->Cell(28,$alto,utf8_decode("Cuenta"),1,0,'C'); 
	$pdf->Cell(26,$alto,utf8_Decode("Monto"),1,2,'C');
	
	$comp_id_aux = $id_comp[$indice];
	if($total_imputacion[$comp_id_aux]){
		for ($ii=0; $ii<$total_imputacion[$comp_id_aux]; $ii++){
			$pdf->SetX(65);
			$pdf->gety();
			$pdf->Cell(30,$alto,$centrog[$comp_id_aux][$ii],1,0,'C');
			$pdf->Cell(25,$alto,$centroc[$comp_id_aux][$ii],1,0,'C');
			$pdf->Cell(27,$alto,$apde_partida[$comp_id_aux][$ii],1,0,'C');
			$pdf->Cell(28,$alto,$cpat_id[$comp_id_aux][$ii],1,0,'C');
			$pdf->Cell(26,$alto,(number_format($apde_monto[$comp_id_aux][$ii],2,'.',',')),1,2,'R');  
		}
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
$pdf->Output();
pg_close($conexion);
?>