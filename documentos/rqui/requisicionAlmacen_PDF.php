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

$idRequ = "";
if (isset($_GET['idRequ']) && $_GET['idRequ'] != "") {
	$idRequ = $_GET['idRequ'];
}

if($idRequ && $idRequ!=""){
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
					"observaciones_almacen, ".
					"esta_nombre, ".
					"depe_nombre, ".
					"pcta_id ".
				"FROM sai_req_bi_ma_ser srbms, sai_estado se, sai_dependenci sd ".
				"WHERE ".
					"srbms.rebms_id = '".$idRequ."' AND ".
					"srbms.esta_id = se.esta_id AND ".
					"srbms.depe_id = sd.depe_id ";
	
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
	$observaciones_almacen = $row["observaciones_almacen"];
	$esta_nombre = $row["esta_nombre"];
	$depe_nombre = $row["depe_nombre"];
	$pcta_id = $row["pcta_id"];
	
	if($rebms_tipo_imputa==TIPO_IMPUTACION_PROYECTO){//Proyecto
		$query = "SELECT proy_titulo FROM sai_proyecto WHERE proy_id = '".$rebms_imp_p_c."'";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$proy_titulo = $row["proy_titulo"];
		$query = "SELECT paes_nombre,centro_gestor,centro_costo FROM sai_proy_a_esp WHERE proy_id = '".$rebms_imp_p_c."' AND paes_id = '".$rebms_imp_esp."'";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$aces_nombre = $row["paes_nombre"]."(".$row["centro_gestor"]."-".$row["centro_costo"].")";
	}else if($rebms_tipo_imputa==TIPO_IMPUTACION_ACCION_CENTRALIZADA){//Accion centralizada
		$query = "SELECT aces_nombre,centro_gestor,centro_costo FROM sai_acce_esp WHERE acce_id = '".$rebms_imp_p_c."' AND aces_id = '".$rebms_imp_esp."'";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$aces_nombre = $row["aces_nombre"]."(".$row["centro_gestor"]."-".$row["centro_costo"].")";
		$proy_titulo = $rebms_imp_p_c."-".$row["aces_nombre"];
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
	
	$contenido .="<p class='titulo'>Solicitud de Requisici&oacute;n</p>";
	
	$contenido .="<table>";
	
	$contenido .="<tr>";
	$contenido .="<td class='nombreCampo'>Procedencia:</td>";
	$contenido .="<td><div class='valorCampo'>".$depe_nombre."</div></td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td class='nombreCampo'>C&oacute;digo de Requisici&oacute;n:</td>";
	$contenido .="<td><div class='valorCampo'>".$idRequ."</div></td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td class='nombreCampo'>Tipo de requisici&oacute;n:</td>";
	$contenido .="<td><div class='valorCampo'>".(($rebms_tipo == TIPO_REQUISICION_MATERIALES)?"Materiales":(($rebms_tipo == TIPO_REQUISICION_BIENES)?"Bienes":(($rebms_tipo == TIPO_REQUISICION_SERVICIOS)?"Servicios":"")))."</div></td>";
	$contenido .="</tr>";

	$contenido .="<tr>";
	$contenido .="<td class='nombreCampo'>C&oacute;digo Proyecto o Acci&oacute;n Centralizada:</td>";
	$contenido .="<td><div class='valorCampo'>".$rebms_imp_p_c."</div></td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td class='nombreCampo'>Denominaci&oacute;n Proyecto o Acci&oacute;n Centralizada:</td>";
	$contenido .="<td><div class='valorCampo'>".$proy_titulo."</div></td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td class='nombreCampo'>Acci&oacute;n Espec&iacute;fica:</td>";
	$contenido .="<td><div class='valorCampo'>".$rebms_imp_esp."</div></td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td class='nombreCampo'>Denominaci&oacute;n Acci&oacute;n Espec&iacute;fica:</td>";
	$contenido .="<td><div class='valorCampo'>".$aces_nombre."</div></td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td class='nombreCampo'>Punto de Cuenta:</td>";
	$contenido .="<td><div class='valorCampo'>".(($pcta_id!="")?$pcta_id:"no aplica")."</div></td>";
	$contenido .="</tr>";
	
	if($rebms_tipo==TIPO_REQUISICION_MATERIALES){
		$tituloTabla = "Material requerido";		
	}else if($rebms_tipo==TIPO_REQUISICION_BIENES){
		$tituloTabla = "Bien requerido";
	}else if($rebms_tipo==TIPO_REQUISICION_SERVICIOS){
		$tituloTabla = "Servicio requerido";
	}
	
	$contenido .="<tr>";
	$contenido .="<td colspan='2'>&nbsp;</td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td class='nombreCampo' colspan='2'><div class='valorCampo'>".$tituloTabla.":</div></td>";
	$contenido .="</tr>";
	
	$contenido .="</table>";
	
	$contenido .="<table class='bordeTabla' cellspacing='0' cellpadding='0'>";
	$contenido .="<tr style='background-color: #45459F; color:#FFFFFF; text-align: center;'>";
	$contenido .="<td class='bordeTabla' width='250px'>Partida</td>";
	if($rebms_tipo==TIPO_REQUISICION_MATERIALES){
		$contenido .="<td class='bordeTabla' width='250px'>Art&iacute;culo</td>";		
	}else if($rebms_tipo==TIPO_REQUISICION_BIENES){
		$contenido .="<td class='bordeTabla' width='250px'>Bien</td>";
	}
	$contenido .="<td class='bordeTabla' width='100px'>Cantidad</td>";
	$contenido .="<td class='bordeTabla' width='100px'>Salida</td>";
	$contenido .="<td class='bordeTabla' width='300px'>Descripci&oacute;n</td>";
	$contenido .="</tr>";
	
	if($rebms_tipo==TIPO_REQUISICION_MATERIALES){
		$resultado = pg_exec($conexion, "SELECT DISTINCT sa.arti_descripcion as nombre, sri.rbms_item_cantidad as cantidad, sri.cantidad_almacen, sri.rbms_item_desc as descripcion, sp.part_nombre as nombre_partida ".
										"FROM sai_rqui_items sri, sai_articulo sa, sai_arti_part_anno sapa, sai_partida sp ".
										"WHERE ".
											"sri.rebms_id = '".$idRequ."' AND ".
											"sri.rbms_item_arti_id = sa.arti_id AND ".
											"sri.rbms_item_arti_id = sapa.arti_id AND ".
											"sapa.part_id = sp.part_id AND ".
											"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ".
										"ORDER BY sa.arti_descripcion ASC");		
	}else if($rebms_tipo==TIPO_REQUISICION_BIENES){
		$resultado = pg_exec($conexion, "SELECT DISTINCT sb.bien_nombre as nombre, sri.rbms_item_cantidad as cantidad, sri.cantidad_almacen, sri.rbms_item_desc as descripcion, sp.part_nombre as nombre_partida ".
										"FROM sai_rqui_items sri, sai_bienes sb, sai_bien_part_anno sbpa, sai_partida sp ".
										"WHERE ".
											"sri.rebms_id = '".$idRequ."' AND ".
											"sri.rbms_item_arti_id = sb.bien_id AND ".
											"sri.rbms_item_arti_id = sbpa.bien_id AND ".
											"sbpa.part_id = sp.part_id AND ".
											"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ".
										"ORDER BY sb.bien_nombre ASC");
	}
	
	$numeroFilas = pg_numrows($resultado);
	
	if($numeroFilas>0){
		$i=0;
		while($row=pg_fetch_array($resultado))  {
			$contenido .="<tr>";
			$contenido .="<td class='bordeTabla'>".$row["nombre_partida"]."</td>";
			$contenido .="<td class='bordeTabla'>".$row["nombre"]."</td>";
			$contenido .="<td class='bordeTabla' align='center'>".$row["cantidad"]."</td>";
			$contenido .="<td class='bordeTabla' align='center'>".(($row["cantidad_almacen"] && $row["cantidad_almacen"]!="" && $row["cantidad_almacen"]!="0")?((int)$row["cantidad"]-(int)$row["cantidad_almacen"]):$row["cantidad"])."</td>";
			$contenido .="<td class='bordeTabla'>".$row["descripcion"]."</td>";
			$contenido .="</tr>";
			$i++;
		}
	}

	$contenido .="</table>";
	
	$contenido .="<br/><table>";	
	
	if($rebms_observaciones && $rebms_observaciones!=""){
		$contenido .="<tr>";
		$contenido .="<td class='nombreCampo'>Observaciones:</td>";
		$contenido .="<td>".$rebms_observaciones."</td>";
		$contenido .="</tr>";		
	}
	
	if($observaciones_almacen && $observaciones_almacen!=""){
		$contenido .="<tr>";
		$contenido .="<td class='nombreCampo'>Observaciones de Almac&eacute;n:</td>";
		$contenido .="<td>".$observaciones_almacen."</td>";
		$contenido .="</tr>";		
	}
	$contenido .="</table>";
	
	convert_to_pdf($contenido);	
}
pg_close($conexion); ?>