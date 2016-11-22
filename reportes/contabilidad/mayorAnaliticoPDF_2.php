<?php
require_once(dirname(__FILE__) . '/../../init.php');
require_once (SAFI_LIB_PATH . '/general.php');
// Modelo
require_once(SAFI_MODELO_PATH. '/firma.php');

require("../../includes/reporteBasePdf.php");//
require("../../lib/fpdf/fpdf.php");
require("../../includes/conexion.php");

if (isset($_POST["hid_desde_itin"]) && isset($_POST["hid_hasta_itin"])) {
	class PDF extends FPDF {     //Cabecera de pagina
		function Header() 	{
$this->Image('../../imagenes/encabezado.jpg',5,5,260,15);
			$this->SetFont('Arial','B',10);
			$this->Sety(28);
			$this->Cell(120);
			$this->Cell(30,5,utf8_decode("MAYOR ANALÍTICO ENTRE ".$_POST["hid_desde_itin"]." Y ".$_POST["hid_hasta_itin"]),27,27,'C');
			$this->Ln(2);
			$this->SetFillColor(69,69,159);
			$this->SetTextColor(255);
			$this->SetDrawColor(128,0,0);
			$this->SetLineWidth(.1);
			$this->SetFont('Arial','B',8);
			$header=array('Fecha','Identif.','Docg.','Ref.','FF','Cta Contable','S. Inicial','Debe','Haber','S. Final','Comentario');
			//$w=array(15,25,22,20,28,18,18,18,18,90);
			$w=array(15,25,25,30,10,25,18,18,18,18,90);
			for($i=0;$i<count($header);$i++)
			$this->Cell($w[$i],5,$header[$i],1,0,'C',1);
			$this->Ln();
		}
		function Footer() {

			global $firmasSeleccionadas;
			$firmasSeleccionadas = array();
			
			$firmasSeleccionadas = SafiModeloFirma::GetFirmaByPerfiles(array('46450', '65150'));
			
			
			$this->SetY(-26);
			$this->SetFont('Arial','I',7);
			$this->Cell(0,10,utf8_decode('     '),0,0,'C');
			$this->SetY(-21);
			$this->SetFont('Arial','I',7);
			$this->Cell(0,10,utf8_decode('______________________________                       ______________________________'),0,0,'C');
			$this->SetY(-18);
			$this->SetFont('Arial','I',7);
			$this->Cell(0,10,utf8_decode('          '.$firmasSeleccionadas['46450']['nombre_empleado'].'                                                        '.utf8_encode($firmasSeleccionadas['65150']['nombre_empleado']).'      '),0,0,'C');
			$this->SetY(-15);
			$this->SetFont('Arial','I',7);
			$this->Cell(0,10,utf8_decode($firmasSeleccionadas['46450']['nombre_cargo'].'                                                                                '.utf8_encode($firmasSeleccionadas['65150']['nombre_cargo']).' '),0,0,'C');
			$this->SetY(-12);
			$this->SetFont('Arial','I',7);
			$this->Cell(0,10,$firmasSeleccionadas['46450']['nombre_dependencia'].'                                                                                     ',0,0,'C');
			$this->SetY(-15);
			$this->SetFont('Arial','I',7);
			$this->Cell(0,10,utf8_decode('SAFI-Fundación Infocentro').'  '.$this->PageNo().'/{nb}',0,0,'R');
			$this->SetY(-12);
			$this->Cell(0,10,fecha(),0,0,'R');
		}

		function PDF($orientation='P',$unit='mm',$format='letter') {
			$this->FPDF($orientation,$unit,$format);
		}

		function FancyTable($data) {
			$this->SetDrawColor(128,0,0);
			$this->SetLineWidth(.1);
			$w=array(15,25,25,30,10,25,18,18,18,18,90);
			$this->SetFillColor(224,235,255);
			$this->SetTextColor(0);
			$this->SetFont('Arial','',8);
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
					$this->Cell($w[3],$this->FontSize+0.75,$row[3],'',0,'R',$row[9]);
					$this->Cell($w[4],$this->FontSize+0.75,$row[4],'',0,'R',$row[9]);
					$this->Cell($w[5],$this->FontSize+0.75,$row[5],'',0,'R',$row[9]);
					$this->Cell($w[6],$this->FontSize+0.75,$row[6],'',0,'R',$row[9]);
					$this->Cell($w[7],$this->FontSize+0.75,$row[7],'',0,'R',$row[9]);
					$this->Cell($w[8],$this->FontSize+0.75,$row[8],'',0,'R',$row[9]);
					$this->Cell($w[9],$this->FontSize+0.75,$row[9],'',0,'L',$row[9]);
					$this->Cell($w[10],$this->FontSize+0.75,$row[10],'',0,'L',$row[9]);
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
	/*if ($mes_total!=12) {
		$mes_total=$mes_total+1;
		if ($mes_total == 1 || $mes_total == 2 || $mes_total == 3 || $mes_total == 4 || $mes_total == 5 || $mes_total == 6 || $mes_total == 7 || $mes_total == 8 || $mes_total == 9) $mes_total="0".$mes_total;		
	}*/
	/*else {
		$mes_total="01";
		$ano_total=$ano_total+1;
	}*/
	$fechaIinicio = "01/".$mes_total."/".$ano_total;
	$fechaFfin = $fecha_inicio;
	//echo "Fecha inicio:".$fechaIinicio." Fecha fin:".$fechaFfin;

	
	if ($consulta_basica==1) {
		if (isset($_POST["cuentaActb"]) && $_POST["cuentaActb"]!="" && isset($_POST["cuenta"]) && strlen($_POST["cuenta"]) > 1){
			$sql = "SELECT src.comp_id, src.reng_comp, sc.cpat_id, sc.cpat_nombre, src.rcomp_debe, src.rcomp_haber, src.rcomp_tot_db, src.rcomp_tot_hab, to_char(comp_fec, 'DD/MM/YYYY')  as fecha_emision, scd.comp_comen, scp.saldo,scd.comp_doc_id,case when (substring(comp_doc_id, 1, 4)='sopg') then (select numero_reserva as fuente from sai_sol_pago where sopg_id = comp_doc_id) when (substring(comp_doc_id, 1, 4)='codi') then (select fte_financiamiento as fuente from sai_codi where comp_id=src.comp_id) else '' end as numero_reserva, case when (substring(comp_doc_id, 1, 4)='sopg') then comp_doc_id when (substring(comp_doc_id, 1, 4)='pgch') then (select docg_id from sai_pago_cheque where pgch_id=comp_doc_id) when substring(comp_doc_id, 1, 4)='tran' then (select docg_id from sai_pago_transferencia where trans_id=comp_doc_id) end as sopg, scd.nro_referencia
				FROM sai_cue_pat sc, sai_reng_comp src , (".$sql_saldo.") scp , sai_comp_diario scd 
				where src.cpat_id = sc.cpat_id and scp.cpat_id = sc.cpat_id and scd.comp_id = src.comp_id and scd.esta_id<>'15' and sc.cpat_id = '".trim($_POST["cuenta"])."' and substring(trim(sc.cpat_id) from 16 for 17) != '00' 
				and scd.comp_fec between to_date('".$_POST["hid_desde_itin"]."', 'DD MM YYYY') and to_date('".$_POST["hid_hasta_itin"]."', 'DD MM YYYY') order by sc.cpat_id, scd.comp_fec";
		}else{
			$sql = "SELECT src.comp_id, src.reng_comp, sc.cpat_id, sc.cpat_nombre, src.rcomp_debe, src.rcomp_haber, src.rcomp_tot_db, src.rcomp_tot_hab, to_char(comp_fec, 'DD/MM/YYYY')  as fecha_emision, scd.comp_comen, scp.saldo,scd.comp_doc_id,case when (substring(comp_doc_id, 1, 4)='sopg') then (select numero_reserva as fuente from sai_sol_pago where sopg_id = comp_doc_id) when (substring(comp_doc_id, 1, 4)='codi') then (select fte_financiamiento as fuente from sai_codi where comp_id=src.comp_id) else '' end as numero_reserva, case when (substring(comp_doc_id, 1, 4)='sopg') then comp_doc_id when (substring(comp_doc_id, 1, 4)='pgch') then (select docg_id from sai_pago_cheque where pgch_id=comp_doc_id) when substring(comp_doc_id, 1, 4)='tran' then (select docg_id from sai_pago_transferencia where trans_id=comp_doc_id) end as sopg,scd.nro_referencia
				FROM sai_cue_pat sc, sai_reng_comp src,  (".$sql_saldo.") scp, sai_comp_diario scd
				where src.cpat_id = sc.cpat_id and scp.cpat_id = sc.cpat_id and scd.comp_id = src.comp_id and scd.esta_id<>'15' and substring(trim(sc.cpat_id) from 16 for 17) != '00' 
				and scd.comp_fec between to_date('".$_POST["hid_desde_itin"]."', 'DD MM YYYY') and to_date('".$_POST["hid_hasta_itin"]."', 'DD MM YYYY')
				order by sc.cpat_id, scd.comp_fec";
		}		
	}
	else {
		$login=$_SESSION['login'];
		require_once("saldoDiarioActualizadoMayorAnalitico_2.php");	

		/*Búsqueda de movimientos en las fechas registradas*/
		if (isset($_POST["cuentaActb"]) && $_POST["cuentaActb"]!="" && isset($_POST["cuenta"]) && strlen($_POST["cuenta"]) > 1){
			/*$sql = "SELECT src.comp_id, src.reng_comp, sc.cpat_id, sc.cpat_nombre, src.rcomp_debe, src.rcomp_haber, src.rcomp_tot_db, src.rcomp_tot_hab, to_char(comp_fec, 'DD/MM/YYYY')  as fecha_emision, scd.comp_comen, scp.saldo,scd.comp_doc_id,case when (substring(comp_doc_id, 1, 4)='sopg') then (select numero_reserva as fuente from sai_sol_pago where sopg_id = comp_doc_id) when (substring(comp_doc_id, 1, 4)='codi') then (select fte_financiamiento as fuente from sai_codi where comp_id=src.comp_id) else '' end as numero_reserva, case when (substring(comp_doc_id, 1, 4)='sopg') then comp_doc_id when (substring(comp_doc_id, 1, 4)='pgch') then (select docg_id from sai_pago_cheque where pgch_id=comp_doc_id) when substring(comp_doc_id, 1, 4)='tran' then (select docg_id from sai_pago_transferencia where trans_id=comp_doc_id) end as sopg, scd.nro_referencia
					FROM sai_cue_pat sc, sai_reng_comp src , (".$sql_total.") scp, sai_comp_diario scd
					where src.cpat_id = sc.cpat_id and scp.cpat_id = sc.cpat_id and scd.esta_id<>'15' and scd.comp_id = src.comp_id and sc.cpat_id = '".trim($_POST["cuenta"])."' and substring(trim(sc.cpat_id) from 16 for 17) != '00' 
					and scd.comp_fec between to_date('".$_POST["hid_desde_itin"]."', 'DD MM YYYY') and to_date('".$_POST["hid_hasta_itin"]."', 'DD MM YYYY') order by sc.cpat_id, scd.comp_fec";*/
			$sql = "SELECT 
							src.comp_id, 
							src.reng_comp, 
							sc.cpat_id, 
							sc.cpat_nombre, 
							src.rcomp_debe, 
							src.rcomp_haber, 
							src.rcomp_tot_db, 
							src.rcomp_tot_hab, 
							to_char(scd.comp_fec, 'DD/MM/YYYY') as fecha_emision, 
							scd.comp_comen, 
							scp.saldo, 
							scd.comp_doc_id, 
							case 
								when (substring(comp_doc_id, 1, 4)='sopg' and substring(scd.comp_id, 1, 4)!='codi') then 
									(select 
										numero_reserva as fuente 
									from sai_sol_pago 
									where sopg_id = comp_doc_id) 
								when (substring(scd.comp_id, 1, 4)='codi') then 
									(select 
										fte_financiamiento as fuente 
									from sai_codi 
									where comp_id=src.comp_id) else '' 
							end as numero_reserva, 
							case 
								when (substring(comp_doc_id, 1, 4)='sopg') then 
									comp_doc_id 
								when (substring(comp_doc_id, 1, 4)='pgch') then 
									(select 
										docg_id 
									from sai_pago_cheque 
									where pgch_id=comp_doc_id) 
								when substring(comp_doc_id, 1, 4)='tran' then 
									(select 
										docg_id 
									from sai_pago_transferencia 
									where trans_id=comp_doc_id) 
							end as sopg, 
							scd.nro_referencia  
						FROM
							(SELECT 
								src.comp_id, 
								src.reng_comp, 
								src.rcomp_debe, 
								src.rcomp_haber, 
								src.rcomp_tot_db, 
								src.rcomp_tot_hab,
								src.cpat_id 
							FROM sai_reng_comp src
							WHERE 
								src.comp_id IN 
									(SELECT 
										scd.comp_id
									FROM sai_comp_diario scd 
									WHERE 
										scd.esta_id<>'15' and scd.comp_fec between to_date('".$_POST["hid_desde_itin"]."', 'DD/MM/YYYY') and to_date('".$_POST["hid_hasta_itin"]."', 'DD/MM/YYYY')
									)
							) src
							INNER JOIN sai_cue_pat sc ON (src.cpat_id = sc.cpat_id)
							INNER JOIN (".$sql_total.") scp ON (sc.cpat_id = scp.cpat_id)
							INNER JOIN sai_comp_diario scd ON (scd.comp_id = src.comp_id)
						WHERE 
							src.cpat_id = '".trim($_POST["cuenta"])."' and 
							substring(src.cpat_id from 16 for 17) <> '00'
						ORDER BY sc.cpat_id, scd.comp_fec";
		}else{
			$sql = "SELECT src.comp_id, src.reng_comp, sc.cpat_id, sc.cpat_nombre, src.rcomp_debe, src.rcomp_haber, src.rcomp_tot_db, src.rcomp_tot_hab, to_char(comp_fec, 'DD/MM/YYYY')  as fecha_emision, scd.comp_comen, scp.saldo,scd.comp_doc_id,case when (substring(comp_doc_id, 1, 4)='sopg') then (select numero_reserva as fuente from sai_sol_pago where sopg_id = comp_doc_id) when (substring(comp_doc_id, 1, 4)='codi') then (select fte_financiamiento as fuente from sai_codi where comp_id=src.comp_id) else '' end as numero_reserva, case when (substring(comp_doc_id, 1, 4)='sopg') then comp_doc_id when (substring(comp_doc_id, 1, 4)='pgch') then (select docg_id from sai_pago_cheque where pgch_id=comp_doc_id) when substring(comp_doc_id, 1, 4)='tran' then (select docg_id from sai_pago_transferencia where trans_id=comp_doc_id) end as sopg, scd.nro_referencia
					FROM sai_cue_pat sc, sai_reng_comp src, (".$sql_total.") scp, sai_comp_diario scd
					where src.cpat_id = sc.cpat_id and scp.cpat_id = sc.cpat_id and scd.esta_id<>'15' and scd.comp_id = src.comp_id and scd.esta_id<>'15' and substring(trim(sc.cpat_id) from 16 for 17) != '00' 
					and scd.comp_fec between to_date('".$_POST["hid_desde_itin"]."', 'DD MM YYYY') and to_date('".$_POST["hid_hasta_itin"]."', 'DD MM YYYY')
					order by sc.cpat_id, scd.comp_fec";
		}
	}	
	$ctaAnt="0";
	$resultado_set_most_or=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");

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
					$valore='||'.'||'.ucwords(strtolower($rowor['cpat_nombre'])).'||'.'||'.'||'.'||'.'||'.'||'.'||'.$fill;
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

				/*$sopgg=substr($rowor['comp_doc_id'], 0, 1).substr($rowor['comp_doc_id'], 5);
				if(substr($rowor['comp_id'], 0, 4)=='codi'){
					$sopgg=substr($rowor['comp_doc_id'], 0, 1).substr($rowor['comp_doc_id'], 5);
				}*/
		$fte_finaciamiento="";
		/*if (substr($rowor['sopg'], 0, 4)=='sopg') { */
		/*$sql_fte= "select f.fuente_financiamiento as fuente from sai_forma_1125 f, sai_sol_pago_imputa s
		where f.form_id_p_ac=s.sopg_acc_pp and f.form_id_aesp=s.sopg_acc_esp
		and s.sopg_id= '".$rowor['sopg']."'";*/
		/*$sql_fte= "select numero_reserva as fuente from sai_sol_pago where sopg_id= '".$rowor['sopg']."'";	
		}
		else {
			$sql_fte="select fte_financiamiento as fuente from sai_codi where comp_id='".$rowor['comp_id']."'";
		}
		$resultado_fte=pg_query($conexion,$sql_fte) or die("Error al consultar la fuente de financiamiento");
		
		if ($row_fte=pg_fetch_array($resultado_fte)) $fte=$row_fte['fuente'];*/
		$fte=$rowor['numero_reserva'];
		if (strlen($rowor['sopg'])>2) $sopg=$rowor['sopg'];
		else $sopg="";					
		$valore=$fechaEmision.'||'.$rowor['comp_id'].'||'.$sopg.'||'.$rowor['nro_referencia'].'||'.$fte.'||'.$rowor['cpat_id'].'||'.$saldoInicial.'||'.$saldoDebe.'||'.$saldoHaber.'||'.$saldoFinal.'||'.ucwords(strtolower(substr($rowor['comp_comen'], 0, 60)));

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
}	
	?>