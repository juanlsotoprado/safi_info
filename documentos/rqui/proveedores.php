<?php
ob_start();
session_start();
if (isset($_POST['idRequ']) && $_POST['idRequ'] != "") {
	header("Content-Type: text/html; charset=iso-8859-1\n");
	$idRequ = $_POST['idRequ'];
	$tipoRequ = $_POST['tipoRequ'];
	require_once("../../includes/conexion.php");
}
require("../../includes/constantes.php");
$tipoProv = "";
if (isset($_POST['tipoProv']) && $_POST['tipoProv'] != "") {
	$tipoProv = $_POST['tipoProv'];
}else{
	$tipoProv = TIPO_PROVEEDORES_TODO;
}
if($idRequ && $idRequ!=""){
	$query = 	"SELECT rebms_tipo ".
				"FROM sai_req_bi_ma_ser ".
				"WHERE ".
				"rebms_id = '".$idRequ."' ";
	
	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado, 0);
	$rebms_tipo = $row[0];
	$resultadoPartidas = pg_exec($conexion,	"SELECT DISTINCT(sip.part_id), sip.id_item ".
											"FROM sai_rqui_items sri, sai_item_partida sip ".
											"WHERE ".
												"sri.rebms_id = '".$idRequ."' AND ".
												"sri.rbms_item_arti_id = sip.id_item ".
											"ORDER BY sip.id_item ASC");		

	$numeroPartidas = pg_numrows($resultadoPartidas);
	if($numeroPartidas>0){
		if($tipoProv == TIPO_PROVEEDORES_TODO){
			$cadenaArticulos = "(";
			for($i = 0; $i<$numeroPartidas; $i++){
				$row = pg_fetch_array($resultadoPartidas, $i);
				$cadenaArticulos .= "'".$row["part_id"]."',";
			}
			$cadenaArticulos = substr($cadenaArticulos, 0, -1).")";
			$estadoInactivo = "2";
			$resultado = pg_exec($conexion,	"SELECT sp.prov_id_rif,sp.prov_nombre, sev.nombre AS estado, COUNT(id_ramo) ".
											"FROM 
												sai_prov_ramo_secundario sprs, 
												sai_proveedor_nuevo sp 
												LEFT OUTER JOIN safi_edos_venezuela sev ON (sp.id_estado = sev.id) ".
											"WHERE ".
												"sprs.id_ramo IN ".$cadenaArticulos." ".
												"AND upper(sprs.prov_id_rif) like upper(sp.prov_id_rif) ".
												"AND sp.prov_esta_id <> ".$estadoInactivo." ".
											"GROUP BY sp.prov_id_rif, sp.prov_nombre, sev.nombre ".
											"HAVING COUNT(sprs.id_ramo) = ".$numeroPartidas." ".
											"ORDER BY sp.prov_nombre ASC");
			$numeroFilas = pg_numrows($resultado);
			if($numeroFilas>0){
?>
<div style="text-align: center;width: 100%;"><p class="normal peq_verde_bold" style="margin-left: 5px;margin-top: 20px;">Proveedores de todos los Ramos</p></div>
<form id="formularioTodos" name="formularioTodos">
	<table style="width: 100%;">
		<tr>
			<td>
				<table class="tablaalertas" style="width: 100%;" background="../../imagenes/fondo_tabla.gif">
					<tr class="td_gray">
						<th style="width: 20px;"><input type="checkbox" id="controlProveedoresTodos" name="controlProveedoresTodos" onclick="marcarTodosNinguno('Todos');"/></th>
						<th>RIF</th>
						<th>Proveedor</th>
						<th>Estado</th>
					</tr>
	<?
					for($ri = 0; $ri < $numeroFilas; $ri++) {
				    	echo "<tr>\n";
				    	$row = pg_fetch_array($resultado, $ri);
				    	echo " <td><input type='checkbox' id='proveedoresTodos".$row[0]."' name='proveedoresTodos".$row[0]."' value='".$row[0]."' onclick='agregarQuitarProveedor(this);'/></td>
				    	<td style='text-align: center;'>", strtoupper(substr(trim($row[0]),0,1))."-".substr(trim($row[0]),1), "</td>
				    	<td>", $row[1], "</td>
						<td>", $row[2], "</td>
				  		</tr>\n";
					}
					//pg_close($conexion);
	?>
				</table>
			</td>
		</tr>
	</table>
</form>
<br/>
<?php
			}else{
				echo "<p class='normal peq_verde_bold' align='center'>Actualmente no hay proveedores que proporcionen todos los ramos</p>";
			}
		}else if($tipoProv == TIPO_PROVEEDORES_UNO){
			$cadenaArticulos = "(";
			for($i = 0; $i<$numeroPartidas; $i++){
				$row = pg_fetch_array($resultadoPartidas, $i);
				$cadenaArticulos .= "'".$row["part_id"]."',";
			}
			$cadenaArticulos = substr($cadenaArticulos, 0, -1).")";
			$estadoInactivo = "2";
			$resultado = pg_exec($conexion,	"SELECT DISTINCT(spa.part_id),spa.part_nombre ".
															"FROM sai_partida spa, sai_prov_ramo_secundario sprs, sai_proveedor_nuevo sp ".
															"WHERE ".
															"sprs.id_ramo IN ".$cadenaArticulos." AND ".
															"sprs.id_ramo = spa.part_id AND ".
															"upper(sprs.prov_id_rif) like upper(sp.prov_id_rif) AND ".
															"sp.prov_esta_id <> ".$estadoInactivo." ".
															"ORDER BY part_id ASC");
			$numeroFilas = pg_numrows($resultado);
			$nombrePartidas = array();
			for($ri = 0; $ri < $numeroFilas; $ri++) {
				$row = pg_fetch_array($resultado, $ri);
				$nombrePartidas[$ri] = $row[1];
			}
			$indicePartidas = 0;
			
			$resultado = pg_exec($conexion,	"SELECT sprs.id_ramo,sp.prov_id_rif,sp.prov_nombre, sev.nombre AS nombre_estado ".
											"FROM 
												sai_prov_ramo_secundario sprs, 
												sai_proveedor_nuevo sp
												LEFT OUTER JOIN safi_edos_venezuela sev ON (sp.id_estado = sev.id) ".
											"WHERE ".
												"sprs.id_ramo IN ".$cadenaArticulos." ".
												"AND upper(sprs.prov_id_rif) like upper(sp.prov_id_rif) ".
												"AND sp.prov_esta_id <> ".$estadoInactivo." ".
											"ORDER BY sprs.id_ramo,sp.prov_nombre ASC");
			
			$numeroFilas = pg_numrows($resultado);
			
			if($numeroFilas>0){
?>
<div style="text-align: center;width: 100%;"><p class="normal peq_verde_bold" style="margin-left: 5px;margin-top: 20px;margin-bottom: 0px;">Proveedores por Ramo</p></div>
<table style="width: 100%;">
	<tr>
		<td>
<?
				$ramoAnterior = "";
				for($ri = 0; $ri < $numeroFilas; $ri++) {
					$row = pg_fetch_array($resultado, $ri);
					
					if($ramoAnterior != $row[0]){
						if($ramoAnterior != ""){
							$indicePartidas++;
							echo "</table>";
						}
?>
			<table>
				<tr>
					<td><p><b>Partida:</b> <?= $row[0]?></p></td>
					<td style='width: 20px;'></td>
					<td><p><b>Ramo:</b> <?= $nombrePartidas[$indicePartidas]?></p></td>
				</tr>
			</table>
			<table class="tablaalertas" style="width: 100%;" background="../../imagenes/fondo_tabla.gif">
				<tr class="td_gray">
					<th style="width: 20px;"><input type="checkbox" id="controlProveedoresRamo<?= $row[0]?>" name="controlProveedoresRamo<?= $row[0]?>" onclick="marcarTodosNinguno('Ramo<?= $row[0]?>');"/></th>
					<th>RIF</th>
					<th>Proveedor</th>
					<th>Estado</th>
				</tr>
<?php
						$ramoAnterior = $row[0];
					}
			    	echo "<tr>\n";
			    	echo " <td><input type='checkbox' id='proveedoresRamo".$row[0].$row[1]."' name='proveedoresRamo".$row[0].$row[1]."' value='".$row[1]."' onclick='agregarQuitarProveedor(this);'/></td>
			   		<td style='text-align: center;'>", strtoupper(substr(trim($row[1]),0,1))."-".substr(trim($row[1]),1), "</td>
			   		<td>", $row[2], "</td>
					<td>", $row[3], "</td>
			  		</tr>\n";
				}
				//pg_close($conexion);
?>
			</table>
		</td>
	</tr>
</table>
<br/>
<?php
			}else{
				echo "<p class='normal peq_verde_bold' align='center'>Actualmente no hay proveedores que proporcionen alguno de los ramos</p>";
			}
		}else if($tipoProv == TIPO_PROVEEDORES_MATRIZ){
			$cadenaArticulos = "(";
			for($i = 0; $i<$numeroPartidas; $i++){
				$row = pg_fetch_array($resultadoPartidas, $i);
				$cadenaArticulos .= "'".$row["part_id"]."',";
			}
			$cadenaArticulos = substr($cadenaArticulos, 0, -1).")";
			$estadoInactivo = "2";
			$resultado = pg_exec($conexion,	"SELECT sp.prov_nombre, sp.prov_id_rif, sprs.id_ramo, sev.nombre AS nombre_estado ".
											"FROM 
												sai_prov_ramo_secundario sprs, 
												sai_proveedor_nuevo sp
												LEFT OUTER JOIN safi_edos_venezuela sev ON (sp.id_estado = sev.id) ".
											"WHERE ".
												"sprs.id_ramo IN ".$cadenaArticulos." ".
												"AND upper(sprs.prov_id_rif) like upper(sp.prov_id_rif) ".
												"AND sp.prov_esta_id <> ".$estadoInactivo." ".
											"ORDER BY sp.prov_nombre,sprs.id_ramo ASC");
			$numeroFilas = pg_numrows($resultado);
			if($numeroFilas>0){
?>
<div style="text-align: center;width: 100%;"><p class="normal peq_verde_bold" style="margin-left: 5px;margin-top: 20px;">Matriz de Proveedores y Ramos</p></div>
<table style="width: 100%;">
	<tr>
		<td>
			<table class="tablaalertas" style="width: 100%;" background="../../imagenes/fondo_tabla.gif">
				<tr class="td_gray">
					<td colspan="2">
						&nbsp;
					</td>
					<td colspan="<?= $numeroPartidas?>" style="text-align: center;">Ramos</td>
				</tr>
				<tr class="td_gray">
					<td colspan="2">
						<input type="checkbox" id="controlProveedoresMatriz" name="controlProveedoresMatriz" onclick="marcarTodosNinguno('Matriz');"/>
						Proveedores
					</td>
<?php
				for($i = 0; $i<$numeroPartidas; $i++){
					$row = pg_fetch_array($resultadoPartidas, $i);
?>
					<td style='text-align: center;'><?= $row["part_id"]?></td>
<?php
				}
?>
				</tr>
<?
				$proveedorAnterior = "";
				$i = 0;
				for($ri = 0; $ri < $numeroFilas; $ri++) {
					$row = pg_fetch_array($resultado, $ri);
					if($proveedorAnterior != $row[1]){
						if($proveedorAnterior != ""){
							$encontro = false;
							while($i<$numeroPartidas){
				   				echo "<td>&nbsp;</td>";
								$i++;
							}
							echo "</tr>";
						}
						$i = 0;
						echo "<tr>";
			    		echo "<td style='width: 20px;'><input type='checkbox' id='proveedoresMatriz".$row[1]."' name='proveedoresMatriz".$row[1]."' value='".$row[1]."' onclick='agregarQuitarProveedor(this);'/></td>";
			    		echo "<td>".$row[0]." (RIF ".strtoupper(substr(trim($row[1]),0,1))."-".substr(trim($row[1]),1)."). ".$row[3]."</td>";
						$proveedorAnterior = $row[1];
					}
					$encontro = false;
			   		while($i<$numeroPartidas && $encontro==false){
			   			$filaPartidas = pg_fetch_array($resultadoPartidas, $i);
			   			if($filaPartidas["part_id"]==$row[2]){
			   				echo "<td style='text-align: center;'>x</td>";
			   				$encontro = true;	
			   			}else{
			   				echo "<td>&nbsp;</td>";
			   			}						
						$i++;
					}
				}
				$encontro = false;
				while($i<$numeroPartidas){
	   				echo "<td>&nbsp;</td>";
					$i++;
				}
				echo "</tr>";
?>
			</table>
		</td>
	</tr>
</table>
<br/>
<?php
			}else{
				echo "<p class='normal peq_verde_bold' align='center'>Actualmente no hay proveedores que proporcionen alguno de los ramos</p>";
			}
		}
	}
}
if (isset($_POST['idRequ']) && $_POST['idRequ'] != "") {
	pg_close($conexion);
}
?>