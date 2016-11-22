<?php
ob_start();
session_start();
require_once(dirname(__FILE__) . '/../../init.php');
require_once(SAFI_INCLUDE_PATH . '/conexion.php');
require(SAFI_INCLUDE_PATH. '/constantes.php');
require(SAFI_INCLUDE_PATH . '/perfiles/constantesPerfiles.php');
require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');

if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();
$tipo=trim($_REQUEST["tipo"]);
$usuario = $_SESSION['login'];
$user_perfil_id = $_SESSION['user_perfil_id'];
$user_depe_id = $_SESSION['user_depe_id'];
if($user_perfil_id==PERFIL_TESORERO){
	$user_perfil_id = PERFIL_JEFE_FINANZAS;
	$user_depe_id = substr(PERFIL_JEFE_FINANZAS, 2, 3);	
}
$dependenciaUsuario = substr($user_perfil_id,2);

$bandeja=BANDEJA_RECIBIDOS;
if (isset($_REQUEST['bandeja']) && $_REQUEST['bandeja'] != "") {
	$bandeja=trim($_REQUEST['bandeja']);
}
$codigo=trim($_REQUEST['codigo']);
$fechaInicio=trim($_REQUEST['fechaInicio']);
$fechaFin=trim($_REQUEST['fechaFin']);
$estado=trim($_REQUEST['estado']);
$de=trim($_REQUEST['de']);
$destinatarioStr = trim($_REQUEST['destinatario']);
$tok = strtok($destinatarioStr, ":");
if ($tok !== false) {
    $destinatario = trim($tok);
}

$asunto=trim($_REQUEST['asunto']);
$opcion=trim($_REQUEST['opcion']);

