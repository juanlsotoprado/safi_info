<?php
require("../../includes/conexion.php");
require("../../lib/fpdf/fpdf.php");
require_once("../../includes/fechas.php");
include("../../includes/funciones.php");
//codigo del documento
$codigo=$_GET['cod_doc']; 
$anulado=$_GET['anulado'];
//*********************************************************************
//Cargamos los Datos del Comprobante de Diario	
$sql="SELECT * FROM  sai_seleccionar_campo ('sai_comp_diario','comp_id,comp_fec,comp_tipo,comp_comen,comp_fec_emis,esta_id,depe_id,comp_doc_id','comp_id=''$codigo''','',2)
resultado_set(comp_id varchar, comp_fec date,comp_tipo varchar,comp_comen text,comp_fec_emis timestamp,esta_id int4, depe_id varchar, comp_doc_id varchar)"; 
$resultado=pg_query($conexion,$sql) or die("Error al mostrar"); 
while($row=pg_fetch_array($resultado))
			{ 
				$id_comp=trim($row['comp_id']);
				$comp_fec=trim($row['comp_fec']);
				$tipo=trim($row['comp_tipo']);
				$comentario=trim($row['comp_comen']);
				$fecha_emis=trim($row['comp_fec_emis']);
				$id_depe=trim($row['depe_id']);
				$comp_doc=trim($row['comp_doc_id']);
		} 	

$sub=substr($comp_doc,0,4);

if ($sub=="pgch"){

 $sql="SELECT * FROM sai_seleccionar_campo('sai_pago_cheque','docg_id','pgch_id=''$comp_doc''','',2) resultado_set(docg_id varchar)";		 
 $resultado=pg_query($conexion,$sql) or die ("Error al consultar el cod sopg");
 if ($row=pg_fetch_array($resultado)){
  $comp_doc=$row['docg_id'];
 }

}

			
$sql_reng="SELECT * FROM  sai_seleccionar_campo ('sai_reng_comp','comp_id,reng_comp,cpat_id,cpat_nombre,rcomp_debe,rcomp_haber,rcomp_tot_db,rcomp_tot_hab','comp_id=''$codigo''','reng_comp',1)
resultado_set(comp_id varchar, reng_comp int8,cpat_id varchar,cpat_nombre varchar,rcomp_debe float8,rcomp_haber float8,rcomp_tot_db float8,rcomp_tot_hab float8)"; 
$resultado=pg_query($conexion,$sql_reng) or die("Error al mostrar"); 
$total_items=pg_num_rows($resultado);
$i=0;
$total_db=0;
$total_haber=0;
while($row=pg_fetch_array($resultado))
			{ 
				$id_comp[$i]=trim($row['comp_id']);
				$comp_reng[$i]=$row['reng_comp'];
				$id_cta[$i]=$row['cpat_id'];
				$nom_cta[$i]=$row['cpat_nombre'];
				$debe[$i]=$row['rcomp_debe'];	
				$haber[$i]=$row['rcomp_haber'];		
				$total_haber=$total_haber+$haber[$i];  
				$total_db=$total_db+$debe[$i];		
			    $i++;
			}
			
$total_imputacion=0;
$sql_presupuesto="SELECT * FROM sai_buscar_datos_causado('".$comp_doc."','coda') as resultado (categoria varchar, aesp varchar, anno int2, apde_tipo bit,part_id varchar, apde_monto float8)";  
$resultado_pre=pg_query($conexion,$sql_presupuesto) or die ("Error al mostrar datos presupuestarios");
if ($resultado_pre)
{
$total_imputacion=pg_num_rows($resultado_pre);
$i=0;

while ($row_pre=pg_fetch_array($resultado_pre)){
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
$sql_nombre_presu="SELECT * FROM sai_consulta_proyecto_accion('".$categoria."','".$aesp."','$apde_tipo','$anno') as resultado (nombre_categ varchar, nombre_esp varchar, cg varchar, cc varchar)";

$resultado_nomb_pre=pg_query($conexion,$sql_nombre_presu) or die ("Error al buscar datos presupuestarios II");
if ($row_pre=pg_fetch_array($resultado_nomb_pre)){
 $nom_categoria=$row_pre['nombre_categ'];
 $nom_aesp =$row_pre['nombre_esp'];
}
	$i++;
}
}
$sql_documento="SELECT * FROM sai_seleccionar_campo('sai_sol_pago','numero_reserva,comp_id','sopg_id=''$comp_doc''','',2) resultado_set(numero_reserva varchar,comp_id varchar)";  
$resultado_doc=pg_query($conexion,$sql_documento) or die ("Error al mostrar datos del documento");
if ($row_doc=pg_fetch_array($resultado_doc)){
 $reserva=$row_doc['numero_reserva'];
 $num_comp=$row_doc['comp_id'];
}

