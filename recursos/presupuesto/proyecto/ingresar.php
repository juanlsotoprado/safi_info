<?php
require("../../../includes/constantes.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:. Ingresar Categor&iacute;a Program&aacute;tica</title>
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
</script>
</head>
<body class="normal">
	<?php
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
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Ya existe un proyecto con el mismo c&oacute;digo y el mismo a&ntilde;o presupuestario.</p>";
	}else if($msg=="9"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>Ya existe una acción centralizada con el mismo c&oacute;digo y el mismo a&ntilde;o presupuestario.</p>";
	}
	?>
	<form name="form" method="post" action="ingresarAccion.php" id="form">
		<table align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td colspan="2" class="normalNegroNegrita">
					INGRESAR CATEGOR&Iacute;A PROGRAM&Aacute;TICA
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
								<input type='radio' name='tipoImputacion' value='<?= TIPO_IMPUTACION_PROYECTO?>' checked="checked" onclick="mostrarCampos(<?= TIPO_IMPUTACION_PROYECTO?>);"/>Proyecto
								<input type='radio' name='tipoImputacion' value='<?= TIPO_IMPUTACION_ACCION_CENTRALIZADA?>' onclick="mostrarCampos(<?= TIPO_IMPUTACION_ACCION_CENTRALIZADA?>);"/>Acci&oacute;n centralizada
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
								<select class="normalNegro" id="anioPresupuestario" name="anioPresupuestario" onchange="validarAnio();">
									<option value="<?= date('Y')?>"><?= date('Y')?></option>
									<option value="<?= date('Y')+1?>"><?= date('Y')+1?></option>
								</select>
								<script>
									anioPresupuestarioAnterior = "<?= date('Y')?>";
								</script>
							</td>
						</tr>
						<tr>
							<td width="20%">C&oacute;digo<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
								<input type="text" class="normalNegro" maxlength="15" size="15" id="codigo" name="codigo" onkeyup="validarTexto(this);"/>
							</td>
						</tr>
						<tr>
							<td width="20%">T&iacute;tulo<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
								<input type="text" class="normalNegro" maxlength="200" size="100" id="titulo" name="titulo" onkeyup="validarTexto(this);"/>
							</td>
						</tr>
						<tr>
							<td width="20%">Descripci&oacute;n<span class="peq_naranja">(*)</span></td>
							<td width="80%" class="normalNegro">
								<div style="width: 100%">
								<textarea class="normalNegro" id="descripcion" name="descripcion" cols="99" rows="2"
									onkeydown="textCounter(this,'descripcionLen',580);"
									onkeyup="textCounter(this,'descripcionLen',580);validarTexto(this);"></textarea><br/>
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
									onkeyup="textCounter(this,'objetivosLen',580);validarTexto(this);"></textarea><br/>
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
									onkeyup="textCounter(this,'resultadoLen',580);validarTexto(this);"></textarea><br/>
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
									onkeyup="textCounter(this,'observacionesLen',200);validarTexto(this);"></textarea><br/>
								<div style="text-align: right;"><input type="text" value="200" class="normalNegro" maxlength="3" size="3" id="observacionesLen" name="observacionesLen" readonly="readonly"/></div>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="hidden" name="tamanoAccionesEspecificas" id="tamanoAccionesEspecificas"/>
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
									</tbody>
									<tr>
										<td height="19" colspan="5">&nbsp;</td>
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
		<input class="normalNegro" type="button" value="Crear" onclick="validarCodigoCategoria();"/>
	</div>
	<div class="ptotal"><span class="peq_naranja">(*)</span> Campo obligatorio</div>
</body>
</html>