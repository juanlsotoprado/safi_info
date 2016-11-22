<?php
ob_start();
require("../../../includes/conexion.php");
require("../../../lib/fpdf/fpdf.php");
require_once("../../../includes/fechas.php");

$codigo=$_REQUEST['id']; 
$consulta=$_REQUEST['consulta'];
$anulado=$_REQUEST['anulado'];
//*********************************************************************
//Cargamos los Datos del Acta de Almacen	
$sql_arti="SELECT distinct(arti_id) as articulos,nombre,t3.esta_id FROM sai_arti_inco t3, sai_arti_almacen t2,sai_item t1
WHERE t3.acta_id='".$codigo."' and t3.acta_id=t2.acta_id and arti_id=t1.id order by nombre";
$resultado_arti=pg_query($conexion,$sql_arti) or die("Error al mostrar 1"); 
$total_items=pg_num_rows($resultado_arti);
$i=0;
while($rows=pg_fetch_array($resultado_arti))
{ 
  if ($rows['esta_id']==15)
	$anulado=1;
//--,entregado_a,salida_id,esta_id 
$sql_acta="SELECT arti_id,fecha_registro,t1.depe_solicitante,t1.usua_login,medida,precio,cantidad,t1.esta_id,proveedor,t2.ubicacion,alm_fecha_recepcion
FROM sai_arti_inco t1, sai_arti_almacen t2
WHERE t1.acta_id='".$codigo."' and t1.acta_id=t2.acta_id and arti_id='".$rows['articulos']."'
GROUP BY arti_id,fecha_registro,t1.depe_solicitante,t1.usua_login,medida,precio,cantidad,t1.esta_id,proveedor,t2.ubicacion,alm_fecha_recepcion ";
 //,salida_id,esta_id
$resultado_acta=pg_query($conexion,$sql_acta) or die("Error al mostrar 2"); 
while($row=pg_fetch_array($resultado_acta))
{       $edo=$row['esta_id'];
		$medida[$i]=trim($row['medida']);
		$login=trim($row['usua_login']);
		$id_depe=trim($row['depe_solicitante']);
		$fecha_acta=cambia_esp(trim($row['fecha_registro']));
		$precio[$i]=$row['precio'];
		$cantidad[$i]=$row['cantidad'];
		$id_art[$i]=trim($row['arti_id']);
		$rif_proveedor=$row['proveedor'];
		$fecha_recepcion=cambia_esp(trim($row['alm_fecha_recepcion']));
		$ubicacion = $row['ubicacion'];
		$i++;
}
} 	

if($ubicacion == 1)
{
	$ubicaciontxt = "Torre";
}
else
{
	$ubicaciontxt = "Galpón";
}

$sql="SELECT empl_nombres, empl_apellidos FROM sai_empleado WHERE empl_cedula= trim('".$login."')"; 
$resultado=pg_exec($conexion,$sql) or die("Error al mostrar 3"); 
if($row=pg_fetch_array($resultado))
{
 $usuario=strtoupper($row['empl_nombres']." ".$row['empl_apellidos']);
}	

$sql_depe="SELECT depe_nombre FROM sai_dependenci WHERE depe_id= trim('".$id_depe."')"; 
$resultado_depe=pg_exec($conexion,$sql_depe) or die("Error al mostrar 4"); 
if($row=pg_fetch_array($resultado_depe))
{
 $dependencia=$row['depe_nombre'];
}

	$nombreproveedor="--";
    $query_proveedor="Select prov_nombre From sai_proveedor_nuevo where prov_id_rif='".$rif_proveedor."'";                   
    $resultado_proveedor=pg_query($conexion,$query_proveedor);
    if($rowpro=pg_fetch_array($resultado_proveedor)){
       $nombreproveedor=$rowpa['proveedor'].":".$rowpro['prov_nombre'];
    }

	