//$reserva=substr($reserva,0,2);
		$sql="SELECT * FROM sai_seleccionar_campo('sai_fuente_fin','fuef_descripcion','fuef_id ='||'''$reserva''','',2) resultado_set(fuef_descripcion varchar)";
		$resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
				if($row=pg_fetch_array($resultado_set_most_p))
				{
 				 $fuente=trim($row['fuef_descripcion']); //Solicitante
				}
  	  $sql= " Select * from sai_buscar_sopg_imputacion('".trim($comp_doc)."') as result ";
	  $sql.= " (sopg_id varchar, sopg_acc_pp varchar, sopg_acc_esp varchar, depe_id varchar, sopg_sub_espe varchar, sopg_monto float8, tipo bit,sopg_monto_exento float8)";
	  $resultado_set= pg_exec($conexion ,$sql);
	  $valido=$resultado_set;
		if ($resultado_set)
  		{
		$total_imputacion=pg_num_rows($resultado_set);
		$i=0;
		while($row=pg_fetch_array($resultado_set))	
		{
			$matriz_imputacion[$i]=trim($row['tipo']);
			$matriz_acc_pp[$i]=trim($row['sopg_acc_pp']); // proy o acc
			$matriz_acc_esp[$i]=trim($row['sopg_acc_esp']); // acc esp
			$matriz_sub_esp[$i]=trim($row['sopg_sub_espe']); //sub-part
			$matriz_monto[$i]=trim($row['sopg_monto']+$row['sopg_monto_exento']); //monto
			$i++;
			}}


/**************************************************************************/
//Cargamos la imagen del Ministerio 
class PDF extends FPDF
{
    //Cabecera de P�gina
	function Header()
	{  $alto=4;
	   global $comp_fec;
	   global $codigo;
	   global $tipo;
	   global $comentario;
	   global $comp_doc;
	   global $nom_categoria;
	   global $nom_aesp;
	   global $categoria;
	   global $aesp;
	   global $fuente;
	   global $anulado;
	   global $num_comp;

		//Logo
		$this->SetX(50);
		$this->SetY(45);
		$this->Image('../../imagenes/encabezado.jpg',3,22,260,15);
		$this->Ln(3);
		//Arial bold 15
		$this->SetFont('Arial','B',14);
		//T�tulo
		$posy= $this->gety();
		$this->SetX(25);
		$this->Cell(250,15,utf8_decode('COMPROBANTE DE DIARIO AUTOMÁTICO '),0,1,'C');
		//Salto de l�nea
		$this->Ln(1);
		$this->Setx(10);
	 	$this->SetFont('Arial','',9);
		$this->Cell(250,$alto,utf8_decode("N° Comprobante:  ").$codigo,1,2,'L');
		$this->Setx(10);
		$this->Cell(83,$alto,utf8_decode("N° Solicitud de Pago: ").$comp_doc,1,0,'L');
		$this->Cell(83,$alto,utf8_decode("Fuente de Financiamiento:  ").$fuente,1,0,'L');
        $this->Cell(84,$alto,utf8_decode("Número de Compromiso:  ").$num_comp,1,2,'L');				
 		$this->SetX(10);
		$this->Cell(250,$alto,"Fecha del Comprobante:  ".$comp_fec,1,1,'L');
        $this->SetX(10);
		$this->Cell(250,$alto,"Comentario:  ".$comentario,1,2,'L');
		//$this->Cell(250,$alto,utf8_decode("Categoría Programática:         ").$categoria.":".$nom_categoria,1,2,'L');
		//$this->SetX(10);
	//	$this->Cell(250,$alto,utf8_decode("Acc. Específica:  ").$aesp." : ".$nom_aesp,1,2,'L');
	//	$this->SetX(10);
	  	$this->Ln();
		$this->SetX(10);
		$this->SetFont('Arial','B',7);
	}
	//Pie de p�gina
	//**********************************************************************

 function Footer()
{  
    global $fecha_emis;
	$this->Cell(260,3,utf8_decode("ESTE COMPROBANTE FUE GENERADO EL DÍA: "). trim($fecha_emis)."\n   ",0,0,'C');
}
     
}		 


