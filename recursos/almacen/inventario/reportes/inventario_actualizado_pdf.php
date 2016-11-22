<?php
require("../../../../includes/conexion.php");
require("../../../../lib/fpdf/fpdf.php");
require_once("../../../../includes/fechas.php");

$buscar=$_GET['hid_buscar']; 
$fecha=$_GET['hid_hasta_itin'];
$fecha_fin=substr($fecha,6,4)."-".substr($fecha,3,2)."-".substr($fecha,0,2)." 23:59:59";

 //Tabla que muestra lista de articulos
 $sql_ar="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','t1.id,nombre,existencia_minima','t1.id=t2.id and esta_id=1','nombre',1) resultado_set(id varchar,nombre varchar,existencia_minima int4)"; 

//Colocamos la imagen del ministerio del lado derecho
class PDF extends FPDF
{
 //Cabecera de p�gina
 function Header() 
 {  
   $alto=4;
   global $fecha;
   global $buscar;

   //Logo
   $this->SetX(35);
   $this->Image('../../../../imagenes/encabezado.jpg',3,20,190,12);
   $this->Ln(3);
		
   $this->SetFont('Arial','B',12);
   //T�tulo
   $posy= $this->gety();
   $this->SetX(8);
   $this->SetY(31);
   $this->Cell(190,15,'INVENTARIO EXISTENTE HASTA LA FECHA '.$fecha,0,1,'C');
		
	//Datos del solicitante
	$this->SetX(8);
	$this->SetFont('Arial','B',7);
		
	if ($buscar==1){
	  $this->Cell(182,$alto,"",1,2,'C');
	  $this->Cell(14,$alto,utf8_decode("CÓDIGO"),1,0,'L');
	  $this->Cell(85,$alto,"NOMBRE",1,0,'L');
	  $this->Cell(15,$alto,utf8_decode("EXIS. MÍNIMA"),1,0,'L');
	  $this->Cell(15,$alto,"EXIS. ACTUAL",1,0,'L');
	  $this->Cell(28,$alto,"PRECIO UNITARIO BS.",1,0,'L');
	  $this->Cell(25,$alto,utf8_decode("MONTO TOTAL BS."),1,2,'L');
	}else{
		  $this->Cell(178,$alto,"",1,2,'C');
	  	  $this->Cell(20,$alto,utf8_decode("CÓDIGO"),1,0,'L');
		  $this->Cell(118,$alto,"NOMBRE",1,0,'L');
		  $this->Cell(20,$alto,utf8_decode("EXIS. MÍNIMA"),1,0,'L');
		  $this->Cell(20,$alto,"EXIS. ACTUAL",1,2,'L');
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
  
  $pdf->SetX(8);
  $pdf->SetFont('Arial','',7);

  if ($buscar==1)
  {
   $i=0;
   $resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de articulos");  
   while($row=pg_fetch_array($resultado_set_most_ar)) 
   {	
	$i++;		
	$arti=$row['id'];
	$total=0;
	$precio=0;
	$monto_total=0;	

  	 //Entradas
	$sql_e="
 		SELECT
			sum(cantidad) as entrada,
			precio,
			alm_id
		FROM
			sai_arti_almacen
			LEFT JOIN sai_arti_inco ON (sai_arti_inco.acta_id = sai_arti_almacen.acta_id)
		WHERE
			alm_fecha_recepcion <='".$fecha_fin."'
			AND arti_id='".$arti."'
			AND
			(
			sai_arti_inco.esta_id <> 15
			OR sai_arti_inco.esta_id IS NULL
			)
		GROUP BY
			arti_id,
			precio,
			alm_id
 	";
     $resultado_entrada=pg_query($conexion,$sql_e) or die("Error al consultar entrada de articulos3");  
	 while($rowe=pg_fetch_array($resultado_entrada)) 
  	 {
  	   $num_entradas=$rowe['entrada'];
  	   $id_almacen=$rowe['alm_id'];
  	   $precio=$rowe['precio'];
  	   $num_salidas=0;
	   $num_devoluciones=0;
  	
	   //SE LE SUMAN LAS DEVOLUCIONES Y SE RESTAN LAS SALIDAS HASTA LA FECHA, 
	   $sql_salidas="select sum(cantidad) as canti_salida,tipo from sai_arti_salida,sai_arti_acta_almacen where n_acta=amat_id and fecha_acta<'".$fecha_fin."' and arti_id='".$arti."' and esta_id<>15 and alm_id='".$id_almacen."' group by tipo";
  	   $num_salidas=0;
  	   $num_devoluciones=0;
  	
  	   $resultado_salidas=pg_query($conexion,$sql_salidas) or die("Error al consultar salida de articulos5");
  	   while($rowsal=pg_fetch_array($resultado_salidas)) 
  	   {	
  		if ($rowsal['tipo']=='S'){
  		  $num_salidas=$rowsal['canti_salida'];	
  		}else{
  			$num_devoluciones=$rowsal['canti_salida'];
  		}
       }
  	
       $inventario_fin=$num_entradas+$num_devoluciones-$num_salidas;
   	
   	   if ($inventario_fin>0)
   	   {	
		 $pdf->SetFont('Arial','B',7);
		 $pdf->Cell(14,$alto,utf8_decode($arti),1,0,'C');
		 $pdf->Cell(85,$alto,$row['nombre'],1,0,'L');
		 $pdf->Cell(15,$alto,$row['existencia_minima'],1,0,'R');
		 $pdf->Cell(15,$alto,$inventario_fin,1,0,'R');
		 $pdf->Cell(28,$alto,str_replace('.',',',$precio),1,0,'R');
		 $monto_total=$precio*$inventario_fin;
		 $pdf->Cell(25,$alto,number_format($monto_total,2,',','.'),1,2,'R');
		 $pdf->SetX(8);
	   }
  	  }	
    }
		
   }else{
	
    $resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de articulos");  
    while($row=pg_fetch_array($resultado_set_most_ar)) 
	{	
	 $i++;		
	 $arti=$row['id'];
	 $total=0;
	 $devolucion=0;
	 $salida=0;
	 $entrada=0;
	 $precio_en=0;
     $total_entrada=0;

	$sql_e="
		SELECT
			sum(cantidad) as entrada,
			precio
		FROM
			sai_arti_almacen
			LEFT JOIN sai_arti_inco ON (sai_arti_inco.acta_id = sai_arti_almacen.acta_id)
		WHERE
			alm_fecha_recepcion <='".$fecha_fin."'
			AND arti_id='".$arti."'
			AND
			(
			sai_arti_inco.esta_id <> 15
			OR sai_arti_inco.esta_id IS NULL
			)
		GROUP BY
			arti_id,
			precio
	";
	
     $resultado_entrada=pg_query($conexion,$sql_e) or die("Error al consultar entrada de articulos");
      
  	 while($rowe=pg_fetch_array($resultado_entrada)) 
  	 {	
      $entrada=$rowe['entrada'];
	  $precio_en=$rowcan['precio'];
	  $total_entrada=$total_entrada+$entrada;
  	 }
	  $sql_s="select sum (cantidad) as cantidad,tipo from sai_arti_salida t1, sai_arti_acta_almacen where
	  amat_id=n_acta and esta_id<>15 and arti_id='".$arti."' and fecha_acta <= '".$fecha_fin."'  group by tipo";

	  $resultado_salida=pg_query($conexion,$sql_s) or die("Error al consultar la salida de los articulos");  
  	  while($rows=pg_fetch_array($resultado_salida)) 
  	  {	
	   if ($rows['tipo']=='S'){
    	$salida=$rows['cantidad'];
	   }else{
		$devolucion=$rows['cantidad'];
	   }
  	  }

	  $total=$total_entrada+$devolucion-$salida;

	  $pdf->Cell(20,$alto,$arti,1,0,'C');
	  $pdf->Cell(118,$alto,$row['nombre'],1,0,'L');
	  $pdf->Cell(20,$alto,$row['existencia_minima'],1,0,'R');
	  if ($total>=0){ 
		$pdf->Cell(20,$alto,$total,1,2,'R');
	  }else {
		$pdf->Cell(20,$alto,"0",1,2,'R');
	 }
		$pdf->SetX(8);
	}
  }
			
	$pdf->Ln();
	$pdf->Ln();

$tipo_documento=substr($codigo,0,4);
$pdf-> Output();
pg_close($conexion);
?> 

