<?php
ob_start();
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
require("../../includes/monto_a_letra.php");

@set_time_limit(10000);
parse_config_file(HTML2PS_DIR.'html2ps.config');
$codigo=$_REQUEST['codigo'];

$sql_salida="SELECT observaciones, to_char(fecha_acta,'DD/MM/YYYY') as fecha,empl_nombres,empl_apellidos,t1.esta_id,ubicacion
FROM sai_arti_custodia t1,sai_empleado t2
 WHERE acta_id='".$codigo."' and empl_cedula=t1.usua_login";

 $resultado_salida=pg_query($conexion,$sql_salida) or die("Error al consultar información de la custodia");
 while($rowd=pg_fetch_array($resultado_salida)) 
 { 
	$fecha=$rowd['fecha'];
	$observaciones=$rowd['observaciones'];
	$elaborado=$rowd['empl_nombres']." ".$rowd['empl_apellidos'];
	$edo=$rowd['esta_id'];
	$ubicacion=$rowd['ubicacion'];
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
	 <br><div align='left' class='nombreCampo'>&nbsp;&nbsp;
	 OFICINA DE GESTI&Oacute;N ADMINISTRATIVA Y FINANCIERA<br>&nbsp;&nbsp;
	 JEFATURA DE BIENES Y SEGURIDAD<br>&nbsp;&nbsp;
	 COORDINACI&Oacute;N DE BIENES NACIONALES</div>
	</td></tr>
  <tr><td ><td align='right' class='textoTabla'><b>Fecha: </b>".$fecha."</b></td></tr>
  <tr><td ><td align='right' class='textoTabla'><b>N&deg; Acta: </b>".$codigo."<br></td></tr>
</table>
				
<br><div align='center' class='textoTabla'><b>CUSTODIA DE ACTIVOS Y/O MATERIALES</b></div><br><br>";
				if ($edo==15){
$contenido .=	" <div align='center'><font color='Red'><STRONG>ANULADO</STRONG></font></div><br>";
  				}
  				if ($ubicacion==2)
$contenido .="<div align='justify' class='nombreCampo'>&nbsp;&nbsp;Los materiales y/o activos detallados a continuaci&oacute;n est&aacute;n siendo enviados a la Torre para su custodia:</div><br>";
			    else
$contenido .="<div align='justify' class='nombreCampo'>&nbsp;&nbsp;Los materiales y/o activos detallados a continuaci&oacute;n est&aacute;n siendo enviados al Galp&oacute;n para su custodia:</div><br>";			    

$sql_custodia="SELECT cantidad,medida,nombre FROM sai_arti_item_custodia t1,sai_item t2 WHERE acta_id='".$codigo."' and t1.id=t2.id  ORDER BY t2.nombre";
$resultado_custodia=pg_query($conexion,$sql_custodia) or die("Error al consultar el detalle de la custodia");
if ($resultado_custodia){
  $num_articulos=pg_num_rows($resultado_custodia);	
}
/*MUESTRA LOS ARTICULOS*/
if ($num_articulos>0){
$contenido .="<table border=1 width='90%' align='center'>".
  "<tr class='textoTabla' align='center'>
	<td><b>Material</b></td>
	<td><b>Unidad de medida</b></td>
	<td><b>Cantidad</b></td></tr>";

while($rowcustodia=pg_fetch_array($resultado_custodia)) 
{ 
$contenido .="<tr class='textoTabla'>
				<td width='50%' align='left'>".$rowcustodia['nombre']."</td>
			    <td width='25%' align='center'>".$rowcustodia['medida']."</td>
				<td width='20%' align='center'>".$rowcustodia['cantidad']."</td></tr>";
 }

$contenido .=" </table><br/>";
}
/**/

$sql_custodia="SELECT * 
FROM sai_bien_item_custodia t1,sai_item t2, sai_biin_items t3
WHERE 
t1.acta_id='".$codigo."' and t3.bien_id=t2.id and t1.clave_bien=t3.clave_bien ORDER BY t2.nombre";
$resultado_custodia=pg_query($conexion,$sql_custodia) or die("Error al consultar el detalle de la custodia");
if ($resultado_custodia){
  $num_activos=pg_num_rows($resultado_custodia);	
}
/*MUESTRA LOS ACTIVOS*/
if ($num_activos>0){
$contenido .="<table border=1 width='90%' align='center'>".
  "<tr class='textoTabla' align='center'>
	<td><b>Activo</b></td>
	<td><b>Serial Bien Nacional</b></td>
	<td><b>Serial activo</b></td></tr>";

while($rowcustodia=pg_fetch_array($resultado_custodia)) 
{ 
$contenido .="<tr class='textoTabla' align='center'>
				<td width='50%'>".$rowcustodia['nombre']."</td>
			    <td width='25%'>".$rowcustodia['etiqueta']."</td>
				<td width='20%'>".$rowcustodia['serial']."</td></tr>";
 }

$contenido .=" </table><br/>";
}
/**/
$contenido .= "<div align='justify' class='nombreCampo'>&nbsp;&nbsp;".$observaciones."</div><br>";
$firma = "<table border='1' width='90%' align='center'>";
$firma .= "<tr align='center' class='textoTabla'><td colspan='2'>Firmas</tr>
		   <tr align='center' class='textoTabla'>".
						"<td width='33%'>".
							"Enviado por: <br/><br/><br><br><br/>".
							"___________________<br/>".$elaborado."
						</td>".
						"<td width='34%'>".
							"Recibido por:<br/><br/><br/>".
							"___________________<br/><br/>".
						"</td>".
					"</tr>".
				"<br/>".

				"</table>".
				"</table><br/>";

	$footer = "<br/>".
				"<style type='text/css'>
						@page {
					 		@bottom-right {
					 		    font-family: arial;
					 		    font-size: 10pt;
					    		content: 'Página ' counter(page) ' de ' counter(pages);
					  		}
					  		@bottom-center {
					  		    font-family: arial;
					  		    font-style:italic;
					  		    font-weight:bold;
					  		    font-size: 10pt;
					    		content: 'SAFI - Fundación Infocentro';
					  		}
						}
					</style>";
	
	$contenido .= $firma;
	$properties = array("marginBottom" => 15, "footerHtml" => $footer);
//}
pg_close($conexion);
ob_clean();
convert_to_pdf($contenido, $properties);?>