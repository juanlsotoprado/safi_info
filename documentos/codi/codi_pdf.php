<?php
require("../../includes/conexion.php");
require("../../lib/fpdf/fpdf.php");
require_once("../../includes/fechas.php");
include("../../includes/funciones.php");
//codigo del documento
$codigo=$_GET['cod_doc'];
$anulado=0; 

$sql="SELECT * FROM  sai_codi WHERE comp_id='".$codigo."'"; 
$resultado=pg_query($conexion,$sql) or die("Error al mostrar"); 
while($row=pg_fetch_array($resultado))
{ 
  $id_comp=trim($row['comp_id']);
  $comp_fec=trim($row['comp_fec']);
  $tipo=trim($row['comp_tipo']);
  $comentario=trim($row['comp_comen']);
  $hora_emis=substr(trim($row['comp_fec_emis']),10);
  $fecha_emis=cambia_esp(trim($row['comp_fec_emis']));
  
  $id_depe=trim($row['depe_id']);
  $edo=$row['esta_id'];
  $documento=$row['comp_doc_id'];
  $referencia=$row['nro_referencia'];
  $compromiso=$row['nro_compromiso'];
  $reserva=$row['fte_financiamiento'];
}
	

$sql="SELECT * FROM  sai_seleccionar_campo ('sai_doc_genera,sai_empleado','empl_nombres, empl_apellidos','docg_id='||'''$codigo'''||' and sai_doc_genera.usua_login=empl_cedula' ,'',2)
resultado_set(empl_nombres varchar, empl_apellidos varchar)";
$resultado=pg_exec($conexion,$sql) or die("Error al mostrar"); 
if($row=pg_fetch_array($resultado))
{
 $usuario=$row['empl_nombres']." ".$row['empl_apellidos'];
}	
		
$sql_reng="SELECT * FROM  sai_seleccionar_campo ('sai_reng_comp','comp_id,reng_comp,cpat_id,cpat_nombre,rcomp_debe,rcomp_haber,rcomp_tot_db,rcomp_tot_hab','comp_id=''$codigo''','reng_comp',1)
resultado_set(comp_id varchar, reng_comp int8,cpat_id varchar,cpat_nombre varchar,rcomp_debe float8,rcomp_haber float8,rcomp_tot_db float8,rcomp_tot_hab float8)"; 
$resultado=pg_query($conexion,$sql_reng) or die("Error al mostrar"); 
$total_items=pg_num_rows($resultado);
$i=0;
while($row=pg_fetch_array($resultado))
{ 
  $id_comp[$i]=trim($row['comp_id']);
  $comp_reng[$i]=$row['reng_comp'];
  $id_cta[$i]=$row['cpat_id'];
  $nom_cta[$i]=$row['cpat_nombre'];
  $debe[$i]=$row['rcomp_debe'];	
  $haber[$i]=$row['rcomp_haber'];		
  $total_db=$row['rcomp_tot_db'];	
  $total_haber=$row['rcomp_tot_hab'];			
  $i++;
}

$total_imputacion=0;
$sql_presupuesto="SELECT * FROM sai_buscar_datos_causado('".$codigo."','codi') as resultado (categoria varchar, aesp varchar, anno int2, apde_tipo bit,part_id varchar, apde_monto float8)";  
$resultado_pre=pg_query($conexion,$sql_presupuesto) or die ("Error al mostrar datos presupuestarios");
if ($resultado_pre)
{
 $total_imputacion=pg_num_rows($resultado_pre);
 $i=0;

 while ($row_pre=pg_fetch_array($resultado_pre))
 {
  $categoria=$row_pre['categoria'];
  $aesp =$row_pre['aesp'];
  $anno =$row_pre['anno'];
  $apde_tipo =$row_pre['apde_tipo'];
  $apde_partida[$i]=$row_pre['part_id'];
  $apde_monto[$i]=$row_pre['apde_monto'];

  if ($apde_tipo==1){ //Por Proyecto
   $query ="Select * from sai_buscar_centro_gestor_costo_proy('".trim($categoria)."','".trim($aesp)."') as result (centro_gestor varchar, centro_costo varchar)";
  }else{ //Por Accion Centralizada
  	   $query ="Select * from  sai_buscar_centro_gestor_costo_acc('".trim($categoria)."','".trim($aesp)."') as result (centro_gestor varchar, centro_costo varchar)";
	  }

  $resultado_query= pg_exec($conexion,$query);
  if ($resultado_query){
    if($row=pg_fetch_array($resultado_query)){
      $centrog[$i] = trim($row['centro_gestor']);
	  $centroc[$i] = trim($row['centro_costo']);
    }
  }

  $i++;
 }
}

  $sql="SELECT fuef_descripcion FROM sai_fuente_fin WHERE fuef_id = '".$reserva."'";
  $resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
  if($row=pg_fetch_array($resultado_set_most_p))
  {
   $fuente=trim($row['fuef_descripcion']); //Solicitante
  }			

