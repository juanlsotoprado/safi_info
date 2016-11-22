<?php
require("../../../includes/conexion.php");
require("../../../lib/fpdf/fpdf.php");
require_once("../../../includes/fechas.php");

$totales=$_GET['totales']; 
$fecha=$_GET['ff'];
$fecha_hoy=date("d/m/Y");
$imprimir=0;
if ($fecha==$fecha_hoy){
	$imprimir=1;
}
$fechaFin=substr($fecha,6,4)."-".substr($fecha,3,2)."-".substr($fecha,0,2)." 23:59:59";
/*
 if ($totales==1)
  $sql_ar="SELECT nombre,count(t1.id)as cantidad,existencia_minima,t1.id FROM sai_item t1, sai_item_bien t3, sai_biin_items t2 WHERE t1.id=t3.id and t1.id=t2.bien_id and fecha_entrada <='".$fechaFin."' group by t1.id,t1.nombre,existencia_minima order by nombre";
 else
  $sql_ar="SELECT nombre,bmarc_nombre,modelo,count(t1.id)as cantidad,existencia_minima,t1.id, bmarc_id FROM sai_item t1, sai_item_bien t3, sai_biin_items t2,sai_bien_marca WHERE t1.id=t3.id and t1.id=t2.bien_id and fecha_entrada <='".$fechaFin."' and  bmarc_id=marca_id group by t1.id,bmarc_nombre,modelo,t1.nombre,existencia_minima, bmarc_id order by nombre";
*/

if($totales == "1"){ // General
	$queryInventarioSelect = "
		item_particular.bien_id AS id_bien,
		item.nombre AS nombre_item,
		count(item.nombre) AS existencia
	";
		
	$queryInventarioGroupBy = "
		item_particular.bien_id,
		item.nombre
	";
		
	$queryInventarioOrderBy = "
		item.nombre
	";
		
} else { // Detallado
	$queryInventarioSelect = "
		item_particular.bien_id AS id_bien,
		item.nombre AS nombre_item,
		count(item.nombre) AS existencia,
		item_particular.modelo,
		marca.bmarc_nombre AS nombre_marca,
		marca.bmarc_id AS id_marca,
		item_bien.existencia_minima
	";
		
	$queryInventarioGroupBy = "
		item_particular.bien_id,
		marca.bmarc_nombre,
		marca.bmarc_id,
		item_particular.modelo,
		item.nombre,
		item_bien.existencia_minima
	";
		
	$queryInventarioOrderBy = "
		item.nombre,
		marca.bmarc_nombre,
		item_particular.modelo
	";
}

