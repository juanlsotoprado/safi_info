<?php
ob_start ();
require ("../../../includes/conexion.php");
require ("../../../lib/fpdf/fpdf.php");
require_once ("../../../includes/fechas.php");

$codigo = $_REQUEST ['id'];

//error_log(print_r($codigo,true));
$datosGenerales = null;
$datosItems = null;

/************************************
 * Obtener datos generales del acta *
 ************************************/
$querydetalle=
"
		SELECT
			general.acta_id,
			general.observaciones,
			to_char(general.fecha_acta, 'DD/MM/YYYY') AS fecha_acta,
			item.arti_id,
			biin.bien_id,
			itemnombre.nombre,
			item.modelo,
			item.marca_id,
			item.ubicacion,
			item.precio,
			item.serial
		FROM
			sai_desincorporar general
			inner join sai_desincorporar_item item on(item.acta_id = general.acta_id)
			inner join sai_biin_items biin on(biin.clave_bien = item.arti_id)
			inner join sai_item itemnombre on(itemnombre.id = biin.bien_id)
		WHERE
			general.acta_id = '".$codigo."'
		";

if(($resultado = pg_query ($conexion, $querydetalle)) === false)
{
	echo "Error al realizar la consulta de los datos del acta";
	error_log("Error al realizar la consulta de los datos del acta \"".$codigo."\". Detalles: " + pg_last_error($conexion));
	exit;
}

$datosGenerales = array();
if ( $row = pg_fetch_array ( $resultado ) )
{
	$datosGenerales = $row;
}

/****************************************
 * Fin Obtener datos generales del acta *
 ****************************************/


/*******************************************
 * Obtener los datos de los items del acta *
 *******************************************/
$query = "
		SELECT
			general.acta_id,
			general.observaciones,
			to_char(general.fecha_acta, 'DD/MM/YYYY') AS fecha_acta,
			item.arti_id,
			biin.bien_id,
			itemnombre.nombre,
			item.modelo,
			item.marca_id,
			nombremarca.bmarc_nombre,
			item.ubicacion,
			item.precio,
			item.serial
		FROM
			sai_desincorporar general
			inner join sai_desincorporar_item item on(item.acta_id = general.acta_id)
			inner join sai_biin_items biin on(biin.clave_bien = item.arti_id)
			inner join sai_item itemnombre on(itemnombre.id = biin.bien_id)
			inner join sai_bien_marca nombremarca on(item.marca_id = nombremarca.bmarc_id)
		WHERE
			general.acta_id = '".$codigo."'
";

if(($resultado = pg_query ($conexion, $query)) === false)
{
	echo "Error al realizar la consulta de los datos de los items";
	error_log("Error al realizar la consulta de los datos de los items del acta \"".$codigo."\". Detalles: " + pg_last_error($conexion));
	exit;
}

$datosItems = array();
while ( $row = pg_fetch_array ( $resultado ) )
{
	$datosItems[] = $row;
}

/**************************************************
 * Fin de Obtener los datos de los items del acta *
 **************************************************/

class PDF extends FPDF {
	function PDF($orientation = 'L', $unit = 'mm', $format = 'Letter') {
		// Call parent constructor
		$this->FPDF ( $orientation, $unit, $format );
	}
	// Cabecera de Página
	function Header() {
		$alto = 4;
		global $datosGenerales;
		// Logo
		$this->SetX ( 50 );
		$this->SetY ( 55 );
		$this->Image ( '../../../imagenes/encabezado.jpg', 3, 22, 260, 15 );
		$this->Ln ( 3 );
		// Arial bold 15
		$this->SetFont ( 'Arial', 'B', 14 );
		// Título
		$posy = $this->gety ();
		$this->SetX ( 3.5 );
		$this->SetY ( 36 );
		$this->Cell ( 250, 15, utf8_decode ( 'ACTA DE DESINCORPORACIÓN ' ), 0, 1, 'C' );
		if ($datosGenerales["esta_id"] == 15) {
			$this->SetTextColor ( 255, 0, 0 );
			$this->Cell ( 250, 15, utf8_decode ( 'ANULADO ' ), 0, 1, 'C' );
			$this->SetTextColor ( 0, 0, 0 );
		}
		
		// Salto de línea
		$this->Ln ( 1 );
		// Movernos a la derecha
		$this->SetXY ( - 60, 45 );
		$this->SetFont ( 'Arial', 'B', 8 );
		$this->Cell ( 40, 5, utf8_decode ( "1.Nº ACTA:  " . $datosGenerales["acta_id"] ), 1, 0, 'L' );
		$this->SetXY ( - 60, 50 );
		$this->Cell ( 40, 5, "2.Fecha:  " . $datosGenerales["fecha_acta"], 1, 0, 'L' );
		
		// Salto de línea
		$this->Ln ( 1 );
		$this->SetXY ( 10, 60 );
		$this->SetFont ( 'Arial', '', 9 );
		$this->Cell ( 250, $alto, utf8_decode ( "Observaciones:  " ) . $datosGenerales["observaciones"], 1, 2, 'L' );
		$this->SetX ( 10 );
		//$this->Cell ( 250, $alto, "Proveedor:  " . $datosGenerales["rif_proveedor"] . ": " . $datosGenerales["nombre_proveedor"], 1, 2, 'L' );
		$this->SetX ( 10 );
		//$this->Cell ( 250, $alto, "Monto recibido:  " . $datosGenerales["monto_recibido"], 1, 2, 'L' );
		$this->Ln ();
		$this->SetFont ( 'Arial', 'B', 7 );
	}
	// Pie de página
	// **********************************************************************
	function Footer() {
		$this->SetX ( 53.5 );
		$this->SetFont ( 'Arial', 'B', 8 );
		// Número de página
		$this->Cell ( 0, 20, utf8_decode ( '  SAFI-Fundación Infocentro' ) . '  ' . $this->PageNo () . '/{nb}', 0, 0, 'R' );
		// $this->Cell(0,16,utf8_decode('Detalle generado el día').' '.date("d/m/y").' a las '.date("H:i:s"),0,0,'R');
	}
}

