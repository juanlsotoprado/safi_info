<?php
require("../../../includes/constantes.php");
require("../../../includes/conexion.php");
$codigo=trim($_REQUEST['codigo']);
$accion=trim($_REQUEST['accion']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:. Detalle Asignaci&oacute;n Presupuestaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body class="normal">
<?php
if($codigo && $codigo!=""){
	$query = 	"SELECT ".
					"sff.fuef_descripcion, ".
					"sd.depe_nombre, ".
					"CASE sf.form_tipo ".
						"WHEN 1::BIT(1) THEN (".
												"SELECT sp.proy_titulo ".
												"FROM sai_proyecto sp ".
												"WHERE ".
													"sp.proy_id = sf.form_id_p_ac AND ".
													"sp.pre_anno = sf.pres_anno ".
											") ".
						"WHEN 0::BIT(1) THEN (".
												"SELECT sac.acce_denom ".
												"FROM sai_ac_central sac ".
												"WHERE ".
													"sac.acce_id = sf.form_id_p_ac AND ".
													"sac.pres_anno = sf.pres_anno ".
											") ".
					"ELSE '' END AS nombre_categoria, ".
					"CASE sf.form_tipo ".
						"WHEN 1::BIT(1) THEN (".
												"SELECT spae.paes_nombre ".
												"FROM sai_proy_a_esp spae ".
												"WHERE ".
													"spae.proy_id = sf.form_id_p_ac AND ".
													"spae.paes_id = sf.form_id_aesp AND ".
													"spae.pres_anno = sf.pres_anno ".
											") ".
						"WHEN 0::BIT(1) THEN (".
												"SELECT sae.aces_nombre ".
												"FROM sai_acce_esp sae ".
												"WHERE ".
													"sae.acce_id = sf.form_id_p_ac AND ".
													"sae.aces_id = sf.form_id_aesp AND ".
													"sae.pres_anno = sf.pres_anno ".
											") ".
					"ELSE '' END AS nombre_accion_especifica ".
				"FROM ".
					"sai_forma_1125 sf, ".
					"sai_fuente_fin sff, ".
					"sai_dependenci sd ".
				"WHERE ".
					"sf.form_id = '".$codigo."' AND ".
					"sf.fuente_financiamiento = sff.fuef_id AND ".
					"sf.depe_cosige = sd.depe_id ";	
	$resultadoQuery=pg_query($conexion,$query);
	$row=pg_fetch_array($resultadoQuery);
	$nombreCategoria = $row["nombre_categoria"];
	$nombreAccionEspecifica = $row["nombre_accion_especifica"];
	$fuenteFinanciamiento = $row["fuef_descripcion"];
	$depe_cosige = $row["depe_nombre"];	
	?>
	<table align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td colspan="2" class="normalNegroNegrita">
				DETALLE ASIGNACI&Oacute;N PRESUPUESTARIA
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="tablaalertas" width="900px">
					<tr>
						<td width="20%">C&oacute;digo</td>
						<td width="80%" class="normalNegro">
							<?= $codigo?>
						</td>
					</tr>
					<tr>
						<td width="20%">Proyecto/Acci&oacute;n centralizada</td>
						<td width="80%" class="normalNegro">
							<?= $nombreCategoria?>
						</td>
					</tr>
					<tr>
						<td width="20%">Acci&oacute;n espec&iacute;fica<span class="peq_naranja">(*)</span></td>
						<td width="80%" class="normalNegro">
							<?= $nombreAccionEspecifica?>
						</td>
					</tr>
					<tr>
						<td width="20%">Fuente de financiamiento<span class="peq_naranja">(*)</span></td>
						<td width="80%" class="normalNegro">
							<?=$fuenteFinanciamiento?>
						</td>
					</tr>
					<tr>
						<td width="20%">Dependencia<span class="peq_naranja">(*)</span></td>
						<td width="80%" class="normalNegro">
							<?=$depe_cosige?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<?php
				$query = 	"SELECT ".
								"sfd.part_id, ".
								"sfd.fodt_monto, ".
								"sfd.fodt_mes, ".
								"sp.part_nombre ".
							"FROM sai_fo1125_det sfd, sai_partida sp ".
							"WHERE ".
								"sfd.form_id = '".$codigo."' AND ".
								"sfd.part_id = sp.part_id AND ".
								"sfd.pres_anno = sp.pres_anno ".
							"ORDER BY sfd.part_id, sfd.fodt_mes";
				$resultadoPartidas = pg_exec($conexion, $query);
				
				$query = 	"SELECT ".
								"DISTINCT(sp.part_id), ".
								"sp.part_nombre ".
							"FROM sai_fo1125_det sfd, sai_partida sp ".
							"WHERE ".
								"sfd.form_id = '".$codigo."' AND ".
								"(SUBSTRING(sfd.part_id FROM 1 FOR 4)||'.00.00.00') = sp.part_id AND ".
								"sfd.pres_anno = sp.pres_anno ".
							"ORDER BY sp.part_id";
				$resultadoPartidasPrimerOrden = pg_exec($conexion, $query);
				$nombresPartidasPrimerOrden = array();
				while($row=pg_fetch_array($resultadoPartidasPrimerOrden)){
					$nombresPartidasPrimerOrden[sizeof($nombresPartidasPrimerOrden)]=$row["part_nombre"];
				}
				?>
				<table width="900px" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
					<tr>
						<td colspan="5">
							<table width="100%" class="tablaalertas" id="tbl_mod">
								<tr class="td_gray normalNegrita">
									<td align="center" width="12%">Partida</td>
									<td align="center" width="28%">Denominaci&oacute;n</td>
									<td align="center" width="12%">1er. Trimestre</td>
									<td align="center" width="12%">2do. Trimestre</td>
									<td align="center" width="12%">3er. Trimestre</td>
									<td align="center" width="12%">4to. Trimestre</td>
									<td align="center" width="12%">Total</td>
								</tr>
								<tbody id="partidas">
								<?php
								$partidaAnterior = "";
								$anteriores = 1;
								$multiplicador = 3;
								$totalPartida = 0;
								$total1erTrimestre = 0;
								$total2doTrimestre = 0;
								$total3erTrimestre = 0;
								$total4toTrimestre = 0;
								$total = 0;
								$partidaAnteriorPrimerOrden = "";
								$totalPrimerOrden1erTrimestre = 0;
								$totalPrimerOrden2doTrimestre = 0;
								$totalPrimerOrden3erTrimestre = 0;
								$totalPrimerOrden4toTrimestre = 0;
								$totalPrimerOrden=0;
								$indiceNombresPartidasPrimerOrden = 0;
								while($row=pg_fetch_array($resultadoPartidas)){
									if($partidaAnteriorPrimerOrden==""){
										$partidaAnteriorPrimerOrden=substr($row["part_id"], 0, 4).".00.00.00";
									}
										
									if($partidaAnterior!=$row["part_id"]){
										if($partidaAnterior!=""){
											while($anteriores<12){
												echo '<td align="right">0,00</td>';
												$anteriores+=$multiplicador;
											}
										?>
											<td align="right" valign="top"><?= number_format($totalPartida,2,',','.')?></td>
										</tr>
										<?php
											if($partidaAnteriorPrimerOrden!=(substr($row["part_id"], 0, 4).".00.00.00")){
										?>
												<tr class="normalNegroNegrita" style="color: #35519B;">
													<td align="center"><?= $partidaAnteriorPrimerOrden?></td>
													<td><?= $nombresPartidasPrimerOrden[$indiceNombresPartidasPrimerOrden]?></td>
													<td align="right"><?= number_format($totalPrimerOrden1erTrimestre,2,',','.')?></td>
													<td align="right"><?= number_format($totalPrimerOrden2doTrimestre,2,',','.')?></td>
													<td align="right"><?= number_format($totalPrimerOrden3erTrimestre,2,',','.')?></td>
													<td align="right"><?= number_format($totalPrimerOrden4toTrimestre,2,',','.')?></td>
													<td align="right"><?= number_format($totalPrimerOrden,2,',','.')?></td>
												</tr>
										<?	
												$partidaAnteriorPrimerOrden=substr($row["part_id"], 0, 4).".00.00.00";
												$totalPrimerOrden1erTrimestre = 0;
												$totalPrimerOrden2doTrimestre = 0;
												$totalPrimerOrden3erTrimestre = 0;
												$totalPrimerOrden4toTrimestre = 0;
												$totalPrimerOrden=0;
												$indiceNombresPartidasPrimerOrden++;
											}
										}
										$partidaAnterior = $row["part_id"];
										$anteriores = 1;
										$totalPartida = 0;
										?>
										<tr class="normalNegro">
											<td align="center" valign="top">
												<?= $row["part_id"]?>
											</td>
											<td valign="top"><?= $row["part_nombre"]?></td>
										<?php
									}
									while($anteriores<$row["fodt_mes"]){
										echo '<td align="right">0,00</td>';
										$anteriores+=$multiplicador;
									}
									$anteriores=$row["fodt_mes"]+$multiplicador;
									$totalPartida+=$row["fodt_monto"];
									$total+=$row["fodt_monto"];
									if($row["fodt_mes"]==1){
										$total1erTrimestre+=$row["fodt_monto"];
										$totalPrimerOrden1erTrimestre+=$row["fodt_monto"];
										$totalPrimerOrden+=$row["fodt_monto"];
									}else if($row["fodt_mes"]==4){
										$total2doTrimestre+=$row["fodt_monto"];
										$totalPrimerOrden2doTrimestre+=$row["fodt_monto"];
										$totalPrimerOrden+=$row["fodt_monto"];
									}else if($row["fodt_mes"]==7){
										$total3erTrimestre+=$row["fodt_monto"];
										$totalPrimerOrden3erTrimestre+=$row["fodt_monto"];
										$totalPrimerOrden+=$row["fodt_monto"];
									}else if($row["fodt_mes"]==10){
										$total4toTrimestre+=$row["fodt_monto"];
										$totalPrimerOrden4toTrimestre+=$row["fodt_monto"];
										$totalPrimerOrden+=$row["fodt_monto"];
									}
									echo '<td align="right" valign="top">'.number_format($row["fodt_monto"],2,',','.').'</td>';
								}
								while($anteriores<12){
									echo '<td align="right">0,00</td>';
									$anteriores+=$multiplicador;
								}
								?>
										<td align="right" valign="top"><?= number_format($totalPartida,2,',','.')?></td>
									</tr>
								</tbody>
								<?php
								if($partidaAnteriorPrimerOrden!=""){
								?>
									<tr class="normalNegroNegrita" style="color: #35519B;">
										<td align="center"><?= $partidaAnteriorPrimerOrden?></td>
										<td><?= $nombresPartidasPrimerOrden[$indiceNombresPartidasPrimerOrden]?></td>
										<td align="right"><?= number_format($totalPrimerOrden1erTrimestre,2,',','.')?></td>
										<td align="right"><?= number_format($totalPrimerOrden2doTrimestre,2,',','.')?></td>
										<td align="right"><?= number_format($totalPrimerOrden3erTrimestre,2,',','.')?></td>
										<td align="right"><?= number_format($totalPrimerOrden4toTrimestre,2,',','.')?></td>
										<td align="right"><?= number_format($totalPrimerOrden,2,',','.')?></td>
									</tr>
								<?	
								}
								?>
								<tr class="normalNegroNegrita">
									<td>&nbsp;</td>
									<td align="center">TOTAL</td>
									<td align="right"><?= number_format($total1erTrimestre,2,',','.')?></td>
									<td align="right"><?= number_format($total2doTrimestre,2,',','.')?></td>
									<td align="right"><?= number_format($total3erTrimestre,2,',','.')?></td>
									<td align="right"><?= number_format($total4toTrimestre,2,',','.')?></td>
									<td align="right"><?= number_format($total,2,',','.')?></td>
								</tr>
								<tr>
									<td height="19" colspan="7">&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<br/>
	<center>
	<span class="normalNegrita">
	<?php
	if($accion=="generar"){
		echo "Usted ha creado con &eacute;xito la asignaci&oacute;n presupuestaria <span class='resultados'>\"".$codigo."\"</span>";
	}else if($accion=="modificar"){
		echo "Usted ha modificado con &eacute;xito la asignaci&oacute;n presupuestaria <span class='resultados'>\"".$codigo."\"</span>";
	}
	?>
	</span>
	</center>
<?php
}
?>
</body>
</html>