$pagina = "1";
if (isset($_REQUEST['pagina']) && $_REQUEST['pagina'] != "") {
	$pagina = $_REQUEST['pagina'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>...:SAFI:Comunicaci&oacute;n</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css"/>
<link type="text/css" href="../../css/safi0.2.css" rel="stylesheet" />
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>

<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
	g_Calendar.setDateFormat('dd/mm/yyyy');
</script>
<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
<script type="text/javascript" src="../../js/lib/actb.js"></script>
<script type="text/javascript" src="../../js/funciones.js"></script>
<script type="text/javascript">
	function verDetalle(codigo){
		location.href="detalle.php?tipo=<?= $tipo?>&codigo="+codigo;
	}
	function modificar(codigo){
		location.href="modificar.php?tipo=<?= $tipo?>&codigo="+codigo;
	}
	function seleccionarOpcion(valor){
		if(valor=='1'){ 
			document.form.fechaInicio.disabled=false;
			document.form.fechaFin.disabled=false;
			document.form.estado.disabled=false;
			document.form.de.disabled=false;
			document.form.destinatario.disabled=false;
			document.form.asunto.disabled=false;
			document.form.codigo.value="";
			document.form.codigo.disabled=true;
		}else if(valor=='2'){ 
			document.form.fechaInicio.disabled=true;
			document.form.fechaFin.disabled=true;
			document.form.estado.disabled=true;
			document.form.de.disabled=true;
			document.form.destinatario.disabled=true;
			document.form.asunto.disabled=true;
			document.form.destinatario.value="";
			document.form.fechaInicio.value="";
			document.form.fechaFin.value="";
			document.form.codigo.disabled=false;
		}
	}
	function buscar(pagina){
		if(pagina){
			document.getElementById("pagina").value = pagina;
		}
		var opcion2 = document.getElementById("opcion2").checked;
		var codigo = document.getElementById("codigo").value;
		if(opcion2==true && codigo==''){
			alert('Debe introducir el c'+oACUTE+'digo de la comunicaci'+oACUTE+'n');
			return;
		}else{
	  		document.form.submit();
		}
	}
	function cambiarBandeja(){
		var bandeja = document.getElementById('bandeja');
		var selectDe = document.getElementById('de');
		var i;
		for(i=selectDe.length-1;i>0;i--){
			selectDe.remove(i);
		}
		var deAuxiliar;
		if(bandeja.options[bandeja.selectedIndex].value=="<?= BANDEJA_RECIBIDOS?>"){
			deAuxiliar = deRecibidos;
		}else if(bandeja.options[bandeja.selectedIndex].value=="<?= BANDEJA_ENVIADOS?>"){
			deAuxiliar = deEnviados;
		}
		for(i=0;i<deAuxiliar.length;i++){
			var option = document.createElement('option');
			option.value = deAuxiliar[i][0];
			option.text = deAuxiliar[i][1];
			try{
				selectDe.add(option, null); // standards compliant; doesn't work in IE
			}catch(ex){
				selectDe.add(option); // IE only
			}
		}
		buscar(1);
	}
</script>
</head>
<body class="normal">
	<form name="form" action="buscar.php" method="post">
		<input type="hidden" id="pagina" name="pagina" value="<?= $pagina?>"/>
		<input type="hidden" id="tipo" name="tipo" value="<?= $tipo?>"/>
		<table width="700px" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td height="21" colspan="4" class="normalNegroNegrita" align="center">
					B&uacute;squeda de 
					<?php
						if($tipo=="memo"){
							echo "Memorandos";
						}else if($tipo=="ofic"){
							echo "Oficios";
						}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="center" class="normalNegroNegrita">
					Bandeja&nbsp;&nbsp;
					<select id="bandeja" name="bandeja" class="normalNegro" onchange="cambiarBandeja();">
						<option value="<?=BANDEJA_RECIBIDOS?>" <?php if($bandeja == BANDEJA_RECIBIDOS){ echo "selected='selected'";} ?>>Recibidos</option>
						<?php 
						if(	(substr($user_perfil_id, 0, 2)."000")==PERFIL_ASISTENTE_ADMINISTRATIVO
							 || $user_perfil_id==PERFIL_ASISTENTE_EJECUTIVO
							 || $user_perfil_id==PERFIL_ASISTENTE_PRESIDENCIA
							 || (substr($user_perfil_id, 0, 2)."000")==PERFIL_JEFE
							 || (substr($user_perfil_id, 0, 2)."000")==PERFIL_GERENTE
							 || (substr($user_perfil_id, 0, 2)."000")==PERFIL_DIRECTOR
							 || $user_perfil_id==PERFIL_DIRECTOR_EJECUTIVO
							 || $user_perfil_id==PERFIL_PRESIDENTE){					
						?>
						<option value="<?=BANDEJA_ENVIADOS?>" <?php if($bandeja == BANDEJA_ENVIADOS){ echo "selected='selected'";} ?>>Enviados</option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr class="td_gray">
				<td class="normalNegroNegrita" colspan="4">
					<input id="opcion2" name="opcion" type="radio" value="2" class="normal" onclick="javascript:seleccionarOpcion(2)" <?php if($opcion=="2"){echo 'checked="checked"';}?>/>
					B&uacute;squeda por C&oacute;digo
				</td>
			</tr>
			<tr>
				<td height="20" align="center" class="normal">&nbsp;</td>
				<td align="left">
					C&oacute;digo de 
					<?php
						if($tipo=="memo"){
							echo "memorando";
						}else if($tipo=="ofic"){
							echo "oficio";
						}
					?>
				</td>
				<td colspan="2">
					<input name="codigo" type="text" class="normalNegro" id="codigo" size="12" <?php if($codigo && $codigo!=""){echo "value='".$codigo."'";}else{echo "disabled='disabled'";}?>/>
				</td>
			</tr>
			<tr class="td_gray">
				<td class="normalNegroNegrita" colspan="4">
					<input id="opcion1" name="opcion" type="radio" value="1" onclick="javascript:seleccionarOpcion(1)" class="normal" <?php if(!$codigo || $codigo=="" || $opcion=="1"){echo 'checked="checked"';}?>/>
					B&uacute;squeda por criterios m&uacute;ltiples
				</td>
			</tr>
			<tr>
				<td height="20" align="center" class="normal">&nbsp;</td>
				<td>
					Estado 
				</td>
				<td colspan="2">
					<select id="estado" name="estado" class="normalNegro">
						<option value="">Todos</option>
						<option value="10" <?php if($estado==ESTADO_TRANSITO){echo 'selected="selected"';}?>>En Transito</option>
						<option value="15" <?php if($estado==ESTADO_ANULADO){echo 'selected="selected"';}?>>Anulados</option>
					</select>
				</td>
			</tr>
			<tr>
				<td height="20" align="center" class="normal">&nbsp;</td>
				<td>
					Remitente 
				</td>
				<td colspan="2">
					<select id="de" name="de" class="normalNegro">
						<option value="">Todos</option>
						<?php
						$nivelOficinaGerencia = "(5,4,3)";
						$queryRecibidos = 	"SELECT depe_id,depe_nombre ".
											"FROM sai_dependenci ".
											"WHERE ".
												"depe_nivel IN ".$nivelOficinaGerencia." AND ".
												"esta_id <> ".ESTADO_ANULADO." ".
											"ORDER BY depe_nombre";
						$resultadoRecibidos = pg_exec($conexion, $queryRecibidos);

						if($user_perfil_id==PERFIL_PRESIDENTE){//PERFIL PRESIDENTE
							$queryEnviados = 	"SELECT depe_id,depe_nombre ".
												"FROM sai_dependenci ".
												"WHERE ".
													"depe_nivel IN ".$nivelOficinaGerencia." AND ".
													"esta_id <> ".ESTADO_ANULADO." ".
												"ORDER BY depe_nombre";
						}else if($user_perfil_id==PERFIL_DIRECTOR_EJECUTIVO){//PERFIL DIRECTOR EJECUTIVO
							$queryEnviados = 	"SELECT depe_id,depe_nombre ".
												"FROM sai_dependenci ".
												"WHERE ".
													"depe_id NOT IN ('150') AND ".
													"depe_nivel IN ".$nivelOficinaGerencia." AND ".
													"esta_id <> ".ESTADO_ANULADO." ".
												"ORDER BY depe_nombre";
						}else if(substr($user_perfil_id, 0, 2)."000"==PERFIL_GERENTE || substr($user_perfil_id, 0, 2)."000"==PERFIL_DIRECTOR){//PERFIL GERENTE O DIRECTOR
							$queryEnviados = 	"SELECT depe_id,depe_nombre ".
												"FROM sai_dependenci ".
												"WHERE ".
													"(".
														"depe_id = '".$user_depe_id."' OR ".
														"depe_id IN (SELECT depe_id FROM sai_dependenci WHERE depe_id_sup = '".$user_depe_id."')".
													") AND ".
													"depe_nivel IN ".$nivelOficinaGerencia." AND ".
												"esta_id <> ".ESTADO_ANULADO." ".
												"ORDER BY depe_nombre";
						}else{//PERFIL JEFE
							$queryEnviados = 	"SELECT depe_id,depe_nombre ".
												"FROM sai_dependenci ".
												"WHERE ".
													"depe_id IN ('".$user_depe_id."') AND ".
													"depe_nivel IN ".$nivelOficinaGerencia." AND ".
												"esta_id <> ".ESTADO_ANULADO." ".
												"ORDER BY depe_nombre";
						}
						$resultadoEnviados = pg_exec($conexion, $queryEnviados);						
						
						if($bandeja==BANDEJA_RECIBIDOS){
							$resultado = $resultadoRecibidos;
						}else{
							$resultado = $resultadoEnviados;
						}
						$numeroFilas = pg_numrows($resultado);
						for($i = 0; $i < $numeroFilas; $i++) {
							$row = pg_fetch_array($resultado, $i);
						?>
							<option value="<?= $row["depe_id"]?>" <?php if($de==$row["depe_id"]){echo 'selected="selected"';}?>><?=$row["depe_nombre"]?></option>
						<?php 
							}
						?>
					</select>
					<script>
						var deRecibidos = new Array();
						var deEnviados = new Array();
					</script>
					<?php 
					$numeroFilas = pg_numrows($resultadoRecibidos);
					for($i = 0; $i < $numeroFilas; $i++) {
						$row = pg_fetch_array($resultadoRecibidos, $i);
					?>
						<script>
							deRecibidos[<?= $i?>]= new Array();
							deRecibidos[<?= $i?>][0]=<?= $row["depe_id"]?>;
							deRecibidos[<?= $i?>][1]='<?= $row["depe_nombre"]?>';
						</script>	
					<?php
					}
					
					$numeroFilas = pg_numrows($resultadoEnviados);
					$dependencias = "(";
					for($i = 0; $i < $numeroFilas; $i++) {
						$row = pg_fetch_array($resultadoEnviados, $i);
						$dependencias .= "'".$row["depe_id"]."',";
					?>
						<script>
							deEnviados[<?= $i?>]= new Array();
							deEnviados[<?= $i?>][0]=<?= $row["depe_id"]?>;
							deEnviados[<?= $i?>][1]='<?= $row["depe_nombre"]?>';
						</script>	
					<?php
					}
					$dependencias = substr($dependencias, 0, -1).")";
					?>					
				</td>
			</tr>
			<tr>
				<td height="20" align="center" class="normal">&nbsp;</td>
				<td valign="top">
					Destinatario 
				</td>
				<td colspan="2">
					<input autocomplete="off" size="69" type="text" id="destinatario" name="destinatario" class="normalNegro" value="<?= $destinatarioStr?>"/>
					<br/>Introduzca la c&eacute;dula o una palabra contenida en el nombre del destinatario.
					<?php
						$query = 	"SELECT ".
										"se.empl_cedula AS cedula, ".
										"UPPER(se.empl_nombres || ' ' || se.empl_apellidos) AS nombre, ".
										"UPPER(sc.carg_nombre) AS cargo, ".
										"UPPER(sd.depe_nombre) AS dependencia ".
									"FROM sai_empleado se, sai_cargo sc, sai_dependenci sd ".
									"WHERE ".
										"se.esta_id <> ".ESTADO_INACTIVO." AND ".
										"se.carg_fundacion = sc.carg_fundacion AND ".
										"se.depe_cosige = sd.depe_id ".
									"ORDER BY se.empl_nombres, se.empl_apellidos ";
						$resultado = pg_exec($conexion, $query);
						$arreglo = "";
						$cedulas = "";
						$nombres = "";
						$cargos = "";
						$dependenciasDestinatario = "";
						while($row=pg_fetch_array($resultado)){
							$cedulas .= "'".$row["cedula"]."',";
							$nombres .= "'".str_replace("\n"," ",$row["nombre"])."',";
							$cargos .= "'".str_replace("\n"," ",$row["cargo"])."',";
							$dependenciasDestinatario .= "'".str_replace("\n"," ",$row["dependencia"])."',";
							$arreglo .= "'".$row["cedula"]." : ".str_replace("\n"," ",$row["nombre"])."',";
						}
						$arreglo = substr($arreglo, 0, -1);
						$cedulas = substr($cedulas, 0, -1);
						$nombres = substr($nombres, 0, -1);
						$cargos = substr($cargos, 0, -1);
						$dependenciasDestinatario = substr($dependenciasDestinatario, 0, -1);
					?>
					<script>
						var destinatario = new Array(<?= $arreglo?>);
						var cedulas = new Array(<?= $cedulas?>);
						var nombres = new Array(<?= $nombres?>);
						var cargos = new Array(<?= $cargos?>);
						var dependencias = new Array(<?= $dependenciasDestinatario?>);
						actb(document.getElementById('destinatario'),destinatario);
					</script>
				</td>
			</tr>
			<tr>
				<td height="20" align="center" class="normal">&nbsp;</td>
				<td align="left">
					Asunto 
				</td>
				<td colspan="2">
					<select id="asunto" name="asunto" class="normalNegro">
						<option value="">Todos</option>
						<?php
						$query = 	"SELECT id,UPPER(nombre) AS nombre ".
									"FROM sai_memorando_asunto ".
									"WHERE ".
										"esta_id <> ".ESTADO_ANULADO." ".
									"ORDER BY nombre";
						$resultado = pg_exec($conexion, $query);
						$numeroFilas = pg_numrows($resultado);
						for($i = 0; $i < $numeroFilas; $i++) {
							$row = pg_fetch_array($resultado, $i);
						?>
							<option value="<?= $row["id"]?>" <?php if($asunto==$row["id"]){echo 'selected="selected"';}?>><?=$row["nombre"]?></option>
						<?php 
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td colspan="2">
					<!-- Agregar los accesos rapidos de las fechas (Hoy, ayer, semana, semana pasada, etc.) -->
					<?php VistaFechas::ConstruirAccesosRapidosFechas("fechaInicio", "fechaFin", "dd/mm/yy") ?>
				</td>
			</tr>
			<tr>
				<td align="center" width="20px">&nbsp;</td>
				<td height="20" width="140px" align="left">
					Elaborados entre
				</td>
				<td width="220px">
					Fecha Inicio:
					<input 
						type="text" size="10" id="fechaInicio" name="fechaInicio"
						class="dateparse" onfocus="javascript: comparar_fechas(document.getElementById('fechaInicio').value,document.getElementById('fechaFin').value);"
						readonly="readonly" <?php if($fechaInicio && $fechaInicio!=""){echo "value='".$fechaInicio."'";}?>
						style="width: 100px;"/>
					<a href="javascript:void(0);" onclick="if(document.getElementById('opcion1').checked==true){g_Calendar.show(event, 'fechaInicio');}" title="Fecha inicio">
						<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Fecha inicio"/>
					</a>
				</td>
				<td width="220px">
					Fecha Fin:
					<input
						type="text" size="10" id="fechaFin" name="fechaFin"
						class="dateparse" onfocus="javascript: comparar_fechas(document.getElementById('fechaInicio').value,document.getElementById('fechaFin').value);"
						readonly="readonly" <?php if($fechaFin && $fechaFin!=""){echo "value='".$fechaFin."'";}?>
						style="width: 100px;"/>
					<a href="javascript:void(0);" onclick="if(document.getElementById('opcion1').checked==true){g_Calendar.show(event, 'fechaFin');}" title="Fecha fin">
						<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Fecha fin"/>
					</a>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="center" height="40px" valign="middle">
					<input class="normalNegro" type="button" value="Buscar" onclick="buscar(1);"/>
				</td>
			</tr>
		</table>
	</form>
<?php
if ($bandeja && $bandeja!="") {
	$tamanoPagina = 10;
	$tamanoVentana = 20;
	$desplazamiento = ($pagina-1)*$tamanoPagina;
	$condicion = false;
	
	$cedulasPresidentes = array();
	$cargo = substr(PERFIL_PRESIDENTE,0,2);
	$dependencia = substr(PERFIL_PRESIDENTE,2);
	$query = 	"SELECT ".
					"se.empl_cedula AS cedula ".
				"FROM sai_empleado se, sai_cargo sc, sai_dependenci sd ".
				"WHERE ".
					"se.esta_id <> ".ESTADO_INACTIVO." AND ".
					"se.carg_fundacion = sc.carg_fundacion AND ".
					"se.depe_cosige = sd.depe_id AND ".
					"se.carg_fundacion = '".$cargo."' AND ".
					"se.depe_cosige = '".$dependencia."' ".	
				"ORDER BY se.empl_nombres, se.empl_apellidos ";
	$resultado = pg_exec($conexion, $query);
	$i=0;
	while($row=pg_fetch_array($resultado)){
		if($i==0 && $user_perfil_id == PERFIL_ASISTENTE_PRESIDENCIA){
			$cedulaSuperior = $row["cedula"];
		}
		$cedulasPresidentes[sizeof($cedulasPresidentes)] = $row["cedula"];
		$i++;
	}

	$cedulasDirectorEjecutivo = array();
	$cargo = substr(PERFIL_DIRECTOR_EJECUTIVO,0,2);
	$dependencia = substr(PERFIL_DIRECTOR_EJECUTIVO,2);
	$query = 	"SELECT ".
					"se.empl_cedula AS cedula ".
				"FROM sai_empleado se, sai_cargo sc, sai_dependenci sd ".
				"WHERE ".
					"se.esta_id <> ".ESTADO_INACTIVO." AND ".
					"se.carg_fundacion = sc.carg_fundacion AND ".
					"se.depe_cosige = sd.depe_id AND ".
					"se.carg_fundacion = '".$cargo."' AND ".
					"se.depe_cosige = '".$dependencia."' ".	
				"ORDER BY se.empl_nombres, se.empl_apellidos ";
	$resultado = pg_exec($conexion, $query);
	$i=0;
	while($row=pg_fetch_array($resultado)){
		if($i==0 && $user_perfil_id == PERFIL_ASISTENTE_EJECUTIVO){
			$cedulaSuperior = $row["cedula"];
		}
		$cedulasDirectorEjecutivo[sizeof($cedulasDirectorEjecutivo)] = $row["cedula"];
		$i++;
	}
	
	$cedulasGerenteDirector = array();
	$cargoGerente = substr(PERFIL_GERENTE,0,2);
	$cargoDirector = substr(PERFIL_DIRECTOR,0,2);
	$query = 	"SELECT ".
					"se.empl_cedula AS cedula, ".
					"se.depe_cosige AS dependencia ".
				"FROM sai_empleado se, sai_cargo sc, sai_dependenci sd ".
				"WHERE ".
					"se.esta_id <> ".ESTADO_INACTIVO." AND ".
					"se.carg_fundacion = sc.carg_fundacion AND ".
					"se.depe_cosige = sd.depe_id AND ".
					"(se.carg_fundacion = '".$cargoGerente."' OR se.carg_fundacion = '".$cargoDirector."') ".
				"ORDER BY se.empl_nombres, se.empl_apellidos ";
	$resultado = pg_exec($conexion, $query);
	while($row=pg_fetch_array($resultado)){
		if(substr($user_perfil_id,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO && $dependenciaUsuario==$row["dependencia"]){
			$cedulaSuperior = $row["cedula"];
		}
		$cedulasGerenteDirector[sizeof($cedulasGerenteDirector)] = $row["cedula"];
	}
	
	$cedulasJefe = array();
	$cargo = substr(PERFIL_JEFE,0,2);
	$query = 	"SELECT ".
					"se.empl_cedula AS cedula ".
				"FROM sai_empleado se, sai_cargo sc, sai_dependenci sd ".
				"WHERE ".
					"se.esta_id <> ".ESTADO_INACTIVO." AND ".
					"se.carg_fundacion = sc.carg_fundacion AND ".
					"se.depe_cosige = sd.depe_id AND ".
					"se.carg_fundacion = '".$cargo."' ".
				"ORDER BY se.empl_nombres, se.empl_apellidos ";
	$resultado = pg_exec($conexion, $query);
	while($row=pg_fetch_array($resultado)){
		$cedulasJefe[sizeof($cedulasJefe)] = $row["cedula"];
	}
	
	$cedulasCoordinador = array();
	$cargo = substr(PERFIL_COORDINADOR,0,2);
	$query = 	"SELECT ".
					"se.empl_cedula AS cedula ".
				"FROM sai_empleado se, sai_cargo sc, sai_dependenci sd ".
				"WHERE ".
					"se.esta_id <> ".ESTADO_INACTIVO." AND ".
					"se.carg_fundacion = sc.carg_fundacion AND ".
					"se.depe_cosige = sd.depe_id AND ".
					"se.carg_fundacion = '".$cargo."' ".
				"ORDER BY se.empl_nombres, se.empl_apellidos ";
	$resultado = pg_exec($conexion, $query);
	while($row=pg_fetch_array($resultado)){
		$cedulasCoordinador[sizeof($cedulasCoordinador)] = $row["cedula"];
	}

	function estanTodos($totales, $destinatarios){
		if(sizeof($totales)==sizeof($destinatarios)){
			$i=0;
			$estanTodos = true;
			while($i<sizeof($totales) && $estanTodos == true){
				$j=0;
				$estanTodos = false;
				while($j<sizeof($destinatarios) && $estanTodos == false){
					if($totales[$i]==$destinatarios[$j][0]){
						$estanTodos = true;
					}
					$j++;
				}
				$i++;
			}
			return $estanTodos;
		}else{
			return false;
		}
	}
	
	$contador = 0;
	$idComunicaciones = "";
	
	echo "<pre>Tipo";
	print_r($tipo);
	echo "</pre>";
	
	if($tipo=="memo"){
		if($bandeja==BANDEJA_RECIBIDOS){
			$query = 	"SELECT COUNT(DISTINCT(id)) ".
						"FROM ".
							"(".			
							"SELECT ".
								"sm.memo_id AS id ".
							"FROM ".
								"sai_memorando sm, ".
								"sai_memorando_para smp ".
								(($destinatario!="")?", (".
															"SELECT DISTINCT(smp.memo_id) AS memo_id FROM sai_memorando_para smp WHERE smp.cedula = '".$destinatario."' ".
															"UNION ".
															"SELECT DISTINCT(smc.memo_id) AS memo_id FROM sai_memorando_cc smc WHERE smc.cedula = '".$destinatario."' ".
														") AS smd ":"").
							"WHERE ";
			if(	(substr($user_perfil_id,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) ||
				($user_perfil_id == PERFIL_ASISTENTE_EJECUTIVO) ||
				($user_perfil_id == PERFIL_ASISTENTE_PRESIDENCIA)){
				$query .=		"(smp.cedula = '".$usuario."' OR smp.cedula = '".$cedulaSuperior."') AND ".(($destinatario!="")?" smp.memo_id = smd.memo_id AND ":"");
			}else{
				$query .=		"smp.cedula = '".$usuario."' AND ".(($destinatario!="")?" smp.memo_id = smd.memo_id AND ":"");
			}
			$query .=			"smp.memo_id = sm.memo_id ";
			if($opcion=="1"){				
				if($fechaInicio!="" && $fechaFin!=""){
					$query .=	"AND sm.fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ";
				}
				if($estado!=""){
					$query .=	"AND sm.esta_id = ".$estado." ";
				}
				if($de!=""){
					$query .=	"AND sm.depe_id = '".$de."' ";
				}
				if($asunto!=""){
					$query .=	"AND sm.id_asunto = '".$asunto."' ";
				}
			}else if($opcion=="2"){
				$query .=		"AND LOWER(sm.memo_id) = '".strtolower($codigo)."' ";
			}
			$query .=		"UNION ".
							"SELECT ".
								"sm.memo_id AS id ".
							"FROM ".
								"sai_memorando sm, ".
								"sai_memorando_cc smc ".
								(($destinatario!="")?", (".
															"SELECT DISTINCT(smp.memo_id) AS memo_id FROM sai_memorando_para smp WHERE smp.cedula = '".$destinatario."' ".
															"UNION ".
															"SELECT DISTINCT(smc.memo_id) AS memo_id FROM sai_memorando_cc smc WHERE smc.cedula = '".$destinatario."' ".
														") AS smd ":"").
							"WHERE ";
			if(	(substr($user_perfil_id,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) ||
				($user_perfil_id == PERFIL_ASISTENTE_EJECUTIVO) ||
				($user_perfil_id == PERFIL_ASISTENTE_PRESIDENCIA)){
				$query .=		"(smc.cedula = '".$usuario."' OR smc.cedula = '".$cedulaSuperior."') AND ".(($destinatario!="")?" smc.memo_id = smd.memo_id AND ":"");
			}else{
				$query .=		"smc.cedula = '".$usuario."' AND ".(($destinatario!="")?" smc.memo_id = smd.memo_id AND ":"");
			}
			$query .=			"smc.memo_id = sm.memo_id ";
			if($opcion=="1"){				
				if($fechaInicio!="" && $fechaFin!=""){
					$query .=	"AND sm.fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ";
				}
				if($estado!=""){
					$query .=	"AND sm.esta_id = ".$estado." ";
				}
				if($de!=""){
					$query .=	"AND sm.depe_id = '".$de."' ";
				}
				if($asunto!=""){
					$query .=	"AND sm.id_asunto = '".$asunto."' ";
				}
			}else if($opcion=="2"){
				$query .=		"AND LOWER(sm.memo_id) = '".strtolower($codigo)."' ";
			}
			$query .=		") AS s ";
			$resultadoContador = pg_exec($conexion, $query);
			$row = pg_fetch_array($resultadoContador, 0);
			$contador = $row[0];
			
			$query = 	"SELECT DISTINCT(id), s.fecha_memorando ".
						"FROM ".
							"(".			
							"SELECT ".
								"sm.memo_id AS id, ".
								"sm.fecha_memorando ".
							"FROM ".
								"sai_memorando sm, ".
								"sai_memorando_para smp ".
								(($destinatario!="")?", (".
															"SELECT DISTINCT(smp.memo_id) AS memo_id FROM sai_memorando_para smp WHERE smp.cedula = '".$destinatario."' ".
															"UNION ".
															"SELECT DISTINCT(smc.memo_id) AS memo_id FROM sai_memorando_cc smc WHERE smc.cedula = '".$destinatario."' ".
														") AS smd ":"").
							"WHERE ";
			if(	(substr($user_perfil_id,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) ||
				($user_perfil_id == PERFIL_ASISTENTE_EJECUTIVO) ||
				($user_perfil_id == PERFIL_ASISTENTE_PRESIDENCIA)){
				$query .=		"(smp.cedula = '".$usuario."' OR smp.cedula = '".$cedulaSuperior."') AND ".(($destinatario!="")?" smp.memo_id = smd.memo_id AND ":"");
			}else{
				$query .=		"smp.cedula = '".$usuario."' AND ".(($destinatario!="")?" smp.memo_id = smd.memo_id AND ":"");
			}
			$query .=			"smp.memo_id = sm.memo_id ";
			if($opcion=="1"){				
				if($fechaInicio!="" && $fechaFin!=""){
					$query .=	"AND sm.fecha_memorando BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ";
				}
				if($estado!=""){
					$query .=	"AND sm.esta_id = ".$estado." ";
				}
				if($de!=""){
					$query .=	"AND sm.depe_id = '".$de."' ";
				}
				if($asunto!=""){
					$query .=	"AND sm.id_asunto = '".$asunto."' ";
				}
			}else if($opcion=="2"){
				$query .=		"AND LOWER(sm.memo_id) = '".strtolower($codigo)."' ";
			}
			$query .=		"UNION ".
							"SELECT ".
								"sm.memo_id AS id, ".
								"sm.fecha_memorando ".
							"FROM ".
								"sai_memorando sm, ".
								"sai_memorando_cc smc ".
								(($destinatario!="")?", (".
															"SELECT DISTINCT(smp.memo_id) AS memo_id FROM sai_memorando_para smp WHERE smp.cedula = '".$destinatario."' ".
															"UNION ".
															"SELECT DISTINCT(smc.memo_id) AS memo_id FROM sai_memorando_cc smc WHERE smc.cedula = '".$destinatario."' ".
														") AS smd ":"").
							"WHERE ";
			if(	(substr($user_perfil_id,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) ||
				($user_perfil_id == PERFIL_ASISTENTE_EJECUTIVO) ||
				($user_perfil_id == PERFIL_ASISTENTE_PRESIDENCIA)){
				$query .=		"(smc.cedula = '".$usuario."' OR smc.cedula = '".$cedulaSuperior."') AND ".(($destinatario!="")?" smc.memo_id = smd.memo_id AND ":"");
			}else{
				$query .=		"smc.cedula = '".$usuario."' AND ".(($destinatario!="")?" smc.memo_id = smd.memo_id AND ":"");
			}
			$query .=			"smc.memo_id = sm.memo_id ";
			if($opcion=="1"){				
				if($fechaInicio!="" && $fechaFin!=""){
					$query .=	"AND sm.fecha_memorando BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ";
				}
				if($estado!=""){
					$query .=	"AND sm.esta_id = ".$estado." ";
				}
				if($de!=""){
					$query .=	"AND sm.depe_id = '".$de."' ";
				}
				if($asunto!=""){
					$query .=	"AND sm.id_asunto = '".$asunto."' ";
				}
			}else if($opcion=="2"){
				$query .=		"AND LOWER(sm.memo_id) = '".strtolower($codigo)."' ";
			}
			$query .=		") AS s ".
						"ORDER BY s.fecha_memorando DESC ".
						"LIMIT ".$tamanoPagina." OFFSET ".$desplazamiento;
			$resultado = pg_exec($conexion, $query);
			while($row=pg_fetch_array($resultado)){
				$idComunicaciones .= "'".$row["id"]."',";
			}
			$idComunicaciones = substr($idComunicaciones, 0, -1);
		}else if($bandeja==BANDEJA_ENVIADOS){
			$hayCondicion = false;
			$query = 	"SELECT ".
							"COUNT(DISTINCT(sm.memo_id)) ".
						"FROM ".
							"sai_memorando sm ";
			if($opcion=="1"){
				$query .= 	(($destinatario!="")?", (".
														"SELECT DISTINCT(smp.memo_id) AS memo_id FROM sai_memorando_para smp WHERE smp.cedula = '".$destinatario."' ".
														"UNION ".
														"SELECT DISTINCT(smc.memo_id) AS memo_id FROM sai_memorando_cc smc WHERE smc.cedula = '".$destinatario."' ".
													") AS smd ":"").
							"WHERE ".
							(($destinatario!="")?" sm.memo_id = smd.memo_id AND ":"");
				if($fechaInicio!="" && $fechaFin!=""){
					$query .=	"sm.fecha_memorando BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ";
				}
				if($estado!=""){
					$query .=	"sm.esta_id = ".$estado." AND ";
				}
				if($de!=""){
					$query .=	"sm.depe_id = '".$de."' ";
				}else{
					$query .=	"sm.depe_id IN ".$dependencias." ";
				}
				if($asunto!=""){
					$query .=	"AND sm.id_asunto = '".$asunto."' ";
				}
			}else if($opcion=="2"){
				$query .=	"WHERE ".
							"LOWER(sm.memo_id) = '".strtolower($codigo)."' AND ".
							"sm.depe_id IN ".$dependencias." ";
			}
			$resultadoContador = pg_exec($conexion, $query);
			$row = pg_fetch_array($resultadoContador, 0);
			$contador = $row[0];
			
			$query = 	"SELECT ".
							"DISTINCT(sm.memo_id) AS id, ".
							"sm.fecha_memorando ".
						"FROM ".
							"sai_memorando sm ";
			if($opcion=="1"){
				$query .=	(($destinatario!="")?", (".
														"SELECT DISTINCT(smp.memo_id) AS memo_id FROM sai_memorando_para smp WHERE smp.cedula = '".$destinatario."' ".
														"UNION ".
														"SELECT DISTINCT(smc.memo_id) AS memo_id FROM sai_memorando_cc smc WHERE smc.cedula = '".$destinatario."' ".
													") AS smd ":"").
							"WHERE ".
							(($destinatario!="")?" sm.memo_id = smd.memo_id AND ":"");
				if($fechaInicio!="" && $fechaFin!=""){
					$query .=	"sm.fecha_memorando BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ";
				}
				if($estado!=""){
					$query .=	"sm.esta_id = ".$estado." AND ";
				}
				if($de!=""){
					$query .=	"sm.depe_id = '".$de."' ";
				}else{										
					$query .=	"sm.depe_id IN ".$dependencias." ";
				}
				if($asunto!=""){
					$query .=	"AND sm.id_asunto = '".$asunto."' ";
				}
			}else if($opcion=="2"){
				$query .= 	"WHERE ".
							"LOWER(sm.memo_id) = '".strtolower($codigo)."' AND ".
							"sm.depe_id IN ".$dependencias." ";
			}
			$query .=	"ORDER BY sm.fecha_memorando DESC, sm.memo_id DESC ".
						"LIMIT ".$tamanoPagina." OFFSET ".$desplazamiento;
			$resultado = pg_exec($conexion, $query);
			while($row=pg_fetch_array($resultado)){
				$idComunicaciones .= "'".$row["id"]."',";
			}
			$idComunicaciones = substr($idComunicaciones, 0, -1);
		}
		$totalPaginas = ($contador%$tamanoPagina == 0)?$contador/$tamanoPagina:intval($contador/$tamanoPagina)+1;
		
		$query = 	"SELECT ".
						"sm.memo_id AS id, ".
						"to_char(sm.fecha_memorando,'DD/MM/YYYY') AS fecha_cadena, ".
						"sd.depe_nombre AS de, ".
						"sma.nombre AS asunto, ".
						"sem.empl_cedula AS cedula, ".
						"sem.empl_nombres || ' ' || sem.empl_apellidos AS personal, ".
						"sde.depe_nombre AS personal_dependencia, ".
						"se.esta_nombre AS estado, ".
						"sem.depe_cosige, ".
						"sem.carg_fundacion, ".
						"sm.usua_login ".
					"FROM ".
						"sai_memorando sm, ".
						"sai_dependenci sd, ".
						"sai_memorando_asunto sma,".
						"sai_memorando_para smp, ".
						"sai_empleado sem, ".
						"sai_dependenci sde, ".
						"sai_estado se ".
					"WHERE ".
						"sm.memo_id IN (".$idComunicaciones.") AND ".
						"sm.depe_id = sd.depe_id AND ".
						"sm.id_asunto = sma.id AND ".
						"sm.memo_id = smp.memo_id AND ".
						"smp.cedula = sem.empl_cedula AND ".
						"sem.depe_cosige = sde.depe_id AND ".
						"sm.esta_id = se.esta_id ".
					"ORDER BY sm.fecha_memorando DESC, sm.memo_id DESC, smp.cedula";
	}else if($tipo=="ofic"){
		if($bandeja==BANDEJA_RECIBIDOS){
			$query = 	"SELECT COUNT(DISTINCT(id)) ".
						"FROM ".
							"(".			
							"SELECT ".
								"so.ofic_id AS id ".
							"FROM ".
								"sai_oficio so, ".
								"sai_oficio_para sop ".
								(($destinatario!="")?", (".
															"SELECT DISTINCT(sop.ofic_id) AS ofic_id FROM sai_oficio_para sop WHERE sop.cedula = '".$destinatario."' ".
															"UNION ".
															"SELECT DISTINCT(soc.ofic_id) AS ofic_id FROM sai_oficio_cc soc WHERE soc.cedula = '".$destinatario."' ".
														") AS sod ":"").
							"WHERE ";
			if(	(substr($user_perfil_id,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) ||
				($user_perfil_id == PERFIL_ASISTENTE_EJECUTIVO) ||
				($user_perfil_id == PERFIL_ASISTENTE_PRESIDENCIA)){
				$query .=		"(sop.cedula = '".$usuario."' OR sop.cedula = '".$cedulaSuperior."') AND ".(($destinatario!="")?" sop.ofic_id = sod.ofic_id AND ":"");
			}else{
				$query .=		"sop.cedula = '".$usuario."' AND ".(($destinatario!="")?" sop.ofic_id = sod.ofic_id AND ":"");
			}
			$query .=			"sop.ofic_id = so.ofic_id ";
			if($opcion=="1"){
				if($fechaInicio!="" && $fechaFin!=""){
					$query .=	"AND so.fecha_oficio BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ";
				}
				if($estado!=""){
					$query .=	"AND so.esta_id = ".$estado." ";
				}
				if($de!=""){
					$query .=	"AND so.depe_id = '".$de."' ";
				}
				if($asunto!=""){
					$query .=	"AND so.id_asunto = '".$asunto."' ";
				}
			}else if($opcion=="2"){
				$query .=		"AND LOWER(so.ofic_id) = '".strtolower($codigo)."' ";
			}
			$query .=		"UNION ".
							"SELECT ".
								"so.ofic_id AS id ".
							"FROM ".
								"sai_oficio so, ".
								"sai_oficio_cc soc ".
								(($destinatario!="")?", (".
															"SELECT DISTINCT(sop.ofic_id) AS ofic_id FROM sai_oficio_para sop WHERE sop.cedula = '".$destinatario."' ".
															"UNION ".
															"SELECT DISTINCT(soc.ofic_id) AS ofic_id FROM sai_oficio_cc soc WHERE soc.cedula = '".$destinatario."' ".
														") AS sod ":"").
							"WHERE ";
			if(	(substr($user_perfil_id,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) ||
				($user_perfil_id == PERFIL_ASISTENTE_EJECUTIVO) ||
				($user_perfil_id == PERFIL_ASISTENTE_PRESIDENCIA)){
				$query .=		"(soc.cedula = '".$usuario."' OR soc.cedula = '".$cedulaSuperior."') AND ".(($destinatario!="")?" soc.ofic_id = sod.ofic_id AND ":"");
			}else{
				$query .=		"soc.cedula = '".$usuario."' AND ".(($destinatario!="")?" soc.ofic_id = sod.ofic_id AND ":"");
			}
			$query .=			"soc.ofic_id = so.ofic_id ";
			if($opcion=="1"){				
				if($fechaInicio!="" && $fechaFin!=""){
					$query .=	"AND so.fecha_oficio BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ";
				}
				if($estado!=""){
					$query .=	"AND so.esta_id = ".$estado." ";
				}
				if($de!=""){
					$query .=	"AND so.depe_id = '".$de."' ";
				}
				if($asunto!=""){
					$query .=	"AND so.id_asunto = '".$asunto."' ";
				}
			}else if($opcion=="2"){
				$query .=		"AND LOWER(so.ofic_id) = '".strtolower($codigo)."' ";
			}
			$query .=		") AS s ";
			$resultadoContador = pg_exec($conexion, $query);
			$row = pg_fetch_array($resultadoContador, 0);
			$contador = $row[0];
			
			$query = 	"SELECT DISTINCT(id), s.fecha_oficio ".
						"FROM ".
							"(".			
							"SELECT ".
								"so.ofic_id AS id, ".
								"so.fecha_oficio ".
							"FROM ".
								"sai_oficio so, ".
								"sai_oficio_para sop ".
								(($destinatario!="")?", (".
															"SELECT DISTINCT(sop.ofic_id) AS ofic_id FROM sai_oficio_para sop WHERE sop.cedula = '".$destinatario."' ".
															"UNION ".
															"SELECT DISTINCT(soc.ofic_id) AS ofic_id FROM sai_oficio_cc soc WHERE soc.cedula = '".$destinatario."' ".
														") AS sod ":"").
							"WHERE ";
			if(	(substr($user_perfil_id,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) ||
				($user_perfil_id == PERFIL_ASISTENTE_EJECUTIVO) ||
				($user_perfil_id == PERFIL_ASISTENTE_PRESIDENCIA)){
				$query .=		"(sop.cedula = '".$usuario."' OR sop.cedula = '".$cedulaSuperior."') AND ".(($destinatario!="")?" sop.ofic_id = sod.ofic_id AND ":"");
			}else{
				$query .=		"sop.cedula = '".$usuario."' AND ".(($destinatario!="")?" sop.ofic_id = sod.ofic_id AND ":"");
			}
			$query .=			"sop.ofic_id = so.ofic_id ";
			if($opcion=="1"){				
				if($fechaInicio!="" && $fechaFin!=""){
					$query .=	"AND so.fecha_oficio BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ";
				}
				if($estado!=""){
					$query .=	"AND so.esta_id = ".$estado." ";
				}
				if($de!=""){
					$query .=	"AND so.depe_id = '".$de."' ";
				}
				if($asunto!=""){
					$query .=	"AND so.id_asunto = '".$asunto."' ";
				}
			}else if($opcion=="2"){
				$query .=		"AND LOWER(so.ofic_id) = '".strtolower($codigo)."' ";
			}
			$query .=		"UNION ".
							"SELECT ".
								"so.ofic_id AS id, ".
								"so.fecha_oficio ".
							"FROM ".
								"sai_oficio so, ".
								"sai_oficio_cc soc ".
								(($destinatario!="")?", (".
															"SELECT DISTINCT(sop.ofic_id) AS ofic_id FROM sai_oficio_para sop WHERE sop.cedula = '".$destinatario."' ".
															"UNION ".
															"SELECT DISTINCT(soc.ofic_id) AS ofic_id FROM sai_oficio_cc soc WHERE soc.cedula = '".$destinatario."' ".
														") AS sod ":"").
							"WHERE ";
			if(	(substr($user_perfil_id,0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) ||
				($user_perfil_id == PERFIL_ASISTENTE_EJECUTIVO) ||
				($user_perfil_id == PERFIL_ASISTENTE_PRESIDENCIA)){
				$query .=		"(soc.cedula = '".$usuario."' OR soc.cedula = '".$cedulaSuperior."') AND ".(($destinatario!="")?" soc.ofic_id = sod.ofic_id AND ":"");
			}else{
				$query .=		"soc.cedula = '".$usuario."' AND ".(($destinatario!="")?" soc.ofic_id = sod.ofic_id AND ":"");
			}
			$query .=			"soc.ofic_id = so.ofic_id ";
			if($opcion=="1"){				
				if($fechaInicio!="" && $fechaFin!=""){
					$query .=	"AND so.fecha_oficio BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') ";
				}
				if($estado!=""){
					$query .=	"AND so.esta_id = ".$estado." ";
				}
				if($de!=""){
					$query .=	"AND so.depe_id = '".$de."' ";
				}
				if($asunto!=""){
					$query .=	"AND so.id_asunto = '".$asunto."' ";
				}
			}else if($opcion=="2"){
				$query .=		"AND LOWER(so.ofic_id) = '".strtolower($codigo)."' ";
			}
			$query .=		") AS s ".
						"ORDER BY s.fecha_oficio DESC ".
						"LIMIT ".$tamanoPagina." OFFSET ".$desplazamiento;
			$resultado = pg_exec($conexion, $query);
			while($row=pg_fetch_array($resultado)){
				$idComunicaciones .= "'".$row["id"]."',";
			}
			$idComunicaciones = substr($idComunicaciones, 0, -1);
		}else if($bandeja==BANDEJA_ENVIADOS){
			$query = 	"SELECT ".
							"COUNT(DISTINCT(so.ofic_id)) ".
						"FROM ".
							"sai_oficio so ";
			if($opcion=="1"){				
				$query .=	(($destinatario!="")?", (".
														"SELECT DISTINCT(sop.ofic_id) AS ofic_id FROM sai_oficio_para sop WHERE sop.cedula = '".$destinatario."' ".
														"UNION ".
														"SELECT DISTINCT(soc.ofic_id) AS ofic_id FROM sai_oficio_cc soc WHERE soc.cedula = '".$destinatario."' ".
													") AS sod ":"").
								"WHERE ".
								(($destinatario!="")?" so.ofic_id = sod.ofic_id AND ":"");
				if($fechaInicio!="" && $fechaFin!=""){
					$query .=	"so.fecha_oficio BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ";
				}
				if($estado!=""){
					$query .=	"so.esta_id = ".$estado." AND ";
				}
				if($de!=""){
					$query .=	"so.depe_id = '".$de."' ";
				}else{										
					$query .=	"so.depe_id IN ".$dependencias." ";
				}
				if($asunto!=""){
					$query .=	"AND so.id_asunto = '".$asunto."' ";
				}
			}else if($opcion=="2"){
				$query .= 	"WHERE ".
								"LOWER(so.ofic_id) = '".strtolower($codigo)."' AND ".
								"so.depe_id IN ".$dependencias." ";
			}
			$resultadoContador = pg_exec($conexion, $query);
			$row = pg_fetch_array($resultadoContador, 0);
			$contador = $row[0];
			
			$query = 	"SELECT ".
							"DISTINCT(so.ofic_id) AS id, ".
							"so.fecha_oficio ".
						"FROM ".
							"sai_oficio so ";
			if($opcion=="1"){				
				$query .=	(($destinatario!="")?", (".
														"SELECT DISTINCT(sop.ofic_id) AS ofic_id FROM sai_oficio_para sop WHERE sop.cedula = '".$destinatario."' ".
														"UNION ".
														"SELECT DISTINCT(soc.ofic_id) AS ofic_id FROM sai_oficio_cc soc WHERE soc.cedula = '".$destinatario."' ".
													") AS sod ":"").
								"WHERE ".
								(($destinatario!="")?" so.ofic_id = sod.ofic_id AND ":"");
				if($fechaInicio!="" && $fechaFin!=""){
					$query .=	"so.fecha_oficio BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ";
				}
				if($estado!=""){
					$query .=	"so.esta_id = ".$estado." AND ";
				}
				if($de!=""){
					$query .=	"so.depe_id = '".$de."' ";
				}else{										
					$query .=	"so.depe_id IN ".$dependencias." ";
				}
				if($asunto!=""){
					$query .=	"AND so.id_asunto = '".$asunto."' ";
				}
			}else if($opcion=="2"){
				$query .=	"WHERE ".
								"LOWER(so.ofic_id) = '".strtolower($codigo)."' AND ".
								"so.depe_id IN ".$dependencias." ";
			}
			$query .=	"ORDER BY so.fecha_oficio DESC, so.ofic_id DESC ".
						"LIMIT ".$tamanoPagina." OFFSET ".$desplazamiento;
			$resultado = pg_exec($conexion, $query);
			while($row=pg_fetch_array($resultado)){
				$idComunicaciones .= "'".$row["id"]."',";
			}
			$idComunicaciones = substr($idComunicaciones, 0, -1);
		}
		$totalPaginas = ($contador%$tamanoPagina == 0)?$contador/$tamanoPagina:intval($contador/$tamanoPagina)+1;
		
		$query = 	"SELECT ".
						"so.ofic_id AS id, ".
						"to_char(so.fecha_oficio,'DD/MM/YYYY') AS fecha_cadena, ".
						"sd.depe_nombre AS de, ".
						"sma.nombre AS asunto, ".
						"sem.empl_cedula AS cedula, ".
						"sem.empl_nombres || ' ' || sem.empl_apellidos AS personal, ".
						"sde.depe_nombre AS personal_dependencia, ".
						"se.esta_nombre AS estado, ".
						"sem.depe_cosige, ".
						"sem.carg_fundacion, ".
						"so.usua_login ".
					"FROM ".
						"sai_oficio so, ".
						"sai_dependenci sd, ".
						"sai_memorando_asunto sma,".
						"sai_oficio_para sop, ".
						"sai_empleado sem, ".
						"sai_dependenci sde, ".
						"sai_estado se ".
					"WHERE ".
						"so.ofic_id IN (".$idComunicaciones.") AND ".
						"so.depe_id = sd.depe_id AND ".
						"so.id_asunto = sma.id AND ".
						"so.ofic_id = sop.ofic_id AND ".
						"sop.cedula = sem.empl_cedula AND ".
						"sem.depe_cosige = sde.depe_id AND ".
						"so.esta_id = se.esta_id ".
					"ORDER BY so.fecha_oficio DESC, so.ofic_id DESC, sop.cedula";
	}
	if($idComunicaciones!=""){
		$resultado=pg_query($conexion,$query);
		$numeroFilas = pg_numrows($resultado);
	}else{
		$numeroFilas = 0;
	}
?>
	<table width="100%" border="0" align="center">
		<tr>
			<td height="27" class="normal peq_verde_bold">
				<div align="center">
					Resultados de la b&uacute;squeda
				</div>
			</td>
		</tr>
	</table>
	<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" cellspacing="0">
		<tr class="td_gray normalNegroNegrita">
			<td width="8%" align="center">C&oacute;digo</td>
			<td width="8%" align="center">Bandeja</td>
			<td width="8%" align="center">Fecha</td>
			<td width="8%" align="center">Estado</td>
			<td width="13%" align="center">Remitente</td>
			<td width="27%" align="center">Para</td>
			<td width="20%" align="center">Asunto</td>
			<td width="8%" align="center"></td>
		</tr>
		<?
		$temporalPresidente = array();
		$temporalDirectorEjecutivo = array();
		$temporalGerenteDirector = array();
		$temporalJefe = array();
		$temporalCoordinador = array();
		$personales = array();
		if($numeroFilas>0){
			$comunicacionAnterior = "";
			$asuntoAnterior = "";
			$usuaLoginAnterior = "";
			while($row=pg_fetch_array($resultado)){
				if($comunicacionAnterior!=$row['id']){
					if($comunicacionAnterior!=""){
						/*if(estanTodos($cedulasPresidentes, $temporalPresidente)==true){
							echo "<li>Presidencia</li>";
						}else{*/
							$i=0;
							while($i<sizeof($temporalPresidente)){
								echo "<li>".$temporalPresidente[$i][1]."</li>";	
								$i++;
							}
						/*}*/							
						/*if(estanTodos($cedulasDirectorEjecutivo, $temporalDirectorEjecutivo)==true){
							echo "<li>Direcci&oacute;n Ejecutiva</li>";
						}else{*/
							$i=0;
							while($i<sizeof($temporalDirectorEjecutivo)){
								echo "<li>".$temporalDirectorEjecutivo[$i][1]."</li>";	
								$i++;
							}
						/*}*/
						if(estanTodos($cedulasGerenteDirector, $temporalGerenteDirector)==true){
							echo "<li>Gerentes y Directores</li>";
						}else{
							$i=0;
							while($i<sizeof($temporalGerenteDirector)){
								echo "<li>".$temporalGerenteDirector[$i][1]."</li>";	
								$i++;
							}
						}
						if(estanTodos($cedulasJefe, $temporalJefe)==true){
							echo "<li>Jefes de Unidad</li>";
						}else{
							$i=0;
							while($i<sizeof($temporalJefe)){
								echo "<li>".$temporalJefe[$i][1]."</li>";	
								$i++;
							}
						}
						if(estanTodos($cedulasCoordinador, $temporalCoordinador)==true){
							echo "<li>Coordinadores</li>";
						}else{
							$i=0;
							while($i<sizeof($temporalCoordinador)){
								echo "<li>".$temporalCoordinador[$i][1]."</li>";	
								$i++;
							}
						}
						$i=0;
						while($i<sizeof($personales)){
							echo "<li>".$personales[$i][1]."</li>";	
							$i++;
						}
						$temporalPresidente = array();
						$temporalDirectorEjecutivo = array();
						$temporalGerenteDirector = array();
						$temporalJefe = array();
						$temporalCoordinador = array();
						$personales = array();
						?>
								</ul>
							</td>
							<td align="center" class="resultadoConDivision" valign="top"><?= $asuntoAnterior;?></td>
							<td align="center" class="resultadoConDivision" valign="top">
								<span class="link">
									<a href="javascript:verDetalle('<?= trim($comunicacionAnterior)?>');">Ver Detalle</a>
								</span>
								<?php
								if($bandeja==BANDEJA_ENVIADOS && $usuaLoginAnterior==$usuario){
								?>
								<br/>
								<span class="link">
									<a href="javascript:modificar('<?= trim($comunicacionAnterior)?>');">Modificar</a>
								</span>
								<?php
								}
								?>
							</td>
						</tr>
						<?php
					}
					$comunicacionAnterior=$row['id'];
					$asuntoAnterior=$row['asunto'];
					$usuaLoginAnterior=$row['usua_login'];
			?>
			<tr class="resultados">
				<td height="28" align="center" class="resultadoConDivision" valign="top">
					<span class="link">
						<a href="javascript:verDetalle('<?= trim($row['id'])?>');"><?= $row['id']?></a>
					</span>
				</td>
				<td align="center" class="resultadoConDivision" valign="top"><?= (($bandeja==BANDEJA_RECIBIDOS)?"Recibido":"Enviado")?></td>
				<td align="center" class="resultadoConDivision" valign="top"><?= $row['fecha_cadena'];?></td>
				<td align="center" class="resultadoConDivision" valign="top"><?= $row['estado'];?></td>
				<td align="center" class="resultadoConDivision" valign="top"><?= $row['de'];?></td>
				<td style="border-top: #005E20 solid 1px;" valign="top">
					<ul>
				<?php
				}
				if(($row['carg_fundacion'].$row['depe_cosige'])==PERFIL_PRESIDENTE){
					$temporalPresidente[sizeof($temporalPresidente)]=array();
					$temporalPresidente[sizeof($temporalPresidente)-1][0]=$row['cedula'];
					$temporalPresidente[sizeof($temporalPresidente)-1][1]=$row['personal']." (".$row["personal_dependencia"].")";
				}else if(($row['carg_fundacion'].$row['depe_cosige'])==PERFIL_DIRECTOR_EJECUTIVO){
					$temporalDirectorEjecutivo[sizeof($temporalDirectorEjecutivo)]=array();
					$temporalDirectorEjecutivo[sizeof($temporalDirectorEjecutivo)-1][0]=$row['cedula'];
					$temporalDirectorEjecutivo[sizeof($temporalDirectorEjecutivo)-1][1]=$row['personal']." (".$row["personal_dependencia"].")";
				}else if(($row['carg_fundacion']."000")==PERFIL_DIRECTOR || ($row['carg_fundacion']."000")==PERFIL_GERENTE){
					$temporalGerenteDirector[sizeof($temporalGerenteDirector)]=array();
					$temporalGerenteDirector[sizeof($temporalGerenteDirector)-1][0]=$row['cedula'];
					$temporalGerenteDirector[sizeof($temporalGerenteDirector)-1][1]=$row['personal']." (".$row["personal_dependencia"].")";
				}else if(($row['carg_fundacion']."000")==PERFIL_JEFE){
					$temporalJefe[sizeof($temporalJefe)]=array();
					$temporalJefe[sizeof($temporalJefe)-1][0]=$row['cedula'];
					$temporalJefe[sizeof($temporalJefe)-1][1]=$row['personal']." (".$row["personal_dependencia"].")";
				}else if(($row['carg_fundacion']."000")==PERFIL_COORDINADOR){
					$temporalCoordinador[sizeof($temporalCoordinador)]=array();
					$temporalCoordinador[sizeof($temporalCoordinador)-1][0]=$row['cedula'];
					$temporalCoordinador[sizeof($temporalCoordinador)-1][1]=$row['personal']." (".$row["personal_dependencia"].")";
				}else{
					$personales[sizeof($personales)]=array();
					$personales[sizeof($personales)-1][0]=$row['cedula'];
					$personales[sizeof($personales)-1][1]=$row['personal']." (".$row["personal_dependencia"].")";
				}
			}
			/*if(estanTodos($cedulasPresidentes, $temporalPresidente)==true){
				echo "<li>Presidencia</li>";
			}else{*/
				$i=0;
				while($i<sizeof($temporalPresidente)){
					echo "<li>".$temporalPresidente[$i][1]."</li>";	
					$i++;
				}
			/*}*/							
			/*if(estanTodos($cedulasDirectorEjecutivo, $temporalDirectorEjecutivo)==true){
				echo "<li>Direcci&oacute;n Ejecutiva</li>";
			}else{*/
				$i=0;
				while($i<sizeof($temporalDirectorEjecutivo)){
					echo "<li>".$temporalDirectorEjecutivo[$i][1]."</li>";	
					$i++;
				}
			/*}*/
			if(estanTodos($cedulasGerenteDirector, $temporalGerenteDirector)==true){
				echo "<li>Gerentes y Directores</li>";
			}else{
				$i=0;
				while($i<sizeof($temporalGerenteDirector)){
					echo "<li>".$temporalGerenteDirector[$i][1]."</li>";	
					$i++;
				}
			}
			if(estanTodos($cedulasJefe, $temporalJefe)==true){
				echo "<li>Jefes de Unidad</li>";
			}else{
				$i=0;
				while($i<sizeof($temporalJefe)){
					echo "<li>".$temporalJefe[$i][1]."</li>";	
					$i++;
				}
			}
			if(estanTodos($cedulasCoordinador, $temporalCoordinador)==true){
				echo "<li>Coordinadores</li>";
			}else{
				$i=0;
				while($i<sizeof($temporalCoordinador)){
					echo "<li>".$temporalCoordinador[$i][1]."</li>";	
					$i++;
				}
			}
			$i=0;
			while($i<sizeof($personales)){
				echo "<li>".$personales[$i][1]."</li>";	
				$i++;
			}
			$temporalPresidente = array();
			$temporalDirectorEjecutivo = array();
			$temporalGerenteDirector = array();
			$temporalJefe = array();
			$temporalCoordinador = array();
			$personales = array();
			?>
					</ul>
				</td>
				<td align="center" class="resultadoConDivision" valign="top"><?= $asuntoAnterior;?></td>
				<td align="center" class="resultadoConDivision" valign="top">
					<span class="link">
						<a href="javascript:verDetalle('<?= trim($comunicacionAnterior)?>');">Ver Detalle</a>
					</span>
					<?php
					if($bandeja==BANDEJA_ENVIADOS && $usuaLoginAnterior==$usuario){					
					?>
					<br/>
					<span class="link">
						<a href="javascript:modificar('<?= trim($comunicacionAnterior)?>');">Modificar</a>
					</span>
					<?php
					}
					?>
				</td>
			</tr>
			<?php
			echo "<tr class='td_gray'><td colspan='8' align='center'>";
			$ventanaActual = ($pagina%$tamanoVentana==0)?$pagina/$tamanoVentana:intval($pagina/$tamanoVentana)+1;
			$i = (($ventanaActual-1)*$tamanoVentana)+1;
			while($i<=$ventanaActual*$tamanoVentana && $i<=$totalPaginas) {
				if($i==(($ventanaActual-1)*$tamanoVentana)+1 && $i!=1){
					echo "<a onclick='buscar(".($i-1).");' style='cursor: pointer;text-decoration: underline;'>&lt;</a> ";
				}
				if($i==$pagina){
					echo $i." ";
				}else{
					echo "<a onclick='buscar(".$i.");' style='cursor: pointer;text-decoration: underline;'>".$i."</a> ";
				}
				if($i==$ventanaActual*$tamanoVentana && $i<$totalPaginas){
					echo "<a onclick='buscar(".($i+1).");' style='cursor: pointer;text-decoration: underline;'>&gt;</a> ";
				}
				$i++;   	
			}
			echo "</td></tr>\n";			
		}else{
			echo "<tr><td align='center' valign='middle' height='50' colspan='8'>No se encontr&oacute; ".(($tipo=="memo")?"memorandos":"oficios")."</td></tr>";
		}
	?>
	</table>
<?php
}
?>
</body>
</html>
<?php pg_close($conexion); ?>