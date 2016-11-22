<?php
ob_start();
require_once(dirname(__FILE__) . '/../../init.php');
require_once (SAFI_LIB_PATH . '/general.php');
require_once(SAFI_INCLUDE_PATH. '/perfiles/constantesPerfiles.php');
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

$codigo=$_REQUEST['codigo'];
$tipo=$_REQUEST['tipo'];

$query=	"SELECT ".
		    "sp.esta_id, ".
			"sp.pcta_garantia, ".
			"sp.pcta_prioridad, ".
			"sp.rif_sugerido, ".
			"sp.pcta_id, ".
			"sp.pcta_id_dest, ".
			"se.empl_nombres as empl_nombres_presenta, ".
			"se.empl_apellidos as empl_apellidos_presenta, ".
			"pcta_asociado, ".
			"sc.carg_nombre, ".
			"sc.carg_fundacion, ".
			"sd.depe_nombre, ".
			"sd3.depe_id, ".
			"sd2.depe_nombre AS depe_solicitante2, ".
			"ser.empl_nombres as empl_nombres_remitente, ".
			"ser.empl_apellidos as empl_apellidos_remitente, ".
			"seu.empl_nombres as empl_nombres_elabora, ".
			"seu.empl_apellidos as empl_apellidos_elabora, ".
			"to_char(sp.pcta_fecha,'DD/MM/YYYY') as pcta_fecha, ".
			"sp.pcta_observacion, ".
			"sp.pcta_descripcion, ".
			"sp.pcta_justificacion, ".
			"sp.pcta_monto_solicitado, ".
			"sp.numero_reserva, ".
			"sp.pcta_lapso, ".
			"sp.pcta_cond_pago, ".
			"spa.pcas_nombre as asunto,sp.usua_login ".
		"FROM ".
			"sai_pcuenta sp, ".
			"sai_empleado se, ".
			"sai_cargo sc, ".
			"sai_dependenci sd, ".
			"sai_dependenci sd2, ".
			"sai_dependenci sd3, ".
			"sai_empleado ser, ".
			"sai_pcta_asunt spa, ".
			"sai_empleado seu ".
		"WHERE ".
			"sp.pcta_id=trim('".$codigo."') AND ".
			"sp.pcta_gerencia = sd.depe_id AND ".
			"sd2.depe_id = (SELECT depe_cosige FROM sai_empleado WHERE empl_cedula=sp.pcta_id_remit) AND ".
			"sd3.depe_id = (SELECT depe_cosige FROM sai_empleado WHERE empl_cedula=sp.pcta_presentado_por) AND ".			
			"sp.pcta_id_remit = ser.empl_cedula AND ".
			"trim(sp.pcta_asunto) = trim(spa.pcas_id) AND ".
			"sp.pcta_presentado_por = se.empl_cedula AND ".
			"se.carg_fundacion = sc.carg_fundacion AND ".
			"sp.usua_login = seu.empl_cedula ";

$result=pg_query($conexion,$query);
$row=pg_fetch_array($result);
$garantia=$row["pcta_garantia"];
$prioridad=$row["pcta_prioridad"];
$ci_destinatario=$row["pcta_id_dest"];
$presentado=$row["empl_nombres_presenta"]." ".$row["empl_apellidos_presenta"];
$pcta_asociado=$row['pcta_asociado'];
$depesolicitante=$row["depe_nombre"];
$depe_id=$row["depe_id"];
$depesolicitante2=$row["depe_solicitante2"];
$remitente=$row["empl_nombres_remitente"]." ".$row["empl_apellidos_remitente"];
$elabora=$row["empl_nombres_elabora"]." ".$row["empl_apellidos_elabora"];
$fecha=$row["pcta_fecha"];	
$observacion= $row["pcta_observacion"]; 
$descripcion=$row["pcta_descripcion"];
$cargo_fundacion=$row["carg_fundacion"];
$cargo_presentado=$row["carg_nombre"];
if (strcmp(trim($depe_id), '650')==0 || strcmp(trim($depe_id),'700')==0)
	$cargo_presentado='Coordinador Nacional';

