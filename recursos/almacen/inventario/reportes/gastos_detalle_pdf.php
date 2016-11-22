<?php

require_once(dirname(__FILE__) . '/../../../../init.php');
require_once(SAFI_MODELO_PATH . '/item.php');
require_once(SAFI_INCLUDE_PATH . '/conexion.php');
require(SAFI_LIB_PATH . "/fpdf/fpdf.php");
require_once(SAFI_INCLUDE_PATH . "/fechas.php");

$paramFechaInicio = trim($_GET['fechaInicio']);
$paramFechaFin = trim($_GET['fechaFin']);
$paramIdDependencia = trim($_GET['idDependencia']);
$idsMateriales = $_GET['idsMateriales'];
$paramIdCategoria = trim($_GET['idCategoria']);

$nombreCategoria = "";
if($paramIdCategoria != null && $paramIdCategoria != "" && $paramIdCategoria != "0")
{
	$query = "
		SELECT
			categoria.tp_desc
		FROM
			sai_arti_tipo categoria
		WHERE
			categoria.tp_id='" . $paramIdCategoria . "'
	";
	
	$resultado = pg_query($conexion, $query);
	
	if($resultado === false){
		echo "Error al realizar la consulta.";
		error_log(pg_last_error());
	} else {
		while($row = pg_fetch_array($resultado))
		{
			$nombreCategoria = $row["tp_desc"];
		}
	}
}

$nombreItem = "";
if($idsMateriales != null && is_array($idsMateriales) && count($idsMateriales) > 0)
{
	
	$materiales = SafiModeloItem::GetItemsByIds($idsMateriales);
	
	if(is_array($materiales)){
		reset($materiales);
		$nombreItem = current($materiales)->GetNombre();
	}
}

$whereInterno = "";
if($idsMateriales !== null && is_array($idsMateriales) && count($idsMateriales) > 0)
{
	$whereInterno = "AND item.id IN ('".implode("', '", $idsMateriales)."')";
}

$whereExterno = "";
if($paramIdDependencia !== null && $paramIdDependencia != ""  && $paramIdDependencia != "0")
{
	$whereExterno = "AND dependencia.depe_id = '".$paramIdDependencia."'";
}
if($paramIdCategoria !== null && $paramIdCategoria != ""  && $paramIdCategoria != "0")
{
	$whereExterno .= "AND categoria.tp_id = '".$paramIdCategoria."'";
}
	     
