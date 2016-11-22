<?php
require("../../includes/constantes.php");
include(dirname(__FILE__).'/../../init.php');
require("../../includes/conexion.php");
require("../../includes/perfiles/constantesPerfiles.php");
$tipo=trim($_REQUEST["tipo"]);
$user_perfil_id = $_SESSION['user_perfil_id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:. Ingresar <?= (($tipo=="memo")?"Memorando":(($tipo=="ofic")?"Oficio":""))?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
<link href="../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
	g_Calendar.setDateFormat('dd/mm/yyyy');
</script>
<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script language="JavaScript" src="../../js/funciones.js"></script>



<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"
	charset="utf-8"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/ui.min.js';?>"
	charset="utf-8"></script>
	
<!-- jQuery and jQuery UI -->

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/jquery-ui-1.8.13.custom.min.js';?>"
	charset="utf-8"></script>
<link
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />

<!-- elRTE -->

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/elrte.min.js';?>"
	charset="utf-8"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/elRTE.options.js';?>"
	charset="utf-8"></script>
<link
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/elrte.min.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />
<link
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/elrte-inner.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />


<!-- elRTE translation messages -->

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/i18n/elrte.es.js';?>"
	charset="utf-8"></script>



<script language="JavaScript" src="../../js/crearModificarMemorando.js"></script>


	
<script type="text/javascript" charset="utf-8">

   $().ready(function() {

	   var opts = {
				doctype  :	' <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">',
				cssClass : 'el-rte',
				lang     : 'es',
				height   : 400,
				toolbar  : 'maxi',
				cssfiles : ['js/editorlr/css/elrte-inner.css']
	
			}
		

		$('#pcuenta_descripcion').elrte(opts);

  });


</script>		










</head>
<body class="normal" onload="onLoad();">
	<?php
	$msg=$_GET['msg'];
	if($msg=="0"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Falta el tipo de comunicacio&acute;n.</p>";
	}else if($msg=="1"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar la dependencia remitente.</p>";
	}else if($msg=="2"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar el asunto.</p>";
	}else if($msg=="3"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar la fecha.</p>";
	}else if($msg=="4"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar la descripci&oacute;n.</p>";
	}else if($msg=="5"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar la despedida.</p>";
	}else if($msg=="6"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar la alineaci&oacute;n de la despedida.</p>";
	}else if($msg=="7"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Falta la cantidad de personal del Para.</p>";
	}else if($msg=="8"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Falta la cantidad de personal del Cc.</p>";
	}else if($msg=="9"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar al menos un (1) personal en la secci&oacute;n Para.</p>";
	}
	
	$cedulasPresidentes = "";
	$nombresPresidentes = "";
	$cargosPresidentes = "";
	$dependenciasPresidentes = "";
	$cargo = substr(PERFIL_PRESIDENTE,0,2);
	$dependencia = substr(PERFIL_PRESIDENTE,2);
	$estadoAnulado = "15";
	$estadoInactivo = "2";
	$query = 	"SELECT ".
					"se.empl_cedula AS cedula, ".
					"UPPER(se.empl_nombres || ' ' || se.empl_apellidos) AS nombre, ".
					"UPPER(sc.carg_nombre) AS cargo, ".
					"UPPER(sd.depe_nombre) AS dependencia ".
				"FROM sai_empleado se, sai_cargo sc, sai_dependenci sd ".
				"WHERE ".
					"se.esta_id <> ".$estadoAnulado." AND se.esta_id <> ".$estadoInactivo." AND ".
					"se.carg_fundacion = sc.carg_fundacion AND ".
					"se.depe_cosige = sd.depe_id AND ".
					"se.carg_fundacion = '".$cargo."' AND ".
					"se.depe_cosige = '".$dependencia."' ".	
				"ORDER BY se.empl_nombres, se.empl_apellidos ";
	$resultado = pg_exec($conexion, $query);
	while($row=pg_fetch_array($resultado)){
		$cedulasPresidentes .= "'".$row["cedula"]."',";
		$nombresPresidentes .= "'".str_replace("\n"," ",$row["nombre"])."',";
		$cargosPresidentes .= "'".str_replace("\n"," ",$row["cargo"])."',";
		$dependenciasPresidentes .= "'".str_replace("\n"," ",$row["dependencia"])."',";
	}
	$cedulasPresidentes = substr($cedulasPresidentes, 0, -1);
	$nombresPresidentes = substr($nombresPresidentes, 0, -1);
	$cargosPresidentes = substr($cargosPresidentes, 0, -1);
	$dependenciasPresidentes = substr($dependenciasPresidentes, 0, -1);
	
	$cedulasDirectorEjecutivo = "";
	$nombresDirectorEjecutivo = "";
	$cargosDirectorEjecutivo = "";
	$dependenciasDirectorEjecutivo = "";
	$cargo = substr(PERFIL_DIRECTOR_EJECUTIVO,0,2);
	$dependencia = substr(PERFIL_DIRECTOR_EJECUTIVO,2);
	$query = 	"SELECT ".
					"se.empl_cedula AS cedula, ".
					"UPPER(se.empl_nombres || ' ' || se.empl_apellidos) AS nombre, ".
					"UPPER(sc.carg_nombre) AS cargo, ".
					"UPPER(sd.depe_nombre) AS dependencia ".
				"FROM sai_empleado se, sai_cargo sc, sai_dependenci sd ".
				"WHERE ".
					"se.esta_id <> ".$estadoAnulado." AND se.esta_id <> ".$estadoInactivo." AND ".
					"se.carg_fundacion = sc.carg_fundacion AND ".
					"se.depe_cosige = sd.depe_id AND ".
					"se.carg_fundacion = '".$cargo."' AND ".
					"se.depe_cosige = '".$dependencia."' ".	
				"ORDER BY se.empl_nombres, se.empl_apellidos ";
	$resultado = pg_exec($conexion, $query);
	while($row=pg_fetch_array($resultado)){
		$cedulasDirectorEjecutivo .= "'".$row["cedula"]."',";
		$nombresDirectorEjecutivo .= "'".str_replace("\n"," ",$row["nombre"])."',";
		$cargosDirectorEjecutivo .= "'".str_replace("\n"," ",$row["cargo"])."',";
		$dependenciasDirectorEjecutivo .= "'".str_replace("\n"," ",$row["dependencia"])."',";
	}
	$cedulasDirectorEjecutivo = substr($cedulasDirectorEjecutivo, 0, -1);
	$nombresDirectorEjecutivo = substr($nombresDirectorEjecutivo, 0, -1);
	$cargosDirectorEjecutivo = substr($cargosDirectorEjecutivo, 0, -1);
	$dependenciasDirectorEjecutivo = substr($dependenciasDirectorEjecutivo, 0, -1);
	
	$cedulasGerenteDirector = "";
	$nombresGerenteDirector = "";
	$cargosGerenteDirector = "";
	$dependenciasGerenteDirector = "";
	$cargoGerente = substr(PERFIL_GERENTE,0,2);
	$cargoDirector = substr(PERFIL_DIRECTOR,0,2);
	$query = 	"SELECT ".
					"se.empl_cedula AS cedula, ".
					"UPPER(se.empl_nombres || ' ' || se.empl_apellidos) AS nombre, ".
					"UPPER(sc.carg_nombre) AS cargo, ".
					"UPPER(sd.depe_nombre) AS dependencia ".
				"FROM sai_empleado se, sai_cargo sc, sai_dependenci sd ".
				"WHERE ".
					"se.esta_id <> ".$estadoAnulado." AND se.esta_id <> ".$estadoInactivo." AND ".
					"se.carg_fundacion = sc.carg_fundacion AND ".
					"se.depe_cosige = sd.depe_id AND ".
					"(se.carg_fundacion = '".$cargoGerente."' OR se.carg_fundacion = '".$cargoDirector."') ".
				"ORDER BY se.empl_nombres, se.empl_apellidos ";
	$resultado = pg_exec($conexion, $query);
	while($row=pg_fetch_array($resultado)){
		$cedulasGerenteDirector .= "'".$row["cedula"]."',";
		$nombresGerenteDirector .= "'".str_replace("\n"," ",$row["nombre"])."',";
		$cargosGerenteDirector .= "'".str_replace("\n"," ",$row["cargo"])."',";
		$dependenciasGerenteDirector .= "'".str_replace("\n"," ",$row["dependencia"])."',";
	}
	$cedulasGerenteDirector = substr($cedulasGerenteDirector, 0, -1);
	$nombresGerenteDirector = substr($nombresGerenteDirector, 0, -1);
	$cargosGerenteDirector = substr($cargosGerenteDirector, 0, -1);
	$dependenciasGerenteDirector = substr($dependenciasGerenteDirector, 0, -1);
	
	$cedulasJefe = "";
	$nombresJefe = "";
	$cargosJefe = "";
	$dependenciasJefe = "";
	$cargo = substr(PERFIL_JEFE,0,2);
	$query = 	"SELECT ".
					"se.empl_cedula AS cedula, ".
					"UPPER(se.empl_nombres || ' ' || se.empl_apellidos) AS nombre, ".
					"UPPER(sc.carg_nombre) AS cargo, ".
					"UPPER(sd.depe_nombre) AS dependencia ".
				"FROM sai_empleado se, sai_cargo sc, sai_dependenci sd ".
				"WHERE ".
					"se.esta_id <> ".$estadoAnulado." AND se.esta_id <> ".$estadoInactivo." AND ".
					"se.carg_fundacion = sc.carg_fundacion AND ".
					"se.depe_cosige = sd.depe_id AND ".
					"se.carg_fundacion = '".$cargo."' ".
				"ORDER BY se.empl_nombres, se.empl_apellidos ";
	$resultado = pg_exec($conexion, $query);
	while($row=pg_fetch_array($resultado)){
		$cedulasJefe .= "'".$row["cedula"]."',";
		$nombresJefe .= "'".str_replace("\n"," ",$row["nombre"])."',";
		$cargosJefe .= "'".str_replace("\n"," ",$row["cargo"])."',";
		$dependenciasJefe .= "'".str_replace("\n"," ",$row["dependencia"])."',";
	}
	$cedulasJefe = substr($cedulasJefe, 0, -1);
	$nombresJefe = substr($nombresJefe, 0, -1);
	$cargosJefe = substr($cargosJefe, 0, -1);
	$dependenciasJefe = substr($dependenciasJefe, 0, -1);
	
	$cedulasCoordinador = "";
	$nombresCoordinador = "";
	$cargosCoordinador = "";
	$dependenciasCoordinador = "";
	$cargo = substr(PERFIL_COORDINADOR,0,2);
	$query = 	"SELECT ".
					"se.empl_cedula AS cedula, ".
					"UPPER(se.empl_nombres || ' ' || se.empl_apellidos) AS nombre, ".
					"UPPER(sc.carg_nombre) AS cargo, ".
					"UPPER(sd.depe_nombre) AS dependencia ".
				"FROM sai_empleado se, sai_cargo sc, sai_dependenci sd ".
				"WHERE ".
					"se.esta_id <> ".$estadoAnulado." AND se.esta_id <> ".$estadoInactivo." AND ".
					"se.carg_fundacion = sc.carg_fundacion AND ".
					"se.depe_cosige = sd.depe_id AND ".
					"se.carg_fundacion = '".$cargo."' ".
				"ORDER BY se.empl_nombres, se.empl_apellidos ";
	$resultado = pg_exec($conexion, $query);
	while($row=pg_fetch_array($resultado)){
		$cedulasCoordinador .= "'".$row["cedula"]."',";
		$nombresCoordinador .= "'".str_replace("\n"," ",$row["nombre"])."',";
		$cargosCoordinador .= "'".str_replace("\n"," ",$row["cargo"])."',";
		$dependenciasCoordinador .= "'".str_replace("\n"," ",$row["dependencia"])."',";
	}
	$cedulasCoordinador = substr($cedulasCoordinador, 0, -1);
	$nombresCoordinador = substr($nombresCoordinador, 0, -1);
	$cargosCoordinador = substr($cargosCoordinador, 0, -1);
	$dependenciasCoordinador = substr($dependenciasCoordinador, 0, -1);
	?>
	<script>
		var cedulasPresidentes = new Array(<?= $cedulasPresidentes?>);
		var nombresPresidentes = new Array(<?= $nombresPresidentes?>);
		var cargosPresidentes = new Array(<?= $cargosPresidentes?>);
		var dependenciasPresidentes = new Array(<?= $dependenciasPresidentes?>);

		var cedulasDirectorEjecutivo = new Array(<?= $cedulasDirectorEjecutivo?>);
		var nombresDirectorEjecutivo = new Array(<?= $nombresDirectorEjecutivo?>);
		var cargosDirectorEjecutivo = new Array(<?= $cargosDirectorEjecutivo?>);
		var dependenciasDirectorEjecutivo = new Array(<?= $dependenciasDirectorEjecutivo?>);

		var cedulasGerenteDirector = new Array(<?= $cedulasGerenteDirector?>);
		var nombresGerenteDirector = new Array(<?= $nombresGerenteDirector?>);
		var cargosGerenteDirector = new Array(<?= $cargosGerenteDirector?>);
		var dependenciasGerenteDirector = new Array(<?= $dependenciasGerenteDirector?>);

		var cedulasJefe = new Array(<?= $cedulasJefe?>);
		var nombresJefe = new Array(<?= $nombresJefe?>);
		var cargosJefe = new Array(<?= $cargosJefe?>);
		var dependenciasJefe = new Array(<?= $dependenciasJefe?>);

		var cedulasCoordinador = new Array(<?= $cedulasCoordinador?>);
		var nombresCoordinador = new Array(<?= $nombresCoordinador?>);
		var cargosCoordinador = new Array(<?= $cargosCoordinador?>);
		var dependenciasCoordinador = new Array(<?= $dependenciasCoordinador?>);
	</script>
	<form name="form" method="post" action="ingresarAccion.php" id="form">
		<input type="hidden" id="tipo" name="tipo" value="<?= $tipo?>"/>
		<table align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td colspan="2" class="normalNegroNegrita">
					INGRESAR <?= (($tipo=="memo")?"MEMORANDO":(($tipo=="ofic")?"OFICIO":""))?>
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
										<td colspan="5">
											<table id="ccs" align="center" class="tablaalertas" width="100%">
												<tr>
													<td>
														A&ntilde;adir personal
														<input autocomplete="off" size="87" type="text" id="personal" name="personal" class="normalNegro"/>
														A&ntilde;adir en
														<select id="paraCc" name="paraCc" class="normalNegro">
															<option value="para">Para</option>
															<option value="cc">Cc</option>
														</select>
														<input type="button" value="Agregar" class="normalNegro" onclick="agregarPersonal();"/>
														<br/>Introduzca la c&eacute;dula o una palabra contenida en el nombre de la persona.
														<?php
															$query = 	"SELECT ".
																			"se.empl_cedula AS cedula, ".
																			"UPPER(se.empl_nombres || ' ' || se.empl_apellidos) AS nombre, ".
																			"UPPER(sc.carg_nombre) AS cargo, ".
																			"UPPER(sd.depe_nombre) AS dependencia ".
																		"FROM sai_empleado se, sai_cargo sc, sai_dependenci sd ".
																		"WHERE ".
																			"se.esta_id <> ".$estadoAnulado." AND se.esta_id <> ".$estadoInactivo." AND ".
																			"se.carg_fundacion = sc.carg_fundacion AND ".
																			"se.depe_cosige = sd.depe_id ".
																		"ORDER BY se.empl_nombres, se.empl_apellidos ";
															$resultado = pg_exec($conexion, $query);
															$arreglo = "";
															$cedulas = "";
															$nombres = "";
															$cargos = "";
															$dependencias = "";
															while($row=pg_fetch_array($resultado)){
																$cedulas .= "'".$row["cedula"]."',";
																$nombres .= "'".str_replace("\n"," ",$row["nombre"])."',";
																$cargos .= "'".str_replace("\n"," ",$row["cargo"])."',";
																$dependencias .= "'".str_replace("\n"," ",$row["dependencia"])."',";
																$arreglo .= "'".$row["cedula"]." : ".str_replace("\n"," ",$row["nombre"])."',";
															}
															$arreglo = substr($arreglo, 0, -1);
															$cedulas = substr($cedulas, 0, -1);
															$nombres = substr($nombres, 0, -1);
															$cargos = substr($cargos, 0, -1);
															$dependencias = substr($dependencias, 0, -1);
														?>
														<script>
															var personal = new Array(<?= $arreglo?>);
															var cedulas = new Array(<?= $cedulas?>);
															var nombres = new Array(<?= $nombres?>);
															var cargos = new Array(<?= $cargos?>);
															var dependencias = new Array(<?= $dependencias?>);
															actb(document.getElementById('personal'),personal);
														</script>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<input type="hidden" id="tamanoPersonalPara" name="tamanoPersonalPara" value="0"/>
											<table id="ccs" align="center" class="tablaalertas" width="100%">
												<tr>
													<td width="20%" valign="top" class="normalNegrita">Para:<span class="peq_naranja">(*)</span></td>
													<td width="80%" class="normalNegro">
														<table class="normal" width="100%">
															<tr>
																<td width="20%"><input type="checkbox" id="PRpara" value="PR" onclick="agregarGrupo(this, 'para');"/>Presidencia</td>
																<td width="20%"><input type="checkbox" id="DEpara" value="DE" onclick="agregarGrupo(this, 'para');"/>Direcci&oacute;n ejecutiva</td>
																<td width="20%"><input type="checkbox" id="GDpara" value="GD" onclick="agregarGrupo(this, 'para');"/>Gerentes/Directores</td>
																<td width="20%"><input type="checkbox" id="JEpara" value="JE" onclick="agregarGrupo(this, 'para');"/>Jefes</td>
																<td width="20%"><input type="checkbox" id="COpara" value="CO" onclick="agregarGrupo(this, 'para');"/>Coordinadores</td>
															</tr>
															<tr>
																<td colspan="5">
																	<table id="tbl_mod_paras" align="center" class="tablaalertas" width="100%">
																		<tr class="td_gray normalNegrita">
																			<td align="center" width="15%">C&eacute;dula</td>
																			<td align="center" width="25%">Nombre</td>
																			<td align="center" width="25%">Cargo</td>
																			<td align="center" width="25%">Dependencia</td>
																			<td align="center" width="10%"></td>
																		</tr>
																		<tbody id="bodyParas"></tbody>
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
											<input type="hidden" id="tamanoPersonalCc" name="tamanoPersonalCc" value="0"/>
											<table id="ccs" align="center" class="tablaalertas" width="100%">
												<tr>
													<td width="20%" valign="top" class="normalNegrita">Cc:</td>
													<td width="80%" class="normalNegro">
														<table class="normal" width="100%">
															<tr>
																<td width="20%"><input type="checkbox" id="PRcc" value="PR" onclick="agregarGrupo(this, 'cc');"/>Presidencia</td>
																<td width="20%"><input type="checkbox" id="DEcc" value="DE" onclick="agregarGrupo(this, 'cc');"/>Direcci&oacute;n ejecutiva</td>
																<td width="20%"><input type="checkbox" id="GDcc" value="GD" onclick="agregarGrupo(this, 'cc');"/>Gerentes/Directores</td>
																<td width="20%"><input type="checkbox" id="JEcc" value="JE" onclick="agregarGrupo(this, 'cc');"/>Jefes</td>
																<td width="20%"><input type="checkbox" id="COcc" value="CO" onclick="agregarGrupo(this, 'cc');"/>Coordinadores</td>
															</tr>
															<tr>
																<td colspan="5">
																	<table id="tbl_mod_ccs" align="center" class="tablaalertas" width="100%">
																		<tr class="td_gray normalNegrita">
																			<td align="center" width="15%">C&eacute;dula</td>
																			<td align="center" width="25%">Nombre</td>
																			<td align="center" width="25%">Cargo</td>
																			<td align="center" width="25%">Dependencia</td>
																			<td align="center" width="10%"></td>
																		</tr>
																		<tbody id="bodyCcs"></tbody>
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
							<td width="20%">De:<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
								<select id="de" name="de" class="normalNegro">
									<?php
									$nivelOficinaGerencia = "5";
									$estadoAnulado = "15";
									/*if($_SESSION['user_depe_id']!="150" && $_SESSION['user_depe_id']!="350"){
										$dependencias = "('150','350','".$_SESSION['user_depe_id']."')";
									}else{
										$dependencias = "('150','350')";
									}*/
									
									if($user_perfil_id==PERFIL_TESORERO){
										$dependencias = "('".substr(PERFIL_JEFE_FINANZAS, 2, 3)."')";
									}else{
										$dependencias = "('".$_SESSION['user_depe_id']."')";
									}
									/**/
									
									$query = 	"SELECT depe_id,depe_nombre ".
												"FROM sai_dependenci ".
												"WHERE ".
													"depe_id IN ".$dependencias." AND ".
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
						
					
						<tr>
							<td width="20%" valign="top">Descripci&oacute;n:<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
						  	
						  	 <div id="pcuenta_descripcion" class="pcuenta_descripcion"><?php echo $descripcion ?></div>
	                         <input type="hidden" name="descripcion" id="pcuenta_descripcionVal" value="">
						  	
							</td>
						</tr>
							<tr>
							<td width="20%">Fecha:<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
								<input type="text" size="10" id="fecha" name="fecha" class="dateparse" readonly="readonly"/>
								<a href="javascript:void(0);" 
									onclick="g_Calendar.show(event, 'fecha');" 
									title="Show popup calendar">
									<img src="../../js/lib/calendarPopup/img/calendar.gif" 
										class="cp_img" 
										alt="Open popup calendar"/>
								</a>
							</td>
						</tr>
						<tr>
							<td width="20%" valign="top">Asunto:<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
								<input id="inputSelectMemorandoAsuntos" class="normalNegro" size="50" maxlength="200"/>
								<input id="asunto" name="asunto" type="hidden" value=""/>
								<div style="width: 340px; color: red; float: right;margin-top: 4px;" id="errorAsunto"></div>
							</td>
						</tr>
						<tr>
							<td width="20%" valign="top">Despedida:<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
								<input type="text" maxlength="100" size="68" id="despedida" name="despedida" value="Atentamente" class="normalNegro"/>
								<span style="margin-left: 5px">Alineaci&oacute;n de despedida: </span>
								<select id="alineacionDespedida" name="alineacionDespedida" class="normalNegro">
									<option value="left">Izquierda</option>
									<option value="center">Centro</option>
									<option value="right">Derecha</option>
								</select>
							</td>
						</tr>
						<tr>
							<td width="20%" valign="top">Firmas adicionales:</td>
							<td width="80%" class="normalNegro">
								<input type="checkbox" id="firmaPresidencia" name="firmaPresidencia"/>Presidencia
								<br/>
								<input type="checkbox" id="firmaAdministracion" name="firmaAdministracion"/>Oficina de Gesti&oacute;n Administrativa y Financiera
							</td>
						</tr>
						<tr>
							<td width="20%" valign="top">Coletilla:</td>
							<td width="80%" class="normalNegro">
								<textarea class="normalNegro" id="coletilla" name="coletilla" cols="96" rows="2"
									onkeydown="textCounter(this,'coletillaLen',200);"
									onkeyup="textCounter(this,'coletillaLen',200);validarTexto(this);"></textarea><br/>
								<div style="text-align: right;width: 700px"><input type="text" value="200" class="normalNegro" maxlength="3" size="3" id="coletillaLen" name="coletillaLen" readonly="readonly"/></div>
							</td>
						</tr>
						<tr>
							<td width="20%" valign="top">Anexos:</td>
							<td width="80%" class="normalNegro">
								<textarea class="normalNegro" id="anexos" name="anexos" cols="96" rows="5"
									onkeydown="textCounter(this,'anexosLen',1000);"
									onkeyup="textCounter(this,'anexosLen',1000);validarTexto(this);"></textarea><br/>
								<div style="text-align: right;width: 700px"><input type="text" value="1000" class="normalNegro" maxlength="3" size="3" id="anexosLen" name="anexosLen" readonly="readonly"/></div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
	<br/>
	<div id="divAcciones" style="text-align: center;">
		<input class="normalNegro" type="button" value="Crear" onclick="crear();"/>
	</div>
	<div class="ptotal"><span class="peq_naranja">(*)</span> Campo obligatorio</div>
</body>
</html>
