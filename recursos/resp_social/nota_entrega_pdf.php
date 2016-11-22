<?php
ob_start();

require_once(dirname(__FILE__) . '/../../init.php');
require_once (SAFI_LIB_PATH . '/general.php');
// Modelo
require_once(SAFI_MODELO_PATH. '/firma.php');

require("../../includes/conexion.php");
if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}

require("../../includes/reporteBasePdf.php"); 
require("../../includes/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require("../../includes/html2ps/funciones.php");

@set_time_limit(10000);
parse_config_file(HTML2PS_DIR.'html2ps.config');

// Obtener los datos de las firmas del documento
$arrFirmas = SafiModeloFirma::GetFirmaByPerfiles(array(PERFIL_JEFE_BIENES, PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS, PERFIL_PRESIDENTE));

$codigo=$_REQUEST['id'];

$revisadoPorNombre = "";

$revision="SELECT * FROM sai_revisiones_doc t1,sai_empleado t2 WHERE revi_doc='".$codigo."' /*and wfop_id=6*/ and t1.usua_login=empl_cedula";
$result_rev=pg_query($conexion,$revision);
if ($rev=pg_fetch_array($result_rev)){
	$por="Revisado por ".$rev['empl_nombres']." ".$rev['empl_apellidos'];
	$revisadoPorNombre = $rev['empl_nombres']." ".$rev['empl_apellidos'];
}

 $sql_salida="SELECT destino, to_char(fecha_acta,'DD/MM/YYYY') as fecha,empl_nombres,empl_apellidos,t1.esta_id
 FROM sai_arti_salida_rs t1,sai_empleado t3 
 WHERE t1.acta_id='".$codigo."' and empl_cedula=t1.usua_login";

 $resultado_salida=pg_query($conexion,$sql_salida) or die("Error al consultar información general del acta");
 while($rowd=pg_fetch_array($resultado_salida)) 
 { 
	$elaborado= $rowd['empl_nombres']." ".$rowd['empl_apellidos'];
	$fecha=$rowd['fecha'];
	$destino=$rowd['destino'];
	$edo=$rowd['esta_id'];
 }

$contenido = "<style type='text/css'>
				.nombreCampo{
							 FONT-SIZE: 16px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none
						}
				.bordeTabla{
							border: solid 1px #000000;
						}
				.textoTabla{
							FONT-WEIGHT: normal; FONT-SIZE: 16px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none
						}
					</style>

				<table border=0 width='100%'>
				<tr>
				<td>
				<div align='left' class='nombreCampo'>&nbsp;&nbsp;
				OFICINA DE GESTI&Oacute;N ADMINISTRATIVA Y FINANCIERA<br>&nbsp;&nbsp;
				JEFATURA DE BIENES Y SEGURIDAD<br>&nbsp;&nbsp;
				COORDINACI&Oacute;N DE BIENES NACIONALES</div>
				</td></tr>
				<tr><td ><td align='right' class='textoTabla'><b>Fecha: </b>".$fecha."</b></td></tr>
				<tr><td ><td align='right' class='textoTabla'><b>N&deg; Nota de entrega: </b>".$codigo."<br></td></tr>
				<tr><td ><td align='right' class='textoTabla'><b>Elaborado por: </b>".$elaborado."</b><br></td></tr>
				</table>
				<br><br>";										  

$contenido .=	"<div align='center' class='textoTabla'><b>RESPONSABILIDAD SOCIAL<br/> AUTORIZACI&Oacute;N DE SALIDA</b></div><br>";
	
				if ($edo==15){
$contenido .=	" <div align='center'><font color='Red'><STRONG>ANULADO</STRONG></font></div><br>";
				  $motivo_anulacion="";
				  $motivo="SELECT memo_contenido FROM sai_docu_sopor,sai_memo WHERE doso_doc_soport=memo_id and doso_doc_fuente='".$codigo."'";
				  $result_motivo=pg_query($conexion,$motivo);
				  if ($row_motivo=pg_fetch_array($result_motivo)){
				    $motivo_anulacion=strtoupper($row_motivo['memo_contenido']);	
				  }
				if ($motivo_anulacion<>""){
$contenido .=	" <div align='center'><STRONG>".$motivo_anulacion."</STRONG></div><br>";				  	
				  }
  				}
$contenido .="<table border=1 width='100%'>".
				" <tr class='textoTabla' align='center'>".
				"<td><b>Item</b></td>".
				"<td><b>Art&iacute;culo</b></td>".
				"<td><b>Medida</b></td>".
				"<td><b>Marca</b></td>".
				"<td width='230'><b>Modelo</b></td>".
				"<td><b>Serial</b></td>".
				"<td><b>Cantidad</b></td></tr>";
 $indice=1;
 
  
//Artículos
$sql_arti="SELECT t3.nombre,t2.cantidad,t2.medida,t4.marca_id,t4.modelo,t4.serial,t4.sec_id
FROM sai_arti_salida_rs t1,sai_arti_salida_rs_item t2,sai_item t3,sai_arti_inco_rs_item t4
WHERE t1.acta_id='".$codigo."' and t1.acta_id=t2.acta_id and id=t2.arti_id and t2.sec_id=t4.sec_id ORDER BY t3.nombre";
//echo $sql_arti;
$resultado_arti=pg_query($conexion,$sql_arti) or die("Error al consultar el detalle del acta");
 
  while($rowarti=pg_fetch_array($resultado_arti)) 
 {  $marca="";
 	$sql="SELECT bmarc_nombre FROM sai_bien_marca WHERE bmarc_id= '".$rowarti['marca_id']."'";
    $resultado=pg_exec($conexion,$sql) or die("Error al consultar la marca"); 
	if($row=pg_fetch_array($resultado))
	{
	 $marca=$row['bmarc_nombre'];
	} 
  $contenido .=" <tr  class='textoTabla' align='center'>".
			    "<td>".$indice."</td>".
			    "<td>".$rowarti['nombre']."</td>".
  				"<td>".$rowarti['medida']."</td>".
  				"<td>".$marca."</td>".
  				"<td>".$rowarti['modelo']."</td>".
  				"<td>".$rowarti['serial']."</td>".
				"<td>".$rowarti['cantidad']."</td></tr>";
                $indice++;
 }



$contenido .=	" </table><br/>";
$contenido .= "<div align='center'>".$destino."</div><br/>";
$firma = "
	
	<table border=1 width='100%'>
		<tr>
			<td style='width: 25%;'>Revisado por: Analista de Jefatura de Bienes y Seguridad</td>
			<td style='width: 25%;'>Verificado por: Coordinador de Jefatura de Bienes y Seguridad</td>
			<td style='width: 25%;'>
				Aprobado por:
				".$arrFirmas[PERFIL_JEFE_BIENES]['nombre_cargo']." de "
				. $arrFirmas[PERFIL_JEFE_BIENES]['nombre_dependencia']."
			</td>
			<td>
				Autorizado por:
				".$arrFirmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_cargo']." de " 
				. $arrFirmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_dependencia']."
			</td>
		</tr>
		<tr>
			<td valign='bottom' style='height: 110px;' align='center'>".$revisadoPorNombre."</td>
			<td valign='bottom' style='height: 110px;'></td>
			<td valign='bottom' align='center'>".$arrFirmas[PERFIL_JEFE_BIENES]['nombre_empleado']."</td>
			<td valign='bottom' align='center'>".$arrFirmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_empleado']."</td>
		</tr>
	</table>

	<div style='width: 100%; height: 5px;'></div>
	
	<table border=1 style='width: 100%;'>
		<tr>
			<td style='width: 33%;'>Almac&eacute;n</td>
			<td style='width: 33%;'>Transporte</td>
			<td>Seguridad</td>
		</tr>
		<tr>
			<td style='height: 60px;'></td>
			<td></td>
			<td></td>
		</tr>
	</table>
	
	<div style='width: 100%; height: 5px;'></div>

	<table border=1 width='100%'>
		<tr>
			<td style='width: 50%;'>Recibido por</td>
			<td>Observaciones</td>
		</tr>
			<td align='center' style='height: 140px;'>
				<div style='width: 100%; height: 20px;'></div>
				___________________
				<div style='width: 100%; height: 25px;'></div>
				<div align='left'>
					Nombre legible:<br/>
					<div style='width: 100%; height: 10px;'></div>
					C&eacute;dula:<span style='padding-left: 200px;'>&nbsp;</span>Fecha:
					<div style='width: 100%; height: 10px;'></div>
					Tel&eacute;fono:
				</div>
			</td>
			<td></td>
		</tr>
	</table>
	
	<br/>
";


	$footer = "<br/>".
				"<style type='text/css'>
						@page {
					 		@bottom-right {
					 		    font-family: arial;
					 		    font-size: 10pt;
					 		    content: 'Página ' counter(page) ' de ' counter(pages);
					 		    vertical-align: bottom;
					 		    padding-bottom: 20px;
					  		}
						}
					</style>
	<span>".GetNotaBienesSalidasReasignaciones()."</span>
	<div style='width: 100%; height: 15px;'></div>
	<span style='align=center;font-family: arial;font-style:italic;font-weight:bold;font-size: 10pt;'>SAFI - Fundación Infocentro</span><br/>
	<span style='align=center;font-family: arial;font-size: 10pt;'>
		Fecha impresión: ".fecha()."
	</span>
	";
	$contenido .= $firma;
	$properties = array("marginBottom" => 27, "footerHtml" => $footer);

pg_close($conexion);
ob_clean();
convert_to_pdf($contenido, $properties);
?>