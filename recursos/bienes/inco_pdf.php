<?php
require("../../includes/conexion.php");
require("../../lib/fpdf/fpdf.php");
require_once("../../includes/fechas.php");

$codigo = "";
if (isset($_GET['codigo']) && $_GET['codigo'] != "") {
	$codigo = $_GET['codigo'];
}

if($codigo && $codigo!=""){
	$anno_pres=$_SESSION['an_o_presupuesto'];
	$query =	"SELECT etiqueta,sbi.esta_id as esta_id,to_char(fecha_registro,'DD/MM/YYYY') as fecha,pcta_id,num_licitacion,".
				"se.empl_nombres || ' ' || se.empl_apellidos as elaborado,sd.depe_nombre, bmarc_nombre,si.nombre,modelo,serial,precio,garantia,descripcion,proveedor, ".
				"case proveedor when '' then '--' else 
				 (select prov_nombre from sai_proveedor_nuevo where prov_id_rif=proveedor)  end as nombre_proveedor, ".
				"to_char(fecha_entrada,'DD/MM/YYYY') as fecha_entrada,binc_nombre ".
				"FROM sai_bien_inco sbi
				 left outer join sai_biin_items sbii on (sbi.acta_id=sbii.acta_id)
				 left outer join sai_item si on (si.id=sbii.bien_id)
				 left outer join sai_empleado se on (sbi.usua_login=se.empl_cedula)
				 left outer join sai_dependenci sd on (sbi.depe_solicitante=sd.depe_id)
				 left outer join sai_bien_marca sbm on (sbm.bmarc_id=sbii.marca_id)
				 left outer join sai_binc_tipo sbt on (binc_id=tipo)
				  WHERE sbi.acta_id=trim('".$codigo."') ";
	//echo $query;
	$resultado=pg_query($conexion,$query);
	$row=pg_fetch_array($resultado);
    
	$esta_id=$row["esta_id"];
	$fecha_acta=$row["fecha"];
	$elaborado=$row["elaborado"];
	$depe_nombre=$row["depe_nombre"];
	$pcta=$row["pcta_id"];
	$licitacion=$row["num_licitacion"];
    $proveedor=$row["nombre_proveedor"];
    $fecha_entrada=$row['fecha_entrada'];
    $tipo_incorporacion=$row['binc_nombre'];
}
    
//Colocamos la imagen del ministerio del lado derecho
class PDF extends FPDF
{
 //Cabecera de p�gina
 function Header() 
 {  
   $alto=4;
   global $codigo;
   global $fecha_acta;
   global $fecha_entrada;
   global $esta_id;
   global $depe_nombre;
   global $licitacion;
   global $pcta;
   global $proveedor;
   global $tipo_incorporacion;
   global $query;

   if ($pcta=="")
    $pcta="--";

   if ($licitacion=="")
    $licitacion="--";
   //Logo
   $this->SetX(35);
   $this->Image('../../imagenes/encabezado.jpg',3,20,264,15);
   $this->Ln(3);
		
   $this->SetFont('Arial','B',12);
   //T�tulo
   $posy= $this->gety();
   $this->SetX(8);
   $this->SetY(31);
   $this->Cell(256,15,'ENTRADA DE ACTIVOS',0,1,'C');
   if ($esta_id == 15) 
   	$this->Cell(258,18,'Anulado ',0,1,'C');
		
	//Datos del solicitante
	
	$this->SetFont('Arial','B',7);
	$this->SetX(8);
		 $this->Cell(88,$alto,"Acta: ".$codigo,1,0,'L');
		 $this->Cell(88,$alto,"Fecha acta: ".$fecha_acta,1,0,'L');
		 $this->Cell(88,$alto,utf8_decode("Fecha de recepción en almacén: ").$fecha_entrada,1,2,'L');
		 $this->SetX(8);
		 $this->Cell(132,$alto,"Dependencia solicitante: ".$depe_nombre,1,0,'L');
		 $this->Cell(132,$alto,utf8_decode("Tipo de incorporación: ").$tipo_incorporacion,1,2,'L');
		 
		 $this->SetX(8);
		 $this->Cell(132,$alto,utf8_decode("Licitación/ Orden de compra: ").$licitacion,1,0,'L');
  	 	 $this->Cell(132,$alto,"Punto de cuenta: ".$pcta,1,2,'L');
  	 	 $this->SetX(8);
	 	 $this->Cell(264,$alto,"Proveedor: ".$proveedor,1,2,'L');

	  $this->SetX(8);
	  $this->Cell(264,$alto,"Activos ingresados",1,2,'C');
	  $this->Cell(8,$alto,utf8_decode("#"),1,0,'C');
	  $this->Cell(85,$alto,utf8_decode("Nombre"),1,0,'L');
	  $this->Cell(26,$alto,"Serial Bien Nacional",1,0,'L');
	  $this->Cell(30,$alto,"Marca",1,0,'L');
	  $this->Cell(45,$alto,"Modelo",1,0,'L');
	  $this->Cell(35,$alto,utf8_decode("Serial activo"),1,0,'L');
	  $this->Cell(20,$alto,utf8_decode("Precio unitario"),1,0,'L');
	  $this->Cell(15,$alto,utf8_decode("Garantía"),1,2,'L');

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
  $pdf=new PDF('L','mm','Letter');
  $pdf->AddPage();
  $pdf->AliasNbPages();  
  $alto=4;
  $posy= $pdf->gety();
  
  $pdf->SetFont('Arial','B',7);
  $pdf->SetXY(8,$posy); 
  $pdf->SetFont('Arial','',7);
  $i=1;
  $resultado=pg_query($conexion,$query);
  while ($row=pg_fetch_array($resultado)){
  

		 $pdf->SetFont('Arial','',7);
		 $pdf->Cell(8,$alto,$i,1,0,'C');
		 $pdf->Cell(85,$alto,utf8_decode($row['nombre']),1,0,'L');
		 $pdf->Cell(26,$alto,$row['etiqueta'],1,0,'C');
		 $pdf->Cell(30,$alto,strtoupper($row['bmarc_nombre']),1,0,'L');
		 $pdf->Cell(45,$alto,strtoupper($row['modelo']),1,0,'L');
		 $pdf->Cell(35,$alto,$row['serial'],1,0,'L');
  	 	 $pdf->Cell(20,$alto,$row['precio'],1,0,'C');
  	 	 if ($row['garantia']=='')
  	 	 	$garantia="--";
  	 	 else
  	 	 $garantia=$row['garantia']." meses";
	 	 $pdf->Cell(15,$alto,$garantia,1,2,'C');
	
		 $pdf->SetX(8);
		 $i++;
    }
				
	$pdf->Ln();
	$pdf->Ln();

$tipo_documento=substr($codigo,0,4);
$pdf-> Output();
pg_close($conexion);
?> 

