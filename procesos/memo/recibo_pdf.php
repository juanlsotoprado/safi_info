<?php
require("../../lib/fpdf/fpdf.php");
require("../../includes/funciones.php");
  /*
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	     {
		   header('Location:../../../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	     }
	ob_end_flush(); 
                
    $perfil = $_SESSION['user_perfil_id'];
    //Verificar si el usuario tiene permiso para el objeto (accion) actual
	$sql = " SELECT * FROM sai_permiso_reporte('liber_pcta','".$perfil."') as resultado ";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	if ($row = pg_fetch_array($resultado)) {
		$tiene_permiso = $row["resultado"];
	}

	if ($tiene_permiso == $_SESSION['cero']) {
		//Enviar mensaje de error
		?>
		<script>
		document.location.href = "../../mensaje.php?pag=principal.php";
		</script>
		<?
		header('Location:index.php',false);	
	}*/
 
$cod_expedidor=$_POST['idcodigo'];
$tlf_info=$_POST['telefonoInfo'];
$nombre_contacto=$_POST['contacto'];
$tlf_contacto=$_POST['telefonoContacto'];
$direccion=$_POST['Direccion'];
$edo=$_POST['edoContacto'];        
$cod_postal=$_POST['codpostalEdo'];
$tlf_contacto2=$_POST['telefonoContacto2'];

/**************************************************************************/

//Colocamos la imagen del ministerio del lado derecho
class PDF extends FPDF
{
    //Cabecera de p�gina
	function Header()
	{  $alto=4;

	}
	
 function Footer()
 {

 }
     
}		 


//**********************************************************************
  $pdf=new PDF('P','mm','A4');
  $pdf->AddPage();
  $pdf->AliasNbPages();  //Alias para el numero total de pagina
  $alto=10;
  $pdf->Cell(205,3,'',0,1,'C');
  $posy=28;
  $pdf->SetFont('Arial','B',8);
  $pdf->SetXY(12,$posy);
 // $pdf->Cell(200,4,utf8_decode('DATOS DE LA ORDEN DE PAGO'),1,1,'C');
  
  $posy= $pdf->gety();
  $posy=20;
  $pdf->SetXY(12,$posy); 
	
  $pdf->Cell(50,8," ".$cod_expedidor,0,2,'L');
  $pdf->SetXY($posx,$posy); 
  $pdf->Cell(50,8,"",0,0,'R'); //Si no se coloca el getX no funciona
  $posx= $pdf->getx()+8;
  $pdf->SetXY($posx,$posy); 

  $pdf->Cell(50,8,$tlf_info,0,2,'L');
  $pdf->SetXY($posx,$posy); 
  $pdf->Cell(50,8,"",0,0,'R'); //Si no se coloca el getX no funciona

  $posx= $pdf->getx()+18;
  $pdf->SetXY($posx,$posy); 
	
  $pdf->Cell(50,8,$tlf_contacto,0,2,'L');
  $pdf->SetXY($posx,$posy); 
  $pdf->Cell(50,8,"",0,0,'R'); //Si no se coloca el getX no funciona
  $posx= $pdf->getx();
  $pdf->SetXY($posx,$posy); 

  $posy= $pdf->gety()+$alto;
  $pdf->SetXY(12,$posy-1); 
  $pdf->MultiCell(80,4,utf8_decode("FUNDACIÓN INFOCENTRO"."\n"."Coordinación General de la Red de Formación"."\n"."Sector La Hoyada, Av. Universidad, Esq. El Chorro Edificio J. Castillo, P5"."\n"."Parroquia Catedral"."\n"."Municipio Libertador"."\n"."Caracas, Distrito Capital"."\n"."ZP 1012"),0);
  $posx= $pdf->getx()+40; 
  $pdf->SetXY(125,$posy);
    
  $pdf->MultiCell(80,4,$nombre_contacto."  Tlf: ".$tlf_contacto2."\n".$direccion,0);
  //$pdf->SetFont('Arial','B',5);

  $posy= $pdf->gety();
  $posx=12;
  $pdf->SetXY($posx,$posy+10); //6
	
  $pdf->Cell(50,4,"Distrito Capital",0,2,'L');
  $pdf->SetXY($posx,$posy+6); 
  $pdf->Cell(50,4,"",0,0,'R'); //Si no se coloca el getX no funciona
  $posx= $pdf->getx();
  $pdf->SetXY($posx+15,$posy+10); //6

  $pdf->Cell(50,4,"ZP 1020",0,2,'L');
  $pdf->SetXY($posx,$posy); 
  $pdf->Cell(50,4,"",0,0,'R'); //Si no se coloca el getX no funciona

  $posx= $pdf->getx()+15;
  $pdf->SetXY($posx,$posy+8); //8
	
  $pdf->Cell(50,8,$edo,0,2,'L');
  $pdf->SetXY( $posx,$posy+8); 
  $pdf->Cell(50,4,"",0,0,'R'); //Si no se coloca el getX no funciona
  $posx= $pdf->getx();
  $pdf->SetXY($posx,$posy+8); //8

  $pdf->Cell(50,8,$cod_postal,0,2,'L');
  $pdf->SetXY($posx,$posy+8); 
  $pdf->Cell(50,4,"",0,0,'R'); //Si no se coloca el getX no funciona
  

 
 //////////////////////////////////////////////////////////////   

$pdf->Ln(); 

$tipo_documento=substr($codigo,0,4);
//include("../../documentos/pdf_con_firma.php");
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
?> 