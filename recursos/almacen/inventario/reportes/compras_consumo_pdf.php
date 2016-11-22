<?php
require("../../../../includes/conexion.php");
require("../../../../lib/fpdf/fpdf.php");
require_once("../../../../includes/fechas.php");

$fecha_in = trim($_GET['txt_inicio']);
$fecha_fi = trim($_GET['hid_hasta_itin']);
$buscar = trim($_GET['hid_buscar']);

// Obtener el reporte
$sql = "
	SELECT
		categoria.tp_id AS categoria_id,
		categoria.tp_desc AS categoria_descripcion,
		item.id AS item_id,
		item.nombre AS item_nombre,
		item_articulo.existencia_minima AS item_existencia_minima,
		item_articulo.unidad_medida AS item_unidad_medida,
		".($buscar == "1" ? "/*entradas.precio*/ entrada_detalle.precio AS entradas_precio," : "")."
		SUM (
			entradas.total_entradas_anteriores - salidas.total_salidas_anteriores
		) AS inventario,
		SUM (
			entradas.total_entradas_entre_fechas
		) AS total_entradas,
		SUM (
			salidas.total_salidas_entre_fechas
		) AS total_salidas,
		SUM (
			(entradas.total_entradas_anteriores - salidas.total_salidas_anteriores + entradas.total_entradas_entre_fechas)
				* entradas.precio
		) AS total_costo,
		SUM (
			entradas.total_entradas_anteriores - salidas.total_salidas_anteriores + entradas.total_entradas_entre_fechas
				- salidas.total_salidas_entre_fechas
		) AS total_inventario,
		SUM (
			/*salidas.total_salidas_entre_fechas * entradas.precio*/
			salidas.total_salidas_entre_fechas * entrada_detalle.precio
		) AS total_gastos
	FROM
		sai_arti_tipo categoria
		INNER JOIN sai_item_articulo item_articulo ON (item_articulo.tipo = categoria.tp_id)
		INNER JOIN sai_item item ON (item_articulo.id = item.id)
		INNER JOIN sai_arti_almacen entrada_detalle ON (entrada_detalle.arti_id = item.id)
		
		INNER JOIN (
			SELECT
				entrada_detalle.arti_id AS id_item,
				entrada_detalle.alm_id AS id_almacen,
				entrada_detalle.precio AS precio,
				SUM(
					CASE
						WHEN entrada_detalle.alm_fecha_recepcion < TO_DATE('".$fecha_in."', 'DD/MM/YYYY') THEN
							entrada_detalle.cantidad
						ELSE
							0
					END 
				) AS total_entradas_anteriores,
				SUM(
					CASE
						WHEN
							entrada_detalle.alm_fecha_recepcion
								BETWEEN TO_DATE('".$fecha_in."', 'DD/MM/YYYY') AND TO_DATE('".$fecha_fi."', 'DD/MM/YYYY') THEN
							entrada_detalle.cantidad
						ELSE
							0
					END
				) AS total_entradas_entre_fechas,
				SUM(entrada_detalle.cantidad) AS total_entrada
			FROM
				sai_arti_inco entrada
				INNER JOIN sai_arti_almacen entrada_detalle ON (entrada_detalle.acta_id = entrada.acta_id)
			WHERE
				entrada.esta_id <> 15
				AND entrada_detalle.alm_fecha_recepcion <= TO_DATE('".$fecha_fi."', 'DD/MM/YYYY')
			GROUP BY
				id_item,
				id_almacen,
				precio
		) AS entradas ON (entradas.id_item = entrada_detalle.arti_id AND entradas.id_almacen = entrada_detalle.alm_id)
		
		INNER JOIN (
			SELECT
				salida_detalle.alm_id AS id_almacen,
				salida_detalle.arti_id AS id_item,
				SUM(
					CASE
						WHEN salida.fecha_acta < TO_DATE('".$fecha_in."', 'DD/MM/YYYY') THEN
							CASE
								WHEN salida.tipo = 'S' THEN
									salida_detalle.cantidad
								ELSE
									salida_detalle.cantidad * -1
							END
						ELSE
							0
					END 
				) AS total_salidas_anteriores,
				SUM(
					CASE
						WHEN
							salida.fecha_acta
								BETWEEN TO_DATE('".$fecha_in."', 'DD/MM/YYYY') AND TO_DATE('".$fecha_fi."', 'DD/MM/YYYY') THEN
							CASE
								WHEN salida.tipo = 'S' THEN
									salida_detalle.cantidad
								ELSE
									salida_detalle.cantidad * -1
							END
						ELSE
							0
					END
				) AS total_salidas_entre_fechas
			FROM
				sai_arti_acta_almacen salida
				INNER JOIN sai_arti_salida salida_detalle ON (salida_detalle.n_acta = salida.amat_id)
			WHERE
				salida.esta_id <> 15
				AND salida.fecha_acta <= TO_DATE('".$fecha_fi."', 'DD/MM/YYYY')
			GROUP BY
				id_almacen,
				id_item
		) AS salidas ON (salidas.id_almacen = entrada_detalle.alm_id AND salidas.id_item = entrada_detalle.arti_id)
	GROUP BY
		categoria_id,
		categoria_descripcion,
		item_id,
		item_nombre,
		item_existencia_minima,
		item_unidad_medida
		".($buscar == "1" ? ", entradas.id_almacen, entradas_precio" : "")."
	HAVING
		SUM (entradas.total_entradas_entre_fechas) > 0
		OR SUM ( salidas.total_salidas_entre_fechas) > 0
	ORDER BY
		categoria_descripcion,
		item_nombre
		".($buscar == "1" ? ", entradas.id_almacen" : "")."
