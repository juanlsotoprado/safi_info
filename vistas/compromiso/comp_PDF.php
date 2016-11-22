<?php
ob_start();
require(SAFI_INCLUDE_PATH."/reporteBasePdf.php");
require(SAFI_INCLUDE_PATH."/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require(SAFI_INCLUDE_PATH."/html2ps/funciones.php");
require(SAFI_INCLUDE_PATH."/monto_a_letra.php");
@set_time_limit(10000);
parse_config_file(HTML2PS_DIR.'html2ps.config');

if	( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}

$objComp = $GLOBALS['SafiRequestVars']['compromiso'];// SafiModeloCompromiso::GetCompromiso(array("idCompromiso" => "comp-4007413"));

if($objComp){
	$objComp->UTF8Encode();
	$objCompArray = $objComp->ToArray();
}


if($objComp){

	$comp = $objComp != null? utf8_decode($objComp->GetId())  : '';
	$fecha = $objComp  != null? utf8_decode($objComp->GetFecha())  : '';
	$unidadDependencia = $objComp->GetGerencia() != null? utf8_decode($objComp->GetGerencia()->GetNombre()) : '';
	$proveedorSugerido = $objComp != null? utf8_decode($objComp->GetRifProveedorSugerido()) : '';



	$cadena = $proveedorSugerido;
	$caracter = ":";
	 
	if (strpos($cadena, $caracter) !== false){
		$proveedorSugerido2 = explode(":",$proveedorSugerido);
		$proveedorSugeridoVal = $proveedorSugerido2[1];
	}else{
		 
		$proveedorSugeridoVal = $proveedorSugerido;
	}

	$asunto = $objComp->GetAsunto() != null? utf8_decode($objComp->GetAsunto()->GetNombre()) : '';
	$asuntoVal = $objComp->GetAsunto() != null? utf8_decode($objComp->GetAsunto()->GetId()) : '';
	 
	$actividad = $objComp->GetActividad() != null? utf8_decode($objComp->GetActividad()->GetNombre()) : '';
	$actividadVal = $objComp->GetActividad() != null? utf8_decode($objComp->GetActividad()->GetId()) : '';
	 
	$fechaInicio = $objComp  != null? utf8_decode($objComp->GetFechaInicio())  : '';
	$fechaFin = $objComp  != null? utf8_decode($objComp->GetFechaFin())  : '';
	 
	$evento = $objComp->GetEvento() != null? utf8_decode($objComp->GetEvento()->GetNombre()) : '';
	$eventoVal = $objComp->GetEvento() != null? utf8_decode($objComp->GetEvento()->GetId()) : '';
	
	
	$controlinterno = $objComp->GetControlInterno() != null? utf8_decode($objComp->GetControlInterno()->GetNombre()) : '';
	$controlinternoVal = $objComp->GetControlInterno() != null? utf8_decode($objComp->GetControlInterno()->GetId()) : '';
	
	
	 
	$codDocumento = $objComp != null? utf8_decode($objComp->GetIdDocumento()) : '';
	$descripcion = $objComp != null?  utf8_decode($objComp->GetDescripcion()) : '';
	$localidad = $objComp->GetLocalidad() != null? utf8_decode($objComp->GetLocalidad()->GetNombre()) : '';
	 
	$infocentro = $objComp->GetInfocentro() != null? utf8_decode($objComp->GetInfocentro()->GetNombre()) : '';
	$infocentroVal = $objComp->GetInfocentro() != null? utf8_decode($objComp->GetInfocentro()->GetId()) : '';
	 
	$nParticipantes = $objComp  != null? utf8_decode($objComp->GetParticipante())  : '';
	$observacion = $objComp  != null? utf8_decode($objComp->GetObservacion())  : '';


	$montoSolicitado =  $objComp != null? utf8_decode($objComp->GetMontoSolicitado())  : 0;

	$pctaAsociado = $objComp != null? utf8_decode($objComp->GetPcta())  : '';

	$estatus = $objComp != null? utf8_decode($objComp->GetCompEstatus()) : '';
	$fechareporte = $objComp != null? utf8_decode($objComp->GetFechaReporte()) : '';
	
	//error_log(print_r( $objComp->GetUsuario() ,true));
	
	$usuario =  $objComp->GetUsuario() != null? utf8_decode($objComp->GetUsuario()->GetNombres()." ".$objComp->GetUsuario()->GetApellidos()) : '';
	$esta =  $objComp->GetEstatus() != null? utf8_decode($objComp->GetEstatus()->GetId()) : '';

	$memo = $GLOBALS['SafiRequestVars']['observacionesDoc'] != false ? $GLOBALS['SafiRequestVars']['observacionesDoc'][0]['observacion']  : '';
}


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
		
	$contenido .= "		<img  style=' width:20%; left:80%; height:200px;position:absolute;z-index:1000;' src='".GetConfig("siteURL")."/imagenes/anulado_safi_3.gif'/>";

}
$contenido .= "			  <table border='1' width='100%'>";

$contenido .=   " <tr class='nombreCampo'><td colspan='2'><b>Elaborado por:</b> ".$usuario."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Unidad/Dependencia:</b> ".$unidadDependencia."</td></tr>".
                " <tr class='nombreCampo' > <td colspan='2'><b>Punto de cuenta:</b> ".$pctaAsociado."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Estatus:</b>".$estatus."</td></tr>";

      if($esta == 15){

       $contenido .=   " <tr class='nombreCampo'><td colspan='2'><b>Motivo de Anulacion:</b> ".$memo." </td></tr>";
       
       }

