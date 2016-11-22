<?php
require("../../../includes/conexion.php");
require("../../../lib/fpdf/fpdf.php");
require_once("../../../includes/fechas.php");
include("../../../includes/funciones.php");

function completar_digitos($cadena,$longitud)
{
 for ($tamano= strlen($cadena); $tamano<$longitud; $tamano++)
 { 
  $cadena = "0".$cadena;
 }
 return $cadena;
}

//codigo del documento
$codigo=$_POST['cod_sopg']; 
$secuencia=$_POST['secuencia'];
$rif=$_POST['rif'];
$proveedor=$_POST['proveedor'];
$secuencia=completar_digitos($secuencia,8);
$valido_iva="no";
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


  $sql="SELECT * FROM sai_seleccionar_campo('sai_tipo_solicitud','nombre_sol','id_sol='||'''$tp_sol''','',2) 
  resultado_set(nombre_sol varchar)"; 
  $resultado_set_most_be=pg_query($conexion,$sql) or die("Error al consultar tipo de solicitud");
  $valido= $resultado_set_most_be;
  if($rowbe=pg_fetch_array($resultado_set_most_be))
  {
   $nombre_sol=$rowbe['nombre_sol'];
  }
	 		
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


	
	
if ($valido)
  {
	  //Buscar las Imputaciones
	  $sql= " Select * from sai_buscar_sopg_imputacion('".trim($codigo)."') as result ";
	  $sql.= " (sopg_id varchar, sopg_acc_pp varchar, sopg_acc_esp varchar, depe_id varchar, sopg_sub_espe varchar, sopg_monto float8, tipo bit, sopg_monto_exento float)";
	  $resultado_set= pg_exec($conexion ,$sql);
	  $valido=$resultado_set;
	  $monto_exento=0;
	  $monto_sujeto=0;
	  while($row=pg_fetch_array($resultado_set))	
	  {
	   $monto_sujeto=$monto_sujeto+trim($row['sopg_monto']); //monto
	   $monto_exento=$monto_exento+trim($row['sopg_monto_exento']); //monto
	  }

	 //Datos del IVA
	 $sql=  " select * from sai_buscar_docu_iva('".trim($codigo)."') as result  ";
   	 $sql.= "( docg_id varchar,ivap_porce float4, docg_monto_base float8, docg_monto_iva float8)";
         $resultado_set= pg_exec($conexion ,$sql);
	 if($row_iva=pg_fetch_array($resultado_set))	
	 {
	   $iva_monto=trim($row_iva['docg_monto_iva']);
	   $iva_porce=trim($row_iva['ivap_porce']);
	   $iva_base=trim($row_iva['docg_monto_base']);
	 }

	/*Consulto las retenciones previas del documento */
	$sql= "select * from sai_seleccionar_campo ('sai_sol_pago_retencion','sopg_ret_monto,sopg_por_rete,sopg_monto_base','sopg_id='||'''$codigo'' and impu_id=''IVA''','',2)  resultado_set (sopg_ret_monto float8,sopg_por_rete float4,sopg_monto_base float8)";
	$resultado_set= pg_exec($conexion ,$sql) or die(utf8_decode("Error al consultar la retención"));
	$valido=$resultado_set;
	if($row_rete_doc=pg_fetch_array($resultado_set))	
	{
	$valido_iva="si";
				$rete_monto_doc=trim($row_rete_doc['sopg_ret_monto']);
				$por_rete_doc=trim($row_rete_doc['sopg_por_rete']);
				$monto_base=trim($row_rete_doc['sopg_monto_base']);
			 }
			

	$sql_fecha="select * from sai_seleccionar_campo('sai_comp_diario','comp_fec','comp_doc_id='||'''$codigo'' and esta_id<>15','',2) resultado_set (comp_fec date)";
	$resultado_set=pg_exec($conexion,$sql_fecha) or die ("Error al consultar fecha del causado");

	if ($rowf=pg_fetch_array($resultado_set)){
	 $fecha_causado=cambia_esp(trim($rowf['comp_fec']));
	 list($dia,$mes,$an_o) = explode ("/",$fecha_causado);
	 
	}
	}

	if ($valido_iva=="si")
        {
	
  	$id_compr=$an_o."-".$mes."-".$secuencia;
	
	$sql_insert="select * from sai_insert_comprobante ('".$id_compr."','".$fecha_causado."','".$codigo."','".$sopg_bene_ci_rif."','".$nombre_bene."','IVA') as resultado";
	$resultado=pg_exec($conexion,$sql_insert) or die (utf8_decode("El número de comprobante ya existe"));

	/*$sql="SELECT max(compr_id) as max_seq FROM sai_comprobante_iva"; 
	$resultado=pg_exec($conexion,$sql);
        if ($rowf=pg_fetch_array($resultado)){
	 $max_seq=(int)substr(trim($rowf['max_seq']),9,18);
	}*/
    
    if ($secuencia==1){
      $a=date(Y);
      $sql_act="update sai_seq_comprobante set comp_num='".$secuencia."', anno='".$a."' where comp_tipo='IVA'";
    }else{
	    $sql_act="update sai_seq_comprobante set comp_num='".$secuencia."' where comp_tipo='IVA'";
    }
	$resultado=pg_exec($conexion,$sql_act);
 	
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
	   global $nombre_sol;
	   global $sopg_monto;
	   global $iva_monto;
	   global $iva_porce;
	   global $iva_base;
  	   global $rif;
	   global $proveedor;



		$this->SetX(70);
		$this->SetY(65);
		$this->Image('../../../imagenes/encabezado.jpg',15,22,260,15);
		$this->Ln(3);
		//Arial bold 15
		$this->SetFont('Arial','B',14);
		//T�tulo
		$posy= $this->gety();
		$this->SetX(30.5);
		$this->SetY(45);
		$this->Cell(250,15,utf8_decode('COMPROBANTE DE RETENCIÓN'),0,1,'C');
		$this->SetX(30.5);
		$this->SetY(45);
		$this->Cell(250,25,'IMPUESTO AL VALOR AGREGADO',0,1,'C');


		//Salto de l�nea
		$this->Ln(1);
	   	//Movernos a la derecha
		$this->SetXY(-77,52);
	    	$this->SetFont('Arial','B',7);
	    	//$this->Cell(30,5,utf8_decode("1.Nº: ").$codigo,1,0,'L');
		$this->Cell(30,5,utf8_decode("1.Nº: ".$an_o."-".$mes."-".$secuencia),1,0,'L');
		$this->SetXY(-77,57);
		$this->Cell(30,5,"2.Fecha: ".$fecha_causado,1,0,'L');
	

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
  $pdf=new PDF('L','mm','letter');
// $pdf=new PDF('P','mm','letter');
  $pdf->AddPage();
  $pdf->AliasNbPages();  //Alias para el numero total de pagina
  $alto=4;
  $pdf->Cell(189,3,'',0,1,'C');
 // $posy= $pdf->gety();
  $posy=70;
  $pdf->SetFont('Arial','B',7);
  $pdf->SetXY(26.5,$posy);
  $pdf->Cell(222.8,4,utf8_decode('DATOS DEL AGENTE DE RETENCIÓN'),1,1,'C');
  
  $pdf->SetFont('Arial','B',5);
  $posy= $pdf->gety();
  $pdf->SetXY(26.5,$posy); 
	
  $pdf->Cell(222.8,8,utf8_decode("NOMBRE O RAZÓN SOCIAL: "),1,2,'L');
  $pdf->SetXY(26.5,($posy+3)); 
  $pdf->SetFont('Arial','B',7);
  $pdf->Cell(222.8,8,utf8_decode("FUNDACIÓN INFOCENTRO"),0,2,'L'); 
  $pdf->SetFont('Arial','B',5);
  $posy= $pdf->gety();
  $pdf->SetXY(26.5,$posy-3); 
  $pdf->Cell(111.4,8,utf8_decode("REGISTRO DE INFORMACIÓN FISCAL: "),1,2,'L');
  $pdf->SetXY(26.5,($posy)); 
  $pdf->SetFont('Arial','B',7);
  $pdf->Cell(111.4,8,utf8_decode("G-20007728-0"),0,2,'L'); 
  $pdf->SetXY(137.9,$posy-3); 
  $pdf->SetFont('Arial','B',5);
  $pdf->Cell(111.4,8,utf8_decode("PERÍODO FISCAL\n"),1,2,'L');
  $pdf->SetXY(137.9,$posy); 
  $pdf->SetFont('Arial','B',7);
  $pdf->Cell(111.4,8,utf8_decode("AÑO ".$an_o." MES ".$mes),0,2,'L'); //Si no se coloca el getX no funciona
  $pdf->SetFont('Arial','B',5);
  $posy= $pdf->gety();
  $pdf->SetXY(26.5,($posy-3)); 
  $pdf->Cell(222.8,8,utf8_decode("DIRECCIÓN DEL AGENTE DE RETENCIÓN: "),1,2,'L');
  $pdf->SetXY(26.5,($posy)); 
  $pdf->SetFont('Arial','B',7);
  $pdf->Cell(222.8,8,utf8_decode("AV. UNIVERSIDAD CON ESQ. EL CHORRO, LA HOYADA, EDIF. TORRE MCT, PISO 11"),0,2,'L'); 

  $posy= $pdf->gety();
  $pdf->SetFont('Arial','B',7);
  $pdf->SetXY(26.5,$posy+5);
  $pdf->Cell(222.8,4,utf8_decode('DATOS DE SUJETO DE RETENCIÓN'),1,2,'C');
  $pdf->SetFont('Arial','B',5);
  $posy= $pdf->gety();
  $posyy= $pdf->gety();

  
  $pdf->SetXY(26.5,$posyy);
  $pdf->Cell(111.4,8,utf8_decode("NOMBRE O RAZÓN SOCIAL: "),1,2,'L');
  $pdf->SetXY(26.5,($posy+3)); 
  $pdf->SetFont('Arial','B',7);
  if ($rif==""){
  $pdf->Cell(111.4,8,html_entity_decode(htmlentities($nombre_bene)),0,2,'l'); //cuadro
  }else{
  $pdf->Cell(111.4,8,html_entity_decode(htmlentities($proveedor)),0,2,'l'); //cuadro
  }
  $pdf->SetFont('Arial','B',5);
  $pdf->SetXY(137.9,$posy); 
  $pdf->SetFont('Arial','B',5);
  $pdf->Cell(111.4,8,utf8_decode("REGISTRO DE INFORMACIÓN FISCAL\n"),1,2,'L');
  $pdf->SetXY(137.9,$posy+3); 
  $pdf->SetFont('Arial','B',7);
  if ($rif==""){
  $pdf->Cell(111.4,8,$sopg_bene_ci_rif,0,2,'L'); //Si no se coloca el getX no funciona
  }else{
  $pdf->Cell(111.4,8,$rif,0,2,'L'); //Si no se coloca el getX no funciona
}

/*$posy= $pdf->gety();
$pdf->SetXY(26.5,($posy+1));
$pdf->Cell(168,4,utf8_decode('IMPUTACIÓN PRESUPUESTARIA'),1,2,'C');
$pdf->SetFont('Arial','B',5);*/
$pdf->SetFont('Arial','B',5);
$posy= $pdf->gety();
$pdf->SetXY(26.5,$posy);

  $pdf->SetX(26.5); 
  $pdf->MultiCell(8.4,$alto," OPER \n".utf8_decode("N°"),1,'C',0); 
  $pdf->SetXY(34.9,$posy);  
  $pdf->MultiCell(12.4,$alto,"FECHA \n"."FACTURA",1,'C',0); 
  $pdf->SetXY(47.3,$posy);  
  $pdf->MultiCell(17.4,$alto,utf8_decode("N°")."\n"."COMPROBANTE",1,'C',0); 
  $pdf->SetXY(64.7,$posy);  
  $pdf->MultiCell(12.4,$alto,utf8_decode("N°")."\n"."FACTURA",1,'C',0); 
  $pdf->SetXY(77.1,$posy);  
  $pdf->MultiCell(12.4,$alto,utf8_decode("N°")."\n"."CONTROL",1,'C',0); 
  $pdf->SetXY(89.5,$posy);  
  $pdf->MultiCell(10.4,$alto,"NOTA"."\n".utf8_decode("DÉBITO"),1,'C',0);
  $pdf->SetXY(99.9,$posy);  
  $pdf->MultiCell(10.4,$alto,"NOTA"."\n".utf8_decode("CRÉDITO"),1,'C',0);
  $pdf->SetXY(110.3,$posy);  
  $pdf->MultiCell(20.4,$alto,"TIPO"."\n". utf8_decode("TRANSACCIÓN"),1,'C',0); 
  $pdf->SetXY(130.7,$posy);  
  $pdf->MultiCell(12.4,$alto,utf8_decode("N° FACT.")."\n"."AFECTADA",1,'C',0); 
  $pdf->SetXY(143.1,$posy);  
  $pdf->MultiCell(20.4,$alto,"TOTAL COMPRAS"."\n"."INCLUYENDO IVA",1,'C',0); 
  $pdf->SetXY(163.5,$posy);  
  $pdf->MultiCell(25.4,$alto,"COMPRAS SIN DERECHO"."\n".utf8_decode("A CRÉDITO IVA"),1,'C',0); 
  $pdf->SetXY(188.9,$posy);  
  $pdf->MultiCell(18.4,$alto,"BASE"."\n"."IMPONIBLE",1,'C',0); 
  $pdf->SetXY(207.3,$posy);  
  $pdf->MultiCell(11.4,$alto,"%"."\n"."ALICUOTA",1,'C',0); 
  $pdf->SetXY(218.7,$posy);  
  $pdf->MultiCell(15.4,$alto,"IMPUESTO"."\n"."IVA",1,'C',0); 
  $pdf->SetXY(234.1,$posy);  
  $pdf->MultiCell(15.2,$alto,"IVA"."\n"."RETENIDO",1,'C',0);

  $posx= $pdf->getx();
  $posyy= $pdf->gety();
  $posy= $pdf->gety();
  $pdf->Setx( $posx);
  
  
  $pdf->SetXY($posx,$posy); 
  $pdf->Cell(50,6,"",0,2,'R'); //Si no se coloca el getX no funciona
  $posy= $pdf->gety();
  
  $pdf->Sety($posyy); 
  $pdf->SetX(26.5); 
  $pdf->Cell(8.4,$alto,"1",1,0,'R'); 
  $pdf->Cell(12.4,$alto ,$factura_fecha,1,0,'R'); 
  $pdf->Cell(17.4,$alto ,$codigo,1,0,'R'); 
  $pdf->Cell(12.4,$alto ,$factura_num,1,0,'R'); 
  $pdf->Cell(12.4,$alto ,$factura_control,1,0,'R'); 
  $pdf->Cell(10.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(10.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(20.4,$alto ,$nombre_sol,1,0,'R'); 
  $pdf->Cell(12.4,$alto ,$factura_num,1,0,'R'); 
  $pdf->Cell(20.4,$alto ,$sopg_monto,1,0,'R'); 
  $pdf->Cell(25.4,$alto ,$monto_exento,1,0,'R'); 
  $pdf->Cell(18.4,$alto ,$iva_base,1,0,'R'); 
  $pdf->Cell(11.4,$alto ,$iva_porce,1,0,'R'); 
  $pdf->Cell(15.4,$alto ,$iva_monto,1,0,'R'); 
  $pdf->Cell(15.2,$alto ,$rete_monto_doc,1,2,'R'); 

 for ($i=0; $i<4;$i++)
 {
  $posy= $pdf->gety();
  $pdf->Sety($posy); 
  $pdf->SetX(26.5); 
  $pdf->Cell(8.4,$alto,"",1,0,'R'); 
  $pdf->Cell(12.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(17.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(12.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(12.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(10.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(10.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(20.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(12.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(20.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(25.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(18.4,$alto ,"",1,0,'R');   
  $pdf->Cell(11.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(15.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(15.2,$alto ,"",1,2,'R'); 
 }
  $pdf->SetX(26.5);
  $pdf->SetFont('Arial','B',7);
  $pdf->Cell(8.4,$alto,"",1,0,'R'); 
  $pdf->Cell(12.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(17.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(12.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(12.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(10.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(10.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(20.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(12.4,$alto ,"TOTAL",1,0,'R'); 
  $pdf->Cell(20.4,$alto ,$sopg_monto,1,0,'R'); 
  $pdf->Cell(25.4,$alto ,$monto_exento,1,0,'R'); 
  $pdf->Cell(18.4,$alto ,$iva_base,1,0,'R'); 
  $pdf->Cell(11.4,$alto ,"",1,0,'R'); 
  $pdf->Cell(15.4,$alto ,$iva_monto,1,0,'R'); 
  $pdf->Cell(15.2,$alto ,$rete_monto_doc,1,2,'R'); 
  $pdf->SetFont('Arial','B',5);
  $pdf->SetX(6.5); 
  
  $pdf->Ln(); 
  $pdf->Ln(); 
  $posy= $pdf->gety();
  $pdf->SetXY(7.5,$posy+1);
  $pdf->SetFont('Arial','B',7);
  $pdf->SetXY(26.5,$posy);
  $pdf->Cell(222.8,4,'FIRMAS',1,2,'C');
  $pdf->SetFont('Arial','B',5);
  $posy= $pdf->gety();

   $pdf->SetXY(26.5,$posy);
   $pdf->MultiCell(111.4,6,"\n\n___________________________\n".utf8_decode("Firma del agente de Retención")."\n" ,1,'C',0);
   $pdf->SetXY(137.9,$posy);
   $pdf->MultiCell(111.4,6,"\n\n___________________________\n".utf8_decode("Firma del Sujeto Retenido")."\n",1,'C',0);
   $pdf->Ln(); 
   

$tipo_documento=substr($codigo,0,4);
//**** Determinamos un nombre temporal para el pdf
$archivo = basename(tempnam(getcwd(),'tmp'));
rename($archivo,$archivo.'.pdf');
$archivo.='.pdf';
//Guardar el pdf en un fichero
$pdf->Output($archivo);?>
<script>
document.write('Cargando archivo PDF, por favor espere ... ');
</script>
<?php //<Redirecci�n con JavaScript
//echo("<HTML><SCRIPT>window.open('$archivo')</SCRIPT></HTML>");
echo("<HTML><SCRIPT>window.open('".$archivo."','pdf')</SCRIPT></HTML>");
limpiarTemporalesPdf(dirname(__FILE__));


 }
else{?>
	<br>
	<div align="center">La solicitud de pago ingresada no tiene Retenci&oacute;n del Impuesto al Valor Agregado </div>
	<br>
	<div align="center"><a href="index_comprobantes.php">Volver</a></div>
       <?}

?> 

