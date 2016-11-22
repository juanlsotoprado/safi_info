<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush();
	
require("../../lib/fpdf/fpdf.php");

$codigo=trim($_REQUEST['codigo']);

$sql="select s.sopg_monto as sopg_monto, pch.pres_anno_docg as ano, d.depe_nombre as nombre_dependencia, pch.pgch_fecha as fecha_pgch, pch.nro_cuenta as numero_cuenta, pch.docg_id as sopg, pch.pgch_asunto as asunto, pch.pgch_obs as observaciones, b.banc_nombre as nombre_banco,  upper(em.empl_nombres) || upper(em.empl_apellidos) as usuario_solicitante, em.empl_email as email, em.empl_tlf_ofic as telefono, ch.nro_cheque as numero_cheque, ch.monto_cheque as monto_cheque, upper(ch.beneficiario_cheque) as beneficiario_cheque, t.nombre_sol as tipo_solicitud, ch.id_cheque as id_cheque, ctb.cpat_id as cpat_id
from sai_pago_cheque pch, sai_cheque ch, sai_banco b, sai_dependenci d, sai_ctabanco ctb, sai_doc_genera dg, sai_empleado em, sai_tipo_solicitud t, sai_sol_pago s
where ch.estatus_cheque<>15 and s.sopg_id=pch.docg_id and s.sopg_tp_solicitud=t.id_sol and pch.docg_id=ch.docg_id and pch.depe_id=d.depe_id and pch.nro_cuenta=ctb.ctab_numero and ctb.banc_id=b.banc_id and pch.depe_id=d.depe_id and dg.docg_id=pch.pgch_id and dg.usua_login=em.empl_cedula and pch.pgch_id='".$codigo."'";
$resultado=pg_query($conexion,$sql);

if ($row=pg_fetch_array($resultado)) {
	$sopg_monto=number_format(trim($row['sopg_monto']),2,',','.');
	$dependencia_solicitante = trim($row['nombre_dependencia']);
	$fecha_pgch = trim($row['fecha_pgch']);
	$fecha_cheque = trim($row['fechaemision_cheque']);	
	$numero_cuenta = trim($row['numero_cuenta']);
	$id_cheque = trim($row['id_cheque']);
	$cpat_id_banco= trim($row['cpat_id']);
	$numero_cheque = trim($row['numero_cheque']);
	$sopg = trim($row['sopg']);	
	$tipo_solicitud=trim($row['tipo_solicitud']);		
	$asunto = trim($row['asunto']);
	$usuario_solicitante = trim($row['usuario_solicitante']);
    $observaciones=trim($row['observaciones']);
	$email_solicitante=trim($row['email']);
	$telefono_solicitante= trim($row['telefono']);
	$nombre_banco = trim($row['nombre_banco']);
	$monto_cheque_float = $row['monto_cheque'];
	$monto_cheque = number_format(trim($row['monto_cheque']),2,',','.');
	$beneficiario_cheque = trim($row['beneficiario_cheque']);
	$anno_id_doc_imputacion = trim($row['anno']);
	//Buscar tipo de doc principal
	$tipo_doc = substr($pgch_docg_id,0,4);	
}			

include("../../includes/monto_a_letra.php");
    $monto_en_letras = monto_letra($monto_cheque_float, " ");

include("../../includes/fechas.php");

//Fecha del cheque
$dia = date("d");
$mes = date("n");
$anno = date("Y");
$fecha_numero = $dia.'/'.$mes.'/'.$anno;
if ($mes==1) $mes= 'enero';
if ($mes==2) $mes= 'febrero';
if ($mes==3) $mes= 'marzo';
if ($mes==4) $mes= 'abril';
if ($mes==5) $mes= 'mayo';
if ($mes==6) $mes= 'junio';
if ($mes==7) $mes= 'julio';
if ($mes==8) $mes= 'agosto';
if ($mes==9) $mes= 'septiembre';
if ($mes==10) $mes= 'octubre';
if ($mes==11) $mes= 'noviembre';
if ($mes==12) $mes= 'diciembre';

$fecha_en_letras = 'Caracas, '. $dia . ' de ' . $mes . '         '.$anno;

$sql = " SELECT * FROM sai_modificar_estado_doc_genera('$codigo',10) as resultado ";
$resultado = pg_query($conexion,$sql) or die("Error al mostrar");	