$queryInventario = "
	SELECT
		".$queryInventarioSelect."
	FROM
		sai_item item
		INNER JOIN sai_item_bien item_bien ON (item_bien.id = item.id)
		INNER JOIN sai_biin_items item_particular
			ON (item.id = item_particular.bien_id)
		INNER JOIN sai_bien_marca marca ON (item_particular.marca_id = marca.bmarc_id)
	WHERE
		item_particular.fecha_entrada <= '".$fechaFin."'
		AND item_particular.etiqueta NOT IN
		(
		SELECT
			COALESCE(asignacion.etiqueta, '') || COALESCE(reasignacion.etiqueta, '') AS etiqueta
		FROM
			(
			SELECT
				tabla.etiqueta,
				max(tabla.fecha_acta) AS fecha_acta
			FROM
				(
				SELECT
					asignacion.asbi_fecha AS fecha_acta,
					asignacion.esta_id AS id_estatus_acta,
					item_particular.bien_id AS id_bien,
					item_particular.modelo AS modelo,
					item_particular.marca_id AS id_marca,
					item_particular.etiqueta AS etiqueta
				FROM
					sai_bien_asbi asignacion
					INNER JOIN sai_bien_asbi_item asignacion_item
						ON (asignacion_item.asbi_id = asignacion.asbi_id)
					INNER JOIN sai_biin_items item_particular
						ON (item_particular.clave_bien = asignacion_item.clave_bien)
	
				UNION
				
				SELECT
					reasignar.fecha_acta AS fecha_acta,
					reasignar.esta_id AS id_estatus_acta,
					item_particular.bien_id AS id_bien,
					item_particular.modelo AS modelo,
					item_particular.marca_id AS id_marca,
					item_particular.etiqueta AS etiqueta
				FROM
					sai_bien_reasignar reasignar
					INNER JOIN sai_bien_reasignar_item reasignar_item
						ON (reasignar.acta_id = reasignar_item.acta_id)
					INNER JOIN sai_biin_items item_particular
						ON (item_particular.clave_bien = reasignar_item.clave_bien)
				) AS tabla
			WHERE
				tabla.fecha_acta <= '".$fechaFin."'
				AND tabla.id_estatus_acta <> 15
			GROUP BY
				tabla.etiqueta
			) fuera_inventario
			
			LEFT JOIN
	
			(
			SELECT
				asignacion.asbi_id AS id_acta,
				asignacion.asbi_fecha AS fecha_acta,
				item_particular.etiqueta
				
			FROM
				sai_bien_asbi asignacion
				INNER JOIN sai_bien_asbi_item asignacion_item
					ON (asignacion_item.asbi_id = asignacion.asbi_id)
				INNER JOIN sai_biin_items item_particular
					ON (item_particular.clave_bien = asignacion_item.clave_bien)
			) asignacion ON (
					asignacion.etiqueta = fuera_inventario.etiqueta
					AND asignacion.fecha_acta = fuera_inventario.fecha_acta
					)
	
			LEFT JOIN
	
			(
			SELECT
				reasignar.fecha_acta AS fecha_acta,
				reasignar.esta_id AS id_estatus_acta,
				reasignar.tipo AS tipo,
				item_particular.etiqueta AS etiqueta
			FROM
				sai_bien_reasignar reasignar
				INNER JOIN sai_bien_reasignar_item reasignar_item
					ON (reasignar.acta_id = reasignar_item.acta_id)
				INNER JOIN sai_biin_items item_particular
					ON (item_particular.clave_bien = reasignar_item.clave_bien)
			) reasignacion ON (
					reasignacion.etiqueta = fuera_inventario.etiqueta
					AND reasignacion.fecha_acta = fuera_inventario.fecha_acta
					)
		WHERE
			asignacion.id_acta LIKE 'a-%'
			OR
			(reasignacion.id_estatus_acta = '9' AND reasignacion.tipo <> 3)
			OR reasignacion.id_estatus_acta <> '9'
		)
	GROUP BY
		".$queryInventarioGroupBy."
	ORDER BY
		".$queryInventarioOrderBy."
";

$resultadoInventario = pg_query($conexion, $queryInventario) or die("Error al consultar lista de activos");

$arrInventario = array();
$idsBien = array();

while($rowInventario = pg_fetch_array($resultadoInventario))
{
	$arrInventario[] = $rowInventario;
	$idsBien[] = $rowInventario['id_bien'];
}

// Si totales General = 1
if($totales == "1" && count($idsBien) > 0){
	$queryDistribucion = "
		SELECT
			item_particular.bien_id AS id_bien,
			count(
				CASE item_particular.ubicacion
					WHEN 2 THEN 1
					ELSE NULL
				END
			) AS en_galpon,
			count(
				CASE item_particular.ubicacion
					WHEN 1 THEN 1
					ELSE NULL
				END
			) AS en_torre
		FROM
			sai_item item
			INNER JOIN sai_item_bien item_bien ON (item_bien.id = item.id)
			INNER JOIN sai_biin_items item_particular
				ON (item.id = item_particular.bien_id)
			INNER JOIN sai_bien_marca marca ON (item_particular.marca_id = marca.bmarc_id)
		WHERE
			item_particular.bien_id IN ('".implode("', '", $idsBien)."')
			AND item_particular.esta_id = '41'
		GROUP BY
			item_particular.bien_id,
			item.nombre
		ORDER BY
			item.nombre
	";
		
	$resultadoDistribucion = pg_query($conexion, $queryDistribucion)
	or die("Error al consultar lista de activos en galpo y/o torre.");
		
	while($rowDistribucion = pg_fetch_array($resultadoDistribucion))
	{
		$arrDistribucion[$rowDistribucion['id_bien']] = $rowDistribucion;
	}
}

		