";

$resultado = pg_query($conexion, $sql);
$reporte = array();

if($resultado == false){
	echo "Error al realizar la consulta.";
	error_log(pg_last_error());
}
else {
	while($row = pg_fetch_array($resultado))
	{
		$reporte[] = $row;
	}
}
 
//Colocamos la imagen del ministerio del lado derecho
class PDF extends FPDF
{
	//Cabecera de página
	function Header() {

		$alto = 4;
		global $fecha;
		global $buscar;
		global $fecha_in;
		global $fecha_fi;
		//Logo
		$this->SetX(35);
		
		$this->Image('../../../../imagenes/encabezado.jpg',23,20,232,12);
		$this->SetX(28);
		
		$this->Ln(3);

		$this->SetFont('Arial','B',12);
		//Título
		$posy= $this->gety();

		$this->SetY(31);
		if (($_GET['hid_buscar'])==1)
		{
			$this->Cell(257, 15, utf8_decode('Balance detallado de compras y consumo del ').$fecha_in." al ".$fecha_fi,0,1,'C');
		}else{
			$this->Cell(257, 15, utf8_decode('Balance general de compras y consumo del ').$fecha_in." al ".$fecha_fi,0,1,'C');
		}

		//Datos del solicitante
		$this->SetX(8);
		$this->SetFont('Arial','B',7);

		$this->Cell(249, $alto, "", 1, 2, 'C');
		//$this->Cell(8,$alto,"# ",1,0,'C');
		$this->Cell(12, $alto, utf8_decode("Código"), 1, 0, 'L');
		$this->Cell(($buscar == 1 ? 80 : 108), $alto, utf8_decode("Artículo"), 1, 0, 'L');
		$this->Cell(13, $alto, utf8_decode("Medida"), 1, 0, 'L');
		$this->Cell(17, $alto, utf8_decode("Inventario"), 1, 0, 'L');
		$this->Cell(16, $alto, utf8_decode("Entradas"), 1, 0,'L');
		if($buscar == 1)
			$this->Cell(28, $alto, utf8_decode("Precio unitario Bs."), 1, 0, 'L');
		$this->Cell(18, $alto, utf8_decode("Total Bs."), 1, 0, 'L');
		$this->Cell(15, $alto, utf8_decode("Salidas"), 1, 0, 'L');
		$this->Cell(25, $alto, utf8_decode("Total inventario"), 1, 0, 'L');
		$this->Cell(25, $alto, utf8_decode("Total gastos Bs."), 1, 2, 'L');
	}