$pdf = new PDF ( 'L', 'mm', 'Letter' );
$pdf->AddPage ();
$pdf->AliasNbPages (); // Alias para el número total de página
$alto = 4;
$posy = $pdf->gety ();
$pdf->SetFont ( 'Arial', 'B', 6 );
$pdf->SetXY ( 10, ($posy + 1) );
$pdf->Cell ( 50, $alto, "", 0, 0, 'l' );
$pdf->SetXY ( 10, ($posy + 1) );
$pdf->SetFont ( 'Arial', 'B', 8 );
$pdf->SetX ( 10 );
$posy = $pdf->gety ();
$pdf->Cell ( 10, $alto, "Item", 1, 0, 'C' );
$pdf->Cell ( 20, $alto, utf8_decode ( "Código" ), 1, 0, 'C' );
$pdf->Cell ( 100, $alto, utf8_decode ( "Descripción detallada o técnica" ), 1, 0, 'C' );
$pdf->Cell ( 35, $alto, "Marca", 1, 0, 'C' );
$pdf->Cell ( 35, $alto, "Modelo", 1, 0, 'C' );
$pdf->Cell ( 25, $alto, "Precio", 1, 0, 'C' );
$pdf->Cell ( 25, $alto, "Serial", 1, 2, 'C' );

$pdf->SetFont ( 'Arial', '', 8 );

$ii = 0;
foreach ($datosItems AS $datoItem)
{	
	$pdf->SetX ( 10 );
	$pdf->Cell ( 10, $alto, (($ii++) + 1), 1, 0, 'C' );
	$pdf->Cell ( 20, $alto, trim ( $datoItem["arti_id"] ), 1, 0, 'C' );
	$pdf->Cell ( 100, $alto, $datoItem["nombre"], 1, 0, 'L' );
	$pdf->Cell ( 35, $alto, $datoItem["bmarc_nombre"], 1, 0, 'C' );
	$pdf->Cell ( 35, $alto, $datoItem["modelo"], 1, 0, 'C' );
	$pdf->Cell ( 25, $alto, $datoItem["precio"], 1, 0, 'C' );
	$pdf->Cell ( 25, $alto, $datoItem["serial"], 1, 2, 'C' );
}

$pdf->Ln ();
$posy = $pdf->gety ();
$pdf->SetFont ( 'Arial', 'B', 8 );
$pdf->SetXY ( 5.5, ($posy + 1) );

$pdf->SetXY ( 10, $posy );
$pdf->Cell ( 125, 3, utf8_decode ( "Analista de Bienes" ), 1, 0, 'c' );
// $pdf->Cell(20,3, utf8_decode("% DE RETENCIÓN"),1,0,'c');
$pdf->Cell ( 125, 3, utf8_decode ( "Coordinación de Bienes Nacionales" ), 1, 0, 'c' );

$pdf->SetXY ( 10, $posy );
$pdf->MultiCell ( 125, 5, "\n\n___________________________\n" . "Registrado por: " . 
		utf8_decode ( strtoupper ( $datosGenerales["empleado_nombres"] . " " . $datosGenerales["empleado_apellidos"] ) ) . "\n", 1, 'C', 0 );
$pdf->SetXY ( 135, $posy );
$pdf->MultiCell ( 125, 5, "\n\n___________________________\n" . "Revisado por: " . "\n", 1, 'C', 0 );
$pdf->SetXY ( 178.5, $posy );

$posy = $pdf->gety ();

$pdf->Ln ();
$pdf->Ln ();
$pdf->Ln ();
if ($datosGenerales["esta_id"] == 15) {
	$pdf->Image ( '../../../imagenes/anulado.jpg', 210, 152, 46, 35 );
}

// Se determina el nombre del archivo, se firma, se abre y se limpia temporales
// $tipo_documento=substr($codigo,0,4);
// include("../../pdf_con_firma.php");
ob_clean ();
$pdf->Output ();
// pg_close($conexion);
?> 