$justificacion=$row["pcta_justificacion"];
$monto=$row["pcta_monto_solicitado"];
$numeroreserva=$row["numero_reserva"];
$lapso=$row["pcta_lapso"];
$condicion=$row["pcta_cond_pago"];
$asunto=$row["asunto"];
$rif=$row["rif_sugerido"];
$edo=$row['esta_id'];
$usua_login=$row['usua_login'];

if (strlen($pcta_asociado)>5) {
	$query =	"SELECT 
					spa.pcas_nombre as asunto 
				FROM sai_pcuenta p
				INNER JOIN sai_pcta_asunt spa ON (p.pcta_asunto = spa.pcas_id)
				WHERE p.pcta_id = '".$pcta_asociado."'";

	$result_destino=pg_query($conexion,$query);
	$asunto = "";
	if($rowd=pg_fetch_array($result_destino)){
		$asunto = $rowd["asunto"]." (alcance al ".$pcta_asociado.")";
	}	
 
	
}
    
if(strpos($ci_destinatario,"/")) {
	$ci_1=substr($ci_destinatario,0,strpos($ci_destinatario,"/"));
 	$ci_2=substr($ci_destinatario,strpos($ci_destinatario,"/")+1);
	$query_destino =	"SELECT ".
							"se.empl_nombres, ".
							"se.empl_apellidos, ".
							"sc.carg_nombre ".
						"FROM sai_empleado se, sai_cargo sc ".
						"WHERE se.carg_fundacion=sc.carg_fundacion AND ".
							"(empl_cedula='".$ci_1."' OR empl_cedula='".$ci_2."')";
}else{
	$query_destino =	"SELECT ".
							"se.empl_nombres, ".
							"se.empl_apellidos, ".
							"sc.carg_nombre ".
						"FROM sai_empleado se, sai_cargo sc ".
						"WHERE ".
							"se.carg_fundacion = sc.carg_fundacion AND ".
							"empl_cedula = '".$ci_destinatario."'";
}

$result_destino=pg_query($conexion,$query_destino);
$destinatario = "";
if($rowd=pg_fetch_array($result_destino)){
	$destinatario=$rowd["empl_nombres"]." ".$rowd["empl_apellidos"]."   ";
	$cargodestinatario=$rowd["carg_nombre"]."   ";
}
$destinatario2 = "";
if($rowd=pg_fetch_array($result_destino)) {
	$destinatario2=$rowd["empl_nombres"]." ".$rowd["empl_apellidos"]."   "; 
	$cargodestinatario2=$rowd["carg_nombre"]."   ";
}

$montoletras=monto_letra($monto, " BOLIVARES");
while(($indice=strpos($descripcion,"<p"))>0) {
	$contenidoAuxiliar = substr($descripcion,$indice);
	$descripcion = substr($descripcion,0,
	$indice).substr($contenidoAuxiliar,strpos($contenidoAuxiliar, ">")+1);
}
$descripcion = str_replace("</p>","",$descripcion);

if ($destinatario2<>''){
	$cargodestinatario2=$cargodestinatario.": ".$destinatario. ",".$cargodestinatario2.": ".$destinatario2;
}else{ 
	$cargodestinatario2=$cargodestinatario.": ".$destinatario;
}
if ($prioridad==1) { $prioridad="Baja"; } 
if ($prioridad==2) { $prioridad="Media";} 
if ($prioridad==3) { $prioridad="Alta"; }

/*if (($pcta_asociado<>"") && ($pcta_asociado<>"0")){
	$asunto=$asunto." ".$pcta_asociado;
}*/

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
					</style><br>
			  <table border=1 width='100%'>";
				//" <tr class='nombreCampo'><td colspan='2' align='center'><b>Punto de cuenta ". $codigo."</b></td></tr>";
				if ($edo==15){
$contenido .=	" <tr class='nombreCampo'><td colspan='2' align='center'><font color='Red'><STRONG>ANULADO</STRONG></font></td></tr>";
  				}

