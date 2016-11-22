<?php
ob_start();
require("../../includes/conexion.php");
require("../../includes/constantes.php");
require("../../includes/monto_a_letra.php");
if	( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}

require("../../includes/reporteBasePdf.php"); 
require("../../includes/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require("../../includes/html2ps/funciones.php");

$codigo = "";
if (isset($_GET['codigo']) && $_GET['codigo'] != "") {
	$codigo = $_GET['codigo'];
	$longitud_comp=strlen($codigo);
    $anno_pres="20".substr($codigo,$longitud_comp-2);
}

if($codigo && $codigo!=""){

	$query =	"SELECT ".
					"sc.esta_id, ".
					"sc.comp_id, ".
	                "cit.nombre as control_interno, ".
					"sc.pcta_id, ".
					"se.empl_nombres || ' ' || se.empl_apellidos as elaborado, ".
					"sd.depe_nombre, ".
					"to_char(sc.comp_fecha,'DD/MM/YYYY') as comp_fecha, ".
					"sc.comp_observacion, ".
					"sc.comp_descripcion, ".
					"sc.comp_justificacion, ".
					"sc.comp_monto_solicitado, ".
					"sc.comp_lapso, ".
					"sc.comp_cond_pago, ".
					"scas.cpas_nombre as asunto, ".
					"sc.rif_sugerido,comp_documento,localidad,id_actividad, ".
					"sc.usua_login, ".
					"sc.comp_estatus, ".
					"sc.id_evento, ".
					"to_char(sc.fecha_inicio,'DD/MM/YYYY') as fecha_inicial, ".
					"to_char(sc.fecha_fin,'DD/MM/YYYY') as fecha_final, ".
					"to_char(sc.fecha_reporte,'DD/MM/YYYY') as fecha_reporte, beneficiario ".
				"FROM sai_comp sc, sai_cargo sca, sai_dependenci sd, sai_compromiso_asunt scas, sai_empleado se,sai_control_comp cit ".
				"WHERE ".
					"sc.comp_id=trim('".$codigo."') AND ".
					"trim(sc.comp_asunto)=trim(scas.cpas_id) AND ".
					"sc.comp_gerencia=sd.depe_id AND ".
	                "sc.control_interno = cit.id AND ".
					"sc.usua_login=se.empl_cedula";

	$resultado=pg_query($conexion,$query);
	$row=pg_fetch_array($resultado);
	$elaborado=$row["elaborado"];
	$comp_fecha=$row["comp_fecha"];
	$controlinterno=$row["control_interno"];
	$observacion=$row["comp_observacion"];
	$descripcion=$row["comp_descripcion"];
	$justificacion=$row["comp_justificacion"];
	$lapso=$row['comp_lapso'];
	$condicion=$row['comp_cond_pago'];
	$monto=$row["comp_monto_solicitado"];
	$pcta_id=$row["pcta_id"];
	$asunto=$row["asunto"];
	$depe_nombre=$row["depe_nombre"];
	$usua_login=$row['usua_login'];
	$rif_sugerido=$row["rif_sugerido"];
	$nombre_proveedor_sugerido=$row["beneficiario"];
	$esta_id=$row['esta_id'];
	$documento=$row['comp_documento'];
    $ubicacion=$row['localidad'];
    $actividad=$row['id_actividad'];
    $estatus=$row['comp_estatus'];
    $fecha_reporte=$row["fecha_reporte"];
    $evento=$row['id_evento'];
    $fecha_i=$row['fecha_inicial'];
    $fecha_f=$row['fecha_final'];
    
    $sql_memo="SELECT memo_contenido FROM sai_docu_sopor,sai_memo WHERE doso_doc_fuente='".$codigo."' and
    doso_doc_soport=memo_id";
    $resultado_memo=pg_query($conexion,$sql_memo);
    if ($row_memo=pg_fetch_array($resultado_memo)){
    	$contenido_memo=$row_memo['memo_contenido'];
    }
    
    $sql_asu = "SELECT * FROM sai_tipo_actividad where id='".$actividad."'";					
	$result=pg_query($conexion,$sql_asu);
	if($row=pg_fetch_array($result))	{
	 $tipo_act=$row['nombre'];
	}  		 

	    
    $sql_asu = "SELECT * FROM sai_tipo_evento where id='".$evento."'";					
	$result=pg_query($conexion,$sql_asu);
	if($row=pg_fetch_array($result))	{
	 $tipo_evento=$row['nombre'];
	}  	
	
	$pcta_asunto=substr($asunto,0,7);

      $sql= " Select * from sai_seleccionar_campo('sai_comp_imputa','comp_monto,comp_acc_pp,comp_acc_esp','comp_id='||'''$codigo''','',2) as resultado_set ";
	  $sql.= " (comp_monto float,comp_acc_pp varchar,comp_acc_esp varchar)";

	  $sql_imp= " Select * from sai_seleccionar_campo('sai_comp_imputa','comp_monto,comp_acc_pp,comp_acc_esp','comp_id='||'''$codigo''','',2) as resultado_set ";
	  $sql_imp.= " (comp_monto float)";
	  $resultado_set= pg_exec($conexion ,$sql);
	  $valido=$resultado_set;
	
		if ($resultado_set)
  		{
		$monto_compromiso=0;
		while($row=pg_fetch_array($resultado_set))	
		 {
			$monto_compromiso=$monto_compromiso+$row['comp_monto']; 
			$proy_acc=$row['comp_acc_pp'];
			$acc_esp=$row['comp_acc_esp'];
   		}}
   		
   	  $sql_fte= " Select * from sai_seleccionar_campo('sai_forma_1125','fuente_financiamiento','esta_id=1 and form_id_p_ac='||'''$proy_acc'' and form_id_aesp='||'''$acc_esp''','',2) as resultado_set ";
	  $sql_fte.= " (fuente_financiamiento varchar)";
	  $resultado_set_fte= pg_exec($conexion ,$sql_fte);
		
	  if($row_fte=pg_fetch_array($resultado_set_fte))	
		{
			$fuente=$row_fte['fuente_financiamiento'];
   		}
   		
   		
	$contenido = "<style type='text/css'>
						.nombreCampo{
							font-weight: bold; 
							font-size: 22px; 
							font-family: arial; 
							TEXT-DECORATION: none
						}
						.bordeTabla{
							border: solid 1px #000000;
						}
						.textoTabla{
							font-weight: normal; 
							font-size: 22px; 
							font-family: arial; 
							TEXT-DECORATION: none
						}
					</style>";
	
	$contenido .="<table width='1000px' class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0'>";
	
	$contenido .="<tr>";
	$contenido .="<td colspan='4' align='center'><span class='nombreCampo'>COMPROMISO ".$codigo."</span></td>";
	$contenido .="</tr>";

	if($esta_id==15){	
		$contenido .="<tr>";
		$contenido .="<td colspan='4'><div align='center'><font color='Red'><strong>ANULADO</strong></div></td>";
		$contenido .="</tr>";
	}
	if ($pcta_id=="0"){
		$pcta_id="N/A";
	}
	$contenido .="<tr>";
	$contenido .="<td colspan='4'><span class='nombreCampo'>Fecha: ".$comp_fecha."</span></td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td colspan='4'><span class='nombreCampo'>Elaborado por:</span> ".$elaborado."</td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td colspan='4'><span class='nombreCampo'>UNIDAD/DEPENDENCIA:</span> ".$depe_nombre."</td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td colspan='2'><span class='nombreCampo'>Punto de cuenta:</span> ".$pcta_id."</td>";
	$contenido .="<td colspan='2'><span class='nombreCampo'>Fuente de Financiamiento:</span> ".$fuente."</td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td colspan='4'><span class='nombreCampo'>Estatus:</span> ".$estatus."</td>";
	$contenido .="</tr>";
	
	if($esta_id==15){	
	$contenido .="<tr>";
	$contenido .="<td colspan='4'><span class='nombreCampo'>Motivo Anulaci&oacute;n:</span> ".$contenido_memo."</td>";
	$contenido .="</tr>";
	}
	$contenido .="<tr>";
	$contenido .="<td colspan='4' align='center'><span class='nombreCampo'>Elementos del compromiso</span></td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td colspan='4'><span class='nombreCampo'>Asunto:</span> ".$asunto."</td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td colspan='4'><span class='nombreCampo'>Control interno:</span> ".$controlinterno."</td>";
	$contenido .="</tr>";
	
	
	
	
	$contenido .="<tr>";
	$contenido .="<td colspan='4'><span class='nombreCampo'>Rif del Proveedor Sugerido:</span> ".$rif_sugerido.":".$nombre_proveedor_sugerido."</td>";
	$contenido .="</tr>";

	$contenido .="<tr>";
	$contenido .="<td colspan='4'><span class='nombreCampo'>Tipo Actividad:</span> ".$tipo_act."</td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td colspan='4'><span class='nombreCampo'>Tipo Evento:</span> ".$tipo_evento."</td>";
	$contenido .="</tr>";

	$contenido .="<tr>";
	$contenido .="<td colspan='4'><span class='nombreCampo'>Duraci&oacute;n de la actividad:</span> ".$fecha_i."-".$fecha_f."</td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td colspan='4'><span class='nombreCampo'>C&oacute;digo Documento:</span> ".$documento."</td>";
	$contenido .="</tr>";
	$contenido .="<tr>";
	$contenido .="<td colspan='4'>".
					"<span class='nombreCampo'>Descripci&oacute;n:</span> ".$descripcion."<br/><br/>";
	if (strlen($fecha_reporte)>1){
	$contenido .=	"<span class='nombreCampo'>Fecha de Reporte:</span> ".$fecha_reporte;
	}
				
	$contenido .="</td></tr>";
	if (($ubicacion<>'') && ($ubicacion<>'0')){
	$sql_asu = "SELECT * FROM safi_edos_venezuela where id='".$ubicacion."'";	
	$result=pg_query($conexion,$sql_asu);
	if($row=pg_fetch_array($result))	{
	 $nombre_ubicacion= $row['nombre'];
	}  		
	
	$contenido .="<tr>";
	$contenido .="<td colspan='4'><span class='nombreCampo'>Ubicaci&oacute;n Infocentro:</span> ".$nombre_ubicacion."</td>";
	$contenido .="</tr>";
	}
	$contenido .="<tr>";
	$contenido .="<td colspan='4'><span class='nombreCampo'>Observaci&oacute;n:</span> ".$observacion."</td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	if ($monto_compromiso<0){
		$prefijo="menos ";
		$prefijo2="-";
		$monto_compromiso=$monto_compromiso*(-1);
	}
	$montoletras=monto_letra($monto_compromiso, " BOLIVARES");
	$contenido .="<td colspan='4'><span class='nombreCampo'>Monto solicitado:</span> El monto total es de ".$prefijo.$montoletras." (BS. F. ".$prefijo2.(number_format($monto_compromiso,2,'.',',')).")</td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td colspan='4' align='center'><span class='nombreCampo'>Datos de imputaci&oacute;n presupuestaria</span></td>";
	$contenido .="</tr>";
	
	$query	=	"SELECT ".
					"sci.comp_monto, ".
					"sci.comp_sub_espe, ".
		            "sci.comp_tipo_impu, ".
					"sae.aces_nombre, ".
					"sae.centro_gestor as aces_centro_gestor, ".
					"sae.centro_costo as aces_centro_costo, ".
					"spae.paes_nombre, ".
					"spae.centro_gestor as proy_centro_gestor, ".
					"spae.centro_costo as proy_centro_costo, ".
					"sac.acce_denom, ".
					"sp.proy_titulo ".
				"FROM ".
					"sai_comp_imputa sci ".
					"left outer join sai_acce_esp sae on (sci.comp_acc_pp=sae.acce_id and sci.comp_acc_esp=sae.aces_id and sae.pres_anno='".$anno_pres."' ) ".
					"left outer join sai_ac_central sac on (sae.acce_id=sac.acce_id and sac.pres_anno='".$anno_pres."') ".
					"left outer join sai_proy_a_esp spae on (sci.comp_acc_pp=spae.proy_id and sci.comp_acc_esp=spae.paes_id and spae.pres_anno='".$anno_pres."' ) ".
					"left outer join sai_proyecto sp on (spae.proy_id=sp.proy_id and sp.pre_anno='".$anno_pres."' ) ".
				"WHERE ".
					"sci.comp_id='".$codigo."' AND sci.pres_anno='".$anno_pres."' order by comp_sub_espe";

	$result=pg_query($conexion,$query);
	while($row=pg_fetch_array($result)){
			
	$comp_monto=$row['comp_monto'];
	$comp_sub_espe=$row["comp_sub_espe"];
	$aces_nombre=$row["aces_nombre"];
	$aces_centro_gestor=$row["aces_centro_gestor"];
	$aces_centro_costo=$row["aces_centro_costo"];
	$paes_nombre=$row["paes_nombre"];
	$proy_centro_gestor=$row["proy_centro_gestor"];
	$proy_centro_costo=$row["proy_centro_costo"];
	$acce_denom=$row["acce_denom"];
	$proy_titulo=$row["proy_titulo"];
	$tipo_p_ac=$row['comp_tipo_impu'];


		$comp_monto=$row['comp_monto'];
		$comp_sub_espe=$row["comp_sub_espe"];
		$contenido .="<tr>";
		
		if ($tipo_p_ac==1){
			$cgestor=$proy_centro_gestor;
			$ccosto=$proy_centro_costo;
		}else{
			$cgestor=$aces_centro_gestor;
			$ccosto=$aces_centro_costo;
		}
		$contenido .="<td width='20%'><span class='nombreCampo'>PP/ACC:</span> ".$cgestor."</td>";
		$contenido .="<td width='25%'><span class='nombreCampo'>Acci&oacute;n Esp.:</span> ".$ccosto."</td>";
		$contenido .="<td width='25%'><span class='nombreCampo'>Partida:</span> ".$comp_sub_espe."</td>";
		$contenido .="<td width='35%'><span class='nombreCampo'>Monto BsF.:".(number_format($comp_monto,2,'.',','))."</span></td>";
		$contenido .="</tr>";
	}
	
	$contenido .="<br/><br/><br/><br/><br/>";
	
	/*if ($usua_login=="14196354"){
	$contenido .="<tr>";
	$contenido .="<td align='center' colspan='4'><span class='nombreCampo'>FIRMAS</span></td></tr>";
	$contenido .="<tr><td align='center' colspan='4' width='360'><span class='nombreCampo'><br>____________________<br/><br/>".utf8_decode('OFICINA DE PLANIFICACIÓN PRESUPUESTO Y CONTROL')."</span></td>";	
	}else{*/
	$contenido .="<tr>";
	$contenido .="<td align='center' colspan='4'><span class='nombreCampo'>FIRMAS</span></td></tr>";
	$contenido .="<tr><td align='center' colspan='2'><span class='nombreCampo'>Registrado por:<br><br><br>____________________<br/><br/>".utf8_decode("Analista de Planificación, Presupuesto y Control")."<br><br></span></td>";
	$contenido .="<td align='center' colspan='2' ><span class='nombreCampo'>Aprobado por:<br><br><br>____________________<br/><br/>Jefe/Director de Planificaci&oacute;n, Presupuesto y Control</span></td>";

	//}
	$contenido .="</tr>";
	$contenido .="</table>";
	ob_clean();
	convert_to_pdf($contenido);
	
	
}
pg_close($conexion);
?>
