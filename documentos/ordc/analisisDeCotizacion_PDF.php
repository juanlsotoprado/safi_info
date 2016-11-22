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

$tipo=$_REQUEST['tipo'];

$idOrdc = "";
if (isset($_GET['idOrdc']) && $_GET['idOrdc'] != "") {
	$idOrdc = $_GET['idOrdc'];
}

if($idOrdc && $idOrdc!=""){
	$estadoAnulado = "15";
	$query = 	"SELECT ".
					"soc.rif_proveedor_seleccionado, ".
					"soc.criterio_seleccion, ".
					"UPPER(soc.observaciones) as observaciones, ".
					"to_char(soc.fecha,'DD-MM-YY') as fecha, ".
					"to_char(soc.fecha,'HH12:MI AM') as hora, ".
					"UPPER(soc.justificacion) as justificacion, ".
					"UPPER(soc.fecha_entrega) as fecha_entrega, ".
					"UPPER(soc.forma_pago) as forma_pago, ".
					"UPPER(soc.garantia_anticipo) as garantia_anticipo, ".
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
					"UPPER(sp.prov_nombre) as prov_nombre, ".
					"sp.prov_domicilio, ".
					"sp.prov_telefonos, ".
					"sp.prov_fax, ".
					"sp.prov_nombre_rl, ".
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
	$hora = $row["hora"];
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
							font-size: 14pt;
							font-weight:bold;
						}
						.subTitulo{
							text-align:center;
							font-size: 11pt;
							font-weight:normal;
						}
						.nombreCampo{
							vertical-align: middle;
							font-weight:bold;
						}
						.nombreCampoTitulo{
							font-family: arial;
							font-size: 10pt;
							vertical-align: middle;
							text-align:center;
							background-color: #CCCCCC;
							height: 25px;
						}
						.nombreCampoTituloSinFondo{
							font-family: arial;
							font-size: 10pt;
							vertical-align: middle;
							text-align:center;
							height: 25px;
						}
						.nombreCampoTituloPequeno{
							font-family: arial;
							vertical-align: middle;
							text-align:center;
							background-color: #CCCCCC;
							height: 25px;
						}
						.bordeTabla{
							border: solid 1px #000000;
						}
						.textoTabla{
							font-family: arial;
							font-size: 9pt;						
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
							height: 40px;
						}
					</style>";
	
	$contenido .="<p class='titulo'>AN&Aacute;LISIS DE COTIZACI&Oacute;N N&deg; ".$codigo_analisis_cotizacion."</p>";

	$contenido .="Fecha: ".$fecha."<br/>Hora: ".$hora;
	
	$contenido .="<table width='100%'><tr><td align='right'>";
	
	$contenido .="<table style='margin-right:-2px;border: solid 2px #000000;border-bottom: none;' class='bordeTabla textoTabla' cellspacing='0' cellpadding='0'>";
	
	$query = 	"SELECT ".
					"to_char(sc.fecha,'DD/MM/YYYY') as fecha, ".
					"sc.id_cotizacion, ".
					"sp.prov_id_rif, ".
					"UPPER(sp.prov_nombre) as prov_nombre, ".
					"sc.redondear ".
				"FROM sai_cotizacion sc, sai_proveedor_nuevo sp ".
				"WHERE ".
					"sc.ordc_id = '".$idOrdc."' AND ".
					"sc.rif_proveedor = sp.prov_id_rif ".
				"ORDER BY sp.prov_nombre";
	$resultadoProveedores = pg_exec($conexion, $query);
	$totalProveedores = pg_numrows($resultadoProveedores);
	$minimoCotizaciones = 3;
	if($totalProveedores<$minimoCotizaciones){
		$diferenciaCotizaciones = $minimoCotizaciones - $totalProveedores;
	}else{
		$diferenciaCotizaciones = 0; 
	}
	$totalProveedores += $diferenciaCotizaciones;
	$contenido .=	"<tr>";
	$redondearProveedores = array();
	if($totalProveedores>1){
		$i=0;
		while($i<$totalProveedores){
			switch($i){
				case 0:
					$nroCotizacion = "PRIMERA";
					break;
				case 1:
					$nroCotizacion = "SEGUNDA";
					break;
				case 2:
					$nroCotizacion = "TERCERA";
					break;
				case 3:
					$nroCotizacion = "CUARTA";
					break;
				case 4:
					$nroCotizacion = "QUINTA";
					break;
				case 5:
					$nroCotizacion = "SEXTA";
					break;
				case 6:
					$nroCotizacion = "SEPTIMA";
					break;
				case 7:
					$nroCotizacion = "OCTAVA";
					break;
				case 8:
					$nroCotizacion = "NOVENA";
					break;
				case 9:
					$nroCotizacion = "DECIMA";
					break;
				default:
					$nroCotizacion = "";
					break;
			}
			if($i==0){
				$contenido .= "<td align='center' width='197px' colspan='2' style='border-right: solid 2px #000000;'>".$nroCotizacion." COTIZACI&Oacute;N</td>";
			}else if($i+1==$totalProveedores){
				$contenido .= "<td align='center' width='197px' colspan='2'>".$nroCotizacion." COTIZACI&Oacute;N</td>";
			}else{
				$contenido .= "<td align='center' width='198px' colspan='2' style='border-right: solid 2px #000000;'>".$nroCotizacion." COTIZACI&Oacute;N</td>";
			}
			$i++;
		}
	}else{
		$contenido .= "<td align='center' width='196px' colspan='2'>PRIMERA COTIZACION</td>";//CUANDO SOLO HAY 1 COTIZACION EL width DEBE SER 196px, SI HAY MAS DE 1 DEBE SER 197px
	}
	
	$contenido .=	"</tr>";
	
	$contenido .="</table>";
	
	$contenido .="<table style='margin-right:-2px; border: solid 2px #000000;border-bottom: none;' class='bordeTabla textoTabla' cellspacing='0' cellpadding='0'>";
	
	$contenido .=	"<tr>".
						"<td align='center' width='100px' style='border-right: solid 2px #000000;border-bottom: solid 2px #000000;'>EMPRESA:</td>";
	if($totalProveedores>1){
		$i=0;
		while($i<$totalProveedores){
			if($i<$totalProveedores-$diferenciaCotizaciones){
				$row = pg_fetch_array($resultadoProveedores, $i);
				$nombreProveedor = $row["prov_nombre"];
				$redondearProveedores[$i] = $row["redondear"];
			}else{
				$nombreProveedor = "";
			}
			if($i==0){
				$contenido .= "<td align='center' width='197px' style='border-right: solid 2px #000000;border-bottom: solid 2px #000000;' colspan='2'>".$nombreProveedor."</td>";
			}else if($i+1==$totalProveedores){
				$contenido .= "<td align='center' width='197px' colspan='2' style='border-bottom: solid 2px #000000;'>".$nombreProveedor."</td>";
			}else{
				$contenido .= "<td align='center' width='198px' style='border-right: solid 2px #000000;border-bottom: solid 2px #000000;' colspan='2'>".$nombreProveedor."</td>";
			}
			$i++;
		}
	}else{
		$row = pg_fetch_array($resultadoProveedores, 0);
		$redondearProveedores[0] = $row["redondear"];
		$contenido .= "<td align='center' width='196px' colspan='2' style='border-bottom: solid 2px #000000;'>".$row["prov_nombre"]."</td>";//CUANDO SOLO HAY 1 COTIZACION EL width DEBE SER 196px, SI HAY MAS DE 1 DEBE SER 197px
	}
	$contenido .=	"</tr>";
	
	$contenido .=	"<tr>".
						"<td align='center' width='100px' style='border-right: solid 2px #000000;border-bottom: solid 2px #000000;'>RIF</td>";
	$i=0;
	while($i<$totalProveedores){
		if($i<$totalProveedores-$diferenciaCotizaciones){
			$row = pg_fetch_array($resultadoProveedores, $i);
			$rifProveedor = strtoupper(substr(trim($row["prov_id_rif"]),0,1))."-".substr(trim($row["prov_id_rif"]),1);
		}else{
			$rifProveedor = "";
		}
		if($i+1==$totalProveedores){
			$contenido .= "<td align='center' colspan='2' style='border-bottom: solid 2px #000000;'>".$rifProveedor."</td>";
		}else{
			$contenido .= "<td align='center' style='border-right: solid 2px #000000;border-bottom: solid 2px #000000;' colspan='2'>".$rifProveedor."</td>";
		}
		$i++;
	}
	$contenido .=	"</tr>";
	
	$contenido .=	"<tr>".
						"<td align='center' style='border-right: solid 2px #000000;'>FECHA DE COT.:</td>";
	$i=0;
	while($i<$totalProveedores){
		if($i<$totalProveedores-$diferenciaCotizaciones){
			$row = pg_fetch_array($resultadoProveedores, $i);
			$fechaProveedor = $row["fecha"];
		}else{
			$fechaProveedor = "";
		}
		if($i+1==$totalProveedores){
			$contenido .= "<td align='center' colspan='2'>".$fechaProveedor."</td>";
		}else{
			$contenido .= "<td align='center' style='border-right: solid 2px #000000;' colspan='2'>".$fechaProveedor."</td>";
		}
		$i++;
	}
	$contenido .=	"</tr>";
	
	$contenido .="</table>";
	
	$contenido .="<table style='border-bottom: none;".(($esta_id==$estadoAnulado)?"background:url(\"http://safi.infocentro.gob.ve/imagenes/anulado_safi_5.gif\") no-repeat repeat-y top center;":"")."' width='100%' class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0'>";
	
	$contenido .=	"<tr>".
						"<td align='center'>DESCRIPCI&Oacute;N</td>".
						"<td align='center' width='100px'>CANTIDAD</td>";
	$i=0;
	while($i<$totalProveedores){
		$contenido .= 	"<td align='center' width='100px'>PRECIO</td>".
						"<td align='center' width='100px'>TOTAL BsF.</td>";	
		$i++;
	}
	$contenido .=	"</tr>";
	
	$query =	"SELECT ".
					"sc.rif_proveedor, ".
					"scb.iva, ".
					"scb.base ".
				"FROM ".
					"sai_cotizacion sc, ".
					"sai_cotizacion_base scb, ".
					"sai_proveedor_nuevo spr ".
				"WHERE ".
					"sc.ordc_id = '".$idOrdc."' AND ".
					"sc.rif_proveedor = spr.prov_id_rif AND ".
					"sc.id_cotizacion = scb.id_cotizacion ".
				"GROUP BY sc.rif_proveedor, spr.prov_nombre, scb.iva, scb.base ".
				"ORDER BY spr.prov_nombre, scb.iva ";
	$resultadoBases = pg_exec($conexion, $query);
	$totalBases = pg_numrows($resultadoBases);
	
	$basesCotizaciones = array();
	$proveedorAnterior = "";
	for($i=0;$i<$totalBases;$i++){
		$row = pg_fetch_array($resultadoBases, $i);
		if($row["rif_proveedor"]!=$proveedorAnterior){
			$proveedorAnterior = $row["rif_proveedor"];
			$basesCotizaciones[sizeof($basesCotizaciones)]=0;
		}
		$basesCotizaciones[sizeof($basesCotizaciones)-1] += $row["base"]*($row["iva"]/100);
	}
	
	//De la requisicion
	$query =	"SELECT ".
					"sci.id_item as id, ".
					"sci.numero_item, ".
					"si.nombre, ".
					"sri.rbms_item_desc as descripcion ".
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
				"GROUP BY sci.id_item, sci.numero_item, si.nombre, sri.rbms_item_desc ".
				//"ORDER BY si.nombre";
				"ORDER BY si.nombre, sci.numero_item";
	
	$resultadoArticulos = pg_exec($conexion, $query);
	$totalArticulos = pg_numrows($resultadoArticulos);
	
	$cadenaArticulos = "(";
	$i=0;
	while($i<$totalArticulos){
		$row = pg_fetch_array($resultadoArticulos, $i);
		$cadenaArticulos .= $row["numero_item"].",";
		$i++;
	}
	$cadenaArticulos = substr($cadenaArticulos, 0, -1).")";
	
	$query =	"SELECT ".
					"sc.rif_proveedor, ".
					"sci.id_item as id, ".
					"sci.cantidad_cotizada, ".
					"sci.precio, ".
					"sci.unidad, ".
					"spr.prov_nombre, ".
					"si.nombre, ".
					"sci.numero_item ".
				"FROM ".
					"sai_cotizacion sc, ".
					"sai_cotizacion_item sci, ".
					"sai_item si, ".
					"sai_item_partida sip, ".
					"sai_partida sp, ".
					"sai_proveedor_nuevo spr ".
				"WHERE ".
					"sc.ordc_id = '".$idOrdc."' AND ".
					"sc.rif_proveedor = spr.prov_id_rif AND ".
					"sc.id_cotizacion = sci.id_cotizacion AND ".
					"sci.numero_item IN ".$cadenaArticulos." AND ".
					"sci.id_item = si.id AND ".
					"sci.id_item = sip.id_item AND ".
					"sip.part_id = sp.part_id AND ".
					"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ".
				"GROUP BY spr.prov_nombre, si.nombre, sc.rif_proveedor, sci.id_item, sci.cantidad_cotizada, sci.precio, sci.unidad, sci.numero_item ".
				//"ORDER BY spr.prov_nombre, si.nombre";
				"ORDER BY spr.prov_nombre, si.nombre, sci.numero_item";
	
	$resultadoCotizaciones = pg_exec($conexion, $query);
	$totalArticulosCotizaciones = pg_numrows($resultadoCotizaciones);
	
	//De la orden de compra
	$query =	"SELECT ".
					"soci.id_item as id, ".
					"soci.numero_item, ".
					"si.nombre, ".
					"soci.especificaciones as descripcion ".
				"FROM ".
					"sai_orden_compra_item soci, ".
					"sai_item si, ".
					"sai_item_partida sip, ".
					"sai_partida sp ".
				"WHERE ".
					"soci.ordc_id = '".$idOrdc."' AND ".
					"soci.id_item = si.id AND ".
					"soci.id_item = sip.id_item AND ".
					"sip.part_id = sp.part_id AND ".
					"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ".
				"GROUP BY soci.id_item, soci.numero_item, si.nombre, soci.especificaciones ".
				"ORDER BY si.nombre, soci.numero_item";
	
	$resultadoArticulosAdicionales = pg_exec($conexion, $query);
	$totalArticulosAdicionales = pg_numrows($resultadoArticulosAdicionales);
	if($totalArticulosAdicionales>0){
		$cadenaArticulosAdicionales = "(";
		$i=0;
		while($i<$totalArticulosAdicionales){
			$row = pg_fetch_array($resultadoArticulosAdicionales, $i);
			$cadenaArticulosAdicionales .= $row["numero_item"].",";
			$i++;
		}
		$cadenaArticulosAdicionales = substr($cadenaArticulosAdicionales, 0, -1).")";
		
		$query =	"SELECT ".
						"sc.rif_proveedor, ".
						"scia.id_item as id, ".
						"scia.cantidad_cotizada, ".
						"scia.precio, ".
						"scia.unidad, ".
						"spr.prov_nombre, ".
						"scia.numero_item ".
					"FROM ".
						"sai_cotizacion sc, ".
						"sai_cotizacion_item_adicional scia, ".
						"sai_item si, ".
						"sai_item_partida sip, ".
						"sai_partida sp, ".
						"sai_proveedor_nuevo spr ".
					"WHERE ".
						"sc.ordc_id = '".$idOrdc."' AND ".
						"sc.rif_proveedor = spr.prov_id_rif AND ".
						"sc.id_cotizacion = scia.id_cotizacion AND ".
						"scia.numero_item IN ".$cadenaArticulosAdicionales." AND ".
						"scia.id_item = si.id AND ".
						"scia.id_item = sip.id_item AND ".
						"sip.part_id = sp.part_id AND ".
						"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ".
					"GROUP BY spr.prov_nombre, si.nombre, sc.rif_proveedor, scia.id_item, scia.cantidad_cotizada, scia.precio, scia.unidad, scia.numero_item ".
					"ORDER BY spr.prov_nombre, si.nombre, scia.numero_item";
		$resultadoCotizacionesAdicionales = pg_exec($conexion, $query);
		$totalArticulosCotizacionesAdicionales = pg_numrows($resultadoCotizacionesAdicionales);
	
	}else{
		$totalArticulosCotizacionesAdicionales = 0;
	}
	
	$cotizaciones = array();
	$indicesCotizaciones = array();
	$subtotalesCotizaciones = array();
	$totalesCotizaciones = array();

	$proveedorAnterior = "";
	$indiceProveedorSeleccionado = "";
	for($i=0;$i<$totalArticulosCotizaciones;$i++){
		$row = pg_fetch_array($resultadoCotizaciones, $i);
		if($proveedorAnterior!=$row["rif_proveedor"]){
			if($proveedorAnterior != ""){
				$banderaAdicionales = 0;
				for($j=0;$j<$totalArticulosCotizacionesAdicionales;$j++){
					$rowAdicionales = pg_fetch_array($resultadoCotizacionesAdicionales, $j);
					if($proveedorAnterior==$rowAdicionales["rif_proveedor"]){
						$banderaAdicionales = 1;
						$articuloDetalle = array();
						$articuloDetalle[0] = $rowAdicionales["id"];
						$articuloDetalle[1] = $rowAdicionales["cantidad_cotizada"];
						$articuloDetalle[2] = $rowAdicionales["precio"];
						$articuloDetalle[3] = $rowAdicionales["unidad"];
						$articuloDetalle[4] = $rowAdicionales["numero_item"];
						$cotizaciones[sizeof($cotizaciones)-1][sizeof($cotizaciones[sizeof($cotizaciones)-1])]=$articuloDetalle;
					}else if($banderaAdicionales==1){
						$banderaAdicionales=2;
					}
					if($banderaAdicionales==2){
						break;
					}
				}
			}			
			$proveedorAnterior = $row["rif_proveedor"];
			$cotizaciones[sizeof($cotizaciones)]=array();
			$indicesCotizaciones[sizeof($indicesCotizaciones)]=0;
			$subtotalesCotizaciones[sizeof($subtotalesCotizaciones)]=0;
			$totalesCotizaciones[sizeof($totalesCotizaciones)]=0;
			
			if($row["rif_proveedor"]==$rif_proveedor_seleccionado){
				$indiceProveedorSeleccionado = sizeof($cotizaciones)-1;
			}
		}
		$articuloDetalle = array();
		$articuloDetalle[0] = $row["id"];
		$articuloDetalle[1] = $row["cantidad_cotizada"];
		$articuloDetalle[2] = $row["precio"];
		$articuloDetalle[3] = $row["unidad"];
		$articuloDetalle[4] = $row["numero_item"];
		$cotizaciones[sizeof($cotizaciones)-1][sizeof($cotizaciones[sizeof($cotizaciones)-1])]=$articuloDetalle;
	}
	if($proveedorAnterior != ""){
		$banderaAdicionales = 0;
		for($j=0;$j<$totalArticulosCotizacionesAdicionales;$j++){
			$rowAdicionales = pg_fetch_array($resultadoCotizacionesAdicionales, $j);
			if($proveedorAnterior==$rowAdicionales["rif_proveedor"]){
				$banderaAdicionales = 1;
				$articuloDetalle = array();
				$articuloDetalle[0] = $rowAdicionales["id"];
				$articuloDetalle[1] = $rowAdicionales["cantidad_cotizada"];
				$articuloDetalle[2] = $rowAdicionales["precio"];
				$articuloDetalle[3] = $rowAdicionales["unidad"];
				$articuloDetalle[4] = $rowAdicionales["numero_item"];
				$cotizaciones[sizeof($cotizaciones)-1][sizeof($cotizaciones[sizeof($cotizaciones)-1])]=$articuloDetalle;
			}else if($banderaAdicionales==1){
				$banderaAdicionales=2;
			}
			if($banderaAdicionales==2){
				break;
			}
		}
	}else{
		for($i=0;$i<$totalArticulosCotizacionesAdicionales;$i++){
			$rowAdicionales = pg_fetch_array($resultadoCotizacionesAdicionales, $i);
			if($proveedorAnterior!=$rowAdicionales["rif_proveedor"]){
				$proveedorAnterior = $rowAdicionales["rif_proveedor"];
				$cotizaciones[sizeof($cotizaciones)]=array();
				$indicesCotizaciones[sizeof($indicesCotizaciones)]=0;
				$subtotalesCotizaciones[sizeof($subtotalesCotizaciones)]=0;
				$totalesCotizaciones[sizeof($totalesCotizaciones)]=0;
				
				if($rowAdicionales["rif_proveedor"]==$rif_proveedor_seleccionado){
					$indiceProveedorSeleccionado = sizeof($cotizaciones)-1;
				}
			}
			$articuloDetalle = array();
			$articuloDetalle[0] = $row["id"];
			$articuloDetalle[1] = $rowAdicionales["cantidad_cotizada"];
			$articuloDetalle[2] = $rowAdicionales["precio"];
			$articuloDetalle[3] = $rowAdicionales["unidad"];
			$articuloDetalle[4] = $rowAdicionales["numero_item"];
			$cotizaciones[sizeof($cotizaciones)-1][sizeof($cotizaciones[sizeof($cotizaciones)-1])]=$articuloDetalle;
		}
		
		/*for($j=0;$j<$totalArticulosCotizacionesAdicionales;$j++){
			$rowAdicionales = pg_fetch_array($resultadoCotizacionesAdicionales, $j);
			$articuloDetalle = array();
			$articuloDetalle[0] = $rowAdicionales["id"];
			$articuloDetalle[1] = $rowAdicionales["cantidad_cotizada"];
			$articuloDetalle[2] = $rowAdicionales["precio"];
			$articuloDetalle[3] = $rowAdicionales["unidad"];
			$articuloDetalle[4] = $rowAdicionales["numero_item"];
			$cotizaciones[sizeof($cotizaciones)-1][sizeof($cotizaciones[sizeof($cotizaciones)-1])]=$articuloDetalle;
		}*/
	}
	
	for($i=0;$i<$totalArticulos;$i++){
		$row = pg_fetch_array($resultadoArticulos, $i);
		$contenido .=	"<tr>".
							"<td>".$row["nombre"].". ".(($row["descripcion"]!="no")?$row["descripcion"]:"")."</td>".
							"<td align='center'>".$cotizaciones[$indiceProveedorSeleccionado][$i][1]*$cotizaciones[$indiceProveedorSeleccionado][$i][3]."</td>";
		
		for($j=0;$j<sizeof($cotizaciones);$j++){
			if($row["numero_item"]==$cotizaciones[$j][$indicesCotizaciones[$j]][4]){
				$contenido .=	"<td align='center'>".$cotizaciones[$j][$indicesCotizaciones[$j]][2]."</td>";
				if($redondearProveedores[$j]=="t"){
					$contenido .=	"<td align='center'>".round(($cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3]),2)."</td>";
					$subtotalesCotizaciones[$j]+=$cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3];
					$totalesCotizaciones[$j]+=$cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3];
				}else{
					$textStr = ($cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3])+"";
					if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
						$textStr = substr($textStr,0,strpos($textStr, ".")+3);
					}
					$contenido .=	"<td align='center'>".$textStr."</td>";
					$subtotalesCotizaciones[$j]+=(float)$textStr;
					$totalesCotizaciones[$j]+=(float)$textStr;
				}
				$indicesCotizaciones[$j]++;
			}else{
				$contenido .=	"<td align='center'>&nbsp;</td>".
								"<td align='center'>&nbsp;</td>";										
			}
		}
		while($j<$totalProveedores){
			$contenido .=	"<td align='center'>&nbsp;</td>".
							"<td align='center'>&nbsp;</td>";
			$j++;
		}
		$contenido .= "</tr>";
	}
	$indiceCantidad = $totalArticulos;
	for($i=0;$i<$totalArticulosAdicionales;$i++){
		$row = pg_fetch_array($resultadoArticulosAdicionales, $i);
		$contenido .=	"<tr>".
							"<td>".$row["nombre"].". ".(($row["descripcion"]!="no")?$row["descripcion"]:"")."</td>";
		if($row["numero_item"]==$cotizaciones[$indiceProveedorSeleccionado][$indiceCantidad][4]){
			$contenido .=	"<td align='center'>".$cotizaciones[$indiceProveedorSeleccionado][$indiceCantidad][1]*$cotizaciones[$indiceProveedorSeleccionado][$indiceCantidad][3]."</td>";
			$indiceCantidad++;
		}else{
			$contenido .=	"<td align='center'>&nbsp;</td>";
		}
		
		for($j=0;$j<sizeof($cotizaciones);$j++){
			if($row["numero_item"]==$cotizaciones[$j][$indicesCotizaciones[$j]][4]){
				$contenido .=	"<td align='center'>".$cotizaciones[$j][$indicesCotizaciones[$j]][2]."</td>";
				if($redondearProveedores[$j]=="t"){
					$contenido .=	"<td align='center'>".round(($cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3]),2)."</td>";
					$subtotalesCotizaciones[$j]+=$cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3];
					$totalesCotizaciones[$j]+=$cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3];
				}else{
					$textStr = ($cotizaciones[$j][$indicesCotizaciones[$j]][1]*$cotizaciones[$j][$indicesCotizaciones[$j]][2]*$cotizaciones[$j][$indicesCotizaciones[$j]][3])+"";
					if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
						$textStr = substr($textStr,0,strpos($textStr, ".")+3);
					}
					$contenido .=	"<td align='center'>".$textStr."</td>";
					$subtotalesCotizaciones[$j]+=(float)$textStr;
					$totalesCotizaciones[$j]+=(float)$textStr;
				}
				$indicesCotizaciones[$j]++;
			}else{
				$contenido .=	"<td align='center'>&nbsp;</td>".
								"<td align='center'>&nbsp;</td>";										
			}
		}
		while($j<$totalProveedores){
			$contenido .=	"<td align='center'>&nbsp;</td>".
							"<td align='center'>&nbsp;</td>";
			$j++;
		}
		$contenido .= "</tr>";
	}

	$contenido .=	"<tr>".
						"<td align='right'>SUB TOTAL</td>".
						"<td>&nbsp;</td>";	
	$i=0;
	while($i<$totalProveedores){
		$contenido .= 	"<td>&nbsp;</td>";
		if($i<sizeof($redondearProveedores)){
			if($redondearProveedores[$i]=="t"){
				$contenido .= "<td align='right'>".round($subtotalesCotizaciones[$i],2)."</td>";
			}else{
				$textStr = ($subtotalesCotizaciones[$i])+"";
				if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
					$textStr = substr($textStr,0,strpos($textStr, ".")+3);
				}
				$contenido .=  "<td align='right'>".$textStr."</td>";
			}
		}else{
			$contenido .=  "<td align='right'>&nbsp;</td>";
		}
		$i++;
	}
	$contenido .= 	"</tr>";
	
	$contenido .=	"<tr>".
						"<td align='right'>IVA</td>".
	$i=0;
	while($i<$totalProveedores){
		if($i==0){
			$colspan = "3";
		}else{
			$colspan = "2";
		}
		if($i<sizeof($redondearProveedores)){
			if($redondearProveedores[$i]=="t"){
				$totalesCotizaciones[$i]+=$basesCotizaciones[$i];
				$contenido .= "<td align='right' colspan='".$colspan."'>".round($basesCotizaciones[$i],2)."</td>";
			}else{
				$textStr = ($basesCotizaciones[$i])+"";
				if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
					$textStr = substr($textStr,0,strpos($textStr, ".")+3);
				}
				$totalesCotizaciones[$i]+=(float)$textStr;
				$contenido .= "<td align='right' colspan='".$colspan."'>".$textStr."</td>";
			}
		}else{
			$contenido .= "<td align='right' colspan='".$colspan."'>&nbsp;</td>";
		}
		$i++;
	}
	$contenido .= 	"</tr>";
	
	$contenido .=	"<tr>".
						"<td align='right'>TOTAL</td>".
	$i=0;
	while($i<$totalProveedores){
		if($i==0){
			$colspan = "3";
		}else{
			$colspan = "2";
		}
		if($i<sizeof($redondearProveedores)){
			if($redondearProveedores[$i]=="t"){
				$contenido .= "<td align='right' colspan='".$colspan."'>".round($totalesCotizaciones[$i],2)."</td>";
			}else{
				$textStr = ($totalesCotizaciones[$i])+"";
				if(strpos($textStr, ".")!== false && strpos($textStr, ".")+3<strlen($textStr)){
					$textStr = substr($textStr,0,strpos($textStr, ".")+3);
				}
				$contenido .= "<td align='right' colspan='".$colspan."'>".$textStr."</td>";
			}
		}else{
			$contenido .= "<td align='right' colspan='".$colspan."'>&nbsp;</td>";
		}
		$i++;
	}
	$contenido .= 	"</tr>";
	
	$contenido .="</table>";
	
	$contenido .="<table style='border-top: none;' width='100%' class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0'>";
	
	$contenido .=	"<tr>".
						"<td align='center' colspan='4'>PROVEEDOR SELECCIONADO</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td align='center' colspan='4'>".$prov_nombre."</td>".
					"</tr>";

	$contenido .=	"<tr>".
						"<td align='center' colspan='4'>CRITERIO DE SELECCI&Oacute;N:</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td align='right' colspan='2'>".
							"1.- PRECIO: _______________".(($criterio_seleccion=="1")?"<U>x</U>":"_")."_______________".
							"<br/>2.- ACUERDO DE PAGO: _______________".(($criterio_seleccion=="2")?"<U>x</U>":"_")."_______________".
							"<br/>3.- CANTIDAD: _______________".(($criterio_seleccion=="3")?"<U>x</U>":"_")."_______________".
						"</td>".
						"<td align='right' colspan='2'>".
							"4.- FECHA DE ENTREGA: ____________________".(($criterio_seleccion=="4")?"<U>x</U>":"_")."____________________".
							"<br/>5.- MARCA RECONOCIDA: ____________________".(($criterio_seleccion=="5")?"<U>x</U>":"_")."____________________".
							"<br/>6.- OTROS: ____________________".(($criterio_seleccion=="6")?"<U>x</U>":"_")."____________________".
						"</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td align='center' colspan='4'>OBSERVACIONES</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td align='center' colspan='4'>".$observaciones."&nbsp;</td>".
					"</tr>";
	
	if($tipo=="F"){		
		$contenido .="</table>".
					"</td>".
				"</tr>".
			"</table>";
		
		$footer = 	"<style type='text/css'>
						@page {
					 		@bottom-right {
					 			margin-top: 20mm;
					    		content: 'Página ' counter(page) ' de ' counter(pages);
					  		}
						}
					</style>".
					"<table width='99%' align='center' class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0'>".
						/*"<tr>".
							//"<td align='center' width='25%'>ELABORADO POR:</td>".
							"<td align='center' width='25%'>REVISADO POR:</td>".
							"<td align='center' width='25%'>CONFORMADO POR:</td>".
							"<td align='center' width='25%'>APROBADO POR:</td>".
						"</tr>".*/
						"<tr>".
							//"<td class='alineadoMedio alineadoCentro'><p><br/></p><div style='text-align: center'>".utf8_encode($elaboradoPor)."</div></td>".
							"<td align='center'>".utf8_encode($firmasSeleccionadas[PERFIL_COORDINADOR_COMPRAS]['nombre_cargo_dependencia'])."</td>".
							"<td align='center'>".utf8_encode($firmasSeleccionadas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_cargo_dependencia'])."</td>".
							"<td align='center'>".utf8_encode($firmasSeleccionadas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_cargo_dependencia'])."</td>".
						"</tr>".
						"<tr>".
							//"<td align='center'>Analista de Compras</td>".
							"<td class='alineadoMedio alineadoCentro'><p><br/></p><div style='text-align: center'>".utf8_encode($firmasSeleccionadas[PERFIL_COORDINADOR_COMPRAS]['nombre_empleado'])."</div></td>".
							"<td class='alineadoMedio alineadoCentro'><p><br/></p><div style='text-align: center'>".utf8_encode($firmasSeleccionadas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_empleado'])."</div></td>".
							"<td class='alineadoMedio alineadoCentro'><p><br/></p><div style='text-align: center'>".utf8_encode($firmasSeleccionadas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_empleado'])."</div></td>".
						"</tr>".
					"</table>".
					"<p style='margin-left: 5px;margin-top: 30px;margin-bottom: 10px;text-align: left;' class='textoTabla'>Elaborado por: _________________________________<br/>".
					"<span style='margin-left: 80px;'>".utf8_encode($elaboradoPor)."</span></p>".
					"<span style='align=center;font-family: arial;font-style:italic;font-weight:bold;font-size: 10pt;'>SAFI - Fundación Infocentro</span><br/>".
					"<span style='align=center;font-family: arial;font-size: 10pt;'>".fecha()."</span>";
		$properties = array("marginBottom" => 50, "footerHtml" => $footer, "landscape" => true);
		convert_to_pdf($contenido, $properties);		
	}else if($tipo=="L"){
		$firma .=	"<tr>".
						"<td colspan='4'>".
							"<table width='100%' style='border: none;' class='bordeTabla textoTabla' cellspacing='0' cellpadding='0' border='1'>";
	
		/*$firma .=	"<tr>".
						//"<td align='center' width='25%'>ELABORADO POR:</td>".
						"<td align='center' width='25%'>REVISADO POR:</td>".
						"<td align='center' width='25%'>CONFORMADO POR:</td>".
						"<td align='center' width='25%'>APROBADO POR:</td>".
					"</tr>";*/
	
		$firma .=	"<tr>".
						//"<td class='alineadoMedio alineadoCentro'><p>&nbsp;<br/>&nbsp;</p><div style='text-align: center'>".$elaboradoPor."</div></td>".
						"<td align='center'>".$firmasSeleccionadas[PERFIL_COORDINADOR_COMPRAS]['nombre_cargo_dependencia']."</td>".
						"<td align='center'>".$firmasSeleccionadas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_cargo_dependencia']."</td>".
						"<td align='center'>".$firmasSeleccionadas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_cargo_dependencia']."</td>".
					"</tr>";
	
		$firma .=	"<tr>".
						//"<td align='center'>Analista de Compras</td>".
						"<td class='alineadoMedio alineadoCentro'><p>&nbsp;<br/>&nbsp;</p><div style='text-align: center'>".$firmasSeleccionadas[PERFIL_COORDINADOR_COMPRAS]['nombre_empleado']."</div></td>".
						"<td class='alineadoMedio alineadoCentro'><p>&nbsp;<br/>&nbsp;</p><div style='text-align: center'>".$firmasSeleccionadas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_empleado']."</div></td>".
						"<td class='alineadoMedio alineadoCentro'><p>&nbsp;<br/>&nbsp;</p><div style='text-align: center'>".$firmasSeleccionadas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_empleado']."</div></td>".
					"</tr>";
		
		$firma .=			"</table>".
						"</td>".
					"</tr>";
		$contenido .= $firma."</table>".
							"</td>".
						"</tr>".
					"</table>".
					"<p style='margin-top: 30px;margin-bottom: 10px;' class='textoTabla'>Elaborado por: _________________________________<br/>".
					"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$elaboradoPor."</p>";
		$properties = array("landscape" => true);
		convert_to_pdf($contenido, $properties);
	}
}
pg_close($conexion);
?>
