<?php 
	ob_start();
	session_start();
	if (isset($_POST['tipoRequ']) && $_POST['tipoRequ'] != "") {
		header("Content-Type: text/html; charset=iso-8859-1\n");
		$tipoRequ = $_POST['tipoRequ'];
	}else{
		$tipoRequ = $tipoRequisicion;
	}
	
	$estadoTransito = "10";
	$estadoDevuelto = "7";
	$estadoAprobado = "13";
	$estadoAnulado = "15";
	$estadoBorrador = "60";
	
	$queryDependencia = "";
	$queryUsuario = "";
	$queryCadenaPorDefecto =	"rebms_id = sdg.docg_id AND ".
								"sdg.wfca_id = swfc.wfca_id AND ".
								"swfc.wfgr_id = swfg.wfgr_id AND ".
								"swfc.wfca_id_hijo = swfch.wfca_id AND ".
								"swfch.wfgr_id = swfgh.wfgr_id ";
	$fromCadena = ", sai_doc_genera sdg, sai_wfcadena swfc, sai_wfgrupo swfg, sai_wfcadena swfch, sai_wfgrupo swfgh ";
	$queryCadena = "";
	if(substr($user_perfil_id, 0, 2)=="37"){//ASISTENTE ADMINISTRATIVO
		$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
					"WHERE ".
						"(swfg.wfgr_perf = '".substr($user_perfil_id, 0, 2)."000' ".
						"OR swfg.wfgr_perf like '".substr($user_perfil_id, 0, 2)."000%' ".
						"OR swfg.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000' ".
						"OR swfg.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000%') ";		
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$idGrupo = $row[0];
		
		$queryDependencia = "srbms.depe_id = '".$_SESSION['user_depe_id']."' ";
		$queryUsuario = "srbms.usua_login = '".$_SESSION['login']."' ";
	}else if($user_perfil_id == "38350" || $user_perfil_id == "68150"){//ASISTENTE EJECUTIVO, SECRETARIA PRESIDENCIA
		$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
					"WHERE ".
						"(swfg.wfgr_perf = '".$user_perfil_id."' ".
						"OR swfg.wfgr_perf like '".$user_perfil_id."%' ".
						"OR swfg.wfgr_perf like '%/".$user_perfil_id."' ".
						"OR swfg.wfgr_perf like '%/".$user_perfil_id."%') ";		
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$idGrupo = $row[0];
		
		$queryDependencia = "srbms.depe_id = '".$_SESSION['user_depe_id']."' ";
		$queryUsuario = "srbms.usua_login = '".$_SESSION['login']."' ";		
	}else if(substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46"){//GERENTE O DIRECTOR
		$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
					"WHERE ".
						"(swfg.wfgr_perf = '".substr($user_perfil_id, 0, 2)."000' ".
						"OR swfg.wfgr_perf like '".substr($user_perfil_id, 0, 2)."000%' ".
						"OR swfg.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000' ".
						"OR swfg.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000%') ";		
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$idGrupo = $row[0];
		$queryDependencia = "srbms.depe_id = '".$_SESSION['user_depe_id']."' ";
		if((!$codigo || $codigo=="") && $estado==ESTADO_REQUISICION_NO_REVISADAS){
			$queryCadena =	"(swfgh.wfgr_perf = '".substr($user_perfil_id, 0, 2)."000' ".
							"OR swfgh.wfgr_perf like '".substr($user_perfil_id, 0, 2)."000%' ".
							"OR swfgh.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000' ".
							"OR swfgh.wfgr_perf like '%/".substr($user_perfil_id, 0, 2)."000%') ";
		}
	}else if($user_perfil_id == "47350" || $user_perfil_id == "65150"){//DIRECTOR EJECUTIVO, PRESIDENTE
		$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
					"WHERE ".
						"(swfg.wfgr_perf = '".$user_perfil_id."' ".
						"OR swfg.wfgr_perf like '".$user_perfil_id."%' ".
						"OR swfg.wfgr_perf like '%/".$user_perfil_id."' ".
						"OR swfg.wfgr_perf like '%/".$user_perfil_id."%') ";		
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$idGrupo = $row[0];
		$queryDependencia = "srbms.depe_id = '".$_SESSION['user_depe_id']."' ";
		if((!$codigo || $codigo=="") && $estado==ESTADO_REQUISICION_NO_REVISADAS){
			$queryCadena =	"(swfgh.wfgr_perf = '".$user_perfil_id."' ".
							"OR swfgh.wfgr_perf like '".$user_perfil_id."%' ".
							"OR swfgh.wfgr_perf like '%/".$user_perfil_id."' ".
							"OR swfgh.wfgr_perf like '%/".$user_perfil_id."%') ";
		}
	}else if($user_perfil_id == "30400"){//ANALISTA DE PRESUPUESTO
		$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
					"WHERE ".
						"swfg.wfgr_perf = '".$user_perfil_id."' ";
		
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$idGrupo = $row["wfgr_id"];
		$vistoBueno = 6;
		if((!$codigo || $codigo=="") && $estado==ESTADO_REQUISICION_NO_REVISADAS){//NO REVISADA
			$queryCadena = 	"swfgh.wfgr_perf = '".$user_perfil_id."' AND ".
							"swfch.wfop_id=".$vistoBueno." ";
		}else if((!$codigo || $codigo=="") && $estado==ESTADO_REQUISICION_APROBADAS){
			$queryCadena = 	"swfg.wfgr_perf = '".$user_perfil_id."' AND ".
							"swfc.wfop_id=".$vistoBueno." ";
		}else if((!$codigo || $codigo=="") && ($estado==ESTADO_REQUISICION_DEVUELTAS_POR_USUARIO || $estado==ESTADO_REQUISICION_ANULADAS)){
			//ULTIMA REVISION DE DOCUMENTO SEA DEVOLUCION Y ECHA POR USUARIO.
			$opcionDevolver = "5";
			$opcionAnular = "24";
			$query = 	"SELECT srd.revi_doc ". 
						"FROM ".
							"sai_revisiones_doc srd, ".
							"(SELECT revi_doc, max(revi_fecha) AS revi_fecha FROM sai_revisiones_doc ".(($estado==ESTADO_REQUISICION_ANULADAS)?" WHERE wfop_id <> ".$opcionAnular." ":"")." GROUP BY revi_doc) AS s ".
						"WHERE ".
							"srd.revi_doc = s.revi_doc AND ".
							"srd.revi_fecha = s.revi_fecha AND ".
							"srd.usua_login = '".$_SESSION['login']."' AND ".
							"srd.perf_id = '".$_SESSION['user_perfil_id']."' AND ".
							"srd.wfop_id = ".$opcionDevolver;
			$resultado = pg_exec($conexion, $query);
			$numeroFilas = pg_numrows($resultado);
			if($numeroFilas>0){
				$revisiones = "(";
				for($i = 0; $i < $numeroFilas; $i++) {
		    		$row = pg_fetch_array($resultado, $i);
		    		$revisiones.= "'".$row["revi_doc"]."',";
				}
				$revisiones = substr($revisiones, 0, -1).")";
			}else{
				$revisiones = "('0')";
			}
			$queryCadena = 	"rebms_id IN ".$revisiones." ";
		}else if((!$codigo || $codigo=="") && $estado==ESTADO_REQUISICION_TODAS){
			$opcionDevolver = "5";
			$opcionAnular = "24";
			$query = 	"SELECT srd.revi_doc ". 
						"FROM ".
							"sai_revisiones_doc srd, ".
							"(SELECT revi_doc, max(revi_fecha) AS revi_fecha FROM sai_revisiones_doc WHERE wfop_id <> ".$opcionAnular." GROUP BY revi_doc) AS s ".
						"WHERE ".
							"srd.revi_doc = s.revi_doc AND ".
							"srd.revi_fecha = s.revi_fecha AND ".
							"srd.usua_login = '".$_SESSION['login']."' AND ".
							"srd.perf_id = '".$_SESSION['user_perfil_id']."' AND ".
							"srd.wfop_id = ".$opcionDevolver;
			$resultado = pg_exec($conexion, $query);
			$numeroFilas = pg_numrows($resultado);
			if($numeroFilas>0){
				$revisiones = "(";
				for($i = 0; $i < $numeroFilas; $i++) {
		    		$row = pg_fetch_array($resultado, $i);
		    		$revisiones.= "'".$row["revi_doc"]."',";
				}
				$revisiones = substr($revisiones, 0, -1).")";
			}else{
				$revisiones = "('0')";
			}
			$queryCadena = 		"((swfgh.wfgr_perf = '".$user_perfil_id."' AND ".
									"swfch.wfop_id=".$vistoBueno.") OR ".
								"(swfg.wfgr_perf = '".$user_perfil_id."' AND ".
									"swfc.wfop_id=".$vistoBueno.") OR ".
								"(rebms_id IN ".$revisiones.")) ";
		}
	}else if($user_perfil_id == "15456" || $user_perfil_id == "42456"){//ANALISTA DE COMPRAS, COORDINADOR DE COMPRAS
		/*$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
					"WHERE ".
						"swfg.wfgr_perf = '".$user_perfil_id."' ";*/
		$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
					"WHERE ".
						"(swfg.wfgr_perf = '".$user_perfil_id."' ".
						"OR swfg.wfgr_perf like '".$user_perfil_id."%' ".
						"OR swfg.wfgr_perf like '%/".$user_perfil_id."' ".
						"OR swfg.wfgr_perf like '%/".$user_perfil_id."%') ";		
		
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$idGrupo = $row["wfgr_id"];
		if((!$codigo || $codigo=="") && $estado==ESTADO_REQUISICION_PENDIENTES){
			/*$queryCadena = 	"swfgh.wfgr_perf = '".$user_perfil_id."' ";*/
			$queryCadena =	"(swfgh.wfgr_perf = '".$user_perfil_id."' ".
							"OR swfgh.wfgr_perf like '".$user_perfil_id."%' ".
							"OR swfgh.wfgr_perf like '%/".$user_perfil_id."' ".
							"OR swfgh.wfgr_perf like '%/".$user_perfil_id."%') ";		
		}
	}
	
	$paginaL = "";
	if($pagina){
		$paginaL = $pagina;
	}else{
		if (isset($_POST['pagina']) && $_POST['pagina'] != "") {
			$paginaL = $_POST['pagina'];
		}
	}
	$tamanoPagina = 12;
	$tamanoVentana = 20;
	
	$desplazamiento = ($paginaL-1)*$tamanoPagina;

	$query = 	"SELECT COUNT(rebms_id) ".
				"FROM sai_req_bi_ma_ser srbms ".$fromCadena." ".
				"WHERE ";
	if($codigo && $codigo!=""){
		$query .=		"rebms_id = '".$codigo."' AND ";
	}else{
		if($tipoRequ!=TIPO_REQUISICION_TODAS){
			$query .=	"rebms_tipo = ".$tipoRequ." AND ";
		}
		if($proyAcc == "true"){
			if($radioProyAcc=="proyecto"){
				$query .=	"rebms_tipo_imputa = '1' AND ";
				if($proyecto!=""){
					$query .=	"rebms_imp_p_c = '".substr($proyecto,0,strpos($proyecto,"-"))."' AND rebms_imp_esp = '".substr($proyecto,strpos($proyecto,"-")+1)."' AND ";	
				}
			}else if($radioProyAcc=="accionCentralizada"){
				$query .=	"rebms_tipo_imputa = '0' AND ";
				if($accionCentralizada!=""){
					$query .=	"rebms_imp_p_c = '".substr($accionCentralizada,0,strpos($accionCentralizada,"-"))."' AND rebms_imp_esp = '".substr($accionCentralizada,strpos($accionCentralizada,"-")+1)."' AND ";	
				}
			}
		}
		if($dependencia != ""){
			$query .=	"srbms.depe_id = '".$dependencia."' AND ";
		}
		if($estado==ESTADO_REQUISICION_PENDIENTES){
			$query .=	"rebms_id NOT IN (SELECT rebms_id FROM sai_sol_coti) AND ";
		}else if($estado==ESTADO_REQUISICION_ENVIADAS){
			$query .=	"rebms_id IN (SELECT rebms_id FROM sai_sol_coti WHERE usua_login = '".$_SESSION['login']."') AND ";
		}else if($estado==ESTADO_REQUISICION_ENVIADAS_POR_OTROS){
			$query .=	"rebms_id IN (SELECT rebms_id FROM sai_sol_coti WHERE usua_login <> '".$_SESSION['login']."') AND ";
		}else if($estado==ESTADO_REQUISICION_NO_REVISADAS && (substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150" || substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46" || $user_perfil_id == "47350" || $user_perfil_id == "65150")){
			$query .=	"srbms.esta_id = ".$estadoTransito." AND ";
		}else if($estado==ESTADO_REQUISICION_DEVUELTAS || $estado==ESTADO_REQUISICION_DEVUELTAS_POR_USUARIO){
			$query .=	"srbms.esta_id = ".$estadoDevuelto." AND ";
		}else if($estado==ESTADO_REQUISICION_APROBADAS){
			$query .=	"srbms.esta_id = ".$estadoAprobado." AND ";
		}else if($estado==ESTADO_REQUISICION_ANULADAS){
			$query .=	"srbms.esta_id = ".$estadoAnulado." AND ";
		}else if($estado==ESTADO_REQUISICION_EN_BORRADOR){
			$query .=	"srbms.esta_id = ".$estadoBorrador." AND ";
		}
		if($controlFechas=="true" && $fechaInicio!="" && $fechaFin!=""){
			$query .=	"rebms_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ";
		}
		if($idViatico != ""){
			$query .= "lower(srbms.vnac_id) = '".strtolower($idViatico)."' AND ";
		}
	}
	
	$query .= $queryCadenaPorDefecto.(($queryCadena!="")?" AND ".$queryCadena:"")." ".
			  (($queryDependencia!="")?" AND ".$queryDependencia:"")." ".
			  (($queryUsuario!="")?" AND ".$queryUsuario:"");

	$resultadoContador = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultadoContador, 0);
	$contador = $row[0];
	$totalPaginas = ($contador%$tamanoPagina == 0)?$contador/$tamanoPagina:intval($contador/$tamanoPagina)+1;
	$contadorCodigo = 0;
	if($codigo && $codigo!=""){
		$query = "SELECT COUNT(rebms_id) FROM sai_sol_coti WHERE rebms_id = '".$codigo."'";
		$resultadoContador = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultadoContador, 0);
		$contadorCodigo = $row[0];
		$query =	"SELECT ".
						"rebms_id, ".
						"to_char(rebms_fecha,'DD/MM/YYYY') as rebms_fecha_cadena, ".
						"rebms_fecha, ".
						"rebms_tipo, ".
						"imputacion_nombre ,".
						"srbms.esta_id, ".
						"esta_nombre, ".
						"depe_nombre ";
		if($contadorCodigo>0){
			$query.= ", srbms.usua_login, srbms.solicitante ";
		}
		$query.= ", swfch.wfop_id, swfgh.wfgr_id ";
		$query.=	"FROM ( ".
						"SELECT ".
							"srbms.rebms_id, ".
							"srbms.rebms_fecha, ".
							"srbms.rebms_tipo, ".
							"spae.centro_gestor || '/' || spae.centro_costo as imputacion_nombre, ".
							"srbms.esta_id, ".
							"se.esta_nombre, ".
							"sd.depe_nombre ";
		if($contadorCodigo>0){
			$query.= 		", ssc.usua_login ".
							", sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante ";
		}
		$query.=		"FROM sai_req_bi_ma_ser srbms,sai_dependenci sd,sai_estado se, sai_proy_a_esp spae ";
		if($contadorCodigo>0){
			$query.= ", sai_sol_coti ssc, sai_usuario su, sai_empleado sem ";
		}				
		$query.=		"WHERE ".
							"srbms.rebms_id = '".$codigo."' AND ";		
		if($contadorCodigo>0){
			$query .=		"srbms.rebms_id = ssc.rebms_id AND ".
							"ssc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula AND ";
		}
		$query .=			(($queryDependencia!="")?$queryDependencia." AND ":"").
							(($queryUsuario!="")?$queryUsuario." AND ":"").
							"srbms.depe_id = sd.depe_id AND ".
							"srbms.esta_id = se.esta_id AND ".
							"srbms.rebms_imp_p_c = spae.proy_id AND ".
							"srbms.rebms_imp_esp = spae.paes_id ".
						"UNION ".
						"SELECT ".
							"srbms.rebms_id, ".
							"srbms.rebms_fecha, ".
							"srbms.rebms_tipo, ".
							"sae.centro_gestor || '/' || sae.centro_costo as imputacion_nombre, ".
							"srbms.esta_id, ".
							"se.esta_nombre, ".
							"sd.depe_nombre ";
		if($contadorCodigo>0){
			$query.= 		", ssc.usua_login ".
							", sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante ";
		}
		$query.=		"FROM sai_req_bi_ma_ser srbms,sai_dependenci sd,sai_estado se, sai_acce_esp sae ";
		if($contadorCodigo>0){
			$query.= ", sai_sol_coti ssc, sai_usuario su, sai_empleado sem ";
		}
		$query.=		"WHERE ".
							"srbms.rebms_id = '".$codigo."' AND ";		
		if($contadorCodigo>0){
			$query .=		"srbms.rebms_id = ssc.rebms_id AND ".
							"ssc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula AND ";
		}
		$query .=			(($queryDependencia!="")?$queryDependencia." AND ":"").
							(($queryUsuario!="")?$queryUsuario." AND ":"").			
							"srbms.depe_id = sd.depe_id AND ".
							"srbms.esta_id = se.esta_id AND ".
							"srbms.rebms_imp_p_c = sae.acce_id AND ".
							"srbms.rebms_imp_esp = sae.aces_id ".
				") AS srbms ".$fromCadena." ".
				"WHERE ".$queryCadenaPorDefecto.(($queryCadena!="")?" AND ".$queryCadena:"");
		if($contadorCodigo>0){
			$query .="ORDER BY rebms_fecha DESC ";
		}		
		$query .=	"LIMIT ".$tamanoPagina." OFFSET ".$desplazamiento;
	}else{
		$query =	"SELECT ".
						"rebms_id, ".
						"to_char(rebms_fecha,'DD/MM/YYYY') as rebms_fecha_cadena, ".
						"rebms_fecha, ".
						"rebms_tipo, ".
						"imputacion_nombre ,".
						"srbms.esta_id, ".
						"esta_nombre, ".
						"depe_nombre ";	
		if($estado==ESTADO_REQUISICION_ENVIADAS || $estado==ESTADO_REQUISICION_ENVIADAS_POR_OTROS){
			$query.= ", srbms.usua_login, srbms.solicitante ";
		}
		$query.= ", swfch.wfop_id, swfgh.wfgr_id ";
		$query.=	"FROM ( ".
						"SELECT ".
							"srbms.rebms_id, ".
							"srbms.rebms_fecha, ".
							"srbms.rebms_tipo, ".
							"spae.centro_gestor || '/' || spae.centro_costo as imputacion_nombre, ".
							"srbms.esta_id, ".
							"se.esta_nombre, ".
							"sd.depe_nombre ";
		if($estado==ESTADO_REQUISICION_ENVIADAS || $estado==ESTADO_REQUISICION_ENVIADAS_POR_OTROS){
			$query.= 		", ssc.usua_login ".
							", sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante ";
		}
		$query.=		"FROM sai_req_bi_ma_ser srbms,sai_dependenci sd,sai_estado se, sai_proy_a_esp spae ";
		if($estado==ESTADO_REQUISICION_ENVIADAS || $estado==ESTADO_REQUISICION_ENVIADAS_POR_OTROS){
			$query.= ", sai_sol_coti ssc, sai_usuario su, sai_empleado sem ";
		}	
		$query.=		"WHERE ";		
		if($tipoRequ!=TIPO_REQUISICION_TODAS){
			$query .=		"srbms.rebms_tipo = ".$tipoRequ." AND ";
		}
		if($proyAcc == "true"){
			if($radioProyAcc=="proyecto"){
				$query .=	"srbms.rebms_tipo_imputa = '1' AND ";	
				if($proyecto!=""){
					$query .=	"srbms.rebms_imp_p_c = '".substr($proyecto,0,strpos($proyecto,"-"))."' AND srbms.rebms_imp_esp = '".substr($proyecto,strpos($proyecto,"-")+1)."' AND ";					
				}
			}else{
				$query .=	"srbms.rebms_tipo_imputa <> '1' AND ";
			}
		}
		if($dependencia != ""){
			$query .=	"srbms.depe_id = '".$dependencia."' AND ";
		}
		if($estado==ESTADO_REQUISICION_PENDIENTES){
			$query .=		"srbms.rebms_id NOT IN (SELECT rebms_id FROM sai_sol_coti) AND ";
		}else if($estado==ESTADO_REQUISICION_ENVIADAS){
			$query .=		"srbms.rebms_id = ssc.rebms_id AND ssc.usua_login = '".$_SESSION['login']."' AND ".
							"ssc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula AND ";
		}else if($estado==ESTADO_REQUISICION_ENVIADAS_POR_OTROS){
			$query .=		"srbms.rebms_id = ssc.rebms_id AND ssc.usua_login <> '".$_SESSION['login']."' AND ".
							"ssc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula AND ";
		}else if($estado==ESTADO_REQUISICION_NO_REVISADAS && (substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150" || substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46" || $user_perfil_id == "47350" || $user_perfil_id == "65150")){//ES ASISTENTE ADMINISTRATIVO, GERENTE O DIRECTOR
			$query .=		"srbms.esta_id = ".$estadoTransito." AND ";	
		}else if($estado==ESTADO_REQUISICION_DEVUELTAS || $estado==ESTADO_REQUISICION_DEVUELTAS_POR_USUARIO){
			$query .=		"srbms.esta_id = ".$estadoDevuelto." AND ";
		}else if($estado==ESTADO_REQUISICION_APROBADAS){
			$query .=		"srbms.esta_id = ".$estadoAprobado." AND ";
		}else if($estado==ESTADO_REQUISICION_ANULADAS){
			$query .=		"srbms.esta_id = ".$estadoAnulado." AND ";
		}else if($estado==ESTADO_REQUISICION_EN_BORRADOR){
			$query .=		"srbms.esta_id = ".$estadoBorrador." AND ";
		}
		if($controlFechas=="true" && $fechaInicio!="" && $fechaFin!=""){
			$query .=		"srbms.rebms_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ";
		}
		if($idViatico != ""){
			$query .=		"lower(srbms.vnac_id) = '".strtolower($idViatico)."' AND ";
		}
		$query .=			(($queryDependencia!="")?$queryDependencia." AND ":"").
							(($queryUsuario!="")?$queryUsuario." AND ":"").
							"srbms.depe_id = sd.depe_id AND ".
							"srbms.esta_id = se.esta_id AND ".
							"srbms.rebms_imp_p_c = spae.proy_id AND ".
							"srbms.rebms_imp_esp = spae.paes_id ".
						"UNION ".
						"SELECT ".
							"srbms.rebms_id, ".
							"srbms.rebms_fecha, ".
							"srbms.rebms_tipo, ".
							"sae.centro_gestor || '/' || sae.centro_costo as imputacion_nombre, ".
							"srbms.esta_id, ".
							"se.esta_nombre, ".
							"sd.depe_nombre ";
		if($estado==ESTADO_REQUISICION_ENVIADAS || $estado==ESTADO_REQUISICION_ENVIADAS_POR_OTROS){
			$query.= 		", ssc.usua_login ".
							", sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante ";
		}
		$query.=		"FROM sai_req_bi_ma_ser srbms,sai_dependenci sd,sai_estado se, sai_acce_esp sae ";
		if($estado==ESTADO_REQUISICION_ENVIADAS || $estado==ESTADO_REQUISICION_ENVIADAS_POR_OTROS){
			$query.= ", sai_sol_coti ssc, sai_usuario su, sai_empleado sem ";
		}	
		$query.=		"WHERE ";	
		if($tipoRequ!=TIPO_REQUISICION_TODAS){
			$query .=		"srbms.rebms_tipo = ".$tipoRequ." AND ";			
		}
		if($proyAcc == "true"){
			if($radioProyAcc=="accionCentralizada"){
				$query .=	"srbms.rebms_tipo_imputa = '0' AND ";
				if($accionCentralizada!=""){
					$query .=	" srbms.rebms_imp_p_c = '".substr($accionCentralizada,0,strpos($accionCentralizada,"-"))."' AND srbms.rebms_imp_esp = '".substr($accionCentralizada,strpos($accionCentralizada,"-")+1)."' AND ";
				}
			}else{
				$query .=	"srbms.rebms_tipo_imputa <> '0' AND ";
			}
		}
		if($dependencia != ""){
			$query .=	"srbms.depe_id = '".$dependencia."' AND ";
		}
		if($estado==ESTADO_REQUISICION_PENDIENTES){
			$query .=		"srbms.rebms_id NOT IN (SELECT rebms_id FROM sai_sol_coti) AND ";
		}else if($estado==ESTADO_REQUISICION_ENVIADAS){
			$query .=		"srbms.rebms_id = ssc.rebms_id AND ssc.usua_login = '".$_SESSION['login']."' AND ".
							"ssc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula AND ";
		}else if($estado==ESTADO_REQUISICION_ENVIADAS_POR_OTROS){
			$query .=		"srbms.rebms_id = ssc.rebms_id AND ssc.usua_login <> '".$_SESSION['login']."' AND ".
							"ssc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula AND ";
		}else if($estado==ESTADO_REQUISICION_NO_REVISADAS && (substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150" || substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46" || $user_perfil_id == "47350" || $user_perfil_id == "65150")){//ES ASISTENTE ADMINISTRATIVO, GERENTE O DIRECTOR
			$query .=		"srbms.esta_id = ".$estadoTransito." AND ";	
		}else if($estado==ESTADO_REQUISICION_DEVUELTAS || $estado==ESTADO_REQUISICION_DEVUELTAS_POR_USUARIO){
			$query .=		"srbms.esta_id = ".$estadoDevuelto." AND ";
		}else if($estado==ESTADO_REQUISICION_APROBADAS){
			$query .=		"srbms.esta_id = ".$estadoAprobado." AND ";
		}else if($estado==ESTADO_REQUISICION_ANULADAS){
			$query .=		"srbms.esta_id = ".$estadoAnulado." AND ";
		}else if($estado==ESTADO_REQUISICION_EN_BORRADOR){
			$query .=		"srbms.esta_id = ".$estadoBorrador." AND ";
		}
		if($controlFechas=="true" && $fechaInicio!="" && $fechaFin!=""){
			$query .=		"srbms.rebms_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ";
		}
		if($idViatico != ""){
			$query .=		"lower(srbms.vnac_id) = '".strtolower($idViatico)."' AND ";
		}
		$query .=			(($queryDependencia!="")?$queryDependencia." AND ":"").
							(($queryUsuario!="")?$queryUsuario." AND ":"").
							"srbms.depe_id = sd.depe_id AND ".
							"srbms.esta_id = se.esta_id AND ".
							"srbms.rebms_imp_p_c = sae.acce_id AND ".
							"srbms.rebms_imp_esp = sae.aces_id ".
					") AS srbms ".$fromCadena." ".
				"WHERE ".$queryCadenaPorDefecto.(($queryCadena!="")?" AND ".$queryCadena:"")." ".
				"ORDER BY srbms.rebms_fecha DESC, srbms.rebms_id DESC ".
				"LIMIT ".$tamanoPagina." OFFSET ".$desplazamiento;
	}
	$resultado = pg_exec($conexion, $query);
	
	if($resultado === false)
		echo "Error: ". pg_last_error($conexion);
	
	$numeroFilas = pg_numrows($resultado);