class PDF extends FPDF
{
    
function PDF($orientation='L',$unit='mm',$format='Letter')
	{
    //Call parent constructor
    	$this->FPDF($orientation,$unit,$format);
	}
//Cabecera de P�gina
	function Header()
	{  $alto=4;
	   global $fecha_acta;
	   global $codigo;
	   global $usuario;
	   global $obs;
	   global $dependencia;
	   global $usuario_entregar;
	   global $jefatura;
	   global $anulado;
       global $iddepe;
	   global $edo;
	   global $nombreproveedor;
	   global $fecha_recepcion;
	   global $ubicaciontxt;
	   global $sql_acta;
	   global $rif_proveedor;
	   global $i;
	   global $id_art;
		//Logo
		$this->SetX(50);
		$this->SetY(55);
		$this->Image('../../../imagenes/encabezado.jpg',3,22,260,15);
		$this->Ln(3);
		//Arial bold 15
		$this->SetFont('Arial','B',14);
		//T�tulo
		$posy= $this->gety();
		$this->SetX(3.5);
		$this->SetY(36);
		$this->Cell(250,15,utf8_decode('ENTRADAS DE ALMACÉN '.$iddepe),0,1,'C');
		if ($edo==15){
		$this->SetTextColor(255,0,0);
		$this->Cell(250,15,utf8_decode('ANULADO '.$iddepe),0,1,'C');
		$this->SetTextColor(0,0,0);
  		}


		//Salto de l�nea
		$this->Ln(1);
	   	//Movernos a la derecha
		$this->SetXY(-60,45);
	    $this->SetFont('Arial','B',8);
		$this->Cell(40,5,utf8_decode("1.Nº ACTA:  ".$codigo),1,0,'L');
		$this->SetXY(-60,50);
		$this->Cell(40,5,"2.Fecha:  ".$fecha_acta,1,0,'L');
		$this->SetXY(-60,55);
		$this->Cell(40,4,utf8_decode("3.Ubicación:  ").utf8_decode($ubicaciontxt),1,0,'L');
	
		//Salto de l�nea
		$this->Ln(1);
		$this->SetXY(10,60);
	 	$this->SetFont('Arial','',9);
		$this->Cell(250,$alto,"Solicitado por:  ".$usuario_entregar."  ".$dependencia,1,2,'L');
		$this->SetX(10);
		$this->Cell(250,$alto,utf8_decode("Fecha recepción almacén:  ").$fecha_recepcion,1,2,'L');
		$this->SetX(10);
		$this->Cell(250,$alto,"Proveedor:  ".$rif_proveedor.$nombreproveedor,1,2,'L');
		$this->Ln();
		 $this->SetFont('Arial','B',7);
	}
	//Pie de p�gina
	//**********************************************************************

 function Footer()
 {  
		$this->SetX(53.5);
		$this->SetFont('Arial','B',8);
		//N�mero de p�gina
		$this->Cell(0,20,utf8_decode('  SAFI-Fundación Infocentro').'  '.$this->PageNo().'/{nb}',0,0,'R');
		//$this->Cell(0,16,utf8_decode('Detalle generado el día').'  '.date("d/m/y").' a las '.date("H:i:s"),0,0,'R');
 	
 }
     

}		 
  $pdf=new PDF('L','mm','Letter');
  $pdf->AddPage();
  $pdf->AliasNbPages();  //Alias para el n�mero total de p�gina
  $alto=4;
  $posy= $pdf->gety();
  $pdf->SetFont('Arial','B',6);
  $pdf->SetXY( 10,($posy+1)); 
  $pdf->Cell(50,$alto,"",0,0,'l'); 
  $pdf->SetXY( 10,($posy+1)); 
  $pdf->SetFont('Arial','B',8);
  $pdf->SetX(10);
  $posy= $pdf->gety();
  $pdf->Cell(24,$alto,"Item",0,0,'C'); 
  $pdf->Cell(20,$alto,utf8_decode("Código"),0,0,'C'); 
  $pdf->Cell(86,$alto,utf8_decode("Nombre del Artículo"),0,0,'C');
  $pdf->Cell(40,$alto,"Unidad de medida",0,0,'C');
  $pdf->Cell(40,$alto,"Cantidad",0,0,'C');
  $pdf->Cell(40,$alto,"Precio unitario",1,2,'C');
  
   
  $pdf->SetX(5.5);
  $pdf->Cell(24,$alto,"",0,0,'C');
  $pdf->Cell(20,$alto,"",0,0,'C');
  $pdf->Cell(86,$alto,"",0,0,'C');
  $pdf->Cell(40,$alto,"",0,0,'C');
  $pdf->Cell(40,$alto,"",0,2,'C');    
  $pdf->SetXY(10,$posy); 
  $pdf->Cell(24,($alto),"",1,0,'C');
  $pdf->Cell(20,($alto),"",1,0,'C');
  $pdf->Cell(86,($alto),"",1,0,'C');
  $pdf->Cell(40,($alto),"",1,0,'C');
  $pdf->Cell(40,($alto),"",1,0,'C');
  $pdf->Cell(40,($alto),"",1,2,'C');  
  $pdf->SetFont('Arial','',8);