$sql= "select * from sai_buscar_memo_soporte('".$codigo."') as resultado ";
$sql.= "(cod_memo varchar, empl_nombres varchar,empl_apellidos varchar, asunto varchar, contenido text, memo_fecha_crea timestamp, depe_id varchar)";	
$resultado_set_memo = pg_exec($conexion ,$sql);
$valido=$resultado_set_memo;
if ($row_memo = pg_fetch_array($resultado_set_memo))
{
 $hora_memo=substr(trim($row_memo['memo_fecha_crea']),10);
 $memo_fecha=cambia_esp($row_memo['memo_fecha_crea']);
 $memo_contenido=$row_memo['contenido'];
 $memo_responsable=$row_memo['empl_nombres']." ".$row_memo['empl_apellidos'];
}else{
	$memo_fecha="No Registrado";
	$memo_contenido="No Registrado";
	$memo_responsable="No Registrado";
}

class PDF extends FPDF
{
    //Cabecera de P�gina
	function Header()
	{  $alto=4;
	   global $comp_fec;
	   global $codigo;
	   global $tipo;
	   global $comentario;
	   global $categoria;
	   global $aesp;
	   global $fuente;
       global $edo; 
       global $documento;
       global $referencia;
       global $compromiso;

		//Logo
		$this->SetX(50);
		$this->SetY(65);
		$this->Image('../../imagenes/encabezado.jpg',3,22,260,15);
		$this->Ln(3);
		


	 	$this->SetFont('Arial','',8);
	 	$this->SetXY(43.5,40);
	    //N�mero de p�gina
	    $this->Cell(223,10,utf8_decode('Página ').$this->PageNo().' de '.'{nb}',0,0,'R');
	    $this->SetXY(33.5,45);
	    $this->Cell(230,8,'Fecha: '.cambia_esp($comp_fec),0,1,'R');
		$this->SetFont('Arial','B',14);
		//T�tulo
		$posy= $this->gety();
		$this->SetX(3.5);
		$this->SetY(41);
		$this->Cell(250,15,'COMPROBANTE DIARIO '.$codigo,0,1,'C');
		
		
		//Salto de l�nea
		$this->Ln(1);
		$this->Setx(10);
	 	$this->SetFont('Arial','',9);
		$this->SetX(10);
		$this->Cell(125,$alto,"Documento asociado:  ".$documento,1,0,'L');
		$this->Cell(125,$alto,utf8_decode("Nº Referencia bancaria:  ").$referencia,1,2,'L');
        $this->SetX(10);
		$this->Cell(250,$alto,utf8_decode("Justificación:  ").$comentario,1,2,'L');
		$this->SetX(10);
		$this->Cell(250,$alto,utf8_decode("Fuente de financiamiento:  ").$fuente,1,2,'L');
		$this->SetX(10);
		$this->Cell(250,$alto,utf8_decode("Nº Compromiso: ").$compromiso,1,2,'L');

	  	$this->Ln();
		$this->SetX(10);
		$this->SetFont('Arial','B',7);
	}
	//Pie de p�gina
	