//Modificar el estado del cheque a "Emitido"
$sql = " SELECT * FROM sai_cambiar_estado_cheque('$id_cheque',45, '') as resultado ";
$resultado = pg_query($conexion,$sql) or die("Error al mostrar");	

/*Buscar cta pasivo y cta transitoria*/
	$sql="select * from sai_sol_pago_imputa where sopg_id='".$sopg."' and sopg_sub_espe <>'4.03.18.01.00'";
	$resultado_proc = pg_query($conexion,$sql) or die("Error al mostrar partidas");
	if ($row_pc = pg_fetch_array($resultado_proc)) {
		$part_id = $row_pc["sopg_sub_espe"];
	}

	$sql="select cpat_pasivo_id, cpat_transitoria_id from sai_convertidor where part_id='".$part_id."'";
	$resultado_proc = pg_query($conexion,$sql) or die("Error al mostrar partidas");
	if ($row_pc = pg_fetch_array($resultado_proc)) {
		$cpat_id_prov = $row_pc["cpat_pasivo_id"];
		$cpat_id_transitoria = $row_pc["cpat_transitoria_id"];
	}
	$sql="select cpat_nombre from sai_cue_pat where cpat_id = '".$cpat_id_prov."'";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");	
	if ($row = pg_fetch_array($resultado)) {
		$cpat_nombre_prov = $row["cpat_nombre"];			
	}
pg_close($conexion);

class PDF extends FPDF { //Definir el encabezado y pie de pagina del cheque
  
	function Header() { 
		global $codigo; 
		global $sopg_monto;
		global $dependencia_solicitante;
		global $fecha_pgch;
		global $fecha_cheque;
		global $numero_cuenta;
		global $id_cheque;
		global $cpat_id_banco;
		global $numero_cheque;
		global $sopg;
		global $tipo_solicitud;		
		global $asunto;
		global $usuario_solicitante;
	    global $observaciones;
		global $email_solicitante;
		global $telefono_solicitante;
		global $nombre_banco;
		global $monto_cheque_float;
		global $monto_cheque;
		global $beneficiario_cheque;
		global $anno_id_doc_imputacion;
		global $monto_en_letras;
		global $fecha_numero;
		global $fecha_en_letras;	
		global $cpat_nombre_prov;			
	}
	
	function Imprime(){
		global $codigo; 
		global $sopg_monto;
		global $dependencia_solicitante;
		global $fecha_pgch;
		global $fecha_cheque;
		global $numero_cuenta;
		global $id_cheque;
		global $cpat_id_banco;
		global $numero_cheque;
		global $sopg;
		global $tipo_solicitud;		
		global $asunto;
		global $usuario_solicitante;
	    global $observaciones;
		global $email_solicitante;
		global $telefono_solicitante;
		global $nombre_banco;
		global $monto_cheque_float;
		global $monto_cheque;
		global $beneficiario_cheque;
		global $anno_id_doc_imputacion;
		global $monto_en_letras;
		global $fecha_numero;
		global $fecha_en_letras;	
		global $cpat_nombre_prov;	
		
		$tipo_fuente_letras = "Arial";
		$tipo_fuente_nros = "Arial";
		$size_fuente_letras = 10;
		$size_fuente_nros = 11;
		$negrita_fuente_letras = '';
		$negrita_fuente_nros = '';
	
		$Ye = 10; 
		$Xe = 0;
		$this->SetXY(120,$Ye+2);
		$this->SetFont($tipo_fuente_nros,$negrita_fuente_nros,$size_fuente_nros);
		$this->Cell($Xe+120,$Ye+2,$monto_cheque);

		$this->SetXY(120,$Ye+7);
		$this->SetFont($tipo_fuente_nros,$negrita_fuente_nros,$size_fuente_nros-1);
		//$this->Cell($Xe+120,$Ye+7,utf8_encode('Caduca a los 90 dias'));

		$this->SetXY(17,$Ye+13);
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,$size_fuente_letras);
		$this->Cell($Xe+17,$Ye+13,$beneficiario_cheque);
		
