<?php 
	ob_start();
	session_start();
	if (isset($_POST['tipoRequ']) && $_POST['tipoRequ'] != "") {
		header("Content-Type: text/html; charset=iso-8859-1\n");
		$tipoRequ = $_POST['tipoRequ'];
	}else{
		$tipoRequ = $tipoRequisicion;	
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
	if($codigo && $codigo!=""){
		$query = 	"SELECT COUNT(soco_id) ".
					"FROM sai_sol_coti ".
					"WHERE soco_id = '".$codigo."' ";
	}else{
		$query = 	"SELECT COUNT(DISTINCT(ssc.soco_id)) ".
					"FROM sai_sol_coti ssc, sai_sol_coti_prov sscp, sai_req_bi_ma_ser srbms ".
					"WHERE ";
		$query .=		"ssc.rebms_id = srbms.rebms_id AND ssc.soco_id = sscp.soco_id ";
		if($codigoCR!=""){
			$query .=	"AND srbms.rebms_id = '".$codigoCR."' ";			
		}
		if($tipoRequ!=TIPO_REQUISICION_TODAS){
			$query .=	"AND srbms.rebms_tipo = ".$tipoRequ." ";			
		}
		if($dependencia != ""){
			$query .=	"AND srbms.depe_id = '".$dependencia."' ";
		}
		if($controlFechas=="true" && $fechaInicio!="" && $fechaFin!=""){
			$query .=	"AND sscp.fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 ";
		}
	}
	$resultadoContador = pg_exec($conexion, $query);
	$numeroFilas = pg_numrows($resultadoContador);
	$contador = 0;
	if($numeroFilas>0){
		$row = pg_fetch_array($resultadoContador, 0);
		$contador = $row[0];
		$totalPaginas = ($contador%$tamanoPagina == 0)?$contador/$tamanoPagina:intval($contador/$tamanoPagina)+1;
		if($codigo && $codigo!=""){
			$query= 	"SELECT ".
							"ssc.soco_id, ".
							"to_char(MAX(sscp.fecha),'DD/MM/YYYY') as soco_fecha, ".
							"MAX(sscp.fecha) as sscp_fecha, ".
							"srbms.rebms_id, ".
							"srbms.rebms_tipo, ".
							"se.esta_nombre, ".
							"sd.depe_nombre, ".
							"sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante ".	
						"FROM sai_req_bi_ma_ser srbms,sai_dependenci sd,sai_estado se, sai_sol_coti ssc, sai_sol_coti_prov sscp, sai_usuario su, sai_empleado sem ".
						"WHERE ".
							"ssc.soco_id = '".$codigo."' AND ".
							"ssc.rebms_id = srbms.rebms_id AND ".
							"ssc.soco_id = sscp.soco_id AND ".
							"ssc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula AND ".
							"srbms.depe_id = sd.depe_id AND ".
							"srbms.esta_id = se.esta_id ".
						"GROUP BY ssc.soco_id, srbms.rebms_id, srbms.rebms_tipo, se.esta_nombre, sd.depe_nombre, solicitante ".
						"ORDER BY sscp_fecha DESC, ssc.soco_id DESC ".
						"LIMIT ".$tamanoPagina." OFFSET ".$desplazamiento;
		}else{
			$query= 	"SELECT ".
							"ssc.soco_id, ".
							"to_char(MAX(sscp.fecha),'DD/MM/YYYY') as soco_fecha, ".
							"MAX(sscp.fecha) as sscp_fecha, ".
							"srbms.rebms_id, ".
							"srbms.rebms_tipo, ".
							"se.esta_nombre, ".
							"sd.depe_nombre, ".
							"sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante ".	
						"FROM sai_req_bi_ma_ser srbms,sai_dependenci sd,sai_estado se, sai_sol_coti ssc, sai_sol_coti_prov sscp, sai_usuario su, sai_empleado sem ".
						"WHERE ".
							"ssc.rebms_id = srbms.rebms_id AND ";
			if($codigoCR!=""){
				$query .=	"srbms.rebms_id = '".$codigoCR."' AND ";			
			}
			if($tipoRequ!=TIPO_REQUISICION_TODAS){
				$query .=	"srbms.rebms_tipo = ".$tipoRequ." AND ";			
			}
			if($dependencia != ""){
				$query .=	"srbms.depe_id = '".$dependencia."' AND ";
			}
			$query .=		"ssc.soco_id = sscp.soco_id AND ".
							"ssc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula AND ";
			if($controlFechas=="true" && $fechaInicio!="" && $fechaFin!=""){
				$query .=	"sscp.fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ";
			}
			$query .=		"srbms.depe_id = sd.depe_id AND ".
							"srbms.esta_id = se.esta_id ".
						"GROUP BY ssc.soco_id, srbms.rebms_id, srbms.rebms_tipo, se.esta_nombre, sd.depe_nombre, solicitante ".
						"ORDER BY sscp_fecha DESC, ssc.soco_id DESC ".
						"LIMIT ".$tamanoPagina." OFFSET ".$desplazamiento;
		}
		$resultado = pg_exec($conexion, $query);
		
		$numeroFilas = pg_numrows($resultado);

	}
?>
<table>
	<tr>
		<td colspan="1" class="normal peq_verde_bold" style="text-align: center;">
			<p>Solicitudes de cotizaci&oacute;n para requisiciones 
			<?php
			if($codigo && $codigo!=""){
				echo "con c&oacute;digo ".$codigo;
			}else{
				echo (($tipoRequ==TIPO_REQUISICION_COMPRA)?"de compra":(($tipoRequ==TIPO_REQUISICION_SERVICIO)?"de servicio":""));
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
					<th><div style="margin-left: 20px;margin-right: 20px;">C&oacute;digo solicitud<br/>cotizaci&oacute;n</div></th>
					<th><div style="margin-left: 20px;margin-right: 20px;">C&oacute;digo<br/>requisici&oacute;n</div></th>
					<th><div style="margin-left: 20px;margin-right: 20px;">Enviada en<br/>fecha</div></th>
					<th><div style="margin-left: 20px;margin-right: 20px;">Tipo<br/>requisici&oacute;n</div></th>
					<th><div style="margin-left: 20px;margin-right: 20px;">Dependencia</div></th>
					<th><div style="margin-left: 20px;margin-right: 20px;">Estado</div></th>
					<th><div style="margin-left: 20px;margin-right: 20px;">Enviada por usuario</div></th>
					<th style="width: 100px;"><div style="margin-left: 20px;margin-right: 20px;">Acci&oacute;n</div></th>
				</tr>
<?
		for($ri = 0; $ri < $numeroFilas; $ri++) {
	    	echo "<tr class='resultados'>\n";
	    	$row = pg_fetch_array($resultado, $ri);
	    	echo "<td align='center'><a href='javascript: verDetalles(\"".$row["soco_id"]."\");' title='Ver detalle de Solicitud de Cotizaci&oacute;n'>".$row["soco_id"]."</a>";
	    	echo "<td align='center'><a href='javascript: verArticulos(\"".$row["rebms_id"]."\");' title='Ver detalle de Requisici&oacute;n'>".$row["rebms_id"]."</a></td>
	   		<td align='center'>", $row["soco_fecha"], "</td>
	   		<td align='center'>", ($row["rebms_tipo"]==TIPO_REQUISICION_COMPRA)?"Compra":(($row["rebms_tipo"]==TIPO_REQUISICION_SERVICIO)?"Servicio":""), "</td>
	   		<td align='center'>", $row["depe_nombre"], "</td>
	   		<td align='center'>", $row["esta_nombre"], "</td>
	   		<td align='center'>".$row["solicitante"]."</td>";
	    	echo "<td align='center'><a href='javascript: verDetalles(\"".$row["soco_id"]."\");' title='Ver detalle de Solicitud de Cotizaci&oacute;n'>Ver Detalle</a>";
	    	echo "</td></tr>\n";
		}
		
		echo "<tr class='td_gray'><td colspan='8' align='center'>";
		$ventanaActual = ($paginaL%$tamanoVentana==0)?$paginaL/$tamanoVentana:intval($paginaL/$tamanoVentana)+1;
		$ri = (($ventanaActual-1)*$tamanoVentana)+1;
		while($ri<=$ventanaActual*$tamanoVentana && $ri<=$totalPaginas) {
			if($ri==(($ventanaActual-1)*$tamanoVentana)+1 && $ri!=1){
				echo "<a onclick='javascript: listarCotizaciones(".($ri-1).");' style='cursor: pointer;text-decoration: underline;'>&lt;</a> ";
			}
			if($ri==$paginaL){
				echo $ri." ";
			}else{
				echo "<a onclick='javascript: listarCotizaciones(".$ri.");' style='cursor: pointer;text-decoration: underline;'>".$ri."</a> ";
			}
			if($ri==$ventanaActual*$tamanoVentana && $ri<$totalPaginas){
				echo "<a onclick='listarCotizaciones(".($ri+1).");' style='cursor: pointer;text-decoration: underline;'>&gt;</a> ";
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
		echo "<tr><td>Actualmente no hay solicitudes de cotizaci&oacute;n</td></tr>";
	}
?>
</table>