<?php
require("../../../includes/conexion.php");
require("../../../lib/fpdf/fpdf.php");
require_once("../../../includes/fechas.php");
include("../../../includes/funciones.php");

$codigo=$_REQUEST['sopg']; 
$comprobante=$_REQUEST['id'];

//*********************************************************************
$sql_p="SELECT * FROM sai_sol_pago WHERE sopg_id='".$codigo."'"; 
$resultado_set_most_p=pg_query($conexion,$sql_p) or die("No hay información para esa solicitud de pago");
$valido=$resultado_set_most_p;
if($row=pg_fetch_array($resultado_set_most_p))
{

  $depe_id=trim($row['depe_solicitante']); //Solicitante
  $tp_sol=trim($row['sopg_tp_solicitud']);
  $sopg_monto=trim($row['sopg_monto']); 
  $fecha_crea=cambia_esp(trim($row['sopg_fecha']));
  $pres_anno=trim($row['pres_anno']);
  $esta_id=trim($row['esta_id']);
  $usua_login=trim($row['usua_login']); //Solicitante
  $sopg_bene_ci_rif=trim($row['sopg_bene_ci_rif']);
  $sopg_bene_tp=trim($row['sopg_bene_tp']);
  $sopg_detalle=trim($row['sopg_detalle']);
  $obs=trim($row['sopg_observacion']);
  $factura_num=trim($row['sopg_factura']);
  $factura_control=trim($row['sopg_factu_num_cont']);
  $factura_fecha=cambia_esp(trim($row['sopg_factu_fecha']));
  
	 		
    //Datos del Solicitante
	$sql_so="select * from sai_buscar_usuario('$usua_login','')
	resultado_set(empl_email varchar, usua_login varchar, usua_activo bool,empl_cedula varchar, empl_nombres varchar,
	empl_apellidos varchar,empl_tlf_ofic varchar,carg_nombre varchar,depe_nombre varchar,depe_id varchar,carg_id varchar)";
	$resultado_set_most_so=pg_query($conexion,$sql_so) or die("Error al consultar partida");
	if($rowso=pg_fetch_array($resultado_set_most_so))
	{
		$cedula=$rowso['empl_cedula'];
		$solicitante=$rowso['empl_nombres'].' '.$rowso['empl_apellidos'];
	}
	
	    //Buscar Nombre del Documento al cual se le asocia la solicitud de pago y el nombre del estado actual
	    $sql_d="select * from sai_buscar_datos_sopg('1',4,'','','$codigo','','',0) 
		resultado_set(docu_nombre varchar, esta_id int4, docg_prioridad int2, esta_nombre varchar)"; 
		$resultado_set_most_d=pg_query($conexion,$sql_d) or die("Error al consultar documento");
		if($rowd=pg_fetch_array($resultado_set_most_d))
		{ 
		    $prioridad=$rowd['docg_prioridad'];
			if (trim($rowd['docu_nombre'])<>"") 
			{
		   $asunto=$rowd['docu_nombre'];
		   
		   }
		}
		
	//Buscar datos del benefiario segun sea el tipo (1:sai_empleado 2_sai_proveedor 3:sai_viat_benef)
	if($sopg_bene_tp==1) //Empleado
	{
	 	$sql_be="select * from sai_buscar_datos_sopg('$sopg_bene_ci_rif',1,'','','','','',0) 
		resultado_set(depe_id varchar, depe_nombre varchar,empl_nombres varchar,empl_apellidos varchar)"; 
		$resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar empleado");
		if($rowbe=pg_fetch_array($resultado_set_most_be))
		{
		  // $nombre_bene=utf8_decode($rowbe['empl_nombres']).' '.utf8_decode($rowbe['empl_apellidos']);
 		$nombre_bene=$rowbe['empl_nombres'].' '.$rowbe['empl_apellidos'];
		   
		}
	}
	else
	   if($sopg_bene_tp==2) //Proveedor
	   {
	       $sql_be="SELECT * FROM sai_seleccionar_campo('sai_proveedor_nuevo','prov_nombre','prov_id_rif='||'''$sopg_bene_ci_rif''','',2) 
		   resultado_set(prov_nombre varchar)"; 
		   $resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar proveedor");
		   if($rowbe=pg_fetch_array($resultado_set_most_be))
		   {
		   	  $nombre_bene=$rowbe['prov_nombre'];
		     //La dependencia es la del solicitante (buscarla por el usua_login registrado)
			 
		   }
	   }
	   else
	       if($sopg_bene_tp==3) //Otro beneficiario
		   {
			   $sql_be="SELECT * FROM sai_seleccionar_campo('sai_viat_benef','benvi_nombres,benvi_apellidos','benvi_cedula='||'''$sopg_bene_ci_rif''','',2) 
			   resultado_set(benvi_nombres varchar,benvi_apellidos varchar)"; 
			   $resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar otro beneficiario");
			   if($rowbe=pg_fetch_array($resultado_set_most_be))
			   {
				  $nombre_bene=$rowbe['benvi_nombres'].' '.$rowbe['benvi_apellidos'];
				  $apellido_bene=$rowbe['benvi_apellidos'];
				 //La dependencia es la del solicitante (buscarla por el usua_login registrado)
				  
			   }
		   }
	
	
}//fin de consultar solicitud de pago
if ($valido){
	$sql= "select * from sai_seleccionar_campo ('sai_sol_pago_retencion','sopg_ret_monto,sopg_por_rete,sopg_monto_base','sopg_id='||'''$codigo'' and impu_id=''LTF''','',2)  resultado_set (sopg_ret_monto float8,sopg_por_rete float4,sopg_monto_base float8)";
	$resultado_set= pg_exec($conexion ,$sql) or die(utf8_decode("Error al consultar la retención"));
	
	if($row_rete_doc=pg_fetch_array($resultado_set))	
	{
         $valido_ltf="si";
				$rete_monto_doc=trim($row_rete_doc['sopg_ret_monto']);
				$por_rete_doc=trim($row_rete_doc['sopg_por_rete']);
				$monto_base=trim($row_rete_doc['sopg_monto_base']);
	}
}


	/*Consulto las retenciones previas del documento */
