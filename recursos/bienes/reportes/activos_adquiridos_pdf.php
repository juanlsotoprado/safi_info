<?php
require("../../../includes/conexion.php");
require("../../../lib/fpdf/fpdf.php");
require_once("../../../includes/fechas.php");

    $wheretipo1="";
	$wheretipo2="";
	$wheretipo3="";
	$wheretipo4="";
	$wheretipo5="";
	$wheretipo6="";
	$wheretipo7="";
	$wheretipo8="";
	$wheretipo9="";
	$criterio2="";
	$criterio3="";
	$criterio4="";
	$criterio5="";
    $fecha_in=trim($_REQUEST['txt_inicio']); 
	$fecha_fi=trim($_REQUEST['hid_hasta_itin']); 
	$fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	$fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
	$depe=$_REQUEST['opt_depe'];
	$marca=$_REQUEST['marca'];
	$descripcion=$_REQUEST['descripcion'];
	$tipo_bien=$_REQUEST['tp_bien'];
	$serial=$_REQUEST['serial'];
	if ($_POST['sbn']<>""){
 	   $wheretipo8 = " and etiqueta = '".$_POST['sbn']."' ";
    }
    
    if ($serial<>""){
 	   $wheretipo9 = " and serial = '".$serial."' ";
      }
	
    if (strlen($fecha_ini)>2) {
      if ($_REQUEST['tipo_fec']==0){
 	   $wheretipo1 = " and fecha_entrada >= '".$fecha_ini."' and fecha_entrada <= '".$fecha_fin."' ";
 	   $group="fecha_entrada,";
      }else{
      	$wheretipo1 = " and asbi_fecha >= '".$fecha_ini."' and asbi_fecha <= '".$fecha_fin."' ";
 	    $group="asbi_fecha,";
      }
      $criterio1=" desde el ".$fec_ini." al ".$fec_fin1;
    }
	
    if ($_REQUEST['opt_depe']>0) {
	  $wheretipo2 = " and depe_solicitante='".$depe."' ";
	  $query_depe="SELECT depe_nombre FROM sai_dependenci WHERE depe_id='".$depe."'";
	  $resultado_depe=pg_query($conexion,$query_depe);	
	  if ($row=pg_fetch_array($resultado_depe)){
	  	$criterio2=" de la dependencia ".utf8_encode(($row['depe_nombre']));
	  }
    }
	 
     if ($_REQUEST['marca']>0){ 
	  $wheretipo3 = " and marca_id='".$marca."' ";
      $query="SELECT bmarc_nombre FROM sai_bien_marca WHERE bmarc_id='".$_REQUEST['marca']."'";
	  $resultado=pg_query($conexion,$query);	
	  if ($row=pg_fetch_array($resultado)){
	  	$criterio3=" de la marca ".$row['bmarc_nombre'];
	  }
     }
     
      if ($_REQUEST['descripcion']<>0) {
	  $wheretipo4 = " and bien_id='".$descripcion."' ";
	  $criterio4=" del activo ".$descripcion;
      }
      
	  if ($_REQUEST['tp_bien']<>0){
	    $from1=",sai_item_bien t5,bien_categoria t6 ";
	  	$wheretipo5 = " and t5.tipo=t6.id and t5.id=t1.bien_id and t6.id='".$tipo_bien."'";
	    $query="SELECT nombre FROM bien_categoria WHERE id='".$tipo_bien."'";
	    $resultado=pg_query($conexion,$query);	
	    if ($row=pg_fetch_array($resultado)){
	  	 $criterio5=" de la clasificación ".utf8_encode($row['nombre']);
	    }
	  }
	  
	  if ($_REQUEST['estatus']>0) 
	   $wheretipo7 = " and t1.esta_id='".$_REQUEST['estatus']."' ";

	 $buscar=$_REQUEST['tp_reporte'];
	  if (($_REQUEST['tp_reporte'])==1){//GENERAL
	 if ($_REQUEST['tipo_fec']==0){
     $sql_tabla1="SELECT fecha_entrada as fecha, depe_solicitante as dependencia,t1.bien_id,precio as precio,count(t1.bien_id) as cantidad,t3.acta_id
     FROM sai_biin_items t1, sai_bien_inco t3".$from1."
     WHERE t3.acta_id=t1.acta_id".$wheretipo1.$wheretipo2.$wheretipo3.$wheretipo4.$wheretipo5.$wheretipo8.$wheretipo9." group by ".$group."depe_solicitante,t1.bien_id,fecha_entrada,precio,t3.acta_id order by 1";
     	
	 }else {
	 $sql_tabla1="SELECT asbi_fecha as fecha, solicitante as dependencia,t1.bien_id,precio as precio,count(t1.bien_id) as cantidad,t3.asbi_id as acta_id
     FROM sai_biin_items t1, sai_bien_asbi_item t2, sai_bien_asbi t3".$from1."
     WHERE t3.asbi_id=t2.asbi_id and t2.clave_bien=t1.clave_bien".$wheretipo1.$wheretipo2.$wheretipo3.$wheretipo4.$wheretipo5.$wheretipo8.$wheretipo9." group by ".$group."solicitante,t1.bien_id,precio,t3.asbi_id order by 1";	 	
	 }
	}else{//DETALLADO
		
	if ($_REQUEST['tipo_fec']==0){
	   $sql_tabla1="SELECT fecha_entrada as fecha_e,0 as fecha_s, depe_solicitante as dependencia,t1.bien_id,t1.ubicacion,precio as precio,t1.esta_id,count(t1.bien_id) as cantidad,depe_nombre,
     modelo,etiqueta,serial,bmarc_nombre,t4.nombre as nombre_bien,esta_nombre,clave_bien,t1.acta_id as acta_e, 0 as acta_s
     FROM sai_biin_items t1, sai_bien_inco t3 ,sai_bien_marca t2,sai_item t4,sai_estado t7,sai_dependenci t8 ".$from1."
     WHERE t8.depe_id=t3.depe_solicitante and t4.id=t1.bien_id and marca_id=bmarc_id and t3.acta_id=t1.acta_id and t7.esta_id=t1.esta_id ".$wheretipo1.$wheretipo2.$wheretipo3.$wheretipo4.$wheretipo5.$wheretipo7.$wheretipo8.$wheretipo9." group by ".$group."depe_solicitante,t1.bien_id,fecha_entrada,t1.ubicacion,precio,t1.esta_id,modelo,etiqueta,serial,bmarc_nombre,t4.nombre,esta_nombre,clave_bien,t1.acta_id,depe_nombre
     ORDER BY t1.bien_id,etiqueta";
	   	}else{
	   $sql_tabla1="SELECT fecha_registro as fecha_e,asbi_fecha as fecha_s, solicitante as dependencia,t1.bien_id,t1.ubicacion,precio as precio,t1.esta_id,count(t1.bien_id) as cantidad,depe_nombre,
     modelo,etiqueta,serial,bmarc_nombre,t4.nombre as nombre_bien,esta_nombre,t1.clave_bien,t10.acta_id as acta_e,t3.asbi_id as acta_s
     FROM sai_biin_items t1,sai_bien_asbi_item t9, sai_bien_asbi t3 ,sai_bien_marca t2,sai_item t4,sai_estado t7,sai_dependenci t8, sai_bien_inco t10 ".$from1."
     WHERE t10.acta_id=t1.acta_id and t8.depe_id=t3.solicitante and t4.id=t1.bien_id and marca_id=bmarc_id and 
     t9.asbi_id=t3.asbi_id and t9.clave_bien=t1.clave_bien and t7.esta_id=t1.esta_id ".$wheretipo1.$wheretipo2.$wheretipo3.$wheretipo4.$wheretipo5.$wheretipo7.$wheretipo8.$wheretipo9." group by ".$group."solicitante,t1.bien_id,asbi_fecha,t1.ubicacion,precio,t1.esta_id,modelo,etiqueta,serial,bmarc_nombre,t4.nombre,esta_nombre,t1.clave_bien,t3.asbi_id,depe_nombre,fecha_registro,t10.acta_id
     ORDER BY 1,t1.bien_id,etiqueta";	   		
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
   global $buscar;
   global $criterio2;
   global $criterio3;
   global $criterio4;
   global $criterio5;

   //Logo
  if ($buscar==1){
   $this->SetX(35);
   $this->Image('../../../imagenes/encabezado.jpg',3,20,190,12);
   $posy= $this->gety();
   $this->SetX(8);
   $this->SetY(31);
  }else{

		$this->Image('../../../imagenes/encabezado.jpg',10,22,260,15);
		$posy= $this->gety();
 	    $this->SetX(78);
        $this->SetY(35);
  }
   $this->Ln(3);
		
   $this->SetFont('Arial','B',12);
   //T�tulo
   
   if ($buscar==1){
   $this->Cell(190,15,'Movimiento de Activos '.utf8_decode($criterio2.$criterio3.$criterio4.$criterio5),0,1,'C');
   }else{
   	      $dia=substr($fecha_ini,8,2);
		  $mes=substr($fecha_ini,5,2);
		  $anno=substr($fecha_ini,0,4);
		  $fec_ini=$dia.'/'.$mes.'/'.$anno;
		  
		  $dia1=substr($fecha_fin,8,2);
		  $mes1=substr($fecha_fin,5,2);
		  $anno1=substr($fecha_fin,0,4);
		  $fec_fin1=$dia1.'/'.$mes1.'/'.$anno1;
		  if (strlen($fec_ini)>5){
		  $this->Cell(245,15,'Movimiento de Activos desde el '.$fec_ini.' al '.$fec_fin1,0,1,'C');	
		  	}else{
  		  $this->Cell(245,15,'Movimiento de Activos al '.date(d.'-'.m.'-'.Y)."".utf8_decode($criterio2.$criterio3.$criterio4.$criterio5),0,1,'C');
		  	}
   }
	//Datos del solicitante
	
	$this->SetFont('Arial','B',7);
		
	if ($buscar==1){

	  $this->SetX(18);
	  $this->Cell(165,$alto,"",1,2,'C');
	  $this->Cell(20,$alto,utf8_decode("Acta"),1,0,'C');
	  $this->Cell(90,$alto,"Activo",1,0,'C');
      $this->Cell(15,$alto,"Fecha",1,0,'C');
	  $this->Cell(15,$alto,"Cantidad",1,0,'C');
	  $this->Cell(25,$alto,"Valor unitaio Bs.",1,2,'C');
	  }
	  
	  else{
		$this->SetX(18);
		  $this->Cell(250,$alto,"",1,2,'C');
	  	  $this->Cell(7,$alto,utf8_decode("#"),1,0,'C');
		  $this->Cell(70,$alto,"Activo",1,0,'C');
		  $this->Cell(15,$alto,utf8_decode("Estatus"),1,0,'C');
		 // $this->Cell(16,$alto,utf8_decode("Acta entrada"),1,0,'C');
		  $this->Cell(18,$alto,utf8_decode("Fecha ingreso"),1,0,'C');
		  $this->Cell(15,$alto,utf8_decode("Acta salida"),1,0,'C');
		  $this->Cell(18,$alto,utf8_decode("Fecha salida"),1,0,'C');		  
		  $this->Cell(27,$alto,utf8_decode("Marca"),1,0,'C');
		  $this->Cell(32,$alto,utf8_decode("Modelo"),1,0,'C');
		  $this->Cell(33,$alto,utf8_decode("Serial activo"),1,0,'C');
		  $this->Cell(15,$alto,utf8_decode("Serial B/N"),1,2,'C');
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
  
  if ($buscar==1)
  $pdf=new PDF();
  else
  $pdf=new PDF('L','mm','Letter');
  $pdf->AddPage();
  $pdf->AliasNbPages();  
  $alto=4;
  $posy= $pdf->gety();
  
  $pdf->SetFont('Arial','B',7);
  $pdf->SetXY(18,$posy); 
  

  $pdf->SetFont('Arial','',7);

   $i=0;
   $resultado_set_t1=pg_query($conexion,$sql_tabla1) or die("Error al mostrar consulta");	
   while ($rowt1=pg_fetch_array($resultado_set_t1)) 
   {	
   	if ($buscar==1){
   	$cantidad=$rowt1['cantidad'];
	$id=$rowt1['bien_id'];
	$monto=$rowt1['precio'];
	if (strlen($rowt1['fecha'])>4)
	$fec=substr($rowt1['fecha'],8,2).'/'.substr($rowt1['fecha'],5,2).'/'.substr($rowt1['fecha'],0,4);
	else
	$fec="--";
   	
   	$sql_d="SELECT * FROM sai_seleccionar_campo('sai_item','id,nombre','id=''$id''','',1) resultado_set(id varchar,nombre varchar)"; 
	$resultado_set_most_d=pg_query($conexion,$sql_d) or die("Error al consultar lista de articulos");
	$rowd=pg_fetch_array($resultado_set_most_d);
	
	
    $pdf->SetFont('Arial','',7);
	$pdf->Cell(20,$alto,$rowt1['acta_id'],1,0,'C');
	$pdf->Cell(90,$alto,strtoupper($rowd['nombre']),1,0,'L');
	$pdf->Cell(15,$alto,$fec,1,0,'L');
	$pdf->Cell(15,$alto,$cantidad,1,0,'C');
	$pdf->Cell(25,$alto,number_format($monto,2,',','.'),1,2,'R');
   	}
	else{
		$i++;
		$id_acta=$rowt1['acta_id'];
		$torre="-";
		$galpon="-";
		if ($rowt1['esta_id']=="41"){
		if ($rowt1['ubicacion']=="1")
		 $torre="X";
		 elseif ($rowt1['ubicacion']=="2")
		 $galpon="X";
		}
		
       	$id_acta=$rowt1['acta_e'];
	    $fec=substr($rowt1['fecha_e'],8,2).'/'.substr($rowt1['fecha_e'],5,2).'/'.substr($rowt1['fecha_e'],0,4);
		
		if ($_REQUEST['tipo_fec']==0){
	      if ($rowt1['esta_nombre']<>"Incorporado"){
	         $sql_acta="SELECT t1.asbi_id,asbi_fecha FROM sai_bien_asbi_item t1,sai_bien_asbi t2 where t1.asbi_id=t2.asbi_id and t2.esta_id<>15 and clave_bien='".$rowt1['clave_bien']."'";
	         $resultado_acta=pg_query($conexion,$sql_acta);
	         if ($row_acta=pg_fetch_array($resultado_acta))
	         	$id_acta_salida=$row_acta['asbi_id'];
	         	$fecha_salida=$row_acta['asbi_fecha'];
	         	if (strlen($row_acta['asbi_fecha'])>4)
		          $fecha_salida=substr($row_acta['asbi_fecha'],8,2).'/'.substr($row_acta['asbi_fecha'],5,2).'/'.substr($row_acta['asbi_fecha'],0,4);
		        else
		          $fecha_salida="";
	         }else{
	        	$id_acta_salida="";
	        	 $fecha_salida="";
	        }}else{
	        	$fecha_salida=$fec=substr($rowt1['fecha_s'],8,2).'/'.substr($rowt1['fecha_s'],5,2).'/'.substr($rowt1['fecha_s'],0,4);
	        	$id_acta_salida=$rowt1['acta_s'];
	        }
	$pdf->Cell(7,$alto,$i,1,0,'C');
	$pdf->Cell(70,$alto,strtoupper($rowt1['nombre_bien']),1,0,'L');
    $pdf->Cell(15,$alto,$rowt1['esta_nombre'],1,0,'L');
    //$pdf->Cell(16,$alto,$id_acta,1,0,'C');
    $pdf->Cell(18,$alto,$fec,1,0,'C');
    $pdf->Cell(15,$alto,$id_acta_salida,1,0,'C');
    $pdf->Cell(18,$alto,$fecha_salida,1,0,'C');
    $pdf->Cell(27,$alto,strtoupper($rowt1['bmarc_nombre']),1,0,'C');
    //MultiCell
	$pdf->Cell(32,$alto, strtoupper($rowt1['modelo']),1,0,'C');
	$pdf->Cell(33,$alto,$rowt1['serial'],1,0,'C');
	$pdf->Cell(15,$alto,$rowt1['etiqueta'],1,2,'C');
	}
		 $pdf->SetX(18);
    }
				
	$pdf->Ln();
	$pdf->Ln();

$tipo_documento=substr($codigo,0,4);
$pdf-> Output();
pg_close($conexion);
?> 