$contenido .=	" <tr class='nombreCampo'><td colspan='2'><b>Preparado para:</b> ".$cargodestinatario2."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Solicitado por:</b> ".$remitente."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Presentado por:</b> ".$presentado."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Dependencia que tramita:</b> ".$depesolicitante."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Dependencia que solicita:</b> ".$depesolicitante2."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2' align='center'><b>Elementos del punto de cuenta</b> </td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Asunto: </b>".$asunto."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Descripci&oacute;n: </b><br />".$descripcion."</td></tr>".
				" <tr class='nombreCampo' align='justify'><td colspan='2'><b>Justificaci&oacute;n: </b>".$justificacion."</td></tr>".
				" <tr class='nombreCampo' align='justify'><td colspan='2'><b>Lapso del convenio/contrato: </b>".$lapso."</td></tr>".
				" <tr class='nombreCampo' align='justify'><td colspan='2'><b>Garant&iacute;a: </b>".$garantia."</td></tr>".
                " <tr class='nombreCampo'><td colspan='2'><b>R.I.F. Proveedor sugerido: </b>".$rif."</td></tr>".
				" <tr class='nombreCampo' align='justify'><td colspan='2'><b>Condiciones de pago: </b>".$condicion."</td></tr>".
				" <tr class='nombreCampo' align='justify'><td colspan='2'><b>Observaci&oacute;n: </b>".$observacion."</td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>Monto solicitado:</b> ". strtoupper($montoletras) ." (BS. F. ".number_format($monto,2,',','.').")</td></tr>".
				" <tr class='nombreCampo'><td colspan='2' align='center'><b>Datos de imputaci&oacute;n presupuestaria </b></td></tr>".
				" <tr class='nombreCampo'><td colspan='2'>".
						"<table align='center' width='70%' border='1'>".
							" <tr align='center' class='nombreCampo'>".
								" <td>Proyecto/Acci&oacute;n Centralizada</td>".
								" <td>Acci&oacute;n espec&iacute;fica</td>".
								" <td>Partida</td>".
								" <td>Monto (BsF.)</td>".
							" </tr>";

$query = "SELECT ".
			"spi.pcta_monto, ".
			"spi.pcta_sub_espe, ".
			"sae.aces_nombre as centralespnombre, ".
			"sae.centro_gestor as centrogestorac, ".
			"sae.centro_costo as centrocostoac, ".
			"spae.paes_nombre as proyectoespnombre, ".
			"spae.centro_gestor as centrogestorpr, ".
			"spae.centro_costo as centrocostopr, ".
			"sac.acce_denom as centralprinombre, ".
			"sp.proy_titulo as proyectoprinombre ".
		"FROM ".
			"sai_pcta_imputa spi ".
			"left outer join sai_acce_esp sae on (spi.pcta_acc_pp=sae.acce_id and spi.pcta_acc_esp=sae.aces_id and spi.pres_anno=sae.pres_anno) ".
			"left outer join sai_ac_central sac on (sae.acce_id=sac.acce_id and spi.pres_anno=sac.pres_anno) ".
			"left outer join sai_proy_a_esp spae on (spi.pcta_acc_pp=spae.proy_id and spi.pcta_acc_esp=spae.paes_id and spi.pres_anno=spae.pres_anno) ".
			"left outer join sai_proyecto sp on (spae.proy_id=sp.proy_id and spi.pres_anno=sp.pre_anno)	".
		"WHERE spi.pcta_id='".$codigo."' order by spi.pcta_sub_espe";

$result=pg_query($conexion,$query);
while($row=pg_fetch_array($result)) {
	$centralespnombre=$row["centralespnombre"];
	$centralprinombre=$row["centralprinombre"];
	$proyectoespnombre=$row["proyectoespnombre"];
	$proyectoprinombre=$row["proyectoprinombre"];

	$montosubespecifica=$row['pcta_monto'];
	$subespecifica=$row["pcta_sub_espe"];
	$centrogestorac=$row["centrogestorac"];
	$centrocostoac=$row["centrocostoac"];
	$centrogestorpr=$row["centrogestorpr"];
	$centrocostopr=$row["centrocostopr"];

	$contenido .= " <tr class='nombreCampo'>"
					." <td>".$centrogestorac."&nbsp;". $centrogestorpr."</td>"
					." <td>".$centrocostoac."&nbsp;".$centrocostopr."</td>"
					." <td>".$subespecifica."</td>"
					." <td>".number_format($montosubespecifica,2,',','.')."</td>"
				." </tr>";
}
$contenido .= 		"</table>".
				" </td></tr>";
				
	$l=strlen($codigo);
	$ao=substr($codigo,$l-2,$l);
	$dependencia=substr($codigo,5,3);
	
	if ($ao<10){
	 $contenido .= " </tr>".
				   " <tr class='nombreCampo'><td colspan='2'><b>N&uacute;mero de reserva:</b> ".$numeroreserva."</td></tr>";
				   
	}
					