$contenido .=    "  <tr class='nombreCampo'><td colspan='2' align='center'><b>Elementos del Compromiso</b> </td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Asunto: </b>".$asunto."</td></tr>".
                 " <tr class='nombreCampo'><td colspan='2'><b>Control interno: </b>".$controlinterno."</td></tr>".
                " <tr class='nombreCampo'><td colspan='2'><b>Rif del Proveedor Sugerido: </b>".$proveedorSugeridoVal."</td></tr>".
				" <tr class='nombreCampo' align='justify' style='display: none;'> <!-- campo comentado inhabilitado por presupuesto 01/04/13 -->
				  <td colspan='2'><b>Tipo Actividad: </b>".$actividad."</td></tr>".
				"  <tr class='nombreCampo' align='justify' style='display: none;'> <!-- campo comentado inhabilitado por presupuesto 01/04/13 -->
				<td colspan='2'><b>Duraci&oacute;n de la Actividad: </b>".$fechaInicio." - ".$fechaFin."</td></tr>".
                " <tr class='nombreCampo' align='justify' style='display: none;'> <!-- campo comentado inhabilitado por presupuesto 01/04/13 -->
                <td colspan='2'><b>Tipo Evento: </b>".$evento."</td></tr>".
				" <tr class='nombreCampo' align='justify'><td colspan='2'><b>c&oacute;digo  Documento: </b>".$codDocumento."</td></tr>".
  				" <tr class='nombreCampo'><td colspan='2'><b>Descripci&oacute;n: </b><br />".$descripcion."</td></tr>".
				" <tr class='nombreCampo' align='justify'><td colspan='2'><b>Fecha Reporte: </b>".$fechareporte."</td></tr>".
				" <tr class='nombreCampo' align='justify'><td colspan='2'><b>Estado: </b>".$localidad."</td></tr>".
                 " <tr class='nombreCampo' align='justify'><td colspan='2'><b>Observaci&oacute;n: </b>".$observacion."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Monto solicitado:</b>".$montoLetraPdf." (BS. F. ".number_format($montoSolicitado,2,',','.').")</td></tr>";

if($objComp){
	if($objComp->GetCompromisoImputas()){

		$contenido .=	" <tr class='nombreCampo'><td colspan='2' align='center'><b>Datos de imputaci&oacute;n presupuestaria </b></td></tr>".
				" <tr class='nombreCampo'><td colspan='2'>
				".
						"<table align='center' width='95%' border='1'>".
							" <tr align='center' class='nombreCampo'>".
								" <th>Proyecto/Acci&oacute;n Centralizada</th>".
								" <th>Acci&oacute;n espec&iacute;fica</th>".
								" <th>Partida</th>".
								" <th >Monto (BsF.)</th>".
			                   " <th>Fuente de Financiamiento</th>".
							" </tr>";

		foreach ($objComp->GetCompromisoImputas() as $pctaImputa) {

			$contenido .= " <tr class='nombreCampo'>";

			if(!$pctaImputa->GetAccionCentralizada()){

				$contenido .= " <td>".$pctaImputa->GetProyectoEspecifica()->GetCentroGestor()."</td>"
				." <td>".$pctaImputa->GetProyectoEspecifica()->GetCentroCosto()."</td>";
					
				$proyAccion =$pctaImputa->GetProyecto()->GetId();
				$proyAccionEspe =$pctaImputa->GetProyectoEspecifica()->GetId();
					
			}else{
				 
				$contenido .= " <td>".$pctaImputa-> GetAccionCentralizadaEspecifica()->GetCentroGestor()."</td>"
				." <td>".$pctaImputa-> GetAccionCentralizadaEspecifica()->GetCentroCosto()."</td>";
					
				$proyAccion = $pctaImputa->GetAccionCentralizada()->GetId();
				$proyAccionEspe = $pctaImputa->GetAccionCentralizadaEspecifica()->GetId();
					
					
			}

			$contenido .= " <td>".$pctaImputa->GetPartida()->GetId()."</td>";
			$contenido .= "  <td align='right'>". number_format($pctaImputa->GetMonto(),2,',','.')."</td>";
			$contenido .= "  <td align='center' >".$GLOBALS['SafiRequestVars']['funteFinanciera'][$proyAccion.'-'.$proyAccionEspe]."</td>";

				
			$contenido .= " </tr>";
				

		}
	}

}
 
$contenido .= 		"</table>".
				" <br/></td></tr>";


 

$contenido .="<tr><td align='center' colspan='2'><span class='nombreCampo'><b>Firmas</b></span></td></tr>";
$contenido .="<tr>";

$contenido .="<td align='center' style='width:450px;'><span class='nombreCampo'>Registrado por:<br><br><br>____________________<br/><br/>".utf8_decode("Analista de Planificación, Presupuesto y Control")."<br><br></span></td>";
$contenido .="<td align='center' ><span class='nombreCampo'>Aprobado por:<br><br><br>____________________<br/><br/>Jefe/Director de Planificaci&oacute;n, Presupuesto y Control</span></td>";

$contenido .="</tr>";


$contenido .= 		"</table>
				 ";


$header = 	"<img width='1000px' src='http://safi.infocentro.gob.ve/imagenes/encabezado.jpg'/>";
$header .=	"<br/><br/><table width='100%' style='font-size: 17pt;'><tr><td align='right' valign='top' width='55%'><b>".$comp."</b></td><td align='right' valign='top' style='font-size: 13pt;'>Fecha: ".$fecha."</td></tr></table>";

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
 
echo $header.$contenido;

$properties = array("headerHtml" => $header);
ob_clean();
convert_to_pdf($contenido, $properties);
