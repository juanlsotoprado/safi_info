<?php
ob_start();
require(SAFI_INCLUDE_PATH."/reporteBasePdf.php"); 
require(SAFI_INCLUDE_PATH."/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require(SAFI_INCLUDE_PATH."/html2ps/funciones.php");
require(SAFI_INCLUDE_PATH."/monto_a_letra.php");
@set_time_limit(10000);
parse_config_file(HTML2PS_DIR.'html2ps.config');

$objPcta = $GLOBALS['SafiRequestVars']['puntosCuenta'];

		// error_log(print_r($objPcta,true));

$pcta = $objPcta  != null?$objPcta->GetId()  : '';
$fecha = $objPcta  != null?$objPcta->GetFecha()  : '';
$preparadoA =  $objPcta != null?$objPcta->GetDestinatario(): '';
$elaboradoPor =  $objPcta->GetUsuario() != null? $objPcta->GetUsuario()->GetId() : '';


//$solicitadoPor = $objPcta->GetRemitente() != null?$objPcta->GetRemitente()->GetNombres()." ".$objPcta->GetRemitente()->GetApellidos(): '';

/* cable ---->*/  $solicitadoPor = $objPcta->GetRemitente() != null?$objPcta->GetRemitente()->GetNombres()." ".$objPcta->GetRemitente()->GetApellidos(): 'Direcci&oacute;n de Gesti&oacute;n Administrativa y Financiera';

$elaboradoPor = $objPcta->GetUsuario() != null? $objPcta->GetUsuario()->GetId() : null;

$presentadoPor = $objPcta->GetPresentadoPor() != null?$objPcta->GetPresentadoPor()->GetNombres()." ".$objPcta->GetPresentadoPor()->GetApellidos(): '';
$dependencia = $objPcta->GetDependencia() != null?$objPcta->GetDependencia()->GetNombre(): '';
$asunto = $objPcta->GetAsunto() != null?$objPcta->GetAsunto()->GetNombre(): '';
$descripcion = $objPcta != null?$objPcta->GetDescripcion(): '';
$justificacion = $objPcta != null?$objPcta->GetJustificacion(): '';
$lapsor = $objPcta != null?$objPcta->GetLapso(): '';
$garantia = $objPcta != null?$objPcta->GetGarantia(): '';
$proveedorSugerido = $objPcta != null?$objPcta->GetRifProveedorSugerido(): '';
$condicionPago =  $objPcta != null? $objPcta->GetCondicionPago() : '';
$observacion = $objPcta  != null?$objPcta->GetObservacion()  : '';
$esta =  $objPcta->GetEstatus() != null? utf8_decode($objPcta->GetEstatus()->GetId()) : '';
$montoSolicitado =  $objPcta != null? utf8_decode($objPcta->GetMontoSolicitado())  : 0;
$pctaAsociado = $objPcta->GetPuntoCuentaAsociado()  != null? $objPcta->GetPuntoCuentaAsociado()->GetId() : '';


    if($pctaAsociado){
    	
    	$asunto = $GLOBALS['SafiRequestVars']['AsuntoPctaAsociado']." (".$asunto." ".$pctaAsociado.")";
    }

if (strcmp(trim($dependenciaId), '650')==0 || strcmp(trim($dependenciaId),'700')==0)
	$cargo_presentado='Coordinador Nacional';


$montoLetraPdf=monto_letra($montoSolicitado, " BOL&Iacute;VARES");	

$contenido = "<style type='text/css'>
						.nombreCampo{
							 FONT-SIZE: 22px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none
						}
						.bordeTabla{
							border: solid 1px #000000;
						}
						.textoTabla{
							FONT-WEIGHT: normal; FONT-SIZE: 18px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none
						}
					</style><br>";
					
					if($esta == 15){
		
	$contenido .= "		<img  style=' width:20%; left:80%; height:200px;position:absolute;z-index:1000;' src='http://localhost/safi0.2/imagenes/anulado_safi_3.gif'/>";

}
					
$contenido .=	" 		 <table border=1 width='100%'>

<tr class='nombreCampo'><td colspan='2'><b>Preparado a:</b> ".$preparadoA."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Elaborado por:</b> ".$elaboradoPor."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Solicitado por:</b> ".$solicitadoPor."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Presentado por:</b> ".$presentadoPor."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Unidad Dependencia:</b> ".$dependencia."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2' align='center'><b>Elementos del punto de cuenta</b> </td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Asunto: </b>".$asunto." </td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Descripci&oacute;n: </b><br />".$descripcion."</td></tr>".
				" <tr class='nombreCampo' align='justify'><td colspan='2'><b>Justificaci&oacute;n: </b>".$justificacion."</td></tr>".
				" <tr class='nombreCampo' align='justify'><td colspan='2'><b>Lapso del Convenio / Contrato: </b>".$lapsor."</td></tr>".
				" <tr class='nombreCampo' align='justify'><td colspan='2'><b>Garant&iacute;a: </b>".$garantia."</td></tr>".
                " <tr class='nombreCampo'><td colspan='2'><b>Proveedor Sugerido: </b>".$proveedorSugerido."</td></tr>".
				" <tr class='nombreCampo' align='justify'><td colspan='2'><b>Condiciones de Pago: </b>".$condicionPago."</td></tr>".
				" <tr class='nombreCampo' align='justify'><td colspan='2'><b>Observaci&oacute;n: </b>".$observacion."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Monto solicitado:</b>".$montoLetraPdf." (BS. F. ".number_format($montoSolicitado,2,',','.').")</td></tr>";

       if($objPcta->GetPuntoCuentasImputas()){

		$contenido .=	" <tr class='nombreCampo'><td colspan='2' align='center'><b>Datos de imputaci&oacute;n presupuestaria </b></td></tr>".
				" <tr class='nombreCampo'><td colspan='2'>".
						"<table align='center' width='70%' border='1'>".
							" <tr align='center' class='nombreCampo'>".
								" <td>Proyecto/Acci&oacute;n Centralizada</td>".
								" <td>Acci&oacute;n espec&iacute;fica</td>".
								" <td>Partida</td>".
								" <td >Monto (BsF.)</td>".
							" </tr>";
         	
       	    foreach ($objPcta->GetPuntoCuentasImputas() as $pctaImputa) {

	                $contenido .= " <tr class='nombreCampo'>";
	                

	                if(!$pctaImputa->GetAccionCentralizada()){
	                	
	                 $contenido .= " <td>".$pctaImputa->GetProyectoEspecifica()->GetCentroGestor()."</td>"
					  ." <td>".$pctaImputa->GetProyectoEspecifica()->GetCentroCosto()."</td>";
					
	                }else{
	                
	                  $contenido .= " <td>".$pctaImputa-> GetAccionCentralizadaEspecifica()->GetCentroGestor()."</td>"
					  ." <td>".$pctaImputa-> GetAccionCentralizadaEspecifica()->GetCentroCosto()."</td>";
	                
	                }
					
				$contenido .= " <td>".$pctaImputa->GetPartida()->GetId()."</td>"
				." <td align='right'>".number_format($pctaImputa->GetMonto(),2,',','.')."</td>"
				." </tr>";
			
            }
        }
         

$contenido .= 		"</table>".
				" </td></tr>
				</table>";



$firma = " <br/><br/><table border='1' width='100%'>";

	$concatenar= $GLOBALS['SafiRequestVars']['concatenar'];
	$firmasSeleccionadas = $GLOBALS['SafiRequestVars']['firmasSeleccionadas'];

	$mostrar = true;
	if (strcmp($concatenar,PERFIL_DIRECTOR_PRESUPUESTO)==0 || strcmp($concatenar,PERFIL_DIRECTOR_EJECUTIVO)==0 || strcmp($concatenar,PERFIL_PRESIDENTE)==0) {
		$mostrar = false;		
	}
		$firma .= 	"<tr align='center' class='textoTabla'>";
				if ($mostrar) {
					$firma .= "<td width='25%' valign='top'>".$firmasSeleccionadas[$concatenar]['nombre_cargo_dependencia']."</td>";
				}
				$firma .= "<td width='25%' valign='top'>".$firmasSeleccionadas[PERFIL_DIRECTOR_PRESUPUESTO]['nombre_cargo_dependencia']."</td>
				<td width='25%' valign='top'>".$firmasSeleccionadas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_cargo_dependencia']."</td>
				<td width='25%' valign='top'>".$firmasSeleccionadas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_cargo_dependencia']."</td>
			</tr>
			<tr align='center' class='textoTabla'>";
				if ($mostrar) {
					$firma .=  "<td height='85' valign='bottom'>".$firmasSeleccionadas[$concatenar]['nombre_empleado']."</td>";
				}		
				$firma .= "<td height='85' valign='bottom'>".$firmasSeleccionadas[PERFIL_DIRECTOR_PRESUPUESTO]['nombre_empleado']."</td>
				<td valign='bottom'>".$firmasSeleccionadas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_empleado']."</td>
				<td valign='bottom'>".$firmasSeleccionadas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_empleado']."</td>
				
			</tr>		

			<tr align='center' class='textoTabla'>";
			
				$firma .= "<td height='130' colspan='4' valign='bottom'  align='center' >
				_________________________________<br/>
				<span style='line-height: 1.5em;'>".$firmasSeleccionadas[PERFIL_PRESIDENTE]['nombre_empleado']."<br/></span>
				".$firmasSeleccionadas[PERFIL_PRESIDENTE]['nombre_cargo_dependencia']."</td>
				
				
			</tr>	

			<tr align='center' class='textoTabla'>";
			
				$firma .= "<td height='90' colspan='4' valign='top' align='center'>
				
				<table height='120' width='800' style='margin-left:150px; font-size:16px'  >
			    	<tr >
				      <td width='500' style='padding-left:5px; font-size:22px;' align='left' > <input   style='width:30px' type='checkbox'> Aprobado<br></td>
				      <td width='300'  style='padding-left:5px; font-size:22px;' align='left'><input  style='width:30px' type='checkbox'> Negado<br></td>
				    </tr>  
				    <tr> 
				      <td width='500' style='padding-left:5px; font-size:22px;' align='left'><input style='width:30px'  type='checkbox'/> Diferido<br></td>
				      <td width='300'  style='padding-left:5px; font-size:22px;' align='left'><input  style='width:30px' type='checkbox'/> Visto<br></td>
				    </tr>
				</table>
				

				</td>
				
				
			</tr>	
			
			
			<tr align='center' class='textoTabla'>";
			
			  $firma .= "<td height='200' colspan='4'  valign='top' style='' align='left' > Observaciones: </td>
				
				
			</tr>	
			
		</table><br/>";	

	
						

$firma .=		"</tr>".
				"</table>";

$firma .= "<br/>".
		" <font size='2'><b>Elaborado por:</b>___________________________ <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".
		$elabora."</font>";
		


	
		$header = 	"<img width='1000px' src='http://safi.infocentro.gob.ve/imagenes/encabezado.jpg'/>";
		$header .=	"<br/><br/><table width='100%' style='font-size: 17pt;'><tr><td align='right' valign='top' width='68%'><b>PUNTO DE CUENTA ".$codigo."</b></td><td align='right' valign='top' style='font-size: 13pt;'>Fecha: ".$fecha."</td></tr></table>";

		$header .=	"<style type='text/css'>
						@page {
							margin-top: 35mm;
							@top-right {
								font-size: 13pt;
					 			margin-top: 30mm;
					 			margin-right: 4px;
					    		content: 'Página ' counter(page) ' de ' counter(pages);
					  		}
						}
					</style>";
	$properties = array("headerHtml" => $header);
	$contenido .= $firma;

    $header = 	"<img width='1000px' src='http://safi.infocentro.gob.ve/imagenes/encabezado.jpg'/>";
    $header .=	"<br/><br/><table width='100%' style='font-size: 17pt;'><tr><td align='right' valign='top' width='65%'><b>Punto de Cuenta ".$pcta."</b></td><td align='right' valign='top' style='font-size: 13pt;'>Fecha: ".$fecha."</td></tr></table>";

    $header .=	"<style type='text/css'>
						@page {
							margin-top: 35mm;
							@top-right {
								font-size: 13pt;
					 			margin-top: 30mm;
					 			margin-right: 4px;
					    		content: 'Página ' counter(page) ' de ' counter(pages);
					  		}
						}
					</style>";
$properties = array("headerHtml" => $header);
convert_to_pdf($contenido, $properties);

?>
