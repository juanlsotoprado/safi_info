<?php
require_once(dirname(__FILE__) . '/../../init.php');
require_once (SAFI_LIB_PATH . '/general.php');
// Modelo
require_once(SAFI_MODELO_PATH. '/firma.php');

require("../../includes/reporteBasePdf.php");
require("../../lib/fpdf/fpdf.php");
require("../../includes/conexion.php");
require("../../includes/constantes.php");

$tipoReporte = "0";
if (isset($_REQUEST['tipoReporte']) && $_REQUEST['tipoReporte'] != "") {
	$tipoReporte = $_REQUEST['tipoReporte'];
}

$fecha = "";
if (isset($_REQUEST['fecha']) && $_REQUEST['fecha'] != "") {
	$fecha = $_REQUEST['fecha'];
}

$nivel = BALANCE_GENERAL_NIVEL_TODOS;
if (isset($_REQUEST['nivel']) && $_REQUEST['nivel'] != "") {
	$nivel = $_REQUEST['nivel'];
}

class PDF extends FPDF{

	function Header(){
		global $fecha;
		global $tipoReporte;
		global $fecha_inicio;
		global $dia;
		global $mes;
		global $ano;
		global $ano_antes;
		global $max_mes;
		global $max_mes_antes;
		global $error;
		global $consulta_basica;
		global $nivel;
		global $condicion;
		//Logo
		$this->Sety(18); //normal
		$this->Image('../../imagenes/encabezado.jpg',3,4,210,15); //normal y desconfiguracion

		$this->SetFont('Arial','B',10);
		//Movernos a la derecha

		$this->Sety(16);
		$this->Setx(100);
		$this->Cell(22,10,utf8_decode("FUNDACIÓN INFOCENTRO G-20007728-0"),27,27,'C');

		//Salto de linea
		$this->Sety(20);
		$this->Setx(100);
		$this->Cell(22,10,utf8_decode((($tipoReporte=="0")?"BALANCE GENERAL":"ESTADO DE RESULTADOS")),27,27,'C');
		
		//Salto de linea
		$this->Sety(24);
		$this->Setx(100);
		$this->Cell(22,10,utf8_decode((($tipoReporte=="0")?"AL ":"DESDE EL 01/01/".$ano." AL ").$fecha),27,27,'C');
		
		//Salto de linea
		$this->Sety(34);
		$this->SetFillColor(69,69,159);
		$this->SetTextColor(255);
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.1);
		$this->SetFont('Arial','B',8);