//Colocamos la imagen del ministerio del lado derecho
class PDF extends FPDF
{
 //Cabecera de p�gina
 function Header() 
 {  
   $alto=4;
   global $fecha;
   global $imprimir;
   global $totales;

   //Logo
   $this->SetX(35);
   $this->Image('../../../imagenes/encabezado.jpg',3,20,190,12);
   $this->Ln(3);
		
   $this->SetFont('Arial','B',12);
   //T�tulo
   $posy= $this->gety();
   $this->SetX(8);
   $this->SetY(31);
   $this->Cell(190,15,'Inventario a la fecha '.$fecha,0,1,'C');
		
	//Datos del solicitante
	
	$this->SetFont('Arial','B',7);
		
	if ($totales==1){
	  if ($imprimir==1){
	  $this->SetX(25);
	  $this->Cell(160,$alto,"",1,2,'C');
	  }else{
	  $this->SetX(38);
	  $this->Cell(120,$alto,"",1,2,'C');
	  }
	  $this->Cell(15,$alto,utf8_decode("Código"),1,0,'L');
	  $this->Cell(85,$alto,"Activo",1,0,'L');
	  if ($imprimir==1){
	  $this->Cell(20,$alto,"Existencia",1,0,'L');
	  $this->Cell(20,$alto,utf8_decode("Galpón"),1,0,'L');
	  $this->Cell(20,$alto,"Torre",1,2,'L');
	  }else{
	  	$this->Cell(20,$alto,"Existencia",1,2,'L');
	  }}else{
		$this->SetX(18);
		  $this->Cell(180,$alto,"",1,2,'C');
	  	  $this->Cell(15,$alto,utf8_decode("Código"),1,0,'L');
		  $this->Cell(85,$alto,"Activo",1,0,'L');
		  $this->Cell(30,$alto,utf8_decode("Marca"),1,0,'L');
		  $this->Cell(30,$alto,utf8_decode("Modelo"),1,0,'L');
		  $this->Cell(20,$alto,"Existencia",1,2,'L');
		}
}
	