$query = "
	SELECT
		salidas.id_acta,
		salidas.fecha_acta,
		TO_CHAR(salidas.fecha_acta, 'DD/MM/YYYY') AS fecha_acta_str,
		dependencia.depe_id AS depe_entregada,
		salidas.arti_id,
		salidas.cantidad,
		salidas.precio,
		salidas.nombre_item,
		salidas.tipo,
		dependencia.depe_nombre AS nombre_dependencia,
		dependencia.depe_nombrecort AS nombre_corto_dependencia,
		categoria.tp_id AS id_categoria,
		categoria.tp_desc AS nombre_categoria
	FROM
		(
				SELECT
					salida.amat_id AS id_acta,
					salida.fecha_acta,
					salida.depe_entregada AS id_dependencia,
					salida_detalle.arti_id,
					salida_detalle.cantidad,
					entrada_detalle.precio,
					item.nombre AS nombre_item,
					salida.tipo,
					item_articulo.tipo AS id_categoria
				FROM
					sai_arti_acta_almacen salida
					INNER JOIN sai_arti_salida salida_detalle ON (salida_detalle.n_acta = salida.amat_id)
					INNER JOIN sai_item_articulo item_articulo ON (item_articulo.id = salida_detalle.arti_id)
					INNER JOIN sai_item item ON (item.id = item_articulo.id)
					INNER JOIN sai_arti_almacen entrada_detalle
						ON (entrada_detalle.alm_id = salida_detalle.alm_id AND entrada_detalle.arti_id = salida_detalle.arti_id)
				WHERE
					salida.esta_id <> 15
					AND salida.entregado_a != '-1'
					AND salida.fecha_acta BETWEEN
						TO_DATE('".$paramFechaInicio."', 'DD/MM/YYYY')
							AND TO_TIMESTAMP('".$paramFechaFin." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
					".$whereInterno."
					
			UNION
			
				SELECT
					salida.amat_id,
					salida.fecha_acta,
					CASE
						WHEN (item_articulo.tipo = '1') THEN '450' 
						WHEN (item_articulo.tipo = '2') THEN '550' 
						WHEN (item_articulo.tipo = '7' or item_articulo.tipo = '11') THEN '453' 
						WHEN (item_articulo.tipo = '8') THEN '600'
						WHEN (item_articulo.tipo = '3') THEN '250'
						--ELSE '450'
					END AS id_dependencia,	
					salida_detalle.arti_id,
					salida_detalle.cantidad,
					entrada_detalle.precio,
					item.nombre,
					salida.tipo,
					item_articulo.tipo AS id_categoria
				FROM
					sai_arti_acta_almacen salida
					INNER JOIN sai_arti_salida salida_detalle ON (salida_detalle.n_acta = salida.amat_id)
					INNER JOIN sai_item_articulo item_articulo ON (item_articulo.id = salida_detalle.arti_id)
					INNER JOIN sai_item item ON (item.id = item_articulo.id)
					INNER JOIN sai_arti_almacen entrada_detalle
						ON (entrada_detalle.alm_id = salida_detalle.alm_id AND entrada_detalle.arti_id = salida_detalle.arti_id)
				WHERE
					salida.esta_id <> 15
					AND salida.entregado_a = '-1'
					AND salida.fecha_acta BETWEEN
						TO_DATE('".$paramFechaInicio."', 'DD/MM/YYYY')
							AND TO_TIMESTAMP('".$paramFechaFin." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
					".$whereInterno."
		)AS salidas
		INNER JOIN sai_arti_tipo categoria ON (categoria.tp_id = salidas.id_categoria)
		INNER JOIN sai_dependenci dependencia ON (dependencia.depe_id = salidas.id_dependencia)
	WHERE
		salidas.id_dependencia IS NOT NULL
		".$whereExterno."
 	ORDER BY
 		".($paramIdDependencia == null || $paramIdDependencia == "" || $paramIdDependencia == '0' 
			? "dependencia.depe_nombre" : "categoria.tp_desc").",
 		salidas.nombre_item,
 		salidas.fecha_acta
";

$resultado = pg_query($conexion, $query);
$reporte = array();

if($resultado === false){
	echo "Error al consultar el reporte.";	
	error_log(pg_last_error());
} else {
	while($row = pg_fetch_array($resultado))
	{
		$reporte[] = $row;
	}
}

class PDF extends FPDF
{
	// Cabecera de página
	function Header() 
	{
		$alto=4;
		GLOBAL $paramFechaInicio;
		GLOBAL $paramFechaFin;
		GLOBAL $paramIdDependencia;
		GLOBAl $nombreItem;
		GLOBAL $paramIdCategoria;
		GLOBAL $nombreCategoria;
		GLOBAL $reporte;

		//Logo
		$this->SetX(35);
		$this->Image('../../../../imagenes/encabezado.jpg', 3, 20, 190, 12);
		$this->Ln(3);
		
		$this->SetFont('Arial', 'B', 12);
		//Título
	  	$titulo = utf8_decode("GASTOS POR DEPENDENCIA DEL ") . $paramFechaInicio . utf8_decode(" AL ") . $paramFechaFin;
		$posy= $this->gety();
		$this->SetX(8);
		$this->SetY(31);
		$this->Cell(190, 15, $titulo, 0, 1, 'C');
		$this->SetX(8);
		
		if($nombreCategoria != null && $nombreCategoria != "")
		{
			$this->SetY(36);
			$this->Cell(190, 15, utf8_decode(" DE ") . $nombreCategoria, 0, 1, 'C');
		}
		if($nombreItem != null && $nombreItem != "")
		{
			$this->SetY(36);
			$this->Cell(190, 15, utf8_decode(" DE ") . $nombreItem, 0, 1, 'C');
		}
		
		// Cabecera de la tabla
		$this->SetX(8);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(190, $alto, "", 1, 2, 'C');
		$this->Cell(24, $alto, utf8_decode("Acta"), 1, 0, 'C');
		$this->Cell(22, $alto, utf8_decode("Fecha"), 1, 0, 'L');
		$this->Cell(80, $alto, utf8_decode("Artículo"), 1, 0, 'L');
		$this->Cell(17, $alto, utf8_decode("Cantidad"), 1, 0, 'L');
		$this->Cell(28, $alto, utf8_decode("Costo unitario"), 1, 0, 'L');
		$this->Cell(19, $alto, utf8_decode("Total Bs."), 1, 2, 'L');
	}
	
	//Pie de página
	function Footer()
	{
		$this->SetX(53.5);
		$this->SetFont('Arial','B',8);
		//Número de página
		$this->Cell(0,10,utf8_decode('  SAFI-Fundación Infocentro').'  '.$this->PageNo().'/{nb}',0,0,'R');
		$this->Cell(0,16,utf8_decode('Detalle generado el día').'  '.date("d/m/y").' a las '.date("H:i:s"),0,0,'R');
	} 
}		 

$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();  
$alto = 4;
$posy= $pdf->gety();

$pdf->SetXY(12,$posy); 
$pdf->SetX(8);
$pdf->SetFont('Arial', '', 9);

$depe_anterior="";
$montoTotalItem = 0;
$montoSubtotal = 0;
$montoTotal = 0;
$cont=0;
$clasificacion='';
$pri=0;

foreach ($reporte AS $row)
{
	$clasificacion_actual = $row['nombre_categoria'];
	$depen = $row['depe_entregada'];
	$monto = $row['precio'];
	
	if (($paramIdDependencia <> '0') && ($clasificacion <> $clasificacion_actual))
	{
		$pdf->SetFont('Arial','B',9);
		if (($pri <> 0) && ($paramIdCategoria == '0'))
		{
			$pdf->Cell(171, $alto, "Sub-total:", 1, 0, 'R');
			$pdf->Cell(19, $alto, number_format($montoSubtotal, 2, ',', '.'), 1, 2, 'R');
			$pdf->SetX(8); 
		}
		$pdf->Cell(190,$alto, mb_strtoupper($row['nombre_categoria'], "ISO-8859-1"), 1, 2, 'C');
		$pdf->SetX(8);
		$montoSubtotal = 0;
		$clasificacion = $clasificacion_actual;
		$pdf->SetFont('Arial', '', 9);
	}
	
	if (($paramIdDependencia == '0') && ($depen <> $depe_anterior))
	{
		$pdf->SetFont('Arial', 'B', 9);
		
		if ($pri <> 0)
		{
			$pdf->Cell(171, $alto, "Sub-total:", 1, 0, 'R');
			$pdf->Cell(19, $alto, number_format($montoSubtotal, 2, ',', '.'), 1, 2, 'R');
			$pdf->SetX(8);
			$montoSubtotal = 0;
		}
		
		$pdf->Cell(190, $alto, mb_strtoupper($row['nombre_dependencia'], "ISO-8859-1"), 1, 2, 'C');
		$depe_anterior = $depen;
		$pdf->SetFont('Arial', '', 9);
	}
	
	$pdf->Cell(24, $alto, $row['id_acta'], 1, 0, 'C');
	$pdf->Cell(22, $alto, $row['fecha_acta_str'], 1, 0, 'C');
	$pdf->Cell(80, $alto, $row['nombre_item'], 1, 0, 'L');
	
	if(trim($row['tipo']) == 'D')
	{ 
		$cantidad = $row['cantidad'] * -1;
	} else {
		$cantidad = $row['cantidad'];
	}
	
	$pdf->Cell(17, $alto, $cantidad, 1, 0, 'C');
	$pdf->Cell(28, $alto, str_replace('.', ',', $monto), 1, 0, 'R');
	
	$montoTotalItem = $monto * $cantidad;
	$montoSubtotal += $montoTotalItem;
	$montoTotal += $montoTotalItem;
	
	$pri++;
	$pdf->Cell(19, $alto, number_format($montoTotalItem, 2, ',', '.'), 1, 2, 'R');
	$pdf->SetX(8);
}
		   
$pdf->Cell(171, $alto, "Sub-total:", 1, 0, 'R');
$pdf->Cell( 19, $alto, number_format($montoSubtotal, 2, ',', '.'), 1, 2, 'R');
$pdf->SetX(8);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(100, $alto, "Total:", 1, 0, 'L');
$pdf->Cell(90, $alto, number_format($montoTotal, 2, ',', '.'), 1, 2, 'R');
$pdf->SetX(8);
$pdf->Ln(5);

$tipo_documento = substr($codigo, 0, 4);
$pdf-> Output();
?>