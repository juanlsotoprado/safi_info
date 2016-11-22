<?php
require("../../../includes/reporteBasePdf.php"); 
require("../../../includes/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require("../../../includes/html2ps/funciones.php");
require("../../../includes/funciones.php");
	

$i=0;
foreach ($GLOBALS['SafiRequestVars']['listaDocumentos'] as $listaDocumento) {
	$i++;
	
	if ($i==1){
		$nro_acta = (strlen($params['numeroActa']) < 1? $listaDocumento['nro_acta'] : $params['numeroActa']);
		$contenido=	"<br><div align='center'> RELACI&Oacute;N NRO.".$nro_acta."  ".strtoupper($params['tipoBusqueda']). " ". strtoupper($GLOBALS['SafiRequestVars']['opcion'])." <br/> ENTREGADA AL DEPARTAMENTO DE CONTABILIDAD</div><br/><br/>
			<table width='100%' border='1' class='tablaalertas'>
	   		<tr class='td_gray'>
		     <td class='normalNegroNegrita' align='center'>#</td>
		     <td class='normalNegroNegrita' align='center'>Fecha de emisi&oacute;n</td>
		     <td class='normalNegroNegrita' align='center'>Movimiento</td>
		     <td class='normalNegroNegrita' align='center'>Nro.Ref.</td>
		     <td class='normalNegroNegrita' align='center'>Beneficiario</td>
		     <td class='normalNegroNegrita' align='center'>Concepto</td>
		     <td class='normalNegroNegrita' align='center'>Monto</td>
			<td class='normalNegroNegrita' align='center'>Documento</td>
		   </tr>";
		
		
		
	}
	$contenido.="<tr class='normal'>
				<td align='center'>".$i."</td>
				<td align='center'>".$listaDocumento['fecha']."</td>
				<td align='center'>".strtoupper((strlen($params['tipoBusqueda']) < 1? $listaDocumento['movimiento'] : substr($params['tipoBusqueda'],0,-1)  ))."</td> 
				<td align='right'>".$listaDocumento['nro_referencia']."</td>
				<td>".$listaDocumento['id_beneficiario'].' '.$listaDocumento['beneficiario']."</div></td>
				<td>".$listaDocumento['tipo_solicitud']."</td>
				<td align='right' >".number_format($listaDocumento['monto'],2,',','.')."</td>
				<td>".$listaDocumento['sopg']."</td>
				</tr>";
}
$contenido .= "</table><br><br><table width='100%' border='0'><tr><td colspan='2'>&nbsp;</td></tr>";
$contenido .= "<tr><td>Preparado por: ".$_SESSION['solicitante']."</td>";		 
$contenido .= "<td>Originales entregados al Departamento de Contabilidad en fecha: ".date ('d/m/Y ')."</td>";
$properties = array("marginBottom" => 25, "footerHtml" => $footer);
convert_to_pdf($contenido, $properties);
?>	