	function Footer()
    {  
     global $fecha_emis;
     global $usuario;
     global $edo;
     global $memo_fecha;
     global $memo_responsable;
     global $memo_contenido;
     global $hora_emis;
     global $hora_memo;
     global $dia;
     
     $this->SetX(10);
	 $this->Cell(260,3,utf8_decode($pagina."Este comprobante fue generado el día: "). trim($fecha_emis).$hora_emis."\n   "."por: ".$usuario,0,0,'C');

    if ($edo==15){
     $this->SetFont('Arial','B',7);
     $this->SetX(50);
     $this->SetY(159);
     $this->Cell(260,4,utf8_decode("Nota: Este comprobante fue anulado el día ").$memo_fecha.$hora_memo. " por ".utf8_Decode($memo_responsable),0,0,'C');
     $this->SetX(90);
     $this->SetY(164);
     $this->Cell(260,3,utf8_Decode("Justificación: ").$memo_contenido,0,0,'C');  	
    }
     $this->SetXY(53.5,-25);
	 $this->SetFont('Arial','B',8);
	 //N�mero de p�gina
	 $this->Cell(165,10,utf8_decode('SAFI-Fundación Infocentro'),0,0,'C');
	 $this->SetFont('Arial','',8);
	 $this->SetXY(53.5,-25);
    
	 $this->Cell(165,16,utf8_decode('Fecha de impresión:').'  '.actual_date(),0,0,'C');//.' a las '.date("H:i:s")
    
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
  $pdf->SetFont('Arial','B',9);
  $pdf->SetX(10);
  $posy= $pdf->gety();
  $pdf->Cell(24,$alto,utf8_decode("Nº Registro"),0,0,'C');
  $pdf->Cell(30,$alto,utf8_decode("Cuenta contable"),0,0,'C'); 
  $pdf->Cell(136,$alto,utf8_decode("Descripción"),0,0,'C');
  $pdf->Cell(30,$alto,"Debe",0,0,'C');
  $pdf->Cell(30,$alto,"Haber",0,2,'C');
  $pdf->SetX(5.5);
  $pdf->Cell(24,$alto,"",0,0,'C');
  $pdf->Cell(30,$alto,"",0,0,'C');
  $pdf->Cell(136,$alto,"",0,0,'C');
  $pdf->Cell(30,$alto,"",0,0,'C');
  $pdf->Cell(30,$alto,"",0,2,'C');    
  $pdf->SetXY(10,$posy); 
  $pdf->Cell(24,($alto),"",1,0,'C');
  $pdf->Cell(30,($alto),"",1,0,'C');
  $pdf->Cell(136,($alto),"",1,0,'C');
  $pdf->Cell(30,($alto),"",1,0,'C');
  $pdf->Cell(30,($alto),"",1,2,'C');  
  $pdf->SetFont('Arial','',8);
  for ($ii=0; $ii<$total_items; $ii++)
  {
	$pdf->SetX(10); 
	$pdf->Cell(24,$alto,($comp_reng[$ii]),1,0,'R'); 
	$cuenta=trim($id_cta[$ii]);
 	$convertidor="SELECT * FROM  sai_seleccionar_campo ('sai_convertidor','part_id','cpat_id=''$cuenta''','',2) resultado_set(part_id varchar)"; 
	$resultado_conv=pg_query($conexion,$convertidor) or die("Error al consultar el convertidor"); 
	$partida='---';
	$p=0;
	  while($row=pg_fetch_array($resultado_conv))
	  { 
		$arreglo_partida[$p]=trim($row['part_id']);
		$p++;
	  }
		 $pdf->Cell(30,$alto,trim($id_cta[$ii]),1,0,'L'); 
		 $pdf->Cell(136,$alto,$nom_cta[$ii],1,0,'L'); 
		 $pdf->Cell(30,$alto,(number_format($debe[$ii],2,'.',',')),1,0,'R');  
		 $pdf->Cell(30,$alto,(number_format($haber[$ii],2,'.',',')),1,2,'R');  
  }
  $pdf->Ln();
  $posy= $pdf->gety();
  $pdf->SetFont('Arial','B',8);
  $pdf->SetXY( 5.5,($posy+1));

  $pdf->SetX(170); 
  $pdf->Cell(30,$alto,"Total: ",1,0,'C'); 
  $pdf->Cell(30,$alto,(number_format($total_db,2,'.',',')),1,0,'R');
  $pdf->Cell(30,$alto,(number_format($total_haber,2,'.',',')),1,2,'R');  
  $pdf->SetX( 80); 
  $pdf->Ln();

  $pdf->SetFont('Arial','B',9);
  $pdf->SetX(65);
  $pdf->Cell(136,$alto,utf8_decode("Imputación presupuestaria"),1,2,'C'); 
  $pdf->SetX(65);
  $pdf->SetFont('Arial','',8);
  $posy= $pdf->gety();

  $pdf->Cell(30,$alto,"Proyecto/Acc",1,0,'C');
  $pdf->Cell(25,$alto,utf8_decode("Acción específica"),1,0,'C');
  $pdf->Cell(27,$alto,"Partida",1,0,'C'); 
  $pdf->Cell(28,$alto,utf8_decode("Cuenta"),1,0,'C'); 
  $pdf->Cell(26,$alto,utf8_Decode("Monto"),1,2,'C');
 
for ($ii=0; $ii<$total_imputacion; $ii++)
{
  $pdf->SetX(65);
  $pdf->gety();
  $pdf->Cell(30,$alto,$centrog[$ii],1,0,'C');
  $pdf->Cell(25,$alto,$centroc[$ii],1,0,'C');
  $id_part=$apde_partida[$ii];
  $pdf->Cell(27,$alto,$id_part,1,0,'C'); 
		
  $convertidor="SELECT * FROM  sai_seleccionar_campo ('sai_convertidor','cpat_id','part_id=''$id_part''','',2) resultado_set(cpat_id varchar)"; 
  $resultado_conv=pg_query($conexion,$convertidor) or die("Error al consultar el convertidor"); 
  if($row=pg_fetch_array($resultado_conv))
  { 
   $cuenta=trim($row['cpat_id']);
  }
		  
  $pdf->Cell(28,$alto,$cuenta,1,0,'C');
  $pdf->Cell(26,$alto,(number_format($apde_monto[$ii],2,'.',',')),1,2,'R');  
}

 /* $posy= $pdf->gety();
  $pdf->SetX(10);
  $pdf->SetXY( 5.5,$posy); 
  $pdf->Cell(250,8,"",0,2,'R'); 
  $posy= $pdf->gety();*/
  
  $pdf->Ln();
   if ($edo==15){
	
  $pdf->Image('../../imagenes/anulado.jpg',210,152,46,35);	
  }
  $pdf->Ln();
  

//Se determina el nombre del archivo, se abre y se limpia temporales
$tipo_documento=substr($codigo,0,4);
$archivo = basename(tempnam(getcwd(),'tmp'));
rename($archivo,$archivo.'.pdf');
$archivo.='.pdf';
$pdf->Output($archivo);
?>
<script>
document.write('El sistema est\u00E1 generando el archivo PDF, por favor espere ... ');
</script>
<?php 
echo("<HTML><SCRIPT>window.open('".$archivo."','pdf')</SCRIPT></HTML>");
limpiarTemporalesPdf(dirname(__FILE__));
pg_close($conexion);?>