//**********************************************************************
  $pdf=new PDF('L','mm','Letter');
  $pdf->AddPage();
  $pdf->AliasNbPages();  //Alias para el n�mero total de p�gina
  $alto=4;
  $posy= $pdf->gety();
  $pdf->SetFont('Arial','B',6);
  $pdf->SetXY(10,($posy+1)); 
  $pdf->Cell(50,$alto,"",0,0,'l'); 
  $pdf->SetXY(10,($posy+1)); 
  $pdf->SetFont('Arial','B',9);
  $pdf->SetX(10);
  $posy= $pdf->gety();
  $pdf->Cell(24,$alto,"RENG",1,0,'C'); 
  $pdf->Cell(30,$alto,utf8_decode("CÓDIGO"),1,0,'C'); 
  $pdf->Cell(136,$alto,utf8_Decode("DESCRIPCIÓN"),1,0,'C');
  $pdf->Cell(30,$alto,"DEBE",1,0,'C');
  $pdf->Cell(30,$alto,"HABER",1,2,'C');
  $pdf->SetX(10);

  $pdf->SetFont('Arial','',8);
  for ($ii=0; $ii<$total_items; $ii++)
  {
	$pdf->SetX(10); 
	$pdf->Cell(24,$alto,trim($comp_reng[$ii]),1,0,'R'); 
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

 	for ($j=0; $j<$p; $j++)
        {
 	 $query_partida="SELECT t3.part_id as partida FROM  sai_apartado t2, sai_apar_det t3 WHERE t2.apar_id=t3.apar_id and t2.apar_docu_id='".$comp_doc."' and t3.part_id='".trim($arreglo_partida[$j])."' and (t3.apde_monto='".$debe[$ii]."' or t3.apde_monto='".$haber[$ii]."')"; 

	 $resultado_part=pg_query($conexion,$query_partida) or die("Error al consultar el convertidor"); 
	 if ($row=pg_fetch_array($resultado_part)){
 		$partida=$row['partida'];
	 }
        }

		 $pdf->Cell(30,$alto,trim($id_cta[$ii]),1,0,'L'); 
		 $pdf->Cell(136,$alto,$nom_cta[$ii],1,0,'L'); 
		 $pdf->Cell(30,$alto,(number_format($debe[$ii],2,'.',',')),1,0,'R');  
		 $pdf->Cell(30,$alto,(number_format($haber[$ii],2,'.',',')),1,2,'R');  
    }
  

  $pdf->Ln();
  $posy= $pdf->gety();
  $pdf->SetFont('Arial','B',6);
  $pdf->SetXY( 3.5,($posy+1));

  $pdf->SetX(170); 
  $pdf->Cell(30,$alto,"TOTAL: ",1,0,'C'); 
  $pdf->Cell(30,$alto,(number_format($total_db,2,'.',',')),1,0,'R');
  $pdf->Cell(30,$alto,(number_format($total_haber,2,'.',',')),1,2,'R');  
  $pdf->SetX( 80); 
  $posy= $pdf->gety();
  $pdf->SetX( 3.5);
  $pdf->SetXY( 3.5,$posy); 
  $pdf->Cell(204,8,"",0,2,'R'); //Si no se coloca el getX no funciona
  $posy= $pdf->gety();
  
  $pdf->SetFont('Arial','B',9);
  $pdf->SetX(65);
  $pdf->Cell(136,$alto,utf8_decode("IMPUTACIÓN PRESUPUESTARIA"),1,2,'C'); 
  $pdf->SetX(65);
  $pdf->SetFont('Arial','',8);
  $posy= $pdf->gety();
  $pdf->Cell(30,$alto,"PROYECTO/ACC",1,0,'C');
  $pdf->Cell(25,$alto,"ACC. ESP",1,0,'C');
  $pdf->Cell(45,$alto,"PARTIDA",1,0,'C'); 
  $pdf->Cell(45,$alto,utf8_decode("CUENTA"),1,0,'C'); 
  $pdf->Cell(46,$alto,utf8_Decode("MONTO"),1,2,'C');
 
		for ($ii=0; $ii<$total_imputacion; $ii++)
    	        {
		$pdf->SetX(65);
		$pdf->gety();
		$pdf->Cell(30,$alto,$centrog[$ii],1,0,'C');
  		$pdf->Cell(25,$alto,$centroc[$ii],1,0,'C');
		$id_part=$matriz_sub_esp[$ii];
        $pdf->Cell(45,$alto,$id_part,1,0,'C'); 
		$convertidor="SELECT * FROM  sai_seleccionar_campo ('sai_convertidor','cpat_id','part_id=''$id_part''','',2) resultado_set(cpat_id varchar)"; 
          	   $resultado_conv=pg_query($conexion,$convertidor) or die("Error al consultar el convertidor"); 
		  if($row=pg_fetch_array($resultado_conv))
		  { 
		   $cuenta=trim($row['cpat_id']);
		  }
		$pdf->Cell(45,$alto,$cuenta,1,0,'C');
		$pdf->Cell(46,$alto,(number_format($matriz_monto[$ii],2,'.',',')),1,2,'R');  
		}
		

  $pdf->Ln();
  if ($anulado==1){
  $pdf->Image('../../imagenes/anulado.jpg',210,152,46,35);	
}
  $pdf->Ln();

//Se determina el nombre del archivo, se firma, se abre y se limpia temporales
$tipo_documento=substr($codigo,0,4);

//include("../pdf_con_firma.php");
$archivo = basename(tempnam(getcwd(),'tmp'));
rename($archivo,$archivo.'.pdf');
$archivo.='.pdf';
$pdf->Output($archivo);
?>
<script>
document.write('Cargando archivo PDF, por favor espere ... ');
</script>
<?php 
echo("<HTML><SCRIPT>window.open('".$archivo."','pdf')</SCRIPT></HTML>");
limpiarTemporalesPdf(dirname(__FILE__));
pg_close($conexion);