		//Cabecera
		$header=array(utf8_decode('Código'),utf8_decode('Descripción'),'Saldo actual');
		$w=array(30,112,60);
		for($i=0;$i<count($header);$i++){
			$this->Cell($w[$i],5,$header[$i],1,0,'C',1);
		}
		$this->Ln();
	}

	function Footer(){
		global $firmasSeleccionadas;
		$firmasSeleccionadas = array();
		$firmasSeleccionadas = SafiModeloFirma::GetFirmaByPerfiles(array('46450', '65150'));

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

	function PDF($orientation='P',$unit='mm',$format='Letter'){
		$this->FPDF($orientation,$unit,$format);
	}

	function FancyTable($datas){
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.1);

		//$w=array(30,90,10,12,10,10,10,10,10,10);
		$w=array(30,112,10,50);
			
		//Restauración de colores y fuentes
		$this->SetTextColor(0);
		$this->SetFont('Arial');

		//Datos
		$xx=10;
		$yy=30;
		$pagina=2;

		$nombreAnterior="";
		foreach($datas as $row){
			$this->setx($xx);
			if($row[4]==1||$row[4]==2||$row[4]==3||$row[4]==4){
				$this->SetTextColor(0);
				$this->SetFont('Arial','B',8);
			}else{
				$this->SetTextColor(0);
				$this->SetFont('Arial','',7);
			}
			$this->Cell($w[0],$this->FontSize+0.75,$row[0],'',0,'R');
			$this->Cell($w[1],$this->FontSize+0.75,$row[1],'',0,'L');
			$this->Cell($w[2],$this->FontSize+0.75,$row[2],'',0,'R');
			if($row[4]==7){
				$this->Cell($w[3],$this->FontSize+0.75,$row[3],'',0,'R');
				/*$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
				$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
				$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
				$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
				$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
				$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');*/
			}else{
				//$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
				if($row[4]==6){
					$this->Cell($w[3],$this->FontSize+0.75,$row[3],'',0,'R');
					/*$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
					$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
					$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
					$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
					$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');*/
				}else{
					//$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
					if($row[4]==5){
						$this->Cell($w[3],$this->FontSize+0.75,$row[3],'',0,'R');
						/*$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
						$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
						$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
						$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');*/
					}else{
						//$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
						if($row[4]==4){
							$this->Cell($w[3],$this->FontSize+0.75,$row[3],'',0,'R');
							/*$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
							$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
							$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');*/
						}else{
							//$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
							if($row[4]==3){
								$this->Cell($w[3],$this->FontSize+0.75,$row[3],'',0,'R');
								/*$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
								$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');*/
							}else{
								//$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
								if($row[4]==2){
									$this->Cell($w[3],$this->FontSize+0.75,$row[3],'',0,'R');
									/*$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');*/
								}else{
									//$this->Cell($w[4],$this->FontSize+0.75,'','',0,'R');
									if($row[4]==1){
										$this->Cell($w[3],$this->FontSize+0.75,$row[3],'',0,'R');
									}
								}
							}
						}
					}
				}
			}
			$this->Ln();
		}
		$this->Cell(array_sum($w),0,'','T');
	}
}

$fecha_inicio = $_POST["fecha"];
$dia = substr($fecha_inicio, 0, 2);
$mes = substr($fecha_inicio, 3, 2)+1-1;
$ano = substr($fecha_inicio, 6, 4);
$ano_antes = $ano-1;
$max_mes = 0;
$max_mes_antes = 0;	
$error="0";
$consulta_basica=0;
$nivel = $_POST["nivel"]; 
$condicion= "1==1";

