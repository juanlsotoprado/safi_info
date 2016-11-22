<?php
require("../../../../includes/conexion.php");
require("../../../../lib/fpdf/fpdf.php");
require_once("../../../../includes/fechas.php");

$nombre_arti=trim($_GET['des_articulo']);
$codigo_arti=trim($_GET['txt_articulo']); 
$fecha_in=trim($_GET['txt_inicio']);
$fecha_fi=trim($_GET['hid_hasta_itin']);
$buscar=trim($_GET['hid_buscar']);

$fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
$fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2)." 00:00:00";
 

//Colocamos la imagen del ministerio del lado derecho
class PDF extends FPDF
{
   //Cabecera de p�gina
   function Header()
   {  
	$alto=4;
	global $codigo_arti;
	global $nombre_arti;
	global $fecha_ini;
	global $fecha_fin;
	global $fecha_in;
	global $fecha_fi;
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
	$this->Cell(190,15,utf8_decode('REPORTE DE MOVIMIENTOS DEL '.$fecha_in.' AL '.$fecha_fi),0,1,'C');
	$this->SetX(8);
	$this->SetY(38);
	$this->Cell(190,15,strtoupper($nombre_arti),0,1,'C');
	$this->SetX(8);
	$this->SetFont('Arial','B',7);
		
	if ($buscar==1){
	 $this->Cell(178,$alto,"",1,2,'C');
	 $this->Cell(20,$alto,"FECHA",1,0,'C');
	 $this->Cell(100,$alto,"DEPENDENCIA",1,0,'L');
	 $this->Cell(18,$alto,"ENTRADAS",1,0,'L');
	 $this->Cell(18,$alto,"SALIDAS",1,0,'L');
	 $this->Cell(22,$alto,"DEVOLUCIONES",1,2,'L');
			
	}else{
		  $this->Cell(190,$alto,"",1,2,'C');
		  $this->Cell(18,$alto,"R.I.F. ",1,0,'C');
		  $this->Cell(112,$alto,"PROVEEDOR",1,0,'L');
		  $this->Cell(16,$alto,"CANTIDAD",1,0,'L');
		  $this->Cell(24,$alto,"COSTO UNITARIO",1,0,'L');
		  $this->Cell(20,$alto,"COSTO TOTAL",1,2,'L');	
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
  $pdf->SetXY(12,$posy); 
  $pdf->SetX(8);
  $pdf->SetFont('Arial','',7);

  
  if ($buscar==1){
		$sql = "
				SELECT
					arti_almacen.alm_fecha_recepcion AS fecha,
					arti_almacen.cantidad,
					arti_almacen.depe_solicitante AS dependencia,
					'E' AS tipo
				FROM
					sai_arti_inco arti_inco
					INNER JOIN sai_arti_almacen arti_almacen ON (arti_almacen.acta_id = arti_inco.acta_id)
				WHERE
					arti_inco.esta_id <> 15
					AND arti_almacen.arti_id='".$codigo_arti."'
					AND arti_almacen.alm_fecha_recepcion >= '".$fecha_ini."'
					AND arti_almacen.alm_fecha_recepcion <= '".$fecha_fin."'
				
			UNION
			
				SELECT
					fecha_acta AS fecha,
					sum(cantidad),
					depe_entregada AS dependencia,
					tipo
				FROM
					sai_arti_acta_almacen,
					sai_arti_salida
				WHERE
					amat_id = n_acta
					AND arti_id='".$codigo_arti."'
					AND esta_id<>15
					AND fecha_acta>='".$fecha_ini."'
					AND fecha_acta<='".$fecha_fin."'
				GROUP BY
					1,
					depe_entregada,
					tipo
					
			ORDER BY 1";
       $resultado_set_t1=pg_query($conexion,$sql) or die("Error al mostrar consulta en sai_arti_existen");
		   
		   $i=0;
		   $total_entradas=0;
		   $total_salidas=0;
		   $total_devolucion=0;
	       while ($rowt1=pg_fetch_array($resultado_set_t1)) 
		   {
		       $i++;
   		       $cantidad=trim($rowt1['cantidad']);
		       $fecha=substr($rowt1['fecha'],8,2).'/'.substr($rowt1['fecha'],5,2).'/'.substr($rowt1['fecha'],0,4);		   

		if(trim($rowt1['tipo']=='E')){ 
		  $movimiento='Entrada'; 
		  $total_entradas=$total_entradas+$cantidad;
		}
		if(trim($rowt1['tipo']=='S')){ 
			$movimiento='Salida'; 
			$total_salidas=$total_salidas+$cantidad;
		}
		if(trim($rowt1['tipo']=='D')){ 
			$movimiento=utf8_decode('Devolución'); 
			$total_devolucion=$total_devolucion+$cantidad;
		}
		   // $pdf->Cell(12,$alto,$i,1,0,'C');
			$pdf->Cell(20,$alto,$fecha,1,0,'C');
		    $depe=$rowt1['dependencia'];
		    $sql_depe="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_nombre','depe_id='||'''$depe''','',2) 
		    resultado_set(depe_nombre varchar)"; 
 	          $resultado=pg_query($conexion,$sql_depe) or die("Error al mostrar consulta de la dependencia");
  		    if ($row=pg_fetch_array($resultado)) 
  		    {
   		     $dependencia=$row['depe_nombre'];
  		    }
		    $pdf->Cell(100,$alto,$dependencia,1,0,'L');
		    if(trim($rowt1['tipo']=='E')){
		     $pdf->Cell(18,$alto,$cantidad,1,0,'R');
			 $pdf->Cell(18,$alto,"-",1,0,'R');
			 $pdf->Cell(22,$alto,"-",1,2,'R');
		    }
		    if(trim($rowt1['tipo']=='S')){
		     $pdf->Cell(18,$alto,"-",1,0,'R');
			 $pdf->Cell(18,$alto,$cantidad,1,0,'R');
			 $pdf->Cell(22,$alto,"-",1,2,'R');
		    }
		   	if(trim($rowt1['tipo']=='D')){
		     $pdf->Cell(18,$alto,"-",1,0,'R');
			 $pdf->Cell(18,$alto,"-",1,0,'R');
			 $pdf->Cell(22,$alto,$cantidad,1,2,'R');
		    }
			$pdf->SetX(8);
		   
		   }
		    $pdf->SetFont('Arial','B',7);
		   	$pdf->Cell(120,$alto,"TOTALES:",1,0,'R');
			$pdf->Cell(18,$alto,$total_entradas,1,0,'R');
			$pdf->Cell(18,$alto,$total_salidas,1,0,'R');
			$pdf->Cell(22,$alto,$total_devolucion,1,0,'R');
		    
			$pdf->Ln(4);
			$pdf->SetX(8);
		    $pdf->Cell(156,$alto,"TOTAL INVENTARIO",1,0,'R');
		    $pdf->Cell(22,$alto,($total_entradas+$total_devolucion)-$total_salidas,1,0,'R');
  }else{
  	
  	  	   $sql = "SELECT cantidad,precio,prov_id_rif FROM sai_arti_almacen where arti_id='".$codigo_arti."' and alm_fecha_recepcion>='".$fecha_ini."' and alm_fecha_recepcion<='".$fecha_fin."' ";
           $resultado_set_t1=pg_query($conexion,$sql) or die("Error al mostrar consulta en sai_arti_existen");
		   $total=0;
	       while ($rowt1=pg_fetch_array($resultado_set_t1)) 
		   {
		    
   		       $cantidad=trim($rowt1['cantidad']);
		     
               if ($rowt1['prov_id_rif']==''){
               	$rif="--";
               	$nombre="--";
               }else{
               	 $rif=$rowt1['prov_id_rif'];
               	 $sql_p="SELECT * FROM sai_seleccionar_campo('sai_proveedor_nuevo','prov_nombre','prov_id_rif='||'''$rif''','',2) resultado_set(prov_nombre varchar)"; 
                 $resultado=pg_query($conexion,$sql_p) or die("Error al mostrar");
	            if($row=pg_fetch_array($resultado)){
	             $nombre=$row['prov_nombre'];	
	            }
               }		   

	
			$pdf->Cell(18,$alto,$rif,1,0,'C');
		    $pdf->Cell(112,$alto,$nombre,1,0,'L');
		    $pdf->Cell(16,$alto,$cantidad,1,0,'R');
			$pdf->Cell(24,$alto,str_replace('.',',',$rowt1['precio']),1,0,'R');
			$pdf->Cell(20,$alto,number_format($rowt1['precio']*$cantidad,2,',','.'),1,2,'R');
		    $total= $total+$rowt1['precio']*$cantidad;
			$pdf->SetX(8);
		   
		   }
            $pdf->SetFont('Arial','B',7);
		   	$pdf->Cell(170,$alto,"TOTAL:",1,0,'R');
			$pdf->Cell(20,$alto,number_format($total,2,',','.'),1,0,'R');
			$pdf->Ln(5);
  	
  }	    

$tipo_documento=substr($codigo,0,4);
$pdf-> Output();
pg_close($conexion);
?> 

