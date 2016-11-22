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
require("../../includes/monto_a_letra.php");

@set_time_limit(10000);
parse_config_file(HTML2PS_DIR.'html2ps.config');

// Obtener los datos de las firmas del documento
$arrFirmas = SafiModeloFirma::GetFirmaByPerfiles(array(PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS, PERFIL_JEFE_BIENES));

$codigo=$_REQUEST['codigo'];
$tipo=$_REQUEST['tipo'];

$revision="SELECT * FROM sai_revisiones_doc t1,sai_empleado t2 WHERE revi_doc='".$codigo."' and wfop_id=6 and t1.usua_login=empl_cedula";
$result_rev=pg_query($conexion,$revision);
if ($rev=pg_fetch_array($result_rev)){
	$revisadoPorNombre = $rev['empl_nombres']." ".$rev['empl_apellidos'];

}

 $sql_salida="SELECT destino, to_char(fecha_acta,'DD/MM/YYYY') as fecha,infocentro,solicitante,empl_nombres,empl_apellidos,t1.esta_id,tipo
 FROM sai_bien_reasignar t1,sai_empleado t3 
 WHERE t1.acta_id='".$codigo."' and empl_cedula=t1.usua_login";

 $resultado_salida=pg_query($conexion,$sql_salida) or die("Error al consultar información general del acta");
 while($rowd=pg_fetch_array($resultado_salida)) 
 { 
	$elaborado= $rowd['empl_nombres']." ".$rowd['empl_apellidos'];
	$fecha=$rowd['fecha'];
	$infocentro=$rowd['infocentro'];
	$destino=$rowd['destino'];
	$solicitante=$rowd['solicitante'];
	$edo=$rowd['esta_id'];
	$tipo_reasignacion=$rowd['tipo'];
 }
