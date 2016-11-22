<?php
require_once(dirname(__FILE__) . '/../../init.php');
require_once (SAFI_LIB_PATH . '/general.php');
require_once(SAFI_MODELO_PATH. '/firma.php');
require_once("../../includes/conexion.php");
require_once("../../includes/constantes.php");
require_once("../../includes/perfiles/constantesPerfiles.php");
if	( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush();
	exit;
}

require_once("../../includes/reporteBasePdf.php");
require_once("../../includes/html2ps/config.inc.php");
require_once(HTML2PS_DIR.'pipeline.factory.class.php');
require_once("../../includes/html2ps/funciones.php");

$tipo=$_REQUEST['tipo'];
$codigo=$_REQUEST['codigo'];

if($tipo && $tipo!="" && $codigo && $codigo!=""){
	if($tipo=="memo"){
		$query = 	"SELECT ".
						"to_char(sm.fecha,'DD/MM/YYYY') AS fecha_cadena, ".
						"sd.depe_id AS depe_id, ".
						"sd.depe_nombre AS dependencia, ".
						"sd.depe_nivel AS nivel_dependencia, ".
						"sd.depe_id_sup AS depe_id_sup, ".
						"sma.nombre AS asunto, ".
						"sm.descripcion, ".
						"sm.anexos, ".
						"sm.despedida,  ".
						"sm.alineacion_despedida, ".
						"sm.coletilla, ".
						"sm.id_asunto, ".
						"sm.usua_login, ".
						"LOWER(SUBSTRING(se.empl_nombres FROM 1 FOR 1)||SUBSTRING(se.empl_apellidos FROM 1 FOR 1)) AS firma_de, ".
						"INITCAP(se.empl_nombres || ' ' || se.empl_apellidos) AS creado_por_nombre, " .
						"sc.carg_nombre AS creado_por_cargo, ".
						"sm.firma_presidencia, ".
						"sm.firma_administracion ".
					"FROM ".
						"sai_memorando sm, ".
						"sai_dependenci sd, ".
						"sai_memorando_asunto sma, ".
						"sai_empleado se, ".
						"sai_usuario su, ".
						"sai_cargo sc ".
					"WHERE ".
						"sm.memo_id = '".$codigo."' AND ".
						"sm.usua_login = su.usua_login AND ".
						"su.empl_cedula = se.empl_cedula AND ".
						"sm.depe_id = sd.depe_id AND ".
						"sm.id_asunto = sma.id AND " .
						"se.carg_fundacion = sc.carg_fundacion";
	}else if($tipo=="ofic"){
		$query = 	"SELECT ".
						"to_char(so.fecha,'DD/MM/YYYY') AS fecha_cadena, ".
						"sd.depe_id AS depe_id, ".
						"sd.depe_nombre AS dependencia, ".
						"sma.nombre AS asunto, ".
						"so.descripcion, ".
						"so.anexos, ".
						"so.despedida, ".
						"so.alineacion_despedida, ".
						"so.coletilla, ".
						"so.id_asunto, ".	
						"so.usua_login, ".
						"LOWER(SUBSTRING(se.empl_nombres FROM 1 FOR 1)||SUBSTRING(se.empl_apellidos FROM 1 FOR 1)) AS firma_de, ".
						"INITCAP(se.empl_nombres || ' ' || se.empl_apellidos) AS creado_por_nombre, " .
						"sc.carg_nombre AS creado_por_cargo ".
					"FROM ".
						"sai_oficio so, ".
						"sai_dependenci sd, ".
						"sai_memorando_asunto sma, ".
						"sai_empleado se, ".
						"sai_usuario su, ".
						"sai_cargo sc ".
					"WHERE ".
						"so.ofic_id = '".$codigo."' AND ".
						"so.usua_login = su.usua_login AND ".
						"su.empl_cedula = se.empl_cedula AND ".
						"so.depe_id = sd.depe_id AND ".
						"so.id_asunto = sma.id AND " .
						"se.carg_fundacion = sc.carg_fundacion";
	}
	
	$resultado=pg_query($conexion,$query);
	$row = pg_fetch_array($resultado, 0);
	$fecha = $row["fecha_cadena"];
	
	$depe_id = $row["depe_id"];
	$dependencia = $row["dependencia"];
	$nivelDependencia = $row["nivel_dependencia"];
	$depe_id_sup = $row["depe_id_sup"];
	$asunto = $row["asunto"];
	$id_asunto = $row["id_asunto"];
	$usua_login = $row["usua_login"];
	
	$descripcion = $row["descripcion"];
	$anexos = str_replace("\n","<br/>",$row["anexos"]);
	$firmaDe = trim($row["firma_de"]);
	$despedida = $row["despedida"];
	$alineacionDespedida = $row["alineacion_despedida"];
	$coletilla = str_replace("\n","<br/>",$row["coletilla"]);
	$creadoPorNombre = $row['creado_por_nombre'];
	$creadoPorCargo = $row['creado_por_cargo'];
	$firmaPresidencia = $row["firma_presidencia"];
	$firmaAdministracion = $row["firma_administracion"];
	
	$estadoActivo = "1";
	$query = 	"SELECT ".
					"INITCAP(se.empl_nombres || ' ' || se.empl_apellidos) AS de, ".
					"UPPER(SUBSTRING(se.empl_nombres FROM 1 FOR 1)||SUBSTRING(se.empl_apellidos FROM 1 FOR 1)) AS firma_de, ".
					"se.carg_fundacion||se.depe_cosige AS perfil ".
				"FROM ".
					"sai_empleado se ".
				"WHERE ".
					"se.depe_cosige = '".(($nivelDependencia > 4)?$depe_id_sup:$depe_id)."' AND ".
					"se.esta_id = ".$estadoActivo." AND ".
					"se.carg_fundacion IN (".
											"'".substr(PERFIL_PRESIDENTE, 0, 2)."',".
											"'".substr(PERFIL_DIRECTOR_EJECUTIVO, 0, 2)."',".
											"'".substr(PERFIL_CONSULTOR_JURIDICO, 0, 2)."',".
											"'".substr(PERFIL_DIRECTOR, 0, 2)."',".
											"'".substr(PERFIL_GERENTE, 0, 2)."'".
											")
				LIMIT 1
	";
	$resultado=pg_query($conexion,$query);
	if($row = pg_fetch_array($resultado))
	{
		//$de = $row["de"];
		$firmaDe = trim($row["firma_de"])."/".$firmaDe;
		$perfil = $row["perfil"];
	} else {
		if($nivelDependencia > 4 ? ($depe_id_sup == "400") : ($depe_id == "400")){
			$query = "
				SELECT ".
					"INITCAP(se.empl_nombres || ' ' || se.empl_apellidos) AS de, ".
					"UPPER(SUBSTRING(se.empl_nombres FROM 1 FOR 1)||SUBSTRING(se.empl_apellidos FROM 1 FOR 1)) AS firma_de, ".
					"se.carg_fundacion||se.depe_cosige AS perfil ".
				"FROM ".
					"sai_empleado se ".
				"WHERE ".
					"se.depe_cosige = '400' AND ".
					"se.esta_id = ".$estadoActivo." AND ".
					"se.carg_fundacion IN ('".substr(PERFIL_JEFE_PRESUPUESTO, 0, 2)."')
				LIMIT 1
			";
			
			$resultado=pg_query($conexion,$query);
			if($row = pg_fetch_array($resultado))
			{
				$firmaDe = trim($row["firma_de"])."/".$firmaDe;
				$perfil = $row["perfil"];
			}
			
		}
		else if ($nivelDependencia > 4 ? ($depe_id_sup == "500") : ($depe_id == "500"))
		{
			$firmaDe = "/".$firmaDe;
			// $perfil = PERFIL_DIRECTOR_TALENTO_HUMANO;
			
		}
		else if ($nivelDependencia > 4 ? ($depe_id_sup == "450") : ($depe_id == "450"))
		{
			$firmaDe = "/".$firmaDe;
			$perfil = PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS;
				
		}
		else {
			echo "
				Error en la consulta del empleado que envia el memo (El apartado \"De:\" en el memo).
				Comun&iacute;quese con la jefatura de sistemas.
			";
			exit;
		}
	}
	
	
	$banderaPara = false;
	$banderaCc = false;
	
	$firmas = array();
	$firmas[] = $perfil;
	
	if ( $firmaPresidencia == "t" ) {
		$incluirFirmaPresidencia = true;
		$firmas[] = PERFIL_PRESIDENTE;
	} else {
		$incluirFirmaPresidencia = false;
	}
	
	
	
	$firmasSeleccionadas = SafiModeloFirma::GetFirmaByPerfiles($firmas);
	
//cable

/*	
if($firmasSeleccionadas[46500]){
	
	$firmasSeleccionadas[46500]['nombre_empleado'] = '';
	
}
*/
	$contenido = "<style type='text/css'>
						.titulo{
							text-align:center;
							font-size: 24pt;
							font-weight:bold;							
						}
						.espaciado{
							height: 8px;							
						}
						.nombreCampo{
							font-family: arial;
							font-size: 22pt;
							font-style:italic;
							text-decoration: underline;
							width: 35%;
							vertical-align: middle;
						}
						.valorCampo{
							margin-top: 5px;
							margin-bottom: 5px;
						}
						.bordeTabla{
							border: solid 1px #000000;
						}
						.alineadoArriba{
							vertical-align: top;
						}
						.alineadoAbajo{
							vertical-align: bottom;
						}
						.textoComunicacion{
							FONT-WEIGHT: bold; FONT-SIZE: 22px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: underline
						}
						.textoTabla{
							FONT-WEIGHT: normal; FONT-SIZE: 22px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none
						}
						.textoAnexos{
							FONT-SIZE: 16px;
						}
					</style>";
	
	$contenido .="<table width='1000px' class='textoTabla' cellspacing='0' cellpadding='0'>";
	
	if($tipo=="memo"){
		$nombreComunicacion = "Memorando";
	}else if($tipo=="ofic"){
		$nombreComunicacion = "Oficio";
	}
	
	$cedulasGerenteDirector = "";
	$cargoGerente = substr(PERFIL_GERENTE,0,2);
	$cargoDirector = substr(PERFIL_DIRECTOR,0,2);
	$query = 	"SELECT ".
					"se.empl_cedula AS cedula, ".
					"UPPER(se.empl_nombres || ' ' || se.empl_apellidos) AS nombre, ".
					"UPPER(sc.carg_nombre) AS cargo, ".
					"UPPER(sd.depe_nombre) AS dependencia ".
				"FROM sai_empleado se, sai_cargo sc, sai_dependenci sd ".
				"WHERE ".
					"se.esta_id = ".$estadoActivo." AND ".
					"se.carg_fundacion = sc.carg_fundacion AND ".
					"se.depe_cosige = sd.depe_id AND ".
					"(se.carg_fundacion = '".$cargoGerente."' OR se.carg_fundacion = '".$cargoDirector."') ".
				"ORDER BY se.empl_nombres, se.empl_apellidos ";
	$resultado = pg_exec($conexion, $query);
	$tamanoGerentesDirectores = pg_num_rows($resultado);
	while($row=pg_fetch_array($resultado)){
		$cedulasGerenteDirector .= "'".$row["cedula"]."',";
	}
	$cedulasGerenteDirector = substr($cedulasGerenteDirector, 0, -1);	
	if($tipo=="memo"){
		$query = 	"SELECT ".
						"COUNT(smp.cedula) ".
					"FROM ".
						"sai_memorando_para smp ".
					"WHERE ".
						"smp.memo_id = '".$codigo."' AND ".
						"smp.cedula IN (".$cedulasGerenteDirector.") ";
	}else if($tipo=="ofic"){
		$query = 	"SELECT ".
						"COUNT(sop.cedula) ".
					"FROM ".
						"sai_oficio_para sop ".
					"WHERE ".
						"sop.ofic_id = '".$codigo."' AND ".
						"sop.cedula IN (".$cedulasGerenteDirector.") ";
	}
	$resultado=pg_query($conexion,$query);
	$row=pg_fetch_array($resultado);
	if($tamanoGerentesDirectores==$row[0]){
		$todosGerentesDirectores = true;
	}else{
		$todosGerentesDirectores = false;
	}
	if($todosGerentesDirectores==true){
		$banderaPara = true;
		$contenido .=	"<tr>".
							"<td width='15%' valign='top'>Para:</td>".
							"<td width='85%'>".
								"Gerentes y Directores".
							"</td>".
						"</tr>";
	}
	
	$cedulasJefe = "";
	$cargo = substr(PERFIL_JEFE,0,2);
	$query = 	"SELECT ".
					"se.empl_cedula AS cedula, ".
					"UPPER(se.empl_nombres || ' ' || se.empl_apellidos) AS nombre, ".
					"UPPER(sc.carg_nombre) AS cargo, ".
					"UPPER(sd.depe_nombre) AS dependencia ".
				"FROM sai_empleado se, sai_cargo sc, sai_dependenci sd ".
				"WHERE ".
					"se.esta_id = ".$estadoActivo." AND ".
					"se.carg_fundacion = sc.carg_fundacion AND ".
					"se.depe_cosige = sd.depe_id AND ".
					"se.carg_fundacion = '".$cargo."' ".
				"ORDER BY se.empl_nombres, se.empl_apellidos ";
	$resultado = pg_exec($conexion, $query);
	$tamanoJefes = pg_num_rows($resultado);
	while($row=pg_fetch_array($resultado)){
		$cedulasJefe .= "'".$row["cedula"]."',";
	}
	$cedulasJefe = substr($cedulasJefe, 0, -1);
	if($tipo=="memo"){
		$query = 	"SELECT ".
						"COUNT(smp.cedula) ".
					"FROM ".
						"sai_memorando_para smp ".
					"WHERE ".
						"smp.memo_id = '".$codigo."' AND ".
						"smp.cedula IN (".$cedulasJefe.") ";
	}else if($tipo=="ofic"){
		$query = 	"SELECT ".
						"COUNT(sop.cedula) ".
					"FROM ".
						"sai_oficio_para sop ".
					"WHERE ".
						"sop.ofic_id = '".$codigo."' AND ".
						"sop.cedula IN (".$cedulasJefe.") ";
	}
	$resultado=pg_query($conexion,$query);
	$row=pg_fetch_array($resultado);
	if($tamanoJefes==$row[0]){
		$todosJefes = true;
	}else{
		$todosJefes = false;
	}
	if($todosJefes==true){
		if($banderaPara==false){
			$banderaPara = true;
			$contenido .=	"<tr>".
								"<td width='15%' valign='top'>Para:</td>".
								"<td width='85%'>".
									"Jefes de Unidad".
								"</td>".
							"</tr>";			
		}else{
			$contenido .=	"<tr>".
								"<td width='15%' valign='top'></td>".
								"<td width='85%'>".
									"Jefes de Unidad".
								"</td>".
							"</tr>";
		}
	}
	
	$cedulasCoordinador = "";
	$cargo = substr(PERFIL_COORDINADOR,0,2);
	$query = 	"SELECT ".
					"se.empl_cedula AS cedula, ".
					"UPPER(se.empl_nombres || ' ' || se.empl_apellidos) AS nombre, ".
					"UPPER(sc.carg_nombre) AS cargo, ".
					"UPPER(sd.depe_nombre) AS dependencia ".
				"FROM sai_empleado se, sai_cargo sc, sai_dependenci sd ".
				"WHERE ".
					"se.esta_id = ".$estadoActivo." AND ".
					"se.carg_fundacion = sc.carg_fundacion AND ".
					"se.depe_cosige = sd.depe_id AND ".
					"se.carg_fundacion = '".$cargo."' ".
				"ORDER BY se.empl_nombres, se.empl_apellidos ";
	$resultado = pg_exec($conexion, $query);
	$tamanoCoordinadores = pg_num_rows($resultado);
	while($row=pg_fetch_array($resultado)){
		$cedulasCoordinador .= "'".$row["cedula"]."',";
	}
	$cedulasCoordinador = substr($cedulasCoordinador, 0, -1);
	if($tipo=="memo"){
		$query = 	"SELECT ".
						"COUNT(smp.cedula) ".
					"FROM ".
						"sai_memorando_para smp ".
					"WHERE ".
						"smp.memo_id = '".$codigo."' AND ".
						"smp.cedula IN (".$cedulasCoordinador.") ";
	}else if($tipo=="ofic"){
		$query = 	"SELECT ".
						"COUNT(sop.cedula) ".
					"FROM ".
						"sai_oficio_para sop ".
					"WHERE ".
						"sop.ofic_id = '".$codigo."' AND ".
						"sop.cedula IN (".$cedulasCoordinador.") ";
	}
	$resultado=pg_query($conexion,$query);
	$row=pg_fetch_array($resultado);
	if($tamanoCoordinadores==$row[0]){
		$todosCoordinadores = true;
	}else{
		$todosCoordinadores = false;
	}
	if($todosCoordinadores==true){
		if($banderaPara==false){
			$banderaPara = true;
			$contenido .=	"<tr>".
								"<td width='15%' valign='top'>Para:</td>".
								"<td width='85%'>".
									"Coordinadores".
								"</td>".
							"</tr>";	
		}else{
			$contenido .=	"<tr>".
								"<td width='15%' valign='top'></td>".
								"<td width='85%'>".
									"Coordinadores".
								"</td>".
							"</tr>";
		}
	}
	
	if($tipo=="memo"){
		$query = 	"SELECT ".
						"smp.cedula, ".
						"se.empl_nombres || ' ' || se.empl_apellidos AS nombre, ".
						"sc.carg_nombre AS cargo, ".
						"sd.depe_nombre AS dependencia, ".
						"se.carg_fundacion||se.depe_cosige AS perfil ".		
					"FROM ".
						"sai_memorando_para smp, ".
						"sai_empleado se, ".
						"sai_dependenci sd, ".
						"sai_cargo sc ".
					"WHERE ".
						"smp.memo_id = '".$codigo."' AND ".
						(($todosGerentesDirectores==true)?"smp.cedula NOT IN (".$cedulasGerenteDirector.") AND ":"").
						(($todosJefes==true)?"smp.cedula NOT IN (".$cedulasJefe.") AND ":"").
						(($todosCoordinadores==true)?"smp.cedula NOT IN (".$cedulasCoordinador.") AND ":"").
						"smp.cedula = se.empl_cedula AND ".
						"se.carg_fundacion = sc.carg_fundacion AND ".
						"se.depe_cosige = sd.depe_id ".
					"ORDER BY sd.depe_nombre, smp.cedula";
	}else if($tipo=="ofic"){
		$query = 	"SELECT ".
						"sop.cedula, ".
						"se.empl_nombres || ' ' || se.empl_apellidos AS nombre, ".
						"sc.carg_nombre AS cargo, ".
						"sd.depe_nombre AS dependencia, ".
						"se.carg_fundacion||se.depe_cosige AS perfil ".
					"FROM ".
						"sai_oficio_para sop, ".
						"sai_empleado se, ".
						"sai_dependenci sd, ".
						"sai_cargo sc ".
					"WHERE ".
						"sop.ofic_id = '".$codigo."' AND ".
						(($todosGerentesDirectores==true)?"sop.cedula NOT IN (".$cedulasGerenteDirector.") AND ":"").
						(($todosJefes==true)?"sop.cedula NOT IN (".$cedulasJefe.") AND ":"").
						(($todosCoordinadores==true)?"sop.cedula NOT IN (".$cedulasCoordinador.") AND ":"").
						"sop.cedula = se.empl_cedula AND ".
						"se.carg_fundacion = sc.carg_fundacion AND ".
						"se.depe_cosige = sd.depe_id ".
					"ORDER BY sd.depe_nombre, sop.cedula";
	}
	$resultado=pg_query($conexion,$query);
	
	$firmas = array();
	$arregloPara = array();
	while($row=pg_fetch_array($resultado)){
		$para=array();
		$para["nombre"]=$row["nombre"];
		$para["perfil"]=$row["perfil"];
		$arregloPara[]=$para;
		$firmas[]=$para["perfil"];
	}
	$firmasPara = SafiModeloFirma::GetFirmaByPerfiles($firmas);
	
	$i=0;
	while($i<sizeof($arregloPara)){
		if($banderaPara==false){
			$banderaPara = true;
			$contenido .=	"<tr>".
								"<td width='15%' valign='top'>Para:</td>".
								"<td width='85%'>".
									/*$arregloPara[$i]["nombre"]." - ".$firmasPara[$arregloPara[$i]["perfil"]]['nombre_cargo_dependencia'].*/
									/*$firmasPara[$arregloPara[$i]["perfil"]]['nombre_empleado']." - ".$firmasPara[$arregloPara[$i]["perfil"]]['nombre_cargo_dependencia'].*/
									$arregloPara[$i]["nombre"]." - ".$firmasPara[$arregloPara[$i]["perfil"]]['nombre_cargo_dependencia'].
								"</td>".
							"</tr>";
		}else{
			$contenido .=	"<tr>".
							"<td width='15%' valign='top'></td>".
							"<td width='85%'>".
								/*$arregloPara[$i]["nombre"]." - ".$firmasPara[$arregloPara[$i]["perfil"]]['nombre_cargo_dependencia'].*/
								/*$firmasPara[$arregloPara[$i]["perfil"]]['nombre_empleado']." - ".$firmasPara[$arregloPara[$i]["perfil"]]['nombre_cargo_dependencia'].*/
								$arregloPara[$i]["nombre"]." - ".$firmasPara[$arregloPara[$i]["perfil"]]['nombre_cargo_dependencia'].
							"</td>".
						"</tr>";
		}
		$i++;
	}
	$contenido .=	"<tr><td class='espaciado' colspan='2'></td></tr>";	
	
	if($tipo=="memo"){
		$query = 	"SELECT ".
						"COUNT(smc.cedula) ".
					"FROM ".
						"sai_memorando_cc smc ".
					"WHERE ".
						"smc.memo_id = '".$codigo."' AND ".
						"smc.cedula IN (".$cedulasGerenteDirector.") ";
	}else if($tipo=="ofic"){
		$query = 	"SELECT ".
						"COUNT(soc.cedula) ".
					"FROM ".
						"sai_oficio_cc soc ".
					"WHERE ".
						"soc.ofic_id = '".$codigo."' AND ".
						"soc.cedula IN (".$cedulasGerenteDirector.") ";
	}
	$resultado=pg_query($conexion,$query);
	$row=pg_fetch_array($resultado);
	if($tamanoGerentesDirectores==$row[0]){
		$todosGerentesDirectores = true;
	}else{
		$todosGerentesDirectores = false;
	}
	if($todosGerentesDirectores==true){
		$banderaCc = true;
		$contenido .=	"<tr>".
							"<td width='15%' valign='top'>Cc:</td>".
							"<td width='85%'>".
								"Gerentes y Directores".
							"</td>".
						"</tr>";
	}
	
	if($tipo=="memo"){
		$query = 	"SELECT ".
						"COUNT(smc.cedula) ".
					"FROM ".
						"sai_memorando_cc smc ".
					"WHERE ".
						"smc.memo_id = '".$codigo."' AND ".
						"smc.cedula IN (".$cedulasJefe.") ";
	}else if($tipo=="ofic"){
		$query = 	"SELECT ".
						"COUNT(soc.cedula) ".
					"FROM ".
						"sai_oficio_cc soc ".
					"WHERE ".
						"soc.ofic_id = '".$codigo."' AND ".
						"soc.cedula IN (".$cedulasJefe.") ";
	}
	$resultado=pg_query($conexion,$query);
	$row=pg_fetch_array($resultado);
	if($tamanoJefes==$row[0]){
		$todosJefes = true;
	}else{
		$todosJefes = false;
	}
	if($todosJefes==true){
		if($banderaCc==false){
			$banderaCc = true;
			$contenido .=	"<tr>".
								"<td width='15%' valign='top'>Cc:</td>".
								"<td width='85%'>".
									"Jefes de Unidad".
								"</td>".
							"</tr>";
		}else{
			$contenido .=	"<tr>".
								"<td width='15%' valign='top'></td>".
								"<td width='85%'>".
									"Jefes de Unidad".
								"</td>".
							"</tr>";			
		}
	}
	
	if($tipo=="memo"){
		$query = 	"SELECT ".
						"COUNT(smc.cedula) ".
					"FROM ".
						"sai_memorando_cc smc ".
					"WHERE ".
						"smc.memo_id = '".$codigo."' AND ".
						"smc.cedula IN (".$cedulasCoordinador.") ";
	}else if($tipo=="ofic"){
		$query = 	"SELECT ".
						"COUNT(soc.cedula) ".
					"FROM ".
						"sai_oficio_cc soc ".
					"WHERE ".
						"soc.ofic_id = '".$codigo."' AND ".
						"soc.cedula IN (".$cedulasCoordinador.") ";
	}
	$resultado=pg_query($conexion,$query);
	$row=pg_fetch_array($resultado);
	if($tamanoCoordinadores==$row[0]){
		$todosCoordinadores = true;
	}else{
		$todosCoordinadores = false;
	}
	if($todosCoordinadores==true){
		if($banderaCc==false){
			$banderaCc = true;
			$contenido .=	"<tr>".
								"<td width='15%' valign='top'>Cc:</td>".
								"<td width='85%'>".
									"Coordinadores".
								"</td>".
							"</tr>";
		}else{
			$contenido .=	"<tr>".
								"<td width='15%' valign='top'></td>".
								"<td width='85%'>".
									"Coordinadores".
								"</td>".
							"</tr>";
		}
	}
	if($tipo=="memo"){
		$query = 	"SELECT ".
						"smc.cedula, ".
						"se.empl_nombres || ' ' || se.empl_apellidos AS nombre, ".
						"sc.carg_nombre AS cargo, ".
						"sd.depe_nombre AS dependencia, ".
						"se.carg_fundacion||se.depe_cosige AS perfil ".
					"FROM ".
						"sai_memorando_cc smc, ".
						"sai_empleado se, ".
						"sai_dependenci sd, ".
						"sai_cargo sc ".
					"WHERE ".
						"smc.memo_id = '".$codigo."' AND ".
						(($todosGerentesDirectores==true)?"smc.cedula NOT IN (".$cedulasGerenteDirector.") AND ":"").
						(($todosJefes==true)?"smc.cedula NOT IN (".$cedulasJefe.") AND ":"").
						(($todosCoordinadores==true)?"smc.cedula NOT IN (".$cedulasCoordinador.") AND ":"").
						"smc.cedula = se.empl_cedula AND ".
						"se.carg_fundacion = sc.carg_fundacion AND ".
						"se.depe_cosige = sd.depe_id ".
					"ORDER BY sd.depe_nombre, smc.cedula";
	}else if($tipo=="ofic"){
		$query = 	"SELECT ".
						"soc.cedula, ".
						"se.empl_nombres || ' ' || se.empl_apellidos AS nombre, ".
						"sc.carg_nombre AS cargo, ".
						"sd.depe_nombre AS dependencia, ".
						"se.carg_fundacion||se.depe_cosige AS perfil ".
					"FROM ".
						"sai_oficio_cc soc, ".
						"sai_empleado se, ".
						"sai_dependenci sd, ".
						"sai_cargo sc ".
					"WHERE ".
						"soc.ofic_id = '".$codigo."' AND ".
						(($todosGerentesDirectores==true)?"soc.cedula NOT IN (".$cedulasGerenteDirector.") AND ":"").
						(($todosJefes==true)?"soc.cedula NOT IN (".$cedulasJefe.") AND ":"").
						(($todosCoordinadores==true)?"soc.cedula NOT IN (".$cedulasCoordinador.") AND ":"").
						"soc.cedula = se.empl_cedula AND ".
						"se.carg_fundacion = sc.carg_fundacion AND ".
						"se.depe_cosige = sd.depe_id ".
					"ORDER BY sd.depe_nombre, soc.cedula";												
	}
	$resultado=pg_query($conexion,$query);
	$numeroFilas = pg_num_rows($resultado);
	if($numeroFilas>0){
		$firmas = array();
		$arregloCc = array();
		while($row=pg_fetch_array($resultado)){
			$cc=array();
			$cc["nombre"]=$row["nombre"];
			$cc["perfil"]=$row["perfil"];
			$arregloCc[]=$cc;
			$firmas[]=$cc["perfil"];
		}
		$firmasCc = SafiModeloFirma::GetFirmaByPerfiles($firmas);
		
		$i=0;
		while($i<sizeof($firmasCc)){
			if($banderaCc==false){
				$banderaCc = true;
				$contenido .=	"<tr>".
									"<td valign='top' width='15%'>Cc:</td>".
									"<td width='85%'>".
										/*$arregloCc[$i]["nombre"]." - ".$firmasCc[$arregloCc[$i]["perfil"]]['nombre_cargo_dependencia'].*/
										/*$firmasCc[$arregloCc[$i]["perfil"]]['nombre_empleado']." - ".$firmasCc[$arregloCc[$i]["perfil"]]['nombre_cargo_dependencia'].*/
										$arregloCc[$i]["nombre"]." - ".$firmasCc[$arregloCc[$i]["perfil"]]['nombre_cargo_dependencia'].
									"</td>".
								"</tr>";
			}else{
				$contenido .=	"<tr>".
									"<td valign='top' width='15%'></td>".
									"<td width='85%'>".
										/*$arregloCc[$i]["nombre"]." - ".$firmasCc[$arregloCc[$i]["perfil"]]['nombre_cargo_dependencia'].*/
										/*$firmasCc[$arregloCc[$i]["perfil"]]['nombre_empleado']." - ".$firmasCc[$arregloCc[$i]["perfil"]]['nombre_cargo_dependencia'].*/
										$arregloCc[$i]["nombre"]." - ".$firmasCc[$arregloCc[$i]["perfil"]]['nombre_cargo_dependencia'].
									"</td>".
								"</tr>";
			}
			$i++;
		}
		$contenido .=	"<tr><td class='espaciado' colspan='2'></td></tr>";			
	}
	
	$contenido .=	"<tr>".	
						"<td>De:</td>".
						/*"<td>".(($de!="")?$de." - ":"").$firmasSeleccionadas[$perfil]['nombre_cargo_dependencia']."</td>".*/
						"<td>".$firmasSeleccionadas[$perfil]['nombre_empleado']." - ".$firmasSeleccionadas[$perfil]['nombre_cargo_dependencia']."</td>".
					"</tr>".
					"<tr><td class='espaciado' colspan='2'></td></tr>".
					"<tr>".	
						"<td>Asunto:</td>".
						"<td>".$asunto."</td>".
					"</tr>".
					"<tr><td class='espaciado' colspan='2'></td></tr>".
					"<tr>".
						"<td colspan='2' style='border-top: 1px solid #000000;'>&nbsp;</td>".
					"</tr>".
					"<tr>".
						"<td colspan='2'>".$descripcion."</td>".
					"</tr>".
					"<tr>".
						"<td colspan='2'>&nbsp;</td>".
					"</tr>".
					"<tr>".
						"<td colspan='2' align='".$alineacionDespedida."'>".$despedida."</td>".
					"</tr>".
					"<tr>".
						"<td colspan='2'>&nbsp;</td>".
					"</tr>".
					"<tr>".
						"<td colspan='2'>&nbsp;</td>".
					"</tr>".
					"<tr>".
						"<td colspan='2'>&nbsp;</td>".
					"</tr>";
	
	$contenido .=	"<tr>".
						"<td colspan='2' align='center'>".
							"<table width='100%'>".
								"<tr align='center'>".
									/*"<td align='center'>".$de."</td>".*/
									"<td align='center'>".$firmasSeleccionadas[$perfil]['nombre_empleado']."</td>".
									(($incluirFirmaPresidencia) ? "<td align='center' width='50%'>".$firmasSeleccionadas[PERFIL_PRESIDENTE]['nombre_empleado']."</td>" : "") .
								"</tr>".
								"<tr align='center'>".
									"<td align='center'>".$firmasSeleccionadas[$perfil]['nombre_cargo_dependencia'].(($coletilla!="")?"<br/><span class='textoAnexos'>".$coletilla."</span>":"")."</td>".
									(($incluirFirmaPresidencia) ? "<td>".$firmasSeleccionadas[PERFIL_PRESIDENTE]['nombre_cargo_dependencia']."</td>" : "") .
								"</tr>".
							"</table>".
						"</td>".
					"</tr>";
	
	if($anexos!=""){
		$contenido .=	"<tr>".
							"<td colspan='2'>&nbsp;</td>".
						"</tr>".
						"<tr>".
							"<td colspan='2' class='textoAnexos'>Anexos:</td>".
						"</tr>".
						"<tr>".
							"<td colspan='2' class='textoAnexos'>".$anexos."</td>".
						"</tr>";
	}		
	$contenido .=		"<tr>".
							"<td colspan='2'>&nbsp;</td>".
						"</tr>".
						"<tr>".
							"<td colspan='2' class='textoAnexos'>".$firmaDe."</td>".
						"</tr>".
					"</table>";
	
	$header = 	"<img width='1000px' src='http://safi.infocentro.gob.ve/imagenes/encabezado.jpg'/>".
				"<br/><br/><table width='100%' style='font-size: 17pt;'><tr><td align='right' valign='top' width='66%'><b>".$nombreComunicacion." ".$codigo."</b><p></p></td><td align='right' valign='top'>Fecha: ".$fecha."</td></tr></table>".
				"<style type='text/css'>
					@page {
						margin-top: 35mm;
						@top-right {
							font-size: 17pt;
				 			margin-top: 30mm;
				 			margin-right: 4px;
				    		content: 'Página ' counter(page) ' de ' counter(pages);
				  		}
					}
				</style>";


	$properties = array("headerHtml" => $header);
	
	convert_to_pdf($contenido, $properties);
}
pg_close($conexion);
?>
