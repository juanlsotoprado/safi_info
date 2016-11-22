<?php
require("../../includes/constantes.php");
require("../../includes/perfiles/constantesPerfiles.php");
require("../../includes/conexion.php");
$tipo=trim($_REQUEST["tipo"]);
$codigo=trim($_REQUEST["codigo"]);
$accion=trim($_REQUEST["accion"]);
$user_perfil_id = $_SESSION['user_perfil_id'];
$user_depe_id = substr($user_perfil_id, 2);
$usuario = $_SESSION['login'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:. Detalle de <?= (($tipo=="memo")?"Memorando":(($tipo=="ofic")?"Oficio":""))?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<?php
/*if(	(substr($user_perfil_id, 0, 2)."000")==PERFIL_ASISTENTE_ADMINISTRATIVO
	 || $user_perfil_id==PERFIL_ASISTENTE_EJECUTIVO
	 || $user_perfil_id==PERFIL_ASISTENTE_PRESIDENCIA
	 || (substr($user_perfil_id, 0, 2)."000")==PERFIL_JEFE
	 || (substr($user_perfil_id, 0, 2)."000")==PERFIL_GERENTE
	 || (substr($user_perfil_id, 0, 2)."000")==PERFIL_DIRECTOR
	 || $user_perfil_id==PERFIL_DIRECTOR_EJECUTIVO
	 || $user_perfil_id==PERFIL_PRESIDENTE){*/
?>
<script language="javascript">
	function generarPdf1(){
		location.href = "memo_PDF.php?formato=L&tipo=<?=$tipo?>&codigo=<?= $codigo?>";
	}
	function generarPdf2(){
		location.href = "memo_PDF.php?formato=F&tipo=<?=$tipo?>&codigo=<?= $codigo?>";
	}
</script>
<?php
/*}*/
?>
</head>
<body class="normal">
<?php
if($tipo && $tipo!="" && $codigo && $codigo!=""){
	if($tipo=="memo"){
		$query = 	"SELECT ".
						"to_char(sm.fecha,'DD/MM/YYYY') AS fecha_cadena, ".
						"sm.depe_id, ".
						"sd.depe_nombre AS dependencia, ".
						"sma.nombre AS asunto, ".
						"sm.descripcion, ".
						"sm.anexos, ".
						"sm.despedida, ".
						"sm.alineacion_despedida, ".
						"sm.coletilla, ".
						"sm.usua_login, ".
						"sm.firma_presidencia, ".
						"sm.firma_administracion ".
					"FROM ".
						"sai_memorando sm, ".
						"sai_dependenci sd, ".
						"sai_memorando_asunto sma ".
					"WHERE ".
						"sm.memo_id = '".$codigo."' AND ".
						"sm.depe_id = sd.depe_id AND ".
						"sm.id_asunto = sma.id ";
	}else if($tipo=="ofic"){
		$query = 	"SELECT ".
						"to_char(so.fecha,'DD/MM/YYYY') AS fecha_cadena, ".
						"so.depe_id, ".
						"sd.depe_nombre AS dependencia, ".
						"sma.nombre AS asunto, ".
						"so.descripcion, ".
						"so.anexos, ".
						"so.despedida, ".
						"so.alineacion_despedida, ".
						"so.coletilla, ".
						"so.usua_login ".
					"FROM ".
						"sai_oficio so, ".
						"sai_dependenci sd, ".
						"sai_memorando_asunto sma ".		
					"WHERE ".
						"so.ofic_id = '".$codigo."' AND ".
						"so.depe_id = sd.depe_id AND ".
						"so.id_asunto = sma.id ";
	}
	$resultado=pg_query($conexion,$query);
	$row = pg_fetch_array($resultado, 0);
	$fecha = $row["fecha_cadena"];
	$depe_id = $row["depe_id"];
	$dependencia = $row["dependencia"];
	$asunto = $row["asunto"];
	$descripcion = $row["descripcion"];
	$anexos = str_replace("\n","<br/>",$row["anexos"]);
	$despedida = $row["despedida"];
	$alineacionDespedida = $row["alineacion_despedida"];
	$coletilla = $row["coletilla"];
	$usua_login = $row["usua_login"];
	$firmaPresidencia = $row["firma_presidencia"];
	$firmaAdministracion = $row["firma_administracion"];
?>
<table align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray">
		<td colspan="2" class="normalNegroNegrita">
			DETALLE DE <?= (($tipo=="memo")?"MEMORANDO":(($tipo=="ofic")?"OFICIO":""))." ".$codigo?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table class="tablaalertas" width="900px">
				<tr class="td_gray">
					<td colspan="5" class="normalNegrita">Destinatario</td>
				</tr>
				<tr>
					<td colspan="2">
						<table width="100%">
							<tr>
								<td colspan="2">
									<?php
									if($tipo=="memo"){
										$query = 	"SELECT ".
														"smp.cedula, ".
														"UPPER(se.empl_nombres || ' ' || se.empl_apellidos) AS nombre, ".
														"UPPER(sc.carg_nombre) AS cargo, ".
														"UPPER(sd.depe_nombre) AS dependencia ".
													"FROM ".
														"sai_memorando_para smp, ".
														"sai_empleado se, ".
														"sai_dependenci sd, ".
														"sai_cargo sc ".
													"WHERE ".
														"smp.memo_id = '".$codigo."' AND ".
														"smp.cedula = se.empl_cedula AND ".
														"se.carg_fundacion = sc.carg_fundacion AND ".
														"se.depe_cosige = sd.depe_id ".
													"ORDER BY sc.carg_fundacion, smp.cedula";
									}else if($tipo=="ofic"){
										$query = 	"SELECT ".
														"sop.cedula, ".
														"UPPER(se.empl_nombres || ' ' || se.empl_apellidos) AS nombre, ".
														"UPPER(sc.carg_nombre) AS cargo, ".
														"UPPER(sd.depe_nombre) AS dependencia ".
													"FROM ".
														"sai_oficio_para sop, ".
														"sai_empleado se, ".
														"sai_dependenci sd, ".
														"sai_cargo sc ".
													"WHERE ".
														"sop.ofic_id = '".$codigo."' AND ".
														"sop.cedula = se.empl_cedula AND ".
														"se.carg_fundacion = sc.carg_fundacion AND ".
														"se.depe_cosige = sd.depe_id ".
													"ORDER BY sc.carg_fundacion, sop.cedula";
									}
									$resultado=pg_query($conexion,$query);
									?>
									<table id="ccs" align="center" class="tablaalertas" width="100%">
										<tr>
											<td width="10%" valign="top" class="normalNegrita">Para:</td>
											<td width="90%" class="normalNegro">
												<table class="normal" width="100%">
													<tr>
														<td colspan="5">
															<table id="tbl_mod_paras" align="center" class="tablaalertas" width="100%">
																<tr class="td_gray normalNegrita">
																	<td align="center" width="15%">C&eacute;dula</td>
																	<td align="center" width="30%">Nombre</td>
																	<td align="center" width="25%">Cargo</td>
																	<td align="center" width="30%">Dependencia</td>
																</tr>
																<tbody id="bodyParas">
																<?php
																while($row=pg_fetch_array($resultado)){
																?>
																	<tr class="normalNegro">
																		<td align="center" valign="top">
																			<?= $row["cedula"]?>
																		</td>
																		<td valign="top">
																			<?= $row["nombre"]?>
																		</td>
																		<td valign="top" align="center">
																			<?= $row["cargo"]?>
																		</td>
																		<td valign="top" align="center">
																			<?= $row["dependencia"]?>
																		</td>
																	</tr>
																<?php
																}																		
																?>
																</tbody>
																<tr>
																	<td colspan="4">&nbsp;</td>																
																</tr> 															
															</table>													
														</td>
													</tr>												
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="2">
								<?php
									if($tipo=="memo"){
										$query = 	"SELECT ".
														"smc.cedula, ".
														"UPPER(se.empl_nombres || ' ' || se.empl_apellidos) AS nombre, ".
														"UPPER(sc.carg_nombre) AS cargo, ".
														"UPPER(sd.depe_nombre) AS dependencia ".
													"FROM ".
														"sai_memorando_cc smc, ".
														"sai_empleado se, ".
														"sai_dependenci sd, ".
														"sai_cargo sc ".
													"WHERE ".
														"smc.memo_id = '".$codigo."' AND ".
														"smc.cedula = se.empl_cedula AND ".
														"se.carg_fundacion = sc.carg_fundacion AND ".
														"se.depe_cosige = sd.depe_id ".
													"ORDER BY sc.carg_fundacion, smc.cedula";
									}else if($tipo=="ofic"){
										$query = 	"SELECT ".
														"soc.cedula, ".
														"UPPER(se.empl_nombres || ' ' || se.empl_apellidos) AS nombre, ".
														"UPPER(sc.carg_nombre) AS cargo, ".
														"UPPER(sd.depe_nombre) AS dependencia ".
													"FROM ".
														"sai_oficio_cc soc, ".
														"sai_empleado se, ".
														"sai_dependenci sd, ".
														"sai_cargo sc ".
													"WHERE ".
														"soc.ofic_id = '".$codigo."' AND ".
														"soc.cedula = se.empl_cedula AND ".
														"se.carg_fundacion = sc.carg_fundacion AND ".
														"se.depe_cosige = sd.depe_id ".
													"ORDER BY sc.carg_fundacion, soc.cedula";												
									}
									$resultado=pg_query($conexion,$query);
									?>
									<table id="ccs" align="center" class="tablaalertas" width="100%">
										<tr>
											<td width="10%" valign="top" class="normalNegrita">Cc:</td>
											<td width="90%" class="normalNegro">
												<table class="normal" width="100%">
													<tr>
														<td colspan="5">
															<table id="tbl_mod_ccs" align="center" class="tablaalertas" width="100%">
																<tr class="td_gray normalNegrita">
																	<td align="center" width="15%">C&eacute;dula</td>
																	<td align="center" width="30%">Nombre</td>
																	<td align="center" width="25%">Cargo</td>
																	<td align="center" width="30%">Dependencia</td>
																</tr>
																<tbody id="bodyCcs">
																<?php
																while($row=pg_fetch_array($resultado)){
																?>
																	<tr class="normalNegro">
																		<td align="center" valign="top">
																			<?= $row["cedula"]?>
																		</td>
																		<td valign="top">
																			<?= $row["nombre"]?>
																		</td>
																		<td valign="top" align="center">
																			<?= $row["cargo"]?>
																		</td>
																		<td valign="top" align="center">
																			<?= $row["dependencia"]?>
																		</td>
																	</tr>
																<?php
																}																		
																?>
																</tbody>
																<tr>
																	<td colspan="4">&nbsp;</td>																
																</tr>
															</table>													
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>											
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table class="tablaalertas" width="900px">
				<tr>
					<td width="20%">De:</td>
					<td width="80%" class="normalNegro">
						<?php 
							if($usua_login=="15023598"){
								echo "Jefatura de Personal";
							}else{
								echo $dependencia;
							}
						?>
					</td>
				</tr>
				<tr>
					<td width="20%">Asunto:</td>
					<td width="80%" class="normalNegro">
						<?= $asunto?>
					</td>
				</tr>
				<tr>
					<td width="20%">Fecha:</td>
					<td width="80%" class="normalNegro">
						<?= $fecha?>
					</td>
				</tr>
				<tr>
					<td width="20%" valign="top">Descripci&oacute;n:</td>
					<td width="80%" class="normalNegro">
				  		<?= $descripcion?>
					</td>
				</tr>
				<tr>
					<td width="20%" valign="top">Despedida:</td>
					<td width="80%" class="normalNegro">
						<?= $despedida?>
						<span style="margin-left: 5px" class="normal">Alineaci&oacute;n de despedida: </span>
						<?php
							if($alineacionDespedida=="left") echo "Izquierda";
							if($alineacionDespedida=="center") echo "Centro";
							if($alineacionDespedida=="right") echo "Derecha";
						?>
					</td>
				</tr>
				<tr>
					<td width="20%" valign="top">Firmas adicionales:</td>
					<td width="80%" class="normalNegro">
						<?php if($firmaPresidencia=="t"){echo 'Presidencia<br/>';}?>
						<?php if($firmaAdministracion=="t"){echo 'Oficina de Gesti&oacute;n Administrativa y Financiera';}?>
					</td>
				</tr>
				<tr>
					<td width="20%" valign="top">Coletilla:</td>
					<td width="80%" class="normalNegro">
						<?= $coletilla?>
					</td>
				</tr>
				<tr>
					<td width="20%" valign="top">Anexos:</td>
					<td width="80%" class="normalNegro">
						<?= $anexos?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?php
	if(/*$usua_login==$usuario*/$depe_id==$user_depe_id || ($user_perfil_id == PERFIL_TESORERO && $depe_id==substr(PERFIL_JEFE_FINANZAS, 2, 3)) ){
	?>
	<tr>
		<td height="16" colspan="2">
			<div align="center" class="normal" style="height: 50px;margin-top: 20px;">
				<div style="margin-top: 12px;float: left;text-align: right;width: 50%;">Para generar su documento en formato PDF haga clic</div>
				<div style="float: right;width: 50%;">
					<table align="left">
						<tr>
							<td align="center" width="150px">
								<a href="javascript: generarPdf1();">aqu&iacute;</a>
								<a href="javascript: generarPdf1();"><img src="../../imagenes/pdf_ico.jpg" border="0" align="center"/></a>
							</td>
							<!-- <td align="center" width="150px">
								<a href="javascript: generarPdf2();">aqu&iacute;</a>
								<a href="javascript: generarPdf2();"><img src="../../imagenes/pdf_ico.jpg" border="0" align="center"/></a>
							</td> -->
						</tr>
						<tr>
							<td align="center"><b>Formato 1 (lineal)</b></td>
							<!-- <td align="center"><b>Formato 2 (firmas por hoja)</b></td> -->
						</tr>					
					</table>
				</div>
				<!-- <div style="margin-top: 12px;float: left;text-align: right;width: 60%;">Para generar su documento en formato PDF haga clic</div>
				<div style="float: right;width: 40%;">
					<table align="left">
						<tr>
							<td width="150px">
								<a href="javascript: generarPdf1();">Formato 1 (lineal) <img src="../../imagenes/pdf_ico.jpg" border="0" align="center"/></a>
								<a href="javascript: generarPdf2();">Formato 2 (firmas x hoja) <img src="../../imagenes/pdf_ico.jpg" border="0" align="center"/></a>
							</td>
						</tr>
					</table>
				</div> -->
			</div>
			<br/>
		</td>
	</tr>
	<?php }?>
</table>
<br/>
<center>
<span class="normalNegrita">
<?php
if($tipo=="memo"){
	$nombre = "el memorando";
}else if($tipo=="ofic"){
	$nombre = "el oficio";
}
if($accion=="generar"){
	echo "Usted ha creado con &eacute;xito ".$nombre." <span class='resultados'>\"".$codigo."\"</span>";
}else if($accion=="modificar"){
	echo "Usted ha modificado con &eacute;xito ".$nombre." <span class='resultados'>\"".$codigo."\"</span>";
}else if($accion=="anular"){
	echo "Usted ha anulado con &eacute;xito ".$nombre." <span class='resultados'>\"".$codigo."\"</span>";
}
?>
</span>
</center>
<?php
}
?>
</body>
</html>