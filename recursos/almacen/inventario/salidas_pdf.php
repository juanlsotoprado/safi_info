<?php
ob_start();
require("../../../includes/conexion.php");
require("../../../lib/fpdf/fpdf.php");
require_once("../../../includes/fechas.php");

$codigo=$_REQUEST['id']; 
$consulta=$_REQUEST['consulta'];
$anulado=0;

if ($consulta<>'1'){
//codigo del documento
$arti_id=$_REQUEST['arti_id'];
$arti_cant=$_REQUEST['cant'];
$arti  = trim(str_replace('{','',$arti_id));
$arti  = trim(str_replace('}','',$arti));

$cant  = trim(str_replace('{','',$arti_cant));
$cant = trim(str_replace('}','',$cant));

$arreglo_arti=split(",",$arti);
$arreglo_cantidad=split(",",$cant);
$longitud=count($arreglo_arti);


}



//*********************************************************************
//Cargamos los Datos del Acta de Almacen	
$sql_arti="SELECT distinct(arti_id) as articulos,nombre,t2.esta_id FROM sai_arti_acta_almacen t2, sai_arti_salida,sai_item t1
WHERE amat_id='".$codigo."' and amat_id=n_acta and arti_id=t1.id order by nombre";

$resultado_arti=pg_query($conexion,$sql_arti) or die("Error al mostrar"); 
$total_items=pg_num_rows($resultado_arti);

$i=0;
while($rows=pg_fetch_array($resultado_arti))
{ 
	if ($rows['esta_id']==15)
	  $anulado=1;
 $sql_acta="SELECT arti_id,fecha_acta,depe_entregada,usua_login,medida,observaciones,entregado_a,salida_id,esta_id 
FROM sai_arti_acta_almacen, sai_arti_salida
WHERE amat_id='".$codigo."' and amat_id=n_acta and arti_id='".$rows['articulos']."'
GROUP BY arti_id,fecha_acta,depe_entregada,usua_login,medida,observaciones,entregado_a,salida_id,esta_id";
//echo $sql_acta;
$resultado_acta=pg_query($conexion,$sql_acta) or die("Error al mostrar"); 
while($row=pg_fetch_array($resultado_acta))
{
		$medida[$i]=trim($row['medida']);
		$login=trim($row['usua_login']);
		$id_depe=trim($row['depe_entregada']);
		$fecha_acta=cambia_esp(trim($row['fecha_acta']));
		$obs=$row['observaciones'];
		$entregar=$row['entregado_a'];

		$id_art[$i]=trim($row['arti_id']);
		$id_salida=$row['salida_id'];
        $edo=$row['esta_id'];
	/*	if ($consulta<>'1'){
		 for ($l=0; $l<$longitud; $l++)
  		 {
		 $arti  = $arreglo_arti[$l];

		  if ($arti==$id_art[$i]){
		   $sql="UPDATE sai_arti_salida set cantidad_solicitada=".$arreglo_cantidad[$l]." WHERE salida_id='".$id_salida."'";

                   $resultado=pg_query($conexion,$sql) or die("Error al mostrar"); 
		 }
                }
                }*/

		$i++;
}
} 	


$sql="SELECT empl_nombres, empl_apellidos FROM sai_empleado WHERE empl_cedula= trim('".$login."')"; 
$resultado=pg_exec($conexion,$sql) or die("Error al mostrar"); 
if($row=pg_fetch_array($resultado))
{
 $usuario=strtoupper($row['empl_nombres']." ".$row['empl_apellidos']);
}	
	
$sql="SELECT empl_nombres, empl_apellidos, depe_cosige FROM sai_empleado WHERE empl_cedula= trim('".$entregar."')"; 
$resultado=pg_exec($conexion,$sql) or die("Error al mostrar"); 
if($row=pg_fetch_array($resultado))
{
 $usuario_entregar=strtoupper($row['empl_nombres']." ".$row['empl_apellidos']);
 $depe_solicita=$row['depe_cosige'];
}	