$pdf=new PDF('P','mm','Letter');
$pdf->SetAutoPageBreak(true,38);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',8);
//$pdf->Ln();

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
		} else {
			$max_mes_antes=$nro;
			$sql_saldo= "select cpat_id, saldo from safi_saldo_contable where mes=".$max_mes_antes." and ano=".$ano_antes;
		}
	} else {
		$max_mes=$row["mes"];
		$sql_saldo= "select cpat_id, saldo from safi_saldo_contable where mes=".$max_mes." and ano=".$ano;
	}
} else {
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
} else {
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
		$sql_or="	SELECT 
						substring(trim(sc.cpat_id) from 1 for 2), 
						sc.cpat_id as cpat_id, 
						sc.cpat_nombre as cpat_nombre, 
						sc.cpat_nivel as cpat_nivel,
						scs.saldo as saldo
					FROM sai_cue_pat sc, (".$sql_saldo.") scs 
					WHERE sc.cpat_id = scs.cpat_id
					ORDER BY sc.cpat_id ";
	} else {
		$login=$_SESSION['login'];
		require_once("saldoDiarioActualizadoGenericas.php");

		/*Búsqueda de movimientos en las fechas registradas*/
		$sql_or="	SELECT 
						substring(trim(sc.cpat_id) from 1 for 2), 
						sc.cpat_id as cpat_id, 
						sc.cpat_nombre as cpat_nombre, 
						sc.cpat_nivel as cpat_nivel,
						scs.saldo as saldo
					FROM sai_cue_pat sc, (".$sql_total.") scs 
					WHERE 
						sc.cpat_id = scs.cpat_id
					ORDER BY sc.cpat_id ";
	}

	$resultado_set_most_or=pg_query($conexion,$sql_or) ;

	$cuentaDatos = 0;
	while($rowor=pg_fetch_array($resultado_set_most_or)){
		if (floatval($rowor['saldo']) > 0.009 OR floatval($rowor['saldo']) < -0.009) {
		$nivelCuenta=$rowor['cpat_nivel'];
		$clase="class='normal'";
		$fondo_str="";
		$ancho = '';
		switch ($nivelCuenta) {
			case 1:
				$espaciado = "                                                                 ";
				$espaciado_nombre = "";
				$ancho = "style='width:190px;float: left;'";
				$fondo_str="bgcolor='#E4E4E4'";
				$clase="class='normalNegrita'";
				break;
			case 2:
				$espaciado = "                                                            ";
				$espaciado_nombre = "   ";
				$ancho = "style='width:160px;float: left;'";
				break;
			case 3:
				$espaciado = "                                                       ";
				$espaciado_nombre = "      ";
				$ancho = "style='width:130px;float: left;'";
				break;
			case 4:
				$espaciado = "                                                  ";
				$espaciado_nombre = "         ";
				$ancho = "style='width:100px;float: left;'";
				break;
			case 5:
				$espaciado = "                                             ";
				$espaciado_nombre = "           ";
				$ancho = "style='width:70px;float: left;'";
				break;
			case 6:
				$espaciado = "                                        ";
				$espaciado_nombre = "              ";
				$ancho = "style='width:40px;float: left;'";
				break;
			case 7:
				$espaciado = "                                   ";
				$espaciado_nombre = "                 ";
				$ancho = "style='width:10px;float: left;'";
				break;
		}
		if($rowor['cpat_id']=='2.0.0.00.00.00.00') 	$pasivo = $rowor['saldo'];
		if($rowor['cpat_id']=='3.0.0.00.00.00.00') 	$patrimonio = $rowor['saldo'];
		if($rowor['cpat_id']=='5.0.0.00.00.00.00') 	$ingresos = $rowor['saldo'];
		if($rowor['cpat_id']=='6.0.0.00.00.00.00') 	$gastos = $rowor['saldo'];
		$subtotal_pasivo_patrimonio = $pasivo + $patrimonio;
		$resultados = $ingresos - $gastos;
		$total = $subtotal_pasivo_patrimonio - $resultados;

		/*if ($nivel == BALANCE_GENERAL_NIVEL_UNO) $condicion = "return (substr('".$rowor['cpat_id']."',2,15) == '0.0.00.00.00.00');";
		 else if ($nivel == BALANCE_GENERAL_NIVEL_DOS) $condicion = "return (substr('".$rowor['cpat_id']."',2,15) == '0.0.00.00.00.00' || substr('".$rowor['cpat_id']."',4,13) == '0.00.00.00.00' || substr('".$rowor['cpat_id']."',6,11) == '00.00.00.00' || substr('".$rowor['cpat_id']."',15,2) != '00');";
		 else if ($nivel == BALANCE_GENERAL_NIVEL_TRES) $condicion = "return (substr('".$rowor['cpat_id']."',2,15) == '0.0.00.00.00.00' || substr('".$rowor['cpat_id']."',6,11) == '00.00.00.00' || substr('".$rowor['cpat_id']."',9,8) == '00.00.00' || substr('".$rowor['cpat_id']."',15,2) != '00');";
		 else if ($nivel == BALANCE_GENERAL_NIVEL_CUATRO) $condicion = "return (substr('".$rowor['cpat_id']."',2,15) == '0.0.00.00.00.00' || substr('".$rowor['cpat_id']."',4,13) == '0.00.00.00.00' || substr('".$rowor['cpat_id']."',9,8) == '00.00.00' || substr('".$rowor['cpat_id']."',15,2) != '00');";
		 else $condicion = "return (1==1);";		*/
		if ($nivel == BALANCE_GENERAL_NIVEL_UNO) $condicion = "return (substr('".$rowor['cpat_id']."',2,15) == '0.0.00.00.00.00');";
		else if ($nivel == BALANCE_GENERAL_NIVEL_DOS) $condicion = "return (substr('".$rowor['cpat_id']."',4,13) == '0.00.00.00.00');";
		else if ($nivel == BALANCE_GENERAL_NIVEL_TRES) $condicion = "return (substr('".$rowor['cpat_id']."',6,11) == '00.00.00.00');";
		else if ($nivel == BALANCE_GENERAL_NIVEL_CUATRO) $condicion = "return (substr('".$rowor['cpat_id']."',9,8) == '00.00.00');";
		else if ($nivel == BALANCE_GENERAL_NIVEL_CINCO) $condicion = "return (substr('".$rowor['cpat_id']."',12,5) == '00.00');";
		else if ($nivel == BALANCE_GENERAL_NIVEL_SEIS) $condicion = "return (substr('".$rowor['cpat_id']."',15,2) == '00');";
		else $condicion = "return (1==1);";

		if( $tipoReporte=="0" && substr($rowor['cpat_id'],0,1)!='5' && substr($rowor['cpat_id'],0,1)!='6' && eval($condicion)==1) {
			if ( $nivelCuenta == 1 ) {
				$nombreCuenta = $espaciado_nombre.strtoupper($rowor['cpat_nombre']);
			} else {
				$nombreCuenta = $espaciado_nombre.ucwords(strtolower($rowor['cpat_nombre']));
			}
			$valore=$rowor['cpat_id'].'||'.$nombreCuenta.'||'.''.'||'./*$espaciado.*/((number_format($rowor['saldo'],2,',','.')=="-0,00")?"0,00":(number_format($rowor['saldo'],2,',','.'))).'||'.$nivelCuenta;
			$data[$cuentaDatos]= explode("||",$valore);
			$cuentaDatos=$cuentaDatos+1;
		} else if( $tipoReporte=="1" && (substr($rowor['cpat_id'],0,1)=='5' || substr($rowor['cpat_id'],0,1)=='6') && eval($condicion)==1) {
			if ( $nivelCuenta == 1 ) {
				$nombreCuenta = $espaciado_nombre.strtoupper($rowor['cpat_nombre']);
			} else {
				$nombreCuenta = $espaciado_nombre.ucwords(strtolower($rowor['cpat_nombre']));
			}
			$valore=$rowor['cpat_id'].'||'.$nombreCuenta.'||'.''.'||'./*$espaciado.*/((number_format($rowor['saldo'],2,',','.')=="-0,00")?"0,00":(number_format($rowor['saldo'],2,',','.'))).'||'.$nivelCuenta;
			$data[$cuentaDatos]= explode("||",$valore);
			$cuentaDatos=$cuentaDatos+1;
		}
		//$data[$cuentaDatos]= explode("||",$valore);
		//$cuentaDatos=$cuentaDatos+1;
		}
	}

	$pdf->FancyTable($data);
	$pdf->Ln(2);
	$pdf->SetFont('Arial','B',9);

	if( $tipoReporte=="0") {
		$pdf->SetX(16);		
		$pdf->Cell(0,10,"Sub Total Pasivo y Patrimonio Bs.F ". number_format($subtotal_pasivo_patrimonio,2,',','.'));
		$pdf->SetX(16);
		$pdf->Cell(0,20,"Resultados del mes Bs.F ". number_format($resultados,2,',','.'));
		$pdf->SetX(16);
		$pdf->Cell(0,30,"Total Pasivo y Patrimonio: Bs.F ". number_format($subtotal_pasivo_patrimonio + $resultados,2,',','.'));
		$pdf->Ln();
		//$pdf->Cell(40,10,"Total Pasivo y Patrimonio: Bs.F.  ". number_format($total,2,',','.'));
	} else {
		$pdf->Cell(40,10,"Resultados del mes: Bs.F.  ".number_format($resultados,2,',','.'));
		$pdf->Ln();		
	}
	$pdf->Output();
}
pg_close($conexion);
?>