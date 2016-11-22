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

$idRequ = "";
if (isset($_GET['idRequ']) && $_GET['idRequ'] != "") {
	$idRequ = $_GET['idRequ'];
}

if($idRequ && $idRequ!=""){
	$estadoAnulado = "15";
	
	$query = 	"SELECT ".
			 		"rebms_tipo, ".
					"rebms_tipo_imputa, ".
					"rebms_imp_p_c, ".
					"rebms_imp_esp, ".
					"rebms_prov_sugerido1, ".
					"rebms_prov_sugerido2, ".
					"rebms_prov_sugerido3, ".
					"rebms_tiempo_entrega_sugerida, ".
					"rebms_garantia_sugerida, ".
					"rebms_observaciones, ".
					"esta_nombre, ".
					"sd.depe_nombre, ".
					"sd.depe_id, ".
					"pcta_id, ".
					"to_char(fecha,'DD/MM/YYYY') as fecha, ".
					"descripcion_general, ".
					"justificacion as justificacion, ".
					"sda.depe_nombre as gerencia_adscripcion, ".
					"(sem.empl_nombres || ' ' || sem.empl_apellidos) as elaborado_por, ".
					"sem.carg_fundacion, ".
					"srbms.esta_id ".
				"FROM sai_req_bi_ma_ser srbms, sai_estado se, sai_dependenci sd, sai_dependenci sda, sai_usuario su, sai_empleado sem ".
				"WHERE ".
					"srbms.rebms_id = '".$idRequ."' AND ".
					"srbms.esta_id = se.esta_id AND ".
					"srbms.depe_id = sd.depe_id AND ".
					"srbms.gerencia_adscripcion = sda.depe_id AND ".
					"srbms.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula ";
	
	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado, 0);
	$rebms_tipo = $row["rebms_tipo"];
	$rebms_tipo_imputa = $row["rebms_tipo_imputa"];
	$rebms_imp_p_c = $row["rebms_imp_p_c"];
	$rebms_imp_esp = $row["rebms_imp_esp"];
	$rebms_prov_sugerido1 = $row["rebms_prov_sugerido1"];
	$rebms_prov_sugerido2 = $row["rebms_prov_sugerido2"];
	$rebms_prov_sugerido3 = $row["rebms_prov_sugerido3"];
	$rebms_tiempo_entrega_sugerida = $row["rebms_tiempo_entrega_sugerida"];
	$rebms_garantia_sugerida = $row["rebms_garantia_sugerida"];
	$rebms_observaciones = $row["rebms_observaciones"];
	$esta_nombre = $row["esta_nombre"];
	$depe_nombre = $row["depe_nombre"];
	$depe_id = $row["depe_id"];
	$pcta_id = $row["pcta_id"];
	$fecha = $row["fecha"];
	$descripcion_general = $row["descripcion_general"];
	$justificacion = $row["justificacion"];
	$gerencia_adscripcion = $row["gerencia_adscripcion"];
	$esta_id = $row["esta_id"];
	
	$elaboradoPor = $row["elaborado_por"];
	$cargFundacion = $row["carg_fundacion"];
	
	/*$queryCargo = "('".substr($cargFundacion, 0, 2)."000')";
	if($cargFundacion==substr(PERFIL_ASISTENTE_ADMINISTRATIVO, 0, 2)){
		if($depe_id!=DEPENDENCIA_CONSULTORIA_JURIDICA){
			$queryCargo = "('".PERFIL_DIRECTOR."','".PERFIL_GERENTE."')";
		}else{
			$queryCargo = "('".PERFIL_CONSULTOR_JURIDICO_CARGO."')";
		}
	}else if(substr($cargFundacion, 0, 2)==substr(PERFIL_ASISTENTE_EJECUTIVO, 0, 2)){
		$queryCargo = "('".PERFIL_DIRECTOR_EJECUTIVO_CARGO."')";
	}else if(substr($cargFundacion, 0, 2)==substr(PERFIL_ASISTENTE_PRESIDENCIA, 0, 2)){
		$queryCargo = "('".PERFIL_PRESIDENTE_CARGO."')";
	}*/
	if($depe_id==DEPENDENCIA_DIRECCION_EJECUTIVA){
		$queryCargo = "('".PERFIL_DIRECTOR_EJECUTIVO_CARGO."')";
	}else if($depe_id==DEPENDENCIA_PRESIDENCIA){
		$queryCargo = "('".PERFIL_PRESIDENTE_CARGO."')";
	}else if($depe_id==DEPENDENCIA_CONSULTORIA_JURIDICA){
		$queryCargo = "('".PERFIL_CONSULTOR_JURIDICO_CARGO."')";
	}else{
		$queryCargo = "('".PERFIL_DIRECTOR."','".PERFIL_GERENTE."')";
	}
	
	$sql=	"SELECT substring(carg_id from 1 for 2)||depe_id as perfil ".
			"FROM sai_depen_cargo ".
			"WHERE ".
				"depe_id = '".$depe_id."' AND ".
				"carg_id IN ".$queryCargo;
	$resultado = pg_exec($conexion ,$sql);
	$row = pg_fetch_array($resultado,0);
	$perfil = $row["perfil"];
	
	$firmas = array();
	$firmas[] = $perfil;
	$firmas[] = PERFIL_PRESIDENTE;
	$firmas[] = PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS;
	$firmas[] = PERFIL_DIRECTOR_PRESUPUESTO;
	$firmasSeleccionadas = SafiModeloFirma::GetFirmaByPerfiles($firmas);
	
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
							font-size: 19pt;
							font-weight:bold;							
						}
						.nombreCampo{
							font-family: arial;
							font-size: 19pt;
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
						.textoTabla{
							FONT-WEIGHT: normal; FONT-SIZE: 19px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none
						}
					</style>";
	
	$contenido .="<table ".(($esta_id==$estadoAnulado)?"style='background:url(\"http://safi.infocentro.gob.ve/imagenes/anulado_safi_3.gif\") no-repeat repeat-y top center;'":"")." width='1000px' class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0'>";
	
	if($rebms_tipo==TIPO_REQUISICION_COMPRA){
		$compraServicio = "&nbsp;Compra: x<br/>&nbsp;Servicio: ";		
	}else if($rebms_tipo==TIPO_REQUISICION_SERVICIO){
		$compraServicio = "&nbsp;Compra: <br/>&nbsp;Servicio: x";
	}
	
	$contenido .=	"<tr>".
						"<td colspan='2'>".
							"<table width='100%' border='1' cellspacing='0' cellpadding='0' style='border: none;'>".
								"<tr>".
									"<td rowspan='2' width='300px' align='center'>Requisici&oacute;n</td>".
									"<td rowspan='2' width='250px'>".$compraServicio."</td>".
									"<td rowspan='2' width='250px'>&nbsp;Punto de Cuenta: ".$pcta_id."</td>".
									"<td width='200px' height='30px'>&nbsp;N&deg;.: ".$idRequ."</td>".
								"</tr>".
								"<tr>".
									"<td height='30px'>&nbsp;Fecha: ".$fecha."</td>".
								"</tr>".
							"</table>".
						"</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td width='50%'><div style='margin-top: 5px;margin-bottom: 5px;'>Unidad Solicitante: ".$depe_nombre."</div></td>".
						"<td width='50%'><div style='margin-top: 5px;margin-bottom: 5px;'>Unidad de adscripci&oacute;n: ".$gerencia_adscripcion."</div></td>".
					"</tr>";
	
	if($rebms_tipo==TIPO_REQUISICION_COMPRA){
		$loSolicitado = "Compra solicitada:";		
	}else if($rebms_tipo==TIPO_REQUISICION_SERVICIO){
		$loSolicitado = "Servicio solicitado:";
	}
	$contenido .=	"<tr>".
						"<td colspan='2' align='center' height='30px'>".$loSolicitado."</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td colspan='2' align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$descripcion_general."&nbsp;</div></td>".
					"</tr>";

	$contenido .=	"<tr>".
						"<td colspan='2'>".
							"<table width='100%' border='1' cellspacing='0' cellpadding='0' style='border: none;'>".
								"<tr>".
									"<td width='22%'><div style='margin-top: 5px;margin-bottom: 5px;'>Descripci&oacute;n:</div></td>".
									"<td width='8%'><div style='margin-top: 5px;margin-bottom: 5px;'>Cantidad:</div></td>".
									"<td width='14%'><div style='margin-top: 5px;margin-bottom: 5px;'>Partida presupuestaria:</div></td>".
									"<td width='56%'><div style='margin-top: 5px;margin-bottom: 5px;'>Especificaci&oacute;n:</div></td>".
								"</tr>";
	
	$resultado = pg_exec($conexion, "SELECT DISTINCT si.nombre AS nombre, sri.rbms_item_cantidad as cantidad, sri.rbms_item_desc as descripcion, INITCAP(sp.part_nombre) as nombre_partida, sp.part_id ".
									"FROM sai_rqui_items sri, sai_item si, sai_item_partida sip, sai_partida sp ".
									"WHERE ".
										"sri.rebms_id = '".$idRequ."' AND ".
										"sri.rbms_item_arti_id = si.id AND ".
										"sri.rbms_item_arti_id = sip.id_item AND ".
										"sip.part_id = sp.part_id AND ".
										"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ".
									"ORDER BY si.nombre ASC");
	
	$numeroFilas = pg_numrows($resultado);
	
	if($numeroFilas>0){
		$i=0;
		while($row=pg_fetch_array($resultado)){
			$contenido .=	"<tr>".
								"<td><div style='margin-top: 5px;margin-bottom: 5px;'>".$row["nombre"]."</div></td>".
								"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$row["cantidad"]."</div></td>".
								"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$row["part_id"]."</div></td>".
								"<td><div style='margin-top: 5px;margin-bottom: 5px;'>".(($row["descripcion"]!="no")?$row["descripcion"]:"")."</div></td>".
							"</tr>";
			$i++;
		}
		$contenido .=			"</table>".
							"</td>".
						"</tr>";		
	}
	
	$contenido .=	"<tr>".
						"<td valign='top' colspan='2'>".
							"<table width='100%' border='0' cellspacing='0' cellpadding='0' style='border: none;'>".
								"<tr>".
									"<td class='alineadoArriba' width='861px' style='border-right: solid'><div style='margin-top: 5px;margin-bottom: 5px;'>Justificaci&oacute;n de la compra o el servicio: (Para compras de ACTIVOS se requiere Informe o Exposici&oacute;n detallada de la necesidad de la adquisici&oacute;n)</div></td>".
									"<td align='center' width='70px' style='border-right: solid;'><div style='margin-top: 5px;margin-bottom: 5px;'>Centro Gestor:</div></td>".
									"<td align='center' width='70px'><div style='margin-top: 5px;margin-bottom: 5px;'>Centro Costo:</div></td>".
								"</tr>".
							"</table>".
						"</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td colspan='2'>".
							"<table width='100%' border='0' cellspacing='0' cellpadding='0' style='border: none;'>".
								"<tr>".
									"<td width='861px' style='border-right: solid'><div style='margin-top: 5px;margin-bottom: 5px;'>".$justificacion."</div></td>".
									"<td align='center' width='70px' style='border-right: solid;'><div style='margin-top: 5px;margin-bottom: 5px;'>".$centro_gestor."</div></td>".
									"<td align='center' width='70px'><div style='margin-top: 5px;margin-bottom: 5px;'>".$centro_costo."</div></td>".
								"</tr>".
							"</table>".
						"</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td colspan='2' align='center' height='30px'>Proveedores sugeridos:</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td colspan='2'>".
							"<table width='100%' border='1' cellspacing='0' cellpadding='0' style='border: none;'>".
								"<tr>".
									"<td align='center' width='36%' height='30px'>Nombre o raz&oacute;n social</td>".
									"<td align='center' width='20%' height='30px'>Persona contacto</td>".
									"<td align='center' width='12%' height='30px'>Tel&eacute;fono</td>".
									"<td align='center' width='12%' height='30px'>Fax</td>".
									"<td align='center' width='20%' height='30px'>Correo electr&oacute;nico</td>".
								"</tr>";

	$prov_nombre_c = "";
	$prov_tel_c = "";
	$prov_fax = "";
	$prov_email = "";
	if($rebms_prov_sugerido1!=""){
		$query = "SELECT prov_id_rif,(prov_nombre) AS prov_nombre, INITCAP(prov_nombre_c) AS prov_nombre_c, prov_tel_c, prov_fax, LOWER(prov_email) AS prov_email FROM sai_proveedor_nuevo WHERE prov_id_rif = '".$rebms_prov_sugerido1."'";
		$resultado = pg_exec($conexion, $query);
		$elementos = pg_numrows($resultado);
		if($elementos>0){
			$row = pg_fetch_array($resultado, 0);
			$rebms_prov_sugerido1 = $row["prov_nombre"]." (RIF ".strtoupper(substr(trim($row["prov_id_rif"]),0,1))."-".substr(trim($row["prov_id_rif"]),1).")";
			$prov_nombre_c = $row["prov_nombre_c"];
			$prov_tel_c = $row["prov_tel_c"];
			$prov_fax = $row["prov_fax"];
			$prov_email = $row["prov_email"];
		}
		$contenido .=	"<tr>".
							"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$rebms_prov_sugerido1."</div></td>".
							"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$prov_nombre_c."</div></td>".
							"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$prov_tel_c."</div></td>".
							"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$prov_fax."</div></td>".
							"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$prov_email."</div></td>".
						"</tr>";
	}
	
	$prov_nombre_c = "";
	$prov_tel_c = "";
	$prov_fax = "";
	$prov_email = "";
	if($rebms_prov_sugerido2!=""){
		$query = "SELECT prov_id_rif,INITCAP(prov_nombre) AS prov_nombre, INITCAP(prov_nombre_c) AS prov_nombre_c, prov_tel_c, prov_fax, LOWER(prov_email) AS prov_email FROM sai_proveedor_nuevo WHERE prov_id_rif = '".$rebms_prov_sugerido2."'";
		$resultado = pg_exec($conexion, $query);
		$elementos = pg_numrows($resultado);
		if($elementos>0){
			$row = pg_fetch_array($resultado, 0);
			$rebms_prov_sugerido2 = $row["prov_nombre"]." (RIF ".strtoupper(substr(trim($row["prov_id_rif"]),0,1))."-".substr(trim($row["prov_id_rif"]),1).")";
			$prov_nombre_c = $row["prov_nombre_c"];
			$prov_tel_c = $row["prov_tel_c"];
			$prov_fax = $row["prov_fax"];
			$prov_email = $row["prov_email"];
		}
		$contenido .=	"<tr>".
							"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$rebms_prov_sugerido2."</div></td>".
							"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$prov_nombre_c."</div></td>".
							"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$prov_tel_c."</div></td>".
							"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$prov_fax."</div></td>".
							"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$prov_email."</div></td>".
						"</tr>";
	}
	
	$prov_nombre_c = "";
	$prov_tel_c = "";
	$prov_fax = "";
	$prov_email = "";
	if($rebms_prov_sugerido3!=""){
		$query = "SELECT prov_id_rif,INITCAP(prov_nombre) AS prov_nombre, INITCAP(prov_nombre_c) AS prov_nombre_c, prov_tel_c, prov_fax, LOWER(prov_email) AS prov_email FROM sai_proveedor_nuevo WHERE prov_id_rif = '".$rebms_prov_sugerido3."'";
		$resultado = pg_exec($conexion, $query);
		$elementos = pg_numrows($resultado);
		if($elementos>0){
			$row = pg_fetch_array($resultado, 0);
			$rebms_prov_sugerido3 = $row["prov_nombre"]." (RIF ".strtoupper(substr(trim($row["prov_id_rif"]),0,1))."-".substr(trim($row["prov_id_rif"]),1).")";
			$prov_nombre_c = $row["prov_nombre_c"];
			$prov_tel_c = $row["prov_tel_c"];
			$prov_fax = $row["prov_fax"];
			$prov_email = $row["prov_email"];
		}
		$contenido .=	"<tr>".
							"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$rebms_prov_sugerido3."</div></td>".
							"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$prov_nombre_c."</div></td>".
							"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$prov_tel_c."</div></td>".
							"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$prov_fax."</div></td>".
							"<td align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$prov_email."</div></td>".
						"</tr>";
	}
	
	$contenido .=			"</table>".
						"</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td colspan='2'>&nbsp;</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td colspan='2' align='center' height='30px'>Observaciones</td>".
					"</tr>".
					"<tr>".
						"<td colspan='2' align='center'><div style='margin-top: 5px;margin-bottom: 5px;'>".$rebms_observaciones."&nbsp;</div></td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td colspan='2'>".
							"<table width='100%' border='1' cellspacing='0' cellpadding='0' style='border: none;font-size: 14pt;'>".
								"<tr>";
	if (	$depe_id==DEPENDENCIA_OFICINA_DE_GESTION_ADMINISTRATIVA_Y_FINANCIERA || 
			$depe_id==DEPENDENCIA_OFICINA_DE_PLANIFICACION_PRESUPUESTO_Y_CONTROL ||
			$depe_id==DEPENDENCIA_PRESIDENCIA
			) {
		$contenido .=				"<td align='center' class='alineadoArriba' width='33%'>".$firmasSeleccionadas[PERFIL_DIRECTOR_PRESUPUESTO]['nombre_cargo_dependencia']."</td>".
									"<td align='center' class='alineadoArriba' width='33%'>".$firmasSeleccionadas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_cargo_dependencia']."</td>".
									"<td align='center' class='alineadoArriba' width='34%'>".$firmasSeleccionadas[PERFIL_PRESIDENTE]['nombre_cargo_dependencia']."</td>".
								"</tr>".
								"<tr>".
									"<td align='center' class='alineadoAbajo' height='100px'>".$firmasSeleccionadas[PERFIL_DIRECTOR_PRESUPUESTO]['nombre_empleado']."</td>".
									"<td align='center' class='alineadoAbajo'>".$firmasSeleccionadas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_empleado']."</td>".
									"<td align='center' class='alineadoAbajo'>".$firmasSeleccionadas[PERFIL_PRESIDENTE]['nombre_empleado']."</td>".
								"</tr>";
	}else{
		$contenido .=				"<td align='center' class='alineadoArriba' width='25%'>".$firmasSeleccionadas[$perfil]['nombre_cargo_dependencia']."</td>".
									"<td align='center' class='alineadoArriba' width='25%'>".$firmasSeleccionadas[PERFIL_DIRECTOR_PRESUPUESTO]['nombre_cargo_dependencia']."</td>".
									"<td align='center' class='alineadoArriba' width='25%'>".$firmasSeleccionadas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_cargo_dependencia']."</td>".
									"<td align='center' class='alineadoArriba' width='25%'>".$firmasSeleccionadas[PERFIL_PRESIDENTE]['nombre_cargo_dependencia']."</td>".
								"</tr>".
								"<tr>".
									"<td align='center' class='alineadoAbajo' height='100px'>".$firmasSeleccionadas[$perfil]['nombre_empleado']."</td>".
									"<td align='center' class='alineadoAbajo' height='100px'>".$firmasSeleccionadas[PERFIL_DIRECTOR_PRESUPUESTO]['nombre_empleado']."</td>".
									"<td align='center' class='alineadoAbajo'>".$firmasSeleccionadas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_empleado']."</td>".
									"<td align='center' class='alineadoAbajo'>".$firmasSeleccionadas[PERFIL_PRESIDENTE]['nombre_empleado']."</td>".
								"</tr>";
	}
	$contenido .=			"</table>".
						"</td>".
					"</tr>".
				"</table>".
				"<br/>".
				"<br/>Elaborado por: _________________________________".
				"<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".
				$elaboradoPor;
	
	$footer = 	"<style type='text/css'>
					@page {
				 		@bottom-right {
				 			margin-top: 18mm;
				    		content: 'Página ' counter(page) ' de ' counter(pages);
				  		}
					}
				</style>".
				"<p align='center'>Av. Universidad, Esquina el Chorro, Torre Ministerial, Piso 11, La Hoyada, Caracas<br/>Teléfono: 0212-7718520/7718672 Fax: 0212-7718672<br/>www.infocentro.gob.ve</p><br/>".
				"<span style='align=center;font-family: arial;font-style:italic;font-weight:bold;font-size: 10pt;'>SAFI - Fundación Infocentro</span><br/>".
				"<span style='align=center;font-family: arial;font-size: 10pt;'>".fecha()."</span>";
	$properties = array("marginBottom" => 25, "footerHtml" => $footer);
	convert_to_pdf($contenido, $properties);
	pg_close($conexion);
}
?>