$contenido .=	" </table><br/>";
$firma = "<table border='1' width='100%'>";
 
if (substr($asunto,0,4)!="Libe"){
	$firmasSeleccionadas;
	$firmasSeleccionadas = array();
	$concatenar=$cargo_fundacion.$depe_id;
	$firmasSeleccionadas = SafiModeloFirma::GetFirmaByPerfiles(array($concatenar,PERFIL_DIRECTOR_PRESUPUESTO,PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS,PERFIL_DIRECTOR_EJECUTIVO,PERFIL_PRESIDENTE));	
	
	//$firma .= "<tr class='nombreCampo'>
		//		<td colspan='4' align='center'><b>Firmas</b></td>
			//</tr>";
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
			
				$firma .= "<td height='120' colspan='4' valign='bottom'  align='center' >
				_________________________________<br/>
				<span style='line-height: 1.5em;'>".$firmasSeleccionadas[PERFIL_PRESIDENTE]['nombre_empleado']."<br/></span>
				".$firmasSeleccionadas[PERFIL_PRESIDENTE]['nombre_cargo_dependencia']."</td>
				
				
			</tr>	

			<tr align='center' class='textoTabla'>";
			
				$firma .= "<td height='90' colspan='4' valign='top' align='center'>
				
				<table  height='90' width='500' style='margin-left:130px;'  >
			    	<tr>
				      <td width='250' style='padding-left:5px;' align='left' > <input type='checkbox'> Aprobado<br></td>
				      <td width='250'  style='padding-left:5px;' align='left'><input type='checkbox'> Negado<br></td>
				    </tr>  
				    <tr> 
				      <td width='250' style='padding-left:5px;' align='left'><input type='checkbox'> Diferido<br></td>
				      <td width='250'  style='padding-left:5px;' align='left'><input type='checkbox'> Visto<br></td>
				    </tr>
				</table>
				
				
				
				
				</td>
				
				
			</tr>	
			
			
			<tr align='center' class='textoTabla'>";
			
			  $firma .= "<td height='90' colspan='4'  valign='top' style='' align='left' > Observaciones: </td>
				
				
			</tr>	
			
			
			
			
			
			
			
			
			
			
			
			
			
			
		</table><br/>";	

	
						
}else{
	if ($usua_login=="14196354"){
	$firma .="<tr>";
	$firma .="<tr><td align='center' colspan='4' width='360'><span class='nombreCampo'><br>____________________<br/><br/>".utf8_decode('Oficina de Planificaci&oacute;n, Presupuesto y Control')."</span></td>";	
	}else{
	$firma .="<tr>";
	$firma .="<td align='center' colspan='4'><span class='nombreCampo'>FIRMAS</span></td></tr>";
	$firma .="<tr><td align='center' colspan='2'><span class='nombreCampo'><br>____________________<br/><br/>".strtoupper($_SESSION['cargo'])."<br><br></span></td>";
	$firma .="<td align='center' colspan='2' ><span class='nombreCampo'><br>____________________<br/><br/>".utf8_decode('Oficina de Planificaci&oacute;n, Presupuesto y Control')."</span></td>";
	}
}
$firma .=		"</tr>".
				"</table>";

$firma .= "<br/>".
		" <font size='2'><b>Elaborado por:</b>___________________________ <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".
		$elabora."</font>";
		

if($tipo=="F"){
	$footer = "<br/>".$firma."<br/>Punto de cuenta: ".$_SESSION["pcta"].
				"<style type='text/css'>
						@page {
					 		@bottom-right {
					 			margin-top: 98mm;
					    		content: 'Página ' counter(page) ' de ' counter(pages);
					  		}
						}
					</style>";
	$properties = array("marginBottom" => 95, "footerHtml" => $footer);
}else if($tipo=="L"){
	
	
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
	
}
pg_close($conexion);
ob_clean();
convert_to_pdf($contenido, $properties);
