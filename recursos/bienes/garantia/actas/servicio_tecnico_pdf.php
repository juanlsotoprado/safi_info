<?php
ob_start();
require_once(dirname(__FILE__) . '/../../../../init.php');
require_once (SAFI_LIB_PATH . '/general.php');
// Modelo
require_once(SAFI_MODELO_PATH. '/firma.php');
require("../../../../includes/conexion.php");

if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../../../index.php',false);
	ob_end_flush(); 
	exit;
}

require("../../../../includes/reporteBasePdf.php"); 
require("../../../../includes/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require("../../../../includes/html2ps/funciones.php");
require("../../../../includes/monto_a_letra.php");

@set_time_limit(10000);
parse_config_file(HTML2PS_DIR.'html2ps.config');

// Obtener los datos de las firmas del documento
$arrFirmas = SafiModeloFirma::GetFirmaByPerfiles(array('46450', '65150'));

$codigo=$_REQUEST['codigo'];
$tipo=$_REQUEST['tipo'];


 $sql_salida="SELECT falla,nro_caso,orden,direccion,t2.observaciones
 FROM sai_bien_garantia t1, sai_nota_salida t2  
 WHERE t2.acta_id='".$codigo."' and acta_garantia=t1.acta_id";

 $resultado_salida=pg_query($conexion,$sql_salida) or die("Error al consultar la nota de salida");
 while($rowd=pg_fetch_array($resultado_salida)) 
 { 
	$falla=$rowd['falla'];
    $caso=$rowd['nro_caso'];
    $orden=$rowd['orden'];
    $direccion=$rowd['direccion'];
    $observacion=$rowd['observaciones'];
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
				<tr><td ><td align='right' class='textoTabla'><b>Fecha: </b>".date('d/m/Y')."</b></td></tr>
				<tr><td ><td align='right' class='textoTabla'><b>".$codigo."</b></b></td></tr>
				</table>
				<br><br>";										  
$contenido .=	"<div align='center' class='textoTabla'><b>NOTA DE SALIDA AL SERVICIO T&Eacute;CNICO</b></div><br>";
$contenido .="<table border=1 width='100%'>".
				" <tr class='textoTabla' align='center'>".
				"<td><b>Item</b></td>".
				"<td><b>Activo</b></td>".
				"<td><b>Marca</b></td>".
				"<td width='230'><b>Modelo</b></td>".
				"<td><b>Serial del Activo</b></td>".
				"<td><b>Serial Bien Nacional</b></td>".
				"<td><b>Cantidad</b></td></tr>";
 $indice=1;
 
 //Activos
 $total_activos=0;
 
 $sql_detalle="SELECT count(t1.clave_bien) as cantidad,bien_id,nombre 
 FROM sai_bien_garantia t1,sai_biin_items t2,sai_item t3,sai_nota_salida t4
 WHERE t4.acta_id='".$codigo."' and t4.acta_garantia=t1.acta_id and t1.clave_bien=t2.clave_bien and t2.bien_id=t3.id
 group by nombre,bien_id";
 //echo $sql_detalle;
 $resultado_detalle=pg_query($conexion,$sql_detalle) or die("Error al consultar el detalle del acta");
 $j=0;
 $num_activos=0;
 while($rowd=pg_fetch_array($resultado_detalle)) 
 { 
 	$cantidad_bien[$num_activos]=$rowd['cantidad'];
	$bien_grupo[$num_activos]=$rowd['bien_id'];
	$bien_nombre[$num_activos]=$rowd['nombre'];
	      
	$sql_bien="SELECT * 
			   FROM sai_biin_items t1,sai_bien_marca,sai_bien_garantia t3,sai_nota_salida t4 
			   WHERE t4.acta_garantia=t3.acta_id and t1.clave_bien=t3.clave_bien and bmarc_id=marca_id and 
			   t4.acta_id='".$codigo."' and bien_id='".$rowd['bien_id']."'";
	//echo $sql_bien;
	$resultado_bien=pg_query($conexion,$sql_bien) or die("Error al mostrar");
		 
		   while($rowdetalle=pg_fetch_array($resultado_bien)) 
	       { 
	       $listado_bienes[$j]=$rowdetalle['bien_id'];
		   $marca_bienes[$j]=$rowdetalle['bmarc_nombre'];
		   $modelo_bienes[$j]=$rowdetalle['modelo'];
		   $serial_bienes[$j]=$rowdetalle['serial'];
		   $etiqueta_bienes[$j]=$rowdetalle['etiqueta'];
		   $j++;
	      }
	      	 $num_activos++;
 }
 
  for ($i=0; $i<$num_activos; $i++)
 {
 $filas=$cantidad_bien[$i];

 $contenido .=" <tr  class='textoTabla' align='center'>".
			  "<td  rowspan='$filas'>".$indice."</td>".
			  "<td  rowspan='$filas'>".$bien_nombre[$i]."</td>";
			  $veces=0;
			  for ($j=0; $j<$filas; $j++)
 			  {
$contenido .=	"<td align='center'>".$marca_bienes[$total_activos]."</td>".
				"<td align='center'>".$modelo_bienes[$total_activos]."</td>".
				"<td align='center'>".$serial_bienes[$total_activos]."</td>".
				"<td align='center'>".$etiqueta_bienes[$total_activos]."</td>";

				if (($j<>$filas-1) && ($veces==0)) {
$contenido .=	"<td rowspan='$filas'>".$cantidad_bien[$i]."</td></tr>";
				 $veces=1;
				}else{
					 if ($filas==1)
					   $contenido .=	"<td rowspan='$filas'>".$cantidad_bien[$i]."</td></tr>";
					 else
					  $contenido .=	"</tr>";
 			}
				$total_activos++;
 			}
                $indice++;
              
 }
 
$contenido .=	" </table><br/>";
$contenido .= "<div align='center'>".$observacion."</div><br/>";
$contenido .= "<div align='center'><b>Direcci&oacute;n: ".$direccion."</b></div><br/>";
$contenido .= "<div align='center'>Falla: ".$falla."</div><br/>";
$contenido .= "<div align='center'><b>Nro de Orden: ".$orden."</b></div><br/>";


$firma = "<table border='1' width='100%'>".
		  "<tr align='center'>";
$firma .=  	"<td>".
			"Solicitado por: <br><br><br>".
			"___________________<br/><br/><br><br/><br>
			</td>
			<td align='center'>
			Autorizado por: <br/><br><br>".
			"___________________<br/>Coordinaci&oacute;n de Bienes Nacionales<br/><br/>
			</td>".
			"<td width='50%'  align='center'>".
			"Recibe conforme:<br/><br/><br>".
			"___________________<br/><br/><br/><div align='left'>Nombre Legible:<br/></div>
			</td></tr>
				
<tr align='center' class='textoTabla'>
<td  colspan='3'><b>".$arrFirmas['46450']['nombre_dependencia']."</b><br>
			<br/><br>

			___________________<br>".
							$arrFirmas['46450']['nombre_empleado']." <br/> ".$arrFirmas['46450']['nombre_cargo']."
</td></tr>";
$firma .="</table><br/>";

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
					</style>".
	
	"<span style='align=center;font-family: arial;font-size: 10pt;'>$por</span><br/>";
	$contenido .= $firma;
	$properties = array("marginBottom" => 15, "footerHtml" => $footer);

pg_close($conexion);
ob_clean();
convert_to_pdf($contenido, $properties);
?>