if ($valido_ltf=="si")
  {

	$sql_fecha="select * from sai_seleccionar_campo('sai_comp_diario','comp_fec','comp_doc_id='||'''$codigo'' and esta_id<>15','',2) resultado_set (comp_fec date)";
	$resultado_set=pg_exec($conexion,$sql_fecha) or die ("Error al consultar fecha del causado");

	if ($rowf=pg_fetch_array($resultado_set)){
	 $fecha_causado=cambia_esp(trim($rowf['comp_fec']));
	 list($dia,$mes,$an_o) = explode ("/",$fecha_causado);
	 
	}


	
/**************************************************************************/

//Colocamos la imagen del ministerio del lado derecho
class PDF extends FPDF
{
    //Cabecera de p�gina
	function Header()
	{  $alto=4;
	   global $fecha_crea;
	   global $requisi;
	   global $analisis ;
	   global $codigo;
	   global $reserva;
	   global $fecha_causado;
	   global $secuencia;
	   global $mes;
	   global $an_o;
	   global $comprobante;
  

		//Logo
		$this->SetX(70);//35
		$this->SetY(65);//
		//$this->Image('../../imagenes/encabezado.jpg',3,14.5,190,12);
		$this->Image('../../../imagenes/encabezado.jpg',25,30,170,15);
		$this->Ln(2);
		//Arial bold 15
		$this->SetFont('Arial','B',12);
		//T�tulo
		$posy= $this->gety();
		$this->SetX(30.5);
		$this->SetY(50);
		$this->Cell(199,15,utf8_decode('COMPROBANTE DE RETENCIÓN'),0,1,'C');
		$this->SetX(23.5);
		$this->SetY(50);
		$this->Cell(199,25,utf8_decode('TIMBRE FISCAL'),0,1,'C');
		//Salto de l�nea
		$this->Ln(1);
	   	//Movernos a la derecha
		$this->SetXY(-51.5,52);
	    	$this->SetFont('Arial','B',7);
	    	//$this->Cell(30,5,utf8_decode("1.Nº: ").$codigo,1,0,'L');
		$this->Cell(35,5,utf8_decode("1.Nº: ".$comprobante),1,0,'L');
		$this->SetXY(-51.5,57);
		$this->Cell(35,5,"2.Fecha: ".$fecha_causado,1,0,'L');
		$this->SetXY(-51.5,62);
		$this->Cell(35,5,utf8_decode("3.N° sopg: ").$codigo,1,0,'L');

	}
	
	
	
	//Pie de p�gina
	//**********************************************************************

 function Footer()
{
  /*$this->Ln(1);
    $this->Ln(1);
    $this->SetX(3.5);
    $this->SetFont('Arial','B',5);
    //N�mero de p�gina
    $this->Cell(0,2,'MCT-DAS-01-03/06(m)',0,0,'l');
	$this->Cell(0,2,'P�gina '.$this->PageNo().'/{nb}',0,1,'R');*/
}
     
}		 


//**********************************************************************
  $pdf=new PDF('P','mm','A4');
// $pdf=new PDF('P','mm','letter');
  $pdf->AddPage();
  $pdf->AliasNbPages();  //Alias para el numero total de pagina
  $alto=4;
  $pdf->Cell(189,3,'',0,1,'C');
 // $posy= $pdf->gety();
  $posy=75;
  $pdf->SetFont('Arial','B',7);
  $pdf->SetXY(26.5,$posy);
  $pdf->Cell(168,4,utf8_decode('DATOS DEL AGENTE DE RETENCIÓN'),1,1,'C');
  
  $pdf->SetFont('Arial','B',5);
  $posy= $pdf->gety();
  $pdf->SetXY(26.5,$posy); 
	
  $pdf->Cell(168,8,utf8_decode("NOMBRE O RAZÓN SOCIAL: "),1,2,'L');
  $pdf->SetXY(26.5,($posy+3)); 
  $pdf->SetFont('Arial','B',7);
  $pdf->Cell(168,8,utf8_decode("FUNDACIÓN INFOCENTRO"),0,2,'L'); 
  $pdf->SetFont('Arial','B',5);
  $posy= $pdf->gety();
  $pdf->SetXY(26.5,$posy-3); 
  $pdf->Cell(90,8,utf8_decode("REGISTRO DE INFORMACIÓN FISCAL: "),1,2,'L');
  $pdf->SetXY(26.5,($posy)); 
  $pdf->SetFont('Arial','B',7);
  $pdf->Cell(90,8,utf8_decode("G-20007728-0"),0,2,'L'); 
  $pdf->SetXY(116.5,$posy-3); 
  $pdf->SetFont('Arial','B',5);
  $pdf->Cell(78,8,utf8_decode("PERÍODO FISCAL\n"),1,2,'L');
  $pdf->SetXY(116.5,$posy); 
  $pdf->SetFont('Arial','B',7);
  $pdf->Cell(78,8,utf8_decode("AÑO ".$an_o." MES ".$mes),0,2,'L'); //Si no se coloca el getX no funciona
  $pdf->SetFont('Arial','B',5);
  $posy= $pdf->gety();
  $pdf->SetXY(26.5,($posy-3)); 
  $pdf->Cell(168,8,utf8_decode("DIRECCIÓN DEL AGENTE DE RETENCIÓN: "),1,2,'L');
  $pdf->SetXY(26.5,($posy)); 
  $pdf->SetFont('Arial','B',7);
  $pdf->Cell(168,8,utf8_decode("AV. UNIVERSIDAD CON ESQ. EL CHORRO, LA HOYADA, EDIF. TORRE MCT, PISO 11"),0,2,'L'); 

  $posy= $pdf->gety();
  $pdf->SetFont('Arial','B',7);
  $pdf->SetXY(26.5,$posy+5);
  $pdf->Cell(168,4,utf8_decode('DATOS DE SUJETO DE RETENCIÓN'),1,2,'C');
  $pdf->SetFont('Arial','B',5);
  $posy= $pdf->gety();
  $posyy= $pdf->gety();

  
  $pdf->SetXY(26.5,$posyy);
  $pdf->Cell(90,8,utf8_decode("NOMBRE O RAZÓN SOCIAL: "),1,2,'L');
  $pdf->SetXY(26.5,($posy+3)); 
  $pdf->SetFont('Arial','B',7);
  $pdf->Cell(90,8,html_entity_decode(htmlentities($nombre_bene)),0,2,'l'); //cuadro
  $pdf->SetFont('Arial','B',5);
  $pdf->SetXY(116.5,$posy); 
  $pdf->SetFont('Arial','B',5);
  $pdf->Cell(78,8,utf8_decode("REGISTRO DE INFORMACIÓN FISCAL\n"),1,2,'L');
  $pdf->SetXY(116.5,$posy+3); 
  $pdf->SetFont('Arial','B',7);
  $pdf->Cell(78,8,$sopg_bene_ci_rif,0,2,'L'); //Si no se coloca el getX no funciona

/*$posy= $pdf->gety();
$pdf->SetXY(26.5,($posy+1));
$pdf->Cell(168,4,utf8_decode('IMPUTACIÓN PRESUPUESTARIA'),1,2,'C');
$pdf->SetFont('Arial','B',5);*/
$pdf->SetFont('Arial','B',5);
$posy= $pdf->gety();
$pdf->SetXY(26.5,$posy);

  $pdf->SetX(26.5); 
  $pdf->Cell(10.4,$alto,utf8_decode("OPER. N°"),1,0,'C'); 
  $pdf->Cell(15.4,$alto,utf8_decode("FECHA PAGO"),1,0,'C'); 
  $pdf->Cell(20.4,$alto,utf8_decode("N° FACTURA"),1,0,'C'); 
  $pdf->Cell(20.4,$alto,utf8_decode("N°CONTROL"),1,0,'C'); 
  $pdf->Cell(20.4,$alto,"BASE IMPONIBLE",1,0,'C'); 
  $pdf->Cell(20.4,$alto,utf8_decode("% RETENCIÓN"),1,0,'C'); 
  $pdf->Cell(28.4,4,"IMPUESTO RETENIDO",1,0,'C'); 
  $pdf->Cell(32.2,$alto,"IMPUESTO RETENIDO ACUMULADO",1,2,'C');

  $posx= $pdf->getx();
  $posyy= $pdf->gety();
  $posy= $pdf->gety();
  $pdf->Setx( $posx);
  
  
  $pdf->SetXY($posx,$posy); 
  $pdf->Cell(50,6,"",0,2,'R'); //Si no se coloca el getX no funciona
  $posy= $pdf->gety();
  
  $pdf->Sety($posyy); 
  $pdf->SetX(26.5); 
  $pdf->Cell(10.4,$alto,"1",1,0,'R'); 
  $pdf->Cell(15.4,$alto ,$fecha_causado,1,0,'R'); 
  $pdf->Cell(20.4,$alto ,$factura_num,1,0,'R'); 
  $pdf->Cell(20.4,$alto ,$factura_control,1,0,'R'); 
  $pdf->Cell(20.4,$alto ,$monto_base,1,0,'R'); 
  $pdf->Cell(20.4,$alto ,$por_rete_doc,1,0,'R'); 
  $pdf->Cell(28.4,$alto ,$rete_monto_doc,1,0,'R'); 
  $pdf->Cell(32.2,$alto ,$rete_monto_doc,1,2,'R'); 

 for ($i=0; $i<9;$i++)
 {
  $posy= $pdf->gety();
  $pdf->Sety($posy); 
  $pdf->SetX(26.5); 
  $pdf->Cell(10.4,$alto,"",1,0,'R'); 
  $pdf->Cell(15.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(20.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(20.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(20.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(20.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(28.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(32.2,$alto ,"",1,2,'R'); 
 }
  $pdf->SetX(26.5);
  $pdf->SetFont('Arial','B',7);
  $pdf->Cell(10.4,$alto,"",1,0,'R'); 
  $pdf->Cell(15.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(20.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(20.4,$alto ,"TOTAL",1,0,'R'); 
  $pdf->Cell(20.4,$alto ,$monto_base,1,0,'R'); 
  $pdf->Cell(20.4,$alto ,$por_rete_doc,1,0,'R'); 
  $pdf->Cell(28.4,$alto ,$rete_monto_doc,1,0,'R'); 
  $pdf->Cell(32.2,$alto ,$rete_monto_doc,1,2,'R'); 
  $pdf->SetFont('Arial','B',5);
  $pdf->SetX(6.5); 
  
  $pdf->Ln(); 
  $pdf->Ln(); 
  $posy= $pdf->gety();
  $pdf->SetXY(7.5,$posy+1);
  $pdf->SetFont('Arial','B',7);
  $pdf->SetXY(26.5,$posy);
  $pdf->Cell(168,4,'FIRMAS',1,2,'C');
  $pdf->SetFont('Arial','B',5);
  $posy= $pdf->gety();

   $pdf->SetXY(26.5,$posy);
   $pdf->MultiCell(84,6,"\n\n___________________________\n".utf8_decode("Firma del agente de Retención")."\n" ,1,'C',0);
   $pdf->SetXY(110.5,$posy);
   $pdf->MultiCell(84,6,"\n\n___________________________\n".utf8_decode("Firma del Sujeto Retenido")."\n",1,'C',0);
   $pdf->Ln(); 
   

$tipo_documento=substr($codigo,0,4);
//**** Determinamos un nombre temporal para el pdf
$archivo = basename(tempnam(getcwd(),'tmp'));
rename($archivo,$archivo.'.pdf');
$archivo.='.pdf';
//Guardar el pdf en un fichero
$pdf->Output($archivo);
//Redirecci�n con JavaScript
?>
<script>
document.write('Cargando archivo PDF, por favor espere ... ');
</script>
<?php 
echo("<HTML><SCRIPT>window.open('$archivo')</SCRIPT></HTML>");
limpiarTemporalesPdf(dirname(__FILE__));
} else{?>
	<br>
	<div align="center">La solicitud de pago ingresada no tiene Retenci&oacute;n del Timbre Fiscal</div>
	<br>
	<div align="center"><a href="index_comprobantes.php">Volver</a></div>
       <?}

?> 

