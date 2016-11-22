<?php
require("../../../includes/constantes.php");
require("../../../includes/conexion.php");
$codigo=trim($_REQUEST['codigo']);
$anioPresupuestario=trim($_REQUEST['anioPress']);
$tipo=trim($_REQUEST['tipo']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:. Modificar Categor&iacute;a Program&aacute;tica</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
	g_Calendar.setDateFormat('dd/mm/yyyy');
</script>
<script language="JavaScript" src="../../../js/lib/jquery/plugins/jquery.min.js"></script>
<script language="JavaScript" src="../../../js/funciones.js"></script>
<script language="JavaScript" src="../../../js/crearModificarCategoriaProgramatica.js"></script>
<script language="Javascript">
function validarFechaCategoria(){
	var fechaInicio=document.getElementById("fechaInicio").value;
	var anio=fechaInicio.substring(6,10);
	if(anio!=document.getElementById("anioPresupuestario").value){
		document.getElementById("fechaInicio").value = "<?= date('d/m/Y')?>";
		alert("No puede agregar una acci"+oACUTE+"n espec"+iACUTE+"fica de un a"+nTILDE+"o diferente al a"+nTILDE+"o presupuestario seleccionado.");
		return false;
	}
	return true;
}
function cancelar(){
	location.href="buscar.php";
}
</script>
</head>
<body class="normal">
<?php
if($codigo && $codigo!="" && $anioPresupuestario && $anioPresupuestario!="" && $tipo!=""){
	if($tipo==TIPO_IMPUTACION_PROYECTO){
		$query = 	"SELECT ".
						"sp.proy_titulo as titulo, ".
						"sp.proy_desc as descripcion, ".
						"sp.proy_obj as objetivos, ".
						"sp.proy_resultado as resultado, ".
						"sp.proy_observa as observaciones ".
					"FROM ".
						"sai_proyecto sp ".
					"WHERE ".
						"sp.proy_id = '".$codigo."' AND sp.pre_anno = ".$anioPresupuestario;
	}else if($tipo==TIPO_IMPUTACION_ACCION_CENTRALIZADA){
		$query = 	"SELECT ".
						"sac.acce_denom as titulo, ".
						"sac.acce_observa as observaciones ".
					"FROM ".
						"sai_ac_central sac ".
					"WHERE ".
						"sac.acce_id = '".$codigo."' AND sac.pres_anno = ".$anioPresupuestario;
	}
	$resultadoQuery=pg_query($conexion,$query);
	$row=pg_fetch_array($resultadoQuery);
	
	$titulo = $row["titulo"];
	if($tipo==TIPO_IMPUTACION_PROYECTO){
		$descripcion = $row["descripcion"];
		$objetivos = $row["objetivos"];
		$resultado = $row["resultado"];		
	}else if($tipo==TIPO_IMPUTACION_ACCION_CENTRALIZADA){
		$descripcion = "";
		$objetivos = "";
		$resultado = "";
	}
	$observaciones = $row["observaciones"];	
	
	$msg=$_GET['msg'];
	if($msg=="0"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar el tipo de categor&iacute;a program&aacute;tica (Proyecto o Acci&oacute;n centralizada).</p>";
	}else if($msg=="1"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar el a&ntilde;o presupuestario.</p>";
	}else if($msg=="2"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar el c&oacute;digo de la categor&iacute;a program&aacute;tica.</p>";
	}else if($msg=="3"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar el t&iacute;tulo de la categor&iacute;a program&aacute;tica.</p>";
	}else if($msg=="4"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar la descripci&oacute;n.</p>";
	}else if($msg=="5"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar los objetivos.</p>";
	}else if($msg=="6"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Falta la cantidad de acciones espec&iacute;ficas.</p>";
	}else if($msg=="7"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Debe indicar al menos una (1) acción espec&iacute;fica para la categor&iacute;a program&aacute;tica.</p>";
	}else if($msg=="8"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>No existe un proyecto con este c&oacute;digo y este a&ntilde;o presupuestario.</p>";
	}else if($msg=="9"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>No existe una acción centralizada con este c&oacute;digo y este a&ntilde;o presupuestario.</p>";
	}
	?>
	<form name="form" method="post" action="modificarAccion.php" id="form">
		<script>
			tipoImputacion = <?= $tipo?>;
		</script>
		<table align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td colspan="2" class="normalNegroNegrita">
					MODIFICAR CATEGOR&Iacute;A PROGRAM&Aacute;TICA
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table class="tablaalertas" width="900px">
						<tr>
							<td width="20%">
								Tipo de categor&iacute;a<span class="peq_naranja">(*)</span>
							</td>
							<td width="80%" class="normalNegro">
								<input type='radio' <?php if($tipo==TIPO_IMPUTACION_PROYECTO){ echo "checked='checked'";}?> disabled="disabled"/>Proyecto
								<input type='radio' <?php if($tipo==TIPO_IMPUTACION_ACCION_CENTRALIZADA){ echo "checked='checked'";}?> disabled="disabled"/>Acci&oacute;n centralizada
								<input type="hidden" value="<?= $tipo?>" name='tipoImputacion'/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table class="tablaalertas" width="900px">
						<tr>
							<td width="20%">A&ntilde;o presupuestario<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
								<select class="normalNegro" disabled="disabled">
									<option value="<?= $anioPresupuestario?>"><?= $anioPresupuestario?></option>
								</select>
								<input type="hidden" value="<?= $anioPresupuestario?>" id="anioPresupuestario" name="anioPresupuestario"/>
							</td>
						</tr>
						<tr>
							<td width="20%">C&oacute;digo<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
								<input type="text" class="normalNegro" size="15" disabled="disabled" value="<?= $codigo?>"/>
								<input type="hidden" value="<?= $codigo?>" id="codigo" name="codigo"/>
								<input type="hidden" value="<?= $codigo?>" id="categoria" name="categoria"/>
							</td>
						</tr>
						<tr>
							<td width="20%">T&iacute;tulo<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
								<input type="text" class="normalNegro" maxlength="200" size="100" id="titulo" name="titulo" onkeyup="validarTexto(this);" value="<?= $titulo?>"/>
							</td>
						</tr>
						<tr>
							<td width="20%">Descripci&oacute;n<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
								<div style="width: 100%">
								<textarea class="normalNegro" id="descripcion" name="descripcion" cols="99" rows="2"
									onkeydown="textCounter(this,'descripcionLen',580);"
									onkeyup="textCounter(this,'descripcionLen',580);validarTexto(this);" <?php if($tipo==TIPO_IMPUTACION_ACCION_CENTRALIZADA){echo "disabled='disabled'";}?>><?= $descripcion?></textarea><br/>
								<div style="text-align: right;"><input type="text" value="580" class="normalNegro" maxlength="3" size="3" id="descripcionLen" name="descripcionLen" readonly="readonly"/></div>
								</div>
							</td>
						</tr>
						<tr>
							<td width="20%">Objetivos<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
								<div style="width: 100%">
								<textarea class="normalNegro" id="objetivos" name="objetivos" cols="99" rows="2"
									onkeydown="textCounter(this,'objetivosLen',580);"
									onkeyup="textCounter(this,'objetivosLen',580);validarTexto(this);" <?php if($tipo==TIPO_IMPUTACION_ACCION_CENTRALIZADA){echo "disabled='disabled'";}?>><?= $objetivos?></textarea><br/>
								<div style="text-align: right;"><input type="text" value="580" class="normalNegro" maxlength="3" size="3" id="objetivosLen" name="objetivosLen" readonly="readonly"/></div>
								</div>
							</td>
						</tr>
						<tr>
							<td width="20%">Resultado</td>
							<td width="80%" class="normalNegro">
								<div style="width: 100%">
								<textarea class="normalNegro" id="resultado" name="resultado" cols="99" rows="2"
									onkeydown="textCounter(this,'resultadoLen',580);"
									onkeyup="textCounter(this,'resultadoLen',580);validarTexto(this);" <?php if($tipo==TIPO_IMPUTACION_ACCION_CENTRALIZADA){echo "disabled='disabled'";}?>><?= $resultado?></textarea><br/>
								<div style="text-align: right;"><input type="text" value="580" class="normalNegro" maxlength="3" size="3" id="resultadoLen" name="resultadoLen" readonly="readonly"/></div>
								</div>
							</td>
						</tr>
						<tr>
							<td width="20%">Observaciones</td>
							<td width="80%" class="normalNegro">
								<div style="width: 100%">
								<textarea class="normalNegro" id="observaciones" name="observaciones" cols="99" rows="2"
									onkeydown="textCounter(this,'observacionesLen',200);"
									onkeyup="textCounter(this,'observacionesLen',200);validarTexto(this);"><?= $observaciones?></textarea><br/>
								<div style="text-align: right;"><input type="text" value="200" class="normalNegro" maxlength="3" size="3" id="observacionesLen" name="observacionesLen" readonly="readonly"/></div>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<?php
						if($tipo==TIPO_IMPUTACION_PROYECTO){
							$query = 	"SELECT ".
											"to_char(spae.paes_fecha_ini,'DD/MM/YYYY') as fecha_inicio, ".
											"spae.paes_nombre as titulo, ".
											"spae.paes_id as id, ".
											"spae.centro_gestor, ".
											"spae.centro_costo ".
										"FROM ".
											"sai_proy_a_esp spae ".
										"WHERE ".
											"spae.proy_id = '".$codigo."' AND spae.pres_anno = ".$anioPresupuestario." ".
										"ORDER BY spae.paes_id";
						}else if($tipo==TIPO_IMPUTACION_ACCION_CENTRALIZADA){
							$query = 	"SELECT ".
											"to_char(sae.aces_fecha_ini,'DD/MM/YYYY') as fecha_inicio, ".
											"sae.aces_nombre as titulo, ".
											"sae.aces_id as id, ".
											"sae.centro_gestor, ".
											"sae.centro_costo ".
										"FROM ".
											"sai_acce_esp sae ".
										"WHERE ".
											"sae.acce_id = '".$codigo."' AND sae.pres_anno = ".$anioPresupuestario." ".
										"ORDER BY sae.aces_id";
						}
						$resultadoQuery=pg_query($conexion,$query);
						$numeroFilas = pg_numrows($resultadoQuery);
					?>
					<input type="hidden" name="tamanoAccionesEspecificas" id="tamanoAccionesEspecificas" value="<?= $numeroFilas?>"/>
					<table width="900px" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
						<tr>
							<td colspan="5">
								Acci&oacute;n espec&iacute;fica<span class="peq_naranja" style="margin-right: 70px;">(*)</span><input size="101" maxlength="300" type="text" id="nombreAccionEspecifica" name="nombreAccionEspecifica" class="normalNegro" onkeyup="validarTexto(this);"/>
							</td>
						</tr>
						<tr>
							<td width="27%">
								Fecha inicio:<span class="peq_naranja">(*)</span>
								<input type="text" size="10" id="fechaInicio" class="dateparse" onfocus="javascript: validarFechaCategoria();" readonly="readonly" value="<?= date('d/m/Y')?>"/>
								<a href="javascript:void(0);" 
									onclick="g_Calendar.show(event, 'fechaInicio');" 
									title="Show popup calendar">
										<img src="../../../js/lib/calendarPopup/img/calendar.gif" 
										class="cp_img" 
										alt="Open popup calendar"/>
								</a>
							</td>
							<td width="21%">
								C&oacute;digo:<span class="peq_naranja">(*)</span>
								<input type="text" size="15" maxlength="15" id="codigoAccionEspecifica" class="normalNegro" onkeyup="validarTexto(this);"/>
							</td>
							<td width="21%">
								Centro gestor:<span class="peq_naranja">(*)</span>
								<input type="text" size="10" maxlength="5" id="centroGestor" class="normalNegro" onkeyup="validarTexto(this);"/>
							</td>
							<td width="21%">
								Centro costos:<span class="peq_naranja">(*)</span>
								<input type="text" size="10" maxlength="5" id="centroCostos" class="normalNegro" onkeyup="validarTexto(this);"/>								
							</td>
							<td width="10%" align="right"><input type="button" value="Agregar" onclick="javascript:agregarAccionEspecifica();" class="normalNegro"/></td>
						</tr>
						<tr>
							<td colspan="5">
								<table width="100%" class="tablaalertas" id="tbl_mod">
									<tr class="td_gray normalNegrita">
										<td align="center" width="14%">Fecha inicio</td>
										<td align="center" width="40%">Nombre</td>
										<td align="center" width="12%">C&oacute;digo</td>
										<td align="center" width="12%">Centro gestor</td>
										<td align="center" width="12%">Centro costos</td>
										<td align="center" width="10%">&nbsp;</td>
									</tr>									
									<tbody id="accionEspecifica">
									<?php
									if($numeroFilas>0){
										$i=0;
										while($row=pg_fetch_array($resultadoQuery)){
									?>
										<tr class="normalNegro">
											<td align="center" valign="top">
												<input type="hidden" name="fechaInicio<?= $i?>" value="<?= $row["fecha_inicio"]?>"/>
												<?= $row["fecha_inicio"]?>
											</td>
											<td align="left" valign="top">
												<input type="hidden" name="nombreAccionEspecifica<?= $i?>" value="<?= $row["titulo"]?>"/>
												<?= $row["titulo"]?>
											</td>
											<td align="center" valign="top">
												<input type="hidden" name="codigoAccionEspecifica<?= $i?>" value="<?= $row["id"]?>"/>
												<?= $row["id"]?>
											</td>
											<td align="center" valign="top">
												<input type="hidden" name="centroGestor<?= $i?>" value="<?= $row["centro_gestor"]?>"/>
												<?= $row["centro_gestor"]?>
											</td>
											<td align="center" valign="top">
												<input type="hidden" name="centroCostos<?= $i?>" value="<?= $row["centro_costo"]?>"/>
												<?= $row["centro_costo"]?>
											</td>
											<td align="center" valign="top" class="link">
												<a href="javascript:eliminarAccionEspecifica('<?= ($i+1)?>')">Eliminar</a>
											</td>
										</tr>
										<script>
											var registro = new Array(5);
											registro[0]='<?= $row["fecha_inicio"]?>';
											registro[1]='<?= $row["titulo"]?>';
											registro[2]='<?= $row["id"]?>';
											registro[3]='<?= $row["centro_gestor"]?>';
											registro[4]='<?= $row["centro_costo"]?>';
											accionesEspecificas[accionesEspecificas.length]=registro;
										</script>
									<?php
											$i++;
										}
									}
									?>
									</tbody>
									<tr>
										<td height="19" colspan="6">&nbsp;</td>
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
		<input class="normalNegro" type="button" value="Cancelar" onclick="cancelar();"/>
		<input class="normalNegro" type="button" value="Modificar" onclick="crear();"/>
	</div>
	<div class="ptotal"><span class="peq_naranja">(*)</span> Campo obligatorio</div>
<?php
}
?>
</body>
</html>