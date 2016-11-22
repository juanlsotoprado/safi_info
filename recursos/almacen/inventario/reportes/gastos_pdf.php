<?php

require_once(dirname(__FILE__) . '/../../../../init.php');
require_once(SAFI_MODELO_PATH . '/item.php');
require_once(SAFI_INCLUDE_PATH . "/conexion.php");
require_once(SAFI_INCLUDE_PATH . "/fechas.php");
require_once(SAFI_LIB_PATH . "/fpdf/fpdf.php");

$paramFechaInicio = trim($_GET['fechaInicio']);
$paramFechaFin = trim($_GET['fechaFin']);
$paramIdDependencia = trim($_GET['idDependencia']);
$paramIdCategoria = trim($_GET['idCategoria']);
$idsMateriales = $_GET['idsMateriales'];

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
		//echo "Error al realizar la consulta.";
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

//Colocamos la imagen del ministerio del lado derecho
class PDF extends FPDF
{
	//Cabecera de página
	function Header()
	{
		$alto=4;
		global $paramFechaInicio;
		global $paramFechaFin;
		global $paramIdDependencia;
		global $nombreItem;
		global $paramIdCategoria;
		global $nombreCategoria;
		
		//Logo
		$this->SetX(35);
		$this->Image('../../../../imagenes/encabezado.jpg',3,20,190,12);
		$this->Ln(3);
		
		$this->SetFont('Arial','',8);
		$this->SetXY(43.5,40);
		//Número de página
		$this->Cell(223,10,utf8_decode('Página ').$this->PageNo().' de '.'{nb}',0,0,'R');
		$this->SetXY(33.5,45);
		$this->Cell(230,8,'Fecha: '.cambia_esp($comp_fec),0,1,'R');
		$this->SetFont('Arial','B',12);
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
		
		// Datos del solicitante
		$this->SetX(35);
		$this->SetFont('Arial','B',8);
		$this->Cell(132,$alto,"",1,2,'C');
		$this->Cell(100,$alto,"Dependencia",1,0,'L');
		$this->Cell(32,$alto,"Monto total en Bs.",1,2,'R');
	}
	
	//Pie de página
	//**********************************************************************
	
	function Footer()
	{
		global $user_nombre;
		//$this->SetX(53.5);
		//$this->SetFont('Arial','B',8);
		//Número de página
		//$this->Cell(0,10,utf8_decode('  SAFI-Fundación Infocentro').'  '.$this->PageNo().'/{nb}',0,0,'R');
		//$this->Cell(0,16,utf8_decode('Detalle generado el día').'  '.date("d/m/y").' a las '.date("H:i:s"),0,0,'R');
		
		$this->SetXY(53.5,-25);
		$this->SetFont('Arial','B',8);
		//Número de página
		$this->Cell(105,10,utf8_decode('SAFI-Fundación Infocentro'),0,0,'C');
		$this->SetFont('Arial','',8);
		$this->SetXY(53.5,-25);
		
		$this->Cell(105,16,utf8_decode('Fecha de impresión:').'  '.actual_date(),0,0,'C');//.' a las '.date("H:i:s")
	}
}

//**********************************************************************
//$pdf=new PDF('P','mm','A4');
$pdf=new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();  
$alto=4;
$posy= $pdf->gety();
  
$pdf->SetXY(12,$posy); 

$pdf->SetX(35);
$pdf->SetFont('Arial','',10);

$whereInterno = "";
if($paramIdCategoria !== null && $paramIdCategoria != ""  && $paramIdCategoria != "0")
{
	$whereInterno = "AND categoria.tp_id = '".$paramIdCategoria."'";
}
if($idsMateriales !== null && is_array($idsMateriales) && count($idsMateriales) > 0)
{
	$whereInterno .= "AND item.id IN ('".implode("', '", $idsMateriales)."')";
}

$whereExterno = "";
if($paramIdDependencia !== null && $paramIdDependencia != ""  && $paramIdDependencia != "0")
{
	$whereExterno = "AND dependencia.depe_id = '".$paramIdDependencia."'";
}

