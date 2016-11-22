<?php
require("../../includes/conexion.php");
require("../../includes/constantes.php");
if	( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush(); 
	exit;
}
require("../../lib/mail/class.phpmailer.php");
require("../../includes/reporteBasePdf.php"); 
require("../../includes/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require("../../includes/html2ps/funciones.php");

$codigo = "";
if (isset($_REQUEST['codigo']) && $_REQUEST['codigo'] != "") {
	$codigo = $_REQUEST['codigo'];
}
$codigoCR = "";
if (isset($_REQUEST['codigoCR']) && $_REQUEST['codigoCR'] != "") {
	$codigoCR = $_REQUEST['codigoCR'];
}
$idRequ = "";
if (isset($_REQUEST['idRequ']) && $_REQUEST['idRequ'] != "") {
	$idRequ = $_REQUEST['idRequ'];
}
$tipoRequ = TIPO_REQUISICION_TODAS;
if (isset($_REQUEST['tipoRequ']) && $_REQUEST['tipoRequ'] != "") {
	$tipoRequ = $_REQUEST['tipoRequ'];
}
$tipoBusq = TIPO_BUSQUEDA_REQUISICIONES;
if (isset($_REQUEST['tipoBusq']) && $_REQUEST['tipoBusq'] != "") {
	$tipoBusq = $_REQUEST['tipoBusq'];
}
$proyAcc = "";
if (isset($_REQUEST['proyAcc']) && $_REQUEST['proyAcc'] != "") {
	$proyAcc = $_REQUEST['proyAcc'];
}
$radioProyAcc = "";
if (isset($_REQUEST['radioProyAcc']) && $_REQUEST['radioProyAcc'] != "") {
	$radioProyAcc = $_REQUEST['radioProyAcc'];
}
$proyecto = "";
$accionCentralizada = "";
if($radioProyAcc=="proyecto"){
	if (isset($_REQUEST['proyecto']) && $_REQUEST['proyecto'] != "") {
		$proyecto = $_REQUEST['proyecto'];
	}		
}else if($radioProyAcc=="accionCentralizada"){
	if (isset($_REQUEST['accionCentralizada']) && $_REQUEST['accionCentralizada'] != "") {
		$accionCentralizada = $_REQUEST['accionCentralizada'];
	}		
}else{
	$proyAcc = "";
}
$dependencia = "";
if (isset($_REQUEST['dependencia']) && $_REQUEST['dependencia'] != "") {
	$dependencia = $_REQUEST['dependencia'];
}
$estado = ESTADO_REQUISICION_PENDIENTES;
if (isset($_REQUEST['estado']) && $_REQUEST['estado'] != "") {
	$estado = $_REQUEST['estado'];
}
$controlFechas = "";
if (isset($_REQUEST['controlFechas']) && $_REQUEST['controlFechas'] != "") {
	$controlFechas = $_REQUEST['controlFechas'];
}
$fechaInicio = "";
if (isset($_REQUEST['fechaInicio']) && $_REQUEST['fechaInicio'] != "") {
	$fechaInicio = $_REQUEST['fechaInicio'];
}
$fechaFin = "";
if (isset($_REQUEST['fechaFin']) && $_REQUEST['fechaFin'] != "") {
	$fechaFin = $_REQUEST['fechaFin'];
}
$pagina = "1";
if (isset($_REQUEST['pagina']) && $_REQUEST['pagina'] != "") {
	$pagina = $_REQUEST['pagina'];
}
if (isset($_REQUEST['asunto']) && $_REQUEST['asunto'] != "") {
	$asunto = $_REQUEST['asunto'];
}
if (isset($_REQUEST['cuerpo']) && $_REQUEST['cuerpo'] != "") {
	$cuerpo = $_REQUEST['cuerpo'];
}
$proveedores = "";
if (isset($_REQUEST['proveedores']) && $_REQUEST['proveedores'] != "") {
	$proveedores = "(";
	$tok = strtok($_REQUEST['proveedores'], ",");
	while ($tok !== false) {
	    $proveedores = $proveedores."'".$tok."',";
	    $tok = strtok(",");
	}
	$proveedores = substr($proveedores, 0, -1).")";
}
$sql = 	"SELECT srbms.depe_id FROM sai_req_bi_ma_ser srbms ".
		"WHERE ".
			"srbms.rebms_id = '".$idRequ."'";
$resultado = pg_exec($conexion, $sql);
$row = pg_fetch_array($resultado, 0);
$depe_id = $row["depe_id"];

$resultado = pg_exec($conexion,	"SELECT soco_id ".
								"FROM sai_sol_coti ".
								"WHERE ".
									"rebms_id = '".$idRequ."' AND usua_login = '".$_SESSION['login']."'");
$numeroFilas = pg_numrows($resultado);
if($numeroFilas==0){
	$sql="select * from sai_insert_sol_coti('".$_SESSION['user_depe_id']."','".$idRequ."','".$_SESSION['login']."') as resultado_set(text)";
	$resultado = pg_exec($conexion ,$sql);
	if ($resultado){
		$row = pg_fetch_array($resultado,0);
		$codido_soco=trim($row[0]);
	}
	
	//Fin de la cadena de Requisicion
	$user_perfil_id = $_SESSION['user_perfil_id'];
	$vistoBueno = 6;
	$estadoAprobado = 13;
	$documentoRequisicion = "rqui";
	$fin = 99;	
	//$queryCadena = "swfg.wfgr_perf = '".$user_perfil_id."' ";
	$queryCadena =	"(swfg.wfgr_perf = '".$user_perfil_id."' ".
						"OR swfg.wfgr_perf like '".$user_perfil_id."%' ".
						"OR swfg.wfgr_perf like '%/".$user_perfil_id."' ".
						"OR swfg.wfgr_perf like '%/".$user_perfil_id."%') ";
	$sql=	"SELECT ".
				"swc.wfca_id, ".
				"swc.wfob_id_ini ".
			"FROM sai_wfcadena swc, sai_wfgrupo swfg ".
			"WHERE ".
				$queryCadena." AND ".
				"swfg.wfgr_id = swc.wfgr_id AND ".
				"swc.docu_id = '".$documentoRequisicion."' AND ".
				"swc.wfop_id = ".$vistoBueno." AND ".
				(($depe_id=="350" || $depe_id=="150")?"swc.depe_id = '".$depe_id."' AND ":" (swc.depe_id IS NULL OR swc.depe_id = '') AND ").
				"swc.wfob_id_sig = ".$fin;
	$resultado = pg_exec($conexion ,$sql);
	if($resultado){
		$row = pg_fetch_array($resultado,0);
		$wfca_id=trim($row[0]);
		$wfob_id_ini=trim($row[1]);
		//Se actualiza el documento generado con el nivel correspondiente en la cadena	
		//$sql="UPDATE sai_doc_genera SET wfob_id_ini = ".$wfob_id_ini.", wfca_id = ".$wfca_id.", perf_id_act = '".$user_perfil_id."', esta_id = ".$estadoAprobado." WHERE docg_id = '".$idRequ."'";
		$sql="UPDATE sai_doc_genera SET wfob_id_ini = ".$wfob_id_ini.", wfca_id = ".$wfca_id.", perf_id_act = '15456', esta_id = ".$estadoAprobado." WHERE docg_id = '".$idRequ."'";
		$resultado = pg_exec($conexion ,$sql);
	}
	
	$insertarDocumento = 1;
	$documentoSoco = "soco";
	//Inicia la cadena de Solicitud de Cotizacion
	$sql="select wfca_id from sai_wfcadena where docu_id = '".$documentoSoco."' AND wfop_id = ".$insertarDocumento;
	$resultado = pg_exec($conexion ,$sql);
	if($resultado){
		$row = pg_fetch_array($resultado,0);
		$wfca_id=trim($row[0]);
	}
	//$sql="select * from sai_insert_doc_generado('".$codido_soco."',1,".$wfca_id.",'".$_SESSION['login']."','".$user_perfil_id."',10,1,'".$user_perfil_id."','N/A') as resultado_set(text)";
	$sql="select * from sai_insert_doc_generado('".$codido_soco."',1,".$wfca_id.",'".$_SESSION['login']."','15456',10,1,'15456','N/A') as resultado_set(text)";
	$resultado_insert = pg_exec($conexion ,$sql);
}else{
	$row = pg_fetch_array($resultado, 0);
	$codido_soco = $row[0];
}

$tok = strtok($_REQUEST['items'], ",");
$date = date("Y-m-j G:i:s");
while ($tok !== false) {
	$query = 	"SELECT ".
					"sri.rbms_item_arti_id AS id_item, ".
					"sri.numero_item, ".
					"sri.rbms_item_cantidad AS cantidad, ".
					"sri.rbms_item_desc AS especificaciones ".
				"FROM ".
					"sai_rqui_items sri ".
				"WHERE ".
					"sri.rebms_id = '".$idRequ."' AND ".
					"sri.numero_item = ".$tok." ";
	$resultadoItem = pg_exec($conexion, $query);
	$rowItem = pg_fetch_array($resultadoItem, 0);
	
	pg_exec($conexion,	"INSERT INTO sai_sol_coti_item ".
						"(soco_id, id_item, numero_item, fecha, especificaciones, cantidad) ".
						"VALUES ('".$codido_soco."',".$rowItem["id_item"].",".$tok.",'".$date."','".$rowItem["especificaciones"]."',".$rowItem["cantidad"].")");
	$tok = strtok(",");
}

$tok = strtok($_REQUEST['proveedores'], ",");
while ($tok !== false) {
	$query = "INSERT INTO sai_sol_coti_prov ".
						"(soco_id, beneficiario_rif, fecha, tiempo_entrega, sitio_entrega, anexos, observaciones, asunto, cuerpo) ".
						"VALUES ('".$codido_soco."','".$tok."','".$date."','".$_REQUEST["tiempoEntrega"]."','".$_REQUEST["sitioEntrega"]."','".$_REQUEST["anexos"]."','".$_REQUEST["observaciones"]."','".$asunto."','".$cuerpo."')"; 

	try{
		if(!pg_exec($conexion,	$query)){
			
			throw new Exception("Error al insertar en la tabla sai_sol_coti_prov. Query: " . $query);
		};
	}catch (Exception $e){	
		error_log($e);
	}
		
	$tok = strtok(",");
}

$query = 	"SELECT ".
		 		"rebms_tipo ".
			"FROM sai_req_bi_ma_ser srbms ".
			"WHERE ".
				"srbms.rebms_id = '".$idRequ."' ";

$resultado = pg_exec($conexion, $query);
$row = pg_fetch_array($resultado, 0);
$rebms_tipo = $row["rebms_tipo"];

$footer = 	"<span style='align=center;font-family: arial;font-style:italic;font-weight:bold;font-size: 10pt;'>SAFI - Fundación Infocentro</span><br/>".
			"<span style='align=center;font-family: arial;font-size: 10pt;'>".fecha()."</span>";

$estilos = "<style type='text/css'>
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
$contenido = $estilos;

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
				"sscp.asunto, ".
				"sscp.cuerpo, ".
				"UPPER(sem.empl_nombres || ' ' || sem.empl_apellidos) as elaborado_por, ".
				"sem.empl_cedula, ".
				"sem.empl_tlf_ofic, ".
				"sem.empl_email ".
			"FROM ".
				"sai_sol_coti_prov sscp, sai_sol_coti ssc, sai_proveedor_nuevo sp, sai_usuario su, sai_empleado sem ".
			"WHERE ".
				"sscp.soco_id = '".$codido_soco."' AND ".
				"sscp.fecha IN (SELECT MAX(fecha) FROM sai_sol_coti_prov WHERE soco_id = '".$codido_soco."') AND ".
				"sscp.beneficiario_rif = sp.prov_id_rif AND ".
				"ssc.soco_id = '".$codido_soco."' AND ".
				"ssc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula ".
			"ORDER BY sp.prov_nombre, sp.prov_id_rif ";

$resultadoProveedores = pg_exec($conexion,$query);
$numeroFilasProveedores = pg_numrows($resultadoProveedores);

$tiempoEntrega = "";
$sitioEntrega = "";
$anexos = "";
$observaciones = "";
$enviadoEnFecha = "";

$attachList = array();
$rj = 0;
for($ri = 0; $ri < $numeroFilasProveedores; $ri++) {
	$rowProveedores = pg_fetch_array($resultadoProveedores, $ri);
	$nombreProveedor = $rowProveedores["prov_nombre"]." (Rif ".strtoupper(substr(trim($rowProveedores["prov_id_rif"]),0,1))."-".substr(trim($rowProveedores["prov_id_rif"]),1).")<br/>";
	$nombreProveedorSinBr = $rowProveedores["prov_nombre"]." (Rif ".strtoupper(substr(trim($rowProveedores["prov_id_rif"]),0,1))."-".substr(trim($rowProveedores["prov_id_rif"]),1).")";
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
	$emailEmpleado = $rowProveedores["empl_email"];
	$emailProveedor = $rowProveedores["prov_email"];

	$contenidoProveedor .=	"<p class='titulo'>Solicitud de Cotizaci&oacute;n</p><br/>".
					"<table width='1000px'>";
	
	if($rebms_tipo==TIPO_REQUISICION_COMPRA){
		$tituloTabla = "Compra requerida";
	}else if($rebms_tipo==TIPO_REQUISICION_SERVICIO){
		$tituloTabla = "Servicio requerido";
	}
	
	$contenidoProveedor .=		"<tr>".
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
								"<div class='valorCampo'>Tlf./Fax: </div>".
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
		$contenidoProveedor .=		"<td class='bordeTabla' width='400px'>Compra</td>";		
	}else if($rebms_tipo==TIPO_REQUISICION_SERVICIO){
		$contenidoProveedor .=		"<td class='bordeTabla' width='400px'>Servicio</td>";
	}
	$contenidoProveedor .=			"<td class='bordeTabla' width='100px'>Cantidad</td>".
							"<td class='bordeTabla' width='500px'>Especificaciones</td>".
						"</tr>";
	
	$resultado = pg_exec($conexion, "SELECT si.nombre, ssci.cantidad, ssci.especificaciones AS descripcion ".
									"FROM sai_sol_coti_item ssci, sai_item si ".
									"WHERE ".
										"ssci.soco_id = '".$codido_soco."' AND ".
										"ssci.fecha = '".$date."' AND ".
										"ssci.id_item = si.id ".
									"ORDER BY si.nombre ASC");
	
	$numeroFilas = pg_numrows($resultado);
	
	if($numeroFilas>0){
		$i=0;
		while($row=pg_fetch_array($resultado))  {
			$contenidoProveedor .="<tr>".
							"<td class='bordeTabla'>".$row["nombre"]."</td>".
							"<td class='bordeTabla' align='center'>".$row["cantidad"]."</td>".
							"<td class='bordeTabla'>".(($row["descripcion"]!="no")?$row["descripcion"]:"")."</td>".
						"</tr>";
			$i++;
		}
	}
	$contenidoProveedor .=	"</table>".
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

	$nombreBeneficiario = "FUNDACIÓN INFOCENTRO";
	
	$contenidoProveedor .=		"<tr>".
							"<td class='nombreCampo'>Beneficiario:</td>".
							"<td><div class='valorCampo'>".utf8_decode($nombreBeneficiario." (RIF ".strtoupper(substr(trim($_REQUEST["beneficiario"]),0,1))."-".substr(trim($_REQUEST["beneficiario"]),1).")")."</div></td>".
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
	$contenido .= $contenidoProveedor;
	
	/*
	 * 
	 * 	if(isset($_SESSION['SafiRequestVars']['nameFile'])){

		 
			$i = 0;
	 	foreach ( $_SESSION['SafiRequestVars']['nameFile'] as $index => $valor){
	 		
	 		

	 		$targetFolder = SAFI_UPLOADS_PATH.'/pcta/'.$valor;
	 		$tempFile =  SAFI_TMP_PATH.'/'. $valor;
	 		copy($tempFile,$targetFolder);
	    
	 		$params['Digital'][] = $valor;

	 	}
	 
	 }
	 * 
	 * */
	
	
	
	if($emailProveedor!=null && $emailProveedor!=""){
		$filename=PDF_PATH_PRE.$rowProveedores["prov_id_rif"].PDF_PATH_POS;
		$properties = array("path" => $filename, "footerHtml" => $footer);
		convert_to_pdf($estilos.$contenidoProveedor, $properties);
		$attachList[$rj] = array(6);
		$adjuntos[] = array();

		/*Archivos/nombre
		$attachList[$rj][0] = $filename;
		$attachList[$rj][1] = "solicitud_cotizacion".$rowProveedores["prov_id_rif"].".pdf";
		Fin archivos*/
		$adjuntos[0][0]= $filename;
		$adjuntos[0][1]= "solicitud_cotizacion".$rowProveedores["prov_id_rif"].".pdf";
		
		$i=1;
		// esto no se esta llenando $_SESSION['SafiRequestVars']['nameFile']
		if($_SESSION['SafiRequestVars']['nameFile']){
		foreach ($_SESSION['SafiRequestVars']['nameFile'] as $index => $valor){
			$adjuntos[$i][0]= SAFI_UPLOADS_PATH."/soco/".$valor;
			$adjuntos[$i][1]= $valor;
			$i++;
		}
		}
		//$attachList[$rj][0] =  SAFI_UPLOADS_PATH."/soco/".$_SESSION['SafiRequestVars']['nameFile'][0];		
		//$attachList[$rj][1] = $_SESSION['SafiRequestVars']['nameFile'][0];
		$attachList[$rj][0] =  $adjuntos;
		$attachList[$rj][1] = $rowProveedores["prov_id_rif"];
		$attachList[$rj][2] = $emailProveedor;
		$attachList[$rj][3] = $nombreProveedorSinBr;
		
		$rj++;
	}
	
	if(($ri+1)<$numeroFilasProveedores){
		$contenido .="<!--NewPage-->";
	}
	$contenidoProveedor = "";
}