		$this->SetXY(18,$Ye+18);
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,$size_fuente_letras);
		$this->Cell($Xe+18,$Ye+18,$monto_en_letras); 
		
		$this->SetXY(4,$Ye+26);
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,12);
		$this->Cell($Xe+4,$Ye+27,$fecha_en_letras);

		$this->SetXY(60,$Ye+38);
		$this->SetFont($tipo_fuente_nros,$negrita_fuente_nros,$size_fuente_nros-1);
		$this->Cell($Xe+60,$Ye+38,utf8_encode('NO ENDOSABLE'));

		$Ye=$Ye+3;
		//Codigo Solicitud de pago
		$this->SetXY(5,$Ye+54);
		$this->SetFont($tipo_fuente_nros,$negrita_fuente_nros,$size_fuente_nros-1);
		$this->Cell($Xe+5,$Ye+54,$sopg);

		//Codigo Pago con Cheque
		$this->SetXY(126,$Ye+54);
		$this->SetFont($tipo_fuente_nros,$negrita_fuente_nros,$size_fuente_nros-1);
		$this->Cell($Xe+126,$Ye+54,$codigo);

		$Ye=$Ye+4;
		//Nro. Cheque
		$this->SetXY(8,$Ye+60);
		$this->SetFont($tipo_fuente_nros,$negrita_fuente_nros,$size_fuente_nros-1);
		$this->Cell($Xe+8,$Ye+60,$numero_cheque);

		//Banco
		$numero_cuenta_ultimos_4 = substr($numero_cuenta, -4);
		$numero_cuenta_ultimos_4 = $numero_cuenta_ultimos_4 ? $numero_cuenta_ultimos_4 : "";
		$this->SetXY(56,$Ye+60);
		$this->SetFont($tipo_fuente_nros,$negrita_fuente_nros,$size_fuente_nros-1);
		$this->Cell($Xe+56,$Ye+60,$nombre_banco . " " . $numero_cuenta_ultimos_4);

		//Fecha
		$this->SetXY(126,$Ye+60);
		$this->SetFont($tipo_fuente_nros,$negrita_fuente_nros,$size_fuente_nros-1);
		$this->Cell($Xe+126,$Ye+60,$fecha_numero);

		//Beneficiario
		$this->SetXY(8,$Ye+64);
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,$size_fuente_letras);
		$this->Cell($Xe+8,$Ye+64,$beneficiario_cheque);

		//Concepto
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,$size_fuente_letras);
		$this->SetXY(16,$Ye+108);		
		$this->MultiCell(130,5,$observaciones);

		$this->SetXY(22,$Ye+100);
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,$size_fuente_letras);
		$this->Cell($Xe+22,$Ye+100,$cpat_nombre_prov);

		$this->SetXY(125,$Ye+100);
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,$size_fuente_letras);
		$this->Cell($Xe+125,$Ye+100,$monto_cheque);

		$this->SetXY(22,$Ye+108);
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,$size_fuente_letras);
		$this->Cell($Xe+22,$Ye+108,$nombre_banco);

		$this->SetXY(141,$Ye+108);
		$this->SetFont($tipo_fuente_letras,$negrita_fuente_letras,$size_fuente_letras);
		$this->Cell($Xe+141,$Ye+108,$monto_cheque);
	}

/*********Pie de pagina***************************/

function Footer() {
		$tipo_fuente_letras = "Arial";
		$tipo_fuente_nros = "Arial";
		$size_fuente_letras = 9;
		$size_fuente_nros = 10;
		$negrita_fuente_letras = '';
		$negrita_fuente_nros = '';	
		$this->SetY(-100);
}
	
function SetCol($col) {
    //Establecer la posición de una columna dada
    $this->col=$col;
    $x=10+$col*65;
    $this->SetLeftMargin($x);
    $this->SetX($x);
}
function AcceptPageBreak() {
    //Método que acepta o no el salto automático de página
    if($this->col<2)  {
        //Ir a la siguiente columna
        $this->SetCol($this->col+1);
        //Establecer la ordenada al principio
        $this->SetY($this->y0);
        //Seguir en esta página
        return false;
    }
    else  {
        //Volver a la primera columna
        $this->SetCol(0);
        //Salto de página
        return true;
    }
}
}		 

$pdf=new PDF();
$pdf->AddPage();

$pdf->Imprime();
$pdf->Ln(3);
$pdf->SetX(140);

$archivo = basename(tempnam(getcwd(),'tmp'));
rename($archivo, $archivo.'.pdf');
$archivo.='.pdf';
//Guardar el pdf en un fichero
$pdf->Output($archivo);

//Redireccion con JavaScript a una ventana nueva por la session
echo("<HTML><SCRIPT>window.open('".$archivo."','pdf')</SCRIPT></HTML>");

include("../../includes/funciones.php");
limpiarTemporalesPdf(dirname(__FILE__));
?>