$ii=0;
$resultado_arti=pg_query($conexion,$sql_arti) or die("Error al mostrar"); 

for($j=0; $j<$i; $j++)//while($rows=pg_fetch_array($resultado_arti))
  {
         $articulo=$id_art[$j];//$rows['articulos'];
		 $pdf->SetX(10); 
		 $pdf->Cell(24,$alto,($ii+1),1,0,'C'); 
		 $pdf->Cell(20,$alto,trim($articulo),1,0,'C'); 
	         

		$sql="SELECT nombre,unidad_medida FROM sai_item t1, sai_item_articulo t2 WHERE t1.id=t2.id and t1.id= trim('".$articulo."')"; 
		$resultado=pg_exec($conexion,$sql) or die("Error al mostrar 5"); 
		while($row=pg_fetch_array($resultado))
		{
 		$descripcion=$row['nombre'];
		$medida=$row['unidad_medida'];
		}
		 $pdf->Cell(86,$alto,$descripcion,1,0,'L'); 
		 $pdf->Cell(40,$alto,$medida,1,0,'C');
	     $pdf->Cell(40,$alto,$cantidad[$j],1,0,'C');    
		 $pdf->Cell(40,$alto,$precio[$j],1,2,'C');  
$ii++;
//$j++;
  }
  

  $pdf->Ln();
  $posy= $pdf->gety();
  $pdf->SetFont('Arial','B',8);
  $pdf->SetXY( 5.5,($posy+1));

  $pdf->SetXY(10,$posy);
  $pdf->Cell(125,3, utf8_decode("Almacén"),1,0,'c'); 
   //$pdf->Cell(20,3, utf8_decode("% DE RETENCIÓN"),1,0,'c'); 
   $pdf->Cell(125,3, utf8_decode("Coordinación de Bienes Nacionales"),1,0,'c'); 
   
   $pdf->SetXY(10,$posy);
   $pdf->MultiCell(125,5,"\n\n___________________________\n"."Registrado por: ".utf8_decode($usuario)."\n",1,'C',0);
   $pdf->SetXY(135,$posy);
   $pdf->MultiCell(125,5,"\n\n___________________________\n"."Revisado por: "."\n" ,1,'C',0);
   $pdf->SetXY(178.5,$posy);
   
   $posy= $pdf->gety();

  $pdf->Ln();
  $pdf->Ln();
  $pdf->Ln(); 
  if ($anulado==1){
  $pdf->Image('../../../imagenes/anulado.jpg',210,152,46,35);	
}


//Se determina el nombre del archivo, se firma, se abre y se limpia temporales
//$tipo_documento=substr($codigo,0,4);
//include("../../pdf_con_firma.php");
	ob_clean();
	$pdf-> Output();
 //pg_close($conexion);
?> 

