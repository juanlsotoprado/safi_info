<?php
require("../../../includes/reporteBasePdf.php"); 
require("../../../includes/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require("../../../includes/html2ps/funciones.php");
require("../../../includes/funciones.php");
	

$contenido = "<div align='center'>
					<b>SALDOS CORRECTOS CONTABLES PARA LA CUENTA NRO.".$form->GetCuentaBancaria()->GetId()." AL ".$form->GetFecha()."</b>
			</div>
			<table>
				<tr align='center'>
					<th><b>Afectaci&oacute;n</b></th>
					<th>Descripci&oacute;n</th>
					<th>Parcial</th>
					<th>Contabilidad</th>
					<th>Banco</th>
				</tr>
				<tr align='center'>
					<td>".$fechaFfin."</td>
					<td>&nbsp;</td>
					<td>&nbsp; </td>
					<td align='right'>".number_format($_SESSION['resultados'],2,',','.')."</td>					
					<td align='right'>".number_format($_SESSION['saldo_banco']*-1,2,',','.')."</td>
				</tr>";	

	$sumatoria_contabilidad = $_SESSION['resultados'];
	$sumatoria_banco = $_SESSION['saldo_banco']*-1;	
	
$contenido .=	"<tr>
					<td>&nbsp;</td>
					<td align='center' colspan='2'><b>Cheques/Transferencias en Tr&aacute;nsito</b></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>				
				</tr>";

if(is_array($GLOBALS['SafiRequestVars']['listaDocumentos']['cheques_transito']) && count($GLOBALS['SafiRequestVars']['listaDocumentos']['cheques_transito'])>0){
	foreach ($GLOBALS['SafiRequestVars']['listaDocumentos']['cheques_transito'] as $listaDocumento) {
		$sumatoria_cheq_transito_banco += $listaDocumento['monto'];
		if (strcmp(trim($listaDocumento['fecha_mes2']),substr(trim($listaDocumento['fecha']),3,7))==0) 
			$sumatoria_cheq_mes_transito += $listaDocumento['monto'];

		$contenido .= "<tr>
					<td align='left'>".$listaDocumento['fecha']."</td>
					<td align='left'>".$listaDocumento['referencia'].' '.$listaDocumento['beneficiario']."</td>
					<td align='right'>".number_format($listaDocumento['monto'],2,',','.')."</td>
					<td align='right'>&nbsp;</td>
					<td align='right'>&nbsp;</td>
				</tr>";
	} 
}	
	$contenido .= "<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align='right'>".number_format($sumatoria_cheq_mes_transito*-1,2,',','.')."</td>
					<td align='right'>".number_format($sumatoria_cheq_transito_banco,2,',','.')."</td>
					</tr>";
 
	$sumatoria_contabilidad += $sumatoria_cheq_mes_transito*-1;
	$sumatoria_banco += $sumatoria_cheq_transito_banco;	

	$contenido .= "<tr>
						<td>&nbsp;</td>
						<td align='center' colspan='2'><b>Dep&oacute;sitos/D&eacute;bitos en Tr&aacute;nsito</b></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>				
					</tr>";

if(is_array($GLOBALS['SafiRequestVars']['listaDocumentos']['codi_transito']) && count($GLOBALS['SafiRequestVars']['listaDocumentos']['codi_transito'])>0){
	foreach ($GLOBALS['SafiRequestVars']['listaDocumentos']['codi_transito'] as $listaDocumento) {
		$sumatoria_codi_transito += $listaDocumento['monto'];

		$contenido .= "<tr>
						<td align='left'>".$listaDocumento['fecha']."</td>
						<td align='left'>".$listaDocumento['referencia'].' '. $listaDocumento['comentario']."</td>
						<td align='right'>".number_format($listaDocumento['monto'],2,',','.')."</td>
						<td align='left'>&nbsp;</td>
						<td align='left'>&nbsp;</td>
					</tr>";
	} 
}  
	$contenido .= "<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align='right'>".number_format($sumatoria_codi_transito,2,',','.')."</td>
						<td align='right'>".number_format($sumatoria_codi_transito*-1,2,',','.')."</td>
					</tr>";

	$sumatoria_contabilidad += $sumatoria_codi_transito;
	$sumatoria_banco += $sumatoria_codi_transito*-1;	
	
	$contenido .= "<tr>
						<td>&nbsp;</td>
						<td align='center' colspan='2'><b>Dep&oacute;sitos/D&eacute;bitos conciliados<b></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>				
					</tr>";

//$sql_codi_conciliado;
if(is_array($GLOBALS['SafiRequestVars']['listaDocumentos']['codi_conciliado']) && count($GLOBALS['SafiRequestVars']['listaDocumentos']['codi_conciliado'])>0){
	foreach ($GLOBALS['SafiRequestVars']['listaDocumentos']['codi_conciliado'] as $listaDocumento) {
		if (strcmp(trim($fecha_mes2),substr(trim($row['fecha_emision']),3,7))==0)
			$monto_cambiado = $listaDocumento['monto']*-1;
		else $monto_cambiado = 0;
		$sumatoria_codi_conciliado_contabilidad += $monto_cambiado*-1;		
		$sumatoria_codi_conciliado_banco += $listaDocumento['monto'];		

		$contenido .= "	<tr>
						<td align='left'>".$listaDocumento['fecha']."</td>
						<td align='left'>".$listaDocumento['referencia'].' '.$listaDocumento['comentario']."</td>
						<td align='left'>&nbsp;</td>
						<td align='right'>".number_format($monto_cambiado,2,',','.')."</td>
						<td align='right'>".number_format($listaDocumento['monto']*-1,2,',','.')."</td>
					</tr>";
	}
}	
	$contenido .= "<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align='right'>".number_format($sumatoria_codi_conciliado_contabilidad,2,',','.')."</td>
						<td align='right'>".number_format($sumatoria_codi_conciliado_banco*-1,2,',','.')."</td>
					</tr>";

	$sumatoria_contabilidad += $sumatoria_codi_conciliado_contabilidad;
	$sumatoria_banco += $sumatoria_codi_conciliado_banco*-1;	

	$contenido .= "<tr>
						<td>&nbsp;</td>
						<td align='center' colspan='2'><b>Cheques/Transferencias conciliados</b></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>				
					</tr>";
	
if(is_array($GLOBALS['SafiRequestVars']['listaDocumentos']['cheques_conciliados']) && count($GLOBALS['SafiRequestVars']['listaDocumentos']['cheques_conciliados'])>0){
	foreach ($GLOBALS['SafiRequestVars']['listaDocumentos']['cheques_conciliados'] as $listaDocumento) {
		$sumatoria_cheq_conciliado_banco += $listaDocumento['monto'];	

		$contenido .= "<tr>
						<td align='left'>".$listaDocumento['fecha']."</td>
						<td align='left'>".$listaDocumento['referencia'].' '.$listaDocumento['beneficiario']."</td>
						<td align='left'>&nbsp;</td>";

		if ((strcmp(trim($listaDocumento['fecha_mes2']),substr(trim($listaDocumento["fecha_emision"]),3,7))==0))
		if (strlen($listaDocumento["fecha_anulacion"])>2) {
			if ((strcmp(trim($listaDocumento['fecha_mes2']),substr(trim($listaDocumento["fecha_anulacion"]),3,7))==0))
				$monto_contabilidad = $listaDocumento["monto_contable"]*-1;
			else
				$monto_contabilidad = 0;
		}
		else
			$monto_contabilidad = $listaDocumento["monto_contable"]*-1;
		else
			$monto_contabilidad = 0;
		
		$sumatoria_cheq_conciliado_contabilidad += $monto_contabilidad;	
		
		$contenido .= "<td align='right'>".number_format($monto_contabilidad,2,',','.')."</td>
					<td align='right'>".number_format($listaDocumento['monto'],2,',','.')."</td>
				</tr>";
	}
}
	$contenido .= "<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align='right'>".number_format($sumatoria_cheq_conciliado_contabilidad,2,',','.')."</td>
						<td align='right'>".number_format($sumatoria_cheq_conciliado_banco,2,',','.')."</td>
					</tr>";

	$sumatoria_contabilidad += $sumatoria_cheq_conciliado_contabilidad;
	$sumatoria_banco += $sumatoria_cheq_conciliado_banco;	

	$contenido .= "<tr>
						<td>&nbsp;</td>
						<td align='center' colspan='2'><b>Cheques en Tr&aacute;nsito Anulados</b></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>				
					</tr>";
	
if(is_array($GLOBALS['SafiRequestVars']['listaDocumentos']['cheques_anulados']) && count($GLOBALS['SafiRequestVars']['listaDocumentos']['cheques_anulados'])>0){
	foreach ($GLOBALS['SafiRequestVars']['listaDocumentos']['cheques_anulados'] as $listaDocumento) {
		$sumatoria_cheq_anulado += $listaDocumento['monto'];

		$contenido .= "<tr>
						<td align='left'>".$listaDocumento['fecha']."</td>
						<td align='left'>".$listaDocumento['referencia'].' '.$listaDocumento['beneficiario']."</td>
						<td align='right'>".number_format($listaDocumento['monto']*-1,2,',','.')."</td>
						<td align='left'>&nbsp;</td>
						<td align='left'>&nbsp;</td>
					</tr>";
	}
}
	$contenido .= "	<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align='right'>".number_format($sumatoria_cheq_anulado,2,',','.')."</td>
						<td align='right'>".number_format($sumatoria_cheq_anulado*-1,2,',','.')."</td>
					</tr>";
 
	$sumatoria_contabilidad += $sumatoria_cheq_anulado;
	$sumatoria_banco += $sumatoria_cheq_anulado*-1;	

	$contenido .= "	<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align='right'>".number_format($sumatoria_contabilidad,2,',','.')."</td>
					<td align='right'>".number_format($sumatoria_banco,2,',','.')."</td>
				</tr>	
	</table>"; 

$properties = array("marginBottom" => 25, "footerHtml" => $footer);
convert_to_pdf($contenido, $properties);
?>	