$query = "
	SELECT
		dependencia.depe_id AS dependencia_id,
		dependencia.depe_nombre AS dependencia_nombre,
		dependencia.depe_id_sup AS dependencia_id_padre,
		dependencia.depe_nivel AS dependencia_nivel,
		SUM(salidas.monto) AS monto
	FROM
		(
				SELECT
					SUM (
						CASE
							WHEN salida.tipo = 'S' THEN
								(salida_detalle.cantidad * entrada_detalle.precio)
							ELSE
								(salida_detalle.cantidad * entrada_detalle.precio) * -1
						END
					) AS monto,
					salida.depe_entregada AS id_dependencia
				FROM
					sai_arti_acta_almacen salida
					INNER JOIN sai_arti_salida salida_detalle ON (salida_detalle.n_acta = salida.amat_id)
					INNER JOIN sai_item_articulo item_articulo ON (item_articulo.id = salida_detalle.arti_id)
					INNER JOIN sai_item item ON (item.id = item_articulo.id)
					INNER JOIN sai_arti_tipo categoria ON (categoria.tp_id = item_articulo.tipo)
					INNER JOIN sai_arti_almacen entrada_detalle
						ON (entrada_detalle.alm_id = salida_detalle.alm_id AND entrada_detalle.arti_id = salida_detalle.arti_id)
				WHERE
					salida.esta_id <> 15
					AND salida.entregado_a != '-1'
					AND salida.fecha_acta BETWEEN
						TO_DATE('".$paramFechaInicio."', 'DD/MM/YYYY')
							AND TO_TIMESTAMP('".$paramFechaFin." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
					".$whereInterno."
				GROUP BY
					salida.depe_entregada
					
			UNION
			
				SELECT
					SUM (
						CASE
							WHEN salida.tipo = 'S' THEN
								(salida_detalle.cantidad * entrada_detalle.precio)
							ELSE
								(salida_detalle.cantidad * entrada_detalle.precio) * -1
						END
					) AS monto,
					CASE
						WHEN (categoria.tp_id='1') THEN '450' 
						WHEN (categoria.tp_id='2') THEN '550' 
						WHEN (categoria.tp_id='7' or categoria.tp_id='11') THEN '453' 
						WHEN (categoria.tp_id='8') THEN '600'
						WHEN (categoria.tp_id='3') THEN '250'
						--ELSE '450'
					END AS id_dependencia
				FROM
					sai_arti_acta_almacen salida
					INNER JOIN sai_arti_salida salida_detalle ON (salida_detalle.n_acta = salida.amat_id)
					INNER JOIN sai_item_articulo item_articulo ON (item_articulo.id = salida_detalle.arti_id)
					INNER JOIN sai_item item ON (item.id = item_articulo.id)
					INNER JOIN sai_arti_tipo categoria ON (categoria.tp_id = item_articulo.tipo)
					INNER JOIN sai_arti_almacen entrada_detalle
						ON (entrada_detalle.alm_id = salida_detalle.alm_id AND entrada_detalle.arti_id = salida_detalle.arti_id)
				WHERE
					salida.esta_id <> 15
					AND salida.entregado_a = '-1'
					AND salida.fecha_acta BETWEEN
						TO_DATE('".$paramFechaInicio."', 'DD/MM/YYYY')
							AND TO_TIMESTAMP('".$paramFechaFin." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
					".$whereInterno."
				GROUP BY
					categoria.tp_id
					
		) salidas
		INNER JOIN sai_dependenci dependencia ON (dependencia.depe_id = salidas.id_dependencia)
	WHERE
		salidas.id_dependencia IS NOT NULL
		".$whereExterno."
	GROUP BY
		dependencia.depe_id,
		dependencia.depe_nombre,
		dependencia.depe_id_sup,
		dependencia.depe_nivel
	ORDER BY
		dependencia_nombre
";
	
$resultado = pg_query($conexion, $query) or die("Error al mostrar consulta");

while ($row = pg_fetch_array($resultado))
{
	$totalGastos += $row['monto'];
	$pdf->Cell(100, $alto + 5, $row['dependencia_nombre'], 1, 0, 'L');
	$pdf->Cell(32, $alto + 5, number_format($row['monto'], 2, ',', '.'), 1, 2, 'R');
	
	$pdf->SetX(35);
}
	
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetX(35);
$pdf->Cell(100,$alto, "Totales:", 1, 0, 'R');
$pdf->Cell(32,$alto,number_format($totalGastos, 2, ',', '.'), 1, 0, 'R');
$pdf->Ln(5);
		  
//Se determina el nombre del archivo, se firma, se abre y se limpia temporales
$tipo_documento=substr($codigo,0,4);
$pdf-> Output();
?> 