$sql_infocentro="SELECT * FROM safi_infocentro WHERE nemotecnico='".$infocentro."'";
$result_infocentro=pg_query($conexion,$sql_infocentro);
if ($row_info=pg_fetch_array($result_infocentro)){
	$nombre_info=$row_info['nombre'];
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
				<tr><td ><td align='right' class='textoTabla'><b>N&deg; Acta: </b>".$codigo."<br></td></tr>
				<tr><td ><td align='right' class='textoTabla'><b>Elaborado por: </b>".$elaborado."</b><br></td></tr>
				</table>
				<br><br>";
	if ($tipo_reasignacion == 3){ // Retornar al inventario
		$contenido .= "<div align='center' class='textoTabla'><b>RETORNO DE ACTIVOS AL INVENTARIO</b></div><br />";
	} else if ($tipo_reasignacion<>1){//COMODATO								  
		if($tipo=='s')
			$contenido .= "
				<div align='center' class='textoTabla'>
					<b>AUTORIZACI&Oacute;N DE SALIDA DE ACTIVOS Y/O MATERIALES</b>
				</div>
				<br />
			";
		else 
			$contenido .= "<div align='center' class='textoTabla'><b>ASIGNACI&Oacute;N DE ACTIVOS Y/O MATERIALES </b></div><br />";
	} else {
		$contenido .= "<div align='center' class='textoTabla'><b>COMODATO NOTA DE ENTREGA </b></div><br>";		
	}
				if ($edo==15){
//$contenido .=	" <div align='center'><font color='Red'><STRONG>ANULADO</STRONG></font></div><br>";
				  $motivo_anulacion="";
				  $motivo="SELECT memo_contenido FROM sai_docu_sopor,sai_memo WHERE doso_doc_soport=memo_id and doso_doc_fuente='".$codigo."'";
				  $result_motivo=pg_query($conexion,$motivo);
				  if ($row_motivo=pg_fetch_array($result_motivo)){
				    $motivo_anulacion=strtoupper($row_motivo['memo_contenido']);	
				  }
					 
$contenido .=	" <div align='center'><font color='Red'><STRONG>ANULADO</STRONG></font></div><br>";
				  if ($motivo_anulacion<>""){
$contenido .=	" <div align='center'><STRONG>".$motivo_anulacion."</STRONG></div><br>";				  	
				  }
  				}
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
 FROM sai_bien_reasignar_item t1,sai_biin_items t2,sai_item t3
 WHERE t1.acta_id='".$codigo."' and t1.clave_bien=t2.clave_bien and t2.bien_id=t3.id
 group by nombre,bien_id";
 $resultado_detalle=pg_query($conexion,$sql_detalle) or die("Error al consultar el detalle del acta");
 $j=0;
 $num_activos=0;
 while($rowd=pg_fetch_array($resultado_detalle)) 
 { 
 	$cantidad_bien[$num_activos]=$rowd['cantidad'];
	$bien_grupo[$num_activos]=$rowd['bien_id'];
	$bien_nombre[$num_activos]=$rowd['nombre'];
	      
	$sql_bien="SELECT * FROM sai_biin_items t1,sai_bien_marca,sai_bien_reasignar_item t3 
	WHERE t1.clave_bien=t3.clave_bien and bmarc_id=marca_id and t3.acta_id='".$codigo."' and bien_id='".$rowd['bien_id']."'";
	$resultado_bien=pg_query($conexion,$sql_bien) or die("Error al mostrar 1");
		 
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
 
 //Artículos
$sql_arti="SELECT t3.nombre,t2.cantidad,t2.medida FROM sai_bien_reasignar t1,sai_bien_reasignar_item t2,sai_item t3
WHERE t1.acta_id='".$codigo."' and t1.acta_id=t2.acta_id and id=arti_id ORDER BY t3.nombre";
$resultado_arti=pg_query($conexion,$sql_arti) or die("Error al consultar el detalle del acta");
 
  while($rowarti=pg_fetch_array($resultado_arti)) 
 {
  $contenido .=" <tr  class='textoTabla' align='center'>".
			    "<td>".$indice."</td>".
			    "<td>".$rowarti['nombre']."</td>".
				"<td>".$rowarti['medida']."</td>".
				"<td>&nbsp;</td>".
  				"<td>&nbsp;</td>".
  				"<td>&nbsp;</td>".
				"<td>".$rowarti['cantidad']."</td></tr>";
                $indice++;
 }
 
$contenido .=	" </table><br/>";
if ($infocentro<>""){
$contenido .= "<div align='center'>".$infocentro." : ".$nombre_info."<br></div>";
}
$contenido .= "<div align='center'>".$destino."</div><br/>";

$sql_str="SELECT t1.*,t3.empl_cedula, empl_nombres, empl_apellidos 
FROM sai_dependenci t1,sai_empleado t3,sai_usuario t2 WHERE 
t3.empl_cedula=t2.empl_cedula and usua_activo=true and t1.depe_id in (".$solicitante.") and t3.depe_cosige=t1.depe_id and	
t3.esta_id='1' and (carg_fundacion='".$_SESSION['gerente']."' or carg_fundacion='".$_SESSION['consultor']."' or carg_fundacion='".$_SESSION['director']."' 
or carg_fundacion='".$_SESSION['director_ej']."' or carg_fundacion='".$_SESSION['presidente']."') order by empl_nombres";
$res_q=pg_exec($sql_str) or die("Error al mostrar");	  
$i=0;
   while($depe_row=pg_fetch_array($res_q)){ 
	 $depe_nombre[$i]=$depe_row['depe_nombre'];
     $gerente[$i]= $depe_row['empl_nombres']." ".$depe_row['empl_apellidos'];
     $i++;
   }
    $num_firmas=$i;

$firma = "
		<table border='1' width='100%'>
			<tr align='center' >
";
for($h=0; $h<$num_firmas; $h++){
	$ancho=100/$num_firmas;
	$firma .= "
				<td width='".$ancho."%' style='height: 130px;'>
					Solicitado por: ".$gerente[$h]."<br/><br/><br/><br/>
					___________________<br/>".$depe_nombre[$h]."
				</td>
	";
}

$firma .= "
			</tr>
		</table>
		
		<div style='width: 100%; height: 5px;'></div>

		<table border=1 width='100%'>
			<tr>
				<td style='width: 25%;'>Revisado por: Analista de Jefatura de Bienes y Seguridad</td>
				<td style='width: 25%;'>Verificaco por: Coordinador de Jefatura de Bienes y Seguridad</td>
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
				<td valign='bottom' align='center' style='height: 110px;'>".$revisadoPorNombre."</td>
				<td valign='bottom'></td>
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
	";
	
	if($tipo_reasignacion == "1" || $tipo_reasignacion == "2"){
		$footer .= "<span>".GetNotaBienesSalidasReasignaciones()."</span>
			<div style='width: 100%; height: 15px;'></div>
		";
	}
	$footer .= "
	<span style='align=center;font-family: arial;font-style:italic;font-weight:bold;font-size: 10pt;'>SAFI - Fundación Infocentro</span><br/>
	<span style='align=center;font-family: arial;font-size: 10pt;'>
		Fecha impresión: ".fecha()."
	</span>
	";
	$contenido .= $firma;
	$properties = array("marginBottom" => ($tipo_reasignacion == "1" || $tipo_reasignacion == "2" ? 27 : 13), "footerHtml" => $footer);

pg_close($conexion);
ob_clean();
convert_to_pdf($contenido, $properties);
?>