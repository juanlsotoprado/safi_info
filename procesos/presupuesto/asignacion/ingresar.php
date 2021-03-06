<?php
require("../../../includes/constantes.php");
require("../../../includes/conexion.php");
$anno_pres=$_SESSION['an_o_presupuesto'];
//$anno_pres=2012; // Cambio temporal para cargar la partida presupuestaria del 2012
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:. Ingresar Asignaci&oacute;n Presupuestaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../../js/lib/jquery/plugins/jquery.min.js"></script>
<script language="JavaScript" src="../../../js/funciones.js"></script>
<script language="JavaScript" src="../../../js/lib/actb.js"></script>
<script language="JavaScript" src="../../../js/lib/Tokenizer.js"></script>
<script language="JavaScript" src="../../../js/crearModificarAsignacionPresupuestaria.js"></script>
</head>
<body class="normal">
	<?php
	
	$msg=$_GET['msg'];
	if($msg=="0"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar la categor&iacute;a program&aacute;tica.</p>";
	}else if($msg=="1"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Error en el formato de la categor&iacute;a program&aacute;tica.</p>";
	}else if($msg=="2"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar la acci&oacute;n espec&iacute;fica.</p>";
	}else if($msg=="3"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar la fuente de financiamiento.</p>";
	}else if($msg=="4"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar la dependencia.</p>";
	}else if($msg=="5"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Falta la cantidad de partidas.</p>";
	}else if($msg=="6"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar al menos una (1) partida para la asingaci&oacute;n presupuestaria.</p>";
	}else if($msg=="7"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>La acci&oacute;n espec&iacute;fica seleccionada ya tiene una asignaci&oacute;n presupuestaria.</p>";
	}
	?>
	<form name="form" method="post" action="ingresarAccion.php" id="form">
		<table align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td colspan="2" class="normalNegroNegrita">
					INGRESAR ASIGNACI&Oacute;N PRESUPUESTARIA
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table class="tablaalertas" width="900px">
						<tr>
							<td width="20%">Proyecto/Acci&oacute;n centralizada<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
								<?php
								$query = 	"SELECT ".
												"id_proyecto_accion, ".
												"nombre, ".
												"pres_anno, ".
												"tipo ".
											"FROM ".
											"( ".
										 		"SELECT ".
													"sp.proy_id as id_proyecto_accion, ".
													"sp.proy_titulo as nombre, ".
													"sp.pre_anno as pres_anno, ".
													"'1' as tipo ".
												"FROM sai_proyecto sp, sai_proy_a_esp spae ".
												"WHERE ".
													"sp.proy_id = spae.proy_id AND ".
													"sp.pre_anno = ".$anno_pres." AND ".
													"sp.pre_anno = spae.pres_anno ".													
												"UNION ".
												"SELECT ".
													"sac.acce_id as id_proyecto_accion, ".
													"sac.acce_denom as nombre, ".
													"sac.pres_anno, ".
													"'0' as tipo ".
												"FROM sai_ac_central sac, sai_acce_esp sae ".
												"WHERE ".
													"sac.acce_id = sae.acce_id AND ".
													"sac.pres_anno = ".$anno_pres." AND ".
													"sac.pres_anno = sae.pres_anno ".
											") as s ".
											"GROUP BY s.tipo, s.pres_anno, s.nombre, id_proyecto_accion ".
											"ORDER BY s.tipo DESC, s.pres_anno, s.nombre ASC";
								$resultado = pg_exec($conexion, $query);
								?>
								<select id="categoriaProgramatica" name="categoriaProgramatica" class="normalNegro" onchange="cambiarAccionEspecifica();">
								<?php
									while($row=pg_fetch_array($resultado)){
										$nombre = $row["nombre"];
										if(strlen($row["nombre"])>120){
											$nombre = substr($row["nombre"], 0, 120)."...";	
										}
										echo "<option value='".$row["tipo"]."|".$row["pres_anno"]."|".$row["id_proyecto_accion"]."'>".(($row["tipo"]=="1")?"Proy (a&ntilde;o ".$row["pres_anno"]."): ":"Acc (a&ntilde;o ".$row["pres_anno"]."): ").$nombre."</option>";
									}
								?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="20%">Acci&oacute;n espec&iacute;fica<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
								<?php 
								$query = 	"SELECT ".
												"id_proyecto_accion, ".
												"tipo, ".
												"id_accion_especifica, ".
												"nombre, ".
												"pres_anno, ".
												"centro_gestor, ".
												"centro_costo ".
											"FROM ".
											"(".
												"SELECT ".
													"spae.proy_id as id_proyecto_accion, ".
													"'1' as tipo, ".
													"spae.paes_id as id_accion_especifica, ".
													"spae.paes_nombre as nombre, ".
													"spae.pres_anno, ".
													"spae.centro_gestor, ".
													"spae.centro_costo, ".
													"sp.proy_titulo as nombre_categoria ".
												"FROM sai_proy_a_esp spae, sai_proyecto sp ".
												"WHERE ".
													"spae.pres_anno = ".$anno_pres." AND ".
													"spae.pres_anno = sp.pre_anno AND ".								
													"spae.proy_id = sp.proy_id ".
												"UNION ".
												"SELECT ".
													"sae.acce_id as id_proyecto_accion, ".
													"'0' as tipo, ".
													"sae.aces_id as id_accion_especifica, ".
													"sae.aces_nombre as nombre, ".
													"sae.pres_anno, ".
													"sae.centro_gestor, ".
													"sae.centro_costo, ".
													"sac.acce_denom as nombre_categoria ".
												"FROM sai_acce_esp sae, sai_ac_central sac ".
												"WHERE ".
													"sae.pres_anno = ".$anno_pres." AND ".
													"sae.pres_anno = sac.pres_anno AND ".
													"sae.acce_id = sac.acce_id ".
											") as s ".
											"ORDER BY s.tipo DESC, s.pres_anno, s.nombre_categoria, s.centro_gestor, s.centro_costo, s.id_accion_especifica";
								$resultado = pg_exec($conexion, $query);
								$idProyectoAccionAnterior = "";
								$idPrimerProyectoAccion = "";
								$idPresAnnoAnterior = "";
								$idPrimerPresAnno = "";
								$tipoAnterior = "";
								$primerTipo = "";
								?>
								<script>
									var proyectoAccion = new Array();
									var accionEspecifica = new Array();
								</script>
								<select id="accionEspecifica" name="accionEspecifica" class="normalNegro">
								<?php
									while($row=pg_fetch_array($resultado)){
										if($idProyectoAccionAnterior=="" || $idProyectoAccionAnterior!=$row["id_proyecto_accion"] || $idPresAnnoAnterior!=$row["pres_anno"] || $tipoAnterior!=$row["tipo"]){
											if($idPrimerProyectoAccion==""){
												$idPrimerProyectoAccion = $row["id_proyecto_accion"];
												$idPrimerPresAnno = $row["pres_anno"];
												$primerTipo = $row["tipo"];
											}
											?>
											<script>
												proyectoAccion[proyectoAccion.length] = '<?= $row["id_proyecto_accion"]?>';
												accionEspecifica[accionEspecifica.length] = new Array();
											</script>
											<?php
											$idProyectoAccionAnterior = $row["id_proyecto_accion"];
											$idPresAnnoAnterior = $row["pres_anno"];
											$tipoAnterior = $row["tipo"];
										}
										$nombre = $row["nombre"];
										if(strlen($row["nombre"])>120){
											$nombre = substr($row["nombre"], 0, 120)."...";	
										}
										if($idPrimerProyectoAccion==$row["id_proyecto_accion"] && $idPrimerPresAnno==$row["pres_anno"] && $primerTipo==$row["tipo"]){
											if($idAccionEspecifica==$row["id_accion_especifica"]){
												$descripcionAccionEspecifica="(".$row["centro_gestor"]."/".$row["centro_costo"].") ".$row["nombre"];
												echo "<option value='".$row["id_accion_especifica"]."' selected='selected'>"."(".$row["centro_gestor"]."/".$row["centro_costo"].") ".$nombre."</option>";												
											}else{
												echo "<option value='".$row["id_accion_especifica"]."'>"."(".$row["centro_gestor"]."/".$row["centro_costo"].") ".$nombre."</option>";
											}
										}
										?>
										<script>
											accionEspecifica[accionEspecifica.length-1][accionEspecifica[accionEspecifica.length-1].length] = new Array(2);
											accionEspecifica[accionEspecifica.length-1][accionEspecifica[accionEspecifica.length-1].length-1][0] = '<?= $row["id_accion_especifica"]?>';
											accionEspecifica[accionEspecifica.length-1][accionEspecifica[accionEspecifica.length-1].length-1][1] = '<?= "(".$row["centro_gestor"]."/".$row["centro_costo"].") ".$nombre?>';
										</script>
										<?php
									}
								?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="20%">Fuente de financiamiento<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
								<select class="normalNegro" id="fuenteFinanciamiento" name="fuenteFinanciamiento">
									<?php
									$estadoAnulado = "15";
									$query = 	"SELECT fuef_id,fuef_descripcion ".
												"FROM sai_fuente_fin ".
												"WHERE ".
												"esta_id <> ".$estadoAnulado." ".
												"ORDER BY fuef_descripcion";
									$resultado = pg_exec($conexion, $query);
									$numeroFilas = pg_numrows($resultado);
									for($i = 0; $i < $numeroFilas; $i++) {
										$row = pg_fetch_array($resultado, $i);
									?>
										<option value="<?= $row["fuef_id"]?>"><?=$row["fuef_descripcion"]?></option>
									<?php 
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="20%">Dependencia<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
								<select class="normalNegro" id="dependencia" name="dependencia">
									<?php
									$nivelOficinaGerencia = "4";
									$nivelPresidencia = "3";
									$query = 	"SELECT depe_id,depe_nombre ".
												"FROM sai_dependenci ".
												"WHERE ".
												"(depe_nivel = ".$nivelOficinaGerencia." OR depe_nivel = ".$nivelPresidencia.") AND ".
												"esta_id <> ".$estadoAnulado." ".
												"ORDER BY depe_nombre";
									$resultado = pg_exec($conexion, $query);
									$numeroFilas = pg_numrows($resultado);
									for($i = 0; $i < $numeroFilas; $i++) {
										$row = pg_fetch_array($resultado, $i);
									?>
										<option value="<?= $row["depe_id"]?>"><?=$row["depe_nombre"]?></option>
									<?php 
										}
									?>
								</select>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="hidden" name="tamanoPartidas" id="tamanoPartidas"/>
					<table width="900px" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
						<tr>
							<td colspan="5">
								Partida<span class="peq_naranja" style="margin-right: 5px;">(*)</span>
								<input autocomplete="off" size="101" type="text" id="partida" name="partida" class="normalNegro"/>
								<br/>Introduzca el n&uacute;mero de partida o una palabra contenida en la descripci&oacute;n de la misma.
								<?php
									$query = 	"SELECT ".
													"sp.part_id, ".
													"sp.part_nombre ".
												"FROM sai_partida sp ".
												"WHERE ".
													"sp.esta_id <> 15 AND ".	
													"sp.pres_anno = ".$anno_pres." AND ".
													"sp.part_id NOT LIKE '%.00.00' ".
												"ORDER BY sp.part_id";
									$resultado = pg_exec($conexion, $query);
									$arreglo = "";
									$idsPartidas = "";
									$nombresPartidas = "";
									while($row=pg_fetch_array($resultado)){
										$idsPartidas .= "'".$row["part_id"]."',";
										$nombresPartidas .= "'".str_replace("\n"," ",$row["part_nombre"])."',";
										$arreglo .= "'".$row["part_id"]." : ".str_replace("\n"," ",$row["part_nombre"])."',";
									}
									$arreglo = substr($arreglo, 0, -1);
									$idsPartidas = substr($idsPartidas, 0, -1);
									$nombresPartidas = substr($nombresPartidas, 0, -1);
								?>
								<script>
									var partidasAMostrar = new Array(<?= $arreglo?>);
									var idsPartidas = new Array(<?= $idsPartidas?>);
									var nombresPartidas = new Array(<?= $nombresPartidas?>);
									actb(document.getElementById('partida'),partidasAMostrar);
								</script>								
							</td>
						</tr>
						<tr>
							<td width="22%">
								1er. Trimestre
								<input class="normalNegro" type="text" id="1erTrimestre" name="1erTrimestre" size="15" onkeyup="validarDecimal(this);" maxlength="20"/>
							</td>
							<td width="22%">
								2do. Trimestre
								<input class="normalNegro" type="text" id="2doTrimestre" name="2doTrimestre" size="15" onkeyup="validarDecimal(this);" maxlength="20"/>
							</td>
							<td width="22%">
								3er. Trimestre
								<input class="normalNegro" type="text" id="3erTrimestre" name="3erTrimestre" size="15" onkeyup="validarDecimal(this);" maxlength="20"/>
							</td>
							<td width="22%">
								4to. Trimestre
								<input class="normalNegro" type="text" id="4toTrimestre" name="4toTrimestre" size="15" onkeyup="validarDecimal(this);" maxlength="20"/>
							</td>
							<td width="12%" align="right"><input type="button" value="Agregar" onclick="javascript:agregarPartida();" class="normalNegro"/></td>
						</tr>
						<tr>
							<td colspan="5">
								<table width="100%" class="tablaalertas" id="tbl_mod">
									<tr class="td_gray normalNegrita">
										<td align="center" width="12%">Partida</td>
										<td align="center" width="30%">Denominaci&oacute;n</td>
										<td align="center" width="12%">1er. Trimestre</td>
										<td align="center" width="12%">2do. Trimestre</td>
										<td align="center" width="12%">3er. Trimestre</td>
										<td align="center" width="12%">4to. Trimestre</td>
										<td align="center" width="10%">&nbsp;</td>
									</tr>
									<tbody id="partidas">
									</tbody>
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
	</form>
	<br/>
	<div id="divAcciones" style="text-align: center;">
		<input class="normalNegro" type="button" value="Crear" onclick="validarCodigoAccionEspecifica();"/>
	</div>
	<div class="ptotal"><span class="peq_naranja">(*)</span> Campo obligatorio</div>
</body>
</html>