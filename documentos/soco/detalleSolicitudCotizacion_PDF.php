<?php
require("../../includes/conexion.php");
require("../../includes/constantes.php");
if	( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush(); 
	exit;
}

require("../../includes/reporteBasePdf.php"); 
require("../../includes/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require("../../includes/html2ps/funciones.php");

$codigo = "";
if (isset($_REQUEST['codigo']) && $_REQUEST['codigo'] != "") {
	$codigo = $_REQUEST['codigo'];
}
$fecha = "";
if (isset($_REQUEST['fecha']) && $_REQUEST['fecha'] != "") {
	$fecha = $_REQUEST['fecha'];
}
$rifProveedor = "";
if (isset($_REQUEST['proveedor']) && $_REQUEST['proveedor'] != "") {
	$rifProveedor = $_REQUEST['proveedor'];
}

$contenido = "<style type='text/css'>
						.titulo{
							text-align:center;
							font-size: 14pt;
							font-weight:bold;							
						}
						.nombreCampo{
							font-family: arial;
							font-size: 12pt;
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
							border: solid 1px #800000;
						}
					</style>";

$query = 	"SELECT ".
				"sp.prov_nombre, ".
				"sp.prov_id_rif, ".
				"sp.prov_telefonos, ".
				"sp.prov_fax, ".
				"sp.prov_email, ".
				"to_char(sscp.fecha,'DD/MM/YYYY HH:MI am') as fecha, ".
				"sscp.tiempo_entrega, ".
				"sscp.sitio_entrega, ".
				"sscp.anexos, ".
				"sscp.observaciones, ".
				"UPPER(sem.empl_nombres || ' ' || sem.empl_apellidos) as elaborado_por, ".
				"sem.empl_cedula, ".
				"sem.empl_tlf_ofic, ".
				"sem.empl_email ".
			"FROM ".
				"sai_sol_coti_prov sscp, sai_sol_coti ssc, sai_proveedor_nuevo sp, sai_usuario su, sai_empleado sem ".
			"WHERE ".
				"sscp.soco_id = '".$codigo."' AND ".
				"to_char(sscp.fecha,'DD/MM/YYYY:HH24:MI:SS') = '".$fecha."' AND ".
				(($rifProveedor && $rifProveedor!="")?" lower(sscp.beneficiario_rif) like '%".strtolower($rifProveedor)."%' AND ":"").
				"sscp.beneficiario_rif = sp.prov_id_rif AND ".
				"ssc.soco_id = '".$codigo."' AND ".
				"ssc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula ".
			"ORDER BY sp.prov_nombre ";

$resultadoProveedores = pg_exec($conexion,$query);
$numeroFilasProveedores = pg_numrows($resultadoProveedores);

$tiempoEntrega = "";
$sitioEntrega = "";
$anexos = "";
$observaciones = "";
$enviadoEnFecha = "";
for($ri = 0; $ri < $numeroFilasProveedores; $ri++) {
	$rowProveedores = pg_fetch_array($resultadoProveedores, $ri);
	$nombreProveedor = $rowProveedores["prov_nombre"]." (Rif ".strtoupper(substr(trim($rowProveedores["prov_id_rif"]),0,1))."-".substr(trim($rowProveedores["prov_id_rif"]),1).")<br/>";
	$tiempoEntrega = $rowProveedores["tiempo_entrega"];
	$sitioEntrega = $rowProveedores["sitio_entrega"];
	$anexos = $rowProveedores["anexos"];
	$observaciones = $rowProveedores["observaciones"];
	$enviadoEnFecha = $rowProveedores["fecha"];
	$telefonos = $rowProveedores["prov_telefonos"];
	$fax = $rowProveedores["prov_fax"];
	$elaboradoPor = $rowProveedores["elaborado_por"];
	$cedulaEmpleado = $rowProveedores["empl_cedula"];
	$telefonoEmpleado = $rowProveedores["empl_tlf_ofic"];
	/*$telefonoEmpleado="";*/
	if (!$telefonoEmpleado || $telefonoEmpleado=="") {
		if($cedulaEmpleado=="11820723"){
			$telefonoEmpleado = "0212-7718660";		
		}else if($cedulaEmpleado=="15244719"){
			$telefonoEmpleado = "0212-7718660";
		}else if($cedulaEmpleado=="14595993"){
			$telefonoEmpleado = "0212-7718864";
		}else if($cedulaEmpleado=="18366421"){
			$telefonoEmpleado = "0212-7718672";
		}else if($cedulaEmpleado=="1756576"){
			$telefonoEmpleado = "0212-7718859";
		}else if($cedulaEmpleado=="24669788"){
			$telefonoEmpleado = "0212-7718864";
		}
	}
	/*if($cedulaEmpleado=="11820723"){
		$telefonoEmpleado = "0212-7718554";		
	}else if($cedulaEmpleado=="15244719"){
		$telefonoEmpleado = "0212-7718660";
	}else if($cedulaEmpleado=="14595993"){
		$telefonoEmpleado = "0212-7718864";
	}else if($cedulaEmpleado=="18366421"){
		$telefonoEmpleado = "0212-7718672";
	}else if($cedulaEmpleado=="1756576"){
		$telefonoEmpleado = "0212-7718859";
	}*/
	$emailEmpleado = $rowProveedores["empl_email"];
	$emailProveedor = $rowProveedores["prov_email"];

	$contenido .=	"<p class='titulo'>Solicitud de Cotizaci&oacute;n</p><br/>".
					"<table width='1000px'>";
	
	$query = 	"SELECT ".
					"srbms.rebms_id, ".
					"srbms.rebms_tipo ".
				"FROM ".
					"sai_sol_coti ssc, sai_req_bi_ma_ser srbms ".
				"WHERE ".
					"ssc.soco_id = '".$codigo."' AND ".
					"ssc.rebms_id = srbms.rebms_id";
	$resultado = pg_exec($conexion,$query);
	$row = pg_fetch_array($resultado, 0);
	$idRequ = $row["rebms_id"];
	$rebms_tipo = $row["rebms_tipo"];
	
	if($rebms_tipo==TIPO_REQUISICION_COMPRA){
		$tituloTabla = "Compra requerida";
	}else if($rebms_tipo==TIPO_REQUISICION_SERVICIO){
		$tituloTabla = "Servicio requerido";
	}
	
	$contenido .=		"<tr>".
							"<td class='nombreCampo' style='width: 100px;'>".
								"<div class='valorCampo'>Para: </div>".
							"<td/>".
							"<td>".
								"<div class='valorCampo'>".$nombreProveedor."</div>".
							"</td>".
							"<td class='nombreCampo' style='width: 100px;'>".
								"<div class='valorCampo'>De: </div>".
							"<td/>".
							"<td>".
								"<div class='valorCampo'>".$elaboradoPor."</div>".
							"</td>".
						"</tr>".
						"<tr>".
							"<td class='nombreCampo' style='width: 100px;'>".
								"<div class='valorCampo'>Tlf./Fax: </div>".
							"<td/>".
							"<td>".
								"<div class='valorCampo'>".$telefonos." / ".$fax."</div>".
							"</td>".
							"<td class='nombreCampo' style='width: 100px;'>".
								"<div class='valorCampo'>Tlf.: </div>".
							"<td/>".
							"<td>".
								"<div class='valorCampo'>".$telefonoEmpleado."</div>".
							"</td>".
						"</tr>".
						"<tr>".
							"<td class='nombreCampo' style='width: 100px;'>".
								"<div class='valorCampo'>Email:</div>".
							"</td>".
							"<td>".
								"<div class='valorCampo'>".$emailProveedor."</div>".
							"</td>".
							"<td class='nombreCampo' style='width: 100px;'>".
								"<div class='valorCampo'>Email:</div>".
							"</td>".
							"<td>".
								"<div class='valorCampo'>".$emailEmpleado.", compras@infocentro.gob.ve</div>".
							"</td>".
						"</tr>".
						"<tr>".
							"<td class='nombreCampo' style='width: 100px;'>".
								"<div class='valorCampo'>Fecha:</div>".
							"</td>".
							"<td colspan='3'>".
								"<div class='valorCampo'>".$enviadoEnFecha."</div>".
							"</td>".
						"</tr>".
						"<tr>".
							"<td class='nombreCampo' colspan='4'>".
								"<div class='valorCampo'>".$tituloTabla.":</div>".
							"</td>".
						"</tr>".
					"</table>".
					"<table class='bordeTabla' cellspacing='0' cellpadding='0'>".
						"<tr style='background-color: #45459F; color:#FFFFFF; text-align: center;'>";
	
	if($rebms_tipo==TIPO_REQUISICION_COMPRA){
		$contenido .="<td class='bordeTabla' width='400px'>Compra</td>";		
	}else if($rebms_tipo==TIPO_REQUISICION_SERVICIO){
		$contenido .="<td class='bordeTabla' width='400px'>Servicio</td>";
	}
	$contenido .=	"<td class='bordeTabla' width='100px'>Cantidad</td>".
					"<td class='bordeTabla' width='500px'>Especificaciones</td>";
	$contenido .="</tr>";
	
	$resultado = pg_exec($conexion, "SELECT si.nombre, ssci.cantidad, ssci.especificaciones AS descripcion ".
									"FROM sai_sol_coti_item ssci, sai_item si ".
									"WHERE ".
										"ssci.soco_id = '".$codigo."' AND ".
										"to_char(ssci.fecha,'DD/MM/YYYY:HH24:MI:SS') = '".$fecha."' AND ".
										"ssci.id_item = si.id ".
									"ORDER BY si.nombre ASC");
		
	$numeroFilas = pg_numrows($resultado);
	
	if($numeroFilas>0){
		$i=0;
		while($row=pg_fetch_array($resultado))  {
			$contenido .="<tr>".
							"<td class='bordeTabla'>".$row["nombre"]."</td>".
							"<td class='bordeTabla' align='center'>".$row["cantidad"]."</td>".
							"<td class='bordeTabla'>".(($row["descripcion"]!="no")?$row["descripcion"]:"")."</td>".
						"</tr>";
			$i++;
		}
	}
	$contenido .=	"</table>".
					"<b>NOTA: REALIZAR EL PRESUPUESTO CON LAS MISMAS ESPECIFICACIONES QUE SE COLOCAN EN LA PRESENTE SOLICITUD</b><br/><br/>".
					"<table width='1000'>".
						"<tr>".
							"<td class='nombreCampo'>Tiempo de entrega:</td>".
							"<td><div class='valorCampo'>".$tiempoEntrega."</div></td>".
						"</tr>".
						"<tr>".
							"<td class='nombreCampo'>Sitio de entrega:</td>".
							"<td><div class='valorCampo'>".$sitioEntrega."</div></td>".
						"</tr>";
	
	$beneficiario = "g200077280";
	$nombreBeneficiario = "FUNDACIÓN INFOCENTRO";
	
	$contenido .=		"<tr>".
							"<td class='nombreCampo'>Beneficiario:</td>".
							"<td><div class='valorCampo'>".utf8_decode($nombreBeneficiario." (RIF ".strtoupper(substr(trim($beneficiario),0,1))."-".substr(trim($beneficiario),1).")")."</div></td>".
						"</tr>".
						"<tr>".
							"<td class='nombreCampo'>Anexos:</td>".
							"<td><div class='valorCampo'>".$anexos."</div></td>".
						"</tr>".
						"<tr>".
							"<td class='nombreCampo'>Observaciones:</td>".
							"<td><div class='valorCampo'>".$observaciones."</div></td>".
						"</tr>".
					"</table>";
	if(($ri+1)<$numeroFilasProveedores){
		$contenido .="<!--NewPage-->";
	}
}

pg_close($conexion);
$footer = 	"<span style='align=center;font-family: arial;font-style:italic;font-weight:bold;font-size: 10pt;'>SAFI - Fundación Infocentro</span><br/>".
			"<span style='align=center;font-family: arial;font-size: 10pt;'>".fecha()."</span>";
$properties = array("footerHtml" => $footer);
convert_to_pdf($contenido, $properties);
?>