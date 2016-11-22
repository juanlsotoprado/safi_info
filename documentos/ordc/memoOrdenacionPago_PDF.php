<?php
require_once(dirname(__FILE__) . '/../../init.php');
require_once (SAFI_LIB_PATH . '/general.php');
require_once(SAFI_MODELO_PATH. '/firma.php');
require("../../includes/conexion.php");
require("../../includes/constantes.php");
require("../../includes/perfiles/constantesPerfiles.php");
if	( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush();
	exit;
}

require("../../includes/funciones.php");
require("../../includes/reporteBasePdf.php");
require("../../includes/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require("../../includes/html2ps/funciones.php");

$idOrdc=$_REQUEST['idOrdc'];
$factura=$_REQUEST['factura'];
$concepto=$_REQUEST['concepto'];
$estadoActivo = "1";
$depeIdAdministracion = substr(PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS, 2);
$cargoCoordinador = substr(PERFIL_COORDINADOR,0,2);
$fecha = date('d/m/y');

if($idOrdc && $idOrdc!=""){
	if (($factura && $factura!="") || ($concepto && $concepto!="")) {
		$bandera = false;
		$query = 	"UPDATE sai_orden_compra ".
					"SET ";
		if ($factura && $factura!="") {
			$query .= 	"factura = '".$factura."' ";
			$bandera = true;
		}
		if ($concepto && $concepto!="") {
			if ( $bandera == true ) {
				$query .= 	", ";				
			}
			$query .= 	"concepto = '".$concepto."' ";
			$bandera = true;
		}
		$query .=	"WHERE ".
						"ordc_id = '".$idOrdc."' ";	
		$resultado = pg_exec($conexion, $query);
	}
	
	$query = 	"SELECT ".
					"soc.codigo_analisis_cotizacion, ".	
					"INITCAP(sp.prov_nombre) AS prov_nombre, ".
					"soc.rif_proveedor_seleccionado, ".
					"SUM(scb.iva*scb.base/100) AS iva, ".
					"soc.justificacion, ".
					"sdrq.depe_id AS depe_id_solicitante, ".
					"sdrq.depe_nombre AS dependencia_solicitante, ".
					"sdoc.depe_id AS depe_id_orden_compra, ".
					"sdoc.depe_nombre AS dependencia_orden_compra, ".
					"LOWER(SUBSTRING(se.empl_nombres FROM 1 FOR 1)||SUBSTRING(se.empl_apellidos FROM 1 FOR 1)) AS firma_de, ".
					"soc.esta_id, ".
					"soc.factura, ".
					"soc.concepto, ".
					"(sec.empl_nombres || ' ' || sec.empl_apellidos) AS coordinador, ".
					"(sedc.empl_nombres || ' ' || sedc.empl_apellidos) AS solicitante ".
				"FROM ".
					"sai_orden_compra soc ".
					"INNER JOIN sai_cotizacion sc ON (soc.ordc_id = sc.ordc_id AND soc.rif_proveedor_seleccionado = sc.rif_proveedor) ".
					"INNER JOIN sai_cotizacion_base scb ON (sc.id_cotizacion = scb.id_cotizacion) ".
					"INNER JOIN sai_req_bi_ma_ser srbms ON (soc.rebms_id = srbms.rebms_id) ".
					"INNER JOIN sai_dependenci sdrq ON (srbms.depe_id = sdrq.depe_id) ".
					"INNER JOIN sai_dependenci sdoc ON (soc.depe_id = sdoc.depe_id) ".
					"INNER JOIN sai_proveedor_nuevo sp ON (soc.rif_proveedor_seleccionado = sp.prov_id_rif) ".
					"INNER JOIN sai_usuario su ON (soc.usua_login = su.usua_login) ".
					"INNER JOIN sai_empleado se ON (su.empl_cedula = se.empl_cedula) ".
					"INNER JOIN sai_empleado sec ON (soc.depe_id = sec.depe_cosige AND sec.carg_fundacion = '".$cargoCoordinador."' AND sec.esta_id = ".$estadoActivo.") ".
					"INNER JOIN sai_depen_cargo sdc ON (srbms.depe_id = sdc.depe_id AND sdc.carg_id IN ('".PERFIL_PRESIDENTE_CARGO."','".PERFIL_DIRECTOR_EJECUTIVO_CARGO."','".PERFIL_DIRECTOR."','".PERFIL_GERENTE."','".PERFIL_CONSULTOR_JURIDICO_CARGO."')) ".
					"LEFT OUTER JOIN sai_empleado sedc ON (sdc.depe_id = sedc.depe_cosige AND SUBSTR(sdc.carg_id,1,2) = sedc.carg_fundacion AND sedc.esta_id = ".$estadoActivo.") ".
				"WHERE ".
					"soc.ordc_id = '".$idOrdc."' ".
				"GROUP BY ".
					"soc.codigo_analisis_cotizacion, ".	
					"sp.prov_nombre, ".
					"soc.rif_proveedor_seleccionado, ".
					"soc.justificacion, ".
					"sdrq.depe_id, ".
					"sdrq.depe_nombre, ".
					"sdoc.depe_id, ".
					"sdoc.depe_nombre, ".
					"se.empl_nombres, ".
					"se.empl_apellidos, ".
					"soc.esta_id, ".
					"soc.factura, ".
					"soc.concepto, ".
					"sec.empl_nombres, ".
					"sec.empl_apellidos, ".
					"sedc.empl_nombres, ".
					"sedc.empl_apellidos ";
	
	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado, 0);
	
	$codigoAnalisisCotizacion = $row["codigo_analisis_cotizacion"];
	$rifProveedorSeleccionado = $row["rif_proveedor_seleccionado"];
	$rif = strtoupper(substr(trim($row["rif_proveedor_seleccionado"]),0,1))."-".substr(trim($row["rif_proveedor_seleccionado"]),1);
	$proveedor = $row["prov_nombre"];
	$depeIdSolicitante = $row["depe_id_solicitante"];
	
	$firmas = array();
	$firmasSeleccionadas = null;
	if ( $depeIdSolicitante == substr(PERFIL_DIRECTOR_PRESUPUESTO,2) ) {
		$firmas[] = PERFIL_DIRECTOR_EJECUTIVO;
		$firmasSeleccionadas = SafiModeloFirma::GetFirmaByPerfiles($firmas);
		
		$dependenciaSolicitante = $firmasSeleccionadas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_cargo_dependencia'];
		$solicitante = $firmasSeleccionadas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_empleado'];
	} else {
		$dependenciaSolicitante = $row["dependencia_solicitante"];
		$solicitante = $row["solicitante"];
	}
	$depeIdOrdenCompra = $row["depe_id_orden_compra"];
	//$dependenciaOrdenCompra = $row["dependencia_orden_compra"];
	$dependenciaOrdenCompra = "Coord. de Compras";
	$firmaDe = trim($row["firma_de"]);
	$estaId = $row["esta_id"];
	$factura = $row["factura"];
	$concepto = $row["concepto"];
	$coordinador = $row["coordinador"];
	$iva = $row["iva"];
	$asunto = "Ordenaci&oacute;n de pago";
	
	$query =	"SELECT ".
					"s.redondear, ".
					"SUM(s.subtotal) AS subtotal ".
				"FROM ".
				"( ".
					"SELECT ".
						"sc.redondear, ".
						"SUM(sci.cantidad_cotizada*sci.precio*sci.unidad) AS subtotal ".
					"FROM ".
						"sai_cotizacion sc ".
						"INNER JOIN sai_cotizacion_item sci ON (sc.id_cotizacion = sci.id_cotizacion) ".
					"WHERE ".
						"sc.ordc_id = '".$idOrdc."' AND ".
						"sc.rif_proveedor = '".$rifProveedorSeleccionado."' ".
					"GROUP BY sc.redondear ".
					"UNION ".
					"SELECT ".
						"sc.redondear, ".
						"SUM(scia.cantidad_cotizada*scia.precio*scia.unidad) AS subtotal ".
					"FROM ".
						"sai_cotizacion sc ".
						"INNER JOIN sai_cotizacion_item_adicional scia ON (sc.id_cotizacion = scia.id_cotizacion) ".
					"WHERE ".
						"sc.ordc_id = '".$idOrdc."' AND ".
						"sc.rif_proveedor = '".$rifProveedorSeleccionado."' ".
					"GROUP BY sc.redondear ".
				") AS s ".
				"GROUP BY s.redondear ";

	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado);
	$redondear = $row["redondear"];
	$subtotal = $row["subtotal"];
	if($redondear=="t"){
		$monto = round($iva+$subtotal,2);
		$monto = dontTouchThePrecision($monto,2);
	}else{
		$monto = $iva+$subtotal;
	}
	
	$query = 	"SELECT ".
					"INITCAP(se.empl_nombres || ' ' || se.empl_apellidos) AS para, ".
					"UPPER(SUBSTRING(se.empl_nombres FROM 1 FOR 1)||SUBSTRING(se.empl_apellidos FROM 1 FOR 1)) AS firma_de, ".
					"sd.depe_nombre AS dependencia_administracion ".
				"FROM ".
					"sai_empleado se ".
					"INNER JOIN sai_dependenci sd ON (se.depe_cosige = sd.depe_id) ".
				"WHERE ".
					"se.depe_cosige = '".$depeIdAdministracion."' AND ".
					"se.esta_id = ".$estadoActivo." AND ".
					"se.carg_fundacion IN (".
											"'".substr(PERFIL_DIRECTOR, 0, 2)."'".
											") ";
	$resultado=pg_query($conexion,$query);
	$row = pg_fetch_array($resultado, 0);
	$para = $row["para"];
	$firmaDe = trim($row["firma_de"])."/".$firmaDe;
	$dependenciaAdministracion = $row["dependencia_administracion"];
	
	$query = 	"SELECT ".
					"DISTINCT(s.part_id) AS part_id ".
				"FROM ".
				"( ".
					"SELECT ".
						"sip.part_id ".
					"FROM ".
						"sai_orden_compra soc ".
						"INNER JOIN sai_cotizacion sc ON (soc.ordc_id = sc.ordc_id AND soc.rif_proveedor_seleccionado = sc.rif_proveedor) ".
						"INNER JOIN sai_cotizacion_item sci ON (sc.id_cotizacion = sci.id_cotizacion) ".
						"INNER JOIN sai_item_partida sip ON (sci.id_item = sip.id_item) ".
					"WHERE ".
						"soc.ordc_id = '".$idOrdc."' ".
					"GROUP BY ".
						"sip.part_id ".
					"UNION ".
					"SELECT ".
						"sip.part_id ".
					"FROM ".
						"sai_orden_compra soc ".
						"INNER JOIN sai_cotizacion sc ON (soc.ordc_id = sc.ordc_id AND soc.rif_proveedor_seleccionado = sc.rif_proveedor) ".
						"INNER JOIN sai_cotizacion_item_adicional scia ON (sc.id_cotizacion = scia.id_cotizacion) ".
						"INNER JOIN sai_item_partida sip ON (scia.id_item = sip.id_item) ".
					"WHERE ".
						"soc.ordc_id = '".$idOrdc."' ".
					"GROUP BY ".
						"sip.part_id ".
				") AS s ".
				"ORDER BY ".
					"s.part_id ";
	$resultado=pg_query($conexion,$query);
	while($row = pg_fetch_array($resultado)){
		$imputacionPresupuestaria .= $row["part_id"]." / ";	
	}
	if(strlen($imputacionPresupuestaria)>2){
		$imputacionPresupuestaria = substr($imputacionPresupuestaria, 0, -3);
	}
	
	$contenido = "<style type='text/css'>
						.titulo{
							text-align:center;
							font-size: 24pt;
							font-weight:bold;							
						}
						.espaciado{
							height: 8px;							
						}
						.nombreCampo{
							font-family: arial;
							font-size: 22pt;
							font-style:italic;
							text-decoration: underline;
							width: 35%;
							vertical-align: middle;
						}
						.valorCampo{
							margin-top: 5px;
							margin-bottom: 5px;
						}
						.bordeTabla{
							border: solid 1px #000000;
						}
						.alineadoArriba{
							vertical-align: top;
						}
						.alineadoAbajo{
							vertical-align: bottom;
						}
						.textoComunicacion{
							FONT-WEIGHT: bold; FONT-SIZE: 22px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: underline
						}
						.textoTabla{
							FONT-WEIGHT: normal; FONT-SIZE: 22px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none
						}
						.textoAnexos{
							FONT-SIZE: 16px;
						}
					</style>".
					"<table width='1000px' class='textoTabla' cellspacing='0' cellpadding='0'>".
						"<tr><td class='espaciado' colspan='2' align='right' style='border-top: 1px solid #000000;'></td></tr>".
						/*"<tr>".
							"<td colspan='2' align='right'>".
								"O/P No. ".$codigoAnalisisCotizacion.
							"</td>".
						"</tr>".*/
						"<tr>".
							"<td colspan='2' align='right'>".
								"&nbsp;".
							"</td>".
						"</tr>".
						"<tr>".
							"<td valign='top' width='15%'>Para:</td>".
							"<td width='85%'>".
								$para." - ".$dependenciaAdministracion.
							"</td>".
						"</tr>".
						"<tr><td class='espaciado' colspan='2'></td></tr>".
						"<tr>".	
							"<td>De:</td>".
							"<td>".$dependenciaOrdenCompra."</td>".
						"</tr>".
						"<tr><td class='espaciado' colspan='2'></td></tr>".
						"<tr>".	
							"<td>Asunto:</td>".
							"<td><b>".$asunto."</b></td>".
						"</tr>".
						"<tr>".
							"<td colspan='2' style='border-bottom: 1px solid #000000;'>&nbsp;</td>".
						"</tr>".
						"<tr><td class='espaciado' colspan='2'></td></tr>".
						"<tr>".
							"<td colspan='2' valign='top'>".
								"Por el presente solicito el pago correspondiente a:".
								"<ul>".
									"<li><div style='width: 30%; float: left;'>Nombre:</div><div style='width: 70%; float: left;'>".$proveedor."&nbsp;</div></li>".
									"<li><div style='width: 30%; float: left;'>RIF:</div><div style='width: 70%; float: left;'>".$rif."&nbsp;</div></li>".
									"<li><div style='width: 30%; float: left;'>Factura:</div><div style='width: 70%; float: left;'>".$factura."&nbsp;</div></li>".
									"<li><div style='width: 30%; float: left;'>Monto:</div><div style='width: 70%; float: left;'>".number_format($monto,2,',','.')."&nbsp;</div></li>".
									"<li><div style='width: 30%; float: left;'>Imputaci&oacute;n Presupuestaria:</div><div style='width: 70%; float: left;'>".$imputacionPresupuestaria."&nbsp;</div></li>".
									"<li><div style='width: 30%; float: left;'>Concepto:</div><div style='width: 70%; float: left;'>".$concepto."&nbsp;</div></li>".
								"</ul>".
							"</td>".
						"</tr>".
						"<tr>".
							"<td colspan='2'>&nbsp;</td>".
						"</tr>".
						"<tr>".
							"<td colspan='2'>Sin otro particular,</td>".
						"</tr>".
						"<tr>".
							"<td colspan='2'>&nbsp;</td>".
						"</tr>".
						"<tr>".
							"<td colspan='2'>".
								"<table width='100%' style='font-size: 15pt;'>".
									"<tr>".
										"<td width='30%'>Atentamente,</td>".
										"<td width='70%' align='center'>Conforme con la Ordenaci&oacute;n de Pago:</td>".
									"</tr>".
								"</table>".
							"</td>".
						"</tr>".
						"<tr>".
							"<td colspan='2'>&nbsp;</td>".
						"</tr>".
						"<tr>".
							"<td colspan='2'>&nbsp;</td>".
						"</tr>".
						"<tr>".
							"<td colspan='2'>&nbsp;</td>".
						"</tr>";
	if($depeIdSolicitante==$depeIdAdministracion){
		$contenido .=		"<tr>".
								"<td colspan='2' align='center'>".
									"<table width='100%' style='font-size: 15pt;'>".
										"<tr>".
											"<td width='40%'>____________________</td>".
											"<td width='60%'>&nbsp;</td>".
										"</tr>".
										"<tr>".
											"<td>".$coordinador."</td>".
											"<td>&nbsp;</td>".
										"</tr>".
										"<tr>".
											"<td>".$dependenciaOrdenCompra."</td>".
											"<td>&nbsp;</td>".
										"</tr>".
									"</table>".
								"</td>".
							"</tr>";			
	}else{
		$contenido .=		"<tr>".
								"<td colspan='2' align='center'>".
									"<table width='100%' style='font-size: 15pt;'>".
										"<tr>".
											"<td width='30%'>____________________</td>".
											//"<td width='32%'>&nbsp;</td>".
											//"<td width='6%'>&nbsp;</td>".
											"<td width='32%'>____________________</td>".
										"</tr>".
										"<tr>".
											"<td valign='top'>".$coordinador."</td>".
										//	"<td valign='top'>&nbsp;</td>".
										//	"<td>&nbsp;</td>".
											"<td valign='top'>".$solicitante."</td>".
										"</tr>".
										"<tr>".
											"<td valign='top'>".$dependenciaOrdenCompra."</td>".
										//	"<td valign='top'>&nbsp;</td>".
										//	"<td>&nbsp;</td>".
											"<td valign='top'>".$dependenciaSolicitante."</td>".
										"</tr>".
									"</table>".
								"</td>".
							"</tr>";
	}
	
	$contenido .=		"<tr>".
							"<td colspan='2'>&nbsp;</td>".
						"</tr>".
						"<tr>".
							"<td colspan='2' class='textoAnexos'>".$firmaDe."</td>".
						"</tr>".
					"</table>";
	$header = 	"<img width='1000px' src='http://safi.infocentro.gob.ve/imagenes/encabezado.jpg'/>".
				//"<br/>".
				//"<table width='100%' style='font-size: 17pt;'><tr><td align='center' valign='top'>MEMORANDO</td></tr></table>".
				//"<table width='100%' style='font-size: 17pt;'><tr><td align='right' valign='top' width='66%'></td><td align='right' valign='top'><br/>Fecha: ".$fecha."</td></tr></table>".
				"<br/>".
				"<br/>".			
				"<table width='100%' style='font-size: 17pt;'><tr><td align='center' valign='top'><b>MEMORANDO O/P No. ".$codigoAnalisisCotizacion."</b></td></tr><tr><td align='right' valign='top'>Fecha: ".$fecha."</td></tr></table>".
				"<style type='text/css'>
					@page {
						margin-top: 35mm;
						@top-right {
							font-size: 17pt;
				 			margin-top: 30mm;
				 			margin-right: 4px;
				    		content: 'Página ' counter(page) ' de ' counter(pages);
				  		}
					}
				</style>";
	$properties = array("headerHtml" => $header);
	convert_to_pdf($contenido, $properties);
}
pg_close($conexion);
?>