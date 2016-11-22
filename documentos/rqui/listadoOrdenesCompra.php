<?php 
	ob_start();
	session_start();
	if (isset($_POST['tipoRequ']) && $_POST['tipoRequ'] != "") {
		header("Content-Type: text/html; charset=iso-8859-1\n");
		$tipoRequ = $_POST['tipoRequ'];
	}else{
		$tipoRequ = $tipoRequisicion;	
	}
	
	$queryCadenaPorDefecto =	"AND soc.ordc_id = sdg.docg_id AND ".
								"sdg.wfca_id = swfc.wfca_id AND ".
								"swfc.wfgr_id = swfg.wfgr_id AND ".
								"swfc.wfca_id_hijo = swfch.wfca_id AND ".
								"swfch.wfgr_id = swfgh.wfgr_id ";
	$conParent = true;
	$fromCadena = ", sai_doc_genera sdg, sai_wfcadena swfc, sai_wfcadena swfch, sai_wfgrupo swfg, sai_wfgrupo swfgh ";
	$queryCadena = "";
	$objetoRevisar = 4;
	$opcionAprobar = 6;
	$opcionFin = 99;
	$opcionDevolver = 5;
	$opcionAnular = 24;
	if($user_perfil_id=="15456"){//ANALISTA DE COMPRAS
		$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
					"WHERE ".
						"swfg.wfgr_perf = '".$user_perfil_id."' ";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$idGrupo = $row["wfgr_id"];
	}else if($user_perfil_id == "42456"){//COORDINADOR DE COMPRAS
		$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
					"WHERE ".
						"swfg.wfgr_perf = '".$user_perfil_id."' ";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$idGrupo = $row["wfgr_id"];
		
		/*if((!$codigo || $codigo=="") && $estado==ESTADO_REQUISICION_NO_REVISADAS){//NO REVISADA
			$queryCadena = 	"sdg.perf_id_act = '".$user_perfil_id."' ";
		}*/
	}else if($user_perfil_id == "46450"){//DIRECTOR DE ADMIN Y FINANZAS
		$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
					"WHERE ".
						"swfg.wfgr_perf = '".$user_perfil_id."' ";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$idGrupo = $row["wfgr_id"];
		
		if((!$codigo || $codigo=="") && $estado==ESTADO_REQUISICION_NO_REVISADAS){//NO REVISADA
			$queryCadena = 	"sdg.perf_id_act = '".$user_perfil_id."' ";
		}
	}else if($user_perfil_id == "30400"){//ANALISTA DE PRESUPUESTO
		$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
					"WHERE ".
						"swfg.wfgr_perf = '".$user_perfil_id."' ";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$idGrupo = $row["wfgr_id"];
		
		if((!$codigo || $codigo=="") && ($estado==ESTADO_REQUISICION_NO_REVISADAS || $estado == ESTADO_REQUISICION_DEVUELTAS)){//NO REVISADA O DEVUELTA POR DIRECTOR PRESUPUESTO
			/*$queryCadena = 	"swfgh.wfgr_perf = '".$user_perfil_id."' AND ".
							"swfch.wfop_id=".$opcionAprobar." ";*/
			$queryCadena = 	"sdg.perf_id_act = '".$user_perfil_id."' ";
		}else if((!$codigo || $codigo=="") && $estado==ESTADO_REQUISICION_PENDIENTES){
			//APROBADAS POR ANALISTA DE PRESUPUESTO
			//Cuyo id de cadena en sai_docgenera es igual al id de la cadena de aprobacion del analista de presupuesto
			$queryCadena = 	"swfg.wfgr_perf = '".$user_perfil_id."' AND ".
							"swfc.wfop_id=".$opcionAprobar." ";
		}else if((!$codigo || $codigo=="") && $estado==ESTADO_REQUISICION_APROBADAS){
			//APROBADAS POR DIRECTOR DE PRESUPUESTO
			//Cuyo id de cadena en sai_docgenera es igual a 0
			$queryCadenaPorDefecto =	"AND soc.ordc_id = sdg.docg_id AND ".
										"sdg.wfca_id = swfch.wfca_id AND ".
										"swfch.wfgr_id = swfgh.wfgr_id ";
			$conParent = false;
			$fromCadena = ", sai_doc_genera sdg, sai_wfcadena swfch, sai_wfgrupo swfgh ";
			$queryCadena = 	"swfch.wfca_id=0 ";
		}else if((!$codigo || $codigo=="") && ($estado==ESTADO_REQUISICION_DEVUELTAS_POR_USUARIO || $estado==ESTADO_REQUISICION_ANULADAS)){
			//ULTIMA REVISION DE DOCUMENTO SEA DEVOLUCION Y ECHA POR USUARIO.
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
			$queryCadena = 	"soc.ordc_id IN ".$revisiones." ";
		}else if((!$codigo || $codigo=="") &&  $estado==ESTADO_REQUISICION_TODAS){
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
			$queryCadenaPorDefecto =	"AND soc.ordc_id = sdg.docg_id AND ".
										"sdg.wfca_id = swfc.wfca_id AND ".
										"swfc.wfgr_id = swfg.wfgr_id ";
			$queryCadenaPorDefecto2 =	"swfc.wfca_id_hijo = swfch.wfca_id AND ".
										"swfch.wfgr_id = swfgh.wfgr_id ";
			
			$queryCadena = 	"((".
								"swfc.wfca_id=0 AND ".
								"swfc.wfca_id = swfch.wfca_id AND ".
								"swfch.wfgr_id = swfgh.wfgr_id ".
							") ".
							"OR ".
							"(".$queryCadenaPorDefecto2." AND ".
								"((swfgh.wfgr_perf = '".$user_perfil_id."' AND swfch.wfop_id=".$opcionAprobar.") OR ".//NO REVISADAS O DEVUELTAS POR DIRECTOR DE PRESUPUESTO
								"(swfg.wfgr_perf = '".$user_perfil_id."' AND swfc.wfop_id=".$opcionAprobar.") OR ".//PENDIENTES
								"soc.ordc_id IN ".$revisiones." ) ".//DEVUELTAS POR ANALISTA DE PRESUPUESTO O ANULADAS
							")) ";
			
		}
	}else if($user_perfil_id == "46400"){//DIRECTOR DE PRESUPUESTO
		$query = 	"SELECT swfg.wfgr_id FROM sai_wfgrupo swfg ".
					"WHERE ".
						"swfg.wfgr_perf = '".$user_perfil_id."' ";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$idGrupo = $row["wfgr_id"];
		
		if((!$codigo || $codigo=="") && $estado == ESTADO_REQUISICION_DEVUELTAS){//DEVUELTA POR DIRECTOR PRESUPUESTO
			$queryCadena = 	"swfg.wfgr_perf = '".$user_perfil_id."' AND ".
							"swfc.wfop_id=".$opcionDevolver." ";
		}else if((!$codigo || $codigo=="") &&  $estado==ESTADO_REQUISICION_PENDIENTES){
			//APROBADAS POR ANALISTA DE PRESUPUESTO
			//Cuyo id de cadena hijo de la cadena en sai_docgenera es igual al id de la cadena de revision del director de presupuesto
			/*$queryCadena = 	"swfgh.wfgr_perf = '".$user_perfil_id."' AND ".
							"swfch.wfop_id=".$opcionAprobar." ";*/
			$queryCadena = 	"sdg.perf_id_act = '".$user_perfil_id."' ";
		}else if((!$codigo || $codigo=="") &&  $estado==ESTADO_REQUISICION_APROBADAS){
			//APROBADAS POR DIRECTOR DE PRESUPUESTO
			//Cuyo id de cadena en sai_docgenera es igual a 0
			$queryCadenaPorDefecto =	"AND soc.ordc_id = sdg.docg_id AND ".
										"sdg.wfca_id = swfch.wfca_id AND ".
										"swfch.wfgr_id = swfgh.wfgr_id ";
			$conParent = false;
			$fromCadena = ", sai_doc_genera sdg, sai_wfcadena swfch, sai_wfgrupo swfgh ";
			$queryCadena = 	"swfch.wfca_id=0 ";
		}else if((!$codigo || $codigo=="") &&  $estado==ESTADO_REQUISICION_TODAS){
			$queryCadenaPorDefecto =	"AND soc.ordc_id = sdg.docg_id AND ".
										"sdg.wfca_id = swfc.wfca_id AND ".
										"swfc.wfgr_id = swfg.wfgr_id ";
			$queryCadenaPorDefecto2 =	"swfc.wfca_id_hijo = swfch.wfca_id AND ".
										"swfch.wfgr_id = swfgh.wfgr_id ";
			
			$queryCadena = 	"((".
								"swfc.wfca_id=0 AND ".
								"swfc.wfca_id = swfch.wfca_id AND ".
								"swfch.wfgr_id = swfgh.wfgr_id ".
							") ".
							"OR ".
							"(".$queryCadenaPorDefecto2." AND ".
							"((swfgh.wfgr_perf = '".$user_perfil_id."' AND swfch.wfop_id=".$opcionAprobar.") OR ".//PENDIENTES
							"(swfg.wfgr_perf = '".$user_perfil_id."' AND swfc.wfop_id=".$opcionDevolver.")) ".//DEVUELTAS POR DIRECTOR DE PRESUPUESTO
							")) ";
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
	$estadoTransito = "10";
	$estadoDevuelto = "7";
	$estadoAprobado = "13";
	$estadoAnulado = "15";
	
	$desplazamiento = ($paginaL-1)*$tamanoPagina;
	if($codigo && $codigo!=""){
		$query = 	"SELECT COUNT(DISTINCT(soc.ordc_id)) ".
					"FROM sai_orden_compra soc ".(($user_perfil_id!="30400" && $user_perfil_id!="46400")?"INNER JOIN sai_proveedor_nuevo sp ON (LOWER(soc.rif_proveedor_seleccionado) LIKE LOWER(sp.prov_id_rif)) ":"")." ".$fromCadena." ".
					"WHERE lower(soc.ordc_id) like '".strtolower($codigo)."' ";
	}else{
		$query = 	"SELECT COUNT(DISTINCT(soc.ordc_id)) ".
					"FROM sai_orden_compra soc ".
						(($user_perfil_id!="30400" && $user_perfil_id!="46400")?
							"INNER JOIN sai_proveedor_nuevo sp ON (LOWER(soc.rif_proveedor_seleccionado) LIKE LOWER(sp.prov_id_rif)) ".
							(($nombreItem != "" && $idItem != "")?"INNER JOIN sai_cotizacion sc ON (soc.ordc_id = sc.ordc_id AND LOWER(soc.rif_proveedor_seleccionado) LIKE LOWER(sc.rif_proveedor)) LEFT OUTER JOIN sai_cotizacion_item sci ON (sc.id_cotizacion = sci.id_cotizacion) LEFT OUTER JOIN sai_cotizacion_item_adicional scia ON (sc.id_cotizacion = scia.id_cotizacion) ":"")
							:"").", ".
						"sai_req_bi_ma_ser srbms ".$fromCadena." ".
					"WHERE ";
		$query .=		"soc.rebms_id = srbms.rebms_id ";
		if($codigoCR!=""){
			$query .=	"AND lower(srbms.rebms_id) like '".strtolower($codigoCR)."' ";
		}
		if($tipoRequ!=TIPO_REQUISICION_TODAS){
			$query .=	"AND srbms.rebms_tipo = ".$tipoRequ." ";			
		}
		if($dependencia != ""){
			$query .=	"AND srbms.depe_id = '".$dependencia."' ";
		}
		if($estado==ESTADO_REQUISICION_NO_REVISADAS && $user_perfil_id != "30400"){
			$query .=	"AND soc.esta_id = ".$estadoTransito;
		}else if($estado==ESTADO_REQUISICION_NO_REVISADAS || $estado==ESTADO_REQUISICION_PENDIENTES){
			$query .=	"AND soc.esta_id = ".$estadoAprobado." ";
		}else if($estado==ESTADO_REQUISICION_DEVUELTAS || $estado==ESTADO_REQUISICION_DEVUELTAS_POR_USUARIO){
			$query .=	"AND soc.esta_id = ".$estadoDevuelto." ";
		}else if($estado==ESTADO_REQUISICION_APROBADAS){
			$query .=	"AND soc.esta_id = ".$estadoAprobado." ";
		}else if($estado==ESTADO_REQUISICION_ANULADAS){
			$query .=	"AND soc.esta_id = ".$estadoAnulado." ";
		}
		if($nombreProveedor != "" && $rifProveedor != ""){
			$query .=	"AND UPPER(sp.prov_id_rif) LIKE '%".cadenaAMayusculas($rifProveedor)."%' ";
		}
		if($nombreItem != "" && $idItem != ""){
			$query .=	"AND (sci.id_item = ".$idItem." OR scia.id_item = ".$idItem.") ";
		}
		if($controlFechas=="true" && $fechaInicio!="" && $fechaFin!=""){
			$query .=	"AND soc.fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 ";
		}
	}
	$query .= $queryCadenaPorDefecto.(($queryCadena!="")?" AND ".$queryCadena:"")." ";
	$resultadoContador = pg_exec($conexion, $query);
	$numeroFilas = pg_numrows($resultadoContador);
	
	$contador = 0;
	if($numeroFilas>0){
		$row = pg_fetch_array($resultadoContador, 0);
		$contador = $row[0];
		$totalPaginas = ($contador%$tamanoPagina == 0)?$contador/$tamanoPagina:intval($contador/$tamanoPagina)+1;
		if($codigo && $codigo!=""){
			$query= 	"SELECT ".
							"soc.ordc_id, ".
							"to_char(soc.fecha,'DD/MM/YYYY') as fecha, ".
							"soc.fecha as soc_fecha, ".
							"srbms.rebms_id, ".
							"srbms.rebms_tipo, ".
							"se.esta_id, ".
							"se.esta_nombre, ".
							"sd.depe_nombre, ".
							"sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante, ".
							(($user_perfil_id!="30400" && $user_perfil_id!="46400")?"sp.prov_nombre as nombre_proveedor, ":"").
							(($conParent==true)?"swfg.wfgr_descrip as wfgr_descrip_parent, swfg.wfgr_id as wfgr_id_parent, ":"")."swfgh.wfgr_descrip, swfgh.wfgr_id ".
						"FROM sai_req_bi_ma_ser srbms,sai_dependenci sd,sai_estado se, sai_orden_compra soc ".(($user_perfil_id!="30400" && $user_perfil_id!="46400")?"INNER JOIN sai_proveedor_nuevo sp ON (LOWER(soc.rif_proveedor_seleccionado) LIKE LOWER(sp.prov_id_rif))":"").", sai_usuario su, sai_empleado sem ".$fromCadena." ".
						"WHERE ".
							"lower(soc.ordc_id) like '".strtolower($codigo)."' AND ".
							"soc.rebms_id = srbms.rebms_id AND ".
							"soc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula AND ".
							"srbms.depe_id = sd.depe_id AND ".
							"soc.esta_id = se.esta_id ";
			$query .= $queryCadenaPorDefecto.(($queryCadena!="")?" AND ".$queryCadena:"")." ".
						"GROUP BY soc.ordc_id, soc.fecha, srbms.rebms_id, srbms.rebms_tipo, se.esta_id, se.esta_nombre, sd.depe_nombre, solicitante ".(($user_perfil_id!="30400" && $user_perfil_id!="46400")?",sp.prov_nombre ":"").", ".(($conParent==true)?"swfg.wfgr_descrip, swfg.wfgr_id, ":"")."swfgh.wfgr_descrip, swfgh.wfgr_id ".
						"ORDER BY soc_fecha DESC, soc.ordc_id DESC ".
						"LIMIT ".$tamanoPagina." OFFSET ".$desplazamiento;
		}else{
			$query= 	"SELECT ".
							"soc.ordc_id, ".
							"to_char(soc.fecha,'DD/MM/YYYY') as fecha, ".
							"soc.fecha as soc_fecha, ".
							"srbms.rebms_id, ".
							"srbms.rebms_tipo, ".
							"se.esta_id, ".
							"se.esta_nombre, ".
							"sd.depe_nombre, ".
							"sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante, ".
							(($user_perfil_id!="30400" && $user_perfil_id!="46400")?"sp.prov_nombre as nombre_proveedor, ":"").
							(($conParent==true)?"swfg.wfgr_descrip as wfgr_descrip_parent, swfg.wfgr_id as wfgr_id_parent, ":"")."swfgh.wfgr_descrip, swfgh.wfgr_id ".	
						"FROM ".
							"sai_req_bi_ma_ser srbms, ".
							"sai_dependenci sd,".
							"sai_estado se, ".
							"sai_orden_compra soc ".(($user_perfil_id!="30400" && $user_perfil_id!="46400")?
								"INNER JOIN sai_proveedor_nuevo sp ON (LOWER(soc.rif_proveedor_seleccionado) LIKE LOWER(sp.prov_id_rif)) ".
								(($nombreItem != "" && $idItem != "")?"INNER JOIN sai_cotizacion sc ON (soc.ordc_id = sc.ordc_id AND LOWER(soc.rif_proveedor_seleccionado) LIKE LOWER(sc.rif_proveedor)) LEFT OUTER JOIN sai_cotizacion_item sci ON (sc.id_cotizacion = sci.id_cotizacion) LEFT OUTER JOIN sai_cotizacion_item_adicional scia ON (sc.id_cotizacion = scia.id_cotizacion) ":"")
								:"").", ".
							"sai_usuario su, ".
							"sai_empleado sem ".$fromCadena." ".
						"WHERE ".
							"soc.rebms_id = srbms.rebms_id AND ";
			if($codigoCR!=""){
				$query .=	"lower(srbms.rebms_id) like '".strtolower($codigoCR)."' AND ";
			}
			if($tipoRequ!=TIPO_REQUISICION_TODAS){
				$query .=	"srbms.rebms_tipo = ".$tipoRequ." AND ";			
			}
			if($dependencia != ""){
				$query .=	"srbms.depe_id = '".$dependencia."' AND ";
			}
			$query .=		"soc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula AND ";
			if($estado==ESTADO_REQUISICION_NO_REVISADAS && $user_perfil_id != "30400"){
				$query .=	"soc.esta_id = ".$estadoTransito." AND ";
			}else if($estado==ESTADO_REQUISICION_NO_REVISADAS || $estado==ESTADO_REQUISICION_PENDIENTES){
				$query .=	"soc.esta_id = ".$estadoAprobado." AND ";
			}else if($estado==ESTADO_REQUISICION_DEVUELTAS || $estado==ESTADO_REQUISICION_DEVUELTAS_POR_USUARIO){
				$query .=	"soc.esta_id = ".$estadoDevuelto." AND ";
			}else if($estado==ESTADO_REQUISICION_APROBADAS){
				$query .=	"soc.esta_id = ".$estadoAprobado." AND ";
			}else if($estado==ESTADO_REQUISICION_ANULADAS){
				$query .=	"soc.esta_id = ".$estadoAnulado." AND ";
			}
			if($nombreProveedor != "" && $rifProveedor != ""){
				$query .=	"UPPER(sp.prov_id_rif) LIKE '%".cadenaAMayusculas($rifProveedor)."%' AND ";
			}
			if($nombreItem != "" && $idItem != ""){
				$query .=	"(sci.id_item = ".$idItem." OR scia.id_item = ".$idItem.") AND ";
			}
			if($controlFechas=="true" && $fechaInicio!="" && $fechaFin!=""){
				$query .=	"soc.fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ";
			}
			$query .=		"srbms.depe_id = sd.depe_id AND ".
							"soc.esta_id = se.esta_id ";
			$query .= $queryCadenaPorDefecto.(($queryCadena!="")?" AND ".$queryCadena:"")." ".
						"GROUP BY soc.ordc_id, soc.fecha, srbms.rebms_id, srbms.rebms_tipo, se.esta_id, se.esta_nombre, sd.depe_nombre, solicitante ".(($user_perfil_id!="30400" && $user_perfil_id!="46400")?",sp.prov_nombre ":"").", ".(($conParent==true)?"swfg.wfgr_descrip, swfg.wfgr_id, ":"")."swfgh.wfgr_descrip, swfgh.wfgr_id ".
						"ORDER BY soc_fecha DESC, soc.ordc_id DESC ".
						"LIMIT ".$tamanoPagina." OFFSET ".$desplazamiento;
		}
		$resultado = pg_exec($conexion, $query);
		$numeroFilas = pg_numrows($resultado);
	}
?>
<table>
	<tr>
		<td colspan="1" class="normal peq_verde_bold" style="text-align: center;">
			<p>
			<?php
			if($codigo && $codigo!=""){
				echo "&Oacute;rdenes con c&oacute;digo ".$codigo;
			}else{
				echo (($tipoRequ==TIPO_REQUISICION_COMPRA)?"&Oacute;rdenes de compra":(($tipoRequ==TIPO_REQUISICION_SERVICIO)?"&Oacute;rdenes de servicio":"&Oacute;rdenes de compra y servicio"));
			} ?>
			</p>			
		</td>
	</tr>
<?php 
	if($numeroFilas>0){
?>
	<tr>
		<td>
			<table class="tablaalertas" background="../../imagenes/fondo_tabla.gif">
				<tr class="td_gray normalNegroNegrita">
					<th><div style="margin-left: 20px;margin-right: 20px;">C&oacute;digo orden<br/>compra</div></th>
					<th><div style="margin-left: 20px;margin-right: 20px;">C&oacute;digo<br/>requisici&oacute;n</div></th>
					<th><div style="margin-left: 20px;margin-right: 20px;">Elaborada<br/>en fecha</div></th>
					<?php 
					if($user_perfil_id!="30400" && $user_perfil_id!="46400"){
					?>
					<th><div style="margin-left: 20px;margin-right: 20px;">Proveedor<br/>seleccionado</div></th>
					<?php 
					}
					?>
					<th><div style="margin-left: 20px;margin-right: 20px;">Tipo de<br/>requisici&oacute;n</div></th>
					<th><div style="margin-left: 20px;margin-right: 20px;">Dependencia</div></th>
					<th><div style="margin-left: 20px;margin-right: 20px;">Estado</div></th>
					<?php
					if($estado == ESTADO_REQUISICION_NO_REVISADAS || $estado == ESTADO_REQUISICION_TODAS){
					?>
					<th><div style="margin-left: 20px;margin-right: 20px;">Por revisar</div></th>
					<?php
					}
					?>
					<th><div style="margin-left: 20px;margin-right: 20px;">Elaborada por usuario</div></th>
					<th style="width: 100px;"><div style="margin-left: 20px;margin-right: 20px;">Acci&oacute;n</div></th>
				</tr>
<?
				for($ri = 0; $ri < $numeroFilas; $ri++) {
			    	echo "<tr class='resultados'>\n";
			    	$row = pg_fetch_array($resultado, $ri);
			    	if($row["wfgr_id"]!=$row["wfgr_id_parent"] && $row["wfgr_id"] == $idGrupo && $row["esta_id"]!=$estadoAnulado){
			    		echo "<td align='center'><a href='javascript: verOrdenDeCompra(\"".$row["ordc_id"]."\",true);' title='Ver detalle de Orden de Compra'>".$row["ordc_id"]."</a>";
			    	}else{
			    		echo "<td align='center'><a href='javascript: verOrdenDeCompra(\"".$row["ordc_id"]."\");' title='Ver detalle de Orden de Compra'>".$row["ordc_id"]."</a>";
			    	}
			    	echo "<td align='center'><a href='javascript: verArticulos(\"".$row["rebms_id"]."\");' title='Ver detalle de Requisici&oacute;n'>".$row["rebms_id"]."</a></td>
			   		<td align='center'>", $row["fecha"], "</td>
			   		".(($user_perfil_id!="30400" && $user_perfil_id!="46400")?"<td align='center'>".$row["nombre_proveedor"]."</td>":"")."
			   		<td align='center'>", ($row["rebms_tipo"]==TIPO_REQUISICION_COMPRA)?"Compra":(($row["rebms_tipo"]==TIPO_REQUISICION_SERVICIO)?"Servicio":""), "</td>
			   		<td align='center'>", $row["depe_nombre"], "</td>";
					if($row["esta_id"]==$estadoDevuelto){
			    		echo "<td align='center' class='normalNegro' style='color: red;'>", $row["esta_nombre"], "</td>";	
			    	}else if($row["esta_id"]==$estadoAnulado){
			    		echo "<td align='center' class='normalNegro' style='color: gray;'>", $row["esta_nombre"], "</td>";	
			    	}else{
			    		echo "<td align='center'>", $row["esta_nombre"], "</td>";	
			    	}
			    	if($estado == ESTADO_REQUISICION_NO_REVISADAS || $estado == ESTADO_REQUISICION_TODAS){
			    		if ( $row["wfgr_id"] != "0" ) {
			    			echo "<td align='center'>", $row["wfgr_descrip"], "</td>";			    			
			    		} else {
			    			echo "<td align='center'>Finalizado</td>";
			    		}
			    	}
			   		echo "<td align='center'>".$row["solicitante"]."</td>";
					if($row["wfgr_id"]!=$row["wfgr_id_parent"] && $row["wfgr_id"] == $idGrupo && $row["esta_id"]!=$estadoAnulado){
						if($user_perfil_id=="15456"){
			    			echo "<td align='center'>".
			    					"<a href='javascript: verOrdenDeCompra(\"".$row["ordc_id"]."\");' title='Ver detalle de Orden de Compra'>Ver Detalle</a>".
			    					"<br/><a href='javascript: verOrdenDeCompra(\"".$row["ordc_id"]."\",true);' title='Modificar Orden de Compra'>Modificar</a>";
						}else{
							echo "<td align='center'><a href='javascript: verOrdenDeCompra(\"".$row["ordc_id"]."\",true);' title='Revisar Orden de Compra'>Revisar</a>";
						}
			    	}else{
			    		echo "<td align='center'><a href='javascript: verOrdenDeCompra(\"".$row["ordc_id"]."\");' title='Ver detalle de Orden de Compra'>Ver Detalle</a>";
			    	}
			    	echo "</td></tr>\n";
				}
				
				echo "<tr class='td_gray'><td colspan='".(($estado == ESTADO_REQUISICION_NO_REVISADAS || $estado == ESTADO_REQUISICION_TODAS)?(($user_perfil_id!="30400" && $user_perfil_id!="46400")?"10":"9"):(($user_perfil_id!="30400" && $user_perfil_id!="46400")?"9":"8"))."' align='center'>";
				$ventanaActual = ($paginaL%$tamanoVentana==0)?$paginaL/$tamanoVentana:intval($paginaL/$tamanoVentana)+1;
				$ri = (($ventanaActual-1)*$tamanoVentana)+1;
				while($ri<=$ventanaActual*$tamanoVentana && $ri<=$totalPaginas) {
					if($ri==(($ventanaActual-1)*$tamanoVentana)+1 && $ri!=1){
						echo "<a onclick='listarOrdenesDeCompra(".($ri-1).");' style='cursor: pointer;text-decoration: underline;'>&lt;</a> ";
					}
					if($ri==$paginaL){
						echo $ri." ";
					}else{
						echo "<a onclick='listarOrdenesDeCompra(".$ri.");' style='cursor: pointer;text-decoration: underline;'>".$ri."</a> ";
					}
					if($ri==$ventanaActual*$tamanoVentana && $ri<$totalPaginas){
						echo "<a onclick='listarOrdenesDeCompra(".($ri+1).");' style='cursor: pointer;text-decoration: underline;'>&gt;</a> ";
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
		echo "<tr><td>Actualmente no hay &oacute;rdenes de compra</td></tr>";
	}
?>
</table>