 //Pie de p�gina
 function Footer() 
 {  
  global $user_nombre;
  $this->SetX(3.5);
  $this->SetFont('Arial','B',7);
  //N�mero de p�gina
  $this->Cell(0,10,utf8_decode('  SAFI-Fundación Infocentro').'  '.$this->PageNo().'/{nb}',0,0,'R');
  $this->Cell(0,16,utf8_decode('Detalle generado el día').'  '.date("d/m/y").' a las '.date("H:i:s"),0,0,'R');
 }
     
}		 

  $pdf=new PDF();
  $pdf->AddPage();
  $pdf->AliasNbPages();  
  $alto=4;
  $posy= $pdf->gety();
  
  $pdf->SetFont('Arial','B',7);
  $pdf->SetXY(12,$posy); 
  

  $pdf->SetFont('Arial','',7);

   $i=0;
   
	//$resultado_set_most_ar=pg_query($conexion, $queryInventario) or die("Error al consultar lista de articulos");  
	//while($row=pg_fetch_array($resultado_set_most_ar))
	foreach ($arrInventario AS $inventario) 
	{	
		//$cant_salida=0;
		//$cantidad_existencia=0;
		$i++;		
		//$arti = $row['id'];
		$arti = $inventario['id_bien'];
		if ($totales==1){
			
			if ($imprimir==1)
				$pdf->SetX(25);
			else
				$pdf->SetX(38);
			
			/*
			$salidas="select count (t2.clave_bien) as cantidad_salida from sai_bien_asbi t1,sai_bien_asbi_item t2,sai_biin_items t3
				where asbi_fecha<='".$fechaFin."' and t1.esta_id<>15 and t1.asbi_id=t2.asbi_id and t2.clave_bien=t3.clave_bien
				and bien_id='".$row['id']."' ";
			$resultado_salida=pg_query($conexion,$salidas);  
			if($row_salida=pg_fetch_array($resultado_salida)){
				$cant_salida=$row_salida['cantidad_salida'];	
			}
			
			$torre=0;
			$sql_torre="SELECT nombre,count(t1.id)as cantidad,existencia_minima,t1.id 
				FROM sai_item t1, sai_item_bien t3, sai_biin_items t2 
				WHERE t1.id=t3.id and t1.id=t2.bien_id and fecha_entrada <='".$fechaFin."' and 
				t2.esta_id=41 and ubicacion=1 and t1.id='".$row['id']."' group by t1.id,t1.nombre,existencia_minima order by nombre";
			$resultado_torre=pg_query($conexion,$sql_torre) or die("Error al consultar lista de activos");  
			if($row_torre=pg_fetch_array($resultado_torre))
			{
				$torre=$row_torre['cantidad'];
			}
			$galpon=0;
			$sql_galpon="SELECT nombre,count(t1.id)as cantidad,existencia_minima,t1.id 
				FROM sai_item t1, sai_item_bien t3, sai_biin_items t2 
				WHERE t1.id=t3.id and t1.id=t2.bien_id and fecha_entrada <='".$fechaFin."' and 
				t2.esta_id=41 and ubicacion=2 and t1.id='".$row['id']."' group by t1.id,t1.nombre,existencia_minima order by nombre";
			$resultado_galpon=pg_query($conexion,$sql_galpon) or die("Error al consultar lista de activos");  
			if($row_galpon=pg_fetch_array($resultado_galpon))
			{
				$galpon=$row_galpon['cantidad'];
			}
			*/
      
		}else{
			$pdf->SetX(18);
			/*
			$salidas="select count (t2.clave_bien) as cantidad_salida from sai_bien_asbi t1,sai_bien_asbi_item t2,sai_biin_items t3
				where asbi_fecha<='".$fechaFin."' and t1.esta_id<>15 and t1.asbi_id=t2.asbi_id and t2.clave_bien=t3.clave_bien
				and bien_id='".$row['id']."'  and modelo='".$row['modelo']."' and marca_id='".$row['bmarc_id']."'";
			$resultado_salida=pg_query($conexion,$salidas);  
			if($row_salida=pg_fetch_array($resultado_salida)){
				$cant_salida=$row_salida['cantidad_salida'];	
			}
			*/
		}
		
		//$cantidad_existencia=$row['cantidad']-$cant_salida;
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(15,$alto,utf8_decode($arti),1,0,'C');
		$pdf->Cell(85,$alto,strtoupper($inventario['nombre_item']),1,0,'L');
		if ($totales==2){
			$pdf->Cell(30,$alto,strtoupper($inventario['nombre_marca']),1,0,'L');
			$pdf->Cell(30,$alto,strtoupper($inventario['modelo']),1,0,'L');
			$pdf->Cell(20,$alto,$inventario['existencia'],1,2,'C');
		}
		if ($totales==1){
			if ($imprimir==1){
				$pdf->Cell(20,$alto,$inventario['existencia'],1,0,'C');
				$pdf->Cell(20,$alto,
						($arrDistribucion[$inventario['id_bien']] ? $arrDistribucion[$inventario['id_bien']]['en_galpon'] : "--" )
						,1,0,'C'
				);
				$pdf->Cell(
						20,$alto,
						($arrDistribucion[$inventario['id_bien']] ? $arrDistribucion[$inventario['id_bien']]['en_torre'] : "--" )
						,1,2,'C'
				);
			}else{
				$pdf->Cell(20,$alto,$inventario['existencia'],1,2,'C');
			}
		}
		$pdf->SetX(8);
	}
				
	$pdf->Ln();
	$pdf->Ln();

	$tipo_documento=substr($codigo,0,4);
	$pdf-> Output();
	pg_close($conexion);
?> 