?>
<table>
	<tr>
		<td colspan="1" class="normal peq_verde_bold" style="text-align: center;">
			<p>Requisiciones
			<?php
			if($codigo && $codigo!=""){
				echo "con c&oacute;digo ".$codigo;
			}else{
				if($estado==ESTADO_REQUISICION_PENDIENTES){
					echo "pendientes";
				}else if($estado==ESTADO_REQUISICION_ENVIADAS){
					echo "enviadas";
				}else if($estado==ESTADO_REQUISICION_NO_REVISADAS){
					echo "en transito";
				}else if($estado==ESTADO_REQUISICION_APROBADAS){
					echo "aprobadas";
				}else if($estado==ESTADO_REQUISICION_DEVUELTAS){
					echo "devueltas";
				}else if($estado==ESTADO_REQUISICION_ANULADAS){
					echo "anuladas";
				}else if($estado==ESTADO_REQUISICION_EN_BORRADOR){
					echo "en borrador";
				}
				echo " ".(($tipoRequ==TIPO_REQUISICION_COMPRA)?"de Compra":(($tipoRequ==TIPO_REQUISICION_SERVICIO)?"de Servicio":""));
			} ?>
			</p>
		</td>
	</tr>
<?php 
	if($numeroFilas>0){
		$columnas = 7;
?>
	<tr>
		<td>
			<table class="tablaalertas" background="../../imagenes/fondo_tabla.gif" >
				<tr class="td_gray normalNegroNegrita">
					<th><div style="margin-left: 20px;margin-right: 20px;">C&oacute;digo Requisici&oacute;n</div></th>
					<th><div style="margin-left: 20px;margin-right: 20px;">Elaborada en Fecha</div></th>
					<th><div style="margin-left: 20px;margin-right: 20px;">Tipo Requisici&oacute;n</div></th>
					<th><div style="margin-left: 20px;margin-right: 20px;">Proy/Acc</div></th>
					<th><div style="margin-left: 20px;margin-right: 20px;">Dependencia</div></th>
					<th><div style="margin-left: 20px;margin-right: 20px;">Estado</div></th>
<?php
		if($estado==ESTADO_REQUISICION_ENVIADAS || $estado==ESTADO_REQUISICION_ENVIADAS_POR_OTROS || ($contadorCodigo>0 && ($user_perfil_id == "15456" || $user_perfil_id == "42456"))) {
			$columnas++;
			echo "<th><div style='margin-left: 20px;margin-right: 20px;'>Tomada por Usuario</div></th>";
		}
?>
					<th style="width: 100px;"><div style="margin-left: 20px;margin-right: 20px;">&nbsp;</div></th>			
				</tr>
<?
		$modificar = "2";
		$vistoBueno = "6";
		for($ri = 0; $ri < $numeroFilas; $ri++) {
	    	echo "<tr class='resultados'>\n";
	    	$row = pg_fetch_array($resultado, $ri);
	    	
			if($user_perfil_id == "15456" || $user_perfil_id == "42456"){
		   		echo "<td align='center'><a href='javascript: verArticulos(\"".$row["rebms_id"]."\");' title='Revisar Requisici&oacute;n'>".$row["rebms_id"]."</a>";
			}else if(substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150"){
				if($row["esta_id"]==$estadoBorrador || ($row["wfop_id"]==$modificar && $row["wfgr_id"] == $idGrupo && $row["esta_id"]!=$estadoAnulado)){
					echo "<td align='center'><a href='javascript: verArticulos(\"".$row["rebms_id"]."\",true);' title='Modificar'>".$row["rebms_id"]."</a>";
				}else{
		   			echo "<td align='center'><a href='javascript: verArticulos(\"".$row["rebms_id"]."\");' title='Ver detalle'>".$row["rebms_id"]."</a>";
				}
			}else if(substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46" || $user_perfil_id == "47350" || $user_perfil_id == "65150"){
				if($row["wfop_id"]==$vistoBueno && $row["wfgr_id"] == $idGrupo && $row["esta_id"]!=$estadoBorrador && $row["esta_id"]!=$estadoAnulado){
					echo "<td align='center'><a href='javascript: verArticulos(\"".$row["rebms_id"]."\",true);' title='Revisar'>".$row["rebms_id"]."</a>";
				}else{
		   			echo "<td align='center'><a href='javascript: verArticulos(\"".$row["rebms_id"]."\");' title='Ver detalle'>".$row["rebms_id"]."</a>";
				}
			}else if($user_perfil_id=="30400"){
				if($row["wfop_id"]==$vistoBueno && $row["wfgr_id"] == $idGrupo){
					echo "<td align='center'><a href='javascript: verArticulos(\"".$row["rebms_id"]."\",true);' title='Revisar'>".$row["rebms_id"]."</a>";
				}else{
		   			echo "<td align='center'><a href='javascript: verArticulos(\"".$row["rebms_id"]."\");' title='Ver detalle'>".$row["rebms_id"]."</a>";
				}
			}
	   		echo "<td align='center'>", $row["rebms_fecha_cadena"], "</td>
	   		<td align='center'>", ($row["rebms_tipo"]==TIPO_REQUISICION_COMPRA)?"Compra":(($row["rebms_tipo"]==TIPO_REQUISICION_SERVICIO)?"Servicio":""), "</td>
	   		<td align='center'>", $row["imputacion_nombre"], "</td>
	   		<td align='center'>", $row["depe_nombre"], "</td>";
	    	if($row["esta_id"]==$estadoDevuelto){
	    		echo "<td align='center' style='color: red;'>", $row["esta_nombre"], "</td>";	
	    	}else if($row["esta_id"]==$estadoAnulado){
	    		echo "<td align='center' style='color: gray;'>", $row["esta_nombre"], "</td>";	
	    	}else if($row["esta_id"]==$estadoBorrador){
	    		echo "<td align='center' style='color: black;'>", $row["esta_nombre"], "</td>";	
	    	}else{
	    		echo "<td align='center'>", $row["esta_nombre"], "</td>";	
	    	}
	    	if($estado==ESTADO_REQUISICION_ENVIADAS || $estado==ESTADO_REQUISICION_ENVIADAS_POR_OTROS || ($contadorCodigo>0 && ($user_perfil_id == "15456" || $user_perfil_id == "42456"))){
				echo "<td align='center'>".$row["solicitante"]."</td>";
			}
			if($user_perfil_id == "15456" || $user_perfil_id == "42456"){
		   		if($estado==ESTADO_REQUISICION_ENVIADAS || $estado==ESTADO_REQUISICION_ENVIADAS_POR_OTROS || $contadorCodigo>0){
		   			echo "<td align='center'><a href='javascript: verSolicitudesDeCotizacion(\"".$row["rebms_id"]."\");' title='Ver Solicitudes de Cotizaci&oacute;n'><img src='../../imagenes/email.jpeg' border='0'/></a>";
		   			if($user_perfil_id == "15456"){
			   			echo "<a href='javascript: ordenDeCompra(\"".$row["rebms_id"]."\");' title='An&aacute;lisis de Cotizaciones'><img src='../../imagenes/analisis.gif' border='0'/></a>";
		   			}
		   		}else{
		   			echo "<td align='center'><a href='javascript: verArticulos(\"".$row["rebms_id"]."\");' title='Revisar Requisici&oacute;n'>Revisar</a>";	
		   		}
			}else if(substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150"){
				if($row["esta_id"]==$estadoBorrador || ($row["wfop_id"]==$modificar && $row["wfgr_id"] == $idGrupo && $row["esta_id"]!=$estadoAnulado)){
					echo "<td align='center'>".
							"<a href='javascript: verArticulos(\"".$row["rebms_id"]."\");' title='Ver detalle'>Ver detalle</a>".
							"<br/><a href='javascript: verArticulos(\"".$row["rebms_id"]."\",true);' title='Modificar Requisici&oacute;n'>Modificar</a>";	
				}else{
		   			echo "<td align='center'><a href='javascript: verArticulos(\"".$row["rebms_id"]."\");' title='Ver detalle'>Ver detalle</a>";
				}
			}else if(substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46" || $user_perfil_id == "47350" || $user_perfil_id == "65150"){
				if($row["wfop_id"]==$vistoBueno && $row["wfgr_id"] == $idGrupo && $row["esta_id"]!=$estadoBorrador && $row["esta_id"]!=$estadoAnulado){
					echo "<td align='center'><a href='javascript: verArticulos(\"".$row["rebms_id"]."\",true);' title='Revisar'>Revisar</a>";
				}else{
		   			echo "<td align='center'><a href='javascript: verArticulos(\"".$row["rebms_id"]."\");' title='Ver detalle'>Ver detalle</a>";
				}
			}else if($user_perfil_id=="30400"){
				if($row["wfop_id"]==$vistoBueno && $row["wfgr_id"] == $idGrupo){
					echo "<td align='center'><a href='javascript: verArticulos(\"".$row["rebms_id"]."\",true);' title='Revisar'>Revisar</a>";
				}else{
		   			echo "<td align='center'><a href='javascript: verArticulos(\"".$row["rebms_id"]."\");' title='Ver detalle'>Ver detalle</a>";
				}
			}
	   		echo "</td>
	  		</tr>\n";
		}
		
		echo "<tr class='td_gray'><td colspan='".$columnas."' align='center'>";
		$ventanaActual = ($paginaL%$tamanoVentana==0)?$paginaL/$tamanoVentana:intval($paginaL/$tamanoVentana)+1;
		$ri = (($ventanaActual-1)*$tamanoVentana)+1;
		while($ri<=$ventanaActual*$tamanoVentana && $ri<=$totalPaginas) {
			if($ri==(($ventanaActual-1)*$tamanoVentana)+1 && $ri!=1){
				echo "<a onclick='listarRequisiciones(".($ri-1).");' style='cursor: pointer;text-decoration: underline;'>&lt;</a> ";
			}
			if($ri==$paginaL){
				echo $ri." ";
			}else{
				echo "<a onclick='listarRequisiciones(".$ri.");' style='cursor: pointer;text-decoration: underline;'>".$ri."</a> ";
			}
			if($ri==$ventanaActual*$tamanoVentana && $ri<$totalPaginas){
				echo "<a onclick='listarRequisiciones(".($ri+1).");' style='cursor: pointer;text-decoration: underline;'>&gt;</a> ";
			}
			$ri++;   	
		}
		echo "</td></tr>\n";
?>
			</table>
		</td>
	</tr>
<?php	
	}else{
		echo "<tr><td>Actualmente no hay requisiciones</td></tr>";
	}
?>
</table>