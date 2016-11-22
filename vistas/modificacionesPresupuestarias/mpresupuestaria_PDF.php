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

  $objPmod =  $GLOBALS['SafiRequestVars']['pmod'] ;

if($objPmod){
	$objPmod->UTF8Encode();
	$objPmodArray = $objPmod->ToArray();
}


if($objPmod){


	$pmod = $objPmod != null? utf8_decode($objPmod->GetId())  : '';
	$fecha = $objPmod  != null? utf8_decode($objPmod->GetFecha())  : '';
	$observacion = $objPmod  != null? utf8_decode($objPmod->GetObservacion())  : '';
	$unidadDependencia = $objPmod->GetDependencia() != null? utf8_decode($objPmod->GetDependencia()->GetNombre()) : '';
	$esta = $objPmod != null? utf8_decode($objPmod->GetEstatus()->GetId()) : '';
    $estatus = $objPmod != null? utf8_decode($objPmod->GetEstatus()->GetNombre()) : '';

     $tipo = $objPmod != null? utf8_decode($objPmod->GetTipoDoc()) : '';
      
                       if($tipo == 1){

						 	$tipo = "Cr&eacute;dito";
						 	
						 }else if($tipo == 2){
						 	
						 	$tipo = "Traspaso";
						 	
						 }else{
						 	
						 	$tipo = "Disminuci&oacute;n";
						 	
						 } 
}




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

$contenido .=  " <tr class='nombreCampo'><td colspan='2'><b>Elaborado por:</b> ".$GLOBALS['SafiRequestVars']['usuario']."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Unidad/Dependencia:</b> ".$unidadDependencia."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Estado:</b>".$estatus."</td></tr>";

      if($esta == 15){

       $contenido .=   " <tr class='nombreCampo'><td colspan='2'><b>Motivo de Anulacion:</b> ".$memo." </td></tr>";
       
       }
$contenido .= "			  <table border='1' width='100%'>";


      if($esta == 15){

       $contenido .=   " <tr class='nombreCampo'><td colspan='2'><b>Motivo de Anulacion:</b> ".$memo." </td></tr>";
       
       }

$contenido .= 
				" <tr class='nombreCampo'><td colspan='2'><b>Tipo: </b>".$tipo."</td></tr>".
                " <tr class='nombreCampo' align='justify'><td colspan='2'><b>Observaci&oacute;n: </b>".$observacion."</td></tr>";

if($objPmod){

	if($objPmod->GetMpresupuestariasImputas()){
		


		$contenido .=	" <tr class='nombreCampo'><td colspan='2' align='center'><b>Datos de imputaci&oacute;n presupuestaria </b></td></tr>".
				" <tr class='nombreCampo'><td colspan='2'>
				".
						"<table align='center' width='95%' border='1'>".
							" <tr align='center' class='nombreCampo'>".
								" <th>Proyecto/Acci&oacute;n Centralizada</th>".
								" <th>Acci&oacute;n espec&iacute;fica</th>".
								" <th>Partida</th>".
								" <th >Monto (BsF.)</th>".
			                   " <th>Acci&oacute;n</th>".
							" </tr>";

		foreach ($objPmod->GetMpresupuestariasImputas() as $pmodImputa) {
			
					
		
                       if($tipo == 1){

						 	$montoSolicitado = $pmodImputa->GetMonto();
						 	
						 }else if($tipo == 2){
						 	
						 if($pmodImputa->GetTipo() < 1){
	
						 	
						 $montoSolicitado = $pmodImputa->GetMonto();
						 
						 }
						 }else{
						 	
						 	$montoSolicitado = $pmodImputa->GetMonto();
						 	
						 } 
						 
						 $montoLetraPdf=monto_letra($montoSolicitado, " BOL&Iacute;VARES");

			$contenido .= " <tr class='nombreCampo'>";

			if(!$pmodImputa->GetAccionCentralizada()){

				$contenido .= " <td>".$pmodImputa->GetProyectoEspecifica()->GetCentroGestor()."</td>"
				." <td>".$pmodImputa->GetProyectoEspecifica()->GetCentroCosto()."</td>";
					
				$proyAccion =$pmodImputa->GetProyecto()->GetId();
				$proyAccionEspe =$pmodImputa->GetProyectoEspecifica()->GetId();
					
			}else{
				 
				$contenido .= " <td>".$pmodImputa-> GetAccionCentralizadaEspecifica()->GetCentroGestor()."</td>"
				." <td>".$pmodImputa-> GetAccionCentralizadaEspecifica()->GetCentroCosto()."</td>";
					
				$proyAccion = $pmodImputa->GetAccionCentralizada()->GetId();
				$proyAccionEspe = $pmodImputa->GetAccionCentralizadaEspecifica()->GetId();
					
					
			}

			$contenido .= " <td>".$pmodImputa->GetPartida()->GetId()."</td>";
			$contenido .= "  <td align='right'>".number_format($pmodImputa->GetMonto(),2,',','.')."</td>";
			
	 
			if($pmodImputa->GetTipo() == 1){
				 $tipoimp = "Recibe";
	      	}else{
				 	$tipoimp =  "Cede";
	      	}
			
			$contenido .= "  <td align='right'>".$tipoimp."</td>";
			$contenido .= " </tr>";


		}	
	}
	
	


}
 

 

$contenido .= "</table>";

 if($tipo == 1){

    $contenido .= " <tr class='nombreCampo'><td colspan='2'><b>Monto solicitado:</b>".$montoLetraPdf." (BS. F. ".number_format($montoSolicitado,2,',','.').")</td></tr>";
 					 	
   }


$header = 	"<img width='1000px' src='http://safi.infocentro.gob.ve/imagenes/encabezado.jpg'/>";
$header .=	"<br/><br/><table width='100%' style='font-size: 17pt;'><tr><td align='right' valign='top' width='65%'><b>Modificacion  ".$pmod."</b></td><td align='right' valign='top' style='font-size: 13pt;'>Fecha: ".$fecha."</td></tr></table>";

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

?>