$sql_depe="SELECT depe_nombre FROM sai_dependenci WHERE depe_id= trim('".$id_depe."')"; 
$resultado_depe=pg_exec($conexion,$sql_depe) or die("Error al mostrar"); 
if($row=pg_fetch_array($resultado_depe))
{
 $jefatura=$row['depe_nombre'];
}

//mostrar dependencia generica
$id_depe=substr($id_depe,0,2)."0";
$sql_depe="SELECT depe_nombre FROM sai_dependenci WHERE depe_id= trim('".$id_depe."')"; 
$resultado_depe=pg_exec($conexion,$sql_depe) or die("Error al mostrar"); 
if($row=pg_fetch_array($resultado_depe))
{
 $dependencia=$row['depe_nombre'];
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
		$this->Cell(250,15,utf8_decode('SALIDAS DE ALMACÉN '.$iddepe),0,1,'C');
		/*if ($edo==15){
		$this->SetTextColor(255,0,0);
		$this->Cell(250,15,utf8_decode('ANULADO '.$iddepe),0,1,'C');
		$this->SetTextColor(0,0,0);
  		}*/


		//Salto de l�nea
		$this->Ln(1);
	   	//Movernos a la derecha
		$this->SetXY(-60,45);
	    	$this->SetFont('Arial','B',8);
		$this->Cell(40,5,utf8_decode("1.Nº ACTA:  ".$codigo),1,0,'L');
		$this->SetXY(-60,50);
		$this->Cell(40,5,"2.Fecha:  ".$fecha_acta,1,0,'L');
	
		//Salto de l�nea
		$this->Ln(1);
		$this->SetXY(10,60);
	 	$this->SetFont('Arial','',9);
		$this->Cell(250,$alto,"ENTREGADO A:  ".$usuario_entregar." - ".$dependencia,1,2,'L');
		$this->SetX(10);
		$this->Cell(250,$alto,"DEPENDENCIA:  ".$jefatura,1,2,'L');
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
		$this->Cell(0,10,utf8_decode('  SAFI-Fundación Infocentro').'  '.$this->PageNo().'/{nb}',0,0,'R');
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
  $pdf->Cell(24,$alto,"ITEM",0,0,'C'); 
  $pdf->Cell(20,$alto,utf8_decode("CÓDIGO"),0,0,'C'); 
  $pdf->Cell(86,$alto,utf8_decode("DESCRIPCIÓN DETALLADA O TÉCNICA"),0,0,'C');
  $pdf->Cell(40,$alto,"UNIDAD DE MEDIDA",0,0,'C');
  $pdf->Cell(40,$alto,"CANTIDAD SOLICITADA",0,0,'C');
  $pdf->Cell(40,$alto,"CANTIDAD ENTREGADA",1,2,'C');
  
   
  $pdf->SetX(5.5);
  $pdf->Cell(24,$alto,"",0,0,'C');
  $pdf->Cell(20,$alto,"",0,0,'C');
  $pdf->Cell(86,$alto,"",0,0,'C');
  $pdf->Cell(40,$alto,"",0,0,'C');
  //$pdf->Cell(40,$alto,"",0,0,'C');
  $pdf->Cell(40,$alto,"",0,2,'C');    
  $pdf->SetXY(10,$posy); 
  $pdf->Cell(24,($alto),"",1,0,'C');
  $pdf->Cell(20,($alto),"",1,0,'C');
  $pdf->Cell(86,($alto),"",1,0,'C');
  $pdf->Cell(40,($alto),"",1,0,'C');
  $pdf->Cell(40,($alto),"",1,0,'C');
  $pdf->Cell(40,($alto),"",1,2,'C');  
  $pdf->SetFont('Arial','',8);
//echo $total_items;
  //for ($ii=0; $ii<$total_items; )
$ii=0;
$resultado_arti=pg_query($conexion,$sql_arti) or die("Error al mostrar"); 
while($rows=pg_fetch_array($resultado_arti))
  {
                 $articulo=$rows['articulos'];
/*echo $id_art[0];
echo "*";
echo $id_art[1];
echo "*";
echo $id_art[2];
echo "*";
echo $id_art[3];*/
		 $pdf->SetX(10); 
		 $pdf->Cell(24,$alto,($ii+1),1,0,'C'); 
		 $pdf->Cell(20,$alto,trim($articulo),1,0,'C'); 
	         

		$sql="SELECT nombre,unidad_medida FROM sai_item t1, sai_item_articulo t2 WHERE t1.id=t2.id and t1.id= trim('".$articulo."')"; 
		$resultado=pg_exec($conexion,$sql) or die("Error al mostrar"); 
		if($row=pg_fetch_array($resultado))
		{
 		$descripcion=$row['nombre'];
		$medida=$row['unidad_medida'];
		}
 		
		for ($l=0; $l<$longitud; $l++)
  		{
		$arti  = $arreglo_arti[$l];

		 if ($arti==$articulo){
   		   $cant_solicitada=$arreglo_cantidad[$l];
		 }
                }

		$sql_cant="SELECT sum(cantidad) as exis_canti FROM sai_arti_acta_almacen, sai_arti_salida
		WHERE amat_id='".$codigo."' and arti_id='".$articulo."' and amat_id=n_acta GROUP BY arti_id";

		$resultado=pg_exec($conexion,$sql_cant) or die("Error al mostrar"); 
		if($row=pg_fetch_array($resultado))
		{
   		$cantidad=trim($row['exis_canti']);
		}


		if ($consulta=='1'){
		 $sql_cant="SELECT cantidad_solicitada FROM sai_arti_acta_almacen, sai_arti_salida WHERE amat_id='".$codigo."' and arti_id='".$articulo."' and amat_id=n_acta ";
         $resultado=pg_exec($conexion,$sql_cant) or die("Error al mostrar"); 
		 if($row=pg_fetch_array($resultado))
		 {
   		  $cant_solicitada=trim($row['cantidad_solicitada']);
		 }
		}

		 $pdf->Cell(86,$alto,$descripcion,1,0,'L'); 
		 $pdf->Cell(40,$alto,$medida,1,0,'C');
	     $pdf->Cell(40,$alto,$cant_solicitada,1,0,'C');    
		 $pdf->Cell(40,$alto,$cantidad,1,2,'C');  
$ii++;
  }
  

  $pdf->Ln();
  $posy= $pdf->gety();
  $pdf->SetFont('Arial','B',8);
  $pdf->SetXY( 5.5,($posy+1));

  $pdf->SetX(10); 
  $pdf->Cell(15,$alto,"OBSERVACIONES: ",0,0,'L'); 
  $pdf->SetX(40); 
  $pdf->MultiCell(250,$alto,$obs,0,'L');
  $pdf->SetX( 80); 
  $pdf->Ln();
  $posy= $pdf->gety();
  $pdf->SetXY( 5.5,$posy); 
  $pdf->Cell(250,4,"",0,2,'R'); //Si no se coloca el getX no funciona
  $posy= $pdf->gety();

  $pdf->SetXY(10,$posy);
  $pdf->Cell(82.5,3, "GERENCIA SOLICITANTE",1,0,'c'); 
  $pdf->Cell(86,3, utf8_decode("ALMACEN"),1,0,'c'); 
   //$pdf->Cell(20,3, utf8_decode("% DE RETENCIÓN"),1,0,'c'); 
   $pdf->Cell(82,3, utf8_decode("COORDINACIÓN DE BIENES NACIONALES"),1,0,'c'); 
   
   $pdf->SetXY(10,$posy);
   $pdf->MultiCell(82.5,5,"\n\n___________________________\n".$usuario_entregar."\n" ,1,'C',0);
   $pdf->SetXY(92.5,$posy);
   $pdf->MultiCell(86,5,"\n\n___________________________\n"."ENTREGADO POR: ".utf8_decode($usuario)."\n",1,'C',0);
   $pdf->SetXY(178.5,$posy);
   $pdf->MultiCell(82,5,"\n\n___________________________\n"."REVISADO POR: "."\n" ,1,'C',0);
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