$numeroFilas = sizeof($attachList);
if($numeroFilas>0){
	require("../../includes/funciones.php");
	$de = "compras@infocentro.gob.ve";
	$nombreDe = utf8_decode("Fundación Infocentro");
	//$de = "sistemas@infocentro.gob.ve";
	$copia = "";
	$nombreCopia = "";
	$copiaOculta = "";
	$nombreCopiaOculta = "";
	
	/*$copia = "compras@infocentro.gob.ve";
	$nombreCopia = utf8_decode("Fundación Infocentro - Compras");*/
	
	/*
	$copiaOculta = "sistemas@infocentro.gob.ve";
	$nombreCopiaOculta = utf8_decode("Fundación Infocentro - Sistemas");
	*/
	
	$message = wordwrap($cuerpo, 70);
	$ri = 0;
	while($ri<$numeroFilas){
		$attachListProveedor = array();
		$attachListProveedor[0] = $attachList[$ri];
		// Descomentar para producción
		/*
		$para = $attachListProveedor[0][2];//Email del proveedor
		$nombrePara=utf8_decode($attachListProveedor[0][3]);//Nombre del proveedor
		*/
		
		// Comentar para producción
		$para = "sistemas@infocentro.gob.ve,ecastillo@infocentro.gob.ve,wmendoza@infocentro.gob.ve";//Email del proveedor
		$nombrePara = "Sistemas";
		
		
		$email2 = explode(",", $para);
		foreach($email2 as $index => $valor)
		{
			enviarEmail($de, $nombreDe, $email2[$index], $nombrePara, $copia, $nombreCopia, $copiaOculta, $nombreCopiaOculta, $asunto." ".$idRequ, $message, $attachListProveedor);
		}
		$ri++;
	}
}
pg_close($conexion);

$footer = 	"<span style='align=center;font-family: arial;font-style:italic;font-weight:bold;font-size: 10pt;'>SAFI - Fundación Infocentro</span><br/>".
			"<span style='align=center;font-family: arial;font-size: 10pt;'>".fecha()."</span>";
$properties = array("footerHtml" => $footer);
unset($_SESSION['SafiRequestVars']['nameFile']);
convert_to_pdf($contenido, $properties);
?>