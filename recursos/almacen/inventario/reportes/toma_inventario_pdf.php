<?php
require("../../../../includes/conexion.php");
require("../../../../lib/fpdf/fpdf.php");
require_once("../../../../includes/fechas.php");

$fecha=date(d."-".m."-".Y);//$_GET['hid_hasta_itin'];
$fecha_fin=substr($fecha,6,4)."-".substr($fecha,3,2)."-".substr($fecha,0,2)." 23:59:59";

//*********************************************************************

 //Tabla que muestra lista de articulos
 $sql_ar="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','t1.id,nombre,existencia_minima,unidad_medida','t1.id=t2.id and esta_id=1','nombre',1) resultado_set(id varchar,nombre varchar,existencia_minima int4,unidad_medida varchar)"; 
				
/**************************************************************************/

//Colocamos la imagen del ministerio del lado derecho
class PDF extends FPDF
{
    //Cabecera de p�gina
	function Header() {  
	
		$alto=4;
		global $fecha;

		//Logo
		$this->SetX(35);
		$this->Image('../../../../imagenes/encabezado.jpg',20,20,242,12);
		$this->Ln(3);
		
		$this->SetFont('Arial','B',12);
		//T�tulo
		$posy= $this->gety();
		$this->SetX(20);
		$this->SetY(31);
		$this->Cell(245,15,'Toma de Inventario al '.date(d."-".m."-".Y) ,0,1,'C');
		
	
		//Datos del solicitante
		$this->SetX(20);
		$this->SetFont('Arial','B',7);
		

			$this->Cell(242,$alto,"",1,2,'C');
			$this->Cell(15,$alto,utf8_decode("Código"),1,0,'L');
			$this->Cell(95,$alto,"Nombre",1,0,'L');
			$this->Cell(12,$alto,utf8_decode("Medida"),1,0,'L');
			$this->Cell(25,$alto,"Existencia SAFI",1,0,'L');
			$this->Cell(25,$alto,"Existencia manual",1,0,'L');
			$this->Cell(70,$alto,"Observaciones",1,2,'L');
	

	}
	
	
	
	//Pie de p�gina
	//**********************************************************************

	 function Footer() {  
	 
		global $user_nombre;
		$this->SetX(3.5);
		$this->SetFont('Arial','B',7);
		//N�mero de p�gina
		$this->Cell(0,10,utf8_decode('  SAFI-Fundación Infocentro').'  '.$this->PageNo().'/{nb}',0,0,'R');
		$this->Cell(-3,16,utf8_decode('Detalle generado el día').'  '.date("d/m/y").' a las '.date("H:i:s"),0,0,'R');
	}
     
}		 


//**********************************************************************
  $pdf=new PDF('L','mm','A4');
  //$pdf=new PDF();
  $pdf->AddPage();
  $pdf->AliasNbPages();  
  $alto=4;
  $posy= $pdf->gety();
  
  $pdf->SetFont('Arial','B',7);
  $pdf->SetXY(12,$posy); 
  
		$pdf->SetX(20);
		$pdf->SetFont('Arial','',7);

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

     $sql_e="select sum(cantidad) as entrada,precio from sai_arti_almacen where alm_fecha_recepcion <='".$fecha_fin."' and arti_id='".$arti."' group by arti_id,precio";
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
	

					
			//$pdf->Cell(12,$alto,$i,1,0,'C');
			$pdf->Cell(15,$alto,$arti,1,0,'C');
		    $pdf->Cell(95,$alto,$row['nombre'],1,0,'L');
			$pdf->Cell(12,$alto,$row['unidad_medida'],1,0,'R');
			if ($total>=0){ 
			$pdf->Cell(25,$alto,$total,1,0,'R');
			}else {
			$pdf->Cell(25,$alto,"0",1,0,'R');
			 }
			 $pdf->Cell(25,$alto,"",1,0,'R');
			 $pdf->Cell(70,$alto,"",1,2,'R');
			$pdf->SetX(20);
	}

		
			
	$pdf->Ln();
	$pdf->Ln();


/****************************************/


//Se determina el nombre del archivo, se firma, se abre y se limpia temporales
$tipo_documento=substr($codigo,0,4);
 $pdf-> Output();
pg_close($conexion);
?> 