	//Pie de página
	//**********************************************************************

	function Footer() {

		global $user_nombre;
		$this->SetX(3.5);
		$this->SetFont('Arial','B',7);
		//Número de página
		$this->Cell(0,10,utf8_decode('  SAFI-Fundación Infocentro').'  '.$this->PageNo().'/{nb}',0,0,'R');
		$this->Cell(0,16,utf8_decode('Detalle generado el día').'  '.date("d/m/y").' a las '.date("H:i:s"),0,0,'R');
	}
	 
}


//**********************************************************************

$pdf = new PDF('L','mm','Letter');

$pdf->AddPage();
$pdf->AliasNbPages();
$alto = 4;
$posy= $pdf->gety();

$pdf->SetX(8);
$pdf->SetFont('Arial', 'B', 7);
$pri = 0;
$sub_total = 0;
$gasto_total = 0;


if(count($reporte) > 0)
{
	$pdf->SetFont('Arial', 'B', 7);
	
	$idCategoriaActual = null;
	$subtotalGastos = 0;
	$totalGastos = 0;
	
	foreach ($reporte AS $row)
	{
		
		if($idCategoriaActual != $row['categoria_id'])
		{
			if ($idCategoriaActual != null)
			{
				$pdf->SetFont('Arial', 'B', 7);
				
				$pdf->Cell(224, $alto, "Subtotal Gastos:", 1, 0, 'R');
				$pdf->Cell(25, $alto, number_format($subtotalGastos, 2, ',' ,'.'), 1, 2, 'R');
				$pdf->SetX(8);
			}
			
			$pdf->SetFont('Arial', 'B', 7);
			$pdf->Cell(249, $alto, $row['categoria_descripcion'], 1, 2, 'C');
			
			$idCategoriaActual = $row['categoria_id'];
			$subtotalGastos = 0;
		}
		
		$subtotalGastos += $row['total_gastos']; 
		$totalGastos += $row['total_gastos'];
		
		$pdf->SetFont('Arial','',7);
		//	$pdf->Cell(8,$alto,"$i",1,0,'C');
		$pdf->Cell(12, $alto, $row['item_id'], 1, 0, 'L');
		$pdf->Cell(($buscar == 1 ? 80 : 108), $alto, $row['item_nombre'], 1, 0, 'L');
		$pdf->Cell(13, $alto, mb_strtoupper($row['item_unidad_medida'], "ISO-8859-1"), 1, 0, 'L');
		$pdf->Cell(17, $alto, $row['inventario'], 1, 0, 'R');
		$pdf->Cell(16, $alto, $row['total_entradas'], 1, 0, 'R');
		if($buscar == "1")
			$pdf->Cell(28, $alto, number_format($row['entradas_precio'], 2, ',', '.'), 1, 0, 'R');
		$pdf->Cell(18, $alto, number_format($row['total_costo'], 2, ',', '.'), 1, 0, 'R');
		$pdf->Cell(15, $alto, $row['total_salidas'], 1, 0, 'R');
		$pdf->Cell(25, $alto, $row['total_inventario'], 1, 0, 'R');
		$pdf->Cell(25, $alto, number_format($row['total_gastos'], 2, ',', '.'), 1, 2, 'R');
		$pdf->SetX(8);
		
	}
	
	$pdf->SetFont('Arial', 'B', 7);
	
	$pdf->Cell(224, $alto, "Subtotal Gastos:", 1, 0, 'R');
	$pdf->Cell(25, $alto, number_format($subtotalGastos, 2, ',' ,'.'), 1, 2, 'R');
	$pdf->SetX(8);
	
	$pdf->Cell(224, $alto, "Total Gastos:", 1, 0, 'R');
	$pdf->Cell(25, $alto, number_format($totalGastos, 2, ',', '.'), 1, 2, 'R');
	$pdf->SetX(8);
	
}
	
$pdf->Ln();
$pdf->Ln();

/****************************************/

//Se determina el nombre del archivo, se firma, se abre y se limpia temporales
$tipo_documento=substr($codigo,0,4);
$pdf-> Output();
?>