<?php
require(dirname(__FILE__) . '/../../init.php');
require("../../includes/conexion.php");
require("../../includes/constantes.php");
if	( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush(); 
	exit;
}
require(SAFI_MODELO_PATH. '/firma.php');
require("../../includes/reporteBasePdf.php"); 
require("../../includes/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require("../../includes/html2ps/funciones.php");

//$tipo=$_REQUEST['tipo'];

$idOrdc = "";
if (isset($_GET['idOrdc']) && $_GET['idOrdc'] != "") {
	$idOrdc = $_GET['idOrdc'];
}

if($idOrdc && $idOrdc!=""){
	$estadoAnulado = "15";
	$query = 	"SELECT ".
					"soc.rif_proveedor_seleccionado, ".
					"soc.criterio_seleccion, ".
					"soc.observaciones, ".
					"to_char(soc.fecha,'DD/MM/YYYY') as fecha, ".
					"soc.justificacion as justificacion, ".
					"soc.fecha_entrega as fecha_entrega, ".
					"soc.forma_pago as forma_pago, ".
					"soc.garantia_anticipo as garantia_anticipo, ".
					"soc.lugar_entrega, ".
					"soc.condiciones_entrega, ".
					"soc.otras_garantias, ".
					"soc.rebms_id, ".
					"soc.depe_id, ".
					"soc.usua_login, ".
					"sem.empl_nombres || ' ' || sem.empl_apellidos as elaborado_por, ".
					"soc.esta_id, ".
					"soc.codigo_analisis_cotizacion, ".
					"se.esta_nombre, ".
					"se.esta_id, ".
					"srbms.rebms_tipo_imputa, ".
					"srbms.rebms_imp_p_c, ".
					"srbms.rebms_imp_esp, ".
					"srbms.rebms_tipo, ".
					"sd.depe_nombre, ".
					"INITCAP(sp.prov_nombre) as prov_nombre, ".
					"sp.prov_domicilio AS prov_domicilio, ".
					"sp.prov_telefonos, ".
					"sp.prov_fax, ".
					"INITCAP(sp.prov_nombre_rl) AS prov_nombre_rl, ".
					"soc.otras_condiciones_observaciones ".
				"FROM sai_orden_compra soc, sai_req_bi_ma_ser srbms, sai_estado se, sai_dependenci sd, sai_proveedor_nuevo sp, sai_usuario su, sai_empleado sem ".
				"WHERE ".
				"soc.ordc_id = '".$idOrdc."' AND ".
				"soc.rif_proveedor_seleccionado = sp.prov_id_rif AND ".
				"soc.esta_id = se.esta_id AND ".
				"soc.rebms_id = srbms.rebms_id AND ".
				"srbms.depe_id = sd.depe_id AND ".
				"soc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula ";
	
	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado, 0);
	
	$rif_proveedor_seleccionado = $row["rif_proveedor_seleccionado"];
	$criterio_seleccion = $row["criterio_seleccion"];
	$observaciones = $row["observaciones"];
	$fecha = $row["fecha"];
	$justificacion = $row["justificacion"];
	$fecha_entrega = $row["fecha_entrega"];
	$forma_pago = $row["forma_pago"];
	$garantia_anticipo = $row["garantia_anticipo"];
	$lugar_entrega = $row["lugar_entrega"];
	$condiciones_entrega = $row["condiciones_entrega"];
	$otras_garantias = $row["otras_garantias"];
	$rebms_id = $row["rebms_id"];
	$depe_id = $row["depe_id"];
	$usua_login = $row["usua_login"];
	$esta_id = $row["esta_id"];
	$codigo_analisis_cotizacion = $row["codigo_analisis_cotizacion"];	
	$esta_nombre = $row["esta_nombre"];
	$esta_id = $row["esta_id"];
	$rebms_tipo_imputa = $row["rebms_tipo_imputa"];
	$rebms_imp_p_c = $row["rebms_imp_p_c"];
	$rebms_imp_esp = $row["rebms_imp_esp"];
	$rebms_tipo = $row["rebms_tipo"];
	$depe_nombre = $row["depe_nombre"];
	$prov_nombre = $row["prov_nombre"];
	$prov_domicilio = $row["prov_domicilio"];
	$prov_telefonos = $row["prov_telefonos"];
	$prov_fax = $row["prov_fax"];
	$prov_nombre_rl = $row["prov_nombre_rl"];
	$otras_condiciones = $row["otras_condiciones_observaciones"];
	
	$elaboradoPor = $row["elaborado_por"];
	
	$firmas = array();
	$firmas[] = PERFIL_COORDINADOR_COMPRAS;
	$firmas[] = PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS;
	$firmas[] = PERFIL_DIRECTOR_EJECUTIVO;
	$firmas[] = PERFIL_PRESIDENTE;
	$firmasSeleccionadas = SafiModeloFirma::GetFirmaByPerfiles($firmas);
	
	$centro_gestor = "";
	$centro_costo = "";
	if($rebms_tipo_imputa==TIPO_IMPUTACION_PROYECTO){//Proyecto
		$query = "SELECT paes_nombre,centro_gestor,centro_costo FROM sai_proy_a_esp WHERE proy_id = '".$rebms_imp_p_c."' AND paes_id = '".$rebms_imp_esp."'";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$centro_gestor = $row["centro_gestor"];
		$centro_costo = $row["centro_costo"];
	}else if($rebms_tipo_imputa==TIPO_IMPUTACION_ACCION_CENTRALIZADA){//Accion centralizada
		$query = "SELECT aces_nombre,centro_gestor,centro_costo FROM sai_acce_esp WHERE acce_id = '".$rebms_imp_p_c."' AND aces_id = '".$rebms_imp_esp."'";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$centro_gestor = $row["centro_gestor"];
		$centro_costo = $row["centro_costo"];
	}

	$contenido = "<style type='text/css'>
						.titulo{
							text-align:center;
							FONT-WEIGHT: bold; FONT-SIZE: 19px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none
						}
						.subTitulo{
							FONT-WEIGHT: bold; FONT-SIZE: 19px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none
						}
						.nombreCampo{
							vertical-align: middle;
							font-weight:bold;
						}
						.nombreCampoTitulo{
							font-weight:bold;
							
							vertical-align: middle;
							text-align:center;
							background-color: #CCCCCC;
							height: 25px;
						}
						.nombreCampoTituloSinFondo{
							font-weight:bold;
							
							vertical-align: middle;
							text-align:center;
							height: 25px;
						}
						.nombreCampoTituloPequeno{
							font-weight:bold;
							
							vertical-align: middle;
							text-align:center;
							background-color: #CCCCCC;
							height: 25px;
						}
						.bordeTabla{
							border: solid 1px #000000;
						}
						.textoTabla{
							FONT-WEIGHT: normal; FONT-SIZE: 19px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none
						}
						.textoPie{
							font-family: arial;
							font-size: 8pt;						
						}
						.alineadoAbajo{
							vertical-align: bottom;
						}
						.alineadoMedio{
							vertical-align: middle;
						}
						.alineadoCentro{
							text-align:center;
						}
						.alineadoDerecha{
							text-align:right;
						}
						.alturaMaxima{
							height: 60px;
						}
						.page-break{
							page-break-after: auto;
						}
					</style>";
	
	$contenido .="<table ".(($esta_id==$estadoAnulado)?"style='background:url(\"http://safi.infocentro.gob.ve/imagenes/anulado_safi_3.gif\") no-repeat repeat-y top center;'":"")." class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0'>";
	
	$contenido .=	"<tr>".
						"<td aling='center' colspan='4' class='nombreCampoTituloSinFondo'>Datos del proveedor</td>".
						"<td colspan='2' rowspan='2' class='alineadoMedio'>Fecha de entrega</td>".
						"<td colspan='2' rowspan='2 class='alineadoMedio alineadoCentro'>".$fecha_entrega."</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td rowspan='3' colspan='4'>".
							"<table>".
								"<tr>".
									"<td style='width: 200px;' class='nombreCampo'>Proveedor:</td>".
									"<td>".$prov_nombre."</td>".
								"</tr>".
								"<tr>".
									"<td class='nombreCampo'>C&oacute;digo del proveedor:</td>".
									"<td>RIF ".substr(trim($rif_proveedor_seleccionado),0,1)."-".substr(trim($rif_proveedor_seleccionado),1)."</td>".
								"</tr>".
								"<tr>".
									"<td class='nombreCampo'>Direcci&oacute;n:</td>".
									"<td>".$prov_domicilio."</td>".
								"</tr>".
								"<tr>".
									"<td class='nombreCampo'>Tel&eacute;fono:</td>".
									"<td>".$prov_telefonos."</td>".
								"</tr>".
								"<tr>".
									"<td class='nombreCampo'>Fax:</td>".
									"<td>".$prov_fax."</td>".
								"</tr>".
								"<tr>".
									"<td class='nombreCampo'>Nombre del representante legal:</td>".
									"<td>".$prov_nombre_rl."</td>".
								"</tr>".
							"</table>".
						"</td>".
	
						/*"<td colspan='2' class='alturaMaxima alineadoMedio'>Fecha de Entrega</td>".//
						"<td colspan='2' class='alineadoMedio alineadoCentro'>".$fecha_entrega."</td>".//*/
	
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td colspan='2' class='alturaMaxima alineadoMedio'>Forma de pago</td>".
						"<td colspan='2' class='alineadoMedio alineadoCentro'>".$forma_pago."</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td colspan='2' class='alturaMaxima alineadoMedio'>Garant&iacute;a de Anticipo</td>".
						"<td colspan='2' class='alineadoMedio alineadoCentro'>".$garantia_anticipo."</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td colspan='8' class='nombreCampoTitulo'>Justificaci&oacute;n</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td colspan='8' class='alineadoCentro'><div style='margin-top: 5px;margin-bottom: 5px;'>".$justificacion."</div></td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td class='nombreCampoTituloPequeno' style='width: 40.875%'>Conceptos</td>".
						"<td class='nombreCampoTituloPequeno' style='width: 6.5%'>Proy/Acc</td>".
						"<td class='nombreCampoTituloPequeno' style='width: 6.375%'>Acc/Esp.</td>".
						"<td class='nombreCampoTituloPequeno' style='width: 11.5%'>Partida</td>".
						"<td class='nombreCampoTituloPequeno' style='width: 6.375%'>Unid.</td>".
						"<td class='nombreCampoTituloPequeno' style='width: 6.375%'>Cant.</td>".
						"<td class='nombreCampoTituloPequeno' style='width: 11.5%'>Precio Unit.</td>".
						"<td class='nombreCampoTituloPequeno' style='width: 10.5%'>Total Bs.</td>".
					"</tr>";
	
	$query =	"SELECT ".
					"sci.id_item, ".
					"sci.numero_item, ".
					"sci.cantidad_cotizada, ".
					"sci.precio, ".
					"sci.unidad, ".
					"si.nombre AS nombre, ".
					"sp.part_id as id_partida, ".
					"sri.rbms_item_desc as descripcion, ".
					"sc.redondear ".
				"FROM ".
					"sai_cotizacion sc, ".
					"sai_cotizacion_item sci, ".
					"sai_rqui_items sri, ".
					"sai_item si, ".
					"sai_item_partida sip, ".
					"sai_partida sp ".
				"WHERE ".
					"sc.ordc_id = '".$idOrdc."' AND ".
					"sc.rif_proveedor = '".$rif_proveedor_seleccionado."' AND ".
					"sc.id_cotizacion = sci.id_cotizacion AND ".
					"sci.id_item = si.id AND ".
					"sci.id_item = sip.id_item AND ".
					"sip.part_id = sp.part_id AND ".
					"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." AND ".
					"sci.id_item = sri.rbms_item_arti_id AND ".
					"sci.numero_item = sri.numero_item AND ".
					"sri.rebms_id = '".$rebms_id."' ".
				"GROUP BY sci.id_item, sci.numero_item, sci.cantidad_cotizada, sci.precio, sci.unidad, si.nombre, sp.part_id, sp.part_nombre, sri.rbms_item_desc, sc.redondear ".
				//"ORDER BY sp.part_id,si.nombre";
				"ORDER BY sp.part_id, si.nombre, sci.numero_item";

	$resultado = pg_exec($conexion, $query);
	$totalArticulos = pg_numrows($resultado);
	
	$query =	"SELECT ".
					"scia.id_item, ".
					"scia.numero_item, ".
					"scia.cantidad_cotizada, ".
					"scia.precio, ".
					"scia.unidad, ".
					"si.nombre AS nombre, ".
					"sp.part_id as id_partida, ".
					"soci.especificaciones as descripcion, ".
					"sc.redondear ".
				"FROM ".
					"sai_cotizacion sc, ".
					"sai_cotizacion_item_adicional scia, ".
					"sai_orden_compra_item soci, ".
					"sai_item si, ".
					"sai_item_partida sip, ".
					"sai_partida sp ".
				"WHERE ".
					"sc.ordc_id = '".$idOrdc."' AND ".
					"sc.rif_proveedor = '".$rif_proveedor_seleccionado."' AND ".
					"sc.id_cotizacion = scia.id_cotizacion AND ".
					"scia.id_item = si.id AND ".
					"scia.id_item = sip.id_item AND ".
					"sip.part_id = sp.part_id AND ".
					"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." AND ".
					"scia.id_item = soci.id_item AND ".
					"scia.numero_item = soci.numero_item ".
				"GROUP BY scia.id_item, soci.numero_item, scia.numero_item, scia.cantidad_cotizada, scia.precio, scia.unidad, si.nombre, sp.part_id, sp.part_nombre, soci.especificaciones, sc.redondear ".
				//"ORDER BY sp.part_id, scia.numero_item";
				"ORDER BY sp.part_id, si.nombre, soci.numero_item";
	
	$resultadoArticulosAdicionales = pg_exec($conexion, $query);
	$totalArticulosAdicionales = pg_numrows($resultadoArticulosAdicionales);
	
	$montoTotalIva = 0;
	$montoTotal = 0;
	
	$partidaAnterior = "";
	$subtotalesPartidas = array();
	$redondearProveedores = null;
	for($i=0;$i<$totalArticulos;$i++){
		$row = pg_fetch_array($resultado, $i);
		$redondearProveedores = $row["redondear"];
		if(trim($partidaAnterior)!=trim($row["id_partida"])){
			$partidaAnterior = $row["id_partida"];
			$subtotalesPartidas[sizeof($subtotalesPartidas)]=array(trim($row["id_partida"]),0);
		}
		$contenido .=	"<tr>".
							"<td><div style='margin-top: 5px;margin-bottom: 5px;'>".$row["nombre"].". ".(($row["descripcion"]!="no")?$row["descripcion"]:"")."</div></td>".
							"<td class='alineadoMedio alineadoCentro'>".$centro_gestor."</td>".
							"<td class='alineadoMedio alineadoCentro'>".$centro_costo."</td>".
							"<td class='alineadoMedio alineadoCentro'>".$row["id_partida"]."</td>".
							"<td class='alineadoMedio alineadoCentro'>".(($row["unidad"]>1)?$row["unidad"]:"&nbsp;")."</td>".
							"<td class='alineadoMedio alineadoCentro'>".$row["cantidad_cotizada"]."</td>".		
							"<td class='alineadoMedio alineadoDerecha'>".$row["precio"]."</td>";
		if($redondearProveedores=="t"){
			$contenido .=	"<td class='alineadoMedio alineadoDerecha'>".round($row["cantidad_cotizada"]*$row["precio"]*$row["unidad"],2)."</td>".
						"</tr>";
			$subtotalesPartidas[sizeof($subtotalesPartidas)-1][1] += $row["cantidad_cotizada"]*$row["precio"]*$row["unidad"];
			$montoTotal += $row["cantidad_cotizada"]*$row["precio"]*$row["unidad"];
		}else{
			$textStr = ($row["cantidad_cotizada"]*$row["precio"]*$row["unidad"])+"";
			if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
				$textStr = substr($textStr,0,strpos($textStr, ".")+3);
			}
			$contenido .=	"<td class='alineadoMedio alineadoDerecha'>".$textStr."</td>".
						"</tr>";
			$subtotalesPartidas[sizeof($subtotalesPartidas)-1][1]+=(float)$textStr;
			$montoTotal+=(float)$textStr;
		}
	}
	
	$partidaAnterior = "";
	$indiceSubtotalPartida = -1;
	for($i=0;$i<$totalArticulosAdicionales;$i++){
		$row = pg_fetch_array($resultadoArticulosAdicionales, $i);
		if($redondearProveedores==null){
			$redondearProveedores = $row["redondear"];
		}
		if(trim($partidaAnterior)!=trim($row["id_partida"])){
			$partidaAnterior = $row["id_partida"];
			$existePartida = false;
			$k=0;
			while($k<sizeof($subtotalesPartidas)){
				if($subtotalesPartidas[$k][0]==trim($row["id_partida"])){
					$existePartida = true;
					break;
				}
				$k++;
			}
			if($existePartida==false){
				$indiceSubtotalPartida = sizeof($subtotalesPartidas);
				$subtotalesPartidas[$indiceSubtotalPartida]=array(trim($row["id_partida"]),0);			
			}else{
				$indiceSubtotalPartida = $k;
			}
		}
		$contenido .=	"<tr>".
							"<td><div style='margin-top: 5px;margin-bottom: 5px;'>".$row["nombre"].". ".(($row["descripcion"]!="no")?$row["descripcion"]:"")."</div></td>".
							"<td class='alineadoMedio alineadoCentro'>".$centro_gestor."</td>".
							"<td class='alineadoMedio alineadoCentro'>".$centro_costo."</td>".
							"<td class='alineadoMedio alineadoCentro'>".$row["id_partida"]."</td>".
							"<td class='alineadoMedio alineadoCentro'>".(($row["unidad"]>1)?$row["unidad"]:"&nbsp;")."</td>".
							"<td class='alineadoMedio alineadoCentro'>".$row["cantidad_cotizada"]."</td>".		
							"<td class='alineadoMedio alineadoDerecha'>".$row["precio"]."</td>";
		if($redondearProveedores=="t"){
			$contenido .=	"<td class='alineadoMedio alineadoDerecha'>".round($row["cantidad_cotizada"]*$row["precio"]*$row["unidad"],2)."</td>".
						"</tr>";
			$subtotalesPartidas[$indiceSubtotalPartida][1] += $row["cantidad_cotizada"]*$row["precio"]*$row["unidad"];
			$montoTotal += $row["cantidad_cotizada"]*$row["precio"]*$row["unidad"];
		}else{
			$textStr = ($row["cantidad_cotizada"]*$row["precio"]*$row["unidad"])+"";
			if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
				$textStr = substr($textStr,0,strpos($textStr, ".")+3);
			}
			$contenido .=	"<td class='alineadoMedio alineadoDerecha'>".$textStr."</td>".
						"</tr>";
			$subtotalesPartidas[$indiceSubtotalPartida][1]+=(float)$textStr;
			$montoTotal+=(float)$textStr;
		}
	}
	if($redondearProveedores==null){
		$redondearProveedores = "t";
	}
	$contenido .=	"<tr>".
						"<td colspan='4' class='nombreCampoTituloPequeno'>&nbsp;</td>".
						"<td class='nombreCampoTituloPequeno'>Base</td>".
						"<td colspan='3' class='nombreCampoTituloPequeno'>&nbsp;</td>".
					"</tr>";
	
	$partidaIva = "4.03.18.01.00";
	
	$query =	"SELECT ".
					"scb.iva, ".
					"scb.base ".
				"FROM ".
					"sai_cotizacion sc, ".
					"sai_cotizacion_base scb ".
				"WHERE ".
					"sc.ordc_id = '".$idOrdc."' AND ".
					"sc.rif_proveedor = '".$rif_proveedor_seleccionado."' AND ".
					"sc.id_cotizacion = scb.id_cotizacion ".
				"ORDER BY scb.iva ";
	$resultadoIvas = pg_exec($conexion, $query);
	$totalIvas = pg_numrows($resultadoIvas);

	for($i=0;$i<$totalIvas;$i++){
		$row = pg_fetch_array($resultadoIvas, $i);
		if($row["iva"]!="" && $row["iva"]!="0"){
			$contenido .=	"<tr>".
							"<td class='alineadoMedio alineadoDerecha'><div style='margin-top: 5px;margin-bottom: 5px;'>IVA del ".$row["iva"]."%</div></td>".
							"<td class='alineadoMedio alineadoCentro'>".$centro_gestor."</td>".
							"<td class='alineadoMedio alineadoCentro'>".$centro_costo."</td>".
							"<td class='alineadoMedio alineadoCentro'>".$partidaIva."</td>".
							"<td class='alineadoMedio alineadoDerecha'>".$row["base"]."</td>".
							"<td>&nbsp;</td>".
							"<td>&nbsp;</td>";
			if($redondearProveedores=="t"){
				$contenido .= "<td class='alineadoMedio alineadoDerecha'>".round($row["base"]*($row["iva"]/100),2)."</td>".
						"</tr>";
				$montoTotalIva += $row["base"]*($row["iva"]/100);
				$montoTotal += $row["base"]*($row["iva"]/100);
			}else{
				$textStr = ($row["base"]*($row["iva"]/100))+"";
				if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
					$textStr = substr($textStr,0,strpos($textStr, ".")+3);
				}
				$contenido .= "<td class='alineadoMedio alineadoDerecha'>".$textStr."</td>".
						"</tr>";
				$montoTotalIva += $row["base"]*($row["iva"]/100);
				$montoTotal += $row["base"]*($row["iva"]/100);
			}
		}
	}
	
	for($i=0;$i<sizeof($subtotalesPartidas);$i++){
		$contenido .=	"<tr>".
						"<td class='alineadoMedio alineadoDerecha'><div style='margin-top: 5px;margin-bottom: 5px;'>Sub-Total</div></td>".
						"<td class='alineadoMedio alineadoCentro'>".$centro_gestor."</td>".
						"<td class='alineadoMedio alineadoCentro'>".$centro_costo."</td>".
						"<td class='alineadoMedio alineadoCentro'>".$subtotalesPartidas[$i][0]."</td>".
						"<td class='alineadoMedio alineadoDerecha'>&nbsp;</td>".
						"<td>&nbsp;</td>".
						"<td>&nbsp;</td>";		
		if($redondearProveedores=="t"){
			$contenido .= "<td class='alineadoMedio alineadoDerecha'>".round($subtotalesPartidas[$i][1],2)."</td>".
						"</tr>";
		}else{
			$textStr = ($subtotalesPartidas[$i][1])+"";
			if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
				$textStr = substr($textStr,0,strpos($textStr, ".")+3);
			}
			$contenido .= "<td class='alineadoMedio alineadoDerecha'>".$textStr."</td>".
					"</tr>";
		}				
	}
	
	$contenido .=	"<tr>".
						"<td class='alineadoMedio alineadoDerecha'><div style='margin-top: 5px;margin-bottom: 5px;'>Total IVA</div></td>".
						"<td class='alineadoMedio alineadoCentro'>".$centro_gestor."</td>".
						"<td class='alineadoMedio alineadoCentro'>".$centro_costo."</td>".
						"<td class='alineadoMedio alineadoCentro'>".$partidaIva."</td>".
						"<td class='alineadoMedio alineadoDerecha'>&nbsp;</td>".
						"<td>&nbsp;</td>".
						"<td>&nbsp;</td>";
	
	if($redondearProveedores=="t"){
		$contenido .=	"<td class='alineadoMedio alineadoDerecha'>".round($montoTotalIva,2)."</td>".
					"</tr>";
	}else{
		$textStr = ($montoTotalIva)+"";
		if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
			$textStr = substr($textStr,0,strpos($textStr, ".")+3);
		}
		$contenido .=	"<td class='alineadoMedio alineadoDerecha'>".$textStr."</td>".
					"</tr>";
	}
	
	$contenido .=	"<tr>".
						"<td colspan='7' class='alineadoMedio'><div style='margin-top: 5px;margin-bottom: 5px;margin-left: 350px;'>Total <!--<sub>(24):</sub>--></div></td>";
	
	if($redondearProveedores=="t"){
		$contenido .=	"<td class='alineadoMedio alineadoDerecha'>".round($montoTotal,2)."</td>".
					"</tr>";
	}else{
		$textStr = ($montoTotal)+"";
		if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
			$textStr = substr($textStr,0,strpos($textStr, ".")+3);
		}
		$contenido .=	"<td class='alineadoMedio alineadoDerecha'>".$textStr."</td>".
					"</tr>";
	}
	
	$contenido .=	"<tr>".
						"<td colspan='8' class='nombreCampoTitulo'>Otras condiciones / Observaciones <!--<sub>(25)</sub>--></td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td colspan='8' class='alineadoMedio alineadoCentro'><div style='margin-top: 5px;margin-bottom: 5px;'>".$otras_condiciones."&nbsp;</div></td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td class='nombreCampo'><div style='margin-top: 5px;margin-bottom: 5px;'>Lugar de entrega <!--<sub>(26):</sub>--></div></td>".
						"<td colspan='7' class='alineadoMedio alineadoCentro'>".$lugar_entrega."</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td class='nombreCampo'><div style='margin-top: 5px;margin-bottom: 5px;'>Condiciones de entrega <!--<sub>(27):</sub>--></div></td>".
						"<td colspan='7' class='alineadoMedio alineadoCentro'>".$condiciones_entrega."</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td class='nombreCampo'><div style='margin-top: 5px;margin-bottom: 5px;'>Otras garant&iacute;as <!--<sub>(28):</sub>--></div></td>".
						"<td colspan='7' class='alineadoMedio alineadoCentro'>".$otras_garantias."</td>".
					"</tr>";
	
	/*if($tipo=="F"){
		$contenido .="</table>";
		
		$firma =		"<tr>".
							"<td colspan='2' class='nombreCampoTituloPequeno'>Analista de compras</td>".
							"<td width='180px' class='nombreCampoTituloPequeno' colspan='3'>Coordinador de compras</td>".
							"<td width='180px' class='nombreCampoTituloPequeno' colspan='3'>Director de la Oficina de Gestión <br/>Administrativa y Financiera</td>".
						"</tr>";
		$firma .=		"<tr>".
							"<td colspan='2' class='alineadoMedio alineadoCentro'><p><br/></p><div style='text-align: center'>".utf8_encode($elaboradoPor)."</div></td>".
							"<td colspan='3' class='alineadoMedio alineadoCentro'><p><br/></p><div style='text-align: center'>".utf8_encode($revisadoPor)."</div></td>".
							"<td colspan='3' class='alineadoMedio alineadoCentro'><p><br/></p><div style='text-align: center'>".utf8_encode($directorAdministrativo)."</div></td>".
						"</tr>";		
		$firma .=		"<tr>".
							"<td colspan='5' class='nombreCampoTitulo'>Firmas autorizadas</td>".
							"<td colspan='3' class='nombreCampoTitulo'>Aceptación del proveedor</td>".
						"</tr>";		
		$firma .=		"<tr>".
							"<td colspan='2' class='nombreCampoTituloPequeno'>Director Ejecutivo</td>".
							"<td colspan='3' class='nombreCampoTituloPequeno'>Presidente</td>".
							"<td colspan='3' class='nombreCampoTituloPequeno'>Empresa <sub>(34):</sub></td>".
						"</tr>";		
		$firma .=		"<tr>".
							"<td colspan='2' class='alineadoMedio alineadoCentro'><p><br/></p><div style='text-align: center'>".utf8_encode($directorEjecutivo)."</div></td>".
							"<td colspan='3' class='alineadoMedio alineadoCentro'><p><br/></p><div style='text-align: center'>".utf8_encode($presidente)."</div></td>".
							"<td colspan='3' class='alineadoMedio alineadoDerecha'>".
								"<table>".
									"<tr>".
										"<td style='height:12px'>Nombre <sub>(35):</sub></td>".
									"</tr>".
									"<tr>".
										"<td style='height:12px'>C.I. <sub>(36):</sub></td>".
									"</tr>".
									"<tr>".
										"<td style='height:12px'>Firma <sub>(37):</sub></td>".
									"</tr>".
									"<tr>".
										"<td></td>".
									"</tr>".
								"</table>".
							"</td>".
						"</tr>";		
		$firma .=		"<tr>".
							"<td colspan='8' class='textoPie'>".
								"<p>IMPORTANTE: La entrega de suministros y facturas se realiza en horario de oficina: Mañana: 9:00 a.m a 11:30 a.m Tarde: 2:00: p.m a 4:00 p.m.</p>".
								"<p align='center'>Oficina Principal: Av. Universidad, Esquina el Chorro, Torre Ministerial, Piso 11, La Hoyada, Caracas<br/>Teléfono: 0212-7718520/7718672 Fax: 0212-7718672</p>".
							"</td>".
						"</tr>";
		$header = "<style type='text/css'>
						@page {
					 		margin-top: 20mm;
					 		@bottom-right {
					    		content: 'Página ' counter(page) ' de ' counter(pages);
					  		}
						}
					</style>".
					"<img width='1000px' src='http://safi.infocentro.gob.ve/imagenes/encabezado.jpg'/>";
		$footer = 	"<table width='100%' class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0' style='font-size: 12pt;'>".
						$firma.
					"</table>".
					"<style type='text/css'>
						@page {
					 		@bottom-right {
					 			margin-top: 108mm;
					    		content: 'Página ' counter(page) ' de ' counter(pages);
					  		}
						}
					</style>".
					"<span style='align=center;font-family: arial;font-style:italic;font-weight:bold;font-size: 10pt;'>SAFI - Fundación Infocentro</span><br/>".
					"<span style='align=center;font-family: arial;font-size: 10pt;'>".fecha()."</span>";
		$properties = array("marginBottom" => 95, "headerHtml" => $header, "footerHtml" => $footer);
		convert_to_pdf($contenido, $properties);
	}else if($tipo=="L"){*/
		$contenido .="</table>";
		$firma =	"<span class='page-break'></span>".
					"<table width='100%' class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0' style='font-size: 12pt;'>".
						"<tr>".
							"<td colspan='4' width='180px' class='nombreCampoTituloPequeno'>".$firmasSeleccionadas[PERFIL_COORDINADOR_COMPRAS]['nombre_cargo_dependencia']."</td>".
							"<td colspan='4' width='180px' class='nombreCampoTituloPequeno'>".$firmasSeleccionadas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_cargo_dependencia']."</td>".
						"</tr>".
						"<tr>".
							"<td colspan='4' class='alineadoMedio alineadoCentro'><p><br/></p><div style='text-align: center'>".$firmasSeleccionadas[PERFIL_COORDINADOR_COMPRAS]['nombre_empleado']."</div></td>".
							"<td colspan='4' class='alineadoMedio alineadoCentro'><p><br/></p><div style='text-align: center'>".$firmasSeleccionadas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_empleado']."</div></td>".
						"</tr>".
						"<tr>".
							"<td colspan='5' class='nombreCampoTitulo'>Firmas autorizadas</td>".
							"<td colspan='3' class='nombreCampoTitulo'>Aceptaci&oacute;n del proveedor</td>".
						"</tr>".
						"<tr>".
							"<td colspan='2' class='nombreCampoTituloPequeno'>".$firmasSeleccionadas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_cargo_dependencia']."</td>".
							"<td colspan='3' class='nombreCampoTituloPequeno'>".$firmasSeleccionadas[PERFIL_PRESIDENTE]['nombre_cargo_dependencia']."</td>".
							"<td colspan='3' class='nombreCampoTituloPequeno'>Empresa <sub>(34):</sub></td>".
						"</tr>".
						"<tr>".
							"<td colspan='2' class='alineadoMedio alineadoCentro'><p><br/></p><div style='text-align: center'>".$firmasSeleccionadas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_empleado']."</div></td>".
							"<td colspan='3' class='alineadoMedio alineadoCentro'><p><br/></p><div style='text-align: center'>".$firmasSeleccionadas[PERFIL_PRESIDENTE]['nombre_empleado']."</div></td>".
							"<td colspan='3' class='alineadoMedio alineadoDerecha'>".
								"<table>".
									"<tr>".
										"<td style='height:12px'>Nombre <sub>(35):</sub></td>".
									"</tr>".
									"<tr>".
										"<td style='height:12px'>C.I. <sub>(36):</sub></td>".
									"</tr>".
									"<tr>".
										"<td style='height:12px'>Firma <sub>(37):</sub></td>".
									"</tr>".
									"<tr>".
										"<td></td>".
									"</tr>".
								"</table>".
							"</td>".
						"</tr>".
						"<tr>".
							"<td colspan='8' class='textoPie'>".
								"<p style='margin-top: 30px;margin-bottom: 10px;'>Elaborado por: _________________________________<br/>".
								"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$elaboradoPor."</p>".
								"<p>IMPORTANTE: La entrega de suministros y facturas se realiza en horario de oficina: Ma&ntilde;ana: 9:00 a.m Tarde: 2:00: p.m a 4:00 p.m.</p>".
								"<p align='center'>Oficina Principal: Av. Universidad, Esquina el Chorro, Torre Ministerial, Piso 11, La Hoyada, Caracas<br/>Tel&eacute;fono: 0212-7718520/7718672 Fax: 0212-7718672</p>".
							"</td>".
						"</tr>".
					"</table>";
		$contenido .= $firma;
		$header = 	"<img width='1000px' src='http://safi.infocentro.gob.ve/imagenes/encabezado.jpg'/>";
		if($rebms_tipo==TIPO_REQUISICION_COMPRA){
			$header .=	"<br/><br/><table width='100%' style='font-size: 17pt;'><tr><td align='right' valign='top' width='68%'><b>Orden de Compra ".$idOrdc." Nro ".$codigo_analisis_cotizacion."<p style='margin-right: 13%;margin-top: -3px;'>RIF. No. G-20007728-0</p></b></td><td align='right' valign='top'>Fecha: ".$fecha."</td></tr></table>";
		}else if($rebms_tipo==TIPO_REQUISICION_SERVICIO){
			$header .=	"<br/><br/><table width='100%' style='font-size: 17pt;'><tr><td align='right' valign='top' width='68%'><b>Orden de Servicio ".$idOrdc." Nro ".$codigo_analisis_cotizacion."<p style='margin-right: 13%;margin-top: -3px;'>RIF. No. G-20007728-0</p></b></td><td align='right' valign='top'>Fecha: ".$fecha."</td></tr></table>";
		}
		$header .=	"<style type='text/css'>
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
	/*}*/
}
pg_close($conexion);
?>
